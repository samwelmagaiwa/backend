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
                <i class="fas fa-building mr-3 text-blue-300"></i>
                Department Management System
              </h1>
              <p class="text-blue-100 text-lg">Manage departments, HODs, and divisional directors</p>
            </div>
          </div>

          <!-- Main Content -->
          <div class="medical-glass-card rounded-b-3xl overflow-hidden">
            <div class="p-6 space-y-8">

              <!-- Statistics Cards -->
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="medical-card bg-gradient-to-r from-blue-600/25 to-blue-600/25 border-2 border-blue-400/40 p-6 rounded-2xl backdrop-blur-sm">
                  <div class="flex items-center space-x-3 mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                      <i class="fas fa-building text-white text-lg"></i>
                    </div>
                    <div>
                      <div class="text-2xl font-bold text-white">{{ statistics.total_departments || 0 }}</div>
                      <div class="text-sm text-blue-100">Total Departments</div>
                    </div>
                  </div>
                </div>

                <div class="medical-card bg-gradient-to-r from-blue-600/25 to-cyan-600/25 border-2 border-blue-400/40 p-6 rounded-2xl backdrop-blur-sm">
                  <div class="flex items-center space-x-3 mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                      <i class="fas fa-user-tie text-white text-lg"></i>
                    </div>
                    <div>
                      <div class="text-2xl font-bold text-white">{{ statistics.departments_with_hod || 0 }}</div>
                      <div class="text-sm text-blue-100">With HOD</div>
                    </div>
                  </div>
                </div>

                <div class="medical-card bg-gradient-to-r from-blue-600/25 to-blue-600/25 border-2 border-blue-400/40 p-6 rounded-2xl backdrop-blur-sm">
                  <div class="flex items-center space-x-3 mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                      <i class="fas fa-crown text-white text-lg"></i>
                    </div>
                    <div>
                      <div class="text-2xl font-bold text-white">{{ statistics.departments_with_divisional_director || 0 }}</div>
                      <div class="text-sm text-blue-100">With Div. Director</div>
                    </div>
                  </div>
                </div>

                <div class="medical-card bg-gradient-to-r from-blue-600/25 to-blue-600/25 border-2 border-blue-400/40 p-6 rounded-2xl backdrop-blur-sm">
                  <div class="flex items-center space-x-3 mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                      <i class="fas fa-toggle-on text-white text-lg"></i>
                    </div>
                    <div>
                      <div class="text-2xl font-bold text-white">{{ statistics.active_departments || 0 }}</div>
                      <div class="text-sm text-blue-100">Active Departments</div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Department Management Section -->
              <div class="medical-card bg-gradient-to-r from-blue-600/25 to-blue-600/25 border-2 border-blue-400/40 p-6 rounded-2xl backdrop-blur-sm">
                <div class="flex items-center justify-between mb-6">
                  <div class="flex items-center space-x-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                      <i class="fas fa-building text-white text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white flex items-center">
                      <i class="fas fa-list mr-2 text-blue-300"></i>
                      Departments ({{ departments.length || 0 }} total)
                    </h3>
                  </div>
                  <div class="flex space-x-3">
                    <button
                      @click="openCreateDialog"
                      class="bg-gradient-to-r from-blue-500 to-blue-600 text-white py-3 px-6 rounded-xl font-semibold hover:from-blue-600 hover:to-blue-700 transition-all duration-300 transform hover:scale-105 shadow-lg flex items-center space-x-2"
                    >
                      <i class="fas fa-plus"></i>
                      <span>Create Department</span>
                    </button>
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
                    <label class="block text-sm font-bold text-blue-100 mb-2">Search Departments</label>
                    <div class="relative">
                      <input
                        v-model="searchQuery"
                        type="text"
                        class="medical-input w-full px-4 py-3 bg-white/15 border-2 border-blue-300/30 rounded-xl focus:border-blue-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-blue-500/20"
                        placeholder="Search by name or code..."
                        @input="debouncedSearch"
                      />
                      <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <i class="fas fa-search text-blue-300"></i>
                      </div>
                    </div>
                  </div>

                  <div>
                    <label class="block text-sm font-bold text-blue-100 mb-2">Filter by Status</label>
                    <div class="relative">
                      <select
                        v-model="filterStatus"
                        class="medical-input w-full px-4 py-3 bg-white/15 border-2 border-blue-300/30 rounded-xl focus:border-blue-400 focus:outline-none text-white backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-blue-500/20 appearance-none cursor-pointer"
                        @change="applyFilters"
                      >
                        <option value="" class="bg-gray-800 text-white">All Status</option>
                        <option value="active" class="bg-gray-800 text-white">Active</option>
                        <option value="inactive" class="bg-gray-800 text-white">Inactive</option>
                      </select>
                      <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                        <i class="fas fa-chevron-down text-blue-300"></i>
                      </div>
                    </div>
                  </div>

                  <div>
                    <label class="block text-sm font-bold text-blue-100 mb-2">Filter by HOD</label>
                    <div class="relative">
                      <select
                        v-model="filterHod"
                        class="medical-input w-full px-4 py-3 bg-white/15 border-2 border-blue-300/30 rounded-xl focus:border-blue-400 focus:outline-none text-white backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-blue-500/20 appearance-none cursor-pointer"
                        @change="applyFilters"
                      >
                        <option value="" class="bg-gray-800 text-white">All HOD Status</option>
                        <option value="with_hod" class="bg-gray-800 text-white">With HOD</option>
                        <option value="without_hod" class="bg-gray-800 text-white">Without HOD</option>
                      </select>
                      <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                        <i class="fas fa-chevron-down text-blue-300"></i>
                      </div>
                    </div>
                  </div>

                  <div>
                    <label class="block text-sm font-bold text-blue-100 mb-2">Sort By</label>
                    <div class="relative">
                      <select
                        v-model="sortBy"
                        class="medical-input w-full px-4 py-3 bg-white/15 border-2 border-blue-300/30 rounded-xl focus:border-blue-400 focus:outline-none text-white backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-blue-500/20 appearance-none cursor-pointer"
                        @change="applyFilters"
                      >
                        <option value="name" class="bg-gray-800 text-white">Name</option>
                        <option value="code" class="bg-gray-800 text-white">Code</option>
                        <option value="created_at" class="bg-gray-800 text-white">Created Date</option>
                      </select>
                      <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                        <i class="fas fa-chevron-down text-blue-300"></i>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Departments List -->
                <div v-if="loading" class="text-center py-8">
                  <div class="inline-flex items-center space-x-2 text-blue-100">
                    <i class="fas fa-spinner fa-spin text-xl"></i>
                    <span>Loading departments...</span>
                  </div>
                </div>

                <div v-else-if="filteredDepartments.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                  <div
                    v-for="department in filteredDepartments"
                    :key="department.id"
                    class="bg-white/15 p-6 rounded-xl backdrop-blur-sm border border-blue-300/30 hover:bg-white/20 transition-all duration-300 hover:shadow-lg hover:shadow-blue-500/20 group"
                  >
                    <div class="flex items-start justify-between mb-4">
                      <div class="flex items-center space-x-3 flex-1">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                          <span class="text-white font-bold text-sm">
                            {{ getInitials(department.name) }}
                          </span>
                        </div>
                        <div class="flex-1">
                          <h4 class="font-bold text-white text-lg">{{ department.name }}</h4>
                          <p class="text-blue-100 text-sm">{{ department.code }}</p>
                          <p v-if="department.description" class="text-blue-200 text-xs mt-1">
                            {{ department.description }}
                          </p>
                        </div>
                      </div>
                      <div class="flex items-center space-x-2">
                        <span
                          :class="[
                            'px-2 py-1 rounded text-xs font-medium',
                            department.is_active
                              ? 'bg-green-500/30 text-green-100 border border-green-400/50'
                              : 'bg-red-500/30 text-red-100 border border-red-400/50'
                          ]"
                        >
                          {{ department.is_active ? 'Active' : 'Inactive' }}
                        </span>
                      </div>
                    </div>

                    <!-- HOD Information -->
                    <div class="mb-4">
                      <div class="text-xs text-blue-100 mb-2">Head of Department:</div>
                      <div v-if="department.hod" class="flex items-center space-x-2">
                        <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center">
                          <i class="fas fa-user-tie text-white text-xs"></i>
                        </div>
                        <div>
                          <div class="text-sm text-white font-medium">{{ department.hod.name }}</div>
                          <div class="text-xs text-blue-100">{{ department.hod.email }}</div>
                          <div v-if="department.hod.pf_number" class="text-xs text-blue-200">
                            PF: {{ department.hod.pf_number }}
                          </div>
                        </div>
                      </div>
                      <div v-else class="px-2 py-1 bg-gray-500/30 text-gray-100 rounded text-xs border border-gray-400/50">
                        No HOD assigned
                      </div>
                    </div>

                    <!-- Divisional Director Information -->
                    <div class="mb-4" v-if="department.has_divisional_director">
                      <div class="text-xs text-blue-100 mb-2">Divisional Director:</div>
                      <div v-if="department.divisional_director" class="flex items-center space-x-2">
                        <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center">
                          <i class="fas fa-crown text-white text-xs"></i>
                        </div>
                        <div>
                          <div class="text-sm text-white font-medium">{{ department.divisional_director.name }}</div>
                          <div class="text-xs text-blue-100">{{ department.divisional_director.email }}</div>
                          <div v-if="department.divisional_director.pf_number" class="text-xs text-blue-200">
                            PF: {{ department.divisional_director.pf_number }}
                          </div>
                        </div>
                      </div>
                      <div v-else class="px-2 py-1 bg-gray-500/30 text-gray-100 rounded text-xs border border-gray-400/50">
                        No Divisional Director assigned
                      </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex space-x-2">
                      <button
                        @click="openEditDialog(department)"
                        class="flex-1 bg-gradient-to-r from-blue-500 to-blue-600 text-white py-2 px-3 rounded-lg text-xs font-semibold hover:from-blue-600 hover:to-blue-700 transition-all duration-300 transform hover:scale-105 shadow-lg flex items-center justify-center space-x-1"
                      >
                        <i class="fas fa-edit"></i>
                        <span>Edit</span>
                      </button>

                      <button
                        @click="toggleDepartmentStatus(department)"
                        class="flex-1 bg-gradient-to-r from-yellow-500 to-yellow-600 text-white py-2 px-3 rounded-lg text-xs font-semibold hover:from-yellow-600 hover:to-yellow-700 transition-all duration-300 transform hover:scale-105 shadow-lg flex items-center justify-center space-x-1"
                      >
                        <i :class="department.is_active ? 'fas fa-toggle-off' : 'fas fa-toggle-on'"></i>
                        <span>{{ department.is_active ? 'Deactivate' : 'Activate' }}</span>
                      </button>

                      <button
                        @click="confirmDelete(department)"
                        class="flex-1 bg-gradient-to-r from-red-500 to-red-600 text-white py-2 px-3 rounded-lg text-xs font-semibold hover:from-red-600 hover:to-red-700 transition-all duration-300 transform hover:scale-105 shadow-lg flex items-center justify-center space-x-1"
                      >
                        <i class="fas fa-trash"></i>
                        <span>Delete</span>
                      </button>
                    </div>
                  </div>
                </div>

                <!-- No Departments Found -->
                <div v-else class="text-center py-8">
                  <div class="text-blue-100">
                    <i class="fas fa-building text-4xl mb-4 opacity-50"></i>
                    <p class="text-lg">No departments found</p>
                    <p class="text-sm opacity-75">
                      {{ searchQuery ? 'Try adjusting your search criteria' : 'No departments available' }}
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>

    <!-- Create/Edit Department Dialog -->
    <div
      v-if="showDialog"
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
            <i class="fas fa-building text-white text-2xl"></i>
          </div>
          <h3 class="text-xl font-bold text-white mb-2">
            {{ isEditing ? 'Edit Department' : 'Create New Department' }}
          </h3>
          <div class="w-12 h-1 bg-white/50 mx-auto rounded-full"></div>
        </div>

        <!-- Body -->
        <div class="p-6">
          <form @submit.prevent="submitForm">
            <!-- Basic Information Section -->
            <div class="medical-card bg-gradient-to-r from-blue-600/15 to-cyan-600/15 border-2 border-blue-400/30 p-6 rounded-2xl backdrop-blur-sm hover:shadow-lg hover:shadow-blue-500/20 transition-all duration-300 mb-6">
              <div class="flex items-center space-x-3 mb-6">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                  <i class="fas fa-info-circle text-white text-lg"></i>
                </div>
                <h4 class="text-xl font-bold text-gray-800">Basic Information</h4>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Department Name -->
                <div class="space-y-2">
                  <div class="flex items-center space-x-2 mb-3">
                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-md">
                      <i class="fas fa-building text-white text-sm"></i>
                    </div>
                    <label class="text-sm font-bold text-gray-700">
                      Department Name *
                    </label>
                  </div>
                  <input
                    v-model="form.name"
                    type="text"
                    required
                    class="w-full px-4 py-3 bg-white/70 border-2 border-blue-300/30 rounded-xl focus:border-blue-500 focus:outline-none text-gray-800 placeholder-gray-500 backdrop-blur-sm transition-all duration-300 hover:bg-white/80 focus:bg-white/90 focus:shadow-lg focus:shadow-blue-500/20"
                    placeholder="Enter department name"
                  />
                  <div v-if="formErrors.name" class="text-red-500 text-sm mt-1 flex items-center space-x-1">
                    <i class="fas fa-exclamation-circle text-xs"></i>
                    <span>{{ formErrors.name[0] }}</span>
                  </div>
                </div>

                <!-- Department Code -->
                <div class="space-y-2">
                  <div class="flex items-center space-x-2 mb-3">
                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-md">
                      <i class="fas fa-code text-white text-sm"></i>
                    </div>
                    <label class="text-sm font-bold text-gray-700">
                      Department Code *
                    </label>
                  </div>
                  <input
                    v-model="form.code"
                    type="text"
                    required
                    class="w-full px-4 py-3 bg-white/70 border-2 border-blue-300/30 rounded-xl focus:border-blue-500 focus:outline-none text-gray-800 placeholder-gray-500 backdrop-blur-sm transition-all duration-300 hover:bg-white/80 focus:bg-white/90 focus:shadow-lg focus:shadow-blue-500/20"
                    placeholder="Enter department code (e.g., ICT, HR)"
                  />
                  <div v-if="formErrors.code" class="text-red-500 text-sm mt-1 flex items-center space-x-1">
                    <i class="fas fa-exclamation-circle text-xs"></i>
                    <span>{{ formErrors.code[0] }}</span>
                  </div>
                </div>

                <!-- Description -->
                <div class="space-y-2 md:col-span-2">
                  <div class="flex items-center space-x-2 mb-3">
                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-md">
                      <i class="fas fa-align-left text-white text-sm"></i>
                    </div>
                    <label class="text-sm font-bold text-gray-700">
                      Description
                    </label>
                  </div>
                  <textarea
                    v-model="form.description"
                    rows="3"
                    class="w-full px-4 py-3 bg-white/70 border-2 border-blue-300/30 rounded-xl focus:border-blue-500 focus:outline-none text-gray-800 placeholder-gray-500 backdrop-blur-sm transition-all duration-300 hover:bg-white/80 focus:bg-white/90 focus:shadow-lg focus:shadow-blue-500/20"
                    placeholder="Enter department description (optional)"
                  ></textarea>
                  <div v-if="formErrors.description" class="text-red-500 text-sm mt-1 flex items-center space-x-1">
                    <i class="fas fa-exclamation-circle text-xs"></i>
                    <span>{{ formErrors.description[0] }}</span>
                  </div>
                </div>

                <!-- Status -->
                <div class="space-y-2">
                  <div class="flex items-center space-x-2 mb-3">
                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-md">
                      <i class="fas fa-toggle-on text-white text-sm"></i>
                    </div>
                    <label class="text-sm font-bold text-gray-700">
                      Status
                    </label>
                  </div>
                  <div class="relative">
                    <select
                      v-model="form.is_active"
                      class="w-full px-4 py-3 bg-white/70 border-2 border-blue-300/30 rounded-xl focus:border-blue-500 focus:outline-none text-gray-800 backdrop-blur-sm transition-all duration-300 hover:bg-white/80 focus:bg-white/90 focus:shadow-lg focus:shadow-blue-500/20 appearance-none cursor-pointer"
                    >
                      <option :value="true" class="bg-gray-800 text-white">Active</option>
                      <option :value="false" class="bg-gray-800 text-white">Inactive</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                      <i class="fas fa-chevron-down text-blue-500"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- HOD Assignment Section -->
            <div class="medical-card bg-gradient-to-r from-blue-600/15 to-blue-600/15 border-2 border-blue-400/30 p-6 rounded-2xl backdrop-blur-sm hover:shadow-lg hover:shadow-blue-500/20 transition-all duration-300 mb-6">
              <div class="flex items-center space-x-3 mb-6">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                  <i class="fas fa-user-tie text-white text-lg"></i>
                </div>
                <h4 class="text-xl font-bold text-gray-800">Head of Department (HOD)</h4>
              </div>

              <div class="space-y-4">
                <div class="space-y-2">
                  <div class="flex items-center space-x-2 mb-3">
                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-md">
                      <i class="fas fa-user-tie text-white text-sm"></i>
                    </div>
                    <label class="text-sm font-bold text-gray-700">
                      Select HOD
                    </label>
                  </div>
                  <div class="relative">
                    <select
                      v-model="form.hod_user_id"
                      class="w-full px-4 py-3 bg-white/70 border-2 border-blue-300/30 rounded-xl focus:border-blue-500 focus:outline-none text-gray-800 backdrop-blur-sm transition-all duration-300 hover:bg-white/80 focus:bg-white/90 focus:shadow-lg focus:shadow-blue-500/20 appearance-none cursor-pointer"
                    >
                      <option value="" class="bg-gray-800 text-white">No HOD assigned</option>
                      <option
                        v-for="hod in eligibleHods"
                        :key="hod.id"
                        :value="hod.id"
                        class="bg-gray-800 text-white"
                      >
                        {{ hod.display_name }}
                      </option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                      <i class="fas fa-chevron-down text-blue-500"></i>
                    </div>
                  </div>
                  <div v-if="formErrors.hod_user_id" class="text-red-500 text-sm mt-1 flex items-center space-x-1">
                    <i class="fas fa-exclamation-circle text-xs"></i>
                    <span>{{ formErrors.hod_user_id[0] }}</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Divisional Director Section -->
            <div class="medical-card bg-gradient-to-r from-blue-600/15 to-blue-600/15 border-2 border-blue-400/30 p-6 rounded-2xl backdrop-blur-sm hover:shadow-lg hover:shadow-blue-500/20 transition-all duration-300 mb-6">
              <div class="flex items-center space-x-3 mb-6">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                  <i class="fas fa-crown text-white text-lg"></i>
                </div>
                <h4 class="text-xl font-bold text-gray-800">Divisional Director</h4>
              </div>

              <div class="space-y-4">
                <!-- Has Divisional Director Checkbox -->
                <div class="flex items-center space-x-3">
                  <input
                    v-model="form.has_divisional_director"
                    type="checkbox"
                    class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                  />
                  <label class="text-sm font-bold text-gray-700">
                    This department has a divisional director
                  </label>
                </div>

                <!-- Divisional Director Selection -->
                <div v-if="form.has_divisional_director" class="space-y-2">
                  <div class="flex items-center space-x-2 mb-3">
                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-md">
                      <i class="fas fa-crown text-white text-sm"></i>
                    </div>
                    <label class="text-sm font-bold text-gray-700">
                      Select Divisional Director *
                    </label>
                  </div>
                  <div class="relative">
                    <select
                      v-model="form.divisional_director_id"
                      :required="form.has_divisional_director"
                      class="w-full px-4 py-3 bg-white/70 border-2 border-blue-300/30 rounded-xl focus:border-blue-500 focus:outline-none text-gray-800 backdrop-blur-sm transition-all duration-300 hover:bg-white/80 focus:bg-white/90 focus:shadow-lg focus:shadow-blue-500/20 appearance-none cursor-pointer"
                    >
                      <option value="" class="bg-gray-800 text-white">Select divisional director</option>
                      <option
                        v-for="director in eligibleDivisionalDirectors"
                        :key="director.id"
                        :value="director.id"
                        class="bg-gray-800 text-white"
                      >
                        {{ director.display_name }}
                      </option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                      <i class="fas fa-chevron-down text-blue-500"></i>
                    </div>
                  </div>
                  <div v-if="formErrors.divisional_director_id" class="text-red-500 text-sm mt-1 flex items-center space-x-1">
                    <i class="fas fa-exclamation-circle text-xs"></i>
                    <span>{{ formErrors.divisional_director_id[0] }}</span>
                  </div>
                </div>
              </div>
            </div>
          </form>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end space-x-3 p-6 border-t border-gray-200">
          <button
            @click="closeDialog"
            :disabled="submitting"
            class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-medium hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Cancel
          </button>
          <button
            @click="submitForm"
            :disabled="submitting || !form.name || !form.code || (form.has_divisional_director && !form.divisional_director_id)"
            class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl font-medium hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed flex items-center space-x-2"
          >
            <i v-if="submitting" class="fas fa-spinner fa-spin"></i>
            <i v-else class="fas fa-save"></i>
            <span>{{ submitting ? 'Saving...' : (isEditing ? 'Update Department' : 'Create Department') }}</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Dialog -->
    <div
      v-if="showDeleteDialog"
      class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 backdrop-blur-sm"
    >
      <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 transform transition-all duration-300 scale-100">
        <div class="p-6">
          <div class="flex items-center space-x-3 mb-4">
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
              <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
            <div>
              <h3 class="text-lg font-bold text-gray-900">Delete Department</h3>
              <p class="text-sm text-gray-600">This action cannot be undone</p>
            </div>
          </div>
          <p class="text-gray-700 mb-6">
            Are you sure you want to delete the department "<strong>{{ departmentToDelete?.name }}</strong>"?
          </p>
          <div class="flex justify-end space-x-3">
            <button
              @click="showDeleteDialog = false"
              class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition-colors"
            >
              Cancel
            </button>
            <button
              @click="deleteDepartment"
              :disabled="deleting"
              class="px-4 py-2 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition-colors disabled:opacity-50 flex items-center space-x-2"
            >
              <i v-if="deleting" class="fas fa-spinner fa-spin"></i>
              <i v-else class="fas fa-trash"></i>
              <span>{{ deleting ? 'Deleting...' : 'Delete' }}</span>
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
import { ref, computed, onMounted } from 'vue'
import { debounce } from 'lodash'
import AppHeader from '@/components/AppHeader.vue'
import ModernSidebar from '@/components/ModernSidebar.vue'
import departmentService from '@/services/departmentService'

