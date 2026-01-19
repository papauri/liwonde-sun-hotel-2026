// Mobile Navigation Toggle with WOW effects
document.addEventListener('DOMContentLoaded', function() {
    const navToggle = document.getElementById('mobile-menu');
    const navMenu = document.querySelector('.nav-menu');

    if (navToggle && navMenu) {
        navToggle.addEventListener('click', function() {
            navMenu.classList.toggle('active');
            navToggle.classList.toggle('active');

            // Add ripple effect to toggle button
            const ripple = document.createElement('span');
            ripple.classList.add('ripple');
            navToggle.appendChild(ripple);

            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    }

    // Close mobile menu when clicking on a link
    const navLinks = document.querySelectorAll('.nav-menu a');
    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            navMenu.classList.remove('active');
            navToggle.classList.remove('active');

            // Add ripple effect to clicked link
            const ripple = document.createElement('span');
            ripple.classList.add('ripple');
            link.appendChild(ripple);

            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });

    // Add mobile-specific animations for elements coming into view
    if ('IntersectionObserver' in window) {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    // Add animation classes based on element type
                    if (entry.target.classList.contains('room-card')) {
                        entry.target.style.animation = 'fadeInUp 0.8s ease forwards, floatCard 6s ease-in-out infinite';
                    } else if (entry.target.classList.contains('facility-card')) {
                        entry.target.style.animation = 'fadeInUp 0.8s ease 0.2s forwards, floatCard 8s ease-in-out infinite';
                    } else if (entry.target.classList.contains('testimonial-card')) {
                        entry.target.style.animation = 'fadeInUp 0.8s ease 0.4s forwards, floatCard 7s ease-in-out infinite';
                    } else if (entry.target.classList.contains('section-header')) {
                        entry.target.style.animation = 'fadeInDown 0.8s ease forwards, glowHeader 4s ease-in-out infinite';
                    }
                }
            });
        }, observerOptions);

        // Observe elements that should animate when in view
        document.querySelectorAll('.room-card, .facility-card, .testimonial-card, .section-header').forEach(el => {
            observer.observe(el);
        });
    }

    // Add mobile-specific touch interactions
    const touchElements = document.querySelectorAll('.room-card, .facility-card, .testimonial-card, .btn');
    touchElements.forEach(element => {
        element.addEventListener('touchstart', function() {
            this.style.transform = 'scale(0.98)';
        });

        element.addEventListener('touchend', function() {
            this.style.transform = '';
        });
    });

    // Header scroll effect
    const header = document.querySelector('.header');
    window.addEventListener('scroll', function() {
        if (window.scrollY > 100) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });

    // Hero Slider with enhanced animations
    const slides = document.querySelectorAll('.slide');
    const indicators = document.querySelectorAll('.indicator');
    const prevBtn = document.querySelector('.prev-slide');
    const nextBtn = document.querySelector('.next-slide');
    let currentSlide = 0;
    let slideInterval;

    function showSlide(index) {
        // Remove active class from all slides with animation
        slides.forEach((slide, i) => {
            if (i === index) {
                slide.classList.add('active');
                // Trigger animation on active slide content
                animateSlideContent(slide);
            } else {
                slide.classList.remove('active');
            }
        });

        indicators.forEach((indicator, i) => {
            indicator.classList.toggle('active', i === index);
        });

        currentSlide = index;
    }

    function animateSlideContent(slide) {
        const elements = slide.querySelectorAll('.slide-content > *');
        elements.forEach((el, i) => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(30px)';
            setTimeout(() => {
                el.style.transition = 'opacity 0.8s ease, transform 0.8s ease';
                el.style.opacity = '1';
                el.style.transform = 'translateY(0)';
            }, 150 * i);
        });
    }

    function nextSlide() {
        let nextIndex = (currentSlide + 1) % slides.length;
        showSlide(nextIndex);
    }

    function prevSlide() {
        let prevIndex = (currentSlide - 1 + slides.length) % slides.length;
        showSlide(prevIndex);
    }

    // Auto slide
    function startSlideShow() {
        slideInterval = setInterval(nextSlide, 6000); // Extended to 6 seconds for better experience
    }

    function stopSlideShow() {
        clearInterval(slideInterval);
    }

    // Event listeners for slider controls
    if (prevBtn) prevBtn.addEventListener('click', () => {
        stopSlideShow();
        prevSlide();
        startSlideShow();
    });

    if (nextBtn) nextBtn.addEventListener('click', () => {
        stopSlideShow();
        nextSlide();
        startSlideShow();
    });

    // Indicator click event
    indicators.forEach((indicator, index) => {
        indicator.addEventListener('click', () => {
            stopSlideShow();
            showSlide(index);
            startSlideShow();
        });
    });

    // Initialize slider
    showSlide(currentSlide);
    // Animate initial slide content
    if (slides[currentSlide]) {
        animateSlideContent(slides[currentSlide]);
    }
    startSlideShow();

    // Pause slideshow on hover
    const heroSlider = document.querySelector('.hero-slider');
    if (heroSlider) {
        heroSlider.addEventListener('mouseenter', stopSlideShow);
        heroSlider.addEventListener('mouseleave', startSlideShow);
    }

    // Enhanced parallax effect for hero section
    window.addEventListener('scroll', function() {
        const scrolled = window.pageYOffset;
        const parallax = document.querySelector('.hero');
        if (parallax) {
            const speed = scrolled * 0.5;
            parallax.style.transform = `translateY(${speed}px)`;
        }
    });

    // Smooth scrolling for anchor links
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    anchorLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;

            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 80,
                    behavior: 'smooth'
                });
            }
        });
    });

    // Animate elements when they come into view
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animated');

                // Specific animations based on element type
                if (entry.target.classList.contains('room-card')) {
                    entry.target.style.animation = 'fadeInUp 0.8s ease forwards';
                } else if (entry.target.classList.contains('facility-card')) {
                    entry.target.style.animation = 'fadeInUp 0.8s ease 0.2s forwards';
                } else if (entry.target.classList.contains('testimonial-card')) {
                    entry.target.style.animation = 'fadeInUp 0.8s ease 0.4s forwards';
                }
            }
        });
    }, observerOptions);

    // Observe elements that should animate when in view
    document.querySelectorAll('.room-card, .facility-card, .testimonial-card, .section-header').forEach(el => {
        observer.observe(el);
    });

    // Newsletter form submission
    const newsletterForms = document.querySelectorAll('.newsletter-form, .footer-form');
    newsletterForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const emailInput = this.querySelector('input[type="email"]');
            const email = emailInput.value.trim();

            if (!validateEmail(email)) {
                alert('Please enter a valid email address.');
                return;
            }

            // Simulate form submission
            alert('Thank you for subscribing to our newsletter!');
            this.reset();
        });
    });

    // Booking form submission (for demo purposes)
    const bookingForm = document.querySelector('#booking-form');
    if (bookingForm) {
        bookingForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Simulate booking submission
            alert('Booking request submitted successfully! We will contact you shortly.');
            this.reset();
        });
    }

    // Contact form submission (for demo purposes)
    const contactForm = document.querySelector('#contact-form');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const name = this.querySelector('[name="name"]').value;
            const email = this.querySelector('[name="email"]').value;
            const subject = this.querySelector('[name="subject"]').value;
            const message = this.querySelector('[name="message"]').value;

            if (!name || !email || !message) {
                alert('Please fill in all required fields.');
                return;
            }

            if (!validateEmail(email)) {
                alert('Please enter a valid email address.');
                return;
            }

            // Simulate form submission
            alert('Thank you for contacting us! We will get back to you soon.');
            this.reset();
        });
    }
});

