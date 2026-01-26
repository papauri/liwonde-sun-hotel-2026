<?php
session_start();

if (!isset($_SESSION['admin_user'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';
require_once '../includes/alert.php';

$user = $_SESSION['admin_user'];
$message = '';
$error = '';
$current_page = basename($_SERVER['PHP_SELF']);

function uploadConferenceImage(array $fileInput): ?string
{
    if (empty($fileInput) || ($fileInput['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return null;
    }

    $uploadDir = __DIR__ . '/../images/conference/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $extension = strtolower(pathinfo($fileInput['name'], PATHINFO_EXTENSION)) ?: 'jpg';
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    if (!in_array($extension, $allowed, true)) {
        return null;
    }

    $filename = 'conference_' . time() . '_' . random_int(1000, 9999) . '.' . $extension;
    $destination = $uploadDir . $filename;

    if (move_uploaded_file($fileInput['tmp_name'], $destination)) {
        return 'images/conference/' . $filename;
    }

    return null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $action = $_POST['action'] ?? '';
        $imagePath = uploadConferenceImage($_FILES['image'] ?? []);

        if ($action === 'add') {
            $stmt = $pdo->prepare("
                INSERT INTO conference_rooms (
                    name, description, capacity, size_sqm, hourly_rate, daily_rate,
                    amenities, image_path, is_active, display_order
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $_POST['name'],
                $_POST['description'],
                $_POST['capacity'],
                $_POST['size_sqm'] ?: null,
                $_POST['hourly_rate'],
                $_POST['daily_rate'],
                $_POST['amenities'] ?? '',
                $imagePath,
                isset($_POST['is_active']) ? 1 : 0,
                $_POST['display_order'] ?? 0
            ]);
            $message = 'Conference room added successfully!';
        }

        if ($action === 'update') {
            if ($imagePath) {
                $stmt = $pdo->prepare("
                    UPDATE conference_rooms
                    SET name = ?, description = ?, capacity = ?, size_sqm = ?, hourly_rate = ?, daily_rate = ?,
                        amenities = ?, image_path = ?, is_active = ?, display_order = ?
                    WHERE id = ?
                ");
                $stmt->execute([
                    $_POST['name'],
                    $_POST['description'],
                    $_POST['capacity'],
                    $_POST['size_sqm'] ?: null,
                    $_POST['hourly_rate'],
                    $_POST['daily_rate'],
                    $_POST['amenities'] ?? '',
                    $imagePath,
                    isset($_POST['is_active']) ? 1 : 0,
                    $_POST['display_order'] ?? 0,
                    $_POST['id']
                ]);
            } else {
                $stmt = $pdo->prepare("
                    UPDATE conference_rooms
                    SET name = ?, description = ?, capacity = ?, size_sqm = ?, hourly_rate = ?, daily_rate = ?,
                        amenities = ?, is_active = ?, display_order = ?
                    WHERE id = ?
                ");
                $stmt->execute([
                    $_POST['name'],
                    $_POST['description'],
                    $_POST['capacity'],
                    $_POST['size_sqm'] ?: null,
                    $_POST['hourly_rate'],
                    $_POST['daily_rate'],
                    $_POST['amenities'] ?? '',
                    isset($_POST['is_active']) ? 1 : 0,
                    $_POST['display_order'] ?? 0,
                    $_POST['id']
                ]);
            }
            $message = 'Conference room updated successfully!';
        }

        if ($action === 'delete') {
            $stmt = $pdo->prepare("DELETE FROM conference_rooms WHERE id = ?");
            $stmt->execute([$_POST['id']]);
            $message = 'Conference room deleted successfully!';
        }
    } catch (PDOException $e) {
        $error = 'Error: ' . $e->getMessage();
    }
}

try {
    $stmt = $pdo->query("SELECT * FROM conference_rooms ORDER BY display_order ASC, name ASC");
    $conference_rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $conference_rooms = [];
    $error = 'Error fetching conference rooms: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conference Rooms - Admin Panel</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin-styles.css">
    
    <style>
        /* Conference management specific styles */
        .card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
            margin-bottom: 24px;
        }
        .card h2 {
            margin: 0 0 16px;
            color: var(--navy);
        }
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 16px;
        }
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        label {
            font-weight: 600;
            color: var(--navy);
            font-size: 14px;
        }
        input,
        textarea {
            padding: 10px 12px;
            border-radius: 6px;
            border: 1px solid #d9d9d9;
            font-size: 14px;
        }
        textarea {
            min-height: 120px;
            resize: vertical;
        }
        .checkbox-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 12px;
        }
        .btn {
            background: var(--gold);
            color: var(--deep-navy);
            border: none;
            padding: 10px 18px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .room-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
            gap: 24px;
        }
        .room-item {
            background: white;
            border: 1px solid #e8e8e8;
            border-radius: 16px;
            padding: 0;
            display: flex;
            flex-direction: column;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
            overflow: hidden;
        }
        .room-item:hover {
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
            transform: translateY(-2px);
            border-color: var(--gold);
        }
        .room-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 20px;
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            border-bottom: 1px solid #e8e8e8;
        }
        .room-header h3 {
            margin: 0;
            color: var(--navy);
            font-size: 18px;
            font-weight: 700;
        }
        .badge {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .badge-active {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
        }
        .badge-inactive {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3);
        }
        .room-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            font-size: 13px;
            color: #666;
            padding: 12px 20px;
            background: #fafbfc;
            border-bottom: 1px solid #e8e8e8;
        }
        .room-meta span {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .room-meta i {
            color: var(--gold);
            font-size: 14px;
        }
        .room-image-container {
            width: 100%;
            height: 180px;
            overflow: hidden;
            background: #f8f9fa;
            border-bottom: 1px solid #e8e8e8;
            position: relative;
        }

        .room-image-preview {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .room-item:hover .room-image-preview {
            transform: scale(1.05);
        }

        .room-image-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #bbb;
            font-size: 48px;
            background: linear-gradient(135deg, #f0f2f5 0%, #e4e6e9 100%);
        }

        .room-form-section {
            padding: 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        .room-form-section .form-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }
        .room-form-section label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #888;
        }
        .room-form-section input,
        .room-form-section textarea {
            padding: 8px 12px;
            font-size: 13px;
            border: 1px solid #e0e0e0;
            background: #fafbfc;
            transition: all 0.2s ease;
        }
        .room-form-section input:focus,
        .room-form-section textarea:focus {
            border-color: var(--gold);
            background: white;
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.1);
        }
        .room-form-section textarea {
            min-height: 80px;
        }
        .room-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            padding: 16px 20px;
            background: #fafbfc;
            border-top: 1px solid #e8e8e8;
        }
        .room-actions .btn {
            flex: 1;
            min-width: 120px;
            padding: 10px 16px;
            font-size: 13px;
            transition: all 0.2s ease;
        }
        .room-actions .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(212, 175, 55, 0.3);
        }
        .room-actions .btn-secondary:hover {
            box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
        }
        .room-form-section .checkbox-row {
            margin-top: 0;
            padding: 8px 0;
        }
        .room-form-section .checkbox-row input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: var(--gold);
            cursor: pointer;
        }
        .room-form-section .checkbox-row label {
            font-size: 13px;
            text-transform: none;
            letter-spacing: normal;
            color: var(--navy);
            cursor: pointer;
        }
        @media (max-width: 768px) {
            .room-list {
                grid-template-columns: 1fr;
            }
            .room-form-section .form-grid {
                grid-template-columns: 1fr;
            }
            .room-actions {
                flex-direction: column;
            }
            .room-actions .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <?php include 'admin-header.php'; ?>

    <div class="content">
        <?php if ($message): ?>
            <?php showAlert($message, 'success'); ?>
        <?php endif; ?>
        <?php if ($error): ?>
            <?php showAlert($error, 'error'); ?>
        <?php endif; ?>

        <div class="card">
            <h2>Add New Conference Room</h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Name *</label>
                        <input type="text" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Capacity *</label>
                        <input type="number" name="capacity" min="1" required>
                    </div>
                    <div class="form-group">
                        <label>Size (sqm)</label>
                        <input type="number" step="0.01" name="size_sqm">
                    </div>
                    <div class="form-group">
                        <label>Hourly Rate *</label>
                        <input type="number" step="0.01" name="hourly_rate" required>
                    </div>
                    <div class="form-group">
                        <label>Daily Rate *</label>
                        <input type="number" step="0.01" name="daily_rate" required>
                    </div>
                    <div class="form-group">
                        <label>Display Order</label>
                        <input type="number" name="display_order" value="0">
                    </div>
                </div>
                <div class="form-group">
                    <label>Description *</label>
                    <textarea name="description" required></textarea>
                </div>
                <div class="form-group">
                    <label>Amenities (comma separated)</label>
                    <textarea name="amenities"></textarea>
                </div>
                <div class="form-group">
                    <label>Featured Image</label>
                    <input type="file" name="image" accept="image/*">
                </div>
                <div class="checkbox-row">
                    <input type="checkbox" name="is_active" id="is_active_add" checked>
                    <label for="is_active_add">Active</label>
                </div>
                <button type="submit" class="btn">Add Conference Room</button>
            </form>
        </div>

        <div class="card">
            <h2>Manage Conference Rooms</h2>
            <div class="room-list">
                <?php foreach ($conference_rooms as $room): ?>
                    <div class="room-item">
                        <div class="room-header">
                            <h3><?php echo htmlspecialchars($room['name']); ?></h3>
                            <span class="badge <?php echo $room['is_active'] ? 'badge-active' : 'badge-inactive'; ?>">
                                <?php echo $room['is_active'] ? '<i class="fas fa-check-circle"></i> Active' : '<i class="fas fa-times-circle"></i> Inactive'; ?>
                            </span>
                        </div>
                        
                        <!-- Image Display -->
                        <div class="room-image-container">
                            <?php if (!empty($room['image_path'])): ?>
                                <img src="../<?php echo htmlspecialchars($room['image_path']); ?>"
                                     alt="<?php echo htmlspecialchars($room['name']); ?>"
                                     class="room-image-preview">
                            <?php else: ?>
                                <div class="room-image-placeholder">
                                    <i class="fas fa-image"></i>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="room-meta">
                            <span><i class="fas fa-users"></i> <?php echo (int) $room['capacity']; ?> Guests</span>
                            <span><i class="fas fa-expand-arrows-alt"></i> <?php echo number_format($room['size_sqm'] ?? 0, 0); ?> sqm</span>
                            <span><i class="fas fa-coins"></i> K <?php echo number_format($room['hourly_rate'], 0); ?>/hr</span>
                            <span><i class="fas fa-calendar-day"></i> K <?php echo number_format($room['daily_rate'], 0); ?>/day</span>
                        </div>

                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="id" value="<?php echo (int) $room['id']; ?>">
                            <div class="room-form-section">
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label>Name *</label>
                                        <input type="text" name="name" value="<?php echo htmlspecialchars($room['name']); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Capacity *</label>
                                        <input type="number" name="capacity" min="1" value="<?php echo htmlspecialchars($room['capacity']); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Size (sqm)</label>
                                        <input type="number" step="0.01" name="size_sqm" value="<?php echo htmlspecialchars($room['size_sqm']); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Hourly Rate *</label>
                                        <input type="number" step="0.01" name="hourly_rate" value="<?php echo htmlspecialchars($room['hourly_rate']); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Daily Rate *</label>
                                        <input type="number" step="0.01" name="daily_rate" value="<?php echo htmlspecialchars($room['daily_rate']); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Display Order</label>
                                        <input type="number" name="display_order" value="<?php echo htmlspecialchars($room['display_order']); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Description *</label>
                                    <textarea name="description" required><?php echo htmlspecialchars($room['description']); ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Amenities (comma separated)</label>
                                    <textarea name="amenities"><?php echo htmlspecialchars($room['amenities']); ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Replace Image</label>
                                    <input type="file" name="image" accept="image/*">
                                </div>
                                <div class="checkbox-row">
                                    <input type="checkbox" name="is_active" id="is_active_<?php echo (int) $room['id']; ?>" <?php echo $room['is_active'] ? 'checked' : ''; ?>>
                                    <label for="is_active_<?php echo (int) $room['id']; ?>">Active</label>
                                </div>
                            </div>
                            <div class="room-actions">
                                <button type="submit" class="btn"><i class="fas fa-save"></i> Save Changes</button>
                                <button type="submit" name="action" value="delete" class="btn btn-secondary" onclick="return confirm('Delete this conference room?');">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </div>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>
</html>
