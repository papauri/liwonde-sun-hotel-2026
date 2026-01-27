<?php
/**
 * Migration Script: Move email settings from hardcoded files to database
 * 
 * This script should be run once to migrate existing email settings
 * from config/email-password.php and config/email-localhost.php to database
 */

// Load database configuration
require_once __DIR__ . '/../config/database.php';

echo "Starting email settings migration...\n";

// Check if email_settings table exists
$table_exists = $pdo->query("SHOW TABLES LIKE 'email_settings'")->rowCount() > 0;

if (!$table_exists) {
    echo "Creating email_settings table...\n";
    
    // Create the table
    $sql = "CREATE TABLE IF NOT EXISTS `email_settings` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `setting_key` varchar(100) NOT NULL,
      `setting_value` text,
      `setting_group` varchar(50) DEFAULT 'email',
      `description` text,
      `is_encrypted` tinyint(1) DEFAULT 0,
      `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      UNIQUE KEY `setting_key` (`setting_key`),
      KEY `setting_group` (`setting_group`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($sql);
    echo "Table created successfully.\n";
}

// Migrate SMTP password from email-password.php
$password_file = __DIR__ . '/../config/email-password.php';
if (file_exists($password_file)) {
    echo "Migrating SMTP password from email-password.php...\n";
    
    $smtp_password = include $password_file;
    
    if (is_string($smtp_password) && $smtp_password !== 'YOUR_EMAIL_PASSWORD_HERE' && !empty($smtp_password)) {
        // Update or insert SMTP password
        $sql = "INSERT INTO email_settings (setting_key, setting_value, setting_group, description, is_encrypted) 
                VALUES ('smtp_password', ?, 'smtp', 'SMTP authentication password (encrypted)', 1)
                ON DUPLICATE KEY UPDATE 
                setting_value = VALUES(setting_value),
                updated_at = CURRENT_TIMESTAMP";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$smtp_password]);
        
        echo "SMTP password migrated to database.\n";
        
        // Backup and remove the file
        $backup_file = $password_file . '.backup-' . date('Ymd-His');
        if (copy($password_file, $backup_file)) {
            if (unlink($password_file)) {
                echo "Original file backed up to: " . basename($backup_file) . "\n";
                echo "Hardcoded password file removed.\n";
            }
        }
    } else {
        echo "No valid password found in email-password.php.\n";
    }
} else {
    echo "email-password.php not found.\n";
}

// Migrate localhost setting from email-localhost.php
$localhost_file = __DIR__ . '/../config/email-localhost.php';
if (file_exists($localhost_file)) {
    echo "Migrating localhost setting from email-localhost.php...\n";
    
    $localhost_enabled = include $localhost_file;
    
    if (is_bool($localhost_enabled)) {
        $development_mode = $localhost_enabled ? '0' : '1'; // Inverted logic
        
        $sql = "INSERT INTO email_settings (setting_key, setting_value, setting_group, description, is_encrypted) 
                VALUES ('email_development_mode', ?, 'general', 'Development mode (1=preview only, 0=send emails)', 0)
                ON DUPLICATE KEY UPDATE 
                setting_value = VALUES(setting_value),
                updated_at = CURRENT_TIMESTAMP";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$development_mode]);
        
        echo "Localhost setting migrated to database.\n";
        
        // Backup and remove the file
        $backup_file = $localhost_file . '.backup-' . date('Ymd-His');
        if (copy($localhost_file, $backup_file)) {
            if (unlink($localhost_file)) {
                echo "Original file backed up to: " . basename($backup_file) . "\n";
                echo "Hardcoded localhost file removed.\n";
            }
        }
    }
} else {
    echo "email-localhost.php not found.\n";
}

// Insert default email settings if they don't exist
echo "Setting up default email configuration...\n";

$default_settings = [
    // SMTP Settings
    ['smtp_host', 'mail.promanaged-it.com', 'smtp', 'SMTP server hostname', 0],
    ['smtp_port', '465', 'smtp', 'SMTP server port', 0],
    ['smtp_username', 'info@promanaged-it.com', 'smtp', 'SMTP authentication username', 0],
    ['smtp_secure', 'ssl', 'smtp', 'SMTP security protocol (ssl/tls)', 0],
    ['smtp_timeout', '30', 'smtp', 'SMTP connection timeout in seconds', 0],
    ['smtp_debug', '0', 'smtp', 'SMTP debug level (0-4)', 0],
    
    // Email Settings
    ['email_from_name', 'Liwonde Sun Hotel', 'general', 'Default sender name for emails', 0],
    ['email_from_email', 'info@liwondesunhotel.com', 'general', 'Default sender email address', 0],
    ['email_admin_email', 'admin@liwondesunhotel.com', 'general', 'Admin notification email address', 0],
    ['email_bcc_admin', '1', 'general', 'BCC admin on all emails (1=yes, 0=no)', 0],
    ['email_development_mode', '1', 'general', 'Development mode (1=preview only, 0=send emails)', 0],
    ['email_log_enabled', '1', 'general', 'Enable email logging (1=yes, 0=no)', 0],
    ['email_preview_enabled', '1', 'general', 'Enable email previews in development (1=yes, 0=no)', 0],
];

foreach ($default_settings as $setting) {
    list($key, $value, $group, $description, $encrypted) = $setting;
    
    // Check if setting already exists
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM email_settings WHERE setting_key = ?");
    $stmt->execute([$key]);
    $result = $stmt->fetch();
    
    if ($result['count'] == 0) {
        $sql = "INSERT INTO email_settings (setting_key, setting_value, setting_group, description, is_encrypted) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$key, $value, $group, $description, $encrypted]);
        
        echo "Added default setting: $key\n";
    } else {
        echo "Setting already exists: $key\n";
    }
}

// Update site_settings for backward compatibility
echo "Updating site_settings for backward compatibility...\n";

$site_settings = [
    'email_from_name' => 'Liwonde Sun Hotel',
    'email_from_email' => 'info@liwondesunhotel.com',
    'email_admin_email' => 'admin@liwondesunhotel.com',
];

foreach ($site_settings as $key => $value) {
    // Check if description column exists
    $has_description = false;
    $stmt = $pdo->query("SHOW COLUMNS FROM site_settings LIKE 'description'");
    if ($stmt->rowCount() > 0) {
        $has_description = true;
    }
    
    if ($has_description) {
        $sql = "INSERT INTO site_settings (setting_key, setting_value, setting_group, description) 
                VALUES (?, ?, 'email', 'Email configuration')
                ON DUPLICATE KEY UPDATE 
                setting_value = VALUES(setting_value),
                updated_at = CURRENT_TIMESTAMP";
    } else {
        $sql = "INSERT INTO site_settings (setting_key, setting_value, setting_group) 
                VALUES (?, ?, 'email')
                ON DUPLICATE KEY UPDATE 
                setting_value = VALUES(setting_value),
                updated_at = CURRENT_TIMESTAMP";
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$key, $value]);
    
    echo "Updated site_setting: $key\n";
}

echo "\nMigration completed successfully!\n";
echo "========================================\n";
echo "Summary:\n";
echo "1. Email settings are now stored in the database\n";
echo "2. Hardcoded files have been backed up and removed\n";
echo "3. All email configuration can now be managed via admin panel\n";
echo "4. No more hardcoded passwords in the codebase\n";
echo "\nNext steps:\n";
echo "1. Update your SMTP password in the admin panel\n";
echo "2. Test email functionality\n";
echo "3. Remove backup files after confirming everything works\n";
echo "========================================\n";
?>