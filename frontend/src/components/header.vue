<template>
  <header class="bg-gradient-to-r from-blue-900 via-blue-800 to-blue-700 shadow-2xl px-2 sm:px-4 lg:px-6 py-2 mb-2 flex justify-between items-center h-12 sm:h-14 lg:h-16 relative z-50 border-b border-blue-600/30" style="backdrop-filter: blur(20px);">
    <!-- Mobile Hamburger Menu -->
    <MobileHamburgerMenu />

    <!-- Logo and Title -->
    <div class="flex items-center flex-1 lg:flex-none">
      <div class="w-6 h-6 sm:w-8 sm:h-8 lg:w-10 lg:h-10 mr-2 sm:mr-3 bg-gradient-to-br from-blue-400 to-blue-500 rounded-lg flex items-center justify-center shadow-lg border border-blue-300/50">
        <i class="fas fa-hospital text-white text-xs sm:text-sm lg:text-base drop-shadow-sm"></i>
      </div>
      <h1 class="text-sm sm:text-lg lg:text-xl font-bold text-white truncate drop-shadow-md">
        <span class="hidden sm:inline">Muhimbili Dashboard</span>
        <span class="sm:hidden">MNH</span>
      </h1>
    </div>

    <!-- Desktop Navigation -->
    <nav class="hidden lg:flex items-center space-x-6 flex-1 justify-center">
      <router-link 
        v-for="item in navigationItems" 
        :key="item.name"
        :to="item.path"
        class="text-sm font-medium text-blue-100 hover:text-white transition-colors px-3 py-2 rounded-md hover:bg-blue-700/30 backdrop-blur-sm drop-shadow-sm"
        active-class="text-white bg-blue-600/50 shadow-lg"
      >
        <i :class="[item.icon, 'mr-2']"></i>
        {{ item.name }}
      </router-link>
    </nav>

    <!-- User Menu -->
    <div class="flex items-center">
      <!-- Notifications (Desktop) -->
      <button class="hidden sm:flex relative items-center p-2 text-blue-200 hover:text-white hover:bg-blue-700/30 rounded-md mr-2 focus-visible transition-colors backdrop-blur-sm">
        <i class="fas fa-bell text-lg drop-shadow-sm"></i>
        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center shadow-lg">3</span>
      </button>

      <!-- User Dropdown -->
      <div class="relative">
        <button 
          @click="toggleDropdown" 
          class="flex items-center text-blue-100 hover:text-white focus-visible rounded-md p-1 sm:p-2 transition-colors backdrop-blur-sm"
          aria-label="User menu"
        >
          <div class="w-6 h-6 sm:w-8 sm:h-8 bg-gradient-to-br from-blue-400 to-blue-500 rounded-full flex items-center justify-center mr-1 sm:mr-2 shadow-lg border border-blue-300/50">
            <i class="fas fa-user text-white text-xs sm:text-sm drop-shadow-sm"></i>
          </div>
          <span class="text-xs sm:text-sm font-medium hidden sm:inline drop-shadow-sm">{{ currentUser?.name || 'User' }}</span>
          <i class="fas fa-chevron-down ml-1 text-xs drop-shadow-sm"></i>
        </button>
        
        <!-- Dropdown Menu -->
        <transition
          enter-active-class="transition ease-out duration-300"
          enter-from-class="transform opacity-0 scale-95 translate-y-2"
          enter-to-class="transform opacity-100 scale-100 translate-y-0"
          leave-active-class="transition ease-in duration-200"
          leave-from-class="transform opacity-100 scale-100 translate-y-0"
          leave-to-class="transform opacity-0 scale-95 translate-y-2"
        >
          <div 
            v-if="showDropdown" 
            class="absolute right-0 mt-3 w-72 bg-gradient-to-br from-blue-900 via-blue-800 to-blue-700 rounded-2xl shadow-2xl py-4 z-50 border-2 border-blue-400/40 backdrop-blur-lg"
            style="backdrop-filter: blur(25px); box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(59, 130, 246, 0.3), inset 0 1px 0 rgba(255, 255, 255, 0.1);"
          >
            <!-- User Info Header -->
            <div class="px-6 py-5 border-b border-blue-400/30 bg-gradient-to-r from-blue-800/50 to-blue-700/50 rounded-t-2xl">
              <div class="flex items-center">
                <div class="relative">
                  <div class="w-14 h-14 bg-gradient-to-br from-blue-400 via-blue-500 to-blue-600 rounded-full flex items-center justify-center mr-4 shadow-xl border-3 border-white/20">
                    <i class="fas fa-user-tie text-white text-xl drop-shadow-lg"></i>
                  </div>
                  <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-green-500 rounded-full border-2 border-white shadow-lg"></div>
                </div>
                <div class="flex-1">
                  <h3 class="text-base font-bold text-white drop-shadow-lg mb-1">Divisional Director</h3>
                  <p class="text-sm text-blue-200 drop-shadow-sm font-medium">divisional@mnh.go.tz</p>
                  <div class="flex items-center mt-1">
                    <div class="w-2 h-2 bg-green-400 rounded-full mr-2 shadow-sm"></div>
                    <span class="text-xs text-green-300 font-medium">Online</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Menu Items -->
            <div class="py-3">
              <router-link 
                to="/profile" 
                @click="closeDropdown"
                class="flex items-center px-6 py-4 text-sm text-blue-100 hover:text-white hover:bg-blue-600/50 transition-all duration-300 group relative overflow-hidden"
              >
                <div class="absolute inset-0 bg-gradient-to-r from-blue-600/20 to-blue-500/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="w-10 h-10 bg-gradient-to-br from-blue-600/40 to-blue-500/40 rounded-xl flex items-center justify-center mr-4 group-hover:bg-gradient-to-br group-hover:from-blue-500 group-hover:to-blue-600 transition-all duration-300 shadow-lg border border-blue-400/30">
                  <i class="fas fa-user-circle text-blue-200 group-hover:text-white text-lg transition-colors duration-300"></i>
                </div>
                <span class="font-semibold drop-shadow-sm relative z-10">Profile</span>
                <i class="fas fa-chevron-right text-blue-300 group-hover:text-white ml-auto text-xs transition-all duration-300 group-hover:translate-x-1"></i>
              </router-link>
              
              <router-link 
                to="/settings" 
                @click="closeDropdown"
                class="flex items-center px-6 py-4 text-sm text-blue-100 hover:text-white hover:bg-blue-600/50 transition-all duration-300 group relative overflow-hidden"
              >
                <div class="absolute inset-0 bg-gradient-to-r from-blue-600/20 to-blue-500/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="w-10 h-10 bg-gradient-to-br from-blue-600/40 to-blue-500/40 rounded-xl flex items-center justify-center mr-4 group-hover:bg-gradient-to-br group-hover:from-blue-500 group-hover:to-blue-600 transition-all duration-300 shadow-lg border border-blue-400/30">
                  <i class="fas fa-cog text-blue-200 group-hover:text-white text-lg transition-colors duration-300"></i>
                </div>
                <span class="font-semibold drop-shadow-sm relative z-10">Settings</span>
                <i class="fas fa-chevron-right text-blue-300 group-hover:text-white ml-auto text-xs transition-all duration-300 group-hover:translate-x-1"></i>
              </router-link>
              
              <div class="mx-6 my-3 border-t border-blue-400/30"></div>
              
              <button 
                @click="showLogoutConfirmation" 
                class="flex items-center w-full px-6 py-4 text-sm text-red-200 hover:text-white hover:bg-red-600/50 transition-all duration-300 group relative overflow-hidden"
              >
                <div class="absolute inset-0 bg-gradient-to-r from-red-600/20 to-red-500/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="w-10 h-10 bg-gradient-to-br from-red-600/40 to-red-500/40 rounded-xl flex items-center justify-center mr-4 group-hover:bg-gradient-to-br group-hover:from-red-500 group-hover:to-red-600 transition-all duration-300 shadow-lg border border-red-400/30">
                  <i class="fas fa-sign-out-alt text-red-300 group-hover:text-white text-lg transition-colors duration-300"></i>
                </div>
                <span class="font-semibold drop-shadow-sm relative z-10">Logout</span>
                <i class="fas fa-chevron-right text-red-300 group-hover:text-white ml-auto text-xs transition-all duration-300 group-hover:translate-x-1"></i>
              </button>
            </div>
          </div>
        </transition>
      </div>
    </div>



    <!-- Logout Confirmation Modal -->
    <transition
      enter-active-class="transition ease-out duration-300"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition ease-in duration-200"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div 
        v-if="showLogoutModal" 
        class="fixed inset-0 bg-black/70 backdrop-blur-md z-[60] flex items-end sm:items-center justify-center p-4 pb-8 sm:pb-4"
        @click="cancelLogout"
        style="backdrop-filter: blur(12px);"
      >
        <!-- Confirmation Card -->
        <transition
          enter-active-class="transition ease-out duration-400"
          enter-from-class="opacity-0 transform scale-90 translate-y-8"
          enter-to-class="opacity-100 transform scale-100 translate-y-0"
          leave-active-class="transition ease-in duration-200"
          leave-from-class="opacity-100 transform scale-100 translate-y-0"
          leave-to-class="opacity-0 transform scale-90 translate-y-8"
        >
          <div 
            v-if="showLogoutModal"
            @click.stop
            class="bg-gradient-to-br from-blue-50 via-white to-blue-50 rounded-3xl max-w-xl w-full mx-4 overflow-hidden border-2 border-blue-300/60 transform translate-y-4 sm:translate-y-0"
            style="box-shadow: 0 35px 60px -12px rgba(29, 78, 216, 0.5), 0 25px 50px -12px rgba(0, 0, 0, 0.3), 0 0 0 1px rgba(59, 130, 246, 0.4), inset 0 2px 0 rgba(255, 255, 255, 0.9); backdrop-filter: blur(25px);"
          >
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-blue-900 via-blue-800 to-blue-700 px-8 py-8 relative overflow-hidden">
              <!-- Animated background pattern -->
              <div class="absolute inset-0 opacity-15">
                <div class="absolute inset-0 bg-gradient-to-r from-blue-600/30 to-blue-500/30 animate-pulse"></div>
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-blue-400 to-transparent"></div>
              </div>
              
              <div class="flex items-center relative z-10">
                <div class="w-16 h-16 bg-gradient-to-br from-red-500 via-red-600 to-red-700 rounded-3xl flex items-center justify-center mr-5 shadow-2xl border-3 border-white/30 relative">
                  <div class="absolute inset-0 bg-gradient-to-br from-red-400/50 to-transparent rounded-3xl"></div>
                  <i class="fas fa-sign-out-alt text-white text-2xl drop-shadow-lg relative z-10"></i>
                </div>
                <div>
                  <h3 class="text-3xl font-bold text-white drop-shadow-lg mb-2">Confirm Logout</h3>
                  <p class="text-blue-200 text-base drop-shadow-sm font-medium">Security confirmation required</p>
                </div>
              </div>
            </div>

            <!-- Modal Body -->
            <div class="px-8 py-10">
              <div class="text-center mb-10">
                <div class="w-24 h-24 bg-gradient-to-br from-blue-100 via-blue-200 to-blue-300 rounded-3xl flex items-center justify-center mx-auto mb-8 shadow-2xl border-3 border-blue-300/60 relative">
                  <div class="absolute inset-0 bg-gradient-to-br from-blue-400/20 to-transparent rounded-3xl"></div>
                  <i class="fas fa-question-circle text-blue-600 text-4xl drop-shadow-lg relative z-10"></i>
                </div>
                <h4 class="text-blue-900 text-2xl font-bold mb-4 drop-shadow-sm">
                  Are you sure you want to logout?
                </h4>
                <p class="text-blue-700 text-lg leading-relaxed font-medium">
                  You will be signed out of your current session and redirected to the login page.
                </p>
              </div>

              <!-- Action Buttons -->
              <div class="flex gap-5">
                <!-- Cancel Button -->
                <button 
                  @click="cancelLogout"
                  class="flex-1 bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:from-blue-600 hover:via-blue-700 hover:to-blue-800 text-white font-bold py-5 px-8 rounded-2xl transition-all duration-400 transform hover:scale-110 hover:shadow-2xl focus:outline-none focus:ring-4 focus:ring-blue-300 focus:ring-offset-3 shadow-2xl border-2 border-blue-400/50 relative overflow-hidden group"
                >
                  <div class="absolute inset-0 bg-gradient-to-r from-blue-400/30 to-blue-500/30 opacity-0 group-hover:opacity-100 transition-opacity duration-400"></div>
                  <div class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity duration-400"></div>
                  <div class="relative z-10 flex items-center justify-center">
                    <i class="fas fa-times mr-3 text-xl drop-shadow-lg"></i>
                    <span class="text-xl drop-shadow-lg font-bold">Cancel</span>
                  </div>
                </button>
                
                <!-- Logout Button -->
                <button 
                  @click="confirmLogout"
                  class="flex-1 bg-gradient-to-r from-red-500 via-red-600 to-red-700 hover:from-red-600 hover:via-red-700 hover:to-red-800 text-white font-bold py-5 px-8 rounded-2xl transition-all duration-400 transform hover:scale-110 hover:shadow-2xl focus:outline-none focus:ring-4 focus:ring-red-300 focus:ring-offset-3 shadow-2xl border-2 border-red-400/50 relative overflow-hidden group"
                >
                  <div class="absolute inset-0 bg-gradient-to-r from-red-400/30 to-orange-400/30 opacity-0 group-hover:opacity-100 transition-opacity duration-400"></div>
                  <div class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity duration-400"></div>
                  <div class="relative z-10 flex items-center justify-center">
                    <i class="fas fa-sign-out-alt mr-3 text-xl drop-shadow-lg"></i>
                    <span class="text-xl drop-shadow-lg font-bold">Logout</span>
                  </div>
                </button>
              </div>
              
              <!-- Additional Info -->
              <div class="mt-8 p-6 bg-gradient-to-r from-blue-100 via-blue-50 to-blue-100 rounded-2xl border-2 border-blue-300/60 shadow-lg">
                <div class="flex items-center justify-center text-blue-800">
                  <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center mr-3 shadow-lg">
                    <i class="fas fa-shield-alt text-white text-sm drop-shadow-sm"></i>
                  </div>
                  <span class="text-base font-bold drop-shadow-sm">Your session data will be securely cleared</span>
                </div>
              </div>
            </div>
          </div>
        </transition>
      </div>
    </transition>
  </header>
