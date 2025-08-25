/**
 * Login Memory Utility
 * Handles saving and retrieving user email for faster login
 */

const STORAGE_KEY = 'remembered_email'

export const loginMemory = {
  /**
   * Save email to localStorage
   * @param {string} email - Email to save
   */
  saveEmail(email) {
    if (email && email.trim()) {
      try {
        localStorage.setItem(STORAGE_KEY, email.trim())
        console.log('📧 Email saved for faster login')
      } catch (error) {
        console.error('Failed to save email:', error)
      }
    }
  },

  /**
   * Get saved email from localStorage
   * @returns {string|null} - Saved email or null
   */
  getSavedEmail() {
    try {
      const savedEmail = localStorage.getItem(STORAGE_KEY)
      if (savedEmail && savedEmail.trim()) {
        console.log('📧 Retrieved saved email for login')
        return savedEmail.trim()
      }
    } catch (error) {
      console.error('Failed to retrieve saved email:', error)
    }
    return null
  },

  /**
   * Clear saved email from localStorage
   */
  clearSavedEmail() {
    try {
      localStorage.removeItem(STORAGE_KEY)
      console.log('📧 Cleared saved email')
    } catch (error) {
      console.error('Failed to clear saved email:', error)
    }
  },

  /**
   * Check if email is saved
   * @returns {boolean} - True if email is saved
   */
  hasRememberedEmail() {
    return !!this.getSavedEmail()
  }
}

export default loginMemory
