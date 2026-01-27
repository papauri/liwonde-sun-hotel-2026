<?php
/**
 * Script to add hotel policy settings to site_settings table
 */

require_once __DIR__ . '/../config/database.php';

echo "Adding hotel policy settings to database...\n";
echo "==========================================\n\n";

try {
    // Read SQL file
    $sqlFile = __DIR__ . '/../Database/add-hotel-policy-settings.sql';
    
    if (!file_exists($sqlFile)) {
        throw new Exception("SQL file not found: $sqlFile");
    }
    
    $sql = file_get_contents($sqlFile);
    
    // Execute SQL
    $pdo->exec($sql);
    
    echo "✅ Hotel policy settings added successfully!\n\n";
    
    // Verify the settings were added
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM site_settings WHERE setting_key IN ('check_in_time', 'check_out_time', 'booking_change_policy') ORDER BY setting_key");
    $settings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Current hotel policy settings:\n";
    echo "==============================\n";
    foreach ($settings as $setting) {
        echo "- " . $setting['setting_key'] . ": " . $setting['setting_value'] . "\n";
    }
    
    echo "\n✅ Done. Settings are now stored in the database and can be managed via admin panel.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>