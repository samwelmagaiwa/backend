<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Services\SmsService;
use App\Models\SmsLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class SmsController extends Controller
{
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Send a single SMS
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendSms(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string|min:9|max:15',
            'message' => 'required|string|max:320',
            'type' => 'sometimes|string|in:notification,approval,announcement,emergency'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $this->smsService->sendSms(
            $request->phone_number,
            $request->message,
            $request->type ?? 'notification'
        );

        return response()->json($result);
    }

    /**
     * Send bulk SMS
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendBulkSms(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'recipients' => 'required|array|min:1|max:100',
            'recipients.*' => 'string|min:9|max:15',
            'message' => 'required|string|max:320',
            'type' => 'sometimes|string|in:bulk,announcement,emergency'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $this->smsService->sendBulkSms(
            $request->recipients,
            $request->message,
            $request->type ?? 'bulk'
        );

        return response()->json([
            'success' => true,
            'message' => 'Bulk SMS processing completed',
            'data' => $result
        ]);
    }

    /**
     * Send SMS to users by role
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendByRole(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'roles' => 'required|array|min:1',
            'roles.*' => 'string|in:admin,staff,head_of_department,divisional_director,ict_director,head_of_it,ict_officer',
            'message' => 'required|string|max:320',
            'type' => 'sometimes|string|in:announcement,emergency,notification'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Get users by roles
        $users = User::whereHas('roles', function ($query) use ($request) {
            $query->whereIn('name', $request->roles);
        })->whereNotNull('phone')->get();

        if ($users->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No users found with the specified roles and phone numbers'
            ], 404);
        }

        $result = $this->smsService->sendBulkSms(
            $users->toArray(),
            $request->message,
            $request->type ?? 'announcement'
        );

        return response()->json([
            'success' => true,
            'message' => 'Role-based SMS processing completed',
            'data' => array_merge($result, [
                'targeted_roles' => $request->roles,
                'users_found' => $users->count()
            ])
        ]);
    }

    /**
     * Send SMS to department users
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendByDepartment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'department_ids' => 'required|array|min:1',
            'department_ids.*' => 'integer|exists:departments,id',
            'message' => 'required|string|max:320',
            'type' => 'sometimes|string|in:announcement,emergency,notification'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Get users by departments
        $users = User::whereIn('department_id', $request->department_ids)
            ->whereNotNull('phone')
            ->with('department')
            ->get();

        if ($users->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No users found in the specified departments with phone numbers'
            ], 404);
        }

        $result = $this->smsService->sendBulkSms(
            $users->toArray(),
            $request->message,
            $request->type ?? 'announcement'
        );

        return response()->json([
            'success' => true,
            'message' => 'Department-based SMS processing completed',
            'data' => array_merge($result, [
                'targeted_departments' => $request->department_ids,
                'users_found' => $users->count(),
                'departments' => $users->groupBy('department_id')->map(function ($group) {
                    return [
                        'department_name' => $group->first()->department->name ?? 'Unknown',
                        'user_count' => $group->count()
                    ];
                })
            ])
        ]);
    }

    /**
     * Get SMS statistics
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStatistics(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date_from' => 'sometimes|date',
            'date_to' => 'sometimes|date|after_or_equal:date_from',
            'type' => 'sometimes|string',
            'status' => 'sometimes|string|in:sent,failed,pending'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Get basic statistics from service
        $basicStats = $this->smsService->getStatistics(
            $request->date_from,
            $request->date_to
        );

        // Get additional detailed statistics
        $query = SmsLog::query();

        if ($request->date_from) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->where('created_at', '<=', $request->date_to);
        }

        if ($request->type) {
            $query->where('type', $request->type);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $detailedStats = [
            'by_type' => $query->selectRaw('type, COUNT(*) as count, 
                          SUM(CASE WHEN status = "sent" THEN 1 ELSE 0 END) as sent,
                          SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed')
                          ->groupBy('type')
                          ->get(),
            
            'by_date' => $query->selectRaw('DATE(created_at) as date, COUNT(*) as count,
                         SUM(CASE WHEN status = "sent" THEN 1 ELSE 0 END) as sent,
                         SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed')
                         ->groupBy('date')
                         ->orderBy('date', 'desc')
                         ->limit(30)
                         ->get(),
            
            'recent_activity' => SmsLog::with('user:id,name')
                                ->orderBy('created_at', 'desc')
                                ->limit(10)
                                ->get()
                                ->map(function ($log) {
                                    return [
                                        'id' => $log->id,
                                        'phone' => $log->formatted_phone,
                                        'short_message' => $log->short_message,
                                        'type' => $log->type,
                                        'status' => $log->readable_status,
                                        'user' => $log->user ? $log->user->name : null,
                                        'created_at' => $log->created_at->format('Y-m-d H:i:s')
                                    ];
                                })
        ];

        return response()->json([
            'success' => true,
            'data' => array_merge($basicStats, [
                'detailed' => $detailedStats
            ])
        ]);
    }

    /**
     * Get SMS logs with filtering
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLogs(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'type' => 'sometimes|string',
            'status' => 'sometimes|string|in:sent,failed,pending',
            'phone_number' => 'sometimes|string',
            'date_from' => 'sometimes|date',
            'date_to' => 'sometimes|date|after_or_equal:date_from',
            'search' => 'sometimes|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $query = SmsLog::with('user:id,name');

        // Apply filters
        if ($request->type) {
            $query->where('type', $request->type);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->phone_number) {
            $query->where('phone_number', 'like', '%' . $request->phone_number . '%');
        }

        if ($request->date_from) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->where('created_at', '<=', $request->date_to);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('message', 'like', '%' . $request->search . '%')
                  ->orWhere('phone_number', 'like', '%' . $request->search . '%');
            });
        }

        $perPage = $request->per_page ?? 20;
        $logs = $query->orderBy('created_at', 'desc')->paginate($perPage);

        // Transform the data
        $logs->getCollection()->transform(function ($log) {
            return [
                'id' => $log->id,
                'phone_number' => $log->formatted_phone,
                'message' => $log->message,
                'short_message' => $log->short_message,
                'type' => $log->type,
                'status' => $log->readable_status,
                'is_successful' => $log->is_successful,
                'delivery_status' => $log->delivery_status,
                'user' => $log->user ? $log->user->name : null,
                'sent_at' => $log->sent_at ? $log->sent_at->format('Y-m-d H:i:s') : null,
                'created_at' => $log->created_at->format('Y-m-d H:i:s')
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $logs
        ]);
    }

    /**
     * Test SMS configuration
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function testConfiguration(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'test_phone' => 'required|string|min:9|max:15'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $testMessage = "SMS Service Test - " . now()->format('Y-m-d H:i:s') . " - MNH IT System";
        
        $result = $this->smsService->sendSms(
            $request->test_phone,
            $testMessage,
            'test'
        );

        return response()->json([
            'success' => $result['success'],
            'message' => $result['success'] ? 'Test SMS sent successfully' : 'Test SMS failed',
            'data' => $result,
            'configuration' => [
                'api_url' => config('sms.api_url'),
                'sender_id' => config('sms.sender_id'),
                'enabled' => config('sms.enabled')
            ]
        ]);
    }
}