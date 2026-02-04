# Cache Management System - User Guide

## Overview
The enhanced Cache Management System provides easy control over your website's caching with toggle switches, bulk operations, and scheduled clearing.

## Features

### 1. Cache Statistics Dashboard
- **Total Cache Files**: Shows the number of cached items
- **Active Caches**: Caches that are still valid
- **Expired Caches**: Caches that have passed their expiry time
- **Total Size**: Disk space used by cache files

### 2. Global Cache Control
Master switch to enable or disable all caching at once.

**How to Use:**
1. Toggle the switch ON to enable all caching
2. Toggle OFF to disable all caching
3. Click "Save Setting"

### 3. Individual Cache Controls
Control specific cache types independently:

- **Hero Slides**: Page hero backgrounds and slides
- **Gallery Images**: Hotel and room gallery images
- **Rooms Data**: Room listings and details
- **Settings**: Site settings and configuration
- **API Responses**: External API call results

**How to Use:**
1. Find the cache type you want to control
2. Click "Enable" or "Disable" button
3. The status badge will update to show ON/OFF

### 4. Bulk Cache Clearing
Clear multiple cache types at once.

**How to Use:**
1. Check the boxes for cache types you want to clear
2. Click "Clear Selected Caches"
3. Confirm the action
4. Selected caches will be deleted

**Options:**
- Hero Slides
- Gallery Images
- Rooms Data
- Settings
- API Responses
- ALL CACHES (clears everything)

### 5. Scheduled Cache Clearing
Automatically clear cache at set intervals.

**How to Use:**
1. Enable the "Auto-Clear" switch
2. Select frequency:
   - Every Hour
   - Every 6 Hours
   - Every 12 Hours
   - Daily (default)
   - Weekly
3. Set the time to run (e.g., 03:00 for 3 AM)
4. Click "Save Schedule"

**Note:** Requires a cron job to be set up on your server.

## Setting Up Cron Jobs

### Option 1: Hourly Check (Recommended)
```bash
0 * * * * php /path/to/admin/scripts/scheduled-cache-clear.php
```

### Option 2: Daily Check
```bash
0 * * * * php /path/to/admin/scripts/scheduled-cache-clear.php
```

### Finding Your Path
Run this command to find your PHP path:
```bash
which php
```

Then add the full path to the script.

### Cron Job Location
- **cPanel**: Cron Jobs → Add New Cron Job
- **Plesk**: Scheduled Tasks → Add Task
- **Direct Server**: `crontab -e`

## Cache File List
View all current cache files with details:
- File Name
- Cache Key
- Size
- Created Date
- Expiry Date
- Status (Active/Expired)

## Best Practices

### When to Clear Cache
1. **After Content Updates**: Clear cache when you update:
   - Room information
   - Gallery images
   - Hero slides
   - Menu items
   - Events

2. **After Settings Changes**: Clear settings cache when you change:
   - Site settings
   - Booking settings
   - Payment settings

3. **Regular Maintenance**: Set up scheduled clearing:
   - Daily: For high-traffic sites
   - Weekly: For moderate-traffic sites
   - Monthly: For low-traffic sites

### When to Disable Caching
- During development/testing
- When troubleshooting issues
- When making frequent content updates

### When to Enable Caching
- In production environments
- After completing updates
- For optimal performance

## Troubleshooting

### Cache Not Clearing
1. Check file permissions on `/cache` directory
2. Ensure cache directory exists and is writable
3. Check if CACHE_ENABLED is true in `config/cache.php`

### Scheduled Clearing Not Working
1. Verify cron job is set up correctly
2. Check server error logs
3. Run script manually: `php scripts/scheduled-cache-clear.php`
4. Check logs at `/logs/cache-clear.log`

### Changes Not Showing
1. Clear the specific cache type
2. Wait for cache to expire (default 1 hour)
3. Disable caching temporarily to test

## Database Settings

Cache settings are stored in `site_settings` table:

| Setting Key | Description | Default |
|------------|-------------|---------|
| `cache_global_enabled` | Master on/off switch | 1 (enabled) |
| `cache_hero_enabled` | Hero slides cache | 1 (enabled) |
| `cache_gallery_enabled` | Gallery cache | 1 (enabled) |
| `cache_rooms_enabled` | Rooms cache | 1 (enabled) |
| `cache_settings_enabled` | Settings cache | 1 (enabled) |
| `cache_api_enabled` | API cache | 1 (enabled) |
| `cache_schedule_enabled` | Auto-clear on/off | 0 (disabled) |
| `cache_schedule_interval` | Frequency | daily |
| `cache_schedule_time` | Time to run | 00:00 |

## Technical Details

### Cache Directory
- **Location**: `/cache`
- **File Format**: `{key}_{hash}.cache`
- **Content**: JSON with data, expiry, and metadata

### Cache Functions
Located in `config/cache.php`:
- `getCache($key)` - Retrieve cached data
- `setCache($key, $value, $ttl)` - Store data in cache
- `deleteCache($key)` - Delete specific cache
- `clearCache()` - Delete all cache
- `clearCacheByPattern($pattern)` - Delete by pattern
- `listCache()` - Get all cache files
- `getCacheStats()` - Get cache statistics

### Default TTL
- Default cache lifetime: 3600 seconds (1 hour)
- Can be overridden per cache call

## Support

For issues or questions:
1. Check this guide first
2. Review logs in `/logs/cache-clear.log`
3. Contact system administrator

## Version History

### Version 2.0 (Current)
- Individual cache type toggles
- Bulk cache clearing
- Scheduled cache clearing
- Enhanced UI with statistics
- One-click enable/disable

### Version 1.0
- Basic cache clearing
- Manual cache management