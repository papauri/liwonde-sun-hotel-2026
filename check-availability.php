<?php
/**
 * Check Room Availability Endpoint
 * Returns availability status for a room on specific dates
 * Used by booking form for real-time availability checking
 */

header('Content-Type: application/json');

require_once 'config/database.php';

// Get parameters
$room_id = isset($_GET['room_id']) ? (int)$_GET['room_id'] : 0;
$check_in = isset($_GET['check_in']) ? $_GET['check_in'] : '';
$check_out = isset($_GET['check_out']) ? $_GET['check_out'] : '';

// Validate required parameters
if (!$room_id || !$check_in || !$check_out) {
    echo json_encode([
        'success' => false,
        'available' => false,
        'message' => 'Missing required parameters'
    ]);
    exit;
}

// Validate dates
try {
    $check_in_date = new DateTime($check_in);
    $check_out_date = new DateTime($check_out);
    $today = new DateTime();
    $today->setTime(0, 0, 0);
    
    if ($check_in_date < $today) {
        echo json_encode([
            'success' => false,
            'available' => false,
            'message' => 'Check-in date cannot be in the past'
        ]);
        exit;
    }
    
    if ($check_out_date <= $check_in_date) {
        echo json_encode([
            'success' => false,
            'available' => false,
            'message' => 'Check-out date must be after check-in date'
        ]);
        exit;
    }
    
    // Check date range (max 30 nights)
    $nights = $check_in_date->diff($check_out_date)->days;
    if ($nights > 30) {
        echo json_encode([
            'success' => false,
            'available' => false,
            'message' => 'Maximum stay is 30 nights'
        ]);
        exit;
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'available' => false,
        'message' => 'Invalid date format'
    ]);
    exit;
}

// Check if room exists
try {
    $stmt = $pdo->prepare("SELECT id, name, price_per_night, max_guests FROM rooms WHERE id = ? AND is_active = 1");
    $stmt->execute([$room_id]);
    $room = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$room) {
        echo json_encode([
            'success' => false,
            'available' => false,
            'message' => 'Room not found'
        ]);
        exit;
    }
    
    // Check availability using the enhanced function
    $availability = checkRoomAvailability($room_id, $check_in, $check_out);
    
    if ($availability['available']) {
        // Room is available
        $total_amount = $room['price_per_night'] * $nights;
        
        echo json_encode([
            'success' => true,
            'available' => true,
            'message' => 'Room is available for your selected dates',
            'room' => $room,
            'nights' => $nights,
            'total' => $total_amount,
            'currency_symbol' => getSetting('currency_symbol', 'MWK')
        ]);
    } else {
        // Room is not available
        $message = 'This room is not available for the selected dates';
        
        if (!empty($availability['conflicts'])) {
            $conflict_details = [];
            foreach ($availability['conflicts'] as $conflict) {
                if ($conflict['type'] === 'blocked') {
                    $conflict_details[] = 'Blocked: ' . $conflict['date'];
                } elseif ($conflict['type'] === 'booking') {
                    $conflict_details[] = 'Booked: ' . $conflict['date'];
                }
            }
            if (!empty($conflict_details)) {
                $message .= ' (' . implode(', ', $conflict_details) . ')';
            }
        }
        
        echo json_encode([
            'success' => true,
            'available' => false,
            'message' => $message,
            'conflicts' => $availability['conflicts'] ?? []
        ]);
    }
    
} catch (PDOException $e) {
    error_log("Availability check error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'available' => false,
        'message' => 'Unable to check availability. Please try again.'
    ]);
}
