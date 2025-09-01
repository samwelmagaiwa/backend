<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

// Load environment variables
if (file_exists(__DIR__ . '/.env')) {
    $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}

// Setup database connection
$capsule = new Capsule;
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => $_ENV['DB_HOST'] ?? '127.0.0.1',
    'database' => $_ENV['DB_DATABASE'] ?? 'backend-api-vue',
    'username' => $_ENV['DB_USERNAME'] ?? 'root',
    'password' => $_ENV['DB_PASSWORD'] ?? '',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

echo "🔧 Fixing migration issue...\n";

try {
    $pdo = $capsule->getConnection()->getPdo();
    echo "✅ Database connection successful\n";
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

try {
    $schema = $capsule->schema();
    
    // Check if migrations table exists
    if ($schema->hasTable('migrations')) {
        echo "✅ Migrations table exists\n";
        
        // Remove the problematic migration entry if it exists
        $deleted = $capsule->table('migrations')
            ->where('migration', '2025_01_27_120000_add_divisional_director_to_departments_table')
            ->delete();
            
        if ($deleted > 0) {
            echo "✅ Removed problematic migration entry from database\n";
        } else {
            echo "ℹ️  Problematic migration entry not found in database\n";
        }
        
        // Also remove the users migration entry if it exists
        $deleted2 = $capsule->table('migrations')
            ->where('migration', '2025_01_27_130000_add_is_active_to_users_table')
            ->delete();
            
        if ($deleted2 > 0) {
            echo "✅ Removed problematic users migration entry from database\n";
        } else {
            echo "ℹ️  Problematic users migration entry not found in database\n";
        }
        
    } else {
        echo "ℹ️  Migrations table does not exist - this is normal for a fresh installation\n";
    }
    
    echo "\n✅ Migration cleanup complete\n";
    echo "\n📋 Next steps:\n";
    echo "1. Run: php artisan migrate\n";
    echo "2. This should now work without errors\n";
    
} catch (Exception $e) {
    echo "❌ Error during cleanup: " . $e->getMessage() . "\n";
}