<template>
  <div v-if="showMonitor && isDevelopment" class="performance-monitor">
    <div class="monitor-header" @click="toggleExpanded">
      <span class="monitor-title">⚡ Performance</span>
      <span class="monitor-toggle">{{ expanded ? "−" : "+" }}</span>
    </div>

    <div v-if="expanded" class="monitor-content">
      <div class="metric-row">
        <span class="metric-label">Load Time:</span>
        <span class="metric-value" :class="getLoadTimeClass()"
          >{{ loadTime }}ms</span
        >
      </div>

      <div class="metric-row">
        <span class="metric-label">Bundle Size:</span>
        <span class="metric-value">{{ bundleSize }}</span>
      </div>

      <div class="metric-row">
        <span class="metric-label">Memory:</span>
        <span class="metric-value">{{ memoryUsage }}</span>
      </div>

      <div class="metric-row">
        <span class="metric-label">FPS:</span>
        <span class="metric-value" :class="getFpsClass()">{{ fps }}</span>
      </div>

      <div class="metric-row">
        <span class="metric-label">Route:</span>
        <span class="metric-value route-name">{{ currentRoute }}</span>
      </div>

      <div class="monitor-actions">
        <button @click="clearCache" class="action-btn">Clear Cache</button>
        <button @click="exportMetrics" class="action-btn">Export</button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'PerformanceMonitor',
  data() {
    return {
      expanded: false,
      showMonitor: false,
      loadTime: 0,
      bundleSize: 'Unknown',
      memoryUsage: 'Unknown',
      fps: 0,
      currentRoute: '',
      fpsCounter: 0,
      lastTime: performance.now()
    }
  },
  computed: {
    isDevelopment() {
      return process.env.NODE_ENV === 'development'
    }
  },
  mounted() {
    this.initializeMonitor()
  },
  beforeUnmount() {
    this.cleanup()
  },
  watch: {
    $route(to) {
      this.currentRoute = to.name || to.path
    }
  },
  methods: {
    initializeMonitor() {
      // Only show in development or when explicitly enabled
      this.showMonitor =
        this.isDevelopment ||
        localStorage.getItem('showPerformanceMonitor') === 'true'

      if (!this.showMonitor) return

      this.currentRoute = this.$route.name || this.$route.path
      this.measureLoadTime()
      this.measureMemoryUsage()
      this.startFpsMonitoring()
      this.estimateBundleSize()

      // Update metrics periodically
      this.metricsInterval = setInterval(() => {
        this.measureMemoryUsage()
      }, 2000)
    },

    measureLoadTime() {
      if (performance.timing) {
        this.loadTime =
          performance.timing.loadEventEnd - performance.timing.navigationStart
      } else if (performance.getEntriesByType) {
        const navigation = performance.getEntriesByType('navigation')[0]
        if (navigation) {
          this.loadTime = Math.round(
            navigation.loadEventEnd - navigation.fetchStart
          )
        }
      }
    },

    measureMemoryUsage() {
      if (performance.memory) {
        const used = Math.round(
          performance.memory.usedJSHeapSize / 1024 / 1024
        )
        const total = Math.round(
          performance.memory.totalJSHeapSize / 1024 / 1024
        )
        this.memoryUsage = `${used}/${total} MB`
      }
    },

    startFpsMonitoring() {
      const measureFps = (currentTime) => {
        this.fpsCounter++

        if (currentTime >= this.lastTime + 1000) {
          this.fps = Math.round(
            (this.fpsCounter * 1000) / (currentTime - this.lastTime)
          )
          this.fpsCounter = 0
          this.lastTime = currentTime
        }

        if (this.showMonitor) {
          requestAnimationFrame(measureFps)
        }
      }

      requestAnimationFrame(measureFps)
    },

    estimateBundleSize() {
      // Estimate based on loaded resources
      if (performance.getEntriesByType) {
        const resources = performance.getEntriesByType('resource')
        let totalSize = 0

        resources.forEach((resource) => {
          if (resource.transferSize) {
            totalSize += resource.transferSize
          }
        })

        if (totalSize > 0) {
          this.bundleSize = `${Math.round(totalSize / 1024)} KB`
        }
      }
    },

    toggleExpanded() {
      this.expanded = !this.expanded
    },

    getLoadTimeClass() {
      if (this.loadTime < 1000) return 'metric-good'
      if (this.loadTime < 3000) return 'metric-warning'
      return 'metric-poor'
    },

    getFpsClass() {
      if (this.fps >= 55) return 'metric-good'
      if (this.fps >= 30) return 'metric-warning'
      return 'metric-poor'
    },

    clearCache() {
      if ('caches' in window) {
        caches.keys().then((names) => {
          names.forEach((name) => {
            caches.delete(name)
          })
        })
      }

      localStorage.clear()
      sessionStorage.clear()

      alert('Cache cleared! Reload the page to see the effect.')
    },

    exportMetrics() {
      const metrics = {
        timestamp: new Date().toISOString(),
        loadTime: this.loadTime,
        bundleSize: this.bundleSize,
        memoryUsage: this.memoryUsage,
        fps: this.fps,
        route: this.currentRoute,
        userAgent: navigator.userAgent,
        viewport: {
          width: window.innerWidth,
          height: window.innerHeight
        }
      }

      const blob = new Blob([JSON.stringify(metrics, null, 2)], {
        type: 'application/json'
      })
      const url = URL.createObjectURL(blob)
      const a = document.createElement('a')
      a.href = url
      a.download = `performance-metrics-${Date.now()}.json`
      a.click()
      URL.revokeObjectURL(url)
    },

    cleanup() {
      if (this.metricsInterval) {
        clearInterval(this.metricsInterval)
      }
    }
  }
}
</script>

<style scoped>
.performance-monitor {
  position: fixed;
  top: 10px;
  right: 10px;
  background: rgba(0, 0, 0, 0.9);
  color: #00ff00;
  font-family: "Courier New", monospace;
  font-size: 12px;
  border-radius: 4px;
  z-index: 10000;
  min-width: 200px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

.monitor-header {
  padding: 8px 12px;
  cursor: pointer;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 1px solid #333;
  background: rgba(0, 0, 0, 0.95);
}

.monitor-title {
  font-weight: bold;
}

.monitor-toggle {
  font-weight: bold;
  font-size: 14px;
}

.monitor-content {
  padding: 8px 12px;
}

.metric-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 4px;
  align-items: center;
}

.metric-label {
  color: #ccc;
}

.metric-value {
  font-weight: bold;
}

.metric-good {
  color: #00ff00;
}

.metric-warning {
  color: #ffaa00;
}

.metric-poor {
  color: #ff4444;
}

.route-name {
  color: #00aaff;
  font-size: 10px;
  max-width: 100px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.monitor-actions {
  margin-top: 8px;
  display: flex;
  gap: 4px;
}

.action-btn {
  background: #333;
  color: #00ff00;
  border: 1px solid #555;
  padding: 4px 8px;
  font-size: 10px;
  cursor: pointer;
  border-radius: 2px;
  flex: 1;
}

.action-btn:hover {
  background: #444;
}

/* Hide on mobile to avoid clutter */
@media (max-width: 768px) {
  .performance-monitor {
    display: none;
  }
}
</style>
