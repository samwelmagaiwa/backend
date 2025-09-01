<template>
  <div class="fixed bottom-4 right-4 bg-black text-white p-4 rounded-lg text-xs max-w-md z-50" v-if="showDebug">
    <div class="flex justify-between items-center mb-2">
      <h3 class="font-bold">Auth Debug Info</h3>
      <button @click="showDebug = false" class="text-gray-400 hover:text-white">×</button>
    </div>

    <div class="space-y-1">
      <div><strong>Auth Ready:</strong> {{ authState.isAuthReady }}</div>
      <div><strong>Authenticated:</strong> {{ authState.isAuthenticated }}</div>
      <div><strong>User Role:</strong> {{ authState.userRole }}</div>
      <div><strong>User Name:</strong> {{ authState.userName }}</div>
      <div><strong>Session Restored:</strong> {{ authState.sessionRestored }}</div>
      <div><strong>Restoring Session:</strong> {{ authState.restoringSession }}</div>
      <div><strong>Current Route:</strong> {{ currentRoute }}</div>
      <div><strong>Default Dashboard:</strong> {{ defaultDashboard }}</div>
      <div><strong>LocalStorage Token:</strong> {{ hasToken ? 'Yes' : 'No' }}</div>
      <div><strong>LocalStorage User:</strong> {{ hasUserData ? 'Yes' : 'No' }}</div>

      <div class="mt-2 pt-2 border-t border-gray-600">
        <div><strong>Raw User Data:</strong></div>
        <pre class="text-xs bg-gray-800 p-1 rounded mt-1 overflow-auto max-h-20">{{ rawUserData }}</pre>
      </div>

      <div class="mt-2 pt-2 border-t border-gray-600">
        <button @click="refreshAuth" class="bg-blue-600 text-white px-2 py-1 rounded text-xs mr-2">
          Refresh Auth
        </button>
        <button @click="clearStorage" class="bg-red-600 text-white px-2 py-1 rounded text-xs">
          Clear Storage
        </button>
      </div>
    </div>
  </div>

  <!-- Toggle button -->
  <button
    @click="showDebug = !showDebug"
    class="fixed bottom-4 left-4 bg-gray-800 text-white px-3 py-2 rounded text-xs z-50"
  >
    🐛 Debug
  </button>
</template>

<script>
import { computed, ref } from 'vue'
import { useStore } from 'vuex'
import { useRoute } from 'vue-router'

export default {
  name: 'AuthDebugger',
  setup() {
    const store = useStore()
    const route = useRoute()
    const showDebug = ref(false)

    const authState = computed(() => ({
      isAuthReady: store.getters['auth/isAuthReady'],
      isAuthenticated: store.getters['auth/isAuthenticated'],
      userRole: store.getters['auth/userRole'],
      userName: store.getters['auth/user']?.name,
      sessionRestored: store.getters['auth/sessionRestored'],
      restoringSession: store.getters['auth/restoringSession']
    }))

    const currentRoute = computed(() => route.path)

    const defaultDashboard = computed(() => {
      if (!authState.value.userRole) return 'N/A'

      const dashboardMap = {
        'admin': '/admin-dashboard',
        'staff': '/user-dashboard',
        'head_of_department': '/hod-dashboard',
        'divisional_director': '/divisional-dashboard',
        'ict_director': '/dict-dashboard',
        'ict_officer': '/ict-dashboard'
      }

      return dashboardMap[authState.value.userRole] || 'Unknown'
    })

    const hasToken = computed(() => !!localStorage.getItem('auth_token'))
    const hasUserData = computed(() => !!localStorage.getItem('user_data'))

    const rawUserData = computed(() => {
      try {
        const userData = localStorage.getItem('user_data')
        return userData ? JSON.parse(userData) : 'No data'
      } catch (e) {
        return 'Invalid JSON'
      }
    })

    const refreshAuth = async() => {
      console.log('🔄 Debug: Manually refreshing auth...')
      await store.dispatch('auth/restoreSession')
    }

    const clearStorage = () => {
      localStorage.removeItem('auth_token')
      localStorage.removeItem('user_data')
      localStorage.removeItem('session_data')
      store.commit('auth/LOGOUT')
      console.log('🗑️ Debug: Storage cleared')
    }

    return {
      showDebug,
      authState,
      currentRoute,
      defaultDashboard,
      hasToken,
      hasUserData,
      rawUserData,
      refreshAuth,
      clearStorage
    }
  }
}
</script>
