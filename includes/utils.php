<?php
// Environment utility functions for Liwonde Sun Hotel Website

// Include environment detection
require_once 'includes/environment.php';

/**
 * Function to display environment badge on pages when in UAT
 */
function displayEnvironmentBadge() {
    if (isUAT()) {
        echo '<div id="environment-badge" style="position: fixed; top: 10px; right: 10px; background: #ff6b35; color: white; padding: 5px 10px; border-radius: 3px; font-size: 12px; z-index: 9999; font-weight: bold;">UAT ENVIRONMENT</div>';
    }
}

/**
 * Function to get analytics code based on environment
 */
function getAnalyticsCode() {
    if (isProd()) {
        // Production analytics code would go here
        return "
        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src='https://www.googletagmanager.com/gtag/js?id=GA_MEASUREMENT_ID'></script>
        <script>
          window.dataLayer = window.dataLayer || [];
          function gtag(){dataLayer.push(arguments);}
          gtag('js', new Date());
          gtag('config', 'GA_MEASUREMENT_ID');
        </script>";
    } else {
        // No analytics in UAT
        return "<!-- Analytics disabled in UAT environment -->";
    }
}

/**
 * Function to get API endpoints based on environment
 */
function getApiEndpoint($endpoint) {
    if (isUAT()) {
        return 'http://localhost:8000/api/' . $endpoint;
    } else {
        return 'https://api.liwondesunhotel.com/' . $endpoint;
    }
}

/**
 * Function to get asset URLs based on environment
 */
function getAssetUrl($assetPath) {
    if (isUAT()) {
        // In UAT, serve assets directly from the local server
        return $assetPath;
    } else {
        // In production, could serve from CDN
        return $assetPath; // For now, same as UAT
    }
}

/**
 * Function to get form action URLs based on environment
 */
function getFormAction($formType) {
    if (isUAT()) {
        return 'http://localhost:8000/process.php';
    } else {
        return 'https://www.liwondesunhotel.com/process.php';
    }
}

/**
 * Function to get debug information display
 */
function displayDebugInfo() {
    if (isUAT()) {
        $debugInfo = getDebugInfo();
        if ($debugInfo) {
            echo '<div id="debug-info" style="position: fixed; bottom: 10px; left: 10px; background: #333; color: #00ff00; padding: 10px; border-radius: 3px; font-size: 11px; z-index: 9999; max-width: 300px; font-family: monospace;">';
            echo '<strong>DEBUG INFO:</strong><br>';
            foreach ($debugInfo as $key => $value) {
                echo $key . ': ' . $value . '<br>';
            }
            echo '</div>';
        }
    }
}

/**
 * Function to check if a specific feature should be enabled based on environment
 */
function isFeatureEnabled($featureName) {
    // Some features might only be enabled in certain environments
    $disabledFeaturesInUAT = []; // Add feature names that should be disabled in UAT
    
    if (isUAT() && in_array($featureName, $disabledFeaturesInUAT)) {
        return false;
    }
    
    return true;
}

/**
 * Function to get the current page URL with environment context
 */
function getCurrentPageUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $uri = $_SERVER['REQUEST_URI'];
    
    return $protocol . '://' . $host . $uri;
}