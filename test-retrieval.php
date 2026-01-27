<?php
require_once 'config/database.php';

$retrieved = getEmailSetting('smtp_password', '');
echo "Password retrieved via getEmailSetting(): ";
if (empty($retrieved)) {
    echo "EMPTY\n";
    echo "\n❌ The password is still not retrievable.\n";
    echo "This means the decryption is failing.\n";
} else {
    echo "SET (" . strlen($retrieved) . " characters)\n";
    echo "First 3 chars: '" . substr($retrieved, 0, 3) . "...'\n";
    echo "\n✅ Password is set and can be retrieved!\n";
    echo "The email system should now work.\n";
    echo "Test with: simple-smtp-test.php\n";
}
?>