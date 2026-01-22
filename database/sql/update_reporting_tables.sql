-- ============================================================================
-- COMPREHENSIVE SQL UPDATES FOR REPORTING TABLES
-- Safe updates that preserve existing data
-- ============================================================================

-- ============================================================================
-- 1. APPLICANTS TABLE - Add reporting-friendly fields
-- ============================================================================

-- Add registration_date if not exists (for date-based reports)
SET @col_exists = (
    SELECT COUNT(*) 
    FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'applicants' 
    AND COLUMN_NAME = 'registration_date'
);

SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `applicants` ADD COLUMN `registration_date` DATE NULL AFTER `created_at`',
    'SELECT "Column registration_date already exists in applicants" AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Update registration_date from created_at for existing records
UPDATE `applicants` 
SET `registration_date` = DATE(`created_at`) 
WHERE `registration_date` IS NULL AND `created_at` IS NOT NULL;


-- Add status field if not exists (active, inactive, graduated, dropped)
SET @col_exists = (
    SELECT COUNT(*) 
    FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'applicants' 
    AND COLUMN_NAME = 'status'
);

SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `applicants` ADD COLUMN `status` VARCHAR(50) DEFAULT ''active'' AFTER `education_level`',
    'SELECT "Column status already exists in applicants" AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- Add index for better reporting performance
SET @idx_exists = (
    SELECT COUNT(*) 
    FROM information_schema.STATISTICS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'applicants' 
    AND INDEX_NAME = 'applicants_branch_status_index'
);

