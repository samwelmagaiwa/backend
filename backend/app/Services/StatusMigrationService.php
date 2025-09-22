<?php

namespace App\Services;

use App\Models\UserAccess;
use Illuminate\Support\Facades\Log;

class StatusMigrationService
{
    /**
     * Mapping from old status values to new specific status columns
     */
    const STATUS_MAPPING = [
        // Initial states
        'pending' => [
            'hod_status' => 'pending',
            'divisional_status' => 'pending',
            'ict_director_status' => 'pending',
            'head_it_status' => 'pending',
            'ict_officer_status' => 'pending'
        ],
        'pending_hod' => [
            'hod_status' => 'pending',
            'divisional_status' => 'pending',
            'ict_director_status' => 'pending',
            'head_it_status' => 'pending',
            'ict_officer_status' => 'pending'
        ],
        
        // HOD approval states
        'hod_approved' => [
            'hod_status' => 'approved',
            'divisional_status' => 'pending',
            'ict_director_status' => 'pending',
            'head_it_status' => 'pending',
            'ict_officer_status' => 'pending'
        ],
        'hod_rejected' => [
            'hod_status' => 'rejected',
            'divisional_status' => 'pending',
            'ict_director_status' => 'pending',
            'head_it_status' => 'pending',
            'ict_officer_status' => 'pending'
        ],
        'pending_divisional' => [
            'hod_status' => 'approved',
            'divisional_status' => 'pending',
            'ict_director_status' => 'pending',
            'head_it_status' => 'pending',
            'ict_officer_status' => 'pending'
        ],
        
        // Divisional approval states
        'divisional_approved' => [
            'hod_status' => 'approved',
            'divisional_status' => 'approved',
            'ict_director_status' => 'pending',
            'head_it_status' => 'pending',
            'ict_officer_status' => 'pending'
        ],
        'divisional_rejected' => [
            'hod_status' => 'approved',
            'divisional_status' => 'rejected',
            'ict_director_status' => 'pending',
            'head_it_status' => 'pending',
            'ict_officer_status' => 'pending'
        ],
        'pending_ict_director' => [
            'hod_status' => 'approved',
            'divisional_status' => 'approved',
            'ict_director_status' => 'pending',
            'head_it_status' => 'pending',
            'ict_officer_status' => 'pending'
        ],
        
        // ICT Director approval states
        'ict_director_approved' => [
            'hod_status' => 'approved',
            'divisional_status' => 'approved',
            'ict_director_status' => 'approved',
            'head_it_status' => 'pending',
            'ict_officer_status' => 'pending'
        ],
        'ict_director_rejected' => [
            'hod_status' => 'approved',
            'divisional_status' => 'approved',
            'ict_director_status' => 'rejected',
            'head_it_status' => 'pending',
            'ict_officer_status' => 'pending'
        ],
        'pending_head_it' => [
            'hod_status' => 'approved',
            'divisional_status' => 'approved',
            'ict_director_status' => 'approved',
            'head_it_status' => 'pending',
            'ict_officer_status' => 'pending'
        ],
        
        // Head IT approval states
        'head_it_approved' => [
            'hod_status' => 'approved',
            'divisional_status' => 'approved',
            'ict_director_status' => 'approved',
            'head_it_status' => 'approved',
            'ict_officer_status' => 'pending'
        ],
        'head_it_rejected' => [
            'hod_status' => 'approved',
            'divisional_status' => 'approved',
            'ict_director_status' => 'approved',
            'head_it_status' => 'rejected',
            'ict_officer_status' => 'pending'
        ],
        'pending_ict_officer' => [
            'hod_status' => 'approved',
            'divisional_status' => 'approved',
            'ict_director_status' => 'approved',
            'head_it_status' => 'approved',
            'ict_officer_status' => 'pending'
        ],
        
        // Final states
        'approved' => [
            'hod_status' => 'approved',
            'divisional_status' => 'approved',
            'ict_director_status' => 'approved',
            'head_it_status' => 'approved',
            'ict_officer_status' => 'implemented'
        ],
        'implemented' => [
            'hod_status' => 'approved',
            'divisional_status' => 'approved',
            'ict_director_status' => 'approved',
            'head_it_status' => 'approved',
            'ict_officer_status' => 'implemented'
        ],
        'rejected' => [
            // For general rejection, we can't know exactly where it failed
            // Will need to check approval dates/signatures to determine precise state
            'hod_status' => 'rejected', // Default assumption
            'divisional_status' => 'pending',
            'ict_director_status' => 'pending',
            'head_it_status' => 'pending',
            'ict_officer_status' => 'pending'
        ]
    ];