</template>

<script>
import { ref } from 'vue'
import { useAuth } from '@/composables/useAuth'
import MobileHamburgerMenu from './MobileHamburgerMenu.vue'

export default {
  name: 'AppHeader',
  components: {
    MobileHamburgerMenu
  },
  setup() {
    const { currentUser, logout } = useAuth()
    
    const showDropdown = ref(false)
    const showLogoutModal = ref(false)
    
    // Navigation items for desktop menu - REMOVED: Dashboard, Forms, Requests, Help
    const navigationItems = ref([
      // COMMENTED OUT: Navigation items removed as requested
      // {
      //   name: 'Dashboard',
      //   path: '/user-dashboard',
      //   icon: 'fas fa-tachometer-alt'
      // },
      // {
      //   name: 'Forms',
      //   path: '/forms',
      //   icon: 'fas fa-file-alt'
      // },
      // {
      //   name: 'Requests',
      //   path: '/requests',
      //   icon: 'fas fa-list-alt'
      // },
      // {
      //   name: 'Help',
      //   path: '/help',
      //   icon: 'fas fa-question-circle'
      // }
    ])
    
    return {
      currentUser,
      logout,
      showDropdown,
      showLogoutModal,
      navigationItems
    }
  },
  methods: {
    toggleDropdown() {
      this.showDropdown = !this.showDropdown
    },
    closeDropdown() {
      this.showDropdown = false
    },
    showLogoutConfirmation() {
      this.showLogoutModal = true
      this.showDropdown = false
    },
    cancelLogout() {
      this.showLogoutModal = false
    },
    async confirmLogout() {
      this.showLogoutModal = false
      try {
        await this.logout()
        this.$router.push('/')
      } catch (error) {
        console.error('Logout failed:', error)
      }
    }
  },
  mounted() {
    // Close dropdowns when clicking outside
    document.addEventListener('click', (e) => {
      if (!this.$el.contains(e.target)) {
        this.showDropdown = false
      }
    })

    // Handle escape key to close modals
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        this.showLogoutModal = false
        this.showDropdown = false
      }
    })
  }
}
</script>

<style scoped>
.text-primary {
  color: #1e40af;
}
.hover\:text-primary:hover {
  color: #1e40af;
}
.bg-primary {
  background-color: #1e40af;
}
.border-primary {
  border-color: #1e40af;
}
.focus-visible {
  @apply focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2;
}
</style>