<?php
session_start();

// Check authentication
if (!isset($_SESSION['admin_user'])) {
    // For AJAX requests, return JSON with 401 instead of redirecting HTML
    $is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    if ($is_ajax) {
        header('Content-Type: application/json');
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Session expired. Please login again.']);
        exit;
    }
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';

// Helpers
function ini_bytes($val) {
    $val = trim((string)$val);
    if ($val === '') return 0;
    $last = strtolower($val[strlen($val) - 1]);
    $num = (int)$val;
    switch ($last) {
        case 'g': $num *= 1024;
        case 'm': $num *= 1024;
        case 'k': $num *= 1024;
    }
    return $num;
}
function is_ajax_request() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

$user = $_SESSION['admin_user'];
$message = '';
$error = '';
$current_page = basename($_SERVER['PHP_SELF']);

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
                $size = isset($_FILES['room_image']['size']) ? (int)$_FILES['room_image']['size'] : 0;
                if ($size > 20 * 1024 * 1024) {
                    $error = 'File too large. Max size is 20MB.';
                    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                        header('Content-Type: application/json');
                        echo json_encode(['success' => false, 'message' => $error]);
                        exit;
                    }
                }
                
                // Optional MIME check to prevent spoofed extensions
                $mime_ok = true;
                if (function_exists('finfo_open')) {
                    $f = finfo_open(FILEINFO_MIME_TYPE);
                    if ($f) {
                        $mime = finfo_file($f, $_FILES['room_image']['tmp_name']);
                        finfo_close($f);
                        $allowed_mimes = ['image/jpeg','image/png','image/webp'];
                        $mime_ok = in_array($mime, $allowed_mimes, true);
                    }
                }

                if (in_array($ext, $allowed) && $mime_ok) {
                    // Create upload directory if not exists
                    $upload_dir = '../images/rooms/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    
                    // Generate unique filename
                    $new_filename = 'room_' . $room_id . '_featured_' . time() . '.' . $ext;
                    $upload_path = $upload_dir . $new_filename;
                    
                    if (move_uploaded_file($_FILES['room_image']['tmp_name'], $upload_path)) {
                        // New image path
                        $image_url = 'images/rooms/' . $new_filename;

                        // Fetch old image (if any) before updating
                        $oldStmt = $pdo->prepare("SELECT image_url FROM rooms WHERE id = ?");
                        $oldStmt->execute([$room_id]);
                        $old_image = $oldStmt->fetchColumn();

                        // Update database with new image path
                        $stmt = $pdo->prepare("UPDATE rooms SET image_url = ? WHERE id = ?");
                        $stmt->execute([$image_url, $room_id]);
                        $message = 'Featured room image uploaded successfully!';

                        // Delete old local file if it exists and is not a remote URL
                        if ($old_image && !preg_match('#^https?://#i', $old_image)) {
                            $old_path = '../' . $old_image;
                            if (file_exists($old_path)) {
                                @unlink($old_path);
                            }
                        }

                        // If request is AJAX, return JSON with the new image URL so client can update modal without closing it
                        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                            header('Content-Type: application/json');
                            echo json_encode(['success' => true, 'image_url' => $image_url]);
                            exit;
                        }
                    } else {
                        $error = 'Failed to upload image.';
                        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                            header('Content-Type: application/json');
                            echo json_encode(['success' => false, 'message' => 'Failed to upload image.']);
                            exit;
                        }
                    }
                } else {
                    $error = 'Invalid file type. Only JPG, PNG, and WEBP allowed.';
                    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                        header('Content-Type: application/json');
                        echo json_encode(['success' => false, 'message' => $error]);
                        exit;
                    }
                }
            } else {
                // POST may exceed post_max_size causing empty $_FILES
                $contentLen = isset($_SERVER['CONTENT_LENGTH']) ? (int)$_SERVER['CONTENT_LENGTH'] : 0;
                $postMax = ini_bytes(ini_get('post_max_size'));
                if ($contentLen > 0 && $postMax > 0 && $contentLen >= $postMax) {
                    $error = 'Upload too large. Server POST limit is ' . ini_get('post_max_size') . '. Please contact admin to increase limit.';
                    if (is_ajax_request()) {
                        header('Content-Type: application/json');
                        echo json_encode(['success' => false, 'message' => $error]);
                        exit;
                    }
                }

                $err = $_FILES['room_image']['error'] ?? 0;
                if ($err === UPLOAD_ERR_INI_SIZE || $err === UPLOAD_ERR_FORM_SIZE) {
                    $error = 'File too large. Max size is 20MB.';
                } elseif ($err === UPLOAD_ERR_PARTIAL) {
                    $error = 'Upload was partial. Please try again.';
                } elseif ($err === UPLOAD_ERR_NO_FILE) {
                    $error = 'No image selected.';
                } else {
                    $error = 'Upload error (code ' . $err . ').';
                }
                if (is_ajax_request()) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => $error]);
                    exit;
                }
            }

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
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            overflow-x: auto;
        }
        .room-table {
            width: 100%;
            min-width: 2200px;
            border-collapse: collapse;
            border: 1px solid #d0d7de;
        }
        .room-table th {
            background: #f6f8fa;
            padding: 12px 14px;
            text-align: left;
            font-size: 12px;
            font-weight: 600;
            color: #24292f;
            text-transform: uppercase;
            border: 1px solid #d0d7de;
            border-bottom: 2px solid #d0d7de;
            white-space: nowrap;
        }
        .room-table td {
            padding: 0;
            border: 1px solid #d0d7de;
            vertical-align: middle;
            background: white;
        }
        .room-table td.image-cell {
            padding: 12px;
            text-align: center;
            vertical-align: middle;
        }
        .room-table tbody tr {
            transition: background 0.2s ease;
        }
        .room-table tbody tr:hover {
            background: #f6f8fa;
        }
        .room-table tbody tr.edit-mode {
            background: #fff8c7;
        }
        .room-table tbody tr.edit-mode td {
            background: #fff8c7;
        }
        .room-table input,
        .room-table textarea,
        .room-table select {
            width: 100%;
            height: 100%;
            min-height: 50px;
            padding: 10px 14px;
            border: none;
            border-radius: 0;
            font-size: 14px;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Helvetica Neue', Arial, sans-serif;
            background: transparent;
            transition: background 0.2s ease;
        }
        .room-table input:focus,
        .room-table textarea:focus,
        .room-table select:focus {
            outline: none;
            background: #fff8c7;
            box-shadow: inset 0 0 0 2px var(--gold);
        }
        .room-table textarea {
            resize: none;
            min-height: 80px;
            line-height: 1.5;
        }
        .room-table select {
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23666' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 8px center;
            padding-right: 28px;
        }
        .cell-view {
            display: block;
            padding: 12px 14px;
            min-height: 50px;
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
        .cell-edit.active input,
        .cell-edit.active textarea,
        .cell-edit.active select {
            display: block;
        }
        .actions-cell {
            white-space: nowrap;
            min-width: 320px;
            padding: 8px 12px !important;
        }
        .action-buttons {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
            align-items: center;
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
            width: 120px;
            height: 90px;
            object-fit: cover;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 3px solid #e8e8e8;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .room-image-preview:hover {
            transform: scale(1.08);
            border-color: var(--gold);
            box-shadow: 0 4px 16px rgba(212, 175, 55, 0.3);
        }
        .no-image {
            width: 120px;
            height: 90px;
            background: linear-gradient(135deg, #f5f5f5 0%, #e8e8e8 100%);
            border-radius: 12px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #999;
            font-size: 28px;
            cursor: pointer;
            border: 3px dashed #ccc;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }
        .no-image:hover {
            background: linear-gradient(135deg, #fff8f0 0%, #ffe8cc 100%);
            border-color: var(--gold);
            box-shadow: 0 4px 12px rgba(212, 175, 55, 0.2);
        }
        .no-image i {
            margin-bottom: 4px;
        }
        .no-image::after {
            content: 'No Image';
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #888;
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
            padding: 6px 14px;
            border: none;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-action i {
            font-size: 11px;
        }
        .btn-edit {
            background: #17a2b8;
            color: white;
        }
        .btn-edit:hover {
            background: #138496;
            transform: translateY(-1px);
            box-shadow: 0 2px 6px rgba(23, 162, 184, 0.3);
        }
        .btn-save {
            background: #28a745;
            color: white;
        }
        .btn-save:hover {
            background: #218838;
            transform: translateY(-1px);
            box-shadow: 0 2px 6px rgba(40, 167, 69, 0.3);
        }
        .btn-cancel {
            background: #6c757d;
            color: white;
        }
        .btn-cancel:hover {
            background: #5a6268;
            transform: translateY(-1px);
            box-shadow: 0 2px 6px rgba(108, 117, 125, 0.3);
        }
        .btn-toggle {
            background: #ffc107;
            color: #212529;
        }
        .btn-toggle:hover {
            background: #e0a800;
            transform: translateY(-1px);
            box-shadow: 0 2px 6px rgba(255, 193, 7, 0.3);
        }
        .btn-featured {
            background: var(--gold);
            color: var(--deep-navy);
        }
        .btn-featured:hover {
            background: #c19b2e;
            transform: translateY(-1px);
            box-shadow: 0 2px 6px rgba(212, 175, 55, 0.3);
        }
        .btn-action[style*="#6f42c1"] {
            background: #6f42c1;
            color: white;
        }
        .btn-action[style*="#6f42c1"]:hover {
            background: #5a32a3;
            transform: translateY(-1px);
            box-shadow: 0 2px 6px rgba(111, 66, 193, 0.3);
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
                padding: 8px 10px;
                font-size: 13px;
            }
            .action-buttons {
                flex-direction: column;
                gap: 4px;
            }
            .btn-action {
                padding: 6px 12px;
                font-size: 11px;
                width: 100%;
                text-align: center;
                justify-content: center;
            }
            .rooms-section {
                padding: 12px;
                overflow-x: auto;
            }
            .room-table {
                min-width: 1800px;
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
                padding: 0;
            }
            .cell-view {
                padding: 8px 10px;
                min-height: 40px;
            }
            .room-table th {
                font-size: 10px;
            }
            .btn-action {
                padding: 5px 10px;
                font-size: 10px;
            }
        }
    </style>
</head>
<body>

    <?php include 'admin-header.php'; ?>

    <div class="content">
        <div class="page-header">
            <h2 class="page-title">Manage Hotel Rooms</h2>
        </div>

        <?php if ($message): ?>
            <?php showAlert($message, 'success'); ?>
        <?php endif; ?>

        <?php if ($error): ?>
            <?php showAlert($error, 'error'); ?>
        <?php endif; ?>

        <div class="rooms-section">
            <?php if (!empty($rooms)): ?>
                <table class="room-table">
                    <thead>
                        <tr>
                            <th style="width: 150px;">Image</th>
                            <th style="width: 80px;">Order</th>
                            <th style="width: 200px;">Room Name</th>
                            <th style="width: 250px;">Short Desc</th>
                            <th style="width: 120px;">Price/Night</th>
                            <th style="width: 80px;">Size</th>
                            <th style="width: 80px;">Guests</th>
                            <th style="width: 140px;">Availability</th>
                            <th style="width: 150px;">Bed Type</th>
                            <th style="width: 250px;">Amenities</th>
                            <th style="width: 120px;">Status</th>
                            <th style="width: 350px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rooms as $room): ?>
                            <tr id="row-<?php echo $room['id']; ?>">
                                <td class="image-cell">
                                    <?php if (!empty($room['image_url'])): ?>
                                        <?php $imgSrc = preg_match('#^https?://#i', $room['image_url']) ? $room['image_url'] : '../' . $room['image_url']; ?>
                                        <img src="<?php echo htmlspecialchars($imgSrc); ?>" 
                                         alt="<?php echo htmlspecialchars($room['name'], ENT_QUOTES); ?>" 
                                             class="room-image-preview" 
                                         onclick="openImageModal(<?php echo $room['id']; ?>, '<?php echo htmlspecialchars($room['name'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($room['image_url'], ENT_QUOTES); ?>')">
                                    <?php else: ?>
                                        <div class="no-image" onclick="openImageModal(<?php echo $room['id']; ?>, '<?php echo htmlspecialchars($room['name'], ENT_QUOTES); ?>', '')">
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
                                    <span class="cell-view"><?php echo htmlspecialchars(getSetting('currency_symbol')); ?> <?php echo number_format($room['price_per_night'], 0); ?></span>
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
                                    <div class="cell-edit" style="display: flex; gap: 10px; align-items: center; height: 100%;">
                                        <input type="number" min="0" style="width: 80px;" value="<?php echo $room['rooms_available'] ?? 0; ?>" data-field="rooms_available" title="Available">
                                        <span style="color: #666; font-weight: 500;">/</span>
                                        <input type="number" min="1" style="width: 80px;" value="<?php echo $room['total_rooms'] ?? 0; ?>" data-field="total_rooms" title="Total">
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
                                        <button class="btn-action" style="background: #6f42c1; color: white;" onclick="openImageModal(<?php echo $room['id']; ?>, '<?php echo htmlspecialchars($room['name'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($room['image_url'] ?? '', ENT_QUOTES); ?>')">
                                            <i class="fas fa-image"></i> Image
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
                        <i class="fas fa-upload"></i> Upload
                    </button>
                </div>
            </form>
        </div>
    </div>


    <script>
        let currentEditingId = null;

        function escapeHtml(str) {
            if (str === undefined || str === null) return '';
            return String(str)
                .replace(/&/g, '&')
                .replace(/</g, '<')
                .replace(/>/g, '>')
                .replace(/"/g, '"')
                .replace(/'/g, '&#39;');
        }

        // Image Modal Functions
        function openImageModal(roomId, roomName, currentImageUrl) {
            document.getElementById('modalRoomName').textContent = roomName + ' - Image';
            document.getElementById('uploadRoomId').value = roomId;
            
            if (currentImageUrl && currentImageUrl !== '') {
                const resolved = /^https?:\/\//i.test(currentImageUrl) ? currentImageUrl : ('../' + currentImageUrl);
                document.getElementById('currentImage').src = resolved;
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

        // Auto-submit featured image upload on file selection (keeps modal open)
        document.getElementById('roomImageInput').addEventListener('change', function() {
            const form = document.getElementById('imageUploadForm');
            performImageFormAjax(form);
        });

        // Image modal AJAX handling - keep featured-image modal open after upload
        function performImageFormAjax(form) {
            const formData = new FormData(form);
            fetch(window.location.href, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(async response => {
                if (response.redirected) {
                    window.location.href = response.url;
                    return null;
                }
                const ct = response.headers.get('content-type') || '';
                if (!ct.includes('application/json')) {
                    return null;
                }
                // try to parse JSON response from server
                return response.json().catch(() => null);
            })
            .then(data => {
                if (data && data.success) {
                    // Update featured image preview in modal
                    const currentImage = document.getElementById('currentImage');
                    if (currentImage) {
                        const _src = /^https?:\/\//i.test(data.image_url) ? data.image_url : ('../' + data.image_url);
                        currentImage.src = _src + '?t=' + Date.now();
                        document.getElementById('currentImageContainer').style.display = 'block';
                    }
                    // Also update the featured image preview in the table row
                    const roomId = document.getElementById('uploadRoomId').value;
                    const row = document.getElementById('row-' + roomId);
                    if (row) {
                        let tableImg = row.querySelector('.room-image-preview');
                        if (tableImg) {
                            const _src2 = /^https?:\/\//i.test(data.image_url) ? data.image_url : ('../' + data.image_url);
                            tableImg.src = _src2 + '?t=' + Date.now();
                            // Ensure subsequent clicks open modal with the new image URL
                            const name = row.querySelector('.cell-view strong')?.textContent || 'Room';
                            tableImg.onclick = () => openImageModal(roomId, name, data.image_url);
                        } else {
                            const cell = row.querySelector('td:first-child');
                            if (cell) {
                                cell.innerHTML = '';
                                const imgEl = document.createElement('img');
                                imgEl.className = 'room-image-preview';
                                imgEl.alt = (row.querySelector('.cell-view strong')?.textContent || 'Room');
                                const _src3 = /^https?:\/\//i.test(data.image_url) ? data.image_url : ('../' + data.image_url);
                                imgEl.src = _src3 + '?t=' + Date.now();
                                imgEl.addEventListener('click', () => {
                                    const name = row.querySelector('.cell-view strong')?.textContent || 'Room';
                                    openImageModal(roomId, name, data.image_url);
                                });
                                cell.appendChild(imgEl);
                            }
                        }
                    }
                    form.reset();
                } else {
                    Alert.show(data && data.message ? data.message : 'Error uploading image', 'error');
                }
            })
            .catch(err => {
                console.error('Error uploading featured image:', err);
                Alert.show('Error uploading image', 'error');
            });
        }

        // Intercept featured image form submit and use AJAX so modal remains open
        document.getElementById('imageUploadForm').addEventListener('submit', function(e) {
            e.preventDefault();
            performImageFormAjax(this);
        });

        // Close modal on outside click
        document.getElementById('imageModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeImageModal();
            }
        });

        // Drag and drop functionality (featured image area)
        document.querySelectorAll('.upload-area').forEach(area => {
            area.addEventListener('dragover', (e) => {
                e.preventDefault();
                area.classList.add('dragover');
            });

            area.addEventListener('dragleave', () => {
                area.classList.remove('dragover');
            });

            area.addEventListener('drop', (e) => {
                e.preventDefault();
                area.classList.remove('dragover');
                
                const files = e.dataTransfer.files;
                if (!files || files.length === 0) return;

                const form = area.closest('form');
                if (!form) return;

                const input = form.querySelector('input[type="file"]');
                if (input) {
                    input.files = files;
                }
            });
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
                    Alert.show('Error saving room', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Alert.show('Error saving room', 'error');
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
                    Alert.show('Error toggling status', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Alert.show('Error toggling status', 'error');
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
                    Alert.show('Error toggling featured status', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Alert.show('Error toggling featured status', 'error');
            });
        }
    </script>
</body>
</html>
