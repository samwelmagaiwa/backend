/**
 * Enhanced User Combined Access Service
 * Uses the new API client with deduplication
 */

import { enhancedAPI } from '@/utils/enhancedApiClient'
import { apiDebugger } from '@/utils/apiDebugger'

class EnhancedUserCombinedAccessService {
  constructor() {
    this.baseUrl = '/v1'
  }

  /**
   * Get departments with automatic deduplication
   */
  async getDepartments() {
    try {
      apiDebugger.logRequest('GET', `${this.baseUrl}/departments`)

      const response = await enhancedAPI.get(`${this.baseUrl}/departments`)

      return {
        success: true,
        data: response.data
      }
    } catch (error) {
      console.error('Failed to fetch departments:', error)

      return {
        success: false,
        message: error.response?.data?.message || 'Failed to fetch departments',
        error: error.message
      }
    }
  }

  /**
   * Submit combined access request (no deduplication for submissions)
   */
  async submitCombinedRequest(formData) {
    try {
      apiDebugger.logRequest('POST', `${this.baseUrl}/combined-access`)

      const response = await enhancedAPI.post(
        `${this.baseUrl}/combined-access`,
        formData,
        {
          headers: {
            'Content-Type': 'multipart/form-data'
          },
          // Don't deduplicate form submissions
          deduplicate: false
        }
      )

      return {
        success: true,
        data: response.data
      }
    } catch (error) {
      console.error('Failed to submit combined request:', error)

      return {
        success: false,
        message: error.response?.data?.message || 'Failed to submit request',
        errors: error.response?.data?.errors || {},
        error: error.message
      }
    }
  }

  /**
   * Check signature with deduplication (safe to deduplicate)
   */
  async checkSignature(pfNumber) {
    try {
      apiDebugger.logRequest('POST', `${this.baseUrl}/check-signature`)

      const response = await enhancedAPI.post(
        `${this.baseUrl}/check-signature`,
        { pf_number: pfNumber },
        {
          deduplicate: true // Safe to deduplicate signature checks
        }
      )

      return {
        success: true,
        data: response.data
      }
    } catch (error) {
      console.error('Failed to check signature:', error)

      return {
        success: false,
        message: error.response?.data?.message || 'Failed to check signature',
        error: error.message
      }
    }
  }

  /**
   * Get user access requests with deduplication
   */
  async getUserAccessRequests(params = {}) {
    try {
      const queryString = new URLSearchParams(params).toString()
      const url = `${this.baseUrl}/user-access${queryString ? `?${queryString}` : ''}`

      apiDebugger.logRequest('GET', url)

      const response = await enhancedAPI.get(url)

      return {
        success: true,
        data: response.data
      }
    } catch (error) {
      console.error('Failed to fetch user access requests:', error)

      return {
        success: false,
        message: error.response?.data?.message || 'Failed to fetch requests',
        error: error.message
      }
    }
  }

  /**
   * Check pending requests status with deduplication
   */
  async checkPendingRequests() {
    try {
      apiDebugger.logRequest('GET', `${this.baseUrl}/user-access/pending-status`)

      const response = await enhancedAPI.get(`${this.baseUrl}/user-access/pending-status`)

      return {
        success: true,
        data: response.data
      }
    } catch (error) {
      console.error('Failed to check pending requests:', error)

      return {
        success: false,
        message: error.response?.data?.message || 'Failed to check pending requests',
        error: error.message
      }
    }
  }
}

// Export singleton instance
export default new EnhancedUserCombinedAccessService()
