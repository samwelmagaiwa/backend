import { computed } from 'vue'
import { useStore } from 'vuex'
import { useRouter } from 'vue-router'
import { ROLES } from '../utils/permissions'

/**
 * Auth composable that provides authentication functionality
 * using Vuex store and Vue Router
 */
export function useAuth() {
  const store = useStore()
  const router = useRouter()

  // Computed properties from store
  const user = computed(() => store.getters['auth/user'])
  const token = computed(() => store.getters['auth/token'])
  const isAuthenticated = computed(() => store.getters['auth/isAuthenticated'])
  const userPermissions = computed(() => store.getters['auth/userPermissions'])
  const userRole = computed(() => store.getters['auth/userRole'])
  const userRoles = computed(() => store.getters['auth/userRoles'])
  const isLoading = computed(() => store.getters['auth/loading'])
  const error = computed(() => store.getters['auth/error'])
  const isAdmin = computed(() => store.getters['auth/isAdmin'])
  const isStaff = computed(() => store.getters['auth/isStaff'])
  const isApprover = computed(() => store.getters['auth/isApprover'])
  const userName = computed(() => user.value?.name || 'User')

  /**
   * Login user with credentials
   * @param {Object} credentials - { email, password }
   * @returns {Promise<Object>} - Login result
   */
  const login = async(credentials) => {
    try {
      const result = await store.dispatch('auth/login', credentials)

      if (result.success) {
        console.log('✅ Login successful, navigating to dashboard...')

        // Navigate to appropriate dashboard based on user role
        const userRole = result.user?.role
        const userRoles = result.user?.roles || []

        // Determine redirect path based on role
        let redirectPath = '/dashboard'

        if (userRole === 'admin' || userRoles.includes('admin')) {
          redirectPath = '/admin/dashboard'
        } else if (userRole === 'ict_officer' || userRoles.includes('ict_officer')) {
          redirectPath = '/ict-approval'
        } else if (userRole === 'head_of_department' || userRoles.includes('head_of_department')) {
          redirectPath = '/hod-approval'
        } else if (userRole === 'divisional_director' || userRoles.includes('divisional_director')) {
          redirectPath = '/director-approval'
        } else {
          redirectPath = '/dashboard'
        }

        // Check if there's a redirect query parameter
        const redirectTo = router.currentRoute.value.query.redirect
        if (redirectTo && typeof redirectTo === 'string') {
          redirectPath = redirectTo
        }

        // Navigate to the determined path
        await router.push(redirectPath)

        return result
      }

      return result
    } catch (error) {
      console.error('❌ Login error in composable:', error)
      return {
        success: false,
        message: error.message || 'Login failed'
      }
    }
  }

  /**
   * Logout current user
   * @returns {Promise<Object>} - Logout result
   */
  const logout = async() => {
    try {
      const result = await store.dispatch('auth/logout')

      if (result.success) {
        console.log('✅ Logout successful, redirecting to login...')
        await router.push('/login')
      }

      return result
    } catch (error) {
      console.error('❌ Logout error in composable:', error)
      // Even if logout fails, clear local state and redirect
      await store.dispatch('auth/logout')
      await router.push('/login')
      return {
        success: true,
        message: 'Logged out locally'
      }
    }
  }

  /**
   * Logout from all sessions
   * @returns {Promise<Object>} - Logout all result
   */
  const logoutAll = async() => {
    try {
      const result = await store.dispatch('auth/logoutAll')

      if (result.success) {
        console.log('✅ Logout all successful, redirecting to login...')
        await router.push('/login')
      }

      return result
    } catch (error) {
      console.error('❌ Logout all error in composable:', error)
      // Even if logout fails, clear local state and redirect
      await store.dispatch('auth/logout')
      await router.push('/login')
      return {
        success: true,
        message: 'Logged out locally'
      }
    }
  }

  /**
   * Update user data
   * @param {Object} userData - Updated user data
   */
  const updateUser = (userData) => {
    store.dispatch('auth/updateUser', userData)
  }

  /**
   * Clear authentication error
   */
  const clearError = () => {
    store.dispatch('auth/clearError')
  }

  /**
   * Check if user has specific permission
   * @param {string} permission - Permission to check
   * @returns {boolean} - Whether user has permission
   */
  const hasPermission = (permission) => {
    return userPermissions.value.includes(permission)
  }

  /**
   * Check if user has any of the specified roles
   * @param {string|Array} roles - Role(s) to check
   * @returns {boolean} - Whether user has any of the roles
   */
  const hasRole = (roles) => {
    const rolesToCheck = Array.isArray(roles) ? roles : [roles]
    const currentRole = userRole.value
    const currentRoles = userRoles.value

    return rolesToCheck.some(role =>
      currentRole === role || currentRoles.includes(role)
    )
  }

  /**
   * Check if user can access a specific route
   * @param {Object} route - Route object with meta.roles
   * @returns {boolean} - Whether user can access route
   */
  const canAccessRoute = (route) => {
    if (!route.meta?.roles) {
      return true // No role restriction
    }

    return hasRole(route.meta.roles)
  }

  /**
   * Require specific role(s) - throws error if user doesn't have required role
   * @param {string|Array} requiredRoles - Required role(s)
   * @throws {Error} - If user doesn't have required role
   */
  const requireRole = (requiredRoles) => {
    if (!hasRole(requiredRoles)) {
      const roleList = Array.isArray(requiredRoles) ? requiredRoles.join(', ') : requiredRoles
      throw new Error(`Access denied. Required role(s): ${roleList}`)
    }
  }

  return {
    // State
    user,
    userName,
    token,
    isAuthenticated,
    userPermissions,
    userRole,
    userRoles,
    isLoading,
    error,
    isAdmin,
    isStaff,
    isApprover,

    // Actions
    login,
    logout,
    logoutAll,
    updateUser,
    clearError,

    // Utilities
    hasPermission,
    hasRole,
    canAccessRoute,
    requireRole,

    // Constants
    ROLES
  }
}

export default useAuth
