<?php
require_once 'config/database.php';

echo "=== page_heroes Table Structure ===\n";
$stmt = $pdo->query('DESCRIBE page_heroes');
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach($columns as $col) {
    echo $col['Field'] . ' - ' . $col['Type'] . "\n";
}

echo "\n=== Gym Page Hero Data ===\n";
$stmt = $pdo->prepare("SELECT * FROM page_heroes WHERE page_slug = 'gym' OR page_url LIKE '%gym.php%'");
$stmt->execute();
$hero = $stmt->fetch(PDO::FETCH_ASSOC);

if ($hero) {
    echo "Found hero data:\n";
    foreach($hero as $key => $value) {
        echo "  {$key}: {$value}\n";
    }
} else {
    echo "No hero found for gym page\n";
}