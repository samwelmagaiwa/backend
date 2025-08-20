import apiClient from '@/utils/apiClient'

export const userInternetAccessFormService = {
  /**
   * Submit Internet Access request
   */
  async submitRequest(formData) {
    try {
      const response = await apiClient.post('/user-internet-access/submit', formData, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      })
      
      return {
        success: true,
        data: response.data.data,
        message: response.data.message,
        request_id: response.data.request_id,
        next_steps: response.data.next_steps
      }
    } catch (error) {
      console.error('Error submitting Internet Access request:', error)
      
      if (error.response?.data) {
        throw {
          message: error.response.data.message || 'Failed to submit request',
          details: error.response.data.errors || {},
          status: error.response.status
        }
      }
      
      throw {
        message: 'Network error. Please check your connection and try again.',
        details: {},
        status: 0
      }
    }
  },

  /**
   * Get user's Internet Access requests
   */
  async getUserRequests(params = {}) {
    try {
      const response = await apiClient.get('/user-internet-access/requests', { params })
      
      return {
        success: true,
        data: response.data.data,
        message: response.data.message
      }
    } catch (error) {
      console.error('Error fetching Internet Access requests:', error)
      throw {
        message: error.response?.data?.message || 'Failed to fetch requests',
        status: error.response?.status || 0
      }
    }
  },

  /**
   * Get specific Internet Access request
   */
  async getRequest(requestId) {
    try {
      const response = await apiClient.get(`/user-internet-access/requests/${requestId}`)
      
      return {
        success: true,
        data: response.data.data,
        message: response.data.message
      }
    } catch (error) {
      console.error('Error fetching Internet Access request:', error)
      throw {
        message: error.response?.data?.message || 'Failed to fetch request',
        status: error.response?.status || 0
      }
    }
  },

  /**
   * Get available departments
   */
  async getDepartments() {
    try {
      const response = await apiClient.get('/user-internet-access/departments')
      
      return {
        success: true,
        data: response.data.data,
        message: response.data.message
      }
    } catch (error) {
      console.error('Error fetching departments:', error)
      throw {
        message: error.response?.data?.message || 'Failed to fetch departments',
        status: error.response?.status || 0
      }
    }
  },

  /**
   * Check if signature exists for PF number
   */
  async checkSignature(pfNumber) {
    try {
      const response = await apiClient.post('/user-internet-access/check-signature', {
        pf_number: pfNumber
      })
      
      return {
        success: true,
        data: response.data.data,
        message: response.data.message
      }
    } catch (error) {
      console.error('Error checking signature:', error)
      throw {
        message: error.response?.data?.message || 'Failed to check signature',
        status: error.response?.status || 0
      }
    }
  },

  /**
   * Validate form data before submission
   */
  validateFormData(formData) {
    const errors = {}

    // PF Number validation
    if (!formData.pfNumber || formData.pfNumber.trim() === '') {
      errors.pfNumber = 'PF Number is required'
    } else if (formData.pfNumber.length < 3) {
      errors.pfNumber = 'PF Number must be at least 3 characters'
    } else if (formData.pfNumber.length > 50) {
      errors.pfNumber = 'PF Number cannot exceed 50 characters'
    } else if (!/^[A-Za-z0-9\-\/]+$/.test(formData.pfNumber)) {
      errors.pfNumber = 'PF Number format is invalid'
    }

    // Staff Name validation
    if (!formData.staffName || formData.staffName.trim() === '') {
      errors.staffName = 'Staff name is required'
    } else if (formData.staffName.length < 2) {
      errors.staffName = 'Staff name must be at least 2 characters'
    } else if (formData.staffName.length > 255) {
      errors.staffName = 'Staff name cannot exceed 255 characters'
    } else if (!/^[a-zA-Z\s\.\-\']+$/.test(formData.staffName)) {
      errors.staffName = 'Staff name contains invalid characters'
    }

    // Phone Number validation
    if (!formData.phoneNumber || formData.phoneNumber.trim() === '') {
      errors.phoneNumber = 'Phone number is required'
    } else if (formData.phoneNumber.length < 10) {
      errors.phoneNumber = 'Phone number must be at least 10 characters'
    } else if (formData.phoneNumber.length > 20) {
      errors.phoneNumber = 'Phone number cannot exceed 20 characters'
    } else if (!/^[\+]?[0-9\s\-\(\)]+$/.test(formData.phoneNumber)) {
      errors.phoneNumber = 'Phone number format is invalid'
    }

    // Department validation
    if (!formData.department) {
      errors.department = 'Department selection is required'
    }

    // Purpose validation
    if (!formData.purpose || formData.purpose.trim() === '') {
      errors.purpose = 'Internet access purpose is required'
    } else if (formData.purpose.length < 10) {
      errors.purpose = 'Purpose must be at least 10 characters'
    } else if (formData.purpose.length > 1000) {
      errors.purpose = 'Purpose cannot exceed 1000 characters'
    }

    // Signature validation
    if (!formData.signature) {
      errors.signature = 'Digital signature is required'
    }

    return {
      isValid: Object.keys(errors).length === 0,
      errors
    }
  },

  /**
   * Create FormData object for API submission
   */
  createFormData(formData) {
    const apiFormData = new FormData()
    
    apiFormData.append('pf_number', formData.pfNumber)
    apiFormData.append('staff_name', formData.staffName)
    apiFormData.append('phone_number', formData.phoneNumber)
    apiFormData.append('department_id', formData.department)
    apiFormData.append('purpose', formData.purpose)
    
    // Handle signature file
    if (formData.signature && formData.signature !== 'existing') {
      apiFormData.append('signature', formData.signature)
    }
    
    return apiFormData
  },

  /**
   * Format request data for display
   */
  formatRequestData(request) {
    return {
      ...request,
      formatted_created_at: new Date(request.created_at).toLocaleDateString(),
      formatted_status: this.getStatusLabel(request.status),
      formatted_request_type: 'Internet Access Request'
    }
  },

  /**
   * Get status label
   */
  getStatusLabel(status) {
    const statusLabels = {
      'pending': 'Pending Review',
      'approved': 'Approved',
      'rejected': 'Rejected',
      'in_review': 'Under Review'
    }
    
    return statusLabels[status] || status
  },

  /**
   * Get status color class
   */
  getStatusColor(status) {
    const statusColors = {
      'pending': 'text-yellow-600 bg-yellow-100',
      'approved': 'text-green-600 bg-green-100',
      'rejected': 'text-red-600 bg-red-100',
      'in_review': 'text-blue-600 bg-blue-100'
    }
    
    return statusColors[status] || 'text-gray-600 bg-gray-100'
  }
}