<?php

namespace App\Traits;

use App\Models\UserAccess;
use Illuminate\Database\Eloquent\Builder;

trait HandlesStatusQueries
{
    /**
     * Apply status filters based on user role and specific status columns
     */
    public function applyRoleBasedStatusFilters(Builder $query, string $userRole): Builder
    {
        switch ($userRole) {
            case 'head_of_department':
            case 'hod':
                // HODs should see requests pending their approval
                return $query->where('hod_status', 'pending')
                           ->orWhere(function($q) {
                               // Also show requests they have already processed for history
                               $q->whereIn('hod_status', ['approved', 'rejected']);
                           });
                           
            case 'divisional_director':
                // Divisional Directors should see requests that HOD approved and are pending divisional approval
                return $query->where('hod_status', 'approved')
                           ->where('divisional_status', 'pending')
                           ->orWhere(function($q) {
                               // Also show requests they have already processed
                               $q->where('hod_status', 'approved')
                                 ->whereIn('divisional_status', ['approved', 'rejected']);
                           });
                           
            case 'ict_director':
                // ICT Directors should see requests that have passed divisional approval
                return $query->where('hod_status', 'approved')
                           ->where('divisional_status', 'approved')
                           ->where('ict_director_status', 'pending')
                           ->orWhere(function($q) {
                               // Also show requests they have already processed
                               $q->where('hod_status', 'approved')
                                 ->where('divisional_status', 'approved')
                                 ->whereIn('ict_director_status', ['approved', 'rejected']);
                           });
                           
            case 'head_it':
                // Head IT should see requests that have passed ICT Director approval
                return $query->where('hod_status', 'approved')
                           ->where('divisional_status', 'approved')
                           ->where('ict_director_status', 'approved')
                           ->where('head_it_status', 'pending')
                           ->orWhere(function($q) {
                               // Also show requests they have already processed
                               $q->where('hod_status', 'approved')
                                 ->where('divisional_status', 'approved')
                                 ->where('ict_director_status', 'approved')
                                 ->whereIn('head_it_status', ['approved', 'rejected']);
                           });
                           
            case 'ict_officer':
                // ICT Officers should see requests ready for implementation
                return $query->where('hod_status', 'approved')
                           ->where('divisional_status', 'approved')
                           ->where('ict_director_status', 'approved')
                           ->where('head_it_status', 'approved')
                           ->where('ict_officer_status', 'pending')
                           ->orWhere(function($q) {
                               // Also show requests they have already processed
                               $q->where('hod_status', 'approved')
                                 ->where('divisional_status', 'approved')
                                 ->where('ict_director_status', 'approved')
                                 ->where('head_it_status', 'approved')
                                 ->whereIn('ict_officer_status', ['implemented', 'rejected']);
                           });
                           
            default:
                // For other roles, show all requests
                return $query;
        }
    }

    /**
     * Get pending requests for a specific approval stage
     */
    public function getPendingRequestsForStage(string $stage): Builder
    {
        $query = UserAccess::query();
        
        switch ($stage) {
            case 'hod':
                return $query->where('hod_status', 'pending');
                
            case 'divisional':
                return $query->where('hod_status', 'approved')
                           ->where('divisional_status', 'pending');
                           
            case 'ict_director':
                return $query->where('hod_status', 'approved')
                           ->where('divisional_status', 'approved')
                           ->where('ict_director_status', 'pending');
                           
            case 'head_it':
                return $query->where('hod_status', 'approved')
                           ->where('divisional_status', 'approved')
                           ->where('ict_director_status', 'approved')
                           ->where('head_it_status', 'pending');
                           
            case 'ict_officer':
                return $query->where('hod_status', 'approved')
                           ->where('divisional_status', 'approved')
                           ->where('ict_director_status', 'approved')
                           ->where('head_it_status', 'approved')
                           ->where('ict_officer_status', 'pending');
                           
            default:
                return $query->whereRaw('1 = 0'); // No results for invalid stage
        }
    }

    /**
     * Get approved requests for a specific approval stage
     */
    public function getApprovedRequestsForStage(string $stage): Builder
    {
        $query = UserAccess::query();
        
        switch ($stage) {
            case 'hod':
                return $query->where('hod_status', 'approved');
                
            case 'divisional':
                return $query->where('hod_status', 'approved')
                           ->where('divisional_status', 'approved');
                           
            case 'ict_director':
                return $query->where('hod_status', 'approved')
                           ->where('divisional_status', 'approved')
                           ->where('ict_director_status', 'approved');
                           
            case 'head_it':
                return $query->where('hod_status', 'approved')
                           ->where('divisional_status', 'approved')
                           ->where('ict_director_status', 'approved')
                           ->where('head_it_status', 'approved');
                           
            case 'ict_officer':
                return $query->where('hod_status', 'approved')
                           ->where('divisional_status', 'approved')
                           ->where('ict_director_status', 'approved')
                           ->where('head_it_status', 'approved')
                           ->where('ict_officer_status', 'implemented');
                           
            default:
                return $query->whereRaw('1 = 0'); // No results for invalid stage
        }
    }

