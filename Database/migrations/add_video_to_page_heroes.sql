-- Migration: Add video support to page_heroes table
-- Date: 2026-02-05
-- Description: Adds hero_video_path and hero_video_type columns to support video backgrounds in page hero sections

-- Add hero_video_path column
ALTER TABLE `page_heroes`
ADD COLUMN `hero_video_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL 
COMMENT 'Path to video file or URL (e.g., Getty Images, YouTube, Vimeo)' 
AFTER `hero_image_path`;

-- Add hero_video_type column
ALTER TABLE `page_heroes`
ADD COLUMN `hero_video_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL 
COMMENT 'Video MIME type (video/mp4, video/webm, etc.) or platform (youtube, vimeo, getty)' 
AFTER `hero_video_path`;

-- Verify columns were added successfully
SELECT 
    COLUMN_NAME, 
    COLUMN_TYPE, 
    IS_NULLABLE, 
    COLUMN_DEFAULT, 
    COLUMN_COMMENT
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME = 'page_heroes'
AND COLUMN_NAME IN ('hero_video_path', 'hero_video_type');