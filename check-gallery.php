<?php
require 'config/database.php';

echo "=== GALLERY TABLE ===\n";
try {
    $stmt = $pdo->query('SELECT id, room_id, title, image_url FROM gallery ORDER BY room_id, id');
    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($images)) {
        echo "Gallery table is EMPTY\n";
    } else {
        foreach ($images as $img) {
            echo "ID: {$img['id']} | Room: {$img['room_id']} | Title: {$img['title']} | URL: {$img['image_url']}\n";
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== ROOMS TABLE (featured images) ===\n";
try {
    $stmt = $pdo->query('SELECT id, name, image_url FROM rooms WHERE is_active = 1');
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($rooms as $room) {
        echo "ID: {$room['id']} | Name: {$room['name']} | Featured URL: {$room['image_url']}\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
