<?php
require 'config/database.php';

echo "Cleaning up orphaned gallery images...\n";

try {
    // Delete images with NULL or empty room_id
    $deleted = $pdo->exec("DELETE FROM gallery WHERE room_id IS NULL OR room_id = 0 OR room_id = ''");
    echo "Deleted $deleted orphaned gallery images\n";
    
    // Show remaining images
    echo "\n=== REMAINING GALLERY IMAGES ===\n";
    $stmt = $pdo->query('SELECT id, room_id, title FROM gallery ORDER BY room_id, id');
    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($images)) {
        echo "Gallery table is now EMPTY\n";
    } else {
        foreach ($images as $img) {
            echo "ID: {$img['id']} | Room: {$img['room_id']} | Title: {$img['title']}\n";
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
