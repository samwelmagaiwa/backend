import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import pinia from './stores'
import statusUtilsPlugin from './plugins/statusUtils'
import { devLog } from './utils/devLogger'

// Reduce noisy console output in development without changing app behavior.
// To enable verbose logs temporarily in the browser console:
//   localStorage.setItem('LOG_LEVEL', 'debug')
// Levels: silent | warn | info | debug
if (process.env.NODE_ENV !== 'production') {
  try {
    const level = (localStorage.getItem('LOG_LEVEL') || 'warn').toLowerCase()
    const noop = () => {}
    const disableByLevel = {
      silent: ['log', 'info', 'debug', 'warn'],
      warn: ['log', 'info', 'debug'],
      info: ['log', 'debug'],
      debug: []
    }

    const methods = disableByLevel[level] || disableByLevel.warn
    methods.forEach((m) => {
      if (console && typeof console[m] === 'function') console[m] = noop
    })
  } catch (_) {
    // ignore
  }
}

// Import CSS files
import('./assets/main.css')
import('./assets/css/responsive.css')
import('./assets/css/z-index-fix.css')

devLog.log('🚀 Starting Vue application...')

// Create app instance
const app = createApp(App)

// (Removed auto hard-reload on chunk errors to keep clean URLs and avoid aborting requests)

// Add global error handler
app.config.errorHandler = (err, vm, info) => {
  devLog.error('❌ Vue Error:', err)
  devLog.error('❌ Component:', vm)
  devLog.error('❌ Info:', info)
}

// Use plugins
app.use(pinia)
app.use(statusUtilsPlugin)
app.use(router)

// Mount the app
app.mount('#app')

devLog.log('✅ Vue application mounted successfully')

// Initialize auth store after mounting
import('./stores/auth').then(({ useAuthStore }) => {
  const authStore = useAuthStore()
  authStore.initializeAuth()
  devLog.log('✅ Auth store initialized')
  try {
    window.dispatchEvent(new CustomEvent('auth-ready'))
  } catch (e) {
    devLog.warn('Auth ready event dispatch failed:', e)
  }
})

// Initialize sidebar store after a short delay
setTimeout(() => {
  import('./stores/sidebar')
    .then(({ useSidebarStore }) => {
      const sidebarStore = useSidebarStore()
      sidebarStore.initializeSidebar()
      devLog.log('✅ Sidebar store initialized')
    })
    .catch((error) => {
      devLog.warn('⚠️ Sidebar initialization failed:', error)
    })
}, 100)

// Add development debug functions
if (process.env.NODE_ENV !== 'production') {
  window.debugAuth = () => {
    devLog.log('Current auth state:', {
      authenticated: !!localStorage.getItem('auth_token'),
      userData: JSON.parse(localStorage.getItem('user_data') || 'null'),
      route: router.currentRoute.value
    })
  }
  devLog.log('🛠️ Debug function available: window.debugAuth()')
}
