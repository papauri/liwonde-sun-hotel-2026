<?php
require_once '../config/database.php';

$room_slug = isset($_GET['room']) ? trim($_GET['room']) : null;
if (!$room_slug) {
    header('Location: /');
    exit;
}

$site_name = getSetting('site_name');
$site_tagline = getSetting('site_tagline');
$site_logo = getSetting('site_logo');
$currency_symbol = getSetting('currency_symbol');
$email_reservations = getSetting('email_reservations');
$phone_main = getSetting('phone_main');

function resolveImageUrl($path) {
    if (!$path) return '';
    $trimmed = trim($path);
    if (stripos($trimmed, 'http://') === 0 || stripos($trimmed, 'https://') === 0) {
        return $trimmed;
    }
    // Add ../ prefix for relative paths since we're in /pages/ subdirectory
    if (stripos($trimmed, 'images/') === 0) {
        return '../' . $trimmed;
    }
    return $trimmed;
}

$policies = [];
try {
    $policyStmt = $pdo->query("SELECT slug, title, summary, content FROM policies WHERE is_active = 1 ORDER BY display_order ASC, id ASC");
    $policies = $policyStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $policies = [];
}

$contact_settings = getSettingsByGroup('contact');
$contact = [];
foreach ($contact_settings as $setting) {
    $contact[$setting['setting_key']] = $setting['setting_value'];
}

$social_settings = getSettingsByGroup('social');
$social = [];
foreach ($social_settings as $setting) {
    $social[$setting['setting_key']] = $setting['setting_value'];
}

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

$room = null;
$room_images = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM rooms WHERE slug = ? AND is_active = 1");
    $stmt->execute([$room_slug]);
    $room = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$room) {
        header('Location: /');
        exit;
    }

    $galleryStmt = $pdo->prepare("SELECT id, title, description, image_url FROM gallery WHERE room_id = ? AND is_active = 1 AND image_url IS NOT NULL AND image_url != '' ORDER BY display_order ASC, id ASC");
    $galleryStmt->execute([$room['id']]);
    $room_images = $galleryStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log('Error fetching room details: ' . $e->getMessage());
}

$room_images = array_values(array_filter($room_images, function($img) {
    return !empty($img['image_url']) && trim($img['image_url']) !== '';
}));

if (empty($room_images) && !empty($room['image_url'])) {
    $room_images[] = [
        'id' => 0,
        'title' => $room['name'],
        'description' => '',
        'image_url' => $room['image_url']
    ];
}

