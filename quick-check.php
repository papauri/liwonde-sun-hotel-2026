<?php
require_once 'config/database.php';

$settings = getAllEmailSettings();
$pass = $settings['smtp_password']['value'] ?? '';
echo "Password in DB: " . (empty($pass) ? 'EMPTY' : 'SET (' . strlen($pass) . ' chars)') . "\n";

$retrieved = getEmailSetting('smtp_password', '');
echo "Retrieved via getEmailSetting: " . (empty($retrieved) ? 'EMPTY' : 'SET (' . strlen($retrieved) . ' chars)') . "\n";

if (!empty($retrieved)) {
    echo "\n✅ Password is set and can be retrieved!\n";
    echo "You can test the email system with: simple-smtp-test.php\n";
} else {
    echo "\n❌ Password is still empty or cannot be retrieved.\n";
    echo "You need to set the password in the admin panel.\n";
    echo "Go to: /admin/booking-settings.php\n";
}
?>