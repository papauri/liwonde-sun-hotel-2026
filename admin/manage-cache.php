<?php
/**
 * Cache Management Utility
 * View, list, and clear cache files easily
 */

require_once __DIR__ . '/../config/cache.php';

// Check if this is an API request or web request
$isApiRequest = isset($_GET['api']) || strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false;

// Handle actions
$action = $_GET['action'] ?? 'view';

if ($action === 'clear') {
    $pattern = $_GET['pattern'] ?? '';
    $message = '';
    
    if ($pattern) {
        $cleared = clearCacheByPattern($pattern);
        $message = "Cleared {$cleared} cache files matching pattern: {$pattern}";
    } else {
        clearCache();
        $message = "All cache cleared successfully!";
    }
    
    if ($isApiRequest) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => $message]);
        exit;
    }
    
    $success = $message;
} elseif ($action === 'delete') {
    $key = $_GET['key'] ?? '';
    $deleted = deleteCache($key);
    
    if ($isApiRequest) {
        header('Content-Type: application/json');
        echo json_encode(['success' => $deleted, 'message' => $deleted ? "Cache deleted: {$key}" : "Failed to delete: {$key}"]);
        exit;
    }
    
    $success = $deleted ? "Cache deleted: {$key}" : "Failed to delete: {$key}";
}

// Get cache information
$caches = listCache();
$stats = getCacheStats();

// Output as JSON if API request
if ($isApiRequest) {
    header('Content-Type: application/json');
    echo json_encode([
        'stats' => $stats,
        'caches' => $caches
    ], JSON_PRETTY_PRINT);
    exit;
}

// Web interface
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cache Management - Liwonde Sun Hotel</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .header {
            background: white;
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .header h1 {
            color: #333;
            margin-bottom: 10px;
        }
        
        .header p {
            color: #666;
        }
        
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        
        .stat-card h3 {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .stat-card .value {
            font-size: 32px;
            font-weight: bold;
            color: #667eea;
        }
        
        .stat-card.expired .value {
            color: #f56565;
        }
        
        .actions {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .actions h2 {
            margin-bottom: 15px;
            color: #333;
        }
        
        .actions .btn {
            display: inline-block;
            padding: 10px 20px;
            margin-right: 10px;
            margin-bottom: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .actions .btn-danger {
            background: #f56565;
            color: white;
        }
        
        .actions .btn-danger:hover {
            background: #e53e3e;
        }
        
        .actions .btn-warning {
            background: #ed8936;
            color: white;
        }
        
        .actions .btn-warning:hover {
            background: #dd6b20;
        }
        
        .actions .btn-secondary {
            background: #718096;
            color: white;
        }
        
        .actions .btn-secondary:hover {
            background: #4a5568;
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            background: #48bb78;
            color: white;
            font-weight: 500;
        }
        
        .cache-table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .cache-table table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .cache-table th,
        .cache-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .cache-table th {
            background: #f7fafc;
            font-weight: 600;
            color: #4a5568;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .cache-table tr:hover {
            background: #f7fafc;
        }
        
        .cache-table .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .cache-table .badge-active {
            background: #c6f6d5;
            color: #22543d;
        }
        
        .cache-table .badge-expired {
            background: #fed7d7;
            color: #742a2a;
        }
        
        .cache-table .file-name {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            color: #4a5568;
        }
        
        .cache-table .cache-key {
            font-weight: 500;
            color: #2d3748;
        }
        
        .cache-table .btn-delete {
            padding: 5px 10px;
            background: #f56565;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            transition: background 0.3s;
        }
        
        .cache-table .btn-delete:hover {
            background: #e53e3e;
        }
        
        .empty-state {
            padding: 60px 20px;
            text-align: center;
            color: #718096;
        }
        
        .empty-state svg {
            width: 64px;
            height: 64px;
            margin-bottom: 20px;
            opacity: 0.5;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>=Â Cache Management</h1>
            <p>View and manage your website's cache files</p>
        </div>
        
        <?php if (isset($success)): ?>
        <div class="alert">
             <?php echo htmlspecialchars($success); ?>
        </div>
        <?php endif; ?>
        
        <div class="stats">
            <div class="stat-card">
                <h3>Total Files</h3>
                <div class="value"><?php echo $stats['total_files']; ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Size</h3>
                <div class="value"><?php echo $stats['total_size_formatted']; ?></div>
            </div>
            <div class="stat-card">
                <h3>Active</h3>
                <div class="value"><?php echo $stats['active_files']; ?></div>
            </div>
            <div class="stat-card expired">
                <h3>Expired</h3>
                <div class="value"><?php echo $stats['expired_files']; ?></div>
            </div>
        </div>
        
        <div class="actions">
            <h2>Quick Actions</h2>
            <a href="?action=clear" class="btn btn-danger" onclick="return confirm('Are you sure you want to clear ALL cache?');">
                =Ñ Clear All Cache
            </a>
            <a href="?action=clear&pattern=hero_*" class="btn btn-warning">
                <¬ Clear Hero Slides Cache
            </a>
            <a href="?action=clear&pattern=gallery_images" class="btn btn-warning">
                =¼ Clear Gallery Cache
            </a>
            <a href="?action=clear&pattern=rooms_*" class="btn btn-warning">
                =Ï Clear Rooms Cache
            </a>
            <a href="?action=clear&pattern=facilities_*" class="btn btn-warning">
                <Ê Clear Facilities Cache
            </a>
            <a href="?action=clear&pattern=setting_*" class="btn btn-secondary">
                ™ Clear Settings Cache
            </a>
            <a href="?" class="btn btn-secondary">
                = Refresh
            </a>
        </div>
        
        <div class="cache-table">
            <?php if (empty($caches)): ?>
            <div class="empty-state">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
                <p>No cache files found</p>
            </div>
            <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Cache File</th>
                        <th>Cache Key</th>
                        <th>Size</th>
                        <th>Created</th>
                        <th>Expires</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($caches as $cache): ?>
                    <tr>
                        <td class="file-name"><?php echo htmlspecialchars($cache['file']); ?></td>
                        <td class="cache-key"><?php echo htmlspecialchars($cache['key']); ?></td>
                        <td><?php echo $cache['size_formatted']; ?></td>
                        <td><?php echo $cache['created_formatted']; ?></td>
                        <td><?php echo $cache['expires_formatted']; ?></td>
                        <td>
                            <span class="badge <?php echo $cache['expired'] ? 'badge-expired' : 'badge-active'; ?>">
                                <?php echo $cache['expired'] ? 'Expired' : 'Active'; ?>
                            </span>
                        </td>
                        <td>
                            <a href="?action=delete&key=<?php echo urlencode($cache['key']); ?>" 
                               class="btn-delete"
                               onclick="return confirm('Delete cache: <?php echo htmlspecialchars($cache['key']); ?>?');">
                                Delete
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>