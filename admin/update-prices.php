<?php
// Liwonde Sun Hotel - Price Update Script
// This script updates all room prices across the website

// Define the new room prices
$newPrices = [
    'standard' => 120,
    'deluxe' => 180,
    'executive_suite' => 280,
    'family_suite' => 220
];

echo "Liwonde Sun Hotel - Price Update Script\n";
echo "========================================\n\n";

// Update the pricing configuration file
$configFile = '../includes/pricing-config.php';
if (file_exists($configFile)) {
    $configContent = file_get_contents($configFile);
    
    // Update prices in the configuration
    $configContent = preg_replace("/('standard'.*?)'price' => \d+/", "$1'price' => " . $newPrices['standard'], $configContent);
    $configContent = preg_replace("/('deluxe'.*?)'price' => \d+/", "$1'price' => " . $newPrices['deluxe'], $configContent);
    $configContent = preg_replace("/('executive_suite'.*?)'price' => \d+/", "$1'price' => " . $newPrices['executive_suite'], $configContent);
    $configContent = preg_replace("/('family_suite'.*?)'price' => \d+/", "$1'price' => " . $newPrices['family_suite'], $configContent);
    
    if (file_put_contents($configFile, $configContent)) {
        echo "✓ Updated pricing configuration file.\n";
    } else {
        echo "✗ Failed to update pricing configuration file.\n";
    }
} else {
    echo "✗ Configuration file not found: $configFile\n";
}

// Update the main index.php file
$indexFile = '../index.php';
if (file_exists($indexFile)) {
    $indexContent = file_get_contents($indexFile);
    
    // Update prices in the featured rooms section
    $indexContent = preg_replace("/(\$120|120<span>\/night<\/span>)/", '$' . $newPrices['standard'] . '<span>/night</span>', $indexContent);
    $indexContent = preg_replace("/(\$180|180<span>\/night<\/span>)/", '$' . $newPrices['deluxe'] . '<span>/night</span>', $indexContent);
    $indexContent = preg_replace("/(\$280|280<span>\/night<\/span>)/", '$' . $newPrices['executive_suite'] . '<span>/night</span>', $indexContent);
    $indexContent = preg_replace("/(\$220|220<span>\/night<\/span>)/", '$' . $newPrices['family_suite'] . '<span>/night</span>', $indexContent);
    
    if (file_put_contents($indexFile, $indexContent)) {
        echo "✓ Updated main index file.\n";
    } else {
        echo "✗ Failed to update main index file.\n";
    }
} else {
    echo "✗ Main index file not found: $indexFile\n";
}

// Update the rooms page
$roomsFile = 'pages/rooms.php';
if (file_exists($roomsFile)) {
    $roomsContent = file_get_contents($roomsFile);
    
    // Update prices in the rooms page
    $roomsContent = preg_replace("/(\$120|120<span>\/night<\/span>)/", '$' . $newPrices['standard'] . '<span>/night</span>', $roomsContent);
    $roomsContent = preg_replace("/(\$180|180<span>\/night<\/span>)/", '$' . $newPrices['deluxe'] . '<span>/night</span>', $roomsContent);
    $roomsContent = preg_replace("/(\$280|280<span>\/night<\/span>)/", '$' . $newPrices['executive_suite'] . '<span>/night</span>', $roomsContent);
    $roomsContent = preg_replace("/(\$220|220<span>\/night<\/span>)/", '$' . $newPrices['family_suite'] . '<span>/night</span>', $roomsContent);
    
    if (file_put_contents($roomsFile, $roomsContent)) {
        echo "✓ Updated rooms page.\n";
    } else {
        echo "✗ Failed to update rooms page.\n";
    }
} else {
    echo "✗ Rooms page not found: $roomsFile\n";
}

// Update the booking page
$bookingFile = 'pages/booking.php';
if (file_exists($bookingFile)) {
    $bookingContent = file_get_contents($bookingFile);
    
    // Update prices in the booking page
    $bookingContent = preg_replace("/(\$120|120<span>\/night<\/span>)/", '$' . $newPrices['standard'] . '<span>/night</span>', $bookingContent);
    $bookingContent = preg_replace("/(\$180|180<span>\/night<\/span>)/", '$' . $newPrices['deluxe'] . '<span>/night</span>', $bookingContent);
    $bookingContent = preg_replace("/(\$280|280<span>\/night<\/span>)/", '$' . $newPrices['executive_suite'] . '<span>/night</span>', $bookingContent);
    $bookingContent = preg_replace("/(\$220|220<span>\/night<\/span>)/", '$' . $newPrices['family_suite'] . '<span>/night</span>', $bookingContent);
    
    if (file_put_contents($bookingFile, $bookingContent)) {
        echo "✓ Updated booking page.\n";
    } else {
        echo "✗ Failed to update booking page.\n";
    }
} else {
    echo "✗ Booking page not found: $bookingFile\n";
}

echo "\nPrice update completed successfully!\n";
echo "New prices:\n";
echo "- Standard Room: $" . $newPrices['standard'] . "/night\n";
echo "- Deluxe Room: $" . $newPrices['deluxe'] . "/night\n";
echo "- Executive Suite: $" . $newPrices['executive_suite'] . "/night\n";
echo "- Family Suite: $" . $newPrices['family_suite'] . "/night\n";
?>