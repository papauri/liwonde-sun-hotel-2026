<?php
session_start();

// Check authentication
if (!isset($_SESSION['admin_user'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';

// Handle AJAX requests for gallery images
if (isset($_GET['action']) && $_GET['action'] === 'get_gallery' && isset($_GET['room_id'])) {
    header('Content-Type: application/json');
    $room_id = intval($_GET['room_id']);
    try {
        $stmt = $pdo->prepare("SELECT * FROM gallery WHERE room_id = ? AND is_active = 1 ORDER BY display_order ASC, id DESC");
        $stmt->execute([$room_id]);
        $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($images);
    } catch (PDOException $e) {
        echo json_encode([]);
    }
    exit;
}

$user = $_SESSION['admin_user'];
$message = '';
$error = '';

// Handle room actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $action = $_POST['action'] ?? '';

        if ($action === 'update') {
            // Update existing room
            $stmt = $pdo->prepare("
                UPDATE rooms 
                SET name = ?, description = ?, short_description = ?, price_per_night = ?, 
                    size_sqm = ?, max_guests = ?, rooms_available = ?, total_rooms = ?, 
                    bed_type = ?, amenities = ?, is_featured = ?, is_active = ?, display_order = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $_POST['name'],
                $_POST['description'] ?? $_POST['short_description'],
                $_POST['short_description'],
                $_POST['price_per_night'],
                $_POST['size_sqm'],
                $_POST['max_guests'],
                $_POST['rooms_available'] ?? 0,
                $_POST['total_rooms'] ?? 0,
                $_POST['bed_type'],
                $_POST['amenities'] ?? '',
                isset($_POST['is_featured']) ? 1 : 0,
                isset($_POST['is_active']) ? 1 : 0,
                $_POST['display_order'] ?? 0,
                $_POST['id']
            ]);
            $message = 'Room updated successfully!';

        } elseif ($action === 'toggle_active') {
            $stmt = $pdo->prepare("UPDATE rooms SET is_active = NOT is_active WHERE id = ?");
            $stmt->execute([$_POST['id']]);
            $message = 'Room status updated!';

        } elseif ($action === 'toggle_featured') {
            $stmt = $pdo->prepare("UPDATE rooms SET is_featured = NOT is_featured WHERE id = ?");
            $stmt->execute([$_POST['id']]);
            $message = 'Featured status updated!';

        } elseif ($action === 'upload_image') {
            // Handle featured image upload
            if (isset($_FILES['room_image']) && $_FILES['room_image']['error'] === 0) {
                $room_id = $_POST['room_id'];
                $allowed = ['jpg', 'jpeg', 'png', 'webp'];
                $filename = $_FILES['room_image']['name'];
                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                
                if (in_array($ext, $allowed)) {
                    // Create upload directory if not exists
                    $upload_dir = '../images/rooms/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    
                    // Generate unique filename
                    $new_filename = 'room_' . $room_id . '_featured_' . time() . '.' . $ext;
                    $upload_path = $upload_dir . $new_filename;
                    
                    if (move_uploaded_file($_FILES['room_image']['tmp_name'], $upload_path)) {
                        // Update database with new image path
                        $image_url = 'images/rooms/' . $new_filename;
                        $stmt = $pdo->prepare("UPDATE rooms SET image_url = ? WHERE id = ?");
                        $stmt->execute([$image_url, $room_id]);
                        $message = 'Featured room image uploaded successfully!';
                    } else {
                        $error = 'Failed to upload image.';
                    }
                } else {
                    $error = 'Invalid file type. Only JPG, PNG, and WEBP allowed.';
                }
            } else {
                $error = 'No image selected or upload error.';
            }

        } elseif ($action === 'upload_gallery_image') {
            // Handle gallery image upload
            if (isset($_FILES['gallery_image']) && $_FILES['gallery_image']['error'] === 0) {
                $room_id = $_POST['room_id'];
                $title = $_POST['image_title'] ?? 'Room View';
                $description = $_POST['image_description'] ?? '';
                $allowed = ['jpg', 'jpeg', 'png', 'webp'];
                $filename = $_FILES['gallery_image']['name'];
                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                
                if (in_array($ext, $allowed)) {
                    // Create upload directory if not exists
                    $upload_dir = '../images/rooms/gallery/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    
                    // Generate unique filename
                    $new_filename = 'room_' . $room_id . '_gallery_' . time() . '.' . $ext;
                    $upload_path = $upload_dir . $new_filename;
                    
                    if (move_uploaded_file($_FILES['gallery_image']['tmp_name'], $upload_path)) {
                        // Insert into gallery table
                        $image_url = 'images/rooms/gallery/' . $new_filename;
                        $stmt = $pdo->prepare("INSERT INTO gallery (title, description, image_url, category, room_id, is_active) VALUES (?, ?, ?, 'rooms', ?, 1)");
                        $stmt->execute([$title, $description, $image_url, $room_id]);
                        $message = 'Gallery image uploaded successfully!';
                    } else {
                        $error = 'Failed to upload gallery image.';
                    }
                } else {
                    $error = 'Invalid file type. Only JPG, PNG, and WEBP allowed.';
                }
            } else {
                $error = 'No image selected or upload error.';
            }

        } elseif ($action === 'delete_gallery_image') {
            $image_id = $_POST['image_id'];
            $stmt = $pdo->prepare("DELETE FROM gallery WHERE id = ?");
            $stmt->execute([$image_id]);
            $message = 'Gallery image deleted successfully!';
        }

    } catch (PDOException $e) {
        $error = 'Error: ' . $e->getMessage();
    }
}

