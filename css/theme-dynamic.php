<?php
/**
 * Dynamic Theme CSS with Aggressive Caching
 * Generates CSS variables from database settings
 * This file is included as a CSS stylesheet
 */

// Include database connection (uses singleton pattern, no duplicate connections)
require_once __DIR__ . '/../config/database.php';

// Generate ETag based on theme settings version
$theme_version = getSetting('theme_version', '1.0');
$etag = md5('theme-v' . $theme_version);

// Check If-None-Match header for 304 response
if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] === $etag) {
    header('HTTP/1.1 304 Not Modified');
    header('ETag: ' . $etag);
    exit;
}

// Set caching headers (cache for 1 hour)
header('Content-Type: text/css; charset=utf-8');
header('Cache-Control: public, max-age=3600, must-revalidate');
header('ETag: ' . $etag);
header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 3600) . ' GMT');

// Get theme colors using cached getSetting function (much faster)
// Passalacqua-inspired warm cream & charcoal palette
$navy = getSetting('navy_color', '#1A1A1A');
$deep_navy = getSetting('deep_navy_color', '#111111');
$gold = getSetting('gold_color', '#8B7355');
$dark_gold = getSetting('dark_gold_color', '#6B5740');
$theme_color = getSetting('theme_color', '#1A1A1A');
$accent_color = getSetting('accent_color', '#8B7355');
?>

:root {
    /* ============================================
       PASSALACQUA-INSPIRED — WARM LUXURY PALETTE
       ============================================ */
    
    /* Primary — Deep Charcoal (near-black) */
    --navy: <?php echo $navy; ?>;
    --deep-navy: <?php echo $deep_navy; ?>;
    --theme-color: <?php echo $theme_color; ?>;
    
    /* Accent — Warm Bronze / Olive */
    --gold: <?php echo $gold; ?>;
    --dark-gold: <?php echo $dark_gold; ?>;
    --accent-color: <?php echo $accent_color; ?>;
    
    /* Warm Cream Backgrounds */
    --cream: #F5F0EB;
    --cream-light: #FAF8F5;
    --cream-dark: #EDE7E0;
    
    /* Neutral Colors */
    --white: #ffffff;
    --light-gray: #F0ECE6;
    --medium-gray: #999999;
    --dark-gray: #333333;
    --charcoal: #1A1A1A;
    --text-primary: #1A1A1A;
    --text-secondary: #6B6B6B;
    --text-muted: #999999;
    
    /* Status Colors */
    --success: #4A7C59;
    --danger: #C45B5B;
    --warning: #C49B2E;
    --info: #5B7FA5;
    
    /* Typography — Elegant Serif + Clean Geometric Sans */
    --font-sans: 'Jost', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    --font-serif: 'Cormorant Garamond', Georgia, 'Times New Roman', serif;
    
    /* Shadows — Extremely Subtle */
    --shadow-subtle: 0 1px 3px rgba(0, 0, 0, 0.04);
    --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.06);
    --shadow-md: 0 4px 20px rgba(0, 0, 0, 0.08);
    --shadow-lg: 0 8px 30px rgba(0, 0, 0, 0.10);
    --shadow-xl: 0 16px 50px rgba(0, 0, 0, 0.12);
    --shadow-premium: 0 20px 60px rgba(0, 0, 0, 0.08);
    --shadow-luxury: 0 8px 40px rgba(0, 0, 0, 0.06);
    --shadow-glow: none;
    
    /* Transitions — Smooth & Understated */
    --transition-fast: 0.2s ease;
    --transition-base: 0.4s cubic-bezier(0.25, 0.1, 0.25, 1);
    --transition-slow: 0.7s cubic-bezier(0.25, 0.1, 0.25, 1);
    --transition-spring: 0.5s cubic-bezier(0.25, 0.1, 0.25, 1);
    
    /* Border Radius — Minimal & Clean */
    --radius-sm: 4px;
    --radius-md: 8px;
    --radius-lg: 12px;
    --radius-xl: 16px;
    --radius-full: 9999px;
}