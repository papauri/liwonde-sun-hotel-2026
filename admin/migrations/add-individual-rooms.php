<?php
/**
 * Database Migration: Add Individual Rooms Support
 * Run this file once to set up the individual_rooms table and related changes
 */

// Get the project root directory
$rootDir = dirname(dirname(__DIR__));
require_once $rootDir . '/config/database.php';

echo "<h1>Individual Rooms Migration</h1>";
echo "<pre>";

// Use the global $pdo connection from database.php
global $pdo;

if (!$pdo) {
    die("Database connection not available");
}

try {
    
    // 1. Create individual_rooms table
    echo "Creating individual_rooms table...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS individual_rooms (
            id INT AUTO_INCREMENT PRIMARY KEY,
            room_type_id INT NOT NULL,
            room_number VARCHAR(50) NOT NULL,
            room_name VARCHAR(100),
            floor_number INT DEFAULT 1,
            status ENUM('available', 'occupied', 'maintenance', 'out_of_service') DEFAULT 'available',
            notes TEXT,
            features TEXT,
            is_active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY unique_room_number (room_number),
            FOREIGN KEY (room_type_id) REFERENCES rooms(id) ON DELETE CASCADE,
            INDEX idx_room_type (room_type_id),
            INDEX idx_status (status),
            INDEX idx_active (is_active)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✓ individual_rooms table created\n\n";
    
    // 2. Add individual_room_id column to bookings table if not exists
    echo "Adding individual_room_id to bookings table...\n";
    try {
        $pdo->exec("
            ALTER TABLE bookings 
            ADD COLUMN individual_room_id INT NULL AFTER room_id,
            ADD INDEX idx_individual_room (individual_room_id),
            ADD CONSTRAINT fk_bookings_individual_room 
                FOREIGN KEY (individual_room_id) REFERENCES individual_rooms(id) 
                ON DELETE SET NULL
        ");
        echo "✓ individual_room_id column added to bookings\n\n";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column') !== false) {
            echo "✓ individual_room_id column already exists\n\n";
        } else {
            throw $e;
        }
    }
    
    // 3. Create room_maintenance_log table
    echo "Creating room_maintenance_log table...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS room_maintenance_log (
            id INT AUTO_INCREMENT PRIMARY KEY,
            individual_room_id INT NOT NULL,
            status_from VARCHAR(50) NOT NULL,
            status_to VARCHAR(50) NOT NULL,
            reason TEXT,
            performed_by INT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (individual_room_id) REFERENCES individual_rooms(id) ON DELETE CASCADE,
            FOREIGN KEY (performed_by) REFERENCES users(id) ON DELETE SET NULL,
            INDEX idx_room (individual_room_id),
            INDEX idx_created (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✓ room_maintenance_log table created\n\n";
    
    // 4. Migrate existing rooms to individual_rooms if not already done
    echo "Migrating existing room types to individual_rooms...\n";
    
    // Get all room types
    $roomTypes = $pdo->query("SELECT id, name, total_rooms FROM rooms WHERE is_active = 1")->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($roomTypes as $roomType) {
        // Check if individual rooms already exist for this type
        $existingCount = $pdo->prepare("SELECT COUNT(*) FROM individual_rooms WHERE room_type_id = ?");
        $existingCount->execute([$roomType['id']]);
        $count = $existingCount->fetchColumn();
        
        if ($count == 0) {
            // Create individual rooms based on total_rooms
            $totalRooms = (int)($roomType['total_rooms'] ?? 1);
            
            for ($i = 1; $i <= $totalRooms; $i++) {
                $roomNumber = strtoupper(substr($roomType['name'], 0, 3)) . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
                $roomName = $roomType['name'] . ' Room ' . $i;
                
                $insert = $pdo->prepare("
                    INSERT INTO individual_rooms (room_type_id, room_number, room_name, status, is_active)
                    VALUES (?, ?, ?, 'available', 1)
                ");
                $insert->execute([$roomType['id'], $roomNumber, $roomName]);
            }
            echo "✓ Created {$totalRooms} individual rooms for {$roomType['name']}\n";
        } else {
            echo "✓ Individual rooms already exist for {$roomType['name']} ({$count} rooms)\n";
        }
    }
    
    echo "\n";
    
    // 5. Update individual room statuses based on current bookings
    echo "Updating individual room statuses based on active bookings...\n";
    
    $activeBookings = $pdo->query("
        SELECT DISTINCT individual_room_id 
        FROM bookings 
        WHERE status IN ('confirmed', 'checked-in') 
        AND individual_room_id IS NOT NULL
    ")->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($activeBookings as $roomId) {
        $pdo->prepare("UPDATE individual_rooms SET status = 'occupied' WHERE id = ?")->execute([$roomId]);
    }
    echo "✓ Updated " . count($activeBookings) . " rooms to occupied status\n\n";
    
    echo "<strong style='color: green;'>✓ Migration completed successfully!</strong>\n";
    echo "\nYou can now use the Individual Rooms management page at: admin/individual-rooms.php\n";
    
} catch (PDOException $e) {
    echo "<strong style='color: red;'>✗ Migration failed:</strong> " . $e->getMessage() . "\n";
    echo "Error Code: " . $e->getCode() . "\n";
}

echo "</pre>";
echo "<p><a href='../admin/individual-rooms.php'>Go to Individual Rooms Management</a></p>";
echo "<p><a href='../admin/dashboard.php'>Go to Admin Dashboard</a></p>";
?>