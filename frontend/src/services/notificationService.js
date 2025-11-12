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

const DEBUG = process.env.NODE_ENV === 'development'

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
   * Resend/notify SMS generically across roles with broad endpoint fallbacks.
   */
  async resendSmsGeneric({ requestId, role = null, target = null, phone = null, channel = 'sms' }) {
    // Build normalization helpers
    const normalizePhone = (p) => {
      if (!p) return null
      const s = String(p).trim()
      const keepPlus = s.startsWith('+')
      const digits = s.replace(/[^\d]/g, '')
      return keepPlus ? `+${digits}` : digits
    }
    const rawPhone = phone || null
    const normalized = normalizePhone(rawPhone)

    // Payload with many compatibility flags and keys
    const payload = {
      request_id: requestId,
      role,
      target,
      notification_type: 'generic_sms',
      resend_only: true,
      force: true,
      force_resend: true,
      resend_sms: true,
      send_sms: true,
      notification_channel: channel,
      // Phone variants
      phone: rawPhone || undefined,
      phone_number: normalized || undefined,
      recipient_phone: normalized || undefined,
      destination_phone: normalized || undefined,
      msisdn: normalized || undefined,
      contact_phone: normalized || undefined,
      requester_phone: rawPhone || undefined,
      requester_phone_number: rawPhone || undefined,
      staff_phone: rawPhone || undefined,
      staff_phone_number: rawPhone || undefined
    }

    // Endpoint candidates (generic + role-based + request-scoped)
    const roleBases = []
    if (role) {
      // map a few known synonyms
      const aliases = {
        head_of_it: ['head-of-it', 'head_it'],
        hod: ['hod', 'head-of-department', 'head_of_department'],
        divisional_director: ['divisional', 'divisional-director'],
        ict_director: ['ict-director', 'dict', 'ict_director'],
        ict_officer: ['ict-officer', 'ict_officer'],
        staff: ['staff', 'user', 'requester']
      }[role] || [role]
      roleBases.push(...aliases)
    }

    const endpoints = []
    // Generic
    endpoints.push(`/notifications/resend`)
    endpoints.push(`/notifications/send`)
    if (requestId) {
      endpoints.push(`/requests/${requestId}/notify`)
      endpoints.push(`/requests/${requestId}/resend-sms`)
      endpoints.push(`/requests/${requestId}/send-notification`)
    }
    // Role-scoped
    for (const base of roleBases) {
      if (requestId) {
        endpoints.push(`/${base}/requests/${requestId}/notify`)
        endpoints.push(`/${base}/requests/${requestId}/resend-sms`)
        endpoints.push(`/${base}/requests/${requestId}/send-notification`)
        // Some modules use resource names
        endpoints.push(`/${base}/access-requests/${requestId}/notify`)
        endpoints.push(`/${base}/access-requests/${requestId}/resend-sms`)
        endpoints.push(`/${base}/access-requests/${requestId}/send-notification`)
      }
      endpoints.push(`/${base}/notify`)
      endpoints.push(`/${base}/resend-sms`)
      endpoints.push(`/${base}/send-notification`)
    }

    let response
    let lastError
    for (let i = 0; i < endpoints.length; i++) {
      try {
        if (process.env.NODE_ENV === 'development') {
          console.log(
            `🔄 NotificationService: Trying resend endpoint ${i + 1}/${endpoints.length}:`,
            endpoints[i]
          )
        }
        response = await notificationAxiosInstance.post(endpoints[i], payload)
        if (process.env.NODE_ENV === 'development') {
          console.log('✅ NotificationService: Resend success via', endpoints[i])
        }
        return {
          success: !!response.data?.success,
          data: response.data?.data,
          message: response.data?.message
        }
      } catch (e) {
        lastError = e
        if (process.env.NODE_ENV === 'development') {
          console.warn(
            '❌ NotificationService: Endpoint failed:',
            endpoints[i],
            e.response?.status,
            e.response?.data?.message || e.message
          )
        }
        continue
      }
    }

    return {
      success: false,
      message:
        lastError?.response?.data?.message ||
        lastError?.message ||
        'Failed to retry SMS via any endpoint'
    }
  },

  /**
   * Get count of pending requests for notification badge (Universal for all roles)
   */
  async getPendingRequestsCount(forceRefresh = false) {
    // Check cache first unless force refresh is requested
    if (!forceRefresh && notificationCache.data && notificationCache.timestamp) {
      const cacheAge = Date.now() - notificationCache.timestamp
      if (cacheAge < notificationCache.maxAge) {
        if (DEBUG)
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
        if (DEBUG && attempt === 1)
          console.log('🔄 NotificationService: Fetching pending requests count...')

        const response = await notificationAxiosInstance.get('/notifications/pending-count')

        if (response.data.success) {
          if (DEBUG) console.log('✅ NotificationService: Pending count loaded successfully')

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
        if (attempt === maxRetries && DEBUG) {
          console.error(
            '❌ NotificationService: Error fetching pending count:',
            error?.message || error
          )
        }

        // Log more detailed error information
        if (error.response) {
          console.error('Response status:', error.response.status)
          console.error('Response data:', error.response.data)
          console.error('Response headers:', error.response.headers)
        }

        lastError = error

        // Don't retry on authentication errors
        if (error.response?.status === 401 || error.response?.status === 403) {
          if (DEBUG) console.log('🔐 Authentication error - not retrying')
          break
        }

        // Don't retry on server errors that are likely persistent
        if (error.response?.status === 500) {
          if (DEBUG) console.log('🚫 Server error detected - checking if retryable...')
          // Only retry server errors once to avoid spam
          if (attempt >= 2) {
            if (DEBUG) console.log('🚫 Server error - not retrying further')
            break
          }
        }

        // Add delay before retry (except on last attempt)
        if (attempt < maxRetries) {
          if (DEBUG && attempt === 1) console.log(`⏳ Retrying in ${attempt * 1000}ms...`)
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
      if (DEBUG) {
        console.error('🚫 NotificationService: All retry attempts failed')
        console.error('Last error details:', {
          message: lastError.message,
          status: lastError.response?.status,
          data: lastError.response?.data
        })
      }

      // Provide graceful fallback instead of throwing error
      // This prevents the UI from breaking when the API is down
      if (DEBUG)
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
      if (DEBUG) console.log('🔄 NotificationService: Fetching pending requests breakdown...')
      const response = await notificationAxiosInstance.get('/notifications/breakdown')

      if (response.data.success) {
        if (DEBUG) console.log('✅ NotificationService: Breakdown loaded successfully')
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
    if (DEBUG) console.log('🗑️ NotificationService: Clearing cache')
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
