import apiClient from '../utils/apiClient'

/**
 * Divisional Access Service for Divisional Director request management
 * Handles all operations related to HOD-approved combined access requests (Jeeva, Wellsoft, Internet)
 * that are ready for divisional director approval
 */
class DivisionalAccessService {
  /**
   * Get all HOD-approved combined access requests for Divisional Director approval
   * @param {Object} filters - Search and filter parameters
   * @returns {Promise<Object>} Response with requests data
   */
  async getDivisionalRequests(filters = {}) {
    try {
      console.log(
        '🔄 Fetching HOD-approved combined access requests for Divisional Director approval...',
        filters
      )

      const params = new URLSearchParams()

      if (filters.search) params.append('search', filters.search)
      if (filters.status) params.append('status', filters.status)
      if (filters.department) params.append('department', filters.department)
      if (filters.per_page) params.append('per_page', filters.per_page)
      if (filters.page) params.append('page', filters.page)

      const response = await apiClient.get(
        `/divisional/combined-access-requests?${params.toString()}`
      )

      if (response.data && response.data.success) {
        console.log(
          '✅ Divisional requests retrieved successfully:',
          response.data.data?.length || 0,
          'requests'
        )
        return {
          success: true,
          data: response.data
        }
      } else {
        throw new Error(response.data?.message || 'Failed to retrieve Divisional requests')
      }
    } catch (error) {
      console.error('❌ Error fetching Divisional requests:', error)
      return {
        success: false,
        error:
          error.response?.data?.message ||
          error.message ||
          'Failed to retrieve Divisional requests',
        data: null
      }
    }
  }

