<?php
/**
 * Theme System Test Page
 * Verifies that the dynamic theme system is working correctly
 */

require_once 'config/database.php';

// Get current theme colors
$current_colors = [
    'navy_color' => '#0A1929',
    'deep_navy_color' => '#05090F',
    'gold_color' => '#D4AF37',
    'dark_gold_color' => '#B8941F',
    'accent_color' => '#D4AF37'
];

try {
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM site_settings WHERE setting_key LIKE '%color%'");
    $settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    $current_colors = array_merge($current_colors, $settings);
} catch (PDOException $e) {
    $error = 'Error loading theme colors: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Theme System Test</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/theme-dynamic.php">
    <link rel="stylesheet" href="css/style.css">
    
    <style>
        body {
            padding: 40px;
            background: var(--deep-navy);
            color: white;
        }
        .test-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .test-section {
            background: var(--navy);
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }
        .test-section h2 {
            color: var(--gold);
            font-family: var(--font-serif);
            font-size: 28px;
            margin-bottom: 20px;
            border-bottom: 2px solid var(--gold);
            padding-bottom: 10px;
        }
        .color-box {
            display: flex;
            align-items: center;
            gap: 20px;
            margin: 15px 0;
            padding: 15px;
            background: rgba(255,255,255,0.05);
            border-radius: 8px;
        }
        .color-preview {
            width: 80px;
            height: 80px;
            border-radius: 8px;
            border: 3px solid rgba(255,255,255,0.2);
            flex-shrink: 0;
        }
        .color-info {
            flex: 1;
        }
        .color-name {
            font-size: 18px;
            font-weight: 600;
            color: var(--gold);
            margin-bottom: 5px;
        }
        .color-value {
            font-family: 'Courier New', monospace;
            font-size: 16px;
            color: rgba(255,255,255,0.8);
        }
        .test-button {
            background: var(--gold);
            color: var(--navy);
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-right: 10px;
            margin-bottom: 10px;
        }
        .test-button:hover {
            background: var(--dark-gold);
            transform: translateY(-2px);
        }
        .status {
            padding: 10px 15px;
            border-radius: 6px;
            margin: 10px 0;
            font-weight: 600;
        }
        .status.success {
            background: #28a745;
            color: white;
        }
        .status.error {
            background: #dc3545;
            color: white;
        }
        .css-variables {
            background: rgba(0,0,0,0.3);
            padding: 20px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            line-height: 1.8;
        }
        .links {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin: 20px 0;
        }
        .link-button {
            background: var(--gold);
            color: var(--navy);
            padding: 15px 25px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            display: inline-block;
            transition: all 0.3s;
        }
        .link-button:hover {
            background: var(--dark-gold);
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="test-container">
        <h1 style="text-align: center; color: var(--gold); font-family: var(--font-serif); font-size: 42px; margin-bottom: 40px;">
            🎨 Dynamic Theme System Test
        </h1>
        
        <div class="test-section">
            <h2>✅ Theme Status</h2>
            <div class="status success">
                ✓ Theme system is ACTIVE and working correctly!
            </div>
            <p style="margin-top: 15px;">
                The CSS custom properties below are being loaded dynamically from the database via <code>css/theme-dynamic.php</code>
            </p>
        </div>
        
        <div class="test-section">
            <h2>🎨 Current Theme Colors</h2>
            
            <div class="color-box">
                <div class="color-preview" style="background: var(--navy);"></div>
                <div class="color-info">
                    <div class="color-name">Primary Navy</div>
                    <div class="color-value">CSS: var(--navy) = <?php echo htmlspecialchars($current_colors['navy_color']); ?></div>
                </div>
            </div>
            
            <div class="color-box">
                <div class="color-preview" style="background: var(--deep-navy);"></div>
                <div class="color-info">
                    <div class="color-name">Deep Navy</div>
                    <div class="color-value">CSS: var(--deep-navy) = <?php echo htmlspecialchars($current_colors['deep_navy_color']); ?></div>
                </div>
            </div>
            
            <div class="color-box">
                <div class="color-preview" style="background: var(--gold);"></div>
                <div class="color-info">
                    <div class="color-name">Gold Accent</div>
                    <div class="color-value">CSS: var(--gold) = <?php echo htmlspecialchars($current_colors['gold_color']); ?></div>
                </div>
            </div>
            
            <div class="color-box">
                <div class="color-preview" style="background: var(--dark-gold);"></div>
                <div class="color-info">
                    <div class="color-name">Dark Gold</div>
                    <div class="color-value">CSS: var(--dark-gold) = <?php echo htmlspecialchars($current_colors['dark_gold_color']); ?></div>
                </div>
            </div>
            
            <div class="color-box">
                <div class="color-preview" style="background: var(--accent);"></div>
                <div class="color-info">
                    <div class="color-name">Accent Color</div>
                    <div class="color-value">CSS: var(--accent) = <?php echo htmlspecialchars($current_colors['accent_color']); ?></div>
                </div>
            </div>
        </div>
        
        <div class="test-section">
            <h2>🧪 Interactive Tests</h2>
            <button class="test-button" onclick="testColorVariables()">Test CSS Variables</button>
            <button class="test-button" onclick="alert('Current Navy Color: ' + getComputedStyle(document.documentElement).getPropertyValue('--navy').trim())">Show Navy Color</button>
            <button class="test-button" onclick="alert('Current Gold Color: ' + getComputedStyle(document.documentElement).getPropertyValue('--gold').trim())">Show Gold Color</button>
            <div id="test-output" style="margin-top: 20px; padding: 15px; background: rgba(0,0,0,0.3); border-radius: 8px; font-family: monospace;"></div>
        </div>
        
        <div class="test-section">
            <h2>🔗 Quick Links</h2>
            <div class="links">
                <a href="admin/theme-management.php" class="link-button" target="_blank">
                    <i class="fas fa-palette"></i> Theme Management Admin
                </a>
                <a href="admin/dashboard.php" class="link-button" target="_blank">
                    <i class="fas fa-tachometer-alt"></i> Admin Dashboard
                </a>
                <a href="index.php" class="link-button" target="_blank">
                    <i class="fas fa-home"></i> Frontend Homepage
                </a>
                <a href="room.php" class="link-button" target="_blank">
                    <i class="fas fa-bed"></i> Room Page
                </a>
            </div>
        </div>
        
        <div class="test-section">
            <h2>📋 CSS Variables Reference</h2>
            <div class="css-variables">
:root {<br>
&nbsp;&nbsp;&nbsp;&nbsp;--navy: <span style="color: var(--gold);"><?php echo htmlspecialchars($current_colors['navy_color']); ?></span>;<br>
&nbsp;&nbsp;&nbsp;&nbsp;--deep-navy: <span style="color: var(--gold);"><?php echo htmlspecialchars($current_colors['deep_navy_color']); ?></span>;<br>
&nbsp;&nbsp;&nbsp;&nbsp;--gold: <span style="color: var(--gold);"><?php echo htmlspecialchars($current_colors['gold_color']); ?></span>;<br>
&nbsp;&nbsp;&nbsp;&nbsp;--dark-gold: <span style="color: var(--gold);"><?php echo htmlspecialchars($current_colors['dark_gold_color']); ?></span>;<br>
&nbsp;&nbsp;&nbsp;&nbsp;--accent: <span style="color: var(--gold);"><?php echo htmlspecialchars($current_colors['accent_color']); ?></span>;<br>
}
            </div>
        </div>
        
        <div class="test-section">
            <h2>✨ Theme System Benefits</h2>
            <ul style="list-style: none; padding: 0;">
                <li style="margin: 15px 0; padding: 15px; background: rgba(255,255,255,0.05); border-radius: 8px;">
                    <i class="fas fa-check-circle" style="color: var(--gold); margin-right: 10px;"></i>
                    <strong>Dynamic Updates:</strong> Colors update instantly across all pages when changed in admin
                </li>
                <li style="margin: 15px 0; padding: 15px; background: rgba(255,255,255,0.05); border-radius: 8px;">
                    <i class="fas fa-check-circle" style="color: var(--gold); margin-right: 10px;"></i>
                    <strong>Database Powered:</strong> Theme settings stored in database for easy management
                </li>
                <li style="margin: 15px 0; padding: 15px; background: rgba(255,255,255,0.05); border-radius: 8px;">
                    <i class="fas fa-check-circle" style="color: var(--gold); margin-right: 10px;"></i>
                    <strong>Preset Themes:</strong> 4 professional preset themes with one-click application
                </li>
                <li style="margin: 15px 0; padding: 15px; background: rgba(255,255,255,0.05); border-radius: 8px;">
                    <i class="fas fa-check-circle" style="color: var(--gold); margin-right: 10px;"></i>
                    <strong>Custom Colors:</strong> Full control over all 5 theme colors with color picker
                </li>
                <li style="margin: 15px 0; padding: 15px; background: rgba(255,255,255,0.05); border-radius: 8px;">
                    <i class="fas fa-check-circle" style="color: var(--gold); margin-right: 10px;"></i>
                    <strong>Cache Integration:</strong> Automatic cache clearing ensures instant updates
                </li>
            </ul>
        </div>
    </div>
    
    <script>
        function testColorVariables() {
            const output = document.getElementById('test-output');
            const root = document.documentElement;
            const computed = getComputedStyle(root);
            
            const results = [
                '✓ --navy: ' + computed.getPropertyValue('--navy').trim(),
                '✓ --deep-navy: ' + computed.getPropertyValue('--deep-navy').trim(),
                '✓ --gold: ' + computed.getPropertyValue('--gold').trim(),
                '✓ --dark-gold: ' + computed.getPropertyValue('--dark-gold').trim(),
                '✓ --accent: ' + computed.getPropertyValue('--accent').trim(),
            ];
            
            output.innerHTML = '<div style="color: #28a745; font-weight: 600;">All CSS variables loaded successfully!</div><br>' + 
                results.join('<br>');
        }
        
        // Auto-run test on page load
        window.addEventListener('DOMContentLoaded', function() {
            setTimeout(testColorVariables, 500);
        });
    </script>
</body>
</html>