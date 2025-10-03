import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import pinia from './stores'
import statusUtilsPlugin from './plugins/statusUtils'

// Import CSS files
import('./assets/main.css')
import('./assets/css/responsive.css')
import('./assets/css/z-index-fix.css')

console.log('🚀 Starting Vue application...')

// Create app instance
const app = createApp(App)

// Add global error handler
app.config.errorHandler = (err, vm, info) => {
  console.error('❌ Vue Error:', err)
  console.error('❌ Component:', vm)
  console.error('❌ Info:', info)
}

// Use plugins
app.use(pinia)
app.use(statusUtilsPlugin)
app.use(router)

// Mount the app
app.mount('#app')

console.log('✅ Vue application mounted successfully')

// Initialize auth store after mounting
import('./stores/auth').then(({ useAuthStore }) => {
  const authStore = useAuthStore()
  authStore.initializeAuth()
  console.log('✅ Auth store initialized')
})

// Initialize sidebar store after a short delay
setTimeout(() => {
  import('./stores/sidebar')
    .then(({ useSidebarStore }) => {
      const sidebarStore = useSidebarStore()
      sidebarStore.initializeSidebar()
      console.log('✅ Sidebar store initialized')
    })
    .catch((error) => {
      console.warn('⚠️ Sidebar initialization failed:', error)
    })
}, 100)

// Add development debug functions
if (process.env.NODE_ENV !== 'production') {
  window.debugAuth = () => {
    console.log('Current auth state:', {
      authenticated: !!localStorage.getItem('auth_token'),
      userData: JSON.parse(localStorage.getItem('user_data') || 'null'),
      route: router.currentRoute.value
    })
  }
  console.log('🛠️ Debug function available: window.debugAuth()')
}
