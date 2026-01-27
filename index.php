<?php
require_once 'config/database.php';
require_once 'includes/reviews-display.php';

// Helper: resolve image URL (supports relative and absolute URLs)
function resolveImageUrl($path) {
    if (!$path) return '';
    $trimmed = trim($path);
    if (stripos($trimmed, 'http://') === 0 || stripos($trimmed, 'https://') === 0) {
        return $trimmed; // external URL
    }
    return $trimmed; // relative path as-is
}

// Fetch site settings
$hero_title = getSetting('hero_title');
$hero_subtitle = getSetting('hero_subtitle');
$site_name = getSetting('site_name');
$site_logo = getSetting('site_logo');
$currency_symbol = getSetting('currency_symbol');
$currency_code = getSetting('currency_code');

// Fetch policies for footer modals
$policies = [];
try {
    $policyStmt = $pdo->query("SELECT slug, title, summary, content FROM policies WHERE is_active = 1 ORDER BY display_order ASC, id ASC");
    $policies = $policyStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $policies = [];
}

// Fetch hero slides (database-driven carousel)
try {
    $stmt = $pdo->query("SELECT title, subtitle, description, primary_cta_text, primary_cta_link, secondary_cta_text, secondary_cta_link, image_path FROM hero_slides WHERE is_active = 1 ORDER BY display_order ASC");
    $hero_slides = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Error fetching hero slides: " . $e->getMessage());
    $hero_slides = [];
}


