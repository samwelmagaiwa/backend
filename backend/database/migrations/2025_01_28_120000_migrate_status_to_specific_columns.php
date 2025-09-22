<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Services\StatusMigrationService;
use App\Models\UserAccess;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        echo "Starting migration of status data to specific columns...\n";
        
        $migrationService = new StatusMigrationService();
        
        // Get all records to migrate
        $userAccessRecords = UserAccess::all();
        $totalRecords = $userAccessRecords->count();
        
        echo "Found {$totalRecords} records to migrate\n";
        
        if ($totalRecords === 0) {
            echo "No records to migrate.\n";
            return;
        }
        
        $successful = 0;
        $failed = 0;
        $errors = [];
        
        // Process in batches to avoid memory issues
        $batchSize = 100;
        $processed = 0;
        
        UserAccess::chunk($batchSize, function ($records) use ($migrationService, &$successful, &$failed, &$errors, &$processed, $totalRecords) {
            foreach ($records as $record) {
                try {
                    $oldStatus = $record->status;
                    
                    // Get the mapping for new status columns
                    $newStatusColumns = $migrationService->mapOldStatusToNewColumns($oldStatus, $record);
                    
                    // Update the record with new status columns
                    $record->update($newStatusColumns);
                    
                    $successful++;
                    $processed++;
                    
                    // Log progress every 10 records
                    if ($processed % 10 === 0) {
                        echo "Processed {$processed}/{$totalRecords} records...\n";
                    }
                    
                    Log::info("Migrated UserAccess ID {$record->id} from status '{$oldStatus}' to specific columns", $newStatusColumns);
                    
                } catch (\Exception $e) {
                    $failed++;
                    $processed++;
                    $errors[] = "Failed to migrate record ID {$record->id}: " . $e->getMessage();
                    
                    Log::error("Failed to migrate UserAccess ID {$record->id}", [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }
        });
        
        echo "\n=== MIGRATION RESULTS ===\n";
        echo "Total records: {$totalRecords}\n";
        echo "Successfully migrated: {$successful}\n";
        echo "Failed: {$failed}\n";
        
        if (!empty($errors)) {
            echo "\nERRORS:\n";
            foreach ($errors as $error) {
                echo "- {$error}\n";
            }
        }
        
        // Verify the migration by checking some records
        echo "\n=== VERIFICATION ===\n";
        $this->verifyMigration();
        
        echo "\nMigration completed successfully!\n";
    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        echo "Reverting status migration by clearing specific status columns...\n";
        
        try {
            // Set all specific status columns to null
            DB::table('user_access')->update([
                'hod_status' => null,
                'divisional_status' => null,
                'ict_director_status' => null,
                'head_it_status' => null,
                'ict_officer_status' => null
            ]);
            
            echo "Successfully cleared all specific status columns.\n";
            
        } catch (\Exception $e) {
            echo "Error reverting migration: " . $e->getMessage() . "\n";
            throw $e;
        }
    }
    
    /**
     * Verify the migration by checking a sample of records
     */
    private function verifyMigration(): void
    {
        $statusCounts = [];
        $migrationService = new StatusMigrationService();
        
        // Count records by original status
        $records = UserAccess::all();
        
        foreach ($records as $record) {
            $originalStatus = $record->status;
            
            if (!isset($statusCounts[$originalStatus])) {
                $statusCounts[$originalStatus] = 0;
            }
            $statusCounts[$originalStatus]++;
            
            // Verify the mapping is correct
            $expectedMapping = $migrationService->mapOldStatusToNewColumns($originalStatus, $record);
            
            $actualMapping = [
                'hod_status' => $record->hod_status,
                'divisional_status' => $record->divisional_status,
                'ict_director_status' => $record->ict_director_status,
                'head_it_status' => $record->head_it_status,
                'ict_officer_status' => $record->ict_officer_status
            ];
            
            // Check if mapping matches
            $matches = true;
            foreach ($expectedMapping as $column => $expectedValue) {
                if ($actualMapping[$column] !== $expectedValue) {
                    $matches = false;
                    break;
                }
            }
            
            if (!$matches) {
                echo "WARNING: Mapping mismatch for record ID {$record->id} with status '{$originalStatus}'\n";
                echo "Expected: " . json_encode($expectedMapping) . "\n";
                echo "Actual: " . json_encode($actualMapping) . "\n";
            }
        }
        
        echo "Status distribution:\n";
        foreach ($statusCounts as $status => $count) {
            echo "- {$status}: {$count} records\n";
        }
        
        // Check if all records have at least one status column populated
        $recordsWithoutStatus = UserAccess::whereNull('hod_status')
            ->whereNull('divisional_status')
            ->whereNull('ict_director_status')
            ->whereNull('head_it_status')
            ->whereNull('ict_officer_status')
            ->count();
            
        if ($recordsWithoutStatus > 0) {
            echo "WARNING: {$recordsWithoutStatus} records have no status columns populated!\n";
        } else {
            echo "✓ All records have at least one status column populated\n";
        }
    }
};
