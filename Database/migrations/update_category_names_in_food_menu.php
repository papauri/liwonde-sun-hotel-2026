<?php
/**
 * Update Category Names in food_menu Table
 * Changes category column from IDs to actual category names
 */

// Database connection
$host = 'promanaged-it.com';
$dbname = 'p601229_hotels';
$username = 'p601229_hotel_admin';
$password = '2:p2WpmX[0YTs7';

$pdo = null;

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Starting category name update...\n";
    echo "Connected to database successfully.\n\n";
    
    // Start transaction
    $pdo->beginTransaction();
    
    // Step 1: Get all categories with their IDs
    echo "Step 1: Fetching category mappings...\n";
    $stmt = $pdo->query("SELECT id, name FROM menu_categories ORDER BY id");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $categoryMap = [];
    foreach ($categories as $cat) {
        $categoryMap[$cat['id']] = $cat['name'];
        echo "  Category ID {$cat['id']} = {$cat['name']}\n";
    }
    
    // Step 2: Update each food_menu item with the category name
    echo "\nStep 2: Updating food_menu records...\n";
    
    $stmt = $pdo->query("SELECT id, category FROM food_menu");
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $updateStmt = $pdo->prepare("UPDATE food_menu SET category = ? WHERE id = ?");
    $updatedCount = 0;
    
    foreach ($items as $item) {
        $categoryId = $item['category'];
        $itemName = $item['id'];
        
        if (isset($categoryMap[$categoryId])) {
            $categoryName = $categoryMap[$categoryId];
            $updateStmt->execute([$categoryName, $itemName]);
            $updatedCount++;
            echo "  Updated item {$itemName}: category {$categoryId} → '{$categoryName}'\n";
        } else {
            echo "  WARNING: Item {$itemName} has unknown category ID {$categoryId}\n";
        }
    }
    
    // Commit transaction
    $pdo->commit();
    
    echo "\n========================================\n";
    echo "Update completed successfully!\n";
    echo "========================================\n";
    echo "- Updated {$updatedCount} menu items\n";
    echo "- Category column now contains names instead of IDs\n";
    echo "\nExample mappings:\n";
    foreach ($categoryMap as $id => $name) {
        echo "  {$id} → {$name}\n";
    }
    
} catch (PDOException $e) {
    if ($pdo !== null && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>