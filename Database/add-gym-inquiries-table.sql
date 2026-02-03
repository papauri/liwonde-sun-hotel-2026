-- Gym Inquiries Table for Liwonde Sun Hotel
-- Stores gym membership and booking inquiries from the website

CREATE TABLE IF NOT EXISTS `gym_inquiries` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `reference_number` VARCHAR(20) NOT NULL UNIQUE,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(50) NOT NULL,
  `membership_type` VARCHAR(100) DEFAULT NULL,
  `preferred_date` DATE DEFAULT NULL,
  `preferred_time` TIME DEFAULT NULL,
  `guests` INT(11) DEFAULT 1,
  `message` TEXT DEFAULT NULL,
  `consent` TINYINT(1) NOT NULL DEFAULT 0,
  `status` VARCHAR(50) NOT NULL DEFAULT 'new',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reference_number` (`reference_number`),
  KEY `idx_status` (`status`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add some sample status values for reference
-- Possible status values: 'new', 'contacted', 'converted', 'closed', 'cancelled'

-- Insert sample data (optional - for testing)
-- INSERT INTO `gym_inquiries` (`reference_number`, `name`, `email`, `phone`, `membership_type`, `preferred_date`, `preferred_time`, `guests`, `message`, `consent`, `status`) VALUES
-- ('GYM-12345678', 'John Doe', 'john@example.com', '+265991234567', 'Premium', '2026-02-15', '10:00:00', 1, 'I am interested in personal training sessions.', 1, 'new');
