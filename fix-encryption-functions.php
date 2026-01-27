<?php
/**
 * Fix encryption functions to match what PHP code expects
 */

require_once 'config/database.php';

echo "Fixing encryption functions...\n\n";

try {
    // Drop existing functions if they exist
    echo "Dropping existing functions...\n";
    try {
        $pdo->exec("DROP FUNCTION IF EXISTS encrypt_setting");
        echo "✅ Dropped encrypt_setting\n";
    } catch (Exception $e) {
        echo "⚠️  Could not drop encrypt_setting: " . $e->getMessage() . "\n";
    }
    
    try {
        $pdo->exec("DROP FUNCTION IF EXISTS decrypt_setting");
        echo "✅ Dropped decrypt_setting\n";
    } catch (Exception $e) {
        echo "⚠️  Could not drop decrypt_setting: " . $e->getMessage() . "\n";
    }
    
    echo "\nCreating fixed functions...\n";
    
    // Create encrypt_setting function WITHOUT key parameter (uses fixed key)
    $encrypt_sql = "
    CREATE FUNCTION encrypt_setting(p_value TEXT)
    RETURNS TEXT
    DETERMINISTIC
    BEGIN
        DECLARE v_encrypted TEXT;
        DECLARE v_salt VARCHAR(32);
        DECLARE v_key_hash VARCHAR(64);
        DECLARE v_key VARCHAR(255);
        
        -- Fixed encryption key (same as in PHP code)
        SET v_key = CONCAT('email_encryption_key_', DATABASE());
        
        -- Generate a random salt
        SET v_salt = SUBSTRING(MD5(RAND()), 1, 16);
        
        -- Create key hash from salt and key
        SET v_key_hash = SHA2(CONCAT(v_salt, v_key), 256);
        
        -- Encrypt using AES with salt prepended
        IF p_value IS NULL OR p_value = '' THEN
            RETURN NULL;
        ELSE
            SET v_encrypted = CONCAT('2:', v_salt, ':', TO_BASE64(AES_ENCRYPT(p_value, v_key_hash)));
            RETURN v_encrypted;
        END IF;
    END;
    ";
    
    $pdo->exec($encrypt_sql);
    echo "✅ Created encrypt_setting function\n";
    
    // Create decrypt_setting function WITHOUT key parameter (uses fixed key)
    $decrypt_sql = "
    CREATE FUNCTION decrypt_setting(p_encrypted TEXT)
    RETURNS TEXT
    DETERMINISTIC
    BEGIN
        DECLARE v_decrypted TEXT;
        DECLARE v_version VARCHAR(10);
        DECLARE v_salt VARCHAR(32);
        DECLARE v_encrypted_data TEXT;
        DECLARE v_key_hash VARCHAR(64);
        DECLARE v_key VARCHAR(255);
        
        -- Fixed encryption key (same as in PHP code)
        SET v_key = CONCAT('email_encryption_key_', DATABASE());
        
        -- Check if encrypted value is not empty
        IF p_encrypted IS NULL OR p_encrypted = '' THEN
            RETURN NULL;
        END IF;
        
        -- Parse version:salt:data format
        SET v_version = SUBSTRING_INDEX(p_encrypted, ':', 1);
        
        IF v_version = '2' THEN
            -- Version 2 format: 2:salt:base64_data
            SET v_salt = SUBSTRING_INDEX(SUBSTRING_INDEX(p_encrypted, ':', 2), ':', -1);
            SET v_encrypted_data = SUBSTRING(p_encrypted, LENGTH(v_version) + LENGTH(v_salt) + 3);
            
            -- Create key hash from salt and key
            SET v_key_hash = SHA2(CONCAT(v_salt, v_key), 256);
            
            -- Decrypt using AES
            SET v_decrypted = AES_DECRYPT(FROM_BASE64(v_encrypted_data), v_key_hash);
        ELSE
            -- Unknown version or plain text
            SET v_decrypted = p_encrypted;
        END IF;
        
        RETURN v_decrypted;
    END;
    ";
    
    $pdo->exec($decrypt_sql);
    echo "✅ Created decrypt_setting function\n";
    
    // Test the functions
    echo "\nTesting encryption/decryption...\n";
    
    $test_value = 'test_password_123';
    $stmt = $pdo->prepare("SELECT encrypt_setting(?) as encrypted, decrypt_setting(encrypt_setting(?)) as decrypted");
    $stmt->execute([$test_value, $test_value]);
    $result = $stmt->fetch();
    
    if ($result && $result['decrypted'] === $test_value) {
        echo "✅ Encryption/decryption test successful!\n";
        echo "Original: '$test_value'\n";
        echo "Encrypted: '" . substr($result['encrypted'], 0, 20) . "...'\n";
        echo "Decrypted: '" . $result['decrypted'] . "'\n";
    } else {
        echo "❌ Encryption/decryption test failed\n";
        if ($result) {
            echo "Expected: '$test_value'\n";
            echo "Got: '" . $result['decrypted'] . "'\n";
        }
    }
    
    // Now test with the actual password from database
    echo "\nTesting with actual database password...\n";
    
    $stmt = $pdo->prepare("SELECT setting_value FROM email_settings WHERE setting_key = 'smtp_password'");
    $stmt->execute();
    $db_result = $stmt->fetch();
    
    if ($db_result && !empty($db_result['setting_value'])) {
        $encrypted_password = $db_result['setting_value'];
        echo "Encrypted password: '$encrypted_password'\n";
        
        $decrypt_stmt = $pdo->prepare("SELECT decrypt_setting(?) as decrypted");
        $decrypt_stmt->execute([$encrypted_password]);
        $decrypt_result = $decrypt_stmt->fetch();
        
        if ($decrypt_result && !empty($decrypt_result['decrypted'])) {
            $decrypted = $decrypt_result['decrypted'];
            echo "✅ Decryption successful!\n";
            echo "Decrypted: '$decrypted'\n";
            
            if ($decrypted === 'YOUR_EMAIL_PASSWORD_HERE') {
                echo "\n⚠️  ⚠️  ⚠️  CRITICAL: Password is still the default placeholder!\n";
                echo "You MUST update the SMTP password in the admin panel.\n";
                echo "Go to: /admin/booking-settings.php\n";
            }
        } else {
            echo "❌ Decryption failed for existing password\n";
            echo "The password might be corrupted or using different encryption.\n";
            echo "You may need to reset the password in the admin panel.\n";
        }
    } else {
        echo "❌ No password found in database\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\nDone.\n";
?>