<template>
  <div class="flex flex-col h-screen">
    <AppHeader />
    <div class="flex flex-1 overflow-hidden">
      <ModernSidebar />
      <main
        class="flex-1 p-6 bg-gradient-to-br from-blue-900 via-blue-800 to-teal-900 overflow-y-auto relative"
      >
        <!-- Background Pattern -->
        <div class="absolute inset-0 overflow-hidden">
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
        </div>

        <div class="max-w-10xl mx-auto relative z-10">
          <!-- Header Section -->
          <div class="medical-glass-card rounded-t-3xl p-6 mb-0 border-b border-blue-300/30">
            <div class="text-center">
              <h1 class="text-4xl font-bold text-white mb-2 tracking-wide drop-shadow-lg">
                <i class="fas fa-users-cog mr-3 text-blue-300"></i>
                User Role Assignment System
              </h1>
              <p class="text-blue-100 text-lg">
                Manage users and assign roles with department integration
              </p>
            </div>
          </div>

          <!-- Main Content -->
          <div class="medical-glass-card rounded-b-3xl overflow-hidden">
            <div class="p-6 space-y-8">
              <!-- Statistics Cards -->
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div
                  class="medical-card bg-gradient-to-r from-green-600/25 to-emerald-600/25 border-2 border-green-400/40 p-6 rounded-2xl backdrop-blur-sm"
                >
                  <div class="flex items-center space-x-3 mb-4">
                    <div
                      class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg"
                    >
                      <i class="fas fa-users text-white text-lg"></i>
                    </div>
                    <div>
                      <div class="text-2xl font-bold text-white">
                        {{ userStatistics.total_users || 0 }}
                      </div>
                      <div class="text-sm text-green-100">Total Users</div>
                    </div>
                  </div>
                </div>

                <div
                  class="medical-card bg-gradient-to-r from-blue-600/25 to-cyan-600/25 border-2 border-blue-400/40 p-6 rounded-2xl backdrop-blur-sm"
                >
                  <div class="flex items-center space-x-3 mb-4">
                    <div
                      class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg"
                    >
                      <i class="fas fa-user-check text-white text-lg"></i>
                    </div>
                    <div>
                      <div class="text-2xl font-bold text-white">
                        {{ userStatistics.users_with_roles || 0 }}
                      </div>
                      <div class="text-sm text-blue-100">Users with Roles</div>
                    </div>
                  </div>
                </div>

                <div
                  class="medical-card bg-gradient-to-r from-purple-600/25 to-indigo-600/25 border-2 border-purple-400/40 p-6 rounded-2xl backdrop-blur-sm"
                >
                  <div class="flex items-center space-x-3 mb-4">
                    <div
                      class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg"
                    >
                      <i class="fas fa-building text-white text-lg"></i>
                    </div>
                    <div>
                      <div class="text-2xl font-bold text-white">
                        {{ userStatistics.department_distribution?.length || 0 }}
                      </div>
                      <div class="text-sm text-purple-100">Departments</div>
                    </div>
                  </div>
                </div>

                <div
                  class="medical-card bg-gradient-to-r from-orange-600/25 to-red-600/25 border-2 border-orange-400/40 p-6 rounded-2xl backdrop-blur-sm"
                >
                  <div class="flex items-center space-x-3 mb-4">
                    <div
                      class="w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg"
                    >
                      <i class="fas fa-crown text-white text-lg"></i>
                    </div>
                    <div>
                      <div class="text-2xl font-bold text-white">
                        {{ userStatistics.hod_users || 0 }}
                      </div>
                      <div class="text-sm text-orange-100">HOD Users</div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- User Management Section -->
              <div
                class="medical-card bg-gradient-to-r from-teal-600/25 to-blue-600/25 border-2 border-teal-400/40 p-6 rounded-2xl backdrop-blur-sm"
              >
                <div class="flex items-center justify-between mb-6">
                  <div class="flex items-center space-x-4">
                    <div
                      class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg"
                    >
                      <i class="fas fa-users-cog text-white text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white flex items-center">
                      <i class="fas fa-list mr-2 text-blue-300"></i>
                      Users and Their Roles ({{ users.length || 0 }} total users)
                    </h3>
                  </div>
                  <div class="flex space-x-3">
                    <button
                      @click="refreshData"
                      :disabled="loading"
                      class="bg-gradient-to-r from-blue-500 to-blue-600 text-white py-3 px-6 rounded-xl font-semibold hover:from-blue-600 hover:to-blue-700 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed transform hover:scale-105 shadow-lg flex items-center space-x-2"
                    >
                      <i :class="loading ? 'fas fa-spinner fa-spin' : 'fas fa-refresh'"></i>
                      <span>{{ loading ? 'Loading...' : 'Refresh' }}</span>
                    </button>
                  </div>
                </div>

                <!-- Filters and Search -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                  <div>
                    <label class="block text-base font-bold text-teal-100 mb-2">Search Users</label>
                    <div class="relative">
                      <input
                        v-model="searchQuery"
                        type="text"
                        class="medical-input w-full px-4 py-3 bg-white/15 border-2 border-teal-300/30 rounded-xl focus:border-cyan-400 focus:outline-none text-white placeholder-teal-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-cyan-500/20"
                        placeholder="Search by name or email..."
                        @input="debouncedSearch"
                      />
                      <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <i class="fas fa-search text-teal-300"></i>
                      </div>
                    </div>
                  </div>

                  <div>
                    <label class="block text-base font-bold text-teal-100 mb-2"
                      >Filter by Role</label
                    >
                    <div class="relative">
                      <select
                        v-model="filterRole"
                        class="medical-input w-full px-4 py-3 bg-white/15 border-2 border-teal-300/30 rounded-xl focus:border-cyan-400 focus:outline-none text-white backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-cyan-500/20 appearance-none cursor-pointer"
                        @change="applyFilters"
                      >
                        <option value="" class="bg-gray-800 text-white">All Roles</option>
                        <option
                          v-for="role in availableRoles"
                          :key="role.id"
                          :value="role.name"
                          class="bg-gray-800 text-white"
                        >
                          {{ role.display_name }}
                        </option>
                      </select>
                      <div
                        class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none"
                      >
                        <i class="fas fa-chevron-down text-teal-300"></i>
                      </div>
                    </div>
                  </div>

                  <div>
                    <label class="block text-base font-bold text-teal-100 mb-2"
                      >Filter by Department</label
                    >
                    <div class="relative">
                      <select
                        v-model="filterDepartment"
                        class="medical-input w-full px-4 py-3 bg-white/15 border-2 border-teal-300/30 rounded-xl focus:border-cyan-400 focus:outline-none text-white backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-cyan-500/20 appearance-none cursor-pointer"
                        @change="applyFilters"
                      >
                        <option value="" class="bg-gray-800 text-white">All Departments</option>
                        <option
                          v-for="department in availableDepartments"
                          :key="department.id"
                          :value="department.id"
                          class="bg-gray-800 text-white"
                        >
                          {{ department.display_name }}
                        </option>
                      </select>
                      <div
                        class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none"
                      >
                        <i class="fas fa-chevron-down text-teal-300"></i>
                      </div>
                    </div>
                  </div>

                  <div>
                    <label class="block text-base font-bold text-teal-100 mb-2">Sort By</label>
                    <div class="relative">
                      <select
                        v-model="sortBy"
                        class="medical-input w-full px-4 py-3 bg-white/15 border-2 border-teal-300/30 rounded-xl focus:border-cyan-400 focus:outline-none text-white backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-cyan-500/20 appearance-none cursor-pointer"
                        @change="applyFilters"
                      >
                        <option value="name" class="bg-gray-800 text-white">Name</option>
                        <option value="email" class="bg-gray-800 text-white">Email</option>
                        <option value="created_at" class="bg-gray-800 text-white">
                          Created Date
                        </option>
                      </select>
                      <div
                        class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none"
                      >
                        <i class="fas fa-chevron-down text-teal-300"></i>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Users List -->
                <div v-if="loading" class="text-center py-8">
                  <div class="inline-flex items-center space-x-2 text-teal-100">
                    <i class="fas fa-spinner fa-spin text-xl"></i>
                    <span>Loading users...</span>
                  </div>
                </div>

                <div
                  v-else-if="filteredUsers.length > 0"
                  class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4"
                >
                  <div
                    v-for="user in filteredUsers"
                    :key="user.id"
                    class="bg-white/15 p-6 rounded-xl backdrop-blur-sm border border-teal-300/30 hover:bg-white/20 transition-all duration-300 hover:shadow-lg hover:shadow-teal-500/20 group"
                  >
                    <div class="flex items-start justify-between mb-4">
                      <div class="flex items-center space-x-3 flex-1">
                        <div
                          class="w-12 h-12 bg-gradient-to-br from-teal-500 to-blue-600 rounded-full flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300"
                        >
                          <span class="text-white font-bold text-sm">
                            {{ getInitials(user.name) }}
                          </span>
                        </div>
                        <div class="flex-1">
                          <h4 class="font-bold text-white text-lg">{{ user.name }}</h4>
                          <p class="text-teal-100 text-sm">{{ user.email }}</p>
                          <p v-if="user.pf_number" class="text-teal-200 text-xs">
                            PF: {{ user.pf_number }}
                          </p>
                          <p v-if="user.department" class="text-teal-200 text-xs">
                            <i class="fas fa-building mr-1"></i>{{ user.department.display_name }}
                          </p>
                        </div>
                      </div>
                    </div>

                    <!-- Current Roles -->
                    <div class="mb-4">
                      <div class="text-xs text-teal-100 mb-2">Current Roles:</div>
                      <div v-if="user.roles && user.roles.length > 0" class="flex flex-wrap gap-1">
                        <span
                          v-for="role in user.roles"
                          :key="role.id"
                          :class="[
                            'px-2 py-1 rounded text-xs border',
                            getRoleColorClasses(role.name)
                          ]"
                        >
                          {{ role.display_name }}
                        </span>
                      </div>
                      <div
                        v-else
                        class="px-2 py-1 bg-gray-500/30 text-gray-100 rounded text-xs border border-gray-400/50"
                      >
                        No roles assigned
                      </div>
                    </div>

                    <!-- HOD Status -->
                    <div class="mb-4" v-if="user.is_hod">
                      <div class="flex items-center space-x-2">
                        <div
                          class="w-6 h-6 bg-yellow-500 rounded-full flex items-center justify-center"
                        >
                          <i class="fas fa-crown text-white text-sm"></i>
                        </div>
                        <span class="text-yellow-100 text-sm font-medium">HOD Status</span>
                      </div>
                      <div
                        v-if="user.departments_as_hod && user.departments_as_hod.length > 0"
                        class="mt-2"
                      >
                        <div class="text-xs text-yellow-200 mb-1">Departments:</div>
                        <div class="flex flex-wrap gap-1">
                          <span
                            v-for="dept in user.departments_as_hod"
                            :key="dept.id"
                            class="px-2 py-1 bg-yellow-500/30 text-yellow-100 rounded text-xs border border-yellow-400/50"
                          >
                            {{ dept.name }}
                          </span>
                        </div>
                      </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex space-x-2">
                      <button
                        @click="openAssignRolesDialog(user)"
                        class="flex-1 bg-gradient-to-r from-blue-500 to-blue-600 text-white py-2 px-3 rounded-lg text-xs font-semibold hover:from-blue-600 hover:to-blue-700 transition-all duration-300 transform hover:scale-105 shadow-lg flex items-center justify-center space-x-1"
                      >
                        <i class="fas fa-user-edit"></i>
                        <span>Assign Roles</span>
                      </button>

                      <button
                        @click="viewRoleHistory(user)"
                        class="flex-1 bg-gradient-to-r from-red-500 to-red-600 text-white py-2 px-3 rounded-lg text-xs font-semibold hover:from-red-600 hover:to-red-700 transition-all duration-300 transform hover:scale-105 shadow-lg flex items-center justify-center space-x-1"
                      >
                        <i class="fas fa-history"></i>
                        <span>History</span>
                      </button>
                    </div>
                  </div>
                </div>

                <!-- No Users Found -->
                <div v-else class="text-center py-8">
                  <div class="text-teal-100">
                    <i class="fas fa-users text-4xl mb-4 opacity-50"></i>
                    <p class="text-lg">No users found</p>
                    <p class="text-sm opacity-75">
                      {{
                        searchQuery ? 'Try adjusting your search criteria' : 'No users available'
                      }}
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>

    <!-- Success/Error Toast Notifications -->
    <div
      v-if="showSnackbar"
      :class="[
        'fixed top-4 right-4 px-6 py-4 rounded-xl shadow-2xl z-50 max-w-md border border-white/20 transform transition-all duration-300',
        snackbarColor === 'success'
          ? 'bg-gradient-to-r from-green-500 to-emerald-500 text-white'
          : snackbarColor === 'error'
            ? 'bg-gradient-to-r from-red-500 to-rose-500 text-white'
            : 'bg-gradient-to-r from-blue-500 to-cyan-500 text-white'
      ]"
    >
      <div class="flex items-center">
        <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center mr-3">
          <i
            :class="[
              'text-sm',
              snackbarColor === 'success'
                ? 'fas fa-check'
                : snackbarColor === 'error'
                  ? 'fas fa-times'
                  : 'fas fa-info'
            ]"
          ></i>
        </div>
        <p class="font-medium flex-1">{{ snackbarMessage }}</p>
        <button
          @click="showSnackbar = false"
          class="ml-3 text-white/80 hover:text-white transition-colors duration-200"
        >
          <i class="fas fa-times"></i>
        </button>
      </div>
    </div>
  </div>