// Fetch all rooms
try {
    $stmt = $pdo->query("SELECT * FROM rooms ORDER BY display_order ASC, name ASC");
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = 'Error fetching rooms: ' . $e->getMessage();
    $rooms = [];
}

// Function to get room gallery images
function getRoomGalleryImages($pdo, $room_id) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM gallery WHERE room_id = ? AND is_active = 1 ORDER BY display_order ASC, id DESC");
        $stmt->execute([$room_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Management - Admin Panel</title>
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
        .rooms-section {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }
        .room-table {
            width: 100%;
            border-collapse: collapse;
        }
        .room-table th {
            background: #f8f9fa;
            padding: 12px;
            text-align: left;
            font-size: 13px;
            font-weight: 600;
            color: #666;
            text-transform: uppercase;
            border-bottom: 2px solid #dee2e6;
        }
        .room-table td {
            padding: 12px;
            border-bottom: 1px solid #f0f0f0;
            vertical-align: middle;
        }
        .room-table tbody tr {
            transition: background 0.2s ease;
        }
        .room-table tbody tr:hover {
            background: #f8f9fa;
        }
        .room-table tbody tr.edit-mode {
            background: #fff3cd;
        }
        .room-table input,
        .room-table textarea,
        .room-table select {
            width: 100%;
            padding: 6px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 13px;
            font-family: inherit;
        }
        .room-table textarea {
            resize: vertical;
            min-height: 50px;
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
        }
        .action-buttons {
            display: flex;
            gap: 4px;
            flex-wrap: wrap;
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
        .room-image-preview {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            cursor: pointer;
            transition: transform 0.2s ease;
            border: 2px solid #e0e0e0;
        }
        .room-image-preview:hover {
            transform: scale(1.05);
            border-color: var(--gold);
        }
        .no-image {
            width: 80px;
            height: 60px;
            background: #f0f0f0;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #999;
            font-size: 24px;
            cursor: pointer;
            border: 2px dashed #ddd;
            transition: all 0.2s ease;
        }
        .no-image:hover {
            background: #e8e8e8;
            border-color: var(--gold);
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
        .current-image {
            width: 100%;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .upload-area {
            border: 2px dashed #ddd;
            border-radius: 8px;
            padding: 40px 20px;
            text-align: center;
            background: #f8f9fa;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }
        .upload-area:hover {
            border-color: var(--gold);
            background: #fff;
        }
        .upload-area i {
            font-size: 48px;
            color: #ddd;
            margin-bottom: 12px;
        }
        .upload-area.dragover {
            border-color: var(--gold);
            background: #fffbf0;
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
        .form-group input[type="file"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
        }
        .modal-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            margin-top: 24px;
        }
        .status-badges {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .btn-action {
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            font-size: 11px;
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
        }
        .btn-edit {
            background: #17a2b8;
            color: white;
        }
        .btn-edit:hover {
            background: #138496;
        }
        .btn-save {
            background: #28a745;
            color: white;
        }
        .btn-save:hover {
            background: #218838;
        }
        .btn-cancel {
            background: #6c757d;
            color: white;
        }
        .btn-cancel:hover {
            background: #5a6268;
        }
        .btn-toggle {
            background: #ffc107;
            color: #212529;
        }
        .btn-toggle:hover {
            background: #e0a800;
        }
        .btn-featured {
            background: var(--gold);
            color: var(--deep-navy);
        }
        .btn-featured:hover {
            background: #c19b2e;
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
            .room-table {
                font-size: 12px;
            }
            .room-table th,
            .room-table td {
                padding: 8px;
            }
            .room-table th {
                font-size: 11px;
            }
            .room-table input,
            .room-table textarea,
            .room-table select {
                padding: 4px;
                font-size: 12px;
            }
            .action-buttons {
                flex-direction: column;
                gap: 2px;
            }
            .btn-action {
                padding: 4px 8px;
                font-size: 10px;
                width: 100%;
                text-align: center;
            }
            .rooms-section {
                padding: 16px;
            }
        }
        @media (max-width: 480px) {
            .content {
                padding: 12px;
            }
            .room-table {
                font-size: 11px;
            }
            .room-table th,
            .room-table td {
                padding: 6px;
            }
            .room-table th {
                font-size: 10px;
            }
            .btn-action {
                padding: 3px 6px;
                font-size: 9px;
            }
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <h1><i class="fas fa-bed"></i> Room Management</h1>
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
            <li><a href="bookings.php"><i class="fas fa-calendar-check"></i> Bookings</a></li>
            <li><a href="room-management.php" class="active"><i class="fas fa-bed"></i> Rooms</a></li>
            <li><a href="conference-management.php"><i class="fas fa-briefcase"></i> Conference Rooms</a></li>
            <li><a href="menu-management.php"><i class="fas fa-utensils"></i> Menu</a></li>
            <li><a href="events-management.php"><i class="fas fa-calendar-alt"></i> Events</a></li>
            <li><a href="../index.php" target="_blank"><i class="fas fa-external-link-alt"></i> View Website</a></li>
        </ul>
    </nav>

    <div class="content">
        <div class="page-header">
            <h2 class="page-title">Manage Hotel Rooms</h2>
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

        <div class="rooms-section">
            <?php if (!empty($rooms)): ?>
                <table class="room-table">
                    <thead>
                        <tr>
                            <th style="width: 7%;">Image</th>
                            <th style="width: 3%;">Order</th>
                            <th style="width: 10%;">Room Name</th>
                            <th style="width: 10%;">Short Desc</th>
                            <th style="width: 5%;">Price/Night</th>
                            <th style="width: 3%;">Size</th>
                            <th style="width: 4%;">Guests</th>
                            <th style="width: 6%;">Availability</th>
                            <th style="width: 7%;">Bed Type</th>
                            <th style="width: 10%;">Amenities</th>
                            <th style="width: 8%;">Status</th>
                            <th style="width: 27%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rooms as $room): ?>
                            <tr id="row-<?php echo $room['id']; ?>">
                                <td>
                                    <?php if (!empty($room['image_url']) && file_exists('../' . $room['image_url'])): ?>
                                        <img src="../<?php echo htmlspecialchars($room['image_url']); ?>" 
                                             alt="<?php echo htmlspecialchars($room['name']); ?>" 
                                             class="room-image-preview" 
                                             onclick="openImageModal(<?php echo $room['id']; ?>, '<?php echo htmlspecialchars($room['name']); ?>', '<?php echo htmlspecialchars($room['image_url']); ?>')">
                                    <?php else: ?>
                                        <div class="no-image" onclick="openImageModal(<?php echo $room['id']; ?>, '<?php echo htmlspecialchars($room['name']); ?>', '')">
                                            <i class="fas fa-camera"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="cell-view"><?php echo $room['display_order']; ?></span>
                                    <input type="number" class="cell-edit" value="<?php echo $room['display_order']; ?>" data-field="display_order">
                                </td>
                                <td>
                                    <span class="cell-view"><strong><?php echo htmlspecialchars($room['name']); ?></strong></span>
                                    <input type="text" class="cell-edit" value="<?php echo htmlspecialchars($room['name']); ?>" data-field="name">
                                </td>
                                <td>
                                    <span class="cell-view"><?php echo htmlspecialchars(substr($room['short_description'], 0, 50)) . '...'; ?></span>
                                    <textarea class="cell-edit" data-field="short_description"><?php echo htmlspecialchars($room['short_description']); ?></textarea>
                                </td>
                                <td>
                                    <span class="cell-view">K <?php echo number_format($room['price_per_night'], 0); ?></span>
                                    <input type="number" class="cell-edit" value="<?php echo $room['price_per_night']; ?>" step="0.01" data-field="price_per_night">
                                </td>
                                <td>
                                    <span class="cell-view"><?php echo $room['size_sqm']; ?></span>
                                    <input type="number" class="cell-edit" value="<?php echo $room['size_sqm']; ?>" data-field="size_sqm">
                                </td>
                                <td>
                                    <span class="cell-view"><?php echo $room['max_guests']; ?></span>
                                    <input type="number" class="cell-edit" value="<?php echo $room['max_guests']; ?>" data-field="max_guests">
                                </td>
                                <td>
                                    <span class="cell-view"><?php echo ($room['rooms_available'] ?? 0) . '/' . ($room['total_rooms'] ?? 0); ?></span>
                                    <div class="cell-edit" style="display: flex; gap: 4px; align-items: center;">
                                        <input type="number" min="0" style="width: 45px;" value="<?php echo $room['rooms_available'] ?? 0; ?>" data-field="rooms_available" title="Available">
                                        <span>/</span>
                                        <input type="number" min="1" style="width: 45px;" value="<?php echo $room['total_rooms'] ?? 0; ?>" data-field="total_rooms" title="Total">
                                    </div>
                                </td>
                                <td>
                                    <span class="cell-view"><?php echo htmlspecialchars($room['bed_type']); ?></span>
                                    <input type="text" class="cell-edit" value="<?php echo htmlspecialchars($room['bed_type']); ?>" data-field="bed_type">
                                </td>
                                <td>
                                    <span class="cell-view"><?php echo htmlspecialchars(substr($room['amenities'] ?? 'N/A', 0, 40)) . '...'; ?></span>
                                    <textarea class="cell-edit" data-field="amenities" placeholder="Comma-separated amenities"><?php echo htmlspecialchars($room['amenities'] ?? ''); ?></textarea>
                                </td>
                                <td>
                                    <span class="cell-view">
                                        <div class="status-badges">
                                            <?php if ($room['is_active']): ?>
                                                <span class="badge badge-active"><i class="fas fa-check-circle"></i> Active</span>
                                            <?php else: ?>
                                                <span class="badge badge-inactive"><i class="fas fa-times-circle"></i> Inactive</span>
                                            <?php endif; ?>
                                            <?php if ($room['is_featured']): ?>
                                                <span class="badge badge-featured"><i class="fas fa-star"></i> Featured</span>
                                            <?php endif; ?>
                                        </div>
                                    </span>
                                    <select class="cell-edit" data-field="is_active">
                                        <option value="1" <?php echo $room['is_active'] ? 'selected' : ''; ?>>Active</option>
                                        <option value="0" <?php echo !$room['is_active'] ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
                                </td>
                                <td class="actions-cell">
                                    <div class="action-buttons">
                                        <button class="btn-action btn-edit" onclick="enterEditMode(<?php echo $room['id']; ?>)" data-edit-btn>
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button class="btn-action btn-save" style="display: none;" onclick="saveRow(<?php echo $room['id']; ?>)" data-save-btn>
                                            <i class="fas fa-save"></i> Save
                                        </button>
                                        <button class="btn-action btn-cancel" style="display: none;" onclick="cancelEdit(<?php echo $room['id']; ?>)" data-cancel-btn>
                                            <i class="fas fa-times"></i> Cancel
                                        </button>
                                        <button class="btn-action btn-toggle" onclick="toggleActive(<?php echo $room['id']; ?>)">
                                            <i class="fas fa-power-off"></i> Toggle
                                        </button>
                                        <button class="btn-action btn-featured" onclick="toggleFeatured(<?php echo $room['id']; ?>)">
                                            <i class="fas fa-star"></i> Featured
                                        </button>
                                        <button class="btn-action" style="background: #6f42c1; color: white;" onclick="openImageModal(<?php echo $room['id']; ?>, '<?php echo htmlspecialchars($room['name']); ?>', '<?php echo htmlspecialchars($room['image_url'] ?? ''); ?>')">
                                            <i class="fas fa-image"></i> Featured
                                        </button>
                                        <button class="btn-action" style="background: #17a2b8; color: white;" onclick="openGalleryModal(<?php echo $room['id']; ?>, '<?php echo htmlspecialchars($room['name']); ?>')">
                                            <i class="fas fa-images"></i> Gallery (<?php echo count(getRoomGalleryImages($pdo, $room['id'])); ?>)
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state" style="text-align: center; padding: 40px; color: #999;">
                    <i class="fas fa-bed" style="font-size: 48px; margin-bottom: 16px; color: #ddd;"></i>
                    <p>No rooms found in the database.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Image Upload Modal -->
    <div class="modal" id="imageModal">
        <div class="modal-content">
            <div class="modal-header">
                <span id="modalRoomName">Room Image</span>
                <span class="modal-close" onclick="closeImageModal()">&times;</span>
            </div>
            
            <div id="currentImageContainer" style="display: none;">
                <h4 style="margin-bottom: 12px; color: #666;">Current Image</h4>
                <img id="currentImage" class="current-image" alt="Current room image">
            </div>

            <h4 style="margin: 20px 0 12px 0; color: #666;">Upload New Image</h4>
            <form method="POST" enctype="multipart/form-data" id="imageUploadForm">
                <input type="hidden" name="action" value="upload_image">
                <input type="hidden" name="room_id" id="uploadRoomId">
                
                <div class="upload-area" onclick="document.getElementById('roomImageInput').click()">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <p style="margin: 8px 0; color: #666;">Click to select an image or drag and drop</p>
                    <small style="color: #999;">JPG, PNG, WEBP (Max 5MB)</small>
                </div>

                <div class="form-group">
                    <label>Select Image File *</label>
                    <input type="file" name="room_image" id="roomImageInput" accept="image/jpeg,image/png,image/webp" required>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-action btn-cancel" onclick="closeImageModal()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn-action btn-save">
                        <i class="fas fa-upload"></i> Upload Image
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Room Gallery Modal -->
    <div class="modal" id="galleryModal">
        <div class="modal-content" style="max-width: 900px;">
            <div class="modal-header">
                <span id="modalGalleryRoomName">Room Gallery</span>
                <span class="modal-close" onclick="closeGalleryModal()">&times;</span>
            </div>
            
            <div id="galleryImagesContainer">
                <h4 style="margin-bottom: 16px; color: #666;">Room Gallery Images</h4>
                <div id="galleryImagesList" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px;">
                    <!-- Gallery images will be loaded here -->
                </div>
            </div>

            <h4 style="margin: 24px 0 12px 0; color: #666; border-top: 2px solid #f0f0f0; padding-top: 24px;">Upload New Gallery Image</h4>
            <form method="POST" enctype="multipart/form-data" id="galleryUploadForm">
                <input type="hidden" name="action" value="upload_gallery_image">
                <input type="hidden" name="room_id" id="uploadGalleryRoomId">
                
                <div class="form-group">
                    <label>Image Title *</label>
                    <input type="text" name="image_title" placeholder="e.g., Bedroom View" required>
                </div>

                <div class="form-group">
                    <label>Image Description</label>
                    <textarea name="image_description" rows="2" placeholder="Optional description"></textarea>
                </div>

                <div class="upload-area" onclick="document.getElementById('galleryImageInput').click()">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <p style="margin: 8px 0; color: #666;">Click to select an image or drag and drop</p>
                    <small style="color: #999;">JPG, PNG, WEBP (Max 5MB)</small>
                </div>

                <div class="form-group">
                    <label>Select Image File *</label>
                    <input type="file" name="gallery_image" id="galleryImageInput" accept="image/jpeg,image/png,image/webp" required>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-action btn-cancel" onclick="closeGalleryModal()">
                        <i class="fas fa-times"></i> Close
                    </button>
                    <button type="submit" class="btn-action btn-save">
                        <i class="fas fa-upload"></i> Upload to Gallery
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let currentEditingId = null;

        // Image Modal Functions
        function openImageModal(roomId, roomName, currentImageUrl) {
            document.getElementById('modalRoomName').textContent = roomName + ' - Image';
            document.getElementById('uploadRoomId').value = roomId;
            
            if (currentImageUrl && currentImageUrl !== '') {
                document.getElementById('currentImage').src = '../' + currentImageUrl;
                document.getElementById('currentImageContainer').style.display = 'block';
            } else {
                document.getElementById('currentImageContainer').style.display = 'none';
            }
            
            document.getElementById('imageModal').classList.add('active');
        }

        function closeImageModal() {
            document.getElementById('imageModal').classList.remove('active');
            document.getElementById('imageUploadForm').reset();
        }

        // Gallery Modal Functions
        function openGalleryModal(roomId, roomName) {
            document.getElementById('modalGalleryRoomName').textContent = roomName + ' - Gallery';
            document.getElementById('uploadGalleryRoomId').value = roomId;
            
            // Load existing gallery images
            fetch(`?action=get_gallery&room_id=${roomId}`)
                .then(response => response.json())
                .then(images => {
                    const container = document.getElementById('galleryImagesList');
                    if (images.length === 0) {
                        container.innerHTML = '<p style="grid-column: 1/-1; text-align: center; color: #999; padding: 40px;">No gallery images yet. Upload your first image below.</p>';
                    } else {
                        container.innerHTML = images.map(img => `
                            <div style="position: relative; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden;">
                                <img src="../${img.image_url}" alt="${img.title}" style="width: 100%; height: 150px; object-fit: cover;">
                                <div style="padding: 8px; background: #f8f9fa;">
                                    <div style="font-weight: 600; font-size: 13px; color: #333; margin-bottom: 4px;">${img.title}</div>
                                    ${img.description ? `<div style="font-size: 11px; color: #666;">${img.description}</div>` : ''}
                                </div>
                                <button onclick="deleteGalleryImage(${img.id})" 
                                        style="position: absolute; top: 8px; right: 8px; background: rgba(220, 53, 69, 0.9); color: white; border: none; border-radius: 4px; padding: 6px 10px; cursor: pointer; font-size: 11px;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        `).join('');
                    }
                })
                .catch(error => {
                    console.error('Error loading gallery:', error);
                });
            
            document.getElementById('galleryModal').classList.add('active');
        }

        function closeGalleryModal() {
            document.getElementById('galleryModal').classList.remove('active');
            document.getElementById('galleryUploadForm').reset();
        }

        function deleteGalleryImage(imageId) {
            if (!confirm('Are you sure you want to delete this gallery image?')) return;
            
            const formData = new FormData();
            formData.append('action', 'delete_gallery_image');
            formData.append('image_id', imageId);
            
            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (response.ok) {
                    location.reload();
                } else {
                    alert('Error deleting image');
                }
            });
        }

        // Close modals on outside click
        document.getElementById('galleryModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeGalleryModal();
            }
        });

        // Close modal on outside click
        document.getElementById('imageModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeImageModal();
            }
        });

        // Drag and drop functionality
        const uploadArea = document.querySelector('.upload-area');
        const fileInput = document.getElementById('roomImageInput');

        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
            }
        });

        // Edit Mode Functions

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
            
            // Collect all edited values
            row.querySelectorAll('.cell-edit.active').forEach(input => {
                const field = input.getAttribute('data-field');
                formData.append(field, input.value);
            });
            
            // Add description (full text, not editable inline but needed)
            formData.append('description', row.querySelector('[data-field="short_description"]') ? row.querySelector('[data-field="short_description"]').value : '');
            
            // Add featured status (not editable inline, but needed for update)
            formData.append('is_featured', 0);
            
            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (response.ok) {
                    window.location.reload();
                } else {
                    alert('Error saving room');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error saving room');
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
    </script>
</body>
</html>
