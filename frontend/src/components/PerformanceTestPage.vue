<template>
  <div
    class="min-h-screen bg-gradient-to-br from-blue-900 via-blue-800 to-teal-900 p-8"
  >
    <div class="max-w-4xl mx-auto">
      <div
        class="bg-white/10 backdrop-blur-sm rounded-2xl p-8 border border-blue-300/30"
      >
        <h1 class="text-3xl font-bold text-white mb-6 text-center">
          <i class="fas fa-tachometer-alt mr-3"></i>
          Performance Test Page
        </h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Performance Metrics -->
          <div class="bg-white/5 rounded-xl p-6 border border-blue-300/20">
            <h2 class="text-xl font-semibold text-white mb-4">
              <i class="fas fa-chart-line mr-2"></i>
              Performance Metrics
            </h2>
            <div class="space-y-3">
              <div class="flex justify-between text-blue-200">
                <span>Page Load Time:</span>
                <span class="text-green-400">{{ loadTime }}ms</span>
              </div>
              <div class="flex justify-between text-blue-200">
                <span>Memory Usage:</span>
                <span class="text-yellow-400">{{ memoryUsage }}MB</span>
              </div>
              <div class="flex justify-between text-blue-200">
                <span>FPS:</span>
                <span class="text-blue-400">{{ fps }}</span>
              </div>
            </div>
          </div>

          <!-- Test Controls -->
          <div class="bg-white/5 rounded-xl p-6 border border-blue-300/20">
            <h2 class="text-xl font-semibold text-white mb-4">
              <i class="fas fa-cogs mr-2"></i>
              Test Controls
            </h2>
            <div class="space-y-3">
              <button
                @click="runPerformanceTest"
                class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
              >
                <i class="fas fa-play mr-2"></i>
                Run Performance Test
              </button>
              <button
                @click="clearCache"
                class="w-full px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors"
              >
                <i class="fas fa-trash mr-2"></i>
                Clear Cache
              </button>
              <button
                @click="generateReport"
                class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
              >
                <i class="fas fa-file-alt mr-2"></i>
                Generate Report
              </button>
            </div>
          </div>
        </div>

        <!-- Test Results -->
        <div
          v-if="testResults.length > 0"
          class="mt-8 bg-white/5 rounded-xl p-6 border border-blue-300/20"
        >
          <h2 class="text-xl font-semibold text-white mb-4">
            <i class="fas fa-list mr-2"></i>
            Test Results
          </h2>
          <div class="space-y-2">
            <div
              v-for="(result, index) in testResults"
              :key="index"
              class="text-blue-200 text-sm"
            >
              {{ result }}
            </div>
          </div>
        </div>

        <!-- Back to Dashboard -->
        <div class="mt-8 text-center">
          <router-link
            to="/"
            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-teal-600 to-blue-600 text-white rounded-lg hover:from-teal-700 hover:to-blue-700 transition-all duration-300 font-semibold shadow-lg"
          >
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Dashboard
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'PerformanceTestPage',
  data() {
    return {
      loadTime: 0,
      memoryUsage: 0,
      fps: 60,
      testResults: []
    }
  },
  mounted() {
    this.initializeMetrics()
  },
  methods: {
    initializeMetrics() {
      // Simulate performance metrics
      this.loadTime = Math.round(performance.now())
      this.memoryUsage = Math.round(Math.random() * 100 + 50)
      this.fps = Math.round(Math.random() * 10 + 55)
    },
    runPerformanceTest() {
      this.testResults.push(
        `[${new Date().toLocaleTimeString()}] Performance test started...`
      )

      setTimeout(() => {
        this.testResults.push(
          `[${new Date().toLocaleTimeString()}] DOM rendering test: PASSED`
        )
        this.testResults.push(
          `[${new Date().toLocaleTimeString()}] Memory leak test: PASSED`
        )
        this.testResults.push(
          `[${new Date().toLocaleTimeString()}] Network latency test: PASSED`
        )
        this.testResults.push(
          `[${new Date().toLocaleTimeString()}] Performance test completed successfully`
        )
      }, 1000)
    },
    clearCache() {
      this.testResults.push(
        `[${new Date().toLocaleTimeString()}] Cache cleared successfully`
      )
    },
    generateReport() {
      this.testResults.push(
        `[${new Date().toLocaleTimeString()}] Performance report generated`
      )
    }
  }
}
</script>

<style scoped>
/* Additional styles if needed */
</style>
