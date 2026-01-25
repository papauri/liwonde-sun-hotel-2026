<?php
require_once 'config/database.php';

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
                <a href="/pages/room.php?room=<?php echo urlencode($room['slug']); ?>" class="room-card fade-in-up room-card-link">
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

    </main>
    <!-- Footer -->
    <footer class="footer" id="contact">
        <div class="container">
            <div class="footer-grid">
                <?php foreach ($footer_links as $column_name => $links): ?>
                <div class="footer-column">
                    <h4><?php echo htmlspecialchars($column_name); ?></h4>
                    <ul class="footer-links">
                        <?php foreach ($links as $link): ?>
                        <li><a href="<?php echo htmlspecialchars($link['link_url']); ?>"><?php echo htmlspecialchars($link['link_text']); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endforeach; ?>

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
                            <a href="tel:<?php echo htmlspecialchars(preg_replace('/[^0-9+]/', '', $contact['phone_main'])); ?>"><?php echo htmlspecialchars($contact['phone_main']); ?></a>
                        </li>
                        <li>
                            <i class="fas fa-envelope"></i>
                            <a href="mailto:<?php echo htmlspecialchars($contact['email_main']); ?>"><?php echo htmlspecialchars($contact['email_main']); ?></a>
                        </li>
                        <li>
                            <i class="fas fa-map-marker-alt"></i>
                            <a href="https://www.google.com/maps/search/<?php echo urlencode(htmlspecialchars($contact['address_line1'])); ?>" target="_blank"><?php echo htmlspecialchars($contact['address_line1']); ?></a>
                        </li>
                        <li>
                            <i class="fas fa-clock"></i>
                            <span><?php echo htmlspecialchars($contact['working_hours']); ?></span>
                        </li>
                    </ul>
                    
                    <div class="social-links">
                        <?php if (!empty($social['facebook_url'])): ?>
                        <a href="<?php echo htmlspecialchars($social['facebook_url']); ?>" class="social-icon" target="_blank">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <?php endif; ?>
                        
                        <?php if (!empty($social['instagram_url'])): ?>
                        <a href="<?php echo htmlspecialchars($social['instagram_url']); ?>" class="social-icon" target="_blank">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <?php endif; ?>
                        
                        <?php if (!empty($social['twitter_url'])): ?>
                        <a href="<?php echo htmlspecialchars($social['twitter_url']); ?>" class="social-icon" target="_blank">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <?php endif; ?>
                        
                        <?php if (!empty($social['linkedin_url'])): ?>
                        <a href="<?php echo htmlspecialchars($social['linkedin_url']); ?>" class="social-icon" target="_blank">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?php echo htmlspecialchars(getSetting('copyright_text')); ?></p>
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

    <!-- JavaScript -->
    <script src="js/main.js" defer></script>
    
    <!-- Scroll to Top Button -->
    <button id="scrollToTop" class="scroll-to-top" aria-label="Scroll to top">
        <img src="images/logo/logo.jpg" alt="<?php echo htmlspecialchars($site_name); ?> Logo" class="scroll-to-top-logo">
    </button>
</body>
</html>