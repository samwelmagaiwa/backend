/**
 * Enhanced Route Guards
 * Provides robust authentication and role-based access control
 */

import { useAuthStore } from '@/stores/auth'
import { useNotificationStore } from '@/stores/notification'
import bookingService from '@/services/bookingService'

/**
 * Check if user has a pending booking request that should lock the booking service page
 * @param {String} userRole - User role
 * @returns {Promise<Object>} - { hasPendingRequest: boolean, pendingRequestInfo?: Object }
 */
export async function checkPendingBookingRequest(userRole) {
  try {
    // Only check for staff users
    if (userRole !== 'staff') {
      return { hasPendingRequest: false }
    }

    const response = await bookingService.checkPendingRequests()

    if (response.success && response.data) {
      return {
        hasPendingRequest: response.data.has_pending_request,
        pendingRequestInfo: response.data.pending_request || null,
        message: response.data.message || null
      }
    }

    return { hasPendingRequest: false }
  } catch (error) {
    console.error('❌ Route Guard: Error checking pending booking request:', error)
    // On error, don't block access
    return { hasPendingRequest: false }
  }
}

/**
 * Check if user has required role for route access
 * @param {Object} route - Vue route object
 * @returns {Promise<Object>} - { hasAccess: boolean, redirectTo?: string, reason?: string }
 */
