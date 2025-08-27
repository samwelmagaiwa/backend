<template>
  <div class="flex flex-col h-screen">
    <AppHeader />
    <div class="flex flex-1 overflow-hidden">
      <ModernSidebar v-model:collapsed="sidebarCollapsed" />
      <main class="flex-1 p-6 overflow-y-auto relative user-dashboard-main">
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
                fontSize: Math.random() * 20 + 10 + 'px',
              }"
            >
              <i
                :class="[
                  'fas',
                  [
                    'fa-heartbeat',
                    'fa-user-md',
                    'fa-hospital',
                    'fa-stethoscope',
                    'fa-plus',
                  ][Math.floor(Math.random() * 5)],
                ]"
              ></i>
            </div>
          </div>
        </div>

        <div class="max-w-7xl mx-auto relative z-10">
          <h1 class="text-4xl font-bold text-white mb-8 drop-shadow-lg">User Dashboard</h1>

          <!-- Enhanced Multi-Layer Welcome Section -->
          <div class="medical-card bg-gradient-to-r from-blue-600/30 to-blue-600/30 border-2 border-blue-400/50 p-8 rounded-3xl backdrop-blur-sm hover:shadow-2xl hover:shadow-blue-500/30 transition-all duration-500 group relative overflow-hidden mb-8">
            <!-- Animated Background Layers -->
            <div class="absolute inset-0 bg-gradient-to-r from-blue-500/15 via-blue-500/10 to-blue-500/15 opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>
            <div class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-br from-blue-400/25 to-transparent rounded-full blur-2xl group-hover:scale-150 transition-transform duration-1000"></div>
            <div class="absolute bottom-0 left-0 w-32 h-32 bg-gradient-to-tr from-blue-400/20 to-transparent rounded-full blur-xl group-hover:scale-125 transition-transform duration-800"></div>

            <!-- Shine Effect -->
            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -skew-x-12 -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>

            <div class="relative z-10">
              <!-- Enhanced Welcome Title -->
              <div class="flex items-center mb-6">
                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-blue-300/50 relative overflow-hidden mr-4">
                  <div class="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent"></div>
                  <i class="fas fa-home text-white text-2xl relative z-10 drop-shadow-lg"></i>
                  <div class="absolute top-1 right-1 w-2 h-2 bg-white/60 rounded-full animate-ping"></div>
                </div>
                <div>
                  <h2 class="text-3xl font-bold text-white mb-2 drop-shadow-lg">Welcome to ICT Access Portal</h2>
                  <p class="text-blue-100 text-lg drop-shadow-sm opacity-90">Streamline your system access and equipment requests</p>
                </div>
              </div>

              <!-- Enhanced Quick Actions -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- View My Requests Button - Blue -->
                <button
                  @click="viewMyRequests"
                  class="medical-button bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-8 py-6 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl hover:shadow-blue-500/30 transform hover:scale-105 active:scale-95 relative overflow-hidden group/btn"
                >
                  <!-- Multi-layer button effects -->
                  <div class="absolute inset-0 bg-gradient-to-r from-blue-500/20 to-blue-600/20 opacity-0 group-hover/btn:opacity-100 transition-opacity duration-300"></div>
                  <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-blue-300/60 to-transparent group-hover/btn:animate-pulse"></div>
                  <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -skew-x-12 -translate-x-full group-hover/btn:translate-x-full transition-transform duration-700"></div>

                  <div class="relative z-10 flex items-center justify-center">
                    <div class="w-8 h-8 bg-blue-400/40 rounded-lg flex items-center justify-center mr-3 group-hover/btn:scale-110 transition-transform duration-300">
                      <i class="fas fa-list-alt text-white text-lg drop-shadow-sm"></i>
                    </div>
                    <span class="text-lg drop-shadow-sm">Track My Applications</span>
                  </div>
                </button>

                <!-- Submit New Request Button - Red -->
                <button
                  @click="showFormSelector = true"
                  class="medical-button bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white px-8 py-6 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl hover:shadow-red-500/30 transform hover:scale-105 active:scale-95 relative overflow-hidden group/btn"
                >
                  <!-- Multi-layer button effects -->
                  <div class="absolute inset-0 bg-gradient-to-r from-red-500/20 to-red-600/20 opacity-0 group-hover/btn:opacity-100 transition-opacity duration-300"></div>
                  <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-red-300/60 to-transparent group-hover/btn:animate-pulse"></div>
                  <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -skew-x-12 -translate-x-full group-hover/btn:translate-x-full transition-transform duration-700"></div>

                  <div class="relative z-10 flex items-center justify-center">
                    <div class="w-8 h-8 bg-red-400/40 rounded-lg flex items-center justify-center mr-3 group-hover/btn:scale-110 transition-transform duration-300">
                      <i class="fas fa-plus text-white text-lg drop-shadow-sm"></i>
                    </div>
                    <span class="text-lg drop-shadow-sm">Create New Application</span>
                  </div>
                </button>
              </div>
            </div>
          </div>

          <!-- Enhanced Multi-Layer Statistics -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Pending Card -->
            <div class="medical-card bg-gradient-to-br from-blue-600/25 to-blue-700/25 border-2 border-blue-400/50 p-8 rounded-2xl backdrop-blur-sm hover:shadow-2xl hover:shadow-blue-500/30 transition-all duration-500 group relative overflow-hidden">
              <!-- Multi-layer background effects -->
              <div class="absolute inset-0 bg-gradient-to-br from-blue-500/15 via-transparent to-blue-600/15 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
              <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-blue-400/30 to-transparent rounded-full blur-xl group-hover:scale-150 transition-transform duration-700"></div>
              <div class="absolute bottom-0 left-0 w-20 h-20 bg-gradient-to-tr from-blue-400/20 to-transparent rounded-full blur-lg group-hover:scale-125 transition-transform duration-600"></div>

              <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                  <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-blue-300/50">
                    <i class="fas fa-clock text-white text-lg drop-shadow-lg"></i>
                  </div>
                  <div class="flex items-center space-x-1 bg-blue-500/30 px-3 py-1 rounded-full border border-blue-400/40">
                    <div class="w-2 h-2 bg-blue-400 rounded-full animate-pulse"></div>
                    <span class="text-xs text-blue-300 font-medium">Processing</span>
                  </div>
                </div>
                <h3 class="text-2xl font-bold text-white mb-3 drop-shadow-lg">Under Review</h3>
                <p class="text-5xl font-bold text-blue-400 drop-shadow-lg relative">
                  12
                  <div class="absolute -inset-2 bg-blue-400/20 blur-lg opacity-50 group-hover:opacity-75 transition-opacity duration-300"></div>
                </p>
                <div class="mt-4 w-full bg-blue-900/30 rounded-full h-2 overflow-hidden">
                  <div class="h-full bg-gradient-to-r from-blue-400 to-blue-500 rounded-full" style="width: 65%"></div>
                </div>
              </div>
            </div>

            <!-- Approved Card -->
            <div class="medical-card bg-gradient-to-br from-blue-600/25 to-blue-700/25 border-2 border-blue-400/50 p-8 rounded-2xl backdrop-blur-sm hover:shadow-2xl hover:shadow-blue-500/30 transition-all duration-500 group relative overflow-hidden">
              <!-- Multi-layer background effects -->
              <div class="absolute inset-0 bg-gradient-to-br from-blue-500/15 via-transparent to-blue-600/15 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
              <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-blue-400/30 to-transparent rounded-full blur-xl group-hover:scale-150 transition-transform duration-700"></div>
              <div class="absolute bottom-0 left-0 w-20 h-20 bg-gradient-to-tr from-blue-400/20 to-transparent rounded-full blur-lg group-hover:scale-125 transition-transform duration-600"></div>

              <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                  <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-blue-300/50">
                    <i class="fas fa-check-circle text-white text-lg drop-shadow-lg"></i>
                  </div>
                  <div class="flex items-center space-x-1 bg-blue-500/30 px-3 py-1 rounded-full border border-blue-400/40">
                    <div class="w-2 h-2 bg-blue-400 rounded-full animate-pulse"></div>
                    <span class="text-xs text-blue-300 font-medium">Completed</span>
                  </div>
                </div>
                <h3 class="text-2xl font-bold text-white mb-3 drop-shadow-lg">Granted Access</h3>
                <p class="text-5xl font-bold text-blue-400 drop-shadow-lg relative">
                  47
                  <div class="absolute -inset-2 bg-blue-400/20 blur-lg opacity-50 group-hover:opacity-75 transition-opacity duration-300"></div>
                </p>
                <div class="mt-4 w-full bg-blue-900/30 rounded-full h-2 overflow-hidden">
                  <div class="h-full bg-gradient-to-r from-blue-400 to-blue-500 rounded-full" style="width: 89%"></div>
                </div>
              </div>
            </div>

            <!-- Rejected Card -->
            <div class="medical-card bg-gradient-to-br from-blue-600/25 to-blue-700/25 border-2 border-blue-400/50 p-8 rounded-2xl backdrop-blur-sm hover:shadow-2xl hover:shadow-blue-500/30 transition-all duration-500 group relative overflow-hidden">
              <!-- Multi-layer background effects -->
              <div class="absolute inset-0 bg-gradient-to-br from-blue-500/15 via-transparent to-blue-600/15 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
              <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-blue-400/30 to-transparent rounded-full blur-xl group-hover:scale-150 transition-transform duration-700"></div>
              <div class="absolute bottom-0 left-0 w-20 h-20 bg-gradient-to-tr from-blue-400/20 to-transparent rounded-full blur-lg group-hover:scale-125 transition-transform duration-600"></div>

              <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                  <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-blue-300/50">
                    <i class="fas fa-edit text-white text-lg drop-shadow-lg"></i>
                  </div>
                  <div class="flex items-center space-x-1 bg-blue-500/30 px-3 py-1 rounded-full border border-blue-400/40">
                    <div class="w-2 h-2 bg-blue-400 rounded-full animate-pulse"></div>
                    <span class="text-xs text-blue-300 font-medium">Reviewed</span>
                  </div>
                </div>
                <h3 class="text-2xl font-bold text-white mb-3 drop-shadow-lg">Needs Revision</h3>
                <p class="text-5xl font-bold text-blue-400 drop-shadow-lg relative">
                  3
                  <div class="absolute -inset-2 bg-blue-400/20 blur-lg opacity-50 group-hover:opacity-75 transition-opacity duration-300"></div>
                </p>
                <div class="mt-4 w-full bg-blue-900/30 rounded-full h-2 overflow-hidden">
                  <div class="h-full bg-gradient-to-r from-blue-400 to-blue-500 rounded-full" style="width: 25%"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>

    <!-- Form Selector Modal -->
    <div
      v-if="showFormSelector"
      class="fixed inset-0 bg-black/60 flex items-center justify-center z-50"
    >
      <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4">
        <div class="bg-blue-600 p-6 text-center rounded-t-xl">
          <h3 class="text-xl font-bold text-white">Select Access Form</h3>
        </div>
        <div class="p-6 space-y-4">
          <button
            @click="selectCombinedForm"
            class="w-full bg-blue-600 hover:bg-blue-700 text-white p-4 rounded-lg font-medium transition-colors"
          >
            <i class="fas fa-layer-group mr-2"></i>
            Combined Access Form
          </button>
          <button
            @click="selectBookingService"
            class="w-full bg-red-600 hover:bg-red-700 text-white p-4 rounded-lg font-medium transition-colors"
          >
            <i class="fas fa-calendar-check mr-2"></i>
            Booking Service
          </button>
          <button
            @click="showFormSelector = false"
            class="w-full bg-gray-300 hover:bg-gray-400 text-gray-800 p-3 rounded-lg font-medium transition-colors"
          >
            Cancel
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import ModernSidebar from './ModernSidebar.vue'
import AppHeader from './AppHeader.vue'
import { useAuth } from '@/composables/useAuth'

