<?php
require_once 'config/database.php';

echo "=== Room-Related Tables ===\n\n";

$stmt = $pdo->query("SHOW TABLES LIKE '%room%'");
$tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

foreach ($tables as $table) {
    echo "$table\n";
}

echo "\n=== Checking room_pictures table ===\n";
try {
    $stmt = $pdo->query("SELECT id, room_id, image_path, video_path, video_type FROM room_pictures WHERE video_path IS NOT NULL LIMIT 5");
    $pictures = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($pictures)) {
        echo "No video entries found in room_pictures\n";
    } else {
        foreach ($pictures as $pic) {
            echo "\nID: {$pic['id']}, Room ID: {$pic['room_id']}\n";
            echo "  Image: {$pic['image_path']}\n";
            echo "  Video: " . ($pic['video_path'] ?: 'NULL') . "\n";
            echo "  Video Type: " . ($pic['video_type'] ?: 'NULL') . "\n";
        }
    }
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>