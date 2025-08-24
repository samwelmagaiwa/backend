import axios from 'axios'

// Base API URL - adjust this to match your Laravel backend URL
const API_BASE_URL = process.env.VUE_APP_API_URL || 'http://localhost:8000/api'

// Create axios instance with default config
const apiClient = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Accept': 'application/json',
  },
  timeout: 30000, // 30 seconds timeout for file uploads
})

// Add request interceptor to include auth token
apiClient.interceptors.request.use(
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

// Add response interceptor for error handling
apiClient.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      // Token expired or invalid
      localStorage.removeItem('auth_token')
      window.location.href = '/login'
    }
    return Promise.reject(error)
  }
)

const bookingService = {
  /**
   * Submit a new booking request
   */
  async submitBooking(bookingData) {
    try {
      console.log('Submitting booking request:', bookingData)
      
      // Create FormData for file upload
      const formData = new FormData()
      
      // Append all form fields
      Object.keys(bookingData).forEach(key => {
        if (bookingData[key] !== null && bookingData[key] !== undefined) {
          if (key === 'signature' && bookingData[key] instanceof File) {
            formData.append(key, bookingData[key])
            console.log(`Added file ${key}:`, bookingData[key].name, bookingData[key].size, 'bytes')
          } else {
            formData.append(key, bookingData[key])
            console.log(`Added field ${key}:`, bookingData[key])
          }
        }
      })
      
      // Debug: Log FormData contents
      console.log('FormData contents:')
      for (let [key, value] of formData.entries()) {
        console.log(key, value)
      }


      
      // If basic validation passes, try the full endpoint
      const response = await apiClient.post('/booking-service/bookings', formData, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      })

      console.log('Booking submission response:', response.data)
      return {
        success: true,
        data: response.data.data,
        message: response.data.message
      }
    } catch (error) {
      console.error('Error submitting booking:', error)
      
      if (error.response?.data?.errors) {
        // Validation errors
        return {
          success: false,
          type: 'validation',
          errors: error.response.data.errors,
          message: 'Please check the form for errors'
        }
      }
      
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to submit booking request'
      }
    }
  },

  /**
   * Get user's bookings
   */
  async getUserBookings(params = {}) {
    try {
      const response = await apiClient.get('/booking-service/bookings', { params })
      
      return {
        success: true,
        data: response.data.data,
        message: response.data.message
      }
    } catch (error) {
      console.error('Error fetching user bookings:', error)
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to fetch bookings'
      }
    }
  },

  /**
   * Get a specific booking
   */
  async getBooking(bookingId) {
    try {
      const response = await apiClient.get(`/booking-service/bookings/${bookingId}`)
      
      return {
        success: true,
        data: response.data.data,
        message: response.data.message
      }
    } catch (error) {
      console.error('Error fetching booking:', error)
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to fetch booking'
      }
    }
  },

  /**
   * Update a booking
   */
  async updateBooking(bookingId, bookingData) {
    try {
      // Create FormData for file upload
      const formData = new FormData()
      
      // Append all form fields
      Object.keys(bookingData).forEach(key => {
        if (bookingData[key] !== null && bookingData[key] !== undefined) {
          if (key === 'signature' && bookingData[key] instanceof File) {
            formData.append(key, bookingData[key])
          } else {
            formData.append(key, bookingData[key])
          }
        }
      })

      // Add method override for PUT request with FormData
      formData.append('_method', 'PUT')

      const response = await apiClient.post(`/booking-service/bookings/${bookingId}`, formData, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      })

      return {
        success: true,
        data: response.data.data,
        message: response.data.message
      }
    } catch (error) {
      console.error('Error updating booking:', error)
      
      if (error.response?.data?.errors) {
        return {
          success: false,
          type: 'validation',
          errors: error.response.data.errors,
          message: 'Please check the form for errors'
        }
      }
      
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to update booking'
      }
    }
  },

  /**
   * Delete a booking
   */
  async deleteBooking(bookingId) {
    try {
      const response = await apiClient.delete(`/booking-service/bookings/${bookingId}`)
      
      return {
        success: true,
        message: response.data.message
      }
    } catch (error) {
      console.error('Error deleting booking:', error)
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to delete booking'
      }
    }
  },

  /**
   * Get available device types
   */
  async getDeviceTypes() {
    try {
      const response = await apiClient.get('/booking-service/device-types')
      
      return {
        success: true,
        data: response.data.data,
        message: response.data.message
      }
    } catch (error) {
      console.error('Error fetching device types:', error)
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to fetch device types',
        data: []
      }
    }
  },

  /**
   * Get available departments
   */
  async getDepartments() {
    try {
      const response = await apiClient.get('/booking-service/departments')
      
      return {
        success: true,
        data: response.data.data,
        message: response.data.message
      }
    } catch (error) {
      console.error('Error fetching departments:', error)
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to fetch departments',
        data: []
      }
    }
  },

  /**
   * Get booking statistics
   */
  async getStatistics() {
    try {
      const response = await apiClient.get('/booking-service/statistics')
      
      return {
        success: true,
        data: response.data.data,
        message: response.data.message
      }
    } catch (error) {
      console.error('Error fetching statistics:', error)
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to fetch statistics'
      }
    }
  },

  /**
   * Admin: Approve a booking
   */
  async approveBooking(bookingId, adminNotes = '') {
    try {
      const response = await apiClient.post(`/booking-service/bookings/${bookingId}/approve`, {
        admin_notes: adminNotes
      })
      
      return {
        success: true,
        data: response.data.data,
        message: response.data.message
      }
    } catch (error) {
      console.error('Error approving booking:', error)
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to approve booking'
      }
    }
  },

  /**
   * Admin: Reject a booking
   */
  async rejectBooking(bookingId, adminNotes) {
    try {
      const response = await apiClient.post(`/booking-service/bookings/${bookingId}/reject`, {
        admin_notes: adminNotes
      })
      
      return {
        success: true,
        data: response.data.data,
        message: response.data.message
      }
    } catch (error) {
      console.error('Error rejecting booking:', error)
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to reject booking'
      }
    }
  }
}

export default bookingService