-- Add Maximum Advance Booking Days Setting
-- This allows admins to configure how many days in advance guests can book

-- Insert the setting if it doesn't exist
INSERT INTO site_settings (setting_key, setting_value, setting_group, display_name, description, data_type, is_editable, created_at, updated_at)
VALUES (
    'max_advance_booking_days',
    '30',
    'booking',
    'Maximum Advance Booking Days',
    'Maximum number of days in advance that guests can make a booking. Default is 30 days (one month).',
    'integer',
    1,
    NOW(),
    NOW()
)
ON DUPLICATE KEY UPDATE 
    setting_value = '30',
    updated_at = NOW();

-- Verify the setting was added
SELECT * FROM site_settings WHERE setting_key = 'max_advance_booking_days';