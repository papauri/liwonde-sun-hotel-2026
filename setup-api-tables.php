<?php
/**
 * Setup script for API tables
 * Run this script to create the necessary database tables for the API
 */

require_once 'config/database.php';

echo "<h1>Setting up API Database Tables</h1>";

// Read SQL file
$sqlFile = 'Database/add-api-keys-table.sql';
if (!file_exists($sqlFile)) {
    die("<p style='color: red;'>SQL file not found: $sqlFile</p>");
}

$sql = file_get_contents($sqlFile);

// Split SQL statements
$statements = array_filter(array_map('trim', explode(';', $sql)));

$successCount = 0;
$errorCount = 0;

foreach ($statements as $statement) {
    if (empty($statement)) {
        continue;
    }
    
    echo "<p><strong>Executing:</strong> " . substr($statement, 0, 100) . "...</p>";
    
    try {
        $pdo->exec($statement);
        echo "<p style='color: green;'>✓ Success</p>";
        $successCount++;
    } catch (PDOException $e) {
        echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
        $errorCount++;
    }
    
    echo "<hr>";
}

echo "<h2>Setup Complete</h2>";
echo "<p><strong>Successful statements:</strong> $successCount</p>";
echo "<p><strong>Errors:</strong> $errorCount</p>";

if ($errorCount === 0) {
    echo "<div style='background-color: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 20px 0;'>
            <h3>✅ API Tables Created Successfully!</h3>
            <p>The API system is now ready to use.</p>
            <ul>
                <li><a href='admin/api-keys.php' target='_blank'>Go to API Keys Management</a></li>
                <li><a href='api/test-api.php' target='_blank'>Test the API</a></li>
                <li><a href='api/' target='_blank'>View API Documentation</a></li>
            </ul>
          </div>";
} else {
    echo "<div style='background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 20px 0;'>
            <h3>⚠️ Some Errors Occurred</h3>
            <p>Please check the errors above. Some tables may already exist.</p>
            <p>You can still try to use the API system, but some features may not work correctly.</p>
          </div>";
}

echo "<h3>Next Steps:</h3>";
echo "<ol>
        <li>Visit <a href='admin/api-keys.php'>API Keys Management</a> to create your first API key</li>
        <li>Use the API key to test the endpoints</li>
        <li>Integrate the API with external websites</li>
      </ol>";

echo "<h3>Sample API Key (for testing):</h3>";
echo "<p>The SQL file includes a sample API key for testing:</p>";
echo "<ul>
        <li><strong>API Key:</strong> <code>test_key_12345</code></li>
        <li><strong>Client:</strong> Test Client</li>
        <li><strong>Permissions:</strong> rooms.read, availability.check, bookings.create, bookings.read</li>
        <li><strong>Rate Limit:</strong> 1000 calls/hour</li>
      </ul>";