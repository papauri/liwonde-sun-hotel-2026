<?php
require_once 'includes/environment.php';
require_once 'includes/utils.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo getSiteName(); ?> | Luxury Accommodation in Malawi</title>
    <meta name="description" content="Experience luxury at Liwonde Sun Hotel, your premier destination in Malawi for comfort, relaxation, and adventure.">
    
    <!-- Favicon -->
    <link rel="icon" href="<?php echo getAssetUrl('images/favicon.ico'); ?>" type="image/x-icon">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo getResourcePath('css/style.css'); ?>">
    
    <?php echo getAnalyticsCode(); ?>
</head>
<body>
    <?php 
    displayEnvironmentBadge();
    ?>

    <!-- Header -->
    <header class="header">
        <div class="container">
            <nav class="navbar">
                <div class="logo">
                    <a href="index.php">
                        <img src="<?php echo getAssetUrl('images/logo.png'); ?>" alt="<?php echo getSiteName(); ?> Logo">
                        <span><?php echo getSiteName(); ?></span>
                    </a>
                </div>
                
                <div class="nav-toggle" id="mobile-menu">
                    <span class="bar"></span>
                    <span class="bar"></span>
                    <span class="bar"></span>
                </div>
                
                <ul class="nav-menu">
                    <li><a href="index.php" class="active">Home</a></li>
                    <li><a href="pages/about.php">About</a></li>
                    <li><a href="pages/rooms.php">Rooms</a></li>
                    <li><a href="pages/facilities.php">Facilities</a></li>
                    <li><a href="pages/gallery.php">Gallery</a></li>
                    <li><a href="pages/contact.php">Contact</a></li>
                    <li><a href="pages/booking.php" class="btn-premium">Book Now</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-slider">
            <div class="slide active">
                <div class="slide-content">
                    <h1>Experience Luxury in the Heart of Malawi</h1>
                    <p>Discover unparalleled comfort and breathtaking views at <?php echo getSiteName(); ?></p>
                    <div class="hero-btns">
                        <a href="pages/booking.php" class="btn-premium">Book Your Stay</a>
                        <a href="pages/rooms.php" class="btn-secondary">Explore Rooms</a>
                    </div>
                    <div class="hero-stats">
                        <div class="stat-item">
                            <span class="stat-number">5★</span>
                            <span class="stat-label">Rating</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">200+</span>
                            <span class="stat-label">Luxury Rooms</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">24/7</span>
                            <span class="stat-label">Service</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="slide">
                <div class="slide-content">
                    <h1>Unforgettable Experiences Await</h1>
                    <p>From wildlife safaris to cultural tours, explore the beauty of Malawi</p>
                    <div class="hero-btns">
                        <a href="pages/facilities.php" class="btn-primary">Our Facilities</a>
                        <a href="pages/gallery.php" class="btn-secondary">View Gallery</a>
                    </div>
                </div>
            </div>
            
            <div class="slide">
                <div class="slide-content">
                    <h1>Dining Excellence</h1>
                    <p>Indulge in exquisite cuisine crafted with local ingredients</p>
                    <div class="hero-btns">
                        <a href="pages/facilities.php#dining" class="btn-primary">Restaurant Menu</a>
                        <a href="pages/booking.php" class="btn-premium">Book Table</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="slider-controls">
            <button class="prev-slide"><i class="fas fa-chevron-left"></i></button>
            <button class="next-slide"><i class="fas fa-chevron-right"></i></button>
        </div>
        
        <div class="slide-indicators">
            <span class="indicator active" data-slide="0"></span>
            <span class="indicator" data-slide="1"></span>
            <span class="indicator" data-slide="2"></span>
        </div>
    </section>

    <!-- Featured Rooms -->
    <section class="featured-rooms">
        <div class="container">
            <div class="section-header">
                <h2>Featured Accommodations</h2>
                <p>Discover our most popular room categories</p>
            </div>
            
            <div class="rooms-grid">
                <div class="room-card">
                    <div class="room-image">
                        <img src="<?php echo getAssetUrl('images/hotel-exterior.jpg'); ?>" alt="Standard Room">
                        <div class="room-badge">Best Value</div>
                    </div>
                    <div class="room-info">
                        <h3>Standard Room</h3>
                        <p>Comfortable accommodation with all essential amenities</p>
                        <div class="room-features">
                            <div class="room-feature">
                                <i class="fas fa-bed"></i>
                                <span>King-size Bed</span>
                            </div>
                            <div class="room-feature">
                                <i class="fas fa-wifi"></i>
                                <span>Free WiFi</span>
                            </div>
                            <div class="room-feature">
                                <i class="fas fa-tv"></i>
                                <span>Smart TV</span>
                            </div>
                        </div>
                        <div class="room-price">
                            <span class="price"><?php echo CURRENCY_SYMBOL; ?>120<span>/night</span></span>
                            <a href="pages/booking.php" class="btn-premium">Book Now</a>
                        </div>
                    </div>
                </div>
                
                <div class="room-card">
                    <div class="room-image">
                        <img src="<?php echo getAssetUrl('images/hotel-lobby.jpg'); ?>" alt="Deluxe Room">
                        <div class="room-badge">Most Popular</div>
                    </div>
                    <div class="room-info">
                        <h3>Deluxe Room</h3>
                        <p>Spacious accommodation with premium amenities and panoramic views</p>
                        <div class="room-features">
                            <div class="room-feature">
                                <i class="fas fa-bed"></i>
                                <span>King-size Bed</span>
                            </div>
                            <div class="room-feature">
                                <i class="fas fa-wifi"></i>
                                <span>Free WiFi</span>
                            </div>
                            <div class="room-feature">
                                <i class="fas fa-tv"></i>
                                <span>Smart TV with Streaming</span>
                            </div>
                            <div class="room-feature">
                                <i class="fas fa-mountain"></i>
                                <span>Scenic Views</span>
                            </div>
                            <div class="room-feature">
                                <i class="fas fa-concierge-bell"></i>
                                <span>Priority Service</span>
                            </div>
                        </div>
                        <div class="room-price">
                            <span class="price"><?php echo CURRENCY_SYMBOL; ?>180<span>/night</span></span>
                            <a href="pages/booking.php" class="btn-premium">Book Now</a>
                        </div>
                    </div>
                </div>
                
                <div class="room-card">
                    <div class="room-image">
                        <img src="<?php echo getAssetUrl('images/pool-area.jpg'); ?>" alt="Executive Suite">
                        <div class="room-badge">Luxury</div>
                    </div>
                    <div class="room-info">
                        <h3>Executive Suite</h3>
                        <p>Luxurious accommodation with separate living area and premium services</p>
                        <div class="room-features">
                            <div class="room-feature">
                                <i class="fas fa-bed"></i>
                                <span>King-size Bed</span>
                            </div>
                            <div class="room-feature">
                                <i class="fas fa-couch"></i>
                                <span>Separate Living Area</span>
                            </div>
                            <div class="room-feature">
                                <i class="fas fa-wifi"></i>
                                <span>Free WiFi</span>
                            </div>
                            <div class="room-feature">
                                <i class="fas fa-tv"></i>
                                <span>Two Smart TVs</span>
                            </div>
                            <div class="room-feature">
                                <i class="fas fa-glass-whiskey"></i>
                                <span>Wet Bar</span>
                            </div>
                            <div class="room-feature">
                                <i class="fas fa-concierge-bell"></i>
                                <span>Priority Service</span>
                            </div>
                            <div class="room-feature">
                                <i class="fas fa-umbrella-beach"></i>
                                <span>Private Terrace</span>
                            </div>
                        </div>
                        <div class="room-price">
                            <span class="price"><?php echo CURRENCY_SYMBOL; ?>280<span>/night</span></span>
                            <a href="pages/booking.php" class="btn-premium">Book Now</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="view-all">
                <a href="pages/rooms.php" class="btn-primary">View All Rooms</a>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="about-section">
        <div class="container">
            <div class="about-content">
                <div class="about-text">
                    <h2>Welcome to <?php echo getSiteName(); ?></h2>
                    <p>Nestled in the heart of Malawi's stunning landscape, <?php echo getSiteName(); ?> offers an exceptional blend of luxury, comfort, and authentic African hospitality. Our commitment to excellence ensures that every guest experiences the warmth and beauty of Malawi in the most comfortable surroundings.</p>
                    <p>Whether you're visiting for business or leisure, our dedicated team is here to make your stay memorable. From our thoughtfully designed accommodations to our world-class dining experiences, every detail has been carefully considered to exceed your expectations.</p>
                    <a href="pages/about.php" class="btn-premium">Learn More</a>
                </div>
                
                <div class="about-image">
                    <img src="<?php echo getAssetUrl('images/hotel-exterior-1024x572.jpg'); ?>" alt="<?php echo getSiteName(); ?> Exterior">
                </div>
            </div>
        </div>
    </section>

    <!-- Facilities -->
    <section class="facilities-section">
        <div class="container">
            <div class="section-header">
                <h2>Hotel Facilities</h2>
                <p>Enjoy our premium amenities and services</p>
            </div>
            
            <div class="facilities-grid">
                <div class="facility-card">
                    <div class="facility-icon">
                        <i class="fas fa-swimming-pool"></i>
                    </div>
                    <h3>Swimming Pool</h3>
                    <p>Refresh yourself in our infinity pool overlooking the beautiful landscape</p>
                </div>
                
                <div class="facility-card">
                    <div class="facility-icon">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <h3>Fine Dining</h3>
                    <p>Experience culinary excellence at our award-winning restaurant</p>
                </div>
                
                <div class="facility-card">
                    <div class="facility-icon">
                        <i class="fas fa-spa"></i>
                    </div>
                    <h3>Spa & Wellness</h3>
                    <p>Relax and rejuvenate with our range of spa treatments</p>
                </div>
                
                <div class="facility-card">
                    <div class="facility-icon">
                        <i class="fas fa-car"></i>
                    </div>
                    <h3>Free Parking</h3>
                    <p>Complimentary parking available for all guests</p>
                </div>
                
                <div class="facility-card">
                    <div class="facility-icon">
                        <i class="fas fa-wifi"></i>
                    </div>
                    <h3>Free WiFi</h3>
                    <p>High-speed internet access throughout the property</p>
                </div>
                
                <div class="facility-card">
                    <div class="facility-icon">
                        <i class="fas fa-concierge-bell"></i>
                    </div>
                    <h3>24/7 Service</h3>
                    <p>Round-the-clock assistance for your convenience</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="testimonials-section">
        <div class="container">
            <div class="section-header">
                <h2>Guest Experiences</h2>
                <p>What our valued guests say about us</p>
            </div>
            
            <div class="testimonials-grid">
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        <p>"The staff at <?php echo getSiteName(); ?> went above and beyond to make our stay memorable. The rooms were luxurious and the view was breathtaking."</p>
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">
                            <img src="<?php echo getAssetUrl('images/hotel-exterior-150x150.jpg'); ?>" alt="Sarah Johnson">
                        </div>
                        <div class="author-info">
                            <h4>Sarah Johnson</h4>
                            <span>Travel Enthusiast</span>
                        </div>
                    </div>
                </div>
                
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        <p>"Perfect location for exploring Malawi. The restaurant served delicious local cuisine and the pool area was incredibly relaxing."</p>
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">
                            <img src="<?php echo getAssetUrl('images/pool-area-150x150.jpg'); ?>" alt="Michael Chen">
                        </div>
                        <div class="author-info">
                            <h4>Michael Chen</h4>
                            <span>Business Traveler</span>
                        </div>
                    </div>
                </div>
                
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        <p>"Exceptional service and attention to detail. The staff made sure our every need was met. Highly recommend this hotel!"</p>
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">
                            <img src="<?php echo getAssetUrl('images/fitness-center-150x150.jpg'); ?>" alt="Emma Rodriguez">
                        </div>
                        <div class="author-info">
                            <h4>Emma Rodriguez</h4>
                            <span>Vacationer</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Newsletter -->
    <section class="newsletter-section">
        <div class="container">
            <div class="newsletter-content">
                <div class="newsletter-text">
                    <h3>Stay Updated</h3>
                    <p>Subscribe to our newsletter for exclusive offers and updates</p>
                </div>
                <form class="newsletter-form" action="<?php echo getFormAction('newsletter'); ?>" method="post">
                    <input type="hidden" name="newsletter" value="1">
                    <input type="email" name="email" placeholder="Enter your email address" required>
                    <button type="submit" class="btn-premium">Subscribe</button>
                </form>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <div class="logo">
                        <img src="<?php echo getAssetUrl('images/logo-white.png'); ?>" alt="<?php echo getSiteName(); ?> Logo">
                        <span><?php echo getSiteName(); ?></span>
                    </div>
                    <p>Your premier destination in Malawi for luxury accommodation and unforgettable experiences.</p>
                    <div class="social-links">
                        <a href="<?php echo SOCIAL_FACEBOOK; ?>"><i class="fab fa-facebook-f"></i></a>
                        <a href="<?php echo SOCIAL_TWITTER; ?>"><i class="fab fa-twitter"></i></a>
                        <a href="<?php echo SOCIAL_INSTAGRAM; ?>"><i class="fab fa-instagram"></i></a>
                        <a href="<?php echo SOCIAL_LINKEDIN; ?>"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>

                <div class="footer-col">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="pages/about.php">About Us</a></li>
                        <li><a href="pages/rooms.php">Rooms</a></li>
                        <li><a href="pages/facilities.php">Facilities</a></li>
                        <li><a href="pages/gallery.php">Gallery</a></li>
                        <li><a href="pages/contact.php">Contact</a></li>
                        <li><a href="pages/booking.php">Book Now</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>Contact Info</h4>
                    <ul class="contact-info">
                        <li><i class="fas fa-map-marker-alt"></i> <span><?php echo CONTACT_ADDRESS; ?></span></li>
                        <li><i class="fas fa-phone"></i> <span><?php echo CONTACT_PHONE; ?></span></li>
                        <li><i class="fas fa-envelope"></i> <span><?php echo SITE_EMAIL; ?></span></li>
                        <li><i class="fas fa-clock"></i> <span><?php echo FRONT_DESK_HOURS; ?></span></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>Newsletter</h4>
                    <p>Subscribe to receive special offers and updates</p>
                    <form class="footer-form" action="<?php echo getFormAction('newsletter'); ?>" method="post">
                        <input type="hidden" name="newsletter" value="1">
                        <input type="email" name="email" placeholder="Your email address" required>
                        <button type="submit" class="btn-premium">Subscribe</button>
                    </form>
                </div>
            </div>

            <div class="footer-bottom">
                <p>2026 <?php echo getSiteName(); ?>. All rights reserved.</p>
                <div class="footer-links">
                    <a href="#">Privacy Policy</a>
                    <a href="#">Terms of Service</a>
                    <a href="#">Sitemap</a>
                </div>
            </div>
        </div>
        <div class="footer-decoration"></div>
        <div class="footer-decoration"></div>
    </footer>

    <!-- JavaScript -->
    <script src="<?php echo getResourcePath('js/main.js'); ?>"></script>
    
    <?php 
    displayDebugInfo();
    ?>
</body>
</html>