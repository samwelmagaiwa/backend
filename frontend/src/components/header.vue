<template>
  <header
    class="bg-gradient-to-r from-blue-900 via-blue-800 to-blue-900 text-white py-3 px-6 border-b border-blue-600/40 shadow-xl backdrop-blur-sm relative z-50"
  >
    <div class="flex justify-between items-center">
      <!-- Left side - Logo and Title -->
      <div class="flex items-center space-x-4">
        <div class="flex items-center space-x-3">
          <div
            class="w-10 h-10 bg-gradient-to-br from-blue-500/30 to-blue-600/30 rounded-lg backdrop-blur-sm border border-blue-400/50 flex items-center justify-center shadow-lg"
          >
            <img
              src="/assets/images/ngao2.png"
              alt="National Shield"
              class="max-w-6 max-h-6 object-contain"
            />
          </div>
          <div>
            <h1 class="text-lg font-bold text-white tracking-wide">
              Muhimbili National Hospital
            </h1>
            <p class="text-xs text-blue-200">ICT Access Management System</p>
          </div>
        </div>
      </div>

      <!-- Right side - User Profile Dropdown -->
      <div class="flex items-center space-x-4">
        <!-- User Profile Dropdown -->
        <div class="relative" ref="profileDropdown">
          <!-- Profile Button -->
          <button
            @click="toggleProfileDropdown"
            class="flex items-center space-x-3 px-4 py-2 bg-blue-600/60 text-white rounded-lg hover:bg-blue-600/80 transition-all duration-300 border border-blue-500/40 focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-lg hover:shadow-blue-500/25"
          >
            <!-- User Avatar -->
            <div
              class="w-8 h-8 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center shadow-lg border-2 border-blue-300/30 relative overflow-hidden"
            >
              <div
                class="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent rounded-full"
              ></div>
              <i
                class="fas fa-user text-white text-sm relative z-10 drop-shadow-lg"
              ></i>
              <!-- Online Status Indicator -->
              <div
                class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 bg-green-400 rounded-full border border-white"
              ></div>
            </div>

            <!-- User Info -->
            <div v-if="currentUser" class="text-left">
              <p class="text-sm font-medium text-white leading-tight">
                {{ currentUser.name }}
              </p>
              <p class="text-xs text-blue-200 capitalize leading-tight">
                {{ formatRole(currentUser.role) }}
              </p>
            </div>

            <!-- Dropdown Arrow -->
            <i
              :class="[
                'fas fa-chevron-down text-xs text-blue-200 transition-transform duration-300',
                showProfileDropdown ? 'rotate-180' : ''
              ]"
            ></i>
          </button>

          <!-- Dropdown Menu -->
          <transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0 scale-95 translate-y-1"
            enter-to-class="opacity-100 scale-100 translate-y-0"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100 scale-100 translate-y-0"
            leave-to-class="opacity-0 scale-95 translate-y-1"
          >
            <div
              v-if="showProfileDropdown"
              class="profile-dropdown absolute right-0 mt-2 w-72 bg-white rounded-xl shadow-2xl border-2 border-blue-300/60 z-[9999] overflow-hidden backdrop-blur-sm"
              style="
                box-shadow:
                  0 25px 50px -12px rgba(59, 130, 246, 0.4),
                  0 10px 25px -5px rgba(59, 130, 246, 0.3),
                  0 4px 6px -1px rgba(59, 130, 246, 0.2),
                  inset 0 1px 0 rgba(255, 255, 255, 0.8),
                  inset 0 -1px 0 rgba(59, 130, 246, 0.1);
                border-image: linear-gradient(135deg, rgba(59, 130, 246, 0.8), rgba(147, 197, 253, 0.6), rgba(59, 130, 246, 0.8)) 1;
              "
            >
              <!-- User Info Header -->
              <div
                class="bg-gradient-to-r from-blue-600 via-blue-700 to-blue-800 px-4 py-4 relative overflow-hidden"
                style="
                  box-shadow:
                    0 8px 25px -5px rgba(59, 130, 246, 0.5),
                    inset 0 1px 0 rgba(255, 255, 255, 0.2),
                    inset 0 -1px 0 rgba(0, 0, 0, 0.1);
                  border-bottom: 2px solid rgba(59, 130, 246, 0.3);
                "
              >
                <!-- Multi-layer background effects -->
                <div class="absolute inset-0 bg-gradient-to-br from-blue-500/20 via-transparent to-blue-900/20"></div>
                <div class="absolute top-0 left-0 w-full h-px bg-gradient-to-r from-transparent via-white/30 to-transparent"></div>
                <div class="absolute bottom-0 left-0 w-full h-px bg-gradient-to-r from-transparent via-blue-300/50 to-transparent"></div>
                <div class="flex items-center space-x-3 relative z-10">
                  <!-- User Avatar Large -->
                  <div
                    class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center relative overflow-hidden"
                    style="
                      box-shadow:
                        0 8px 20px -4px rgba(59, 130, 246, 0.6),
                        0 4px 8px -2px rgba(59, 130, 246, 0.4),
                        inset 0 2px 4px rgba(255, 255, 255, 0.3),
                        inset 0 -2px 4px rgba(0, 0, 0, 0.2);
                      border: 2px solid rgba(255, 255, 255, 0.4);
                    "
                  >
                    <div
                      class="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent rounded-full"
                    ></div>
                    <i
                      class="fas fa-user text-white text-lg relative z-10 drop-shadow-lg"
                    ></i>
                    <!-- Online Status -->
                    <div
                      class="absolute -bottom-1 -right-1 w-3 h-3 bg-green-400 rounded-full border-2 border-white animate-pulse"
                    ></div>
                  </div>

                  <!-- User Details -->
                  <div class="flex-1">
                    <h3 class="text-white font-semibold text-sm">
                      {{ currentUser?.name || 'User' }}
                    </h3>
                    <p class="text-blue-100 text-xs">
                      {{ currentUser?.email || 'user@example.com' }}
                    </p>
                    <div class="flex items-center mt-1">
                      <span
                        class="bg-blue-500/40 text-blue-100 px-2 py-0.5 rounded-full text-xs font-medium relative overflow-hidden"
                        style="
                          box-shadow:
                            0 2px 8px rgba(59, 130, 246, 0.4),
                            inset 0 1px 0 rgba(255, 255, 255, 0.2);
                          border: 1px solid rgba(147, 197, 253, 0.5);
                        "
                      >
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-400/20 to-blue-600/20"></div>
                        <div class="relative z-10">
                          <i class="fas fa-id-badge mr-1"></i>
                          {{ formatRole(currentUser?.role) }}
                        </div>
                      </span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Menu Items -->
              <div class="py-2">
                <!-- Dashboard -->
                <button
                  @click="goToDashboard"
                  class="w-full flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 transition-all duration-300 text-left relative overflow-hidden group"
                >
                  <div
                    class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center mr-3 relative z-10"
                    style="
                      box-shadow:
                        0 4px 8px rgba(29, 78, 216, 0.4),
                        inset 0 1px 0 rgba(255, 255, 255, 0.3),
                        inset 0 -1px 0 rgba(29, 78, 216, 0.3);
                      border: 1px solid rgba(147, 197, 253, 0.4);
                    "
                  >
                    <i class="fas fa-home text-white text-sm"></i>
                  </div>
                  <div class="relative z-10">
                    <p class="font-medium text-sm">Dashboard</p>
                    <p class="text-xs text-gray-500">Go to your dashboard</p>
                  </div>
                </button>

                <!-- Profile -->
                <button
                  @click="goToProfile"
                  class="w-full flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 transition-all duration-300 text-left relative overflow-hidden group"
                >
                  <div
                    class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center mr-3 relative z-10"
                    style="
                      box-shadow:
                        0 4px 8px rgba(29, 78, 216, 0.4),
                        inset 0 1px 0 rgba(255, 255, 255, 0.3),
                        inset 0 -1px 0 rgba(29, 78, 216, 0.3);
                      border: 1px solid rgba(147, 197, 253, 0.4);
                    "
                  >
                    <i class="fas fa-user-circle text-white text-sm"></i>
                  </div>
                  <div class="relative z-10">
                    <p class="font-medium text-sm">My Profile</p>
                    <p class="text-xs text-gray-500">View and edit profile</p>
                  </div>
                </button>

                <!-- Reset Onboarding (Admin only) -->
                <button
                  v-if="currentUser?.role === 'admin'"
                  @click="openOnboardingReset"
                  class="w-full flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 transition-all duration-300 text-left relative overflow-hidden group"
                >
                  <div
                    class="w-8 h-8 bg-orange-500 rounded-lg flex items-center justify-center mr-3 relative z-10"
                    style="
                      box-shadow:
                        0 4px 8px rgba(234, 88, 12, 0.4),
                        inset 0 1px 0 rgba(255, 255, 255, 0.3),
                        inset 0 -1px 0 rgba(154, 52, 18, 0.3);
                      border: 1px solid rgba(251, 146, 60, 0.4);
                    "
                  >
                    <i class="fas fa-undo text-white text-sm"></i>
                  </div>
                  <div class="relative z-10">
                    <p class="font-medium text-sm">Reset Onboarding</p>
                    <p class="text-xs text-gray-500">Reset Terms & ICT Policy per user</p>
                  </div>
                </button>

                <!-- Debug Info (Temporary - Remove after testing) -->
                <div v-if="isDevelopment" class="px-4 py-2 text-xs text-gray-500 border-t border-gray-200">
                  Debug: Role = "{{ currentUser?.role || 'undefined' }}" |
                  Is Admin = {{ currentUser?.role === 'admin' ? 'YES' : 'NO' }}
                </div>

                <!-- Divider -->
                <div class="border-t border-gray-200 my-2"></div>

                <!-- Help & Support -->
                <button
                  @click="showHelp"
                  class="w-full flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 transition-all duration-300 text-left relative overflow-hidden group"
                >
                  <div
                    class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center mr-3 relative z-10"
                    style="
                      box-shadow:
                        0 4px 8px rgba(34, 197, 94, 0.4),
                        inset 0 1px 0 rgba(255, 255, 255, 0.3),
                        inset 0 -1px 0 rgba(21, 128, 61, 0.3);
                      border: 1px solid rgba(74, 222, 128, 0.4);
                    "
                  >
                    <i class="fas fa-question-circle text-white text-sm"></i>
                  </div>
                  <div class="relative z-10">
                    <p class="font-medium text-sm">Help & Support</p>
                    <p class="text-xs text-gray-500">Get assistance</p>
                  </div>
                </button>

                <!-- Divider -->
                <div class="border-t border-gray-200 my-2"></div>

                <!-- Logout -->
                <button
                  @click="logout"
                  class="w-full flex items-center px-4 py-3 text-gray-700 hover:bg-red-50 transition-all duration-300 text-left relative overflow-hidden group"
                >
                  <div
                    class="w-8 h-8 bg-red-500 rounded-lg flex items-center justify-center mr-3 relative z-10"
                    style="
                      box-shadow:
                        0 4px 8px rgba(153, 27, 27, 0.4),
                        inset 0 1px 0 rgba(255, 255, 255, 0.3),
                        inset 0 -1px 0 rgba(153, 27, 27, 0.3);
                      border: 1px solid rgba(248, 113, 113, 0.4);
                    "
                  >
                    <i class="fas fa-sign-out-alt text-white text-sm"></i>
                  </div>
                  <div class="relative z-10">
                    <p class="font-medium text-sm">Sign Out</p>
                    <p class="text-xs text-gray-500">Logout from your account</p>
                  </div>
                </button>
              </div>
            </div>
          </transition>
        </div>
      </div>
    </div>

    <!-- Help Modal -->
    <div
      v-if="showHelpModal"
      class="fixed inset-0 bg-black/60 flex items-center justify-center z-[9998] backdrop-blur-sm"
    >
      <div
        class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 transform transition-all duration-300 scale-100 overflow-hidden"
      >
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-blue-800 p-6 text-center shadow-lg">
          <div
            class="w-16 h-16 bg-blue-500/30 rounded-full flex items-center justify-center mx-auto mb-4 backdrop-blur-sm border border-blue-300/40 shadow-lg"
          >
            <i class="fas fa-question-circle text-white text-2xl"></i>
          </div>
          <h3 class="text-xl font-bold text-white mb-2">Help & Support</h3>
        </div>

        <!-- Body -->
        <div class="p-6">
          <div class="space-y-4">
            <div class="flex items-center p-3 bg-blue-50 rounded-lg shadow-sm border border-blue-100">
              <div
                class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center mr-3 shadow-md"
              >
                <i class="fas fa-phone text-white"></i>
              </div>
              <div>
                <p class="font-medium text-gray-800">ICT Support</p>
                <p class="text-sm text-gray-600">+255 123 456 789</p>
              </div>
            </div>

            <div class="flex items-center p-3 bg-blue-50 rounded-lg shadow-sm border border-blue-100">
              <div
                class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center mr-3 shadow-md"
              >
                <i class="fas fa-envelope text-white"></i>
              </div>
              <div>
                <p class="font-medium text-gray-800">Email Support</p>
                <p class="text-sm text-gray-600">ict@mnh.or.tz</p>
              </div>
            </div>

            <div class="flex items-center p-3 bg-blue-50 rounded-lg shadow-sm border border-blue-100">
              <div
                class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center mr-3 shadow-md"
              >
                <i class="fas fa-book text-white"></i>
              </div>
              <div>
                <p class="font-medium text-gray-800">User Guide</p>
                <p class="text-sm text-gray-600">Access system documentation</p>
              </div>
            </div>
          </div>

          <!-- Close Button -->
          <div class="mt-6">
            <button
              @click="showHelpModal = false"
              class="w-full bg-blue-600 text-white py-3 px-4 rounded-xl font-medium hover:bg-blue-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105"
            >
              Close
            </button>
          </div>
        </div>
      </div>
    </div>
  </header>
