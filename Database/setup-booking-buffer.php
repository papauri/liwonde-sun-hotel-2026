<?php
/**
 * Setup script for booking time buffer setting
 * Run this file to add the booking_time_buffer_minutes setting to site_settings
 */

require_once __DIR__ . '/../config/database.php';

echo "<h2>Setting up Booking Time Buffer Setting</h2>";

try {
    // Check if setting already exists
    $checkStmt = $pdo->prepare("SELECT setting_key FROM site_settings WHERE setting_key = 'booking_time_buffer_minutes'");
    $checkStmt->execute();
    
    if ($checkStmt->rowCount() > 0) {
        echo "<p style='color: orange;'>⚠️ Setting 'booking_time_buffer_minutes' already exists. Skipping insertion.</p>";
        
        // Display current value
        $stmt = $pdo->prepare("SELECT * FROM site_settings WHERE setting_key = 'booking_time_buffer_minutes'");
        $stmt->execute();
        $setting = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<pre>";
        print_r($setting);
        echo "</pre>";
    } else {
        // Insert the new setting (using correct column names for the actual table structure)
        $insertStmt = $pdo->prepare("
            INSERT INTO site_settings (
                setting_key,
                setting_value,
                setting_group
            ) VALUES (
                'booking_time_buffer_minutes',
                '60',
                'booking'
            )
        ");
        
        $insertStmt->execute();
        echo "<p style='color: green;'>✅ Successfully added 'booking_time_buffer_minutes' setting with default value of 60 minutes.</p>";
        
        // Verify insertion
        $stmt = $pdo->prepare("SELECT * FROM site_settings WHERE setting_key = 'booking_time_buffer_minutes'");
        $stmt->execute();
        $setting = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<pre>";
        print_r($setting);
        echo "</pre>";
    }
    
    echo "<p style='color: blue;'>ℹ️ You can now use getSetting('booking_time_buffer_minutes') to retrieve this value.</p>";
    echo "<p><a href='../admin/booking-settings.php'>Go to Booking Settings</a></p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
