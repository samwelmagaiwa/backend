<template>
  <div class="flex flex-col h-screen">
    <AppHeader />
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
                    'fa-building',
                    'fa-user-tie',
                    'fa-crown',
                    'fa-sitemap',
                    'fa-users-cog',
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
                      <i class="fas fa-building"></i>
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
                  DEPARTMENT HOD ASSIGNMENT SYSTEM
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
                    Department Statistics
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
                        <i class="fas fa-building text-white text-lg"></i>
                      </div>
                      <div>
                        <div class="text-2xl font-bold text-white">
                          {{ departmentStatistics.total_departments || 0 }}
                        </div>
                        <div class="text-sm text-blue-100">Total Departments</div>
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
                          {{ departmentStatistics.with_hod || 0 }}
                        </div>
                        <div class="text-sm text-blue-100">With HOD</div>
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
                          {{ departmentStatistics.without_hod || 0 }}
                        </div>
                        <div class="text-sm text-blue-100">Without HOD</div>
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
                        <i class="fas fa-file-alt text-white text-lg"></i>
                      </div>
                      <div>
                        <div class="text-2xl font-bold text-white">
                          {{ departmentStatistics.with_pending_requests || 0 }}
                        </div>
                        <div class="text-sm text-blue-100">With Pending Requests</div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Department Management Section -->
              <div
                class="medical-card bg-gradient-to-r from-teal-600/25 to-blue-600/25 border-2 border-teal-400/40 p-6 rounded-2xl backdrop-blur-sm hover:shadow-2xl hover:shadow-teal-500/20 transition-all duration-500 group"
              >
                <div class="flex items-center justify-between mb-6">
                  <div class="flex items-center space-x-4">
                    <div
                      class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-blue-300/50"
                    >
                      <i class="fas fa-sitemap text-white text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white flex items-center">
                      <i class="fas fa-list mr-2 text-blue-300"></i>
                      Departments and HOD Assignments ({{ departmentsPagination.total || 0 }} total departments)
                    </h3>
                  </div>
                  <div class="flex items-center space-x-3">
                    <button
                      @click="openCreateDepartmentDialog"
                      class="bg-gradient-to-r from-blue-500 to-blue-600 text-white py-3 px-6 rounded-xl font-semibold hover:from-blue-600 hover:to-blue-700 transition-all duration-300 transform hover:scale-105 shadow-lg flex items-center space-x-2"
                    >
                      <i class="fas fa-plus"></i>
                      <span>Create New Department</span>
                    </button>
                    <button
                      @click="refreshData"
                      :disabled="departmentsLoading"
                      class="bg-gradient-to-r from-blue-500 to-blue-600 text-white py-3 px-6 rounded-xl font-semibold hover:from-blue-600 hover:to-blue-700 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed transform hover:scale-105 shadow-lg flex items-center space-x-2"
                    >
                      <i :class="departmentsLoading ? 'fas fa-spinner fa-spin' : 'fas fa-refresh'"></i>
                      <span>{{ departmentsLoading ? 'Loading...' : 'Refresh' }}</span>
                    </button>
                  </div>
                </div>

                <!-- Filters and Search -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                  <div>
                    <label class="block text-sm font-bold text-teal-100 mb-2">
                      Search Departments
                    </label>
                    <div class="relative">
                      <input
                        v-model="searchQuery"
                        type="text"
                        class="medical-input w-full px-4 py-3 bg-white/15 border-2 border-teal-300/30 rounded-xl focus:border-cyan-400 focus:outline-none text-white placeholder-teal-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-cyan-500/20"
                        placeholder="Search by name or code..."
                        @input="debouncedSearch"
                      />
                      <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <i class="fas fa-search text-teal-300"></i>
                      </div>
                    </div>
                  </div>

                  <div>
                    <label class="block text-sm font-bold text-teal-100 mb-2">
                      HOD Status
                    </label>
                    <div class="relative">
                      <select
                        v-model="hodStatus"
                        class="medical-input w-full px-4 py-3 bg-white/15 border-2 border-teal-300/30 rounded-xl focus:border-cyan-400 focus:outline-none text-white backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-cyan-500/20 appearance-none cursor-pointer"
                        @change="applyFilters"
                      >
                        <option value="" class="bg-gray-800 text-white">
                          All Departments
                        </option>
                        <option value="assigned" class="bg-gray-800 text-white">
                          With HOD
                        </option>
                        <option value="unassigned" class="bg-gray-800 text-white">
                          Without HOD
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
                        <option value="code" class="bg-gray-800 text-white">
                          Code
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
                        v-model="showActiveOnly"
                        type="checkbox"
                        @change="applyFilters"
                        class="w-5 h-5 text-teal-600 border-teal-300 rounded focus:ring-teal-500"
                      />
                      <label class="text-sm text-teal-100">
                        Active Only
                      </label>
                    </div>
                  </div>
                </div>

                <!-- Departments List -->
                <div v-if="departmentsWithHodsLoading" class="text-center py-8">
                  <div class="inline-flex items-center space-x-2 text-teal-100">
                    <i class="fas fa-spinner fa-spin text-xl"></i>
                    <span>Loading departments...</span>
                  </div>
                </div>

                <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">


                  <!-- Existing Department Cards -->
                  <div
                    v-for="department in allDepartmentsWithHods"
                    :key="department.id"
                    class="bg-white/15 p-6 rounded-xl backdrop-blur-sm border border-teal-300/30 hover:bg-white/20 transition-all duration-300 hover:shadow-lg hover:shadow-teal-500/20 group"
                  >
                    <div class="flex items-start justify-between mb-4">
                      <div class="flex items-center space-x-3 flex-1">
                        <div class="w-12 h-12 bg-gradient-to-br from-teal-500 to-blue-600 rounded-full flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                          <i class="fas fa-building text-white text-lg"></i>
                        </div>
                        <div class="flex-1">
                          <div class="flex items-center space-x-2 mb-1">
                            <h4 class="font-bold text-white text-lg">{{ department.name }}</h4>
                            <span
                              :class="[
                                'px-2 py-1 rounded text-xs',
                                department.is_active ? 'bg-green-500/30 text-green-100 border border-green-400/50' : 'bg-gray-500/30 text-gray-100 border border-gray-400/50'
                              ]"
                            >
                              {{ department.is_active ? 'Active' : 'Inactive' }}
                            </span>
                          </div>
                          <p class="text-teal-100 text-sm">Code: {{ department.code }}</p>
                          <p v-if="department.description" class="text-teal-200 text-xs mt-1">
                            {{ department.description }}
                          </p>
                        </div>
                      </div>
                    </div>

                    <!-- Current HOD -->
                    <div class="mb-4">
                      <div class="text-xs text-teal-100 mb-2">Current HOD:</div>
                      <div v-if="department.hod" class="bg-blue-500/20 rounded-lg p-3 border border-blue-400/30">
                        <div class="flex items-center space-x-3 mb-2">
                          <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                            <span class="text-white font-bold text-xs">
                              {{ getInitials(department.hod.name) }}
                            </span>
                          </div>
                          <div class="flex-1">
                            <h5 class="font-semibold text-white text-sm">{{ department.hod.name }}</h5>
                            <p class="text-blue-100 text-xs">{{ department.hod.email }}</p>
                            <p v-if="department.hod.pf_number" class="text-blue-200 text-xs">
                              PF: {{ department.hod.pf_number }}
                            </p>
                          </div>
                        </div>
                        <div v-if="department.hod.roles && department.hod.roles.length > 0" class="flex flex-wrap gap-1">
                          <span
                            v-for="role in department.hod.roles"
                            :key="role"
                            class="px-2 py-1 bg-blue-600/30 text-blue-100 rounded text-xs border border-blue-400/50"
                          >
                            {{ role }}
                          </span>
                        </div>
                      </div>
                      <div v-else class="bg-orange-500/20 rounded-lg p-3 border border-orange-400/30 text-center">
                        <div class="flex items-center justify-center space-x-2">
                          <i class="fas fa-exclamation-triangle text-orange-300"></i>
                          <span class="text-orange-100 text-sm font-medium">No HOD Assigned</span>
                        </div>
                      </div>
                    </div>

                    <!-- Pending Requests -->
                    <div class="mb-4">
                      <div class="text-xs text-teal-100 mb-1">Pending Requests:</div>
                      <span
                        :class="[
                          'px-3 py-1 rounded-full text-sm',
                          department.pending_requests_count > 0 ? 'bg-red-500/30 text-red-100 border border-red-400/50' : 'bg-green-500/30 text-green-100 border border-green-400/50'
                        ]"
                      >
                        {{ department.pending_requests_count || 0 }} pending
                      </span>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex space-x-2">
                      <button
                        v-if="!department.hod"
                        @click="openAssignHodDialog(department)"
                        class="flex-1 bg-gradient-to-r from-blue-500 to-blue-600 text-white py-2 px-3 rounded-lg text-xs font-semibold hover:from-blue-600 hover:to-blue-700 transition-all duration-300 transform hover:scale-105 shadow-lg flex items-center justify-center space-x-1"
                      >
                        <i class="fas fa-user-plus"></i>
                        <span>Assign HOD</span>
                      </button>

                      <button
                        v-else
                        @click="openChangeHodDialog(department)"
                        class="flex-1 bg-gradient-to-r from-orange-500 to-orange-600 text-white py-2 px-3 rounded-lg text-xs font-semibold hover:from-orange-600 hover:to-orange-700 transition-all duration-300 transform hover:scale-105 shadow-lg flex items-center justify-center space-x-1"
                      >
                        <i class="fas fa-user-edit"></i>
                        <span>Change HOD</span>
                      </button>

                      <!-- Three-dot Actions Menu -->
                      <div v-if="department.hod" class="relative">
                        <button
                          @click="toggleActionsMenu(department.id)"
                          class="actions-button bg-gradient-to-r from-gray-500 to-gray-600 text-white py-2 px-3 rounded-lg text-xs font-semibold hover:from-gray-600 hover:to-gray-700 transition-all duration-300 transform hover:scale-105 shadow-lg flex items-center justify-center"
                        >
                          <i class="fas fa-ellipsis-v"></i>
                        </button>

                        <!-- Actions Dropdown -->
                        <div
                          v-if="activeActionsMenu === department.id"
                          class="actions-dropdown absolute right-0 top-full mt-2 w-48 bg-white rounded-lg shadow-xl border border-gray-200 z-50 overflow-hidden"
                        >
                          <button
                            @click="editHod(department)"
                            class="w-full px-4 py-3 text-left text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-200 flex items-center space-x-2"
                          >
                            <i class="fas fa-edit text-blue-500"></i>
                            <span>Edit HOD</span>
                          </button>
                          <button
                            @click="updateHod(department)"
                            class="w-full px-4 py-3 text-left text-sm text-gray-700 hover:bg-green-50 hover:text-green-600 transition-colors duration-200 flex items-center space-x-2"
                          >
                            <i class="fas fa-sync-alt text-green-500"></i>
                            <span>Update HOD</span>
                          </button>
                          <button
                            @click="confirmRemoveHod(department)"
                            class="w-full px-4 py-3 text-left text-sm text-gray-700 hover:bg-red-50 hover:text-red-600 transition-colors duration-200 flex items-center space-x-2"
                          >
                            <i class="fas fa-trash text-red-500"></i>
                            <span>Delete HOD</span>
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- No Departments Found -->
                <div v-if="!departmentsWithHodsLoading && allDepartmentsWithHods.length === 0" class="text-center py-8">
                  <div class="text-teal-100">
                    <i class="fas fa-building text-4xl mb-4 opacity-50"></i>
                    <p class="text-lg">No departments found</p>
                    <p class="text-sm opacity-75">
                      {{ searchQuery ? 'Try adjusting your search criteria' : 'No departments available' }}
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


    <!-- Assign/Change HOD Dialog -->
    <div
      v-if="assignHodDialog"
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
            {{ isChangeMode ? 'Change' : 'Assign' }} HOD for {{ selectedDepartment?.name }}
          </h3>
          <div class="w-12 h-1 bg-white/50 mx-auto rounded-full"></div>
        </div>

        <!-- Body -->
        <div class="p-6">
          <div v-if="selectedDepartment" class="mb-6">
            <div class="bg-blue-50 rounded-xl p-4 border border-blue-100 shadow-md">
              <div class="flex items-center space-x-3 mb-3">
                <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center">
                  <i class="fas fa-building text-white"></i>
                </div>
                <div>
                  <h4 class="font-semibold text-gray-800">{{ selectedDepartment.name }}</h4>
                  <p class="text-sm text-gray-600">Code: {{ selectedDepartment.code }}</p>
                  <p v-if="selectedDepartment.description" class="text-xs text-gray-500">
                    {{ selectedDepartment.description }}
                  </p>
                  <p v-if="selectedDepartment.hod && isChangeMode" class="text-sm text-blue-600 font-medium">
                    Current HOD: {{ selectedDepartment.hod.name }}
                  </p>
                </div>
              </div>
            </div>
          </div>

          <form ref="assignHodForm" @submit.prevent="assignHod">
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Select HOD *
              </label>
              <div class="relative">
                <select
                  v-model="selectedHodId"
                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200 transition-all duration-200"
                  required
                >
                  <option value="" disabled>Choose a HOD...</option>
                  <option
                    v-for="hod in availableHods"
                    :key="hod.id"
                    :value="hod.id"
                  >
                    {{ hod.display_name || `${hod.name} (${hod.pf_number}) - ${hod.email}` }}
                  </option>
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                  <i class="fas fa-chevron-down text-gray-400"></i>
                </div>
              </div>
              <div v-if="getFieldError('hod_user_id')" class="text-red-500 text-sm mt-1">
                {{ getFieldError('hod_user_id') }}
              </div>

              <!-- User Details Preview -->
              <div v-if="selectedHodId && selectedHodDetails" class="mt-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <h4 class="text-sm font-semibold text-gray-800 mb-2">Selected User Details:</h4>
                <div class="grid grid-cols-2 gap-2 text-sm">
                  <div><strong>Name:</strong> {{ selectedHodDetails.name }}</div>
                  <div><strong>PF Number:</strong> {{ selectedHodDetails.pf_number }}</div>
                  <div><strong>Email:</strong> {{ selectedHodDetails.email }}</div>
                  <div><strong>Phone:</strong> {{ selectedHodDetails.phone || 'N/A' }}</div>
                  <div><strong>Department:</strong> {{ selectedHodDetails.department?.name || 'N/A' }}</div>
                  <div><strong>Roles:</strong> {{ selectedHodDetails.role_display_names?.join(', ') || 'N/A' }}</div>
                </div>
              </div>
            </div>


          </form>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end space-x-3 p-6 border-t border-gray-200">
          <button
            @click="closeAssignHodDialog"
            :disabled="assignHodSubmitting"
            class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-medium hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Cancel
          </button>
          <button
            @click="assignHod"
            :disabled="assignHodSubmitting || !selectedHodId"
            class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl font-medium hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed flex items-center space-x-2"
          >
            <i v-if="assignHodSubmitting" class="fas fa-spinner fa-spin"></i>
            <i v-else class="fas fa-save"></i>
            <span>{{ assignHodSubmitting ? 'Processing...' : (isChangeMode ? 'Change' : 'Assign') + ' HOD' }}</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Remove HOD Confirmation Dialog -->
    <div
      v-if="removeHodDialog"
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
            Confirm Remove HOD
          </h3>
          <div class="w-12 h-1 bg-white/50 mx-auto rounded-full"></div>
        </div>

        <!-- Body -->
        <div class="p-6 text-center">
          <div class="mb-6">
            <div
              class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg"
            >
              <i class="fas fa-user-minus text-red-600 text-lg"></i>
            </div>
            <h4 class="text-lg font-semibold text-gray-800 mb-3">
              {{ departmentToRemoveHod?.hod?.name }}
            </h4>
            <p class="text-gray-600 text-sm leading-relaxed">
              Are you sure you want to remove this HOD from
              <strong>{{ departmentToRemoveHod?.name }}</strong>?
            </p>
          </div>

          <!-- Warning for pending requests -->
          <div
            v-if="departmentToRemoveHod?.pending_requests_count > 0"
            class="bg-orange-50 rounded-xl p-4 mb-6 border border-orange-100 shadow-md"
          >
            <div class="flex items-center justify-center space-x-2 mb-2">
              <div
                class="w-6 h-6 bg-orange-500 rounded-full flex items-center justify-center shadow-lg"
              >
                <i class="fas fa-exclamation text-white text-xs"></i>
              </div>
              <span class="text-sm font-medium text-orange-800">
                Warning: Pending Requests
              </span>
            </div>
            <p class="text-sm text-orange-700">
              This department has {{ departmentToRemoveHod.pending_requests_count }} pending requests.
              Removing the HOD will affect the approval workflow.
            </p>
          </div>

          <p class="text-xs text-gray-500 mb-6">
            The department will not have an HOD until a new one is assigned.
          </p>

          <!-- Action Buttons -->
          <div class="flex space-x-3">
            <button
              @click="removeHodDialog = false"
              class="flex-1 bg-gray-100 text-gray-700 py-3 px-4 rounded-xl font-medium hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2 transition-all duration-200 shadow-md hover:shadow-lg"
            >
              Cancel
            </button>

            <button
              @click="removeHod"
              :disabled="removeHodLoading"
              class="flex-1 bg-gradient-to-r from-red-600 to-red-700 text-white py-3 px-4 rounded-xl font-medium hover:from-red-700 hover:to-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center space-x-2"
            >
              <i v-if="removeHodLoading" class="fas fa-spinner fa-spin"></i>
              <i v-else class="fas fa-user-minus"></i>
              <span>{{ removeHodLoading ? 'Removing...' : 'Remove HOD' }}</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Create Department Dialog -->
    <div
      v-if="createDepartmentDialog"
      class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 backdrop-blur-sm"
    >
      <div
        class="bg-gradient-to-br from-blue-50 via-white to-blue-50 rounded-3xl shadow-2xl max-w-3xl w-full mx-4 max-h-[95vh] overflow-y-auto transform transition-all duration-300 scale-100 animate-slideUp border-2 border-blue-200/50"
      >
        <!-- Enhanced Header with Multi-Layer Design -->
        <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-blue-800 p-8 text-center relative overflow-hidden rounded-t-3xl">
          <!-- Background Pattern -->
          <div class="absolute inset-0 opacity-10">
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-white/30 to-transparent rounded-full blur-2xl"></div>
            <div class="absolute bottom-0 left-0 w-24 h-24 bg-gradient-to-tr from-white/20 to-transparent rounded-full blur-xl"></div>
          </div>

          <!-- Enhanced Icon Container -->
          <div class="relative z-10">
            <div class="w-20 h-20 bg-gradient-to-br from-white/20 to-white/10 rounded-2xl flex items-center justify-center mx-auto mb-6 backdrop-blur-sm border-2 border-white/30 shadow-2xl relative overflow-hidden group">
              <!-- Icon Background Glow -->
              <div class="absolute inset-0 bg-gradient-to-br from-blue-400/30 to-blue-600/30 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
              <!-- Multi-Layer Icon -->
              <i class="fas fa-building text-white text-3xl relative z-10 drop-shadow-lg group-hover:scale-110 transition-transform duration-300"></i>
              <!-- Animated Particles -->
              <div class="absolute top-2 right-2 w-2 h-2 bg-white/60 rounded-full animate-ping"></div>
              <div class="absolute bottom-3 left-3 w-1.5 h-1.5 bg-blue-300/80 rounded-full animate-pulse"></div>
            </div>

            <!-- Enhanced Title -->
            <h3 class="text-2xl font-bold text-white mb-3 drop-shadow-lg">
              <span class="bg-gradient-to-r from-white to-blue-100 bg-clip-text text-transparent">Create New Department</span>
            </h3>
            <p class="text-blue-100/90 text-sm mb-4 drop-shadow-sm">Set up a new department with advanced configuration options</p>
            <div class="w-16 h-1 bg-gradient-to-r from-white/60 to-white/30 mx-auto rounded-full shadow-sm"></div>
          </div>
        </div>

        <!-- Enhanced Body with Card Design -->
        <div class="p-8 space-y-6">
          <form ref="createDepartmentForm" @submit.prevent="createDepartment">
            <!-- Department Information Card -->
            <div class="bg-gradient-to-r from-blue-50/50 to-blue-100/30 rounded-2xl p-6 border-2 border-blue-200/40 backdrop-blur-sm hover:shadow-lg hover:shadow-blue-500/10 transition-all duration-300 group">
              <div class="flex items-center space-x-3 mb-6">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-blue-300/50">
                  <i class="fas fa-info-circle text-white text-lg"></i>
                </div>
                <h4 class="text-lg font-bold text-gray-800 flex items-center">
                  <span class="bg-gradient-to-r from-blue-600 to-blue-700 bg-clip-text text-transparent">Department Information</span>
                </h4>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Department Name -->
                <div class="group/field">
                  <label class="block text-sm font-bold text-gray-700 mb-3 flex items-center">
                    <i class="fas fa-building mr-2 text-blue-500"></i>
                    Department Name
                    <span class="text-red-500 ml-1">*</span>
                  </label>
                  <div class="relative">
                    <input
                      v-model="newDepartment.name"
                      type="text"
                      class="w-full px-4 py-4 bg-white/80 border-2 border-blue-200/50 rounded-xl focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200/50 transition-all duration-300 hover:bg-white hover:border-blue-300/70 focus:bg-white focus:shadow-lg focus:shadow-blue-500/20 group-hover/field:border-blue-300/70 text-gray-800 placeholder-gray-400"
                      placeholder="Enter department name"
                      required
                    />
                    <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-blue-500/5 to-blue-500/5 opacity-0 group-hover/field:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
                    <div class="absolute right-4 top-1/2 transform -translate-y-1/2 text-blue-400/50">
                      <i class="fas fa-edit"></i>
                    </div>
                  </div>
                  <div v-if="getFieldError('name')" class="text-red-500 text-sm mt-2 flex items-center">
                    <i class="fas fa-exclamation-circle mr-1"></i>
                    {{ getFieldError('name') }}
                  </div>
                </div>

                <!-- Department Code -->
                <div class="group/field">
                  <label class="block text-sm font-bold text-gray-700 mb-3 flex items-center">
                    <i class="fas fa-code mr-2 text-blue-500"></i>
                    Department Code
                    <span class="text-red-500 ml-1">*</span>
                  </label>
                  <div class="relative">
                    <input
                      v-model="newDepartment.code"
                      type="text"
                      class="w-full px-4 py-4 bg-white/80 border-2 border-blue-200/50 rounded-xl focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200/50 transition-all duration-300 hover:bg-white hover:border-blue-300/70 focus:bg-white focus:shadow-lg focus:shadow-blue-500/20 group-hover/field:border-blue-300/70 text-gray-800 placeholder-gray-400"
                      placeholder="Enter department code"
                      required
                    />
                    <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-blue-500/5 to-blue-500/5 opacity-0 group-hover/field:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
                    <div class="absolute right-4 top-1/2 transform -translate-y-1/2 text-blue-400/50">
                      <i class="fas fa-hashtag"></i>
                    </div>
                  </div>
                  <div v-if="getFieldError('code')" class="text-red-500 text-sm mt-2 flex items-center">
                    <i class="fas fa-exclamation-circle mr-1"></i>
                    {{ getFieldError('code') }}
                  </div>
                </div>
              </div>

              <!-- Description -->
              <div class="mt-6 group/field">
                <label class="block text-sm font-bold text-gray-700 mb-3 flex items-center">
                  <i class="fas fa-align-left mr-2 text-blue-500"></i>
                  Description
                  <span class="text-gray-400 text-xs ml-2">(Optional)</span>
                </label>
                <div class="relative">
                  <textarea
                    v-model="newDepartment.description"
                    rows="4"
                    class="w-full px-4 py-4 bg-white/80 border-2 border-blue-200/50 rounded-xl focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200/50 transition-all duration-300 hover:bg-white hover:border-blue-300/70 focus:bg-white focus:shadow-lg focus:shadow-blue-500/20 group-hover/field:border-blue-300/70 text-gray-800 placeholder-gray-400 resize-none"
                    placeholder="Enter department description (optional)"
                  ></textarea>
                  <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-blue-500/5 to-blue-500/5 opacity-0 group-hover/field:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
                  <div class="absolute right-4 top-4 text-blue-400/50">
                    <i class="fas fa-comment-alt"></i>
                  </div>
                </div>
                <div v-if="getFieldError('description')" class="text-red-500 text-sm mt-2 flex items-center">
                  <i class="fas fa-exclamation-circle mr-1"></i>
                  {{ getFieldError('description') }}
                </div>
              </div>
            </div>

            <!-- Department Settings Card -->
            <div class="bg-gradient-to-r from-green-50/50 to-emerald-100/30 rounded-2xl p-6 border-2 border-green-200/40 backdrop-blur-sm hover:shadow-lg hover:shadow-green-500/10 transition-all duration-300 group">
              <div class="flex items-center space-x-3 mb-6">
                <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-green-300/50">
                  <i class="fas fa-cogs text-white text-lg"></i>
                </div>
                <h4 class="text-lg font-bold text-gray-800 flex items-center">
                  <span class="bg-gradient-to-r from-green-600 to-emerald-700 bg-clip-text text-transparent">Department Settings</span>
                </h4>
              </div>

              <!-- Status Toggle -->
              <div class="mb-6">
                <label class="block text-sm font-bold text-gray-700 mb-4 flex items-center">
                  <i class="fas fa-toggle-on mr-2 text-green-500"></i>
                  Department Status
                </label>
                <div class="bg-white/60 rounded-xl p-4 border border-green-200/50 hover:bg-white/80 transition-all duration-300">
                  <label class="flex items-center space-x-4 cursor-pointer group/toggle">
                    <div class="relative">
                      <input
                        v-model="newDepartment.is_active"
                        type="checkbox"
                        class="sr-only"
                      />
                      <div :class="[
                        'w-12 h-6 rounded-full transition-all duration-300 shadow-inner',
                        newDepartment.is_active ? 'bg-gradient-to-r from-green-500 to-emerald-500' : 'bg-gray-300'
                      ]">
                        <div :class="[
                          'w-5 h-5 bg-white rounded-full shadow-lg transform transition-transform duration-300 mt-0.5',
                          newDepartment.is_active ? 'translate-x-6' : 'translate-x-0.5'
                        ]"></div>
                      </div>
                    </div>
                    <div class="flex-1">
                      <span class="text-lg font-semibold text-gray-800 group-hover/toggle:text-green-600 transition-colors duration-300">
                        {{ newDepartment.is_active ? 'Active Department' : 'Inactive Department' }}
                      </span>
                      <p class="text-sm text-gray-600 mt-1">
                        {{ newDepartment.is_active ? 'Department is active and can be assigned HODs and manage requests' : 'Department is inactive and cannot manage requests' }}
                      </p>
                    </div>
                    <div class="text-2xl">
                      <i :class="[
                        'fas transition-all duration-300',
                        newDepartment.is_active ? 'fa-check-circle text-green-500' : 'fa-times-circle text-gray-400'
                      ]"></i>
                    </div>
                  </label>
                </div>
              </div>
            </div>

            <!-- HOD Assignment Card -->
            <div class="bg-gradient-to-r from-purple-50/50 to-indigo-100/30 rounded-2xl p-6 border-2 border-purple-200/40 backdrop-blur-sm hover:shadow-lg hover:shadow-purple-500/10 transition-all duration-300 group">
              <div class="flex items-center space-x-3 mb-6">
                <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-purple-300/50">
                  <i class="fas fa-user-tie text-white text-lg"></i>
                </div>
                <h4 class="text-lg font-bold text-gray-800 flex items-center">
                  <span class="bg-gradient-to-r from-purple-600 to-indigo-700 bg-clip-text text-transparent">HOD Assignment Options</span>
                </h4>
              </div>

              <!-- Auto-assign HOD Option -->
              <div class="bg-white/60 rounded-xl p-5 border border-purple-200/50 hover:bg-white/80 transition-all duration-300">
                <label class="flex items-start space-x-4 cursor-pointer group/hod">
                  <div class="relative mt-1">
                    <input
                      v-model="autoAssignHod"
                      type="checkbox"
                      class="sr-only"
                    />
                    <div :class="[
                      'w-6 h-6 rounded-lg border-2 transition-all duration-300 flex items-center justify-center',
                      autoAssignHod ? 'bg-gradient-to-br from-purple-500 to-indigo-500 border-purple-500' : 'bg-white border-gray-300 group-hover/hod:border-purple-300'
                    ]">
                      <i v-if="autoAssignHod" class="fas fa-check text-white text-sm"></i>
                    </div>
                  </div>
                  <div class="flex-1">
                    <span class="text-lg font-semibold text-gray-800 group-hover/hod:text-purple-600 transition-colors duration-300">
                      Assign HOD immediately after creation
                    </span>
                    <p class="text-sm text-gray-600 mt-2 leading-relaxed">
                      If checked, you'll be prompted to assign a Head of Department immediately after the department is created. This ensures the department is ready to manage requests right away.
                    </p>
                    <div class="flex items-center space-x-2 mt-3">
                      <div class="w-2 h-2 bg-purple-400 rounded-full animate-pulse"></div>
                      <span class="text-xs text-purple-600 font-medium">Recommended for immediate deployment</span>
                    </div>
                  </div>
                  <div class="text-2xl mt-1">
                    <i :class="[
                      'fas transition-all duration-300',
                      autoAssignHod ? 'fa-user-check text-purple-500' : 'fa-user-clock text-gray-400'
                    ]"></i>
                  </div>
                </label>
              </div>
            </div>
          </form>
        </div>

        <!-- Enhanced Action Buttons -->
        <div class="flex justify-between items-center p-8 border-t-2 border-blue-100/50 bg-gradient-to-r from-gray-50/50 to-blue-50/30 rounded-b-3xl">
          <div class="text-sm text-gray-600">
            <i class="fas fa-info-circle mr-2 text-blue-500"></i>
            All required fields must be completed
          </div>
          <div class="flex space-x-4">
            <button
              @click="closeCreateDepartmentDialog"
              :disabled="createDepartmentSubmitting"
              class="px-8 py-4 bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 rounded-xl font-semibold hover:from-gray-200 hover:to-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed transform hover:scale-105 shadow-lg hover:shadow-xl border border-gray-300/50 flex items-center space-x-2"
            >
              <i class="fas fa-times"></i>
              <span>Cancel</span>
            </button>
            <button
              @click="createDepartment"
              :disabled="createDepartmentSubmitting || !newDepartment.name || !newDepartment.code"
              class="px-8 py-4 bg-gradient-to-r from-blue-600 via-blue-700 to-blue-800 text-white rounded-xl font-semibold hover:from-blue-700 hover:via-blue-800 hover:to-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed flex items-center space-x-3 border border-blue-500/50 relative overflow-hidden group/create"
            >
              <!-- Button Shine Effect -->
              <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -skew-x-12 -translate-x-full group-hover/create:translate-x-full transition-transform duration-700"></div>

              <div class="relative z-10 flex items-center space-x-3">
                <div class="w-6 h-6 bg-white/20 rounded-lg flex items-center justify-center">
                  <i v-if="createDepartmentSubmitting" class="fas fa-spinner fa-spin text-sm"></i>
                  <i v-else class="fas fa-building text-sm"></i>
                </div>
                <span class="font-bold">{{ createDepartmentSubmitting ? 'Creating Department...' : 'Create Department' }}</span>
                <i class="fas fa-arrow-right group-hover/create:translate-x-1 transition-transform duration-300"></i>
              </div>
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
import { debounce } from 'lodash'
import AppHeader from '@/components/AppHeader.vue'
import ModernSidebar from '@/components/ModernSidebar.vue'
import AppFooter from '@/components/footer.vue'
import departmentHodService from '@/services/departmentHodService'

