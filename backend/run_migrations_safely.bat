@echo off
echo 🔧 Running migration fix and migrations safely...
echo.

echo 📋 Step 1: Cleaning up any problematic migration entries...
php fix_migration_issue.php
echo.

echo 📋 Step 2: Running migrations...
php artisan migrate
echo.

echo 📋 Step 3: Checking migration status...
php artisan migrate:status
echo.

echo ✅ Migration process complete!
pause