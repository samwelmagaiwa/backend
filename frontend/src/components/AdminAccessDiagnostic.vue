<template>
  <div class="max-w-6xl mx-auto p-6 bg-white shadow-lg rounded-lg">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Admin Access Diagnostic Tool</h1>
    <p class="text-gray-600 mb-8">This tool helps diagnose why you're seeing "Failed to load data" messages in admin components.</p>

    <!-- Authentication Status -->
    <div class="mb-8 p-6 bg-blue-50 border border-blue-200 rounded-lg">
      <h2 class="text-xl font-semibold text-blue-800 mb-4 flex items-center">
        <i class="fas fa-user-shield mr-2"></i>
        Authentication Status
      </h2>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <p class="mb-2"><strong>Authenticated:</strong>
            <span :class="isAuthenticated ? 'text-green-600' : 'text-red-600'">
              {{ isAuthenticated ? 'Yes' : 'No' }}
            </span>
          </p>
          <p class="mb-2"><strong>User Name:</strong> {{ user?.name || 'Not available' }}</p>
          <p class="mb-2"><strong>Email:</strong> {{ user?.email || 'Not available' }}</p>
          <p class="mb-2"><strong>User Role:</strong>
            <span class="font-mono bg-gray-100 px-2 py-1 rounded">
              {{ userRole || 'No role detected' }}
            </span>
          </p>
        </div>
        <div>
          <p class="mb-2"><strong>Token Present:</strong>
            <span :class="hasToken ? 'text-green-600' : 'text-red-600'">
              {{ hasToken ? 'Yes' : 'No' }}
            </span>
          </p>
          <p class="mb-2"><strong>All User Roles:</strong></p>
          <div v-if="user?.roles && user.roles.length > 0" class="flex flex-wrap gap-1">
            <span
              v-for="role in user.roles"
              :key="role.id"
              class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs"
            >
              {{ role.display_name }}
            </span>
          </div>
          <span v-else class="text-gray-500 text-sm">No roles assigned</span>
        </div>
      </div>
    </div>

    <!-- API Configuration -->
    <div class="mb-8 p-6 bg-gray-50 border border-gray-200 rounded-lg">
      <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
        <i class="fas fa-cog mr-2"></i>
        API Configuration
      </h2>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <p class="mb-2"><strong>API Base URL:</strong>
            <code class="bg-gray-100 px-2 py-1 rounded text-sm">{{ apiBaseUrl }}</code>
          </p>
          <p class="mb-2"><strong>API Timeout:</strong> {{ apiTimeout }}ms</p>
        </div>
        <div>
          <p class="mb-2"><strong>Environment:</strong> {{ environment }}</p>
          <p class="mb-2"><strong>Debug Mode:</strong> {{ debugMode ? 'Enabled' : 'Disabled' }}</p>
        </div>
      </div>
    </div>

    <!-- API Endpoint Tests -->
    <div class="mb-8 p-6 bg-yellow-50 border border-yellow-200 rounded-lg">
      <h2 class="text-xl font-semibold text-yellow-800 mb-4 flex items-center">
        <i class="fas fa-flask mr-2"></i>
        API Endpoint Tests
      </h2>
      <p class="text-yellow-700 mb-4">Testing the specific endpoints that are failing...</p>

      <div class="space-y-4">
        <!-- Health Check -->
        <div class="flex items-center justify-between p-3 bg-white rounded border">
          <div>
            <strong>Health Check:</strong> <code>/api/health</code>
          </div>
          <div class="flex items-center space-x-2">
            <span v-if="tests.health.loading" class="text-blue-600">
              <i class="fas fa-spinner fa-spin"></i> Testing...
            </span>
            <span v-else-if="tests.health.success" class="text-green-600">
              <i class="fas fa-check-circle"></i> Success
            </span>
            <span v-else-if="tests.health.error" class="text-red-600">
              <i class="fas fa-times-circle"></i> Failed
            </span>
            <button
              @click="testHealthEndpoint"
              :disabled="tests.health.loading"
              class="px-3 py-1 bg-blue-600 text-white rounded text-sm hover:bg-blue-700 disabled:opacity-50"
            >
              Test
            </button>
          </div>
        </div>

        <!-- Current User -->
        <div class="flex items-center justify-between p-3 bg-white rounded border">
          <div>
            <strong>Current User:</strong> <code>/api/user</code>
          </div>
          <div class="flex items-center space-x-2">
            <span v-if="tests.currentUser.loading" class="text-blue-600">
              <i class="fas fa-spinner fa-spin"></i> Testing...
            </span>
            <span v-else-if="tests.currentUser.success" class="text-green-600">
              <i class="fas fa-check-circle"></i> Success
            </span>
            <span v-else-if="tests.currentUser.error" class="text-red-600">
              <i class="fas fa-times-circle"></i> Failed
            </span>
            <button
              @click="testCurrentUserEndpoint"
              :disabled="tests.currentUser.loading"
              class="px-3 py-1 bg-blue-600 text-white rounded text-sm hover:bg-blue-700 disabled:opacity-50"
            >
              Test
            </button>
          </div>
        </div>

        <!-- Admin Users -->
        <div class="flex items-center justify-between p-3 bg-white rounded border">
          <div>
            <strong>Admin Users:</strong> <code>/api/admin/users</code>
          </div>
          <div class="flex items-center space-x-2">
            <span v-if="tests.adminUsers.loading" class="text-blue-600">
              <i class="fas fa-spinner fa-spin"></i> Testing...
            </span>
            <span v-else-if="tests.adminUsers.success" class="text-green-600">
              <i class="fas fa-check-circle"></i> Success
            </span>
            <span v-else-if="tests.adminUsers.error" class="text-red-600">
              <i class="fas fa-times-circle"></i> Failed
            </span>
            <button
              @click="testAdminUsersEndpoint"
              :disabled="tests.adminUsers.loading"
              class="px-3 py-1 bg-blue-600 text-white rounded text-sm hover:bg-blue-700 disabled:opacity-50"
            >
              Test
            </button>
          </div>
        </div>

        <!-- Admin Form Data -->
        <div class="flex items-center justify-between p-3 bg-white rounded border">
          <div>
            <strong>Admin Form Data:</strong> <code>/api/admin/users/create-form-data</code>
          </div>
          <div class="flex items-center space-x-2">
            <span v-if="tests.adminFormData.loading" class="text-blue-600">
              <i class="fas fa-spinner fa-spin"></i> Testing...
            </span>
            <span v-else-if="tests.adminFormData.success" class="text-green-600">
              <i class="fas fa-check-circle"></i> Success
            </span>
            <span v-else-if="tests.adminFormData.error" class="text-red-600">
              <i class="fas fa-times-circle"></i> Failed
            </span>
            <button
              @click="testAdminFormDataEndpoint"
              :disabled="tests.adminFormData.loading"
              class="px-3 py-1 bg-blue-600 text-white rounded text-sm hover:bg-blue-700 disabled:opacity-50"
            >
              Test
            </button>
          </div>
        </div>

        <!-- Admin Statistics -->
        <div class="flex items-center justify-between p-3 bg-white rounded border">
          <div>
            <strong>Admin Statistics:</strong> <code>/api/admin/users/statistics</code>
          </div>
          <div class="flex items-center space-x-2">
            <span v-if="tests.adminStats.loading" class="text-blue-600">
              <i class="fas fa-spinner fa-spin"></i> Testing...
            </span>
            <span v-else-if="tests.adminStats.success" class="text-green-600">
              <i class="fas fa-check-circle"></i> Success
            </span>
            <span v-else-if="tests.adminStats.error" class="text-red-600">
              <i class="fas fa-times-circle"></i> Failed
            </span>
            <button
              @click="testAdminStatsEndpoint"
              :disabled="tests.adminStats.loading"
              class="px-3 py-1 bg-blue-600 text-white rounded text-sm hover:bg-blue-700 disabled:opacity-50"
            >
              Test
            </button>
          </div>
        </div>
      </div>

      <div class="mt-4">
        <button
          @click="runAllTests"
          :disabled="isRunningAllTests"
          class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 disabled:opacity-50"
        >
          <i v-if="isRunningAllTests" class="fas fa-spinner fa-spin mr-2"></i>
          {{ isRunningAllTests ? 'Running All Tests...' : 'Run All Tests' }}
        </button>
      </div>
    </div>

    <!-- Test Results -->
    <div v-if="testResults.length > 0" class="mb-8 p-6 bg-gray-50 border border-gray-200 rounded-lg">
      <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
        <i class="fas fa-clipboard-list mr-2"></i>
        Test Results
      </h2>
      <div class="space-y-3 max-h-96 overflow-y-auto">
        <div
          v-for="(result, index) in testResults"
          :key="index"
          :class="[
            'p-3 rounded border-l-4',
            result.success ? 'bg-green-50 border-green-400' : 'bg-red-50 border-red-400'
          ]"
        >
          <div class="flex justify-between items-start">
            <div class="flex-1">
              <p class="font-semibold" :class="result.success ? 'text-green-800' : 'text-red-800'">
                {{ result.endpoint }}
              </p>
              <p class="text-sm mt-1" :class="result.success ? 'text-green-700' : 'text-red-700'">
                {{ result.message }}
              </p>
              <div v-if="result.details" class="mt-2">
                <p class="text-xs text-gray-600 mb-1">Details:</p>
                <pre class="text-xs bg-white p-2 rounded overflow-x-auto border">{{ JSON.stringify(result.details, null, 2) }}</pre>
              </div>
            </div>
            <span class="text-xs text-gray-500 ml-4">{{ result.timestamp }}</span>
          </div>
        </div>
      </div>
      <button
        @click="clearResults"
        class="mt-3 px-3 py-1 text-sm bg-gray-600 text-white rounded hover:bg-gray-700"
      >
        Clear Results
      </button>
    </div>

    <!-- Solutions -->
    <div class="mb-8 p-6 bg-green-50 border border-green-200 rounded-lg">
      <h2 class="text-xl font-semibold text-green-800 mb-4 flex items-center">
        <i class="fas fa-lightbulb mr-2"></i>
        Common Solutions
      </h2>
      <div class="space-y-4">
        <div class="border-l-4 border-red-500 pl-4">
          <h3 class="font-semibold text-red-800">If you see "Access denied. Admin privileges required":</h3>
          <p class="text-red-700 text-sm mt-1">
            Your user account doesn't have the 'admin' role. Contact a system administrator to assign you the admin role.
          </p>
        </div>

        <div class="border-l-4 border-orange-500 pl-4">
          <h3 class="font-semibold text-orange-800">If you see "Authentication required":</h3>
          <p class="text-orange-700 text-sm mt-1">
            You're not logged in or your session has expired. Please log out and log back in.
          </p>
        </div>

        <div class="border-l-4 border-blue-500 pl-4">
          <h3 class="font-semibold text-blue-800">If you see network errors:</h3>
          <p class="text-blue-700 text-sm mt-1">
            The backend server might not be running. Check that Laravel is running on the configured API URL.
          </p>
        </div>

        <div class="border-l-4 border-purple-500 pl-4">
          <h3 class="font-semibold text-purple-800">If tests pass but you still see "Failed to load data":</h3>
          <p class="text-purple-700 text-sm mt-1">
            There might be a JavaScript error in the component. Check the browser console for error messages.
          </p>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="p-6 bg-blue-50 border border-blue-200 rounded-lg">
      <h2 class="text-xl font-semibold text-blue-800 mb-4 flex items-center">
        <i class="fas fa-tools mr-2"></i>
        Quick Actions
      </h2>
      <div class="flex flex-wrap gap-3">
        <button
          @click="refreshUserData"
          class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
        >
          <i class="fas fa-refresh mr-2"></i>
          Refresh User Data
        </button>
        <button
          @click="clearLocalStorage"
          class="px-4 py-2 bg-orange-600 text-white rounded hover:bg-orange-700"
        >
          <i class="fas fa-trash mr-2"></i>
          Clear Local Storage
        </button>
        <router-link
          to="/admin/users"
          class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 inline-flex items-center"
        >
          <i class="fas fa-arrow-right mr-2"></i>
          Go to User Management
        </router-link>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import { useStore } from 'vuex'
