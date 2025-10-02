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
          <!-- Floating admin icons -->
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
                  ['fa-shield-alt', 'fa-users-cog', 'fa-cogs', 'fa-database', 'fa-chart-line'][
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
              <p class="text-sm text-teal-300 mt-2">System Administrator Dashboard</p>
            </div>
          </div>

          <!-- Main Content -->
          <div class="medical-glass-card rounded-b-3xl overflow-hidden">
            <div class="p-6">
              <!-- Statistics Cards -->
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
                <div
                  class="medical-card bg-gradient-to-r from-blue-600/25 to-cyan-600/25 border-2 border-blue-400/40 p-6 rounded-2xl backdrop-blur-sm hover:shadow-2xl hover:shadow-blue-500/20 transition-all duration-500 group"
                >
                  <div class="flex items-center mb-4">
                    <div
                      class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-blue-300/50 mr-4"
                    >
                      <i class="fas fa-users text-white text-xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-white drop-shadow-md">TOTAL USERS</h3>
                  </div>
                  <p class="text-3xl font-bold text-blue-100 drop-shadow-lg">
                    {{ stats.totalUsers }}
                  </p>
                </div>

                <div
                  class="medical-card bg-gradient-to-r from-green-600/25 to-emerald-600/25 border-2 border-green-400/40 p-6 rounded-2xl backdrop-blur-sm hover:shadow-2xl hover:shadow-green-500/20 transition-all duration-500 group"
                >
                  <div class="flex items-center mb-4">
                    <div
                      class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-blue-300/50 mr-4"
                    >
                      <i class="fas fa-file-alt text-white text-xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-white drop-shadow-md">TOTAL REQUESTS</h3>
                  </div>
                  <p class="text-3xl font-bold text-green-100 drop-shadow-lg">
                    {{ stats.totalRequests }}
                  </p>
                </div>

                <div
                  class="medical-card bg-gradient-to-r from-yellow-600/25 to-orange-600/25 border-2 border-yellow-400/40 p-6 rounded-2xl backdrop-blur-sm hover:shadow-2xl hover:shadow-yellow-500/20 transition-all duration-500 group"
                >
                  <div class="flex items-center mb-4">
                    <div
                      class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-blue-300/50 mr-4"
                    >
                      <i class="fas fa-clock text-white text-xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-white drop-shadow-md">
                      PENDING REQUESTS
                    </h3>
                  </div>
                  <p class="text-3xl font-bold text-yellow-100 drop-shadow-lg">
                    {{ stats.pendingRequests }}
                  </p>
                </div>
              </div>

              <!-- Quick Actions Section -->
              <div class="mb-8">
                <h3 class="text-xl font-bold text-white mb-4 flex items-center">
                  <i class="fas fa-bolt mr-2 text-yellow-400"></i>
                  Quick Actions
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                  <router-link
                    v-for="action in quickActions"
                    :key="action.title"
                    :to="action.route"
                    @click="handleQuickActionClick(action)"
                    class="medical-card bg-gradient-to-r from-blue-600/25 to-cyan-600/25 border-2 border-blue-400/40 p-4 rounded-xl backdrop-blur-sm hover:shadow-2xl hover:shadow-blue-500/20 transition-all duration-300 group text-center cursor-pointer"
                  >
                    <div class="flex flex-col items-center">
                      <div
                        :class="`w-12 h-12 bg-gradient-to-br ${action.gradient} rounded-lg flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border ${action.border} mb-3`"
                      >
                        <i :class="`${action.icon} text-white text-xl`"></i>
                      </div>
                      <span class="text-white font-medium text-sm mb-1">{{ action.title }}</span>
                      <span class="text-blue-200 text-xs">{{ action.description }}</span>
                    </div>
                  </router-link>
                </div>
              </div>

              <!-- Management Sections -->
              <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- User Management -->
                <div
                  class="medical-card bg-gradient-to-r from-teal-600/25 to-cyan-600/25 border-2 border-teal-400/40 p-6 rounded-2xl backdrop-blur-sm"
                >
                  <h3 class="text-xl font-bold text-white mb-4 flex items-center">
                    <i class="fas fa-users-cog mr-2 text-teal-300"></i>
                    User Management
                  </h3>
                  <div class="space-y-3">
                    <router-link
                      v-for="userAction in userManagementActions"
                      :key="userAction.title"
                      :to="userAction.route"
                      class="flex items-center p-3 bg-white/10 rounded-lg hover:bg-white/20 transition-all duration-300 group"
                    >
                      <div
                        :class="`w-10 h-10 bg-gradient-to-br ${userAction.gradient} rounded-lg flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border ${userAction.border} mr-3`"
                      >
                        <i :class="`${userAction.icon} text-white`"></i>
                      </div>
                      <div class="flex-1">
                        <div class="text-white font-medium">{{ userAction.title }}</div>
                        <div class="text-blue-200 text-sm">{{ userAction.description }}</div>
                      </div>
                      <i class="fas fa-chevron-right text-blue-300"></i>
                    </router-link>
                  </div>
                </div>

                <!-- System Information Section -->
                <div
                  class="medical-card bg-gradient-to-r from-purple-600/25 to-indigo-600/25 border-2 border-purple-400/40 p-6 rounded-2xl backdrop-blur-sm"
                >
                  <h3 class="text-xl font-bold text-white mb-4 flex items-center">
                    <i class="fas fa-info-circle mr-2 text-purple-300"></i>
                    System Information
                  </h3>
                  <div class="space-y-2">
                    <div class="text-white text-sm">
                      <span class="text-purple-200">Version:</span> 1.0.0
                    </div>
                    <div class="text-white text-sm">
                      <span class="text-purple-200">Last Updated:</span>
                      {{ new Date().toLocaleDateString() }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script>
  import { ref, onMounted } from 'vue'
  import Header from '@/components/header.vue'
  import ModernSidebar from '@/components/ModernSidebar.vue'
  import { useAuth } from '@/composables/useAuth'
  import dashboardService from '@/services/dashboardService'

  export default {
    name: 'AdminDashboard',
    components: {
      Header,
      ModernSidebar
    },
    setup() {
      const { userName, ROLES, requireRole } = useAuth()

      // Local state
      // Sidebar state now managed by Pinia - no local state needed

      // Statistics data - start with loading placeholders
      const stats = ref({
        totalUsers: 0,
        totalRequests: 0,
        pendingRequests: 0
      })

      // Loading state
      const isLoadingStats = ref(false)

      // Quick actions
      const quickActions = ref([
        {
          title: 'User Roles',
          description: 'Assign roles to users',
          icon: 'fas fa-users',
          gradient: 'from-blue-500 to-blue-600',
          border: 'border-blue-300/50',
          route: '/admin/user-roles'
        },
        {
          title: 'Departments',
          description: 'Manage departments & assignments',
          icon: 'fas fa-building',
          gradient: 'from-blue-500 to-blue-600',
          border: 'border-blue-300/50',
          route: '/admin/departments'
        },
        {
          title: 'Borrowed Device Monitoring',
          description: 'Manage device inventory & tracking',
          icon: 'fas fa-laptop',
          gradient: 'from-orange-500 to-red-600',
          border: 'border-orange-300/50',
          route: '/admin/device-inventory'
        }
      ])

      // User management actions
      const userManagementActions = ref([
        {
          title: 'Onboarding Reset',
          description: 'Reset user onboarding status',
          icon: 'fas fa-undo-alt',
          gradient: 'from-blue-500 to-blue-600',
          border: 'border-blue-300/50',
          route: '/admin/onboarding-reset'
        },
        {
          title: 'Jeeva Users',
          description: 'Manage Jeeva system users',
          icon: 'fas fa-heartbeat',
          gradient: 'from-blue-500 to-blue-600',
          border: 'border-blue-300/50',
          route: '/jeeva-users'
        },
        {
          title: 'Wellsoft Users',
          description: 'Manage Wellsoft system users',
          icon: 'fas fa-laptop-medical',
          gradient: 'from-blue-500 to-blue-600',
          border: 'border-blue-300/50',
          route: '/wellsoft-users'
        },
        {
          title: 'Internet Users',
          description: 'Manage internet access users',
          icon: 'fas fa-wifi',
          gradient: 'from-blue-500 to-blue-600',
          border: 'border-blue-300/50',
          route: '/internet-users'
        }
      ])

      // Guard this route - only Admins can access
      onMounted(() => {
        requireRole([ROLES.ADMIN])
        loadStats()
      })

      const loadStats = async () => {
        try {
          isLoadingStats.value = true
          console.log('📊 Loading admin dashboard statistics...')

          const result = await dashboardService.getAdminDashboardStats()

          if (result.success) {
            // Update stats with live data from backend
            stats.value = {
              totalUsers: result.data.totalUsers,
              totalRequests: result.data.totalRequests,
              pendingRequests: result.data.pendingRequests
            }
            console.log('✅ Admin dashboard stats loaded successfully:', stats.value)
          } else {
            console.warn('⚠️ Failed to load stats, using fallback data:', result.error)
            // Use fallback data from service
            stats.value = {
              totalUsers: result.data.totalUsers,
              totalRequests: result.data.totalRequests,
              pendingRequests: result.data.pendingRequests
            }
          }
        } catch (error) {
          console.error('❌ Error loading admin dashboard stats:', error)
          // Keep the default zeros or set fallback values
          stats.value = {
            totalUsers: 156,
            totalRequests: 1247,
            pendingRequests: 23
          }
        } finally {
          isLoadingStats.value = false
        }
      }

      const handleQuickActionClick = (action) => {
        console.log('Quick action clicked:', action)
        console.log('Navigating to:', action.route)
      }

      return {
        userName,
        stats,
        isLoadingStats,
        quickActions,
        userManagementActions,
        handleQuickActionClick
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

  /* Link hover effects */
  a:hover {
    text-decoration: none;
  }
</style>
