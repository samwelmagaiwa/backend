import { createRouter, createWebHistory } from 'vue-router'
import { getDefaultDashboard, ROLES } from '../utils/permissions'
import { preloadRouteBasedImages } from '../utils/imagePreloader'

// Lazy load LoginPageWrapper as well for better initial bundle size
const LoginPageWrapper = () =>
  import(/* webpackChunkName: "auth" */ '../components/LoginPageWrapper.vue')

const routes = [


  // Public routes
  {
    path: '/',
    name: 'LoginPage',
    component: LoginPageWrapper,
    meta: {
      requiresAuth: false,
      isPublic: true
    }
  },
  {
    path: '/login',
    name: 'Login',
    component: LoginPageWrapper,
    meta: {
      requiresAuth: false,
      isPublic: true
    }
  },
  {
    path: '/ict-policy',
    name: 'IctPolicy',
    component: () =>
      import(/* webpackChunkName: "public" */ '../components/IctPolicy.vue'),
    meta: {
      requiresAuth: false,
      isPublic: true
    }
  },
  {
    path: '/terms-of-service',
    name: 'TermsOfService',
    component: () =>
      import(
        /* webpackChunkName: "public" */ '../components/TermsOfService.vue'
      ),
    meta: {
      requiresAuth: false,
      isPublic: true
    }
  },

  // Dashboard routes
  {
    path: '/admin-dashboard',
    name: 'AdminDashboard',
    component: () => import('../components/admin/AdminDashboard.vue'),
    meta: { requiresAuth: true, roles: ['admin'] }
  },
  {
    path: '/admin/roles',
    name: 'RoleManagement',
    component: () => import('../components/admin/RoleManagement.vue'),
    meta: { requiresAuth: true, roles: ['admin', 'super_admin'] }
  },
  {
    path: '/admin/user-roles',
    name: 'UserRoleAssignment',
    component: () => import('../components/admin/UserRoleAssignment.vue'),
    meta: { requiresAuth: true, roles: ['admin', 'super_admin'] }
  },
  {
    path: '/admin/department-hods',
    name: 'DepartmentHodAssignment',
    component: () => import('../components/admin/DepartmentHodAssignment.vue'),
    meta: { requiresAuth: true, roles: ['admin', 'super_admin'] }
  },
  {
    path: '/user-dashboard',
    name: 'UserDashboard',
    component: () => import('../components/UserDashboardWorking.vue'),
    meta: {
      requiresAuth: false,
      isPublic: true
    }
  },
  {
    path: '/dict-dashboard',
    name: 'DictDashboard',
    component: () => import('../components/DictDashboard.vue'),
    meta: {
      requiresAuth: true,
      roles: [ROLES.ICT_DIRECTOR]
    }
  },
  {
    path: '/hod-dashboard',
    name: 'HodDashboard',
    component: () => import('../components/HodDashboard.vue'),
    meta: {
      requiresAuth: true,
      roles: [ROLES.HEAD_OF_DEPARTMENT]
    }
  },

  {
    path: '/divisional-dashboard',
    name: 'DivisionalDashboard',
    component: () => import('../components/DivisionalDashboard.vue'),
    meta: {
      requiresAuth: true,
      roles: [ROLES.DIVISIONAL_DIRECTOR]
    }
  },
  {
    path: '/ict-dashboard',
    name: 'IctDashboard',
    component: () => import('../components/IctDashboard.vue'),
    meta: {
      requiresAuth: true,
      roles: [ROLES.ICT_OFFICER]
    }
  },

  // Onboarding flow (for first-time users)
  {
    path: '/onboarding',
    name: 'Onboarding',
    component: () => import('../components/OnboardingPage.vue'),
    meta: {
      requiresAuth: true,
      roles: Object.values(ROLES).filter((role) => role !== ROLES.ADMIN) // All roles except admin
    }
  },


  // Settings (accessible to all authenticated users)
  {
    path: '/settings',
    name: 'Settings',
    component: () => import('../components/SettingsPage.vue'),
    meta: {
      requiresAuth: true,
      roles: Object.values(ROLES)
    }
  },

  // User Profile (accessible to all authenticated users)
  {
    path: '/profile',
    name: 'UserProfile',
    component: () => import('../components/UserProfile.vue'),
    meta: {
      requiresAuth: true,
      roles: Object.values(ROLES)
    }
  },


  // Access approval forms
  {
    path: '/jeeva-access',
    name: 'JeevaAccessForm',
    component: () => import('../components/views/forms/JeevaAccessForm.vue'),
    meta: {
      requiresAuth: true,
      roles: [
        ROLES.DIVISIONAL_DIRECTOR,
        ROLES.HEAD_OF_DEPARTMENT,
        ROLES.ICT_DIRECTOR,
        ROLES.ICT_OFFICER
      ]
    }
  },
  {
    path: '/wellsoft-access',
    name: 'WellsoftAccessForm',
    component: () => import('../components/views/forms/wellSoftAccessForm.vue'),
    meta: {
      requiresAuth: true,
      roles: [
        ROLES.DIVISIONAL_DIRECTOR,
        ROLES.HEAD_OF_DEPARTMENT,
        ROLES.ICT_DIRECTOR,
        ROLES.ICT_OFFICER
      ]
    }
  },
  {
    path: '/internet-access',
    name: 'InternetAccessForm',
    component: () => import('../components/views/forms/internetAccessForm.vue'),
    meta: {
      requiresAuth: true,
      roles: [
        ROLES.DIVISIONAL_DIRECTOR,
        ROLES.HEAD_OF_DEPARTMENT,
        ROLES.ICT_DIRECTOR,
        ROLES.ICT_OFFICER
      ]
    }
  },
  {
    path: '/both-service-form',
    name: 'BothServiceForm',
    component: () => import('../components/views/forms/both-service-form.vue'),
    meta: {
      requiresAuth: true,
      roles: [
        ROLES.DIVISIONAL_DIRECTOR,
        ROLES.HEAD_OF_DEPARTMENT,
        ROLES.ICT_DIRECTOR,
        ROLES.ICT_OFFICER
      ]
    },
    alias: ['/both-service-from']
  },

  // User submission forms
  // COMMENTED OUT: Individual forms - now using Combined Access Form only
  /*
  {
    path: '/user-jeeva-form',
    name: 'UserJeevaForm',
    component: () => import('../components/views/forms/userjeevaform.vue'),
    meta: {
      requiresAuth: true,
      roles: [ROLES.STAFF]
    }
  },
  {
    path: '/user-wellsoft-form',
    name: 'UserWellSoftForm',
    component: () => import('../components/views/forms/userWellSoftForm.vue'),
    meta: {
      requiresAuth: true,
      roles: [ROLES.STAFF]
    }
  },
  {
    path: '/user-internet-form',
    name: 'UserInternetForm',
    component: () => import('../components/views/forms/UserInternetAccessForm.vue'),
    meta: {
      requiresAuth: true,
      roles: [ROLES.STAFF]
    }
  },
  */
  {
    path: '/user-combined-form',
    name: 'UserCombinedForm',
    component: () =>
      import('../components/views/forms/UserCombinedAccessForm.vue'),
    meta: {
      requiresAuth: true,
      roles: [ROLES.STAFF]
    }
  },
  {
    path: '/booking-service',
    name: 'BookingService',
    component: () => import('../components/views/booking/BookingService.vue'),
    meta: {
      requiresAuth: true,
      roles: [ROLES.STAFF]
    }
  },
  {
    path: '/request-status',
    name: 'RequestStatusPage',
    component: () => import('../components/views/requests/RequestStatusPage.vue'),
    meta: {
      requiresAuth: true,
      roles: [ROLES.STAFF]
    }
  },

  // ICT Approval routes (ICT Officer only)
  {
    path: '/ict-approval/requests',
    name: 'RequestsList',
    component: () =>
      import('../components/views/ict-approval/RequestsList.vue'),
    meta: {
      requiresAuth: true,
      roles: [ROLES.ICT_OFFICER]
    }
  },
  {
    path: '/ict-approval/request/:id',
    name: 'RequestDetails',
    component: () =>
      import('../components/views/ict-approval/RequestDetails.vue'),
    meta: {
      requiresAuth: true,
      roles: [ROLES.ICT_OFFICER]
    }
  },

  // Internal Access Requests Dashboard (for approvers)
  {
    path: '/hod-dashboard/request-list',
    name: 'HODDashboardRequestList',
    component: () =>
      import('../components/views/requests/InternalAccessList.vue'),
    meta: {
      requiresAuth: true,
      roles: [
        ROLES.HEAD_OF_DEPARTMENT,
        ROLES.DIVISIONAL_DIRECTOR,
        ROLES.ICT_DIRECTOR,
        ROLES.ICT_OFFICER
      ]
    }
  },

  // Redirect old route to new route for backward compatibility
  {
    path: '/internal-access/list',
    redirect: '/hod-dashboard/request-list'
  },

  {
    path: '/internal-access/details',
    name: 'InternalAccessDetails',
    component: () =>
      import('../components/views/requests/InternalAccessDetails.vue'),
    meta: {
      requiresAuth: true,
      roles: [
        ROLES.DIVISIONAL_DIRECTOR,
        ROLES.HEAD_OF_DEPARTMENT,
        ROLES.ICT_DIRECTOR,
        ROLES.ICT_OFFICER
      ]
    }
  },

  // Admin User Management routes
  {
    path: '/service-users',
    name: 'ServiceUsers',
    component: () => import('../components/admin/JeevaUsers.vue'),
    meta: {
      requiresAuth: true,
      roles: [ROLES.ADMIN]
    }
  },
  {
    path: '/admin/onboarding-reset',
    name: 'OnboardingReset',
    component: () => import('../components/admin/OnboardingReset.vue'),
    meta: {
      requiresAuth: true,
      roles: [ROLES.ADMIN]
    }
  },
  {
    path: '/wellsoft-users',
    name: 'WellsoftUsers',
    component: () => import('../components/admin/WellsoftUsers.vue'),
    meta: {
      requiresAuth: true,
      roles: [ROLES.ADMIN]
    }
  },
  {
    path: '/internet-users',
    name: 'InternetUsers',
    component: () => import('../components/admin/InternetUsers.vue'),
    meta: {
      requiresAuth: true,
      roles: [ROLES.ADMIN]
    }
  },

  // Legacy admin routes (for backward compatibility)
  {
    path: '/admin/users/jeeva',
    redirect: '/service-users'
  },
  {
    path: '/jeeva-users',
    redirect: '/service-users'
  },
  {
    path: '/admin/users/wellsoft',
    redirect: '/wellsoft-users'
  },
  {
    path: '/admin/users/internet',
    redirect: '/internet-users'
  },

  // Catch-all route for 404 errors
  {
    path: '/:pathMatch(.*)*',
    name: 'NotFound',
    component: () =>
      import('../components/NotFound.vue').catch(() => {
        // Fallback if NotFound component doesn't exist
        return {
          template:
            '<div class="text-center p-8"><h1 class="text-2xl font-bold text-red-600">Page Not Found</h1><p class="mt-4">The requested page could not be found.</p></div>'
        }
      })
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Navigation guards
router.beforeEach(async(to, from, next) => {
  console.log('🔄 Router: Navigating from', from.path, 'to', to.path)

  try {
    // Check localStorage first for immediate auth state
    const token = localStorage.getItem('auth_token')
    const userData = localStorage.getItem('user_data')
    let storedUser = null
    let storedRole = null

    if (token && userData) {
      try {
        storedUser = JSON.parse(userData)
        storedRole = storedUser.role
        console.log('💾 Router: Found stored auth data - Role:', storedRole, 'User:', storedUser.name)
      } catch (error) {
        console.error('💾 Router: Failed to parse stored user data:', error)
        localStorage.removeItem('auth_token')
        localStorage.removeItem('user_data')
      }
    }

    // Use the new auth utility
    console.log('📦 Router: Importing auth utility...')
    const authModule = await import('../utils/auth')
    const auth = authModule.default

    // Force auth initialization if we have stored data but auth isn't initialized
    if ((token && userData && storedUser) && !auth.isAuthenticated) {
      console.log('🔄 Router: Auth not initialized but have stored data, force initializing...')
      await auth.initializeAuth(true) // Force initialization
    }

    // Use stored data as fallback if auth utility isn't ready
    const isAuthenticated = auth.isAuthenticated || (token && userData && storedUser)
    const userRole = auth.userRole || storedRole
    const user = auth.currentUser || storedUser
    const requiresAuth = to.meta.requiresAuth !== false
    const isPublicRoute = to.meta.isPublic === true

    console.log('🔍 Router: Auth state check:')
    console.log('  - hasStoredToken:', !!token)
    console.log('  - hasStoredUser:', !!userData)
    console.log('  - storedRole:', storedRole)
    console.log('  - auth.isAuthenticated:', auth.isAuthenticated)
    console.log('  - auth.userRole:', auth.userRole)
    console.log('  - finalIsAuthenticated:', isAuthenticated)
    console.log('  - finalUserRole:', userRole)
    console.log('  - finalUser:', user?.name)
    console.log('  - requiresAuth:', requiresAuth)
    console.log('  - isPublicRoute:', isPublicRoute)
    // Handle public routes
    if (isPublicRoute) {
      // If user is authenticated and trying to access login page, redirect appropriately
      if (isAuthenticated && (to.name === 'LoginPage' || to.name === 'Login')) {
        console.log('🔍 User is authenticated with role:', userRole)

        // Check if user needs onboarding (skip for admin)
        if (user && user.needs_onboarding && userRole !== 'admin') {
          console.log('🔄 User needs onboarding, redirecting...')
          return next('/onboarding')
        }

        // Check if there's a redirect query parameter
        if (to.query.redirect) {
          console.log('🔄 Redirect parameter found:', to.query.redirect)
          const redirectPath = to.query.redirect

          // Find the route that matches the redirect path
          const targetRoute = router.resolve(redirectPath)

          // Check if the user has access to the redirect route
          if (targetRoute && targetRoute.meta && targetRoute.meta.roles) {
            if (targetRoute.meta.roles.includes(userRole)) {
              console.log('✅ User has access to redirect path:', redirectPath)
              return next(redirectPath)
            } else {
              console.warn(
                '⚠️ User does not have access to redirect path:',
                redirectPath
              )
              console.log('🔄 Redirecting to default dashboard instead')
              // Fall through to default dashboard logic
            }
          } else {
            // If no role restrictions, allow the redirect
            console.log(
              '🔄 No role restrictions on redirect path, allowing:',
              redirectPath
            )
            return next(redirectPath)
          }
        }

        const defaultDashboard = getDefaultDashboard(userRole)
        console.log(
          '🏠 Default dashboard for',
          userRole,
          ':',
          defaultDashboard
        )

        if (defaultDashboard) {
          console.log('🔄 Redirecting to default dashboard:', defaultDashboard)
          return next(defaultDashboard)
        } else {
          console.error('❌ No default dashboard found for role:', userRole)
          // Fallback based on role - ensure consistency
          if (userRole === 'admin') {
            return next('/admin-dashboard')
          } else if (userRole === 'head_of_department') {
            return next('/hod-dashboard')

          } else if (userRole === 'divisional_director') {
            return next('/divisional-dashboard')
          } else if (userRole === 'ict_director') {
            return next('/dict-dashboard')
          } else if (userRole === 'ict_officer') {
            return next('/ict-dashboard')
          } else {
            return next('/user-dashboard')
          }
        }
      }
      return next()
    }

    // Handle protected routes
    if (requiresAuth) {
      // Check if user is authenticated
      if (!isAuthenticated) {
        return next({
          name: 'LoginPage',
          query: { redirect: to.fullPath }
        })
      }

      // Check if user needs onboarding (except for onboarding route itself)
      if (
        to.name !== 'Onboarding' &&
        userRole !== ROLES.ADMIN &&
        user &&
        user.needs_onboarding
      ) {
        return next('/onboarding')
      }

      // Prevent access to onboarding if already completed or admin
      if (to.name === 'Onboarding') {
        if (userRole === ROLES.ADMIN) {
          const defaultDashboard = getDefaultDashboard(userRole)
          return next(defaultDashboard || '/admin-dashboard')
        }
        if (!user || !user.needs_onboarding) {
          const defaultDashboard = getDefaultDashboard(userRole)
          return next(defaultDashboard || '/')
        }
      }

      // Check role-based access
      if (to.meta.roles && to.meta.roles.length > 0) {
        console.log('🔍 Router: Checking role access for route:', to.path)
        console.log('  - Required roles:', to.meta.roles)
        console.log('  - User role:', userRole)
        console.log('  - Stored role:', storedRole)

        // Check if user has required role (use both current and stored role)
        const hasRequiredRole = to.meta.roles.includes(userRole) || to.meta.roles.includes(storedRole)

        if (!hasRequiredRole) {
          console.log('⚠️ Router: User does not have required role for', to.path)
          console.log('  - Checked userRole:', userRole, 'in', to.meta.roles, '=', to.meta.roles.includes(userRole))
          console.log('  - Checked storedRole:', storedRole, 'in', to.meta.roles, '=', to.meta.roles.includes(storedRole))

          // Only redirect if we're sure the user doesn't have access
          // Don't redirect if we're already on the user's default dashboard
          const defaultDashboard = getDefaultDashboard(userRole || storedRole)
          console.log('🏠 Router: Default dashboard for role', userRole || storedRole, ':', defaultDashboard)

          if (defaultDashboard && defaultDashboard !== to.path) {
            console.log('🔄 Router: Redirecting to default dashboard with access denied error')
            return next({
              path: defaultDashboard,
              query: { error: 'access_denied' }
            })
          } else if (to.path === defaultDashboard) {
            console.log('✅ Router: User is accessing their default dashboard, allowing...')
            // Allow access to default dashboard even if role check fails temporarily
          } else {
            console.log('🔄 Router: No default dashboard found, redirecting to login')
            // Fallback to login if no default dashboard
            return next({
              name: 'LoginPage',
              query: { error: 'access_denied' }
            })
          }
        } else {
          console.log('✅ Router: User has required role for', to.path)
        }
      } else {
        console.log('🔍 Router: No role restrictions for route:', to.path)
      }
    }

    // Preload images for the target route
    preloadRouteBasedImages(to.path)

    console.log('✅ Router: Navigation completed successfully to', to.path)
    next()
  } catch (error) {
    console.error('❌ Router navigation error:', error)
    console.error('Error details:', {
      message: error.message,
      stack: error.stack,
      to: to.path,
      from: from.path
    })
    // Fallback to login page on any router error
    next('/')
  }
})

// After navigation guard for error handling
router.afterEach((to, from, failure) => {
  if (failure) {
    // Only log actual navigation failures, not expected ones
    if (failure.type === 8) {
      // Navigation was cancelled (type 8), this is normal behavior
      console.log('Navigation cancelled:', failure.message)
    } else if (failure.type === 16) {
      // Navigation was duplicated (type 16), user is already on target route
      console.log('Navigation duplicated - already on target route')
    } else {
      // Log actual navigation errors
      console.error('Navigation failed:', failure)
      console.error('Failed route:', to)
      console.error('Previous route:', from)

      // Provide user-friendly error message for critical failures
      if (failure.type === 2) {
        console.error(
          'Route not found - this might indicate a missing route definition'
        )
      }
    }
  }
})

// Global error handler for route component loading
router.onError((error) => {
  console.error('Router error:', error)

  // Handle component loading errors
  if (error.message && error.message.includes('Loading chunk')) {
    console.error('Chunk loading failed, reloading page...')
    window.location.reload()
  }

  // Handle __vccOpts errors
  if (error.message && error.message.includes('__vccOpts')) {
    console.error('Component compilation error, attempting recovery...')
    // Try to navigate to a safe route
    router.push('/').catch(() => {
      window.location.href = '/'
    })
  }
})

export default router
