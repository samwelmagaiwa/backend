<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Setup database connection
$capsule = new Capsule;
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => $_ENV['DB_HOST'],
    'database' => $_ENV['DB_DATABASE'],
    'username' => $_ENV['DB_USERNAME'],
    'password' => $_ENV['DB_PASSWORD'],
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

echo "🔍 Checking database connection...\n";

try {
    $pdo = $capsule->getConnection()->getPdo();
    echo "✅ Database connection successful\n";
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n🔍 Checking if migrations table exists...\n";

try {
    $schema = $capsule->schema();
    
    if ($schema->hasTable('migrations')) {
        echo "✅ Migrations table exists\n";
        
        // Get all migrations that have been run
        $migrations = $capsule->table('migrations')->orderBy('batch')->get();
        
        echo "\n📋 Migrations that have been run:\n";
        foreach ($migrations as $migration) {
            echo "  - {$migration->migration} (batch: {$migration->batch})\n";
        }
        
        // Check if the problematic migration is in the table
        $problematicMigration = $capsule->table('migrations')
            ->where('migration', '2025_01_27_120000_add_divisional_director_to_departments_table')
            ->first();
            
        if ($problematicMigration) {
            echo "\n⚠️  Found problematic migration in database: 2025_01_27_120000_add_divisional_director_to_departments_table\n";
            echo "   This needs to be removed from the migrations table\n";
        } else {
            echo "\n✅ Problematic migration not found in database\n";
        }
        
    } else {
        echo "❌ Migrations table does not exist\n";
        echo "   You may need to run: php artisan migrate:install\n";
    }
    
    echo "\n🔍 Checking if departments table exists...\n";
    
    if ($schema->hasTable('departments')) {
        echo "✅ Departments table exists\n";
        
        // Check if it has the columns we're trying to add
        if ($schema->hasColumn('departments', 'divisional_director_id')) {
            echo "✅ Column 'divisional_director_id' already exists\n";
        } else {
            echo "❌ Column 'divisional_director_id' does not exist\n";
        }
        
        if ($schema->hasColumn('departments', 'has_divisional_director')) {
            echo "✅ Column 'has_divisional_director' already exists\n";
        } else {
            echo "❌ Column 'has_divisional_director' does not exist\n";
        }
        
    } else {
        echo "❌ Departments table does not exist\n";
        echo "   This is the root cause of the migration failure\n";
    }
    
    echo "\n🔍 Checking if users table exists...\n";
    
    if ($schema->hasTable('users')) {
        echo "✅ Users table exists\n";
        
        // Check if it has the is_active column
        if ($schema->hasColumn('users', 'is_active')) {
            echo "✅ Column 'is_active' already exists\n";
        } else {
            echo "❌ Column 'is_active' does not exist\n";
        }
        
    } else {
        echo "❌ Users table does not exist\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error checking database: " . $e->getMessage() . "\n";
}

echo "\n✅ Database check complete\n";