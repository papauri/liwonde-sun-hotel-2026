<?php
/**
 * Liwonde Sun Hotel - PHP Integration Example
 * 
 * This file demonstrates how to integrate the booking API
 * using PHP on your server.
 */

// ============================================
// CONFIGURATION - UPDATE THESE VALUES
// ============================================
define('API_BASE_URL', 'https://liwondesunhotel.com/api/');
define('API_KEY', 'YOUR_API_KEY_HERE');
define('PARTNER_NAME', 'YOUR_COMPANY_NAME');
// Note: These are example values. In production, these should be fetched from the database
// using getSetting() function from config/database.php

// ============================================
// API REQUEST FUNCTION
// ============================================
function apiRequest($endpoint, $method = 'GET', $data = null) {
    $url = API_BASE_URL . $endpoint;
    
    $ch = curl_init($url);
    
    // Set headers
    $headers = [
        'X-API-Key: ' . API_KEY,
        'Content-Type: application/json',
        'User-Agent: LiwondeHotel-PHP-Client/1.0'
    ];
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        return [
            'success' => false,
            'error' => 'cURL Error: ' . $error,
            'code' => 500
        ];
    }
    
    $result = json_decode($response, true);
    
    return [
        'success' => $result['success'] ?? false,
        'data' => $result['data'] ?? null,
        'error' => $result['error'] ?? null,
        'code' => $httpCode
    ];
}

// ============================================
// SITE SETTINGS FUNCTIONS
// ============================================
function getSiteSettings() {
    $result = apiRequest('site-settings');
    
    if ($result['success'] && isset($result['data'])) {
        return $result['data'];
    }
    
    // Return default settings if API fails
    // Note: In production, these should be fetched from the database
    // using getSetting() function from config/database.php
    return [
        'hotel' => [
            'name' => getSetting('site_name', 'Liwonde Sun Hotel'),
            'tagline' => getSetting('site_tagline', 'Where Luxury Meets Nature'),
            'url' => getSetting('site_url', 'https://liwondesunhotel.com'),
            'logo' => getSetting('site_logo', '')
        ],
        'currency' => [
            'symbol' => getSetting('currency_symbol', 'MWK'),
            'code' => getSetting('currency_code', 'MWK')
        ]
    ];
}

// ============================================
// ROOMS FUNCTIONS
// ============================================
function getRooms() {
    $result = apiRequest('rooms');
    
    if ($result['success'] && isset($result['data']['rooms'])) {
        return $result['data']['rooms'];
    }
    
    return [];
}

function displayRooms($rooms, $siteSettings = null) {
    if (empty($rooms)) {
        echo '<div class="alert alert-info">No rooms available at this time.</div>';
        return;
    }
    
    $currencySymbol = $siteSettings['currency']['symbol'] ?? 'MWK';
    
    echo '<div class="rooms-grid">';
    
    foreach ($rooms as $room) {
        $imageUrl = !empty($room['image_url']) ? htmlspecialchars($room['image_url']) : 'https://via.placeholder.com/400x200?text=' . urlencode($room['name']);
        $price = number_format($room['price_per_night']);
        
        echo '
            <div class="room-card">
                <img src="' . $imageUrl . '" alt="' . htmlspecialchars($room['name']) . '" class="room-image">
                <div class="room-content">
                    <h3 class="room-name">' . htmlspecialchars($room['name']) . '</h3>
                    <p class="room-description">' . htmlspecialchars($room['short_description'] ?? '') . '</p>
                    <div class="room-details">
                        <span class="room-price">' . htmlspecialchars($currencySymbol) . ' ' . $price . '/night</span>
                        <span class="room-guests">Max ' . $room['max_guests'] . ' guests</span>
                    </div>
                    <form method="POST" action="">
                        <input type="hidden" name="room_id" value="' . $room['id'] . '">
                        <input type="hidden" name="room_name" value="' . htmlspecialchars($room['name']) . '">
                        <input type="hidden" name="room_price" value="' . $room['price_per_night'] . '">
                        <button type="submit" name="action" value="check_availability" class="btn btn-full">
                            Check Availability
                        </button>
                    </form>
                </div>
            </div>
        ';
    }
    
    echo '</div>';
}

