const state = {
  user: null,
  token: localStorage.getItem('auth_token'),
  isAuthenticated: false,
  userPermissions: [],
  loading: false,
  error: null,
  sessionRestored: false // Track if session has been restored from localStorage
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

  LOGOUT(state) {
    state.user = null
    state.token = null
    state.isAuthenticated = false
    state.userPermissions = []
    state.loading = false
    state.error = null
    state.sessionRestored = false
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

        // Store user data with role information
        const userData = {
          ...user,
          role: user.role_name || user.role, // Ensure role is available
          roles: user.roles || [],
          permissions: user.permissions || []
        }

        commit('SET_USER', userData)
        commit('SET_TOKEN', token)
        commit('SET_USER_PERMISSIONS', userData.permissions)

        return { success: true, user: userData }
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
    commit('SET_USER', user)
    commit('SET_USER_PERMISSIONS', user.permissions || [])
  },

  clearError({ commit }) {
    commit('CLEAR_ERROR')
  },

  // Restore session from localStorage on app initialization
  async restoreSession({ commit, dispatch }) {
    console.log('🔄 Auth Store: Restoring session from localStorage...')

    const token = localStorage.getItem('auth_token')
    const userData = localStorage.getItem('user_data')

    if (token && userData) {
      try {
        const user = JSON.parse(userData)
        console.log('📊 Auth Store: Found stored session data:', {
          hasToken: !!token,
          userName: user.name,
          userRole: user.role || user.role_name,
          userId: user.id
        })

        // Restore user and token immediately
        commit('SET_TOKEN', token)
        commit('SET_USER', user)
        commit('SET_SESSION_RESTORED', true)

        console.log('✅ Auth Store: Session restored successfully')

        // Verify token in background (don't await to avoid blocking)
        setTimeout(() => {
          dispatch('verifyToken')
        }, 100)

        return { success: true, user }
      } catch (error) {
        console.error('❌ Auth Store: Failed to parse stored user data:', error)
        // Clear corrupted data
        localStorage.removeItem('auth_token')
        localStorage.removeItem('user_data')
        localStorage.removeItem('session_data')
        commit('SET_SESSION_RESTORED', true)
        return { success: false, error: 'Corrupted session data' }
      }
    } else {
      console.log('🚨 Auth Store: No stored session data found')
      commit('SET_SESSION_RESTORED', true)
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
        // Update user data with fresh backend data
        const userData = {
          ...result.data.data,
          role: result.data.data.role_name || result.data.data.role,
          roles: result.data.data.roles || [],
          permissions: result.data.data.permissions || []
        }

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
  userRole: (state) => state.user?.role || state.user?.role_name,
  userRoles: (state) => state.user?.roles || [],
  loading: (state) => state.loading,
  error: (state) => state.error,
  sessionRestored: (state) => state.sessionRestored,
  isAdmin: (state) =>
    state.user?.role === 'admin' || (state.user?.roles || []).includes('admin'),
  isStaff: (state) =>
    state.user?.role === 'staff' || (state.user?.roles || []).includes('staff'),
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
