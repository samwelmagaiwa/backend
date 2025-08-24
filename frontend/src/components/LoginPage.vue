<template>
  <div class="min-h-screen bg-gray-100">
    <div class="flex bg-white overflow-hidden w-full h-screen">
      <!-- Left side: Branding -->
      <div class="hidden md:block w-1/2 relative overflow-hidden">
        <div class="absolute inset-0 bg-cover bg-center background-animate" style="background-image: url('/assets/images/image1.jpg');"></div>
        <div class="relative z-10 flex flex-col items-up justify-up h-full p-8 text-white">
        </div>
        <!-- Enhanced S-curved design -->
        <svg class="absolute right-0 top-0 h-full w-24 text-white" viewBox="0 0 100 100" preserveAspectRatio="none">
          <path d="M0 0 C50 0 0 50 0 50 C0 50 50 100 100 100 L100 0 Z" fill="currentColor" />
        </svg>
      </div>

      <!-- Right side: Login Form -->
      <div class="w-full md:w-1/2 p-8 flex flex-col justify-center items-center">
        <h1 class="text-4xl font-serif font-bold text-primary animate-fadeIn mb-4">Muhimbili National Hospital</h1>
        
        <div class="w-40 h-40 rounded-full overflow-hidden border-2 border-primary flex items-center justify-center mb-12">
          <img src="/assets/images/logo.jpg" alt="Muhimbili Logo" class="max-w-full max-h-full animate-flipX" />
        </div>
        <div class="w-full max-w-md">
          <h2 class="text-2xl font-bold mb-6 text-center text-primary">Login</h2>
          
          <!-- Error message display -->
          <div v-if="errorMessage" class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded flex items-center">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            {{ errorMessage }}
          </div>
          
          <!-- Redirect explanation -->
          <RedirectExplanation />
          
          <form @submit.prevent="handleLogin">
            <div class="mb-4">
              <label class="block text-gray-700 font-bold text-left">Email</label>
              <input 
                v-model="credentials.email" 
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
                v-model="credentials.password" 
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
                  v-model="rememberMe" 
                  type="checkbox" 
                  class="mr-2 rounded focus:ring-2 focus:ring-primary"
                >
                <span>Remember my email</span>
              </label>
              
              <!-- Clear saved email button -->
              <button 
                v-if="credentials.email"
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
              :disabled="loading"
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
          <div v-if="credentials.email && rememberMe" class="mt-4 p-3 bg-blue-50 rounded-lg border border-blue-200">
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

            </div>
            <p class="text-xs text-green-600 mb-2">
              Using Laravel backend at <code class="bg-green-100 px-1 rounded">{{ apiUrl }}</code>
            </p>

            <p class="text-xs text-gray-600 mt-1">
              Please use your registered credentials to login.
            </p>
          </div>


        </div>
      </div>
    </div>

    <!-- Success Snackbar -->
    <div v-if="showSuccessSnackbar" class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
      <div class="flex items-center">
        <i class="fas fa-check-circle mr-2"></i>
        Login successful! Redirecting...
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import RedirectExplanation from './RedirectExplanation.vue'

export default {
  name: 'LoginPage',
  components: {
    RedirectExplanation
  },
  
  data() {
    return {
      credentials: {
        email: '',
        password: ''
      },
      rememberMe: true,
      loading: false,
      showSuccessSnackbar: false,

    }
  },
  
  computed: {
    ...mapGetters('auth', ['error']),
    
    apiUrl() {
      return process.env.VUE_APP_API_URL || 'http://localhost:8000/api'
    },

    
    errorMessage() {
      if (this.$route.query.error === 'access_denied') {
        return 'Access denied. You do not have permission to access that page.'
      }
      return this.error
    }
  },
  
  methods: {
    ...mapActions('auth', ['login', 'clearError']),
    
    async handleLogin() {
      if (!this.credentials.email || !this.credentials.password) {
        return
      }
      
      this.clearError()
      this.loading = true
      
      try {
        console.log('🚀 Attempting login with:', {
          email: this.credentials.email,
          rememberMe: this.rememberMe
        })
        
        const result = await this.login({
          email: this.credentials.email,
          password: this.credentials.password
        })
        
        if (result.success) {
          this.showSuccessSnackbar = true
          console.log('✅ Login successful! Navigation should be handled by auth system.')
          
          const userRole = result.user.role
          console.log('🔍 Login successful, user role:', userRole)
          
          // Check if there's a redirect parameter in the URL
          const redirectParam = this.$route.query.redirect
          console.log('🔍 Redirect parameter:', redirectParam)
          
          let redirectPath = '/user-dashboard' // default fallback
          
          // First priority: Check if user needs onboarding
          if (result.user.needs_onboarding && userRole !== 'admin') {
            redirectPath = '/onboarding'
            console.log('🔄 User needs onboarding, redirecting to:', redirectPath)
          }
          // Second priority: Honor the redirect parameter if user has access
          else if (redirectParam) {
            // Validate that the user has access to the redirect path
            const targetRoute = this.$router.resolve(redirectParam)
            
            if (targetRoute && targetRoute.meta && targetRoute.meta.roles) {
              if (targetRoute.meta.roles.includes(userRole)) {
                redirectPath = redirectParam
                console.log('✅ User has access to redirect path:', redirectPath)
              } else {
                console.warn('⚠️ User does not have access to redirect path:', redirectParam)
                // Fall through to role-based default
                redirectPath = this.getDefaultDashboardForRole(userRole)
              }
            } else {
              // No role restrictions, allow the redirect
              redirectPath = redirectParam
              console.log('🔄 No role restrictions on redirect path, allowing:', redirectPath)
            }
          }
          // Third priority: Role-based default dashboard
          else {
            redirectPath = this.getDefaultDashboardForRole(userRole)
          }
          
          console.log('🔄 Final redirect path:', redirectPath)
          
          // Clear password but keep email if remember is enabled
          this.credentials.password = ''
          
          // Redirect after a short delay
          setTimeout(() => {
            this.$router.push(redirectPath)
          }, 1500)
          
        } else {
          console.error('❌ Login failed:', result.error)
          // Clear password on failed login for security
          this.credentials.password = ''
        }
      } catch (error) {
        console.error('Login error:', error)
        this.credentials.password = ''
      } finally {
        this.loading = false
      }
    },

    
    getDefaultDashboardForRole(userRole) {
      switch (userRole) {
        case 'admin':
          return '/admin-dashboard'
        case 'divisional_director':
          return '/divisional-dashboard'
        case 'head_of_department':
          return '/hod-dashboard/request-list'
        case 'hod_it':
          return '/hod-it-dashboard'
        case 'ict_director':
          return '/dict-dashboard'
        case 'ict_officer':
          return '/ict-dashboard'
        case 'staff':
          return '/user-dashboard'
        default:
          console.warn('Unknown role, defaulting to user dashboard:', userRole)
          return '/user-dashboard'
      }
    },
    
    clearSavedEmail() {
      this.credentials.email = ''
      this.rememberMe = false
    }
  },
  
  mounted() {
    this.clearError()
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
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

@keyframes flipX {
  0% { transform: scaleX(1); }
  50% { transform: scaleX(-1); }
  100% { transform: scaleX(1); }
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
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