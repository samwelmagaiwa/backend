import apiClient from '../utils/apiClient'

/**
 * Service for handling combined user access requests
 * Supports multiple access types in a single request (Jeeva, Wellsoft, Internet)
 */
export const userCombinedAccessService = {
  /**
   * Submit a combined access request
   * @param {FormData} formData - Form data including signature file
   * @returns {Promise<Object>} - API response
   */
  async submitCombinedRequest(formData) {
    try {
      const response = await apiClient.post('/v1/combined-access', formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })
      return {
        success: true,
        data: response.data
      }
    } catch (error) {
      console.error('Combined access request submission failed:', error)
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to submit combined access request',
        errors: error.response?.data?.errors || {},
        status: error.response?.status
      }
    }
  },

  /**
   * Get user's combined access requests
   * @param {Object} params - Query parameters (status, search, etc.)
   * @returns {Promise<Object>} - API response with paginated requests
   */
  async getUserRequests(params = {}) {
    try {
      const response = await apiClient.get('/v1/user-access', { params })
      return {
        success: true,
        data: response.data
      }
    } catch (error) {
      console.error('Failed to fetch user access requests:', error)
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to fetch requests',
        status: error.response?.status
      }
    }
  },

  /**
   * Get a specific access request by ID
   * @param {number} requestId - Request ID
   * @returns {Promise<Object>} - API response with request details
   */
  async getRequestById(requestId) {
    try {
      const response = await apiClient.get(`/v1/user-access/${requestId}`)
      return {
        success: true,
        data: response.data
      }
    } catch (error) {
      console.error('Failed to fetch request details:', error)
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to fetch request details',
        status: error.response?.status
      }
    }
  },

  /**
   * Update an existing access request
   * @param {number} requestId - Request ID
   * @param {FormData} formData - Updated form data
   * @returns {Promise<Object>} - API response
   */
  async updateRequest(requestId, formData) {
    try {
      // Use POST with method override for multipart/form-data compatibility
      formData.append('_method', 'PUT')

      const response = await apiClient.post(`/v1/user-access/${requestId}`, formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })
      return {
        success: true,
        data: response.data
      }
    } catch (error) {
      console.error('Failed to update access request:', error)
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to update request',
        errors: error.response?.data?.errors || {},
        status: error.response?.status
      }
    }
  },

  /**
   * Delete an access request
   * @param {number} requestId - Request ID
   * @returns {Promise<Object>} - API response
   */
  async deleteRequest(requestId) {
    try {
      const response = await apiClient.delete(`/v1/user-access/${requestId}`)
      return {
        success: true,
        data: response.data
      }
    } catch (error) {
      console.error('Failed to delete access request:', error)
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to delete request',
        status: error.response?.status
      }
    }
  },

  /**
   * Get available departments
   * @returns {Promise<Object>} - API response with departments list
   */
  async getDepartments() {
    try {
      const response = await apiClient.get('/v1/departments')
      return {
        success: true,
        data: response.data
      }
    } catch (error) {
      console.error('Failed to fetch departments:', error)
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to fetch departments',
        status: error.response?.status
      }
    }
  },

  /**
   * Check if signature exists for a PF number
   * @param {string} pfNumber - PF Number
   * @returns {Promise<Object>} - API response with signature info
   */
  async checkSignature(pfNumber) {
    try {
      const response = await apiClient.post('/v1/check-signature', {
        pf_number: pfNumber
      })
      return {
        success: true,
        data: response.data
      }
    } catch (error) {
      console.error('Failed to check signature:', error)
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to check signature',
        status: error.response?.status
      }
    }
  },

  /**
   * Check if user has any pending requests
   * @returns {Promise<Object>} - API response with pending request status
   */
  async checkPendingRequests() {
    try {
      const response = await apiClient.get('/v1/user-access/pending-status')
      return {
        success: true,
        data: response.data
      }
    } catch (error) {
      console.error('Failed to check pending requests:', error)
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to check pending requests',
        status: error.response?.status
      }
    }
  },

  /**
   * Get all Wellsoft modules from database
   * @returns {Promise<Object>} Response with Wellsoft modules data
   */
  async getWellsoftModules() {
    try {
      console.log('🔄 UserCombinedAccessService: Fetching Wellsoft modules...')

      const response = await apiClient.get('/wellsoft-modules')

      if (response.data && response.data.success) {
        console.log(
          '✅ UserCombinedAccessService: Wellsoft modules loaded:',
          response.data.data?.length || 0
        )
        return {
          success: true,
          data: response.data.data
        }
      } else {
        throw new Error(response.data?.message || 'Failed to load Wellsoft modules')
      }
    } catch (error) {
      console.error('❌ UserCombinedAccessService: Error fetching Wellsoft modules:', error)
      return {
        success: false,
        error: error.response?.data?.message || error.message || 'Failed to load Wellsoft modules',
        data: []
      }
    }
  },

  /**
   * Get all Jeeva modules from database
   * @returns {Promise<Object>} Response with Jeeva modules data
   */
  async getJeevaModules() {
    try {
      console.log('🔄 UserCombinedAccessService: Fetching Jeeva modules...')

      const response = await apiClient.get('/jeeva-modules')

      if (response.data && response.data.success) {
        console.log(
          '✅ UserCombinedAccessService: Jeeva modules loaded:',
          response.data.data?.length || 0
        )
        return {
          success: true,
          data: response.data.data
        }
      } else {
        throw new Error(response.data?.message || 'Failed to load Jeeva modules')
      }
    } catch (error) {
      console.error('❌ UserCombinedAccessService: Error fetching Jeeva modules:', error)
      return {
        success: false,
        error: error.response?.data?.message || error.message || 'Failed to load Jeeva modules',
        data: []
      }
    }
  }
}

export default userCombinedAccessService
