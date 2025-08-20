<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;

class SignatureService
{
    /**
     * The base path for signatures in storage.
     */
    private const SIGNATURE_BASE_PATH = 'signatures';

    /**
     * Allowed signature file extensions.
     */
    private const ALLOWED_EXTENSIONS = ['png', 'jpg', 'jpeg'];

    /**
     * Find existing signature file for a given PF number.
     */
    public function findExistingSignature(string $pfNumber): ?string
    {
        try {
            $pfNumber = $this->cleanPfNumber($pfNumber);
            $signaturePath = self::SIGNATURE_BASE_PATH . '/' . $pfNumber;

            // Check if the PF number directory exists
            if (!Storage::disk('public')->exists($signaturePath)) {
                Log::info("Signature directory not found for PF: {$pfNumber}");
                return null;
            }

            // Get all files in the PF number directory
            $files = Storage::disk('public')->files($signaturePath);

            // Find the first valid signature file
            foreach ($files as $file) {
                $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                
                if (in_array($extension, self::ALLOWED_EXTENSIONS)) {
                    Log::info("Found existing signature for PF {$pfNumber}: {$file}");
                    return $file;
                }
            }

            Log::info("No valid signature file found for PF: {$pfNumber}");
            return null;

        } catch (\Exception $e) {
            Log::error("Error finding signature for PF {$pfNumber}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Store uploaded signature file.
     */
    public function storeUploadedSignature(UploadedFile $file, string $pfNumber): string
    {
        try {
            $pfNumber = $this->cleanPfNumber($pfNumber);
            $extension = $file->getClientOriginalExtension();
            $filename = 'signature_' . time() . '.' . $extension;
            $signaturePath = self::SIGNATURE_BASE_PATH . '/' . $pfNumber;

            // Store the file
            $filePath = $file->storeAs($signaturePath, $filename, 'public');

            Log::info("Uploaded signature stored for PF {$pfNumber}: {$filePath}");
            return $filePath;

        } catch (\Exception $e) {
            Log::error("Error storing uploaded signature for PF {$pfNumber}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get the signature path for a user access request.
     * First tries to find existing signature, then falls back to uploaded file.
     */
    public function getSignaturePath(string $pfNumber, ?UploadedFile $uploadedFile = null): ?string
    {
        // First, try to find existing signature
        $existingSignature = $this->findExistingSignature($pfNumber);
        
        if ($existingSignature) {
            return $existingSignature;
        }

        // If no existing signature and file is uploaded, store the uploaded file
        if ($uploadedFile) {
            return $this->storeUploadedSignature($uploadedFile, $pfNumber);
        }

        // No signature found or uploaded
        Log::warning("No signature available for PF: {$pfNumber}");
        return null;
    }

    /**
     * Get the full URL for a signature file.
     */
    public function getSignatureUrl(?string $signaturePath): ?string
    {
        if (!$signaturePath) {
            return null;
        }

        return Storage::disk('public')->url($signaturePath);
    }

    /**
     * Check if a signature file exists.
     */
    public function signatureExists(?string $signaturePath): bool
    {
        if (!$signaturePath) {
            return false;
        }

        return Storage::disk('public')->exists($signaturePath);
    }

    /**
     * Delete a signature file.
     */
    public function deleteSignature(?string $signaturePath): bool
    {
        if (!$signaturePath || !$this->signatureExists($signaturePath)) {
            return false;
        }

        try {
            Storage::disk('public')->delete($signaturePath);
            Log::info("Deleted signature: {$signaturePath}");
            return true;
        } catch (\Exception $e) {
            Log::error("Error deleting signature {$signaturePath}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Clean PF number for use in file paths.
     */
    private function cleanPfNumber(string $pfNumber): string
    {
        // Remove special characters that might cause issues in file paths
        return preg_replace('/[^A-Za-z0-9\-]/', '_', strtoupper(trim($pfNumber)));
    }

    /**
     * Get signature file info.
     */
    public function getSignatureInfo(?string $signaturePath): ?array
    {
        if (!$signaturePath || !$this->signatureExists($signaturePath)) {
            return null;
        }

        try {
            $size = Storage::disk('public')->size($signaturePath);
            $lastModified = Storage::disk('public')->lastModified($signaturePath);
            $url = $this->getSignatureUrl($signaturePath);

            return [
                'path' => $signaturePath,
                'url' => $url,
                'size' => $size,
                'size_human' => $this->formatBytes($size),
                'last_modified' => date('Y-m-d H:i:s', $lastModified),
                'exists' => true,
            ];
        } catch (\Exception $e) {
            Log::error("Error getting signature info for {$signaturePath}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Format bytes to human readable format.
     */
    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}