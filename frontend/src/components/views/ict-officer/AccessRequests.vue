<template>
  <div class="flex flex-col h-screen">
    <Header />
    <div class="flex flex-1 overflow-hidden">
      <div class="sidebar-narrow">
        <ModernSidebar />
      </div>
      <main class="flex-1 p-4 bg-blue-900 overflow-y-auto">
        <div class="max-w-full mx-auto">
          <!-- Error Display -->
          <div
            v-if="error"
            class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"
          >
            <h3 class="font-bold text-xl">Error</h3>
            <p class="text-lg">{{ error }}</p>
            <button
              @click="fetchAccessRequests"
              class="mt-2 bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 text-base font-medium"
            >
              Retry
            </button>
          </div>

          <!-- Stats -->
          <div class="grid grid-cols-4 gap-4 mb-6">
            <div class="bg-green-600/25 border border-green-400/40 p-4 rounded-lg">
              <h3 class="text-green-200 text-lg font-semibold">Available for Assignment</h3>
              <p class="text-white text-3xl font-bold">{{ stats.unassigned }}</p>
            </div>
            <div class="bg-blue-600/25 border border-blue-400/40 p-4 rounded-lg">
              <h3 class="text-blue-200 text-lg font-semibold">Assigned to ICT</h3>
              <p class="text-white text-3xl font-bold">{{ stats.assigned }}</p>
            </div>
            <div class="bg-purple-600/25 border border-purple-400/40 p-4 rounded-lg">
              <h3 class="text-purple-200 text-lg font-semibold">In Progress</h3>
              <p class="text-white text-3xl font-bold">{{ stats.inProgress }}</p>
            </div>
            <div class="bg-teal-600/25 border border-teal-400/40 p-4 rounded-lg">
              <h3 class="text-teal-200 text-lg font-semibold">Total Requests</h3>
              <p class="text-white text-3xl font-bold">{{ stats.total }}</p>
            </div>
          </div>

          <!-- Filters -->
          <div class="bg-white/10 rounded-lg p-4 mb-6">
            <div class="flex gap-4">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Search by staff name, PF number, or department..."
                class="flex-1 px-3 py-2 bg-white/20 border border-blue-300/30 rounded text-white placeholder-blue-200/60 text-base"
              />
              <select
                v-model="statusFilter"
                class="px-3 py-2 bg-white/20 border border-blue-300/30 rounded text-white text-base"
              >
                <option value="">All Requests</option>
                <option value="unassigned">Available for Assignment</option>
                <option value="assigned_to_ict">Assigned to ICT</option>
                <option value="implementation_in_progress">In Progress</option>
                <option value="completed">Completed</option>
              </select>
              <button
                @click="refreshRequests"
                class="px-6 py-2 bg-teal-600 text-white rounded hover:bg-teal-700 text-base font-medium"
              >
                Refresh
              </button>
            </div>
          </div>

          <!-- Requests Table -->
          <div class="bg-white/10 rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
              <table class="w-full">
                <thead class="bg-blue-800/50">
                  <tr>
                    <th class="px-4 py-3 text-left text-blue-100 text-base font-semibold">
                      Request ID
                    </th>
                    <th class="px-4 py-3 text-left text-blue-100 text-base font-semibold">
                      Request Type
                    </th>
                    <th class="px-4 py-3 text-left text-blue-100 text-base font-semibold">
                      Requested Modules
                    </th>
                    <th class="px-4 py-3 text-left text-blue-100 text-base font-semibold">
                      Requester Name & PF Number
                    </th>
                    <th class="px-4 py-3 text-left text-blue-100 text-base font-semibold">
                      Head of IT Approval Date
                    </th>
                    <th class="px-4 py-3 text-left text-blue-100 text-base font-semibold">
                      Status
                    </th>
                    <th class="px-4 py-3 text-center text-blue-100 text-base font-semibold">
                      Actions
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="request in filteredRequests"
                    :key="request.id"
                    :class="[
                      'border-t border-blue-300/20 hover:bg-blue-700/30 transition-colors duration-200',
                      isUnassigned(request.status)
                        ? 'bg-yellow-900/20 border-l-4 border-yellow-400'
                        : 'bg-green-900/10 border-l-4 border-green-600'
                    ]"
                  >
                    <!-- Request ID -->
                    <td class="px-4 py-3">
                      <div class="flex items-center space-x-2">
                        <!-- Status Indicator Icon -->
                        <div class="flex-shrink-0">
                          <i
                            v-if="isUnassigned(request.status)"
                            class="fas fa-clock text-yellow-400 text-base"
                            title="Available for assignment"
                          ></i>
                          <i
                            v-else
                            class="fas fa-check-circle text-green-400 text-base"
                            title="Assigned or in progress"
                          ></i>
                        </div>
                        <div>
                          <div class="text-white font-medium text-base">
                            {{
                              request.request_id || `REQ-${request.id.toString().padStart(6, '0')}`
                            }}
                          </div>
                          <div class="text-purple-300 text-sm">ID: {{ request.id }}</div>
                        </div>
                      </div>
                    </td>

                    <!-- Request Type -->
                    <td class="px-4 py-3">
                      <div class="text-white font-medium text-base">
                        {{ getRequestType(request) }}
                      </div>
                      <div class="text-blue-300 text-sm">
                        {{ request.department_name || request.department || 'Unknown Dept' }}
                      </div>
                    </td>

                    <!-- Requested Modules -->
                    <td class="px-4 py-3">
                      <div class="flex flex-wrap gap-1">
                        <span
                          v-if="hasService(request, 'jeeva')"
                          class="px-2 py-1 rounded text-sm bg-blue-100 text-blue-800"
                        >
                          Jeeva
                        </span>
                        <span
                          v-if="hasService(request, 'wellsoft')"
                          class="px-2 py-1 rounded text-sm bg-green-100 text-green-800"
                        >
                          Wellsoft
                        </span>
                        <span
                          v-if="hasService(request, 'internet')"
                          class="px-2 py-1 rounded text-sm bg-cyan-100 text-cyan-800"
                        >
                          Internet
                        </span>
                      </div>
                    </td>

                    <!-- Requester Name & PF Number -->
                    <td class="px-4 py-3">
                      <div class="text-white font-medium text-base">
                        {{ request.staff_name || request.full_name || 'Unknown User' }}
                      </div>
                      <div class="text-blue-300 text-sm">
                        {{ request.phone || request.phone_number || 'No phone' }}
                      </div>
                      <div v-if="request.pf_number" class="text-teal-300 text-sm">
                        PF: {{ request.pf_number }}
                      </div>
                    </td>

                    <!-- Head of IT Approval Date -->
                    <td class="px-4 py-3">
                      <div class="text-white font-medium text-base">
                        {{
                          formatDate(
                            request.head_of_it_approval_date || request.head_of_it_approved_at
                          )
                        }}
                      </div>
                      <div class="text-blue-300 text-sm">
                        {{
                          formatTime(
                            request.head_of_it_approval_date || request.head_of_it_approved_at
                          )
                        }}
                      </div>
                    </td>

                    <!-- Status -->
                    <td class="px-4 py-3">
                      <div class="flex flex-col">
                        <span
                          :class="getStatusBadgeClass(request.status)"
                          class="rounded text-base font-medium inline-block"
                          :style="{ padding: '4px 8px', width: 'fit-content' }"
                        >
                          {{ getStatusText(request.status) }}
                        </span>
                      </div>
                    </td>

                    <!-- Actions -->
                    <td class="px-4 py-3 text-center">
                      <div class="relative inline-block">
                        <!-- Three-dot menu button -->
                        <button
                          @click="toggleDropdown(request.id, $event)"
                          class="w-8 h-8 flex items-center justify-center text-white hover:bg-white/10 rounded-full transition-colors duration-200"
                          :title="
                            'Actions for ' +
                            (request.request_id || 'REQ-' + request.id.toString().padStart(6, '0'))
                          "
                        >
                          <i class="fas fa-ellipsis-v text-lg"></i>
                        </button>

                        <!-- Dropdown menu -->
                        <div
                          v-if="activeDropdown === request.id"
                          class="fixed w-48 bg-white rounded-lg shadow-2xl border border-gray-200 py-1"
                          :style="getDropdownPosition()"
                          style="z-index: 9999"
                          @click.stop
                        >
                          <!-- Always available actions -->
                          <button
                            @click="handleMenuAction('viewRequest', request)"
                            class="w-full text-left px-4 py-2 text-sm bg-blue-50 text-blue-800 border-b border-blue-200 hover:bg-blue-100 focus:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-inset transition-all duration-200 flex items-center group first:rounded-t-lg"
                          >
                            <i
                              class="fas fa-eye mr-3 text-blue-600 group-hover:text-blue-700 group-focus:text-blue-700 transition-colors duration-200"
                            ></i>
                            <span class="font-medium">View & Process</span>
                          </button>

                          <button
                            @click="handleMenuAction('viewTimeline', request)"
                            class="w-full text-left px-4 py-2 text-sm bg-indigo-50 text-indigo-800 border-b border-indigo-200 hover:bg-indigo-100 focus:bg-indigo-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-inset transition-all duration-200 flex items-center group"
                          >
                            <i
                              class="fas fa-history mr-3 text-indigo-600 group-hover:text-indigo-700 group-focus:text-indigo-700 transition-colors duration-200"
                            ></i>
                            <span class="font-medium">View Timeline</span>
                          </button>

                          <!-- Conditional actions based on status -->
                          <template v-if="isUnassigned(request.status)">
                            <button
                              @click="handleMenuAction('assignTask', request)"
                              class="w-full text-left px-4 py-2 text-sm bg-green-50 text-green-800 hover:bg-green-100 focus:bg-green-100 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-inset transition-all duration-200 flex items-center group last:rounded-b-lg"
                            >
                              <i
                                class="fas fa-user-plus mr-3 text-green-600 group-hover:text-green-700 group-focus:text-green-700 transition-colors duration-200"
                              ></i>
                              <span class="font-medium">Assign Task</span>
                            </button>
                          </template>

                          <template v-else-if="request.status === 'assigned_to_ict'">
                            <button
                              @click="handleMenuAction('updateProgress', request)"
                              class="w-full text-left px-4 py-2 text-sm bg-purple-50 text-purple-800 border-b border-purple-200 hover:bg-purple-100 focus:bg-purple-100 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-inset transition-all duration-200 flex items-center group"
                            >
                              <i
                                class="fas fa-tasks mr-3 text-purple-600 group-hover:text-purple-700 group-focus:text-purple-700 transition-colors duration-200"
                              ></i>
                              <span class="font-medium">Update Progress</span>
                            </button>
                            <button
                              @click="handleMenuAction('cancelTask', request)"
                              class="w-full text-left px-4 py-2 text-sm bg-red-50 text-red-800 hover:bg-red-100 focus:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-inset transition-all duration-200 flex items-center group last:rounded-b-lg"
                            >
                              <i
                                class="fas fa-times-circle mr-3 text-red-600 group-hover:text-red-700 group-focus:text-red-700 transition-colors duration-200"
                              ></i>
                              <span class="font-medium">Cancel Task</span>
                            </button>
                          </template>

                          <template v-else-if="request.status === 'implementation_in_progress'">
                            <button
                              @click="handleMenuAction('cancelTask', request)"
                              class="w-full text-left px-4 py-2 text-sm bg-red-50 text-red-800 hover:bg-red-100 focus:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-inset transition-all duration-200 flex items-center group last:rounded-b-lg"
                            >
                              <i
                                class="fas fa-times-circle mr-3 text-red-600 group-hover:text-red-700 group-focus:text-red-700 transition-colors duration-200"
                              ></i>
                              <span class="font-medium">Cancel Task</span>
                            </button>
                          </template>
                        </div>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Empty State -->
            <div v-if="filteredRequests.length === 0" class="text-center py-12">
              <h3 class="text-white text-xl font-medium mb-2">No requests found</h3>
              <p class="text-blue-300 text-base">
                {{
                  searchQuery || statusFilter
                    ? 'No requests match your current filters.'
                    : 'No access requests approved by Head of IT are currently available.'
                }}
              </p>
            </div>

            <!-- Pagination -->
            <div v-if="filteredRequests.length > 0" class="px-4 py-3 border-t border-blue-300/30">
              <div class="text-blue-300 text-base">
                Showing {{ filteredRequests.length }} of {{ accessRequests.length }} requests
              </div>
            </div>
          </div>

          <AppFooter />
        </div>
      </main>
    </div>

    <!-- Loading Modal -->
    <div
      v-if="isLoading"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
      <div class="bg-white rounded-lg shadow-xl p-8 text-center">
        <div
          class="w-16 h-16 border-4 border-blue-600 border-t-transparent rounded-full animate-spin mx-auto mb-4"
        ></div>
        <p class="text-gray-600 text-base font-medium">Loading access requests...</p>
      </div>
    </div>

    <!-- Timeline Modal -->
    <RequestTimeline
      :show="showTimeline"
      :request-id="selectedRequestId"
      @close="closeTimeline"
      @updated="handleTimelineUpdate"
      @openUpdateProgress="openUpdateProgress"
    />

    <!-- Update Progress Modal (separate from timeline) -->
    <div
      v-if="showUpdateProgress && selectedRequest"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[10001]"
      @click.self="closeUpdateProgress"
    >
      <div class="max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <UpdateProgress
          :show="showUpdateProgress"
          :request-id="selectedRequestId"
          :request-data="selectedRequest"
          @close="closeUpdateProgress"
          @updated="handleUpdateProgressUpdate"
        />
      </div>
    </div>
  </div>
