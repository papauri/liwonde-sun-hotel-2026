<?php
/**
 * Check Live Database Categories
 */
require_once 'config/database.php';

echo "=== Checking Live Database Categories ===\n\n";

// Check menu_categories table
echo "1. Menu Categories Table (menu_categories):\n";
echo "-------------------------------------------\n";
try {
    $stmt = $pdo->query("SELECT * FROM menu_categories ORDER BY display_order ASC");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($categories)) {
        echo "  No categories found in menu_categories table\n";
    } else {
        foreach ($categories as $cat) {
            $status = $cat['is_active'] ? 'ACTIVE' : 'INACTIVE';
            echo "  - {$cat['name']} (Order: {$cat['display_order']}, Status: {$status})\n";
            if ($cat['description']) {
                echo "    Description: {$cat['description']}\n";
            }
        }
    }
} catch (PDOException $e) {
    echo "  ERROR: " . $e->getMessage() . "\n";
}

echo "\n2. Food Menu Table (food_menu categories):\n";
echo "--------------------------------------------\n";
try {
    $stmt = $pdo->query("SELECT DISTINCT category FROM food_menu ORDER BY category ASC");
    $food_categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "  Categories in food_menu:\n";
    foreach ($food_categories as $cat) {
        $catName = $cat['category'];
        
        // Count items in this category
        $countStmt = $pdo->prepare("SELECT COUNT(*) as count FROM food_menu WHERE category = ?");
        $countStmt->execute([$catName]);
        $count = $countStmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        echo "  - {$catName} ({$count} items)\n";
    }
} catch (PDOException $e) {
    echo "  ERROR: " . $e->getMessage() . "\n";
}

echo "\n3. Issue Analysis:\n";
echo "------------------\n";

// Check if menu_categories is_active affects food_menu display
echo "The issue you're experiencing:\n";
echo "- When you set is_active = 0 in menu_categories, items still show\n";
echo "- This is because the LEFT JOIN in restaurant.php doesn't filter by is_active\n";
echo "- The food_menu.category column stores the name directly, not a foreign key\n";
echo "\nConclusion: You're right - menu_categories table is redundant!\n";
echo "\nRecommendation: Simplify to use only food_menu table\n";

?>