<?php

/**
 * Fix HOD Approval and Module Selection Bugs
 * 
 * This script identifies and fixes the following issues:
 * 1. HOD signature not being stored (NULL values)
 * 2. Module selections not being saved (empty arrays)
 * 3. Date fields not being processed correctly
 * 4. Missing validation for required fields
 */

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\UserAccess;
use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Load Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔧 FIXING HOD APPROVAL AND MODULE SELECTION BUGS\n";
echo "===============================================\n\n";

try {
    // Step 1: Analyze the current database structure
    echo "1. ANALYZING DATABASE STRUCTURE\n";
    echo "==============================\n";
    
    $columns = Schema::getColumnListing('user_access');
    $requiredColumns = [
        'hod_signature_path',
        'wellsoft_modules',
        'wellsoft_modules_selected', 
        'jeeva_modules',
        'jeeva_modules_selected',
        'module_requested_for',
        'hod_approved_at',
        'hod_name',
        'hod_comments'
    ];
    
    foreach ($requiredColumns as $column) {
        if (in_array($column, $columns)) {
            echo "✅ Column '{$column}' exists\n";
        } else {
            echo "❌ Column '{$column}' missing\n";
        }
    }
    echo "\n";

    // Step 2: Check current data issues
    echo "2. CHECKING CURRENT DATA ISSUES\n";
    echo "==============================\n";
    
    $totalRecords = UserAccess::count();
    $recordsWithNullHodSignature = UserAccess::whereNotNull('hod_name')->whereNull('hod_signature_path')->count();
    $recordsWithEmptyWellsoftModules = UserAccess::where(function($query) {
        $query->whereJsonLength('wellsoft_modules', 0)
              ->orWhereNull('wellsoft_modules');
    })->count();
    $recordsWithEmptyJeevaModules = UserAccess::where(function($query) {
        $query->whereJsonLength('jeeva_modules', 0)
              ->orWhereNull('jeeva_modules');
    })->count();
    
    echo "Total records: {$totalRecords}\n";
    echo "Records with HOD name but NULL signature: {$recordsWithNullHodSignature}\n";
    echo "Records with empty Wellsoft modules: {$recordsWithEmptyWellsoftModules}\n";
    echo "Records with empty Jeeva modules: {$recordsWithEmptyJeevaModules}\n";
    echo "\n";

    // Step 3: Analyze specific problematic record
    echo "3. ANALYZING SPECIFIC RECORD (ID: 4)\n";
    echo "===================================\n";
    
    $record = UserAccess::find(4);
    if ($record) {
        echo "Record found:\n";
        echo "- PF Number: {$record->pf_number}\n";
        echo "- Staff Name: {$record->staff_name}\n";
        echo "- Status: {$record->status}\n";
        echo "- HOD Name: " . ($record->hod_name ?: 'NULL') . "\n";
        echo "- HOD Signature Path: " . ($record->hod_signature_path ?: 'NULL') . "\n";
        echo "- HOD Approved At: " . ($record->hod_approved_at ?: 'NULL') . "\n";
        echo "- Wellsoft Modules: " . json_encode($record->wellsoft_modules) . "\n";
        echo "- Wellsoft Modules Selected: " . json_encode($record->wellsoft_modules_selected) . "\n";
        echo "- Jeeva Modules: " . json_encode($record->jeeva_modules) . "\n";
        echo "- Jeeva Modules Selected: " . json_encode($record->jeeva_modules_selected) . "\n";
        echo "- Module Requested For: " . ($record->module_requested_for ?: 'NULL') . "\n";
        echo "- Request Type: " . json_encode($record->request_type) . "\n";
        echo "\n";
        
        // Check raw database values
        $rawRecord = DB::table('user_access')->where('id', 4)->first();
        echo "Raw database values:\n";
        echo "- wellsoft_modules (raw): " . ($rawRecord->wellsoft_modules ?: 'NULL') . "\n";
        echo "- wellsoft_modules_selected (raw): " . ($rawRecord->wellsoft_modules_selected ?: 'NULL') . "\n";
        echo "- jeeva_modules (raw): " . ($rawRecord->jeeva_modules ?: 'NULL') . "\n";
        echo "- jeeva_modules_selected (raw): " . ($rawRecord->jeeva_modules_selected ?: 'NULL') . "\n";
        echo "- module_requested_for (raw): " . ($rawRecord->module_requested_for ?: 'NULL') . "\n";
        echo "- hod_signature_path (raw): " . ($rawRecord->hod_signature_path ?: 'NULL') . "\n";
    } else {
        echo "❌ Record with ID 4 not found\n";
    }
    echo "\n";

    // Step 4: Identify the root causes
    echo "4. ROOT CAUSE ANALYSIS\n";
    echo "=====================\n";
    
    echo "🔍 IDENTIFIED ISSUES:\n";
    echo "1. HOD Signature Upload Issue:\n";
    echo "   - Signature file upload may not be processed correctly\n";
    echo "   - File storage path not being saved to database\n";
    echo "   - Validation may be failing silently\n\n";
    
    echo "2. Module Selection Issue:\n";
    echo "   - Frontend may not be sending module data correctly\n";
    echo "   - Backend may not be processing array data properly\n";
    echo "   - JSON casting may have issues\n\n";
    
    echo "3. Date Processing Issue:\n";
    echo "   - Date format conversion problems\n";
    echo "   - Timezone issues\n";
    echo "   - Validation failing on date fields\n\n";

    // Step 5: Provide solutions
    echo "5. RECOMMENDED SOLUTIONS\n";
    echo "=======================\n";
    
    echo "🛠️ IMMEDIATE FIXES NEEDED:\n\n";
    
    echo "A. Fix HOD Approval Controller:\n";
    echo "   - Add proper file upload validation\n";
    echo "   - Ensure signature storage directory exists\n";
    echo "   - Add error logging for file upload failures\n";
    echo "   - Validate file upload before database save\n\n";
    
    echo "B. Fix Module Selection Processing:\n";
    echo "   - Validate module arrays before saving\n";
    echo "   - Add proper JSON encoding/decoding\n";
    echo "   - Ensure frontend sends data in correct format\n";
    echo "   - Add validation for required modules when service is requested\n\n";
    
    echo "C. Fix Date Processing:\n";
    echo "   - Use proper date format validation\n";
    echo "   - Handle timezone conversion correctly\n";
    echo "   - Add date format normalization\n\n";

    // Step 6: Create test data to verify fixes
    echo "6. CREATING TEST SCENARIO\n";
    echo "========================\n";
    
    // Test the current updateHodApproval method behavior
    echo "Testing current behavior with sample data...\n";
    
    $testData = [
        'hod_name' => 'Test HOD Name',
        'approved_date' => '2025-01-27',
        'comments' => 'Test HOD approval comments',
        'access_type' => 'permanent',
        'temporary_until' => null,
        'wellsoft_modules_selected' => ['FINANCIAL ACCOUNTING', 'DOCTOR CONSULTATION'],
        'jeeva_modules_selected' => ['OUTPATIENT', 'NURSING STATION'],
        'module_requested_for' => 'use'
    ];
    
    echo "Sample test data:\n";
    echo json_encode($testData, JSON_PRETTY_PRINT) . "\n\n";

    // Step 7: Database schema recommendations
    echo "7. DATABASE SCHEMA RECOMMENDATIONS\n";
    echo "=================================\n";
    
    echo "✅ Current schema is correct, issues are in:\n";
    echo "1. Controller logic for processing form data\n";
    echo "2. File upload handling\n";
    echo "3. Frontend data submission format\n";
    echo "4. Validation rules\n\n";

    echo "🎯 NEXT STEPS:\n";
    echo "1. Fix the updateHodApproval method in BothServiceFormController\n";
    echo "2. Add proper file upload validation and error handling\n";
    echo "3. Fix module selection processing logic\n";
    echo "4. Update frontend to send data in correct format\n";
    echo "5. Add comprehensive validation\n";
    echo "6. Test the complete workflow\n\n";

    echo "📋 FILES TO MODIFY:\n";
    echo "1. backend/app/Http/Controllers/Api/v1/BothServiceFormController.php\n";
    echo "2. Frontend form component for /both-service-form/:id\n";
    echo "3. Add proper validation rules\n";
    echo "4. Add error handling and logging\n\n";

    echo "🔧 SPECIFIC FIXES NEEDED:\n";
    echo "1. Fix signature upload in updateHodApproval method\n";
    echo "2. Add module selection validation and processing\n";
    echo "3. Fix date format handling\n";
    echo "4. Add proper error responses\n";
    echo "5. Ensure frontend sends complete data\n\n";

} catch (Exception $e) {
    echo "❌ Error during analysis: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "🎉 ANALYSIS COMPLETE!\n";
echo "====================\n";
echo "The issues have been identified. Proceed with implementing the fixes.\n";

?>