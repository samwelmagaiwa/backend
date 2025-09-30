<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DebugSwaggerController extends Controller
{
    public function debugUrls()
    {
        // Get the same data that would be passed to the modern-index.blade.php template
        $documentationTitle = config('l5-swagger.defaults.api_title', 'API Documentation');
        
        // Build the URLs array the same way L5Swagger does
        $urlsToDocs = [];
        
        // Get all documented APIs
        $documentations = config('l5-swagger.documentations', ['default' => []]);
        
        foreach ($documentations as $urlPath => $config) {
            $title = $config['api']['title'] ?? ucfirst($urlPath) . ' API';
            $jsonUrl = route('l5-swagger.'.$urlPath.'.docs');
            $urlsToDocs[$title] = $jsonUrl;
        }
        
        // Add our custom comprehensive API docs if not already included
        if (!in_array(route('api.docs'), $urlsToDocs)) {
            $urlsToDocs['Comprehensive API Docs'] = route('api.docs');
        }
        
        return response()->json([
            'success' => true,
            'debug_info' => [
                'documentation_title' => $documentationTitle,
                'urls_to_docs' => $urlsToDocs,
                'urls_count' => count($urlsToDocs),
                'primary_url' => reset($urlsToDocs),
                'comprehensive_url' => route('api.docs'),
                'comprehensive_url_accessible' => $this->testUrlAccessibility(route('api.docs')),
            ],
            'recommendations' => [
                'issue' => 'Swagger UI is loading from multiple URLs, might not be using the comprehensive one',
                'solution' => 'Force Swagger UI to use only the comprehensive API docs URL',
                'next_steps' => [
                    'Modify the blade template to use only the comprehensive URL',
                    'Clear browser cache completely',
                    'Ensure /api/api-docs is the primary and only URL'
                ]
            ]
        ], 200, [], JSON_PRETTY_PRINT);
    }
    
    private function testUrlAccessibility($url)
    {
        try {
            $context = stream_context_create([
                'http' => [
                    'timeout' => 5,
                    'method' => 'GET',
                    'header' => 'Accept: application/json'
                ]
            ]);
            
            $response = @file_get_contents($url, false, $context);
            if ($response === false) {
                return false;
            }
            
            $data = json_decode($response, true);
            return [
                'accessible' => true,
                'has_paths' => isset($data['paths']),
                'paths_count' => isset($data['paths']) ? count($data['paths']) : 0
            ];
        } catch (\Exception $e) {
            return [
                'accessible' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
