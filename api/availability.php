<?php
/**
 * Availability API Endpoint
 * GET /api/availability
 *
 * Checks room availability for given dates
 * Requires permission: availability.check
 *
 * Parameters:
 * - room_id (required): Room ID to check
 * - check_in (required): Check-in date (YYYY-MM-DD)
 * - check_out (required): Check-out date (YYYY-MM-DD)
 * - number_of_guests (optional): Number of guests
 *
 * SECURITY: This file must only be accessed through api/index.php
 * Direct access is blocked to prevent authentication bypass
 */

// Prevent direct access - must be accessed through api/index.php router
if (!defined('API_ACCESS_ALLOWED') || !isset($auth) || !isset($client)) {
    http_response_code(403);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'error' => 'Direct access to this endpoint is not allowed',
        'code' => 403,
        'message' => 'Please use the API router at /api/availability'
    ]);
    exit;
}

// Check permission
if (!$auth->checkPermission($client, 'availability.check')) {
    ApiResponse::error('Permission denied: availability.check', 403);
}

try {
    // Check if this is an individual rooms availability request
    $roomTypeId = isset($_GET['room_type_id']) ? (int)$_GET['room_type_id'] : null;
    $individualRoomsRequest = isset($_GET['individual_rooms']) && $_GET['individual_rooms'] === 'true';
    
    // Get parameters
    $roomId = isset($_GET['room_id']) ? (int)$_GET['room_id'] : null;
    $checkIn = isset($_GET['check_in']) ? $_GET['check_in'] : null;
    $checkOut = isset($_GET['check_out']) ? $_GET['check_out'] : null;
    $numberOfGuests = isset($_GET['number_of_guests']) ? (int)$_GET['number_of_guests'] : null;
    $excludeBookingId = isset($_GET['exclude_booking_id']) ? (int)$_GET['exclude_booking_id'] : null;
    
    // Handle individual rooms availability request
    if ($individualRoomsRequest && $roomTypeId) {
        getIndividualRoomsAvailability($roomTypeId, $checkIn, $checkOut, $excludeBookingId);
        return;
    }
    
    // Validate required parameters
    if (!$roomId || !$checkIn || !$checkOut) {
        ApiResponse::validationError([
            'room_id' => $roomId ? null : 'Room ID is required',
            'check_in' => $checkIn ? null : 'Check-in date is required',
            'check_out' => $checkOut ? null : 'Check-out date is required'
        ]);
    }
    
    // Validate dates
    $checkInDate = new DateTime($checkIn);
    $checkOutDate = new DateTime($checkOut);
    $today = new DateTime('today');
    
    if ($checkInDate < $today) {
        ApiResponse::error('Check-in date cannot be in the past', 400);
    }
    
    if ($checkOutDate <= $checkInDate) {
        ApiResponse::error('Check-out date must be after check-in date', 400);
    }
    
    // Check advance booking restriction
    $maxAdvanceDays = (int)getSetting('max_advance_booking_days', 30);
    $maxAdvanceDate = new DateTime();
    $maxAdvanceDate->modify('+' . $maxAdvanceDays . ' days');
    
    if ($checkInDate > $maxAdvanceDate) {
        ApiResponse::error("Bookings can only be made up to {$maxAdvanceDays} days in advance. Please select an earlier check-in date.", 400);
    }
    
    // Check if room exists and is active
    $roomStmt = $pdo->prepare("
        SELECT id, name, price_per_night, max_guests, rooms_available 
        FROM rooms 
        WHERE id = ? AND is_active = 1
    ");
    $roomStmt->execute([$roomId]);
    $room = $roomStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$room) {
        ApiResponse::error('Room not found or not available', 404);
    }
    
    // Check capacity if number of guests provided
    if ($numberOfGuests && $numberOfGuests > $room['max_guests']) {
        ApiResponse::error("This room can accommodate maximum {$room['max_guests']} guests", 400);
    }
    
    // Check availability using existing function from config/database.php
    // isRoomAvailable() is already loaded via config/database.php
    $available = isRoomAvailable($roomId, $checkIn, $checkOut);
    
    if ($available) {
        // Calculate nights and total
        $nights = $checkInDate->diff($checkOutDate)->days;
        $total = $room['price_per_night'] * $nights;
        
        // Get any conflicting bookings for detailed info
        $conflictsStmt = $pdo->prepare("
            SELECT 
                b.booking_reference,
                b.guest_name,
                b.check_in_date,
                b.check_out_date,
                b.status
            FROM bookings b
            WHERE b.room_id = ?
            AND b.status IN ('pending', 'confirmed', 'checked-in')
            AND (
                (b.check_in_date < ? AND b.check_out_date > ?) OR
                (b.check_in_date >= ? AND b.check_in_date < ?)
            )
            ORDER BY b.check_in_date ASC
        ");
        $conflictsStmt->execute([$roomId, $checkOut, $checkIn, $checkIn, $checkOut]);
        $conflicts = $conflictsStmt->fetchAll(PDO::FETCH_ASSOC);
        
        $response = [
            'available' => true,
            'room' => [
                'id' => $room['id'],
                'name' => $room['name'],
                'price_per_night' => (float)$room['price_per_night'],
                'max_guests' => (int)$room['max_guests'],
                'rooms_available' => (int)$room['rooms_available']
            ],
            'dates' => [
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'nights' => $nights
            ],
            'pricing' => [
                'price_per_night' => (float)$room['price_per_night'],
                'total' => (float)$total,
                'currency' => getSetting('currency_symbol', 'MWK'),
                'currency_code' => getSetting('currency_code', 'MWK')
            ],
            'conflicts' => $conflicts,
            'message' => 'Room is available for your selected dates'
        ];
        
        ApiResponse::success($response, 'Room available');
    } else {
        // Get conflicting bookings for detailed error
        $conflictsStmt = $pdo->prepare("
            SELECT 
                b.booking_reference,
                b.guest_name,
                b.check_in_date,
                b.check_out_date,
                b.status
            FROM bookings b
            WHERE b.room_id = ?
            AND b.status IN ('pending', 'confirmed', 'checked-in')
            AND (
                (b.check_in_date < ? AND b.check_out_date > ?) OR
                (b.check_in_date >= ? AND b.check_in_date < ?)
            )
            ORDER BY b.check_in_date ASC
        ");
        $conflictsStmt->execute([$roomId, $checkOut, $checkIn, $checkIn, $checkOut]);
        $conflicts = $conflictsStmt->fetchAll(PDO::FETCH_ASSOC);
        
        $response = [
            'available' => false,
            'room' => [
                'id' => $room['id'],
                'name' => $room['name']
            ],
            'dates' => [
                'check_in' => $checkIn,
                'check_out' => $checkOut
            ],
            'conflicts' => $conflicts,
            'message' => 'This room is not available for the selected dates. Please choose different dates.'
        ];
        
        ApiResponse::success($response, 'Room not available');
    }
    
} catch (Exception $e) {
    error_log("Availability API Error: " . $e->getMessage());
    ApiResponse::error('Failed to check availability: ' . $e->getMessage(), 500);
}

/**
 * Get individual rooms availability for a room type and date range
 *
 * Parameters:
 * - room_type_id (required): Room type ID
 * - check_in (required): Check-in date (YYYY-MM-DD)
 * - check_out (required): Check-out date (YYYY-MM-DD)
 * - exclude_booking_id (optional): Exclude this booking from conflicts
 */
function getIndividualRoomsAvailability($roomTypeId, $checkIn, $checkOut, $excludeBookingId = null) {
    global $pdo;
    
    // Validate dates
    $checkInDate = new DateTime($checkIn);
    $checkOutDate = new DateTime($checkOut);
    $today = new DateTime('today');
    
    if ($checkInDate < $today) {
        ApiResponse::error('Check-in date cannot be in the past', 400);
    }
    
    if ($checkOutDate <= $checkInDate) {
        ApiResponse::error('Check-out date must be after check-in date', 400);
    }
    
    // Check if room type exists
    $typeStmt = $pdo->prepare("
        SELECT id, name, slug, price_per_night, max_guests
        FROM room_types
        WHERE id = ? AND is_active = 1
    ");
    $typeStmt->execute([$roomTypeId]);
    $roomType = $typeStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$roomType) {
        ApiResponse::error('Room type not found or inactive', 404);
    }
    
    // Get all individual rooms for this type
    $roomsStmt = $pdo->prepare("
        SELECT
            ir.id,
            ir.room_number,
            ir.room_name,
            ir.floor,
            ir.status,
            ir.specific_amenities
        FROM individual_rooms ir
        WHERE ir.room_type_id = ? AND ir.is_active = 1
        ORDER BY ir.display_order ASC, ir.room_number ASC
    ");
    $roomsStmt->execute([$roomTypeId]);
    $individualRooms = $roomsStmt->fetchAll(PDO::FETCH_ASSOC);
    
    $availableRooms = [];
    $unavailableRooms = [];
    
    foreach ($individualRooms as $room) {
        // Check if room is available based on status
        if (!in_array($room['status'], ['available', 'cleaning'])) {
            // Room is not available due to status
            $unavailableRooms[] = [
                'id' => $room['id'],
                'room_number' => $room['room_number'],
                'room_name' => $room['room_name'],
                'floor' => $room['floor'],
                'status' => $room['status'],
                'reason' => getRoomStatusReason($room['status'])
            ];
            continue;
        }
        
        // Check for conflicting bookings
        $conflictStmt = $pdo->prepare("
            SELECT
                b.id,
                b.booking_reference,
                b.guest_name,
                b.check_in_date,
                b.check_out_date,
                b.status
            FROM bookings b
            WHERE b.individual_room_id = ?
            AND b.status IN ('pending', 'confirmed', 'checked-in')
            AND NOT (b.check_out_date <= ? OR b.check_in_date >= ?)
            " . ($excludeBookingId ? "AND b.id != ?" : "") . "
            LIMIT 1
        ");
        
        $params = [$room['id'], $checkIn, $checkOut];
        if ($excludeBookingId) {
            $params[] = $excludeBookingId;
        }
        
        $conflictStmt->execute($params);
        $conflict = $conflictStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($conflict) {
            // Room has a booking conflict
            $unavailableRooms[] = [
                'id' => $room['id'],
                'room_number' => $room['room_number'],
                'room_name' => $room['room_name'],
                'floor' => $room['floor'],
                'status' => 'booked',
                'reason' => 'Booked by ' . $conflict['guest_name'],
                'booking_reference' => $conflict['booking_reference'],
                'conflicting_booking' => $conflict
            ];
        } else {
            // Room is available
            $availableRooms[] = [
                'id' => $room['id'],
                'room_number' => $room['room_number'],
                'room_name' => $room['room_name'],
                'floor' => $room['floor'],
                'status' => $room['status'],
                'specific_amenities' => $room['specific_amenities'] ? json_decode($room['specific_amenities'], true) : []
            ];
        }
    }
    
    // Check for blocked dates
    $blockedStmt = $pdo->prepare("
        SELECT block_date, reason
        FROM room_blocked_dates
        WHERE (room_id = ? OR room_id IS NULL)
        AND block_date >= ? AND block_date < ?
        ORDER BY block_date ASC
    ");
    $blockedStmt->execute([$roomTypeId, $checkIn, $checkOut]);
    $blockedDates = $blockedStmt->fetchAll(PDO::FETCH_ASSOC);
    
    $response = [
        'available' => count($availableRooms) > 0,
        'room_type' => [
            'id' => $roomType['id'],
            'name' => $roomType['name'],
            'slug' => $roomType['slug'],
            'price_per_night' => (float)$roomType['price_per_night'],
            'max_guests' => (int)$roomType['max_guests']
        ],
        'dates' => [
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'nights' => $checkInDate->diff($checkOutDate)->days
        ],
        'available_rooms' => $availableRooms,
        'unavailable_rooms' => $unavailableRooms,
        'blocked_dates' => $blockedDates,
        'summary' => [
            'total_rooms' => count($individualRooms),
            'available_count' => count($availableRooms),
            'unavailable_count' => count($unavailableRooms)
        ]
    ];
    
    ApiResponse::success($response, 'Individual rooms availability retrieved');
}

/**
 * Get human-readable reason for room status
 */
function getRoomStatusReason($status) {
    $reasons = [
        'occupied' => 'Currently occupied by guest',
        'maintenance' => 'Under maintenance',
        'cleaning' => 'Being cleaned',
        'out_of_order' => 'Out of order'
    ];
    return $reasons[$status] ?? 'Not available';
}