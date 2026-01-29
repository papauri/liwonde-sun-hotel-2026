<?php
/**
 * Command-line test script for Liwonde Sun Hotel Booking API
 * This can be run directly from the terminal without a web server
 */

echo "=== Liwonde Sun Hotel Booking API Test ===\n\n";

// Change to the project directory
$projectDir = dirname(__DIR__);
chdir($projectDir);

// Load configuration
require_once 'config/database.php';
require_once 'config/email.php';

echo "✓ Database configuration loaded\n";

// Function to simulate API request
function apiRequest($endpoint, $method = 'GET', $data = null) {
    global $pdo;
    
    // Simulate API key
    $apiKey = 'test_key_12345';
    
    echo "\n--- Testing: $method $endpoint ---\n";
    
    // Initialize variables that will be set in api/index.php
    $_SERVER['REQUEST_METHOD'] = $method;
    $_GET = [];
    $_POST = [];
    
    // Parse endpoint and set query parameters
    if (strpos($endpoint, '?') !== false) {
        list($path, $query) = explode('?', $endpoint, 2);
        parse_str($query, $_GET);
    } else {
        $path = $endpoint;
    }
    
    // Set POST data
    if ($method === 'POST' && $data) {
        $_POST = $data;
    }
    
    // Mock headers
    $_SERVER['HTTP_X_API_KEY'] = $apiKey;
    
    // Start output buffering to capture response
    ob_start();
    
    try {
        // Load and execute the API
        $apiFile = __DIR__ . '/index.php';
        if (!file_exists($apiFile)) {
            throw new Exception("API file not found: $apiFile");
        }
        
        // Simulate routing - remove /api/ prefix if present
        if (strpos($path, '/api/') === 0) {
            $path = substr($path, 5);
        }
        // Handle root endpoint
        if ($path === '/') {
            $path = '';
        }
        
        // Load authentication
        require_once __DIR__ . '/../config/database.php';
        
        // Only define classes once
        if (!class_exists('ApiAuth', false)) {
            class ApiAuth {
                private $pdo;
            
            public function __construct($pdo) {
                $this->pdo = $pdo;
            }
            
            public function authenticate() {
                $apiKey = $this->getApiKey();
                
                if (!$apiKey) {
                    $this->sendError('API key is required', 401);
                }
                
                $client = $this->validateApiKey($apiKey);
                
                if (!$client) {
                    $this->sendError('Invalid API key', 401);
                }
                
                return $client;
            }
            
            // Make sendError throw exception for CLI testing
            private function sendError($message, $code = 400) {
                echo json_encode([
                    'success' => false,
                    'error' => $message,
                    'code' => $code
                ]);
                throw new Exception('API_RESPONSE_ERROR');
            }
            
            private function getApiKey() {
                return isset($_SERVER['HTTP_X_API_KEY']) ? $_SERVER['HTTP_X_API_KEY'] : null;
            }
            
            private function validateApiKey($apiKey) {
                try {
                    $stmt = $this->pdo->prepare("
                        SELECT id, api_key, client_name, client_website, client_email, 
                               permissions, rate_limit_per_hour, is_active, usage_count
                        FROM api_keys 
                        WHERE is_active = 1
                    ");
                    $stmt->execute();
                    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    foreach ($clients as $client) {
                        if (password_verify($apiKey, $client['api_key'])) {
                            $client['permissions'] = json_decode($client['permissions'], true) ?? [];
                            return $client;
                        }
                    }
                    
                    return null;
                } catch (PDOException $e) {
                    error_log("API Auth Error: " . $e->getMessage());
                    return null;
                }
            }
            
            public function checkPermission($client, $permission) {
                return in_array($permission, $client['permissions']);
            }
        }
        
        if (!class_exists('ApiResponse', false)) {
            class ApiResponse {
            public static function success($data = null, $message = 'Success', $code = 200) {
                echo json_encode([
                    'success' => true,
                    'message' => $message,
                    'data' => $data,
                    'timestamp' => date('c')
                ]);
                // Use return instead of exit for CLI testing
                throw new Exception('API_RESPONSE_SUCCESS');
            }
            
            public static function error($message, $code = 400, $details = null) {
                echo json_encode([
                    'success' => false,
                    'error' => $message,
                    'details' => $details,
                    'code' => $code,
                    'timestamp' => date('c')
                ]);
                // Use return instead of exit for CLI testing
                throw new Exception('API_RESPONSE_ERROR');
            }
            
            public static function validationError($errors) {
                self::error('Validation failed', 422, $errors);
            }
        }
        }
        
        // Make $pdo globally available for endpoint files
        $GLOBALS['pdo'] = $pdo;
        
        // Initialize authentication and make globally available
        $auth = new ApiAuth($pdo);
        $client = $auth->authenticate();
        $GLOBALS['auth'] = $auth;
        $GLOBALS['client'] = $client;
        
        // Define constant to allow access to endpoint files
        define('API_ACCESS_ALLOWED', true);
        
        // Route the request
        switch ($path) {
            case 'rooms':
                require_once __DIR__ . '/rooms.php';
                break;
            case 'availability':
                require_once __DIR__ . '/availability.php';
                break;
            case 'bookings':
                if ($method === 'POST') {
                    // Simulate POST data
                    $GLOBALS['input'] = $data;
                    require_once __DIR__ . '/bookings.php';
                } elseif ($method === 'GET' && isset($_GET['id'])) {
                    require_once __DIR__ . '/booking-details.php';
                } else {
                    ApiResponse::error('Method not allowed or missing booking ID', 405);
                }
                break;
            case '':
                ApiResponse::success([
                    'api' => 'Liwonde Sun Hotel Booking API',
                    'version' => '1.0.0',
                    'endpoints' => [
                        'GET /api/rooms' => 'List available rooms',
                        'GET /api/availability' => 'Check room availability',
                        'POST /api/bookings' => 'Create a new booking',
                        'GET /api/bookings?id={id}' => 'Get booking status'
                    ]
                ]);
                break;
            default:
                ApiResponse::error('Endpoint not found', 404);
        }
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage(),
            'code' => 500
        ]);
    }
    
    $response = ob_get_clean();
    return json_decode($response, true);
}

