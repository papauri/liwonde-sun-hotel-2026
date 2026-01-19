<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us | Liwonde Sun Hotel</title>
    <meta name="description" content="Get in touch with Liwonde Sun Hotel. Contact us for reservations, inquiries, and more.">
    
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
                    <li><a href="facilities.php">Facilities</a></li>
                    <li><a href="gallery.php">Gallery</a></li>
                    <li><a href="contact.php" class="active">Contact</a></li>
                    <li><a href="booking.php" class="btn-premium">Book Now</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Page Banner -->
    <section class="page-banner">
        <div class="banner-content">
            <h1>Contact Us</h1>
            <p>We'd love to hear from you. Reach out to us for any inquiries.</p>
        </div>
    </section>

    <!-- Contact Information -->
    <section class="contact-info-section">
        <div class="container">
            <div class="section-header">
                <h2>Get In Touch</h2>
                <p>Reach out to us for reservations, inquiries, or feedback</p>
            </div>
            
            <div class="contact-grid">
                <div class="contact-card">
                    <div class="contact-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h3>Location</h3>
                    <p>Liwonde National Park, Malawi</p>
                    <p>P.O. Box 1234, Blantyre, Malawi</p>
                </div>
                
                <div class="contact-card">
                    <div class="contact-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <h3>Phone</h3>
                    <p>Reservations: +265 123 456 789</p>
                    <p>General Inquiry: +265 987 654 321</p>
                </div>
                
                <div class="contact-card">
                    <div class="contact-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <h3>Email</h3>
                    <p>info@liwondesunhotel.com</p>
                    <p>reservations@liwondesunhotel.com</p>
                </div>
                
                <div class="contact-card">
                    <div class="contact-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3>Opening Hours</h3>
                    <p>Front Desk: 24/7</p>
                    <p>Restaurant: 6:30 AM - 10:00 PM</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Form -->
    <section class="contact-form-section">
        <div class="container">
            <div class="section-header">
                <h2>Send Us a Message</h2>
                <p>Fill out the form below and we'll get back to you as soon as possible</p>
            </div>
            
            <div class="contact-form-container">
                <form id="contact-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Full Name</label>
                            <input type="text" id="name" name="name" placeholder="Enter your full name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" placeholder="Enter your email" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" placeholder="Enter your phone number">
                        </div>
                        
                        <div class="form-group">
                            <label for="subject">Subject</label>
                            <input type="text" id="subject" name="subject" placeholder="Enter subject" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" rows="6" placeholder="Enter your message" required></textarea>
                    </div>
                    
                    <button type="submit" class="btn-primary">Send Message</button>
                </form>
                
                <div class="contact-info">
                    <h3>Other Ways to Reach Us</h3>
                    <p>For immediate assistance, you can reach our front desk at any time. Our team is available 24/7 to assist with reservations, inquiries, and special requests.</p>
                    
                    <div class="contact-methods">
                        <div class="method">
                            <i class="fas fa-comment"></i>
                            <div>
                                <h4>Live Chat</h4>
                                <p>Chat with our support team during business hours</p>
                            </div>
                        </div>
                        
                        <div class="method">
                            <i class="fas fa-video"></i>
                            <div>
                                <h4>Video Call</h4>
                                <p>Schedule a video consultation for group bookings</p>
                            </div>
                        </div>
                        
                        <div class="method">
                            <i class="fas fa-mobile-alt"></i>
                            <div>
                                <h4>WhatsApp</h4>
                                <p>Send us a WhatsApp message: +265 123 456 789</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Map Section -->
    <section class="map-section">
        <div class="container">
            <div class="section-header">
                <h2>Find Us</h2>
                <p>Visit us at Liwonde Sun Hotel</p>
            </div>
            
            <div class="map-container">
                <!-- Note: In a real implementation, you would embed a Google Maps iframe here -->
                <div class="map-placeholder">
                    <div class="map-content">
                        <h3>Liwonde Sun Hotel Location</h3>
                        <p>Located in the heart of Liwonde National Park, Malawi</p>
                        <p>Approximately 2 hours from Lilongwe International Airport</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq-section">
        <div class="container">
            <div class="section-header">
                <h2>Frequently Asked Questions</h2>
                <p>Common questions about our hotel and services</p>
            </div>
            
            <div class="faq-container">
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>How do I make a reservation?</h3>
                        <i class="fas fa-plus"></i>
                    </div>
                    <div class="faq-answer">
                        <p>You can make a reservation through our website, by calling our reservations line at +265 123 456 789, or by sending an email to reservations@liwondesunhotel.com. We recommend booking in advance, especially during peak seasons.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>What is the check-in and check-out time?</h3>
                        <i class="fas fa-plus"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Check-in time is 2:00 PM and check-out time is 11:00 AM. Early check-in and late check-out may be available upon request, subject to availability.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Is Wi-Fi available?</h3>
                        <i class="fas fa-plus"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Yes, complimentary high-speed Wi-Fi is available throughout the hotel, including all guest rooms, common areas, and meeting spaces.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Are pets allowed?</h3>
                        <i class="fas fa-plus"></i>
                    </div>
                    <div class="faq-answer">
                        <p>We are a pet-friendly hotel. Pets are welcome in designated rooms for an additional fee. Please inform us at the time of booking if you plan to travel with a pet.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>What activities are available nearby?</h3>
                        <i class="fas fa-plus"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Our location near Liwonde National Park offers excellent opportunities for wildlife safaris, bird watching, and nature walks. We also offer guided tours to local cultural sites, fishing trips on the Shire River, and hiking excursions to Mount Soche.</p>
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
    <script>
        // FAQ accordion functionality
        document.addEventListener('DOMContentLoaded', function() {
            const faqQuestions = document.querySelectorAll('.faq-question');
            
            faqQuestions.forEach(question => {
                question.addEventListener('click', function() {
                    const faqItem = this.parentElement;
                    const answer = faqItem.querySelector('.faq-answer');
                    const icon = this.querySelector('i');
                    
                    // Toggle answer visibility
                    if (answer.style.maxHeight) {
                        answer.style.maxHeight = null;
                        icon.classList.remove('fa-minus');
                        icon.classList.add('fa-plus');
                    } else {
                        answer.style.maxHeight = answer.scrollHeight + 'px';
                        icon.classList.remove('fa-plus');
                        icon.classList.add('fa-minus');
                    }
                });
            });
        });
    </script>
</body>
</html>