<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Define specific booking permissions
        $bookingPermissions = [
            'view_device_bookings',
            'approve_device_bookings', 
            'reject_device_bookings',
            'manage_device_inventory',
            'view_booking_statistics'
        ];
        
        // Get roles that should have booking approval permissions
        $ictRoles = ['admin', 'ict_officer', 'ict_director'];
        
        foreach ($ictRoles as $roleName) {
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $currentPermissions = is_array($role->permissions) ? $role->permissions : [];
                
                // Add new booking permissions
                $updatedPermissions = array_unique(array_merge($currentPermissions, $bookingPermissions));
                
                $role->update(['permissions' => $updatedPermissions]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Define booking permissions to remove
        $bookingPermissions = [
            'view_device_bookings',
            'approve_device_bookings',
            'reject_device_bookings', 
            'manage_device_inventory',
            'view_booking_statistics'
        ];
        
        // Get roles that had booking approval permissions
        $ictRoles = ['admin', 'ict_officer', 'ict_director'];
        
        foreach ($ictRoles as $roleName) {
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $currentPermissions = is_array($role->permissions) ? $role->permissions : [];
                
                // Remove booking permissions
                $updatedPermissions = array_diff($currentPermissions, $bookingPermissions);
                
                $role->update(['permissions' => array_values($updatedPermissions)]);
            }
        }
    }
};
