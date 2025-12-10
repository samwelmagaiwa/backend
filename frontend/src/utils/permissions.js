/**
 * Centralized Role-Based Access Control (RBAC) Configuration
 *
 * This file defines all roles and their allowed routes/permissions.
 * It provides a single source of truth for access control throughout the application.
 */

// Define all available roles
export const ROLES = {
  ADMIN: 'admin',
  DIVISIONAL_DIRECTOR: 'divisional_director',
  HEAD_OF_DEPARTMENT: 'head_of_department',
  ICT_DIRECTOR: 'ict_director',
  HEAD_OF_IT: 'head_of_it',
  STAFF: 'staff',
  ICT_OFFICER: 'ict_officer',
  SECRETARY_ICT: 'secretary_ict'
}

// Static route permissions for known system roles (default configuration)
export const ROLE_PERMISSIONS = {
  [ROLES.ADMIN]: {
    routes: [
      '/admin-dashboard',
      '/admin/dashboard',
      '/service-users',
      '/admin/user-roles',
      '/admin/departments',
      '/admin/onboarding-reset'
    ],
    dashboards: ['admin-dashboard'],
    forms: [],
    userManagement: ['service-users'],
    adminFeatures: ['admin/user-roles', 'admin/departments', 'admin/onboarding-reset']
  },

  [ROLES.DIVISIONAL_DIRECTOR]: {
    routes: [
      '/divisional-dashboard',
      '/divisional-dashboard/combined-requests',
      '/jeeva-access',
      '/wellsoft-access',
      '/internet-access',
      '/both-service-form',
      '/internal-access/list',
      '/internal-access/details',
      '/onboarding'
    ],
    dashboards: ['divisional-dashboard'],
    forms: ['jeeva-access', 'wellsoft-access', 'internet-access', 'both-service-form'],
    userManagement: [],
    requestsManagement: [
      'hod-dashboard/request-list',
      'internal-access/list',
      'internal-access/details'
    ]
  },

  [ROLES.HEAD_OF_DEPARTMENT]: {
    routes: [
      '/hod-dashboard',
      '/hod-dashboard/create-user',
      '/hod-dashboard/request-list',
      '/hod-dashboard/combined-requests',
      '/hod-dashboard/divisional-recommendations',
      '/jeeva-access',
      '/wellsoft-access',
      '/internet-access',
      '/both-service-form',
      '/internal-access/list',
      '/internal-access/details',
      '/onboarding'
    ],
    dashboards: ['hod-dashboard'],
    forms: ['jeeva-access', 'wellsoft-access', 'internet-access', 'both-service-form'],
    userManagement: [],
    requestsManagement: ['internal-access/list', 'internal-access/details']
  },

  [ROLES.ICT_DIRECTOR]: {
    routes: [
      '/dict-dashboard',
      '/dict-dashboard/combined-requests',
      '/jeeva-access',
      '/wellsoft-access',
      '/internet-access',
      '/both-service-form',
      '/internal-access/list',
      '/internal-access/details',
      '/onboarding'
    ],
    dashboards: ['ict-director-dashboard'],
    forms: ['jeeva-access', 'wellsoft-access', 'internet-access', 'both-service-form'],
    userManagement: [],
    requestsManagement: [
      'dict-dashboard/combined-requests',
      'internal-access/list',
      'internal-access/details'
    ]
  },

  [ROLES.STAFF]: {
    routes: [
      '/user-dashboard',
      '/user-jeeva-form',
      '/user-wellsoft-form',
      '/user-internet-form',
      '/user-combined-form',
      '/booking-service',
      '/onboarding'
    ],
    dashboards: ['user-dashboard'],
    forms: [
      'user-jeeva-form',
      'user-wellsoft-form',
      'user-internet-form',
      'user-combined-form',
      'booking-service'
    ],
    userManagement: []
  },

  [ROLES.ICT_OFFICER]: {
    routes: [
      '/ict-dashboard',
      '/ict-dashboard/access-requests',
      '/ict-dashboard/access-service',
      '/user-security-access/:id',
      '/ict-approval/requests',
      '/ict-approval/requests-simple',
      '/ict-approval/requests-original',
      '/ict-approval/request/:id',
      '/user-security-access/:id',
      '/jeeva-access',
      '/wellsoft-access',
      '/internet-access',
      '/both-service-form',
      '/both-service-form/:id',
      '/hod-dashboard/debug',
      '/debug/ict-requests',
      '/debug/user-security-access-test',
      '/debug/api-test',
      '/onboarding'
    ],
    dashboards: ['ict-dashboard'],
    forms: ['jeeva-access', 'wellsoft-access', 'internet-access', 'both-service-form'],
    userManagement: [],
    deviceManagement: [
      'ict-approval/requests',
      'ict-approval/request/:id',
      'user-security-access/:id'
    ],
    requestsManagement: ['ict-dashboard/access-requests', 'user-security-access/:id']
  },

  // ICT Secretary role: shares most dashboard/routes with ICT Officer but typically has only
  // device-booking permissions. Access Requests visibility will be refined dynamically in the
  // sidebar based on permissions.
  [ROLES.SECRETARY_ICT]: {
    // ICT Secretary: focused on device booking approvals and basic user dashboard.
    // Explicitly exclude ICT Access Requests dashboard (/ict-dashboard/access-requests).
    routes: [
      '/user-dashboard',
      // Device booking approval/monitoring (separate SPA route for secretary)
      '/secretary-approval/requests',
      // Shared detail and utility views
      '/ict-approval/requests-simple',
      '/ict-approval/requests-original',
      '/ict-approval/request/:id',
      // Staff-facing booking flows
      '/booking-service',
      '/request-status',
      '/request-details',
      // Onboarding remains accessible for all non-admin roles
      '/onboarding'
    ],
    // Use a dedicated secretary device dashboard as primary landing page
    dashboards: ['secretary-device-dashboard', 'user-dashboard'],
    forms: [],
    userManagement: [],
    deviceManagement: ['ict-approval/requests', 'ict-approval/request/:id'],
    // No access to ICT Access Requests dashboard for secretary role
    requestsManagement: []
  },

  [ROLES.HEAD_OF_IT]: {
    routes: [
      '/head_of_it-dashboard',
      '/head_of_it-dashboard/combined-requests',
      '/jeeva-access',
      '/wellsoft-access',
      '/internet-access',
      '/both-service-form',
      '/internal-access/list',
      '/internal-access/details',
      '/onboarding'
    ],
    dashboards: ['head_of_it-dashboard'],
    forms: ['jeeva-access', 'wellsoft-access', 'internet-access', 'both-service-form'],
    userManagement: [],
    requestsManagement: [
      'head_of_it-dashboard/combined-requests',
      'internal-access/list',
      'internal-access/details'
    ]
  }
}

