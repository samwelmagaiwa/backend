<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $documentationTitle }}</title>
    
    <!-- Modern Design Meta -->
    <meta name="description" content="Modern API Documentation for {{ $documentationTitle }}">
    <meta name="author" content="MNH API Team">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ l5_swagger_asset($documentation, 'favicon-32x32.png') }}" sizes="32x32"/>
    <link rel="icon" type="image/png" href="{{ l5_swagger_asset($documentation, 'favicon-16x16.png') }}" sizes="16x16"/>
    
    <!-- Swagger UI CSS -->
    <link rel="stylesheet" type="text/css" href="{{ l5_swagger_asset($documentation, 'swagger-ui.css') }}">
    
    <!-- Custom Modern CSS -->
    <link rel="stylesheet" type="text/css" href="{{ asset('swagger-assets/modern-api-docs.css') }}">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* Custom overrides for better loading experience */
        html {
            box-sizing: border-box;
            overflow: -moz-scrollbars-vertical;
            overflow-y: scroll;
        }
        
        *,
        *:before,
        *:after {
            box-sizing: inherit;
        }

        body {
            margin: 0;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            min-height: 100vh;
        }

        /* Loading animation */
        .api-loading {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            transition: opacity 0.3s ease;
        }

        .api-loading.hidden {
            opacity: 0;
            pointer-events: none;
        }

        .loading-spinner {
            width: 40px;
            height: 40px;
            border: 3px solid #e2e8f0;
            border-top: 3px solid #3b82f6;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .loading-text {
            margin-top: 1rem;
            color: #64748b;
            font-weight: 500;
        }

        /* Custom header enhancement */
        .modern-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 0;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .modern-header .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .modern-header h1 {
            margin: 0;
            font-size: 1.875rem;
            font-weight: 700;
        }

        .modern-header p {
            margin: 0.5rem 0 0 0;
            opacity: 0.9;
            font-size: 1rem;
        }

        .version-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.875rem;
            font-weight: 500;
            margin-left: 1rem;
        }

        /* Server info enhancement */
        .server-info {
            background: white;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            border: 1px solid #e2e8f0;
        }

        .server-info h3 {
            margin: 0 0 0.5rem 0;
            font-size: 1rem;
            font-weight: 600;
            color: #1a202c;
        }

        .server-url {
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
            background: #f8fafc;
            padding: 0.5rem;
            border-radius: 4px;
            border: 1px solid #e2e8f0;
            color: #3b82f6;
            font-size: 0.875rem;
        }

        /* Download buttons */
        .download-btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: #ff6c37;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
        }
        
        .download-btn:hover {
            background: #e55a2b;
            color: white;
            text-decoration: none;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Footer */
        .modern-footer {
            margin-top: 1.5rem;
            padding: 1rem 0;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            color: #64748b;
            font-size: 0.875rem;
        }

        /* View Options compact card */
        .view-options-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 0.5rem 0.75rem;
            margin: 0.5rem 0;
            box-shadow: 0 1px 2px rgba(0,0,0,0.04);
        }
        .view-options-row {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem 0.75rem;
            align-items: center;
            justify-content: space-between;
        }
        .view-options-left,
        .view-options-right {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        .view-search-input {
            border: 1px solid #cbd5e1;
            background: #f8fafc;
            padding: 0.375rem 0.5rem;
            border-radius: 6px;
            font-size: 0.875rem;
            min-width: 240px;
        }

        @if(config('l5-swagger.defaults.ui.display.dark_mode'))
        /* Dark mode overrides */
        @media (prefers-color-scheme: dark) {
            body {
                background: linear-gradient(135deg, #1a202c 0%, #2d3748 100%);
            }
            
            .modern-header {
                background: linear-gradient(135deg, #4c51bf 0%, #553c9a 100%);
            }
            
            .server-info {
                background: #2d3748;
                border-color: #4a5568;
            }
            
            .server-info h3 {
                color: #f7fafc;
            }
            
            .server-url {
                background: #1a202c;
                border-color: #4a5568;
                color: #63b3ed;
            }
            
            .modern-footer {
                border-color: #4a5568;
                color: #a0aec0;
            }
        }
        @endif
        /* Hide Swagger default info panel below the Explore input */
        .swagger-ui .info {
            display: none !important;
        }
    </style>
</head>

<body @if(config('l5-swagger.defaults.ui.display.dark_mode')) class="dark-mode" @endif>
    <!-- Loading Screen -->
    <div class="api-loading" id="apiLoading">
        <div style="text-align: center;">
            <div class="loading-spinner"></div>
            <div class="loading-text">Loading API Documentation...</div>
        </div>
    </div>

    <!-- Enhanced Modern Header -->
    <div class="modern-header">
        <div class="container">
            <h1>
                🚀 {{ $documentationTitle }}
                <span class="version-badge">v2.0.0</span>
            </h1>
            <p>🎯 Comprehensive API Documentation - All <span id="headerTotalEndpoints">0</span> Endpoints with Complete CRUD & System Management</p>
            
            <!-- API Statistics Dashboard -->
            <div class="api-stats" style="margin-top: 1.5rem; display: flex; flex-wrap: wrap; gap: 1rem; align-items: center;">
                <div class="stat-badge" style="background: rgba(255, 255, 255, 0.15); padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.875rem;">
                    <span style="font-weight: 600;">📊 <span id="statTotalEndpoints">0</span></span> Total Endpoints
                </div>
                <div class="stat-badge" style="background: rgba(255, 255, 255, 0.15); padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.875rem;">
                    <span style="font-weight: 600;">🏗️ <span id="statCategories">0</span></span> Categories
                </div>
                <div class="stat-badge" style="background: rgba(255, 255, 255, 0.15); padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.875rem;">
                    <span style="font-weight: 600;">🔧 <span id="statPaths">0</span></span> Paths
                </div>
                <div class="stat-badge" style="background: rgba(255, 255, 255, 0.15); padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.875rem;">
                    <span style="font-weight: 600;">🎯 ✅</span> Complete CRUD
                </div>
            </div>
            
            <!-- HTTP Method Distribution -->
            <div class="method-stats" style="margin-top: 1rem; display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: center;">
                <div class="method-badge get" style="background: #61affe; color: white; padding: 0.375rem 0.75rem; border-radius: 6px; font-size: 0.8rem; font-weight: 500;">
                    <span id="badgeGet">GET 0 (0%)</span>
                </div>
                <div class="method-badge post" style="background: #49cc90; color: white; padding: 0.375rem 0.75rem; border-radius: 6px; font-size: 0.8rem; font-weight: 500;">
                    <span id="badgePost">POST 0 (0%)</span>
                </div>
                <div class="method-badge put" style="background: #fca130; color: white; padding: 0.375rem 0.75rem; border-radius: 6px; font-size: 0.8rem; font-weight: 500;">
                    <span id="badgePut">PUT 0 (0%)</span>
                </div>
                <div class="method-badge patch" style="background: #50e3c2; color: white; padding: 0.375rem 0.75rem; border-radius: 6px; font-size: 0.8rem; font-weight: 500;">
                    <span id="badgePatch">PATCH 0 (0%)</span>
                </div>
                <div class="method-badge delete" style="background: #f93e3e; color: white; padding: 0.375rem 0.75rem; border-radius: 6px; font-size: 0.8rem; font-weight: 500;">
                    <span id="badgeDelete">DELETE 0 (0%)</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Wrapper -->
    <div style="max-width: 1200px; margin: 0 auto; padding: 0 1rem;">
        <!-- Quick Actions -->
        <div class="server-info" style="display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: center; justify-content: space-between;">
            <div style="flex: 1; min-width: 260px;">
                <h3>🌐 API Server</h3>
                <div class="server-url" id="currentServer">{{ config('app.url') ?? 'http://localhost:8000' }}</div>
            </div>
            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                <a href="{{ url('/api/docs') }}" target="_blank" class="download-btn" style="text-decoration: none;">
                    View JSON
                </a>
                <a href="{{ url('/api/postman-collection') }}" target="_blank" class="download-btn" style="text-decoration: none; background:#0ea5e9;">
                    Download Postman
                </a>
                <a href="{{ url('/api/documentation') }}" target="_blank" class="download-btn" style="text-decoration: none; background:#16a34a;">
                    Classic UI
                </a>
            </div>
        </div>

        <!-- View Options (Servers + Authorize + Search) -->
        <div class="view-options-card" id="viewOptions">
            <div class="view-options-row">
                <div class="view-options-left" id="viewOptionsLeft">
                    <!-- The Swagger scheme-container (servers + authorize) will be moved here after UI loads -->
                </div>
                <div class="view-options-right">
                    <input id="viewSearch" class="view-search-input" type="text" placeholder="Search endpoints..." aria-label="Search endpoints" />
                    <button id="viewAuthorizeBtn" type="button" class="download-btn" style="background:#16a34a;">Authorize</button>
                </div>
            </div>
        </div>

        <!-- Swagger UI Container -->
        <div id="swagger-ui"></div>

        <!-- Footer -->
        <div class="modern-footer">
            <p>Built with ❤️ by the MNH Development Team | Powered by Swagger UI & Laravel</p>
            <p style="margin-top: 0.5rem; font-size: 0.75rem; opacity: 0.8;">
                Last updated: {{ date('Y-m-d H:i:s') }}
            </p>
        </div>
    </div>

    <!-- Swagger UI Scripts -->
    <script src="{{ l5_swagger_asset($documentation, 'swagger-ui-bundle.js') }}"></script>
    <script src="{{ l5_swagger_asset($documentation, 'swagger-ui-standalone-preset.js') }}"></script>
    
    <!-- Modern API Enhancements Script -->
    <script src="{{ asset('swagger-assets/modern-api-enhancements.js') }}"></script>
    
    <script>
        window.onload = function() {
            // Hide loading screen
            setTimeout(function() {
                document.getElementById('apiLoading').classList.add('hidden');
                setTimeout(function() {
                    document.getElementById('apiLoading').style.display = 'none';
                }, 300);
            }, 500);

            // Force use of comprehensive API docs URL only
            const comprehensiveApiUrl = "{{ url('/api/docs') }}";
            
            const urls = [
                {name: "Comprehensive API Docs (All 336 Endpoints)", url: comprehensiveApiUrl}
            ];

            console.log('🔧 Forcing Swagger UI to use comprehensive API docs:', comprehensiveApiUrl);

            // Build Swagger UI
            const ui = SwaggerUIBundle({
                dom_id: '#swagger-ui',
                url: comprehensiveApiUrl,  // Use single URL instead of urls array
                "urls.primaryName": "Comprehensive API Documentation",
                operationsSorter: {!! isset($operationsSorter) ? '\"' . $operationsSorter . '\"' : 'null' !!},
                configUrl: {!! isset($configUrl) ? '\"' . $configUrl . '\"' : 'null' !!},
                validatorUrl: {!! isset($validatorUrl) ? '\"' . $validatorUrl . '\"' : 'null' !!},
                oauth2RedirectUrl: "{{ route('l5-swagger.'.$documentation.'.oauth2_callback', [], $useAbsolutePath) }}",

                requestInterceptor: function(request) {
                    request.headers['X-CSRF-TOKEN'] = '{{ csrf_token() }}';
                    request.headers['Accept'] = 'application/json';
                    return request;
                },

                responseInterceptor: function(response) {
                    // Add custom response handling if needed
                    return response;
                },

                onComplete: function() {
                    // Custom completion handler
                    console.log('🚀 Modern API Documentation loaded successfully!');
                    console.log('✅ Comprehensive API Docs URL:', comprehensiveApiUrl);
                    
                    // Verify endpoint count
                    const operations = document.querySelectorAll('.opblock');
                    console.log('📊 Total endpoints loaded:', operations.length);
                    
                    if (operations.length < 50) {
                        console.warn('⚠️ Warning: Only', operations.length, 'endpoints loaded. Expected 265+');
                        console.log('🔍 Check if comprehensive API docs are being used correctly.');
                    } else {
                        console.log('✅ Success: All comprehensive endpoints loaded!');
                    }
                    
                    // Add custom enhancements
                    enhanceSwaggerUI();
                },

                presets: [
                    SwaggerUIBundle.presets.apis,
                    SwaggerUIStandalonePreset
                ],

                plugins: [
                    SwaggerUIBundle.plugins.DownloadUrl
                ],

                layout: "StandaloneLayout",
                docExpansion: "{!! config('l5-swagger.defaults.ui.display.doc_expansion', 'none') !!}",
                deepLinking: true,
                filter: {!! config('l5-swagger.defaults.ui.display.filter') ? 'true' : 'false' !!},
                persistAuthorization: "{!! config('l5-swagger.defaults.ui.authorization.persist_authorization') ? 'true' : 'false' !!}",
                showExtensions: true,
                showCommonExtensions: true,
                tryItOutEnabled: true,

                // Custom configuration for better UX
                syntaxHighlight: {
                    activate: true,
                    theme: "agate"
                },
                
                // Enhanced request/response display
                defaultModelsExpandDepth: 2,
                defaultModelExpandDepth: 2,
                defaultModelRendering: 'example',
                displayOperationId: false,
                displayRequestDuration: true,
                maxDisplayedTags: 50,
                showRequestHeaders: true,
                supportedSubmitMethods: ['get', 'post', 'put', 'delete', 'patch', 'head', 'options']
            });

            window.ui = ui;

            @if(in_array('oauth2', array_column(config('l5-swagger.defaults.securityDefinitions.securitySchemes'), 'type')))
            ui.initOAuth({
                usePkceWithAuthorizationCodeGrant: "{!! (bool)config('l5-swagger.defaults.ui.authorization.oauth2.use_pkce_with_authorization_code_grant') !!}"
            });
            @endif

            // Custom enhancements function
            function enhanceSwaggerUI() {
                // Add method count badges and dynamic header stats
                setTimeout(function() {
                    addMethodCountBadges();
                    addCustomFeatures();
                    setupViewOptionsToolbar();
                    updateHeaderStats();
                }, 800);
            }

            function addMethodCountBadges() {
                const tags = document.querySelectorAll('.opblock-tag-section h3');
                tags.forEach(function(tagHeader) {
                    const tagSection = tagHeader.closest('.opblock-tag-section');
                    if (tagSection) {
                        const operations = tagSection.querySelectorAll('.opblock');
                        const count = operations.length;
                        
                        // Add operation count badge
                        const countBadge = document.createElement('span');
                        countBadge.style.cssText = `
                            background: rgba(255, 255, 255, 0.2);
                            color: white;
                            padding: 0.25rem 0.5rem;
                            border-radius: 1rem;
                            font-size: 0.75rem;
                            font-weight: 500;
                            margin-left: 0.5rem;
                        `;
                        countBadge.textContent = `${count} endpoint${count !== 1 ? 's' : ''}`;
                        tagHeader.appendChild(countBadge);
                    }
                });
            }

            async function updateHeaderStats() {
                try {
                    const res = await fetch(comprehensiveApiUrl, { cache: 'no-store' });
                    const spec = await res.json();
                    const paths = spec.paths || {};
                    const tags = new Set((spec.tags || []).map(t => t.name));
                    let totalOps = 0;
                    const methodCounts = { get:0, post:0, put:0, patch:0, delete:0 };
                    Object.keys(paths).forEach(p => {
                        const ops = paths[p] || {};
                        Object.keys(ops).forEach(m => {
                            const lm = m.toLowerCase();
                            if (methodCounts.hasOwnProperty(lm)) {
                                methodCounts[lm]++;
                                totalOps++;
                                // collect tags if spec.tags not provided
                                const op = ops[m] || {};
                                (op.tags || []).forEach(t => tags.add(t));
                            }
                        });
                    });
                    const totalPaths = Object.keys(paths).length;
                    // Update header counters
                    const setText = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };
                    setText('headerTotalEndpoints', totalOps);
                    setText('statTotalEndpoints', totalOps);
                    setText('statCategories', tags.size);
                    setText('statPaths', totalPaths);
                    function pct(n){ return totalOps ? Math.round((n/totalOps)*100) : 0; }
                    setText('badgeGet', `GET ${methodCounts.get} (${pct(methodCounts.get)}%)`);
                    setText('badgePost', `POST ${methodCounts.post} (${pct(methodCounts.post)}%)`);
                    setText('badgePut', `PUT ${methodCounts.put} (${pct(methodCounts.put)}%)`);
                    setText('badgePatch', `PATCH ${methodCounts.patch} (${pct(methodCounts.patch)}%)`);
                    setText('badgeDelete', `DELETE ${methodCounts.delete} (${pct(methodCounts.delete)}%)`);
                } catch (e) {
                    console.warn('Failed to compute header stats', e);
                }
            }

            function addCustomFeatures() {
                // Add search functionality enhancement
                const filterInput = document.querySelector('.operation-filter-input');
                if (filterInput) {
                    filterInput.placeholder = "🔍 Search endpoints...";
                    filterInput.style.borderRadius = '6px';
                }

                // Add copy to clipboard for code examples
                const codeBlocks = document.querySelectorAll('pre code');
                codeBlocks.forEach(function(block) {
                    const copyBtn = document.createElement('button');
                    copyBtn.innerHTML = '📋';
                    copyBtn.style.cssText = `
                        position: absolute;
                        top: 0.5rem;
                        right: 0.5rem;
                        background: rgba(0, 0, 0, 0.1);
                        border: none;
                        border-radius: 4px;
                        padding: 0.25rem 0.5rem;
                        cursor: pointer;
                        font-size: 0.75rem;
                    `;
                    
                    copyBtn.onclick = function() {
                        navigator.clipboard.writeText(block.textContent);
                        copyBtn.innerHTML = '✅';
                        setTimeout(() => copyBtn.innerHTML = '📋', 2000);
                    };
                    
                    if (block.parentElement.style.position !== 'relative') {
                        block.parentElement.style.position = 'relative';
                    }
                    block.parentElement.appendChild(copyBtn);
                });

                // Update server info
                const serverSelect = document.querySelector('.servers select');
                if (serverSelect) {
                    serverSelect.addEventListener('change', function() {
                        const currentServerDiv = document.getElementById('currentServer');
                        if (currentServerDiv) {
                            currentServerDiv.textContent = this.value;
                        }
                    });
                }
            }

            // Build compact toolbar with Servers + Authorize + Search
            function setupViewOptionsToolbar() {
                const toolbarCard = document.getElementById('viewOptions');
                const toolbarLeft = document.getElementById('viewOptionsLeft');
                const viewSearch = document.getElementById('viewSearch');

                // Helper to connect our search box to Swagger's native filter
                function wireSearchBox() {
                    const filterInput = document.querySelector('.operation-filter-input');
                    if (filterInput && viewSearch) {
                        viewSearch.addEventListener('input', function () {
                            filterInput.value = viewSearch.value;
                            filterInput.dispatchEvent(new Event('input', { bubbles: true }));
                        });
                        return true;
                    }
                    return false;
                }

                // Try to locate Swagger's native "View Options" block and inject our controls there
                const headings = Array.from(document.querySelectorAll('.swagger-ui h3, .swagger-ui h4'));
                const viewHeading = headings.find(h => /view options/i.test(h.textContent || ''));
                let injectedIntoNative = false;
                if (viewHeading) {
                    // Create a right-side container inside the native section
                    const nativeContainer = document.createElement('div');
                    nativeContainer.className = 'view-options-right';
                    nativeContainer.style.marginLeft = 'auto';

                    // Move scheme-container into native block (Servers + Authorize)
                    const schemeContainer = document.querySelector('.scheme-container');
                    if (schemeContainer) {
                        // Place our container just after the heading
                        viewHeading.parentElement.appendChild(nativeContainer);
                        nativeContainer.appendChild(schemeContainer);
                        schemeContainer.style.margin = '0';
                        injectedIntoNative = true;
                    }

                    // Add Authorize proxy
                    const proxyAuthorizeBtn = document.createElement('button');
                    proxyAuthorizeBtn.id = 'viewAuthorizeBtn';
                    proxyAuthorizeBtn.type = 'button';
                    proxyAuthorizeBtn.className = 'download-btn';
                    proxyAuthorizeBtn.style.background = '#16a34a';
                    proxyAuthorizeBtn.textContent = 'Authorize';
                    nativeContainer.appendChild(proxyAuthorizeBtn);

                    // Link proxy to native authorize
                    const nativeAuthorizeBtn = document.querySelector('.auth-wrapper .authorize, .scheme-container .authorize');
                    if (nativeAuthorizeBtn) {
                        proxyAuthorizeBtn.addEventListener('click', function () { nativeAuthorizeBtn.click(); });
                    } else {
                        proxyAuthorizeBtn.style.display = 'none';
                    }

                    // Add search box
                    const nativeSearch = document.createElement('input');
                    nativeSearch.id = 'viewSearch';
                    nativeSearch.className = 'view-search-input';
                    nativeSearch.type = 'text';
                    nativeSearch.placeholder = 'Search endpoints...';
                    nativeContainer.appendChild(nativeSearch);

                    // Wire it
                    wireSearchBox();
                }

                // Fallback: use our custom card if we couldn't inject into native
                if (!injectedIntoNative && toolbarLeft) {
                    const schemeContainer = document.querySelector('.scheme-container');
                    if (schemeContainer) {
                        toolbarLeft.appendChild(schemeContainer);
                        schemeContainer.style.margin = '0';
                    }

                    // Wire Authorize button
                    const nativeAuthorizeBtn = document.querySelector('.auth-wrapper .authorize, .scheme-container .authorize');
                    const proxyAuthorizeBtn = document.getElementById('viewAuthorizeBtn');
                    if (nativeAuthorizeBtn && proxyAuthorizeBtn) {
                        proxyAuthorizeBtn.addEventListener('click', function () { nativeAuthorizeBtn.click(); });
                    } else if (proxyAuthorizeBtn) {
                        proxyAuthorizeBtn.style.display = 'none';
                    }

                    wireSearchBox();
                } else if (injectedIntoNative && toolbarCard) {
                    // Hide our fallback card if we successfully injected into native block
                    toolbarCard.style.display = 'none';
                }
            }
        };

        // Add keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl/Cmd + K to focus search
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                const searchInput = document.querySelector('.operation-filter-input');
                if (searchInput) {
                    searchInput.focus();
                }
            }
            
            // Escape to clear search
            if (e.key === 'Escape') {
                const searchInput = document.querySelector('.operation-filter-input');
                if (searchInput && searchInput === document.activeElement) {
                    searchInput.value = '';
                    searchInput.dispatchEvent(new Event('input'));
                }
            }
        });

        // Add smooth scrolling to anchors
        document.addEventListener('click', function(e) {
            if (e.target.tagName === 'A' && e.target.getAttribute('href') && e.target.getAttribute('href').startsWith('#')) {
                e.preventDefault();
                const target = document.querySelector(e.target.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    </script>

    <!-- Analytics or additional tracking scripts can be added here -->
    <!-- Google Analytics, Hotjar, etc. -->
    
</body>
</html>
