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
                    <li class="nav-item"><a href="/index.php#contact" class="nav-link"><span class="link-text">Contact</span></a></li>
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

     <style>
     @media (max-width: 1024px) {
         /* Ensure the header (and toggle button) stays above the full-screen menu */
         .header { overflow: visible !important; }
         /* Use flex so nav items stay visible; logo is absolutely centered */
         .navbar { position: relative !important; display: flex !important; justify-content: space-between !important; align-items: center !important; padding: 0 12px !important; }
         /* Reduce inactive nav-menu stacking so header remains visible; when active, menu will be above header */
         .nav-menu { z-index: 900 !important; position: fixed !important; top: 0 !important; left: 0 !important; width: 100vw !important; height: 100vh !important; }
         .nav-menu.active { z-index: 10002 !important; }
         .mobile-menu-overlay { z-index: 10001 !important; }

         /* Position logo text at center */
         .logo-text {
             position: absolute !important;
             left: 50% !important;
             transform: translateX(-50%) !important;
             z-index: 10003 !important;
             pointer-events: auto !important; /* keep logo clickable */
         }

         /* Move logo icon to top left */
         .logo-hotel-icon {
             position: absolute !important;
             left: 12px !important;
             top: 12px !important;
             transform: none !important;
             z-index: 10003 !important;
             pointer-events: auto !important;
         }

         /* Ensure container keeps space for button and doesn't collapse */
         .header .container { padding-left: 12px !important; padding-right: 12px !important; }

         /* Move mobile menu button to the right */
         .mobile-menu-btn { 
             position: relative !important; 
             z-index: 10004 !important;
             margin-left: auto !important; /* Push to the right */
         }

         /* For 769px-1024px: Show horizontal navbar instead of full-screen */
         @media (min-width: 769px) and (max-width: 1024px) {
             .nav-menu {
                 display: flex !important;
                 position: relative !important;
                 top: auto !important;
                 right: auto !important;
                 bottom: auto !important;
                 left: auto !important;
                 width: auto !important;
                 height: auto !important;
                 max-width: none !important;
                 flex-direction: row !important;
                 gap: 5px !important;
                 background: linear-gradient(90deg, rgba(10, 25, 41, 0.8) 0%, rgba(15, 30, 50, 0.75) 100%) !important;
                 padding: 0 20px !important;
                 box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2) !important;
                 z-index: auto !important;
                 transform: none !important;
                 border-radius: 50px !important;
                 border: 1px solid rgba(212, 175, 55, 0.15) !important;
                 backdrop-filter: blur(10px) !important;
                 overflow: visible !important;
             }

             .nav-menu::before {
                 display: none !important;
             }

             .nav-item {
                 position: relative;
             }

             .nav-link {
                 display: block;
                 padding: 10px 16px !important;
                 color: rgba(251, 248, 243, 0.9) !important;
                 font-size: 13px;
                 font-weight: 500;
                 letter-spacing: 1.5px;
                 text-transform: uppercase;
                 position: relative;
                 transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
             }

             .link-text {
                 position: relative;
                 z-index: 2;
             }

             .nav-link::before {
                 content: '';
                 position: absolute;
                 bottom: 5px;
                 left: 16px;
                 right: 16px;
                 height: 2px;
                 background: linear-gradient(90deg, transparent, var(--gold), transparent);
                 transform: scaleX(0);
                 transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
             }

             .nav-link:hover::before,
             .nav-link.active::before {
                 transform: scaleX(1);
             }

             .nav-link:hover {
                 background: rgba(212, 175, 55, 0.12);
                 color: var(--gold);
                 transform: translateY(-2px);
             }

             .nav-link.active {
                 background: rgba(212, 175, 55, 0.15);
                 color: var(--gold);
             }

             .nav-link::after {
                 content: '';
                 position: absolute;
                 top: 50%;
                 left: 50%;
                 transform: translate(-50%, -50%);
                 width: 0;
                 height: 0;
                 border-radius: 50%;
                 background: radial-gradient(circle, rgba(212, 175, 55, 0.15) 0%, transparent 70%);
                 transition: all 0.5s ease;
                 z-index: 1;
                 opacity: 0;
             }

             .nav-link:hover::after {
                 width: 120%;
                 height: 200%;
                 opacity: 1;
             }

             .nav-item-cta {
                 margin-left: 15px;
             }

             .nav-cta {
                 display: inline-flex;
                 align-items: center;
                 gap: 8px;
                 padding: 10px 22px !important;
                 background: linear-gradient(135deg, var(--gold) 0%, var(--dark-gold) 100%);
                 color: var(--navy) !important;
                 font-size: 12px;
                 font-weight: 700;
                 letter-spacing: 1.5px;
                 text-transform: uppercase;
                 border-radius: 50px;
                 border: 2px solid rgba(255, 255, 255, 0.2);
                 box-shadow: 0 4px 20px rgba(212, 175, 55, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.3);
                 position: relative;
                 overflow: hidden;
                 transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
             }

             .nav-cta::before {
                 content: none !important;
             }

             .nav-cta::after {
                 content: '';
                 position: absolute;
                 top: 50%;
                 left: 50%;
                 transform: translate(-50%, -50%);
                 width: 0;
                 height: 0;
                 background: radial-gradient(circle, rgba(255, 255, 255, 0.4) 0%, transparent 70%);
                 border-radius: 50%;
                 transition: width 0.6s, height 0.6s;
             }

             .nav-cta:hover::after {
                 width: 300px;
                 height: 300px;
             }

             .nav-cta:hover {
                 transform: translateY(-2px);
                 box-shadow: 0 8px 30px rgba(212, 175, 55, 0.6), inset 0 1px 0 rgba(255, 255, 255, 0.4);
                 border-color: rgba(255, 255, 255, 0.4);
             }

             .nav-cta:active {
                 transform: translateY(0);
             }

             /* Don't hide mobile menu button on tablets - it's still needed */
             .mobile-menu-btn {
                 display: flex !important;
                 align-items: center;
                 justify-content: center;
                 width: 48px;
                 height: 48px;
                 background: linear-gradient(135deg, rgba(212, 175, 55, 0.2) 0%, rgba(212, 175, 55, 0.1) 100%);
                 border: 2px solid rgba(212, 175, 55, 0.6);
                 border-radius: 12px;
                 cursor: pointer;
                 transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
                 position: relative;
                 z-index: 10004;
                 box-shadow: 0 4px 16px rgba(212, 175, 55, 0.3), inset 0 1px 0 rgba(255, 255, 255, 0.1);
             }

             /* Hide absolute positioning overrides for tablets when horizontal nav is shown */
             .logo-text {
                 position: relative !important;
                 left: auto !important;
                 transform: none !important;
                 margin: 0 !important;
                 pointer-events: auto !important;
             }

             .logo-hotel-icon {
                 position: relative !important;
                 left: auto !important;
                 top: auto !important;
                 margin-right: 8px !important;
                 margin-left: 0 !important;
                 pointer-events: auto !important;
             }
         }

         /* When menu is closed it's translated off-screen; active class handles visible menu */
     }
     </style>