<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestLogCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:log {message}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Log test messages for benchmarking and analysis';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $message = $this->argument('message');
        
        // Log to file
        Log::channel('single')->info('TEST BENCHMARK: ' . $message);
        
        // Also output to console
        $this->info($message);
        
        return 0;
    }
}
