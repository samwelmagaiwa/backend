<template>
  <div class="min-h-screen bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
      <h1 class="text-3xl font-bold text-gray-900 mb-8">API Test Component</h1>

      <!-- User Info -->
      <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-xl font-semibold mb-4">Current User Information</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <h3 class="font-medium text-gray-700">Authentication</h3>
            <p class="text-sm text-gray-600">Authenticated: {{ isAuthenticated }}</p>
            <p class="text-sm text-gray-600">User Role: {{ userRole }}</p>
            <p class="text-sm text-gray-600">User Name: {{ userName }}</p>
          </div>
          <div>
            <h3 class="font-medium text-gray-700">Roles & Permissions</h3>
            <p class="text-sm text-gray-600">All Roles: {{ userRoles.join(', ') || 'None' }}</p>
            <p class="text-sm text-gray-600">Is ICT Officer: {{ isIctOfficer }}</p>
          </div>
        </div>
      </div>

      <!-- API Tests -->
      <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-xl font-semibold mb-4">API Endpoint Tests</h2>

        <div class="space-y-4">
          <!-- Test ICT Approval Endpoint -->
          <div class="border border-gray-200 rounded p-4">
            <h3 class="font-medium text-gray-700 mb-2">ICT Approval Endpoint</h3>
            <button
              @click="testIctApprovalEndpoint"
              :disabled="isTestingIct"
              class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50 mr-4"
            >
              {{ isTestingIct ? 'Testing...' : 'Test /ict-approval/device-requests' }}
            </button>

            <div v-if="ictTestResult" class="mt-4">
              <h4 class="font-medium text-gray-700">Result:</h4>
              <div
                :class="ictTestResult.success ? 'text-green-600' : 'text-red-600'"
                class="text-sm"
              >
                Status: {{ ictTestResult.success ? 'SUCCESS' : 'FAILED' }}
              </div>
              <div class="text-sm text-gray-600">Message: {{ ictTestResult.message }}</div>
              <div v-if="ictTestResult.status" class="text-sm text-gray-600">
                HTTP Status: {{ ictTestResult.status }}
              </div>
              <pre class="bg-gray-100 p-2 rounded text-xs overflow-auto mt-2">{{
                JSON.stringify(ictTestResult, null, 2)
              }}</pre>
            </div>
          </div>

          <!-- Test Booking Service Endpoint -->
          <div class="border border-gray-200 rounded p-4">
            <h3 class="font-medium text-gray-700 mb-2">Booking Service Endpoint</h3>
            <button
              @click="testBookingServiceEndpoint"
              :disabled="isTestingBooking"
              class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50 mr-4"
            >
              {{ isTestingBooking ? 'Testing...' : 'Test /booking-service/ict-approval-requests' }}
            </button>

            <div v-if="bookingTestResult" class="mt-4">
              <h4 class="font-medium text-gray-700">Result:</h4>
              <div
                :class="bookingTestResult.success ? 'text-green-600' : 'text-red-600'"
                class="text-sm"
              >
                Status: {{ bookingTestResult.success ? 'SUCCESS' : 'FAILED' }}
              </div>
              <div class="text-sm text-gray-600">Message: {{ bookingTestResult.message }}</div>
              <div v-if="bookingTestResult.status" class="text-sm text-gray-600">
                HTTP Status: {{ bookingTestResult.status }}
              </div>
              <pre class="bg-gray-100 p-2 rounded text-xs overflow-auto mt-2">{{
                JSON.stringify(bookingTestResult, null, 2)
              }}</pre>
            </div>
          </div>

          <!-- Test User Endpoint -->
          <div class="border border-gray-200 rounded p-4">
            <h3 class="font-medium text-gray-700 mb-2">User Info Endpoint</h3>
            <button
              @click="testUserEndpoint"
              :disabled="isTestingUser"
              class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50 mr-4"
            >
              {{ isTestingUser ? 'Testing...' : 'Test /api/user' }}
            </button>

            <div v-if="userTestResult" class="mt-4">
              <h4 class="font-medium text-gray-700">Result:</h4>
              <div
                :class="userTestResult.success ? 'text-green-600' : 'text-red-600'"
                class="text-sm"
              >
                Status: {{ userTestResult.success ? 'SUCCESS' : 'FAILED' }}
              </div>
              <pre class="bg-gray-100 p-2 rounded text-xs overflow-auto mt-2">{{
                JSON.stringify(userTestResult, null, 2)
              }}</pre>
            </div>
          </div>
        </div>
      </div>

      <!-- Service Test -->
      <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold mb-4">Device Borrowing Service Test</h2>
        <button
          @click="testDeviceBorrowingService"
          :disabled="isTestingService"
          class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50"
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
    </div>
  </div>
