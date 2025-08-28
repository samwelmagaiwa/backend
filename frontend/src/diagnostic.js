/**
 * Diagnostic Script for Blank Page Issues
 * Run this in browser console to diagnose problems
 */

console.log('🔍 Starting diagnostic...')

// Check if Vue is loaded
console.log('Vue available:', typeof window.Vue !== 'undefined')

// Check if app is mounted
console.log('App element exists:', !!document.getElementById('app'))
console.log('App element content:', document.getElementById('app')?.innerHTML?.length || 0)

// Check for JavaScript errors
window.addEventListener('error', (e) => {
  console.error('❌ JavaScript Error:', e.error)
})

// Check for unhandled promise rejections
window.addEventListener('unhandledrejection', (e) => {
  console.error('❌ Unhandled Promise Rejection:', e.reason)
})

// Check CSS loading
const checkCSS = () => {
  const stylesheets = Array.from(document.styleSheets)
  console.log('Stylesheets loaded:', stylesheets.length)

  stylesheets.forEach((sheet, index) => {
    try {
      console.log(`Stylesheet ${index}:`, sheet.href || 'inline')
      console.log('  Rules:', sheet.cssRules?.length || 'N/A')
    } catch (e) {
      console.log(`Stylesheet ${index}: CORS blocked or error`)
    }
  })
}

// Check router
const checkRouter = () => {
  console.log('Current URL:', window.location.href)
  console.log('Current path:', window.location.pathname)
  console.log('Hash:', window.location.hash)
}

// Check localStorage
const checkStorage = () => {
  console.log('Auth token:', localStorage.getItem('auth_token') ? 'Present' : 'Missing')
  console.log('User data:', localStorage.getItem('user_data') ? 'Present' : 'Missing')
  console.log('Session data:', localStorage.getItem('session_data') ? 'Present' : 'Missing')
}

// Check network requests
const checkNetwork = () => {
  const observer = new PerformanceObserver((list) => {
    list.getEntries().forEach((entry) => {
      if (entry.name.includes('.js') || entry.name.includes('.css')) {
        console.log(`Resource: ${entry.name}`)
        console.log(`  Status: ${entry.responseStatus || 'Unknown'}`)
        console.log(`  Size: ${entry.transferSize || 'Unknown'} bytes`)
        console.log(`  Duration: ${entry.duration.toFixed(2)}ms`)
      }
    })
  })

  observer.observe({ entryTypes: ['resource'] })
}

// Run all checks
setTimeout(() => {
  console.log('🔍 Running diagnostic checks...')
  checkCSS()
  checkRouter()
  checkStorage()
  checkNetwork()

  // Check if main elements exist
  console.log('Body classes:', document.body.className)
  console.log('Head children:', document.head.children.length)
  console.log('Script tags:', document.querySelectorAll('script').length)
  console.log('Link tags:', document.querySelectorAll('link').length)

  // Check for common issues
  const appElement = document.getElementById('app')
  if (appElement) {
    console.log('App element found')
    console.log('App element classes:', appElement.className)
    console.log('App element children:', appElement.children.length)

    if (appElement.children.length === 0) {
      console.warn('⚠️ App element is empty - Vue may not have mounted')
    }
  } else {
    console.error('❌ App element not found')
  }

  console.log('🔍 Diagnostic complete')
}, 1000)

// Export for manual use
window.runDiagnostic = () => {
  checkCSS()
  checkRouter()
  checkStorage()
  checkNetwork()
}
