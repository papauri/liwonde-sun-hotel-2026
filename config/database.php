<?php
/**
 * Database Configuration
 * Liwonde Sun Hotel - Premium Database Connection
 * Supports both LOCAL and PRODUCTION environments
 */

// Include caching system first
require_once __DIR__ . '/cache.php';

// Database configuration - multiple security options
// Priority: 1. Local config file, 2. Environment variables, 3. Hardcoded fallback

// Option 1: Check for local config file (for cPanel/production)
if (file_exists(__DIR__ . '/database.local.php')) {
    include __DIR__ . '/database.local.php';
} else {
    // Option 2: Use environment variables (for development)
    $db_host = getenv('DB_HOST') ?: 'promanaged-it.com';
    $db_name = getenv('DB_NAME') ?: 'p601229_hotels';
    $db_user = getenv('DB_USER') ?: 'p601229_hotel_admin';
    $db_pass = getenv('DB_PASS') ?: '2:p2WpmX[0YTs7';
    $db_port = getenv('DB_PORT') ?: '3306';
    $db_charset = 'utf8mb4';
}

// Define database constants
define('DB_HOST', $db_host);
define('DB_PORT', $db_port);
define('DB_NAME', $db_name);
define('DB_USER', $db_user);
define('DB_PASS', $db_pass);
define('DB_CHARSET', $db_charset);

// Create PDO connection with performance optimizations
try {
    // Diagnostic logging
    error_log("Database Connection Attempt:");
    error_log("  Host: " . DB_HOST);
    error_log("  Port: " . DB_PORT);
    error_log("  Database: " . DB_NAME);
    error_log("  User: " . DB_USER);
    error_log("  Environment Variables Set: " . (getenv('DB_HOST') ? 'YES' : 'NO'));
    
    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_PERSISTENT => false, // Disabled for remote DB to prevent connection pooling issues
        PDO::ATTR_TIMEOUT => 10, // Connection timeout in seconds (increased for remote DB)
        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true, // Buffer results for better performance
    ];
    
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    
    // Set timezone after connection
    $pdo->exec("SET time_zone = '+00:00'");
    
    error_log("Database Connection Successful!");
    
} catch (PDOException $e) {
    // Always show a beautiful custom error page (sleeping bear)
    $errorMsg = htmlspecialchars($e->getMessage());
    error_log("Database Connection Error: " . $e->getMessage());
    error_log("Error Code: " . $e->getCode());
    include_once __DIR__ . '/../includes/db-error.php';
    exit;
}

// Settings cache to avoid repeated queries
$_SITE_SETTINGS = [];

/**
 * Helper function to get setting value with file-based caching
 * DRAMATICALLY reduces database queries and remote connection overhead
 */
