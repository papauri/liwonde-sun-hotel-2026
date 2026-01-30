-- Migration: Payments and Accounting System
-- Date: 2026-01-29
-- Description: Add comprehensive payment tracking with VAT support for room and conference bookings

-- Start transaction
START TRANSACTION;

-- =====================================================
-- 1. CREATE PAYMENTS TABLE
-- =====================================================

CREATE TABLE IF NOT EXISTS `payments` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `payment_reference` varchar(50) NOT NULL COMMENT 'Unique payment reference like PAY-2026-000001',
  `booking_type` enum('room','conference') NOT NULL COMMENT 'Type of booking',
  `booking_id` int UNSIGNED NOT NULL COMMENT 'ID from bookings or conference_inquiries table',
  `booking_reference` varchar(50) NOT NULL COMMENT 'Reference from booking (LSH2026xxxx or CONF-2026-xxxx)',
  `payment_date` date NOT NULL,
  `payment_amount` decimal(10,2) NOT NULL COMMENT 'Amount paid before VAT',
  `vat_rate` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT 'VAT percentage (e.g., 16.50 for 16.5%)',
  `vat_amount` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Calculated VAT amount',
  `total_amount` decimal(10,2) NOT NULL COMMENT 'Total including VAT',
  `payment_method` enum('cash','bank_transfer','mobile_money','credit_card','debit_card','cheque','other') NOT NULL DEFAULT 'cash',
  `payment_reference_number` varchar(100) DEFAULT NULL COMMENT 'Transaction ID, receipt number, or cheque number',
  `payment_status` enum('pending','partial','fully_paid','overdue','refunded') NOT NULL DEFAULT 'pending',
  `invoice_generated` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Whether invoice has been generated',
  `invoice_path` varchar(255) DEFAULT NULL COMMENT 'Path to generated invoice file',
  `notes` text DEFAULT NULL COMMENT 'Additional payment notes',
  `recorded_by` int UNSIGNED DEFAULT NULL COMMENT 'Admin user who recorded the payment',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payment_reference` (`payment_reference`),
  KEY `idx_booking_type_id` (`booking_type`, `booking_id`),
  KEY `idx_payment_date` (`payment_date`),
  KEY `idx_payment_status` (`payment_status`),
  KEY `idx_recorded_by` (`recorded_by`),
  CONSTRAINT `fk_payments_admin` FOREIGN KEY (`recorded_by`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='All payment transactions for room and conference bookings';

-- =====================================================
-- 2. ADD VAT/TAX SETTINGS TO SITE_SETTINGS
-- =====================================================

-- Insert VAT and accounting settings
INSERT INTO `site_settings` (`setting_key`, `setting_value`, `setting_group`) VALUES
('vat_enabled', '1', 'accounting'),
('vat_rate', '16.5', 'accounting'),
('vat_number', 'MW123456789', 'accounting'),
('payment_terms', 'Payment due upon check-in', 'accounting'),
('invoice_prefix', 'INV', 'accounting'),
('invoice_start_number', '1001', 'accounting')
ON DUPLICATE KEY UPDATE
  `setting_value` = VALUES(`setting_value`);

-- =====================================================
-- 3. UPDATE BOOKINGS TABLE
-- =====================================================

-- Check if columns exist before adding (for re-runnable migration)
SET @dbname = DATABASE();
SET @tablename = 'bookings';
SET @columnname1 = 'amount_paid';
SET @columnname2 = 'amount_due';
SET @columnname3 = 'vat_rate';
SET @columnname4 = 'vat_amount';
SET @columnname5 = 'total_with_vat';
SET @columnname6 = 'last_payment_date';

SET @preparedStatement1 = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = @tablename
    AND COLUMN_NAME = @columnname1
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN `', @columnname1, '` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT ''Total amount paid so far'' AFTER `total_amount`')
));
PREPARE alterIfNotExists1 FROM @preparedStatement1;
EXECUTE alterIfNotExists1;
DEALLOCATE PREPARE alterIfNotExists1;

SET @preparedStatement2 = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = @tablename
    AND COLUMN_NAME = @columnname2
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN `', @columnname2, '` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT ''Remaining amount to be paid'' AFTER `', @columnname1, '`')
));
PREPARE alterIfNotExists2 FROM @preparedStatement2;
EXECUTE alterIfNotExists2;
DEALLOCATE PREPARE alterIfNotExists2;

SET @preparedStatement3 = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = @tablename
    AND COLUMN_NAME = @columnname3
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN `', @columnname3, '` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT ''VAT rate applied'' AFTER `', @columnname2, '`')
));
PREPARE alterIfNotExists3 FROM @preparedStatement3;
EXECUTE alterIfNotExists3;
DEALLOCATE PREPARE alterIfNotExists3;

SET @preparedStatement4 = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = @tablename
    AND COLUMN_NAME = @columnname4
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN `', @columnname4, '` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT ''VAT amount'' AFTER `', @columnname3, '`')
));
PREPARE alterIfNotExists4 FROM @preparedStatement4;
EXECUTE alterIfNotExists4;
DEALLOCATE PREPARE alterIfNotExists4;

SET @preparedStatement5 = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = @tablename
    AND COLUMN_NAME = @columnname5
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN `', @columnname5, '` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT ''Total amount including VAT'' AFTER `', @columnname4, '`')
));
PREPARE alterIfNotExists5 FROM @preparedStatement5;
EXECUTE alterIfNotExists5;
DEALLOCATE PREPARE alterIfNotExists5;

SET @preparedStatement6 = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = @tablename
    AND COLUMN_NAME = @columnname6
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN `', @columnname6, '` date DEFAULT NULL COMMENT ''Date of last payment'' AFTER `', @columnname5, '`')
));
PREPARE alterIfNotExists6 FROM @preparedStatement6;
EXECUTE alterIfNotExists6;
DEALLOCATE PREPARE alterIfNotExists6;

-- Add index for payment_status if it doesn't exist
SET @indexname = 'idx_payment_status';
SET @preparedStatement7 = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = @tablename
    AND INDEX_NAME = @indexname
  ) > 0,
  'SELECT 1',
  CONCAT('CREATE INDEX `', @indexname, '` ON ', @tablename, ' (`payment_status`)')
));
PREPARE createIndexIfNotExists1 FROM @preparedStatement7;
EXECUTE createIndexIfNotExists1;
DEALLOCATE PREPARE createIndexIfNotExists1;

-- =====================================================
-- 4. UPDATE CONFERENCE_INQUIRIES TABLE
-- =====================================================

SET @tablename = 'conference_inquiries';
SET @columnname1 = 'amount_paid';
SET @columnname2 = 'amount_due';
SET @columnname3 = 'vat_rate';
SET @columnname4 = 'vat_amount';
SET @columnname5 = 'total_with_vat';
SET @columnname6 = 'last_payment_date';
SET @columnname7 = 'deposit_required';
SET @columnname8 = 'deposit_amount';
SET @columnname9 = 'deposit_paid';

SET @preparedStatement1 = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = @tablename
    AND COLUMN_NAME = @columnname1
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN `', @columnname1, '` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT ''Total amount paid so far'' AFTER `total_amount`')
));
PREPARE alterIfNotExists1 FROM @preparedStatement1;
EXECUTE alterIfNotExists1;
DEALLOCATE PREPARE alterIfNotExists1;

SET @preparedStatement2 = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = @tablename
    AND COLUMN_NAME = @columnname2
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN `', @columnname2, '` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT ''Remaining amount to be paid'' AFTER `', @columnname1, '`')
));
PREPARE alterIfNotExists2 FROM @preparedStatement2;
EXECUTE alterIfNotExists2;
DEALLOCATE PREPARE alterIfNotExists2;

SET @preparedStatement3 = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = @tablename
    AND COLUMN_NAME = @columnname3
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN `', @columnname3, '` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT ''VAT rate applied'' AFTER `', @columnname2, '`')
));
PREPARE alterIfNotExists3 FROM @preparedStatement3;
EXECUTE alterIfNotExists3;
DEALLOCATE PREPARE alterIfNotExists3;

SET @preparedStatement4 = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = @tablename
    AND COLUMN_NAME = @columnname4
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN `', @columnname4, '` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT ''VAT amount'' AFTER `', @columnname3, '`')
));
PREPARE alterIfNotExists4 FROM @preparedStatement4;
EXECUTE alterIfNotExists4;
DEALLOCATE PREPARE alterIfNotExists4;

SET @preparedStatement5 = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = @tablename
    AND COLUMN_NAME = @columnname5
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN `', @columnname5, '` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT ''Total amount including VAT'' AFTER `', @columnname4, '`')
));
PREPARE alterIfNotExists5 FROM @preparedStatement5;
EXECUTE alterIfNotExists5;
DEALLOCATE PREPARE alterIfNotExists5;

SET @preparedStatement6 = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = @tablename
    AND COLUMN_NAME = @columnname6
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN `', @columnname6, '` date DEFAULT NULL COMMENT ''Date of last payment'' AFTER `', @columnname5, '`')
));
PREPARE alterIfNotExists6 FROM @preparedStatement6;
EXECUTE alterIfNotExists6;
DEALLOCATE PREPARE alterIfNotExists6;

SET @preparedStatement7 = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = @tablename
    AND COLUMN_NAME = @columnname7
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN `', @columnname7, '` tinyint(1) NOT NULL DEFAULT 0 COMMENT ''Whether deposit is required'' AFTER `', @columnname6, '`')
));
PREPARE alterIfNotExists7 FROM @preparedStatement7;
EXECUTE alterIfNotExists7;
DEALLOCATE PREPARE alterIfNotExists7;

SET @preparedStatement8 = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = @tablename
    AND COLUMN_NAME = @columnname8
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN `', @columnname8, '` decimal(10,2) DEFAULT NULL COMMENT ''Required deposit amount'' AFTER `', @columnname7, '`')
));
PREPARE alterIfNotExists8 FROM @preparedStatement8;
EXECUTE alterIfNotExists8;
DEALLOCATE PREPARE alterIfNotExists8;

SET @preparedStatement9 = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = @tablename
    AND COLUMN_NAME = @columnname9
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN `', @columnname9, '` tinyint(1) NOT NULL DEFAULT 0 COMMENT ''Whether deposit has been paid'' AFTER `', @columnname8, '`')
));
PREPARE alterIfNotExists9 FROM @preparedStatement9;
EXECUTE alterIfNotExists9;
DEALLOCATE PREPARE alterIfNotExists9;

-- =====================================================
-- 5. UPDATE PAYMENTS TABLE - ADD MISSING COLUMNS
-- =====================================================

SET @tablename = 'payments';
SET @columnname1 = 'conference_id';
SET @columnname2 = 'payment_type';
SET @columnname3 = 'invoice_number';

-- Add conference_id column for linking to conference bookings
SET @preparedStatement1 = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = @tablename
    AND COLUMN_NAME = @columnname1
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN `', @columnname1, '` int UNSIGNED DEFAULT NULL COMMENT ''Optional link to conference_inquiries table for conference-specific payments'' AFTER `booking_id`')
));
PREPARE alterIfNotExists1 FROM @preparedStatement1;
EXECUTE alterIfNotExists1;
DEALLOCATE PREPARE alterIfNotExists1;

-- Add payment_type column to distinguish payment types
SET @preparedStatement2 = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = @tablename
    AND COLUMN_NAME = @columnname2
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN `', @columnname2, '` enum(''deposit'',''full_payment'',''partial_payment'',''refund'',''adjustment'') DEFAULT NULL COMMENT ''Type of payment transaction'' AFTER `payment_method`')
));
PREPARE alterIfNotExists2 FROM @preparedStatement2;
EXECUTE alterIfNotExists2;
DEALLOCATE PREPARE alterIfNotExists2;

-- Add invoice_number column for invoice reference
SET @preparedStatement3 = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = @tablename
    AND COLUMN_NAME = @columnname3
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN `', @columnname3, '` varchar(50) DEFAULT NULL COMMENT ''Invoice number (e.g., INV-2026-000001)'' AFTER `invoice_generated`')
));
PREPARE alterIfNotExists3 FROM @preparedStatement3;
EXECUTE alterIfNotExists3;
DEALLOCATE PREPARE alterIfNotExists3;

-- Add amount column (additional field, coexists with payment_amount)
SET @columnname4 = 'amount';
SET @preparedStatement4 = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = @tablename
    AND COLUMN_NAME = @columnname4
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN `', @columnname4, '` decimal(10,2) DEFAULT 0.00 COMMENT ''Additional payment amount field - coexists with payment_amount'' AFTER `invoice_number`')
));
PREPARE alterIfNotExists4 FROM @preparedStatement4;
EXECUTE alterIfNotExists4;
DEALLOCATE PREPARE alterIfNotExists4;

-- Add status column (additional field, coexists with payment_status)
SET @columnname5 = 'status';
SET @preparedStatement5 = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = @tablename
    AND COLUMN_NAME = @columnname5
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN `', @columnname5, '` enum(''pending'',''completed'',''failed'',''refunded'') DEFAULT ''pending'' COMMENT ''Additional payment status field - coexists with payment_status'' AFTER `amount`')
));
PREPARE alterIfNotExists5 FROM @preparedStatement5;
EXECUTE alterIfNotExists5;
DEALLOCATE PREPARE alterIfNotExists5;

-- Add transaction_id column (additional field, coexists with payment_reference_number)
SET @columnname6 = 'transaction_id';
SET @preparedStatement6 = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = @tablename
    AND COLUMN_NAME = @columnname6
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN `', @columnname6, '` varchar(100) DEFAULT NULL COMMENT ''Additional transaction reference field - coexists with payment_reference_number'' AFTER `status`')
));
PREPARE alterIfNotExists6 FROM @preparedStatement6;
EXECUTE alterIfNotExists6;
DEALLOCATE PREPARE alterIfNotExists6;

-- Add index for conference_id if it doesn't exist
SET @indexname = 'idx_conference_id';
SET @preparedStatement4 = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = @tablename
    AND INDEX_NAME = @indexname
  ) > 0,
  'SELECT 1',
  CONCAT('CREATE INDEX `', @indexname, '` ON ', @tablename, ' (`', @columnname1, '`)')
));
PREPARE createIndexIfNotExists2 FROM @preparedStatement4;
EXECUTE createIndexIfNotExists2;
DEALLOCATE PREPARE createIndexIfNotExists2;

-- =====================================================
-- 6. CREATE MIGRATION_LOG TABLE
-- =====================================================

CREATE TABLE IF NOT EXISTS `migration_log` (
  `migration_id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration_name` varchar(100) NOT NULL COMMENT 'Unique name of the migration',
  `migration_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'When the migration was run',
  `status` enum('pending','in_progress','completed','failed') NOT NULL DEFAULT 'pending' COMMENT 'Migration status',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`migration_id`),
  UNIQUE KEY `idx_migration_name` (`migration_name`),
  KEY `idx_migration_date` (`migration_date`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Log of database migrations';

-- =====================================================
-- 6. CREATE MIGRATION LOG ENTRY
-- =====================================================

INSERT INTO `migration_log` (`migration_name`, `migration_date`, `status`)
VALUES ('payments_accounting_system', NOW(), 'completed')
ON DUPLICATE KEY UPDATE 
  `migration_date` = NOW(),
  `status` = 'completed';

-- Commit transaction
COMMIT;

-- =====================================================
-- VERIFICATION QUERIES
-- =====================================================

-- Verify payments table created
-- SELECT COUNT(*) as payments_table_exists FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payments';

-- Verify new columns in bookings table
-- SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'bookings' AND COLUMN_NAME IN ('amount_paid', 'amount_due', 'vat_rate', 'vat_amount', 'total_with_vat', 'last_payment_date');

-- Verify new columns in conference_inquiries table
-- SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'conference_inquiries' AND COLUMN_NAME IN ('amount_paid', 'amount_due', 'vat_rate', 'vat_amount', 'total_with_vat', 'last_payment_date', 'deposit_required', 'deposit_amount', 'deposit_paid');

-- Verify new columns in payments table
-- SELECT COLUMN_NAME, DATA_TYPE, COLUMN_DEFAULT, IS_NULLABLE, COLUMN_COMMENT FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payments' AND COLUMN_NAME IN ('conference_id', 'payment_type', 'invoice_number', 'amount', 'status', 'transaction_id') ORDER BY ORDINAL_POSITION;

-- Verify VAT settings added
-- SELECT setting_key, setting_value FROM site_settings WHERE setting_group = 'accounting';
