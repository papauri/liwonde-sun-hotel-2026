-- ============================================================
-- site_pages – Public page registry for nav & page management
-- ============================================================

CREATE TABLE IF NOT EXISTS site_pages (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    page_key      VARCHAR(50)   NOT NULL UNIQUE,
    title         VARCHAR(100)  NOT NULL,
    file_path     VARCHAR(100)  NOT NULL,
    icon          VARCHAR(50)   NOT NULL DEFAULT 'fa-file',
    description   VARCHAR(255)  NULL,
    nav_position  INT UNSIGNED  NOT NULL DEFAULT 0,
    show_in_nav   TINYINT(1)    NOT NULL DEFAULT 1,
    is_enabled    TINYINT(1)    NOT NULL DEFAULT 1,
    created_at    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Seed default pages ────────────────────────────────────────
INSERT INTO site_pages (page_key, title, file_path, icon, description, nav_position, show_in_nav, is_enabled) VALUES
('home',           'Home',           'index.php',           'fa-home',           'Hotel homepage',                                    10, 1, 1),
('rooms',          'Rooms',          'rooms-gallery.php',   'fa-bed',            'Browse available rooms and suites',                 20, 1, 1),
('rooms-showcase', 'Rooms Showcase', 'rooms-showcase.php',  'fa-door-open',      'Detailed room showcase with booking flows',         25, 0, 1),
('restaurant',     'Restaurant',     'restaurant.php',      'fa-utensils',       'Restaurant menu, gallery and reservations',          30, 1, 1),
('gym',            'Gym',            'gym.php',             'fa-dumbbell',       'Fitness centre, classes and wellness packages',      40, 1, 1),
('conference',     'Conference',     'conference.php',      'fa-briefcase',      'Conference and meeting facilities',                  50, 1, 1),
('events',         'Events',         'events.php',          'fa-calendar-alt',   'Upcoming events and celebrations',                  60, 1, 1),
('booking',        'Book Now',       'booking.php',         'fa-calendar-check', 'Online room booking',                               70, 1, 1),
('privacy-policy', 'Privacy Policy', 'privacy-policy.php',  'fa-shield-alt',     'Privacy & cookie policy',                           80, 0, 1),
('room',           'Room Details',   'room.php',            'fa-info-circle',    'Individual room detail page',                        90, 0, 1)
ON DUPLICATE KEY UPDATE title = VALUES(title);
