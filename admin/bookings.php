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
            $stmt = $pdo->prepare("UPDATE bookings SET status = ? WHERE id = ?");
            $stmt->execute([$_POST['status'], $_POST['id']]);
            $message = 'Booking status updated!';

        } elseif ($action === 'update_payment') {
            $stmt = $pdo->prepare("UPDATE bookings SET payment_status = ? WHERE id = ?");
            $stmt->execute([$_POST['payment_status'], $_POST['id']]);
            $message = 'Payment status updated!';
        }

    } catch (PDOException $e) {
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
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
        }
        .admin-nav a:hover,
        .admin-nav a.active {
            color: var(--gold);
            border-bottom-color: var(--gold);
        }
        .content {
            padding: 32px;
            max-width: 1600px;
            margin: 0 auto;
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
        }
        .booking-table th {
            background: #f8f9fa;
            padding: 12px;
            text-align: left;
            font-size: 13px;
            font-weight: 600;
            color: #666;
            text-transform: uppercase;
            border-bottom: 2px solid #dee2e6;
        }
        .booking-table td {
            padding: 12px;
            border-bottom: 1px solid #f0f0f0;
            vertical-align: middle;
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
            padding: 4px 8px;
            border: none;
            border-radius: 4px;
            font-size: 11px;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-right: 4px;
        }
        .quick-action.confirm {
            background: #28a745;
            color: white;
        }
        .quick-action.check-in {
            background: #17a2b8;
            color: white;
        }
        .quick-action.paid {
            background: var(--gold);
            color: var(--deep-navy);
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
    <div class="admin-header">
        <h1><i class="fas fa-calendar-check"></i> All Bookings</h1>
        <div class="user-info">
            <div>
                <div><?php echo htmlspecialchars($user['full_name']); ?></div>
                <div style="font-size: 12px; opacity: 0.8;"><?php echo htmlspecialchars($user['role']); ?></div>
            </div>
            <a href="logout.php" class="btn-logout">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>

    <nav class="admin-nav">
        <ul>
            <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="bookings.php" class="active"><i class="fas fa-calendar-check"></i> Bookings</a></li>
            <li><a href="room-management.php"><i class="fas fa-bed"></i> Rooms</a></li>
            <li><a href="conference-management.php"><i class="fas fa-briefcase"></i> Conference Rooms</a></li>
            <li><a href="room-gallery-management.php"><i class="fas fa-images"></i> Room Gallery</a></li>
            <li><a href="menu-management.php"><i class="fas fa-utensils"></i> Menu</a></li>
            <li><a href="events-management.php"><i class="fas fa-calendar-alt"></i> Events</a></li>
            <li><a href="../index.php" target="_blank"><i class="fas fa-external-link-alt"></i> View Website</a></li>
        </ul>
    </nav>

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
                            <th>Ref</th>
                            <th>Guest Name</th>
                            <th>Room</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Nights</th>
                            <th>Guests</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Payment</th>
                            <th>Actions</th>
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
                                        <button class="quick-action check-in" onclick="updateStatus(<?php echo $booking['id']; ?>, 'checked-in')">
                                            <i class="fas fa-sign-in-alt"></i> Check In
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
                            <th>Date Received</th>
                            <th>Company/Name</th>
                            <th>Contact</th>
                            <th>Event Type</th>
                            <th>Expected Date</th>
                            <th>Attendees</th>
                            <th>Status</th>
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
