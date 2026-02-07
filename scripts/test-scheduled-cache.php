<?php
/**
 * Test Scheduled Cache Clear Script
 * Run this to manually test the cache clearing functionality
 */

echo "=================================================================\n";
echo "  Testing Scheduled Cache Clear\n";
echo "=================================================================\n\n";

// Load database connection
require_once __DIR__ . '/../config/database.php';

// Fetch current settings
echo "Fetching current settings...\n";
$stmt = $pdo->query("
    SELECT setting_key, setting_value 
    FROM site_settings 
    WHERE setting_key LIKE 'cache_%'
    ORDER BY setting_key
");
$settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

echo "\nCurrent Cache Settings:\n";
echo "-------------------------\n";
foreach ($settings as $key => $value) {
    $display_value = $value;
    
    // Format timestamp if it's cache_last_run
    if ($key === 'cache_last_run' && is_numeric($value)) {
        $display_value = date('Y-m-d H:i:s', $value) . " ($value)";
    }
    
    echo str_pad($key, 30) . ": $display_value\n";
}

echo "\n=================================================================\n";
echo "  Running Cache Clear Test\n";
echo "=================================================================\n\n";

// Force run the scheduled script
echo "Executing scheduled-cache-clear.php...\n\n";

// Execute and capture output
$output = [];
$return_var = 0;
exec("php \"" . __DIR__ . "/scheduled-cache-clear.php\" 2>&1", $output, $return_var);

if (!empty($output)) {
    echo "Script Output:\n";
    echo implode("\n", $output) . "\n\n";
} else {
    echo "Script executed silently (normal behavior).\n\n";
}

echo "Exit Code: $return_var\n";

// Check if log file was created/updated
$log_file = __DIR__ . '/../logs/cache-clear.log';
if (file_exists($log_file)) {
    echo "\nLast 10 log entries:\n";
    echo "-------------------------\n";
    $log_content = file($log_file);
    $last_entries = array_slice($log_content, -10);
    echo implode("", $last_entries);
} else {
    echo "\nWARNING: Log file not found at: $log_file\n";
    echo "This might mean the schedule is disabled or the script didn't run.\n";
}

// Fetch updated last_run time
$stmt = $pdo->query("
    SELECT setting_value 
    FROM site_settings 
    WHERE setting_key = 'cache_last_run'
");
$last_run = $stmt->fetchColumn();

echo "\n=================================================================\n";
echo "  Test Results\n";
echo "=================================================================\n\n";

if ($last_run) {
    $time_diff = time() - (int)$last_run;
    echo "Last Run: " . date('Y-m-d H:i:s', $last_run) . " ($time_diff seconds ago)\n";
    
    if ($time_diff < 10) {
        echo "STATUS: ✓ Cache clear executed successfully!\n";
    } else {
        echo "STATUS: ⚠ Cache clear may not have executed (check settings).\n";
    }
} else {
    echo "STATUS: ✗ No cache_last_run timestamp found.\n";
    echo "The scheduled clear may not have run yet, or the schedule is disabled.\n";
}

echo "\nTo view real-time logs, run:\n";
echo "  Get-Content logs\\cache-clear.log -Wait\n";

echo "\n=================================================================\n";
?>
