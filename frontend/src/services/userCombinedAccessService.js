import axios from 'axios'

// Base API URL - adjust this to match your Laravel backend URL
const API_BASE_URL = process.env.VUE_APP_API_URL || 'http://localhost:8000/api'

// Create axios instance with default config
const apiClient = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'multipart/form-data',
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

export const userCombinedAccessService = {
  /**
   * Submit combined access request
   * @param {Object} formData - The form data including services and applicant details
   * @returns {Promise} API response
   */
  async submitCombinedAccessRequest(formData) {
    try {
      // 🔹 Create clean payload to avoid Proxy serialization issues
      const cleanPayload = {
        ...formData,
        services: {
          jeeva: formData.services.jeeva || false,
          wellsoft: formData.services.wellsoft || false,
          internet: formData.services.internet || false
        }
      }

      // Create FormData object for file upload
      const submitData = new FormData()
      
      // Add basic applicant details
      submitData.append('pf_number', cleanPayload.pfNumber)
      submitData.append('staff_name', cleanPayload.staffName)
      submitData.append('phone_number', cleanPayload.phoneNumber)
      submitData.append('department_id', cleanPayload.department)
      
      // Add signature file
      if (cleanPayload.signature) {
        submitData.append('signature', cleanPayload.signature)
      }
      
      // Add services object to FormData (for backend prepareForValidation method)
      // Only send services that are actually selected
      if (cleanPayload.services.jeeva) {
        submitData.append('services[jeeva]', '1')
      }
      if (cleanPayload.services.wellsoft) {
        submitData.append('services[wellsoft]', '1')
      }
      if (cleanPayload.services.internet) {
        submitData.append('services[internet]', '1')
      }
      
      // Add request_type array based on selected services
      const requestTypes = []
      if (cleanPayload.services.jeeva) requestTypes.push('jeeva_access')
      if (cleanPayload.services.wellsoft) requestTypes.push('wellsoft')
      if (cleanPayload.services.internet) requestTypes.push('internet_access_request')
      
      // Add each request type to FormData (Laravel expects request_type[] format for arrays)
      requestTypes.forEach((type) => {
        submitData.append('request_type[]', type)
      })
      
      // Add internet purposes if internet service is selected
      if (cleanPayload.services.internet && cleanPayload.internetPurposes) {
        cleanPayload.internetPurposes.forEach((purpose, index) => {
          if (purpose && purpose.trim()) {
            submitData.append(`internetPurposes[${index}]`, purpose.trim())
          }
        })
      }
      
      // Debug: Log FormData contents
      console.log('FormData contents:');
      for (let [key, value] of submitData.entries()) {
        console.log(key, value);
      }
      
      console.log('Submitting combined access request:', {
        pfNumber: cleanPayload.pfNumber,
        staffName: cleanPayload.staffName,
        services: cleanPayload.services,
        requestTypes: requestTypes,
        hasSignature: !!cleanPayload.signature,
        internetPurposes: cleanPayload.internetPurposes?.filter(p => p?.trim())
      })
      
      const response = await apiClient.post('/v1/combined-access', submitData)
      
      console.log('Combined access request submitted successfully:', response.data)
      return response.data
      
    } catch (error) {
      console.error('Error submitting combined access request:', error)
      
      // Handle validation errors
      if (error.response?.status === 422) {
        const validationErrors = error.response.data.errors || {}
        console.error('Validation errors:', validationErrors)
        console.error('Full error response:', error.response.data)
        throw {
          type: 'validation',
          message: 'Please check the form for errors.',
          errors: validationErrors
        }
      }
      
      // Handle other errors
      throw {
        type: 'error',
        message: error.response?.data?.message || 'Failed to submit request. Please try again.',
        details: error.response?.data
      }
    }
  },

  /**
   * Get available departments
   * @returns {Promise} List of departments
   */
  async getDepartments() {
    try {
      const response = await apiClient.get('/v1/departments')
      return response.data
    } catch (error) {
      console.error('Error fetching departments:', error)
      throw {
        type: 'error',
        message: 'Failed to load departments. Please refresh the page.',
        details: error.response?.data
      }
    }
  },

  /**
   * Check if signature exists for a PF number
   * @param {string} pfNumber - The PF number to check
   * @returns {Promise} Signature check result
   */
  async checkSignature(pfNumber) {
    try {
      const response = await apiClient.post('/v1/check-signature', {
        pf_number: pfNumber
      })
      return response.data
    } catch (error) {
      console.error('Error checking signature:', error)
      throw {
        type: 'error',
        message: 'Failed to check signature.',
        details: error.response?.data
      }
    }
  },

  /**
   * Get user's access requests
   * @param {Object} filters - Optional filters
   * @returns {Promise} List of access requests
   */
  async getUserAccessRequests(filters = {}) {
    try {
      const params = new URLSearchParams()
      
      if (filters.status) params.append('status', filters.status)
      if (filters.search) params.append('search', filters.search)
      if (filters.per_page) params.append('per_page', filters.per_page)
      if (filters.page) params.append('page', filters.page)
      
      const response = await apiClient.get(`/v1/user-access?${params.toString()}`)
      return response.data
    } catch (error) {
      console.error('Error fetching user access requests:', error)
      throw {
        type: 'error',
        message: 'Failed to load access requests.',
        details: error.response?.data
      }
    }
  }
}

export default userCombinedAccessService