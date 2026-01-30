-- =====================================================
-- Add Missing Columns to Payments Table
-- =====================================================
-- Date: 2026-01-30
-- Description: Adds missing columns to the payments table for enhanced payment tracking
-- Database: p601229_hotels
-- Host: promanaged-it.com
-- 
-- This script adds the following columns to the payments table:
-- - conference_id: Link to conference bookings
-- - payment_type: Type of payment transaction
-- - invoice_number: Invoice reference number
-- - amount: Additional payment amount field (DECIMAL)
-- - status: Additional payment status field (ENUM)
-- - transaction_id: Additional transaction reference field (VARCHAR)
--
-- Note: These are ADDITIONAL columns that coexist with existing columns:
-- - payment_amount (existing) - amount (new)
-- - payment_status (existing) - status (new)
-- - payment_reference_number (existing) - transaction_id (new)
-- =====================================================

USE `p601229_hotels`;

-- Start transaction for safe execution
START TRANSACTION;

-- =====================================================
-- 1. ADD conference_id COLUMN
-- =====================================================
-- Purpose: Optional link to conference_inquiries table for conference-specific payments
-- Type: INT UNSIGNED (nullable)
-- Default: NULL
-- Position: After booking_id column
-- =====================================================

SET @dbname = DATABASE();
SET @tablename = 'payments';
SET @columnname = 'conference_id';

SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = @tablename
    AND COLUMN_NAME = @columnname
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN `', @columnname, '` int UNSIGNED DEFAULT NULL COMMENT ''Optional link to conference_inquiries table for conference-specific payments'' AFTER `booking_id`')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- =====================================================
-- 2. ADD payment_type COLUMN
-- =====================================================
-- Purpose: Distinguish between different types of payment transactions
-- Type: ENUM with values: deposit, full_payment, partial_payment, refund, adjustment
-- Default: NULL (nullable)
-- Position: After payment_method column
-- =====================================================

SET @columnname = 'payment_type';

SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = @tablename
    AND COLUMN_NAME = @columnname
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN `', @columnname, '` enum(''deposit'',''full_payment'',''partial_payment'',''refund'',''adjustment'') DEFAULT NULL COMMENT ''Type of payment transaction'' AFTER `payment_method`')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- =====================================================
-- 3. ADD invoice_number COLUMN
-- =====================================================
-- Purpose: Store the invoice number associated with this payment
-- Type: VARCHAR(50) (nullable)
-- Default: NULL
-- Position: After invoice_generated column
-- Example values: INV-2026-000001, CONF-INV-2026-000001
-- =====================================================

SET @columnname = 'invoice_number';

SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = @tablename
    AND COLUMN_NAME = @columnname
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN `', @columnname, '` varchar(50) DEFAULT NULL COMMENT ''Invoice number (e.g., INV-2026-000001)'' AFTER `invoice_generated`')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- =====================================================
-- 4. ADD amount COLUMN
-- =====================================================
-- Purpose: Additional payment amount field (coexists with payment_amount)
-- Type: DECIMAL(10,2) (nullable)
-- Default: 0.00
-- Position: After invoice_number column
-- Note: This is an ADDITIONAL column, not a replacement for payment_amount
-- =====================================================

SET @columnname = 'amount';

SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = @tablename
    AND COLUMN_NAME = @columnname
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN `', @columnname, '` decimal(10,2) DEFAULT 0.00 COMMENT ''Additional payment amount field - coexists with payment_amount'' AFTER `invoice_number`')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- =====================================================
-- 5. ADD status COLUMN
-- =====================================================
-- Purpose: Additional payment status field (coexists with payment_status)
-- Type: ENUM with values: pending, completed, failed, refunded
-- Default: 'pending' (nullable)
-- Position: After amount column
-- Note: This is an ADDITIONAL column, not a replacement for payment_status
-- =====================================================

SET @columnname = 'status';

SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = @tablename
    AND COLUMN_NAME = @columnname
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN `', @columnname, '` enum(''pending'',''completed'',''failed'',''refunded'') DEFAULT ''pending'' COMMENT ''Additional payment status field - coexists with payment_status'' AFTER `amount`')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- =====================================================
-- 6. ADD transaction_id COLUMN
-- =====================================================
-- Purpose: Additional transaction reference field (coexists with payment_reference_number)
-- Type: VARCHAR(100) (nullable)
-- Default: NULL
-- Position: After status column
-- Note: This is an ADDITIONAL column, not a replacement for payment_reference_number
-- =====================================================

SET @columnname = 'transaction_id';

SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = @tablename
    AND COLUMN_NAME = @columnname
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN `', @columnname, '` varchar(100) DEFAULT NULL COMMENT ''Additional transaction reference field - coexists with payment_reference_number'' AFTER `status`')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- =====================================================
-- 7. CREATE INDEX FOR conference_id
-- =====================================================
-- Purpose: Improve query performance when joining with conference_inquiries table
-- =====================================================

SET @indexname = 'idx_conference_id';

SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = @tablename
    AND INDEX_NAME = @indexname
  ) > 0,
  'SELECT 1',
  CONCAT('CREATE INDEX `', @indexname, '` ON ', @tablename, ' (`conference_id`)')
));
PREPARE createIndexIfNotExists FROM @preparedStatement;
EXECUTE createIndexIfNotExists;
DEALLOCATE PREPARE createIndexIfNotExists;

-- =====================================================
-- VERIFICATION QUERIES
-- =====================================================
-- Run these queries after execution to verify the changes

-- Verify columns were added successfully
-- SELECT COLUMN_NAME, DATA_TYPE, COLUMN_DEFAULT, IS_NULLABLE, COLUMN_COMMENT
-- FROM INFORMATION_SCHEMA.COLUMNS
-- WHERE TABLE_SCHEMA = 'p601229_hotels'
-- AND TABLE_NAME = 'payments'
-- AND COLUMN_NAME IN ('conference_id', 'payment_type', 'invoice_number', 'amount', 'status', 'transaction_id')
-- ORDER BY ORDINAL_POSITION;

-- Verify index was created
-- SELECT INDEX_NAME, COLUMN_NAME
-- FROM INFORMATION_SCHEMA.STATISTICS
-- WHERE TABLE_SCHEMA = 'p601229_hotels'
-- AND TABLE_NAME = 'payments'
-- AND INDEX_NAME = 'idx_conference_id';

-- Commit transaction
COMMIT;

-- =====================================================
-- EXECUTION COMPLETE
-- =====================================================
-- All missing columns have been added to the payments table.
-- You can now use these columns in your application code.
-- =====================================================
