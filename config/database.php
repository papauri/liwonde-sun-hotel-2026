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

// Create PDO connection
try {
    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    // Always show a beautiful custom error page (sleeping bear)
    $errorMsg = htmlspecialchars($e->getMessage());
    error_log("Database Connection Error: " . $e->getMessage());
    include_once __DIR__ . '/../includes/db-error.php';
    exit;
}

/**
 * Helper function to get setting value
 */
function getSetting($key, $default = '') {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT setting_value FROM site_settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetch();
        return $result ? $result['setting_value'] : $default;
    } catch (PDOException $e) {
        error_log("Error fetching setting: " . $e->getMessage());
        return $default;
    }
}

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
