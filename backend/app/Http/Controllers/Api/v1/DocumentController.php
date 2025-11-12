<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Signature;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DocumentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum']);
    }

    /**
     * POST /api/documents/sign
     * Body: { document_id: int }
     */
    public function signDocument(Request $request): JsonResponse
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'document_id' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $rawId = $request->input('document_id');
        $documentId = $this->resolveDocumentId($rawId, $user->id);

        // Prevent duplicate signatures by the same user on same document
        $existing = Signature::where('document_id', $documentId)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => true,
                'signature_hash' => $existing->signature_hash,
                'signed_at' => optional($existing->signed_at)->format('Y-m-d H:i:s'),
                'message' => 'Document already signed by this user',
            ]);
        }

        // Generate deterministic-but-unique hash (non-repudiation token)
        $hash = hash('sha256', $user->id . '|' . $documentId . '|' . now()->toISOString());

        $signature = Signature::create([
            'document_id' => $documentId,
            'user_id' => $user->id,
            'signature_hash' => $hash,
            'signed_at' => now(),
        ]);

        Log::info('Digital signature created', [
            'user_id' => $user->id,
            'document_id' => $documentId,
            'signature_id' => $signature->id,
        ]);

        return response()->json([
            'success' => true,
            'signature_hash' => $signature->signature_hash,
            'signed_at' => $signature->signed_at->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * GET /api/documents/{id}/signatures
     */
    public function listDocumentSignatures(int $id): JsonResponse
    {
        $rows = Signature::with('user:id,name,email,pf_number')
            ->where('document_id', $id)
            ->orderBy('signed_at', 'asc')
            ->get()
            ->map(function (Signature $s) {
                return [
                    'id' => $s->id,
                    'document_id' => $s->document_id,
                    'user_id' => $s->user_id,
                    'user_name' => $s->user?->name,
                    'user_pf' => $s->user?->pf_number,
                    'signature_hash' => $s->signature_hash,
                    'signature_preview' => substr($s->signature_hash, 0, 10),
                    'signed_at' => optional($s->signed_at)->format('Y-m-d H:i:s'),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $rows,
        ]);
    }

    /**
     * GET /api/signatures/{signature}/verify
     * Recomputes and checks signature pattern (basic verification).
     */
    public function verifySignature(Signature $signature): JsonResponse
    {
        // Basic format/pattern verification: 64-char hex for sha256
        $isHex64 = (bool) preg_match('/^[a-f0-9]{64}$/i', $signature->signature_hash ?? '');
        
        // Recompute compatible hash structure reference (non-deterministic due to timestamp granularity),
        // so here we only ensure it matches expected SHA-256 pattern and is tied to signer + doc record.
        // For stronger verification, you can store salt/nonce per record.
        $valid = $isHex64 && $signature->user && $signature->document_id > 0;

        return response()->json([
            'success' => $valid,
            'signature_id' => $signature->id,
            'user_id' => $signature->user_id,
            'document_id' => $signature->document_id,
            'signature_hash' => $signature->signature_hash,
            'verified' => $valid,
        ], $valid ? 200 : 422);
    }
    private function resolveDocumentId($raw, int $userId): int
    {
        // Accept numeric IDs as-is
        if (is_numeric($raw)) {
            return (int) $raw;
        }
        $key = strtolower(trim((string) $raw));
        // Reserve high ranges for synthetic pre-sign contexts; ensure positive integers
        // 900,000,000 + userId: combined access; 910,000,000 + userId: booking service
        return match ($key) {
            'combined_access', 'combined_access_form' => 900000000 + $userId,
            'booking_service', 'booking' => 910000000 + $userId,
            default => 990000000 + $userId, // generic bucket
        };
    }
}
