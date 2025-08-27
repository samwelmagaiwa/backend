/**
 * Authentication System integrated with Laravel Backend
 * Uses Laravel Sanctum for API authentication
 */

import { reactive, computed } from 'vue'
import { ROLES, isValidRole, getDefaultDashboard } from './permissions'
import { authAPI } from './apiClient'

// Global reactive state
const authState = reactive({
  isAuthenticated: false,
  user: null,
  token: null,
  tokenName: null,
  sessionInfo: null,
  activeSessions: [],
  loading: false,
  error: null
})

// Role mapping from backend to frontend constants
const roleMapping = {
  admin: ROLES.ADMIN,
  divisional_director: ROLES.DIVISIONAL_DIRECTOR,
  head_of_department: ROLES.HEAD_OF_DEPARTMENT,
  'Head of Department': ROLES.HEAD_OF_DEPARTMENT, // Handle title case
  HOD: ROLES.HEAD_OF_DEPARTMENT, // Handle abbreviation
  hod: ROLES.HEAD_OF_DEPARTMENT, // Handle lowercase

  ict_director: ROLES.ICT_DIRECTOR,
  staff: ROLES.STAFF,
  ict_officer: ROLES.ICT_OFFICER
}

// Map backend role name to frontend role constant
const mapBackendRole = (backendRoleName) => {
  console.log('🔍 Backend role received:', backendRoleName)

  // Handle undefined or null roles
  if (
    !backendRoleName ||
    backendRoleName === 'undefined' ||
    backendRoleName === 'null'
  ) {
    console.error('❌ Backend returned invalid role:', backendRoleName)
    console.error(
      '❌ This indicates a database issue - user has no role assigned!'
    )
    console.log('🔄 Defaulting to "staff" role as fallback')
    return ROLES.STAFF // Default to staff role
  }

  const mappedRole = roleMapping[backendRoleName]

  if (mappedRole) {
    console.log('🎯 Successfully mapped to frontend role:', mappedRole)
    return mappedRole
  } else {
    console.warn('⚠️ Role mapping not found for:', backendRoleName)
    console.log('Available role mappings:', Object.keys(roleMapping))
    console.log('🔄 Using original role name as fallback')
    return backendRoleName
  }
}

// Onboarding utility functions
const onboardingUtils = {
  // Check if user needs onboarding
  needsOnboarding(user) {
    if (!user) {
      return false
    }

    if (user.role === ROLES.ADMIN) {
      return false
    }

    // Use backend data if available
    if (user.needs_onboarding !== undefined) {
      return user.needs_onboarding
    }

    // Fallback to localStorage
    const completedUsers = JSON.parse(
      localStorage.getItem('onboarding_completed') || '[]'
    )
    return !completedUsers.includes(user.id)
  },

  // Mark user as having completed onboarding
  async markOnboardingComplete(userId) {
    try {
      const result = await authAPI.completeOnboarding()
      if (result.success) {
        // Update the current user's onboarding status in auth state
        if (authState.user && authState.user.id === userId) {
          authState.user = {
            ...authState.user,
            needs_onboarding: false
          }
          localStorage.setItem('user_data', JSON.stringify(authState.user))
        }

        // Update localStorage for compatibility
        const completedUsers = JSON.parse(
          localStorage.getItem('onboarding_completed') || '[]'
        )
        if (!completedUsers.includes(userId)) {
          completedUsers.push(userId)
          localStorage.setItem(
            'onboarding_completed',
            JSON.stringify(completedUsers)
          )
        }
        return true
      }
      return false
    } catch (error) {
      console.error('Failed to mark onboarding complete:', error)
      return false
    }
  },

  // Check if user has completed onboarding
  hasCompletedOnboarding(user) {
    if (!user) return false

    // Use backend data if available
    if (user.needs_onboarding !== undefined) {
      return !user.needs_onboarding
    }

    // Fallback to localStorage
    const completedUsers = JSON.parse(
      localStorage.getItem('onboarding_completed') || '[]'
    )
    return completedUsers.includes(user.id)
  }
}

