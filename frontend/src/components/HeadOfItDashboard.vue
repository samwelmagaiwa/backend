<template>
  <div class="flex flex-col h-screen">
    <Header />
    <div class="flex flex-1 overflow-hidden">
      <ModernSidebar />
      <main
        class="flex-1 p-6 bg-gradient-to-br from-blue-900 via-blue-800 to-teal-900 overflow-y-auto relative"
      >
        <!-- Medical Background Pattern -->
        <div class="absolute inset-0 overflow-hidden" style="z-index: 1; pointer-events: none">
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
          <!-- Floating ICT icons -->
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
                  [
                    'fa-user-cog',
                    'fa-shield-alt',
                    'fa-network-wired',
                    'fa-server',
                    'fa-laptop-code'
                  ][Math.floor(Math.random() * 5)]
                ]"
              ></i>
            </div>
          </div>
        </div>

        <div class="max-w-full mx-auto relative" style="z-index: 10">
          <!-- Header Section -->
          <div class="medical-glass-card rounded-3xl p-4 mb-0">
            <div class="text-center">
              <h2
                class="text-xl font-bold text-blue-100 tracking-wide drop-shadow-md animate-fade-in-delay"
              >
                Welcome, {{ userName }}
              </h2>
            </div>
          </div>

          <!-- Main Content -->
          <div class="medical-glass-card rounded-3xl overflow-hidden mt-4">
            <div class="p-6">
              <!-- Quick Actions Section -->
              <div class="mb-8">
                <h3 class="text-xl font-bold text-white mb-4 flex items-center">
                  <i class="fas fa-bolt mr-2 text-blue-400"></i>
                  Quick Actions
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                  <div
                    @click="navigateToRequests"
                    class="medical-card bg-gradient-to-r from-purple-600/25 to-pink-600/25 border-2 border-purple-400/40 p-4 rounded-xl backdrop-blur-sm hover:shadow-2xl hover:shadow-purple-500/20 transition-all duration-300 group text-center cursor-pointer"
                    style="z-index: 20; position: relative"
                  >
                    <div class="flex flex-col items-center">
                      <div
                        class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-600 rounded-lg flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-purple-300/50 mb-2"
                      >
                        <i class="fas fa-layer-group text-white"></i>
                      </div>
                      <span class="text-white font-medium text-sm">Access Requests</span>
                    </div>
                  </div>

                  <div
                    class="medical-card bg-gradient-to-r from-blue-600/25 to-cyan-600/25 border-2 border-blue-400/40 p-4 rounded-xl backdrop-blur-sm hover:shadow-2xl hover:shadow-blue-500/20 transition-all duration-300 group text-center opacity-50 cursor-not-allowed"
                  >
                    <div class="flex flex-col items-center">
                      <div
                        class="w-10 h-10 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-lg flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-blue-300/50 mb-2"
                      >
                        <i class="fas fa-chart-bar text-white"></i>
                      </div>
                      <span class="text-white font-medium text-sm">Analytics (Coming Soon)</span>
                    </div>
                  </div>
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
  import { onMounted } from 'vue'
  import Header from '@/components/header.vue'
  import ModernSidebar from './ModernSidebar.vue'
  import AppFooter from '@/components/footer.vue'
  import { useAuth } from '@/composables/useAuth'

  export default {
    name: 'HeadOfItDashboard',
    components: {
      Header,
      ModernSidebar,
      AppFooter
    },
    setup() {
      const { userName, ROLES, requireRole } = useAuth()

      // Guard this route - only Head of IT can access
      onMounted(() => {
        console.log('HeadOfItDashboard: Component mounted, user:', userName.value)
        requireRole([ROLES.HEAD_OF_IT])
      })

      return {
        userName
      }
    },
    methods: {
      navigateToRequests() {
        console.log('Navigating to requests page...')
        this.$router.push('/head_of_it-dashboard/combined-requests')
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
</style>
