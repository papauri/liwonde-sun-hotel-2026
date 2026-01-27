<?php
/**
 * Database-Driven Email Configuration for any hotel
 * All settings stored in database - no hardcoded files
 */

// Require database connection for settings
require_once __DIR__ . '/database.php';

// Load PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load PHPMailer from local directory
if (file_exists(__DIR__ . '/../PHPMailer/src/PHPMailer.php')) {
    require_once __DIR__ . '/../PHPMailer/src/PHPMailer.php';
    require_once __DIR__ . '/../PHPMailer/src/SMTP.php';
    require_once __DIR__ . '/../PHPMailer/src/Exception.php';
} elseif (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    // Fallback to Composer autoloader
    require_once __DIR__ . '/../vendor/autoload.php';
}

// Get email settings from database - NO HARCODED DEFAULTS
$email_from_name = getEmailSetting('email_from_name', '');
$email_from_email = getEmailSetting('email_from_email', '');
$email_admin_email = getEmailSetting('email_admin_email', '');
$email_site_name = getSetting('site_name', '');
$email_site_url = getSetting('site_url', '');

// SMTP Configuration - From database only
$smtp_host = getEmailSetting('smtp_host', '');
$smtp_port = (int)getEmailSetting('smtp_port', 0);
$smtp_username = getEmailSetting('smtp_username', '');
$smtp_password = getEmailSetting('smtp_password', '');
$smtp_secure = getEmailSetting('smtp_secure', '');
$smtp_timeout = (int)getEmailSetting('smtp_timeout', 30);
$smtp_debug = (int)getEmailSetting('smtp_debug', 0);

// Email settings
$email_bcc_admin = (bool)getEmailSetting('email_bcc_admin', 0);
$email_development_mode = (bool)getEmailSetting('email_development_mode', 0);
$email_log_enabled = (bool)getEmailSetting('email_log_enabled', 0);
$email_preview_enabled = (bool)getEmailSetting('email_preview_enabled', 0);

// Check if we're on localhost
$is_localhost = isset($_SERVER['HTTP_HOST']) && (
    strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || 
    strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false ||
    strpos($_SERVER['HTTP_HOST'], '.local') !== false
);

// Development mode: show previews on localhost unless explicitly disabled
$development_mode = $is_localhost && $email_development_mode;

/**
 * Send email using PHPMailer
 * 
 * @param string $to Recipient email
 * @param string $toName Recipient name
 * @param string $subject Email subject
 * @param string $htmlBody HTML email body
 * @param string $textBody Plain text body (optional)
 * @return array Result array with success status and message
 */
function sendEmail($to, $toName, $subject, $htmlBody, $textBody = '') {
    global $email_from_name, $email_from_email, $email_admin_email;
    global $smtp_host, $smtp_port, $smtp_username, $smtp_password, $smtp_secure, $smtp_timeout, $smtp_debug;
    global $email_bcc_admin, $development_mode, $email_log_enabled, $email_preview_enabled;
    
    // If in development mode and no password or preview enabled, show preview
    if ($development_mode && (empty($smtp_password) || $email_preview_enabled)) {
        return createEmailPreview($to, $toName, $subject, $htmlBody, $textBody);
    }
    
    try {
        // Create PHPMailer instance
        $mail = new PHPMailer(true);
        
        // Server settings
        $mail->isSMTP();
        $mail->Host = $smtp_host;
        $mail->SMTPAuth = true;
        $mail->Username = $smtp_username;
        $mail->Password = $smtp_password;
        $mail->SMTPSecure = $smtp_secure;
        $mail->Port = $smtp_port;
        $mail->Timeout = $smtp_timeout;
        
        if ($smtp_debug > 0) {
            $mail->SMTPDebug = $smtp_debug;
        }
        
        // Recipients
        $mail->setFrom($smtp_username, $email_from_name);
        $mail->addAddress($to, $toName);
        $mail->addReplyTo($email_from_email, $email_from_name);
        
        // Add BCC for admin if enabled
        if ($email_bcc_admin && !empty($email_admin_email)) {
            $mail->addBCC($email_admin_email);
        }
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $htmlBody;
        $mail->AltBody = $textBody ?: strip_tags($htmlBody);
        
        $mail->send();
        
        // Log email if enabled
        if ($email_log_enabled) {
            logEmail($to, $toName, $subject, 'sent');
        }
        
        return [
            'success' => true,
            'message' => 'Email sent successfully via SMTP'
        ];
        
    } catch (Exception $e) {
        error_log("PHPMailer Error: " . $e->getMessage());
        
        // Log error if enabled
        if ($email_log_enabled) {
            logEmail($to, $toName, $subject, 'failed', $e->getMessage());
        }
        
        // If development mode, show preview instead of failing
        if ($development_mode) {
            return createEmailPreview($to, $toName, $subject, $htmlBody, $textBody);
        }
        
        return [
            'success' => false,
            'message' => 'Failed to send email: ' . $e->getMessage()
        ];
    }
}

