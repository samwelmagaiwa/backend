import axios from 'axios'

const API_BASE_URL = process.env.VUE_APP_API_URL || 'http://127.0.0.1:8000/api'

// Create axios instance with default config
const axiosInstance = axios.create({
  baseURL: API_BASE_URL,
  timeout: 10000, // Default timeout
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json'
  }
})

// Create a separate instance for notification requests with longer timeout
const notificationAxiosInstance = axios.create({
  baseURL: API_BASE_URL,
  timeout: 30000, // 30 seconds for notification requests
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json'
  }
})

// Add auth token to requests for both instances
const addAuthInterceptor = (instance) => {
  instance.interceptors.request.use(
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
}

// Apply auth interceptor to both instances
addAuthInterceptor(axiosInstance)
addAuthInterceptor(notificationAxiosInstance)

// Simple cache to reduce frequent API calls
const notificationCache = {
  data: null,
  timestamp: null,
  maxAge: 15000 // 15 seconds cache
}

const notificationService = {
  /**
   * Get count of pending requests for notification badge (Universal for all roles)
   */
  async getPendingRequestsCount(forceRefresh = false) {
    // Check cache first unless force refresh is requested
    if (!forceRefresh && notificationCache.data && notificationCache.timestamp) {
      const cacheAge = Date.now() - notificationCache.timestamp
      if (cacheAge < notificationCache.maxAge) {
        console.log(
          '📋 NotificationService: Using cached count (age: ' + Math.round(cacheAge / 1000) + 's)'
        )
        return notificationCache.data
      }
    }

    let lastError = null
    const maxRetries = 2

    for (let attempt = 1; attempt <= maxRetries; attempt++) {
      try {
        console.log(
          `🔄 NotificationService: Fetching pending requests count (attempt ${attempt}/${maxRetries})...`
        )

        const response = await notificationAxiosInstance.get('/notifications/pending-count')

        if (response.data.success) {
          console.log('✅ NotificationService: Pending count loaded successfully')

          // Cache the successful response
          notificationCache.data = response.data.data
          notificationCache.timestamp = Date.now()

          return response.data.data
        } else {
          console.error(
            '❌ NotificationService: Failed to load pending count:',
            response.data.message
          )
          lastError = new Error(response.data.message || 'Failed to load pending requests count')
        }
      } catch (error) {
        console.error(
          `❌ NotificationService: Error fetching pending count (attempt ${attempt}):`,
          error
        )
        
        // Log more detailed error information
        if (error.response) {
          console.error('Response status:', error.response.status)
          console.error('Response data:', error.response.data)
          console.error('Response headers:', error.response.headers)
        }
        
        lastError = error

        // Don't retry on authentication errors
        if (error.response?.status === 401 || error.response?.status === 403) {
          console.log('🔐 Authentication error - not retrying')
          break
        }
        
        // Don't retry on server errors that are likely persistent
        if (error.response?.status === 500) {
          console.log('🚫 Server error detected - checking if retryable...')
          // Only retry server errors once to avoid spam
          if (attempt >= 2) {
            console.log('🚫 Server error - not retrying further')
            break
          }
        }

        // Add delay before retry (except on last attempt)
        if (attempt < maxRetries) {
          console.log(`⏳ Retrying in ${attempt * 1000}ms...`)
          await new Promise((resolve) => setTimeout(resolve, attempt * 1000))
        }
      }
    }

    // All retries failed, handle error
    if (lastError) {
      // If we have cached data, use it as fallback
      if (notificationCache.data) {
        console.warn('⚠️ NotificationService: Using stale cached data due to API failure')
        return notificationCache.data
      }

      // Log the error for debugging but provide graceful fallback
      console.error('🚫 NotificationService: All retry attempts failed')
      console.error('Last error details:', {
        message: lastError.message,
        status: lastError.response?.status,
        data: lastError.response?.data
      })

      // Provide graceful fallback instead of throwing error
      // This prevents the UI from breaking when the API is down
      console.warn('🔄 NotificationService: Providing fallback data to prevent UI breakage')
      const fallbackData = { total_pending: 0, requires_attention: false, details: null }
      
      // Cache the fallback for a short time to reduce repeated failed requests
      notificationCache.data = fallbackData
      notificationCache.timestamp = Date.now()
      
      return fallbackData
    }

    // Fallback to empty result if no cache and no error (shouldn't happen)
    return { total_pending: 0, requires_attention: false, details: null }
  },

  /**
   * Get detailed breakdown of pending requests by stage (Admin only)
   */
  async getPendingRequestsBreakdown() {
    try {
      console.log('🔄 NotificationService: Fetching pending requests breakdown...')
      const response = await notificationAxiosInstance.get('/notifications/breakdown')

      if (response.data.success) {
        console.log('✅ NotificationService: Breakdown loaded successfully')
        return response.data.data
      } else {
        console.error('❌ NotificationService: Failed to load breakdown:', response.data.message)
        throw new Error(response.data.message || 'Failed to load breakdown')
      }
    } catch (error) {
      console.error('❌ NotificationService: Error fetching breakdown:', error)
      let errorMessage = 'Network error while loading breakdown'

      if (error.response) {
        errorMessage = error.response.data?.message || 'Failed to load breakdown'
      } else if (error.code === 'ECONNABORTED') {
        errorMessage = 'Request timed out - server may be slow'
      } else if (error.message.includes('Network Error')) {
        errorMessage = 'Network connection error'
      }

      throw new Error(errorMessage)
    }
  },

  /**
   * Clear notification cache (useful for forcing refresh)
   */
  clearCache() {
    console.log('🗑️ NotificationService: Clearing cache')
    notificationCache.data = null
    notificationCache.timestamp = null
  },

  /**
   * Get role-specific notification settings
   */
  getRoleNotificationConfig(roleName) {
    const roleConfigs = {
      head_of_department: {
        routePattern: '/hod-dashboard',
        menuItems: ['/hod-dashboard/request-list'],
        description: 'New requests awaiting HOD approval'
      },
      divisional_director: {
        routePattern: '/divisional-dashboard',
        menuItems: ['/divisional-dashboard/combined-requests'],
        description: 'HOD-approved requests awaiting divisional approval'
      },
      ict_director: {
        routePattern: '/dict-dashboard',
        menuItems: ['/dict-dashboard/combined-requests'],
        description: 'Divisional-approved requests awaiting ICT Director approval'
      },
      head_of_it: {
        routePattern: '/head_of_it-dashboard',
        menuItems: ['/head_of_it-dashboard/combined-requests'],
        description: 'ICT Director-approved requests awaiting Head of IT approval'
      },
      ict_officer: {
        routePattern: '/ict-dashboard',
        menuItems: ['/ict-dashboard/access-requests'],
        description: 'Requests requiring implementation (excludes implemented/completed)'
      },
      admin: {
        routePattern: '/admin-dashboard',
        menuItems: ['/admin-dashboard', '/service-users'],
        description: 'System-wide pending requests'
      }
    }

    return (
      roleConfigs[roleName] || {
        routePattern: '/user-dashboard',
        menuItems: [],
        description: 'No pending approval tasks'
      }
    )
  },

  /**
   * Check if a route should show notification badge for the current user role
   */
  shouldShowNotificationForRoute(routePath, userRole) {
    const config = this.getRoleNotificationConfig(userRole)
    return config.menuItems.includes(routePath)
  }
}

export default notificationService
