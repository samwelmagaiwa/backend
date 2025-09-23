<template>
  <div class="p-6 bg-white min-h-screen">
    <h1 class="text-2xl font-bold mb-6">🔧 Head of IT Navigation Debug</h1>

    <div class="space-y-6">
      <!-- Authentication Status -->
      <div class="bg-blue-50 p-4 rounded-lg">
        <h2 class="text-lg font-semibold mb-2">Authentication Status</h2>
        <div class="space-y-1">
          <p><span class="font-medium">Is Authenticated:</span> {{ isAuthenticated }}</p>
          <p><span class="font-medium">User Role:</span> {{ userRole }}</p>
          <p><span class="font-medium">User Name:</span> {{ userName }}</p>
          <p><span class="font-medium">Is Head of IT:</span> {{ isHeadOfIt }}</p>
        </div>
      </div>

      <!-- Permissions -->
      <div class="bg-green-50 p-4 rounded-lg">
        <h2 class="text-lg font-semibold mb-2">Permissions</h2>
        <div class="space-y-1">
          <p><span class="font-medium">Allowed Routes:</span></p>
          <ul class="list-disc ml-4">
            <li v-for="route in allowedRoutes" :key="route">{{ route }}</li>
          </ul>
        </div>
      </div>

      <!-- Navigation Test -->
      <div class="bg-yellow-50 p-4 rounded-lg">
        <h2 class="text-lg font-semibold mb-2">Navigation Test</h2>
        <div class="space-y-2">
          <button
            @click="navigateTo('/head_of_it-dashboard')"
            class="bg-blue-600 text-white px-4 py-2 rounded mr-2"
          >
            Go to Dashboard
          </button>
          <button
            @click="navigateTo('/head_of_it-dashboard/combined-requests')"
            class="bg-green-600 text-white px-4 py-2 rounded mr-2"
          >
            Go to Access Requests
          </button>
          <p class="text-sm text-gray-600 mt-2">Current Route: {{ $route.path }}</p>
        </div>
      </div>

      <!-- Router Info -->
      <div class="bg-gray-50 p-4 rounded-lg">
        <h2 class="text-lg font-semibold mb-2">Router Information</h2>
        <div class="space-y-1">
          <p><span class="font-medium">Current Path:</span> {{ $route.path }}</p>
          <p><span class="font-medium">Route Name:</span> {{ $route.name }}</p>
          <p><span class="font-medium">Route Meta:</span></p>
          <pre class="text-xs bg-white p-2 rounded">{{ JSON.stringify($route.meta, null, 2) }}</pre>
        </div>
      </div>

      <!-- Local Storage Debug -->
      <div class="bg-purple-50 p-4 rounded-lg">
        <h2 class="text-lg font-semibold mb-2">Local Storage Debug</h2>
        <div class="space-y-1">
          <p>
            <span class="font-medium">Auth Token:</span> {{ authToken ? 'Present' : 'Missing' }}
          </p>
          <p><span class="font-medium">User Data:</span> {{ userData ? 'Present' : 'Missing' }}</p>
          <pre v-if="userData" class="text-xs bg-white p-2 rounded">{{
            JSON.stringify(userData, null, 2)
          }}</pre>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import { computed } from 'vue'
  import { useRouter } from 'vue-router'
  import { useAuth } from '@/composables/useAuth'
  import { ROLES, ROLE_PERMISSIONS } from '@/utils/permissions'

  export default {
    name: 'HeadOfItDebug',
    setup() {
      const router = useRouter()
      const { isAuthenticated, userRole, userName } = useAuth()

      const isHeadOfIt = computed(() => userRole.value === ROLES.HEAD_OF_IT)

      const allowedRoutes = computed(() => {
        if (!userRole.value) return []
        const permissions = ROLE_PERMISSIONS[userRole.value]
        return permissions ? permissions.routes : []
      })

      const authToken = computed(() => localStorage.getItem('auth_token'))
      const userData = computed(() => {
        const data = localStorage.getItem('user_data')
        try {
          return data ? JSON.parse(data) : null
        } catch {
          return null
        }
      })

      function navigateTo(path) {
        console.log('🚀 Navigating to:', path)
        router
          .push(path)
          .then(() => {
            console.log('✅ Navigation successful to:', path)
          })
          .catch((error) => {
            console.error('❌ Navigation failed:', error)
          })
      }

      return {
        isAuthenticated,
        userRole,
        userName,
        isHeadOfIt,
        allowedRoutes,
        authToken,
        userData,
        navigateTo
      }
    }
  }
</script>
