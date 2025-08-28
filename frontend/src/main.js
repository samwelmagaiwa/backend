import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'
import pinia from './stores' // Add Pinia

// Import CSS files with error handling
try {
  import('./assets/main.css')
  import('./assets/css/responsive.css')
  import('./assets/css/z-index-fix.css')
} catch (cssError) {
  console.warn('⚠️ CSS import failed:', cssError)
}

console.log('🚀 Starting Vue application...')

try {
  const app = createApp(App)

  // Use plugins
  app.use(router)
  app.use(store)
  app.use(pinia) // Add Pinia

  // Global error handler
  app.config.errorHandler = (err, vm, info) => {
    console.error('❌ Vue Error:', err)
    console.error('❌ Component:', vm)
    console.error('❌ Info:', info)
  }

  // Mount the app
  app.mount('#app')
  console.log('✅ Vue application mounted successfully')

  // Restore authentication session after app mount
  setTimeout(async() => {
    try {
      console.log('🔄 Restoring authentication session...')
      await store.dispatch('auth/restoreSession')
      console.log('✅ Authentication session restoration completed')
    } catch (sessionError) {
      console.warn('⚠️ Session restoration failed:', sessionError)
    }
  }, 50) // Small delay to ensure store is ready

  // Initialize sidebar state with Pinia
  setTimeout(async() => {
    try {
      console.log('🔄 Initializing sidebar state with Pinia...')
      const { useSidebarStore } = await import('./stores/sidebar')
      const sidebarStore = useSidebarStore()
      sidebarStore.initializeSidebar()
      console.log('✅ Pinia sidebar state initialization completed')
    } catch (sidebarError) {
      console.warn('⚠️ Pinia sidebar initialization failed:', sidebarError)
    }
  }, 100) // Small delay after auth restoration

  // Initialize performance optimizations after app mount
  setTimeout(async() => {
    try {
      const { initializePerformanceOptimizations } = await import('./utils/performance')
      initializePerformanceOptimizations()
      console.log('✅ Performance optimizations initialized')
    } catch (perfError) {
      console.warn('⚠️ Performance optimization failed:', perfError)
    }
  }, 100)

  // Initialize smart image preloading based on user state
  setTimeout(async() => {
    try {
      const { initializeImagePreloading } = await import('./utils/imagePreloader')
      const authModule = await import('./utils/auth')
      const auth = authModule.default

      const userRole = auth.userRole
      const currentRoute = router.currentRoute.value.path

      if (userRole) {
        console.log('🎯 Initializing role-based image preloading for:', userRole)
        initializeImagePreloading(userRole, currentRoute)
      } else {
        console.log('⏳ User not authenticated yet, will preload images after login')
      }
    } catch (preloadError) {
      console.warn('⚠️ Image preloading failed:', preloadError)
    }
  }, 500) // Small delay to ensure auth is initialized

} catch (error) {
  console.error('❌ Failed to start Vue application:', error)

  // Fallback: Show error message in DOM
  const errorDiv = document.createElement('div')
  errorDiv.innerHTML = `
    <div style="
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background: #fee;
      border: 2px solid #f00;
      padding: 20px;
      border-radius: 8px;
      font-family: Arial, sans-serif;
      max-width: 500px;
      text-align: center;
      z-index: 10000;
    ">
      <h2 style="color: #c00; margin-top: 0;">Application Error</h2>
      <p>The application failed to start. Please check the console for details.</p>
      <p style="font-size: 12px; color: #666;">Error: ${error.message}</p>
      <button onclick="window.location.reload()" style="
        background: #007cba;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 4px;
        cursor: pointer;
        margin-top: 10px;
      ">Reload Page</button>
    </div>
  `
  document.body.appendChild(errorDiv)
}
