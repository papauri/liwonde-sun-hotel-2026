<?php
/**
 * Web-based Cache Cron Test
 * Access via browser: https://yoursite.com/test-cache-cron.php
 */

// Set headers for plain text output
header('Content-Type: text/plain; charset=utf-8');

echo "=================================================================\n";
echo "  CACHE CRON DIAGNOSTIC TEST\n";
echo "=================================================================\n\n";

echo "Current Time: " . date('Y-m-d H:i:s') . "\n";
echo "Server: " . $_SERVER['SERVER_NAME'] . "\n";
echo "PHP Version: " . PHP_VERSION . "\n\n";

// Check if cron-errors.log exists and show content
echo "=================================================================\n";
echo "  CRON ERRORS LOG\n";
echo "=================================================================\n\n";

$cron_error_log = __DIR__ . '/logs/cron-errors.log';
if (file_exists($cron_error_log)) {
    echo "File exists: YES\n";
    echo "File size: " . filesize($cron_error_log) . " bytes\n\n";
    echo "Last 30 lines:\n";
    echo "-------------------------\n";
    $lines = file($cron_error_log);
    $last_lines = array_slice($lines, -30);
    echo implode("", $last_lines);
} else {
    echo "File exists: NO\n";
    echo "Expected path: $cron_error_log\n";
}

echo "\n=================================================================\n";
echo "  CACHE SETTINGS CHECK\n";
echo "=================================================================\n\n";

try {
    require_once __DIR__ . '/config/database.php';
    
    $stmt = $pdo->query("
        SELECT setting_key, setting_value 
        FROM site_settings 
        WHERE setting_key LIKE 'cache_%'
        ORDER BY setting_key
    ");
    $settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    
    if (empty($settings)) {
        echo "WARNING: No cache settings found in database!\n";
    } else {
        foreach ($settings as $key => $value) {
            $display = $value;
            if ($key === 'cache_last_run' && is_numeric($value)) {
                $display = date('Y-m-d H:i:s', $value) . " ($value)";
            }
            echo str_pad($key, 30) . ": $display\n";
        }
    }
    
    // Check if schedule is enabled
    $schedule_enabled = isset($settings['cache_schedule_enabled']) ? (int)$settings['cache_schedule_enabled'] : 0;
    
    echo "\n";
    if (!$schedule_enabled) {
        echo "⚠ WARNING: Cache schedule is DISABLED in admin panel!\n";
        echo "   Go to Admin Panel > Cache Management and enable 'Auto-Clear'\n";
    } else {
        echo "✓ Cache schedule is ENABLED\n";
    }
    
} catch (Exception $e) {
    echo "ERROR connecting to database:\n";
    echo $e->getMessage() . "\n";
}

echo "\n=================================================================\n";
echo "  MANUAL CRON EXECUTION TEST\n";
echo "=================================================================\n\n";

echo "Attempting to run scheduled-cache-clear.php...\n\n";

// Capture output
ob_start();
$start_time = microtime(true);

try {
    include __DIR__ . '/scripts/scheduled-cache-clear.php';
    $execution_time = microtime(true) - $start_time;
    
    $output = ob_get_clean();
    
    echo "Execution time: " . number_format($execution_time, 3) . " seconds\n";
    
    if (!empty($output)) {
        echo "Script output:\n";
        echo "-------------------------\n";
        echo $output . "\n";
    } else {
        echo "Script executed silently (normal behavior)\n";
    }
    
} catch (Exception $e) {
    ob_end_clean();
    echo "ERROR during execution:\n";
    echo $e->getMessage() . "\n";
    echo "\nStack trace:\n";
    echo $e->getTraceAsString() . "\n";
}

echo "\n=================================================================\n";
echo "  LOG FILES CHECK\n";
echo "=================================================================\n\n";

$log_files = [
    'cache-clear.log' => __DIR__ . '/logs/cache-clear.log',
    'cron-errors.log' => __DIR__ . '/logs/cron-errors.log',
    'php-errors.log' => __DIR__ . '/logs/php-errors.log'
];

foreach ($log_files as $name => $path) {
    echo "$name:\n";
    if (file_exists($path)) {
        $size = filesize($path);
        $modified = date('Y-m-d H:i:s', filemtime($path));
        echo "  ✓ EXISTS - Size: $size bytes, Modified: $modified\n";
        
        if ($size > 0 && $size < 10000) {
            echo "  Last 5 lines:\n";
            $lines = file($path);
            $last = array_slice($lines, -5);
            foreach ($last as $line) {
                echo "    " . trim($line) . "\n";
            }
        }
    } else {
        echo "  ✗ NOT FOUND at: $path\n";
        
        // Check if directory exists
        $dir = dirname($path);
        if (!is_dir($dir)) {
            echo "  ✗ Directory does not exist: $dir\n";
        } else {
            echo "  ✓ Directory exists, permissions: " . substr(sprintf('%o', fileperms($dir)), -4) . "\n";
        }
    }
    echo "\n";
}

echo "=================================================================\n";
echo "  FILE PERMISSIONS CHECK\n";
echo "=================================================================\n\n";

$check_paths = [
    'logs directory' => __DIR__ . '/logs',
    'cache directory' => __DIR__ . '/cache',
    'scripts directory' => __DIR__ . '/scripts',
    'scheduled-cache-clear.php' => __DIR__ . '/scripts/scheduled-cache-clear.php'
];

foreach ($check_paths as $name => $path) {
    if (file_exists($path)) {
        $perms = substr(sprintf('%o', fileperms($path)), -4);
        $writeable = is_writable($path) ? 'YES' : 'NO';
        echo str_pad($name, 30) . ": Perms=$perms, Writable=$writeable\n";
    } else {
        echo str_pad($name, 30) . ": NOT FOUND\n";
    }
}

echo "\n=================================================================\n";
echo "  RECOMMENDATIONS\n";
echo "=================================================================\n\n";

// Provide actionable recommendations
$recommendations = [];

if (!file_exists(__DIR__ . '/logs/cache-clear.log')) {
    $recommendations[] = "1. Cache clear log not created - check if schedule is enabled in admin panel";
}

if (file_exists($cron_error_log) && filesize($cron_error_log) > 0) {
    $recommendations[] = "2. Check cron-errors.log above for PHP errors preventing execution";
}

if (!isset($schedule_enabled) || !$schedule_enabled) {
    $recommendations[] = "3. Enable cache schedule in Admin Panel > Cache Management";
}

if (!empty($recommendations)) {
    foreach ($recommendations as $rec) {
        echo "⚠ $rec\n";
    }
} else {
    echo "✓ Everything looks good!\n";
    echo "✓ If cron is set to run every minute, you should see cache-clear.log updates soon.\n";
}

echo "\n=================================================================\n";
echo "Test completed at " . date('Y-m-d H:i:s') . "\n";
echo "=================================================================\n";
?>
