/**
 * Enhanced Booking Service
 * Uses the new API client with deduplication
 */

import { enhancedAPI } from '@/utils/enhancedApiClient'
import { apiDebugger } from '@/utils/apiDebugger'

class EnhancedBookingService {
  constructor() {
    this.baseUrl = '/booking-service'
  }

  /**
   * Get departments with automatic deduplication
   */
  async getDepartments() {
    try {
      apiDebugger.logRequest('GET', `${this.baseUrl}/departments`)

      const response = await enhancedAPI.get(`${this.baseUrl}/departments`)

      return {
        success: true,
        data: response.data
      }
    } catch (error) {
      console.error('Failed to fetch departments:', error)

      return {
        success: false,
        message: error.response?.data?.message || 'Failed to fetch departments',
        error: error.message
      }
    }
  }

  /**
   * Get device types with automatic deduplication
   */
  async getDeviceTypes() {
    try {
      apiDebugger.logRequest('GET', `${this.baseUrl}/device-types`)

      const response = await enhancedAPI.get(`${this.baseUrl}/device-types`)

      return {
        success: true,
        data: response.data
      }
    } catch (error) {
      console.error('Failed to fetch device types:', error)

      return {
        success: false,
        message: error.response?.data?.message || 'Failed to fetch device types',
        error: error.message
      }
    }
  }

  /**
   * Submit booking request (no deduplication for submissions)
   */
  async submitBooking(formData) {
    try {
      apiDebugger.logRequest('POST', `${this.baseUrl}/bookings`)

      const response = await enhancedAPI.post(`${this.baseUrl}/bookings`, formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        },
        // Don't deduplicate booking submissions
        deduplicate: false
      })

      return {
        success: true,
        data: response.data
      }
    } catch (error) {
      console.error('Failed to submit booking:', error)

      return {
        success: false,
        message: error.response?.data?.message || 'Failed to submit booking',
        errors: error.response?.data?.errors || {},
        error: error.message
      }
    }
  }

  /**
   * Get bookings with deduplication
   */
  async getBookings(params = {}) {
    try {
      const queryString = new URLSearchParams(params).toString()
      const url = `${this.baseUrl}/bookings${queryString ? `?${queryString}` : ''}`

      apiDebugger.logRequest('GET', url)

      const response = await enhancedAPI.get(url)

      return {
        success: true,
        data: response.data
      }
    } catch (error) {
      console.error('Failed to fetch bookings:', error)

      return {
        success: false,
        message: error.response?.data?.message || 'Failed to fetch bookings',
        error: error.message
      }
    }
  }

  /**
   * Get booking by ID with deduplication
   */
  async getBooking(id) {
    try {
      apiDebugger.logRequest('GET', `${this.baseUrl}/bookings/${id}`)

      const response = await enhancedAPI.get(`${this.baseUrl}/bookings/${id}`)

      return {
        success: true,
        data: response.data
      }
    } catch (error) {
      console.error('Failed to fetch booking:', error)

      return {
        success: false,
        message: error.response?.data?.message || 'Failed to fetch booking',
        error: error.message
      }
    }
  }

  /**
   * Update booking (no deduplication for updates)
   */
  async updateBooking(id, data) {
    try {
      apiDebugger.logRequest('PUT', `${this.baseUrl}/bookings/${id}`)

      const response = await enhancedAPI.put(`${this.baseUrl}/bookings/${id}`, data, {
        deduplicate: false // Don't deduplicate updates
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
        error: error.message
      }
    }
  }

  /**
   * Approve booking (no deduplication for actions)
   */
  async approveBooking(id, data = {}) {
    try {
      apiDebugger.logRequest('POST', `${this.baseUrl}/bookings/${id}/approve`)

      const response = await enhancedAPI.post(`${this.baseUrl}/bookings/${id}/approve`, data, {
        deduplicate: false // Don't deduplicate approval actions
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
        error: error.message
      }
    }
  }

  /**
   * Reject booking (no deduplication for actions)
   */
  async rejectBooking(id, data = {}) {
    try {
      apiDebugger.logRequest('POST', `${this.baseUrl}/bookings/${id}/reject`)

      const response = await enhancedAPI.post(`${this.baseUrl}/bookings/${id}/reject`, data, {
        deduplicate: false // Don't deduplicate rejection actions
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
        error: error.message
      }
    }
  }

  /**
   * Get booking statistics with deduplication
   */
  async getStatistics(params = {}) {
    try {
      const queryString = new URLSearchParams(params).toString()
      const url = `${this.baseUrl}/statistics${queryString ? `?${queryString}` : ''}`

      apiDebugger.logRequest('GET', url)

      const response = await enhancedAPI.get(url)

      return {
        success: true,
        data: response.data
      }
    } catch (error) {
      console.error('Failed to fetch statistics:', error)

      return {
        success: false,
        message: error.response?.data?.message || 'Failed to fetch statistics',
        error: error.message
      }
    }
  }
}

// Export singleton instance
export default new EnhancedBookingService()