// Authentication functions
export const auth = {
  // State getters
  get isAuthenticated() {
    return authState.isAuthenticated
  },

  get currentUser() {
    return authState.user
  },

  get userRole() {
    return authState.user?.role || null
  },

  get userName() {
    return authState.user?.name || ''
  },

  get userEmail() {
    return authState.user?.email || ''
  },

  get isLoading() {
    return authState.loading
  },

  get error() {
    return authState.error
  },

  // Actions
  async login(credentials) {
    try {
      authState.loading = true
      authState.error = null

      // Call backend API
      const result = await authAPI.login(credentials)

      if (result.success) {
        const { user, token } = result.data

        // Debug: Log the raw user data from backend
        console.log('📊 Raw backend user data:', user)
        console.log('🔍 user.role:', user.role)
        console.log('🔍 user.role_name:', user.role_name)
        console.log('🔍 user.roles:', user.roles)
        console.log('🔍 user.permissions:', user.permissions)

        // Map backend role to frontend role constant
        // Use primary role (role_name) for routing, but store all roles
        const mappedUser = {
          ...user,
          role: mapBackendRole(user.role_name || user.role),
          roles: user.roles || [],
          permissions: user.permissions || []
        }

        // Validate user role
        if (!isValidRole(mappedUser.role)) {
          throw new Error('Invalid user role received from server')
        }

        // Store in localStorage with session info
        const sessionData = {
          token,
          tokenName: result.data.token_name,
          sessionInfo: result.data.session_info,
          user: mappedUser
        }

        localStorage.setItem('auth_token', token)
        localStorage.setItem('user_data', JSON.stringify(mappedUser))
        localStorage.setItem('session_data', JSON.stringify(sessionData))

        console.log(
          '✅ Token stored in localStorage:',
          token.substring(0, 20) + '...'
        )
        console.log('✅ User data stored:', mappedUser.name, mappedUser.role)
        console.log('✅ Session info stored:', result.data.token_name)

        // Update state
        authState.isAuthenticated = true
        authState.user = mappedUser
        authState.token = token
        authState.tokenName = result.data.token_name
        authState.sessionInfo = result.data.session_info

        console.log(
          '✅ Auth state updated - isAuthenticated:',
          authState.isAuthenticated
        )

        return { success: true, user: mappedUser }
      } else {
        throw new Error(result.error)
      }
    } catch (error) {
      const errorMessage = error.message || 'Login failed'
      authState.error = errorMessage
      return { success: false, error: errorMessage }
    } finally {
      authState.loading = false
    }
  },

  async logout() {
    try {
      authState.loading = true

      // Call backend API to invalidate token
      await authAPI.logout()

      // Clear localStorage
      localStorage.removeItem('auth_token')
      localStorage.removeItem('user_data')
      localStorage.removeItem('session_data')

      // Clear state
      authState.isAuthenticated = false
      authState.user = null
      authState.token = null
      authState.tokenName = null
      authState.sessionInfo = null
      authState.activeSessions = []
      authState.error = null

      return { success: true }
    } catch (error) {
      // Even if logout fails, clear local state
      localStorage.removeItem('auth_token')
      localStorage.removeItem('user_data')
      localStorage.removeItem('session_data')
      authState.isAuthenticated = false
      authState.user = null
      authState.token = null
      authState.tokenName = null
      authState.sessionInfo = null
      authState.activeSessions = []

      console.warn(
        'Logout API call failed, but local state cleared:',
        error.message
      )
      return { success: true }
    } finally {
      authState.loading = false
    }
  },

  // Initialize authentication from localStorage
  async initializeAuth(force = false) {
    try {
      console.log('🔄 Auth: Initializing authentication...', { force })

      // If already authenticated and not forced, don't reinitialize
      if (!force && authState.isAuthenticated && authState.user && authState.token) {
        console.log('✅ Auth: Already authenticated, skipping initialization')
        return true
      }

      // Set loading state
      authState.loading = true

      const token = localStorage.getItem('auth_token')
      const userData = localStorage.getItem('user_data')
      const sessionData = localStorage.getItem('session_data')

      console.log('🔍 Auth: Checking stored data...', {
        hasToken: !!token,
        hasUserData: !!userData,
        hasSessionData: !!sessionData
      })

      if (token && userData) {
        try {
          const user = JSON.parse(userData)
          let session = null

          console.log('📊 Auth: Parsed user data:', {
            name: user.name,
            role: user.role,
            id: user.id
          })

          // Try to parse session data if available
          if (sessionData) {
            try {
              session = JSON.parse(sessionData)
            } catch (sessionParseError) {
              console.warn('Failed to parse session data:', sessionParseError)
            }
          }

          // Validate stored data
          if (isValidRole(user.role)) {
            console.log('✅ Auth: Valid role found, setting auth state...', user.role)

            // Set auth state immediately with stored data
            authState.isAuthenticated = true
            authState.user = user
            authState.token = token
            authState.tokenName = session?.tokenName || null
            authState.sessionInfo = session?.sessionInfo || null

            console.log('✅ Auth: Auth state updated successfully')
            console.log('  - isAuthenticated:', authState.isAuthenticated)
            console.log('  - user.role:', authState.user.role)
            console.log('  - user.name:', authState.user.name)

            // Try to verify token in background (don't await to avoid blocking)
            setTimeout(() => {
              this.verifyTokenInBackground(token, user)
            }, 100)

            return true
          } else {
            console.error('❌ Auth: Invalid role in stored data:', user.role)
            // Clear invalid data
            this.clearAuthData()
          }
        } catch (parseError) {
          console.error('Failed to parse stored user data:', parseError)
          // Clear corrupted data
          this.clearAuthData()
        }
      } else {
        console.log('🚨 Auth: No stored auth data found')
        // Ensure auth state is cleared
        authState.isAuthenticated = false
        authState.user = null
        authState.token = null
        authState.tokenName = null
        authState.sessionInfo = null
        authState.activeSessions = []
      }
    } catch (error) {
      console.error('Failed to initialize auth:', error)
      // Clear auth state on error
      this.clearAuthData()
    } finally {
      // Always clear loading state
      authState.loading = false
    }

    console.log('❌ Auth: Initialization failed')
    return false
  },

  // Verify token in background without blocking initialization
  async verifyTokenInBackground(_token, _user) {
    try {
      const userResult = await authAPI.getCurrentUser()

      if (userResult.success) {
        // Update user data from backend
        const backendUser = userResult.data
        console.log(
          '🔄 Background verification - Raw backend user:',
          backendUser
        )
        console.log('🔍 Background - user.role:', backendUser.role)
        console.log('🔍 Background - user.role_name:', backendUser.role_name)
        console.log('🔍 Background - user.roles:', backendUser.roles)
        console.log(
          '🔍 Background - user.permissions:',
          backendUser.permissions
        )

        const mappedUser = {
          ...backendUser,
          role: mapBackendRole(backendUser.role_name || backendUser.role),
          roles: backendUser.roles || [],
          permissions: backendUser.permissions || []
        }

        // Update state with fresh data
        authState.user = mappedUser
        localStorage.setItem('user_data', JSON.stringify(mappedUser))
      } else {
        // If token is invalid/expired, clear auth data
        if (
          userResult.error &&
          (userResult.error.includes('401') ||
            userResult.error.includes('Unauthorized') ||
            userResult.error.includes('expired'))
        ) {
          this.clearAuthData()
          // Redirect to login
          if (
            window.location.pathname !== '/' &&
            window.location.pathname !== '/login'
          ) {
            window.location.href = '/login'
          }
        }
      }
    } catch (error) {
      // Check if it's an authentication error
      if (
        error.message &&
        (error.message.includes('401') ||
          error.message.includes('Unauthorized'))
      ) {
        this.clearAuthData()
        if (
          window.location.pathname !== '/' &&
          window.location.pathname !== '/login'
        ) {
          window.location.href = '/login'
        }
      }
      // Don't clear auth data on network errors
    }
  },

  // Clear authentication data
  clearAuthData() {
    localStorage.removeItem('auth_token')
    localStorage.removeItem('user_data')
    localStorage.removeItem('session_data')
    authState.isAuthenticated = false
    authState.user = null
    authState.token = null
    authState.tokenName = null
    authState.sessionInfo = null
    authState.activeSessions = []
    authState.error = null
  },

  // Clear error
  clearError() {
    authState.error = null
  },

  // Get default dashboard for current user
  getDefaultDashboard() {
    const dashboard = getDefaultDashboard(this.userRole)
    console.log(
      '🏠 Getting default dashboard for role:',
      this.userRole,
      '→',
      dashboard
    )
    return dashboard
  },

  // Check if user needs onboarding
  needsOnboarding() {
    return onboardingUtils.needsOnboarding(this.currentUser)
  },

  // Mark onboarding as complete
  async markOnboardingComplete(userId) {
    return await onboardingUtils.markOnboardingComplete(userId)
  },

  // Session management methods
  async logoutAll() {
    try {
      authState.loading = true

      // Call backend API to revoke all tokens
      const result = await authAPI.logoutAll()

      if (result.success) {
        // Clear localStorage
        this.clearAuthData()

        return { success: true, data: result.data }
      } else {
        throw new Error(result.error)
      }
    } catch (error) {
      // Even if logout fails, clear local state
      this.clearAuthData()
      console.warn(
        'LogoutAll API call failed, but local state cleared:',
        error.message
      )
      return { success: true }
    } finally {
      authState.loading = false
    }
  },

  async getActiveSessions() {
    try {
      const result = await authAPI.getActiveSessions()

      if (result.success) {
        authState.activeSessions = result.data.sessions || []
        return { success: true, data: result.data }
      } else {
        throw new Error(result.error)
      }
    } catch (error) {
      const errorMessage = error.message || 'Failed to fetch active sessions'
      authState.error = errorMessage
      return { success: false, error: errorMessage }
    }
  },

  async revokeSession(tokenId) {
    try {
      const result = await authAPI.revokeSession(tokenId)

      if (result.success) {
        // Refresh active sessions list
        await this.getActiveSessions()
        return { success: true, data: result.data }
      } else {
        throw new Error(result.error)
      }
    } catch (error) {
      const errorMessage = error.message || 'Failed to revoke session'
      authState.error = errorMessage
      return { success: false, error: errorMessage }
    }
  },

  // Get current session info
  get currentSession() {
    return {
      tokenName: authState.tokenName,
      sessionInfo: authState.sessionInfo,
      isAuthenticated: authState.isAuthenticated
    }
  },

  get activeSessions() {
    return authState.activeSessions
  }
}

