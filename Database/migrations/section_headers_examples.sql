-- ============================================
-- Section Headers - Usage Examples
-- ============================================
-- This file shows how to manage section headers after the table is created

-- ============================================
-- View All Section Headers
-- ============================================
SELECT 
    section_key,
    page,
    section_label,
    section_subtitle,
    section_title,
    section_description,
    is_active
FROM section_headers
ORDER BY page, display_order;

-- ============================================
-- Update Existing Section Header
-- ============================================
-- Example: Update homepage rooms section
UPDATE section_headers
SET 
    section_label = 'Accommodations',
    section_subtitle = 'Where Comfort Meets Luxury',
    section_title = 'Luxurious Rooms & Suites',
    section_description = 'Experience unmatched comfort in our meticulously designed rooms and suites'
WHERE section_key = 'home_rooms' AND page = 'index';

-- ============================================
-- Add New Section Header
-- ============================================
-- Example: Add a new section for spa services
INSERT INTO section_headers 
    (section_key, page, section_label, section_subtitle, section_title, section_description, display_order, is_active)
VALUES 
    ('spa_services', 'spa', 'Relaxation', 'Rejuvenate Your Body & Mind', 'Premium Spa Services', 'Indulge in our world-class spa treatments', 1, 1);

-- ============================================
-- Disable/Enable Section Header
-- ============================================
-- Disable a section (won't appear on site)
UPDATE section_headers SET is_active = 0 WHERE section_key = 'home_facilities' AND page = 'index';

-- Enable a section
UPDATE section_headers SET is_active = 1 WHERE section_key = 'home_facilities' AND page = 'index';

-- ============================================
-- Delete Section Header (use with caution)
-- ============================================
-- DELETE FROM section_headers WHERE section_key = 'old_section' AND page = 'old_page';

-- ============================================
-- Get All Section Headers for Specific Page
-- ============================================
-- View all sections for the gym page
SELECT * FROM section_headers WHERE page = 'gym' ORDER BY display_order;

-- View global sections (available on all pages)
SELECT * FROM section_headers WHERE page = 'global' ORDER BY display_order;

-- ============================================
-- Update Display Order (for reordering sections)
-- ============================================
UPDATE section_headers SET display_order = 1 WHERE section_key = 'gym_wellness' AND page = 'gym';
UPDATE section_headers SET display_order = 2 WHERE section_key = 'gym_facilities' AND page = 'gym';
UPDATE section_headers SET display_order = 3 WHERE section_key = 'gym_classes' AND page = 'gym';

-- ============================================
-- Bulk Update - Change Label for Multiple Sections
-- ============================================
-- Example: Update all "Stay Active" labels
UPDATE section_headers 
SET section_label = 'Fitness & Wellness'
WHERE section_label = 'Stay Active';

-- ============================================
-- Search Section Headers
-- ============================================
-- Find all sections with "fitness" in title
SELECT * FROM section_headers 
WHERE section_title LIKE '%fitness%' 
OR section_description LIKE '%fitness%';

-- Find all sections on restaurant page
SELECT * FROM section_headers WHERE page = 'restaurant';

-- ============================================
-- Clear Subtitle for All Sections (if needed)
-- ============================================
-- UPDATE section_headers SET section_subtitle = NULL;
