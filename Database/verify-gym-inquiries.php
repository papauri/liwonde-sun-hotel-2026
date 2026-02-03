<?php
/**
 * Verify Gym Inquiries Table
 */

require_once '../config/database.php';

echo "<h2>Gym Inquiries Table Verification</h2>";

try {
    // Check if table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'gym_inquiries'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color:green;'>✓ Table 'gym_inquiries' exists.</p>";
        
        // Show table structure
        echo "<h3>Table Structure:</h3>";
        $columns = $pdo->query("DESCRIBE gym_inquiries")->fetchAll(PDO::FETCH_ASSOC);
        echo "<table border='1' cellpadding='8' style='border-collapse:collapse;'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        foreach ($columns as $col) {
            echo "<tr>";
            echo "<td>{$col['Field']}</td>";
            echo "<td>{$col['Type']}</td>";
            echo "<td>{$col['Null']}</td>";
            echo "<td>{$col['Key']}</td>";
            echo "<td>{$col['Default']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Count records
        $count = $pdo->query("SELECT COUNT(*) as count FROM gym_inquiries")->fetch(PDO::FETCH_ASSOC)['count'];
        echo "<h3>Records: $count</h3>";
        
        // Show recent records if any
        if ($count > 0) {
            echo "<h3>Recent Inquiries:</h3>";
            $inquiries = $pdo->query("SELECT * FROM gym_inquiries ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
            echo "<table border='1' cellpadding='8' style='border-collapse:collapse;'>";
            echo "<tr><th>Reference</th><th>Name</th><th>Email</th><th>Status</th><th>Created</th></tr>";
            foreach ($inquiries as $inq) {
                echo "<tr>";
                echo "<td>{$inq['reference_number']}</td>";
                echo "<td>{$inq['name']}</td>";
                echo "<td>{$inq['email']}</td>";
                echo "<td>{$inq['status']}</td>";
                echo "<td>{$inq['created_at']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        
        echo "<hr>";
        echo "<p><strong>Testing Links:</strong></p>";
        echo "<ul>";
        echo "<li><a href='../gym.php'>Test Gym Booking Form</a></li>";
        echo "<li><a href='../admin/gym-inquiries.php'>Admin Gym Inquiries Page</a></li>";
        echo "</ul>";
        
    } else {
        echo "<p style='color:red;'>✗ Table 'gym_inquiries' does not exist.</p>";
        echo "<p><a href='setup-gym-inquiries.php'>Run Setup Script</a></p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color:red;'><strong>✗ Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
