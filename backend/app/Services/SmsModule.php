<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Models\SmsLog;
use Exception;

/**
 * Consolidated SMS Module
 * 
 * All SMS functionality in one place.
 * Handles sending SMS via KODA TECH API, logging, and approval notifications.
 */
class SmsModule
{
    protected $apiUrl;
    protected $apiKey;
    protected $secretKey;
    protected $senderId;
    protected $testMode;
    protected $enabled;
    protected $timeout;

    public function __construct()
    {
        $this->testMode = config('sms.test_mode', true);
        $this->apiUrl = $this->testMode 
            ? 'https://messaging-service.co.tz/api/sms/v1/test/text/single'
            : 'https://messaging-service.co.tz/api/sms/v1/text/single';
        $this->apiKey = config('sms.api_key', 'beneth');
        $this->secretKey = config('sms.secret_key', 'Beneth@1701');
        $this->senderId = config('sms.sender_id', 'KODA TECH');
        $this->enabled = config('sms.enabled', false);
        $this->timeout = config('sms.timeout', 30);
    }

    // ==================== CORE SENDING FUNCTIONS ====================

    /**
     * Send SMS to a single recipient
     */
    public function sendSms(string $phoneNumber, string $message, string $type = 'notification'): array
    {
        try {
            // Check if SMS is enabled
            if (!$this->enabled) {
                Log::info('SMS disabled - would have sent', ['phone' => $phoneNumber, 'message' => substr($message, 0, 50)]);
                return $this->buildResponse(false, 'SMS service is disabled');
            }

            // Format and validate phone number
            $phoneNumber = $this->formatPhoneNumber($phoneNumber);
            if (!$this->isValidPhoneNumber($phoneNumber)) {
                return $this->buildResponse(false, 'Invalid phone number format');
            }

            // Check rate limiting
            if ($this->isRateLimited($phoneNumber)) {
                return $this->buildResponse(false, 'Rate limit exceeded');
            }

            // Send via API
            $response = $this->sendViaApi($phoneNumber, $message);

            // Log the attempt
            $this->logSms($phoneNumber, $message, $type, $response['success'], $response);

            return $response;

        } catch (Exception $e) {
            Log::error('SMS Module Error', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage()
            ]);
            return $this->buildResponse(false, 'SMS error: ' . $e->getMessage());
        }
    }

    /**
     * Send SMS to multiple recipients
     */
    public function sendBulkSms(array $recipients, string $message, string $type = 'bulk'): array
    {
        $results = ['total' => count($recipients), 'sent' => 0, 'failed' => 0, 'details' => []];

        foreach ($recipients as $recipient) {
            $phoneNumber = $this->extractPhoneNumber($recipient);
            
            if (!$phoneNumber) {
                $results['failed']++;
                continue;
            }

            $result = $this->sendSms($phoneNumber, $message, $type);
            
            if ($result['success']) {
                $results['sent']++;
            } else {
                $results['failed']++;
            }

            $results['details'][] = [
                'phone' => $phoneNumber,
                'status' => $result['success'] ? 'sent' : 'failed',
                'message' => $result['message']
            ];

            // Small delay to avoid overwhelming API
            usleep(100000); // 0.1 second
        }

        return $results;
    }

    // ==================== APPROVAL NOTIFICATIONS ====================

    /**
     * Send notification when a request is approved
     * 
     * @param mixed $request - The access request object
     * @param User $approver - Who just approved it
     * @param string $approvalLevel - 'hod', 'divisional', 'ict_director', 'head_it'
     * @param User|null $nextApprover - Next person who needs to approve (if any)
     */
    public function notifyRequestApproved($request, User $approver, string $approvalLevel, ?User $nextApprover = null): array
    {
        $results = ['requester_notified' => false, 'next_approver_notified' => false];

        try {
            // 1. Notify the requester
            $results['requester_notified'] = $this->notifyRequester($request, $approvalLevel);
            
            // Update requester SMS status
            $this->updateRequesterSmsStatus($request, $results['requester_notified']);

            // 2. Notify next approver if exists
            if ($nextApprover && $nextApprover->phone) {
                $results['next_approver_notified'] = $this->notifyNextApprover($request, $nextApprover, $approvalLevel);
                
                // Update next approver SMS status based on their level
                $this->updateNextApproverSmsStatus($request, $approvalLevel, $results['next_approver_notified']);
            }

            Log::info('Approval SMS notifications sent', [
                'request_id' => $request->id,
                'level' => $approvalLevel,
                'results' => $results
            ]);

        } catch (Exception $e) {
            Log::error('Failed to send approval notifications', [
                'request_id' => $request->id ?? null,
                'error' => $e->getMessage()
            ]);
        }

        return $results;
    }

    /**
     * Notify requester about approval
     */
    protected function notifyRequester($request, string $approvalLevel): bool
    {
        // Get phone number - prioritize from user relationship (users table)
        $phone = null;
        $phoneSource = 'unknown';
        
        // Try to get from request fields first
        if (!empty($request->phone)) {
            $phone = $request->phone;
            $phoneSource = 'request.phone';
        } elseif (!empty($request->phone_number)) {
            $phone = $request->phone_number;
            $phoneSource = 'request.phone_number';
        } elseif (isset($request->user) && !empty($request->user->phone)) {
            // Get from users table via relationship
            $phone = $request->user->phone;
            $phoneSource = 'users.phone (via relationship)';
        }
        
        if (!$phone) {
            Log::warning('No phone number for requester', [
                'request_id' => $request->id,
                'user_id' => $request->user_id ?? null,
                'checked_sources' => ['request.phone', 'request.phone_number', 'user.phone']
            ]);
            return false;
        }
        
        Log::info('SMS phone number resolved', [
            'request_id' => $request->id,
            'phone_source' => $phoneSource,
            'phone_masked' => substr($phone, 0, 6) . '***'
        ]);

        // Get requester name
        $name = $request->staff_name ?? $request->full_name ?? $request->user->name ?? 'User';

        // Get request types
        $types = $this->getRequestTypes($request);

        // Get reference
        $ref = $request->request_id ?? 'MLG-REQ' . str_pad($request->id, 6, '0', STR_PAD_LEFT);

        // Build message
        $levelName = $this->getApprovalLevelName($approvalLevel);
        $message = "Dear {$name}, your {$types} request has been APPROVED by {$levelName}. Reference: {$ref}. You will be notified on next steps. - EABMS";

        // Send SMS
        $result = $this->sendSms($phone, $message, 'approval');
        return $result['success'];
    }

    /**
     * Notify next approver about pending request
     */
    protected function notifyNextApprover($request, User $nextApprover, string $currentLevel): bool
    {
        // Get phone from users table
        if (!$nextApprover->phone) {
            Log::warning('No phone number for next approver', [
                'approver_id' => $nextApprover->id,
                'approver_name' => $nextApprover->name,
                'approver_role' => $nextApprover->roles->pluck('name')->toArray() ?? []
            ]);
            return false;
        }
        
        Log::info('Next approver phone number found', [
            'approver_id' => $nextApprover->id,
            'approver_name' => $nextApprover->name,
            'phone_source' => 'users.phone',
            'phone_masked' => substr($nextApprover->phone, 0, 6) . '***'
        ]);

        // Get requester info
        $requesterName = $request->staff_name ?? $request->full_name ?? $request->user->name ?? 'Staff';
        $department = $request->department ?? $request->user->department->name ?? 'N/A';

        // Get request types
        $types = $this->getRequestTypes($request);

        // Get reference
        $ref = $request->request_id ?? 'MLG-REQ' . str_pad($request->id, 6, '0', STR_PAD_LEFT);

        // Build message
        $message = "PENDING APPROVAL: {$types} request from {$requesterName} ({$department}) requires your review. Ref: {$ref}. Please check the system. - EABMS";

        // Send SMS
        $result = $this->sendSms($nextApprover->phone, $message, 'approval_notification');
        return $result['success'];
    }

    // ==================== API COMMUNICATION ====================

    /**
     * Send SMS via KODA TECH API using cURL
     */
    protected function sendViaApi(string $phoneNumber, string $message): array
    {
        try {
            // Test mode bypass
            if ($this->testMode && !$this->enabled) {
                Log::info('SMS TEST MODE (disabled)', ['phone' => $phoneNumber]);
                return $this->buildResponse(true, 'SMS sent (test mode - disabled)', ['test_mode' => true]);
            }

            // Encode credentials for Basic Auth
            $auth = base64_encode($this->apiKey . ':' . $this->secretKey);

            // Prepare payload
            $payload = [
                "from" => $this->senderId,
                "to" => $phoneNumber,
                "text" => $message,
                "reference" => uniqid("ref_")
            ];

            // Initialize cURL
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Authorization: Basic " . $auth,
                "Content-Type: application/json",
                "Accept: application/json"
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

            // Execute
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            // Handle cURL errors
            if ($curlError) {
                Log::error('SMS cURL Error', ['error' => $curlError]);
                return $this->buildResponse(false, 'Connection error: ' . $curlError);
            }

            // Handle HTTP errors
            if ($httpCode !== 200) {
                Log::error('SMS HTTP Error', ['code' => $httpCode, 'response' => $response]);
                return $this->buildResponse(false, 'HTTP Error: ' . $httpCode);
            }

            // Decode JSON
            $data = json_decode($response, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('SMS JSON Error', ['error' => json_last_error_msg()]);
                return $this->buildResponse(false, 'Invalid JSON response');
            }

            // Check for success
            if (isset($data['messages']) && is_array($data['messages'])) {
                Log::info('SMS sent successfully', ['count' => count($data['messages'])]);
                return $this->buildResponse(true, 'SMS sent successfully', $data);
            }

            // Check for errors
            if (isset($data['error'])) {
                return $this->buildResponse(false, 'API Error: ' . ($data['error']['message'] ?? 'Unknown'), $data);
            }

            return $this->buildResponse(true, 'SMS processed', $data);

        } catch (Exception $e) {
            Log::error('SMS API Exception', ['error' => $e->getMessage()]);
            return $this->buildResponse(false, 'Exception: ' . $e->getMessage());
        }
    }

    // ==================== HELPER FUNCTIONS ====================

    /**
     * Format phone number to international format
     * Automatically adds Tanzania country code +255 if missing
     */
    protected function formatPhoneNumber(string $phoneNumber): string
    {
        // Remove all non-numeric characters (spaces, +, -, etc.)
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);

        // Handle different formats and auto-add country code
        
        // Already has country code 255 (e.g., 255712345678)
        if (strlen($phoneNumber) === 12 && substr($phoneNumber, 0, 3) === '255') {
            return $phoneNumber;
        }
        
        // 9 digits starting with 7 (e.g., 712345678)
        if (strlen($phoneNumber) === 9 && substr($phoneNumber, 0, 1) === '7') {
            return '255' . $phoneNumber;
        }
        
        // 10 digits starting with 07 (e.g., 0712345678)
        if (strlen($phoneNumber) === 10 && substr($phoneNumber, 0, 2) === '07') {
            return '255' . substr($phoneNumber, 1);
        }
        
        // 9 digits starting with 6 (e.g., 612345678) - Vodacom
        if (strlen($phoneNumber) === 9 && substr($phoneNumber, 0, 1) === '6') {
            return '255' . $phoneNumber;
        }
        
        // 10 digits starting with 06 (e.g., 0612345678) - Vodacom
        if (strlen($phoneNumber) === 10 && substr($phoneNumber, 0, 2) === '06') {
            return '255' . substr($phoneNumber, 1);
        }
        
        // If doesn't match any pattern, return as-is
        return $phoneNumber;
    }

    /**
     * Validate Tanzanian phone number
     */
    protected function isValidPhoneNumber(string $phoneNumber): bool
    {
        return preg_match('/^255[67][0-9]{8}$/', $phoneNumber);
    }

    /**
     * Check rate limiting (5 SMS per hour per number)
     */
    protected function isRateLimited(string $phoneNumber): bool
    {
        $key = 'sms_rate_limit:' . $phoneNumber;
        $attempts = Cache::get($key, 0);
        
        $maxAttempts = config('sms.rate_limit.max_per_hour_per_number', 5);
        
        if ($attempts >= $maxAttempts) {
            return true;
        }

        Cache::put($key, $attempts + 1, now()->addHour());
        return false;
    }

    /**
     * Extract phone number from various formats
     */
    protected function extractPhoneNumber($recipient): ?string
    {
        if (is_string($recipient)) {
            return $recipient;
        }
        
        if ($recipient instanceof User) {
            return $recipient->phone;
        }
        
        if (is_array($recipient) && isset($recipient['phone'])) {
            return $recipient['phone'];
        }
        
        return null;
    }

    /**
     * Get request types as string
     */
    protected function getRequestTypes($request): string
    {
        $types = [];
        if ($request->jeeva_access ?? false) $types[] = 'Jeeva';
        if ($request->wellsoft_access ?? false) $types[] = 'Wellsoft';
        if ($request->internet_access ?? false) $types[] = 'Internet';
        return implode(' & ', $types) ?: 'Access';
    }

    /**
     * Get human-readable approval level name
     */
    protected function getApprovalLevelName(string $level): string
    {
        $levels = [
            'hod' => 'HOD',
            'divisional' => 'Divisional Director',
            'ict_director' => 'ICT Director',
            'head_it' => 'Head of IT'
        ];
        return $levels[$level] ?? ucfirst($level);
    }

    /**
     * Log SMS attempt to database
     */
    protected function logSms(string $phoneNumber, string $message, string $type, bool $success, array $response): void
    {
        try {
            SmsLog::create([
                'phone_number' => $phoneNumber,
                'message' => $message,
                'type' => $type,
                'status' => $success ? 'sent' : 'failed',
                'provider_response' => json_encode($response),
                'sent_at' => $success ? now() : null
            ]);
        } catch (Exception $e) {
            Log::error('Failed to log SMS', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Build standardized response
     */
    protected function buildResponse(bool $success, string $message, $data = null): array
    {
        return [
            'success' => $success,
            'message' => $message,
            'data' => $data
        ];
    }

    // ==================== PUBLIC UTILITY FUNCTIONS ====================

    /**
     * Get SMS statistics
     */
    public function getStatistics(?string $dateFrom = null, ?string $dateTo = null): array
    {
        $query = SmsLog::query();

        if ($dateFrom) {
            $query->where('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->where('created_at', '<=', $dateTo);
        }

        $total = $query->count();
        $sent = $query->where('status', 'sent')->count();
        $failed = $query->where('status', 'failed')->count();

        return [
            'total' => $total,
            'sent' => $sent,
            'failed' => $failed,
            'success_rate' => $total > 0 ? round(($sent / $total) * 100, 2) : 0
        ];
    }

    /**
     * Test SMS configuration
     */
    public function testConfiguration(string $testPhone): array
    {
        $message = "SMS Service Test - " . now()->format('Y-m-d H:i:s') . " - MNH IT System";
        return $this->sendSms($testPhone, $message, 'test');
    }

    /**
     * Check if SMS service is enabled
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Check if in test mode
     */
    public function isTestMode(): bool
    {
        return $this->testMode;
    }

    // ==================== SMS STATUS TRACKING ====================

    /**
     * Update requester SMS notification status
     */
    protected function updateRequesterSmsStatus($request, bool $success): void
    {
        try {
            $request->update([
                'sms_sent_to_requester_at' => $success ? now() : null,
                'sms_to_requester_status' => $success ? 'sent' : 'failed'
            ]);
        } catch (Exception $e) {
            Log::error('Failed to update requester SMS status', [
                'request_id' => $request->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Update next approver SMS notification status based on approval level
     */
    protected function updateNextApproverSmsStatus($request, string $approvalLevel, bool $success): void
    {
        try {
            $statusField = null;
            $timestampField = null;

            // Determine which fields to update based on current approval level
            // The SMS is being sent TO the next level
            switch ($approvalLevel) {
                case 'hod':
                    // HOD just approved, so SMS is sent to Divisional
                    $timestampField = 'sms_sent_to_divisional_at';
                    $statusField = 'sms_to_divisional_status';
                    break;
                case 'divisional':
                    // Divisional just approved, so SMS is sent to ICT Director
                    $timestampField = 'sms_sent_to_ict_director_at';
                    $statusField = 'sms_to_ict_director_status';
                    break;
                case 'ict_director':
                    // ICT Director just approved, so SMS is sent to Head of IT
                    $timestampField = 'sms_sent_to_head_it_at';
                    $statusField = 'sms_to_head_it_status';
                    break;
                case 'head_it':
                    // Head of IT is final - no next approver
                    return;
            }

            if ($statusField && $timestampField) {
                $request->update([
                    $timestampField => $success ? now() : null,
                    $statusField => $success ? 'sent' : 'failed'
                ]);
            }
        } catch (Exception $e) {
            Log::error('Failed to update next approver SMS status', [
                'request_id' => $request->id,
                'approval_level' => $approvalLevel,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get SMS notification status for a request
     */
    public function getSmsNotificationStatus($request): array
    {
        return [
            'hod' => [
                'status' => $request->sms_to_hod_status ?? 'pending',
                'sent_at' => $request->sms_sent_to_hod_at,
                'sent' => $request->sms_to_hod_status === 'sent'
            ],
            'divisional' => [
                'status' => $request->sms_to_divisional_status ?? 'pending',
                'sent_at' => $request->sms_sent_to_divisional_at,
                'sent' => $request->sms_to_divisional_status === 'sent'
            ],
            'ict_director' => [
                'status' => $request->sms_to_ict_director_status ?? 'pending',
                'sent_at' => $request->sms_sent_to_ict_director_at,
                'sent' => $request->sms_to_ict_director_status === 'sent'
            ],
            'head_it' => [
                'status' => $request->sms_to_head_it_status ?? 'pending',
                'sent_at' => $request->sms_sent_to_head_it_at,
                'sent' => $request->sms_to_head_it_status === 'sent'
            ],
            'requester' => [
                'status' => $request->sms_to_requester_status ?? 'pending',
                'sent_at' => $request->sms_sent_to_requester_at,
                'sent' => $request->sms_to_requester_status === 'sent'
            ]
        ];
    }
}
