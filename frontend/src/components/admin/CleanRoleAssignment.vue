<template>
  <div class="flex flex-col h-screen">
    <AppHeader />
    <div class="flex flex-1 overflow-hidden">
      <ModernSidebar />
      <main class="flex-1 p-6 bg-gradient-to-br from-blue-900 via-blue-800 to-teal-900 overflow-y-auto relative">
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
                Role Assignment System
              </h1>
              <p class="text-blue-100 text-lg">Assign roles to users from database</p>
            </div>
          </div>

          <!-- Main Content -->
          <div class="medical-glass-card rounded-b-3xl overflow-hidden">
            <div class="p-6 space-y-6">

              <!-- Statistics Cards -->
              <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="medical-card bg-gradient-to-r from-blue-600/25 to-cyan-600/25 border-2 border-blue-400/40 p-4 rounded-xl backdrop-blur-sm">
                  <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                      <i class="fas fa-users text-white text-lg"></i>
                    </div>
                    <div>
                      <div class="text-2xl font-bold text-white">{{ statistics.total_users || 0 }}</div>
                      <div class="text-sm text-blue-100">Total Users</div>
                    </div>
                  </div>
                </div>

                <div class="medical-card bg-gradient-to-r from-green-600/25 to-emerald-600/25 border-2 border-green-400/40 p-4 rounded-xl backdrop-blur-sm">
                  <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center">
                      <i class="fas fa-user-check text-white text-lg"></i>
                    </div>
                    <div>
                      <div class="text-2xl font-bold text-white">{{ statistics.users_with_roles || 0 }}</div>
                      <div class="text-sm text-green-100">With Roles</div>
                    </div>
                  </div>
                </div>

                <div class="medical-card bg-gradient-to-r from-purple-600/25 to-indigo-600/25 border-2 border-purple-400/40 p-4 rounded-xl backdrop-blur-sm">
                  <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center">
                      <i class="fas fa-shield-alt text-white text-lg"></i>
                    </div>
                    <div>
                      <div class="text-2xl font-bold text-white">{{ availableRoles.length || 0 }}</div>
                      <div class="text-sm text-purple-100">Available Roles</div>
                    </div>
                  </div>
                </div>

                <div class="medical-card bg-gradient-to-r from-orange-600/25 to-red-600/25 border-2 border-orange-400/40 p-4 rounded-xl backdrop-blur-sm">
                  <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center">
                      <i class="fas fa-history text-white text-lg"></i>
                    </div>
                    <div>
                      <div class="text-2xl font-bold text-white">{{ statistics.recent_role_changes || 0 }}</div>
                      <div class="text-sm text-orange-100">Recent Changes</div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- User Selection and Role Assignment -->
              <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- User Selection Panel -->
                <div class="medical-card bg-gradient-to-r from-teal-600/25 to-blue-600/25 border-2 border-teal-400/40 p-6 rounded-2xl backdrop-blur-sm">
                  <div class="flex items-center space-x-3 mb-6">
                    <div class="w-12 h-12 bg-gradient-to-br from-teal-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg">
                      <i class="fas fa-user-friends text-white text-lg"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white">Select User</h3>
                  </div>

                  <!-- Search Users -->
                  <div class="mb-4">
                    <label class="block text-sm font-bold text-teal-100 mb-2">Search Users</label>
                    <div class="relative">
                      <input
                        v-model="userSearchQuery"
                        type="text"
                        class="w-full px-4 py-3 bg-white/15 border-2 border-teal-300/30 rounded-xl focus:border-cyan-400 focus:outline-none text-white placeholder-teal-200/60 backdrop-blur-sm"
                        placeholder="Search by name or email..."
                        @input="searchUsers"
                      />
                      <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                        <i class="fas fa-search text-teal-300"></i>
                      </div>
                    </div>
                  </div>

                  <!-- Users List -->
                  <div class="space-y-2 max-h-96 overflow-y-auto">
                    <div v-if="loadingUsers" class="text-center py-4">
                      <i class="fas fa-spinner fa-spin text-teal-300 text-xl"></i>
                      <p class="text-teal-100 mt-2">Loading users...</p>
                    </div>

                    <div v-else-if="filteredUsers.length === 0" class="text-center py-4">
                      <i class="fas fa-users text-teal-300 text-2xl opacity-50"></i>
                      <p class="text-teal-100 mt-2">No users found</p>
                    </div>

                    <div
                      v-for="user in filteredUsers"
                      :key="user.id"
                      @click="selectUser(user)"
                      :class="[
                        'p-4 rounded-lg cursor-pointer transition-all duration-200 border',
                        selectedUser?.id === user.id
                          ? 'bg-white/25 border-cyan-400/60 shadow-lg'
                          : 'bg-white/10 border-teal-300/30 hover:bg-white/20 hover:border-cyan-400/40'
                      ]"
                    >
                      <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-teal-500 to-blue-600 rounded-full flex items-center justify-center">
                          <span class="text-white font-bold text-sm">{{ getInitials(user.name) }}</span>
                        </div>
                        <div class="flex-1">
                          <h4 class="font-bold text-white">{{ user.name }}</h4>
                          <p class="text-teal-100 text-sm">{{ user.email }}</p>
                          <div v-if="user.roles && user.roles.length > 0" class="flex flex-wrap gap-1 mt-1">
                            <span
                              v-for="role in user.roles.slice(0, 2)"
                              :key="role.id"
                              class="px-2 py-1 bg-teal-500/30 text-teal-100 rounded text-xs"
                            >
                              {{ role.name }}
                            </span>
                            <span v-if="user.roles.length > 2" class="px-2 py-1 bg-gray-500/30 text-gray-100 rounded text-xs">
                              +{{ user.roles.length - 2 }} more
                            </span>
                          </div>
                          <div v-else class="text-xs text-teal-200 mt-1">No roles assigned</div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Role Assignment Panel -->
                <div class="medical-card bg-gradient-to-r from-purple-600/25 to-indigo-600/25 border-2 border-purple-400/40 p-6 rounded-2xl backdrop-blur-sm">
                  <div class="flex items-center space-x-3 mb-6">
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                      <i class="fas fa-user-tag text-white text-lg"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white">Assign Roles</h3>
                  </div>

                  <div v-if="!selectedUser" class="text-center py-8">
                    <i class="fas fa-user-plus text-purple-300 text-3xl opacity-50"></i>
                    <p class="text-purple-100 mt-2">Select a user to assign roles</p>
                  </div>

                  <div v-else class="space-y-6">
                    <!-- Selected User Info -->
                    <div class="bg-white/10 rounded-lg p-4 border border-purple-300/30">
                      <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-full flex items-center justify-center">
                          <span class="text-white font-bold">{{ getInitials(selectedUser.name) }}</span>
                        </div>
                        <div>
                          <h4 class="font-bold text-white">{{ selectedUser.name }}</h4>
                          <p class="text-purple-100 text-sm">{{ selectedUser.email }}</p>
                        </div>
                      </div>
                    </div>

                    <!-- Role Selection Dropdown -->
                    <div class="space-y-4">
                      <div>
                        <label class="block text-sm font-bold text-purple-100 mb-2">
                          <i class="fas fa-list mr-2"></i>Select Roles
                        </label>
                        <div class="relative">
                          <select
                            v-model="selectedRoleToAdd"
                            @change="addRoleToSelection"
                            class="w-full px-4 py-3 bg-white/15 border-2 border-purple-300/30 rounded-xl focus:border-purple-400 focus:outline-none text-white backdrop-blur-sm appearance-none cursor-pointer"
                          >
                            <option value="" class="bg-gray-800 text-white">Choose a role to add...</option>
                            <option
                              v-for="role in availableRolesForDropdown"
                              :key="role.id"
                              :value="role.id"
                              class="bg-gray-800 text-white"
                            >
                              {{ role.name }} {{ role.description ? '- ' + role.description : '' }}
                            </option>
                          </select>
                          <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                            <i class="fas fa-chevron-down text-purple-300"></i>
                          </div>
                        </div>
                        <p class="text-xs text-purple-200 mt-1">
                          <i class="fas fa-info-circle mr-1"></i>
                          Select roles from the dropdown to assign to the user
                        </p>
                      </div>

                      <!-- Alternative: Show Roles List -->
                      <div>
                        <div class="flex items-center justify-between mb-2">
                          <label class="text-sm font-bold text-purple-100">
                            <i class="fas fa-check-square mr-2"></i>Or select from list
                          </label>
                          <button
                            @click="showRolesList = !showRolesList"
                            class="text-sm text-purple-300 hover:text-purple-100 font-medium flex items-center space-x-1"
                          >
                            <span>{{ showRolesList ? 'Hide' : 'Show' }} roles list</span>
                            <i :class="showRolesList ? 'fas fa-chevron-up' : 'fas fa-chevron-down'"></i>
                          </button>
                        </div>

                        <div v-if="showRolesList" class="bg-white/10 border-2 border-purple-300/30 rounded-xl p-4 max-h-64 overflow-y-auto">
                          <div class="space-y-2">
                            <div
                              v-for="role in availableRoles"
                              :key="role.id"
                              class="flex items-center space-x-3 p-2 bg-white/10 rounded-lg hover:bg-white/20 transition-colors duration-200"
                            >
                              <input
                                v-model="selectedRoleIds"
                                :value="role.id"
                                type="checkbox"
                                class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
                              />
                              <div class="flex-1">
                                <div class="flex items-center space-x-2">
                                  <span :class="['px-2 py-1 rounded text-xs', getRoleColorClasses(role.name)]">
                                    {{ role.name }}
                                  </span>
                                </div>
                                <p v-if="role.description" class="text-xs text-purple-200 mt-1">
                                  {{ role.description }}
                                </p>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>

                      <!-- Selected Roles Preview -->
                      <div v-if="selectedRoleIds.length > 0">
                        <label class="block text-sm font-bold text-purple-100 mb-2">
                          <i class="fas fa-check mr-2"></i>Selected Roles ({{ selectedRoleIds.length }}):
                        </label>
                        <div class="flex flex-wrap gap-2">
                          <span
                            v-for="roleId in selectedRoleIds"
                            :key="roleId"
                            :class="['px-3 py-2 rounded-lg text-sm flex items-center space-x-2', getRoleColorClasses(getRoleName(roleId))]"
                          >
                            <span>{{ getRoleName(roleId) }}</span>
                            <button
                              @click="removeRoleFromSelection(roleId)"
                              class="text-current hover:text-red-300 transition-colors duration-200"
                            >
                              <i class="fas fa-times text-xs"></i>
                            </button>
                          </span>
                        </div>
                      </div>

                      <!-- Current User Roles -->
                      <div v-if="selectedUser.roles && selectedUser.roles.length > 0">
                        <label class="block text-sm font-bold text-purple-100 mb-2">
                          <i class="fas fa-user-shield mr-2"></i>Current Roles:
                        </label>
                        <div class="flex flex-wrap gap-2">
                          <span
                            v-for="role in selectedUser.roles"
                            :key="role.id"
                            :class="['px-3 py-2 rounded-lg text-sm flex items-center space-x-2', getRoleColorClasses(role.name)]"
                          >
                            <span>{{ role.name }}</span>
                            <button
                              @click="quickRemoveRole(role.id)"
                              class="text-current hover:text-red-300 transition-colors duration-200"
                              title="Remove this role"
                            >
                              <i class="fas fa-times text-xs"></i>
                            </button>
                          </span>
                        </div>
                      </div>

                      <!-- Action Buttons -->
                      <div class="flex space-x-3 pt-4">
                        <button
                          @click="assignRoles"
                          :disabled="assigningRoles || selectedRoleIds.length === 0"
                          class="flex-1 bg-gradient-to-r from-purple-600 to-purple-700 text-white py-3 px-6 rounded-xl font-semibold hover:from-purple-700 hover:to-purple-800 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center space-x-2"
                        >
                          <i v-if="assigningRoles" class="fas fa-spinner fa-spin"></i>
                          <i v-else class="fas fa-save"></i>
                          <span>{{ assigningRoles ? 'Updating...' : 'Update Roles' }}</span>
                        </button>

                        <button
                          @click="viewRoleHistory"
                          :disabled="!selectedUser"
                          class="bg-gradient-to-r from-gray-600 to-gray-700 text-white py-3 px-6 rounded-xl font-semibold hover:from-gray-700 hover:to-gray-800 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed flex items-center space-x-2"
                        >
                          <i class="fas fa-history"></i>
                          <span>History</span>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Role History Modal -->
              <div
                v-if="showRoleHistory"
                class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 backdrop-blur-sm"
              >
                <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
                  <!-- Header -->
                  <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-6 text-center">
                    <h3 class="text-xl font-bold text-white mb-2">
                      Role History for {{ selectedUser?.name }}
                    </h3>
                  </div>

                  <!-- Body -->
                  <div class="p-6">
                    <div v-if="loadingHistory" class="text-center py-8">
                      <i class="fas fa-spinner fa-spin text-blue-500 text-2xl"></i>
                      <p class="text-gray-600 mt-2">Loading role history...</p>
                    </div>

                    <div v-else-if="roleHistory.length > 0" class="space-y-4">
                      <div
                        v-for="(change, index) in roleHistory"
                        :key="index"
                        class="bg-gray-50 rounded-xl p-4 border border-gray-200"
                      >
                        <div class="flex items-start space-x-4">
                          <div
                            :class="[
                              'w-10 h-10 rounded-full flex items-center justify-center',
                              change.action === 'assigned' ? 'bg-green-500' : 'bg-red-500'
                            ]"
                          >
                            <i
                              :class="[
                                'text-white text-sm',
                                change.action === 'assigned' ? 'fas fa-plus' : 'fas fa-minus'
                              ]"
                            ></i>
                          </div>
                          <div class="flex-1">
                            <div class="flex items-center justify-between mb-2">
                              <h4 class="font-semibold text-gray-800">
                                Role {{ change.action === 'assigned' ? 'Assigned' : 'Removed' }}
                              </h4>
                              <span class="text-sm text-gray-500">
                                {{ formatDateTime(change.changed_at) }}
                              </span>
                            </div>
                            <div class="mb-2">
                              <span :class="['px-3 py-1 rounded-full text-sm', getRoleColorClasses(change.role?.name)]">
                                {{ change.role?.name }}
                              </span>
                            </div>
                            <div v-if="change.changed_by" class="text-sm text-gray-600">
                              <i class="fas fa-user text-gray-400 mr-1"></i>
                              Changed by: {{ change.changed_by?.name }}
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div v-else class="text-center py-8">
                      <i class="fas fa-history text-gray-400 text-3xl"></i>
                      <h4 class="text-lg font-semibold text-gray-800 mb-2 mt-4">No role history</h4>
                      <p class="text-gray-600">This user has no role change history.</p>
                    </div>
                  </div>

                  <!-- Footer -->
                  <div class="flex justify-end p-6 border-t border-gray-200">
                    <button
                      @click="showRoleHistory = false"
                      class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-medium hover:bg-gray-200 transition-colors duration-200"
                    >
                      Close
                    </button>
                  </div>
                </div>
              </div>

              <!-- Success/Error Messages -->
              <div
                v-if="showMessage"
                :class="[
                  'fixed top-4 right-4 px-6 py-4 rounded-xl shadow-2xl z-50 max-w-md transform transition-all duration-300',
                  messageType === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
                ]"
              >
                <div class="flex items-center">
                  <i
                    :class="[
                      'mr-3',
                      messageType === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle'
                    ]"
                  ></i>
                  <p class="font-medium">{{ message }}</p>
                  <button
                    @click="showMessage = false"
                    class="ml-3 text-white/80 hover:text-white transition-colors duration-200"
                  >
                    <i class="fas fa-times"></i>
                  </button>
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
import { ref, computed, onMounted } from 'vue'
import AppHeader from '@/components/AppHeader.vue'
import ModernSidebar from '@/components/ModernSidebar.vue'
import roleAssignmentService from '@/services/roleAssignmentService'

