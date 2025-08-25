/**
 * Personal Information Service
 * Handles API calls for capturing personal information from user_access table
 */

import apiClient from '@/utils/apiClient'

class PersonalInfoService {
  /**
   * Get personal information from user_access table by ID
   * @param {number} userAccessId - The user access record ID
   * @returns {Promise<Object>} Personal information data
   */
  async getPersonalInfoFromUserAccess(userAccessId) {
    try {
      const response = await apiClient.get(
        `/both-service-form/user-access/${userAccessId}/personal-info`
      )

      if (response.data.success) {
        return {
          success: true,
          data: response.data.data,
          meta: response.data.meta
        }
      } else {
        throw new Error(
          response.data.message || 'Failed to get personal information'
        )
      }
    } catch (error) {
      console.error('Error getting personal info from user access:', error)
      return {
        success: false,
        error:
          error.response?.data?.message ||
          error.message ||
          'Failed to get personal information'
      }
    }
  }

  /**
   * Get all user access requests for HOD dashboard
   * @returns {Promise<Object>} User access requests with personal information
   */
  async getUserAccessRequestsForHOD() {
    try {
      const response = await apiClient.get(
        '/both-service-form/hod/user-access-requests'
      )

      if (response.data.success) {
        return {
          success: true,
          data: response.data.data,
          meta: response.data.meta
        }
      } else {
        throw new Error(
          response.data.message || 'Failed to get user access requests'
        )
      }
    } catch (error) {
      console.error('Error getting user access requests for HOD:', error)
      return {
        success: false,
        error:
          error.response?.data?.message ||
          error.message ||
          'Failed to get user access requests'
      }
    }
  }

  /**
   * Transform personal information data for form display
   * @param {Object} personalInfo - Raw personal information from API
   * @returns {Object} Transformed data for form
   */
  transformPersonalInfoForForm(personalInfo) {
    return {
      pfNumber: personalInfo.pf_number || '',
      staffName: personalInfo.staff_name || '',
      department: personalInfo.department || '',
      departmentId: personalInfo.department_id || null,
      contactNumber: personalInfo.contact_number || '',
      signature: {
        path: personalInfo.signature?.path || null,
        url: personalInfo.signature?.url || null,
        exists: personalInfo.signature?.exists || false,
        status: personalInfo.signature?.exists
          ? 'Uploaded'
          : 'No signature uploaded'
      },
      requestDetails: personalInfo.request_details || {},
      userDetails: personalInfo.user_details || {}
    }
  }

  /**
   * Get display name for request type
   * @param {string|Array} requestType - Request type from database
   * @returns {string} Display name
   */
  getRequestTypeDisplay(requestType) {
    if (Array.isArray(requestType)) {
      const typeNames = requestType.map((type) =>
        this.getSingleRequestTypeDisplay(type)
      )
      return typeNames.join(', ')
    }
    return this.getSingleRequestTypeDisplay(requestType)
  }

  /**
   * Get display name for single request type
   * @param {string} type - Single request type
   * @returns {string} Display name
   */
  getSingleRequestTypeDisplay(type) {
    const typeMap = {
      jeeva_access: 'Jeeva Access',
      wellsoft: 'Wellsoft Access',
      internet_access_request: 'Internet Access',
      combined_services: 'Combined Services',
      both_service: 'Both Service Form'
    }
    return typeMap[type] || type
  }

  /**
   * Calculate priority based on days pending
   * @param {number} daysPending - Number of days pending
   * @returns {string} Priority level
   */
  calculatePriority(daysPending) {
    if (daysPending > 7) return 'High Priority'
    if (daysPending > 3) return 'Medium Priority'
    return 'Normal Priority'
  }

  /**
   * Format contact number for display
   * @param {string} contactNumber - Raw contact number
   * @returns {string} Formatted contact number
   */
  formatContactNumber(contactNumber) {
    if (!contactNumber) return 'N/A'

    // Remove any non-digit characters
    const digits = contactNumber.replace(/\D/g, '')

    // Format as 0XXX XXX XXX for Tanzanian numbers
    if (digits.length === 10 && digits.startsWith('0')) {
      return `${digits.slice(0, 4)} ${digits.slice(4, 7)} ${digits.slice(7)}`
    }

    return contactNumber
  }

  /**
   * Validate personal information data
   * @param {Object} personalInfo - Personal information to validate
   * @returns {Object} Validation result
   */
  validatePersonalInfo(personalInfo) {
    const errors = {}

    if (!personalInfo.pfNumber || personalInfo.pfNumber.trim() === '') {
      errors.pfNumber = 'PF Number is required'
    }

    if (!personalInfo.staffName || personalInfo.staffName.trim() === '') {
      errors.staffName = 'Staff Name is required'
    }

    if (!personalInfo.department || personalInfo.department.trim() === '') {
      errors.department = 'Department is required'
    }

    if (
      !personalInfo.contactNumber ||
      personalInfo.contactNumber.trim() === ''
    ) {
      errors.contactNumber = 'Contact Number is required'
    } else {
      // Validate contact number format
      const phoneRegex = /^0\d{9}$/
      const cleanNumber = personalInfo.contactNumber.replace(/\s/g, '')
      if (!phoneRegex.test(cleanNumber)) {
        errors.contactNumber = 'Contact Number must be in format 0XXX XXX XXX'
      }
    }

    if (!personalInfo.signature?.exists) {
      errors.signature = 'Signature is required'
    }

    return {
      isValid: Object.keys(errors).length === 0,
      errors
    }
  }
}

// Export singleton instance
export default new PersonalInfoService()
