<?php
/**
 * Diagnostic script to test the search-bookings API
 * This will help identify the JSON parse error issue
 */

// Show error log location
echo "<h2>PHP Error Log Configuration</h2>";
echo "<pre>";
echo "error_log setting: " . ini_get('error_log') . "\n";
echo "log_errors: " . (ini_get('log_errors') ? 'Enabled' : 'Disabled') . "\n";
echo "display_errors: " . (ini_get('display_errors') ? 'Enabled' : 'Disabled') . "\n";
echo "</pre>";

// Test the API endpoint
echo "<h2>Testing API Endpoint</h2>";
echo "<h3>Test 1: Check if file exists</h3>";
$apiFile = __DIR__ . '/search-bookings.php';
echo "API File path: " . $apiFile . "<br>";
echo "File exists: " . (file_exists($apiFile) ? 'YES' : 'NO') . "<br>";

echo "<h3>Test 2: Check database.php path</h3>";
$dbPath1 = __DIR__ . '/../config/database.php';
$dbPath2 = __DIR__ . '/../../config/database.php';
echo "Path 1 (../config/database.php): " . $dbPath1 . " - " . (file_exists($dbPath1) ? 'EXISTS' : 'NOT FOUND') . "<br>";
echo "Path 2 (../../config/database.php): " . $dbPath2 . " - " . (file_exists($dbPath2) ? 'EXISTS' : 'NOT FOUND') . "<br>";

echo "<h3>Test 3: Direct API Call (with session)</h3>";
session_start();
$_SESSION['admin_user'] = 'test'; // Fake auth for testing

// Test recent bookings call
$apiUrl = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/search-bookings.php?type=room&recent=1';
echo "API URL: " . htmlspecialchars($apiUrl) . "<br><br>";

// Make the request
echo "<h4>Response:</h4>";
echo "<pre style='background: #f5f5f5; padding: 10px; border: 1px solid #ccc;'>";
$context = stream_context_create([
    'http' => [
        'header' => 'Cookie: ' . $_SERVER['HTTP_COOKIE'] ?? ''
    ]
]);
$response = file_get_contents($apiUrl, false, $context);
if ($response === false) {
    echo "ERROR: Failed to get response<br>";
    echo "Error: " . error_get_last()['message'] ?? 'Unknown error';
} else {
    echo "Raw Response Length: " . strlen($response) . " bytes<br>";
    echo "Raw Response (first 500 chars):<br>";
    echo htmlspecialchars(substr($response, 0, 500));
    if (strlen($response) > 500) {
        echo "... (truncated)";
    }
    
    echo "<br><br>--- Attempting JSON Decode ---<br>";
    $json = json_decode($response, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "JSON Decode: SUCCESS<br>";
        print_r($json);
    } else {
        echo "JSON Decode: FAILED - " . json_last_error_msg() . "<br>";
    }
}
echo "</pre>";

echo "<h3>Test 4: Check for PHP errors in response</h3>";
if (strpos($response ?? '', '<b>Warning</b>') !== false || strpos($response ?? '', '<b>Fatal error</b>') !== false) {
    echo "<span style='color: red;'>PHP ERRORS DETECTED IN RESPONSE!</span><br>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
} else {
    echo "<span style='color: green;'>No PHP errors detected in response</span>";
}
?>