function getSetting($key, $default = '') {
    global $pdo, $_SITE_SETTINGS;
    
    // Check in-memory cache first (fastest)
    if (isset($_SITE_SETTINGS[$key])) {
        return $_SITE_SETTINGS[$key];
    }
    
    // Check file cache (much faster than database query)
    $cachedValue = getCache("setting_{$key}", null);
    if ($cachedValue !== null) {
        $_SITE_SETTINGS[$key] = $cachedValue;
        return $cachedValue;
    }
    
    try {
        $stmt = $pdo->prepare("SELECT setting_value FROM site_settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetch();
        $value = $result ? $result['setting_value'] : $default;
        
        // Cache in memory
        $_SITE_SETTINGS[$key] = $value;
        
        // Cache in file for next request (1 hour TTL)
        setCache("setting_{$key}", $value, 3600);
        
        return $value;
    } catch (PDOException $e) {
        error_log("Error fetching setting: " . $e->getMessage());
        return $default;
    }
}

/**
 * Helper function to get email setting value with caching
 * Handles encrypted settings like passwords
 */
function getEmailSetting($key, $default = '') {
    global $pdo;
    
    try {
        // Check if email_settings table exists (cached)
        $table_exists = getCache("table_email_settings", null);
        if ($table_exists === null) {
            $table_exists = $pdo->query("SHOW TABLES LIKE 'email_settings'")->rowCount() > 0;
            setCache("table_email_settings", $table_exists, 86400); // Cache for 24 hours
        }
        
        if (!$table_exists) {
            // Fallback to site_settings for backward compatibility
            return getSetting($key, $default);
        }
        
        // Try file cache first
        $cachedValue = getCache("email_setting_{$key}", null);
        if ($cachedValue !== null) {
            return $cachedValue;
        }
        
        $stmt = $pdo->prepare("SELECT setting_value, is_encrypted FROM email_settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetch();
        
        if (!$result) {
            return $default;
        }
        
        $value = $result['setting_value'];
        $is_encrypted = (bool)$result['is_encrypted'];
        
        // Handle encrypted values (like passwords)
        if ($is_encrypted && !empty($value)) {
            try {
                // Try to decrypt using database function
                $stmt = $pdo->prepare("SELECT decrypt_setting(?) as decrypted_value");
                $stmt->execute([$value]);
                $decrypted = $stmt->fetch();
                if ($decrypted && !empty($decrypted['decrypted_value'])) {
                    $value = $decrypted['decrypted_value'];
                } else {
                    $value = ''; // Don't expose encrypted data
                }
            } catch (Exception $e) {
                $value = ''; // Don't expose encrypted data on error
            }
        }
        
        // Cache the result (1 hour TTL for encrypted, 6 hours for unencrypted)
        $ttl = $is_encrypted ? 3600 : 21600;
        setCache("email_setting_{$key}", $value, $ttl);
        
        return $value;
    } catch (PDOException $e) {
        error_log("Error fetching email setting: " . $e->getMessage());
        return $default;
    }
}

/**
 * Helper function to get all email settings
 */
function getAllEmailSettings() {
    global $pdo;
    
    $settings = [];
    try {
        // Check if email_settings table exists
        $table_exists = $pdo->query("SHOW TABLES LIKE 'email_settings'")->rowCount() > 0;
        
        if (!$table_exists) {
            return $settings;
        }
        
        $stmt = $pdo->query("SELECT setting_key, setting_value, is_encrypted, description FROM email_settings ORDER BY setting_group, setting_key");
        $results = $stmt->fetchAll();
        
        foreach ($results as $row) {
            $key = $row['setting_key'];
            $value = $row['setting_value'];
            $is_encrypted = (bool)$row['is_encrypted'];
            
            // Handle encrypted values
            if ($is_encrypted && !empty($value)) {
                try {
                    $stmt2 = $pdo->prepare("SELECT decrypt_setting(?) as decrypted_value");
                    $stmt2->execute([$value]);
                    $decrypted = $stmt2->fetch();
                    if ($decrypted && !empty($decrypted['decrypted_value'])) {
                        $value = $decrypted['decrypted_value'];
                    } else {
                        $value = ''; // Don't expose encrypted data
                    }
                } catch (Exception $e) {
                    $value = ''; // Don't expose encrypted data on error
                }
            }
            
            $settings[$key] = [
                'value' => $value,
                'encrypted' => $is_encrypted,
                'description' => $row['description']
            ];
        }
        
        return $settings;
    } catch (PDOException $e) {
        error_log("Error fetching all email settings: " . $e->getMessage());
        return $settings;
    }
}

/**
 * Helper function to update email setting
 */
function updateEmailSetting($key, $value, $description = null, $is_encrypted = false) {
    global $pdo;
    
    try {
        // Check if email_settings table exists
        $table_exists = $pdo->query("SHOW TABLES LIKE 'email_settings'")->rowCount() > 0;
        
        if (!$table_exists) {
            // Fallback to site_settings for backward compatibility
            return updateSetting($key, $value);
        }
        
        // Handle encryption if needed
        $final_value = $value;
        if ($is_encrypted && !empty($value)) {
            try {
                $stmt = $pdo->prepare("SELECT encrypt_setting(?) as encrypted_value");
                $stmt->execute([$value]);
                $encrypted = $stmt->fetch();
                if ($encrypted && !empty($encrypted['encrypted_value'])) {
                    $final_value = $encrypted['encrypted_value'];
                }
            } catch (Exception $e) {
                error_log("Error encrypting setting {$key}: " . $e->getMessage());
                return false;
            }
        }
        
        // Update or insert
        $sql = "INSERT INTO email_settings (setting_key, setting_value, is_encrypted, description) 
                VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE 
                setting_value = VALUES(setting_value),
                is_encrypted = VALUES(is_encrypted),
                description = VALUES(description),
                updated_at = CURRENT_TIMESTAMP";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$key, $final_value, $is_encrypted ? 1 : 0, $description]);
        
        // Clear cache for this setting
        global $_SITE_SETTINGS;
        if (isset($_SITE_SETTINGS[$key])) {
            unset($_SITE_SETTINGS[$key]);
        }
        
        return true;
    } catch (PDOException $e) {
        error_log("Error updating email setting: " . $e->getMessage());
        return false;
    }
}

/**
 * Helper function to update setting (for backward compatibility)
 */
function updateSetting($key, $value) {
    global $pdo;
    
    try {
        $sql = "INSERT INTO site_settings (setting_key, setting_value) 
                VALUES (?, ?)
                ON DUPLICATE KEY UPDATE 
                setting_value = VALUES(setting_value),
                updated_at = CURRENT_TIMESTAMP";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$key, $value]);
        
        // Clear cache for this setting
        global $_SITE_SETTINGS;
        if (isset($_SITE_SETTINGS[$key])) {
            unset($_SITE_SETTINGS[$key]);
        }
        
        return true;
    } catch (PDOException $e) {
        error_log("Error updating setting: " . $e->getMessage());
        return false;
    }
}

/**
 * Preload common settings for better performance
 */
function preloadCommonSettings() {
    $common_settings = [
        'site_name', 'site_description', 'currency_symbol',
        'phone_main', 'email_reservations', 'email_info',
        'social_facebook', 'social_instagram', 'social_twitter'
    ];
    
    foreach ($common_settings as $setting) {
        getSetting($setting);
    }
}

// Preload common settings for faster page loads
preloadCommonSettings();

/**
 * Helper function to get all settings by group
 */
function getSettingsByGroup($group) {
    global $pdo;
    
    // Check cache first
    $cached = getCache("settings_group_{$group}", null);
    if ($cached !== null) {
        return $cached;
    }
    
    try {
        $stmt = $pdo->prepare("SELECT setting_key, setting_value FROM site_settings WHERE setting_group = ?");
        $stmt->execute([$group]);
        $result = $stmt->fetchAll();
        
        // Cache for 30 minutes
        setCache("settings_group_{$group}", $result, 1800);
        
        return $result;
    } catch (PDOException $e) {
        error_log("Error fetching settings: " . $e->getMessage());
        return [];
    }
}

/**
 * Helper function to get cached rooms with optional filters
 * Dramatically reduces database queries for room listings
 */
function getCachedRooms($filters = []) {
    global $pdo;
    
    // Create cache key from filters
    $cacheKey = 'rooms_' . md5(json_encode($filters));
    $cached = getCache($cacheKey, null);
    if ($cached !== null) {
        return $cached;
    }
    
    try {
        $sql = "SELECT * FROM rooms WHERE is_active = 1";
        $params = [];
        
        if (!empty($filters['is_featured'])) {
            $sql .= " AND is_featured = 1";
        }
        
        $sql .= " ORDER BY display_order ASC, id ASC";
        
        if (!empty($filters['limit'])) {
            $sql .= " LIMIT ?";
            $params[] = (int)$filters['limit'];
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Cache for 15 minutes
        setCache($cacheKey, $rooms, 900);
        
        return $rooms;
    } catch (PDOException $e) {
        error_log("Error fetching rooms: " . $e->getMessage());
        return [];
    }
}

/**
 * Helper function to get cached facilities
 */
function getCachedFacilities($filters = []) {
    global $pdo;
    
    $cacheKey = 'facilities_' . md5(json_encode($filters));
    $cached = getCache($cacheKey, null);
    if ($cached !== null) {
        return $cached;
    }
    
    try {
        $sql = "SELECT * FROM facilities WHERE is_active = 1";
        $params = [];
        
        if (!empty($filters['is_featured'])) {
            $sql .= " AND is_featured = 1";
        }
        
        $sql .= " ORDER BY display_order ASC";
        
        if (!empty($filters['limit'])) {
            $sql .= " LIMIT ?";
            $params[] = (int)$filters['limit'];
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $facilities = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Cache for 30 minutes
        setCache($cacheKey, $facilities, 1800);
        
        return $facilities;
    } catch (PDOException $e) {
        error_log("Error fetching facilities: " . $e->getMessage());
        return [];
    }
}

/**
 * Helper function to get cached gallery images
 */
function getCachedGalleryImages() {
    global $pdo;
    
    $cacheKey = 'gallery_images';
    $cached = getCache($cacheKey, null);
    if ($cached !== null) {
        return $cached;
    }
    
    try {
        $stmt = $pdo->query("
            SELECT * FROM hotel_gallery 
            WHERE is_active = 1 
            ORDER BY display_order ASC
        ");
        $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Cache for 1 hour
        setCache($cacheKey, $images, 3600);
        
        return $images;
    } catch (PDOException $e) {
        error_log("Error fetching gallery images: " . $e->getMessage());
        return [];
    }
}

/**
 * Helper function to get cached hero slides
 */
function getCachedHeroSlides() {
    global $pdo;
    
    $cacheKey = 'hero_slides';
    $cached = getCache($cacheKey, null);
    if ($cached !== null) {
        return $cached;
    }
    
    try {
        $stmt = $pdo->query("
            SELECT title, subtitle, description, primary_cta_text, primary_cta_link, 
                   secondary_cta_text, secondary_cta_link, image_path 
            FROM hero_slides 
            WHERE is_active = 1 
            ORDER BY display_order ASC
        ");
        $slides = $stmt->fetchAll();
        
        // Cache for 1 hour
        setCache($cacheKey, $slides, 3600);
        
        return $slides;
    } catch (PDOException $e) {
        error_log("Error fetching hero slides: " . $e->getMessage());
        return [];
    }
}

/**
 * Helper function to get cached testimonials
 */
function getCachedTestimonials($limit = 3) {
    global $pdo;
    
    $cacheKey = "testimonials_{$limit}";
    $cached = getCache($cacheKey, null);
    if ($cached !== null) {
        return $cached;
    }
    
    try {
        $stmt = $pdo->prepare("
            SELECT * FROM testimonials
            WHERE is_featured = 1 AND is_approved = 1
            ORDER BY display_order ASC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        $testimonials = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Cache for 30 minutes
        setCache($cacheKey, $testimonials, 1800);
        
        return $testimonials;
    } catch (PDOException $e) {
        error_log("Error fetching testimonials: " . $e->getMessage());
        return [];
    }
}

/**
 * Helper function to get cached policies
 */
function getCachedPolicies() {
    global $pdo;
    
    $cacheKey = 'policies';
    $cached = getCache($cacheKey, null);
    if ($cached !== null) {
        return $cached;
    }
    
    try {
        $stmt = $pdo->query("
            SELECT slug, title, summary, content 
            FROM policies 
            WHERE is_active = 1 
            ORDER BY display_order ASC, id ASC
        ");
        $policies = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Cache for 1 hour
        setCache($cacheKey, $policies, 3600);
        
        return $policies;
    } catch (PDOException $e) {
        error_log("Error fetching policies: " . $e->getMessage());
        return [];
    }
}

/**
 * Helper function to get cached About Us content
 */
function getCachedAboutUs() {
    global $pdo;
    
    $cacheKey = 'about_us';
    $cached = getCache($cacheKey, null);
    if ($cached !== null) {
        return $cached;
    }
    
    try {
        // Get main about content
        $stmt = $pdo->prepare("SELECT * FROM about_us WHERE section_type = 'main' AND is_active = 1 ORDER BY display_order LIMIT 1");
        $stmt->execute();
        $about_content = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Get features
        $stmt = $pdo->prepare("SELECT * FROM about_us WHERE section_type = 'feature' AND is_active = 1 ORDER BY display_order");
        $stmt->execute();
        $about_features = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get stats
        $stmt = $pdo->prepare("SELECT * FROM about_us WHERE section_type = 'stat' AND is_active = 1 ORDER BY display_order");
        $stmt->execute();
        $about_stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $result = [
            'content' => $about_content,
            'features' => $about_features,
            'stats' => $about_stats
        ];
        
        // Cache for 1 hour
        setCache($cacheKey, $result, 3600);
        
        return $result;
    } catch (PDOException $e) {
        error_log("Error fetching about us content: " . $e->getMessage());
        return ['content' => null, 'features' => [], 'stats' => []];
    }
}

/**
 * Invalidate all data caches when content changes
 */
function invalidateDataCaches() {
    // Clear all data caches
    $patterns = [
        'rooms_*',
        'facilities_*',
        'gallery_images',
        'hero_slides',
        'testimonials_*',
        'policies',
        'about_us',
        'settings_group_*'
    ];
    
    foreach ($patterns as $pattern) {
        $files = glob(CACHE_DIR . '/' . md5(str_replace('*', '', $pattern)) . '*');
        if ($files) {
            foreach ($files as $file) {
                @unlink($file);
            }
        }
    }
}

/**
 * Helper: fetch active page hero by page slug.
 * Returns associative array or null.
 */
function getPageHero(string $page_slug): ?array {
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            SELECT *
            FROM page_heroes
            WHERE page_slug = ? AND is_active = 1
            ORDER BY display_order ASC, id ASC
            LIMIT 1
        ");
        $stmt->execute([$page_slug]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    } catch (PDOException $e) {
        error_log("Error fetching page hero ({$page_slug}): " . $e->getMessage());
        return null;
    }
}

/**
 * Helper: fetch active page hero by exact page URL (e.g. /restaurant.php).
 * Returns associative array or null.
 */
function getPageHeroByUrl(string $page_url): ?array {
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            SELECT *
            FROM page_heroes
            WHERE page_url = ? AND is_active = 1
            ORDER BY display_order ASC, id ASC
            LIMIT 1
        ");
        $stmt->execute([$page_url]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    } catch (PDOException $e) {
        error_log("Error fetching page hero by url ({$page_url}): " . $e->getMessage());
        return null;
    }
}

/**
 * Helper: get hero for the current request without hardcoding per-page slugs.
 * Strategy:
 *  1) Try exact match on page_url (SCRIPT_NAME).
 *  2) Fallback to slug derived from current filename (basename without .php).
 */
function getCurrentPageHero(): ?array {
    $script = $_SERVER['SCRIPT_NAME'] ?? '';
    if ($script) {
        $byUrl = getPageHeroByUrl($script);
        if ($byUrl) return $byUrl;
    }

    $path = $_SERVER['SCRIPT_FILENAME'] ?? $script;
    if (!$path) return null;

    $slug = strtolower(pathinfo($path, PATHINFO_FILENAME));
    $slug = str_replace('_', '-', $slug);

    return getPageHero($slug);
}

/**
 * Helper: fetch active page loader subtext by page slug.
 * Returns the subtext string if found and active, null otherwise.
 */
function getPageLoader(string $page_slug): ?string {
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            SELECT subtext
            FROM page_loaders
            WHERE page_slug = ? AND is_active = 1
            LIMIT 1
        ");
        $stmt->execute([$page_slug]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['subtext'] : null;
    } catch (PDOException $e) {
        error_log("Error fetching page loader ({$page_slug}): " . $e->getMessage());
        return null;
    }
}

/**
 * Helper function to check room availability
 * Returns true if room is available, false if booked
 */
function isRoomAvailable($room_id, $check_in_date, $check_out_date, $exclude_booking_id = null) {
    global $pdo;
    try {
        // First check if there are any rooms available at all
        $room_stmt = $pdo->prepare("SELECT rooms_available, total_rooms FROM rooms WHERE id = ?");
        $room_stmt->execute([$room_id]);
        $room = $room_stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$room || $room['rooms_available'] <= 0) {
            return false; // No rooms available
        }
        
        // Then check for overlapping bookings
        $sql = "
            SELECT COUNT(*) as bookings 
            FROM bookings 
            WHERE room_id = ? 
            AND status IN ('pending', 'confirmed', 'checked-in')
            AND NOT (check_out_date <= ? OR check_in_date >= ?)
        ";
        $params = [$room_id, $check_in_date, $check_out_date];
        
        // Exclude a specific booking (useful when updating existing bookings)
        if ($exclude_booking_id) {
            $sql .= " AND id != ?";
            $params[] = $exclude_booking_id;
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Check if number of overlapping bookings is less than available rooms
        $overlapping_bookings = $result['bookings'];
        $rooms_available = $room['rooms_available'];
        
        return $overlapping_bookings < $rooms_available;
    } catch (PDOException $e) {
        error_log("Error checking room availability: " . $e->getMessage());
        return false; // Assume unavailable on error
    }
}

/**
 * Enhanced function to check room availability with detailed conflict information
 * Returns array with availability status and conflict details
 */
function checkRoomAvailability($room_id, $check_in_date, $check_out_date, $exclude_booking_id = null) {
    global $pdo;
    
    $result = [
        'available' => true,
        'conflicts' => [],
        'room_exists' => false,
        'room' => null
    ];
    
    try {
        // Check if room exists and get details
        $stmt = $pdo->prepare("SELECT * FROM rooms WHERE id = ? AND is_active = 1");
        $stmt->execute([$room_id]);
        $room = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$room) {
            $result['room_exists'] = false;
            $result['error'] = 'Room not found or inactive';
            return $result;
        }
        
        $result['room'] = $room;
        $result['room_exists'] = true;
        
        // Validate dates
        $check_in = new DateTime($check_in_date);
        $check_out = new DateTime($check_out_date);
        $today = new DateTime();
        
        if ($check_in < $today) {
            $result['available'] = false;
            $result['error'] = 'Check-in date cannot be in the past';
            return $result;
        }
        
        if ($check_out <= $check_in) {
            $result['available'] = false;
            $result['error'] = 'Check-out date must be after check-in date';
            return $result;
        }
        
        // Check if there are rooms available
        if ($room['rooms_available'] <= 0) {
            $result['available'] = false;
            $result['error'] = 'No rooms of this type are currently available';
            return $result;
        }
        
        // Check for overlapping bookings
        $sql = "
            SELECT 
                id,
                booking_reference,
                check_in_date,
                check_out_date,
                status,
                guest_name
            FROM bookings 
            WHERE room_id = ? 
            AND status IN ('pending', 'confirmed', 'checked-in')
            AND NOT (check_out_date <= ? OR check_in_date >= ?)
        ";
        $params = [$room_id, $check_in_date, $check_out_date];
        
        // Exclude specific booking for updates
        if ($exclude_booking_id) {
            $sql .= " AND id != ?";
            $params[] = $exclude_booking_id;
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $conflicts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Check if number of overlapping bookings exceeds available rooms
        $overlapping_bookings = count($conflicts);
        $rooms_available = $room['rooms_available'];
        
        if ($overlapping_bookings >= $rooms_available) {
            $result['available'] = false;
            $result['conflicts'] = $conflicts;
            $result['error'] = 'Room is not available for the selected dates';
            
            // Build detailed conflict message
            $conflict_details = [];
            foreach ($conflicts as $conflict) {
                $conflict_check_in = new DateTime($conflict['check_in_date']);
                $conflict_check_out = new DateTime($conflict['check_out_date']);
                $conflict_details[] = sprintf(
                    "Booking %s (%s) from %s to %s",
                    $conflict['booking_reference'],
                    $conflict['guest_name'],
                    $conflict_check_in->format('M j, Y'),
                    $conflict_check_out->format('M j, Y')
                );
            }
            $result['conflict_message'] = implode('; ', $conflict_details);
        }
        
        // Calculate number of nights
        $interval = $check_in->diff($check_out);
        $result['nights'] = $interval->days;
        
        // Check if room has enough capacity for requested dates
        $max_guests = (int)$room['max_guests'];
        if ($max_guests > 0) {
            $result['max_guests'] = $max_guests;
        }
        
        return $result;
        
    } catch (PDOException $e) {
        error_log("Error checking room availability: " . $e->getMessage());
        $result['available'] = false;
        $result['error'] = 'Database error while checking availability';
        return $result;
    } catch (Exception $e) {
        error_log("Error checking room availability: " . $e->getMessage());
        $result['available'] = false;
        $result['error'] = 'Invalid date format';
        return $result;
    }
}

/**
 * Function to validate booking data before insertion/update
 * Returns array with validation status and error messages
 */
function validateBookingData($data) {
    $errors = [];
    
    // Required fields
    $required_fields = ['room_id', 'guest_name', 'guest_email', 'guest_phone', 'check_in_date', 'check_out_date', 'number_of_guests'];
    foreach ($required_fields as $field) {
        if (empty($data[$field])) {
            $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
        }
    }
    
    // Email validation
    if (!empty($data['guest_email'])) {
        if (!filter_var($data['guest_email'], FILTER_VALIDATE_EMAIL)) {
            $errors['guest_email'] = 'Invalid email address';
        }
    }
    
    // Phone number validation (basic)
    if (!empty($data['guest_phone'])) {
        $phone = preg_replace('/[^0-9+]/', '', $data['guest_phone']);
        if (strlen($phone) < 8) {
            $errors['guest_phone'] = 'Phone number is too short';
        }
    }
    
    // Number of guests validation
    if (!empty($data['number_of_guests'])) {
        $guests = (int)$data['number_of_guests'];
        if ($guests < 1) {
            $errors['number_of_guests'] = 'At least 1 guest is required';
        } elseif ($guests > 20) {
            $errors['number_of_guests'] = 'Maximum 20 guests allowed';
        }
    }
    
    // Date validation
    if (!empty($data['check_in_date']) && !empty($data['check_out_date'])) {
        try {
            $check_in = new DateTime($data['check_in_date']);
            $check_out = new DateTime($data['check_out_date']);
            $today = new DateTime();
            $today->setTime(0, 0, 0);
            
            if ($check_in < $today) {
                $errors['check_in_date'] = 'Check-in date cannot be in the past';
            }
            
            if ($check_out <= $check_in) {
                $errors['check_out_date'] = 'Check-out date must be after check-in date';
            }
            
    // Maximum stay duration (30 days)
    $max_stay = new DateTime();
    $max_stay->modify('+30 days');
    if ($check_out > $max_stay) {
        $errors['check_out_date'] = 'Maximum stay duration is 30 days';
    }
    
    // Maximum advance booking days (configurable setting)
    $max_advance_days = (int)getSetting('max_advance_booking_days', 30);
    $max_advance_date = new DateTime();
    $max_advance_date->modify('+' . $max_advance_days . ' days');
    if ($check_in > $max_advance_date) {
        $errors['check_in_date'] = "Bookings can only be made up to {$max_advance_days} days in advance. Please select an earlier check-in date.";
    }
    
        } catch (Exception $e) {
            $errors['dates'] = 'Invalid date format';
        }
    }
    
    return [
        'valid' => empty($errors),
        'errors' => $errors
    ];
}

/**
 * Function to validate booking with room availability check
 * Combines data validation and availability checking
 */
function validateBookingWithAvailability($data, $exclude_booking_id = null) {
    // First validate data
    $validation = validateBookingData($data);
    if (!$validation['valid']) {
        return [
            'valid' => false,
            'errors' => $validation['errors'],
            'type' => 'validation'
        ];
    }
    
    // Then check room availability
    $availability = checkRoomAvailability(
        $data['room_id'],
        $data['check_in_date'],
        $data['check_out_date'],
        $exclude_booking_id
    );
    
    if (!$availability['available']) {
        return [
            'valid' => false,
            'errors' => [
                'availability' => $availability['error'],
                'conflicts' => $availability['conflict_message'] ?? 'No specific conflicts found'
            ],
            'type' => 'availability',
            'conflicts' => $availability['conflicts']
        ];
    }
    
    // Check if number of guests exceeds room capacity
    if (isset($availability['max_guests']) && isset($data['number_of_guests'])) {
        if ((int)$data['number_of_guests'] > (int)$availability['max_guests']) {
            return [
                'valid' => false,
                'errors' => [
                    'number_of_guests' => "Room capacity is {$availability['max_guests']} guests"
                ],
                'type' => 'capacity'
            ];
        }
    }
    
    return [
        'valid' => true,
        'availability' => $availability
    ];
}