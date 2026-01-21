<?php
require 'config/database.php';

try {
    echo "Adding columns to rooms table...\n";
    
    // Check if columns already exist
    $stmt = $pdo->query("SHOW COLUMNS FROM rooms LIKE 'rooms_available'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE rooms ADD COLUMN rooms_available INT DEFAULT 5 AFTER max_guests");
        echo "✓ Added rooms_available column\n";
    } else {
        echo "- rooms_available column already exists\n";
    }
    
    $stmt = $pdo->query("SHOW COLUMNS FROM rooms LIKE 'total_rooms'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE rooms ADD COLUMN total_rooms INT DEFAULT 5 AFTER rooms_available");
        echo "✓ Added total_rooms column\n";
    } else {
        echo "- total_rooms column already exists\n";
    }
    
    // Update existing rows
    $pdo->exec("UPDATE rooms SET rooms_available = 5 WHERE rooms_available IS NULL");
    $pdo->exec("UPDATE rooms SET total_rooms = 5 WHERE total_rooms IS NULL");
    echo "✓ Updated default values\n";
    
    echo "\n=== ROOMS TABLE STRUCTURE ===\n";
    $stmt = $pdo->query("SELECT id, name, rooms_available, total_rooms FROM rooms");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "Room: {$row['name']} | Available: {$row['rooms_available']}/{$row['total_rooms']}\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
