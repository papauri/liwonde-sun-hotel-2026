<?php
require_once '../config/database.php';

// Fetch site settings
$site_name = getSetting('site_name', 'Liwonde Sun Hotel');
$site_logo = getSetting('site_logo', 'images/logo/logo.png');
$currency_symbol = getSetting('currency_symbol', 'K');
$currency_code = getSetting('currency_code', 'MWK');

// Fetch menu items by category
$menu_items = [];
try {
    $stmt = $pdo->query("SELECT * FROM restaurant_menu WHERE is_available = 1 ORDER BY category, display_order ASC, id ASC");
    $all_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Group by category
    foreach ($all_items as $item) {
        $menu_items[$item['category']][] = $item;
    }
} catch (PDOException $e) {
    error_log("Error fetching menu: " . $e->getMessage());
}

// Fetch restaurant gallery
$gallery_images = [];
try {
    $stmt = $pdo->query("SELECT * FROM restaurant_gallery WHERE is_active = 1 ORDER BY display_order ASC, id ASC");
    $gallery_images = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching gallery: " . $e->getMessage());
}

// Fetch policies for footer modals
$policies = [];
try {
    $policyStmt = $pdo->query("SELECT slug, title, summary, content FROM policies WHERE is_active = 1 ORDER BY display_order ASC, id ASC");
    $policies = $policyStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching policies: " . $e->getMessage());
}
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
    <title>Fine Dining Restaurant - <?php echo htmlspecialchars($site_name); ?> | Gourmet Cuisine in Malawi</title>
    <meta name="description" content="Experience exquisite fine dining at <?php echo htmlspecialchars($site_name); ?>. Fresh local cuisine, international dishes, craft cocktails, and premium bar service in an elegant setting.">
    <meta name="keywords" content="fine dining malawi, gourmet restaurant, lake malawi dining, luxury restaurant liwonde, international cuisine">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://<?php echo $_SERVER['HTTP_HOST']; ?>/pages/restaurant.php">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="restaurant">
    <meta property="og:url" content="https://<?php echo $_SERVER['HTTP_HOST']; ?>/pages/restaurant.php">
    <meta property="og:title" content="Fine Dining Restaurant - <?php echo htmlspecialchars($site_name); ?>">
    <meta property="og:description" content="Experience exquisite fine dining with fresh local cuisine, international dishes, and premium bar service.">
    <meta property="og:image" content="https://<?php echo $_SERVER['HTTP_HOST']; ?>/images/restaurant/hero.jpg">
    
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="https://<?php echo $_SERVER['HTTP_HOST']; ?>/pages/restaurant.php">
    <meta property="twitter:title" content="Fine Dining Restaurant - <?php echo htmlspecialchars($site_name); ?>">
    <meta property="twitter:description" content="Experience exquisite fine dining with fresh local cuisine, international dishes, and premium bar service.">
    <meta property="twitter:image" content="https://<?php echo $_SERVER['HTTP_HOST']; ?>/images/restaurant/hero.jpg">
    
    <!-- Preload Critical Resources -->
    <link rel="preload" href="../css/style.css" as="style">
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" as="style">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"></noscript>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></noscript>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/style.css">
    
    <!-- Structured Data - Restaurant Schema -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Restaurant",
      "name": "<?php echo htmlspecialchars($site_name); ?> Restaurant",
      "image": "https://<?php echo $_SERVER['HTTP_HOST']; ?>/images/restaurant/hero.jpg",
      "description": "Fine dining restaurant offering fresh local cuisine, international dishes, and premium bar service",
      "servesCuisine": ["International", "African", "Continental"],
      "priceRange": "$$$",
      "url": "https://<?php echo $_SERVER['HTTP_HOST']; ?>/pages/restaurant.php"
    }
    </script>
