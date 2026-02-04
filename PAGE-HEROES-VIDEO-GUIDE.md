# Page Heroes Video Functionality Guide

## Overview
Video support has been added to the `page_heroes` table, allowing you to display video backgrounds in hero sections across all pages (restaurant, conference, events, gym, rooms-gallery, etc.).

## Database Changes
Two new columns have been added to the `page_heroes` table:
- `hero_video_path` - Stores the video URL or file path
- `hero_video_type` - Stores the video MIME type or platform

## How It Works

### Automatic Video Detection
The hero component (`includes/hero.php`) automatically detects and displays videos:
1. If `hero_video_path` has a value → Display video
2. If `hero_video_path` is empty → Display `hero_image_path` instead

### Video Display Settings
Videos are displayed with these default settings:
- ✅ **Autoplay** - Video starts automatically
- ✅ **Muted** - Audio is muted (required for autoplay)
- ✅ **Loop** - Video repeats continuously
- ✅ **No Controls** - Clean appearance without player controls

## Supported Video Sources

### 1. Getty Images Videos (Recommended)
**Format:** Direct MP4 URL from Getty Images
```
https://media.gettyimages.com/id/[VIDEO_ID]/video/[VIDEO_NAME].mp4?s=mp4-640x640-gi&k=20&c=[TOKEN]
```
**Example:**
```
hero_video_path: https://media.gettyimages.com/id/1413260731/video/a-journalist-team-writing-or-working-on-a-story-together-at-a-media-company-in-a-boardroom.mp4?s=mp4-640x640-gi&k=20&c=EFu8OY0uCLKrtGIGCZ6tHZpUGXJa1gHimC4pf5Iim40=
hero_video_type: video/mp4
```

### 2. YouTube Videos
**Format:** YouTube watch URL or embed URL
```
https://www.youtube.com/watch?v=VIDEO_ID
```
**Example:**
```
hero_video_path: https://www.youtube.com/watch?v=dQw4w9WgXcQ
hero_video_type: youtube
```

### 3. Vimeo Videos
**Format:** Vimeo video URL
```
https://vimeo.com/VIDEO_ID
```
**Example:**
```
hero_video_path: https://vimeo.com/123456789
hero_video_type: vimeo
```

### 4. Direct Video Files
**Format:** Path to hosted video file
```
images/videos/restaurant-hero.mp4
https://yourdomain.com/videos/gym-hero.mp4
```
**Example:**
```
hero_video_path: images/videos/restaurant-hero.mp4
hero_video_type: video/mp4
```

## How to Add Videos

### Option 1: Via phpMyAdmin
1. Open phpMyAdmin
2. Navigate to `p601229_hotels` database
3. Click on `page_heroes` table
4. Find the page you want to update (e.g., restaurant, conference)
5. Edit the row
6. Enter video URL in `hero_video_path`
7. Enter video type in `hero_video_type`
8. Save

### Option 2: Via SQL Query
```sql
UPDATE page_heroes
SET 
    hero_video_path = 'https://media.gettyimages.com/id/1413260731/video/a-journalist-team-writing-or-working-on-a-story-together-at-a-media-company-in-a-boardroom.mp4?s=mp4-640x640-gi&k=20&c=EFu8OY0uCLKrtGIGCZ6tHZpUGXJa1gHimC4pf5Iim40=',
    hero_video_type = 'video/mp4'
WHERE page_slug = 'restaurant';
```

## Available Pages
You can add videos to these page heroes:

| Page Slug | Page URL | Description |
|-----------|----------|-------------|
| restaurant | /restaurant.php | Fine Dining Restaurant page |
| conference | /conference.php | Conference & Meetings page |
| events | /events.php | Events & Experiences page |
| gym | /gym.php | Gym & Fitness Center page |
| rooms-gallery | /rooms-gallery.php | Rooms & Suites listing |
| rooms-showcase | /rooms-showcase.php | Rooms showcase page |

## Removing Videos
To remove a video and revert to image display:
```sql
UPDATE page_heroes
SET 
    hero_video_path = NULL,
    hero_video_type = NULL
WHERE page_slug = 'restaurant';
```

## Technical Details

### Video Rendering
The video is rendered using the existing `renderVideoEmbed()` function from `includes/video-display.php`, which:
- Detects video platform (YouTube, Vimeo, Getty Images, or direct file)
- Generates appropriate embed code
- Applies responsive styling
- Handles video attributes (autoplay, muted, loop, etc.)

### Fallback Behavior
If no video is set, the hero section automatically falls back to displaying the `hero_image_path` image as the background.

### Styling
Videos are displayed with:
- Full-width coverage
- Object-fit: cover for proper scaling
- Absolute positioning within the hero container
- Z-index behind the hero content
- Overlay for text readability

## Example Use Cases

### Restaurant Page
```sql
UPDATE page_heroes
SET 
    hero_video_path = 'https://media.gettyimages.com/id/2076075171/video/abstract-defocused-background-of-restaurant.mp4?s=612x612&w=0&k=20&c=_KsEUAChBiOQDEMP6bumoJPoHkD5WTFmPBh1R1oeTz8=',
    hero_video_type = 'video/mp4'
WHERE page_slug = 'restaurant';
```

### Conference Page
```sql
UPDATE page_heroes
SET 
    hero_video_path = 'https://www.youtube.com/watch?v=3aTnsFOFq4w',
    hero_video_type = 'youtube'
WHERE page_slug = 'conference';
```

### Gym Page
```sql
UPDATE page_heroes
SET 
    hero_video_path = 'images/gym/gym-workout.mp4',
    hero_video_type = 'video/mp4'
WHERE page_slug = 'gym';
```

## Troubleshooting

### Video Not Displaying
1. Check that `hero_video_path` is not NULL
2. Verify the video URL is accessible
3. Check browser console for errors
4. Ensure video URL is HTTPS (required for mixed content security)

### Video Not Autoplaying
1. Ensure `hero_video_type` is set correctly
2. Check browser autoplay policies (some browsers block unmuted autoplay)
3. Videos are automatically muted to comply with browser policies

### Mobile Performance
Videos are optimized for mobile but consider:
- Video file size (recommend under 10MB)
- Using compressed formats (H.264 for MP4)
- Testing on actual mobile devices

## Migration Details
- **Migration Date:** 2026-02-05
- **Migration File:** Database/migrations/add_video_to_page_heroes.sql
- **Migration Script:** Database/migrations/execute_add_video_to_page_heroes.php

## Related Features
This implementation follows the same pattern used for:
- ✅ Home page hero slides (`hero_slides` table)
- ✅ Room detail pages (`rooms` table)
- ✅ Events page (`events` table)

All video functionality uses the shared `renderVideoEmbed()` helper function for consistency.