export default {
  name: 'UserDashboard',
  components: {
    ModernSidebar,
    AppHeader
  },
  setup() {
    const router = useRouter()
    const { requireRole, ROLES } = useAuth()

    // Local state
    const sidebarCollapsed = ref(false)
    const showFormSelector = ref(false)

    // Guard this route - only staff can access
    onMounted(() => {
      console.log('UserDashboard mounted successfully')
      requireRole([ROLES.STAFF])
    })

    // Methods
    const viewMyRequests = () => {
      router.push('/request-status')
    }

    const selectCombinedForm = () => {
      showFormSelector.value = false
      router.push('/user-combined-form')
    }

    const selectBookingService = () => {
      showFormSelector.value = false
      router.push('/booking-service')
    }

    return {
      sidebarCollapsed,
      showFormSelector,
      viewMyRequests,
      selectCombinedForm,
      selectBookingService
    }
  }
}
</script>

<style scoped>
/* User Dashboard Main Background */
.user-dashboard-main {
  background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 50%, #1e40af 100%) !important;
  min-height: 100vh;
}

/* Medical Glass morphism effects */
.medical-card {
  position: relative;
  overflow: hidden;
  background: rgba(59, 130, 246, 0.1);
  backdrop-filter: blur(15px);
  -webkit-backdrop-filter: blur(15px);
}

.medical-card::before {
  content: "";
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(
    90deg,
    transparent,
    rgba(96, 165, 250, 0.2),
    transparent
  );
  transition: left 0.5s;
}

.medical-card:hover::before {
  left: 100%;
}

.medical-button {
  position: relative;
  z-index: 1;
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

.animate-float {
  animation: float 6s ease-in-out infinite;
}

/* Enhanced transitions */
.transition-colors {
  transition: all 0.3s ease;
}

/* Multi-layer hover transforms */
.group:hover {
  transform: translateY(-2px) scale(1.02);
}

.group\/btn:hover {
  transform: translateY(-1px) scale(1.05);
}

.group\/btn:active {
  transform: translateY(0px) scale(0.95);
}

/* Glow effects */
.drop-shadow-lg {
  filter: drop-shadow(0 10px 8px rgba(0, 0, 0, 0.04)) drop-shadow(0 4px 3px rgba(0, 0, 0, 0.1));
}

/* Progress bar animations */
@keyframes progress-shine {
  0% {
    transform: translateX(-100%);
  }
  100% {
    transform: translateX(100%);
  }
}

.animate-progress {
  animation: progress-shine 2s ease-in-out infinite;
}
</style>
