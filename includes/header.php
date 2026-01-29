    <header class="header">
        <div class="container">
            <nav class="navbar">
                <a href="/" class="logo" aria-label="Go to home">
                    <?php if (!empty($site_logo)): ?>
                    <img src="<?php echo htmlspecialchars($site_logo); ?>" alt="<?php echo htmlspecialchars($site_name); ?>" class="logo-image" />
                    <?php endif; ?>
                    <span class="logo-text"><?php echo htmlspecialchars($site_name); ?></span>
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
                
                <?php
                // Determine current page for active nav highlighting
                $request_uri = $_SERVER['REQUEST_URI'];
                // Remove query string and anchors
                $path = parse_url($request_uri, PHP_URL_PATH);
                $current_page = basename($path, '.php');
                if (empty($current_page) || $current_page === '') {
                    $current_page = 'index';
                }
                $current_page = strtolower($current_page);
                // Normalise common slug variants (e.g. room_gallery vs rooms-gallery)
                $current_page = str_replace('_', '-', $current_page);
                
                // Function to check if nav link is active
                function is_nav_active($link, $current) {
                    // Extract page name from link
                    $link_page = basename($link, '.php');
                    if (empty($link_page) || $link_page === '' || $link === '/') {
                        $link_page = 'index';
                    }
                    $link_page = strtolower($link_page);
                    
                    // Normalise common slug variants (e.g. room_gallery vs rooms-gallery)
                    $link_page = str_replace('_', '-', $link_page);
                    $current = str_replace('_', '-', strtolower($current));
                    
                    // If the nav link relates to Rooms, treat any room-related page as active.
                    // This covers variations like: rooms-gallery, rooms-showcase, room (detail), rooms_gallery, room_gallery, etc.
                    if (strpos($link_page, 'room') !== false) {
                        // consider it active if current page contains 'room' (covers room, rooms, room-gallery, rooms-showcase, etc.)
                        if (strpos($current, 'room') !== false) {
                            return true;
                        }
                    }
                    
                    // Default: exact match (with home special-case)
                    return $current === $link_page || ($current === 'index' && $link === '/');
                }
                ?>
                
                <ul class="nav-menu" id="primary-nav">
                    <li class="nav-item"><a href="/" class="nav-link <?php echo is_nav_active('/', $current_page) ? 'active' : ''; ?>"><span class="link-text">Home</span></a></li>
                    <li class="nav-item"><a href="/rooms-gallery.php" class="nav-link <?php echo is_nav_active('/rooms-gallery.php', $current_page) ? 'active' : ''; ?>"><span class="link-text">Rooms</span></a></li>
                    <li class="nav-item"><a href="/restaurant.php" class="nav-link <?php echo is_nav_active('/restaurant.php', $current_page) ? 'active' : ''; ?>"><span class="link-text">Restaurant</span></a></li>
                    <li class="nav-item"><a href="/conference.php" class="nav-link <?php echo is_nav_active('/conference.php', $current_page) ? 'active' : ''; ?>"><span class="link-text">Conference</span></a></li>
                    <li class="nav-item"><a href="/events.php" class="nav-link <?php echo is_nav_active('/events.php', $current_page) ? 'active' : ''; ?>"><span class="link-text">Events</span></a></li>
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