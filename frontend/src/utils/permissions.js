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
  STAFF: 'staff',
  ICT_OFFICER: 'ict_officer'
}

// Define route permissions for each role
export const ROLE_PERMISSIONS = {
  [ROLES.ADMIN]: {
    routes: [
      '/admin-dashboard',
      '/jeeva-users',
      '/wellsoft-users',
      '/internet-users'
    ],
    dashboards: ['admin-dashboard'],
    forms: [],
    userManagement: ['jeeva-users', 'wellsoft-users', 'internet-users']
  },

  [ROLES.DIVISIONAL_DIRECTOR]: {
    routes: [
      '/divisional-dashboard',
      '/jeeva-access',
      '/wellsoft-access',
      '/internet-access',
      '/both-service-form',
      '/internal-access/list',
      '/internal-access/details',
      '/onboarding'
    ],
    dashboards: ['divisional-dashboard'],
    forms: [
      'jeeva-access',
      'wellsoft-access',
      'internet-access',
      'both-service-form'
    ],
    userManagement: [],
    requestsManagement: ['internal-access/list', 'internal-access/details']
  },

  [ROLES.HEAD_OF_DEPARTMENT]: {
    routes: [
      '/hod-dashboard',
      '/hod-dashboard/request-list',
      '/jeeva-access',
      '/wellsoft-access',
      '/internet-access',
      '/both-service-form',
      '/internal-access/list',
      '/internal-access/details',
      '/onboarding'
    ],
    dashboards: ['hod-dashboard'],
    forms: [
      'jeeva-access',
      'wellsoft-access',
      'internet-access',
      'both-service-form'
    ],
    userManagement: [],
    requestsManagement: ['internal-access/list', 'internal-access/details']
  },


  [ROLES.ICT_DIRECTOR]: {
    routes: [
      '/dict-dashboard',
      '/jeeva-access',
      '/wellsoft-access',
      '/internet-access',
      '/both-service-form',
      '/internal-access/list',
      '/internal-access/details',
      '/onboarding'
    ],
    dashboards: ['dict-dashboard'],
    forms: [
      'jeeva-access',
      'wellsoft-access',
      'internet-access',
      'both-service-form'
    ],
    userManagement: [],
    requestsManagement: ['internal-access/list', 'internal-access/details']
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
      '/jeeva-access',
      '/wellsoft-access',
      '/internet-access',
      '/both-service-form',
      '/ict-approval/requests',
      '/ict-approval/request/:id',
      '/internal-access/list',
      '/internal-access/details',
      '/onboarding'
    ],
    dashboards: ['ict-dashboard'],
    forms: [
      'jeeva-access',
      'wellsoft-access',
      'internet-access',
      'both-service-form'
    ],
    userManagement: [],
    deviceManagement: ['ict-approval/requests', 'ict-approval/request/:id'],
    requestsManagement: ['internal-access/list', 'internal-access/details']
  }
}

// Public routes that don't require authentication
export const PUBLIC_ROUTES = [
  '/',
  '/login',
  '/ict-policy',
  '/terms-of-service'
]

// Onboarding routes (accessible to authenticated non-admin users)
export const ONBOARDING_ROUTES = ['/onboarding']

// Settings routes (accessible to all authenticated users)
export const SETTINGS_ROUTES = ['/settings']

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

  // Check if user role exists and has permissions
  const permissions = ROLE_PERMISSIONS[userRole]
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
  const permissions = ROLE_PERMISSIONS[userRole]
  if (!permissions) {
    return PUBLIC_ROUTES
  }

  let allowedRoutes = [...PUBLIC_ROUTES, ...permissions.routes]

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
  const permissions = ROLE_PERMISSIONS[userRole]
  if (!permissions || !permissions.dashboards.length) {
    return null
  }

  // Return the first dashboard as default
  const dashboardName = permissions.dashboards[0]

  // Map dashboard names to routes
  const dashboardRoutes = {
    'admin-dashboard': '/admin-dashboard',
    'user-dashboard': '/user-dashboard',
    'dict-dashboard': '/dict-dashboard',
    'hod-dashboard': '/hod-dashboard',

    'divisional-dashboard': '/divisional-dashboard',
    'ict-dashboard': '/ict-dashboard'
  }

  return dashboardRoutes[dashboardName] || null
}

/**
 * Check if a user role has access to user management features
 * @param {string} userRole - The user's role
 * @returns {boolean} - Whether the user can manage users
 */
export function hasUserManagementAccess(userRole) {
  const permissions = ROLE_PERMISSIONS[userRole]
  return permissions && permissions.userManagement.length > 0
}

/**
 * Validate if a role exists in the system
 * @param {string} role - The role to validate
 * @returns {boolean} - Whether the role is valid
 */
export function isValidRole(role) {
  return Object.values(ROLES).includes(role)
}
