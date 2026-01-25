<?php
session_start();

// Check authentication
if (!isset($_SESSION['admin_user'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';

$user = $_SESSION['admin_user'];
$message = '';
$error = '';

// Handle booking actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $action = $_POST['action'] ?? '';

        if ($action === 'update_status') {
            $booking_id = (int)($_POST['id'] ?? 0);
            $new_status = $_POST['status'] ?? '';

            if ($booking_id <= 0) {
                throw new Exception('Invalid booking id');
            }

            // Enforce business rules:
            // - Check-in only allowed when confirmed AND paid
            // - Cancel check-in (undo) allowed only when currently checked-in
            if ($new_status === 'checked-in') {
                $stmt = $pdo->prepare("UPDATE bookings SET status = 'checked-in' WHERE id = ? AND status = 'confirmed' AND payment_status = 'paid'");
                $stmt->execute([$booking_id]);
                if ($stmt->rowCount() === 0) {
                    $check = $pdo->prepare("SELECT status, payment_status FROM bookings WHERE id = ?");
                    $check->execute([$booking_id]);
                    $row = $check->fetch(PDO::FETCH_ASSOC);
                    if (!$row) {
                        throw new Exception('Booking not found');
                    }
                    throw new Exception("Cannot check in unless booking is CONFIRMED and PAID (current: status={$row['status']}, payment={$row['payment_status']})");
                }
                $message = 'Guest checked in!';

            } elseif ($new_status === 'cancel-checkin') {
                $stmt = $pdo->prepare("UPDATE bookings SET status = 'confirmed' WHERE id = ? AND status = 'checked-in'");
                $stmt->execute([$booking_id]);
                if ($stmt->rowCount() === 0) {
                    $check = $pdo->prepare("SELECT status FROM bookings WHERE id = ?");
                    $check->execute([$booking_id]);
                    $row = $check->fetch(PDO::FETCH_ASSOC);
                    if (!$row) {
                        throw new Exception('Booking not found');
                    }
                    throw new Exception("Cannot cancel check-in unless booking is currently checked-in (current: {$row['status']})");
                }
                $message = 'Check-in cancelled (reverted to confirmed).';
            } else {
                $allowed = ['pending', 'confirmed', 'checked-out', 'cancelled'];
                if (!in_array($new_status, $allowed, true)) {
                    throw new Exception('Invalid status');
                }
                $stmt = $pdo->prepare("UPDATE bookings SET status = ? WHERE id = ?");
                $stmt->execute([$new_status, $booking_id]);
                $message = 'Booking status updated!';
            }

        } elseif ($action === 'update_payment') {
            $stmt = $pdo->prepare("UPDATE bookings SET payment_status = ? WHERE id = ?");
            $stmt->execute([$_POST['payment_status'], $_POST['id']]);
            $message = 'Payment status updated!';
        }

    } catch (Throwable $e) {
        $error = 'Error: ' . $e->getMessage();
    }
}

// Fetch all bookings with room details
try {
    $stmt = $pdo->query("
        SELECT b.*, r.name as room_name 
        FROM bookings b
        LEFT JOIN rooms r ON b.room_id = r.id
        ORDER BY b.created_at DESC
    ");
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Also fetch conference inquiries
    $conf_stmt = $pdo->query("
        SELECT * FROM conference_inquiries 
        ORDER BY created_at DESC
    ");
    $conference_inquiries = $conf_stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error = 'Error fetching bookings: ' . $e->getMessage();
    $bookings = [];
    $conference_inquiries = [];
}

// Count statistics
$total_bookings = count($bookings);
$pending = count(array_filter($bookings, fn($b) => $b['status'] === 'pending'));
$confirmed = count(array_filter($bookings, fn($b) => $b['status'] === 'confirmed'));
$checked_in = count(array_filter($bookings, fn($b) => $b['status'] === 'checked-in'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Bookings - Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --gold: #d4af37;
            --navy: #142841;
            --deep-navy: #0f1d2e;
            --cream: #fbf8f3;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--cream);
            color: #333;
        }
        .admin-header {
            background: linear-gradient(135deg, var(--deep-navy) 0%, var(--navy) 100%);
            color: white;
            padding: 20px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .admin-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            color: var(--gold);
        }
        .admin-header .user-info {
            display: flex;
            align-items: center;
            gap: 24px;
        }
        .admin-header .user-name {
            font-size: 14px;
        }
        .admin-header .user-role {
            background: var(--gold);
            color: var(--deep-navy);
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .btn-logout {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            padding: 8px 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 6px;
            text-decoration: none;
            font-size: 13px;
            transition: all 0.3s ease;
        }
        .btn-logout:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        .admin-nav {
            background: white;
            border-bottom: 1px solid #e0e0e0;
            padding: 0 32px;
        }
        .admin-nav ul {
            list-style: none;
            display: flex;
            gap: 32px;
        }
        .admin-nav a {
            display: block;
            padding: 16px 0;
            color: #666;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            border-bottom: 2px solid transparent;
            transition: all 0.3s ease;
            white-space: nowrap;
        }
        .admin-nav a:hover,
        .admin-nav a.active {
            color: var(--gold);
            border-bottom-color: var(--gold);
        }
        .content {
            padding: 32px;
            max-width: 100%;
            margin: 0 auto;
            overflow-x: auto;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }
        .stat-card h3 {
            font-size: 14px;
            color: #666;
            margin-bottom: 8px;
        }
        .stat-card .number {
            font-size: 32px;
            font-weight: 700;
            color: var(--navy);
        }
        .stat-card.pending .number { color: #ffc107; }
        .stat-card.confirmed .number { color: #28a745; }
        .stat-card.checked-in .number { color: #17a2b8; }
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }
        .page-title {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            color: var(--navy);
        }
        .alert {
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .bookings-section {
            background: white;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            overflow-x: auto;
        }
        .section-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--navy);
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 2px solid var(--gold);
        }
        .booking-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1800px;
            border: 1px solid #d0d7de;
        }
        .booking-table th {
            background: #f6f8fa;
            padding: 12px;
            text-align: left;
            font-size: 13px;
            font-weight: 600;
            color: #666;
            text-transform: uppercase;
            border: 1px solid #d0d7de;
        }
        .booking-table td {
            padding: 12px;
            border: 1px solid #d0d7de;
            vertical-align: middle;
            background: white;
        }
        .booking-table tbody tr:hover {
            background: #f8f9fa;
        }
        .badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            white-space: nowrap;
        }
        .badge-pending { background: #ffc107; color: #212529; }
        .badge-confirmed { background: #28a745; color: white; }
        .badge-checked-in { background: #17a2b8; color: white; }
        .badge-checked-out { background: #6c757d; color: white; }
        .badge-cancelled { background: #dc3545; color: white; }
        .badge-unpaid { background: #dc3545; color: white; }
        .badge-partial { background: #ffc107; color: #212529; }
        .badge-paid { background: #28a745; color: white; }
        .badge-new { background: #17a2b8; color: white; }
        .badge-contacted { background: #6c757d; color: white; }
        .quick-action {
            padding: 6px 14px;
            border: none;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-right: 4px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }
        .quick-action:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }
        .quick-action.confirm {
            background: #28a745;
            color: white;
        }
        .quick-action.confirm:hover {
            background: #229954;
        }
        .quick-action.check-in {
            background: #17a2b8;
            color: white;
        }
        .quick-action.check-in:hover {
            background: #138496;
        }
        .quick-action.undo-checkin {
            background: #6c757d;
            color: white;
        }
        .quick-action.undo-checkin:hover {
            background: #5a6268;
        }
        .quick-action.disabled {
            opacity: 0.55;
            cursor: not-allowed;
        }
        .quick-action.paid {
            background: var(--gold);
            color: var(--deep-navy);
        }
        .quick-action.paid:hover {
            background: #c19b2e;
        }
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #999;
        }
        .empty-state i {
            font-size: 48px;
            margin-bottom: 16px;
            color: #ddd;
        }
        @media (max-width: 768px) {
            .content {
                padding: 16px;
            }
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }
            .stat-card {
                padding: 16px;
            }
            .stat-card .number {
                font-size: 24px;
            }
            .booking-table {
                font-size: 12px;
            }
            .booking-table th,
            .booking-table td {
                padding: 8px;
            }
            .booking-table th {
                font-size: 11px;
            }
        }
        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            .booking-table {
                font-size: 11px;
            }
            .booking-table th,
            .booking-table td {
                padding: 6px;
            }
        }
    </style>
</head>
<body>
    <?php include 'admin-header.php'; ?>


    <div class="content">
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Bookings</h3>
                <div class="number"><?php echo $total_bookings; ?></div>
            </div>
            <div class="stat-card pending">
                <h3>Pending</h3>
                <div class="number"><?php echo $pending; ?></div>
            </div>
            <div class="stat-card confirmed">
                <h3>Confirmed</h3>
                <div class="number"><?php echo $confirmed; ?></div>
            </div>
            <div class="stat-card checked-in">
                <h3>Checked In</h3>
                <div class="number"><?php echo $checked_in; ?></div>
            </div>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <!-- Room Bookings -->
        <div class="bookings-section">
            <h3 class="section-title">
                <i class="fas fa-bed"></i> Room Bookings
                <span style="font-size: 14px; font-weight: normal; color: #666;">
                    (<?php echo count($bookings); ?> total)
                </span>
            </h3>

            <?php if (!empty($bookings)): ?>
                <table class="booking-table">
                    <thead>
                        <tr>
                            <th style="width: 120px;">Ref</th>
                            <th style="width: 200px;">Guest Name</th>
                            <th style="width: 180px;">Room</th>
                            <th style="width: 140px;">Check In</th>
                            <th style="width: 140px;">Check Out</th>
                            <th style="width: 80px;">Nights</th>
                            <th style="width: 80px;">Guests</th>
                            <th style="width: 120px;">Total</th>
                            <th style="width: 120px;">Status</th>
                            <th style="width: 120px;">Payment</th>
                            <th style="width: 400px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $booking): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($booking['booking_reference']); ?></strong></td>
                                <td>
                                    <?php echo htmlspecialchars($booking['guest_name']); ?>
                                    <br><small style="color: #666;"><?php echo htmlspecialchars($booking['guest_phone']); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($booking['room_name']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($booking['check_in_date'])); ?></td>
                                <td><?php echo date('M d, Y', strtotime($booking['check_out_date'])); ?></td>
                                <td><?php echo $booking['number_of_nights']; ?></td>
                                <td><?php echo $booking['number_of_guests']; ?></td>
                                <td><strong>K <?php echo number_format($booking['total_amount'], 0); ?></strong></td>
                                <td>
                                    <span class="badge badge-<?php echo $booking['status']; ?>">
                                        <?php echo ucfirst($booking['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-<?php echo $booking['payment_status']; ?>">
                                        <?php echo ucfirst($booking['payment_status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($booking['status'] === 'pending'): ?>
                                        <button class="quick-action confirm" onclick="updateStatus(<?php echo $booking['id']; ?>, 'confirmed')">
                                            <i class="fas fa-check"></i> Confirm
                                        </button>
                                    <?php endif; ?>
                                    <?php if ($booking['status'] === 'confirmed'): ?>
                                        <?php $can_checkin = ($booking['payment_status'] === 'paid'); ?>
                                        <button class="quick-action check-in <?php echo $can_checkin ? '' : 'disabled'; ?>"
                                                onclick="<?php echo $can_checkin ? "updateStatus({$booking['id']}, 'checked-in')" : "alert('Cannot check in: booking must be PAID first.')"; ?>">
                                            <i class="fas fa-sign-in-alt"></i> Check In
                                        </button>
                                    <?php endif; ?>
                                    <?php if ($booking['status'] === 'checked-in'): ?>
                                        <button class="quick-action undo-checkin" onclick="updateStatus(<?php echo $booking['id']; ?>, 'cancel-checkin')">
                                            <i class="fas fa-undo"></i> Cancel Check-in
                                        </button>
                                    <?php endif; ?>
                                    <?php if ($booking['payment_status'] !== 'paid'): ?>
                                        <button class="quick-action paid" onclick="updatePayment(<?php echo $booking['id']; ?>, 'paid')">
                                            <i class="fas fa-dollar-sign"></i> Paid
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-calendar-times"></i>
                    <p>No room bookings yet.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Conference Inquiries -->
        <div class="bookings-section">
            <h3 class="section-title">
                <i class="fas fa-users"></i> Conference Inquiries
                <span style="font-size: 14px; font-weight: normal; color: #666;">
                    (<?php echo count($conference_inquiries); ?> total)
                </span>
            </h3>

            <?php if (!empty($conference_inquiries)): ?>
                <table class="booking-table">
                    <thead>
                        <tr>
                            <th style="width: 140px;">Date Received</th>
                            <th style="width: 220px;">Company/Name</th>
                            <th style="width: 220px;">Contact</th>
                            <th style="width: 180px;">Event Type</th>
                            <th style="width: 140px;">Expected Date</th>
                            <th style="width: 100px;">Attendees</th>
                            <th style="width: 140px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($conference_inquiries as $inquiry): ?>
                            <tr>
                                <td><?php echo date('M d, Y', strtotime($inquiry['created_at'])); ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($inquiry['company_name']); ?></strong>
                                    <br><small><?php echo htmlspecialchars($inquiry['contact_person']); ?></small>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($inquiry['email']); ?>
                                    <br><small style="color: #666;"><?php echo htmlspecialchars($inquiry['phone']); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($inquiry['event_type']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($inquiry['expected_date'])); ?></td>
                                <td><?php echo $inquiry['number_of_attendees']; ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $inquiry['status']; ?>">
                                        <?php echo ucfirst($inquiry['status']); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <p>No conference inquiries yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function updateStatus(id, status) {
            const formData = new FormData();
            formData.append('action', 'update_status');
            formData.append('id', id);
            formData.append('status', status);
            
            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (response.ok) {
                    window.location.reload();
                } else {
                    alert('Error updating status');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating status');
            });
        }

        function updatePayment(id, payment_status) {
            const formData = new FormData();
            formData.append('action', 'update_payment');
            formData.append('id', id);
            formData.append('payment_status', payment_status);
            
            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (response.ok) {
                    window.location.reload();
                } else {
                    alert('Error updating payment');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating payment');
            });
        }
    </script>
</body>
</html>
