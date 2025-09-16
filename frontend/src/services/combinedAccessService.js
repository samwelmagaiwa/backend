import apiClient from '../utils/apiClient'

/**
 * Combined Access Service for HOD request management
 * Handles all operations related to combined access requests (Jeeva, Wellsoft, Internet)
 */
class CombinedAccessService {
  /**
   * Get all combined access requests for HOD approval
   * @param {Object} filters - Search and filter parameters
   * @returns {Promise<Object>} Response with requests data
   */
  async getHodRequests(filters = {}) {
    try {
      console.log('🔄 Fetching combined access requests for HOD approval...', filters)

      const params = new URLSearchParams()
      
      if (filters.search) params.append('search', filters.search)
      if (filters.status) params.append('status', filters.status)
      if (filters.department) params.append('department', filters.department)
      if (filters.per_page) params.append('per_page', filters.per_page)
      if (filters.page) params.append('page', filters.page)

      const response = await apiClient.get(`/hod/combined-access-requests?${params.toString()}`)

      if (response.data && response.data.success) {
        console.log('✅ HOD requests retrieved successfully:', response.data.data?.length || 0, 'requests')
        return {
          success: true,
          data: response.data
        }
      } else {
        throw new Error(response.data?.message || 'Failed to retrieve HOD requests')
      }
    } catch (error) {
      console.error('❌ Error fetching HOD requests:', error)
      return {
        success: false,
        error: error.response?.data?.message || error.message || 'Failed to retrieve HOD requests',
        data: null
      }
    }
  }

  /**
   * Get a specific combined access request by ID
   * @param {number} requestId - The request ID
   * @returns {Promise<Object>} Response with request data
   */
  async getRequestById(requestId) {
    try {
      console.log('🔄 Fetching combined access request:', requestId)

      const response = await apiClient.get(`/hod/combined-access-requests/${requestId}`)

      if (response.data && response.data.success) {
        console.log('✅ Request retrieved successfully:', requestId)
        return {
          success: true,
          data: response.data.data
        }
      } else {
        throw new Error(response.data?.message || 'Request not found')
      }
    } catch (error) {
      console.error('❌ Error fetching request:', error)
      return {
        success: false,
        error: error.response?.data?.message || error.message || 'Failed to retrieve request',
        data: null
      }
    }
  }

  /**
   * Update HOD approval status for a combined access request
   * @param {number} requestId - The request ID
   * @param {Object} approvalData - Approval data (status, comments, etc.)
   * @returns {Promise<Object>} Response with update result
   */
  async updateHodApproval(requestId, approvalData) {
    try {
      console.log('🔄 Updating HOD approval for request:', requestId, approvalData)

      const formData = new FormData()
      
      // Add approval data
      formData.append('hod_status', approvalData.status) // 'approved' or 'rejected'
      formData.append('hod_comments', approvalData.comments || '')
      formData.append('hod_name', approvalData.hodName || '')
      formData.append('hod_approved_at', new Date().toISOString())

      // Add HOD signature if provided
      if (approvalData.hodSignature) {
        formData.append('hod_signature', approvalData.hodSignature)
      }

      // Add module-specific data if provided
      if (approvalData.moduleData) {
        formData.append('module_data', JSON.stringify(approvalData.moduleData))
      }

      // Add access rights if provided
      if (approvalData.accessRights) {
        formData.append('access_rights', JSON.stringify(approvalData.accessRights))
      }

      const response = await apiClient.post(`/hod/combined-access-requests/${requestId}/approve`, formData)

      if (response.data && response.data.success) {
        console.log('✅ HOD approval updated successfully:', requestId)
        return {
          success: true,
          data: response.data.data,
          message: 'Request approval updated successfully'
        }
      } else {
        throw new Error(response.data?.message || 'Failed to update approval')
      }
    } catch (error) {
      console.error('❌ Error updating HOD approval:', error)
      return {
        success: false,
        error: error.response?.data?.message || error.message || 'Failed to update approval',
        errors: error.response?.data?.errors || null
      }
    }
  }