// ============================================
// AVAILABILITY FUNCTIONS
// ============================================
function checkAvailability($roomId, $checkIn, $checkOut) {
    $result = apiRequest("availability?room_id={$roomId}&check_in={$checkIn}&check_out={$checkOut}");
    
    if ($result['success']) {
        return $result['data'];
    }
    
    return null;
}

function displayAvailabilityForm($roomId, $roomName, $checkIn, $checkOut, $availabilityData, $siteSettings = null) {
    if (!$availabilityData || !$availabilityData['available']) {
        echo '<div class="alert alert-error">';
        echo '<strong>Room Not Available</strong><br>';
        echo $availabilityData['message'] ?? 'This room is not available for the selected dates.';
        echo '<br><a href="?" class="btn btn-secondary">Try Different Dates</a>';
        echo '</div>';
        return;
    }
    
    $room = $availabilityData['room'];
    $dates = $availabilityData['dates'];
    $pricing = $availabilityData['pricing'];
    $currencySymbol = $siteSettings['currency']['symbol'] ?? 'MWK';
    
    echo '
        <div class="alert alert-success">
            <strong>Room Available!</strong><br>
            <strong>Dates:</strong> ' . htmlspecialchars($checkIn) . ' to ' . htmlspecialchars($checkOut) . ' (' . $dates['nights'] . ' nights)<br>
            <strong>Total:</strong> ' . htmlspecialchars($currencySymbol) . ' ' . number_format($pricing['total']) . '
        </div>
        
        <form method="POST" action="" id="booking-form">
            <input type="hidden" name="room_id" value="' . $roomId . '">
            <input type="hidden" name="check_in_date" value="' . htmlspecialchars($checkIn) . '">
            <input type="hidden" name="check_out_date" value="' . htmlspecialchars($checkOut) . '">
            
            <div class="form-row">
                <div class="form-group">
                    <label for="guest_name">Full Name *</label>
                    <input type="text" id="guest_name" name="guest_name" required>
                </div>
                <div class="form-group">
                    <label for="guest_email">Email *</label>
                    <input type="email" id="guest_email" name="guest_email" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="guest_phone">Phone *</label>
                    <input type="tel" id="guest_phone" name="guest_phone" required>
                </div>
                <div class="form-group">
                    <label for="guest_country">Country</label>
                    <input type="text" id="guest_country" name="guest_country">
                </div>
            </div>
            
            <div class="form-group">
                <label for="guest_address">Address</label>
                <input type="text" id="guest_address" name="guest_address">
            </div>
            
            <div class="form-group">
                <label for="number_of_guests">Number of Guests *</label>
                <select id="number_of_guests" name="number_of_guests" required>
    ';
    
    for ($i = 1; $i <= $room['max_guests']; $i++) {
        $selected = isset($_POST['number_of_guests']) && $_POST['number_of_guests'] == $i ? ' selected' : '';
        echo '<option value="' . $i . '"' . $selected . '>' . $i . ' Guest' . ($i > 1 ? 's' : '') . '</option>';
    }
    
    echo '
                </select>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="check_in">Check-in Date *</label>
                    <input type="date" id="check_in" name="check_in_date" value="' . htmlspecialchars($checkIn) . '" required>
                </div>
                <div class="form-group">
                    <label for="check_out">Check-out Date *</label>
                    <input type="date" id="check_out" name="check_out_date" value="' . htmlspecialchars($checkOut) . '" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="special_requests">Special Requests</label>
                <textarea id="special_requests" name="special_requests" rows="3"></textarea>
            </div>
            
            <button type="submit" name="action" value="create_booking" class="btn btn-full">
                Confirm Booking
            </button>
            <a href="?" class="btn btn-secondary btn-full">Cancel</a>
        </form>
    ';
}

