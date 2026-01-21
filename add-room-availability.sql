-- Add rooms_available column to track inventory for each room type
ALTER TABLE rooms ADD COLUMN rooms_available INT DEFAULT 5 AFTER max_guests;
ALTER TABLE rooms ADD COLUMN total_rooms INT DEFAULT 5 AFTER rooms_available;

-- Update existing rooms with default values
UPDATE rooms SET rooms_available = 5, total_rooms = 5 WHERE rooms_available IS NULL;

-- Add comment
ALTER TABLE rooms MODIFY COLUMN rooms_available INT DEFAULT 5 COMMENT 'Number of rooms currently available for booking';
ALTER TABLE rooms MODIFY COLUMN total_rooms INT DEFAULT 5 COMMENT 'Total number of rooms of this type';
