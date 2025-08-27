<template>
  <div class="mobile-menu-container">
    <!-- Hamburger Button -->
    <button
      @click="toggleMenu"
      class="hamburger-button lg:hidden p-3 rounded-xl text-white hover:bg-blue-700/30 focus:outline-none focus:ring-2 focus:ring-blue-400 transition-all duration-300 backdrop-blur-sm relative overflow-hidden group"
      :class="{ active: isMenuOpen }"
      aria-label="Toggle mobile menu"
    >
      <!-- Animated background -->
      <div
        class="absolute inset-0 bg-gradient-to-r from-blue-600/20 to-teal-600/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-xl"
      ></div>

      <!-- Hamburger Icon -->
      <div class="hamburger-icon relative z-10">
        <span class="hamburger-line"></span>
        <span class="hamburger-line"></span>
        <span class="hamburger-line"></span>
      </div>
    </button>

    <!-- Mobile Menu Overlay -->
    <transition
      enter-active-class="transition-opacity ease-linear duration-300"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-opacity ease-linear duration-300"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="isMenuOpen"
        class="fixed inset-0 bg-black/70 backdrop-blur-md z-40 lg:hidden"
        @click="closeMenu"
        style="backdrop-filter: blur(12px)"
      ></div>
    </transition>

    <!-- Mobile Menu Panel -->
    <transition
      enter-active-class="transition ease-in-out duration-300 transform"
      enter-from-class="-translate-x-full"
      enter-to-class="translate-x-0"
      leave-active-class="transition ease-in-out duration-300 transform"
      leave-from-class="translate-x-0"
      leave-to-class="-translate-x-full"
    >
      <div
        v-if="isMenuOpen"
        class="fixed top-0 left-0 h-full w-80 bg-gradient-to-b from-blue-900 via-blue-800 to-teal-900 shadow-2xl z-50 lg:hidden overflow-y-auto border-r-2 border-blue-600/50"
        style="
          backdrop-filter: blur(25px);
          box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        "
      >
        <!-- Menu Header -->
        <div
          class="flex items-center justify-between p-6 border-b-2 border-blue-600/30 bg-gradient-to-r from-blue-800/50 to-blue-700/50"
        >
          <div class="flex items-center">
            <!-- Hospital Logo -->
            <div
              class="w-12 h-12 bg-gradient-to-br from-blue-400 to-cyan-500 rounded-xl flex items-center justify-center mr-4 shadow-xl border-2 border-white/20"
            >
              <i class="fas fa-hospital text-white text-xl drop-shadow-lg"></i>
            </div>
            <div>
              <h2 class="text-xl font-bold text-white drop-shadow-lg">
                Muhimbili
              </h2>
              <!-- Portal text removed -->
            </div>
          </div>

          <!-- Close Button -->
          <button
            @click="closeMenu"
            class="p-3 rounded-xl text-blue-200 hover:text-white hover:bg-blue-700/30 transition-all duration-300 backdrop-blur-sm relative overflow-hidden group"
          >
            <div
              class="absolute inset-0 bg-gradient-to-r from-red-500/20 to-red-600/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-xl"
            ></div>
            <i class="fas fa-times text-xl relative z-10 drop-shadow-sm"></i>
          </button>
        </div>

        <!-- User Info Section -->
        <div
          class="p-6 border-b border-blue-600/30 bg-gradient-to-r from-blue-800/30 to-blue-700/30"
        >
          <div class="flex items-center">
            <div
              class="w-14 h-14 bg-gradient-to-br from-blue-400 via-cyan-400 to-blue-500 rounded-full flex items-center justify-center mr-4 shadow-xl border-3 border-white/20 relative"
            >
              <i class="fas fa-user-md text-white text-xl drop-shadow-lg"></i>
              <div
                class="absolute -bottom-1 -right-1 w-5 h-5 bg-green-500 rounded-full border-2 border-white shadow-lg"
              ></div>
            </div>
            <div class="flex-1">
              <h3 class="text-lg font-bold text-white drop-shadow-lg">
                {{ currentUser?.name || "Staff Member" }}
              </h3>
              <!-- Role display removed -->
              <div class="flex items-center mt-1">
                <div
                  class="w-2 h-2 bg-green-400 rounded-full mr-2 shadow-sm"
                ></div>
                <span class="text-xs text-green-300 font-medium">Online</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="py-4">
          <!-- Dashboard Section -->
          <div class="mb-6">
            <div class="px-6 py-2">
              <h3
                class="text-xs font-semibold text-blue-200 uppercase tracking-wider drop-shadow-sm flex items-center"
              >
                <i class="fas fa-tachometer-alt mr-2 text-blue-300"></i>
                Dashboard
              </h3>
            </div>

            <!-- Dashboard Menu Item -->
            <router-link
              :to="getDashboardRoute()"
              @click="closeMenu"
              class="flex items-center px-6 py-4 text-white hover:bg-blue-600/50 transition-all duration-300 group relative overflow-hidden border-l-4 border-transparent hover:border-blue-400"
              :class="{ 'bg-blue-600/50 border-blue-400': isDashboardActive }"
            >
              <div
                class="absolute inset-0 bg-gradient-to-r from-blue-600/20 to-cyan-600/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
              ></div>
              <div
                class="w-12 h-12 bg-gradient-to-br from-blue-600/40 to-blue-500/40 rounded-xl flex items-center justify-center mr-4 group-hover:bg-gradient-to-br group-hover:from-blue-500 group-hover:to-cyan-500 transition-all duration-300 shadow-lg border border-blue-400/30"
              >
                <i
                  class="fas fa-chart-pie text-blue-200 group-hover:text-white text-lg transition-colors duration-300"
                ></i>
              </div>
              <div class="flex-1 relative z-10">
                <span class="font-semibold drop-shadow-sm text-lg"
                  >Dashboard</span
                >
                <p
                  class="text-sm text-blue-200 opacity-80 group-hover:opacity-100 transition-opacity duration-300"
                >
                  Main control panel
                </p>
              </div>
              <i
                class="fas fa-chevron-right text-blue-300 group-hover:text-white transition-all duration-300 group-hover:translate-x-1"
              ></i>
            </router-link>

            <!-- User Dashboard (if different from main dashboard) -->
            <router-link
              v-if="showUserDashboard"
              to="/user-dashboard"
              @click="closeMenu"
              class="flex items-center px-6 py-4 text-white hover:bg-blue-600/50 transition-all duration-300 group relative overflow-hidden border-l-4 border-transparent hover:border-emerald-400"
              :class="{
                'bg-blue-600/50 border-emerald-400':
                  $route.path === '/user-dashboard',
              }"
            >
              <div
                class="absolute inset-0 bg-gradient-to-r from-emerald-600/20 to-blue-600/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
              ></div>
              <div
                class="w-12 h-12 bg-gradient-to-br from-emerald-600/40 to-emerald-500/40 rounded-xl flex items-center justify-center mr-4 group-hover:bg-gradient-to-br group-hover:from-emerald-500 group-hover:to-blue-500 transition-all duration-300 shadow-lg border border-emerald-400/30"
              >
                <i
                  class="fas fa-user text-emerald-200 group-hover:text-white text-lg transition-colors duration-300"
                ></i>
              </div>
              <div class="flex-1 relative z-10">
                <span class="font-semibold drop-shadow-sm text-lg"
                  >User Dashboard</span
                >
                <p
                  class="text-sm text-blue-200 opacity-80 group-hover:opacity-100 transition-opacity duration-300"
                >
                  Personal workspace
                </p>
              </div>
              <i
                class="fas fa-chevron-right text-blue-300 group-hover:text-white transition-all duration-300 group-hover:translate-x-1"
              ></i>
            </router-link>
          </div>

          <!-- Request Forms Section -->
          <div class="mb-6">
            <div class="px-6 py-2">
              <h3
                class="text-xs font-semibold text-blue-200 uppercase tracking-wider drop-shadow-sm flex items-center"
              >
                <i class="fas fa-file-alt mr-2 text-blue-300"></i>
                Request Forms
              </h3>
            </div>

            <!-- Forms Submenu -->
            <div class="space-y-1">
              <!-- COMMENTED OUT: Individual forms - now using Combined Access Form only

              Jeeva Access Form, Wellsoft Access Form, and Internet Access Form
              have been replaced with the Combined Access Form below.

              The individual forms are no longer used in the current implementation.
              -->

              <!-- Combined Access Form -->
              <router-link
                :to="getFormRoute('combined')"
                @click="closeMenu"
                class="flex items-center px-6 py-4 text-white hover:bg-gradient-to-r hover:from-cyan-600/30 hover:to-blue-600/30 transition-all duration-300 group relative overflow-hidden border-l-4 border-transparent hover:border-cyan-400"
                :class="{
                  'bg-gradient-to-r from-cyan-600/30 to-blue-600/30 border-cyan-400':
                    isFormActive('combined'),
                }"
              >
                <div
                  class="absolute inset-0 bg-gradient-to-r from-cyan-600/10 to-blue-600/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                ></div>
                <div
                  class="w-10 h-10 bg-gradient-to-br from-cyan-600/40 to-cyan-500/40 rounded-lg flex items-center justify-center mr-4 group-hover:bg-gradient-to-br group-hover:from-cyan-500 group-hover:to-blue-500 transition-all duration-300 shadow-lg border border-cyan-400/30"
                >
                  <i
                    class="fas fa-layer-group text-cyan-200 group-hover:text-white transition-colors duration-300"
                  ></i>
                </div>
                <div class="flex-1 relative z-10">
                  <span class="font-medium drop-shadow-sm"
                    >Combined Access</span
                  >
                  <p
                    class="text-xs text-blue-200 opacity-80 group-hover:opacity-100 transition-opacity duration-300"
                  >
                    Multiple services
                  </p>
                </div>
                <i
                  class="fas fa-chevron-right text-blue-300 group-hover:text-white text-sm transition-all duration-300 group-hover:translate-x-1"
                ></i>
              </router-link>
            </div>
          </div>
        </nav>

        <!-- Footer -->
        <div
          class="mt-auto border-t-2 border-blue-600/30 p-6 bg-gradient-to-r from-blue-800/30 to-blue-700/30"
        >
          <div class="text-center">
            <div class="text-xs text-blue-300 drop-shadow-sm mb-2">
              © 2025 Muhimbili National Hospital
            </div>
            <!-- Footer text removed -->
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
import { ref, computed } from 'vue'
import { useAuth } from '@/composables/useAuth'
import { ROLES } from '@/utils/permissions'

