-- Add booking time buffer setting to site_settings
-- This setting defines the minimum advance time required for bookings (in minutes)
-- Default: 60 minutes (1 hour)

-- Check if setting exists, if not add it
INSERT INTO site_settings (setting_key, setting_value, setting_type, category, display_name, description, is_editable, display_order, created_at)
SELECT 
    'booking_time_buffer_minutes',
    '60',
    'number',
    'booking',
    'Booking Time Buffer (Minutes)',
    'Minimum advance time required for gym and conference bookings. Users cannot book within this time buffer from the current time.',
    1,
    50,
    NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM site_settings WHERE setting_key = 'booking_time_buffer_minutes'
);

-- Verify the setting was added
SELECT * FROM site_settings WHERE setting_key = 'booking_time_buffer_minutes';
