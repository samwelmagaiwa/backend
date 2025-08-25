<template>
  <div class="fixed top-4 right-4 bg-white border border-gray-300 rounded-lg shadow-lg p-4 max-w-md z-50">
    <h3 class="text-lg font-bold mb-2">Auth Debug Info</h3>

    <div class="space-y-2 text-sm">
      <div>
        <strong>Auth State:</strong>
        <div class="ml-2">
          <div>Authenticated: {{ isAuthenticated }}</div>
          <div>Loading: {{ isLoading }}</div>
          <div>User Role: {{ userRole || 'null' }}</div>
          <div>User Name: {{ userName || 'null' }}</div>
        </div>
      </div>

      <div>
        <strong>Current Route:</strong>
        <div class="ml-2">
          <div>Path: {{ $route.path }}</div>
          <div>Name: {{ $route.name }}</div>
          <div>Query: {{ JSON.stringify($route.query) }}</div>
        </div>
      </div>

      <div>
        <strong>Route Meta:</strong>
        <div class="ml-2">
          <div>Requires Auth: {{ $route.meta.requiresAuth }}</div>
          <div>Required Roles: {{ JSON.stringify($route.meta.roles) }}</div>
        </div>
      </div>

      <div>
        <strong>Permissions:</strong>
        <div class="ml-2">
          <div>Default Dashboard: {{ defaultDashboard || 'null' }}</div>
          <div>Can Access Current Route: {{ canAccessCurrentRoute }}</div>
        </div>
      </div>

      <div>
        <strong>LocalStorage:</strong>
        <div class="ml-2">
          <div>Has Token: {{ hasToken }}</div>
          <div>Has User Data: {{ hasUserData }}</div>
          <div>Stored Role: {{ storedRole || 'null' }}</div>
        </div>
      </div>
    </div>

    <button
      @click="refreshAuth"
      class="mt-3 px-3 py-1 bg-blue-500 text-white rounded text-sm hover:bg-blue-600"
    >
      Refresh Auth
    </button>

    <button
      @click="visible = false"
      class="mt-3 ml-2 px-3 py-1 bg-gray-500 text-white rounded text-sm hover:bg-gray-600"
    >
      Hide
    </button>
  </div>
</template>

<script>
import { computed, ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useAuth } from '../composables/useAuth'
import { hasRouteAccess } from '../utils/permissions'
import auth from '../utils/auth'

export default {
  name: 'AuthDebugger',
  setup() {
    const route = useRoute()
    const { isAuthenticated, userRole, userName, isLoading, defaultDashboard } = useAuth()

    const visible = ref(true)
    const hasToken = ref(false)
    const hasUserData = ref(false)
    const storedRole = ref(null)

    const canAccessCurrentRoute = computed(() => {
      return hasRouteAccess(userRole.value, route.path)
    })

    const checkLocalStorage = () => {
      hasToken.value = !!localStorage.getItem('auth_token')
      hasUserData.value = !!localStorage.getItem('user_data')

      try {
        const userData = localStorage.getItem('user_data')
        if (userData) {
          const user = JSON.parse(userData)
          storedRole.value = user.role
        }
      } catch (error) {
        storedRole.value = 'parse error'
      }
    }

    const refreshAuth = async() => {
      console.log('🔄 Manual auth refresh triggered')
      await auth.initializeAuth(true)
      checkLocalStorage()
    }

    onMounted(() => {
      checkLocalStorage()
    })

    return {
      visible,
      isAuthenticated,
      userRole,
      userName,
      isLoading,
      defaultDashboard,
      canAccessCurrentRoute,
      hasToken,
      hasUserData,
      storedRole,
      refreshAuth
    }
  }
}
</script>
