<?php
/**
 * Cache Management Script
 * Clear and manage the file-based cache system
 */

require_once '../config/database.php';
require_once '../config/cache.php';

// Check if user is logged in
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$success = '';
$error = '';
$cache_stats = [
    'cache_dir' => CACHE_DIR,
    'cache_enabled' => CACHE_ENABLED ? 'Yes' : 'No',
    'cache_files' => 0,
    'cache_size' => '0 B',
    'oldest_file' => 'N/A',
    'newest_file' => 'N/A'
];

// Get cache statistics
if (is_dir(CACHE_DIR)) {
    $files = glob(CACHE_DIR . '/*.cache');
    $cache_stats['cache_files'] = count($files);
    
    if (!empty($files)) {
        $total_size = 0;
        $oldest_time = PHP_INT_MAX;
        $newest_time = 0;
        
        foreach ($files as $file) {
            $size = filesize($file);
            $total_size += $size;
            $time = filemtime($file);
            
            if ($time < $oldest_time) {
                $oldest_time = $time;
                $cache_stats['oldest_file'] = date('Y-m-d H:i:s', $time);
            }
            if ($time > $newest_time) {
                $newest_time = $time;
                $cache_stats['newest_file'] = date('Y-m-d H:i:s', $time);
            }
        }
        
        $cache_stats['cache_size'] = formatBytes($total_size);
    }
}

// Handle cache clearing
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        if ($action === 'clear_all') {
            clearCache();
            $success = 'All cache cleared successfully!';
        } elseif ($action === 'clear_expired') {
            $cleared = clearExpiredCache();
            $success = "Cleared {$cleared} expired cache files!";
        }
    } catch (Exception $e) {
        $error = 'Error: ' . $e->getMessage();
    }
}

/**
 * Clear expired cache files
 */
function clearExpiredCache() {
    $cleared = 0;
    $files = glob(CACHE_DIR . '/*.cache');
    
    if ($files) {
        foreach ($files as $file) {
            $data = file_get_contents($file);
            $cache = json_decode($data, true);
            
            if ($cache && isset($cache['expiry'])) {
                if (time() > $cache['expiry']) {
                    @unlink($file);
                    $cleared++;
                }
            }
        }
    }
    
    return $cleared;
}

/**
 * Format bytes to human readable format
 */
function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, $precision) . ' ' . $units[$pow];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cache Management - Liwonde Sun Hotel Admin</title>
    <link rel="stylesheet" href="css/admin-styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <div class="admin-layout">
        <?php include 'admin-header.php'; ?>
        
        <main class="admin-content">
            <div class="page-header">
                <h1><i class="fas fa-database"></i> Cache Management</h1>
            </div>

            <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo htmlspecialchars($success); ?>
            </div>
            <?php endif; ?>

            <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>

            <div class="cache-stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-folder-open"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value"><?php echo $cache_stats['cache_files']; ?></div>
                        <div class="stat-label">Cache Files</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-hdd"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value"><?php echo $cache_stats['cache_size']; ?></div>
                        <div class="stat-label">Total Size</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-toggle-on"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value"><?php echo $cache_stats['cache_enabled']; ?></div>
                        <div class="stat-label">Cache Enabled</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value" style="font-size: 14px;">
                            <?php echo $cache_stats['newest_file']; ?>
                        </div>
                        <div class="stat-label">Last Updated</div>
                    </div>
                </div>
            </div>

            <div class="card">
                <h2><i class="fas fa-cog"></i> Cache Actions</h2>
                
                <div class="action-buttons">
                    <form method="POST" onsubmit="return confirm('Are you sure you want to clear ALL cache files?');">
                        <input type="hidden" name="action" value="clear_all">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Clear All Cache
                        </button>
                    </form>

                    <form method="POST" onsubmit="return confirm('Are you sure you want to clear expired cache files?');">
                        <input type="hidden" name="action" value="clear_expired">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-broom"></i> Clear Expired Cache
                        </button>
                    </form>

                    <a href="dashboard.php" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                </div>
            </div>

            <div class="card">
                <h2><i class="fas fa-info-circle"></i> Cache Information</h2>
                <table class="data-table">
                    <tr>
                        <td><strong>Cache Directory:</strong></td>
                        <td><?php echo htmlspecialchars($cache_stats['cache_dir']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Cache Enabled:</strong></td>
                        <td><?php echo $cache_stats['cache_enabled']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Default TTL:</strong></td>
                        <td>1 hour (3600 seconds)</td>
                    </tr>
                    <tr>
                        <td><strong>Oldest Cache File:</strong></td>
                        <td><?php echo $cache_stats['oldest_file']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Newest Cache File:</strong></td>
                        <td><?php echo $cache_stats['newest_file']; ?></td>
                    </tr>
                </table>
            </div>

            <div class="card">
                <h2><i class="fas fa-question-circle"></i> How Caching Works</h2>
                <div class="info-box">
                    <p><strong>What is cached:</strong></p>
                    <ul>
                        <li>Site settings (site_name, currency_symbol, etc.)</li>
                        <li>Email settings (SMTP configuration, etc.)</li>
                        <li>Database table existence checks</li>
                    </ul>
                    
                    <p><strong>Benefits:</strong></p>
                    <ul>
                        <li>Dramatically reduces database queries</li>
                        <li>Faster page load times</li>
                        <li>Less load on remote database server</li>
                    </ul>
                    
                    <p><strong>When to clear cache:</strong></p>
                    <ul>
                        <li>After updating site settings in the database</li>
                        <li>After changing email configuration</li>
                        <li>If you see outdated information on pages</li>
                        <li>Cache automatically expires after TTL (1 hour)</li>
                    </ul>
                </div>
            </div>
        </main>
    </div>
</body>
</html>