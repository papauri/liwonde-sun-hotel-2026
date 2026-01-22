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

// Handle menu item actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $action = $_POST['action'] ?? '';

        if ($action === 'add') {
            // Add new menu item
            $stmt = $pdo->prepare("
                INSERT INTO menu_items (name, description, price, category, is_active, item_order)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $_POST['name'],
                $_POST['description'],
                $_POST['price'],
                $_POST['category'],
                isset($_POST['is_active']) ? 1 : 0,
                $_POST['item_order'] ?? 0
            ]);
            $message = 'Menu item added successfully!';

        } elseif ($action === 'update') {
            // Update existing menu item
            $stmt = $pdo->prepare("
                UPDATE menu_items 
                SET name = ?, description = ?, price = ?, category = ?, is_active = ?, item_order = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $_POST['name'],
                $_POST['description'],
                $_POST['price'],
                $_POST['category'],
                $_POST['is_active'] ?? 1,
                $_POST['item_order'] ?? 0,
                $_POST['id']
            ]);
            $message = 'Menu item updated successfully!';

        } elseif ($action === 'delete') {
            // Delete menu item
            $stmt = $pdo->prepare("DELETE FROM menu_items WHERE id = ?");
            $stmt->execute([$_POST['id']]);
            $message = 'Menu item deleted successfully!';

        } elseif ($action === 'toggle_availability') {
            // Toggle availability
            $stmt = $pdo->prepare("UPDATE menu_items SET is_active = NOT is_active WHERE id = ?");
            $stmt->execute([$_POST['id']]);
            $message = 'Menu item availability updated!';
        }

    } catch (PDOException $e) {
        $error = 'Error: ' . $e->getMessage();
    }
}

