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
              @click="fetchRequests"
              class="mt-2 bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 text-base font-medium"
            >
              Retry
            </button>
          </div>

          <!-- Stats -->
          <div class="grid grid-cols-4 gap-4 mb-6">
            <div class="bg-yellow-600/25 border border-yellow-400/40 p-4 rounded-lg">
              <h3 class="text-yellow-200 text-lg font-semibold">Pending My Approval</h3>
              <p class="text-white text-3xl font-bold">{{ stats.pendingHeadOfIt }}</p>
            </div>
            <div class="bg-green-600/25 border border-green-400/40 p-4 rounded-lg">
              <h3 class="text-green-200 text-lg font-semibold">Approved by Me</h3>
              <p class="text-white text-3xl font-bold">{{ stats.headOfItApproved }}</p>
            </div>
            <div class="bg-red-600/25 border border-red-400/40 p-4 rounded-lg">
              <h3 class="text-red-200 text-lg font-semibold">Rejected by Me</h3>
              <p class="text-white text-3xl font-bold">{{ stats.headOfItRejected }}</p>
            </div>
            <div class="bg-blue-600/25 border border-blue-400/40 p-4 rounded-lg">
              <h3 class="text-blue-200 text-lg font-semibold">Total Requests</h3>
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
                <option value="">All My Requests</option>
                <option value="pending">Show All Pending</option>
                <option value="ict_director_approved">Pending My Approval</option>
                <option value="head_of_it_approved">Approved by Me</option>
                <option value="head_of_it_rejected">Rejected by Me</option>
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
                    Personal Information
                  </th>
                  <th class="px-4 py-3 text-left text-blue-100 text-base font-semibold">
                    ICT Director Approval Date
                  </th>
                  <th class="px-4 py-3 text-left text-blue-100 text-base font-semibold">
                    Current Status
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
                  :data-request-id="request.id"
                  :class="[
                    'border-t border-blue-300/20 hover:bg-blue-700/30 transition-colors duration-200',
                    isPendingStatus(request.status)
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
                          v-if="isPendingStatus(request.status)"
                          class="fas fa-clock text-yellow-400 text-base"
                          title="Pending your approval"
                        ></i>
                        <i
                          v-else
                          class="fas fa-check-circle text-green-400 text-base"
                          title="Processed by you"
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

                  <!-- Personal Information -->
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
                    <div class="text-blue-200 text-sm">
                      Dept: {{ request.department || 'Unknown' }}
                    </div>
                  </td>

                  <!-- ICT Director Approval Date -->
                  <td class="px-4 py-3">
                    <div class="text-white font-medium text-base">
                      {{ formatDate(request.ict_director_approved_at) }}
                    </div>
                    <div class="text-blue-300 text-sm">
                      {{ formatTime(request.ict_director_approved_at) }}
                    </div>
                  </td>

                  <!-- Current Status -->
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
                  <td class="px-4 py-3 text-center relative">
                    <div class="flex justify-center three-dot-menu">
                      <!-- Three-dot menu button -->
                      <button
                        @click="toggleDropdown(request.id)"
                        class="three-dot-button p-2 text-white hover:bg-blue-600/40 rounded-full transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-400 hover:scale-105 active:scale-95"
                        :class="{ 'bg-blue-600/40 shadow-lg': openDropdownId === request.id }"
                        :aria-label="
                          'Actions for request ' +
                          (request.request_id || 'REQ-' + request.id.toString().padStart(6, '0'))
                        "
                      >
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                          <path
                            d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"
                          />
                        </svg>
                      </button>

                      <!-- Dropdown menu -->
                      <div
                        v-show="openDropdownId === request.id"
                        class="dropdown-menu fixed w-48 bg-white rounded-lg shadow-xl border border-gray-200 z-[10001] py-1 min-w-max"
                        :style="getDropdownPositionStyle(request.id)"
                        @click.stop
                      >
                        <!-- Role-specific actions -->
                        <template
                          v-for="(action, index) in getAvailableActions(request)"
                          :key="action.key"
                        >
                          <button
                            @click.stop="executeAction(action.key, request)"
                            :class="[
                              'w-full text-left px-4 py-2 text-sm transition-all duration-200 flex items-center space-x-3 group focus:outline-none focus:ring-2 focus:ring-inset',
                              getActionButtonClass(
                                action.key,
                                index,
                                getAvailableActions(request).length
                              )
                            ]"
                          >
                            <i
                              :class="[action.icon, getActionIconClass(action.key)]"
                              class="w-4 h-4 flex-shrink-0 transition-colors duration-200"
                            ></i>
                            <span class="font-medium">{{ action.label }}</span>
                          </button>
                        </template>
                      </div>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>

            <!-- Empty State -->
            <div v-if="filteredRequests.length === 0" class="text-center py-12">
              <h3 class="text-white text-xl font-medium mb-2">No requests found</h3>
              <p class="text-blue-300 text-base">
                {{
                  searchQuery || statusFilter
                    ? 'No requests match your current filters.'
                    : 'No requests available for Head of IT review. Requests appear here when approved by ICT Directors.'
                }}
              </p>
            </div>

            <!-- Pagination -->
            <div v-if="filteredRequests.length > 0" class="px-4 py-3 border-t border-blue-300/30">
              <div class="text-blue-300 text-base">
                Showing {{ filteredRequests.length }} of {{ requests.length }} requests
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
      <div
        class="rounded-xl shadow-2xl p-8 text-center border border-blue-400/40"
        style="background: linear-gradient(90deg, #0b3a82, #0a2f6f, #0b3a82)"
      >
        <div class="flex justify-center mb-4">
          <div
            class="w-12 h-12 border-4 border-white border-t-transparent rounded-full animate-spin"
          ></div>
        </div>
        <p class="text-blue-100 font-medium">Loading requests...</p>
      </div>
    </div>

    <!-- Timeline Modal -->
    <RequestTimeline
      :show="showTimeline"
      :request-id="selectedRequestId"
      @close="closeTimeline"
      @updated="handleTimelineUpdate"
    />
  </div>
