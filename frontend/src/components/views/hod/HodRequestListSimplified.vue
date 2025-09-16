<template>
  <div class="flex flex-col h-screen">
    <Header />
    <div class="flex flex-1 overflow-hidden">
      <ModernSidebar />
      <main class="flex-1 p-4 bg-blue-900 overflow-y-auto">
        <div class="max-w-full mx-auto">
          <!-- Header -->
          <div class="bg-white/10 rounded-lg p-6 mb-4">
            <h1 class="text-2xl font-bold text-white mb-2">
              ACCESS REQUESTS - HOD APPROVAL STAGE
            </h1>
            <p class="text-blue-200">
              Staff requests displayed in FIFO order. Click "View & Process" to capture: Module Requested for, Module Request, Access Rights, and Comments.
            </p>
          </div>

          <!-- Error Display -->
          <div v-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <h3 class="font-bold">Error</h3>
            <p>{{ error }}</p>
            <button @click="fetchRequests" class="mt-2 bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
              Retry
            </button>
          </div>

          <!-- Stats -->
          <div class="grid grid-cols-4 gap-4 mb-6">
            <div class="bg-yellow-600/25 border border-yellow-400/40 p-4 rounded-lg">
              <h3 class="text-yellow-200 text-sm">Pending HOD Approval</h3>
              <p class="text-white text-2xl font-bold">{{ stats.pendingHod }}</p>
            </div>
            <div class="bg-green-600/25 border border-green-400/40 p-4 rounded-lg">
              <h3 class="text-green-200 text-sm">HOD Approved</h3>
              <p class="text-white text-2xl font-bold">{{ stats.hodApproved }}</p>
            </div>
            <div class="bg-red-600/25 border border-red-400/40 p-4 rounded-lg">
              <h3 class="text-red-200 text-sm">HOD Rejected</h3>
              <p class="text-white text-2xl font-bold">{{ stats.hodRejected }}</p>
            </div>
            <div class="bg-blue-600/25 border border-blue-400/40 p-4 rounded-lg">
              <h3 class="text-blue-200 text-sm">Total Requests</h3>
              <p class="text-white text-2xl font-bold">{{ stats.total }}</p>
            </div>
          </div>

          <!-- Filters -->
          <div class="bg-white/10 rounded-lg p-4 mb-6">
            <div class="flex gap-4">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Search by staff name, PF number, or department..."
                class="flex-1 px-3 py-2 bg-white/20 border border-blue-300/30 rounded text-white placeholder-blue-200/60"
              />
              <select
                v-model="statusFilter"
                class="px-3 py-2 bg-white/20 border border-blue-300/30 rounded text-white"
              >
                <option value="">All Statuses</option>
                <option value="pending_hod">Pending HOD</option>
                <option value="hod_approved">HOD Approved</option>
                <option value="hod_rejected">HOD Rejected</option>
              </select>
              <button
                @click="refreshRequests"
                class="px-6 py-2 bg-teal-600 text-white rounded hover:bg-teal-700"
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
                  <th class="px-4 py-3 text-left text-blue-100">Request ID</th>
                  <th class="px-4 py-3 text-left text-blue-100">Request Type</th>
                  <th class="px-4 py-3 text-left text-blue-100">Personal Information</th>
                  <th class="px-4 py-3 text-left text-blue-100">Submission Date (FIFO)</th>
                  <th class="px-4 py-3 text-left text-blue-100">Current Status</th>
                  <th class="px-4 py-3 text-center text-blue-100">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="request in filteredRequests" :key="request.id" class="border-t border-blue-300/20 hover:bg-blue-700/30">
                  <!-- Request ID -->
                  <td class="px-4 py-3">
                    <div class="text-white font-medium">
                      {{ request.request_id || `REQ-${request.id.toString().padStart(6, '0')}` }}
                    </div>
                    <div class="text-purple-300 text-xs">ID: {{ request.id }}</div>
                  </td>

                  <!-- Request Type -->
                  <td class="px-4 py-3">
                    <div class="flex flex-wrap gap-1">
                      <span v-if="hasService(request, 'jeeva')" class="px-2 py-1 rounded text-xs bg-blue-100 text-blue-800">
                        Jeeva
                      </span>
                      <span v-if="hasService(request, 'wellsoft')" class="px-2 py-1 rounded text-xs bg-green-100 text-green-800">
                        Wellsoft
                      </span>
                      <span v-if="hasService(request, 'internet')" class="px-2 py-1 rounded text-xs bg-cyan-100 text-cyan-800">
                        Internet
                      </span>
                    </div>
                  </td>

                  <!-- Personal Information -->
                  <td class="px-4 py-3">
                    <div class="text-white font-medium">
                      {{ request.staff_name || request.full_name || 'Unknown User' }}
                    </div>
                    <div class="text-blue-300 text-sm">
                      {{ request.phone || request.phone_number || 'No phone' }}
                    </div>
                    <div v-if="request.pf_number" class="text-teal-300 text-xs">
                      PF: {{ request.pf_number }}
                    </div>
                    <div class="text-blue-200 text-xs">
                      Dept: {{ request.department || 'Unknown' }}
                    </div>
                  </td>

                  <!-- Submission Date -->
                  <td class="px-4 py-3">
                    <div class="text-white font-medium">
                      {{ formatDate(request.created_at || request.submission_date) }}
                    </div>
                    <div class="text-blue-300 text-xs">
                      {{ formatTime(request.created_at || request.submission_date) }}
                    </div>
                  </td>

                  <!-- Current Status -->
                  <td class="px-4 py-3">
                    <span :class="getStatusBadgeClass(request.hod_status || request.status || 'pending_hod')" class="px-2 py-1 rounded text-xs font-medium">
                      {{ getStatusText(request.hod_status || request.status || 'pending_hod') }}
                    </span>
                  </td>

                  <!-- Actions -->
                  <td class="px-4 py-3 text-center">
                    <div class="flex items-center justify-center space-x-2">
                      <button
                        @click="viewAndProcessRequest(request.id)"
                        class="px-3 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700"
                      >
                        View & Process
                      </button>
                      <button
                        v-if="canEdit(request)"
                        @click="editRequest(request.id)"
                        class="px-3 py-1 bg-amber-600 text-white text-xs rounded hover:bg-amber-700"
                      >
                        Edit
                      </button>
                      <button
                        v-if="canCancel(request)"
                        @click="cancelRequest(request.id)"
                        class="px-3 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700"
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
              <h3 class="text-white text-lg font-medium mb-2">No requests found</h3>
              <p class="text-blue-300">
                {{ searchQuery || statusFilter ? 'No requests are pending your approval.' : 'Try adjusting your filters' }}
              </p>
            </div>

            <!-- Pagination -->
            <div v-if="filteredRequests.length > 0" class="px-4 py-3 border-t border-blue-300/30">
              <div class="text-blue-300 text-sm">
                Showing {{ filteredRequests.length }} of {{ requests.length }} requests
              </div>
            </div>
          </div>

          <AppFooter />
        </div>
      </main>
    </div>

    <!-- Loading Modal -->
    <div v-if="isLoading" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-xl p-8 text-center">
        <div class="w-16 h-16 border-4 border-blue-600 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
        <p class="text-gray-600">Loading requests...</p>
      </div>
    </div>
  </div>