// Run tests
$testsPassed = 0;
$testsFailed = 0;

// Test 1: API Info
echo "\n" . str_repeat("=", 60) . "\n";
echo "TEST 1: API Information\n";
echo str_repeat("=", 60) . "\n";

$result = apiRequest('/', 'GET');
if ($result['success'] ?? false) {
    echo "✓ PASSED: API info endpoint working\n";
    echo "  API Name: " . ($result['data']['api'] ?? 'N/A') . "\n";
    echo "  Version: " . ($result['data']['version'] ?? 'N/A') . "\n";
    $testsPassed++;
} else {
    echo "✗ FAILED: " . ($result['error'] ?? 'Unknown error') . "\n";
    $testsFailed++;
}

// Test 2: Get Rooms
echo "\n" . str_repeat("=", 60) . "\n";
echo "TEST 2: Get Available Rooms\n";
echo str_repeat("=", 60) . "\n";

$result = apiRequest('/rooms', 'GET');
if ($result['success'] ?? false) {
    $roomCount = isset($result['data']['rooms']) ? count($result['data']['rooms']) : 0;
    echo "✓ PASSED: Retrieved $roomCount rooms\n";
    if ($roomCount > 0) {
        echo "  Sample room: " . $result['data']['rooms'][0]['name'] . "\n";
        echo "  Room ID: " . $result['data']['rooms'][0]['id'] . "\n";
        $testRoomId = $result['data']['rooms'][0]['id'];
    }
    $testsPassed++;
} else {
    echo "✗ FAILED: " . ($result['error'] ?? 'Unknown error') . "\n";
    $testsFailed++;
}

// Test 3: Check Availability
echo "\n" . str_repeat("=", 60) . "\n";
echo "TEST 3: Check Room Availability\n";
echo str_repeat("=", 60) . "\n";

$testRoomId = isset($testRoomId) ? $testRoomId : 1;
$checkInDate = date('Y-m-d', strtotime('+30 days'));
$checkOutDate = date('Y-m-d', strtotime('+32 days'));

echo "Testing Room ID: $testRoomId\n";
echo "Check-in: $checkInDate\n";
echo "Check-out: $checkOutDate\n\n";

$result = apiRequest("/availability?room_id=$testRoomId&check_in=$checkInDate&check_out=$checkOutDate", 'GET');
if ($result['success'] ?? false) {
    $available = $result['data']['available'] ?? false;
    if ($available) {
        echo "✓ PASSED: Room is available\n";
        $currencySymbol = getSetting('currency_symbol', 'MWK');
        echo " Price per night: $currencySymbol " . number_format($result['data']['pricing']['price_per_night'] ?? 0) . "\n";
        echo "  Total price: $currencySymbol " . number_format($result['data']['pricing']['total'] ?? 0) . "\n";
    } else {
        echo "⚠ WARNING: Room not available (this is valid if room is booked)\n";
    }
    $testsPassed++;
} else {
    echo "✗ FAILED: " . ($result['error'] ?? 'Unknown error') . "\n";
    $testsFailed++;
}

