# Liwonde Sun Hotel Website

A modern, database-driven hotel website with dynamic content management, booking system, and admin panel.

## Project Overview

The Liwonde Sun Hotel website features a comprehensive booking system, dynamic hero sections, restaurant menu management, conference facilities, and an admin dashboard for managing all aspects of the hotel operations.

## Key Features

- **Dynamic Hero System**: Database-driven hero sections for each page
- **Room Booking System**: Online room reservations with availability checking
- **Restaurant & Bar Management**: Dynamic food and drink menus
- **Conference & Events**: Meeting facilities and event management
- **Admin Dashboard**: Complete backend management interface
- **Gallery System**: Image galleries for rooms and hotel facilities

## Dynamic Page Hero System

### Overview
The website features a database-driven hero section system that allows for dynamic content management without code changes.

### Database Table: `page_heroes`

```sql
CREATE TABLE `page_heroes` (
  `id` int NOT NULL,
  `page_slug` varchar(100) NOT NULL COMMENT 'Unique page identifier e.g., restaurant, conference',
  `page_url` varchar(255) NOT NULL COMMENT 'URL path e.g., /restaurant.php',
  `hero_title` varchar(200) NOT NULL,
  `hero_subtitle` varchar(200) DEFAULT NULL,
  `hero_description` text,
  `hero_image_path` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `display_order` int DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)
```

### Helper Functions (config/database.php)

- **`getCurrentPageHero()`**: Automatically fetches hero data for the current page
  - First tries exact match on `page_url` (using `SCRIPT_NAME`)
  - Falls back to `page_slug` derived from filename

- **`getPageHero(string $page_slug)`**: Fetches hero data by page slug identifier

- **`getPageHeroByUrl(string $page_url)`**: Fetches hero data by exact page URL

### Implementation Status

| Page | Status | File Path |
|------|--------|-----------|
| Restaurant | ✅ Dynamic | `restaurant.php` |
| Conference | ✅ Dynamic | `conference.php` |
| Events | ✅ Dynamic | `events.php` |
| Rooms Showcase | ✅ Dynamic | `rooms-showcase.php` |
| Individual Room | ✅ Dynamic | `room.php` |

### How to Use

#### For PHP Pages

```php
// Fetch page hero (DB-driven)
$pageHero = getCurrentPageHero();
$heroData = [
    'hero_title' => $pageHero['hero_title'] ?? 'Default Title',
    'hero_subtitle' => $pageHero['hero_subtitle'] ?? 'Default Subtitle',
    'hero_description' => $pageHero['hero_description'] ?? 'Default description',
    'hero_image_path' => $pageHero['hero_image_path'] ?? 'images/hero/slide1.jpg',
];

// In HTML
<section class="page-hero" style="background-image: url('<?php echo htmlspecialchars($heroData['hero_image_path']); ?>');">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <span class="hero-subtitle"><?php echo htmlspecialchars($heroData['hero_subtitle']); ?></span>
        <h1 class="hero-title"><?php echo htmlspecialchars($heroData['hero_title']); ?></h1>
        <p class="hero-description"><?php echo htmlspecialchars($heroData['hero_description']); ?></p>
    </div>
</section>
```

#### Managing Hero Content in Database

**Insert New Hero Entry:**
```sql
INSERT INTO `page_heroes` (
    `page_slug`, `page_url`, `hero_title`, `hero_subtitle`,
    `hero_description`, `hero_image_path`, `is_active`, `display_order`
) VALUES (
    'your-page', '/your-page.php', 'Your Title', 'Your Subtitle',
    'Your description text here', 'images/your-image.jpg', 1, 1
);
```

**Update Existing Hero Entry:**
```sql
UPDATE `page_heroes`
SET 
    `hero_title` = 'New Title',
    `hero_subtitle` = 'New Subtitle',
    `hero_description` = 'New description',
    `hero_image_path` = 'images/new-image.jpg'
WHERE `page_slug` = 'your-page';
```

**Toggle Active/Inactive:**
```sql
UPDATE `page_heroes`
SET `is_active` = 0
WHERE `page_slug` = 'your-page';
```

### Current Hero Data in Database

| Page Slug | Page URL | Title | Subtitle |
|-----------|-----------|-------|----------|
| restaurant | /restaurant.php | Fine Dining Restaurant & Bars | Culinary Excellence |
| conference | /conference.php | Conference & Meeting Facilities | Business Excellence |
| events | /events.php | Events & Experiences | Celebrations & Gatherings |
| rooms-showcase | /rooms-showcase.php | Rooms & Suites | Riverfront Luxury |

### Adding New Pages

To add hero functionality to a new page:

