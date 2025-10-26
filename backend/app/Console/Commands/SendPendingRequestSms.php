<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserAccess;
use App\Models\User;
use App\Services\SmsModule;
use Illuminate\Support\Facades\Log;

class SendPendingRequestSms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:send-pending {--request-id= : Specific request ID to process}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send SMS notifications for pending HOD approval requests';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting SMS notification process...');
        $sms = app(SmsModule::class);
        
        // Check if SMS is enabled
        if (!$sms->isEnabled()) {
            $this->warn('⚠️  SMS is disabled in configuration. Enable SMS_ENABLED=true in .env');
            return 1;
        }
        
        $requestId = $this->option('request-id');
        
        if ($requestId) {
            // Process specific request
            return $this->processSingleRequest($requestId, $sms);
        } else {
            // Process all pending requests
            return $this->processAllPendingRequests($sms);
        }
    }
    
    /**
     * Process a single request by ID
     */
    protected function processSingleRequest($requestId, $sms)
    {
        try {
            $this->info("📋 Processing request ID: {$requestId}");
            
            $request = UserAccess::with(['user'])->find($requestId);
            
            if (!$request) {
                $this->error("❌ Request ID {$requestId} not found!");
                return 1;
            }
            
            // Determine who to notify based on status
            $status = $request->hod_status ?? 'pending';
            
            if ($status === 'pending' || $status === null) {
                // Notify HOD
                $this->notifyHOD($request, $sms);
            } else if ($status === 'approved') {
                // Already approved by HOD, notify next level
                $this->notifyNextLevel($request, $sms, 'hod');
            }
            
            $this->info('✅ Processing complete!');
            return 0;
            
        } catch (\Exception $e) {
            $this->error("❌ Error: {$e->getMessage()}");
            Log::error('SMS Command Error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return 1;
        }
    }
    
    /**
     * Process all pending requests
     */
    protected function processAllPendingRequests($sms)
    {
        $this->info('📋 Finding all pending HOD approval requests...');
        
        $pendingRequests = UserAccess::with(['user'])
            ->whereNull('hod_status')
            ->orWhere('hod_status', 'pending')
            ->get();
        
        if ($pendingRequests->isEmpty()) {
            $this->info('✅ No pending requests found.');
            return 0;
        }
        
        $this->info("📊 Found {$pendingRequests->count()} pending request(s)");
        
        $sent = 0;
        $failed = 0;
        
        foreach ($pendingRequests as $request) {
            try {
                $this->line("\n📤 Processing Request #{$request->id} - {$request->staff_name}");
                
                $result = $this->notifyHOD($request, $sms);
                
                if ($result) {
                    $sent++;
                } else {
                    $failed++;
                }
                
            } catch (\Exception $e) {
                $this->error("  ❌ Failed: {$e->getMessage()}");
                $failed++;
            }
        }
        
        $this->newLine();
        $this->info("📊 Summary:");
        $this->info("  ✅ Sent: {$sent}");
        $this->info("  ❌ Failed: {$failed}");
        
        return 0;
    }
    
    /**
     * Notify HOD about pending request
     */
    protected function notifyHOD($request, $sms)
    {
        // Get HOD based on department
        $hod = User::whereHas('departmentsAsHOD', function($q) use ($request) {
            $q->where('departments.id', $request->department_id);
        })->first();
        
        if (!$hod) {
            $this->warn("  ⚠️  No HOD found for department ID: {$request->department_id}");
            return false;
        }
        
        if (!$hod->phone) {
            $this->warn("  ⚠️  HOD {$hod->name} has no phone number");
            return false;
        }
        
        $this->line("  📱 Sending SMS to HOD: {$hod->name} ({$hod->phone})");
        
        // Build message - use request_type JSON array - handle all variations
        $types = [];
        $requestTypes = is_array($request->request_type) ? $request->request_type : json_decode($request->request_type ?? '[]', true);
        
        if (in_array('jeeva_access_request', $requestTypes) || in_array('jeeva_access', $requestTypes) || in_array('jeeva', $requestTypes)) $types[] = 'Jeeva';
        if (in_array('wellsoft_access_request', $requestTypes) || in_array('wellsoft_access', $requestTypes) || in_array('wellsoft', $requestTypes)) $types[] = 'Wellsoft';
        if (in_array('internet_access_request', $requestTypes) || in_array('internet_access', $requestTypes) || in_array('internet', $requestTypes)) $types[] = 'Internet';
        $typeStr = implode(' & ', $types) ?: 'Access';
        
        $ref = 'MLG-REQ' . str_pad($request->id, 6, '0', STR_PAD_LEFT);
        $message = "PENDING APPROVAL: {$typeStr} request from {$request->staff_name} requires your review. Ref: {$ref}. Please check the system. - EABMS";
        
        $result = $sms->sendSms($hod->phone, $message, 'pending_notification');
        
        if ($result['success']) {
            $this->info("  ✅ SMS sent successfully!");
            
            // Update request to track SMS was sent
            $request->update([
                'sms_sent_to_hod_at' => now(),
                'sms_to_hod_status' => 'sent'
            ]);
            
            return true;
        } else {
            $this->error("  ❌ SMS failed: {$result['message']}");
            
            $request->update([
                'sms_to_hod_status' => 'failed'
            ]);
            
            return false;
        }
    }
    
    /**
     * Notify next approval level
     */
    protected function notifyNextLevel($request, $sms, $currentLevel)
    {
        $levelMap = [
            'hod' => 'divisional_director',
            'divisional' => 'ict_director',
            'ict_director' => 'head_of_it'
        ];
        
        $nextRoleName = $levelMap[$currentLevel] ?? null;
        
        if (!$nextRoleName) {
            $this->info("  ℹ️  No next approval level after {$currentLevel}");
            return false;
        }
        
        // Get next approver based on the request's department
        // IMPORTANT: For divisional_director, we must get the director assigned to
        // the specific department of this request, since divisional directors can
        // oversee multiple departments. We use the department relationship.
        if ($nextRoleName === 'divisional_director' && $request->department) {
            $nextApprover = $request->department->divisional_director_id 
                ? User::find($request->department->divisional_director_id)
                : User::whereHas('roles', fn($q) => $q->where('name', $nextRoleName))->first();
        } else {
            // For ICT Director and Head of IT, these are organization-wide roles
            $nextApprover = User::whereHas('roles', fn($q) => 
                $q->where('name', $nextRoleName)
            )->first();
        }
        
        if (!$nextApprover || !$nextApprover->phone) {
            $this->warn("  ⚠️  Next approver ({$nextRoleName}) not found or has no phone");
            return false;
        }
        
        $this->line("  📱 Sending SMS to {$nextRoleName}: {$nextApprover->name}");
        
        // Use the SMS module's notification method
        $result = $sms->notifyRequestApproved(
            $request,
            User::find($request->hod_approved_by),
            $currentLevel,
            $nextApprover
        );
        
        if ($result['next_approver_notified']) {
            $this->info("  ✅ Next approver notified!");
            return true;
        } else {
            $this->error("  ❌ Failed to notify next approver");
            return false;
        }
    }
}
