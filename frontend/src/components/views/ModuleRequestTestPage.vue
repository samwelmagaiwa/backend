<template>
  <div class="module-request-test-page">
    <div class="container mx-auto px-4 py-8">
      <h1 class="text-3xl font-bold text-gray-800 mb-8">Module Request System Test</h1>

      <!-- Test Status -->
      <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <h2 class="text-lg font-semibold text-blue-800 mb-2">Backend Status</h2>
        <div class="space-y-2">
          <div class="flex items-center">
            <span
              class="w-4 h-4 rounded-full mr-2"
              :class="backendStatus.api ? 'bg-green-500' : 'bg-red-500'"
            ></span>
            <span>API Connection: {{ backendStatus.api ? 'Connected' : 'Failed' }}</span>
          </div>
          <div class="flex items-center">
            <span
              class="w-4 h-4 rounded-full mr-2"
              :class="backendStatus.modules ? 'bg-green-500' : 'bg-red-500'"
            ></span>
            <span>Modules Endpoint: {{ backendStatus.modules ? 'Working' : 'Failed' }}</span>
          </div>
          <div class="flex items-center">
            <span
              class="w-4 h-4 rounded-full mr-2"
              :class="backendStatus.database ? 'bg-green-500' : 'bg-red-500'"
            ></span>
            <span>Database: {{ backendStatus.database ? 'Connected' : 'Failed' }}</span>
          </div>
        </div>
      </div>

      <!-- Available Modules Display -->
      <div class="bg-white shadow-lg rounded-lg p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Available Wellsoft Modules</h2>
        <div v-if="availableModules.length > 0" class="grid grid-cols-3 gap-4">
          <div
            v-for="module in availableModules"
            :key="module.id"
            class="p-3 border border-gray-200 rounded-lg"
          >
            <h3 class="font-semibold">{{ module.name }}</h3>
            <p class="text-sm text-gray-600">{{ module.description }}</p>
          </div>
        </div>
        <div v-else class="text-gray-500">No modules available or failed to load.</div>
      </div>

      <!-- Module Request Form -->
      <ModuleRequestForm />

      <!-- API Test Section -->
      <div class="bg-gray-50 rounded-lg p-6 mt-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4">API Test Section</h2>
        <div class="space-y-4">
          <button
            @click="testModulesEndpoint"
            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
          >
            Test Modules Endpoint
          </button>
          <button
            @click="testCreateRequest"
            class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"
          >
            Test Create Request
          </button>
          <button
            @click="checkBackendHealth"
            class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700"
          >
            Check Backend Health
          </button>
        </div>

        <!-- Test Results -->
        <div v-if="testResults" class="mt-4 p-4 bg-white rounded border">
          <h3 class="font-semibold mb-2">Test Results:</h3>
          <pre class="text-xs overflow-auto">{{ JSON.stringify(testResults, null, 2) }}</pre>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import axios from 'axios'
  import ModuleRequestForm from './forms/ModuleRequestForm.vue'

  export default {
    name: 'ModuleRequestTestPage',
    components: {
      ModuleRequestForm
    },
    data() {
      return {
        backendStatus: {
          api: false,
          modules: false,
          database: false
        },
        availableModules: [],
        testResults: null
      }
    },
    async mounted() {
      await this.checkBackendHealth()
      await this.loadAvailableModules()
    },
    methods: {
      async checkBackendHealth() {
        try {
          // Test basic API connection
          const healthResponse = await axios.get('/api/health')
          this.backendStatus.api = true
          this.backendStatus.database = healthResponse.data.status === 'ok'

          console.log('Health check response:', healthResponse.data)
        } catch (error) {
          console.error('Health check failed:', error)
          this.backendStatus.api = false
          this.backendStatus.database = false
        }
      },

      async loadAvailableModules() {
        try {
          const response = await axios.get('/api/module-requests/modules')
          this.availableModules = response.data.data || []
          this.backendStatus.modules = true

          console.log('Available modules:', this.availableModules)
        } catch (error) {
          console.error('Error loading modules:', error)
          this.backendStatus.modules = false
          this.availableModules = []
        }
      },

      async testModulesEndpoint() {
        try {
          const response = await axios.get('/api/module-requests/modules')
          this.testResults = {
            endpoint: '/api/module-requests/modules',
            status: 'success',
            data: response.data
          }
        } catch (error) {
          this.testResults = {
            endpoint: '/api/module-requests/modules',
            status: 'error',
            error: error.response?.data || error.message
          }
        }
      },

      async testCreateRequest() {
        try {
          const testPayload = {
            user_access_id: 1, // Test ID
            module_requested_for: 'use',
            wellsoft_modules: ['Registrar', 'Cashier']
          }

          const response = await axios.post('/api/module-requests', testPayload)
          this.testResults = {
            endpoint: '/api/module-requests',
            method: 'POST',
            payload: testPayload,
            status: 'success',
            data: response.data
          }
        } catch (error) {
          this.testResults = {
            endpoint: '/api/module-requests',
            method: 'POST',
            status: 'error',
            error: error.response?.data || error.message
          }
        }
      }
    }
  }
</script>

<style scoped>
  .module-request-test-page {
    min-height: 100vh;
    background-color: #f9fafb;
  }

  .container {
    max-width: 1200px;
  }

  .grid-cols-3 {
    grid-template-columns: repeat(3, minmax(0, 1fr));
  }

  .gap-4 {
    gap: 1rem;
  }

  .space-y-2 > * + * {
    margin-top: 0.5rem;
  }

  .space-y-4 > * + * {
    margin-top: 1rem;
  }
</style>
