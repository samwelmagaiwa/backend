/**
 * Global Logout Guard
 * Prevents duplicate logout API calls across the application
 */

class LogoutGuard {
  constructor() {
    this.isLoggingOut = false
    this.logoutPromise = null
    this.lastLogoutTime = 0
    this.cooldownPeriod = 2000 // 2 seconds cooldown
  }

  /**
   * Execute logout with deduplication
   */
  async executeLogout(logoutFunction) {
    const now = Date.now()

    // Check if we're in cooldown period
    if (now - this.lastLogoutTime < this.cooldownPeriod) {
      console.log('⚠️ Logout in cooldown period, ignoring duplicate call')
      return { success: true, message: 'Logout in cooldown period' }
    }

    // If already logging out, return the existing promise
    if (this.isLoggingOut && this.logoutPromise) {
      console.log('⚠️ Logout already in progress, waiting for completion...')
      return this.logoutPromise
    }

    console.log('🚪 Starting logout process...')
    this.isLoggingOut = true
    this.lastLogoutTime = now

    // Create and store the logout promise
    this.logoutPromise = this.performLogout(logoutFunction)

    return this.logoutPromise
  }

  /**
   * Perform the actual logout
   */
  async performLogout(logoutFunction) {
    try {
      const result = await logoutFunction()
      console.log('✅ Logout completed successfully')
      return result
    } catch (error) {
      console.error('❌ Logout failed:', error)
      throw error
    } finally {
      // Reset after a delay to prevent rapid re-logout attempts
      setTimeout(() => {
        this.isLoggingOut = false
        this.logoutPromise = null
      }, 1000)
    }
  }

  /**
   * Reset the guard (useful for testing)
   */
  reset() {
    this.isLoggingOut = false
    this.logoutPromise = null
    this.lastLogoutTime = 0
  }

  /**
   * Check if logout is in progress
   */
  get isInProgress() {
    return this.isLoggingOut
  }
}

// Export singleton instance
export const logoutGuard = new LogoutGuard()

// Add to window for debugging
if (typeof window !== 'undefined' && process.env.VUE_APP_DEBUG === 'true') {
  window.logoutGuard = logoutGuard
}

export default logoutGuard
