<?php

/**
 * Diagnostic Script for HOD Signature Storage Issues
 * 
 * This script will help identify why HOD signatures are not being stored properly
 */

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\UserAccess;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

echo "🔍 DIAGNOSING HOD SIGNATURE STORAGE ISSUES\n";
echo "==========================================\n\n";

try {
    // Step 1: Check storage configuration
    echo "1. STORAGE CONFIGURATION CHECK\n";
    echo "==============================\n";
    
    $storageConfig = config('filesystems');
    $defaultDisk = $storageConfig['default'];
    $publicDisk = $storageConfig['disks']['public'] ?? null;
    
    echo "Default disk: {$defaultDisk}\n";
    echo "Public disk config: " . json_encode($publicDisk, JSON_PRETTY_PRINT) . "\n";
    
    // Check if public disk is properly configured
    if ($publicDisk) {
        $publicRoot = $publicDisk['root'];
        $publicUrl = $publicDisk['url'];
        echo "Public disk root: {$publicRoot}\n";
        echo "Public disk URL: {$publicUrl}\n";
        
        // Check if directory exists
        if (is_dir($publicRoot)) {
            echo "✅ Public storage directory exists\n";
            echo "Directory permissions: " . substr(sprintf('%o', fileperms($publicRoot)), -4) . "\n";
        } else {
            echo "❌ Public storage directory does not exist: {$publicRoot}\n";
        }
    }
    echo "\n";

    // Step 2: Check signature directories
    echo "2. SIGNATURE DIRECTORIES CHECK\n";
    echo "==============================\n";
    
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
        $exists = Storage::disk('public')->exists($dir);
        $fullPath = Storage::disk('public')->path($dir);
        
        echo "Directory: {$dir}\n";
        echo "  Exists: " . ($exists ? "✅ Yes" : "❌ No") . "\n";
        echo "  Full path: {$fullPath}\n";
        
        if ($exists) {
            $files = Storage::disk('public')->files($dir);
            echo "  Files count: " . count($files) . "\n";
            if (count($files) > 0) {
                echo "  Sample files: " . implode(', ', array_slice($files, 0, 3)) . "\n";
            }
            
            // Check permissions
            if (is_dir($fullPath)) {
                echo "  Permissions: " . substr(sprintf('%o', fileperms($fullPath)), -4) . "\n";
            }
        }
        echo "\n";
    }

    // Step 3: Check database records with HOD signature issues
    echo "3. DATABASE RECORDS ANALYSIS\n";
    echo "============================\n";
    
    $totalRecords = UserAccess::count();
    $recordsWithHodName = UserAccess::whereNotNull('hod_name')->count();
    $recordsWithHodSignature = UserAccess::whereNotNull('hod_signature_path')->count();
    $recordsWithHodNameButNoSignature = UserAccess::whereNotNull('hod_name')
        ->whereNull('hod_signature_path')->count();
    
    echo "Total user access records: {$totalRecords}\n";
    echo "Records with HOD name: {$recordsWithHodName}\n";
    echo "Records with HOD signature path: {$recordsWithHodSignature}\n";
    echo "Records with HOD name but NO signature: {$recordsWithHodNameButNoSignature}\n\n";
    
    // Get specific problematic records
    $problematicRecords = UserAccess::whereNotNull('hod_name')
        ->whereNull('hod_signature_path')
        ->limit(5)
        ->get();
    
    echo "PROBLEMATIC RECORDS (HOD name but no signature):\n";
    foreach ($problematicRecords as $record) {
        echo "- ID: {$record->id}, PF: {$record->pf_number}, HOD: {$record->hod_name}, Status: {$record->status}\n";
    }
    echo "\n";

    // Step 4: Test file upload simulation
    echo "4. FILE UPLOAD SIMULATION TEST\n";
    echo "==============================\n";
    
    // Create a test file
    $testContent = "Test HOD signature content";
    $testFileName = 'test_hod_signature_' . time() . '.txt';
    $testFilePath = sys_get_temp_dir() . '/' . $testFileName;
    file_put_contents($testFilePath, $testContent);
    
    echo "Created test file: {$testFilePath}\n";
    echo "Test file size: " . filesize($testFilePath) . " bytes\n";
    
    try {
        // Test storage using Laravel Storage
        $signatureDir = 'signatures/hod';
        
        // Ensure directory exists
        if (!Storage::disk('public')->exists($signatureDir)) {
            Storage::disk('public')->makeDirectory($signatureDir);
            echo "✅ Created signature directory: {$signatureDir}\n";
        } else {
            echo "✅ Signature directory already exists: {$signatureDir}\n";
        }
        
        // Store test file
        $storedPath = Storage::disk('public')->putFileAs(
            $signatureDir,
            new \Illuminate\Http\File($testFilePath),
            $testFileName
        );
        
        echo "✅ Test file stored at: {$storedPath}\n";
        
        // Verify file exists
        if (Storage::disk('public')->exists($storedPath)) {
            echo "✅ Test file verified to exist in storage\n";
            $storedSize = Storage::disk('public')->size($storedPath);
            echo "Stored file size: {$storedSize} bytes\n";
            
            // Get URL
            $url = Storage::url($storedPath);
            echo "File URL: {$url}\n";
            
            // Clean up test file
            Storage::disk('public')->delete($storedPath);
            echo "✅ Test file cleaned up\n";
        } else {
            echo "❌ Test file NOT found in storage after upload\n";
        }
        
    } catch (\Exception $e) {
        echo "❌ File upload test failed: " . $e->getMessage() . "\n";
    }
    
    // Clean up temp file
    if (file_exists($testFilePath)) {
        unlink($testFilePath);
    }
    echo "\n";

    // Step 5: Check Laravel logs for HOD signature related errors
    echo "5. RECENT LOG ANALYSIS\n";
    echo "======================\n";
    
    $logPath = storage_path('logs/laravel.log');
    if (file_exists($logPath)) {
        echo "Log file exists: {$logPath}\n";
        echo "Log file size: " . number_format(filesize($logPath)) . " bytes\n";
        
        // Read last 100 lines and look for HOD signature related entries
        $logLines = file($logPath);
        $recentLines = array_slice($logLines, -100);
        
        $hodSignatureLines = array_filter($recentLines, function($line) {
            return stripos($line, 'hod_signature') !== false || 
                   stripos($line, 'HOD signature') !== false ||
                   stripos($line, 'signature upload') !== false;
        });
        
        if (!empty($hodSignatureLines)) {
            echo "\nRecent HOD signature related log entries:\n";
            foreach (array_slice($hodSignatureLines, -10) as $line) {
                echo "  " . trim($line) . "\n";
            }
        } else {
            echo "No recent HOD signature related log entries found\n";
        }
    } else {
        echo "❌ Log file not found: {$logPath}\n";
    }
    echo "\n";

    // Step 6: Check specific record (ID 4 if it exists)
    echo "6. SPECIFIC RECORD ANALYSIS (ID: 4)\n";
    echo "===================================\n";
    
    $record = UserAccess::find(4);
    if ($record) {
        echo "Record found:\n";
        echo "- ID: {$record->id}\n";
        echo "- PF Number: {$record->pf_number}\n";
        echo "- Staff Name: {$record->staff_name}\n";
        echo "- Status: {$record->status}\n";
        echo "- HOD Name: " . ($record->hod_name ?: 'NULL') . "\n";
        echo "- HOD Signature Path: " . ($record->hod_signature_path ?: 'NULL') . "\n";
        echo "- HOD Approved At: " . ($record->hod_approved_at ?: 'NULL') . "\n";
        echo "- Created At: {$record->created_at}\n";
        echo "- Updated At: {$record->updated_at}\n";
        
        // Check if signature file exists (if path is set)
        if ($record->hod_signature_path) {
            $signatureExists = Storage::disk('public')->exists($record->hod_signature_path);
            echo "- Signature File Exists: " . ($signatureExists ? "✅ Yes" : "❌ No") . "\n";
            
            if ($signatureExists) {
                $signatureSize = Storage::disk('public')->size($record->hod_signature_path);
                $signatureUrl = Storage::url($record->hod_signature_path);
                echo "- Signature File Size: {$signatureSize} bytes\n";
                echo "- Signature URL: {$signatureUrl}\n";
            }
        }
    } else {
        echo "❌ Record with ID 4 not found\n";
    }
    echo "\n";

    // Step 7: Check controller method implementation
    echo "7. CONTROLLER METHOD ANALYSIS\n";
    echo "=============================\n";
    
    $controllerPath = __DIR__ . '/app/Http/Controllers/Api/v1/BothServiceFormController.php';
    if (file_exists($controllerPath)) {
        $controllerContent = file_get_contents($controllerPath);
        
        // Check for key methods and patterns
        $patterns = [
            'updateHodApprovalForRecord' => 'updateHodApprovalForRecord method exists',
            'hod_signature_path' => 'hod_signature_path field handling',
            'Storage::disk(\'public\')' => 'Public disk usage',
            'storeAs(' => 'File storage method',
            'makeDirectory(' => 'Directory creation',
            'DB::beginTransaction' => 'Transaction handling',
        ];
        
        foreach ($patterns as $pattern => $description) {
            $count = substr_count($controllerContent, $pattern);
            echo "- {$description}: " . ($count > 0 ? "✅ Found ({$count} occurrences)" : "❌ Not found") . "\n";
        }
    } else {
        echo "❌ Controller file not found: {$controllerPath}\n";
    }
    echo "\n";

    // Step 8: Provide recommendations
    echo "8. RECOMMENDATIONS\n";
    echo "==================\n";
    
    $recommendations = [];
    
    if ($recordsWithHodNameButNoSignature > 0) {
        $recommendations[] = "Fix {$recordsWithHodNameButNoSignature} records with HOD names but missing signatures";
    }
    
    if (!Storage::disk('public')->exists('signatures/hod')) {
        $recommendations[] = "Create missing HOD signature directory";
    }
    
    if (empty($recommendations)) {
        echo "✅ No immediate issues detected\n";
    } else {
        foreach ($recommendations as $i => $recommendation) {
            echo ($i + 1) . ". {$recommendation}\n";
        }
    }
    echo "\n";

    // Step 9: Generate fix script
    echo "9. GENERATING FIX SCRIPT\n";
    echo "========================\n";
    
    $fixScript = "<?php\n\n";
    $fixScript .= "// Auto-generated fix script for HOD signature issues\n";
    $fixScript .= "// Generated on: " . date('Y-m-d H:i:s') . "\n\n";
    
    $fixScript .= "require_once __DIR__ . '/vendor/autoload.php';\n";
    $fixScript .= "\$app = require_once __DIR__ . '/bootstrap/app.php';\n";
    $fixScript .= "\$app->make(Illuminate\\Contracts\\Console\\Kernel::class)->bootstrap();\n\n";
    
    $fixScript .= "use Illuminate\\Support\\Facades\\Storage;\n";
    $fixScript .= "use App\\Models\\UserAccess;\n\n";
    
    $fixScript .= "echo \"🔧 FIXING HOD SIGNATURE ISSUES...\\n\";\n\n";
    
    // Create directories if missing
    $fixScript .= "// Create missing directories\n";
    $fixScript .= "if (!Storage::disk('public')->exists('signatures/hod')) {\n";
    $fixScript .= "    Storage::disk('public')->makeDirectory('signatures/hod');\n";
    $fixScript .= "    echo \"✅ Created signatures/hod directory\\n\";\n";
    $fixScript .= "}\n\n";
    
    // Add more fixes as needed
    $fixScript .= "echo \"✅ Fix script completed\\n\";\n";
    
    file_put_contents(__DIR__ . '/fix_hod_signature_issues.php', $fixScript);
    echo "✅ Fix script generated: fix_hod_signature_issues.php\n";

} catch (Exception $e) {
    echo "❌ Error during diagnosis: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n🎉 DIAGNOSIS COMPLETE!\n";
echo "======================\n";
echo "Check the output above for issues and run the generated fix script if needed.\n";

?>