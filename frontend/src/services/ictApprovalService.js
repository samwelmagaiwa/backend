import apiClient from '../utils/apiClient'

/**
 * ICT Approval Service for Device Borrowing Requests
 * Handles ICT officer approval/rejection of device booking requests
 * Auto-captures user details from users/staff table
 */
export const ictApprovalService = {
  /**
   * Get all device borrowing requests with auto-captured user details
   * @param {Object} params - Query parameters (status, device_type, department, etc.)
   * @returns {Promise<Object>} - API response with paginated requests
   */
  async getDeviceBorrowingRequests(params = {}) {
    try {
      // Use the dedicated ICT approval endpoint
      const response = await apiClient.get('/ict-approval/device-requests', {
        params: {
          ...params,
          per_page: params.per_page || 50
        }
      })

      // Transform data to ensure all user details are properly populated
      const requests = response.data.data || response.data
      const transformedRequests = Array.isArray(requests)
        ? requests.map((request) => ({
            // Core request data
            id: request.id,
            request_id: request.request_id || `REQ-${request.id.toString().padStart(6, '0')}`,

            // Auto-captured user details from users/staff table
            borrower_id: request.user_id || request.borrower_id,
            borrower_name:
              request.user?.name || request.borrower_name || request.staff?.name || 'Unknown User',
            borrower_email: request.user?.email || request.borrower_email || request.staff?.email,
            borrower_phone: request.user?.phone || request.borrower_phone || request.staff?.phone,
            pf_number: request.user?.pf_number || request.staff?.pf_number,

            // Department details
            department_id:
              request.user?.department_id || request.department_id || request.staff?.department_id,
            department:
              request.user?.department?.name ||
              request.department ||
              request.staff?.department?.name ||
              'Unknown Department',

            // Device details
            device_type: request.device_type,
            custom_device: request.custom_device,
            device_name:
              request.device_name ||
              this.getDeviceDisplayName(request.device_type, request.custom_device),
            device_inventory_id: request.device_inventory_id,
            device_available:
              typeof request.device_available !== 'undefined' ? request.device_available : null,

            // Booking details
            booking_date: request.booking_date,
            collection_date: request.collection_date,
            return_date: request.return_date,
            purpose: request.purpose,

            // Signature details
            signature: request.signature,
            signature_path: request.signature_path,
            has_signature: !!(request.signature || request.signature_path),

            // Status and approval
            status: request.status || 'pending',
            ict_status: request.ict_status || 'pending',
            ict_approval_date: request.ict_approval_date,
            ict_notes: request.ict_notes,

            // Timestamps
            created_at: request.created_at,
            updated_at: request.updated_at,

            // Raw data for advanced operations
            _raw: request
          }))
        : []

      return {
        success: true,
        data: {
          data: transformedRequests,
          current_page: response.data.current_page || 1,
          last_page: response.data.last_page || 1,
          per_page: response.data.per_page || 15,
          total: response.data.total || transformedRequests.length
        }
      }
    } catch (error) {
      console.error('Failed to fetch device borrowing requests:', error)
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to fetch device borrowing requests',
        error: error.message,
        status: error.response?.status
      }
    }
  },

  /**
   * Get detailed information for a specific device borrowing request
   * @param {number} requestId - Request ID
   * @returns {Promise<Object>} - API response with request details
   */
  async getDeviceBorrowingRequestDetails(requestId) {
    try {
      console.log('🌐 ICT Approval Service - Request Details for ID:', requestId)

      const response = await apiClient.get(`/booking-service/bookings/${requestId}`)

      const request = response.data.data || response.data

      // Transform data with auto-captured user details
      const transformedRequest = {
        // Core request data
        id: request.id,
        request_id: request.request_id || `REQ-${request.id.toString().padStart(6, '0')}`,

        // User details from booking service
        borrower_id: request.user_id,
        borrower_name: request.borrower_name || 'Unknown User',
        borrower_email: request.user?.email,
        borrower_phone: request.phone_number,
        pf_number: request.user?.pf_number,

        // Department details
        department_id: request.user?.department_id,
        department: request.departmentInfo?.name || request.department || 'Unknown Department',

        // Device details
        device_type: request.device_type,
        custom_device: request.custom_device,
        device_name: this.getDeviceDisplayName(request.device_type, request.custom_device),

        // Booking details
        booking_date: request.booking_date,
        return_date: request.return_date,
        return_time: request.return_time,
        reason: request.reason,

        // Signature details
        signature_path: request.signature_path,
        signature_url: request.signature_url,
        has_signature: !!request.signature_path,

        // Status and approval
        status: request.status || 'pending',
        ict_status: request.ict_approve || 'pending',
        ict_approval_date: request.ict_approved_at,
        ict_notes: request.ict_notes,
        ict_approved_by: request.ict_approved_by_name || request.ictApprovedBy?.name,

        // Timestamps
        created_at: request.created_at,
        updated_at: request.updated_at,

        // Raw data
        _raw: request
      }

      return {
        success: true,
        data: transformedRequest
      }
    } catch (error) {
      console.error('Failed to fetch request details:', error)
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to fetch request details',
        error: error.message,
        status: error.response?.status
      }
    }
  },

  /**
   * Approve a device borrowing request
   * @param {number} requestId - Request ID
   * @param {string} notes - ICT officer notes (optional)
   * @returns {Promise<Object>} - API response
   */
  async approveDeviceBorrowingRequest(requestId, notes = '') {
    try {
      const response = await apiClient.post(`/booking-service/bookings/${requestId}/ict-approve`, {
        ict_notes: notes
      })

      return {
        success: true,
        data: response.data,
        message: 'Device borrowing request approved successfully'
      }
    } catch (error) {
      console.error('Failed to approve request:', error)
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to approve request',
        errors: error.response?.data?.errors || {},
        status: error.response?.status
      }
    }
  },

  /**
   * Reject a device borrowing request
   * @param {number} requestId - Request ID
   * @param {string} notes - ICT officer rejection reason (required)
   * @returns {Promise<Object>} - API response
   */
  async rejectDeviceBorrowingRequest(requestId, notes) {
    try {
      if (!notes || notes.trim() === '') {
        throw new Error('Rejection reason is required')
      }

      const response = await apiClient.post(`/booking-service/bookings/${requestId}/ict-reject`, {
        ict_notes: notes
      })

      return {
        success: true,
        data: response.data,
        message: 'Device borrowing request rejected successfully'
      }
    } catch (error) {
      console.error('Failed to reject request:', error)
      return {
        success: false,
        message: error.response?.data?.message || error.message || 'Failed to reject request',
        errors: error.response?.data?.errors || {},
        status: error.response?.status
      }
    }
  },

  /**
   * Get statistics for device borrowing requests
   * @returns {Promise<Object>} - API response with statistics
   */
  async getDeviceBorrowingStatistics() {
    try {
      const response = await apiClient.get('/ict-approval/device-requests/statistics')

      const stats = response.data.data || response.data

      return {
        success: true,
        data: {
          pending: stats.pending || 0,
          approved: stats.approved || 0,
          rejected: stats.rejected || 0,
          returned: stats.returned || 0,
          compromised: stats.compromised || 0,
          total: stats.total || 0
        }
      }
    } catch (error) {
      console.error('Failed to fetch statistics:', error)
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to fetch statistics',
        status: error.response?.status
      }
    }
  },

  /**
   * Auto-capture and link user details to a booking request
   * This function is called when a booking request is submitted
   * @param {number} bookingId - Booking ID
   * @param {number} userId - User ID from authentication
   * @returns {Promise<Object>} - API response
   */
  async linkUserDetailsToBooking(bookingId, userId) {
    try {
      const response = await apiClient.post(
        `/ict-approval/device-requests/${bookingId}/link-user`,
        {
          user_id: userId,
          auto_capture: true
        }
      )

      return {
        success: true,
        data: response.data,
        message: 'User details linked to booking request successfully'
      }
    } catch (error) {
      console.error('Failed to link user details:', error)
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to link user details',
        status: error.response?.status
      }
    }
  },

  /**
   * Get device display name
   * @param {string} deviceType - Device type code
   * @param {string} customDevice - Custom device name
   * @returns {string} - Formatted device display name
   */
  getDeviceDisplayName(deviceType, customDevice) {
    if (deviceType === 'others' || deviceType === 'custom') {
      return customDevice || 'Other Device'
    }

    const deviceNames = {
      projector: 'Projector',
      tv_remote: 'TV Remote',
      hdmi_cable: 'HDMI Cable',
      monitor: 'Monitor',
      cpu: 'CPU',
      keyboard: 'Keyboard',
      mouse: 'Mouse',
      pc: 'PC',
      laptop: 'Laptop',
      tablet: 'Tablet',
      headphones: 'Headphones',
      speaker: 'Speaker',
      webcam: 'Webcam',
      printer: 'Printer'
    }

    return (
      deviceNames[deviceType] ||
      (deviceType || 'Unknown Device').replace(/_/g, ' ').replace(/\b\w/g, (l) => l.toUpperCase())
    )
  },

  /**
   * Format date string for display
   * @param {string} dateString - ISO date string
   * @returns {string} - Formatted date
   */
  formatDate(dateString) {
    if (!dateString) return ''
    const date = new Date(dateString)
    return date.toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric'
    })
  },

  /**
   * Format time string for display
   * @param {string} dateString - ISO date string
   * @returns {string} - Formatted time
   */
  formatTime(dateString) {
    if (!dateString) return ''
    const date = new Date(dateString)
    return date.toLocaleTimeString('en-US', {
      hour: '2-digit',
      minute: '2-digit'
    })
  },

  /**
   * Get status badge class for styling
   * @param {string} status - Request status
   * @returns {string} - CSS classes for status badge
   */
  getStatusBadgeClass(status) {
    const classes = {
      pending: 'bg-yellow-100 text-yellow-800 border border-yellow-200',
      approved: 'bg-green-100 text-green-800 border border-green-200',
      rejected: 'bg-red-100 text-red-800 border border-red-200',
      returned: 'bg-blue-100 text-blue-800 border border-blue-200',
      compromised: 'bg-purple-100 text-purple-800 border border-purple-200'
    }
    return classes[status] || 'bg-gray-100 text-gray-800 border border-gray-200'
  },

  /**
   * Get status icon
   * @param {string} status - Request status
   * @returns {string} - Font Awesome icon class
   */
  getStatusIcon(status) {
    const icons = {
      pending: 'fas fa-clock',
      approved: 'fas fa-check-circle',
      rejected: 'fas fa-times-circle',
      returned: 'fas fa-undo',
      compromised: 'fas fa-exclamation-triangle'
    }
    return icons[status] || 'fas fa-question-circle'
  },

  /**
   * Get status text for display
   * @param {string} status - Request status
   * @returns {string} - Human readable status text
   */
  getStatusText(status) {
    const texts = {
      pending: 'Pending',
      approved: 'Approved',
      rejected: 'Rejected',
      returned: 'Returned',
      compromised: 'Compromised'
    }
    return texts[status] || 'Unknown'
  }
}

export default ictApprovalService
