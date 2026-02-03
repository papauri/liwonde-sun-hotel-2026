#!/usr/bin/env php
<?php
/**
 * Cron Job: Check and Process Tentative Bookings
 * Run: Every hour (crontab: 0 * * * *)
 *
 * This script:
 * 1. Finds tentative bookings that have expired
 * 2. Finds pending bookings that have expired
 * 3. Marks them as expired
 * 4. Sends expiration emails to guests
 * 5. Sends admin notification emails
 * 6. Restores room availability
 * 7. Logs all actions
 * 8. Finds bookings expiring in 24 hours and sends reminder emails
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
    $processed_pending = 0;
    $processed_reminders = 0;
    $errors = 0;
    
    // Get settings
    $grace_period_hours = (int)getSetting('tentative_grace_period_hours', 0);
    $pending_duration_hours = (int)getSetting('pending_duration_hours', 24);
    
    echo "Settings loaded:\n";
    echo "  - Grace period: {$grace_period_hours} hours\n";
    echo "  - Pending duration: {$pending_duration_hours} hours\n\n";
    
    // ============================================
    // PART 1: Process Expired Tentative Bookings
    // ============================================
    
    echo "PART 1: Checking for expired tentative bookings...\n";
    
    // Build query with grace period support
    $grace_period_sql = $grace_period_hours > 0
        ? "AND tentative_expires_at < DATE_SUB(NOW(), INTERVAL {$grace_period_hours} HOUR)"
        : "";
    
    $stmt = $pdo->prepare("
        SELECT * FROM bookings
        WHERE status = 'tentative'
        AND tentative_expires_at IS NOT NULL
        AND tentative_expires_at < NOW()
        {$grace_period_sql}
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
                
                // Send expiration email to guest
                $email_result = sendTentativeBookingExpiredEmail($booking);
                if ($email_result['success']) {
                    echo "  - Expiration email sent to guest\n";
                } else {
                    echo "  - Guest email failed: {$email_result['message']}\n";
                }
                
                // Send admin notification
                $admin_email_result = sendAdminBookingExpiredNotification($booking, 'tentative');
                if ($admin_email_result['success']) {
                    echo "  - Admin notification sent\n";
                } else {
                    echo "  - Admin notification failed: {$admin_email_result['message']}\n";
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
    // PART 2: Process Expired Pending Bookings
    // ============================================
    
    echo "PART 2: Checking for expired pending bookings...\n";
    
    $stmt = $pdo->prepare("
        SELECT * FROM bookings
        WHERE status = 'pending'
        AND created_at < DATE_SUB(NOW(), INTERVAL ? HOUR)
        AND expired_at IS NULL
    ");
    $stmt->execute([$pending_duration_hours]);
    $expired_pending = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Found " . count($expired_pending) . " expired pending bookings\n\n";
    
    if (!empty($expired_pending)) {
        foreach ($expired_pending as $booking) {
            echo "Processing pending booking: {$booking['booking_reference']}\n";
            
            $pdo->beginTransaction();
            
            try {
                // Get room details for email
                $room_stmt = $pdo->prepare("SELECT * FROM rooms WHERE id = ?");
                $room_stmt->execute([$booking['room_id']]);
                $room = $room_stmt->fetch(PDO::FETCH_ASSOC);
                
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
                    VALUES (?, 'expired', ?, NULL, 'Pending booking auto-expired by cron', NULL)
                ");
                $log->execute([
                    $booking['id'],
                    $booking['created_at']
                ]);
                
                // Restore room availability if configured
                $pending_block_availability = getSetting('pending_block_availability', '1');
                if ($pending_block_availability == '1') {
                    $restore = $pdo->prepare("
                        UPDATE rooms
                        SET rooms_available = rooms_available + 1
                        WHERE id = ? AND rooms_available < total_rooms
                    ");
                    $restore->execute([$booking['room_id']]);
                    echo "  - Room availability restored\n";
                }
                
                // Send expiration email to guest
                $email_result = sendPendingBookingExpiredEmail($booking, $room);
                if ($email_result['success']) {
                    echo "  - Expiration email sent to guest\n";
                } else {
                    echo "  - Guest email failed: {$email_result['message']}\n";
                }
                
                // Send admin notification
                $admin_email_result = sendAdminBookingExpiredNotification($booking, 'pending');
                if ($admin_email_result['success']) {
                    echo "  - Admin notification sent\n";
                } else {
                    echo "  - Admin notification failed: {$admin_email_result['message']}\n";
                }
                
                $pdo->commit();
                echo "  - Pending booking marked as expired\n\n";
                $processed_pending++;
                
            } catch (Exception $e) {
                $pdo->rollBack();
                echo "  - ERROR: {$e->getMessage()}\n\n";
                error_log("Failed to expire pending booking {$booking['booking_reference']}: {$e->getMessage()}");
                $errors++;
            }
        }
    }
    
    // ============================================
    // PART 3: Send Reminder Emails (24h before expiry)
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
    // PART 4: Summary Statistics
    // ============================================
    
    echo "========================================\n";
    echo "SUMMARY\n";
    echo "========================================\n";
    echo "Tentative bookings expired: {$processed_expired}\n";
    echo "Pending bookings expired: {$processed_pending}\n";
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
    $logEntry = "[" . date('Y-m-d H:i:s') . "] Tentative Expired: {$processed_expired}, Pending Expired: {$processed_pending}, Reminders: {$processed_reminders}, Errors: {$errors}\n";
    file_put_contents($logFile, $logEntry, FILE_APPEND);
    
    exit(0);
    
} catch (Exception $e) {
    echo "CRITICAL ERROR: {$e->getMessage()}\n";
    error_log("Tentative booking cron failed: " . $e->getMessage());
    exit(1);
}