</template>

<script>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useAuth } from '@/composables/useAuth'
import { useRouter } from 'vue-router'
import { logoutGuard } from '@/utils/logoutGuard'

export default {
  name: 'HeaderComponent',
  setup() {
    const { currentUser, logout: authLogout } = useAuth()
    const router = useRouter()

    // Reactive state
    const showProfileDropdown = ref(false)
    const showHelpModal = ref(false)
    const profileDropdown = ref(null)

    // Environment check using Vue CLI's process.env
    const isDevelopment = computed(() => {
      return process.env.NODE_ENV === 'development'
    })

    // Methods
    const toggleProfileDropdown = () => {
      showProfileDropdown.value = !showProfileDropdown.value
    }

    const closeProfileDropdown = () => {
      showProfileDropdown.value = false
    }

    const formatRole = (role) => {
      if (!role) return 'User'

      const roleMap = {
        admin: 'Administrator',
        ict_officer: 'ICT Officer',
        head_of_department: 'Head of Department',

        divisional_director: 'Divisional Director',
        ict_director: 'ICT Director',
        staff: 'Staff Member'
      }

      return roleMap[role] || role.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())
    }

    const goToDashboard = () => {
      closeProfileDropdown()

      // Navigate to appropriate dashboard based on user role
      if (currentUser.value?.role) {
        switch (currentUser.value.role) {
          case 'admin':
            router.push('/admin-dashboard')
            break
          case 'ict_officer':
            router.push('/ict-dashboard')
            break
          case 'head_of_department':
            router.push('/hod-dashboard')
            break
          case 'divisional_director':
            router.push('/divisional-dashboard')
            break
          case 'ict_director':
            router.push('/dict-dashboard')
            break
          case 'staff':
            router.push('/user-dashboard')
            break
          default:
            router.push('/user-dashboard')
        }
      } else {
        router.push('/user-dashboard')
      }
    }

    const goToProfile = () => {
      closeProfileDropdown()
      router.push('/profile')
    }

    const openOnboardingReset = () => {
      closeProfileDropdown()
      router.push('/admin/onboarding-reset')
    }

    const showHelp = () => {
      closeProfileDropdown()
      showHelpModal.value = true
    }

    const logout = async() => {
      closeProfileDropdown()

      try {
        await logoutGuard.executeLogout(async() => {
          await authLogout()
        })
        router.push('/login')
      } catch (error) {
        console.error('Logout error:', error)
        // Force redirect to login even if logout fails
        router.push('/login')
      }
    }

    // Click outside to close dropdown
    const handleClickOutside = (event) => {
      if (profileDropdown.value && !profileDropdown.value.contains(event.target)) {
        closeProfileDropdown()
      }
    }

    // Lifecycle
    onMounted(() => {
      document.addEventListener('click', handleClickOutside)
    })

    onUnmounted(() => {
      document.removeEventListener('click', handleClickOutside)
    })

    return {
      currentUser,
      showProfileDropdown,
      showHelpModal,
      profileDropdown,
      isDevelopment,
      toggleProfileDropdown,
      closeProfileDropdown,
      formatRole,
      goToDashboard,
      goToProfile,
      openOnboardingReset,
      showHelp,
      logout
    }
  }
}
</script>

