-- Fix for booking date picker issue
-- Adds the missing max_advance_booking_days setting
-- This setting controls how far in advance guests can book rooms

-- Insert the missing setting
INSERT INTO `site_settings` (`setting_key`, `setting_value`, `setting_group`, `updated_at`)
VALUES ('max_advance_booking_days', '365', 'booking', NOW())
ON DUPLICATE KEY UPDATE 
    `setting_value` = '365',
    `updated_at` = NOW();

-- Also add the payment_policy setting if it doesn't exist
INSERT INTO `site_settings` (`setting_key`, `setting_value`, `setting_group`, `updated_at`)
VALUES ('payment_policy', 'Full payment is required upon check-in. We accept cash, credit cards, and bank transfers.', 'booking', NOW())
ON DUPLICATE KEY UPDATE 
    `setting_value` = `setting_value`,
    `updated_at` = NOW();

-- Verify the settings were added
SELECT * FROM site_settings WHERE setting_key IN ('max_advance_booking_days', 'payment_policy');
