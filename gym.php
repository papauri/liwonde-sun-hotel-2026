<?php
require_once 'config/database.php';
require_once 'config/email.php';
require_once 'includes/validation.php';

// Start session for any session-based functionality
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Fetch site settings
$site_name = getSetting('site_name');
$site_logo = getSetting('site_logo');
$email_main = getSetting('email_main');


// Fetch gym content
$gymContent = [
    'wellness_title' => '',
    'wellness_description' => '',
    'wellness_image_path' => '',
    'badge_text' => '',
    'personal_training_image_path' => ''
];

try {
    $stmt = $pdo->query("SELECT * FROM gym_content WHERE is_active = 1 ORDER BY id DESC LIMIT 1");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $gymContent = array_merge($gymContent, $row);
    }
} catch (PDOException $e) {
    error_log("Error fetching gym content: " . $e->getMessage());
}

// Fetch gym features
$gymFeatures = [];
try {
    $stmt = $pdo->query("SELECT icon_class, title, description FROM gym_features WHERE is_active = 1 ORDER BY display_order ASC, id ASC");
    $gymFeatures = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching gym features: " . $e->getMessage());
}

// Fetch gym facilities (grid)
$gymFacilities = [];
try {
    $stmt = $pdo->query("SELECT icon_class, title, description FROM gym_facilities WHERE is_active = 1 ORDER BY display_order ASC, id ASC");
    $gymFacilities = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching gym facilities: " . $e->getMessage());
}

// Fetch classes
$gymClasses = [];
try {
    $stmt = $pdo->query("SELECT title, description, day_label, time_label, level_label FROM gym_classes WHERE is_active = 1 ORDER BY display_order ASC, id ASC");
    $gymClasses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching gym classes: " . $e->getMessage());
}

// Fetch packages
$gymPackages = [];
try {
    $stmt = $pdo->query("SELECT name, icon_class, includes_text, duration_label, price, currency_code, is_featured FROM gym_packages WHERE is_active = 1 ORDER BY display_order ASC, id ASC");
    $gymPackages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching gym packages: " . $e->getMessage());
}

