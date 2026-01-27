<?php
/**
 * Booking Details API Endpoint
 * GET /api/bookings?id={id}
 * 
 * Retrieves booking details by ID or reference
 * Requires permission: bookings.read
 * 
 * Parameters:
 * - id (required): Booking ID or reference
 */

// Check permission
if (!$auth->checkPermission($client, 'bookings.read')) {
    ApiResponse::error('Permission denied: bookings.read', 403);
}

try {
    // Get booking ID or reference
    $bookingIdentifier = isset($_GET['id']) ? trim($_GET['id']) : null;
    
    if (!$bookingIdentifier) {
        ApiResponse::error('Booking ID or reference is required', 400);
    }
    
    // Determine if it's an ID (numeric) or reference (string)
    if (is_numeric($bookingIdentifier)) {
        // Search by ID
        $stmt = $pdo->prepare("
            SELECT 
                b.*,
                r.name as room_name,
                r.price_per_night,
                r.max_guests,
                r.image_url as room_image,
                r.amenities as room_amenities
            FROM bookings b
            LEFT JOIN rooms r ON b.room_id = r.id
            WHERE b.id = ?
        ");
        $stmt->execute([(int)$bookingIdentifier]);
    } else {
        // Search by reference
        $stmt = $pdo->prepare("
            SELECT 
                b.*,
                r.name as room_name,
                r.price_per_night,
                r.max_guests,
                r.image_url as room_image,
                r.amenities as room_amenities
            FROM bookings b
            LEFT JOIN rooms r ON b.room_id = r.id
            WHERE b.booking_reference = ?
        ");
        $stmt->execute([$bookingIdentifier]);
    }
    
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$booking) {
        ApiResponse::error('Booking not found', 404);
    }
    
    // Check if client has permission to view this booking
    // (In a real system, you might want to check if this booking belongs to the client's website)
    // For now, we'll allow any authenticated client to view any booking
    
    // Format room amenities
    $roomAmenities = [];
    if ($booking['room_amenities']) {
        if (strpos($booking['room_amenities'], '[') === 0) {
            $roomAmenities = json_decode($booking['room_amenities'], true);
        } else {
            $roomAmenities = array_map('trim', explode(',', $booking['room_amenities']));
        }
    }
    
    // Format response
    $response = [
        'booking' => [
            'id' => (int)$booking['id'],
            'booking_reference' => $booking['booking_reference'],
            'status' => $booking['status'],
            'payment_status' => $booking['payment_status'],
            'room' => [
                'id' => (int)$booking['room_id'],
                'name' => $booking['room_name'],
                'price_per_night' => (float)$booking['price_per_night'],
                'max_guests' => (int)$booking['max_guests'],
                'image_url' => $booking['room_image'],
                'amenities' => $roomAmenities
            ],
            'guest' => [
                'name' => $booking['guest_name'],
                'email' => $booking['guest_email'],
                'phone' => $booking['guest_phone'],
                'country' => $booking['guest_country'],
                'address' => $booking['guest_address']
            ],
            'dates' => [
                'check_in' => $booking['check_in_date'],
                'check_out' => $booking['check_out_date'],
                'nights' => (int)$booking['number_of_nights']
            ],
            'details' => [
                'number_of_guests' => (int)$booking['number_of_guests'],
                'special_requests' => $booking['special_requests']
            ],
            'pricing' => [
                'total_amount' => (float)$booking['total_amount'],
                'currency' => getSetting('currency_symbol', 'MWK'),
                'currency_code' => getSetting('currency_code', 'MWK')
            ],
            'timestamps' => [
                'created_at' => $booking['created_at'],
                'updated_at' => $booking['updated_at']
            ]
        ],
        'actions' => [
            'can_cancel' => in_array($booking['status'], ['pending', 'confirmed']),
            'can_check_in' => $booking['status'] === 'confirmed',
            'can_check_out' => $booking['status'] === 'checked-in',
            'cancellation_policy' => 'Cancellations up to 48 hours before arrival are free of charge.'
        ],
        'contact' => [
            'hotel_name' => getSetting('site_name', 'Liwonde Sun Hotel'),
            'phone' => getSetting('phone_main', '+265 123 456 789'),
            'email' => getSetting('email_reservations', 'book@liwondesunhotel.com'),
            'address' => getSetting('address_line1', 'Liwonde National Park Road') . ', ' . 
                         getSetting('address_country', 'Malawi')
        ]
    ];
    
    // Add notes if available
    $notesStmt = $pdo->prepare("
        SELECT note_text, created_at 
        FROM booking_notes 
        WHERE booking_id = ? 
        ORDER BY created_at DESC
    ");
    $notesStmt->execute([$booking['id']]);
    $notes = $notesStmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($notes) {
        $response['booking']['notes'] = $notes;
    }
    
    ApiResponse::success($response, 'Booking retrieved successfully');
    
} catch (PDOException $e) {
    error_log("Booking Details API Error: " . $e->getMessage());
    ApiResponse::error('Failed to retrieve booking details', 500);
}