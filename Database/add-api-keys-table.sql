-- API Keys Table for External Booking System Integration
-- This table stores API keys for external websites to access the booking system

CREATE TABLE IF NOT EXISTS `api_keys` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `api_key` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Hashed API key',
  `client_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Name of the client/website using the API',
  `client_website` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Website URL of the client',
  `client_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Contact email for the client',
  `permissions` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'JSON array of permissions: ["rooms.read", "availability.check", "bookings.create", "bookings.read"]',
  `rate_limit_per_hour` int NOT NULL DEFAULT 100 COMMENT 'Maximum API calls per hour',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Whether the API key is active',
  `last_used_at` timestamp NULL DEFAULT NULL COMMENT 'Last time the API key was used',
  `usage_count` int UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Total number of API calls made',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `api_key` (`api_key`),
  KEY `idx_client_name` (`client_name`),
  KEY `idx_is_active` (`is_active`),
  KEY `idx_last_used` (`last_used_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='API keys for external booking system access';

-- Insert a sample API key for testing (key: test_key_12345)
-- The actual API key should be hashed using password_hash()
INSERT INTO `api_keys` (
  `api_key`,
  `client_name`,
  `client_website`,
  `client_email`,
  `permissions`,
  `rate_limit_per_hour`,
  `is_active`
) VALUES (
  '$2y$10$kHKXltLQhR3JuVFtHQ7mZ.KhVjTNKJf7tEU0IwD8HKzKdvyG1Cy/W', -- Hash of 'test_key_12345'
  'Test Client',
  'https://example.com',
  'test@example.com',
  '["rooms.read", "availability.check", "bookings.create", "bookings.read"]',
  1000,
  1
);

-- API Usage Log Table
CREATE TABLE IF NOT EXISTS `api_usage_logs` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `api_key_id` int UNSIGNED NOT NULL,
  `endpoint` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'API endpoint called',
  `method` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'HTTP method',
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Client IP address',
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Client user agent',
  `response_code` int NOT NULL COMMENT 'HTTP response code',
  `response_time` decimal(10,4) NOT NULL COMMENT 'Response time in seconds',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_api_key_id` (`api_key_id`),
  KEY `idx_endpoint` (`endpoint`),
  KEY `idx_created_at` (`created_at`),
  CONSTRAINT `fk_api_usage_logs_api_key_id` FOREIGN KEY (`api_key_id`) REFERENCES `api_keys` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Log of API usage for monitoring and analytics';