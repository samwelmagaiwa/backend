<template>
  <div class="flex flex-col h-screen">
    <AppHeader />
    <div class="flex flex-1 overflow-hidden">
      <ModernSidebar v-model:collapsed="sidebarCollapsed" />
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
                    'fa-users',
                    'fa-user-cog',
                    'fa-crown',
                    'fa-user-shield',
                    'fa-id-badge',
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
                      <i class="fas fa-users-cog"></i>
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
                  USER ROLE ASSIGNMENT SYSTEM
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
              <!-- Statistics Cards -->
              <div
                class="medical-card bg-gradient-to-r from-blue-600/25 to-cyan-600/25 border-2 border-blue-400/40 p-6 rounded-2xl backdrop-blur-sm hover:shadow-2xl hover:shadow-blue-500/20 transition-all duration-500 group"
              >
                <div class="flex items-center space-x-4 mb-6">
                  <div
                    class="w-14 h-14 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-blue-300/50"
                  >
                    <i class="fas fa-chart-bar text-white text-xl"></i>
                  </div>
                  <h3 class="text-xl font-bold text-white flex items-center">
                    <i class="fas fa-analytics mr-2 text-blue-300"></i>
                    User Role Statistics
                  </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                  <div
                    class="bg-white/15 p-4 rounded-xl backdrop-blur-sm border border-blue-300/30 hover:bg-white/20 transition-all duration-300 hover:shadow-lg hover:shadow-blue-500/20 group"
                  >
                    <div class="flex items-center space-x-3">
                      <div
                        class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300"
                      >
                        <i class="fas fa-users text-white text-lg"></i>
                      </div>
                      <div>
                        <div class="text-2xl font-bold text-white">
                          {{ userRoleStatistics.total_users || 0 }}
                        </div>
                        <div class="text-sm text-blue-100">Total Users</div>
                      </div>
                    </div>
                  </div>

                  <div
                    class="bg-white/15 p-4 rounded-xl backdrop-blur-sm border border-blue-300/30 hover:bg-white/20 transition-all duration-300 hover:shadow-lg hover:shadow-blue-500/20 group"
                  >
                    <div class="flex items-center space-x-3">
                      <div
                        class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300"
                      >
                        <i class="fas fa-user-check text-white text-lg"></i>
                      </div>
                      <div>
                        <div class="text-2xl font-bold text-white">
                          {{ userRoleStatistics.users_with_roles || 0 }}
                        </div>
                        <div class="text-sm text-blue-100">Users with Roles</div>
                      </div>
                    </div>
                  </div>

                  <div
                    class="bg-white/15 p-4 rounded-xl backdrop-blur-sm border border-blue-300/30 hover:bg-white/20 transition-all duration-300 hover:shadow-lg hover:shadow-blue-500/20 group"
                  >
                    <div class="flex items-center space-x-3">
                      <div
                        class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300"
                      >
                        <i class="fas fa-user-times text-white text-lg"></i>
                      </div>
                      <div>
                        <div class="text-2xl font-bold text-white">
                          {{ userRoleStatistics.users_without_roles || 0 }}
                        </div>
                        <div class="text-sm text-blue-100">Users without Roles</div>
                      </div>
                    </div>
                  </div>

                  <div
                    class="bg-white/15 p-4 rounded-xl backdrop-blur-sm border border-blue-300/30 hover:bg-white/20 transition-all duration-300 hover:shadow-lg hover:shadow-blue-500/20 group"
                  >
                    <div class="flex items-center space-x-3">
                      <div
                        class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300"
                      >
                        <i class="fas fa-crown text-white text-lg"></i>
                      </div>
                      <div>
                        <div class="text-2xl font-bold text-white">
                          {{ userRoleStatistics.hod_users || 0 }}
                        </div>
                        <div class="text-sm text-blue-100">HOD Users</div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- User Management Section -->
              <div
                class="medical-card bg-gradient-to-r from-teal-600/25 to-blue-600/25 border-2 border-teal-400/40 p-6 rounded-2xl backdrop-blur-sm hover:shadow-2xl hover:shadow-teal-500/20 transition-all duration-500 group"
              >
                <div class="flex items-center justify-between mb-6">
                  <div class="flex items-center space-x-4">
                    <div
                      class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-blue-300/50"
                    >
                      <i class="fas fa-users-cog text-white text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white flex items-center">
                      <i class="fas fa-list mr-2 text-blue-300"></i>
                      Users and Their Roles ({{ usersWithRolesPagination.total || 0 }} total users)
                    </h3>
                  </div>
                  <div class="flex space-x-3">
                    <button
                      @click="openCreateUserDialog"
                      class="bg-gradient-to-r from-blue-500 to-blue-600 text-white py-3 px-6 rounded-xl font-semibold hover:from-blue-600 hover:to-blue-700 transition-all duration-300 transform hover:scale-105 shadow-lg flex items-center space-x-2"
                    >
                      <i class="fas fa-user-plus"></i>
                      <span>Create User</span>
                    </button>
                    <button
                      @click="refreshData"
                      :disabled="usersWithRolesLoading"
                      class="bg-gradient-to-r from-blue-500 to-blue-600 text-white py-3 px-6 rounded-xl font-semibold hover:from-blue-600 hover:to-blue-700 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed transform hover:scale-105 shadow-lg flex items-center space-x-2"
                    >
                      <i :class="usersWithRolesLoading ? 'fas fa-spinner fa-spin' : 'fas fa-refresh'"></i>
                      <span>{{ usersWithRolesLoading ? 'Loading...' : 'Refresh' }}</span>
                    </button>
                  </div>
                </div>


                <!-- Filters and Search -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                  <div>
                    <label class="block text-sm font-bold text-teal-100 mb-2">
                      Search Users
                    </label>
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
                    <label class="block text-sm font-bold text-teal-100 mb-2">
                      Filter by Role
                    </label>
                    <div class="relative">
                      <select
                        v-model="filterRole"
                        class="medical-input w-full px-4 py-3 bg-white/15 border-2 border-teal-300/30 rounded-xl focus:border-cyan-400 focus:outline-none text-white backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-cyan-500/20 appearance-none cursor-pointer"
                        @change="applyFilters"
                      >
                        <option value="" class="bg-gray-800 text-white">
                          All Roles
                        </option>
                        <option
                          v-for="option in roleFilterOptions.slice(1)"
                          :key="option.value"
                          :value="option.value"
                          class="bg-gray-800 text-white"
                        >
                          {{ option.text }}
                        </option>
                      </select>
                      <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                        <i class="fas fa-chevron-down text-teal-300"></i>
                      </div>
                    </div>
                  </div>

                  <div>
                    <label class="block text-sm font-bold text-teal-100 mb-2">
                      Sort By
                    </label>
                    <div class="relative">
                      <select
                        v-model="sortBy"
                        class="medical-input w-full px-4 py-3 bg-white/15 border-2 border-teal-300/30 rounded-xl focus:border-cyan-400 focus:outline-none text-white backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-cyan-500/20 appearance-none cursor-pointer"
                        @change="applyFilters"
                      >
                        <option value="name" class="bg-gray-800 text-white">
                          Name
                        </option>
                        <option value="email" class="bg-gray-800 text-white">
                          Email
                        </option>
                        <option value="created_at" class="bg-gray-800 text-white">
                          Created Date
                        </option>
                      </select>
                      <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                        <i class="fas fa-chevron-down text-teal-300"></i>
                      </div>
                    </div>
                  </div>

                  <div>
                    <label class="block text-sm font-bold text-teal-100 mb-2">
                      Filter Options
                    </label>
                    <div class="flex items-center space-x-3 h-12">
                      <input
                        v-model="showHodOnly"
                        type="checkbox"
                        @change="applyFilters"
                        class="w-5 h-5 text-teal-600 border-teal-300 rounded focus:ring-teal-500"
                      />
                      <label class="text-sm text-teal-100">
                        HODs Only
                      </label>
                    </div>
                  </div>
                </div>

                <!-- Users List -->
                <div v-if="usersWithRolesLoading" class="text-center py-8">
                  <div class="inline-flex items-center space-x-2 text-teal-100">
                    <i class="fas fa-spinner fa-spin text-xl"></i>
                    <span>Loading users...</span>
                  </div>
                </div>

                <div v-else-if="allUsersWithRoles.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                  <div
                    v-for="user in allUsersWithRoles"
                    :key="user.id"
                    class="bg-white/15 p-6 rounded-xl backdrop-blur-sm border border-teal-300/30 hover:bg-white/20 transition-all duration-300 hover:shadow-lg hover:shadow-teal-500/20 group"
                  >
                    <div class="flex items-start justify-between mb-4">
                      <div class="flex items-center space-x-3 flex-1">
                        <div class="w-12 h-12 bg-gradient-to-br from-teal-500 to-blue-600 rounded-full flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
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
                          {{ role.name }}
                        </span>
                      </div>
                      <div v-else class="px-2 py-1 bg-gray-500/30 text-gray-100 rounded text-xs border border-gray-400/50">
                        No roles assigned
                      </div>
                    </div>

                    <!-- HOD Status -->
                    <div class="mb-4">
                      <div v-if="user.is_hod" class="flex items-center space-x-2">
                        <div class="w-6 h-6 bg-yellow-500 rounded-full flex items-center justify-center">
                          <i class="fas fa-crown text-white text-xs"></i>
                        </div>
                        <span class="text-yellow-100 text-sm font-medium">HOD Status</span>
                      </div>
                      <div v-if="user.departments_as_hod && user.departments_as_hod.length > 0" class="mt-2">
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

                    <!-- Primary Role -->
                    <div class="mb-4">
                      <div class="text-xs text-teal-100 mb-1">Primary Role:</div>
                      <div v-if="user.primary_role" :class="['px-2 py-1 rounded text-xs border inline-block', getRoleColorClasses(user.primary_role)]">
                        {{ user.primary_role }}
                      </div>
                      <div v-else class="text-teal-200 text-xs">None assigned</div>
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
                      {{ searchQuery ? 'Try adjusting your search criteria' : 'No users available' }}
                    </p>
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


    <!-- Assign Roles Dialog -->
    <div
      v-if="assignRolesDialog"
      class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 backdrop-blur-sm"
    >
      <div
        class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto transform transition-all duration-300 scale-100 animate-slideUp"
      >
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-6 text-center">
          <div
            class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4 backdrop-blur-sm border border-white/30"
          >
            <i class="fas fa-user-edit text-white text-2xl"></i>
          </div>
          <h3 class="text-xl font-bold text-white mb-2">
            Assign Roles to {{ selectedUser?.name }}
          </h3>
          <div class="w-12 h-1 bg-white/50 mx-auto rounded-full"></div>
        </div>

        <!-- Body -->
        <div class="p-6">
          <div v-if="selectedUser" class="mb-6">
            <div class="bg-blue-50 rounded-xl p-4 border border-blue-100 shadow-md">
              <div class="flex items-center space-x-3 mb-3">
                <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center">
                  <span class="text-white font-bold">
                    {{ getInitials(selectedUser.name) }}
                  </span>
                </div>
                <div>
                  <h4 class="font-semibold text-gray-800">{{ selectedUser.name }}</h4>
                  <p class="text-sm text-gray-600">{{ selectedUser.email }}</p>
                  <p v-if="selectedUser.pf_number" class="text-xs text-gray-500">
                    PF: {{ selectedUser.pf_number }}
                  </p>
                </div>
              </div>
            </div>
          </div>

          <form ref="assignRolesForm" @submit.prevent="assignRoles">
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Select Roles *
              </label>
              <div class="space-y-2 max-h-48 overflow-y-auto border border-gray-300 rounded-xl p-3">
                <div
                  v-for="role in availableRoles"
                  :key="role.id"
                  class="flex items-center space-x-3 p-2 hover:bg-gray-50 rounded-lg transition-colors duration-200"
                >
                  <input
                    v-model="selectedRoleIds"
                    :value="role.id"
                    type="checkbox"
                    class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                  />
                  <div class="flex-1">
                    <div class="flex items-center space-x-2">
                      <span :class="['px-2 py-1 rounded text-xs border', getRoleColorClasses(role.name)]">
                        {{ role.name }}
                      </span>
                    </div>
                    <p v-if="role.description" class="text-sm text-gray-600 mt-1">
                      {{ role.description }}
                    </p>
                  </div>
                </div>
              </div>
              <div v-if="getFieldError('role_ids')" class="text-red-500 text-sm mt-1">
                {{ getFieldError('role_ids') }}
              </div>
            </div>


            <!-- Selected Roles Preview -->
            <div v-if="selectedRoleIds.length > 0" class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Selected Roles ({{ selectedRoleIds.length }}):
              </label>
              <div class="flex flex-wrap gap-2">
                <span
                  v-for="roleId in selectedRoleIds"
                  :key="roleId"
                  :class="[
                    'px-3 py-1 rounded-full text-sm border flex items-center space-x-2',
                    getRoleColorClasses(availableRoles.find(r => r.id === roleId)?.name || '')
                  ]"
                >
                  <span>{{ availableRoles.find(r => r.id === roleId)?.name }}</span>
                  <button
                    @click="removeRole(roleId)"
                    class="text-current hover:text-red-500 transition-colors duration-200"
                  >
                    <i class="fas fa-times text-xs"></i>
                  </button>
                </span>
              </div>
            </div>
          </form>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end space-x-3 p-6 border-t border-gray-200">
          <button
            @click="closeAssignRolesDialog"
            :disabled="assignRolesSubmitting"
            class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-medium hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Cancel
          </button>
          <button
            @click="assignRoles"
            :disabled="assignRolesSubmitting || selectedRoleIds.length === 0"
            class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl font-medium hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed flex items-center space-x-2"
          >
            <i v-if="assignRolesSubmitting" class="fas fa-spinner fa-spin"></i>
            <i v-else class="fas fa-save"></i>
            <span>{{ assignRolesSubmitting ? 'Updating...' : 'Update Roles' }}</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Role History Dialog -->
    <div
      v-if="roleHistoryDialog"
      class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 backdrop-blur-sm"
    >
      <div
        class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto transform transition-all duration-300 scale-100 animate-slideUp"
      >
        <!-- Header -->
        <div class="bg-gradient-to-r from-red-500 to-red-600 p-6 text-center">
          <div
            class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4 backdrop-blur-sm border border-white/30"
          >
            <i class="fas fa-history text-white text-2xl"></i>
          </div>
          <h3 class="text-xl font-bold text-white mb-2">
            Role History for {{ selectedUser?.name }}
          </h3>
          <div class="w-12 h-1 bg-white/50 mx-auto rounded-full"></div>
        </div>

        <!-- Body -->
        <div class="p-6">
          <div v-if="roleHistory.length > 0" class="space-y-4">
            <div
              v-for="(change, index) in roleHistory"
              :key="index"
              class="bg-gray-50 rounded-xl p-4 border border-gray-200 shadow-sm hover:shadow-md transition-shadow duration-200"
            >
              <div class="flex items-start space-x-4">
                <div
                  :class="[
                    'w-10 h-10 rounded-full flex items-center justify-center shadow-lg',
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
                      {{ formatDateTime(change.created_at) }}
                    </span>
                  </div>
                  <div class="mb-2">
                    <span :class="['px-3 py-1 rounded-full text-sm border', getRoleColorClasses(change.role_name)]">
                      {{ change.role_name }}
                    </span>
                  </div>
                  <div v-if="change.changed_by" class="text-sm text-gray-600">
                    <i class="fas fa-user text-gray-400 mr-1"></i>
                    Changed by: {{ change.changed_by }}
                  </div>
                  <div v-if="change.reason" class="text-sm text-gray-600 mt-1">
                    <i class="fas fa-comment text-gray-400 mr-1"></i>
                    Reason: {{ change.reason }}
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div v-if="roleHistory.length === 0" class="text-center py-8">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <i class="fas fa-history text-gray-400 text-2xl"></i>
            </div>
            <h4 class="text-lg font-semibold text-gray-800 mb-2">No role history</h4>
            <p class="text-gray-600">
              This user has no role change history.
            </p>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end p-6 border-t border-gray-200">
          <button
            @click="roleHistoryDialog = false"
            class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-medium hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2 transition-all duration-200 shadow-md hover:shadow-lg"
          >
            Close
          </button>
        </div>
      </div>
    </div>

    <!-- Create User Dialog -->
    <div
      v-if="createUserDialog"
      class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 backdrop-blur-sm"
    >
      <div
        class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto transform transition-all duration-300 scale-100 animate-slideUp"
      >
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-6 text-center">
          <div
            class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4 backdrop-blur-sm border border-white/30"
          >
            <i class="fas fa-user-plus text-white text-2xl"></i>
          </div>
          <h3 class="text-xl font-bold text-white mb-2">
            Create New User
          </h3>
          <div class="w-12 h-1 bg-white/50 mx-auto rounded-full"></div>
        </div>

        <!-- Body -->
        <div class="p-6">
          <form ref="createUserForm" @submit.prevent="createUser">
            <!-- User Basic Information Section -->
            <div class="medical-card bg-gradient-to-r from-blue-600/15 to-cyan-600/15 border-2 border-blue-400/30 p-6 rounded-2xl backdrop-blur-sm hover:shadow-lg hover:shadow-blue-500/20 transition-all duration-300 mb-6">
              <div class="flex items-center space-x-3 mb-6">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                  <i class="fas fa-user text-white text-lg"></i>
                </div>
                <h4 class="text-xl font-bold text-gray-800">User Information</h4>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Full Name -->
                <div class="space-y-2">
                  <div class="flex items-center space-x-2 mb-3">
                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-md">
                      <i class="fas fa-user text-white text-sm"></i>
                    </div>
                    <label class="text-sm font-bold text-gray-700">
                      Full Name *
                    </label>
                  </div>
                  <div class="relative">
                    <input
                      v-model="createUserForm.name"
                      type="text"
                      required
                      class="w-full px-4 py-3 bg-white/70 border-2 border-blue-300/30 rounded-xl focus:border-blue-500 focus:outline-none text-gray-800 placeholder-gray-500 backdrop-blur-sm transition-all duration-300 hover:bg-white/80 focus:bg-white/90 focus:shadow-lg focus:shadow-blue-500/20"
                      placeholder="Enter full name"
                    />
                    <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-blue-500/5 to-cyan-500/5 opacity-0 hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
                  </div>
                  <div v-if="getCreateUserFieldError('name')" class="text-red-500 text-sm mt-1 flex items-center space-x-1">
                    <i class="fas fa-exclamation-circle text-xs"></i>
                    <span>{{ getCreateUserFieldError('name') }}</span>
                  </div>
                </div>

                <!-- Email Address -->
                <div class="space-y-2">
                  <div class="flex items-center space-x-2 mb-3">
                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-md">
                      <i class="fas fa-envelope text-white text-sm"></i>
                    </div>
                    <label class="text-sm font-bold text-gray-700">
                      Email Address *
                    </label>
                  </div>
                  <div class="relative">
                    <input
                      v-model="createUserForm.email"
                      type="email"
                      required
                      class="w-full px-4 py-3 bg-white/70 border-2 border-blue-300/30 rounded-xl focus:border-blue-500 focus:outline-none text-gray-800 placeholder-gray-500 backdrop-blur-sm transition-all duration-300 hover:bg-white/80 focus:bg-white/90 focus:shadow-lg focus:shadow-blue-500/20"
                      placeholder="Enter email address"
                    />
                    <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-blue-500/5 to-cyan-500/5 opacity-0 hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
                  </div>
                  <div v-if="getCreateUserFieldError('email')" class="text-red-500 text-sm mt-1 flex items-center space-x-1">
                    <i class="fas fa-exclamation-circle text-xs"></i>
                    <span>{{ getCreateUserFieldError('email') }}</span>
                  </div>
                </div>

                <!-- PF Number -->
                <div class="space-y-2">
                  <div class="flex items-center space-x-2 mb-3">
                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-md">
                      <i class="fas fa-id-badge text-white text-sm"></i>
                    </div>
                    <label class="text-sm font-bold text-gray-700">
                      PF Number
                    </label>
                  </div>
                  <div class="relative">
                    <input
                      v-model="createUserForm.pf_number"
                      type="text"
                      class="w-full px-4 py-3 bg-white/70 border-2 border-blue-300/30 rounded-xl focus:border-blue-500 focus:outline-none text-gray-800 placeholder-gray-500 backdrop-blur-sm transition-all duration-300 hover:bg-white/80 focus:bg-white/90 focus:shadow-lg focus:shadow-blue-500/20"
                      placeholder="Enter PF number"
                    />
                    <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-blue-500/5 to-cyan-500/5 opacity-0 hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
                  </div>
                  <div v-if="getCreateUserFieldError('pf_number')" class="text-red-500 text-sm mt-1 flex items-center space-x-1">
                    <i class="fas fa-exclamation-circle text-xs"></i>
                    <span>{{ getCreateUserFieldError('pf_number') }}</span>
                  </div>
                </div>

                <!-- Phone Number -->
                <div class="space-y-2">
                  <div class="flex items-center space-x-2 mb-3">
                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-md">
                      <i class="fas fa-phone text-white text-sm"></i>
                    </div>
                    <label class="text-sm font-bold text-gray-700">
                      Phone Number
                    </label>
                  </div>
                  <div class="relative">
                    <input
                      v-model="createUserForm.phone"
                      type="tel"
                      class="w-full px-4 py-3 bg-white/70 border-2 border-blue-300/30 rounded-xl focus:border-blue-500 focus:outline-none text-gray-800 placeholder-gray-500 backdrop-blur-sm transition-all duration-300 hover:bg-white/80 focus:bg-white/90 focus:shadow-lg focus:shadow-blue-500/20"
                      placeholder="Enter phone number"
                    />
                    <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-blue-500/5 to-cyan-500/5 opacity-0 hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
                  </div>
                  <div v-if="getCreateUserFieldError('phone')" class="text-red-500 text-sm mt-1 flex items-center space-x-1">
                    <i class="fas fa-exclamation-circle text-xs"></i>
                    <span>{{ getCreateUserFieldError('phone') }}</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Department and Security Section -->
            <div class="medical-card bg-gradient-to-r from-purple-600/15 to-indigo-600/15 border-2 border-purple-400/30 p-6 rounded-2xl backdrop-blur-sm hover:shadow-lg hover:shadow-purple-500/20 transition-all duration-300 mb-6">
              <div class="flex items-center space-x-3 mb-6">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                  <i class="fas fa-building text-white text-lg"></i>
                </div>
                <h4 class="text-xl font-bold text-gray-800">Department & Security</h4>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Department -->
                <div class="space-y-2">
                  <div class="flex items-center space-x-2 mb-3">
                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-md">
                      <i class="fas fa-building text-white text-sm"></i>
                    </div>
                    <label class="text-sm font-bold text-gray-700">
                      Department
                    </label>
                  </div>
                  <div class="relative">
                    <select
                      v-model="createUserForm.department_id"
                      class="w-full px-4 py-3 bg-white/70 border-2 border-purple-300/30 rounded-xl focus:border-purple-500 focus:outline-none text-gray-800 backdrop-blur-sm transition-all duration-300 hover:bg-white/80 focus:bg-white/90 focus:shadow-lg focus:shadow-purple-500/20 appearance-none cursor-pointer"
                    >
                      <option value="" class="bg-gray-800 text-white">Select Department</option>
                      <option
                        v-for="department in availableDepartments"
                        :key="department.id"
                        :value="department.id"
                        class="bg-gray-800 text-white"
                      >
                        {{ department.name }}
                      </option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                      <i class="fas fa-chevron-down text-purple-500"></i>
                    </div>
                  </div>
                  <div v-if="getCreateUserFieldError('department_id')" class="text-red-500 text-sm mt-1 flex items-center space-x-1">
                    <i class="fas fa-exclamation-circle text-xs"></i>
                    <span>{{ getCreateUserFieldError('department_id') }}</span>
                  </div>
                </div>

                <!-- Password -->
                <div class="space-y-2">
                  <div class="flex items-center space-x-2 mb-3">
                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-md">
                      <i class="fas fa-lock text-white text-sm"></i>
                    </div>
                    <label class="text-sm font-bold text-gray-700">
                      Password *
                    </label>
                  </div>
                  <div class="relative">
                    <input
                      v-model="createUserForm.password"
                      type="password"
                      required
                      class="w-full px-4 py-3 bg-white/70 border-2 border-purple-300/30 rounded-xl focus:border-purple-500 focus:outline-none text-gray-800 placeholder-gray-500 backdrop-blur-sm transition-all duration-300 hover:bg-white/80 focus:bg-white/90 focus:shadow-lg focus:shadow-purple-500/20"
                      placeholder="Enter password"
                    />
                    <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-purple-500/5 to-indigo-500/5 opacity-0 hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
                  </div>
                  <div v-if="getCreateUserFieldError('password')" class="text-red-500 text-sm mt-1 flex items-center space-x-1">
                    <i class="fas fa-exclamation-circle text-xs"></i>
                    <span>{{ getCreateUserFieldError('password') }}</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Roles Assignment Section -->
            <div class="medical-card bg-gradient-to-r from-green-600/15 to-emerald-600/15 border-2 border-green-400/30 p-6 rounded-2xl backdrop-blur-sm hover:shadow-lg hover:shadow-green-500/20 transition-all duration-300 mb-6">
              <div class="flex items-center space-x-3 mb-6">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                  <i class="fas fa-user-tag text-white text-lg"></i>
                </div>
                <h4 class="text-xl font-bold text-gray-800">Assign Roles</h4>
              </div>

              <div class="space-y-6">
                <!-- Role Selection Dropdown -->
                <div class="space-y-2">
                  <div class="flex items-center space-x-2 mb-3">
                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-md">
                      <i class="fas fa-list text-white text-sm"></i>
                    </div>
                    <label class="text-sm font-bold text-gray-700">
                      Select Roles
                    </label>
                  </div>
                  <div class="relative">
                    <select
                      v-model="selectedRoleForAdd"
                      @change="addRoleToUser"
                      class="w-full px-4 py-3 bg-white/70 border-2 border-green-300/30 rounded-xl focus:border-green-500 focus:outline-none text-gray-800 backdrop-blur-sm transition-all duration-300 hover:bg-white/80 focus:bg-white/90 focus:shadow-lg focus:shadow-green-500/20 appearance-none cursor-pointer"
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
                      <i class="fas fa-chevron-down text-green-500"></i>
                    </div>
                  </div>
                  <div class="text-xs text-gray-600 mt-1 flex items-center space-x-1">
                    <i class="fas fa-info-circle text-green-500"></i>
                    <span>Select roles from the dropdown to assign to the user</span>
                  </div>
                </div>

                <!-- Alternative: Checkbox List (Optional) -->
                <div class="space-y-2">
                  <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                      <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-md">
                        <i class="fas fa-check-square text-white text-sm"></i>
                      </div>
                      <label class="text-sm font-bold text-gray-700">
                        Or select from list
                      </label>
                    </div>
                    <button
                      @click="toggleRolesList"
                      type="button"
                      class="text-sm text-green-600 hover:text-green-700 font-medium flex items-center space-x-1"
                    >
                      <span>{{ showRolesList ? 'Hide' : 'Show' }} roles list</span>
                      <i :class="showRolesList ? 'fas fa-chevron-up' : 'fas fa-chevron-down'"></i>
                    </button>
                  </div>

                  <div v-if="showRolesList" class="bg-white/50 border-2 border-green-300/30 rounded-xl p-4 max-h-64 overflow-y-auto">
                    <div class="space-y-3">
                      <div
                        v-for="role in availableRoles"
                        :key="role.id"
                        class="flex items-center space-x-3 p-3 bg-white/50 rounded-lg hover:bg-white/70 transition-all duration-200"
                      >
                        <input
                          v-model="createUserForm.role_ids"
                          :value="role.id"
                          type="checkbox"
                          class="w-5 h-5 text-green-600 border-gray-300 rounded focus:ring-green-500 focus:ring-2"
                        />
                        <div class="flex items-center space-x-2 flex-1">
                          <div class="w-5 h-5 bg-gradient-to-br from-blue-500 to-blue-600 rounded flex items-center justify-center">
                            <i class="fas fa-shield-alt text-white text-xs"></i>
                          </div>
                          <div class="flex-1">
                            <span class="text-sm font-medium text-gray-700">
                              {{ role.name }}
                            </span>
                            <p v-if="role.description" class="text-xs text-gray-600 mt-1">
                              {{ role.description }}
                            </p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div v-if="getCreateUserFieldError('role_ids')" class="text-red-500 text-sm flex items-center space-x-1">
                  <i class="fas fa-exclamation-circle text-xs"></i>
                  <span>{{ getCreateUserFieldError('role_ids') }}</span>
                </div>
              </div>

              <!-- Selected Roles Preview -->
              <div v-if="createUserForm.role_ids.length > 0" class="mt-6">
                <div class="flex items-center space-x-2 mb-3">
                  <div class="w-6 h-6 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-sm">
                    <i class="fas fa-check text-white text-xs"></i>
                  </div>
                  <label class="text-sm font-bold text-gray-700">
                    Selected Roles ({{ createUserForm.role_ids.length }}):
                  </label>
                </div>
                <div class="flex flex-wrap gap-2">
                  <span
                    v-for="roleId in createUserForm.role_ids"
                    :key="roleId"
                    class="px-3 py-2 bg-blue-500/20 text-blue-800 rounded-lg text-sm border border-blue-400/50 flex items-center space-x-2 shadow-sm"
                  >
                    <span>{{ availableRoles.find(r => r.id === roleId)?.name }}</span>
                    <button
                      @click="removeRoleFromCreateForm(roleId)"
                      class="text-blue-600 hover:text-red-500 transition-colors duration-200"
                    >
                      <i class="fas fa-times text-xs"></i>
                    </button>
                  </span>
                </div>
              </div>
            </div>
          </form>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end space-x-3 p-6 border-t border-gray-200">
          <button
            @click="closeCreateUserDialog"
            :disabled="createUserSubmitting"
            class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-medium hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Cancel
          </button>
          <button
            @click="createUser"
            :disabled="createUserSubmitting || !createUserForm.name || !createUserForm.email || !createUserForm.password"
            class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl font-medium hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed flex items-center space-x-2"
          >
            <i v-if="createUserSubmitting" class="fas fa-spinner fa-spin"></i>
            <i v-else class="fas fa-user-plus"></i>
            <span>{{ createUserSubmitting ? 'Creating...' : 'Create User' }}</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Success/Error Toast Notifications -->
    <div
      v-if="showSnackbar"
      :class="[
        'fixed top-4 right-4 px-6 py-4 rounded-xl shadow-2xl z-50 max-w-md border border-white/20 transform transition-all duration-300',
        snackbarColor === 'success' ? 'bg-gradient-to-r from-green-500 to-emerald-500 text-white' :
        snackbarColor === 'error' ? 'bg-gradient-to-r from-red-500 to-rose-500 text-white' :
        'bg-gradient-to-r from-blue-500 to-cyan-500 text-white'
      ]"
    >
      <div class="flex items-center">
        <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center mr-3">
          <i
            :class="[
              'text-sm',
              snackbarColor === 'success' ? 'fas fa-check' :
              snackbarColor === 'error' ? 'fas fa-times' :
              'fas fa-info'
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
import { mapGetters, mapActions } from 'vuex'
import { debounce } from 'lodash'
import AppHeader from '@/components/AppHeader.vue'
import ModernSidebar from '@/components/ModernSidebar.vue'
import AppFooter from '@/components/footer.vue'