    /**
     * Get rejected requests for a specific approval stage
     */
    public function getRejectedRequestsForStage(string $stage): Builder
    {
        $query = UserAccess::query();
        
        switch ($stage) {
            case 'hod':
                return $query->where('hod_status', 'rejected');
                
            case 'divisional':
                return $query->where('hod_status', 'approved')
                           ->where('divisional_status', 'rejected');
                           
            case 'ict_director':
                return $query->where('hod_status', 'approved')
                           ->where('divisional_status', 'approved')
                           ->where('ict_director_status', 'rejected');
                           
            case 'head_it':
                return $query->where('hod_status', 'approved')
                           ->where('divisional_status', 'approved')
                           ->where('ict_director_status', 'approved')
                           ->where('head_it_status', 'rejected');
                           
            case 'ict_officer':
                return $query->where('hod_status', 'approved')
                           ->where('divisional_status', 'approved')
                           ->where('ict_director_status', 'approved')
                           ->where('head_it_status', 'approved')
                           ->where('ict_officer_status', 'rejected');
                           
            default:
                return $query->whereRaw('1 = 0'); // No results for invalid stage
        }
    }

    /**
     * Get statistics for a specific approval stage
     */
    public function getStageStatistics(string $stage): array
    {
        return [
            'pending' => $this->getPendingRequestsForStage($stage)->count(),
            'approved' => $this->getApprovedRequestsForStage($stage)->count(),
            'rejected' => $this->getRejectedRequestsForStage($stage)->count(),
            'total' => $this->getPendingRequestsForStage($stage)->count() + 
                      $this->getApprovedRequestsForStage($stage)->count() + 
                      $this->getRejectedRequestsForStage($stage)->count()
        ];
    }

    /**
     * Get all requests that have any rejection in the workflow
     */
    public function getRequestsWithRejections(): Builder
    {
        return UserAccess::query()->where(function($query) {
            $query->where('hod_status', 'rejected')
                  ->orWhere('divisional_status', 'rejected')
                  ->orWhere('ict_director_status', 'rejected')
                  ->orWhere('head_it_status', 'rejected')
                  ->orWhere('ict_officer_status', 'rejected');
        });
    }

    /**
     * Get fully completed requests (all stages approved/implemented)
     */
    public function getCompletedRequests(): Builder
    {
        return UserAccess::query()
            ->where('hod_status', 'approved')
            ->where('divisional_status', 'approved')
            ->where('ict_director_status', 'approved')
            ->where('head_it_status', 'approved')
            ->where('ict_officer_status', 'implemented');
    }

    /**
     * Get requests in progress (no rejections, not fully complete)
     */
    public function getRequestsInProgress(): Builder
    {
        return UserAccess::query()
            ->where('hod_status', '!=', 'rejected')
            ->where('divisional_status', '!=', 'rejected')
            ->where('ict_director_status', '!=', 'rejected')
            ->where('head_it_status', '!=', 'rejected')
            ->where('ict_officer_status', '!=', 'rejected')
            ->where(function($query) {
                $query->where('hod_status', 'pending')
                      ->orWhere('divisional_status', 'pending')
                      ->orWhere('ict_director_status', 'pending')
                      ->orWhere('head_it_status', 'pending')
                      ->orWhere('ict_officer_status', 'pending');
            });
    }

    /**
     * Apply department filtering for HOD users
     */
    public function applyDepartmentFiltering(Builder $query, $user): Builder
    {
        if ($user->hasRole('head_of_department')) {
            $hodDepartmentIds = $user->departmentsAsHOD()->pluck('id')->toArray();
            if (!empty($hodDepartmentIds)) {
                return $query->whereIn('department_id', $hodDepartmentIds);
            } else {
                // If user has HOD role but no departments assigned, show no requests
                return $query->whereRaw('1 = 0');
            }
        }
        
        return $query;
    }

    /**
     * Get overall system statistics using new status columns
     */
    public function getSystemStatistics(): array
    {
        $total = UserAccess::count();
        
        return [
            'total' => $total,
            'pending' => $this->getRequestsInProgress()->count(),
            'completed' => $this->getCompletedRequests()->count(),
            'rejected' => $this->getRequestsWithRejections()->count(),
            'by_stage' => [
                'hod' => $this->getStageStatistics('hod'),
                'divisional' => $this->getStageStatistics('divisional'),
                'ict_director' => $this->getStageStatistics('ict_director'),
                'head_it' => $this->getStageStatistics('head_it'),
                'ict_officer' => $this->getStageStatistics('ict_officer')
            ]
        ];
    }
}
