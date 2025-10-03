<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Services\SmsService;
use App\Events\ApprovalRequestSubmitted;
use App\Events\ApprovalStatusChanged;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Example controller showing how to integrate SMS notifications
 * into existing approval workflow
 */
class ExampleApprovalController extends Controller
{
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Example: Submit Jeeva Access Request with SMS notification
     */
    public function submitJeevaAccessRequest(Request $request)
    {
        // Validate request data
        $validatedData = $request->validate([
            'reason' => 'required|string|max:500',
            'department_id' => 'required|exists:departments,id'
        ]);

        $user = auth()->user();
        
        // Create the request record (example)
        $accessRequest = (object) [
            'id' => uniqid(),
            'user_id' => $user->id,
            'type' => 'jeeva_access',
            'reason' => $validatedData['reason'],
            'status' => 'pending',
            'created_at' => now(),
            'reference' => 'JEV-' . strtoupper(substr(uniqid(), -6))
        ];

        // Get approvers (HODs, Directors, etc.)
        $approvers = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['head_of_department', 'divisional_director', 'ict_director']);
        })->whereNotNull('phone')->get();

        try {
            // Fire event for SMS notification
            ApprovalRequestSubmitted::dispatch(
                $user,
                $accessRequest,
                'jeeva_access',
                $approvers->toArray(),
                [
                    'department' => $user->department->name ?? 'Unknown',
                    'reason' => $validatedData['reason']
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Jeeva access request submitted successfully',
                'data' => [
                    'reference' => $accessRequest->reference,
                    'status' => $accessRequest->status,
                    'approvers_notified' => $approvers->count()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to process Jeeva access request', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to submit request. Please try again.'
            ], 500);
        }
    }

    /**
     * Example: Approve/Reject Request with SMS notification
     */
    public function updateRequestStatus(Request $request, $requestId)
    {
        $validatedData = $request->validate([
            'status' => 'required|in:approved,rejected',
            'reason' => 'sometimes|string|max:500'
        ]);

        $approver = auth()->user();
        
        // Get the request (example - replace with your actual model)
        $accessRequest = (object) [
            'id' => $requestId,
            'user_id' => 1, // Example user ID
            'type' => 'jeeva_access',
            'status' => 'pending',
            'reference' => 'JEV-ABC123'
        ];

        $requestUser = User::find($accessRequest->user_id);
        if (!$requestUser) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        $oldStatus = $accessRequest->status;
        $newStatus = $validatedData['status'];

        try {
            // Update request status (implement your logic here)
            $accessRequest->status = $newStatus;

            // Get additional users to notify for approved requests (like IT staff)
            $additionalNotifyUsers = [];
            if ($newStatus === 'approved') {
                $additionalNotifyUsers = User::whereHas('roles', function ($query) {
                    $query->whereIn('name', ['ict_officer', 'head_of_it']);
                })->whereNotNull('phone')->get()->toArray();
            }

            // Fire event for SMS notification
            ApprovalStatusChanged::dispatch(
                $requestUser,
                $accessRequest,
                'jeeva_access',
                $oldStatus,
                $newStatus,
                $approver,
                $validatedData['reason'] ?? null,
                $additionalNotifyUsers,
                [
                    'approved_by' => $approver->name,
                    'approved_at' => now()->format('Y-m-d H:i')
                ]
            );

            return response()->json([
                'success' => true,
                'message' => "Request {$newStatus} successfully",
                'data' => [
                    'reference' => $accessRequest->reference,
                    'status' => $accessRequest->status,
                    'approved_by' => $approver->name,
                    'additional_notifications_sent' => count($additionalNotifyUsers)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update request status', [
                'request_id' => $requestId,
                'approver_id' => $approver->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update request status. Please try again.'
            ], 500);
        }
    }

    /**
     * Example: Send bulk announcement SMS
     */
    public function sendSystemAnnouncement(Request $request)
    {
        $validatedData = $request->validate([
            'message' => 'required|string|max:320',
            'target' => 'required|in:all_users,by_role,by_department',
            'roles' => 'required_if:target,by_role|array',
            'roles.*' => 'string|in:admin,staff,head_of_department,divisional_director,ict_director,head_of_it,ict_officer',
            'department_ids' => 'required_if:target,by_department|array',
            'department_ids.*' => 'integer|exists:departments,id'
        ]);

        $sender = auth()->user();

        try {
            $recipients = [];
            $targetInfo = '';

            switch ($validatedData['target']) {
                case 'all_users':
                    $recipients = User::whereNotNull('phone')->get()->toArray();
                    $targetInfo = 'All users';
                    break;

                case 'by_role':
                    $recipients = User::whereHas('roles', function ($query) use ($validatedData) {
                        $query->whereIn('name', $validatedData['roles']);
                    })->whereNotNull('phone')->get()->toArray();
                    $targetInfo = 'Users with roles: ' . implode(', ', $validatedData['roles']);
                    break;

                case 'by_department':
                    $recipients = User::whereIn('department_id', $validatedData['department_ids'])
                        ->whereNotNull('phone')->get()->toArray();
                    $targetInfo = 'Users in departments: ' . implode(', ', $validatedData['department_ids']);
                    break;
            }

            if (empty($recipients)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No recipients found for the specified criteria'
                ], 404);
            }

            // Send bulk SMS
            $result = $this->smsService->sendBulkSms(
                $recipients,
                $validatedData['message'],
                'announcement'
            );

            // Log the announcement
            Log::info('System announcement sent', [
                'sender_id' => $sender->id,
                'sender_name' => $sender->name,
                'target' => $validatedData['target'],
                'recipients_count' => count($recipients),
                'sent_count' => $result['sent'],
                'failed_count' => $result['failed']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'System announcement sent successfully',
                'data' => [
                    'target_info' => $targetInfo,
                    'total_recipients' => $result['total'],
                    'sent' => $result['sent'],
                    'failed' => $result['failed'],
                    'success_rate' => $result['total'] > 0 ? round(($result['sent'] / $result['total']) * 100, 2) . '%' : '0%'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send system announcement', [
                'sender_id' => $sender->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send announcement. Please try again.'
            ], 500);
        }
    }

    /**
     * Example: Direct SMS integration (without events)
     */
    public function sendDirectNotification(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'message' => 'required|string|max:320',
            'type' => 'sometimes|string|in:urgent,reminder,information'
        ]);

        $recipient = User::find($validatedData['user_id']);
        $sender = auth()->user();

        if (!$recipient->phone) {
            return response()->json([
                'success' => false,
                'message' => 'Recipient has no phone number'
            ], 400);
        }

        try {
            // Send SMS directly using the service
            $result = $this->smsService->sendSms(
                $recipient->phone,
                $validatedData['message'],
                $validatedData['type'] ?? 'notification'
            );

            // Log the direct notification
            Log::info('Direct SMS notification sent', [
                'sender_id' => $sender->id,
                'recipient_id' => $recipient->id,
                'success' => $result['success']
            ]);

            return response()->json([
                'success' => $result['success'],
                'message' => $result['success'] 
                    ? 'SMS notification sent successfully' 
                    : 'Failed to send SMS: ' . $result['message'],
                'data' => [
                    'recipient' => $recipient->name,
                    'phone' => $recipient->phone,
                    'sent_at' => now()->format('Y-m-d H:i:s')
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send direct SMS notification', [
                'sender_id' => $sender->id,
                'recipient_id' => $recipient->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send SMS notification. Please try again.'
            ], 500);
        }
    }
}