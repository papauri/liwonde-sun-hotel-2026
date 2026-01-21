<?php
require_once 'config/database.php';

// Fetch site settings
$site_name = getSetting('site_name', 'Liwonde Sun Hotel');
$site_logo = getSetting('site_logo', 'images/logo/logo.png');
$currency_symbol = getSetting('currency_symbol', 'K');
$currency_code = getSetting('currency_code', 'MWK');

// Fetch policies for footer modals
$policies = [];
try {
    $policyStmt = $pdo->query("SELECT slug, title, summary, content FROM policies WHERE is_active = 1 ORDER BY display_order ASC, id ASC");
    $policies = $policyStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $policies = [];
}

// Determine which menu to display (default: Food)
$menu_type = isset($_GET['menu']) ? $_GET['menu'] : 'Food';
$valid_menus = ['Food', 'Coffee', 'Bar'];
if (!in_array($menu_type, $valid_menus)) {
    $menu_type = 'Food';
}

// Menu type labels and icons
$menu_labels = [
    'Food' => ['label' => 'Food Menu', 'icon' => 'fa-utensils'],
    'Coffee' => ['label' => 'Coffee Shop', 'icon' => 'fa-mug-hot'],
    'Bar' => ['label' => 'Bar & Drinks', 'icon' => 'fa-wine-glass']
];

