<?php
/**
 * Verify and fix API key
 */

require_once __DIR__ . '/../config/database.php';

echo "=== Verifying API Key ===\n\n";

$testKey = 'test_key_12345';

// Get existing key from database
$stmt = $pdo->prepare("SELECT id, api_key, client_name FROM api_keys WHERE client_name = 'Test Client'");
$stmt->execute();
$keyData = $stmt->fetch(PDO::FETCH_ASSOC);

if ($keyData) {
    echo "Found existing API key:\n";
    echo "  ID: " . $keyData['id'] . "\n";
    echo "  Client: " . $keyData['client_name'] . "\n";
    echo "  Stored Hash: " . substr($keyData['api_key'], 0, 20) . "...\n\n";
    
    // Test if the key verifies
    $verified = password_verify($testKey, $keyData['api_key']);
    echo "Verification test: " . ($verified ? "✓ PASS" : "✗ FAIL") . "\n\n";
    
    if (!$verified) {
        echo "The stored hash doesn't match. Updating...\n";
        
        // Generate new hash
        $newHash = password_hash($testKey, PASSWORD_DEFAULT);
        echo "New hash: " . substr($newHash, 0, 20) . "...\n";
        
        // Update database
        $updateStmt = $pdo->prepare("UPDATE api_keys SET api_key = ? WHERE id = ?");
        $updateStmt->execute([$newHash, $keyData['id']]);
        
        echo "✓ API key hash updated successfully\n\n";
        
        // Verify the update
        $verified = password_verify($testKey, $newHash);
        echo "New verification test: " . ($verified ? "✓ PASS" : "✗ FAIL") . "\n";
    }
} else {
    echo "No API key found. Creating new one...\n";
    
    $newHash = password_hash($testKey, PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("INSERT INTO api_keys (api_key, client_name, client_website, client_email, permissions, rate_limit_per_hour, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $newHash,
        'Test Client',
        'https://example.com',
        'test@example.com',
        json_encode(['rooms.read', 'availability.check', 'bookings.create', 'bookings.read']),
        1000,
        1
    ]);
    
    echo "✓ New API key created\n";
}

echo "\n=== Verification Complete ===\n";
echo "\nTest API Key: $testKey\n";
echo "You can now run: php api/test-api-cli.php\n\n";