export default {
  name: 'DepartmentHodAssignment',

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
        {
          text: 'Department',
          value: 'department_info',
          sortable: false,
          width: '300px'
        },
        { text: 'Current HOD', value: 'hod', sortable: false, width: '300px' },
        {
          text: 'Pending Requests',
          value: 'pending_requests_count',
          sortable: true
        },
        { text: 'Actions', value: 'actions', sortable: false, width: '150px' }
      ],

      tableOptions: {
        page: 1,
        itemsPerPage: 15,
        sortBy: ['name'],
        sortDesc: [false]
      },

      // Search and filters
      searchQuery: '',
      hodStatus: null,
      sortBy: 'name',
      showActiveOnly: true,

      hodStatusOptions: [
        { text: 'All Departments', value: null },
        { text: 'With HOD', value: 'assigned' },
        { text: 'Without HOD', value: 'unassigned' }
      ],

      sortOptions: [
        { text: 'Name', value: 'name' },
        { text: 'Code', value: 'code' },
        { text: 'Created Date', value: 'created_at' }
      ],

      // Dialog states
      assignHodDialog: false,
      removeHodDialog: false,
      createDepartmentDialog: false,

      // Form data
      selectedHodId: null,
      assignHodFormValid: false,
      assignHodSubmitting: false,
      assignHodFormErrors: {},
      isChangeMode: false,

      // Validation rules
      hodSelectionRules: [(v) => !!v || 'HOD selection is required'],

      // Remove HOD confirmation
      departmentToRemoveHod: null,
      removeHodLoading: false,

      // Selected department
      selectedDepartment: null,

      // Create department data
      newDepartment: {
        name: '',
        code: '',
        description: '',
        is_active: true
      },
      createDepartmentSubmitting: false,
      createDepartmentFormErrors: {},
      autoAssignHod: false,

      // Snackbar
      showSnackbar: false,
      snackbarMessage: '',
      snackbarColor: 'success',

      // Real data from API
      departmentsData: [],
      departmentStatisticsData: {},
      availableHodsData: [],
      departmentsLoading: false,
      departmentsWithHodsLoading: false,
      statisticsLoading: false,
      hodsLoading: false,
      paginationData: {
        current_page: 1,
        last_page: 1,
        per_page: 15,
        total: 0
      },

      // Actions menu state
      activeActionsMenu: null
    }
  },

  computed: {
    // Real computed properties using API data
    departmentStatistics() {
      return {
        total_departments: this.departmentStatisticsData.total_departments || 0,
        with_hod: this.departmentStatisticsData.departments_with_hod || 0,
        without_hod: this.departmentStatisticsData.departments_without_hod || 0,
        with_pending_requests: this.departmentStatisticsData.departments_with_pending_requests || 0
      }
    },

    departmentsPagination() {
      return {
        total: this.paginationData.total || 0,
        current_page: this.paginationData.current_page || 1,
        last_page: this.paginationData.last_page || 1,
        per_page: this.paginationData.per_page || 15
      }
    },

    allDepartmentsWithHods() {
      return this.departmentsData || []
    },

    availableHods() {
      return this.availableHodsData || []
    },

    debouncedSearch() {
      return debounce(this.applyFilters, 500)
    },

    selectedHodDetails() {
      if (!this.selectedHodId) return null
      return this.availableHods.find(hod => hod.id === this.selectedHodId)
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

    assignHodDialog(val) {
      if (!val) {
        this.resetAssignHodForm()
      }
    },

    createDepartmentDialog(val) {
      if (!val) {
        this.resetCreateDepartmentForm()
      }
    }
  },

  async created() {
    await this.initializeData()

    // Check if we should auto-open the create department dialog
    const urlParams = new URLSearchParams(window.location.search)
    if (urlParams.get('action') === 'create') {
      this.openCreateDepartmentDialog()
      // Clean up the URL parameter
      const url = new URL(window.location)
      url.searchParams.delete('action')
      window.history.replaceState({}, '', url)
    }
  },

  mounted() {
    // Add click outside listener to close actions menu
    document.addEventListener('click', this.handleClickOutside)
  },

  beforeUnmount() {
    // Remove click outside listener
    document.removeEventListener('click', this.handleClickOutside)
  },

  methods: {
    // Real API methods
    async fetchDepartmentsWithHods(params = {}) {
      this.departmentsWithHodsLoading = true
      try {
        const result = await departmentHodService.getDepartmentsWithHods(params)

        if (result.success) {
          this.departmentsData = result.data.data || []
          this.paginationData = {
            current_page: result.data.current_page || 1,
            last_page: result.data.last_page || 1,
            per_page: result.data.per_page || 15,
            total: result.data.total || 0
          }
        } else {
          this.showErrorMessage(result.error || 'Failed to fetch departments')
          this.departmentsData = []
        }
      } catch (error) {
        console.error('Error fetching departments:', error)
        this.showErrorMessage('Failed to fetch departments')
        this.departmentsData = []
      } finally {
        this.departmentsWithHodsLoading = false
      }
    },

    async fetchEligibleHods() {
      this.hodsLoading = true
      try {
        const result = await departmentHodService.getEligibleHods()

        if (result.success) {
          this.availableHodsData = result.data || []
        } else {
          this.showErrorMessage(result.error || 'Failed to fetch eligible HODs')
          this.availableHodsData = []
        }
      } catch (error) {
        console.error('Error fetching eligible HODs:', error)
        this.showErrorMessage('Failed to fetch eligible HODs')
        this.availableHodsData = []
      } finally {
        this.hodsLoading = false
      }
    },

    async fetchDepartmentHodStatistics() {
      this.statisticsLoading = true
      try {
        const result = await departmentHodService.getStatistics()

        if (result.success) {
          this.departmentStatisticsData = result.data || {}
        } else {
          this.showErrorMessage(result.error || 'Failed to fetch statistics')
          this.departmentStatisticsData = {}
        }
      } catch (error) {
        console.error('Error fetching statistics:', error)
        this.showErrorMessage('Failed to fetch statistics')
        this.departmentStatisticsData = {}
      } finally {
        this.statisticsLoading = false
      }
    },

    async assignHodToDepartment(data) {
      try {
        const result = await departmentHodService.assignHod(data.departmentId, data.hodUserId)

        if (result.success) {
          this.showSuccessMessage(result.message || 'HOD assigned successfully')
          return { success: true }
        } else {
          this.showErrorMessage(result.error || 'Failed to assign HOD')
          return {
            success: false,
            errors: result.errors || {}
          }
        }
      } catch (error) {
        console.error('Error assigning HOD:', error)
        this.showErrorMessage('Failed to assign HOD')
        return { success: false }
      }
    },

    async removeHodFromDepartment(departmentId) {
      try {
        const result = await departmentHodService.removeHod(departmentId)

        if (result.success) {
          this.showSuccessMessage(result.message || 'HOD removed successfully')
          return { success: true }
        } else {
          this.showErrorMessage(result.error || 'Failed to remove HOD')
          return { success: false }
        }
      } catch (error) {
        console.error('Error removing HOD:', error)
        this.showErrorMessage('Failed to remove HOD')
        return { success: false }
      }
    },

    setSelectedDepartment(department) {
      this.selectedDepartment = department
    },

    clearMessages() {
      // Clear any existing messages
      this.showSnackbar = false
    },

    async createDepartmentAPI(data) {
      try {
        const result = await departmentHodService.createDepartment(data)

        if (result.success) {
          return {
            success: true,
            data: result.data
          }
        } else {
          return {
            success: false,
            errors: result.errors || {},
            message: result.error
          }
        }
      } catch (error) {
        console.error('Error creating department:', error)
        return {
          success: false,
          message: 'Failed to create department'
        }
      }
    },

    async initializeData() {
      try {
        await Promise.all([
          this.fetchDepartmentsWithHods(this.getFilterParams()),
          this.fetchEligibleHods(),
          this.fetchDepartmentHodStatistics()
        ])
      } catch (error) {
        console.log('Error initializing data:', error)
      }
    },

    async refreshData() {
      await Promise.all([
        this.fetchDepartmentsWithHods(this.getFilterParams()),
        this.fetchEligibleHods(),
        this.fetchDepartmentHodStatistics()
      ])
    },

    async applyFilters() {
      this.tableOptions.page = 1
      await this.fetchDepartmentsWithHods(this.getFilterParams())
    },

    async handleTableUpdate(options) {
      this.tableOptions = options
      await this.fetchDepartmentsWithHods(this.getFilterParams())
    },

    getFilterParams() {
      return {
        search: this.searchQuery,
        hod_status: this.hodStatus,
        is_active: this.showActiveOnly,
        sort_by: this.sortBy,
        sort_order: this.tableOptions.sortDesc[0] ? 'desc' : 'asc',
        page: this.tableOptions.page,
        per_page: this.tableOptions.itemsPerPage
      }
    },

    // HOD assignment methods
    openAssignHodDialog(department) {
      this.setSelectedDepartment(department)
      this.isChangeMode = false
      this.assignHodDialog = true
    },

    openChangeHodDialog(department) {
      this.setSelectedDepartment(department)
      this.selectedHodId = department.hod?.id || null
      this.isChangeMode = true
      this.assignHodDialog = true
    },

    closeAssignHodDialog() {
      this.assignHodDialog = false
      this.setSelectedDepartment(null)
    },

    resetAssignHodForm() {
      this.selectedHodId = null
      this.assignHodFormErrors = {}
      this.assignHodFormValid = false
      this.isChangeMode = false

      if (this.$refs.assignHodForm) {
        this.$refs.assignHodForm.resetValidation()
      }
    },

    async assignHod() {
      if (!this.$refs.assignHodForm.validate()) {
        return
      }

      this.assignHodSubmitting = true
      this.assignHodFormErrors = {}

      try {
        const result = await this.assignHodToDepartment({
          departmentId: this.selectedDepartment.id,
          hodUserId: this.selectedHodId
        })

        if (result.success) {
          this.closeAssignHodDialog()
          await Promise.all([
            this.fetchEligibleHods(),
            this.fetchDepartmentHodStatistics()
          ])
        } else {
          this.assignHodFormErrors = result.errors || {}
        }
      } catch (error) {
        console.error('Error assigning HOD:', error)
      } finally {
        this.assignHodSubmitting = false
      }
    },

    // Remove HOD methods
    confirmRemoveHod(department) {
      this.departmentToRemoveHod = department
      this.removeHodDialog = true
    },

    async removeHod() {
      if (!this.departmentToRemoveHod) return

      this.removeHodLoading = true

      try {
        const result = await this.removeHodFromDepartment(
          this.departmentToRemoveHod.id
        )

        if (result.success) {
          this.removeHodDialog = false
          this.departmentToRemoveHod = null
          await Promise.all([
            this.fetchEligibleHods(),
            this.fetchDepartmentHodStatistics()
          ])
        }
      } catch (error) {
        console.error('Error removing HOD:', error)
      } finally {
        this.removeHodLoading = false
      }
    },

    // Create Department methods
    openCreateDepartmentDialog() {
      this.createDepartmentDialog = true
    },

    closeCreateDepartmentDialog() {
      this.createDepartmentDialog = false
      this.resetCreateDepartmentForm()
    },

    resetCreateDepartmentForm() {
      this.newDepartment = {
        name: '',
        code: '',
        description: '',
        is_active: true
      }
      this.createDepartmentFormErrors = {}
      this.autoAssignHod = false
      this.createDepartmentSubmitting = false
    },

    async createDepartment() {
      if (!this.newDepartment.name || !this.newDepartment.code) {
        this.showErrorMessage('Please fill in all required fields')
        return
      }

      this.createDepartmentSubmitting = true
      this.createDepartmentFormErrors = {}

      try {
        const result = await this.createDepartmentAPI(this.newDepartment)

        if (result.success) {
          this.showSuccessMessage(`Department "${this.newDepartment.name}" created successfully!`)

          // If auto-assign HOD is checked, open the assign HOD dialog
          if (this.autoAssignHod) {
            this.closeCreateDepartmentDialog()
            // Simulate the new department for HOD assignment
            const newDepartment = {
              id: result.data.id,
              name: result.data.name,
              code: result.data.code,
              description: result.data.description,
              is_active: result.data.is_active,
              hod: null,
              pending_requests_count: 0
            }
            this.openAssignHodDialog(newDepartment)
          } else {
            this.closeCreateDepartmentDialog()
          }

          // Refresh the departments list
          await this.refreshData()
        } else {
          this.createDepartmentFormErrors = result.errors || {}
          this.showErrorMessage('Failed to create department. Please check the form for errors.')
        }
      } catch (error) {
        console.error('Error creating department:', error)
        this.showErrorMessage('An error occurred while creating the department')
      } finally {
        this.createDepartmentSubmitting = false
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
        head_of_department: 'blue',
        ict_director: 'teal',
        ict_officer: 'green',
        divisional_director: 'orange',
        staff: 'grey'
      }
      return colors[roleName] || 'primary'
    },

    getFieldError(fieldName) {
      // Check assign HOD form errors first
      if (this.assignHodFormErrors[fieldName]) {
        return this.assignHodFormErrors[fieldName][0]
      }
      // Check create department form errors
      if (this.createDepartmentFormErrors[fieldName]) {
        return this.createDepartmentFormErrors[fieldName][0]
      }
      return ''
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
    },

    // Actions menu methods
    toggleActionsMenu(departmentId) {
      if (this.activeActionsMenu === departmentId) {
        this.activeActionsMenu = null
      } else {
        this.activeActionsMenu = departmentId
      }
    },

    closeActionsMenu() {
      this.activeActionsMenu = null
    },

    // HOD management actions
    async editHod(department) {
      this.closeActionsMenu()
      try {
        const result = await departmentHodService.getHodDetails(department.id)
        if (result.success) {
          // Open edit dialog with HOD details
          this.selectedDepartment = department
          this.selectedHodId = department.hod.id
          this.isChangeMode = true
          this.assignHodDialog = true
          this.showSuccessMessage('HOD details loaded for editing')
        } else {
          this.showErrorMessage(result.error || 'Failed to load HOD details')
        }
      } catch (error) {
        console.error('Error loading HOD details:', error)
        this.showErrorMessage('Failed to load HOD details')
      }
    },

    async updateHod(department) {
      this.closeActionsMenu()
      // Open the change HOD dialog
      this.openChangeHodDialog(department)
    },

    async deleteHod(department) {
      this.closeActionsMenu()
      try {
        const result = await departmentHodService.deleteHod(department.id)
        if (result.success) {
          this.showSuccessMessage(result.message || 'HOD deleted successfully')
          await this.refreshData()
        } else {
          this.showErrorMessage(result.error || 'Failed to delete HOD')
        }
      } catch (error) {
        console.error('Error deleting HOD:', error)
        this.showErrorMessage('Failed to delete HOD')
      }
    },

    // Click outside handler
    handleClickOutside(event) {
      // Check if the click is outside the actions menu
      const actionsMenus = document.querySelectorAll('.actions-dropdown')
      const actionsButtons = document.querySelectorAll('.actions-button')

      let clickedInsideMenu = false
      let clickedOnButton = false

      // Check if clicked inside any actions menu
      actionsMenus.forEach(menu => {
        if (menu.contains(event.target)) {
          clickedInsideMenu = true
        }
      })

      // Check if clicked on any actions button
      actionsButtons.forEach(button => {
        if (button.contains(event.target)) {
          clickedOnButton = true
        }
      })

      // Close menu if clicked outside and not on button
      if (!clickedInsideMenu && !clickedOnButton) {
        this.closeActionsMenu()
      }
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

/* Department card hover effects */
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