export default {
  name: 'MobileHamburgerMenu',
  setup() {
    const { currentUser, userRole } = useAuth()
    const isMenuOpen = ref(false)

    const showUserDashboard = computed(() => {
      // Show User Dashboard option if user is not already on a user-specific dashboard
      return (
        userRole.value === ROLES.STAFF || userRole.value === ROLES.ICT_OFFICER
      )
    })

    const isDashboardActive = computed(() => {
      const currentPath = window.location.pathname
      const dashboardPaths = [
        '/admin-dashboard',
        '/user-dashboard',
        '/dict-dashboard',
        '/hod-dashboard',
        '/divisional-dashboard',
        '/ict-dashboard'
      ]
      return dashboardPaths.includes(currentPath)
    })

    function toggleMenu() {
      isMenuOpen.value = !isMenuOpen.value
    }

    function closeMenu() {
      isMenuOpen.value = false
    }

    function getRoleDisplayName(role) {
      const roleNames = {
        [ROLES.ADMIN]: 'Administrator',
        [ROLES.DIVISIONAL_DIRECTOR]: 'Divisional Director',
        [ROLES.HEAD_OF_DEPARTMENT]: 'Head of Department',

        [ROLES.ICT_DIRECTOR]: 'ICT Director',
        [ROLES.STAFF]: 'Staff Member',
        [ROLES.ICT_OFFICER]: 'ICT Officer'
      }
      return roleNames[role] || 'Staff Member'
    }

    function getDashboardRoute() {
      // Return appropriate dashboard based on user role
      const dashboardRoutes = {
        [ROLES.ADMIN]: '/admin-dashboard',
        [ROLES.DIVISIONAL_DIRECTOR]: '/divisional-dashboard',
        [ROLES.HEAD_OF_DEPARTMENT]: '/hod-dashboard',

        [ROLES.ICT_DIRECTOR]: '/dict-dashboard',
        [ROLES.ICT_OFFICER]: '/ict-dashboard',
        [ROLES.STAFF]: '/user-dashboard'
      }
      return dashboardRoutes[userRole.value] || '/user-dashboard'
    }

    function getFormRoute(formType) {
      // Return appropriate form route based on user role and form type
      const isStaff = userRole.value === ROLES.STAFF

      const formRoutes = {
        // COMMENTED OUT: Individual forms - now using Combined Access Form only
        // jeeva: isStaff ? '/user-jeeva-form' : '/jeeva-access',
        // wellsoft: isStaff ? '/user-wellsoft-form' : '/wellsoft-access',
        // internet: isStaff ? '/user-internet-form' : '/internet-access',
        combined: isStaff ? '/user-combined-form' : '/both-service-form'
      }

      return formRoutes[formType] || '/user-dashboard'
    }

    function isFormActive(formType) {
      const currentPath = window.location.pathname
      const formPaths = {
        // COMMENTED OUT: Individual forms - now using Combined Access Form only
        // jeeva: ['/user-jeeva-form', '/jeeva-access'],
        // wellsoft: ['/user-wellsoft-form', '/wellsoft-access'],
        // internet: ['/user-internet-form', '/internet-access'],
        combined: ['/user-combined-form', '/both-service-form']
      }

      return formPaths[formType]?.includes(currentPath) || false
    }

    // Close menu when clicking outside or pressing escape
    function handleKeydown(event) {
      if (event.key === 'Escape') {
        closeMenu()
      }
    }

    // Handle window resize
    function handleResize() {
      if (window.innerWidth >= 1024) {
        closeMenu()
      }
    }

    return {
      isMenuOpen,
      currentUser,
      userRole,
      showUserDashboard,
      isDashboardActive,
      toggleMenu,
      closeMenu,
      getRoleDisplayName,
      getDashboardRoute,
      getFormRoute,
      isFormActive,
      handleKeydown,
      handleResize
    }
  },
  mounted() {
    // Add event listeners
    document.addEventListener('keydown', this.handleKeydown)
    window.addEventListener('resize', this.handleResize)
  },
  beforeUnmount() {
    // Remove event listeners
    document.removeEventListener('keydown', this.handleKeydown)
    window.removeEventListener('resize', this.handleResize)
  }
}
</script>