    /**
     * Convert old status value to new specific status columns
     */
    public function mapOldStatusToNewColumns(string $oldStatus, UserAccess $userAccess = null): array
    {
        if (!isset(self::STATUS_MAPPING[$oldStatus])) {
            Log::warning("Unknown status mapping for: {$oldStatus}");
            return self::STATUS_MAPPING['pending']; // Default to pending
        }

        $mapping = self::STATUS_MAPPING[$oldStatus];

        // For 'rejected' status, try to determine where rejection occurred by checking approval dates
        if ($oldStatus === 'rejected' && $userAccess) {
            $mapping = $this->determineRejectionPoint($userAccess);
        }

        return $mapping;
    }

    /**
     * Determine rejection point based on approval dates and signatures
     */
    private function determineRejectionPoint(UserAccess $userAccess): array
    {
        // Check approval progression to determine where rejection occurred
        if (empty($userAccess->hod_approved_at)) {
            // Rejected at HOD level
            return [
                'hod_status' => 'rejected',
                'divisional_status' => 'pending',
                'ict_director_status' => 'pending',
                'head_it_status' => 'pending',
                'ict_officer_status' => 'pending'
            ];
        } elseif (empty($userAccess->divisional_approved_at)) {
            // Rejected at Divisional level
            return [
                'hod_status' => 'approved',
                'divisional_status' => 'rejected',
                'ict_director_status' => 'pending',
                'head_it_status' => 'pending',
                'ict_officer_status' => 'pending'
            ];
        } elseif (empty($userAccess->ict_director_approved_at)) {
            // Rejected at ICT Director level
            return [
                'hod_status' => 'approved',
                'divisional_status' => 'approved',
                'ict_director_status' => 'rejected',
                'head_it_status' => 'pending',
                'ict_officer_status' => 'pending'
            ];
        } elseif (empty($userAccess->head_it_approved_at)) {
            // Rejected at Head IT level
            return [
                'hod_status' => 'approved',
                'divisional_status' => 'approved',
                'ict_director_status' => 'approved',
                'head_it_status' => 'rejected',
                'ict_officer_status' => 'pending'
            ];
        } else {
            // Rejected at ICT Officer level
            return [
                'hod_status' => 'approved',
                'divisional_status' => 'approved',
                'ict_director_status' => 'approved',
                'head_it_status' => 'approved',
                'ict_officer_status' => 'rejected'
            ];
        }
    }

    /**
     * Calculate overall status from specific status columns
     */
    public function calculateOverallStatus(array $statusColumns): string
    {
        $hodStatus = $statusColumns['hod_status'] ?? 'pending';
        $divisionalStatus = $statusColumns['divisional_status'] ?? 'pending';
        $ictDirectorStatus = $statusColumns['ict_director_status'] ?? 'pending';
        $headItStatus = $statusColumns['head_it_status'] ?? 'pending';
        $ictOfficerStatus = $statusColumns['ict_officer_status'] ?? 'pending';

        // Check for rejections (any rejection stops the workflow)
        if ($hodStatus === 'rejected') return 'hod_rejected';
        if ($divisionalStatus === 'rejected') return 'divisional_rejected';
        if ($ictDirectorStatus === 'rejected') return 'ict_director_rejected';
        if ($headItStatus === 'rejected') return 'head_it_rejected';
        if ($ictOfficerStatus === 'rejected') return 'ict_officer_rejected';

        // Check approval progression
        if ($ictOfficerStatus === 'implemented') return 'implemented';
        if ($headItStatus === 'approved' && $ictOfficerStatus === 'pending') return 'pending_ict_officer';
        if ($ictDirectorStatus === 'approved' && $headItStatus === 'pending') return 'pending_head_it';
        if ($divisionalStatus === 'approved' && $ictDirectorStatus === 'pending') return 'pending_ict_director';
        if ($hodStatus === 'approved' && $divisionalStatus === 'pending') return 'pending_divisional';
        if ($hodStatus === 'pending') return 'pending_hod';

        // Default case
        return 'pending';
    }

