<template>
  <div class="flex flex-col h-screen">
    <AppHeader />
    <div class="flex flex-1 overflow-hidden">
      <DynamicSidebar v-model:collapsed="sidebarCollapsed" />
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
                    'fa-shield-alt',
                    'fa-users-cog',
                    'fa-key',
                    'fa-user-shield',
                    'fa-cogs',
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
                      <i class="fas fa-shield-alt"></i>
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
                  ROLE MANAGEMENT SYSTEM
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
                    Role Statistics
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
                        <i class="fas fa-shield-check text-white text-lg"></i>
                      </div>
                      <div>
                        <div class="text-2xl font-bold text-white">
                          {{ roleStatistics.total_roles || 0 }}
                        </div>
                        <div class="text-sm text-blue-100">Total Roles</div>
                      </div>
                    </div>
                  </div>

                  <div
                    class="bg-white/15 p-4 rounded-xl backdrop-blur-sm border border-green-300/30 hover:bg-white/20 transition-all duration-300 hover:shadow-lg hover:shadow-green-500/20 group"
                  >
                    <div class="flex items-center space-x-3">
                      <div
                        class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300"
                      >
                        <i class="fas fa-cog text-white text-lg"></i>
                      </div>
                      <div>
                        <div class="text-2xl font-bold text-white">
                          {{ roleStatistics.system_roles || 0 }}
                        </div>
                        <div class="text-sm text-green-100">System Roles</div>
                      </div>
                    </div>
                  </div>

                  <div
                    class="bg-white/15 p-4 rounded-xl backdrop-blur-sm border border-purple-300/30 hover:bg-white/20 transition-all duration-300 hover:shadow-lg hover:shadow-purple-500/20 group"
                  >
                    <div class="flex items-center space-x-3">
                      <div
                        class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300"
                      >
                        <i class="fas fa-user-edit text-white text-lg"></i>
                      </div>
                      <div>
                        <div class="text-2xl font-bold text-white">
                          {{ roleStatistics.custom_roles || 0 }}
                        </div>
                        <div class="text-sm text-purple-100">Custom Roles</div>
                      </div>
                    </div>
                  </div>

                  <div
                    class="bg-white/15 p-4 rounded-xl backdrop-blur-sm border border-orange-300/30 hover:bg-white/20 transition-all duration-300 hover:shadow-lg hover:shadow-orange-500/20 group"
                  >
                    <div class="flex items-center space-x-3">
                      <div
                        class="w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300"
                      >
                        <i class="fas fa-users text-white text-lg"></i>
                      </div>
                      <div>
                        <div class="text-2xl font-bold text-white">
                          {{ roleStatistics.roles_with_users || 0 }}
                        </div>
                        <div class="text-sm text-orange-100">Roles in Use</div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Role Management Section -->
              <div
                class="medical-card bg-gradient-to-r from-teal-600/25 to-blue-600/25 border-2 border-teal-400/40 p-6 rounded-2xl backdrop-blur-sm hover:shadow-2xl hover:shadow-teal-500/20 transition-all duration-500 group"
              >
                <div class="flex items-center justify-between mb-6">
                  <div class="flex items-center space-x-4">
                    <div
                      class="w-14 h-14 bg-gradient-to-br from-teal-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-teal-300/50"
                    >
                      <i class="fas fa-shield-alt text-white text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white flex items-center">
                      <i class="fas fa-list mr-2 text-teal-300"></i>
                      Role Management ({{ rolesPagination.total || 0 }} total roles)
                    </h3>
                  </div>
                  <button
                    @click="openCreateRoleDialog"
                    :disabled="!canManageRoles"
                    class="bg-gradient-to-r from-green-500 to-emerald-500 text-white py-3 px-6 rounded-xl font-semibold hover:from-green-600 hover:to-emerald-600 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed transform hover:scale-105 shadow-lg flex items-center space-x-2"
                  >
                    <i class="fas fa-plus"></i>
                    <span>Create New Role</span>
                  </button>
                </div>



                <!-- Filters and Search -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                  <div>
                    <label class="block text-sm font-bold text-teal-100 mb-2">
                      Search Roles
                    </label>
                    <div class="relative">
                      <input
                        v-model="searchQuery"
                        type="text"
                        class="medical-input w-full px-4 py-3 bg-white/15 border-2 border-teal-300/30 rounded-xl focus:border-cyan-400 focus:outline-none text-white placeholder-teal-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-cyan-500/20"
                        placeholder="Search by role name..."
                        @input="debouncedSearch"
                      />
                      <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <i class="fas fa-search text-teal-300"></i>
                      </div>
                    </div>
                  </div>

                  <div>
                    <label class="block text-sm font-bold text-teal-100 mb-2">
                      Filter by Type
                    </label>
                    <div class="relative">
                      <select
                        v-model="filterType"
                        class="medical-input w-full px-4 py-3 bg-white/15 border-2 border-teal-300/30 rounded-xl focus:border-cyan-400 focus:outline-none text-white backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-cyan-500/20 appearance-none cursor-pointer"
                        @change="applyFilters"
                      >
                        <option value="" class="bg-gray-800 text-white">
                          All Types
                        </option>
                        <option value="system" class="bg-gray-800 text-white">
                          System Roles
                        </option>
                        <option value="custom" class="bg-gray-800 text-white">
                          Custom Roles
                        </option>
                        <option value="deletable" class="bg-gray-800 text-white">
                          Deletable Roles
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
                        <option value="sort_order" class="bg-gray-800 text-white">
                          Sort Order
                        </option>
                        <option value="name" class="bg-gray-800 text-white">
                          Name
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
                      Actions
                    </label>
                    <button
                      @click="refreshRoles"
                      :disabled="rolesLoading"
                      class="w-full bg-gradient-to-r from-blue-500 to-cyan-500 text-white py-3 px-4 rounded-xl font-semibold hover:from-blue-600 hover:to-cyan-600 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed transform hover:scale-105 shadow-lg flex items-center justify-center space-x-2"
                    >
                      <i :class="rolesLoading ? 'fas fa-spinner fa-spin' : 'fas fa-refresh'"></i>
                      <span>{{ rolesLoading ? 'Loading...' : 'Refresh' }}</span>
                    </button>
                  </div>
                </div>

                <!-- Roles List -->
                <div v-if="rolesLoading" class="text-center py-8">
                  <div class="inline-flex items-center space-x-2 text-teal-100">
                    <i class="fas fa-spinner fa-spin text-xl"></i>
                    <span>Loading roles...</span>
                  </div>
                </div>

                <div v-else-if="allRoles.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                  <div
                    v-for="role in allRoles"
                    :key="role.id"
                    class="bg-white/15 p-6 rounded-xl backdrop-blur-sm border border-teal-300/30 hover:bg-white/20 transition-all duration-300 hover:shadow-lg hover:shadow-teal-500/20 group"
                  >
                    <div class="flex items-start justify-between mb-4">
                      <div class="flex-1">
                        <div class="flex items-center space-x-2 mb-2">
                          <div
                            :class="[
                              'w-3 h-3 rounded-full',
                              role.is_system_role ? 'bg-blue-400' : 'bg-purple-400'
                            ]"
                          ></div>
                          <h4 class="font-bold text-white text-lg">{{ role.name }}</h4>
                        </div>
                        <p class="text-teal-100 text-sm mb-2">
                          {{ role.description || 'No description' }}
                        </p>
                        <div class="flex items-center space-x-4 text-xs text-teal-200">
                          <span class="flex items-center space-x-1">
                            <i class="fas fa-users"></i>
                            <span>{{ role.users_count || 0 }} users</span>
                          </span>
                          <span class="flex items-center space-x-1">
                            <i class="fas fa-key"></i>
                            <span>{{ (role.permissions || []).length }} permissions</span>
                          </span>
                        </div>
                      </div>
                    </div>

                    <!-- Role Type Badge -->
                    <div class="flex items-center justify-between mb-4">
                      <div
                        :class="[
                          'px-3 py-1 rounded-full text-xs font-semibold',
                          role.is_system_role
                            ? 'bg-blue-500/30 text-blue-100 border border-blue-400/50'
                            : 'bg-purple-500/30 text-purple-100 border border-purple-400/50'
                        ]"
                      >
                        {{ role.is_system_role ? 'System Role' : 'Custom Role' }}
                      </div>
                      <div class="text-xs text-teal-200">
                        {{ formatDate(role.created_at) }}
                      </div>
                    </div>

                    <!-- Permissions Preview -->
                    <div v-if="role.permissions && role.permissions.length > 0" class="mb-4">
                      <div class="text-xs text-teal-100 mb-2">Permissions:</div>
                      <div class="flex flex-wrap gap-1">
                        <span
                          v-for="permission in role.permissions.slice(0, 3)"
                          :key="permission"
                          class="px-2 py-1 bg-teal-500/30 text-teal-100 rounded text-xs border border-teal-400/50"
                        >
                          {{ formatPermissionName(permission) }}
                        </span>
                        <span
                          v-if="role.permissions.length > 3"
                          class="px-2 py-1 bg-gray-500/30 text-gray-100 rounded text-xs border border-gray-400/50"
                        >
                          +{{ role.permissions.length - 3 }} more
                        </span>
                      </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex space-x-2">
                      <button
                        @click="viewRole(role)"
                        class="flex-1 bg-gradient-to-r from-blue-500 to-blue-600 text-white py-2 px-3 rounded-lg text-xs font-semibold hover:from-blue-600 hover:to-blue-700 transition-all duration-300 transform hover:scale-105 shadow-lg flex items-center justify-center space-x-1"
                      >
                        <i class="fas fa-eye"></i>
                        <span>View</span>
                      </button>

                      <button
                        @click="editRole(role)"
                        :disabled="!canEditRole(role)"
                        class="flex-1 bg-gradient-to-r from-orange-500 to-orange-600 text-white py-2 px-3 rounded-lg text-xs font-semibold hover:from-orange-600 hover:to-orange-700 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed transform hover:scale-105 shadow-lg flex items-center justify-center space-x-1"
                      >
                        <i class="fas fa-edit"></i>
                        <span>Edit</span>
                      </button>

                      <button
                        @click="confirmDeleteRole(role)"
                        :disabled="!canDeleteRole(role)"
                        class="flex-1 bg-gradient-to-r from-red-500 to-red-600 text-white py-2 px-3 rounded-lg text-xs font-semibold hover:from-red-600 hover:to-red-700 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed transform hover:scale-105 shadow-lg flex items-center justify-center space-x-1"
                      >
                        <i class="fas fa-trash"></i>
                        <span>Delete</span>
                      </button>
                    </div>
                  </div>
                </div>

                <!-- No Roles Found -->
                <div v-else class="text-center py-8">
                  <div class="text-teal-100">
                    <i class="fas fa-shield-alt text-4xl mb-4 opacity-50"></i>
                    <p class="text-lg">No roles found</p>
                    <p class="text-sm opacity-75">
                      {{ searchQuery ? 'Try adjusting your search criteria' : 'Create your first role to get started' }}
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>
      <!-- Footer -->
      <AppFooter />
    </div>




    <!-- Create/Edit Role Dialog -->
    <div
      v-if="roleDialog"
      class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 backdrop-blur-sm"
    >
      <div
        class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto transform transition-all duration-300 scale-100 animate-slideUp"
      >
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-6 text-center">
          <div
            class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4 backdrop-blur-sm border border-white/30"
          >
            <i
              :class="[
                'text-white text-2xl',
                isEditMode ? 'fas fa-edit' : 'fas fa-plus'
              ]"
            ></i>
          </div>
          <h3 class="text-xl font-bold text-white mb-2">
            {{ isEditMode ? 'Edit Role' : 'Create New Role' }}
          </h3>
          <div class="w-12 h-1 bg-white/50 mx-auto rounded-full"></div>
        </div>

        <!-- Body -->
        <div class="p-6">
          <form ref="roleForm" @submit.prevent="saveRole">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Role Name *
                </label>
                <input
                  v-model="roleForm.name"
                  type="text"
                  required
                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:border-blue-500 focus:outline-none transition-all duration-300"
                  placeholder="Enter role name"
                />
                <div v-if="getFieldError('name')" class="text-red-500 text-sm mt-1">
                  {{ getFieldError('name') }}
                </div>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Sort Order
                </label>
                <input
                  v-model="roleForm.sort_order"
                  type="number"
                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:border-blue-500 focus:outline-none transition-all duration-300"
                  placeholder="Lower numbers appear first"
                />
              </div>
            </div>

            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Description
              </label>
              <textarea
                v-model="roleForm.description"
                rows="3"
                maxlength="1000"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:border-blue-500 focus:outline-none transition-all duration-300 resize-none"
                placeholder="Enter role description"
              ></textarea>
              <div v-if="getFieldError('description')" class="text-red-500 text-sm mt-1">
                {{ getFieldError('description') }}
              </div>
              <div class="text-gray-500 text-sm mt-1">
                {{ (roleForm.description || '').length }}/1000 characters
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
              <div class="flex items-center space-x-3">
                <input
                  v-model="roleForm.is_system_role"
                  type="checkbox"
                  :disabled="isEditMode && selectedRole?.is_system_role"
                  class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                />
                <div>
                  <label class="text-sm font-medium text-gray-700">
                    System Role
                  </label>
                  <p class="text-xs text-gray-500">
                    System roles cannot be deleted
                  </p>
                </div>
              </div>
              <div class="flex items-center space-x-3">
                <input
                  v-model="roleForm.is_deletable"
                  type="checkbox"
                  :disabled="roleForm.is_system_role"
                  class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                />
                <div>
                  <label class="text-sm font-medium text-gray-700">
                    Deletable
                  </label>
                  <p class="text-xs text-gray-500">
                    Allow this role to be deleted
                  </p>
                </div>
              </div>
            </div>

            <!-- Permissions Section -->
            <div class="border-t border-gray-200 pt-6">
              <div class="flex items-center space-x-2 mb-4">
                <i class="fas fa-key text-blue-600"></i>
                <h4 class="text-lg font-semibold text-gray-800">Permissions</h4>
              </div>

              <div
                v-for="(permissions, category) in availablePermissions"
                :key="category"
                class="mb-6"
              >
                <h5 class="text-md font-medium text-gray-700 mb-3 capitalize">
                  {{ category.replace('_', ' ') }}
                </h5>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                  <div
                    v-for="permission in permissions"
                    :key="permission"
                    class="flex items-center space-x-2"
                  >
                    <input
                      v-model="roleForm.permissions"
                      :value="permission"
                      type="checkbox"
                      class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                    />
                    <label class="text-sm text-gray-700">
                      {{ formatPermissionName(permission) }}
                    </label>
                  </div>
                </div>
              </div>
            </div>
          </form>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end space-x-3 p-6 border-t border-gray-200">
          <button
            @click="closeRoleDialog"
            :disabled="roleFormSubmitting"
            class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-medium hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Cancel
          </button>
          <button
            @click="saveRole"
            :disabled="roleFormSubmitting || !roleForm.name"
            class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl font-medium hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed flex items-center space-x-2"
          >
            <i v-if="roleFormSubmitting" class="fas fa-spinner fa-spin"></i>
            <i v-else :class="isEditMode ? 'fas fa-save' : 'fas fa-plus'"></i>
            <span>{{ roleFormSubmitting ? 'Saving...' : (isEditMode ? 'Update' : 'Create') }}</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Dialog -->
    <div
      v-if="deleteDialog"
      class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 backdrop-blur-sm"
    >
      <div
        class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 transform transition-all duration-300 scale-100 animate-slideUp overflow-hidden"
      >
        <!-- Header -->
        <div class="bg-gradient-to-r from-red-500 to-red-600 p-6 text-center">
          <div
            class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4 backdrop-blur-sm border border-white/30"
          >
            <i class="fas fa-exclamation-triangle text-white text-2xl"></i>
          </div>
          <h3 class="text-xl font-bold text-white mb-2">
            Confirm Delete
          </h3>
          <div class="w-12 h-1 bg-white/50 mx-auto rounded-full"></div>
        </div>

        <!-- Body -->
        <div class="p-6 text-center">
          <div class="mb-6">
            <div
              class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg"
            >
              <i class="fas fa-shield-alt text-red-600 text-lg"></i>
            </div>
            <h4 class="text-lg font-semibold text-gray-800 mb-3">
              {{ roleToDelete?.name }}
            </h4>
            <p class="text-gray-600 text-sm leading-relaxed">
              Are you sure you want to delete this role?
            </p>
          </div>

          <!-- Warning for roles with users -->
          <div
            v-if="roleToDelete?.users_count > 0"
            class="bg-orange-50 rounded-xl p-4 mb-6 border border-orange-100 shadow-md"
          >
            <div class="flex items-center justify-center space-x-2 mb-2">
              <div
                class="w-6 h-6 bg-orange-500 rounded-full flex items-center justify-center shadow-lg"
              >
                <i class="fas fa-exclamation text-white text-xs"></i>
              </div>
              <span class="text-sm font-medium text-orange-800">
                Warning: Role in Use
              </span>
            </div>
            <p class="text-sm text-orange-700">
              This role is assigned to {{ roleToDelete.users_count }} user(s). You
              must remove all users from this role before deleting it.
            </p>
          </div>

          <p class="text-xs text-gray-500 mb-6">
            This action cannot be undone.
          </p>

          <!-- Action Buttons -->
          <div class="flex space-x-3">
            <button
              @click="deleteDialog = false"
              class="flex-1 bg-gray-100 text-gray-700 py-3 px-4 rounded-xl font-medium hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2 transition-all duration-200 shadow-md hover:shadow-lg"
            >
              Cancel
            </button>

            <button
              @click="deleteRole"
              :disabled="deleteLoading || roleToDelete?.users_count > 0"
              class="flex-1 bg-gradient-to-r from-red-600 to-red-700 text-white py-3 px-4 rounded-xl font-medium hover:from-red-700 hover:to-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center space-x-2"
            >
              <i v-if="deleteLoading" class="fas fa-spinner fa-spin"></i>
              <i v-else class="fas fa-trash"></i>
              <span>{{ deleteLoading ? 'Deleting...' : 'Delete' }}</span>
            </button>
          </div>
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
import { ref } from 'vue'
import { mapGetters, mapActions } from 'vuex'
import { debounce } from 'lodash'
import AppHeader from '@/components/AppHeader.vue'
import DynamicSidebar from '@/components/DynamicSidebar.vue'
import AppFooter from '@/components/footer.vue'

