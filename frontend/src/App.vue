<template>
  <div id="app">
    <!-- Debug info -->
    <div v-if="showDebug" class="fixed top-0 left-0 bg-black text-white p-4 z-50 text-xs">
      <div>Auth Ready: {{ isAuthReady }}</div>
      <div>Restoring Session: {{ restoringSession }}</div>
      <div>Session Restored: {{ sessionRestored }}</div>
      <div>Is Authenticated: {{ isAuthenticated }}</div>
      <div>User Role: {{ userRole }}</div>
      <div>Current User: {{ currentUser?.name }}</div>
      <div>Auth Error: {{ authError }}</div>
    </div>

    <!-- Loading screen during auth initialization -->
    <AuthLoadingScreen v-if="!isAuthReady" :message="loadingMessage" :progress="loadingProgress" />

    <!-- Main content -->
    <main v-else>
      <router-view />
    </main>

    <!-- Notification Systems -->
    <NotificationSystem />
    <CompactNotificationSystem />

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

    <!-- Auth Debugger Component removed - using inline debug info above -->
  </div>
</template>

<script>
  import { computed, onMounted, ref } from 'vue'
  import AuthLoadingScreen from './components/AuthLoadingScreen.vue'
  import NotificationSystem from './components/NotificationSystem.vue'
  import CompactNotificationSystem from './components/CompactNotificationSystem.vue'
  import { useAuthStore } from './stores/auth'

  export default {
    name: 'App',
    components: {
      AuthLoadingScreen,
      NotificationSystem,
      CompactNotificationSystem
    },

    setup() {
      const authStore = useAuthStore()
      const showDebug = ref(false)

      // Computed properties from Pinia auth store
      const isAuthReady = computed(() => authStore.isInitialized)
      const restoringSession = computed(() => false)
      const sessionRestored = computed(() => authStore.isInitialized)
      const isAuthenticated = computed(() => authStore.isAuthenticated)
      const currentUser = computed(() => authStore.user)
      const userRole = computed(() => authStore.userRole)
      const authError = computed(() => authStore.error)

      // Loading message based on current state
      const loadingMessage = computed(() => {
        if (restoringSession.value) {
          return 'Restoring your session...'
        }
        if (!sessionRestored.value) {
          return 'Initializing authentication...'
        }
        return 'Preparing application...'
      })

      // Loading progress based on current state
      const loadingProgress = computed(() => {
        if (!sessionRestored.value) {
          return 30
        }
        if (restoringSession.value) {
          return 60
        }
        if (isAuthReady.value) {
          return 100
        }
        return 80
      })

      // Initialize authentication on app mount
      onMounted(async () => {
        console.log('🔄 App: Mounted, auth should already be initialized in main.js')

        // Log current auth state
        console.log('🔍 App: Current auth state:', {
          isAuthReady: isAuthReady.value,
          restoringSession: restoringSession.value,
          sessionRestored: sessionRestored.value,
          isAuthenticated: isAuthenticated.value,
          userRole: userRole.value,
          userName: currentUser.value?.name
        })
      })

      // Clear auth error
      const clearAuthError = () => {
        authStore.clearError()
      }

      return {
        // Debug
        showDebug,

        // Auth state
        isAuthReady,
        restoringSession,
        sessionRestored,
        isAuthenticated,
        currentUser,
        userRole,
        authError,
        loadingMessage,
        loadingProgress,

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
    0% {
      transform: rotate(0deg);
    }
    100% {
      transform: rotate(360deg);
    }
  }

  .animate-spin {
    animation: spin 1s linear infinite;
  }
</style>
