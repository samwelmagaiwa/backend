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

        <div class="max-w-full mx-auto relative z-10">
          <!-- Header Section -->
          <div
            class="medical-glass-card rounded-t-3xl p-6 mb-0 border-b border-blue-300/30"
          >
            <div class="flex justify-between items-center">
              <!-- Left Logo -->
              <div
                class="w-28 h-28 mr-6 transform hover:scale-110 transition-transform duration-300"
              >
                <div
                  class="w-full h-full bg-gradient-to-br from-blue-500/20 to-teal-500/20 rounded-2xl backdrop-blur-sm border-2 border-blue-300/40 flex items-center justify-center shadow-2xl hover:shadow-blue-500/25"
                >
                  <img
                    src="/assets/images/ngao2.png"
                    alt="National Shield"
                    class="max-w-18 max-h-18 object-contain"
                  />
                </div>
              </div>

              <!-- Center Content -->
              <div class="text-center flex-1">
                <h1
                  class="text-4xl font-bold text-white mb-2 tracking-wide drop-shadow-lg animate-fade-in"
                >
                  MUHIMBILI NATIONAL HOSPITAL
                </h1>
                <div class="relative inline-block mb-2">
                  <div
                    class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-10 py-4 rounded-full text-xl font-bold shadow-2xl transform hover:scale-105 transition-all duration-300 border-2 border-blue-400/60"
                  >
                    <span class="relative z-10 flex items-center gap-2">
                      <i class="fas fa-cog"></i>
                      ADMIN SETTINGS
                    </span>
                    <div
                      class="absolute inset-0 bg-gradient-to-r from-blue-700 to-blue-800 rounded-full opacity-0 hover:opacity-100 transition-opacity duration-300"
                    ></div>
                  </div>
                </div>
                <h2
                  class="text-2xl font-bold text-blue-100 tracking-wide drop-shadow-md animate-fade-in-delay"
                >
                  ONBOARDING RESET MANAGEMENT
                </h2>
              </div>

              <!-- Right Logo -->
              <div
                class="w-28 h-28 ml-6 transform hover:scale-110 transition-transform duration-300"
              >
                <div
                  class="w-full h-full bg-gradient-to-br from-teal-500/20 to-blue-500/20 rounded-2xl backdrop-blur-sm border-2 border-teal-300/40 flex items-center justify-center shadow-2xl hover:shadow-teal-500/25"
                >
                  <img
                    src="/assets/images/logo2.png"
                    alt="Muhimbili Logo"
                    class="max-w-18 max-h-18 object-contain"
                  />
                </div>
              </div>
            </div>
          </div>

          <!-- Main Content -->
          <div class="medical-glass-card rounded-b-3xl overflow-hidden">
            <div class="p-6 space-y-6">
              <!-- User Search Section -->
              <div
                class="medical-card bg-gradient-to-r from-blue-600/25 to-cyan-600/25 border-2 border-blue-400/40 p-6 rounded-2xl backdrop-blur-sm hover:shadow-2xl hover:shadow-blue-500/20 transition-all duration-500 group"
              >
                <div class="flex items-start space-x-4">
                  <div class="flex-shrink-0">
                    <div
                      class="w-14 h-14 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-blue-300/50"
                    >
                      <i class="fas fa-search text-white text-xl"></i>
                    </div>
                  </div>
                  <div class="flex-1">
                    <h3
                      class="text-xl font-bold text-white mb-4 flex items-center"
                    >
                      <i class="fas fa-user-search mr-2 text-blue-300"></i>
                      Search User
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <div>
                        <label
                          class="block text-sm font-bold text-blue-100 mb-2"
                        >
                          Search by Name or Email
                        </label>
                        <div class="relative">
                          <input
                            v-model="searchQuery"
                            type="text"
                            class="medical-input w-full px-4 py-3 bg-white/15 border-2 border-blue-300/30 rounded-xl focus:border-cyan-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-cyan-500/20"
                            placeholder="Enter user name or email"
                            @input="searchUsers"
                          />
                          <div
                            class="absolute inset-0 rounded-xl bg-gradient-to-r from-cyan-500/10 to-blue-500/10 opacity-0 hover:opacity-100 transition-opacity duration-300 pointer-events-none"
                          ></div>
                        </div>
                      </div>

                      <div>
                        <label
                          class="block text-sm font-bold text-blue-100 mb-2"
                        >
                          Filter by Role
                        </label>
                        <div class="relative">
                          <select
                            v-model="selectedRole"
                            class="medical-input w-full px-4 py-3 bg-white/15 border-2 border-blue-300/30 rounded-xl focus:border-cyan-400 focus:outline-none text-white backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-cyan-500/20 appearance-none cursor-pointer"
                            @change="searchUsers"
                          >
                            <option value="" class="bg-gray-800 text-white">
                              All Roles
                            </option>
                            <option
                              value="staff"
                              class="bg-gray-800 text-white"
                            >
                              Staff
                            </option>
                            <option
                              value="head_of_department"
                              class="bg-gray-800 text-white"
                            >
                              Head of Department
                            </option>

                            <option
                              value="ict_officer"
                              class="bg-gray-800 text-white"
                            >
                              ICT Officer
                            </option>
                            <option
                              value="ict_director"
                              class="bg-gray-800 text-white"
                            >
                              ICT Director
                            </option>
                            <option
                              value="divisional_director"
                              class="bg-gray-800 text-white"
                            >
                              Divisional Director
                            </option>
                          </select>
                          <div
                            class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none"
                          >
                            <i class="fas fa-chevron-down text-blue-300"></i>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Users List Section -->
              <div
                class="medical-card bg-gradient-to-r from-teal-600/25 to-blue-600/25 border-2 border-teal-400/40 p-6 rounded-2xl backdrop-blur-sm hover:shadow-2xl hover:shadow-teal-500/20 transition-all duration-500 group"
              >
                <div class="flex items-center space-x-4 mb-6">
                  <div
                    class="w-14 h-14 bg-gradient-to-br from-teal-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-teal-300/50"
                  >
                    <i class="fas fa-users text-white text-xl"></i>
                  </div>
                  <h3 class="text-xl font-bold text-white flex items-center">
                    <i class="fas fa-list mr-2 text-teal-300"></i>
                    Users ({{ filteredUsers.length }})
                  </h3>
                </div>

                <!-- Loading State -->
                <div v-if="loading" class="text-center py-8">
                  <div class="inline-flex items-center space-x-2 text-blue-100">
                    <i class="fas fa-spinner fa-spin text-xl"></i>
                    <span>Loading users...</span>
                  </div>
                </div>

                <!-- Error State -->
                <div v-else-if="error" class="text-center py-8">
                  <div class="bg-red-500/20 border border-red-400/40 rounded-xl p-6 backdrop-blur-sm">
                    <div class="text-red-100">
                      <i class="fas fa-exclamation-triangle text-4xl mb-4 opacity-75"></i>
                      <p class="text-lg font-semibold mb-2">Error Loading Users</p>
                      <p class="text-sm opacity-75">{{ error }}</p>
                      <button
                        @click="loadUsers(1)"
                        class="mt-4 bg-red-500 text-white py-2 px-4 rounded-lg hover:bg-red-600 transition-all duration-300"
                      >
                        <i class="fas fa-retry mr-2"></i>
                        Retry
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Users Grid -->
                <div
                  v-else-if="filteredUsers.length > 0"
                  class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4"
                >
                  <div
                    v-for="user in filteredUsers"
                    :key="user.id"
                    class="bg-white/15 p-4 rounded-xl backdrop-blur-sm border border-teal-300/30 hover:bg-white/20 transition-all duration-300 hover:shadow-lg hover:shadow-teal-500/20"
                  >
                    <div class="flex items-start justify-between mb-3">
                      <div class="flex-1">
                        <h4 class="font-bold text-white text-sm">
                          {{ user.name }}
                        </h4>
                        <p class="text-teal-100 text-xs">{{ user.email }}</p>
                        <p class="text-teal-200 text-xs capitalize">
                          {{ user.role ? user.role.replace("_", " ") : 'No Role' }}
                        </p>
                      </div>
                      <div class="flex flex-col space-y-1">
                        <div class="flex items-center space-x-1">
                          <span class="text-xs text-teal-100">Terms:</span>
                          <i
                            :class="[
                              user.onboarding_status?.terms_accepted
                                ? 'fas fa-check-circle text-green-400'
                                : 'fas fa-times-circle text-red-400',
                              'text-xs',
                            ]"
                          ></i>
                        </div>
                        <div class="flex items-center space-x-1">
                          <span class="text-xs text-teal-100">ICT:</span>
                          <i
                            :class="[
                              user.onboarding_status?.ict_policy_accepted
                                ? 'fas fa-check-circle text-green-400'
                                : 'fas fa-times-circle text-red-400',
                              'text-xs',
                            ]"
                          ></i>
                        </div>
                      </div>
                    </div>

                    <div class="flex space-x-2">
                      <button
                        @click="resetUserOnboarding(user, 'terms')"
                        :disabled="!user.onboarding_status?.terms_accepted"
                        class="flex-1 bg-red-500 text-white py-2 px-3 rounded-lg text-xs font-semibold hover:bg-red-600 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed transform hover:scale-105 shadow-lg"
                        :class="
                          !user.onboarding_status?.terms_accepted
                            ? 'opacity-50 cursor-not-allowed'
                            : ''
                        "
                      >
                        <i class="fas fa-undo mr-1"></i>
                        Reset Terms
                      </button>

                      <button
                        @click="resetUserOnboarding(user, 'ict')"
                        :disabled="!user.onboarding_status?.ict_policy_accepted"
                        class="flex-1 bg-gradient-to-r from-blue-500 to-blue-600 text-white py-2 px-3 rounded-lg text-xs font-semibold hover:from-blue-600 hover:to-blue-700 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed transform hover:scale-105 shadow-lg"
                        :class="
                          !user.onboarding_status?.ict_policy_accepted
                            ? 'opacity-50 cursor-not-allowed'
                            : ''
                        "
                      >
                        <i class="fas fa-undo mr-1"></i>
                        Reset ICT
                      </button>
                    </div>

                    <button
                      @click="resetUserOnboarding(user, 'both')"
                      :disabled="
                        !user.onboarding_status?.terms_accepted &&
                        !user.onboarding_status?.ict_policy_accepted
                      "
                      class="w-full mt-2 bg-blue-500 text-white py-2 px-3 rounded-lg text-xs font-semibold hover:bg-blue-600 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed transform hover:scale-105 shadow-lg"
                      :class="
                        !user.onboarding_status?.terms_accepted &&
                        !user.onboarding_status?.ict_policy_accepted
                          ? 'opacity-50 cursor-not-allowed'
                          : ''
                      "
                    >
                      <i class="fas fa-refresh mr-1"></i>
                      Reset Both
                    </button>
                  </div>
                </div>

                <!-- No Users Found -->
                <div v-else class="text-center py-8">
                  <div class="text-teal-100">
                    <i class="fas fa-users text-4xl mb-4 opacity-50"></i>
                    <p class="text-lg">No users found</p>
                    <p class="text-sm opacity-75">
                      Try adjusting your search criteria
                    </p>
                  </div>
                </div>

                <!-- Pagination -->
                <div v-if="totalPages > 1" class="mt-6 flex justify-center">
                  <div class="flex items-center space-x-2">
                    <button
                      @click="loadUsers(currentPage - 1)"
                      :disabled="currentPage <= 1"
                      class="px-3 py-2 bg-white/15 text-white rounded-lg hover:bg-white/20 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-300"
                    >
                      <i class="fas fa-chevron-left"></i>
                    </button>

                    <span class="px-4 py-2 text-white font-medium">
                      Page {{ currentPage }} of {{ totalPages }}
                    </span>

                    <button
                      @click="loadUsers(currentPage + 1)"
                      :disabled="currentPage >= totalPages"
                      class="px-3 py-2 bg-white/15 text-white rounded-lg hover:bg-white/20 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-300"
                    >
                      <i class="fas fa-chevron-right"></i>
                    </button>
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

    <!-- Confirmation Modal -->
    <div
      v-if="showConfirmModal"
      class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 backdrop-blur-sm"
    >
      <div
        class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 transform transition-all duration-300 scale-100 animate-slideUp overflow-hidden modal-shadow"
      >
        <!-- Header -->
        <div
          class="bg-gradient-to-r from-blue-500 to-blue-600 p-6 text-center header-shadow"
        >
          <div
            class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4 backdrop-blur-sm border border-white/30 icon-shadow"
          >
            <i
              class="fas fa-exclamation-triangle text-white text-2xl drop-shadow-lg"
            ></i>
          </div>
          <h3 class="text-xl font-bold text-white mb-2 drop-shadow-md">
            Confirm Reset
          </h3>
          <div
            class="w-12 h-1 bg-white/50 mx-auto rounded-full shadow-sm"
          ></div>
        </div>

        <!-- Body -->
        <div class="p-6 text-center body-shadow">
          <div class="mb-6">
            <div
              class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg user-icon-shadow"
            >
              <i class="fas fa-user text-blue-600 text-lg drop-shadow-sm"></i>
            </div>
            <h4 class="text-lg font-semibold text-gray-800 mb-3 drop-shadow-sm">
              {{ confirmData.userName }}
            </h4>
            <p class="text-gray-600 text-sm leading-relaxed">
              {{ confirmData.message }}
            </p>
          </div>

          <!-- Reset Type Indicator -->
          <div
            class="bg-blue-50 rounded-xl p-4 mb-6 border border-blue-100 shadow-md reset-indicator-shadow"
          >
            <div class="flex items-center justify-center space-x-4">
              <div
                v-if="
                  confirmData.type === 'terms' || confirmData.type === 'both'
                "
                class="flex items-center space-x-2"
              >
                <div
                  class="w-6 h-6 bg-orange-500 rounded-full flex items-center justify-center shadow-lg policy-icon-shadow"
                >
                  <i
                    class="fas fa-file-contract text-white text-xs drop-shadow-sm"
                  ></i>
                </div>
                <span class="text-sm font-medium text-gray-700 drop-shadow-sm"
                  >Terms of Service</span
                >
              </div>

              <div v-if="confirmData.type === 'both'" class="text-gray-400">
                <i class="fas fa-plus text-xs drop-shadow-sm"></i>
              </div>

              <div
                v-if="confirmData.type === 'ict' || confirmData.type === 'both'"
                class="flex items-center space-x-2"
              >
                <div
                  class="w-6 h-6 bg-purple-500 rounded-full flex items-center justify-center shadow-lg policy-icon-shadow"
                >
                  <i
                    class="fas fa-shield-alt text-white text-xs drop-shadow-sm"
                  ></i>
                </div>
                <span class="text-sm font-medium text-gray-700 drop-shadow-sm"
                  >ICT Policy</span
                >
              </div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex space-x-3">
            <button
              @click="cancelReset"
              class="flex-1 bg-gray-100 text-gray-700 py-3 px-4 rounded-xl font-medium hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2 transition-all duration-200 shadow-md hover:shadow-lg cancel-button-shadow"
            >
              <span class="drop-shadow-sm">Cancel</span>
            </button>

            <button
              @click="confirmReset"
              class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 text-white py-3 px-4 rounded-xl font-medium hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl reset-button-shadow"
            >
              <i class="fas fa-undo mr-2 drop-shadow-sm"></i>
              <span class="drop-shadow-sm">Reset</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, watch } from 'vue'