export default {
  name: 'RoleManagement',
  
  components: {
    AppHeader,
    DynamicSidebar,
    AppFooter
  },

  data() {
    return {
      sidebarCollapsed: false,
      // Table configuration
      headers: [
        { text: 'Role Name', value: 'name', sortable: true },
        { text: 'Description', value: 'description', sortable: false },
        { text: 'Permissions', value: 'permissions', sortable: false },
        { text: 'Users', value: 'users_count', sortable: true },
        { text: 'Created', value: 'created_at', sortable: true },
        { text: 'Actions', value: 'actions', sortable: false, width: '150px' }
      ],

      tableOptions: {
        page: 1,
        itemsPerPage: 15,
        sortBy: ['sort_order'],
        sortDesc: [false]
      },

      // Search and filters
      searchQuery: '',
      filterType: null,
      sortBy: 'sort_order',

      filterOptions: [
        { text: 'All Roles', value: null },
        { text: 'System Roles', value: 'system' },
        { text: 'Custom Roles', value: 'custom' },
        { text: 'Deletable Roles', value: 'deletable' }
      ],

      sortOptions: [
        { text: 'Sort Order', value: 'sort_order' },
        { text: 'Name', value: 'name' },
        { text: 'Created Date', value: 'created_at' }
      ],

      // Dialog states
      roleDialog: false,
      deleteDialog: false,

      // Form data
      roleForm: {
        name: '',
        description: '',
        permissions: [],
        is_system_role: false,
        is_deletable: true,
        sort_order: null
      },

      roleFormValid: false,
      roleFormSubmitting: false,
      roleFormErrors: {},

      // Validation rules
      nameRules: [
        (v) => !!v || 'Role name is required',
        (v) =>
          (v && v.length >= 2) || 'Role name must be at least 2 characters',
        (v) =>
          (v && v.length <= 255) ||
          'Role name must be less than 255 characters',
        (v) =>
          (v && /^[a-zA-Z0-9_\s]+$/.test(v)) ||
          'Role name can only contain letters, numbers, underscores, and spaces'
      ],

      // Delete confirmation
      roleToDelete: null,
      deleteLoading: false,

      // Snackbar
      showSnackbar: false,
      snackbarMessage: '',
      snackbarColor: 'success'
    }
  },

  computed: {
    ...mapGetters('roleManagement', [
      'allRoles',
      'rolesLoading',
      'rolesPagination',
      'roleStatistics',
      'availablePermissions',
      'selectedRole',
      'error',
      'successMessage'
    ]),

    ...mapGetters('auth', ['user', 'userPermissions']),

    isEditMode() {
      return !!this.selectedRole
    },

    canManageRoles() {
      return this.userPermissions.includes('create_roles')
    },

    debouncedSearch() {
      return debounce(this.applyFilters, 500)
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

    roleDialog(val) {
      if (!val) {
        this.resetRoleForm()
      }
    }
  },

  async created() {
    await this.initializeData()
  },

  methods: {
    ...mapActions('roleManagement', [
      'fetchRoles',
      'createRole',
      'updateRole',
      'deleteRole',
      'fetchRoleStatistics',
      'fetchAvailablePermissions',
      'setSelectedRole',
      'clearMessages'
    ]),

    async initializeData() {
      await Promise.all([
        this.fetchRoles(),
        this.fetchRoleStatistics(),
        this.fetchAvailablePermissions()
      ])
    },

    async refreshRoles() {
      await this.fetchRoles(this.getFilterParams())
    },

    async applyFilters() {
      this.tableOptions.page = 1
      await this.fetchRoles(this.getFilterParams())
    },

    async handleTableUpdate(options) {
      this.tableOptions = options
      await this.fetchRoles(this.getFilterParams())
    },

    getFilterParams() {
      return {
        search: this.searchQuery,
        type: this.filterType,
        sort_by: this.sortBy,
        sort_order: this.tableOptions.sortDesc[0] ? 'desc' : 'asc',
        page: this.tableOptions.page,
        per_page: this.tableOptions.itemsPerPage
      }
    },

    // Role dialog methods
    openCreateRoleDialog() {
      this.setSelectedRole(null)
      this.resetRoleForm()
      this.roleDialog = true
    },

    editRole(role) {
      this.setSelectedRole(role)
      this.populateRoleForm(role)
      this.roleDialog = true
    },

    viewRole(role) {
      // Navigate to role details page or show details dialog
      this.$router.push(`/admin/roles/${role.id}`)
    },

    closeRoleDialog() {
      this.roleDialog = false
      this.setSelectedRole(null)
    },

    resetRoleForm() {
      this.roleForm = {
        name: '',
        description: '',
        permissions: [],
        is_system_role: false,
        is_deletable: true,
        sort_order: null
      }
      this.roleFormErrors = {}
      this.roleFormValid = false

      if (this.$refs.roleForm) {
        this.$refs.roleForm.resetValidation()
      }
    },

    populateRoleForm(role) {
      this.roleForm = {
        name: role.name,
        description: role.description || '',
        permissions: [...(role.permissions || [])],
        is_system_role: role.is_system_role,
        is_deletable: role.is_deletable,
        sort_order: role.sort_order
      }
    },

    async saveRole() {
      if (!this.$refs.roleForm.validate()) {
        return
      }

      this.roleFormSubmitting = true
      this.roleFormErrors = {}

      try {
        let result

        if (this.isEditMode) {
          result = await this.updateRole({
            roleId: this.selectedRole.id,
            roleData: this.roleForm
          })
        } else {
          result = await this.createRole(this.roleForm)
        }

        if (result.success) {
          this.closeRoleDialog()
          await this.fetchRoleStatistics()
        } else {
          this.roleFormErrors = result.errors || {}
        }
      } catch (error) {
        console.error('Error saving role:', error)
      } finally {
        this.roleFormSubmitting = false
      }
    },

    // Delete methods
    confirmDeleteRole(role) {
      this.roleToDelete = role
      this.deleteDialog = true
    },

    async deleteRole() {
      if (!this.roleToDelete) return

      this.deleteLoading = true

      try {
        const result = await this.deleteRole(this.roleToDelete.id)

        if (result.success) {
          this.deleteDialog = false
          this.roleToDelete = null
          await this.fetchRoleStatistics()
        }
      } catch (error) {
        console.error('Error deleting role:', error)
      } finally {
        this.deleteLoading = false
      }
    },

    // Permission methods
    canEditRole(role) {
      return (
        this.userPermissions.includes('edit_roles') &&
        (!role.is_system_role || this.user.roles.includes('super_admin'))
      )
    },

    canDeleteRole(role) {
      return (
        this.userPermissions.includes('delete_roles') &&
        role.is_deletable &&
        role.users_count === 0 &&
        (!role.is_system_role || this.user.roles.includes('super_admin'))
      )
    },

    // Utility methods
    formatDate(dateString) {
      if (!dateString) return ''
      return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      })
    },

    formatPermissionName(permission) {
      return permission
        .replace(/_/g, ' ')
        .replace(/\b\w/g, (l) => l.toUpperCase())
    },

    getFieldError(fieldName) {
      return this.roleFormErrors[fieldName]
        ? this.roleFormErrors[fieldName][0]
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

/* Role card hover effects */
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
