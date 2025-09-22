<template>
  <div class="min-h-screen bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
      <h1 class="text-3xl font-bold text-gray-800 mb-8">Request Details Debug</h1>

      <!-- URL Parameters -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">URL Parameters</h2>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-base font-medium text-gray-600">Request ID</label>
            <p class="text-lg text-gray-800">{{ $route.query.id || 'Not provided' }}</p>
          </div>
          <div>
            <label class="block text-base font-medium text-gray-600">Request Type</label>
            <p class="text-lg text-gray-800">{{ $route.query.type || 'Not provided' }}</p>
          </div>
          <div>
            <label class="block text-base font-medium text-gray-600">User Access ID</label>
            <p class="text-lg text-gray-800">{{ $route.query.userAccessId || 'Not provided' }}</p>
          </div>
        </div>
      </div>

      <!-- User Information -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Current User</h2>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-base font-medium text-gray-600">User ID</label>
            <p class="text-lg text-gray-800">{{ userInfo.id || 'Not available' }}</p>
          </div>
          <div>
            <label class="block text-base font-medium text-gray-600">User Name</label>
            <p class="text-lg text-gray-800">{{ userInfo.name || 'Not available' }}</p>
          </div>
          <div>
            <label class="block text-base font-medium text-gray-600">User Role</label>
            <p class="text-lg text-gray-800">{{ userInfo.role || 'Not available' }}</p>
          </div>
          <div>
            <label class="block text-base font-medium text-gray-600">User Email</label>
            <p class="text-lg text-gray-800">{{ userInfo.email || 'Not available' }}</p>
          </div>
        </div>
      </div>

      <!-- API Test Results -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">API Test Results</h2>

        <!-- Test Request Status API -->
        <div class="mb-6">
          <div class="flex items-center justify-between mb-2">
            <h3 class="text-lg font-medium text-gray-600">Request Status API Test</h3>
            <button
              @click="testRequestStatusAPI"
              :disabled="isTestingRequestStatus"
              class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 disabled:opacity-50"
            >
              {{ isTestingRequestStatus ? 'Testing...' : 'Test API' }}
            </button>
          </div>
          <div class="bg-gray-50 p-4 rounded border">
            <pre class="text-sm text-gray-700 whitespace-pre-wrap">{{ requestStatusResult }}</pre>
          </div>
        </div>

        <!-- Test Debug API -->
        <div class="mb-6">
          <div class="flex items-center justify-between mb-2">
            <h3 class="text-lg font-medium text-gray-600">Debug API Test</h3>
            <button
              @click="testDebugAPI"
              :disabled="isTestingDebug"
              class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 disabled:opacity-50"
            >
              {{ isTestingDebug ? 'Testing...' : 'Test Debug' }}
            </button>
          </div>
          <div class="bg-gray-50 p-4 rounded border">
            <pre class="text-sm text-gray-700 whitespace-pre-wrap">{{ debugResult }}</pre>
          </div>
        </div>

        <!-- Test All User Requests -->
        <div class="mb-6">
          <div class="flex items-center justify-between mb-2">
            <h3 class="text-lg font-medium text-gray-600">All User Requests</h3>
            <button
              @click="testAllRequestsAPI"
              :disabled="isTestingAllRequests"
              class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 disabled:opacity-50"
            >
              {{ isTestingAllRequests ? 'Testing...' : 'Get All Requests' }}
            </button>
          </div>
          <div class="bg-gray-50 p-4 rounded border">
            <pre class="text-sm text-gray-700 whitespace-pre-wrap">{{ allRequestsResult }}</pre>
          </div>
        </div>
      </div>

      <!-- Navigation -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Navigation</h2>
        <div class="flex space-x-4">
          <button
            @click="goToOriginalPage"
            class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700"
          >
            Go to Original Request Details
          </button>
          <button
            @click="goToRequestStatus"
            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
          >
            Go to Request Status
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import { ref, onMounted } from 'vue'
  import { useRoute, useRouter } from 'vue-router'
  import { useAuth } from '@/composables/useAuth'
  import requestStatusService from '@/services/requestStatusService'

  export default {
    name: 'RequestDetailsDebug',
    setup() {
      const route = useRoute()
      const router = useRouter()
      const { user } = useAuth()

      const userInfo = ref({})
      const requestStatusResult = ref('Not tested yet')
      const debugResult = ref('Not tested yet')
      const allRequestsResult = ref('Not tested yet')
      const isTestingRequestStatus = ref(false)
      const isTestingDebug = ref(false)
      const isTestingAllRequests = ref(false)

      const testRequestStatusAPI = async () => {
        isTestingRequestStatus.value = true
        try {
          const requestId = route.query.id
          const requestType = route.query.type

          requestStatusResult.value = `Testing with ID: ${requestId}, Type: ${requestType}\n\n`

          const result = await requestStatusService.getRequestDetails(requestId, requestType)
          requestStatusResult.value += JSON.stringify(result, null, 2)
        } catch (error) {
          requestStatusResult.value += `Error: ${error.message}\n${error.stack}`
        } finally {
          isTestingRequestStatus.value = false
        }
      }

      const testDebugAPI = async () => {
        isTestingDebug.value = true
        try {
          const result = await requestStatusService.debug()
          debugResult.value = JSON.stringify(result, null, 2)
        } catch (error) {
          debugResult.value = `Error: ${error.message}\n${error.stack}`
        } finally {
          isTestingDebug.value = false
        }
      }

      const testAllRequestsAPI = async () => {
        isTestingAllRequests.value = true
        try {
          const result = await requestStatusService.getRequests()
          allRequestsResult.value = JSON.stringify(result, null, 2)
        } catch (error) {
          allRequestsResult.value = `Error: ${error.message}\n${error.stack}`
        } finally {
          isTestingAllRequests.value = false
        }
      }

      const goToOriginalPage = () => {
        const params = new URLSearchParams()
        if (route.query.id) params.append('id', route.query.id)
        if (route.query.type) params.append('type', route.query.type)
        if (route.query.userAccessId) params.append('userAccessId', route.query.userAccessId)

        router.push(`/request-details?${params.toString()}`)
      }

      const goToRequestStatus = () => {
        router.push('/request-status')
      }

      onMounted(() => {
        userInfo.value = user.value || {}
      })

      return {
        userInfo,
        requestStatusResult,
        debugResult,
        allRequestsResult,
        isTestingRequestStatus,
        isTestingDebug,
        isTestingAllRequests,
        testRequestStatusAPI,
        testDebugAPI,
        testAllRequestsAPI,
        goToOriginalPage,
        goToRequestStatus
      }
    }
  }
</script>
