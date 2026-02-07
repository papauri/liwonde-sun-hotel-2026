<?php
/**
 * Scheduled Cache Clearing Script
 * Run this via cron job to automatically clear cache based on settings
 * 
 * Cron Examples:
 * - Hourly: 0 * * * * php /path/to/scripts/scheduled-cache-clear.php
 * - Daily at 3 AM: 0 3 * * * php /path/to/scripts/scheduled-cache-clear.php
 * - Weekly: 0 3 * * 0 php /path/to/scripts/scheduled-cache-clear.php
 */

// Production error handling
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/cron-errors.log');

// Load database connection
require_once __DIR__ . '/../config/database.php';

// Fetch schedule settings
$stmt = $pdo->query("
    SELECT setting_key, setting_value 
    FROM site_settings 
    WHERE setting_key IN ('cache_schedule_enabled', 'cache_schedule_interval', 'cache_schedule_time')
");
$settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

$schedule_enabled = isset($settings['cache_schedule_enabled']) ? (int)$settings['cache_schedule_enabled'] : 0;
$interval = $settings['cache_schedule_interval'] ?? 'daily';
$scheduled_time = $settings['cache_schedule_time'] ?? '00:00';

// Check if scheduling is enabled
if (!$schedule_enabled) {
    error_log("Scheduled cache clear: Schedule is disabled");
    exit(0);
}

// Check if we should run based on interval and time
$now = time();
$current_hour = date('H');
$current_time = date('H:i');

// Parse scheduled time
list($scheduled_hour, $scheduled_minute) = explode(':', $scheduled_time);

$should_run = false;

switch ($interval) {
    case 'hourly':
        // Run every hour at the scheduled minute
        $should_run = (date('i') == $scheduled_minute);
        break;
        
    case '6hours':
        // Run every 6 hours (0, 6, 12, 18)
        $hour_interval = (int)$current_hour % 6;
        $should_run = ($hour_interval == (int)$scheduled_hour % 6) && (date('i') == $scheduled_minute);
        break;
        
    case '12hours':
        // Run every 12 hours (0, 12)
        $hour_interval = (int)$current_hour % 12;
        $should_run = ($hour_interval == (int)$scheduled_hour % 12) && (date('i') == $scheduled_minute);
        break;
        
    case 'daily':
        // Run once daily at scheduled time
        $should_run = ($current_time == $scheduled_time);
        break;
        
    case 'weekly':
        // Run once weekly at scheduled time (on Sunday)
        $should_run = (date('w') == 0) && ($current_time == $scheduled_time);
        break;
}

if (!$should_run) {
    // Silent exit - not scheduled to run now
    exit(0);
}

// Load cache functions
require_once __DIR__ . '/../config/cache.php';

// Clear all cache
$cleared = clearCache();

// Log the action
$log_file = __DIR__ . '/../logs/cache-clear.log';
$log_entry = date('Y-m-d H:i:s') . " - Scheduled cache clear executed. Files cleared: {$cleared}\n";

// Ensure log directory exists
$log_dir = dirname($log_file);
if (!file_exists($log_dir)) {
    mkdir($log_dir, 0755, true);
}

file_put_contents($log_file, $log_entry, FILE_APPEND);
error_log("Scheduled cache clear completed: {$cleared} files cleared");

exit(0);
?>