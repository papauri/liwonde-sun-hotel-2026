<?php
/**
 * Database Configuration
 * Liwonde Sun Hotel - Premium Database Connection
 * Supports both LOCAL and PRODUCTION environments
 */

// Detect environment (local vs production)
$isLocal = (
    in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1', 'localhost']) ||
    strpos($_SERVER['HTTP_HOST'] ?? '', 'localhost') !== false ||
    strpos($_SERVER['SERVER_NAME'] ?? '', 'localhost') !== false
);

// Environment overrides via OS env vars (helps when testing locally against remote DB)
$envHost = getenv('DB_HOST') ?: null;
$envPort = getenv('DB_PORT') ?: '3306';
$envName = getenv('DB_NAME') ?: null;
$envUser = getenv('DB_USER') ?: null;
$envPass = getenv('DB_PASS') ?: null;
$forceProd = filter_var(getenv('DB_FORCE_PROD'), FILTER_VALIDATE_BOOL) || filter_var(getenv('DB_FORCE_PRODUCTION'), FILTER_VALIDATE_BOOL);

// Database credentials based on environment / overrides
if ($envHost || $envName || $envUser || $envPass || $forceProd) {
    // ENV override: always use when supplied (useful for remote DB from local machine)
    define('DB_HOST', $envHost ?: 'localhost');
    define('DB_PORT', $envPort ?: '3306');
    define('DB_NAME', $envName ?: 'p601229_hotels');
    define('DB_USER', $envUser ?: 'p601229_hotel_admin');
    define('DB_PASS', $envPass ?: '2:p2WpmX[0YTs7');
    define('DB_CHARSET', 'utf8mb4');
} elseif ($isLocal) {
    // LOCAL DEVELOPMENT SETTINGS (connect to live DB)
    define('DB_HOST', 'promanaged-it.com');
    define('DB_PORT', '3306');
    define('DB_NAME', 'p601229_hotels');
    define('DB_USER', 'p601229_hotel_admin');
    define('DB_PASS', '2:p2WpmX[0YTs7');
    define('DB_CHARSET', 'utf8mb4');
} else {
    // PRODUCTION SETTINGS (used when running on the live server)
    define('DB_HOST', 'promanaged-it.com');
    define('DB_PORT', '3306');
    define('DB_NAME', 'p601229_hotels');
    define('DB_USER', 'p601229_hotel_admin');
    define('DB_PASS', '2:p2WpmX[0YTs7');
    define('DB_CHARSET', 'utf8mb4');
}

// Create PDO connection with performance optimizations
try {
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
    
} catch (PDOException $e) {
    // Always show a beautiful custom error page (sleeping bear)
    $errorMsg = htmlspecialchars($e->getMessage());
    error_log("Database Connection Error: " . $e->getMessage());
    include_once __DIR__ . '/../includes/db-error.php';
    exit;
}

// Settings cache to avoid repeated queries
$_SITE_SETTINGS = [];

/**
 * Helper function to get setting value with caching
 */
function getSetting($key, $default = '') {
    global $pdo, $_SITE_SETTINGS;
    
    // Check cache first
    if (isset($_SITE_SETTINGS[$key])) {
        return $_SITE_SETTINGS[$key];
    }
    
    try {
        $stmt = $pdo->prepare("SELECT setting_value FROM site_settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetch();
        $value = $result ? $result['setting_value'] : $default;
        
        // Cache the result
        $_SITE_SETTINGS[$key] = $value;
        
        return $value;
    } catch (PDOException $e) {
        error_log("Error fetching setting: " . $e->getMessage());
        return $default;
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
    try {
        $stmt = $pdo->prepare("SELECT setting_key, setting_value FROM site_settings WHERE setting_group = ?");
        $stmt->execute([$group]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error fetching settings: " . $e->getMessage());
        return [];
    }
}

/**
 * Helper function to check room availability
 * Returns true if room is available, false if booked
 */
function isRoomAvailable($room_id, $check_in_date, $check_out_date, $exclude_booking_id = null) {
    global $pdo;
    try {
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
        
        return $result['bookings'] == 0; // Available if no conflicting bookings
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
        
        if (!empty($conflicts)) {
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