</template>

<script>
  import { ref, computed, onMounted } from 'vue'
  import { debounce } from 'lodash'
  import AppHeader from '@/components/AppHeader.vue'
  import ModernSidebar from '@/components/ModernSidebar.vue'
  import _roleAssignmentService from '@/services/roleAssignmentService'

  export default {
    name: 'UserRoleAssignment',
    components: {
      AppHeader,
      ModernSidebar
    },
    setup() {
      // Reactive data
      // Sidebar state now managed by Pinia - no local state needed
      const loading = ref(false)
      const users = ref([])
      const availableRoles = ref([])
      const availableDepartments = ref([])
      const userStatistics = ref({
        total_users: 0,
        users_with_roles: 0,
        department_distribution: [],
        hod_users: 0
      })

      // Filters
      const searchQuery = ref('')
      const filterRole = ref('')
      const filterDepartment = ref('')
      const sortBy = ref('name')

      // Dialog states - removed create user dialog

      // Snackbar
      const showSnackbar = ref(false)
      const snackbarMessage = ref('')
      const snackbarColor = ref('success')

      // Computed properties
      const filteredUsers = computed(() => {
        let filtered = [...users.value]

        // Apply search filter
        if (searchQuery.value) {
          const query = searchQuery.value.toLowerCase()
          filtered = filtered.filter(
            (user) =>
              user.name.toLowerCase().includes(query) ||
              user.email.toLowerCase().includes(query) ||
              (user.pf_number && user.pf_number.toLowerCase().includes(query))
          )
        }

        // Apply role filter
        if (filterRole.value) {
          filtered = filtered.filter(
            (user) => user.roles && user.roles.some((role) => role.name === filterRole.value)
          )
        }

        // Apply department filter
        if (filterDepartment.value) {
          filtered = filtered.filter((user) => user.department_id == filterDepartment.value)
        }

        // Apply sorting
        filtered.sort((a, b) => {
          const aValue = a[sortBy.value] || ''
          const bValue = b[sortBy.value] || ''
          return aValue.localeCompare(bValue)
        })

        return filtered
      })

      // Methods
      const initializeData = async () => {
        loading.value = true
        try {
          await Promise.all([fetchUsers(), fetchUserStatistics()])
        } catch (error) {
          console.error('Error initializing data:', error)
          showErrorMessage('Failed to load data')
        } finally {
          loading.value = false
        }
      }

      const fetchUsers = async () => {
        try {
          // TODO: Replace with appropriate service call for fetching users
          // const response = await userRoleService.getAllUsers()
          // For now, set empty array to avoid errors
          users.value = []
          console.log('Users fetching not implemented yet')
        } catch (error) {
          console.error('Error fetching users:', error)
          throw error
        }
      }

      const fetchUserStatistics = async () => {
        try {
          // TODO: Replace with appropriate service call for fetching statistics
          // const response = await userRoleService.getUserStatistics()
          // For now, set empty object to avoid errors
          userStatistics.value = {
            total_users: 0,
            users_with_roles: 0,
            department_distribution: [],
            hod_users: 0
          }
          console.log('User statistics fetching not implemented yet')
        } catch (error) {
          console.error('Error fetching user statistics:', error)
          throw error
        }
      }

      const refreshData = async () => {
        await initializeData()
      }

      const debouncedSearch = debounce(() => {
        // Search is handled by computed property
      }, 500)

      const applyFilters = () => {
        // Filters are handled by computed property
      }

      // Create user methods removed

      // Utility methods
      const getInitials = (name) => {
        return name
          .split(' ')
          .map((word) => word.charAt(0))
          .join('')
          .toUpperCase()
          .substring(0, 2)
      }

      const getRoleColorClasses = (roleName) => {
        const colorClasses = {
          admin: 'bg-red-500/30 text-red-100 border-red-400/50',
          head_of_department: 'bg-blue-500/30 text-blue-100 border-blue-400/50',
          ict_director: 'bg-teal-500/30 text-teal-100 border-teal-400/50',
          ict_officer: 'bg-green-500/30 text-green-100 border-green-400/50',
          divisional_director: 'bg-orange-500/30 text-orange-100 border-orange-400/50',
          staff: 'bg-gray-500/30 text-gray-100 border-gray-400/50'
        }
        return colorClasses[roleName] || 'bg-blue-500/30 text-blue-100 border-blue-400/50'
      }

      const _showSuccessMessage = (message) => {
        snackbarMessage.value = message
        snackbarColor.value = 'success'
        showSnackbar.value = true
        setTimeout(() => {
          showSnackbar.value = false
        }, 5000)
      }

      const showErrorMessage = (message) => {
        snackbarMessage.value = message
        snackbarColor.value = 'error'
        showSnackbar.value = true
        setTimeout(() => {
          showSnackbar.value = false
        }, 5000)
      }

      // Placeholder methods for role assignment and history
      const openAssignRolesDialog = (user) => {
        console.log('Open assign roles dialog for:', user)
        // TODO: Implement role assignment dialog
      }

      const viewRoleHistory = (user) => {
        console.log('View role history for:', user)
        // TODO: Implement role history dialog
      }

      // Initialize data on mount
      onMounted(() => {
        initializeData()
      })

      return {
        // Reactive data
        loading,
        users,
        availableRoles,
        availableDepartments,
        userStatistics,
        searchQuery,
        filterRole,
        filterDepartment,
        sortBy,
        showSnackbar,
        snackbarMessage,
        snackbarColor,

        // Computed
        filteredUsers,

        // Methods
        refreshData,
        debouncedSearch,
        applyFilters,
        getInitials,
        getRoleColorClasses,
        openAssignRolesDialog,
        viewRoleHistory
      }
    }
  }
