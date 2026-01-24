<?php
require_once 'config/database.php';

// Fetch policies for footer modals
$policies = [];
try {
    $policyStmt = $pdo->query("SELECT slug, title, summary, content FROM policies WHERE is_active = 1 ORDER BY display_order ASC, id ASC");
    $policies = $policyStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $policies = [];
}

// Fetch active conference rooms
try {
    $stmt = $pdo->prepare("SELECT * FROM conference_rooms WHERE is_active = 1 ORDER BY display_order ASC");
    $stmt->execute();
    $conference_rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $conference_rooms = [];
    error_log("Conference rooms fetch error: " . $e->getMessage());
}

// Handle inquiry submission
$inquiry_success = false;
$inquiry_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $room_id = filter_input(INPUT_POST, 'conference_room_id', FILTER_VALIDATE_INT);
        $company_name = trim($_POST['company_name'] ?? '');
        $contact_person = trim($_POST['contact_person'] ?? '');
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $phone = trim($_POST['phone'] ?? '');
        $event_date = $_POST['event_date'] ?? '';
        $start_time = $_POST['start_time'] ?? '';
        $end_time = $_POST['end_time'] ?? '';
        $attendees = filter_input(INPUT_POST, 'number_of_attendees', FILTER_VALIDATE_INT);
        $event_type = trim($_POST['event_type'] ?? '');
        $special_requirements = trim($_POST['special_requirements'] ?? '');
        $catering = isset($_POST['catering_required']) ? 1 : 0;
        $av_equipment = trim($_POST['av_equipment'] ?? '');

        // Validation
        if (!$room_id || !$company_name || !$contact_person || !$email || !$phone || !$event_date || !$start_time || !$end_time || !$attendees) {
            throw new Exception('Please fill in all required fields.');
        }

        // Get room details for pricing
        $room_stmt = $pdo->prepare("SELECT * FROM conference_rooms WHERE id = ? AND is_active = 1");
        $room_stmt->execute([$room_id]);
        $room = $room_stmt->fetch(PDO::FETCH_ASSOC);

        if (!$room) {
            throw new Exception('Selected conference room is not available.');
        }

        // Calculate hours and cost
        $start = new DateTime($start_time);
        $end = new DateTime($end_time);
        $hours = $start->diff($end)->h + ($start->diff($end)->i / 60);
        $total_amount = $hours * $room['hourly_rate'];

        // Generate unique inquiry reference
        do {
            $inquiry_reference = 'CONF-' . date('Y') . '-' . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);
            $ref_check = $pdo->prepare("SELECT id FROM conference_inquiries WHERE inquiry_reference = ?");
            $ref_check->execute([$inquiry_reference]);
        } while ($ref_check->rowCount() > 0);

        // Insert inquiry
        $insert_stmt = $pdo->prepare("
            INSERT INTO conference_inquiries (
                inquiry_reference, conference_room_id, company_name, contact_person, 
                email, phone, event_date, start_time, end_time, number_of_attendees, 
                event_type, special_requirements, catering_required, av_equipment, total_amount
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $insert_stmt->execute([
            $inquiry_reference, $room_id, $company_name, $contact_person,
            $email, $phone, $event_date, $start_time, $end_time, $attendees,
            $event_type, $special_requirements, $catering, $av_equipment, $total_amount
        ]);

        $inquiry_success = true;
        $success_reference = $inquiry_reference;

    } catch (Exception $e) {
        $inquiry_error = $e->getMessage();
    }
}

$currency_symbol = getSetting('currency_symbol', 'K');
$site_name = getSetting('site_name', 'Liwonde Sun Hotel');
$site_logo = getSetting('site_logo', 'images/logo.png');
$site_tagline = getSetting('site_tagline', 'Where Luxury Meets Nature');

