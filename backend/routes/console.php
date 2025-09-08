<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\DeviceInventory;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Fix device inventory quantity inconsistencies
Artisan::command('device-inventory:fix-quantities', function () {
    $this->info('Checking device inventory quantities...');
    
    $devices = DeviceInventory::all();
    $fixedCount = 0;
    
    foreach ($devices as $device) {
        $expectedAvailable = $device->total_quantity - $device->borrowed_quantity;
        
        if ($device->available_quantity !== $expectedAvailable) {
            $this->warn("Fixing device: {$device->device_name}");
            $this->line("  Total: {$device->total_quantity}");
            $this->line("  Borrowed: {$device->borrowed_quantity}");
            $this->line("  Available (old): {$device->available_quantity}");
            $this->line("  Available (new): {$expectedAvailable}");
            
            $device->available_quantity = max(0, $expectedAvailable);
            $device->save();
            
            $fixedCount++;
        }
    }
    
    if ($fixedCount > 0) {
        $this->info("Fixed {$fixedCount} device(s) with quantity inconsistencies.");
    } else {
        $this->info('All device quantities are consistent.');
    }
    
    // Display current statistics
    $this->info("\nCurrent Statistics:");
    $totalInventory = DeviceInventory::active()->sum('total_quantity');
    $availableInventory = DeviceInventory::active()->sum('available_quantity');
    $borrowedInventory = DeviceInventory::active()->sum('borrowed_quantity');
    
    $this->line("Total Devices: {$totalInventory}");
    $this->line("Available: {$availableInventory}");
    $this->line("Borrowed: {$borrowedInventory}");
    $this->line("Formula Check: {$totalInventory} - {$borrowedInventory} = " . ($totalInventory - $borrowedInventory));
    
    if (($totalInventory - $borrowedInventory) === $availableInventory) {
        $this->info('✓ Formula is correct: Available = Total - Borrowed');
    } else {
        $this->error('✗ Formula mismatch detected!');
    }
})->purpose('Fix device inventory quantity inconsistencies');
