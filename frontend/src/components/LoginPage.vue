<template>
  <div class="min-h-screen bg-gray-100">
    <div class="flex bg-white overflow-hidden w-full h-screen">
      <!-- Left side: Branding -->
      <div class="hidden md:block w-1/2 relative overflow-hidden">
        <div
          class="absolute inset-0 bg-cover bg-center background-animate"
          style="background-image: url('/assets/images/image1.jpg')"
        ></div>
        <div class="relative z-10 flex flex-col items-up justify-up h-full p-8 text-white"></div>
        <!-- Enhanced S-curved design -->
        <svg
          class="absolute right-0 top-0 h-full w-24 text-white"
          viewBox="0 0 100 100"
          preserveAspectRatio="none"
        >
          <path d="M0 0 C50 0 0 50 0 50 C0 50 50 100 100 100 L100 0 Z" fill="currentColor" />
        </svg>
      </div>

      <!-- Right side: Login Form -->
      <div class="w-full md:w-1/2 p-8 flex flex-col justify-center items-center">
        <h1 class="text-4xl font-serif font-bold text-primary animate-fadeIn mb-4">
          Muhimbili National Hospital
        </h1>

        <div
          class="w-40 h-40 rounded-full overflow-hidden border-2 border-primary flex items-center justify-center mb-12"
        >
          <img
            src="/assets/images/logo.jpg"
            alt="Muhimbili Logo"
            class="max-w-full max-h-full animate-flipX"
          />
        </div>
        <div class="w-full max-w-md">
          <h2 class="text-2xl font-bold mb-6 text-center text-primary">Login</h2>

          <!-- Error message display -->
          <div
            v-if="errorMessage"
            class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded flex items-center"
          >
            <i class="fas fa-exclamation-triangle mr-2"></i>
            {{ errorMessage }}
          </div>
          <form @submit.prevent="handleLogin">
            <div class="mb-4">
              <label class="block text-gray-700 font-bold text-left">Email</label>
              <input
                v-model="email"
                type="email"
                class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                required
                placeholder="Enter your email"
                :disabled="loading"
              />
            </div>

            <div class="mb-4">
              <label class="block text-gray-700 font-bold text-left">Password</label>
              <input
                v-model="password"
                type="password"
                class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                required
                placeholder="Enter your password"
                :disabled="loading"
              />
            </div>

            <!-- Remember Email Checkbox -->
            <div class="mb-4 flex items-center justify-between">
              <label class="flex items-center text-sm text-gray-600">
                <input
                  v-model="rememberEmail"
                  type="checkbox"
                  class="mr-2 rounded focus:ring-2 focus:ring-primary"
                />
                <span>Remember my email</span>
              </label>

              <!-- Clear saved email button -->
              <button
                v-if="email"
                type="button"
                @click="clearSavedEmail"
                class="text-xs text-gray-500 hover:text-red-600 transition-colors"
                title="Clear saved email"
              >
                <i class="fas fa-times mr-1"></i>
                Clear
              </button>
            </div>

            <button
              type="submit"
              :disabled="isLoading"
              class="w-full bg-primary text-white p-2 rounded hover:bg-opacity-90 transition ease-in-out duration-300 animate-fadeIn delay-2 animate-bounceIn disabled:opacity-50"
            >
              <span v-if="loading">
                <i class="fas fa-spinner fa-spin mr-2"></i>
                Logging in...
              </span>
              <span v-else>
                <i class="fas fa-sign-in-alt mr-2"></i>
                Login
              </span>
            </button>
          </form>

          <!-- Email Memory Info -->
          <div
            v-if="email && rememberEmail"
            class="mt-4 p-3 bg-blue-50 rounded-lg border border-blue-200"
          >
            <div class="flex items-center text-blue-700">
              <i class="fas fa-user-check mr-2"></i>
              <span class="text-sm font-medium">Email Remembered</span>
            </div>
            <p class="text-xs text-blue-600 mt-1">
              Your email will be saved for faster login next time.
            </p>
          </div>

          <!-- Backend Connection Info -->
          <div class="mt-6 p-4 bg-green-50 rounded-lg border border-green-200">
            <div class="flex items-center justify-between text-green-700 mb-2">
              <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <span class="font-semibold">Backend API</span>
              </div>
              <button
                @click="testConnection"
                :disabled="testingConnection"
                class="px-3 py-1 bg-blue-500 text-white text-sm rounded hover:bg-blue-600 disabled:opacity-50"
              >
                <i v-if="testingConnection" class="fas fa-spinner fa-spin mr-1"></i>
                <i v-else class="fas fa-plug mr-1"></i>
                {{ testingConnection ? 'Testing...' : 'Test' }}
              </button>
            </div>
            <p class="text-xs text-green-600 mb-2">
              Using Laravel backend at
              <code class="bg-green-100 px-1 rounded">{{ apiUrl }}</code>
            </p>
            <div
              v-if="connectionStatus"
              class="text-xs"
              :class="connectionStatus.success ? 'text-green-600' : 'text-red-600'"
            >
              <i
                :class="connectionStatus.success ? 'fas fa-check-circle' : 'fas fa-times-circle'"
                class="mr-1"
              ></i>
              {{ connectionStatus.message }}
            </div>
            <p class="text-xs text-gray-600 mt-1">
              Please use your registered credentials to login.
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Success Snackbar -->
    <div
      v-if="showSuccessSnackbar"
      class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50"
    >
      <div class="flex items-center">
        <i class="fas fa-check-circle mr-2"></i>
        Login successful! Redirecting...
      </div>
    </div>
  </div>
</template>

