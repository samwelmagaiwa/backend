import axios from 'axios'

const API_BASE_URL = process.env.VUE_APP_API_URL || 'http://192.168.43.253:8000/api'

// Create axios instance with default config
const apiClient = axios.create({
  baseURL: API_BASE_URL,
  timeout: parseInt(process.env.VUE_APP_API_TIMEOUT) || 10000,
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json'
  }
})

// Add auth token to requests
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

// Handle response errors
apiClient.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      // Token expired or invalid
      localStorage.removeItem('auth_token')
      localStorage.removeItem('user')
      window.location.href = '/login'
    }
    return Promise.reject(error)
  }
)

export const roleManagementService = {
  // Role Management
  async getRoles(params = {}) {
    try {
      const response = await apiClient.get('/roles', { params })
      return response.data
    } catch (error) {
      throw this.handleError(error)
    }
  },

  async createRole(roleData) {
    try {
      const response = await apiClient.post('/roles', roleData)
      return response.data
    } catch (error) {
      throw this.handleError(error)
    }
  },

  async updateRole(roleId, roleData) {
    try {
      const response = await apiClient.put(`/roles/${roleId}`, roleData)
      return response.data
    } catch (error) {
      throw this.handleError(error)
    }
  },

  async deleteRole(roleId) {
    try {
      const response = await apiClient.delete(`/roles/${roleId}`)
      return response.data
    } catch (error) {
      throw this.handleError(error)
    }
  },

  async getRole(roleId) {
    try {
      const response = await apiClient.get(`/roles/${roleId}`)
      return response.data
    } catch (error) {
      throw this.handleError(error)
    }
  },

  async getRoleStatistics() {
    try {
      const response = await apiClient.get('/roles/statistics')
      return response.data
    } catch (error) {
      throw this.handleError(error)
    }
  },

  async getAvailablePermissions() {
    try {
      const response = await apiClient.get('/roles/permissions')
      return response.data
    } catch (error) {
      throw this.handleError(error)
    }
  },

  // User Role Management
  async getUsersWithRoles(params = {}) {
    try {
      const response = await apiClient.get('/user-roles', { params })
      return response.data
    } catch (error) {
      throw this.handleError(error)
    }
  },

  async assignRolesToUser(userId, roleIds) {
    try {
      const response = await apiClient.post(`/user-roles/${userId}/assign`, {
        role_ids: roleIds
      })
      return response.data
    } catch (error) {
      throw this.handleError(error)
    }
  },

  async removeRoleFromUser(userId, roleId) {
    try {
      const response = await apiClient.delete(
        `/user-roles/${userId}/roles/${roleId}`
      )
      return response.data
    } catch (error) {
      throw this.handleError(error)
    }
  },

  async getUserRoleHistory(userId) {
    try {
      const response = await apiClient.get(`/user-roles/${userId}/history`)
      return response.data
    } catch (error) {
      throw this.handleError(error)
    }
  },

  async getUserRoleStatistics() {
    try {
      const response = await apiClient.get('/user-roles/statistics')
      return response.data
    } catch (error) {
      throw this.handleError(error)
    }
  },

  // Department HOD Management
  async getDepartmentsWithHods(params = {}) {
    try {
      const response = await apiClient.get('/department-hod', { params })
      return response.data
    } catch (error) {
      throw this.handleError(error)
    }
  },

  async assignHodToDepartment(departmentId, hodUserId) {
    try {
      const response = await apiClient.post(
        `/department-hod/${departmentId}/assign`,
        {
          hod_user_id: hodUserId
        }
      )
      return response.data
    } catch (error) {
      throw this.handleError(error)
    }
  },

  async removeHodFromDepartment(departmentId) {
    try {
      const response = await apiClient.delete(
        `/department-hod/${departmentId}/remove`
      )
      return response.data
    } catch (error) {
      throw this.handleError(error)
    }
  },

  async getEligibleHods() {
    try {
      const response = await apiClient.get('/department-hod/eligible-hods')
      return response.data
    } catch (error) {
      throw this.handleError(error)
    }
  },

  async getDepartmentHodStatistics() {
    try {
      const response = await apiClient.get('/department-hod/statistics')
      return response.data
    } catch (error) {
      throw this.handleError(error)
    }
  },

  // Error handling
  handleError(error) {
    console.error('API Error:', error)

    if (error.response) {
      // Server responded with error status
      const { status, data } = error.response

      return {
        success: false,
        message: data.message || `HTTP Error ${status}`,
        errors: data.errors || {},
        status
      }
    } else if (error.request) {
      // Request made but no response received
      return {
        success: false,
        message: 'Network error. Please check your connection.',
        errors: {},
        status: 0
      }
    } else {
      // Something else happened
      return {
        success: false,
        message: error.message || 'An unexpected error occurred.',
        errors: {},
        status: 0
      }
    }
  }
}

export default roleManagementService
