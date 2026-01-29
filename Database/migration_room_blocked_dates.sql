-- Migration: Room Blocked Dates Table
-- Purpose: Enable manual blocking of dates for rooms (maintenance, events, etc.)
-- Date: 2026-01-29
-- Author: Liwonde Sun Hotel System

-- Create room_blocked_dates table
CREATE TABLE IF NOT EXISTS `room_blocked_dates` (
  `id` int NOT NULL AUTO_INCREMENT,
  `room_id` int DEFAULT NULL COMMENT 'Room ID (NULL means block all rooms)',
  `block_date` date NOT NULL COMMENT 'Date to block from bookings',
  `block_type` enum('maintenance','event','manual','full') NOT NULL DEFAULT 'manual' COMMENT 'Reason for blocking the date',
  `reason` varchar(255) DEFAULT NULL COMMENT 'Optional explanation for blocking',
  `created_by` int unsigned DEFAULT NULL COMMENT 'Admin user who created this block',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'When the block was created',
  PRIMARY KEY (`id`),
  KEY `idx_room_date` (`room_id`, `block_date`),
  KEY `idx_block_date` (`block_date`),
  KEY `idx_block_type` (`block_type`),
  KEY `idx_created_at` (`created_at`),
  CONSTRAINT `fk_blocked_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_blocked_admin` FOREIGN KEY (`created_by`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Manually blocked dates for rooms - prevents bookings on specified dates';

-- Insert default blocked dates (optional - remove if not needed)
-- Uncomment below lines to add sample blocked dates
-- INSERT INTO `room_blocked_dates` (`room_id`, `block_date`, `block_type`, `reason`, `created_by`) VALUES
-- (NULL, '2026-02-14', 'event', 'Valentine''s Day Special Event', 1),
-- (NULL, '2026-12-25', 'event', 'Christmas Day', 1);

-- Add indexes for performance optimization
-- These are already included in the CREATE TABLE statement above
-- but listed here for documentation purposes

-- Migration complete
-- Next steps:
-- 1. Update config/database.php with blocked date functions
-- 2. Create admin interface for managing blocked dates
-- 3. Update availability checking to include blocked dates
-- 4. Create calendar component for booking form
