<?php
/**
 * LIWONDE SUN HOTEL - DRINK MENU UPDATE SCRIPT
 * Date: February 2026
 * Description: Directly updates the drink_menu table with new February 2026 menu items
 * 
 * USAGE: Upload this file to your server and visit it in a browser
 * URL: https://promanaged-it.com/hotelsmw/scripts/update-drink-menu-live.php
 * 
 * SECURITY: Delete this file after successful update!
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set execution time limit for large database operations
set_time_limit(300);

// Include database configuration
require_once __DIR__ . '/../config/database.php';

// Start output buffering
ob_start();

echo "<!DOCTYPE html>
<html>
<head>
    <title>Drink Menu Update - Liwonde Sun Hotel</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1000px; margin: 50px auto; padding: 20px; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .warning { background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin: 10px 0; }
        h1 { color: #0A1929; }
        h2 { color: #333; border-bottom: 2px solid #0A1929; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #0A1929; color: white; }
        tr:hover { background: #f5f5f5; }
        .stats { display: flex; gap: 20px; flex-wrap: wrap; }
        .stat-card { background: #0A1929; color: white; padding: 20px; border-radius: 8px; flex: 1; min-width: 200px; }
        .stat-card h3 { margin: 0 0 10px 0; font-size: 14px; }
        .stat-card .number { font-size: 32px; font-weight: bold; }
    </style>
</head>
<body>
    <h1>🍹 Drink Menu Update - February 2026</h1>
";

try {
    // Verify database connection
    if (!isset($pdo) || !$pdo) {
        throw new Exception("Database connection not available. Please check database configuration.");
    }
    
    echo "<div class='info'>✅ Connected to database successfully</div>";
    
    // Begin transaction
    $pdo->beginTransaction();
    echo "<div class='info'>🔄 Transaction started</div>";
    
    // Step 1: Delete all existing drink menu items
    echo "<h2>Step 1: Removing Old Menu Items</h2>";
    $deleteStmt = $pdo->prepare("DELETE FROM drink_menu");
    $deleteStmt->execute();
    $deletedCount = $deleteStmt->rowCount();
    echo "<div class='warning'>🗑️ Deleted {$deletedCount} old menu items</div>";
    
    // Step 2: Insert new menu items
    echo "<h2>Step 2: Inserting New Menu Items</h2>";
    
    $menuItems = [
        // ============================================
        // COFFEE SHOP MENU (Non-Alcoholic)
        // ============================================
        ['Non-Alcoholic', 'Lime Cordial', 'Refreshing lime cordial', 1500.00, 0, 1],
        ['Non-Alcoholic', 'Coke/Fanta Can', 'Soft drink can', 6000.00, 0, 2],
        ['Non-Alcoholic', 'Bottled Water', 'Pure bottled water', 2000.00, 1, 3],
        ['Non-Alcoholic', 'Mineral Drinks', 'Premium mineral water', 2000.00, 0, 4],
        ['Non-Alcoholic', 'Ginger Ale', 'Classic ginger ale', 5500.00, 0, 5],
        ['Non-Alcoholic', 'Indian Tonic', 'Premium tonic water', 5500.00, 0, 6],
        ['Non-Alcoholic', 'Soda Water', 'Fresh soda water', 5500.00, 0, 7],
        ['Non-Alcoholic', 'Appletiser', 'Sparkling apple juice', 12000.00, 1, 8],
        ['Non-Alcoholic', 'Grapetiser', 'Sparkling grape juice', 12000.00, 1, 9],
        ['Non-Alcoholic', 'Fruitcana Juice', 'Fresh fruit juice', 5000.00, 0, 10],
        ['Non-Alcoholic', 'Enjoy 250ml', 'Premium juice drink', 2500.00, 0, 11],
        ['Non-Alcoholic', 'Enjoy 500ml', 'Premium juice drink', 6000.00, 0, 12],
        ['Non-Alcoholic', 'Dragon Energy Drink', 'Energy booster drink', 7000.00, 1, 13],
        ['Non-Alcoholic', 'Azam Ukwaju', 'Tamarind juice', 4000.00, 0, 14],
        ['Non-Alcoholic', 'Azam Ukwaju 500ml', 'Tamarind juice large', 5000.00, 0, 15],
        ['Non-Alcoholic', 'Ceres Juice Glass', 'Fresh juice by glass', 7000.00, 0, 16],
        ['Non-Alcoholic', 'Redbull', 'Energy drink', 8000.00, 1, 17],
        ['Non-Alcoholic', 'Embe Juice', 'Fresh fruit juice', 4000.00, 0, 18],
        ['Non-Alcoholic', 'Embe Juice 500ml', 'Fresh fruit juice large', 5000.00, 0, 19],
        ['Non-Alcoholic', 'Ceres Juice 250ml Bottle', 'Premium juice bottle', 8000.00, 0, 20],
        ['Non-Alcoholic', 'Ceres 200ml', 'Premium juice small', 4000.00, 0, 21],
        ['Non-Alcoholic', 'Fruitree', 'Fresh fruit drink', 8000.00, 0, 22],
        
        // COCKTAILS
        ['Cocktails', 'Rockshandy', 'Classic rockshandy cocktail', 7000.00, 0, 23],
        ['Cocktails', 'Chapman', 'Chapman cocktail blend', 6500.00, 1, 24],
        
        // COFFEE & HOT BEVERAGES (Coffee Shop)
        ['Coffee', 'Mzuzu Coffee', 'Premium Malawian coffee', 6000.00, 1, 25],
        ['Coffee', 'Espresso Single Shot', 'Rich single shot espresso', 3000.00, 0, 26],
        ['Coffee', 'Espresso Double Shot', 'Bold double shot espresso', 5000.00, 0, 27],
        ['Coffee', 'Cappuccino', 'Creamy cappuccino with foam', 8000.00, 1, 28],
        ['Coffee', 'Coffee Latte', 'Smooth latte with steamed milk', 8000.00, 1, 29],
        ['Coffee', 'Mocachinno', 'Chocolate coffee blend', 10000.00, 1, 30],
        ['Coffee', 'Cup of Ricoffy', 'Classic ricoffy coffee', 7500.00, 0, 31],
        ['Coffee', 'Hot Chocolate', 'Rich hot chocolate', 10000.00, 1, 32],
        ['Coffee', 'Cup of Cocoa', 'Pure cocoa beverage', 9000.00, 0, 33],
        ['Coffee', 'Cup of Malawi Tea', 'Traditional Malawian tea', 6500.00, 1, 34],
        ['Coffee', 'Rooibos Tea', 'Premium rooibos tea', 6000.00, 0, 35],
        
        // ICE CREAM CORNER & SHAKES
        ['Desserts', 'Ice Cream Cone', 'Creamy ice cream in cone', 4000.00, 0, 36],
        ['Desserts', 'Ice Cream Cup', 'Ice cream served in cup', 5000.00, 0, 37],
        ['Desserts', 'Milk Shakes', 'Thick creamy milkshake', 8500.00, 1, 38],
        ['Desserts', 'Smoothies', 'Fresh fruit smoothie', 8500.00, 1, 39],
        
        // ============================================
        // POOL BAR MENU (Alcoholic Beverages)
        // ============================================
        
        // WHISKY
        ['Whisky', 'Glenfiddich 15 Years', 'Premium 15-year-old single malt', 140000.00, 1, 40],
        ['Whisky', 'Glenfiddich 12 Years', 'Classic 12-year-old single malt', 9000.00, 1, 41],
        ['Whisky', 'Johnnie Walker Black Label', 'Premium blended Scotch whisky', 7000.00, 1, 42],
        ['Whisky', 'Johnnie Walker Red Label', 'Classic blended Scotch whisky', 4500.00, 0, 43],
        ['Whisky', 'Jameson Select Reserve', 'Premium Irish whiskey', 9000.00, 1, 44],
        ['Whisky', 'Jameson Triple Distilled', 'Smooth Irish whiskey', 5500.00, 0, 45],
        ['Whisky', 'J&B Whiskey', 'Classic blended Scotch whisky', 3600.00, 0, 46],
        ['Whisky', 'Grants', 'Smooth blended Scotch whisky', 4000.00, 0, 47],
        ['Whisky', 'Bells', 'Classic blended Scotch whisky', 5500.00, 0, 48],
        ['Whisky', 'Chivas Regal', 'Premium blended Scotch whisky', 6000.00, 1, 49],
        ['Whisky', 'Jack Daniels', 'Tennessee whiskey', 6500.00, 1, 50],
        ['Whisky', 'Best Whisky', 'Premium blended whisky', 3400.00, 0, 51],
        
        // BRANDY
        ['Brandy', 'Hennessey', 'Premium cognac', 9000.00, 1, 52],
        ['Brandy', 'KWV 10 Years', 'Aged 10-year brandy', 5500.00, 0, 53],
        ['Brandy', 'KWV 5 Years', 'Aged 5-year brandy', 3000.00, 0, 54],
        ['Brandy', 'KWV 3 Years', 'Aged 3-year brandy', 2500.00, 0, 55],
        ['Brandy', 'Klipdrift', 'Classic South African brandy', 2200.00, 0, 56],
        ['Brandy', 'Richelieu', 'Premium brandy', 2600.00, 0, 57],
        ['Brandy', 'Premier Brandy', 'Quality brandy', 3000.00, 0, 58],
        
        // GIN
        ['Gin', 'Malawi Gin', 'Local gin', 2500.00, 0, 59],
        ['Gin', 'Whitley Nerry Gin', 'Premium gin', 3000.00, 0, 60],
        ['Gin', 'Beefeater Gin', 'Classic London dry gin', 3000.00, 1, 61],
        ['Gin', 'Cruxland Gin', 'Premium gin', 2500.00, 0, 62],
        ['Gin', 'Have A Rock Dry Gin', 'Dry gin', 2500.00, 0, 63],
        ['Gin', 'Have A Rock Rose Gin', 'Rose gin', 2500.00, 0, 64],
        ['Gin', 'Stretton\'s Gin', 'Premium gin', 2000.00, 0, 65],
        
        // VODKA
        ['Vodka', 'Malawi Vodka', 'Local vodka', 2000.00, 0, 66],
        ['Vodka', '1818 S/Vodka', 'Premium vodka', 3200.00, 0, 67],
        ['Vodka', 'Ciroc Vodka', 'Premium French vodka', 2200.00, 1, 68],
        ['Vodka', 'Cruz Infusion Vodka', 'Infused vodka', 2200.00, 0, 69],
        ['Vodka', 'Cruz Vodka', 'Premium vodka', 2900.00, 0, 70],
        
        // RUM
        ['Rum', 'Malibu', 'Caribbean rum with coconut', 5000.00, 1, 71],
        ['Rum', 'Captain Morgan Rum', 'Classic spiced rum', 4000.00, 0, 72],
        ['Rum', 'Captain Morgan Spiced Gold', 'Premium spiced rum', 4000.00, 1, 73],
        
        // TEQUILA
        ['Tequila', 'Tequila Gold', 'Gold tequila', 5000.00, 0, 74],
        ['Tequila', 'Tequila Silver', 'Silver tequila', 5000.00, 0, 75],
        ['Tequila', 'Cactus Jack & Ponchos Tequila', 'Premium tequila', 5000.00, 0, 76],
        
        // LIQUEUR
        ['Liqueur', 'Zappa', 'Premium liqueur', 5000.00, 0, 77],
        ['Liqueur', 'Potency', 'Strong liqueur', 5000.00, 0, 78],
        ['Liqueur', 'Amarula Cream', 'Cream liqueur', 5000.00, 1, 79],
        ['Liqueur', 'Best Cream', 'Cream liqueur', 3500.00, 0, 80],
        ['Liqueur', 'Strawberry Lips', 'Strawberry liqueur', 5000.00, 0, 81],
        ['Liqueur', 'Kahlua', 'Coffee liqueur', 5000.00, 1, 82],
        ['Liqueur', 'Southern Comfort', 'American liqueur', 3200.00, 0, 83],
        ['Liqueur', 'Jagermeister', 'Herbal liqueur', 5000.00, 1, 84],
        ['Liqueur', 'Sour Monkey', 'Sour liqueur', 4000.00, 0, 85],
        
        // LOCAL BEERS
        ['Beer', 'Carlsberg Green', 'Premium Danish lager', 4000.00, 0, 86],
        ['Beer', 'Carlsberg Special', 'Special brew', 4000.00, 0, 87],
        ['Beer', 'Carlsberg Chill', 'Chilled lager', 5000.00, 0, 88],
        ['Beer', 'Castel Beer', 'Local beer', 4000.00, 0, 89],
        ['Beer', 'Kuche-Kuche', 'Malawian beer', 4000.00, 1, 90],
        ['Beer', 'Doppel', 'Premium beer', 4000.00, 0, 91],
        ['Beer', 'Pomme Breeze', 'Fruit beer', 4000.00, 0, 92],
        
        // IMPORTED BEERS & CIDERS
        ['Beer', 'Hunters Gold/Dry', 'South African cider', 10000.00, 1, 93],
        ['Beer', 'Savanna Dry', 'Premium cider', 10000.00, 1, 94],
        ['Beer', 'Smirnoff Guarana', 'Energy beer', 10000.00, 0, 95],
        ['Beer', 'Amstel Beers', 'Dutch lager', 10000.00, 0, 96],
        ['Beer', 'Windhoek Lager/Draft', 'Namibian beer', 10000.00, 1, 97],
        ['Beer', 'Breezer/Brutol', 'Premium beer mix', 10000.00, 0, 98],
        ['Beer', 'Flying Fish', 'Premium cider', 10000.00, 0, 99],
        ['Beer', 'Heineken Beer', 'Dutch lager', 10000.00, 1, 100],
        ['Beer', 'Budweiser Beer', 'American lager', 10000.00, 1, 101],
        ['Beer', '2M Beer', 'Imported beer', 10000.00, 0, 102],
        ['Beer', 'Cane Ciders & Beers', 'Premium ciders', 12000.00, 1, 103],
        
        // WINES
        ['Wine', 'All Cask Wines', 'House wines', 7500.00, 0, 104],
        ['Wine', 'Nederburg Red Wines', 'Premium South African red wine', 58000.00, 1, 105],
        ['Wine', 'Four Cousins Bottle', 'Sweet wine', 30000.00, 0, 106],
        ['Wine', 'Four Cousins 1.5L', 'Large format sweet wine', 40000.00, 0, 107],
        
        // TOBACCO
        ['Tobacco', 'Peter Stuyvesant', 'Premium cigarettes', 6000.00, 0, 108],
        ['Tobacco', 'Dunhill Blue', 'Premium cigarettes', 6000.00, 0, 109],
        ['Tobacco', 'Pall Mall Red', 'Classic cigarettes', 4500.00, 0, 110],
        ['Tobacco', 'Pall Mall Green', 'Classic cigarettes', 4500.00, 0, 111],
        
        // HOT BEVERAGES (Pool Bar)
        ['Coffee', 'Cappuccino (Pool Bar)', 'Fresh cappuccino', 5000.00, 0, 112],
        ['Coffee', 'Hot Chocolate (Pool Bar)', 'Rich hot chocolate', 7000.00, 0, 113],
        ['Coffee', 'Jacobs', 'Premium coffee', 5000.00, 0, 114],
        ['Coffee', 'Mzuzu Coffee (Pool Bar)', 'Malawian coffee', 5000.00, 0, 115],
        ['Coffee', 'Malawi Tea (Pool Bar)', 'Local tea', 2500.00, 0, 116],
    ];
    
    // Prepare INSERT statement
    $insertStmt = $pdo->prepare("
        INSERT INTO drink_menu (category, item_name, description, price, currency_code, is_available, is_featured, display_order)
        VALUES (?, ?, ?, ?, 'MWK', 1, ?, ?)
    ");
    
    // Insert all items
    $insertedCount = 0;
    $errors = [];
    
    foreach ($menuItems as $item) {
        try {
            $insertStmt->execute($item);
            $insertedCount++;
        } catch (PDOException $e) {
            $errors[] = "Error inserting {$item[1]}: " . $e->getMessage();
        }
    }
    
    echo "<div class='success'>✅ Inserted {$insertedCount} new menu items</div>";
    
    if (!empty($errors)) {
        echo "<div class='error'>⚠️ Some items had errors:</div>";
        foreach ($errors as $error) {
            echo "<div class='error'>- {$error}</div>";
        }
    }
    
    // Commit transaction
    $pdo->commit();
    echo "<div class='success'>🎉 Transaction committed successfully!</div>";
    
    // Display statistics
    echo "<h2>📊 Menu Statistics</h2>";
    $statsStmt = $pdo->query("
        SELECT category, COUNT(*) as item_count, MIN(price) as min_price, MAX(price) as max_price 
        FROM drink_menu 
        GROUP BY category 
        ORDER BY category
    ");
    
    echo "<div class='stats'>";
    while ($row = $statsStmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<div class='stat-card'>
            <h3>{$row['category']}</h3>
            <div class='number'>{$row['item_count']}</div>
            <div>Range: MK" . number_format($row['min_price']) . " - MK" . number_format($row['max_price']) . "</div>
        </div>";
    }
    echo "</div>";
    
    // Total count
    $totalStmt = $pdo->query("SELECT COUNT(*) as total FROM drink_menu");
    $total = $totalStmt->fetch(PDO::FETCH_ASSOC);
    echo "<div class='stat-card' style='background: #28a745;'>
        <h3>TOTAL ITEMS</h3>
        <div class='number'>{$total['total']}</div>
    </div>";
    
    // Show sample data
    echo "<h2>📋 Sample Menu Items</h2>";
    $sampleStmt = $pdo->query("SELECT category, item_name, price, is_featured FROM drink_menu ORDER BY display_order LIMIT 10");
    echo "<table>
        <tr>
            <th>Category</th>
            <th>Item Name</th>
            <th>Price</th>
            <th>Featured</th>
        </tr>";
    while ($row = $sampleStmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>
            <td>{$row['category']}</td>
            <td>{$row['item_name']}</td>
            <td>MK" . number_format($row['price'], 2) . "</td>
            <td>" . ($row['is_featured'] ? '⭐' : '-') . "</td>
        </tr>";
    }
    echo "</table>";
    
    echo "<div class='success'>
        <h2>✅ UPDATE COMPLETE!</h2>
        <p><strong>Database updated successfully!</strong></p>
        <p>Your February 2026 drink menu is now live with {$insertedCount} items.</p>
        <p><strong>⚠️ IMPORTANT:</strong> Please delete this file for security!</p>
        <p>File location: <code>/scripts/update-drink-menu-live.php</code></p>
    </div>";
    
} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "<div class='error'>
        <h2>❌ ERROR!</h2>
        <p><strong>{$e->getMessage()}</strong></p>
        <p>The transaction was rolled back. No changes were made to the database.</p>
    </div>";
}

echo "</body>
</html>";

// Flush output
ob_end_flush();
?>