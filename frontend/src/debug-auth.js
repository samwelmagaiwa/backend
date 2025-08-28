/**
 * Debug script to check current auth state
 * Run this in browser console to see what's stored
 */

console.log('🔍 === AUTH DEBUG INFORMATION ===')

// Check localStorage
const token = localStorage.getItem('auth_token')
const userData = localStorage.getItem('user_data')
const sessionData = localStorage.getItem('session_data')

console.log('📦 localStorage data:')
console.log('  - auth_token:', token ? 'Present (' + token.substring(0, 20) + '...)' : 'Missing')
console.log('  - user_data:', userData ? 'Present' : 'Missing')
console.log('  - session_data:', sessionData ? 'Present' : 'Missing')

if (userData) {
  try {
    const user = JSON.parse(userData)
    console.log('👤 Parsed user data:')
    console.log('  - name:', user.name)
    console.log('  - role:', user.role)
    console.log('  - role_name:', user.role_name)
    console.log('  - roles:', user.roles)
    console.log('  - permissions:', user.permissions)
    console.log('  - needs_onboarding:', user.needs_onboarding)
  } catch (e) {
    console.error('❌ Failed to parse user data:', e)
  }
}

// Check current auth state
import('./utils/auth.js').then(authModule => {
  const auth = authModule.default
  console.log('🔐 Current auth state:')
  console.log('  - isAuthenticated:', auth.isAuthenticated)
  console.log('  - userRole:', auth.userRole)
  console.log('  - userName:', auth.userName)
  console.log('  - currentUser:', auth.currentUser)
})

// Check role constants
import('./utils/permissions.js').then(permModule => {
  const { ROLES } = permModule
  console.log('🎭 Available roles:')
  console.log(ROLES)
})

console.log('🔍 === END AUTH DEBUG ===')

// Export for manual use
window.debugAuth = () => {
  console.log('Current auth state:', {
    token: localStorage.getItem('auth_token') ? 'Present' : 'Missing',
    userData: localStorage.getItem('user_data'),
    isAuthenticated: window.auth?.isAuthenticated,
    userRole: window.auth?.userRole
  })
}
