<?php
/**
 * Room Availability API
 * Returns JSON response for AJAX availability checking
 */

header('Content-Type: application/json');
require_once 'config/database.php';

// Only handle GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

try {
    $room_id = filter_var($_GET['room_id'] ?? 0, FILTER_VALIDATE_INT);
    $check_in = $_GET['check_in'] ?? '';
    $check_out = $_GET['check_out'] ?? '';

    // Validate inputs
    if (!$room_id || !$check_in || !$check_out) {
        throw new Exception('Missing required parameters: room_id, check_in, check_out');
    }

    // Validate dates
    $check_in_date = new DateTime($check_in);
    $check_out_date = new DateTime($check_out);
    $today = new DateTime('today');

    if ($check_in_date < $today) {
        throw new Exception('Check-in date cannot be in the past');
    }

    if ($check_out_date <= $check_in_date) {
        throw new Exception('Check-out date must be after check-in date');
    }

    // Check advance booking restriction
    $max_advance_days = (int)getSetting('max_advance_booking_days', 30);
    $max_advance_date = new DateTime();
    $max_advance_date->modify('+' . $max_advance_days . ' days');
    
    if ($check_in_date > $max_advance_date) {
        throw new Exception("Bookings can only be made up to {$max_advance_days} days in advance. Please select an earlier check-in date.");
    }

    // Check if room exists and is active
    $room_stmt = $pdo->prepare("SELECT id, name, price_per_night, max_guests FROM rooms WHERE id = ? AND is_active = 1");
    $room_stmt->execute([$room_id]);
    $room = $room_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$room) {
        throw new Exception('Room not found or not available');
    }

    // Check availability using our helper function
    $available = isRoomAvailable($room_id, $check_in, $check_out);

    if ($available) {
        // Calculate nights and total
        $nights = $check_in_date->diff($check_out_date)->days;
        $total = $room['price_per_night'] * $nights;

        echo json_encode([
            'available' => true,
            'room' => $room,
            'nights' => $nights,
            'total' => $total,
            'check_in' => $check_in,
            'check_out' => $check_out,
            'message' => 'Room is available for your selected dates'
        ]);
    } else {
        echo json_encode([
            'available' => false,
            'room' => $room,
            'message' => 'This room is not available for the selected dates. Please choose different dates.'
        ]);
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'available' => false,
        'error' => $e->getMessage()
    ]);
} catch (PDOException $e) {
    error_log("Availability API Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'available' => false,
        'error' => 'Unable to check availability. Please try again.'
    ]);
}