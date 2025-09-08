/**
 * Normalize user data to ensure consistent role information
 * @param {Object} user - Raw user data from API
 * @returns {Object} - Normalized user data
 */
function normalizeUserData(user) {
  if (!user) return null

  // Determine the primary role from multiple possible sources
  const primaryRole =
    user.role ||
    user.primary_role ||
    user.role_name ||
    (user.roles && user.roles.length > 0 ? user.roles[0] : 'staff')

  // Ensure roles is always an array of strings
  let rolesArray = []
  if (Array.isArray(user.roles)) {
    // If roles is already an array of strings, use it
    if (user.roles.length > 0 && typeof user.roles[0] === 'string') {
      rolesArray = user.roles
    }
    // If roles is an array of objects, extract the name property
    else if (user.roles.length > 0 && typeof user.roles[0] === 'object') {
      rolesArray = user.roles.map((role) => role.name || role)
    }
  }
  // If no roles array but we have a primary role, create array with that role
  else if (primaryRole) {
    rolesArray = [primaryRole]
  }

  console.log('🔄 Auth Store: Normalizing user data:', {
    originalRoles: user.roles,
    normalizedRoles: rolesArray,
    primaryRole,
    originalRole: user.role,
    originalRoleName: user.role_name
  })

  return {
    ...user,
    role: primaryRole, // Normalized role field
    role_name: primaryRole, // For backward compatibility
    primary_role: primaryRole, // Explicit primary role
    roles: rolesArray, // Always an array of role name strings
    permissions: user.permissions || []
  }
}

const state = {
  user: null,
  token: localStorage.getItem('auth_token'),
  isAuthenticated: false,
  userPermissions: [],
  loading: false,
  error: null,
  sessionRestored: false, // Track if session has been restored from localStorage
  authInitialized: false, // Track if auth system is fully initialized
  restoringSession: false // Track if session restoration is in progress
}

const mutations = {
  SET_USER(state, user) {
    state.user = user
    state.isAuthenticated = !!user
    if (user) {
      localStorage.setItem('user_data', JSON.stringify(user))
      // Set permissions from user data
      state.userPermissions = user.permissions || []
    } else {
      localStorage.removeItem('user_data')
      state.userPermissions = []
    }
  },

  SET_TOKEN(state, token) {
    state.token = token
    if (token) {
      localStorage.setItem('auth_token', token)
    } else {
      localStorage.removeItem('auth_token')
    }
  },

  SET_USER_PERMISSIONS(state, permissions) {
    state.userPermissions = permissions
  },

  SET_LOADING(state, loading) {
    state.loading = loading
  },

  SET_ERROR(state, error) {
    state.error = error
  },

  CLEAR_ERROR(state) {
    state.error = null
  },

  SET_SESSION_RESTORED(state, restored) {
    state.sessionRestored = restored
  },

  SET_AUTH_INITIALIZED(state, initialized) {
    state.authInitialized = initialized
  },

  SET_RESTORING_SESSION(state, restoring) {
    state.restoringSession = restoring
  },

  LOGOUT(state) {
    state.user = null
    state.token = null
    state.isAuthenticated = false
    state.userPermissions = []
    state.loading = false
    state.error = null
    state.sessionRestored = false
    state.authInitialized = false
    state.restoringSession = false
    localStorage.removeItem('auth_token')
    localStorage.removeItem('user_data')
    localStorage.removeItem('session_data')
  }
}

