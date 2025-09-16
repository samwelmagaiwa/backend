import { createRouter, createWebHistory } from 'vue-router'
import { ROLES } from '../utils/permissions'
import { preloadRouteBasedImages } from '../utils/imagePreloader'
import { enhancedNavigationGuard } from '../utils/routeGuards'

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
    meta: { requiresAuth: true, roles: [ROLES.ADMIN] }
  },
  {
    path: '/admin/dashboard',
    name: 'AdminDashboardAlt',
    component: () => import('../components/admin/AdminDashboard.vue'),
    meta: { requiresAuth: true, roles: [ROLES.ADMIN] }
  },

  {
    path: '/admin/user-roles',
    name: 'UserRoleAssignment',
    component: () => import('../components/admin/UserRoleAssignment.vue'),
    meta: { requiresAuth: true, roles: [ROLES.ADMIN] }
  },
  {
    path: '/admin/clean-role-assignment',
    name: 'CleanRoleAssignment',
    component: () => import('../components/admin/CleanRoleAssignment.vue'),
    meta: { requiresAuth: true, roles: [ROLES.ADMIN] }
  },

  {
    path: '/admin/departments',
    name: 'DepartmentManagement',
    component: () => import('../components/admin/DepartmentManagement.vue'),
    meta: { requiresAuth: true, roles: [ROLES.ADMIN] }
  },
  {
    path: '/admin/device-inventory',
    name: 'DeviceInventoryManagement',
    component: () => import('../components/admin/DeviceInventoryManagement.vue'),
    meta: { requiresAuth: true, roles: [ROLES.ADMIN] }
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
  {
    path: '/both-service-form/:id',
    name: 'BothServiceFormReview',
    component: () => import('../components/views/forms/both-service-form.vue'),
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

  // User submission forms
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
  {
    path: '/edit-booking-request',
    name: 'EditBookingRequest',
    component: () =>
      import('../components/views/booking/EditBookingRequest.vue').catch((error) => {
        console.error('Failed to load EditBookingRequest component:', error)
        // Return a fallback component
        return {
          template: `
          <div class="p-8 text-center">
            <h1 class="text-2xl font-bold text-red-600 mb-4">Component Loading Error</h1>
            <p class="text-gray-600 mb-4">Failed to load EditBookingRequest component.</p>
            <button @click="$router.go(-1)" class="bg-blue-500 text-white px-4 py-2 rounded">Go Back</button>
          </div>
        `
        }
      }),
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
  {
    path: '/request-details',
    name: 'StaffRequestDetails',
    component: () => import('../components/views/requests/InternalAccessDetails.vue'),
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
    path: '/ict-approval/requests-simple',
    name: 'RequestsListSimple',
    component: () => import('../components/views/ict-approval/RequestsListSimple.vue'),
    meta: {
      requiresAuth: true,
      roles: [ROLES.ICT_OFFICER]
    }
  },
  {
    path: '/ict-approval/requests-original',
    name: 'RequestsListOriginal',
    component: () => import('../components/views/ict-approval/RequestsList.vue'),
    meta: {
      requiresAuth: true,
      roles: [ROLES.ICT_OFFICER]
    }
  },
  {
    path: '/debug/ict-requests',
    name: 'RequestsListDebug',
    component: () => import('../components/debug/RequestsListDebug.vue'),
    meta: {
      requiresAuth: true,
      roles: [ROLES.ICT_OFFICER]
    }
  },
  {
    path: '/debug/api-test',
    name: 'ApiTestComponent',
    component: () => import('../components/debug/ApiTestComponent.vue'),
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
  // Internal Access Requests Dashboard (for approvers) - Redirect to new combined requests
  {
    path: '/hod-dashboard/request-list',
    name: 'HODDashboardRequestList',
    redirect: '/hod-dashboard/combined-requests'
  },
  // HOD Combined Access Requests List
  {
    path: '/hod-dashboard/combined-requests',
    name: 'HODCombinedRequestList',
    component: () =>
      import(/* webpackChunkName: "hod" */ '../components/views/hod/HodRequestListSimplified.vue'),
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

  // Debug route for HOD Dashboard
  {
    path: '/hod-dashboard/debug',
    name: 'HODDashboardDebug',
    component: () => import('../components/debug/HODDashboardDebug.vue'),
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

  // Debug route for Request Details
  {
    path: '/debug/request-details',
    name: 'RequestDetailsDebug',
    component: () => import('../components/debug/RequestDetailsDebug.vue'),
    meta: {
      requiresAuth: true,
      roles: Object.values(ROLES)
    }
  },
  // Debug route for API Testing
  {
    path: '/debug/api-test',
    name: 'ApiTest',
    component: () => import('../components/debug/ApiTest.vue'),
    meta: {
      requiresAuth: true,
      roles: Object.values(ROLES)
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
  {
    path: '/admin/department-hods',
    redirect: '/admin/departments'
  },
  // Admin dashboard redirect for consistency
  {
    path: '/admin',
    redirect: '/admin-dashboard'
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

// Navigation guard (Pinia-only)
router.beforeEach(async (to, from, next) => {
  try {
    return enhancedNavigationGuard(to, from, next)
  } catch (error) {
    console.error('❌ Router: Navigation error:', error)
    next('/login')
  }
})

// Image preloading after navigation
router.afterEach((to) => {
  // Preload images for the target route
  preloadRouteBasedImages(to.path)
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
