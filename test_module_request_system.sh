#!/bin/bash

echo "🚀 TESTING MODULE REQUEST SYSTEM"
echo "================================="
echo ""

# Change to backend directory
cd backend

echo "1. Running migrations..."
php artisan migrate --force

echo ""
echo "2. Checking if tables exist..."
php artisan tinker --execute="
echo Schema::hasTable('wellsoft_modules') ? '✅ wellsoft_modules table exists' : '❌ wellsoft_modules table missing';
echo \"\n\";
echo Schema::hasTable('wellsoft_modules_selected') ? '✅ wellsoft_modules_selected table exists' : '❌ wellsoft_modules_selected table missing';
echo \"\n\";
echo 'Module count: ' . App\Models\WellsoftModule::count();
echo \"\n\";
"

echo ""
echo "3. Testing API routes..."
php artisan route:list --name=module-requests

echo ""
echo "4. Starting Laravel development server..."
echo "You can now test the API at http://localhost:8000"
echo ""
echo "Available endpoints:"
echo "- GET  http://localhost:8000/api/module-requests/modules"
echo "- POST http://localhost:8000/api/module-requests"
echo "- GET  http://localhost:8000/api/module-requests/{userAccessId}"
echo "- PUT  http://localhost:8000/api/module-requests/{userAccessId}"
echo ""
echo "Frontend test page will be available at:"
echo "http://localhost:8080/module-request-test"
echo ""

# Start the Laravel server
php artisan serve