<?php
/**
 * Simple SMTP Test
 * Basic script to test SMTP authentication without complex PHPMailer calls
 * Uses database-based email configuration
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

// Get SMTP Configuration from database - NO HARCODED DEFAULTS
$smtp_host = getEmailSetting('smtp_host', '');
$smtp_port = (int)getEmailSetting('smtp_port', 0);
$smtp_username = getEmailSetting('smtp_username', '');
$smtp_password = getEmailSetting('smtp_password', '');
$smtp_secure = getEmailSetting('smtp_secure', '');
$email_from_name = getEmailSetting('email_from_name', '');
$email_from_email = getEmailSetting('email_from_email', '');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple SMTP Test</title>
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
        pre {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Simple SMTP Test</h1>

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

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            
            echo '<div style="margin-top: 20px;">';
            
            if ($action === 'test_connection') {
                echo '<h3>Testing SMTP Connection...</h3>';
                
                if (empty($smtp_password)) {
                    echo '<div class="status error"><strong>Failed:</strong> SMTP password is not configured. Please update email settings in the admin panel.</div>';
                } else {
                    try {
                        echo '<div class="info"><strong>Debug Output:</strong></div>';
                        echo '<pre>';
                        
                        // Create a new PHPMailer instance
                        $mail = new PHPMailer(true);
                        
                        // Enable SMTP debugging
                        $mail->SMTPDebug = SMTP::DEBUG_CONNECTION;
                        $mail->Debugoutput = function($str, $level) {
                            echo htmlspecialchars($str) . "\n";
                        };
                        
                        // Set up SMTP
                        $mail->isSMTP();
                        $mail->Host = $smtp_host;
                        $mail->SMTPAuth = true;
                        $mail->Username = $smtp_username;
                        $mail->Password = $smtp_password;
                        $mail->SMTPSecure = $smtp_secure;
                        $mail->Port = $smtp_port;
                        $mail->Timeout = 10;
                        
                        // Try to send a test email
                        $mail->setFrom($smtp_username, 'SMTP Test');
                        $mail->addAddress($smtp_username, 'Test'); // Send to ourselves
                        $mail->Subject = 'SMTP Connection Test';
                        $mail->Body = 'This is a test email to verify SMTP connection.';
                        
                        if ($mail->send()) {
                            echo '</pre>';
                            echo '<div class="status success"><strong>✓ Success:</strong> SMTP connection and authentication successful! Test email sent.</div>';
                        } else {
                            echo '</pre>';
                            echo '<div class="status error"><strong>✗ Failed:</strong> ' . htmlspecialchars($mail->ErrorInfo) . '</div>';
                        }
                        
                    } catch (Exception $e) {
                        echo '</pre>';
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

        <form method="POST" action="simple-smtp-test.php">
            <button type="submit" name="action" value="test_connection" class="btn">
                Test SMTP Connection
            </button>
            
            <div style="margin-top: 20px;">
                <label for="test_email" style="font-weight: bold;">Send Test Email To:</label>
                <input type="email" id="test_email" name="test_email" 
                       placeholder="your-email@example.com" 
                       style="padding: 10px; width: 300px; border: 2px solid #ddd; border-radius: 5px;">
                <button type="submit" name="action" value="send_test_email" class="btn btn-test">
                    Send Test Email
                </button>
            </div>
        </form>

        <div class="info">
            <h3>📋 How to Configure SMTP Settings</h3>
            <ol style="margin: 10px 0 10px 20px; line-height: 1.8;">
                <li>Go to the <a href="admin/login.php" target="_blank">Admin Panel</a> (login required)</li>
                <li>Navigate to <strong>Booking Settings</strong> → <strong>Email Configuration</strong></li>
                <li>Update your SMTP settings (host, port, username, password)</li>
                <li>Click "Save Email Settings"</li>
                <li>Return here and click "Test SMTP Connection" button above</li>
                <li>If it works, test the full email system using <a href="test-email.php">test-email.php</a></li>
            </ol>
            
            <h4>Common Issues:</h4>
            <ul style="margin: 10px 0 10px 20px;">
                <li><strong>Using regular password:</strong> Some email providers require an "App Password"</li>
                <li><strong>Account disabled:</strong> Verify the email account is active</li>
                <li><strong>Wrong SMTP settings:</strong> Check with your hosting provider</li>
                <li><strong>Firewall blocking:</strong> Port 465 (SSL) or 587 (TLS) might be blocked</li>
            </ul>
        </div>

        <div class="info">
            <h3>🔒 Security Note</h3>
            <p><strong>All email settings are now stored securely in the database.</strong> No more hardcoded passwords in files. 
            Your SMTP password is encrypted for security. You can update it anytime in the admin panel.</p>
        </div>
    </div>
</body>
</html>