<template>
  <div class="min-h-screen bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
      <h1 class="text-3xl font-bold text-gray-900 mb-8">Admin Dashboard Diagnostic</h1>

      <!-- Authentication Status -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Authentication Status</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <p><strong>Is Authenticated:</strong>
              <span :class="isAuthenticated ? 'text-green-600' : 'text-red-600'">
                {{ isAuthenticated ? 'Yes' : 'No' }}
              </span>
            </p>
            <p><strong>User Role:</strong>
              <span class="text-blue-600">{{ userRole || 'None' }}</span>
            </p>
            <p><strong>User Name:</strong>
              <span class="text-blue-600">{{ userName || 'None' }}</span>
            </p>
            <p><strong>Is Admin:</strong>
              <span :class="isAdmin ? 'text-green-600' : 'text-red-600'">
                {{ isAdmin ? 'Yes' : 'No' }}
              </span>
            </p>
          </div>
          <div>
            <p><strong>Token Present:</strong>
              <span :class="!!token ? 'text-green-600' : 'text-red-600'">
                {{ !!token ? 'Yes' : 'No' }}
              </span>
            </p>
            <p><strong>Session Restored:</strong>
              <span :class="sessionRestored ? 'text-green-600' : 'text-red-600'">
                {{ sessionRestored ? 'Yes' : 'No' }}
              </span>
            </p>
          </div>
        </div>
      </div>

      <!-- User Object -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">User Object</h2>
        <pre class="bg-gray-100 p-4 rounded text-sm overflow-auto">{{ JSON.stringify(user, null, 2) }}</pre>
      </div>

      <!-- Route Information -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Route Information</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <p><strong>Current Route:</strong> {{ $route.path }}</p>
            <p><strong>Route Name:</strong> {{ $route.name }}</p>
            <p><strong>Has Admin Access:</strong>
              <span :class="hasAdminAccess ? 'text-green-600' : 'text-red-600'">
                {{ hasAdminAccess ? 'Yes' : 'No' }}
              </span>
            </p>
          </div>
          <div>
            <p><strong>Route Meta:</strong></p>
            <pre class="bg-gray-100 p-2 rounded text-xs">{{ JSON.stringify($route.meta, null, 2) }}</pre>
          </div>
        </div>
      </div>

      <!-- Permissions Check -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Permissions Check</h2>
        <div class="space-y-2">
          <p><strong>Can Access Admin Dashboard:</strong>
            <span :class="canAccessAdminDashboard ? 'text-green-600' : 'text-red-600'">
              {{ canAccessAdminDashboard ? 'Yes' : 'No' }}
            </span>
          </p>
          <p><strong>Has Admin Role:</strong>
            <span :class="hasRole(['admin']) ? 'text-green-600' : 'text-red-600'">
              {{ hasRole(['admin']) ? 'Yes' : 'No' }}
            </span>
          </p>
          <p><strong>Default Dashboard:</strong>
            <span class="text-blue-600">{{ defaultDashboard || 'None' }}</span>
          </p>
        </div>
      </div>

      <!-- Actions -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Actions</h2>
        <div class="space-x-4">
          <button
            @click="tryNavigateToAdmin"
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
          >
            Try Navigate to Admin Dashboard
          </button>
          <button
            @click="refreshAuth"
            class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded"
          >
            Refresh Auth State
          </button>
          <button
            @click="checkComponents"
            class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded"
          >
            Check Components
          </button>
        </div>
      </div>

      <!-- Component Check Results -->
      <div v-if="componentCheckResults" class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Component Check Results</h2>
        <pre class="bg-gray-100 p-4 rounded text-sm overflow-auto">{{ componentCheckResults }}</pre>
      </div>

      <!-- Error Messages -->
      <div v-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        <strong>Error:</strong> {{ error }}
      </div>

      <!-- Navigation Results -->
      <div v-if="navigationResult" class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-6">
        <strong>Navigation Result:</strong> {{ navigationResult }}
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import { useAuth } from '@/composables/useAuth'
import { useStore } from 'vuex'
import { useRouter } from 'vue-router'
import { getDefaultDashboard } from '@/utils/permissions'

export default {
  name: 'AdminDashboardDiagnostic',
  setup() {
    const {
      user,
      userName,
      token,
      isAuthenticated,
      userRole,
      isAdmin,
      hasRole,
      ROLES
    } = useAuth()

    const store = useStore()
    const router = useRouter()

    const error = ref('')
    const navigationResult = ref('')
    const componentCheckResults = ref('')

    const sessionRestored = computed(() => store.getters['auth/sessionRestored'])
    const defaultDashboard = computed(() => getDefaultDashboard(userRole.value))

    const hasAdminAccess = computed(() => {
      return isAuthenticated.value && hasRole([ROLES.ADMIN])
    })

    const canAccessAdminDashboard = computed(() => {
      const adminRoute = router.resolve('/admin-dashboard')
      if (!adminRoute.meta?.roles) return true
      return hasRole(adminRoute.meta.roles)
    })

    const tryNavigateToAdmin = async() => {
      try {
        navigationResult.value = 'Attempting navigation...'
        await router.push('/admin-dashboard')
        navigationResult.value = 'Navigation successful!'
      } catch (err) {
        error.value = `Navigation failed: ${err.message}`
        navigationResult.value = 'Navigation failed!'
      }
    }

    const refreshAuth = async() => {
      try {
        await store.dispatch('auth/restoreSession')
        navigationResult.value = 'Auth state refreshed!'
      } catch (err) {
        error.value = `Auth refresh failed: ${err.message}`
      }
    }

    const checkComponents = async() => {
      const results = []

      try {
        // Try to import AdminDashboard component
        const AdminDashboard = await import('@/components/admin/AdminDashboard.vue')
        results.push('✅ AdminDashboard component loads successfully')

        // Check if component has proper export
        if (AdminDashboard.default) {
          results.push('✅ AdminDashboard has default export')
        } else {
          results.push('❌ AdminDashboard missing default export')
        }

      } catch (err) {
        results.push(`❌ AdminDashboard component failed to load: ${err.message}`)
      }

      try {
        // Check other required components
        await import('@/components/header.vue')
        results.push('✅ Header component loads successfully')
      } catch (err) {
        results.push(`❌ Header component failed: ${err.message}`)
      }

      try {
        await import('@/components/ModernSidebar.vue')
        results.push('✅ ModernSidebar component loads successfully')
      } catch (err) {
        results.push(`❌ ModernSidebar component failed: ${err.message}`)
      }

      try {
        await import('@/components/footer.vue')
        results.push('✅ Footer component loads successfully')
      } catch (err) {
        results.push(`❌ Footer component failed: ${err.message}`)
      }

      componentCheckResults.value = results.join('\n')
    }

    onMounted(() => {
      console.log('🔍 Admin Dashboard Diagnostic mounted')
      console.log('Auth state:', {
        isAuthenticated: isAuthenticated.value,
        userRole: userRole.value,
        isAdmin: isAdmin.value,
        user: user.value
      })
    })

    return {
      user,
      userName,
      token,
      isAuthenticated,
      userRole,
      isAdmin,
      hasRole,
      sessionRestored,
      defaultDashboard,
      hasAdminAccess,
      canAccessAdminDashboard,
      error,
      navigationResult,
      componentCheckResults,
      tryNavigateToAdmin,
      refreshAuth,
      checkComponents
    }
  }
}
</script>

<style scoped>
/* Add any specific styles here */
</style>
