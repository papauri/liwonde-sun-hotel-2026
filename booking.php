<?php
session_start();
require_once 'config/database.php';

// Handle booking submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Collect booking data
        $booking_data = [
            'room_id' => filter_var($_POST['room_id'], FILTER_VALIDATE_INT),
            'guest_name' => trim($_POST['guest_name']),
            'guest_email' => trim($_POST['guest_email']),
            'guest_phone' => trim($_POST['guest_phone']),
            'guest_country' => trim($_POST['guest_country'] ?? ''),
            'guest_address' => trim($_POST['guest_address'] ?? ''),
            'number_of_guests' => filter_var($_POST['number_of_guests'], FILTER_VALIDATE_INT),
            'check_in_date' => $_POST['check_in_date'],
            'check_out_date' => $_POST['check_out_date'],
            'special_requests' => trim($_POST['special_requests'] ?? '')
        ];

        // Use enhanced validation with availability check
        $validation_result = validateBookingWithAvailability($booking_data);

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
        $room_id = $booking_data['room_id'];
        $guest_name = $booking_data['guest_name'];
        $guest_email = $booking_data['guest_email'];
        $guest_phone = $booking_data['guest_phone'];
        $guest_country = $booking_data['guest_country'];
        $guest_address = $booking_data['guest_address'];
        $number_of_guests = $booking_data['number_of_guests'];
        $check_in_date = $booking_data['check_in_date'];
        $check_out_date = $booking_data['check_out_date'];
        $special_requests = $booking_data['special_requests'];

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

        // Insert booking with transaction for data integrity
        $pdo->beginTransaction(); // Start transaction
        
        try {
            $insert_stmt = $pdo->prepare("
                INSERT INTO bookings (
                    booking_reference, room_id, guest_name, guest_email, guest_phone, 
                    guest_country, guest_address, number_of_guests, check_in_date, 
                    check_out_date, number_of_nights, total_amount, special_requests, status
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
            ");

            $insert_stmt->execute([
                $booking_reference, $room_id, $guest_name, $guest_email, $guest_phone,
                $guest_country, $guest_address, $number_of_guests, $check_in_date,
                $check_out_date, $number_of_nights, $total_amount, $special_requests
            ]);

            // Commit transaction - booking secured with foreign key constraints!
            $pdo->commit();

            // Success - redirect to confirmation
            $_SESSION['booking_success'] = [
                'reference' => $booking_reference,
                'guest_name' => $guest_name,
                'room_name' => $room['name'],
                'check_in' => $check_in_date,
                'check_out' => $check_out_date,
                'nights' => $number_of_nights,
                'total' => $total_amount
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

// Fetch available rooms for booking form
$rooms_stmt = $pdo->query("SELECT id, name, price_per_night, max_guests, short_description, image_url FROM rooms WHERE is_active = 1 ORDER BY display_order ASC");
$available_rooms = $rooms_stmt->fetchAll(PDO::FETCH_ASSOC);

$site_name = getSetting('site_name');
$currency_symbol = getSetting('currency_symbol');
$phone_main = getSetting('phone_main');
$email_reservations = getSetting('email_reservations');
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
    <link rel="stylesheet" href="css/style.css">
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
    </style>
</head>
<body class="booking-page">
    <?php include 'includes/loader.php'; ?>
    
    <div class="booking-container">
        <div class="booking-header">
            <h1>Book Your Stay</h1>
            <p>Complete the form below to reserve your room. Our team will confirm your booking shortly.</p>
        </div>

        <?php if (isset($error_message)): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error_message); ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="booking.php" class="booking-form-card" id="bookingForm">
            <!-- Room Selection -->
            <div class="form-section">
                <h3 class="form-section-title"><i class="fas fa-bed"></i> Select Your Room</h3>
                <div class="room-selection">
                    <?php foreach ($available_rooms as $room): ?>
                    <label class="room-option" onclick="selectRoom(this)">
                        <input type="radio" name="room_id" value="<?php echo $room['id']; ?>" required>
                        <div class="room-info">
                            <h4><?php echo htmlspecialchars($room['name']); ?></h4>
                            <p><?php echo htmlspecialchars($room['short_description']); ?></p>
                            <p><i class="fas fa-users"></i> Max <?php echo $room['max_guests']; ?> guests</p>
                        </div>
                        <div class="room-price">
                            <div class="room-price-amount"><?php echo $currency_symbol; ?><?php echo number_format($room['price_per_night'], 0); ?></div>
                            <div class="room-price-period">per night</div>
                        </div>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Guest Information -->
            <div class="form-section">
                <h3 class="form-section-title"><i class="fas fa-user"></i> Guest Information</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="guest_name" class="required">Full Name</label>
                        <input type="text" id="guest_name" name="guest_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="guest_email" class="required">Email Address</label>
                        <input type="email" id="guest_email" name="guest_email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="guest_phone" class="required">Phone Number</label>
                        <input type="tel" id="guest_phone" name="guest_phone" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="guest_country">Country</label>
                        <input type="text" id="guest_country" name="guest_country" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label for="guest_address">Address</label>
                    <textarea id="guest_address" name="guest_address" class="form-control" rows="2"></textarea>
                </div>
            </div>

            <!-- Booking Details -->
            <div class="form-section">
                <h3 class="form-section-title"><i class="fas fa-calendar-alt"></i> Booking Details</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="check_in_date" class="required">Check-in Date</label>
                        <input type="date" id="check_in_date" name="check_in_date" class="form-control" required min="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="form-group">
                        <label for="check_out_date" class="required">Check-out Date</label>
                        <input type="date" id="check_out_date" name="check_out_date" class="form-control" required min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
                    </div>
                    <div class="form-group">
                        <label for="number_of_guests" class="required">Number of Guests</label>
                        <select id="number_of_guests" name="number_of_guests" class="form-control" required>
                            <option value="">Select...</option>
                            <option value="1">1 Guest</option>
                            <option value="2">2 Guests</option>
                            <option value="3">3 Guests</option>
                            <option value="4">4 Guests</option>
                            <option value="5">5+ Guests</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="special_requests">Special Requests (Optional)</label>
                    <textarea id="special_requests" name="special_requests" class="form-control" rows="3" placeholder="E.g., early check-in, airport pickup, dietary requirements..."></textarea>
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
                <i class="fas fa-info-circle"></i> Payment will be made at the hotel upon arrival. We accept cash only.
            </p>
        </form>
    </div>

    <script src="js/main.js"></script>
    <script>
        function selectRoom(label) {
            document.querySelectorAll('.room-option').forEach(opt => opt.classList.remove('selected'));
            label.classList.add('selected');
            updateSummary();
        }

        function updateSummary() {
            const roomRadio = document.querySelector('input[name="room_id"]:checked');
            const checkIn = document.getElementById('check_in_date').value;
            const checkOut = document.getElementById('check_out_date').value;

            if (roomRadio && checkIn && checkOut) {
                const roomOption = roomRadio.closest('.room-option');
                const roomName = roomOption.querySelector('h4').textContent;
                const roomPrice = parseFloat(roomOption.querySelector('.room-price-amount').textContent.replace(/[^0-9.]/g, ''));
                const roomId = roomRadio.value;

                const checkInDate = new Date(checkIn);
                const checkOutDate = new Date(checkOut);
                const nights = Math.ceil((checkOutDate - checkInDate) / (1000 * 60 * 60 * 24));

                if (nights > 0) {
                    // Check availability via AJAX
                    checkRoomAvailability(roomId, checkIn, checkOut, function(response) {
                        if (response.available) {
                            const total = response.total;
                            
                            document.getElementById('summaryRoom').textContent = roomName;
                            document.getElementById('summaryCheckIn').textContent = checkInDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                            document.getElementById('summaryCheckOut').textContent = checkOutDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                            document.getElementById('summaryNights').textContent = nights + (nights === 1 ? ' night' : ' nights');
                            document.getElementById('summaryTotal').textContent = '<?php echo $currency_symbol; ?>' + total.toLocaleString();
                            
                            document.getElementById('bookingSummary').style.display = 'block';
                            
                            // Enable submit button
                            const submitBtn = document.querySelector('.btn-submit');
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = '<i class="fas fa-check-circle"></i> Confirm Booking';
                            submitBtn.style.opacity = '1';
                        } else {
                            // Room not available
                            document.getElementById('bookingSummary').style.display = 'none';
                            
                            // Disable submit button and show message
                            const submitBtn = document.querySelector('.btn-submit');
                            submitBtn.disabled = true;
                            submitBtn.innerHTML = '<i class="fas fa-times-circle"></i> Room Not Available';
                            submitBtn.style.opacity = '0.6';
                            
                            // Show availability message
                            showAvailabilityMessage(response.message, false);
                        }
                    });
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
            // Remove existing messages
            const existingMsg = document.querySelector('.availability-message');
            if (existingMsg) {
                existingMsg.remove();
            }

            // Create new message
            const msgDiv = document.createElement('div');
            msgDiv.className = `availability-message alert ${isSuccess ? 'alert-success' : 'alert-danger'}`;
            msgDiv.innerHTML = `<i class="fas fa-${isSuccess ? 'check' : 'exclamation'}-circle"></i> ${message}`;

            // Insert before booking summary
            const summary = document.getElementById('bookingSummary');
            summary.parentNode.insertBefore(msgDiv, summary);

            // Auto-remove after 5 seconds
            setTimeout(() => {
                if (msgDiv.parentNode) {
                    msgDiv.remove();
                }
            }, 5000);
        }

        document.getElementById('check_in_date').addEventListener('change', function() {
            const checkIn = new Date(this.value);
            const nextDay = new Date(checkIn);
            nextDay.setDate(checkIn.getDate() + 1);
            document.getElementById('check_out_date').min = nextDay.toISOString().split('T')[0];
            updateSummary();
        });

        document.getElementById('check_out_date').addEventListener('change', updateSummary);

        // Add form submission validation
        document.querySelector('form').addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent default submission
            
            const roomRadio = document.querySelector('input[name="room_id"]:checked');
            const checkIn = document.getElementById('check_in_date').value;
            const checkOut = document.getElementById('check_out_date').value;
            
            if (roomRadio && checkIn && checkOut) {
                const submitBtn = document.querySelector('.btn-submit');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Checking Availability...';
                
                // Final availability check before submission
                checkRoomAvailability(roomRadio.value, checkIn, checkOut, (response) => {
                    if (response.available) {
                        // All good, proceed with form submission
                        submitBtn.innerHTML = '<i class="fas fa-check-circle"></i> Processing Booking...';
                        this.submit(); // Submit the form
                    } else {
                        // Room no longer available
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<i class="fas fa-times-circle"></i> Room No Longer Available';
                        submitBtn.style.opacity = '0.6';
                        showAvailabilityMessage('This room is no longer available for the selected dates. Please choose different dates or another room.', false);
                    }
                });
            }
        });
    </script>
</body>
</html>