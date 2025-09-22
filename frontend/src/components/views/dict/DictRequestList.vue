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
            <h3 class="font-bold text-lg">Error</h3>
            <p class="text-base">{{ error }}</p>
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
              <h3 class="text-yellow-200 text-base font-semibold">Pending My Approval</h3>
              <p class="text-white text-3xl font-bold">{{ stats.pendingDict }}</p>
            </div>
            <div class="bg-green-600/25 border border-green-400/40 p-4 rounded-lg">
              <h3 class="text-green-200 text-base font-semibold">Approved by Me</h3>
              <p class="text-white text-3xl font-bold">{{ stats.dictApproved }}</p>
            </div>
            <div class="bg-red-600/25 border border-red-400/40 p-4 rounded-lg">
              <h3 class="text-red-200 text-base font-semibold">Rejected by Me</h3>
              <p class="text-white text-3xl font-bold">{{ stats.dictRejected }}</p>
            </div>
            <div class="bg-blue-600/25 border border-blue-400/40 p-4 rounded-lg">
              <h3 class="text-blue-200 text-base font-semibold">Total Requests</h3>
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
                    Divisional Approval Date
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
                          class="fas fa-clock text-yellow-400 text-sm"
                          title="Pending your approval"
                        ></i>
                        <i
                          v-else
                          class="fas fa-check-circle text-green-400 text-sm"
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
                        class="px-2 py-1 rounded text-xs bg-blue-100 text-blue-800"
                      >
                        Jeeva
                      </span>
                      <span
                        v-if="hasService(request, 'wellsoft')"
                        class="px-2 py-1 rounded text-xs bg-green-100 text-green-800"
                      >
                        Wellsoft
                      </span>
                      <span
                        v-if="hasService(request, 'internet')"
                        class="px-2 py-1 rounded text-xs bg-cyan-100 text-cyan-800"
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
                        class="rounded text-sm font-medium inline-block"
                        :style="{ padding: '4px 8px', width: 'fit-content' }"
                      >
                        {{ getStatusText(request.status) }}
                      </span>
                    </div>
                  </td>

                  <!-- Actions -->
                  <td class="px-4 py-3 text-center">
                    <div class="flex flex-col items-center space-y-1">
                      <button
                        @click="viewAndProcessRequest(request.id)"
                        class="bg-blue-600 text-white text-sm rounded hover:bg-blue-700 inline-block font-medium"
                        :style="{ padding: '6px 12px', width: 'fit-content' }"
                      >
                        View & Process
                      </button>
                      <button
                        v-if="canEdit(request)"
                        @click="editRequest(request.id)"
                        class="bg-amber-600 text-white text-sm rounded hover:bg-amber-700 inline-block font-medium"
                        :style="{ padding: '6px 12px', width: 'fit-content' }"
                      >
                        Edit
                      </button>
                      <button
                        v-if="canCancel(request)"
                        @click="cancelRequest(request.id)"
                        class="bg-red-600 text-white text-sm rounded hover:bg-red-700 inline-block font-medium"
                        :style="{ padding: '6px 12px', width: 'fit-content' }"
                      >
                        Cancel
                      </button>
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
      <div class="bg-white rounded-lg shadow-xl p-8 text-center">
        <div
          class="w-16 h-16 border-4 border-blue-600 border-t-transparent rounded-full animate-spin mx-auto mb-4"
        ></div>
        <p class="text-gray-600 text-base font-medium">Loading requests...</p>
      </div>
    </div>
  </div>
</template>

<script>
  import Header from '@/components/header.vue'
  import ModernSidebar from '@/components/ModernSidebar.vue'
  import AppFooter from '@/components/footer.vue'
  import dictAccessService from '@/services/dictAccessService'
  import statusUtils from '@/utils/statusUtils'

  export default {
    name: 'DictRequestList',
    components: {
      Header,
      ModernSidebar,
      AppFooter
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
      // Clean up event listener
      window.removeEventListener('focus', this.onWindowFocus)
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
            // Handle the nested response structure: response.data.data.data
            const responseData = response.data?.data || response.data || {}
            this.requests = Array.isArray(responseData.data)
              ? responseData.data
              : Array.isArray(responseData)
                ? responseData
                : []
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

          if (response.success) {
            this.stats = response.data
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
          // Count requests pending ICT Director approval (approved by Divisional Director)
          pendingDict: requests.filter((r) => this.isPendingStatus(r.status)).length,

          // Count requests approved by ICT Director
          dictApproved: requests.filter(
            (r) =>
              r.status === 'dict_approved' ||
              r.status === 'ict_director_approved' ||
              r.status === 'approved' ||
              r.status === 'implemented' ||
              r.status === 'completed'
          ).length,

          // Count requests rejected by ICT Director
          dictRejected: requests.filter(
            (r) => r.status === 'dict_rejected' || r.status === 'ict_director_rejected'
          ).length,

          // Total requests relevant to ICT Director
          total: requests.length
        }
      },

      async refreshRequests() {
        await this.fetchRequests()
      },

      viewAndProcessRequest(requestId) {
        // Navigate to both-service-form.vue with populated data (same as Divisional Director)
        this.$router.push(`/both-service-form/${requestId}`)
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
        // Define which statuses are considered "pending ICT Director approval"
        const pendingStatuses = [
          'divisional_approved', // Main pending status for ICT Director
          'pending_ict_director', // Alternative pending status
          'pending_dict' // Another possible pending status
        ]
        return pendingStatuses.includes(status)
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
</style>
