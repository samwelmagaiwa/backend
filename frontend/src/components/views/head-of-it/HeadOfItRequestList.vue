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
                  <td class="px-4 py-3 text-center">
                    <div class="flex flex-col items-center space-y-1">
                      <button
                        @click="viewAndProcessRequest(request.id)"
                        class="bg-blue-600 text-white text-base rounded hover:bg-blue-700 inline-block font-medium"
                        :style="{ padding: '6px 12px', width: 'fit-content' }"
                      >
                        View & Process
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
  import headOfItService from '@/services/headOfItService'
  import statusUtils from '@/utils/statusUtils'

  export default {
    name: 'HeadOfItRequestList',
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
          pendingHeadOfIt: 0,
          headOfItApproved: 0,
          headOfItRejected: 0,
          total: 0
        },
        error: null,
        $statusUtils: statusUtils
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
        await this.fetchRequests()
        console.log('HeadOfItRequestList: Component initialized successfully')
      } catch (error) {
        console.error('HeadOfItRequestList: Error during mount:', error)
        this.error = 'Failed to initialize component: ' + error.message
        this.isLoading = false
      }
    },
    methods: {
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
      }
    }
  }
</script>

<style scoped>
  .sidebar-narrow {
    flex-shrink: 0;
  }
</style>
