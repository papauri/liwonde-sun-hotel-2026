<?php
/**
 * Gym Inquiries Table Setup Script
 * Run this file once to create the gym_inquiries table
 */

// Load database configuration
require_once '../config/database.php';

echo "<h2>Gym Inquiries Table Setup</h2>";
echo "<p>Creating gym_inquiries table...</p>";

try {
    // Drop table if it exists (to handle partial creations)
    $pdo->exec("DROP TABLE IF EXISTS gym_inquiries");
    echo "<p>Dropped any existing gym_inquiries table...</p>";
    
    // Read the SQL file
    $sqlFile = __DIR__ . '/add-gym-inquiries-table.sql';
    if (!file_exists($sqlFile)) {
        die("<p style='color:red;'>Error: SQL file not found at $sqlFile</p>");
    }
    
    $sql = file_get_contents($sqlFile);
    
    // Remove comments and split into statements
    $sql = preg_replace('/--.*$/m', '', $sql);
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    $pdo->beginTransaction();
    
    foreach ($statements as $statement) {
        if (!empty($statement) && stripos($statement, 'CREATE TABLE') !== false) {
            // Remove IF NOT EXISTS for clean creation
            $statement = str_replace('IF NOT EXISTS', '', $statement);
            $pdo->exec($statement);
        }
    }
    
    $pdo->commit();
    
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
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "<p style='color:red;'><strong>✗ Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Check if the table already exists or there's a database connection issue.</p>";
}
?>
