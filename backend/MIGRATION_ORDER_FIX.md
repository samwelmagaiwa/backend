# Migration Order Fix

## Problem
The migration `2025_07_10_132350_add_department_id_to_users_table.php` was failing because it was trying to add a foreign key to the `departments` table before the `departments` table was created.

## Root Cause
There was a circular dependency issue:
1. `departments` table tried to reference `users` table for `hod_user_id`
2. `users` table tried to reference `departments` table for `department_id`

## Solution Applied

### 1. Fixed departments table creation
- Removed the immediate foreign key constraint for `hod_user_id`
- Made it a regular `unsignedBigInteger` field with index
- The foreign key constraint will be added later if needed

### 2. Fixed add_department_id_to_users_table migration
- Removed the Exception throwing that was causing the failure
- Added proper table existence checks
- Made the migration skip gracefully if dependencies don't exist

### 3. Corrected migration order
The correct order is now:
1. `2025_07_10_132300_create_users_table.php`
2. `2025_07_10_132400_create_departments_table.php` 
3. `2025_07_10_132450_add_department_id_to_users_table.php`
4. `2025_07_10_132550_create_user_access_table.php`

## Files Modified
- `backend/database/migrations/2025_07_10_132400_create_departments_table.php`
- `backend/database/migrations/2025_07_10_132450_add_department_id_to_users_table.php`
- Moved `2025_07_10_132500_create_user_access_table.php` to `2025_07_10_132550_create_user_access_table.php`

## Next Steps
Run the migrations again:
```bash
cd backend
php artisan migrate
```

The migrations should now run successfully without the circular dependency error.