// Handle booking form submission
$bookingSuccess = false;
$bookingError = '';
$bookingReference = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['gym_booking_form'])) {
    // Initialize validation errors array
    $validation_errors = [];
    $sanitized_data = [];
    
    // Validate full_name
    $name_validation = validateName($_POST['full_name'] ?? '', 2, true);
    if (!$name_validation['valid']) {
        $validation_errors['full_name'] = $name_validation['error'];
    } else {
        $sanitized_data['full_name'] = sanitizeString($name_validation['value'], 100);
    }
    
    // Validate email
    $email_validation = validateEmail($_POST['email'] ?? '');
    if (!$email_validation['valid']) {
        $validation_errors['email'] = $email_validation['error'];
    } else {
        // Use validated email directly - no need to sanitize as validation already ensures it's safe
        $sanitized_data['email'] = $_POST['email'];
    }
    
    // Validate phone
    $phone_validation = validatePhone($_POST['phone'] ?? '');
    if (!$phone_validation['valid']) {
        $validation_errors['phone'] = $phone_validation['error'];
    } else {
        $sanitized_data['phone'] = $phone_validation['sanitized'];
    }
    
    // Validate preferred_date
    $date_validation = validateDate($_POST['preferred_date'] ?? '', false, true);
    if (!$date_validation['valid']) {
        $validation_errors['preferred_date'] = $date_validation['error'];
    } else {
        $sanitized_data['preferred_date'] = $date_validation['date']->format('Y-m-d');
    }
    
    // Validate preferred_time
    $time_validation = validateTime($_POST['preferred_time'] ?? '');
    if (!$time_validation['valid']) {
        $validation_errors['preferred_time'] = $time_validation['error'];
    } else {
        $sanitized_data['preferred_time'] = $time_validation['time'];
    }
    
    // Validate package_choice
    $package_validation = validateText($_POST['package_choice'] ?? '', 1, 100, true);
    if (!$package_validation['valid']) {
        $validation_errors['package_choice'] = $package_validation['error'];
    } else {
        $sanitized_data['package_choice'] = sanitizeString($package_validation['value'], 100);
    }
    
    // Validate goals (optional)
    $goals_validation = validateText($_POST['goals'] ?? '', 0, 1000, false);
    if (!$goals_validation['valid']) {
        $validation_errors['goals'] = $goals_validation['error'];
    } else {
        $sanitized_data['goals'] = sanitizeString($goals_validation['value'], 1000);
    }
    
    // Validate guests (optional)
    $guests_validation = validateNumber($_POST['guests'] ?? '', 1, 10, false);
    if (!$guests_validation['valid']) {
        $validation_errors['guests'] = $guests_validation['error'];
    } else {
        $sanitized_data['guests'] = $guests_validation['value'] ?? 1;
    }
    
    // Validate consent checkbox
    $consent = isset($_POST['consent']);
    if (!$consent) {
        $validation_errors['consent'] = 'You must accept consent to proceed.';
    }
    
    // Check for validation errors
    if (!empty($validation_errors)) {
        $error_messages = [];
        foreach ($validation_errors as $field => $message) {
            $error_messages[] = ucfirst(str_replace('_', ' ', $field)) . ': ' . $message;
        }
        $bookingError = implode('; ', $error_messages);
    } else {
        // Prepare booking data for email functions
        $booking_data = [
            'name' => $sanitized_data['full_name'],
            'email' => $sanitized_data['email'],
            'phone' => $sanitized_data['phone'],
            'preferred_date' => $sanitized_data['preferred_date'],
            'preferred_time' => $sanitized_data['preferred_time'],
            'package_choice' => $sanitized_data['package_choice'],
            'guests' => $sanitized_data['guests'] ?? 1,
            'goals' => $sanitized_data['goals'] ?? ''
        ];
        
        // Send confirmation email to customer
        $customer_result = sendGymBookingEmail($booking_data);
        if (!$customer_result['success']) {
            error_log("Failed to send gym booking confirmation email: " . $customer_result['message']);
        }
        
        // Send notification email to admin
        $admin_result = sendGymAdminNotificationEmail($booking_data);
        if ($admin_result['success']) {
            $bookingSuccess = true;
            // Generate a reference number for the customer
            $bookingReference = 'GYM-' . strtoupper(substr(uniqid(), -8));
            error_log("Gym booking submitted successfully from: " . $sanitized_data['email'] . " with reference: " . $bookingReference);
        } else {
            $bookingError = 'Unable to send your request. Please try again or contact front desk directly.';
            error_log("Gym booking admin notification failed. Error: " . $admin_result['message']);
        }
    }
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
    <title>Fitness & Wellness Center - <?php echo htmlspecialchars($site_name); ?> | Gym, Spa & Yoga</title>
    <meta name="description" content="State-of-the-art fitness center and wellness facilities at <?php echo htmlspecialchars($site_name); ?>. Modern gym equipment, spa services, yoga classes, personal training, and holistic wellness programs.">
    <meta name="keywords" content="fitness center malawi, hotel gym, spa malawi, yoga classes, wellness center, personal training, health club">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://<?php echo $_SERVER['HTTP_HOST']; ?>/gym.php">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://<?php echo $_SERVER['HTTP_HOST']; ?>/gym.php">
    <meta property="og:title" content="Fitness & Wellness Center - <?php echo htmlspecialchars($site_name); ?>">
    <meta property="og:description" content="State-of-the-art fitness center with modern equipment, spa, yoga, and personal training services.">
    <meta property="og:image" content="https://<?php echo $_SERVER['HTTP_HOST']; ?>/images/gym/hero.jpg">
    
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="https://<?php echo $_SERVER['HTTP_HOST']; ?>/gym.php">
    <meta property="twitter:title" content="Fitness & Wellness Center - <?php echo htmlspecialchars($site_name); ?>">
    <meta property="twitter:description" content="State-of-the-art fitness center with modern equipment, spa, yoga, and personal training services.">
    <meta property="twitter:image" content="https://<?php echo $_SERVER['HTTP_HOST']; ?>/images/gym/hero.jpg">
    
    <!-- Preload Critical Resources -->
    <link rel="preload" href="css/style.css" as="style">
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
    <link rel="stylesheet" href="css/style.css">
    
    <!-- Structured Data - Sports Activity Location Schema -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "SportsActivityLocation",
      "name": "<?php echo htmlspecialchars($site_name); ?> Fitness & Wellness Center",
      "image": "https://<?php echo $_SERVER['HTTP_HOST']; ?>/images/gym/hero.jpg",
      "description": "State-of-the-art fitness center with modern equipment, spa, yoga, and personal training services",
      "url": "https://<?php echo $_SERVER['HTTP_HOST']; ?>/gym.php"
    }
    </script>
