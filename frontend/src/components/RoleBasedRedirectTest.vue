<template>
  <div class="max-w-4xl mx-auto p-6 bg-white shadow-lg rounded-lg">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Role-Based Redirect System Test</h1>

    <!-- Current Auth State -->
    <div class="mb-8 p-4 bg-blue-50 border border-blue-200 rounded-lg">
      <h2 class="text-xl font-semibold text-blue-800 mb-4">Current Authentication State</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <p><strong>Authenticated:</strong>
            <span :class="isAuthenticated ? 'text-green-600' : 'text-red-600'">
              {{ isAuthenticated ? 'Yes' : 'No' }}
            </span>
          </p>
          <p><strong>User:</strong> {{ user?.name || 'Not logged in' }}</p>
          <p><strong>Email:</strong> {{ user?.email || 'N/A' }}</p>
          <p><strong>Role:</strong>
            <span class="font-mono bg-gray-100 px-2 py-1 rounded">
              {{ userRole || 'No role' }}
            </span>
          </p>
        </div>
        <div>
          <p><strong>Session Restored:</strong>
            <span :class="sessionRestored ? 'text-green-600' : 'text-orange-600'">
              {{ sessionRestored ? 'Yes' : 'No' }}
            </span>
          </p>
          <p><strong>Needs Onboarding:</strong>
            <span :class="user?.needs_onboarding ? 'text-orange-600' : 'text-green-600'">
              {{ user?.needs_onboarding ? 'Yes' : 'No' }}
            </span>
          </p>
          <p><strong>Token:</strong>
            <span class="font-mono text-xs">
              {{ token ? token.substring(0, 20) + '...' : 'No token' }}
            </span>
          </p>
        </div>
      </div>
    </div>

    <!-- Test Actions -->
    <div class="mb-8 p-4 bg-gray-50 border border-gray-200 rounded-lg">
      <h2 class="text-xl font-semibold text-gray-800 mb-4">Test Actions</h2>
      <div class="flex flex-wrap gap-3">
        <button
          @click="testSessionRestore"
          :disabled="loading"
          class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 disabled:opacity-50"
        >
          Test Session Restore
        </button>
        <button
          @click="testTokenVerification"
          :disabled="loading || !token"
          class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 disabled:opacity-50"
        >
          Test Token Verification
        </button>
        <button
          @click="testRoleBasedRedirect"
          :disabled="loading || !isAuthenticated"
          class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 disabled:opacity-50"
        >
          Test Role-Based Redirect
        </button>
        <button
          @click="simulateRefresh"
          :disabled="loading"
          class="px-4 py-2 bg-orange-600 text-white rounded hover:bg-orange-700 disabled:opacity-50"
        >
          Simulate Page Refresh
        </button>
        <button
          @click="clearSession"
          :disabled="loading"
          class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 disabled:opacity-50"
        >
          Clear Session
        </button>
      </div>
    </div>

    <!-- Test Results -->
    <div class="mb-8 p-4 bg-gray-50 border border-gray-200 rounded-lg">
      <h2 class="text-xl font-semibold text-gray-800 mb-4">Test Results</h2>
      <div class="space-y-3 max-h-96 overflow-y-auto">
        <div
          v-for="(result, index) in testResults"
          :key="index"
          :class="[
            'p-3 rounded border-l-4',
            result.type === 'success' ? 'bg-green-50 border-green-400 text-green-800' :
            result.type === 'error' ? 'bg-red-50 border-red-400 text-red-800' :
            result.type === 'warning' ? 'bg-yellow-50 border-yellow-400 text-yellow-800' :
            'bg-blue-50 border-blue-400 text-blue-800'
          ]"
        >
          <div class="flex justify-between items-start">
            <div>
              <p class="font-semibold">{{ result.title }}</p>
              <p class="text-sm mt-1">{{ result.message }}</p>
              <pre v-if="result.data" class="text-xs mt-2 bg-white p-2 rounded overflow-x-auto">{{ JSON.stringify(result.data, null, 2) }}</pre>
            </div>
            <span class="text-xs text-gray-500">{{ result.timestamp }}</span>
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

    <!-- Role-Based Dashboard Links -->
    <div class="mb-8 p-4 bg-green-50 border border-green-200 rounded-lg">
      <h2 class="text-xl font-semibold text-green-800 mb-4">Role-Based Dashboard Links</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
        <router-link
          v-for="dashboard in dashboards"
          :key="dashboard.role"
          :to="dashboard.path"
          :class="[
            'block p-3 rounded border text-center transition-colors',
            userRole === dashboard.role
              ? 'bg-green-100 border-green-300 text-green-800 hover:bg-green-200'
              : 'bg-gray-100 border-gray-300 text-gray-600 hover:bg-gray-200'
          ]"
        >
          <div class="font-semibold">{{ dashboard.name }}</div>
          <div class="text-sm">{{ dashboard.role }}</div>
          <div v-if="userRole === dashboard.role" class="text-xs text-green-600 mt-1">
            ✓ Your Dashboard
          </div>
        </router-link>
      </div>
    </div>

    <!-- Loading Indicator -->
    <div v-if="loading" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white p-6 rounded-lg shadow-lg">
        <div class="flex items-center space-x-3">
          <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
          <span>Testing...</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'
