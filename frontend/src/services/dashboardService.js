import apiClient from '../utils/apiClient'

class DashboardService {
  /**
   * Fetch user dashboard statistics
   * @returns {Promise<Object>} Dashboard statistics including processing, completed, and revision counts
   */
  async getUserDashboardStats() {
    try {
      console.log('📊 Fetching user dashboard statistics...')

      const response = await apiClient.get('/user/dashboard-stats')

      if (response.data && response.data.success) {
        console.log('✅ Dashboard stats fetched successfully:', response.data.data)
        return {
          success: true,
          data: {
            processing: response.data.data.processing || 0,
            underReview: response.data.data.under_review || response.data.data.processing || 0,
            completed: response.data.data.completed || 0,
            grantedAccess: response.data.data.granted_access || response.data.data.completed || 0,
            revision: response.data.data.revision || response.data.data.needs_revision || 0,
            needsRevision: response.data.data.needs_revision || response.data.data.revision || 0,
            total: response.data.data.total || 0,
            // Progress calculations
            processingPercentage: this.calculatePercentage(
              response.data.data.processing || 0,
              response.data.data.total || 1
            ),
            completedPercentage: this.calculatePercentage(
              response.data.data.completed || 0,
              response.data.data.total || 1
            ),
            revisionPercentage: this.calculatePercentage(
              response.data.data.revision || response.data.data.needs_revision || 0,
              response.data.data.total || 1
            )
          }
        }
      } else {
        throw new Error(response.data?.message || 'Failed to fetch dashboard statistics')
      }
    } catch (error) {
      console.error(
        '❌ Error fetching user dashboard stats, trying ICT Officer stats as fallback...',
        error
      )
      // Attempt ICT Officer statistics for officer role
      try {
        const resp2 = await apiClient.get('/ict-officer/statistics')
        if (resp2.data && resp2.data.success) {
          const s = resp2.data.data || {}
          const total = (s.all_time?.total ?? 0) || 0
          const assigned = s.all_time?.assigned ?? 0
          const inProgress = s.all_time?.in_progress ?? 0
          const completed = s.all_time?.completed ?? 0
          const processing = assigned + inProgress
          return {
            success: true,
            data: {
              processing,
              underReview: processing,
              completed,
              grantedAccess: completed,
              revision: 0,
              needsRevision: 0,
              total: total || processing + completed,
              processingPercentage: this.calculatePercentage(
                processing,
                total || processing + completed || 1
              ),
              completedPercentage: this.calculatePercentage(
                completed,
                total || processing + completed || 1
              ),
              revisionPercentage: 0
            }
          }
        }
        throw new Error(resp2.data?.message || 'ICT Officer stats unavailable')
      } catch (e2) {
        console.error('❌ ICT Officer stats fallback failed:', e2)
        // Final fallback: zeros (no mock numbers)
        const zeros = {
          processing: 0,
          underReview: 0,
          completed: 0,
          grantedAccess: 0,
          revision: 0,
          needsRevision: 0,
          total: 0,
          processingPercentage: 0,
          completedPercentage: 0,
          revisionPercentage: 0
        }
        return { success: false, error: e2.message || error.message, data: zeros }
      }
    }
  }

  /**
   * Fetch detailed request status breakdown
   * @returns {Promise<Object>} Detailed status breakdown
   */
  async getRequestStatusBreakdown() {
    try {
      console.log('📊 Fetching request status breakdown...')

      const response = await apiClient.get('/user/request-status-breakdown')

      if (response.data && response.data.success) {
        return {
          success: true,
          data: response.data.data
        }
      } else {
        throw new Error(response.data?.message || 'Failed to fetch request status breakdown')
      }
    } catch (error) {
      console.error('❌ Error fetching request status breakdown:', error)
      return {
        success: false,
        error: error.message,
        data: {}
      }
    }
  }

  /**
   * Calculate percentage with proper rounding
   * @param {number} value
   * @param {number} total
   * @returns {number} Percentage (0-100)
   */
  calculatePercentage(value, total) {
    if (!total || total === 0) return 0
    return Math.round((value / total) * 100)
  }

  /**
   * Get mock statistics for fallback
   * @returns {Object} Mock statistics
   */
  getMockStats() {
    return {
      processing: 12,
      underReview: 12,
      completed: 47,
      grantedAccess: 47,
      revision: 3,
      needsRevision: 3,
      total: 62,
      processingPercentage: 19, // 12/62
      completedPercentage: 76, // 47/62
      revisionPercentage: 5 // 3/62
    }
  }

  /**
   * Get mock admin statistics for fallback
   * @returns {Object} Mock admin statistics
   */
  getAdminMockStats() {
    return {
      totalUsers: 156,
      totalRequests: 1247,
      pendingRequests: 23,
      activeUsers: 5,
      todaysRequests: 45,
      completedRequests: 1224,
      completionRate: 98
    }
  }

  /**
   * Fetch admin dashboard statistics
   * @returns {Promise<Object>} Admin dashboard statistics including total users, requests, etc.
   */
  async getAdminDashboardStats() {
    try {
      console.log('📊 Fetching admin dashboard statistics...')

      const response = await apiClient.get('/admin/dashboard-stats')

      if (response.data && response.data.success) {
        console.log('✅ Admin dashboard stats fetched successfully:', response.data.data)
        return {
          success: true,
          data: {
            totalUsers: response.data.data.total_users || 0,
            totalRequests: response.data.data.total_requests || 0,
            pendingRequests: response.data.data.pending_requests || 0,
            activeUsers: response.data.data.active_users || 0,
            todaysRequests: response.data.data.todays_requests || 0,
            completedRequests: response.data.data.completed_requests || 0,
            completionRate: response.data.data.completion_rate || 0
          }
        }
      } else {
        throw new Error(response.data?.message || 'Failed to fetch admin dashboard statistics')
      }
    } catch (error) {
      console.error('❌ Error fetching admin dashboard stats:', error)

      // Return mock data as fallback to prevent UI breaking
      return {
        success: false,
        error: error.message || 'Failed to fetch admin dashboard statistics',
        data: this.getAdminMockStats()
      }
    }
  }

  /**
   * Refresh dashboard statistics (useful for periodic updates)
   * @returns {Promise<Object>} Fresh dashboard statistics
   */
  async refreshStats() {
    console.log('🔄 Refreshing dashboard statistics...')
    return await this.getUserDashboardStats()
  }

  /**
   * Get user's recent activity summary
   * @param {number} limit - Number of recent activities to fetch
   * @returns {Promise<Object>} Recent activity data
   */
  async getRecentActivity(limit = 10) {
    try {
      console.log('📈 Fetching recent activity...')

      const response = await apiClient.get(`/user/recent-activity?limit=${limit}`)

      if (response.data && response.data.success) {
        return {
          success: true,
          data: response.data.data
        }
      } else {
        throw new Error(response.data?.message || 'Failed to fetch recent activity')
      }
    } catch (error) {
      console.error('❌ Error fetching recent activity:', error)
      return {
        success: false,
        error: error.message,
        data: []
      }
    }
  }
}

export default new DashboardService()
