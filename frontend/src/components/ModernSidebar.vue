<template>
  <aside
    v-if="isAuthenticated && !isLoading && userRole"
    class="h-screen flex flex-col transition-all duration-300 ease-in-out overflow-hidden relative bg-blue-600"
    :class="[
      isCollapsed ? 'w-16' : 'w-72'
    ]"
    aria-label="Sidebar navigation"
    style="background: linear-gradient(135deg, #3B82F6 0%, #2563EB 50%, #1D4ED8 100%);"
  >
    <!-- Decorative Border -->
    <div class="absolute inset-0 border-2 border-dashed border-white/20 rounded-2xl m-2"></div>

    <!-- Main Content Container -->
    <div class="relative z-10 flex flex-col h-full p-4">
      <!-- User Profile Section -->
      <div class="mb-6">
        <div v-if="!isCollapsed" class="flex items-center space-x-3">
          <!-- User Avatar -->
          <div class="relative">
            <div class="w-12 h-12 rounded-full bg-orange-500 flex items-center justify-center shadow-lg border-2 border-white">
              <span class="text-white font-bold text-lg">{{ userInitials }}</span>
            </div>
          </div>

          <!-- User Info -->
          <div class="flex-1 min-w-0">
            <h3 class="text-white font-bold text-sm truncate">
              {{ userName || 'JOHN DOE' }}
            </h3>
            <p class="text-blue-100 text-xs truncate">
              {{ getRoleDisplayName(userRole) }}
            </p>
          </div>
        </div>

        <!-- Collapsed User Avatar -->
        <div v-else class="flex justify-center">
          <div class="w-10 h-10 rounded-full bg-orange-500 flex items-center justify-center shadow-lg border-2 border-white">
            <span class="text-white font-bold text-sm">{{ userInitials }}</span>
          </div>
        </div>
      </div>

      <!-- Search Section -->
      <div v-if="!isCollapsed" class="mb-6">
        <div class="relative">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-4 w-4 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="SEARCH..."
            class="w-full pl-10 pr-3 py-3 text-sm rounded-lg bg-white/10 border border-white/20 text-white placeholder-blue-200 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-white/30 focus:bg-white/20 backdrop-blur-sm"
          />
        </div>
      </div>

      <!-- Collapsed Search Icon -->
      <div v-else class="mb-6 flex justify-center">
        <div class="w-10 h-10 rounded-lg bg-white/10 border border-white/20 flex items-center justify-center">
          <svg class="h-5 w-5 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
        </div>
      </div>

      <!-- Navigation Menu -->
      <nav class="flex-1 space-y-2 overflow-y-auto custom-scrollbar">
        <!-- Dashboard Items -->
        <div v-if="filteredDashboardItems.length > 0">
          <router-link
            v-for="item in filteredDashboardItems"
            :key="item.path"
            :to="item.path"
            class="group flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 relative"
            :class="[
              $route.path === item.path
                ? 'bg-white text-blue-600 shadow-lg'
                : 'text-white hover:bg-white/10 hover:text-white',
              isCollapsed ? 'justify-center' : ''
            ]"
            v-tooltip="isCollapsed ? item.displayName : ''"
          >
            <div
              class="flex items-center justify-center w-5 h-5 transition-colors duration-200"
              :class="isCollapsed ? '' : 'mr-3'"
            >
              <i :class="[item.icon, 'text-current']"></i>
            </div>
            <span v-if="!isCollapsed" class="truncate uppercase tracking-wide">{{ item.displayName.toUpperCase() }}</span>
          </router-link>
        </div>

        <!-- User Management Items (Admin only) -->
        <div v-if="filteredUserManagementItems.length > 0">
          <router-link
            v-for="item in filteredUserManagementItems"
            :key="item.path"
            :to="item.path"
            class="group flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 relative"
            :class="[
              $route.path === item.path
                ? 'bg-white text-blue-600 shadow-lg'
                : 'text-white hover:bg-white/10 hover:text-white',
              isCollapsed ? 'justify-center' : ''
            ]"
            v-tooltip="isCollapsed ? item.displayName : ''"
          >
            <div
              class="flex items-center justify-center w-5 h-5 transition-colors duration-200"
              :class="isCollapsed ? '' : 'mr-3'"
            >
              <i :class="[item.icon, 'text-current']"></i>
            </div>
            <span v-if="!isCollapsed" class="truncate uppercase tracking-wide">{{ item.displayName.toUpperCase() }}</span>
          </router-link>
        </div>

        <!-- Requests Management Items -->
        <div v-if="filteredRequestsManagementItems.length > 0">
          <router-link
            v-for="item in filteredRequestsManagementItems"
            :key="item.path"
            :to="item.path"
            class="group flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 relative"
            :class="[
              $route.path === item.path
                ? 'bg-white text-blue-600 shadow-lg'
                : 'text-white hover:bg-white/10 hover:text-white',
              isCollapsed ? 'justify-center' : ''
            ]"
            v-tooltip="isCollapsed ? item.displayName : ''"
          >
            <div
              class="flex items-center justify-center w-5 h-5 transition-colors duration-200"
              :class="isCollapsed ? '' : 'mr-3'"
            >
              <i :class="[item.icon, 'text-current']"></i>
            </div>
            <span v-if="!isCollapsed" class="truncate uppercase tracking-wide">{{ item.displayName.toUpperCase() }}</span>
          </router-link>
        </div>

        <!-- Device Management Items (ICT Officer only) -->
        <div v-if="filteredDeviceManagementItems.length > 0">
          <router-link
            v-for="item in filteredDeviceManagementItems"
            :key="item.path"
            :to="item.path"
            class="group flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 relative"
            :class="[
              $route.path === item.path
                ? 'bg-white text-blue-600 shadow-lg'
                : 'text-white hover:bg-white/10 hover:text-white',
              isCollapsed ? 'justify-center' : ''
            ]"
            v-tooltip="isCollapsed ? item.displayName : ''"
          >
            <div
              class="flex items-center justify-center w-5 h-5 transition-colors duration-200"
              :class="isCollapsed ? '' : 'mr-3'"
            >
              <i :class="[item.icon, 'text-current']"></i>
            </div>
            <span v-if="!isCollapsed" class="truncate uppercase tracking-wide">{{ item.displayName.toUpperCase() }}</span>
          </router-link>
        </div>


      </nav>

      <!-- Bottom Section -->
      <div class="space-y-2 mt-6">
        <!-- Help Center -->
        <button
          @click="showHelp"
          class="w-full flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 text-white hover:bg-white/10"
          :class="isCollapsed ? 'justify-center' : ''"
          v-tooltip="isCollapsed ? 'Help Center' : ''"
        >
          <div
            class="flex items-center justify-center w-5 h-5 transition-colors duration-200"
            :class="isCollapsed ? '' : 'mr-3'"
          >
            <svg class="w-5 h-5 text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <span v-if="!isCollapsed" class="truncate uppercase tracking-wide">HELP CENTER</span>
        </button>

        <!-- Logout -->
        <button
          @click="handleLogout"
          class="w-full flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 text-white hover:bg-white/10"
          :class="isCollapsed ? 'justify-center' : ''"
          v-tooltip="isCollapsed ? 'Log Out' : ''"
        >
          <div
            class="flex items-center justify-center w-5 h-5 transition-colors duration-200"
            :class="isCollapsed ? '' : 'mr-3'"
          >
            <svg class="w-5 h-5 text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
          </div>
          <span v-if="!isCollapsed" class="truncate uppercase tracking-wide">LOG OUT</span>
        </button>


      </div>

      <!-- Collapse Toggle Button -->
      <div class="mt-4 flex justify-center">
        <button
          @click="toggleCollapse"
          class="p-2 rounded-lg transition-all duration-200 hover:bg-white/10 text-white"
          :aria-label="isCollapsed ? 'Expand sidebar' : 'Collapse sidebar'"
        >
          <svg class="w-5 h-5 transition-transform duration-300" :class="{ 'rotate-180': isCollapsed }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
        </button>
      </div>
    </div>

    <!-- Help Modal -->
    <div
      v-if="showHelpModal"
      class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 backdrop-blur-sm"
      @click="showHelpModal = false"
    >
      <div
        class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 transform transition-all duration-300 scale-100 overflow-hidden"
        @click.stop
      >
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-6 text-center shadow-lg">
          <div class="w-16 h-16 bg-blue-500/30 rounded-full flex items-center justify-center mx-auto mb-4 backdrop-blur-sm border border-blue-300/40 shadow-lg">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <h3 class="text-xl font-bold text-white mb-2">Help & Support</h3>
        </div>

        <!-- Body -->
        <div class="p-6">
          <div class="space-y-4">
            <div class="flex items-center p-3 rounded-lg shadow-sm border bg-blue-50 border-blue-100">
              <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center mr-3 shadow-md">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
              </div>
              <div>
                <p class="font-medium text-gray-800">ICT Support</p>
                <p class="text-sm text-gray-600">+255 123 456 789</p>
              </div>
            </div>

            <div class="flex items-center p-3 rounded-lg shadow-sm border bg-blue-50 border-blue-100">
              <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center mr-3 shadow-md">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
              </div>
              <div>
                <p class="font-medium text-gray-800">Email Support</p>
                <p class="text-sm text-gray-600">ict@mnh.or.tz</p>
              </div>
            </div>

            <div class="flex items-center p-3 rounded-lg shadow-sm border bg-blue-50 border-blue-100">
              <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center mr-3 shadow-md">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
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
  </aside>
