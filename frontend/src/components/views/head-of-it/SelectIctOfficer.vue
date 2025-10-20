<template>
  <div class="flex flex-col h-screen">
    <Header />
    <div class="flex flex-1 overflow-hidden">
      <div class="sidebar-narrow">
        <ModernSidebar />
      </div>
      <main
        class="flex-1 p-2 relative bg-blue-900 overflow-visible"
        style="
          background: linear-gradient(
            135deg,
            #1e3a8a 0%,
            #1e40af 25%,
            #1d4ed8 50%,
            #1e40af 75%,
            #1e3a8a 100%
          );
        "
      >
        <!-- Medical Background Pattern -->
        <div class="absolute inset-0 overflow-visible pointer-events-none">
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

        <div class="max-w-full mx-auto relative z-10 px-4">
          <!-- Back Button Only -->
          <div class="medical-glass-card rounded-t-3xl p-4 mb-0 border-b border-blue-300/30">
            <button
              @click="goBack"
              class="p-2 rounded-lg bg-teal-600/20 hover:bg-teal-600/30 transition-colors"
            >
              <i class="fas fa-arrow-left text-teal-300 hover:text-white"></i>
            </button>
          </div>

          <!-- Error Display -->
          <div
            v-if="error"
            class="medical-glass-card rounded-none border-t-0 border-b border-blue-300/30"
          >
            <div class="p-4 bg-red-600/20 border border-red-400/30 rounded-lg">
              <h3 class="font-bold text-xl text-red-300">Error</h3>
              <p class="text-lg text-red-200">{{ error }}</p>
              <button
                @click="fetchIctOfficers"
                class="mt-2 bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 text-base font-medium"
              >
                Retry
              </button>
            </div>
          </div>

          <!-- Search & Filter Controls -->
          <div class="medical-glass-card rounded-none border-t-0 border-b border-blue-300/30">
            <div class="p-4">
              <div
                class="flex flex-col lg:flex-row gap-4 items-start lg:items-center justify-between"
              >
                <!-- Search Section -->
                <div class="flex-1 max-w-xl">
                  <div class="relative">
                    <div
                      class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                    >
                      <i class="fas fa-search text-teal-300"></i>
                    </div>
                    <input
                      v-model="searchQuery"
                      type="text"
                      placeholder="Search by name, PF number, or phone number..."
                      class="w-full pl-10 pr-4 py-2.5 bg-white/10 border border-teal-300/30 rounded-lg focus:border-teal-400 focus:outline-none text-white placeholder-teal-200/60 backdrop-blur-sm transition-colors"
                    />
                    <button
                      v-if="searchQuery"
                      @click="clearSearch"
                      class="absolute inset-y-0 right-0 pr-3 flex items-center text-teal-300 hover:text-white"
                    >
                      <i class="fas fa-times"></i>
                    </button>
                  </div>
                </div>

                <!-- Filter Controls -->
                <div class="flex flex-wrap gap-3 items-center">
                  <!-- Status Filter -->
                  <div class="relative">
                    <select
                      v-model="statusFilter"
                      class="appearance-none bg-white/10 border border-teal-300/30 rounded-lg px-4 py-2.5 pr-10 text-white text-sm focus:border-teal-400 focus:outline-none backdrop-blur-sm cursor-pointer"
                    >
                      <option value="" class="bg-blue-900 text-white">All Officers</option>
                      <option value="Available" class="bg-blue-900 text-white">Available</option>
                      <option value="Assigned" class="bg-blue-900 text-white">Assigned</option>
                      <option value="Busy" class="bg-blue-900 text-white">Busy</option>
                      <option value="Completed" class="bg-blue-900 text-white">Completed</option>
                    </select>
                    <div
                      class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none"
                    >
                      <i class="fas fa-chevron-down text-teal-300"></i>
                    </div>
                  </div>

                  <!-- Clear Filters Button -->
                  <button
                    v-if="searchQuery || statusFilter"
                    @click="clearAllFilters"
                    class="px-3 py-2.5 bg-red-600/30 hover:bg-red-600/50 text-red-200 rounded-lg transition-colors border border-red-400/30 text-sm flex items-center"
                    title="Clear all filters"
                  >
                    <i class="fas fa-times mr-1"></i>
                    Clear
                  </button>

                  <!-- Refresh Button -->
                  <button
                    @click="refreshOfficers"
                    :disabled="isLoading"
                    class="px-4 py-2.5 bg-teal-600 hover:bg-teal-700 text-white rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed text-sm flex items-center"
                    title="Refresh officers list"
                  >
                    <i class="fas fa-sync-alt mr-2" :class="{ 'animate-spin': isLoading }"></i>
                    Refresh
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Main Content -->
          <div class="medical-glass-card rounded-b-3xl overflow-visible relative">
            <div class="p-6">
              <!-- Loading State -->
              <div v-if="isLoading" class="text-center py-12">
                <i class="fas fa-spinner fa-spin text-4xl text-teal-400 mb-4"></i>
                <p class="text-white text-lg">Loading ICT Officers...</p>
              </div>

              <!-- ICT Officers Table -->
              <div v-else class="overflow-visible rounded-xl border border-blue-300/20">
                <div class="overflow-x-auto overflow-visible relative">
                  <table class="w-full">
                    <!-- Table Header -->
                    <thead
                      class="bg-gradient-to-r from-blue-600/20 to-indigo-600/20 border-b border-blue-300/20"
                    >
                      <tr>
                        <th
                          class="px-6 py-4 text-left text-sm font-semibold text-blue-200 uppercase tracking-wider"
                        >
                          <i class="fas fa-hashtag mr-2"></i>S/N
                        </th>
                        <th
                          class="px-6 py-4 text-left text-sm font-semibold text-blue-200 uppercase tracking-wider"
                        >
                          <i class="fas fa-user mr-2"></i>Full Name
                        </th>
                        <th
                          class="px-6 py-4 text-left text-sm font-semibold text-blue-200 uppercase tracking-wider"
                        >
                          <i class="fas fa-id-card mr-2"></i>PF Number
                        </th>
                        <th
                          class="px-6 py-4 text-left text-sm font-semibold text-blue-200 uppercase tracking-wider"
                        >
                          <i class="fas fa-phone mr-2"></i>Phone Number
                        </th>
                        <th
                          class="px-6 py-4 text-left text-sm font-semibold text-blue-200 uppercase tracking-wider"
                        >
                          <i class="fas fa-info-circle mr-2"></i>Status
                        </th>
                        <th
                          class="px-6 py-4 text-center text-sm font-semibold text-blue-200 uppercase tracking-wider"
                        >
                          <i class="fas fa-cogs mr-2"></i>Actions
                        </th>
                      </tr>
                    </thead>

                    <!-- Table Body -->
                    <tbody class="divide-y divide-blue-300/10">
                      <tr
                        v-for="(officer, index) in filteredOfficers"
                        :key="officer.id"
                        :class="[
                          'hover:bg-blue-600/10 transition-colors duration-200',
                          index % 2 === 0 ? 'bg-blue-950/20' : 'bg-blue-950/10'
                        ]"
                      >
                        <!-- S/N -->
                        <td class="px-6 py-4 text-white text-base font-medium">
                          {{ index + 1 }}
                        </td>

                        <!-- Full Name -->
                        <td class="px-6 py-4">
                          <div class="flex items-center space-x-3">
                            <div
                              class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center"
                            >
                              <span class="text-white font-bold text-sm">{{
                                getInitials(officer.name)
                              }}</span>
                            </div>
                            <div>
                              <p class="text-white font-medium text-base">{{ officer.name }}</p>
                              <p class="text-blue-300 text-sm">
                                {{ officer.position || 'ICT Officer' }}
                              </p>
                            </div>
                          </div>
                        </td>

                        <!-- PF Number -->
                        <td class="px-6 py-4">
                          <span class="text-white text-base font-mono">{{
                            officer.pf_number || 'N/A'
                          }}</span>
                        </td>

                        <!-- Phone Number -->
                        <td class="px-6 py-4">
                          <span class="text-white text-base">{{
                            officer.phone_number || 'N/A'
                          }}</span>
                        </td>

                        <!-- Status -->
                        <td class="px-6 py-4">
                          <span
                            :class="getStatusBadgeClass(officer.status)"
                            class="px-3 py-1 rounded-full text-sm font-medium"
                          >
                            {{ officer.status || 'Available' }}
                          </span>
                        </td>

                        <!-- Actions -->
                        <td class="px-6 py-4 text-center relative overflow-visible">
                          <!-- Create a local stacking context without covering footer -->
                          <div style="position: relative; z-index: 1">
                          </div>
                          <div class="flex justify-center three-dot-menu">
                            <!-- Three-dot menu button -->
                            <button
                              @click="toggleDropdown(officer.id, $event)"
                              class="three-dot-button p-2 text-white hover:bg-blue-600/40 rounded-full transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-400 hover:scale-105 active:scale-95"
                              :class="{ 'bg-blue-600/40 shadow-lg': openDropdownId === officer.id }"
                              :aria-label="'Actions for officer ' + officer.name"
                              :ref="'button-' + officer.id"
                            >
                              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                  d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"
                                />
                              </svg>
                            </button>

                            <!-- Dropdown menu is rendered in a fixed overlay at the end of the component -->
                          </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>

                <!-- Empty State -->
                <div v-if="filteredOfficers.length === 0" class="text-center py-12">
                  <div
                    class="w-16 h-16 bg-blue-500/20 rounded-full flex items-center justify-center mx-auto mb-4"
                  >
                    <i class="fas fa-users text-blue-400 text-2xl"></i>
                  </div>
                  <h3 class="text-white text-xl font-medium mb-2">No ICT Officers Found</h3>
                  <p class="text-blue-300 text-base">
                    {{
                      searchQuery || statusFilter
                        ? 'No officers match your current filters.'
                        : 'No ICT officers available at this time.'
                    }}
                  </p>
                </div>

                <!-- Table Footer -->
                <div
                  v-if="filteredOfficers.length > 0"
                  class="bg-blue-800/30 px-6 py-3 border-t border-blue-300/20"
                >
                  <p class="text-blue-300 text-base">
                    Showing {{ filteredOfficers.length }} of {{ ictOfficers.length }} officers
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Assignment Confirmation Modal -->
          <div
            v-if="showConfirmModal"
            class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4"
          >
            <div
              class="bg-white rounded-2xl shadow-2xl max-w-md w-full transform transition-all duration-300 scale-100"
            >
              <div
                class="px-6 py-4 text-center rounded-t-2xl"
                style="background: linear-gradient(135deg, #0f52ba 0%, #1e3a8a 100%)"
              >
                <div
                  class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-3 shadow-lg"
                >
                  <i class="fas fa-question text-2xl" style="color: #0f52ba"></i>
                </div>
                <h3 class="text-xl font-bold text-white">Confirm Assignment</h3>
              </div>

              <div class="px-6 py-6">
                <p class="text-gray-700 text-lg mb-6 text-center">
                  Are you sure you want to assign this task to
                  <span class="font-semibold text-blue-600">{{ selectedOfficer?.name }}</span
                  >?
                </p>

                <div class="flex gap-3">
                  <button
                    @click="assignTask"
                    :disabled="isAssigning"
                    class="flex-1 bg-green-600 text-white py-3 px-4 rounded-lg hover:bg-green-700 disabled:opacity-50 transition duration-200 text-base font-medium flex items-center justify-center"
                  >
                    <i v-if="isAssigning" class="fas fa-spinner fa-spin mr-2"></i>
                    <i v-else class="fas fa-check mr-2"></i>
                    {{ isAssigning ? 'Assigning...' : 'Yes, Assign' }}
                  </button>
                  <button
                    @click="showConfirmModal = false"
                    :disabled="isAssigning"
                    class="flex-1 bg-gray-500 text-white py-3 px-4 rounded-lg hover:bg-gray-600 transition duration-200 text-base font-medium"
                  >
                    Cancel
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Success Modal -->
          <div
            v-if="showSuccessModal"
            class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4"
          >
            <div
              class="bg-white rounded-2xl shadow-2xl max-w-md w-full transform transition-all duration-500 scale-100"
            >
              <div
                class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4 text-center rounded-t-2xl"
              >
                <div
                  class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-3 shadow-lg"
                >
                  <i class="fas fa-check text-green-600 text-2xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-white">Task Assigned!</h3>
              </div>

              <div class="px-6 py-6 text-center">
                <p class="text-gray-700 text-lg mb-6">
                  Task successfully assigned to
                  <span class="font-semibold text-blue-600">{{ assignedOfficer?.name }}</span
                  >.
                </p>

                <button
                  @click="goBackToRequests"
                  class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white py-3 px-6 rounded-xl font-semibold hover:from-blue-700 hover:to-blue-800 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 text-lg"
                >
                  Back to Requests
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Fixed overlay dropdown portal -->
        <div v-show="openDropdownId" class="fixed inset-0 pointer-events-none" style="z-index: 99999" @click="closeAllDropdowns">
          <div
            class="dropdown-menu-portal pointer-events-auto fixed bg-white rounded-lg shadow-xl border border-gray-200 py-1"
            :style="{
              top: dropdownCoords.top + 'px',
              left: dropdownCoords.left + 'px',
              zIndex: 100000,
              minWidth: '12rem',
              maxWidth: '16rem',
              maxHeight: '240px',
              overflowY: 'auto',
              boxShadow: '0 10px 25px rgba(0,0,0,0.15), 0 4px 6px rgba(0,0,0,0.1)'
            }"
            @click.stop
          >
            <button
              @click.stop="executeAction('assign_task', ictOfficers.find(o => o.id === openDropdownId))"
              :disabled="(ictOfficers.find(o => o.id === openDropdownId)?.status) === 'Busy' || isAssigning"
              class="w-full text-left px-4 py-2 text-sm bg-green-50 text-green-800 border-b border-green-200 hover:bg-green-100 focus:bg-green-100 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-inset transition-all duration-200 flex items-center space-x-3 group disabled:opacity-50 disabled:cursor-not-allowed disabled:bg-gray-100 disabled:text-gray-500 first:rounded-t-lg"
            >
              <i class="fas fa-user-plus text-green-600 group-hover:text-green-700 group-focus:text-green-700 w-4 h-4 flex-shrink-0 transition-colors duration-200"></i>
              <span class="font-medium">Assign Task</span>
            </button>
            <button
              @click.stop="executeAction('view_progress', ictOfficers.find(o => o.id === openDropdownId))"
              class="w-full text-left px-4 py-2 text-sm bg-blue-50 text-blue-800 border-b border-blue-200 hover:bg-blue-100 focus:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-inset transition-all duration-200 flex items-center space-x-3 group"
            >
              <i class="fas fa-eye text-blue-600 group-hover:text-blue-700 group-focus:text-blue-700 w-4 h-4 flex-shrink-0 transition-colors duration-200"></i>
              <span class="font-medium">View Progress</span>
            </button>
            <button
              @click.stop="executeAction('cancel_task', ictOfficers.find(o => o.id === openDropdownId))"
              :disabled="(ictOfficers.find(o => o.id === openDropdownId)?.status) !== 'Assigned'"
              class="w-full text-left px-4 py-2 text-sm bg-red-50 text-red-800 hover:bg-red-100 focus:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-inset transition-all duration-200 flex items-center space-x-3 group disabled:opacity-50 disabled:cursor-not-allowed disabled:bg-gray-100 disabled:text-gray-500 last:rounded-b-lg"
            >
              <i class="fas fa-times text-red-600 group-hover:text-red-700 group-focus:text-red-700 w-4 h-4 flex-shrink-0 transition-colors duration-200"></i>
              <span class="font-medium">Cancel Task</span>
            </button>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script>
  import Header from '@/components/header.vue'
  import ModernSidebar from '@/components/ModernSidebar.vue'
  import headOfItService from '@/services/headOfItService'

  export default {
    name: 'SelectIctOfficer',
    components: {
      Header,
      ModernSidebar
    },
    data() {
      return {
        requestId: null,
        requestInfo: null,
        ictOfficers: [],
        searchQuery: '',
        statusFilter: '',
        isLoading: false,
        error: null,
        showConfirmModal: false,
        showSuccessModal: false,
        selectedOfficer: null,
        assignedOfficer: null,
        isAssigning: false,
        // Dropdown state management
        openDropdownId: null,
        dropdownCoords: { top: 0, left: 0 },
        dropdownPlacement: 'below', // 'below' | 'above' based on available space
        dropdownWidth: 192
      }
    },
    computed: {
      filteredOfficers() {
        let filtered = [...this.ictOfficers]

        // Filter by search query
        if (this.searchQuery.trim()) {
          const query = this.searchQuery.toLowerCase()
          filtered = filtered.filter(
            (officer) =>
              (officer.name || '').toLowerCase().includes(query) ||
              (officer.pf_number || '').toLowerCase().includes(query) ||
              (officer.phone_number || '').toLowerCase().includes(query)
          )
        }

        // Filter by status
        if (this.statusFilter) {
          filtered = filtered.filter(
            (officer) => (officer.status || 'Available') === this.statusFilter
          )
        }

        // Sort by availability (Available first, then by name)
        return filtered.sort((a, b) => {
          const statusA = a.status || 'Available'
          const statusB = b.status || 'Available'

          if (statusA === 'Available' && statusB !== 'Available') return -1
          if (statusA !== 'Available' && statusB === 'Available') return 1

          return (a.name || '').localeCompare(b.name || '')
        })
      }
    },
    async mounted() {
      this.requestId = this.$route.params.id
      if (!this.requestId) {
        this.error = 'No request ID provided'
        return
      }

      await Promise.all([this.fetchRequestInfo(), this.fetchIctOfficers()])

      // Add click listener to close dropdowns when clicking outside
      document.addEventListener('click', this.closeAllDropdowns)
    },

    beforeUnmount() {
      // Clean up event listeners
      document.removeEventListener('click', this.closeAllDropdowns)
    },
    methods: {
      async fetchRequestInfo() {
        try {
          const result = await headOfItService.getRequestById(this.requestId)
          if (result.success) {
            this.requestInfo = result.data
          }
        } catch (error) {
          console.error('Error fetching request info:', error)
        }
      },

      async fetchIctOfficers() {
        this.isLoading = true
        this.error = null

        try {
          const result = await headOfItService.getIctOfficers()
          if (result.success) {
            this.ictOfficers = result.data || []
          } else {
            this.error = result.message
          }
        } catch (error) {
          this.error = 'Failed to load ICT officers'
        } finally {
          this.isLoading = false
        }
      },

      async refreshOfficers() {
        await this.fetchIctOfficers()
      },

      confirmAssignTask(officer) {
        this.selectedOfficer = officer
        this.showConfirmModal = true
      },

      async assignTask() {
        if (!this.selectedOfficer) return

        this.isAssigning = true

        try {
          const result = await headOfItService.assignTaskToIctOfficer(
            this.requestId,
            this.selectedOfficer.id
          )

          if (result.success) {
            this.assignedOfficer = this.selectedOfficer
            this.showConfirmModal = false
            this.showSuccessModal = true

            // Update officer status locally
            const officerIndex = this.ictOfficers.findIndex((o) => o.id === this.selectedOfficer.id)
            if (officerIndex !== -1) {
              this.ictOfficers[officerIndex].status = 'Assigned'
            }
          } else {
            alert('Failed to assign task: ' + result.message)
          }
        } catch (error) {
          alert('Error assigning task: ' + error.message)
        } finally {
          this.isAssigning = false
        }
      },

      viewProgress(officer) {
        // Navigate to progress view or show modal
        alert(`Viewing progress for ${officer.name} - Feature coming soon!`)
      },

      async cancelTask(officer) {
        if (confirm(`Are you sure you want to cancel the task assigned to ${officer.name}?`)) {
          try {
            const result = await headOfItService.cancelTaskAssignment(this.requestId)
            if (result.success) {
              // Update officer status locally
              const officerIndex = this.ictOfficers.findIndex((o) => o.id === officer.id)
              if (officerIndex !== -1) {
                this.ictOfficers[officerIndex].status = 'Available'
              }
              alert('Task assignment cancelled successfully')
            } else {
              alert('Failed to cancel task: ' + result.message)
            }
          } catch (error) {
            alert('Error cancelling task: ' + error.message)
          }
        }
      },

      getInitials(name) {
        if (!name) return 'NA'
        return name
          .split(' ')
          .map((n) => n[0])
          .join('')
          .toUpperCase()
          .slice(0, 2)
      },

      getStatusBadgeClass(status) {
        const statusClasses = {
          Available: 'bg-green-500 text-green-900',
          Assigned: 'bg-blue-500 text-blue-900',
          Busy: 'bg-yellow-500 text-yellow-900',
          Completed: 'bg-gray-500 text-gray-900'
        }
        return statusClasses[status] || statusClasses['Available']
      },

      hasService(request, serviceType) {
        if (!request?.request_types) return false

        if (Array.isArray(request.request_types)) {
          return request.request_types.some((type) =>
            type.toLowerCase().includes(serviceType.toLowerCase())
          )
        }

        if (typeof request.request_types === 'string') {
          return request.request_types.toLowerCase().includes(serviceType.toLowerCase())
        }

        return false
      },

      goBack() {
        this.$router.push(`/head_of_it-dashboard/both-service-form/${this.requestId}`)
      },

      goBackToRequests() {
        this.$router.push('/head_of_it-dashboard/combined-requests')
      },

      clearSearch() {
        this.searchQuery = ''
      },

      clearAllFilters() {
        this.searchQuery = ''
        this.statusFilter = ''
      },

      // Dropdown management methods
      toggleDropdown(officerId, event) {
        event?.stopPropagation()
        const willOpen = this.openDropdownId !== officerId
        this.openDropdownId = willOpen ? officerId : null
        if (!willOpen) return

        try {
          const button = event?.target?.closest('.three-dot-button')
          if (!button) {
            this.dropdownPlacement = 'below'
            this.dropdownCoords = { top: 0, left: 0 }
            return
          }
          const rect = button.getBoundingClientRect()
          const viewportHeight = window.innerHeight
          const viewportWidth = window.innerWidth
          const spaceBelow = viewportHeight - rect.bottom
          const spaceAbove = rect.top
          const estimatedMenuHeight = 180
          const menuWidth = this.dropdownWidth

          // Decide placement
          this.dropdownPlacement = spaceBelow < estimatedMenuHeight && spaceAbove > spaceBelow ? 'above' : 'below'

          // Compute left so that menu right aligns with button right but stays within viewport
          let left = rect.right - menuWidth
          left = Math.max(8, Math.min(left, viewportWidth - menuWidth - 8))

          // Compute top based on placement
          let top = this.dropdownPlacement === 'below' ? rect.bottom + 8 : rect.top - estimatedMenuHeight - 8
          top = Math.max(8, Math.min(top, viewportHeight - 8))

          this.dropdownCoords = { top, left }
        } catch (e) {
          this.dropdownPlacement = 'below'
          this.dropdownCoords = { top: 0, left: 0 }
        }
      },

      calculateDropdownPosition(event) {
        if (!event || !event.target) return

        const button = event.target.closest('.three-dot-button')
        if (!button) return

        const rect = button.getBoundingClientRect()
        const isMobile = window.innerWidth <= 768
        const dropdownWidth = isMobile ? 160 : 192 // Smaller on mobile
        const dropdownHeight = 120 // Approximate height for 3 items

        // Calculate initial position
        let top = rect.bottom + 8 // 8px margin below button
        let left = rect.right - dropdownWidth // Align right edge with button

        // Get viewport dimensions
        const viewportWidth = window.innerWidth
        const viewportHeight = window.innerHeight
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop

        // Adjust for scroll position
        top += scrollTop

        // Adjust horizontal position if too far right or left
        if (left < 10) {
          left = 10
        } else if (left + dropdownWidth > viewportWidth - 10) {
          left = viewportWidth - dropdownWidth - 10
        }

        // For mobile, center the dropdown better
        if (isMobile) {
          left = Math.max(10, Math.min(left, viewportWidth - dropdownWidth - 10))
        }

        // Adjust vertical position if too far down
        const bottomSpace = viewportHeight - (rect.bottom - scrollTop)
        if (bottomSpace < dropdownHeight + 20) {
          // Show above button instead
          top = rect.top + scrollTop - dropdownHeight - 8
        }

        // Ensure dropdown doesn't go above viewport
        if (top < scrollTop + 10) {
          top = scrollTop + 10
        }

        console.log('Dropdown position calculated:', {
          top,
          left,
          viewportWidth,
          viewportHeight,
          isMobile
        })
        this.dropdownPosition = { top, left }
      },

      closeAllDropdowns(event) {
        // Only close if clicking inside menu or on button, otherwise close
        if (
          event &&
          (event.target.closest('.dropdown-menu') || event.target.closest('.three-dot-button'))
        ) {
          return
        }
        this.openDropdownId = null
      },

      // Execute action based on key
      executeAction(actionKey, officer) {
        console.log('SelectIctOfficer: Executing action:', actionKey, 'for officer:', officer.id)
        this.openDropdownId = null

        switch (actionKey) {
          case 'assign_task':
            this.confirmAssignTask(officer)
            break
          case 'view_progress':
            this.viewProgress(officer)
            break
          case 'cancel_task':
            this.cancelTask(officer)
            break
          default:
            console.warn('Unknown action:', actionKey)
        }
      }
    }
  }
