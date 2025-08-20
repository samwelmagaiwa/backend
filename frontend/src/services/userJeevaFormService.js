import axios from 'axios'

// Base API configuration
const API_BASE_URL = process.env.VUE_APP_API_URL || 'http://localhost:8000/api'

// Create axios instance with default config
const apiClient = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
})

// Add auth token to requests
apiClient.interceptors.request.use((config) => {
  const token = localStorage.getItem('auth_token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

// Handle response errors
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

export const userJeevaFormService = {
  /**
   * Submit a new Jeeva access request
   * @param {FormData} formData - Form data including signature file
   * @returns {Promise} API response
   */
  async submitRequest(formData) {
    try {
      const response = await apiClient.post('/user-jeeva/submit', formData, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      })
      return response.data
    } catch (error) {
      throw this.handleError(error)
    }
  },

  /**
   * Get user's Jeeva access requests
   * @param {Object} params - Query parameters (page, per_page, status, search, etc.)
   * @returns {Promise} API response
   */
  async getRequests(params = {}) {
    try {
      const response = await apiClient.get('/user-jeeva/requests', { params })
      return response.data
    } catch (error) {
      throw this.handleError(error)
    }
  },

  /**
   * Get a specific Jeeva access request
   * @param {number} requestId - Request ID
   * @returns {Promise} API response
   */
  async getRequest(requestId) {
    try {
      const response = await apiClient.get(`/user-jeeva/requests/${requestId}`)
      return response.data
    } catch (error) {
      throw this.handleError(error)
    }
  },

  /**
   * Get available departments
   * @returns {Promise} API response
   */
  async getDepartments() {
    try {
      const response = await apiClient.get('/user-jeeva/departments')
      return response.data
    } catch (error) {
      throw this.handleError(error)
    }
  },

  /**
   * Check if signature exists for a PF number
   * @param {string} pfNumber - PF Number
   * @returns {Promise} API response
   */
  async checkSignature(pfNumber) {
    try {
      const response = await apiClient.post('/user-jeeva/check-signature', {
        pf_number: pfNumber
      })
      return response.data
    } catch (error) {
      throw this.handleError(error)
    }
  },

  /**
   * Handle API errors consistently
   * @param {Error} error - Axios error object
   * @returns {Object} Formatted error object
   */
  handleError(error) {
    if (error.response) {
      // Server responded with error status
      const { status, data } = error.response
      return {
        success: false,
        message: data.message || 'An error occurred',
        error: data.error || 'Server error',
        status,
        details: data.errors || null,
        ...data
      }
    } else if (error.request) {
      // Network error
      return {
        success: false,
        message: 'Network error. Please check your connection.',
        error: 'Network error',
        status: 0
      }
    } else {
      // Other error
      return {
        success: false,
        message: 'An unexpected error occurred.',
        error: error.message || 'Unknown error',
        status: 0
      }
    }
  },

  /**
   * Create FormData object from form data
   * @param {Object} formData - Form data object
   * @returns {FormData} FormData object for multipart upload
   */
  createFormData(formData) {
    const data = new FormData()
    
    // Add text fields
    data.append('pf_number', formData.pfNumber || '')
    data.append('staff_name', formData.staffName || '')
    data.append('phone_number', formData.phoneNumber || '')
    data.append('department_id', formData.department || '')
    
    // Add signature file if present
    if (formData.signature && formData.signature instanceof File) {
      data.append('signature', formData.signature)
    }
    
    return data
  },

  /**
   * Validate form data before submission
   * @param {Object} formData - Form data to validate
   * @returns {Object} Validation result
   */
  validateFormData(formData) {
    const errors = {}

    // Validate PF Number
    if (!formData.pfNumber?.trim()) {
      errors.pfNumber = 'PF Number is required'
    } else if (formData.pfNumber.trim().length < 3) {
      errors.pfNumber = 'PF Number must be at least 3 characters'
    // eslint-disable-next-line no-useless-escape
    } else if (!/^[A-Za-z0-9\-\/]+$/.test(formData.pfNumber.trim())) {
      errors.pfNumber = 'PF Number format is invalid'
    }

    // Validate Staff Name
    if (!formData.staffName?.trim()) {
      errors.staffName = 'Staff Name is required'
    } else if (formData.staffName.trim().length < 2) {
      errors.staffName = 'Staff Name must be at least 2 characters'
    // eslint-disable-next-line no-useless-escape
    } else if (!/^[a-zA-Z\s\.\-\']+$/.test(formData.staffName.trim())) {
      errors.staffName = 'Staff Name contains invalid characters'
    }

    // Validate Phone Number
    if (!formData.phoneNumber?.trim()) {
      errors.phoneNumber = 'Phone Number is required'
    } else if (formData.phoneNumber.trim().length < 10) {
      errors.phoneNumber = 'Phone Number must be at least 10 digits'
    // eslint-disable-next-line no-useless-escape
    } else if (!/^[\+]?[0-9\s\-\(\)]+$/.test(formData.phoneNumber.trim())) {
      errors.phoneNumber = 'Phone Number format is invalid'
    }

    // Validate Department
    if (!formData.department) {
      errors.department = 'Department selection is required'
    }

    // Validate Signature
    if (!formData.signature) {
      errors.signature = 'Digital signature is required'
    } else if (formData.signature instanceof File) {
      // Validate file type
      const allowedTypes = ['image/png', 'image/jpg', 'image/jpeg']
      if (!allowedTypes.includes(formData.signature.type)) {
        errors.signature = 'Please upload a PNG or JPG image file only'
      }
      // Validate file size (2MB)
      else if (formData.signature.size > 2 * 1024 * 1024) {
        errors.signature = 'File size must be less than 2MB'
      }
    }

    return {
      isValid: Object.keys(errors).length === 0,
      errors
    }
  }
}

export default userJeevaFormService