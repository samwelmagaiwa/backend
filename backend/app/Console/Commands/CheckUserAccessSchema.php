<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CheckUserAccessSchema extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user-access:check-schema 
                            {--fix : Automatically fix schema mismatches}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and optionally fix user_access table schema';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Checking user_access table schema...');
        $this->newLine();

        // Check if table exists
        if (!Schema::hasTable('user_access')) {
            $this->error('❌ Table user_access does not exist!');
            return 1;
        }

        // Get current column information
        $requestTypeColumn = $this->getColumnInfo('request_type');
        $purposeColumn = $this->getColumnInfo('purpose');

        $this->displayCurrentSchema($requestTypeColumn, $purposeColumn);
        
        // Check for schema mismatches
        $issues = $this->checkForIssues($requestTypeColumn, $purposeColumn);
        
        if (empty($issues)) {
            $this->info('✅ Schema is correct! No issues found.');
            return 0;
        }

        $this->displayIssues($issues);

        if ($this->option('fix')) {
            $this->fixIssues($issues, $requestTypeColumn, $purposeColumn);
        } else {
            $this->newLine();
            $this->warn('💡 To automatically fix these issues, run:');
            $this->line('   php artisan user-access:check-schema --fix');
        }

        return 0;
    }

    /**
     * Get column information
     */
    private function getColumnInfo(string $columnName): ?object
    {
        $columns = DB::select("SHOW COLUMNS FROM user_access WHERE Field = ?", [$columnName]);
        return $columns[0] ?? null;
    }

    /**
     * Display current schema
     */
    private function displayCurrentSchema(?object $requestTypeColumn, ?object $purposeColumn): void
    {
        $this->info('📋 Current Schema:');
        $this->table(
            ['Column', 'Type', 'Null', 'Default'],
            [
                [
                    'request_type',
                    $requestTypeColumn->Type ?? 'NOT FOUND',
                    $requestTypeColumn->Null ?? 'N/A',
                    $requestTypeColumn->Default ?? 'NULL'
                ],
                [
                    'purpose',
                    $purposeColumn->Type ?? 'NOT FOUND',
                    $purposeColumn->Null ?? 'N/A',
                    $purposeColumn->Default ?? 'NULL'
                ]
            ]
        );
        $this->newLine();
    }

    /**
     * Check for schema issues
     */
    private function checkForIssues(?object $requestTypeColumn, ?object $purposeColumn): array
    {
        $issues = [];

        // Check request_type column
        if (!$requestTypeColumn) {
            $issues[] = [
                'type' => 'missing_column',
                'column' => 'request_type',
                'description' => 'request_type column is missing'
            ];
        } else {
            $type = strtolower($requestTypeColumn->Type);
            
            if (strpos($type, 'json') === false) {
                $issues[] = [
                    'type' => 'wrong_type',
                    'column' => 'request_type',
                    'current' => $requestTypeColumn->Type,
                    'expected' => 'JSON',
                    'description' => 'request_type should be JSON for array casting'
                ];
            }
        }

        // Check purpose column
        if ($purposeColumn) {
            $type = strtolower($purposeColumn->Type);
            
            if (strpos($type, 'json') === false && strpos($type, 'text') === false) {
                $issues[] = [
                    'type' => 'wrong_type',
                    'column' => 'purpose',
                    'current' => $purposeColumn->Type,
                    'expected' => 'JSON or TEXT',
                    'description' => 'purpose should be JSON or TEXT'
                ];
            }
        }

        return $issues;
    }

    /**
     * Display issues found
     */
    private function displayIssues(array $issues): void
    {
        $this->error('❌ Schema Issues Found:');
        $this->newLine();

        foreach ($issues as $issue) {
            $this->line("🔸 {$issue['description']}");
            if (isset($issue['current']) && isset($issue['expected'])) {
                $this->line("   Current: {$issue['current']}");
                $this->line("   Expected: {$issue['expected']}");
            }
            $this->newLine();
        }
    }

    /**
     * Fix schema issues
     */
    private function fixIssues(array $issues, ?object $requestTypeColumn, ?object $purposeColumn): void
    {
        $this->info('🔧 Fixing schema issues...');
        $this->newLine();

        foreach ($issues as $issue) {
            try {
                switch ($issue['type']) {
                    case 'wrong_type':
                        $this->fixColumnType($issue);
                        break;
                    case 'missing_column':
                        $this->addMissingColumn($issue);
                        break;
                }
            } catch (\Exception $e) {
                $this->error("❌ Failed to fix {$issue['column']}: " . $e->getMessage());
            }
        }

        $this->info('✅ Schema fixes completed!');
        $this->newLine();
        
        // Show updated schema
        $this->info('📋 Updated Schema:');
        $requestTypeColumn = $this->getColumnInfo('request_type');
        $purposeColumn = $this->getColumnInfo('purpose');
        $this->displayCurrentSchema($requestTypeColumn, $purposeColumn);
    }

    /**
     * Fix column type
     */
    private function fixColumnType(array $issue): void
    {
        $column = $issue['column'];
        $this->line("🔄 Converting {$column} to JSON...");

        if ($column === 'request_type') {
            // Convert ENUM to JSON
            DB::statement("
                ALTER TABLE user_access 
                MODIFY COLUMN request_type JSON
            ");
            
            // Convert existing ENUM values to JSON arrays
            DB::statement("
                UPDATE user_access 
                SET request_type = JSON_ARRAY(request_type)
                WHERE JSON_VALID(request_type) = 0
            ");
            
        } elseif ($column === 'purpose') {
            // Convert to JSON
            DB::statement("
                ALTER TABLE user_access 
                MODIFY COLUMN purpose JSON NULL
            ");
            
            // Convert existing text values to JSON arrays where not null
            DB::statement("
                UPDATE user_access 
                SET purpose = JSON_ARRAY(purpose)
                WHERE purpose IS NOT NULL 
                AND purpose != '' 
                AND JSON_VALID(purpose) = 0
            ");
        }

        $this->info("✅ {$column} converted to JSON");
    }

    /**
     * Add missing column
     */
    private function addMissingColumn(array $issue): void
    {
        $column = $issue['column'];
        $this->line("🔄 Adding missing column {$column}...");

        if ($column === 'request_type') {
            DB::statement("
                ALTER TABLE user_access 
                ADD COLUMN request_type JSON NULL
            ");
        }

        $this->info("✅ {$column} column added");
    }
}