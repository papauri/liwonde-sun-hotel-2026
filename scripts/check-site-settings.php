<?php
require_once __DIR__ . '/../config/database.php';

try {
    $stmt = $pdo->query('DESCRIBE site_settings');
    $columns = $stmt->fetchAll();
    
    echo "site_settings table structure:\n";
    foreach ($columns as $col) {
        echo $col['Field'] . ' ' . $col['Type'] . "\n";
    }
    
    // Check if description column exists
    $has_description = false;
    foreach ($columns as $col) {
        if ($col['Field'] === 'description') {
            $has_description = true;
            break;
        }
    }
    
    echo "\nDescription column exists: " . ($has_description ? 'YES' : 'NO') . "\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>