</head>
<body>
    <?php include 'includes/loader.php'; ?>
    
    <!-- Loading Animation -->
    <div class="page-loader">
        <div class="loader-content">
            <div class="luxury-spinner"></div>
            <p class="loader-text">Preparing Your Wellness Journey</p>
        </div>
    </div>

    <?php if ($bookingSuccess): ?>
    <div class="alert-banner success" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); border-left: 5px solid #155724; padding: 20px 0;">
        <div class="container" style="display: flex; align-items: center; justify-content: center; flex-direction: column; text-align: center;">
            <i class="fas fa-check-circle" style="font-size: 48px; margin-bottom: 15px;"></i>
            <h2 style="color: #fff; margin: 0 0 10px 0; font-size: 28px;">Booking Request Submitted Successfully!</h2>
            <p style="color: #fff; margin: 0 0 15px 0; font-size: 18px;">Thank you for your gym booking request. Our team will contact you within 24 hours to confirm your booking.</p>
            <div style="background: rgba(255,255,255,0.2); padding: 12px 25px; border-radius: 8px; margin-top: 10px;">
                <strong style="color: #fff; font-size: 16px;">Reference Number: <span style="background: #fff; color: #28a745; padding: 5px 15px; border-radius: 5px; font-weight: bold;"><?php echo htmlspecialchars($bookingReference); ?></span></strong>
            </div>
            <p style="color: #fff; margin: 15px 0 0 0; font-size: 14px; opacity: 0.9;">Please save this reference number for your records. A confirmation email has been sent to your email address.</p>
        </div>
    </div>
    <?php elseif (!empty($bookingError)): ?>
    <div class="alert-banner error" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); border-left: 5px solid #721c24; padding: 20px 0;">
        <div class="container" style="display: flex; align-items: center; justify-content: center; flex-direction: column; text-align: center;">
            <i class="fas fa-exclamation-triangle" style="font-size: 48px; margin-bottom: 15px;"></i>
            <h2 style="color: #fff; margin: 0 0 10px 0; font-size: 28px;">Booking Request Failed</h2>
            <p style="color: #fff; margin: 0; font-size: 18px;"><?php echo htmlspecialchars($bookingError); ?></p>
            <p style="color: #fff; margin: 10px 0 0 0; font-size: 14px; opacity: 0.9;">Please try again or contact our front desk directly for assistance.</p>
        </div>
    </div>
    <?php endif; ?>
    
    <?php include 'includes/header.php'; ?>
    
    <!-- Mobile Menu Overlay -->
    <div class="mobile-menu-overlay" role="presentation"></div>

    <!-- Hero Section -->
    <?php include 'includes/hero.php'; ?>

    <!-- Wellness Overview -->
    <section class="wellness-intro section-padding">
        <div class="container">
            <div class="wellness-content-grid">
                <div class="wellness-text" data-aos="fade-right">
                    <span class="section-label">Your Wellness Journey</span>
                    <h2 class="section-title"><?php echo htmlspecialchars($gymContent['wellness_title']); ?></h2>
                    <p class="section-description"><?php echo htmlspecialchars($gymContent['wellness_description']); ?></p>
                    
                    <div class="wellness-features">
                        <?php if (!empty($gymFeatures)): ?>
                            <?php foreach ($gymFeatures as $feature): ?>
                                <div class="feature-item">
                                    <i class="<?php echo htmlspecialchars($feature['icon_class']); ?>"></i>
                                    <div>
                                        <h4><?php echo htmlspecialchars($feature['title']); ?></h4>
                                        <p><?php echo htmlspecialchars($feature['description']); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="feature-item">
                                <i class="fas fa-dumbbell"></i>
                                <div>
                                    <h4>Modern Equipment</h4>
                                    <p>Latest cardio machines, free weights, and resistance training equipment</p>
                                </div>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-user-md"></i>
                                <div>
                                    <h4>Personal Training</h4>
                                    <p>Certified trainers available for one-on-one sessions and customized programs</p>
                                </div>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-spa"></i>
                                <div>
                                    <h4>Spa & Recovery</h4>
                                    <p>Massage therapy, sauna, and steam rooms for post-workout relaxation</p>
                                </div>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-clock"></i>
                                <div>
                                    <h4>Flexible Hours</h4>
                                    <p>Open daily from 5:30 AM to 10:00 PM for your convenience</p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="wellness-image" data-aos="fade-left">
                    <img src="<?php echo htmlspecialchars($gymContent['wellness_image_path']); ?>" alt="Modern Fitness Center" loading="lazy">
                    <div class="image-badge">
                        <i class="fas fa-trophy"></i>
                        <span><?php echo htmlspecialchars($gymContent['badge_text']); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Gym Facilities -->
    <section class="gym-facilities section-padding bg-dark">
        <div class="container">
            <div class="section-header text-center">
                <span class="section-label">What We Offer</span>
                <h2 class="section-title">Comprehensive Fitness Facilities</h2>
                <p class="section-description">Everything you need for a complete wellness experience</p>
            </div>

            <div class="facilities-grid">
                <?php if (!empty($gymFacilities)): ?>
                    <?php foreach ($gymFacilities as $index => $facility): ?>
                        <div class="facility-card" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                            <div class="facility-icon"><i class="<?php echo htmlspecialchars($facility['icon_class']); ?>"></i></div>
                            <h3><?php echo htmlspecialchars($facility['title']); ?></h3>
                            <p><?php echo htmlspecialchars($facility['description']); ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="facility-card" data-aos="fade-up">
                        <div class="facility-icon"><i class="fas fa-running"></i></div>
                        <h3>Cardio Zone</h3>
                        <p>Treadmills, ellipticals, stationary bikes, and rowing machines with entertainment screens and heart rate monitoring</p>
                    </div>
                    <div class="facility-card" data-aos="fade-up" data-aos-delay="100">
                        <div class="facility-icon"><i class="fas fa-dumbbell"></i></div>
                        <h3>Strength Training</h3>
                        <p>Complete range of free weights, barbells, resistance machines, and functional training equipment</p>
                    </div>
                    <div class="facility-card" data-aos="fade-up" data-aos-delay="200">
                        <div class="facility-icon"><i class="fas fa-child"></i></div>
                        <h3>Yoga & Pilates Studio</h3>
                        <p>Dedicated studio space for yoga, pilates, and meditation with daily group classes</p>
                    </div>
                    <div class="facility-card" data-aos="fade-up" data-aos-delay="300">
                        <div class="facility-icon"><i class="fas fa-swimming-pool"></i></div>
                        <h3>Lap Pool</h3>
                        <p>25-meter heated pool perfect for swimming workouts and aqua aerobics sessions</p>
                    </div>
                    <div class="facility-card" data-aos="fade-up" data-aos-delay="400">
                        <div class="facility-icon"><i class="fas fa-hot-tub"></i></div>
                        <h3>Spa & Sauna</h3>
                        <p>Traditional sauna, steam room, and jacuzzi for relaxation and muscle recovery</p>
                    </div>
                    <div class="facility-card" data-aos="fade-up" data-aos-delay="500">
                        <div class="facility-icon"><i class="fas fa-apple-alt"></i></div>
                        <h3>Nutrition Bar</h3>
                        <p>Fresh smoothies, protein shakes, and healthy snacks to fuel your workout</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Class Schedule -->
    <section class="class-schedule section-padding">
        <div class="container">
            <div class="section-header text-center">
                <span class="section-label">Stay Active</span>
                <h2 class="section-title">Group Fitness Classes</h2>
                <p class="section-description">Join our expert-led classes designed for all fitness levels</p>
            </div>

            <div class="schedule-grid">
                <?php if (!empty($gymClasses)): ?>
                    <?php foreach ($gymClasses as $idx => $class): ?>
                        <div class="class-card" data-aos="zoom-in" data-aos-delay="<?php echo $idx * 100; ?>">
                            <div class="class-time">
                                <span class="day"><?php echo htmlspecialchars($class['day_label']); ?></span>
                                <span class="time"><?php echo htmlspecialchars($class['time_label']); ?></span>
                            </div>
                            <h3><?php echo htmlspecialchars($class['title']); ?></h3>
                            <p><?php echo htmlspecialchars($class['description']); ?></p>
                            <span class="class-level"><?php echo htmlspecialchars($class['level_label']); ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="class-card" data-aos="zoom-in">
                        <div class="class-time">
                            <span class="day">Monday - Friday</span>
                            <span class="time">6:30 AM</span>
                        </div>
                        <h3>Morning Yoga Flow</h3>
                        <p>Start your day with energizing yoga sequences</p>
                        <span class="class-level">All Levels</span>
                    </div>
                    <div class="class-card" data-aos="zoom-in" data-aos-delay="100">
                        <div class="class-time">
                            <span class="day">Tuesday & Thursday</span>
                            <span class="time">7:00 AM</span>
                        </div>
                        <h3>HIIT Bootcamp</h3>
                        <p>High-intensity interval training for maximum results</p>
                        <span class="class-level">Intermediate</span>
                    </div>
                    <div class="class-card" data-aos="zoom-in" data-aos-delay="200">
                        <div class="class-time">
                            <span class="day">Wednesday & Saturday</span>
                            <span class="time">8:00 AM</span>
                        </div>
                        <h3>Pilates Core</h3>
                        <p>Strengthen your core with controlled movements</p>
                        <span class="class-level">All Levels</span>
                    </div>
                    <div class="class-card" data-aos="zoom-in" data-aos-delay="300">
                        <div class="class-time">
                            <span class="day">Daily</span>
                            <span class="time">6:00 PM</span>
                        </div>
                        <h3>Evening Meditation</h3>
                        <p>Wind down with guided meditation and breathing</p>
                        <span class="class-level">All Levels</span>
                    </div>
                <?php endif; ?>
            </div>

            <div class="schedule-cta text-center">
                <button class="btn btn-primary" data-open-booking><i class="fas fa-calendar-plus"></i> Book a Class</button>
                <a href="#" onclick="alert('Full schedule download will be available soon. Please contact us for the complete class schedule.'); return false;" class="btn btn-outline"><i class="fas fa-download"></i> Download Full Schedule</a>
            </div>
        </div>
    </section>

    <!-- Personal Training -->
    <section class="personal-training section-padding bg-dark">
        <div class="container">
            <div class="training-content-grid">
                <div class="training-image" data-aos="fade-right">
                    <img src="<?php echo htmlspecialchars($gymContent['personal_training_image_path']); ?>" alt="Personal Training" loading="lazy">
                </div>
                
                <div class="training-text" data-aos="fade-left">
                    <span class="section-label">One-on-One Coaching</span>
                    <h2 class="section-title">Personal Training Programs</h2>
                    <p class="section-description">Achieve your fitness goals faster with personalized guidance from our certified trainers.</p>
                    
                    <ul class="training-benefits">
                        <li><i class="fas fa-check-circle"></i> Customized workout plans tailored to your goals</li>
                        <li><i class="fas fa-check-circle"></i> Nutritional guidance and meal planning</li>
                        <li><i class="fas fa-check-circle"></i> Progress tracking and regular assessments</li>
                        <li><i class="fas fa-check-circle"></i> Flexible scheduling to fit your stay</li>
                        <li><i class="fas fa-check-circle"></i> Expert form correction and injury prevention</li>
                    </ul>
                    
                    <div class="training-cta">
                        <button class="btn btn-primary" data-open-booking><i class="fas fa-user-plus"></i> Book a Session</button>
                        <p class="training-note">First consultation is complimentary for hotel guests</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Wellness Packages -->
    <section class="wellness-packages section-padding">
        <div class="container">
            <div class="section-header text-center">
                <span class="section-label">Exclusive Offers</span>
                <h2 class="section-title">Wellness Packages</h2>
                <p class="section-description">Comprehensive packages designed for optimal health and relaxation</p>
            </div>

            <div class="packages-grid">
                <?php if (!empty($gymPackages)): ?>
                    <?php foreach ($gymPackages as $idx => $package): ?>
                        <?php $includes = array_filter(array_map('trim', preg_split('/\r\n|\n|\r/', $package['includes_text'] ?? ''))); ?>
                        <div class="package-card <?php echo !empty($package['is_featured']) ? 'featured' : ''; ?>" data-aos="flip-left" data-aos-delay="<?php echo $idx * 100; ?>">
                            <?php if (!empty($package['is_featured'])): ?><span class="popular-badge">Most Popular</span><?php endif; ?>
                            <div class="package-icon"><i class="<?php echo htmlspecialchars($package['icon_class']); ?>"></i></div>
                            <h3><?php echo htmlspecialchars($package['name']); ?></h3>
                            <ul class="package-includes">
                                <?php if (!empty($includes)): ?>
                                    <?php foreach ($includes as $include): ?>
                                        <li><?php echo htmlspecialchars($include); ?></li>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </ul>
                            <?php if (!empty($package['duration_label'])): ?>
                                <div class="package-duration"><?php echo htmlspecialchars($package['duration_label']); ?></div>
                            <?php endif; ?>
                            <div class="package-price">
                                <span class="price-currency"><?php echo htmlspecialchars($package['currency_code']); ?></span>
                                <span class="price-amount"><?php echo number_format($package['price'] ?? 0, 0); ?></span>
                            </div>
                            <button class="btn <?php echo !empty($package['is_featured']) ? 'btn-primary' : 'btn-outline'; ?>" type="button" data-open-booking>Book Package</button>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="package-card" data-aos="flip-left">
                        <div class="package-icon"><i class="fas fa-leaf"></i></div>
                        <h3>Rejuvenation Retreat</h3>
                        <ul class="package-includes">
                            <li>3 personal training sessions</li>
                            <li>Daily yoga classes</li>
                            <li>2 spa massages</li>
                            <li>Nutrition consultation</li>
                            <li>Complimentary smoothie bar access</li>
                        </ul>
                        <div class="package-duration">5 Days</div>
                        <div class="package-price">
                            <span class="price-currency"><?php echo htmlspecialchars(getSetting('currency_code')); ?></span>
                            <span class="price-amount">45,000</span>
                        </div>
                        <button class="btn btn-outline" type="button" data-open-booking>Book Package</button>
                    </div>
                    <div class="package-card featured" data-aos="flip-left" data-aos-delay="100">
                        <span class="popular-badge">Most Popular</span>
                        <div class="package-icon"><i class="fas fa-star"></i></div>
                        <h3>Ultimate Wellness</h3>
                        <ul class="package-includes">
                            <li>5 personal training sessions</li>
                            <li>Unlimited group classes</li>
                            <li>4 spa treatments</li>
                            <li>Full nutrition program</li>
                            <li>Fitness assessment & tracking</li>
                            <li>Complimentary wellness amenities</li>
                        </ul>
                        <div class="package-duration">7 Days</div>
                        <div class="package-price">
                            <span class="price-currency"><?php echo htmlspecialchars(getSetting('currency_code')); ?></span>
                            <span class="price-amount">85,000</span>
                        </div>
                        <button class="btn btn-primary" type="button" data-open-booking>Book Package</button>
                    </div>
                    <div class="package-card" data-aos="flip-left" data-aos-delay="200">
                        <div class="package-icon"><i class="fas fa-dumbbell"></i></div>
                        <h3>Fitness Kickstart</h3>
                        <ul class="package-includes">
                            <li>2 personal training sessions</li>
                            <li>Group class pass (5 classes)</li>
                            <li>1 spa massage</li>
                            <li>Fitness assessment</li>
                            <li>Workout plan to take home</li>
                        </ul>
                        <div class="package-duration">3 Days</div>
                        <div class="package-price">
                            <span class="price-currency"><?php echo htmlspecialchars(getSetting('currency_code')); ?></span>
                            <span class="price-amount">28,000</span>
                        </div>
                        <button class="btn btn-outline" type="button" data-open-booking>Book Package</button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Booking Modal -->
    <div class="booking-modal" id="bookingModal" data-booking-modal>
        <div class="booking-modal__overlay" data-close-booking></div>
        <div class="booking-modal__content">
            <button class="booking-modal__close" aria-label="Close booking form" data-close-booking>&times;</button>
            <div class="booking-modal__header">
                <span class="booking-pill">Gym Booking</span>
                <h3>Request a Session</h3>
                <p>Complete form and our team will confirm your booking via email.</p>
            </div>
            <form method="POST" class="booking-form" novalidate>
                <input type="hidden" name="gym_booking_form" value="1">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="full_name">Full Name *</label>
                        <input type="text" id="full_name" name="full_name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone *</label>
                        <input type="tel" id="phone" name="phone" required>
                    </div>
                    <div class="form-group">
                        <label for="guests">Guests</label>
                        <input type="number" id="guests" name="guests" min="1" max="10" placeholder="1">
                    </div>
                    <div class="form-group">
                        <label for="preferred_date">Preferred Date *</label>
                        <input type="date" id="preferred_date" name="preferred_date" required>
                    </div>
                    <div class="form-group">
                        <label for="preferred_time">Preferred Time *</label>
                        <input type="time" id="preferred_time" name="preferred_time" required>
                    </div>
                    <div class="form-group full">
                        <label for="package_choice">Select Package *</label>
                        <select id="package_choice" name="package_choice" required>
                            <option value="">Choose a package</option>
                            <?php foreach ($gymPackages as $pkg): ?>
                                <option value="<?php echo htmlspecialchars($pkg['name']); ?>">
                                    <?php echo htmlspecialchars($pkg['name']); ?> (<?php echo htmlspecialchars($pkg['currency_code']); ?> <?php echo number_format($pkg['price'], 0); ?>)
                                </option>
                            <?php endforeach; ?>
                            <?php if (empty($gymPackages)): ?>
                                <option value="Custom Request">Custom Request</option>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="form-group full">
                        <label for="goals">Fitness Goals / Notes</label>
                        <textarea id="goals" name="goals" rows="4" placeholder="Tell us what you want to achieve or any special requests"></textarea>
                    </div>
                    <div class="form-consent full">
                        <label class="checkbox">
                            <input type="checkbox" name="consent" required>
                            <span>I agree to be contacted about this booking request.</span>
                        </label>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary full-width">Send Booking Request</button>
                <p class="privacy-note">We send bookings to: <?php echo htmlspecialchars($email_main); ?>. We respect your privacy.</p>
            </form>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <!-- Scripts -->
    <script src="js/main.js"></script>
    <script>
        // Page loader
        window.addEventListener('load', function() {
            const loader = document.querySelector('.page-loader');
            if (loader) {
                loader.classList.add('fade-out');
                setTimeout(() => { loader.style.display = 'none'; }, 500);
            }
        });

        // Booking modal - custom implementation for booking-modal structure
        const bookingModal = document.querySelector('[data-booking-modal]');
        const openButtons = document.querySelectorAll('[data-open-booking]');
        const closeButtons = document.querySelectorAll('[data-close-booking]');

        function openModal() {
            if (bookingModal) {
                bookingModal.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeModal() {
            if (bookingModal) {
                bookingModal.classList.remove('active');
                document.body.style.overflow = '';
            }
        }

        openButtons.forEach(btn => btn.addEventListener('click', openModal));
        closeButtons.forEach(btn => btn.addEventListener('click', closeModal));
        document.addEventListener('keyup', (e) => {
            if (e.key === 'Escape') closeModal();
        });
    </script>

    <?php include 'includes/scroll-to-top.php'; ?>
</body>
</html>
