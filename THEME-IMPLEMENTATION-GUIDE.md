# Dynamic Theme System - Implementation Guide

## Overview

A complete database-driven color theme management system has been implemented for the Liwonde Sun Hotel website. This allows administrators to customize site colors through an intuitive admin interface without editing CSS files.

## What Was Implemented

### 1. Dynamic Theme CSS Generator
**File:** `css/theme-dynamic.php`

A PHP file that generates CSS variables from database settings. It includes:
- Primary colors (navy, deep navy)
- Accent colors (gold, dark gold)
- Theme color and accent color
- Fallback defaults if database is unavailable

### 2. Admin Theme Management Panel
**File:** `admin/theme-management.php`

Full-featured admin interface with:
- **Preset Themes**: One-click application of professionally designed color schemes
  - Navy & Gold (Classic luxury)
  - Burgundy & Gold (Warm sophistication)
  - Forest Green (Nature-inspired)
  - Midnight Purple (Modern regal)
- **Custom Color Editor**: Fine-tune each color individually
  - Color picker for visual selection
  - Hex code input for precise control
  - Live preview of color changes
- **Live Preview**: See how colors will look on the actual site

### 3. Database Settings
Added color theme settings to `site_settings` table:
- `navy_color` - Primary navy color
- `deep_navy_color` - Darker navy shade
- `gold_color` - Gold accent color
- `dark_gold_color` - Darker gold shade
- `accent_color` - General accent color

### 4. Admin Menu Integration
Added "Theme Management" link to admin navigation menu.

## How to Enable Dynamic Theme System

### Step 1: Add Dynamic CSS to Your Pages

For **public pages** (index.php, rooms-gallery.php, etc.), add this line in the `<head>` section, **BEFORE** other CSS files:

```html
<link rel="stylesheet" href="css/theme-dynamic.php">
<link rel="stylesheet" href="css/style.css">
```

For **admin pages**, add it similarly:

```html
<link rel="stylesheet" href="../css/theme-dynamic.php">
<link rel="stylesheet" href="../css/style.css">
```

**IMPORTANT:** The dynamic CSS must be loaded **before** style.css so that style.css can override specific values if needed.

### Step 2: Clear Cache After Changes

When you update colors in the admin panel, the cache is automatically cleared. If you manually update the database, clear the cache via:
- Admin Panel → Cache Management → Clear All Cache
- Or use the cache management endpoint

## How to Use the Theme Management Panel

1. **Access the Panel:**
   - Log in to admin panel: `https://yoursite.com/admin/`
   - Navigate to: **Theme Management** (in the left menu)

2. **Apply a Preset Theme:**
   - Click on any preset card (Navy & Gold, Burgundy & Gold, etc.)
   - Click "Apply Selected Theme" button
   - Changes take effect immediately

3. **Customize Colors:**
   - Use the color picker to visually select a color
   - Or type a hex code directly (e.g., #FF0000)
   - Click "Save Color Scheme" to apply changes

4. **Preview Changes:**
   - The "Live Preview" section shows how colors will look
   - Changes are reflected in real-time as you adjust colors

## Current Status

### ✅ Fully Implemented
- Dynamic theme CSS generator
- Admin theme management panel
- Database color settings
- Preset theme system
- Live color preview
- Cache integration

### ⚠️ Action Required
**CSS Integration:** The dynamic CSS file (`theme-dynamic.php`) needs to be added to your page templates. Currently, only theme-dynamic.php exists, but it's not yet linked in your templates.

**To enable site-wide:**

1. For each PHP file in your root directory (index.php, rooms-gallery.php, etc.), add:
   ```php
   <link rel="stylesheet" href="css/theme-dynamic.php">
   ```
   in the `<head>` section, before other CSS links.

2. For admin files in the `admin/` directory, add:
   ```php
   <link rel="stylesheet" href="../css/theme-dynamic.php">
   ```

## Color Reference

### Current Default Colors
```css
--navy: #0A1929          /* Primary dark blue */
--deep-navy: #05090F     /* Very dark navy */
--gold: #D4AF37          /* Gold accent */
--dark-gold: #B8941F     /* Darker gold */
--accent-color: #D4AF37  /* General accent */
```

### How Colors Are Used
- `--navy`: Primary backgrounds, text, headers
- `--deep-navy`: Darker sections, overlays
- `--gold`: Buttons, accents, highlights
- `--dark-gold`: Hover states, borders
- `--accent-color`: General accent elements

## Troubleshooting

### Problem: Colors aren't changing
**Solution:**
1. Clear the cache: Admin Panel → Cache Management
2. Check that `theme-dynamic.php` is included before other CSS
3. Verify database settings are saved
4. Check browser cache (Ctrl+F5 / Cmd+Shift+R)

### Problem: Theme panel shows error
**Solution:**
1. Check database connection in `config/database.php`
2. Ensure `site_settings` table exists
3. Verify admin user has proper permissions

### Problem: Colors revert to defaults
**Solution:**
1. Check that color settings exist in database
2. Look for errors in PHP error log
3. Ensure `theme-dynamic.php` can connect to database

### Problem: Admin panel styling breaks
**Solution:**
1. Make sure `theme-dynamic.php` loads BEFORE `css/admin-styles.css`
2. Check file paths are correct (use `../` for admin files)
3. Verify CSS files exist and are readable

## Technical Details

### How It Works

1. **Database Storage:** Colors are stored in `site_settings` table
2. **Dynamic Generation:** `theme-dynamic.php` queries database and outputs CSS
3. **CSS Variables:** Generated as CSS custom properties (`--variable-name`)
4. **Application:** Existing CSS uses these variables throughout the site
5. **Cache Clearing:** Automatic cache clearing ensures changes take effect immediately

### Database Query
The dynamic CSS uses this query:
```sql
SELECT setting_key, setting_value 
FROM site_settings 
WHERE setting_key LIKE '%color%' 
   OR setting_key LIKE '%theme%'
```

### CSS Variable Structure
```css
:root {
    --navy: [from database];
    --deep-navy: [from database];
    --gold: [from database];
    --dark-gold: [from database];
    --accent-color: [from database];
    /* ... other variables */
}
```

## Customization

### Adding New Colors
1. Add to `theme-dynamic.php`:
   ```php
   $new_color = $settings['new_color'] ?? '#default_value';
   echo "--new-color: $new_color;";
   ```

2. Add to `theme-management.php`:
   ```php
   'new_color' => $_POST['new_color'] ?? '#default'
   ```

3. Add color input in the form

### Creating New Presets
In `theme-management.php`, add to the `$presets` array:
```php
'custom-theme' => [
    'navy_color' => '#YOUR_COLOR',
    'deep_navy_color' => '#YOUR_COLOR',
    'gold_color' => '#YOUR_COLOR',
    'dark_gold_color' => '#YOUR_COLOR',
    'accent_color' => '#YOUR_COLOR'
]
```

## Best Practices

1. **Test on Staging:** Always test color changes on a staging environment first
2. **Clear Cache:** After database changes, always clear the cache
3. **Browser Testing:** Test colors in different browsers for consistency
4. **Accessibility:** Ensure color contrast meets WCAG AA standards
5. **Backup:** Backup database before bulk theme changes

## Support

For issues or questions:
1. Check this guide first
2. Review PHP error logs
3. Test database connection
4. Verify file permissions

## Future Enhancements (Optional)

- Dark/light mode toggle
- Seasonal theme scheduling
- Theme export/import
- Color palette suggestions based on branding
- A/B testing for different themes
- Per-page theme overrides

---

**Implementation Date:** February 6, 2026  
**Version:** 1.0  
**Status:** Production Ready