import Header from '@/components/header.vue'
import ModernSidebar from '@/components/ModernSidebar.vue'
import AppFooter from '@/components/footer.vue'
import { useAuth } from '@/composables/useAuth'
import { authAPI } from '@/utils/apiClient'

export default {
  name: 'OnboardingReset',
  components: {
    Header,
    ModernSidebar,
    AppFooter
  },
  setup() {
    const { ROLES, requireRole, isAuthenticated, userRole } = useAuth()

    // Local state
    // Sidebar state now managed by Pinia - no local state needed
    const searchQuery = ref('')
    const selectedRole = ref('')
    const users = ref([])
    const filteredUsers = ref([])
    const loading = ref(false)
    const showConfirmModal = ref(false)
    const confirmData = ref({})
    const pendingReset = ref(null)
    const error = ref(null)

    // Pagination state
    const currentPage = ref(1)
    const totalPages = ref(1)
    const totalUsers = ref(0)

    const loadUsers = async(page = 1) => {
      loading.value = true
      error.value = null

      try {
        console.log('🔍 Loading users for onboarding reset...', {
          page,
          isAuthenticated: isAuthenticated.value,
          userRole: userRole.value
        })

        // Check authentication first
        if (!isAuthenticated.value) {
          throw new Error('User not authenticated')
        }

        // Check admin role
        if (userRole.value !== 'admin') {
          throw new Error('Admin access required')
        }

        const params = {
          page: page,
          per_page: 20
        }

        // Add search filter
        if (searchQuery.value.trim()) {
          params.search = searchQuery.value.trim()
        }

        // Add role filter
        if (selectedRole.value) {
          params.role = selectedRole.value
        }

        console.log('📡 Making API call to get users...', params)
        const result = await authAPI.getUsers(params)
        console.log('📡 API response:', result)

        if (result.success) {
          // Transform users to ensure role property exists
          const transformedUsers = (result.data.users || []).map(user => ({
            ...user,
            role: user.role || user.primary_role || 'staff' // Fallback to primary_role or 'staff'
          }))
          
          users.value = transformedUsers
          filteredUsers.value = transformedUsers
          currentPage.value = result.data.pagination?.current_page || 1
          totalPages.value = result.data.pagination?.last_page || 1
          totalUsers.value = result.data.pagination?.total || 0
          console.log('✅ Users loaded successfully:', {
            count: users.value.length,
            total: totalUsers.value
          })
        } else {
          console.error('❌ Failed to load users:', result.error)
          error.value = result.error || 'Error loading users'
          showNotification(result.error || 'Error loading users', 'error')
          users.value = []
          filteredUsers.value = []
        }
      } catch (error) {
        console.error('❌ Error loading users:', error)
        error.value = error.message || 'Error loading users'
        showNotification(error.message || 'Error loading users', 'error')
        users.value = []
        filteredUsers.value = []
      } finally {
        loading.value = false
      }
    }

    const searchUsers = () => {
      // Reload users with new filters
      loadUsers(1)
    }

    // Watch for changes in search and role filters
    watch([searchQuery, selectedRole], () => {
      // Debounce the search
      clearTimeout(searchUsers.debounceTimer)
      searchUsers.debounceTimer = setTimeout(() => {
        searchUsers()
      }, 500)
    })

    const resetUserOnboarding = (user, type) => {
      // Map frontend type to backend type
      const typeMapping = {
        terms: 'terms',
        ict: 'ict',
        both: 'all'
      }

      const backendType = typeMapping[type] || 'all'

      // Prepare confirmation data
      const confirmMessage =
        type === 'both'
          ? 'Reset both Terms of Service and ICT Policy for this user?'
          : type === 'terms'
            ? 'Reset Terms of Service for this user?'
            : 'Reset ICT Policy for this user?'

      confirmData.value = {
        userName: user.name,
        message: confirmMessage,
        type: type
      }

      pendingReset.value = { user, type: backendType }
      showConfirmModal.value = true
    }

    const confirmReset = async() => {
      try {
        const { user, type } = pendingReset.value

        // Hide modal
        showConfirmModal.value = false

        // Call backend API
        const result = await authAPI.resetUserOnboarding(user.id, type)

        if (result.success) {
          // Reload users to get updated status
          await loadUsers(currentPage.value)

          const successMessage =
            type === 'all'
              ? `Successfully reset both Terms of Service and ICT Policy for ${user.name}`
              : type === 'terms'
                ? `Successfully reset Terms of Service for ${user.name}`
                : type === 'ict'
                  ? `Successfully reset ICT Policy for ${user.name}`
                  : `Successfully reset declaration for ${user.name}`

          showNotification(successMessage, 'success')
        } else {
          console.error('Failed to reset onboarding:', result.error)
          showNotification(
            result.error || 'Error resetting onboarding status',
            'error'
          )
        }
      } catch (error) {
        console.error('Error resetting onboarding:', error)
        showNotification('Error resetting onboarding status', 'error')
      } finally {
        // Clear pending reset
        pendingReset.value = null
      }
    }

    const cancelReset = () => {
      showConfirmModal.value = false
      pendingReset.value = null
    }

    const showNotification = (message, type = 'info') => {
      const colors = {
        success: 'from-green-500 to-emerald-500',
        error: 'from-red-500 to-rose-500',
        info: 'from-blue-500 to-cyan-500'
      }

      const toast = document.createElement('div')
      toast.className = `fixed top-4 right-4 bg-gradient-to-r ${colors[type]} text-white px-6 py-4 rounded-xl shadow-2xl z-50 max-w-md border border-white/20`
      toast.innerHTML = `
        <div class="flex items-center">
          <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center mr-3">
            <i class="fas ${
  type === 'success'
    ? 'fa-check'
    : type === 'error'
      ? 'fa-times'
      : 'fa-info'
} text-sm"></i>
          </div>
          <p class="font-medium">${message}</p>
        </div>
      `

      document.body.appendChild(toast)

      setTimeout(() => {
        if (document.body.contains(toast)) {
          document.body.removeChild(toast)
        }
      }, 4000)
    }

    onMounted(async() => {
      try {
        console.log('🚀 OnboardingReset component mounted')

        // Guard this route - only admins can access
        console.log('🔐 Checking admin role...')
        requireRole([ROLES.ADMIN])
        console.log('✅ Admin role verified')

        // Check for search parameter in URL
        const urlParams = new URLSearchParams(window.location.search)
        const searchParam = urlParams.get('search')
        if (searchParam) {
          searchQuery.value = searchParam
          console.log('🔍 Search parameter found:', searchParam)
        }

        console.log('📡 Loading users...')
        await loadUsers()

      } catch (error) {
        console.error('❌ Error in onMounted:', error)
        error.value = error.message || 'Failed to initialize component'
        showNotification(error.message || 'Failed to initialize component', 'error')
      }
    })

    // Add debounce timer property
    searchUsers.debounceTimer = null

    return {
      searchQuery,
      selectedRole,
      users,
      filteredUsers,
      loading,
      error,
      showConfirmModal,
      confirmData,
      currentPage,
      totalPages,
      totalUsers,
      searchUsers,
      resetUserOnboarding,
      confirmReset,
      cancelReset,
      loadUsers
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
  box-shadow: 0 8px 32px rgba(29, 78, 216, 0.4),
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

.animate-float {
  animation: float 6s ease-in-out infinite;
}

.animate-fade-in {
  animation: fade-in 1s ease-out;
}

.animate-fade-in-delay {
  animation: fade-in-delay 1s ease-out 0.3s both;
}

.animate-slideUp {
  animation: slideUp 0.3s ease-out;
}

/* Enhanced Shadow Effects */
.modal-shadow {
  box-shadow: 0 35px 60px -12px rgba(0, 0, 0, 0.35),
    0 25px 50px -12px rgba(59, 130, 246, 0.25),
    0 15px 30px -8px rgba(59, 130, 246, 0.15),
    0 8px 16px -4px rgba(0, 0, 0, 0.1), 0 0 0 1px rgba(255, 255, 255, 0.1),
    inset 0 2px 4px rgba(255, 255, 255, 0.15),
    inset 0 -1px 2px rgba(0, 0, 0, 0.05);
  transform: translateY(-4px);
}

.header-shadow {
  box-shadow: 0 8px 16px -4px rgba(0, 0, 0, 0.15),
    0 4px 8px -2px rgba(59, 130, 246, 0.2), 0 2px 4px -1px rgba(0, 0, 0, 0.1),
    inset 0 2px 4px rgba(255, 255, 255, 0.15),
    inset 0 -2px 4px rgba(0, 0, 0, 0.1);
  position: relative;
}

.header-shadow::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(
    135deg,
    rgba(255, 255, 255, 0.1) 0%,
    transparent 50%,
    rgba(0, 0, 0, 0.05) 100%
  );
  pointer-events: none;
  border-radius: inherit;
}

.icon-shadow {
  box-shadow: 0 16px 32px rgba(59, 130, 246, 0.4),
    0 12px 24px rgba(59, 130, 246, 0.3), 0 8px 16px rgba(0, 0, 0, 0.15),
    0 4px 8px rgba(0, 0, 0, 0.1), 0 0 0 2px rgba(255, 255, 255, 0.1),
    inset 0 2px 4px rgba(255, 255, 255, 0.25),
    inset 0 -2px 4px rgba(0, 0, 0, 0.1);
  animation: iconPulse 2s ease-in-out infinite;
}

@keyframes iconPulse {
  0%,
  100% {
    box-shadow: 0 16px 32px rgba(59, 130, 246, 0.4),
      0 12px 24px rgba(59, 130, 246, 0.3), 0 8px 16px rgba(0, 0, 0, 0.15),
      0 4px 8px rgba(0, 0, 0, 0.1), 0 0 0 2px rgba(255, 255, 255, 0.1),
      inset 0 2px 4px rgba(255, 255, 255, 0.25),
      inset 0 -2px 4px rgba(0, 0, 0, 0.1);
  }
  50% {
    box-shadow: 0 20px 40px rgba(59, 130, 246, 0.5),
      0 16px 32px rgba(59, 130, 246, 0.4), 0 12px 24px rgba(0, 0, 0, 0.2),
      0 8px 16px rgba(0, 0, 0, 0.15), 0 0 0 3px rgba(255, 255, 255, 0.15),
      inset 0 3px 6px rgba(255, 255, 255, 0.3),
      inset 0 -3px 6px rgba(0, 0, 0, 0.15);
  }
}

.body-shadow {
  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.1);
}

.user-icon-shadow {
  box-shadow: 0 8px 20px rgba(59, 130, 246, 0.35),
    0 6px 16px rgba(59, 130, 246, 0.25), 0 4px 12px rgba(0, 0, 0, 0.15),
    0 2px 4px rgba(0, 0, 0, 0.1), 0 0 0 1px rgba(59, 130, 246, 0.1),
    inset 0 1px 2px rgba(255, 255, 255, 0.2);
  transform: translateY(-2px);
  transition: all 0.3s ease;
}

.user-icon-shadow:hover {
  box-shadow: 0 12px 28px rgba(59, 130, 246, 0.4),
    0 8px 20px rgba(59, 130, 246, 0.3), 0 6px 16px rgba(0, 0, 0, 0.2),
    0 4px 8px rgba(0, 0, 0, 0.15), 0 0 0 2px rgba(59, 130, 246, 0.15),
    inset 0 2px 4px rgba(255, 255, 255, 0.25);
  transform: translateY(-4px);
}

.reset-indicator-shadow {
  box-shadow: 0 8px 16px -4px rgba(59, 130, 246, 0.2),
    0 6px 12px -2px rgba(59, 130, 246, 0.15), 0 4px 8px -1px rgba(0, 0, 0, 0.1),
    0 2px 4px -1px rgba(0, 0, 0, 0.06), 0 0 0 1px rgba(59, 130, 246, 0.1),
    inset 0 2px 4px rgba(255, 255, 255, 0.15),
    inset 0 -1px 2px rgba(0, 0, 0, 0.05);
  border: 1px solid rgba(59, 130, 246, 0.1);
  background: linear-gradient(
    135deg,
    rgba(255, 255, 255, 0.1) 0%,
    rgba(59, 130, 246, 0.05) 100%
  );
}

.policy-icon-shadow {
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2), 0 6px 12px rgba(0, 0, 0, 0.15),
    0 4px 8px rgba(0, 0, 0, 0.1), 0 2px 4px rgba(0, 0, 0, 0.05),
    0 0 0 1px rgba(255, 255, 255, 0.1), inset 0 1px 2px rgba(255, 255, 255, 0.2),
    inset 0 -1px 2px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
}

