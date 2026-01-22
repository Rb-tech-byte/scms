-- ============================================================================
-- SAFE SQL UPDATES FOR REPORTING TABLES - MySQL 5.7 Compatible
-- Add these to your scms.sql file or run separately in phpMyAdmin
-- ============================================================================

-- ============================================================================
-- 1. UPDATE PAYMENT_SCHEDULES TABLE (if exists)
-- ============================================================================

-- Check and add installment_number
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

-- Check and add payment_id
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

-- Add index for payment_schedules
SET @idx_exists = (
    SELECT COUNT(*) 
    FROM information_schema.STATISTICS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'payment_schedules' 
    AND INDEX_NAME = 'payment_schedules_status_due_date_index'
);

SET @sql = IF(@idx_exists = 0, 
    'ALTER TABLE `payment_schedules` ADD INDEX `payment_schedules_status_due_date_index` (`status`, `due_date`)',
    'SELECT "Index payment_schedules_status_due_date_index already exists" AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add foreign key for payment_id
SET @fk_exists = (
    SELECT COUNT(*) 
    FROM information_schema.KEY_COLUMN_USAGE 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'payment_schedules' 
    AND CONSTRAINT_NAME = 'payment_schedules_payment_id_foreign'
);

SET @sql = IF(@fk_exists = 0, 
    'ALTER TABLE `payment_schedules` ADD CONSTRAINT `payment_schedules_payment_id_foreign` FOREIGN KEY (`payment_id`) REFERENCES `payments`(`id`) ON DELETE SET NULL',
    'SELECT "Foreign key payment_schedules_payment_id_foreign already exists" AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- ============================================================================
-- 2. UPDATE APPLICANTS TABLE
-- ============================================================================

-- Add registration_date
SET @col_exists = (
    SELECT COUNT(*) 
    FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'applicants' 
    AND COLUMN_NAME = 'registration_date'
);

SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `applicants` ADD COLUMN `registration_date` DATE NULL AFTER `created_at`',
    'SELECT "Column registration_date already exists" AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add status
SET @col_exists = (
    SELECT COUNT(*) 
    FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'applicants' 
    AND COLUMN_NAME = 'status'
);

SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `applicants` ADD COLUMN `status` VARCHAR(50) DEFAULT ''active'' AFTER `education_level`',
    'SELECT "Column status already exists" AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add index for applicants
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

-- Populate registration_date from created_at
UPDATE `applicants` 
SET `registration_date` = DATE(`created_at`) 
WHERE `registration_date` IS NULL AND `created_at` IS NOT NULL;


-- ============================================================================
-- 3. UPDATE ENROLLMENTS TABLE
-- ============================================================================

-- Add enrollment_status
SET @col_exists = (
    SELECT COUNT(*) 
    FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'enrollments' 
    AND COLUMN_NAME = 'enrollment_status'
);

SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `enrollments` ADD COLUMN `enrollment_status` VARCHAR(50) DEFAULT ''active'' AFTER `end_date`',
    'SELECT "Column enrollment_status already exists" AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add completion_date
SET @col_exists = (
    SELECT COUNT(*) 
    FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'enrollments' 
    AND COLUMN_NAME = 'completion_date'
);

SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `enrollments` ADD COLUMN `completion_date` DATE NULL AFTER `enrollment_status`',
    'SELECT "Column completion_date already exists" AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add attendance_percentage
SET @col_exists = (
    SELECT COUNT(*) 
    FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'enrollments' 
    AND COLUMN_NAME = 'attendance_percentage'
);

SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `enrollments` ADD COLUMN `attendance_percentage` DECIMAL(5,2) DEFAULT 0.00 AFTER `completion_date`',
    'SELECT "Column attendance_percentage already exists" AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add index for enrollments
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
-- 4. UPDATE INVOICES TABLE
-- ============================================================================

-- Add total_amount
SET @col_exists = (
    SELECT COUNT(*) 
    FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'invoices' 
    AND COLUMN_NAME = 'total_amount'
);

SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `invoices` ADD COLUMN `total_amount` DECIMAL(12,2) DEFAULT 0.00 AFTER `balance`',
    'SELECT "Column total_amount already exists" AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add payment_method
SET @col_exists = (
    SELECT COUNT(*) 
    FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'invoices' 
    AND COLUMN_NAME = 'payment_method'
);

SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `invoices` ADD COLUMN `payment_method` VARCHAR(50) NULL AFTER `status`',
    'SELECT "Column payment_method already exists" AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add due_date
SET @col_exists = (
    SELECT COUNT(*) 
    FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'invoices' 
    AND COLUMN_NAME = 'due_date'
);

SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `invoices` ADD COLUMN `due_date` DATE NULL AFTER `date`',
    'SELECT "Column due_date already exists" AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add index for invoices
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

-- Calculate total_amount from balance + paid_amount
UPDATE `invoices` 
SET `total_amount` = COALESCE(`balance`, 0) + COALESCE(`paid_amount`, 0) 
WHERE `total_amount` = 0 OR `total_amount` IS NULL;


-- ============================================================================
-- 5. UPDATE PAYMENTS TABLE
-- ============================================================================

-- Add payment_method
SET @col_exists = (
    SELECT COUNT(*) 
    FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'payments' 
    AND COLUMN_NAME = 'payment_method'
);

SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `payments` ADD COLUMN `payment_method` VARCHAR(50) DEFAULT ''cash'' AFTER `amount`',
    'SELECT "Column payment_method already exists" AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add transaction_reference
SET @col_exists = (
    SELECT COUNT(*) 
    FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'payments' 
    AND COLUMN_NAME = 'transaction_reference'
);

SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `payments` ADD COLUMN `transaction_reference` VARCHAR(100) NULL AFTER `payment_method`',
    'SELECT "Column transaction_reference already exists" AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add payment_status
SET @col_exists = (
    SELECT COUNT(*) 
    FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'payments' 
    AND COLUMN_NAME = 'payment_status'
);

SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `payments` ADD COLUMN `payment_status` VARCHAR(50) DEFAULT ''completed'' AFTER `transaction_reference`',
    'SELECT "Column payment_status already exists" AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add index for payments
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
-- 6. UPDATE COURSES TABLE
-- ============================================================================

-- Add is_active
SET @col_exists = (
    SELECT COUNT(*) 
    FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'courses' 
    AND COLUMN_NAME = 'is_active'
);

SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `courses` ADD COLUMN `is_active` TINYINT(1) DEFAULT 1 AFTER `title`',
    'SELECT "Column is_active already exists" AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add category
SET @col_exists = (
    SELECT COUNT(*) 
    FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'courses' 
    AND COLUMN_NAME = 'category'
);

SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `courses` ADD COLUMN `category` VARCHAR(100) NULL AFTER `is_active`',
    'SELECT "Column category already exists" AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- ============================================================================
-- 7. UPDATE BRANCHES TABLE
-- ============================================================================

-- Add is_active
SET @col_exists = (
    SELECT COUNT(*) 
    FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'branches' 
    AND COLUMN_NAME = 'is_active'
);

SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `branches` ADD COLUMN `is_active` TINYINT(1) DEFAULT 1 AFTER `name`',
    'SELECT "Column is_active already exists" AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add region
SET @col_exists = (
    SELECT COUNT(*) 
    FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'branches' 
    AND COLUMN_NAME = 'region'
);

SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `branches` ADD COLUMN `region` VARCHAR(100) NULL AFTER `is_active`',
    'SELECT "Column region already exists" AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- ============================================================================
-- VERIFICATION - Check if updates were successful
-- ============================================================================

SELECT 'Database Update Complete!' AS status;

-- Check applicants
SELECT 'Applicants Table' AS table_name, COUNT(*) as total_records, 
       SUM(CASE WHEN status IS NOT NULL THEN 1 ELSE 0 END) as records_with_status,
       SUM(CASE WHEN registration_date IS NOT NULL THEN 1 ELSE 0 END) as records_with_date
FROM applicants;

-- Check enrollments  
SELECT 'Enrollments Table' AS table_name, COUNT(*) as total_records,
       SUM(CASE WHEN enrollment_status IS NOT NULL THEN 1 ELSE 0 END) as records_with_status
FROM enrollments;

-- Check invoices
SELECT 'Invoices Table' AS table_name, COUNT(*) as total_records,
       SUM(CASE WHEN total_amount > 0 THEN 1 ELSE 0 END) as records_with_total
FROM invoices;

-- Check payments
SELECT 'Payments Table' AS table_name, COUNT(*) as total_records,
       SUM(CASE WHEN payment_method IS NOT NULL THEN 1 ELSE 0 END) as records_with_method
FROM payments;

-- Check courses
SELECT 'Courses Table' AS table_name, COUNT(*) as total_records,
       SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_courses
FROM courses;

-- Check branches
SELECT 'Branches Table' AS table_name, COUNT(*) as total_records,
       SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_branches
FROM branches;

SELECT '✓ All tables updated successfully!' AS final_status;