</head>
<body>
    <?php include '../includes/loader.php'; ?>
    
    <!-- Loading Animation -->
    <div class="page-loader">
        <div class="loader-content">
            <div class="luxury-spinner"></div>
            <p class="loader-text">Preparing Your Culinary Experience</p>
        </div>
    </div>
    
    <?php include '../includes/header.php'; ?>
    
    <!-- Mobile Menu Overlay -->
    <div class="mobile-menu-overlay" role="presentation"></div>

    <!-- Hero Section -->
    <section class="page-hero" style="background-image: url('../images/restaurant/hero-bg.jpg');">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <span class="hero-subtitle">Culinary Excellence</span>
            <h1 class="hero-title">Fine Dining Restaurant & Bar</h1>
            <p class="hero-description">Savor exceptional cuisine crafted from the finest local and international ingredients</p>
        </div>
    </section>

    <!-- Restaurant Gallery Grid -->
    <section class="restaurant-gallery section-padding">
        <div class="container">
            <div class="section-header text-center">
                <span class="section-label">Visual Journey</span>
                <h2 class="section-title">Our Dining Spaces</h2>
                <p class="section-description">From elegant interiors to breathtaking views, every detail creates the perfect ambiance</p>
            </div>

            <div class="gallery-grid">
                <?php if (!empty($gallery_images)): ?>
                    <?php foreach ($gallery_images as $index => $image): ?>
                        <div class="gallery-item" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                            <img src="../<?php echo htmlspecialchars($image['image_path']); ?>" alt="<?php echo htmlspecialchars($image['caption']); ?>" loading="lazy">
                            <div class="gallery-overlay">
                                <p class="gallery-caption"><?php echo htmlspecialchars($image['caption']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Fallback images if database is empty -->
                    <div class="gallery-item"><img src="../images/restaurant/dining-area-1.jpg" alt="Elegant Dining Area" loading="lazy"><div class="gallery-overlay"><p class="gallery-caption">Elegant Dining Area</p></div></div>
                    <div class="gallery-item"><img src="../images/restaurant/dining-area-2.jpg" alt="Intimate Indoor Seating" loading="lazy"><div class="gallery-overlay"><p class="gallery-caption">Intimate Indoor Seating</p></div></div>
                    <div class="gallery-item"><img src="../images/restaurant/bar-area.jpg" alt="Premium Bar" loading="lazy"><div class="gallery-overlay"><p class="gallery-caption">Premium Bar</p></div></div>
                    <div class="gallery-item"><img src="../images/restaurant/food-platter.jpg" alt="Fresh Seafood" loading="lazy"><div class="gallery-overlay"><p class="gallery-caption">Fresh Seafood</p></div></div>
                    <div class="gallery-item"><img src="../images/restaurant/fine-dining.jpg" alt="Fine Dining Experience" loading="lazy"><div class="gallery-overlay"><p class="gallery-caption">Fine Dining Experience</p></div></div>
                    <div class="gallery-item"><img src="../images/restaurant/outdoor-terrace.jpg" alt="Alfresco Terrace" loading="lazy"><div class="gallery-overlay"><p class="gallery-caption">Alfresco Terrace</p></div></div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Menu Section -->
    <section class="restaurant-menu section-padding bg-dark">
        <div class="container">
            <div class="section-header text-center">
                <span class="section-label">Culinary Delights</span>
                <h2 class="section-title">Our Menu</h2>
                <p class="section-description">Discover our carefully curated selection of dishes and beverages</p>
            </div>

            <!-- Menu Category Tabs -->
            <div class="menu-tabs">
                <?php if (!empty($menu_items)): ?>
                    <?php $first = true; foreach (array_keys($menu_items) as $category): ?>
                        <button class="menu-tab <?php echo $first ? 'active' : ''; ?>" data-category="<?php echo strtolower(str_replace(' ', '-', $category)); ?>">
                            <?php echo htmlspecialchars($category); ?>
                        </button>
                        <?php $first = false; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <button class="menu-tab active" data-category="breakfast">Breakfast</button>
                    <button class="menu-tab" data-category="lunch">Lunch</button>
                    <button class="menu-tab" data-category="dinner">Dinner</button>
                    <button class="menu-tab" data-category="drinks">Drinks</button>
                    <button class="menu-tab" data-category="bar">Bar</button>
                    <button class="menu-tab" data-category="desserts">Desserts</button>
                <?php endif; ?>
            </div>

            <!-- Menu Items -->
            <?php if (!empty($menu_items)): ?>
                <?php $first_cat = true; foreach ($menu_items as $category => $items): ?>
                    <div class="menu-category <?php echo $first_cat ? 'active' : ''; ?>" data-category="<?php echo strtolower(str_replace(' ', '-', $category)); ?>">
                        <div class="menu-items-grid">
                            <?php foreach ($items as $item): ?>
                                <div class="menu-item <?php echo $item['is_featured'] ? 'featured' : ''; ?>">
                                    <?php if ($item['is_featured']): ?>
                                        <span class="featured-badge"><i class="fas fa-star"></i> Chef's Special</span>
                                    <?php endif; ?>
                                    
                                    <div class="menu-item-header">
                                        <h3 class="menu-item-name"><?php echo htmlspecialchars($item['item_name']); ?></h3>
                                        <span class="menu-item-price"><?php echo $currency_symbol; ?><?php echo number_format($item['price'], 2); ?></span>
                                    </div>
                                    
                                    <?php if (!empty($item['description'])): ?>
                                        <p class="menu-item-description"><?php echo htmlspecialchars($item['description']); ?></p>
                                    <?php endif; ?>
                                    
                                    <div class="menu-item-tags">
                                        <?php if ($item['is_vegetarian']): ?><span class="tag tag-vegetarian"><i class="fas fa-leaf"></i> Vegetarian</span><?php endif; ?>
                                        <?php if ($item['is_vegan']): ?><span class="tag tag-vegan"><i class="fas fa-seedling"></i> Vegan</span><?php endif; ?>
                                        <?php if (!empty($item['allergens'])): ?><span class="tag tag-allergen"><i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($item['allergens']); ?></span><?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php $first_cat = false; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="menu-category active">
                    <p class="text-center" style="color: var(--light-gray); padding: 40px;">Menu items coming soon. Please contact our restaurant for current offerings.</p>
                </div>
            <?php endif; ?>

            <div class="menu-cta text-center">
                <a href="#book" class="btn btn-primary"><i class="fas fa-utensils"></i> Reserve a Table</a>
                <a href="#contact" class="btn btn-outline"><i class="fas fa-phone"></i> Call Restaurant</a>
            </div>
        </div>
    </section>

    <!-- Experience Section -->
    <section class="restaurant-experience section-padding">
        <div class="container">
            <div class="experience-grid">
                <div class="experience-item" data-aos="fade-up">
                    <div class="experience-icon"><i class="fas fa-utensils"></i></div>
                    <h3>Fine Dining</h3>
                    <p>Experience culinary artistry with our carefully crafted menu featuring local Malawian flavors and international cuisine</p>
                </div>
                <div class="experience-item" data-aos="fade-up" data-aos-delay="100">
                    <div class="experience-icon"><i class="fas fa-cocktail"></i></div>
                    <h3>Premium Bar</h3>
                    <p>Enjoy handcrafted cocktails, fine wines, and premium spirits in our elegant bar lounge</p>
                </div>
                <div class="experience-item" data-aos="fade-up" data-aos-delay="200">
                    <div class="experience-icon"><i class="fas fa-fish"></i></div>
                    <h3>Fresh Local Ingredients</h3>
                    <p>We source the freshest chambo from Lake Malawi and seasonal produce from local farms</p>
                </div>
                <div class="experience-item" data-aos="fade-up" data-aos-delay="300">
                    <div class="experience-icon"><i class="fas fa-sun"></i></div>
                    <h3>Alfresco Dining</h3>
                    <p>Dine under the stars on our terrace with breathtaking views of the surrounding landscape</p>
                </div>
            </div>
        </div>
    </section>

    <?php include '../includes/footer.php'; ?>

    <!-- Scripts -->
    <script src="../js/main.js"></script>
    <script>
        // Menu tab switching
        document.querySelectorAll('.menu-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                const category = this.getAttribute('data-category');
                
                // Update active tab
                document.querySelectorAll('.menu-tab').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                
                // Update active category
                document.querySelectorAll('.menu-category').forEach(c => c.classList.remove('active'));
                document.querySelector(`.menu-category[data-category="${category}"]`).classList.add('active');
            });
        });

        // Page loader
        window.addEventListener('load', function() {
            document.querySelector('.page-loader').classList.add('fade-out');
            setTimeout(() => {
                document.querySelector('.page-loader').style.display = 'none';
            }, 500);
        });
    </script>
</body>
</html>
