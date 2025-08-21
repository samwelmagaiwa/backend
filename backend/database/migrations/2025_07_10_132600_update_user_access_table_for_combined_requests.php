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
            // Check if table exists
            if (!Schema::hasTable('user_access')) {
                throw new \Exception('Table user_access does not exist');
            }

            // Check current column type
            $columns = DB::select("SHOW COLUMNS FROM user_access WHERE Field = 'request_type'");
            if (empty($columns)) {
                throw new \Exception('Column request_type does not exist');
            }

            $currentType = strtolower($columns[0]->Type);
            
            // Only proceed if it's currently an ENUM
            if (strpos($currentType, 'enum') !== false) {
                // First, add a temporary column
                DB::statement('ALTER TABLE user_access ADD COLUMN request_type_temp JSON NULL');
                
                // Copy data from old column to new column (convert single values to JSON arrays)
                DB::statement("
                    UPDATE user_access 
                    SET request_type_temp = JSON_ARRAY(request_type)
                    WHERE request_type IS NOT NULL
                ");
                
                // Drop the old column
                DB::statement('ALTER TABLE user_access DROP COLUMN request_type');
                
                // Rename the temp column
                DB::statement('ALTER TABLE user_access CHANGE COLUMN request_type_temp request_type JSON NULL');
                
                echo "Successfully converted request_type from ENUM to JSON\n";
            } else {
                echo "Column request_type is already JSON or not ENUM, skipping conversion\n";
            }
            
            // Handle purpose column conversion
            $purposeColumns = DB::select("SHOW COLUMNS FROM user_access WHERE Field = 'purpose'");
            if (!empty($purposeColumns)) {
                $purposeType = strtolower($purposeColumns[0]->Type);
                
                if (strpos($purposeType, 'text') !== false) {
                    // Convert TEXT to JSON
                    DB::statement('ALTER TABLE user_access ADD COLUMN purpose_temp JSON NULL');
                    
                    // Copy data, converting text to JSON array if not empty
                    DB::statement("
                        UPDATE user_access 
                        SET purpose_temp = CASE 
                            WHEN purpose IS NOT NULL AND purpose != '' THEN JSON_ARRAY(purpose)
                            ELSE NULL 
                        END
                    ");
                    
                    DB::statement('ALTER TABLE user_access DROP COLUMN purpose');
                    DB::statement('ALTER TABLE user_access CHANGE COLUMN purpose_temp purpose JSON NULL');
                    
                    echo "Successfully converted purpose from TEXT to JSON\n";
                } else {
                    echo "Column purpose is already JSON or not TEXT, skipping conversion\n";
                }
            }
            
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
            // Check if table exists
            if (!Schema::hasTable('user_access')) {
                return;
            }

            // Convert request_type back from JSON to ENUM
            $columns = DB::select("SHOW COLUMNS FROM user_access WHERE Field = 'request_type'");
            if (!empty($columns)) {
                $currentType = strtolower($columns[0]->Type);
                
                if (strpos($currentType, 'json') !== false) {
                    // Add temporary ENUM column
                    DB::statement("ALTER TABLE user_access ADD COLUMN request_type_temp ENUM('jeeva_access', 'wellsoft', 'internet_access_request') NULL");
                    
                    // Copy first value from JSON array to ENUM
                    DB::statement("
                        UPDATE user_access 
                        SET request_type_temp = JSON_UNQUOTE(JSON_EXTRACT(request_type, '$[0]'))
                        WHERE request_type IS NOT NULL
                    ");
                    
                    DB::statement('ALTER TABLE user_access DROP COLUMN request_type');
                    DB::statement('ALTER TABLE user_access CHANGE COLUMN request_type_temp request_type ENUM(\'jeeva_access\', \'wellsoft\', \'internet_access_request\') NULL');
                }
            }
            
            // Convert purpose back from JSON to TEXT
            $purposeColumns = DB::select("SHOW COLUMNS FROM user_access WHERE Field = 'purpose'");
            if (!empty($purposeColumns)) {
                $purposeType = strtolower($purposeColumns[0]->Type);
                
                if (strpos($purposeType, 'json') !== false) {
                    DB::statement('ALTER TABLE user_access ADD COLUMN purpose_temp TEXT NULL');
                    
                    // Convert JSON array back to text (join with commas)
                    DB::statement("
                        UPDATE user_access 
                        SET purpose_temp = JSON_UNQUOTE(JSON_EXTRACT(purpose, '$[0]'))
                        WHERE purpose IS NOT NULL
                    ");
                    
                    DB::statement('ALTER TABLE user_access DROP COLUMN purpose');
                    DB::statement('ALTER TABLE user_access CHANGE COLUMN purpose_temp purpose TEXT NULL');
                }
            }
            
        } catch (\Exception $e) {
            echo "Rollback failed: " . $e->getMessage() . "\n";
            throw $e;
        }
    }
};