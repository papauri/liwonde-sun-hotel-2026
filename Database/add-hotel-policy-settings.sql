-- Add hotel policy settings to site_settings table
INSERT INTO site_settings (setting_key, setting_value, setting_group) VALUES
('check_in_time', '2:00 PM', 'booking'),
('check_out_time', '11:00 AM', 'booking'),
('booking_change_policy', 'If you need to make any changes, please contact us at least 48 hours before your arrival.', 'booking')
ON DUPLICATE KEY UPDATE 
    setting_value = VALUES(setting_value),
    setting_group = VALUES(setting_group),
    updated_at = CURRENT_TIMESTAMP;
