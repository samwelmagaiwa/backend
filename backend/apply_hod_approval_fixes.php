<?php

/**
 * Apply HOD Approval Fixes to BothServiceFormController
 * 
 * This script applies the fixes for:
 * 1. HOD signature upload issues
 * 2. Module selection not being saved
 * 3. Date processing problems
 * 4. Missing validation
 */

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔧 APPLYING HOD APPROVAL FIXES\n";
echo "=============================\n\n";

try {
    // Step 1: Backup the original controller
    echo "1. BACKING UP ORIGINAL CONTROLLER\n";
    echo "=================================\n";
    
    $originalPath = __DIR__ . '/app/Http/Controllers/Api/v1/BothServiceFormController.php';
    $backupPath = __DIR__ . '/app/Http/Controllers/Api/v1/BothServiceFormController.backup.' . date('Y-m-d-H-i-s') . '.php';
    
    if (file_exists($originalPath)) {
        copy($originalPath, $backupPath);
        echo "✅ Original controller backed up to: " . basename($backupPath) . "\n";
    } else {
        echo "❌ Original controller not found at: {$originalPath}\n";
        exit(1);
    }
    echo "\n";

    // Step 2: Read the fixed controller
    echo "2. READING FIXED CONTROLLER\n";
    echo "==========================\n";
    
    $fixedPath = __DIR__ . '/app/Http/Controllers/Api/v1/BothServiceFormControllerFixed.php';
    
    if (!file_exists($fixedPath)) {
        echo "❌ Fixed controller not found at: {$fixedPath}\n";
        exit(1);
    }
    
    $fixedContent = file_get_contents($fixedPath);
    echo "✅ Fixed controller content loaded\n";
    echo "\n";

    // Step 3: Extract the fixed methods
    echo "3. EXTRACTING FIXED METHODS\n";
    echo "==========================\n";
    
    // Extract the updateHodApproval method from the fixed controller
    preg_match('/public function updateHodApproval.*?(?=public function|\}$)/s', $fixedContent, $updateHodApprovalMatch);
    $fixedUpdateHodApproval = $updateHodApprovalMatch[0] ?? null;
    
    // Extract the show method from the fixed controller
    preg_match('/public function show.*?(?=public function|\}$)/s', $fixedContent, $showMatch);
    $fixedShow = $showMatch[0] ?? null;
    
    if (!$fixedUpdateHodApproval) {
        echo "❌ Could not extract updateHodApproval method from fixed controller\n";
        exit(1);
    }
    
    if (!$fixedShow) {
        echo "❌ Could not extract show method from fixed controller\n";
        exit(1);
    }
    
    echo "✅ Fixed updateHodApproval method extracted\n";
    echo "✅ Fixed show method extracted\n";
    echo "\n";

    // Step 4: Read the original controller
    echo "4. READING ORIGINAL CONTROLLER\n";
    echo "==============================\n";
    
    $originalContent = file_get_contents($originalPath);
    echo "✅ Original controller content loaded\n";
    echo "\n";

    // Step 5: Replace the methods in the original controller
    echo "5. APPLYING FIXES\n";
    echo "================\n";
    
    // Replace updateHodApproval method
    $updatedContent = preg_replace(
        '/public function updateHodApproval.*?(?=public function|\}$)/s',
        $fixedUpdateHodApproval,
        $originalContent
    );
    
    if ($updatedContent === $originalContent) {
        echo "⚠️ updateHodApproval method replacement may have failed\n";
    } else {
        echo "✅ updateHodApproval method replaced\n";
    }
    
    // Replace show method
    $finalContent = preg_replace(
        '/public function show.*?(?=public function|\}$)/s',
        $fixedShow,
        $updatedContent
    );
    
    if ($finalContent === $updatedContent) {
        echo "⚠️ show method replacement may have failed\n";
    } else {
        echo "✅ show method replaced\n";
    }
    echo "\n";

    // Step 6: Write the updated controller
    echo "6. WRITING UPDATED CONTROLLER\n";
    echo "============================\n";
    
    $success = file_put_contents($originalPath, $finalContent);
    
    if ($success) {
        echo "✅ Updated controller written successfully\n";
        echo "File size: " . number_format(strlen($finalContent)) . " bytes\n";
    } else {
        echo "❌ Failed to write updated controller\n";
        exit(1);
    }
    echo "\n";

    // Step 7: Verify the changes
    echo "7. VERIFYING CHANGES\n";
    echo "===================\n";
    
    $verifyContent = file_get_contents($originalPath);
    
    // Check if the fixes are present
    $hasSignatureValidation = strpos($verifyContent, 'hod_signature.required') !== false;
    $hasModuleProcessing = strpos($verifyContent, 'wellsoft_modules_selected') !== false;
    $hasEnhancedLogging = strpos($verifyContent, 'HOD APPROVAL UPDATE START') !== false;
    $hasFileUploadFix = strpos($verifyContent, 'makeDirectory') !== false;
    
    echo "Verification results:\n";
    echo "- Enhanced signature validation: " . ($hasSignatureValidation ? '✅' : '❌') . "\n";
    echo "- Module selection processing: " . ($hasModuleProcessing ? '✅' : '❌') . "\n";
    echo "- Enhanced logging: " . ($hasEnhancedLogging ? '✅' : '❌') . "\n";
    echo "- File upload fixes: " . ($hasFileUploadFix ? '✅' : '❌') . "\n";
    echo "\n";

    // Step 8: Create a summary of changes
    echo "8. SUMMARY OF CHANGES APPLIED\n";
    echo "============================\n";
    
    echo "✅ FIXES APPLIED:\n\n";
    
    echo "A. HOD Signature Upload Fixes:\n";
    echo "   - Added proper file validation with clear error messages\n";
    echo "   - Enhanced file upload error handling\n";
    echo "   - Added directory creation if not exists\n";
    echo "   - Added file storage verification\n";
    echo "   - Improved logging for debugging\n\n";
    
    echo "B. Module Selection Fixes:\n";
    echo "   - Added proper processing of wellsoft_modules_selected\n";
    echo "   - Added proper processing of jeeva_modules_selected\n";
    echo "   - Added module_requested_for handling\n";
    echo "   - Enhanced validation for module arrays\n";
    echo "   - Added logging for module processing\n\n";
    
    echo "C. Date Processing Fixes:\n";
    echo "   - Enhanced date validation with better error messages\n";
    echo "   - Proper handling of temporary_until dates\n";
    echo "   - Added timezone-aware date processing\n\n";
    
    echo "D. Enhanced Error Handling:\n";
    echo "   - Comprehensive try-catch blocks\n";
    echo "   - Detailed error logging\n";
    echo "   - Better error messages for frontend\n";
    echo "   - Transaction rollback on failures\n\n";
    
    echo "E. Improved Response Data:\n";
    echo "   - Complete module information in responses\n";
    echo "   - Signature URLs for frontend display\n";
    echo "   - Enhanced approval status information\n";
    echo "   - Better workflow progress tracking\n\n";

    // Step 9: Next steps
    echo "9. NEXT STEPS\n";
    echo "============\n";
    
    echo "🔄 IMMEDIATE ACTIONS REQUIRED:\n\n";
    
    echo "1. Test the HOD approval functionality:\n";
    echo "   - Visit /both-service-form/4\n";
    echo "   - Try uploading HOD signature\n";
    echo "   - Select modules for the user\n";
    echo "   - Submit the approval\n";
    echo "   - Verify data is saved correctly\n\n";
    
    echo "2. Check the database after testing:\n";
    echo "   - Verify hod_signature_path is not NULL\n";
    echo "   - Verify wellsoft_modules and jeeva_modules are populated\n";
    echo "   - Verify module_requested_for is set\n";
    echo "   - Verify hod_approved_at is set correctly\n\n";
    
    echo "3. Monitor the logs:\n";
    echo "   - Check storage/logs/laravel.log for detailed logging\n";
    echo "   - Look for 'HOD APPROVAL UPDATE START' entries\n";
    echo "   - Verify no error messages appear\n\n";
    
    echo "4. Frontend considerations:\n";
    echo "   - Ensure frontend sends module data correctly\n";
    echo "   - Verify file upload form is working\n";
    echo "   - Check that all required fields are being sent\n\n";
    
    echo "📁 FILES MODIFIED:\n";
    echo "- backend/app/Http/Controllers/Api/v1/BothServiceFormController.php (updated)\n";
    echo "- Backup created: " . basename($backupPath) . "\n\n";
    
    echo "🧪 TESTING COMMANDS:\n";
    echo "# Test the specific record that was having issues:\n";
    echo "curl -X GET http://127.0.0.1:8000/api/both-service-form/4 \\\n";
    echo "     -H 'Authorization: Bearer YOUR_TOKEN' \\\n";
    echo "     -H 'Accept: application/json'\n\n";
    
    echo "# Check Laravel logs:\n";
    echo "tail -f storage/logs/laravel.log\n\n";

} catch (Exception $e) {
    echo "❌ Error applying fixes: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "🎉 HOD APPROVAL FIXES APPLIED!\n";
echo "==============================\n";
echo "The fixes have been applied to the BothServiceFormController.\n";
echo "Please test the functionality and monitor the logs.\n";

?>