<?php
/**
 * Check Video Data in Database
 */
require_once 'config/database.php';

echo "=== Checking Video Data in Database ===\n\n";

// Check page_heroes table
echo "1. Page Heroes Table (page_heroes):\n";
echo "-------------------------------------\n";
try {
    $stmt = $pdo->query("SELECT page_slug, hero_title, hero_video_path, hero_video_type FROM page_heroes");
    $heroes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($heroes as $hero) {
        echo "\nPage: {$hero['page_slug']}\n";
        echo "  Title: {$hero['hero_title']}\n";
        echo "  Video Path: " . ($hero['hero_video_path'] ?: 'NULL') . "\n";
        echo "  Video Type: " . ($hero['hero_video_type'] ?: 'NULL') . "\n";
        if (!empty($hero['hero_video_path'])) {
            $hasVideo = !empty($hero['hero_video_path']);
            echo "  Has Video: " . ($hasVideo ? 'YES' : 'NO') . "\n";
        }
    }
} catch (PDOException $e) {
    echo "  ERROR: " . $e->getMessage() . "\n";
}

echo "\n2. Hotel Gallery Table (hotel_gallery):\n";
echo "----------------------------------------\n";
try {
    $stmt = $pdo->query("SELECT id, title, video_path, video_type FROM hotel_gallery WHERE video_path IS NOT NULL");
    $gallery = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($gallery)) {
        echo "  No video entries found in hotel_gallery\n";
    } else {
        foreach ($gallery as $item) {
            echo "\nID: {$item['id']}\n";
            echo "  Title: {$item['title']}\n";
            echo "  Video Path: " . ($item['video_path'] ?: 'NULL') . "\n";
            echo "  Video Type: " . ($item['video_type'] ?: 'NULL') . "\n";
        }
    }
} catch (PDOException $e) {
    echo "  ERROR: " . $e->getMessage() . "\n";
}

echo "\n3. Room Gallery Table (room_gallery):\n";
echo "--------------------------------------\n";
try {
    $stmt = $pdo->query("SELECT id, room_id, image_path, video_path, video_type FROM room_gallery WHERE video_path IS NOT NULL LIMIT 10");
    $roomGallery = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($roomGallery)) {
        echo "  No video entries found in room_gallery\n";
    } else {
        foreach ($roomGallery as $item) {
            echo "\nID: {$item['id']}, Room ID: {$item['room_id']}\n";
            echo "  Image Path: {$item['image_path']}\n";
            echo "  Video Path: " . ($item['video_path'] ?: 'NULL') . "\n";
            echo "  Video Type: " . ($item['video_type'] ?: 'NULL') . "\n";
        }
    }
} catch (PDOException $e) {
    echo "  ERROR: " . $e->getMessage() . "\n";
}

echo "\n4. Analysis:\n";
echo "-----------\n";
echo "The videos are not showing because:\n";
echo "- Check if video_path and video_type columns exist and have data\n";
echo "- Check if video URLs are valid\n";
echo "- Check if there are any CSP (Content Security Policy) issues\n";
echo "- Check if video files exist on the server (for local files)\n";

?>