// Fetch menu items for the selected menu type
try {
    $stmt = $pdo->prepare("
        SELECT * FROM menu_items 
        WHERE is_active = 1 AND menu_type = ?
        ORDER BY category_order ASC, item_order ASC
    ");
    $stmt->execute([$menu_type]);
    $all_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $all_items = [];
}

// Group items by category
$menu_items = [];
$category_orders = [];
foreach ($all_items as $item) {
    $cat = $item['category'];
    if (!isset($menu_items[$cat])) {
        $menu_items[$cat] = [];
        $category_orders[$cat] = $item['category_order'];
    }
    $menu_items[$cat][] = $item;
}

// Sort by category order
uasort($menu_items, function($a, $b) use ($category_orders) {
    $cat_a = array_key_first($a);
    $cat_b = array_key_first($b);
    return ($category_orders[$cat_a] ?? 0) - ($category_orders[$cat_b] ?? 0);
});

// Generate QR code URL
$current_url = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/restaurant.php?menu=' . urlencode($menu_type);
$qr_code_url = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($current_url);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes, viewport-fit=cover">
    <meta name="theme-color" content="#0A1929">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="format-detection" content="telephone=yes">
    <title><?php echo htmlspecialchars($site_name); ?> - Restaurant</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Restaurant Page Specific Styles */
        .restaurant-hero {
            position: relative;
            min-height: 620px;
            display: flex;
            align-items: center;
            background: linear-gradient(135deg, var(--deep-navy) 0%, #1a2844 100%);
            padding: 120px 0 80px 0;
            overflow: hidden;
            margin-top: 0;
        }

        .restaurant-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: repeating-linear-gradient(45deg, transparent, transparent 35px, rgba(212, 175, 55, 0.03) 35px, rgba(212, 175, 55, 0.03) 70px);
            animation: slidePattern 20s linear infinite;
        }

        @keyframes slidePattern {
            0% { transform: translateX(0); }
            100% { transform: translateX(70px); }
        }

        .restaurant-hero .container {
            position: relative;
            z-index: 10;
        }

        .restaurant-hero-content {
            position: relative;
            color: white;
            animation: fadeInUp 1s cubic-bezier(0.34, 1.56, 0.64, 1) 0.2s backwards;
            max-width: 100%;
            padding: 0;
        }

        .restaurant-hero-layout {
            display: grid;
            grid-template-columns: minmax(0, 1.1fr) minmax(320px, 0.9fr);
            gap: 32px;
            align-items: center;
        }

        .hero-copy {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .hero-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.14);
            font-size: 12px;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .hero-eyebrow i {
            color: var(--gold);
        }

        .restaurant-hero-content h1 {
            font-family: 'Playfair Display', serif;
            font-size: 60px;
            font-weight: 700;
            margin-bottom: 10px;
            background: linear-gradient(135deg, var(--gold) 0%, #ffd700 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .restaurant-hero-content p {
            font-size: 18px;
            color: rgba(255, 255, 255, 0.8);
            letter-spacing: 0.6px;
            text-transform: none;
            max-width: 640px;
            margin-bottom: 20px;
        }

        .restaurant-hero-actions {
            display: flex;
            flex-direction: column;
            gap: 16px;
            align-items: flex-start;
        }

        /* Menu Type Selector */
        .menu-type-selector {
            display: flex;
            gap: 20px;
            justify-content: flex-start;
            margin: 16px 0 0 0;
            flex-wrap: wrap;
        }

        .menu-type-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 15px 30px;
            background: transparent;
            border: 2px solid var(--gold);
            color: var(--gold);
            font-family: 'Poppins', sans-serif;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            border-radius: 8px;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            position: relative;
            text-decoration: none;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .menu-type-btn:hover {
            background: rgba(212, 175, 55, 0.1);
            transform: translateY(-2px);
        }

        .menu-type-btn.active {
            background: linear-gradient(135deg, var(--gold) 0%, #ffc700 100%);
            color: var(--deep-navy);
            box-shadow: 0 8px 25px rgba(212, 175, 55, 0.3);
        }

        .menu-type-btn i {
            font-size: 20px;
        }

        .hero-qr-card {
            background: rgba(10, 25, 41, 0.65);
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: 18px;
            padding: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.35);
            backdrop-filter: blur(14px);
        }

        .qr-section {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .qr-text {
            text-align: left;
        }

        .qr-text h4 {
            margin: 0 0 10px 0;
            color: #fff;
            font-size: 16px;
        }

        .qr-text p {
            margin: 0 0 10px 0;
            color: rgba(255, 255, 255, 0.78);
            font-size: 13px;
        }

        .qr-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            font-size: 12px;
            letter-spacing: 0.4px;
            text-transform: uppercase;
        }

        /* Menu Container */
        .menu-container {
            background: #fafafa;
            padding: 60px 0;
        }

        .menu-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 36px;
            gap: 16px;
            flex-wrap: wrap;
        }

        .menu-header-text {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .menu-title {
            font-family: 'Playfair Display', serif;
            font-size: 42px;
            color: var(--deep-navy);
            margin: 0;
        }

        .qr-code {
            width: 150px;
            height: 150px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            background: white;
        }

        .qr-code img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .menu-kicker {
            margin: 0;
            color: #6b7280;
            font-size: 14px;
        }

        /* Category Buttons */
        .menu-categories {
            display: flex;
            gap: 15px;
            margin-bottom: 50px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .category-btn {
            padding: 12px 24px;
            background: white;
            border: 2px solid #ddd;
            color: var(--deep-navy);
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            border-radius: 25px;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .category-btn:hover {
            border-color: var(--gold);
            color: var(--gold);
            transform: translateY(-2px);
        }

        .category-btn.active {
            background: var(--gold);
            border-color: var(--gold);
            color: white;
            box-shadow: 0 6px 20px rgba(212, 175, 55, 0.3);
        }

        /* Menu Sections */
        .menu-section {
            display: none;
            animation: fadeIn 0.5s ease-in;
        }

        .menu-section.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 36px;
            font-weight: 700;
            color: var(--deep-navy);
            margin-bottom: 45px;
            padding-bottom: 18px;
            border-bottom: 4px solid transparent;
            background: linear-gradient(to right, var(--gold) 0%, var(--gold) 40%, transparent 40%);
            background-position: bottom;
            background-size: 100% 4px;
            background-repeat: no-repeat;
            display: inline-block;
            position: relative;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0;
            width: 40%;
            height: 4px;
            background: linear-gradient(90deg, var(--gold) 0%, #ffd700 100%);
            box-shadow: 0 2px 8px rgba(212, 175, 55, 0.4);
        }

        /* Menu Grid */
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 35px;
            margin-bottom: 60px;
        }

        @media (max-width: 768px) {
            .menu-grid {
                grid-template-columns: 1fr;
                gap: 25px;
            }
        }

        .menu-item {
            background: white;
            padding: 28px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            border: 1px solid rgba(212, 175, 55, 0.12);
            border-left: 4px solid var(--gold);
            animation: slideUp 0.5s ease forwards;
            display: flex;
            flex-direction: column;
            min-height: 220px;
            position: relative;
            overflow: hidden;
        }

        .menu-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--gold) 0%, #ffd700 50%, var(--gold) 100%);
            transform: scaleX(0);
            transition: transform 0.4s ease;
        }

        .menu-item:hover::before {
            transform: scaleX(1);
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .menu-item:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 35px rgba(212, 175, 55, 0.25);
            border-color: var(--gold);
            border-left-width: 5px;
        }

        .item-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 14px;
            gap: 20px;
            padding-bottom: 12px;
            border-bottom: 1px solid rgba(212, 175, 55, 0.15);
        }

        .item-name {
            font-family: 'Playfair Display', serif;
            font-size: 20px;
            font-weight: 700;
            color: var(--deep-navy);
            line-height: 1.3;
            flex: 1;
        }

        .item-price {
            color: var(--gold);
            font-weight: 800;
            font-size: 20px;
            letter-spacing: 0.3px;
            white-space: nowrap;
            background: linear-gradient(135deg, var(--gold) 0%, #ffd700 50%, var(--dark-gold) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: 0 2px 8px rgba(212, 175, 55, 0.2);
            font-family: 'Poppins', sans-serif;
        }

        .item-description {
            color: #555;
            font-size: 15px;
            line-height: 1.7;
            margin-bottom: 18px;
            flex-grow: 1;
            font-weight: 400;
        }

        .item-tags {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: auto;
        }

        .tag {
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.12) 0%, rgba(212, 175, 55, 0.08) 100%);
            color: var(--gold);
            padding: 6px 14px;
            font-size: 11px;
            border-radius: 20px;
            font-weight: 700;
            text-transform: uppercase;
            border: 1px solid rgba(212, 175, 55, 0.25);
            transition: all 0.3s ease;
            letter-spacing: 0.5px;
        }

        .tag:hover {
            background: var(--gold);
            color: var(--deep-navy);
            transform: scale(1.05);
        }

        @media (max-width: 1024px) {
            .menu-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .restaurant-hero-content h1 {
                font-size: 48px;
            }

            .restaurant-hero-layout {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .restaurant-hero {
                min-height: 580px;
                align-items: center;
                padding: 100px 0 60px 0;
            }

            .restaurant-hero-layout {
                grid-template-columns: 1fr;
                gap: 24px;
            }

            .hero-eyebrow {
                font-size: 10px;
                padding: 6px 12px;
            }

            .restaurant-hero-content h1 {
                font-size: 28px;
                margin-bottom: 8px;
            }

            .restaurant-hero-content p {
                font-size: 14px;
                margin-bottom: 16px;
            }

            .menu-type-selector {
                gap: 10px;
                margin: 16px 0 0 0;
                flex-wrap: wrap;
            }

            .menu-type-btn {
                padding: 12px 18px;
                font-size: 12px;
                border-radius: 8px;
            }

            .menu-type-btn i {
                font-size: 16px;
            }

            .hero-qr-card {
                padding: 20px;
            }

            .qr-code {
                width: 100px;
                height: 100px;
            }

            .qr-text h4 {
                font-size: 14px;
                margin-bottom: 8px;
            }

            .qr-text p {
                font-size: 12px;
                margin-bottom: 8px;
            }

            .qr-pill {
                font-size: 10px;
                padding: 6px 10px;
            }

            .menu-grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }

            .menu-categories {
                gap: 10px;
            }

            .category-btn {
                padding: 10px 18px;
                font-size: 12px;
            }

            .qr-section {
                flex-direction: column;
                text-align: center;
            }

            .qr-text {
                text-align: center;
            }

            .menu-header {
                margin-bottom: 30px;
            }

            .menu-title {
                font-size: 28px;
            }

            .section-title {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/loader.php'; ?>
    
    <?php include 'includes/header.php'; ?>
    
    <!-- Mobile Menu Overlay -->
    <div class="mobile-menu-overlay" role="presentation"></div>

    <!-- Restaurant Hero -->
    <section class="restaurant-hero">
        <div class="container">
            <div class="restaurant-hero-content">
                <div class="restaurant-hero-layout">
                    <div class="hero-copy">
                        <span class="hero-eyebrow"><i class="fas fa-gem"></i> Dining & Drinks</span>
                        <h1>Our Restaurant</h1>
                        <p>Fine Dining Experience</p>
                        <div class="restaurant-hero-actions">
                            <div class="menu-type-selector">
                                <?php foreach ($valid_menus as $menu): ?>
                                    <a href="restaurant.php?menu=<?php echo urlencode($menu); ?>" 
                                       class="menu-type-btn <?php echo $menu === $menu_type ? 'active' : ''; ?>">
                                        <i class="fas <?php echo $menu_labels[$menu]['icon']; ?>"></i>
                                        <?php echo $menu_labels[$menu]['label']; ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <div class="hero-qr-card">
                        <div class="qr-section">
                            <div class="qr-code">
                                <img src="<?php echo htmlspecialchars($qr_code_url); ?>" alt="QR Code" />
                            </div>
                            <div class="qr-text">
                                <h4>Access on Mobile</h4>
                                <p>Scan to open the <?php echo $menu_labels[$menu_type]['label']; ?> instantly.</p>
                                <div class="qr-pill"><i class="fas <?php echo $menu_labels[$menu_type]['icon']; ?>"></i> Now viewing: <?php echo $menu_labels[$menu_type]['label']; ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Menu Section -->
    <section class="menu-container">
        <div class="container">
            <!-- Menu Header with QR Code -->
            <div class="menu-header">
                <div class="menu-header-text">
                    <h2 class="menu-title">
                        <i class="fas <?php echo $menu_labels[$menu_type]['icon']; ?>"></i>
                        <?php echo $menu_labels[$menu_type]['label']; ?>
                    </h2>
                    <p class="menu-kicker">Curated selections for <?php echo $menu_labels[$menu_type]['label']; ?>.</p>
                </div>
            </div>

            <!-- Category Buttons (if multiple categories exist) -->
            <?php if (count($menu_items) > 1): ?>
            <div class="menu-categories">
                <?php $first = true; foreach ($menu_items as $category => $items): ?>
                    <button class="category-btn <?php echo $first ? 'active' : ''; ?>" 
                            data-category="<?php echo htmlspecialchars($category); ?>">
                        <?php echo htmlspecialchars($category); ?>
                    </button>
                    <?php $first = false; endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Menu Items by Category -->
            <?php $first = true; foreach ($menu_items as $category => $items): ?>
            <div class="menu-section <?php echo $first ? 'active' : ''; ?>" 
                 data-category="<?php echo htmlspecialchars($category); ?>">
                <h3 class="section-title"><?php echo htmlspecialchars($category); ?></h3>
                <div class="menu-grid">
                    <?php foreach ($items as $index => $item): ?>
                    <div class="menu-item" style="animation-delay: <?php echo $index * 0.1; ?>s">
                        <div class="item-header">
                            <span class="item-name"><?php echo htmlspecialchars($item['name']); ?></span>
                            <span class="item-price"><?php echo htmlspecialchars($currency_symbol); ?><?php echo number_format($item['price'], 2); ?></span>
                        </div>
                        <p class="item-description"><?php echo htmlspecialchars($item['description']); ?></p>
                        <?php if (!empty($item['tags'])): ?>
                        <div class="item-tags">
                            <?php foreach (explode(',', $item['tags']) as $tag): ?>
                                <span class="tag"><?php echo htmlspecialchars(trim($tag)); ?></span>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php $first = false; endforeach; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer" id="contact">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-column">
                    <h4>About Us</h4>
                    <ul class="footer-links">
                        <li><a href="index.php">Home</a></li>
                        <li><a href="index.php#rooms">Our Rooms</a></li>
                        <li><a href="restaurant.php">Restaurant</a></li>
                        <li><a href="index.php#facilities">Facilities</a></li>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h4>Quick Links</h4>
                    <ul class="footer-links">
                        <li><a href="index.php#testimonials">Reviews</a></li>
                        <li><a href="index.php#contact">Contact</a></li>
                        <li><a href="#" class="policy-link" data-policy="booking-policy">Booking Policy</a></li>
                        <li><a href="#" class="policy-link" data-policy="cancellation-policy">Cancellation</a></li>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h4>Policies</h4>
                    <ul class="footer-links">
                        <li><a href="#" class="policy-link" data-policy="booking-policy">Booking Policy</a></li>
                        <li><a href="#" class="policy-link" data-policy="cancellation-policy">Cancellation</a></li>
                        <li><a href="#" class="policy-link" data-policy="dining-policy">Dining Policy</a></li>
                        <li><a href="#" class="policy-link" data-policy="faqs">FAQs</a></li>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h4>Contact Information</h4>
                    <ul class="contact-info">
                        <li>
                            <i class="fas fa-phone"></i>
                            <a href="tel:+265123456789">+265 123 456 789</a>
                        </li>
                        <li>
                            <i class="fas fa-envelope"></i>
                            <a href="mailto:info@liwondesunhotel.com">info@liwondesunhotel.com</a>
                        </li>
                        <li>
                            <i class="fas fa-map-marker-alt"></i>
                            <a href="https://www.google.com/maps/search/Liwonde+Malawi" target="_blank">Liwonde, Malawi</a>
                        </li>
                        <li>
                            <i class="fas fa-clock"></i>
                            <span>24/7 Available</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2026 Liwonde Sun Hotel. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <?php if (!empty($policies)): ?>
    <div class="policy-overlay" data-policy-overlay></div>
    <div class="policy-modals">
        <?php foreach ($policies as $policy): ?>
        <div class="policy-modal" data-policy-modal="<?php echo htmlspecialchars($policy['slug']); ?>">
            <div class="policy-modal__content">
                <button class="policy-modal__close" aria-label="Close policy modal" data-policy-close>&times;</button>
                <div class="policy-modal__header">
                    <span class="policy-pill">Policy</span>
                    <h3><?php echo htmlspecialchars($policy['title']); ?></h3>
                    <?php if (!empty($policy['summary'])): ?>
                    <p class="policy-summary"><?php echo htmlspecialchars($policy['summary']); ?></p>
                    <?php endif; ?>
                </div>
                <div class="policy-modal__body">
                    <p><?php echo nl2br(htmlspecialchars($policy['content'])); ?></p>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <script src="js/main.js"></script>
    <script>
        // Menu category filtering
        document.querySelectorAll('.category-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const category = this.dataset.category;
                
                // Update active button
                document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                // Update active section
                document.querySelectorAll('.menu-section').forEach(section => {
                    section.classList.remove('active');
                });
                document.querySelector(`.menu-section[data-category="${category}"]`).classList.add('active');
            });
        });
    </script>
</body>
</html>
