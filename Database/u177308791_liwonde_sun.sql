/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19-11.8.3-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: u177308791_liwonde_sun
-- ------------------------------------------------------
-- Server version	11.8.3-MariaDB-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;

--
-- Table structure for table `admin_users`
--

DROP TABLE IF EXISTS `admin_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `admin_users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `role` enum('admin','receptionist','manager') NOT NULL DEFAULT 'receptionist',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_users`
--

/*!40000 ALTER TABLE `admin_users` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `admin_users` VALUES
(1,'admin','admin@liwondesunhotel.com','$2y$10$kHKXltLQhR3JuVFtHQ7mZ.KhVjTNKJf7tEU0IwD8HKzKdvyG1Cy/W','System Administrator','admin',1,NULL,'2026-01-20 19:08:40','2026-01-20 19:08:40'),
(2,'receptionist','reception@liwondesunhotel.com','$2y$10$OFHlFcgoqltOd7X6Z3IqVeg0961Adk9LxyfW8UBBfENSawMRZ3fF6','Front Desk','receptionist',1,'2026-01-20 19:09:56','2026-01-20 19:08:40','2026-01-20 19:09:56');
/*!40000 ALTER TABLE `admin_users` ENABLE KEYS */;
commit;

--
-- Table structure for table `booking_notes`
--

DROP TABLE IF EXISTS `booking_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `booking_notes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `booking_id` int(11) unsigned NOT NULL,
  `note_text` text NOT NULL,
  `created_by` int(11) unsigned DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_booking_id` (`booking_id`),
  KEY `idx_created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `booking_notes`
--

/*!40000 ALTER TABLE `booking_notes` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `booking_notes` ENABLE KEYS */;
commit;

--
-- Table structure for table `bookings`
--

DROP TABLE IF EXISTS `bookings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `bookings` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `booking_reference` varchar(20) NOT NULL,
  `room_id` int(11) unsigned NOT NULL,
  `guest_name` varchar(255) NOT NULL,
  `guest_email` varchar(255) NOT NULL,
  `guest_phone` varchar(50) NOT NULL,
  `guest_country` varchar(100) DEFAULT NULL,
  `guest_address` text DEFAULT NULL,
  `number_of_guests` int(11) NOT NULL DEFAULT 1,
  `check_in_date` date NOT NULL,
  `check_out_date` date NOT NULL,
  `number_of_nights` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `special_requests` text DEFAULT NULL,
  `status` enum('pending','confirmed','checked-in','checked-out','cancelled') NOT NULL DEFAULT 'pending',
  `payment_status` enum('unpaid','partial','paid') NOT NULL DEFAULT 'unpaid',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `booking_reference` (`booking_reference`),
  KEY `idx_booking_ref` (`booking_reference`),
  KEY `idx_room_id` (`room_id`),
  KEY `idx_guest_email` (`guest_email`),
  KEY `idx_status` (`status`),
  KEY `idx_dates` (`check_in_date`,`check_out_date`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bookings`
--

/*!40000 ALTER TABLE `bookings` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `bookings` VALUES
(1,'LSH2026001',1,'John Banda','john.banda@example.com','+265 999 123 456',NULL,NULL,2,'2026-01-25','2026-01-28',3,450000.00,NULL,'confirmed','unpaid','2026-01-20 19:08:40','2026-01-20 19:08:40'),
(2,'LSH2026002',2,'Sarah Phiri','sarah.phiri@example.com','+265 888 234 567',NULL,NULL,1,'2026-02-01','2026-02-03',2,280000.00,NULL,'pending','unpaid','2026-01-20 19:08:40','2026-01-20 19:08:40'),
(3,'LSH2026003',3,'Michael Chimbwanda','michael.c@example.com','+265 777 345 678',NULL,NULL,4,'2026-02-05','2026-02-08',3,600000.00,NULL,'pending','unpaid','2026-01-20 19:08:40','2026-01-20 19:08:40'),
(4,'LSH2026004',1,'Grace Mwale','grace.mwale@example.com','+265 666 456 789',NULL,NULL,2,'2026-02-10','2026-02-12',2,300000.00,NULL,'pending','unpaid','2026-01-20 19:08:40','2026-01-20 19:08:40');
/*!40000 ALTER TABLE `bookings` ENABLE KEYS */;
commit;

--
-- Table structure for table `conference_inquiries`
--

DROP TABLE IF EXISTS `conference_inquiries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `conference_inquiries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `inquiry_reference` varchar(50) NOT NULL,
  `conference_room_id` int(11) NOT NULL,
  `company_name` varchar(200) NOT NULL,
  `contact_person` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `event_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `number_of_attendees` int(11) NOT NULL,
  `event_type` varchar(100) DEFAULT NULL,
  `special_requirements` text DEFAULT NULL,
  `catering_required` tinyint(1) DEFAULT 0,
  `av_equipment` text DEFAULT NULL,
  `status` enum('pending','confirmed','cancelled','completed') DEFAULT 'pending',
  `total_amount` decimal(10,2) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `inquiry_reference` (`inquiry_reference`),
  KEY `idx_conference_inquiry_date` (`event_date`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `conference_inquiries`
--

/*!40000 ALTER TABLE `conference_inquiries` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `conference_inquiries` ENABLE KEYS */;
commit;

--
-- Table structure for table `conference_rooms`
--

DROP TABLE IF EXISTS `conference_rooms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `conference_rooms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `capacity` int(11) NOT NULL,
  `size_sqm` decimal(10,2) DEFAULT NULL,
  `hourly_rate` decimal(10,2) NOT NULL,
  `daily_rate` decimal(10,2) NOT NULL,
  `amenities` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_conference_room_active` (`is_active`,`display_order`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `conference_rooms`
--

/*!40000 ALTER TABLE `conference_rooms` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `conference_rooms` VALUES
(1,'Executive Boardroom','Intimate boardroom perfect for high-level meetings and presentations. Features mahogany furnishings, premium leather seating, and state-of-the-art video conferencing capabilities.',12,35.00,15000.00,100000.00,'Video Conferencing, Smart TV, Whiteboard, High-Speed WiFi, Coffee Service','images/conference/executive-boardroom.jpg',1,1,'2026-01-20 22:35:58','2026-01-20 22:35:58'),
(2,'Grand Conference Hall','Our largest conference space, ideal for seminars, workshops, and corporate events. Divisible into three sections with soundproof partitions for flexible event configurations.',150,200.00,35000.00,250000.00,'Stage & Podium, Professional Sound System, Projection Screen, WiFi, Air Conditioning, Breakout Rooms','images/conference/grand-hall.jpg',1,2,'2026-01-20 22:35:58','2026-01-20 22:35:58'),
(3,'Lakeside Meeting Room','Modern meeting space with panoramic views of the lake. Natural lighting and contemporary design create an inspiring environment for creative sessions and strategic planning.',30,60.00,20000.00,140000.00,'Projector & Screen, Video Conferencing, Whiteboard, WiFi, Lake View, Terrace Access','images/conference/lakeside-room.jpg',1,3,'2026-01-20 22:35:58','2026-01-20 22:35:58'),
(4,'Executive Boardroom','Intimate boardroom perfect for high-level meetings and presentations. Features mahogany furnishings, premium leather seating, and state-of-the-art video conferencing capabilities.',12,35.00,15000.00,100000.00,'Video Conferencing, Smart TV, Whiteboard, High-Speed WiFi, Coffee Service','images/conference/executive-boardroom.jpg',1,1,'2026-01-20 22:36:31','2026-01-20 22:36:31'),
(5,'Grand Conference Hall','Our largest conference space, ideal for seminars, workshops, and corporate events. Divisible into three sections with soundproof partitions for flexible event configurations.',150,200.00,35000.00,250000.00,'Stage & Podium, Professional Sound System, Projection Screen, WiFi, Air Conditioning, Breakout Rooms','images/conference/grand-hall.jpg',1,2,'2026-01-20 22:36:31','2026-01-20 22:36:31'),
(6,'Lakeside Meeting Room','Modern meeting space with panoramic views of the lake. Natural lighting and contemporary design create an inspiring environment for creative sessions and strategic planning.',30,60.00,20000.00,140000.00,'Projector & Screen, Video Conferencing, Whiteboard, WiFi, Lake View, Terrace Access','images/conference/lakeside-room.jpg',1,3,'2026-01-20 22:36:31','2026-01-20 22:36:31');
/*!40000 ALTER TABLE `conference_rooms` ENABLE KEYS */;
commit;

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `event_date` date NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `location` varchar(200) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `ticket_price` decimal(10,2) DEFAULT 0.00,
  `capacity` int(11) DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_events_date` (`event_date`,`is_active`),
  KEY `idx_events_featured` (`is_featured`,`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `events`
--

/*!40000 ALTER TABLE `events` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `events` VALUES
(1,'New Year Gala Dinner','Ring in the New Year with an elegant five-course dinner, live entertainment, and spectacular fireworks display over the lake. Dress code: Black tie.','2026-12-31','19:00:00','01:00:00','Grand Conference Hall','images/events/gala-dinner.jpg',50000.00,150,1,1,1,'2026-01-20 22:35:58','2026-01-20 22:35:58'),
(2,'Wine Tasting Evening','Join our sommelier for an exclusive tasting of premium South African wines paired with artisan cheeses and canapés. Learn about wine regions, varietals, and perfect food pairings.','2026-02-14','18:00:00','21:00:00','Lakeside Terrace','images/events/wine-tasting.jpg',25000.00,40,1,1,2,'2026-01-20 22:35:58','2026-01-20 22:35:58'),
(3,'Business Networking Breakfast','Monthly networking event for local business leaders and entrepreneurs. Complimentary breakfast buffet with opportunities to connect and collaborate.','2026-02-28','07:00:00','09:30:00','Executive Boardroom','images/events/business-breakfast.jpg',0.00,30,0,1,3,'2026-01-20 22:35:58','2026-01-20 22:35:58'),
(4,'Easter Sunday Brunch','Celebrate Easter with a lavish buffet brunch featuring international cuisines, live cooking stations, and entertainment for children. Perfect for the whole family.','2026-04-05','11:00:00','15:00:00','Restaurant & Terrace','images/events/easter-brunch.jpg',35000.00,100,1,1,4,'2026-01-20 22:35:58','2026-01-20 22:35:58'),
(5,'Lake Festival Cultural Night','Experience traditional Malawian culture with live music, dance performances, and authentic local cuisine. Supporting local artists and community initiatives.','2026-05-15','17:00:00','22:00:00','Outdoor Grounds','images/events/cultural-night.jpg',15000.00,200,1,1,5,'2026-01-20 22:35:58','2026-01-20 22:35:58'),
(6,'New Year Gala Dinner','Ring in the New Year with an elegant five-course dinner, live entertainment, and spectacular fireworks display over the lake. Dress code: Black tie.','2026-12-31','19:00:00','01:00:00','Grand Conference Hall','images/events/gala-dinner.jpg',50000.00,150,1,1,1,'2026-01-20 22:36:31','2026-01-20 22:36:31'),
(7,'Wine Tasting Evening','Join our sommelier for an exclusive tasting of premium South African wines paired with artisan cheeses and canapés. Learn about wine regions, varietals, and perfect food pairings.','2026-02-14','18:00:00','21:00:00','Lakeside Terrace','images/events/wine-tasting.jpg',25000.00,40,1,1,2,'2026-01-20 22:36:31','2026-01-20 22:36:31'),
(8,'Business Networking Breakfast','Monthly networking event for local business leaders and entrepreneurs. Complimentary breakfast buffet with opportunities to connect and collaborate.','2026-02-28','07:00:00','09:30:00','Executive Boardroom','images/events/business-breakfast.jpg',0.00,30,0,1,3,'2026-01-20 22:36:31','2026-01-20 22:36:31'),
(9,'Easter Sunday Brunch','Celebrate Easter with a lavish buffet brunch featuring international cuisines, live cooking stations, and entertainment for children. Perfect for the whole family.','2026-04-05','11:00:00','15:00:00','Restaurant & Terrace','images/events/easter-brunch.jpg',35000.00,100,1,1,4,'2026-01-20 22:36:31','2026-01-20 22:36:31'),
(10,'Lake Festival Cultural Night','Experience traditional Malawian culture with live music, dance performances, and authentic local cuisine. Supporting local artists and community initiatives.','2026-05-15','17:00:00','22:00:00','Outdoor Grounds','images/events/cultural-night.jpg',15000.00,200,1,1,5,'2026-01-20 22:36:31','2026-01-20 22:36:31');
/*!40000 ALTER TABLE `events` ENABLE KEYS */;
commit;

--
-- Table structure for table `facilities`
--

DROP TABLE IF EXISTS `facilities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `facilities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `short_description` varchar(255) DEFAULT NULL,
  `icon_class` varchar(100) DEFAULT NULL,
  `page_url` varchar(255) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_facilities_featured` (`is_featured`,`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `facilities`
--

/*!40000 ALTER TABLE `facilities` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `facilities` VALUES
(1,'Fine Dining Restaurant','fine-dining','Award-winning restaurant serving international and local cuisine. Our Michelin-star chef creates exceptional culinary experiences using the finest ingredients. Open 6am-11pm daily.','World-class cuisine with Michelin-star chef','fas fa-utensils','pages/restaurant.php',NULL,1,1,1,'2026-01-19 20:22:49','2026-01-20 14:17:17'),
(2,'Luxury Spa & Wellness','spa-wellness','Full-service spa offering massages, facials, and wellness treatments. Expert therapists provide personalized experiences using premium organic products. Includes sauna and steam room.','Rejuvenating spa treatments and wellness services','fas fa-spa',NULL,NULL,1,1,2,'2026-01-19 20:22:49','2026-01-19 20:22:49'),
(3,'Olympic Swimming Pool','swimming-pool','Olympic-sized outdoor pool with heated water, children\'s pool, waterslide, and poolside bar service. Perfect for relaxation and recreation year-round.','Heated Olympic pool with poolside service','fas fa-swimming-pool',NULL,NULL,1,1,3,'2026-01-19 20:22:49','2026-01-19 20:22:49'),
(4,'State-of-the-Art Fitness Center','fitness-center','Modern gym with personal trainers, cardio machines, weights, and dedicated yoga studio. Daily classes available. Open 24/7 for guests.','Premium gym with personal training available','fas fa-dumbbell','pages/gym.php',NULL,1,1,4,'2026-01-19 20:22:49','2026-01-20 14:17:17'),
(5,'High-Speed WiFi','wifi','Ultra-fast fiber internet throughout the hotel. Dedicated business center with meeting facilities and tech support available.','Complimentary ultra-fast internet access','fas fa-wifi',NULL,NULL,1,1,5,'2026-01-19 20:22:49','2026-01-19 20:22:49'),
(6,'24/7 Concierge Service','concierge','Dedicated concierge team for all your needs. Arrange tours, transportation, dining reservations, and special requests anytime.','Personalized service around the clock','fas fa-concierge-bell',NULL,NULL,1,1,6,'2026-01-19 20:22:49','2026-01-19 20:22:49');
/*!40000 ALTER TABLE `facilities` ENABLE KEYS */;
commit;

--
-- Table structure for table `footer_links`
--

DROP TABLE IF EXISTS `footer_links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `footer_links` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `column_name` varchar(100) NOT NULL,
  `link_text` varchar(100) NOT NULL,
  `link_url` varchar(255) NOT NULL,
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `footer_links`
--

/*!40000 ALTER TABLE `footer_links` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `footer_links` VALUES
(1,'About Hotel','About Us','/about',1,1),
(2,'About Hotel','Sustainability','/sustainability',2,1),
(3,'About Hotel','Awards','/awards',3,1),
(4,'About Hotel','History','/history',4,1),
(5,'Guest Services','Rooms & Suites','/rooms',1,1),
(6,'Guest Services','Facilities','/facilities',2,1),
(7,'Guest Services','Special Offers','/offers',3,1),
(8,'Guest Services','Group Bookings','/groups',4,1),
(9,'Dining & Entertainment','Fine Dining','/dining',1,1),
(10,'Dining & Entertainment','Spa Services','/spa',2,1),
(11,'Dining & Entertainment','Events & Conferences','/events',3,1),
(12,'Dining & Entertainment','Activities','/activities',4,1);
/*!40000 ALTER TABLE `footer_links` ENABLE KEYS */;
commit;

--
-- Table structure for table `gallery`
--

DROP TABLE IF EXISTS `gallery`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `gallery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(150) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `image_url` varchar(255) NOT NULL,
  `category` varchar(50) NOT NULL,
  `room_id` int(11) DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `room_id` (`room_id`),
  CONSTRAINT `gallery_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gallery`
--

/*!40000 ALTER TABLE `gallery` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `gallery` VALUES
(1,'Presidential Suite','Luxury accommodation','https://amazingarchitecture.com/storage/files/1/architecture-firms/zikzak-architects/hotel-interior-design/hotel-interior-design-zikzak-architects-3.jpg','rooms',1,2,1,'2026-01-19 20:22:49'),
(2,'Fine Dining Restaurant','World-class cuisine','images/hotel-exterior-1024x572.jpg','dining',NULL,2,1,'2026-01-19 20:22:49'),
(3,'Olympic Pool','Heated swimming pool','images/hotel-exterior-1024x572.jpg','facilities',NULL,3,1,'2026-01-19 20:22:49'),
(4,'Hotel Exterior','Main entrance','images/hotel-exterior-1024x572.jpg','exterior',NULL,4,1,'2026-01-19 20:22:49'),
(5,'Luxury Spa','Wellness center','images/hotel-exterior-1024x572.jpg','facilities',NULL,5,1,'2026-01-19 20:22:49'),
(6,'Sunset View','Evening beauty','images/hotel-exterior-1024x572.jpg','exterior',NULL,6,1,'2026-01-19 20:22:49'),
(7,'Presidential Suite Living Area','Spacious living area with premium furnishings','images/gallery/hotel-lobby.jpg','rooms',NULL,NULL,1,'2026-01-20 07:57:13'),
(8,'Hotel Exterior View','Stunning hotel facade during golden hour','images/gallery/hotel-exterior_1-1024x572.jpg','exterior',NULL,8,1,'2026-01-20 07:57:13'),
(9,'Pool Area Relaxation','Olympic pool with panoramic views','images/gallery/pool-area-1024x683.jpg','facilities',NULL,9,1,'2026-01-20 07:57:13'),
(10,'Fitness Excellence','State-of-the-art fitness facilities','images/gallery/fitness-center-1024x683.jpg','facilities',NULL,10,1,'2026-01-20 07:57:13'),
(11,'Presidential Suite Living Area','Spacious living area with premium furnishings','images/gallery/hotel-lobby.jpg','rooms',NULL,NULL,1,'2026-01-20 08:03:22'),
(12,'Hotel Exterior View','Stunning hotel facade during golden hour','images/gallery/hotel-exterior_1-1024x572.jpg','exterior',NULL,8,1,'2026-01-20 08:03:22'),
(13,'Pool Area Relaxation','Olympic pool with panoramic views','images/gallery/pool-area-1024x683.jpg','facilities',NULL,9,1,'2026-01-20 08:03:22'),
(14,'Fitness Excellence','State-of-the-art fitness facilities','images/gallery/fitness-center-1024x683.jpg','facilities',NULL,10,1,'2026-01-20 08:03:22'),
(15,'Presidential Suite Living Area','Spacious living area with premium furnishings','images/gallery/hotel-lobby.jpg','rooms',NULL,NULL,1,'2026-01-20 08:07:18'),
(16,'Hotel Exterior View','Stunning hotel facade during golden hour','images/gallery/hotel-exterior_1-1024x572.jpg','exterior',NULL,8,1,'2026-01-20 08:07:18'),
(17,'Pool Area Relaxation','Olympic pool with panoramic views','images/gallery/pool-area-1024x683.jpg','facilities',NULL,9,1,'2026-01-20 08:07:18'),
(18,'Fitness Excellence','State-of-the-art fitness facilities','images/gallery/fitness-center-1024x683.jpg','facilities',NULL,10,1,'2026-01-20 08:07:18'),
(19,'Presidential Suite - Master Bedroom','Spacious master bedroom with king bed and city views','images/rooms/presidential-master.jpg','rooms',1,NULL,1,'2026-01-20 16:07:07'),
(20,'Presidential Suite - Living Area','Elegant living room with contemporary furnishings','images/rooms/presidential-living.jpg','rooms',1,NULL,1,'2026-01-20 16:07:07'),
(21,'Presidential Suite - Bathroom','Luxurious marble bathroom with spa features','images/rooms/presidential-bathroom.jpg','rooms',1,NULL,1,'2026-01-20 16:07:07'),
(22,'Presidential Suite - Terrace','Private terrace with panoramic city views','images/rooms/presidential-terrace.jpg','rooms',1,NULL,1,'2026-01-20 16:07:07'),
(23,'Executive Suite - Bedroom','Premium bedroom with king bed','images/rooms/executive-bedroom.jpg','rooms',2,1,1,'2026-01-20 16:07:07'),
(24,'Executive Suite - Work Area','Dedicated workspace with desk and business amenities','images/rooms/executive-work.jpg','rooms',2,2,1,'2026-01-20 16:07:07'),
(25,'Executive Suite - Lounge','Comfortable lounge area','images/rooms/executive-lounge.jpg','rooms',2,3,1,'2026-01-20 16:07:07'),
(26,'Executive Suite - Bathroom','Modern bathroom with premium toiletries','images/rooms/executive-bathroom.jpg','rooms',2,4,1,'2026-01-20 16:07:07'),
(27,'Family Suite - Main Bedroom','Spacious master bedroom with king bed','images/rooms/family-main.jpg','rooms',3,1,1,'2026-01-20 16:07:07'),
(28,'Family Suite - Second Bedroom','Comfortable second bedroom with double bed','images/rooms/family-second.jpg','rooms',3,2,1,'2026-01-20 16:07:07'),
(29,'Family Suite - Living Area','Shared living and dining space','images/rooms/family-living.jpg','rooms',3,3,1,'2026-01-20 16:07:07'),
(30,'Family Suite - Kitchen','Kitchenette with cooking facilities','images/rooms/family-kitchen.jpg','rooms',3,4,1,'2026-01-20 16:07:07'),
(31,'Presidential Suite - Master Bedroom','Spacious master bedroom with king bed and city views','https://amazingarchitecture.com/storage/files/1/architecture-firms/zikzak-architects/hotel-interior-design/hotel-interior-design-zikzak-architects-2.jpg','rooms',1,4,1,'2026-01-20 16:31:10'),
(32,'Presidential Suite - Living Area','Elegant living room with contemporary furnishings','https://amazingarchitecture.com/storage/files/1/architecture-firms/zikzak-architects/hotel-interior-design/hotel-interior-design-zikzak-architects-2.jpg','rooms',1,3,1,'2026-01-20 16:31:10'),
(33,'Presidential Suite - Bathroom','Luxurious marble bathroom with spa features','https://amazingarchitecture.com/storage/files/1/architecture-firms/zikzak-architects/hotel-interior-design/hotel-interior-design-zikzak-architects-1.jpg','rooms',1,1,1,'2026-01-20 16:31:10'),
(34,'Presidential Suite - Terrace','Private terrace with panoramic city views','https://source.unsplash.com/1200x1200/?hotel,terrace,view,city','rooms',1,NULL,1,'2026-01-20 16:31:10'),
(35,'Executive Suite - Bedroom','Premium bedroom with king bed','https://source.unsplash.com/1200x1200/?hotel,executive,bedroom','rooms',2,1,1,'2026-01-20 16:31:10'),
(36,'Executive Suite - Work Area','Dedicated workspace with desk and business amenities','https://source.unsplash.com/1200x1200/?hotel,workspace,desk','rooms',2,2,1,'2026-01-20 16:31:10'),
(37,'Executive Suite - Lounge','Comfortable lounge area','https://source.unsplash.com/1200x1200/?hotel,lounge,sofa','rooms',2,3,1,'2026-01-20 16:31:10'),
(38,'Executive Suite - Bathroom','Modern bathroom with premium toiletries','https://source.unsplash.com/1200x1200/?hotel,bathroom,modern','rooms',2,4,1,'2026-01-20 16:31:10'),
(39,'Family Suite - Main Bedroom','Spacious master bedroom with king bed','https://source.unsplash.com/1200x1200/?family,hotel,room','rooms',3,1,1,'2026-01-20 16:31:10'),
(40,'Family Suite - Second Bedroom','Comfortable second bedroom with double bed','https://source.unsplash.com/1200x1200/?kids,bedroom,hotel','rooms',3,2,1,'2026-01-20 16:31:10'),
(41,'Family Suite - Living Area','Shared living and dining space','https://source.unsplash.com/1200x1200/?family,living,room','rooms',3,3,1,'2026-01-20 16:31:10'),
(42,'Family Suite - Kitchen','Kitchenette with cooking facilities','https://source.unsplash.com/1200x1200/?kitchenette,hotel,apartment','rooms',3,4,1,'2026-01-20 16:31:10');
/*!40000 ALTER TABLE `gallery` ENABLE KEYS */;
commit;

--
-- Table structure for table `gym_classes`
--

DROP TABLE IF EXISTS `gym_classes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `gym_classes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `day_label` varchar(120) NOT NULL,
  `time_label` varchar(50) NOT NULL,
  `level_label` varchar(80) DEFAULT 'All Levels',
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gym_classes`
--

/*!40000 ALTER TABLE `gym_classes` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `gym_classes` VALUES
(13,'Morning Yoga Flow','Start your day with energizing yoga sequences','Monday - Friday','6:30 AM','All Levels',1,1),
(14,'HIIT Bootcamp','High-intensity interval training for maximum results','Tuesday & Thursday','7:00 AM','Intermediate',2,1),
(15,'Pilates Core','Strengthen your core with controlled movements','Wednesday & Saturday','8:00 AM','All Levels',3,1),
(16,'Evening Meditation','Wind down with guided meditation and breathing','Daily','6:00 PM','All Levels',4,1);
/*!40000 ALTER TABLE `gym_classes` ENABLE KEYS */;
commit;

--
-- Table structure for table `gym_content`
--

DROP TABLE IF EXISTS `gym_content`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `gym_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hero_title` varchar(200) NOT NULL DEFAULT 'Fitness & Wellness Center',
  `hero_subtitle` varchar(200) NOT NULL DEFAULT 'Health & Vitality',
  `hero_description` text DEFAULT NULL,
  `hero_image_path` varchar(255) DEFAULT 'images/gym/hero-bg.jpg',
  `wellness_title` varchar(200) NOT NULL DEFAULT 'Transform Your Body & Mind',
  `wellness_description` text DEFAULT NULL,
  `wellness_image_path` varchar(255) DEFAULT 'images/gym/fitness-center.jpg',
  `badge_text` varchar(120) DEFAULT 'Award-Winning Facilities',
  `personal_training_image_path` varchar(255) DEFAULT 'images/gym/personal-training.jpg',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gym_content`
--

/*!40000 ALTER TABLE `gym_content` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `gym_content` VALUES
(4,'Fitness & Wellness Center','Health & Vitality','State-of-the-art facilities designed to elevate your physical and mental well-being','images/gym/hero-bg.jpg','Transform Your Body & Mind','Our fitness and wellness center offers everything you need to maintain your health routine while traveling or start a new wellness journey.','images/gym/fitness-center.jpg','Award-Winning Facilities','images/gym/personal-training.jpg',1,'2026-01-20 15:26:43','2026-01-20 15:26:43');
/*!40000 ALTER TABLE `gym_content` ENABLE KEYS */;
commit;

--
-- Table structure for table `gym_facilities`
--

DROP TABLE IF EXISTS `gym_facilities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `gym_facilities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `icon_class` varchar(100) NOT NULL DEFAULT 'fas fa-check',
  `title` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gym_facilities`
--

/*!40000 ALTER TABLE `gym_facilities` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `gym_facilities` VALUES
(19,'fas fa-running','Cardio Zone','Treadmills, ellipticals, bikes, and rowers with entertainment screens and HR monitoring',1,1),
(20,'fas fa-dumbbell','Strength Training','Full range of free weights, barbells, and functional rigs',2,1),
(21,'fas fa-child','Yoga & Pilates Studio','Dedicated studio for yoga, pilates, and meditation with daily classes',3,1),
(22,'fas fa-swimming-pool','Lap Pool','25-meter heated pool ideal for swim workouts and aqua aerobics',4,1),
(23,'fas fa-hot-tub','Spa & Sauna','Traditional sauna, steam room, and jacuzzi for recovery',5,1),
(24,'fas fa-apple-alt','Nutrition Bar','Smoothies, protein shakes, and healthy snacks to fuel your workout',6,1);
/*!40000 ALTER TABLE `gym_facilities` ENABLE KEYS */;
commit;

--
-- Table structure for table `gym_features`
--

DROP TABLE IF EXISTS `gym_features`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `gym_features` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `icon_class` varchar(100) NOT NULL DEFAULT 'fas fa-dumbbell',
  `title` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gym_features`
--

/*!40000 ALTER TABLE `gym_features` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `gym_features` VALUES
(13,'fas fa-dumbbell','Modern Equipment','Latest cardio machines, free weights, and resistance training equipment',1,1),
(14,'fas fa-user-md','Personal Training','Certified trainers available for one-on-one sessions and customized programs',2,1),
(15,'fas fa-spa','Spa & Recovery','Massage therapy, sauna, and steam rooms for post-workout relaxation',3,1),
(16,'fas fa-clock','Flexible Hours','Open daily from 5:30 AM to 10:00 PM for your convenience',4,1);
/*!40000 ALTER TABLE `gym_features` ENABLE KEYS */;
commit;

--
-- Table structure for table `gym_packages`
--

DROP TABLE IF EXISTS `gym_packages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `gym_packages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `icon_class` varchar(100) DEFAULT 'fas fa-leaf',
  `includes_text` text DEFAULT NULL COMMENT 'Line-separated bullet points',
  `duration_label` varchar(50) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `currency_code` varchar(10) DEFAULT 'MWK',
  `cta_text` varchar(120) DEFAULT 'Book Package',
  `cta_link` varchar(255) DEFAULT '#book',
  `is_featured` tinyint(1) DEFAULT 0,
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gym_packages`
--

/*!40000 ALTER TABLE `gym_packages` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `gym_packages` VALUES
(7,'Rejuvenation Retreat','fas fa-leaf','3 personal training sessions\nDaily yoga classes\n2 spa massages\nNutrition consultation\nComplimentary smoothie bar access','5 Days',45000.00,'MWK','Book Package','#book',0,1,1),
(8,'Ultimate Wellness','fas fa-star','5 personal training sessions\nUnlimited group classes\n4 spa treatments\nFull nutrition program\nFitness assessment & tracking\nComplimentary wellness amenities','7 Days',8500.00,'MWK','Book Package','#book',1,2,1),
(9,'Fitness Kickstart','fas fa-dumbbell','2 personal training sessions\nGroup class pass (5 classes)\n1 spa massage\nFitness assessment\nWorkout plan to take home','3 Days',28000.00,'MWK','Book Package','#book',0,3,1);
/*!40000 ALTER TABLE `gym_packages` ENABLE KEYS */;
commit;

--
-- Table structure for table `hero_slides`
--

DROP TABLE IF EXISTS `hero_slides`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `hero_slides` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `subtitle` varchar(200) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image_path` varchar(255) NOT NULL,
  `primary_cta_text` varchar(100) DEFAULT NULL,
  `primary_cta_link` varchar(255) DEFAULT NULL,
  `secondary_cta_text` varchar(100) DEFAULT NULL,
  `secondary_cta_link` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hero_slides`
--

/*!40000 ALTER TABLE `hero_slides` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `hero_slides` VALUES
(1,'Experience Unparalleled Luxury','Where Luxury Meets Nature','Discover the perfect blend of comfort, elegance, and exceptional service at Malawi\'s premier destination','images/hero/slide1.jpg','Book a Suite','#book','View Rooms','#rooms',1,1,'2026-01-20 07:55:39','2026-01-20 07:55:39'),
(2,'Sunrise Over the Shire River','Golden hours above pristine waters','Wake to breathtaking Malawian sunrises framed by elegant interiors and world-class amenities','images/hero/slide2.jpg','See Gallery','#gallery','Plan Your Stay','#contact',1,2,'2026-01-20 07:55:39','2026-01-20 07:55:39'),
(3,'Award-Winning Dining','Michelin-Star Culinary Excellence','Savor exceptional cuisine crafted by our renowned chefs using the finest local and international ingredients','images/hero/slide3.jpg','View Menu','#facilities','Reserve a Table','#contact',1,3,'2026-01-20 07:55:39','2026-01-20 07:55:39'),
(4,'Ultimate Relaxation & Wellness','Your sanctuary of serenity','Indulge in our luxury spa, Olympic pool, and state-of-the-art fitness facilities designed for your well-being','images/hero/slide4.jpg','Explore Spa','#facilities','Book Treatment','#book',1,4,'2026-01-20 07:55:39','2026-01-20 07:55:39');
/*!40000 ALTER TABLE `hero_slides` ENABLE KEYS */;
commit;

--
-- Table structure for table `hotel_gallery`
--

DROP TABLE IF EXISTS `hotel_gallery`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `hotel_gallery` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(500) NOT NULL,
  `category` varchar(100) DEFAULT 'general' COMMENT 'e.g., exterior, interior, rooms, facilities, dining, events',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_active_order` (`is_active`,`display_order`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hotel_gallery`
--

/*!40000 ALTER TABLE `hotel_gallery` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `hotel_gallery` VALUES
(1,'Hotel Exterior View','Stunning front view of Liwonde Sun Hotel','images/hotel_gallery/art.jpg','exterior',1,1,'2026-01-20 17:25:33','2026-01-20 17:27:39'),
(2,'Luxury Pool Area','Infinity pool overlooking the Shire River','images/hotel_gallery/Outside2.png','facilities',1,2,'2026-01-20 17:25:33','2026-01-20 17:29:31'),
(3,'Elegant Dining Hall','Our award-winning restaurant interior','https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=800&q=80','dining',1,3,'2026-01-20 17:25:33','2026-01-20 17:25:33'),
(4,'Executive Suite','Spacious suite with panoramic views','https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=800&q=80','rooms',1,4,'2026-01-20 17:25:33','2026-01-20 17:25:33'),
(5,'Rooftop Lounge','Sunset views from our rooftop bar','https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=800&q=80','facilities',1,5,'2026-01-20 17:25:33','2026-01-20 17:25:33'),
(6,'Grand Lobby','Welcome to luxury and elegance','https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=800&q=80','interior',1,6,'2026-01-20 17:25:33','2026-01-20 17:25:33'),
(7,'Spa & Wellness','Rejuvenate in our world-class spa','https://images.unsplash.com/photo-1540555700478-4be289fbecef?w=800&q=80','facilities',1,7,'2026-01-20 17:25:33','2026-01-20 17:25:33'),
(8,'Garden Terrace','Lush gardens perfect for events','https://images.unsplash.com/photo-1519167758481-83f29da8c11f?w=800&q=80','exterior',1,8,'2026-01-20 17:25:33','2026-01-20 17:25:33');
/*!40000 ALTER TABLE `hotel_gallery` ENABLE KEYS */;
commit;

--
-- Table structure for table `menu_categories`
--

DROP TABLE IF EXISTS `menu_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `menu_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu_categories`
--

/*!40000 ALTER TABLE `menu_categories` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `menu_categories` VALUES
(1,'Breakfast','breakfast','Start your day with our gourmet breakfast selection',1,1),
(2,'Appetizers','appetizers','Elegant starters to begin your meal',2,1),
(3,'Main Courses','main-courses','Exquisite main dishes prepared by our Michelin-star chef',3,1),
(4,'Desserts','desserts','Indulgent sweet creations',4,1),
(5,'Beverages','beverages','Premium drinks and cocktails',5,1);
/*!40000 ALTER TABLE `menu_categories` ENABLE KEYS */;
commit;

--
-- Table structure for table `menu_items`
--

DROP TABLE IF EXISTS `menu_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `menu_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(8,2) NOT NULL,
  `category` varchar(100) NOT NULL,
  `menu_type` varchar(50) NOT NULL DEFAULT 'Food',
  `category_order` int(11) DEFAULT 0,
  `item_order` int(11) DEFAULT 0,
  `tags` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `category` (`category`),
  KEY `menu_type` (`menu_type`),
  KEY `is_active` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu_items`
--

/*!40000 ALTER TABLE `menu_items` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `menu_items` VALUES
(1,'Smoked Duck Breast','Tender duck breast with berry reduction and microgreens',1900.00,'Appetizers','Food',1,1,'Gluten-Free, Premium',1,'2026-01-20 10:13:28','2026-01-20 22:09:51'),
(2,'Lobster Bisque','Creamy lobster soup with truffle oil and crème fraîche',1600.00,'Appetizers','Food',1,2,'Shellfish, Classic',1,'2026-01-20 10:13:28','2026-01-20 10:47:45'),
(3,'Pan-Seared Scallops','Three succulent scallops with lemon butter and asparagus',6000.00,'Appetizers','Food',1,3,'Shellfish, Premium',1,'2026-01-20 10:13:28','2026-01-20 22:10:07'),
(4,'Foie Gras Terrine','House-made foie gras with brioche toast and fig jam',25.50,'Appetizers','Food',1,4,'Premium, Delicacy',1,'2026-01-20 10:13:28','2026-01-20 10:13:28'),
(5,'Wagyu Beef Steak','Premium A5 Wagyu with seasonal vegetables and miso sauce',65.00,'Main Courses','Food',2,1,'Beef, Premium',1,'2026-01-20 10:13:28','2026-01-20 10:13:28'),
(6,'Dover Sole Meunière','Whole Dover sole with brown butter and fresh lemon',48.00,'Main Courses','Food',2,2,'Fish, Classic',1,'2026-01-20 10:13:28','2026-01-20 10:13:28'),
(7,'Rack of Lamb','Herb-crusted lamb with rosemary jus and root vegetables',52.00,'Main Courses','Food',2,3,'Lamb, Traditional',1,'2026-01-20 10:13:28','2026-01-20 10:13:28'),
(8,'Pan-Seared Duck Breast','With orange gastrique and crispy confit potatoes',44.00,'Main Courses','Food',2,4,'Poultry, Modern',1,'2026-01-20 10:13:28','2026-01-20 10:13:28'),
(9,'Lobster Tail','Fresh Atlantic lobster tail with drawn butter and champagne sauce',58.00,'Main Courses','Food',2,5,'Shellfish, Premium',1,'2026-01-20 10:13:28','2026-01-20 10:13:28'),
(10,'Chocolate Soufflé','Warm chocolate soufflé with vanilla crème anglaise',14.50,'Desserts','Food',3,1,'Vegetarian, Decadent',1,'2026-01-20 10:13:28','2026-01-20 22:03:33'),
(11,'Crème Brûlée','Classic vanilla bean crème brûlée with torched sugar crust',12.00,'Desserts','Food',3,2,'Vegetarian, Classic',1,'2026-01-20 10:13:28','2026-01-20 10:13:28'),
(12,'Lemon Tart','Crispy pastry with tangy lemon curd and meringue',13.00,'Desserts','Food',3,3,'Vegetarian, Citrus',1,'2026-01-20 10:13:28','2026-01-20 10:13:28'),
(13,'Tiramisu','Traditional Italian tiramisu with layers of mascarpone and espresso',11.50,'Desserts','Food',3,4,'Vegetarian, Italian',1,'2026-01-20 10:13:28','2026-01-20 10:13:28'),
(14,'Espresso','Rich Italian espresso',6.00,'Coffee','Coffee',1,1,'Hot, Premium',1,'2026-01-20 10:13:28','2026-01-20 10:13:28'),
(15,'Cappuccino','Creamy cappuccino with artistic latte art',8.50,'Coffee','Coffee',1,2,'Hot, Classic',1,'2026-01-20 10:13:28','2026-01-20 10:13:28'),
(16,'Cortado','Perfect balance of espresso and steamed milk',7.50,'Coffee','Coffee',1,3,'Hot, Balanced',1,'2026-01-20 10:13:28','2026-01-20 10:13:28'),
(17,'Latte','Smooth latte with your choice of milk',8.00,'Coffee','Coffee',1,4,'Hot, Creamy',1,'2026-01-20 10:13:28','2026-01-20 10:13:28'),
(18,'Macchiato','Espresso marked with a touch of foam',7.00,'Coffee','Coffee',1,5,'Hot, Strong',1,'2026-01-20 10:13:28','2026-01-20 10:13:28'),
(19,'Americano','Bold americano with hot water',6.50,'Coffee','Coffee',1,6,'Hot, Classic',1,'2026-01-20 10:13:28','2026-01-20 10:13:28'),
(20,'Mocha','Rich espresso with chocolate and steamed milk',9.50,'Coffee','Coffee',1,7,'Hot, Chocolate',1,'2026-01-20 10:13:28','2026-01-20 10:13:28'),
(21,'Iced Coffee','Cold brew coffee served over ice',7.50,'Coffee','Coffee',1,8,'Cold, Refreshing',1,'2026-01-20 10:13:28','2026-01-20 10:13:28'),
(22,'Flat White','Espresso with velvety microfoam milk',9.00,'Coffee','Coffee',1,9,'Hot, Premium',1,'2026-01-20 10:13:28','2026-01-20 10:13:28'),
(23,'Irish Coffee','Coffee with whiskey, sugar, and whipped cream',14.00,'Coffee','Coffee',1,10,'Hot, Alcohol',1,'2026-01-20 10:13:28','2026-01-20 10:13:28'),
(24,'Château Margaux 2015','Bordeaux blend, full-bodied with dark fruit notes',185.00,'Wine','Bar',1,1,'Red Wine, Bordeaux',1,'2026-01-20 10:13:28','2026-01-20 10:13:28'),
(25,'Opus One 2019','California Cabernet blend, elegant and balanced',165.00,'Wine','Bar',1,2,'Red Wine, California',1,'2026-01-20 10:13:28','2026-01-20 10:13:28'),
(26,'Chablis Grand Cru','Crisp and mineral, perfect for seafood pairing',65.00,'Wine','Bar',1,3,'White Wine, French',1,'2026-01-20 10:13:28','2026-01-20 10:13:28'),
(27,'Champagne Cristal','Prestige cuvée with fine bubbles and complexity',275.00,'Wine','Bar',1,4,'Champagne, Premium',1,'2026-01-20 10:13:28','2026-01-20 10:13:28'),
(28,'Margarita','Classic tequila, lime, and triple sec',12.00,'Cocktails','Bar',2,1,'Tequila, Classic',1,'2026-01-20 10:13:28','2026-01-20 10:13:28'),
(29,'Mojito','Rum with fresh mint, lime, and soda',13.50,'Cocktails','Bar',2,2,'Rum, Refreshing',1,'2026-01-20 10:13:28','2026-01-20 10:13:28'),
(30,'Manhattan','Whiskey, vermouth, and bitters',14.00,'Cocktails','Bar',2,3,'Whiskey, Classic',1,'2026-01-20 10:13:28','2026-01-20 10:13:28'),
(31,'Espresso Martini','Vodka, coffee liqueur, and fresh espresso',15.00,'Cocktails','Bar',2,4,'Vodka, Coffee',1,'2026-01-20 10:13:28','2026-01-20 10:13:28'),
(32,'Old Fashioned','Whiskey, sugar, bitters, and orange twist',14.50,'Cocktails','Bar',2,5,'Whiskey, Classic',1,'2026-01-20 10:13:28','2026-01-20 10:13:28'),
(33,'Cosmopolitan','Vodka, cranberry, lime, and triple sec',13.00,'Cocktails','Bar',2,6,'Vodka, Fruity',1,'2026-01-20 10:13:28','2026-01-20 10:13:28'),
(34,'Piña Colada','Rum, coconut cream, and pineapple',12.50,'Cocktails','Bar',2,7,'Rum, Tropical',1,'2026-01-20 10:13:28','2026-01-20 10:13:28'),
(35,'Daiquiri','White rum, fresh lime juice, and sugar',11.50,'Cocktails','Bar',2,8,'Rum, Classic',1,'2026-01-20 10:13:28','2026-01-20 10:13:28'),
(36,'Craft Beer - IPA','Bold hoppy India Pale Ale',8.50,'Beer','Bar',3,1,'IPA, Craft',1,'2026-01-20 10:13:28','2026-01-20 10:13:28'),
(37,'Craft Beer - Stout','Rich and creamy stout with chocolate notes',9.00,'Beer','Bar',3,2,'Stout, Craft',1,'2026-01-20 10:13:28','2026-01-20 10:13:28'),
(38,'Craft Beer - Lager','Crisp and refreshing lager',7.50,'Beer','Bar',3,3,'Lager, Craft',1,'2026-01-20 10:13:28','2026-01-20 10:13:28'),
(39,'Fresh Orange Juice','Freshly squeezed orange juice',8.00,'Non-Alcoholic','Bar',4,1,'Fresh, Juice',1,'2026-01-20 10:13:28','2026-01-20 10:13:28'),
(40,'Sparkling Water','Perrier or San Pellegrino',7.00,'Non-Alcoholic','Bar',4,2,'Sparkling, Refreshing',1,'2026-01-20 10:13:28','2026-01-20 10:13:28');
/*!40000 ALTER TABLE `menu_items` ENABLE KEYS */;
commit;

--
-- Table structure for table `newsletter_subscribers`
--

DROP TABLE IF EXISTS `newsletter_subscribers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `newsletter_subscribers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(150) NOT NULL,
  `subscription_status` enum('active','unsubscribed') DEFAULT 'active',
  `subscribed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `unsubscribed_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `newsletter_subscribers`
--

/*!40000 ALTER TABLE `newsletter_subscribers` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `newsletter_subscribers` ENABLE KEYS */;
commit;

--
-- Table structure for table `policies`
--

DROP TABLE IF EXISTS `policies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `policies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `slug` varchar(100) NOT NULL,
  `title` varchar(150) NOT NULL,
  `summary` varchar(255) DEFAULT NULL,
  `content` text NOT NULL,
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `policies`
--

/*!40000 ALTER TABLE `policies` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `policies` VALUES
(1,'booking-policy','Booking Policy','Flexible bookings with secure guarantees','Bookings are confirmed upon receipt of payment guarantee. Amendments are subject to availability. Early check-in and late check-out are available on request and may incur additional charges.',1,1,'2026-01-20 10:54:23'),
(2,'cancellation-policy','Cancellation Policy','Simple cancellations with fair terms','Cancellations up to 48 hours before arrival are free of charge. Within 48 hours or no-shows incur the first night charge. Non-refundable rates are fully prepaid and non-changeable.',2,1,'2026-01-20 10:54:23'),
(3,'dining-policy','Dining Policy','Elegant dining etiquette','Smart casual dress code applies after 6pm. Outside food and beverages are not permitted in dining venues. Allergy and dietary requests are accommodated with advance notice.',3,1,'2026-01-20 10:54:23'),
(4,'faqs','FAQs','Quick answers to common questions','Check-in: 14:00, Check-out: 11:00. Airport transfers can be arranged. Children are welcome; extra beds available on request. High-speed WiFi is complimentary throughout the property.',4,1,'2026-01-20 10:54:23');
/*!40000 ALTER TABLE `policies` ENABLE KEYS */;
commit;

--
-- Table structure for table `restaurant_gallery`
--

DROP TABLE IF EXISTS `restaurant_gallery`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `restaurant_gallery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `image_path` varchar(255) NOT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `category` varchar(50) DEFAULT 'restaurant' COMMENT 'restaurant, bar, dining-area, food',
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `restaurant_gallery`
--

/*!40000 ALTER TABLE `restaurant_gallery` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `restaurant_gallery` VALUES
(2,'images/restaurant/dining-area-2.jpg','Intimate indoor seating','dining-area',2,1,'2026-01-20 14:17:17'),
(3,'images/restaurant/bar-area.jpg','Premium bar with signature cocktails','bar',3,1,'2026-01-20 14:17:17'),
(4,'images/restaurant/food-platter.jpg','Fresh seafood platter','food',4,1,'2026-01-20 14:17:17'),
(13,'images/restaurant/dining-area-1.jpg','Elegant dining area with panoramic views','dining-area',1,1,'2026-01-20 15:22:41'),
(17,'images/restaurant/fine-dining.jpg','Fine dining experience','restaurant',5,1,'2026-01-20 15:22:41'),
(18,'images/restaurant/outdoor-terrace.jpg','Alfresco dining terrace','dining-area',6,1,'2026-01-20 15:22:41');
/*!40000 ALTER TABLE `restaurant_gallery` ENABLE KEYS */;
commit;

--
-- Table structure for table `restaurant_menu`
--

DROP TABLE IF EXISTS `restaurant_menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `restaurant_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(100) NOT NULL COMMENT 'Breakfast, Lunch, Dinner, Drinks, Bar, Desserts, etc.',
  `item_name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `currency_code` varchar(10) DEFAULT 'MWK',
  `image_path` varchar(255) DEFAULT NULL,
  `is_available` tinyint(1) DEFAULT 1,
  `is_featured` tinyint(1) DEFAULT 0 COMMENT 'Featured items shown prominently',
  `is_vegetarian` tinyint(1) DEFAULT 0,
  `is_vegan` tinyint(1) DEFAULT 0,
  `allergens` varchar(255) DEFAULT NULL COMMENT 'Comma-separated allergen list',
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `category` (`category`),
  KEY `is_available` (`is_available`),
  KEY `is_featured` (`is_featured`)
) ENGINE=InnoDB AUTO_INCREMENT=131 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `restaurant_menu`
--

/*!40000 ALTER TABLE `restaurant_menu` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `restaurant_menu` VALUES
(1,'Breakfast','Continental Breakfast','Assorted pastries, fresh fruits, yogurt, cereals, and freshly brewed coffee',2500.00,'MWK',NULL,1,1,1,0,NULL,1,'2026-01-20 14:17:17','2026-01-20 14:17:17'),
(2,'Breakfast','Full English Breakfast','Eggs, bacon, sausages, grilled tomatoes, mushrooms, beans, and toast',3500.00,'MWK',NULL,1,0,0,0,NULL,2,'2026-01-20 14:17:17','2026-01-20 14:17:17'),
(3,'Breakfast','Malawian Breakfast Platter','Nsima with traditional relish, fresh mandasi, and masala tea',2800.00,'MWK',NULL,1,1,0,0,NULL,3,'2026-01-20 14:17:17','2026-01-20 14:17:17'),
(4,'Breakfast','Pancake Stack','Fluffy pancakes with maple syrup, fresh berries, and whipped cream',2200.00,'MWK',NULL,1,0,1,0,NULL,4,'2026-01-20 14:17:17','2026-01-20 14:17:17'),
(5,'Lunch','Chambo Fish & Chips','Fresh chambo from Lake Malawi, crispy chips, and tartar sauce',4500.00,'MWK',NULL,1,1,0,0,NULL,1,'2026-01-20 14:17:17','2026-01-20 14:17:17'),
(6,'Lunch','Grilled Chicken Caesar Salad','Tender chicken breast, crisp romaine, parmesan, croutons, and Caesar dressing',3800.00,'MWK',NULL,1,0,0,0,NULL,2,'2026-01-20 14:17:17','2026-01-20 14:17:17'),
(7,'Lunch','Vegetable Curry with Rice','Aromatic curry with seasonal vegetables, served with basmati rice',3200.00,'MWK',NULL,1,1,1,0,NULL,3,'2026-01-20 14:17:17','2026-01-20 14:17:17'),
(8,'Lunch','Club Sandwich Deluxe','Triple-decker with turkey, bacon, lettuce, tomato, and fries',3500.00,'MWK',NULL,1,0,0,0,NULL,4,'2026-01-20 14:17:17','2026-01-20 14:17:17'),
(9,'Dinner','Grilled T-Bone Steak','Premium aged beef, herb butter, roasted vegetables, and choice of sides',8500.00,'MWK',NULL,1,1,0,0,NULL,1,'2026-01-20 14:17:17','2026-01-20 14:17:17'),
(10,'Dinner','Pan-Seared Chambo','Lake Malawi chambo with lemon butter sauce, seasonal vegetables',6500.00,'MWK',NULL,1,1,0,0,NULL,2,'2026-01-20 14:17:17','2026-01-20 14:17:17'),
(11,'Dinner','Slow-Roasted Lamb Shank','Tender lamb with red wine jus, creamy mashed potatoes, and greens',7800.00,'MWK',NULL,1,0,0,0,NULL,3,'2026-01-20 14:17:17','2026-01-20 14:17:17'),
(12,'Dinner','Vegetarian Risotto','Creamy mushroom and truffle risotto with parmesan and fresh herbs',4500.00,'MWK',NULL,1,0,1,0,NULL,4,'2026-01-20 14:17:17','2026-01-20 14:17:17'),
(13,'Dinner','Seafood Platter','Grilled prawns, calamari, fish fillet, and mussels with garlic butter',9500.00,'MWK',NULL,1,1,0,0,NULL,5,'2026-01-20 14:17:17','2026-01-20 14:17:17'),
(14,'Drinks','Fresh Tropical Juice','Mango, pineapple, or passion fruit - freshly squeezed',800.00,'MWK',NULL,1,0,1,0,NULL,1,'2026-01-20 14:17:17','2026-01-20 14:17:17'),
(15,'Drinks','Iced Coffee','Chilled espresso with milk and vanilla syrup',1200.00,'MWK',NULL,1,0,1,0,NULL,2,'2026-01-20 14:17:17','2026-01-20 14:17:17'),
(16,'Drinks','Malawian Masala Tea','Spiced tea with ginger, cardamom, and cinnamon',600.00,'MWK',NULL,1,1,1,0,NULL,3,'2026-01-20 14:17:17','2026-01-20 14:17:17'),
(17,'Drinks','Smoothie Bowl','Blended fruits with granola, coconut, and honey',1800.00,'MWK',NULL,1,0,1,0,NULL,4,'2026-01-20 14:17:17','2026-01-20 14:17:17'),
(18,'Bar','Signature Sunset Cocktail','Rum, passion fruit, lime, and mint - our house special',2500.00,'MWK',NULL,1,1,1,0,NULL,1,'2026-01-20 14:17:17','2026-01-20 14:17:17'),
(19,'Bar','Local Craft Beer Selection','Carlsberg, Kuche Kuche, or other Malawian brews',1200.00,'MWK',NULL,1,0,1,0,NULL,2,'2026-01-20 14:17:17','2026-01-20 14:17:17'),
(20,'Bar','Premium Wine List','Selection of red, white, and rosé wines from around the world',3500.00,'MWK',NULL,1,0,1,0,NULL,3,'2026-01-20 14:17:17','2026-01-20 14:17:17'),
(21,'Bar','Whiskey Collection','Single malt, blended, and bourbon options available',4000.00,'MWK',NULL,1,0,1,0,NULL,4,'2026-01-20 14:17:17','2026-01-20 14:17:17'),
(22,'Bar','Mojito Classic','White rum, fresh mint, lime, soda, and sugar',2200.00,'MWK',NULL,1,1,1,0,NULL,5,'2026-01-20 14:17:17','2026-01-20 14:17:17'),
(23,'Desserts','Chocolate Lava Cake','Warm chocolate cake with molten center, vanilla ice cream',2200.00,'MWK',NULL,1,1,1,0,NULL,1,'2026-01-20 14:17:17','2026-01-20 14:17:17'),
(24,'Desserts','Malawian Banana Fritters','Sweet fried bananas with honey and cinnamon',1500.00,'MWK',NULL,1,0,1,0,NULL,2,'2026-01-20 14:17:17','2026-01-20 14:17:17'),
(25,'Desserts','Cheesecake Selection','Classic, strawberry, or chocolate cheesecake',2000.00,'MWK',NULL,1,0,1,0,NULL,3,'2026-01-20 14:17:17','2026-01-20 14:17:17'),
(26,'Desserts','Fresh Fruit Platter','Seasonal tropical fruits with passion fruit coulis',1800.00,'MWK',NULL,1,1,1,0,NULL,4,'2026-01-20 14:17:17','2026-01-20 14:17:17'),
(27,'Breakfast','Continental Breakfast','Assorted pastries, fresh fruits, yogurt, cereals, and freshly brewed coffee',2500.00,'MWK',NULL,1,1,1,0,NULL,1,'2026-01-20 14:48:31','2026-01-20 14:48:31'),
(28,'Breakfast','Full English Breakfast','Eggs, bacon, sausages, grilled tomatoes, mushrooms, beans, and toast',3500.00,'MWK',NULL,1,0,0,0,NULL,2,'2026-01-20 14:48:31','2026-01-20 14:48:31'),
(29,'Breakfast','Malawian Breakfast Platter','Nsima with traditional relish, fresh mandasi, and masala tea',2800.00,'MWK',NULL,1,1,0,0,NULL,3,'2026-01-20 14:48:31','2026-01-20 14:48:31'),
(30,'Breakfast','Pancake Stack','Fluffy pancakes with maple syrup, fresh berries, and whipped cream',2200.00,'MWK',NULL,1,0,1,0,NULL,4,'2026-01-20 14:48:31','2026-01-20 14:48:31'),
(31,'Lunch','Chambo Fish & Chips','Fresh chambo from Lake Malawi, crispy chips, and tartar sauce',4500.00,'MWK',NULL,1,1,0,0,NULL,1,'2026-01-20 14:48:31','2026-01-20 14:48:31'),
(32,'Lunch','Grilled Chicken Caesar Salad','Tender chicken breast, crisp romaine, parmesan, croutons, and Caesar dressing',3800.00,'MWK',NULL,1,0,0,0,NULL,2,'2026-01-20 14:48:31','2026-01-20 14:48:31'),
(33,'Lunch','Vegetable Curry with Rice','Aromatic curry with seasonal vegetables, served with basmati rice',3200.00,'MWK',NULL,1,1,1,0,NULL,3,'2026-01-20 14:48:31','2026-01-20 14:48:31'),
(34,'Lunch','Club Sandwich Deluxe','Triple-decker with turkey, bacon, lettuce, tomato, and fries',3500.00,'MWK',NULL,1,0,0,0,NULL,4,'2026-01-20 14:48:31','2026-01-20 14:48:31'),
(35,'Dinner','Grilled T-Bone Steak','Premium aged beef, herb butter, roasted vegetables, and choice of sides',8500.00,'MWK',NULL,1,1,0,0,NULL,1,'2026-01-20 14:48:31','2026-01-20 14:48:31'),
(36,'Dinner','Pan-Seared Chambo','Lake Malawi chambo with lemon butter sauce, seasonal vegetables',6500.00,'MWK',NULL,1,1,0,0,NULL,2,'2026-01-20 14:48:31','2026-01-20 14:48:31'),
(37,'Dinner','Slow-Roasted Lamb Shank','Tender lamb with red wine jus, creamy mashed potatoes, and greens',7800.00,'MWK',NULL,1,0,0,0,NULL,3,'2026-01-20 14:48:31','2026-01-20 14:48:31'),
(38,'Dinner','Vegetarian Risotto','Creamy mushroom and truffle risotto with parmesan and fresh herbs',4500.00,'MWK',NULL,1,0,1,0,NULL,4,'2026-01-20 14:48:31','2026-01-20 14:48:31'),
(39,'Dinner','Seafood Platter','Grilled prawns, calamari, fish fillet, and mussels with garlic butter',9500.00,'MWK',NULL,1,1,0,0,NULL,5,'2026-01-20 14:48:31','2026-01-20 14:48:31'),
(40,'Drinks','Fresh Tropical Juice','Mango, pineapple, or passion fruit - freshly squeezed',800.00,'MWK',NULL,1,0,1,0,NULL,1,'2026-01-20 14:48:31','2026-01-20 14:48:31'),
(41,'Drinks','Iced Coffee','Chilled espresso with milk and vanilla syrup',1200.00,'MWK',NULL,1,0,1,0,NULL,2,'2026-01-20 14:48:31','2026-01-20 14:48:31'),
(42,'Drinks','Malawian Masala Tea','Spiced tea with ginger, cardamom, and cinnamon',600.00,'MWK',NULL,1,1,1,0,NULL,3,'2026-01-20 14:48:31','2026-01-20 14:48:31'),
(43,'Drinks','Smoothie Bowl','Blended fruits with granola, coconut, and honey',1800.00,'MWK',NULL,1,0,1,0,NULL,4,'2026-01-20 14:48:31','2026-01-20 14:48:31'),
(44,'Bar','Signature Sunset Cocktail','Rum, passion fruit, lime, and mint - our house special',2500.00,'MWK',NULL,1,1,1,0,NULL,1,'2026-01-20 14:48:31','2026-01-20 14:48:31'),
(45,'Bar','Local Craft Beer Selection','Carlsberg, Kuche Kuche, or other Malawian brews',1200.00,'MWK',NULL,1,0,1,0,NULL,2,'2026-01-20 14:48:31','2026-01-20 14:48:31'),
(46,'Bar','Premium Wine List','Selection of red, white, and rosé wines from around the world',3500.00,'MWK',NULL,1,0,1,0,NULL,3,'2026-01-20 14:48:31','2026-01-20 14:48:31'),
(47,'Bar','Whiskey Collection','Single malt, blended, and bourbon options available',4000.00,'MWK',NULL,1,0,1,0,NULL,4,'2026-01-20 14:48:31','2026-01-20 14:48:31'),
(48,'Bar','Mojito Classic','White rum, fresh mint, lime, soda, and sugar',2200.00,'MWK',NULL,1,1,1,0,NULL,5,'2026-01-20 14:48:31','2026-01-20 14:48:31'),
(49,'Desserts','Chocolate Lava Cake','Warm chocolate cake with molten center, vanilla ice cream',2200.00,'MWK',NULL,1,1,1,0,NULL,1,'2026-01-20 14:48:31','2026-01-20 14:48:31'),
(50,'Desserts','Malawian Banana Fritters','Sweet fried bananas with honey and cinnamon',1500.00,'MWK',NULL,1,0,1,0,NULL,2,'2026-01-20 14:48:31','2026-01-20 14:48:31'),
(51,'Desserts','Cheesecake Selection','Classic, strawberry, or chocolate cheesecake',2000.00,'MWK',NULL,1,0,1,0,NULL,3,'2026-01-20 14:48:31','2026-01-20 14:48:31'),
(52,'Desserts','Fresh Fruit Platter','Seasonal tropical fruits with passion fruit coulis',1800.00,'MWK',NULL,1,1,1,0,NULL,4,'2026-01-20 14:48:31','2026-01-20 14:48:31'),
(53,'Breakfast','Continental Breakfast','Assorted pastries, fresh fruits, yogurt, cereals, and freshly brewed coffee',2500.00,'MWK',NULL,1,1,1,0,NULL,1,'2026-01-20 15:22:41','2026-01-20 15:22:41'),
(54,'Breakfast','Full English Breakfast','Eggs, bacon, sausages, grilled tomatoes, mushrooms, beans, and toast',3500.00,'MWK',NULL,1,0,0,0,NULL,2,'2026-01-20 15:22:41','2026-01-20 15:22:41'),
(55,'Breakfast','Malawian Breakfast Platter','Nsima with traditional relish, fresh mandasi, and masala tea',2800.00,'MWK',NULL,1,1,0,0,NULL,3,'2026-01-20 15:22:41','2026-01-20 15:22:41'),
(56,'Breakfast','Pancake Stack','Fluffy pancakes with maple syrup, fresh berries, and whipped cream',2200.00,'MWK',NULL,1,0,1,0,NULL,4,'2026-01-20 15:22:41','2026-01-20 15:22:41'),
(57,'Lunch','Chambo Fish & Chips','Fresh chambo from Lake Malawi, crispy chips, and tartar sauce',4500.00,'MWK',NULL,1,1,0,0,NULL,1,'2026-01-20 15:22:41','2026-01-20 15:22:41'),
(58,'Lunch','Grilled Chicken Caesar Salad','Tender chicken breast, crisp romaine, parmesan, croutons, and Caesar dressing',3800.00,'MWK',NULL,1,0,0,0,NULL,2,'2026-01-20 15:22:41','2026-01-20 15:22:41'),
(59,'Lunch','Vegetable Curry with Rice','Aromatic curry with seasonal vegetables, served with basmati rice',3200.00,'MWK',NULL,1,1,1,0,NULL,3,'2026-01-20 15:22:41','2026-01-20 15:22:41'),
(60,'Lunch','Club Sandwich Deluxe','Triple-decker with turkey, bacon, lettuce, tomato, and fries',3500.00,'MWK',NULL,1,0,0,0,NULL,4,'2026-01-20 15:22:41','2026-01-20 15:22:41'),
(61,'Dinner','Grilled T-Bone Steak','Premium aged beef, herb butter, roasted vegetables, and choice of sides',8500.00,'MWK',NULL,1,1,0,0,NULL,1,'2026-01-20 15:22:41','2026-01-20 15:22:41'),
(62,'Dinner','Pan-Seared Chambo','Lake Malawi chambo with lemon butter sauce, seasonal vegetables',6500.00,'MWK',NULL,1,1,0,0,NULL,2,'2026-01-20 15:22:41','2026-01-20 15:22:41'),
(63,'Dinner','Slow-Roasted Lamb Shank','Tender lamb with red wine jus, creamy mashed potatoes, and greens',7800.00,'MWK',NULL,1,0,0,0,NULL,3,'2026-01-20 15:22:41','2026-01-20 15:22:41'),
(64,'Dinner','Vegetarian Risotto','Creamy mushroom and truffle risotto with parmesan and fresh herbs',4500.00,'MWK',NULL,1,0,1,0,NULL,4,'2026-01-20 15:22:41','2026-01-20 15:22:41'),
(65,'Dinner','Seafood Platter','Grilled prawns, calamari, fish fillet, and mussels with garlic butter',9500.00,'MWK',NULL,1,1,0,0,NULL,5,'2026-01-20 15:22:41','2026-01-20 15:22:41'),
(66,'Drinks','Fresh Tropical Juice','Mango, pineapple, or passion fruit - freshly squeezed',800.00,'MWK',NULL,1,0,1,0,NULL,1,'2026-01-20 15:22:41','2026-01-20 15:22:41'),
(67,'Drinks','Iced Coffee','Chilled espresso with milk and vanilla syrup',1200.00,'MWK',NULL,1,0,1,0,NULL,2,'2026-01-20 15:22:41','2026-01-20 15:22:41'),
(68,'Drinks','Malawian Masala Tea','Spiced tea with ginger, cardamom, and cinnamon',600.00,'MWK',NULL,1,1,1,0,NULL,3,'2026-01-20 15:22:41','2026-01-20 15:22:41'),
(69,'Drinks','Smoothie Bowl','Blended fruits with granola, coconut, and honey',1800.00,'MWK',NULL,1,0,1,0,NULL,4,'2026-01-20 15:22:41','2026-01-20 15:22:41'),
(70,'Bar','Signature Sunset Cocktail','Rum, passion fruit, lime, and mint - our house special',2500.00,'MWK',NULL,1,1,1,0,NULL,1,'2026-01-20 15:22:41','2026-01-20 15:22:41'),
(71,'Bar','Local Craft Beer Selection','Carlsberg, Kuche Kuche, or other Malawian brews',1200.00,'MWK',NULL,1,0,1,0,NULL,2,'2026-01-20 15:22:41','2026-01-20 15:22:41'),
(72,'Bar','Premium Wine List','Selection of red, white, and rosé wines from around the world',3500.00,'MWK',NULL,1,0,1,0,NULL,3,'2026-01-20 15:22:41','2026-01-20 15:22:41'),
(73,'Bar','Whiskey Collection','Single malt, blended, and bourbon options available',4000.00,'MWK',NULL,1,0,1,0,NULL,4,'2026-01-20 15:22:41','2026-01-20 15:22:41'),
(74,'Bar','Mojito Classic','White rum, fresh mint, lime, soda, and sugar',2200.00,'MWK',NULL,1,1,1,0,NULL,5,'2026-01-20 15:22:41','2026-01-20 15:22:41'),
(75,'Desserts','Chocolate Lava Cake','Warm chocolate cake with molten center, vanilla ice cream',2200.00,'MWK',NULL,1,1,1,0,NULL,1,'2026-01-20 15:22:41','2026-01-20 15:22:41'),
(76,'Desserts','Malawian Banana Fritters','Sweet fried bananas with honey and cinnamon',1500.00,'MWK',NULL,1,0,1,0,NULL,2,'2026-01-20 15:22:41','2026-01-20 15:22:41'),
(77,'Desserts','Cheesecake Selection','Classic, strawberry, or chocolate cheesecake',2000.00,'MWK',NULL,1,0,1,0,NULL,3,'2026-01-20 15:22:41','2026-01-20 15:22:41'),
(78,'Desserts','Fresh Fruit Platter','Seasonal tropical fruits with passion fruit coulis',1800.00,'MWK',NULL,1,1,1,0,NULL,4,'2026-01-20 15:22:41','2026-01-20 15:22:41'),
(79,'Breakfast','Continental Breakfast','Assorted pastries, fresh fruits, yogurt, cereals, and freshly brewed coffee',2500.00,'MWK',NULL,1,1,1,0,NULL,1,'2026-01-20 15:24:02','2026-01-20 15:24:02'),
(80,'Breakfast','Full English Breakfast','Eggs, bacon, sausages, grilled tomatoes, mushrooms, beans, and toast',3500.00,'MWK',NULL,1,0,0,0,NULL,2,'2026-01-20 15:24:02','2026-01-20 15:24:02'),
(81,'Breakfast','Malawian Breakfast Platter','Nsima with traditional relish, fresh mandasi, and masala tea',2800.00,'MWK',NULL,1,1,0,0,NULL,3,'2026-01-20 15:24:02','2026-01-20 15:24:02'),
(82,'Breakfast','Pancake Stack','Fluffy pancakes with maple syrup, fresh berries, and whipped cream',2200.00,'MWK',NULL,1,0,1,0,NULL,4,'2026-01-20 15:24:02','2026-01-20 15:24:02'),
(83,'Lunch','Chambo Fish & Chips','Fresh chambo from Lake Malawi, crispy chips, and tartar sauce',4500.00,'MWK',NULL,1,1,0,0,NULL,1,'2026-01-20 15:24:02','2026-01-20 15:24:02'),
(84,'Lunch','Grilled Chicken Caesar Salad','Tender chicken breast, crisp romaine, parmesan, croutons, and Caesar dressing',3800.00,'MWK',NULL,1,0,0,0,NULL,2,'2026-01-20 15:24:02','2026-01-20 15:24:02'),
(85,'Lunch','Vegetable Curry with Rice','Aromatic curry with seasonal vegetables, served with basmati rice',3200.00,'MWK',NULL,1,1,1,0,NULL,3,'2026-01-20 15:24:02','2026-01-20 15:24:02'),
(86,'Lunch','Club Sandwich Deluxe','Triple-decker with turkey, bacon, lettuce, tomato, and fries',3500.00,'MWK',NULL,1,0,0,0,NULL,4,'2026-01-20 15:24:02','2026-01-20 15:24:02'),
(87,'Dinner','Grilled T-Bone Steak','Premium aged beef, herb butter, roasted vegetables, and choice of sides',8500.00,'MWK',NULL,1,1,0,0,NULL,1,'2026-01-20 15:24:02','2026-01-20 15:24:02'),
(88,'Dinner','Pan-Seared Chambo','Lake Malawi chambo with lemon butter sauce, seasonal vegetables',6500.00,'MWK',NULL,1,1,0,0,NULL,2,'2026-01-20 15:24:02','2026-01-20 15:24:02'),
(89,'Dinner','Slow-Roasted Lamb Shank','Tender lamb with red wine jus, creamy mashed potatoes, and greens',7800.00,'MWK',NULL,1,0,0,0,NULL,3,'2026-01-20 15:24:02','2026-01-20 15:24:02'),
(90,'Dinner','Vegetarian Risotto','Creamy mushroom and truffle risotto with parmesan and fresh herbs',4500.00,'MWK',NULL,1,0,1,0,NULL,4,'2026-01-20 15:24:02','2026-01-20 15:24:02'),
(91,'Dinner','Seafood Platter','Grilled prawns, calamari, fish fillet, and mussels with garlic butter',9500.00,'MWK',NULL,1,1,0,0,NULL,5,'2026-01-20 15:24:02','2026-01-20 15:24:02'),
(92,'Drinks','Fresh Tropical Juice','Mango, pineapple, or passion fruit - freshly squeezed',800.00,'MWK',NULL,1,0,1,0,NULL,1,'2026-01-20 15:24:02','2026-01-20 15:24:02'),
(93,'Drinks','Iced Coffee','Chilled espresso with milk and vanilla syrup',1200.00,'MWK',NULL,1,0,1,0,NULL,2,'2026-01-20 15:24:02','2026-01-20 15:24:02'),
(94,'Drinks','Malawian Masala Tea','Spiced tea with ginger, cardamom, and cinnamon',600.00,'MWK',NULL,1,1,1,0,NULL,3,'2026-01-20 15:24:02','2026-01-20 15:24:02'),
(95,'Drinks','Smoothie Bowl','Blended fruits with granola, coconut, and honey',1800.00,'MWK',NULL,1,0,1,0,NULL,4,'2026-01-20 15:24:02','2026-01-20 15:24:02'),
(96,'Bar','Signature Sunset Cocktail','Rum, passion fruit, lime, and mint - our house special',2500.00,'MWK',NULL,1,1,1,0,NULL,1,'2026-01-20 15:24:02','2026-01-20 15:24:02'),
(97,'Bar','Local Craft Beer Selection','Carlsberg, Kuche Kuche, or other Malawian brews',1200.00,'MWK',NULL,1,0,1,0,NULL,2,'2026-01-20 15:24:02','2026-01-20 15:24:02'),
(98,'Bar','Premium Wine List','Selection of red, white, and rosé wines from around the world',3500.00,'MWK',NULL,1,0,1,0,NULL,3,'2026-01-20 15:24:02','2026-01-20 15:24:02'),
(99,'Bar','Whiskey Collection','Single malt, blended, and bourbon options available',4000.00,'MWK',NULL,1,0,1,0,NULL,4,'2026-01-20 15:24:02','2026-01-20 15:24:02'),
(100,'Bar','Mojito Classic','White rum, fresh mint, lime, soda, and sugar',2200.00,'MWK',NULL,1,1,1,0,NULL,5,'2026-01-20 15:24:02','2026-01-20 15:24:02'),
(101,'Desserts','Chocolate Lava Cake','Warm chocolate cake with molten center, vanilla ice cream',2200.00,'MWK',NULL,1,1,1,0,NULL,1,'2026-01-20 15:24:02','2026-01-20 15:24:02'),
(102,'Desserts','Malawian Banana Fritters','Sweet fried bananas with honey and cinnamon',1500.00,'MWK',NULL,1,0,1,0,NULL,2,'2026-01-20 15:24:02','2026-01-20 15:24:02'),
(103,'Desserts','Cheesecake Selection','Classic, strawberry, or chocolate cheesecake',2000.00,'MWK',NULL,1,0,1,0,NULL,3,'2026-01-20 15:24:02','2026-01-20 15:24:02'),
(104,'Desserts','Fresh Fruit Platter','Seasonal tropical fruits with passion fruit coulis',1800.00,'MWK',NULL,1,1,1,0,NULL,4,'2026-01-20 15:24:02','2026-01-20 15:24:02'),
(105,'Breakfast','Continental Breakfast','Assorted pastries, fresh fruits, yogurt, cereals, and freshly brewed coffee',2500.00,'MWK',NULL,1,1,1,0,NULL,1,'2026-01-20 15:26:43','2026-01-20 15:26:43'),
(106,'Breakfast','Full English Breakfast','Eggs, bacon, sausages, grilled tomatoes, mushrooms, beans, and toast',3500.00,'MWK',NULL,1,0,0,0,NULL,2,'2026-01-20 15:26:43','2026-01-20 15:26:43'),
(107,'Breakfast','Malawian Breakfast Platter','Nsima with traditional relish, fresh mandasi, and masala tea',2800.00,'MWK',NULL,1,1,0,0,NULL,3,'2026-01-20 15:26:43','2026-01-20 15:26:43'),
(108,'Breakfast','Pancake Stack','Fluffy pancakes with maple syrup, fresh berries, and whipped cream',2200.00,'MWK',NULL,1,0,1,0,NULL,4,'2026-01-20 15:26:43','2026-01-20 15:26:43'),
(109,'Lunch','Chambo Fish & Chips','Fresh chambo from Lake Malawi, crispy chips, and tartar sauce',4500.00,'MWK',NULL,1,1,0,0,NULL,1,'2026-01-20 15:26:43','2026-01-20 15:26:43'),
(110,'Lunch','Grilled Chicken Caesar Salad','Tender chicken breast, crisp romaine, parmesan, croutons, and Caesar dressing',3800.00,'MWK',NULL,1,0,0,0,NULL,2,'2026-01-20 15:26:43','2026-01-20 15:26:43'),
(111,'Lunch','Vegetable Curry with Rice','Aromatic curry with seasonal vegetables, served with basmati rice',3200.00,'MWK',NULL,1,1,1,0,NULL,3,'2026-01-20 15:26:43','2026-01-20 15:26:43'),
(112,'Lunch','Club Sandwich Deluxe','Triple-decker with turkey, bacon, lettuce, tomato, and fries',3500.00,'MWK',NULL,1,0,0,0,NULL,4,'2026-01-20 15:26:43','2026-01-20 15:26:43'),
(113,'Dinner','Grilled T-Bone Steak','Premium aged beef, herb butter, roasted vegetables, and choice of sides',8500.00,'MWK',NULL,1,1,0,0,NULL,1,'2026-01-20 15:26:43','2026-01-20 15:26:43'),
(114,'Dinner','Pan-Seared Chambo','Lake Malawi chambo with lemon butter sauce, seasonal vegetables',6500.00,'MWK',NULL,1,1,0,0,NULL,2,'2026-01-20 15:26:43','2026-01-20 15:26:43'),
(115,'Dinner','Slow-Roasted Lamb Shank','Tender lamb with red wine jus, creamy mashed potatoes, and greens',7800.00,'MWK',NULL,1,0,0,0,NULL,3,'2026-01-20 15:26:43','2026-01-20 15:26:43'),
(116,'Dinner','Vegetarian Risotto','Creamy mushroom and truffle risotto with parmesan and fresh herbs',4500.00,'MWK',NULL,1,0,1,0,NULL,4,'2026-01-20 15:26:43','2026-01-20 15:26:43'),
(117,'Dinner','Seafood Platter','Grilled prawns, calamari, fish fillet, and mussels with garlic butter',9500.00,'MWK',NULL,1,1,0,0,NULL,5,'2026-01-20 15:26:43','2026-01-20 15:26:43'),
(118,'Drinks','Fresh Tropical Juice','Mango, pineapple, or passion fruit - freshly squeezed',800.00,'MWK',NULL,1,0,1,0,NULL,1,'2026-01-20 15:26:43','2026-01-20 15:26:43'),
(119,'Drinks','Iced Coffee','Chilled espresso with milk and vanilla syrup',1200.00,'MWK',NULL,1,0,1,0,NULL,2,'2026-01-20 15:26:43','2026-01-20 15:26:43'),
(120,'Drinks','Malawian Masala Tea','Spiced tea with ginger, cardamom, and cinnamon',600.00,'MWK',NULL,1,1,1,0,NULL,3,'2026-01-20 15:26:43','2026-01-20 15:26:43'),
(121,'Drinks','Smoothie Bowl','Blended fruits with granola, coconut, and honey',1800.00,'MWK',NULL,1,0,1,0,NULL,4,'2026-01-20 15:26:43','2026-01-20 15:26:43'),
(122,'Bar','Signature Sunset Cocktail','Rum, passion fruit, lime, and mint - our house special',2500.00,'MWK',NULL,1,1,1,0,NULL,1,'2026-01-20 15:26:43','2026-01-20 15:26:43'),
(123,'Bar','Local Craft Beer Selection','Carlsberg, Kuche Kuche, or other Malawian brews',1200.00,'MWK',NULL,1,0,1,0,NULL,2,'2026-01-20 15:26:43','2026-01-20 15:26:43'),
(124,'Bar','Premium Wine List','Selection of red, white, and rosé wines from around the world',3500.00,'MWK',NULL,1,0,1,0,NULL,3,'2026-01-20 15:26:43','2026-01-20 15:26:43'),
(125,'Bar','Whiskey Collection','Single malt, blended, and bourbon options available',4000.00,'MWK',NULL,1,0,1,0,NULL,4,'2026-01-20 15:26:43','2026-01-20 15:26:43'),
(126,'Bar','Mojito Classic','White rum, fresh mint, lime, soda, and sugar',2200.00,'MWK',NULL,1,1,1,0,NULL,5,'2026-01-20 15:26:43','2026-01-20 15:26:43'),
(127,'Desserts','Chocolate Lava Cake','Warm chocolate cake with molten center, vanilla ice cream',2200.00,'MWK',NULL,1,1,1,0,NULL,1,'2026-01-20 15:26:43','2026-01-20 15:26:43'),
(128,'Desserts','Malawian Banana Fritters','Sweet fried bananas with honey and cinnamon',1500.00,'MWK',NULL,1,0,1,0,NULL,2,'2026-01-20 15:26:43','2026-01-20 15:26:43'),
(129,'Desserts','Cheesecake Selection','Classic, strawberry, or chocolate cheesecake',2000.00,'MWK',NULL,1,0,1,0,NULL,3,'2026-01-20 15:26:43','2026-01-20 15:26:43'),
(130,'Desserts','Fresh Fruit Platter','Seasonal tropical fruits with passion fruit coulis',1800.00,'MWK',NULL,1,1,1,0,NULL,4,'2026-01-20 15:26:43','2026-01-20 15:26:43');
/*!40000 ALTER TABLE `restaurant_menu` ENABLE KEYS */;
commit;

--
-- Table structure for table `rooms`
--

DROP TABLE IF EXISTS `rooms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `rooms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `short_description` varchar(255) DEFAULT NULL,
  `price_per_night` decimal(10,2) NOT NULL,
  `size_sqm` int(11) DEFAULT NULL,
  `max_guests` int(11) DEFAULT 2,
  `rooms_available` int(11) DEFAULT 5,
  `total_rooms` int(11) DEFAULT 5,
  `bed_type` varchar(50) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `badge` varchar(50) DEFAULT NULL,
  `amenities` text DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_rooms_featured` (`is_featured`,`is_active`),
  KEY `idx_rooms_price` (`price_per_night`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rooms`
--

/*!40000 ALTER TABLE `rooms` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `rooms` VALUES
(1,'Presidential Suite','presidential-suite','The epitome of luxury living with expansive space, private terrace, jacuzzi, and dedicated butler service. This stunning suite offers panoramic views and the ultimate in comfort and sophistication.','Ultimate luxury with private terrace and exclusive service',50000.00,100,4,5,5,'King Bed','images/rooms/room_1_1768949756.png','Luxury','King Bed,Private Terrace,Jacuzzi,Butler Service,Living Area,Dining Area,Full Kitchen,Smart TV,Premium WiFi,Climate Control',1,1,1,'2026-01-19 20:22:49','2026-01-20 22:55:52'),
(2,'Executive Suite','executive-suite','Designed for discerning business travelers, featuring separate work area, premium furnishings, and personalized butler service. Perfect blend of productivity and comfort.','Premium executive suite with work area and butler service',30050.00,60,3,5,5,'King Bed','images\\rooms\\Deluxe Room.jpg',NULL,'King Bed,Work Desk,Butler Service,Living Area,Smart TV,High-Speed WiFi,Coffee Machine,Mini Bar,Safe',1,1,2,'2026-01-19 20:22:49','2026-01-20 16:57:11'),
(3,'Family Suite','family-suite','Spacious two-bedroom suite perfect for families, featuring two king beds, dual bathrooms, and separate living area. Create lasting memories in ultimate comfort.','Spacious family accommodation with 2 bedrooms',30020.00,55,6,5,5,'2 King Beds','images\\rooms\\family_suite.jpg','Family','2 King Beds,2 Bathrooms,Living Area,Kitchenette,Smart TV,Kids Welcome,Free WiFi,Climate Control',1,1,3,'2026-01-19 20:22:49','2026-01-20 17:01:46'),
(4,'Deluxe Suite','deluxe-suite','Luxurious suite with marble bathroom featuring jacuzzi tub, separate living area, and premium bedding. Experience sophistication and indulgence.','Luxury suite with jacuzzi and separate living area',28000.00,45,2,5,5,'King Bed','https://source.unsplash.com/1600x900/?deluxe,suite,hotel,luxury,spa','Popular','King Bed,Jacuzzi Tub,Living Area,Marble Bathroom,Premium Bedding,Smart TV,Mini Bar,Free WiFi',1,1,4,'2026-01-19 20:22:49','2026-01-20 16:28:43'),
(5,'Superior Room','superior-room','Spacious room with premium furnishings, stunning views, and modern amenities. Enjoy comfort and elegance in every detail.','Spacious room with premium amenities and views',21000.00,35,2,5,5,'King Bed','https://source.unsplash.com/1600x900/?superior,hotel,room,view,interior',NULL,'King Bed,City View,Balcony,Smart TV,Free WiFi,Coffee Machine,Safe,Climate Control',0,1,5,'2026-01-19 20:22:49','2026-01-20 16:28:43'),
(6,'Standard Room','standard-room','Comfortable and well-appointed room with all essential amenities for a pleasant stay. Perfect for travelers seeking quality at exceptional value.','Comfortable room with essential amenities',15000.00,25,2,5,5,'Queen Bed','https://source.unsplash.com/1600x900/?standard,hotel,room,interior','Value','Queen Bed,Free WiFi,Smart TV,Daily Breakfast,Climate Control,Safe,Coffee Machine',0,1,6,'2026-01-19 20:22:49','2026-01-20 16:28:43');
/*!40000 ALTER TABLE `rooms` ENABLE KEYS */;
commit;

--
-- Table structure for table `site_settings`
--

DROP TABLE IF EXISTS `site_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `site_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text NOT NULL,
  `setting_group` varchar(50) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `site_settings`
--

/*!40000 ALTER TABLE `site_settings` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `site_settings` VALUES
(1,'site_name','Liwonde Sun Hotel','general','2026-01-19 20:22:49'),
(2,'site_tagline','Where Luxury Meets Nature','general','2026-01-19 20:22:49'),
(3,'hero_title','Experience Unparalleled Luxury','hero','2026-01-19 20:22:49'),
(4,'hero_subtitle','Discover the perfect blend of comfort, elegance, and exceptional service at Malawi\'s premier destination','hero','2026-01-19 20:22:49'),
(5,'phone_main','+265 123 456 785','contact','2026-01-20 07:43:44'),
(6,'phone_reservations','+265 987 654 321','contact','2026-01-19 20:22:49'),
(7,'email_main','info@liwondesunhotel.com','contact','2026-01-19 20:22:49'),
(8,'email_reservations','book@liwondesunhotel.com','contact','2026-01-19 20:22:49'),
(9,'address_line1','Liwonde National Park Road','contact','2026-01-19 20:22:49'),
(10,'address_line2','Liwonde, Southern Region','contact','2026-01-19 20:22:49'),
(11,'address_country','Malawi','contact','2026-01-19 20:22:49'),
(12,'facebook_url','https://facebook.com/liwondesunhotel','social','2026-01-19 20:22:49'),
(13,'instagram_url','https://instagram.com/liwondesunhotel','social','2026-01-19 20:22:49'),
(14,'twitter_url','https://twitter.com/liwondesunhotel','social','2026-01-19 20:22:49'),
(15,'linkedin_url','https://linkedin.com/company/liwondesunhotel','social','2026-01-19 20:22:49'),
(16,'working_hours','24/7 Available','contact','2026-01-19 20:22:49'),
(17,'copyright_text','2026 Liwonde Sun Hotel. All rights reserved.','general','2026-01-19 20:22:49'),
(18,'currency_symbol','MWK','general','2026-01-20 10:16:28'),
(19,'currency_code','MWK','general','2026-01-20 10:16:13'),
(20,'site_logo','images/logo/logo.jpg','general','2026-01-20 13:59:34');
/*!40000 ALTER TABLE `site_settings` ENABLE KEYS */;
commit;

--
-- Table structure for table `testimonials`
--

DROP TABLE IF EXISTS `testimonials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `testimonials` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `guest_name` varchar(100) NOT NULL,
  `guest_location` varchar(100) DEFAULT NULL,
  `rating` int(11) DEFAULT 5,
  `testimonial_text` text NOT NULL,
  `stay_date` date DEFAULT NULL,
  `guest_image` varchar(255) DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT 0,
  `is_approved` tinyint(1) DEFAULT 1,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_testimonials_featured` (`is_featured`,`is_approved`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `testimonials`
--

/*!40000 ALTER TABLE `testimonials` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `testimonials` VALUES
(1,'Sarah Johnson','London, UK',5,'Absolutely stunning hotel! The service was impeccable, rooms were luxurious, and the restaurant exceeded all expectations. Can\'t wait to return.','2025-12-15',NULL,1,1,1,'2026-01-19 20:22:49'),
(2,'Michael Chen','Singapore',5,'Best hotel experience in Africa. The attention to detail, the spa facilities, and the breathtaking views made our anniversary unforgettable.','2025-11-20',NULL,1,1,2,'2026-01-19 20:22:49'),
(3,'Emma Williams','New York, USA',5,'Five stars aren\'t enough! From check-in to check-out, everything was perfect. The staff went above and beyond to make our stay special.','2026-01-05',NULL,1,1,3,'2026-01-19 20:22:49');
/*!40000 ALTER TABLE `testimonials` ENABLE KEYS */;
commit;

--
-- Dumping routines for database 'u177308791_liwonde_sun'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2026-01-20 23:30:32
