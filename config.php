<?php
// Configuration file for Liwonde Sun Hotel Website with Environment Detection

// Detect environment based on server name
$serverName = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? '';

// Determine environment
if (strpos($serverName, 'localhost') !== false ||
    strpos($serverName, '127.0.0.1') !== false ||
    strpos($serverName, '0.0.0.0') !== false ||
    strpos($serverName, 'liwonde-sun-hotel-2026') !== false) {
    define('ENVIRONMENT', 'UAT');
} else {
    define('ENVIRONMENT', 'PROD');
}

// Environment-specific settings
if (ENVIRONMENT === 'UAT') {
    // UAT/Development settings
    define('SITE_NAME', 'Liwonde Sun Hotel - UAT');
    define('SITE_URL', 'http://' . $serverName . '/liwondesunhotel.com/public_html/liwonde-sun-hotel-2026');
    define('SITE_EMAIL', 'uat-info@liwondesunhotel.com');
    define('RESERVATIONS_EMAIL', 'uat-reservations@liwondesunhotel.com');
    define('SHOW_ERRORS', true);
    define('DEBUG_MODE', true);
} else {
    // Production settings
    define('SITE_NAME', 'Liwonde Sun Hotel');
    define('SITE_URL', 'https://www.liwondesunhotel.com');
    define('SITE_EMAIL', 'info@liwondesunhotel.com');
    define('RESERVATIONS_EMAIL', 'reservations@liwondesunhotel.com');
    define('SHOW_ERRORS', false);
    define('DEBUG_MODE', false);
}

// Contact information (same for both environments)
define('CONTACT_PHONE', '+265 123 456 789');
define('CONTACT_ADDRESS', 'Liwonde National Park, Malawi');
define('CONTACT_PO_BOX', 'P.O. Box 1234, Blantyre, Malawi');

// Business hours
define('FRONT_DESK_HOURS', '24/7');
define('RESTAURANT_HOURS', '6:30 AM - 10:00 PM');

// Social media links
define('SOCIAL_FACEBOOK', '#');
define('SOCIAL_TWITTER', '#');
define('SOCIAL_INSTAGRAM', '#');
define('SOCIAL_LINKEDIN', '#');

// Currency settings
define('CURRENCY_SYMBOL', '$');
define('CURRENCY_CODE', 'USD');

// Booking settings
define('MIN_BOOKING_DAYS', 1);
define('MAX_BOOKING_DAYS', 30);
define('CANCELLATION_HOURS', 48); // Hours before arrival

// Email settings (update with your SMTP settings)
define('SMTP_HOST', 'smtp.gmail.com'); // Example SMTP server
define('SMTP_PORT', 587);             // Example SMTP port
define('SMTP_USERNAME', 'your-email@gmail.com');
define('SMTP_PASSWORD', 'your-password');
define('SMTP_SECURE', 'tls');         // Encryption method

// Image settings
define('MAX_IMAGE_SIZE', 5000000); // 5MB in bytes
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif']);

// Error reporting based on environment
if (SHOW_ERRORS) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

// Timezone
date_default_timezone_set('Africa/Blantyre');

// Session settings
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);

// Debug info (only in UAT)
if (defined('DEBUG_MODE') && DEBUG_MODE) {
    define('DEBUG_INFO', [
        'environment' => ENVIRONMENT,
        'server_name' => $serverName,
        'site_url' => SITE_URL
    ]);
}