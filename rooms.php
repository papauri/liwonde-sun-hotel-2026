<?php
require_once 'config/database.php';

// Core settings
$site_name = getSetting('site_name', 'Liwonde Sun Hotel');
$site_logo = getSetting('site_logo', 'images/logo/logo.png');
$site_tagline = getSetting('site_tagline', 'Where Luxury Meets Nature');
$currency_symbol = getSetting('currency_symbol', 'K');
$email_reservations = getSetting('email_reservations', 'book@liwondesunhotel.com');
$phone_main = getSetting('phone_main', '+265 123 456 789');

// Policies for modals
$policies = [];
try {
    $policyStmt = $pdo->query("SELECT slug, title, summary, content FROM policies WHERE is_active = 1 ORDER BY display_order ASC, id ASC");
    $policies = $policyStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $policies = [];
}

// Rooms collection
$rooms = [];
try {
    $roomStmt = $pdo->query("SELECT * FROM rooms WHERE is_active = 1 ORDER BY is_featured DESC, display_order ASC, id ASC");
    $rooms = $roomStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $rooms = [];
}

$hero_room = $rooms[0] ?? [
    'name' => 'Signature Suites',
    'short_description' => 'Elevated riverfront stays crafted for discerning guests',
    'description' => 'Discover contemporary suites with panoramic views, elevated amenities, and seamless technology for effortless stays.',
    'price_per_night' => 320,
    'image_url' => 'images/hero/slide1.jpg',
    'badge' => 'Featured',
    'size_sqm' => 60,
    'max_guests' => 3,
    'bed_type' => 'King Bed',
];

$active_slug = isset($_GET['room']) ? $_GET['room'] : ($rooms[0]['slug'] ?? null);
if ($active_slug) {
    foreach ($rooms as $room) {
        if ($room['slug'] === $active_slug) {
            $hero_room = $room;
            break;
        }
    }
}

// Contact settings
$contact_settings = getSettingsByGroup('contact');
$contact = [];
foreach ($contact_settings as $setting) {
    $contact[$setting['setting_key']] = $setting['setting_value'];
}

// Social settings
$social_settings = getSettingsByGroup('social');
$social = [];
foreach ($social_settings as $setting) {
    $social[$setting['setting_key']] = $setting['setting_value'];
}

// Footer links
$footer_links_raw = [];
try {
    $footerStmt = $pdo->query("SELECT column_name, link_text, link_url FROM footer_links WHERE is_active = 1 ORDER BY column_name, display_order");
    $footer_links_raw = $footerStmt->fetchAll();
} catch (PDOException $e) {
    $footer_links_raw = [];
}

$footer_links = [];
foreach ($footer_links_raw as $link) {
    $footer_links[$link['column_name']][] = $link;
}

