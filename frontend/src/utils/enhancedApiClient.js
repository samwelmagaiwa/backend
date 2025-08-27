/**
 * Enhanced API Client with Request Deduplication
 * Prevents duplicate API requests and provides consistent error handling
 */

import axios from 'axios'
import { API_CONFIG, APP_CONFIG } from './config'
import { requestDeduplicator } from './requestDeduplicator'

// Create single axios instance
const apiClient = axios.create({
  baseURL: API_CONFIG.BASE_URL,
  timeout: API_CONFIG.TIMEOUT,
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json',
    'X-Requested-With': 'XMLHttpRequest'
  }
})

// Request counter for tracking
let requestCounter = 0

// Request interceptor
apiClient.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('auth_token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }

    // Add request ID for tracking
    config.requestId = `${Date.now()}-${++requestCounter}`
    config.startTime = Date.now()

    if (APP_CONFIG.DEBUG) {
      console.log(`🚀 API Request [${config.requestId}]: ${config.method?.toUpperCase()} ${config.url}`)
    }

    return config
  },
  (error) => {
    console.error('❌ API Request Error:', error)
    return Promise.reject(error)
  }
)

// Response interceptor
apiClient.interceptors.response.use(
  (response) => {
    const duration = Date.now() - response.config.startTime

    if (APP_CONFIG.DEBUG) {
      console.log(`✅ API Response [${response.config.requestId}]: ${response.status} (${duration}ms)`)
    }

    return response
  },
  (error) => {
    const duration = error.config ? Date.now() - error.config.startTime : 0

    if (APP_CONFIG.DEBUG) {
      console.error(`❌ API Error [${error.config?.requestId}]: ${error.message} (${duration}ms)`)
    }

    // Handle 401 Unauthorized
    if (error.response?.status === 401) {
      console.warn('🔐 Authentication failed - clearing tokens')
      localStorage.removeItem('auth_token')
      localStorage.removeItem('user_data')
      localStorage.removeItem('session_data')

      if (window.location.pathname !== '/' && window.location.pathname !== '/login') {
        window.location.href = '/'
      }
    }

    return Promise.reject(error)
  }
)

// Enhanced API methods with deduplication
export const enhancedAPI = {
  /**
   * GET request with automatic deduplication
   */
  async get(url, config = {}) {
    const key = requestDeduplicator.generateKey('GET', url)
    return requestDeduplicator.executeRequest(key, () =>
      apiClient.get(url, config)
    )
  },

  /**
   * POST request with optional deduplication
   */
  async post(url, data, config = {}) {
    const shouldDeduplicate = config.deduplicate === true
    delete config.deduplicate

    if (shouldDeduplicate) {
      const key = requestDeduplicator.generateKey('POST', url, data)
      return requestDeduplicator.executeRequest(key, () =>
        apiClient.post(url, data, config)
      )
    }

    return apiClient.post(url, data, config)
  },

  /**
   * PUT request with optional deduplication
   */
  async put(url, data, config = {}) {
    const shouldDeduplicate = config.deduplicate === true
    delete config.deduplicate

    if (shouldDeduplicate) {
      const key = requestDeduplicator.generateKey('PUT', url, data)
      return requestDeduplicator.executeRequest(key, () =>
        apiClient.put(url, data, config)
      )
    }

    return apiClient.put(url, data, config)
  },

  /**
   * PATCH request with optional deduplication
   */
  async patch(url, data, config = {}) {
    const shouldDeduplicate = config.deduplicate === true
    delete config.deduplicate

    if (shouldDeduplicate) {
      const key = requestDeduplicator.generateKey('PATCH', url, data)
      return requestDeduplicator.executeRequest(key, () =>
        apiClient.patch(url, data, config)
      )
    }

    return apiClient.patch(url, data, config)
  },

  /**
   * DELETE request with optional deduplication
   */
  async delete(url, config = {}) {
    const shouldDeduplicate = config.deduplicate === true
    delete config.deduplicate

    if (shouldDeduplicate) {
      const key = requestDeduplicator.generateKey('DELETE', url)
      return requestDeduplicator.executeRequest(key, () =>
        apiClient.delete(url, config)
      )
    }

    return apiClient.delete(url, config)
  },

  /**
   * Get the raw axios instance for special cases
   */
  getInstance() {
    return apiClient
  }
}

// Export both the enhanced API and the raw client
export { apiClient }
export default enhancedAPI
