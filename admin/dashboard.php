<?php
session_start();

// Check authentication
if (!isset($_SESSION['admin_user'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';

$user = $_SESSION['admin_user'];
$today = date('Y-m-d');

// Fetch dashboard statistics
try {
    // Today's check-ins
    $checkins_stmt = $pdo->prepare("SELECT COUNT(*) as count FROM bookings WHERE check_in_date = ? AND status IN ('confirmed', 'pending')");
    $checkins_stmt->execute([$today]);
    $today_checkins = $checkins_stmt->fetch(PDO::FETCH_ASSOC)['count'];

    // Today's check-outs
    $checkouts_stmt = $pdo->prepare("SELECT COUNT(*) as count FROM bookings WHERE check_out_date = ? AND status = 'checked-in'");
    $checkouts_stmt->execute([$today]);
    $today_checkouts = $checkouts_stmt->fetch(PDO::FETCH_ASSOC)['count'];

    // Pending bookings
    $pending_stmt = $pdo->query("SELECT COUNT(*) as count FROM bookings WHERE status = 'pending'");
    $pending_bookings = $pending_stmt->fetch(PDO::FETCH_ASSOC)['count'];

    // Currently checked in
    $current_stmt = $pdo->query("SELECT COUNT(*) as count FROM bookings WHERE status = 'checked-in'");
    $current_guests = $current_stmt->fetch(PDO::FETCH_ASSOC)['count'];

    // Recent bookings (last 10)
    $recent_stmt = $pdo->query("
        SELECT b.*, r.name as room_name 
        FROM bookings b
        JOIN rooms r ON b.room_id = r.id
        ORDER BY b.created_at DESC
        LIMIT 10
    ");
    $recent_bookings = $recent_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Upcoming check-ins (next 7 days)
    $upcoming_stmt = $pdo->prepare("
        SELECT b.*, r.name as room_name 
        FROM bookings b
        JOIN rooms r ON b.room_id = r.id
        WHERE b.check_in_date BETWEEN ? AND DATE_ADD(?, INTERVAL 7 DAY)
        AND b.status IN ('pending', 'confirmed')
        ORDER BY b.check_in_date ASC
    ");
    $upcoming_stmt->execute([$today, $today]);
    $upcoming_checkins = $upcoming_stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error = "Unable to load dashboard data.";
}

$site_name = getSetting('site_name', 'Liwonde Sun Hotel');
$currency_symbol = getSetting('currency_symbol', 'K');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | <?php echo htmlspecialchars($site_name); ?> Admin</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --gold: #D4AF37;
            --navy: #0A1929;
            --deep-navy: #050D14;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f7fa;
        }
        .admin-header {
            background: linear-gradient(135deg, var(--deep-navy) 0%, var(--navy) 100%);
            color: white;
            padding: 16px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
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
        }
        .admin-nav a:hover,
        .admin-nav a.active {
            color: var(--gold);
            border-bottom-color: var(--gold);
        }
        .dashboard-content {
            padding: 32px;
            max-width: 1400px;
            margin: 0 auto;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 24px;
            margin-bottom: 40px;
        }
        .stat-card {
            background: white;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border-left: 4px solid var(--gold);
        }
        .stat-card .stat-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--gold) 0%, #c49b2e 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
        }
        .stat-card .stat-icon i {
            font-size: 24px;
            color: white;
        }
        .stat-card .stat-value {
            font-size: 36px;
            font-weight: 700;
            color: var(--navy);
            margin-bottom: 4px;
        }
        .stat-card .stat-label {
            font-size: 13px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            color: var(--navy);
            margin-bottom: 20px;
        }
        .bookings-table {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table thead {
            background: var(--navy);
            color: white;
        }
        .table th {
            padding: 16px;
            text-align: left;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .table td {
            padding: 16px;
            border-bottom: 1px solid #f0f0f0;
        }
        .table tbody tr:hover {
            background: #f8f9fa;
        }
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .badge-pending {
            background: #fff3cd;
            color: #856404;
        }
        .badge-confirmed {
            background: #d1ecf1;
            color: #0c5460;
        }
        .badge-checked-in {
            background: #d4edda;
            color: #155724;
        }
        .badge-checked-out {
            background: #e2e3e5;
            color: #383d41;
        }
        .badge-cancelled {
            background: #f8d7da;
            color: #721c24;
        }
        .btn {
            padding: 6px 16px;
            border: none;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        .btn-sm {
            padding: 4px 12px;
            font-size: 11px;
        }
        .btn-primary {
            background: var(--gold);
            color: var(--deep-navy);
        }
        .btn-primary:hover {
            background: #c49b2e;
        }
        .btn-success {
            background: #28a745;
            color: white;
        }
        .btn-success:hover {
            background: #218838;
        }
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        .btn-danger:hover {
            background: #c82333;
        }
        .quick-actions {
            display: flex;
            gap: 8px;
        }
        @media (max-width: 768px) {
            .dashboard-content {
                padding: 20px;
            }
            .admin-nav ul {
                overflow-x: auto;
            }
            .stats-grid {
                grid-template-columns: 1fr;
            }
            .table {
                font-size: 13px;
            }
            .table th, .table td {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <h1><i class="fas fa-hotel"></i> <?php echo htmlspecialchars($site_name); ?></h1>
        <div class="user-info">
            <div>
                <div class="user-name"><?php echo htmlspecialchars($user['full_name']); ?></div>
                <div class="user-role"><?php echo htmlspecialchars($user['role']); ?></div>
            </div>
            <a href="logout.php" class="btn-logout">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>

<?php $current_page = basename($_SERVER['PHP_SELF']); ?>
    <nav class="admin-nav">
        <ul>
            <li><a href="dashboard.php" class="<?php echo $current_page === 'dashboard.php' ? 'active' : ''; ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="bookings.php" class="<?php echo $current_page === 'bookings.php' ? 'active' : ''; ?>"><i class="fas fa-calendar-check"></i> Bookings</a></li>
            <li><a href="room-management.php" class="<?php echo $current_page === 'room-management.php' ? 'active' : ''; ?>"><i class="fas fa-bed"></i> Rooms</a></li>
            <li><a href="conference-management.php" class="<?php echo $current_page === 'conference-management.php' ? 'active' : ''; ?>"><i class="fas fa-briefcase"></i> Conference Rooms</a></li>
            <li><a href="menu-management.php" class="<?php echo $current_page === 'menu-management.php' ? 'active' : ''; ?>"><i class="fas fa-utensils"></i> Menu</a></li>
            <li><a href="events-management.php" class="<?php echo $current_page === 'events-management.php' ? 'active' : ''; ?>"><i class="fas fa-calendar-alt"></i> Events</a></li>
            <li><a href="../index.php" target="_blank"><i class="fas fa-external-link-alt"></i> View Website</a></li>
        </ul>
    </nav>

    <div class="dashboard-content">
        <h2 class="section-title">Dashboard Overview</h2>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-value"><?php echo $today_checkins; ?></div>
                <div class="stat-label">Today's Check-ins</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-calendar-times"></i>
                </div>
                <div class="stat-value"><?php echo $today_checkouts; ?></div>
                <div class="stat-label">Today's Check-outs</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-value"><?php echo $pending_bookings; ?></div>
                <div class="stat-label">Pending Bookings</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-value"><?php echo $current_guests; ?></div>
                <div class="stat-label">Current Guests</div>
            </div>
        </div>

        <!-- Today's Check-ins Management -->
        <div style="background: white; padding: 24px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); margin-bottom: 32px;">
            <h3 style="margin-bottom: 20px; color: var(--navy); font-size: 20px; font-weight: 700;">
                <i class="fas fa-door-open"></i> Today's Check-ins (<?php echo $today_checkins; ?>)
            </h3>
            
            <?php
            $today_checkin_list = $pdo->prepare("
                SELECT b.*, r.name as room_name 
                FROM bookings b
                JOIN rooms r ON b.room_id = r.id
                WHERE b.check_in_date = ? 
                AND b.status IN ('confirmed', 'pending')
                ORDER BY b.created_at ASC
            ");
            $today_checkin_list->execute([$today]);
            $checkin_bookings = $today_checkin_list->fetchAll(PDO::FETCH_ASSOC);
            ?>
            
            <?php if (!empty($checkin_bookings)): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Booking Ref</th>
                            <th>Guest Name</th>
                            <th>Room</th>
                            <th>Check-out</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($checkin_bookings as $booking): ?>
                            <tr id="checkin-row-<?php echo $booking['id']; ?>">
                                <td><strong><?php echo htmlspecialchars($booking['booking_reference']); ?></strong></td>
                                <td><?php echo htmlspecialchars($booking['guest_name']); ?></td>
                                <td><?php echo htmlspecialchars($booking['room_name']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($booking['check_out_date'])); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $booking['status']; ?>" id="status-<?php echo $booking['id']; ?>">
                                        <?php echo ucfirst($booking['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($booking['status'] !== 'checked-in'): ?>
                                        <button onclick="processCheckIn(<?php echo $booking['id']; ?>, '<?php echo htmlspecialchars(addslashes($booking['guest_name'])); ?>')" 
                                                id="checkin-btn-<?php echo $booking['id']; ?>"
                                                style="background: var(--gold); color: var(--deep-navy); border: none; padding: 6px 14px; border-radius: 6px; cursor: pointer; font-size: 12px; font-weight: 600;">
                                            <i class="fas fa-check"></i> Check In
                                        </button>
                                    <?php else: ?>
                                        <span style="color: #28a745; font-weight: 600;"><i class="fas fa-check-circle"></i> Checked In</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div style="text-align: center; padding: 40px; color: #999;">
                    <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 12px; color: #ddd;"></i>
                    <p>No check-ins scheduled for today</p>
                </div>
            <?php endif; ?>
        </div>

        <h3 class="section-title">Upcoming Check-ins (Next 7 Days)</h3>
        <div class="bookings-table">
            <table class="table">
                <thead>
                    <tr>
                        <th>Booking Ref</th>
                        <th>Guest Name</th>
                        <th>Room</th>
                        <th>Check-in</th>
                        <th>Nights</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($upcoming_checkins)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px; color: #999;">
                            No upcoming check-ins in the next 7 days
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($upcoming_checkins as $booking): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($booking['booking_reference']); ?></strong></td>
                        <td><?php echo htmlspecialchars($booking['guest_name']); ?></td>
                        <td><?php echo htmlspecialchars($booking['room_name']); ?></td>
                        <td><?php echo date('M j, Y', strtotime($booking['check_in_date'])); ?></td>
                        <td><?php echo $booking['number_of_nights']; ?></td>
                        <td>
                            <span class="badge badge-<?php echo $booking['status']; ?>">
                                <?php echo ucfirst($booking['status']); ?>
                            </span>
                        </td>
                        <td>
                            <div class="quick-actions">
                                <?php if ($booking['status'] == 'pending'): ?>
                                <a href="booking-action.php?id=<?php echo $booking['id']; ?>&action=confirm" class="btn btn-success btn-sm">Confirm</a>
                                <?php elseif ($booking['status'] == 'confirmed'): ?>
                                <a href="booking-action.php?id=<?php echo $booking['id']; ?>&action=checkin" class="btn btn-primary btn-sm">Check In</a>
                                <?php endif; ?>
                                <a href="booking-details.php?id=<?php echo $booking['id']; ?>" class="btn btn-primary btn-sm">View</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <h3 class="section-title" style="margin-top: 40px;">Recent Bookings</h3>
        <div class="bookings-table">
            <table class="table">
                <thead>
                    <tr>
                        <th>Booking Ref</th>
                        <th>Guest Name</th>
                        <th>Room</th>
                        <th>Dates</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_bookings as $booking): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($booking['booking_reference']); ?></strong></td>
                        <td><?php echo htmlspecialchars($booking['guest_name']); ?></td>
                        <td><?php echo htmlspecialchars($booking['room_name']); ?></td>
                        <td>
                            <?php echo date('M j', strtotime($booking['check_in_date'])); ?> - 
                            <?php echo date('M j, Y', strtotime($booking['check_out_date'])); ?>
                        </td>
                        <td><?php echo $currency_symbol; ?><?php echo number_format($booking['total_amount'], 0); ?></td>
                        <td>
                            <span class="badge badge-<?php echo $booking['status']; ?>">
                                <?php echo ucfirst($booking['status']); ?>
                            </span>
                        </td>
                        <td>
                            <a href="booking-details.php?id=<?php echo $booking['id']; ?>" class="btn btn-primary btn-sm">View</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function processCheckIn(bookingId, guestName) {
            if (!confirm(`Check in ${guestName}?`)) {
                return;
            }

            const formData = new FormData();
            formData.append('action', 'checkin');
            formData.append('booking_id', bookingId);

            fetch('process-checkin.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update UI
                    const statusBadge = document.getElementById(`status-${bookingId}`);
                    statusBadge.className = 'badge badge-checked-in';
                    statusBadge.textContent = 'Checked-in';
                    
                    const button = document.getElementById(`checkin-btn-${bookingId}`);
                    button.outerHTML = '<span style="color: #28a745; font-weight: 600;"><i class="fas fa-check-circle"></i> Checked In</span>';
                    
                    alert(`${guestName} successfully checked in!`);
                } else {
                    alert('Error: ' + (data.message || 'Failed to check in guest'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred during check-in');
            });
        }
    </script>
</body>
</html>