    /**
     * Get the next pending approval stage
     */
    public function getNextPendingStage(array $statusColumns): ?string
    {
        $hodStatus = $statusColumns['hod_status'] ?? 'pending';
        $divisionalStatus = $statusColumns['divisional_status'] ?? 'pending';
        $ictDirectorStatus = $statusColumns['ict_director_status'] ?? 'pending';
        $headItStatus = $statusColumns['head_it_status'] ?? 'pending';
        $ictOfficerStatus = $statusColumns['ict_officer_status'] ?? 'pending';

        // Check for any rejections (workflow is stopped)
        if (in_array('rejected', [$hodStatus, $divisionalStatus, $ictDirectorStatus, $headItStatus, $ictOfficerStatus])) {
            return null;
        }

        // Find the next pending stage
        if ($hodStatus === 'pending') return 'hod';
        if ($divisionalStatus === 'pending') return 'divisional';
        if ($ictDirectorStatus === 'pending') return 'ict_director';
        if ($headItStatus === 'pending') return 'head_it';
        if ($ictOfficerStatus === 'pending') return 'ict_officer';

        return null; // All stages completed
    }

    /**
     * Check if workflow is complete (all approved/implemented)
     */
    public function isWorkflowComplete(array $statusColumns): bool
    {
        return ($statusColumns['hod_status'] ?? 'pending') === 'approved' &&
               ($statusColumns['divisional_status'] ?? 'pending') === 'approved' &&
               ($statusColumns['ict_director_status'] ?? 'pending') === 'approved' &&
               ($statusColumns['head_it_status'] ?? 'pending') === 'approved' &&
               ($statusColumns['ict_officer_status'] ?? 'pending') === 'implemented';
    }

    /**
     * Check if workflow has any rejections
     */
    public function hasRejections(array $statusColumns): bool
    {
        $statuses = [
            $statusColumns['hod_status'] ?? 'pending',
            $statusColumns['divisional_status'] ?? 'pending',
            $statusColumns['ict_director_status'] ?? 'pending',
            $statusColumns['head_it_status'] ?? 'pending',
            $statusColumns['ict_officer_status'] ?? 'pending'
        ];

        return in_array('rejected', $statuses);
    }

    /**
     * Get workflow completion percentage
     */
    public function getWorkflowProgress(array $statusColumns): int
    {
        $totalSteps = 5;
        $completedSteps = 0;

        if (($statusColumns['hod_status'] ?? 'pending') === 'approved') $completedSteps++;
        if (($statusColumns['divisional_status'] ?? 'pending') === 'approved') $completedSteps++;
        if (($statusColumns['ict_director_status'] ?? 'pending') === 'approved') $completedSteps++;
        if (($statusColumns['head_it_status'] ?? 'pending') === 'approved') $completedSteps++;
        if (($statusColumns['ict_officer_status'] ?? 'pending') === 'implemented') $completedSteps++;

        return (int) (($completedSteps / $totalSteps) * 100);
    }

    /**
     * Migrate a single UserAccess record to use new status columns
     */
    public function migrateUserAccessRecord(UserAccess $userAccess): bool
    {
        try {
            $newStatusColumns = $this->mapOldStatusToNewColumns($userAccess->status, $userAccess);
            
            $userAccess->update($newStatusColumns);
            
            Log::info("Migrated UserAccess ID {$userAccess->id} from status '{$userAccess->status}' to new columns", $newStatusColumns);
            
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to migrate UserAccess ID {$userAccess->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Migrate all UserAccess records to use new status columns
     */
    public function migrateAllRecords(): array
    {
        $results = [
            'total' => 0,
            'successful' => 0,
            'failed' => 0,
            'errors' => []
        ];

        $userAccessRecords = UserAccess::all();
        $results['total'] = $userAccessRecords->count();

        foreach ($userAccessRecords as $record) {
            if ($this->migrateUserAccessRecord($record)) {
                $results['successful']++;
            } else {
                $results['failed']++;
                $results['errors'][] = "Failed to migrate record ID: {$record->id}";
            }
        }

        Log::info("Status migration completed", $results);
        
        return $results;
    }
}
