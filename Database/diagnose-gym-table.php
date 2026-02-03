<?php
/**
 * Diagnose Gym Inquiries Table Issue
 */

require_once '../config/database.php';

echo "<h2>Gym Inquiries Table Diagnostics</h2>";

try {
    // Check if table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'gym_inquiries'");
    $tableExists = $stmt->rowCount() > 0;
    
    echo "<h3>Step 1: Check if table exists</h3>";
    if ($tableExists) {
        echo "<p style='color:orange;'>⚠ Table 'gym_inquiries' already exists</p>";
        
        // Show table structure
        echo "<h3>Current Table Structure:</h3>";
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
        
        // Show indexes
        echo "<h3>Current Indexes:</h3>";
        $indexes = $pdo->query("SHOW INDEX FROM gym_inquiries")->fetchAll(PDO::FETCH_ASSOC);
        echo "<table border='1' cellpadding='8' style='border-collapse:collapse;'>";
        echo "<tr><th>Key_name</th><th>Column_name</th><th>Index_type</th></tr>";
        foreach ($indexes as $idx) {
            echo "<tr>";
            echo "<td>{$idx['Key_name']}</td>";
            echo "<td>{$idx['Column_name']}</td>";
            echo "<td>{$idx['Index_type']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        echo "<hr>";
        echo "<p><strong>The table already exists and appears to be working!</strong></p>";
        echo "<p>You can proceed with testing the gym inquiries system.</p>";
        echo "<ul>";
        echo "<li><a href='../gym.php'>Test Gym Booking Form</a></li>";
        echo "<li><a href='../admin/gym-inquiries.php'>Admin Gym Inquiries Page</a></li>";
        echo "</ul>";
        
    } else {
        echo "<p style='color:red;'>✗ Table 'gym_inquiries' does not exist</p>";
        
        echo "<h3>Step 2: Attempt to create table</h3>";
        
        // Try to create table
        $createTableSQL = "
        CREATE TABLE gym_inquiries (
            id INT(11) NOT NULL AUTO_INCREMENT,
            reference_number VARCHAR(20) NOT NULL,
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
            UNIQUE KEY ref_number (reference_number),
            KEY idx_status (status),
            KEY idx_created_at (created_at),
            KEY idx_email (email)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        try {
            $pdo->exec($createTableSQL);
            echo "<p style='color:green;'>✓ Table created successfully!</p>";
            
            // Verify
            $stmt = $pdo->query("SHOW TABLES LIKE 'gym_inquiries'");
            if ($stmt->rowCount() > 0) {
                echo "<p style='color:green;'>✓ Table verified in database.</p>";
            }
            
            echo "<ul>";
            echo "<li><a href='../gym.php'>Test Gym Booking Form</a></li>";
            echo "<li><a href='../admin/gym-inquiries.php'>Admin Gym Inquiries Page</a></li>";
            echo "</ul>";
            
        } catch (PDOException $e) {
            echo "<p style='color:red;'>✗ Failed to create table: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }
    
} catch (PDOException $e) {
    echo "<p style='color:red;'><strong>✗ Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
