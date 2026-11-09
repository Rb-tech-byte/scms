# How to Add Updates to Your scms.sql File

## **Simple Steps**

### **Option 1: Add to Existing SQL Dump (Recommended)**

1. **Open your SQL dump file:**
   ```
   c:\wamp64\www\scms\scms.sql
   ```

2. **Scroll to the very end of the file** (after all the INSERT statements)

3. **Copy the contents from:**
   ```
   c:\wamp64\www\scms\database\sql\add_to_scms_dump.sql
   ```

4. **Paste it at the end of scms.sql** (before the final `/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;`)

5. **Save the file**

6. **Import the updated scms.sql:**
   - Open phpMyAdmin
   - Select your database
   - Click "Import"
   - Choose the updated `scms.sql` file
   - Click "Go"

---

### **Option 2: Run Separate SQL File**

If you don't want to modify scms.sql:

1. **First import your original scms.sql** (if not already imported)

2. **Then run the update script:**
   - Open phpMyAdmin
   - Select your database
   - Click "SQL" tab
   - Copy content from `database/sql/add_to_scms_dump.sql`
   - Paste and click "Go"

---

## **What Gets Added**

When you import, these fields will be added to your tables:

### **applicants**
- `registration_date` - Auto-filled from created_at
- `status` - Default: 'active'

### **enrollments**
- `enrollment_status` - Default: 'active'
- `completion_date`
- `attendance_percentage`

### **invoices**
- `total_amount` - Auto-calculated
- `payment_method`
- `due_date`

### **payments**
- `payment_method` - Default: 'cash'
- `transaction_reference`
- `payment_status` - Default: 'completed'

### **courses**
- `is_active` - Default: TRUE
- `category`

### **branches**
- `is_active` - Default: TRUE
- `region`

### **payment_schedules** (if exists)
- `installment_number`
- `payment_id`

---

## **Safety Features**

-  Uses `ADD COLUMN IF NOT EXISTS` - Won't duplicate columns
-  Preserves all existing data
-  Auto-populates default values
-  Adds performance indexes
-  Includes verification queries

---

## **Quick Method**

**Just want to update quickly?**

1. Open `scms.sql` in a text editor
2. Go to the end of the file
3. Add this line before the last line:
   ```sql
   SOURCE c:/wamp64/www/scms/database/sql/add_to_scms_dump.sql;
   ```
4. Import `scms.sql`

---

## **Verification**

After import, you'll see messages like:
```
Applicants Table Updated - X records
Enrollments Table Updated - X records
Invoices Table Updated - X records
All tables updated successfully!
```

---

## **File Locations**

```
c:\wamp64\www\scms\
├── scms.sql                                    ← Your main database dump
└── database\sql\
    ├── add_to_scms_dump.sql                   ← Add this to scms.sql 
    ├── update_payment_schedules_table.sql     ← Alternative method
    └── update_reporting_tables.sql            ← Alternative method
```

---

## **Pro Tip**

**For a complete fresh import:**

1. Backup your current database first!
2. Drop the existing database
3. Create a new database
4. Import the updated `scms.sql` file

---

## ️ **Important Notes**

-  **Safe to run multiple times** - Won't duplicate columns
-  **No data loss** - All existing data preserved
-  **Auto-population** - New fields get default values
-  **Reversible** - Can be rolled back if needed

---

## **Troubleshooting**

### "Column already exists" error?
**Solution:** This is normal! The script checks before adding. Your database is already updated.

### Import fails?
**Solution:** Make sure you're using MySQL 5.7+ or MariaDB 10.2+

### Want to verify?
**Solution:** Run this query after import:
```sql
SHOW COLUMNS FROM applicants LIKE 'status';
SHOW COLUMNS FROM invoices LIKE 'total_amount';
```

---

## **You're Done!**

After importing, your database will have all the new reporting fields ready to use!

**Next:** Start using the enhanced reporting features in your application.
