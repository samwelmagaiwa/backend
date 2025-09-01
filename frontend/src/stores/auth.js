/**
 * Pinia Auth Store
 * Provides persistent authentication state with localStorage integration
 * Works alongside Vuex auth store for enhanced reliability
 */

import { defineStore } from 'pinia'
import { ref, computed, watch } from 'vue'

const AUTH_STORAGE_KEY = 'auth_state_pinia'
const USER_STORAGE_KEY = 'user_data'
const TOKEN_STORAGE_KEY = 'auth_token'

// Helper function to safely parse JSON from localStorage
const _safeParseJSON = (value, fallback) => {
  try {
    return value ? JSON.parse(value) : fallback
  } catch (error) {
    console.warn('Failed to parse JSON from localStorage:', error)
    return fallback
  }
}

// Helper function to safely stringify and store in localStorage
const safeSetStorage = (key, value) => {
  try {
    localStorage.setItem(key, JSON.stringify(value))
    return true
  } catch (error) {
    console.error('Failed to save to localStorage:', error)
    return false
  }
}

export const useAuthStore = defineStore('auth', () => {
  // State
  const isAuthenticated = ref(false)
  const user = ref(null)
  const token = ref(null)
  const userRole = ref(null)
  const userRoles = ref([])
  const userPermissions = ref([])
  const isInitialized = ref(false)
  const isLoading = ref(false)
  const error = ref(null)

  // Getters (computed)
  const userName = computed(() => user.value?.name || 'User')
  const userEmail = computed(() => user.value?.email || '')
  const isAdmin = computed(() => userRole.value === 'admin' || userRoles.value.includes('admin'))
  const isStaff = computed(() => userRole.value === 'staff' || userRoles.value.includes('staff'))

  const isApprover = computed(() => {
    const role = userRole.value
    const approverRoles = [
      'divisional_director',
      'head_of_department',
      'ict_director',
      'ict_officer'
    ]
    return approverRoles.includes(role) || userRoles.value.some(r => approverRoles.includes(r))
  })

  // Actions
  const initializeAuth = () => {
    if (isInitialized.value) return

    console.log('🔄 Pinia Auth: Initializing authentication state...')

    // Restore from localStorage
    const storedToken = localStorage.getItem(TOKEN_STORAGE_KEY)
    const storedUserData = localStorage.getItem(USER_STORAGE_KEY)
    const storedAuthState = localStorage.getItem(AUTH_STORAGE_KEY)

    if (storedToken && storedUserData) {
      try {
        const userData = JSON.parse(storedUserData)
        const _authState = storedAuthState ? JSON.parse(storedAuthState) : {}

        // Set authentication state
        token.value = storedToken
        user.value = userData
        userRole.value = userData.role || userData.role_name
        userRoles.value = userData.roles || []
        userPermissions.value = userData.permissions || []
        isAuthenticated.value = true

        console.log('✅ Pinia Auth: Restored authentication state:', {
          userName: userData.name,
          userRole: userRole.value,
          isAuthenticated: isAuthenticated.value
        })
      } catch (error) {
        console.error('❌ Pinia Auth: Failed to restore state:', error)
        clearAuth()
      }
    } else {
      console.log('🚨 Pinia Auth: No stored authentication data found')
    }

    // Set up watchers for persistence
    setupPersistenceWatchers()

    isInitialized.value = true
    console.log('✅ Pinia Auth: Initialization completed')
  }

  const setupPersistenceWatchers = () => {
    // Watch authentication state changes
    watch(
      () => ({ isAuthenticated: isAuthenticated.value, userRole: userRole.value }),
      (newValue) => {
        const success = safeSetStorage(AUTH_STORAGE_KEY, newValue)
        if (success) {
          console.log('💾 Pinia Auth: Auth state saved:', newValue)
        }
      },
      { deep: true, immediate: false }
    )

    // Watch user data changes
    watch(
      () => user.value,
      (newUser) => {
        if (newUser) {
          const success = safeSetStorage(USER_STORAGE_KEY, newUser)
          if (success) {
            console.log('💾 Pinia Auth: User data saved')
          }
        }
      },
      { deep: true, immediate: false }
    )

    // Watch token changes
    watch(
      () => token.value,
      (newToken) => {
        if (newToken) {
          localStorage.setItem(TOKEN_STORAGE_KEY, newToken)
          console.log('💾 Pinia Auth: Token saved')
        } else {
          localStorage.removeItem(TOKEN_STORAGE_KEY)
          console.log('🗑️ Pinia Auth: Token removed')
        }
      },
      { immediate: false }
    )
  }

  const setAuthData = (authData) => {
    console.log('🔄 Pinia Auth: Setting auth data:', {
      hasUser: !!authData.user,
      hasToken: !!authData.token,
      userRole: authData.user?.role,
      userRoles: authData.user?.roles
    })

    token.value = authData.token
    user.value = authData.user
    userRole.value = authData.user?.role || authData.user?.role_name

    // Ensure roles is always an array of strings
    let rolesArray = []
    if (Array.isArray(authData.user?.roles)) {
      // If roles is already an array of strings, use it
      if (authData.user.roles.length > 0 && typeof authData.user.roles[0] === 'string') {
        rolesArray = authData.user.roles
      }
      // If roles is an array of objects, extract the name property
      else if (authData.user.roles.length > 0 && typeof authData.user.roles[0] === 'object') {
        rolesArray = authData.user.roles.map(role => role.name || role)
      }
    }
    // If no roles array but we have a primary role, create array with that role
    else if (userRole.value) {
      rolesArray = [userRole.value]
    }

    userRoles.value = rolesArray
    userPermissions.value = authData.user?.permissions || []
    isAuthenticated.value = true
    error.value = null

    console.log('✅ Pinia Auth: Auth data set successfully:', {
      role: userRole.value,
      roles: userRoles.value
    })
  }

  const updateUser = (userData) => {
    console.log('🔄 Pinia Auth: Updating user data')
    user.value = { ...user.value, ...userData }
    userRole.value = userData.role || userData.role_name || userRole.value

    // Ensure roles is always an array of strings when updating
    if (userData.roles) {
      let rolesArray = []
      if (Array.isArray(userData.roles)) {
        // If roles is already an array of strings, use it
        if (userData.roles.length > 0 && typeof userData.roles[0] === 'string') {
          rolesArray = userData.roles
        }
        // If roles is an array of objects, extract the name property
        else if (userData.roles.length > 0 && typeof userData.roles[0] === 'object') {
          rolesArray = userData.roles.map(role => role.name || role)
        }
      }
      userRoles.value = rolesArray
    }

    userPermissions.value = userData.permissions || userPermissions.value
  }

  const clearAuth = () => {
    console.log('🔄 Pinia Auth: Clearing authentication state')

    isAuthenticated.value = false
    user.value = null
    token.value = null
    userRole.value = null
    userRoles.value = []
    userPermissions.value = []
    error.value = null

    // Clear localStorage
    localStorage.removeItem(TOKEN_STORAGE_KEY)
    localStorage.removeItem(USER_STORAGE_KEY)
    localStorage.removeItem(AUTH_STORAGE_KEY)

    console.log('✅ Pinia Auth: Authentication state cleared')
  }

  const setLoading = (loading) => {
    isLoading.value = loading
  }

  const setError = (errorMessage) => {
    error.value = errorMessage
    console.error('❌ Pinia Auth: Error set:', errorMessage)
  }

  const clearError = () => {
    error.value = null
  }

  const hasPermission = (permission) => {
    return userPermissions.value.includes(permission)
  }

  const hasRole = (roles) => {
    const rolesToCheck = Array.isArray(roles) ? roles : [roles]
    return rolesToCheck.some(role =>
      userRole.value === role || userRoles.value.includes(role)
    )
  }

  const hasAnyRole = (roles) => {
    return hasRole(roles)
  }

  // Sync with Vuex store
  const syncWithVuex = async() => {
    try {
      const store = (await import('../store')).default
      const vuexUser = store.getters['auth/user']
      const vuexToken = store.getters['auth/token']
      const vuexIsAuthenticated = store.getters['auth/isAuthenticated']

      if (vuexIsAuthenticated && vuexUser && vuexToken) {
        console.log('🔄 Pinia Auth: Syncing with Vuex store')
        setAuthData({
          user: vuexUser,
          token: vuexToken
        })
      }
    } catch (error) {
      console.error('❌ Pinia Auth: Failed to sync with Vuex:', error)
    }
  }

  // Debug helpers
  const getDebugInfo = () => ({
    isAuthenticated: isAuthenticated.value,
    isInitialized: isInitialized.value,
    isLoading: isLoading.value,
    userName: userName.value,
    userRole: userRole.value,
    userRoles: userRoles.value,
    hasToken: !!token.value,
    error: error.value,
    storageData: {
      token: localStorage.getItem(TOKEN_STORAGE_KEY),
      userData: localStorage.getItem(USER_STORAGE_KEY),
      authState: localStorage.getItem(AUTH_STORAGE_KEY)
    }
  })

  return {
    // State
    isAuthenticated,
    user,
    token,
    userRole,
    userRoles,
    userPermissions,
    isInitialized,
    isLoading,
    error,

    // Getters
    userName,
    userEmail,
    isAdmin,
    isStaff,
    isApprover,

    // Actions
    initializeAuth,
    setAuthData,
    updateUser,
    clearAuth,
    setLoading,
    setError,
    clearError,
    hasPermission,
    hasRole,
    hasAnyRole,
    syncWithVuex,
    getDebugInfo
  }
})
