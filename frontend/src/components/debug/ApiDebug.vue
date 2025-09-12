<template>
  <div class="p-4 bg-gray-100 rounded-lg max-w-4xl mx-auto">
    <h3 class="text-lg font-bold mb-4">API Debug Tool</h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <!-- Authentication Status -->
      <div class="bg-white p-4 rounded shadow">
        <h4 class="font-semibold mb-2">Authentication Status</h4>
        <div class="space-y-2 text-sm">
          <div>Token: {{ authToken ? 'Present' : 'Missing' }}</div>
          <div>Token Length: {{ authToken?.length || 0 }}</div>
          <div>User Data: {{ userData ? 'Present' : 'Missing' }}</div>
          <div>User ID: {{ userData?.id || 'N/A' }}</div>
          <div>User Role: {{ userData?.role || 'N/A' }}</div>
        </div>
      </div>

      <!-- API Configuration -->
      <div class="bg-white p-4 rounded shadow">
        <h4 class="font-semibold mb-2">API Configuration</h4>
        <div class="space-y-2 text-sm">
          <div>Base URL: {{ apiBaseUrl }}</div>
          <div>Timeout: {{ apiTimeout }}ms</div>
          <div>Environment: {{ nodeEnv }}</div>
        </div>
      </div>

      <!-- Test Results -->
      <div class="bg-white p-4 rounded shadow md:col-span-2">
        <h4 class="font-semibold mb-2">Test Results</h4>
        <div class="space-y-2">
          <button
            @click="testHealthEndpoint"
            class="bg-blue-500 text-white px-4 py-2 rounded mr-2"
            :disabled="testing"
          >
            Test Health
          </button>
          <button
            @click="testAuthEndpoint"
            class="bg-green-500 text-white px-4 py-2 rounded mr-2"
            :disabled="testing"
          >
            Test Auth
          </button>
          <button
            @click="testPendingRequests"
            class="bg-orange-500 text-white px-4 py-2 rounded"
            :disabled="testing"
          >
            Test Pending Requests
          </button>
        </div>

        <div v-if="testResults.length > 0" class="mt-4">
          <div
            v-for="(result, index) in testResults"
            :key="index"
            class="p-2 mb-2 rounded text-sm"
            :class="result.success ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
          >
            <strong>{{ result.test }}:</strong> {{ result.message }}
            <div v-if="result.data" class="mt-1">
              <pre class="text-xs overflow-auto">{{ JSON.stringify(result.data, null, 2) }}</pre>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import apiClient from '@/utils/apiClient'
  import { API_CONFIG } from '@/utils/config'

  export default {
    name: 'ApiDebug',
    data() {
      return {
        testing: false,
        testResults: []
      }
    },
    computed: {
      authToken() {
        return localStorage.getItem('auth_token')
      },
      userData() {
        const data = localStorage.getItem('user_data')
        return data ? JSON.parse(data) : null
      },
      apiBaseUrl() {
        return API_CONFIG.BASE_URL
      },
      apiTimeout() {
        return API_CONFIG.TIMEOUT
      },
      nodeEnv() {
        return process.env.NODE_ENV
      }
    },
    methods: {
      addResult(test, success, message, data = null) {
        this.testResults.unshift({
          test,
          success,
          message,
          data,
          timestamp: new Date().toLocaleTimeString()
        })
      },

      async testHealthEndpoint() {
        this.testing = true
        try {
          const response = await apiClient.get('/health')
          this.addResult('Health Check', true, 'API is responding', response.data)
        } catch (error) {
          this.addResult('Health Check', false, error.message, {
            status: error.response?.status,
            data: error.response?.data
          })
        } finally {
          this.testing = false
        }
      },

      async testAuthEndpoint() {
        this.testing = true
        try {
          const response = await apiClient.get('/user')
          this.addResult('Auth Check', true, 'Authentication working', response.data)
        } catch (error) {
          this.addResult('Auth Check', false, error.message, {
            status: error.response?.status,
            data: error.response?.data
          })
        } finally {
          this.testing = false
        }
      },

      async testPendingRequests() {
        this.testing = true
        try {
          const response = await apiClient.get('/booking-service/check-pending-requests')
          this.addResult('Pending Requests', true, 'Endpoint working', response.data)
        } catch (error) {
          this.addResult('Pending Requests', false, error.message, {
            status: error.response?.status,
            data: error.response?.data
          })
        } finally {
          this.testing = false
        }
      }
    }
  }
</script>
