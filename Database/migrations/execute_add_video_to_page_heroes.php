<?php
/**
 * Migration Script: Add Video Support to page_heroes Table
 * Run this script to add hero_video_path and hero_video_type columns
 * 
 * Usage: php Database/migrations/execute_add_video_to_page_heroes.php
 * Or visit in browser: https://yourdomain.com/Database/migrations/execute_add_video_to_page_heroes.php
 */

// Load database configuration
require_once __DIR__ . '/../../config/database.php';

echo "=== Migration: Add Video Support to page_heroes Table ===\n";
echo "Starting migration...\n\n";

try {
    // Start transaction
    $pdo->beginTransaction();
    
    // Check if columns already exist
    $checkSql = "
        SELECT COUNT(*) as count
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = 'page_heroes'
        AND COLUMN_NAME IN ('hero_video_path', 'hero_video_type')
    ";
    $stmt = $pdo->query($checkSql);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result['count'] >= 2) {
        echo "✓ Video columns already exist in page_heroes table.\n";
        echo "Migration not needed.\n";
        exit(0);
    }
    
    // Add hero_video_path column
    echo "Adding hero_video_path column...\n";
    $sql1 = "
        ALTER TABLE `page_heroes`
        ADD COLUMN `hero_video_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL 
        COMMENT 'Path to video file or URL (e.g., Getty Images, YouTube, Vimeo)' 
        AFTER `hero_image_path`
    ";
    $pdo->exec($sql1);
    echo "✓ hero_video_path column added successfully.\n";
    
    // Add hero_video_type column
    echo "Adding hero_video_type column...\n";
    $sql2 = "
        ALTER TABLE `page_heroes`
        ADD COLUMN `hero_video_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL 
        COMMENT 'Video MIME type (video/mp4, video/webm, etc.) or platform (youtube, vimeo, getty)' 
        AFTER `hero_video_path`
    ";
    $pdo->exec($sql2);
    echo "✓ hero_video_type column added successfully.\n";
    
    // Verify columns were added
    echo "\nVerifying columns...\n";
    $verifySql = "
        SELECT 
            COLUMN_NAME, 
            COLUMN_TYPE, 
            IS_NULLABLE, 
            COLUMN_DEFAULT, 
            COLUMN_COMMENT
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = 'page_heroes'
        AND COLUMN_NAME IN ('hero_video_path', 'hero_video_type')
    ";
    $stmt = $pdo->query($verifySql);
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($columns as $column) {
        echo sprintf(
            "  - %s: %s, NULL=%s, DEFAULT=%s\n",
            $column['COLUMN_NAME'],
            $column['COLUMN_TYPE'],
            $column['IS_NULLABLE'],
            $column['COLUMN_DEFAULT'] ?? 'NULL'
        );
    }
    
    // Commit transaction
    $pdo->commit();
    
    echo "\n✓ Migration completed successfully!\n";
    echo "You can now add video URLs to page_heroes records.\n";
    
} catch (PDOException $e) {
    // Rollback on error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    echo "\n✗ Migration failed!\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "Please check your database connection and permissions.\n";
    exit(1);
}