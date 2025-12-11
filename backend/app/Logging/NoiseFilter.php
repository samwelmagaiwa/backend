<?php

namespace App\Logging;

class NoiseFilter
{
    /**
     * Invoke Monolog tap to filter out noisy log records before writing.
     * Returning null will drop the record.
     *
     * @param array $record
     * @return array|null
     */
    public function __invoke(array $record)
    {
        $remove = [
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
        ];

        $message = $record['message'] ?? '';
        foreach ($remove as $noise) {
            if ($message !== '' && str_contains($message, $noise)) {
                return null; // Skip writing the log
            }
        }

        return $record;
    }
}
