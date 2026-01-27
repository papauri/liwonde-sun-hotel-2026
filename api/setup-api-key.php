<?php
/**
 * Simple script to set up the API key in the database
 */

require_once __DIR__ . '/../config/database.php';

echo "=== Setting Up API Key ===\n\n";

// Check if api_keys table exists
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'api_keys'");
    $tableExists = $stmt->rowCount() > 0;
    
    if (!$tableExists) {
        echo "Creating api_keys table...\n";
        
        $sql = "CREATE TABLE IF NOT EXISTS `api_keys` (
            `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
            `api_key` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Hashed API key',
            `client_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Name of the client/website using the API',
            `client_website` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Website URL of the client',
            `client_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Contact email for the client',
            `permissions` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'JSON array of permissions',
            `rate_limit_per_hour` int NOT NULL DEFAULT 100 COMMENT 'Maximum API calls per hour',
            `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Whether the API key is active',
            `last_used_at` timestamp NULL DEFAULT NULL COMMENT 'Last time the API key was used',
            `usage_count` int UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Total number of API calls made',
            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `api_key` (`api_key`),
            KEY `idx_client_name` (`client_name`),
            KEY `idx_is_active` (`is_active`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $pdo->exec($sql);
        echo "✓ api_keys table created\n\n";
    } else {
        echo "✓ api_keys table already exists\n\n";
    }
    
    // Check if api_usage_logs table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'api_usage_logs'");
    $tableExists = $stmt->rowCount() > 0;
    
    if (!$tableExists) {
        echo "Creating api_usage_logs table...\n";
        
        $sql = "CREATE TABLE IF NOT EXISTS `api_usage_logs` (
            `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
            `api_key_id` int UNSIGNED NOT NULL,
            `endpoint` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'API endpoint called',
            `method` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'HTTP method',
            `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Client IP address',
            `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Client user agent',
            `response_code` int NOT NULL COMMENT 'HTTP response code',
            `response_time` decimal(10,4) NOT NULL COMMENT 'Response time in seconds',
            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `idx_api_key_id` (`api_key_id`),
            KEY `idx_endpoint` (`endpoint`),
            KEY `idx_created_at` (`created_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $pdo->exec($sql);
        echo "✓ api_usage_logs table created\n\n";
    } else {
        echo "✓ api_usage_logs table already exists\n\n";
    }
    
} catch (PDOException $e) {
    die("Error creating tables: " . $e->getMessage() . "\n");
}

// Check if test API key already exists
echo "Checking for test API key...\n";
$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM api_keys WHERE client_name = 'Test Client'");
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result['count'] > 0) {
    echo "⚠ Test API key already exists\n";
    
    // Show existing keys
    $stmt = $pdo->query("SELECT id, client_name, client_email, is_active FROM api_keys");
    $keys = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "\nExisting API Keys:\n";
    echo str_repeat("-", 80) . "\n";
    echo sprintf("%-5s | %-20s | %-30s | %-10s\n", "ID", "Client Name", "Email", "Active");
    echo str_repeat("-", 80) . "\n";
    
    foreach ($keys as $key) {
        echo sprintf("%-5s | %-20s | %-30s | %-10s\n", 
            $key['id'], 
            $key['client_name'], 
            $key['client_email'], 
            $key['is_active'] ? 'Yes' : 'No'
        );
    }
    
} else {
    // Insert test API key
    echo "Inserting test API key...\n";
    
    $apiKey = 'test_key_12345';
    $hashedKey = password_hash($apiKey, PASSWORD_DEFAULT);
    
    $sql = "INSERT INTO api_keys (
        api_key,
        client_name,
        client_website,
        client_email,
        permissions,
        rate_limit_per_hour,
        is_active
    ) VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);
    
    $permissions = json_encode(['rooms.read', 'availability.check', 'bookings.create', 'bookings.read']);
    
    $stmt->execute([
        $hashedKey,
        'Test Client',
        'https://example.com',
        'test@example.com',
        $permissions,
        1000,
        1
    ]);
    
    echo "✓ Test API key inserted successfully\n";
    echo "\nAPI Key Details:\n";
    echo "  Key: $apiKey\n";
    echo "  Client: Test Client\n";
    echo "  Permissions: rooms.read, availability.check, bookings.create, bookings.read\n";
    echo "  Rate Limit: 1000 calls/hour\n";
    echo "  Status: Active\n";
}

echo "\n=== Setup Complete ===\n";
echo "\nYou can now run the test script:\n";
echo "  php api/test-api-cli.php\n";
echo "\nOr use the API key: test_key_12345\n\n";