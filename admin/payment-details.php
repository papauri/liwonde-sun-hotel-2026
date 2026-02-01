<?php
session_start();

// Check authentication
if (!isset($_SESSION['admin_user'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';
require_once '../includes/modal.php';
require_once '../includes/alert.php';

$user = $_SESSION['admin_user'];
$site_name = getSetting('site_name');
$currency_symbol = getSetting('currency_symbol');

// Get payment ID
$paymentId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$paymentId) {
    $_SESSION['alert'] = ['type' => 'error', 'message' => 'Payment ID is required'];
    header('Location: payments.php');
    exit;
}

// Get payment details
$stmt = $pdo->prepare("
    SELECT 
        p.*,
        CASE 
            WHEN p.booking_type = 'room' THEN CONCAT(b.guest_name, ' (', b.booking_reference, ')')
            WHEN p.booking_type = 'conference' THEN CONCAT(ci.organization_name, ' (', ci.enquiry_reference, ')')
            ELSE 'Unknown'
        END as booking_description,
        CASE 
            WHEN p.booking_type = 'room' THEN b.booking_reference
            WHEN p.booking_type = 'conference' THEN ci.enquiry_reference
            ELSE NULL
        END as booking_reference,
        CASE 
            WHEN p.booking_type = 'room' THEN b.guest_name
            WHEN p.booking_type = 'conference' THEN ci.contact_name
            ELSE NULL
        END as customer_name,
        CASE 
            WHEN p.booking_type = 'room' THEN b.guest_email
            WHEN p.booking_type = 'conference' THEN ci.contact_email
            ELSE NULL
        END as customer_email,
        CASE 
            WHEN p.booking_type = 'room' THEN b.guest_phone
            WHEN p.booking_type = 'conference' THEN ci.contact_phone
            ELSE NULL
        END as customer_phone
    FROM payments p
    LEFT JOIN bookings b ON p.booking_type = 'room' AND p.booking_id = b.id
    LEFT JOIN conference_inquiries ci ON p.booking_type = 'conference' AND p.booking_id = ci.id
    WHERE p.id = ? AND p.deleted_at IS NULL
");
$stmt->execute([$paymentId]);
$payment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$payment) {
    $_SESSION['alert'] = ['type' => 'info', 'message' => 'Payment not found. It may have been deleted or does not exist.'];
    header('Location: payments.php');
    exit;
}

// Get booking details
$bookingDetails = null;
if ($payment['booking_type'] === 'room') {
    $bookingStmt = $pdo->prepare("
        SELECT 
            b.*,
            r.name as room_name,
            r.price_per_night
        FROM bookings b
        LEFT JOIN rooms r ON b.room_id = r.id
        WHERE b.id = ?
    ");
    $bookingStmt->execute([$payment['booking_id']]);
    $booking = $bookingStmt->fetch(PDO::FETCH_ASSOC);
    
    if ($booking) {
        $bookingDetails = [
            'type' => 'room',
            'id' => (int)$booking['id'],
            'reference' => $booking['booking_reference'],
            'room' => [
                'id' => (int)$booking['room_id'],
                'name' => $booking['room_name'],
                'price_per_night' => (float)$booking['price_per_night']
            ],
            'guest' => [
                'name' => $booking['guest_name'],
                'email' => $booking['guest_email'],
                'phone' => $booking['guest_phone']
            ],
            'dates' => [
                'check_in' => $booking['check_in_date'],
                'check_out' => $booking['check_out_date'],
                'nights' => (int)$booking['number_of_nights']
            ],
            'amounts' => [
                'total_amount' => (float)$booking['total_amount'],
                'amount_paid' => (float)$booking['amount_paid'],
                'amount_due' => (float)$booking['amount_due'],
                'vat_rate' => (float)$booking['vat_rate'],
                'vat_amount' => (float)$booking['vat_amount'],
                'total_with_vat' => (float)$booking['total_with_vat']
            ],
            'status' => $booking['status']
        ];
    }
} elseif ($payment['booking_type'] === 'conference') {
    $confStmt = $pdo->prepare("SELECT * FROM conference_inquiries WHERE id = ?");
    $confStmt->execute([$payment['booking_id']]);
    $enquiry = $confStmt->fetch(PDO::FETCH_ASSOC);
    
    if ($enquiry) {
        $bookingDetails = [
            'type' => 'conference',
            'id' => (int)$enquiry['id'],
            'reference' => $enquiry['enquiry_reference'],
            'organization' => [
                'name' => $enquiry['organization_name'],
                'contact_person' => $enquiry['contact_name'],
                'email' => $enquiry['contact_email'],
                'phone' => $enquiry['contact_phone']
            ],
            'event' => [
                'type' => $enquiry['event_type'],
                'start_date' => $enquiry['start_date'],
                'end_date' => $enquiry['end_date'],
                'expected_attendees' => (int)$enquiry['expected_attendees']
            ],
            'amounts' => [
                'total_amount' => (float)$enquiry['total_amount'],
                'amount_paid' => (float)$enquiry['amount_paid'],
                'amount_due' => (float)$enquiry['amount_due'],
                'vat_rate' => (float)$enquiry['vat_rate'],
                'vat_amount' => (float)$enquiry['vat_amount'],
                'total_with_vat' => (float)$enquiry['total_with_vat'],
                'deposit_required' => (float)$enquiry['deposit_required'],
                'deposit_amount' => (float)$enquiry['deposit_amount'],
                'deposit_paid' => (float)$enquiry['deposit_paid']
            ],
            'status' => $enquiry['status']
        ];
    }
}

// Get other payments for this booking
$otherPaymentsStmt = $pdo->prepare("
    SELECT * FROM payments
    WHERE booking_type = ? AND booking_id = ? AND id != ? AND deleted_at IS NULL
    ORDER BY payment_date DESC, created_at DESC
");
$otherPaymentsStmt->execute([$payment['booking_type'], $payment['booking_id'], $paymentId]);
$otherPayments = $otherPaymentsStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Details | <?php echo htmlspecialchars($site_name); ?> Admin</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="css/admin-styles.css">
    
    <style>
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 16px;
        }
        
        .details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 24px;
            margin-bottom: 24px;
        }
        
        .detail-card {
            background: white;
            padding: 24px;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow);
        }
        
        .detail-card h3 {
            margin-bottom: 20px;
            color: var(--navy);
            font-size: 18px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #eee;
        }
        
        .detail-row:last-child {
            border-bottom: none;
        }
        
        .detail-label {
            font-weight: 500;
            color: #666;
        }
        
        .detail-value {
            font-weight: 600;
            color: var(--navy);
            text-align: right;
        }
        
        .detail-value.large {
            font-size: 20px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        }
        
        .badge-completed {
            background: #d4edda;
            color: #155724;
        }
        
        .badge-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .badge-refunded {
            background: #f8d7da;
            color: #721c24;
        }
        
        .badge-partially_refunded {
            background: #e2e3e5;
            color: #383d41;
        }
        
        .badge-failed {
            background: #f8d7da;
            color: #721c24;
        }
        
        .receipt-preview {
            background: #f8f9fa;
            border: 2px dashed #ddd;
            padding: 24px;
            border-radius: var(--radius);
            text-align: center;
            margin-top: 20px;
        }
        
        .receipt-preview.has-receipt {
            background: #e7f3ff;
            border-color: var(--navy);
        }
        
        .receipt-number {
            font-size: 24px;
            font-weight: 700;
            color: var(--navy);
            margin-bottom: 8px;
        }
        
        .booking-summary {
            background: #f8f9fa;
            padding: 16px;
            border-radius: var(--radius);
            margin-bottom: 20px;
        }
        
        .booking-summary h4 {
            margin-bottom: 12px;
            color: var(--navy);
        }
        
        .booking-summary p {
            margin: 6px 0;
            font-size: 14px;
        }
        
        .other-payments {
            margin-top: 24px;
        }
        
        .other-payments h4 {
            margin-bottom: 16px;
            color: var(--navy);
        }
        
        .payment-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            background: #f8f9fa;
            border-radius: var(--radius);
            margin-bottom: 8px;
        }
        
        .payment-item-info {
            flex: 1;
        }
        
        .payment-item-ref {
            font-weight: 600;
            color: var(--navy);
        }
        
        .payment-item-date {
            font-size: 12px;
            color: #666;
        }
        
        .payment-item-amount {
            font-weight: 600;
            color: var(--navy);
        }
        
        .action-buttons {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
        
        .action-buttons a,
        .action-buttons button {
            padding: 10px 20px;
            border-radius: var(--radius);
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            border: none;
            font-family: inherit;
        }
        
        .btn-primary {
            background: var(--navy);
            color: white;
        }
        
        .btn-primary:hover {
            background: var(--gold);
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .btn-danger:hover {
            background: #c82333;
        }
    </style>
</head>
<body>

    <?php include 'admin-header.php'; ?>

    <div class="content">
        <div class="page-header">
            <div>
                <h2 class="section-title">Payment Details</h2>
                <p style="color: #666; margin-top: 4px;">
                    Reference: <strong><?php echo htmlspecialchars($payment['payment_reference']); ?></strong>
                </p>
            </div>
            
            <div class="action-buttons">
                <?php if ($payment['payment_status'] !== 'completed'): ?>
                    <a href="payment-add.php?edit=<?php echo $paymentId; ?>" class="btn-primary">
                        <i class="fas fa-edit"></i> Edit Payment
                    </a>
                <?php endif; ?>
                <a href="payments.php" class="btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Payments
                </a>
            </div>
        </div>

        <div class="details-grid">
            <!-- Payment Information -->
            <div class="detail-card">
                <h3><i class="fas fa-money-bill-wave"></i> Payment Information</h3>
                
                <div class="detail-row">
                    <span class="detail-label">Payment Reference</span>
                    <span class="detail-value"><?php echo htmlspecialchars($payment['payment_reference']); ?></span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Payment Date</span>
                    <span class="detail-value"><?php echo date('F j, Y', strtotime($payment['payment_date'])); ?></span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Payment Method</span>
                    <span class="detail-value"><?php echo ucfirst(str_replace('_', ' ', $payment['payment_method'])); ?></span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Status</span>
                    <span class="detail-value">
                        <span class="status-badge badge-<?php echo $payment['payment_status']; ?>">
                            <?php echo ucfirst(str_replace('_', ' ', $payment['payment_status'])); ?>
                        </span>
                    </span>
                </div>
                
                <?php if ($payment['transaction_reference']): ?>
                    <div class="detail-row">
                        <span class="detail-label">Transaction Reference</span>
                        <span class="detail-value"><?php echo htmlspecialchars($payment['transaction_reference']); ?></span>
                    </div>
                <?php endif; ?>
                
                <div class="detail-row">
                    <span class="detail-label">Processed By</span>
                    <span class="detail-value"><?php echo htmlspecialchars($payment['processed_by'] ?? 'System'); ?></span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Created</span>
                    <span class="detail-value"><?php echo date('F j, Y g:i A', strtotime($payment['created_at'])); ?></span>
                </div>
                
                <?php if ($payment['updated_at'] !== $payment['created_at']): ?>
                    <div class="detail-row">
                        <span class="detail-label">Last Updated</span>
                        <span class="detail-value"><?php echo date('F j, Y g:i A', strtotime($payment['updated_at'])); ?></span>
                    </div>
                <?php endif; ?>
                
                <?php if ($payment['notes']): ?>
                    <div class="detail-row">
                        <span class="detail-label">Notes</span>
                        <span class="detail-value" style="text-align: left; font-weight: 400;">
                            <?php echo nl2br(htmlspecialchars($payment['notes'])); ?>
                        </span>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Amount Breakdown -->
            <div class="detail-card">
                <h3><i class="fas fa-calculator"></i> Amount Breakdown</h3>
                
                <div class="detail-row">
                    <span class="detail-label">Subtotal (excl. VAT)</span>
                    <span class="detail-value"><?php echo $currency_symbol; ?><?php echo number_format($payment['payment_amount'], 2); ?></span>
                </div>
                
                <?php if ($payment['vat_amount'] > 0): ?>
                    <div class="detail-row">
                        <span class="detail-label">VAT Rate</span>
                        <span class="detail-value"><?php echo number_format($payment['vat_rate'], 2); ?>%</span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">VAT Amount</span>
                        <span class="detail-value"><?php echo $currency_symbol; ?><?php echo number_format($payment['vat_amount'], 2); ?></span>
                    </div>
                <?php endif; ?>
                
                <div class="detail-row">
                    <span class="detail-label">Total Amount</span>
                    <span class="detail-value large"><?php echo $currency_symbol; ?><?php echo number_format($payment['total_amount'], 2); ?></span>
                </div>
                
                <!-- Receipt Information -->
                <div class="receipt-preview <?php echo $payment['receipt_number'] ? 'has-receipt' : ''; ?>">
                    <?php if ($payment['receipt_number']): ?>
                        <i class="fas fa-receipt" style="font-size: 32px; color: var(--navy); margin-bottom: 12px;"></i>
                        <div class="receipt-number"><?php echo htmlspecialchars($payment['receipt_number']); ?></div>
                        <p style="color: #666;">Receipt Generated</p>
                    <?php else: ?>
                        <i class="fas fa-clock" style="font-size: 32px; color: #999; margin-bottom: 12px;"></i>
                        <p style="color: #999;">No receipt generated</p>
                        <p style="font-size: 12px; color: #999;">Receipt will be generated when payment is completed</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Booking Information -->
        <?php if ($bookingDetails): ?>
            <div class="detail-card" style="margin-bottom: 24px;">
                <h3><i class="fas fa-calendar-check"></i> Booking Information</h3>
                
                <div class="booking-summary">
                    <h4><?php echo ucfirst($bookingDetails['type']); ?> Booking</h4>
                    
                    <?php if ($bookingDetails['type'] === 'room'): ?>
                        <p><strong>Reference:</strong> <?php echo htmlspecialchars($bookingDetails['reference']); ?></p>
                        <p><strong>Room:</strong> <?php echo htmlspecialchars($bookingDetails['room']['name']); ?></p>
                        <p><strong>Guest:</strong> <?php echo htmlspecialchars($bookingDetails['guest']['name']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($bookingDetails['guest']['email']); ?></p>
                        <p><strong>Dates:</strong> <?php echo date('M j, Y', strtotime($bookingDetails['dates']['check_in'])); ?> - <?php echo date('M j, Y', strtotime($bookingDetails['dates']['check_out'])); ?> (<?php echo $bookingDetails['dates']['nights']; ?> nights)</p>
                        <p><strong>Total Amount:</strong> <?php echo $currency_symbol; ?><?php echo number_format($bookingDetails['amounts']['total_amount'], 0); ?></p>
                        <p><strong>Amount Paid:</strong> <span style="color: #28a745;"><?php echo $currency_symbol; ?><?php echo number_format($bookingDetails['amounts']['amount_paid'], 0); ?></span></p>
                        <p><strong>Amount Due:</strong> <span style="color: <?php echo $bookingDetails['amounts']['amount_due'] > 0 ? '#dc3545' : '#28a745'; ?>;"><?php echo $currency_symbol; ?><?php echo number_format($bookingDetails['amounts']['amount_due'], 0); ?></span></p>
                        <?php if ($bookingDetails['amounts']['vat_amount'] > 0): ?>
                            <p><strong>VAT:</strong> <?php echo $currency_symbol; ?><?php echo number_format($bookingDetails['amounts']['vat_amount'], 0); ?> (<?php echo $bookingDetails['amounts']['vat_rate']; ?>%)</p>
                        <?php endif; ?>
                        <p><strong>Status:</strong> <span class="status-badge badge-<?php echo $bookingDetails['status']; ?>"><?php echo ucfirst($bookingDetails['status']); ?></span></p>
                    <?php else: ?>
                        <p><strong>Reference:</strong> <?php echo htmlspecialchars($bookingDetails['reference']); ?></p>
                        <p><strong>Organization:</strong> <?php echo htmlspecialchars($bookingDetails['organization']['name']); ?></p>
                        <p><strong>Contact:</strong> <?php echo htmlspecialchars($bookingDetails['organization']['contact_person']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($bookingDetails['organization']['email']); ?></p>
                        <p><strong>Event Type:</strong> <?php echo htmlspecialchars($bookingDetails['event']['type']); ?></p>
                        <p><strong>Dates:</strong> <?php echo date('M j, Y', strtotime($bookingDetails['event']['start_date'])); ?> - <?php echo date('M j, Y', strtotime($bookingDetails['event']['end_date'])); ?></p>
                        <p><strong>Total Amount:</strong> <?php echo $currency_symbol; ?><?php echo number_format($bookingDetails['amounts']['total_amount'], 0); ?></p>
                        <p><strong>Amount Paid:</strong> <span style="color: #28a745;"><?php echo $currency_symbol; ?><?php echo number_format($bookingDetails['amounts']['amount_paid'], 0); ?></span></p>
                        <p><strong>Amount Due:</strong> <span style="color: <?php echo $bookingDetails['amounts']['amount_due'] > 0 ? '#dc3545' : '#28a745'; ?>;"><?php echo $currency_symbol; ?><?php echo number_format($bookingDetails['amounts']['amount_due'], 0); ?></span></p>
                        <?php if ($bookingDetails['amounts']['deposit_required'] > 0): ?>
                            <p><strong>Deposit Required:</strong> <?php echo $currency_symbol; ?><?php echo number_format($bookingDetails['amounts']['deposit_required'], 0); ?> (Paid: <?php echo $currency_symbol; ?><?php echo number_format($bookingDetails['amounts']['deposit_paid'], 0); ?>)</p>
                        <?php endif; ?>
                        <?php if ($bookingDetails['amounts']['vat_amount'] > 0): ?>
                            <p><strong>VAT:</strong> <?php echo $currency_symbol; ?><?php echo number_format($bookingDetails['amounts']['vat_amount'], 0); ?> (<?php echo $bookingDetails['amounts']['vat_rate']; ?>%)</p>
                        <?php endif; ?>
                        <p><strong>Status:</strong> <span class="status-badge badge-<?php echo $bookingDetails['status']; ?>"><?php echo ucfirst($bookingDetails['status']); ?></span></p>
                    <?php endif; ?>
                </div>
                
                <a href="<?php echo $bookingDetails['type'] === 'room' ? 'booking-details.php?id=' . $bookingDetails['id'] : 'conference-management.php'; ?>" class="btn-primary" style="display: inline-block; padding: 10px 20px; text-decoration: none;">
                    <i class="fas fa-external-link-alt"></i> View Full Booking Details
                </a>
            </div>
        <?php endif; ?>

        <!-- Other Payments for this Booking -->
        <?php if (!empty($otherPayments)): ?>
            <div class="detail-card">
                <h3><i class="fas fa-list"></i> Other Payments for this Booking</h3>
                
                <div class="other-payments">
                    <?php foreach ($otherPayments as $otherPayment): ?>
                        <div class="payment-item">
                            <div class="payment-item-info">
                                <div class="payment-item-ref"><?php echo htmlspecialchars($otherPayment['payment_reference']); ?></div>
                                <div class="payment-item-date"><?php echo date('M j, Y', strtotime($otherPayment['payment_date'])); ?></div>
                            </div>
                            <div class="payment-item-amount">
                                <?php echo $currency_symbol; ?><?php echo number_format($otherPayment['total_amount'], 0); ?>
                                <span class="status-badge badge-<?php echo $otherPayment['payment_status']; ?>" style="margin-left: 8px;">
                                    <?php echo ucfirst(str_replace('_', ' ', $otherPayment['payment_status'])); ?>
                                </span>
                            </div>
                            <a href="payment-details.php?id=<?php echo $otherPayment['id']; ?>" class="btn-secondary" style="padding: 6px 12px; font-size: 12px; margin-left: 12px;">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>
