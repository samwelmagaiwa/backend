<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\ModuleAccessRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use PDF;

class ModuleAccessRequestController extends Controller
{
    public function index(): JsonResponse
    {
        $requests = ModuleAccessRequest::with('user')->get();
        return response()->json($requests);
    }

    public function store(Request $request): JsonResponse
    {
        Log::info('ModuleAccessRequestController@store called', [
            'payload' => $request->all(),
            'user' => $request->user()
        ]);
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'access_type' => 'required|in:permanent,temporary',
            'temporary_until' => 'nullable|date|required_if:access_type,temporary',
            'modules' => 'required|array',
            'modules.*' => 'string',
            'comments' => 'nullable|string',
        ]);

        $accessRequest = ModuleAccessRequest::create($data);
        return response()->json([
            'message' => 'Access request created successfully.',
            'data' => $accessRequest,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $request = ModuleAccessRequest::with('user')->findOrFail($id);
        return response()->json($request);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $accessRequest = ModuleAccessRequest::findOrFail($id);
        $data = $request->validate([
            'access_type' => 'in:permanent,temporary',
            'temporary_until' => 'nullable|date|required_if:access_type,temporary',
            'modules' => 'array',
            'modules.*' => 'string',
            'comments' => 'nullable|string',
        ]);
        $accessRequest->update($data);
        return response()->json([
            'message' => 'Access request updated successfully.',
            'data' => $accessRequest,
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $accessRequest = ModuleAccessRequest::findOrFail($id);
        $accessRequest->delete();
        return response()->json(['message' => 'Access request deleted successfully.']);
    }

    public function userInfo(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'pf_number' => $user->pf_number ?? '',
            'staff_name' => $user->name ?? '',
            'role' => $user->role ? $user->role->name : '',
        ]);
    }

    public function exportPdf(Request $request)
    {
        $user = $request->user();
        $authorizedRoles = ['Head of Department', 'Director of ICT', 'Head of IT', 'ICT Officer', 'staff', 'Admin'];
        if (!$user || !in_array($user->role ? $user->role->name : '', $authorizedRoles)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $pf_number = $request->query('pf_number');
        $form = \App\Models\JeevaAccessRequest::where('pf_number', $pf_number)->latest()->first();
        if (!$form) {
            return response()->json(['error' => 'Form not found'], 404);
        }
        // Generate PDF (simple HTML to PDF for demonstration)
        $html = view('pdf.jeeva_access_request', ['form' => $form])->render();
        $pdf = PDF::loadHTML($html);
        return $pdf->download('jeeva_access_request_'.$pf_number.'.pdf');
    }
}
