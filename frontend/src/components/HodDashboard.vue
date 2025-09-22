<template>
  <div class="flex flex-col h-screen">
    <Header />
    <div class="flex flex-1 overflow-hidden">
      <ModernSidebar />
      <main
        class="flex-1 p-6 bg-gradient-to-br from-blue-900 via-blue-800 to-teal-900 overflow-y-auto relative"
      >
        <!-- Medical Background Pattern -->
        <div class="absolute inset-0 overflow-hidden">
          <!-- Medical Cross Pattern -->
          <div class="absolute inset-0 opacity-5">
            <div class="grid grid-cols-12 gap-8 h-full transform rotate-45">
              <div
                v-for="i in 48"
                :key="i"
                class="bg-white rounded-full w-2 h-2 animate-pulse"
                :style="{ animationDelay: i * 0.1 + 's' }"
              ></div>
            </div>
          </div>
          <!-- Floating medical icons -->
          <div class="absolute inset-0">
            <div
              v-for="i in 15"
              :key="i"
              class="absolute text-white opacity-10 animate-float"
              :style="{
                left: Math.random() * 100 + '%',
                top: Math.random() * 100 + '%',
                animationDelay: Math.random() * 3 + 's',
                animationDuration: Math.random() * 3 + 2 + 's',
                fontSize: Math.random() * 20 + 10 + 'px'
              }"
            >
              <i
                :class="[
                  'fas',
                  ['fa-heartbeat', 'fa-user-md', 'fa-hospital', 'fa-stethoscope', 'fa-plus'][
                    Math.floor(Math.random() * 5)
                  ]
                ]"
              ></i>
            </div>
          </div>
        </div>

        <div class="max-w-full mx-auto relative z-10">
          <!-- Header Section -->
          <div class="medical-glass-card rounded-t-3xl p-4 mb-0 border-b border-blue-300/30">
            <div class="text-center">
              <h2
                class="text-xl font-bold text-blue-100 tracking-wide drop-shadow-md animate-fade-in-delay"
              >
                Welcome, {{ userName }}
              </h2>
              <p class="text-sm text-teal-300 mt-2">Head of Department Dashboard</p>
            </div>
          </div>

          <!-- Main Content -->
          <div class="medical-glass-card rounded-b-3xl overflow-hidden">
            <div class="p-6">
              <!-- Quick Actions Section -->
              <div>
                <h3 class="text-xl font-bold text-white mb-4 flex items-center">
                  <i class="fas fa-bolt mr-2 text-blue-400"></i>
                  Quick Actions
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <!-- Use button element for better click handling -->
                  <button
                    type="button"
                    @click="navigateToDivisionalRecommendations"
                    class="medical-card bg-gradient-to-r from-blue-600/25 to-cyan-600/25 border-2 border-blue-400/40 p-4 rounded-xl backdrop-blur-sm hover:shadow-2xl hover:shadow-blue-500/20 transition-all duration-300 group text-center cursor-pointer w-full"
                    style="
                      background: none;
                      border: 2px solid rgba(96, 165, 250, 0.4);
                      outline: none;
                    "
                  >
                    <div class="flex flex-col items-center">
                      <div
                        class="w-10 h-10 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-lg flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-blue-300/50 mb-2"
                      >
                        <i class="fas fa-comments text-white"></i>
                      </div>
                      <span class="text-white font-medium text-lg">Divisional Recommendations</span>
                    </div>
                  </button>

                  <router-link
                    to="/hod-dashboard/combined-requests"
                    class="medical-card bg-gradient-to-r from-purple-600/25 to-pink-600/25 border-2 border-purple-400/40 p-4 rounded-xl backdrop-blur-sm hover:shadow-2xl hover:shadow-purple-500/20 transition-all duration-300 group text-center"
                  >
                    <div class="flex flex-col items-center">
                      <div
                        class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-600 rounded-lg flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-purple-300/50 mb-2"
                      >
                        <i class="fas fa-layer-group text-white"></i>
                      </div>
                      <span class="text-white font-medium text-lg">Combined Requests</span>
                    </div>
                  </router-link>
                </div>
              </div>

              <!-- Footer -->
              <AppFooter />
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script>
  import { ref, onMounted } from 'vue'
  import { useRouter } from 'vue-router'
  import Header from '@/components/header.vue'
  import ModernSidebar from './ModernSidebar.vue'
  import AppFooter from '@/components/footer.vue'
  import { useAuth } from '@/composables/useAuth'

  export default {
    name: 'HodDashboard',
    components: {
      Header,
      ModernSidebar,
      AppFooter
    },
    setup() {
      const router = useRouter()
      const { userName, ROLES, requireRole } = useAuth()

      // Local state
      // Sidebar state now managed by Pinia - no local state needed

      const recentRequests = ref([])
      const quickActions = ref([
        { name: 'View Requests', icon: 'fas fa-list', route: '/hod-dashboard/request-list' }
      ])

      const performAction = (action) => {
        console.log('Performing action:', action)
        // Add your action implementation here
      }

      const navigateToDivisionalRecommendations = async (event) => {
        console.log('🔍 Divisional Recommendations button clicked!')

        // Prevent default behavior
        if (event) {
          event.preventDefault()
          event.stopPropagation()
        }

        const targetPath = '/hod-dashboard/divisional-recommendations'

        try {
          console.log('🚀 Attempting Vue Router navigation first...')

          // Try Vue Router first (most elegant solution)
          if (router && typeof router.push === 'function') {
            await router.push(targetPath)
            console.log('✅ Vue Router navigation successful!')
            return
          }

          console.log('⚠️ Vue Router not available, trying window.location...')
        } catch (routerError) {
          console.warn('⚠️ Vue Router failed:', routerError.message)
        }

        try {
          // Fallback: Use window.location.href
          const targetUrl = window.location.origin + targetPath
          console.log('Target URL:', targetUrl)
          window.location.href = targetUrl
        } catch (locationError) {
          console.error('❌ All navigation methods failed:', locationError)
          // Last resort: direct pathname change
          window.location.pathname = targetPath
        }
      }

      // Guard this route - only Head of Department can access
      onMounted(() => {
        requireRole([ROLES.HEAD_OF_DEPARTMENT])
      })

      return {
        userName,
        recentRequests,
        quickActions,
        performAction,
        navigateToDivisionalRecommendations
      }
    }
  }
