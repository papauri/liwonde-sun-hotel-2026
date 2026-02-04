# Hotel Gallery Video Support

## Overview

The hotel gallery system now supports video content alongside images. This allows you to display Getty Images videos, YouTube videos, Vimeo videos, or locally hosted video files in the hotel gallery carousel.

## What's New

### Database Changes

**New columns added to `hotel_gallery` table:**
- `video_path` (varchar(500)) - Stores the video URL or file path
- `video_type` (varchar(50)) - Stores the video MIME type or platform identifier

These columns are optional - if no video is specified, the gallery will display the image as before.

### Code Changes

**Updated `config/database.php`:**
- `getCachedGalleryImages()` now includes `video_path` and `video_type` in the query
- Results are cached for 1 hour for performance

**Updated `includes/hotel-gallery.php`:**
- Includes the video display helper (`video-display.php`)
- Checks for video content before displaying images
- Automatically renders video embeds using the `renderVideoEmbed()` function
- Falls back to image display if no video is available

## Supported Video Sources

### 1. Getty Images Videos (Primary)
```
video_path: https://media.gettyimages.com/id/[VIDEO_ID]/video/[VIDEO_TITLE].mp4?s=mp4-640x640-gi&k=20&c=[HASH]
video_type: getty (or leave NULL for auto-detection)
```

**Example:**
```sql
UPDATE hotel_gallery 
SET video_path = 'https://media.gettyimages.com/id/1161129424/video/cheerful-entrepreneurs-shaking-hands-during-break.mp4?s=mp4-640x640-gi&k=20&c=8jeMCO1pMfOVYPDB8aSbOfRqvqyVWWjwfb0BK9xiF-w=',
    video_type = 'getty'
WHERE id = 1;
```

### 2. YouTube Videos
```
video_path: https://www.youtube.com/watch?v=[VIDEO_ID]
video_type: youtube (or leave NULL for auto-detection)
```

**Example:**
```sql
UPDATE hotel_gallery 
SET video_path = 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
    video_type = 'youtube'
WHERE id = 2;
```

### 3. Vimeo Videos
```
video_path: https://vimeo.com/[VIDEO_ID]
video_type: vimeo (or leave NULL for auto-detection)
```

**Example:**
```sql
UPDATE hotel_gallery 
SET video_path = 'https://vimeo.com/123456789',
    video_type = 'vimeo'
WHERE id = 3;
```

### 4. Local Video Files
```
video_path: videos/gallery/hotel-tour.mp4
video_type: video/mp4
```

**Example:**
```sql
UPDATE hotel_gallery 
SET video_path = 'videos/gallery/pool-area-tour.mp4',
    video_type = 'video/mp4'
WHERE id = 4;
```

## Video Display Settings

Videos in the hotel gallery are displayed with these default settings:
- **Autoplay:** Yes (videos play automatically)
- **Muted:** Yes (sound is muted to comply with browser autoplay policies)
- **Controls:** No (minimal UI for cleaner gallery appearance)
- **Loop:** Yes (videos repeat continuously)
- **Styling:** Full-coverage object-fit for professional appearance

These settings are optimized for background video effects in the gallery carousel.

## How to Add Videos to Gallery

### Method 1: Direct SQL Update

```sql
-- Add a Getty Images video to an existing gallery item
UPDATE hotel_gallery 
SET 
    video_path = 'https://media.gettyimages.com/id/[VIDEO_ID]/video/[TITLE].mp4?s=mp4-640x640-gi&k=20&c=[HASH]',
    video_type = 'getty'
WHERE id = [YOUR_GALLERY_ITEM_ID];
```

### Method 2: Insert New Gallery Item with Video

```sql
INSERT INTO hotel_gallery (
    title, 
    description, 
    image_url, 
    video_path, 
    video_type, 
    category, 
    is_active, 
    display_order
) VALUES (
    'Hotel Pool Video Tour',
    'Beautiful aerial view of our Olympic-sized swimming pool',
    'images/hotel_gallery/pool-fallback.jpg',  -- Fallback image
    'https://media.gettyimages.com/id/[VIDEO_ID]/video/pool-tour.mp4?s=mp4-640x640-gi&k=20&c=[HASH]',
    'getty',
    'facilities',
    1,
    10
);
```

### Method 3: Via Admin Panel (If Available)

If your admin panel has gallery management, you can add videos through the UI by:
1. Navigate to Gallery Management
2. Edit or create a gallery item
3. Enter the video URL in the "Video Path" field
4. Optionally specify the video type
5. Save the changes

