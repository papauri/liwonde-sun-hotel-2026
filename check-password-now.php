<?php
/**
 * Check if password has been set
 */

require_once 'config/database.php';

echo "Checking current email settings...\n\n";

try {
    $email_settings = getAllEmailSettings();
    
    echo "Current SMTP Configuration:\n";
    echo str_repeat("-", 50) . "\n";
    
    $host = $email_settings['smtp_host']['value'] ?? '';
    $port = $email_settings['smtp_port']['value'] ?? '';
    $username = $email_settings['smtp_username']['value'] ?? '';
    $password = $email_settings['smtp_password']['value'] ?? '';
    
    echo "Host: $host\n";
    echo "Port: $port\n";
    echo "Username: $username\n";
    
    if (empty($password)) {
        echo "Password: ❌ EMPTY (not set)\n";
        echo "\nThe password is still empty! You need to set it.\n";
    } else {
        echo "Password: ✅ SET (encrypted, length: " . strlen($password) . ")\n";
        
        // Try to retrieve it
        $retrieved = getEmailSetting('smtp_password', '');
        if (empty($retrieved)) {
            echo "\n⚠️  WARNING: Password retrieval failed!\n";
            echo "The password is stored but cannot be decrypted.\n";
            echo "Check the MySQL decrypt_setting() function.\n";
        } else {
            echo "\n✅ Password retrieval successful!\n";
            echo "Retrieved password length: " . strlen($retrieved) . "\n";
            
            // Test the configuration
            echo "\nTesting email configuration...\n";
            require_once 'config/email.php';
            
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = getEmailSetting('smtp_host', '');
            $mail->Port = (int)getEmailSetting('smtp_port', 465);
            $mail->SMTPAuth = true;
            $mail->Username = getEmailSetting('smtp_username', '');
            $mail->Password = getEmailSetting('smtp_password', '');
            $mail->SMTPSecure = getEmailSetting('smtp_secure', 'ssl');
            
            echo "PHPMailer configuration loaded:\n";
            echo "- Host: " . $mail->Host . "\n";
            echo "- Port: " . $mail->Port . "\n";
            echo "- Username: " . $mail->Username . "\n";
            echo "- Password: " . (!empty($mail->Password) ? '✅ Loaded' : '❌ Empty') . "\n";
            echo "- Secure: " . $mail->SMTPSecure . "\n";
            
            if (!empty($mail->Password)) {
                echo "\n✅ Email configuration is ready!\n";
                echo "You can test with: simple-smtp-test.php\n";
            }
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\nDone.\n";
?>