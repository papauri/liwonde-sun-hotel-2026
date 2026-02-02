-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 02, 2026 at 12:35 AM
-- Server version: 8.0.44-cll-lve
-- PHP Version: 8.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `p601229_hotels`
--

DELIMITER $$
--
-- Functions
--
$$

$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `about_us`
--

CREATE TABLE `about_us` (
  `id` int NOT NULL,
  `section_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'main, feature, stat',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subtitle` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` text COLLATE utf8mb4_unicode_ci,
  `image_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `icon_class` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stat_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stat_label` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `display_order` int DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `about_us`
--

INSERT INTO `about_us` (`id`, `section_type`, `title`, `subtitle`, `content`, `image_url`, `icon_class`, `stat_number`, `stat_label`, `display_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'main', 'Experience Luxury Redefined', 'Our Story', 'Nestled in the heart of Malawi, Liwonde Sun Hotel offers an unparalleled luxury experience where timeless elegance meets modern comfort. For over two decades, we\'ve been creating unforgettable memories for discerning travelers from around the world.', 'images/hotel_gallery/Outside2.png', NULL, NULL, NULL, 1, 1, '2026-01-26 11:46:36', '2026-01-26 11:46:36'),
(2, 'feature', 'Award-Winning Services', NULL, 'Consistently recognized for exceptional hospitality and guest satisfaction', NULL, 'fas fa-award', NULL, NULL, 1, 1, '2026-01-26 11:46:36', '2026-01-26 13:23:23'),
(3, 'feature', 'Sustainable Luxury', NULL, 'Committed to eco-friendly practices while maintaining premium standards', NULL, 'fas fa-leaf', NULL, NULL, 2, 1, '2026-01-26 11:46:36', '2026-01-26 11:46:36'),
(4, 'feature', 'Personalized Care', NULL, 'Tailored experiences designed around your unique preferences and needs', NULL, 'fas fa-heart', NULL, NULL, 3, 1, '2026-01-26 11:46:36', '2026-01-26 11:46:36'),
(5, 'feature', '5-Star Excellence', NULL, 'Maintaining the highest standards of quality, comfort, and attention to detail', NULL, 'fas fa-star', NULL, NULL, 4, 1, '2026-01-26 11:46:36', '2026-01-26 11:46:36'),
(6, 'stat', NULL, NULL, NULL, NULL, NULL, '25+', 'Years Excellence', 1, 1, '2026-01-26 11:46:36', '2026-01-26 11:46:36'),
(7, 'stat', NULL, NULL, NULL, NULL, NULL, '98%', 'Guest Satisfaction', 2, 1, '2026-01-26 11:46:36', '2026-01-26 11:46:36'),
(8, 'stat', NULL, NULL, NULL, NULL, NULL, '50+', 'Awards Won', 3, 0, '2026-01-26 11:46:36', '2026-01-26 13:24:21'),
(9, 'stat', NULL, NULL, NULL, NULL, NULL, '10k+', 'Happy Guests', 4, 1, '2026-01-26 11:46:36', '2026-01-26 11:46:36');

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int UNSIGNED NOT NULL,
  `username` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','receptionist','manager') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'receptionist',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `failed_login_attempts` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `email`, `password_hash`, `full_name`, `role`, `is_active`, `last_login`, `created_at`, `updated_at`, `failed_login_attempts`) VALUES
(1, 'admin', 'admin@liwondesunhotel.com', '$2y$10$kHKXltLQhR3JuVFtHQ7mZ.KhVjTNKJf7tEU0IwD8HKzKdvyG1Cy/W', 'System Administrator', 'admin', 1, NULL, '2026-01-20 19:08:40', '2026-01-20 19:08:40', 0),
(2, 'receptionist', 'reception@liwondesunhotel.com', '$2y$10$OFHlFcgoqltOd7X6Z3IqVeg0961Adk9LxyfW8UBBfENSawMRZ3fF6', 'Front Desk', 'receptionist', 1, '2026-02-01 22:11:04', '2026-01-20 19:08:40', '2026-02-01 22:11:04', 0);

-- --------------------------------------------------------

--
-- Table structure for table `api_keys`
--

