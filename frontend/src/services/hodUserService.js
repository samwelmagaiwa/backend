import apiClient from '@/utils/apiClient'

export const hodUserService = {
  /**
   * Create a new staff user on behalf of the current Head of Department.
   * Backend will automatically:
   * - Force the role to `staff`
   * - Restrict department to HOD-owned departments
   */
  async createUser(userData) {
    try {
      const response = await apiClient.post('/hod/users', userData)
      return response.data
    } catch (error) {
      console.error('Error creating HOD staff user:', error)
      throw error
    }
  }
}

export default hodUserService
