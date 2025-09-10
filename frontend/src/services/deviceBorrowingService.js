/**
 * Device Borrowing Request Service
 *
 * Handles ICT approval of device borrowing requests from booking_service table
 */

import apiClient from '../utils/apiClient'

export const deviceBorrowingService = {
  /**
   * Get all device borrowing requests for ICT approval (pending, approved, rejected)
   * @param {Object} params - Query parameters (search, device_type, department, ict_status, etc.)
   * @returns {Promise<Object>} - API response with paginated requests
   */
  async getAllRequests(params = {}) {
    try {
      const response = await apiClient.get('/booking-service/ict-approval-requests', {
        params: {
          ...params,
          per_page: params.per_page || 50
        }
      })

      const requests = response.data.data?.data || response.data.data || []
      const transformedRequests = Array.isArray(requests)
        ? requests.map((request) => this.transformRequest(request))
        : []

      return {
        success: true,
        data: {
          data: transformedRequests,
          current_page: response.data.data?.current_page || 1,
          last_page: response.data.data?.last_page || 1,
          per_page: response.data.data?.per_page || 50,
          total: response.data.data?.total || transformedRequests.length
        }
      }
    } catch (error) {
      console.error('Failed to fetch device borrowing requests:', error)
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to fetch requests',
        error: error.message,
        status: error.response?.status
      }
    }
  },

  /**
   * Get device borrowing requests pending ICT approval (backward compatibility)
   * @param {Object} params - Query parameters (search, device_type, department, etc.)
   * @returns {Promise<Object>} - API response with paginated pending requests
   */
  async getPendingRequests(params = {}) {
    return this.getAllRequests({
      ...params,
      ict_status: 'pending'
    })
  },

  /**
   * Get detailed information for a specific device borrowing request
   * @param {number} requestId - Request ID
   * @returns {Promise<Object>} - API response with request details
   */
  async getRequestDetails(requestId) {
    try {
      console.log('🔍 Fetching request details for ID:', requestId)

      // Try ICT approval endpoint first (for ICT officers)
      let response
      try {
        response = await apiClient.get(`/ict-approval/device-requests/${requestId}`)
        console.log('✅ Successfully fetched from ICT approval endpoint')
      } catch (ictError) {
        console.log(
          '⚠️ ICT approval endpoint failed, trying booking service endpoint:',
          ictError.response?.status
        )

        // Fallback to booking service endpoint
        response = await apiClient.get(`/booking-service/bookings/${requestId}`)
        console.log('✅ Successfully fetched from booking service endpoint')
      }

      const request = response.data.data || response.data
      console.log('📄 Raw request data:', request)

      if (!request) {
        throw new Error('No request data received from API')
      }

      const transformedRequest = this.transformRequest(request)
      console.log('🔄 Transformed request data:', transformedRequest)

      return {
        success: true,
        data: transformedRequest
      }
    } catch (error) {
      console.error('❌ Failed to fetch request details:', {
        requestId,
        error: error.message,
        status: error.response?.status,
        statusText: error.response?.statusText,
        responseData: error.response?.data
      })

      return {
        success: false,
        message:
          error.response?.data?.message || error.message || 'Failed to fetch request details',
        error: error.message,
        status: error.response?.status,
        details: {
          requestId,
          endpoint: error.config?.url,
          method: error.config?.method
        }
      }
    }
  },

  /**
   * Approve a device borrowing request (ICT Officer)
   * @param {number} requestId - Request ID
   * @param {string} notes - ICT officer notes (optional)
   * @returns {Promise<Object>} - API response
   */
  async approveRequest(requestId, notes = '') {
    try {
      console.log('✅ Approving request ID:', requestId, 'with notes:', notes)

      // Try ICT approval endpoint first
      let response
      try {
        response = await apiClient.post(`/ict-approval/device-requests/${requestId}/approve`, {
          ict_notes: notes
        })
        console.log('✅ Successfully approved via ICT approval endpoint')
      } catch (ictError) {
        console.log(
          '⚠️ ICT approval endpoint failed, trying booking service endpoint:',
          ictError.response?.status
        )

        // Fallback to booking service endpoint
        response = await apiClient.post(`/booking-service/bookings/${requestId}/ict-approve`, {
          ict_notes: notes
        })
        console.log('✅ Successfully approved via booking service endpoint')
      }

      return {
        success: true,
        data: this.transformRequest(response.data.data || response.data),
        message: 'Device borrowing request approved successfully'
      }
    } catch (error) {
      console.error('❌ Failed to approve request:', {
        requestId,
        error: error.message,
        status: error.response?.status,
        responseData: error.response?.data
      })

      return {
        success: false,
        message: error.response?.data?.message || error.message || 'Failed to approve request',
        errors: error.response?.data?.errors || {},
        status: error.response?.status
      }
    }
  },

  /**
   * Reject a device borrowing request (ICT Officer)
   * @param {number} requestId - Request ID
   * @param {string} notes - ICT officer rejection reason (required)
   * @returns {Promise<Object>} - API response
   */
  async rejectRequest(requestId, notes) {
    try {
      if (!notes || notes.trim() === '') {
        throw new Error('Rejection reason is required')
      }

      console.log('❌ Rejecting request ID:', requestId, 'with notes:', notes)

      // Try ICT approval endpoint first
      let response
      try {
        response = await apiClient.post(`/ict-approval/device-requests/${requestId}/reject`, {
          ict_notes: notes
        })
        console.log('✅ Successfully rejected via ICT approval endpoint')
      } catch (ictError) {
        console.log(
          '⚠️ ICT approval endpoint failed, trying booking service endpoint:',
          ictError.response?.status
        )

        // Fallback to booking service endpoint
        response = await apiClient.post(`/booking-service/bookings/${requestId}/ict-reject`, {
          ict_notes: notes
        })
        console.log('✅ Successfully rejected via booking service endpoint')
      }

      return {
        success: true,
        data: this.transformRequest(response.data.data || response.data),
        message: 'Device borrowing request rejected successfully'
      }
    } catch (error) {
      console.error('❌ Failed to reject request:', {
        requestId,
        error: error.message,
        status: error.response?.status,
        responseData: error.response?.data
      })

      return {
        success: false,
        message: error.response?.data?.message || error.message || 'Failed to reject request',
        errors: error.response?.data?.errors || {},
        status: error.response?.status
      }
    }
  },

  /**
   * Transform raw request data to standardized format
   * @param {Object} request - Raw request data from API
   * @returns {Object} - Transformed request data
   */
  transformRequest(request) {
    if (!request) return null

    return {
      // Core request data
      id: request.id,
      request_id: `REQ-${request.id.toString().padStart(6, '0')}`,

      // Auto-captured user details
      borrower_id: request.user_id || request.borrower_id,
      borrower_name: request.user?.name || request.borrower_name || 'Unknown User',
      borrower_email: request.user?.email || request.borrower_email,
      borrower_phone: request.user?.phone || request.phone_number || request.borrower_phone,
      pf_number: request.user?.pf_number || request.pf_number,

      // Department details
      department_id: request.user?.department_id || request.department_id,
      department:
        request.user?.department?.name ||
        request.departmentInfo?.name ||
        request.department ||
        'Unknown Department',

      // Device details
      device_type: request.device_type,
      custom_device: request.custom_device,
      device_name: this.getDeviceDisplayName(request.device_type, request.custom_device),
      device_inventory_id: request.device_inventory_id,

      // Booking details
      booking_date: request.booking_date,
      return_date: request.return_date,
      return_time: request.return_time,
      return_date_time: request.return_date_time,
      reason: request.reason,

      // Signature details
      signature_path: request.signature_path,
      signature_url: request.signature_url,
      has_signature: !!request.signature_path,

      // Status and approval
      status: request.status || 'pending',
      ict_approve: request.ict_approve || 'pending',
      ict_approved_at: request.ict_approved_at,
      ict_notes: request.ict_notes,
      ict_approved_by: request.ict_approved_by,

      // Return status
      return_status: request.return_status || 'not_yet_returned',

      // Admin approval (separate from ICT approval)
      admin_approved_by: request.approved_by,
      admin_approved_at: request.approved_at,
      admin_notes: request.admin_notes,

      // Timestamps
      created_at: request.created_at,
      updated_at: request.updated_at,

      // Raw data for advanced operations
      _raw: request
    }
  },

  /**
   * Get device display name based on type and custom device
   * @param {string} deviceType - Device type
   * @param {string} customDevice - Custom device name
   * @returns {string} - Display name
   */
  getDeviceDisplayName(deviceType, customDevice) {
    if (deviceType === 'others' && customDevice) {
      return customDevice
    }

    const deviceTypes = {
      projector: 'Projector',
      tv_remote: 'TV Remote',
      hdmi_cable: 'HDMI Cable',
      monitor: 'Monitor',
      cpu: 'CPU',
      keyboard: 'Keyboard',
      pc: 'PC',
      others: 'Others'
    }

    return deviceTypes[deviceType] || deviceType || 'Unknown Device'
  },

  /**
   * Format date string for display
   * @param {string} dateString - ISO date string
   * @returns {string} - Formatted date
   */
  formatDate(dateString) {
    if (!dateString) return 'N/A'

    try {
      return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      })
    } catch (error) {
      return dateString
    }
  },

  /**
   * Format datetime string for display
   * @param {string} dateString - ISO datetime string
   * @returns {string} - Formatted datetime
   */
  formatDateTime(dateString) {
    if (!dateString) return 'N/A'

    try {
      return new Date(dateString).toLocaleString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      })
    } catch (error) {
      return dateString
    }
  },

  /**
   * Get CSS class for status badge
   * @param {string} status - ICT approval status
   * @returns {string} - CSS classes
   */
  getStatusBadgeClass(status) {
    const statusClasses = {
      pending: 'bg-yellow-100 text-yellow-800 border-yellow-200',
      approved: 'bg-green-100 text-green-800 border-green-200',
      rejected: 'bg-red-100 text-red-800 border-red-200'
    }

    return statusClasses[status] || statusClasses.pending
  },

  /**
   * Get icon for status
   * @param {string} status - ICT approval status
   * @returns {string} - Font Awesome icon class
   */
  getStatusIcon(status) {
    const statusIcons = {
      pending: 'fas fa-clock',
      approved: 'fas fa-check-circle',
      rejected: 'fas fa-times-circle'
    }

    return statusIcons[status] || statusIcons.pending
  },

  /**
   * Get text for status
   * @param {string} status - ICT approval status
   * @returns {string} - Status text
   */
  getStatusText(status) {
    const statusTexts = {
      pending: 'Pending',
      approved: 'Approved',
      rejected: 'Rejected'
    }

    return statusTexts[status] || status || 'Unknown'
  },

  /**
   * Get CSS class for return status badge
   * @param {string} returnStatus - Return status
   * @returns {string} - CSS classes
   */
  getReturnStatusBadgeClass(returnStatus) {
    const statusClasses = {
      not_yet_returned: 'bg-blue-100 text-blue-800 border-blue-200',
      returned: 'bg-green-100 text-green-800 border-green-200',
      returned_but_compromised: 'bg-red-100 text-red-800 border-red-200',
      out_of_stock: 'bg-yellow-100 text-yellow-800 border-yellow-200' // Treated as pending
    }

    return statusClasses[returnStatus] || statusClasses.not_yet_returned
  },

  /**
   * Get icon for return status
   * @param {string} returnStatus - Return status
   * @returns {string} - Font Awesome icon class
   */
  getReturnStatusIcon(returnStatus) {
    const statusIcons = {
      not_yet_returned: 'fas fa-hourglass-half',
      returned: 'fas fa-check-circle',
      returned_but_compromised: 'fas fa-exclamation-triangle'
    }

    return statusIcons[returnStatus] || statusIcons.not_yet_returned
  },

  /**
   * Get text for return status
   * @param {string} returnStatus - Return status
   * @returns {string} - Return status text
   */
  getReturnStatusText(returnStatus) {
    const statusTexts = {
      not_yet_returned: 'Not Returned',
      returned: 'Returned',
      returned_but_compromised: 'Compromised'
    }

    return statusTexts[returnStatus] || returnStatus || 'Unknown'
  },

  /**
   * Save device condition assessment when issuing device
   * @param {number} requestId - Request ID
   * @param {Object} assessmentData - Assessment data
   * @returns {Promise<Object>} - API response
   */
  async saveIssuingAssessment(requestId, assessmentData) {
    try {
      console.log('💾 Saving issuing assessment for request ID:', requestId, assessmentData)

      // Try ICT approval endpoint first (recommended approach)
      let response
      try {
        response = await apiClient.post(
          `/ict-approval/device-requests/${requestId}/assessment/issuing`,
          assessmentData
        )
        console.log('✅ Successfully saved via ICT approval endpoint')
      } catch (ictError) {
        console.log(
          '⚠️ ICT approval endpoint failed, trying booking service endpoint:',
          ictError.response?.status
        )

        // Fallback to booking service endpoint
        response = await apiClient.post(
          `/booking-service/bookings/${requestId}/assessment/issuing`,
          assessmentData
        )
        console.log('✅ Successfully saved via booking service endpoint')
      }

      return {
        success: true,
        data: this.transformRequest(response.data.data || response.data),
        message: response.data.message || 'Device issued and condition assessment saved successfully'
      }
    } catch (error) {
      console.error('❌ Failed to save issuing assessment:', {
        requestId,
        error: error.message,
        status: error.response?.status,
        responseData: error.response?.data
      })

      return {
        success: false,
        message: error.response?.data?.message || error.message || 'Failed to save assessment',
        errors: error.response?.data?.errors || {},
        status: error.response?.status
      }
    }
  },

  /**
   * Save device condition assessment when receiving device back
   * @param {number} requestId - Request ID
   * @param {Object} assessmentData - Assessment data
   * @returns {Promise<Object>} - API response
   */
  async saveReceivingAssessment(requestId, assessmentData) {
    try {
      console.log('📥 Saving receiving assessment for request ID:', requestId, assessmentData)

      // Try ICT approval endpoint first (recommended approach)
      let response
      try {
        response = await apiClient.post(
          `/ict-approval/device-requests/${requestId}/assessment/receiving`,
          assessmentData
        )
        console.log('✅ Successfully saved via ICT approval endpoint')
      } catch (ictError) {
        console.log(
          '⚠️ ICT approval endpoint failed, trying booking service endpoint:',
          ictError.response?.status
        )

        // Fallback to booking service endpoint
        response = await apiClient.post(
          `/booking-service/bookings/${requestId}/assessment/receiving`,
          assessmentData
        )
        console.log('✅ Successfully saved via booking service endpoint')
      }

      return {
        success: true,
        data: this.transformRequest(response.data.data || response.data),
        message: response.data.message || 'Device received and assessment completed successfully'
      }
    } catch (error) {
      console.error('❌ Failed to save receiving assessment:', {
        requestId,
        error: error.message,
        status: error.response?.status,
        responseData: error.response?.data
      })

      return {
        success: false,
        message: error.response?.data?.message || error.message || 'Failed to save assessment',
        errors: error.response?.data?.errors || {},
        status: error.response?.status
      }
    }
  }
}

export default deviceBorrowingService
