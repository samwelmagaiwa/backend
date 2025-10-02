import apiClient from '@/utils/apiClient'

export const adminUserService = {
  /**
   * Get all users with their roles and departments
   */
  async getAllUsers(params = {}) {
    try {
      const response = await apiClient.get('/admin/users', { params })
      return response.data
    } catch (error) {
      console.error('Error fetching users:', error)
      throw error
    }
  },

  /**
   * Get form data for creating a new user (roles and departments)
   */
  async getCreateFormData() {
    try {
      const response = await apiClient.get('/admin/users/create-form-data')
      return response.data
    } catch (error) {
      console.error('Error fetching create form data:', error)
      throw error
    }
  },

  /**
   * Get all departments
   */
  async getDepartments() {
    try {
      const response = await apiClient.get('/admin/users/departments')
      return response.data
    } catch (error) {
      console.error('Error fetching departments:', error)
      throw error
    }
  },

  /**
   * Get all roles
   */
  async getRoles() {
    try {
      const response = await apiClient.get('/admin/users/roles')
      return response.data
    } catch (error) {
      console.error('Error fetching roles:', error)
      throw error
    }
  },

  /**
   * Create a new user
   */
  async createUser(userData) {
    try {
      const response = await apiClient.post('/admin/users', userData)
      return response.data
    } catch (error) {
      console.error('Error creating user:', error)
      throw error
    }
  },

  /**
   * Update an existing user
   */
  async updateUser(userId, userData) {
    try {
      const response = await apiClient.put(`/admin/users/${userId}`, userData)
      return response.data
    } catch (error) {
      console.error('Error updating user:', error)
      throw error
    }
  },

  /**
   * Delete a user
   */
  async deleteUser(userId) {
    try {
      const response = await apiClient.delete(`/admin/users/${userId}`)
      return response.data
    } catch (error) {
      console.error('Error deleting user:', error)
      throw error
    }
  },

  /**
   * Toggle user active status
   */
  async toggleUserStatus(userId) {
    try {
      const response = await apiClient.patch(`/admin/users/${userId}/toggle-status`)
      return response.data
    } catch (error) {
      console.error('Error toggling user status:', error)
      throw error
    }
  },

  /**
   * Get user statistics
   */
  async getUserStatistics() {
    try {
      const response = await apiClient.get('/admin/users/statistics')
      return response.data
    } catch (error) {
      console.error('Error fetching user statistics:', error)
      throw error
    }
  },

  /**
   * Get specific user details
   */
  async getUser(userId) {
    try {
      const response = await apiClient.get(`/admin/users/${userId}`)
      return response.data
    } catch (error) {
      console.error('Error fetching user details:', error)
      throw error
    }
  },

  /**
   * Validate user data before submission
   */
  async validateUserData(userData) {
    try {
      const response = await apiClient.post('/admin/users/validate', userData)
      return response.data
    } catch (error) {
      console.error('Error validating user data:', error)
      throw error
    }
  },

  /**
   * Get all permissions
   */
  async getPermissions() {
    try {
      const response = await apiClient.get('/admin/permissions')
      return response.data
    } catch (error) {
      console.error('Error fetching permissions:', error)
      throw error
    }
  },

  /**
   * Create a new role
   */
  async createRole(roleData) {
    try {
      const response = await apiClient.post('/admin/roles', roleData)
      return response.data
    } catch (error) {
      console.error('Error creating role:', error)
      throw error
    }
  },

  /**
   * Update an existing role
   */
  async updateRole(roleId, roleData) {
    try {
      const response = await apiClient.put(`/admin/roles/${roleId}`, roleData)
      return response.data
    } catch (error) {
      console.error('Error updating role:', error)
      throw error
    }
  },

  /**
   * Delete a role
   */
  async deleteRole(roleId) {
    try {
      const response = await apiClient.delete(`/admin/roles/${roleId}`)
      return response.data
    } catch (error) {
      console.error('Error deleting role:', error)
      throw error
    }
  },

  /**
   * Assign roles to a user
   */
  async assignUserRoles(userId, roleIds) {
    try {
      const response = await apiClient.post(`/user-roles/${userId}/assign`, { role_ids: roleIds })
      return response.data
    } catch (error) {
      console.error('Error assigning roles:', error)
      throw error
    }
  },

  /**
   * Get user role history
   */
  async getUserRoleHistory(userId, params = {}) {
    try {
      const response = await apiClient.get(`/user-roles/${userId}/history`, { params })
      return response.data
    } catch (error) {
      console.error('Error fetching user role history:', error)
      throw error
    }
  },

  /**
   * Remove a specific role from a user
   */
  async removeUserRole(userId, roleId) {
    try {
      const response = await apiClient.delete(`/user-roles/${userId}/roles/${roleId}`)
      return response.data
    } catch (error) {
      console.error('Error removing role:', error)
      throw error
    }
  }
}

export default adminUserService
