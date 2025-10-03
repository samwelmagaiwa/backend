import { createApp } from 'vue'

console.log('🚀 Starting Vue application (step by step)...')

// Helper function to update the page with current status
const updateStatus = (step, message, isError = false) => {
  const appDiv = document.getElementById('app')
  const existingContent = appDiv.querySelector('.debug-content') || document.createElement('div')
  existingContent.className = 'debug-content'

  const statusColor = isError ? '#c62828' : '#2e7d32'
  const icon = isError ? '❌' : '✅'

  existingContent.innerHTML += `
    <div style="padding: 10px; margin: 5px 0; border-left: 4px solid ${statusColor}; background: ${isError ? '#ffebee' : '#e8f5e8'};">
      <strong>${icon} Step ${step}:</strong> ${message}
    </div>
  `

  if (!appDiv.contains(existingContent)) {
    appDiv.appendChild(existingContent)
  }

  console.log(`${icon} Step ${step}: ${message}`)
}

async function initializeApp() {
  try {
    updateStatus(1, 'Vue imported successfully')

    // Step 2: Import App component
    updateStatus(2, 'Importing App component...')
    const App = (await import('./App.vue')).default
    updateStatus(2, 'App component imported successfully')

    // Step 3: Create Vue app
    updateStatus(3, 'Creating Vue app...')
    const app = createApp(App)
    updateStatus(3, 'Vue app created successfully')

    // Step 4: Import and setup Pinia
    updateStatus(4, 'Importing Pinia...')
    const pinia = (await import('./stores')).default
    app.use(pinia)
    updateStatus(4, 'Pinia configured successfully')

    // Step 5: Import and setup router
    updateStatus(5, 'Importing Router...')
    const router = (await import('./router')).default
    app.use(router)
    updateStatus(5, 'Router configured successfully')

    // Step 6: Import and setup status utils plugin
    updateStatus(6, 'Importing Status Utils plugin...')
    const statusUtilsPlugin = (await import('./plugins/statusUtils')).default
    app.use(statusUtilsPlugin)
    updateStatus(6, 'Status Utils plugin configured successfully')

    // Step 7: Setup error handler
    updateStatus(7, 'Setting up error handler...')
    app.config.errorHandler = (err, _vm, _info) => {
      console.error('❌ Vue Error:', err)
      updateStatus('ERROR', `Vue runtime error: ${err.message}`, true)
    }
    updateStatus(7, 'Error handler configured successfully')

    // Step 8: Mount the app
    updateStatus(8, 'Mounting Vue app...')
    app.mount('#app')
    updateStatus(8, 'Vue app mounted successfully! 🎉')

    // Step 9: Initialize auth store
    updateStatus(9, 'Initializing auth store...')
    const { useAuthStore } = await import('./stores/auth')
    const authStore = useAuthStore()
    authStore.initializeAuth()
    updateStatus(9, 'Auth store initialized successfully')

    // Step 10: Initialize sidebar store
    setTimeout(async () => {
      updateStatus(10, 'Initializing sidebar store...')
      const { useSidebarStore } = await import('./stores/sidebar')
      const sidebarStore = useSidebarStore()
      sidebarStore.initializeSidebar()
      updateStatus(10, 'All initialization completed! ✨')
    }, 100)
  } catch (error) {
    updateStatus('ERROR', `Failed: ${error.message}`, true)
    console.error('❌ Initialization failed:', error)

    // Show detailed error
    const appDiv = document.getElementById('app')
    appDiv.innerHTML += `
      <div style="margin-top: 20px; padding: 15px; background: #ffebee; border-radius: 8px; font-family: monospace;">
        <strong>Detailed Error:</strong><br>
        <strong>Message:</strong> ${error.message}<br>
        <strong>Stack:</strong><br>
        <pre style="white-space: pre-wrap; font-size: 12px;">${error.stack}</pre>
      </div>
    `
  }
}

// Start the initialization
initializeApp()
