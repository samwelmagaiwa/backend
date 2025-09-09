<template>
  <div class="flex flex-col h-screen">
    <div class="p-6 bg-gray-900 text-white">
      <h1 class="text-2xl font-bold mb-4">API Test - Request Details</h1>

      <div class="mb-4">
        <label class="block mb-2">Request ID:</label>
        <input
          v-model="requestId"
          type="text"
          class="px-3 py-2 bg-gray-800 border border-gray-600 rounded text-white"
          placeholder="Enter request ID"
        />
      </div>

      <div class="mb-4">
        <label class="block mb-2">Request Type:</label>
        <select
          v-model="requestType"
          class="px-3 py-2 bg-gray-800 border border-gray-600 rounded text-white"
        >
          <option value="booking_service">Booking Service</option>
          <option value="combined_access">Combined Access</option>
        </select>
      </div>

      <div class="mb-4">
        <button
          @click="testApi"
          :disabled="isLoading"
          class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded disabled:opacity-50 mr-2"
        >
          {{ isLoading ? 'Loading...' : 'Test Request Details' }}
        </button>
        <button
          @click="testPendingRequests"
          :disabled="isLoading"
          class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded disabled:opacity-50"
        >
          {{ isLoading ? 'Loading...' : 'Test Pending Requests' }}
        </button>
      </div>

      <div class="bg-gray-800 p-4 rounded">
        <h2 class="text-lg font-bold mb-2">API Response:</h2>
        <pre class="text-sm text-green-400 overflow-auto max-h-96">{{ responseText }}</pre>
      </div>
    </div>
  </div>
</template>

<script>
  import { ref } from 'vue'
  import requestStatusService from '@/services/requestStatusService'
  import bookingService from '@/services/bookingService'

  export default {
    name: 'ApiTest',
    setup() {
      const requestId = ref('8')
      const requestType = ref('booking_service')
      const isLoading = ref(false)
      const responseText = ref('No test run yet...')

      const testApi = async () => {
        isLoading.value = true
        responseText.value = 'Loading...'

        try {
          console.log('🧪 Testing API with:', {
            id: requestId.value,
            type: requestType.value
          })

          const response = await requestStatusService.getRequestDetails(
            requestId.value,
            requestType.value
          )

          responseText.value = JSON.stringify(response, null, 2)
          console.log('🧪 API Response:', response)
        } catch (error) {
          responseText.value = `Error: ${error.message}\n\nFull Error: ${JSON.stringify(error, null, 2)}`
          console.error('🧪 API Test Error:', error)
        } finally {
          isLoading.value = false
        }
      }

      const testPendingRequests = async () => {
        isLoading.value = true
        responseText.value = 'Loading pending requests test...'

        try {
          console.log('🧪 Testing checkPendingRequests API')

          const response = await bookingService.checkPendingRequests()

          responseText.value = JSON.stringify(response, null, 2)
          console.log('🧪 Pending Requests Response:', response)
        } catch (error) {
          responseText.value = `Error: ${error.message}\n\nFull Error: ${JSON.stringify(error, null, 2)}`
          console.error('🧪 Pending Requests Test Error:', error)
        } finally {
          isLoading.value = false
        }
      }

      return {
        requestId,
        requestType,
        isLoading,
        responseText,
        testApi,
        testPendingRequests
      }
    }
  }
</script>
