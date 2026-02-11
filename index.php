<?php
// Load security configuration first
require_once 'config/security.php';

require_once 'config/database.php';
require_once 'includes/reviews-display.php';
require_once 'includes/video-display.php';
require_once 'includes/section-headers.php';

// Send security headers
sendSecurityHeaders();

// Helper: resolve image URL (supports relative and absolute URLs)
function resolveImageUrl($path) {
    if (!$path) return '';
    $trimmed = trim($path);
    if (stripos($trimmed, 'http://') === 0 || stripos($trimmed, 'https://') === 0) {
        return $trimmed; // external URL
    }
    return $trimmed; // relative path as-is
}

// Fetch site settings (cached)
$hero_title = getSetting('hero_title');
$hero_subtitle = getSetting('hero_subtitle');
$site_name = getSetting('site_name');
$site_logo = getSetting('site_logo');
$currency_symbol = getSetting('currency_symbol');
$currency_code = getSetting('currency_code');

// Fetch cached data for performance
$policies = getCachedPolicies();
$hero_slides = getCachedHeroSlides();
$featured_rooms = getCachedRooms(['is_featured' => true, 'limit' => 6]);
$facilities = getCachedFacilities(['is_featured' => true, 'limit' => 6]);
$gallery_images = getCachedGalleryImages();
$testimonials = getCachedTestimonials(3);

// Fetch cached About Us content
$about_data = getCachedAboutUs();
$about_content = $about_data['content'];
$about_features = $about_data['features'];
$about_stats = $about_data['stats'];

// Fetch hotel-wide reviews (with caching)
$hotel_reviews = [];
$review_averages = [];
try {
    // Try to get from cache first
    $reviews_cache = getCache('hotel_reviews_6', null);
    
    if ($reviews_cache !== null) {
        $hotel_reviews = $reviews_cache['reviews'];
        $review_averages = $reviews_cache['averages'];
    } else {
        // Fetch from database if not cached
        $reviews_data = fetchReviews(null, 'approved', 6, 0);
        
        if (isset($reviews_data['data'])) {
            $hotel_reviews = $reviews_data['data']['reviews'] ?? [];
            $review_averages = $reviews_data['data']['averages'] ?? [];
        } else {
            $hotel_reviews = $reviews_data['reviews'] ?? [];
            $review_averages = $reviews_data['averages'] ?? [];
        }
        
        // Cache for 30 minutes
        setCache('hotel_reviews_6', [
            'reviews' => $hotel_reviews,
            'averages' => $review_averages
        ], 1800);
    }
} catch (Exception $e) {
    error_log("Error fetching hotel reviews: " . $e->getMessage());
    $hotel_reviews = [];
    $review_averages = [];
}

// Fetch contact settings (cached)
$contact_settings = getSettingsByGroup('contact');
$contact = [];
foreach ($contact_settings as $setting) {
    $contact[$setting['setting_key']] = $setting['setting_value'];
}

// Fetch social media links (cached)
$social_settings = getSettingsByGroup('social');
$social = [];
foreach ($social_settings as $setting) {
    $social[$setting['setting_key']] = $setting['setting_value'];
}