</script>

<style scoped>
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

  .medical-input {
    position: relative;
    z-index: 1;
    color: white;
  }

  .medical-input::placeholder {
    color: rgba(191, 219, 254, 0.6);
  }

  .medical-input:focus {
    border-color: rgba(45, 212, 191, 0.8);
    box-shadow: 0 0 0 3px rgba(45, 212, 191, 0.2);
  }

  /* Select dropdown styling */
  select.medical-input {
    background-image: none;
  }

  select.medical-input option {
    background-color: #1f2937;
    color: white;
    padding: 8px;
  }

  /* Animations */
  @keyframes slideUp {
    from {
      opacity: 0;
      transform: translateY(20px) scale(0.95);
    }
    to {
      opacity: 1;
      transform: translateY(0) scale(1);
    }
  }

  .animate-slideUp {
    animation: slideUp 0.3s ease-out;
  }

  /* Custom scrollbar */
  ::-webkit-scrollbar {
    width: 8px;
  }

  ::-webkit-scrollbar-track {
    background: rgba(59, 130, 246, 0.1);
    border-radius: 4px;
  }

  ::-webkit-scrollbar-thumb {
    background: rgba(96, 165, 250, 0.5);
    border-radius: 4px;
  }

  ::-webkit-scrollbar-thumb:hover {
    background: rgba(96, 165, 250, 0.7);
  }
</style>
