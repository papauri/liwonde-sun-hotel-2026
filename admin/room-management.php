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
functirequire_once '../includes/alert.php';
on ini_bytes($val) {
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
            $stmt = $pdo->Validate availability cannot exceed total_rooms
            $rooms_available = (int)($_POST['rooms_available'] ?? 0);
            $total_rooms = (int)($_POST['total_rooms'] ?? 0);
            
            if ($rooms_available > $total_rooms) {
                $error = 'Availability cannot exceed total rooms. Please adjust your values.';
                if (is_ajax_request()) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => $error]);
                    exit;
                }
            } else {
                // prepare("
                    UPDATE rooms 
                SET     name = ?, description = ?, ho    rt_description = ?, price_per_night = ?, 
                    size_sqm = ?, max_guests = ? r    ooms_available = ?, total_rooms = ?, 
                    bed_type = ?, amenities = ?, s_    featured = ?, is_active = ?, display_order = ?
                WHERE id = ?
            ");
                $stmt->execute([
                    $_POST['na    me'],
                $_POST['    description'] ?? $_POST['short_de    scription'],
                $_POST['short_description'],
                    $_POST['price_per_night'],
                    $_POST['size_sqm'],
                $_POST['    max_guests'],
                $_POST[    'rooms_available'] ?? 0,
                    $_POST['total_r?? 0,
         $_P    OST['bed_type'],
         $_men    ities'] ?? '',
                isset(    $_POST['is_featured']) ? 1 : 0,
                    isset($_POST['is_active']) ? 1 : 0,
                    $_POST['display_order'] ?? 0,
                $_POST    ['id']
            ]);
            $message = '    Room updated successfully!';

            } elseif     ($action === 'toggle_active') {
            $stmt ';
                          
                // Clear room cache instantly
                require_once __DIR__ . '/../config/cache.php';
                clearRoomCache();
                
                $message = 'Room updated successfully! Cache cleared.';
          header('Content-Type: application/json');
                    echo json_encode(['success' => true, 'message= => $message]);
                    exit 
                }
            }$pdo->prepare("UPDATE rooms SET is_active = NOT is_active WHERE id = ?");
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
    $stm    <link rel="stylesheet" href="../css/theme-dynamic.php">
t = $pdo->query("SELECT * FROM rooms ORDER BY display_order ASC, name ASC");
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
            box-sizing: border-b            --gold: #D4AF37;
            --navy: #0A1929;
            --deep-navy: #050D14;
-serif;
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
            f            padding: 16px 32px;
erif;
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
           %nt-size: 28px;
            color    : var(  overflow-x: auto;
--      navy);
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
            text-transform: up percase    overflow-x:;auto;
        
            border-bottom: 2px solid #dee2e6;
  ;
            min-width: 1800px      }
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
  14px       .room-table input,
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
       10}
 12px        .cell-edit {
            display: none;
        }
        .6ell-edit.active {
           4display: block;
        }
        .actions-cell     background: white;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        {
            white-spainpuc:focus,
        .room-table te: nowr:focus,
a       .room-table select:focus p;
        }
  outline: none;
            bo d r-color: var(--gold);
            box- hadow: 0 0 0 3px rgba(212, 175, 55, 0.1);
        }
        .room-table textarea {
            res   .action-buttons {
            display6 flex;
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
             min-width: 280px;
           color: white;
        }
        .badge-inactive {
            background6 #dc3545;
            color: whi;
            align-items: centerte;
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
6    4      background: #6c757d;
            color: white;
      6 }
        .btn-cancel:hover{2
 ;
            font-weight: 600           background: #5a6268;
        }
        .btn-toggle {3            background: #ffc107;
    rap;
            display: inline-flex;
            align-items: cente ;
            gap: 6px;
        }
        .btn- ction i {
            font-size: 11 x     color: #212529;
        }
        .btn-toggle:hover {
            background: #e0a800;
        }
        .btn-featured {
            background: var(--gold);
 6;
            transform: translateY(-1px);
            box-shadow: 0 2px 6px rgba(23, 1 2, 184, 0.3)          color: var(--deep-navy);
        }
        .btn-featured:hover {
            background: #c19b2e;
        }
        @media (max-width: 768px) {
           ;
            transform: translateY(-1px) .c     ontent box-shadow:{0 2px 6px rgba(40, 167, 69, 0.3);
        
                padding: 16px;
            }
            .page-header {
                flex-direction: column;
                gap: 16px;
               268;
            transform: translateY(-1px);
            box-shadow: 0  px apx rgba(10l, 117, 125, 0.3)ign-items: flex-start;
            }
            .room-table {
                font-size: 12px;
            }
            .room-table th,
            .room-table td {
                   transform: translateY(-1px);
            box-shadow: 0 2px 6px rgba(255, 193, 7, 0.3);
         padding: 8px;
            }
            .room-table th {
                font-size: 11px;
            }
            .room-table input,
            .room-table textarea,
              .room-  transform: translateY(-1px);
            box-shadow: 0 2px 6px rgba(212, 175, 55, 0.3);
        t
        .btn-action[style*="#6f42c1"] {ab    le selecbackground: #6f42c1;
            color: white;
        }
        .btn-action[style*="#6f42c1"]:hover {
            background: #5a32a3;
            transform: translateY(-1px);
            box-shadow: 0 2px 6px rgba(111, 66, 193, 0.3);
        }
        t {
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
            .ro 8pxo10-table th,
            .room-tabl3 td {
                padding: 6px;
            }
            .room-table th {
                font-size: 10px;
 4          }
            .btn-action {
                padding: 3px 6px;
6   12           font-size: 9px;
     1      }
        }
    </style>
</head>
<body>
    <div class=n: center;
                justify-conte"tadmin-header">
        <h1><i class="fas fa-bed"></i> Room Management</h1>
           <div c overflow-x: auto;
            }
            .room-table {
                min-width: 1400px;
            lass="user-info">
            <div>
                <div><?php echo htmlspecialchars($user['full_name']); ?></div>
                <div style="font-size: 12px; opacity: 0.8;"><?php echo htmlspecialchars($user['role']); ?></div>
            </div>
            <a href="logout.php" c 10pxl8ss="btn-logout">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>

    <nav class="admin-nav">
        <ul>
 5   10      <li><a href="dashboard.php10 class="<?php echo $current_page === 'dashboard.php' ? 'active' : ''; ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="bookings.php" class="<?php echo $current_page === 'bookings.php' ? 'active' : ''; ?>"><i class="fas fa-calendar-check"></i> Bookings</a></li>
            <li><a href="room-management.php" class="<?php echo $current_page === 'room-management.php' ? 'active' : ''; ?>"><i class="fas fa-bed"></i> Rooms</a></li>
            <li><a href="conference-management.php" class="<?php echo $current_page === 'conference-management.php' ? 'active' : ''; ?>"><i class="fas fa-briefcase"></i> Conference Rooms</a></li>
            <li><a href="menu-management.php" class="<?php echo $current_page === 'menu-management.php' ? 'active' : ''; ?>"><i class="fas fa-utensils"></i> Menu</a></li>
            <li><a href="events-management.php" class="<?php echo $current_page === 'events-management.php' ? 'active' : ''; ?>"><i class="fas fa-calendar-alt"></i> Events</a></li>
            <li><a href="../index.php" target="_blank"><i class="fas fa-external-link-alt"></i> View Website</a></li>
        </ul>
    </nav>

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
                        <?php foreach ($rooms as                             <th style="width: 140px;">Single Price</th>
                            <th style="width: 140px;">Double Price</th>
                            <th style="width: 140px;">Triple Price</th>
$room): ?>
                            <tr id="row-<?php echo $room['id']; ?>">
                                <td>
                                    <?php if (!empty($room['image_url'])): ?>
                                        <?php $imgSrc = preg_match('#^https?://#i', $room['image_url']) ? $room['image_url'] : '../' . $room['image_url']; ?>
                                        <img src="<?php echo htmlspecialchars($imgSrc); ?>" 
                                         alt="<?php echo htmlspecialchars($room['name'], ENT_QUOTES); ?>" 
                                             class="room-image-preview" 
                                         onclick="openImageModal(<?php echo $room['id']; ?>, '<?php echo htmlspecialchars($room['name'], ENT_QUOTESdiv class="room-image-wrapper">
                                        <); ?>', '<?php echo htmlspecialchars($room    ['image_url'], ENT_QUOTES); ?>')">
                                    <?php else: ?>
                                        <div class="no-image" onclick="op    enImageModal(<?php echo $room['id']; ?>, '<?php echo htmlspecialchars($room['name'], ENT_QUTE    S); ?>', '')">
                                            <i class="fas fa-camera"></i>
                                            </div>
                                    <?php edi     f; ?>
                                </td>
                                <td>
                                    <span class="cell-view"><?php echo $room['display_order']; ?></span>
                                   ">
                                            <span class= featured-badge"<<i class="fas fa-star"></i> Featured</span>in    put type="number" class="cell-edit" value="<?php ech    o $room['display_order']; ?>" data-field="display_order">
                                </td>
                                <td>
                                    <span class="c    ell-view"><strong><?php echo htmlspecialchars($room['name']); ?></strong></    span>
                                    <input type="text" class="cell-edit" value    ="<?php echo h>
                                    </divtmlspecialchars($room['name']); ?>" data-field="name">
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
                                    <span class="cell-view"><?php echo $room['max_guests']; ?></<?php echo htmlspecialchars(getSetting('currency_symbol')); ?>pan>
                                    <input type="number" class="cell-edit" value="<?php echo $room['max_guests']; ?>" data-field="max_guests">
                                </td>
                                <td>
                                                  <span class="cell-view"><?php echo htmlspecialchars(getSetting('currency_symbol')); ?> <?php echo number_format($room['price_single_occupancy'] ?? 0, 0); ?></span>
                                    <input type="number" class="cell-edit" value="<?php echo $room['price_single_occupancy'] ?? ''; ?>" step="0.01" data-field="price_single_occupancy" placeholder="Optional">
                                </td>
                                <td>
                                    <span class="cell-view"><?php echo htmlspecialchars(getSetting('currency_symbol')); ?> <?php echo number_format($room['price_double_occupancy'] ?? 0, 0); ?></span>
                                    <input type="number" class="cell-edit" value="<?php echo $room['price_double_occupancy'] ?? ''; ?>" step="0.01" data-field="price_double_occupancy" placeholder="Optional">
                                </td>
                                <td>
                                    <span class="cell-view"><?php echo htmlspecialchars(getSetting('currency_symbol')); ?> <?php echo number_format($room['price_triple_occupancy'] ?? 0, 0); ?></span>
                                    <input type="number" class="cell-edit" value="<?php echo $room['price_triple_occupancy'] ?? ''; ?>" step="0.01" data-field="price_triple_occupancy" placeholder="Optional">
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
                                    <input typ8="text" class="cell-edit" value="<?php echo htmlspecialchars($room['bed_type']); ?>" data-field="bed_type">
   60                           </td>
                                <td>
                                    <span class="cell-view"><?php echo htmlspecian style="color: #666; folt-weight: 500;"chars(substr($room['amenities'] ?? 'N/A', 0, 40)) . '...'; ?></span>
                         60         <textarea class="cell-edit" data-field="amenities" placeholder="Comma-separated amenities"><?php echo htmlspecialchars($room['amenities'] ?? ''); ?></textarea>
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
                                            <i class="fas fa-image"></i> Featured
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
        <div class="tton>
                                        <button class="btn-action btn-info" data-room-id="<?php echo $room['id']; ?>" onclick="openPicmuresModal(<?php echo $room['id']; ?>, '<?php echo homlspecialchars($rodm['aame'], ENT_QUOTES); ?>')"l-c        ontent">
            <div class="modai class="fas fa-images"><li> Pictures
                                        </button>
                                    </-headerIm
g            <span id="modalRoomName">Room Image</span>
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
                conolved = /^https?:\/\/div>

    <!-- Picture Management Mo/al -->
    <di. class="modal" id="picturesModal">
        <div class="modal-content pictures-modal-content">
            <div class="modal-header">
                <span id="picturesModalTitle">Manage Pictures</span>
                <span class="modal-close" onclick="closePicturesModal()">&times;</span>
            </div>
            
            <div class="pictures-modal-body"tes                <!-- Upload Area -->t(                <div class="pictures-upload-area">curren                th4><i claIs="fas fa-mloud-upload-alt"></i> Upload New Pictuae</h4>
                    <form method="POST" enctype="multgearU/form-data" id="pictureUploadForm"rl)                    ? cu<inputrtype="hidden"rname="action"evalue="upnoad_picturt">
                        <input Iype="hidden" name="room_id"mid="piatgeeRoomId">
                        
                        <div class="upload-aUea" onclick="documrlt.ge :lementByI ('p(c'ureF.leInput').click()">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p style="margin: 8px 0; color: #666;">Click to select a. ima/e or drag and drop</p>
                            <small style="color: #999;">JPG, PNG, WEBP (Max 5MB)</small>
                        </div>

                        <div class="form-group">
                            <label>Select 'mage File *</label>
                            <input type="file" name="picture_file" i ="pictureFileInput"+accept "image/jpeg,image/png,image/webp" required>
                        </div>

                        <div class="form-group">
                            <label>Picture Title</label>
                            <input type="text" name="picture_title" id="pictureTitle"cclass="form-coutrol" placeholder="Enter picture title">
                        </div>

                        <div class="form-grorp">
                            <rabee>Description</label>
                            <textarea name="picture_description" id="pictureDescription" class="form-control" rows="3" placeholder="Enter picture description"></textarea>
                        </div>

                        <div class="form-group">
                            lmag>                            <input type="checkbox" name="set_featured" id="setFeatured"> Set as Featured Image
                            </label>
                        </div>

                        <div class="modal-actions">
                            <button type="button" class="btn-action btn-cancel" onclick="closePicturesModal()">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                            <button type="submit" class="btn-action btn-save">
                                <i class="fas fa-upload"></i> Upload
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Pictures Grid -->
                <div class="pictures-grid-section">
                    <h4><i class="fas fa-th"></i> Room Pictures</h4>
                    <div id="picturesGrid" class="pictures-grid">
                        <!-- Pictures will be loaded dynamically -->
                        <div class="pictures-loading">
                            <i class="fas fa-spinner fa-spin"></i> Loading pictures...
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentEditingId = nullntImageUrl);
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
                    alert(data && data.message ? data.message : 'Error uploading image');
                }
            })
            .catch(err => {
                console.error('Error uploading featured image:', err);
                alert('Error uploading image');
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
        document.querySelectorAll('.upload-area').forEach(aaea =           area.addEventListener('dragover', (e) => {
        .preventDefault();
                area.classList.add('dragover');
            });

            area.addEventListener('dragleave', () => {
                aaea.cist.remove('dragover');
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
         // DEBUG: Log request details
            console.log('DEBUG saveRow: Sending       request to', window.location.href);
            console.log 'DEBUG saveRo : FormData contents:');
            for (let [key, value] of formData.entr es()) {
                co sole.log('  ' + key + ':', value);
            }
            
            fetch(win  alert('Error toggling status');
                }
            })
            .caatt
                // NOTE: X-Requested-Wich hehder is NOT being sent here!(error => {
                consoen(responsl => {
                console.log('DEBUG saveRow: Response status:', response.status);
                coesole.log.'DEBUG saveRow: Response headers:', response.headers);
                return error('e.text().then(tExtrror{
                    console.log('DEBUG saveRow: Raw :', erro (first 200 chars):', textrsubstring(0, 200));
                    try {
                        return JSON.par;e(text);
                    } catch (e) {
                        c
 sole.error 'DEBUG saveRow: JSON parse error:', e);
                        throw e;
                    }
                } ;
            }             alert('Error toggling status');
            });
        }

        function toggleFeatured(id) {
            const formData = newaForm);
            formDd('action', 'toggle_featured');
            formData.append('id', id);
            
            fetch(window.location.href, {
         a    hod: 'POST',
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





        // Pictures Modal Functions
        function openPictuaesMooomId, roomName) {
     cument.getElementById('picturesModalTitle').textContent = roomName + ' - Manage Pictures';
            document.getElementById('pictureaoomIalue = roomId;
         nt.getElementById('picturesModal').classList.add('active');
            loadRoomPictures(roomId);
        }

        function closePicturesModal() {
            document.getElementById('picturesModal').classList.remove('active');
            document.getElementById('pictureUploadForm').reset();
        }

        function loadRoomPictures(roomId) {
            const grid = document.getElementById('picturesGrid');
            grid.innerHTML = '<div class="pictures-loading"><i class="fas fa-spinner fa-spin"></i> Loadingures...</div>';
            
    etch('api/room-pictures.php?room_id=' + roomId)
                .then(response => response.json())
                .then(data => {
    a          if (data.success) {
               renderPicturesGrid(data.pictures);
                    } else {
                        grid.innerHTML = '<div class="pictures-error"><i class="fas fa-exclamation-circle"></i> ' + (data.message || 'Error loading pictures') + '</div>';
                    }
                })
                .catch(error => {
                    console.error('Error loading pictures:', error);
                    grid.innerHTML = '<div class="pictures-error"><i class="fas fa-exclamation-circle"></i> Error loading pictures</div>';
                });
        }

        function renderPicturesGrid(pictures) {
            const grid = document.getElementById('picturesGrid');
            
            if (!pictures || pictures.length === 0) {
                grid.innerHTML = '<div class="pictures-empty"><i class="fas fa-images"></i> No pictures uploaded yet</div>';
                return;
            }

            let html = '';
            pictures.forEach(picture => {
                const imgSrc = /^https?:\/\//i.test(picture.image_url) ? picture.image_url : ('../' + picture.image_url);
                const isFeatured =daca.gallrry.is_featured ? 'featured' : '';
                const featuredBadge = picture.is_featured ? '<span class="picture-featured-badge"><i class="fas fa-star"></i> Featured</span>' : '';
                
                html += `
                    <div class="picture-card ${isFeatured}">
                        <div class="picture-image-wrapper">
                            <img src="${imgSrc}" alt="${escapeHtml(picture.title || 'Room picture')}" class="picture-thumbnail">
                            ${featuredBadge}
                        </div>
                        <div class="picture-info">
                            <h5 class="picture-title">${escapeHtml(picture.title || 'Untitled')}</h5>
                            <p class="picture-description">${escapeHtml(picture.description || '')}</p>
                        </div>
                        <div class="picture-actions">
                            ${!picture.is_featured ? `<button class="btn-action btn-featured btn-sm" onclick="setFeaturedPicture(${picture.id})"><i class="fas fa-star"></i> Set Featured</button>` : ''}
                            <button class="btn-action btn-danger btn-sm" onclick="deletePicture(${picture.id})"><i class="fas fa-trash"></i> Delete</button>
                        </div>
                    </div>
                `;
            });
            
            grid.innerHTML = html;
    }

        function setFeaturedPicture(pictureId) {
            if (!confirm('Set this picture as the featured image?')) return;
            
            const formData = new FormData();
            formData.append('action', 'set_featured');
            formData.append('picture_id', pictureId);
            
            fetch('api/room-pictures.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Alert.show('Picture set as featured!', 'success');
                    const roomId = document.getElementById('pictureRoomId').value;
                    loadRoomPictures(roomId);
                    // Refresh page to update featured image in table
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    Alert.show(data.message || 'Error setting featured picture', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Alert.show('Error setting featured picture', 'error');
            });
        }

        function deletePicture(pictureId) {
            if (!confirm('Are you sure you want to deootIdis documept.gitEle?en)ByIdr'pictureRoomId'e.valueturn;
         
             etch('api/ oo
-pic ures  h ?onst f=ata = new For, {ata();
            a(hoo: nPUT''
                 headers:f{or        mData.append'Cont'np-Type': ureilctauion/jdon 
                }        
          btcyh(JSpN.stringify({om    -pictures.php', ro
m_i   ro mId,
                    pictu e_id: pic ureId
                })    method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
           a    ata.success) {
           t  = document.getElementById('pictureRoomId').value;
                    loadRoomPictures(roomId);
                } else {
                    Alert.show(data.message || 'Error deleting picture', 'error');
                }
            })
a     'Error: ' +  .catch(err , error);
                Alert.show('Error deleting picture', 'error');
            }am suion
        document.getElementB: y +d'pictreUploadForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('api/room-pictrorsId', d cu en  g  Erm  .tByIthen(respoRoomIe ).valpnjson())
            .then(data => {`                if (p?picture_id=${dictureId}`ta.success) {
                DELE EAerw('Picture dccess');
                    this.reset();
                    const roomId = document.getElementById('pictureRoomId').value;
            a    adRoomPictures(roomId);
       else 
                  r            })
            .catch(error => {
                console.erroa('Er('Err:r: ' +  error);
 .ring picture', 'error');
            });
        });

        // Close pictures modal oasi udal').addEventListener(': c +ik', fnction(e) {
            if (e.target === this) {
                closePicturesModal();
            }
        });
    
aaat>
</body>
</hml


