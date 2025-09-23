<template>
  <div class="flex flex-col h-screen">
    <Header />
    <div class="flex flex-1 overflow-hidden">
      <div class="sidebar-narrow">
        <ModernSidebar />
      </div>
      <main
        class="flex-1 p-6 bg-gradient-to-br from-blue-900 via-blue-800 to-teal-900 overflow-y-auto"
      >
        <div class="max-w-7xl mx-auto">
          <!-- Header -->
          <div class="mb-6">
            <button
              @click="goBack"
              class="mb-4 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition duration-200 flex items-center text-base"
            >
              <i class="fas fa-arrow-left mr-2"></i>
              Back to Processing
            </button>
            <h1 class="text-3xl font-bold text-white">Select ICT Officer</h1>
            <p class="text-blue-200 mt-2 text-lg">
              Choose an ICT Officer to assign the task for Request ID:
              <span class="font-semibold text-white">{{ requestId }}</span>
            </p>
          </div>

          <!-- Request Info Card -->
          <div v-if="requestInfo" class="medical-glass-card rounded-xl p-4 mb-6">
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center">
                  <i class="fas fa-user text-white text-xl"></i>
                </div>
                <div>
                  <h3 class="text-white font-semibold text-lg">{{ requestInfo.staff_name }}</h3>
                  <p class="text-blue-200 text-base">
                    PF: {{ requestInfo.pf_number }} | Dept: {{ requestInfo.department }}
                  </p>
                </div>
              </div>
              <div class="text-right">
                <p class="text-blue-200 text-sm">Requesting access to:</p>
                <div class="flex gap-2 mt-1">
                  <span
                    v-if="hasService(requestInfo, 'jeeva')"
                    class="px-2 py-1 rounded text-xs bg-blue-500 text-white"
                    >Jeeva</span
                  >
                  <span
                    v-if="hasService(requestInfo, 'wellsoft')"
                    class="px-2 py-1 rounded text-xs bg-green-500 text-white"
                    >Wellsoft</span
                  >
                  <span
                    v-if="hasService(requestInfo, 'internet')"
                    class="px-2 py-1 rounded text-xs bg-cyan-500 text-white"
                    >Internet</span
                  >
                </div>
              </div>
            </div>
          </div>

          <!-- Error Display -->
          <div
            v-if="error"
            class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6"
          >
            <h3 class="font-bold text-xl">Error</h3>
            <p class="text-lg">{{ error }}</p>
            <button
              @click="fetchIctOfficers"
              class="mt-2 bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 text-base font-medium"
            >
              Retry
            </button>
          </div>

          <!-- Search and Filter -->
          <div class="medical-glass-card rounded-xl p-4 mb-6">
            <div class="flex flex-col md:flex-row gap-4">
              <div class="flex-1">
                <input
                  v-model="searchQuery"
                  type="text"
                  placeholder="Search by name, PF number, or phone number..."
                  class="w-full px-4 py-3 bg-white/20 border border-blue-300/30 rounded-lg text-white placeholder-blue-200/60 text-base focus:outline-none focus:ring-2 focus:ring-blue-400"
                />
              </div>
              <div class="flex gap-3">
                <select
                  v-model="statusFilter"
                  class="px-4 py-3 bg-white/20 border border-blue-300/30 rounded-lg text-white text-base focus:outline-none focus:ring-2 focus:ring-blue-400"
                >
                  <option value="">All Officers</option>
                  <option value="Available">Available</option>
                  <option value="Assigned">Assigned</option>
                  <option value="Busy">Busy</option>
                  <option value="Completed">Completed</option>
                </select>
                <button
                  @click="refreshOfficers"
                  class="px-6 py-3 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition duration-200 text-base font-medium"
                >
                  <i class="fas fa-sync mr-2"></i>
                  Refresh
                </button>
              </div>
            </div>
          </div>

          <!-- Loading State -->
          <div v-if="isLoading" class="text-center py-12">
            <div
              class="w-16 h-16 border-4 border-blue-600 border-t-transparent rounded-full animate-spin mx-auto mb-4"
            ></div>
            <p class="text-white text-lg">Loading ICT Officers...</p>
          </div>

          <!-- ICT Officers Table -->
          <div v-if="!isLoading" class="medical-glass-card rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
              <table class="w-full min-w-full">
                <thead class="bg-blue-800/50">
                  <tr>
                    <th class="px-6 py-4 text-left text-blue-100 text-base font-semibold">S/N</th>
                    <th class="px-6 py-4 text-left text-blue-100 text-base font-semibold">
                      Full Name
                    </th>
                    <th class="px-6 py-4 text-left text-blue-100 text-base font-semibold">
                      PF Number
                    </th>
                    <th class="px-6 py-4 text-left text-blue-100 text-base font-semibold">
                      Phone Number
                    </th>
                    <th class="px-6 py-4 text-left text-blue-100 text-base font-semibold">
                      Status
                    </th>
                    <th class="px-6 py-4 text-center text-blue-100 text-base font-semibold">
                      Actions
                    </th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-blue-300/20">
                  <tr
                    v-for="(officer, index) in filteredOfficers"
                    :key="officer.id"
                    class="hover:bg-blue-700/30 transition-colors duration-200"
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
                      <span class="text-white text-base">{{ officer.phone_number || 'N/A' }}</span>
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
                    <td class="px-6 py-4">
                      <div class="flex justify-center space-x-2">
                        <button
                          @click="confirmAssignTask(officer)"
                          :disabled="officer.status === 'Busy' || isAssigning"
                          class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed transition duration-200 text-sm font-medium"
                        >
                          <i class="fas fa-user-plus mr-1"></i>
                          Assign Task
                        </button>
                        <button
                          @click="viewProgress(officer)"
                          class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200 text-sm font-medium"
                        >
                          <i class="fas fa-eye mr-1"></i>
                          View Progress
                        </button>
                        <button
                          @click="cancelTask(officer)"
                          :disabled="officer.status !== 'Assigned'"
                          class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed transition duration-200 text-sm font-medium"
                        >
                          <i class="fas fa-times mr-1"></i>
                          Cancel Task
                        </button>
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

        <!-- Assignment Confirmation Modal -->
        <div
          v-if="showConfirmModal"
          class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4"
        >
          <div
            class="bg-white rounded-2xl shadow-2xl max-w-md w-full transform transition-all duration-300 scale-100"
          >
            <div
              class="bg-gradient-to-r from-orange-600 to-orange-700 px-6 py-4 text-center rounded-t-2xl"
            >
              <div
                class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-3 shadow-lg"
              >
                <i class="fas fa-question text-orange-600 text-2xl"></i>
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
        isAssigning: false
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
        this.$router.push(`/head_of_it-dashboard/process-request/${this.requestId}`)
      },

      goBackToRequests() {
        this.$router.push('/head_of_it-dashboard/combined-requests')
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
  }
</style>
