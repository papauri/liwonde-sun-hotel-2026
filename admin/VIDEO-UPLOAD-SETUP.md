# Video Upload Support - Setup Guide

This guide will help you set up and use the video upload functionality across your admin panel.

## What's Included

✅ **Full video upload support** for events, rooms, heroes, and any other admin section  
✅ **Multiple video formats** supported (MP4, WebM, OGG, MOV, AVI, MKV)  
✅ **Automatic video processing** with validation and error handling  
✅ **Video preview** in admin panels before upload  
✅ **Frontend video display** helper functions  
✅ **File size management** (100MB default, configurable)  
✅ **Organized storage** in `/videos/` directory by category

## Quick Setup

### Step 1: Run Database Migration

Execute the SQL migration to add video columns to your database:

```bash
# Option 1: Import via command line
mysql -u your_username -p your_database < admin/add-video-support.sql

# Option 2: Copy and run in phpMyAdmin
# Open admin/add-video-support.sql and execute the statements
```

### Step 2: Create Videos Directory

The system will automatically create this directory when you upload your first video, but you can create it manually:

```bash
mkdir -p videos/events
mkdir -p videos/rooms
mkdir -p videos/hero
mkdir -p videos/general
```

### Step 3: Configure PHP Upload Settings

Make sure your PHP configuration supports video uploads. Edit your `php.ini`:

```ini
upload_max_filesize = 100M
post_max_size = 100M
memory_limit = 256M
max_execution_time = 300
```

### Step 4: File Permissions

Ensure the web server can write to the videos directory:

```bash
chmod 755 videos
chmod -R 755 videos/*
chown -R www-data:www-data videos  # For Apache
# or
chown -R nginx:nginx videos  # For Nginx
```

## Usage

### For Developers: Adding Video Upload to Admin Pages

1. **Include the video upload handler:**

```php
require_once 'video-upload-handler.php';
```

2. **Handle video uploads in your POST processing:**

```php
$videoUpload = uploadVideo($_FILES['video'] ?? null, 'events');
$videoPath = $videoUpload['path'] ?? null;
$videoType = $videoUpload['type'] ?? null;

// Save to database
$stmt = $pdo->prepare("UPDATE events SET video_path = ?, video_type = ? WHERE id = ?");
$stmt->execute([$videoPath, $videoType, $eventId]);
```

3. **Add video upload field to your form:**

```php
<div class="form-group">
    <label>Video (Optional)</label>
    <input type="file" name="video" accept="video/*">
</div>
```

### For Admin Users: Uploading Videos

1. Navigate to any admin section with video upload support (Events, Rooms, etc.)
2. Click "Add New" or edit an existing item
3. Find the "Video (Optional)" section
4. Click the upload area or drag and drop a video file
5. See the file size preview
6. Save your changes

### Frontend: Displaying Videos

Use the `renderVideoEmbed()` helper function:

```php
<?php
require_once 'admin/video-upload-handler.php';

// Display a video with default options
echo renderVideoEmbed($event['video_path'], $event['video_type']);

// Display with custom options
echo renderVideoEmbed($room['video_path'], $room['video_type'], [
    'autoplay' => true,
    'muted' => true,
    'loop' => true,
    'controls' => false,
    'class' => 'hero-video',
    'style' => 'width: 100%; max-height: 600px;'
]);
?>
```

## Features

### Supported Video Formats

- **MP4** (.mp4) - Best compatibility
- **WebM** (.webm) - Open format, excellent quality
- **OGG** (.ogv) - Open source format
- **MOV** (.mov) - Apple QuickTime format
- **AVI** (.avi) - Legacy format
- **MKV** (.mkv) - Modern container format

### File Size Management

- **Default max size:** 100MB per video
- **Configurable** per upload category
- **Automatic validation** with clear error messages
- **Human-readable file size display** (e.g., "15.2 MB")

### Video Storage

Videos are organized by category:

```
/videos/
  ├── events/
  │   ├── video_1234567890_1234.mp4
  │   └── video_1234567891_5678.webm
  ├── rooms/
  │   ├── video_1234567892_9012.mp4
  │   └── video_1234567893_3456.webm
  ├── hero/
  │   └── hero_video.mp4
  └── general/
      └── video_1234567894_7890.mp4
```

### Frontend Display Options

```php
// Video with controls (default)
echo renderVideoEmbed($videoPath, $videoType);

// Autoplay hero video (no controls, muted, loop)
echo renderVideoEmbed($heroVideo, $heroType, [
    'autoplay' => true,
    'muted' => true,
    'controls' => false,
    'loop' => true
]);

// Styled video
echo renderVideoEmbed($videoPath, $videoType, [
    'class' => 'my-custom-video',
    'style' => 'width: 100%; border-radius: 12px; box-shadow: 0 4px 16px rgba(0,0,0,0.1);'
]);
```

