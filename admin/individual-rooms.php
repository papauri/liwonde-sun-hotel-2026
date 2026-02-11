<?php
/**
 * Individual Rooms Management - Admin Panel
 * Manage individual rooms (specific rooms like "Executive 101", "VVIP Suite")
 */
require_once 'admin-init.php';

$message = '';
$error = '';

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $action = $_POST['action'] ?? '';
        
        if ($action === 'add_individual_room') {
            $room_type_id = (int)$_POST['room_type_id'];
            $room_number = trim($_POST['room_number']);
            $room_name = trim($_POST['room_name'] ?? '');
            $floor = trim($_POST['floor'] ?? '');
            $status = $_POST['status'] ?? 'available';
            $notes = trim($_POST['notes'] ?? '');
            $display_order = (int)($_POST['display_order'] ?? 0);
            
            // Validate
            if (empty($room_type_id) || empty($room_number)) {
                $error = 'Room type and room number are required.';
            } else {
                // Check if room number already exists
                $check = $pdo->prepare("SELECT COUNT(*) FROM individual_rooms WHERE room_number = ?");
                $check->execute([$room_number]);
                if ($check->fetchColumn() > 0) {
                    $error = 'Room number already exists. Please use a unique room number.';
                } else {
                    // Insert new individual room
                    $stmt = $pdo->prepare("
                        INSERT INTO individual_rooms (room_type_id, room_number, room_name, floor, status, notes, display_order)
                        VALUES (?, ?, ?, ?, ?, ?, ?)
                    ");
                    $stmt->execute([$room_type_id, $room_number, $room_name, $floor, $status, $notes, $display_order]);
                    
                    // Log the creation
                    $room_id = $pdo->lastInsertId();
                    $logStmt = $pdo->prepare("
                        INSERT INTO room_maintenance_log (individual_room_id, status_from, status_to, performed_by)
                        VALUES (?, NULL, ?, ?)
                    ");
                    $logStmt->execute([$room_id, $status, $user['id'] ?? null]);
                    
                    $message = 'Individual room added successfully!';
                }
            }
            
        } elseif ($action === 'update_individual_room') {
            $id = (int)$_POST['id'];
            $room_type_id = (int)$_POST['room_type_id'];
            $room_number = trim($_POST['room_number']);
            $room_name = trim($_POST['room_name'] ?? '');
            $floor = trim($_POST['floor'] ?? '');
            $notes = trim($_POST['notes'] ?? '');
            $is_active = isset($_POST['is_active']) ? 1 : 0;
            $display_order = (int)($_POST['display_order'] ?? 0);
            
            // Validate
            if (empty($room_type_id) || empty($room_number)) {
                $error = 'Room type and room number are required.';
            } else {
                // Check if room number already exists (excluding current room)
                $check = $pdo->prepare("SELECT COUNT(*) FROM individual_rooms WHERE room_number = ? AND id != ?");
                $check->execute([$room_number, $id]);
                if ($check->fetchColumn() > 0) {
                    $error = 'Room number already exists. Please use a unique room number.';
                } else {
                    $stmt = $pdo->prepare("
                        UPDATE individual_rooms 
                        SET room_type_id = ?, room_number = ?, room_name = ?, floor = ?, notes = ?, is_active = ?, display_order = ?
                        WHERE id = ?
                    ");
                    $stmt->execute([$room_type_id, $room_number, $room_name, $floor, $notes, $is_active, $display_order, $id]);
                    $message = 'Individual room updated successfully!';
                }
            }
            
        } elseif ($action === 'update_status') {
            $id = (int)$_POST['id'];
            $new_status = $_POST['new_status'];
            $reason = trim($_POST['reason'] ?? '');
            
            $validStatuses = ['available', 'occupied', 'maintenance', 'cleaning', 'out_of_order'];
            if (!in_array($new_status, $validStatuses)) {
                $error = 'Invalid status.';
            } else {
                // Get current status
                $currentStmt = $pdo->prepare("SELECT status FROM individual_rooms WHERE id = ?");
                $currentStmt->execute([$id]);
                $current = $currentStmt->fetch(PDO::FETCH_ASSOC);
                
                if ($current) {
                    $old_status = $current['status'];
                    
                    // Update status
                    $stmt = $pdo->prepare("UPDATE individual_rooms SET status = ? WHERE id = ?");
                    $stmt->execute([$new_status, $id]);
                    
                    // Log the change
                    $logStmt = $pdo->prepare("
                        INSERT INTO room_maintenance_log (individual_room_id, status_from, status_to, reason, performed_by)
                        VALUES (?, ?, ?, ?, ?)
                    ");
                    $logStmt->execute([$id, $old_status, $new_status, $reason, $user['id'] ?? null]);
                    
                    $message = 'Room status updated successfully!';
                } else {
                    $error = 'Room not found.';
                }
            }
            
        } elseif ($action === 'delete_individual_room') {
            $id = (int)$_POST['id'];
            
            // Check for active bookings
            $bookingsCheck = $pdo->prepare("
                SELECT COUNT(*) FROM bookings 
                WHERE individual_room_id = ? AND status IN ('pending', 'confirmed', 'checked-in') AND check_out_date >= CURDATE()
            ");
            $bookingsCheck->execute([$id]);
            if ($bookingsCheck->fetchColumn() > 0) {
                $error = 'Cannot delete room with active bookings.';
            } else {
                $stmt = $pdo->prepare("DELETE FROM individual_rooms WHERE id = ?");
                $stmt->execute([$id]);
                $message = 'Individual room deleted successfully!';
            }
            
        } elseif ($action === 'bulk_status_change') {
            $room_ids = $_POST['room_ids'] ?? [];
            $new_status = $_POST['bulk_status'] ?? '';
            
            if (empty($room_ids) || empty($new_status)) {
                $error = 'Please select rooms and a status.';
            } else {
                $validStatuses = ['available', 'occupied', 'maintenance', 'cleaning', 'out_of_order'];
                if (!in_array($new_status, $validStatuses)) {
                    $error = 'Invalid status.';
                } else {
                    foreach ($room_ids as $room_id) {
                        $room_id = (int)$room_id;
                        
                        // Get current status
                        $currentStmt = $pdo->prepare("SELECT status FROM individual_rooms WHERE id = ?");
                        $currentStmt->execute([$room_id]);
                        $current = $currentStmt->fetch(PDO::FETCH_ASSOC);
                        
                        if ($current) {
                            // Update status
                            $stmt = $pdo->prepare("UPDATE individual_rooms SET status = ? WHERE id = ?");
                            $stmt->execute([$new_status, $room_id]);
                            
                            // Log the change
                            $logStmt = $pdo->prepare("
                                INSERT INTO room_maintenance_log (individual_room_id, status_from, status_to, reason, performed_by)
                                VALUES (?, ?, ?, 'Bulk status change', ?)
                            ");
                            $logStmt->execute([$room_id, $current['status'], $new_status, $user['id'] ?? null]);
                        }
                    }
                    $message = count($room_ids) . ' rooms updated successfully!';
                }
            }
        }
        
    } catch (PDOException $e) {
        $error = 'Database error: ' . $e->getMessage();
    }
}

// Get filter parameters
$filter_room_type = isset($_GET['room_type']) ? (int)$_GET['room_type'] : null;
$filter_status = $_GET['status'] ?? null;
$filter_floor = $_GET['floor'] ?? null;

// Build query for individual rooms
$whereClauses = ['1=1'];
$params = [];

if ($filter_room_type) {
    $whereClauses[] = 'ir.room_type_id = ?';
    $params[] = $filter_room_type;
}
if ($filter_status) {
    $whereClauses[] = 'ir.status = ?';
    $params[] = $filter_status;
}
if ($filter_floor) {
    $whereClauses[] = 'ir.floor = ?';
    $params[] = $filter_floor;
}

$stmt = $pdo->prepare("
    SELECT 
        ir.*,
        r.name as room_type_name,
        r.price_per_night,
        b.booking_reference as current_booking_ref,
        b.guest_name as current_guest,
        b.check_in_date as current_checkin,
        b.check_out_date as current_checkout
    FROM individual_rooms ir
    LEFT JOIN rooms r ON ir.room_type_id = r.id
    LEFT JOIN bookings b ON ir.id = b.individual_room_id 
        AND b.status IN ('confirmed', 'checked-in') 
        AND b.check_out_date >= CURDATE()
    WHERE " . implode(' AND ', $whereClauses) . "
    ORDER BY r.name ASC, ir.floor ASC, ir.room_number ASC
");
$stmt->execute($params);
$individualRooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get room types for dropdown
$roomTypesStmt = $pdo->query("SELECT id, name FROM rooms WHERE is_active = 1 ORDER BY name");
$roomTypes = $roomTypesStmt->fetchAll(PDO::FETCH_ASSOC);

// Get unique floors for filter
$floorsStmt = $pdo->query("SELECT DISTINCT floor FROM individual_rooms WHERE floor IS NOT NULL AND floor != '' ORDER BY floor");
$floors = $floorsStmt->fetchAll(PDO::FETCH_COLUMN);

// Get status summary
$summaryStmt = $pdo->query("
    SELECT 
        status,
        COUNT(*) as count
    FROM individual_rooms
    WHERE is_active = 1
    GROUP BY status
");
$statusSummary = [];
foreach ($summaryStmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $statusSummary[$row['status']] = $row['count'];
}

$currency = htmlspecialchars(getSetting('currency_symbol', 'MWK'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Individual Rooms Management - Admin Panel</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;1,300;1,400;1,500&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/theme-dynamic.php">
    <link rel="stylesheet" href="css/admin-styles.css">
    <link rel="stylesheet" href="css/admin-components.css">
    
    <style>
        .status-summary {
            display: flex;
            gap: 16px;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }
        .status-card {
            background: white;
            border-radius: 10px;
            padding: 16px 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 140px;
        }
        .status-card .icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }
        .status-available .icon { background: #d4edda; color: #155724; }
        .status-occupied .icon { background: #fff3cd; color: #856404; }
        .status-cleaning .icon { background: #cce5ff; color: #004085; }
        .status-maintenance .icon { background: #f8d7da; color: #721c24; }
        .status-out_of_order .icon { background: #e2e3e5; color: #383d41; }
        .status-card .count {
            font-size: 24px;
            font-weight: 700;
            color: var(--deep-navy, #1A1A1A);
        }
        .status-card .label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .filters-bar {
            background: white;
            border-radius: 10px;
            padding: 16px 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            align-items: center;
        }
        .filters-bar select {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
        }
        
        .rooms-table {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        .rooms-table table {
            width: 100%;
            border-collapse: collapse;
        }
        .rooms-table th {
            background: #f8f9fa;
            padding: 14px 16px;
            text-align: left;
            font-size: 12px;
            font-weight: 600;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #eee;
        }
        .rooms-table td {
            padding: 14px 16px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }
        .rooms-table tr:hover {
            background: #f8f9fa;
        }
        .room-number {
            font-weight: 700;
            color: var(--deep-navy, #1A1A1A);
        }
        .room-name {
            font-size: 13px;
            color: #666;
        }
        
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .status-available { background: #d4edda; color: #155724; }
        .status-occupied { background: #fff3cd; color: #856404; }
        .status-cleaning { background: #cce5ff; color: #004085; }
        .status-maintenance { background: #f8d7da; color: #721c24; }
        .status-out_of_order { background: #e2e3e5; color: #383d41; }
        
        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: all 0.2s;
        }
        .btn-primary { background: var(--gold, #8B7355); color: var(--deep-navy, #1A1A1A); }
        .btn-primary:hover { background: #6B5740; }
        .btn-secondary { background: #6c757d; color: white; }
        .btn-secondary:hover { background: #5a6268; }
        .btn-info { background: #17a2b8; color: white; }
        .btn-info:hover { background: #138496; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-danger:hover { background: #c82333; }
        .btn-success { background: #28a745; color: white; }
        .btn-success:hover { background: #218838; }
        
        .actions-cell {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }
        
        .current-booking {
            font-size: 12px;
            background: #fff3cd;
            padding: 6px 10px;
            border-radius: 6px;
            margin-top: 4px;
        }
        .current-booking a {
            color: #856404;
            font-weight: 600;
        }
        
        /* Modal styles */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 9999;
            justify-content: center;
            align-items: flex-start;
            padding: 40px 20px;
            overflow-y: auto;
        }
        .modal-overlay.active {
            display: flex;
        }
        .modal-content {
            background: white;
            border-radius: 12px;
            width: 100%;
            max-width: 600px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            animation: modalIn 0.2s ease-out;
        }
        @keyframes modalIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 24px;
            border-bottom: 1px solid #eee;
        }
        .modal-header h3 {
            margin: 0;
            font-size: 18px;
            color: var(--deep-navy, #1A1A1A);
        }
        .modal-close {
            background: none;
            border: none;
            font-size: 28px;
            cursor: pointer;
            color: #999;
            line-height: 1;
        }
        .modal-close:hover { color: #333; }
        .modal-body {
            padding: 24px;
            max-height: 70vh;
            overflow-y: auto;
        }
        .form-group {
            margin-bottom: 16px;
        }
        .form-group label {
            display: block;
            font-weight: 600;
            font-size: 13px;
            margin-bottom: 6px;
            color: #333;
        }
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            font-family: inherit;
            box-sizing: border-box;
        }
        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            border-color: var(--gold, #8B7355);
            outline: none;
            box-shadow: 0 0 0 3px rgba(139, 115, 85,0.15);
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        .form-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            padding: 16px 24px;
            border-top: 1px solid #eee;
            background: #f8f9fa;
            border-radius: 0 0 12px 12px;
        }
        
        .checkbox-wrapper {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .checkbox-wrapper input[type="checkbox"] {
            width: 18px;
            height: 18px;
        }
        
        .bulk-actions {
            background: #f8f9fa;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 16px;
            display: flex;
            gap: 12px;
            align-items: center;
        }
        .bulk-actions.hidden {
            display: none;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .page-header h2 {
            margin: 0;
            font-family: 'Cormorant Garamond', Georgia, serif;
            color: var(--navy, #1A1A1A);
        }
        
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            .rooms-table {
                overflow-x: auto;
            }
            .rooms-table table {
                min-width: 800px;
            }
        }
    </style>
</head>
<body>
    <?php require_once 'includes/admin-header.php'; ?>

    <div class="content">
        <div class="page-header">
            <h2><i class="fas fa-door-open"></i> Individual Rooms Management</h2>
            <button class="btn btn-primary" onclick="openAddModal()">
                <i class="fas fa-plus"></i> Add Individual Room
            </button>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-success" style="background: #d4edda; color: #155724; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px;">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger" style="background: #f8d7da; color: #721c24; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px;">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <!-- Status Summary -->
        <div class="status-summary">
            <div class="status-card status-available">
                <div class="icon"><i class="fas fa-check"></i></div>
                <div>
                    <div class="count"><?php echo $statusSummary['available'] ?? 0; ?></div>
                    <div class="label">Available</div>
                </div>
            </div>
            <div class="status-card status-occupied">
                <div class="icon"><i class="fas fa-user"></i></div>
                <div>
                    <div class="count"><?php echo $statusSummary['occupied'] ?? 0; ?></div>
                    <div class="label">Occupied</div>
                </div>
            </div>
            <div class="status-card status-cleaning">
                <div class="icon"><i class="fas fa-broom"></i></div>
                <div>
                    <div class="count"><?php echo $statusSummary['cleaning'] ?? 0; ?></div>
                    <div class="label">Cleaning</div>
                </div>
            </div>
            <div class="status-card status-maintenance">
                <div class="icon"><i class="fas fa-tools"></i></div>
                <div>
                    <div class="count"><?php echo $statusSummary['maintenance'] ?? 0; ?></div>
                    <div class="label">Maintenance</div>
                </div>
            </div>
            <div class="status-card status-out_of_order">
                <div class="icon"><i class="fas fa-ban"></i></div>
                <div>
                    <div class="count"><?php echo $statusSummary['out_of_order'] ?? 0; ?></div>
                    <div class="label">Out of Order</div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="filters-bar">
            <form method="GET" style="display: flex; gap: 16px; flex-wrap: wrap; align-items: center;">
                <select name="room_type" onchange="this.form.submit()">
                    <option value="">All Room Types</option>
                    <?php foreach ($roomTypes as $type): ?>
                        <option value="<?php echo $type['id']; ?>" <?php echo $filter_room_type == $type['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($type['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <select name="status" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="available" <?php echo $filter_status === 'available' ? 'selected' : ''; ?>>Available</option>
                    <option value="occupied" <?php echo $filter_status === 'occupied' ? 'selected' : ''; ?>>Occupied</option>
                    <option value="cleaning" <?php echo $filter_status === 'cleaning' ? 'selected' : ''; ?>>Cleaning</option>
                    <option value="maintenance" <?php echo $filter_status === 'maintenance' ? 'selected' : ''; ?>>Maintenance</option>
                    <option value="out_of_order" <?php echo $filter_status === 'out_of_order' ? 'selected' : ''; ?>>Out of Order</option>
                </select>
                <?php if (!empty($floors)): ?>
                <select name="floor" onchange="this.form.submit()">
                    <option value="">All Floors</option>
                    <?php foreach ($floors as $floor): ?>
                        <option value="<?php echo htmlspecialchars($floor); ?>" <?php echo $filter_floor === $floor ? 'selected' : ''; ?>>
                            Floor <?php echo htmlspecialchars($floor); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php endif; ?>
                <a href="?" class="btn btn-secondary btn-sm"><i class="fas fa-times"></i> Clear</a>
            </form>
        </div>

        <!-- Bulk Actions -->
        <div class="bulk-actions hidden" id="bulkActions">
            <span id="selectedCount">0 selected</span>
            <select id="bulkStatus">
                <option value="">Change status to...</option>
                <option value="available">Available</option>
                <option value="occupied">Occupied</option>
                <option value="cleaning">Cleaning</option>
                <option value="maintenance">Maintenance</option>
                <option value="out_of_order">Out of Order</option>
            </select>
            <button class="btn btn-primary btn-sm" onclick="applyBulkStatus()">
                <i class="fas fa-check"></i> Apply
            </button>
        </div>

        <!-- Rooms Table -->
        <div class="rooms-table">
            <form method="POST" id="bulkForm">
                <input type="hidden" name="action" value="bulk_status_change">
                <table>
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="selectAll" onchange="toggleSelectAll()"></th>
                            <th>Room</th>
                            <th>Type</th>
                            <th>Floor</th>
                            <th>Status</th>
                            <th>Current Booking</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($individualRooms)): ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 40px; color: #666;">
                                    <i class="fas fa-door-open" style="font-size: 48px; margin-bottom: 16px; display: block; color: #ddd;"></i>
                                    No individual rooms found. Click "Add Individual Room" to create one.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($individualRooms as $room): ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="room_ids[]" value="<?php echo $room['id']; ?>" onchange="updateBulkActions()">
                                    </td>
                                    <td>
                                        <div class="room-number"><?php echo htmlspecialchars($room['room_number']); ?></div>
                                        <?php if ($room['room_name']): ?>
                                            <div class="room-name"><?php echo htmlspecialchars($room['room_name']); ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($room['room_type_name'] ?? 'Unknown'); ?></td>
                                    <td><?php echo $room['floor'] ? htmlspecialchars($room['floor']) : '-'; ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $room['status']; ?>">
                                            <i class="fas fa-<?php 
                                                echo $room['status'] === 'available' ? 'check' : 
                                                    ($room['status'] === 'occupied' ? 'user' : 
                                                    ($room['status'] === 'cleaning' ? 'broom' : 
                                                    ($room['status'] === 'maintenance' ? 'tools' : 'ban'))); 
                                            ?>"></i>
                                            <?php echo ucfirst(str_replace('_', ' ', $room['status'])); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($room['current_booking_ref']): ?>
                                            <div class="current-booking">
                                                <a href="booking-details.php?id=<?php echo $room['current_booking_id'] ?? ''; ?>">
                                                    <?php echo htmlspecialchars($room['current_booking_ref']); ?>
                                                </a>
                                                <br>
                                                <small><?php echo htmlspecialchars($room['current_guest']); ?></small>
                                                <br>
                                                <small><?php echo $room['current_checkin']; ?> &rarr; <?php echo $room['current_checkout']; ?></small>
                                            </div>
                                        <?php else: ?>
                                            <span style="color: #999;">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="actions-cell">
                                            <button class="btn btn-info btn-sm" onclick="openEditModal(<?php echo htmlspecialchars(json_encode($room)); ?>)">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <button class="btn btn-success btn-sm" onclick="openStatusModal(<?php echo $room['id']; ?>, '<?php echo $room['status']; ?>', '<?php echo htmlspecialchars($room['room_number']); ?>')">
                                                <i class="fas fa-exchange-alt"></i> Status
                                            </button>
                                            <button class="btn btn-danger btn-sm" onclick="confirmDelete(<?php echo $room['id']; ?>, '<?php echo htmlspecialchars($room['room_number']); ?>')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </form>
        </div>
    </div>

    <!-- Add/Edit Modal -->
    <div class="modal-overlay" id="roomModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle"><i class="fas fa-plus"></i> Add Individual Room</h3>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <form method="POST" id="roomForm">
                <input type="hidden" name="action" id="formAction" value="add_individual_room">
                <input type="hidden" name="id" id="roomId">
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="room_type_id">Room Type *</label>
                            <select name="room_type_id" id="room_type_id" required>
                                <option value="">Select Room Type</option>
                                <?php foreach ($roomTypes as $type): ?>
                                    <option value="<?php echo $type['id']; ?>"><?php echo htmlspecialchars($type['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="room_number">Room Number *</label>
                            <input type="text" name="room_number" id="room_number" placeholder="e.g., EXEC-101" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="room_name">Room Name</label>
                            <input type="text" name="room_name" id="room_name" placeholder="e.g., Executive Room 1">
                        </div>
                        <div class="form-group">
                            <label for="floor">Floor</label>
                            <input type="text" name="floor" id="floor" placeholder="e.g., 1">
                        </div>
                    </div>
                    <div class="form-row" id="statusRow" style="display: none;">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="status">
                                <option value="available">Available</option>
                                <option value="occupied">Occupied</option>
                                <option value="cleaning">Cleaning</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="out_of_order">Out of Order</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="display_order">Display Order</label>
                            <input type="number" name="display_order" id="display_order" value="0" min="0">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea name="notes" id="notes" rows="3" placeholder="Any special notes about this room..."></textarea>
                    </div>
                    <div class="form-group" id="activeRow" style="display: none;">
                        <div class="checkbox-wrapper">
                            <input type="checkbox" name="is_active" id="is_active" checked>
                            <label for="is_active">Active (room is available for booking)</label>
                        </div>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Room</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Status Change Modal -->
    <div class="modal-overlay" id="statusModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-exchange-alt"></i> Change Room Status</h3>
                <button class="modal-close" onclick="closeStatusModal()">&times;</button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="update_status">
                <input type="hidden" name="id" id="statusRoomId">
                <div class="modal-body">
                    <p>Changing status for room: <strong id="statusRoomNumber"></strong></p>
                    <div class="form-group">
                        <label for="new_status">New Status</label>
                        <select name="new_status" id="new_status" required>
                            <option value="available">Available</option>
                            <option value="occupied">Occupied</option>
                            <option value="cleaning">Cleaning</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="out_of_order">Out of Order</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="reason">Reason (optional)</label>
                        <textarea name="reason" id="reason" rows="2" placeholder="Reason for status change..."></textarea>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeStatusModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-check"></i> Update Status</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Form -->
    <form method="POST" id="deleteForm" style="display: none;">
        <input type="hidden" name="action" value="delete_individual_room">
        <input type="hidden" name="id" id="deleteRoomId">
    </form>

    <script>
        // Add Modal
        function openAddModal() {
            document.getElementById('modalTitle').innerHTML = '<i class="fas fa-plus"></i> Add Individual Room';
            document.getElementById('formAction').value = 'add_individual_room';
            document.getElementById('roomForm').reset();
            document.getElementById('roomId').value = '';
            document.getElementById('statusRow').style.display = 'none';
            document.getElementById('activeRow').style.display = 'none';
            document.getElementById('roomModal').classList.add('active');
        }

        // Edit Modal
        function openEditModal(room) {
            document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit"></i> Edit Individual Room';
            document.getElementById('formAction').value = 'update_individual_room';
            document.getElementById('roomId').value = room.id;
            document.getElementById('room_type_id').value = room.room_type_id;
            document.getElementById('room_number').value = room.room_number;
            document.getElementById('room_name').value = room.room_name || '';
            document.getElementById('floor').value = room.floor || '';
            document.getElementById('status').value = room.status;
            document.getElementById('display_order').value = room.display_order || 0;
            document.getElementById('notes').value = room.notes || '';
            document.getElementById('is_active').checked = room.is_active == 1;
            document.getElementById('statusRow').style.display = 'grid';
            document.getElementById('activeRow').style.display = 'block';
            document.getElementById('roomModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('roomModal').classList.remove('active');
        }

        // Status Modal
        function openStatusModal(roomId, currentStatus, roomNumber) {
            document.getElementById('statusRoomId').value = roomId;
            document.getElementById('statusRoomNumber').textContent = roomNumber;
            document.getElementById('new_status').value = currentStatus;
            document.getElementById('statusModal').classList.add('active');
        }

        function closeStatusModal() {
            document.getElementById('statusModal').classList.remove('active');
        }

        // Delete
        function confirmDelete(roomId, roomNumber) {
            if (confirm('Are you sure you want to delete room "' + roomNumber + '"?\n\nThis action cannot be undone.')) {
                document.getElementById('deleteRoomId').value = roomId;
                document.getElementById('deleteForm').submit();
            }
        }

        // Bulk Actions
        function toggleSelectAll() {
            const checkboxes = document.querySelectorAll('input[name="room_ids[]"]');
            const selectAll = document.getElementById('selectAll').checked;
            checkboxes.forEach(cb => cb.checked = selectAll);
            updateBulkActions();
        }

        function updateBulkActions() {
            const checkboxes = document.querySelectorAll('input[name="room_ids[]"]:checked');
            const count = checkboxes.length;
            document.getElementById('selectedCount').textContent = count + ' selected';
            document.getElementById('bulkActions').classList.toggle('hidden', count === 0);
        }

        function applyBulkStatus() {
            const status = document.getElementById('bulkStatus').value;
            if (!status) {
                alert('Please select a status.');
                return;
            }
            const checkboxes = document.querySelectorAll('input[name="room_ids[]"]:checked');
            if (checkboxes.length === 0) {
                alert('Please select at least one room.');
                return;
            }
            if (confirm('Change status of ' + checkboxes.length + ' room(s) to "' + status + '"?')) {
                document.getElementById('bulkStatus').value = status;
                document.querySelector('#bulkForm input[name="bulk_status"]')?.remove();
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'bulk_status';
                input.value = status;
                document.getElementById('bulkForm').appendChild(input);
                document.getElementById('bulkForm').submit();
            }
        }

        // Close modals on outside click
        document.querySelectorAll('.modal-overlay').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.remove('active');
                }
            });
        });
    </script>

    <?php require_once 'includes/admin-footer.php'; ?>
</body>
</html>