// Extract unique badges for filters
$badges = ['All Rooms'];
$badge_counts = [];
foreach ($rooms as $room) {
    if (!empty($room['badge'])) {
        if (!in_array($room['badge'], $badges)) {
            $badges[] = $room['badge'];
        }
        $badge_counts[$room['badge']] = isset($badge_counts[$room['badge']]) ? $badge_counts[$room['badge']] + 1 : 1;
    }
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
    <title><?php echo htmlspecialchars($site_name); ?> | Rooms & Suites</title>
    <meta name="description" content="Explore contemporary rooms and suites at <?php echo htmlspecialchars($site_name); ?> with seamless booking integration.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="rooms-page">
    <?php include 'includes/loader.php'; ?>
    
    <?php include 'includes/header.php'; ?>
    
    <!-- Mobile Menu Overlay -->
    <div class="mobile-menu-overlay" role="presentation"></div>

    <section class="rooms-hero" style="background-image: linear-gradient(135deg, rgba(5, 9, 15, 0.76), rgba(10, 25, 41, 0.75)), url('<?php echo htmlspecialchars($hero_room['image_url']); ?>');">
        <div class="container">
            <div class="rooms-hero__content">
                <div class="pill">Riverfront Luxury</div>
                <h1><?php echo htmlspecialchars($hero_room['name']); ?></h1>
                <p><?php echo htmlspecialchars($hero_room['short_description'] ?? $site_tagline); ?></p>
                <div class="rooms-hero__meta">
                    <span><i class="fas fa-user-friends"></i> Up to <?php echo htmlspecialchars($hero_room['max_guests'] ?? 2); ?> Guests</span>
                    <span><i class="fas fa-ruler-combined"></i> <?php echo htmlspecialchars($hero_room['size_sqm'] ?? 40); ?> sqm</span>
                    <span><i class="fas fa-bed"></i> <?php echo htmlspecialchars($hero_room['bed_type'] ?? 'King Bed'); ?></span>
                    <span><i class="fas fa-tag"></i> <?php echo htmlspecialchars($currency_symbol); ?><?php echo number_format($hero_room['price_per_night'], 0); ?>/night</span>
                </div>
                <div class="rooms-hero__actions">
                    <a class="btn btn-primary" href="#book" data-room-slug="<?php echo htmlspecialchars($hero_room['slug'] ?? ''); ?>">Book This Room</a>
                    <a class="btn btn-outline" href="#collection">View All Rooms</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Room Images Grid Section -->
    <section class="section" style="padding-top: 40px; padding-bottom: 40px;">
        <div class="container">
            <!-- Desktop: Grid Layout - Mobile: Stack Layout -->
            <?php
            // Fetch gallery images for hero room
            $hero_gallery = [];
            try {
                $stmt = $pdo->prepare("SELECT title, image_url FROM gallery WHERE room_id = ? AND is_active = 1 AND image_url IS NOT NULL AND image_url != '' ORDER BY display_order ASC, id ASC LIMIT 4");
                $stmt->execute([$hero_room['id']]);
                $hero_gallery = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Fallback to featured image if no gallery images
                if (empty($hero_gallery) && !empty($hero_room['image_url'])) {
                    $hero_gallery = [['image_url' => $hero_room['image_url'], 'title' => $hero_room['name']]];
                }
            } catch (PDOException $e) {
                $hero_gallery = [['image_url' => $hero_room['image_url'], 'title' => $hero_room['name']]];
            }
            ?>
            <?php if (!empty($hero_gallery)): ?>
            <div class="room-gallery-grid" style="margin-bottom: 40px;">
                <?php foreach ($hero_gallery as $img): ?>
                <div class="gallery-item">
                    <img src="<?php echo htmlspecialchars($img['image_url']); ?>" alt="<?php echo htmlspecialchars($img['title'] ?? $hero_room['name']); ?>">
                    <div class="gallery-item-label"><?php echo htmlspecialchars($img['title'] ?? $hero_room['name']); ?></div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <div class="room-detail-info">
                <h2 class="room-detail-title"><?php echo htmlspecialchars($hero_room['name']); ?></h2>
                <p class="room-detail-description"><?php echo htmlspecialchars($hero_room['description'] ?? $hero_room['short_description']); ?></p>
                
                <div class="room-detail-specs">
                    <div class="spec-item">
                        <i class="fas fa-users"></i>
                        <div class="spec-label">Guests</div>
                        <div class="spec-value">Up to <?php echo htmlspecialchars($hero_room['max_guests'] ?? 2); ?></div>
                    </div>
                    <div class="spec-item">
                        <i class="fas fa-ruler-combined"></i>
                        <div class="spec-label">Floor Space</div>
                        <div class="spec-value"><?php echo htmlspecialchars($hero_room['size_sqm'] ?? 40); ?> sqm</div>
                    </div>
                    <div class="spec-item">
                        <i class="fas fa-bed"></i>
                        <div class="spec-label">Bed Type</div>
                        <div class="spec-value"><?php echo htmlspecialchars($hero_room['bed_type'] ?? 'King'); ?></div>
                    </div>
                    <div class="spec-item">
                        <i class="fas fa-tag"></i>
                        <div class="spec-label">Nightly Rate</div>
                        <div class="spec-value"><?php echo htmlspecialchars($currency_symbol); ?><?php echo number_format($hero_room['price_per_night'], 0); ?></div>
                    </div>
                </div>

                <div class="amenities-list">
                    <h3 style="font-size: 18px; color: var(--navy); margin-bottom: 14px; font-weight: 700;">Room Amenities</h3>
                    <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                        <?php 
                        $amenities = explode(',', $hero_room['amenities'] ?? '');
                        foreach ($amenities as $amenity): 
                        ?>
                        <span style="background: linear-gradient(135deg, rgba(212, 175, 55, 0.15) 0%, rgba(212, 175, 55, 0.08) 100%); color: var(--navy); padding: 10px 16px; border-radius: 12px; font-size: 14px; font-weight: 600; border: 1px solid rgba(212, 175, 55, 0.25);">
                            <i class="fas fa-check" style="color: var(--gold); margin-right: 6px;"></i><?php echo htmlspecialchars(trim($amenity)); ?>
                        </span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Modern Image Gallery Grid -->
            <div class="room-gallery-grid">
                <?php
                // Fetch gallery images for this room from database
                try {
                    $stmt = $pdo->prepare("
                        SELECT title, description, image_url 
                        FROM gallery 
                        WHERE room_id = ? AND category = 'rooms' AND is_active = 1
                        ORDER BY display_order ASC, created_at ASC
                        LIMIT 4
                    ");
                    $stmt->execute([$hero_room['id']]);
                    $gallery_images = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    // If we have gallery images, show them
                    if (!empty($gallery_images)) {
                        foreach ($gallery_images as $gallery_img) {
                            ?>
                            <div class="gallery-item">
                                <img src="<?php echo htmlspecialchars($gallery_img['image_url']); ?>" alt="<?php echo htmlspecialchars($gallery_img['title']); ?>">
                                <div class="gallery-item-label"><?php echo htmlspecialchars($gallery_img['title']); ?></div>
                            </div>
                            <?php
                        }
                    } else {
                        // Fallback to hero image if no gallery images
                        ?>
                        <div class="gallery-item">
                            <img src="<?php echo htmlspecialchars($hero_room['image_url']); ?>" alt="<?php echo htmlspecialchars($hero_room['name']); ?> - Main View">
                            <div class="gallery-item-label">Main View</div>
                        </div>
                        <?php
                    }
                } catch (PDOException $e) {
                    // Fallback on error
                    ?>
                    <div class="gallery-item">
                        <img src="<?php echo htmlspecialchars($hero_room['image_url']); ?>" alt="<?php echo htmlspecialchars($hero_room['name']); ?> - Main View">
                        <div class="gallery-item-label">Main View</div>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
    </section>

    <main>
        <section class="section" id="collection">
            <div class="container">
                <div class="section-header">
                    <span class="section-subtitle">Stay Collection</span>
                    <h2 class="section-title">Pick Your Perfect Space</h2>
                    <p class="section-description">Suites and rooms crafted for business, romance, and family stays with direct booking flows.</p>
                </div>

                <div class="rooms-filter">
                    <?php foreach ($badges as $badge): ?>
                    <span class="chip <?php echo $badge === 'All Rooms' ? 'active' : ''; ?>" data-filter="<?php echo htmlspecialchars(strtolower(str_replace(' ', '-', $badge))); ?>">
                        <?php echo htmlspecialchars($badge); ?>
                    </span>
                    <?php endforeach; ?>
                </div>

                <div class="rooms-grid modern-grid fancy-3d-grid">
                    <?php foreach ($rooms as $room): 
                        $amenities = array_slice(explode(',', $room['amenities']), 0, 4);
                    ?>
                    <article class="room-tile fancy-3d-card" tabindex="0" data-room-id="<?php echo (int)$room['id']; ?>" data-room-slug="<?php echo htmlspecialchars($room['slug']); ?>" data-badge="<?php echo htmlspecialchars(strtolower(str_replace(' ', '-', $room['badge'] ?? 'all-rooms'))); ?>" data-filter="all-rooms <?php echo htmlspecialchars(strtolower(str_replace(' ', '-', $room['badge'] ?? ''))); ?>">
                        <div class="room-tile__3d-bg"></div>
                        <a class="room-tile__image" href="rooms.php?room=<?php echo urlencode($room['slug']); ?>#book" aria-label="Open booking for <?php echo htmlspecialchars($room['name']); ?>">
                            <img src="<?php echo htmlspecialchars($room['image_url']); ?>" alt="<?php echo htmlspecialchars($room['name']); ?>">
                            <?php if (!empty($room['badge'])): ?>
                            <span class="room-tile__badge"><?php echo htmlspecialchars($room['badge']); ?></span>
                            <?php endif; ?>
                        </a>
                        <div class="room-tile__body">
                            <div class="room-tile__header">
                                <div>
                                    <h3><?php echo htmlspecialchars($room['name']); ?></h3>
                                    <p><?php echo htmlspecialchars($room['short_description']); ?></p>
                                </div>
                                <div class="room-tile__price">
                                    <span class="amount"><?php echo htmlspecialchars($currency_symbol); ?><?php echo number_format($room['price_per_night'], 0); ?></span>
                                    <small>per night</small>
                                </div>
                            </div>
                            <div class="room-tile__meta">
                                <span><i class="fas fa-user-friends"></i> <?php echo htmlspecialchars($room['max_guests']); ?> guests</span>
                                <span><i class="fas fa-ruler-combined"></i> <?php echo htmlspecialchars($room['size_sqm']); ?> sqm</span>
                                <span><i class="fas fa-bed"></i> <?php echo htmlspecialchars($room['bed_type']); ?></span>
                            </div>
                            <div class="room-tile__amenities">
                                <?php foreach ($amenities as $amenity): ?>
                                <span class="pill-small"><?php echo htmlspecialchars(trim($amenity)); ?></span>
                                <?php endforeach; ?>
                            </div>
                            <div class="room-tile__actions">
                                <a class="btn btn-outline" href="rooms.php?room=<?php echo urlencode($room['slug']); ?>">View Details</a>
                                <a class="btn btn-primary" href="index.php?room=<?php echo urlencode($room['slug']); ?>#book" data-room-book="<?php echo htmlspecialchars($room['slug']); ?>">Book Now</a>
                            </div>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
                <script>
                // 3D card tilt effect for room tiles
                document.querySelectorAll('.fancy-3d-card').forEach(card => {
                  card.addEventListener('mousemove', function(e) {
                    const rect = card.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const y = e.clientY - rect.top;
                    const centerX = rect.width / 2;
                    const centerY = rect.height / 2;
                    const rotateX = ((y - centerY) / centerY) * 10;
                    const rotateY = ((x - centerX) / centerX) * 10;
                    card.style.transform = `perspective(900px) rotateX(${-rotateX}deg) rotateY(${rotateY}deg) scale(1.04)`;
                  });
                  card.addEventListener('mouseleave', function() {
                    card.style.transform = '';
                  });
                  card.addEventListener('focus', function() {
                    card.style.boxShadow = '0 0 0 4px var(--gold)';
                  });
                  card.addEventListener('blur', function() {
                    card.style.boxShadow = '';
                  });
                });
                </script>
            </div>
        </section>

        <section class="booking-cta" id="book">
            <div class="container booking-cta__grid">
                <div class="booking-cta__content">
                    <div class="pill">Direct Booking</div>
                    <h2>Ready to reserve your stay?</h2>
                    <p>Pick your preferred suite and we will secure it instantly. Share your dates and guest count and our team will confirm right away.</p>
                    <div class="booking-cta__actions">
                        <a class="btn btn-primary" href="tel:<?php echo htmlspecialchars(preg_replace('/[^0-9+]/', '', $phone_main)); ?>"><i class="fas fa-phone"></i> Call Reservations</a>
                        <a class="btn btn-outline" href="mailto:<?php echo htmlspecialchars($email_reservations); ?>?subject=Room%20Reservation"><i class="fas fa-envelope"></i> Email Booking</a>
                    </div>
                </div>
                <div class="booking-cta__card">
                    <div class="booking-cta__row">
                        <span>Featured Room</span>
                        <strong><?php echo htmlspecialchars($hero_room['name']); ?></strong>
                    </div>
                    <div class="booking-cta__row">
                        <span>Nightly Rate</span>
                        <strong><?php echo htmlspecialchars($currency_symbol); ?><?php echo number_format($hero_room['price_per_night'], 0); ?></strong>
                    </div>
                    <div class="booking-cta__row">
                        <span>Capacity</span>
                        <strong><?php echo htmlspecialchars($hero_room['max_guests'] ?? 2); ?> guests</strong>
                    </div>
                    <div class="booking-cta__row">
                        <span>Floor Space</span>
                        <strong><?php echo htmlspecialchars($hero_room['size_sqm'] ?? 40); ?> sqm</strong>
                    </div>
                    <a class="btn btn-primary" href="index.php?room=<?php echo urlencode($hero_room['slug'] ?? ''); ?>#book">Proceed to Booking</a>
                </div>
            </div>
        </section>
    </main>

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
                            <a href="tel:<?php echo htmlspecialchars(preg_replace('/[^0-9+]/', '', $contact['phone_main'] ?? $phone_main)); ?>"><?php echo htmlspecialchars($contact['phone_main'] ?? $phone_main); ?></a>
                        </li>
                        <li>
                            <i class="fas fa-envelope"></i>
                            <a href="mailto:<?php echo htmlspecialchars($contact['email_main'] ?? $email_reservations); ?>"><?php echo htmlspecialchars($contact['email_main'] ?? $email_reservations); ?></a>
                        </li>
                        <li>
                            <i class="fas fa-map-marker-alt"></i>
                            <a href="https://www.google.com/maps/search/<?php echo urlencode(htmlspecialchars($contact['address_line1'] ?? 'Liwonde, Malawi')); ?>" target="_blank"><?php echo htmlspecialchars($contact['address_line1'] ?? 'Liwonde, Malawi'); ?></a>
                        </li>
                        <li>
                            <i class="fas fa-clock"></i>
                            <span><?php echo htmlspecialchars($contact['working_hours'] ?? '24/7 Available'); ?></span>
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
                <p>&copy; <?php echo htmlspecialchars(getSetting('copyright_text', '2026 Liwonde Sun Hotel. All rights reserved.')); ?></p>
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
</body>
</html>
