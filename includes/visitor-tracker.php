<?php
/**
 * Visitor Tracker
 * Lightweight visitor/session logger for public pages.
 * Include this file in footer.php so it runs on every public page.
 * Does NOT track admin pages, API calls, or AJAX requests.
 *
 * Records: IP, user agent, device type, browser, OS, referrer, page URL.
 * Uses session to avoid duplicate inserts on rapid page loads.
 */

// Only track if we have a DB connection and sessions
if (!isset($pdo) || session_status() !== PHP_SESSION_ACTIVE) return;

// Don't track admin pages, API endpoints, or AJAX requests
$_vt_script = $_SERVER['SCRIPT_FILENAME'] ?? '';
$_vt_file = basename($_SERVER['PHP_SELF']);
if (strpos($_vt_script, DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR) !== false
    || strpos($_vt_script, DIRECTORY_SEPARATOR . 'api' . DIRECTORY_SEPARATOR) !== false
    || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')) {
    return;
}

// Rate limit: max 1 log per page per 2 seconds per session
$_vt_key = 'vt_last_' . md5($_vt_file);
if (isset($_SESSION[$_vt_key]) && (time() - $_SESSION[$_vt_key]) < 2) {
    return;
}

try {
    // Check if table exists (cached in session for performance)
    if (!isset($_SESSION['_vt_table_ok'])) {
        $check = $pdo->query("SHOW TABLES LIKE 'site_visitors'");
        if ($check->rowCount() === 0) {
            // Auto-create table
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS `site_visitors` (
                    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    `session_id` VARCHAR(128) NOT NULL,
                    `ip_address` VARCHAR(45) NOT NULL,
                    `user_agent` TEXT,
                    `device_type` ENUM('desktop', 'tablet', 'mobile', 'bot', 'unknown') DEFAULT 'unknown',
                    `browser` VARCHAR(100) DEFAULT NULL,
                    `os` VARCHAR(100) DEFAULT NULL,
                    `referrer` TEXT DEFAULT NULL,
                    `referrer_domain` VARCHAR(255) DEFAULT NULL,
                    `country` VARCHAR(100) DEFAULT NULL,
                    `page_url` VARCHAR(500) NOT NULL,
                    `page_title` VARCHAR(255) DEFAULT NULL,
                    `is_first_visit` TINYINT(1) DEFAULT 0,
                    `visit_duration` INT DEFAULT NULL COMMENT 'Seconds on page',
                    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    INDEX `idx_session` (`session_id`),
                    INDEX `idx_ip` (`ip_address`),
                    INDEX `idx_created` (`created_at`),
                    INDEX `idx_page` (`page_url`(191)),
                    INDEX `idx_device` (`device_type`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        }
        $_SESSION['_vt_table_ok'] = true;
    }

    // Gather visitor data
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    if (strpos($ip, ',') !== false) {
        $ip = trim(explode(',', $ip)[0]);
    }

    $referrer = $_SERVER['HTTP_REFERER'] ?? '';
    $referrer_domain = '';
    if (!empty($referrer)) {
        $parsed = parse_url($referrer);
        $referrer_domain = $parsed['host'] ?? '';
    }

    $page_url = $_SERVER['REQUEST_URI'] ?? '/' . $_vt_file;
    $session_id = session_id();

    // Detect device type
    $device_type = 'desktop';
    if (preg_match('/bot|crawl|spider|slurp|googlebot|bingbot|yandex|baidu/i', $ua)) {
        $device_type = 'bot';
    } elseif (preg_match('/mobile|android.*mobile|iphone|ipod|blackberry|opera mini|iemobile/i', $ua)) {
        $device_type = 'mobile';
    } elseif (preg_match('/tablet|ipad|playbook|silk|kindle/i', $ua)) {
        $device_type = 'tablet';
    } elseif (empty($ua)) {
        $device_type = 'unknown';
    }

    // Detect browser
    $browser = 'Unknown';
    if (preg_match('/Edg\//i', $ua)) $browser = 'Edge';
    elseif (preg_match('/OPR\//i', $ua)) $browser = 'Opera';
    elseif (preg_match('/Chrome\//i', $ua) && !preg_match('/Edg/i', $ua)) $browser = 'Chrome';
    elseif (preg_match('/Firefox\//i', $ua)) $browser = 'Firefox';
    elseif (preg_match('/Safari\//i', $ua) && !preg_match('/Chrome/i', $ua)) $browser = 'Safari';
    elseif (preg_match('/MSIE|Trident/i', $ua)) $browser = 'IE';
    elseif (preg_match('/bot|crawl|spider/i', $ua)) $browser = 'Bot';

    // Detect OS
    $os = 'Unknown';
    if (preg_match('/Windows NT 10/i', $ua)) $os = 'Windows 10/11';
    elseif (preg_match('/Windows NT/i', $ua)) $os = 'Windows';
    elseif (preg_match('/Macintosh|Mac OS/i', $ua)) $os = 'macOS';
    elseif (preg_match('/Linux/i', $ua) && preg_match('/Android/i', $ua)) $os = 'Android';
    elseif (preg_match('/Linux/i', $ua)) $os = 'Linux';
    elseif (preg_match('/iPhone|iPad|iPod/i', $ua)) $os = 'iOS';
    elseif (preg_match('/CrOS/i', $ua)) $os = 'Chrome OS';

    // Check if first visit in this session
    $is_first = 0;
    if (!isset($_SESSION['_vt_first_logged'])) {
        $is_first = 1;
        $_SESSION['_vt_first_logged'] = true;
    }

    // Insert visitor record
    $stmt = $pdo->prepare("
        INSERT INTO site_visitors
        (session_id, ip_address, user_agent, device_type, browser, os, referrer, referrer_domain, page_url, is_first_visit, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    $stmt->execute([
        $session_id, $ip, substr($ua, 0, 1000), $device_type, $browser, $os,
        substr($referrer, 0, 1000), $referrer_domain, substr($page_url, 0, 500), $is_first
    ]);

    $_SESSION[$_vt_key] = time();

} catch (Exception $e) {
    // Silently fail — visitor tracking should never break the page
    error_log('Visitor tracker error: ' . $e->getMessage());
}
