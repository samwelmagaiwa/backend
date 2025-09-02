/**
 * Enhanced Route Guards
 * Provides robust authentication and role-based access control
 */

import { useAuthStore } from '@/stores/auth'

/**
 * Check if user has required role for route access
 * @param {Object} route - Vue route object
 * @param {Object} vuexStore - Vuex store instance
 * @returns {Promise<Object>} - { hasAccess: boolean, redirectTo?: string, reason?: string }
 */
export async function checkRouteAccess(route, vuexStore) {
  try {
    // Get auth state from both stores
    const piniaAuthStore = useAuthStore()

    // Ensure Pinia auth is initialized
    if (!piniaAuthStore.isInitialized) {
      piniaAuthStore.initializeAuth()
    }

    // Get authentication status from both sources
    const vuexAuth = vuexStore.getters['auth/isAuthenticated']
    const vuexUser = vuexStore.getters['auth/user']
    const vuexRole = vuexStore.getters['auth/userRole']

    const piniaAuth = piniaAuthStore.isAuthenticated
    const piniaUser = piniaAuthStore.user
    const piniaRole = piniaAuthStore.userRole

    // Use the most reliable source - prioritize data that has roles array
    const isAuthenticated = piniaAuth || vuexAuth
    const user = piniaUser || vuexUser

    // Prioritize role from source that has roles array populated
    let userRole = null
    if (piniaUser && piniaUser.roles && piniaUser.roles.length > 0) {
      userRole = piniaUser.role || piniaUser.role_name || piniaUser.roles[0]
    } else if (vuexUser && vuexUser.roles && vuexUser.roles.length > 0) {
      userRole = vuexUser.role || vuexUser.role_name || vuexUser.roles[0]
    } else {
      userRole = piniaRole || vuexRole
    }

    // Get roles array from the most reliable source
    const userRoles = (piniaUser && piniaUser.roles && piniaUser.roles.length > 0)
      ? piniaUser.roles
      : (vuexUser && vuexUser.roles && vuexUser.roles.length > 0)
        ? vuexUser.roles
        : []

    console.log('🔍 Route Guard: Checking access for', route.path, {
      vuex: { auth: vuexAuth, role: vuexRole, roles: vuexUser?.roles },
      pinia: { auth: piniaAuth, role: piniaRole, roles: piniaUser?.roles },
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
      if (user?.needs_onboarding && userRole !== 'admin' && userRole !== 'ADMIN' && route.name !== 'Onboarding') {
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
        console.log('🚫 Route Guard: Admin user trying to access onboarding, redirecting to dashboard')
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
      const hasRequiredRole = route.meta.roles.includes(userRole) ||
                             userRoles.some(role => route.meta.roles.includes(role))

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
          reason: `Role ${effectiveRole} not authorized for this route. Required roles: ${route.meta.roles.join(', ')}`
        }
      }
    }

    // Special case: If no specific route meta but user is authenticated, allow access to dashboard routes
    if (!route.meta?.roles && isAuthenticated) {
      const dashboardRoutes = ['/admin-dashboard', '/user-dashboard', '/hod-dashboard', '/dict-dashboard', '/divisional-dashboard', '/ict-dashboard']

      if (dashboardRoutes.includes(route.path)) {
        const { getDefaultDashboard } = await import('./permissions')
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
export async function enhancedNavigationGuard(to, from, next, vuexStore) {
  console.log('🔄 Enhanced Route Guard: Navigating from', from.path, 'to', to.path)

  try {
    // Wait for auth system to be fully initialized
    let maxWaitTime = 5000 // 5 seconds max wait
    let waitTime = 0
    const checkInterval = 50

    while (!vuexStore.getters['auth/isAuthReady'] && waitTime < maxWaitTime) {
      console.log('⏳ Enhanced Route Guard: Waiting for auth initialization...', {
        authInitialized: vuexStore.getters['auth/authInitialized'],
        restoringSession: vuexStore.getters['auth/restoringSession'],
        sessionRestored: vuexStore.getters['auth/sessionRestored'],
        waitTime
      })

      // If session restoration hasn't started, trigger it
      if (!vuexStore.getters['auth/sessionRestored'] && !vuexStore.getters['auth/restoringSession']) {
        console.log('⏳ Enhanced Route Guard: Triggering session restoration...')
        vuexStore.dispatch('auth/restoreSession')
      }

      await new Promise(resolve => setTimeout(resolve, checkInterval))
      waitTime += checkInterval
    }

    if (waitTime >= maxWaitTime) {
      console.warn('⚠️ Enhanced Route Guard: Auth initialization timeout, proceeding anyway')
    }

    // Ensure Pinia auth is also initialized and synced
    const piniaAuthStore = useAuthStore()
    if (!piniaAuthStore.isInitialized) {
      console.log('⏳ Enhanced Route Guard: Initializing Pinia auth...')
      piniaAuthStore.initializeAuth()
      await piniaAuthStore.syncWithVuex()
    }

    // Get final authentication state
    const vuexAuth = vuexStore.getters['auth/isAuthenticated']
    const vuexUser = vuexStore.getters['auth/user']
    const vuexRole = vuexStore.getters['auth/userRole']

    console.log('🔍 Enhanced Route Guard: Final auth state:', {
      vuexAuth,
      vuexRole,
      userName: vuexUser?.name,
      targetRoute: to.path,
      authReady: vuexStore.getters['auth/isAuthReady']
    })

    // Handle redirect query parameter first
    if (to.query.redirect && vuexAuth && vuexRole) {
      const redirectPath = to.query.redirect
      console.log('🔄 Enhanced Route Guard: Processing redirect parameter:', {
        redirectPath,
        currentPath: to.path,
        userRole: vuexRole
      })

      // Check if user has access to the redirect path
      const redirectRoute = { path: redirectPath, meta: {} }
      const redirectAccessCheck = await checkRouteAccess(redirectRoute, vuexStore)

      if (redirectAccessCheck.hasAccess) {
        console.log('✅ Enhanced Route Guard: Redirecting to intended path:', redirectPath)
        return next({ path: redirectPath, replace: true })
      } else {
        console.log('🚫 Enhanced Route Guard: User cannot access redirect path, going to default dashboard')
        const { getDefaultDashboard } = await import('./permissions')
        const defaultDashboard = getDefaultDashboard(vuexRole)
        return next({ path: defaultDashboard || '/login', replace: true })
      }
    }

    // Special handling for role-based dashboard redirects on page refresh
    if (vuexAuth && vuexRole && (to.path === '/' || to.path === '/login')) {
      const { getDefaultDashboard } = await import('./permissions')
      const defaultDashboard = getDefaultDashboard(vuexRole)

      if (defaultDashboard && defaultDashboard !== to.path) {
        console.log('🔄 Enhanced Route Guard: Redirecting authenticated user to role-based dashboard:', {
          role: vuexRole,
          dashboard: defaultDashboard
        })
        return next({ path: defaultDashboard, replace: true })
      }
    }

    // Check route access
    const accessCheck = await checkRouteAccess(to, vuexStore)

    if (!accessCheck.hasAccess) {
      console.log('🚫 Enhanced Route Guard: Access denied -', accessCheck.reason)

      if (accessCheck.redirectTo) {
        // Don't add redirect query if user is being redirected to their default dashboard
        const { getDefaultDashboard } = await import('./permissions')
        const userDefaultDashboard = getDefaultDashboard(vuexRole)

        const shouldAddRedirect = accessCheck.redirectTo !== userDefaultDashboard &&
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

/**
 * Sync authentication state between Vuex and Pinia
 * @param {Object} vuexStore - Vuex store instance
 */
export async function syncAuthStores(vuexStore) {
  try {
    const piniaAuthStore = useAuthStore()

    // Sync from Vuex to Pinia if Vuex has data
    const vuexUser = vuexStore.getters['auth/user']
    const vuexToken = vuexStore.getters['auth/token']
    const vuexIsAuthenticated = vuexStore.getters['auth/isAuthenticated']

    if (vuexIsAuthenticated && vuexUser && vuexToken && !piniaAuthStore.isAuthenticated) {
      console.log('🔄 Syncing Vuex auth state to Pinia')
      piniaAuthStore.setAuthData({
        user: vuexUser,
        token: vuexToken
      })
    }

    // Sync from Pinia to Vuex if Pinia has data
    else if (piniaAuthStore.isAuthenticated && piniaAuthStore.user && !vuexIsAuthenticated) {
      console.log('🔄 Syncing Pinia auth state to Vuex')
      vuexStore.commit('auth/SET_TOKEN', piniaAuthStore.token)
      vuexStore.commit('auth/SET_USER', piniaAuthStore.user)
    }

  } catch (error) {
    console.error('❌ Failed to sync auth stores:', error)
  }
}

export default {
  checkRouteAccess,
  enhancedNavigationGuard,
  syncAuthStores
}
