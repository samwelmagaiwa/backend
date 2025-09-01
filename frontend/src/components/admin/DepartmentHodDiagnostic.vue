<template>
  <div class="p-6 bg-gray-100 min-h-screen">
    <div class="max-w-4xl mx-auto">
      <h1 class="text-3xl font-bold text-gray-800 mb-6">Department HOD Navigation Diagnostic</h1>

      <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Navigation Test</h2>

        <div class="space-y-4">
          <div>
            <h3 class="text-lg font-medium text-gray-600 mb-2">Current Route:</h3>
            <p class="text-gray-800 bg-gray-100 p-2 rounded">{{ $route.path }}</p>
          </div>

          <div>
            <h3 class="text-lg font-medium text-gray-600 mb-2">Test Navigation:</h3>
            <div class="space-x-4">
              <button
                @click="navigateToDepartmentHods"
                class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition-colors"
              >
                Navigate to Department HODs
              </button>

              <button
                @click="navigateToAdminDashboard"
                class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition-colors"
              >
                Back to Admin Dashboard
              </button>
            </div>
          </div>

          <div>
            <h3 class="text-lg font-medium text-gray-600 mb-2">Router Link Test:</h3>
            <div class="space-x-4">
              <router-link
                to="/admin/departments"
                class="inline-block bg-purple-500 text-white px-4 py-2 rounded hover:bg-purple-600 transition-colors"
              >
                Router Link to Department HODs
              </router-link>

              <router-link
                to="/admin-dashboard"
                class="inline-block bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition-colors"
              >
                Router Link to Admin Dashboard
              </router-link>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Route Information</h2>

        <div class="space-y-2">
          <div>
            <strong>Route Name:</strong> {{ $route.name }}
          </div>
          <div>
            <strong>Route Path:</strong> {{ $route.path }}
          </div>
          <div>
            <strong>Route Params:</strong> {{ JSON.stringify($route.params) }}
          </div>
          <div>
            <strong>Route Query:</strong> {{ JSON.stringify($route.query) }}
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">API Test</h2>

        <div class="space-y-4">
          <button
            @click="testDepartmentHodAPI"
            :disabled="apiTesting"
            class="bg-orange-500 text-white px-4 py-2 rounded hover:bg-orange-600 transition-colors disabled:opacity-50"
          >
            {{ apiTesting ? 'Testing...' : 'Test Department HOD API' }}
          </button>

          <div v-if="apiResult" class="mt-4">
            <h3 class="text-lg font-medium text-gray-600 mb-2">API Result:</h3>
            <pre class="bg-gray-100 p-4 rounded text-sm overflow-auto">{{ JSON.stringify(apiResult, null, 2) }}</pre>
          </div>

          <div v-if="apiError" class="mt-4">
            <h3 class="text-lg font-medium text-red-600 mb-2">API Error:</h3>
            <pre class="bg-red-100 p-4 rounded text-sm overflow-auto text-red-800">{{ apiError }}</pre>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import departmentHodService from '@/services/departmentHodService'

export default {
  name: 'DepartmentHodDiagnostic',
  setup() {
    const router = useRouter()
    const apiTesting = ref(false)
    const apiResult = ref(null)
    const apiError = ref(null)

    const navigateToDepartmentHods = () => {
      console.log('Attempting to navigate to /admin/departments')
      router.push('/admin/departments')
        .then(() => {
          console.log('Navigation successful')
        })
        .catch((error) => {
          console.error('Navigation failed:', error)
        })
    }

    const navigateToAdminDashboard = () => {
      console.log('Attempting to navigate to /admin-dashboard')
      router.push('/admin-dashboard')
        .then(() => {
          console.log('Navigation successful')
        })
        .catch((error) => {
          console.error('Navigation failed:', error)
        })
    }

    const testDepartmentHodAPI = async() => {
      apiTesting.value = true
      apiResult.value = null
      apiError.value = null

      try {
        console.log('Testing Department HOD API...')
        const result = await departmentHodService.getDepartmentsWithHods()
        console.log('API Result:', result)
        apiResult.value = result
      } catch (error) {
        console.error('API Error:', error)
        apiError.value = error.message || 'Unknown error'
      } finally {
        apiTesting.value = false
      }
    }

    return {
      navigateToDepartmentHods,
      navigateToAdminDashboard,
      testDepartmentHodAPI,
      apiTesting,
      apiResult,
      apiError
    }
  }
}
</script>
