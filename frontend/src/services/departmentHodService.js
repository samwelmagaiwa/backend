import apiClient from '@/utils/apiClient'

/**
 * Department HOD Management Service
 * Handles all API calls related to department and HOD management
 */
class DepartmentHodService {
  constructor() {
    this.baseUrl = '/department-hod'
  }

  /**
   * Get departments with HOD assignments
   * @param {Object} params - Query parameters for filtering and pagination
   * @returns {Promise<Object>} - API response with departments data
   */
  async getDepartmentsWithHods(params = {}) {
    try {
      const response = await apiClient.get(this.baseUrl, { params })

      if (response.data.success) {
        return {
          success: true,
          data: response.data.data,
          message: response.data.message
        }
      } else {
        return {
          success: false,
          error: response.data.message || 'Failed to fetch departments'
        }
      }
    } catch (error) {
      console.error('Failed to fetch departments with HODs:', error)
      return {
        success: false,
        error: error.response?.data?.message || 'Failed to fetch departments'
      }
    }
  }

  /**
   * Get eligible HOD users
   * @returns {Promise<Object>} - API response with eligible HODs
   */
  async getEligibleHods() {
    try {
      const response = await apiClient.get(`${this.baseUrl}/eligible-hods`)

      if (response.data.success) {
        return {
          success: true,
          data: response.data.data,
          message: response.data.message
        }
      } else {
        return {
          success: false,
          error: response.data.message || 'Failed to fetch eligible HODs'
        }
      }
    } catch (error) {
      console.error('Failed to fetch eligible HODs:', error)
      return {
        success: false,
        error: error.response?.data?.message || 'Failed to fetch eligible HODs'
      }
    }
  }

  /**
   * Get department HOD statistics
   * @returns {Promise<Object>} - API response with statistics
   */
  async getStatistics() {
    try {
      const response = await apiClient.get(`${this.baseUrl}/statistics`)

      if (response.data.success) {
        return {
          success: true,
          data: response.data.data,
          message: response.data.message
        }
      } else {
        return {
          success: false,
          error: response.data.message || 'Failed to fetch statistics'
        }
      }
    } catch (error) {
      console.error('Failed to fetch department statistics:', error)
      return {
        success: false,
        error: error.response?.data?.message || 'Failed to fetch statistics'
      }
    }
  }

  /**
   * Assign HOD to a department
   * @param {number} departmentId - Department ID
   * @param {number} hodUserId - HOD User ID
   * @returns {Promise<Object>} - API response
   */
  async assignHod(departmentId, hodUserId) {
    try {
      const response = await apiClient.post(`${this.baseUrl}/${departmentId}/assign`, {
        hod_user_id: hodUserId
      })

      if (response.data.success) {
        return {
          success: true,
          data: response.data.data,
          message: response.data.message
        }
      } else {
        return {
          success: false,
          error: response.data.message || 'Failed to assign HOD',
          errors: response.data.errors || {}
        }
      }
    } catch (error) {
      console.error('Failed to assign HOD:', error)
      return {
        success: false,
        error: error.response?.data?.message || 'Failed to assign HOD',
        errors: error.response?.data?.errors || {}
      }
    }
  }

  /**
   * Remove HOD from a department
   * @param {number} departmentId - Department ID
   * @returns {Promise<Object>} - API response
   */
  async removeHod(departmentId) {
    try {
      const response = await apiClient.delete(`${this.baseUrl}/${departmentId}/remove`)

      if (response.data.success) {
        return {
          success: true,
          message: response.data.message
        }
      } else {
        return {
          success: false,
          error: response.data.message || 'Failed to remove HOD'
        }
      }
    } catch (error) {
      console.error('Failed to remove HOD:', error)
      return {
        success: false,
        error: error.response?.data?.message || 'Failed to remove HOD'
      }
    }
  }

  /**
   * Create a new department
   * @param {Object} departmentData - Department data
   * @returns {Promise<Object>} - API response
   */
  async createDepartment(departmentData) {
    try {
      const response = await apiClient.post('/admin/departments', departmentData)

      if (response.data.success) {
        return {
          success: true,
          data: response.data.data,
          message: response.data.message
        }
      } else {
        return {
          success: false,
          error: response.data.message || 'Failed to create department',
          errors: response.data.errors || {}
        }
      }
    } catch (error) {
      console.error('Failed to create department:', error)
      return {
        success: false,
        error: error.response?.data?.message || 'Failed to create department',
        errors: error.response?.data?.errors || {}
      }
    }
  }

  /**
   * Update HOD for a department
   * @param {number} departmentId - Department ID
   * @param {number} hodUserId - New HOD User ID
   * @returns {Promise<Object>} - API response
   */
  async updateHod(departmentId, hodUserId) {
    try {
      const response = await apiClient.put(`${this.baseUrl}/${departmentId}/update`, {
        hod_user_id: hodUserId
      })

      if (response.data.success) {
        return {
          success: true,
          data: response.data.data,
          message: response.data.message
        }
      } else {
        return {
          success: false,
          error: response.data.message || 'Failed to update HOD',
          errors: response.data.errors || {}
        }
      }
    } catch (error) {
      console.error('Failed to update HOD:', error)
      return {
        success: false,
        error: error.response?.data?.message || 'Failed to update HOD',
        errors: error.response?.data?.errors || {}
      }
    }
  }

  /**
   * Get HOD details for a department
   * @param {number} departmentId - Department ID
   * @returns {Promise<Object>} - API response
   */
  async getHodDetails(departmentId) {
    try {
      const response = await apiClient.get(`${this.baseUrl}/${departmentId}/details`)

      if (response.data.success) {
        return {
          success: true,
          data: response.data.data,
          message: response.data.message
        }
      } else {
        return {
          success: false,
          error: response.data.message || 'Failed to get HOD details'
        }
      }
    } catch (error) {
      console.error('Failed to get HOD details:', error)
      return {
        success: false,
        error: error.response?.data?.message || 'Failed to get HOD details'
      }
    }
  }

  /**
   * Delete HOD assignment from a department
   * @param {number} departmentId - Department ID
   * @returns {Promise<Object>} - API response
   */
  async deleteHod(departmentId) {
    try {
      const response = await apiClient.delete(`${this.baseUrl}/${departmentId}/delete`)

      if (response.data.success) {
        return {
          success: true,
          message: response.data.message
        }
      } else {
        return {
          success: false,
          error: response.data.message || 'Failed to delete HOD'
        }
      }
    } catch (error) {
      console.error('Failed to delete HOD:', error)
      return {
        success: false,
        error: error.response?.data?.message || 'Failed to delete HOD'
      }
    }
  }
}

export default new DepartmentHodService()
