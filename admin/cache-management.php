<?php
/**
 * Enhanced Cache Management System
 * Easy cache control with toggles, scheduling, and bulk operations
 */

require_once 'admin-init.php';

$user = [
    'id' => $_SESSION['admin_user_id'],
    'username' => $_SESSION['admin_username'],
    'role' => $_SESSION['admin_role'],
    'full_name' => $_SESSION['admin_full_name']
];

$message = '';
$error = '';
$success = false;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        switch ($action) {
            case 'toggle_cache':
                $cache_type = $_POST['cache_type'] ?? '';
                $enabled = isset($_POST['enabled']) ? 1 : 0;
                
                // Update or insert cache setting
                $stmt = $pdo->prepare("
                    INSERT INTO site_settings (setting_key, setting_value, updated_at)
                    VALUES (?, ?, NOW())
                    ON DUPLICATE KEY UPDATE setting_value = ?, updated_at = NOW()
                ");
                $stmt->execute(["cache_{$cache_type}_enabled", $enabled, $enabled]);
                
                $message = "Cache '{$cache_type}' " . ($enabled ? 'enabled' : 'disabled') . " successfully!";
                $success = true;
                break;
                
            case 'clear_cache':
                $cache_types = $_POST['cache_types'] ?? [];
                
                if (empty($cache_types)) {
                    $error = 'Please select at least one cache type to clear.';
                } else {
                    require_once __DIR__ . '/../config/cache.php';
                    $cleared = 0;
                    $files_cleared = 0;
                    
                    foreach ($cache_types as $type) {
                        switch ($type) {
                            case 'all':
                                $before = count(glob(CACHE_DIR . '/*.cache'));
                                clearCache();
                                $files_cleared += $before;
                                $cleared++;
                                break;
                            case 'email':
                                $before = count(glob(CACHE_DIR . '/email_*.cache'));
                                clearCacheByPattern('email_*');
                                $files_cleared += $before;
                                $cleared++;
                                break;
                            case 'settings':
                                $before = count(glob(CACHE_DIR . '/setting_*.cache'));
                                clearCacheByPattern('setting_*');
                                $files_cleared += $before;
                                $cleared++;
                                break;
                            case 'tables':
                                $before = count(glob(CACHE_DIR . '/table_*.cache'));
                                clearCacheByPattern('table_*');
                                $files_cleared += $before;
                                $cleared++;
                                break;
                        }
                    }
                    
                    $message = "Successfully cleared {$files_cleared} cache files in {$cleared} cache type(s)!";
                    $success = true;
                }
                break;
                
            case 'set_schedule':
                $enabled = isset($_POST['schedule_enabled']) ? 1 : 0;
                $interval = $_POST['schedule_interval'] ?? 'daily';
                $time = $_POST['schedule_time'] ?? '00:00';
                
                // Update schedule settings
                $stmt = $pdo->prepare("
                    INSERT INTO site_settings (setting_key, setting_value, updated_at)
                    VALUES (?, ?, NOW())
                    ON DUPLICATE KEY UPDATE setting_value = ?, updated_at = NOW()
                ");
                $stmt->execute(['cache_schedule_enabled', $enabled, $enabled]);
                $stmt->execute(['cache_schedule_interval', $interval, $interval]);
                $stmt->execute(['cache_schedule_time', $time, $time]);
                
                $message = "Cache clearing schedule " . ($enabled ? 'enabled' : 'disabled') . "!";
                $success = true;
                break;
                
            case 'set_global_cache':
                $enabled = isset($_POST['global_cache_enabled']) ? 1 : 0;
                
                $stmt = $pdo->prepare("
                    INSERT INTO site_settings (setting_key, setting_value, updated_at)
                    VALUES ('cache_global_enabled', ?, NOW())
                    ON DUPLICATE KEY UPDATE setting_value = ?, updated_at = NOW()
                ");
                $stmt->execute([$enabled, $enabled]);
                
                $message = "Global caching " . ($enabled ? 'enabled' : 'disabled') . "!";
                $success = true;
                break;
        }
    } catch (PDOException $e) {
        $error = 'Database error: ' . $e->getMessage();
    }
}

// Get current cache settings
$cache_settings = [];
try {
    $stmt = $pdo->query("
        SELECT setting_key, setting_value 
        FROM site_settings 
        WHERE setting_key LIKE 'cache_%' OR setting_key LIKE '%_cache_%'
    ");
    $settings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($settings as $setting) {
        $cache_settings[$setting['setting_key']] = $setting['setting_value'];
    }
} catch (PDOException $e) {
    $error = 'Error fetching cache settings: ' . $e->getMessage();
}

// Get cache statistics
require_once __DIR__ . '/../config/cache.php';
$stats = getCacheStats();
$caches = listCache();

// Define cache types - based on actual cache patterns in the system
$cache_types = [
    'email' => [
        'name' => 'Email Settings',
        'icon' => 'fa-envelope',
        'description' => 'Email configuration and SMTP settings',
        'patterns' => ['email_*']
    ],
    'settings' => [
        'name' => 'Site Settings',
        'icon' => 'fa-cog',
        'description' => 'General site settings and configuration',
        'patterns' => ['setting_*']
    ],
    'tables' => [
        'name' => 'Database Tables',
        'icon' => 'fa-database',
        'description' => 'Cached database table data',
        'patterns' => ['table_*']
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cache Management - Admin Panel</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="css/admin-styles.css">
    
    <style>
        .cache-overview {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .cache-stat-card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .cache-stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }
        
        .cache-stat-icon.primary {
            background: rgba(212, 175, 55, 0.1);
            color: var(--gold);
        }
        
        .cache-stat-icon.success {
            background: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }
        
        .cache-stat-icon.warning {
            background: rgba(255, 193, 7, 0.1);
            color: #ffc107;
        }
        
        .cache-stat-icon.danger {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }
        
        .cache-stat-info h3 {
            font-size: 28px;
            font-weight: 700;
            color: var(--navy);
            margin-bottom: 4px;
        }
        
        .cache-stat-info p {
            font-size: 14px;
            color: #666;
            margin: 0;
        }
        
        .cache-section {
            background: white;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }
        
        .cache-section h2 {
            font-size: 20px;
            font-weight: 600;
            color: var(--navy);
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid var(--gold);
        }
        
        .cache-toggle-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 16px;
        }
        
        .cache-toggle-item {
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 16px;
            transition: all 0.3s;
        }
        
        .cache-toggle-item:hover {
            border-color: var(--gold);
        }
        
        .cache-toggle-item.active {
            border-color: #28a745;
            background: rgba(40, 167, 69, 0.05);
        }
        
        .cache-toggle-item.inactive {
            border-color: #dc3545;
            background: rgba(220, 53, 69, 0.05);
        }
        
        .cache-toggle-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }
        
        .cache-toggle-name {
            font-weight: 600;
            color: var(--navy);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .cache-toggle-status {
            font-size: 12px;
            font-weight: 600;
            padding: 4px 8px;
            border-radius: 4px;
        }
        
        .cache-toggle-status.enabled {
            background: #28a745;
            color: white;
        }
        
        .cache-toggle-status.disabled {
            background: #dc3545;
            color: white;
        }
        
        .cache-toggle-desc {
            font-size: 13px;
            color: #666;
            margin-bottom: 12px;
        }
        
        .cache-toggle-btn {
            width: 100%;
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .cache-toggle-btn.enable {
            background: #28a745;
            color: white;
        }
        
        .cache-toggle-btn.enable:hover {
            background: #218838;
        }
        
        .cache-toggle-btn.disable {
            background: #dc3545;
            color: white;
        }
        
        .cache-toggle-btn.disable:hover {
            background: #c82333;
        }
        
        .bulk-clear-form {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 20px;
        }
        
        .cache-checkbox-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .cache-checkbox-item:hover {
            border-color: var(--gold);
        }
        
        .cache-checkbox-item input[type="checkbox"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
        }
        
        .cache-checkbox-item label {
            cursor: pointer;
            font-weight: 500;
            color: var(--navy);
            flex: 1;
        }
        
        .btn-primary {
            background: var(--gold);
            color: var(--deep-navy);
        }
        
        .btn-primary:hover {
            background: #d4af37;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(212, 175, 55, 0.3);
        }
        
        .schedule-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            align-items: end;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        
        .form-group label {
            font-weight: 600;
            color: var(--navy);
            font-size: 14px;
        }
        
        .form-group select,
        .form-group input[type="time"] {
            padding: 10px 14px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        .form-group select:focus,
        .form-group input:focus {
            outline: none;
            border-color: var(--gold);
        }
        
        .switch-container {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 30px;
        }
        
        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 30px;
        }
        
        .slider:before {
            position: absolute;
            content: "";
            height: 24px;
            width: 24px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        
        input:checked + .slider {
            background-color: var(--gold);
        }
        
        input:checked + .slider:before {
            transform: translateX(30px);
        }
        
        .cache-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .cache-table th,
        .cache-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .cache-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: var(--navy);
            font-size: 13px;
            text-transform: uppercase;
        }
        
        .cache-table tr:hover {
            background: #f8f9fa;
        }
    </style>
</head>
<body>
    <?php require_once 'admin-header.php'; ?>
    
    <div class="content">
        <div class="page-header">
            <h2 class="page-title">
                <i class="fas fa-bolt"></i> Cache Management
            </h2>
            <p class="text-muted">Control website caching for optimal performance</p>
        </div>
        
        <?php if ($message): ?>
            <?php showAlert($message, 'success'); ?>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <?php showAlert($error, 'error'); ?>
        <?php endif; ?>
        
        <!-- Cache Statistics Overview -->
        <div class="cache-overview">
            <div class="cache-stat-card">
                <div class="cache-stat-icon primary">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="cache-stat-info">
                    <h3><?php echo $stats['total_files']; ?></h3>
                    <p>Total Cache Files</p>
                </div>
            </div>
            
            <div class="cache-stat-card">
                <div class="cache-stat-icon success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="cache-stat-info">
                    <h3><?php echo $stats['active_files']; ?></h3>
                    <p>Active Caches</p>
                </div>
            </div>
            
            <div class="cache-stat-card">
                <div class="cache-stat-icon warning">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="cache-stat-info">
                    <h3><?php echo $stats['expired_files']; ?></h3>
                    <p>Expired Caches</p>
                </div>
            </div>
            
            <div class="cache-stat-card">
                <div class="cache-stat-icon danger">
                    <i class="fas fa-hdd"></i>
                </div>
                <div class="cache-stat-info">
                    <h3><?php echo $stats['total_size_formatted']; ?></h3>
                    <p>Total Size</p>
                </div>
            </div>
        </div>
        
        <!-- Global Cache Control -->
        <div class="cache-section">
            <h2><i class="fas fa-power-off"></i> Global Cache Control</h2>
            <form method="POST" style="display: flex; align-items: center; gap: 20px;">
                <input type="hidden" name="action" value="set_global_cache">
                
                <div class="switch-container">
                    <label class="switch">
                        <input type="checkbox" name="global_cache_enabled" 
                               <?php echo isset($cache_settings['cache_global_enabled']) && $cache_settings['cache_global_enabled'] ? 'checked' : ''; ?>>
                        <span class="slider"></span>
                    </label>
                    <span style="font-weight: 600; color: var(--navy);">
                        Enable All Caching
                    </span>
                </div>
                
                <button type="submit" class="btn-action btn-save">
                    <i class="fas fa-save"></i> Save Setting
                </button>
            </form>
        </div>
        
        <!-- Individual Cache Toggles -->
        <div class="cache-section">
            <h2><i class="fas fa-toggle-on"></i> Individual Cache Controls</h2>
            <div class="cache-toggle-grid">
                <?php foreach ($cache_types as $type => $info): ?>
                <?php 
                $enabled = isset($cache_settings["cache_{$type}_enabled"]) 
                    ? $cache_settings["cache_{$type}_enabled"] 
                    : 1; // Default enabled
                ?>
                <div class="cache-toggle-item <?php echo $enabled ? 'active' : 'inactive'; ?>">
                    <div class="cache-toggle-header">
                        <div class="cache-toggle-name">
                            <i class="fas <?php echo $info['icon']; ?>"></i>
                            <?php echo $info['name']; ?>
                        </div>
                        <span class="cache-toggle-status <?php echo $enabled ? 'enabled' : 'disabled'; ?>">
                            <?php echo $enabled ? 'ON' : 'OFF'; ?>
                        </span>
                    </div>
                    <p class="cache-toggle-desc"><?php echo $info['description']; ?></p>
                    <form method="POST">
                        <input type="hidden" name="action" value="toggle_cache">
                        <input type="hidden" name="cache_type" value="<?php echo $type; ?>">
                        <input type="hidden" name="enabled" value="<?php echo $enabled ? 0 : 1; ?>">
                        <button type="submit" class="cache-toggle-btn <?php echo $enabled ? 'disable' : 'enable'; ?>">
                            <i class="fas fa-power-off"></i>
                            <?php echo $enabled ? 'Disable' : 'Enable'; ?>
                        </button>
                    </form>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Bulk Cache Clearing -->
        <div class="cache-section">
            <h2><i class="fas fa-eraser"></i> Bulk Cache Clearing</h2>
            <form method="POST">
                <input type="hidden" name="action" value="clear_cache">
                
                <div class="bulk-clear-form">
                    <label class="cache-checkbox-item">
                        <input type="checkbox" name="cache_types[]" value="email">
                        <span><i class="fas fa-envelope"></i> Email Settings (<?php echo count(array_filter($caches, function($c) { return strpos($c['key'], 'email_') === 0; })); ?> files)</span>
                    </label>
                    
                    <label class="cache-checkbox-item">
                        <input type="checkbox" name="cache_types[]" value="settings">
                        <span><i class="fas fa-cog"></i> Site Settings (<?php echo count(array_filter($caches, function($c) { return strpos($c['key'], 'setting_') === 0; })); ?> files)</span>
                    </label>
                    
                    <label class="cache-checkbox-item">
                        <input type="checkbox" name="cache_types[]" value="tables">
                        <span><i class="fas fa-database"></i> Database Tables (<?php echo count(array_filter($caches, function($c) { return strpos($c['key'], 'table_') === 0; })); ?> files)</span>
                    </label>
                    
                    <label class="cache-checkbox-item" style="border-color: #dc3545; background: rgba(220, 53, 69, 0.05);">
                        <input type="checkbox" name="cache_types[]" value="all">
                        <span><i class="fas fa-trash"></i> <strong>ALL CACHES</strong></span>
                    </label>
                </div>
                
                <button type="submit" class="btn-action btn-delete" 
                        onclick="return confirm('Are you sure you want to clear the selected caches?');">
                    <i class="fas fa-eraser"></i> Clear Selected Caches
                </button>
            </form>
        </div>
        
        <!-- Scheduled Cache Clearing -->
        <div class="cache-section">
            <h2><i class="fas fa-clock"></i> Scheduled Cache Clearing</h2>
            <form method="POST">
                <input type="hidden" name="action" value="set_schedule">
                
                <div class="schedule-form">
                    <div class="switch-container">
                        <label class="switch">
                            <input type="checkbox" name="schedule_enabled" 
                                   <?php echo isset($cache_settings['cache_schedule_enabled']) && $cache_settings['cache_schedule_enabled'] ? 'checked' : ''; ?>>
                            <span class="slider"></span>
                        </label>
                        <span style="font-weight: 600; color: var(--navy);">
                            Enable Auto-Clear
                        </span>
                    </div>
                    
                    <div class="form-group">
                        <label>Clear Frequency</label>
                        <select name="schedule_interval">
                            <option value="hourly" 
                                    <?php echo (isset($cache_settings['cache_schedule_interval']) && $cache_settings['cache_schedule_interval'] == 'hourly') ? 'selected' : ''; ?>>
                                Every Hour
                            </option>
                            <option value="6hours" 
                                    <?php echo (isset($cache_settings['cache_schedule_interval']) && $cache_settings['cache_schedule_interval'] == '6hours') ? 'selected' : ''; ?>>
                                Every 6 Hours
                            </option>
                            <option value="12hours" 
                                    <?php echo (isset($cache_settings['cache_schedule_interval']) && $cache_settings['cache_schedule_interval'] == '12hours') ? 'selected' : ''; ?>>
                                Every 12 Hours
                            </option>
                            <option value="daily" 
                                    <?php echo (!isset($cache_settings['cache_schedule_interval']) || $cache_settings['cache_schedule_interval'] == 'daily') ? 'selected' : ''; ?>>
                                Daily
                            </option>
                            <option value="weekly" 
                                    <?php echo (isset($cache_settings['cache_schedule_interval']) && $cache_settings['cache_schedule_interval'] == 'weekly') ? 'selected' : ''; ?>>
                                Weekly
                            </option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Clear At Time</label>
                        <input type="time" name="schedule_time" 
                               value="<?php echo $cache_settings['cache_schedule_time'] ?? '00:00'; ?>"
                               min="00:00" max="23:59">
                    </div>
                    
                    <button type="submit" class="btn-action btn-save">
                        <i class="fas fa-save"></i> Save Schedule
                    </button>
                </div>
            </form>
            
            <div style="margin-top: 20px; padding: 16px; background: #fff3cd; border-left: 4px solid #ffc107; border-radius: 4px;">
                <i class="fas fa-info-circle"></i>
                <strong>Note:</strong> Scheduled cache clearing requires a cron job to be set up on your server.
                <code>php scripts/scheduled-cache-clear.php</code> should run according to your schedule.
            </div>
        </div>
        
        <!-- Cache Files List -->
        <?php if (!empty($caches)): ?>
        <div class="cache-section">
            <h2><i class="fas fa-list"></i> Current Cache Files (<?php echo count($caches); ?>)</h2>
            <div style="overflow-x: auto;">
                <table class="cache-table">
                    <thead>
                        <tr>
                            <th>File Name</th>
                            <th>Cache Key</th>
                            <th>Size</th>
                            <th>Created</th>
                            <th>Expires</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($caches as $cache): ?>
                        <tr>
                            <td><code><?php echo htmlspecialchars($cache['file']); ?></code></td>
                            <td><?php echo htmlspecialchars($cache['key']); ?></td>
                            <td><?php echo $cache['size_formatted']; ?></td>
                            <td><?php echo $cache['created_formatted']; ?></td>
                            <td><?php echo $cache['expires_formatted']; ?></td>
                            <td>
                                <span class="badge <?php echo $cache['expired'] ? 'badge-expired' : 'badge-active'; ?>">
                                    <?php echo $cache['expired'] ? 'Expired' : 'Active'; ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <?php require_once 'admin-footer.php'; ?>
</body>
</html>