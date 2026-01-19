<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | Liwonde Sun Hotel</title>
    <meta name="description" content="Learn about Liwonde Sun Hotel, our story, mission, and commitment to providing exceptional hospitality in Malawi.">
    
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
                    <li><a href="about.php" class="active">About</a></li>
                    <li><a href="rooms.php">Rooms</a></li>
                    <li><a href="facilities.php">Facilities</a></li>
                    <li><a href="gallery.php">Gallery</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <li><a href="booking.php" class="btn-book">Book Now</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Page Banner -->
    <section class="page-banner">
        <div class="banner-content">
            <h1>About Us</h1>
            <p>Discover our story and commitment to excellence</p>
        </div>
    </section>

    <!-- About Content -->
    <section class="about-content-section">
        <div class="container">
            <div class="about-content-wrapper">
                <div class="about-text">
                    <h2>Our Story</h2>
                    <p>Liwonde Sun Hotel was founded with a vision to provide exceptional hospitality in the heart of Malawi. Located near the beautiful Liwonde National Park, our hotel combines luxury accommodations with authentic African experiences.</p>
                    
                    <p>Established in 2010, we have grown from a small boutique establishment to one of Malawi's premier destinations for travelers seeking comfort, adventure, and cultural immersion. Our journey has been guided by a passion for hospitality and a deep respect for the natural beauty that surrounds us.</p>
                    
                    <h3>Our Mission</h3>
                    <p>To provide our guests with unforgettable experiences that showcase the best of Malawian culture, hospitality, and natural beauty, while maintaining the highest standards of service and comfort.</p>
                    
                    <h3>Our Vision</h3>
                    <p>To be recognized as the leading luxury hotel destination in Malawi, known for our exceptional service, sustainable practices, and contribution to the local community.</p>
                    
                    <h3>Our Values</h3>
                    <div class="values-grid">
                        <div class="value-card">
                            <div class="value-icon">
                                <i class="fas fa-heart"></i>
                            </div>
                            <h4>Hospitality</h4>
                            <p>We believe in the warmth of Malawian hospitality and strive to make every guest feel welcome.</p>
                        </div>
                        
                        <div class="value-card">
                            <div class="value-icon">
                                <i class="fas fa-leaf"></i>
                            </div>
                            <h4>Sustainability</h4>
                            <p>We are committed to preserving the natural environment and supporting local communities.</p>
                        </div>
                        
                        <div class="value-card">
                            <div class="value-icon">
                                <i class="fas fa-star"></i>
                            </div>
                            <h4>Excellence</h4>
                            <p>We continuously strive to exceed our guests' expectations in every aspect of our service.</p>
                        </div>
                        
                        <div class="value-card">
                            <div class="value-icon">
                                <i class="fas fa-handshake"></i>
                            </div>
                            <h4>Integrity</h4>
                            <p>We conduct our business with honesty, transparency, and respect for all stakeholders.</p>
                        </div>
                    </div>

                    <div class="about-highlights">
                        <div class="highlight-item">
                            <i class="fas fa-award"></i>
                            <div class="highlight-content">
                                <h4>5-Star Rating</h4>
                                <p>Recognized for excellence in hospitality and service</p>
                            </div>
                        </div>
                        <div class="highlight-item">
                            <i class="fas fa-globe-africa"></i>
                            <div class="highlight-content">
                                <h4>Authentic Experience</h4>
                                <p>Immerse yourself in genuine Malawian culture</p>
                            </div>
                        </div>
                        <div class="highlight-item">
                            <i class="fas fa-leaf"></i>
                            <div class="highlight-content">
                                <h4>Eco-Friendly</h4>
                                <p>Committed to sustainable tourism practices</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="about-image">
                    <img src="../images/hotel-exterior-1024x572.jpg" alt="Liwonde Sun Hotel Exterior">
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="team-section">
        <div class="container">
            <div class="section-header">
                <h2>Meet Our Leadership Team</h2>
                <p>Experience professionals dedicated to your comfort</p>
            </div>
            
            <div class="team-grid">
                <div class="team-member">
                    <div class="member-image">
                        <img src="../images/hotel-lobby-768x429.jpg" alt="John Manda - General Manager">
                    </div>
                    <div class="member-info">
                        <h3>John Manda</h3>
                        <h4>General Manager</h4>
                        <p>With over 15 years in hospitality management, John brings extensive experience in luxury hotel operations.</p>
                        <div class="social-links">
                            <a href="#"><i class="fab fa-facebook-f"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                </div>
                
                <div class="team-member">
                    <div class="member-image">
                        <img src="../images/pool-area-768x512.jpg" alt="Grace Tembo - Operations Director">
                    </div>
                    <div class="member-info">
                        <h3>Grace Tembo</h3>
                        <h4>Operations Director</h4>
                        <p>Grace oversees daily operations ensuring seamless guest experiences and efficient service delivery.</p>
                        <div class="social-links">
                            <a href="#"><i class="fab fa-facebook-f"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                </div>
                
                <div class="team-member">
                    <div class="member-image">
                        <img src="../images/fitness-center-768x512.jpg" alt="David Phiri - Chef de Cuisine">
                    </div>
                    <div class="member-info">
                        <h3>David Phiri</h3>
                        <h4>Chef de Cuisine</h4>
                        <p>David creates culinary experiences that blend international techniques with local flavors.</p>
                        <div class="social-links">
                            <a href="#"><i class="fab fa-facebook-f"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Awards Section -->
    <section class="awards-section">
        <div class="container">
            <div class="section-header">
                <h2>Our Recognition</h2>
                <p>Awards and certifications we've earned</p>
            </div>
            
            <div class="awards-grid">
                <div class="award-card">
                    <div class="award-icon">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <h3>Malawi Tourism Award</h3>
                    <p>Best Luxury Hotel 2023</p>
                </div>
                
                <div class="award-card">
                    <div class="award-icon">
                        <i class="fas fa-award"></i>
                    </div>
                    <h3>Green Tourism Certification</h3>
                    <p>Sustainable Practices 2022</p>
                </div>
                
                <div class="award-card">
                    <div class="award-icon">
                        <i class="fas fa-medal"></i>
                    </div>
                    <h3>Guest Choice Award</h3>
                    <p>Top 10 Hotels in Malawi 2023</p>
                </div>
                
                <div class="award-card">
                    <div class="award-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>Health & Safety</h3>
                    <p>Certified COVID-19 Safe 2021-2024</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2>Ready to Experience Luxury?</h2>
                <p>Book your stay at Liwonde Sun Hotel and discover the beauty of Malawi</p>
                <a href="pages/booking.php" class="btn-premium">Book Your Stay</a>
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