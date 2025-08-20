/**
 * Login Memory Utility
 * Manages remembering user email for faster login
 */

const STORAGE_KEY = 'remembered_email'

export const loginMemory = {
  /**
   * Save email to localStorage
   * @param {string} email - Email to remember
   */
  saveEmail(email) {
    if (email && email.trim()) {
      localStorage.setItem(STORAGE_KEY, email.trim())
      console.log('💾 Email saved for next login:', email)
      return true
    }
    return false
  },

  /**
   * Get saved email from localStorage
   * @returns {string|null} - Saved email or null
   */
  getSavedEmail() {
    const savedEmail = localStorage.getItem(STORAGE_KEY)
    if (savedEmail) {
      console.log('✅ Loaded saved email:', savedEmail)
      return savedEmail
    }
    return null
  },

  /**
   * Clear saved email from localStorage
   */
  clearSavedEmail() {
    localStorage.removeItem(STORAGE_KEY)
    console.log('🗑️ Cleared saved email')
  },

  /**
   * Check if email is currently saved
   * @returns {boolean}
   */
  hasRememberedEmail() {
    return localStorage.getItem(STORAGE_KEY) !== null
  },

  /**
   * Update saved email if it exists
   * @param {string} newEmail - New email to save
   */
  updateSavedEmail(newEmail) {
    if (this.hasRememberedEmail() && newEmail && newEmail.trim()) {
      this.saveEmail(newEmail)
      return true
    }
    return false
  }
}

export default loginMemory