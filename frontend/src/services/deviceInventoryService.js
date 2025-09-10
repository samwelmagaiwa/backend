import apiClient from '../utils/apiClient'

/**
 * Service for handling device inventory management
 * Manages ICT equipment inventory and availability
 */
export const deviceInventoryService = {
  /**
   * Get all devices with pagination and filters
   * @param {Object} params - Query parameters
   * @returns {Promise<Object>} - API response
   */
  async getDevices(params = {}) {
    try {
      const response = await apiClient.get('/device-inventory', { params })
      return {
        success: true,
        data: response.data
      }
    } catch (error) {
      console.error('Failed to fetch devices:', error)
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to fetch devices',
        status: error.response?.status
      }
    }
  },

  /**
   * Get available devices for booking
   * @returns {Promise<Object>} - API response
   */
  async getAvailableDevices() {
    try {
      const response = await apiClient.get('/device-inventory/available')
      return {
        success: true,
        data: response.data
      }
    } catch (error) {
      console.error('Failed to fetch available devices:', error)
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to fetch available devices',
        status: error.response?.status
      }
    }
  },

  /**
   * Create a new device
   * @param {Object} deviceData - Device information
   * @returns {Promise<Object>} - API response
   */
  async createDevice(deviceData) {
    try {
      const response = await apiClient.post('/device-inventory', deviceData)
      return {
        success: true,
        data: response.data
      }
    } catch (error) {
      console.error('Failed to create device:', error)
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to create device',
        errors: error.response?.data?.errors || {},
        status: error.response?.status
      }
    }
  },

  /**
   * Get a specific device by ID
   * @param {number} deviceId - Device ID
   * @returns {Promise<Object>} - API response
   */
  async getDevice(deviceId) {
    try {
      const response = await apiClient.get(`/device-inventory/${deviceId}`)
      return {
        success: true,
        data: response.data
      }
    } catch (error) {
      console.error('Failed to fetch device:', error)
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to fetch device',
        status: error.response?.status
      }
    }
  },

  /**
   * Update an existing device
   * @param {number} deviceId - Device ID
   * @param {Object} deviceData - Updated device information
   * @returns {Promise<Object>} - API response
   */
  async updateDevice(deviceId, deviceData) {
    try {
      const response = await apiClient.put(`/device-inventory/${deviceId}`, deviceData)
      return {
        success: true,
        data: response.data
      }
    } catch (error) {
      console.error('Failed to update device:', error)
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to update device',
        errors: error.response?.data?.errors || {},
        status: error.response?.status
      }
    }
  },

  /**
   * Delete a device
   * @param {number} deviceId - Device ID
   * @returns {Promise<Object>} - API response
   */
  async deleteDevice(deviceId) {
    try {
      const response = await apiClient.delete(`/device-inventory/${deviceId}`)
      return {
        success: true,
        data: response.data
      }
    } catch (error) {
      console.error('Failed to delete device:', error)
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to delete device',
        status: error.response?.status
      }
    }
  },

  /**
   * Get device inventory statistics
   * @returns {Promise<Object>} - API response
   */
  async getStatistics() {
    try {
      const response = await apiClient.get('/device-inventory/statistics')
      return {
        success: true,
        data: response.data
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
   * Get availability status color class
   * @param {string} status - Availability status
   * @returns {string} - CSS class
   */
  getStatusColorClass(status) {
    const statusClasses = {
      available: 'text-green-600 bg-green-100',
      low_stock: 'text-yellow-600 bg-yellow-100',
      out_of_stock: 'text-red-600 bg-red-100',
      inactive: 'text-gray-600 bg-gray-100'
    }
    return statusClasses[status] || 'text-gray-600 bg-gray-100'
  },

  /**
   * Get availability status display text
   * @param {string} status - Availability status
   * @returns {string} - Display text
   */
  getStatusDisplayText(status) {
    const statusTexts = {
      available: 'Available',
      low_stock: 'Low Stock',
      out_of_stock: 'Out of Stock',
      inactive: 'Inactive'
    }
    return statusTexts[status] || 'Unknown'
  },

  /**
   * Get availability status icon
   * @param {string} status - Availability status
   * @returns {string} - Icon class
   */
  getStatusIcon(status) {
    const statusIcons = {
      available: 'fas fa-check-circle',
      low_stock: 'fas fa-exclamation-triangle',
      out_of_stock: 'fas fa-times-circle',
      inactive: 'fas fa-pause-circle'
    }
    return statusIcons[status] || 'fas fa-question-circle'
  },
  /**
   * Fix device inventory quantity inconsistencies.
   * @returns {Promise<Object>} - API response
   */
  async fixQuantities() {
    try {
      const response = await apiClient.post('/device-inventory/fix-quantities')
      return {
        success: true,
        data: response.data.data
      }
    } catch (error) {
      console.error('Failed to fix quantities:', error)
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to fix quantities',
        status: error.response?.status
      }
    }
  },

  /**
   * Get return status color class for aggregated device return status
   * @param {string} status - Aggregated return status
   * @returns {string} - CSS class
   */
  getReturnStatusColorClass(status) {
    const statusClasses = {
      all_returned: 'text-green-600 bg-green-100',
      partially_returned: 'text-yellow-600 bg-yellow-100',
      none_returned: 'text-blue-600 bg-blue-100',
      some_compromised: 'text-red-600 bg-red-100',
      no_bookings: 'text-gray-600 bg-gray-100'
    }
    return statusClasses[status] || 'text-gray-600 bg-gray-100'
  },

  /**
   * Get return status icon for aggregated device return status
   * @param {string} status - Aggregated return status
   * @returns {string} - Icon class
   */
  getReturnStatusIcon(status) {
    const statusIcons = {
      all_returned: 'fas fa-check-circle',
      partially_returned: 'fas fa-clock',
      none_returned: 'fas fa-hourglass-half',
      some_compromised: 'fas fa-exclamation-triangle',
      no_bookings: 'fas fa-minus-circle'
    }
    return statusIcons[status] || 'fas fa-question-circle'
  },

  /**
   * Get return status display text for aggregated device return status
   * @param {string} status - Aggregated return status
   * @param {number} unreturnedCount - Number of unreturned items
   * @param {number} compromisedCount - Number of compromised items
   * @returns {string} - Display text
   */
  getReturnStatusDisplayText(status, unreturnedCount = 0, compromisedCount = 0) {
    const statusTexts = {
      all_returned: 'All Returned',
      partially_returned: `${unreturnedCount} Not Returned`,
      none_returned: `${unreturnedCount} Not Returned`,
      some_compromised: `${compromisedCount} Compromised`,
      no_bookings: 'No Bookings'
    }
    return statusTexts[status] || 'Unknown'
  }
}

export default deviceInventoryService
