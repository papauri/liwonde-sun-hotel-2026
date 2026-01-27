-- Migration: Add review_type column to reviews table
-- Date: 2026-01-27
-- Description: Adds review_type column to support hotel-wide review functionality

ALTER TABLE `reviews` ADD COLUMN `review_type` ENUM('general','room','restaurant','spa','conference','gym','service') DEFAULT 'general' AFTER `room_id`;