</script>

<style scoped>
  .text-primary {
    color: #1e40af;
  }

  /* Medical Glass morphism effects */
  .medical-glass-card {
    background: rgba(59, 130, 246, 0.15);
    backdrop-filter: blur(25px);
    -webkit-backdrop-filter: blur(25px);
    border: 2px solid rgba(96, 165, 250, 0.3);
    box-shadow:
      0 8px 32px rgba(29, 78, 216, 0.4),
      inset 0 1px 0 rgba(255, 255, 255, 0.1);
  }

  .medical-card {
    position: relative;
    overflow: hidden;
    background: rgba(59, 130, 246, 0.1);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    cursor: pointer;
    pointer-events: auto;
    z-index: 10;
  }

  .medical-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(96, 165, 250, 0.2), transparent);
    transition: left 0.5s;
    pointer-events: none;
    z-index: -1;
  }

  .medical-card:hover::before {
    left: 100%;
  }

  /* Animations */
  @keyframes float {
    0%,
    100% {
      transform: translateY(0px);
    }
    50% {
      transform: translateY(-20px);
    }
  }

  @keyframes fade-in {
    from {
      opacity: 0;
      transform: translateY(20px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  @keyframes fade-in-delay {
    from {
      opacity: 0;
      transform: translateY(20px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .animate-float {
    animation: float 6s ease-in-out infinite;
  }

  .animate-fade-in {
    animation: fade-in 1s ease-out;
  }

  .animate-fade-in-delay {
    animation: fade-in-delay 1s ease-out 0.3s both;
  }

  /* Hover effects for cards */
  .hover-card {
    transition: all 0.3s ease;
  }

  .hover-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
  }

  /* Button hover effects */
  button:hover {
    transform: translateY(-1px);
  }

  /* Router link specific styles */
  .medical-card.router-link {
    display: block;
    text-decoration: none;
    color: inherit;
    outline: none;
    position: relative;
    z-index: 10;
  }

  .medical-card:focus {
    outline: 2px solid rgba(96, 165, 250, 0.8);
    outline-offset: 2px;
  }

  .medical-card:active {
    transform: scale(0.98);
    transition: transform 0.1s;
  }

  /* Debug styles - ensure clickability */
  .medical-card {
    user-select: none;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
  }

  .medical-card:hover {
    cursor: pointer !important;
    border-color: rgba(96, 165, 250, 0.8) !important;
    background: rgba(59, 130, 246, 0.3) !important;
    transform: translateY(-2px) !important;
    box-shadow: 0 8px 32px rgba(59, 130, 246, 0.6) !important;
  }

  /* Ensure all child elements don't block pointer events */
  .medical-card * {
    pointer-events: none;
  }

  /* But allow the card itself to receive events */
  .medical-card {
    pointer-events: auto !important;
  }
</style>