</template>

<script>
  import Header from '@/components/header.vue'
  import ModernSidebar from '@/components/ModernSidebar.vue'
  import AppFooter from '@/components/footer.vue'
  import RequestTimeline from '@/components/common/RequestTimeline.vue'
  import UpdateProgress from '@/components/common/UpdateProgress.vue'
  import { useAuth } from '@/composables/useAuth'
  import ictOfficerService from '@/services/ictOfficerService'

  export default {
    name: 'IctAccessRequests',
    components: {
      Header,
      ModernSidebar,
      AppFooter,
      RequestTimeline,
      UpdateProgress
    },
    data() {
      return {
        accessRequests: [],
        searchQuery: '',
        statusFilter: '',
        isLoading: false,
        stats: {
          unassigned: 0,
          assigned: 0,
          inProgress: 0,
          total: 0
        },
        error: null,
        // Timeline modal
        showTimeline: false,
        selectedRequestId: null,
        selectedRequest: null,
        // Update progress modal
        showUpdateProgress: false,
        // Dropdown menu state
        activeDropdown: null,
        dropdownPosition: null
      }
    },

    computed: {
      filteredRequests() {
        if (!Array.isArray(this.accessRequests)) {
          console.warn('IctAccessRequests: accessRequests is not an array:', this.accessRequests)
          return []
        }

        let filtered = this.accessRequests

        // Filter by search query
        if (this.searchQuery) {
          const query = this.searchQuery.toLowerCase()
          filtered = filtered.filter(
            (request) =>
              (request.staff_name || request.full_name || '').toLowerCase().includes(query) ||
              (request.pf_number || '').toLowerCase().includes(query) ||
              (request.department_name || request.department || '').toLowerCase().includes(query) ||
              (request.request_id || '').toLowerCase().includes(query)
          )
        }

        // Filter by status
        if (this.statusFilter) {
          if (this.statusFilter === 'unassigned') {
            filtered = filtered.filter((request) => this.isUnassigned(request.status))
          } else {
            filtered = filtered.filter((request) => request.status === this.statusFilter)
          }
        }

        // Sort: Unassigned first, then by date
        return filtered.sort((a, b) => {
          const aUnassigned = this.isUnassigned(a.status)
          const bUnassigned = this.isUnassigned(b.status)

          if (aUnassigned && !bUnassigned) return -1
          if (!aUnassigned && bUnassigned) return 1

          const getRelevantDate = (request) => {
            return new Date(
              request.head_of_it_approval_date ||
                request.head_of_it_approved_at ||
                request.created_at ||
                0
            )
          }

          const dateA = getRelevantDate(a)
          const dateB = getRelevantDate(b)

          return dateA - dateB
        })
      }
    },
    async mounted() {
      try {
        console.log('IctAccessRequests: Component mounted, initializing...')
        const { ROLES, requireRole } = useAuth()
        requireRole([ROLES.ICT_OFFICER])
        await this.fetchAccessRequests()
        console.log('IctAccessRequests: Component initialized successfully')
        
        // Refresh notification badges when component loads
        this.refreshNotificationBadge()

        // Add click outside listener for dropdown
        document.addEventListener('click', this.handleClickOutside)
      } catch (error) {
        console.error('IctAccessRequests: Error during mount:', error)
        this.error = 'Failed to initialize component: ' + error.message
        this.isLoading = false
      }
    },
    
    async activated() {
      // This is called when the component is activated (navigated to)
      // Useful for refreshing data and notifications
      console.log('IctAccessRequests: Component activated, refreshing data...')
      await this.fetchAccessRequests()
      this.refreshNotificationBadge()
    },

    beforeUnmount() {
      // Remove click outside listener
      document.removeEventListener('click', this.handleClickOutside)
    },
    methods: {
      async fetchAccessRequests() {
        console.log('🔄 IctAccessRequests: Starting to fetch access requests...')
        this.isLoading = true
        this.error = null

        try {
          // Fetch access requests approved by Head of IT using the service
          const result = await ictOfficerService.getAccessRequests()

          if (result.success) {
            console.log('✅ IctAccessRequests: Requests loaded successfully:', result.data.length)
            this.accessRequests = result.data || []

            // Calculate stats
            this.stats = {
              unassigned: this.accessRequests.filter((r) => this.isUnassigned(r.status)).length,
              assigned: this.accessRequests.filter((r) => r.status === 'assigned_to_ict').length,
              inProgress: this.accessRequests.filter(
                (r) => r.status === 'implementation_in_progress'
              ).length,
              total: this.accessRequests.length
            }
          } else {
            console.error('❌ IctAccessRequests: Failed to load requests:', result.message)
            this.error = result.message
            this.accessRequests = []
          }
        } catch (err) {
          console.error('❌ IctAccessRequests: Error loading requests:', err)
          this.error = 'Network error while loading access requests. Please check your connection.'
          this.accessRequests = []
        } finally {
          this.isLoading = false
        }
      },

      async refreshRequests() {
        console.log('🔄 IctAccessRequests: Refreshing requests...')
        await this.fetchAccessRequests()
      },

      viewRequest(request) {
        console.log('👁️ IctAccessRequests: Viewing request details:', request.id)
        this.$router.push(`/both-service-form/${request.id}`)
      },

      async assignTask(request) {
        console.log('📋 IctAccessRequests: Assigning task for request:', request.id)

        try {
          const result = await ictOfficerService.assignTaskToSelf(
            request.id,
            'Task assigned by ICT Officer'
          )

          if (result.success) {
            // Refresh the requests list to show updated status
            await this.fetchAccessRequests()
            // Trigger notification badge refresh
            this.refreshNotificationBadge()
            // Show success message (you could use a toast notification here)
            alert('Task assigned successfully!')
          } else {
            alert('Failed to assign task: ' + result.message)
          }
        } catch (error) {
          console.error('Error assigning task:', error)
          alert('Network error while assigning task')
        }
      },

      async updateProgress(request) {
        console.log('📈 IctAccessRequests: Updating progress for request:', request.id)

        // For now, just set to in progress status
        // In a real application, you might want to show a modal to capture progress details
        try {
          const result = await ictOfficerService.updateProgress(
            request.id,
            'implementation_in_progress',
            'Implementation started'
          )

          if (result.success) {
            // Refresh the requests list to show updated status
            await this.fetchAccessRequests()
            // Trigger notification badge refresh
            this.refreshNotificationBadge()
            alert('Progress updated successfully!')
          } else {
            alert('Failed to update progress: ' + result.message)
          }
        } catch (error) {
          console.error('Error updating progress:', error)
          alert('Network error while updating progress')
        }
      },

      async cancelTask(request) {
        console.log('❌ IctAccessRequests: Canceling task for request:', request.id)

        // Show confirmation dialog and handle cancellation
        const reason = prompt('Please provide a reason for canceling this task:')
        if (reason && reason.trim()) {
          try {
            const result = await ictOfficerService.cancelTask(request.id, reason)

            if (result.success) {
              // Refresh the requests list to show updated status
              await this.fetchAccessRequests()
              // Trigger notification badge refresh
              this.refreshNotificationBadge()
              alert('Task canceled successfully!')
            } else {
              alert('Failed to cancel task: ' + result.message)
            }
          } catch (error) {
            console.error('Error canceling task:', error)
            alert('Network error while canceling task')
          }
        }
      },

      isUnassigned(status) {
        // Status indicating request is available for assignment
        return status === 'head_of_it_approved' || !status
      },

      hasService(request, serviceType) {
        // Check if request includes a specific service type
        if (serviceType === 'jeeva') {
          return (
            request.jeeva_access_required ||
            (request.request_types && request.request_types.includes('jeeva'))
          )
        }
        if (serviceType === 'wellsoft') {
          return (
            request.wellsoft_access_required ||
            (request.request_types && request.request_types.includes('wellsoft'))
          )
        }
        if (serviceType === 'internet') {
          return (
            request.internet_access_required ||
            (request.request_types && request.request_types.includes('internet'))
          )
        }
        return false
      },

      getRequestType(request) {
        const services = []
        if (this.hasService(request, 'jeeva')) services.push('Jeeva')
        if (this.hasService(request, 'wellsoft')) services.push('Wellsoft')
        if (this.hasService(request, 'internet')) services.push('Internet')
        return services.length > 0 ? services.join(' + ') : 'Access Request'
      },

      getStatusBadgeClass(status) {
        const statusClasses = {
          head_of_it_approved: 'bg-yellow-500 text-yellow-900',
          assigned_to_ict: 'bg-blue-500 text-blue-900',
          implementation_in_progress: 'bg-purple-500 text-purple-900',
          completed: 'bg-emerald-500 text-emerald-900',
          cancelled: 'bg-red-500 text-red-900'
        }
        return statusClasses[status] || 'bg-gray-500 text-gray-900'
      },

      getStatusText(status) {
        const statusTexts = {
          head_of_it_approved: 'Available for Assignment',
          assigned_to_ict: 'Assigned to ICT Officer',
          implementation_in_progress: 'Implementation in Progress',
          completed: 'Implementation Completed',
          cancelled: 'Task Cancelled'
        }
        return (
          statusTexts[status] ||
          status?.replace(/_/g, ' ').replace(/\b\w/g, (l) => l.toUpperCase()) ||
          'Unknown Status'
        )
      },

      viewTimeline(request) {
        console.log('📅 IctAccessRequests: Opening timeline for request:', request.id)
        this.selectedRequestId = request.id
        this.selectedRequest = request
        this.showTimeline = true
      },

      closeTimeline() {
        console.log('📅 IctAccessRequests: Closing timeline modal')
        this.showTimeline = false
        this.selectedRequestId = null
        this.selectedRequest = null
        this.showUpdateProgress = false
      },

      async handleTimelineUpdate() {
        console.log('🔄 IctAccessRequests: Timeline updated, refreshing requests list...')
        await this.fetchAccessRequests()
        // Also refresh the notification badge
        this.refreshNotificationBadge()
      },

      openUpdateProgress(request) {
        console.log(
          '📝 IctAccessRequests: Opening update progress section for request:',
          request?.id
        )
        if (request && this.canShowUpdateProgress(request)) {
          this.selectedRequest = request
          this.selectedRequestId = request.id
          this.showUpdateProgress = true
        }
      },

      closeUpdateProgress() {
        console.log('📝 IctAccessRequests: Closing update progress section')
        this.showUpdateProgress = false
      },

      async handleUpdateProgressUpdate() {
        console.log('🔄 IctAccessRequests: Progress updated, refreshing data...')
        // Refresh the requests list
        await this.fetchAccessRequests()
        // Refresh the notification badge
        this.refreshNotificationBadge()
        // Close both timeline and update progress
        this.closeTimeline()
      },

      canShowUpdateProgress(request) {
        // Only show update progress for ICT Officer assigned requests that are not completed
        if (!request) return false

        // Get current user from auth
        const { user } = useAuth()
        const currentUserId = user.value?.id

        return (
          request.ict_officer_user_id &&
          request.ict_officer_user_id === currentUserId &&
          !request.ict_officer_implemented_at &&
          request.ict_officer_status !== 'implemented' &&
          request.ict_officer_status !== 'rejected' &&
          (request.status === 'assigned_to_ict' || request.status === 'implementation_in_progress')
        )
      },


      // Dropdown functionality
      toggleDropdown(requestId, event) {
        this.activeDropdown = this.activeDropdown === requestId ? null : requestId

        if (this.activeDropdown === requestId && event) {
          // Store the button position for dropdown positioning
          const button = event.target.closest('button')
          if (button) {
            const rect = button.getBoundingClientRect()
            this.dropdownPosition = {
              top: rect.bottom + window.scrollY + 4,
              right: window.innerWidth - rect.right - window.scrollX
            }
          }
        }
      },

      getDropdownPosition() {
        if (this.dropdownPosition) {
          // Check if dropdown would go off screen
          const dropdownWidth = 192 // w-48 = 12rem = 192px
          const dropdownHeight = 200 // estimated height for dropdown

          let { top, right } = this.dropdownPosition

          // Adjust horizontal position if too close to left edge
          if (right + dropdownWidth > window.innerWidth) {
            right = Math.max(4, window.innerWidth - dropdownWidth - 4)
          }

          // Adjust vertical position if too close to bottom
          if (top + dropdownHeight > window.innerHeight + window.scrollY) {
            top = Math.max(4, top - dropdownHeight - 32) // 32px for button height
          }

          return {
            top: `${top}px`,
            right: `${right}px`,
            left: 'auto'
          }
        }
        // Fallback positioning
        return {
          top: '100%',
          right: '0',
          left: 'auto',
          position: 'absolute'
        }
      },

      handleClickOutside(event) {
        // Close dropdown if clicking outside the dropdown or three-dot button
        if (
          this.activeDropdown &&
          !event.target.closest('[style*="z-index: 9999"]') &&
          !event.target.closest('button[title*="Actions for"]')
        ) {
          this.activeDropdown = null
          this.dropdownPosition = null
        }
      },

      handleMenuAction(action, request) {
        // Close dropdown first
        this.activeDropdown = null
        this.dropdownPosition = null

        // Execute the appropriate action
        switch (action) {
          case 'viewRequest':
            this.viewRequest(request)
            break
          case 'viewTimeline':
            this.viewTimeline(request)
            break
          case 'assignTask':
            this.assignTask(request)
            break
          case 'updateProgress':
            this.updateProgress(request)
            break
          case 'cancelTask':
            this.cancelTask(request)
            break
          default:
            console.warn('Unknown action:', action)
        }
      },

      formatDate(dateString) {
        if (!dateString) return 'N/A'
        try {
          return new Date(dateString).toLocaleDateString('en-GB')
        } catch {
          return 'Invalid Date'
        }
      },

      formatTime(dateString) {
        if (!dateString) return 'N/A'
        try {
          return new Date(dateString).toLocaleTimeString('en-GB', {
            hour: '2-digit',
            minute: '2-digit'
          })
        } catch {
          return 'Invalid Time'
        }
      },
      
      // Refresh notification badge in sidebar
      refreshNotificationBadge() {
        try {
          console.log('🔔 AccessRequests: Triggering notification badge refresh')
          
          // Method 1: Trigger global refresh event
          if (window.dispatchEvent) {
            const event = new CustomEvent('refresh-notifications', {
              detail: { source: 'AccessRequests', reason: 'request_updated' }
            })
            window.dispatchEvent(event)
          }
          
          // Method 2: Direct call to sidebar instance
          if (window.sidebarInstance && window.sidebarInstance.fetchNotificationCounts) {
            window.sidebarInstance.fetchNotificationCounts(true) // force refresh
          }
        } catch (error) {
          console.warn('Failed to refresh notification badge:', error)
        }
      }
    }
  }
</script>

<style scoped>
  .sidebar-narrow {
    flex-shrink: 0;
  }
</style>