.policy-icon-shadow:hover {
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.25), 0 8px 16px rgba(0, 0, 0, 0.2),
    0 6px 12px rgba(0, 0, 0, 0.15), 0 4px 8px rgba(0, 0, 0, 0.1),
    0 0 0 2px rgba(255, 255, 255, 0.15),
    inset 0 2px 4px rgba(255, 255, 255, 0.25),
    inset 0 -2px 4px rgba(0, 0, 0, 0.15);
  transform: translateY(-2px);
}

.cancel-button-shadow {
  box-shadow: 0 8px 16px -4px rgba(0, 0, 0, 0.15),
    0 6px 12px -2px rgba(0, 0, 0, 0.1), 0 4px 8px -1px rgba(0, 0, 0, 0.08),
    0 2px 4px -1px rgba(0, 0, 0, 0.06), inset 0 1px 2px rgba(255, 255, 255, 0.1);
  transform: translateY(-1px);
}

.cancel-button-shadow:hover {
  box-shadow: 0 16px 24px -4px rgba(0, 0, 0, 0.2),
    0 12px 20px -2px rgba(0, 0, 0, 0.15), 0 8px 16px -1px rgba(0, 0, 0, 0.1),
    0 4px 8px -1px rgba(0, 0, 0, 0.08),
    inset 0 2px 4px rgba(255, 255, 255, 0.15);
  transform: translateY(-3px);
}