  /**
   * Cancel a combined access request
   * @param {number} requestId - The request ID
   * @param {string} reason - Cancellation reason
   * @returns {Promise<Object>} Response with cancellation result
   */
  async cancelRequest(requestId, reason) {
    try {
      console.log('🔄 Cancelling combined access request:', requestId)

      const response = await apiClient.post(`/hod/combined-access-requests/${requestId}/cancel`, {
        reason: reason || 'Cancelled by HOD',
        cancelled_at: new Date().toISOString()
      })

      if (response.data && response.data.success) {
        console.log('✅ Request cancelled successfully:', requestId)
        return {
          success: true,
          data: response.data.data,
          message: 'Request cancelled successfully'
        }
      } else {
        throw new Error(response.data?.message || 'Failed to cancel request')
      }
    } catch (error) {
      console.error('❌ Error cancelling request:', error)
      return {
        success: false,
        error: error.response?.data?.message || error.message || 'Failed to cancel request'
      }
    }
  }

  /**
   * Get statistics for HOD dashboard
   * @returns {Promise<Object>} Response with statistics
   */
  async getHodStatistics() {
    try {
      console.log('🔄 Fetching HOD statistics...')

      const response = await apiClient.get('/hod/combined-access-requests/statistics')

      if (response.data && response.data.success) {
        console.log('✅ HOD statistics retrieved successfully')
        return {
          success: true,
          data: response.data.data
        }
      } else {
        throw new Error(response.data?.message || 'Failed to retrieve statistics')
      }
    } catch (error) {
      console.error('❌ Error fetching HOD statistics:', error)
      return {
        success: false,
        error: error.response?.data?.message || error.message || 'Failed to retrieve statistics',
        data: {
          pendingHod: 0,
          hodApproved: 0,
          hodRejected: 0,
          total: 0
        }
      }
    }
  }

  /**
   * Export HOD requests to Excel/CSV
   * @param {Object} filters - Export filters
   * @param {string} format - Export format ('excel' or 'csv')
   * @returns {Promise<Object>} Response with export result
   */
  async exportHodRequests(filters = {}, format = 'excel') {
    try {
      console.log('🔄 Exporting HOD requests...', { filters, format })

      const params = new URLSearchParams()
      
      if (filters.search) params.append('search', filters.search)
      if (filters.status) params.append('status', filters.status)
      if (filters.department) params.append('department', filters.department)
      if (filters.date_from) params.append('date_from', filters.date_from)
      if (filters.date_to) params.append('date_to', filters.date_to)
      
      params.append('format', format)

      const response = await apiClient.get(`/hod/combined-access-requests/export?${params.toString()}`, {
        responseType: 'blob'
      })

      console.log('✅ Export completed successfully')
      return {
        success: true,
        data: response.data,
        filename: this.getFilenameFromHeaders(response.headers) || `hod_requests_${Date.now()}.${format === 'excel' ? 'xlsx' : 'csv'}`
      }
    } catch (error) {
      console.error('❌ Error exporting HOD requests:', error)
      return {
        success: false,
        error: error.response?.data?.message || error.message || 'Failed to export requests'
      }
    }
  }

  /**
   * Search requests by multiple criteria
   * @param {Object} criteria - Search criteria
   * @returns {Promise<Object>} Response with search results
   */
  async searchRequests(criteria) {
    try {
      console.log('🔄 Searching requests with criteria:', criteria)

      const response = await apiClient.post('/hod/combined-access-requests/search', criteria)

      if (response.data && response.data.success) {
        console.log('✅ Search completed successfully:', response.data.data?.length || 0, 'results')
        return {
          success: true,
          data: response.data
        }
      } else {
        throw new Error(response.data?.message || 'Search failed')
      }
    } catch (error) {
      console.error('❌ Error searching requests:', error)
      return {
        success: false,
        error: error.response?.data?.message || error.message || 'Search failed',
        data: null
      }
    }
  }

  /**
   * Bulk approve multiple requests
   * @param {Array} requestIds - Array of request IDs
   * @param {Object} approvalData - Bulk approval data
   * @returns {Promise<Object>} Response with bulk approval result
   */
  async bulkApprove(requestIds, approvalData) {
    try {
      console.log('🔄 Bulk approving requests:', requestIds.length)

      const response = await apiClient.post('/hod/combined-access-requests/bulk-approve', {
        request_ids: requestIds,
        hod_comments: approvalData.comments || '',
        hod_name: approvalData.hodName || '',
        approved_at: new Date().toISOString()
      })

      if (response.data && response.data.success) {
        console.log('✅ Bulk approval completed successfully')
        return {
          success: true,
          data: response.data.data,
          message: `${response.data.data.approved_count || 0} requests approved successfully`
        }
      } else {
        throw new Error(response.data?.message || 'Bulk approval failed')
      }
    } catch (error) {
      console.error('❌ Error in bulk approval:', error)
      return {
        success: false,
        error: error.response?.data?.message || error.message || 'Bulk approval failed',
        data: null
      }
    }
  }