const actions = {
  async login({ commit }, credentials) {
    commit('SET_LOADING', true)
    commit('CLEAR_ERROR')

    try {
      const authService = await import('@/services/authService')
      const result = await authService.default.login(credentials)

      if (result.success) {
        const { user, token } = result.data

        // Normalize user data with consistent role information
        const normalizedUserData = normalizeUserData(user)

        console.log('🔄 Auth Store: Normalized user data on login:', {
          originalRole: user.role,
          originalRoleName: user.role_name,
          originalPrimaryRole: user.primary_role,
          normalizedRole: normalizedUserData.role,
          roles: normalizedUserData.roles
        })

        commit('SET_USER', normalizedUserData)
        commit('SET_TOKEN', token)
        commit('SET_USER_PERMISSIONS', normalizedUserData.permissions)

        return { success: true, user: normalizedUserData }
      } else {
        commit('SET_ERROR', result.message)
        return {
          success: false,
          message: result.message,
          errors: result.errors
        }
      }
    } catch (error) {
      const errorMessage = error.message || 'Login failed'
      commit('SET_ERROR', errorMessage)
      return { success: false, message: errorMessage }
    } finally {
      commit('SET_LOADING', false)
    }
  },

  async logout({ commit }) {
    commit('SET_LOADING', true)

    try {
      const authService = await import('@/services/authService')
      await authService.default.logout()

      commit('LOGOUT')
      return { success: true }
    } catch (error) {
      // Clear state even if API call fails
      commit('LOGOUT')
      console.warn('Logout API failed, but local state cleared:', error)
      return { success: true }
    } finally {
      commit('SET_LOADING', false)
    }
  },

  async logoutAll({ commit }) {
    commit('SET_LOADING', true)

    try {
      const authService = await import('@/services/authService')
      const result = await authService.default.logoutAll()

      commit('LOGOUT')
      return result
    } catch (error) {
      commit('LOGOUT')
      return { success: true }
    } finally {
      commit('SET_LOADING', false)
    }
  },

  updateUser({ commit }, user) {
    const normalizedUserData = normalizeUserData(user)
    commit('SET_USER', normalizedUserData)
    commit('SET_USER_PERMISSIONS', normalizedUserData.permissions || [])
  },

  clearError({ commit }) {
    commit('CLEAR_ERROR')
  },

  // Restore session from localStorage on app initialization
  async restoreSession({ commit, dispatch, state }) {
    // Prevent multiple simultaneous restoration attempts
    if (state.restoringSession || state.sessionRestored) {
      console.log('⏳ Auth Store: Session restoration already in progress or completed')
      return { success: state.sessionRestored, user: state.user }
    }

    commit('SET_RESTORING_SESSION', true)
    console.log('🔄 Auth Store: Restoring session from localStorage...')

    const token = localStorage.getItem('auth_token')
    const userData = localStorage.getItem('user_data')

    if (token && userData) {
      try {
        const user = JSON.parse(userData)
        console.log('📆 Auth Store: Found stored session data:', {
          hasToken: !!token,
          userName: user.name,
          userRole: user.role || user.role_name,
          userId: user.id,
          rawUserData: user
        })

        // Normalize restored user data to ensure consistent role information
        const restoredUserData = normalizeUserData(user)

        console.log('🔄 Auth Store: Normalized restored user data:', {
          originalRole: user.role,
          originalRoleName: user.role_name,
          originalPrimaryRole: user.primary_role,
          normalizedRole: restoredUserData.role,
          roles: restoredUserData.roles
        })

        // Restore user and token immediately
        commit('SET_TOKEN', token)
        commit('SET_USER', restoredUserData)
        commit('SET_USER_PERMISSIONS', restoredUserData.permissions)
        commit('SET_SESSION_RESTORED', true)
        commit('SET_AUTH_INITIALIZED', true)
        commit('SET_RESTORING_SESSION', false)

        console.log('✅ Auth Store: Session restored successfully:', {
          role: restoredUserData.role,
          role_name: restoredUserData.role_name,
          userName: restoredUserData.name,
          isAuthenticated: true
        })

        // Verify token in background (don't await to avoid blocking)
        setTimeout(() => {
          dispatch('verifyToken')
        }, 100)

        return { success: true, user: restoredUserData }
      } catch (error) {
        console.error('❌ Auth Store: Failed to parse stored user data:', error)
        // Clear corrupted data
        localStorage.removeItem('auth_token')
        localStorage.removeItem('user_data')
        localStorage.removeItem('session_data')
        commit('SET_SESSION_RESTORED', true)
        commit('SET_AUTH_INITIALIZED', true)
        commit('SET_RESTORING_SESSION', false)
        return { success: false, error: 'Corrupted session data' }
      }
    } else {
      console.log('🚨 Auth Store: No stored session data found')
      commit('SET_SESSION_RESTORED', true)
      commit('SET_AUTH_INITIALIZED', true)
      commit('SET_RESTORING_SESSION', false)
      return { success: false, error: 'No stored session' }
    }
  },

  // Verify current token with backend
  async verifyToken({ commit, state }) {
    if (!state.token) {
      return { success: false, error: 'No token to verify' }
    }

    try {
      const authService = await import('@/services/authService')
      const result = await authService.default.getCurrentUser()

      if (result.success) {
        // Normalize user data from backend verification
        const userData = normalizeUserData(result.data.data)

        console.log('🔄 Auth Store: Normalized user data from token verification:', {
          originalData: result.data.data,
          normalizedRole: userData.role,
          roles: userData.roles
        })

        commit('SET_USER', userData)
        commit('SET_USER_PERMISSIONS', userData.permissions)

        console.log('✅ Auth Store: Token verified and user data updated')
        return { success: true, user: userData }
      } else {
        // Token is invalid, clear auth data
        console.warn('⚠️ Auth Store: Token verification failed, clearing session')
        commit('LOGOUT')
        return { success: false, error: 'Token verification failed' }
      }
    } catch (error) {
      console.error('❌ Auth Store: Token verification error:', error)
      // Don't clear session on network errors, only on auth errors
      if (error.response?.status === 401) {
        commit('LOGOUT')
      }
      return { success: false, error: error.message }
    }
  }
}

const getters = {
  user: (state) => state.user,
  token: (state) => state.token,
  isAuthenticated: (state) => state.isAuthenticated,
  userPermissions: (state) => state.userPermissions,
  userRole: (state) => {
    if (!state.user) return null
    // Use the normalized role field first, then fallback to other sources
    return (
      state.user.role ||
      state.user.primary_role ||
      state.user.role_name ||
      (state.user.roles && state.user.roles.length > 0 ? state.user.roles[0] : null)
    )
  },
  userRoles: (state) => state.user?.roles || [],
  loading: (state) => state.loading,
  error: (state) => state.error,
  sessionRestored: (state) => state.sessionRestored,
  authInitialized: (state) => state.authInitialized,
  restoringSession: (state) => state.restoringSession,
  isAuthReady: (state) => state.authInitialized && !state.restoringSession,
  isAdmin: (state) => state.user?.role === 'admin' || (state.user?.roles || []).includes('admin'),
  isStaff: (state) => state.user?.role === 'staff' || (state.user?.roles || []).includes('staff'),
  isApprover: (state) => {
    const role = state.user?.role
    const approverRoles = [
      'divisional_director',
      'head_of_department',
      'ict_director',
      'ict_officer'
    ]
    return (
      approverRoles.includes(role) ||
      (state.user?.roles || []).some((r) => approverRoles.includes(r))
    )
  }
}

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters
}
