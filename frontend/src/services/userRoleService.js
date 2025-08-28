/**
 * User Role Management Service
 * Handles API calls for user role assignment and user creation
 */

import apiClient from '@/utils/apiClient'

export const userRoleService = {
  /**
   * Get all departments for dropdown selection
   */
  async getDepartments() {
    try {
      const response = await apiClient.get('/user-roles/departments')
      return {
        success: true,
        data: response.data.data,
        message: response.data.message
      }
    } catch (error) {
      console.error('Error fetching departments:', error)
      return {
        success: false,
        error: error.response?.data?.message || 'Failed to fetch departments',
        data: []
      }
    }
  },

  /**
   * Create a new user with roles and department assignment
   */
  async createUser(userData) {
    try {
      const response = await apiClient.post('/user-roles/create-user', userData)
      return {
        success: true,
        data: response.data.data,
        message: response.data.message
      }
    } catch (error) {
      console.error('Error creating user:', error)
      return {
        success: false,
        error: error.response?.data?.message || 'Failed to create user',
        errors: error.response?.data?.errors || {}
      }
    }
  },

  /**
   * Get all users with their roles
   */
  async getUsers(params = {}) {
    try {
      const response = await apiClient.get('/user-roles', { params })
      return {
        success: true,
        data: response.data.data,
        message: response.data.message
      }
    } catch (error) {
      console.error('Error fetching users:', error)
      return {
        success: false,
        error: error.response?.data?.message || 'Failed to fetch users',
        data: { data: [], total: 0 }
      }
    }
  },

  /**
   * Get user role statistics
   */
  async getStatistics() {
    try {
      const response = await apiClient.get('/user-roles/statistics')
      return {
        success: true,
        data: response.data.data,
        message: response.data.message
      }
    } catch (error) {
      console.error('Error fetching statistics:', error)
      return {
        success: false,
        error: error.response?.data?.message || 'Failed to fetch statistics',
        data: {}
      }
    }
  },

  /**
   * Assign roles to a user
   */
  async assignRoles(userId, roleIds) {
    try {
      const response = await apiClient.post(`/user-roles/${userId}/assign`, {
        role_ids: roleIds
      })
      return {
        success: true,
        data: response.data.data,
        message: response.data.message
      }
    } catch (error) {
      console.error('Error assigning roles:', error)
      return {
        success: false,
        error: error.response?.data?.message || 'Failed to assign roles',
        errors: error.response?.data?.errors || {}
      }
    }
  },

  /**
   * Remove a role from a user
   */
  async removeRole(userId, roleId) {
    try {
      const response = await apiClient.delete(`/user-roles/${userId}/roles/${roleId}`)
      return {
        success: true,
        message: response.data.message
      }
    } catch (error) {
      console.error('Error removing role:', error)
      return {
        success: false,
        error: error.response?.data?.message || 'Failed to remove role'
      }
    }
  },

  /**
   * Get user's role history
   */
  async getRoleHistory(userId) {
    try {
      const response = await apiClient.get(`/user-roles/${userId}/history`)
      return {
        success: true,
        data: response.data.data,
        message: response.data.message
      }
    } catch (error) {
      console.error('Error fetching role history:', error)
      return {
        success: false,
        error: error.response?.data?.message || 'Failed to fetch role history',
        data: { data: [] }
      }
    }
  }
}

export default userRoleService