// Fetch featured rooms
$stmt = $pdo->query("
    SELECT * FROM rooms 
    WHERE is_featured = 1 AND is_active = 1 
    ORDER BY display_order ASC 
    LIMIT 6
");
$featured_rooms = $stmt->fetchAll();

// Fetch featured facilities
$stmt = $pdo->query("
    SELECT * FROM facilities 
    WHERE is_featured = 1 AND is_active = 1 
    ORDER BY display_order ASC 
    LIMIT 6
");
$facilities = $stmt->fetchAll();

// Fetch hotel gallery images
try {
    $stmt = $pdo->query("
        SELECT * FROM hotel_gallery 
        WHERE is_active = 1 
        ORDER BY display_order ASC
    ");
    $gallery_images = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Error fetching hotel gallery: " . $e->getMessage());
    $gallery_images = [];
}

// Fetch testimonials
$stmt = $pdo->query("
    SELECT * FROM testimonials
    WHERE is_featured = 1 AND is_approved = 1
    ORDER BY display_order ASC
    LIMIT 3
");
$testimonials = $stmt->fetchAll();

// Fetch hotel-wide reviews
$hotel_reviews = [];
$review_averages = [];
try {
    $reviews_data = fetchReviews(null, 'approved', 6, 0);
    $hotel_reviews = $reviews_data['reviews'] ?? [];
    $review_averages = $reviews_data['averages'] ?? [];
} catch (Exception $e) {
    error_log("Error fetching hotel reviews: " . $e->getMessage());
    $hotel_reviews = [];
    $review_averages = [];
}

// Fetch contact settings
$contact_settings = getSettingsByGroup('contact');
$contact = [];
foreach ($contact_settings as $setting) {
    $contact[$setting['setting_key']] = $setting['setting_value'];
}

// Fetch social media links
$social_settings = getSettingsByGroup('social');
$social = [];
foreach ($social_settings as $setting) {
    $social[$setting['setting_key']] = $setting['setting_value'];
}

// Fetch footer links grouped by column
$stmt = $pdo->query("
    SELECT column_name, link_text, link_url 
    FROM footer_links 
    WHERE is_active = 1 
    ORDER BY column_name, display_order
");
$footer_links_raw = $stmt->fetchAll();

// Group footer links by column
$footer_links = [];
foreach ($footer_links_raw as $link) {
    $footer_links[$link['column_name']][] = $link;
}

// Fetch About Us content from database
$about_content = [];
$about_features = [];
$about_stats = [];
try {
    // Get main about content
    $stmt = $pdo->query("SELECT * FROM about_us WHERE section_type = 'main' AND is_active = 1 ORDER BY display_order LIMIT 1");
    $about_content = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Get features
    $stmt = $pdo->query("SELECT * FROM about_us WHERE section_type = 'feature' AND is_active = 1 ORDER BY display_order");
    $about_features = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get stats
    $stmt = $pdo->query("SELECT * FROM about_us WHERE section_type = 'stat' AND is_active = 1 ORDER BY display_order");
    $about_stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching about us content: " . $e->getMessage());
    // Fallback to empty arrays
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
    <title><?php echo htmlspecialchars($site_name); ?> - Luxury Hotel in Malawi | Premium Accommodation</title>
    <meta name="description" content="<?php echo htmlspecialchars($hero_subtitle); ?>. Book your stay at Malawi's premier luxury hotel featuring world-class dining, spa, and breathtaking views.">
    <meta name="keywords" content="luxury hotel malawi, liwonde accommodation, premium resort, lake malawi hotel, 5-star hotel malawi">
    <meta name="author" content="<?php echo htmlspecialchars($site_name); ?>">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://<?php echo $_SERVER['HTTP_HOST']; ?>/">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://<?php echo $_SERVER['HTTP_HOST']; ?>/">
    <meta property="og:title" content="<?php echo htmlspecialchars($site_name); ?> - Luxury Hotel in Malawi">
    <meta property="og:description" content="<?php echo htmlspecialchars($hero_subtitle); ?>">
    <meta property="og:image" content="https://<?php echo $_SERVER['HTTP_HOST']; ?>/images/hero/slide1.jpg">
    
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="https://<?php echo $_SERVER['HTTP_HOST']; ?>/">
    <meta property="twitter:title" content="<?php echo htmlspecialchars($site_name); ?> - Luxury Hotel in Malawi">
    <meta property="twitter:description" content="<?php echo htmlspecialchars($hero_subtitle); ?>">
    <meta property="twitter:image" content="https://<?php echo $_SERVER['HTTP_HOST']; ?>/images/hero/slide1.jpg">
    
    <!-- Preload Critical Resources -->
    <link rel="preload" href="css/style.css" as="style">
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" as="style">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" media="print" onload="this.media='all'">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
    
    <!-- Structured Data - Local Business -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Hotel",
      "name": "<?php echo htmlspecialchars($site_name); ?>",
      "image": "https://<?php echo $_SERVER['HTTP_HOST']; ?>/images/hero/slide1.jpg",
      "description": "<?php echo htmlspecialchars($hero_subtitle); ?>",
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "<?php echo htmlspecialchars($contact['address_line1']); ?>",
        "addressLocality": "Liwonde",
        "addressRegion": "Southern Region",
        "addressCountry": "MW"
      },
      "telephone": "<?php echo htmlspecialchars($contact['phone_main']); ?>",
      "email": "<?php echo htmlspecialchars($contact['email_main']); ?>",
      "url": "https://<?php echo $_SERVER['HTTP_HOST']; ?>/",
      "starRating": {
        "@type": "Rating",
        "ratingValue": "5"
      },
      "priceRange": "$$$"
    }
    </script>
</head>
<body>
    <?php include 'includes/loader.php'; ?>
    
    <!-- Header & Navigation - Supreme Premium -->
    <?php include 'includes/header.php'; ?>

    <main>
    <!-- Hero Section: Luxury Carousel -->
    <section class="hero" id="home">
        <div class="hero-carousel">
            <?php foreach ($hero_slides as $index => $slide): ?>
            <div class="hero-slide <?php echo $index === 0 ? 'active' : ''; ?>" data-index="<?php echo $index; ?>" style="background-image: url('<?php echo htmlspecialchars($slide['image_path']); ?>');">
                <div class="hero-overlay"></div>
                <div class="hero-content fade-in-up">
                    <span class="hero-subtitle"><?php echo htmlspecialchars($slide['subtitle']); ?></span>
                    <h1 class="hero-title"><?php echo htmlspecialchars($slide['title']); ?></h1>
                    <p class="hero-description"><?php echo htmlspecialchars($slide['description']); ?></p>
                    
                    <div class="hero-cta">
                        <?php if (!empty($slide['primary_cta_text'])): ?>
                            <a href="<?php echo htmlspecialchars($slide['primary_cta_link']); ?>" class="btn btn-primary"><?php echo htmlspecialchars($slide['primary_cta_text']); ?></a>
                        <?php endif; ?>
                        <?php if (!empty($slide['secondary_cta_text'])): ?>
                            <a href="<?php echo htmlspecialchars($slide['secondary_cta_link']); ?>" class="btn btn-outline"><?php echo htmlspecialchars($slide['secondary_cta_text']); ?></a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="hero-controls">
            <button class="hero-nav hero-prev" aria-label="Previous slide"><i class="fas fa-bed"></i></button>
            <div class="hero-indicators">
                <?php foreach ($hero_slides as $index => $slide): ?>
                    <button class="hero-indicator <?php echo $index === 0 ? 'active' : ''; ?>" data-index="<?php echo $index; ?>" data-hotel="<?php echo htmlspecialchars($site_name); ?>" aria-label="Go to slide <?php echo $index + 1; ?>"></button>
                <?php endforeach; ?>
            </div>
            <button class="hero-nav hero-next" aria-label="Next slide"><i class="fas fa-utensils"></i></button>
        </div>

        <!-- Time and Weather Widget - Desktop -->
        <div class="hero-widget hero-widget-desktop">
            <div class="widget-time">
                <span class="time-display" id="heroTime">--:--</span>
                <span class="time-period" id="heroAmpm">AM</span>
            </div>
            <div class="widget-separator"></div>
            <div class="widget-weather">
                <i class="fas fa-sun"></i>
                <span class="temp-display" id="heroTemp">--°C</span>
            </div>
        </div>

        <!-- Time and Weather Widget - Mobile -->
        <div class="hero-widget-mobile">
            <span class="mobile-time" id="heroTimeMobile">--:--</span>
            <span class="mobile-period" id="heroAmpmMobile">AM</span>
            <span class="mobile-separator">|</span>
            <i class="fas fa-sun mobile-icon"></i>
            <span class="mobile-temp" id="heroTempMobile">--°C</span>
        </div>
    </section>

    <!-- About Us Section - Luxury Experience -->
    <section class="about-section" id="about">
        <div class="container about-container">
            <div class="about-grid">
                <div class="about-content">
                    <?php if (!empty($about_content)): ?>
                        <span class="about-eyebrow"><?php echo htmlspecialchars($about_content['subtitle'] ?? 'Our Story'); ?></span>
                        <h2 class="about-title"><?php echo htmlspecialchars($about_content['title'] ?? 'Experience Luxury Redefined'); ?></h2>
                        <p class="about-description">
                            <?php echo htmlspecialchars($about_content['content'] ?? 'Nestled in the heart of Malawi, Liwonde Sun Hotel offers an unparalleled luxury experience where timeless elegance meets modern comfort. For over two decades, we\'ve been creating unforgettable memories for discerning travelers from around the world.'); ?>
                        </p>
                    <?php else: ?>
                        <span class="about-eyebrow">Our Story</span>
                        <h2 class="about-title">Experience Luxury Redefined</h2>
                        <p class="about-description">
                            Nestled in the heart of Malawi, Liwonde Sun Hotel offers an unparalleled luxury experience 
                            where timeless elegance meets modern comfort. For over two decades, we've been creating 
                            unforgettable memories for discerning travelers from around the world.
                        </p>
                    <?php endif; ?>
                    
                    <?php if (!empty($about_features)): ?>
                    <div class="about-features">
                        <?php foreach ($about_features as $feature): ?>
                        <div class="about-feature">
                            <?php if (!empty($feature['icon_class'])): ?>
                            <div class="feature-icon">
                                <i class="<?php echo htmlspecialchars($feature['icon_class']); ?>"></i>
                            </div>
                            <?php endif; ?>
                            <div class="feature-content">
                                <?php if (!empty($feature['title'])): ?>
                                <h4><?php echo htmlspecialchars($feature['title']); ?></h4>
                                <?php endif; ?>
                                <?php if (!empty($feature['content'])): ?>
                                <p><?php echo htmlspecialchars($feature['content']); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="about-features">
                        <div class="about-feature">
                            <div class="feature-icon">
                                <i class="fas fa-award"></i>
                            </div>
                            <div class="feature-content">
                                <h4>Award-Winning Service</h4>
                                <p>Consistently recognized for exceptional hospitality and guest satisfaction</p>
                            </div>
                        </div>
                        <div class="about-feature">
                            <div class="feature-icon">
                                <i class="fas fa-leaf"></i>
                            </div>
                            <div class="feature-content">
                                <h4>Sustainable Luxury</h4>
                                <p>Committed to eco-friendly practices while maintaining premium standards</p>
                            </div>
                        </div>
                        <div class="about-feature">
                            <div class="feature-icon">
                                <i class="fas fa-heart"></i>
                            </div>
                            <div class="feature-content">
                                <h4>Personalized Care</h4>
                                <p>Tailored experiences designed around your unique preferences and needs</p>
                            </div>
                        </div>
                        <div class="about-feature">
                            <div class="feature-icon">
                                <i class="fas fa-star"></i>
                            </div>
                            <div class="feature-content">
                                <h4>5-Star Excellence</h4>
                                <p>Maintaining the highest standards of quality, comfort, and attention to detail</p>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($about_stats)): ?>
                    <div class="about-stats">
                        <?php foreach ($about_stats as $stat): ?>
                        <div class="stat-item">
                            <?php if (!empty($stat['stat_number'])): ?>
                            <span class="stat-number"><?php echo htmlspecialchars($stat['stat_number']); ?></span>
                            <?php endif; ?>
                            <?php if (!empty($stat['stat_label'])): ?>
                            <span class="stat-label"><?php echo htmlspecialchars($stat['stat_label']); ?></span>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="about-stats">
                        <div class="stat-item">
                            <span class="stat-number">25+</span>
                            <span class="stat-label">Years Excellence</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">98%</span>
                            <span class="stat-label">Guest Satisfaction</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">50+</span>
                            <span class="stat-label">Awards Won</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">10k+</span>
                            <span class="stat-label">Happy Guests</span>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="about-cta">
                        <a href="#rooms" class="btn btn-primary">Explore Our Rooms</a>
                        <a href="#contact" class="btn btn-outline">Contact Us</a>
                    </div>
                </div>
                
                <div class="about-image">
                    <?php if (!empty($about_content['image_url'])): ?>
                    <img src="<?php echo htmlspecialchars(resolveImageUrl($about_content['image_url'])); ?>" alt="Liwonde Sun Hotel - Luxury Exterior" loading="lazy">
                    <?php else: ?>
                    <img src="images/hotel_gallery/Outside2.png" alt="Liwonde Sun Hotel - Luxury Exterior" loading="lazy">
                    <?php endif; ?>
                </div>
                    </div>
            </div>
        </div>
    </section>

    <!-- Featured Rooms Section -->
    <section class="section" id="rooms" style="background: #FBF8F3;">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">Accommodations</span>
                <h2 class="section-title">Luxurious Rooms & Suites</h2>
                <p class="section-description">Experience unmatched comfort in our meticulously designed rooms and suites</p>
            </div>
            
            <div class="rooms-grid" id="roomsGrid" data-room-count="<?php echo count($featured_rooms); ?>">
                <?php foreach ($featured_rooms as $room): 
                    $amenities = explode(',', $room['amenities']);
                    $amenities_display = array_slice($amenities, 0, 3);
                ?>
                <a href="room.php?room=<?php echo urlencode($room['slug']); ?>" class="room-card fade-in-up room-card-link">
                    <div class="room-image">
                        <img src="<?php echo htmlspecialchars(resolveImageUrl($room['image_url'])); ?>" alt="<?php echo htmlspecialchars($room['name']); ?> - Luxury accommodation at <?php echo htmlspecialchars($site_name); ?>" loading="lazy">
                        <?php if ($room['badge']): ?>
                        <div class="room-badge"><?php echo htmlspecialchars($room['badge']); ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="room-content">
                        <div class="room-header">
                            <h3 class="room-name"><?php echo htmlspecialchars($room['name']); ?></h3>
                            <div class="room-price">
                                <div class="price-amount"><?php echo htmlspecialchars($currency_symbol); ?><?php echo number_format($room['price_per_night'], 0); ?></div>
                                <div class="price-period">per night</div>
                            </div>
                        </div>
                        
                        <p class="room-description"><?php echo htmlspecialchars($room['short_description']); ?></p>
                        
                        <div class="room-amenities">
                            <?php foreach ($amenities_display as $amenity): ?>
                            <span class="amenity-tag"><?php echo htmlspecialchars(trim($amenity)); ?></span>
                            <?php endforeach; ?>
                        </div>
                        
                        <?php 
                        $available = $room['rooms_available'] ?? 0;
                        $total = $room['total_rooms'] ?? 0;
                        if ($total > 0):
                            $availability_status = $available == 0 ? 'sold-out' : ($available <= 2 ? 'limited' : '');
                        ?>
                        <div class="room-availability <?php echo $availability_status; ?>">
                            <?php if ($available == 0): ?>
                                <i class="fas fa-times-circle"></i> Sold Out
                            <?php elseif ($available <= 2): ?>
                                <i class="fas fa-exclamation-triangle"></i> Only <?php echo $available; ?> left
                            <?php else: ?>
                                <i class="fas fa-check-circle"></i> <?php echo $available; ?> rooms available
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                        
                        <div class="btn btn-primary room-cta">View &amp; Book</div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Facilities Section -->
    <section class="section" id="facilities">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">Amenities</span>
                <h2 class="section-title">World-Class Facilities</h2>
                <p class="section-description">Indulge in our premium facilities designed for your ultimate comfort</p>
            </div>
            
            <div class="facilities-grid">
                <?php foreach ($facilities as $facility): ?>
                <?php if (!empty($facility['page_url'])): ?>
                    <a href="<?php echo htmlspecialchars($facility['page_url']); ?>" class="facility-card facility-card-link">
                        <div class="facility-icon">
                            <i class="<?php echo htmlspecialchars($facility['icon_class']); ?>"></i>
                        </div>
                        <h3 class="facility-name"><?php echo htmlspecialchars($facility['name']); ?></h3>
                        <p class="facility-description"><?php echo htmlspecialchars($facility['short_description']); ?></p>
                        <span class="facility-link-arrow"><i class="fas fa-arrow-right"></i></span>
                    </a>
                <?php else: ?>
                    <div class="facility-card">
                        <div class="facility-icon">
                            <i class="<?php echo htmlspecialchars($facility['icon_class']); ?>"></i>
                        </div>
                        <h3 class="facility-name"><?php echo htmlspecialchars($facility['name']); ?></h3>
                        <p class="facility-description"><?php echo htmlspecialchars($facility['short_description']); ?></p>
                    </div>
                <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Hotel Gallery Carousel Section -->
    <section class="section hotel-gallery-section" id="gallery">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">Visual Journey</span>
                <h2 class="section-title">Explore Our Hotel</h2>
                <p class="section-description">Immerse yourself in the beauty and luxury of Liwonde Sun Hotel</p>
            </div>
            
            <div class="gallery-carousel-wrapper">
                <button class="gallery-nav-btn gallery-nav-prev" aria-label="Previous">
                    <i class="fas fa-chevron-left"></i>
                </button>
                
                <div class="gallery-carousel-container">
                    <div class="gallery-carousel-track">
                        <?php foreach ($gallery_images as $index => $image): ?>
                        <div class="gallery-carousel-item" data-index="<?php echo $index; ?>">
                            <div class="gallery-item-inner">
                                <img src="<?php echo htmlspecialchars(resolveImageUrl($image['image_url'])); ?>" 
                                     alt="<?php echo htmlspecialchars($image['title']); ?>" 
                                     loading="lazy">
                                <div class="gallery-item-overlay">
                                    <div class="gallery-item-content">
                                        <h4><?php echo htmlspecialchars($image['title']); ?></h4>
                                        <?php if (!empty($image['description'])): ?>
                                        <p><?php echo htmlspecialchars($image['description']); ?></p>
                                        <?php endif; ?>
                                        <span class="gallery-category-badge">
                                            <i class="fas fa-tag"></i> <?php echo htmlspecialchars($image['category']); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <button class="gallery-nav-btn gallery-nav-next" aria-label="Next">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
            
            <div class="gallery-dots">
                <?php foreach ($gallery_images as $index => $image): ?>
                <button class="gallery-dot <?php echo $index === 0 ? 'active' : ''; ?>" 
                        data-index="<?php echo $index; ?>" 
                        aria-label="Go to image <?php echo $index + 1; ?>"></button>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="section" id="testimonials" style="background: #FBF8F3;">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">Reviews</span>
                <h2 class="section-title">What Our Guests Say</h2>
                <p class="section-description">Hear from those who have experienced our exceptional hospitality</p>
            </div>
            
            <div class="testimonials-grid">
                <?php foreach ($testimonials as $testimonial): ?>
                <div class="testimonial-card">
                    <div class="testimonial-quote">"</div>
                    <p class="testimonial-text"><?php echo htmlspecialchars($testimonial['testimonial_text']); ?></p>
                    
                    <div class="testimonial-author">
                        <div class="author-info">
                            <div class="author-name"><?php echo htmlspecialchars($testimonial['guest_name']); ?></div>
                            <div class="author-location"><?php echo htmlspecialchars($testimonial['guest_location']); ?></div>
                            <div class="testimonial-rating">
                                <?php for ($i = 0; $i < $testimonial['rating']; $i++): ?>
                                <i class="fas fa-star"></i>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Hotel Reviews Section -->
    <?php if (!empty($hotel_reviews) || !empty($review_averages)): ?>
    <section class="section hotel-reviews-section" id="reviews">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">Guest Reviews</span>
                <h2 class="section-title">What Our Guests Say</h2>
                <p class="section-description">Read authentic reviews from guests who have experienced our hospitality</p>
            </div>
            
            <?php if (!empty($review_averages)): ?>
            <!-- Rating Summary -->
            <div class="reviews-summary-wrapper">
                <div class="reviews-overall-rating">
                    <div class="overall-rating-score">
                        <span class="rating-number"><?php echo number_format($review_averages['avg_rating'] ?? 0, 1); ?></span>
                        <div class="rating-stars">
                            <?php echo displayStarRating($review_averages['avg_rating'] ?? 0, 24, true); ?>
                        </div>
                        <span class="rating-count"><?php echo ($review_averages['total_count'] ?? 0); ?> reviews</span>
                    </div>
                </div>
                
                <div class="reviews-category-breakdown">
                    <div class="category-rating-item">
                        <span class="category-label">Service</span>
                        <div class="category-rating-bar">
                            <div class="category-rating-fill" style="width: <?php echo ($review_averages['avg_service'] ?? 0) * 20; ?>%;"></div>
                        </div>
                        <span class="category-value"><?php echo number_format($review_averages['avg_service'] ?? 0, 1); ?></span>
                    </div>
                    <div class="category-rating-item">
                        <span class="category-label">Cleanliness</span>
                        <div class="category-rating-bar">
                            <div class="category-rating-fill" style="width: <?php echo ($review_averages['avg_cleanliness'] ?? 0) * 20; ?>%;"></div>
                        </div>
                        <span class="category-value"><?php echo number_format($review_averages['avg_cleanliness'] ?? 0, 1); ?></span>
                    </div>
                    <div class="category-rating-item">
                        <span class="category-label">Location</span>
                        <div class="category-rating-bar">
                            <div class="category-rating-fill" style="width: <?php echo ($review_averages['avg_location'] ?? 0) * 20; ?>%;"></div>
                        </div>
                        <span class="category-value"><?php echo number_format($review_averages['avg_location'] ?? 0, 1); ?></span>
                    </div>
                    <div class="category-rating-item">
                        <span class="category-label">Value</span>
                        <div class="category-rating-bar">
                            <div class="category-rating-fill" style="width: <?php echo ($review_averages['avg_value'] ?? 0) * 20; ?>%;"></div>
                        </div>
                        <span class="category-value"><?php echo number_format($review_averages['avg_value'] ?? 0, 1); ?></span>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($hotel_reviews)): ?>
            <!-- Reviews List -->
            <div class="hotel-reviews-list">
                <?php foreach ($hotel_reviews as $review): ?>
                <div class="hotel-review-card">
                    <div class="review-header">
                        <div class="review-guest-info">
                            <div class="guest-avatar">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="guest-details">
                                <span class="guest-name"><?php echo htmlspecialchars($review['guest_name']); ?></span>
                                <span class="review-date"><?php echo date('F j, Y', strtotime($review['created_at'])); ?></span>
                            </div>
                        </div>
                        <div class="review-rating">
                            <?php echo displayStarRating($review['rating'], 16, true); ?>
                        </div>
                    </div>
                    
                    <div class="review-content">
                        <p class="review-text"><?php echo htmlspecialchars($review['comment']); ?></p>
                    </div>
                    
                    <?php if (!empty($review['latest_response'])): ?>
                    <div class="review-admin-response">
                        <div class="admin-response-header">
                            <i class="fas fa-reply"></i>
                            <span>Response from <?php echo htmlspecialchars($site_name); ?></span>
                        </div>
                        <p class="admin-response-text"><?php echo htmlspecialchars($review['latest_response']); ?></p>
                        <?php if (!empty($review['latest_response_date'])): ?>
                        <span class="admin-response-date"><?php echo date('F j, Y', strtotime($review['latest_response_date'])); ?></span>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Reviews Actions -->
            <div class="reviews-actions">
                <a href="submit-review.php" class="btn btn-primary">
                    <i class="fas fa-pen"></i> Write a Review
                </a>
                <a href="admin/reviews.php" class="btn btn-outline">
                    <i class="fas fa-list"></i> View All Reviews
                </a>
            </div>
            <?php else: ?>
            <!-- No Reviews Message -->
            <div class="no-reviews-message">
                <i class="fas fa-star"></i>
                <p>No reviews yet. Be the first to share your experience!</p>
                <a href="submit-review.php" class="btn btn-primary">Write a Review</a>
            </div>
            <?php endif; ?>
        </div>
    </section>
    <?php endif; ?>

    </main>
    
    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- JavaScript -->
    <script src="js/main.js" defer></script>
    
    <!-- Scroll to Top Button -->
    <button id="scrollToTop" class="scroll-to-top" aria-label="Scroll to top">
        <img src="images/logo/logo.jpg" alt="<?php echo htmlspecialchars($site_name); ?> Logo" class="scroll-to-top-logo">
    </button>
</body>
</html>