$hero_image = resolveImageUrl($room_images[0]['image_url'] ?? $room['image_url']);
$amenities = array_filter(array_map('trim', explode(',', $room['amenities'] ?? '')));
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
    <title><?php echo htmlspecialchars($room['name']); ?> | <?php echo htmlspecialchars($site_name); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($room['short_description'] ?? $site_tagline); ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="rooms-page">
    <?php include '../includes/loader.php'; ?>
    
    <?php include '../includes/header.php'; ?>
    
    <!-- Mobile Menu Overlay -->
    <div class="mobile-menu-overlay" role="presentation"></div>

    <section class="rooms-hero" style="background-image: linear-gradient(135deg, rgba(5, 9, 15, 0.76), rgba(10, 25, 41, 0.75)), url('<?php echo htmlspecialchars($hero_image); ?>');">
        <div class="container">
            <div class="rooms-hero__grid">
                <div class="rooms-hero__content">
                    <div class="pill">Signature Stay</div>
                    <h1><?php echo htmlspecialchars($room['name']); ?></h1>
                    <p><?php echo htmlspecialchars($room['short_description'] ?? $site_tagline); ?></p>
                    <div class="rooms-hero__actions">
                        <a class="btn btn-primary" href="../booking.php?room_id=<?php echo $room['id']; ?>">Book This Room</a>
                        <a class="btn btn-outline" href="../index.php#rooms">View All Rooms</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section room-detail-below" style="padding-top: 30px; padding-bottom: 40px;">
        <div class="container">

            <?php if (!empty($room_images)): ?>
            <h3 style="font-size: 24px; color: var(--navy); margin: 40px 0 24px 0; font-weight: 700;">Room Gallery</h3>
            <div class="room-gallery-grid">
                <?php foreach ($room_images as $img): ?>
                <div class="gallery-item">
                    <img src="<?php echo htmlspecialchars(resolveImageUrl($img['image_url'])); ?>" alt="<?php echo htmlspecialchars($img['title']); ?>">
                    <div class="gallery-item-label"><?php echo htmlspecialchars($img['title']); ?></div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Room Details Section - Moved below gallery -->
            <aside class="room-detail-info room-detail-info--horizontal" aria-label="Room details">
                <div class="room-detail-header">
                    <h2 class="room-detail-title">About the Room</h2>
                    <a class="btn btn-primary btn-booking" href="../booking.php?room_id=<?php echo $room['id']; ?>">
                        <i class="fas fa-calendar-check"></i> Book Now
                    </a>
                </div>
                <p class="room-detail-description"><?php echo htmlspecialchars($room['description'] ?? $room['short_description']); ?></p>
                <div class="room-detail-specs">
                    <div class="spec-item"><i class="fas fa-users"></i><div class="spec-label">Guests</div><div class="spec-value">Up to <?php echo htmlspecialchars($room['max_guests'] ?? 2); ?></div></div>
                    <div class="spec-item"><i class="fas fa-ruler-combined"></i><div class="spec-label">Floor Space</div><div class="spec-value"><?php echo htmlspecialchars($room['size_sqm']); ?> sqm</div></div>
                    <div class="spec-item"><i class="fas fa-bed"></i><div class="spec-label">Bed Type</div><div class="spec-value"><?php echo htmlspecialchars($room['bed_type']); ?></div></div>
                    <div class="spec-item"><i class="fas fa-tag"></i><div class="spec-label">Nightly Rate</div><div class="spec-value"><?php echo htmlspecialchars($currency_symbol); ?><?php echo number_format($room['price_per_night'], 0); ?></div></div>
                    <?php
                    $available = $room['rooms_available'] ?? 0;
                    $total = $room['total_rooms'] ?? 0;
                    if ($total > 0):
                        $availability_class = $available == 0 ? 'unavailable' : ($available <= 2 ? 'low' : 'good');
                    ?>
                    <div class="spec-item availability-<?php echo $availability_class; ?>">
                        <i class="fas fa-door-open"></i>
                        <div class="spec-label">Availability</div>
                        <div class="spec-value"><?php echo $available; ?>/<?php echo $total; ?> rooms</div>
                    </div>
                    <?php endif; ?>
                </div>
                <?php if (!empty($amenities)): ?>
                <div class="amenities-list">
                    <h3 class="amenities-list__title">Room Amenities</h3>
                    <div class="amenities-list__chips">
                        <?php foreach ($amenities as $amenity): ?>
                        <span class="amenities-chip"><i class="fas fa-check"></i><?php echo htmlspecialchars($amenity); ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </aside>
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
                <div class="booking-cta__row"><span>Selected Room</span><strong><?php echo htmlspecialchars($room['name']); ?></strong></div>
                <div class="booking-cta__row"><span>Nightly Rate</span><strong><?php echo htmlspecialchars($currency_symbol); ?><?php echo number_format($room['price_per_night'], 0); ?></strong></div>
                <div class="booking-cta__row"><span>Capacity</span><strong><?php echo htmlspecialchars($room['max_guests'] ?? 2); ?> guests</strong></div>
                <div class="booking-cta__row"><span>Floor Space</span><strong><?php echo htmlspecialchars($room['size_sqm'] ?? 40); ?> sqm</strong></div>
                <a class="btn btn-primary" href="../booking.php?room_id=<?php echo $room['id']; ?>">Proceed to Booking</a>
            </div>
        </div>
    </section>

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
                        <li><i class="fas fa-phone"></i><a href="tel:<?php echo htmlspecialchars(preg_replace('/[^0-9+]/', '', $contact['phone_main'] ?? $phone_main)); ?>"><?php echo htmlspecialchars($contact['phone_main'] ?? $phone_main); ?></a></li>
                        <li><i class="fas fa-envelope"></i><a href="mailto:<?php echo htmlspecialchars($contact['email_main'] ?? $email_reservations); ?>"><?php echo htmlspecialchars($contact['email_main'] ?? $email_reservations); ?></a></li>
                        <li><i class="fas fa-map-marker-alt"></i><a href="https://www.google.com/maps/search/<?php echo urlencode(htmlspecialchars($contact['address_line1'] ?? getSetting('address_line1'))); ?>" target="_blank"><?php echo htmlspecialchars($contact['address_line1'] ?? getSetting('address_line1')); ?></a></li>
                        <li><i class="fas fa-clock"></i><span><?php echo htmlspecialchars($contact['working_hours'] ?? getSetting('working_hours')); ?></span></li>
                    </ul>
                    <div class="social-links">
                        <?php if (!empty($social['facebook_url'])): ?><a href="<?php echo htmlspecialchars($social['facebook_url']); ?>" class="social-icon" target="_blank"><i class="fab fa-facebook-f"></i></a><?php endif; ?>
                        <?php if (!empty($social['instagram_url'])): ?><a href="<?php echo htmlspecialchars($social['instagram_url']); ?>" class="social-icon" target="_blank"><i class="fab fa-instagram"></i></a><?php endif; ?>
                        <?php if (!empty($social['twitter_url'])): ?><a href="<?php echo htmlspecialchars($social['twitter_url']); ?>" class="social-icon" target="_blank"><i class="fab fa-twitter"></i></a><?php endif; ?>
                        <?php if (!empty($social['linkedin_url'])): ?><a href="<?php echo htmlspecialchars($social['linkedin_url']); ?>" class="social-icon" target="_blank"><i class="fab fa-linkedin-in"></i></a><?php endif; ?>
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
                    <?php if (!empty($policy['summary'])): ?><p class="policy-summary"><?php echo htmlspecialchars($policy['summary']); ?></p><?php endif; ?>
                </div>
                <div class="policy-modal__body">
                    <p><?php echo nl2br(htmlspecialchars($policy['content'])); ?></p>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <script src="../js/main.js"></script>
</body>
</html>
