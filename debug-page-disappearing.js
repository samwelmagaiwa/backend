/**
 * Debug script to help identify why /both-service-form/15 page disappears for ICT Officer
 * Run this in the browser console when the issue occurs
 */

console.log('🔍 Debug: Starting page disappearing investigation...');

// 1. Check if there are any Vue errors
window.addEventListener('error', (e) => {
  console.error('🚨 Global Error:', e.error);
  console.error('🚨 Error Message:', e.message);
  console.error('🚨 Error Source:', e.filename, e.lineno, e.colno);
});

window.addEventListener('unhandledrejection', (e) => {
  console.error('🚨 Unhandled Promise Rejection:', e.reason);
});

// 2. Monitor Vue component lifecycle
function debugVueComponent() {
  console.log('🔍 Checking Vue component state...');
  
  const app = document.querySelector('#app');
  if (!app) {
    console.error('❌ Vue app element not found');
    return;
  }
  
  const vueInstance = app.__vue_app__;
  if (!vueInstance) {
    console.error('❌ Vue instance not found');
    return;
  }
  
  console.log('✅ Vue app instance exists');
}

// 3. Check router state
function debugRouter() {
  console.log('🔍 Checking router state...');
  
  if (window.$router) {
    console.log('✅ Router exists');
    console.log('📍 Current route:', window.$router.currentRoute.value);
  } else {
    console.error('❌ Router not accessible globally');
  }
}

// 4. Monitor auth store
function debugAuthStore() {
  console.log('🔍 Checking auth store...');
  
  // Try to access Pinia auth store
  try {
    const { useAuthStore } = require('./stores/auth');
    const authStore = useAuthStore();
    console.log('👤 Auth Store State:', {
      isAuthenticated: authStore.isAuthenticated,
      user: authStore.user,
      userRole: authStore.userRole,
      isInitialized: authStore.isInitialized
    });
  } catch (error) {
    console.error('❌ Cannot access auth store:', error);
  }
}

// 5. Check for navigation guards
function debugNavigationGuards() {
  console.log('🔍 Checking navigation guards...');
  
  // Monitor route changes
  if (window.$router) {
    window.$router.beforeEach((to, from, next) => {
      console.log('🛡️ Navigation Guard:', {
        from: from.path,
        to: to.path,
        timestamp: new Date().toISOString()
      });
      
      // Don't interfere with navigation, just log
      next();
    });
  }
}

// 6. Monitor for forced redirects or reloads
function debugForceRedirects() {
  console.log('🔍 Monitoring for forced redirects/reloads...');
  
  // Override window.location methods to track calls
  const originalReload = window.location.reload;
  const originalAssign = window.location.assign;
  const originalReplace = window.location.replace;
  
  window.location.reload = function() {
    console.error('🚨 FORCED RELOAD DETECTED!');
    console.trace('Reload called from:');
    return originalReload.apply(this, arguments);
  };
  
  window.location.assign = function() {
    console.error('🚨 LOCATION ASSIGN DETECTED!', arguments);
    console.trace('Assign called from:');
    return originalAssign.apply(this, arguments);
  };
  
  window.location.replace = function() {
    console.error('🚨 LOCATION REPLACE DETECTED!', arguments);
    console.trace('Replace called from:');
    return originalReplace.apply(this, arguments);
  };
  
  // Override router push/replace
  if (window.$router) {
    const originalPush = window.$router.push;
    const originalRouterReplace = window.$router.replace;
    
    window.$router.push = function() {
      console.log('📍 Router Push:', arguments);
      console.trace('Router push called from:');
      return originalPush.apply(this, arguments);
    };
    
    window.$router.replace = function() {
      console.log('📍 Router Replace:', arguments);
      console.trace('Router replace called from:');
      return originalRouterReplace.apply(this, arguments);
    };
  }
}

// 7. Check for API errors that might cause redirects
function debugAPIErrors() {
  console.log('🔍 Monitoring API errors...');
  
  // Monitor fetch requests
  const originalFetch = window.fetch;
  window.fetch = async function() {
    const response = await originalFetch.apply(this, arguments);
    
    if (!response.ok) {
      console.error('🌐 API Error:', {
        url: arguments[0],
        status: response.status,
        statusText: response.statusText
      });
    }
    
    return response;
  };
}

// Run all debug functions
export function runPageDisappearingDebug() {
  console.log('🔍 Starting comprehensive debug...');
  
  debugVueComponent();
  debugRouter();
  debugAuthStore();
  debugNavigationGuards();
  debugForceRedirects();
  debugAPIErrors();
  
  console.log('✅ Debug monitoring enabled. Navigate to /both-service-form/15 as ICT Officer now.');
}

// Make it available globally
window.runPageDisappearingDebug = runPageDisappearingDebug;

console.log('🛠️ Debug script loaded. Run window.runPageDisappearingDebug() to start monitoring.');
