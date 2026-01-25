<?php
require_once 'config/database.php';

echo "<h1>Database Connection Test</h1>";
echo "<p>Connected to: " . DB_HOST . "</p>";
echo "<p>Database: " . DB_NAME . "</p>";

echo "<h2>Food Menu Test</h2>";
try {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM food_menu");
    $result = $stmt->fetch();
    echo "<p>Food menu items in database: " . $result['count'] . "</p>";
    
    $stmt = $pdo->query("SELECT * FROM food_menu LIMIT 5");
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre>";
    print_r($items);
    echo "</pre>";
} catch (PDOException $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
}

echo "<h2>Drink Menu Test</h2>";
try {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM drink_menu");
    $result = $stmt->fetch();
    echo "<p>Drink menu items in database: " . $result['count'] . "</p>";
    
    $stmt = $pdo->query("SELECT * FROM drink_menu LIMIT 5");
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre>";
    print_r($items);
    echo "</pre>";
} catch (PDOException $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
}

echo "<h2>Site Settings Test</h2>";
echo "<p>Site Name: " . getSetting('site_name') . "</p>";
echo "<p>Currency Symbol: " . getSetting('currency_symbol') . "</p>";
?>
