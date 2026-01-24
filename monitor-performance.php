<?php
/**
 * Performance Monitoring Script
 * Liwonde Sun Hotel - Page Load Performance Analyzer
 * 
 * Run this script to check page performance and identify bottlenecks
 * Usage: php monitor-performance.php
 */

require_once 'config/database.php';

// Enable error reporting for monitoring
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Performance tracking
$page_load_start = microtime(true);
$memory_start = memory_get_usage();
$query_count = 0;
$slow_queries = [];

// Custom query wrapper for monitoring
function monitoredQuery($pdo, $sql, $params = []) {
    global $query_count, $slow_queries;
    
    $start_time = microtime(true);
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $query_time = (microtime(true) - $start_time) * 1000; // Convert to milliseconds
    
    $query_count++;
    
    // Track slow queries (> 100ms)
    if ($query_time > 100) {
        $slow_queries[] = [
            'sql' => $sql,
            'time' => round($query_time, 2),
            'params' => $params
        ];
    }
    
    return $stmt;
}

echo str_repeat('=', 70) . "\n";
echo "  LIWONDE SUN HOTEL - PERFORMANCE MONITOR\n";
echo str_repeat('=', 70) . "\n\n";

// Test 1: Database Connection Time
$connection_test_start = microtime(true);
try {
    $test_conn = new PDO(
        "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    $test_conn = null;
    $connection_time = (microtime(true) - $connection_test_start) * 1000;
    echo "✓ Database Connection: " . round($connection_time, 2) . "ms\n";
} catch (PDOException $e) {
    echo "✗ Database Connection Failed: " . $e->getMessage() . "\n";
}

// Test 2: Settings Query Performance
$settings_test_start = microtime(true);
$site_name = getSetting('site_name');
$currency = getSetting('currency_symbol');
$settings_time = (microtime(true) - $settings_test_start) * 1000;
echo "✓ Settings Query: " . round($settings_time, 2) . "ms\n";

// Test 3: Rooms Query Performance
$rooms_test_start = microtime(true);
$stmt = monitoredQuery($pdo, 
    "SELECT id, name, price_per_night FROM rooms WHERE is_active = 1 ORDER BY display_order ASC"
);
$rooms = $stmt->fetchAll();
$rooms_time = (microtime(true) - $rooms_test_start) * 1000;
echo "✓ Rooms Query (" . count($rooms) . " rooms): " . round($rooms_time, 2) . "ms\n";

// Test 4: Bookings Query Performance
$bookings_test_start = microtime(true);
$stmt = monitoredQuery($pdo,
    "SELECT COUNT(*) as total FROM bookings WHERE status IN ('pending', 'confirmed', 'checked-in')"
);
$active_bookings = $stmt->fetch()['total'];
$bookings_time = (microtime(true) - $bookings_test_start) * 1000;
echo "✓ Active Bookings Count: " . round($bookings_time, 2) . "ms ({$active_bookings} bookings)\n";

// Test 5: Availability Check Performance
$check_test_start = microtime(true);
$result = checkRoomAvailability(1, date('Y-m-d', strtotime('+7 days')), date('Y-m-d', strtotime('+10 days')));
$availability_time = (microtime(true) - $check_test_start) * 1000;
$avail_status = $result['available'] ? 'Available' : 'Not Available';
echo "✓ Availability Check: " . round($availability_time, 2) . "ms ({$avail_status})\n";

// Test 6: Index Usage Check
echo "\n" . str_repeat('-', 70) . "\n";
echo "  INDEX USAGE ANALYSIS\n";
echo str_repeat('-', 70) . "\n\n";

$indexes_to_check = [
    'bookings' => ['idx_bookings_room_status', 'idx_bookings_dates', 'idx_bookings_status_dates'],
    'rooms' => ['idx_rooms_active', 'idx_rooms_order'],
    'site_settings' => ['idx_site_settings_key', 'idx_site_settings_group']
];

foreach ($indexes_to_check as $table => $expected_indexes) {
    $stmt = $pdo->query("SHOW INDEX FROM {$table}");
    $existing_indexes = $stmt->fetchAll(PDO::FETCH_COLUMN, 2); // Get index names (column 2)
    
    echo "Table: {$table}\n";
    foreach ($expected_indexes as $index) {
        $status = in_array($index, $existing_indexes) ? '✓' : '✗';
        echo "  {$status} {$index}\n";
    }
    echo "\n";
}

// Test 7: Table Statistics
echo str_repeat('-', 70) . "\n";
echo "  TABLE STATISTICS\n";
echo str_repeat('-', 70) . "\n\n";

$tables = ['bookings', 'rooms', 'site_settings', 'booking_notes'];
foreach ($tables as $table) {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM {$table}");
    $count = $stmt->fetch()['count'];
    $stmt = $pdo->query("SHOW TABLE STATUS LIKE '{$table}'");
    $status = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $size_kb = round(($status['Data_length'] + $status['Index_length']) / 1024, 2);
    echo "✓ {$table}: {$count} records (Size: {$size_kb} KB)\n";
}

// Test 8: Query Performance Summary
echo "\n" . str_repeat('-', 70) . "\n";
echo "  QUERY PERFORMANCE SUMMARY\n";
echo str_repeat('-', 70) . "\n\n";
echo "Total Queries Executed: {$query_count}\n";

if (!empty($slow_queries)) {
    echo "\n⚠️  SLOW QUERIES DETECTED (> 100ms):\n";
    foreach ($slow_queries as $slow) {
        echo "\n  Query Time: {$slow['time']}ms\n";
        echo "  SQL: " . substr($slow['sql'], 0, 100) . "...\n";
    }
} else {
    echo "\n✓ No slow queries detected (all queries < 100ms)\n";
}

// Test 9: Memory Usage
$memory_end = memory_get_usage();
$memory_peak = memory_get_peak_usage();
$memory_used = round(($memory_end - $memory_start) / 1024, 2);
$memory_peak_mb = round($memory_peak / 1024 / 1024, 2);

echo "\n" . str_repeat('-', 70) . "\n";
echo "  MEMORY USAGE\n";
echo str_repeat('-', 70) . "\n\n";
echo "Memory Used: {$memory_used} KB\n";
echo "Peak Memory: {$memory_peak_mb} MB\n";

// Test 10: Overall Page Load Time
$total_load_time = (microtime(true) - $page_load_start) * 1000;

echo "\n" . str_repeat('=', 70) . "\n";
echo "  OVERALL PERFORMANCE\n";
echo str_repeat('=', 70) . "\n\n";
echo "Total Script Execution Time: " . round($total_load_time, 2) . "ms\n";

// Performance Rating
if ($total_load_time < 100) {
    echo "Performance Rating: ⭐⭐⭐⭐⭐ Excellent (< 100ms)\n";
} elseif ($total_load_time < 300) {
    echo "Performance Rating: ⭐⭐⭐⭐ Good (< 300ms)\n";
} elseif ($total_load_time < 500) {
    echo "Performance Rating: ⭐⭐⭐ Average (< 500ms)\n";
} elseif ($total_load_time < 1000) {
    echo "Performance Rating: ⭐⭐ Slow (< 1s)\n";
    echo "⚠️  Consider optimizing queries or adding indexes\n";
} else {
    echo "Performance Rating: ⭐ Critical (>= 1s)\n";
    echo "⚠️  PERFORMANCE ISSUES DETECTED - Action required!\n";
}

echo "\n" . str_repeat('=', 70) . "\n";
echo "  RECOMMENDATIONS\n";
echo str_repeat('=', 70) . "\n\n";

$recommendations = [];

if ($connection_time > 200) {
    $recommendations[] = "Database connection is slow (> 200ms). Check network latency.";
}

if ($settings_time > 50) {
    $recommendations[] = "Settings queries are slow. Ensure idx_site_settings_key exists.";
}

if ($rooms_time > 100) {
    $recommendations[] = "Rooms query is slow. Ensure idx_rooms_active and idx_rooms_order exist.";
}

if ($total_load_time > 500) {
    $recommendations[] = "Overall performance is slow. Consider implementing page caching.";
}

if (empty($recommendations)) {
    echo "✓ No performance issues detected. System is running optimally!\n";
} else {
    foreach ($recommendations as $rec) {
        echo "• {$rec}\n";
    }
}

echo "\n";