// Fetch all menu items grouped by category
try {
    $stmt = $pdo->query("
        SELECT * FROM menu_items 
        ORDER BY category, item_order ASC, name ASC
    ");
    $menu_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Group by category
    $grouped_items = [];
    $categories = [];
    foreach ($menu_items as $item) {
        $grouped_items[$item['category']][] = $item;
        if (!in_array($item['category'], $categories)) {
            $categories[] = $item['category'];
        }
    }
    // Sort categories alphabetically
    sort($categories);

} catch (PDOException $e) {
    $error = 'Error fetching menu items: ' . $e->getMessage();
    $grouped_items = [];
    $categories = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Management - Admin Panel</title>
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
            max-width: 1400px;
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
        .btn-add {
            background: var(--gold);
            color: var(--deep-navy);
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
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
        .category-section {
            background: white;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }
        .category-header {
            font-size: 20px;
            font-weight: 700;
            color: var(--navy);
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 2px solid var(--gold);
        }
        .menu-table {
            width: 100%;
            border-collapse: collapse;
        }
        .menu-table th {
            background: #f8f9fa;
            padding: 12px;
            text-align: left;
            font-size: 13px;
            font-weight: 600;
            color: #666;
            text-transform: uppercase;
            border-bottom: 2px solid #dee2e6;
        }
        .menu-table td {
            padding: 12px;
            border-bottom: 1px solid #f0f0f0;
            vertical-align: middle;
        }
        .menu-table tbody tr {
            transition: background 0.2s ease;
        }
        .menu-table tbody tr:hover {
            background: #f8f9fa;
        }
        .menu-table tbody tr.edit-mode {
            background: #fff3cd;
        }
        .menu-table input,
        .menu-table textarea,
        .menu-table select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 13px;
            font-family: inherit;
            background: white;
            transition: all 0.2s ease;
        }
        .menu-table input:focus,
        .menu-table textarea:focus,
        .menu-table select:focus {
            border-color: var(--gold);
            outline: none;
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.1);
        }
        .menu-table textarea {
            resize: vertical;
            min-height: 60px;
        }
        tr.editing {
            background: rgba(212, 175, 55, 0.05);
            box-shadow: 0 0 0 2px rgba(212, 175, 55, 0.2);
        }
        tr.editing input,
        tr.editing textarea,
        tr.editing select {
            border-color: var(--gold);
            background: white;
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
            min-width: 120px;
        }
        .action-buttons {
            display: flex;
            gap: 6px;
            align-items: center;
            justify-content: flex-start;
        }
        .badge-available {
            background: #28a745;
            color: white;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }
        .badge-unavailable {
            background: #dc3545;
            color: white;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }
        .btn-action {
            min-width: 32px;
            height: 32px;
            padding: 0 8px;
            border: none;
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            position: relative;
            white-space: nowrap;
        }
        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .btn-action:active {
            transform: translateY(0);
        }
        .btn-edit {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
        }
        .btn-edit:hover {
            background: linear-gradient(135deg, #2980b9 0%, #21618c 100%);
        }
        .btn-save {
            background: linear-gradient(135deg, #27ae60 0%, #229954 100%);
            color: white;
        }
        .btn-save:hover {
            background: linear-gradient(135deg, #229954 0%, #1e8449 100%);
        }
        .btn-cancel {
            background: linear-gradient(135deg, #95a5a6 0%, #7f8c8d 100%);
            color: white;
        }
        .btn-cancel:hover {
            background: linear-gradient(135deg, #7f8c8d 0%, #707b7c 100%);
        }
        .btn-delete {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            color: white;
        }
        .btn-delete:hover {
            background: linear-gradient(135deg, #c0392b 0%, #a93226 100%);
        }
        .btn-toggle {
            background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
            color: white;
        }
        .btn-toggle:hover {
            background: linear-gradient(135deg, #e67e22 0%, #d35400 100%);
        }
        .btn-toggle.active {
            background: linear-gradient(135deg, #27ae60 0%, #229954 100%);
        }
        .btn-toggle.active:hover {
            background: linear-gradient(135deg, #229954 0%, #1e8449 100%);
        }
        .edit-mode {
            background: #fff3cd !important;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
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
        }
        .modal-header {
            font-size: 24px;
            font-weight: 700;
            color: var(--navy);
            margin-bottom: 24px;
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
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
        }
        .form-group textarea {
            resize: vertical;
            min-height: 80px;
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
            .page-header {
                flex-direction: column;
                gap: 16px;
                align-items: flex-start;
            }
            .btn-add {
                width: 100%;
                justify-content: center;
            }
            .menu-table {
                font-size: 12px;
            }
            .menu-table th,
            .menu-table td {
                padding: 8px;
            }
            .menu-table th {
                font-size: 11px;
            }
            .menu-table input,
            .menu-table textarea,
            .menu-table select {
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
            .category-section {
                padding: 16px;
            }
            .category-header {
                font-size: 16px;
                margin-bottom: 12px;
            }
        }
        @media (max-width: 480px) {
            .content {
                padding: 12px;
            }
            .menu-table {
                font-size: 11px;
            }
            .menu-table th,
            .menu-table td {
                padding: 6px;
            }
            .menu-table th {
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
        <h1><i class="fas fa-utensils"></i> Menu Management</h1>
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
            <li><a href="room-management.php"><i class="fas fa-bed"></i> Rooms</a></li>
            <li><a href="conference-management.php"><i class="fas fa-briefcase"></i> Conference Rooms</a></li>
            <li><a href="room-gallery-management.php"><i class="fas fa-images"></i> Room Gallery</a></li>
            <li><a href="menu-management.php" class="active"><i class="fas fa-utensils"></i> Menu</a></li>
            <li><a href="events-management.php"><i class="fas fa-calendar-alt"></i> Events</a></li>
            <li><a href="../index.php" target="_blank"><i class="fas fa-external-link-alt"></i> View Website</a></li>
        </ul>
    </nav>

    <div class="content">
        <div class="page-header">
            <h2 class="page-title">Restaurant Menu Items</h2>
            <button class="btn-add" onclick="openAddModal()">
                <i class="fas fa-plus"></i> Add Menu Item
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

        <?php foreach ($categories as $category): ?>
            <div class="category-section">
                <h3 class="category-header" style="display: flex; justify-content: space-between; align-items: center;">
                    <span>
                        <i class="fas fa-<?php 
                            echo $category === 'Breakfast' ? 'coffee' : 
                                ($category === 'Lunch' ? 'hamburger' : 
                                ($category === 'Dinner' ? 'drumstick-bite' : 
                                ($category === 'Beverages' ? 'glass-martini-alt' : 'ice-cream'))); 
                        ?>"></i>
                        <?php echo $category; ?>
                        <?php if (isset($grouped_items[$category])): ?>
                            <span style="font-size: 14px; font-weight: normal; color: #666;">
                                (<?php echo count($grouped_items[$category]); ?> items)
                            </span>
                        <?php endif; ?>
                    </span>
                    <button class="btn-add" onclick="openAddModal('<?php echo htmlspecialchars($category); ?>')" style="font-size: 12px; padding: 8px 16px;">
                        <i class="fas fa-plus"></i> Add Item
                    </button>
                </h3>

                <?php if (isset($grouped_items[$category]) && !empty($grouped_items[$category])): ?>
                    <table class="menu-table">
                        <thead>
                            <tr>
                                <th style="width: 5%;">Order</th>
                                <th style="width: 20%;">Item Name</th>
                                <th style="width: 35%;">Description</th>
                                <th style="width: 10%;">Price (K)</th>
                                <th style="width: 10%;">Status</th>
                                <th style="width: 20%;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($grouped_items[$category] as $item): ?>
                                <tr id="row-<?php echo $item['id']; ?>" data-category="<?php echo htmlspecialchars($item['category']); ?>">
                                    <td>
                                        <input type="number" value="<?php echo $item['item_order']; ?>" data-field="item_order">
                                    </td>
                                    <td>
                                        <input type="text" value="<?php echo htmlspecialchars($item['name']); ?>" data-field="name">
                                    </td>
                                    <td>
                                        <textarea data-field="description"><?php echo htmlspecialchars($item['description']); ?></textarea>
                                    </td>
                                    <td>
                                        <input type="number" value="<?php echo $item['price']; ?>" step="0.01" data-field="price">
                                    </td>
                                    <td>
                                        <select data-field="is_active">
                                            <option value="1" <?php echo $item['is_active'] ? 'selected' : ''; ?>>Available</option>
                                            <option value="0" <?php echo !$item['is_active'] ? 'selected' : ''; ?>>Unavailable</option>
                                        </select>
                                    </td>
                                    <td class="actions-cell">
                                        <div class="action-buttons" style="display: flex; gap: 6px; flex-wrap: wrap; justify-content: center;">
                                            <!-- Save Button (Always Visible) -->
                                            <button class="btn-action btn-save" 
                                                    onclick="saveRow(<?php echo $item['id']; ?>, '<?php echo htmlspecialchars($item['category']); ?>')" 
                                                    title="Save Changes"
                                                    style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white;">
                                                <i class="fas fa-save"></i>
                                            </button>
                                            
                                            <!-- Toggle Availability -->
                                            <button class="btn-action btn-toggle <?php echo $item['is_active'] ? 'active' : ''; ?>" 
                                                    onclick="quickToggle(<?php echo $item['id']; ?>)" 
                                                    title="<?php echo $item['is_active'] ? 'Mark as Unavailable' : 'Mark as Available'; ?>">
                                                <i class="fas fa-toggle-<?php echo $item['is_active'] ? 'on' : 'off'; ?>"></i>
                                            </button>
                                            
                                            <!-- Delete -->
                                            <button class="btn-action btn-delete" 
                                                    onclick="if(confirm('Delete this menu item?')) deleteRow(<?php echo $item['id']; ?>, '<?php echo htmlspecialchars($item['category']); ?>')" 
                                                    title="Delete Item">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <p>No items in this category yet. Click "Add Menu Item" to get started.</p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Add Menu Item Modal -->
    <div class="modal" id="addMenuModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 10000; align-items: center; justify-content: center;">
        <div style="background: white; border-radius: 12px; padding: 32px; max-width: 600px; width: 90%; max-height: 90vh; overflow-y: auto;">
            <div style="font-size: 24px; font-weight: 700; color: var(--navy); margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center;">
                <span>Add New Menu Item</span>
                <span onclick="closeAddModal()" style="cursor: pointer; font-size: 28px; color: #999;">&times;</span>
            </div>
            
            <form method="POST">
                <input type="hidden" name="action" value="add">
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">Category *</label>
                    <select name="category" id="add_category" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat); ?>"><?php echo htmlspecialchars($cat); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">Item Name *</label>
                    <input type="text" name="name" id="add_name" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">Description</label>
                    <textarea name="description" id="add_description" rows="3" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;"></textarea>
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">Price (K) *</label>
                    <input type="number" name="price" id="add_price" step="0.01" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">Display Order</label>
                    <input type="number" name="item_order" id="add_order" value="0" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" name="is_active" id="add_active" checked style="width: auto;">
                        <span style="font-weight: 600;">Active (visible on menu)</span>
                    </label>
                </div>
                
                <div style="display: flex; gap: 12px; justify-content: flex-end;">
                    <button type="button" onclick="closeAddModal()" style="background: #6c757d; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer;">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" style="background: var(--gold); color: var(--deep-navy); border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-weight: 600;">
                        <i class="fas fa-save"></i> Add Item
                    </button>
                </div>
            </form>
        </div>
    </div>


    <script>
        function openAddModal(category = null) {
            const modal = document.getElementById('addMenuModal');
            modal.style.display = 'flex';
            
            // Pre-select category if provided
            if (category) {
                const categorySelect = document.getElementById('add_category');
                if (categorySelect) {
                    categorySelect.value = category;
                }
            }
        }

        function closeAddModal() {
            document.getElementById('addMenuModal').style.display = 'none';
        }

        // Close modal when clicking outside
        document.getElementById('addMenuModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeAddModal();
            }
        });

        function saveRow(id, category) {
            const row = document.getElementById(`row-${id}`);
            const formData = new FormData();
            
            formData.append('action', 'update');
            formData.append('id', id);
            formData.append('category', category);
            
            // Collect ALL field values
            formData.append('item_order', row.querySelector('[data-field="item_order"]').value);
            formData.append('name', row.querySelector('[data-field="name"]').value);
            formData.append('description', row.querySelector('[data-field="description"]').value);
            formData.append('price', row.querySelector('[data-field="price"]').value);
            formData.append('is_active', row.querySelector('[data-field="is_active"]').value);
            
            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (response.ok) {
                    window.location.reload();
                } else {
                    alert('Error saving item');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error saving item');
            });
        }

        // Quick toggle availability
        function quickToggle(id) {
            const formData = new FormData();
            formData.append('action', 'toggle_availability');
            formData.append('id', id);
            
            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (response.ok) {
                    window.location.reload();
                } else {
                    alert('Error toggling availability');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error toggling availability');
            });
        }

        function deleteRow(id, category) {
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
                    alert('Error deleting item');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deleting item');
            });
        }
    </script>
</body>
</html>
