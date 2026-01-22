-- ============================================================================
-- SAFE SQL UPDATE FOR PAYMENT_SCHEDULES TABLE
-- This script adds new columns without losing existing data
-- ============================================================================

-- Step 1: Add installment_number column (if it doesn't exist)
-- ============================================================================
SET @col_exists = (
    SELECT COUNT(*) 
    FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'payment_schedules' 
    AND COLUMN_NAME = 'installment_number'
);

SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `payment_schedules` ADD COLUMN `installment_number` INT NOT NULL DEFAULT 1 AFTER `invoice_id`',
    'SELECT "Column installment_number already exists" AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- Step 2: Add payment_id column (if it doesn't exist)
-- ============================================================================
SET @col_exists = (
    SELECT COUNT(*) 
    FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'payment_schedules' 
    AND COLUMN_NAME = 'payment_id'
);

SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `payment_schedules` ADD COLUMN `payment_id` BIGINT UNSIGNED NULL AFTER `paid_at`',
    'SELECT "Column payment_id already exists" AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- Step 3: Add foreign key constraint (if it doesn't exist)
-- ============================================================================
SET @fk_exists = (
    SELECT COUNT(*) 
    FROM information_schema.KEY_COLUMN_USAGE 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'payment_schedules' 
    AND CONSTRAINT_NAME = 'payment_schedules_payment_id_foreign'
);

SET @sql = IF(@fk_exists = 0, 
    'ALTER TABLE `payment_schedules` 
     ADD CONSTRAINT `payment_schedules_payment_id_foreign` 
     FOREIGN KEY (`payment_id`) REFERENCES `payments`(`id`) ON DELETE SET NULL',
    'SELECT "Foreign key payment_schedules_payment_id_foreign already exists" AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- Step 4: Add composite index for performance (if it doesn't exist)
-- ============================================================================
SET @idx_exists = (
    SELECT COUNT(*) 
    FROM information_schema.STATISTICS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'payment_schedules' 
    AND INDEX_NAME = 'payment_schedules_status_due_date_index'
);

SET @sql = IF(@idx_exists = 0, 
    'ALTER TABLE `payment_schedules` 
     ADD INDEX `payment_schedules_status_due_date_index` (`status`, `due_date`)',
    'SELECT "Index payment_schedules_status_due_date_index already exists" AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- Step 5: Update existing records with sequential installment numbers
-- ============================================================================
-- This uses a temporary table approach which is safer

-- Create temporary table to store the new installment numbers
DROP TEMPORARY TABLE IF EXISTS temp_installment_numbers;

CREATE TEMPORARY TABLE temp_installment_numbers AS
SELECT 
    id,
    @row_num := IF(@prev_invoice = invoice_id, @row_num + 1, 1) AS new_installment_number,
    @prev_invoice := invoice_id
FROM 
    payment_schedules,
    (SELECT @row_num := 0, @prev_invoice := NULL) AS vars
ORDER BY 
    invoice_id, due_date, id;

-- Update the main table with the new installment numbers
UPDATE payment_schedules ps
INNER JOIN temp_installment_numbers tin ON ps.id = tin.id
SET ps.installment_number = tin.new_installment_number;

-- Clean up
DROP TEMPORARY TABLE IF EXISTS temp_installment_numbers;


-- ============================================================================
-- VERIFICATION QUERIES
-- Run these to verify the changes were applied correctly
-- ============================================================================

-- Check if columns exist
SELECT 
    COLUMN_NAME,
    DATA_TYPE,
    IS_NULLABLE,
    COLUMN_DEFAULT
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME = 'payment_schedules'
AND COLUMN_NAME IN ('installment_number', 'payment_id')
ORDER BY ORDINAL_POSITION;

-- Check if foreign key exists
SELECT 
    CONSTRAINT_NAME,
    COLUMN_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME = 'payment_schedules'
AND CONSTRAINT_NAME = 'payment_schedules_payment_id_foreign';

-- Check if index exists
SELECT 
    INDEX_NAME,
    COLUMN_NAME,
    SEQ_IN_INDEX
FROM information_schema.STATISTICS
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME = 'payment_schedules'
AND INDEX_NAME = 'payment_schedules_status_due_date_index'
ORDER BY SEQ_IN_INDEX;

-- Verify installment numbers are set correctly
SELECT 
    invoice_id,
    id,
    installment_number,
    due_date,
    amount,
    status
FROM payment_schedules
ORDER BY invoice_id, installment_number
LIMIT 20;

-- Count records per invoice to verify numbering
SELECT 
    invoice_id,
    COUNT(*) as total_schedules,
    MIN(installment_number) as min_number,
    MAX(installment_number) as max_number
FROM payment_schedules
GROUP BY invoice_id
ORDER BY invoice_id
LIMIT 10;


-- ============================================================================
-- ROLLBACK SCRIPT (Use only if you need to undo the changes)
-- ============================================================================
/*
-- Remove foreign key
ALTER TABLE `payment_schedules` DROP FOREIGN KEY `payment_schedules_payment_id_foreign`;

-- Remove index
ALTER TABLE `payment_schedules` DROP INDEX `payment_schedules_status_due_date_index`;

-- Remove columns
ALTER TABLE `payment_schedules` DROP COLUMN `payment_id`;
ALTER TABLE `payment_schedules` DROP COLUMN `installment_number`;
*/