export default {
  name: 'UserRoleAssignment',

  components: {
    AppHeader,
    ModernSidebar,
    AppFooter
  },

  data() {
    return {
      sidebarCollapsed: false,
      // Table configuration
      headers: [
        { text: 'User', value: 'user_info', sortable: false, width: '250px' },
        { text: 'Current Roles', value: 'roles', sortable: false },
        { text: 'HOD Status', value: 'is_hod', sortable: false },
        { text: 'Primary Role', value: 'primary_role', sortable: false },
        { text: 'Actions', value: 'actions', sortable: false, width: '120px' }
      ],

      tableOptions: {
        page: 1,
        itemsPerPage: 15,
        sortBy: ['name'],
        sortDesc: [false]
      },

      // Search and filters
      searchQuery: '',
      filterRole: null,
      sortBy: 'name',
      showHodOnly: false,

      sortOptions: [
        { text: 'Name', value: 'name' },
        { text: 'Email', value: 'email' },
        { text: 'Created Date', value: 'created_at' }
      ],

      // Dialog states
      assignRolesDialog: false,
      roleHistoryDialog: false,
      createUserDialog: false,

      // Form data
      selectedRoleIds: [],
      assignRolesFormValid: false,
      assignRolesSubmitting: false,
      assignRolesFormErrors: {},

      // Validation rules
      roleSelectionRules: [
        (v) => (v && v.length > 0) || 'At least one role must be selected'
      ],

      // Role history
      roleHistory: [],

      // Create User Form
      createUserForm: {
        name: '',
        email: '',
        pf_number: '',
        phone: '',
        department_id: '',
        password: '',
        role_ids: []
      },
      createUserSubmitting: false,
      createUserFormErrors: {},
      availableDepartments: [],

      // Role selection for dropdown
      selectedRoleForAdd: '',
      showRolesList: false,

      // Snackbar
      showSnackbar: false,
      snackbarMessage: '',
      snackbarColor: 'success'
    }
  },

  computed: {
    ...mapGetters('roleManagement', [
      'allUsersWithRoles',
      'usersWithRolesLoading',
      'usersWithRolesPagination',
      'userRoleStatistics',
      'allRoles',
      'selectedUser',
      'error',
      'successMessage'
    ]),

    ...mapGetters('auth', ['user', 'userPermissions']),

    availableRoles() {
      return this.allRoles.filter((role) => {
        // Filter out roles that user cannot assign
        if (role.is_system_role && !this.user.roles.includes('super_admin')) {
          return false
        }
        return true
      })
    },

    roleFilterOptions() {
      const options = [{ text: 'All Roles', value: null }]
      this.allRoles.forEach((role) => {
        options.push({ text: role.name, value: role.name })
      })
      return options
    },

    debouncedSearch() {
      return debounce(this.applyFilters, 500)
    },

    availableRolesForDropdown() {
      // Filter out roles that are already selected
      return this.availableRoles.filter(role =>
        !this.createUserForm.role_ids.includes(role.id)
      )
    }
  },

  watch: {
    error(newError) {
      if (newError) {
        this.showErrorMessage(newError)
      }
    },

    successMessage(newMessage) {
      if (newMessage) {
        this.showSuccessMessage(newMessage)
      }
    },

    assignRolesDialog(val) {
      if (!val) {
        this.resetAssignRolesForm()
      }
    }
  },

  async created() {
    await this.initializeData()
  },

  methods: {
    ...mapActions('roleManagement', [
      'fetchUsersWithRoles',
      'fetchRoles',
      'fetchUserRoleStatistics',
      'assignRolesToUser',
      'removeRoleFromUser',
      'setSelectedUser',
      'clearMessages'
    ]),

    async initializeData() {
      await Promise.all([
        this.fetchUsersWithRoles(),
        this.fetchRoles(),
        this.fetchUserRoleStatistics()
      ])
    },

    async refreshData() {
      await Promise.all([
        this.fetchUsersWithRoles(this.getFilterParams()),
        this.fetchUserRoleStatistics()
      ])
    },

    async applyFilters() {
      this.tableOptions.page = 1
      await this.fetchUsersWithRoles(this.getFilterParams())
    },

    async handleTableUpdate(options) {
      this.tableOptions = options
      await this.fetchUsersWithRoles(this.getFilterParams())
    },

    getFilterParams() {
      return {
        search: this.searchQuery,
        role: this.filterRole,
        is_hod: this.showHodOnly,
        sort_by: this.sortBy,
        sort_order: this.tableOptions.sortDesc[0] ? 'desc' : 'asc',
        page: this.tableOptions.page,
        per_page: this.tableOptions.itemsPerPage
      }
    },

    // Role assignment methods
    openAssignRolesDialog(user) {
      this.setSelectedUser(user)
      this.selectedRoleIds = user.roles.map((role) => role.id)
      this.assignRolesDialog = true
    },

    closeAssignRolesDialog() {
      this.assignRolesDialog = false
      this.setSelectedUser(null)
    },

    resetAssignRolesForm() {
      this.selectedRoleIds = []
      this.assignRolesFormErrors = {}
      this.assignRolesFormValid = false

      if (this.$refs.assignRolesForm) {
        this.$refs.assignRolesForm.resetValidation()
      }
    },

    async assignRoles() {
      if (!this.$refs.assignRolesForm.validate()) {
        return
      }

      this.assignRolesSubmitting = true
      this.assignRolesFormErrors = {}

      try {
        const result = await this.assignRolesToUser({
          userId: this.selectedUser.id,
          roleIds: this.selectedRoleIds
        })

        if (result.success) {
          this.closeAssignRolesDialog()
          await this.fetchUserRoleStatistics()
        } else {
          this.assignRolesFormErrors = result.errors || {}
        }
      } catch (error) {
        console.error('Error assigning roles:', error)
      } finally {
        this.assignRolesSubmitting = false
      }
    },

    removeRole(roleId) {
      this.selectedRoleIds = this.selectedRoleIds.filter((id) => id !== roleId)
    },

    async quickRemoveRole(roleId) {
      if (!confirm('Are you sure you want to remove this role?')) {
        return
      }

      try {
        const result = await this.removeRoleFromUser({
          userId: this.selectedUser.id,
          roleId: roleId
        })

        if (result.success) {
          await this.fetchUserRoleStatistics()
        }
      } catch (error) {
        console.error('Error removing role:', error)
      }
    },

    // Role history methods
    async viewRoleHistory(user) {
      this.setSelectedUser(user)
      this.roleHistoryDialog = true

      try {
        const response = await this.$store.dispatch(
          'roleManagement/getUserRoleHistory',
          user.id
        )
        if (response.success) {
          this.roleHistory = response.data.data
        }
      } catch (error) {
        console.error('Error fetching role history:', error)
        this.roleHistory = []
      }
    },

    // Create User methods
    openCreateUserDialog() {
      this.resetCreateUserForm()
      this.createUserDialog = true
      this.fetchDepartments()
    },

    closeCreateUserDialog() {
      this.createUserDialog = false
      this.resetCreateUserForm()
    },

    resetCreateUserForm() {
      this.createUserForm = {
        name: '',
        email: '',
        pf_number: '',
        phone: '',
        department_id: '',
        password: '',
        role_ids: []
      }
      this.createUserFormErrors = {}
      this.selectedRoleForAdd = ''
      this.showRolesList = false
    },

    async createUser() {
      this.createUserSubmitting = true
      this.createUserFormErrors = {}

      try {
        // Add your API call here
        // const result = await userService.createUser(this.createUserForm)

        // Mock success for now
        await new Promise(resolve => setTimeout(resolve, 1000))

        this.showSuccessMessage('User created successfully!')
        this.closeCreateUserDialog()
        await this.refreshData()
      } catch (error) {
        console.error('Error creating user:', error)
        this.showErrorMessage('Failed to create user')
        // Handle validation errors
        if (error.response && error.response.data && error.response.data.errors) {
          this.createUserFormErrors = error.response.data.errors
        }
      } finally {
        this.createUserSubmitting = false
      }
    },

    removeRoleFromCreateForm(roleId) {
      this.createUserForm.role_ids = this.createUserForm.role_ids.filter(id => id !== roleId)
    },

    addRoleToUser() {
      if (this.selectedRoleForAdd && !this.createUserForm.role_ids.includes(this.selectedRoleForAdd)) {
        this.createUserForm.role_ids.push(this.selectedRoleForAdd)
        this.selectedRoleForAdd = '' // Reset dropdown
      }
    },

    toggleRolesList() {
      this.showRolesList = !this.showRolesList
    },

    async fetchDepartments() {
      try {
        // Add your API call here
        // const result = await departmentService.getDepartments()
        // this.availableDepartments = result.data

        // Mock data for now
        this.availableDepartments = [
          { id: 1, name: 'Information Technology' },
          { id: 2, name: 'Human Resources' },
          { id: 3, name: 'Finance' },
          { id: 4, name: 'Medical Services' },
          { id: 5, name: 'Administration' }
        ]
      } catch (error) {
        console.error('Error fetching departments:', error)
        this.availableDepartments = []
      }
    },

    // Utility methods
    getInitials(name) {
      return name
        .split(' ')
        .map((word) => word.charAt(0))
        .join('')
        .toUpperCase()
        .substring(0, 2)
    },

    getRoleColor(roleName) {
      const colors = {
        admin: 'red',
        super_admin: 'purple',
        head_of_department: 'blue',

        ict_director: 'teal',
        ict_officer: 'green',
        divisional_director: 'orange',
        staff: 'grey'
      }
      return colors[roleName] || 'primary'
    },

    getRoleColorClasses(roleName) {
      const colorClasses = {
        admin: 'bg-red-500/30 text-red-100 border-red-400/50',
        super_admin: 'bg-purple-500/30 text-purple-100 border-purple-400/50',
        head_of_department: 'bg-blue-500/30 text-blue-100 border-blue-400/50',

        ict_director: 'bg-teal-500/30 text-teal-100 border-teal-400/50',
        ict_officer: 'bg-green-500/30 text-green-100 border-green-400/50',
        divisional_director: 'bg-orange-500/30 text-orange-100 border-orange-400/50',
        staff: 'bg-gray-500/30 text-gray-100 border-gray-400/50'
      }
      return colorClasses[roleName] || 'bg-blue-500/30 text-blue-100 border-blue-400/50'
    },

    formatDateTime(dateString) {
      if (!dateString) return ''
      return new Date(dateString).toLocaleString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      })
    },

    getFieldError(fieldName) {
      return this.assignRolesFormErrors[fieldName]
        ? this.assignRolesFormErrors[fieldName][0]
        : ''
    },

    getCreateUserFieldError(fieldName) {
      return this.createUserFormErrors[fieldName]
        ? this.createUserFormErrors[fieldName][0]
        : ''
    },

    showSuccessMessage(message) {
      this.snackbarMessage = message
      this.snackbarColor = 'success'
      this.showSnackbar = true
      this.clearMessages()
    },

    showErrorMessage(message) {
      this.snackbarMessage = message
      this.snackbarColor = 'error'
      this.showSnackbar = true
      this.clearMessages()
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

/* User card hover effects */
.group:hover .group-hover\:scale-110 {
  transform: scale(1.1);
}

/* Button hover effects */
button:hover {
  transform: translateY(-1px);
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .grid-cols-4 {
    grid-template-columns: repeat(1, minmax(0, 1fr));
  }

  .grid-cols-3 {
    grid-template-columns: repeat(1, minmax(0, 1fr));
  }

  .text-4xl {
    font-size: 2rem;
  }

  .text-2xl {
    font-size: 1.5rem;
  }
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