</template>

<script>
  import Header from '@/components/header.vue'
  import ModernSidebar from '@/components/ModernSidebar.vue'
  import AppFooter from '@/components/footer.vue'
  import RequestTimeline from '@/components/common/RequestTimeline.vue'
  import headOfItService from '@/services/headOfItService'
  import statusUtils from '@/utils/statusUtils'
  import { useAuth } from '@/composables/useAuth'

  export default {
    name: 'HeadOfItRequestList',
    components: {
      Header,
      ModernSidebar,
      AppFooter,
      RequestTimeline
    },
    setup() {
      const { userRole } = useAuth()
      return { userRole }
    },
    data() {
      return {
        requests: [],
        searchQuery: '',
        statusFilter: '',
        isLoading: false,
        stats: {
          pendingHeadOfIt: 0,
          headOfItApproved: 0,
          headOfItRejected: 0,
          total: 0
        },
        error: null,
        // Dropdown state management
        openDropdownId: null,
        // Timeline modal state
        showTimeline: false,
        selectedRequestId: null,
        $statusUtils: statusUtils,
        // Session-based tracking for assigned tasks (with sessionStorage persistence)
        assignedRequestsThisSession: new Set()
      }
    },
    computed: {
      filteredRequests() {
        if (!Array.isArray(this.requests)) {
          console.warn('HeadOfItRequestList: requests is not an array:', this.requests)
          return []
        }

        let filtered = this.requests

        // Filter by search query
        if (this.searchQuery) {
          const query = this.searchQuery.toLowerCase()
          filtered = filtered.filter(
            (request) =>
              (request.staff_name || request.full_name || '').toLowerCase().includes(query) ||
              (request.pf_number || '').toLowerCase().includes(query) ||
              (request.department || '').toLowerCase().includes(query) ||
              (request.request_id || '').toLowerCase().includes(query)
          )
        }

        // Filter by status
        if (this.statusFilter) {
          if (this.statusFilter === 'pending') {
            filtered = filtered.filter((request) => this.isPendingStatus(request.status))
          } else {
            filtered = filtered.filter((request) => request.status === this.statusFilter)
          }
        }

        // FIFO Sorting: Pending requests first, then approved requests
        return filtered.sort((a, b) => {
          const aPending = this.isPendingStatus(a.status)
          const bPending = this.isPendingStatus(b.status)

          if (aPending && !bPending) return -1
          if (!aPending && bPending) return 1

          const getRelevantDate = (request) => {
            if (this.isPendingStatus(request.status)) {
              return new Date(request.ict_director_approved_at || request.created_at || 0)
            }
            return new Date(request.head_of_it_approved_at || request.updated_at || 0)
          }

          const dateA = getRelevantDate(a)
          const dateB = getRelevantDate(b)

          return dateA - dateB
        })
      }
    },
    async mounted() {
      try {
        console.log('HeadOfItRequestList: Component mounted, initializing...')

        // Initialize session-based tracking from sessionStorage
        this.initializeSessionTracking()

        await this.fetchRequests()

        // Add click listener to close dropdowns when clicking outside
        document.addEventListener('click', this.closeAllDropdowns)

        console.log('HeadOfItRequestList: Component initialized successfully')
      } catch (error) {
        console.error('HeadOfItRequestList: Error during mount:', error)
        this.error = 'Failed to initialize component: ' + error.message
        this.isLoading = false
      }
    },

    beforeUnmount() {
      // Clean up event listeners
      document.removeEventListener('click', this.closeAllDropdowns)
    },
    methods: {
      // Initialize session tracking from sessionStorage
      initializeSessionTracking() {
        try {
          const storedAssignedRequests = sessionStorage.getItem('headOfIt_assignedRequests')
          if (storedAssignedRequests) {
            const assignedIds = JSON.parse(storedAssignedRequests)
            this.assignedRequestsThisSession = new Set(assignedIds)
            console.log(
              '📦 Restored assigned requests from session:',
              Array.from(this.assignedRequestsThisSession)
            )
          }
        } catch (error) {
          console.error('⚠️ Failed to restore assigned requests from session:', error)
          this.assignedRequestsThisSession = new Set()
        }
      },

      // Persist session tracking to sessionStorage
      persistSessionTracking() {
        try {
          const assignedIds = Array.from(this.assignedRequestsThisSession)
          sessionStorage.setItem('headOfIt_assignedRequests', JSON.stringify(assignedIds))
          console.log('💾 Persisted assigned requests to session:', assignedIds)
        } catch (error) {
          console.error('⚠️ Failed to persist assigned requests to session:', error)
        }
      },
      async fetchRequests() {
        console.log('🔄 HeadOfItRequestList: Starting to fetch requests...')
        this.isLoading = true
        this.error = null

        try {
          const result = await headOfItService.getPendingRequests()

          if (result.success) {
            console.log('✅ HeadOfItRequestList: Requests loaded successfully:', result.data.length)

            this.requests = result.data.requests || []

            // Calculate stats
            this.stats = {
              pendingHeadOfIt: this.requests.filter((r) => r.status === 'ict_director_approved')
                .length,
              headOfItApproved: this.requests.filter((r) => r.status === 'head_of_it_approved')
                .length,
              headOfItRejected: this.requests.filter((r) => r.status === 'head_of_it_rejected')
                .length,
              total: this.requests.length
            }
          } else {
            console.error('❌ HeadOfItRequestList: Failed to load requests:', result.message)
            this.error = result.message
            this.requests = []
          }
        } catch (error) {
          console.error('❌ HeadOfItRequestList: Error loading requests:', error)
          this.error = 'Network error while loading requests. Please check your connection.'
          this.requests = []
        } finally {
          this.isLoading = false
        }
      },

      async refreshRequests() {
        console.log('🔄 HeadOfItRequestList: Refreshing requests...')
        await this.fetchRequests()
      },

      viewAndProcessRequest(requestId) {
        console.log('👁️ HeadOfItRequestList: Viewing request for processing:', requestId)
        this.$router.push(`/head_of_it-dashboard/both-service-form/${requestId}`)
      },

      isPendingStatus(status) {
        const pendingStatuses = ['ict_director_approved']
        return pendingStatuses.includes(status)
      },

      hasService(request, serviceType) {
        if (!request.request_types) return false

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

      getStatusBadgeClass(status) {
        const statusClasses = {
          ict_director_approved: 'bg-yellow-500 text-yellow-900',
          head_of_it_approved: 'bg-green-500 text-green-900',
          head_of_it_rejected: 'bg-red-500 text-red-900',
          assigned_to_ict: 'bg-blue-500 text-blue-900',
          implementation_in_progress: 'bg-purple-500 text-purple-900',
          completed: 'bg-emerald-500 text-emerald-900'
        }
        return statusClasses[status] || 'bg-gray-500 text-gray-900'
      },

      getStatusText(status) {
        const statusTexts = {
          ict_director_approved: 'Pending Your Approval',
          head_of_it_approved: 'Approved by You',
          head_of_it_rejected: 'Rejected by You',
          assigned_to_ict: 'Assigned to ICT Officer',
          implementation_in_progress: 'Implementation in Progress',
          completed: 'Implementation Completed'
        }
        return (
          statusTexts[status] || status.replace(/_/g, ' ').replace(/\b\w/g, (l) => l.toUpperCase())
        )
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

      // Dropdown management methods
      toggleDropdown(requestId) {
        console.log(
          'HeadOfItRequestList: Toggling dropdown for request:',
          requestId,
          'Current open:',
          this.openDropdownId
        )
        if (this.openDropdownId === requestId) {
          this.openDropdownId = null
        } else {
          this.openDropdownId = requestId
        }
      },

      closeAllDropdowns(event) {
        // Only close if clicking outside the dropdown menu or three-dot button
        if (
          event &&
          (event.target.closest('.dropdown-menu') || event.target.closest('.three-dot-button'))
        ) {
          return
        }
        console.log('HeadOfItRequestList: Closing all dropdowns')
        this.openDropdownId = null
      },

      // Helper method to check if task has been assigned to an ICT officer
      isTaskAssigned(request) {
        console.log('🔍 Checking task assignment for request:', request.id, {
          status: request.status,
          assigned_ict_officer_id: request.assigned_ict_officer_id,
          ict_officer_id: request.ict_officer_id,
          assigned_to_ict_officer: request.assigned_to_ict_officer,
          ict_officer: request.ict_officer,
          ict_officer_name: request.ict_officer_name,
          assigned_officer: request.assigned_officer,
          task_assigned_at: request.task_assigned_at,
          fullRequest: request // Log the full request to see available fields
        })

        // Primary check: Status indicates assignment
        const statusBasedAssignment = [
          'assigned_to_ict',
          'implementation_in_progress',
          'implemented',
          'completed'
        ].includes(request.status)

        // Secondary checks: Explicit officer assignment fields
        const fieldBasedAssignment = !!(
          request.assigned_ict_officer_id ||
          request.ict_officer_id ||
          request.assigned_to_ict_officer ||
          request.ict_officer ||
          request.ict_officer_name ||
          request.assigned_officer ||
          request.task_assigned_at ||
          request.assignee ||
          request.assignee_id ||
          request.task_assignment ||
          request.task_assignment_id
        )

        // Tertiary check: Look for any field containing 'officer' or 'assign'
        const dynamicFieldCheck = Object.keys(request).some((key) => {
          const lowerKey = key.toLowerCase()
          return (
            (lowerKey.includes('officer') || lowerKey.includes('assign')) &&
            request[key] &&
            request[key] !== null &&
            request[key] !== ''
          )
        })

        const isAssigned = statusBasedAssignment || fieldBasedAssignment || dynamicFieldCheck

        console.log('📋 Task Assignment Result:', {
          requestId: request.id,
          statusBasedAssignment,
          fieldBasedAssignment,
          dynamicFieldCheck,
          finalResult: isAssigned,
          allRequestKeys: Object.keys(request)
        })

        return isAssigned
      },

      // Helper method to check if request is in read-only mode (already processed)
      isReadOnlyMode(request) {
        return !this.isPendingStatus(request.status)
      },

      // Role-specific actions logic for Head of IT
      getAvailableActions(request) {
        const actions = []
        const userRole = this.userRole
        const status = request.status
        const taskAssigned = this.isTaskAssigned(request)
        const isReadOnly = this.isReadOnlyMode(request)

        console.log('🔍 Dynamic Actions Debug:', {
          requestId: request.id,
          status,
          taskAssigned,
          isReadOnly,
          isPending: this.isPendingStatus(status)
        })

        // Define role mappings
        const ROLES = {
          HEAD_OF_IT: 'head_of_it',
          ICT_OFFICER: 'ict_officer',
          ICT_DIRECTOR: 'ict_director',
          DIVISIONAL_DIRECTOR: 'divisional_director',
          HEAD_OF_DEPARTMENT: 'hod',
          STAFF: 'staff'
        }

        // Head of IT specific actions (this component is for Head of IT dashboard)
        if (userRole === ROLES.HEAD_OF_IT || !userRole) {
          // Default to Head of IT if no role detected

          // Dynamic View & Process / View Task button
          if (this.isPendingStatus(status)) {
            // For pending requests - show "View & Process" (editable mode)
            actions.push({
              key: 'view_and_process',
              label: 'View & Process',
              icon: 'fas fa-eye'
            })
          } else {
            // For processed requests - show "View Task" (read-only mode)
            actions.push({
              key: 'view_and_process',
              label: 'View Task',
              icon: 'fas fa-eye'
            })
          }

          // Dynamic Assign Task button - ONLY show for requests that:
          // 1. Are approved by Head of IT (status = 'head_of_it_approved')
          // 2. Have NOT progressed beyond approval (no assignment yet)
          // 3. Are NOT in any assigned/progress/completed state
          // EMERGENCY FIX: Multiple layers of checks to ensure button is hidden for assigned tasks

          // Layer 1: Status-based check
          const isExactlyApproved = status === 'head_of_it_approved'
          const hasNotProgressed = ![
            'assigned_to_ict',
            'implementation_in_progress',
            'implemented',
            'completed'
          ].includes(status)

          // Layer 2: Field-based assignment detection
          const hasAssignmentIndicators = taskAssigned

          // Layer 3: Emergency override - if request has any officer/assignment fields, hide button
          const emergencyCheck = !Object.keys(request).some((key) => {
            const lowerKey = key.toLowerCase()
            const value = request[key]
            return (
              (lowerKey.includes('officer') || lowerKey.includes('assign')) &&
              value &&
              value !== null &&
              value !== '' &&
              value !== 0
            )
          })

          // Layer 4: Session tracking - hide button if already assigned in current session
          const notAssignedThisSession = !this.assignedRequestsThisSession.has(request.id)

          // ONLY show assign button if ALL conditions are met:
          // 1. Status is exactly 'head_of_it_approved'
          // 2. Status has not progressed beyond approval
          // 3. No field-based assignment indicators
          // 4. Emergency check passes (no officer/assignment fields found)
          // 5. Not assigned in current session
          const canAssignTask =
            isExactlyApproved &&
            hasNotProgressed &&
            !hasAssignmentIndicators &&
            emergencyCheck &&
            notAssignedThisSession

          console.log('🎯 Assign Task Button Logic (ULTRA EMERGENCY FIX):', {
            requestId: request.id,
            status,
            // Layer 1: Status checks
            isExactlyApproved,
            hasNotProgressed,
            // Layer 2: Field-based detection
            hasAssignmentIndicators,
            // Layer 3: Emergency override
            emergencyCheck,
            // Layer 4: Session tracking
            notAssignedThisSession,
            sessionTrackedIds: Array.from(this.assignedRequestsThisSession),
            // Final result
            canAssignTask,
            decision: canAssignTask
              ? '✅ SHOWING Assign Task button'
              : '❌ HIDING Assign Task button',
            failureReason: !canAssignTask
              ? !isExactlyApproved
                ? `Status '${status}' is not exactly 'head_of_it_approved'`
                : !hasNotProgressed
                  ? `Status '${status}' indicates task has progressed beyond approval`
                  : hasAssignmentIndicators
                    ? 'Field-based assignment indicators detected'
                    : !emergencyCheck
                      ? 'Emergency check failed - officer/assignment fields found'
                      : !notAssignedThisSession
                        ? 'Already assigned in current session'
                        : 'Unknown reason'
              : 'All checks passed - showing button'
          })

          if (canAssignTask) {
            actions.push({
              key: 'assign_task',
              label: 'Assign Task',
              icon: 'fas fa-user-plus'
            })
          }

          // Dynamic Cancel Task button - Apply same comprehensive checks as Assign Task button
          // ONLY show cancel button if:
          // 1. Status is exactly 'head_of_it_approved' (same as assign button)
          // 2. Task has NOT been assigned/progressed (same multi-layer checks)
          // 3. Not in completed/implemented state

          // Use the same comprehensive logic as assign task button
          const canCancelTask =
            isExactlyApproved &&
            hasNotProgressed &&
            !hasAssignmentIndicators &&
            emergencyCheck &&
            notAssignedThisSession &&
            !['completed', 'implemented'].includes(status)

          console.log('🎯 Cancel Task Button Logic (ULTRA EMERGENCY FIX):', {
            requestId: request.id,
            status,
            // Layer 1: Status checks
            isExactlyApproved,
            hasNotProgressed,
            // Layer 2: Field-based detection
            hasAssignmentIndicators,
            // Layer 3: Emergency override
            emergencyCheck,
            // Layer 4: Session tracking
            notAssignedThisSession,
            // Layer 5: Not completed/implemented
            notCompletedOrImplemented: !['completed', 'implemented'].includes(status),
            sessionTrackedIds: Array.from(this.assignedRequestsThisSession),
            // Final result
            canCancelTask,
            decision: canCancelTask
              ? '✅ SHOWING Cancel Task button'
              : '❌ HIDING Cancel Task button',
            failureReason: !canCancelTask
              ? !isExactlyApproved
                ? `Status '${status}' is not exactly 'head_of_it_approved'`
                : !hasNotProgressed
                  ? `Status '${status}' indicates task has progressed beyond approval`
                  : hasAssignmentIndicators
                    ? 'Field-based assignment indicators detected'
                    : !emergencyCheck
                      ? 'Emergency check failed - officer/assignment fields found'
                      : !notAssignedThisSession
                        ? 'Already assigned in current session'
                        : ['completed', 'implemented'].includes(status)
                          ? 'Request is completed or implemented'
                          : 'Unknown reason'
              : 'All checks passed - showing button'
          })

          if (canCancelTask) {
            actions.push({
              key: 'cancel_task',
              label: 'Cancel Task',
              icon: 'fas fa-times-circle'
            })
          }

          // Dynamic View Progress button - only show if:
          // 1. Task has been assigned to ICT officer
          // 2. There's actual progress to view
          if (
            taskAssigned &&
            ['assigned_to_ict', 'implementation_in_progress', 'implemented', 'completed'].includes(
              status
            )
          ) {
            actions.push({
              key: 'view_progress',
              label: 'View Progress',
              icon: 'fas fa-chart-line'
            })
          }

          // Always show view timeline (universal action)
          actions.push({
            key: 'view_timeline',
            label: 'View Timeline',
            icon: 'fas fa-history'
          })
        }
        // ICT Officer actions (if somehow an ICT Officer accesses this page)
        else if (userRole === ROLES.ICT_OFFICER) {
          actions.push({
            key: 'view_and_process',
            label: 'View Details',
            icon: 'fas fa-eye'
          })

          // Only show for requests assigned to this officer
          if (['assigned_to_ict', 'implementation_in_progress'].includes(status)) {
            actions.push({
              key: 'update_progress',
              label: 'Update Progress',
              icon: 'fas fa-edit'
            })
          }

          actions.push({
            key: 'view_timeline',
            label: 'View Timeline',
            icon: 'fas fa-history'
          })
        }
        // Other roles (read-only access)
        else {
          actions.push({
            key: 'view_and_process',
            label: 'View Details',
            icon: 'fas fa-eye'
          })
          actions.push({
            key: 'view_timeline',
            label: 'View Timeline',
            icon: 'fas fa-history'
          })
        }

        return actions
      },

      // Execute action based on key
      executeAction(actionKey, request) {
        console.log('HeadOfItRequestList: Executing action:', actionKey, 'for request:', request.id)
        this.closeAllDropdowns()

        switch (actionKey) {
          case 'assign_task':
            this.assignTask(request)
            break
          case 'cancel_task':
            this.cancelTask(request)
            break
          case 'update_progress':
            this.updateProgress(request)
            break
          case 'view_progress':
            this.viewProgress(request)
            break
          case 'view_timeline':
            this.viewTimeline(request)
            break
          case 'view_and_process':
            this.viewAndProcessRequest(request.id)
            break
          default:
            console.warn('Unknown action:', actionKey)
        }
      },

      // Head of IT specific action methods
      assignTask(request) {
        console.log('🚀 Head of IT: Assigning task for request:', request.id)

        // Add to session tracking to prevent showing button again
        this.assignedRequestsThisSession.add(request.id)
        console.log('📝 Added request', request.id, 'to assigned requests session tracking')

        // Persist to sessionStorage immediately
        this.persistSessionTracking()

        // Navigate to ICT Officer selection page
        this.$router.push(`/head_of_it-dashboard/select-ict-officer/${request.id}`)
      },

      async cancelTask(request) {
        console.log('Head of IT: Cancelling task for request:', request.id)

        // Show confirmation dialog for task cancellation
        const confirmed = confirm(
          `Are you sure you want to cancel the task for Request ${request.request_id || 'REQ-' + request.id.toString().padStart(6, '0')}? This will stop the implementation process.`
        )

        if (confirmed) {
          const reason = prompt('Please provide a reason for cancelling the task:')
          if (reason && reason.trim() !== '') {
            try {
              console.log('🔄 Canceling task assignment via API...')

              // Call the actual headOfItService API to cancel task
              const result = await headOfItService.cancelTaskAssignment(request.id, {
                reason: reason.trim(),
                cancelled_by: 'head_of_it',
                cancelled_at: new Date().toISOString()
              })

              if (result.success) {
                // Show success message
                alert(
                  `✅ Task cancelled successfully for Request ${request.request_id || 'REQ-' + request.id.toString().padStart(6, '0')}.\nReason: ${reason}`
                )

                // Refresh the requests list to show updated status
                await this.fetchRequests()
                console.log('✅ Task cancellation completed and list refreshed')
              } else {
                // Show error message from API
                alert(`❌ Failed to cancel task: ${result.message}`)
                console.error('❌ Task cancellation failed:', result.message)
              }
            } catch (error) {
              console.error('❌ Error during task cancellation:', error)
              alert(`❌ Error cancelling task: ${error.message || 'Network error occurred'}`)
            }
          } else {
            alert('Cancellation reason is required')
          }
        }
      },

      updateProgress(request) {
        console.log('Head of IT: Update progress for request:', request.id)
        // This would typically open a modal or navigate to an update form
        alert(
          'Update Progress functionality would be implemented here. This might open a modal or dedicated page.'
        )
      },

      viewTimeline(request) {
        console.log('📅 HeadOfItRequestList: Opening timeline for request:', request.id)
        this.selectedRequestId = request.id
        this.showTimeline = true
      },

      closeTimeline() {
        console.log('📅 HeadOfItRequestList: Closing timeline modal')
        this.showTimeline = false
        this.selectedRequestId = null
      },

      async handleTimelineUpdate() {
        console.log('🔄 HeadOfItRequestList: Timeline updated, refreshing requests list...')
        await this.fetchRequests()
      },

      viewProgress(request) {
        console.log('👁️ HeadOfItRequestList: Viewing progress for request:', request.id)
        // Navigate to progress view - using ICT dashboard route for consistency
        this.$router.push(`/ict-dashboard/request-progress/${request.id}`)
      },

      // Helper method to determine if separator should be shown
      shouldShowSeparator(currentAction, previousAction) {
        // Add separator before destructive actions or different action groups
        const destructiveActions = ['cancel_task']
        const viewActions = ['view_timeline', 'view_and_process']

        // Separate destructive actions
        if (
          destructiveActions.includes(currentAction.key) &&
          !destructiveActions.includes(previousAction?.key)
        ) {
          return true
        }

        // Separate view actions from edit actions
        if (
          viewActions.includes(currentAction.key) &&
          !viewActions.includes(previousAction?.key) &&
          previousAction?.key !== 'view_and_process'
        ) {
          return true
        }

        return false
      },

      // Dynamic dropdown positioning to prevent cutoff issues
      getDropdownPositionStyle(requestId) {
        // Use requestId to find the button element and calculate position
        const buttonElement = document.querySelector(
          `[data-request-id="${requestId}"] .three-dot-button`
        )

        if (!buttonElement) {
          // Fallback to basic positioning
          return {
            position: 'fixed',
            top: 'auto',
            left: 'auto',
            right: '1rem',
            boxShadow: '0 10px 25px rgba(0, 0, 0, 0.15), 0 4px 6px rgba(0, 0, 0, 0.1)'
          }
        }

        const buttonRect = buttonElement.getBoundingClientRect()
        const viewportHeight = window.innerHeight
        const viewportWidth = window.innerWidth
        const dropdownWidth = 192 // 12rem = 192px
        const dropdownHeight = 200 // Approximate dropdown height

        let top = buttonRect.bottom + 8 // 8px margin from button
        let left = buttonRect.right - dropdownWidth

        // Adjust if dropdown would go off-screen vertically
        if (top + dropdownHeight > viewportHeight) {
          top = buttonRect.top - dropdownHeight - 8
        }

        // Adjust if dropdown would go off-screen horizontally
        if (left < 16) {
          left = buttonRect.left
        }

        // Ensure dropdown doesn't go off right edge
        if (left + dropdownWidth > viewportWidth - 16) {
          left = viewportWidth - dropdownWidth - 16
        }

        return {
          position: 'fixed',
          top: `${top}px`,
          left: `${left}px`,
          boxShadow: '0 10px 25px rgba(0, 0, 0, 0.15), 0 4px 6px rgba(0, 0, 0, 0.1)'
        }
      },

      // Get distinct styling classes for each action type
      getActionButtonClass(actionKey, index, totalActions) {
        const baseClasses = {
          view_and_process:
            'bg-blue-50 text-blue-800 border-b border-blue-200 hover:bg-blue-100 focus:bg-blue-100 focus:ring-blue-500',
          view_timeline:
            'bg-indigo-50 text-indigo-800 border-b border-indigo-200 hover:bg-indigo-100 focus:bg-indigo-100 focus:ring-indigo-500',
          assign_task:
            'bg-green-50 text-green-800 border-b border-green-200 hover:bg-green-100 focus:bg-green-100 focus:ring-green-500',
          view_progress:
            'bg-orange-50 text-orange-800 border-b border-orange-200 hover:bg-orange-100 focus:bg-orange-100 focus:ring-orange-500',
          update_progress:
            'bg-purple-50 text-purple-800 border-b border-purple-200 hover:bg-purple-100 focus:bg-purple-100 focus:ring-purple-500',
          cancel_task: 'bg-red-50 text-red-800 hover:bg-red-100 focus:bg-red-100 focus:ring-red-500'
        }

        let classes =
          baseClasses[actionKey] ||
          'bg-gray-50 text-gray-800 hover:bg-gray-100 focus:bg-gray-100 focus:ring-gray-500'

        // Add rounded corners for first and last items
        if (index === 0) {
          classes += ' first:rounded-t-lg'
        }
        if (index === totalActions - 1) {
          classes += ' last:rounded-b-lg'
        }

        return classes
      },

      // Get distinct icon colors for each action type
      getActionIconClass(actionKey) {
        const iconClasses = {
          view_and_process: 'text-blue-600 group-hover:text-blue-700 group-focus:text-blue-700',
          view_timeline: 'text-indigo-600 group-hover:text-indigo-700 group-focus:text-indigo-700',
          assign_task: 'text-green-600 group-hover:text-green-700 group-focus:text-green-700',
          view_progress: 'text-orange-600 group-hover:text-orange-700 group-focus:text-orange-700',
          update_progress:
            'text-purple-600 group-hover:text-purple-700 group-focus:text-purple-700',
          cancel_task: 'text-red-600 group-hover:text-red-700 group-focus:text-red-700'
        }

        return (
          iconClasses[actionKey] ||
          'text-gray-600 group-hover:text-gray-700 group-focus:text-gray-700'
        )
      }
    }
  }
</script>

<style scoped>
  .sidebar-narrow {
    flex-shrink: 0;
  }

  /* Three-dot menu enhancements */
  .three-dot-menu {
    position: relative;
  }

  /* Ensure dropdown is always visible and properly positioned */
  .dropdown-menu {
    /* Position and z-index are now set dynamically via style binding */
    min-width: 12rem;
    max-width: 16rem;
  }

  /* Mobile responsive adjustments */
  @media (max-width: 768px) {
    .dropdown-menu {
      min-width: 10rem;
      max-width: 14rem;
      right: 1rem !important;
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
</style>
