# Liwonde Sun Hotel - 2026 Modern Website

This is the new modern website for Liwonde Sun Hotel, built with HTML, CSS, JavaScript, and PHP. This website replaces the previous WordPress installation with a clean, fast, and modern design focused on user experience.

## Features

- Fully responsive design that works on all devices
- Modern UI/UX with 2026 aesthetic
- Fast loading times
- SEO optimized
- Easy navigation
- Interactive elements
- Form handling for bookings and contacts
- Dual environment support (UAT/PROD)

## Pages Included

- Home (index.php)
- About Us (pages/about.php)
- Rooms & Accommodations (pages/rooms.php)
- Facilities & Services (pages/facilities.php)
- Photo Gallery (pages/gallery.php)
- Contact Us (pages/contact.php)
- Booking System (pages/booking.php)

## Technologies Used

- HTML5
- CSS3 (with modern features)
- JavaScript (ES6+)
- PHP for server-side processing
- Responsive design with Flexbox and Grid
- Modern JavaScript for interactivity
- Environment detection system

## File Structure

```
liwonde-sun-hotel-2026/
├── css/
│   └── style.css
├── js/
│   └── main.js
├── images/
├── includes/
│   ├── environment.php
│   └── utils.php
├── pages/
│   ├── about.php
│   ├── rooms.php
│   ├── facilities.php
│   ├── gallery.php
│   ├── contact.php
│   └── booking.php
├── index.php
├── process.php
├── config.php
├── sitemap.xml
├── robots.txt
└── .htaccess
```

## Environment Setup

### UAT (User Acceptance Testing)
To run the website locally for testing:

```bash
php -S 0.0.0.0:8000
```

Or:

```bash
php -S 127.0.0.1:8000
```

Then visit: http://localhost:8000 or http://127.0.0.1:8000

When running on localhost, 127.0.0.1, 0.0.0.0, or with "liwonde-sun-hotel-2026" in the path, the site will automatically detect UAT mode with:
- UAT-specific site name ("Liwonde Sun Hotel - UAT")
- Different email addresses
- Error reporting enabled
- Debug information displayed
- Caching disabled

### Production
When deployed to the production domain (liwondesunhotel.com), the site will automatically detect PROD mode with:
- Production site name ("Liwonde Sun Hotel")
- Production email addresses
- Error reporting disabled
- Analytics enabled
- Caching optimized

## Mobile Responsiveness

The website is fully responsive and optimized for all device sizes:
- Desktop (1200px+)
- Tablet landscape (992px - 1199px)
- Tablet portrait (768px - 991px)
- Mobile landscape (576px - 767px)
- Mobile portrait (320px - 575px)

## Support

For technical support, please contact your web developer or hosting provider. The website is designed to be easily maintainable by anyone with basic HTML/CSS/PHP knowledge.