<?php
// Environment Detection and Configuration Loader
// This file should be included at the top of all PHP pages

// Define the base path relative to this file
$basePath = dirname(__DIR__);

// Include the config file
$configFile = $basePath . '/config.php';
if (file_exists($configFile)) {
    require_once $configFile;
} else {
    // Fallback configuration if config.php is missing
    define('ENVIRONMENT', 'PROD');
    define('SITE_NAME', 'Liwonde Sun Hotel');
    define('SITE_URL', 'https://www.liwondesunhotel.com');
    define('SITE_EMAIL', 'info@liwondesunhotel.com');
    define('SHOW_ERRORS', false);
}

// Function to get the current environment
function getEnvironment() {
    return defined('ENVIRONMENT') ? ENVIRONMENT : 'PROD';
}

// Function to check if in UAT environment
function isUAT() {
    return getEnvironment() === 'UAT';
}

// Function to check if in PROD environment
function isProd() {
    return getEnvironment() === 'PROD';
}

// Function to get the site URL based on environment
function getSiteUrl() {
    return defined('SITE_URL') ? SITE_URL : 'https://www.liwondesunhotel.com';
}

// Function to get the site name based on environment
function getSiteName() {
    return defined('SITE_NAME') ? SITE_NAME : 'Liwonde Sun Hotel';
}

// Function to conditionally load resources based on environment
function getResourcePath($path) {
    if (isUAT()) {
        // In UAT, we might want to bust cache more aggressively
        return $path . '?v=' . time();
    }
    return $path;
}

// Function to get debug information if in UAT
function getDebugInfo() {
    if (isUAT() && defined('DEBUG_INFO')) {
        return DEBUG_INFO;
    }
    return null;
}

// Add HTTP security headers based on environment
if (isProd()) {
    // In production, enforce HTTPS
    if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
            // Handle load balancer case
            $_SERVER['HTTPS'] = 'on';
        } else {
            // Redirect to HTTPS in production
            // Uncomment the following lines when deploying to production
            // $redirectUrl = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            // header("Location: $redirectUrl", true, 301);
            // exit();
        }
    }
}

// Set appropriate cache headers based on environment
if (isUAT()) {
    // Disable caching in UAT for easier testing
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Pragma: no-cache");
    header("Expires: 0");
} else {
    // Set appropriate caching headers for production
    header("Cache-Control: public, max-age=3600"); // Cache for 1 hour
}