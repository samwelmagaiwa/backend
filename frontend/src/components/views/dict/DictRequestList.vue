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
              <h3 class="text-yellow-200 text-lg font-bold">Pending My Approval</h3>
              <p class="text-white text-4xl font-bold">{{ stats.pendingDict }}</p>
            </div>
            <div class="bg-green-600/25 border border-green-400/40 p-4 rounded-lg">
              <h3 class="text-green-200 text-lg font-bold">Approved by Me</h3>
              <p class="text-white text-4xl font-bold">{{ stats.dictApproved }}</p>
            </div>
            <div class="bg-red-600/25 border border-red-400/40 p-4 rounded-lg">
              <h3 class="text-red-200 text-lg font-bold">Rejected by Me</h3>
              <p class="text-white text-4xl font-bold">{{ stats.dictRejected }}</p>
            </div>
            <div class="bg-blue-600/25 border border-blue-400/40 p-4 rounded-lg">
              <h3 class="text-blue-200 text-lg font-bold">Total Requests</h3>
              <p class="text-white text-4xl font-bold">{{ stats.total }}</p>
            </div>
          </div>

          <!-- Filters -->
          <div class="bg-white/10 rounded-lg p-4 mb-6">
            <div class="flex gap-4">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Search by staff name, PF number, or department..."
                class="flex-1 px-4 py-3 bg-white/20 border border-blue-300/30 rounded text-white placeholder-blue-200/60 text-lg"
              />
              <select
                v-model="statusFilter"
                class="px-4 py-3 bg-white/20 border border-blue-300/30 rounded text-white text-lg"
              >
                <option value="">All My Requests</option>
                <option value="pending">Show All Pending</option>
                <option value="divisional_approved">Pending My Approval</option>
                <option value="ict_director_approved">Approved by Me</option>
                <option value="dict_approved">Dict Approved</option>
                <option value="dict_rejected">Rejected by Me</option>
                <option value="ict_director_rejected">ICT Rejected</option>
                <option value="approved">Approved</option>
                <option value="implemented">Implemented</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
              </select>
              <button
                @click="refreshRequests"
                class="px-6 py-3 bg-teal-600 text-white rounded hover:bg-teal-700 text-lg font-bold"
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
                  <th class="px-4 py-4 text-left text-blue-100 text-lg font-bold">Request ID</th>
                  <th class="px-4 py-4 text-left text-blue-100 text-lg font-bold">Request Type</th>
                  <th class="px-4 py-4 text-left text-blue-100 text-lg font-bold">
                    Personal Information
                  </th>
                  <th class="px-4 py-4 text-left text-blue-100 text-lg font-bold">
                    Divisional Approval Date
                  </th>
                  <th class="px-4 py-4 text-left text-blue-100 text-lg font-bold">
                    Current Status
                  </th>
                  <th class="px-4 py-4 text-center text-blue-100 text-lg font-bold">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="request in filteredRequests"
                  :key="request.id"
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

                  <!-- Divisional Approval Date -->
                  <td class="px-4 py-3">
                    <div class="text-white font-medium text-base">
                      {{ formatDate(request.divisional_approved_at) }}
                    </div>
                    <div class="text-blue-300 text-sm">
                      {{ formatTime(request.divisional_approved_at) }}
                    </div>
                  </td>

                  <!-- Current Status -->
                  <td class="px-4 py-3">
                    <div class="flex flex-col">
                      <!-- Display the exact database status -->
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
                        @click.stop="toggleDropdown(request.id)"
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
                        class="dropdown-menu absolute right-0 top-full mt-1 w-48 bg-white rounded-lg shadow-xl border border-gray-200 z-[10000] py-1 min-w-max"
                        @click.stop="() => {}"
                        style="
                          box-shadow:
                            0 10px 25px rgba(0, 0, 0, 0.15),
                            0 4px 6px rgba(0, 0, 0, 0.1);
                        "
                      >
                        <!-- Role-specific actions -->
                        <template
                          v-for="(action, index) in getAvailableActions(request)"
                          :key="action.key"
                        >
                          <button
                            @click.stop="executeAction(action.key, request)"
                            class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 flex items-center space-x-3 group"
                            :class="{
                              'border-t border-gray-100':
                                index > 0 &&
                                shouldShowSeparator(action, getAvailableActions(request)[index - 1])
                            }"
                          >
                            <i
                              :class="action.icon"
                              class="text-gray-400 group-hover:text-gray-600 w-4 h-4 flex-shrink-0 transition-colors duration-200"
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
                    : 'No requests available for ICT Director review. Requests appear here when approved by Divisional Directors.'
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
  import dictAccessService from '@/services/dictAccessService'
  import statusUtils from '@/utils/statusUtils'
  import { useAuth } from '@/composables/useAuth'

  export default {
    name: 'DictRequestList',
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
          pendingDict: 0,
          dictApproved: 0,
          dictRejected: 0,
          total: 0
        },
        error: null,
        // Dropdown state management
        openDropdownId: null,
        // Timeline modal state
        showTimeline: false,
        selectedRequestId: null,
        // Add status utilities for consistent status handling
        $statusUtils: statusUtils
      }
    },
    watch: {
      // Watch for route changes to refresh data when returning from form
      $route(to, from) {
        console.log('DictRequestList: Route changed from', from?.path, 'to', to?.path)

        // If coming back to this route from the form, refresh data
        if (
          to.path === '/dict-dashboard/combined-requests' &&
          from?.path?.includes('/both-service-form/')
        ) {
          console.log('DictRequestList: Detected return from form - refreshing data')
          this.fetchRequests()
        }
      }
    },
    computed: {
      filteredRequests() {
        // Ensure requests is always an array
        if (!Array.isArray(this.requests)) {
          console.warn('DictRequestList: requests is not an array:', this.requests)
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

        // Filter by status - handle special 'pending' filter and exact database status
        if (this.statusFilter) {
          if (this.statusFilter === 'pending') {
            // Show all pending requests (waiting for ICT Director approval)
            filtered = filtered.filter((request) => this.isPendingStatus(request.status))
          } else {
            // Filter by exact status
            filtered = filtered.filter((request) => request.status === this.statusFilter)
          }
        }

        // FIFO Sorting: Pending requests first (by submission/approval date), then approved requests
        return filtered.sort((a, b) => {
          // Priority 1: Pending requests should come first
          const aPending = this.isPendingStatus(a.status)
          const bPending = this.isPendingStatus(b.status)

          if (aPending && !bPending) return -1 // a comes first
          if (!aPending && bPending) return 1 // b comes first

          // Priority 2: Within same category (pending/approved), use FIFO based on relevant date
          const getRelevantDate = (request) => {
            // For pending requests, use divisional approval date (when they entered the queue)
            if (this.isPendingStatus(request.status)) {
              return new Date(request.divisional_approved_at || request.created_at || 0)
            }
            // For approved/processed requests, use ICT director approval date
            return new Date(request.ict_director_approved_at || request.updated_at || 0)
          }

          const dateA = getRelevantDate(a)
          const dateB = getRelevantDate(b)

          // FIFO: older dates first (earliest submitted/approved first)
          return dateA - dateB
        })
      }
    },
    async mounted() {
      try {
        console.log('DictRequestList: Component mounted, initializing...')
        await this.fetchRequests()

        // Add window focus listener to refresh data when user returns from approval form
        window.addEventListener('focus', this.onWindowFocus)

        // Add click listener to close dropdowns when clicking outside
        document.addEventListener('click', this.closeAllDropdowns)

        console.log('DictRequestList: Component initialized successfully')
      } catch (error) {
        console.error('DictRequestList: Error during mount:', error)
        this.error = 'Failed to initialize component: ' + error.message
        this.isLoading = false
      }
    },

    // Add Vue 3 router navigation guard to refresh data when returning to this route
    async beforeRouteEnter(to, from, next) {
      // This runs before the component is created
      console.log('DictRequestList: Entering route from:', from?.path || 'initial')

      // If coming from form approval, we want to refresh data after component is mounted
      if (from?.path?.includes('/both-service-form/')) {
        next((vm) => {
          console.log('DictRequestList: Refreshing data after return from form')
          vm.fetchRequests()
        })
      } else {
        next()
      }
    },

    async beforeRouteUpdate(to, from, next) {
      // This runs when the route is updated but the same component is reused
      console.log('DictRequestList: Route updated from', from?.path, 'to', to?.path)

      if (to.meta?.refreshOnEnter || from?.path?.includes('/both-service-form/')) {
        await this.fetchRequests()
      }

      next()
    },

    async activated() {
      // This runs when the component is activated (keep-alive) or re-entered
      console.log('DictRequestList: Component activated - refreshing data')
      await this.fetchRequests()
    },

    beforeUnmount() {
      // Clean up event listeners
      window.removeEventListener('focus', this.onWindowFocus)
      document.removeEventListener('click', this.closeAllDropdowns)
    },
    methods: {
      async fetchRequests() {
        this.isLoading = true
        this.error = null

        try {
          console.log('Fetching combined access requests for ICT Director (pending + approved)...')

          const response = await dictAccessService.getDictRequests({
            search: this.searchQuery || undefined,
            status: this.statusFilter || undefined,
            per_page: 50,
            include_processed: true, // Include both pending and already processed by ICT Director
            show_all_ict_director_requests: true // Show all requests relevant to ICT Director
          })

          if (response.success) {
            // Normalize response to array regardless of pagination shape
            const raw = response.data
            let items = []
            if (Array.isArray(raw)) {
              items = raw
            } else if (raw && Array.isArray(raw.data)) {
              items = raw.data
            } else if (raw && raw.data && Array.isArray(raw.data.data)) {
              // Some APIs nest as { data: { data: [...] } }
              items = raw.data.data
            }
            this.requests = items
            console.log('Combined access requests loaded:', this.requests.length)
            console.log('Raw response data:', response.data)

            // Also fetch statistics
            await this.fetchStatistics()
          } else {
            throw new Error(response.error || 'Failed to fetch requests')
          }
        } catch (error) {
          console.error('Error fetching requests:', error)
          this.error =
            'Unable to load combined access requests. Please check your connection and try again.'
          this.requests = []
          this.calculateStats()
        } finally {
          this.isLoading = false
        }
      },

      async fetchStatistics() {
        try {
          const response = await dictAccessService.getDictStatistics()

          if (response.success && response.data) {
            // Map backend keys (ict director) to UI cards explicitly
            const d = response.data || {}
            this.stats = {
              pendingDict: Number(d.pendingDict ?? d.pending ?? 0),
              dictApproved: Number(d.dictApproved ?? 0),
              dictRejected: Number(d.dictRejected ?? 0),
              total: Number(d.total ?? 0)
            }

            // If backend returned zeros but we have data loaded, fallback to local calculation
            const sum =
              (this.stats.pendingDict || 0) +
              (this.stats.dictApproved || 0) +
              (this.stats.dictRejected || 0) +
              (this.stats.total || 0)
            if (sum === 0 && Array.isArray(this.requests) && this.requests.length > 0) {
              this.calculateStats()
            }
          } else {
            // Fall back to calculating stats from loaded requests
            this.calculateStats()
          }
        } catch (error) {
          console.error('Error fetching statistics:', error)
          // Fall back to calculating stats from loaded requests
          this.calculateStats()
        }
      },

      calculateStats() {
        // Ensure requests is an array before calculating stats
        const requests = Array.isArray(this.requests) ? this.requests : []

        this.stats = {
          // ICT Director perspective uses mapped statuses: 'pending' | 'approved' | 'rejected'
          pendingDict: requests.filter((r) => r.status === 'pending').length,
          dictApproved: requests.filter(
            (r) => r.status === 'approved' || r.ict_director_status === 'approved'
          ).length,
          dictRejected: requests.filter(
            (r) => r.status === 'rejected' || r.ict_director_status === 'rejected'
          ).length,
          total: requests.length
        }
      },

      async refreshRequests() {
        await this.fetchRequests()
      },

      viewAndProcessRequest(requestId) {
        // Navigate to ICT Director-specific both-service-form review route
        this.$router.push(`/dict-dashboard/both-service-form/${requestId}`)
      },

      editRequest(requestId) {
        // Navigate to edit mode (same as Divisional Director)
        this.$router.push(`/both-service-form/${requestId}/edit`)
      },

      async cancelRequest(requestId) {
        try {
          const confirmed = confirm(
            'Are you sure you want to cancel this request? This action cannot be undone.'
          )
          if (!confirmed) return

          const reason = prompt('Please provide a reason for cancellation:')
          if (!reason || reason.trim() === '') {
            alert('Cancellation reason is required')
            return
          }

          console.log('Cancelling request:', requestId)

          const response = await dictAccessService.cancelRequest(requestId, reason)

          if (response.success) {
            // Update local state
            const requestIndex = this.requests.findIndex((r) => r.id === requestId)
            if (requestIndex !== -1) {
              this.requests[requestIndex].dict_status = 'cancelled'
              this.requests[requestIndex].status = 'cancelled'
            }

            this.calculateStats()
            alert('Request cancelled successfully!')
          } else {
            throw new Error(response.error || 'Failed to cancel request')
          }
        } catch (error) {
          console.error('Error cancelling request:', error)
          alert('Error cancelling request: ' + error.message)
        }
      },

      hasService(request, serviceType) {
        return (
          (request.services && request.services.includes(serviceType)) ||
          (request.request_types &&
            request.request_types.some(
              (type) =>
                (serviceType === 'jeeva' && type === 'jeeva_access') ||
                (serviceType === 'wellsoft' && type === 'wellsoft') ||
                (serviceType === 'internet' && type === 'internet_access_request')
            ))
        )
      },

      canEdit(request) {
        // ICT Director can edit requests that are Divisional Director approved and pending ICT Director approval
        return request.status === 'divisional_approved'
      },

      canCancel(request) {
        // ICT Director can cancel requests that are not in final states
        const finalStatuses = ['dict_rejected', 'cancelled', 'approved', 'implemented', 'completed']
        return !finalStatuses.includes(request.status)
      },

      formatDate(dateString) {
        if (!dateString) return 'N/A'
        const date = new Date(dateString)
        return date.toLocaleDateString('en-US', {
          year: 'numeric',
          month: 'short',
          day: 'numeric'
        })
      },

      formatTime(dateString) {
        if (!dateString) return 'N/A'
        const date = new Date(dateString)
        return date.toLocaleTimeString('en-US', {
          hour: '2-digit',
          minute: '2-digit'
        })
      },

      getStatusBadgeClass(status) {
        return this.$statusUtils.getStatusBadgeClass(status, 'DictRequestList')
      },

      getStatusIcon(status) {
        return this.$statusUtils.getStatusIcon(status)
      },

      getStatusText(status) {
        // Use centralized status utility with component name for debugging
        return this.$statusUtils.getStatusText(status, 'DictRequestList')
      },

      isPendingStatus(status) {
        // ICT Director perspective (controller maps ict_director_status to 'pending')
        return status === 'pending'
      },

      /**
       * Handle request approval - updates local state to prevent disappearing
       * @param {number} requestId - The request ID
       * @param {Object} approvalData - Approval data
       */
      async handleRequestApproval(requestId, approvalData) {
        try {
          console.log('Approving request:', requestId, approvalData)

          // Call the approval API
          const response = await dictAccessService.approveRequest(requestId, approvalData)

          if (response.success) {
            // Update local state to reflect approval without removing from list
            const requestIndex = this.requests.findIndex((r) => r.id === requestId)
            if (requestIndex !== -1) {
              // Update the status to approved but keep in list - use multiple possible approved statuses
              this.requests[requestIndex].status = 'dict_approved' // Main ICT Director approved status
              this.requests[requestIndex].dict_status = 'approved'
              this.requests[requestIndex].ict_director_approved_at = new Date().toISOString()
              this.requests[requestIndex].ict_director_comments = approvalData.comments || ''
              this.requests[requestIndex].ict_director_name = approvalData.name || ''

              console.log('✅ Request locally updated to approved:', {
                id: requestId,
                newStatus: this.requests[requestIndex].status,
                dictStatus: this.requests[requestIndex].dict_status
              })

              // Trigger reactivity
              this.$forceUpdate()
            }

            // Update statistics
            this.calculateStats()

            alert('Request approved successfully!')

            // Optionally refresh data to ensure consistency
            await this.fetchRequests()
          } else {
            throw new Error(response.error || 'Failed to approve request')
          }
        } catch (error) {
          console.error('Error approving request:', error)
          alert('Error approving request: ' + error.message)
        }
      },

      /**
       * Handle request rejection - updates local state
       * @param {number} requestId - The request ID
       * @param {Object} rejectionData - Rejection data
       */
      async handleRequestRejection(requestId, rejectionData) {
        try {
          console.log('Rejecting request:', requestId, rejectionData)

          // Call the rejection API
          const response = await dictAccessService.rejectRequest(requestId, rejectionData)

          if (response.success) {
            // Update local state to reflect rejection
            const requestIndex = this.requests.findIndex((r) => r.id === requestId)
            if (requestIndex !== -1) {
              this.requests[requestIndex].status = 'dict_rejected' // Main ICT Director rejected status
              this.requests[requestIndex].dict_status = 'rejected'
              this.requests[requestIndex].ict_director_rejected_at = new Date().toISOString()
              this.requests[requestIndex].ict_director_comments = rejectionData.comments || ''
              this.requests[requestIndex].ict_director_name = rejectionData.name || ''

              console.log('❌ Request locally updated to rejected:', {
                id: requestId,
                newStatus: this.requests[requestIndex].status,
                dictStatus: this.requests[requestIndex].dict_status
              })

              // Trigger reactivity
              this.$forceUpdate()
            }

            // Update statistics
            this.calculateStats()

            alert('Request rejected successfully!')

            // Optionally refresh data to ensure consistency
            await this.fetchRequests()
          } else {
            throw new Error(response.error || 'Failed to reject request')
          }
        } catch (error) {
          console.error('Error rejecting request:', error)
          alert('Error rejecting request: ' + error.message)
        }
      },

      /**
       * Handle window focus event - refresh data when user returns to the list
       * This ensures that if user approves/rejects a request and comes back,
       * the list shows updated status
       */
      onWindowFocus() {
        // Only refresh if the component is currently active, not loading, and on the right route
        const isOnDictRequestsPage = this.$route?.path === '/dict-dashboard/combined-requests'

        if (!this.isLoading && !document.hidden && isOnDictRequestsPage) {
          console.log('Window focused on dict requests page - refreshing data')
          // Use a small delay to ensure any backend updates are processed
          setTimeout(() => {
            this.fetchRequests()
          }, 500)
        }
      },

      // Dropdown management methods
      toggleDropdown(requestId) {
        console.log('Toggle dropdown for request:', requestId)
        this.openDropdownId = this.openDropdownId === requestId ? null : requestId

        // Add a small delay to ensure the dropdown is positioned correctly
        if (this.openDropdownId === requestId) {
          this.$nextTick(() => {
            console.log('Dropdown opened for request:', requestId)
          })
        }
      },

      closeAllDropdowns(event) {
        // Only close if clicking outside the dropdown
        if (!event || !event.target.closest('.three-dot-menu')) {
          console.log('Closing all dropdowns')
          this.openDropdownId = null
        }
      },

      // Role-specific actions logic
      getAvailableActions(request) {
        const actions = []
        const userRole = this.userRole
        const status = request.status

        // Define role mappings
        const ROLES = {
          HEAD_OF_IT: 'head_of_it',
          ICT_OFFICER: 'ict_officer',
          ICT_DIRECTOR: 'ict_director',
          DIVISIONAL_DIRECTOR: 'divisional_director',
          HEAD_OF_DEPARTMENT: 'hod',
          STAFF: 'staff'
        }

        // Head of IT actions
        if (userRole === ROLES.HEAD_OF_IT) {
          if (['dict_approved', 'ict_director_approved', 'approved'].includes(status)) {
            actions.push({
              key: 'assign_task',
              label: 'Assign Task',
              icon: 'fas fa-user-plus'
            })
            actions.push({
              key: 'cancel_task',
              label: 'Cancel Task',
              icon: 'fas fa-times-circle'
            })
          }
          actions.push({
            key: 'view_timeline',
            label: 'View Timeline',
            icon: 'fas fa-history'
          })
        }
        // ICT Officer actions
        else if (userRole === ROLES.ICT_OFFICER) {
          // Only show for requests assigned to this officer
          if (['assigned', 'in_progress', 'head_it_approved'].includes(status)) {
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
        // ICT Director actions (current default for this page)
        else if (userRole === ROLES.ICT_DIRECTOR) {
          actions.push({
            key: 'view_and_process',
            label: 'Approve',
            icon: 'fas fa-check-circle',
            color: 'green'
          })
          if (this.canEdit(request)) {
            actions.push({
              key: 'edit_request',
              label: 'Edit Request',
              icon: 'fas fa-edit'
            })
          }
          if (this.canCancel(request)) {
            actions.push({
              key: 'cancel_request',
              label: 'Cancel Request',
              icon: 'fas fa-times'
            })
          }
          // Add View Progress for requests that are in progress or implemented
          if (
            [
              'implementation_in_progress',
              'implemented',
              'completed',
              'assigned_to_ict',
              'head_it_approved'
            ].includes(status)
          ) {
            actions.push({
              key: 'view_progress',
              label: 'View Progress',
              icon: 'fas fa-chart-line'
            })
          }
          actions.push({
            key: 'view_timeline',
            label: 'View Timeline',
            icon: 'fas fa-history'
          })
        }
        // Other roles (Divisional Director, HOD, Requester)
        else {
          actions.push({
            key: 'view_and_process',
            label: 'Approve',
            icon: 'fas fa-check-circle',
            color: 'green'
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
          case 'view_timeline':
            this.viewTimeline(request)
            break
          case 'view_progress':
            this.viewProgress(request)
            break
          case 'view_and_process':
            this.viewAndProcessRequest(request.id)
            break
          case 'edit_request':
            this.editRequest(request.id)
            break
          case 'cancel_request':
            this.cancelRequest(request.id)
            break
          default:
            console.warn('Unknown action:', actionKey)
        }
      },

      // New action methods
      assignTask(request) {
        console.log('Assigning task for request:', request.id)
        // Navigate to ICT Officer selection page
        this.$router.push(`/head_of_it-dashboard/select-ict-officer/${request.id}`)
      },

      cancelTask(request) {
        console.log('Cancelling task for request:', request.id)
        // Similar to existing cancelRequest but for Head of IT
        this.cancelRequest(request.id)
      },

      updateProgress(request) {
        console.log('Updating progress for request:', request.id)
        // This would typically open a modal or navigate to an update form
        alert(
          'Update Progress functionality would be implemented here. This might open a modal or dedicated page.'
        )
      },

      viewProgress(request) {
        console.log('👁️ DictRequestList: Viewing progress for request:', request.id)
        // Navigate to progress view - using ICT dashboard route for consistency
        this.$router.push(`/user-security-access/${request.user_access_id || request.id}`)
      },

      viewTimeline(request) {
        console.log('📅 DictRequestList: Opening timeline for request:', request.id)
        this.selectedRequestId = request.id
        this.showTimeline = true
      },

      closeTimeline() {
        console.log('📅 DictRequestList: Closing timeline modal')
        this.showTimeline = false
        this.selectedRequestId = null
      },

      async handleTimelineUpdate() {
        console.log('🔄 DictRequestList: Timeline updated, refreshing requests list...')
        await this.fetchRequests()
      },

      // Helper method to determine if separator should be shown
      shouldShowSeparator(currentAction, previousAction) {
        // Add separator before destructive actions or different action groups
        const destructiveActions = ['cancel_task', 'cancel_request']
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
      }
    }
  }
</script>

<style scoped>
  .sidebar-narrow {
    /* Force the sidebar to be a bit wider */
    max-width: 280px;
  }

  /* Override the sidebar width classes for this component */
  .sidebar-narrow :deep(.sidebar-expanded) {
    width: 280px !important;
  }

  .sidebar-narrow :deep(.sidebar-collapsed) {
    width: 64px !important;
  }

  /* Three-dot menu enhancements */
  .three-dot-menu {
    position: relative;
  }

  /* Ensure dropdown is always visible and properly positioned */
  .dropdown-menu {
    position: absolute !important;
    z-index: 9999 !important;
    min-width: 12rem;
    max-width: 16rem;
    transform: translateX(-100%);
  }

  /* Fix for table cell positioning context */
  .three-dot-menu {
    position: relative;
    z-index: 1;
  }

  .three-dot-menu .dropdown-menu {
    position: absolute;
    top: 100%;
    right: 0;
    transform: translateX(0);
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
    /* Ensure dropdown appears above other elements */
    box-shadow:
      0 10px 25px rgba(0, 0, 0, 0.15),
      0 4px 6px rgba(0, 0, 0, 0.1) !important;
  }

  /* Ensure table cells allow overflow for dropdown */
  .three-dot-menu {
    position: relative;
    z-index: 10;
  }

  /* Make sure table doesn't clip dropdown */
  table {
    position: relative;
    z-index: 1;
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

  /* Ensure main content doesn't block header dropdowns */
  main {
    position: relative;
    z-index: 1;
  }
</style>
