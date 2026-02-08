-- Migration 002: Create site_visitors table for visitor analytics
-- This table logs anonymous visitor sessions for reporting in admin panel.
-- The table is also auto-created by includes/visitor-tracker.php on first visit.
-- Schema must stay in sync with the auto-create DDL in includes/visitor-tracker.php.

CREATE TABLE IF NOT EXISTS `site_visitors` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `session_id` VARCHAR(128) NOT NULL,
    `ip_address` VARCHAR(45) NOT NULL,
    `user_agent` TEXT,
    `device_type` ENUM('desktop', 'tablet', 'mobile', 'bot', 'unknown') DEFAULT 'unknown',
    `browser` VARCHAR(100) DEFAULT NULL,
    `os` VARCHAR(100) DEFAULT NULL,
    `referrer` TEXT DEFAULT NULL,
    `referrer_domain` VARCHAR(255) DEFAULT NULL,
    `country` VARCHAR(100) DEFAULT NULL,
    `page_url` VARCHAR(500) NOT NULL,
    `page_title` VARCHAR(255) DEFAULT NULL,
    `is_first_visit` TINYINT(1) DEFAULT 0,
    `visit_duration` INT DEFAULT NULL COMMENT 'Seconds on page',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_session` (`session_id`),
    INDEX `idx_ip` (`ip_address`),
    INDEX `idx_created` (`created_at`),
    INDEX `idx_page` (`page_url`(191)),
    INDEX `idx_device` (`device_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