// ============================================
// BOOKING FUNCTIONS
// ============================================
function createBooking($bookingData) {
    $result = apiRequest('bookings', 'POST', $bookingData);
    
    if ($result['success']) {
        return $result['data'];
    }
    
    return null;
}

function displayConfirmation($bookingData, $siteSettings = null) {
    $booking = $bookingData['booking'];
    $nextSteps = $bookingData['next_steps'] ?? [];
    $currencySymbol = $siteSettings['currency']['symbol'] ?? 'MWK';
    
    echo '
        <div class="booking-confirmation">
            <h2>✅ Booking Confirmed!</h2>
            <div class="booking-details">
                <p><strong>Booking Reference:</strong> ' . htmlspecialchars($booking['booking_reference']) . '</p>
                <p><strong>Room:</strong> ' . htmlspecialchars($booking['room']['name']) . '</p>
                <p><strong>Guest:</strong> ' . htmlspecialchars($booking['guest']['name']) . '</p>
                <p><strong>Email:</strong> ' . htmlspecialchars($booking['guest']['email']) . '</p>
                <p><strong>Phone:</strong> ' . htmlspecialchars($booking['guest']['phone']) . '</p>
                <p><strong>Check-in:</strong> ' . htmlspecialchars($booking['dates']['check_in']) . '</p>
                <p><strong>Check-out:</strong> ' . htmlspecialchars($booking['dates']['check_out']) . '</p>
                <p><strong>Nights:</strong> ' . $booking['dates']['nights'] . '</p>
                <p><strong>Guests:</strong> ' . $booking['details']['number_of_guests'] . '</p>
                <p><strong>Total Amount:</strong> ' . htmlspecialchars($currencySymbol) . ' ' . number_format($booking['pricing']['total_amount']) . '</p>
                <p><strong>Status:</strong> ' . htmlspecialchars($booking['status']) . '</p>
            </div>
            
            <div class="alert alert-info">
                <strong>Next Steps:</strong>
                <ul>
                    <li>' . htmlspecialchars($nextSteps['payment'] ?? 'Payment will be made at the hotel upon arrival.') . '</li>
                    <li>' . htmlspecialchars($nextSteps['confirmation'] ?? 'Your booking is pending confirmation.') . '</li>
                    <li>' . htmlspecialchars($nextSteps['contact'] ?? 'If you have any questions, please contact the hotel.') . '</li>
                </ul>
            </div>
            
            <a href="?" class="btn btn-full">Book Another Room</a>
        </div>
    ';
}

function displayError($message) {
    echo '
        <div class="alert alert-error">
            <strong>Error:</strong> ' . htmlspecialchars($message) . '
            <a href="?" class="btn btn-secondary">Try Again</a>
        </div>
    ';
}

// ============================================
// MAIN LOGIC
// ============================================
// Fetch site settings from database
$siteSettings = getSiteSettings();

$action = $_POST['action'] ?? '';
$roomId = $_POST['room_id'] ?? null;
$checkIn = $_POST['check_in_date'] ?? '';
$checkOut = $_POST['check_out_date'] ?? '';

