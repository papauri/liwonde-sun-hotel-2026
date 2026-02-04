<?php
/**
 * Example: How to Display Videos on Frontend
 *
 * This file demonstrates how to display videos uploaded through the admin panel
 * on your public-facing website pages.
 */

// Include admin initialization for authentication
require_once 'admin-init.php';

// Include the video upload handler to access helper functions
require_once 'video-upload-handler.php';

// Example 1: Display single event video
function displayEventVideo($event) {
    if (!empty($event['video_path'])) {
        echo '<div class="event-video-wrapper">';
        echo '<h3>Event Video</h3>';
        
        // Display video with controls
        echo renderVideoEmbed($event['video_path'], $event['video_type'], [
            'controls' => true,
            'class' => 'event-video',
            'style' => 'width: 100%; max-width: 800px; border-radius: 12px;'
        ]);
        
        echo '</div>';
    }
}

// Example 2: Display hero section video (autoplay, muted, loop)
function displayHeroVideo($videoPath, $videoType) {
    if (!empty($videoPath)) {
        echo '<div class="hero-video-container">';
        
        // Autoplay video without controls for hero section
        echo renderVideoEmbed($videoPath, $videoType, [
            'autoplay' => true,
            'muted' => true,
            'loop' => true,
            'controls' => false,
            'class' => 'hero-background-video',
            'style' => 'position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; z-index: -1;'
        ]);
        
        echo '</div>';
    }
}

// Example 3: Display room video with poster image
function displayRoomVideo($room) {
    if (!empty($room['video_path'])) {
        echo '<div class="room-video-wrapper">';
        
        // Optional: Show thumbnail/image first
        if (!empty($room['image_path'])) {
            echo '<img src="' . htmlspecialchars($room['image_path']) . '" 
                     alt="' . htmlspecialchars($room['name']) . '" 
                     class="room-thumbnail">';
        }
        
        // Display video with controls
        echo renderVideoEmbed($room['video_path'], $room['video_type'], [
            'controls' => true,
            'class' => 'room-video',
            'style' => 'width: 100%; border-radius: 12px; box-shadow: 0 4px 16px rgba(0,0,0,0.1);'
        ]);
        
        echo '</div>';
    }
}

// Example 4: Display video gallery
function displayVideoGallery($videos) {
    if (!empty($videos)) {
        echo '<div class="video-gallery">';
        echo '<h2>Video Gallery</h2>';
        echo '<div class="gallery-grid">';
        
        foreach ($videos as $video) {
            echo '<div class="gallery-item">';
            echo '<h3>' . htmlspecialchars($video['title']) . '</h3>';
            
            if (!empty($video['video_path'])) {
                echo renderVideoEmbed($video['video_path'], $video['video_type'], [
                    'controls' => true,
                    'class' => 'gallery-video',
                    'style' => 'width: 100%; aspect-ratio: 16/9; border-radius: 8px;'
                ]);
            }
            
            echo '</div>';
        }
        
        echo '</div>';
        echo '</div>';
    }
}

// Example 5: Conditional display with fallback
function displayVideoWithFallback($videoPath, $videoType, $imagePath, $altText) {
    echo '<div class="media-container">';
    
    if (!empty($videoPath)) {
        // Display video if available
        echo renderVideoEmbed($videoPath, $videoType, [
            'controls' => true,
            'class' => 'primary-media'
        ]);
    } elseif (!empty($imagePath)) {
        // Fallback to image if no video
        echo '<img src="' . htmlspecialchars($imagePath) . '" 
                 alt="' . htmlspecialchars($altText) . '" 
                 class="primary-media">';
    } else {
        // No media available
        echo '<div class="no-media-placeholder">No media available</div>';
    }
    
    echo '</div>';
}

// Example 6: Full integration in events.php page
/*
Copy this into your events.php page where events are displayed:

<?php
// At the top of events.php
require_once 'admin/video-upload-handler.php';

// In your events display loop
foreach ($events as $event) {
    ?>
    <div class="event-card">
        <?php if (!empty($event['video_path'])): ?>
            <div class="event-media">
                <?php 
                echo renderVideoEmbed($event['video_path'], $event['video_type'], [
                    'controls' => true,
                    'style' => 'width: 100%; border-radius: 8px; max-height: 400px; object-fit: cover;'
                ]);
                ?>
            </div>
        <?php elseif (!empty($event['image_path'])): ?>
            <div class="event-media">
                <img src="<?php echo htmlspecialchars($event['image_path']); ?>" 
                     alt="<?php echo htmlspecialchars($event['title']); ?>">
            </div>
        <?php endif; ?>
        
        <h2><?php echo htmlspecialchars($event['title']); ?></h2>
        <p><?php echo htmlspecialchars($event['description']); ?></p>
    </div>
    <?php
}
?>
*/

