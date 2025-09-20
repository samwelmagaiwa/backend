<template>
  <div class="access-rights-approval-test-page">
    <div class="container mx-auto px-4 py-8">
      <h1 class="text-3xl font-bold text-gray-800 mb-8">
        Access Rights and Approval Workflow Test
      </h1>

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
            <span>Routes: {{ backendStatus.routes ? 'Available' : 'Failed' }}</span>
          </div>
        </div>
      </div>

      <!-- Database Schema Information -->
      <div class="bg-white shadow-lg rounded-lg p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Database Schema Information</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <h3 class="font-semibold text-gray-700 mb-2">Access Rights Columns</h3>
            <ul class="text-sm text-gray-600 space-y-1">
              <li>• access_type (enum: permanent, temporary)</li>
              <li>• temporary_until (date)</li>
            </ul>
          </div>
          <div>
            <h3 class="font-semibold text-gray-700 mb-2">Approval Workflow Columns</h3>
            <ul class="text-sm text-gray-600 space-y-1">
              <li>• hod_name, hod_signature_path, hod_approved_at</li>
              <li>
                • divisional_director_name, divisional_director_signature_path,
                divisional_approved_at
              </li>
              <li>• ict_director_name, ict_director_signature_path, ict_director_approved_at</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Access Rights and Approval Form -->
      <div class="bg-gray-50 rounded-lg p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4">
          Access Rights and Approval Workflow Form
        </h2>
        <AccessRightsApprovalForm />
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
          <button
            @click="testFileUpload"
            class="px-4 py-2 bg-orange-600 text-white rounded hover:bg-orange-700"
          >
            Test File Upload
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
                access_type: 'permanent', // or "temporary"
                temporary_until: '2024-12-31', // required if temporary
                hod_name: 'Dr. John Doe',
                hod_date: '2024-01-15',
                hod_signature: 'file upload',
                hod_comments: 'Approved for access',
                divisional_director_name: 'Dr. Jane Smith',
                divisional_director_date: '2024-01-16',
                divisional_director_signature: 'file upload',
                divisional_director_comments: 'Approved',
                ict_director_name: 'Mr. Bob Johnson',
                ict_director_date: '2024-01-17',
                ict_director_signature: 'file upload',
                ict_director_comments: 'Final approval granted'
              },
              null,
              2
            )
          }}</pre>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import axios from 'axios'
  import AccessRightsApprovalForm from './forms/AccessRightsApprovalForm.vue'

  export default {
    name: 'AccessRightsApprovalTestPage',
    components: {
      AccessRightsApprovalForm
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

        // Test if routes are available
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

      async testCreateRequest() {
        try {
          const testPayload = {
            user_access_id: 1, // Test ID
            access_type: 'permanent',
            hod_name: 'Dr. Test HOD',
            hod_date: '2024-01-15',
            hod_comments: 'Test approval comment',
            divisional_director_name: 'Dr. Test Divisional Director',
            divisional_director_date: '2024-01-16',
            divisional_director_comments: 'Test divisional approval',
            ict_director_name: 'Mr. Test ICT Director',
            ict_director_date: '2024-01-17',
            ict_director_comments: 'Test ICT approval'
          }

          const response = await axios.post('/api/access-rights-approval', testPayload)
          this.testResults = {
            endpoint: '/api/access-rights-approval',
            method: 'POST',
            payload: testPayload,
            status: 'success',
            data: response.data
          }
        } catch (error) {
          this.testResults = {
            endpoint: '/api/access-rights-approval',
            method: 'POST',
            status: 'error',
            error: error.response?.data || error.message
          }
        }
      },

      async testFileUpload() {
        try {
          // Create a test file (blob)
          const testFileContent = 'Test signature file content'
          const testFile = new Blob([testFileContent], { type: 'text/plain' })

          const formData = new FormData()
          formData.append('user_access_id', '1')
          formData.append('access_type', 'temporary')
          formData.append('temporary_until', '2024-12-31')
          formData.append('hod_name', 'Dr. Test HOD')
          formData.append('hod_date', '2024-01-15')
          formData.append('hod_signature', testFile, 'test_signature.txt')
          formData.append('hod_comments', 'Test file upload')

          const response = await axios.post('/api/access-rights-approval', formData, {
            headers: {
              'Content-Type': 'multipart/form-data'
            }
          })

          this.testResults = {
            endpoint: '/api/access-rights-approval',
            method: 'POST (File Upload)',
            status: 'success',
            data: response.data,
            message: 'File upload test completed'
          }
        } catch (error) {
          this.testResults = {
            endpoint: '/api/access-rights-approval',
            method: 'POST (File Upload)',
            status: 'error',
            error: error.response?.data || error.message
          }
        }
      }
    }
  }
</script>

<style scoped>
  .access-rights-approval-test-page {
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
