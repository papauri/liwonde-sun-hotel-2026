-- Email Settings Table
-- Stores all email configuration in database instead of hardcoded files

CREATE TABLE IF NOT EXISTS `email_settings` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default email settings
INSERT INTO `email_settings` (`setting_key`, `setting_value`, `setting_group`, `description`, `is_encrypted`) VALUES
('smtp_host', 'mail.promanaged-it.com', 'smtp', 'SMTP server hostname', 0),
('smtp_port', '465', 'smtp', 'SMTP server port', 0),
('smtp_username', 'info@promanaged-it.com', 'smtp', 'SMTP authentication username', 0),
('smtp_password', '', 'smtp', 'SMTP authentication password (encrypted)', 1),
('smtp_secure', 'ssl', 'smtp', 'SMTP security protocol (ssl/tls)', 0),
('smtp_timeout', '30', 'smtp', 'SMTP connection timeout in seconds', 0),
('smtp_debug', '0', 'smtp', 'SMTP debug level (0-4)', 0),
('email_from_name', 'Liwonde Sun Hotel', 'general', 'Default sender name for emails', 0),
('email_from_email', 'info@liwondesunhotel.com', 'general', 'Default sender email address', 0),
('email_admin_email', 'admin@liwondesunhotel.com', 'general', 'Admin notification email address', 0),
('email_bcc_admin', '1', 'general', 'BCC admin on all emails (1=yes, 0=no)', 0),
('email_development_mode', '1', 'general', 'Development mode (1=preview only, 0=send emails)', 0),
('email_log_enabled', '1', 'general', 'Enable email logging (1=yes, 0=no)', 0),
('email_preview_enabled', '1', 'general', 'Enable email previews in development (1=yes, 0=no)', 0);

-- Add email-related settings to site_settings table for backward compatibility
INSERT INTO `site_settings` (`setting_key`, `setting_value`, `setting_group`, `description`) 
SELECT `setting_key`, `setting_value`, `setting_group`, `description`
FROM `email_settings`
WHERE `setting_key` IN ('email_from_name', 'email_from_email', 'email_admin_email')
ON DUPLICATE KEY UPDATE 
  `setting_value` = VALUES(`setting_value`),
  `description` = VALUES(`description`);

-- Update existing site_settings if they exist
UPDATE `site_settings` SET 
  `setting_value` = 'Liwonde Sun Hotel',
  `description` = 'Default sender name for emails'
WHERE `setting_key` = 'email_from_name';

UPDATE `site_settings` SET 
  `setting_value` = 'info@liwondesunhotel.com',
  `description` = 'Default sender email address'
WHERE `setting_key` = 'email_from_email';

UPDATE `site_settings` SET 
  `setting_value` = 'admin@liwondesunhotel.com',
  `description` = 'Admin notification email address'
WHERE `setting_key` = 'email_admin_email';

-- Create function to encrypt sensitive data (simplified version)
DELIMITER //
CREATE FUNCTION IF NOT EXISTS `encrypt_setting`(value TEXT) RETURNS TEXT
DETERMINISTIC
BEGIN
    -- In production, use AES_ENCRYPT with a proper key from environment
    -- For now, we'll use a simple base64 encoding
    RETURN TO_BASE64(value);
END//
DELIMITER ;

-- Create function to decrypt sensitive data
DELIMITER //
CREATE FUNCTION IF NOT EXISTS `decrypt_setting`(value TEXT) RETURNS TEXT
DETERMINISTIC
BEGIN
    -- In production, use AES_DECRYPT with a proper key from environment
    -- For now, we'll decode base64
    RETURN FROM_BASE64(value);
END//
DELIMITER ;