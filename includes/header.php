    <?php
    ?>
    <header class="header">
        <div class="container">
            <nav class="navbar">
                <!-- Mobile: Hotel icon on far left (separate element) -->
                <a href="/" class="logo-hotel-icon-link" aria-label="Go to home">
                    <svg class="logo-hotel-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2 22V6L12 2L22 6V22H14V16C14 15.4696 13.7893 14.9609 13.4142 14.5858C13.0391 14.2107 12.5304 14 12 14C11.4696 14 10.9609 14.2107 10.5858 14.5858C10.2107 14.9609 10 15.4696 10 16V22H2Z" stroke="url(#logoGradient)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M6 10H8M6 14H8M16 10H18M16 14H18" stroke="url(#logoGradient)" stroke-width="1.5" stroke-linecap="round"/>
                        <defs>
                            <linearGradient id="logoGradient" x1="2" y1="2" x2="22" y2="22">
                                <stop offset="0%" stop-color="#D4AF37"/>
                                <stop offset="50%" stop-color="#FFD700"/>
                                <stop offset="100%" stop-color="#B8860B"/>
                            </linearGradient>
                        </defs>
                    </svg>
                </a>
                
                <!-- Mobile: Logo text in middle (separate element) -->
                <a href="/" class="logo-text-link" aria-label="Go to home">
                    <span class="logo-text"><?php echo htmlspecialchars($site_name); ?></span>
                </a>
                
                <!-- Desktop: Original logo structure (hidden on mobile) -->
                <a href="/" class="logo" aria-label="Go to home">
                    <?php if (!empty($site_logo)): ?>
                    <img src="<?php echo htmlspecialchars($site_logo); ?>" alt="<?php echo htmlspecialchars($site_name); ?>" class="logo-image" />
                    <?php endif; ?>
                    <span class="logo-text"><?php echo htmlspecialchars($site_name); ?></span>
                    <svg class="logo-hotel-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2 22V6L12 2L22 6V22H14V16C14 15.4696 13.7893 14.9609 13.4142 14.5858C13.0391 14.2107 12.5304 14 12 14C11.4696 14 10.9609 14.2107 10.5858 14.5858C10.2107 14.9609 10 15.4696 10 16V22H2Z" stroke="url(#logoGradient)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M6 10H8M6 14H8M16 10H18M16 14H18" stroke="url(#logoGradient)" stroke-width="1.5" stroke-linecap="round"/>
                        <defs>
                            <linearGradient id="logoGradient2" x1="2" y1="2" x2="22" y2="22">
                                <stop offset="0%" stop-color="#D4AF37"/>
                                <stop offset="50%" stop-color="#FFD700"/>
                                <stop offset="100%" stop-color="#B8860B"/>
                            </linearGradient>
                        </defs>
                    </svg>
                </a>
                
                <?php
                // Determine current page for active nav highlighting - SIMPLE VERSION
                $current_file = basename($_SERVER['PHP_SELF']);
                
                // Function to check if nav link is active
                function is_nav_active($link) {
                    global $current_file;
                    
                    // Home link
                    if ($link === '/') {
                        return $current_file === 'index.php';
                    }
                    
                    // Extract filename from link
                    $link_file = basename($link);
                    
                    // Special case: room.php should activate "Rooms" nav link
                    if ($current_file === 'room.php' && $link_file === 'rooms-gallery.php') {
                        return true;
                    }
                    
                    // Direct comparison for all other pages
                    return $current_file === $link_file;
                }
                ?>
                
                <ul class="nav-menu" id="primary-nav">
                    <li class="nav-item"><a href="/" class="nav-link <?php echo is_nav_active('/') ? 'active' : ''; ?>"><span class="link-text">Home</span></a></li>
                    <li class="nav-item"><a href="/rooms-gallery.php" class="nav-link <?php echo is_nav_active('/rooms-gallery.php') ? 'active' : ''; ?>"><span class="link-text">Rooms</span></a></li>
                    <li class="nav-item"><a href="/restaurant.php" class="nav-link <?php echo is_nav_active('/restaurant.php') ? 'active' : ''; ?>"><span class="link-text">Restaurant</span></a></li>
                    <li class="nav-item"><a href="/gym.php" class="nav-link <?php echo is_nav_active('/gym.php') ? 'active' : ''; ?>"><span class="link-text">Gym</span></a></li>
                    <li class="nav-item"><a href="/conference.php" class="nav-link <?php echo is_nav_active('/conference.php') ? 'active' : ''; ?>"><span class="link-text">Conference</span></a></li>
                    <li class="nav-item"><a href="/events.php" class="nav-link <?php echo is_nav_active('/events.php') ? 'active' : ''; ?>"><span class="link-text">Events</span></a></li>
                    <li class="nav-item"><a href="#contact" class="nav-link contact-link"><span class="link-text">Contact</span></a></li>
                    <li class="nav-item nav-item-cta"><a href="/booking.php" class="nav-cta"><i class="fas fa-calendar-check"></i> Book Now</a></li>
                </ul>
                
                <button class="mobile-menu-btn" type="button" aria-controls="primary-nav" aria-expanded="false" aria-label="Open menu">
                    <span class="menu-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </button>
            </nav>
        </div>
    </header>

    <!-- Mobile Menu Overlay -->
    <div class="mobile-menu-overlay" role="presentation"></div>