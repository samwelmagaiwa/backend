import { createRouter, createWebHistory } from 'vue-router'
import LoginPage from '../components/LoginPage.vue'
import { getDefaultDashboard, ROLES } from '../utils/permissions'
import auth from '../utils/auth'

const routes = [
  // Public routes
  {
    path: '/',
    name: 'LoginPage',
    component: LoginPage,
    meta: { 
      requiresAuth: false,
      isPublic: true
    }
  },
  {
    path: '/login',
    name: 'Login',
    component: LoginPage,
    meta: { 
      requiresAuth: false,
      isPublic: true
    }
  },
  {
    path: '/ict-policy',
    name: 'IctPolicy',
    component: () => import('../components/IctPolicy.vue'),
    meta: { 
      requiresAuth: false,
      isPublic: true
    }
  },
  {
    path: '/terms-of-service',
    name: 'TermsOfService',
    component: () => import('../components/TermsOfService.vue'),
    meta: { 
      requiresAuth: false,
      isPublic: true
    }
  },

  // Dashboard routes
  {
    path: '/admin-dashboard',
    name: 'AdminDashboard',
    component: () => import('../components/AdminDashboard.vue'),
    meta: {
      requiresAuth: true,
      roles: [ROLES.ADMIN]
    }
  },
  {
    path: '/user-dashboard',
    name: 'UserDashboard',
    component: () => import('../components/UserDashboard.vue'),
    meta: {
      requiresAuth: true,
      roles: [ROLES.STAFF]
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
    path: '/hod-it-dashboard',
    name: 'HodItDashboard',
    component: () => import('../components/HodItDashboard.vue'),
    meta: {
      requiresAuth: true,
      roles: [ROLES.HOD_IT]
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
      roles: Object.values(ROLES).filter(role => role !== ROLES.ADMIN) // All roles except admin
    }
  },

  // Multi-session demo page (for testing)

  // Onboarding demo page (for testing)
  // {
  //   path: '/onboarding-demo',
  //   name: 'OnboardingDemo',
  //   component: () => import('../components/OnboardingDemo.vue'),
  //   meta: {
  //     requiresAuth: true,
  //     roles: Object.values(ROLES)
  //   }
  // },

  // Auth test page (for debugging) - DISABLED: Component not found
  // {
  //   path: '/auth-test',
  //   name: 'AuthTest',
  //   component: () => import('../components/AuthTest.vue'),
  //   meta: {
  //     requiresAuth: true,
  //     roles: Object.values(ROLES)
  //   }
  // },

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

  // Access approval forms
  {
    path: '/jeeva-access',
    name: 'JeevaAccessForm',
    component: () => import('../components/views/forms/JeevaAccessForm.vue'),
    meta: {
      requiresAuth: true,
      roles: [ROLES.DIVISIONAL_DIRECTOR, ROLES.HEAD_OF_DEPARTMENT, ROLES.HOD_IT, ROLES.ICT_DIRECTOR, ROLES.ICT_OFFICER]
    }
  },
  {
    path: '/wellsoft-access',
    name: 'WellsoftAccessForm',
    component: () => import('../components/views/forms/wellSoftAccessForm.vue'),
    meta: {
      requiresAuth: true,
      roles: [ROLES.DIVISIONAL_DIRECTOR, ROLES.HEAD_OF_DEPARTMENT, ROLES.HOD_IT, ROLES.ICT_DIRECTOR, ROLES.ICT_OFFICER]
    }
  },
  {
    path: '/internet-access',
    name: 'InternetAccessForm',
    component: () => import('../components/views/forms/internetAccessForm.vue'),
    meta: {
      requiresAuth: true,
      roles: [ROLES.DIVISIONAL_DIRECTOR, ROLES.HEAD_OF_DEPARTMENT, ROLES.HOD_IT, ROLES.ICT_DIRECTOR, ROLES.ICT_OFFICER]
    }
  },
  {
    path: '/both-service-form',
    name: 'BothServiceForm',
    component: () => import('../components/views/forms/both-service-form.vue'),
    meta: {
      requiresAuth: true,
      roles: [ROLES.DIVISIONAL_DIRECTOR, ROLES.HEAD_OF_DEPARTMENT, ROLES.HOD_IT, ROLES.ICT_DIRECTOR, ROLES.ICT_OFFICER]
    },
    alias: ['/both-service-from']
  },

  // User submission forms
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
  {
    path: '/user-combined-form',
    name: 'UserCombinedForm',
    component: () => import('../components/views/forms/UserCombinedAccessForm.vue'),
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

  // ICT Approval routes (ICT Officer only)
  {
    path: '/ict-approval/requests',
    name: 'RequestsList',
    component: () => import('../components/views/ict-approval/RequestsList.vue'),
    meta: {
      requiresAuth: true,
      roles: [ROLES.ICT_OFFICER]
    }
  },
  {
    path: '/ict-approval/request/:id',
    name: 'RequestDetails',
    component: () => import('../components/views/ict-approval/RequestDetails.vue'),
    meta: {
      requiresAuth: true,
      roles: [ROLES.ICT_OFFICER]
    }
  },

  // Internal Access Requests Dashboard (for approvers)
  {
    path: '/internal-access/list',
    name: 'InternalAccessList',
    component: () => import('../components/views/requests/InternalAccessList.vue'),
    meta: {
      requiresAuth: true,
      roles: [ROLES.DIVISIONAL_DIRECTOR, ROLES.HEAD_OF_DEPARTMENT, ROLES.HOD_IT, ROLES.ICT_DIRECTOR, ROLES.ICT_OFFICER]
    }
  },
  {
    path: '/internal-access/details',
    name: 'InternalAccessDetails',
    component: () => import('../components/views/requests/InternalAccessDetails.vue'),
    meta: {
      requiresAuth: true,
      roles: [ROLES.DIVISIONAL_DIRECTOR, ROLES.HEAD_OF_DEPARTMENT, ROLES.HOD_IT, ROLES.ICT_DIRECTOR, ROLES.ICT_OFFICER]
    }
  },

  // Admin User Management routes
  {
    path: '/jeeva-users',
    name: 'JeevaUsers',
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
    redirect: '/jeeva-users'
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
    component: () => import('../components/NotFound.vue').catch(() => {
      // Fallback if NotFound component doesn't exist
      return { template: '<div class="text-center p-8"><h1 class="text-2xl font-bold text-red-600">Page Not Found</h1><p class="mt-4">The requested page could not be found.</p></div>' }
    })
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Navigation guards
router.beforeEach(async (to, from, next) => {
  try {
    // Initialize authentication state
    await auth.initializeAuth()

    const isAuthenticated = auth.isAuthenticated
    const userRole = auth.userRole
    const requiresAuth = to.meta.requiresAuth !== false
    const isPublicRoute = to.meta.isPublic === true

  // Handle public routes
  if (isPublicRoute) {
    // If user is authenticated and trying to access login page, redirect appropriately
    if (isAuthenticated && (to.name === 'LoginPage' || to.name === 'Login')) {
      // Check if user needs onboarding
      if (auth.needsOnboarding()) {
        return next('/onboarding')
      }
      
      const defaultDashboard = getDefaultDashboard(userRole)
      if (defaultDashboard) {
        return next(defaultDashboard)
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
    if (to.name !== 'Onboarding' && userRole !== ROLES.ADMIN && auth.needsOnboarding()) {
      return next('/onboarding')
    }

    // Prevent access to onboarding if already completed or admin
    if (to.name === 'Onboarding') {
      if (userRole === ROLES.ADMIN) {
        const defaultDashboard = getDefaultDashboard(userRole)
        return next(defaultDashboard || '/admin-dashboard')
      }
      if (!auth.needsOnboarding()) {
        const defaultDashboard = getDefaultDashboard(userRole)
        return next(defaultDashboard || '/')
      }
    }

    // Check role-based access
    if (to.meta.roles && to.meta.roles.length > 0) {
      if (!to.meta.roles.includes(userRole)) {
        // User doesn't have permission, redirect to their default dashboard
        const defaultDashboard = getDefaultDashboard(userRole)
        if (defaultDashboard && defaultDashboard !== to.path) {
          return next({
            path: defaultDashboard,
            query: { error: 'access_denied' }
          })
        } else {
          // Fallback to login if no default dashboard
          return next({
            name: 'LoginPage',
            query: { error: 'access_denied' }
          })
        }
      }
    }
  }

    next()
  } catch (error) {
    console.error('Router navigation error:', error)
    // Fallback to login page on any router error
    next('/')
  }
})

// After navigation guard for error handling
router.afterEach((to, from, failure) => {
  if (failure) {
    console.error('Navigation failed:', failure)
    // Log additional details for debugging
    console.error('Failed route:', to)
    console.error('Previous route:', from)
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