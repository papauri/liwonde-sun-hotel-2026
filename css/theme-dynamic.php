<?php
/**
 * Dynamic Theme CSS
 * Generates CSS variables from database settings
 * This file is included as a CSS stylesheet
 */
header('Content-Type: text/css; charset=utf-8');

// Include database connection
require_once __DIR__ . '/../config/database.php';

// Get theme colors from database with defaults
try {
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM site_settings WHERE setting_key LIKE '%color%' OR setting_key LIKE '%theme%'");
    $settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    
    $navy = $settings['navy_color'] ?? '#0A1929';
    $deep_navy = $settings['deep_navy_color'] ?? '#05090F';
    $gold = $settings['gold_color'] ?? '#D4AF37';
    $dark_gold = $settings['dark_gold_color'] ?? '#B8941F';
    $theme_color = $settings['theme_color'] ?? '#0A1929';
    $accent_color = $settings['accent_color'] ?? $gold;
    
} catch (PDOException $e) {
    // Fallback to defaults if database fails
    $navy = '#0A1929';
    $deep_navy = '#05090F';
    $gold = '#D4AF37';
    $dark_gold = '#B8941F';
    $theme_color = '#0A1929';
    $accent_color = $gold;
}
?>

:root {
    /* Primary Colors */
    --navy: <?php echo $navy; ?>;
    --deep-navy: <?php echo $deep_navy; ?>;
    --theme-color: <?php echo $theme_color; ?>;
    
    /* Accent Colors */
    --gold: <?php echo $gold; ?>;
    --dark-gold: <?php echo $dark_gold; ?>;
    --accent-color: <?php echo $accent_color; ?>;
    
    /* Neutral Colors */
    --white: #ffffff;
    --cream: #FBF8F3;
    --light-gray: #f8f9fa;
    --medium-gray: #6c757d;
    --dark-gray: #343a40;
    
    /* Status Colors */
    --success: #28a745;
    --danger: #dc3545;
    --warning: #ffc107;
    --info: #17a2b8;
    
    /* Typography */
    --font-sans: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    --font-serif: 'Playfair Display', Georgia, serif;
    
    /* Shadows */
    --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.1);
    --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.1);
    --shadow-xl: 0 20px 25px rgba(0, 0, 0, 0.1);
    --shadow-luxury: 0 8px 30px rgba(212, 175, 55, 0.15);
    
    /* Transitions */
    --transition-fast: 0.15s ease;
    --transition-base: 0.3s ease;
    --transition-slow: 0.5s ease;
    
    /* Border Radius */
    --radius-sm: 8px;
    --radius-md: 12px;
    --radius-lg: 16px;
    --radius-xl: 24px;
    --radius-full: 9999px;
}