// Example CSS for video displays (add to your CSS file)
/*
.event-video-wrapper {
    margin: 20px 0;
}

.event-video-wrapper h3 {
    margin-bottom: 12px;
    color: var(--navy);
}

.hero-video-container {
    position: relative;
    width: 100%;
    height: 600px;
    overflow: hidden;
}

.room-video-wrapper {
    margin: 24px 0;
}

.room-thumbnail {
    width: 100%;
    max-height: 400px;
    object-fit: cover;
    border-radius: 12px;
    margin-bottom: 16px;
}

.video-gallery {
    margin: 40px 0;
}

.gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 24px;
    margin-top: 20px;
}

.gallery-item {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.gallery-item h3 {
    padding: 16px;
    margin: 0;
    color: var(--navy);
}

.media-container {
    position: relative;
    width: 100%;
}

.primary-media {
    width: 100%;
    display: block;
}

.no-media-placeholder {
    padding: 40px;
    text-align: center;
    background: #f5f5f5;
    border-radius: 8px;
    color: #999;
}

// Responsive
@media (max-width: 768px) {
    .gallery-grid {
        grid-template-columns: 1fr;
    }
    
    .hero-video-container {
        height: 400px;
    }
}
*/

// Example 7: Checking video info before display
function displayVideoWithInfo($videoPath, $videoType) {
    $videoInfo = getVideoInfo($videoPath);
    
    if ($videoInfo) {
        echo '<div class="video-with-info">';
        echo '<div class="video-player">';
        echo renderVideoEmbed($videoPath, $videoType, [
            'controls' => true,
            'style' => 'width: 100%;'
        ]);
        echo '</div>';
        echo '<div class="video-meta">';
        echo '<small>File size: ' . htmlspecialchars($videoInfo['size_formatted']) . '</small>';
        echo '</div>';
        echo '</div>';
    }
}

// Example 8: Lazy loading videos (advanced)
function displayVideoLazy($videoPath, $videoType, $placeholderImage = '') {
    $uniqueId = 'video_' . uniqid();
    
    echo '<div class="video-lazy-container" id="' . $uniqueId . '">';
    
    if ($placeholderImage) {
        echo '<img src="' . htmlspecialchars($placeholderImage) . '" 
                 alt="Click to play video" 
                 class="video-placeholder"
                 onclick="loadVideo(\'' . $uniqueId . '\', \'' . htmlspecialchars($videoPath) . '\', \'' . htmlspecialchars($videoType) . '\')">';
        echo '<div class="play-button" onclick="loadVideo(\'' . $uniqueId . '\', \'' . htmlspecialchars($videoPath) . '\', \'' . htmlspecialchars($videoType) . '\')">
                  <i class="fas fa-play"></i>
              </div>';
    }
    
    echo '</div>';
    
    // JavaScript to load video on click
    echo '<script>
    function loadVideo(containerId, videoPath, videoType) {
        const container = document.getElementById(containerId);
        container.innerHTML = \'<video controls autoplay style="width: 100%; border-radius: 12px;"><source src="\' + videoPath + \'" type="\' + videoType + \'">Your browser does not support video.</video>\';
    }
    </script>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Display Examples</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div style="max-width: 1200px; margin: 40px auto; padding: 20px;">
        <h1>Video Display Examples</h1>
        
        <!-- Example 1: Basic Event Video -->
        <section style="margin: 40px 0;">
            <h2>Example 1: Event Video (with controls)</h2>
            <?php
            // Simulate event data
            $exampleEvent = [
                'title' => 'Business Conference',
                'video_path' => 'videos/events/example.mp4',
                'video_type' => 'video/mp4'
            ];
            displayEventVideo($exampleEvent);
            ?>
        </section>
        
        <!-- Example 2: Hero Video -->
        <section style="margin: 40px 0;">
            <h2>Example 2: Hero Video (autoplay, muted, loop)</h2>
            <div class="hero-video-container" style="position: relative; height: 400px; overflow: hidden; border-radius: 12px;">
                <?php
                // Simulate hero video
                $heroPath = 'videos/hero/hero_video.mp4';
                $heroType = 'video/mp4';
                
                if (file_exists(__DIR__ . '/../' . $heroPath)) {
                    displayHeroVideo($heroPath, $heroType);
                } else {
                    echo '<p style="padding: 20px; text-align: center; background: #f0f0f0;">Upload a hero video to see it autoplay here</p>';
                }
                ?>
            </div>
        </section>
        
        <!-- Example 3: Video with Fallback -->
        <section style="margin: 40px 0;">
            <h2>Example 3: Video with Image Fallback</h2>
            <?php
            $exampleMedia = [
                'video_path' => null, // Set to null to test fallback
                'video_type' => null,
                'image_path' => 'images/rooms/room_1_1768948896.png',
                'alt' => 'Room preview'
            ];
            displayVideoWithFallback(
                $exampleMedia['video_path'],
                $exampleMedia['video_type'],
                $exampleMedia['image_path'],
                $exampleMedia['alt']
            );
            ?>
        </section>
        
        <div style="background: #f8f9fa; padding: 24px; border-radius: 12px; margin-top: 40px;">
            <h3>How to Use These Examples</h3>
            <ol style="line-height: 1.8;">
                <li><strong>Include the video handler:</strong> <code>require_once 'admin/video-upload-handler.php';</code></li>
                <li><strong>Fetch video data from database:</strong> Include <code>video_path</code> and <code>video_type</code> columns</li>
                <li><strong>Use the helper function:</strong> <code>renderVideoEmbed($videoPath, $videoType, $options);</code></li>
                <li><strong>Customize options:</strong> autoplay, muted, controls, loop, class, style</li>
                <li><strong>Test thoroughly:</strong> Check across browsers and devices</li>
            </ol>
            
            <p style="margin-top: 16px;">
                <strong>Full Documentation:</strong> See <code>admin/VIDEO-UPLOAD-SETUP.md</code> for complete reference.
            </p>
        </div>
    </div>
</body>
</html>