.reset-button-shadow {
  box-shadow: 0 16px 32px -8px rgba(59, 130, 246, 0.5),
    0 12px 24px -4px rgba(59, 130, 246, 0.4),
    0 8px 16px -2px rgba(59, 130, 246, 0.3), 0 4px 8px -1px rgba(0, 0, 0, 0.1),
    0 0 0 1px rgba(59, 130, 246, 0.2), inset 0 2px 4px rgba(255, 255, 255, 0.15),
    inset 0 -1px 2px rgba(0, 0, 0, 0.1);
  transform: translateY(-2px);
  position: relative;
  overflow: hidden;
}

.reset-button-shadow::before {
  content: "";
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(
    90deg,
    transparent,
    rgba(255, 255, 255, 0.2),
    transparent
  );
  transition: left 0.5s;
}

.reset-button-shadow:hover::before {
  left: 100%;
}

.reset-button-shadow:hover {
  box-shadow: 0 24px 48px -8px rgba(59, 130, 246, 0.6),
    0 20px 40px -4px rgba(59, 130, 246, 0.5),
    0 16px 32px -2px rgba(59, 130, 246, 0.4),
    0 8px 16px -1px rgba(0, 0, 0, 0.15), 0 0 0 2px rgba(59, 130, 246, 0.3),
    inset 0 3px 6px rgba(255, 255, 255, 0.2),
    inset 0 -2px 4px rgba(0, 0, 0, 0.15);
  transform: translateY(-4px) scale(1.02);
}
</style>