// Fetch footer links (cached)
$footer_links_raw = getCache('footer_links', null);
if ($footer_links_raw === null) {
    try {
        $stmt = $pdo->query("
            SELECT column_name, link_text, link_url 
            FROM footer_links 
            WHERE is_active = 1 
            ORDER BY column_name, display_order
        ");
        $footer_links_raw = $stmt->fetchAll();
        setCache('footer_links', $footer_links_raw, 3600);
    } catch (PDOException $e) {
        $footer_links_raw = [];
    }
}

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
    <meta name="theme-color" content="#1A1A1A">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="format-detection" content="telephone=yes">
    <title><?php echo htmlspecialchars($site_name); ?> - Luxury Hotel | Premium Accommodation</title>
    <meta name="description" content="<?php echo htmlspecialchars($hero_subtitle); ?>. Book your stay at our premier luxury hotel featuring world-class dining, spa, and breathtaking views.">
    <meta name="keywords" content="<?php echo htmlspecialchars(getSetting('default_keywords', 'luxury hotel, premium accommodation, resort')); ?>">
    <meta name="author" content="<?php echo htmlspecialchars($site_name); ?>">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://<?php echo $_SERVER['HTTP_HOST']; ?>/">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://<?php echo $_SERVER['HTTP_HOST']; ?>/">
    <meta property="og:title" content="<?php echo htmlspecialchars($site_name); ?> - Luxury Hotel">
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
    <?php if (!empty($hero_slides[0]['image_path'])): ?>
    <link rel="preload" as="image" href="<?php echo htmlspecialchars($hero_slides[0]['image_path']); ?>" fetchpriority="high">
    <?php endif; ?>
    
    <!-- Session Handler -->
    <script src="js/session-handler.js" defer></script>
    
    <!-- Modal Component -->
    <script src="js/modal.js" defer></script>
    
    <!-- JavaScript -->
    <script src="js/main.js" defer></script>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;1,300;1,400;1,500&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" media="print" onload="this.media='all'">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/theme-dynamic.php">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/footer.css">
    
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
        "addressLocality": "<?php echo htmlspecialchars($contact['address_line2'] ?? ''); ?>",
        "addressRegion": "<?php echo htmlspecialchars($contact['address_region'] ?? ''); ?>",
        "addressCountry": "<?php echo htmlspecialchars($contact['address_country'] ?? ''); ?>"
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

    <!-- Passalacqua-Inspired Hero Section: Editorial, Full-Bleed, Minimal -->
    <section class="hero editorial-hero" id="home">
        <?php $slide = $hero_slides[0] ?? null; ?>
        <?php if ($slide): ?>
        <div class="editorial-hero-bg" style="background-image: url('<?php echo htmlspecialchars($slide['image_path']); ?>');"></div>
        <div class="editorial-hero-overlay"></div>
        <div class="editorial-hero-content container">
            <span class="editorial-hero-eyebrow"><?php echo htmlspecialchars($slide['subtitle']); ?></span>
            <h1 class="editorial-hero-title"><?php echo htmlspecialchars($slide['title']); ?></h1>
            <div class="editorial-hero-divider"></div>
            <p class="editorial-hero-description"><?php echo htmlspecialchars($slide['description']); ?></p>
            <div class="editorial-hero-cta">
                <?php if (!empty($slide['primary_cta_text'])): ?>
                    <a href="<?php echo htmlspecialchars($slide['primary_cta_link']); ?>" class="btn btn-primary"><?php echo htmlspecialchars($slide['primary_cta_text']); ?></a>
                <?php endif; ?>
                <?php if (!empty($slide['secondary_cta_text'])): ?>
                    <a href="<?php echo htmlspecialchars($slide['secondary_cta_link']); ?>" class="btn btn-outline"><?php echo htmlspecialchars($slide['secondary_cta_text']); ?></a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </section>


    <!-- Passalacqua-Inspired About Section: Editorial, Gold Divider, Left Image -->
    <section class="about-section editorial-about" id="about">
        <div class="container editorial-about-container">
            <div class="editorial-about-grid">
                <div class="editorial-about-image">
                    <?php if (!empty($about_content['image_url'])): ?>
                    <img src="<?php echo htmlspecialchars(resolveImageUrl($about_content['image_url'])); ?>" alt="<?php echo htmlspecialchars($site_name); ?> - Luxury Exterior" loading="lazy">
                    <?php else: ?>
                    <img src="images/hotel_gallery/Outside2.png" alt="<?php echo htmlspecialchars($site_name); ?> - Luxury Exterior" loading="lazy">
                    <?php endif; ?>
                </div>
                <div class="editorial-about-content">
                    <span class="editorial-about-eyebrow"><?php echo htmlspecialchars($about_content['subtitle'] ?? 'Our Story'); ?></span>
                    <h2 class="editorial-about-title"><?php echo htmlspecialchars($about_content['title'] ?? 'Experience Luxury Redefined'); ?></h2>
                    <div class="editorial-about-divider"></div>
                    <p class="editorial-about-description">
                        <?php echo htmlspecialchars($about_content['content'] ?? 'Nestled in the heart of Malawi, ' . htmlspecialchars($site_name) . ' offers an unparalleled luxury experience where timeless elegance meets modern comfort. For over two decades, we\'ve been creating unforgettable memories for discerning travelers from around the world.'); ?>
                    </p>
                    <div class="editorial-about-features">
                        <?php foreach (($about_features ?? []) as $feature): ?>
                        <div class="editorial-about-feature">
                            <?php if (!empty($feature['icon_class'])): ?>
                            <i class="<?php echo htmlspecialchars($feature['icon_class']); ?>"></i>
                            <?php endif; ?>
                            <span class="feature-title"><?php echo htmlspecialchars($feature['title']); ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="editorial-about-stats">
                        <?php foreach (($about_stats ?? []) as $stat): ?>
                        <div class="editorial-about-stat">
                            <span class="stat-number"><?php echo htmlspecialchars($stat['stat_number']); ?></span>
                            <span class="stat-label"><?php echo htmlspecialchars($stat['stat_label']); ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="editorial-about-cta">
                        <a href="#rooms" class="btn btn-primary">Explore Our Rooms</a>
                        <a href="#contact" class="btn btn-outline">Contact Us</a>
                    </div>
                </div>
            </div>
        </div>
    </section>


        <!-- Passalacqua-Inspired Rooms Section: Editorial, Borderless, Large Images -->
        <section class="editorial-rooms-section" id="rooms">
            <div class="container">
                <?php renderSectionHeader('home_rooms', 'index', [
                    'label' => 'Accommodations',
                    'title' => 'Luxurious Rooms & Suites',
                    'description' => 'Experience unmatched comfort in our meticulously designed rooms and suites'
                ]); ?>
                <div class="editorial-rooms-grid" id="roomsGrid" data-room-count="<?php echo count($featured_rooms); ?>">
                    <?php foreach ($featured_rooms as $room): 
                        $amenities = explode(',', $room['amenities']);
                        $amenities_display = array_slice($amenities, 0, 3);
                    ?>
                    <a href="room.php?room=<?php echo urlencode($room['slug']); ?>" class="editorial-room-card fade-in-up room-card-link">
                        <div class="editorial-room-image">
                            <img src="<?php echo htmlspecialchars(resolveImageUrl($room['image_url'])); ?>" alt="<?php echo htmlspecialchars($room['name']); ?> - Luxury accommodation at <?php echo htmlspecialchars($site_name); ?>" loading="lazy">
                            <?php if ($room['badge']): ?>
                            <div class="editorial-room-badge"><?php echo htmlspecialchars($room['badge']); ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="editorial-room-content">
                            <h3 class="editorial-room-name"><?php echo htmlspecialchars($room['name']); ?></h3>
                            <div class="editorial-room-divider"></div>
                            <div class="editorial-room-meta">
                                <span class="editorial-room-price"><?php echo htmlspecialchars($currency_symbol); ?><?php echo number_format($room['price_per_night'], 0); ?> <span class="meta-period">per night</span></span>
                                <span class="editorial-room-amenities">
                                    <?php foreach ($amenities_display as $amenity): ?>
                                    <span class="amenity-tag"><?php echo htmlspecialchars(trim($amenity)); ?></span>
                                    <?php endforeach; ?>
                                </span>
                            </div>
                            <p class="editorial-room-description"><?php echo htmlspecialchars($room['short_description']); ?></p>
                            <?php 
                                $available = $room['rooms_available'] ?? 0;
                                $total = $room['total_rooms'] ?? 0;
                                if ($total > 0):
                                    $availability_status = $available == 0 ? 'sold-out' : ($available <= 2 ? 'limited' : '');
                            ?>
                            <div class="editorial-room-availability <?php echo $availability_status; ?>">
                                <?php if ($available == 0): ?>
                                    <i class="fas fa-times-circle"></i> Sold Out
                                <?php elseif ($available <= 2): ?>
                                    <i class="fas fa-exclamation-triangle"></i> Only <?php echo $available; ?> left
                                <?php else: ?>
                                    <i class="fas fa-check-circle"></i> <?php echo $available; ?> rooms available
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                            <div class="btn btn-primary editorial-room-cta">View & Book</div>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>


        <!-- Passalacqua-Inspired Facilities Section: Editorial, Borderless, Large Icons -->
        <section class="editorial-facilities-section" id="facilities">
            <div class="container">
                <?php renderSectionHeader('home_facilities', 'index', [
                    'label' => 'Amenities',
                    'title' => 'World-Class Facilities',
                    'description' => 'Indulge in our premium facilities designed for your ultimate comfort'
                ]); ?>
                <div class="editorial-facilities-grid">
                    <?php foreach ($facilities as $facility): ?>
                        <div class="editorial-facility-card">
                            <div class="editorial-facility-icon">
                                <i class="<?php echo htmlspecialchars($facility['icon_class']); ?>"></i>
                            </div>
                            <div class="editorial-facility-content">
                                <h3 class="editorial-facility-name"><?php echo htmlspecialchars($facility['name']); ?></h3>
                                <div class="editorial-facility-divider"></div>
                                <p class="editorial-facility-description"><?php echo htmlspecialchars($facility['short_description']); ?></p>
                                <?php if (!empty($facility['page_url'])): ?>
                                    <a href="<?php echo htmlspecialchars($facility['page_url']); ?>" class="editorial-facility-link"><i class="fas fa-arrow-right"></i></a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

    <!-- Upcoming Events Section -->
    <?php 
    $upcoming_events_page = 'index';
    include 'includes/upcoming-events.php'; 
    ?>

    <!-- Hotel Gallery Carousel Section -->
    <?php include 'includes/hotel-gallery.php'; ?>

    <!-- Hotel Reviews Section -->
    <?php include 'includes/reviews-section.php'; ?>


        <!-- Passalacqua-Inspired Testimonials Section: Editorial, Borderless, Large Serif Quotes -->
        <section class="editorial-testimonials-section" id="testimonials">
            <div class="container">
                <?php renderSectionHeader('home_testimonials', 'index', [
                    'label' => 'Reviews',
                    'title' => 'What Our Guests Say',
                    'description' => 'Hear from those who have experienced our exceptional hospitality'
                ]); ?>
                <div class="editorial-testimonials-grid">
                    <?php foreach ($testimonials as $testimonial): ?>
                    <div class="editorial-testimonial-card">
                        <div class="editorial-testimonial-quote">“</div>
                        <p class="editorial-testimonial-text"><?php echo htmlspecialchars($testimonial['testimonial_text']); ?></p>
                        <div class="editorial-testimonial-divider"></div>
                        <div class="editorial-testimonial-author">
                            <span class="editorial-testimonial-author-name"><?php echo htmlspecialchars($testimonial['guest_name']); ?></span>
                            <span class="editorial-testimonial-author-location"><?php echo htmlspecialchars($testimonial['guest_location']); ?></span>
                            <span class="editorial-testimonial-rating">
                                <?php for ($i = 0; $i < $testimonial['rating']; $i++): ?>
                                    <i class="fas fa-star"></i>
                                <?php endfor; ?>
                            </span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

    </main>
    

        <!-- Passalacqua-Inspired Editorial Footer: Minimal, Borderless, Gold-Accented -->
        <footer class="editorial-footer">
            <div class="container">
                <div class="editorial-footer-content">
                    <div class="editorial-footer-logo">
                        <img src="images/logo/logo-footer.png" alt="Hotel Logo" height="48">
                    </div>
                    <nav class="editorial-footer-links">
                        <a href="/rooms-showcase.php">Rooms</a>
                        <a href="/restaurant.php">Restaurant</a>
                        <a href="/events.php">Events</a>
                        <a href="/gym.php">Gym</a>
                        <a href="/privacy-policy.php">Privacy Policy</a>
                    </nav>
                    <div class="editorial-footer-contact">
                        <div><?php echo htmlspecialchars($siteSettings['address']); ?></div>
                        <div><a href="tel:<?php echo htmlspecialchars($siteSettings['phone']); ?>"><?php echo htmlspecialchars($siteSettings['phone']); ?></a></div>
                        <div><a href="mailto:<?php echo htmlspecialchars($siteSettings['email']); ?>"><?php echo htmlspecialchars($siteSettings['email']); ?></a></div>
                    </div>
                </div>
                <div class="editorial-footer-divider"></div>
                <div class="editorial-footer-bottom">
                    <span>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($siteSettings['hotel_name']); ?>. All rights reserved.</span>
                </div>
            </div>
        </footer>