CREATE TABLE `api_keys` (
  `id` int UNSIGNED NOT NULL,
  `api_key` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Hashed API key',
  `client_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Name of the client/website using the API',
  `client_website` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Website URL of the client',
  `client_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Contact email for the client',
  `permissions` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'JSON array of permissions: ["rooms.read", "availability.check", "bookings.create", "bookings.read"]',
  `rate_limit_per_hour` int NOT NULL DEFAULT '100' COMMENT 'Maximum API calls per hour',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Whether the API key is active',
  `last_used_at` timestamp NULL DEFAULT NULL COMMENT 'Last time the API key was used',
  `usage_count` int UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Total number of API calls made',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='API keys for external booking system access';

--
-- Dumping data for table `api_keys`
--

INSERT INTO `api_keys` (`id`, `api_key`, `client_name`, `client_website`, `client_email`, `permissions`, `rate_limit_per_hour`, `is_active`, `last_used_at`, `usage_count`, `created_at`, `updated_at`) VALUES
(1, '$2y$10$3SV7ph3x7/ttZKUx3rvf8.tVLy6.OaifO3tcYfCeTRV7eSPa3PPX6', 'Test Client', 'https://promanaged-it.com', 'test@example.com', '[\"rooms.read\", \"availability.check\", \"bookings.create\", \"bookings.read\"]', 1000, 1, '2026-01-28 23:42:38', 3, '2026-01-27 13:30:53', '2026-01-28 23:48:54');

-- --------------------------------------------------------

--
-- Table structure for table `api_usage_logs`
--

CREATE TABLE `api_usage_logs` (
  `id` int UNSIGNED NOT NULL,
  `api_key_id` int UNSIGNED NOT NULL,
  `endpoint` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'API endpoint called',
  `method` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'HTTP method',
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Client IP address',
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Client user agent',
  `response_code` int NOT NULL COMMENT 'HTTP response code',
  `response_time` decimal(10,4) NOT NULL COMMENT 'Response time in seconds',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Log of API usage for monitoring and analytics';

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int UNSIGNED NOT NULL,
  `booking_reference` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `room_id` int UNSIGNED NOT NULL,
  `guest_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guest_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guest_phone` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guest_country` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guest_address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `number_of_guests` int NOT NULL DEFAULT '1',
  `check_in_date` date NOT NULL,
  `check_out_date` date NOT NULL,
  `number_of_nights` int NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `amount_paid` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Total amount paid so far',
  `amount_due` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Remaining amount to be paid',
  `vat_rate` decimal(5,2) NOT NULL DEFAULT '0.00' COMMENT 'VAT rate applied',
  `vat_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'VAT amount',
  `total_with_vat` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Total amount including VAT',
  `last_payment_date` date DEFAULT NULL COMMENT 'Date of last payment',
  `special_requests` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','tentative','confirmed','checked-in','checked-out','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `is_tentative` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Whether this is a tentative booking',
  `tentative_expires_at` datetime DEFAULT NULL COMMENT 'When tentative booking expires',
  `deposit_required` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Whether deposit is required',
  `deposit_amount` decimal(10,2) DEFAULT NULL COMMENT 'Required deposit amount',
  `deposit_paid` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Whether deposit has been paid',
  `deposit_paid_at` datetime DEFAULT NULL COMMENT 'When deposit was paid',
  `reminder_sent` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Expiration reminder sent',
  `reminder_sent_at` datetime DEFAULT NULL COMMENT 'When reminder was sent',
  `converted_to_confirmed_at` datetime DEFAULT NULL COMMENT 'When converted to confirmed',
  `expired_at` datetime DEFAULT NULL COMMENT 'When booking expired',
  `tentative_notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Notes about tentative booking',
  `payment_status` enum('unpaid','partial','paid') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unpaid',
  `payment_amount` decimal(10,2) DEFAULT '0.00',
  `payment_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `expires_at` datetime DEFAULT NULL COMMENT 'When tentative booking expires (NULL for non-tentative bookings)',
  `converted_from_tentative` tinyint(1) DEFAULT '0' COMMENT 'Whether this booking was converted from tentative status (1=yes, 0=no)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `booking_reference`, `room_id`, `guest_name`, `guest_email`, `guest_phone`, `guest_country`, `guest_address`, `number_of_guests`, `check_in_date`, `check_out_date`, `number_of_nights`, `total_amount`, `amount_paid`, `amount_due`, `vat_rate`, `vat_amount`, `total_with_vat`, `last_payment_date`, `special_requests`, `status`, `is_tentative`, `tentative_expires_at`, `deposit_required`, `deposit_amount`, `deposit_paid`, `deposit_paid_at`, `reminder_sent`, `reminder_sent_at`, `converted_to_confirmed_at`, `expired_at`, `tentative_notes`, `payment_status`, `payment_amount`, `payment_date`, `created_at`, `updated_at`, `expires_at`, `converted_from_tentative`) VALUES
(23, 'LSH20262626', 3, 'JOHN-PAUL CHIRWA', 'johnpaulchirwa@gmail.com', '0860081635', 'Ireland', '10 Lois na Coille\r\nBallykilmurray, Tullamore', 2, '2026-02-02', '2026-02-06', 4, 120080.00, 139893.20, 0.00, 16.50, 19813.20, 139893.20, '2026-02-01', '', 'checked-in', 0, '2026-02-03 19:11:28', 0, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, 'paid', 0.00, NULL, '2026-02-01 19:11:28', '2026-02-01 19:35:14', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `booking_notes`
--

CREATE TABLE `booking_notes` (
  `id` int UNSIGNED NOT NULL,
  `booking_id` int UNSIGNED NOT NULL,
  `note_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cancellation_log`
--

CREATE TABLE `cancellation_log` (
  `id` int NOT NULL,
  `booking_id` int NOT NULL,
  `booking_reference` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `booking_type` enum('room','conference') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'room',
  `guest_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cancellation_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `cancelled_by` int NOT NULL,
  `cancellation_reason` text COLLATE utf8mb4_unicode_ci,
  `email_sent` tinyint(1) DEFAULT '0',
  `email_status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Audit log for all booking cancellations with email tracking';

-- --------------------------------------------------------

--
-- Table structure for table `conference_inquiries`
--

CREATE TABLE `conference_inquiries` (
  `id` int NOT NULL,
  `inquiry_reference` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `conference_room_id` int NOT NULL,
  `company_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_person` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `event_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `number_of_attendees` int NOT NULL,
  `event_type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `special_requirements` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `catering_required` tinyint(1) DEFAULT '0',
  `av_equipment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','confirmed','cancelled','completed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `total_amount` decimal(10,2) DEFAULT NULL,
  `amount_paid` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Total amount paid so far',
  `amount_due` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Remaining amount to be paid',
  `vat_rate` decimal(5,2) NOT NULL DEFAULT '0.00' COMMENT 'VAT rate applied',
  `vat_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'VAT amount',
  `total_with_vat` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Total amount including VAT',
  `last_payment_date` date DEFAULT NULL COMMENT 'Date of last payment',
  `deposit_required` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Whether deposit is required',
  `deposit_amount` decimal(10,2) DEFAULT NULL COMMENT 'Required deposit amount',
  `deposit_paid` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Whether deposit has been paid',
  `payment_status` enum('pending','deposit_paid','full_paid','refunded') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `total_paid` decimal(10,2) DEFAULT '0.00',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `conference_rooms`
--

CREATE TABLE `conference_rooms` (
  `id` int NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `capacity` int NOT NULL,
  `size_sqm` decimal(10,2) DEFAULT NULL,
  `hourly_rate` decimal(10,2) NOT NULL,
  `daily_rate` decimal(10,2) NOT NULL,
  `amenities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `image_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `display_order` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `conference_rooms`
--

INSERT INTO `conference_rooms` (`id`, `name`, `description`, `capacity`, `size_sqm`, `hourly_rate`, `daily_rate`, `amenities`, `image_path`, `is_active`, `display_order`, `created_at`, `updated_at`) VALUES
(1, 'Executive Boardroom', 'Intimate boardroom perfect for high-level meetings and presentations. Features mahogany furnishings, premium leather seating, and state-of-the-art video conferencing capabilities.', 12, 35.00, 15000.00, 100000.00, 'Video Conferencing, Smart TV, Whiteboard, High-Speed WiFi, Coffee Service', 'images/conference/executive-boardroom.jpg', 1, 1, '2026-01-20 22:35:58', '2026-01-20 22:35:58'),
(2, 'Grand Conference Hall', 'Our largest conference space, ideal for seminars, workshops, and corporate events. Divisible into three sections with soundproof partitions for flexible event configurations.', 150, 200.00, 35000.00, 250000.00, 'Stage & Podium, Professional Sound System, Projection Screen, WiFi, Air Conditioning, Breakout Rooms', 'images/conference/grand-hall.jpg', 1, 2, '2026-01-20 22:35:58', '2026-01-20 22:35:58'),
(3, 'Lakeside Meeting Room', 'Modern meeting space with panoramic views of the lake. Natural lighting and contemporary design create an inspiring environment for creative sessions and strategic planning.', 30, 60.00, 20000.00, 140000.00, 'Projector & Screen, Video Conferencing, Whiteboard, WiFi, Lake View, Terrace Access', 'images/conference/lakeside-room.jpg', 1, 3, '2026-01-20 22:35:58', '2026-01-20 22:35:58'),
(4, 'Executive Boardroom', 'Intimate boardroom perfect for high-level meetings and presentations. Features mahogany furnishings, premium leather seating, and state-of-the-art video conferencing capabilities.', 12, 35.00, 15000.00, 100000.00, 'Video Conferencing, Smart TV, Whiteboard, High-Speed WiFi, Coffee Service', 'images/conference/executive-boardroom.jpg', 1, 1, '2026-01-20 22:36:31', '2026-01-20 22:36:31'),
(5, 'Grand Conference Hall', 'Our largest conference space, ideal for seminars, workshops, and corporate events. Divisible into three sections with soundproof partitions for flexible event configurations.', 150, 200.00, 35000.00, 250000.00, 'Stage & Podium, Professional Sound System, Projection Screen, WiFi, Air Conditioning, Breakout Rooms', 'images/conference/grand-hall.jpg', 1, 2, '2026-01-20 22:36:31', '2026-01-20 22:36:31'),
(6, 'Lakeside Meeting Room', 'Modern meeting space with panoramic views of the lake. Natural lighting and contemporary design create an inspiring environment for creative sessions and strategic planning.', 30, 60.00, 20000.00, 140000.00, 'Projector & Screen, Video Conferencing, Whiteboard, WiFi, Lake View, Terrace Access', 'images/conference/lakeside-room.jpg', 1, 3, '2026-01-20 22:36:31', '2026-01-20 22:36:31');

-- --------------------------------------------------------

--
-- Table structure for table `drink_menu`
--

CREATE TABLE `drink_menu` (
  `id` int NOT NULL,
  `category` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Coffee, Wine, Cocktails, Beer, Non-Alcoholic, etc.',
  `item_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `price` decimal(10,2) NOT NULL,
  `currency_code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'MWK',
  `image_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_available` tinyint(1) DEFAULT '1',
  `is_featured` tinyint(1) DEFAULT '0' COMMENT 'Featured items shown prominently',
  `tags` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Comma-separated tags',
  `display_order` int DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `drink_menu`
--

INSERT INTO `drink_menu` (`id`, `category`, `item_name`, `description`, `price`, `currency_code`, `image_path`, `is_available`, `is_featured`, `tags`, `display_order`, `created_at`, `updated_at`) VALUES
(1, 'Coffee', 'Espresso', 'Rich Italian espresso', 600.00, 'MWK', NULL, 1, 0, 'Hot, Premium', 1, '2026-01-25 11:22:33', '2026-01-25 11:22:33'),
(2, 'Coffee', 'Cappuccino', 'Creamy cappuccino with artistic latte art', 850.00, 'MWK', NULL, 1, 1, 'Hot, Classic', 2, '2026-01-25 11:22:33', '2026-01-25 11:22:33'),
(3, 'Coffee', 'Cortado', 'Perfect balance of espresso and steamed milk', 750.00, 'MWK', NULL, 1, 0, 'Hot, Balanced', 3, '2026-01-25 11:22:33', '2026-01-25 11:22:33'),
(4, 'Coffee', 'Latte', 'Smooth latte with your choice of milk', 800.00, 'MWK', NULL, 1, 0, 'Hot, Creamy', 4, '2026-01-25 11:22:33', '2026-01-25 11:22:33'),
(5, 'Coffee', 'Macchiato', 'Espresso marked with a touch of foam', 700.00, 'MWK', NULL, 1, 0, 'Hot, Strong', 5, '2026-01-25 11:22:33', '2026-01-25 11:22:33'),
(6, 'Coffee', 'Americano', 'Bold americano with hot water', 650.00, 'MWK', NULL, 1, 0, 'Hot, Classic', 6, '2026-01-25 11:22:33', '2026-01-25 11:22:33'),
(7, 'Coffee', 'Mocha', 'Rich espresso with chocolate and steamed milk', 950.00, 'MWK', NULL, 1, 1, 'Hot, Chocolate', 7, '2026-01-25 11:22:33', '2026-01-25 11:22:33'),
(8, 'Coffee', 'Iced Coffee', 'Cold brew coffee served over ice', 750.00, 'MWK', NULL, 1, 0, 'Cold, Refreshing', 8, '2026-01-25 11:22:33', '2026-01-25 11:22:33'),
(9, 'Coffee', 'Flat White', 'Espresso with velvety microfoam milk', 900.00, 'MWK', NULL, 1, 0, 'Hot, Premium', 9, '2026-01-25 11:22:33', '2026-01-25 11:22:33'),
(10, 'Coffee', 'Irish Coffee', 'Coffee with whiskey, sugar, and whipped cream', 1400.00, 'MWK', NULL, 1, 1, 'Hot, Alcohol', 10, '2026-01-25 11:22:33', '2026-01-25 11:22:33'),
(11, 'Wine', 'Château Margaux 2015', 'Bordeaux blend, full-bodied with dark fruit notes', 18500.00, 'MWK', NULL, 1, 1, 'Red Wine, Bordeaux', 1, '2026-01-25 11:22:33', '2026-01-25 11:22:33'),
(12, 'Wine', 'Opus One 2019', 'California Cabernet blend, elegant and balanced', 16500.00, 'MWK', NULL, 1, 0, 'Red Wine, California', 2, '2026-01-25 11:22:33', '2026-01-25 11:22:33'),
(13, 'Wine', 'Chablis Grand Cru', 'Crisp and mineral, perfect for seafood pairing', 6500.00, 'MWK', NULL, 1, 0, 'White Wine, French', 3, '2026-01-25 11:22:33', '2026-01-25 11:22:33'),
(14, 'Wine', 'Champagne Cristal', 'Prestige cuvée with fine bubbles and complexity', 27500.00, 'MWK', NULL, 1, 1, 'Champagne, Premium', 4, '2026-01-25 11:22:33', '2026-01-25 11:22:33'),
(15, 'Wine', 'Pinot Noir Reserve', 'Elegant red with notes of cherry and oak', 8500.00, 'MWK', NULL, 1, 0, 'Red Wine, Light', 5, '2026-01-25 11:22:33', '2026-01-25 11:22:33'),
(16, 'Wine', 'Sauvignon Blanc', 'Crisp white with citrus and tropical fruit notes', 5500.00, 'MWK', NULL, 1, 0, 'White Wine, Fresh', 6, '2026-01-25 11:22:33', '2026-01-25 11:22:33'),
(17, 'Cocktails', 'Margarita', 'Classic tequila, lime, and triple sec', 1200.00, 'MWK', NULL, 1, 0, 'Tequila, Classic', 1, '2026-01-25 11:22:33', '2026-01-25 11:22:33'),
(18, 'Cocktails', 'Mojito', 'Rum with fresh mint, lime, and soda', 1350.00, 'MWK', NULL, 1, 1, 'Rum, Refreshing', 2, '2026-01-25 11:22:33', '2026-01-25 11:22:33'),
(19, 'Cocktails', 'Manhattan', 'Whiskey, vermouth, and bitters', 1400.00, 'MWK', NULL, 1, 0, 'Whiskey, Classic', 3, '2026-01-25 11:22:33', '2026-01-25 11:22:33'),
(20, 'Cocktails', 'Espresso Martini', 'Vodka, coffee liqueur, and fresh espresso', 1500.00, 'MWK', NULL, 1, 1, 'Vodka, Coffee', 4, '2026-01-25 11:22:33', '2026-01-25 11:22:33'),
(21, 'Cocktails', 'Old Fashioned', 'Whiskey, sugar, bitters, and orange twist', 1450.00, 'MWK', NULL, 1, 0, 'Whiskey, Classic', 5, '2026-01-25 11:22:33', '2026-01-25 11:22:33'),
(22, 'Cocktails', 'Cosmopolitan', 'Vodka, cranberry, lime, and triple sec', 1300.00, 'MWK', NULL, 1, 0, 'Vodka, Fruity', 6, '2026-01-25 11:22:33', '2026-01-25 11:22:33'),
(23, 'Cocktails', 'Piña Colada', 'Rum, coconut cream, and pineapple', 1250.00, 'MWK', NULL, 1, 0, 'Rum, Tropical', 7, '2026-01-25 11:22:33', '2026-01-25 11:22:33'),
(24, 'Cocktails', 'Daiquiri', 'White rum, fresh lime juice, and sugar', 1150.00, 'MWK', NULL, 1, 0, 'Rum, Classic', 8, '2026-01-25 11:22:33', '2026-01-25 11:22:33'),
(25, 'Beer', 'Craft Beer - IPA', 'Bold hoppy India Pale Ale', 850.00, 'MWK', NULL, 1, 1, 'IPA, Craft', 1, '2026-01-25 11:22:33', '2026-01-25 11:22:33'),
(26, 'Beer', 'Craft Beer - Stout', 'Rich and creamy stout with chocolate notes', 900.00, 'MWK', NULL, 1, 0, 'Stout, Craft', 2, '2026-01-25 11:22:33', '2026-01-25 11:22:33'),
(27, 'Beer', 'Craft Beer - Lager', 'Crisp and refreshing lager', 750.00, 'MWK', NULL, 1, 0, 'Lager, Craft', 3, '2026-01-25 11:22:33', '2026-01-25 11:22:33'),
(28, 'Beer', 'Carlsberg', 'Premium Danish lager', 1200.00, 'MWK', NULL, 1, 0, 'Lager, Imported', 4, '2026-01-25 11:22:33', '2026-01-25 11:22:33'),
(29, 'Beer', 'Kuche Kuche', 'Local Malawian lager', 1000.00, 'MWK', NULL, 1, 1, 'Lager, Local', 5, '2026-01-25 11:22:33', '2026-01-25 11:22:33'),
(30, 'Beer', 'Guinness', 'Irish dry stout', 1300.00, 'MWK', NULL, 1, 0, 'Stout, Irish', 6, '2026-01-25 11:22:33', '2026-01-25 11:22:33'),
(31, 'Non-Alcoholic', 'Fresh Orange Juice', 'Freshly squeezed orange juice', 800.00, 'MWK', NULL, 1, 1, 'Fresh, Juice', 1, '2026-01-25 11:22:33', '2026-01-25 11:22:33'),
(32, 'Non-Alcoholic', 'Sparkling Water', 'Perrier or San Pellegrino', 700.00, 'MWK', NULL, 1, 0, 'Sparkling, Refreshing', 2, '2026-01-25 11:22:33', '2026-01-25 11:22:33'),
(33, 'Non-Alcoholic', 'Fresh Tropical Juice', 'Mango, pineapple, or passion fruit - freshly squeezed', 800.00, 'MWK', NULL, 1, 0, 'Fresh, Juice', 3, '2026-01-25 11:22:33', '2026-01-25 11:22:33'),
(34, 'Non-Alcoholic', 'Malawian Masala Tea', 'Spiced tea with ginger, cardamom, and cinnamon', 600.00, 'MWK', NULL, 1, 1, 'Hot, Tea', 4, '2026-01-25 11:22:33', '2026-01-25 11:22:33'),
(35, 'Non-Alcoholic', 'Smoothie Bowl', 'Blended fruits with granola, coconut, and honey', 1800.00, 'MWK', NULL, 1, 0, 'Fresh, Healthy', 5, '2026-01-25 11:22:33', '2026-01-25 11:22:33'),
(36, 'Non-Alcoholic', 'Lemonade', 'Fresh lemonade with mint', 650.00, 'MWK', NULL, 1, 0, 'Fresh, Cold', 6, '2026-01-25 11:22:33', '2026-01-25 11:22:33');

-- --------------------------------------------------------

--
-- Table structure for table `email_settings`
--

CREATE TABLE `email_settings` (
  `id` int NOT NULL,
  `setting_key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_value` text COLLATE utf8mb4_unicode_ci,
  `setting_group` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'email',
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_encrypted` tinyint(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `email_settings`
--

INSERT INTO `email_settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `description`, `is_encrypted`, `created_at`, `updated_at`) VALUES
(1, 'smtp_password', '2:1c003835715c7a9a:Y72xf7agVITsio1WTOTy+w==', 'smtp', '', 1, '2026-01-27 09:51:05', '2026-01-27 11:30:08'),
(2, 'email_development_mode', '0', 'general', '', 0, '2026-01-27 09:51:06', '2026-01-27 11:30:08'),
(3, 'smtp_host', 'mail.promanaged-it.com', 'smtp', '', 0, '2026-01-27 09:51:06', '2026-01-27 11:30:07'),
(4, 'smtp_port', '465', 'smtp', '', 0, '2026-01-27 09:51:06', '2026-01-27 11:30:07'),
(5, 'smtp_username', 'info@promanaged-it.com', 'smtp', '', 0, '2026-01-27 09:51:07', '2026-01-27 11:30:08'),
(6, 'smtp_secure', 'ssl', 'smtp', '', 0, '2026-01-27 09:51:07', '2026-01-27 11:30:08'),
(7, 'smtp_timeout', '30', 'smtp', 'SMTP connection timeout in seconds', 0, '2026-01-27 09:51:08', '2026-01-27 09:51:08'),
(8, 'smtp_debug', '0', 'smtp', 'SMTP debug level (0-4)', 0, '2026-01-27 09:51:08', '2026-01-27 09:51:08'),
(9, 'email_from_name', 'Liwonde Sun Hotel', 'general', '', 0, '2026-01-27 09:51:09', '2026-01-27 11:30:08'),
(10, 'email_from_email', 'info@liwondesunhotel.com', 'general', '', 0, '2026-01-27 09:51:09', '2026-01-27 11:30:08'),
(11, 'email_admin_email', 'admin@liwondesunhotel.com', 'general', '', 0, '2026-01-27 09:51:10', '2026-01-27 11:30:08'),
(12, 'email_bcc_admin', '1', 'general', '', 0, '2026-01-27 09:51:10', '2026-01-27 11:30:08'),
(13, 'email_log_enabled', '1', 'general', '', 0, '2026-01-27 09:51:11', '2026-01-27 11:30:08'),
(14, 'email_preview_enabled', '1', 'general', '', 0, '2026-01-27 09:51:11', '2026-01-27 11:30:08'),
(147, 'invoice_recipients', 'accounts@promanaged-it.com', 'invoicing', 'Comma-separated list of email addresses to receive invoice copies (in addition to SMTP username)', 0, '2026-01-27 16:00:35', '2026-01-27 16:00:35'),
(148, 'send_invoice_emails', '1', 'invoicing', 'Send invoice emails when payment is marked as paid (1=yes, 0=no)', 0, '2026-01-27 16:00:35', '2026-01-27 16:00:35');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int NOT NULL,
  `title` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `event_date` date NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `location` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ticket_price` decimal(10,2) DEFAULT '0.00',
  `capacity` int DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `display_order` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `title`, `description`, `event_date`, `start_time`, `end_time`, `location`, `image_path`, `ticket_price`, `capacity`, `is_featured`, `is_active`, `display_order`, `created_at`, `updated_at`) VALUES
(1, 'New Year Gala Dinner', 'Ring in the New Year with an elegant five-course dinner, live entertainment, and spectacular fireworks display over the lake. Dress code: Black tie.', '2026-12-31', '19:00:00', '01:00:00', 'Grand Conference Hall', 'images/events/gala-dinner.jpg', 50000.00, 150, 1, 1, 1, '2026-01-20 22:35:58', '2026-01-20 22:35:58'),
(2, 'Wine Tasting Evening', 'Join our sommelier for an exclusive tasting of premium South African wines paired with artisan cheeses and canapés. Learn about wine regions, varietals, and perfect food pairings.', '2026-02-14', '18:00:00', '21:00:00', 'Lakeside Terrace', 'images/events/event_1769125595_5057.png', 25000.00, 40, 1, 1, 2, '2026-01-20 22:35:58', '2026-01-22 23:46:35'),
(3, 'Business Networking Breakfast', 'Monthly networking event for local business leaders and entrepreneurs. Complimentary breakfast buffet with opportunities to connect and collaborate.', '2026-02-28', '07:00:00', '09:30:00', 'Executive Boardroom', 'images/events/business-breakfast.jpg', 0.00, 30, 0, 1, 3, '2026-01-20 22:35:58', '2026-01-20 22:35:58'),
(4, 'Easter Sunday Brunch', 'Celebrate Easter with a lavish buffet brunch featuring international cuisines, live cooking stations, and entertainment for children. Perfect for the whole family.', '2026-04-05', '11:00:00', '15:00:00', 'Restaurant & Terrace', 'images/events/easter-brunch.jpg', 35000.00, 100, 1, 1, 4, '2026-01-20 22:35:58', '2026-01-20 22:35:58'),
(5, 'Lake Festival Cultural Night', 'Experience traditional Malawian culture with live music, dance performances, and authentic local cuisine. Supporting local artists and community initiatives.', '2026-05-15', '17:00:00', '22:00:00', 'Outdoor Grounds', 'images/events/cultural-night.jpg', 15000.00, 200, 1, 1, 5, '2026-01-20 22:35:58', '2026-01-20 22:35:58'),
(6, 'New Year Gala Dinner', 'Ring in the New Year with an elegant five-course dinner, live entertainment, and spectacular fireworks display over the lake. Dress code: Black tie.', '2026-12-31', '19:00:00', '01:00:00', 'Grand Conference Hall', 'images/events/gala-dinner.jpg', 50000.00, 150, 1, 1, 1, '2026-01-20 22:36:31', '2026-01-20 22:36:31'),
(7, 'Breakfast Morning', '', '2026-02-14', '18:00:00', '21:00:00', 'Lakeside Terrace', 'images/events/wine-tasting.jpg', 25000.00, 40, 1, 1, 0, '2026-01-20 22:36:31', '2026-01-26 23:24:56'),
(8, 'Business Networking Breakfast', 'Monthly networking event for local business leaders and entrepreneurs. Complimentary breakfast buffet with opportunities to connect and collaborate.', '2026-02-28', '07:00:00', '09:30:00', 'Executive Boardroom', 'images/events/business-breakfast.jpg', 0.00, 30, 0, 1, 3, '2026-01-20 22:36:31', '2026-01-20 22:36:31'),
(9, 'Easter Sunday Brunch', 'Celebrate Easter with a lavish buffet brunch featuring international cuisines, live cooking stations, and entertainment for children. Perfect for the whole family.', '2026-04-05', '11:00:00', '15:00:00', 'Restaurant & Terrace', 'images/events/easter-brunch.jpg', 35000.00, 100, 1, 1, 4, '2026-01-20 22:36:31', '2026-01-20 22:36:31'),
(10, 'Lake Festival Cultural Night', 'Experience traditional Malawian culture with live music, dance performances, and authentic local cuisine. Supporting local artists and community initiatives.', '2026-05-15', '17:00:00', '22:00:00', 'Outdoor Grounds', 'images/events/cultural-night.jpg', 15000.00, 200, 1, 1, 5, '2026-01-20 22:36:31', '2026-01-20 22:36:31');

-- --------------------------------------------------------

--
-- Table structure for table `facilities`
--

CREATE TABLE `facilities` (
  `id` int NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `icon_class` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `page_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `display_order` int DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `facilities`
--

INSERT INTO `facilities` (`id`, `name`, `slug`, `description`, `short_description`, `icon_class`, `page_url`, `image_url`, `is_featured`, `is_active`, `display_order`, `created_at`, `updated_at`) VALUES
(1, 'Fine Dining Restaurant', 'fine-dining', 'Award-winning restaurant serving international and local cuisine. Our Michelin-star chef creates exceptional culinary experiences using the finest ingredients. Open 6am-11pm daily.', 'World-class cuisine with Michelin-star chef', 'fas fa-utensils', 'restaurant.php', NULL, 1, 1, 1, '2026-01-19 20:22:49', '2026-01-25 21:25:51'),
(2, 'Luxury Spa & Wellness', 'spa-wellness', 'Full-service spa offering massages, facials, and wellness treatments. Expert therapists provide personalized experiences using premium organic products. Includes sauna and steam room.', 'Rejuvenating spa treatments and wellness services', 'fas fa-spa', NULL, NULL, 1, 1, 2, '2026-01-19 20:22:49', '2026-01-19 20:22:49'),
(3, 'Olympic Swimming Pool', 'swimming-pool', 'Olympic-sized outdoor pool with heated water, children\'s pool, waterslide, and poolside bar service. Perfect for relaxation and recreation year-round.', 'Heated Olympic pool with poolside service', 'fas fa-swimming-pool', NULL, NULL, 1, 1, 3, '2026-01-19 20:22:49', '2026-01-19 20:22:49'),
(4, 'State-of-the-Art Fitness Center', 'fitness-center', 'Modern gym with personal trainers, cardio machines, weights, and dedicated yoga studio. Daily classes available. Open 24/7 for guests.', 'Premium gym with personal training available', 'fas fa-dumbbell', 'gym.php', NULL, 1, 1, 4, '2026-01-19 20:22:49', '2026-01-25 21:26:01'),
(5, 'High-Speed WiFi', 'wifi', 'Ultra-fast fiber internet throughout the hotel. Dedicated business center with meeting facilities and tech support available.', 'Complimentary ultra-fast internet access', 'fas fa-wifi', NULL, NULL, 1, 1, 5, '2026-01-19 20:22:49', '2026-01-19 20:22:49'),
(6, '24/7 Concierge Service', 'concierge', 'Dedicated concierge team for all your needs. Arrange tours, transportation, dining reservations, and special requests anytime.', 'Personalized service around the clock', 'fas fa-concierge-bell', NULL, NULL, 1, 1, 6, '2026-01-19 20:22:49', '2026-01-19 20:22:49');

-- --------------------------------------------------------

--
-- Table structure for table `food_menu`
--

CREATE TABLE `food_menu` (
  `id` int NOT NULL,
  `category` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Breakfast, Lunch, Dinner, Desserts, etc.',
  `item_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `price` decimal(10,2) NOT NULL,
  `currency_code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'MWK',
  `image_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_available` tinyint(1) DEFAULT '1',
  `is_featured` tinyint(1) DEFAULT '0' COMMENT 'Featured items shown prominently',
  `is_vegetarian` tinyint(1) DEFAULT '0',
  `is_vegan` tinyint(1) DEFAULT '0',
  `allergens` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Comma-separated allergen list',
  `display_order` int DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `food_menu`
--

INSERT INTO `food_menu` (`id`, `category`, `item_name`, `description`, `price`, `currency_code`, `image_path`, `is_available`, `is_featured`, `is_vegetarian`, `is_vegan`, `allergens`, `display_order`, `created_at`, `updated_at`) VALUES
(1, 'Breakfast', 'Continental Breakfast', 'Assorted pastries, fresh fruits, yogurt, cereals, and freshly brewed coffee', 2500.00, 'MWK', NULL, 1, 1, 1, 1, NULL, 1, '2026-01-25 11:22:33', '2026-01-25 11:22:33'),
(2, 'Breakfast', 'Full English Breakfast', 'Eggs, bacon, sausages, grilled tomatoes, mushrooms, beans, and toast', 3500.00, 'MWK', NULL, 1, 0, 0, 0, NULL, 2, '2026-01-25 11:22:33', '2026-01-25 11:22:33'),
(3, 'Breakfast', 'Malawian Breakfast Platter', 'Nsima with traditional relish, fresh mandasi, and masala tea', 2800.00, 'MWK', NULL, 1, 1, 0, 0, NULL, 3, '2026-01-25 11:22:33', '2026-01-25 11:22:33'),
(4, 'Breakfast', 'Pancake Stack', 'Fluffy pancakes with maple syrup, fresh berries, and whipped cream', 2200.00, 'MWK', NULL, 1, 0, 1, 0, NULL, 4, '2026-01-25 11:22:33', '2026-01-25 11:22:33'),
(5, 'Breakfast', 'Avocado Toast', 'Sourdough bread with smashed avocado, poached eggs, and chili flakes', 3000.00, 'MWK', NULL, 1, 0, 1, 0, NULL, 5, '2026-01-25 11:22:33', '2026-01-25 11:22:33'),
(6, 'Breakfast', 'Oatmeal Bowl', 'Steel-cut oats with honey, fresh berries, and nuts', 1800.00, 'MWK', NULL, 1, 0, 1, 1, NULL, 6, '2026-01-25 11:22:33', '2026-01-25 11:22:33'),
(7, 'Lunch', 'Chambo Fish & Chips', 'Fresh chambo from Lake Malawi, crispy chips, and tartar sauce', 4500.00, 'MWK', NULL, 1, 1, 0, 0, NULL, 1, '2026-01-25 11:22:33', '2026-01-25 11:22:33'),
(8, 'Lunch', 'Grilled Chicken Caesar Salad', 'Tender chicken breast, crisp romaine, parmesan, croutons, and Caesar dressing', 3800.00, 'MWK', NULL, 1, 0, 0, 0, NULL, 2, '2026-01-25 11:22:33', '2026-01-25 11:22:33'),
(9, 'Lunch', 'Vegetable Curry with Rice', 'Aromatic curry with seasonal vegetables, served with basmati rice', 3200.00, 'MWK', NULL, 1, 1, 1, 1, NULL, 3, '2026-01-25 11:22:33', '2026-01-25 11:22:33'),
(10, 'Lunch', 'Club Sandwich Deluxe', 'Triple-decker with turkey, bacon, lettuce, tomato, and fries', 3500.00, 'MWK', NULL, 1, 0, 0, 0, NULL, 4, '2026-01-25 11:22:33', '2026-01-25 11:22:33'),
(11, 'Lunch', 'Beef Burger', 'Juicy beef patty with cheese, lettuce, tomato, and house sauce', 4000.00, 'MWK', NULL, 1, 0, 0, 0, NULL, 5, '2026-01-25 11:22:33', '2026-01-25 11:22:33'),
(12, 'Lunch', 'Quinoa Salad', 'Fresh quinoa with roasted vegetables, feta cheese, and lemon vinaigrette', 2800.00, 'MWK', NULL, 1, 0, 1, 1, NULL, 6, '2026-01-25 11:22:33', '2026-01-25 11:22:33'),
(13, 'Dinner', 'Grilled T-Bone Steak', 'Premium aged beef, herb butter, roasted vegetables, and choice of sides', 8500.00, 'MWK', NULL, 1, 1, 0, 0, NULL, 1, '2026-01-25 11:22:33', '2026-01-25 11:22:33'),
(14, 'Dinner', 'Pan-Seared Chambo', 'Lake Malawi chambo with lemon butter sauce, seasonal vegetables', 6500.00, 'MWK', NULL, 1, 1, 0, 0, NULL, 2, '2026-01-25 11:22:33', '2026-01-25 11:22:33'),
(15, 'Dinner', 'Slow-Roasted Lamb Shank', 'Tender lamb with red wine jus, creamy mashed potatoes, and greens', 7800.00, 'MWK', NULL, 1, 0, 0, 0, NULL, 3, '2026-01-25 11:22:33', '2026-01-25 11:22:33'),
(16, 'Dinner', 'Vegetarian Risotto', 'Creamy mushroom and truffle risotto with parmesan and fresh herbs', 4500.00, 'MWK', NULL, 1, 0, 1, 0, NULL, 4, '2026-01-25 11:22:33', '2026-01-25 11:22:33'),
(17, 'Dinner', 'Seafood Platter', 'Grilled prawns, calamari, fish fillet, and mussels with garlic butter', 9500.00, 'MWK', NULL, 1, 1, 0, 0, NULL, 5, '2026-01-25 11:22:33', '2026-01-25 11:22:33'),
(18, 'Dinner', 'Grilled Chicken Breast', 'Herb-marinated chicken with roasted potatoes and seasonal vegetables', 5500.00, 'MWK', NULL, 1, 0, 0, 0, NULL, 6, '2026-01-25 11:22:33', '2026-01-25 11:22:33'),
(19, 'Desserts', 'Chocolate Lava Cake', 'Warm chocolate cake with molten center, vanilla ice cream', 2200.00, 'MWK', NULL, 1, 1, 1, 0, NULL, 1, '2026-01-25 11:22:33', '2026-01-25 11:22:33'),
(20, 'Desserts', 'Malawian Banana Fritters', 'Sweet fried bananas with honey and cinnamon', 1500.00, 'MWK', NULL, 1, 0, 1, 1, NULL, 2, '2026-01-25 11:22:33', '2026-01-25 11:22:33'),
(21, 'Desserts', 'Cheesecake Selection', 'Classic, strawberry, or chocolate cheesecake', 2000.00, 'MWK', NULL, 1, 0, 1, 0, NULL, 3, '2026-01-25 11:22:33', '2026-01-25 11:22:33'),
(22, 'Desserts', 'Fresh Fruit Platter', 'Seasonal tropical fruits with passion fruit coulis', 1800.00, 'MWK', NULL, 1, 1, 1, 1, NULL, 4, '2026-01-25 11:22:33', '2026-01-25 11:22:33'),
(23, 'Desserts', 'Crème Brûlée', 'Classic vanilla bean crème brûlée with torched sugar crust', 2500.00, 'MWK', NULL, 1, 0, 1, 0, NULL, 5, '2026-01-25 11:22:33', '2026-01-25 11:22:33'),
(24, 'Desserts', 'Tiramisu', 'Traditional Italian tiramisu with layers of mascarpone and espresso', 2300.00, 'MWK', NULL, 1, 0, 1, 0, NULL, 6, '2026-01-25 11:22:33', '2026-01-25 11:22:33');

-- --------------------------------------------------------

--
-- Table structure for table `footer_links`
--

CREATE TABLE `footer_links` (
  `id` int NOT NULL,
  `column_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `link_text` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `link_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `secondary_link_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `display_order` int DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `footer_links`
--

INSERT INTO `footer_links` (`id`, `column_name`, `link_text`, `link_url`, `secondary_link_url`, `display_order`, `is_active`) VALUES
(1, 'About Hotel', 'About Us', '#about', 'index.php#about', 1, 1),
(2, 'About Hotel', 'Sustainability', '#facilities', 'index.php#facilities', 2, 1),
(3, 'About Hotel', 'Awards', '#testimonials', 'index.php#testimonials', 3, 1),
(4, 'About Hotel', 'History', '#home', 'index.php#home', 4, 1),
(5, 'Guest Services', 'Rooms & Suites', '#rooms', 'index.php#rooms', 1, 1),
(6, 'Guest Services', 'Facilities', '#facilities', 'index.php#facilities', 2, 1),
(7, 'Guest Services', 'Special Offers', '#home', 'index.php#home', 3, 1),
(8, 'Guest Services', 'Group Bookings', '#home', 'index.php#home', 4, 1),
(9, 'Dining & Entertainment', 'Fine Dining', '#facilities', 'index.php#facilities', 1, 1),
(10, 'Dining & Entertainment', 'Spa Services', '#facilities', 'index.php#facilities', 2, 1),
(11, 'Dining & Entertainment', 'Events & Conferences', '#facilities', 'index.php#facilities', 3, 1),
(12, 'Dining & Entertainment', 'Activities', '#facilities', 'index.php#facilities', 4, 1);

-- --------------------------------------------------------

--
-- Table structure for table `gallery`
--

CREATE TABLE `gallery` (
  `id` int NOT NULL,
  `title` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `room_id` int DEFAULT NULL,
  `display_order` int DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gallery`
--

INSERT INTO `gallery` (`id`, `title`, `description`, `image_url`, `category`, `room_id`, `display_order`, `is_active`, `created_at`) VALUES
(2, 'Fine Dining Restaurant', 'World-class cuisine', 'images/hotel-exterior-1024x572.jpg', 'dining', NULL, 2, 1, '2026-01-19 20:22:49'),
(3, 'Olympic Pool', 'Heated swimming pool', 'images/hotel-exterior-1024x572.jpg', 'facilities', NULL, 3, 1, '2026-01-19 20:22:49'),
(4, 'Hotel Exterior', 'Main entrance', 'images/hotel-exterior-1024x572.jpg', 'exterior', NULL, 4, 1, '2026-01-19 20:22:49'),
(5, 'Luxury Spa', 'Wellness center', 'images/hotel-exterior-1024x572.jpg', 'facilities', NULL, 5, 1, '2026-01-19 20:22:49'),
(6, 'Sunset View', 'Evening beauty', 'images/hotel-exterior-1024x572.jpg', 'exterior', NULL, 6, 1, '2026-01-19 20:22:49'),
(7, 'Presidential Suite Living Area', 'Spacious living area with premium furnishings', 'images/gallery/hotel-lobby.jpg', 'rooms', NULL, NULL, 1, '2026-01-20 07:57:13'),
(8, 'Hotel Exterior View', 'Stunning hotel facade during golden hour', 'images/gallery/hotel-exterior_1-1024x572.jpg', 'exterior', NULL, 8, 1, '2026-01-20 07:57:13'),
(9, 'Pool Area Relaxation', 'Olympic pool with panoramic views', 'images/gallery/pool-area-1024x683.jpg', 'facilities', NULL, 9, 1, '2026-01-20 07:57:13'),
(10, 'Fitness Excellence', 'State-of-the-art fitness facilities', 'images/gallery/fitness-center-1024x683.jpg', 'facilities', NULL, 10, 1, '2026-01-20 07:57:13'),
(11, 'Presidential Suite Living Area', 'Spacious living area with premium furnishings', 'images/gallery/hotel-lobby.jpg', 'rooms', NULL, NULL, 1, '2026-01-20 08:03:22'),
(12, 'Hotel Exterior View', 'Stunning hotel facade during golden hour', 'images/gallery/hotel-exterior_1-1024x572.jpg', 'exterior', NULL, 8, 1, '2026-01-20 08:03:22'),
(13, 'Pool Area Relaxation', 'Olympic pool with panoramic views', 'images/gallery/pool-area-1024x683.jpg', 'facilities', NULL, 9, 1, '2026-01-20 08:03:22'),
(14, 'Fitness Excellence', 'State-of-the-art fitness facilities', 'images/gallery/fitness-center-1024x683.jpg', 'facilities', NULL, 10, 1, '2026-01-20 08:03:22'),
(15, 'Presidential Suite Living Area', 'Spacious living area with premium furnishings', 'images/gallery/hotel-lobby.jpg', 'rooms', NULL, NULL, 1, '2026-01-20 08:07:18'),
(16, 'Hotel Exterior View', 'Stunning hotel facade during golden hour', 'images/gallery/hotel-exterior_1-1024x572.jpg', 'exterior', NULL, 8, 1, '2026-01-20 08:07:18'),
(17, 'Pool Area Relaxation', 'Olympic pool with panoramic views', 'images/gallery/pool-area-1024x683.jpg', 'facilities', NULL, 9, 1, '2026-01-20 08:07:18'),
(18, 'Fitness Excellence', 'State-of-the-art fitness facilities', 'images/gallery/fitness-center-1024x683.jpg', 'facilities', NULL, 10, 1, '2026-01-20 08:07:18'),
(23, 'Executive Suite - Bedroom', 'Premium bedroom with king bed', 'images/rooms/executive-bedroom.jpg', 'rooms', 2, 1, 1, '2026-01-20 16:07:07'),
(24, 'Executive Suite - Work Area', 'Dedicated workspace with desk and business amenities', 'images/rooms/executive-work.jpg', 'rooms', 2, 2, 1, '2026-01-20 16:07:07'),
(25, 'Executive Suite - Lounge', 'Comfortable lounge area', 'images/rooms/executive-lounge.jpg', 'rooms', 2, 3, 1, '2026-01-20 16:07:07'),
(26, 'Executive Suite - Bathroom', 'Modern bathroom with premium toiletries', 'images/rooms/executive-bathroom.jpg', 'rooms', 2, 4, 1, '2026-01-20 16:07:07'),
(27, 'Family Suite - Main Bedroom', 'Spacious master bedroom with king bed', 'images/rooms/family-main.jpg', 'rooms', 3, 1, 1, '2026-01-20 16:07:07'),
(28, 'Family Suite - Second Bedroom', 'Comfortable second bedroom with double bed', 'images/rooms/family-second.jpg', 'rooms', 3, 2, 1, '2026-01-20 16:07:07'),
(29, 'Family Suite - Living Area', 'Shared living and dining space', 'images/rooms/family-living.jpg', 'rooms', 3, 3, 1, '2026-01-20 16:07:07'),
(30, 'Family Suite - Kitchen', 'Kitchenette with cooking facilities', 'images/rooms/family-kitchen.jpg', 'rooms', 3, 4, 1, '2026-01-20 16:07:07'),
(34, 'Test', 'Test', 'images/rooms/gallery/room_1_gallery_1769091599.png', 'rooms', 1, NULL, 1, '2026-01-20 16:31:10'),
(35, 'Executive Suite - Bedroom', 'Premium bedroom with king bed', 'images/rooms/gallery/room_2_gallery_1769091563.png', 'rooms', 2, 1, 1, '2026-01-20 16:31:10'),
(36, 'Executive Suite - Work Area', 'Dedicated workspace with desk and business amenities', 'https://source.unsplash.com/1200x1200/?hotel,workspace,desk', 'rooms', 2, 2, 1, '2026-01-20 16:31:10'),
(37, 'Executive Suite - Lounge', 'Comfortable lounge area', 'https://source.unsplash.com/1200x1200/?hotel,lounge,sofa', 'rooms', 2, 3, 1, '2026-01-20 16:31:10'),
(38, 'Executive Suite - Bathroom', 'Modern bathroom with premium toiletries', 'https://source.unsplash.com/1200x1200/?hotel,bathroom,modern', 'rooms', 2, 4, 1, '2026-01-20 16:31:10'),
(39, 'Family Suite - Main Bedroom', 'Spacious master bedroom with king bed', 'https://source.unsplash.com/1200x1200/?family,hotel,room', 'rooms', 3, 1, 1, '2026-01-20 16:31:10'),
(40, 'Family Suite - Second Bedroom', 'Comfortable second bedroom with double bed', 'https://source.unsplash.com/1200x1200/?kids,bedroom,hotel', 'rooms', 3, 2, 1, '2026-01-20 16:31:10'),
(41, 'Family Suite - Living Area', 'Shared living and dining space', 'https://source.unsplash.com/1200x1200/?family,living,room', 'rooms', 3, 3, 1, '2026-01-20 16:31:10'),
(42, 'Family Suite - Kitchen', 'Kitchenette with cooking facilities', 'https://source.unsplash.com/1200x1200/?kitchenette,hotel,apartment', 'rooms', 3, 4, 1, '2026-01-20 16:31:10'),
(43, 'Bedroom', 'New View', 'images/rooms/gallery/room_4_gallery_1769093132.png', 'rooms', 4, 0, 1, '2026-01-22 14:45:32');

-- --------------------------------------------------------

--
-- Table structure for table `gym_classes`
--

CREATE TABLE `gym_classes` (
  `id` int NOT NULL,
  `title` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `day_label` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `time_label` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `level_label` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'All Levels',
  `display_order` int DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gym_classes`
--

INSERT INTO `gym_classes` (`id`, `title`, `description`, `day_label`, `time_label`, `level_label`, `display_order`, `is_active`) VALUES
(13, 'Morning Yoga Flow', 'Start your day with energizing yoga sequences', 'Monday - Friday', '6:30 AM', 'All Levels', 1, 1),
(14, 'HIIT Bootcamp', 'High-intensity interval training for maximum results', 'Tuesday & Thursday', '7:00 AM', 'Intermediate', 2, 1),
(15, 'Pilates Core', 'Strengthen your core with controlled movements', 'Wednesday & Saturday', '8:00 AM', 'All Levels', 3, 1),
(16, 'Evening Meditation', 'Wind down with guided meditation and breathing', 'Daily', '6:00 PM', 'All Levels', 4, 1);

-- --------------------------------------------------------

--
-- Table structure for table `gym_content`
--

CREATE TABLE `gym_content` (
  `id` int NOT NULL,
  `hero_title` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Fitness & Wellness Center',
  `hero_subtitle` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Health & Vitality',
  `hero_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `hero_image_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'images/gym/hero-bg.jpg',
  `wellness_title` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Transform Your Body & Mind',
  `wellness_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `wellness_image_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'images/gym/fitness-center.jpg',
  `badge_text` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Award-Winning Facilities',
  `personal_training_image_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'images/gym/personal-training.jpg',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gym_content`
--

INSERT INTO `gym_content` (`id`, `hero_title`, `hero_subtitle`, `hero_description`, `hero_image_path`, `wellness_title`, `wellness_description`, `wellness_image_path`, `badge_text`, `personal_training_image_path`, `is_active`, `created_at`, `updated_at`) VALUES
(4, 'Fitness & Wellness Center', 'Health & Vitality', 'State-of-the-art facilities designed to elevate your physical and mental well-being', 'images/gym/hero-bg.jpg', 'Transform Your Body & Mind', 'Our fitness and wellness center offers everything you need to maintain your health routine while traveling or start a new wellness journey.', 'images/gym/fitness-center.jpg', 'Award-Winning Facilities', 'images/gym/personal-training.jpg', 1, '2026-01-20 15:26:43', '2026-01-20 15:26:43');

-- --------------------------------------------------------

--
-- Table structure for table `gym_facilities`
--

CREATE TABLE `gym_facilities` (
  `id` int NOT NULL,
  `icon_class` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fas fa-check',
  `title` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `display_order` int DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gym_facilities`
--

INSERT INTO `gym_facilities` (`id`, `icon_class`, `title`, `description`, `display_order`, `is_active`) VALUES
(19, 'fas fa-running', 'Cardio Zone', 'Treadmills, ellipticals, bikes, and rowers with entertainment screens and HR monitoring', 1, 1),
(20, 'fas fa-dumbbell', 'Strength Training', 'Full range of free weights, barbells, and functional rigs', 2, 1),
(21, 'fas fa-child', 'Yoga & Pilates Studio', 'Dedicated studio for yoga, pilates, and meditation with daily classes', 3, 1),
(22, 'fas fa-swimming-pool', 'Lap Pool', '25-meter heated pool ideal for swim workouts and aqua aerobics', 4, 1),
(23, 'fas fa-hot-tub', 'Spa & Sauna', 'Traditional sauna, steam room, and jacuzzi for recovery', 5, 1),
(24, 'fas fa-apple-alt', 'Nutrition Bar', 'Smoothies, protein shakes, and healthy snacks to fuel your workout', 6, 1);

-- --------------------------------------------------------

--
-- Table structure for table `gym_features`
--

CREATE TABLE `gym_features` (
  `id` int NOT NULL,
  `icon_class` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fas fa-dumbbell',
  `title` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `display_order` int DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gym_features`
--

INSERT INTO `gym_features` (`id`, `icon_class`, `title`, `description`, `display_order`, `is_active`) VALUES
(13, 'fas fa-dumbbell', 'Modern Equipment', 'Latest cardio machines, free weights, and resistance training equipment', 1, 1),
(14, 'fas fa-user-md', 'Personal Training', 'Certified trainers available for one-on-one sessions and customized programs', 2, 1),
(15, 'fas fa-spa', 'Spa & Recovery', 'Massage therapy, sauna, and steam rooms for post-workout relaxation', 3, 1),
(16, 'fas fa-clock', 'Flexible Hours', 'Open daily from 5:30 AM to 10:00 PM for your convenience', 4, 1);

-- --------------------------------------------------------

--
-- Table structure for table `gym_packages`
--

CREATE TABLE `gym_packages` (
  `id` int NOT NULL,
  `name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon_class` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'fas fa-leaf',
  `includes_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Line-separated bullet points',
  `duration_label` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `currency_code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'MWK',
  `cta_text` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Book Package',
  `cta_link` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '#book',
  `is_featured` tinyint(1) DEFAULT '0',
  `display_order` int DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gym_packages`
--

INSERT INTO `gym_packages` (`id`, `name`, `icon_class`, `includes_text`, `duration_label`, `price`, `currency_code`, `cta_text`, `cta_link`, `is_featured`, `display_order`, `is_active`) VALUES
(7, 'Rejuvenation Retreat', 'fas fa-leaf', '3 personal training sessions\nDaily yoga classes\n2 spa massages\nNutrition consultation\nComplimentary smoothie bar access', '5 Days', 45000.00, 'MWK', 'Book Package', '#book', 0, 1, 1),
(8, 'Ultimate Wellness', 'fas fa-star', '5 personal training sessions\nUnlimited group classes\n4 spa treatments\nFull nutrition program\nFitness assessment & tracking\nComplimentary wellness amenities', '7 Days', 8500.00, 'MWK', 'Book Package', '#book', 1, 2, 1),
(9, 'Fitness Kickstart', 'fas fa-dumbbell', '2 personal training sessions\nGroup class pass (5 classes)\n1 spa massage\nFitness assessment\nWorkout plan to take home', '3 Days', 28000.00, 'MWK', 'Book Package', '#book', 0, 3, 1);

-- --------------------------------------------------------

--
-- Table structure for table `hero_slides`
--

CREATE TABLE `hero_slides` (
  `id` int NOT NULL,
  `title` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `subtitle` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `image_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `primary_cta_text` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `primary_cta_link` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `secondary_cta_text` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `secondary_cta_link` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `display_order` int DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hero_slides`
--

INSERT INTO `hero_slides` (`id`, `title`, `subtitle`, `description`, `image_path`, `primary_cta_text`, `primary_cta_link`, `secondary_cta_text`, `secondary_cta_link`, `is_active`, `display_order`, `created_at`, `updated_at`) VALUES
(1, 'Experience Unparalleled Luxury', 'Where Luxury Meets Nature', 'Discover the perfect blend of comfort, elegance, and exceptional service at Malawi\'s premier destination', 'images/hero/slide1.jpg', 'Book a Suite', '#book', 'View Rooms', '#rooms', 1, 1, '2026-01-20 07:55:39', '2026-01-20 07:55:39'),
(2, 'Sunrise Over the Shire River', 'Golden hours above pristine waters', 'Wake to breathtaking Malawian sunrises framed by elegant interiors and world-class amenities', 'images/hero/slide2.jpg', 'See Gallery', '#gallery', 'Plan Your Stay', '#contact', 1, 2, '2026-01-20 07:55:39', '2026-01-20 07:55:39'),
(3, 'Award-Winning Dining', 'Michelin-Star Culinary Excellence', 'Savor exceptional cuisine crafted by our renowned chefs using the finest local and international ingredients', 'images/hero/slide3.jpg', 'View Menu', '#facilities', 'Reserve a Table', '#contact', 1, 3, '2026-01-20 07:55:39', '2026-01-20 07:55:39'),
(4, 'Ultimate Relaxation & Wellness', 'Your sanctuary of serenity', 'Indulge in our luxury spa, Olympic pool, and state-of-the-art fitness facilities designed for your well-being', 'images/hero/slide4.jpg', 'Explore Spa', '#facilities', 'Book Treatment', '#book', 1, 4, '2026-01-20 07:55:39', '2026-01-20 07:55:39');

-- --------------------------------------------------------

--
-- Table structure for table `hotel_gallery`
--

CREATE TABLE `hotel_gallery` (
  `id` int UNSIGNED NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `image_url` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'general' COMMENT 'e.g., exterior, interior, rooms, facilities, dining, events',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `display_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hotel_gallery`
--

INSERT INTO `hotel_gallery` (`id`, `title`, `description`, `image_url`, `category`, `is_active`, `display_order`, `created_at`, `updated_at`) VALUES
(1, 'Hotel Exterior View', 'Stunning front view of Liwonde Sun Hotel', 'images/hotel_gallery/art.jpg', 'exterior', 1, 1, '2026-01-20 17:25:33', '2026-01-20 17:27:39'),
(2, 'Luxury Pool Area', 'Infinity pool overlooking the Shire River', 'images/hotel_gallery/Outside2.png', 'facilities', 1, 2, '2026-01-20 17:25:33', '2026-01-20 17:29:31'),
(3, 'Elegant Dining Hall', 'Our award-winning restaurant interior', 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=800&q=80', 'dining', 1, 3, '2026-01-20 17:25:33', '2026-01-20 17:25:33'),
(4, 'Executive Suite', 'Spacious suite with panoramic views', 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=800&q=80', 'rooms', 1, 4, '2026-01-20 17:25:33', '2026-01-20 17:25:33'),
(5, 'Rooftop Lounge', 'Sunset views from our rooftop bar', 'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=800&q=80', 'facilities', 1, 5, '2026-01-20 17:25:33', '2026-01-20 17:25:33'),
(6, 'Grand Lobby', 'Welcome to luxury and elegance', 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=800&q=80', 'interior', 1, 6, '2026-01-20 17:25:33', '2026-01-20 17:25:33'),
(7, 'Spa & Wellness', 'Rejuvenate in our world-class spa', 'https://images.unsplash.com/photo-1540555700478-4be289fbecef?w=800&q=80', 'facilities', 1, 7, '2026-01-20 17:25:33', '2026-01-20 17:25:33');

-- --------------------------------------------------------

--
-- Table structure for table `menu_categories`
--

CREATE TABLE `menu_categories` (
  `id` int NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `display_order` int DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menu_categories`
--

INSERT INTO `menu_categories` (`id`, `name`, `slug`, `description`, `display_order`, `is_active`) VALUES
(1, 'Breakfast', 'breakfast', 'Start your day with our gourmet breakfast selection', 1, 1),
(2, 'Appetizers', 'appetizers', 'Elegant starters to begin your meal', 2, 1),
(3, 'Main Courses', 'main-courses', 'Exquisite main dishes prepared by our Michelin-star chef', 3, 1),
(4, 'Desserts', 'desserts', 'Indulgent sweet creations', 4, 1),
(5, 'Beverages', 'beverages', 'Premium drinks and cocktails', 5, 1);

-- --------------------------------------------------------

--
-- Table structure for table `migration_log`
--

CREATE TABLE `migration_log` (
  `migration_id` int UNSIGNED NOT NULL,
  `migration_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Unique name of the migration',
  `migration_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'When the migration was run',
  `status` enum('pending','in_progress','completed','failed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending' COMMENT 'Migration status',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Log of database migrations';

--
-- Dumping data for table `migration_log`
--

INSERT INTO `migration_log` (`migration_id`, `migration_name`, `migration_date`, `status`, `created_at`) VALUES
(1, 'payments_accounting_system', '2026-01-30 00:12:22', 'completed', '2026-01-30 00:12:22');

-- --------------------------------------------------------

--
-- Table structure for table `newsletter_subscribers`
--

CREATE TABLE `newsletter_subscribers` (
  `id` int NOT NULL,
  `email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `subscription_status` enum('active','unsubscribed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `subscribed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `unsubscribed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `page_heroes`
--

CREATE TABLE `page_heroes` (
  `id` int NOT NULL,
  `page_slug` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Unique page identifier e.g., restaurant, conference',
  `page_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'URL path e.g., /restaurant.php',
  `hero_title` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `hero_subtitle` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hero_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `hero_image_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `display_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `page_heroes`
--

INSERT INTO `page_heroes` (`id`, `page_slug`, `page_url`, `hero_title`, `hero_subtitle`, `hero_description`, `hero_image_path`, `is_active`, `display_order`, `created_at`, `updated_at`) VALUES
(1, 'restaurant', '/restaurant.php', 'Fine Dining Restaurant & Bars', 'Culinary Excellences', 'Savor exceptional cuisine crafted from finest local and international ingredients', 'https://media.gettyimages.com/id/662284115/photo/fresh-dish-ready-to-be-served-at-restaurant.jpg?s=612x612&w=0&k=20&c=joJR03nqnpZ_ZBxU8aRHX2Qz657W_xAcftrjknIsc0c=', 1, 1, '2026-01-25 18:03:42', '2026-01-25 19:32:37'),
(2, 'conference', '/conference.php', 'Conference & Meetings Facilities', 'Business Excellence', 'Businsess-ready venues with premium technology, flexibles layouts, and tailored service for every executive gathering.', 'https://media.gettyimages.com/id/1413260731/photo/a-journalist-team-writing-or-working-on-a-story-together-at-a-media-company-in-a-boardroom.jpg?s=612x612&w=0&k=20&c=EFu8OY0uCLKrtGIGCZ6tHZpUGXJa1gHimC4pf5Iim40=', 1, 2, '2026-01-25 18:03:42', '2026-01-25 19:57:55'),
(3, 'events', '/events.php', 'Events & Experiences', 'Celebrations & Gathering', 'From exclusive wine tastings to cultural nights—discover moments worth remembering at Liwonde Sun Hotel.', 'https://media.gettyimages.com/id/1434116601/photo/zoom-of-hands-laptop-search-or-business-meeting-for-teamwork-marketing-planning-or-target.jpg?s=612x612&w=0&k=20&c=oIngQBqrLY43jKRMhSlTs9xxtGx1YJdrFHXeXchCssg=', 1, 3, '2026-01-25 18:03:42', '2026-01-25 19:52:02'),
(4, 'rooms-showcase', '/rooms-showcase.php', 'Rooms & Suites', 'Riverfront Luxury', 'Explore contemporary rooms and suites with panoramic views of the Shire River, featuring premium amenities and seamless booking integration.', 'https://media.gettyimages.com/id/1382975780/photo/businessman-with-cardkey-unlocking-door-in-hotel.jpg?s=612x612&w=0&k=20&c=yltGZmc_7emEGkP1UAPndO25Iih48zGnVJsqVp38Me8=', 1, 4, '2026-01-25 19:08:32', '2026-01-25 19:54:57'),
(6, 'rooms-gallery', '/rooms-gallery.php', 'Rooms & Suites', 'Riverfront Luxury', 'Explore contemporary rooms and suites with panoramic views of the Shire River, featuring premium amenities and seamless booking integration.', 'https://media.gettyimages.com/id/1382975780/photo/businessman-with-cardkey-unlocking-door-in-hotel.jpg?s=612x612&w=0&k=20&c=yltGZmc_7emEGkP1UAPndO25Iih48zGnVJsqVp38Me8=', 1, 5, '2026-01-25 19:08:32', '2026-01-25 19:54:57'),
(7, 'gym', '/gym.php', 'Gym', 'Your Wellness Journey begins', 'Comprehensive packages designed for optimal health and relaxation', 'images/gym/fitness-center.jpg', 1, 6, '2026-01-25 19:08:32', '2026-01-25 19:54:57');

-- --------------------------------------------------------

--
-- Table structure for table `page_loaders`
--

CREATE TABLE `page_loaders` (
  `id` int NOT NULL,
  `page_slug` varchar(255) NOT NULL,
  `subtext` varchar(255) NOT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `page_loaders`
--

INSERT INTO `page_loaders` (`id`, `page_slug`, `subtext`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'index', 'Loading Excellence...', 1, '2026-01-26 08:37:51', '2026-01-26 08:37:51'),
(2, 'restaurant', 'Preparing Culinary Delights...', 1, '2026-01-26 08:37:51', '2026-01-26 08:37:51'),
(3, 'gym', 'Getting Fit...', 1, '2026-01-26 08:37:51', '2026-01-26 08:37:51'),
(4, 'conference', 'Setting Up Your Event...', 1, '2026-01-26 08:37:51', '2026-01-26 08:37:51'),
(5, 'events', 'Loading Exciting Events...', 1, '2026-01-26 08:37:51', '2026-01-26 08:37:51'),
(6, 'room', 'Finding Your Perfect Room...', 1, '2026-01-26 08:37:51', '2026-01-26 08:37:51'),
(7, 'booking', 'Processing Your Reservation...', 1, '2026-01-26 08:37:51', '2026-01-26 08:37:51'),
(8, 'rooms-gallery', 'Finding Your Perfect Room...', 1, '2026-01-26 08:37:51', '2026-01-26 08:37:51');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int UNSIGNED NOT NULL,
  `payment_reference` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Unique payment reference like PAY-2026-000001',
  `booking_type` enum('room','conference') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Type of booking',
  `booking_id` int UNSIGNED NOT NULL COMMENT 'ID from bookings or conference_inquiries table',
  `conference_id` int UNSIGNED DEFAULT NULL COMMENT 'Optional link to conference_inquiries table for conference-specific payments',
  `booking_reference` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Reference from booking (LSH2026xxxx or CONF-2026-xxxx)',
  `payment_date` date NOT NULL,
  `payment_amount` decimal(10,2) NOT NULL COMMENT 'Amount paid before VAT',
  `vat_rate` decimal(5,2) NOT NULL DEFAULT '0.00' COMMENT 'VAT percentage (e.g., 16.50 for 16.5%)',
  `vat_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Calculated VAT amount',
  `total_amount` decimal(10,2) NOT NULL COMMENT 'Total including VAT',
  `payment_method` enum('cash','bank_transfer','mobile_money','credit_card','debit_card','cheque','other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cash',
  `payment_type` enum('deposit','full_payment','partial_payment','refund','adjustment') COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Type of payment transaction',
  `payment_reference_number` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Transaction ID, receipt number, or cheque number',
  `payment_status` enum('pending','partial','paid','completed','refunded','cancelled') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `invoice_generated` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Whether invoice has been generated',
  `invoice_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Invoice number (e.g., INV-2026-000001)',
  `amount` decimal(10,2) DEFAULT '0.00' COMMENT 'Additional payment amount field - coexists with payment_amount',
  `status` enum('pending','completed','failed','refunded') COLLATE utf8mb4_unicode_ci DEFAULT 'pending' COMMENT 'Additional payment status field - coexists with payment_status',
  `transaction_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Additional transaction reference field - coexists with payment_reference_number',
  `invoice_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Path to generated invoice file',
  `notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Additional payment notes',
  `recorded_by` int UNSIGNED DEFAULT NULL COMMENT 'Admin user who recorded the payment',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `cc_emails` text COLLATE utf8mb4_unicode_ci COMMENT 'Additional CC email addresses for payment receipt',
  `receipt_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Sequential receipt number for payments',
  `processed_by` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Admin user who processed the payment',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Soft delete timestamp'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='All payment transactions for room and conference bookings';

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `payment_reference`, `booking_type`, `booking_id`, `conference_id`, `booking_reference`, `payment_date`, `payment_amount`, `vat_rate`, `vat_amount`, `total_amount`, `payment_method`, `payment_type`, `payment_reference_number`, `payment_status`, `invoice_generated`, `invoice_number`, `amount`, `status`, `transaction_id`, `invoice_path`, `notes`, `recorded_by`, `created_at`, `updated_at`, `cc_emails`, `receipt_number`, `processed_by`, `deleted_at`) VALUES
(3, 'PAY-2026-000023', 'room', 23, NULL, 'LSH20262626', '2026-02-01', 120080.00, 16.50, 19813.20, 139893.20, 'cash', 'full_payment', NULL, 'completed', 1, 'INV-2026-001001', 0.00, 'completed', NULL, 'invoices/INV-2026-001001.html', NULL, 2, '2026-02-01 19:18:53', '2026-02-01 20:07:15', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `policies`
--

CREATE TABLE `policies` (
  `id` int NOT NULL,
  `slug` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `summary` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_order` int DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `policies`
--

INSERT INTO `policies` (`id`, `slug`, `title`, `summary`, `content`, `display_order`, `is_active`, `updated_at`) VALUES
(1, 'booking-policy', 'Booking Policy', 'Flexible bookings with secure guarantees', 'Bookings are confirmed upon receipt of payment guarantee. Amendments are subject to availability. Early check-in and late check-out are available on request and may incur additional charges.', 1, 1, '2026-01-20 10:54:23'),
(2, 'cancellation-policy', 'Cancellation Policy', 'Simple cancellations with fair terms', 'Cancellations up to 48 hours before arrival are free of charge. Within 48 hours or no-shows incur the first night charge. Non-refundable rates are fully prepaid and non-changeable.', 2, 1, '2026-01-20 10:54:23'),
(3, 'dining-policy', 'Dining Policy', 'Elegant dining etiquette', 'Smart casual dress code applies after 6pm. Outside food and beverages are not permitted in dining venues. Allergy and dietary requests are accommodated with advance notice.', 3, 1, '2026-01-20 10:54:23'),
(4, 'faqs', 'FAQs', 'Quick answers to common questions', 'Check-in: 14:00, Check-out: 11:00. Airport transfers can be arranged. Children are welcome; extra beds available on request. High-speed WiFi is complimentary throughout the property.', 4, 1, '2026-01-20 10:54:23');

-- --------------------------------------------------------

--
-- Table structure for table `restaurant_gallery`
--

CREATE TABLE `restaurant_gallery` (
  `id` int NOT NULL,
  `image_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `caption` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'restaurant' COMMENT 'restaurant, bar, dining-area, food',
  `display_order` int DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `restaurant_gallery`
--

INSERT INTO `restaurant_gallery` (`id`, `image_path`, `caption`, `category`, `display_order`, `is_active`, `created_at`) VALUES
(2, 'https://media.gettyimages.com/id/2076075171/photo/abstract-defocused-background-of-restaurant.jpg?s=612x612&w=0&k=20&c=_KsEUAChBiOQDEMP6bumoJPoHkD5WTFmPBh1R1oeTz8=', 'Intimate indoor seating', 'dining-area', 2, 1, '2026-01-20 14:17:17'),
(3, 'https://media.gettyimages.com/id/1758301432/photo/luxury-cocktails-dark-mood-dark-delicious-cocktails-for-brunch-delight.jpg?s=612x612&w=0&k=20&c=UO2273jUYp1WvoWFbJklxEZDjtHKQwVDcKe8ziDqo5A=', 'Premium bar with signature cocktails', 'bar', 3, 1, '2026-01-20 14:17:17'),
(4, 'https://media.gettyimages.com/id/2183697442/photo/a-seafood-platter-with-crabs-yabbies-prawns-and-mussels.jpg?s=612x612&w=0&k=20&c=zPMIG91apQkIcQTpUjr_8DH84enJydwO_0SLiCCNMCk=', 'Fresh seafood platter', 'food', 4, 1, '2026-01-20 14:17:17'),
(13, 'https://media.gettyimages.com/id/1400584557/photo/happy-woman-toasting-with-a-glass-of-wine-during-a-dinner-celebration.jpg?s=612x612&w=0&k=20&c=FXRZHwaTK0iIj3sntl0v5GokMf57dB1jVOn9h7zkUR8=', 'Elegant dining area with panoramic views', 'dining-area', 1, 1, '2026-01-20 15:22:41'),
(17, 'https://media.gettyimages.com/id/1494508942/photo/chef.jpg?s=612x612&w=0&k=20&c=bQGrV0fE-q-mynbVI1DOunZdwte9cyQ0dBf4_m8TUmQ=', 'Fine dining experience', 'restaurant', 5, 1, '2026-01-20 15:22:41'),
(18, 'https://media.gettyimages.com/id/1272158224/photo/using-a-bbq-blower-to-stoke-coal-on-a-simple-barbecue-grill.jpg?s=612x612&w=0&k=20&c=BugTQ1FTnUH7nAdJc4PKNM0YJcgVF8a3Y44Zqv50kqs=', 'Alfresco dining terrace', 'dining-area', 6, 1, '2026-01-20 15:22:41');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int NOT NULL,
  `booking_id` int UNSIGNED DEFAULT NULL COMMENT 'Link to bookings table if guest stayed',
  `room_id` int DEFAULT NULL COMMENT 'Link to rooms table',
  `review_type` enum('general','room','restaurant','spa','conference','gym','service') COLLATE utf8mb4_unicode_ci DEFAULT 'general',
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
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Last update date'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Guest reviews with detailed ratings';

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `booking_id`, `room_id`, `review_type`, `guest_name`, `guest_email`, `rating`, `title`, `comment`, `service_rating`, `cleanliness_rating`, `location_rating`, `value_rating`, `status`, `created_at`, `updated_at`) VALUES
(1, NULL, NULL, 'general', 'Test User', 'test@example.com', 5, 'Excellent Stay', 'This was a wonderful experience at the hotel. The service was outstanding and the room was very clean.', NULL, NULL, NULL, NULL, 'approved', '2026-01-27 14:47:18', '2026-01-27 22:45:26'),
(2, NULL, 1, 'general', 'John Doe', 'john@example.com', 4, 'Great Room', 'The room was spacious and comfortable. Staff was very helpful.', 5, 5, 4, 4, 'approved', '2026-01-27 14:48:28', '2026-01-27 15:29:21'),
(3, NULL, NULL, 'general', 'Test User', 'test@example.com', 5, 'Great Stay', 'This is a test review submission to verify the form works without cURL', NULL, NULL, NULL, NULL, 'approved', '2026-01-27 16:10:19', '2026-01-27 22:44:47');

-- --------------------------------------------------------

--
-- Table structure for table `review_responses`
--

CREATE TABLE `review_responses` (
  `id` int NOT NULL,
  `review_id` int NOT NULL COMMENT 'Foreign key to reviews table',
  `admin_id` int UNSIGNED DEFAULT NULL COMMENT 'Link to admin_users table',
  `response` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Admin response content',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Response date'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Admin responses to guest reviews';

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price_per_night` decimal(10,2) NOT NULL,
  `size_sqm` int DEFAULT NULL,
  `max_guests` int DEFAULT '2',
  `rooms_available` int DEFAULT '5' COMMENT 'Number of rooms currently available for booking',
  `total_rooms` int DEFAULT '5' COMMENT 'Total number of rooms of this type',
  `bed_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `badge` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amenities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `is_featured` tinyint(1) DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `display_order` int DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `name`, `slug`, `description`, `short_description`, `price_per_night`, `size_sqm`, `max_guests`, `rooms_available`, `total_rooms`, `bed_type`, `image_url`, `badge`, `amenities`, `is_featured`, `is_active`, `display_order`, `created_at`, `updated_at`) VALUES
(1, 'Presidential Suite', 'presidential-suite', 'Ultimate luxury with private terrace and exclusive service', 'Ultimate luxury with private terrace and exclusive service', 50000.00, 110, 4, 3, 5, 'King Bed', 'images/rooms/room_1_1768949756.png', 'Luxury', 'King Bed,Private Terrace,Jacuzzi,Butler Service,Living Area,Dining Area,Full Kitchen,Smart TV,Premium WiFi,Climate Control', 1, 1, 1, '2026-01-19 20:22:49', '2026-02-01 12:31:01'),
(2, 'Executive Suite', 'executive-suite', 'Premium executive suite with work area and butler service', 'Premium executive suite with work area and butler service', 30050.00, 60, 3, 4, 5, 'King Bed', 'images\\rooms\\Deluxe Room.jpg', NULL, 'King Bed,Work Desk,Butler Service,Living Area,Smart TV,High-Speed WiFi,Coffee Machine,Mini Bar,Safe', 1, 1, 2, '2026-01-19 20:22:49', '2026-02-01 12:38:20'),
(3, 'Family Suite', 'family-suite', 'Spacious two-bedroom suite perfect for families, featuring two king beds, dual bathrooms, and separate living area. Create lasting memories in ultimate comfort.', 'Spacious family accommodation with 2 bedrooms', 30020.00, 55, 6, 5, 5, '2 King Beds', 'images\\rooms\\family_suite.jpg', 'Family', '2 King Beds,2 Bathrooms,Living Area,Kitchenette,Smart TV,Kids Welcome,Free WiFi,Climate Control', 1, 1, 3, '2026-01-19 20:22:49', '2026-01-20 17:01:46'),
(4, 'Deluxe Suite', 'deluxe-suite', 'Luxurious suite with marble bathroom featuring jacuzzi tub, separate living area, and premium bedding. Experience sophistication and indulgence.', 'Luxury suite with jacuzzi and separate living area', 28000.00, 45, 2, 4, 5, 'King Bed', 'images/rooms/room_4_featured_1769093172.png', 'Popular', 'King Bed,Jacuzzi Tub,Living Area,Marble Bathroom,Premium Bedding,Smart TV,Mini Bar,Free WiFi', 1, 1, 4, '2026-01-19 20:22:49', '2026-01-30 07:42:24'),
(5, 'Superior Room', 'superior-room', 'Spacious room with premium furnishings, stunning views, and modern amenities. Enjoy comfort and elegance in every detail.', 'Spacious room with premium amenities and views', 21000.00, 35, 2, 5, 5, 'King Bed', 'https://source.unsplash.com/1600x900/?superior,hotel,room,view,interior', NULL, 'King Bed,City View,Balcony,Smart TV,Free WiFi,Coffee Machine,Safe,Climate Control', 0, 0, 5, '2026-01-19 20:22:49', '2026-01-22 23:45:09'),
(6, 'Standard Room', 'standard-room', 'Comfortable and well-appointed room with all essential amenities for a pleasant stay. Perfect for travelers seeking quality at exceptional value.', 'Comfortable room with essential amenities', 15000.00, 25, 2, 5, 5, 'Queen Bed', 'https://source.unsplash.com/1600x900/?standard,hotel,room,interior', 'Value', 'Queen Bed,Free WiFi,Smart TV,Daily Breakfast,Climate Control,Safe,Coffee Machine', 0, 0, 6, '2026-01-19 20:22:49', '2026-01-22 23:45:13');

-- --------------------------------------------------------

--
-- Table structure for table `room_blocked_dates`
--

CREATE TABLE `room_blocked_dates` (
  `id` int NOT NULL,
  `room_id` int DEFAULT NULL COMMENT 'Room ID (NULL means block all rooms)',
  `block_date` date NOT NULL COMMENT 'Date to block from bookings',
  `block_type` enum('maintenance','event','manual','full') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'manual' COMMENT 'Reason for blocking the date',
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Optional explanation for blocking',
  `created_by` int UNSIGNED DEFAULT NULL COMMENT 'Admin user who created this block',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'When the block was created'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Manually blocked dates for rooms - prevents bookings on specified dates';

-- --------------------------------------------------------

--
-- Table structure for table `site_settings`
--

CREATE TABLE `site_settings` (
  `id` int NOT NULL,
  `setting_key` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_group` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `site_settings`
--

INSERT INTO `site_settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `updated_at`) VALUES
(1, 'site_name', 'Liwonde Sun Hotel', 'general', '2026-01-19 20:22:49'),
(2, 'site_tagline', 'Where Luxury Meets Nature', 'general', '2026-01-19 20:22:49'),
(3, 'hero_title', 'Experience Unparalleled Luxury', 'hero', '2026-01-19 20:22:49'),
(4, 'hero_subtitle', 'Discover the perfect blend of comfort, elegance, and exceptional service at Malawi\'s premier destination', 'hero', '2026-01-19 20:22:49'),
(5, 'phone_main', '+265 123 456 785', 'contact', '2026-01-20 07:43:44'),
(6, 'phone_reservations', '+265 987 654 321', 'contact', '2026-01-19 20:22:49'),
(9, 'address_line1', 'Liwonde National Park Road', 'contact', '2026-01-19 20:22:49'),
(10, 'address_line2', 'Liwonde, Southern Region', 'contact', '2026-01-19 20:22:49'),
(11, 'address_country', 'Malawi', 'contact', '2026-01-19 20:22:49'),
(12, 'facebook_url', 'https://facebook.com/liwondesunhotel', 'social', '2026-01-19 20:22:49'),
(13, 'instagram_url', 'https://instagram.com/liwondesunhotel', 'social', '2026-01-19 20:22:49'),
(14, 'twitter_url', 'https://twitter.com/liwondesunhotel', 'social', '2026-01-19 20:22:49'),
(15, 'linkedin_url', 'https://linkedin.com/company/liwondesunhotel', 'social', '2026-01-19 20:22:49'),
(16, 'working_hours', '24/7 Available', 'contact', '2026-01-19 20:22:49'),
(17, 'copyright_text', '2026 Liwonde Sun Hotel. All rights reserved.', 'general', '2026-01-19 20:22:49'),
(18, 'currency_symbol', 'MWK', 'general', '2026-01-20 10:16:28'),
(19, 'currency_code', 'MWK', 'general', '2026-01-20 10:16:13'),
(20, 'site_logo', '', 'general', '2026-01-21 23:24:01'),
(23, 'site_url', 'http://liwondesunhotel.com', 'general', '2026-01-27 07:20:42'),
(27, 'check_in_time', '2:00 PM', 'booking', '2026-01-27 12:02:11'),
(28, 'check_out_time', '11:00 AM', 'booking', '2026-01-27 12:02:11'),
(29, 'booking_change_policy', 'If you need to make any changes, please contact us at least 48 hours before your arrival.', 'booking', '2026-01-27 12:02:11'),
(30, 'email_main', 'test@liwondesunhotel.com', 'contact', '2026-01-28 01:12:46'),
(32, 'vat_enabled', '1', 'accounting', '2026-01-30 00:09:59'),
(33, 'vat_rate', '16.5', 'accounting', '2026-01-30 00:09:59'),
(34, 'vat_number', 'MW123456789', 'accounting', '2026-01-30 00:09:59'),
(35, 'payment_terms', 'Payment due upon check-in', 'accounting', '2026-01-30 00:09:59'),
(36, 'invoice_prefix', 'INV', 'accounting', '2026-01-30 00:09:59'),
(37, 'invoice_start_number', '1001', 'accounting', '2026-01-30 00:09:59'),
(44, 'max_advance_booking_days', '22', 'booking', '2026-01-30 00:40:21'),
(45, 'payment_policy', 'Full payment is required upon check-in. We accept cash, credit cards, and bank transfers.', 'booking', '2026-01-30 00:36:10'),
(76, 'tentative_enabled', '1', 'bookings', '2026-02-01 16:32:10'),
(77, 'tentative_duration_hours', '48', 'bookings', '2026-02-01 16:32:10'),
(78, 'tentative_reminder_hours', '24', 'bookings', '2026-02-01 16:32:10'),
(79, 'tentative_max_extensions', '2', 'bookings', '2026-02-01 16:32:10'),
(80, 'tentative_deposit_percent', '20', 'bookings', '2026-02-01 16:32:10'),
(81, 'tentative_deposit_required', '0', 'bookings', '2026-02-01 16:32:10'),
(82, 'tentative_block_availability', '1', 'bookings', '2026-02-01 16:32:10'),
(90, 'whatsapp_number', '+265888860670', 'contact', '2026-02-01 19:09:42'),
(102, 'footer_credits', '© 2026 Liwonde Sun Hotel.', 'general', '2026-02-02 00:33:02'),
(103, 'footer_design_credit', 'Powered by ProManaged IT', 'general', '2026-02-02 00:33:08'),
(104, 'footer_share_title', 'Share', 'general', '2026-02-02 00:32:07'),
(105, 'footer_connect_title', 'Connect With Us', 'general', '2026-02-02 00:32:07'),
(106, 'footer_contact_title', 'Contact Information', 'general', '2026-02-02 00:32:07'),
(107, 'footer_policies_title', 'Policies', 'general', '2026-02-02 00:32:07');

-- --------------------------------------------------------

--
-- Table structure for table `tentative_booking_log`
--

CREATE TABLE `tentative_booking_log` (
  `id` int UNSIGNED NOT NULL,
  `booking_id` int UNSIGNED NOT NULL,
  `action` enum('created','extended','reminder_sent','converted','expired','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL,
  `previous_expires_at` datetime DEFAULT NULL,
  `new_expires_at` datetime DEFAULT NULL,
  `action_reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `performed_by` int UNSIGNED DEFAULT NULL COMMENT 'Admin user ID, or NULL for system',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Audit log for tentative booking actions';

-- --------------------------------------------------------

--
-- Table structure for table `testimonials`
--

CREATE TABLE `testimonials` (
  `id` int NOT NULL,
  `guest_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guest_location` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rating` int DEFAULT '5',
  `testimonial_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `stay_date` date DEFAULT NULL,
  `guest_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT '0',
  `is_approved` tinyint(1) DEFAULT '1',
  `display_order` int DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `testimonials`
--

INSERT INTO `testimonials` (`id`, `guest_name`, `guest_location`, `rating`, `testimonial_text`, `stay_date`, `guest_image`, `is_featured`, `is_approved`, `display_order`, `created_at`) VALUES
(1, 'Sarah Johnson', 'London, UK', 4, 'Absolutely stunning hotel! The service was impeccable, rooms were luxurious, and the restaurant exceeded all expectations. Can\'t wait to return.', '2025-12-15', NULL, 1, 1, 1, '2026-01-19 20:22:49'),
(2, 'Michael Chen', 'Singapore', 5, 'Best hotel experience in Africa. The attention to detail, the spa facilities, and the breathtaking views made our anniversary unforgettable.', '2025-11-20', NULL, 1, 1, 2, '2026-01-19 20:22:49'),
(3, 'Emma Williams', 'New York, USA', 5, 'Five stars aren\'t enough! From check-in to check-out, everything was perfect. The staff went above and beyond to make our stay special.', '2026-01-05', NULL, 1, 1, 3, '2026-01-19 20:22:49');

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_active_tentative_bookings`
-- (See below for the actual view)
--
CREATE TABLE `v_active_tentative_bookings` (
`id` int unsigned
,`booking_reference` varchar(20)
,`room_id` int unsigned
,`room_name` varchar(100)
,`room_slug` varchar(100)
,`price_per_night` decimal(10,2)
,`guest_name` varchar(255)
,`guest_email` varchar(255)
,`guest_phone` varchar(50)
,`check_in_date` date
,`check_out_date` date
,`number_of_nights` int
,`total_amount` decimal(10,2)
,`status` enum('pending','tentative','confirmed','checked-in','checked-out','cancelled')
,`is_tentative` tinyint(1)
,`tentative_expires_at` datetime
,`deposit_required` tinyint(1)
,`deposit_amount` decimal(10,2)
,`deposit_paid` tinyint(1)
,`reminder_sent` tinyint(1)
,`reminder_sent_at` datetime
,`created_at` timestamp
,`tentative_notes` text
,`hours_until_expiration` bigint
,`expiration_status` varchar(8)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_tentative_booking_stats`
-- (See below for the actual view)
--
CREATE TABLE `v_tentative_booking_stats` (
`total_tentative_bookings` bigint
,`active_count` decimal(23,0)
,`warning_count` decimal(23,0)
,`critical_count` decimal(23,0)
,`expired_count` decimal(23,0)
,`deposits_required_count` decimal(23,0)
,`deposits_paid_count` decimal(23,0)
,`total_deposits_amount` decimal(32,2)
,`reminders_sent_count` decimal(23,0)
,`total_value` decimal(32,2)
,`average_booking_value` decimal(14,6)
,`unique_rooms_booked` bigint
);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `about_us`
--
ALTER TABLE `about_us`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_section_type` (`section_type`),
  ADD KEY `idx_display_order` (`display_order`),
  ADD KEY `idx_is_active` (`is_active`);

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `api_keys`
--
ALTER TABLE `api_keys`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `api_key` (`api_key`),
  ADD KEY `idx_client_name` (`client_name`),
  ADD KEY `idx_is_active` (`is_active`),
  ADD KEY `idx_last_used` (`last_used_at`);

--
-- Indexes for table `api_usage_logs`
--
ALTER TABLE `api_usage_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_api_key_id` (`api_key_id`),
  ADD KEY `idx_endpoint` (`endpoint`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `booking_reference` (`booking_reference`),
  ADD KEY `idx_booking_ref` (`booking_reference`),
  ADD KEY `idx_room_id` (`room_id`),
  ADD KEY `idx_guest_email` (`guest_email`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_dates` (`check_in_date`,`check_out_date`),
  ADD KEY `idx_payment_status` (`payment_status`),
  ADD KEY `idx_expires_at` (`expires_at`),
  ADD KEY `idx_tentative_bookings` (`status`,`expires_at`),
  ADD KEY `idx_tentative_expires` (`tentative_expires_at`,`status`),
  ADD KEY `idx_is_tentative` (`is_tentative`,`status`);

--
-- Indexes for table `booking_notes`
--
ALTER TABLE `booking_notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_booking_id` (`booking_id`),
  ADD KEY `idx_created_by` (`created_by`);

--
-- Indexes for table `cancellation_log`
--
ALTER TABLE `cancellation_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_booking_id` (`booking_id`),
  ADD KEY `idx_booking_reference` (`booking_reference`),
  ADD KEY `idx_cancellation_date` (`cancellation_date`),
  ADD KEY `idx_booking_type` (`booking_type`);

--
-- Indexes for table `conference_inquiries`
--
ALTER TABLE `conference_inquiries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `inquiry_reference` (`inquiry_reference`),
  ADD KEY `idx_conference_inquiry_date` (`event_date`,`status`);

--
-- Indexes for table `conference_rooms`
--
ALTER TABLE `conference_rooms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_conference_room_active` (`is_active`,`display_order`);

--
-- Indexes for table `drink_menu`
--
ALTER TABLE `drink_menu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category` (`category`),
  ADD KEY `is_available` (`is_available`),
  ADD KEY `is_featured` (`is_featured`);

--
-- Indexes for table `email_settings`
--
ALTER TABLE `email_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`),
  ADD KEY `setting_group` (`setting_group`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_events_date` (`event_date`,`is_active`),
  ADD KEY `idx_events_featured` (`is_featured`,`is_active`);

--
-- Indexes for table `facilities`
--
ALTER TABLE `facilities`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_facilities_featured` (`is_featured`,`is_active`);

--
-- Indexes for table `food_menu`
--
ALTER TABLE `food_menu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category` (`category`),
  ADD KEY `is_available` (`is_available`),
  ADD KEY `is_featured` (`is_featured`);

--
-- Indexes for table `footer_links`
--
ALTER TABLE `footer_links`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gallery`
--
ALTER TABLE `gallery`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `gym_classes`
--
ALTER TABLE `gym_classes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gym_content`
--
ALTER TABLE `gym_content`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gym_facilities`
--
ALTER TABLE `gym_facilities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gym_features`
--
ALTER TABLE `gym_features`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gym_packages`
--
ALTER TABLE `gym_packages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hero_slides`
--
ALTER TABLE `hero_slides`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hotel_gallery`
--
ALTER TABLE `hotel_gallery`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_active_order` (`is_active`,`display_order`);

--
-- Indexes for table `menu_categories`
--
ALTER TABLE `menu_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `migration_log`
--
ALTER TABLE `migration_log`
  ADD PRIMARY KEY (`migration_id`),
  ADD UNIQUE KEY `idx_migration_name` (`migration_name`),
  ADD KEY `idx_migration_date` (`migration_date`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `newsletter_subscribers`
--
ALTER TABLE `newsletter_subscribers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `page_heroes`
--
ALTER TABLE `page_heroes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `page_slug` (`page_slug`),
  ADD UNIQUE KEY `page_url` (`page_url`),
  ADD KEY `idx_page_heroes_active_order` (`is_active`,`display_order`);

--
-- Indexes for table `page_loaders`
--
ALTER TABLE `page_loaders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `page_slug` (`page_slug`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payment_reference` (`payment_reference`),
  ADD UNIQUE KEY `receipt_number` (`receipt_number`),
  ADD KEY `idx_booking_type_id` (`booking_type`,`booking_id`),
  ADD KEY `idx_payment_date` (`payment_date`),
  ADD KEY `idx_payment_status` (`payment_status`),
  ADD KEY `idx_recorded_by` (`recorded_by`),
  ADD KEY `idx_conference_id` (`conference_id`);

--
-- Indexes for table `policies`
--
ALTER TABLE `policies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `restaurant_gallery`
--
ALTER TABLE `restaurant_gallery`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_room_id` (`room_id`),
  ADD KEY `idx_booking_id` (`booking_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_guest_email` (`guest_email`),
  ADD KEY `idx_rating` (`rating`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `review_responses`
--
ALTER TABLE `review_responses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_review_id` (`review_id`),
  ADD KEY `idx_admin_id` (`admin_id`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_rooms_featured` (`is_featured`,`is_active`),
  ADD KEY `idx_rooms_price` (`price_per_night`);

--
-- Indexes for table `room_blocked_dates`
--
ALTER TABLE `room_blocked_dates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_room_date` (`room_id`,`block_date`),
  ADD KEY `idx_block_date` (`block_date`),
  ADD KEY `idx_block_type` (`block_type`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `fk_blocked_admin` (`created_by`);

--
-- Indexes for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `tentative_booking_log`
--
ALTER TABLE `tentative_booking_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_booking_id` (`booking_id`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `performed_by` (`performed_by`);

--
-- Indexes for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_testimonials_featured` (`is_featured`,`is_approved`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `about_us`
--
ALTER TABLE `about_us`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `api_keys`
--
ALTER TABLE `api_keys`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `api_usage_logs`
--
ALTER TABLE `api_usage_logs`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `booking_notes`
--
ALTER TABLE `booking_notes`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cancellation_log`
--
ALTER TABLE `cancellation_log`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `conference_inquiries`
--
ALTER TABLE `conference_inquiries`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `conference_rooms`
--
ALTER TABLE `conference_rooms`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `drink_menu`
--
ALTER TABLE `drink_menu`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `email_settings`
--
ALTER TABLE `email_settings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=149;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `facilities`
--
ALTER TABLE `facilities`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `food_menu`
--
ALTER TABLE `food_menu`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `footer_links`
--
ALTER TABLE `footer_links`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `gallery`
--
ALTER TABLE `gallery`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `gym_classes`
--
ALTER TABLE `gym_classes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `gym_content`
--
ALTER TABLE `gym_content`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `gym_facilities`
--
ALTER TABLE `gym_facilities`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `gym_features`
--
ALTER TABLE `gym_features`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `gym_packages`
--
ALTER TABLE `gym_packages`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `hero_slides`
--
ALTER TABLE `hero_slides`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `hotel_gallery`
--
ALTER TABLE `hotel_gallery`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `menu_categories`
--
ALTER TABLE `menu_categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `migration_log`
--
ALTER TABLE `migration_log`
  MODIFY `migration_id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `newsletter_subscribers`
--
ALTER TABLE `newsletter_subscribers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `page_heroes`
--
ALTER TABLE `page_heroes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `page_loaders`
--
ALTER TABLE `page_loaders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `policies`
--
ALTER TABLE `policies`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `restaurant_gallery`
--
ALTER TABLE `restaurant_gallery`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `review_responses`
--
ALTER TABLE `review_responses`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `room_blocked_dates`
--
ALTER TABLE `room_blocked_dates`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `site_settings`
--
ALTER TABLE `site_settings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108;

--
-- AUTO_INCREMENT for table `tentative_booking_log`
--
ALTER TABLE `tentative_booking_log`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

-- --------------------------------------------------------

--
-- Structure for view `v_active_tentative_bookings`
--
DROP TABLE IF EXISTS `v_active_tentative_bookings`;

CREATE ALGORITHM=UNDEFINED DEFINER=`p601229`@`localhost` SQL SECURITY DEFINER VIEW `v_active_tentative_bookings`  AS SELECT `b`.`id` AS `id`, `b`.`booking_reference` AS `booking_reference`, `b`.`room_id` AS `room_id`, `r`.`name` AS `room_name`, `r`.`slug` AS `room_slug`, `r`.`price_per_night` AS `price_per_night`, `b`.`guest_name` AS `guest_name`, `b`.`guest_email` AS `guest_email`, `b`.`guest_phone` AS `guest_phone`, `b`.`check_in_date` AS `check_in_date`, `b`.`check_out_date` AS `check_out_date`, `b`.`number_of_nights` AS `number_of_nights`, `b`.`total_amount` AS `total_amount`, `b`.`status` AS `status`, `b`.`is_tentative` AS `is_tentative`, `b`.`tentative_expires_at` AS `tentative_expires_at`, `b`.`deposit_required` AS `deposit_required`, `b`.`deposit_amount` AS `deposit_amount`, `b`.`deposit_paid` AS `deposit_paid`, `b`.`reminder_sent` AS `reminder_sent`, `b`.`reminder_sent_at` AS `reminder_sent_at`, `b`.`created_at` AS `created_at`, `b`.`tentative_notes` AS `tentative_notes`, timestampdiff(HOUR,now(),`b`.`tentative_expires_at`) AS `hours_until_expiration`, (case when (`b`.`tentative_expires_at` < now()) then 'expired' when (`b`.`tentative_expires_at` <= (now() + interval 24 hour)) then 'critical' when (`b`.`tentative_expires_at` <= (now() + interval 48 hour)) then 'warning' else 'active' end) AS `expiration_status` FROM (`bookings` `b` left join `rooms` `r` on((`b`.`room_id` = `r`.`id`))) WHERE ((`b`.`is_tentative` = 1) AND (`b`.`status` = 'tentative') AND (`b`.`tentative_expires_at` is not null)) ORDER BY `b`.`tentative_expires_at` ASC ;

-- --------------------------------------------------------

--
-- Structure for view `v_tentative_booking_stats`
--
DROP TABLE IF EXISTS `v_tentative_booking_stats`;

CREATE ALGORITHM=UNDEFINED DEFINER=`p601229`@`localhost` SQL SECURITY DEFINER VIEW `v_tentative_booking_stats`  AS SELECT count(0) AS `total_tentative_bookings`, sum((case when (`v_active_tentative_bookings`.`expiration_status` = 'active') then 1 else 0 end)) AS `active_count`, sum((case when (`v_active_tentative_bookings`.`expiration_status` = 'warning') then 1 else 0 end)) AS `warning_count`, sum((case when (`v_active_tentative_bookings`.`expiration_status` = 'critical') then 1 else 0 end)) AS `critical_count`, sum((case when (`v_active_tentative_bookings`.`expiration_status` = 'expired') then 1 else 0 end)) AS `expired_count`, sum((case when (`v_active_tentative_bookings`.`deposit_required` = 1) then 1 else 0 end)) AS `deposits_required_count`, sum((case when (`v_active_tentative_bookings`.`deposit_paid` = 1) then 1 else 0 end)) AS `deposits_paid_count`, sum(`v_active_tentative_bookings`.`deposit_amount`) AS `total_deposits_amount`, sum((case when (`v_active_tentative_bookings`.`reminder_sent` = 1) then 1 else 0 end)) AS `reminders_sent_count`, sum(`v_active_tentative_bookings`.`total_amount`) AS `total_value`, avg(`v_active_tentative_bookings`.`total_amount`) AS `average_booking_value`, count(distinct `v_active_tentative_bookings`.`room_id`) AS `unique_rooms_booked` FROM `v_active_tentative_bookings` ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `api_usage_logs`
--
ALTER TABLE `api_usage_logs`
  ADD CONSTRAINT `fk_api_usage_logs_api_key_id` FOREIGN KEY (`api_key_id`) REFERENCES `api_keys` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `gallery`
--
ALTER TABLE `gallery`
  ADD CONSTRAINT `gallery_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_payments_admin` FOREIGN KEY (`recorded_by`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `fk_reviews_booking_id` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_reviews_room_id` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `review_responses`
--
ALTER TABLE `review_responses`
  ADD CONSTRAINT `fk_review_responses_admin_id` FOREIGN KEY (`admin_id`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_review_responses_review_id` FOREIGN KEY (`review_id`) REFERENCES `reviews` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `room_blocked_dates`
--
ALTER TABLE `room_blocked_dates`
  ADD CONSTRAINT `fk_blocked_admin` FOREIGN KEY (`created_by`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_blocked_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tentative_booking_log`
--
ALTER TABLE `tentative_booking_log`
  ADD CONSTRAINT `tentative_booking_log_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tentative_booking_log_ibfk_2` FOREIGN KEY (`performed_by`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
