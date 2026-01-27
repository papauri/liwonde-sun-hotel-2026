<?php
/**
 * Test Email Script - Database Configuration
 * Tests the new database-based email configuration system
 */

require_once 'config/database.php';
require_once 'config/email.php';

// Get current email settings for display
$email_settings = getAllEmailSettings();
$current_settings = [];
foreach ($email_settings as $key => $setting) {
    $current_settings[$key] = $setting['value'];
}

// Check if we're on localhost
$is_localhost = isset($_SERVER['HTTP_HOST']) && (
    strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || 
    strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false ||
    strpos($_SERVER['HTTP_HOST'], '.local') !== false
);

// Handle test email submission
$test_result = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_email'])) {
    $test_email = filter_var($_POST['test_email'], FILTER_VALIDATE_EMAIL);
    
    if ($test_email) {
        $subject = 'Test Email - Database Configuration';
        $htmlBody = '
        <h1 style="color: #0A1929; text-align: center;">✅ Email System Test</h1>
        <p>This is a test email to verify that the database-based email configuration is working correctly.</p>
        
        <div style="background: #f8f9fa; border: 2px solid #0A1929; padding: 20px; margin: 20px 0; border-radius: 10px;">
            <h2 style="color: #0A1929; margin-top: 0;">Configuration Details</h2>
            
            <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #ddd;">
                <span style="font-weight: bold; color: #0A1929;">Configuration Source:</span>
                <span style="color: #D4AF37; font-weight: bold;">Database</span>
            </div>
            
            <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #ddd;">
                <span style="font-weight: bold; color: #0A1929;">SMTP Host:</span>
                <span style="color: #333;">' . htmlspecialchars($current_settings['smtp_host'] ?? 'Not set') . '</span>
            </div>
            
            <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #ddd;">
                <span style="font-weight: bold; color: #0A1929;">SMTP Username:</span>
                <span style="color: #333;">' . htmlspecialchars($current_settings['smtp_username'] ?? 'Not set') . '</span>
            </div>
            
            <div style="display: flex; justify-content: space-between; padding: 10px 0;">
                <span style="font-weight: bold; color: #0A1929;">Development Mode:</span>
                <span style="color: #333;">' . (($current_settings['email_development_mode'] ?? '1') === '1' ? 'Enabled (Preview Only)' : 'Disabled (Sending Emails)') . '</span>
            </div>
        </div>
        
        <div style="background: #e3f2fd; padding: 15px; border-left: 4px solid #2196f3; border-radius: 5px; margin: 20px 0;">
            <h3 style="color: #1565c0; margin-top: 0;">System Status</h3>
            <p style="color: #1565c0; margin: 0;">
                <strong>✅ All email settings are now stored in the database.</strong><br>
                No more hardcoded passwords in files. Your SMTP password is encrypted for security.
            </p>
        </div>
        
        <p>If you received this email, the database-based email configuration is working correctly!</p>
        
        <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 2px solid #0A1929;">
            <p style="color: #666; font-size: 14px;">
                <strong>Liwonde Sun Hotel Email System</strong><br>
                Database Configuration Test
            </p>
        </div>';
        
        $test_result = sendEmail(
            $test_email,
            'Test Recipient',
            $subject,
            $htmlBody
        );
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Email - Database Configuration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 900px;
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
        .config-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        .config-item {
            background: white;
            padding: 15px;
            border-radius: 6px;
            border: 1px solid #e0e0e0;
        }
        .config-label {
            font-weight: bold;
            color: #666;
            font-size: 13px;
            margin-bottom: 5px;
        }
        .config-value {
            font-family: monospace;
            color: #D4AF37;
            word-break: break-all;
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
        .form-group {
            margin: 20px 0;
        }
        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
            color: #0A1929;
        }
        .form-group input {
            width: 100%;
            max-width: 400px;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        .warning {
            background: #fff3cd;
            color: #856404;
            border-left: 4px solid #ffc107;
            padding: 15px;
            border-radius: 4px;
            margin: 15px 0;
        }
        .success-badge {
            background: #28a745;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            display: inline-block;
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Test Email - Database Configuration</h1>
        
        <div class="info">
            <h3>✅ Database-Based Email Configuration</h3>
            <p>All email settings are now stored in the database. No more hardcoded passwords in files!</p>
            <p>Your SMTP password is encrypted for security. You can update all email settings in the admin panel.</p>
        </div>
        
        <?php if ($is_localhost): ?>
        <div class="warning">
            <h3>🖥️ Localhost Detected</h3>
            <p>You are running on localhost. Emails will be saved as preview files instead of being sent.</p>
            <p>Check the <code>logs/email-previews/</code> folder for email previews.</p>
        </div>
        <?php endif; ?>
        
        <div class="config-section">
            <h3>Current Email Configuration <span class="success-badge">FROM DATABASE</span></h3>
            <div class="config-grid">
                <div class="config-item">
                    <div class="config-label">SMTP Host</div>
                    <div class="config-value"><?php echo htmlspecialchars($current_settings['smtp_host'] ?? 'Not set'); ?></div>
                </div>
                <div class="config-item">
                    <div class="config-label">SMTP Port</div>
                    <div class="config-value"><?php echo htmlspecialchars($current_settings['smtp_port'] ?? 'Not set'); ?></div>
                </div>
                <div class="config-item">
                    <div class="config-label">SMTP Username</div>
                    <div class="config-value"><?php echo htmlspecialchars($current_settings['smtp_username'] ?? 'Not set'); ?></div>
                </div>
                <div class="config-item">
                    <div class="config-label">SMTP Password</div>
                    <div class="config-value <?php echo empty($current_settings['smtp_password'] ?? '') ? 'password-hidden' : ''; ?>">
                        <?php echo empty($current_settings['smtp_password'] ?? '') ? 'Not configured' : '*** ENCRYPTED ***'; ?>
                    </div>
                </div>
                <div class="config-item">
                    <div class="config-label">From Name</div>
                    <div class="config-value"><?php echo htmlspecialchars($current_settings['email_from_name'] ?? 'Not set'); ?></div>
                </div>
                <div class="config-item">
                    <div class="config-label">From Email</div>
                    <div class="config-value"><?php echo htmlspecialchars($current_settings['email_from_email'] ?? 'Not set'); ?></div>
                </div>
                <div class="config-item">
                    <div class="config-label">Development Mode</div>
                    <div class="config-value"><?php echo ($current_settings['email_development_mode'] ?? '1') === '1' ? 'Enabled' : 'Disabled'; ?></div>
                </div>
                <div class="config-item">
                    <div class="config-label">Email Logging</div>
                    <div class="config-value"><?php echo ($current_settings['email_log_enabled'] ?? '1') === '1' ? 'Enabled' : 'Disabled'; ?></div>
                </div>
            </div>
        </div>
        
        <?php if ($test_result): ?>
        <div class="status <?php echo $test_result['success'] ? 'success' : 'error'; ?>">
            <h3><?php echo $test_result['success'] ? '✅ Test Result' : '❌ Test Result'; ?></h3>
            <p><?php echo htmlspecialchars($test_result['message']); ?></p>
            <?php if (isset($test_result['preview_url'])): ?>
            <p><strong>Preview URL:</strong> <a href="<?php echo htmlspecialchars($test_result['preview_url']); ?>" target="_blank"><?php echo htmlspecialchars($test_result['preview_url']); ?></a></p>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <form method="POST" action="test-email-database.php">
            <div class="form-group">
                <label for="test_email">Send Test Email To:</label>
                <input type="email" id="test_email" name="test_email" 
                       placeholder="your-email@example.com" 
                       required>
            </div>
            
            <button type="submit" class="btn btn-test">
                Send Test Email
            </button>
        </form>
        
        <div class="info" style="margin-top: 30px;">
            <h3>📋 How to Update Email Settings</h3>
            <ol style="margin: 10px 0 10px 20px; line-height: 1.8;">
                <li>Go to the <a href="admin/login.php" target="_blank">Admin Panel</a> (login required)</li>
                <li>Navigate to <strong>Booking Settings</strong> → <strong>Email Configuration</strong></li>
                <li>Update your SMTP settings (host, port, username, password)</li>
                <li>Click "Save Email Settings"</li>
                <li>Return here and test the email system</li>
            </ol>
        </div>
        
        <div class="info">
            <h3>🔒 Security Features</h3>
            <ul style="margin: 10px 0 10px 20px;">
                <li><strong>Database Storage:</strong> All settings stored in database, not files</li>
                <li><strong>Password Encryption:</strong> SMTP passwords are encrypted</li>
                <li><strong>No Hardcoded Files:</strong> Removed email-password.php and email-localhost.php</li>
                <li><strong>Admin Control:</strong> All settings manageable through admin panel</li>
                <li><strong>Development Mode:</strong> Safe testing on localhost with previews</li>
            </ul>
        </div>
        
        <div style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #e0e0e0;">
            <p><strong>Related Tests:</strong></p>
            <ul>
                <li><a href="simple-smtp-test.php">Simple SMTP Test</a> - Test SMTP connection only</li>
                <li><a href="admin/booking-settings.php">Admin Email Settings</a> - Update email configuration</li>
                <li><a href="booking.php">Booking System</a> - Test full booking flow with emails</li>
            </ul>
        </div>
    </div>
</body>
</html>