<style scoped>
/* Header specific styles */
header {
  backdrop-filter: blur(10px);
  -webkit-backdrop-filter: blur(10px);
  position: relative;
  z-index: 50;
}

/* Profile dropdown specific z-index */
.profile-dropdown {
  z-index: 9999 !important;
  position: absolute !important;
  top: 100% !important;
  right: 0 !important;
}

/* Ensure dropdown container has proper stacking context */
.relative {
  position: relative;
  z-index: 100;
}

/* Button hover effects */
button:hover {
  transform: translateY(-1px);
}

/* Dropdown shadow */
.shadow-2xl {
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .w-72 {
    width: calc(100vw - 2rem);
    max-width: 20rem;
  }

  .flex.justify-between {
    flex-direction: column;
    gap: 0.5rem;
  }

  .relative {
    position: static;
  }

  .absolute.right-0 {
    position: fixed;
    right: 1rem;
    left: 1rem;
    width: auto;
  }
}

/* Animation improvements */
.transition-all {
  transition: all 0.3s ease;
}

/* Profile button active state */
.focus\:ring-2:focus {
  box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5);
}

/* Dropdown item hover effects */
.hover\:bg-blue-50:hover {
  background-color: rgba(239, 246, 255, 1);
}

.hover\:bg-red-50:hover {
  background-color: rgba(254, 242, 242, 1);
}

/* Status indicator animation */
@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}

.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Global override for dropdown visibility */
:deep(.profile-dropdown) {
  z-index: 9999 !important;
  position: absolute !important;
}
</style>
