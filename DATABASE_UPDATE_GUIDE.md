# 🔧 Safe Database Update Guide - No Data Loss

## ⚠️ IMPORTANT: Your Data is Safe!

You have **TWO OPTIONS** to update your database without losing any data:

---

## 🎯 **OPTION 1: Using Laravel Migration (Recommended)**

This is the safest and easiest method.

### Steps:

1. **Run the migration:**
   ```bash
   php artisan migrate
   ```

2. **That's it!** The migration will:
   - ✅ Check if columns already exist (won't duplicate)
   - ✅ Add `installment_number` column
   - ✅ Add `payment_id` column
   - ✅ Add foreign key constraint
   - ✅ Add performance indexes
   - ✅ Automatically number existing schedules sequentially
   - ✅ **Preserve ALL existing data**

### Migration File Location:
```
database/migrations/2026_01_23_000001_add_fields_to_payment_schedules_table.php
```

---

## 🎯 **OPTION 2: Manual SQL Execution**

If you prefer to run SQL directly in phpMyAdmin or MySQL client.

### Steps:

1. **Open the SQL file:**
   ```
   database/sql/update_payment_schedules_table.sql
   ```

2. **Copy the entire content**

3. **Execute in phpMyAdmin:**
   - Go to phpMyAdmin
   - Select your database
   - Click on "SQL" tab
   - Paste the SQL script
   - Click "Go"

4. **The script will:**
   - ✅ Check if columns exist before adding
   - ✅ Add new columns safely
   - ✅ Add foreign keys and indexes
   - ✅ Update existing records with installment numbers
   - ✅ Show verification queries
   - ✅ **Preserve ALL existing data**

---

## 📋 **What Gets Added?**

### New Columns:
1. **`installment_number`** (INT, NOT NULL, DEFAULT 1)
   - Automatically numbered for existing records (1, 2, 3...)
   - Grouped by invoice_id

2. **`payment_id`** (BIGINT UNSIGNED, NULLABLE)
   - Links schedule to payment when paid
   - NULL for unpaid schedules

### New Indexes:
- Composite index on (`status`, `due_date`) for faster queries

### New Foreign Keys:
- `payment_id` → `payments.id` (ON DELETE SET NULL)

---

## ✅ **Verification**

After running either option, verify the changes:

### Check Table Structure:
```sql
DESCRIBE payment_schedules;
```

### Check Your Data:
```sql
SELECT * FROM payment_schedules ORDER BY invoice_id, installment_number LIMIT 10;
```

You should see:
- All your existing records intact
- New `installment_number` column with values 1, 2, 3... per invoice
- New `payment_id` column (NULL for now)

---

## 🔄 **Rollback (If Needed)**

If something goes wrong, you can rollback:

### Using Migration:
```bash
php artisan migrate:rollback --step=1
```

### Using SQL:
The SQL file includes a rollback script at the bottom (commented out).

---

## 📊 **Before & After Comparison**

### BEFORE:
```
| id | invoice_id | due_date   | amount | status  |
|----|------------|------------|--------|---------|
| 1  | 100        | 2026-02-01 | 500.00 | pending |
| 2  | 100        | 2026-03-01 | 500.00 | pending |
| 3  | 101        | 2026-02-15 | 300.00 | paid    |
```

### AFTER:
```
| id | invoice_id | installment_number | due_date   | amount | status  | payment_id |
|----|------------|--------------------|------------|--------|---------|------------|
| 1  | 100        | 1                  | 2026-02-01 | 500.00 | pending | NULL       |
| 2  | 100        | 2                  | 2026-03-01 | 500.00 | pending | NULL       |
| 3  | 101        | 1                  | 2026-02-15 | 300.00 | paid    | NULL       |
```

**Notice:** All original data is preserved! ✅

---

## 🎓 **Recommended Approach**

**Use Option 1 (Laravel Migration)** because:
- ✅ Safer (built-in checks)
- ✅ Easier (one command)
- ✅ Trackable (migration history)
- ✅ Reversible (easy rollback)
- ✅ Tested (Laravel's migration system)

---

## 🆘 **Troubleshooting**

### "Column already exists" error?
**Solution:** The migration/SQL checks for this. If you get this error, the columns are already added. You're good to go!

### "Foreign key constraint fails"?
**Solution:** Make sure the `payments` table exists and has an `id` column.

### Data looks wrong?
**Solution:** Run the verification queries in the SQL file to check.

---

## 📞 **Need Help?**

If you encounter any issues:
1. Check the error message
2. Run the verification queries
3. Contact the development team
4. DO NOT run `migrate:fresh` - it will delete all data!

---

## ✨ **Summary**

- ✅ **No data will be lost**
- ✅ **Existing records are preserved**
- ✅ **New columns added safely**
- ✅ **Automatic numbering applied**
- ✅ **Fully reversible**
- ✅ **Production-safe**

**Choose your preferred option and proceed with confidence!** 🚀
