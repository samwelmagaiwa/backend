console.log('🚀 Debug main.js starting...')

try {
  // Step 1: Test Vue import
  console.log('📦 Importing Vue...')
  const { createApp } = require('vue')
  console.log('✅ Vue imported successfully')

  // Step 2: Test basic app creation
  console.log('📦 Creating Vue app...')
  const app = createApp({
    template: '<div><h1>Vue Debug App</h1><p>If you see this, Vue is working!</p></div>'
  })
  console.log('✅ Vue app created successfully')

  // Step 3: Test app mount
  console.log('📦 Mounting Vue app...')
  app.mount('#app')
  console.log('✅ Vue app mounted successfully')
} catch (error) {
  console.error('❌ Vue initialization failed:', error)

  // Show error on page
  const appDiv = document.getElementById('app')
  if (appDiv) {
    appDiv.innerHTML = `
      <div style="padding: 20px; font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto;">
        <h2 style="color: #c62828;">❌ Vue Initialization Failed</h2>
        <div style="background: #ffebee; padding: 15px; border-radius: 8px; margin: 20px 0;">
          <strong>Error:</strong> ${error.message}<br>
          <strong>Stack:</strong><br>
          <pre style="white-space: pre-wrap; font-size: 12px;">${error.stack}</pre>
        </div>
        <p><strong>This helps us identify the exact problem with Vue loading in Chrome.</strong></p>
      </div>
    `
  }
}
