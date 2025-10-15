<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserAccess;

class FixRequestIds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:request-ids';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix NULL request_id values in user_access table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Checking for UserAccess records with NULL request_id...');
        
        // Get all records where request_id is NULL
        $nullRecords = UserAccess::whereNull('request_id')->get();
        
        if ($nullRecords->isEmpty()) {
            $this->info('✅ No records with NULL request_id found. All good!');
            return 0;
        }
        
        $this->warn("⚠️  Found {$nullRecords->count()} records with NULL request_id");
        
        if (!$this->confirm('Do you want to fix these records?')) {
            $this->info('Operation cancelled.');
            return 0;
        }
        
        $fixed = 0;
        
        foreach ($nullRecords as $record) {
            $generatedId = 'REQ-' . str_pad($record->id, 6, '0', STR_PAD_LEFT);
            
            $this->info("Fixing ID {$record->id}: {$record->staff_name} -> {$generatedId}");
            
            $record->request_id = $generatedId;
            if ($record->saveQuietly()) {
                $fixed++;
                $this->info("  ✅ Fixed!");
            } else {
                $this->error("  ❌ Failed to save!");
            }
        }
        
        $this->info("\n🎉 Fixed {$fixed} out of {$nullRecords->count()} records!");
        
        return 0;
    }
}
