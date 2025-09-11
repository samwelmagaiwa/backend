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
      // Try ICT approval endpoint first (more reliable)
      let response
      try {
        response = await apiClient.get('/ict-approval/device-requests', {
          params: {
            ...params,
            per_page: params.per_page || 50
          }
        })
        console.log('Successfully fetched from ICT approval endpoint')
      } catch (ictError) {
        console.log(
          'ICT approval endpoint failed, trying booking service endpoint:',
          ictError.response?.status,
          ictError.response?.data?.message
        )

        // Fallback to booking service endpoint
        response = await apiClient.get('/booking-service/ict-approval-requests', {
          params: {
            ...params,
            per_page: params.per_page || 50
          }
        })
        console.log('Successfully fetched from booking service endpoint')
      }

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
      let sourceEndpoint = ''
      try {
        response = await apiClient.get(`/ict-approval/device-requests/${requestId}`)
        sourceEndpoint = 'ict-approval/device-requests'
        console.log('✅ Successfully fetched from ICT approval endpoint')
      } catch (ictError) {
        console.log(
          '⚠️ ICT approval endpoint failed, trying booking service endpoint:',
          ictError.response?.status
        )

        // Fallback to booking service endpoint
        response = await apiClient.get(`/booking-service/bookings/${requestId}`)
        sourceEndpoint = 'booking-service/bookings'
        console.log('✅ Successfully fetched from booking service endpoint')
      }

      const request = response.data.data || response.data
      // Add source endpoint information for better type detection
      request._endpoint = sourceEndpoint
      request._source = sourceEndpoint.includes('booking') ? 'booking_service' : 'ict_approval'
      
      console.log('📄 Raw request data:', request)
      console.log('🔍 Source endpoint:', sourceEndpoint)
      console.log('🔍 Raw reason field:', request.reason)
      console.log('🔍 Raw purpose field:', request.purpose)
      console.log('🔍 Raw phone fields:', {
        phone_number: request.phone_number,
        borrower_phone: request.borrower_phone,
        user_phone: request.user?.phone,
        phoneNumber: request.phoneNumber
      })
      
      // Check request type before transformation
      const isBooking = this.isBookingRequest(request)
      console.log('🔍 Request type analysis:', {
        requestId: request.id,
        isBookingRequest: isBooking,
        detectedType: isBooking ? 'booking' : 'access'
      })

      if (!request) {
        throw new Error('No request data received from API')
      }

      const transformedRequest = this.transformRequest(request)
      console.log('🔄 Transformed request data:', transformedRequest)
      console.log('🔍 Transformed reason field:', transformedRequest.reason)
      console.log('🔍 Transformed purpose field:', transformedRequest.purpose)
      console.log('🔍 Transformed phone field:', transformedRequest.borrower_phone)
      console.log('🔍 Final request type:', transformedRequest.request_type)

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
   * Delete a device borrowing request (ICT Officer)
   * @param {number} requestId - Request ID
   * @returns {Promise<Object>} - API response
   */
  async deleteRequest(requestId) {
    try {
      console.log('🗑️ Deleting request ID:', requestId)

      // Try ICT approval endpoint first
      try {
        await apiClient.delete(`/ict-approval/device-requests/${requestId}`)
        console.log('✅ Successfully deleted via ICT approval endpoint')
      } catch (ictError) {
        console.log(
          '⚠️ ICT approval endpoint failed, trying booking service endpoint:',
          ictError.response?.status
        )

        // Fallback to booking service endpoint
        await apiClient.delete(`/booking-service/bookings/${requestId}`)
        console.log('✅ Successfully deleted via booking service endpoint')
      }

      return {
        success: true,
        message: 'Device borrowing request deleted successfully'
      }
    } catch (error) {
      console.error('❌ Failed to delete request:', {
        requestId,
        error: error.message,
        status: error.response?.status,
        responseData: error.response?.data
      })

      return {
        success: false,
        message: error.response?.data?.message || error.message || 'Failed to delete request',
        errors: error.response?.data?.errors || {},
        status: error.response?.status
      }
    }
  },

  /**
   * Bulk delete device borrowing requests (ICT Officer)
   * @param {Array<number>} requestIds - Array of Request IDs
   * @returns {Promise<Object>} - API response
   */
  async bulkDeleteRequests(requestIds) {
    try {
      console.log('🗑️ Bulk deleting request IDs:', requestIds)

      // Try ICT approval endpoint first
      let response
      try {
        response = await apiClient.post('/ict-approval/device-requests/bulk-delete', {
          request_ids: requestIds
        })
        console.log('✅ Successfully bulk deleted via ICT approval endpoint')
      } catch (ictError) {
        console.log(
          '⚠️ ICT approval endpoint failed, trying booking service endpoint:',
          ictError.response?.status
        )

        // Fallback to booking service endpoint (if implemented)
        response = await apiClient.post('/booking-service/bookings/bulk-delete', {
          request_ids: requestIds
        })
        console.log('✅ Successfully bulk deleted via booking service endpoint')
      }

      return {
        success: true,
        data: response.data.data || {},
        message: response.data.message || 'Requests deleted successfully'
      }
    } catch (error) {
      console.error('❌ Failed to bulk delete requests:', {
        requestIds,
        error: error.message,
        status: error.response?.status,
        responseData: error.response?.data
      })

      return {
        success: false,
        message: error.response?.data?.message || error.message || 'Failed to delete requests',
        errors: error.response?.data?.errors || {},
        status: error.response?.status
      }
    }
  },

  /**
   * Check if a request can be deleted based on its status
   * @param {Object} request - Request object
   * @returns {boolean} - Whether the request can be deleted
   */
  canDeleteRequest(request) {
    // Allow deletion of: pending requests, rejected requests, and approved requests that have been returned
    if (request.ict_approve === 'pending' || request.status === 'pending') {
      return true
    }
    if (request.ict_approve === 'rejected') {
      return true
    }
    if (
      request.ict_approve === 'approved' &&
      (request.return_status === 'returned' || request.return_status === 'returned_but_compromised')
    ) {
      return true
    }
    return false
  },

  /**
   * Check if the request is a booking request (from booking_service table)
   * @param {Object} request - Raw request data from API
   * @returns {boolean} - True if it's a booking request
   */
  isBookingRequest(request) {
    // Check for booking-specific fields that indicate this is from booking_service table
    const bookingIndicators = [
      'phone_number', // booking_service uses phone_number field
      'device_inventory_id', // booking_service has device inventory links
      'return_time', // booking_service has return_time field
      'ict_approve', // booking_service has ICT approval workflow
      'device_condition_issuing', // booking_service has device assessments
      'booking_date' // booking_service has booking_date field
    ]
    
    // Check if request has multiple booking-specific indicators
    const hasBookingFields = bookingIndicators.filter(field => 
      request.hasOwnProperty(field) && request[field] !== null && request[field] !== undefined
    ).length
    
    // Also check the endpoint or source if available
    const isFromBookingEndpoint = request._source === 'booking_service' || 
                                 request._endpoint?.includes('booking') ||
                                 request._endpoint?.includes('device-requests')
    
    console.log('🔍 Request type detection:', {
      requestId: request.id,
      hasBookingFields: hasBookingFields,
      bookingFieldsFound: bookingIndicators.filter(field => request.hasOwnProperty(field)),
      isFromBookingEndpoint,
      isBookingRequest: hasBookingFields >= 3 || isFromBookingEndpoint
    })
    
    // Consider it a booking request if it has 3+ booking indicators or comes from booking endpoint
    return hasBookingFields >= 3 || isFromBookingEndpoint
  },

  /**
   * Get phone number - now always prioritizes users table
   * @param {Object} request - Raw request data from API
   * @returns {string|null} - Phone number from users table
   */
  getPhoneNumber(request) {
    // Always prioritize phone number from users table for both booking and approval processes
    const phoneNumber = request.user?.phone || request.phone_number || request.borrower_phone
    
    console.log('📞 Auto-captured phone number from users table:', {
      requestId: request.id,
      user_phone: request.user?.phone,
      phone_number: request.phone_number,
      borrower_phone: request.borrower_phone,
      selected: phoneNumber,
      source: request.user?.phone ? 'users_table' : (request.phone_number ? 'booking_service_table' : 'fallback')
    })
    
    return phoneNumber
  },

  /**
   * Transform raw request data to standardized format
   * @param {Object} request - Raw request data from API
   * @returns {Object} - Transformed request data
   */
  transformRequest(request) {
    if (!request) return null

    // Detect request type and get appropriate phone number
    const isBooking = this.isBookingRequest(request)
    const phoneNumber = this.getPhoneNumber(request)

    return {
      // Core request data
      id: request.id,
      request_id: `REQ-${request.id.toString().padStart(6, '0')}`,
      request_type: isBooking ? 'booking' : 'access', // Add request type for reference

      // Auto-captured user details
      borrower_id: request.user_id || request.borrower_id,
      borrower_name: request.user?.name || request.borrower_name || 'Unknown User',
      borrower_email: request.user?.email || request.borrower_email,
      borrower_phone: phoneNumber,
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
      reason: request.reason || request.purpose,
      purpose: request.purpose || request.reason,

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
        message:
          response.data.message || 'Device issued and condition assessment saved successfully'
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