<script>
  import { ref, computed, watch, onMounted } from 'vue'
  import { useRoute } from 'vue-router'
  import { useAuth } from '@/composables/useAuth'
  import loginMemory from '@/utils/loginMemory'

  export default {
    name: 'LoginPage',
    setup() {
      const { login, isLoading, error, clearError } = useAuth()
      const route = useRoute()

      // Connection testing
      const testingConnection = ref(false)
      const connectionStatus = ref(null)

      // Form data with localStorage persistence for email
      const email = ref('')
      const password = ref('')
      const rememberEmail = ref(true)
      const loading = ref(false)

      // API URL for display
      const apiUrl = process.env.VUE_APP_API_URL || 'http://localhost:8000/api'

      // Load saved email on component mount
      const loadSavedEmail = () => {
        const savedEmail = loginMemory.getSavedEmail()
        if (savedEmail) {
          email.value = savedEmail
          rememberEmail.value = true
        }
      }

      // Save email to localStorage when remember is enabled
      const saveEmail = () => {
        if (rememberEmail.value && email.value) {
          loginMemory.saveEmail(email.value)
        } else {
          loginMemory.clearSavedEmail()
        }
      }

      // Watch for changes in rememberEmail checkbox
      watch(rememberEmail, (newValue) => {
        if (!newValue) {
          loginMemory.clearSavedEmail()
        } else if (email.value) {
          saveEmail()
        }
      })

      // Error handling
      const errorMessage = computed(() => {
        if (route.query.error === 'access_denied') {
          return 'Access denied. You do not have permission to access that page.'
        }
        return error.value
      })

      // Methods
      const handleLogin = async () => {
        clearError()

        if (!email.value || !password.value) {
          return
        }

        loading.value = true

        // Save email if remember is enabled
        if (rememberEmail.value) {
          saveEmail()
        }

        console.log('🚀 Attempting login with:', {
          email: email.value,
          rememberEmail: rememberEmail.value
        })

        // Use the auth composable's login method which handles navigation
        const result = await login({
          email: email.value,
          password: password.value
        })

        if (result.success) {
          console.log('✅ Login successful! Navigation should be handled by auth composable.')
          // Clear password but keep email if remember is enabled
          password.value = ''
        } else {
          console.error('❌ Login failed:', result.error)
          // Clear password on failed login for security
          password.value = ''
        }

        loading.value = false
      }

      const clearSavedEmail = () => {
        loginMemory.clearSavedEmail()
        email.value = ''
        rememberEmail.value = false
      }

      // Test API connection
      const testConnection = async () => {
        try {
          testingConnection.value = true
          connectionStatus.value = null

          console.log('🔌 Testing API connection to:', apiUrl)

          // Try to make a simple request to test connectivity
          const response = await fetch(apiUrl.replace('/api', '/api/user'), {
            method: 'GET',
            headers: {
              Accept: 'application/json',
              'Content-Type': 'application/json'
            }
          })

          if (response.ok || response.status === 401) {
            // 401 is expected for unauthenticated requests, but means API is reachable
            connectionStatus.value = {
              success: true,
              message: `API is reachable (Status: ${response.status})`
            }
          } else {
            connectionStatus.value = {
              success: false,
              message: `API connection failed (Status: ${response.status})`
            }
          }
        } catch (error) {
          console.error('🔌 API connection test failed:', error)
          connectionStatus.value = {
            success: false,
            message: 'Connection failed - API unreachable'
          }
        } finally {
          testingConnection.value = false
        }
      }

      // Initialize component
      onMounted(() => {
        clearError()
        loadSavedEmail()
      })

      return {
        email,
        password,
        rememberEmail,
        loading,
        errorMessage,
        isLoading,
        handleLogin,
        clearSavedEmail,
        apiUrl,
        testConnection,
        testingConnection,
        connectionStatus
      }
    }
  }
</script>

<style scoped>
  /* Define primary color */
  .bg-primary {
    background-color: #1e40af; /* Blue-700 */
  }

  .text-primary {
    color: #1e40af; /* Blue-700 */
  }

  .border-primary {
    border-color: #1e40af; /* Blue-700 */
  }

  .focus\:ring-primary:focus {
    --tw-ring-color: #1e40af;
  }

  /* Animation keyframes */
  @keyframes rotate {
    from {
      transform: rotate(0deg);
    }
    to {
      transform: rotate(360deg);
    }
  }

  @keyframes flipX {
    0% {
      transform: scaleX(1);
    }
    50% {
      transform: scaleX(-1);
    }
    100% {
      transform: scaleX(1);
    }
  }

  @keyframes fadeIn {
    from {
      opacity: 0;
    }
    to {
      opacity: 1;
    }
  }

  @keyframes bounceIn {
    0% {
      transform: scale(0.1);
      opacity: 0;
    }
    60% {
      transform: scale(1.2);
      opacity: 1;
    }
    100% {
      transform: scale(1);
    }
  }

  @keyframes example {
    0% {
      background-position: 0% 0%;
      transform: translate(0, 0);
    }
    25% {
      background-position: 100% 0%;
      transform: translate(20px, 0);
    }
    50% {
      background-position: 100% 100%;
      transform: translate(20px, 20px);
    }
    75% {
      background-position: 0% 100%;
      transform: translate(0, 20px);
    }
    100% {
      background-position: 0% 0%;
      transform: translate(0, 0);
    }
  }

  /* Animation classes */
  .animate-rotate {
    animation: rotate 2s linear infinite;
  }

  .animate-flipX {
    animation: flipX 2s linear infinite;
  }

  .animate-fadeIn {
    animation: fadeIn 1.5s ease-in;
  }

  .animate-bounceIn {
    animation: bounceIn 1s;
  }

  .delay-1 {
    animation-delay: 0.5s;
  }

  .delay-2 {
    animation-delay: 1s;
  }

  .background-animate {
    animation: example 5s linear 2s infinite alternate;
  }
</style>
