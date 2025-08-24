import { createRouter, createWebHistory } from 'vue-router'
import { getDefaultDashboard, ROLES } from '../utils/permissions'
import auth from '../utils/auth'
import { preloadRouteBasedImages } from '../utils/imagePreloader'

// Lazy load LoginPage as well for better initial bundle size
const LoginPage = () => import(/* webpackChunkName: "auth" */ '../components/LoginPage.vue')

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
    component: () => import(/* webpackChunkName: "public" */ '../components/IctPolicy.vue'),
    meta: { 
      requiresAuth: false,
      isPublic: true
    }
  },
  {
    path: '/terms-of-service',
    name: 'TermsOfService',
    component: () => import(/* webpackChunkName: "public" */ '../components/TermsOfService.vue'),
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
  // HOD Dashboard removed - HOD users now use hod-dashboard/request-list as their dashboard
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
    path: '/hod-dashboard/request-list',
    name: 'HODDashboardRequestList',
    component: () => import('../components/views/requests/InternalAccessList.vue'),
    meta: {
      requiresAuth: true,
      roles: [ROLES.HEAD_OF_DEPARTMENT, ROLES.DIVISIONAL_DIRECTOR, ROLES.ICT_DIRECTOR, ROLES.HOD_IT, ROLES.ICT_OFFICER]
    }
  },
  
  // Simple version for debugging (if needed)
  {
    path: '/hod-dashboard/request-list-simple',
    name: 'HODDashboardRequestListSimple',
    component: () => import('../components/views/requests/InternalAccessListSimple.vue'),
    meta: {
      requiresAuth: true,
      roles: [ROLES.HEAD_OF_DEPARTMENT, ROLES.DIVISIONAL_DIRECTOR, ROLES.ICT_DIRECTOR, ROLES.HOD_IT, ROLES.ICT_OFFICER]
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
    // Initialize authentication state from store
    const store = await import('@/store')
    
    // Initialize auth state from localStorage if available
    const token = localStorage.getItem('auth_token')
    const userData = localStorage.getItem('user_data')
    
    if (token && userData && !store.default.getters['auth/isAuthenticated']) {
      try {
        const user = JSON.parse(userData)
        store.default.commit('auth/SET_TOKEN', token)
        store.default.commit('auth/SET_USER', user)
        console.log('🔄 Restored auth state from localStorage:', user.role)
      } catch (error) {
        console.error('Failed to restore auth state:', error)
        localStorage.removeItem('auth_token')
        localStorage.removeItem('user_data')
      }
    }
    
    const isAuthenticated = store.default.getters['auth/isAuthenticated']
    const userRole = store.default.getters['auth/userRole']
    const requiresAuth = to.meta.requiresAuth !== false
    const isPublicRoute = to.meta.isPublic === true

  // Handle public routes
  if (isPublicRoute) {
    // If user is authenticated and trying to access login page, redirect appropriately
    if (isAuthenticated && (to.name === 'LoginPage' || to.name === 'Login')) {
      console.log('🔍 User is authenticated with role:', userRole)
      
      // Check if user needs onboarding (skip for admin)
      const user = store.default.getters['auth/user']
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
            console.warn('⚠️ User does not have access to redirect path:', redirectPath)
            console.log('🔄 Redirecting to default dashboard instead')
            // Fall through to default dashboard logic
          }
        } else {
          // If no role restrictions, allow the redirect
          console.log('🔄 No role restrictions on redirect path, allowing:', redirectPath)
          return next(redirectPath)
        }
      }
      
      const defaultDashboard = getDefaultDashboard(userRole)
      console.log('🏠 Default dashboard for', userRole, ':', defaultDashboard)
      
      if (defaultDashboard) {
        console.log('🔄 Redirecting to default dashboard:', defaultDashboard)
        return next(defaultDashboard)
      } else {
        console.error('❌ No default dashboard found for role:', userRole)
        // Fallback based on role
        if (userRole === 'admin') {
          return next('/admin-dashboard')
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
    const user = store.default.getters['auth/user']
    if (to.name !== 'Onboarding' && userRole !== ROLES.ADMIN && user && user.needs_onboarding) {
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

    // Preload images for the target route
    preloadRouteBasedImages(to.path)
    
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
        console.error('Route not found - this might indicate a missing route definition')
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