<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Role;

class SyncUserRoleIds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:sync-role-ids {--dry-run : Show what would be updated without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deprecated: role_id has been removed. This command is a no-op and kept for backward compatibility.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->warn('This command is deprecated: users.role_id field has been removed. No action taken.');
        return 0;
    }
}