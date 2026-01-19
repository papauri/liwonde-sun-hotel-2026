<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accommodations | Liwonde Sun Hotel</title>
    <meta name="description" content="Explore our luxurious rooms and suites at Liwonde Sun Hotel. Book your perfect accommodation in Malawi.">
    
    <!-- Favicon -->
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="rooms-page">
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
                    <li><a href="rooms.php" class="active">Rooms</a></li>
                    <li><a href="facilities.php">Facilities</a></li>
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
            <h1>Our Accommodations</h1>
            <p>Discover our range of luxurious rooms and suites</p>
        </div>
    </section>

    <!-- Room Categories -->
    <section class="rooms-section">
        <div class="container">
            <div class="section-header">
                <h2>Choose Your Perfect Stay</h2>
                <p>Each room category offers unique amenities and views</p>
            </div>
            
            <div class="rooms-list">
                <div class="room-category">
                    <div class="room-image">
                        <img src="../images/hotel-exterior-1024x572.jpg" alt="Standard Room">
                    </div>
                    <div class="room-details">
                        <div class="room-header">
                            <h3>Standard Room</h3>
                            <div class="room-price">
                                <span class="price">$120<span>/night</span></span>
                            </div>
                        </div>
                        <div class="room-features">
                            <div class="room-feature">
                                <i class="fas fa-bed"></i>
                                <span>King-size Bed</span>
                            </div>
                            <div class="room-feature">
                                <i class="fas fa-ruler-combined"></i>
                                <span>25 m²</span>
                            </div>
                            <div class="room-feature">
                                <i class="fas fa-wifi"></i>
                                <span>Free WiFi</span>
                            </div>
                            <div class="room-feature">
                                <i class="fas fa-tv"></i>
                                <span>Smart TV</span>
                            </div>
                            <div class="room-feature">
                                <i class="fas fa-utensils"></i>
                                <span>Daily Breakfast</span>
                            </div>
                        </div>
                        <p>Our Standard Rooms offer comfort and convenience with all essential amenities. Perfect for solo travelers or couples looking for a cozy retreat after exploring the wonders of Malawi.</p>
                        <div class="room-amenities">
                            <h4>Included Amenities</h4>
                            <ul>
                                <li><i class="fas fa-check-circle"></i> Air conditioning</li>
                                <li><i class="fas fa-check-circle"></i> Private bathroom</li>
                                <li><i class="fas fa-check-circle"></i> Daily housekeeping</li>
                                <li><i class="fas fa-check-circle"></i> Tea & coffee facilities</li>
                                <li><i class="fas fa-check-circle"></i> Desk workspace</li>
                            </ul>
                        </div>
                        <a href="booking.php" class="btn-premium">Book Now</a>
                    </div>
                </div>
                
                <div class="room-category">
                    <div class="room-image">
                        <img src="../images/hotel-lobby-1024x572.jpg" alt="Deluxe Room">
                    </div>
                    <div class="room-details">
                        <div class="room-header">
                            <h3>Deluxe Room</h3>
                            <div class="room-price">
                                <span class="price">$180<span>/night</span></span>
                            </div>
                        </div>
                        <div class="room-features">
                            <div class="room-feature">
                                <i class="fas fa-bed"></i>
                                <span>King-size Bed</span>
                            </div>
                            <div class="room-feature">
                                <i class="fas fa-ruler-combined"></i>
                                <span>35 m²</span>
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
                                <i class="fas fa-coffee"></i>
                                <span>Mini Bar & Coffee Maker</span>
                            </div>
                            <div class="room-feature">
                                <i class="fas fa-mountain"></i>
                                <span>Scenic View</span>
                            </div>
                        </div>
                        <p>Deluxe Rooms provide extra space and upgraded amenities. Enjoy panoramic views of the surrounding landscape from your private balcony, along with premium bedding and luxury toiletries.</p>
                        <div class="room-amenities">
                            <h4>Included Amenities</h4>
                            <ul>
                                <li><i class="fas fa-check-circle"></i> Air conditioning</li>
                                <li><i class="fas fa-check-circle"></i> Private bathroom with bathtub</li>
                                <li><i class="fas fa-check-circle"></i> Daily housekeeping</li>
                                <li><i class="fas fa-check-circle"></i> Mini bar & coffee station</li>
                                <li><i class="fas fa-check-circle"></i> Balcony with scenic view</li>
                                <li><i class="fas fa-check-circle"></i> Premium toiletries</li>
                            </ul>
                        </div>
                        <a href="booking.php" class="btn-premium">Book Now</a>
                    </div>
                </div>
                
                <div class="room-category">
                    <div class="room-image">
                        <img src="../images/pool-area-1024x683.jpg" alt="Executive Suite">
                    </div>
                    <div class="room-details">
                        <div class="room-header">
                            <h3>Executive Suite</h3>
                            <div class="room-price">
                                <span class="price">$280<span>/night</span></span>
                            </div>
                        </div>
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
                                <i class="fas fa-ruler-combined"></i>
                                <span>60 m²</span>
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
                        </div>
                        <p>Our Executive Suites offer the ultimate luxury experience with separate living and sleeping areas. Enjoy premium amenities, personalized service, and breathtaking views from your private terrace.</p>
                        <div class="room-amenities">
                            <h4>Premium Amenities</h4>
                            <ul>
                                <li><i class="fas fa-check-circle"></i> King-size bed with premium linens</li>
                                <li><i class="fas fa-check-circle"></i> Separate living and dining areas</li>
                                <li><i class="fas fa-check-circle"></i> Private terrace with panoramic views</li>
                                <li><i class="fas fa-check-circle"></i> Two smart TVs with streaming services</li>
                                <li><i class="fas fa-check-circle"></i> Fully stocked wet bar</li>
                                <li><i class="fas fa-check-circle"></i> Priority check-in/check-out</li>
                                <li><i class="fas fa-check-circle"></i> 24/7 butler service</li>
                                <li><i class="fas fa-check-circle"></i> Luxury bath amenities</li>
                            </ul>
                        </div>
                        <a href="booking.php" class="btn-premium">Book Now</a>
                    </div>
                </div>
                
                <div class="room-category">
                    <div class="room-image">
                        <img src="../images/fitness-center-1024x683.jpg" alt="Family Suite">
                    </div>
                    <div class="room-details">
                        <div class="room-header">
                            <h3>Family Suite</h3>
                            <div class="room-price">
                                <span class="price">$220<span>/night</span></span>
                            </div>
                        </div>
                        <div class="room-features">
                            <div class="room-feature">
                                <i class="fas fa-bed"></i>
                                <span>King-size Bed + Twin Beds</span>
                            </div>
                            <div class="room-feature">
                                <i class="fas fa-ruler-combined"></i>
                                <span>50 m²</span>
                            </div>
                            <div class="room-feature">
                                <i class="fas fa-wifi"></i>
                                <span>Free WiFi</span>
                            </div>
                            <div class="room-feature">
                                <i class="fas fa-tv"></i>
                                <span>TV in Each Bedroom</span>
                            </div>
                            <div class="room-feature">
                                <i class="fas fa-child"></i>
                                <span>Kids-friendly Amenities</span>
                            </div>
                            <div class="room-feature">
                                <i class="fas fa-gamepad"></i>
                                <span>Children Activities</span>
                            </div>
                        </div>
                        <p>Designed for families, our Family Suites offer spacious accommodation with separate sleeping areas for parents and children. Includes child-friendly amenities and entertainment options.</p>
                        <div class="room-amenities">
                            <h4>Family-Friendly Amenities</h4>
                            <ul>
                                <li><i class="fas fa-check-circle"></i> King-size bed for parents + twin beds for children</li>
                                <li><i class="fas fa-check-circle"></i> Separate bedrooms for privacy</li>
                                <li><i class="fas fa-check-circle"></i> TV in each bedroom</li>
                                <li><i class="fas fa-check-circle"></i> Kids-friendly bathroom amenities</li>
                                <li><i class="fas fa-check-circle"></i> Child safety features</li>
                                <li><i class="fas fa-check-circle"></i> Game console & toys</li>
                                <li><i class="fas fa-check-circle"></i> Children's menu options</li>
                                <li><i class="fas fa-check-circle"></i> Babysitting services available</li>
                            </ul>
                        </div>
                        <a href="booking.php" class="btn-premium">Book Now</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Amenities Section -->
    <section class="amenities-section">
        <div class="container">
            <div class="section-header">
                <h2>Room Amenities</h2>
                <p>All rooms include these premium amenities</p>
            </div>
            
            <div class="amenities-grid">
                <div class="amenity-card">
                    <div class="amenity-icon">
                        <i class="fas fa-wifi"></i>
                    </div>
                    <h3>Free WiFi</h3>
                    <p>High-speed internet access throughout your room</p>
                </div>
                
                <div class="amenity-card">
                    <div class="amenity-icon">
                        <i class="fas fa-concierge-bell"></i>
                    </div>
                    <h3>24/7 Room Service</h3>
                    <p>Enjoy meals in the comfort of your room anytime</p>
                </div>
                
                <div class="amenity-card">
                    <div class="amenity-icon">
                        <i class="fas fa-tshirt"></i>
                    </div>
                    <h3>Daily Housekeeping</h3>
                    <p>Professional cleaning service every day</p>
                </div>
                
                <div class="amenity-card">
                    <div class="amenity-icon">
                        <i class="fas fa-snowflake"></i>
                    </div>
                    <h3>Air Conditioning</h3>
                    <p>Climate control for your comfort</p>
                </div>
                
                <div class="amenity-card">
                    <div class="amenity-icon">
                        <i class="fas fa-shower"></i>
                    </div>
                    <h3>Luxury Toiletries</h3>
                    <p>Premium bathroom amenities</p>
                </div>
                
                <div class="amenity-card">
                    <div class="amenity-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>Safe Deposit Box</h3>
                    <p>Secure storage for valuables</p>
                </div>
                
                <div class="amenity-card">
                    <div class="amenity-icon">
                        <i class="fas fa-glass-whiskey"></i>
                    </div>
                    <h3>Minibar</h3>
                    <p>Refreshments and beverages (in select rooms)</p>
                </div>
                
                <div class="amenity-card">
                    <div class="amenity-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <h3>Direct Dial Phone</h3>
                    <p>International calling capabilities</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Booking Section -->
    <section class="booking-section" id="booking-section">
        <div class="container">
            <div class="section-header">
                <h2>Book Your Stay</h2>
                <p>Reserve your perfect accommodation today</p>
            </div>
            
            <div class="booking-form-container">
                <form id="booking-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="checkin">Check-in Date</label>
                            <input type="date" id="checkin" name="checkin" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="checkout">Check-out Date</label>
                            <input type="date" id="checkout" name="checkout" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="adults">Adults</label>
                            <select id="adults" name="adults" required>
                                <option value="1">1 Adult</option>
                                <option value="2" selected>2 Adults</option>
                                <option value="3">3 Adults</option>
                                <option value="4">4 Adults</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="children">Children</label>
                            <select id="children" name="children">
                                <option value="0" selected>No Children</option>
                                <option value="1">1 Child</option>
                                <option value="2">2 Children</option>
                                <option value="3">3 Children</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="room-type">Room Type</label>
                        <select id="room-type" name="room-type" required>
                            <option value="" disabled selected>Select a room type</option>
                            <option value="standard">Standard Room</option>
                            <option value="deluxe">Deluxe Room</option>
                            <option value="suite">Executive Suite</option>
                            <option value="family">Family Suite</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="special-requests">Special Requests</label>
                        <textarea id="special-requests" name="special-requests" rows="3" placeholder="Any special requests or requirements"></textarea>
                    </div>
                    
                    <button type="submit" class="btn-primary">Check Availability & Book</button>
                </form>
                
                <div class="booking-info">
                    <h3>Why Book Direct?</h3>
                    <ul>
                        <li><i class="fas fa-check-circle"></i> Best Rate Guarantee</li>
                        <li><i class="fas fa-check-circle"></i> Free Cancellation</li>
                        <li><i class="fas fa-check-circle"></i> Exclusive Member Benefits</li>
                        <li><i class="fas fa-check-circle"></i> 24/7 Customer Support</li>
                        <li><i class="fas fa-check-circle"></i> Flexible Payment Options</li>
                    </ul>
                    
                    <div class="contact-info">
                        <h4>Need Assistance?</h4>
                        <p>Call us at <strong>+265 123 456 789</strong> or email us at <strong>reservations@liwondesunhotel.com</strong></p>
                    </div>
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