export default {
  name: 'CleanRoleAssignment',
  components: {
    AppHeader,
    ModernSidebar
  },
  setup() {
    // Reactive data
    // Sidebar state now managed by Pinia - no local state needed
    const availableRoles = ref([])
    const allUsers = ref([])
    const selectedUser = ref(null)
    const selectedRoleIds = ref([])
    const selectedRoleToAdd = ref('')
    const userSearchQuery = ref('')
    const showRolesList = ref(false)
    const showRoleHistory = ref(false)
    const roleHistory = ref([])
    const statistics = ref({})

    // Loading states
    const loadingRoles = ref(false)
    const loadingUsers = ref(false)
    const assigningRoles = ref(false)
    const loadingHistory = ref(false)

    // Message system
    const showMessage = ref(false)
    const message = ref('')
    const messageType = ref('success')

    // Computed properties
    const filteredUsers = computed(() => {
      if (!userSearchQuery.value) return allUsers.value
      const query = userSearchQuery.value.toLowerCase()
      return allUsers.value.filter(user =>
        user.name.toLowerCase().includes(query) ||
        user.email.toLowerCase().includes(query)
      )
    })

    const availableRolesForDropdown = computed(() => {
      return availableRoles.value.filter(role =>
        !selectedRoleIds.value.includes(role.id)
      )
    })

    // Methods
    const loadRoles = async() => {
      loadingRoles.value = true
      try {
        const result = await roleAssignmentService.getAllRoles()
        if (result.success) {
          availableRoles.value = result.data.data || []
        } else {
          showErrorMessage(result.message)
        }
      } catch (error) {
        console.error('Error loading roles:', error)
        showErrorMessage('Failed to load roles')
      } finally {
        loadingRoles.value = false
      }
    }

    const loadUsers = async() => {
      loadingUsers.value = true
      try {
        const result = await roleAssignmentService.getUsersWithRoles()
        if (result.success) {
          allUsers.value = result.data.data || []
        } else {
          showErrorMessage(result.message)
        }
      } catch (error) {
        console.error('Error loading users:', error)
        showErrorMessage('Failed to load users')
      } finally {
        loadingUsers.value = false
      }
    }

    const loadStatistics = async() => {
      try {
        const result = await roleAssignmentService.getRoleStatistics()
        if (result.success) {
          statistics.value = result.data
        }
      } catch (error) {
        console.error('Error loading statistics:', error)
      }
    }

    const selectUser = (user) => {
      selectedUser.value = user
      selectedRoleIds.value = user.roles ? user.roles.map(role => role.id) : []
    }

    const addRoleToSelection = () => {
      if (selectedRoleToAdd.value && !selectedRoleIds.value.includes(selectedRoleToAdd.value)) {
        selectedRoleIds.value.push(selectedRoleToAdd.value)
        selectedRoleToAdd.value = ''
      }
    }

    const removeRoleFromSelection = (roleId) => {
      selectedRoleIds.value = selectedRoleIds.value.filter(id => id !== roleId)
    }

    const assignRoles = async() => {
      if (!selectedUser.value || selectedRoleIds.value.length === 0) return

      assigningRoles.value = true
      try {
        const result = await roleAssignmentService.assignRolesToUser(
          selectedUser.value.id,
          selectedRoleIds.value
        )

        if (result.success) {
          showSuccessMessage(result.message || 'Roles updated successfully')
          await loadUsers()
          await loadStatistics()

          // Update selected user
          const updatedUser = allUsers.value.find(u => u.id === selectedUser.value.id)
          if (updatedUser) {
            selectedUser.value = updatedUser
            selectedRoleIds.value = updatedUser.roles ? updatedUser.roles.map(role => role.id) : []
          }
        } else {
          showErrorMessage(result.message)
        }
      } catch (error) {
        console.error('Error assigning roles:', error)
        showErrorMessage('Failed to assign roles')
      } finally {
        assigningRoles.value = false
      }
    }

    const quickRemoveRole = async(roleId) => {
      if (!selectedUser.value || !confirm('Are you sure you want to remove this role?')) return

      try {
        const result = await roleAssignmentService.removeRoleFromUser(
          selectedUser.value.id,
          roleId
        )

        if (result.success) {
          showSuccessMessage(result.message || 'Role removed successfully')
          await loadUsers()
          await loadStatistics()

          // Update selected user
          const updatedUser = allUsers.value.find(u => u.id === selectedUser.value.id)
          if (updatedUser) {
            selectedUser.value = updatedUser
            selectedRoleIds.value = updatedUser.roles ? updatedUser.roles.map(role => role.id) : []
          }
        } else {
          showErrorMessage(result.message)
        }
      } catch (error) {
        console.error('Error removing role:', error)
        showErrorMessage('Failed to remove role')
      }
    }

    const viewRoleHistory = async() => {
      if (!selectedUser.value) return

      showRoleHistory.value = true
      loadingHistory.value = true

      try {
        const result = await roleAssignmentService.getUserRoleHistory(selectedUser.value.id)
        if (result.success) {
          roleHistory.value = result.data.data || []
        } else {
          showErrorMessage(result.message)
          roleHistory.value = []
        }
      } catch (error) {
        console.error('Error loading role history:', error)
        showErrorMessage('Failed to load role history')
        roleHistory.value = []
      } finally {
        loadingHistory.value = false
      }
    }

    const searchUsers = () => {
      // Debounced search is handled by the computed property
    }

    // Utility methods
    const getInitials = (name) => {
      return name
        .split(' ')
        .map(word => word.charAt(0))
        .join('')
        .toUpperCase()
        .substring(0, 2)
    }

    const getRoleName = (roleId) => {
      const role = availableRoles.value.find(r => r.id === roleId)
      return role ? role.name : 'Unknown'
    }

    const getRoleColorClasses = (roleName) => {
      const colorClasses = {
        admin: 'bg-red-500/30 text-red-100 border border-red-400/50',
        head_of_department: 'bg-blue-500/30 text-blue-100 border border-blue-400/50',
        ict_director: 'bg-teal-500/30 text-teal-100 border border-teal-400/50',
        ict_officer: 'bg-green-500/30 text-green-100 border border-green-400/50',
        divisional_director: 'bg-orange-500/30 text-orange-100 border border-orange-400/50',
        staff: 'bg-gray-500/30 text-gray-100 border border-gray-400/50'
      }
      return colorClasses[roleName] || 'bg-blue-500/30 text-blue-100 border border-blue-400/50'
    }

    const formatDateTime = (dateString) => {
      if (!dateString) return ''
      return new Date(dateString).toLocaleString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      })
    }

    const showSuccessMessage = (msg) => {
      message.value = msg
      messageType.value = 'success'
      showMessage.value = true
      setTimeout(() => {
        showMessage.value = false
      }, 5000)
    }

    const showErrorMessage = (msg) => {
      message.value = msg
      messageType.value = 'error'
      showMessage.value = true
      setTimeout(() => {
        showMessage.value = false
      }, 5000)
    }

    // Initialize data on mount
    onMounted(async() => {
      await Promise.all([
        loadRoles(),
        loadUsers(),
        loadStatistics()
      ])
    })

    return {
      // Reactive data
      availableRoles,
      allUsers,
      selectedUser,
      selectedRoleIds,
      selectedRoleToAdd,
      userSearchQuery,
      showRolesList,
      showRoleHistory,
      roleHistory,
      statistics,

      // Loading states
      loadingRoles,
      loadingUsers,
      assigningRoles,
      loadingHistory,

      // Message system
      showMessage,
      message,
      messageType,

      // Computed
      filteredUsers,
      availableRolesForDropdown,

      // Methods
      selectUser,
      addRoleToSelection,
      removeRoleFromSelection,
      assignRoles,
      quickRemoveRole,
      viewRoleHistory,
      searchUsers,
      getInitials,
      getRoleName,
      getRoleColorClasses,
      formatDateTime,
      showSuccessMessage,
      showErrorMessage
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
