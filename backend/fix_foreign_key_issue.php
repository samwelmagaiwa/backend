<?php

require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "🔧 Fixing foreign key constraint issues...\n\n";

try {
    // 1. Check if declarations table exists (might be partially created)
    echo "1️⃣ Checking declarations table status...\n";
    
    if (Schema::hasTable('declarations')) {
        echo "   ⚠️  Declarations table exists (possibly incomplete)\n";
        
        // Check if it has the problematic foreign key
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'declarations' 
            AND COLUMN_NAME = 'user_id' 
            AND REFERENCED_TABLE_NAME = 'users'
        ");
        
        if (!empty($foreignKeys)) {
            echo "   Foreign key constraint exists, dropping table to recreate...\n";
        }
        
        // Drop the table to start fresh
        Schema::dropIfExists('declarations');
        echo "   ✅ Dropped existing declarations table\n";
    } else {
        echo "   ✅ Declarations table doesn't exist (good for fresh creation)\n";
    }
    
    // 2. Verify users table structure
    echo "\n2️⃣ Verifying users table structure...\n";
    
    if (!Schema::hasTable('users')) {
        echo "   ❌ Users table doesn't exist!\n";
        echo "   Creating basic users table structure...\n";
        
        // Create a basic users table if it doesn't exist
        Schema::create('users', function ($table) {
            $table->id(); // BIGINT UNSIGNED AUTO_INCREMENT
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('phone')->nullable();
            $table->string('pf_number', 50)->nullable();
            $table->string('staff_name')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
            
            // Add indexes
            $table->index('email');
            $table->index('pf_number');
            $table->index('is_active');
        });
        
        echo "   ✅ Created users table\n";
    } else {
        echo "   ✅ Users table exists\n";
        
        // Check users.id column type
        $usersIdColumn = DB::select("SHOW COLUMNS FROM users WHERE Field = 'id'");
        if (!empty($usersIdColumn)) {
            $idColumn = $usersIdColumn[0];
            echo "   Users.id type: {$idColumn->Type}\n";
            
            if (strpos(strtolower($idColumn->Type), 'bigint') === false) {
                echo "   ⚠️  WARNING: users.id is not BIGINT type. This may cause foreign key issues.\n";
            } else {
                echo "   ✅ Users.id has correct BIGINT type\n";
            }
        }
    }
    
    // 3. Remove failed migration entry
    echo "\n3️⃣ Cleaning migration table...\n";
    
    $failedMigrations = [
        '2025_01_27_120000_create_declarations_table'
    ];
    
    foreach ($failedMigrations as $migration) {
        $exists = DB::table('migrations')->where('migration', $migration)->exists();
        if ($exists) {
            DB::table('migrations')->where('migration', $migration)->delete();
            echo "   ✅ Removed failed migration: {$migration}\n";
        }
    }
    
    // 4. Create declarations table manually with proper foreign key handling
    echo "\n4️⃣ Creating declarations table with safe foreign key...\n";
    
    // Create table without foreign key first
    DB::statement("
        CREATE TABLE declarations (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id BIGINT UNSIGNED NOT NULL,
            full_name VARCHAR(255) NOT NULL,
            pf_number VARCHAR(255) NOT NULL,
            department VARCHAR(255) NOT NULL,
            job_title VARCHAR(255) NOT NULL,
            signature_date DATE NOT NULL,
            agreement_accepted TINYINT(1) NOT NULL DEFAULT 0,
            signature_info JSON NULL,
            ip_address VARCHAR(255) NULL,
            user_agent TEXT NULL,
            submitted_at TIMESTAMP NOT NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            PRIMARY KEY (id),
            INDEX declarations_user_id_pf_number_index (user_id, pf_number),
            INDEX declarations_submitted_at_index (submitted_at),
            INDEX declarations_pf_number_index (pf_number)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
    echo "   ✅ Created declarations table structure\n";
    
    // Add foreign key constraint separately
    try {
        DB::statement("
            ALTER TABLE declarations 
            ADD CONSTRAINT declarations_user_id_foreign 
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ");
        echo "   ✅ Added foreign key constraint successfully\n";
    } catch (Exception $e) {
        echo "   ⚠️  Could not add foreign key constraint: " . $e->getMessage() . "\n";
        echo "   The table was created but without foreign key constraint.\n";
    }
    
    // Try to add unique constraint on pf_number
    try {
        DB::statement("ALTER TABLE declarations ADD UNIQUE KEY declarations_pf_number_unique (pf_number)");
        echo "   ✅ Added unique constraint on pf_number\n";
    } catch (Exception $e) {
        echo "   ⚠️  Could not add unique constraint on pf_number: " . $e->getMessage() . "\n";
    }
    
    // 5. Verify the fix
    echo "\n5️⃣ Verifying the fix...\n";
    
    if (Schema::hasTable('declarations')) {
        echo "   ✅ Declarations table exists\n";
        
        // Check foreign key
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'declarations' 
            AND COLUMN_NAME = 'user_id' 
            AND REFERENCED_TABLE_NAME = 'users'
        ");
        
        if (!empty($foreignKeys)) {
            echo "   ✅ Foreign key constraint is properly created\n";
        } else {
            echo "   ⚠️  Foreign key constraint is missing (table still functional)\n";
        }
        
        // Show table structure
        echo "\n   📊 Declarations table structure:\n";
        $columns = DB::select("DESCRIBE declarations");
        foreach ($columns as $column) {
            echo "      {$column->Field}: {$column->Type}\n";
        }
    }
    
    echo "\n🎉 Foreign key issue fix completed!\n";
    echo "\nNext steps:\n";
    echo "1. Run: php artisan migrate (to run any remaining migrations)\n";
    echo "2. Test the application functionality\n";
    
} catch (Exception $e) {
    echo "❌ Error during fix: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    
    // Cleanup on error
    try {
        Schema::dropIfExists('declarations');
        echo "\n🧹 Cleaned up partial declarations table\n";
    } catch (Exception $cleanupError) {
        echo "\n⚠️  Could not cleanup: " . $cleanupError->getMessage() . "\n";
    }
    
    exit(1);
}
?>