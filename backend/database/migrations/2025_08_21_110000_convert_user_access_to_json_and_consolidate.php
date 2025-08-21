<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        try {
            echo "Starting conversion to JSON and consolidation...\n";
            
            // Step 1: Convert request_type column to JSON
            echo "Converting request_type column to JSON...\n";
            DB::statement('ALTER TABLE user_access MODIFY COLUMN request_type JSON');
            
            // Step 2: Convert purpose column to JSON if it's not already
            $purposeColumns = DB::select("SHOW COLUMNS FROM user_access WHERE Field = 'purpose'");
            if (!empty($purposeColumns)) {
                $purposeType = strtolower($purposeColumns[0]->Type);
                if (strpos($purposeType, 'json') === false) {
                    echo "Converting purpose column to JSON...\n";
                    DB::statement('ALTER TABLE user_access MODIFY COLUMN purpose JSON NULL');
                }
            }
            
            // Step 3: Consolidate duplicate records
            echo "Consolidating duplicate records...\n";
            
            // Get all records grouped by user_id, pf_number, staff_name, phone_number, department_id, signature_path
            $duplicateGroups = DB::select("
                SELECT 
                    user_id, pf_number, staff_name, phone_number, department_id, signature_path,
                    MIN(id) as keep_id,
                    GROUP_CONCAT(DISTINCT request_type) as request_types,
                    GROUP_CONCAT(DISTINCT purpose) as purposes,
                    MIN(created_at) as earliest_created,
                    MAX(updated_at) as latest_updated,
                    status
                FROM user_access 
                GROUP BY user_id, pf_number, staff_name, phone_number, department_id, signature_path, status
                HAVING COUNT(*) > 1
            ");
            
            echo "Found " . count($duplicateGroups) . " groups with duplicates\n";
            
            foreach ($duplicateGroups as $group) {
                // Parse request types
                $requestTypes = array_filter(array_unique(explode(',', $group->request_types)));
                $requestTypesJson = json_encode(array_values($requestTypes));
                
                // Parse purposes (filter out NULL and empty values)
                $purposes = array_filter(
                    array_unique(explode(',', $group->purposes)), 
                    function($purpose) {
                        return $purpose !== null && $purpose !== '' && $purpose !== 'NULL';
                    }
                );
                $purposesJson = !empty($purposes) ? json_encode(array_values($purposes)) : null;
                
                echo "Consolidating PF {$group->pf_number}: " . implode(', ', $requestTypes) . "\n";
                
                // Update the record we're keeping
                DB::table('user_access')
                    ->where('id', $group->keep_id)
                    ->update([
                        'request_type' => $requestTypesJson,
                        'purpose' => $purposesJson,
                        'updated_at' => $group->latest_updated
                    ]);
                
                // Delete the duplicate records
                DB::table('user_access')
                    ->where('user_id', $group->user_id)
                    ->where('pf_number', $group->pf_number)
                    ->where('staff_name', $group->staff_name)
                    ->where('phone_number', $group->phone_number)
                    ->where('department_id', $group->department_id)
                    ->where('signature_path', $group->signature_path)
                    ->where('status', $group->status)
                    ->where('id', '!=', $group->keep_id)
                    ->delete();
            }
            
            // Step 4: Convert remaining single request_type values to JSON arrays
            echo "Converting remaining single values to JSON arrays...\n";
            DB::statement("
                UPDATE user_access 
                SET request_type = JSON_ARRAY(request_type)
                WHERE JSON_VALID(request_type) = 0
            ");
            
            // Step 5: Convert remaining single purpose values to JSON arrays where applicable
            DB::statement("
                UPDATE user_access 
                SET purpose = JSON_ARRAY(purpose)
                WHERE purpose IS NOT NULL 
                AND purpose != '' 
                AND JSON_VALID(purpose) = 0
            ");
            
            echo "Conversion and consolidation completed successfully!\n";
            
        } catch (\Exception $e) {
            echo "Migration failed: " . $e->getMessage() . "\n";
            throw $e;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            echo "Reverting JSON conversion and expanding consolidated records...\n";
            
            // Get all records with JSON request_types
            $jsonRecords = DB::select("
                SELECT id, user_id, pf_number, staff_name, phone_number, department_id, 
                       signature_path, purpose, request_type, status, created_at, updated_at
                FROM user_access 
                WHERE JSON_VALID(request_type) = 1
            ");
            
            foreach ($jsonRecords as $record) {
                $requestTypes = json_decode($record->request_type, true);
                $purposes = json_decode($record->purpose, true);
                
                if (is_array($requestTypes) && count($requestTypes) > 1) {
                    // Keep the first request type in the original record
                    DB::table('user_access')
                        ->where('id', $record->id)
                        ->update([
                            'request_type' => $requestTypes[0],
                            'purpose' => is_array($purposes) && !empty($purposes) ? $purposes[0] : $record->purpose
                        ]);
                    
                    // Create new records for additional request types
                    for ($i = 1; $i < count($requestTypes); $i++) {
                        DB::table('user_access')->insert([
                            'user_id' => $record->user_id,
                            'pf_number' => $record->pf_number,
                            'staff_name' => $record->staff_name,
                            'phone_number' => $record->phone_number,
                            'department_id' => $record->department_id,
                            'signature_path' => $record->signature_path,
                            'purpose' => is_array($purposes) && isset($purposes[$i]) ? $purposes[$i] : null,
                            'request_type' => $requestTypes[$i],
                            'status' => $record->status,
                            'created_at' => $record->created_at,
                            'updated_at' => $record->updated_at
                        ]);
                    }
                }
            }
            
            // Convert columns back to original types
            DB::statement("ALTER TABLE user_access MODIFY COLUMN request_type ENUM('jeeva_access', 'wellsoft', 'internet_access_request')");
            DB::statement('ALTER TABLE user_access MODIFY COLUMN purpose TEXT NULL');
            
            echo "Rollback completed successfully!\n";
            
        } catch (\Exception $e) {
            echo "Rollback failed: " . $e->getMessage() . "\n";
            throw $e;
        }
    }
};