// Initialize auth immediately on module load
auth.initializeAuth()

// Export reactive computed properties for use in components
export const useAuth = () => {
  return {
    // Reactive state
    isAuthenticated: computed(() => authState.isAuthenticated),
    currentUser: computed(() => authState.user),
    userRole: computed(() => authState.user?.role || null),
    userName: computed(() => authState.user?.name || ''),
    userEmail: computed(() => authState.user?.email || ''),
    isLoading: computed(() => authState.loading),
    error: computed(() => authState.error),

    // Computed permissions
    isAdmin: computed(() => authState.user?.role === ROLES.ADMIN),
    isStaff: computed(() => authState.user?.role === ROLES.STAFF),
    isApprover: computed(() => {
      const role = authState.user?.role
      return [
        ROLES.DIVISIONAL_DIRECTOR,
        ROLES.HEAD_OF_DEPARTMENT,

        ROLES.ICT_DIRECTOR,
        ROLES.ICT_OFFICER
      ].includes(role)
    }),

    // Onboarding state
    needsOnboarding: computed(() =>
      onboardingUtils.needsOnboarding(authState.user)
    ),
    hasCompletedOnboarding: computed(() =>
      authState.user
        ? onboardingUtils.hasCompletedOnboarding(authState.user.id)
        : false
    ),

    defaultDashboard: computed(() => getDefaultDashboard(authState.user?.role)),

    // Methods
    login: auth.login.bind(auth),
    logout: auth.logout.bind(auth),
    logoutAll: auth.logoutAll.bind(auth),
    getActiveSessions: auth.getActiveSessions.bind(auth),
    revokeSession: auth.revokeSession.bind(auth),
    clearError: auth.clearError.bind(auth),
    markOnboardingComplete: (userId) =>
      onboardingUtils.markOnboardingComplete(userId),

    // Session info
    currentSession: computed(() => auth.currentSession),
    activeSessions: computed(() => authState.activeSessions),
    tokenName: computed(() => authState.tokenName),
    sessionInfo: computed(() => authState.sessionInfo),

    // Constants
    ROLES
  }
}

// Export for direct access
export default auth
