<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Clean up duplicate device assessments before adding unique constraint
        // Keep the latest assessment for each booking_id + assessment_type combination
        
        $duplicates = DB::select("
            SELECT booking_id, assessment_type, COUNT(*) as count
            FROM device_assessments 
            GROUP BY booking_id, assessment_type 
            HAVING COUNT(*) > 1
        ");
        
        foreach ($duplicates as $duplicate) {
            // Get all assessments for this booking_id + assessment_type
            $assessments = DB::select("
                SELECT id, created_at 
                FROM device_assessments 
                WHERE booking_id = ? AND assessment_type = ?
                ORDER BY created_at DESC
            ", [$duplicate->booking_id, $duplicate->assessment_type]);
            
            // Keep the latest one (first in the ordered list), delete the rest
            $keepId = $assessments[0]->id;
            $deleteIds = array_slice(array_column($assessments, 'id'), 1);
            
            if (!empty($deleteIds)) {
                DB::table('device_assessments')
                    ->whereIn('id', $deleteIds)
                    ->delete();
                    
                \Log::info('Cleaned up duplicate assessments', [
                    'booking_id' => $duplicate->booking_id,
                    'assessment_type' => $duplicate->assessment_type,
                    'kept_id' => $keepId,
                    'deleted_ids' => $deleteIds,
                    'deleted_count' => count($deleteIds)
                ]);
            }
        }
        
        \Log::info('Duplicate assessment cleanup completed', [
            'total_duplicates_found' => count($duplicates)
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cannot reverse the cleanup of duplicates
        \Log::info('Cannot reverse duplicate assessment cleanup');
    }
};