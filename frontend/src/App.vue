<template>
  <div id="app">
    <!-- Debug info -->
    <div v-if="showDebug" class="fixed top-0 left-0 bg-black text-white p-4 z-50 text-xs">
      <div>Auth Loading: {{ isLoading }}</div>
      <div>Is Authenticated: {{ isAuthenticated }}</div>
      <div>User Role: {{ userRole }}</div>
      <div>Current User: {{ currentUser?.name }}</div>
      <div>Auth Error: {{ authError }}</div>
    </div>

    <!-- Loading overlay during auth initialization -->
    <div
      v-if="isLoading"
      class="fixed inset-0 bg-blue-900 flex items-center justify-center z-[10000]"
    >
      <div class="text-center text-white">
        <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-white mx-auto mb-4"></div>
        <p class="text-lg font-medium">Initializing Authentication...</p>
      </div>
    </div>

    <!-- Main content -->
    <main v-else>
      <router-view />
    </main>

    <!-- Global error notification -->
    <div
      v-if="authError"
      class="fixed top-4 right-4 bg-red-500 text-white px-6 py-4 rounded-xl shadow-2xl z-[9999] max-w-md"
    >
      <div class="flex items-center">
        <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center mr-3">
          <i class="fas fa-exclamation-triangle text-sm"></i>
        </div>
        <p class="font-medium flex-1">{{ authError }}</p>
        <button
          @click="clearAuthError"
          class="ml-3 text-white/80 hover:text-white transition-colors duration-200"
        >
          <i class="fas fa-times"></i>
        </button>
      </div>
    </div>

    <!-- Debug toggle -->
    <button
      @click="showDebug = !showDebug"
      class="fixed bottom-4 left-4 bg-gray-800 text-white px-2 py-1 rounded text-xs z-50"
    >
      Debug
    </button>
  </div>
</template>

<script>
import { onMounted, ref } from 'vue'

export default {
  name: 'App',

  setup() {
    const showDebug = ref(false)
    const isLoading = ref(false)
    const isAuthenticated = ref(false)
    const currentUser = ref(null)
    const userRole = ref(null)
    const authError = ref(null)

    // Initialize authentication on app mount
    onMounted(async() => {
      console.log('🔄 App: Mounted, initializing auth...')

      try {
        // Import auth utility
        const authModule = await import('@/utils/auth')
        const { useAuth } = authModule

        if (useAuth) {
          const auth = useAuth()

          // Set reactive references
          isLoading.value = auth.isLoading.value
          isAuthenticated.value = auth.isAuthenticated.value
          currentUser.value = auth.currentUser.value
          userRole.value = auth.userRole.value
          authError.value = auth.error.value

          console.log('✅ App: Auth system ready')
          console.log('  - isAuthenticated:', auth.isAuthenticated.value)
          console.log('  - userRole:', auth.userRole.value)
          console.log('  - currentUser:', auth.currentUser.value?.name)
        } else {
          console.error('❌ App: useAuth not available')
        }
      } catch (error) {
        console.error('❌ App: Auth system error:', error)
        authError.value = error.message
      }
    })

    // Clear auth error
    const clearAuthError = () => {
      authError.value = null
    }

    return {
      // Debug
      showDebug,

      // Auth state
      isAuthenticated,
      currentUser,
      userRole,
      isLoading,
      authError,

      // Methods
      clearAuthError
    }
  }
}
</script>

<style>
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  color: #2c3e50;
  min-height: 100vh;
}

main {
  min-height: 100vh;
}

/* Global styles */
* {
  box-sizing: border-box;
}

body {
  margin: 0;
  padding: 0;
}

/* Loading animation */
@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.animate-spin {
  animation: spin 1s linear infinite;
}
</style>
