<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;
use L5Swagger\Http\Controllers\SwaggerController as BaseSwaggerController;

class SwaggerController extends BaseSwaggerController
{
    /**
     * Display modern Swagger UI page.
     *
     * @param Request $request
     * @param string $documentation
     * @return Response
     */
    public function api(Request $request, string $documentation = 'default'): Response
    {
        $documentationConfig = config('l5-swagger.documentations.' . $documentation);
        
        if (empty($documentationConfig)) {
            abort(404, 'API documentation not found.');
        }

        // Check if modern theme is enabled
        $useModernTheme = config('l5-swagger.defaults.ui.display.modern_theme', true);
        
        // Use modern template if enabled
        $templateName = $useModernTheme ? 'modern-index' : 'index';
        
        $data = [
            'documentation' => $documentation,
            'documentationTitle' => $documentationConfig['api']['title'] ?? 'API Documentation',
            'urlToDocs' => $this->getUrlToDocs($documentation),
            'useAbsolutePath' => $documentationConfig['paths']['use_absolute_path'] ?? false,
            'operationsSorter' => config('l5-swagger.defaults.operations_sort'),
            'configUrl' => config('l5-swagger.defaults.additional_config_url'),
            'validatorUrl' => config('l5-swagger.defaults.validator_url'),
        ];

        // Add URLs to docs
        $urlsToDocs = [];
        foreach (config('l5-swagger.documentations') as $key => $config) {
            $urlsToDocs[$config['api']['title'] ?? $key] = route('l5-swagger.' . $key . '.docs');
        }
        $data['urlsToDocs'] = $urlsToDocs;

        return response()->view('l5-swagger::' . $templateName, $data)
            ->header('Content-Type', 'text/html');
    }

    /**
     * Get the URL to the documentation JSON.
     *
     * @param string $documentation
     * @return string
     */
    private function getUrlToDocs(string $documentation): string
    {
        $config = config('l5-swagger.documentations.' . $documentation);
        
        if (empty($config)) {
            return '';
        }

        return route('l5-swagger.' . $documentation . '.docs', [], $config['paths']['use_absolute_path'] ?? false);
    }

    /**
     * Serve custom assets for modern theme.
     *
     * @param Request $request
     * @param string $asset
     * @return Response
     */
    public function asset(Request $request, string $asset): Response
    {
        $customAssetsPath = public_path('swagger-assets');
        $filePath = $customAssetsPath . '/' . $asset;
        
        // Check if custom asset exists
        if (File::exists($filePath)) {
            $mimeType = $this->getMimeType($asset);
            $content = File::get($filePath);
            
            return response($content)
                ->header('Content-Type', $mimeType)
                ->header('Cache-Control', 'public, max-age=86400'); // Cache for 1 day
        }

        // Fall back to parent method for default assets
        return parent::asset($request, $asset);
    }

    /**
     * Get MIME type for asset files.
     *
     * @param string $filename
     * @return string
     */
    private function getMimeType(string $filename): string
    {
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        
        $mimeTypes = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            'ttf' => 'font/ttf',
            'eot' => 'application/vnd.ms-fontobject',
        ];

        return $mimeTypes[$extension] ?? 'text/plain';
    }

    /**
     * Get enhanced documentation with modern features.
     *
     * @param Request $request
     * @param string $documentation
     * @return Response
     */
    public function docs(Request $request, string $documentation = 'default'): Response
    {
        $config = config('l5-swagger.documentations.' . $documentation);
        
        if (empty($config)) {
            abort(404, 'Documentation not found');
        }

        $docsPath = $config['paths']['docs'] ?? storage_path('api-docs');
        $jsonFile = $docsPath . '/' . ($config['paths']['docs_json'] ?? 'api-docs.json');

        if (!File::exists($jsonFile)) {
            abort(404, 'Documentation file not found');
        }

        $content = File::get($jsonFile);
        $docs = json_decode($content, true);

        // Enhance documentation with modern features
        if (is_array($docs)) {
            $docs = $this->enhanceDocumentation($docs);
            $content = json_encode($docs, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        }

        return response($content)
            ->header('Content-Type', 'application/json')
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    }

    /**
     * Enhance documentation with modern features.
     *
     * @param array $docs
     * @return array
     */
    private function enhanceDocumentation(array $docs): array
    {
        // Add server information if not present
        if (!isset($docs['servers']) || empty($docs['servers'])) {
            $docs['servers'] = [
                [
                    'url' => config('app.url', 'http://localhost:8000'),
                    'description' => config('app.name', 'API') . ' Server'
                ]
            ];
        }

        // Enhanced info section
        if (!isset($docs['info']['contact'])) {
            $docs['info']['contact'] = [
                'name' => 'API Support',
                'email' => config('mail.from.address', 'support@example.com'),
                'url' => config('app.url')
            ];
        }

        if (!isset($docs['info']['license'])) {
            $docs['info']['license'] = [
                'name' => 'Proprietary',
                'url' => config('app.url') . '/license'
            ];
        }

        // Add security schemes if not present
        if (!isset($docs['components']['securitySchemes'])) {
            $docs['components']['securitySchemes'] = [
                'bearerAuth' => [
                    'type' => 'http',
                    'scheme' => 'bearer',
                    'bearerFormat' => 'JWT',
                    'description' => 'JWT Authorization header using the Bearer scheme.'
                ],
                'apiKey' => [
                    'type' => 'apiKey',
                    'in' => 'header',
                    'name' => 'X-API-Key',
                    'description' => 'API key for authentication'
                ]
            ];
        }

        // Add common responses if not present
        if (!isset($docs['components']['responses'])) {
            $docs['components']['responses'] = [
                'UnauthorizedError' => [
                    'description' => 'Authentication information is missing or invalid',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'message' => ['type' => 'string', 'example' => 'Unauthenticated'],
                                    'error' => ['type' => 'string', 'example' => 'unauthorized']
                                ]
                            ]
                        ]
                    ]
                ],
                'ValidationError' => [
                    'description' => 'Validation failed',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'message' => ['type' => 'string', 'example' => 'The given data was invalid.'],
                                    'errors' => ['type' => 'object']
                                ]
                            ]
                        ]
                    ]
                ],
                'ServerError' => [
                    'description' => 'Internal server error',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'message' => ['type' => 'string', 'example' => 'Internal server error'],
                                    'error' => ['type' => 'string']
                                ]
                            ]
                        ]
                    ]
                ]
            ];
        }

        // Sort paths alphabetically for better organization
        if (isset($docs['paths'])) {
            ksort($docs['paths']);
        }

        // Add tags if not present
        if (!isset($docs['tags'])) {
            $tags = [];
            if (isset($docs['paths'])) {
                foreach ($docs['paths'] as $path => $methods) {
                    foreach ($methods as $method => $operation) {
                        if (isset($operation['tags'])) {
                            foreach ($operation['tags'] as $tag) {
                                if (!in_array($tag, array_column($tags, 'name'))) {
                                    $tags[] = [
                                        'name' => $tag,
                                        'description' => ucfirst($tag) . ' related endpoints'
                                    ];
                                }
                            }
                        }
                    }
                }
            }
            $docs['tags'] = $tags;
        }

        return $docs;
    }
}
