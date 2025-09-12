import apiClient from '../utils/apiClient'

/**
 * Service for handling device booking requests
 * Manages ICT equipment reservations and bookings
 */
export const bookingService = {
  /**
   * Submit a new booking request
   * @param {FormData} formData - Form data including signature file
   * @returns {Promise<Object>} - API response
   */
  async submitBooking(formData) {
    try {
      const response = await apiClient.post('/booking-service/bookings', formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })
      return {
        success: true,
        data: response.data
      }
    } catch (error) {
      console.error('Booking submission failed:', error)
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to submit booking request',
        errors: error.response?.data?.errors || {},
        status: error.response?.status
      }
    }
  },

  /**
   * Get user's booking requests
   * @param {Object} params - Query parameters (status, device_type, etc.)
   * @returns {Promise<Object>} - API response with paginated bookings
   */
  async getUserBookings(params = {}) {
    try {
      const response = await apiClient.get('/booking-service/bookings', {
        params
      })
      return {
        success: true,
        data: response.data
      }
    } catch (error) {
      console.error('Failed to fetch user bookings:', error)
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to fetch bookings',
        status: error.response?.status
      }
    }
  },

  /**
   * Get a specific booking by ID
   * @param {number} bookingId - Booking ID
   * @returns {Promise<Object>} - API response with booking details
   */
  async getBookingById(bookingId) {
    try {
      const response = await apiClient.get(`/booking-service/bookings/${bookingId}`)
      return {
        success: true,
        data: response.data
      }
    } catch (error) {
      console.error('Failed to fetch booking details:', error)
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to fetch booking details',
        status: error.response?.status
      }
    }
  },

  /**
   * Update an existing booking
   * @param {number} bookingId - Booking ID
   * @param {FormData} formData - Updated form data
   * @returns {Promise<Object>} - API response
   */
  async updateBooking(bookingId, formData) {
    try {
      const response = await apiClient.post(`/booking-service/bookings/${bookingId}`, formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })
      return {
        success: true,
        data: response.data
      }
    } catch (error) {
      console.error('Failed to update booking:', error)
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to update booking',
        errors: error.response?.data?.errors || {},
        status: error.response?.status
      }
    }
  },

  /**
   * Delete a booking
   * @param {number} bookingId - Booking ID
   * @returns {Promise<Object>} - API response
   */
  async deleteBooking(bookingId) {
    try {
      const response = await apiClient.delete(`/booking-service/bookings/${bookingId}`)
      return {
        success: true,
        data: response.data
      }
    } catch (error) {
      console.error('Failed to delete booking:', error)
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to delete booking',
        status: error.response?.status
      }
    }
  },

  /**
   * Get available device types
   * @returns {Promise<Object>} - API response with device types
   */
  async getDeviceTypes() {
    try {
      const response = await apiClient.get('/booking-service/device-types')
      return {
        success: true,
        data: response.data
      }
    } catch (error) {
      console.error('Failed to fetch device types:', error)
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to fetch device types',
        status: error.response?.status
      }
    }
  },

  /**
   * Get available departments
   * @returns {Promise<Object>} - API response with departments
   */
  async getDepartments() {
    try {
      const response = await apiClient.get('/booking-service/departments')
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
   * Get booking statistics
   * @returns {Promise<Object>} - API response with statistics
   */
  async getStatistics() {
    try {
      const response = await apiClient.get('/booking-service/statistics')
      return {
        success: true,
        data: response.data
      }
    } catch (error) {
      console.error('Failed to fetch booking statistics:', error)
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to fetch statistics',
        status: error.response?.status
      }
    }
  },

  /**
   * Admin: Approve a booking
   * @param {number} bookingId - Booking ID
   * @param {string} adminNotes - Optional admin notes
   * @returns {Promise<Object>} - API response
   */
  async approveBooking(bookingId, adminNotes = '') {
    try {
      const response = await apiClient.post(`/booking-service/bookings/${bookingId}/approve`, {
        admin_notes: adminNotes
      })
      return {
        success: true,
        data: response.data
      }
    } catch (error) {
      console.error('Failed to approve booking:', error)
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to approve booking',
        status: error.response?.status
      }
    }
  },

  /**
   * Admin: Reject a booking
   * @param {number} bookingId - Booking ID
   * @param {string} adminNotes - Required rejection reason
   * @returns {Promise<Object>} - API response
   */
  async rejectBooking(bookingId, adminNotes) {
    try {
      const response = await apiClient.post(`/booking-service/bookings/${bookingId}/reject`, {
        admin_notes: adminNotes
      })
      return {
        success: true,
        data: response.data
      }
    } catch (error) {
      console.error('Failed to reject booking:', error)
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to reject booking',
        errors: error.response?.data?.errors || {},
        status: error.response?.status
      }
    }
  },

  /**
   * Test validation endpoint (for debugging)
   * @param {Object} testData - Test data to validate
   * @returns {Promise<Object>} - API response
   */
  async testValidation(testData) {
    try {
      const response = await apiClient.post('/booking-service/test-validation', testData)
      return {
        success: true,
        data: response.data
      }
    } catch (error) {
      console.error('Validation test failed:', error)
      return {
        success: false,
        message: error.response?.data?.message || 'Validation test failed',
        errors: error.response?.data?.errors || {},
        status: error.response?.status
      }
    }
  },

  /**
   * Debug departments endpoint
   * @returns {Promise<Object>} - API response with debug info
   */
  async debugDepartments() {
    try {
      const response = await apiClient.get('/booking-service/debug-departments')
      return {
        success: true,
        data: response.data
      }
    } catch (error) {
      console.error('Failed to fetch debug departments:', error)
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to fetch debug departments',
        status: error.response?.status
      }
    }
  },

  /**
   * Seed departments if they don't exist
   * @returns {Promise<Object>} - API response
   */
  async seedDepartments() {
    try {
      const response = await apiClient.post('/booking-service/seed-departments')
      return {
        success: true,
        data: response.data
      }
    } catch (error) {
      console.error('Failed to seed departments:', error)
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to seed departments',
        status: error.response?.status
      }
    }
  },

  /**
   * Check if user has any pending booking requests
   * @returns {Promise<Object>} - API response with pending request info
   */
  async checkPendingRequests() {
    try {
      const response = await apiClient.get('/booking-service/check-pending-requests')
      return {
        success: true,
        data: response.data
      }
    } catch (error) {
      console.error('Failed to check pending requests:', error)
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to check pending requests',
        status: error.response?.status,
        has_pending_request: false
      }
    }
  },

  /**
   * Check if user can submit a new booking request
   * Validates against unreturned devices and active requests
   * @returns {Promise<Object>} - API response with eligibility status
   */
  async canSubmitNewRequest() {
    try {
      const response = await apiClient.get('/booking-service/can-submit-new-request')
      return {
        success: true,
        data: response.data.data || response.data,
        can_submit: response.data.can_submit,
        message: response.data.message,
        active_request: response.data.active_request || null,
        return_status_info: response.data.return_status_info || null
      }
    } catch (error) {
      console.error('Failed to check new request eligibility:', error)
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to check request eligibility',
        status: error.response?.status,
        can_submit: false,
        active_request: null
      }
    }
  }
}

export default bookingService