export default {
  name: 'DepartmentManagement',
  components: {
    AppHeader,
    ModernSidebar
  },
  setup() {
    // Reactive data
    const loading = ref(false)
    const departments = ref([])
    const eligibleHods = ref([])
    const eligibleDivisionalDirectors = ref([])
    const statistics = ref({
      total_departments: 0,
      departments_with_hod: 0,
      departments_with_divisional_director: 0,
      active_departments: 0
    })

    // Filters
    const searchQuery = ref('')
    const filterStatus = ref('')
    const filterHod = ref('')
    const sortBy = ref('name')

    // Dialog states
    const showDialog = ref(false)
    const showDeleteDialog = ref(false)
    const isEditing = ref(false)
    const submitting = ref(false)
    const deleting = ref(false)

    // Form data
    const form = ref({
      name: '',
      code: '',
      description: '',
      is_active: true,
      hod_user_id: '',
      has_divisional_director: false,
      divisional_director_id: ''
    })
    const formErrors = ref({})
    const departmentToDelete = ref(null)

    // Snackbar
    const showSnackbar = ref(false)
    const snackbarMessage = ref('')
    const snackbarColor = ref('success')

    // Computed properties
    const filteredDepartments = computed(() => {
      let filtered = [...departments.value]

      // Apply search filter
      if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase()
        filtered = filtered.filter(dept =>
          dept.name.toLowerCase().includes(query) ||
          dept.code.toLowerCase().includes(query) ||
          (dept.description && dept.description.toLowerCase().includes(query))
        )
      }

      // Apply status filter
      if (filterStatus.value) {
        const isActive = filterStatus.value === 'active'
        filtered = filtered.filter(dept => dept.is_active === isActive)
      }

      // Apply HOD filter
      if (filterHod.value) {
        if (filterHod.value === 'with_hod') {
          filtered = filtered.filter(dept => dept.hod)
        } else if (filterHod.value === 'without_hod') {
          filtered = filtered.filter(dept => !dept.hod)
        }
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
    const initializeData = async() => {
      loading.value = true
      try {
        await Promise.all([
          fetchDepartments(),
          fetchFormData()
        ])
        calculateStatistics()
      } catch (error) {
        console.error('Error initializing data:', error)
        console.error('Error details:', {
          message: error.message,
          response: error.response?.data,
          status: error.response?.status,
          statusText: error.response?.statusText
        })
        showErrorMessage(`Failed to load data: ${error.response?.data?.message || error.message}`)
      } finally {
        loading.value = false
      }
    }

    const fetchDepartments = async() => {
      try {
        console.log('Fetching departments...')
        const response = await departmentService.getDepartments()
        console.log('Departments response:', response)
        if (response.success) {
          departments.value = response.data.data || []
          console.log('Departments loaded:', departments.value.length)
        } else {
          console.error('Departments fetch failed:', response)
          throw new Error(response.message)
        }
      } catch (error) {
        console.error('Error fetching departments:', error)
        throw error
      }
    }

    const fetchFormData = async(departmentId = null) => {
      try {
        console.log('Fetching form data...', departmentId ? `for department ${departmentId}` : 'for new department')
        const response = await departmentService.getFormData(departmentId)
        console.log('Form data response:', response)
        if (response.success) {
          eligibleHods.value = response.data.eligible_hods || []
          eligibleDivisionalDirectors.value = response.data.eligible_divisional_directors || []
          console.log('Form data loaded - HODs:', eligibleHods.value.length, 'Directors:', eligibleDivisionalDirectors.value.length)
          console.log('Total users checked:', response.data.total_users_checked)
        } else {
          console.error('Form data fetch failed:', response)
          throw new Error(response.message)
        }
      } catch (error) {
        console.error('Error fetching form data:', error)
        throw error
      }
    }

    const calculateStatistics = () => {
      statistics.value = {
        total_departments: departments.value.length,
        departments_with_hod: departments.value.filter(d => d.hod).length,
        departments_with_divisional_director: departments.value.filter(d => d.divisional_director).length,
        active_departments: departments.value.filter(d => d.is_active).length
      }
    }

    const refreshData = async() => {
      await initializeData()
    }

    const debouncedSearch = debounce(() => {
      // Search is handled by computed property
    }, 500)

    const applyFilters = () => {
      // Filters are handled by computed property
    }

    // Dialog methods
    const openCreateDialog = () => {
      resetForm()
      isEditing.value = false
      showDialog.value = true
    }

    const openEditDialog = async(department) => {
      resetForm()
      isEditing.value = true
      form.value = {
        name: department.name,
        code: department.code,
        description: department.description || '',
        is_active: department.is_active,
        hod_user_id: department.hod?.id || '',
        has_divisional_director: department.has_divisional_director,
        divisional_director_id: department.divisional_director?.id || ''
      }
      form.value.id = department.id

      // Refresh form data with current department ID to get available users
      try {
        await fetchFormData(department.id)
      } catch (error) {
        console.error('Error refreshing form data for edit:', error)
        showErrorMessage('Failed to load available users for editing')
      }

      showDialog.value = true
    }

    const closeDialog = () => {
      showDialog.value = false
      resetForm()
    }

    const resetForm = () => {
      form.value = {
        name: '',
        code: '',
        description: '',
        is_active: true,
        hod_user_id: '',
        has_divisional_director: false,
        divisional_director_id: ''
      }
      formErrors.value = {}
    }

    const submitForm = async() => {
      submitting.value = true
      formErrors.value = {}

      try {
        const formData = { ...form.value }

        // Clear divisional_director_id if has_divisional_director is false
        if (!formData.has_divisional_director) {
          formData.divisional_director_id = ''
        }

        let response
        if (isEditing.value) {
          response = await departmentService.updateDepartment(form.value.id, formData)
        } else {
          response = await departmentService.createDepartment(formData)
        }

        if (response.success) {
          showSuccessMessage(response.message)
          closeDialog()
          await refreshData()
        } else {
          if (response.errors) {
            formErrors.value = response.errors
          } else {
            showErrorMessage(response.message)
          }
        }
      } catch (error) {
        console.error('Error submitting form:', error)
        showErrorMessage('Failed to save department')
      } finally {
        submitting.value = false
      }
    }

    const confirmDelete = (department) => {
      departmentToDelete.value = department
      showDeleteDialog.value = true
    }

    const deleteDepartment = async() => {
      if (!departmentToDelete.value) return

      deleting.value = true
      try {
        const response = await departmentService.deleteDepartment(departmentToDelete.value.id)
        if (response.success) {
          showSuccessMessage(response.message)
          showDeleteDialog.value = false
          departmentToDelete.value = null
          await refreshData()
        } else {
          showErrorMessage(response.message)
        }
      } catch (error) {
        console.error('Error deleting department:', error)
        showErrorMessage('Failed to delete department')
      } finally {
        deleting.value = false
      }
    }

    const toggleDepartmentStatus = async(department) => {
      try {
        const response = await departmentService.toggleDepartmentStatus(department.id)
        if (response.success) {
          showSuccessMessage(response.message)
          await refreshData()
        } else {
          showErrorMessage(response.message)
        }
      } catch (error) {
        console.error('Error toggling department status:', error)
        showErrorMessage('Failed to toggle department status')
      }
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

    // Initialize data on mount
    onMounted(() => {
      initializeData()
    })

    return {
      // Reactive data
      loading,
      departments,
      eligibleHods,
      eligibleDivisionalDirectors,
      statistics,
      searchQuery,
      filterStatus,
      filterHod,
      sortBy,
      showDialog,
      showDeleteDialog,
      isEditing,
      submitting,
      deleting,
      form,
      formErrors,
      departmentToDelete,
      showSnackbar,
      snackbarMessage,
      snackbarColor,

      // Computed
      filteredDepartments,

      // Methods
      refreshData,
      debouncedSearch,
      applyFilters,
      openCreateDialog,
      openEditDialog,
      closeDialog,
      submitForm,
      confirmDelete,
      deleteDepartment,
      toggleDepartmentStatus,
      getInitials
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
  border-color: rgba(59, 130, 246, 0.8);
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
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