export async function checkRouteAccess(route) {
  try {
    // Get auth state from Pinia store
    const piniaAuthStore = useAuthStore()

    // Ensure Pinia auth is initialized
    if (!piniaAuthStore.isInitialized) {
      piniaAuthStore.initializeAuth()
    }

    const isAuthenticated = piniaAuthStore.isAuthenticated
    const user = piniaAuthStore.user
    const userRole = piniaAuthStore.userRole
    const userRoles = user && Array.isArray(user.roles) ? user.roles : []

    console.log('🔍 Route Guard: Checking access for', route.path, {
      final: { auth: isAuthenticated, role: userRole, roles: userRoles }
    })

    // Check if route requires authentication
    if (route.meta?.requiresAuth !== false) {
      if (!isAuthenticated) {
        return {
          hasAccess: false,
          redirectTo: '/login',
          reason: 'Not authenticated'
        }
      }

      // Check if user needs onboarding (skip for admin users completely)
      if (
        user?.needs_onboarding &&
        userRole !== 'admin' &&
        userRole !== 'ADMIN' &&
        route.name !== 'Onboarding'
      ) {
        console.log('🔄 Route Guard: User needs onboarding, redirecting...', {
          userRole,
          needs_onboarding: user.needs_onboarding,
          targetRoute: route.path,
          routeName: route.name,
          userId: user.id,
          userName: user.name
        })
        return {
          hasAccess: false,
          redirectTo: '/onboarding',
          reason: 'Onboarding required'
        }
      }

      // Debug log when onboarding check passes
      if (route.name !== 'Onboarding') {
        console.log('✅ Route Guard: Onboarding check passed', {
          userRole,
          needs_onboarding: user?.needs_onboarding,
          targetRoute: route.path,
          routeName: route.name,
          isAdmin: userRole === 'admin' || userRole === 'ADMIN'
        })
      }

      // Additional check: if admin user is trying to access onboarding, redirect to dashboard
      if ((userRole === 'admin' || userRole === 'ADMIN') && route.name === 'Onboarding') {
        console.log(
          '🚫 Route Guard: Admin user trying to access onboarding, redirecting to dashboard'
        )
        return {
          hasAccess: false,
          redirectTo: '/admin-dashboard',
          reason: 'Admin users do not need onboarding'
        }
      }
    }

    // Check role-based access
    if (route.meta?.roles && route.meta.roles.length > 0) {
      if (!userRole && (!userRoles || userRoles.length === 0)) {
        return {
          hasAccess: false,
          redirectTo: '/login',
          reason: 'No user role found'
        }
      }

      // Check if user has required role (check both primary role and roles array)
      const hasRequiredRole =
        route.meta.roles.includes(userRole) ||
        userRoles.some((role) => route.meta.roles.includes(role))

      if (!hasRequiredRole) {
        console.log('🚫 Route Guard: Role mismatch:', {
          userRole,
          userRoles,
          requiredRoles: route.meta.roles,
          routePath: route.path
        })

        // Get default dashboard for user's role (use first role from array if primary role is missing)
        const effectiveRole = userRole || (userRoles.length > 0 ? userRoles[0] : null)
        const { getDefaultDashboard } = await import('./permissions')
        const defaultDashboard = getDefaultDashboard(effectiveRole)

        return {
          hasAccess: false,
          redirectTo: defaultDashboard || '/login',
          reason: `Role ${effectiveRole} not authorized for this route. Required roles: ${route.meta.roles.join(
            ', '
          )}`
        }
      }
    }

    // Special case: If no specific route meta but user is authenticated, allow access to dashboard routes
    if (!route.meta?.roles && isAuthenticated) {
      const { isDashboardRoute, getDefaultDashboard } = await import('./permissions')

      if (isDashboardRoute(route.path)) {
        const userDefaultDashboard = getDefaultDashboard(userRole)

        // Allow access if this is the user's default dashboard
        if (route.path === userDefaultDashboard) {
          console.log('✅ Route Guard: Allowing access to user default dashboard:', route.path)
          return {
            hasAccess: true,
            reason: 'User accessing their default dashboard'
          }
        }

        // Redirect to correct dashboard if accessing wrong one
        console.log('🔄 Route Guard: Redirecting to correct dashboard:', {
          requestedDashboard: route.path,
          userDefaultDashboard,
          userRole
        })

        return {
          hasAccess: false,
          redirectTo: userDefaultDashboard || '/login',
          reason: `Redirecting to correct dashboard for role ${userRole}`
        }
      }
    }

    // Special check for booking service page - prevent access if user has pending request
    if (route.path === '/booking-service' && userRole === 'staff') {
      const pendingCheck = await checkPendingBookingRequest(userRole)

      if (pendingCheck.hasPendingRequest) {
        console.log(
          '🚫 Route Guard: Blocking booking service access - user has pending request:',
          pendingCheck.pendingRequestInfo
        )

        // Store pending booking info and show notification
        try {
          const notificationStore = useNotificationStore()
          notificationStore.setPendingBookingInfo(pendingCheck.pendingRequestInfo)
          notificationStore.showBookingLockNotification(pendingCheck.pendingRequestInfo)
        } catch (error) {
          console.warn('Failed to show booking lock notification:', error)
        }

        return {
          hasAccess: false,
          redirectTo: '/request-status',
          reason:
            'User has a pending booking request. Access to booking service is locked until request is processed.',
          pendingRequestInfo: pendingCheck.pendingRequestInfo
        }
      }
    }

    return {
      hasAccess: true,
      reason: 'Access granted'
    }
  } catch (error) {
    console.error('❌ Route Guard: Error checking access:', error)
    return {
      hasAccess: false,
      redirectTo: '/login',
      reason: 'Route guard error'
    }
  }
}

/**
 * Enhanced navigation guard with dual auth store support
 * @param {Object} to - Target route
 * @param {Object} from - Source route
 * @param {Function} next - Navigation callback
 * @param {Object} vuexStore - Vuex store instance
 */