  /**
   * Get request history/audit trail
   * @param {number} requestId - The request ID
   * @returns {Promise<Object>} Response with request history
   */
  async getRequestHistory(requestId) {
    try {
      console.log('🔄 Fetching request history:', requestId)

      const response = await apiClient.get(`/hod/combined-access-requests/${requestId}/history`)

      if (response.data && response.data.success) {
        console.log('✅ Request history retrieved successfully')
        return {
          success: true,
          data: response.data.data
        }
      } else {
        throw new Error(response.data?.message || 'Failed to retrieve request history')
      }
    } catch (error) {
      console.error('❌ Error fetching request history:', error)
      return {
        success: false,
        error: error.response?.data?.message || error.message || 'Failed to retrieve request history',
        data: []
      }
    }
  }

  /**
   * Helper method to extract filename from response headers
   * @param {Object} headers - Response headers
   * @returns {string|null} Extracted filename
   */
  getFilenameFromHeaders(headers) {
    try {
      const contentDisposition = headers['content-disposition'] || headers['Content-Disposition']
      if (contentDisposition) {
        const filenameMatch = contentDisposition.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/)
        if (filenameMatch && filenameMatch[1]) {
          return filenameMatch[1].replace(/['"]/g, '')
        }
      }
      return null
    } catch (error) {
      console.warn('Could not extract filename from headers:', error)
      return null
    }
  }

  /**
   * Validate request data before submission
   * @param {Object} requestData - Request data to validate
   * @returns {Object} Validation result
   */
  validateRequestData(requestData) {
    const errors = []

    if (!requestData.staff_name || requestData.staff_name.trim() === '') {
      errors.push('Staff name is required')
    }

    if (!requestData.pf_number || requestData.pf_number.trim() === '') {
      errors.push('PF Number is required')
    }

    if (!requestData.department || requestData.department.trim() === '') {
      errors.push('Department is required')
    }

    if (!requestData.phone || requestData.phone.trim() === '') {
      errors.push('Phone number is required')
    }

    if (!requestData.services || requestData.services.length === 0) {
      errors.push('At least one service must be selected')
    }

    return {
      isValid: errors.length === 0,
      errors: errors
    }
  }

  /**
   * Format request data for display
   * @param {Object} request - Raw request data
   * @returns {Object} Formatted request data
   */
  formatRequestForDisplay(request) {
    return {
      ...request,
      formattedDate: this.formatDate(request.created_at || request.submission_date),
      formattedTime: this.formatTime(request.created_at || request.submission_date),
      statusText: this.getStatusText(request.hod_status || request.status),
      servicesText: this.getServicesText(request.services || request.request_types),
      requestIdDisplay: request.request_id || `REQ-${request.id.toString().padStart(6, '0')}`
    }
  }

  /**
   * Format date string
   * @param {string} dateString - Date string to format
   * @returns {string} Formatted date
   */
  formatDate(dateString) {
    if (!dateString) return 'N/A'
    const date = new Date(dateString)
    return date.toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric'
    })
  }

  /**
   * Format time string
   * @param {string} dateString - Date string to format
   * @returns {string} Formatted time
   */
  formatTime(dateString) {
    if (!dateString) return 'N/A'
    const date = new Date(dateString)
    return date.toLocaleTimeString('en-US', {
      hour: '2-digit',
      minute: '2-digit'
    })
  }

  /**
   * Get status text
   * @param {string} status - Status code
   * @returns {string} Human-readable status text
   */
  getStatusText(status) {
    const statusMap = {
      'pending_hod': 'Pending HOD Approval',
      'hod_approved': 'HOD Approved',
      'hod_rejected': 'HOD Rejected',
      'cancelled': 'Cancelled',
      'completed': 'Completed'
    }
    return statusMap[status] || 'Unknown Status'
  }

  /**
   * Get services text
   * @param {Array} services - Services array
   * @returns {string} Comma-separated services text
   */
  getServicesText(services) {
    if (!services || services.length === 0) return 'No services'
    
    const serviceMap = {
      'jeeva': 'Jeeva',
      'jeeva_access': 'Jeeva',
      'wellsoft': 'Wellsoft',
      'internet': 'Internet',
      'internet_access_request': 'Internet'
    }
    
    return services.map(service => serviceMap[service] || service).join(', ')
  }
}

export default new CombinedAccessService()