// Handle form submissions
if ($action === 'check_availability' && $roomId) {
    // Get dates from user input or use defaults
    if (empty($checkIn)) {
        $checkIn = date('Y-m-d', strtotime('+7 days'));
    }
    if (empty($checkOut)) {
        $checkOut = date('Y-m-d', strtotime('+10 days'));
    }
    
    $availability = checkAvailability($roomId, $checkIn, $checkOut);
    $roomName = $_POST['room_name'] ?? '';
    
    if ($availability) {
        displayAvailabilityForm($roomId, $roomName, $checkIn, $checkOut, $availability, $siteSettings);
    } else {
        displayError('Failed to check room availability. Please try again.');
    }
    
} elseif ($action === 'create_booking') {
    // Validate required fields
    $required = ['room_id', 'guest_name', 'guest_email', 'guest_phone', 'number_of_guests', 'check_in_date', 'check_out_date'];
    $missing = [];
    
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            $missing[] = ucfirst(str_replace('_', ' ', $field));
        }
    }
    
    if (!empty($missing)) {
        displayError('Missing required fields: ' . implode(', ', $missing));
    } elseif (!filter_var($_POST['guest_email'], FILTER_VALIDATE_EMAIL)) {
        displayError('Invalid email address.');
    } else {
        $bookingData = [
            'room_id' => (int)$_POST['room_id'],
            'guest_name' => $_POST['guest_name'],
            'guest_email' => $_POST['guest_email'],
            'guest_phone' => $_POST['guest_phone'],
            'guest_country' => $_POST['guest_country'] ?? '',
            'guest_address' => $_POST['guest_address'] ?? '',
            'number_of_guests' => (int)$_POST['number_of_guests'],
            'check_in_date' => $_POST['check_in_date'],
            'check_out_date' => $_POST['check_out_date'],
            'special_requests' => $_POST['special_requests'] ?? ''
        ];
        
        $result = createBooking($bookingData);
        
        if ($result) {
            displayConfirmation($result, $siteSettings);
        } else {
            displayError('Failed to create booking. Please try again or contact support.');
        }
    }
    
} else {
    // Default: Show rooms
    $rooms = getRooms();
    displayRooms($rooms, $siteSettings);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($siteSettings['hotel']['name'] ?? 'Liwonde Sun Hotel'); ?> - PHP Integration</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            background: linear-gradient(135deg, #2c5aa0 0%, #1a4480 100%);
            color: white;
            padding: 40px 20px;
            text-align: center;
            border-radius: 12px;
            margin-bottom: 30px;
        }
        
        .header h1 {
            font-size: 36px;
            margin-bottom: 10px;
        }
        
        .header p {
            opacity: 0.9;
            font-size: 16px;
        }
        
        .section {
            background: white;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .section h2 {
            color: #2c5aa0;
            margin-bottom: 20px;
            font-size: 24px;
        }
        
        .rooms-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .room-card {
            border: 1px solid #e9ecef;
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .room-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }
        
        .room-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background: #e9ecef;
        }
        
        .room-content {
            padding: 20px;
        }
        
        .room-name {
            font-size: 20px;
            font-weight: 600;
            color: #2c5aa0;
            margin-bottom: 10px;
        }
        
        .room-description {
            color: #6c757d;
            font-size: 14px;
            margin-bottom: 15px;
            line-height: 1.5;
        }
        
        .room-details {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .room-price {
            font-size: 22px;
            font-weight: 700;
            color: #2c5aa0;
        }
        
        .room-guests {
            color: #6c757d;
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ced4da;
            border-radius: 6px;
            font-size: 16px;
            font-family: inherit;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #2c5aa0;
            box-shadow: 0 0 0 3px rgba(44, 90, 160, 0.1);
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        
        .alert ul {
            margin: 10px 0 0 20px;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #2c5aa0;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.3s;
            text-decoration: none;
            text-align: center;
        }
        
        .btn:hover {
            background: #1a4480;
        }
        
        .btn-full {
            width: 100%;
            display: block;
        }
        
        .btn-secondary {
            background: #6c757d;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        .booking-confirmation {
            text-align: center;
        }
        
        .booking-confirmation h2 {
            color: #28a745;
            margin-bottom: 20px;
        }
        
        .booking-details {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: left;
        }
        
        .booking-details p {
            margin: 10px 0;
        }
        
        .booking-details strong {
            color: #333;
        }
        
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .rooms-grid {
                grid-template-columns: 1fr;
            }
            
            .header h1 {
                font-size: 28px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏨 <?php echo htmlspecialchars($siteSettings['hotel']['name'] ?? 'Liwonde Sun Hotel'); ?></h1>
            <p><?php echo htmlspecialchars($siteSettings['hotel']['tagline'] ?? 'Where Luxury Meets Nature'); ?></p>
        </div>
        
        <?php
        // Content is rendered by PHP logic above
        ?>
    </div>
</body>
</html>
