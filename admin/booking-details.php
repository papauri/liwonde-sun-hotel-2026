<?php
session_start();

// Check authentication
if (!isset($_SESSION['admin_user'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';

$user = $_SESSION['admin_user'];
$booking_id = filter_var($_GET['id'] ?? 0, FILTER_VALIDATE_INT);
$action = $_GET['action'] ?? '';

if (!$booking_id) {
    header('Location: dashboard.php');
    exit;
}

// Handle status changes
if ($action && $_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        switch ($action) {
            case 'confirm':
                $stmt = $pdo->prepare("UPDATE bookings SET status = 'confirmed', updated_at = NOW() WHERE id = ? AND status = 'pending'");
                $stmt->execute([$booking_id]);
                $_SESSION['success_message'] = 'Booking confirmed successfully.';
                break;
            
            case 'checkin':
                $stmt = $pdo->prepare("UPDATE bookings SET status = 'checked-in', updated_at = NOW() WHERE id = ? AND status = 'confirmed'");
                $stmt->execute([$booking_id]);
                $_SESSION['success_message'] = 'Guest checked in successfully.';
                break;
            
            case 'checkout':
                $stmt = $pdo->prepare("UPDATE bookings SET status = 'checked-out', updated_at = NOW() WHERE id = ? AND status = 'checked-in'");
                $stmt->execute([$booking_id]);
                $_SESSION['success_message'] = 'Guest checked out successfully.';
                break;
            
            case 'cancel':
                $stmt = $pdo->prepare("UPDATE bookings SET status = 'cancelled', updated_at = NOW() WHERE id = ?");
                $stmt->execute([$booking_id]);
                $_SESSION['success_message'] = 'Booking cancelled.';
                break;
        }
    } catch (PDOException $e) {
        $_SESSION['error_message'] = 'Action failed. Please try again.';
    }
    
    header('Location: dashboard.php');
    exit;
}

// Fetch booking details
try {
    $stmt = $pdo->prepare("
        SELECT b.*, r.name as room_name, r.price_per_night
        FROM bookings b
        JOIN rooms r ON b.room_id = r.id
        WHERE b.id = ?
    ");
    $stmt->execute([$booking_id]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$booking) {
        $_SESSION['error_message'] = 'Booking not found.';
        header('Location: dashboard.php');
        exit;
    }

    // Fetch booking notes
    $notes_stmt = $pdo->prepare("
        SELECT n.*, u.full_name as created_by_name 
        FROM booking_notes n
        LEFT JOIN admin_users u ON n.created_by = u.id
        WHERE n.booking_id = ?
        ORDER BY n.created_at DESC
    ");
    $notes_stmt->execute([$booking_id]);
    $notes = $notes_stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $_SESSION['error_message'] = 'Unable to load booking details.';
    header('Location: dashboard.php');
    exit;
}

// Handle note submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_note'])) {
    $note_text = trim($_POST['note_text'] ?? '');
    
    if ($note_text) {
        try {
            $insert_stmt = $pdo->prepare("INSERT INTO booking_notes (booking_id, note_text, created_by) VALUES (?, ?, ?)");
            $insert_stmt->execute([$booking_id, $note_text, $user['id']]);
            $_SESSION['success_message'] = 'Note added successfully.';
            header("Location: booking-details.php?id=$booking_id");
            exit;
        } catch (PDOException $e) {
            $error_message = 'Failed to add note.';
        }
    }
}

// Handle payment status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_payment'])) {
    $payment_status = $_POST['payment_status'];
    $previous_status = $booking['payment_status'];
    
    try {
        $update_stmt = $pdo->prepare("UPDATE bookings SET payment_status = ?, updated_at = NOW() WHERE id = ?");
        $update_stmt->execute([$payment_status, $booking_id]);
        
        $_SESSION['success_message'] = 'Payment status updated.';
        
        // Send invoice email if payment status changed to 'paid'
        if ($payment_status === 'paid' && $previous_status !== 'paid') {
            require_once '../config/invoice.php';
            $invoice_result = sendPaymentInvoiceEmail($booking_id);
            
            if ($invoice_result['success']) {
                $_SESSION['success_message'] .= ' Invoice sent successfully!';
            } else {
                error_log("Invoice email failed: " . $invoice_result['message']);
                $_SESSION['success_message'] .= ' (Invoice email failed - check logs)';
            }
        }
        
        header("Location: booking-details.php?id=$booking_id");
        exit;
    } catch (PDOException $e) {
        $error_message = 'Failed to update payment status.';
    }
}

$site_name = getSetting('site_name');
$currency_symbol = getSetting('currency_symbol');
$current_page = 'bookings.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Details | <?php echo htmlspecialchars($site_name); ?> Admin</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin-styles.css">
    
    <style>
        /* Booking details specific styles */
        .booking-details-container {
            max-width: 1200px;
            margin: 32px auto;
            padding: 0 32px;
        }
        .details-card {
            background: white;
            border-radius: 12px;
            padding: 32px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            margin-bottom: 24px;
        }
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 2px solid #f0f0f0;
        }
        .card-header h2 {
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            color: var(--navy);
        }
        .booking-ref {
            font-family: 'Courier New', monospace;
            font-size: 20px;
            font-weight: 700;
            color: var(--gold);
        }
        .details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 24px;
            margin-bottom: 24px;
        }
        .detail-item label {
            display: block;
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 6px;
        }
        .detail-item .value {
            font-size: 16px;
            font-weight: 600;
            color: var(--navy);
        }
        .status-badge {
            display: inline-block;
            padding: 6px 16px;
            border-radius: 16px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        .status-confirmed {
            background: #d1ecf1;
            color: #0c5460;
        }
        .status-checked-in {
            background: #d4edda;
            color: #155724;
        }
        .status-checked-out {
            background: #e2e3e5;
            color: #383d41;
        }
        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }
        .action-buttons {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
        .btn {
            padding: 10px 24px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        .btn-success {
            background: #28a745;
            color: white;
        }
        .btn-primary {
            background: var(--gold);
            color: var(--deep-navy);
        }
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        .btn-warning {
            background: #ffc107;
            color: var(--deep-navy);
        }
        .notes-section {
            margin-top: 32px;
        }
        .note-item {
            background: #f8f9fa;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 12px;
            border-left: 3px solid var(--gold);
        }
        .note-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 12px;
            color: #666;
        }
        .note-text {
            color: var(--navy);
            line-height: 1.6;
        }
        .add-note-form textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            resize: vertical;
            min-height: 80px;
        }
        .add-note-form textarea:focus {
            outline: none;
            border-color: var(--gold);
        }
        select.form-control {
            padding: 10px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
        }
        @media (max-width: 768px) {
            .booking-details-container {
                padding: 0 20px;
            }
            .details-grid {
                grid-template-columns: 1fr;
            }
            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
	<?php include 'admin-header.php'; ?>

    <div class="booking-details-container">
        <div class="details-card">
                    <div class="value"><?php echo htmlspecialchars($booking['guest_phone']); ?></div>
                </div>
                <div class="detail-item">
                    <label>Country</label>
                    <div class="value"><?php echo htmlspecialchars($booking['guest_country'] ?: 'N/A'); ?></div>
                </div>
                <div class="detail-item">
                    <label>Room</label>
                    <div class="value"><?php echo htmlspecialchars($booking['room_name']); ?></div>
                </div>
                <div class="detail-item">
                    <label>Number of Guests</label>
                    <div class="value"><?php echo $booking['number_of_guests']; ?></div>
                </div>
                <div class="detail-item">
                    <label>Check-in Date</label>
                    <div class="value"><?php echo date('M j, Y', strtotime($booking['check_in_date'])); ?></div>
                </div>
                <div class="detail-item">
                    <label>Check-out Date</label>
                    <div class="value"><?php echo date('M j, Y', strtotime($booking['check_out_date'])); ?></div>
                </div>
                <div class="detail-item">
                    <label>Number of Nights</label>
                    <div class="value"><?php echo $booking['number_of_nights']; ?></div>
                </div>
                <div class="detail-item">
                    <label>Total Amount</label>
                    <div class="value" style="color: var(--gold); font-size: 20px;">
                        <?php echo $currency_symbol; ?><?php echo number_format($booking['total_amount'], 0); ?>
                    </div>
                </div>
                <div class="detail-item">
                    <label>Booking Status</label>
                    <div class="value">
                        <span class="status-badge status-<?php echo $booking['status']; ?>">
                            <?php echo ucfirst($booking['status']); ?>
                        </span>
                    </div>
                </div>
                <div class="detail-item">
                    <label>Payment Status</label>
                    <div class="value">
                        <form method="POST" style="display: inline;">
                            <select name="payment_status" class="form-control" onchange="this.form.submit()">
                                <option value="unpaid" <?php echo $booking['payment_status'] == 'unpaid' ? 'selected' : ''; ?>>Unpaid</option>
                                <option value="partial" <?php echo $booking['payment_status'] == 'partial' ? 'selected' : ''; ?>>Partial</option>
                                <option value="paid" <?php echo $booking['payment_status'] == 'paid' ? 'selected' : ''; ?>>Paid</option>
                            </select>
                            <input type="hidden" name="update_payment" value="1">
                        </form>
                    </div>
                </div>
            </div>

            <?php if ($booking['special_requests']): ?>
            <div class="detail-item" style="margin-top: 20px;">
                <label>Special Requests</label>
                <div class="value"><?php echo nl2br(htmlspecialchars($booking['special_requests'])); ?></div>
            </div>
            <?php endif; ?>

            <div style="margin-top: 32px;">
                <label style="display: block; margin-bottom: 12px; font-weight: 600;">Quick Actions</label>
                <div class="action-buttons">
                    <?php if ($booking['status'] == 'pending'): ?>
                    <a href="booking-details.php?id=<?php echo $booking_id; ?>&action=confirm" class="btn btn-success" onclick="return confirm('Confirm this booking?')">
                        <i class="fas fa-check"></i> Confirm Booking
                    </a>
                    <?php endif; ?>

                    <?php if ($booking['status'] == 'confirmed'): ?>
                    <a href="booking-details.php?id=<?php echo $booking_id; ?>&action=checkin" class="btn btn-primary" onclick="return confirm('Check in this guest?')">
                        <i class="fas fa-sign-in-alt"></i> Check In
                    </a>
                    <?php endif; ?>

                    <?php if ($booking['status'] == 'checked-in'): ?>
                    <a href="booking-details.php?id=<?php echo $booking_id; ?>&action=checkout" class="btn btn-warning" onclick="return confirm('Check out this guest?')">
                        <i class="fas fa-sign-out-alt"></i> Check Out
                    </a>
                    <?php endif; ?>

                    <?php if (!in_array($booking['status'], ['checked-out', 'cancelled'])): ?>
                    <a href="booking-details.php?id=<?php echo $booking_id; ?>&action=cancel" class="btn btn-danger" onclick="return confirm('Cancel this booking? This cannot be undone.')">
                        <i class="fas fa-times"></i> Cancel Booking
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="details-card">
            <h3 style="margin-bottom: 20px; color: var(--navy);">Internal Notes</h3>
            
            <div class="add-note-form">
                <form method="POST">
                    <textarea name="note_text" placeholder="Add a note about this booking..." required></textarea>
                    <button type="submit" name="add_note" class="btn btn-primary" style="margin-top: 12px;">
                        <i class="fas fa-plus"></i> Add Note
                    </button>
                </form>
            </div>

            <div class="notes-section">
                <?php if (empty($notes)): ?>
                <p style="color: #999; text-align: center; padding: 20px;">No notes yet</p>
                <?php else: ?>
                <?php foreach ($notes as $note): ?>
                <div class="note-item">
                    <div class="note-header">
                        <span><strong><?php echo htmlspecialchars($note['created_by_name'] ?? 'Unknown'); ?></strong></span>
                        <span><?php echo date('M j, Y g:i A', strtotime($note['created_at'])); ?></span>
                    </div>
                    <div class="note-text"><?php echo nl2br(htmlspecialchars($note['note_text'])); ?></div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