</template>

<script>
import { computed, ref, watch, onMounted, nextTick } from 'vue'
import { useRouter } from 'vue-router'
import { ROLE_PERMISSIONS, ROLES } from '../utils/permissions'
import { useAuth } from '../composables/useAuth'
import auth from '../utils/auth'
import { logoutGuard } from '@/utils/logoutGuard'

export default {
  name: 'ModernSidebar',
  props: {
    collapsed: {
      type: Boolean,
      default: false
    }
  },
  emits: ['update:collapsed'],
  setup(props, { emit }) {
    const router = useRouter()
    const { currentUser, userRole, logout, isAuthenticated, isLoading } = useAuth()

    // Local state
    const stableUserRole = ref(null)
    const searchQuery = ref('')
    const showHelpModal = ref(false)

    // Responsive behavior
    const isSmallScreen = ref(window.innerWidth < 768)

    // Watch for screen size changes
    const handleResize = () => {
      isSmallScreen.value = window.innerWidth < 768
      if (isSmallScreen.value && !isCollapsed.value) {
        emit('update:collapsed', true)
      }
    }

    onMounted(() => {
      window.addEventListener('resize', handleResize)
      handleResize() // Initial check
    })

    // Watch for authentication state changes
    watch([isAuthenticated, userRole], ([authenticated, role]) => {
      if (authenticated && role) {
        stableUserRole.value = role
      }
    }, { immediate: true })

    // Initialize auth state on mount
    onMounted(async() => {
      const token = localStorage.getItem('auth_token')
      const userData = localStorage.getItem('user_data')

      if (token && userData) {
        try {
          const user = JSON.parse(userData)
          if (user.role) {
            stableUserRole.value = user.role
          }
        } catch (error) {
          console.error('Failed to parse stored user data:', error)
        }

        if (!isAuthenticated.value && !isLoading.value) {
          await nextTick()
          auth.initializeAuth()
        }
      }

      if (isAuthenticated.value && userRole.value) {
        stableUserRole.value = userRole.value
      }
    })

    // Computed properties
    const isCollapsed = computed({
      get: () => props.collapsed,
      set: (value) => emit('update:collapsed', value)
    })

    const userName = computed(() => {
      return currentUser.value?.name || 'JOHN DOE'
    })

    const userInitials = computed(() => {
      const name = userName.value
      return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
    })

    // Get menu items based on stable user role
    const menuItems = computed(() => {
      const role = stableUserRole.value || userRole.value
      if (!role) return []

      const permissions = ROLE_PERMISSIONS[role]
      if (!permissions) return []

      return permissions.routes
        .map((route) => {
          const metadata = getRouteMetadata(route)
          return {
            path: route,
            ...metadata
          }
        })
        .filter((item) => item.name)
    })

    // Categorize menu items
    const dashboardItems = computed(() =>
      menuItems.value.filter((item) => item.category === 'dashboard')
    )

    const userManagementItems = computed(() =>
      menuItems.value.filter((item) => item.category === 'user-management')
    )

    const deviceManagementItems = computed(() =>
      menuItems.value.filter((item) => item.category === 'device-management')
    )

    const requestsManagementItems = computed(() =>
      menuItems.value.filter((item) => item.category === 'requests-management')
    )

    // Filtered items based on search
    const filteredDashboardItems = computed(() => {
      if (!searchQuery.value) return dashboardItems.value
      return dashboardItems.value.filter(item =>
        item.displayName.toLowerCase().includes(searchQuery.value.toLowerCase())
      )
    })

    const filteredUserManagementItems = computed(() => {
      if (!searchQuery.value) return userManagementItems.value
      return userManagementItems.value.filter(item =>
        item.displayName.toLowerCase().includes(searchQuery.value.toLowerCase())
      )
    })

    const filteredDeviceManagementItems = computed(() => {
      if (!searchQuery.value) return deviceManagementItems.value
      return deviceManagementItems.value.filter(item =>
        item.displayName.toLowerCase().includes(searchQuery.value.toLowerCase())
      )
    })

    const filteredRequestsManagementItems = computed(() => {
      if (!searchQuery.value) return requestsManagementItems.value
      return requestsManagementItems.value.filter(item =>
        item.displayName.toLowerCase().includes(searchQuery.value.toLowerCase())
      )
    })

    // Methods
    function toggleCollapse() {
      isCollapsed.value = !isCollapsed.value
    }


    function showHelp() {
      showHelpModal.value = true
    }

    function getRoleDisplayName(role) {
      const roleNames = {
        [ROLES.ADMIN]: 'Administrator',
        [ROLES.DIVISIONAL_DIRECTOR]: 'Divisional Director',
        [ROLES.HEAD_OF_DEPARTMENT]: 'Head of Department',
        [ROLES.ICT_DIRECTOR]: 'ICT Director',
        [ROLES.STAFF]: 'D. IN MEDICINE',
        [ROLES.ICT_OFFICER]: 'ICT Officer'
      }
      return roleNames[role] || role
    }

    async function handleLogout() {
      try {
        await logoutGuard.executeLogout(async() => {
          await logout()
        })
        router.push('/')
      } catch (error) {
        console.error('Logout failed:', error)
        router.push('/')
      }
    }

    function getRouteMetadata(route) {
      const metadata = {
        // Dashboards
        '/admin-dashboard': {
          name: 'AdminDashboard',
          displayName: 'Admin Dashboard',
          icon: 'fas fa-user-shield',
          category: 'dashboard',
          description: 'Administrative control panel'
        },
        '/user-dashboard': {
          name: 'UserDashboard',
          displayName: 'User Dashboard',
          icon: 'fas fa-home',
          category: 'dashboard',
          description: 'User portal and services'
        },
        '/dict-dashboard': {
          name: 'DictDashboard',
          displayName: 'ICT Director Dashboard',
          icon: 'fas fa-user-cog',
          category: 'dashboard',
          description: 'ICT Director control panel'
        },
        '/hod-dashboard': {
          name: 'HodDashboard',
          displayName: 'HOD Dashboard',
          icon: 'fas fa-user-tie',
          category: 'dashboard',
          description: 'Head of Department panel'
        },
        '/divisional-dashboard': {
          name: 'DivisionalDashboard',
          displayName: 'Divisional Dashboard',
          icon: 'fas fa-building',
          category: 'dashboard',
          description: 'Divisional Director panel'
        },
        '/ict-dashboard': {
          name: 'IctDashboard',
          displayName: 'ICT Dashboard',
          icon: 'fas fa-laptop-code',
          category: 'dashboard',
          description: 'ICT Officer panel'
        },

        // User Management (Admin only)
        '/jeeva-users': {
          name: 'JeevaUsers',
          displayName: 'Jeeva Users',
          icon: 'fas fa-file-medical',
          category: 'user-management',
          description: 'Manage Jeeva system users'
        },
        '/wellsoft-users': {
          name: 'WellsoftUsers',
          displayName: 'Wellsoft Users',
          icon: 'fas fa-laptop-medical',
          category: 'user-management',
          description: 'Manage Wellsoft system users'
        },
        '/internet-users': {
          name: 'InternetUsers',
          displayName: 'Internet Users',
          icon: 'fas fa-wifi',
          category: 'user-management',
          description: 'Manage internet access users'
        },

        // Device Management (ICT Officer only)
        '/ict-approval/requests': {
          name: 'RequestsList',
          displayName: 'Device Requests',
          icon: 'fas fa-clipboard-list',
          category: 'device-management',
          description: 'Manage device borrowing requests'
        },
        '/ict-approval/request/:id': {
          name: 'RequestDetails',
          displayName: 'Request Details',
          icon: 'fas fa-clipboard-check',
          category: 'device-management',
          description: 'View and assess device requests'
        },

        // Requests Management (for approvers)
        '/hod-dashboard/request-list': {
          name: 'HODDashboardRequestList',
          displayName: 'Access Requests',
          icon: 'fas fa-clipboard-check',
          category: 'requests-management',
          description: 'Review access requests'
        },
        '/internal-access/list': {
          name: 'InternalAccessList',
          displayName: 'Access Requests',
          icon: 'fas fa-clipboard-check',
          category: 'requests-management',
          description: 'Review access requests'
        },
        '/internal-access/details': {
          name: 'InternalAccessDetails',
          displayName: 'Request Details',
          icon: 'fas fa-file-signature',
          category: 'requests-management',
          description: 'Review and approve requests'
        }
      }

      return metadata[route] || {}
    }

    // Theme is automatically initialized by useTheme composable

    return {
      // State
      isCollapsed,
      searchQuery,
      showHelpModal,
      stableUserRole,

      // Computed
      currentUser,
      userRole,
      isAuthenticated,
      isLoading,
      userName,
      userInitials,
      dashboardItems,
      userManagementItems,
      deviceManagementItems,
      requestsManagementItems,
      filteredDashboardItems,
      filteredUserManagementItems,
      filteredDeviceManagementItems,
      filteredRequestsManagementItems,

      // Methods
      toggleCollapse,
      showHelp,
      getRoleDisplayName,
      handleLogout
    }
  },
  directives: {
    tooltip: {
      mounted(el, binding) {
        if (binding.value) {
          el.title = binding.value
        }
      },
      updated(el, binding) {
        if (binding.value) {
          el.title = binding.value
        } else {
          el.removeAttribute('title')
        }
      }
    }
  }
}
</script>

<style scoped>
/* Custom scrollbar */
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background-color: rgba(255, 255, 255, 0.3);
  border-radius: 2px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background-color: transparent;
}

/* Smooth transitions */
.transition-all {
  transition-property: all;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}

/* Backdrop blur support */
.backdrop-blur-sm {
  backdrop-filter: blur(4px);
  -webkit-backdrop-filter: blur(4px);
}

/* Focus styles */
.focus\:ring-2:focus {
  outline: 2px solid transparent;
  outline-offset: 2px;
  box-shadow: 0 0 0 2px var(--tw-ring-color);
}
</style>
