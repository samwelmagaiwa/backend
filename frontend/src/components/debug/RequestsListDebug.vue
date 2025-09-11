<template>
  <div class="min-h-screen bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
      <h1 class="text-3xl font-bold text-gray-900 mb-8">ICT Approval Requests Debug</h1>

      <!-- Debug Info -->
      <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-xl font-semibold mb-4">Debug Information</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <h3 class="font-medium text-gray-700">Authentication Status</h3>
            <p class="text-sm text-gray-600">Authenticated: {{ isAuthenticated }}</p>
            <p class="text-sm text-gray-600">User Role: {{ userRole }}</p>
            <p class="text-sm text-gray-600">User Name: {{ userName }}</p>
          </div>
          <div>
            <h3 class="font-medium text-gray-700">Component Status</h3>
            <p class="text-sm text-gray-600">Loading: {{ isLoading }}</p>
            <p class="text-sm text-gray-600">Error: {{ error || 'None' }}</p>
            <p class="text-sm text-gray-600">Requests Count: {{ requests.length }}</p>
          </div>
        </div>
      </div>

      <!-- Error Display -->
      <div
        v-if="error"
        class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-8"
      >
        <h3 class="font-bold">Error:</h3>
        <p>{{ error }}</p>
      </div>

      <!-- Loading State -->
      <div
        v-if="isLoading"
        class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-8"
      >
        <p>Loading requests...</p>
      </div>

      <!-- Service Test -->
      <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-xl font-semibold mb-4">Service Test</h2>
        <button
          @click="testService"
          :disabled="isTestingService"
          class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50"
        >
          {{ isTestingService ? 'Testing...' : 'Test Device Borrowing Service' }}
        </button>

        <div v-if="serviceTestResult" class="mt-4">
          <h3 class="font-medium text-gray-700">Service Test Result:</h3>
          <pre class="bg-gray-100 p-4 rounded text-sm overflow-auto">{{
            JSON.stringify(serviceTestResult, null, 2)
          }}</pre>
        </div>
      </div>

      <!-- Requests Display -->
      <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold mb-4">Requests ({{ requests.length }})</h2>

        <div v-if="requests.length === 0 && !isLoading" class="text-gray-500 text-center py-8">
          No requests found
        </div>

        <div v-else class="space-y-4">
          <div
            v-for="request in requests"
            :key="request.id"
            class="border border-gray-200 rounded p-4"
          >
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <p class="font-medium">ID: {{ request.id }}</p>
                <p class="text-sm text-gray-600">
                  Borrower: {{ request.borrower_name || 'Unknown' }}
                </p>
              </div>
              <div>
                <p class="text-sm text-gray-600">
                  Device: {{ request.device_name || request.device_type || 'Unknown' }}
                </p>
                <p class="text-sm text-gray-600">
                  Department: {{ request.department || 'Unknown' }}
                </p>
              </div>
              <div>
                <p class="text-sm text-gray-600">Status: {{ request.ict_approve || 'pending' }}</p>
                <p class="text-sm text-gray-600">Date: {{ formatDate(request.booking_date) }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import { useAuthStore } from '@/stores/auth'
  import deviceBorrowingService from '@/services/deviceBorrowingService'

  export default {
    name: 'RequestsListDebug',
    data() {
      return {
        requests: [],
        isLoading: false,
        error: null,
        isTestingService: false,
        serviceTestResult: null
      }
    },
    computed: {
      authStore() {
        return useAuthStore()
      },
      isAuthenticated() {
        return this.authStore.isAuthenticated
      },
      userRole() {
        return this.authStore.userRole
      },
      userName() {
        return this.authStore.userName
      }
    },
    async mounted() {
      console.log('Debug Component: Mounted')
      console.log('Auth State:', {
        isAuthenticated: this.isAuthenticated,
        userRole: this.userRole,
        userName: this.userName
      })

      await this.fetchRequests()
    },
    methods: {
      async fetchRequests() {
        this.isLoading = true
        this.error = null

        try {
          console.log('Debug: Fetching requests...')
          const response = await deviceBorrowingService.getAllRequests({
            per_page: 10
          })

          console.log('Debug: Service response:', response)

          if (response.success) {
            this.requests = response.data.data || []
            console.log('Debug: Requests loaded:', this.requests.length)
          } else {
            throw new Error(response.message || 'Failed to fetch requests')
          }
        } catch (error) {
          console.error('Debug: Error fetching requests:', error)
          this.error = error.message
        } finally {
          this.isLoading = false
        }
      },

      async testService() {
        this.isTestingService = true
        this.serviceTestResult = null

        try {
          console.log('Testing service...')
          const result = await deviceBorrowingService.getAllRequests({ per_page: 5 })
          this.serviceTestResult = result
          console.log('Service test result:', result)
        } catch (error) {
          console.error('Service test error:', error)
          this.serviceTestResult = {
            error: true,
            message: error.message,
            stack: error.stack
          }
        } finally {
          this.isTestingService = false
        }
      },

      formatDate(dateString) {
        if (!dateString) return 'N/A'
        try {
          return new Date(dateString).toLocaleDateString()
        } catch {
          return dateString
        }
      }
    }
  }
</script>
