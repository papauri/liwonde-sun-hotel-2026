<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facilities & Services | Liwonde Sun Hotel</title>
    <meta name="description" content="Explore the premium facilities and services at Liwonde Sun Hotel. From spa to dining, enjoy luxury in Malawi.">
    
    <!-- Favicon -->
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <nav class="navbar">
                <div class="logo">
                    <a href="../index.php">
                        <img src="../images/logo.png" alt="<?php echo getSiteName(); ?> Logo">
                        <span><?php echo getSiteName(); ?></span>
                    </a>
                </div>

                <div class="nav-toggle" id="mobile-menu">
                    <span class="bar"></span>
                    <span class="bar"></span>
                    <span class="bar"></span>
                </div>

                <ul class="nav-menu">
                    <li><a href="../index.php">Home</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="rooms.php">Rooms</a></li>
                    <li><a href="facilities.php" class="active">Facilities</a></li>
                    <li><a href="gallery.php">Gallery</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <li><a href="booking.php" class="btn-premium">Book Now</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Page Banner -->
    <section class="page-banner">
        <div class="banner-content">
            <h1>Our Facilities & Services</h1>
            <p>Experience luxury amenities and exceptional services</p>
        </div>
    </section>

    <!-- Main Facilities -->
    <section class="facilities-main">
        <div class="container">
            <div class="section-header">
                <h2>Premium Facilities</h2>
                <p>Enjoy our world-class amenities designed for your comfort</p>
            </div>
            
            <div class="facilities-grid">
                <div class="facility-item">
                    <div class="facility-image">
                        <img src="../images/pool-area-1024x683.jpg" alt="Swimming Pool">
                    </div>
                    <div class="facility-content">
                        <h3>Infinity Swimming Pool</h3>
                        <p>Take a refreshing dip in our temperature-controlled infinity pool while enjoying panoramic views of the surrounding landscape. The pool area features comfortable loungers, umbrellas, and a poolside bar serving refreshing cocktails and light snacks.</p>
                        <div class="facility-features">
                            <div class="feature">
                                <i class="fas fa-clock"></i>
                                <span>Open 6:00 AM - 10:00 PM</span>
                            </div>
                            <div class="feature">
                                <i class="fas fa-thermometer-half"></i>
                                <span>Temperature controlled</span>
                            </div>
                            <div class="feature">
                                <i class="fas fa-umbrella-beach"></i>
                                <span>Poolside service</span>
                            </div>
                        </div>
                        <div class="facility-details">
                            <h4>Pool Amenities</h4>
                            <ul>
                                <li><i class="fas fa-check-circle"></i> Temperature-controlled water</li>
                                <li><i class="fas fa-check-circle"></i> Panoramic views of the landscape</li>
                                <li><i class="fas fa-check-circle"></i> Comfortable sun loungers</li>
                                <li><i class="fas fa-check-circle"></i> Poolside bar & restaurant</li>
                                <li><i class="fas fa-check-circle"></i> Towel service</li>
                                <li><i class="fas fa-check-circle"></i> Lifeguard on duty</li>
                                <li><i class="fas fa-check-circle"></i> Swimming lessons available</li>
                                <li><i class="fas fa-check-circle"></i> Pool games & activities</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="facility-item reverse">
                    <div class="facility-image">
                        <img src="../images/hotel-lobby-1024x572.jpg" alt="Restaurant">
                    </div>
                    <div class="facility-content">
                        <h3>Signature Restaurant</h3>
                        <p>Indulge in culinary excellence at our signature restaurant featuring a fusion of international and local Malawian cuisine. Our talented chefs prepare dishes using fresh, locally-sourced ingredients. The restaurant offers indoor dining and a scenic outdoor terrace.</p>
                        <div class="facility-features">
                            <div class="feature">
                                <i class="fas fa-clock"></i>
                                <span>Breakfast: 6:30-10:30 AM</span>
                            </div>
                            <div class="feature">
                                <i class="fas fa-utensils"></i>
                                <span>Lunch: 12:00-3:00 PM</span>
                            </div>
                            <div class="feature">
                                <i class="fas fa-wine-bottle"></i>
                                <span>Dinner: 6:00-10:00 PM</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="facility-item">
                    <div class="facility-image">
                        <img src="../images/fitness-center-1024x683.jpg" alt="Spa & Wellness Center">
                    </div>
                    <div class="facility-content">
                        <h3>Spa & Wellness Center</h3>
                        <p>Rejuvenate your body and mind at our tranquil spa offering a range of treatments inspired by traditional African wellness practices. Our skilled therapists provide massages, facials, and holistic treatments using natural products.</p>
                        <div class="facility-features">
                            <div class="feature">
                                <i class="fas fa-clock"></i>
                                <span>Open 9:00 AM - 8:00 PM</span>
                            </div>
                            <div class="feature">
                                <i class="fas fa-user-md"></i>
                                <span>Qualified therapists</span>
                            </div>
                            <div class="feature">
                                <i class="fas fa-leaf"></i>
                                <span>Natural products</span>
                            </div>
                        </div>
                        <div class="facility-details">
                            <h4>Spa Treatments</h4>
                            <ul>
                                <li><i class="fas fa-check-circle"></i> Traditional African massage</li>
                                <li><i class="fas fa-check-circle"></i> Aromatherapy sessions</li>
                                <li><i class="fas fa-check-circle"></i> Facial treatments</li>
                                <li><i class="fas fa-check-circle"></i> Body wraps & scrubs</li>
                                <li><i class="fas fa-check-circle"></i> Yoga & meditation</li>
                                <li><i class="fas fa-check-circle"></i> Sauna & steam room</li>
                                <li><i class="fas fa-check-circle"></i> Couples therapy packages</li>
                                <li><i class="fas fa-check-circle"></i> Wellness consultations</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="facility-item reverse">
                    <div class="facility-image">
                        <img src="../images/conference-hall-768x429.jpg" alt="Conference Center">
                    </div>
                    <div class="facility-content">
                        <h3>Conference & Events Center</h3>
                        <p>Host your business meetings, conferences, or special events in our state-of-the-art facilities equipped with modern technology. Our event spaces accommodate groups from 10 to 200 people with customizable setups and professional event coordination.</p>
                        <div class="facility-features">
                            <div class="feature">
                                <i class="fas fa-users"></i>
                                <span>Capacity: Up to 200 people</span>
                            </div>
                            <div class="feature">
                                <i class="fas fa-projector"></i>
                                <span>AV equipment included</span>
                            </div>
                            <div class="feature">
                                <i class="fas fa-concierge-bell"></i>
                                <span>Event planning services</span>
                            </div>
                        </div>
                        <div class="facility-details">
                            <h4>Event Services</h4>
                            <ul>
                                <li><i class="fas fa-check-circle"></i> Capacity for 10-200 guests</li>
                                <li><i class="fas fa-check-circle"></i> Professional AV equipment</li>
                                <li><i class="fas fa-check-circle"></i> Customizable room setups</li>
                                <li><i class="fas fa-check-circle"></i> Catering services available</li>
                                <li><i class="fas fa-check-circle"></i> Dedicated event coordinator</li>
                                <li><i class="fas fa-check-circle"></i> High-speed WiFi throughout</li>
                                <li><i class="fas fa-check-circle"></i> Business center access</li>
                                <li><i class="fas fa-check-circle"></i> Parking for attendees</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Additional Services -->
    <section class="services-section">
        <div class="container">
            <div class="section-header">
                <h2>Additional Services</h2>
                <p>Enhance your stay with our premium services</p>
            </div>
            
            <div class="services-grid">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-car"></i>
                    </div>
                    <h3>Airport Transfer</h3>
                    <p>Convenient pickup and drop-off service from and to the airport. Professional drivers and comfortable vehicles ensure a smooth start and end to your journey.</p>
                </div>
                
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-dumbbell"></i>
                    </div>
                    <h3>Fitness Center</h3>
                    <p>Maintain your fitness routine in our fully-equipped gym featuring modern cardio and strength-training equipment with scenic views.</p>
                </div>
                
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <h3>Gift Shop</h3>
                    <p>Find unique souvenirs, local crafts, and essentials at our boutique gift shop located in the lobby area.</p>
                </div>
                
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-bus"></i>
                    </div>
                    <h3>Tour Desk</h3>
                    <p>Plan your adventures with our knowledgeable tour desk staff who can arrange safaris, cultural tours, and other excursions.</p>
                </div>
                
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-baby"></i>
                    </div>
                    <h3>Childcare</h3>
                    <p>Professional childcare services available upon request, allowing parents to enjoy some time alone.</p>
                </div>
                
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-dog"></i>
                    </div>
                    <h3>Pet-Friendly</h3>
                    <p>Travel with your furry friends in designated pet-friendly rooms with special amenities.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Dining Options -->
    <section class="dining-section" id="dining">
        <div class="container">
            <div class="section-header">
                <h2>Dining Experiences</h2>
                <p>Discover our diverse culinary offerings</p>
            </div>
            
            <div class="dining-grid">
                <div class="dining-item">
                    <div class="dining-image">
                        <img src="../images/hotel-lobby-1024x572.jpg" alt="Main Restaurant">
                    </div>
                    <div class="dining-content">
                        <h3>Main Restaurant</h3>
                        <p>Our elegant main restaurant serves breakfast, lunch, and dinner featuring international cuisine with local influences. The menu changes seasonally to incorporate the freshest ingredients.</p>
                        <div class="dining-hours">
                            <h4>Opening Hours:</h4>
                            <p>Monday - Sunday: 6:30 AM - 10:00 PM</p>
                        </div>
                    </div>
                </div>
                
                <div class="dining-item">
                    <div class="dining-image">
                        <img src="../images/pool-area-1024x683.jpg" alt="Lounge Bar">
                    </div>
                    <div class="dining-content">
                        <h3>Lounge Bar</h3>
                        <p>Relax in our sophisticated lounge bar offering an extensive selection of wines, spirits, and cocktails. The perfect spot for afternoon tea or evening drinks with live acoustic music.</p>
                        <div class="dining-hours">
                            <h4>Opening Hours:</h4>
                            <p>Monday - Sunday: 11:00 AM - 12:00 AM</p>
                        </div>
                    </div>
                </div>
                
                <div class="dining-item">
                    <div class="dining-image">
                        <img src="../images/pool-area-768x512.jpg" alt="Pool Bar">
                    </div>
                    <div class="dining-content">
                        <h3>Pool Bar</h3>
                        <p>Enjoy refreshing drinks and light snacks without leaving the pool area. Our pool bar serves tropical cocktails, fresh juices, and quick bites throughout the day.</p>
                        <div class="dining-hours">
                            <h4>Opening Hours:</h4>
                            <p>Monday - Sunday: 10:00 AM - 6:00 PM</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Activities -->
    <section class="activities-section">
        <div class="container">
            <div class="section-header">
                <h2>Activities & Excursions</h2>
                <p>Explore the beauty of Malawi and surrounding areas</p>
            </div>
            
            <div class="activities-grid">
                <div class="activity-card">
                    <div class="activity-icon">
                        <i class="fas fa-tree"></i>
                    </div>
                    <h3>Liwonde National Park Safari</h3>
                    <p>Experience the incredible wildlife of Liwonde National Park with our guided safari tours. Spot elephants, lions, leopards, and numerous bird species.</p>
                    <a href="pages/booking.php" class="btn-book">Book Tour</a>
                </div>
                
                <div class="activity-card">
                    <div class="activity-icon">
                        <i class="fas fa-water"></i>
                    </div>
                    <h3>Shire River Cruise</h3>
                    <p>Enjoy a scenic boat cruise along the Shire River, part of the Lower Zambezi ecosystem, with opportunities for fishing and wildlife viewing.</p>
                    <a href="pages/booking.php" class="btn-book">Book Tour</a>
                </div>
                
                <div class="activity-card">
                    <div class="activity-icon">
                        <i class="fas fa-mountain"></i>
                    </div>
                    <h3>Mount Soche Hiking</h3>
                    <p>Embark on guided hikes up Mount Soche for breathtaking panoramic views of the Shire Highlands and surrounding landscapes.</p>
                    <a href="pages/booking.php" class="btn-book">Book Tour</a>
                </div>
                
                <div class="activity-card">
                    <div class="activity-icon">
                        <i class="fas fa-ship"></i>
                    </div>
                    <h3>Lake Malawi Day Trip</h3>
                    <p>Visit the crystal-clear waters of Lake Malawi for swimming, snorkeling, and exploring local villages (seasonal availability).</p>
                    <a href="pages/booking.php" class="btn-book">Book Tour</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <div class="logo">
                        <img src="../images/logo-white.png" alt="Liwonde Sun Hotel Logo">
                        <span>Liwonde Sun Hotel</span>
                    </div>
                    <p>Your premier destination in Malawi for luxury accommodation and unforgettable experiences.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                
                <div class="footer-col">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="../index.php">Home</a></li>
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="rooms.php">Rooms</a></li>
                        <li><a href="facilities.php">Facilities</a></li>
                        <li><a href="gallery.php">Gallery</a></li>
                        <li><a href="contact.php">Contact</a></li>
                        <li><a href="booking.php">Book Now</a></li>
                    </ul>
                </div>
                
                <div class="footer-col">
                    <h4>Contact Info</h4>
                    <ul class="contact-info">
                        <li><i class="fas fa-map-marker-alt"></i> <span>Liwonde, Malawi</span></li>
                        <li><i class="fas fa-phone"></i> <span>+265 123 456 789</span></li>
                        <li><i class="fas fa-envelope"></i> <span>info@liwondesunhotel.com</span></li>
                        <li><i class="fas fa-clock"></i> <span>Open 24/7</span></li>
                    </ul>
                </div>
                
                <div class="footer-col">
                    <h4>Newsletter</h4>
                    <p>Subscribe to receive special offers and updates</p>
                    <form class="footer-form" action="../process.php" method="post">
                        <input type="hidden" name="newsletter" value="1">
                        <input type="email" name="email" placeholder="Your email address" required>
                        <button type="submit" class="btn-premium">Subscribe</button>
                    </form>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>2026 Liwonde Sun Hotel. All rights reserved.</p>
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
    <script src="js/main.js"></script>
</body>
</html>