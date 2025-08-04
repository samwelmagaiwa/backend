<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class JeevaAccessRequestController extends Controller
{
    // Placeholder method to satisfy route registration
    public function store(Request $request)
    {
        return response()->json(['message' => 'JeevaAccessRequestController placeholder.'], 200);
    }

    public function userInfo(Request $request)
    {
        return response()->json(['message' => 'User info placeholder.'], 200);
    }

    public function exportPdf(Request $request)
    {
        return response()->json(['message' => 'Export PDF placeholder.'], 200);
    }
}
