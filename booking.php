<?php
/**
 * Room Booking Page with Enhanced Security
 * Features:
 * - CSRF protection
 * - Secure session management
 * - Input validation
 */

// Load security configuration first
require_once 'config/security.php';

require_once 'config/database.php';
require_once 'config/email.php';
require_once 'includes/validation.php';

// Send security headers
sendSecurityHeaders();

// Handle booking submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    requireCsrfValidation();
    
    try {
        // Initialize validation errors array
        $validation_errors = [];
        $sanitized_data = [];
        
        // Validate room_id
        $room_validation = validateRoomId($_POST['room_id'] ?? '');
        if (!$room_validation['valid']) {
            $validation_errors['room_id'] = $room_validation['error'];
        } else {
            $sanitized_data['room_id'] = $room_validation['room']['id'];
        }
        
        // Validate guest_name
        $name_validation = validateName($_POST['guest_name'] ?? '', 2, true);
        if (!$name_validation['valid']) {
            $validation_errors['guest_name'] = $name_validation['error'];
        } else {
            $sanitized_data['guest_name'] = sanitizeString($name_validation['value'], 100);
        }
        
        // Validate guest_email
        $guest_email_value = $_POST['guest_email'] ?? '';
        
        if (empty($guest_email_value)) {
            $validation_errors['guest_email'] = 'Guest email is required';
        } else {
            $guest_email_value = trim($guest_email_value);
            
            if (!filter_var($guest_email_value, FILTER_VALIDATE_EMAIL)) {
                $validation_errors['guest_email'] = 'Please enter a valid email address';
            } else {
                $sanitized_data['guest_email'] = sanitizeString($guest_email_value, 254);
            }
        }
        
        // Validate guest_phone
        $phone_validation = validatePhone($_POST['guest_phone'] ?? '');
        if (!$phone_validation['valid']) {
            $validation_errors['guest_phone'] = $phone_validation['error'];
        } else {
            $sanitized_data['guest_phone'] = $phone_validation['sanitized'];
        }
        
        // Validate guest_country (optional)
        $country_validation = validateText($_POST['guest_country'] ?? '', 0, 100, false);
        if (!$country_validation['valid']) {
            $validation_errors['guest_country'] = $country_validation['error'];
        } else {
            $sanitized_data['guest_country'] = sanitizeString($country_validation['value'], 100);
        }
        
        // Validate guest_address (optional)
        $address_validation = validateText($_POST['guest_address'] ?? '', 0, 500, false);
        if (!$address_validation['valid']) {
            $validation_errors['guest_address'] = $address_validation['error'];
        } else {
            $sanitized_data['guest_address'] = sanitizeString($address_validation['value'], 500);
        }
        
        // Validate number_of_guests
        $guests_validation = validateNumber($_POST['number_of_guests'] ?? '', 1, 20, true);
        if (!$guests_validation['valid']) {
            $validation_errors['number_of_guests'] = $guests_validation['error'];
        } else {
            $sanitized_data['number_of_guests'] = $guests_validation['value'];
        }
        
        // Validate check_in_date
        $check_in_validation = validateDate($_POST['check_in_date'] ?? '', false, true);
        if (!$check_in_validation['valid']) {
            $validation_errors['check_in_date'] = $check_in_validation['error'];
        } else {
            $sanitized_data['check_in_date'] = $check_in_validation['date']->format('Y-m-d');
        }
        
        // Validate check_out_date
        $check_out_validation = validateDate($_POST['check_out_date'] ?? '', false, true);
        if (!$check_out_validation['valid']) {
            $validation_errors['check_out_date'] = $check_out_validation['error'];
        } else {
            $sanitized_data['check_out_date'] = $check_out_validation['date']->format('Y-m-d');
        }
        
        // Validate date range
        if (empty($validation_errors['check_in_date']) && empty($validation_errors['check_out_date'])) {
            $date_range_validation = validateDateRange($sanitized_data['check_in_date'], $sanitized_data['check_out_date'], 30);
            if (!$date_range_validation['valid']) {
                $validation_errors['dates'] = $date_range_validation['error'];
            }
        }
        
        // Validate special_requests (optional)
        $requests_validation = validateText($_POST['special_requests'] ?? '', 0, 1000, false);
        if (!$requests_validation['valid']) {
            $validation_errors['special_requests'] = $requests_validation['error'];
        } else {
            $sanitized_data['special_requests'] = sanitizeString($requests_validation['value'], 1000);
        }
        
        // Check for validation errors
        if (!empty($validation_errors)) {
            $error_messages = [];
            foreach ($validation_errors as $field => $message) {
                $error_messages[] = ucfirst(str_replace('_', ' ', $field)) . ': ' . $message;
            }
            throw new Exception(implode('; ', $error_messages));
        }
        
        // Use enhanced validation with availability check
        $validation_result = validateBookingWithAvailability($sanitized_data);

        if (!$validation_result['valid']) {
            // Handle validation errors
            if ($validation_result['type'] === 'availability') {
                // Room availability issue - provide detailed conflict info
                $conflict_message = $validation_result['errors']['availability'];
                if (!empty($validation_result['conflicts'])) {
                    $conflict_message .= ' ' . $validation_result['errors']['conflicts'];
                }
                throw new Exception($conflict_message);
            } elseif ($validation_result['type'] === 'capacity') {
                // Room capacity issue
                throw new Exception($validation_result['errors']['number_of_guests']);
            } else {
                // General validation errors
                $error_messages = [];
                foreach ($validation_result['errors'] as $field => $message) {
                    $error_messages[] = "$field: $message";
                }
                throw new Exception(implode('; ', $error_messages));
            }
        }

        // All validations passed - proceed with booking
        $room_id = $sanitized_data['room_id'];
        $guest_name = $sanitized_data['guest_name'];
        $guest_email = $sanitized_data['guest_email'];
        $guest_phone = $sanitized_data['guest_phone'];
        $guest_country = $sanitized_data['guest_country'];
        $guest_address = $sanitized_data['guest_address'];
        $number_of_guests = $sanitized_data['number_of_guests'];
        $check_in_date = $sanitized_data['check_in_date'];
        $check_out_date = $sanitized_data['check_out_date'];
        $special_requests = $sanitized_data['special_requests'];

        // Get booking type (standard or tentative)
        $is_tentative_booking = isset($_POST['booking_type']) && $_POST['booking_type'] === 'tentative';
        
        // Get room details for pricing
        $room = $validation_result['availability']['room'];
        $number_of_nights = $validation_result['availability']['nights'];
        $total_amount = $room['price_per_night'] * $number_of_nights;

        // Generate unique booking reference (guaranteed unique)
        do {
            $booking_reference = 'LSH' . date('Y') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            $ref_check = $pdo->prepare("SELECT COUNT(*) as count FROM bookings WHERE booking_reference = ?");
            $ref_check->execute([$booking_reference]);
            $ref_exists = $ref_check->fetch(PDO::FETCH_ASSOC)['count'] > 0;
        } while ($ref_exists);

        // Determine status and tentative expiration
        $booking_status = $is_tentative_booking ? 'tentative' : 'pending';
        $is_tentative = $is_tentative_booking ? 1 : 0;
        $tentative_expires_at = null;
        
        if ($is_tentative_booking) {
            // Get tentative duration from settings (default 48 hours)
            $tentative_duration_hours = (int)getSetting('tentative_duration_hours', 48);
            $tentative_expires_at = date('Y-m-d H:i:s', strtotime("+{$tentative_duration_hours} hours"));
        }

        // Insert booking with transaction for data integrity
        $pdo->beginTransaction(); // Start transaction
        
        try {
            $insert_stmt = $pdo->prepare("
                INSERT INTO bookings (
                    booking_reference, room_id, guest_name, guest_email, guest_phone,
                    guest_country, guest_address, number_of_guests, check_in_date,
                    check_out_date, number_of_nights, total_amount, special_requests, status,
                    is_tentative, tentative_expires_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $insert_stmt->execute([
                $booking_reference, $room_id, $guest_name, $guest_email, $guest_phone,
                $guest_country, $guest_address, $number_of_guests, $check_in_date,
                $check_out_date, $number_of_nights, $total_amount, $special_requests,
                $booking_status, $is_tentative, $tentative_expires_at
            ]);

            // Commit transaction - booking secured with foreign key constraints!
            $pdo->commit();

            // Send email notifications using working email system
            $booking_data = [
                'id' => $pdo->lastInsertId(),
                'booking_reference' => $booking_reference,
                'room_id' => $room_id,
                'guest_name' => $guest_name,
                'guest_email' => $guest_email,
                'guest_phone' => $guest_phone,
                'check_in_date' => $check_in_date,
                'check_out_date' => $check_out_date,
                'number_of_nights' => $number_of_nights,
                'number_of_guests' => $number_of_guests,
                'total_amount' => $total_amount,
                'special_requests' => $special_requests,
                'status' => $booking_status,
                'is_tentative' => $is_tentative,
                'tentative_expires_at' => $tentative_expires_at
            ];
            
            // Send appropriate email based on booking type
            if ($is_tentative_booking) {
                // Send tentative booking confirmation email
                $email_result = sendTentativeBookingConfirmedEmail($booking_data);
                $log_type = "Tentative booking confirmed";
            } else {
                // Send standard booking received email
                $email_result = sendBookingReceivedEmail($booking_data);
                $log_type = "Booking received";
            }
            
            // Log email result for debugging
            if (!$email_result['success']) {
                error_log("Failed to send {$log_type} email: " . $email_result['message']);
            } else {
                // Log success with preview URL if available
                $logMsg = "{$log_type} email processed (PHPMailer)";
                if (isset($email_result['preview_url'])) {
                    $logMsg .= " - Preview: " . $email_result['preview_url'];
                }
                error_log($logMsg);
            }
            
            // Send notification to admin (simplified PHPMailer)
            $admin_result = sendAdminNotificationEmail($booking_data);
            
            if (!$admin_result['success']) {
                error_log("Failed to send admin notification: " . $admin_result['message']);
            } else {
                // Log success with preview URL if available
                $logMsg = "Admin notification processed (PHPMailer)";
                if (isset($admin_result['preview_url'])) {
                    $logMsg .= " - Preview: " . $admin_result['preview_url'];
                }
                error_log($logMsg);
            }

            // Success - redirect to confirmation
            $_SESSION['booking_success'] = [
                'reference' => $booking_reference,
                'guest_name' => $guest_name,
                'room_name' => $room['name'],
                'check_in' => $check_in_date,
                'check_out' => $check_out_date,
                'nights' => $number_of_nights,
                'total' => $total_amount,
                'email_sent' => $email_result['success'],
                'is_tentative' => $is_tentative,
                'tentative_expires_at' => $tentative_expires_at
            ];

            header('Location: booking-confirmation.php?ref=' . $booking_reference);
            exit;
            
        } catch (Exception $e) {
            // Rollback on insert error
            $pdo->rollBack();
            throw $e;
        }

    } catch (Exception $e) {
        // Rollback transaction on any error
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $error_message = $e->getMessage();
    }
}

// Get pre-selected room from URL
$preselected_room_id = isset($_GET['room_id']) ? (int)$_GET['room_id'] : null;
$preselected_room = null;

// Fetch available rooms for booking form with all details needed for validation
$rooms_stmt = $pdo->query("SELECT id, name, price_per_night, max_guests, rooms_available, total_rooms, short_description, image_url FROM rooms WHERE is_active = 1 ORDER BY display_order ASC");
$available_rooms = $rooms_stmt->fetchAll(PDO::FETCH_ASSOC);

// Build rooms data for JavaScript
$rooms_data = [];
foreach ($available_rooms as $room) {
    $rooms_data[] = [
        'id' => (int)$room['id'],
        'name' => $room['name'],
        'max_guests' => (int)$room['max_guests'],
        'price_per_night' => (float)$room['price_per_night'],
        'rooms_available' => (int)$room['rooms_available'],
        'total_rooms' => (int)$room['total_rooms']
    ];
}

// Get pre-selected room details
if ($preselected_room_id) {
    foreach ($available_rooms as $room) {
        if ($room['id'] == $preselected_room_id) {
            $preselected_room = $room;
            break;
        }
    }
}

// Fetch site settings
$site_name = getSetting('site_name');
$site_logo = getSetting('site_logo');
$currency_symbol = getSetting('currency_symbol');
$phone_main = getSetting('phone_main');
$email_reservations = getSetting('email_reservations');
$email_reservations_esc = addslashes($email_reservations); // For JavaScript

// Get maximum advance booking days
$max_advance_days = (int)getSetting('max_advance_booking_days');
$max_advance_date = date('Y-m-d', strtotime("+{$max_advance_days} days"));

// Get blocked dates for the pre-selected room (or all rooms)
$blocked_dates = [];
if ($preselected_room_id) {
    $blocked_dates = getBlockedDates($preselected_room_id);
} else {
    $blocked_dates = getBlockedDates();
}

// Format blocked dates for JavaScript
$blocked_dates_array = [];
foreach ($blocked_dates as $bd) {
    $blocked_dates_array[] = $bd['block_date'];
}

// Get payment policy
$payment_policy = getSetting('payment_policy');

// Fetch policies for footer modals
$policies = [];
try {
    $policyStmt = $pdo->query("SELECT slug, title, summary, content FROM policies WHERE is_active = 1 ORDER BY display_order ASC, id ASC");
    $policies = $policyStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching policies: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes, viewport-fit=cover">
    <meta name="theme-color" content="#0A1929">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="format-detection" content="telephone=yes">
    <title>Book Your Stay | <?php echo htmlspecialchars($site_name); ?></title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/footer.css">
    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .booking-page {
            background: #f8f9fa;
            padding-top: 90px;
            min-height: 100vh;
        }
        .booking-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        .booking-header {
            text-align: center;
            margin-bottom: 40px;
        }
        .booking-header h1 {
            font-family: var(--font-serif);
            font-size: 36px;
            color: var(--navy);
            margin-bottom: 10px;
        }
        .booking-header p {
            color: #666;
            font-size: 16px;
        }
        .booking-form-card {
            background: white;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }
        .form-section {
            margin-bottom: 32px;
        }
        .form-section-title {
            font-family: var(--font-serif);
            font-size: 22px;
            color: var(--navy);
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid var(--gold);
        }
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-weight: 600;
            color: var(--navy);
            margin-bottom: 8px;
            font-size: 14px;
        }
        .form-group label.required::after {
            content: ' *';
            color: #dc3545;
        }
        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s ease;
            font-family: var(--font-sans);
        }
        .form-control:focus {
            outline: none;
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.1);
        }
        select.form-control {
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23333' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 16px center;
            padding-right: 40px;
        }
        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }
        .room-selection {
            display: grid;
            gap: 16px;
            margin-top: 16px;
        }
        .room-option {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 16px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .room-option:hover {
            border-color: var(--gold);
            background: rgba(212, 175, 55, 0.05);
        }
        .room-option input[type="radio"] {
            width: 20px;
            height: 20px;
            accent-color: var(--gold);
        }
        .room-option.selected {
            border-color: var(--gold);
            background: rgba(212, 175, 55, 0.1);
        }
        .room-info h4 {
            margin: 0 0 4px 0;
            color: var(--navy);
            font-size: 18px;
        }
        .room-info p {
            margin: 0;
            color: #666;
            font-size: 13px;
        }
        .room-price {
            margin-left: auto;
            text-align: right;
        }
        .room-price-amount {
            font-size: 20px;
            font-weight: 700;
            color: var(--gold);
        }
        .room-price-period {
            font-size: 12px;
            color: #666;
        }
        .alert {
            padding: 16px 20px;
            border-radius: 8px;
            margin-bottom: 24px;
        }
        .alert-danger {
            background: #fee;
            border-left: 4px solid #dc3545;
            color: #721c24;
        }
        .alert-success {
            background: #d4edda;
            border-left: 4px solid #28a745;
            color: #155724;
        }
        .booking-summary {
            background: linear-gradient(135deg, var(--deep-navy) 0%, var(--navy) 100%);
            color: white;
            padding: 24px;
            border-radius: 12px;
            margin-top: 32px;
        }
        .booking-summary h3 {
            margin: 0 0 16px 0;
            color: var(--gold);
            font-size: 20px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .summary-row:last-child {
            border-bottom: none;
            font-size: 20px;
            font-weight: 700;
            color: var(--gold);
            margin-top: 8px;
        }
        .btn-submit {
            width: 100%;
            padding: 16px 32px;
            background: linear-gradient(135deg, var(--gold) 0%, #c49b2e 100%);
            color: var(--deep-navy);
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 24px;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(212, 175, 55, 0.4);
        }
        
        .btn-submit:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
            background: #ccc;
        }

        .availability-message {
            margin: 20px 0;
            padding: 15px;
            border-radius: 8px;
            font-weight: 500;
            text-align: center;
            animation: slideIn 0.3s ease-out;
        }

        .availability-message.alert-success {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .availability-message.alert-danger {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .availability-message i {
            margin-right: 8px;
            font-size: 1.1em;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Calendar Styles */
        .calendar-wrapper {
            position: relative;
            margin-top: 10px;
        }
        
        .flatpickr-calendar {
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            border: none;
        }
        
        .flatpickr-day.selected {
            background: var(--gold) !important;
            border-color: var(--gold) !important;
            color: var(--deep-navy) !important;
        }
        
        .flatpickr-day.inRange {
            background: rgba(212, 175, 55, 0.2) !important;
            border-color: rgba(212, 175, 55, 0.3) !important;
        }
        
        .flatpickr-day.startRange,
        .flatpickr-day.endRange {
            background: var(--gold) !important;
            color: var(--deep-navy) !important;
        }
        
        .flatpickr-day.blocked-date {
            background: #dc3545 !important;
            color: white !important;
            cursor: not-allowed;
            opacity: 0.7;
            border-color: #dc3545 !important;
            position: relative;
        }
        
        .flatpickr-day.blocked-date:hover {
            background: #c82333 !important;
        }
        
        .flatpickr-day.blocked-date .blocked-indicator {
            position: absolute;
            top: 2px;
            right: 2px;
            width: 6px;
            height: 6px;
            background: white;
            border-radius: 50%;
        }
        
        .flatpickr-day.unavailable-date {
            background: #6c757d !important;
            color: white !important;
            cursor: not-allowed;
            opacity: 0.5;
        }
        
        .flatpickr-day.available-date {
            background: #28a745 !important;
            color: white !important;
        }
        
        .flatpickr-day.available-date:hover {
            background: #218838 !important;
        }
        
        .flatpickr-day.disabled {
            background: #fee !important;
            color: #dc3545 !important;
            border-color: #f8d7da !important;
        }
        
        .flatpickr-day.disabled:not(.blocked-date) {
            background: #f8f9fa !important;
            color: #6c757d !important;
            cursor: not-allowed;
        }
        
        .calendar-legend {
            display: flex;
            gap: 15px;
            margin-top: 15px;
            padding: 12px;
            background: #f8f9fa;
            border-radius: 8px;
            font-size: 13px;
        }
        
        .legend-item {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .legend-color {
            width: 16px;
            height: 16px;
            border-radius: 4px;
        }
        
        .legend-color.available {
            background: #28a745;
        }
        
        .legend-color.blocked {
            background: #dc3545;
        }
        
        .legend-color.unavailable {
            background: #6c757d;
        }
        
        .legend-color.selected {
            background: var(--gold);
        }
        
        @media (max-width: 768px) {
            .booking-page {
                padding-top: 70px;
            }
            .booking-form-card {
                padding: 24px 20px;
            }
            .booking-header h1 {
                font-size: 28px;
            }
            .form-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Booking Type Selection Styles */
        .booking-type-selection {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 16px;
        }

        .booking-type-option {
            position: relative;
            cursor: pointer;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            padding: 20px;
            transition: all 0.3s ease;
            background: white;
        }

        .booking-type-option:hover {
            border-color: var(--gold);
            background: rgba(212, 175, 55, 0.05);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .booking-type-option.selected {
            border-color: var(--gold);
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.1) 0%, rgba(212, 175, 55, 0.05) 100%);
            box-shadow: 0 4px 16px rgba(212, 175, 55, 0.3);
        }

        .booking-type-option input[type="radio"] {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .booking-type-content {
            position: relative;
        }

        .booking-type-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
        }

        .booking-type-header i {
            font-size: 24px;
            color: var(--gold);
        }

        .booking-type-header h4 {
            margin: 0;
            color: var(--navy);
            font-size: 18px;
            font-weight: 600;
        }

        .booking-type-description {
            margin: 0 0 12px 0;
            color: #666;
            font-size: 14px;
            line-height: 1.6;
        }

        .booking-type-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .booking-type-badge.recommended {
            background: linear-gradient(135deg, var(--gold) 0%, #c49b2e 100%);
            color: var(--deep-navy);
        }

        .booking-type-badge.info {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            color: #1565c0;
        }

        .booking-type-option.selected .booking-type-badge.recommended {
            background: linear-gradient(135deg, #c49b2e 0%, #b88a1f 100%);
        }

        @media (max-width: 768px) {
            .booking-type-selection {
                grid-template-columns: 1fr;
            }
            
            /* Mobile responsive styles */
            .booking-page {
                padding-top: 70px;
            }
            
            .booking-container {
                padding: 20px 15px;
            }
            
            .booking-form-card {
                padding: 20px;
                border-radius: 12px;
            }
            
            .booking-header h1 {
                font-size: 24px;
            }
            
            .booking-header p {
                font-size: 14px;
            }
            
            .form-section-title {
                font-size: 18px;
                margin-bottom: 16px;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }
            
            .form-group {
                margin-bottom: 16px;
            }
            
            .form-control {
                padding: 10px 14px;
                font-size: 14px;
            }
            
            .room-option {
                flex-direction: column;
                align-items: flex-start;
                padding: 12px;
            }
            
            .room-option input[type="radio"] {
                width: 18px;
                height: 18px;
            }
            
            .room-info h4 {
                font-size: 16px;
            }
            
            .room-info p {
                font-size: 12px;
            }
            
            .room-price {
                margin-left: 0;
                margin-top: 8px;
                text-align: left;
            }
            
            .room-price-amount {
                font-size: 18px;
            }
            
            .booking-summary {
                padding: 16px;
                margin-top: 24px;
            }
            
            .booking-summary h3 {
                font-size: 18px;
            }
            
            .summary-row {
                font-size: 14px;
                padding: 10px 0;
            }
            
            .summary-row:last-child {
                font-size: 18px;
            }
            
            .btn-submit {
                padding: 14px 24px;
                font-size: 15px;
            }
            
            .alert {
                padding: 12px 16px;
                font-size: 14px;
            }
        }
        
        @media (max-width: 480px) {
            .booking-container {
                padding: 15px 10px;
            }
            
            .booking-form-card {
                padding: 16px;
            }
            
            .booking-header h1 {
                font-size: 20px;
            }
            
            .form-section-title {
                font-size: 16px;
            }
            
            .room-info h4 {
                font-size: 15px;
            }
            
            .room-price-amount {
                font-size: 16px;
            }
        }
    </style>
</head>
<body class="booking-page">
    <?php include 'includes/loader.php'; ?>
    
    <?php include 'includes/header.php'; ?>
    <?php include 'includes/alert.php'; ?>
    
    <div class="booking-container">
        <div class="booking-header">
            <h1>Book Your Stay</h1>
            <p>Complete the form below to reserve your room. Our team will confirm your booking shortly.</p>
        </div>

        <?php if (isset($error_message)): ?>
            <?php showAlert($error_message, 'error'); ?>
        <?php endif; ?>

        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . (isset($_GET['room_id']) ? '?room_id=' . (int)$_GET['room_id'] : '')); ?>" class="booking-form-card" id="bookingForm">
            <?php echo getCsrfField(); ?>
            <!-- Room Selection (hidden if pre-selected) -->
            <?php if (!$preselected_room): ?>
            <div class="form-section">
                <h3 class="form-section-title"><i class="fas fa-bed"></i> Select Your Room</h3>
                <div class="room-selection">
                    <?php foreach ($available_rooms as $room): ?>
                    <label class="room-option" onclick="selectRoom(this)" data-room-id="<?php echo $room['id']; ?>" data-room-name="<?php echo htmlspecialchars($room['name']); ?>" data-room-price="<?php echo $room['price_per_night']; ?>" data-max-guests="<?php echo $room['max_guests']; ?>" data-rooms-available="<?php echo $room['rooms_available']; ?>">
                        <input type="radio" name="room_id" value="<?php echo $room['id']; ?>" required>
                        <div class="room-info">
                            <h4><?php echo htmlspecialchars($room['name']); ?></h4>
                            <p><?php echo htmlspecialchars($room['short_description']); ?></p>
                            <p><i class="fas fa-users"></i> Max <?php echo $room['max_guests']; ?> guests <?php echo $room['rooms_available'] > 1 ? "({$room['rooms_available']} rooms available)" : ''; ?></p>
                        </div>
                        <div class="room-price">
                            <div class="room-price-amount"><?php echo $currency_symbol; ?><?php echo number_format($room['price_per_night'], 0); ?></div>
                            <div class="room-price-period">per night</div>
                        </div>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Booking Type Selection -->
            <div class="form-section">
                <h3 class="form-section-title"><i class="fas fa-clipboard-list"></i> Booking Type</h3>
                <div class="booking-type-selection">
                    <label class="booking-type-option" onclick="selectBookingType('standard')">
                        <input type="radio" name="booking_type" value="standard" checked>
                        <div class="booking-type-content">
                            <div class="booking-type-header">
                                <i class="fas fa-check-circle"></i>
                                <h4>Standard Booking</h4>
                            </div>
                            <p class="booking-type-description">
                                Confirm your booking immediately. Our team will review and confirm your reservation within 24 hours.
                                Payment details will be provided upon confirmation.
                            </p>
                            <div class="booking-type-badge recommended">
                                <i class="fas fa-star"></i> Recommended
                            </div>
                        </div>
                    </label>
                    
                    <label class="booking-type-option" onclick="selectBookingType('tentative')">
                        <input type="radio" name="booking_type" value="tentative">
                        <div class="booking-type-content">
                            <div class="booking-type-header">
                                <i class="fas fa-clock"></i>
                                <h4>Tentative Booking</h4>
                            </div>
                            <p class="booking-type-description">
                                Place this room on temporary hold for <?php echo (int)getSetting('tentative_duration_hours', 48); ?> hours without immediate confirmation.
                                Perfect when you need time to finalize travel plans. You'll receive a reminder before expiration.
                            </p>
                            <div class="booking-type-badge info">
                                <i class="fas fa-info-circle"></i> No payment required yet
                            </div>
                        </div>
                    </label>
                </div>
                <p style="margin-top: 15px; color: #666; font-size: 13px; text-align: center;">
                    <i class="fas fa-lightbulb" style="color: var(--gold);"></i>
                    <strong>Tentative bookings</strong> can be converted to standard bookings anytime before expiration.
                    After expiration, the room hold will be released automatically.
                </p>
            </div>

            <!-- Pre-selected Room Info (shown if room is pre-selected) -->
            <?php if ($preselected_room): ?>
            <div class="form-section">
                <h3 class="form-section-title"><i class="fas fa-bed"></i> Selected Room</h3>
                <div class="room-selection">
                    <div class="room-option selected" style="border-color: var(--gold); background: rgba(212, 175, 55, 0.1);">
                        <input type="hidden" name="room_id" value="<?php echo $preselected_room['id']; ?>" id="preselectedRoomId">
                        <div class="room-info">
                            <h4><?php echo htmlspecialchars($preselected_room['name']); ?></h4>
                            <p><?php echo htmlspecialchars($preselected_room['short_description']); ?></p>
                            <p><i class="fas fa-users"></i> Max <?php echo $preselected_room['max_guests']; ?> guests <?php echo $preselected_room['rooms_available'] > 1 ? "({$preselected_room['rooms_available']} rooms available)" : ''; ?></p>
                        </div>
                        <div class="room-price">
                            <div class="room-price-amount"><?php echo $currency_symbol; ?><?php echo number_format($preselected_room['price_per_night'], 0); ?></div>
                            <div class="room-price-period">per night</div>
                        </div>
                    </div>
                </div>
                <p style="margin-top: 10px; color: #666; font-size: 14px;">
                    <a href="booking.php" style="color: var(--gold); text-decoration: none;">
                        <i class="fas fa-arrow-left"></i> Choose a different room
                    </a>
                </p>
            </div>
            <?php endif; ?>

            <!-- Guest Information -->
            <div class="form-section">
                <h3 class="form-section-title"><i class="fas fa-user"></i> Guest Information</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="guest_name" class="required">Full Name</label>
                        <input type="text" id="guest_name" name="guest_name" class="form-control" required value="<?php echo isset($_POST['guest_name']) ? htmlspecialchars($_POST['guest_name']) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label for="guest_email" class="required">Email Address</label>
                        <input type="email" id="guest_email" name="guest_email" class="form-control" required value="<?php echo isset($_POST['guest_email']) ? htmlspecialchars($_POST['guest_email']) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label for="guest_phone" class="required">Phone Number</label>
                        <input type="tel" id="guest_phone" name="guest_phone" class="form-control" required value="<?php echo isset($_POST['guest_phone']) ? htmlspecialchars($_POST['guest_phone']) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label for="guest_country">Country</label>
                        <input type="text" id="guest_country" name="guest_country" class="form-control" value="<?php echo isset($_POST['guest_country']) ? htmlspecialchars($_POST['guest_country']) : ''; ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="guest_address">Address</label>
                    <textarea id="guest_address" name="guest_address" class="form-control" rows="2"><?php echo isset($_POST['guest_address']) ? htmlspecialchars($_POST['guest_address']) : ''; ?></textarea>
                </div>
            </div>

            <!-- Booking Details -->
            <div class="form-section">
                <h3 class="form-section-title"><i class="fas fa-calendar-alt"></i> Booking Details</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="check_in_date" class="required">Check-in Date</label>
                        <div class="calendar-wrapper">
                            <input type="text" id="check_in_date" name="check_in_date" class="form-control" required
                                   placeholder="Select check-in date" readonly>
                        </div>
                        <small style="color: #666; font-size: 12px; margin-top: 5px; display: block;">
                            <i class="fas fa-info-circle"></i> Bookings can only be made up to <?php echo $max_advance_days; ?> days in advance
                        </small>
                    </div>
                    <div class="form-group">
                        <label for="check_out_date" class="required">Check-out Date</label>
                        <div class="calendar-wrapper">
                            <input type="text" id="check_out_date" name="check_out_date" class="form-control" required
                                   placeholder="Select check-out date" readonly>
                        </div>
                    </div>
                </div>
                
                <!-- Calendar Legend -->
                <div class="calendar-legend">
                    <div class="legend-item">
                        <div class="legend-color available"></div>
                        <span>Available</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color blocked"></div>
                        <span>Blocked</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color unavailable"></div>
                        <span>Unavailable</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color selected"></div>
                        <span>Selected</span>
                    </div>
                    <div class="form-group">
                        <label for="number_of_guests" class="required">Number of Guests</label>
                        <select id="number_of_guests" name="number_of_guests" class="form-control" required>
                            <option value="">Select room first...</option>
                        </select>
                        <small id="guestCapacityHint" style="color: #666; font-size: 12px; margin-top: 5px; display: none;"></small>
                    </div>
                    
                    <!-- Second Room Suggestion (hidden by default) -->
                    <div id="secondRoomSuggestion" style="display: none; margin-top: 15px; padding: 15px; background: linear-gradient(135deg, #fff8e1 0%, #ffecb3 100%); border-left: 4px solid var(--gold); border-radius: 8px;">
                        <div style="display: flex; align-items: start; gap: 12px;">
                            <i class="fas fa-info-circle" style="color: var(--gold); font-size: 20px; margin-top: 2px;"></i>
                            <div>
                                <h4 style="margin: 0 0 8px 0; color: var(--navy); font-size: 16px;">Consider Booking Multiple Rooms</h4>
                                <p style="margin: 0 0 10px 0; color: #666; font-size: 14px;">Your group size exceeds the maximum capacity for one room. You can book multiple rooms to accommodate all guests.</p>
                                <div id="secondRoomOptions" style="margin-top: 10px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="special_requests">Special Requests (Optional)</label>
                    <textarea id="special_requests" name="special_requests" class="form-control" rows="3" placeholder="E.g., early check-in, airport pickup, dietary requirements..."><?php echo isset($_POST['special_requests']) ? htmlspecialchars($_POST['special_requests']) : ''; ?></textarea>
                </div>
            </div>

            <!-- Booking Summary -->
            <div class="booking-summary" id="bookingSummary" style="display: none;">
                <h3>Booking Summary</h3>
                <div class="summary-row">
                    <span>Room:</span>
                    <span id="summaryRoom">-</span>
                </div>
                <div class="summary-row">
                    <span>Check-in:</span>
                    <span id="summaryCheckIn">-</span>
                </div>
                <div class="summary-row">
                    <span>Check-out:</span>
                    <span id="summaryCheckOut">-</span>
                </div>
                <div class="summary-row">
                    <span>Number of Nights:</span>
                    <span id="summaryNights">-</span>
                </div>
                <div class="summary-row">
                    <span>Total Amount:</span>
                    <span id="summaryTotal">-</span>
                </div>
            </div>

            <button type="submit" class="btn-submit">
                <i class="fas fa-check-circle"></i> Confirm Booking
            </button>

            <p style="text-align: center; margin-top: 20px; color: #666; font-size: 13px;">
                <i class="fas fa-info-circle"></i> <?php echo htmlspecialchars($payment_policy); ?>
            </p>
        </form>
    </div>

    <?php include 'includes/footer.php'; ?>
    
    <script src="js/modal.js"></script>
    <script src="js/main.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        // Site settings
        const emailReservations = '<?php echo $email_reservations_esc; ?>';
        const currencySymbol = '<?php echo htmlspecialchars($currency_symbol); ?>';
        
        // Blocked dates from server
        const blockedDates = <?php echo json_encode($blocked_dates_array); ?>;
        const preselectedRoomId = <?php echo $preselected_room_id ? $preselected_room_id : 'null'; ?>;
        const preselectedRoomPrice = <?php echo $preselected_room ? $preselected_room['price_per_night'] : 'null'; ?>;
        const preselectedRoomName = <?php echo $preselected_room ? '"' . addslashes($preselected_room['name']) . '"' : 'null'; ?>;
        const preselectedRoomMaxGuests = <?php echo $preselected_room ? $preselected_room['max_guests'] : 'null'; ?>;
        
        // Rooms data for dynamic validation
        const roomsData = <?php echo json_encode($rooms_data); ?>;
        
        let checkInCalendar = null;
        let checkOutCalendar = null;
        let selectedRoomId = preselectedRoomId;
        let selectedRoomPrice = preselectedRoomPrice;
        let selectedRoomName = preselectedRoomName;
        let selectedRoomMaxGuests = preselectedRoomMaxGuests;
        
        // Initialize calendars
        function initCalendars() {
            const today = new Date();
            const maxDate = new Date();
            maxDate.setDate(maxDate.getDate() + <?php echo $max_advance_days; ?>);
            
            // Check-in calendar
            checkInCalendar = flatpickr('#check_in_date', {
                minDate: 'today',
                maxDate: maxDate,
                dateFormat: 'Y-m-d',
                disable: blockedDates,
                onDayCreate: function(dObj, dStr, fp, dayElem) {
                    // Add custom class for blocked dates
                    const dateStr = fp.formatDate(dayElem.dateObj, 'Y-m-d');
                    if (blockedDates.includes(dateStr)) {
                        dayElem.classList.add('blocked-date');
                        dayElem.innerHTML += '<span class="blocked-indicator"></span>';
                    }
                },
                onChange: function(selectedDates, dateStr, instance) {
                    if (selectedDates.length > 0) {
                        // Update check-out calendar min date
                        const nextDay = new Date(selectedDates[0]);
                        nextDay.setDate(nextDay.getDate() + 1);
                        
                        if (checkOutCalendar) {
                            checkOutCalendar.set('minDate', nextDay);
                            
                            // If check-out is before new min date, clear it
                            const currentCheckOut = checkOutCalendar.selectedDates[0];
                            if (currentCheckOut && currentCheckOut < nextDay) {
                                checkOutCalendar.clear();
                            }
                        }
                    }
                    updateSummary();
                }
            });
            
            // Check-out calendar
            checkOutCalendar = flatpickr('#check_out_date', {
                minDate: 'today',
                maxDate: maxDate,
                dateFormat: 'Y-m-d',
                disable: blockedDates,
                onDayCreate: function(dObj, dStr, fp, dayElem) {
                    // Add custom class for blocked dates
                    const dateStr = fp.formatDate(dayElem.dateObj, 'Y-m-d');
                    if (blockedDates.includes(dateStr)) {
                        dayElem.classList.add('blocked-date');
                        dayElem.innerHTML += '<span class="blocked-indicator"></span>';
                    }
                },
                onChange: function() {
                    updateSummary();
                }
            });
        }
        
        // Initialize calendars on page load
        document.addEventListener('DOMContentLoaded', function() {
            initCalendars();
            
            // If room is pre-selected, initialize with that room
            if (preselectedRoomId) {
                selectedRoomId = preselectedRoomId;
                selectedRoomPrice = preselectedRoomPrice;
                selectedRoomName = preselectedRoomName;
                selectedRoomMaxGuests = preselectedRoomMaxGuests;
                updateGuestOptions(preselectedRoomMaxGuests);
            }
        });
        
        function updateSummaryWithDates(selection) {
            const roomRadio = document.querySelector('input[name="room_id"]:checked');
            if (!roomRadio) return;
            
            const roomOption = roomRadio.closest('.room-option');
            const roomName = roomOption.querySelector('h4').textContent;
            const roomPrice = parseFloat(roomOption.querySelector('.room-price-amount').textContent.replace(/[^0-9.]/g, ''));
            
            const checkInDate = new Date(selection.checkIn);
            const checkOutDate = new Date(selection.checkOut);
            
            document.getElementById('summaryRoom').textContent = roomName;
            document.getElementById('summaryCheckIn').textContent = checkInDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
            document.getElementById('summaryCheckOut').textContent = checkOutDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
            document.getElementById('summaryNights').textContent = selection.nights + (selection.nights === 1 ? ' night' : ' nights');
            document.getElementById('summaryTotal').textContent = currencySymbol + (roomPrice * selection.nights).toLocaleString();
            document.getElementById('bookingSummary').style.display = 'block';
            
            // Enable submit button
            const submitBtn = document.querySelector('.btn-submit');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-check-circle"></i> Confirm Booking';
            submitBtn.style.opacity = '1';
        }
        
        function selectRoom(label) {
            document.querySelectorAll('.room-option').forEach(opt => opt.classList.remove('selected'));
            label.classList.add('selected');
            
            const roomRadio = label.querySelector('input[type="radio"]');
            const roomId = parseInt(roomRadio.value);
            const roomName = label.getAttribute('data-room-name');
            const roomPrice = parseFloat(label.getAttribute('data-room-price'));
            
            selectedRoomId = roomId;
            selectedRoomName = roomName;
            selectedRoomPrice = roomPrice;
            selectedRoomMaxGuests = parseInt(label.getAttribute('data-max-guests'));
            
            // Update guest options based on room capacity
            updateGuestOptions(selectedRoomMaxGuests);
            
            // Reinitialize calendars with new room's blocked dates
            updateBlockedDatesForRoom(roomId);
        }
        
        // Update guest dropdown options based on room capacity
        function updateGuestOptions(maxGuests) {
            const guestSelect = document.getElementById('number_of_guests');
            const capacityHint = document.getElementById('guestCapacityHint');
            
            // Clear existing options
            guestSelect.innerHTML = '<option value="">Select number of guests...</option>';
            
            // Add options up to max guests
            for (let i = 1; i <= maxGuests; i++) {
                const option = document.createElement('option');
                option.value = i;
                option.textContent = i + (i === 1 ? ' Guest' : ' Guests');
                guestSelect.appendChild(option);
            }
            
            // Add option for more guests (will trigger second room suggestion)
            const moreOption = document.createElement('option');
            moreOption.value = maxGuests + 1;
            moreOption.textContent = (maxGuests + 1) + '+ Guests (Multiple Rooms)';
            moreOption.style.color = '#dc3545';
            moreOption.style.fontWeight = '600';
            guestSelect.appendChild(moreOption);
            
            // Update capacity hint
            capacityHint.textContent = `This room accommodates up to ${maxGuests} guest${maxGuests > 1 ? 's' : ''}.`;
            capacityHint.style.display = 'block';
            
            // Reset selection
            guestSelect.value = '';
            
            // Hide second room suggestion
            document.getElementById('secondRoomSuggestion').style.display = 'none';
        }
        
        // Check if guests exceed capacity and show second room suggestion
        function checkGuestCapacity() {
            const guestSelect = document.getElementById('number_of_guests');
            const numGuests = parseInt(guestSelect.value);
            const suggestionBox = document.getElementById('secondRoomSuggestion');
            const optionsContainer = document.getElementById('secondRoomOptions');
            
            if (!numGuests || !selectedRoomMaxGuests) {
                suggestionBox.style.display = 'none';
                return;
            }
            
            // Check if guests exceed room capacity
            if (numGuests > selectedRoomMaxGuests) {
                suggestionBox.style.display = 'block';
                
                // Calculate how many rooms needed
                const roomsNeeded = Math.ceil(numGuests / selectedRoomMaxGuests);
                
                // Build suggestion message
                let html = `
                    <div style="background: white; padding: 12px; border-radius: 6px; margin-top: 8px;">
                        <strong style="color: var(--navy);">Recommended: ${roomsNeeded} room${roomsNeeded > 1 ? 's' : ''}</strong>
                        <p style="margin: 5px 0 0 0; font-size: 13px; color: #666;">
                            Each ${selectedRoomName} can accommodate up to ${selectedRoomMaxGuests} guest${selectedRoomMaxGuests > 1 ? 's' : ''}.
                        </p>
                        <p style="margin: 8px 0 0 0; font-size: 13px; color: #666;">
                            <strong>Option 1:</strong> Complete this booking for up to ${selectedRoomMaxGuests} guests, then make another booking for the remaining ${numGuests - selectedRoomMaxGuests} guest${numGuests - selectedRoomMaxGuests > 1 ? 's' : ''}.
                        </p>
                        <p style="margin: 5px 0 0 0; font-size: 13px; color: #666;">
                            <strong>Option 2:</strong> Contact us directly at <a href="mailto:${emailReservations}" style="color: var(--gold);">${emailReservations}</a> for group booking assistance.
                        </p>
                    </div>
                `;
                
                optionsContainer.innerHTML = html;
                
                // Disable submit button
                const submitBtn = document.querySelector('.btn-submit');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Please Adjust Guest Count';
                submitBtn.style.opacity = '0.6';
            } else {
                suggestionBox.style.display = 'none';
                
                // Enable submit button if all validations pass
                validateFormForSubmit();
            }
        }
        
        // Validate form for submit
        function validateFormForSubmit() {
            const checkIn = document.getElementById('check_in_date').value;
            const checkOut = document.getElementById('check_out_date').value;
            const numGuests = document.getElementById('number_of_guests').value;
            const submitBtn = document.querySelector('.btn-submit');
            
            if (selectedRoomId && checkIn && checkOut && numGuests) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-check-circle"></i> Confirm Booking';
                submitBtn.style.opacity = '1';
            } else {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-calendar-check"></i> Complete All Fields';
                submitBtn.style.opacity = '0.6';
            }
        }
        
        function updateBlockedDatesForRoom(roomId) {
            // Fetch blocked dates for this room
            fetch('api/blocked-dates.php?room_id=' + roomId)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data) {
                        const roomBlockedDates = data.data.map(bd => bd.block_date);
                        
                        // Update both calendars
                        checkInCalendar.set('disable', roomBlockedDates);
                        checkOutCalendar.set('disable', roomBlockedDates);
                    }
                })
                .catch(error => {
                    console.error('Failed to fetch blocked dates:', error);
                });
        }
        
        function checkRoomAvailability(roomId, checkIn, checkOut, callback) {
            const url = `check-availability.php?room_id=${roomId}&check_in=${checkIn}&check_out=${checkOut}`;
            
            fetch(url)
                .then(response => response.json())
                .then(callback)
                .catch(error => {
                    console.error('Availability check failed:', error);
                    callback({ available: false, message: 'Unable to check availability' });
                });
        }
        
        function showAvailabilityMessage(message, isSuccess) {
            // Use new Alert component
            Alert.show(message, isSuccess ? 'success' : 'error', {
                timeout: 5000,
                position: 'top'
            });
        }
        
        function updateSummary() {
            const checkIn = document.getElementById('check_in_date').value;
            const checkOut = document.getElementById('check_out_date').value;

            if (selectedRoomId && checkIn && checkOut) {
                const checkInDate = new Date(checkIn);
                const checkOutDate = new Date(checkOut);
                const nights = Math.ceil((checkOutDate - checkInDate) / (1000 * 60 * 60 * 24));

                if (nights > 0) {
                    const total = selectedRoomPrice * nights;
                    
                    document.getElementById('summaryRoom').textContent = selectedRoomName;
                    document.getElementById('summaryCheckIn').textContent = checkInDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                    document.getElementById('summaryCheckOut').textContent = checkOutDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                    document.getElementById('summaryNights').textContent = nights + (nights === 1 ? ' night' : ' nights');
                    document.getElementById('summaryTotal').textContent = currencySymbol + total.toLocaleString();
                    
                    document.getElementById('bookingSummary').style.display = 'block';
                    
                    // Enable submit button
                    const submitBtn = document.querySelector('.btn-submit');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-check-circle"></i> Confirm Booking';
                    submitBtn.style.opacity = '1';
                } else {
                    document.getElementById('bookingSummary').style.display = 'none';
                }
            }
        }

        function checkRoomAvailability(roomId, checkIn, checkOut, callback) {
            const url = `check-availability.php?room_id=${roomId}&check_in=${checkIn}&check_out=${checkOut}`;
            
            fetch(url)
                .then(response => response.json())
                .then(callback)
                .catch(error => {
                    console.error('Availability check failed:', error);
                    callback({ available: false, message: 'Unable to check availability' });
                });
        }

        function showAvailabilityMessage(message, isSuccess) {
            // Use the new Alert component
            Alert.show(message, isSuccess ? 'success' : 'error', {
                timeout: 5000,
                position: 'top'
            });
        }

        document.getElementById('check_in_date').addEventListener('change', function() {
            const checkIn = new Date(this.value);
            const nextDay = new Date(checkIn);
            nextDay.setDate(checkIn.getDate() + 1);
            document.getElementById('check_out_date').min = nextDay.toISOString().split('T')[0];
            updateSummary();
            validateFormForSubmit();
        });

        document.getElementById('check_out_date').addEventListener('change', function() {
            updateSummary();
            validateFormForSubmit();
        });
        
        // Add guest count change listener
        document.getElementById('number_of_guests').addEventListener('change', checkGuestCapacity);
        
        // Booking type selection function
        function selectBookingType(type) {
            document.querySelectorAll('.booking-type-option').forEach(opt => {
                opt.classList.remove('selected');
            });
            
            const selectedOption = document.querySelector(`input[name="booking_type"][value="${type}"]`);
            if (selectedOption) {
                selectedOption.closest('.booking-type-option').classList.add('selected');
            }
        }
        
        // Initialize booking type selection on page load
        document.addEventListener('DOMContentLoaded', function() {
            selectBookingType('standard');
        });
    </script>

    <?php include 'includes/scroll-to-top.php'; ?>
</body>
</html>