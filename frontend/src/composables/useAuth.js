import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth as useAuthState } from '../utils/auth'
import { 
  hasRouteAccess, 
  getAllowedRoutes,
  hasUserManagementAccess
} from '../utils/permissions'

/**
 * Authentication and authorization composable
 * Provides reactive access to user state and permission checking
 */
export function useAuth() {
  const router = useRouter()
  const authState = useAuthState()

  // Reactive user state from auth utility
  const {
    isAuthenticated,
    currentUser,
    userRole,
    userName,
    userEmail,
    isLoading,
    error,
    isAdmin,
    isStaff,
    isApprover,
    defaultDashboard,
    needsOnboarding,
    hasCompletedOnboarding,
    markOnboardingComplete,
    login: authLogin,
    logout: authLogout,
    clearError,
    ROLES
  } = authState

  // Permission checks
  const canAccessRoute = (routePath) => {
    return hasRouteAccess(userRole.value, routePath)
  }

  const canManageUsers = computed(() => {
    return hasUserManagementAccess(userRole.value)
  })

  const allowedRoutes = computed(() => {
    return getAllowedRoutes(userRole.value)
  })

  // Authentication actions
  const login = async (credentials) => {
    try {
      const result = await authLogin(credentials)
      if (result.success) {
        // Wait a bit for the auth state to update
        await new Promise(resolve => setTimeout(resolve, 100))
        
        try {
          // Check if user needs onboarding (non-admin users who haven't completed it)
          if (needsOnboarding.value) {
            // Redirect to onboarding flow
            await router.push('/onboarding')
          } else {
            // Redirect to intended route or default dashboard
            const redirect = router.currentRoute.value.query.redirect
            const targetRoute = redirect || defaultDashboard.value || '/user-dashboard'
            
            // Use Vue Router for proper SPA navigation
            await router.push(targetRoute)
          }
        } catch (navError) {
          console.warn('Router navigation failed:', navError)
          // Fallback to router push with default route
          try {
            await router.push('/user-dashboard')
          } catch (fallbackError) {
            console.error('Fallback navigation also failed:', fallbackError)
            // Last resort: use window.location but only if router completely fails
            window.location.href = '/user-dashboard'
          }
        }
      }
      return result
    } catch (error) {
      console.error('Login error:', error)
      return { success: false, error: error.message }
    }
  }

  const logout = async () => {
    try {
      await authLogout()
      await router.push('/')
      return { success: true }
    } catch (error) {
      console.error('Logout error:', error)
      return { success: false, error: error.message }
    }
  }

  // Route protection
  const requireAuth = () => {
    if (!isAuthenticated.value) {
      router.push({
        name: 'LoginPage',
        query: { redirect: router.currentRoute.value.fullPath }
      })
      return false
    }
    return true
  }

  const requireRole = (requiredRoles) => {
    if (!requireAuth()) return false

    const roles = Array.isArray(requiredRoles) ? requiredRoles : [requiredRoles]
    if (!roles.includes(userRole.value)) {
      // Redirect to default dashboard with error
      const dashboard = defaultDashboard.value
      if (dashboard) {
        router.push({
          path: dashboard,
          query: { error: 'access_denied' }
        })
      } else {
        router.push('/')
      }
      return false
    }
    return true
  }

  // Utility functions
  const getRoleDisplayName = (role = userRole.value) => {
    const roleNames = {
      [ROLES.ADMIN]: 'Administrator',
      [ROLES.DIVISIONAL_DIRECTOR]: 'Divisional Director',
      [ROLES.HEAD_OF_DEPARTMENT]: 'Head of Department',
      [ROLES.HOD_IT]: 'Head of IT Department',
      [ROLES.ICT_DIRECTOR]: 'ICT Director',
      [ROLES.STAFF]: 'Staff Member',
      [ROLES.ICT_OFFICER]: 'ICT Officer'
    }
    return roleNames[role] || role
  }

  const hasPermission = (permission) => {
    // Custom permission checking logic can be added here
    // For now, we'll use role-based checks
    switch (permission) {
      case 'manage_users':
        return canManageUsers.value
      case 'approve_requests':
        return isApprover.value
      case 'submit_requests':
        return isStaff.value
      case 'admin_access':
        return isAdmin.value
      default:
        return false
    }
  }

  // Error handling is handled by the auth utility

  return {
    // State
    isAuthenticated,
    currentUser,
    userRole,
    userName,
    userEmail,
    isLoading,
    error,

    // Computed permissions
    canManageUsers,
    isAdmin,
    isStaff,
    isApprover,
    allowedRoutes,
    defaultDashboard,
    needsOnboarding,
    hasCompletedOnboarding,
    markOnboardingComplete,

    // Methods
    login,
    logout,
    canAccessRoute,
    requireAuth,
    requireRole,
    getRoleDisplayName,
    hasPermission,
    clearError,

    // Constants
    ROLES
  }
}

/**
 * Route guard composable for components
 * Use this in components that need to check permissions
 */
export function useRouteGuard() {
  const { requireAuth, requireRole, canAccessRoute } = useAuth()

  const guardRoute = (requiredRoles = null) => {
    if (!requireAuth()) return false
    if (requiredRoles && !requireRole(requiredRoles)) return false
    return true
  }

  return {
    guardRoute,
    requireAuth,
    requireRole,
    canAccessRoute
  }
}

/**
 * Permission directive for conditional rendering
 * Use this to show/hide elements based on permissions
 */
export function usePermissions() {
  const { hasPermission, userRole, canAccessRoute } = useAuth()

  const canShow = (permission) => {
    if (typeof permission === 'string') {
      return hasPermission(permission)
    }
    if (typeof permission === 'object') {
      if (permission.roles) {
        return permission.roles.includes(userRole.value)
      }
      if (permission.route) {
        return canAccessRoute(permission.route)
      }
    }
    return false
  }

  return {
    canShow,
    hasPermission,
    canAccessRoute
  }
}