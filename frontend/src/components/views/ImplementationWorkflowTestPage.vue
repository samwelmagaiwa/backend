<template>
  <div class="implementation-workflow-test-page">
    <div class="container mx-auto px-4 py-8">
      <h1 class="text-3xl font-bold text-gray-800 mb-8">Implementation Workflow Test</h1>

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
              :class="backendStatus.database ? 'bg-green-500' : 'bg-red-500'"
            ></span>
            <span>Database: {{ backendStatus.database ? 'Connected' : 'Failed' }}</span>
          </div>
          <div class="flex items-center">
            <span
              class="w-4 h-4 rounded-full mr-2"
              :class="backendStatus.routes ? 'bg-green-500' : 'bg-red-500'"
            ></span>
            <span>Implementation Routes: {{ backendStatus.routes ? 'Available' : 'Failed' }}</span>
          </div>
        </div>
      </div>

      <!-- Database Schema Information -->
      <div class="bg-white shadow-lg rounded-lg p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Database Schema Information</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <h3 class="font-semibold text-gray-700 mb-2">Head of IT Columns</h3>
            <ul class="text-sm text-gray-600 space-y-1">
              <li>• head_it_name</li>
              <li>• head_it_signature_path</li>
              <li>• head_it_approved_at</li>
              <li>• head_it_comments</li>
            </ul>
          </div>
          <div>
            <h3 class="font-semibold text-gray-700 mb-2">ICT Officer Columns</h3>
            <ul class="text-sm text-gray-600 space-y-1">
              <li>• ict_officer_name</li>
              <li>• ict_officer_signature_path</li>
              <li>• ict_officer_implemented_at</li>
              <li>• ict_officer_comments</li>
              <li>• implementation_comments</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Implementation Workflow Form -->
      <div class="bg-gray-50 rounded-lg p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Implementation Workflow Form</h2>
        <ImplementationWorkflowForm />
      </div>

      <!-- API Test Section -->
      <div class="bg-gray-50 rounded-lg p-6 mt-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4">API Test Section</h2>
        <div class="space-y-4">
          <button
            @click="testApiEndpoint"
            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
          >
            Test API Endpoint
          </button>
          <button
            @click="testCreateImplementation"
            class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"
          >
            Test Create Implementation
          </button>
          <button
            @click="checkBackendHealth"
            class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700"
          >
            Check Backend Health
          </button>
          <button
            @click="testFileUpload"
            class="px-4 py-2 bg-orange-600 text-white rounded hover:bg-orange-700"
          >
            Test File Upload
          </button>
          <button
            @click="testCompleteWorkflow"
            class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700"
          >
            Test Complete Workflow
          </button>
        </div>

        <!-- Test Results -->
        <div v-if="testResults" class="mt-4 p-4 bg-white rounded border">
          <h3 class="font-semibold mb-2">Test Results:</h3>
          <pre class="text-xs overflow-auto">{{ JSON.stringify(testResults, null, 2) }}</pre>
        </div>
      </div>

      <!-- Sample Data Display -->
      <div class="bg-white shadow-lg rounded-lg p-6 mt-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Sample Request Data</h2>
        <div class="bg-gray-100 p-4 rounded">
          <h3 class="font-semibold mb-2">Expected API Request Format:</h3>
          <pre class="text-xs">{{
            JSON.stringify(
              {
                user_access_id: 1,
                head_it_name: 'Dr. John Smith',
                head_it_date: '2024-01-20',
                head_it_signature: 'file upload',
                head_it_comments: 'Approved for implementation',
                ict_officer_name: 'Mr. Bob Wilson',
                ict_officer_date: '2024-01-21',
                ict_officer_signature: 'file upload',
                ict_officer_comments: 'Access granted successfully',
                implementation_comments: 'Implementation completed without issues'
              },
              null,
              2
            )
          }}</pre>
        </div>
      </div>

      <!-- Workflow Progress Visualization -->
      <div class="bg-white shadow-lg rounded-lg p-6 mt-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Complete Workflow Progress</h2>
        <div class="space-y-4">
          <div class="flex items-center space-x-4">
            <div
              class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center text-white text-sm"
            >
              1
            </div>
            <span class="text-sm">HOD/BM Approval</span>
          </div>
          <div class="flex items-center space-x-4">
            <div
              class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center text-white text-sm"
            >
              2
            </div>
            <span class="text-sm">Divisional Director Approval</span>
          </div>
          <div class="flex items-center space-x-4">
            <div
              class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center text-white text-sm"
            >
              3
            </div>
            <span class="text-sm">ICT Director Approval</span>
          </div>
          <div class="flex items-center space-x-4">
            <div
              class="w-8 h-8 bg-indigo-500 rounded-full flex items-center justify-center text-white text-sm"
            >
              4
            </div>
            <span class="text-sm font-semibold text-indigo-700">Head of IT Approval</span>
          </div>
          <div class="flex items-center space-x-4">
            <div
              class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center text-white text-sm"
            >
              5
            </div>
            <span class="text-sm font-semibold text-orange-700">ICT Officer Implementation</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import axios from 'axios'
  import ImplementationWorkflowForm from './forms/ImplementationWorkflowForm.vue'

  export default {
    name: 'ImplementationWorkflowTestPage',
    components: {
      ImplementationWorkflowForm
    },
    data() {
      return {
        backendStatus: {
          api: false,
          database: false,
          routes: false
        },
        testResults: null
      }
    },
    async mounted() {
      await this.checkBackendHealth()
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

        // Test if implementation routes are available
        try {
          const _response = await axios.get('/api/v1/user-access')
          this.backendStatus.routes = true
        } catch (error) {
          console.error('Routes check failed:', error)
          this.backendStatus.routes = false
        }
      },

      async testApiEndpoint() {
        try {
          // Test getting user access requests
          const response = await axios.get('/api/v1/user-access')
          this.testResults = {
            endpoint: '/api/v1/user-access',
            status: 'success',
            data: response.data,
            message: 'User access requests retrieved successfully'
          }
        } catch (error) {
          this.testResults = {
            endpoint: '/api/v1/user-access',
            status: 'error',
            error: error.response?.data || error.message
          }
        }
      },

      async testCreateImplementation() {
        try {
          const testPayload = {
            user_access_id: 1, // Test ID
            head_it_name: 'Dr. Test Head of IT',
            head_it_date: '2024-01-20',
            head_it_comments: 'Test Head of IT approval',
            ict_officer_name: 'Mr. Test ICT Officer',
            ict_officer_date: '2024-01-21',
            ict_officer_comments: 'Test ICT Officer implementation',
            implementation_comments: 'Test implementation completed'
          }

          const response = await axios.post('/api/implementation-workflow', testPayload)
          this.testResults = {
            endpoint: '/api/implementation-workflow',
            method: 'POST',
            payload: testPayload,
            status: 'success',
            data: response.data
          }
        } catch (error) {
          this.testResults = {
            endpoint: '/api/implementation-workflow',
            method: 'POST',
            status: 'error',
            error: error.response?.data || error.message
          }
        }
      },

      async testFileUpload() {
        try {
          // Create test files (blobs)
          const testHeadItFile = new Blob(['Test Head of IT signature'], { type: 'text/plain' })
          const testIctOfficerFile = new Blob(['Test ICT Officer signature'], {
            type: 'text/plain'
          })

          const formData = new FormData()
          formData.append('user_access_id', '1')
          formData.append('head_it_name', 'Dr. Test Head of IT')
          formData.append('head_it_date', '2024-01-20')
          formData.append('head_it_signature', testHeadItFile, 'head_it_signature.txt')
          formData.append('head_it_comments', 'Test Head of IT file upload')
          formData.append('ict_officer_name', 'Mr. Test ICT Officer')
          formData.append('ict_officer_date', '2024-01-21')
          formData.append('ict_officer_signature', testIctOfficerFile, 'ict_officer_signature.txt')
          formData.append('ict_officer_comments', 'Test ICT Officer file upload')
          formData.append('implementation_comments', 'Test implementation with file uploads')

          const response = await axios.post('/api/implementation-workflow', formData, {
            headers: {
              'Content-Type': 'multipart/form-data'
            }
          })

          this.testResults = {
            endpoint: '/api/implementation-workflow',
            method: 'POST (File Upload)',
            status: 'success',
            data: response.data,
            message: 'File upload test completed'
          }
        } catch (error) {
          this.testResults = {
            endpoint: '/api/implementation-workflow',
            method: 'POST (File Upload)',
            status: 'error',
            error: error.response?.data || error.message
          }
        }
      },

      async testCompleteWorkflow() {
        try {
          // Test getting implementation workflow details
          const response = await axios.get('/api/implementation-workflow/1')
          this.testResults = {
            endpoint: '/api/implementation-workflow/1',
            method: 'GET',
            status: 'success',
            data: response.data,
            message: 'Implementation workflow details retrieved'
          }
        } catch (error) {
          this.testResults = {
            endpoint: '/api/implementation-workflow/1',
            method: 'GET',
            status: 'error',
            error: error.response?.data || error.message
          }
        }
      }
    }
  }
</script>

<style scoped>
  .implementation-workflow-test-page {
    min-height: 100vh;
    background-color: #f9fafb;
  }

  .container {
    max-width: 1200px;
  }

  .grid-cols-1 {
    grid-template-columns: repeat(1, minmax(0, 1fr));
  }

  .gap-6 {
    gap: 1.5rem;
  }

  .space-y-1 > * + * {
    margin-top: 0.25rem;
  }

  .space-y-2 > * + * {
    margin-top: 0.5rem;
  }

  .space-y-4 > * + * {
    margin-top: 1rem;
  }

  @media (min-width: 768px) {
    .md\:grid-cols-2 {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }
  }
</style>
