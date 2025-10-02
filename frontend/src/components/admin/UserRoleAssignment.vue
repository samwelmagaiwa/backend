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
                      @click="openCreateUserModal"
                      class="bg-gradient-to-r from-emerald-500 to-emerald-600 text-white py-3 px-6 rounded-xl font-semibold hover:from-emerald-600 hover:to-emerald-700 transition-all duration-300 transform hover:scale-105 shadow-lg flex items-center space-x-2"
                    >
                      <i class="fas fa-user-plus"></i>
                      <span>Create User</span>
                    </button>
                    <button
                      @click="openCreateRoleModal"
                      class="bg-gradient-to-r from-purple-500 to-purple-600 text-white py-3 px-6 rounded-xl font-semibold hover:from-purple-600 hover:to-purple-700 transition-all duration-300 transform hover:scale-105 shadow-lg flex items-center space-x-2"
                    >
                      <i class="fas fa-shield-alt"></i>
                      <span>Create Role</span>
                    </button>
                    <button
                      @click="refreshData"
                      :disabled="loading"
                      class="bg-gradient-to-r from-blue-500 to-blue-600 text-white py-3 px-6 rounded-xl font-semibold hover:from-blue-600 hover:to-blue-700 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed transform hover:scale-105 shadow-lg flex items-center space-x-2"
                    >
                      <i :class="loading ? 'fas fa-spinner fa-spin' : 'fas fa-refresh'"></i>
                      <span>{{ loading ? 'Loading...' : 'Refresh' }}</span>
                    </button>
                    <button
                      @click="debugFilterData"
                      class="bg-gradient-to-r from-yellow-500 to-yellow-600 text-white py-3 px-6 rounded-xl font-semibold hover:from-yellow-600 hover:to-yellow-700 transition-all duration-300 transform hover:scale-105 shadow-lg flex items-center space-x-2"
                    >
                      <i class="fas fa-bug"></i>
                      <span>Debug</span>
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
                    <label class="block text-base font-bold text-teal-100 mb-2">Filter by Role</label>
                    <div class="relative">
                      <select
                        v-model="filterRole"
                        class="medical-input w-full px-4 py-3 bg-white/15 border-2 border-teal-300/30 rounded-xl focus:border-cyan-400 focus:outline-none text-white backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-cyan-500/20 appearance-none cursor-pointer"
                        @change="applyFilters"
                      >
                        <option value="" class="bg-blue-900 text-white">All Roles ({{ availableRoles.length }} available)</option>
                        <option
                          v-for="role in availableRoles"
                          :key="role.id"
                          :value="role.name"
                          class="bg-blue-900 text-white"
                          :title="`Value: ${role.name}, Display: ${role.display_name || role.name}`"
                        >
                          {{ role.display_name || role.name }}
                        </option>
                      </select>
                      <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                        <i class="fas fa-chevron-down text-teal-300"></i>
                      </div>
                    </div>
                  </div>

                  <div>
                    <label class="block text-base font-bold text-teal-100 mb-2">Filter by Department</label>
                    <div class="relative">
                      <select
                        v-model="filterDepartment"
                        class="medical-input w-full px-4 py-3 bg-white/15 border-2 border-teal-300/30 rounded-xl focus:border-cyan-400 focus:outline-none text-white backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-cyan-500/20 appearance-none cursor-pointer"
                        @change="applyFilters"
                      >
                        <option value="" class="bg-blue-900 text-white">All Departments ({{ availableDepartments.length }} available)</option>
                        <option
                          v-for="department in availableDepartments"
                          :key="department.id"
                          :value="department.id"
                          class="bg-blue-900 text-white"
                        >
                          {{ department.display_name || department.name }}
                        </option>
                      </select>
                      <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
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
                        <option value="name" class="bg-blue-900 text-white">Name</option>
                        <option value="email" class="bg-blue-900 text-white">Email</option>
                        <option value="created_at" class="bg-blue-900 text-white">
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
                  v-else-if="Array.isArray(filteredUsers) && filteredUsers.length > 0"
                  class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4"
                >
                  <div
                    v-for="user in filteredUsers"
                    :key="user.id || user.email || Math.random()"
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
                          <h4 class="font-bold text-white text-lg">{{ user.name || 'Unknown User' }}</h4>
                          <p class="text-teal-100 text-sm">{{ user.email || 'No email' }}</p>
                          <p v-if="user.pf_number" class="text-teal-200 text-xs">
                            PF: {{ user.pf_number }}
                          </p>
                          <p v-if="user.department && user.department.display_name" class="text-teal-200 text-xs">
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

    <!-- Create User Modal -->
    <div
      v-if="showCreateUserModal"
      class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4"
      @click="closeCreateUserModal"
    >
      <div
        class="bg-gradient-to-br from-blue-900 via-blue-800 to-teal-900 rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto"
        @click.stop
      >
        <!-- Modal Header -->
        <div class="bg-red-800 p-6 rounded-t-2xl border-b border-blue-500/30">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
              <div class="w-12 h-12 bg-blue-500/30 rounded-xl flex items-center justify-center">
                <i class="fas fa-user-plus text-white text-xl"></i>
              </div>
              <div>
                <h2 class="text-4xl font-bold text-white">Create New User</h2>
                <p class="text-blue-100 text-lg">Add a new user with roles and permissions</p>
              </div>
            </div>
            <button
              @click="closeCreateUserModal"
              class="w-10 h-10 bg-white/10 hover:bg-white/20 rounded-lg transition-colors duration-200 flex items-center justify-center"
            >
              <i class="fas fa-times text-white"></i>
            </button>
          </div>
        </div>

        <!-- Modal Body -->
        <div class="p-6">
          <form @submit.prevent="createUser" class="space-y-6">
            <!-- Personal Information Section -->
            <div class="bg-white/10 rounded-xl p-8 backdrop-blur-sm border border-blue-300/30">
              <h3 class="text-3xl font-semibold text-white mb-6 flex items-center">
                <i class="fas fa-user mr-3 text-blue-300 text-2xl"></i>
                Personal Information
              </h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label class="block text-xl font-bold text-blue-100 mb-3">Name *</label>
                  <input
                    v-model="newUser.name"
                    type="text"
                    class="medical-input w-full px-5 py-4 text-xl bg-white/15 border-2 border-blue-300/30 rounded-xl focus:border-blue-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm"
                    placeholder="Enter full name"
                    required
                  />
                  <p v-if="userFormErrors.name" class="text-red-400 text-xs mt-1">{{ userFormErrors.name }}</p>
                </div>
                <div>
                  <label class="block text-lg font-bold text-blue-100 mb-3">Email Address *</label>
                  <input
                    v-model="newUser.email"
                    type="email"
                    class="medical-input w-full px-5 py-4 text-lg bg-white/15 border-2 border-blue-300/30 rounded-xl focus:border-blue-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm"
                    placeholder="user@example.com"
                    required
                  />
                  <p v-if="userFormErrors.email" class="text-red-400 text-xs mt-1">{{ userFormErrors.email }}</p>
                </div>
                <div>
                  <label class="block text-lg font-bold text-blue-100 mb-3">Phone Number *</label>
                  <input
                    v-model="newUser.phone"
                    type="tel"
                    class="medical-input w-full px-5 py-4 text-lg bg-white/15 border-2 border-blue-300/30 rounded-xl focus:border-blue-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm"
                    placeholder="Enter phone number"
                    required
                  />
                  <p v-if="userFormErrors.phone" class="text-red-400 text-xs mt-1">{{ userFormErrors.phone }}</p>
                </div>
                <div>
                  <label class="block text-lg font-bold text-blue-100 mb-3">PF Number</label>
                  <input
                    v-model="newUser.pf_number"
                    type="text"
                    class="medical-input w-full px-5 py-4 text-lg bg-white/15 border-2 border-blue-300/30 rounded-xl focus:border-blue-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm"
                    placeholder="Enter PF number (optional)"
                  />
                  <p v-if="userFormErrors.pf_number" class="text-red-400 text-xs mt-1">{{ userFormErrors.pf_number }}</p>
                </div>
                <div>
                  <label class="block text-lg font-bold text-blue-100 mb-3">Password *</label>
                  <div class="relative">
                    <input
                      v-model="newUser.password"
                      :type="showPassword ? 'text' : 'password'"
                      class="medical-input w-full px-5 py-4 text-lg bg-white/15 border-2 border-blue-300/30 rounded-xl focus:border-blue-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm pr-12"
                      placeholder="Enter password"
                      required
                    />
                    <button
                      type="button"
                      @click="showPassword = !showPassword"
                      class="absolute right-3 top-1/2 transform -translate-y-1/2 text-blue-300 hover:text-blue-200"
                    >
                      <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                    </button>
                  </div>
                  <p v-if="userFormErrors.password" class="text-red-400 text-xs mt-1">{{ userFormErrors.password }}</p>
                </div>
                <div>
                  <label class="block text-lg font-bold text-blue-100 mb-3">Confirm Password *</label>
                  <div class="relative">
                    <input
                      v-model="newUser.password_confirmation"
                      :type="showConfirmPassword ? 'text' : 'password'"
                      class="medical-input w-full px-5 py-4 text-lg bg-white/15 border-2 border-blue-300/30 rounded-xl focus:border-blue-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm pr-12"
                      placeholder="Confirm password"
                      required
                    />
                    <button
                      type="button"
                      @click="showConfirmPassword = !showConfirmPassword"
                      class="absolute right-3 top-1/2 transform -translate-y-1/2 text-blue-300 hover:text-blue-200"
                    >
                      <i :class="showConfirmPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                    </button>
                  </div>
                  <p v-if="userFormErrors.password_confirmation" class="text-red-400 text-xs mt-1">{{ userFormErrors.password_confirmation }}</p>
                  <p v-if="!userFormErrors.password_confirmation && newUser.password_confirmation && newUser.password !== newUser.password_confirmation" class="text-red-400 text-xs mt-1">Passwords do not match</p>
                </div>
              </div>
            </div>

            <!-- Department and Role Assignment Section -->
            <div class="bg-white/10 rounded-xl p-8 backdrop-blur-sm border border-blue-300/30">
              <h3 class="text-2xl font-bold text-white mb-6 flex items-center">
                <i class="fas fa-building mr-3 text-blue-300 text-xl"></i>
                Department & Role Assignment
              </h3>
              <div class="mb-6">
                <label class="flex items-center space-x-4 cursor-pointer">
                  <input
                    v-model="newUser.is_active"
                    type="checkbox"
                    class="w-5 h-5 text-blue-600 bg-white/20 border-blue-300/50 rounded focus:ring-blue-500 focus:ring-2"
                  />
                  <span class="text-lg font-bold text-blue-100">User is active</span>
                </label>
                <p class="text-base text-blue-200/70 mt-2">Inactive users cannot log in</p>
                <p v-if="userFormErrors.is_active" class="text-red-400 text-xs mt-1">{{ userFormErrors.is_active }}</p>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label class="block text-lg font-bold text-blue-100 mb-3">Department</label>
                  <div class="relative">
                    <select
                      v-model="newUser.department_id"
                      class="medical-input w-full px-5 py-4 text-lg bg-white/15 border-2 border-blue-300/30 rounded-xl focus:border-blue-400 focus:outline-none text-white backdrop-blur-sm appearance-none cursor-pointer"
                    >
                      <option value="" class="bg-blue-900 text-white">Select Department (Optional)</option>
                      <option
                        v-for="department in availableDepartments"
                        :key="department.id"
                        :value="department.id"
                        class="bg-blue-900 text-white"
                      >
                        {{ department.display_name || department.name }}
                      </option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                      <i class="fas fa-chevron-down text-blue-300"></i>
                    </div>
                  </div>
                  <p v-if="userFormErrors.department_id" class="text-red-400 text-xs mt-1">{{ userFormErrors.department_id }}</p>
                </div>
                <div>
                  <label class="block text-lg font-bold text-blue-100 mb-3">Primary Role</label>
                  <div class="relative">
                    <select
                      v-model="newUser.primary_role"
                      class="medical-input w-full px-5 py-4 text-lg bg-white/15 border-2 border-blue-300/30 rounded-xl focus:border-blue-400 focus:outline-none text-white backdrop-blur-sm appearance-none cursor-pointer"
                    >
                      <option value="" class="bg-blue-900 text-white">Select Primary Role (Optional)</option>
                      <option
                        v-for="role in availableRoles"
                        :key="role.id"
                        :value="role.id"
                        class="bg-blue-900 text-white"
                      >
                        {{ role.display_name || role.name }}
                      </option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                      <i class="fas fa-chevron-down text-blue-300"></i>
                    </div>
                  </div>
                  <p v-if="userFormErrors.primary_role" class="text-red-400 text-xs mt-1">{{ userFormErrors.primary_role }}</p>
                </div>
              </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-4 pt-4 border-t border-blue-300/30">
              <button
                type="button"
                @click="closeCreateUserModal"
                class="px-6 py-3 bg-gray-600 text-white rounded-xl font-semibold hover:bg-gray-700 transition-all duration-300 flex items-center space-x-2"
              >
                <i class="fas fa-times"></i>
                <span>Cancel</span>
              </button>
              <button
                type="submit"
                :disabled="creatingUser"
                class="px-6 py-3 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-xl font-semibold hover:from-emerald-600 hover:to-emerald-700 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed flex items-center space-x-2"
              >
                <i :class="creatingUser ? 'fas fa-spinner fa-spin' : 'fas fa-user-plus'"></i>
                <span>{{ creatingUser ? 'Creating...' : 'Create User' }}</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Create Role Modal -->
    <div
      v-if="showCreateRoleModal"
      class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4"
      @click="closeCreateRoleModal"
    >
      <div
        class="bg-blue-900 rounded-2xl shadow-2xl w-full max-w-7xl max-h-[90vh] overflow-y-auto"
        @click.stop
      >
        <!-- Modal Header -->
        <div class="bg-red-800 p-8 rounded-t-2xl border-b border-blue-500/30">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-6">
              <div class="w-16 h-16 bg-blue-500/30 rounded-xl flex items-center justify-center">
                <i class="fas fa-shield-alt text-white text-2xl"></i>
              </div>
              <div>
                <h2 class="text-4xl font-bold text-white">Create New Role</h2>
                <p class="text-blue-100 text-lg">Define a new role with specific permissions</p>
              </div>
            </div>
            <button
              @click="closeCreateRoleModal"
              class="w-12 h-12 bg-white/10 hover:bg-white/20 rounded-lg transition-colors duration-200 flex items-center justify-center"
            >
              <i class="fas fa-times text-white text-lg"></i>
            </button>
          </div>
        </div>

        <!-- Modal Body -->
        <div class="p-8">
          <form @submit.prevent="createRole" class="space-y-8">
            <!-- Role Information Section -->
            <div class="bg-white/10 rounded-xl p-8 backdrop-blur-sm border border-blue-300/30">
              <h3 class="text-2xl font-semibold text-white mb-6 flex items-center">
                <i class="fas fa-info-circle mr-3 text-blue-300 text-xl"></i>
                Role Information
              </h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label class="block text-base font-medium text-blue-100 mb-3">Role Name *</label>
                  <input
                    v-model="newRole.name"
                    type="text"
                    class="medical-input w-full px-5 py-4 text-lg bg-white/15 border-2 border-blue-300/30 rounded-xl focus:border-blue-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm"
                    placeholder="e.g. senior_manager"
                    required
                  />
                  <p class="text-blue-200/70 text-sm mt-2">Use lowercase with underscores (used internally)</p>
                  <p v-if="roleFormErrors.name" class="text-red-400 text-xs mt-1">{{ roleFormErrors.name }}</p>
                </div>
                <div>
                  <label class="block text-base font-medium text-blue-100 mb-3">Display Name *</label>
                  <input
                    v-model="newRole.display_name"
                    type="text"
                    class="medical-input w-full px-5 py-4 text-lg bg-white/15 border-2 border-blue-300/30 rounded-xl focus:border-blue-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm"
                    placeholder="e.g. Senior Manager"
                    required
                  />
                  <p class="text-blue-200/70 text-sm mt-2">Human-readable name (shown to users)</p>
                  <p v-if="roleFormErrors.display_name" class="text-red-400 text-xs mt-1">{{ roleFormErrors.display_name }}</p>
                </div>
              </div>
              <div class="mt-6">
                <label class="block text-base font-medium text-blue-100 mb-3">Description</label>
                <textarea
                  v-model="newRole.description"
                  rows="4"
                  class="medical-input w-full px-5 py-4 text-lg bg-white/15 border-2 border-blue-300/30 rounded-xl focus:border-blue-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm resize-none"
                  placeholder="Describe the role and its responsibilities (optional)"
                ></textarea>
                <p v-if="roleFormErrors.description" class="text-red-400 text-xs mt-1">{{ roleFormErrors.description }}</p>
              </div>
            </div>

            <!-- Permissions Section -->
            <div class="bg-white/10 rounded-xl p-8 backdrop-blur-sm border border-blue-300/30">
              <h3 class="text-2xl font-semibold text-white mb-6 flex items-center justify-between">
                <div class="flex items-center">
                  <i class="fas fa-key mr-3 text-blue-300 text-xl"></i>
                  Permissions
                </div>
                <div class="flex items-center space-x-3">
                  <button
                    type="button"
                    @click="selectAllPermissions"
                    class="text-sm px-4 py-2 bg-blue-500/30 text-blue-100 rounded-lg hover:bg-blue-500/40 transition-colors font-medium"
                  >
                    Select All
                  </button>
                  <button
                    type="button"
                    @click="clearAllPermissions"
                    class="text-sm px-4 py-2 bg-red-500/30 text-red-100 rounded-lg hover:bg-red-500/40 transition-colors font-medium"
                  >
                    Clear All
                  </button>
                </div>
              </h3>
              
              <div v-if="loadingPermissions" class="text-center py-8">
                <div class="inline-flex items-center space-x-2 text-blue-100">
                  <i class="fas fa-spinner fa-spin text-xl"></i>
                  <span>Loading permissions...</span>
                </div>
              </div>
              
              <div v-else-if="availablePermissions.length > 0" class="space-y-6">
                <!-- Group permissions by category if they have prefixes -->
                <div v-for="(permissionGroup, category) in groupedPermissions" :key="category" class="space-y-3">
                  <div class="flex items-center space-x-3 mb-4">
                    <h4 class="text-base font-semibold text-blue-200 capitalize">{{ category }} Permissions</h4>
                    <div class="flex-1 h-px bg-blue-300/30"></div>
                  </div>
                  <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">
                    <div
                      v-for="permission in permissionGroup"
                      :key="permission.id"
                      class="flex items-center space-x-3 p-3 rounded-lg hover:bg-white/10 transition-colors"
                    >
                      <input
                        :id="'permission-' + permission.id"
                        v-model="newRole.permissions"
                        :value="permission.id"
                        type="checkbox"
                        class="w-5 h-5 text-blue-600 bg-white/20 border-blue-300/50 rounded focus:ring-blue-500 focus:ring-2"
                      />
                      <label
                        :for="'permission-' + permission.id"
                        class="text-base text-blue-100 cursor-pointer hover:text-white transition-colors flex-1"
                      >
                        {{ permission.display_name || permission.name }}
                      </label>
                    </div>
                  </div>
                </div>
              </div>
              
              <div v-else class="text-center py-8 text-blue-300">
                <i class="fas fa-key text-2xl mb-2 opacity-50"></i>
                <p>No permissions available</p>
              </div>
              
              <p v-if="roleFormErrors.permissions" class="text-red-400 text-xs mt-2">{{ roleFormErrors.permissions }}</p>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-6 pt-6 border-t border-blue-300/30">
              <button
                type="button"
                @click="closeCreateRoleModal"
                class="px-8 py-4 text-lg bg-gray-600 text-white rounded-xl font-semibold hover:bg-gray-700 transition-all duration-300 flex items-center space-x-2"
              >
                <i class="fas fa-times text-lg"></i>
                <span>Cancel</span>
              </button>
              <button
                type="submit"
                :disabled="creatingRole"
                class="px-8 py-4 text-lg bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-xl font-semibold hover:from-purple-600 hover:to-purple-700 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed flex items-center space-x-2"
              >
                <i :class="creatingRole ? 'fas fa-spinner fa-spin text-lg' : 'fas fa-shield-alt text-lg'"></i>
                <span>{{ creatingRole ? 'Creating...' : 'Create Role' }}</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Assign Roles Modal -->
    <div
      v-if="showAssignRolesModal && selectedUser"
      class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4"
      @click="closeAssignRolesModal"
    >
      <div
        class="bg-blue-900 rounded-2xl shadow-2xl w-full max-w-5xl max-h-[90vh] overflow-y-auto"
        @click.stop
      >
        <!-- Modal Header -->
        <div class="bg-blue-800 p-6 rounded-t-2xl border-b border-blue-500/30">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
              <div class="w-12 h-12 bg-blue-500/30 rounded-xl flex items-center justify-center">
                <i class="fas fa-user-edit text-white text-xl"></i>
              </div>
              <div>
                <h2 class="text-2xl font-bold text-white">Assign Roles</h2>
                <p class="text-blue-100">Manage roles for {{ selectedUser.name }}</p>
              </div>
            </div>
            <button
              @click="closeAssignRolesModal"
              class="w-10 h-10 bg-white/10 hover:bg-white/20 rounded-lg transition-colors duration-200 flex items-center justify-center"
            >
              <i class="fas fa-times text-white"></i>
            </button>
          </div>
        </div>

        <!-- Modal Body -->
        <form @submit.prevent="assignRolesToUser" class="p-6">
          <!-- User Info -->
          <div class="bg-white/10 rounded-xl p-4 mb-6 backdrop-blur-sm border border-blue-300/30">
            <div class="flex items-center space-x-4">
              <div class="w-16 h-16 bg-gradient-to-br from-teal-500 to-blue-600 rounded-full flex items-center justify-center">
                <span class="text-white font-bold text-lg">{{ getInitials(selectedUser.name) }}</span>
              </div>
              <div>
                <h3 class="text-xl font-bold text-white">{{ selectedUser.name }}</h3>
                <p class="text-blue-100">{{ selectedUser.email }}</p>
                <p v-if="selectedUser.department && selectedUser.department.display_name" class="text-blue-200 text-sm">
                  <i class="fas fa-building mr-1"></i>{{ selectedUser.department.display_name }}
                </p>
              </div>
            </div>
          </div>

          <!-- Current Roles -->
          <div class="mb-6">
            <h3 class="text-lg font-semibold text-white mb-3 flex items-center">
              <i class="fas fa-shield-alt mr-2 text-blue-300"></i>
              Current Roles
            </h3>
            <div v-if="selectedUser.roles && selectedUser.roles.length > 0" class="flex flex-wrap gap-2">
              <span
                v-for="role in selectedUser.roles"
                :key="role.id"
                :class="[
                  'px-3 py-1 rounded-lg text-sm border',
                  getRoleColorClasses(role.name)
                ]"
              >
                {{ role.display_name || role.name }}
              </span>
            </div>
            <div v-else class="px-3 py-1 bg-gray-500/30 text-gray-100 rounded text-sm border border-gray-400/50">
              No roles currently assigned
            </div>
          </div>

            <!-- Role Assignment -->
          <div class="mb-6">
            <h3 class="text-lg font-semibold text-white mb-3 flex items-center">
              <i class="fas fa-tasks mr-2 text-blue-300"></i>
              Assign Roles
            </h3>
            
            <!-- Loading state for roles -->
            <div v-if="loadingRoles" class="text-center py-8">
              <div class="inline-flex items-center space-x-2 text-blue-100">
                <i class="fas fa-spinner fa-spin text-xl"></i>
                <span>Loading available roles...</span>
              </div>
            </div>
            
            <!-- No roles found -->
            <div v-else-if="!availableRoles || availableRoles.length === 0" class="text-center py-8">
              <div class="text-blue-100">
                <i class="fas fa-exclamation-triangle text-4xl mb-4 opacity-50"></i>
                <p class="text-lg mb-2">No roles available</p>
                <p class="text-sm opacity-75">Unable to load roles for assignment</p>
                <button @click="fetchRoles" class="mt-4 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                  <i class="fas fa-refresh mr-2"></i>Retry
                </button>
              </div>
            </div>
            
            <!-- Roles grid -->
            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">
              <div
                v-for="role in availableRoles"
                :key="role.id"
                class="flex items-center space-x-3 p-3 rounded-lg hover:bg-white/10 transition-colors border border-blue-300/20"
              >
                <input
                  :id="'assign-role-' + role.id"
                  v-model="userRoleAssignment.selectedRoles"
                  :value="role.id"
                  type="checkbox"
                  class="w-4 h-4 text-blue-600 bg-white/20 border-blue-300/50 rounded focus:ring-blue-500 focus:ring-2"
                />
                <label
                  :for="'assign-role-' + role.id"
                  class="text-sm text-blue-100 cursor-pointer hover:text-white transition-colors flex-1"
                >
                  <div class="font-medium">{{ role.display_name || role.name }}</div>
                  <div v-if="role.description" class="text-xs text-blue-200/70 mt-1">{{ role.description }}</div>
                </label>
              </div>
            </div>
            <p v-if="roleAssignmentErrors.role_ids" class="text-red-400 text-xs mt-2">{{ roleAssignmentErrors.role_ids[0] }}</p>
          </div>

          <!-- Form Actions -->
          <div class="flex justify-end space-x-4 pt-4 border-t border-blue-300/30">
            <button
              type="button"
              @click="closeAssignRolesModal"
              class="px-6 py-3 bg-gray-600 text-white rounded-xl font-semibold hover:bg-gray-700 transition-all duration-300 flex items-center space-x-2"
            >
              <i class="fas fa-times"></i>
              <span>Cancel</span>
            </button>
            <button
              type="submit"
              :disabled="assigningRoles"
              class="px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl font-semibold hover:from-blue-600 hover:to-blue-700 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed flex items-center space-x-2"
            >
              <i :class="assigningRoles ? 'fas fa-spinner fa-spin' : 'fas fa-user-edit'"></i>
              <span>{{ assigningRoles ? 'Updating...' : 'Update Roles' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Role History Modal -->
    <div
      v-if="showRoleHistoryModal && selectedUser"
      class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4"
      @click="closeRoleHistoryModal"
    >
      <div
        class="bg-gradient-to-br from-blue-900 via-blue-800 to-teal-900 rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto"
        @click.stop
      >
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-red-600 to-red-700 p-6 rounded-t-2xl border-b border-red-500/30">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
              <div class="w-12 h-12 bg-red-500/30 rounded-xl flex items-center justify-center">
                <i class="fas fa-history text-white text-xl"></i>
              </div>
              <div>
                <h2 class="text-2xl font-bold text-white">Role History</h2>
                <p class="text-red-100">Role changes for {{ selectedUser.name }}</p>
              </div>
            </div>
            <button
              @click="closeRoleHistoryModal"
              class="w-10 h-10 bg-white/10 hover:bg-white/20 rounded-lg transition-colors duration-200 flex items-center justify-center"
            >
              <i class="fas fa-times text-white"></i>
            </button>
          </div>
        </div>

        <!-- Modal Body -->
        <div class="p-6">
          <!-- User Info -->
          <div class="bg-white/10 rounded-xl p-4 mb-6 backdrop-blur-sm border border-blue-300/30">
            <div class="flex items-center space-x-4">
              <div class="w-16 h-16 bg-gradient-to-br from-teal-500 to-blue-600 rounded-full flex items-center justify-center">
                <span class="text-white font-bold text-lg">{{ getInitials(selectedUser.name) }}</span>
              </div>
              <div>
                <h3 class="text-xl font-bold text-white">{{ selectedUser.name }}</h3>
                <p class="text-blue-100">{{ selectedUser.email }}</p>
                <p v-if="selectedUser.department && selectedUser.department.display_name" class="text-blue-200 text-sm">
                  <i class="fas fa-building mr-1"></i>{{ selectedUser.department.display_name }}
                </p>
              </div>
            </div>
          </div>

          <!-- Loading State -->
          <div v-if="loadingRoleHistory" class="text-center py-8">
            <div class="inline-flex items-center space-x-2 text-blue-100">
              <i class="fas fa-spinner fa-spin text-xl"></i>
              <span>Loading role history...</span>
            </div>
          </div>

          <!-- Role History Timeline -->
          <div v-else-if="roleHistory.length > 0" class="space-y-4">
            <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
              <i class="fas fa-clock mr-2 text-blue-300"></i>
              Role Change Timeline
            </h3>
            <div class="space-y-3">
              <div
                v-for="(change, index) in roleHistory"
                :key="change.id || index"
                class="bg-white/10 rounded-xl p-4 backdrop-blur-sm border border-blue-300/20 hover:bg-white/15 transition-colors"
              >
                <div class="flex items-start justify-between">
                  <div class="flex items-start space-x-4">
                    <!-- Action Icon -->
                    <div
                      :class="[
                        'w-10 h-10 rounded-full flex items-center justify-center shadow-lg',
                        change.action === 'assigned' ? 'bg-green-500/30 text-green-100' : 'bg-red-500/30 text-red-100'
                      ]"
                    >
                      <i :class="change.action === 'assigned' ? 'fas fa-plus' : 'fas fa-minus'"></i>
                    </div>
                    
                    <!-- Change Details -->
                    <div class="flex-1">
                      <div class="flex items-center space-x-3 mb-2">
                        <span
                          :class="[
                            'px-2 py-1 rounded text-xs font-semibold',
                            change.action === 'assigned' ? 'bg-green-500/30 text-green-100' : 'bg-red-500/30 text-red-100'
                          ]"
                        >
                          {{ change.action === 'assigned' ? 'Role Added' : 'Role Removed' }}
                        </span>
                        <span
                          v-if="change.role"
                          :class="[
                            'px-2 py-1 rounded text-xs border',
                            getRoleColorClasses(change.role.name)
                          ]"
                        >
                          {{ change.role.display_name || change.role.name }}
                        </span>
                      </div>
                      
                      <p class="text-blue-100 text-sm mb-1">
                        <span class="font-medium">{{ change.role?.display_name || change.role?.name || 'Unknown Role' }}</span>
                        {{ change.action === 'assigned' ? 'was assigned to' : 'was removed from' }} this user
                      </p>
                      
                      <div class="flex items-center space-x-4 text-xs text-blue-200/70">
                        <span v-if="change.changed_by">
                          <i class="fas fa-user mr-1"></i>
                          Changed by: {{ change.changed_by.name || 'Unknown' }}
                        </span>
                        <span v-if="change.changed_at">
                          <i class="fas fa-calendar mr-1"></i>
                          {{ new Date(change.changed_at).toLocaleDateString() }} at {{ new Date(change.changed_at).toLocaleTimeString() }}
                        </span>
                      </div>
                      
                      <!-- Metadata -->
                      <div v-if="change.metadata && (change.metadata.context || change.metadata.changed_by_email)" class="mt-2 text-xs text-blue-200/50">
                        <div v-if="change.metadata.context" class="mb-1">
                          <i class="fas fa-info-circle mr-1"></i>
                          Context: {{ change.metadata.context }}
                        </div>
                        <div v-if="change.metadata.changed_by_email">
                          <i class="fas fa-envelope mr-1"></i>
                          By: {{ change.metadata.changed_by_email }}
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Timestamp -->
                  <div class="text-xs text-blue-200/70 text-right">
                    <div v-if="change.changed_at">{{ new Date(change.changed_at).toLocaleDateString() }}</div>
                    <div v-if="change.changed_at">{{ new Date(change.changed_at).toLocaleTimeString() }}</div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- No History -->
          <div v-else class="text-center py-8 text-blue-300">
            <i class="fas fa-history text-4xl mb-4 opacity-50"></i>
            <h3 class="text-lg font-medium mb-2">No Role History</h3>
            <p class="text-blue-200/70">No role changes have been recorded for this user yet.</p>
          </div>

          <!-- Close Button -->
          <div class="flex justify-end pt-6 border-t border-blue-300/30">
            <button
              @click="closeRoleHistoryModal"
              class="px-6 py-3 bg-gray-600 text-white rounded-xl font-semibold hover:bg-gray-700 transition-all duration-300 flex items-center space-x-2"
            >
              <i class="fas fa-times"></i>
              <span>Close</span>
            </button>
          </div>
        </div>
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
  import adminUserService from '@/services/adminUserService'

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

      // Modal states
      const showCreateUserModal = ref(false)
      const showCreateRoleModal = ref(false)
      const showAssignRolesModal = ref(false)
      const showRoleHistoryModal = ref(false)
      const creatingUser = ref(false)
      const creatingRole = ref(false)
      const assigningRoles = ref(false)
      const loadingRoleHistory = ref(false)
      const loadingPermissions = ref(false)
      const loadingRoles = ref(false)
      const showPassword = ref(false)
      const showConfirmPassword = ref(false)

      // Form data
      const newUser = ref({
        name: '',
        email: '',
        phone: '',
        pf_number: '',
        password: '',
        password_confirmation: '',
        department_id: '',
        primary_role: '',
        is_active: true
      })

      const newRole = ref({
        name: '',
        display_name: '',
        description: '',
        permissions: []
      })

      // Form validation errors
      const userFormErrors = ref({})
      const roleFormErrors = ref({})

      // Permissions data
      const availablePermissions = ref([])

      // Role assignment and history data
      const selectedUser = ref(null)
      const userRoleAssignment = ref({
        selectedRoles: []
      })
      const roleHistory = ref([])
      const roleAssignmentErrors = ref({})

      // Snackbar
      const showSnackbar = ref(false)
      const snackbarMessage = ref('')
      const snackbarColor = ref('success')

      // Computed properties
      const filteredUsers = computed(() => {
        // Ensure users.value is an array before processing
        if (!Array.isArray(users.value)) {
          console.warn('users.value is not an array:', users.value)
          return []
        }

        console.log('Total users loaded:', users.value.length)
        if (users.value.length > 0) {
          console.log('Sample user structure:', {
            name: users.value[0].name,
            roles: users.value[0].roles
          })
        }
        
        let filtered = [...users.value]

        // Apply search filter
        if (searchQuery.value) {
          const query = searchQuery.value.toLowerCase()
          filtered = filtered.filter(
            (user) =>
              user.name && user.name.toLowerCase().includes(query) ||
              user.email && user.email.toLowerCase().includes(query) ||
              (user.pf_number && user.pf_number.toLowerCase().includes(query))
          )
        }

        // Apply role filter
        if (filterRole.value) {
          console.log('Filtering by role:', filterRole.value)
          console.log('Available users before role filter:', filtered.length)
          console.log('Sample user roles:', filtered.length > 0 ? filtered[0].roles : 'No users')
          
          filtered = filtered.filter((user) => {
            const hasRole = user.roles && Array.isArray(user.roles) && user.roles.some((role) => {
              const matches = role.name === filterRole.value
              if (user.roles.length > 0) {
                console.log(`User ${user.name} has roles:`, user.roles.map(r => r.name), `Looking for: ${filterRole.value}, Match: ${matches}`)
              }
              return matches
            })
            return hasRole
          })
          
          console.log('Users after role filter:', filtered.length)
        }

        // Apply department filter
        if (filterDepartment.value) {
          filtered = filtered.filter((user) => user.department_id == filterDepartment.value)
        }

        // Apply sorting
        filtered.sort((a, b) => {
          const aValue = (a[sortBy.value] || '').toString()
          const bValue = (b[sortBy.value] || '').toString()
          return aValue.localeCompare(bValue)
        })

        return filtered
      })

      // Group permissions by category
      const groupedPermissions = computed(() => {
        const groups = {}
        
        availablePermissions.value.forEach(permission => {
          // Try to determine category from permission name
          const parts = permission.name.split('.')
          const category = parts.length > 1 ? parts[0] : 'general'
          
          if (!groups[category]) {
            groups[category] = []
          }
          groups[category].push(permission)
        })
        
        return groups
      })

      // Methods
      const initializeData = async () => {
        loading.value = true
        try {
          // Initialize users as empty array to prevent errors
          if (!Array.isArray(users.value)) {
            users.value = []
          }
          
          // Fetch all data including filter options
          const results = await Promise.allSettled([
            fetchUsers(),
            fetchUserStatistics(),
            fetchRoles(),
            fetchDepartments()
          ])
          
          // Log any failures but don't throw
          results.forEach((result, index) => {
            const labels = ['users', 'statistics', 'roles', 'departments']
            if (result.status === 'rejected') {
              console.error(`Data fetch ${labels[index]} failed:`, result.reason)
            }
          })
          
        } catch (error) {
          console.error('Error initializing data:', error)
          // Ensure arrays are still initialized even if something fails
          users.value = users.value || []
          availableRoles.value = availableRoles.value || []
          availableDepartments.value = availableDepartments.value || []
        } finally {
          loading.value = false
        }
      }

      const fetchUsers = async () => {
        try {
          console.log('Fetching users...')
          const response = await adminUserService.getAllUsers()
          
          // Ensure we have a proper response structure
          console.log('Raw user response structure:', response)
          if (response && response.success && response.data) {
            // Handle success response structure
            if (Array.isArray(response.data.users)) {
              users.value = response.data.users
            } else if (Array.isArray(response.data)) {
              users.value = response.data
            } else {
              console.warn('Unexpected success response structure:', response.data)
              users.value = []
            }
          } else if (response && response.data) {
            // Handle different possible response structures (fallback)
            if (Array.isArray(response.data)) {
              users.value = response.data
            } else if (response.data.users && Array.isArray(response.data.users)) {
              users.value = response.data.users
            } else if (response.data.data && Array.isArray(response.data.data)) {
              users.value = response.data.data
            } else {
              console.warn('Unexpected response structure:', response)
              users.value = []
            }
          } else {
            console.warn('No data in response:', response)
            users.value = []
          }
          
          console.log('Fetched users successfully:', users.value.length)
          console.log('All users fetched:', users.value.map(u => ({
            name: u.name,
            email: u.email,
            roles: u.roles ? u.roles.map(r => r.name) : []
          })))
        } catch (error) {
          console.error('Error fetching users:', error)
          // Always ensure users.value is an array
          users.value = []
          
          // Show user-friendly message if it's a network error
          if (error.code === 'NETWORK_ERROR' || error.message?.includes('Network Error')) {
            showErrorMessage('Network error: Unable to fetch users. Please check your connection.')
          } else if (error.response?.status === 401) {
            showErrorMessage('Authentication error: Please log in again.')
          } else if (error.response?.status >= 500) {
            showErrorMessage('Server error: Unable to fetch users. Please try again later.')
          }
        }
      }

      const fetchUserStatistics = async () => {
        try {
          const response = await adminUserService.getUserStatistics()
          userStatistics.value = response.data || {
            total_users: 0,
            users_with_roles: 0,
            department_distribution: [],
            hod_users: 0
          }
          console.log('Fetched user statistics')
        } catch (error) {
          console.error('Error fetching user statistics:', error)
          // Set default values but don't throw
          userStatistics.value = {
            total_users: 0,
            users_with_roles: 0,
            department_distribution: [],
            hod_users: 0
          }
        }
      }

      const refreshData = async () => {
        await initializeData()
      }
      
      const debugFilterData = () => {
        console.log('=== DEBUG FILTER DATA ===')
        console.log('Total users loaded:', users.value.length)
        console.log('Available roles:', availableRoles.value.map(r => ({ id: r.id, name: r.name, display: r.display_name })))
        console.log('Current filter role:', filterRole.value)
        console.log('Users with super_admin role:')
        users.value.forEach(user => {
          if (user.roles && user.roles.some(role => role.name === 'super_admin')) {
            console.log(`  - ${user.name} (${user.email}) has roles:`, user.roles.map(r => r.name))
          }
        })
        console.log('Users with any roles:')
        users.value.forEach(user => {
          if (user.roles && user.roles.length > 0) {
            console.log(`  - ${user.name} has roles:`, user.roles.map(r => r.name))
          } else {
            console.log(`  - ${user.name} has NO roles`)
          }
        })
        console.log('==========================')
      }

      const debouncedSearch = debounce(() => {
        // Search is handled by computed property
      }, 500)

      const applyFilters = () => {
        // Filters are handled by computed property
      }

      // Modal methods
      const openCreateUserModal = async () => {
        showCreateUserModal.value = true
        userFormErrors.value = {}
        
        // Load form data (departments and roles)
        try {
          const [departmentsResponse, rolesResponse] = await Promise.all([
            adminUserService.getDepartments(),
            adminUserService.getRoles()
          ])
          
          availableDepartments.value = departmentsResponse.data || []
          availableRoles.value = rolesResponse.data || []
        } catch (error) {
          console.error('Error loading form data:', error)
          showErrorMessage('Failed to load form data')
        }
      }

      const closeCreateUserModal = () => {
        showCreateUserModal.value = false
        resetUserForm()
      }

      const openCreateRoleModal = async () => {
        showCreateRoleModal.value = true
        roleFormErrors.value = {}
        
        // Load permissions
        await fetchPermissions()
      }

      const closeCreateRoleModal = () => {
        showCreateRoleModal.value = false
        resetRoleForm()
      }

      // Form reset methods
      const resetUserForm = () => {
        newUser.value = {
          name: '',
          email: '',
          phone: '',
          pf_number: '',
          password: '',
          password_confirmation: '',
          department_id: '',
          primary_role: '',
          is_active: true
        }
        userFormErrors.value = {}
        showPassword.value = false
        showConfirmPassword.value = false
      }


      const resetRoleForm = () => {
        newRole.value = {
          name: '',
          display_name: '',
          description: '',
          permissions: []
        }
        roleFormErrors.value = {}
      }

      // Permission methods
      const fetchPermissions = async () => {
        loadingPermissions.value = true
        try {
          const response = await adminUserService.getPermissions()
          availablePermissions.value = response.data || []
        } catch (error) {
          console.error('Error fetching permissions:', error)
          // Fallback to default permissions if API fails
          availablePermissions.value = [
            { id: 1, name: 'users.view', display_name: 'View Users' },
            { id: 2, name: 'users.create', display_name: 'Create Users' },
            { id: 3, name: 'users.edit', display_name: 'Edit Users' },
            { id: 4, name: 'users.delete', display_name: 'Delete Users' },
            { id: 5, name: 'roles.view', display_name: 'View Roles' },
            { id: 6, name: 'roles.create', display_name: 'Create Roles' },
            { id: 7, name: 'roles.edit', display_name: 'Edit Roles' },
            { id: 8, name: 'roles.delete', display_name: 'Delete Roles' },
            { id: 9, name: 'departments.view', display_name: 'View Departments' },
            { id: 10, name: 'departments.manage', display_name: 'Manage Departments' },
            { id: 11, name: 'requests.view', display_name: 'View Requests' },
            { id: 12, name: 'requests.approve', display_name: 'Approve Requests' },
            { id: 13, name: 'admin.full_access', display_name: 'Full Admin Access' }
          ]
        } finally {
          loadingPermissions.value = false
        }
      }

      const selectAllPermissions = () => {
        newRole.value.permissions = availablePermissions.value.map(p => p.id)
      }

      const clearAllPermissions = () => {
        newRole.value.permissions = []
      }

      // Form submission methods
      const createUser = async () => {
        creatingUser.value = true
        userFormErrors.value = {}

        // Client-side validation for password confirmation
        if (newUser.value.password !== newUser.value.password_confirmation) {
          userFormErrors.value = {
            password_confirmation: ['Passwords do not match.']
          }
          creatingUser.value = false
          return
        }

        try {
          const userData = {
            name: newUser.value.name.trim(),
            email: newUser.value.email.trim().toLowerCase(),
            phone: newUser.value.phone.trim(),
            pf_number: newUser.value.pf_number.trim() || null,
            password: newUser.value.password,
            password_confirmation: newUser.value.password_confirmation,
            department_id: newUser.value.department_id || null,
            is_active: newUser.value.is_active,
            role_ids: [] // Backend expects 'role_ids', not 'roles'
          }

          // Add primary role if selected
          if (newUser.value.primary_role) {
            userData.role_ids.push(parseInt(newUser.value.primary_role))
          }
          
          // If no roles selected, assign default 'staff' role
          if (userData.role_ids.length === 0) {
            // Find staff role ID from available roles
            const staffRole = availableRoles.value.find(role => role.name === 'staff')
            if (staffRole) {
              userData.role_ids.push(staffRole.id)
            } else {
              // If no staff role found, show error
              throw new Error('No default role available. Please select a role or create a staff role first.')
            }
          }

          const response = await adminUserService.createUser(userData)
          
          if (response.success) {
            showSuccessMessage('User created successfully!')
            closeCreateUserModal()
            await refreshData() // Refresh the user list
          } else {
            throw new Error(response.message || 'Failed to create user')
          }
        } catch (error) {
          console.error('Error creating user:', error)
          
          // Handle validation errors
          if (error.response?.status === 422 && error.response.data?.errors) {
            userFormErrors.value = error.response.data.errors
          } else {
            showErrorMessage(error.message || 'Failed to create user')
          }
        } finally {
          creatingUser.value = false
        }
      }

      const createRole = async () => {
        creatingRole.value = true
        roleFormErrors.value = {}

        try {
          const roleData = {
            name: newRole.value.name.trim(),
            display_name: newRole.value.display_name.trim(),
            description: newRole.value.description.trim() || null,
            permissions: newRole.value.permissions
          }

          const response = await adminUserService.createRole(roleData)
          
          if (response.success) {
            showSuccessMessage('Role created successfully!')
            closeCreateRoleModal()
            await refreshData() // Refresh data
          } else {
            throw new Error(response.message || 'Failed to create role')
          }
          
        } catch (error) {
          console.error('Error creating role:', error)
          
          // Handle validation errors
          if (error.response?.status === 422 && error.response.data?.errors) {
            roleFormErrors.value = error.response.data.errors
          } else {
            showErrorMessage(error.message || 'Failed to create role')
          }
        } finally {
          creatingRole.value = false
        }
      }

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

      const showSuccessMessage = (message) => {
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

      // Fetch roles function
      const fetchRoles = async () => {
        loadingRoles.value = true
        try {
          console.log('Fetching roles...')
          const response = await adminUserService.getRoles()
          console.log('Raw response:', response)
          
          if (response && response.success && response.data) {
            availableRoles.value = Array.isArray(response.data) ? response.data : []
            console.log('Fetched roles successfully for filters:', availableRoles.value.length, 'roles loaded')
            console.log('Available roles for filters:', availableRoles.value.map(r => r.display_name || r.name))
          } else {
            console.warn('Invalid response structure:', response)
            availableRoles.value = []
          }
        } catch (error) {
          console.error('Error fetching roles:', error)
          console.error('Error details:', {
            message: error.message,
            response: error.response?.data,
            status: error.response?.status
          })
          availableRoles.value = []
          showErrorMessage('Failed to load available roles: ' + (error.response?.data?.message || error.message))
        } finally {
          loadingRoles.value = false
        }
      }

      // Fetch departments function
      const fetchDepartments = async () => {
        try {
          console.log('Fetching departments...')
          const response = await adminUserService.getDepartments()
          console.log('Raw departments response:', response)
          
          if (response && response.success && response.data) {
            availableDepartments.value = Array.isArray(response.data) ? response.data : []
            console.log('Fetched departments successfully for filters:', availableDepartments.value.length, 'departments loaded')
            console.log('Available departments for filters:', availableDepartments.value.map(d => d.display_name || d.name))
          } else if (response && response.data) {
            // Fallback for different response structure
            availableDepartments.value = Array.isArray(response.data) ? response.data : []
            console.log('Fetched departments with fallback structure:', availableDepartments.value.length)
          } else {
            availableDepartments.value = []
            console.log('No departments data received')
          }
        } catch (error) {
          console.error('Error fetching departments:', error)
          availableDepartments.value = []
          showErrorMessage('Failed to load departments')
        }
      }

      // Role assignment and history methods
      const openAssignRolesDialog = async (user) => {
        try {
          console.log('Opening assign roles dialog for user:', user.name)
          selectedUser.value = user
          userRoleAssignment.value.selectedRoles = user.roles ? user.roles.map(role => role.id) : []
          roleAssignmentErrors.value = {}
          showAssignRolesModal.value = true
          
          // Always fetch roles to ensure we have the latest data
          console.log('Current availableRoles length:', availableRoles.value.length)
          await fetchRoles()
          
          if (availableDepartments.value.length === 0) {
            await fetchDepartments()
          }
        } catch (error) {
          console.error('Error opening assign roles dialog:', error)
          showErrorMessage('Failed to open role assignment dialog')
        }
      }

      const viewRoleHistory = async (user) => {
        try {
          selectedUser.value = user
          loadingRoleHistory.value = true
          showRoleHistoryModal.value = true
          roleHistory.value = []
          
          // Fetch role history
          const response = await adminUserService.getUserRoleHistory(user.id)
          if (response.success) {
            roleHistory.value = response.data.data || []
          } else {
            throw new Error(response.message || 'Failed to fetch role history')
          }
        } catch (error) {
          console.error('Error fetching role history:', error)
          showErrorMessage('Failed to load role history')
          roleHistory.value = []
        } finally {
          loadingRoleHistory.value = false
        }
      }

      const closeAssignRolesModal = () => {
        showAssignRolesModal.value = false
        selectedUser.value = null
        userRoleAssignment.value.selectedRoles = []
        roleAssignmentErrors.value = {}
      }

      const closeRoleHistoryModal = () => {
        showRoleHistoryModal.value = false
        selectedUser.value = null
        roleHistory.value = []
      }

      const assignRolesToUser = async () => {
        if (!selectedUser.value) return
        
        assigningRoles.value = true
        roleAssignmentErrors.value = {}
        
        try {
          const response = await adminUserService.assignUserRoles(
            selectedUser.value.id,
            userRoleAssignment.value.selectedRoles
          )
          
          if (response.success) {
            console.log('Role assignment successful:', response)
            showSuccessMessage('Roles updated successfully!')
            closeAssignRolesModal()
            console.log('Refreshing data after role assignment...')
            await refreshData() // Refresh user list to show updated roles
            console.log('Data refresh completed')
          } else {
            throw new Error(response.message || 'Failed to assign roles')
          }
        } catch (error) {
          console.error('Error assigning roles:', error)
          
          // Handle validation errors
          if (error.response?.status === 422 && error.response.data?.errors) {
            roleAssignmentErrors.value = error.response.data.errors
          } else {
            showErrorMessage(error.message || 'Failed to assign roles')
          }
        } finally {
          assigningRoles.value = false
        }
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

        // Modal states
        showCreateUserModal,
        showCreateRoleModal,
        showAssignRolesModal,
        showRoleHistoryModal,
        creatingUser,
        creatingRole,
        assigningRoles,
        loadingRoleHistory,
        loadingPermissions,
        loadingRoles,
        showPassword,
        showConfirmPassword,

        // Form data
        newUser,
        newRole,
        userFormErrors,
        roleFormErrors,
        availablePermissions,
        selectedUser,
        userRoleAssignment,
        roleHistory,
        roleAssignmentErrors,

        // Computed
        filteredUsers,
        groupedPermissions,

        // Methods
        refreshData,
        debugFilterData,
        debouncedSearch,
        applyFilters,
        getInitials,
        getRoleColorClasses,
        fetchRoles,
        fetchDepartments,
        openAssignRolesDialog,
        viewRoleHistory,

        // Modal methods
        openCreateUserModal,
        closeCreateUserModal,
        openCreateRoleModal,
        closeCreateRoleModal,
        closeAssignRolesModal,
        closeRoleHistoryModal,

        // Form methods
        createUser,
        createRole,
        assignRolesToUser,
        selectAllPermissions,
        clearAllPermissions,
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

  /* Force blue background for all input and select elements */
  input.medical-input, textarea.medical-input {
    background-color: rgba(30, 58, 138, 0.6) !important;
    border-color: rgba(96, 165, 250, 0.5) !important;
    color: white !important;
  }

  input.medical-input:focus, textarea.medical-input:focus {
    background-color: rgba(30, 58, 138, 0.7) !important;
    border-color: rgba(45, 212, 191, 0.8) !important;
    color: white !important;
  }

  input.medical-input::placeholder, textarea.medical-input::placeholder {
    color: rgba(191, 219, 254, 0.7) !important;
  }

  /* Handle browser autofill forcing white background */
  input.medical-input:-webkit-autofill,
  input.medical-input:-webkit-autofill:hover,
  input.medical-input:-webkit-autofill:focus,
  textarea.medical-input:-webkit-autofill,
  textarea.medical-input:-webkit-autofill:hover,
  textarea.medical-input:-webkit-autofill:focus {
    -webkit-box-shadow: 0 0 0px 1000px rgba(30, 58, 138, 0.6) inset !important;
    box-shadow: 0 0 0px 1000px rgba(30, 58, 138, 0.6) inset !important;
    -webkit-text-fill-color: #ffffff !important;
    caret-color: #ffffff !important;
    border-color: rgba(96, 165, 250, 0.5) !important;
    transition: background-color 9999s ease-in-out 0s !important;
  }

  input.medical-input:-webkit-autofill:focus,
  textarea.medical-input:-webkit-autofill:focus {
    -webkit-box-shadow: 0 0 0px 1000px rgba(30, 58, 138, 0.7) inset !important;
    box-shadow: 0 0 0px 1000px rgba(30, 58, 138, 0.7) inset !important;
    border-color: rgba(45, 212, 191, 0.8) !important;
  }

  /* Firefox */
  input.medical-input:-moz-autofill,
  textarea.medical-input:-moz-autofill {
    box-shadow: 0 0 0px 1000px rgba(30, 58, 138, 0.6) inset !important;
    -moz-appearance: none !important;
    color: #ffffff !important;
    caret-color: #ffffff !important;
    border-color: rgba(96, 165, 250, 0.5) !important;
  }

  /* Force blue background for all select dropdowns */
  select.medical-input {
    background-color: rgba(30, 58, 138, 0.9) !important;
    color: white !important;
  }

  select.medical-input option {
    background-color: #1e3a8a !important;
    color: white !important;
    padding: 8px !important;
  }

  select.medical-input option:hover {
    background-color: #1d4ed8 !important;
    color: white !important;
  }

  select.medical-input option:checked {
    background-color: #2563eb !important;
    color: white !important;
  }
</style>
