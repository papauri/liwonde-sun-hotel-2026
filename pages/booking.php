<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Your Stay | Liwonde Sun Hotel</title>
    <meta name="description" content="Book your stay at Liwonde Sun Hotel. Secure online reservations for the best rates and availability.">
    
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
                    <li><a href="contact.php">Contact</a></li>
                    <li><a href="booking.php" class="active btn-premium">Book Now</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Page Banner -->
    <section class="page-banner">
        <div class="banner-content">
            <h1>Book Your Stay</h1>
            <p>Reserve your perfect accommodation at Liwonde Sun Hotel</p>
        </div>
    </section>

    <!-- Booking Section -->
    <section class="booking-section">
        <div class="container">
            <div class="section-header">
                <h2>Make Your Reservation</h2>
                <p>Secure your stay with our easy booking process</p>
            </div>
            
            <div class="booking-container">
                <div class="booking-form-wrapper">
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
                                <label for="room-type">Room Type</label>
                                <select id="room-type" name="room-type" required>
                                    <option value="">Select Room Type</option>
                                    <option value="standard">Standard Room - $120/night</option>
                                    <option value="deluxe">Deluxe Room - $180/night</option>
                                    <option value="suite">Executive Suite - $280/night</option>
                                    <option value="family">Family Suite - $220/night</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="guests">Number of Guests</label>
                                <select id="guests" name="guests" required>
                                    <option value="1">1 Guest</option>
                                    <option value="2" selected>2 Guests</option>
                                    <option value="3">3 Guests</option>
                                    <option value="4">4 Guests</option>
                                    <option value="5">5 Guests</option>
                                    <option value="6">6 Guests</option>
                                </select>
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
                            <label for="special-requests">Special Requests</label>
                            <textarea id="special-requests" name="special-requests" rows="3" placeholder="Any special requests or requirements"></textarea>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="first-name">First Name</label>
                                <input type="text" id="first-name" name="first-name" placeholder="Enter your first name" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="last-name">Last Name</label>
                                <input type="text" id="last-name" name="last-name" placeholder="Enter your last name" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" placeholder="Enter your email address" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" placeholder="Enter your phone number" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="country">Country</label>
                            <select id="country" name="country" required>
                                <option value="">Select Country</option>
                                <option value="MW">Malawi</option>
                                <option value="ZA">South Africa</option>
                                <option value="ZW">Zimbabwe</option>
                                <option value="TZ">Tanzania</option>
                                <option value="KE">Kenya</option>
                                <option value="UG">Uganda</option>
                                <option value="US">United States</option>
                                <option value="GB">United Kingdom</option>
                                <option value="DE">Germany</option>
                                <option value="FR">France</option>
                                <option value="AU">Australia</option>
                                <option value="CN">China</option>
                                <option value="IN">India</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="newsletter" value="yes">
                                <span class="checkmark"></span>
                                Subscribe to our newsletter for special offers and updates
                            </label>
                        </div>
                        
                        <div class="form-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="terms" value="accepted" required>
                                <span class="checkmark"></span>
                                I agree to the <a href="#" class="terms-link">Terms and Conditions</a> and <a href="#" class="terms-link">Privacy Policy</a>
                            </label>
                        </div>
                        
                        <button type="submit" class="btn-premium btn-full">Complete Reservation</button>
                    </form>
                    
                    <div class="booking-summary">
                        <h3>Reservation Summary</h3>
                        <div class="summary-item">
                            <span>Room Type:</span>
                            <span id="summary-room">Not selected</span>
                        </div>
                        <div class="summary-item">
                            <span>Check-in:</span>
                            <span id="summary-checkin">Not selected</span>
                        </div>
                        <div class="summary-item">
                            <span>Check-out:</span>
                            <span id="summary-checkout">Not selected</span>
                        </div>
                        <div class="summary-item">
                            <span>Guests:</span>
                            <span id="summary-guests">2</span>
                        </div>
                        <div class="summary-item total">
                            <span>Total:</span>
                            <span id="summary-total">$0</span>
                        </div>
                        
                        <div class="payment-options">
                            <h4>Payment Methods Accepted</h4>
                            <div class="payment-icons">
                                <i class="fab fa-cc-visa"></i>
                                <i class="fab fa-cc-mastercard"></i>
                                <i class="fab fa-cc-amex"></i>
                                <i class="fab fa-cc-discover"></i>
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                        </div>
                        
                        <div class="booking-features">
                            <div class="feature">
                                <i class="fas fa-check-circle"></i>
                                <span>Free cancellation up to 48 hours</span>
                            </div>
                            <div class="feature">
                                <i class="fas fa-check-circle"></i>
                                <span>Best rate guarantee</span>
                            </div>
                            <div class="feature">
                                <i class="fas fa-check-circle"></i>
                                <span>Instant confirmation</span>
                            </div>
                            <div class="feature">
                                <i class="fas fa-check-circle"></i>
                                <span>24/7 customer support</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Special Offers -->
    <section class="offers-section">
        <div class="container">
            <div class="section-header">
                <h2>Special Offers</h2>
                <p>Take advantage of our exclusive deals</p>
            </div>
            
            <div class="offers-grid">
                <div class="offer-card">
                    <div class="offer-badge">SAVE 20%</div>
                    <h3>Early Bird Special</h3>
                    <p>Book 30 days in advance and save 20% on your stay. Perfect for those who plan ahead.</p>
                    <ul>
                        <li>20% discount on room rate</li>
                        <li>Free breakfast for two</li>
                        <li>Complimentary room upgrade</li>
                    </ul>
                    <a href="#" class="btn-premium">Book Now</a>
                </div>
                
                <div class="offer-card featured">
                    <div class="offer-badge">FEATURED</div>
                    <h3>Weekend Getaway</h3>
                    <p>Enjoy a relaxing weekend with our special weekend package deal.</p>
                    <ul>
                        <li>Third night free</li>
                        <li>Complimentary spa credit</li>
                        <li>Free airport transfer</li>
                    </ul>
                    <a href="#" class="btn-premium">Book Now</a>
                </div>
                
                <div class="offer-card">
                    <div class="offer-badge">MEMBERS ONLY</div>
                    <h3>Loyalty Package</h3>
                    <p>Exclusive benefits for our loyalty program members.</p>
                    <ul>
                        <li>15% discount on stays</li>
                        <li>Free room service</li>
                        <li>Priority check-in/check-out</li>
                    </ul>
                    <a href="#" class="btn-premium">Join Program</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Booking Policy -->
    <section class="policy-section">
        <div class="container">
            <div class="section-header">
                <h2>Booking Policies</h2>
                <p>Important information about your reservation</p>
            </div>
            
            <div class="policy-grid">
                <div class="policy-item">
                    <div class="policy-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h3>Check-in & Check-out</h3>
                    <p>Check-in time is 2:00 PM and check-out time is 11:00 AM. Early check-in and late check-out may be available upon request, subject to availability.</p>
                </div>
                
                <div class="policy-item">
                    <div class="policy-icon">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <h3>Payment Policy</h3>
                    <p>A valid credit card is required to secure your reservation. Full payment is due at the time of booking. We accept major credit cards and bank transfers.</p>
                </div>
                
                <div class="policy-item">
                    <div class="policy-icon">
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                    <h3>Cancellation Policy</h3>
                    <p>Cancellations made 48 hours prior to arrival are eligible for a full refund. Cancellations made within 48 hours of arrival will incur a penalty equal to one night's stay.</p>
                </div>
                
                <div class="policy-item">
                    <div class="policy-icon">
                        <i class="fas fa-paw"></i>
                    </div>
                    <h3>Pet Policy</h3>
                    <p>We welcome pets in designated rooms for an additional fee of $25 per night. Please inform us at the time of booking if you plan to travel with a pet.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Book Direct -->
    <section class="direct-booking-section">
        <div class="container">
            <div class="section-header">
                <h2>Why Book Direct?</h2>
                <p>Advantages of booking with us directly</p>
            </div>
            
            <div class="direct-booking-grid">
                <div class="direct-booking-item">
                    <div class="direct-booking-icon">
                        <i class="fas fa-tag"></i>
                    </div>
                    <h3>Best Rate Guarantee</h3>
                    <p>If you find a lower price elsewhere, we'll match it and give you an additional 10% discount.</p>
                </div>
                
                <div class="direct-booking-item">
                    <div class="direct-booking-icon">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <h3>Free Cancellation</h3>
                    <p>Change or cancel your reservation up to 48 hours before arrival at no extra cost.</p>
                </div>
                
                <div class="direct-booking-item">
                    <div class="direct-booking-icon">
                        <i class="fas fa-medal"></i>
                    </div>
                    <h3>Exclusive Member Benefits</h3>
                    <p>Access to member-only rates, upgrades, and special perks when you book directly.</p>
                </div>
                
                <div class="direct-booking-item">
                    <div class="direct-booking-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h3>24/7 Customer Support</h3>
                    <p>Speak directly with our team for immediate assistance with your reservation.</p>
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
        // Update booking summary when form fields change
        document.addEventListener('DOMContentLoaded', function() {
            const checkinInput = document.getElementById('checkin');
            const checkoutInput = document.getElementById('checkout');
            const roomTypeSelect = document.getElementById('room-type');
            const guestsSelect = document.getElementById('guests');
            const summaryRoom = document.getElementById('summary-room');
            const summaryCheckin = document.getElementById('summary-checkin');
            const summaryCheckout = document.getElementById('summary-checkout');
            const summaryGuests = document.getElementById('summary-guests');
            const summaryTotal = document.getElementById('summary-total');
            
            // Set minimum date to today
            const today = new Date().toISOString().split('T')[0];
            checkinInput.setAttribute('min', today);
            
            // Update summary when inputs change
            checkinInput.addEventListener('change', updateSummary);
            checkoutInput.addEventListener('change', updateSummary);
            roomTypeSelect.addEventListener('change', updateSummary);
            guestsSelect.addEventListener('change', updateSummary);
            
            function updateSummary() {
                // Update room type
                const roomType = roomTypeSelect.options[roomTypeSelect.selectedIndex].text;
                summaryRoom.textContent = roomType.split(' - ')[0] || 'Not selected';
                
                // Update dates
                summaryCheckin.textContent = checkinInput.value || 'Not selected';
                summaryCheckout.textContent = checkoutInput.value || 'Not selected';
                
                // Update guests
                summaryGuests.textContent = guestsSelect.value;
                
                // Calculate total
                if (checkinInput.value && checkoutInput.value && roomTypeSelect.value) {
                    const checkinDate = new Date(checkinInput.value);
                    const checkoutDate = new Date(checkoutInput.value);
                    
                    if (checkoutDate > checkinDate) {
                        const timeDiff = checkoutDate.getTime() - checkinDate.getTime();
                        const nights = Math.ceil(timeDiff / (1000 * 3600 * 24));
                        
                        // Extract price from room type option
                        const roomPriceMatch = roomType.match(/\$(\d+)\/night/);
                        const roomPrice = roomPriceMatch ? parseInt(roomPriceMatch[1]) : 0;
                        
                        const total = nights * roomPrice;
                        summaryTotal.textContent = `$${total}`;
                    } else {
                        summaryTotal.textContent = '$0';
                    }
                } else {
                    summaryTotal.textContent = '$0';
                }
            }
            
            // Set default check-in to tomorrow
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            checkinInput.value = tomorrow.toISOString().split('T')[0];
            
            // Set default check-out to day after tomorrow
            const dayAfterTomorrow = new Date();
            dayAfterTomorrow.setDate(dayAfterTomorrow.getDate() + 2);
            checkoutInput.value = dayAfterTomorrow.toISOString().split('T')[0];
            
            // Initialize summary
            updateSummary();
        });
    </script>
</body>
</html>