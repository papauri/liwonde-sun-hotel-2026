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
$current_page = basename($_SERVER['PHP_SELF']);

// Simple helper to process uploaded event images
function uploadEventImage($fileInput)
{
    if (!$fileInput || !isset($fileInput['tmp_name']) || $fileInput['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $uploadDir = __DIR__ . '/../images/events/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $ext = pathinfo($fileInput['name'], PATHINFO_EXTENSION) ?: 'jpg';
    $filename = 'event_' . time() . '_' . random_int(1000, 9999) . '.' . strtolower($ext);
    $relativePath = 'images/events/' . $filename;
    $destination = $uploadDir . $filename;

    if (move_uploaded_file($fileInput['tmp_name'], $destination)) {
        return $relativePath;
    }

    return null;
}

// Handle event actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $action = $_POST['action'] ?? '';

        if ($action === 'add') {
            $imagePath = uploadEventImage($_FILES['image'] ?? null);

            $stmt = $pdo->prepare("
                INSERT INTO events (title, description, event_date, start_time, end_time, location, ticket_price, capacity, is_featured, is_active, display_order, image_path)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $_POST['title'],
                $_POST['description'],
                $_POST['event_date'],
                $_POST['start_time'],
                $_POST['end_time'],
                $_POST['location'],
                $_POST['ticket_price'] ?? 0,
                $_POST['capacity'],
                isset($_POST['is_featured']) ? 1 : 0,
                isset($_POST['is_active']) ? 1 : 0,
                $_POST['display_order'] ?? 0,
                $imagePath
            ]);
            $message = 'Event added successfully!';

        } elseif ($action === 'update') {
            $imagePath = uploadEventImage($_FILES['image'] ?? null);

            if ($imagePath) {
                $stmt = $pdo->prepare("
                    UPDATE events 
                    SET title = ?, description = ?, event_date = ?, start_time = ?, end_time = ?, 
                        location = ?, ticket_price = ?, capacity = ?, is_featured = ?, is_active = ?, display_order = ?, image_path = ?
                    WHERE id = ?
                ");
                $stmt->execute([
                    $_POST['title'],
                    $_POST['description'],
                    $_POST['event_date'],
                    $_POST['start_time'],
                    $_POST['end_time'],
                    $_POST['location'],
                    $_POST['ticket_price'] ?? 0,
                    $_POST['capacity'],
                    isset($_POST['is_featured']) ? 1 : 0,
                    isset($_POST['is_active']) ? 1 : 0,
                    $_POST['display_order'] ?? 0,
                    $imagePath,
                    $_POST['id']
                ]);
            } else {
                $stmt = $pdo->prepare("
                    UPDATE events 
                    SET title = ?, description = ?, event_date = ?, start_time = ?, end_time = ?, 
                        location = ?, ticket_price = ?, capacity = ?, is_featured = ?, is_active = ?, display_order = ?
                    WHERE id = ?
                ");
                $stmt->execute([
                    $_POST['title'],
                    $_POST['description'],
                    $_POST['event_date'],
                    $_POST['start_time'],
                    $_POST['end_time'],
                    $_POST['location'],
                    $_POST['ticket_price'] ?? 0,
                    $_POST['capacity'],
                    isset($_POST['is_featured']) ? 1 : 0,
                    isset($_POST['is_active']) ? 1 : 0,
                    $_POST['display_order'] ?? 0,
                    $_POST['id']
                ]);
            }
            $message = 'Event updated successfully!';

        } elseif ($action === 'delete') {
            $stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
            $stmt->execute([$_POST['id']]);
            $message = 'Event deleted successfully!';

        } elseif ($action === 'toggle_active') {
            $stmt = $pdo->prepare("UPDATE events SET is_active = NOT is_active WHERE id = ?");
            $stmt->execute([$_POST['id']]);
            $message = 'Event status updated!';

        } elseif ($action === 'toggle_featured') {
            $stmt = $pdo->prepare("UPDATE events SET is_featured = NOT is_featured WHERE id = ?");
            $stmt->execute([$_POST['id']]);
            $message = 'Featured status updated!';

        } elseif ($action === 'update_image') {
            $imagePath = uploadEventImage($_FILES['image'] ?? null);
            if ($imagePath) {
                $stmt = $pdo->prepare("UPDATE events SET image_path = ? WHERE id = ?");
                $stmt->execute([$imagePath, $_POST['id']]);
                $message = 'Event image updated!';
            } else {
                $error = 'Please select a valid image to upload.';
            }
        }

    } catch (PDOException $e) {
        $error = 'Error: ' . $e->getMessage();
    }
}

// Fetch all events
try {
    $stmt = $pdo->query("
        SELECT * FROM events 
        ORDER BY event_date ASC, display_order ASC
    ");
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = 'Error fetching events: ' . $e->getMessage();
    $events = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events Management - Admin Panel</title>
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
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
        }
        .page-title {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            color: var(--navy);
        }
        .btn-add {
            background: var(--gold);
            color: var(--deep-navy);
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(212, 175, 55, 0.3);
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
        .events-section {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            overflow-x: auto;
        }
        .event-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 2000px;
            border: 1px solid #d0d7de;
        }
        .event-table th {
            background: #f6f8fa;
            padding: 12px;
            text-align: left;
            font-size: 13px;
            font-weight: 600;
            color: #666;
            text-transform: uppercase;
            border: 1px solid #d0d7de;
        }
        .event-table td {
            padding: 0;
            border: 1px solid #d0d7de;
            vertical-align: middle;
            background: white;
        }
        .event-table tbody tr {
            transition: background 0.2s ease;
        }
        .event-table tbody tr:hover {
            background: #f8f9fa;
        }
        .title-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .event-thumb {
            width: 70px;
            height: 70px;
            border-radius: 10px;
            object-fit: cover;
            border: 2px solid #eee;
            background: #fafafa;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .event-thumb:hover {
            transform: scale(2.5);
            box-shadow: 0 8px 24px rgba(0,0,0,0.25);
            z-index: 100;
            border-color: var(--gold);
        }
        .event-thumb-placeholder {
            width: 70px;
            height: 70px;
            border-radius: 10px;
            background: linear-gradient(135deg, #f0f0f0 0%, #e0e0e0 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #999;
            font-size: 24px;
        }
        .event-table tbody tr.edit-mode {
            background: #fff3cd;
        }
        .event-table input,
        .event-table textarea,
        .event-table select {
            width: 100%;
            min-height: 50px;
            padding: 10px 14px;
            border: none;
            border-radius: 0;
            font-size: 14px;
            font-family: inherit;
            background: transparent;
        }
        .event-table input:focus,
        .event-table textarea:focus,
        .event-table select:focus {
            background: #fff8c7;
            box-shadow: inset 0 0 0 2px var(--gold);
            outline: none;
        }
        .event-table textarea {
            resize: vertical;
            min-height: 80px;
        }
        .cell-view {
            display: block;
        }
        .cell-view.hidden {
            display: none;
        }
        .cell-edit {
            display: none;
        }
        .cell-edit.active {
            display: block;
        }
        .actions-cell {
            white-space: nowrap;
            min-width: 200px;
        }
        .action-buttons {
            display: flex;
            gap: 6px;
            align-items: center;
            justify-content: flex-start;
        }
        .action-group {
            display: flex;
            gap: 4px;
            padding: 0 4px;
        }
        .action-group:not(:last-child) {
            border-right: 1px solid #e0e0e0;
            padding-right: 8px;
        }
        .badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            display: inline-block;
            margin: 2px;
        }
        .badge-active {
            background: #28a745;
            color: white;
        }
        .badge-inactive {
            background: #dc3545;
            color: white;
        }
        .badge-featured {
            background: var(--gold);
            color: var(--deep-navy);
        }
        .badge-free {
            background: #17a2b8;
            color: white;
        }
        .status-badges {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .btn-action {
            padding: 6px 14px;
            border: none;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            white-space: nowrap;
        }
        .btn-action:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }
        .btn-action:active {
            transform: translateY(0);
        }
        .btn-edit {
            background: #3498db;
            color: white;
        }
        .btn-edit:hover {
            background: #2980b9;
        }
        .btn-save {
            background: #27ae60;
            color: white;
        }
        .btn-save:hover {
            background: #229954;
        }
        .btn-cancel {
            background: #95a5a6;
            color: white;
        }
        .btn-cancel:hover {
            background: #7f8c8d;
        }
        .btn-delete {
            background: #e74c3c;
            color: white;
        }
        .btn-delete:hover {
            background: #c0392b;
        }
        .btn-toggle {
            background: #f39c12;
            color: white;
        }
        .btn-toggle:hover {
            background: #e67e22;
        }
        .btn-featured {
            background: var(--gold);
            color: var(--deep-navy);
        }
        .btn-featured:hover {
            background: #c19b2e;
        }
        .btn-upload {
            background: #9b59b6;
            color: white;
        }
        .btn-upload:hover {
            background: #8e44ad;
        }
        .image-upload-form {
            display: inline-block;
        }
        .image-upload-form input[type="file"] {
            display: none;
        }
        .image-upload-wrapper {
            position: relative;
        }
        .current-image-preview {
            width: 100%;
            max-width: 300px;
            border-radius: 12px;
            margin-bottom: 16px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
        }
        .image-upload-area {
            border: 2px dashed #ddd;
            border-radius: 12px;
            padding: 24px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }
        .image-upload-area:hover {
            border-color: var(--gold);
            background: #fff;
        }
        .image-upload-area i {
            font-size: 48px;
            color: var(--gold);
            margin-bottom: 12px;
            display: block;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 10000;
            align-items: center;
            justify-content: center;
        }
        .modal.active {
            display: flex;
        }
        .modal-content {
            background: white;
            border-radius: 12px;
            padding: 32px;
            max-width: 600px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }
        .modal-header {
            font-size: 24px;
            font-weight: 700;
            color: var(--navy);
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .modal-close {
            cursor: pointer;
            font-size: 28px;
            color: #999;
            transition: color 0.2s ease;
        }
        .modal-close:hover {
            color: #333;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
        }
        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .checkbox-group input {
            width: auto;
        }
        .modal-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            margin-top: 24px;
        }
        @media (max-width: 768px) {
            .content {
                padding: 16px;
            }
            .page-header {
                flex-direction: column;
                gap: 16px;
                align-items: flex-start;
            }
            .btn-add {
                width: 100%;
            }
            .event-table {
                font-size: 12px;
            }
            .event-table th,
            .event-table td {
                padding: 8px;
            }
            .action-buttons {
                flex-direction: column;
            }
            .btn-action {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <h1><i class="fas fa-calendar-alt"></i> Events Management</h1>
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

    <div class="content">
        <div class="page-header">
            <h2 class="page-title">Manage Hotel Events</h2>
            <button class="btn-add" onclick="openAddModal()">
                <i class="fas fa-plus"></i> Add New Event
            </button>
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

        <div class="events-section">
            <?php if (!empty($events)): ?>
                <table class="event-table">
                    <thead>
                        <tr>
                            <th style="width: 250px;">Title</th>
                            <th style="width: 140px;">Date</th>
                            <th style="width: 180px;">Time</th>
                            <th style="width: 200px;">Location</th>
                            <th style="width: 120px;">Price</th>
                            <th style="width: 100px;">Capacity</th>
                            <th style="width: 140px;">Status</th>
                            <th style="width: 400px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($events as $event): ?>
                            <tr id="row-<?php echo $event['id']; ?>">
                                <td>
                                    <div class="title-cell cell-view">
                                        <?php if (!empty($event['image_path'])): ?>
                                            <img src="../<?php echo htmlspecialchars($event['image_path']); ?>" 
                                                 alt="Event: <?php echo htmlspecialchars($event['title']); ?>" 
                                                 class="event-thumb" 
                                                 title="Click image upload to change"
                                                 onerror="this.outerHTML='&lt;div class=&quot;event-thumb-placeholder&quot;&gt;&lt;i class=&quot;fas fa-image&quot;&gt;&lt;/i&gt;&lt;/div&gt;';">
                                        <?php else: ?>
                                            <div class="event-thumb-placeholder"><i class="fas fa-image"></i></div>
                                        <?php endif; ?>
                                        <div>
                                            <strong><?php echo htmlspecialchars($event['title']); ?></strong>
                                            <div style="font-size: 11px; color: #999; margin-top: 2px;">
                                                <?php echo !empty($event['image_path']) ? basename($event['image_path']) : 'No image'; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="text" class="cell-edit" value="<?php echo htmlspecialchars($event['title']); ?>" data-field="title">
                                </td>
                                <td>
                                    <span class="cell-view"><?php echo date('M d, Y', strtotime($event['event_date'])); ?></span>
                                    <input type="date" class="cell-edit" value="<?php echo $event['event_date']; ?>" data-field="event_date">
                                </td>
                                <td>
                                    <span class="cell-view">
                                        <?php echo date('H:i', strtotime($event['start_time'])); ?> - 
                                        <?php echo date('H:i', strtotime($event['end_time'])); ?>
                                    </span>
                                    <input type="time" class="cell-edit" value="<?php echo $event['start_time']; ?>" data-field="start_time" placeholder="Start">
                                    <input type="time" class="cell-edit" value="<?php echo $event['end_time']; ?>" data-field="end_time" placeholder="End">
                                </td>
                                <td>
                                    <span class="cell-view"><?php echo htmlspecialchars($event['location']); ?></span>
                                    <input type="text" class="cell-edit" value="<?php echo htmlspecialchars($event['location']); ?>" data-field="location">
                                </td>
                                <td>
                                    <span class="cell-view">
                                        <?php if ($event['ticket_price'] == 0): ?>
                                            <span class="badge badge-free">Free</span>
                                        <?php else: ?>
                                            <?php echo htmlspecialchars(getSetting('currency_symbol')); ?> <?php echo number_format($event['ticket_price'], 0); ?>
                                        <?php endif; ?>
                                    </span>
                                    <input type="number" class="cell-edit" value="<?php echo $event['ticket_price']; ?>" step="0.01" data-field="ticket_price">
                                </td>
                                <td>
                                    <span class="cell-view"><?php echo $event['capacity']; ?></span>
                                    <input type="number" class="cell-edit" value="<?php echo $event['capacity']; ?>" data-field="capacity">
                                </td>
                                <td>
                                    <span class="cell-view">
                                        <div class="status-badges">
                                            <?php if ($event['is_active']): ?>
                                                <span class="badge badge-active"><i class="fas fa-check-circle"></i> Active</span>
                                            <?php else: ?>
                                                <span class="badge badge-inactive"><i class="fas fa-times-circle"></i> Inactive</span>
                                            <?php endif; ?>
                                            <?php if ($event['is_featured']): ?>
                                                <span class="badge badge-featured"><i class="fas fa-star"></i> Featured</span>
                                            <?php endif; ?>
                                        </div>
                                    </span>
                                    <select class="cell-edit" data-field="is_active">
                                        <option value="1" <?php echo $event['is_active'] ? 'selected' : ''; ?>>Active</option>
                                        <option value="0" <?php echo !$event['is_active'] ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
                                </td>
                                <td class="actions-cell">
                                    <div class="action-buttons">
                                        <!-- Edit Group -->
                                        <div class="action-group">
                                            <button class="btn-action btn-edit" onclick="enterEditMode(<?php echo $event['id']; ?>)" data-edit-btn title="Edit Event">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <button class="btn-action btn-save" style="display: none;" onclick="saveRow(<?php echo $event['id']; ?>)" data-save-btn title="Save Changes">
                                                <i class="fas fa-check"></i> Save
                                            </button>
                                            <button class="btn-action btn-cancel" style="display: none;" onclick="cancelEdit(<?php echo $event['id']; ?>)" data-cancel-btn title="Cancel Edit">
                                                <i class="fas fa-times"></i> Cancel
                                            </button>
                                        </div>
                                        
                                        <!-- Status Group -->
                                        <div class="action-group">
                                            <button class="btn-action btn-toggle" onclick="toggleActive(<?php echo $event['id']; ?>)" title="Toggle Active/Inactive">
                                                <i class="fas fa-power-off"></i> Toggle
                                            </button>
                                            <button class="btn-action btn-featured" onclick="toggleFeatured(<?php echo $event['id']; ?>)" title="Toggle Featured">
                                                <i class="fas fa-star"></i> Featured
                                            </button>
                                        </div>
                                        
                                        <!-- Media Group -->
                                        <div class="action-group">
                                            <form class="image-upload-form" method="POST" enctype="multipart/form-data" style="margin: 0; display: inline-block;">
                                                <input type="hidden" name="action" value="update_image">
                                                <input type="hidden" name="id" value="<?php echo $event['id']; ?>">
                                                <label class="btn-action btn-upload" style="cursor: pointer; margin: 0;" title="Upload Event Image">
                                                    <i class="fas fa-image"></i> Image
                                                    <input type="file" name="image" accept="image/jpeg,image/png,image/jpg,image/webp" style="display: none;" onchange="if(confirm('Upload and replace event image?')) { this.form.submit(); } else { this.value=''; }">
                                                </label>
                                            </form>
                                        </div>
                                        
                                        <!-- Delete Group -->
                                        <div class="action-group">
                                            <button class="btn-action btn-delete" onclick="if(confirm('Delete this event?')) deleteEvent(<?php echo $event['id']; ?>)" title="Delete Event">
                                                <i class="fas fa-trash-alt"></i> Delete
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div style="text-align: center; padding: 60px 20px; color: #999;">
                    <i class="fas fa-calendar-times" style="font-size: 64px; margin-bottom: 16px; color: #ddd;"></i>
                    <p>No events found. Click "Add New Event" to create your first event.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Add Event Modal -->
    <div class="modal" id="eventModal">
        <div class="modal-content">
            <div class="modal-header">
                <span id="modalTitle">Add New Event</span>
                <span class="modal-close" onclick="closeModal()">&times;</span>
            </div>
            
            <form method="POST" id="eventForm" enctype="multipart/form-data">
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="id" id="eventId">
                
                <div class="form-group">
                    <label>Event Title *</label>
                    <input type="text" name="title" id="eventTitle" required>
                </div>
                
                <div class="form-group">
                    <label>Description *</label>
                    <textarea name="description" id="eventDescription" required></textarea>
                </div>
                
                <div class="form-group">
                    <label>Event Date *</label>
                    <input type="date" name="event_date" id="eventDate" required>
                </div>
                
                <div class="form-group">
                    <label>Start Time</label>
                    <input type="time" name="start_time" id="eventStartTime">
                </div>
                
                <div class="form-group">
                    <label>End Time</label>
                    <input type="time" name="end_time" id="eventEndTime">
                </div>
                
                <div class="form-group">
                    <label>Location</label>
                    <input type="text" name="location" id="eventLocation" placeholder="e.g., Grand Conference Hall">
                </div>
                
                <div class="form-group">
                    <label>Ticket Price (<?php echo htmlspecialchars(getSetting('currency_symbol')); ?>)</label>
                    <input type="number" name="ticket_price" id="eventPrice" step="0.01" value="0">
                    <small style="color: #666;">Enter 0 for free events</small>
                </div>
                
                <div class="form-group">
                    <label>Capacity</label>
                    <input type="number" name="capacity" id="eventCapacity">
                </div>
                
                <div class="form-group">
                    <label>Display Order</label>
                    <input type="number" name="display_order" id="eventOrder" value="0">
                </div>

                <div class="form-group">
                    <label>Event Image</label>
                    <div class="image-upload-wrapper">
                        <div class="image-upload-area" onclick="document.getElementById('eventImage').click()">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <div style="font-weight: 600; margin-bottom: 4px;">Click to Upload Event Image</div>
                            <div style="font-size: 12px; color: #999;">Recommended: 1200x800px (JPG, PNG, WebP)</div>
                        </div>
                        <input type="file" name="image" id="eventImage" accept="image/jpeg,image/png,image/jpg,image/webp" style="display: none;" onchange="previewModalImage(this)">
                        <div id="imagePreviewContainer" style="margin-top: 16px; display: none;">
                            <img id="imagePreview" class="current-image-preview" alt="Preview">
                            <div style="font-size: 12px; color: #666; margin-top: 8px;">
                                <i class="fas fa-check-circle" style="color: #28a745;"></i> 
                                <span id="imageFileName"></span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="form-group checkbox-group">
                    <input type="checkbox" name="is_featured" id="eventFeatured">
                    <label for="eventFeatured">Feature this event</label>
                </div>
                
                <div class="form-group checkbox-group">
                    <input type="checkbox" name="is_active" id="eventActive" checked>
                    <label for="eventActive">Active (visible on website)</label>
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="btn-action btn-cancel" onclick="closeModal()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn-action btn-save">
                        <i class="fas fa-save"></i> Save
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let currentEditingId = null;

        function openAddModal() {
            document.getElementById('modalTitle').textContent = 'Add New Event';
            document.getElementById('formAction').value = 'add';
            document.getElementById('eventForm').reset();
            document.getElementById('eventActive').checked = true;
            document.getElementById('eventModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('eventModal').classList.remove('active');
        }

        document.getElementById('eventModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        function enterEditMode(id) {
            if (currentEditingId && currentEditingId !== id) {
                cancelEdit(currentEditingId);
            }

            currentEditingId = id;
            const row = document.getElementById(`row-${id}`);
            
            row.querySelectorAll('.cell-view').forEach(el => el.classList.add('hidden'));
            row.querySelectorAll('.cell-edit').forEach(el => el.classList.add('active'));
            
            row.querySelector('[data-edit-btn]').style.display = 'none';
            row.querySelector('[data-save-btn]').style.display = 'block';
            row.querySelector('[data-cancel-btn]').style.display = 'block';
            
            row.classList.add('edit-mode');
        }

        function cancelEdit(id) {
            const row = document.getElementById(`row-${id}`);
            
            row.querySelectorAll('.cell-view').forEach(el => el.classList.remove('hidden'));
            row.querySelectorAll('.cell-edit').forEach(el => el.classList.remove('active'));
            
            row.querySelector('[data-edit-btn]').style.display = 'block';
            row.querySelector('[data-save-btn]').style.display = 'none';
            row.querySelector('[data-cancel-btn]').style.display = 'none';
            
            row.classList.remove('edit-mode');
            currentEditingId = null;
        }

        function saveRow(id) {
            const row = document.getElementById(`row-${id}`);
            const formData = new FormData();
            
            formData.append('action', 'update');
            formData.append('id', id);
            formData.append('description', ''); // Not editable inline
            formData.append('is_featured', 0);
            formData.append('display_order', 0);
            
            row.querySelectorAll('.cell-edit.active').forEach(input => {
                const field = input.getAttribute('data-field');
                formData.append(field, input.value);
            });
            
            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (response.ok) {
                    window.location.reload();
                } else {
                    alert('Error saving event');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error saving event');
            });
        }

        function deleteEvent(id) {
            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('id', id);
            
            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (response.ok) {
                    window.location.reload();
                } else {
                    alert('Error deleting event');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deleting event');
            });
        }

        function toggleActive(id) {
            const formData = new FormData();
            formData.append('action', 'toggle_active');
            formData.append('id', id);
            
            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (response.ok) {
                    window.location.reload();
                } else {
                    alert('Error toggling status');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error toggling status');
            });
        }

        function toggleFeatured(id) {
            const formData = new FormData();
            formData.append('action', 'toggle_featured');
            formData.append('id', id);
            
            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (response.ok) {
                    window.location.reload();
                } else {
                    alert('Error toggling featured status');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error toggling featured status');
            });
        }

        function previewModalImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    document.getElementById('imagePreview').src = e.target.result;
                    document.getElementById('imageFileName').textContent = input.files[0].name;
                    document.getElementById('imagePreviewContainer').style.display = 'block';
                };
                
                reader.readAsDataURL(input.files[0]);
            }
        }

        function openAddModal() {
            document.getElementById('modalTitle').textContent = 'Add New Event';
            document.getElementById('formAction').value = 'add';
            document.getElementById('eventForm').reset();
            document.getElementById('eventActive').checked = true;
            document.getElementById('imagePreviewContainer').style.display = 'none';
            document.getElementById('eventModal').classList.add('active');
        }
    </script>
</body>
</html>
