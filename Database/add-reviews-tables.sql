-- phpMyAdmin SQL Dump
-- https://www.phpmyadmin.net/
--
-- Database: `p601229_hotels`
--
-- Reviews and Ratings System for Liwonde Sun Hotel
-- This file creates the tables for guest reviews and admin responses

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--
-- Stores guest reviews with detailed ratings and moderation status
--

DROP TABLE IF EXISTS `reviews`;

CREATE TABLE `reviews` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT 'Unique review identifier',
  `booking_id` int UNSIGNED DEFAULT NULL COMMENT 'Link to bookings table if guest stayed',
  `room_id` int DEFAULT NULL COMMENT 'Link to rooms table',
  `guest_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Name of reviewer',
  `guest_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Email of reviewer',
  `rating` int NOT NULL COMMENT 'Overall rating from 1 to 5 stars',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Review title',
  `comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Review content',
  `service_rating` int DEFAULT NULL COMMENT 'Service rating from 1 to 5',
  `cleanliness_rating` int DEFAULT NULL COMMENT 'Cleanliness rating from 1 to 5',
  `location_rating` int DEFAULT NULL COMMENT 'Location rating from 1 to 5',
  `value_rating` int DEFAULT NULL COMMENT 'Value rating from 1 to 5',
  `status` enum('pending','approved','rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending' COMMENT 'Moderation status',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Review submission date',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Last update date',
  PRIMARY KEY (`id`),
  KEY `idx_room_id` (`room_id`),
  KEY `idx_booking_id` (`booking_id`),
  KEY `idx_status` (`status`),
  KEY `idx_guest_email` (`guest_email`),
  KEY `idx_rating` (`rating`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Guest reviews with detailed ratings';

-- --------------------------------------------------------

--
-- Table structure for table `review_responses`
--
-- Stores admin responses to guest reviews
--

DROP TABLE IF EXISTS `review_responses`;

CREATE TABLE `review_responses` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT 'Unique response identifier',
  `review_id` int NOT NULL COMMENT 'Foreign key to reviews table',
  `admin_id` int UNSIGNED DEFAULT NULL COMMENT 'Link to admin_users table',
  `response` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Admin response content',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Response date',
  PRIMARY KEY (`id`),
  KEY `idx_review_id` (`review_id`),
  KEY `idx_admin_id` (`admin_id`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Admin responses to guest reviews';

-- --------------------------------------------------------

--
-- Foreign Key Constraints
--

-- Add foreign key for reviews.room_id referencing rooms.id
ALTER TABLE `reviews`
  ADD CONSTRAINT `fk_reviews_room_id` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- Add foreign key for reviews.booking_id referencing bookings.id
ALTER TABLE `reviews`
  ADD CONSTRAINT `fk_reviews_booking_id` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- Add foreign key for review_responses.review_id referencing reviews.id with CASCADE delete
ALTER TABLE `review_responses`
  ADD CONSTRAINT `fk_review_responses_review_id` FOREIGN KEY (`review_id`) REFERENCES `reviews` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- Add foreign key for review_responses.admin_id referencing admin_users.id
ALTER TABLE `review_responses`
  ADD CONSTRAINT `fk_review_responses_admin_id` FOREIGN KEY (`admin_id`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- --------------------------------------------------------

--
-- AUTO_INCREMENT for tables
--

ALTER TABLE `reviews`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `review_responses`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
