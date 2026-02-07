-- ============================================
-- Section Headers Table
-- ============================================
-- This table stores dynamic section headers for all website sections
-- Allows admin to customize section labels, titles, and descriptions
-- without modifying code
--
-- ADMIN INTERFACE: /admin/section-headers-management.php
-- Provides visual interface to manage all section headers with live preview
--
-- CSS STYLES: Defined in css/style.css
-- - .section-label: Gold, uppercase, bold (category tag)
-- - .section-subtitle: Gray, italic, serif (decorative subtitle)
-- - .section-title: Navy, bold, serif (main heading)
-- - .section-description: Gray, regular (supporting text)

CREATE TABLE IF NOT EXISTS `section_headers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `section_key` varchar(100) NOT NULL COMMENT 'Unique identifier for the section (e.g., home_rooms, home_facilities)',
  `page` varchar(50) NOT NULL COMMENT 'Page where section appears (e.g., index, restaurant, gym)',
  `section_label` varchar(100) DEFAULT NULL COMMENT 'Small label above title (optional)',
  `section_subtitle` varchar(255) DEFAULT NULL COMMENT 'Subtitle text between label and title (optional)',
  `section_title` varchar(200) NOT NULL COMMENT 'Main section heading',
  `section_description` text DEFAULT NULL COMMENT 'Description text below title',
  `display_order` int(11) DEFAULT 0 COMMENT 'Order of section on page',
  `is_active` tinyint(1) DEFAULT 1 COMMENT 'Whether section header is active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_section` (`section_key`, `page`),
  INDEX `idx_page` (`page`),
  INDEX `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Insert Default Section Headers
-- ============================================

-- Homepage sections
INSERT INTO `section_headers` (`section_key`, `page`, `section_label`, `section_subtitle`, `section_title`, `section_description`, `display_order`) VALUES
('home_rooms', 'index', 'Accommodations', 'Where Comfort Meets Luxury', 'Luxurious Rooms & Suites', 'Experience unmatched comfort in our meticulously designed rooms and suites', 1),
('home_facilities', 'index', 'Amenities', NULL, 'World-Class Facilities', 'Indulge in our premium facilities designed for your ultimate comfort', 2),
('home_testimonials', 'index', 'Reviews', NULL, 'What Our Guests Say', 'Hear from those who have experienced our exceptional hospitality', 3);

-- Hotel Gallery section (used on multiple pages)
INSERT INTO `section_headers` (`section_key`, `page`, `section_label`, `section_subtitle`, `section_title`, `section_description`, `display_order`) VALUES
('hotel_gallery', 'index', 'Visual Journey', 'Discover Our Story', 'Explore Our Hotel', 'Immerse yourself in the beauty and luxury of our hotel', 4);

-- Reviews section (used on multiple pages)
INSERT INTO `section_headers` (`section_key`, `page`, `section_label`, `section_subtitle`, `section_title`, `section_description`, `display_order`) VALUES
('hotel_reviews', 'global', 'Guest Reviews', NULL, 'What Our Guests Say', 'Read authentic reviews from guests who have experienced our hospitality', 1);

-- Restaurant sections
INSERT INTO `section_headers` (`section_key`, `page`, `section_label`, `section_subtitle`, `section_title`, `section_description`, `display_order`) VALUES
('restaurant_gallery', 'restaurant', 'Visual Journey', NULL, 'Our Dining Spaces', 'From elegant interiors to breathtaking views, every detail creates the perfect ambiance', 1),
('restaurant_menu', 'restaurant', 'Culinary Delights', 'A Symphony of Flavors', 'Our Menu', 'Discover our carefully curated selection of dishes and beverages', 2);

-- Gym sections
INSERT INTO `section_headers` (`section_key`, `page`, `section_label`, `section_subtitle`, `section_title`, `section_description`, `display_order`) VALUES
('gym_wellness', 'gym', 'Your Wellness Journey', 'Transform Your Life', 'Start Your Fitness Journey', 'Transform your body and mind with our state-of-the-art facilities', 1),
('gym_facilities', 'gym', 'What We Offer', NULL, 'Comprehensive Fitness Facilities', 'Everything you need for a complete wellness experience', 2),
('gym_classes', 'gym', 'Stay Active', NULL, 'Group Fitness Classes', 'Join our expert-led classes designed for all fitness levels', 3),
('gym_training', 'gym', 'One-on-One Coaching', NULL, 'Personal Training Programs', 'Achieve your fitness goals faster with personalized guidance from our certified trainers', 4),
('gym_packages', 'gym', 'Exclusive Offers', NULL, 'Wellness Packages', 'Comprehensive packages designed for optimal health and relaxation', 5);

-- Rooms showcase section
INSERT INTO `section_headers` (`section_key`, `page`, `section_label`, `section_subtitle`, `section_title`, `section_description`, `display_order`) VALUES
('rooms_collection', 'rooms-showcase', 'Stay Collection', NULL, 'Pick Your Perfect Space', 'Suites and rooms crafted for business, romance, and family stays with direct booking flows', 1);

-- Conference sections
INSERT INTO `section_headers` (`section_key`, `page`, `section_label`, `section_subtitle`, `section_title`, `section_description`, `display_order`) VALUES
('conference_overview', 'conference', 'Professional Events', 'Where Business Meets Excellence', 'Conference & Meeting Facilities', 'State-of-the-art venues for your business needs', 1);

-- Events sections
INSERT INTO `section_headers` (`section_key`, `page`, `section_label`, `section_subtitle`, `section_title`, `section_description`, `display_order`) VALUES
('events_overview', 'events', 'Celebrations & Gatherings', NULL, 'Upcoming Events', 'Discover our curated experiences and special occasions', 1);