// Test 4: Create Booking
echo "\n" . str_repeat("=", 60) . "\n";
echo "TEST 4: Create Booking\n";
echo str_repeat("=", 60) . "\n";

$bookingData = [
    'room_id' => $testRoomId,
    'guest_name' => 'CLI Test User',
    'guest_email' => 'cli-test@example.com',
    'guest_phone' => '+265987654321',
    'guest_country' => 'Malawi',
    'guest_address' => 'Test Address, CLI Test',
    'number_of_guests' => 2,
    'check_in_date' => $checkInDate,
    'check_out_date' => $checkOutDate,
    'special_requests' => 'CLI Test Booking - Please Ignore'
];

$result = apiRequest('/bookings', 'POST', $bookingData);
if ($result['success'] ?? false) {
    echo "✓ PASSED: Booking created successfully\n";
    echo "  Booking Reference: " . ($result['data']['booking']['booking_reference'] ?? 'N/A') . "\n";
    echo "  Status: " . ($result['data']['booking']['status'] ?? 'N/A') . "\n";
    echo "  Total Amount: $currencySymbol " . number_format($result['data']['booking']['pricing']['total_amount'] ?? 0) . "\n";
    $bookingRef = $result['data']['booking']['booking_reference'] ?? null;
    $testsPassed++;
} else {
    echo "✗ FAILED: " . ($result['error'] ?? 'Unknown error') . "\n";
    if (isset($result['details'])) {
        echo "  Details: " . json_encode($result['details']) . "\n";
    }
    $testsFailed++;
}

// Test 5: Get Booking Details (if booking was created)
if (isset($bookingRef)) {
    echo "\n" . str_repeat("=", 60) . "\n";
    echo "TEST 5: Get Booking Details\n";
    echo str_repeat("=", 60) . "\n";
    
    echo "Fetching booking: $bookingRef\n\n";
    
    $result = apiRequest("/bookings?id=$bookingRef", 'GET');
    if ($result['success'] ?? false) {
        echo "✓ PASSED: Booking details retrieved\n";
        echo "  Guest: " . ($result['data']['booking']['guest']['name'] ?? 'N/A') . "\n";
        echo "  Email: " . ($result['data']['booking']['guest']['email'] ?? 'N/A') . "\n";
        echo "  Check-in: " . ($result['data']['booking']['dates']['check_in'] ?? 'N/A') . "\n";
        $testsPassed++;
    } else {
        echo "✗ FAILED: " . ($result['error'] ?? 'Unknown error') . "\n";
        $testsFailed++;
    }
}

// Test 6: Error Handling - Invalid API Key
echo "\n" . str_repeat("=", 60) . "\n";
echo "TEST 6: Error Handling - Invalid API Key\n";
echo str_repeat("=", 60) . "\n";

$_SERVER['HTTP_X_API_KEY'] = 'invalid_key_12345';
$result = apiRequest('/rooms', 'GET');
if (($result['success'] ?? true) === false) {
    echo "✓ PASSED: Invalid API key rejected\n";
    echo "  Error: " . ($result['error'] ?? 'N/A') . "\n";
    echo "  Code: " . ($result['code'] ?? 'N/A') . "\n";
    $testsPassed++;
} else {
    echo "✗ FAILED: Invalid API key was accepted\n";
    $testsFailed++;
}

// Summary
echo "\n" . str_repeat("=", 60) . "\n";
echo "TEST SUMMARY\n";
echo str_repeat("=", 60) . "\n";
echo "Total Tests: " . ($testsPassed + $testsFailed) . "\n";
echo "✓ Passed: $testsPassed\n";
echo "✗ Failed: $testsFailed\n";

if ($testsFailed === 0) {
    echo "\n✅ ALL TESTS PASSED! The booking API is working correctly.\n";
} else {
    echo "\n⚠️ Some tests failed. Please check the errors above.\n";
    echo "\nCommon issues:\n";
    echo "  - API tables not created (run setup-api-tables.php)\n";
    echo "  - No rooms in database\n";
    echo "  - Database connection issues\n";
    echo "  - Missing permissions\n";
}

echo "\nFor more detailed testing, use the web interface:\n";
echo "  Open: api/test-api.php in your browser\n\n";