1. **Create Database Entry:**
   ```sql
   INSERT INTO `page_heroes` (
       `page_slug`, `page_url`, `hero_title`, `hero_subtitle`,
       `hero_description`, `hero_image_path`, `is_active`, `display_order`
   ) VALUES (
       'new-page', '/new-page.php', 'Page Title', 'Page Subtitle',
       'Page description', 'images/page-hero.jpg', 1, 5
   );
   ```

2. **Update PHP Page:**
   ```php
   // At the top of your PHP file, after database.php include
   $pageHero = getCurrentPageHero();
   $heroData = [
       'hero_title' => $pageHero['hero_title'] ?? 'Default Title',
       'hero_subtitle' => $pageHero['hero_subtitle'] ?? 'Default Subtitle',
       'hero_description' => $pageHero['hero_description'] ?? 'Default description',
       'hero_image_path' => $pageHero['hero_image_path'] ?? 'images/hero/slide1.jpg',
   ];
   
   // In your HTML hero section
   <!-- Use $heroData array variables -->
   ```

3. **Create Image**: Ensure the hero image exists in the specified path

### Troubleshooting

#### Hero Not Showing
1. Check `is_active` is set to 1
2. Verify `page_slug` or `page_url` matches the PHP file
3. Check `hero_image_path` is correct and file exists
4. Review PHP error logs for database connection issues

#### Wrong Hero Content Displayed
1. Verify `page_slug` is unique (no duplicates)
2. Check `display_order` if multiple active entries exist
3. Clear any caching that might be serving old content

## Image Guidelines

### Restaurant Images (`images/restaurant/`)
Upload the following images:
- **hero-bg.jpg** - Restaurant hero background (1920x1080px recommended)
- **dining-area-1.jpg** - Main dining area photo
- **dining-area-2.jpg** - Indoor seating area
- **bar-area.jpg** - Bar counter and seating
- **food-platter.jpg** - Featured dish or platter
- **fine-dining.jpg** - Fine dining experience photo
- **outdoor-terrace.jpg** - Outdoor dining area

### Gym/Wellness Images (`images/gym/`)
Upload the following images:
- **hero-bg.jpg** - Gym hero background (1920x1080px recommended)
- **fitness-center.jpg** - Overview of gym equipment and space
- **personal-training.jpg** - Personal trainer with client

### Logo (`images/logo/`)
Upload:
- **logo.png** or **logo.jpg** - Hotel logo (transparent PNG recommended, 200x80px approximately)

**Note:** All images should be optimized for web (compressed without quality loss) and follow naming conventions exactly as shown.

## Image Attributions

Room images in `images/rooms/` are downloaded from Unsplash via source.unsplash.com. They are free to use under the Unsplash License: https://unsplash.com/license

## Project Structure

```
liwonde-sun-hotel-2026/
├── admin/                    # Admin panel files
├── config/                   # Configuration files (database, email)
├── css/                      # Stylesheets
├── data/                     # JSON data files (menu.json)
├── Database/                 # SQL database files
├── images/                   # All image assets
│   ├── conference/
│   ├── events/
│   ├── gallery/
│   ├── gym/
│   ├── hero/
│   ├── hotel_gallery/
│   ├── logo/
│   ├── restaurant/
│   └── rooms/
├── includes/                 # Common includes (header, footer, etc.)
├── js/                       # JavaScript files
├── scripts/                  # Utility scripts
├── .clinerules/              # Project rules
├── .github/                  # GitHub workflows
├── booking.php               # Booking page
├── booking-confirmation.php  # Booking confirmation
├── check-availability.php    # Availability checker
├── conference.php            # Conference facilities page
├── events.php                # Events page
├── gym.php                   # Gym/fitness page
├── index.php                 # Homepage
├── restaurant.php            # Restaurant page
├── room.php                  # Individual room page
├── rooms-gallery.php         # Rooms gallery
└── rooms-showcase.php        # Rooms showcase
```

## Database Configuration

Database settings are configured in `config/database.php`:
- DB_HOST
- DB_PORT
- DB_NAME
- DB_USER
- DB_PASS

## Getting Started

1. Import the database schema from `Database/p601229_hotels.sql`
2. Configure database credentials in `config/database.php`
3. Configure email settings in `config/email.php`
4. Upload required images to the appropriate directories
5. Run the development server: `php -S localhost:8000`

## Admin Panel

Access the admin panel at `/admin/login.php` to manage:
- Bookings
- Rooms
- Menu items
- Events
- Conference facilities

## Benefits of Dynamic Hero System

1. **No Code Changes**: Update hero content directly in the database
2. **Easy Management**: Use phpMyAdmin or admin panel to make changes
3. **Consistent Fallbacks**: Default values if database entry is missing
4. **Flexible**: Each page can have unique hero content
5. **SEO-Friendly**: Dynamic meta tags can be updated alongside hero content

## Future Enhancements

- Admin panel interface for hero management
- Version history for hero content changes
- A/B testing capabilities for different hero variants
- Multi-language support
- Scheduled hero content changes