</script>

<style scoped>
  .sidebar-narrow {
    flex-shrink: 0;
  }

  .medical-glass-card {
    background: rgba(59, 130, 246, 0.15);
    backdrop-filter: blur(25px);
    -webkit-backdrop-filter: blur(25px);
    border: 2px solid rgba(96, 165, 250, 0.3);
    box-shadow:
      0 8px 32px rgba(29, 78, 216, 0.4),
      inset 0 1px 0 rgba(255, 255, 255, 0.1);
  }

  /* Three-dot menu enhancements */
  .three-dot-menu {
    position: relative;
    /* no z-index by default to avoid overlaying pagination/footer */
  }

  /* Removed old in-cell dropdown styling since menu is now in portal layer */
  .dropdown-menu-portal button:hover {
    transform: translateX(2px);
  }

  /* Ensure the footer/pagination remains above table body (unchanged) */
  .medical-glass-card > .p-6 + div .bg-blue-800\/30 {
    position: relative;
    z-index: 3500;
  }

  /* Ensure table cells do not clip absolutely-positioned children */
  td, th {
    overflow: visible !important;
  }

  /* Animation for dropdown appearance */
  .dropdown-menu {
    animation: dropdownFadeIn 0.15s ease-out;
    transform-origin: top right;
  }

  @keyframes dropdownFadeIn {
    from {
      opacity: 0;
      transform: scale(0.95) translateY(-5px);
    }
    to {
      opacity: 1;
      transform: scale(1) translateY(0);
    }
  }

  /* Hover effects for better UX */
  .dropdown-menu button:hover {
    transform: translateX(2px);
  }

  /* High contrast focus states for accessibility */
  .three-dot-button:focus {
    outline: 2px solid #3b82f6 !important;
    outline-offset: 2px;
  }

  .dropdown-menu button:focus {
    outline: 2px solid #3b82f6;
    outline-offset: -2px;
    background-color: #f3f4f6;
  }

  /* Mobile Responsive */
  @media (max-width: 768px) {
    .overflow-x-auto {
      -webkit-overflow-scrolling: touch;
    }

    .min-w-full {
      min-width: 800px;
    }

    .px-6 {
      padding-left: 1rem;
      padding-right: 1rem;
    }

    /* Mobile dropdown adjustments */
    .dropdown-menu {
      min-width: 10rem !important;
      max-width: 14rem !important;
      /* Position will be handled by JavaScript for mobile */
    }

    /* Increase touch target size on mobile */
    .three-dot-button {
      padding: 12px !important;
      min-width: 44px;
      min-height: 44px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .dropdown-menu button {
      padding: 12px 16px !important;
      font-size: 16px !important;
    }
  }

  /* Tablet adjustments */
  @media (min-width: 769px) and (max-width: 1024px) {
    .dropdown-menu {
      min-width: 11rem;
      max-width: 15rem;
    }
  }
</style>