function resolveConferenceImage(?string $imagePath): string
{
    $fallback = 'images/hero/slide1.jpg';
    if (!empty($imagePath)) {
        $normalized = ltrim($imagePath, '/');
        $fullPath = __DIR__ . '/' . $normalized;
        if (file_exists($fullPath)) {
            return $normalized;
        }
    }

    if (file_exists(__DIR__ . '/' . $fallback)) {
        return $fallback;
    }

    return '';
}
$hero_image = resolveConferenceImage('images/hero/slide1.jpg');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conference Facilities - <?php echo htmlspecialchars($site_name); ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .conference-hero {
            background: linear-gradient(135deg, rgba(15, 29, 46, 0.95) 0%, rgba(20, 40, 65, 0.9) 100%), 
                        url('<?php echo htmlspecialchars($hero_image); ?>') center/cover;
            min-height: 420px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            padding: 100px 20px 60px;
        }

        .conference-hero h1 {
            font-size: 48px;
            font-family: var(--font-serif);
            margin-bottom: 16px;
            color: var(--gold);
        }

        .conference-hero p {
            font-size: 20px;
            max-width: 700px;
            margin: 0 auto;
            color: rgba(255, 255, 255, 0.85);
        }

        .conference-rooms-section {
            padding: 90px 0;
            background: #f4f7fb;
        }

        .conference-rooms-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 32px;
            align-items: stretch;
        }

        .conference-room-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            position: relative;
            display: flex;
            flex-direction: column;
        }

        .conference-room-card.featured {
            border: 2px solid var(--gold);
        }

        .conference-room-card:hover {
            transform: translateY(-12px);
            box-shadow: 0 16px 48px rgba(0, 0, 0, 0.2);
        }

        .conference-room-image {
            position: relative;
            width: 100%;
            height: 260px;
            overflow: hidden;
        }

        .conference-room-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }

        .conference-room-card:hover .conference-room-image img {
            transform: scale(1.1);
        }

        .conference-room-content {
            padding: 28px;
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .conference-room-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
            gap: 16px;
        }

        .conference-room-title {
            font-size: 24px;
            font-family: var(--font-serif);
            color: var(--navy);
            margin: 0 0 12px 0;
            line-height: 1.3;
        }

        .conference-room-capacity {
            background: var(--gold);
            color: var(--deep-navy);
            padding: 8px 14px;
            border-radius: 12px;
            text-align: center;
            font-weight: 700;
            font-size: 13px;
            white-space: nowrap;
        }

        .conference-room-description {
            color: #666;
            line-height: 1.8;
            margin-bottom: 16px;
            flex: 1;
        }

        .conference-room-details {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 16px;
            color: #666;
            font-size: 14px;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .detail-item i {
            color: var(--gold);
        }

        .amenities-list {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 16px;
        }

        .amenity-tag {
            background: #f5f5f5;
            color: #555;
            padding: 6px 11px;
            border-radius: 6px;
            transition: all 0.25s ease;
            border: 1px solid rgba(0, 0, 0, 0.06);
            font-size: 11px;
            font-weight: 500;
        }

        .amenity-tag:hover {
            background: var(--navy);
            color: white;
            border-color: var(--navy);
        }

        .pricing-section {
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.15), rgba(212, 175, 55, 0.05));
            color: var(--navy);
            padding: 16px;
            border-radius: 12px;
            border: 1px solid rgba(212, 175, 55, 0.35);
            box-shadow: 0 6px 20px rgba(212, 175, 55, 0.15);
        }

        .pricing-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid rgba(212, 175, 55, 0.15);
        }

        .pricing-row:last-child {
            border-bottom: none;
        }

        .pricing-label {
            font-size: 14px;
            color: #666;
            font-weight: 500;
        }

        .pricing-value {
            font-size: 20px;
            font-weight: 700;
            color: var(--navy);
        }

        .btn-inquire {
            background: linear-gradient(135deg, var(--gold) 0%, #c49b2e 100%);
            color: var(--deep-navy);
            padding: 16px 40px;
            border-radius: 8px;
            border: none;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-inquire:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(212, 175, 55, 0.4);
        }

        .inquiry-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 10000;
            overflow-y: auto;
            padding: 20px;
        }

        .inquiry-modal.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .inquiry-form-container {
            background: white;
            border-radius: 16px;
            max-width: 800px;
            width: 100%;
            padding: 40px;
            position: relative;
        }

        .close-modal {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 32px;
            color: #999;
            cursor: pointer;
            transition: color 0.3s;
        }

        .close-modal:hover {
            color: var(--navy);
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--navy);
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 15px;
            transition: border-color 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--gold);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .checkbox-group input[type="checkbox"] {
            width: auto;
        }

        .success-message {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }

        .error-message {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            color: #721c24;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }

        .conference-empty {
            padding: 40px;
            background: white;
            border-radius: 16px;
            text-align: center;
            border: 1px solid rgba(15, 29, 46, 0.08);
            box-shadow: 0 12px 40px rgba(10, 20, 35, 0.08);
        }

        .conference-empty h2 {
            color: var(--navy);
            margin-bottom: 12px;
        }

        @media (max-width: 1024px) {
            .conference-rooms-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 24px;
            }
        }

        @media (max-width: 768px) {
            .conference-rooms-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
        }

        @media (max-width: 768px) {
            .conference-hero h1 {
                font-size: 32px;
            }

            .conference-rooms-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .conference-room-card {
                margin-bottom: 0;
            }

            .conference-room-image {
                height: 200px;
            }

            .conference-room-content {
                padding: 20px 16px;
            }

            .conference-room-header {
                gap: 12px;
                margin-bottom: 16px;
            }

            .conference-room-title {
                font-size: 20px;
                margin-bottom: 10px;
            }

            .conference-room-capacity {
                padding: 8px 12px;
                font-size: 11px;
                width: fit-content;
            }

            .conference-room-description {
                font-size: 13px;
                margin-bottom: 16px;
            }

            .conference-room-details {
                gap: 12px;
                font-size: 13px;
                margin-bottom: 16px;
            }

            .amenities-list {
                gap: 8px;
                margin-bottom: 16px;
            }

            .amenity-tag {
                padding: 6px 10px;
                font-size: 11px;
            }

            .pricing-section {
                padding: 12px 12px;
                margin-bottom: 16px;
            }

            .pricing-row {
                padding: 8px 0;
            }

            .pricing-label {
                font-size: 13px;
            }

            .pricing-value {
                font-size: 18px;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .inquiry-form-container {
                padding: 24px;
            }
        }

        @media (max-width: 480px) {
            .conference-hero {
                min-height: 300px;
                padding: 70px 16px 30px;
            }

            .conference-hero h1 {
                font-size: 26px;
            }

            .conference-hero p {
                font-size: 14px;
            }

            .conference-room-image {
                height: 180px;
            }

            .conference-room-title {
                font-size: 18px;
            }

            .conference-room-header {
                flex-direction: column;
                gap: 8px;
            }

            .conference-room-details {
                flex-direction: column;
                gap: 8px;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/loader.php'; ?>
    <?php include 'includes/header.php'; ?>

    <!-- Hero Section -->
    <section class="conference-hero">
        <div class="container">
            <h1>Conference & Meeting Facilities</h1>
            <p>Business-ready venues with premium technology, flexible layouts, and tailored service for every executive gathering.</p>
        </div>
    </section>

    <!-- Conference Rooms Section -->
    <section class="conference-rooms-section">
        <div class="container">
            <?php if (empty($conference_rooms)): ?>
                <div class="conference-empty">
                    <h2>No conference rooms available</h2>
                    <p>Our team is preparing the conference lineup. Please check back soon or contact us for tailored corporate options.</p>
                </div>
            <?php else: ?>
                <div class="conference-rooms-grid">
                    <?php foreach ($conference_rooms as $room): ?>
                        <?php
                        $amenities = !empty($room['amenities']) ? explode(',', $room['amenities']) : [];
                        $image_path = resolveConferenceImage($room['image_path'] ?? '');
                        ?>
                        <div class="conference-room-card">
                            <?php if (!empty($image_path)): ?>
                                <img src="<?php echo htmlspecialchars($image_path); ?>"
                                     alt="<?php echo htmlspecialchars($room['name']); ?>"
                                     class="conference-room-image">
                            <?php else: ?>
                                <div class="conference-room-image" style="background: linear-gradient(135deg, #e0e0e0 0%, #f5f5f5 100%); display: flex; align-items: center; justify-content: center; color: #999;">
                                    <i class="fas fa-image" style="font-size: 32px;"></i>
                                </div>
                            <?php endif; ?>
                            
                            <div class="conference-room-content">
                                <div class="conference-room-header">
                                    <h2 class="conference-room-title"><?php echo htmlspecialchars($room['name']); ?></h2>
                                    <span class="conference-room-capacity">
                                        <i class="fas fa-users"></i> Up to <?php echo $room['capacity']; ?> People
                                    </span>
                                </div>

                                <p class="conference-room-description"><?php echo htmlspecialchars($room['description']); ?></p>

                                <div class="conference-room-details">
                                    <div class="detail-item">
                                        <i class="fas fa-expand-arrows-alt"></i>
                                        <span><?php echo number_format($room['size_sqm'], 0); ?> sqm</span>
                                    </div>
                                    <div class="detail-item">
                                        <i class="fas fa-users"></i>
                                        <span>Capacity: <?php echo $room['capacity']; ?> people</span>
                                    </div>
                                    <div class="detail-item">
                                        <i class="fas fa-briefcase"></i>
                                        <span>Executive-ready service</span>
                                    </div>
                                </div>

                                <?php if (!empty($amenities)): ?>
                                <div class="amenities-list">
                                    <?php foreach ($amenities as $amenity): ?>
                                        <span class="amenity-tag">
                                            <i class="fas fa-check"></i> <?php echo trim(htmlspecialchars($amenity)); ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>

                                <div class="pricing-section">
                                    <div class="pricing-row">
                                        <span class="pricing-label">Hourly Rate</span>
                                        <span class="pricing-value"><?php echo $currency_symbol . number_format($room['hourly_rate'], 0); ?>/hour</span>
                                    </div>
                                    <div class="pricing-row">
                                        <span class="pricing-label">Full Day Rate</span>
                                        <span class="pricing-value"><?php echo $currency_symbol . number_format($room['daily_rate'], 0); ?>/day</span>
                                    </div>
                                </div>

                                <button class="btn-inquire" onclick="openInquiryModal(<?php echo $room['id']; ?>, '<?php echo htmlspecialchars($room['name']); ?>')">
                                    <i class="fas fa-envelope"></i> Send Inquiry
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Inquiry Modal -->
    <div class="inquiry-modal" id="inquiryModal">
        <div class="inquiry-form-container">
            <span class="close-modal" onclick="closeInquiryModal()">&times;</span>
            
            <h2 style="margin-bottom: 24px; color: var(--navy); font-family: var(--font-serif);">
                Conference Room Inquiry
            </h2>

            <?php if ($inquiry_success): ?>
                <div class="success-message">
                    <i class="fas fa-check-circle"></i> 
                    <strong>Inquiry Submitted Successfully!</strong><br>
                    Your reference number is: <strong><?php echo $success_reference; ?></strong><br>
                    We will contact you within 24 hours to confirm your booking.
                </div>
            <?php endif; ?>

            <?php if ($inquiry_error): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i> 
                    <?php echo htmlspecialchars($inquiry_error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <input type="hidden" name="conference_room_id" id="selectedRoomId">
                
                <div class="form-group">
                    <label>Conference Room</label>
                    <input type="text" id="selectedRoomName" disabled style="background: #f5f5f5;">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Company Name *</label>
                        <input type="text" name="company_name" required>
                    </div>
                    <div class="form-group">
                        <label>Contact Person *</label>
                        <input type="text" name="contact_person" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label>Phone *</label>
                        <input type="tel" name="phone" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Event Date *</label>
                    <input type="date" name="event_date" min="<?php echo date('Y-m-d'); ?>" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Start Time *</label>
                        <input type="time" name="start_time" required>
                    </div>
                    <div class="form-group">
                        <label>End Time *</label>
                        <input type="time" name="end_time" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Number of Attendees *</label>
                        <input type="number" name="number_of_attendees" min="1" required>
                    </div>
                    <div class="form-group">
                        <label>Event Type</label>
                        <select name="event_type">
                            <option value="">Select type...</option>
                            <option value="Meeting">Meeting</option>
                            <option value="Conference">Conference</option>
                            <option value="Workshop">Workshop</option>
                            <option value="Seminar">Seminar</option>
                            <option value="Training">Training</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>AV Equipment Requirements</label>
                    <input type="text" name="av_equipment" placeholder="e.g., Projector, Microphones, Sound System">
                </div>

                <div class="form-group checkbox-group">
                    <input type="checkbox" name="catering_required" id="catering">
                    <label for="catering" style="margin-bottom: 0;">Catering Required</label>
                </div>

                <div class="form-group">
                    <label>Special Requirements</label>
                    <textarea name="special_requirements" rows="4" placeholder="Any additional requests or requirements..."></textarea>
                </div>

                <button type="submit" class="btn-inquire" style="width: 100%;">
                    <i class="fas fa-paper-plane"></i> Submit Inquiry
                </button>
            </form>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="js/main.js"></script>
    <script>
        function openInquiryModal(roomId, roomName) {
            document.getElementById('selectedRoomId').value = roomId;
            document.getElementById('selectedRoomName').value = roomName;
            document.getElementById('inquiryModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeInquiryModal() {
            document.getElementById('inquiryModal').classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        <?php if ($inquiry_success): ?>
            openInquiryModal(0, '');
        <?php endif; ?>
    </script>
</body>
</html>