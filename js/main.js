/**
 * Liwonde Sun Hotel - Main JavaScript
 * Premium Interactions & Animations
 */

// Page Loader
window.addEventListener('load', function() {
    const loader = document.getElementById('page-loader');
    if (loader) {
        setTimeout(() => {
            loader.classList.add('hidden');
        }, 800);
    }
});

// Show loader on page navigation
document.addEventListener('click', function(e) {
    const link = e.target.closest('a');
    if (link && link.href && !link.href.startsWith('#') && !link.target && link.href.startsWith(window.location.origin)) {
        const loader = document.getElementById('page-loader');
        if (loader) {
            loader.classList.remove('hidden');
        }
    }
});

document.addEventListener('DOMContentLoaded', function() {
        // Room Featured Image AJAX Upload
        const imageUploadForm = document.getElementById('imageUploadForm');
        const currentImage = document.getElementById('currentImage');
        const currentImageContainer = document.getElementById('currentImageContainer');
        if (imageUploadForm) {
            imageUploadForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(imageUploadForm);
                // Debug: Log form data keys and values
                for (let pair of formData.entries()) {
                    console.log('FormData:', pair[0], pair[1]);
                }
                fetch('room-management.php', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(async res => {
                    let text = await res.text();
                    try {
                        let data = JSON.parse(text);
                        console.log('Upload response:', data);
                        if (data.success && data.image_url) {
                            if (currentImage) {
                                currentImage.src = '../' + data.image_url;
                                currentImageContainer.style.display = 'block';
                            }
                            alert('Image uploaded successfully!');
                        } else {
                            alert(data.message || 'Upload failed.');
                        }
                    } catch (err) {
                        console.error('Invalid JSON response:', text);
                        alert('Upload failed. Server error or invalid response.');
                    }
                })
                .catch((err) => {
                    console.error('Network or JS error:', err);
                    alert('Upload failed. Network or server error.');
                });
            });
        }
    
    // Time and Temperature Widget
    function updateTimeAndTemp() {
        const timeParts = new Intl.DateTimeFormat('en-GB', {
            timeZone: 'Africa/Blantyre',
            hour12: true,
            hour: '2-digit',
            minute: '2-digit'
        }).formatToParts(new Date());

        const hours = timeParts.find(p => p.type === 'hour')?.value || '--';
        const minutes = timeParts.find(p => p.type === 'minute')?.value || '--';
        const ampm = (timeParts.find(p => p.type === 'dayPeriod')?.value || 'AM').toUpperCase();
        
        // Desktop widget
        const timeDisplay = document.getElementById('heroTime');
        const ampmDisplay = document.getElementById('heroAmpm');
        
        if (timeDisplay) timeDisplay.textContent = `${hours}:${minutes}`;
        if (ampmDisplay) ampmDisplay.textContent = ampm;
        
        // Mobile widget
        const timeDisplayMobile = document.getElementById('heroTimeMobile');
        const ampmDisplayMobile = document.getElementById('heroAmpmMobile');
        
        if (timeDisplayMobile) timeDisplayMobile.textContent = `${hours}:${minutes}`;
        if (ampmDisplayMobile) ampmDisplayMobile.textContent = ampm;
        
        // Update Temperature (simulated with random value for demo)
        const tempDisplay = document.getElementById('heroTemp');
        const tempDisplayMobile = document.getElementById('heroTempMobile');
        
        if (tempDisplay || tempDisplayMobile) {
            const temp = Math.round(22 + Math.random() * 8); // 22-30°C range
            if (tempDisplay) tempDisplay.textContent = `${temp}°C`;
            if (tempDisplayMobile) tempDisplayMobile.textContent = `${temp}°C`;
        }
    }
    
    // Initial update
    updateTimeAndTemp();
    
    // Update every minute
    setInterval(updateTimeAndTemp, 60000);
    
    // Smooth scrolling for navigation links
    const navLinks = document.querySelectorAll('a[href^="#"]');
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const targetSection = document.querySelector(targetId);
            if (targetSection) {
                const headerOffset = 80;
                const elementPosition = targetSection.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - headerOffset;
                
                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });

    // Debug: capture menu link clicks and defaultPrevented state
    document.addEventListener('click', function(e) {
        const link = e.target.closest('a');
        if (link && link.closest('.nav-menu')) {
            console.log('[mobile-menu] capture click', {
                href: link.getAttribute('href'),
                defaultPrevented: e.defaultPrevented,
                cancelable: e.cancelable
            });
            setTimeout(() => {
                console.log('[mobile-menu] post-click location', window.location.href);
            }, 100);
        }
    }, true);
    
    // Supreme Premium Header Scroll Effect
    const header = document.querySelector('.header');
    let lastScroll = 0;
    
    window.addEventListener('scroll', function() {
        const currentScroll = window.pageYOffset;
        
        if (currentScroll > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
        
        lastScroll = currentScroll;
    });
    
    // Active nav link on scroll
    const sections = document.querySelectorAll('section[id]');
    const navItems = document.querySelectorAll('.nav-link');
    
    function highlightNavigation() {
        const scrollPos = window.pageYOffset + 100;
        
        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.offsetHeight;
            const sectionId = section.getAttribute('id');
            
            if (scrollPos >= sectionTop && scrollPos < sectionTop + sectionHeight) {
                navItems.forEach(link => {
                    link.classList.remove('active');
                    if (link.getAttribute('href') === '#' + sectionId) {
                        link.classList.add('active');
                    }
                });
            }
        });
    }
    
    window.addEventListener('scroll', highlightNavigation);
    
    // Intersection Observer for fade-in animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    // Observe all cards
    const cards = document.querySelectorAll('.room-card, .facility-card, .testimonial-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = `all 0.6s ease ${index * 0.1}s`;
        observer.observe(card);
    });

    // Scroll to Top Button
    const scrollToTopBtn = document.getElementById('scrollToTop');
    
    if (scrollToTopBtn) {
        // Show/hide button based on scroll position
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                scrollToTopBtn.classList.add('visible');
            } else {
                scrollToTopBtn.classList.remove('visible');
            }
        });
        
        // Scroll to top on click
        scrollToTopBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
    
    // Mobile menu functionality
    const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
    const navMenu = document.querySelector('.nav-menu');
    const mobileMenuOverlay = document.querySelector('.mobile-menu-overlay');

    if (mobileMenuBtn && navMenu) {
        const setMenuOpen = (open) => {
            navMenu.classList.toggle('active', open);
            mobileMenuBtn.classList.toggle('active', open);
            if (mobileMenuOverlay) mobileMenuOverlay.classList.toggle('active', open);

            mobileMenuBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
            mobileMenuBtn.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');

            document.body.style.overflow = open ? 'hidden' : '';

            if (open) {
                // Ensure menu is at top of screen and visible
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }

            console.log('[mobile-menu] setMenuOpen', {
                open,
                navMenuActive: navMenu.classList.contains('active'),
                overlayActive: mobileMenuOverlay ? mobileMenuOverlay.classList.contains('active') : null
            });
        };

        const isMenuOpen = () => navMenu.classList.contains('active');

        // Primary toggle
        mobileMenuBtn.addEventListener('click', function() {
            console.log('[mobile-menu] toggle button clicked');
            setMenuOpen(!isMenuOpen());
        });

        // Close menu when clicking overlay
        if (mobileMenuOverlay) {
            mobileMenuOverlay.addEventListener('click', function() {
                console.log('[mobile-menu] overlay clicked');
                setMenuOpen(false);
            });
        }

        // Close menu when clicking on a link
        navMenu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', function() {
                console.log('[mobile-menu] nav link clicked', this.getAttribute('href'));
                setMenuOpen(false);
            });
        });

        // Close on ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && isMenuOpen()) setMenuOpen(false);
        });

        // Safety: if we resize to desktop, force-close and unlock scroll
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 1191 && isMenuOpen()) setMenuOpen(false);
        });
    }
    
    // Hero carousel
    const heroSlides = document.querySelectorAll('.hero-slide');
    const heroIndicators = document.querySelectorAll('.hero-indicator');
    const prevBtns = document.querySelectorAll('.hero-prev');
    const nextBtns = document.querySelectorAll('.hero-next');
    let heroIndex = 0;
    let heroTimer;

    function setHeroSlide(index) {
        if (!heroSlides.length) return;
        heroIndex = (index + heroSlides.length) % heroSlides.length;
        heroSlides.forEach((slide, i) => {
            slide.classList.toggle('active', i === heroIndex);
        });
        heroIndicators.forEach((dot, i) => {
            dot.classList.toggle('active', i === heroIndex);
        });
    }

    function nextHeroSlide() {
        setHeroSlide(heroIndex + 1);
    }

    function prevHeroSlideFn() {
        setHeroSlide(heroIndex - 1);
    }

    function startHeroAuto() {
        if (heroTimer) clearInterval(heroTimer);
        heroTimer = setInterval(nextHeroSlide, 6000);
    }

    if (heroSlides.length) {
        startHeroAuto();
        setHeroSlide(0);

        heroIndicators.forEach(dot => {
            dot.addEventListener('click', () => {
                const index = parseInt(dot.dataset.index, 10);
                setHeroSlide(index);
                startHeroAuto();
            });
        });

        nextBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                nextHeroSlide();
                startHeroAuto();
            });
        });

        prevBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                prevHeroSlideFn();
                startHeroAuto();
            });
        });

        // Pause on hover (desktop)
        const heroSection = document.querySelector('.hero');
        if (heroSection) {
            heroSection.addEventListener('mouseenter', () => heroTimer && clearInterval(heroTimer));
            heroSection.addEventListener('mouseleave', startHeroAuto);
        }
    }
    
    // Add hover effect to room cards
    const roomCards = document.querySelectorAll('.room-card');
    roomCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
    
    // Lazy loading for images
    const images = document.querySelectorAll('img[data-src]');
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
                imageObserver.unobserve(img);
            }
        });
    });
    
    images.forEach(img => imageObserver.observe(img));

    // Policy modals - Using new Modal component
    const policyLinks = document.querySelectorAll('.policy-link');
    const policyOverlay = document.querySelector('[data-policy-overlay]');
    
    console.log('Policy links found:', policyLinks.length);
    console.log('Modal available:', typeof Modal);
    
    function openPolicy(slug) {
        const modalId = 'policy-' + slug;
        console.log('Opening policy modal:', modalId);
        const modal = document.getElementById(modalId);
        console.log('Modal element found:', !!modal);
        
        if (typeof Modal !== 'undefined' && Modal.open) {
            Modal.open(modalId);
        } else {
            console.error('Modal component not available');
        }
    }
    
    policyLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const slug = this.dataset.policy;
            console.log('Policy link clicked, slug:', slug);
            openPolicy(slug);
        });
    });
    
    if (policyOverlay) {
        policyOverlay.addEventListener('click', function() {
            if (typeof Modal !== 'undefined' && Modal.closeAll) {
                Modal.closeAll();
            }
        });
    }

    // Room Detail Carousel - DISABLED (Using Grid Layout Instead)
    // The carousel functionality has been replaced with a modern grid layout
    // in rooms-showcase.php. Keep this code commented out for reference.
    /*
    const roomCarousel = document.getElementById('roomCarousel');
    if (roomCarousel) {
        const slides = roomCarousel.querySelectorAll('.carousel-slide');
        const carouselDots = document.getElementById('carouselDots');
        const prevBtn = roomCarousel.querySelector('.carousel-prev');
        const nextBtn = roomCarousel.querySelector('.carousel-next');
        
        let currentSlide = 0;

        // Create dots
        slides.forEach((_, index) => {
            const dot = document.createElement('button');
            dot.className = `carousel-dot ${index === 0 ? 'active' : ''}`;
            dot.setAttribute('aria-label', `Go to slide ${index + 1}`);
            dot.addEventListener('click', () => goToSlide(index));
            carouselDots.appendChild(dot);
        });

        function updateCarousel() {
            slides.forEach(slide => slide.classList.remove('active'));
            carouselDots.querySelectorAll('.carousel-dot').forEach(dot => dot.classList.remove('active'));
            
            slides[currentSlide].classList.add('active');
            carouselDots.children[currentSlide].classList.add('active');
        }

        function goToSlide(index) {
            currentSlide = (index + slides.length) % slides.length;
            updateCarousel();
        }

        function nextSlide() {
            currentSlide = (currentSlide + 1) % slides.length;
            updateCarousel();
        }

        function prevSlide() {
            currentSlide = (currentSlide - 1 + slides.length) % slides.length;
            updateCarousel();
        }

        if (prevBtn) prevBtn.addEventListener('click', prevSlide);
        if (nextBtn) nextBtn.addEventListener('click', nextSlide);

        // Keyboard navigation
        document.addEventListener('keydown', (e) => {
            if (roomCarousel.offsetParent !== null) { // Only if visible
                if (e.key === 'ArrowLeft') prevSlide();
                if (e.key === 'ArrowRight') nextSlide();
            }
        });

        // Auto-advance carousel every 6 seconds
        setInterval(nextSlide, 6000);
    }
    */

    // Room Filter Chips
    const filterChips = document.querySelectorAll('.chip');
    const roomTiles = document.querySelectorAll('.room-tile');

    if (filterChips.length > 0 && roomTiles.length > 0) {
        filterChips.forEach(chip => {
            chip.addEventListener('click', function() {
                const filterValue = this.getAttribute('data-filter');
                
                // Update active chip
                filterChips.forEach(c => c.classList.remove('active'));
                this.classList.add('active');

                // Filter rooms
                roomTiles.forEach(tile => {
                    const tileFilter = tile.getAttribute('data-filter');
                    
                    // Show/hide based on filter
                    if (filterValue === 'all-rooms' || tileFilter.includes(filterValue)) {
                        tile.style.display = '';
                        // Trigger animation
                        setTimeout(() => {
                            tile.style.opacity = '1';
                            tile.style.transform = 'translateY(0)';
                        }, 10);
                    } else {
                        tile.style.opacity = '0';
                        tile.style.transform = 'translateY(10px)';
                        setTimeout(() => {
                            tile.style.display = 'none';
                        }, 300);
                    }
                });
            });
        });

        // Add transition styles to room tiles
        roomTiles.forEach(tile => {
            tile.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
            tile.style.opacity = '1';
            tile.style.transform = 'translateY(0)';
        });
    }
    
    // Hotel Gallery Carousel
    const galleryCarousel = document.querySelector('.gallery-carousel-track');
    const galleryItems = document.querySelectorAll('.gallery-carousel-item');
    const galleryPrevBtn = document.querySelector('.gallery-nav-prev');
    const galleryNextBtn = document.querySelector('.gallery-nav-next');
    const galleryDots = document.querySelectorAll('.gallery-dot');
    
    if (galleryCarousel && galleryItems.length > 0) {
        let currentGalleryIndex = 0;
        let itemsToShow = getItemsToShow();
        
        function getItemsToShow() {
            if (window.innerWidth <= 768) return 1;
            if (window.innerWidth <= 1024) return 3;
            return 4;
        }
        
        function updateCarousel(smooth = true) {
            const itemWidth = galleryItems[0].offsetWidth;
            const gap = 20;
            const offset = currentGalleryIndex * (itemWidth + gap);
            
            galleryCarousel.style.transition = smooth ? 'transform 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94)' : 'none';
            galleryCarousel.style.transform = `translateX(-${offset}px)`;
            
            // Update active dot
            galleryDots.forEach((dot, index) => {
                dot.classList.toggle('active', index === currentGalleryIndex);
            });
        }
        
        function nextSlide() {
            const maxIndex = Math.max(0, galleryItems.length - itemsToShow);
            currentGalleryIndex = Math.min(currentGalleryIndex + 1, maxIndex);
            updateCarousel();
        }
        
        function prevSlide() {
            currentGalleryIndex = Math.max(currentGalleryIndex - 1, 0);
            updateCarousel();
        }
        
        // Navigation buttons
        if (galleryNextBtn) {
            galleryNextBtn.addEventListener('click', nextSlide);
        }
        
        if (galleryPrevBtn) {
            galleryPrevBtn.addEventListener('click', prevSlide);
        }
        
        // Dot navigation
        galleryDots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                currentGalleryIndex = index;
                updateCarousel();
            });
        });
        
        // Auto-play carousel
        let autoplayInterval = setInterval(nextSlide, 4000);
        
        // Pause on hover
        const carouselWrapper = document.querySelector('.gallery-carousel-wrapper');
        if (carouselWrapper) {
            carouselWrapper.addEventListener('mouseenter', () => {
                clearInterval(autoplayInterval);
            });
            
            carouselWrapper.addEventListener('mouseleave', () => {
                autoplayInterval = setInterval(nextSlide, 4000);
            });
        }
        
        // Responsive resize handler
        let resizeTimer;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => {
                const newItemsToShow = getItemsToShow();
                if (newItemsToShow !== itemsToShow) {
                    itemsToShow = newItemsToShow;
                    currentGalleryIndex = Math.min(currentGalleryIndex, Math.max(0, galleryItems.length - itemsToShow));
                    updateCarousel(false);
                }
            }, 250);
        });
        
        // Touch/swipe support
        let touchStartX = 0;
        let touchEndX = 0;
        
        if (carouselWrapper) {
            carouselWrapper.addEventListener('touchstart', (e) => {
                touchStartX = e.touches[0].clientX;
            }, { passive: true });
            
            carouselWrapper.addEventListener('touchend', (e) => {
                touchEndX = e.changedTouches[0].clientX;
                const swipeDistance = touchStartX - touchEndX;
                
                if (Math.abs(swipeDistance) > 50) {
                    if (swipeDistance > 0) {
                        nextSlide();
                    } else {
                        prevSlide();
                    }
                }
            }, { passive: true });
        }
        
        // Initialize
        updateCarousel(false);
    }
    
    console.log('Liwonde Sun Hotel - Website loaded successfully');
});
