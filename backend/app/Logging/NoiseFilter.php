<?php

namespace App\Logging;

class NoiseFilter
{
    /**
     * Invoke Monolog tap to filter out noisy log records before writing.
     * Returning null will drop the record.
     *
     * IMPORTANT: We only filter INFO/DEBUG-level logs. Warnings/Errors must always be kept.
     *
     * @param array $record
     * @return array|null
     */
    public function __invoke(array $record)
    {
        $level = strtoupper((string)($record['level_name'] ?? ''));
        $isLowPriority = in_array($level, ['DEBUG', 'INFO', 'NOTICE'], true);
        if (!$isLowPriority) {
            return $record;
        }

        // Message-only filters (keep list short and focused on high-volume noise).
        $remove = [
            // Generic request lifecycle noise
            'API Request Started',
            'API Request Completed',
            'prepareForValidation',
            'Running validation',
            'DB transaction started',
            'DB transaction committed',
            'Cache hit',
            'Cache miss',
            'Sending response',
            'Handling request',

            // Security health checks are useful during setup, but too noisy for normal runtime
            '🔒 Security features enabled',

            // Common "list/timeline/view" success logs (combined access + borrowing flows)
            'Getting access request timeline',
            'Getting request by ID',
            'Getting ICT officers list',
            'Requests loaded successfully',
            'Fetching',
        ];

        $message = (string)($record['message'] ?? '');
        foreach ($remove as $noise) {
            if ($message !== '' && str_contains($message, $noise)) {
                return null; // Skip writing the log
            }
        }

        return $record;
    }
}
