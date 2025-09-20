<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

try {
    // Create PDO connection
    $host = $_ENV['DB_HOST'];
    $port = $_ENV['DB_PORT'];
    $database = $_ENV['DB_DATABASE'];
    $username = $_ENV['DB_USERNAME'];
    $password = $_ENV['DB_PASSWORD'];
    
    $dsn = "mysql:host={$host};port={$port};dbname={$database}";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    echo "✅ Database connection successful!\n";
    echo "Database: {$database}\n\n";
    
    // Check if user_access table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'user_access'");
    if ($stmt->rowCount() == 0) {
        echo "❌ user_access table does not exist!\n";
        echo "Please run migrations first: php artisan migrate\n";
        exit(1);
    }
    
    echo "✅ user_access table exists\n\n";
    
    // Get table structure
    $stmt = $pdo->query("SHOW COLUMNS FROM user_access");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $requiredColumns = [
        'hod_signature_path',
        'wellsoft_modules',
        'wellsoft_modules_selected', 
        'jeeva_modules',
        'jeeva_modules_selected',
        'access_type',
        'module_requested_for',
        'temporary_until'
    ];
    
    $existingColumns = array_column($columns, 'Field');
    
    echo "=== USER_ACCESS TABLE STRUCTURE ===\n";
    foreach ($columns as $column) {
        $nullable = $column['Null'] === 'YES' ? 'NULL' : 'NOT NULL';
        $default = $column['Default'] !== null ? "DEFAULT '{$column['Default']}'" : '';
        echo sprintf("%-30s %-20s %-10s %s\n", 
            $column['Field'], 
            $column['Type'], 
            $nullable,
            $default
        );
    }
    
    echo "\n=== REQUIRED COLUMNS CHECK ===\n";
    $missingColumns = [];
    foreach ($requiredColumns as $column) {
        if (in_array($column, $existingColumns)) {
            echo "✅ " . $column . " - EXISTS\n";
        } else {
            echo "❌ " . $column . " - MISSING\n";
            $missingColumns[] = $column;
        }
    }
    
    if (!empty($missingColumns)) {
        echo "\n⚠️  MISSING COLUMNS DETECTED!\n";
        echo "The following columns are missing from the user_access table:\n";
        foreach ($missingColumns as $column) {
            echo "- " . $column . "\n";
        }
        echo "\nPlease run the following migrations:\n";
        echo "php artisan migrate\n";
        
        // Check which migrations might be pending
        echo "\n=== CHECKING MIGRATION STATUS ===\n";
        $stmt = $pdo->query("SHOW TABLES LIKE 'migrations'");
        if ($stmt->rowCount() > 0) {
            $stmt = $pdo->query("SELECT migration FROM migrations ORDER BY batch DESC, id DESC LIMIT 10");
            $migrations = $stmt->fetchAll(PDO::FETCH_COLUMN);
            echo "Last 10 migrations run:\n";
            foreach ($migrations as $migration) {
                echo "- " . $migration . "\n";
            }
        } else {
            echo "❌ migrations table does not exist - Laravel not properly set up\n";
        }
    } else {
        echo "\n✅ All required columns exist!\n";
        
        // Test a sample query to see if data can be retrieved
        echo "\n=== TESTING DATA RETRIEVAL ===\n";
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM user_access");
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        echo "Total records in user_access: {$count}\n";
        
        if ($count > 0) {
            // Get a sample record to check field values
            $stmt = $pdo->query("SELECT id, wellsoft_modules_selected, jeeva_modules_selected, access_type, module_requested_for, temporary_until, hod_signature_path FROM user_access ORDER BY id DESC LIMIT 1");
            $sample = $stmt->fetch(PDO::FETCH_ASSOC);
            
            echo "\nSample record (ID: {$sample['id']}):\n";
            foreach ($requiredColumns as $column) {
                $value = $sample[$column] ?? 'NULL';
                if (is_null($value)) {
                    $value = 'NULL';
                } elseif (empty($value)) {
                    $value = 'EMPTY';
                }
                echo "- {$column}: {$value}\n";
            }
        }
    }
    
} catch (PDOException $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}