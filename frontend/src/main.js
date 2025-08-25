import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'
import './assets/main.css'
import './assets/css/responsive.css'
import { initializePerformanceOptimizations } from './utils/performance'
import { initializeImagePreloading } from './utils/imagePreloader'
import auth from './utils/auth'

const app = createApp(App)

// Use plugins
app.use(router)
app.use(store)

// Mount the app
app.mount('#app')

// Initialize performance optimizations after app mount
initializePerformanceOptimizations()

// Initialize smart image preloading based on user state
setTimeout(() => {
  const userRole = auth.userRole
  const currentRoute = router.currentRoute.value.path

  if (userRole) {
    console.log('🎯 Initializing role-based image preloading for:', userRole)
    initializeImagePreloading(userRole, currentRoute)
  } else {
    console.log(
      '⏳ User not authenticated yet, will preload images after login'
    )
  }
}, 500) // Small delay to ensure auth is initialized