// Dynamic role permissions registry (populated at runtime from backend roles/permissions)
const DYNAMIC_ROLE_PERMISSIONS = {}

// Public routes that don't require authentication
export const PUBLIC_ROUTES = ['/', '/login', '/ict-policy', '/terms-of-service']

// Onboarding routes (accessible to authenticated non-admin users)
export const ONBOARDING_ROUTES = ['/onboarding']

// Settings routes (accessible to all authenticated users)
export const SETTINGS_ROUTES = ['/settings']

/**
 * Internal helper: get permissions definition for a role
 * Prefers dynamic registry, then falls back to static ROLE_PERMISSIONS
 */
function getPermissionsForRole(userRole) {
  return DYNAMIC_ROLE_PERMISSIONS[userRole] || ROLE_PERMISSIONS[userRole] || null
}

/**
 * Register or extend dynamic permissions for a role at runtime.
 * This does NOT remove any static permissions; it merges routes/dashboards.
 * @param {string} roleName
 * @param {{routes?: string[], dashboards?: string[], forms?: string[], userManagement?: string[], deviceManagement?: string[], requestsManagement?: string[]}} dynamicDef
 */
export function registerDynamicRolePermissions(roleName, dynamicDef = {}) {
  if (!roleName) return

  const base = getPermissionsForRole(roleName) || {
    routes: [],
    dashboards: [],
    forms: [],
    userManagement: [],
    deviceManagement: [],
    requestsManagement: []
  }

  const merged = {
    ...base,
    ...dynamicDef,
    routes: Array.from(new Set([...(base.routes || []), ...(dynamicDef.routes || [])])),
    dashboards:
      dynamicDef.dashboards && dynamicDef.dashboards.length > 0
        ? dynamicDef.dashboards
        : base.dashboards || []
  }

  DYNAMIC_ROLE_PERMISSIONS[roleName] = merged
}