import authService from '@/services/authService'

export default {
  name: 'RoleBasedRedirectTest',
  data() {
    return {
      loading: false,
      testResults: [],
      dashboards: [
        { role: 'admin', name: 'Admin Dashboard', path: '/admin-dashboard' },
        { role: 'staff', name: 'User Dashboard', path: '/user-dashboard' },
        { role: 'head_of_department', name: 'HOD Dashboard', path: '/hod-dashboard' },
        { role: 'divisional_director', name: 'Divisional Dashboard', path: '/divisional-dashboard' },
        { role: 'ict_director', name: 'ICT Director Dashboard', path: '/dict-dashboard' },
        { role: 'ict_officer', name: 'ICT Officer Dashboard', path: '/ict-dashboard' }
      ]
    }
  },
  computed: {
    ...mapGetters('auth', [
      'isAuthenticated',
      'user',
      'userRole',
      'token',
      'sessionRestored',
      'loading'
    ])
  },
  methods: {
    ...mapActions('auth', [
      'restoreSession',
      'verifyToken',
      'logout'
    ]),

    addResult(type, title, message, data = null) {
      this.testResults.unshift({
        type,
        title,
        message,
        data,
        timestamp: new Date().toLocaleTimeString()
      })
    },

    async testSessionRestore() {
      this.loading = true
      try {
        this.addResult('info', 'Session Restore Test', 'Starting session restoration test...')

        const result = await this.restoreSession()

        if (result.success) {
          this.addResult('success', 'Session Restore Success', 'Session restored successfully', result)
        } else {
          this.addResult('warning', 'Session Restore Failed', result.error || 'No stored session found')
        }
      } catch (error) {
        this.addResult('error', 'Session Restore Error', error.message)
      } finally {
        this.loading = false
      }
    },

    async testTokenVerification() {
      this.loading = true
      try {
        this.addResult('info', 'Token Verification Test', 'Verifying current token...')

        const result = await this.verifyToken()

        if (result.success) {
          this.addResult('success', 'Token Verification Success', 'Token is valid and user data updated', result.user)
        } else {
          this.addResult('error', 'Token Verification Failed', result.error || 'Token verification failed')
        }
      } catch (error) {
        this.addResult('error', 'Token Verification Error', error.message)
      } finally {
        this.loading = false
      }
    },

    async testRoleBasedRedirect() {
      this.loading = true
      try {
        this.addResult('info', 'Role-Based Redirect Test', 'Testing role-based redirect...')

        const result = await authService.getRoleBasedRedirect()

        if (result.success) {
          this.addResult('success', 'Role-Based Redirect Success',
            `Redirect URL: ${result.data.data.redirect_url}`, result.data)
        } else {
          this.addResult('error', 'Role-Based Redirect Failed', result.message)
        }
      } catch (error) {
        this.addResult('error', 'Role-Based Redirect Error', error.message)
      } finally {
        this.loading = false
      }
    },

    async simulateRefresh() {
      this.loading = true
      try {
        this.addResult('info', 'Refresh Simulation', 'Simulating page refresh...')

        // Clear auth state
        await this.logout()

        // Wait a moment
        await new Promise(resolve => setTimeout(resolve, 500))

        // Restore session (simulating what happens on page load)
        const result = await this.restoreSession()

        if (result.success) {
          this.addResult('success', 'Refresh Simulation Success',
            'Session restored after simulated refresh', result)
        } else {
          this.addResult('warning', 'Refresh Simulation Result',
            'No session to restore (user would need to login)')
        }
      } catch (error) {
        this.addResult('error', 'Refresh Simulation Error', error.message)
      } finally {
        this.loading = false
      }
    },

    async clearSession() {
      this.loading = true
      try {
        this.addResult('info', 'Clear Session', 'Clearing authentication session...')

        await this.logout()

        // Also clear localStorage manually
        localStorage.removeItem('auth_token')
        localStorage.removeItem('user_data')
        localStorage.removeItem('session_data')

        this.addResult('success', 'Session Cleared', 'All authentication data cleared')
      } catch (error) {
        this.addResult('error', 'Clear Session Error', error.message)
      } finally {
        this.loading = false
      }
    },

    clearResults() {
      this.testResults = []
    }
  },

  mounted() {
    this.addResult('info', 'Test Component Loaded', 'Role-based redirect test component is ready')
  }
}
</script>

<style scoped>
/* Add any additional styles if needed */
</style>
