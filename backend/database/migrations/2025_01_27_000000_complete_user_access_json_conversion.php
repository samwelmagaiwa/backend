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
            echo "🔄 Completing user_access JSON conversion...\n";
            
            // Check if table exists first
            if (!Schema::hasTable('user_access')) {
                echo "⚠️ Table user_access does not exist. Skipping JSON conversion.\n";
                echo "This migration will run automatically when the table is created.\n";
                return;
            }
            
            // Check current column type
            $columns = DB::select("SHOW COLUMNS FROM user_access WHERE Field = 'request_type'");
            if (empty($columns)) {
                echo "⚠️ Column request_type does not exist. Skipping conversion.\n";
                return;
            }

            $currentType = strtolower($columns[0]->Type);
            echo "Current request_type column type: {$currentType}\n";

            // If it's still ENUM, convert to JSON
            if (strpos($currentType, 'enum') !== false) {
                echo "Converting ENUM to JSON...\n";
                
                // Convert column to JSON
                DB::statement('ALTER TABLE user_access MODIFY COLUMN request_type JSON');
                
                // Convert existing ENUM values to JSON arrays
                DB::statement("
                    UPDATE user_access 
                    SET request_type = JSON_ARRAY(request_type)
                    WHERE JSON_VALID(request_type) = 0
                ");
                
                echo "✅ request_type converted to JSON\n";
            } else {
                echo "✅ request_type is already in correct format\n";
            }

            // Check and fix purpose column if needed
            $purposeColumns = DB::select("SHOW COLUMNS FROM user_access WHERE Field = 'purpose'");
            if (!empty($purposeColumns)) {
                $purposeType = strtolower($purposeColumns[0]->Type);
                echo "Current purpose column type: {$purposeType}\n";
                
                if (strpos($purposeType, 'json') === false) {
                    echo "Converting purpose to JSON...\n";
                    
                    // Convert to JSON
                    DB::statement('ALTER TABLE user_access MODIFY COLUMN purpose JSON NULL');
                    
                    // Convert existing text values to JSON arrays where not null
                    DB::statement("
                        UPDATE user_access 
                        SET purpose = JSON_ARRAY(purpose)
                        WHERE purpose IS NOT NULL 
                        AND purpose != '' 
                        AND JSON_VALID(purpose) = 0
                    ");
                    
                    echo "✅ purpose converted to JSON\n";
                } else {
                    echo "✅ purpose is already in correct format\n";
                }
            }

            // Verify final state
            $finalColumns = DB::select("SHOW COLUMNS FROM user_access WHERE Field IN ('request_type', 'purpose')");
            echo "📋 Final column types:\n";
            foreach ($finalColumns as $column) {
                echo "  {$column->Field}: {$column->Type}\n";
            }

        } catch (\Exception $e) {
            echo "❌ Migration failed: " . $e->getMessage() . "\n";
            throw $e;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            echo "🔄 Reverting JSON conversion...\n";
            
            // Check if table exists first
            if (!Schema::hasTable('user_access')) {
                echo "⚠️ Table user_access does not exist. Nothing to revert.\n";
                return;
            }
            
            // Convert JSON arrays back to single values and change column type
            DB::statement("
                UPDATE user_access 
                SET request_type = JSON_UNQUOTE(JSON_EXTRACT(request_type, '$[0]'))
                WHERE JSON_VALID(request_type) = 1
            ");
            
            // Convert back to ENUM
            DB::statement("
                ALTER TABLE user_access 
                MODIFY COLUMN request_type ENUM('jeeva_access', 'wellsoft', 'internet_access_request')
            ");
            
            // Convert purpose back to TEXT
            DB::statement("
                UPDATE user_access 
                SET purpose = JSON_UNQUOTE(JSON_EXTRACT(purpose, '$[0]'))
                WHERE JSON_VALID(purpose) = 1
            ");
            
            DB::statement('ALTER TABLE user_access MODIFY COLUMN purpose TEXT NULL');
            
            echo "✅ Reverted to ENUM and TEXT format\n";
            
        } catch (\Exception $e) {
            echo "❌ Rollback failed: " . $e->getMessage() . "\n";
            throw $e;
        }
    }
};