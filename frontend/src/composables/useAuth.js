import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { ROLES, getDefaultDashboard } from '../utils/permissions'

/**
 * Auth composable that provides authentication functionality
 * using Pinia auth store and Vue Router
 */
export function useAuth() {
  const router = useRouter()
  const authStore = useAuthStore()

  // Computed properties from Pinia store
  const user = computed(() => authStore.user)
  const token = computed(() => authStore.token)
  const isAuthenticated = computed(() => authStore.isAuthenticated)
  const userPermissions = computed(() => authStore.userPermissions)
  const userRole = computed(() => authStore.userRole)
  const userRoles = computed(() => authStore.userRoles)
  const isLoading = computed(() => authStore.isLoading)
  const error = computed(() => authStore.error)
  const isAdmin = computed(() => authStore.isAdmin)
  const isStaff = computed(() => authStore.isStaff)
  const isApprover = computed(() => authStore.isApprover)
  const userName = computed(() => user.value?.name || 'User')

  /**
   * Login user with credentials
   * @param {Object} credentials - { email, password }
   * @returns {Promise<Object>} - Login result
   */
  const login = async (credentials) => {
    try {
      const { authService } = await import('@/services/authService')
      const result = await authService.login(credentials)

      if (result.success) {
        const { user, token } = result.data

        // Set Pinia auth data
        authStore.setAuthData({ user, token })

        // Determine redirect path
        let redirectPath = getDefaultDashboard(authStore.userRole) || '/user-dashboard'
        if (user?.needs_onboarding && authStore.userRole !== 'admin') {
          redirectPath = '/onboarding'
        }
        const redirectTo = router.currentRoute.value.query.redirect
        if (redirectTo && typeof redirectTo === 'string') {
          redirectPath = redirectTo
        }

        await router.push(redirectPath)
        return { success: true, user }
      } else {
        // Handle failed login - set error message from authService response
        const errorMessage = result.message || 'Login failed'
        console.error('❌ Login failed:', errorMessage)
        authStore.setError(errorMessage)
        return {
          success: false,
          message: errorMessage
        }
      }
    } catch (error) {
      console.error('❌ Login error in composable:', error)
      authStore.setError(error.message || 'Login failed')
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
  const logout = async () => {
    try {
      const { authService } = await import('@/services/authService')
      await authService.logout()
      authStore.clearAuth()
      await router.push('/login')
      return { success: true }
    } catch (error) {
      authStore.clearAuth()
      await router.push('/login')
      return { success: true }
    }
  }

  /**
   * Logout from all sessions
   * @returns {Promise<Object>} - Logout all result
   */
  const logoutAll = async () => {
    try {
      const { authService } = await import('@/services/authService')
      const result = await authService.logoutAll()
      authStore.clearAuth()
      await router.push('/login')
      return result.success ? result : { success: true }
    } catch (error) {
      authStore.clearAuth()
      await router.push('/login')
      return { success: true }
    }
  }

  /**
   * Update user data
   * @param {Object} userData - Updated user data
   */
  const updateUser = (userData) => {
    authStore.updateUser(userData)
  }

  /**
   * Clear authentication error
   */
  const clearError = () => {
    authStore.clearError()
  }

  /**
   * Check if user has specific permission
   * @param {string} permission - Permission to check
   * @returns {boolean} - Whether user has permission
   */
  const hasPermission = (permission) => {
    return authStore.hasPermission(permission)
  }

  /**
   * Check if user has any of the specified roles
   * @param {string|Array} roles - Role(s) to check
   * @returns {boolean} - Whether user has any of the roles
   */
  const hasRole = (roles) => {
    return authStore.hasRole(roles)
  }

  /**
   * Check if user can access a specific route
   * @param {Object} route - Route object with meta.roles
   * @returns {boolean} - Whether user can access route
   */
  const canAccessRoute = (route) => {
    if (!route.meta?.roles) return true
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

  /**
   * Get display name for a role
   * @param {string} role - Role to get display name for
   * @returns {string} - Display name for the role
   */
  const getRoleDisplayName = (role) => {
    if (!role) return 'User'

    const roleNames = {
      [ROLES.ADMIN]: 'Administrator',
      [ROLES.DIVISIONAL_DIRECTOR]: 'Divisional Director',
      [ROLES.HEAD_OF_DEPARTMENT]: 'Head of Department',
      [ROLES.ICT_DIRECTOR]: 'ICT Director',
      [ROLES.HEAD_OF_IT]: 'Head of IT',
      [ROLES.STAFF]: 'D. IN MEDICINE',
      [ROLES.ICT_OFFICER]: 'ICT Officer',
      [ROLES.SECRETARY_ICT]: 'Secretary ICT'
    }
    return roleNames[role] || role
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
    getRoleDisplayName,

    // Constants
    ROLES
  }
}

export default useAuth