  /**
   * Get a specific combined access request by ID for Divisional Director
   * @param {number} requestId - The request ID
   * @returns {Promise<Object>} Response with request data
   */
  async getRequestById(requestId) {
    try {
      console.log('🔄 Fetching combined access request for Divisional Director:', requestId)

      const response = await apiClient.get(`/divisional/combined-access-requests/${requestId}`)

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
   * Update Divisional Director approval status for a combined access request
   * @param {number} requestId - The request ID
   * @param {Object} approvalData - Approval data (status, comments, etc.)
   * @returns {Promise<Object>} Response with update result
   */
  async updateDivisionalApproval(requestId, approvalData) {
    try {
      console.log('🔄 Updating Divisional Director approval for request:', requestId, approvalData)

      const formData = new FormData()

      // Add approval data
      formData.append('divisional_status', approvalData.status) // 'approved' or 'rejected'
      formData.append('divisional_comments', approvalData.comments || '')
      formData.append('divisional_name', approvalData.divisionalName || '')
      formData.append('divisional_approved_at', new Date().toISOString())

      // Add Divisional Director signature if provided
      if (approvalData.divisionalSignature) {
        formData.append('divisional_signature', approvalData.divisionalSignature)
      }

      // Add module-specific data if provided
      if (approvalData.moduleData) {
        formData.append('module_data', JSON.stringify(approvalData.moduleData))
      }

      // Add access rights if provided
      if (approvalData.accessRights) {
        formData.append('access_rights', JSON.stringify(approvalData.accessRights))
      }

      const response = await apiClient.post(
        `/divisional/combined-access-requests/${requestId}/approve`,
        formData
      )

      if (response.data && response.data.success) {
        console.log('✅ Divisional Director approval updated successfully:', requestId)
        return {
          success: true,
          data: response.data.data,
          message: 'Request approval updated successfully'
        }
      } else {
        throw new Error(response.data?.message || 'Failed to update approval')
      }
    } catch (error) {
      console.error('❌ Error updating Divisional Director approval:', error)
      return {
        success: false,
        error: error.response?.data?.message || error.message || 'Failed to update approval',
        errors: error.response?.data?.errors || null
      }
    }
  }

  /**
   * Cancel a combined access request (Divisional Director action)
   * @param {number} requestId - The request ID
   * @param {string} reason - Cancellation reason
   * @returns {Promise<Object>} Response with cancellation result
   */
  async cancelRequest(requestId, reason) {
    try {
      console.log('🔄 Cancelling combined access request (Divisional Director):', requestId)

      const response = await apiClient.post(
        `/divisional/combined-access-requests/${requestId}/cancel`,
        {
          reason: reason || 'Cancelled by Divisional Director',
          cancelled_at: new Date().toISOString()
        }
      )

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
   * Get statistics for Divisional Director dashboard
   * @returns {Promise<Object>} Response with statistics
   */
  async getDivisionalStatistics() {
    try {
      console.log('🔄 Fetching Divisional Director statistics...')

      const response = await apiClient.get('/divisional/combined-access-requests/statistics')

      if (response.data && response.data.success) {
        console.log('✅ Divisional Director statistics retrieved successfully')
        return {
          success: true,
          data: response.data.data
        }
      } else {
        throw new Error(response.data?.message || 'Failed to retrieve statistics')
      }
    } catch (error) {
      console.error('❌ Error fetching Divisional Director statistics:', error)
      return {
        success: false,
        error: error.response?.data?.message || error.message || 'Failed to retrieve statistics',
        data: {
          pendingDivisional: 0,
          divisionalApproved: 0,
          divisionalRejected: 0,
          total: 0
        }
      }
    }
  }

  /**
   * Export Divisional Director requests to Excel/CSV
   * @param {Object} filters - Export filters
   * @param {string} format - Export format ('excel' or 'csv')
   * @returns {Promise<Object>} Response with export result
   */
  async exportDivisionalRequests(filters = {}, format = 'excel') {
    try {
      console.log('🔄 Exporting Divisional Director requests...', { filters, format })

      const params = new URLSearchParams()

      if (filters.search) params.append('search', filters.search)
      if (filters.status) params.append('status', filters.status)
      if (filters.department) params.append('department', filters.department)
      if (filters.date_from) params.append('date_from', filters.date_from)
      if (filters.date_to) params.append('date_to', filters.date_to)

      params.append('format', format)

      const response = await apiClient.get(
        `/divisional/combined-access-requests/export?${params.toString()}`,
        {
          responseType: 'blob'
        }
      )

      console.log('✅ Export completed successfully')
      return {
        success: true,
        data: response.data,
        filename:
          this.getFilenameFromHeaders(response.headers) ||
          `divisional_requests_${Date.now()}.${format === 'excel' ? 'xlsx' : 'csv'}`
      }
    } catch (error) {
      console.error('❌ Error exporting Divisional Director requests:', error)
      return {
        success: false,
        error: error.response?.data?.message || error.message || 'Failed to export requests'
      }
    }
  }

  /**
   * Search requests by multiple criteria (Divisional Director)
   * @param {Object} criteria - Search criteria
   * @returns {Promise<Object>} Response with search results
   */
  async searchRequests(criteria) {
    try {
      console.log('🔄 Searching Divisional Director requests with criteria:', criteria)

      const response = await apiClient.post('/divisional/combined-access-requests/search', criteria)

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
   * Bulk approve multiple requests (Divisional Director)
   * @param {Array} requestIds - Array of request IDs
   * @param {Object} approvalData - Bulk approval data
   * @returns {Promise<Object>} Response with bulk approval result
   */
  async bulkApprove(requestIds, approvalData) {
    try {
      console.log('🔄 Bulk approving requests (Divisional Director):', requestIds.length)

      const response = await apiClient.post('/divisional/combined-access-requests/bulk-approve', {
        request_ids: requestIds,
        divisional_comments: approvalData.comments || '',
        divisional_name: approvalData.divisionalName || '',
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
   * Get request history/audit trail (Divisional Director view)
   * @param {number} requestId - The request ID
   * @returns {Promise<Object>} Response with request history
   */
  async getRequestHistory(requestId) {
    try {
      console.log('🔄 Fetching request history (Divisional Director):', requestId)

      const response = await apiClient.get(
        `/divisional/combined-access-requests/${requestId}/history`
      )

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
        error:
          error.response?.data?.message || error.message || 'Failed to retrieve request history',
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
        const filenameMatch = contentDisposition.match(/filename[^;=\n]*=((['\"]).*?\2|[^;\n]*)/)
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
   * Validate request data before submission (Divisional Director)
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
   * Format request data for display (Divisional Director view)
   * @param {Object} request - Raw request data
   * @returns {Object} Formatted request data
   */
  formatRequestForDisplay(request) {
    return {
      ...request,
      formattedHodApprovalDate: this.formatDate(
        request.hod_approved_at || request.hod_approval_date
      ),
      formattedHodApprovalTime: this.formatTime(
        request.hod_approved_at || request.hod_approval_date
      ),
      statusText: this.getStatusText(request.divisional_status || request.status),
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
   * Get status text (Divisional Director context)
   * @param {string} status - Status code
   * @returns {string} Human-readable status text
   */
  getStatusText(status) {
    const statusMap = {
      hod_approved: 'Pending Divisional Approval',
      divisional_approved: 'Divisional Approved',
      divisional_rejected: 'Divisional Rejected',
      cancelled: 'Cancelled',
      completed: 'Completed'
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
      jeeva: 'Jeeva',
      jeeva_access: 'Jeeva',
      wellsoft: 'Wellsoft',
      internet: 'Internet',
      internet_access_request: 'Internet'
    }

    return services.map((service) => serviceMap[service] || service).join(', ')
  }
}

export default new DivisionalAccessService()
