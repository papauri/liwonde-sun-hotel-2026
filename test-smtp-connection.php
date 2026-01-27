<?php
/**
 * SMTP Connection Test
 * Simple script to test SMTP authentication and connection
 */

require_once 'config/database.php';

// Load PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load PHPMailer from local directory
if (file_exists(__DIR__ . '/PHPMailer/src/PHPMailer.php')) {
    require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
    require_once __DIR__ . '/PHPMailer/src/SMTP.php';
    require_once __DIR__ . '/PHPMailer/src/Exception.php';
} elseif (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

// SMTP Configuration
$smtp_host = 'mail.promanaged-it.com';
$smtp_port = 465;
$smtp_username = 'info@promanaged-it.com';
$smtp_secure = 'ssl';

// Get SMTP password from secure file
$smtp_password = '';
if (file_exists(__DIR__ . '/config/email-password.php')) {
    $smtp_password = include __DIR__ . '/config/email-password.php';
    if (!is_string($smtp_password) || $smtp_password === 'YOUR_EMAIL_PASSWORD_HERE') {
        $smtp_password = '';
    }
}

$test_email = 'test@example.com'; // Change this to your test email
$test_recipient = 'test@example.com'; // Change this to your actual email

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMTP Connection Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #0A1929;
            border-bottom: 3px solid #D4AF37;
            padding-bottom: 10px;
        }
        .status {
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
            border-left: 4px solid #17a2b8;
            padding: 15px;
            margin: 15px 0;
            border-radius: 4px;
        }
        .config-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .config-section h3 {
            margin-top: 0;
            color: #0A1929;
        }
        .config-item {
            margin: 10px 0;
            padding: 10px;
            background: white;
            border-radius: 4px;
        }
        .config-label {
            font-weight: bold;
            color: #666;
        }
        .config-value {
            font-family: monospace;
            color: #D4AF37;
        }
        .password-hidden {
            color: #666;
            font-style: italic;
        }
        .btn {
            background: #0A1929;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin: 10px 5px 10px 0;
        }
        .btn:hover {
            background: #D4AF37;
        }
        .btn-test {
            background: #28a745;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-envelope"></i> SMTP Connection Test</h1>

        <div class="config-section">
            <h3>Current Configuration</h3>
            <div class="config-item">
                <div class="config-label">SMTP Host:</div>
                <div class="config-value"><?php echo htmlspecialchars($smtp_host); ?></div>
            </div>
            <div class="config-item">
                <div class="config-label">SMTP Port:</div>
                <div class="config-value"><?php echo $smtp_port; ?></div>
            </div>
            <div class="config-item">
                <div class="config-label">SMTP Secure:</div>
                <div class="config-value"><?php echo htmlspecialchars($smtp_secure); ?></div>
            </div>
            <div class="config-item">
                <div class="config-label">SMTP Username:</div>
                <div class="config-value"><?php echo htmlspecialchars($smtp_username); ?></div>
            </div>
            <div class="config-item">
                <div class="config-label">SMTP Password:</div>
                <div class="config-value <?php echo empty($smtp_password) ? 'password-hidden' : ''; ?>">
                    <?php 
                    if (empty($smtp_password) || $smtp_password === 'YOUR_EMAIL_PASSWORD_HERE') {
                        echo 'Not configured (placeholder password)';
                    } else {
                        echo '*** CONFIGURED ***';
                    }
                    ?>
                </div>
            </div>
        </div>

        <div class="info">
            <h3>⚠️ SMTP Authentication Error</h3>
            <p><strong>Error:</strong> "Could not authenticate" - This means the SMTP username or password is incorrect.</p>
            <p><strong>Solution:</strong> Please update the SMTP password in <code>config/email-password.php</code></p>
        </div>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            
            echo '<div style="margin-top: 20px;">';
            
            if ($action === 'test_connection') {
                echo '<h3>Testing SMTP Connection...</h3>';
                
                if (empty($smtp_password)) {
                    echo '<div class="status error"><strong>Failed:</strong> SMTP password is not configured. Please update config/email-password.php</div>';
                } else {
                    try {
                        $mail = new PHPMailer(true);
                        $mail->isSMTP();
                        $mail->Host = $smtp_host;
                        $mail->SMTPAuth = true;
                        $mail->Username = $smtp_username;
                        $mail->Password = $smtp_password;
                        $mail->SMTPSecure = $smtp_secure;
                        $mail->Port = $smtp_port;
                        
                        // Test authentication without sending email
                        $mail->SMTPDebug = 3;
                        $mail->Debugoutput = 'html';
                        
                        echo '<div class="status error"><strong>Debug Output:</strong><pre style="margin: 10px 0; padding: 10px; background: #fff; border-radius: 4px;">';
                        
                        // This will attempt to connect and authenticate
                        ob_start();
                        try {
                            // Set up the mail object
                            $mail->setFrom($smtp_username, 'SMTP Test');
                            
                            // Try to verify connection by connecting to SMTP
                            // We'll use the smtpConnect method which handles everything
                            if (!$mail->smtpConnect()) {
                                throw new Exception('Failed to connect to SMTP server');
                            }
                            
                            // Test authentication by trying to send a simple command
                            $mail->getSMTPInstance()->hello('localhost');
                            
                        } catch (Exception $e) {
                            ob_end_clean();
                            throw $e;
                        }
                        ob_end_clean();
                        
                        echo '</pre></div>';
                        echo '<div class="status success"><strong>✓ Success:</strong> SMTP connection and authentication successful!</div>';
                        
                    } catch (Exception $e) {
                        echo '<div class="status error"><strong>✗ Failed:</strong> ' . htmlspecialchars($e->getMessage()) . '</div>';
                        echo '<div class="info"><strong>Troubleshooting Tips:</strong><ul style="margin: 10px 0 10px 20px;">';
                        echo '<li>Check that the email account exists and is active</li>';
                        echo '<li>Verify the password is correct (not a placeholder)</li>';
                        echo '<li>Check if the email provider requires "App Password" instead of regular password</li>';
                        echo '<li>Verify SMTP host and port are correct</li>';
                        echo '<li>Check firewall settings</li>';
                        echo '</ul></div>';
                    }
                }
            } elseif ($action === 'send_test_email') {
                $recipient = filter_var($_POST['test_email'], FILTER_VALIDATE_EMAIL);
                
                if (!$recipient) {
                    echo '<div class="status error"><strong>Error:</strong> Please enter a valid email address</div>';
                } elseif (empty($smtp_password)) {
                    echo '<div class="status error"><strong>Failed:</strong> SMTP password is not configured</div>';
                } else {
                    try {
                        $mail = new PHPMailer(true);
                        $mail->isSMTP();
                        $mail->Host = $smtp_host;
                        $mail->SMTPAuth = true;
                        $mail->Username = $smtp_username;
                        $mail->Password = $smtp_password;
                        $mail->SMTPSecure = $smtp_secure;
                        $mail->Port = $smtp_port;
                        
                        $mail->setFrom($smtp_username, 'SMTP Test');
                        $mail->addAddress($recipient, 'Test Recipient');
                        $mail->Subject = 'SMTP Test Email';
                        $mail->Body = '<h1>SMTP Test Successful!</h1><p>This is a test email from the Liwonde Sun Hotel booking system.</p>';
                        $mail->AltBody = 'SMTP Test Successful! This is a test email.';
                        
                        if ($mail->send()) {
                            echo '<div class="status success"><strong>✓ Success:</strong> Test email sent to ' . htmlspecialchars($recipient) . ' successfully!</div>';
                        } else {
                            throw new Exception($mail->ErrorInfo);
                        }
                    } catch (Exception $e) {
                        echo '<div class="status error"><strong>✗ Failed:</strong> ' . htmlspecialchars($e->getMessage()) . '</div>';
                    }
                }
            }
            
            echo '</div>';
        }
        ?>

        <form method="POST" action="test-smtp-connection.php">
            <button type="submit" name="action" value="test_connection" class="btn">
                <i class="fas fa-plug"></i> Test SMTP Connection
            </button>
            
            <div style="margin-top: 20px;">
                <label for="test_email" style="font-weight: bold;">Send Test Email To:</label>
                <input type="email" id="test_email" name="test_email" 
                       placeholder="your-email@example.com" 
                       style="padding: 10px; width: 300px; border: 2px solid #ddd; border-radius: 5px;">
                <button type="submit" name="action" value="send_test_email" class="btn btn-test">
                    <i class="fas fa-paper-plane"></i> Send Test Email
                </button>
            </div>
        </form>

        <div class="info">
            <h3>📋 Next Steps</h3>
            <ol style="margin: 10px 0 10px 20px; line-height: 1.8;">
                <li>Open <code>config/email-password.php</code></li>
                <li>Replace the password with the correct SMTP password for <code>info@promanaged-it.com</code></li>
                <li>Save the file</li>
                <li>Click "Test SMTP Connection" button above</li>
                <li>Then test the full email system using <a href="test-email.php">test-email.php</a></li>
            </ol>
            <p><strong>Note:</strong> If you're using Gmail or similar service, you may need to generate an "App Password" instead of using your regular password.</p>
        </div>

        <div class="info">
            <h3>🔒 Security Note</h3>
            <p>The <code>config/email-password.php</code> file should <strong>never</strong> be committed to version control. It's already in <code>.gitignore</code> but double-check this.</p>
        </div>
    </div>
</body>
</html>