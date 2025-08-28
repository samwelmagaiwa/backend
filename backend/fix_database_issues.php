<?php

require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "🔧 Fixing database issues...\n\n";

try {
    // Check current database connection
    DB::connection()->getPdo();
    echo "✅ Database connection successful\n";
    
    // Get current database name
    $database = DB::connection()->getDatabaseName();
    echo "📊 Working with database: {$database}\n\n";
    
    // 1. Check and fix users table structure
    echo "1️⃣ Checking users table structure...\n";
    
    if (!Schema::hasTable('users')) {
        echo "❌ Users table doesn't exist!\n";
        exit(1);
    }
    
    // Check existing columns
    $columns = Schema::getColumnListing('users');
    echo "   Current columns: " . implode(', ', $columns) . "\n";
    
    // Check existing indexes
    $indexes = DB::select("SHOW INDEX FROM users");
    echo "   Current indexes:\n";
    foreach ($indexes as $index) {
        echo "   - {$index->Key_name} on {$index->Column_name}\n";
    }
    
    // 2. Remove duplicate indexes if they exist
    echo "\n2️⃣ Cleaning up duplicate indexes...\n";
    
    $indexesToCheck = [
        'users_pf_number_index',
        'users_is_active_index', 
        'users_department_id_is_active_index',
        'users_pf_number_unique'
    ];
    
    foreach ($indexesToCheck as $indexName) {
        $exists = DB::select("SHOW INDEX FROM users WHERE Key_name = ?", [$indexName]);
        if (!empty($exists)) {
            echo "   Dropping existing index: {$indexName}\n";
            try {
                DB::statement("ALTER TABLE users DROP INDEX `{$indexName}`");
                echo "   ✅ Dropped {$indexName}\n";
            } catch (Exception $e) {
                echo "   ⚠️  Could not drop {$indexName}: " . $e->getMessage() . "\n";
            }
        }
    }
    
    // 3. Add missing columns
    echo "\n3️⃣ Adding missing columns...\n";
    
    $columnsToAdd = [
        'staff_name' => "ALTER TABLE users ADD COLUMN staff_name VARCHAR(255) NULL AFTER name",
        'is_active' => "ALTER TABLE users ADD COLUMN is_active BOOLEAN DEFAULT 1 AFTER password", 
        'department_id' => "ALTER TABLE users ADD COLUMN department_id BIGINT UNSIGNED NULL AFTER phone",
        'pf_number' => "ALTER TABLE users ADD COLUMN pf_number VARCHAR(50) NULL AFTER email"
    ];
    
    foreach ($columnsToAdd as $column => $sql) {
        if (!Schema::hasColumn('users', $column)) {
            echo "   Adding column: {$column}\n";
            try {
                DB::statement($sql);
                echo "   ✅ Added {$column}\n";
            } catch (Exception $e) {
                echo "   ⚠️  Could not add {$column}: " . $e->getMessage() . "\n";
            }
        } else {
            echo "   ✅ Column {$column} already exists\n";
        }
    }
    
    // 4. Add foreign key constraint for department_id
    echo "\n4️⃣ Adding foreign key constraints...\n";
    
    if (Schema::hasColumn('users', 'department_id') && Schema::hasTable('departments')) {
        // Check if foreign key already exists
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = ? 
            AND TABLE_NAME = 'users' 
            AND COLUMN_NAME = 'department_id' 
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ", [$database]);
        
        if (empty($foreignKeys)) {
            echo "   Adding foreign key constraint for department_id\n";
            try {
                DB::statement("ALTER TABLE users ADD CONSTRAINT users_department_id_foreign FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL");
                echo "   ✅ Added foreign key constraint\n";
            } catch (Exception $e) {
                echo "   ⚠️  Could not add foreign key: " . $e->getMessage() . "\n";
            }
        } else {
            echo "   ✅ Foreign key constraint already exists\n";
        }
    }
    
    // 5. Create cache table if it doesn't exist
    echo "\n5️⃣ Creating cache table...\n";
    
    if (!Schema::hasTable('cache')) {
        echo "   Creating cache table\n";
        try {
            DB::statement("
                CREATE TABLE cache (
                    `key` varchar(255) NOT NULL,
                    `value` mediumtext NOT NULL,
                    `expiration` int NOT NULL,
                    PRIMARY KEY (`key`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
            echo "   ✅ Created cache table\n";
        } catch (Exception $e) {
            echo "   ⚠️  Could not create cache table: " . $e->getMessage() . "\n";
        }
    } else {
        echo "   ✅ Cache table already exists\n";
    }
    
    // 6. Create cache_locks table if it doesn't exist
    if (!Schema::hasTable('cache_locks')) {
        echo "   Creating cache_locks table\n";
        try {
            DB::statement("
                CREATE TABLE cache_locks (
                    `key` varchar(255) NOT NULL,
                    `owner` varchar(255) NOT NULL,
                    `expiration` int NOT NULL,
                    PRIMARY KEY (`key`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
            echo "   ✅ Created cache_locks table\n";
        } catch (Exception $e) {
            echo "   ⚠️  Could not create cache_locks table: " . $e->getMessage() . "\n";
        }
    } else {
        echo "   ✅ Cache_locks table already exists\n";
    }
    
    echo "\n🎉 Database cleanup completed!\n";
    echo "\nNext steps:\n";
    echo "1. Run: php artisan migrate\n";
    echo "2. Run: php artisan cache:clear\n";
    echo "3. Run: php artisan config:clear\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
?>