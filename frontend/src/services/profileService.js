import apiClient from '@/services/apiClient'

export const profileService = {
  /**
   * Upload a new avatar for the current user
   * @param {File} file - image file
   */
  async uploadAvatar(file) {
    const formData = new FormData()
    formData.append('avatar', file)

    try {
      const response = await apiClient.post('/profile/avatar', formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })

      return {
        success: true,
        data: response.data
      }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to upload profile picture',
        errors: error.response?.data?.errors || {}
      }
    }
  }
}

export default profileService