// Email validation helper function
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// Image gallery lightbox functionality
function initImageGallery() {
    const galleryItems = document.querySelectorAll('.gallery-item');
    galleryItems.forEach(item => {
        item.addEventListener('click', function() {
            const imgSrc = this.querySelector('img').getAttribute('src');
            const caption = this.getAttribute('data-caption') || '';

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
}

// Initialize gallery if on gallery page
if (document.querySelector('.gallery-page')) {
    initImageGallery();
}

// Room selection functionality
function initRoomSelection() {
    const roomCards = document.querySelectorAll('.room-card');
    roomCards.forEach(card => {
        card.addEventListener('click', function() {
            // Remove selected class from all cards
            roomCards.forEach(c => c.classList.remove('selected'));

            // Add selected class to clicked card
            this.classList.add('selected');

            // Scroll to booking section
            const bookingSection = document.querySelector('#booking-section');
            if (bookingSection) {
                bookingSection.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
}

// Initialize room selection if on rooms page
if (document.querySelector('.rooms-page')) {
    initRoomSelection();
}

// Form validation for booking page
function validateBookingForm(formData) {
    const requiredFields = ['checkin', 'checkout', 'guests', 'room-type'];
    for (const field of requiredFields) {
        if (!formData[field]) {
            return { valid: false, message: `Please fill in the ${field.replace('-', ' ')} field.` };
        }
    }

    // Check if check-in is before check-out
    const checkinDate = new Date(formData.checkin);
    const checkoutDate = new Date(formData.checkout);

    if (checkinDate >= checkoutDate) {
        return { valid: false, message: 'Check-out date must be after check-in date.' };
    }

    // Check if dates are in the future
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    if (checkinDate < today) {
        return { valid: false, message: 'Check-in date must be today or in the future.' };
    }

    return { valid: true, message: 'Form is valid.' };
}

// Initialize tooltips
function initTooltips() {
    const tooltipElements = document.querySelectorAll('[data-tooltip]');
    tooltipElements.forEach(element => {
        element.addEventListener('mouseenter', function() {
            const tooltipText = this.getAttribute('data-tooltip');
            const tooltip = document.createElement('div');
            tooltip.className = 'tooltip';
            tooltip.textContent = tooltipText;
            document.body.appendChild(tooltip);

            const rect = this.getBoundingClientRect();
            tooltip.style.top = rect.top - tooltip.offsetHeight - 10 + 'px';
            tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
        });

        element.addEventListener('mouseleave', function() {
            const tooltip = document.querySelector('.tooltip');
            if (tooltip) {
                document.body.removeChild(tooltip);
            }
        });
    });
}

// Initialize tooltips if any exist
if (document.querySelectorAll('[data-tooltip]').length > 0) {
    initTooltips();
}

// Add scroll reveal animations
function initScrollAnimations() {
    const animatedElements = document.querySelectorAll('.room-card, .facility-card, .testimonial-card, .section-header');

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, { threshold: 0.1 });

    animatedElements.forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'opacity 0.8s ease, transform 0.8s ease';
        observer.observe(el);
    });
}

// Initialize scroll animations when DOM is loaded
document.addEventListener('DOMContentLoaded', initScrollAnimations);

// Gallery filtering functionality
function initGalleryFiltering() {
    const filterButtons = document.querySelectorAll('.filter-btn');
    const galleryItems = document.querySelectorAll('.gallery-item');

    if (filterButtons.length > 0) {
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
    }
}

// Initialize gallery filtering if on gallery page
if (document.querySelector('.gallery-page')) {
    initGalleryFiltering();
}

// FAQ accordion functionality
function initFAQAccordion() {
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
}

// Initialize FAQ accordion if on contact page
if (document.querySelector('.faq-question')) {
    initFAQAccordion();
}