/**
 * Check if a user role has access to a specific route
 * @param {string} userRole - The user's role
 * @param {string} routePath - The route path to check
 * @returns {boolean} - Whether the user has access
 */
export function hasRouteAccess(userRole, routePath) {
  // Allow access to public routes
  if (PUBLIC_ROUTES.includes(routePath)) {
    return true
  }

  // Allow access to onboarding routes for non-admin users
  if (ONBOARDING_ROUTES.includes(routePath) && userRole !== ROLES.ADMIN) {
    return true
  }

  // Allow access to settings routes for all authenticated users
  if (SETTINGS_ROUTES.includes(routePath) && userRole) {
    return true
  }

  // Check if user role exists and has permissions (dynamic or static)
  const permissions = getPermissionsForRole(userRole)
  if (!permissions) {
    return false
  }

  // Check if route is in user's allowed routes
  return permissions.routes.includes(routePath)
}

/**
 * Get all allowed routes for a user role
 * @param {string} userRole - The user's role
 * @returns {string[]} - Array of allowed route paths
 */
export function getAllowedRoutes(userRole) {
  const permissions = getPermissionsForRole(userRole)
  if (!permissions) {
    return PUBLIC_ROUTES
  }

  let allowedRoutes = [...PUBLIC_ROUTES, ...(permissions.routes || [])]

  // Add onboarding routes for non-admin users
  if (userRole !== ROLES.ADMIN) {
    allowedRoutes = [...allowedRoutes, ...ONBOARDING_ROUTES]
  }

  // Add settings routes for all authenticated users
  if (userRole) {
    allowedRoutes = [...allowedRoutes, ...SETTINGS_ROUTES]
  }

  return allowedRoutes
}

/**
 * Get the default dashboard route for a user role
 * @param {string} userRole - The user's role
 * @returns {string|null} - Default dashboard route or null
 */
export function getDefaultDashboard(userRole) {
  const permissions = getPermissionsForRole(userRole)
  if (!permissions || !permissions.dashboards || !permissions.dashboards.length) {
    return null
  }

  // Return the first dashboard as default
  const dashboardName = permissions.dashboards[0]
  return DASHBOARD_ROUTES[dashboardName] || null
}

/**
 * Check if a user role has access to user management features
 * @param {string} userRole - The user's role
 * @returns {boolean} - Whether the user can manage users
 */
export function hasUserManagementAccess(userRole) {
  const permissions = getPermissionsForRole(userRole)
  return permissions && permissions.userManagement.length > 0
}

// Centralized dashboard route mapping - single source of truth
const DASHBOARD_ROUTES = {
  'admin-dashboard': '/admin-dashboard',
  'user-dashboard': '/user-dashboard',
  'ict-director-dashboard': '/dict-dashboard',
  'hod-dashboard': '/hod-dashboard',
  'divisional-dashboard': '/divisional-dashboard',
  'ict-dashboard': '/ict-dashboard',
  'head_of_it-dashboard': '/head_of_it-dashboard',
  // Synthetic dashboard for booking-only dynamic roles: go straight to ICT device requests
  'device-dashboard': '/ict-approval/requests',
  // Dedicated dashboard for ICT Secretary: secretary-specific device requests view
  'secretary-device-dashboard': '/secretary-approval/requests'
}