import apiClient from '@/utils/apiClient'
import { API_CONFIG, APP_CONFIG, ENVIRONMENT } from '@/utils/config'

export default {
  name: 'AdminAccessDiagnostic',
  setup() {
    const store = useStore()

    // Reactive data
    const testResults = ref([])
    const isRunningAllTests = ref(false)

    // Test states
    const tests = ref({
      health: { loading: false, success: null, error: null },
      currentUser: { loading: false, success: null, error: null },
      adminUsers: { loading: false, success: null, error: null },
      adminFormData: { loading: false, success: null, error: null },
      adminStats: { loading: false, success: null, error: null }
    })

    // Computed properties
    const isAuthenticated = computed(() => store.getters['auth/isAuthenticated'])
    const user = computed(() => store.getters['auth/user'])
    const userRole = computed(() => store.getters['auth/userRole'])
    const hasToken = computed(() => !!localStorage.getItem('auth_token'))

    const apiBaseUrl = computed(() => API_CONFIG.BASE_URL)
    const apiTimeout = computed(() => API_CONFIG.TIMEOUT)
    const environment = computed(() => ENVIRONMENT.IS_DEVELOPMENT ? 'Development' : 'Production')
    const debugMode = computed(() => APP_CONFIG.DEBUG)

    // Methods
    const addResult = (endpoint, success, message, details = null) => {
      testResults.value.unshift({
        endpoint,
        success,
        message,
        details,
        timestamp: new Date().toLocaleTimeString()
      })
    }

    const testHealthEndpoint = async() => {
      tests.value.health.loading = true
      tests.value.health.success = null
      tests.value.health.error = null

      try {
        const response = await apiClient.get('/health')
        tests.value.health.success = true
        addResult('/api/health', true, 'Health check passed', response.data)
      } catch (error) {
        tests.value.health.error = true
        addResult('/api/health', false, `Health check failed: ${error.message}`, {
          status: error.response?.status,
          data: error.response?.data
        })
      } finally {
        tests.value.health.loading = false
      }
    }

    const testCurrentUserEndpoint = async() => {
      tests.value.currentUser.loading = true
      tests.value.currentUser.success = null
      tests.value.currentUser.error = null

      try {
        const response = await apiClient.get('/user')
        tests.value.currentUser.success = true
        addResult('/api/user', true, 'Current user data retrieved successfully', {
          user_id: response.data.id,
          user_name: response.data.name,
          user_roles: response.data.roles
        })
      } catch (error) {
        tests.value.currentUser.error = true
        addResult('/api/user', false, `Failed to get current user: ${error.message}`, {
          status: error.response?.status,
          data: error.response?.data
        })
      } finally {
        tests.value.currentUser.loading = false
      }
    }

    const testAdminUsersEndpoint = async() => {
      tests.value.adminUsers.loading = true
      tests.value.adminUsers.success = null
      tests.value.adminUsers.error = null

      try {
        const response = await apiClient.get('/admin/users')
        tests.value.adminUsers.success = true
        addResult('/api/admin/users', true, 'Admin users endpoint accessible', {
          total_users: response.data.data?.users?.length || 0
        })
      } catch (error) {
        tests.value.adminUsers.error = true
        addResult('/api/admin/users', false, `Admin users endpoint failed: ${error.message}`, {
          status: error.response?.status,
          data: error.response?.data
        })
      } finally {
        tests.value.adminUsers.loading = false
      }
    }

    const testAdminFormDataEndpoint = async() => {
      tests.value.adminFormData.loading = true
      tests.value.adminFormData.success = null
      tests.value.adminFormData.error = null

      try {
        const response = await apiClient.get('/admin/users/create-form-data')
        tests.value.adminFormData.success = true
        addResult('/api/admin/users/create-form-data', true, 'Admin form data endpoint accessible', {
          roles_count: response.data.data?.roles?.length || 0,
          departments_count: response.data.data?.departments?.length || 0
        })
      } catch (error) {
        tests.value.adminFormData.error = true
        addResult('/api/admin/users/create-form-data', false, `Admin form data endpoint failed: ${error.message}`, {
          status: error.response?.status,
          data: error.response?.data
        })
      } finally {
        tests.value.adminFormData.loading = false
      }
    }

    const testAdminStatsEndpoint = async() => {
      tests.value.adminStats.loading = true
      tests.value.adminStats.success = null
      tests.value.adminStats.error = null

      try {
        const response = await apiClient.get('/admin/users/statistics')
        tests.value.adminStats.success = true
        addResult('/api/admin/users/statistics', true, 'Admin statistics endpoint accessible', {
          total_users: response.data.data?.total_users || 0
        })
      } catch (error) {
        tests.value.adminStats.error = true
        addResult('/api/admin/users/statistics', false, `Admin statistics endpoint failed: ${error.message}`, {
          status: error.response?.status,
          data: error.response?.data
        })
      } finally {
        tests.value.adminStats.loading = false
      }
    }

    const runAllTests = async() => {
      isRunningAllTests.value = true
      addResult('Test Suite', true, 'Starting comprehensive API tests...')

      try {
        await testHealthEndpoint()
        await new Promise(resolve => setTimeout(resolve, 500))

        await testCurrentUserEndpoint()
        await new Promise(resolve => setTimeout(resolve, 500))

        await testAdminUsersEndpoint()
        await new Promise(resolve => setTimeout(resolve, 500))

        await testAdminFormDataEndpoint()
        await new Promise(resolve => setTimeout(resolve, 500))

        await testAdminStatsEndpoint()

        addResult('Test Suite', true, 'All tests completed')
      } catch (error) {
        addResult('Test Suite', false, `Test suite error: ${error.message}`)
      } finally {
        isRunningAllTests.value = false
      }
    }

    const clearResults = () => {
      testResults.value = []
    }

    const refreshUserData = async() => {
      try {
        await store.dispatch('auth/restoreSession')
        addResult('User Data', true, 'User data refreshed successfully')
      } catch (error) {
        addResult('User Data', false, `Failed to refresh user data: ${error.message}`)
      }
    }

    const clearLocalStorage = () => {
      localStorage.removeItem('auth_token')
      localStorage.removeItem('user_data')
      localStorage.removeItem('session_data')
      localStorage.removeItem('sidebar_collapsed')
      addResult('Local Storage', true, 'Local storage cleared. Please refresh the page and log in again.')
    }

    // Initialize
    onMounted(() => {
      addResult('Diagnostic Tool', true, 'Admin access diagnostic tool loaded')
    })

    return {
      // State
      testResults,
      isRunningAllTests,
      tests,

      // Computed
      isAuthenticated,
      user,
      userRole,
      hasToken,
      apiBaseUrl,
      apiTimeout,
      environment,
      debugMode,

      // Methods
      testHealthEndpoint,
      testCurrentUserEndpoint,
      testAdminUsersEndpoint,
      testAdminFormDataEndpoint,
      testAdminStatsEndpoint,
      runAllTests,
      clearResults,
      refreshUserData,
      clearLocalStorage
    }
  }
}
</script>

<style scoped>
/* Add any component-specific styles here */
</style>
