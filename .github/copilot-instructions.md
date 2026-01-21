# Liwonde Sun Hotel - AI Coding Instructions

## Project Overview
Premium 5-star hotel website built with **PHP + MySQL** (PDO). Single-page architecture with database-driven content (hero carousel, rooms, facilities, testimonials). Focus on admin-editable dynamic content rather than hardcoded pages.

## Architecture & Data Flow

### Core Pattern: Settings + Entity Tables + PDO Queries
- **config/database.php**: Centralizes PDO connection, environment detection (local vs production), and helper functions
- **Database**: Site settings stored in `site_settings` table (key-value pairs) fetched via `getSetting('key')` helper
- **Data Tables**: `hero_slides`, `rooms`, `facilities`, `testimonials`, `gallery` - all with `is_active`, `display_order`, `created_at`, `updated_at` columns
- **index.php**: Main page fetches data via prepared PDO queries with fallback hardcoded content when DB unavailable

### Key Database Tables
```sql
site_settings    -- Key-value config (site_name, phone_main, social links, etc.)
hero_slides      -- Carousel data (is_active=1, ORDER BY display_order)
rooms            -- Room inventory (price_per_night, max_guests, is_featured, size_sqm)
facilities       -- Amenities (is_featured for homepage display)
testimonials     -- Guest reviews (is_approved before display)
gallery          -- Images (category-based filtering)
```

## Development Workflow

### Starting Local Server
```bash
./START-LOCAL-SERVER.bat  # PHP dev server on http://localhost:8000
```
Points to remote Hostinger DB by default. Override with env vars: `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`

### Database Setup
- Run `database.sql` (production schema with all tables)
- Run `database-local.sql` for local dev with sample data
- Run `add-hero-carousel-table.sql` to add/migrate hero_slides table
- Rename hero images to `slide1.jpg`, `slide2.jpg`, `slide3.jpg`, `slide4.jpg` in `images/hero/`

### Database Connection Logic
Environment detection in `config/database.php` (lines 8-11):
- Local: `localhost`, `127.0.0.1`, or hostname contains "localhost"
- Production: Everything else
- **Override priority**: OS env vars > isLocal check (allows testing remote DB locally)

## Code Patterns & Conventions

### Error Handling
- PDOException caught; errors logged, generic message shown in production
- DB query failures return empty arrays (not exceptions) - frontend shows nothing or fallback data
- Example: Hero carousel has inline fallback slides if `hero_slides` table missing

### Dynamic Content Retrieval
```php
$value = getSetting('hero_title', 'Default Value');  // Returns setting_value or default
$settings_array = getSettingsByGroup('contact');     // Returns array of all settings in group
```

### Data Rendering with Activation & Order
- Always query with `WHERE is_active = 1 ORDER BY display_order ASC`
- Frontend displays exactly DB order (no client-side re-sorting)
- Use `is_featured` boolean for homepage vs full listing pages

### Image Paths
- Stored in DB as relative paths: `images/gallery/hotel-exterior.jpg`
- Frontend renders as `<img src="<?php echo $image_path; ?>">`

## Integration Points

### Admin Panel Status
- **pages/** and **admin/** folders are empty (stub structure)
- Admin functionality not yet implemented - future development
- Settings currently editable only via direct database access (phpMyAdmin)

### External Services
- Hostinger hosting: `srv1789.hstgr.io` database server
- Hardcoded credentials in `config/database.php` (no .env file currently)

### Frontend Stack
- **CSS**: `css/style.css` (must include hover states, responsive breakpoints)
- **JS**: `js/main.js` (carousel controllers, form handlers)
- **Data**: `data/menu.json` for navigation menu (if applicable)

## When Adding Features

1. **New dynamic content**: Create table in `database.sql`, add migration SQL file
2. **New config values**: Add `INSERT` to `site_settings` in `database.sql`, retrieve via `getSetting()`
3. **Query failures**: Wrap in try-catch, return empty array, ensure HTML has graceful fallback
4. **Images**: Store paths in DB, use relative URLs, verify `images/` directory exists
5. **Admin forms**: Currently not implemented - hardcode or use direct DB queries for now
