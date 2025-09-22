import apiClient from '../utils/apiClient'

/**
 * ICT Director Access Service
 * Handles all API calls for ICT Director access request management
 */
const dictAccessService = {
  /**
   * Get all combined access requests for ICT Director approval
   * @param {Object} params - Query parameters
   * @returns {Promise<Object>}
   */
  async getDictRequests(params = {}) {
    try {
      const response = await apiClient.get('/dict/combined-access-requests', { params })
      return {
        success: true,
        data: response.data
      }
    } catch (error) {
      console.error('Error fetching ICT Director requests:', error)
      return {
        success: false,
        error: error.response?.data?.message || error.message || 'Failed to fetch requests'
      }
    }
  },

  /**
   * Get a specific combined access request by ID
   * @param {number} requestId - The request ID
   * @returns {Promise<Object>}
   */
  async getDictRequest(requestId) {
    try {
      const response = await apiClient.get(`/dict/combined-access-requests/${requestId}`)
      return {
        success: true,
        data: response.data
      }
    } catch (error) {
      console.error('Error fetching ICT Director request:', error)
      return {
        success: false,
        error: error.response?.data?.message || error.message || 'Failed to fetch request'
      }
    }
  },

  /**
   * Update ICT Director approval for a combined access request
   * @param {number} requestId - The request ID
   * @param {Object} approvalData - The approval data
   * @returns {Promise<Object>}
   */
  async updateDictApproval(requestId, approvalData) {
    try {
      const response = await apiClient.post(
        `/dict/combined-access-requests/${requestId}/approve`,
        approvalData
      )
      return {
        success: true,
        data: response.data
      }
    } catch (error) {
      console.error('Error updating ICT Director approval:', error)
      return {
        success: false,
        error: error.response?.data?.message || error.message || 'Failed to update approval'
      }
    }
  },

  /**
   * Cancel a combined access request
   * @param {number} requestId - The request ID
   * @param {string} reason - Cancellation reason
   * @returns {Promise<Object>}
   */
  async cancelRequest(requestId, reason) {
    try {
      const response = await apiClient.post(`/dict/combined-access-requests/${requestId}/cancel`, {
        reason
      })
      return {
        success: true,
        data: response.data
      }
    } catch (error) {
      console.error('Error cancelling request:', error)
      return {
        success: false,
        error: error.response?.data?.message || error.message || 'Failed to cancel request'
      }
    }
  },

  /**
   * Get statistics for ICT Director dashboard
   * @returns {Promise<Object>}
   */
  async getDictStatistics() {
    try {
      const response = await apiClient.get('/dict/combined-access-requests/statistics')
      return {
        success: true,
        data: response.data.data || response.data
      }
    } catch (error) {
      console.error('Error fetching ICT Director statistics:', error)
      return {
        success: false,
        error: error.response?.data?.message || error.message || 'Failed to fetch statistics'
      }
    }
  },

  /**
   * Approve a combined access request as ICT Director
   * @param {number} requestId - The request ID
   * @param {Object} data - Approval data including comments
   * @returns {Promise<Object>}
   */
  async approveRequest(requestId, data = {}) {
    return await this.updateDictApproval(requestId, {
      dict_status: 'approved',
      dict_comments: data.comments || '',
      dict_name: data.name || ''
    })
  },

  /**
   * Reject a combined access request as ICT Director
   * @param {number} requestId - The request ID
   * @param {Object} data - Rejection data including comments
   * @returns {Promise<Object>}
   */
  async rejectRequest(requestId, data = {}) {
    return await this.updateDictApproval(requestId, {
      dict_status: 'rejected',
      dict_comments: data.comments || '',
      dict_name: data.name || ''
    })
  },

  /**
   * Get dict recommendations for divisional directors
   * Shows requests that have ict_director_comments populated (rejections, approvals, recommendations)
   * @param {Object} params - Query parameters
   * @returns {Promise<Object>}
   */
  async getDictRecommendations(params = {}) {
    try {
      // Primary endpoint for dict recommendations
      const response = await apiClient.get('/divisional/dict-recommendations', { params })
      return {
        success: true,
        data: response.data
      }
    } catch (error) {
      console.error('Error fetching dict recommendations:', error)

      // Fallback: try to get requests with ict_director_comments from combined requests
      try {
        console.log('Trying fallback: combined-access-requests with ict_director_comments filter')
        const fallbackResponse = await apiClient.get('/divisional/combined-access-requests', {
          params: {
            ...params,
            has_ict_director_comments: true // Filter for requests with comments
          }
        })

        console.log('Fallback response structure:', {
          hasData: !!fallbackResponse.data,
          dataType: typeof fallbackResponse.data,
          hasDataData: !!fallbackResponse.data?.data,
          dataDataType: typeof fallbackResponse.data?.data,
          isDataArray: Array.isArray(fallbackResponse.data?.data)
        })

        // Filter results to only include requests with actual ict_director_comments
        // Ensure we have a valid array to work with
        const responseData = fallbackResponse.data?.data || []
        const dataArray = Array.isArray(responseData) ? responseData : []

        const filteredData = {
          ...fallbackResponse.data,
          data: dataArray.filter(
            (request) =>
              request.ict_director_comments && request.ict_director_comments.trim() !== ''
          )
        }

        return {
          success: true,
          data: filteredData
        }
      } catch (fallbackError) {
        console.error('Fallback also failed:', fallbackError)
        return {
          success: false,
          error:
            error.response?.data?.message || error.message || 'Failed to fetch dict recommendations'
        }
      }
    }
  },

  /**
   * Submit divisional director response to ICT Director
   * @param {number} requestId - The request ID
   * @param {Object} responseData - Response data
   * @returns {Promise<Object>}
   */
  async submitDivisionalResponse(requestId, responseData) {
    try {
      const response = await apiClient.post(
        `/divisional/respond-to-dict/${requestId}`,
        responseData
      )
      return {
        success: true,
        data: response.data
      }
    } catch (error) {
      console.error('Error submitting divisional response:', error)
      return {
        success: false,
        error: error.response?.data?.message || error.message || 'Failed to submit response'
      }
    }
  }
}

export default dictAccessService