## Testing Video Display

After adding videos to the database:

1. **Clear the cache** to refresh the gallery data:
   ```bash
   php -r "require_once 'config/cache.php'; clearCache('gallery_images');"
   ```

2. **Visit any page** that includes the hotel gallery (e.g., homepage, gallery page)

3. **Verify video playback:**
   - Video should autoplay in the carousel
   - Video should loop continuously
   - Video should be muted (sound not required)
   - Navigation controls should still work

## Troubleshooting

### Video Not Showing

**Problem:** Gallery displays image instead of video

**Solutions:**
1. Clear the cache: `clearCache('gallery_images')`
2. Verify video_path is set in database
3. Check browser console for CSP errors
4. Ensure video URL is accessible

**Check Database:**
```sql
SELECT id, title, video_path, video_type 
FROM hotel_gallery 
WHERE video_path IS NOT NULL;
```

### Video Not Playing

**Problem:** Video shows but doesn't autoplay

**Solutions:**
1. Check browser console for autoplay policy errors
2. Verify video_url is valid and accessible
3. Try different browser (Safari has strict autoplay policies)
4. Ensure video format is supported by browser

### Video Aspect Ratio Issues

**Problem:** Video appears stretched or cropped

**Solution:** Videos use `object-fit: cover` for full coverage. This is intentional for background video effects. If you need different behavior, modify the CSS in `includes/hotel-gallery.php`.

## Security Considerations

### Content Security Policy (CSP)

Your CSP has been updated to allow video embeds from:
- **Getty Images** (*.gettyimages.com)
- **YouTube** (*.youtube.com, *.googlevideo.com)
- **Vimeo** (*.vimeo.com, *.vimeocdn.com)
- **Dailymotion** (*.dailymotion.com)

If you use other video sources, update the CSP in `includes/header.php`:

```php
// Add to img-src and frame-src
frame-src 'self' *.gettyimages.com *.youtube.com *.vimeo.com *.dailymotion.com;
```

## Performance Optimization

### Caching

Gallery data is cached for 1 hour to reduce database queries. After adding videos:
- Clear the cache manually (see above)
- Or wait for cache to expire naturally (1 hour)

### Video Loading

Videos are loaded on-demand as users navigate the carousel. Only the visible video is loaded at a time, improving page load performance.

## Migration Summary

### Database Migration

**Migration Script:** `Database/migrations/execute_add_video_to_hotel_gallery.php`

**Changes Made:**
1. Added `video_path` column to `hotel_gallery` table
2. Added `video_type` column to `hotel_gallery` table
3. Updated `getCachedGalleryImages()` to include video columns
4. Updated `hotel-gallery.php` to render videos

**Status:** ✅ Complete

### Code Changes

**Files Modified:**
- `config/database.php` - Updated getCachedGalleryImages() query
- `includes/hotel-gallery.php` - Added video rendering logic
- `includes/video-display.php` - Already existed, reused for gallery videos

**Files Created:**
- `Database/migrations/add_video_to_hotel_gallery.sql`
- `Database/migrations/execute_add_video_to_hotel_gallery.php`
- `HOTEL-GALLERY-VIDEO-SUPPORT.md` (this file)

## Best Practices

1. **Always provide a fallback image** - Set `image_url` even when using video, as fallback for older browsers or slow connections

2. **Use Getty Images videos** - They're reliable, high-quality, and properly licensed

3. **Keep videos short** - Gallery videos should be 10-30 seconds for optimal loading and user experience

4. **Test on mobile** - Videos consume more data than images, ensure mobile experience is good

5. **Consider bandwidth** - Not all users have fast connections, videos should enhance not hinder the experience

## Future Enhancements

Potential improvements to the gallery video system:
- [ ] Add video thumbnail generation
- [ ] Support for multiple video formats with fallbacks
- [ ] Video compression optimization
- [ ] Admin panel UI for video management
- [ ] Lazy loading for off-screen gallery videos
- [ ] Video quality options based on connection speed

## Support

For issues or questions:
1. Check this documentation first
2. Review browser console for errors
3. Verify database data is correct
4. Clear cache and retry
5. Check CSP headers if videos are blocked

## Summary

The hotel gallery now supports videos alongside images, providing a more dynamic and engaging visual experience for your guests. The system is backward compatible - existing image-only gallery items continue to work as before, while new items can include video content.

**Migration Status:** ✅ Complete and ready to use
**Cache Status:** Cleared and ready
**Testing Required:** Add videos to gallery items and verify display