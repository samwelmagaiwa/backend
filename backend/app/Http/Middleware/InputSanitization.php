<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InputSanitization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Sanitize all input data
        $input = $request->all();
        $sanitized = $this->sanitizeArray($input);
        $request->merge($sanitized);
        
        // Also sanitize JSON payload if present
        if ($request->isJson() && $request->getContent()) {
            $jsonData = json_decode($request->getContent(), true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($jsonData)) {
                $sanitizedJson = $this->sanitizeArray($jsonData);
                $request->initialize(
                    $request->query->all(),
                    $sanitizedJson,
                    $request->attributes->all(),
                    $request->cookies->all(),
                    $request->files->all(),
                    $request->server->all(),
                    json_encode($sanitizedJson)
                );
            }
        }
        
        return $next($request);
    }
    
    /**
     * Recursively sanitize array data
     *
     * @param array $data
     * @return array
     */
    private function sanitizeArray(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->sanitizeArray($value);
            } elseif (is_string($value)) {
                $data[$key] = $this->sanitizeString($value);
            }
        }
        
        return $data;
    }
    
    /**
     * Sanitize string input
     *
     * @param string $input
     * @return string
     */
    private function sanitizeString(string $input): string
    {
        // Remove HTML tags while preserving content
        $sanitized = strip_tags($input);
        
        // Remove JavaScript event handlers and dangerous attributes
        $sanitized = preg_replace('/on\w+\s*=\s*["\'][^"\']*["\']/i', '', $sanitized);
        
        // Remove script tags and their content
        $sanitized = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi', '', $sanitized);
        
        // Remove style tags and their content
        $sanitized = preg_replace('/<style\b[^<]*(?:(?!<\/style>)<[^<]*)*<\/style>/mi', '', $sanitized);
        
        // Remove iframe tags
        $sanitized = preg_replace('/<iframe\b[^<]*(?:(?!<\/iframe>)<[^<]*)*<\/iframe>/mi', '', $sanitized);
        
        // Remove object and embed tags
        $sanitized = preg_replace('/<(object|embed|form|meta|link)\b[^>]*>/i', '', $sanitized);
        
        // Remove javascript: and data: protocols
        $sanitized = preg_replace('/javascript\s*:/i', '', $sanitized);
        $sanitized = preg_replace('/data\s*:\s*text\/html/i', '', $sanitized);
        $sanitized = preg_replace('/vbscript\s*:/i', '', $sanitized);
        
        // Remove potentially dangerous Unicode characters
        $sanitized = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $sanitized);
        
        // Decode HTML entities to prevent double encoding issues
        $sanitized = html_entity_decode($sanitized, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        // Encode HTML entities for output safety
        $sanitized = htmlspecialchars($sanitized, ENT_QUOTES | ENT_HTML5, 'UTF-8', false);
        
        return trim($sanitized);
    }
}
