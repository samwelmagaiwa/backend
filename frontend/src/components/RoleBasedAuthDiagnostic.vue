<template>
  <div class="min-h-screen bg-gray-100 py-8">
    <div class="max-w-4xl mx-auto px-4">
      <div class="bg-white rounded-lg shadow-lg p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">🔐 Role-Based Authentication Diagnostic</h1>

        <!-- Current Auth State -->
        <div class="mb-8">
          <h2 class="text-lg font-semibold text-gray-700 mb-4">Current Authentication State</h2>
          <div class="bg-gray-50 rounded-lg p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <h3 class="font-medium text-gray-600 mb-2">Vuex Store</h3>
                <div class="space-y-1 text-sm">
                  <div><strong>Authenticated:</strong> {{ vuexAuth.isAuthenticated ? '✅' : '❌' }}</div>
                  <div><strong>User:</strong> {{ vuexAuth.user?.name || 'None' }}</div>
                  <div><strong>Role:</strong> {{ vuexAuth.userRole || 'None' }}</div>
                  <div><strong>Roles:</strong> {{ vuexAuth.userRoles?.join(', ') || 'None' }}</div>
                  <div><strong>Loading:</strong> {{ vuexAuth.isLoading ? '⏳' : '✅' }}</div>
                </div>
              </div>
              <div>
                <h3 class="font-medium text-gray-600 mb-2">Pinia Store</h3>
                <div class="space-y-1 text-sm">
                  <div><strong>Authenticated:</strong> {{ piniaAuth.isAuthenticated ? '✅' : '❌' }}</div>
                  <div><strong>User:</strong> {{ piniaAuth.user?.name || 'None' }}</div>
                  <div><strong>Role:</strong> {{ piniaAuth.userRole || 'None' }}</div>
                  <div><strong>Initialized:</strong> {{ piniaAuth.isInitialized ? '✅' : '❌' }}</div>
                  <div><strong>Loading:</strong> {{ piniaAuth.isLoading ? '⏳' : '✅' }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Role-Dashboard Mapping -->
        <div class="mb-8">
          <h2 class="text-lg font-semibold text-gray-700 mb-4">Role-Dashboard Mapping</h2>
          <div class="bg-blue-50 rounded-lg p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div v-for="(role, key) in ROLES" :key="key" class="flex justify-between items-center">
                <span class="font-medium">{{ role }}:</span>
                <span class="text-blue-600">{{ getDefaultDashboard(role) || 'No dashboard' }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Current User Dashboard -->
        <div class="mb-8" v-if="currentUserRole">
          <h2 class="text-lg font-semibold text-gray-700 mb-4">Current User Dashboard</h2>
          <div class="bg-green-50 rounded-lg p-4">
            <div class="flex justify-between items-center">
              <span class="font-medium">Your Role ({{ currentUserRole }}) Dashboard:</span>
              <span class="text-green-600 font-semibold">{{ currentUserDashboard }}</span>
            </div>
            <button
              @click="navigateToDashboard"
              class="mt-3 bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition-colors"
            >
              Go to Dashboard
            </button>
          </div>
        </div>

        <!-- Route Access Test -->
        <div class="mb-8">
          <h2 class="text-lg font-semibold text-gray-700 mb-4">Route Access Test</h2>
          <div class="space-y-2">
            <div v-for="route in testRoutes" :key="route.path" class="flex justify-between items-center p-3 bg-gray-50 rounded">
              <div>
                <span class="font-medium">{{ route.path }}</span>
                <span class="text-sm text-gray-500 ml-2">({{ route.roles?.join(', ') || 'Public' }})</span>
              </div>
              <div class="flex items-center space-x-2">
                <span :class="hasAccess(route) ? 'text-green-600' : 'text-red-600'">
                  {{ hasAccess(route) ? '✅ Allowed' : '❌ Denied' }}
                </span>
                <button
                  @click="testNavigation(route.path)"
                  class="bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600 transition-colors"
                >
                  Test
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- LocalStorage Data -->
        <div class="mb-8">
          <h2 class="text-lg font-semibold text-gray-700 mb-4">LocalStorage Data</h2>
          <div class="bg-gray-50 rounded-lg p-4">
            <div class="space-y-2 text-sm">
              <div><strong>auth_token:</strong> {{ hasToken ? '✅ Present' : '❌ Missing' }}</div>
              <div><strong>user_data:</strong> {{ hasUserData ? '✅ Present' : '❌ Missing' }}</div>
              <div v-if="storedUserData" class="mt-2">
                <strong>Stored User:</strong>
                <pre class="mt-1 text-xs bg-white p-2 rounded border">{{ JSON.stringify(storedUserData, null, 2) }}</pre>
              </div>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex space-x-4">
          <button
            @click="refreshAuth"
            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition-colors"
          >
            Refresh Auth State
          </button>
          <button
            @click="clearAuth"
            class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition-colors"
          >
            Clear Auth Data
          </button>
          <button
            @click="testLogin"
            class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition-colors"
          >
            Test Login Flow
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { computed, ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from '../composables/useAuth'
import { useAuthStore } from '../stores/auth'
import { ROLES, getDefaultDashboard } from '../utils/permissions'
import { useStore } from 'vuex'

export default {
  name: 'RoleBasedAuthDiagnostic',
  setup() {
    const router = useRouter()
    const store = useStore()
    const {
      user,
      userRole,
      userRoles,
      isAuthenticated,
      isLoading,
      _hasRole,
      _login,
      logout
    } = useAuth()
    const piniaAuthStore = useAuthStore()

    const hasToken = ref(false)
    const hasUserData = ref(false)
    const storedUserData = ref(null)

    // Test routes for access checking
    const testRoutes = [
      { path: '/admin-dashboard', roles: ['admin'] },
      { path: '/user-dashboard', roles: ['staff'] },
      { path: '/ict-dashboard', roles: ['ict_officer'] },
      { path: '/hod-dashboard', roles: ['head_of_department'] },
      { path: '/dict-dashboard', roles: ['ict_director'] },
      { path: '/divisional-dashboard', roles: ['divisional_director'] },
      { path: '/onboarding', roles: null },
      { path: '/settings', roles: null }
    ]

    const checkLocalStorage = () => {
      hasToken.value = !!localStorage.getItem('auth_token')
      hasUserData.value = !!localStorage.getItem('user_data')

      if (hasUserData.value) {
        try {
          storedUserData.value = JSON.parse(localStorage.getItem('user_data'))
        } catch (error) {
          console.error('Failed to parse stored user data:', error)
          storedUserData.value = null
        }
      }
    }

    onMounted(() => {
      checkLocalStorage()
      // Ensure Pinia auth is initialized
      if (!piniaAuthStore.isInitialized) {
        piniaAuthStore.initializeAuth()
      }
    })

    const vuexAuth = computed(() => ({
      isAuthenticated: isAuthenticated.value,
      user: user.value,
      userRole: userRole.value,
      userRoles: userRoles.value,
      isLoading: isLoading.value
    }))

    const piniaAuth = computed(() => ({
      isAuthenticated: piniaAuthStore.isAuthenticated,
      user: piniaAuthStore.user,
      userRole: piniaAuthStore.userRole,
      isInitialized: piniaAuthStore.isInitialized,
      isLoading: piniaAuthStore.isLoading
    }))

    const currentUserRole = computed(() => userRole.value)
    const currentUserDashboard = computed(() =>
      currentUserRole.value ? getDefaultDashboard(currentUserRole.value) : null
    )

    const hasAccess = (route) => {
      if (!route.roles) return true // Public route
      if (!currentUserRole.value) return false
      return route.roles.includes(currentUserRole.value)
    }

    const navigateToDashboard = () => {
      if (currentUserDashboard.value) {
        router.push(currentUserDashboard.value)
      }
    }

    const testNavigation = (path) => {
      router.push(path).catch(error => {
        console.log('Navigation test result:', error.message)
      })
    }

    const refreshAuth = async() => {
      console.log('🔄 Refreshing auth state...')
      checkLocalStorage()

      // Restore Vuex session
      await store.dispatch('auth/restoreSession')

      // Sync Pinia
      await piniaAuthStore.syncWithVuex()

      console.log('✅ Auth refreshed')
    }

    const clearAuth = async() => {
      await logout()
      checkLocalStorage()
    }

    const testLogin = () => {
      router.push('/login')
    }

    return {
      // Data
      ROLES,
      testRoutes,
      hasToken,
      hasUserData,
      storedUserData,

      // Computed
      vuexAuth,
      piniaAuth,
      currentUserRole,
      currentUserDashboard,

      // Methods
      getDefaultDashboard,
      hasAccess,
      navigateToDashboard,
      testNavigation,
      refreshAuth,
      clearAuth,
      testLogin
    }
  }
}
</script>

<style scoped>
/* Component-specific styles */
</style>
