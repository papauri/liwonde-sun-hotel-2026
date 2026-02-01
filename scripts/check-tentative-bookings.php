#!/usr/bin/env php
<?php
/**
 * Cron Job: Check and Process Tentative Bookings
 * Run: Every hour (crontab: 0 * * * *)
 * 
 * This script:
 * 1. Finds tentative bookings that have expired
 * 2. Marks them as expired
 * 3. Sends expiration emails
 * 4. Restores room availability
 * 5. Logs all actions
 * 6. Finds bookings expiring in 24 hours and sends reminder emails
 */

// Load bootstrap
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/email.php';

echo "========================================\n";
echo "Tentative Booking Check Cron\n";
echo "Started: " . date('Y-m-d H:i:s') . "\n";
echo "========================================\n\n";

try {
    $processed_expired = 0;
    $processed_reminders = 0;
    $errors = 0;
    
    // ============================================
    // PART 1: Process Expired Tentative Bookings
    // ============================================
    
    echo "PART 1: Checking for expired tentative bookings...\n";
    
    $stmt = $pdo->prepare("
        SELECT * FROM bookings
        WHERE status = 'tentative'
        AND tentative_expires_at IS NOT NULL
        AND tentative_expires_at < NOW()
        AND expired_at IS NULL
    ");
    $stmt->execute();
    $expired_bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Found " . count($expired_bookings) . " expired tentative bookings\n\n";
    
    if (!empty($expired_bookings)) {
        foreach ($expired_bookings as $booking) {
            echo "Processing booking: {$booking['booking_reference']}\n";
            
            $pdo->beginTransaction();
            
            try {
                // Mark as expired
                $update = $pdo->prepare("
                    UPDATE bookings
                    SET status = 'expired',
                        expired_at = NOW(),
                        updated_at = NOW()
                    WHERE id = ?
                ");
                $update->execute([$booking['id']]);
                
                // Log expiration in tentative_booking_log
                $log = $pdo->prepare("
                    INSERT INTO tentative_booking_log
                    (booking_id, action, previous_expires_at, new_expires_at, action_reason, performed_by)
                    VALUES (?, 'expired', ?, NULL, 'Automatic expiration by cron', NULL)
                ");
                $log->execute([
                    $booking['id'],
                    $booking['tentative_expires_at']
                ]);
                
                // Restore room availability if configured
                $tentative_block_availability = getSetting('tentative_block_availability', '1');
                if ($tentative_block_availability == '1') {
                    $restore = $pdo->prepare("
                        UPDATE rooms
                        SET rooms_available = rooms_available + 1
                        WHERE id = ? AND rooms_available < total_rooms
                    ");
                    $restore->execute([$booking['room_id']]);
                    echo "  - Room availability restored\n";
                }
                
                // Send expiration email
                $email_result = sendTentativeBookingExpiredEmail($booking);
                if ($email_result['success']) {
                    echo "  - Expiration email sent\n";
                } else {
                    echo "  - Email failed: {$email_result['message']}\n";
                }
                
                $pdo->commit();
                echo "  - Booking marked as expired\n\n";
                $processed_expired++;
                
            } catch (Exception $e) {
                $pdo->rollBack();
                echo "  - ERROR: {$e->getMessage()}\n\n";
                error_log("Failed to expire booking {$booking['booking_reference']}: {$e->getMessage()}");
                $errors++;
            }
        }
    }
    
    // ============================================
    // PART 2: Send Reminder Emails (24h before expiry)
    // ============================================
    
    echo "PART 2: Checking for bookings expiring in 24 hours...\n";
    
    $reminder_hours = (int)getSetting('tentative_reminder_hours', 24);
    
    $stmt = $pdo->prepare("
        SELECT * FROM bookings
        WHERE status = 'tentative'
        AND tentative_expires_at IS NOT NULL
        AND tentative_expires_at > NOW()
        AND tentative_expires_at <= DATE_ADD(NOW(), INTERVAL ? HOUR)
        AND reminder_sent = 0
    ");
    $stmt->execute([$reminder_hours]);
    $reminder_bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Found " . count($reminder_bookings) . " bookings needing reminders\n\n";
    
    if (!empty($reminder_bookings)) {
        foreach ($reminder_bookings as $booking) {
            echo "Processing reminder for booking: {$booking['booking_reference']}\n";
            
            $pdo->beginTransaction();
            
            try {
                // Send reminder email
                $email_result = sendTentativeBookingReminderEmail($booking);
                
                if ($email_result['success']) {
                    // Mark reminder as sent
                    $update = $pdo->prepare("
                        UPDATE bookings
                        SET reminder_sent = 1,
                            reminder_sent_at = NOW(),
                            updated_at = NOW()
                        WHERE id = ?
                    ");
                    $update->execute([$booking['id']]);
                    
                    // Log reminder in tentative_booking_log
                    $log = $pdo->prepare("
                        INSERT INTO tentative_booking_log
                        (booking_id, action, previous_expires_at, new_expires_at, action_reason, performed_by)
                        VALUES (?, 'reminder_sent', ?, ?, 'Reminder sent by cron', NULL)
                    ");
                    $log->execute([
                        $booking['id'],
                        $booking['tentative_expires_at'],
                        $booking['tentative_expires_at']
                    ]);
                    
                    echo "  - Reminder email sent\n";
                    $processed_reminders++;
                } else {
                    echo "  - Email failed: {$email_result['message']}\n";
                    $errors++;
                }
                
                $pdo->commit();
                echo "  - Reminder processed\n\n";
                
            } catch (Exception $e) {
                $pdo->rollBack();
                echo "  - ERROR: {$e->getMessage()}\n\n";
                error_log("Failed to send reminder for booking {$booking['booking_reference']}: {$e->getMessage()}");
                $errors++;
            }
        }
    }
    
    // ============================================
    // PART 3: Summary Statistics
    // ============================================
    
    echo "========================================\n";
    echo "SUMMARY\n";
    echo "========================================\n";
    echo "Expired bookings processed: {$processed_expired}\n";
    echo "Reminder emails sent: {$processed_reminders}\n";
    echo "Errors encountered: {$errors}\n";
    echo "Completed: " . date('Y-m-d H:i:s') . "\n";
    echo "========================================\n";
    
    // Log summary to file
    $logDir = __DIR__ . '/../logs';
    if (!file_exists($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $logFile = $logDir . '/tentative-booking-cron.log';
    $logEntry = "[" . date('Y-m-d H:i:s') . "] Expired: {$processed_expired}, Reminders: {$processed_reminders}, Errors: {$errors}\n";
    file_put_contents($logFile, $logEntry, FILE_APPEND);
    
    exit(0);
    
} catch (Exception $e) {
    echo "CRITICAL ERROR: {$e->getMessage()}\n";
    error_log("Tentative booking cron failed: " . $e->getMessage());
    exit(1);
}
