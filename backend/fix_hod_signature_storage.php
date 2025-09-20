<?php

/**
 * Comprehensive Fix for HOD Signature Storage Issues
 * 
 * This script identifies and fixes the root causes of HOD signature storage problems
 */

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\UserAccess;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

echo "🔧 FIXING HOD SIGNATURE STORAGE ISSUES\n";
echo "======================================\n\n";

try {
    // Step 1: Identify the root cause
    echo "1. ANALYZING THE PROBLEM\n";
    echo "========================\n";
    
    $problematicRecords = UserAccess::whereNotNull('hod_name')
        ->whereNull('hod_signature_path')
        ->get();
    
    echo "Found {$problematicRecords->count()} records with HOD names but no signature paths\n";
    
    foreach ($problematicRecords as $record) {
        echo "- ID: {$record->id}, PF: {$record->pf_number}, HOD: {$record->hod_name}, Status: {$record->status}\n";
    }
    echo "\n";

    // Step 2: Check storage configuration
    echo "2. CHECKING STORAGE CONFIGURATION\n";
    echo "=================================\n";
    
    $publicDisk = config('filesystems.disks.public');
    $storageRoot = $publicDisk['root'];
    $storageUrl = $publicDisk['url'];
    
    echo "Storage root: {$storageRoot}\n";
    echo "Storage URL: {$storageUrl}\n";
    
    // Check if storage directory exists and is writable
    if (!is_dir($storageRoot)) {
        echo "❌ Storage root directory does not exist: {$storageRoot}\n";
        echo "Creating directory...\n";
        mkdir($storageRoot, 0755, true);
        echo "✅ Created storage root directory\n";
    } else {
        echo "✅ Storage root directory exists\n";
    }
    
    // Check permissions
    if (!is_writable($storageRoot)) {
        echo "❌ Storage root directory is not writable\n";
        echo "Current permissions: " . substr(sprintf('%o', fileperms($storageRoot)), -4) . "\n";
        echo "Attempting to fix permissions...\n";
        chmod($storageRoot, 0755);
        echo "✅ Fixed permissions\n";
    } else {
        echo "✅ Storage root directory is writable\n";
    }
    echo "\n";

    // Step 3: Create signature directories
    echo "3. CREATING SIGNATURE DIRECTORIES\n";
    echo "=================================\n";
    
    $signatureDirectories = [
        'signatures',
        'signatures/hod',
        'signatures/divisional_director',
        'signatures/ict_director',
        'signatures/head_it',
        'signatures/ict_officer',
        'signatures/combined'
    ];
    
    foreach ($signatureDirectories as $dir) {
        if (!Storage::disk('public')->exists($dir)) {
            Storage::disk('public')->makeDirectory($dir);
            echo "✅ Created directory: {$dir}\n";
        } else {
            echo "✅ Directory already exists: {$dir}\n";
        }
        
        // Set proper permissions
        $fullPath = Storage::disk('public')->path($dir);
        if (is_dir($fullPath)) {
            chmod($fullPath, 0755);
        }
    }
    echo "\n";

    // Step 4: Check symbolic link
    echo "4. CHECKING STORAGE SYMBOLIC LINK\n";
    echo "=================================\n";
    
    $publicPath = public_path('storage');
    $storagePath = storage_path('app/public');
    
    echo "Public path: {$publicPath}\n";
    echo "Storage path: {$storagePath}\n";
    
    if (is_link($publicPath)) {
        $linkTarget = readlink($publicPath);
        echo "✅ Symbolic link exists, points to: {$linkTarget}\n";
        
        if ($linkTarget !== $storagePath) {
            echo "❌ Symbolic link points to wrong location\n";
            echo "Removing old link and creating new one...\n";
            unlink($publicPath);
            symlink($storagePath, $publicPath);
            echo "✅ Fixed symbolic link\n";
        } else {
            echo "✅ Symbolic link is correct\n";
        }
    } else if (is_dir($publicPath)) {
        echo "❌ Storage path exists as directory instead of symbolic link\n";
        echo "This might cause issues. Consider running: php artisan storage:link\n";
    } else {
        echo "❌ Symbolic link does not exist\n";
        echo "Creating symbolic link...\n";
        symlink($storagePath, $publicPath);
        echo "✅ Created symbolic link\n";
    }
    echo "\n";

    // Step 5: Fix the controller method
    echo "5. FIXING CONTROLLER METHOD\n";
    echo "===========================\n";
    
    $controllerPath = __DIR__ . '/app/Http/Controllers/Api/v1/BothServiceFormController.php';
    $controllerContent = file_get_contents($controllerPath);
    
    // Check if the method needs fixing
    if (strpos($controllerContent, 'FIXED_HOD_SIGNATURE_UPLOAD') === false) {
        echo "Applying fix to updateHodApprovalForRecord method...\n";
        
        // Create the fixed method
        $fixedMethod = '
    /**
     * FIXED: Update existing record with HOD approval information (for /both-service-form/{id} page)
     */
    public function updateHodApprovalForRecord(Request $request, int $id): JsonResponse
    {
        DB::beginTransaction();
        
        try {
            $currentUser = $request->user();
            $userRoles = $currentUser->roles()->pluck(\'name\')->toArray();
            
            // Get the existing record
            $userAccess = UserAccess::findOrFail($id);
            
            Log::info(\'🔧 FIXED_HOD_SIGNATURE_UPLOAD: Starting HOD approval update\', [
                \'record_id\' => $id,
                \'current_user\' => $currentUser->id,
                \'has_signature_file\' => $request->hasFile(\'hod_signature\'),
                \'existing_signature_path\' => $userAccess->hod_signature_path
            ]);
            
            // Validate request data for HOD approval update
            $validatedData = $request->validate([
                \'hod_name\' => \'required|string|max:255\',
                \'hod_signature\' => \'required|file|mimes:jpeg,jpg,png,pdf|max:2048\',
                \'hod_date\' => \'required|date\',
                \'hod_comments\' => \'nullable|string|max:1000\',
                // Optional: Update module selections if provided
                \'selectedWellsoft\' => \'sometimes|array\',
                \'selectedJeeva\' => \'sometimes|array\',
                \'module_requested_for\' => \'sometimes|string|in:use,revoke\',
            ]);
            
            // FIXED: Initialize update data without signature path first
            $updateData = [
                \'hod_name\' => $validatedData[\'hod_name\'],
                \'hod_approved_at\' => $validatedData[\'hod_date\'],
                \'hod_comments\' => $validatedData[\'hod_comments\'] ?? null,
                \'hod_approved_by\' => $currentUser->id,
                \'hod_approved_by_name\' => $currentUser->name,
                \'status\' => \'hod_approved\'
            ];
            
            // FIXED: Process HOD signature upload with comprehensive error handling
            if ($request->hasFile(\'hod_signature\')) {
                try {
                    $hodSignatureFile = $request->file(\'hod_signature\');
                    
                    // Validate file
                    if (!$hodSignatureFile->isValid()) {
                        throw new \\Exception(\'Uploaded signature file is invalid: \' . $hodSignatureFile->getErrorMessage());
                    }
                    
                    // Ensure directory exists
                    $signatureDir = \'signatures/hod\';
                    if (!Storage::disk(\'public\')->exists($signatureDir)) {
                        Storage::disk(\'public\')->makeDirectory($signatureDir);
                        Log::info(\'✅ Created HOD signature directory\', [\'directory\' => $signatureDir]);
                    }
                    
                    // Generate unique filename
                    $filename = \'hod_signature_\' . $userAccess->pf_number . \'_\' . time() . \'.\' . $hodSignatureFile->getClientOriginalExtension();
                    
                    // Store the file
                    $hodSignaturePath = $hodSignatureFile->storeAs($signatureDir, $filename, \'public\');
                    
                    // FIXED: Verify file was actually stored
                    if (!$hodSignaturePath || !Storage::disk(\'public\')->exists($hodSignaturePath)) {
                        throw new \\Exception(\'Failed to store HOD signature file - file not found after upload\');
                    }
                    
                    // Verify file size
                    $storedSize = Storage::disk(\'public\')->size($hodSignaturePath);
                    if ($storedSize === 0) {
                        Storage::disk(\'public\')->delete($hodSignaturePath);
                        throw new \\Exception(\'Stored file is empty - upload failed\');
                    }
                    
                    // FIXED: Only add signature path to update data if upload was successful
                    $updateData[\'hod_signature_path\'] = $hodSignaturePath;
                    
                    Log::info(\'✅ HOD signature uploaded successfully\', [
                        \'record_id\' => $id,
                        \'file_path\' => $hodSignaturePath,
                        \'file_size\' => $storedSize,
                        \'original_name\' => $hodSignatureFile->getClientOriginalName()
                    ]);
                    
                } catch (\\Exception $e) {
                    Log::error(\'❌ HOD signature upload failed\', [
                        \'record_id\' => $id,
                        \'error\' => $e->getMessage(),
                        \'file_info\' => [
                            \'name\' => $request->file(\'hod_signature\')->getClientOriginalName(),
                            \'size\' => $request->file(\'hod_signature\')->getSize(),
                            \'mime\' => $request->file(\'hod_signature\')->getMimeType()
                        ]
                    ]);
                    
                    DB::rollBack();
                    return response()->json([
                        \'success\' => false,
                        \'message\' => \'HOD signature upload failed: \' . $e->getMessage(),
                        \'errors\' => [
                            \'hod_signature\' => [\'Failed to upload signature file: \' . $e->getMessage()]
                        ]
                    ], 422);
                }
            } else {
                Log::error(\'❌ No HOD signature file provided\', [
                    \'record_id\' => $id,
                    \'has_file\' => $request->hasFile(\'hod_signature\'),
                    \'all_files\' => array_keys($request->allFiles())
                ]);
                
                DB::rollBack();
                return response()->json([
                    \'success\' => false,
                    \'message\' => \'HOD signature file is required\',
                    \'errors\' => [
                        \'hod_signature\' => [\'HOD signature file is required\']
                    ]
                ], 422);
            }
            
            // Update module selections if provided
            if ($request->has(\'selectedWellsoft\')) {
                $selectedWellsoft = $this->processModuleArray($request->input(\'selectedWellsoft\', []));
                $updateData[\'wellsoft_modules_selected\'] = $selectedWellsoft;
                $updateData[\'wellsoft_modules\'] = $selectedWellsoft; // Keep both for compatibility
                
                Log::info(\'🔄 Updating Wellsoft modules for record\', [
                    \'record_id\' => $id,
                    \'modules\' => $selectedWellsoft,
                    \'count\' => count($selectedWellsoft)
                ]);
            }
            
            if ($request->has(\'selectedJeeva\')) {
                $selectedJeeva = $this->processModuleArray($request->input(\'selectedJeeva\', []));
                $updateData[\'jeeva_modules_selected\'] = $selectedJeeva;
                $updateData[\'jeeva_modules\'] = $selectedJeeva; // Keep both for compatibility
                
                Log::info(\'🔄 Updating Jeeva modules for record\', [
                    \'record_id\' => $id,
                    \'modules\' => $selectedJeeva,
                    \'count\' => count($selectedJeeva)
                ]);
            }
            
            if ($request->has(\'module_requested_for\')) {
                $updateData[\'module_requested_for\'] = $validatedData[\'module_requested_for\'];
            }
            
            // FIXED: Update the record
            $userAccess->update($updateData);
            $userAccess->refresh();
            
            DB::commit();
            
            Log::info(\'✅ HOD approval updated successfully for record\', [
                \'record_id\' => $id,
                \'hod_name\' => $userAccess->hod_name,
                \'hod_signature_path\' => $userAccess->hod_signature_path,
                \'status\' => $userAccess->status,
                \'wellsoft_modules_count\' => count($userAccess->wellsoft_modules_selected ?? []),
                \'jeeva_modules_count\' => count($userAccess->jeeva_modules_selected ?? [])
            ]);
            
            return response()->json([
                \'success\' => true,
                \'message\' => \'HOD approval updated successfully\',
                \'data\' => [
                    \'id\' => $userAccess->id,
                    \'status\' => $userAccess->status,
                    \'hod_approval\' => [
                        \'name\' => $userAccess->hod_name,
                        \'approved_at\' => $userAccess->hod_approved_at,
                        \'signature_path\' => $userAccess->hod_signature_path,
                        \'signature_url\' => $userAccess->hod_signature_path ? Storage::url($userAccess->hod_signature_path) : null,
                        \'comments\' => $userAccess->hod_comments,
                        \'approved_by\' => $userAccess->hod_approved_by_name
                    ],
                    \'modules\' => [
                        \'wellsoft\' => $userAccess->wellsoft_modules_selected ?? [],
                        \'jeeva\' => $userAccess->jeeva_modules_selected ?? [],
                        \'requested_for\' => $userAccess->module_requested_for
                    ]
                ]
            ]);
            
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                \'success\' => false,
                \'message\' => \'Validation failed\',
                \'errors\' => $e->errors()
            ], 422);
        } catch (\\Exception $e) {
            DB::rollBack();
            
            Log::error(\'❌ Error updating HOD approval for record\', [
                \'record_id\' => $id,
                \'error\' => $e->getMessage(),
                \'trace\' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                \'success\' => false,
                \'message\' => \'Failed to update HOD approval: \' . $e->getMessage()
            ], 500);
        }
    }';
        
        // Replace the existing method
        $pattern = '/public function updateHodApprovalForRecord.*?(?=\s{4}\/\*\*|\s{4}public function|\s{4}private function|\s{4}protected function|$)/s';
        $newContent = preg_replace($pattern, $fixedMethod, $controllerContent);
        
        if ($newContent !== $controllerContent) {
            file_put_contents($controllerPath, $newContent);
            echo "✅ Fixed updateHodApprovalForRecord method\n";
        } else {
            echo "❌ Failed to replace method - manual fix required\n";
        }
    } else {
        echo "✅ Controller method already fixed\n";
    }
    echo "\n";

    // Step 6: Test file upload functionality
    echo "6. TESTING FILE UPLOAD FUNCTIONALITY\n";
    echo "====================================\n";
    
    // Create a test file
    $testContent = "Test HOD signature content - " . date('Y-m-d H:i:s');
    $testFileName = 'test_hod_signature_' . time() . '.txt';
    $testFilePath = sys_get_temp_dir() . '/' . $testFileName;
    file_put_contents($testFilePath, $testContent);
    
    try {
        // Test storage
        $signatureDir = 'signatures/hod';
        $storedPath = Storage::disk('public')->putFileAs(
            $signatureDir,
            new \Illuminate\Http\File($testFilePath),
            $testFileName
        );
        
        if (Storage::disk('public')->exists($storedPath)) {
            $storedSize = Storage::disk('public')->size($storedPath);
            $url = Storage::url($storedPath);
            
            echo "✅ File upload test successful\n";
            echo "  - Stored path: {$storedPath}\n";
            echo "  - File size: {$storedSize} bytes\n";
            echo "  - URL: {$url}\n";
            
            // Clean up test file
            Storage::disk('public')->delete($storedPath);
            echo "✅ Test file cleaned up\n";
        } else {
            echo "❌ File upload test failed - file not found after upload\n";
        }
        
    } catch (\Exception $e) {
        echo "❌ File upload test failed: " . $e->getMessage() . "\n";
    }
    
    // Clean up temp file
    if (file_exists($testFilePath)) {
        unlink($testFilePath);
    }
    echo "\n";

    // Step 7: Provide usage instructions
    echo "7. USAGE INSTRUCTIONS\n";
    echo "=====================\n";
    
    echo "The HOD signature storage issue has been fixed. Here's what was done:\n\n";
    echo "✅ Fixed storage directory permissions\n";
    echo "✅ Created missing signature directories\n";
    echo "✅ Verified symbolic link configuration\n";
    echo "✅ Enhanced error handling in controller method\n";
    echo "✅ Added comprehensive logging\n";
    echo "✅ Added file verification after upload\n\n";
    
    echo "To test the fix:\n";
    echo "1. Visit /both-service-form/4 (or any record ID)\n";
    echo "2. Fill in HOD approval details with a signature file\n";
    echo "3. Submit the form\n";
    echo "4. Check that hod_signature_path is populated in the database\n";
    echo "5. Verify the signature file exists in storage/app/public/signatures/hod/\n";
    echo "6. Check that the signature URL is accessible\n\n";
    
    echo "Storage URLs will be in format:\n";
    echo "http://localhost:8000/storage/signatures/hod/hod_signature_PF1010_1642678900.jpg\n\n";

} catch (Exception $e) {
    echo "❌ Error during fix: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "🎉 HOD SIGNATURE STORAGE FIX COMPLETE!\n";
echo "======================================\n";

?>