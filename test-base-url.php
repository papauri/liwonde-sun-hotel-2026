<?php
/**
 * Test Base URL Configuration
 */

require_once 'config/base-url.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Base URL Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; }
        h1 { color: #0a1929; }
        .test-item { padding: 10px; margin: 10px 0; border-left: 4px solid #28a745; background: #f8f9fa; }
        .url { font-family: monospace; color: #007bff; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔗 Base URL Test</h1>
        
        <div class="test-item">
            <strong>BASE_PATH:</strong> <span class="url"><?php echo BASE_PATH; ?></span>
        </div>
        
        <div class="test-item">
            <strong>Current Script:</strong> <span class="url"><?php echo $_SERVER['SCRIPT_NAME']; ?></span>
        </div>
        
        <h2>Generated URLs:</h2>
        
        <div class="test-item">
            <strong>siteUrl('/'):</strong> <span class="url"><?php echo siteUrl('/'); ?></span>
        </div>
        
        <div class="test-item">
            <strong>siteUrl('rooms-gallery.php'):</strong> <span class="url"><?php echo siteUrl('rooms-gallery.php'); ?></span>
        </div>
        
        <div class="test-item">
            <strong>siteUrl('booking.php'):</strong> <span class="url"><?php echo siteUrl('booking.php'); ?></span>
        </div>
        
        <h2>Test Links:</h2>
        <div class="test-item">
            <a href="<?php echo siteUrl('/'); ?>">Home</a> |
            <a href="<?php echo siteUrl('rooms-gallery.php'); ?>">Rooms</a> |
            <a href="<?php echo siteUrl('booking.php'); ?>">Book Now</a>
        </div>
        
        <p><strong>✅ If the links above work correctly, the base URL is configured properly!</strong></p>
    </div>
</body>
</html>