<style scoped>
/* Hamburger Button Animation */
.hamburger-button {
  position: relative;
  z-index: 51;
}

.hamburger-icon {
  width: 24px;
  height: 18px;
  position: relative;
  transform: rotate(0deg);
  transition: 0.3s ease-in-out;
  cursor: pointer;
}

.hamburger-line {
  display: block;
  position: absolute;
  height: 3px;
  width: 100%;
  background: currentColor;
  border-radius: 2px;
  opacity: 1;
  left: 0;
  transform: rotate(0deg);
  transition: 0.3s ease-in-out;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.hamburger-line:nth-child(1) {
  top: 0px;
}

.hamburger-line:nth-child(2) {
  top: 7px;
}

.hamburger-line:nth-child(3) {
  top: 14px;
}

/* Active state - X formation */
.hamburger-button.active .hamburger-line:nth-child(1) {
  top: 7px;
  transform: rotate(135deg);
}

.hamburger-button.active .hamburger-line:nth-child(2) {
  opacity: 0;
  left: -60px;
}

.hamburger-button.active .hamburger-line:nth-child(3) {
  top: 7px;
  transform: rotate(-135deg);
}

/* Hover effects */
.hamburger-button:hover .hamburger-line {
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
}

/* Mobile menu animations */
.mobile-menu-container {
  position: relative;
}

/* Custom scrollbar for mobile menu */
.overflow-y-auto::-webkit-scrollbar {
  width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  background-color: rgba(59, 130, 246, 0.3);
  border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  background-color: rgba(0, 0, 0, 0.1);
}

/* Smooth transitions for all interactive elements */
* {
  transition-property: color, background-color, border-color,
    text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter,
    backdrop-filter;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 200ms;
}

/* Focus styles for accessibility */
button:focus {
  outline: none;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
}

/* Active link styles */
.router-link-active {
  background: rgba(59, 130, 246, 0.3);
  border-left-color: #3b82f6;
}

/* Responsive adjustments */
@media (max-width: 480px) {
  .w-80 {
    width: 100vw;
  }
}

/* Animation keyframes */
@keyframes slideInLeft {
  from {
    transform: translateX(-100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
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

/* Backdrop blur support */
@supports (backdrop-filter: blur(12px)) {
  .backdrop-blur-md {
    backdrop-filter: blur(12px);
  }
}

@supports not (backdrop-filter: blur(12px)) {
  .backdrop-blur-md {
    background-color: rgba(0, 0, 0, 0.8);
  }
}
</style>
