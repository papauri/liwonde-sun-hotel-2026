<?php
/**
 * Set SMTP password directly (for debugging)
 */

require_once 'config/database.php';

echo "Set SMTP Password Tool\n";
echo "=====================\n\n";

// Check if password is provided as argument
$new_password = '';
if (isset($argv[1])) {
    $new_password = $argv[1];
} elseif (isset($_GET['password'])) {
    $new_password = $_GET['password'];
}

if (empty($new_password)) {
    echo "Usage: php set-smtp-password.php \"your_password\"\n";
    echo "Or visit: set-smtp-password.php?password=your_password\n\n";
    
    echo "Current SMTP settings:\n";
    $email_settings = getAllEmailSettings();
    echo "Host: " . ($email_settings['smtp_host']['value'] ?? 'Not set') . "\n";
    echo "Port: " . ($email_settings['smtp_port']['value'] ?? 'Not set') . "\n";
    echo "Username: " . ($email_settings['smtp_username']['value'] ?? 'Not set') . "\n";
    echo "Password: " . (empty($email_settings['smtp_password']['value']) ? '❌ NOT SET' : '✅ Set') . "\n";
    
    exit;
}

echo "Setting SMTP password...\n";

try {
    // Update the password
    $result = updateEmailSetting('smtp_password', $new_password, 'SMTP authentication password (encrypted)', true);
    
    if ($result) {
        echo "✅ Password updated successfully!\n";
        
        // Verify it was saved
        $email_settings = getAllEmailSettings();
        $saved_password = $email_settings['smtp_password']['value'] ?? '';
        
        if (!empty($saved_password)) {
            echo "✅ Password is now stored in database (encrypted)\n";
            echo "Password length in DB: " . strlen($saved_password) . " characters\n";
            
            // Test if we can retrieve it
            $retrieved_password = getEmailSetting('smtp_password', '');
            if (!empty($retrieved_password)) {
                echo "✅ Password can be retrieved via getEmailSetting()\n";
                echo "Retrieved password length: " . strlen($retrieved_password) . " characters\n";
                
                // Test the actual email configuration
                echo "\nTesting email configuration...\n";
                require_once 'config/email.php';
                
                // Create a test PHPMailer instance
                $mail = new PHPMailer\PHPMailer\PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = getEmailSetting('smtp_host', '');
                $mail->Port = (int)getEmailSetting('smtp_port', 465);
                $mail->SMTPAuth = true;
                $mail->Username = getEmailSetting('smtp_username', '');
                $mail->Password = getEmailSetting('smtp_password', '');
                $mail->SMTPSecure = getEmailSetting('smtp_secure', 'ssl');
                
                echo "Configuration loaded:\n";
                echo "- Host: " . $mail->Host . "\n";
                echo "- Port: " . $mail->Port . "\n";
                echo "- Username: " . $mail->Username . "\n";
                echo "- Password: " . (!empty($mail->Password) ? '✅ Set' : '❌ Empty') . "\n";
                echo "- Secure: " . $mail->SMTPSecure . "\n";
                
                if (empty($mail->Password)) {
                    echo "\n⚠️  WARNING: Password is still empty after retrieval!\n";
                    echo "There might be an issue with the encryption/decryption.\n";
                } else {
                    echo "\n✅ Email configuration looks good!\n";
                    echo "You can now test with: simple-smtp-test.php\n";
                }
            } else {
                echo "❌ ERROR: Password retrieval failed!\n";
                echo "The password was saved but cannot be retrieved.\n";
                echo "Check the MySQL decrypt_setting() function.\n";
            }
        } else {
            echo "❌ ERROR: Password appears empty after saving!\n";
            echo "Check the updateEmailSetting() function.\n";
        }
    } else {
        echo "❌ ERROR: Failed to update password!\n";
        echo "Check database connection and permissions.\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\nDone.\n";
?>