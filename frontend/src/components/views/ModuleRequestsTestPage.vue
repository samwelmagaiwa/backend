<template>
  <div class="module-requests-test-page">
    <div class="container mx-auto px-4 py-8">
      <h1 class="text-3xl font-bold text-gray-800 mb-8">Module Requests System Test</h1>

      <!-- Test Status -->
      <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <h2 class="text-lg font-semibold text-blue-800 mb-2">Backend Status</h2>
        <div class="grid grid-cols-2 gap-4">
          <div class="space-y-2">
            <h3 class="font-medium text-blue-700">Wellsoft Modules</h3>
            <div class="flex items-center">
              <span
                class="w-4 h-4 rounded-full mr-2"
                :class="backendStatus.wellsoft.api ? 'bg-green-500' : 'bg-red-500'"
              ></span>
              <span>API Connection: {{ backendStatus.wellsoft.api ? 'Connected' : 'Failed' }}</span>
            </div>
            <div class="flex items-center">
              <span
                class="w-4 h-4 rounded-full mr-2"
                :class="backendStatus.wellsoft.modules ? 'bg-green-500' : 'bg-red-500'"
              ></span>
              <span
                >Modules Endpoint: {{ backendStatus.wellsoft.modules ? 'Working' : 'Failed' }}</span
              >
            </div>
          </div>
          <div class="space-y-2">
            <h3 class="font-medium text-blue-700">Jeeva Modules</h3>
            <div class="flex items-center">
              <span
                class="w-4 h-4 rounded-full mr-2"
                :class="backendStatus.jeeva.api ? 'bg-green-500' : 'bg-red-500'"
              ></span>
              <span>API Connection: {{ backendStatus.jeeva.api ? 'Connected' : 'Failed' }}</span>
            </div>
            <div class="flex items-center">
              <span
                class="w-4 h-4 rounded-full mr-2"
                :class="backendStatus.jeeva.modules ? 'bg-green-500' : 'bg-red-500'"
              ></span>
              <span
                >Modules Endpoint: {{ backendStatus.jeeva.modules ? 'Working' : 'Failed' }}</span
              >
            </div>
          </div>
        </div>
      </div>

      <!-- Available Modules Display -->
      <div class="grid grid-cols-2 gap-6 mb-6">
        <!-- Wellsoft Modules -->
        <div class="bg-white shadow-lg rounded-lg p-6">
          <h2 class="text-xl font-bold text-gray-800 mb-4">Available Wellsoft Modules</h2>
          <div v-if="wellsoftModules.length > 0" class="space-y-2 max-h-64 overflow-y-auto">
            <div
              v-for="module in wellsoftModules"
              :key="module.id"
              class="p-2 border border-gray-200 rounded text-sm"
            >
              <strong>{{ module.name }}</strong>
              <p class="text-gray-600 text-xs">{{ module.description }}</p>
            </div>
          </div>
          <div v-else class="text-gray-500">No Wellsoft modules available or failed to load.</div>
          <p class="text-sm text-gray-600 mt-2">Total: {{ wellsoftModules.length }} modules</p>
        </div>

        <!-- Jeeva Modules -->
        <div class="bg-white shadow-lg rounded-lg p-6">
          <h2 class="text-xl font-bold text-gray-800 mb-4">Available Jeeva Modules</h2>
          <div v-if="jeevaModules.length > 0" class="space-y-2 max-h-64 overflow-y-auto">
            <div
              v-for="module in jeevaModules"
              :key="module.id"
              class="p-2 border border-gray-200 rounded text-sm"
            >
              <strong>{{ module.name }}</strong>
              <p class="text-gray-600 text-xs">{{ module.description }}</p>
            </div>
          </div>
          <div v-else class="text-gray-500">No Jeeva modules available or failed to load.</div>
          <p class="text-sm text-gray-600 mt-2">Total: {{ jeevaModules.length }} modules</p>
        </div>
      </div>

      <!-- Module Request Forms -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Wellsoft Module Request Form -->
        <div class="bg-gray-50 rounded-lg p-6">
          <h2 class="text-xl font-bold text-gray-800 mb-4">Wellsoft Module Request</h2>
          <ModuleRequestForm />
        </div>

        <!-- Jeeva Module Request Form -->
        <div class="bg-gray-50 rounded-lg p-6">
          <h2 class="text-xl font-bold text-gray-800 mb-4">Jeeva Module Request</h2>
          <JeevaModuleRequestForm />
        </div>
      </div>

      <!-- API Test Section -->
      <div class="bg-gray-50 rounded-lg p-6 mt-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4">API Test Section</h2>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <h3 class="font-semibold mb-2">Wellsoft API Tests</h3>
            <div class="space-y-2">
              <button
                @click="testWellsoftModulesEndpoint"
                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm"
              >
                Test Wellsoft Modules
              </button>
              <button
                @click="testWellsoftCreateRequest"
                class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm"
              >
                Test Wellsoft Create
              </button>
            </div>
          </div>
          <div>
            <h3 class="font-semibold mb-2">Jeeva API Tests</h3>
            <div class="space-y-2">
              <button
                @click="testJeevaModulesEndpoint"
                class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 text-sm"
              >
                Test Jeeva Modules
              </button>
              <button
                @click="testJeevaCreateRequest"
                class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 text-sm"
              >
                Test Jeeva Create
              </button>
            </div>
          </div>
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
  import JeevaModuleRequestForm from './forms/JeevaModuleRequestForm.vue'

  export default {
    name: 'ModuleRequestsTestPage',
    components: {
      ModuleRequestForm,
      JeevaModuleRequestForm
    },
    data() {
      return {
        backendStatus: {
          wellsoft: {
            api: false,
            modules: false
          },
          jeeva: {
            api: false,
            modules: false
          }
        },
        wellsoftModules: [],
        jeevaModules: [],
        testResults: null
      }
    },
    async mounted() {
      await this.checkBackendHealth()
      await this.loadWellsoftModules()
      await this.loadJeevaModules()
    },
    methods: {
      async checkBackendHealth() {
        try {
          // Test basic API connection
          const healthResponse = await axios.get('/api/health')
          this.backendStatus.wellsoft.api = true
          this.backendStatus.jeeva.api = true

          console.log('Health check response:', healthResponse.data)
        } catch (error) {
          console.error('Health check failed:', error)
          this.backendStatus.wellsoft.api = false
          this.backendStatus.jeeva.api = false
        }
      },

      async loadWellsoftModules() {
        try {
          const response = await axios.get('/api/module-requests/modules')
          this.wellsoftModules = response.data.data || []
          this.backendStatus.wellsoft.modules = true

          console.log('Wellsoft modules:', this.wellsoftModules.length)
        } catch (error) {
          console.error('Error loading Wellsoft modules:', error)
          this.backendStatus.wellsoft.modules = false
          this.wellsoftModules = []
        }
      },

      async loadJeevaModules() {
        try {
          const response = await axios.get('/api/module-requests/jeeva/modules')
          this.jeevaModules = response.data.data || []
          this.backendStatus.jeeva.modules = true

          console.log('Jeeva modules:', this.jeevaModules.length)
        } catch (error) {
          console.error('Error loading Jeeva modules:', error)
          this.backendStatus.jeeva.modules = false
          this.jeevaModules = []
        }
      },

      async testWellsoftModulesEndpoint() {
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

      async testJeevaModulesEndpoint() {
        try {
          const response = await axios.get('/api/module-requests/jeeva/modules')
          this.testResults = {
            endpoint: '/api/module-requests/jeeva/modules',
            status: 'success',
            data: response.data
          }
        } catch (error) {
          this.testResults = {
            endpoint: '/api/module-requests/jeeva/modules',
            status: 'error',
            error: error.response?.data || error.message
          }
        }
      },

      async testWellsoftCreateRequest() {
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
      },

      async testJeevaCreateRequest() {
        try {
          const testPayload = {
            user_access_id: 1, // Test ID
            jeeva_modules: ['FINANCIAL ACCOUNTING', 'DOCTOR CONSULTATION', 'OUTPATIENT']
          }

          const response = await axios.post('/api/module-requests/jeeva', testPayload)
          this.testResults = {
            endpoint: '/api/module-requests/jeeva',
            method: 'POST',
            payload: testPayload,
            status: 'success',
            data: response.data
          }
        } catch (error) {
          this.testResults = {
            endpoint: '/api/module-requests/jeeva',
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
  .module-requests-test-page {
    min-height: 100vh;
    background-color: #f9fafb;
  }

  .container {
    max-width: 1400px;
  }

  .grid-cols-2 {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }

  .grid-cols-3 {
    grid-template-columns: repeat(3, minmax(0, 1fr));
  }

  .gap-2 {
    gap: 0.5rem;
  }

  .gap-4 {
    gap: 1rem;
  }

  .gap-6 {
    gap: 1.5rem;
  }

  .space-y-2 > * + * {
    margin-top: 0.5rem;
  }

  .space-y-4 > * + * {
    margin-top: 1rem;
  }

  .max-h-64 {
    max-height: 16rem;
  }

  .overflow-y-auto {
    overflow-y: auto;
  }
</style>
