<?php
require 'config/database.php';

echo "=== CHECKING ROOM 1 (Presidential Suite) ===\n";
$stmt = $pdo->query("SELECT id, name, slug FROM rooms WHERE id = 1");
$room = $stmt->fetch(PDO::FETCH_ASSOC);
echo "ID: {$room['id']}, Name: {$room['name']}, Slug: {$room['slug']}\n\n";

echo "=== GALLERY IMAGES FOR ROOM 1 ===\n";
$galleryStmt = $pdo->prepare("SELECT id, title, description, image_url FROM gallery WHERE room_id = ? AND is_active = 1 AND image_url IS NOT NULL AND image_url != '' ORDER BY display_order ASC, id ASC");
$galleryStmt->execute([1]);
$images = $galleryStmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($images)) {
    echo "No images found\n";
} else {
    foreach ($images as $img) {
        echo "ID: {$img['id']} | Title: {$img['title']} | URL: {$img['image_url']}\n";
    }
}

echo "\n=== FILTERED IMAGES (what room.php would see) ===\n";
$filtered = array_values(array_filter($images, function($img) {
    return !empty($img['image_url']) && trim($img['image_url']) !== '';
}));

foreach ($filtered as $img) {
    echo "ID: {$img['id']} | Title: {$img['title']} | URL: {$img['image_url']}\n";
}