/**
 * Create email preview for development mode
 */
function createEmailPreview($to, $toName, $subject, $htmlBody, $textBody = '') {
    global $email_from_name, $email_from_email, $email_admin_email, $email_site_name, $email_site_url;
    global $email_log_enabled;
    
    // Log email if enabled
    if ($email_log_enabled) {
        logEmail($to, $toName, $subject, 'preview');
    }
    
    // Create email preview file
    $previewDir = __DIR__ . '/../logs/email-previews';
    if (!file_exists($previewDir)) {
        mkdir($previewDir, 0755, true);
    }
    
    $previewFile = $previewDir . '/' . date('Y-m-d-His') . '-' . preg_replace('/[^a-z0-9]/i', '-', strtolower($subject)) . '.html';
    $previewContent = '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Email Preview: ' . htmlspecialchars($subject) . '</title>
        <style>
            body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
            .email-preview { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
            .email-info { background: #e3f2fd; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
            .email-info h3 { margin-top: 0; color: #1565c0; }
            .email-info p { margin: 5px 0; }
            .email-content { border: 1px solid #ddd; padding: 20px; border-radius: 5px; }
            .dev-note { background: #fff3cd; padding: 10px; border-left: 4px solid #ffc107; margin-top: 20px; border-radius: 5px; }
        </style>
    </head>
    <body>
        <div class="email-preview">
            <div class="email-info">
                <h3>📧 Email Preview (Development Mode)</h3>
                <p><strong>From:</strong> ' . htmlspecialchars($email_from_name) . ' <' . htmlspecialchars($email_from_email) . '></p>
                <p><strong>To:</strong> ' . htmlspecialchars($toName) . ' <' . htmlspecialchars($to) . '></p>
                <p><strong>Subject:</strong> ' . htmlspecialchars($subject) . '</p>
                <p><strong>Time:</strong> ' . date('Y-m-d H:i:s') . '</p>
                <p><strong>Status:</strong> Preview only - email would be sent via SMTP in production</p>
            </div>
            <div class="email-content">' . $htmlBody . '</div>
            <div class="dev-note">
                <p><strong>💡 Development Note:</strong> This is a preview. In production, emails will be sent automatically using SMTP.</p>
            </div>
        </div>
    </body>
    </html>';
    
    file_put_contents($previewFile, $previewContent);
    
    return [
        'success' => true,
        'message' => 'Email preview created (development mode)',
        'preview_url' => str_replace(__DIR__ . '/../', '', $previewFile)
    ];
}

/**
 * Log email activity
 */
function logEmail($to, $toName, $subject, $status, $error = '') {
    $logDir = __DIR__ . '/../logs';
    if (!file_exists($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $logFile = $logDir . '/email-log.txt';
    $logEntry = "[" . date('Y-m-d H:i:s') . "] [$status] $subject to $to ($toName)";
    if ($error) {
        $logEntry .= " - Error: $error";
    }
    $logEntry .= "\n";
    file_put_contents($logFile, $logEntry, FILE_APPEND);
}

/**
 * Send booking received email (sent immediately when user submits booking)
 */
function sendBookingReceivedEmail($booking) {
    global $pdo, $email_from_name, $email_from_email, $email_admin_email, $email_site_name, $email_site_url;
    
    try {
        // Get room details
        $stmt = $pdo->prepare("SELECT * FROM rooms WHERE id = ?");
        $stmt->execute([$booking['room_id']]);
        $room = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$room) {
            throw new Exception("Room not found");
        }
        
        // Prepare email content
        $htmlBody = '
        <h1 style="color: #0A1929; text-align: center;">Booking Received - Awaiting Confirmation</h1>
        <p>Dear ' . htmlspecialchars($booking['guest_name']) . ',</p>
        <p>Thank you for your booking request with <strong>' . htmlspecialchars($email_site_name) . '</strong>. We have received your reservation and it is currently being reviewed by our team.</p>
        
        <div style="background: #f8f9fa; border: 2px solid #0A1929; padding: 20px; margin: 20px 0; border-radius: 10px;">
            <h2 style="color: #0A1929; margin-top: 0;">Booking Details</h2>
            
            <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #ddd;">
                <span style="font-weight: bold; color: #0A1929;">Booking Reference:</span>
                <span style="color: #D4AF37; font-weight: bold; font-size: 18px;">' . htmlspecialchars($booking['booking_reference']) . '</span>
            </div>
            
            <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #ddd;">
                <span style="font-weight: bold; color: #0A1929;">Room:</span>
                <span style="color: #333;">' . htmlspecialchars($room['name']) . '</span>
            </div>
            
            <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #ddd;">
                <span style="font-weight: bold; color: #0A1929;">Check-in Date:</span>
                <span style="color: #333;">' . date('F j, Y', strtotime($booking['check_in_date'])) . '</span>
            </div>
            
            <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #ddd;">
                <span style="font-weight: bold; color: #0A1929;">Check-out Date:</span>
                <span style="color: #333;">' . date('F j, Y', strtotime($booking['check_out_date'])) . '</span>
            </div>
            
            <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #ddd;">
                <span style="font-weight: bold; color: #0A1929;">Number of Nights:</span>
                <span style="color: #333;">' . $booking['number_of_nights'] . ' night' . ($booking['number_of_nights'] != 1 ? 's' : '') . '</span>
            </div>
            
            <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #ddd;">
                <span style="font-weight: bold; color: #0A1929;">Number of Guests:</span>
                <span style="color: #333;">' . $booking['number_of_guests'] . ' guest' . ($booking['number_of_guests'] != 1 ? 's' : '') . '</span>
            </div>
            
            <div style="display: flex; justify-content: space-between; padding: 10px 0;">
                <span style="font-weight: bold; color: #0A1929;">Total Amount:</span>
                <span style="color: #D4AF37; font-weight: bold; font-size: 18px;">MWK ' . number_format($booking['total_amount'], 0) . '</span>
            </div>
        </div>
        
        <div style="background: #e7f3ff; padding: 15px; border-left: 4px solid #0d6efd; border-radius: 5px; margin: 20px 0;">
            <h3 style="color: #0d6efd; margin-top: 0;">What Happens Next?</h3>
            <p style="color: #0d6efd; margin: 0;">
                <strong>Our team will review your booking and contact you within 24 hours to confirm availability.</strong><br>
                Once confirmed, you will receive a second email with final confirmation.
            </p>
        </div>
        
        <div style="background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; border-radius: 5px; margin: 20px 0;">
            <h3 style="color: #856404; margin-top: 0;">Payment Information</h3>
            <p style="color: #856404; margin: 0;">
                <strong>Payment will be made at the hotel upon arrival.</strong><br>
                We accept cash payments only. Please bring the total amount of <strong>MWK ' . number_format($booking['total_amount'], 0) . '</strong> with you.
            </p>
        </div>';
        
        if (!empty($booking['special_requests'])) {
            $htmlBody .= '
            <div style="background: #e7f3ff; padding: 15px; border-left: 4px solid #0d6efd; border-radius: 5px; margin: 20px 0;">
                <h3 style="color: #0d6efd; margin-top: 0;">Special Requests</h3>
                <p style="color: #0d6efd; margin: 0;">' . htmlspecialchars($booking['special_requests']) . '</p>
            </div>';
        }
        
        $htmlBody .= '
        <p>If you have any questions, please contact us at <a href="mailto:' . htmlspecialchars($email_from_email) . '">' . htmlspecialchars($email_from_email) . '</a> or call +265 123 456 789.</p>
        
        <p>Thank you for choosing ' . htmlspecialchars($email_site_name) . '!</p>
        
        <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 2px solid #0A1929;">
            <p style="color: #666; font-size: 14px;">
                <strong>The ' . htmlspecialchars($email_site_name) . ' Team</strong><br>
                <a href="' . htmlspecialchars($email_site_url) . '">' . htmlspecialchars($email_site_url) . '</a>
            </p>
        </div>';
        
        // Send email
        return sendEmail(
            $booking['guest_email'],
            $booking['guest_name'],
            'Booking Received - ' . htmlspecialchars($email_site_name) . ' [' . $booking['booking_reference'] . ']',
            $htmlBody
        );
        
    } catch (Exception $e) {
        error_log("Send Booking Received Email Error: " . $e->getMessage());
        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}

/**
 * Send booking confirmed email (sent when admin approves booking)
 */
function sendBookingConfirmedEmail($booking) {
    global $pdo, $email_from_name, $email_from_email, $email_admin_email, $email_site_name, $email_site_url;
    
    try {
        // Get room details
        $stmt = $pdo->prepare("SELECT * FROM rooms WHERE id = ?");
        $stmt->execute([$booking['room_id']]);
        $room = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$room) {
            throw new Exception("Room not found");
        }
        
        // Prepare email content
        $htmlBody = '
        <h1 style="color: #0A1929; text-align: center;">Booking Confirmed!</h1>
        <p>Dear ' . htmlspecialchars($booking['guest_name']) . ',</p>
        <p>Great news! Your booking with <strong>' . htmlspecialchars($email_site_name) . '</strong> has been confirmed by our team.</p>
        
        <div style="background: #f8f9fa; border: 2px solid #0A1929; padding: 20px; margin: 20px 0; border-radius: 10px;">
            <h2 style="color: #0A1929; margin-top: 0;">Booking Details</h2>
            
            <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #ddd;">
                <span style="font-weight: bold; color: #0A1929;">Booking Reference:</span>
                <span style="color: #D4AF37; font-weight: bold; font-size: 18px;">' . htmlspecialchars($booking['booking_reference']) . '</span>
            </div>
            
            <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #ddd;">
                <span style="font-weight: bold; color: #0A1929;">Room:</span>
                <span style="color: #333;">' . htmlspecialchars($room['name']) . '</span>
            </div>
            
            <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #ddd;">
                <span style="font-weight: bold; color: #0A1929;">Check-in Date:</span>
                <span style="color: #333;">' . date('F j, Y', strtotime($booking['check_in_date'])) . '</span>
            </div>
            
            <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #ddd;">
                <span style="font-weight: bold; color: #0A1929;">Check-out Date:</span>
                <span style="color: #333;">' . date('F j, Y', strtotime($booking['check_out_date'])) . '</span>
            </div>
            
            <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #ddd;">
                <span style="font-weight: bold; color: #0A1929;">Number of Nights:</span>
                <span style="color: #333;">' . $booking['number_of_nights'] . ' night' . ($booking['number_of_nights'] != 1 ? 's' : '') . '</span>
            </div>
            
            <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #ddd;">
                <span style="font-weight: bold; color: #0A1929;">Number of Guests:</span>
                <span style="color: #333;">' . $booking['number_of_guests'] . ' guest' . ($booking['number_of_guests'] != 1 ? 's' : '') . '</span>
            </div>
            
            <div style="display: flex; justify-content: space-between; padding: 10px 0;">
                <span style="font-weight: bold; color: #0A1929;">Total Amount:</span>
                <span style="color: #D4AF37; font-weight: bold; font-size: 18px;">MWK ' . number_format($booking['total_amount'], 0) . '</span>
            </div>
        </div>
        
        <div style="background: #d4edda; padding: 15px; border-left: 4px solid #28a745; border-radius: 5px; margin: 20px 0;">
            <h3 style="color: #155724; margin-top: 0;">✅ Booking Status: Confirmed</h3>
            <p style="color: #155724; margin: 0;">
                <strong>Your booking is now confirmed and guaranteed!</strong><br>
                We look forward to welcoming you to ' . htmlspecialchars($email_site_name) . '.
            </p>
        </div>
        
        <div style="background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; border-radius: 5px; margin: 20px 0;">
            <h3 style="color: #856404; margin-top: 0;">Payment Information</h3>
            <p style="color: #856404; margin: 0;">
                <strong>Payment will be made at the hotel upon arrival.</strong><br>
                We accept cash payments only. Please bring the total amount of <strong>MWK ' . number_format($booking['total_amount'], 0) . '</strong> with you.
            </p>
        </div>
        
        <div style="background: #e7f3ff; padding: 15px; border-left: 4px solid #0d6efd; border-radius: 5px; margin: 20px 0;">
            <h3 style="color: #0d6efd; margin-top: 0;">Next Steps</h3>
            <p style="color: #0d6efd; margin: 0;">
                <strong>Please save your booking reference:</strong> ' . htmlspecialchars($booking['booking_reference']) . '<br>
                <strong>Check-in time:</strong> 2:00 PM<br>
                <strong>Check-out time:</strong> 11:00 AM<br>
                <strong>Contact us:</strong> If you need to make any changes, please contact us at least 48 hours before your arrival.
            </p>
        </div>';
        
        if (!empty($booking['special_requests'])) {
            $htmlBody .= '
            <div style="background: #e7f3ff; padding: 15px; border-left: 4px solid #0d6efd; border-radius: 5px; margin: 20px 0;">
                <h3 style="color: #0d6efd; margin-top: 0;">Special Requests</h3>
                <p style="color: #0d6efd; margin: 0;">' . htmlspecialchars($booking['special_requests']) . '</p>
            </div>';
        }
        
        $htmlBody .= '
        <p>If you have any questions, please contact us at <a href="mailto:' . htmlspecialchars($email_from_email) . '">' . htmlspecialchars($email_from_email) . '</a> or call +265 123 456 789.</p>
        
        <p>We look forward to welcoming you to ' . htmlspecialchars($email_site_name) . '!</p>
        
        <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 2px solid #0A1929;">
            <p style="color: #666; font-size: 14px;">
                <strong>The ' . htmlspecialchars($email_site_name) . ' Team</strong><br>
                <a href="' . htmlspecialchars($email_site_url) . '">' . htmlspecialchars($email_site_url) . '</a>
            </p>
        </div>';
        
        // Send email
        return sendEmail(
            $booking['guest_email'],
            $booking['guest_name'],
            'Booking Confirmed - ' . htmlspecialchars($email_site_name) . ' [' . $booking['booking_reference'] . ']',
            $htmlBody
        );
        
    } catch (Exception $e) {
        error_log("Send Booking Confirmed Email Error: " . $e->getMessage());
        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}

/**
 * Send admin notification email
 */
function sendAdminNotificationEmail($booking) {
    global $email_from_name, $email_from_email, $email_admin_email, $email_site_name, $email_site_url;
    
    try {
        $htmlBody = '
        <h1 style="color: #0A1929; text-align: center;">📋 New Booking Received</h1>
        <p>A new booking has been made on the website.</p>
        
        <div style="background: #f8f9fa; border: 2px solid #0A1929; padding: 20px; margin: 20px 0; border-radius: 10px;">
            <h2 style="color: #0A1929; margin-top: 0;">Booking Details</h2>
            
            <div style=\'display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #ddd;\'>
                <span style="font-weight: bold; color: #0A1929;">Booking Reference:</span>
                <span style="color: #D4AF37; font-weight: bold;">' . htmlspecialchars($booking['booking_reference']) . '</span>
            </div>
            
            <div style=\'display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #ddd;\'>
                <span style="font-weight: bold; color: #0A1929;">Guest Name:</span>
                <span style="color: #333;">' . htmlspecialchars($booking['guest_name']) . '</span>
            </div>
            
            <div style=\'display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #ddd;\'>
                <span style="font-weight: bold; color: #0A1929;">Guest Email:</span>
                <span style="color: #333;">' . htmlspecialchars($booking['guest_email']) . '</span>
            </div>
            
            <div style=\'display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #ddd;\'>
                <span style="font-weight: bold; color: #0A1929;">Guest Phone:</span>
                <span style="color: #333;">' . htmlspecialchars($booking['guest_phone']) . '</span>
            </div>
            
            <div style=\'display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #ddd;\'>
                <span style="font-weight: bold; color: #0A1929;">Check-in Date:</span>
                <span style="color: #333;">' . date('F j, Y', strtotime($booking['check_in_date'])) . '</span>
            </div>
            
            <div style=\'display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #ddd;\'>
                <span style="font-weight: bold; color: #0A1929;">Check-out Date:</span>
                <span style="color: #333;">' . date('F j, Y', strtotime($booking['check_out_date'])) . '</span>
            </div>
            
            <div style=\'display: flex; justify-content: space-between; padding: 10px 0;\'>
                <span style="font-weight: bold; color: #0A1929;">Total Amount:</span>
                <span style="color: #D4AF37; font-weight: bold;">MWK ' . number_format($booking['total_amount'], 0) . '</span>
            </div>
        </div>
        
        <div style="text-align: center; margin-top: 30px;">
            <a href="' . htmlspecialchars($email_site_url) . '/admin/bookings.php" style="display: inline-block; background: #D4AF37; color: #0A1929; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 16px;">
                View Booking in Admin Panel
            </a>
        </div>';
        
        // Send email
        return sendEmail(
            $email_admin_email,
            'Reservations Team',
            'New Booking - ' . htmlspecialchars($email_site_name) . ' [' . $booking['booking_reference'] . ']',
            $htmlBody
        );
        
    } catch (Exception $e) {
        error_log("Send Admin Notification Error: " . $e->getMessage());
        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}
