<?php
/**
 * Direct Gym Inquiries Table Creation
 */

require_once '../config/database.php';

echo "<h2>Gym Inquiries Table Setup</h2>";
echo "<p>Creating gym_inquiries table...</p>";

try {
    // Drop table if exists
    $pdo->exec("DROP TABLE IF EXISTS gym_inquiries");
    echo "<p>Dropped any existing table...</p>";
    
    // Create table directly
    $createTableSQL = "
    CREATE TABLE gym_inquiries (
        id INT(11) NOT NULL AUTO_INCREMENT,
        reference_number VARCHAR(20) NOT NULL UNIQUE,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        phone VARCHAR(50) NOT NULL,
        membership_type VARCHAR(100) DEFAULT NULL,
        preferred_date DATE DEFAULT NULL,
        preferred_time TIME DEFAULT NULL,
        guests INT(11) DEFAULT 1,
        message TEXT DEFAULT NULL,
        consent TINYINT(1) NOT NULL DEFAULT 0,
        status VARCHAR(50) NOT NULL DEFAULT 'new',
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY reference_number (reference_number),
        KEY idx_status (status),
        KEY idx_created_at (created_at),
        KEY idx_email (email)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($createTableSQL);
    
    echo "<p style='color:green;'><strong>✓ Success!</strong> Gym inquiries table created successfully.</p>";
    
    // Verify table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'gym_inquiries'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color:green;'>✓ Table 'gym_inquiries' verified in database.</p>";
        
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
    }
    
    echo "<hr>";
    echo "<p><strong>Next Steps:</strong></p>";
    echo "<ol>";
    echo "<li>Delete this setup file for security</li>";
    echo "<li>Test the gym booking form at <a href='../gym.php'>gym.php</a></li>";
    echo "<li>View gym inquiries in admin at <a href='../admin/gym-inquiries.php'>admin/gym-inquiries.php</a></li>";
    echo "</ol>";
    
} catch (PDOException $e) {
    echo "<p style='color:red;'><strong>✗ Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
