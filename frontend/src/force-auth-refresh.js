/**
 * Force refresh auth state to see updated role mapping
 * Run this in browser console after the auth fix
 */

console.log('🔄 Force refreshing auth state...')

// Import auth module and force refresh
import('./utils/auth.js').then(async(authModule) => {
  const auth = authModule.default

  console.log('📊 Current state before refresh:')
  console.log('  - isAuthenticated:', auth.isAuthenticated)
  console.log('  - userRole:', auth.userRole)
  console.log('  - userName:', auth.userName)

  // Force re-initialize auth
  console.log('🔄 Forcing auth re-initialization...')
  await auth.initializeAuth(true)

  console.log('📊 State after refresh:')
  console.log('  - isAuthenticated:', auth.isAuthenticated)
  console.log('  - userRole:', auth.userRole)
  console.log('  - userName:', auth.userName)
  console.log('  - currentUser:', auth.currentUser)

  // Also trigger background verification
  console.log('🔄 Triggering background verification...')
  const token = localStorage.getItem('auth_token')
  const userData = localStorage.getItem('user_data')

  if (token && userData) {
    const user = JSON.parse(userData)
    await auth.verifyTokenInBackground(token, user)

    console.log('📊 State after background verification:')
    console.log('  - isAuthenticated:', auth.isAuthenticated)
    console.log('  - userRole:', auth.userRole)
    console.log('  - userName:', auth.userName)
    console.log('  - currentUser:', auth.currentUser)
  }
})

// Also export for manual use
window.forceAuthRefresh = async() => {
  const authModule = await import('./utils/auth.js')
  const auth = authModule.default
  await auth.initializeAuth(true)
  console.log('Auth refreshed. New role:', auth.userRole)
}