## API Reference

### uploadVideo()

Upload a video file with validation.

```php
uploadVideo($fileInput, $category = 'general', $maxSize = 104857600)

// Parameters:
// $fileInput - $_FILES array element (e.g., $_FILES['video'])
// $category - Subdirectory name (e.g., 'events', 'rooms')
// $maxSize - Maximum file size in bytes (default: 100MB)

// Returns:
// Array with 'path', 'type', 'size', 'original_name' on success
// null on failure
```

### deleteVideo()

Delete a video file from the server.

```php
deleteVideo($videoPath)

// Parameters:
// $videoPath - Relative path to video file

// Returns:
// true on success, false on failure
```

### getVideoInfo()

Get information about a video file.

```php
getVideoInfo($videoPath)

// Parameters:
// $videoPath - Relative path to video file

// Returns:
// Array with 'path', 'size', 'size_formatted', 'type', 'url'
// false on failure
```

### renderVideoEmbed()

Generate HTML for video player.

```php
renderVideoEmbed($videoPath, $videoType = null, $options = [])

// Parameters:
// $videoPath - Relative path to video file
// $videoType - MIME type (auto-detected if null)
// $options - Array of display options:
//   - autoplay (bool): Auto-play video
//   - muted (bool): Mute audio
//   - controls (bool): Show player controls
//   - loop (bool): Loop video
//   - class (string): CSS class name
//   - style (string): Inline CSS styles

// Returns:
// HTML string for video player
```

## Troubleshooting

### "Video upload failed: File size exceeds maximum"

**Solution:** Increase the upload size limit in your `php.ini` or adjust the `$maxSize` parameter in the `uploadVideo()` function.

### "Video upload failed: Invalid file type"

**Solution:** Ensure the video file is in one of the supported formats (MP4, WebM, OGG, MOV, AVI, MKV).

### Videos not showing on frontend

**Solution 1:** Check that the video file exists in the `/videos/` directory.  
**Solution 2:** Verify the `video_path` and `video_type` are saved in the database.  
**Solution 3:** Check browser console for video loading errors.  
**Solution 4:** Ensure proper file permissions on the `/videos/` directory.

### Permission denied errors

**Solution:** Set proper permissions:

```bash
chmod 755 videos
chmod -R 755 videos/*
```

## Best Practices

1. **Video Compression:** Compress videos before uploading for faster loading
2. **Format Selection:** Use MP4 for best browser compatibility
3. **File Size:** Keep videos under 50MB for optimal performance
4. **Autoplay:** Always mute autoplay videos (browser requirement)
5. **Fallback:** Provide an image fallback for videos
6. **Testing:** Test videos across different browsers and devices

## Examples

### Adding Video to Events Page (Complete)

```php
<?php
require_once 'admin-init.php';
require_once 'video-upload-handler.php';

// Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $videoUpload = uploadVideo($_FILES['video'] ?? null, 'events');
    $videoPath = $videoUpload['path'] ?? null;
    $videoType = $videoUpload['type'] ?? null;
    
    // Save to database
    $stmt = $pdo->prepare("INSERT INTO events (title, video_path, video_type) VALUES (?, ?, ?)");
    $stmt->execute([$_POST['title'], $videoPath, $videoType]);
}

// Display form
?>
<form method="POST" enctype="multipart/form-data">
    <input type="text" name="title" required>
    <input type="file" name="video" accept="video/*">
    <button type="submit">Save</button>
</form>
```

### Displaying Video on Frontend

```php
<?php
require_once 'admin/video-upload-handler.php';

// In your events loop
foreach ($events as $event) {
    // Display event image
    if ($event['image_path']) {
        echo '<img src="' . htmlspecialchars($event['image_path']) . '">';
    }
    
    // Display event video
    if ($event['video_path']) {
        echo renderVideoEmbed($event['video_path'], $event['video_type'], [
            'controls' => true,
            'style' => 'width: 100%; max-height: 400px;'
        ]);
    }
}
?>
```

## Support

For issues or questions:
1. Check this documentation
2. Review the `admin/video-upload-handler.php` source code
3. Check browser console for JavaScript errors
4. Review server error logs

## Future Enhancements

Potential improvements:
- [ ] Video thumbnail generation
- [ ] Video compression on upload
- [ ] Multiple video files per item
- [ ] Video gallery management
- [ ] YouTube/Vimeo embed support
- [ ] Video analytics
- [ ] Bandwidth optimization

---

**Version:** 1.0.0  
**Last Updated:** February 2026  
**Compatible With:** Liwonde Sun Hotel Admin Panel