</template>

<script>
  import { useAuthStore } from '@/stores/auth'
  import { deviceBorrowingService } from '@/services/deviceBorrowingService'
  import apiClient from '@/utils/apiClient'

  export default {
    name: 'ApiTestComponent',
    data() {
      return {
        isTestingIct: false,
        isTestingBooking: false,
        isTestingUser: false,
        isTestingService: false,
        ictTestResult: null,
        bookingTestResult: null,
        userTestResult: null,
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
      },
      userRoles() {
        return this.authStore.userRoles || []
      },
      isIctOfficer() {
        return this.userRole === 'ict_officer' || this.userRoles.includes('ict_officer')
      }
    },
    methods: {
      async testIctApprovalEndpoint() {
        this.isTestingIct = true
        this.ictTestResult = null

        try {
          console.log('Testing ICT approval endpoint...')
          const response = await apiClient.get('/ict-approval/device-requests', {
            params: { per_page: 5 }
          })

          this.ictTestResult = {
            success: true,
            message: 'ICT approval endpoint working',
            status: response.status,
            data: response.data,
            count: response.data?.data?.data?.length || 0
          }
          console.log('ICT endpoint test result:', this.ictTestResult)
        } catch (error) {
          console.error('ICT endpoint test error:', error)
          this.ictTestResult = {
            success: false,
            message: error.response?.data?.message || error.message,
            status: error.response?.status,
            error: error.response?.data || error.message,
            stack: error.stack
          }
        } finally {
          this.isTestingIct = false
        }
      },

      async testBookingServiceEndpoint() {
        this.isTestingBooking = true
        this.bookingTestResult = null

        try {
          console.log('Testing booking service endpoint...')
          const response = await apiClient.get('/booking-service/ict-approval-requests', {
            params: { per_page: 5 }
          })

          this.bookingTestResult = {
            success: true,
            message: 'Booking service endpoint working',
            status: response.status,
            data: response.data,
            count: response.data?.data?.data?.length || 0
          }
          console.log('Booking endpoint test result:', this.bookingTestResult)
        } catch (error) {
          console.error('Booking endpoint test error:', error)
          this.bookingTestResult = {
            success: false,
            message: error.response?.data?.message || error.message,
            status: error.response?.status,
            error: error.response?.data || error.message,
            stack: error.stack
          }
        } finally {
          this.isTestingBooking = false
        }
      },

      async testUserEndpoint() {
        this.isTestingUser = true
        this.userTestResult = null

        try {
          console.log('Testing user endpoint...')
          const response = await apiClient.get('/user')

          this.userTestResult = {
            success: true,
            message: 'User endpoint working',
            status: response.status,
            data: response.data
          }
          console.log('User endpoint test result:', this.userTestResult)
        } catch (error) {
          console.error('User endpoint test error:', error)
          this.userTestResult = {
            success: false,
            message: error.response?.data?.message || error.message,
            status: error.response?.status,
            error: error.response?.data || error.message
          }
        } finally {
          this.isTestingUser = false
        }
      },

      async testDeviceBorrowingService() {
        this.isTestingService = true
        this.serviceTestResult = null

        try {
          console.log('Testing device borrowing service...')
          const result = await deviceBorrowingService.getAllRequests({ per_page: 5 })
          this.serviceTestResult = result
          console.log('Service test result:', result)
        } catch (error) {
          console.error('Service test error:', error)
          this.serviceTestResult = {
            success: false,
            message: error.message,
            error: true,
            stack: error.stack
          }
        } finally {
          this.isTestingService = false
        }
      }
    }
  }
</script>