SET @sql = IF(@idx_exists = 0, 
    'ALTER TABLE `applicants` ADD INDEX `applicants_branch_status_index` (`branch_id`, `status`)',
    'SELECT "Index applicants_branch_status_index already exists" AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- ============================================================================
-- 2. ENROLLMENTS TABLE - Add reporting fields
-- ============================================================================

-- Add enrollment_status if not exists
SET @col_exists = (
    SELECT COUNT(*) 
    FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'enrollments' 
    AND COLUMN_NAME = 'enrollment_status'
);

SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `enrollments` ADD COLUMN `enrollment_status` VARCHAR(50) DEFAULT ''active'' AFTER `end_date`',
    'SELECT "Column enrollment_status already exists in enrollments" AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- Add completion_date if not exists
SET @col_exists = (
    SELECT COUNT(*) 
    FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'enrollments' 
    AND COLUMN_NAME = 'completion_date'
);

SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `enrollments` ADD COLUMN `completion_date` DATE NULL AFTER `end_date`',
    'SELECT "Column completion_date already exists in enrollments" AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- Add attendance_percentage if not exists
SET @col_exists = (
    SELECT COUNT(*) 
    FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'enrollments' 
    AND COLUMN_NAME = 'attendance_percentage'
);

SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `enrollments` ADD COLUMN `attendance_percentage` DECIMAL(5,2) DEFAULT 0.00 AFTER `enrollment_status`',
    'SELECT "Column attendance_percentage already exists in enrollments" AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- Add index for course and venue reporting
SET @idx_exists = (
    SELECT COUNT(*) 
    FROM information_schema.STATISTICS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'enrollments' 
    AND INDEX_NAME = 'enrollments_course_venue_index'
);

SET @sql = IF(@idx_exists = 0, 
    'ALTER TABLE `enrollments` ADD INDEX `enrollments_course_venue_index` (`course_id`, `venue_id`)',
    'SELECT "Index enrollments_course_venue_index already exists" AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- ============================================================================
-- 3. INVOICES TABLE - Add financial reporting fields
-- ============================================================================

-- Add total_amount if not exists (for easier reporting)
SET @col_exists = (
    SELECT COUNT(*) 
    FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'invoices' 
    AND COLUMN_NAME = 'total_amount'
);

SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `invoices` ADD COLUMN `total_amount` DECIMAL(12,2) DEFAULT 0.00 AFTER `balance`',
    'SELECT "Column total_amount already exists in invoices" AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Calculate total_amount from balance + paid_amount for existing records
UPDATE `invoices` 
SET `total_amount` = COALESCE(`balance`, 0) + COALESCE(`paid_amount`, 0) 
WHERE `total_amount` = 0 OR `total_amount` IS NULL;


-- Add payment_method if not exists
SET @col_exists = (
    SELECT COUNT(*) 
    FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'invoices' 
    AND COLUMN_NAME = 'payment_method'
);

SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `invoices` ADD COLUMN `payment_method` VARCHAR(50) NULL AFTER `status`',
    'SELECT "Column payment_method already exists in invoices" AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- Add due_date if not exists
SET @col_exists = (
    SELECT COUNT(*) 
    FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'invoices' 
    AND COLUMN_NAME = 'due_date'
);

SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `invoices` ADD COLUMN `due_date` DATE NULL AFTER `date`',
    'SELECT "Column due_date already exists in invoices" AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- Add index for status and date reporting
SET @idx_exists = (
    SELECT COUNT(*) 
    FROM information_schema.STATISTICS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'invoices' 
    AND INDEX_NAME = 'invoices_status_date_index'
);

SET @sql = IF(@idx_exists = 0, 
    'ALTER TABLE `invoices` ADD INDEX `invoices_status_date_index` (`status`, `date`)',
    'SELECT "Index invoices_status_date_index already exists" AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- ============================================================================
-- 4. PAYMENTS TABLE - Add payment tracking fields
-- ============================================================================

-- Add payment_method if not exists
SET @col_exists = (
    SELECT COUNT(*) 
    FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'payments' 
    AND COLUMN_NAME = 'payment_method'
);

SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `payments` ADD COLUMN `payment_method` VARCHAR(50) DEFAULT ''cash'' AFTER `amount`',
    'SELECT "Column payment_method already exists in payments" AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- Add transaction_reference if not exists
SET @col_exists = (
    SELECT COUNT(*) 
    FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'payments' 
    AND COLUMN_NAME = 'transaction_reference'
);

SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `payments` ADD COLUMN `transaction_reference` VARCHAR(100) NULL AFTER `payment_method`',
    'SELECT "Column transaction_reference already exists in payments" AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- Add payment_status if not exists
SET @col_exists = (
    SELECT COUNT(*) 
    FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'payments' 
    AND COLUMN_NAME = 'payment_status'
);

SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `payments` ADD COLUMN `payment_status` VARCHAR(50) DEFAULT ''completed'' AFTER `transaction_reference`',
    'SELECT "Column payment_status already exists in payments" AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- Add index for date-based reporting
SET @idx_exists = (
    SELECT COUNT(*) 
    FROM information_schema.STATISTICS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'payments' 
    AND INDEX_NAME = 'payments_date_method_index'
);

SET @sql = IF(@idx_exists = 0, 
    'ALTER TABLE `payments` ADD INDEX `payments_date_method_index` (`date`, `payment_method`)',
    'SELECT "Index payments_date_method_index already exists" AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- ============================================================================
-- 5. COURSES TABLE - Add course analytics fields
-- ============================================================================

-- Add is_active if not exists
SET @col_exists = (
    SELECT COUNT(*) 
    FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'courses' 
    AND COLUMN_NAME = 'is_active'
);

SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `courses` ADD COLUMN `is_active` BOOLEAN DEFAULT TRUE AFTER `name`',
    'SELECT "Column is_active already exists in courses" AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- Add category if not exists
SET @col_exists = (
    SELECT COUNT(*) 
    FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'courses' 
    AND COLUMN_NAME = 'category'
);

SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `courses` ADD COLUMN `category` VARCHAR(100) NULL AFTER `name`',
    'SELECT "Column category already exists in courses" AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- ============================================================================
-- 6. BRANCHES TABLE - Add branch analytics fields
-- ============================================================================

-- Add is_active if not exists
SET @col_exists = (
    SELECT COUNT(*) 
    FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'branches' 
    AND COLUMN_NAME = 'is_active'
);

SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `branches` ADD COLUMN `is_active` BOOLEAN DEFAULT TRUE AFTER `name`',
    'SELECT "Column is_active already exists in branches" AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- Add region if not exists
SET @col_exists = (
    SELECT COUNT(*) 
    FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'branches' 
    AND COLUMN_NAME = 'region'
);

SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `branches` ADD COLUMN `region` VARCHAR(100) NULL AFTER `name`',
    'SELECT "Column region already exists in branches" AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- ============================================================================
-- VERIFICATION QUERIES
-- Run these to verify all changes were applied correctly
-- ============================================================================

-- Check applicants table
SELECT 'APPLICANTS TABLE' AS table_name;
SELECT 
    COLUMN_NAME,
    DATA_TYPE,
    IS_NULLABLE,
    COLUMN_DEFAULT
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME = 'applicants'
AND COLUMN_NAME IN ('registration_date', 'status')
ORDER BY ORDINAL_POSITION;

-- Check enrollments table
SELECT 'ENROLLMENTS TABLE' AS table_name;
SELECT 
    COLUMN_NAME,
    DATA_TYPE,
    IS_NULLABLE,
    COLUMN_DEFAULT
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME = 'enrollments'
AND COLUMN_NAME IN ('enrollment_status', 'completion_date', 'attendance_percentage')
ORDER BY ORDINAL_POSITION;

-- Check invoices table
SELECT 'INVOICES TABLE' AS table_name;
SELECT 
    COLUMN_NAME,
    DATA_TYPE,
    IS_NULLABLE,
    COLUMN_DEFAULT
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME = 'invoices'
AND COLUMN_NAME IN ('total_amount', 'payment_method', 'due_date')
ORDER BY ORDINAL_POSITION;

-- Check payments table
SELECT 'PAYMENTS TABLE' AS table_name;
SELECT 
    COLUMN_NAME,
    DATA_TYPE,
    IS_NULLABLE,
    COLUMN_DEFAULT
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME = 'payments'
AND COLUMN_NAME IN ('payment_method', 'transaction_reference', 'payment_status')
ORDER BY ORDINAL_POSITION;

-- Check courses table
SELECT 'COURSES TABLE' AS table_name;
SELECT 
    COLUMN_NAME,
    DATA_TYPE,
    IS_NULLABLE,
    COLUMN_DEFAULT
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME = 'courses'
AND COLUMN_NAME IN ('is_active', 'category')
ORDER BY ORDINAL_POSITION;

-- Check branches table
SELECT 'BRANCHES TABLE' AS table_name;
SELECT 
    COLUMN_NAME,
    DATA_TYPE,
    IS_NULLABLE,
    COLUMN_DEFAULT
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME = 'branches'
AND COLUMN_NAME IN ('is_active', 'region')
ORDER BY ORDINAL_POSITION;

-- Check all indexes
SELECT 
    TABLE_NAME,
    INDEX_NAME,
    GROUP_CONCAT(COLUMN_NAME ORDER BY SEQ_IN_INDEX) AS columns
FROM information_schema.STATISTICS
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME IN ('applicants', 'enrollments', 'invoices', 'payments')
AND INDEX_NAME LIKE '%_index'
GROUP BY TABLE_NAME, INDEX_NAME
ORDER BY TABLE_NAME, INDEX_NAME;


-- ============================================================================
-- DATA INTEGRITY CHECKS
-- ============================================================================

-- Check for NULL values in critical fields
SELECT 'Data Integrity Check' AS check_type;

SELECT 
    'Applicants with NULL registration_date' AS issue,
    COUNT(*) AS count
FROM applicants
WHERE registration_date IS NULL;

SELECT 
    'Invoices with NULL total_amount' AS issue,
    COUNT(*) AS count
FROM invoices
WHERE total_amount IS NULL OR total_amount = 0;

SELECT 
    'Enrollments with NULL status' AS issue,
    COUNT(*) AS count
FROM enrollments
WHERE enrollment_status IS NULL;


-- ============================================================================
-- SUMMARY STATISTICS
-- ============================================================================

SELECT 'Summary Statistics' AS report_type;

-- Applicants by status
SELECT 
    'Applicants by Status' AS metric,
    status,
    COUNT(*) AS count
FROM applicants
GROUP BY status;

-- Enrollments by status
SELECT 
    'Enrollments by Status' AS metric,
    enrollment_status,
    COUNT(*) AS count
FROM enrollments
GROUP BY enrollment_status;

-- Payments by method
SELECT 
    'Payments by Method' AS metric,
    payment_method,
    COUNT(*) AS count,
    SUM(amount) AS total_amount
FROM payments
GROUP BY payment_method;

-- Invoices by status
SELECT 
    'Invoices by Status' AS metric,
    status,
    COUNT(*) AS count,
    SUM(total_amount) AS total_amount,
    SUM(paid_amount) AS paid_amount,
    SUM(balance) AS balance
FROM invoices
GROUP BY status;
