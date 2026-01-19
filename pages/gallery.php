<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery | Liwonde Sun Hotel</title>
    <meta name="description" content="Explore our photo gallery showcasing the beautiful Liwonde Sun Hotel, rooms, facilities, and surrounding areas.">
    
    <!-- Favicon -->
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="gallery-page">
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
                    <li><a href="facilities.php">Facilities</a></li>
                    <li><a href="gallery.php" class="active">Gallery</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <li><a href="booking.php" class="btn-premium">Book Now</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Page Banner -->
    <section class="page-banner">
        <div class="banner-content">
            <h1>Photo Gallery</h1>
            <p>Experience the beauty of Liwonde Sun Hotel through our images</p>
        </div>
    </section>

    <!-- Gallery Section -->
    <section class="gallery-section">
        <div class="container">
            <div class="section-header">
                <h2>Discover Our Spaces</h2>
                <p>Explore our luxurious accommodations and beautiful surroundings</p>
            </div>
            
            <!-- Gallery Filters -->
            <div class="gallery-filters">
                <button class="filter-btn active" data-filter="all">All</button>
                <button class="filter-btn" data-filter="exterior">Exterior</button>
                <button class="filter-btn" data-filter="rooms">Rooms</button>
                <button class="filter-btn" data-filter="dining">Dining</button>
                <button class="filter-btn" data-filter="recreation">Recreation</button>
                <button class="filter-btn" data-filter="nature">Nature</button>
            </div>
            
            <!-- Gallery Grid -->
            <div class="gallery-grid">
                <!-- Exterior Images -->
                <div class="gallery-item" data-category="exterior">
                    <img src="../images/hotel-exterior-1024x572.jpg" alt="Liwonde Sun Hotel Exterior">
                    <div class="gallery-overlay">
                        <div class="gallery-info">
                            <h3>Hotel Exterior</h3>
                            <p>Beautiful entrance and architecture</p>
                        </div>
                        <div class="gallery-actions">
                            <a href="../images/hotel-exterior-1024x572.jpg" class="gallery-zoom" data-lightbox="gallery" data-title="Hotel Exterior">
                                <i class="fas fa-search-plus"></i>
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="gallery-item" data-category="exterior">
                    <img src="../images/hotel-lobby-1024x572.jpg" alt="Hotel Lobby">
                    <div class="gallery-overlay">
                        <div class="gallery-info">
                            <h3>Lobby Area</h3>
                            <p>Elegant reception and seating area</p>
                        </div>
                    </div>
                </div>
                
                <div class="gallery-item" data-category="exterior">
                    <img src="../images/pool-area-1024x683.jpg" alt="Pool Area">
                    <div class="gallery-overlay">
                        <div class="gallery-info">
                            <h3>Pool Area</h3>
                            <p>Infinity pool with scenic views</p>
                        </div>
                    </div>
                </div>
                
                <!-- Room Images -->
                <div class="gallery-item" data-category="rooms">
                    <img src="../images/hotel-exterior-768x429.jpg" alt="Standard Room">
                    <div class="gallery-overlay">
                        <div class="gallery-info">
                            <h3>Standard Room</h3>
                            <p>Comfortable and well-appointed</p>
                        </div>
                    </div>
                </div>
                
                <div class="gallery-item" data-category="rooms">
                    <img src="../images/hotel-lobby-768x429.jpg" alt="Deluxe Room">
                    <div class="gallery-overlay">
                        <div class="gallery-info">
                            <h3>Deluxe Room</h3>
                            <p>Spacious with premium amenities</p>
                        </div>
                    </div>
                </div>
                
                <div class="gallery-item" data-category="rooms">
                    <img src="../images/pool-area-768x512.jpg" alt="Executive Suite">
                    <div class="gallery-overlay">
                        <div class="gallery-info">
                            <h3>Executive Suite</h3>
                            <p>Luxurious suite with separate living area</p>
                        </div>
                    </div>
                </div>
                
                <div class="gallery-item" data-category="rooms">
                    <img src="images/bedroom-view.jpg" alt="Bedroom View">
                    <div class="gallery-overlay">
                        <div class="gallery-info">
                            <h3>Room View</h3>
                            <p>Scenic view from our rooms</p>
                        </div>
                    </div>
                </div>
                
                <div class="gallery-item" data-category="rooms">
                    <img src="images/bathroom-luxury.jpg" alt="Luxury Bathroom">
                    <div class="gallery-overlay">
                        <div class="gallery-info">
                            <h3>Luxury Bathroom</h3>
                            <p>Modern amenities and fixtures</p>
                        </div>
                    </div>
                </div>
                
                <!-- Dining Images -->
                <div class="gallery-item" data-category="dining">
                    <img src="images/restaurant-main.jpg" alt="Main Restaurant">
                    <div class="gallery-overlay">
                        <div class="gallery-info">
                            <h3>Main Restaurant</h3>
                            <p>Elegant dining experience</p>
                        </div>
                    </div>
                </div>
                
                <div class="gallery-item" data-category="dining">
                    <img src="images/restaurant-terrace.jpg" alt="Restaurant Terrace">
                    <div class="gallery-overlay">
                        <div class="gallery-info">
                            <h3>Restaurant Terrace</h3>
                            <p>Outdoor dining with scenic views</p>
                        </div>
                    </div>
                </div>
                
                <div class="gallery-item" data-category="dining">
                    <img src="images/lounge-bar.jpg" alt="Lounge Bar">
                    <div class="gallery-overlay">
                        <div class="gallery-info">
                            <h3>Lounge Bar</h3>
                            <p>Sophisticated evening atmosphere</p>
                        </div>
                    </div>
                </div>
                
                <div class="gallery-item" data-category="dining">
                    <img src="images/breakfast-table.jpg" alt="Breakfast Spread">
                    <div class="gallery-overlay">
                        <div class="gallery-info">
                            <h3>Breakfast Spread</h3>
                            <p>Delicious morning meal options</p>
                        </div>
                    </div>
                </div>
                
                <!-- Recreation Images -->
                <div class="gallery-item" data-category="recreation">
                    <img src="images/spa-treatment.jpg" alt="Spa Treatment Room">
                    <div class="gallery-overlay">
                        <div class="gallery-info">
                            <h3>Spa Treatment</h3>
                            <p>Tranquil wellness experience</p>
                        </div>
                    </div>
                </div>
                
                <div class="gallery-item" data-category="recreation">
                    <img src="../images/fitness-center-1024x683.jpg" alt="Fitness Center">
                    <div class="gallery-overlay">
                        <div class="gallery-info">
                            <h3>Fitness Center</h3>
                            <p>Well-equipped exercise facility</p>
                        </div>
                    </div>
                </div>
                
                <div class="gallery-item" data-category="recreation">
                    <img src="images/conference-hall.jpg" alt="Conference Hall">
                    <div class="gallery-overlay">
                        <div class="gallery-info">
                            <h3>Conference Hall</h3>
                            <p>Modern event facilities</p>
                        </div>
                    </div>
                </div>
                
                <div class="gallery-item" data-category="recreation">
                    <img src="images/garden-path.jpg" alt="Hotel Garden">
                    <div class="gallery-overlay">
                        <div class="gallery-info">
                            <h3>Hotel Garden</h3>
                            <p>Tranquil walking paths</p>
                        </div>
                    </div>
                </div>
                
                <!-- Nature Images -->
                <div class="gallery-item" data-category="nature">
                    <img src="images/nature-view1.jpg" alt="Nature View">
                    <div class="gallery-overlay">
                        <div class="gallery-info">
                            <h3>Natural Surroundings</h3>
                            <p>Beautiful landscape views</p>
                        </div>
                    </div>
                </div>
                
                <div class="gallery-item" data-category="nature">
                    <img src="images/nature-view2.jpg" alt="Wildlife Sighting">
                    <div class="gallery-overlay">
                        <div class="gallery-info">
                            <h3>Wildlife</h3>
                            <p>Nearby wildlife and fauna</p>
                        </div>
                    </div>
                </div>
                
                <div class="gallery-item" data-category="nature">
                    <img src="images/sunset-view.jpg" alt="Sunset View">
                    <div class="gallery-overlay">
                        <div class="gallery-info">
                            <h3>Sunset View</h3>
                            <p>Breathtaking evening skies</p>
                        </div>
                    </div>
                </div>
                
                <div class="gallery-item" data-category="nature">
                    <img src="images/morning-mist.jpg" alt="Morning Mist">
                    <div class="gallery-overlay">
                        <div class="gallery-info">
                            <h3>Morning Mist</h3>
                            <p>Peaceful early morning atmosphere</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Video Section -->
    <section class="video-section">
        <div class="container">
            <div class="section-header">
                <h2>Virtual Tour</h2>
                <p>Experience Liwonde Sun Hotel from the comfort of your home</p>
            </div>
            
            <div class="video-container">
                <div class="video-placeholder">
                    <img src="images/video-thumbnail.jpg" alt="Virtual Tour Thumbnail">
                    <div class="play-button">
                        <i class="fas fa-play"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Instagram Feed -->
    <section class="instagram-section">
        <div class="container">
            <div class="section-header">
                <h2>Follow Us</h2>
                <p>Share your experience with #LiwondeSunHotel</p>
            </div>
            
            <div class="instagram-grid">
                <div class="instagram-item">
                    <img src="images/insta1.jpg" alt="Instagram Post">
                    <div class="instagram-overlay">
                        <i class="fab fa-instagram"></i>
                        <span>Like & Follow</span>
                    </div>
                </div>
                
                <div class="instagram-item">
                    <img src="images/insta2.jpg" alt="Instagram Post">
                    <div class="instagram-overlay">
                        <i class="fab fa-instagram"></i>
                        <span>Like & Follow</span>
                    </div>
                </div>
                
                <div class="instagram-item">
                    <img src="images/insta3.jpg" alt="Instagram Post">
                    <div class="instagram-overlay">
                        <i class="fab fa-instagram"></i>
                        <span>Like & Follow</span>
                    </div>
                </div>
                
                <div class="instagram-item">
                    <img src="images/insta4.jpg" alt="Instagram Post">
                    <div class="instagram-overlay">
                        <i class="fab fa-instagram"></i>
                        <span>Like & Follow</span>
                    </div>
                </div>
                
                <div class="instagram-item">
                    <img src="images/insta5.jpg" alt="Instagram Post">
                    <div class="instagram-overlay">
                        <i class="fab fa-instagram"></i>
                        <span>Like & Follow</span>
                    </div>
                </div>
                
                <div class="instagram-item">
                    <img src="images/insta6.jpg" alt="Instagram Post">
                    <div class="instagram-overlay">
                        <i class="fab fa-instagram"></i>
                        <span>Like & Follow</span>
                    </div>
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
                <a href="pages/booking.php" class="btn-primary">Book Your Stay</a>
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
    <script>
        // Gallery filtering functionality
        document.addEventListener('DOMContentLoaded', function() {
            const filterButtons = document.querySelectorAll('.filter-btn');
            const galleryItems = document.querySelectorAll('.gallery-item');
            
            filterButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Remove active class from all buttons
                    filterButtons.forEach(btn => btn.classList.remove('active'));
                    
                    // Add active class to clicked button
                    this.classList.add('active');
                    
                    const filterValue = this.getAttribute('data-filter');
                    
                    galleryItems.forEach(item => {
                        if (filterValue === 'all' || item.getAttribute('data-category') === filterValue) {
                            item.style.display = 'block';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                });
            });
            
            // Lightbox functionality
            const galleryItems = document.querySelectorAll('.gallery-item');
            galleryItems.forEach(item => {
                item.addEventListener('click', function() {
                    const imgSrc = this.querySelector('img').getAttribute('src');
                    const caption = this.querySelector('.gallery-info h3').textContent + ' - ' + 
                                   this.querySelector('.gallery-info p').textContent;
                    
                    // Create lightbox overlay
                    const lightbox = document.createElement('div');
                    lightbox.className = 'lightbox';
                    lightbox.innerHTML = `
                        <div class="lightbox-content">
                            <span class="close-lightbox">&times;</span>
                            <img src="${imgSrc}" alt="${caption}">
                            <div class="lightbox-caption">${caption}</div>
                        </div>
                    `;
                    
                    document.body.appendChild(lightbox);
                    
                    // Close lightbox when clicking on close button or outside the image
                    const closeBtn = lightbox.querySelector('.close-lightbox');
                    closeBtn.addEventListener('click', function() {
                        document.body.removeChild(lightbox);
                    });
                    
                    lightbox.addEventListener('click', function(e) {
                        if (e.target === lightbox) {
                            document.body.removeChild(lightbox);
                        }
                    });
                });
            });
        });
    </script>
</body>
</html>