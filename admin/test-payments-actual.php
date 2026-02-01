<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== Testing actual payments.php ===\n\n";

// Simulate admin session
$_SESSION['admin_user'] = ['full_name' => 'Test', 'role' => 'admin'];
$_GET = [];

echo "Including payments.php...\n";
try {
    include 'payments.php';
    echo "\n✓ Page executed successfully\n";
} catch (Throwable $e) {
    echo "\n✗ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}
