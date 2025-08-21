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

            echo "Checking current table structure...\n";
            
            // Get current column information
            $columns = DB::select("SHOW COLUMNS FROM user_access WHERE Field = 'request_type'");
            if (empty($columns)) {
                throw new \Exception('Column request_type does not exist');
            }

            $currentType = strtolower($columns[0]->Type);
            echo "Current column type: {$currentType}\n";

            // Check for constraints on the table (MySQL version compatible)
            $constraints = [];
            try {
                // Try MySQL 8.0+ approach first
                $constraints = DB::select("
                    SELECT CONSTRAINT_NAME, CHECK_CLAUSE 
                    FROM INFORMATION_SCHEMA.CHECK_CONSTRAINTS 
                    WHERE CONSTRAINT_SCHEMA = DATABASE() 
                    AND TABLE_NAME = 'user_access'
                    AND CONSTRAINT_NAME LIKE '%request_type%'
                ");
            } catch (\Exception $e) {
                echo "MySQL 8.0+ check constraints not available, trying alternative approach...\n";
                
                // For older MySQL versions, try to get table constraints
                try {
                    $tableStatus = DB::select("SHOW CREATE TABLE user_access");
                    if (!empty($tableStatus)) {
                        $createStatement = $tableStatus[0]->{'Create Table'};
                        echo "Table creation statement analysis...\n";
                        
                        // Look for CHECK constraints in the CREATE TABLE statement
                        if (preg_match('/CONSTRAINT.*request_type.*CHECK/i', $createStatement)) {
                            echo "Found CHECK constraint in table definition\n";
                            // We'll handle this by recreating the column
                        } else {
                            echo "No CHECK constraints found in table definition\n";
                        }
                    }
                } catch (\Exception $e2) {
                    echo "Could not analyze table constraints: " . $e2->getMessage() . "\n";
                }
            }

            if (!empty($constraints)) {
                echo "Found constraints on request_type column:\n";
                foreach ($constraints as $constraint) {
                    echo "- {$constraint->CONSTRAINT_NAME}\n";
                    
                    try {
                        // Drop the constraint
                        DB::statement("ALTER TABLE user_access DROP CHECK {$constraint->CONSTRAINT_NAME}");
                        echo "Dropped constraint: {$constraint->CONSTRAINT_NAME}\n";
                    } catch (\Exception $e) {
                        echo "Could not drop constraint {$constraint->CONSTRAINT_NAME}: " . $e->getMessage() . "\n";
                    }
                }
            } else {
                echo "No check constraints found on request_type column\n";
            }

            // Now fix the column type based on what we want
            if (strpos($currentType, 'longtext') !== false || strpos($currentType, 'text') !== false) {
                echo "Converting column from TEXT/LONGTEXT to proper format...\n";
                
                try {
                    // First, let's try to add a new column and copy data
                    echo "Adding temporary column...\n";
                    DB::statement("ALTER TABLE user_access ADD COLUMN request_type_new ENUM('jeeva_access', 'wellsoft', 'internet_access_request') NULL");
                    
                    // Copy valid data from old column to new column
                    echo "Copying valid data...\n";
                    DB::statement("
                        UPDATE user_access 
                        SET request_type_new = CASE 
                            WHEN request_type = 'jeeva_access' THEN 'jeeva_access'
                            WHEN request_type = 'wellsoft' THEN 'wellsoft' 
                            WHEN request_type = 'internet_access_request' THEN 'internet_access_request'
                            ELSE 'jeeva_access'
                        END
                        WHERE request_type IS NOT NULL
                    ");
                    
                    // Drop the old column
                    echo "Dropping old column...\n";
                    DB::statement("ALTER TABLE user_access DROP COLUMN request_type");
                    
                    // Rename the new column
                    echo "Renaming new column...\n";
                    DB::statement("ALTER TABLE user_access CHANGE COLUMN request_type_new request_type ENUM('jeeva_access', 'wellsoft', 'internet_access_request') NULL");
                    
                    echo "Successfully converted request_type to ENUM\n";
                    
                } catch (\Exception $e) {
                    echo "Column conversion failed: " . $e->getMessage() . "\n";
                    echo "Trying direct conversion...\n";
                    
                    try {
                        // Try direct conversion as fallback
                        DB::statement("
                            ALTER TABLE user_access 
                            MODIFY COLUMN request_type ENUM('jeeva_access', 'wellsoft', 'internet_access_request') NULL
                        ");
                        echo "Direct conversion successful\n";
                    } catch (\Exception $e2) {
                        echo "Direct conversion also failed: " . $e2->getMessage() . "\n";
                        throw $e2;
                    }
                }
                
            } else if (strpos($currentType, 'json') !== false) {
                echo "Column is already JSON, no conversion needed\n";
            } else if (strpos($currentType, 'enum') !== false) {
                echo "Column is already ENUM, checking values...\n";
                
                // Check if the ENUM has the right values
                if (strpos($currentType, 'jeeva_access') === false || 
                    strpos($currentType, 'wellsoft') === false || 
                    strpos($currentType, 'internet_access_request') === false) {
                    
                    echo "ENUM values are incorrect, updating...\n";
                    try {
                        DB::statement("
                            ALTER TABLE user_access 
                            MODIFY COLUMN request_type ENUM('jeeva_access', 'wellsoft', 'internet_access_request') NULL
                        ");
                        echo "Updated ENUM values\n";
                    } catch (\Exception $e) {
                        echo "Failed to update ENUM values: " . $e->getMessage() . "\n";
                        throw $e;
                    }
                } else {
                    echo "ENUM values are correct\n";
                }
            }

            // Verify the final state
            $finalColumns = DB::select("SHOW COLUMNS FROM user_access WHERE Field = 'request_type'");
            $finalType = $finalColumns[0]->Type;
            echo "Final column type: {$finalType}\n";

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
        // This migration is a fix, so we don't need to reverse it
        echo "This is a fix migration, no rollback needed\n";
    }
};