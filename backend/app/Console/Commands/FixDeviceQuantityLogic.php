<?php

namespace App\Console\Commands;

use App\Models\BookingService;
use App\Models\DeviceInventory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixDeviceQuantityLogic extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'device:fix-quantities {--dry-run : Show what would be changed without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix device inventory quantities based on actual approved bookings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        if ($isDryRun) {
            $this->info('DRY RUN MODE - No changes will be made');
        }
        
        $this->info('Analyzing device inventory quantities...');
        
        $devices = DeviceInventory::all();
        $fixedCount = 0;
        
        foreach ($devices as $device) {
            // Count actually borrowed devices (approved and not yet returned)
            $actualBorrowedCount = BookingService::where('device_inventory_id', $device->id)
                ->where('ict_approve', 'approved')
                ->whereNotIn('return_status', ['returned', 'returned_but_compromised'])
                ->count();
                
            $expectedAvailable = $device->total_quantity - $actualBorrowedCount;
            $currentAvailable = $device->available_quantity;
            $currentBorrowed = $device->borrowed_quantity;
            
            if ($currentAvailable !== $expectedAvailable || $currentBorrowed !== $actualBorrowedCount) {
                $this->warn("Device ID {$device->id} ({$device->device_name}) has incorrect quantities:");
                $this->line("  Total: {$device->total_quantity}");
                $this->line("  Current Available: {$currentAvailable} -> Should be: {$expectedAvailable}");
                $this->line("  Current Borrowed: {$currentBorrowed} -> Should be: {$actualBorrowedCount}");
                
                if (!$isDryRun) {
                    $device->update([
                        'available_quantity' => max(0, $expectedAvailable),
                        'borrowed_quantity' => $actualBorrowedCount
                    ]);
                    $this->info("  ✓ Fixed quantities for {$device->device_name}");
                }
                
                $fixedCount++;
            } else {
                $this->info("Device ID {$device->id} ({$device->device_name}) quantities are correct");
            }
        }
        
        if ($fixedCount > 0) {
            if ($isDryRun) {
                $this->warn("Found {$fixedCount} devices with incorrect quantities. Run without --dry-run to fix them.");
            } else {
                $this->info("Fixed quantities for {$fixedCount} devices.");
            }
        } else {
            $this->info('All device quantities are correct!');
        }
        
        return 0;
    }
}