export async function enhancedNavigationGuard(to, from, next) {
  console.log('🔄 Enhanced Route Guard: Navigating from', from.path, 'to', to.path)

  try {
    // Wait for auth system to be fully initialized
    let maxWaitTime = 5000 // 5 seconds max wait
    let waitTime = 0
    const checkInterval = 50

    const piniaAuthStore = useAuthStore()
    while (!piniaAuthStore.isInitialized && waitTime < maxWaitTime) {
      console.log('⏳ Enhanced Route Guard: Waiting for auth initialization...', {
        authInitialized: piniaAuthStore.isInitialized,
        waitTime
      })

      if (!piniaAuthStore.isInitialized) {
        console.log('⏳ Enhanced Route Guard: Initializing auth...')
        piniaAuthStore.initializeAuth()
      }

      await new Promise((resolve) => setTimeout(resolve, checkInterval))
      waitTime += checkInterval
    }

    if (waitTime >= maxWaitTime) {
      console.warn('⚠️ Enhanced Route Guard: Auth initialization timeout, proceeding anyway')
    }

    // Ensure Pinia auth is initialized
    if (!piniaAuthStore.isInitialized) {
      console.log('⏳ Enhanced Route Guard: Initializing Pinia auth...')
      piniaAuthStore.initializeAuth()
    }

    console.log('🔍 Enhanced Route Guard: Final auth state:', {
      isAuthenticated: piniaAuthStore.isAuthenticated,
      userRole: piniaAuthStore.userRole,
      userName: piniaAuthStore.user?.name,
      targetRoute: to.path,
      authReady: piniaAuthStore.isInitialized
    })

    // Handle redirect query parameter first
    if (to.query.redirect && piniaAuthStore.isAuthenticated && piniaAuthStore.userRole) {
      const redirectPath = to.query.redirect
      console.log('🔄 Enhanced Route Guard: Processing redirect parameter:', {
        redirectPath,
        currentPath: to.path,
        userRole: piniaAuthStore.userRole
      })

      // Check if user has access to the redirect path
      const redirectRoute = { path: redirectPath, meta: {} }
      const redirectAccessCheck = await checkRouteAccess(redirectRoute)

      if (redirectAccessCheck.hasAccess) {
        console.log('✅ Enhanced Route Guard: Redirecting to intended path:', redirectPath)
        return next({ path: redirectPath, replace: true })
      } else {
        console.log(
          '🚫 Enhanced Route Guard: User cannot access redirect path, going to default dashboard'
        )
        const { getDefaultDashboard } = await import('./permissions')
        const defaultDashboard = getDefaultDashboard(piniaAuthStore.userRole)
        return next({ path: defaultDashboard || '/login', replace: true })
      }
    }

    // Special handling for role-based dashboard redirects on page refresh
    if (
      piniaAuthStore.isAuthenticated &&
      piniaAuthStore.userRole &&
      (to.path === '/' || to.path === '/login')
    ) {
      const { getDefaultDashboard } = await import('./permissions')
      const defaultDashboard = getDefaultDashboard(piniaAuthStore.userRole)

      if (defaultDashboard && defaultDashboard !== to.path) {
        console.log(
          '🔄 Enhanced Route Guard: Redirecting authenticated user to role-based dashboard:',
          {
            role: piniaAuthStore.userRole,
            dashboard: defaultDashboard
          }
        )
        return next({ path: defaultDashboard, replace: true })
      }
    }

    // Check route access
    const accessCheck = await checkRouteAccess(to)

    if (!accessCheck.hasAccess) {
      console.log('🚫 Enhanced Route Guard: Access denied -', accessCheck.reason)

      if (accessCheck.redirectTo) {
        // Don't add redirect query if user is being redirected to their default dashboard
        const { getDefaultDashboard } = await import('./permissions')
        const userDefaultDashboard = getDefaultDashboard(piniaAuthStore.userRole)

        const shouldAddRedirect =
          accessCheck.redirectTo !== userDefaultDashboard &&
          to.path !== accessCheck.redirectTo &&
          !to.path.startsWith('/login') &&
          !to.path.startsWith('/onboarding')

        console.log('🔄 Enhanced Route Guard: Redirecting with access check:', {
          redirectTo: accessCheck.redirectTo,
          userDefaultDashboard,
          shouldAddRedirect,
          originalPath: to.path
        })

        return next({
          path: accessCheck.redirectTo,
          query: shouldAddRedirect ? { redirect: to.fullPath } : undefined,
          replace: true // Use replace to avoid adding to history
        })
      } else {
        return next(false)
      }
    }

    console.log('✅ Enhanced Route Guard: Access granted -', accessCheck.reason)
    next()
  } catch (error) {
    console.error('❌ Enhanced Route Guard: Navigation error:', error)
    next('/login')
  }
}

export default {
  checkRouteAccess,
  enhancedNavigationGuard
}
