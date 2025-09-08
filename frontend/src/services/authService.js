import axios from 'axios'

const API_BASE_URL = process.env.VUE_APP_API_URL || 'http://127.0.0.1:8000/api'

// Create axios instance with default config
const apiClient = axios.create({
  baseURL: API_BASE_URL,
  timeout: parseInt(process.env.VUE_APP_API_TIMEOUT) || 10000,
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json'
  }
})

// Add auth token to requests
apiClient.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('auth_token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  (error) => {
    return Promise.reject(error)
  }
)

// Handle response errors
apiClient.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      // Token expired or invalid
      localStorage.removeItem('auth_token')
      localStorage.removeItem('user_data')
      localStorage.removeItem('session_data')
      window.location.href = '/login'
    }
    return Promise.reject(error)
  }
)

export const authService = {
  /**
   * Login user with email and password
   * @param {Object} credentials - { email, password }
   * @returns {Promise<Object>} - Login response
   */
  async login(credentials) {
    try {
      const response = await apiClient.post('/login', credentials)
      return {
        success: true,
        data: response.data
      }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || 'Login failed',
        errors: error.response?.data?.errors || {}
      }
    }
  },

  /**
   * Logout current user
   * @returns {Promise<Object>} - Logout response
   */
  async logout() {
    try {
      const response = await apiClient.post('/logout')
      return {
        success: true,
        data: response.data
      }
    } catch (error) {
      // Even if logout API fails, we should clear local state
      return {
        success: true,
        message: 'Logged out locally (API call failed)'
      }
    }
  },

  /**
   * Logout from all sessions
   * @returns {Promise<Object>} - Logout all response
   */
  async logoutAll() {
    try {
      const response = await apiClient.post('/logout-all')
      return {
        success: true,
        data: response.data
      }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || 'Logout all failed'
      }
    }
  },

  /**
   * Get current user data
   * @returns {Promise<Object>} - User data response
   */
  async getCurrentUser() {
    try {
      const response = await apiClient.get('/current-user')
      return {
        success: true,
        data: response.data
      }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to get user data',
        status: error.response?.status
      }
    }
  },

  /**
   * Get role-based redirect URL
   * @returns {Promise<Object>} - Redirect URL response
   */
  async getRoleBasedRedirect() {
    try {
      const response = await apiClient.get('/role-redirect')
      return {
        success: true,
        data: response.data
      }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to get redirect URL',
        status: error.response?.status
      }
    }
  },

  /**
   * Get active sessions
   * @returns {Promise<Object>} - Active sessions response
   */
  async getActiveSessions() {
    try {
      const response = await apiClient.get('/active-sessions')
      return {
        success: true,
        data: response.data
      }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to get active sessions'
      }
    }
  },

  /**
   * Revoke a specific session
   * @param {number} tokenId - Token ID to revoke
   * @returns {Promise<Object>} - Revoke session response
   */
  async revokeSession(tokenId) {
    try {
      const response = await apiClient.post('/revoke-session', {
        token_id: tokenId
      })
      return {
        success: true,
        data: response.data
      }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to revoke session'
      }
    }
  }
}

export default authService
