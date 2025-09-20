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
        // Add foreign keys to user_access table
        if (Schema::hasTable('user_access')) {
            Schema::table('user_access', function (Blueprint $table) {
                // Add foreign key constraints now that the required tables exist
                if (Schema::hasTable('users') && !$this->foreignKeyExists('user_access', 'user_access_user_id_foreign')) {
                    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                }
                
                if (Schema::hasTable('users') && !$this->foreignKeyExists('user_access', 'user_access_cancelled_by_foreign')) {
                    $table->foreign('cancelled_by')->references('id')->on('users')->onDelete('set null');
                }
                
                if (Schema::hasTable('departments') && !$this->foreignKeyExists('user_access', 'user_access_department_id_foreign')) {
                    $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
                }
            });
            
            echo "✅ Added foreign key constraints to user_access table\n";
        }
        
        // Add foreign keys to device_inventory table
        if (Schema::hasTable('device_inventory')) {
            Schema::table('device_inventory', function (Blueprint $table) {
                if (Schema::hasTable('users') && !$this->foreignKeyExists('device_inventory', 'device_inventory_created_by_foreign')) {
                    $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
                }
                
                if (Schema::hasTable('users') && !$this->foreignKeyExists('device_inventory', 'device_inventory_updated_by_foreign')) {
                    $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
                }
            });
            
            echo "✅ Added foreign key constraints to device_inventory table\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove foreign keys from user_access table
        if (Schema::hasTable('user_access')) {
            Schema::table('user_access', function (Blueprint $table) {
                // Drop foreign key constraints
                if ($this->foreignKeyExists('user_access', 'user_access_user_id_foreign')) {
                    $table->dropForeign('user_access_user_id_foreign');
                }
                
                if ($this->foreignKeyExists('user_access', 'user_access_cancelled_by_foreign')) {
                    $table->dropForeign('user_access_cancelled_by_foreign');
                }
                
                if ($this->foreignKeyExists('user_access', 'user_access_department_id_foreign')) {
                    $table->dropForeign('user_access_department_id_foreign');
                }
            });
            
            echo "✅ Removed foreign key constraints from user_access table\n";
        }
        
        // Remove foreign keys from device_inventory table
        if (Schema::hasTable('device_inventory')) {
            Schema::table('device_inventory', function (Blueprint $table) {
                if ($this->foreignKeyExists('device_inventory', 'device_inventory_created_by_foreign')) {
                    $table->dropForeign('device_inventory_created_by_foreign');
                }
                
                if ($this->foreignKeyExists('device_inventory', 'device_inventory_updated_by_foreign')) {
                    $table->dropForeign('device_inventory_updated_by_foreign');
                }
            });
            
            echo "✅ Removed foreign key constraints from device_inventory table\n";
        }
    }
    
    /**
     * Check if a foreign key constraint exists
     */
    private function foreignKeyExists(string $table, string $constraintName): bool
    {
        $result = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = ? 
            AND CONSTRAINT_NAME = ?
        ", [$table, $constraintName]);
        
        return !empty($result);
    }
};
