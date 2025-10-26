import axios from 'axios'

const API_BASE_URL = process.env.VUE_APP_API_URL || 'http://127.0.0.1:8000/api'

// Create axios instance with default config
const axiosInstance = axios.create({
  baseURL: API_BASE_URL,
  timeout: 0, // No timeout - let backend take as long as needed
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json'
  }
})

// Add auth token to requests
axiosInstance.interceptors.request.use(
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

const ictOfficerService = {
  /**
   * Get all access requests approved by Head of IT and available for ICT Officer implementation
   */
  async getAccessRequests() {
    try {
      console.log('🔄 IctOfficerService: Fetching access requests...')
      const response = await axiosInstance.get('/ict-officer/access-requests')

      if (response.data.success) {
        console.log('✅ IctOfficerService: Access requests loaded successfully')
        return {
          success: true,
          data: response.data.data,
          message: response.data.message
        }
      } else {
        console.error(
          '❌ IctOfficerService: Failed to load access requests:',
          response.data.message
        )
        return {
          success: false,
          message: response.data.message || 'Failed to load access requests'
        }
      }
    } catch (error) {
      console.error('❌ IctOfficerService: Error fetching access requests:', error)
      return {
        success: false,
        message: error.response?.data?.message || 'Network error while loading access requests'
      }
    }
  },

  /**
   * Get a specific access request by ID
   */
  async getAccessRequestById(requestId) {
    try {
      console.log('🔄 IctOfficerService: Fetching access request by ID:', requestId)
      const response = await axiosInstance.get(`/ict-officer/access-requests/${requestId}`)

      if (response.data.success) {
        console.log('✅ IctOfficerService: Access request loaded successfully')
        return {
          success: true,
          data: response.data.data,
          message: response.data.message
        }
      } else {
        console.error('❌ IctOfficerService: Failed to load access request:', response.data.message)
        return {
          success: false,
          message: response.data.message || 'Failed to load access request'
        }
      }
    } catch (error) {
      console.error('❌ IctOfficerService: Error fetching access request:', error)
      return {
        success: false,
        message: error.response?.data?.message || 'Network error while loading access request'
      }
    }
  },

  /**
   * Assign a task to self (ICT Officer takes ownership of implementation)
   */
  async assignTaskToSelf(requestId, notes = '') {
    try {
      console.log('🔄 IctOfficerService: Assigning task to self:', requestId)

      const response = await axiosInstance.post(
        `/ict-officer/access-requests/${requestId}/assign`,
        {
          notes: notes
        }
      )

      if (response.data.success) {
        console.log('✅ IctOfficerService: Task assigned successfully')
        return {
          success: true,
          data: response.data.data,
          message: response.data.message
        }
      } else {
        console.error('❌ IctOfficerService: Failed to assign task:', response.data.message)
        return {
          success: false,
          message: response.data.message || 'Failed to assign task'
        }
      }
    } catch (error) {
      console.error('❌ IctOfficerService: Error assigning task:', error)
      return {
        success: false,
        message: error.response?.data?.message || 'Network error while assigning task'
      }
    }
  },

  /**
   * Update progress on an assigned task
   */
  async updateProgress(requestId, status, notes = '') {
    try {
      console.log('🔄 IctOfficerService: Updating progress:', requestId, status)

      const response = await axiosInstance.put(
        `/ict-officer/access-requests/${requestId}/progress`,
        {
          status: status,
          notes: notes
        }
      )

      if (response.data.success) {
        console.log('✅ IctOfficerService: Progress updated successfully')
        return {
          success: true,
          data: response.data.data,
          message: response.data.message
        }
      } else {
        console.error('❌ IctOfficerService: Failed to update progress:', response.data.message)
        return {
          success: false,
          message: response.data.message || 'Failed to update progress'
        }
      }
    } catch (error) {
      console.error('❌ IctOfficerService: Error updating progress:', error)
      return {
        success: false,
        message: error.response?.data?.message || 'Network error while updating progress'
      }
    }
  },

  /**
   * Update implementation progress with detailed comments and status
   */
  async updateImplementationProgress(requestId, status, comments = '') {
    try {
      console.log('🔄 IctOfficerService: Updating implementation progress:', requestId, status)

      const payload = {
        implementation_status: status,
        implementation_comments: comments
      }

      // Add timestamp based on status
      if (status === 'implementation_in_progress') {
        payload.ict_officer_started_at = new Date().toISOString()
      } else if (status === 'implemented') {
        payload.ict_officer_implemented_at = new Date().toISOString()
      }

      const response = await axiosInstance.put(
        `/ict-officer/access-requests/${requestId}/implementation`,
        payload
      )

      if (response.data.success) {
        console.log('✅ IctOfficerService: Implementation progress updated successfully')
        return {
          success: true,
          data: response.data.data,
          message: response.data.message || 'Implementation progress updated successfully'
        }
      } else {
        console.error(
          '❌ IctOfficerService: Failed to update implementation progress:',
          response.data.message
        )
        return {
          success: false,
          message: response.data.message || 'Failed to update implementation progress'
        }
      }
    } catch (error) {
      console.error('❌ IctOfficerService: Error updating implementation progress:', error)
      return {
        success: false,
        message:
          error.response?.data?.message || 'Network error while updating implementation progress'
      }
    }
  },

  /**
   * Cancel an assigned task (unassign from self)
   */
  async cancelTask(requestId, reason) {
    try {
      console.log('🔄 IctOfficerService: Canceling task:', requestId)

      const response = await axiosInstance.post(
        `/ict-officer/access-requests/${requestId}/cancel`,
        {
          reason: reason
        }
      )

      if (response.data.success) {
        console.log('✅ IctOfficerService: Task canceled successfully')
        return {
          success: true,
          data: response.data.data,
          message: response.data.message
        }
      } else {
        console.error('❌ IctOfficerService: Failed to cancel task:', response.data.message)
        return {
          success: false,
          message: response.data.message || 'Failed to cancel task'
        }
      }
    } catch (error) {
      console.error('❌ IctOfficerService: Error canceling task:', error)
      return {
        success: false,
        message: error.response?.data?.message || 'Network error while canceling task'
      }
    }
  },

  /**
   * Complete implementation of access request
   */
  async completeImplementation(requestId, notes = '', signatureFile = null) {
    try {
      console.log('🔄 IctOfficerService: Completing implementation:', requestId)

      const formData = new FormData()
      formData.append('notes', notes)
      if (signatureFile) {
        formData.append('signature', signatureFile)
      }

      const response = await axiosInstance.post(
        `/ict-officer/access-requests/${requestId}/complete`,
        formData,
        {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        }
      )

      if (response.data.success) {
        console.log('✅ IctOfficerService: Implementation completed successfully')
        return {
          success: true,
          data: response.data.data,
          message: response.data.message
        }
      } else {
        console.error(
          '❌ IctOfficerService: Failed to complete implementation:',
          response.data.message
        )
        return {
          success: false,
          message: response.data.message || 'Failed to complete implementation'
        }
      }
    } catch (error) {
      console.error('❌ IctOfficerService: Error completing implementation:', error)
      return {
        success: false,
        message: error.response?.data?.message || 'Network error while completing implementation'
      }
    }
  },

  /**
   * Get detailed timeline for a specific access request
   */
  async getRequestTimeline(requestId) {
    try {
      console.log('🔄 IctOfficerService: Fetching request timeline:', requestId)
      const response = await axiosInstance.get(`/ict-officer/access-requests/${requestId}/timeline`)

      if (response.data.success) {
        console.log('✅ IctOfficerService: Request timeline loaded successfully')
        return response.data.data
      } else {
        console.error(
          '❌ IctOfficerService: Failed to load request timeline:',
          response.data.message
        )
        throw new Error(response.data.message || 'Failed to load request timeline')
      }
    } catch (error) {
      console.error('❌ IctOfficerService: Error fetching request timeline:', error)
      if (error.response) {
        throw new Error(error.response.data?.message || 'Failed to load timeline data')
      } else {
        throw new Error('Network error while loading timeline')
      }
    }
  },

  /**
   * Get access request timeline (alias for getRequestTimeline)
   */
  async getAccessRequestTimeline(requestId) {
    try {
      console.log('🔄 IctOfficerService: Fetching access request timeline:', requestId)
      const response = await axiosInstance.get(`/ict-officer/access-requests/${requestId}/timeline`)

      if (response.data.success) {
        console.log('✅ IctOfficerService: Access request timeline loaded successfully')
        return {
          success: true,
          data: response.data.data,
          message: response.data.message
        }
      } else {
        console.error(
          '❌ IctOfficerService: Failed to load access request timeline:',
          response.data.message
        )
        return {
          success: false,
          message: response.data.message || 'Failed to load access request timeline'
        }
      }
    } catch (error) {
      console.error('❌ IctOfficerService: Error fetching access request timeline:', error)
      return {
        success: false,
        message:
          error.response?.data?.message || 'Network error while loading access request timeline'
      }
    }
  },

  /**
   * Grant access to a request (final step with timeline logging)
   */
  async grantAccess(requestId, data) {
    try {
      console.log('🔄 IctOfficerService: Granting access for request:', requestId)
      console.log('Grant access data:', data)

      const response = await axiosInstance.post(
        `/ict-officer/access-requests/${requestId}/grant-access`,
        {
          comments: data.comments || '',
          implementation_status: 'implemented',
          implementation_completed_at: new Date().toISOString()
        }
      )

      if (response.data.success) {
        console.log('✅ IctOfficerService: Access granted successfully')
        return {
          success: true,
          data: response.data.data,
          message: response.data.message || 'Access granted successfully'
        }
      } else {
        console.error('❌ IctOfficerService: Failed to grant access:', response.data.message)
        return {
          success: false,
          message: response.data.message || 'Failed to grant access'
        }
      }
    } catch (error) {
      console.error('❌ IctOfficerService: Error granting access:', error)
      return {
        success: false,
        message: error.response?.data?.message || 'Network error while granting access'
      }
    }
  },

  /**
   * Get statistics for ICT Officer dashboard
   */
  async getStatistics() {
    try {
      console.log('🔄 IctOfficerService: Fetching statistics...')
      const response = await axiosInstance.get('/ict-officer/statistics')

      if (response.data.success) {
        console.log('✅ IctOfficerService: Statistics loaded successfully')
        return {
          success: true,
          data: response.data.data,
          message: response.data.message
        }
      } else {
        console.error('❌ IctOfficerService: Failed to load statistics:', response.data.message)
        return {
          success: false,
          message: response.data.message || 'Failed to load statistics'
        }
      }
    } catch (error) {
      console.error('❌ IctOfficerService: Error fetching statistics:', error)
      return {
        success: false,
        message: error.response?.data?.message || 'Network error while loading statistics'
      }
    }
  },

  /**
   * Get count of pending access requests for notification badge
   */
  async getPendingRequestsCount() {
    try {
      console.log('🔄 IctOfficerService: Fetching pending requests count...')
      const response = await axiosInstance.get('/ict-officer/pending-count')

      if (response.data.success) {
        // Success logged only in development mode
        if (process.env.NODE_ENV === 'development') {
          console.log('✅ IctOfficerService: Pending count loaded successfully')
        }
        return response.data.data
      } else {
        console.error('❌ IctOfficerService: Failed to load pending count:', response.data.message)
        throw new Error(response.data.message || 'Failed to load pending requests count')
      }
    } catch (error) {
      console.error('❌ IctOfficerService: Error fetching pending count:', error)
      if (error.response) {
        throw new Error(error.response.data?.message || 'Failed to load pending count')
      } else {
        throw new Error('Network error while loading pending count')
      }
    }
  }
}

export default ictOfficerService
