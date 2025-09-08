/**
 * API Request Debugger
 * Tracks and identifies duplicate API requests
 */

class ApiDebugger {
  constructor() {
    this.requests = []
    this.maxRequests = 100
    this.duplicateThreshold = 5000 // 5 seconds
    this.enabled = process.env.VUE_APP_DEBUG === 'true'
  }

  /**
   * Log an API request
   */
  logRequest(method, url, timestamp = Date.now()) {
    if (!this.enabled) return

    const request = {
      id: `${timestamp}-${Math.random().toString(36).substr(2, 9)}`,
      method: method.toUpperCase(),
      url,
      timestamp,
      time: new Date(timestamp).toLocaleTimeString(),
      date: new Date(timestamp).toLocaleDateString()
    }

    this.requests.unshift(request)

    // Keep only last N requests
    if (this.requests.length > this.maxRequests) {
      this.requests = this.requests.slice(0, this.maxRequests)
    }

    // Check for duplicates
    this.checkForDuplicates(request)

    return request
  }

  /**
   * Check for duplicate requests
   */
  checkForDuplicates(newRequest) {
    const recentDuplicates = this.requests.filter(
      (req) =>
        req.id !== newRequest.id &&
        req.method === newRequest.method &&
        req.url === newRequest.url &&
        newRequest.timestamp - req.timestamp < this.duplicateThreshold
    )

    if (recentDuplicates.length > 0) {
      console.group('🚨 DUPLICATE REQUEST DETECTED')
      console.warn(`Method: ${newRequest.method}`)
      console.warn(`URL: ${newRequest.url}`)
      console.warn(`Count: ${recentDuplicates.length + 1}`)
      console.warn(
        'Times:',
        [newRequest, ...recentDuplicates].map((r) => r.time)
      )
      console.warn('Stack trace:')
      console.trace()
      console.groupEnd()

      // Return duplicate info for programmatic use
      return {
        isDuplicate: true,
        count: recentDuplicates.length + 1,
        requests: [newRequest, ...recentDuplicates]
      }
    }

    return { isDuplicate: false }
  }

  /**
   * Get recent requests
   */
  getRecentRequests(seconds = 10) {
    const cutoff = Date.now() - seconds * 1000
    return this.requests.filter((req) => req.timestamp > cutoff)
  }

  /**
   * Get duplicate requests
   */
  getDuplicates(seconds = 10) {
    const recent = this.getRecentRequests(seconds)
    const groups = {}

    // Group by method:url
    recent.forEach((req) => {
      const key = `${req.method}:${req.url}`
      if (!groups[key]) {
        groups[key] = []
      }
      groups[key].push(req)
    })

    // Filter to only duplicates
    return Object.entries(groups)
      .filter(([, requests]) => requests.length > 1)
      .reduce((acc, [key, requests]) => {
        acc[key] = requests.sort((a, b) => b.timestamp - a.timestamp)
        return acc
      }, {})
  }

  /**
   * Get request statistics
   */
  getStats(seconds = 60) {
    const recent = this.getRecentRequests(seconds)
    const duplicates = this.getDuplicates(seconds)

    const methodCounts = {}
    const urlCounts = {}

    recent.forEach((req) => {
      methodCounts[req.method] = (methodCounts[req.method] || 0) + 1
      urlCounts[req.url] = (urlCounts[req.url] || 0) + 1
    })

    return {
      totalRequests: recent.length,
      duplicateGroups: Object.keys(duplicates).length,
      totalDuplicates: Object.values(duplicates).reduce((sum, group) => sum + group.length, 0),
      methodCounts,
      urlCounts: Object.entries(urlCounts)
        .sort(([, a], [, b]) => b - a)
        .slice(0, 10), // Top 10 URLs
      timeRange: `${seconds} seconds`
    }
  }

  /**
   * Print a detailed report
   */
  printReport(seconds = 60) {
    if (!this.enabled) {
      console.log('API Debugger is disabled. Set VUE_APP_DEBUG=true to enable.')
      return
    }

    const stats = this.getStats(seconds)
    const duplicates = this.getDuplicates(seconds)

    console.group(`📊 API Request Report (Last ${seconds} seconds)`)

    console.log(`Total Requests: ${stats.totalRequests}`)
    console.log(`Duplicate Groups: ${stats.duplicateGroups}`)
    console.log(`Total Duplicates: ${stats.totalDuplicates}`)

    if (stats.totalDuplicates > 0) {
      console.group('🚨 Duplicate Requests')
      Object.entries(duplicates).forEach(([key, requests]) => {
        console.group(`${key} (${requests.length} requests)`)
        requests.forEach((req) => {
          console.log(`${req.time} - ${req.id}`)
        })
        console.groupEnd()
      })
      console.groupEnd()
    }

    console.group('📈 Method Distribution')
    Object.entries(stats.methodCounts).forEach(([method, count]) => {
      console.log(`${method}: ${count}`)
    })
    console.groupEnd()

    console.group('🔗 Top URLs')
    stats.urlCounts.forEach(([url, count]) => {
      console.log(`${url}: ${count}`)
    })
    console.groupEnd()

    console.groupEnd()
  }

  /**
   * Clear request history
   */
  clear() {
    this.requests = []
    console.log('🗑️ API request history cleared')
  }

  /**
   * Export request data
   */
  export(seconds = 60) {
    return {
      requests: this.getRecentRequests(seconds),
      duplicates: this.getDuplicates(seconds),
      stats: this.getStats(seconds),
      exportTime: new Date().toISOString()
    }
  }

  /**
   * Monitor a specific endpoint
   */
  monitorEndpoint(endpoint, seconds = 30) {
    const requests = this.getRecentRequests(seconds)
      .filter((req) => req.url.includes(endpoint))
      .sort((a, b) => b.timestamp - a.timestamp)

    console.group(`🔍 Monitoring: ${endpoint}`)
    console.log(`Requests in last ${seconds} seconds: ${requests.length}`)

    if (requests.length > 0) {
      requests.forEach((req) => {
        console.log(`${req.method} ${req.url} at ${req.time}`)
      })
    } else {
      console.log('No requests found')
    }

    console.groupEnd()

    return requests
  }
}

// Create singleton instance
export const apiDebugger = new ApiDebugger()

// Add to window for debugging
if (typeof window !== 'undefined' && process.env.VUE_APP_DEBUG === 'true') {
  window.apiDebugger = apiDebugger

  // Add helpful debugging commands
  window.debugAPI = {
    report: (seconds = 60) => apiDebugger.printReport(seconds),
    duplicates: (seconds = 10) => apiDebugger.getDuplicates(seconds),
    monitor: (endpoint, seconds = 30) => apiDebugger.monitorEndpoint(endpoint, seconds),
    clear: () => apiDebugger.clear(),
    export: (seconds = 60) => apiDebugger.export(seconds)
  }

  console.log('🔧 API Debugging tools available:')
  console.log('  window.debugAPI.report() - Show request report')
  console.log('  window.debugAPI.duplicates() - Show duplicate requests')
  console.log('  window.debugAPI.monitor("endpoint") - Monitor specific endpoint')
  console.log('  window.debugAPI.clear() - Clear request history')
  console.log('  window.debugAPI.export() - Export request data')
}

export default apiDebugger
