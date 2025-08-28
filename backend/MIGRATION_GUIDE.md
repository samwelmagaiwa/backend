# Migration Guide - Fix for Column Not Found Error

## Problem
The migration failed because it was trying to add `is_active` column after `staff_name`, but `staff_name` column doesn't exist in the users table.

## Solution
I've created a comprehensive migration that safely adds both missing columns.

## Steps to Fix

### 1. Check Current Migration Status
```bash
php artisan migrate:status
```

### 2. If the problematic migration is pending, run:
```bash
php artisan migrate
```

### 3. If the migration already failed and is marked as run, you may need to:
```bash
# Option A: Rollback the last batch and re-run
php artisan migrate:rollback --step=1
php artisan migrate

# Option B: If that doesn't work, reset and fresh migrate
php artisan migrate:fresh --seed
```

## What the Fix Does

The new migration `2025_01_27_250000_fix_users_table_structure.php` safely:

1. **Checks if `staff_name` column exists** before adding it
2. **Checks if `is_active` column exists** before adding it  
3. **Adds proper indexes** for performance
4. **Handles rollback safely**

## Current Users Table Structure

After successful migration, your users table will have:
- `id` (primary key)
- `role_id` (foreign key to roles)
- `name`
- `pf_number` (unique)
- `staff_name` (nullable) ← **NEW**
- `phone` (unique)
- `is_active` (boolean, default true) ← **NEW**
- `email` (unique)
- `email_verified_at` (nullable)
- `password`
- `remember_token`
- `created_at`
- `updated_at`

## Verification

After running migrations, verify the structure:
```bash
# Check if columns exist
php artisan tinker
>>> Schema::hasColumn('users', 'staff_name')
>>> Schema::hasColumn('users', 'is_active')
>>> exit
```

## Test the System

After successful migration:
```bash
# Test the role system
curl http://localhost:8000/api/test/role-system

# Test user endpoints (requires authentication)
curl -H "Authorization: Bearer YOUR_TOKEN" http://localhost:8000/api/admin/user-management/roles
```

## If You Still Have Issues

1. **Check Laravel logs**: `storage/logs/laravel.log`
2. **Check database directly**:
   ```sql
   DESCRIBE users;
   ```
3. **Manual column addition** (if needed):
   ```sql
   ALTER TABLE users ADD COLUMN staff_name VARCHAR(255) NULL AFTER pf_number;
   ALTER TABLE users ADD COLUMN is_active TINYINT(1) NOT NULL DEFAULT 1 AFTER staff_name;
   ALTER TABLE users ADD INDEX idx_is_active (is_active);
   ```

## Next Steps

Once migrations are successful:
1. Run the role seeder: `php artisan db:seed --class=RoleManagementSeeder`
2. Test the API endpoints
3. Integrate with your frontend

## Migration Files Order

The migrations should run in this order:
1. `2025_07_10_132300_create_users_table.php` (original)
2. `2025_07_14_125826_add_pf_number_to_users_table.php` (existing)
3. `2025_01_27_210000_create_role_management_tables.php` (role system)
4. `2025_01_27_250000_fix_users_table_structure.php` (our fix)
5. `2025_01_27_240000_ensure_all_users_have_roles.php` (role migration)

This should resolve the "Column not found" error and get your system working properly.