</template>

<script>
import Header from '@/components/header.vue'
import ModernSidebar from '@/components/ModernSidebar.vue'
import AppFooter from '@/components/footer.vue'
import combinedAccessService from '@/services/combinedAccessService'

export default {
  name: 'HodRequestListSimplified',
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
        pendingHod: 0,
        hodApproved: 0,
        hodRejected: 0,
        total: 0
      },
      error: null
    }
  },
  computed: {
    filteredRequests() {
      // Ensure requests is always an array
      if (!Array.isArray(this.requests)) {
        console.warn('HodRequestList: requests is not an array:', this.requests)
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
        filtered = filtered.filter(
          (request) => (request.hod_status || request.status || 'pending_hod') === this.statusFilter
        )
      }

      // Sort by FIFO order (oldest first)
      return filtered.sort((a, b) => {
        const dateA = new Date(a.created_at || a.submission_date || 0)
        const dateB = new Date(b.created_at || b.submission_date || 0)
        return dateA - dateB
      })
    }
  },
  async mounted() {
    try {
      console.log('HodRequestListSimplified: Component mounted, initializing...')
      await this.fetchRequests()
      console.log('HodRequestListSimplified: Component initialized successfully')
    } catch (error) {
      console.error('HodRequestListSimplified: Error during mount:', error)
      this.error = 'Failed to initialize component: ' + error.message
      this.isLoading = false
    }
  },
  methods: {
    async fetchRequests() {
      this.isLoading = true
      this.error = null

      try {
        console.log('Fetching combined access requests for HOD approval...')

        const response = await combinedAccessService.getHodRequests({
          search: this.searchQuery || undefined,
          status: this.statusFilter || undefined,
          per_page: 50
        })

        if (response.success) {
          // Handle the nested response structure: response.data.data.data
          const responseData = response.data?.data || response.data || {}
          this.requests = Array.isArray(responseData.data) ? responseData.data : (Array.isArray(responseData) ? responseData : [])
          console.log('Combined access requests loaded:', this.requests.length)
          console.log('Raw response data:', response.data)
          
          // Also fetch statistics
          await this.fetchStatistics()
        } else {
          throw new Error(response.error || 'Failed to fetch requests')
        }

      } catch (error) {
        console.error('Error fetching requests:', error)
        this.error = 'Unable to load combined access requests. Please check your connection and try again.'
        this.requests = []
        this.calculateStats()
      } finally {
        this.isLoading = false
      }
    },

    async fetchStatistics() {
      try {
        const response = await combinedAccessService.getHodStatistics()
        
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
        pendingHod: requests.filter((r) => (r.hod_status || r.status) === 'pending_hod').length,
        hodApproved: requests.filter((r) => (r.hod_status || r.status) === 'hod_approved').length,
        hodRejected: requests.filter((r) => (r.hod_status || r.status) === 'hod_rejected').length,
        total: requests.length
      }
    },

    async refreshRequests() {
      await this.fetchRequests()
    },

    viewAndProcessRequest(requestId) {
      // Navigate to both-service-form.vue with populated data
      this.$router.push(`/both-service-form/${requestId}`)
    },

    editRequest(requestId) {
      // Navigate to edit mode
      this.$router.push(`/both-service-form/${requestId}/edit`)
    },

    async cancelRequest(requestId) {
      try {
        const confirmed = confirm('Are you sure you want to cancel this request? This action cannot be undone.')
        if (!confirmed) return

        const reason = prompt('Please provide a reason for cancellation:')
        if (!reason || reason.trim() === '') {
          alert('Cancellation reason is required')
          return
        }

        console.log('Cancelling request:', requestId)
        
        const response = await combinedAccessService.cancelRequest(requestId, reason)
        
        if (response.success) {
          // Update local state
          const requestIndex = this.requests.findIndex(r => r.id === requestId)
          if (requestIndex !== -1) {
            this.requests[requestIndex].hod_status = 'cancelled'
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
      return request.services && request.services.includes(serviceType) ||
             request.request_types && request.request_types.some(type => 
               (serviceType === 'jeeva' && type === 'jeeva_access') ||
               (serviceType === 'wellsoft' && type === 'wellsoft') ||
               (serviceType === 'internet' && type === 'internet_access_request')
             )
    },

    canEdit(request) {
      // HOD can edit requests that are pending
      return (request.hod_status || request.status) === 'pending_hod'
    },

    canCancel(request) {
      // HOD can cancel requests that are not already rejected or cancelled
      const status = request.hod_status || request.status
      return status !== 'hod_rejected' && status !== 'cancelled'
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
      const classes = {
        'pending_hod': 'bg-yellow-100 text-yellow-800',
        'hod_approved': 'bg-green-100 text-green-800',
        'hod_rejected': 'bg-red-100 text-red-800',
        'cancelled': 'bg-gray-100 text-gray-800'
      }
      return classes[status] || 'bg-gray-100 text-gray-800'
    },

    getStatusIcon(status) {
      const icons = {
        'pending_hod': 'fas fa-clock',
        'hod_approved': 'fas fa-check',
        'hod_rejected': 'fas fa-times',
        'cancelled': 'fas fa-ban'
      }
      return icons[status] || 'fas fa-question'
    },

    getStatusText(status) {
      const texts = {
        'pending_hod': 'Pending HOD Approval',
        'hod_approved': 'HOD Approved',
        'hod_rejected': 'HOD Rejected',
        'cancelled': 'Cancelled'
      }
      return texts[status] || 'Unknown Status'
    }
  }
}
</script>
