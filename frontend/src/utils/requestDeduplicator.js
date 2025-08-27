/**
 * Request Deduplication System
 * Prevents duplicate API requests and provides caching
 */

class RequestDeduplicator {
  constructor() {
    this.pendingRequests = new Map()
    this.requestCache = new Map()
    this.cacheTimeout = 5000 // 5 seconds cache
    this.debugMode = process.env.VUE_APP_DEBUG === 'true'
  }

  /**
   * Generate a unique key for the request
   */
  generateKey(method, url, data = null) {
    const dataString = data ? JSON.stringify(data) : ''
    return `${method.toUpperCase()}:${url}:${dataString}`
  }

  /**
   * Check if request is already pending
   */
  isPending(key) {
    return this.pendingRequests.has(key)
  }

  /**
   * Get cached response if available and not expired
   */
  getCachedResponse(key) {
    const cached = this.requestCache.get(key)
    if (cached && Date.now() - cached.timestamp < this.cacheTimeout) {
      if (this.debugMode) {
        console.log(`🔄 Using cached response for: ${key}`)
      }
      return cached.response
    }
    this.requestCache.delete(key)
    return null
  }

  /**
   * Execute request with deduplication
   */
  async executeRequest(key, requestFn) {
    // Check cache first for GET requests
    if (key.startsWith('GET:')) {
      const cachedResponse = this.getCachedResponse(key)
      if (cachedResponse) {
        return cachedResponse
      }
    }

    // Check if request is already pending
    if (this.isPending(key)) {
      if (this.debugMode) {
        console.log(`⏳ Request already pending, waiting for: ${key}`)
      }
      return this.pendingRequests.get(key)
    }

    // Execute new request
    if (this.debugMode) {
      console.log(`🚀 Executing new request: ${key}`)
    }

    const requestPromise = requestFn()
      .then(response => {
        // Cache successful GET responses
        if (key.startsWith('GET:')) {
          this.requestCache.set(key, {
            response,
            timestamp: Date.now()
          })
        }
        return response
      })
      .catch(error => {
        // Don't cache errors
        throw error
      })
      .finally(() => {
        // Remove from pending requests
        this.pendingRequests.delete(key)
      })

    // Store pending request
    this.pendingRequests.set(key, requestPromise)

    return requestPromise
  }

  /**
   * Clear cache for specific pattern
   */
  clearCache(pattern = null) {
    if (pattern) {
      for (const key of this.requestCache.keys()) {
        if (key.includes(pattern)) {
          this.requestCache.delete(key)
          if (this.debugMode) {
            console.log(`🗑️ Cleared cache for: ${key}`)
          }
        }
      }
    } else {
      this.requestCache.clear()
      if (this.debugMode) {
        console.log('🗑️ Cleared all cache')
      }
    }
  }

  /**
   * Clear all pending requests (useful for component unmount)
   */
  clearPending() {
    const count = this.pendingRequests.size
    this.pendingRequests.clear()
    if (this.debugMode && count > 0) {
      console.log(`🗑️ Cleared ${count} pending requests`)
    }
  }

  /**
   * Get statistics for debugging
   */
  getStats() {
    return {
      pendingRequests: this.pendingRequests.size,
      cachedResponses: this.requestCache.size,
      cacheKeys: Array.from(this.requestCache.keys()),
      pendingKeys: Array.from(this.pendingRequests.keys())
    }
  }
}

// Create singleton instance
export const requestDeduplicator = new RequestDeduplicator()

// Add to window for debugging
if (typeof window !== 'undefined' && process.env.VUE_APP_DEBUG === 'true') {
  window.requestDeduplicator = requestDeduplicator
}

export default requestDeduplicator
