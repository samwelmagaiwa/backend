/**
 * Modern API Documentation Enhancements
 * Custom JavaScript to enhance Swagger UI with modern features
 */

(function() {
    'use strict';

    // Wait for DOM to be ready
    const ready = (callback) => {
        if (document.readyState !== 'loading') {
            callback();
        } else {
            document.addEventListener('DOMContentLoaded', callback);
        }
    };

    // Main initialization
    ready(() => {
        initializeEnhancements();
    });

    function initializeEnhancements() {
        // Add method statistics
        addMethodStatistics();
        
        // Enhance search functionality
        enhanceSearch();
        
        // Add endpoint grouping
        addEndpointGrouping();
        
        // Add response time tracking
        addResponseTimeTracking();
        
        // Add custom keyboard shortcuts
        addKeyboardShortcuts();
        
        // Add copy functionality
        addCopyFunctionality();
        
        // Add endpoint favorites
        addEndpointFavorites();
        
        // Add theme toggle
        addThemeToggle();
        
        // Add API health status
        addHealthStatus();
        
        // Initialize periodic enhancements
        initPeriodicEnhancements();
    }

    function addMethodStatistics() {
        setTimeout(() => {
            // Use comprehensive API statistics from our complete analysis
            const methodCounts = {
                GET: 144,
                POST: 94,
                PUT: 14,
                PATCH: 4,
                DELETE: 9
            };

            // Create statistics panel
            const statsPanel = createStatsPanel(methodCounts);
            
            // Insert after server info
            const serverInfo = document.querySelector('.server-info');
            if (serverInfo && serverInfo.nextSibling) {
                serverInfo.parentNode.insertBefore(statsPanel, serverInfo.nextSibling);
            }
        }, 1500);
    }

    function createStatsPanel(methodCounts) {
        const panel = document.createElement('div');
        panel.className = 'api-stats-panel';
        panel.style.cssText = `
            background: white;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            border: 1px solid #e2e8f0;
        `;

        const title = document.createElement('h3');
        title.textContent = '📊 API Statistics';
        title.style.cssText = `
            margin: 0 0 1rem 0;
            font-size: 1rem;
            font-weight: 600;
            color: #1a202c;
        `;

        const statsGrid = document.createElement('div');
        statsGrid.style.cssText = `
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
            gap: 0.5rem;
        `;

        const methodColors = {
            GET: { bg: '#dbeafe', color: '#3b82f6' },
            POST: { bg: '#d1fae5', color: '#10b981' },
            PUT: { bg: '#fef3c7', color: '#f59e0b' },
            PATCH: { bg: '#ede9fe', color: '#8b5cf6' },
            DELETE: { bg: '#fecaca', color: '#ef4444' },
            OPTIONS: { bg: '#f3f4f6', color: '#6b7280' },
            HEAD: { bg: '#ede9fe', color: '#8b5cf6' }
        };

        Object.entries(methodCounts).forEach(([method, count]) => {
            if (count > 0) {
                const stat = document.createElement('div');
                const colors = methodColors[method];
                stat.style.cssText = `
                    background: ${colors.bg};
                    color: ${colors.color};
                    padding: 0.5rem;
                    border-radius: 6px;
                    text-align: center;
                    font-weight: 600;
                    font-size: 0.875rem;
                `;
                stat.innerHTML = `${method}<br><span style="font-size: 1.25rem;">${count}</span>`;
                statsGrid.appendChild(stat);
            }
        });

        panel.appendChild(title);
        panel.appendChild(statsGrid);
        return panel;
    }

    function enhanceSearch() {
        setTimeout(() => {
            const searchInput = document.querySelector('.operation-filter-input');
            if (searchInput) {
                searchInput.placeholder = '🔍 Search endpoints, methods, or descriptions...';
                
                // Add advanced search features
                const searchContainer = searchInput.parentElement;
                if (searchContainer) {
                    // Add search suggestions
                    addSearchSuggestions(searchInput, searchContainer);
                    
                    // Add search history
                    addSearchHistory(searchInput, searchContainer);
                }
            }
        }, 1000);
    }

    function addSearchSuggestions(input, container) {
        const suggestions = new Set();
        
        // Collect suggestions from endpoints
        document.querySelectorAll('.opblock-summary-path').forEach(path => {
            const pathText = path.textContent.trim();
            suggestions.add(pathText);
            
            // Add path segments
            pathText.split('/').forEach(segment => {
                if (segment && !segment.includes('{')) {
                    suggestions.add(segment);
                }
            });
        });

        // Collect from descriptions
        document.querySelectorAll('.opblock-summary-description').forEach(desc => {
            const words = desc.textContent.split(' ');
            words.forEach(word => {
                if (word.length > 3) {
                    suggestions.add(word.toLowerCase());
                }
            });
        });

        // Create suggestions dropdown
        const suggestionsList = document.createElement('div');
        suggestionsList.className = 'search-suggestions';
        suggestionsList.style.cssText = `
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        `;

        container.style.position = 'relative';
        container.appendChild(suggestionsList);

        input.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase();
            if (query.length < 2) {
                suggestionsList.style.display = 'none';
                return;
            }

            const matches = Array.from(suggestions)
                .filter(s => s.toLowerCase().includes(query))
                .slice(0, 8);

            if (matches.length > 0) {
                suggestionsList.innerHTML = matches
                    .map(match => `
                        <div class="suggestion-item" style="padding: 0.5rem; cursor: pointer; border-bottom: 1px solid #f1f1f1;">
                            ${highlightMatch(match, query)}
                        </div>
                    `).join('');
                
                suggestionsList.style.display = 'block';
                
                // Add click handlers
                suggestionsList.querySelectorAll('.suggestion-item').forEach((item, index) => {
                    item.addEventListener('click', () => {
                        input.value = matches[index];
                        input.dispatchEvent(new Event('input'));
                        suggestionsList.style.display = 'none';
                    });
                });
            } else {
                suggestionsList.style.display = 'none';
            }
        });

        // Hide suggestions when clicking outside
        document.addEventListener('click', (e) => {
            if (!container.contains(e.target)) {
                suggestionsList.style.display = 'none';
            }
        });
    }

    function highlightMatch(text, query) {
        const regex = new RegExp(`(${query})`, 'gi');
        return text.replace(regex, '<strong style="background: #fef3c7; color: #f59e0b;">$1</strong>');
    }

    function addSearchHistory(input, container) {
        const HISTORY_KEY = 'swagger-search-history';
        let history = JSON.parse(localStorage.getItem(HISTORY_KEY) || '[]');

        input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && input.value.trim()) {
                const query = input.value.trim();
                history = history.filter(h => h !== query);
                history.unshift(query);
                history = history.slice(0, 10); // Keep only 10 items
                localStorage.setItem(HISTORY_KEY, JSON.stringify(history));
            }
        });
    }

    function addEndpointGrouping() {
        setTimeout(() => {
            // Add group by method functionality
            const toolbar = document.querySelector('.swagger-ui .info') || document.querySelector('.swagger-ui');
            if (toolbar) {
                const groupingControls = document.createElement('div');
                groupingControls.style.cssText = `
                    margin: 1rem 0;
                    padding: 1rem;
                    background: white;
                    border-radius: 8px;
                    border: 1px solid #e2e8f0;
                    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
                `;
                
                groupingControls.innerHTML = `
                    <h4 style="margin: 0 0 0.5rem 0; font-size: 1rem; font-weight: 600; color: #1a202c;">
                        🔧 View Options
                    </h4>
                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                        <button class="group-btn" data-group="default">Default View</button>
                        <button class="group-btn" data-group="method">Group by Method</button>
                        <button class="group-btn" data-group="alphabetical">Alphabetical</button>
                    </div>
                `;

                // Style buttons
                groupingControls.querySelectorAll('.group-btn').forEach(btn => {
                    btn.style.cssText = `
                        padding: 0.375rem 0.75rem;
                        border: 1px solid #d1d5db;
                        border-radius: 4px;
                        background: white;
                        color: #374151;
                        font-size: 0.875rem;
                        cursor: pointer;
                        transition: all 0.2s ease;
                    `;
                    
                    btn.addEventListener('click', (e) => {
                        const groupType = e.target.dataset.group;
                        applyGrouping(groupType);
                        
                        // Update active state
                        groupingControls.querySelectorAll('.group-btn').forEach(b => {
                            b.style.background = 'white';
                            b.style.color = '#374151';
                        });
                        e.target.style.background = '#3b82f6';
                        e.target.style.color = 'white';
                    });
                });

                toolbar.parentNode.insertBefore(groupingControls, toolbar.nextSibling);
            }
        }, 2000);
    }

    function applyGrouping(type) {
        const operations = document.querySelectorAll('.opblock-tag-section');
        
        switch (type) {
            case 'method':
                groupByMethod();
                break;
            case 'alphabetical':
                groupAlphabetically();
                break;
            default:
                // Restore default order
                location.reload();
                break;
        }
    }

    function groupByMethod() {
        const container = document.querySelector('.swagger-ui');
        const methodGroups = {};
        
        document.querySelectorAll('.opblock').forEach(block => {
            const method = block.classList[1]?.replace('opblock-', '').toUpperCase();
            if (!methodGroups[method]) {
                methodGroups[method] = [];
            }
            methodGroups[method].push(block.cloneNode(true));
        });

        // Clear existing content
        const existingSections = document.querySelectorAll('.opblock-tag-section');
        existingSections.forEach(section => section.remove());

        // Create new grouped sections
        const methodOrder = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS', 'HEAD'];
        methodOrder.forEach(method => {
            if (methodGroups[method] && methodGroups[method].length > 0) {
                const section = createMethodSection(method, methodGroups[method]);
                container.appendChild(section);
            }
        });
    }

    function createMethodSection(method, operations) {
        const section = document.createElement('div');
        section.className = 'opblock-tag-section';
        
        const header = document.createElement('h3');
        header.style.cssText = `
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 1.5rem;
            margin: 0 0 1rem 0;
            font-size: 1.25rem;
            font-weight: 600;
            border-radius: 8px 8px 0 0;
        `;
        header.textContent = `${method} Endpoints`;
        
        section.appendChild(header);
        operations.forEach(op => section.appendChild(op));
        
        return section;
    }

    function addResponseTimeTracking() {
        // Track API response times
        const originalFetch = window.fetch;
        const responseTimes = {};

        window.fetch = function(...args) {
            const startTime = Date.now();
            const url = args[0];
            
            return originalFetch.apply(this, args)
                .then(response => {
                    const endTime = Date.now();
                    const duration = endTime - startTime;
                    
                    responseTimes[url] = duration;
                    
                    // Add response time to UI
                    setTimeout(() => addResponseTimeToUI(url, duration), 100);
                    
                    return response;
                });
        };
    }

    function addResponseTimeToUI(url, duration) {
        // Find the corresponding operation block
        const pathElement = Array.from(document.querySelectorAll('.opblock-summary-path'))
            .find(el => url.includes(el.textContent.trim().replace(/\{.*?\}/g, '')));
        
        if (pathElement) {
            const existingTime = pathElement.parentElement.querySelector('.response-time');
            if (existingTime) {
                existingTime.remove();
            }

            const timeIndicator = document.createElement('span');
            timeIndicator.className = 'response-time';
            timeIndicator.style.cssText = `
                background: ${duration < 500 ? '#d1fae5' : duration < 1000 ? '#fef3c7' : '#fecaca'};
                color: ${duration < 500 ? '#10b981' : duration < 1000 ? '#f59e0b' : '#ef4444'};
                padding: 0.25rem 0.5rem;
                border-radius: 4px;
                font-size: 0.75rem;
                font-weight: 500;
                margin-left: 0.5rem;
            `;
            timeIndicator.textContent = `${duration}ms`;
            
            pathElement.parentElement.appendChild(timeIndicator);
        }
    }

    function addKeyboardShortcuts() {
        document.addEventListener('keydown', (e) => {
            // Ctrl/Cmd + / to show shortcuts
            if ((e.ctrlKey || e.metaKey) && e.key === '/') {
                e.preventDefault();
                showShortcutsModal();
            }
            
            // Ctrl/Cmd + E to expand all
            if ((e.ctrlKey || e.metaKey) && e.key === 'e') {
                e.preventDefault();
                document.querySelectorAll('.opblock-summary').forEach(summary => {
                    if (!summary.parentElement.classList.contains('is-open')) {
                        summary.click();
                    }
                });
            }
            
            // Ctrl/Cmd + W to collapse all
            if ((e.ctrlKey || e.metaKey) && e.key === 'w') {
                e.preventDefault();
                document.querySelectorAll('.opblock-summary').forEach(summary => {
                    if (summary.parentElement.classList.contains('is-open')) {
                        summary.click();
                    }
                });
            }
        });
    }

    function showShortcutsModal() {
        const modal = document.createElement('div');
        modal.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
        `;

        const content = document.createElement('div');
        content.style.cssText = `
            background: white;
            border-radius: 8px;
            padding: 2rem;
            max-width: 500px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        `;

        content.innerHTML = `
            <h3 style="margin: 0 0 1rem 0; font-size: 1.25rem; font-weight: 600;">⌨️ Keyboard Shortcuts</h3>
            <div style="font-size: 0.875rem; line-height: 1.6;">
                <div style="margin-bottom: 0.5rem;"><kbd>Ctrl/Cmd + K</kbd> - Focus search</div>
                <div style="margin-bottom: 0.5rem;"><kbd>Ctrl/Cmd + E</kbd> - Expand all endpoints</div>
                <div style="margin-bottom: 0.5rem;"><kbd>Ctrl/Cmd + W</kbd> - Collapse all endpoints</div>
                <div style="margin-bottom: 0.5rem;"><kbd>Ctrl/Cmd + /</kbd> - Show shortcuts</div>
                <div style="margin-bottom: 0.5rem;"><kbd>Escape</kbd> - Clear search / Close modals</div>
            </div>
            <button id="closeShortcuts" style="
                margin-top: 1rem;
                padding: 0.5rem 1rem;
                background: #3b82f6;
                color: white;
                border: none;
                border-radius: 4px;
                cursor: pointer;
            ">Close</button>
        `;

        modal.appendChild(content);
        document.body.appendChild(modal);

        // Close modal
        const closeHandler = () => modal.remove();
        content.querySelector('#closeShortcuts').addEventListener('click', closeHandler);
        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeHandler();
        });
        
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeHandler();
        }, { once: true });
    }

    function addCopyFunctionality() {
        setTimeout(() => {
            // Add copy buttons to code examples
            document.querySelectorAll('pre').forEach(pre => {
                if (!pre.querySelector('.copy-btn')) {
                    const copyBtn = document.createElement('button');
                    copyBtn.className = 'copy-btn';
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
                        transition: all 0.2s ease;
                    `;
                    
                    copyBtn.addEventListener('click', async () => {
                        try {
                            await navigator.clipboard.writeText(pre.textContent);
                            copyBtn.innerHTML = '✅';
                            copyBtn.style.background = '#d1fae5';
                            setTimeout(() => {
                                copyBtn.innerHTML = '📋';
                                copyBtn.style.background = 'rgba(0, 0, 0, 0.1)';
                            }, 2000);
                        } catch (err) {
                            console.error('Copy failed:', err);
                        }
                    });
                    
                    if (pre.style.position !== 'relative') {
                        pre.style.position = 'relative';
                    }
                    pre.appendChild(copyBtn);
                }
            });
        }, 2000);
    }

    function addEndpointFavorites() {
        const FAVORITES_KEY = 'swagger-favorites';
        let favorites = JSON.parse(localStorage.getItem(FAVORITES_KEY) || '[]');

        setTimeout(() => {
            document.querySelectorAll('.opblock-summary').forEach(summary => {
                const path = summary.querySelector('.opblock-summary-path')?.textContent.trim();
                const method = summary.parentElement.classList[1]?.replace('opblock-', '').toUpperCase();
                const endpointKey = `${method}:${path}`;

                if (!summary.querySelector('.favorite-btn')) {
                    const favoriteBtn = document.createElement('button');
                    favoriteBtn.className = 'favorite-btn';
                    favoriteBtn.innerHTML = favorites.includes(endpointKey) ? '⭐' : '☆';
                    favoriteBtn.style.cssText = `
                        background: none;
                        border: none;
                        font-size: 1rem;
                        cursor: pointer;
                        margin-left: 0.5rem;
                        padding: 0.25rem;
                        color: #f59e0b;
                    `;

                    favoriteBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        
                        if (favorites.includes(endpointKey)) {
                            favorites = favorites.filter(f => f !== endpointKey);
                            favoriteBtn.innerHTML = '☆';
                        } else {
                            favorites.push(endpointKey);
                            favoriteBtn.innerHTML = '⭐';
                        }
                        
                        localStorage.setItem(FAVORITES_KEY, JSON.stringify(favorites));
                    });

                    summary.appendChild(favoriteBtn);
                }
            });
        }, 1500);
    }

    function addThemeToggle() {
        const toggle = document.createElement('button');
        toggle.innerHTML = '🌓';
        toggle.style.cssText = `
            position: fixed;
            top: 1rem;
            right: 1rem;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 50%;
            width: 3rem;
            height: 3rem;
            cursor: pointer;
            font-size: 1.2rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            transition: all 0.2s ease;
        `;

        toggle.addEventListener('click', () => {
            document.body.classList.toggle('dark-theme');
            const isDark = document.body.classList.contains('dark-theme');
            localStorage.setItem('swagger-theme', isDark ? 'dark' : 'light');
        });

        document.body.appendChild(toggle);

        // Load saved theme
        const savedTheme = localStorage.getItem('swagger-theme');
        if (savedTheme === 'dark') {
            document.body.classList.add('dark-theme');
        }
    }

    function addHealthStatus() {
        // Add API health indicator
        const healthIndicator = document.createElement('div');
        healthIndicator.style.cssText = `
            position: fixed;
            bottom: 1rem;
            right: 1rem;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        `;

        // Check API health
        fetch('/api/health')
            .then(response => {
                if (response.ok) {
                    healthIndicator.innerHTML = '🟢 API Online';
                    healthIndicator.style.borderColor = '#10b981';
                } else {
                    healthIndicator.innerHTML = '🟡 API Issues';
                    healthIndicator.style.borderColor = '#f59e0b';
                }
            })
            .catch(() => {
                healthIndicator.innerHTML = '🔴 API Offline';
                healthIndicator.style.borderColor = '#ef4444';
            });

        document.body.appendChild(healthIndicator);
    }

    function initPeriodicEnhancements() {
        // Run enhancements periodically for dynamic content
        setInterval(() => {
            addCopyFunctionality();
            // Add other enhancements that need to be re-applied
        }, 5000);
    }

})();
