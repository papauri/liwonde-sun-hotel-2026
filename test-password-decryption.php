<?php
/**
 * Test password decryption with the new MySQL functions
 */

require_once 'config/database.php';

echo "Testing password decryption with new MySQL functions...\n\n";

try {
    // Get the encrypted password from database
    $stmt = $pdo->prepare("SELECT setting_value FROM email_settings WHERE setting_key = ?");
    $stmt->execute(['smtp_password']);
    $result = $stmt->fetch();
    
    if (!$result || empty($result['setting_value'])) {
        echo "❌ No password found in database\n";
        exit;
    }
    
    $encryptedPassword = $result['setting_value'];
    echo "Encrypted password from database: '$encryptedPassword'\n";
    echo "Password length: " . strlen($encryptedPassword) . "\n";
    
    // Try to decrypt it using the MySQL function directly
    $encryptionKey = 'email_encryption_key_' . DB_NAME;
    
    $decryptStmt = $pdo->prepare("SELECT decrypt_setting(?, ?) as decrypted");
    $decryptStmt->execute([$encryptedPassword, $encryptionKey]);
    $decryptResult = $decryptStmt->fetch();
    
    if ($decryptResult && !empty($decryptResult['decrypted'])) {
        $decryptedPassword = $decryptResult['decrypted'];
        echo "✅ Password decryption successful!\n";
        echo "Decrypted password: '$decryptedPassword'\n";
        echo "Decrypted length: " . strlen($decryptedPassword) . "\n";
        
        // Check if it's the placeholder
        if ($decryptedPassword === 'YOUR_EMAIL_PASSWORD_HERE') {
            echo "\n⚠️  ⚠️  ⚠️  CRITICAL ISSUE FOUND!\n";
            echo "The password in the database is still the default placeholder: 'YOUR_EMAIL_PASSWORD_HERE'\n";
            echo "This is why the SMTP test is failing!\n";
            echo "\nYou MUST update this with your actual email password.\n";
            echo "\nTo fix this:\n";
            echo "1. Go to: /admin/booking-settings.php\n";
            echo "2. Find the 'SMTP Password' field\n";
            echo "3. Enter your actual email password (not the placeholder)\n";
            echo "4. Click 'Save Email Settings'\n";
            echo "5. Test again with: simple-smtp-test.php\n";
        } else {
            echo "\n✅ The password appears to be a real password (not a placeholder).\n";
            echo "If SMTP is still failing, the password might be incorrect or the email account has issues.\n";
        }
    } else {
        echo "❌ Password decryption failed or returned empty\n";
        echo "The encrypted password might be corrupted or using a different encryption key.\n";
        
        // Try to see what version it is
        $version = substr($encryptedPassword, 0, strpos($encryptedPassword, ':'));
        echo "Encryption version: '$version'\n";
        
        if ($version === '2') {
            echo "The password is encrypted with version 2 encryption.\n";
            echo "The issue might be with the encryption key.\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\nDone.\n";
?>