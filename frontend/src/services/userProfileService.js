import apiClient from '../utils/apiClient'

/**
 * User Profile Service for form auto-population
 * Provides methods to retrieve and update user profile data
 */
class UserProfileService {
  /**
   * Get current user's profile information for form auto-population
   * @returns {Promise<Object>} User profile data
   */
  async getCurrentUserProfile() {
    try {
      console.log('🔄 Fetching current user profile for auto-population...')

      const response = await apiClient.get('/profile/current')

      if (response.data && response.data.success) {
        console.log('✅ User profile retrieved successfully:', response.data.data)
        return {
          success: true,
          data: response.data.data
        }
      } else {
        throw new Error(response.data?.message || 'Failed to retrieve user profile')
      }
    } catch (error) {
      console.error('❌ Error fetching user profile:', error)
      return {
        success: false,
        error: error.message || 'Failed to retrieve user profile',
        data: null
      }
    }
  }

  /**
   * Look up user by PF Number (for HOD/Admin use)
   * @param {string} pfNumber - PF Number to look up
   * @returns {Promise<Object>} User data or error
   */
  async getUserByPfNumber(pfNumber) {
    try {
      console.log('🔍 Looking up user by PF Number:', pfNumber)

      const response = await apiClient.post('/profile/lookup-pf', {
        pf_number: pfNumber
      })

      if (response.data && response.data.success) {
        console.log('✅ User found:', response.data.data)
        return {
          success: true,
          data: response.data.data
        }
      } else {
        return {
          success: false,
          message: response.data?.message || 'User not found',
          data: null
        }
      }
    } catch (error) {
      console.error('❌ Error looking up user:', error)
      return {
        success: false,
        error: error.response?.data?.message || error.message || 'Failed to look up user',
        data: null
      }
    }
  }

  /**
   * Check if PF Number exists
   * @param {string} pfNumber - PF Number to check
   * @returns {Promise<Object>} Existence check result
   */
  async checkPfNumberExists(pfNumber) {
    try {
      console.log('🔍 Checking if PF Number exists:', pfNumber)

      const response = await apiClient.post('/profile/check-pf', {
        pf_number: pfNumber
      })

      if (response.data && response.data.success) {
        return {
          success: true,
          exists: response.data.exists,
          pfNumber: pfNumber
        }
      } else {
        throw new Error(response.data?.message || 'Failed to check PF Number')
      }
    } catch (error) {
      console.error('❌ Error checking PF Number:', error)
      return {
        success: false,
        error: error.message || 'Failed to check PF Number',
        exists: false
      }
    }
  }

  /**
   * Get departments list for dropdowns
   * @returns {Promise<Object>} Departments list
   */
  async getDepartments() {
    try {
      console.log('📋 Fetching departments list...')

      const response = await apiClient.get('/profile/departments')

      if (response.data && response.data.success) {
        console.log('✅ Departments retrieved:', response.data.count, 'departments')
        return {
          success: true,
          data: response.data.data,
          count: response.data.count
        }
      } else {
        throw new Error(response.data?.message || 'Failed to retrieve departments')
      }
    } catch (error) {
      console.error('❌ Error fetching departments:', error)
      return {
        success: false,
        error: error.message || 'Failed to retrieve departments',
        data: []
      }
    }
  }

  /**
   * Update current user's profile information
   * @param {Object} profileData - Profile data to update
   * @returns {Promise<Object>} Update result
   */
  async updateCurrentUserProfile(profileData) {
    try {
      console.log('🔄 Updating user profile...')

      const response = await apiClient.put('/profile/current', profileData)

      if (response.data && response.data.success) {
        console.log('✅ Profile updated successfully')
        return {
          success: true,
          data: response.data.data,
          message: 'Profile updated successfully'
        }
      } else {
        throw new Error(response.data?.message || 'Failed to update profile')
      }
    } catch (error) {
      console.error('❌ Error updating profile:', error)
      return {
        success: false,
        error: error.response?.data?.message || error.message || 'Failed to update profile',
        errors: error.response?.data?.errors || null
      }
    }
  }

  /**
   * Auto-populate form data from user profile
   * @returns {Promise<Object>} Form data ready for population
   */
  async getFormAutoPopulationData() {
    try {
      const profileResult = await this.getCurrentUserProfile()

      if (profileResult.success) {
        const userData = profileResult.data

        // Format data specifically for form auto-population
        const formData = {
          // Core user information
          pfNumber: userData.pf_number || '',
          staffName: userData.staff_name || userData.full_name || '',
          fullName: userData.full_name || userData.name || '',
          phoneNumber: userData.phone || userData.phone_number || '',
          email: userData.email || '',

          // Department information
          departmentId: userData.department_id || null,
          departmentName: userData.department?.name || '',
          departmentCode: userData.department?.code || '',
          departmentFullName:
            userData.department?.display_name || userData.department?.full_name || '',

          // Additional metadata
          userId: userData.id,
          primaryRole: userData.primary_role || '',
          roles: userData.roles || [],
          isActive: userData.is_active !== false
        }

        return {
          success: true,
          data: formData,
          message: 'Form data populated successfully'
        }
      } else {
        return profileResult
      }
    } catch (error) {
      console.error('❌ Error preparing form auto-population data:', error)
      return {
        success: false,
        error: error.message || 'Failed to prepare form data',
        data: null
      }
    }
  }

  /**
   * Validate user data before form submission
   * @param {Object} userData - User data to validate
   * @returns {Object} Validation result
   */
  validateUserData(userData) {
    const errors = []

    if (!userData.pfNumber || userData.pfNumber.trim() === '') {
      errors.push('PF Number is required')
    }

    if (!userData.staffName || userData.staffName.trim() === '') {
      errors.push('Staff Name is required')
    }

    if (!userData.phoneNumber || userData.phoneNumber.trim() === '') {
      errors.push('Phone Number is required')
    }

    if (!userData.departmentId) {
      errors.push('Department is required')
    }

    return {
      isValid: errors.length === 0,
      errors: errors
    }
  }
}

export default new UserProfileService()