/**
 * Get all dashboard routes from role permissions
 * @returns {string[]} - Array of all dashboard routes
 */
export function getAllDashboardRoutes() {
  const dashboardRoutes = new Set()

  // Extract all dashboard routes from role permissions
  Object.values({ ...ROLE_PERMISSIONS, ...DYNAMIC_ROLE_PERMISSIONS }).forEach((permissions) => {
    if (permissions.dashboards) {
      permissions.dashboards.forEach((dashboardName) => {
        const route = DASHBOARD_ROUTES[dashboardName]
        if (route) {
          dashboardRoutes.add(route)
        }
      })
    }
  })

  return Array.from(dashboardRoutes)
}

/**
 * Check if a route path is a dashboard route
 * @param {string} routePath - The route path to check
 * @returns {boolean} - Whether the route is a dashboard route
 */
export function isDashboardRoute(routePath) {
  return Object.values(DASHBOARD_ROUTES).includes(routePath)
}

/**
 * Get dashboard routes for a specific role
 * @param {string} userRole - The user's role
 * @returns {string[]} - Array of dashboard routes for the role
 */
export function getDashboardRoutesForRole(userRole) {
  const permissions = ROLE_PERMISSIONS[userRole]
  if (!permissions || !permissions.dashboards) {
    return []
  }

  return permissions.dashboards
    .map((dashboardName) => DASHBOARD_ROUTES[dashboardName])
    .filter((route) => route)
}

/**
 * Validate if a role exists in the system
 * @param {string} role - The role to validate
 * @returns {boolean} - Whether the role is valid
 */
export function isValidRole(role) {
  return Object.values(ROLES).includes(role)
}

/**
 * Infer and register dynamic permissions for a user based on backend permissions.
 * This is a conservative heuristic: unknown roles become either staff-like or ICT-like.
 * It does NOT alter existing system roles.
 * @param {Object} user - Authenticated user object from backend
 */
export function inferDynamicPermissionsForUser(user) {
  if (!user || typeof user !== 'object') return

  const primaryRole = user.role || user.role_name || user.primary_role
  if (!primaryRole) return

  // Do not override explicit static configuration for known system roles
  if (ROLE_PERMISSIONS[primaryRole]) {
    return
  }

  const permissions = Array.isArray(user.permissions) ? user.permissions : []

  const hasBookingApproval = permissions.some((p) =>
    [
      'approve_device_bookings',
      'view_device_bookings',
      'view_booking_statistics',
      'manage_device_inventory'
    ].includes(p)
  )

  if (hasBookingApproval) {
    // ICT-like role focused on device bookings ONLY (no user-access workflow).
    // Show a device-centric dashboard and hide ICT Access Requests from sidebar/routes.
    registerDynamicRolePermissions(primaryRole, {
      routes: [
        // Device booking approval/monitoring
        '/ict-approval/requests',
        '/ict-approval/request/:id',

        // Staff-facing booking flows
        '/booking-service',
        '/request-status',
        '/request-details',

        // Onboarding remains accessible for all non-admin roles
        '/onboarding'
      ],
      // Use synthetic "device-dashboard" so default dashboard is the device requests page
      dashboards: ['device-dashboard']
    })
  } else {
    // Default to staff-like experience
    registerDynamicRolePermissions(primaryRole, {
      routes: [
        '/user-dashboard',
        '/user-combined-form',
        '/booking-service',
        '/request-status',
        '/request-details',
        '/onboarding'
      ],
      dashboards: ['user-dashboard']
    })
  }
}
