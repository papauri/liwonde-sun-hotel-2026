# Dynamic Page Hero System

## Overview
The Liwonde Sun Hotel website now features a database-driven hero section system that allows for dynamic content management without code changes.

## Database Table: `page_heroes`

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

## Helper Functions (config/database.php)

### `getCurrentPageHero()`
Automatically fetches hero data for the current page:
1. First tries exact match on `page_url` (using `SCRIPT_NAME`)
2. Falls back to `page_slug` derived from filename

### `getPageHero(string $page_slug)`
Fetches hero data by page slug identifier.

### `getPageHeroByUrl(string $page_url)`
Fetches hero data by exact page URL.

## Implementation Status

| Page | Status | File Path |
|------|--------|-----------|
| Restaurant | ✅ Dynamic | `pages/restaurant.php` |
| Conference | ✅ Dynamic | `conference.php` |
| Events | ✅ Dynamic | `events.php` |
| Rooms Showcase | ✅ Dynamic | `rooms-showcase.php` |
| Individual Room | ✅ Dynamic | `pages/room.php` |

## How to Use

### For PHP Pages

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

### Managing Hero Content in Database

#### Insert New Hero Entry

```sql
INSERT INTO `page_heroes` (
    `page_slug`, `page_url`, `hero_title`, `hero_subtitle`,
    `hero_description`, `hero_image_path`, `is_active`, `display_order`
) VALUES (
    'your-page', '/your-page.php', 'Your Title', 'Your Subtitle',
    'Your description text here', 'images/your-image.jpg', 1, 1
);
```

#### Update Existing Hero Entry

```sql
UPDATE `page_heroes`
SET 
    `hero_title` = 'New Title',
    `hero_subtitle` = 'New Subtitle',
    `hero_description` = 'New description',
    `hero_image_path` = 'images/new-image.jpg'
WHERE `page_slug` = 'your-page';
```

#### Toggle Active/Inactive

```sql
UPDATE `page_heroes`
SET `is_active` = 0
WHERE `page_slug` = 'your-page';
```

## Current Hero Data in Database

The following hero entries are currently configured:

| Page Slug | Page URL | Title | Subtitle |
|-----------|-----------|-------|----------|
| restaurant | /restaurant.php | Fine Dining Restaurant & Bars | Culinary Excellence |
| conference | /conference.php | Conference & Meeting Facilities | Business Excellence |
| events | /events.php | Events & Experiences | Celebrations & Gatherings |
| rooms-showcase | /rooms-showcase.php | Rooms & Suites | Riverfront Luxury |

## Benefits

1. **No Code Changes**: Update hero content directly in the database
2. **Easy Management**: Use phpMyAdmin or admin panel to make changes
3. **Consistent Fallbacks**: Default values if database entry is missing
4. **Flexible**: Each page can have unique hero content
5. **SEO-Friendly**: Dynamic meta tags can be updated alongside hero content

## Adding New Pages

To add hero functionality to a new page:

1. **Create Database Entry**:
   ```sql
   INSERT INTO `page_heroes` (
       `page_slug`, `page_url`, `hero_title`, `hero_subtitle`,
       `hero_description`, `hero_image_path`, `is_active`, `display_order`
   ) VALUES (
       'new-page', '/new-page.php', 'Page Title', 'Page Subtitle',
       'Page description', 'images/page-hero.jpg', 1, 5
   );
   ```

2. **Update PHP Page**:
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

## Troubleshooting

### Hero Not Showing

1. Check `is_active` is set to 1
2. Verify `page_slug` or `page_url` matches the PHP file
3. Check `hero_image_path` is correct and file exists
4. Review PHP error logs for database connection issues

### Wrong Hero Content Displayed

1. Verify `page_slug` is unique (no duplicates)
2. Check `display_order` if multiple active entries exist
3. Clear any caching that might be serving old content

## Future Enhancements

- Admin panel interface for hero management
- Version history for hero content changes
- A/B testing capabilities for different hero variants
- Multi-language support
- Scheduled hero content changes