<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserAccess;

class DebugUserAccess extends Command
{
    protected $signature = 'debug:user-access {id}';
    protected $description = 'Debug user access record';

    public function handle()
    {
        $id = $this->argument('id');
        $record = UserAccess::find($id);
        
        if (!$record) {
            $this->error("Record {$id} not found");
            return 1;
        }
        
        $this->info("Record {$id} found:");
        $this->line('wellsoft_modules: ' . json_encode($record->wellsoft_modules));
        $this->line('jeeva_modules: ' . json_encode($record->jeeva_modules));
        $this->line('wellsoft_modules_selected: ' . json_encode($record->wellsoft_modules_selected));
        $this->line('jeeva_modules_selected: ' . json_encode($record->jeeva_modules_selected));
        $this->line('module_requested_for: ' . ($record->module_requested_for ?? 'NULL'));
        $this->line('hod_signature_path: ' . ($record->hod_signature_path ?? 'NULL'));
        $this->line('hod_name: ' . ($record->hod_name ?? 'NULL'));
        $this->line('access_type: ' . ($record->access_type ?? 'NULL'));
        $this->line('temporary_until: ' . ($record->temporary_until ?? 'NULL'));
        $this->line('request_type: ' . json_encode($record->request_type));
        
        // Show raw database values
        $this->line('\n--- RAW DATABASE VALUES ---');
        $raw = $record->getRawOriginal();
        $this->line('wellsoft_modules (raw): ' . ($raw['wellsoft_modules'] ?? 'NULL'));
        $this->line('jeeva_modules (raw): ' . ($raw['jeeva_modules'] ?? 'NULL'));
        $this->line('hod_signature_path (raw): ' . ($raw['hod_signature_path'] ?? 'NULL'));
        
        return 0;
    }
}
