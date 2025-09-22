<?php

namespace App\Http\Controllers\Api\ICTApproval;

use App\Http\Controllers\Controller;
use App\Models\DeviceAssessment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class DeviceAssessmentController extends Controller
{
    public function saveIssuing($id, Request $request)
    {
        return $this->saveAssessment($id, $request, 'issuing');
    }

    public function saveReceiving($id, Request $request)
    {
        return $this->saveAssessment($id, $request, 'receiving');
    }

    protected function saveAssessment($bookingId, Request $request, string $type)
    {
        $validator = Validator::make($request->all(), [
            'physical_condition' => 'required|in:excellent,good,fair,poor',
            'functionality' => 'required|in:fully_functional,partially_functional,not_functional',
            'accessories_complete' => 'boolean',
            'visible_damage' => 'boolean',
            'damage_description' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $payload = $validator->validated();

        // Persist assessment
        $assessment = DeviceAssessment::create([
            'booking_id' => $bookingId,
            'assessment_type' => $type,
            'physical_condition' => $payload['physical_condition'],
            'functionality' => $payload['functionality'],
            'accessories_complete' => $payload['accessories_complete'] ?? false,
            'has_damage' => $payload['visible_damage'] ?? false,
            'damage_description' => ($payload['visible_damage'] ?? false) ? ($payload['damage_description'] ?? null) : null,
            'notes' => $payload['notes'] ?? null,
            'assessed_by' => optional(Auth::user())->id,
            'assessed_at' => Carbon::now(),
        ]);

        // Update booking status in booking_service table
        $statusUpdate = [];
        if ($type === 'issuing') {
            $statusUpdate['status'] = 'in_use';
            $statusUpdate['device_issued_at'] = Carbon::now();
            $statusUpdate['device_condition_issuing'] = json_encode([
                'physical_condition' => $assessment->physical_condition,
                'functionality' => $assessment->functionality,
                'accessories_complete' => $assessment->accessories_complete,
                'visible_damage' => $assessment->has_damage,
                'damage_description' => $assessment->damage_description,
                'assessed_at' => $assessment->assessed_at->toISOString(),
                'assessed_by' => $assessment->assessed_by,
                'assessment_type' => 'issuing'
            ]);
        } else {
            $statusUpdate['status'] = 'returned';
            $statusUpdate['device_received_at'] = Carbon::now();
            $statusUpdate['device_condition_receiving'] = json_encode([
                'physical_condition' => $assessment->physical_condition,
                'functionality' => $assessment->functionality,
                'accessories_complete' => $assessment->accessories_complete,
                'visible_damage' => $assessment->has_damage,
                'damage_description' => $assessment->damage_description,
                'assessed_at' => $assessment->assessed_at->toISOString(),
                'assessed_by' => $assessment->assessed_by,
                'assessment_type' => 'receiving'
            ]);
            // If returned with damage, reflect that in return_status
            if ($assessment->has_damage) {
                $statusUpdate['return_status'] = 'returned_but_compromised';
            } else {
                $statusUpdate['return_status'] = 'returned';
            }
        }
        
        $statusUpdate['assessed_by'] = $assessment->assessed_by;
        $statusUpdate['assessment_notes'] = $assessment->notes;

        DB::table('booking_service')->where('id', $bookingId)->update($statusUpdate);

        // Fetch updated booking record to return
        $booking = DB::table('booking_service')
            ->leftJoin('users', 'users.id', '=', 'booking_service.user_id')
            ->leftJoin('departments', 'departments.id', '=', 'booking_service.department_id')
            ->select('booking_service.*', 'users.name as user_name', 'users.email as user_email', 'departments.name as department_name')
            ->where('booking_service.id', $bookingId)
            ->first();

        // Transform the booking data to a consistent format
        $transformedData = [
            'id' => $booking->id,
            'request_id' => 'REQ-' . str_pad($booking->id, 6, '0', STR_PAD_LEFT),
            'borrower_name' => $booking->borrower_name,
            'department' => $booking->department_name ?? $booking->department,
            'device_type' => $booking->device_type,
            'custom_device' => $booking->custom_device,
            'reason' => $booking->reason,
            'status' => $booking->status,
            'ict_approve' => $booking->ict_approve,
            'return_status' => $booking->return_status,
            'device_issued_at' => $booking->device_issued_at,
            'device_received_at' => $booking->device_received_at,
            'assessed_by' => $booking->assessed_by,
            'assessment_notes' => $booking->assessment_notes,
            'device_condition_issuing' => $booking->device_condition_issuing ? json_decode($booking->device_condition_issuing, true) : null,
            'device_condition_receiving' => $booking->device_condition_receiving ? json_decode($booking->device_condition_receiving, true) : null,
        ];

        return response()->json([
            'success' => true,
            'message' => $type === 'issuing'
                ? 'Device issued and assessment saved successfully'
                : 'Device received and assessment saved successfully',
            'data' => $transformedData,
        ]);
    }
}
