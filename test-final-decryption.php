<?php
/**
 * Final test of password decryption
 */

require_once 'config/database.php';

echo "Final test of password decryption...\n\n";

try {
    // Test 1: Check if functions exist
    echo "Test 1: Checking if functions exist...\n";
    $functions = ['encrypt_setting', 'decrypt_setting'];
    foreach ($functions as $func) {
        $stmt = $pdo->query("SHOW FUNCTION STATUS WHERE Db = DATABASE() AND Name = '$func'");
        if ($stmt->rowCount() > 0) {
            echo "✅ $func function exists\n";
        } else {
            echo "❌ $func function does NOT exist\n";
        }
    }
    
    // Test 2: Test basic encryption/decryption
    echo "\nTest 2: Testing basic encryption/decryption...\n";
    $test_value = 'test123';
    $stmt = $pdo->prepare("SELECT encrypt_setting(?) as encrypted, decrypt_setting(encrypt_setting(?)) as decrypted");
    $stmt->execute([$test_value, $test_value]);
    $result = $stmt->fetch();
    
    if ($result && $result['decrypted'] === $test_value) {
        echo "✅ Basic encryption/decryption works!\n";
    } else {
        echo "❌ Basic encryption/decryption failed\n";
    }
    
    // Test 3: Test with actual database password
    echo "\nTest 3: Testing actual database password...\n";
    $stmt = $pdo->prepare("SELECT setting_value FROM email_settings WHERE setting_key = 'smtp_password'");
    $stmt->execute();
    $db_result = $stmt->fetch();
    
    if ($db_result && !empty($db_result['setting_value'])) {
        $encrypted = $db_result['setting_value'];
        echo "Encrypted password in DB: '$encrypted'\n";
        
        // Try to decrypt
        $decrypt_stmt = $pdo->prepare("SELECT decrypt_setting(?) as decrypted");
        $decrypt_stmt->execute([$encrypted]);
        $decrypt_result = $decrypt_stmt->fetch();
        
        if ($decrypt_result && !empty($decrypt_result['decrypted'])) {
            $decrypted = $decrypt_result['decrypted'];
            echo "✅ Decryption successful!\n";
            echo "Decrypted password: '$decrypted'\n";
            
            if ($decrypted === 'YOUR_EMAIL_PASSWORD_HERE') {
                echo "\n⚠️  ⚠️  ⚠️  IMPORTANT: The password is still the default placeholder!\n";
                echo "This is why the SMTP test is failing.\n";
                echo "\nACTION REQUIRED:\n";
                echo "1. Go to: /admin/booking-settings.php\n";
                echo "2. Find 'SMTP Password' field\n";
                echo "3. Enter your ACTUAL email password (not the placeholder)\n";
                echo "4. Click 'Save Email Settings'\n";
                echo "5. Test again with: simple-smtp-test.php\n";
            } else {
                echo "\n✅ The password appears to be a real password.\n";
                echo "If SMTP is still failing, check:\n";
                echo "1. The password is correct for the email account\n";
                echo "2. The email account allows SMTP access\n";
                echo "3. No firewall is blocking port 465\n";
            }
        } else {
            echo "❌ Decryption failed\n";
            echo "The encrypted password might be corrupted.\n";
            echo "Solution: Update the password in the admin panel.\n";
        }
    } else {
        echo "❌ No password found in database\n";
        echo "You need to set the SMTP password in the admin panel.\n";
    }
    
    // Test 4: Test getEmailSetting() function
    echo "\nTest 4: Testing getEmailSetting() function...\n";
    $password_from_function = getEmailSetting('smtp_password', '');
    echo "getEmailSetting('smtp_password'): '$password_from_function'\n";
    echo "Length: " . strlen($password_from_function) . "\n";
    
    if (empty($password_from_function)) {
        echo "❌ getEmailSetting() returns empty string\n";
        echo "This confirms why the SMTP test shows 'password not configured'\n";
    } else {
        echo "✅ getEmailSetting() returns a value\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\nDone.\n";
?>