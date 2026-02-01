<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== Testing payments.php ===\n\n";

// Simulate admin session
$_SESSION['admin_user'] = ['full_name' => 'Test', 'role' => 'admin'];
$_GET = [];

echo "Step 1: Including database.php...\n";
require_once '../config/database.php';
echo "✓ Database included\n\n";

echo "Step 2: Testing getSetting function...\n";
$site_name = getSetting('site_name');
echo "✓ Site name: $site_name\n\n";

echo "Step 3: Testing payments query...\n";
try {
    $sql = "
        SELECT
            p.*,
            CASE
                WHEN p.booking_type = 'room' THEN CONCAT(b.guest_name, ' (', b.booking_reference, ')')
                WHEN p.booking_type = 'conference' THEN CONCAT(ci.company_name, ' (', ci.inquiry_reference, ')')
                ELSE 'Unknown'
            END as booking_description,
            CASE
                WHEN p.booking_type = 'room' THEN b.booking_reference
                WHEN p.booking_type = 'conference' THEN ci.inquiry_reference
                ELSE NULL
            END as booking_reference,
            CASE
                WHEN p.booking_type = 'room' THEN b.guest_email
                WHEN p.booking_type = 'conference' THEN ci.email
                ELSE NULL
            END as contact_email
        FROM payments p
        LEFT JOIN bookings b ON p.booking_type = 'room' AND p.booking_id = b.id
        LEFT JOIN conference_inquiries ci ON p.booking_type = 'conference' AND p.booking_id = ci.id
        LIMIT 1
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([]);
    echo "✓ Query executed successfully\n\n";
} catch (PDOException $e) {
    echo "✗ ERROR: " . $e->getMessage() . "\n\n";
}

echo "Step 4: Testing summary query...\n";
try {
    $summaryStmt = $pdo->prepare("
        SELECT
            COUNT(*) as total_payments,
            SUM(CASE WHEN payment_status = 'completed' THEN total_amount ELSE 0 END) as total_collected,
            SUM(CASE WHEN payment_status = 'pending' THEN total_amount ELSE 0 END) as total_pending,
            SUM(CASE WHEN payment_status IN ('refunded', 'partially_refunded') THEN total_amount ELSE 0 END) as total_refunded
        FROM payments
    ");
    $summaryStmt->execute();
    $summary = $summaryStmt->fetch(PDO::FETCH_ASSOC);
    echo "✓ Summary query executed\n";
    print_r($summary);
} catch (PDOException $e) {
    echo "✗ ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";
