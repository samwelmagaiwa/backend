<template>
  <div class="min-h-screen bg-gray-100">
    <Header />
    <div class="flex">
      <ModernSidebar />
      <main class="flex-1 p-8">
        <div class="max-w-6xl mx-auto">
          <h1 class="text-3xl font-bold text-gray-900 mb-8">ICT Device Requests</h1>

          <!-- Loading State -->
          <div v-if="isLoading" class="text-center py-8">
            <div
              class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"
            ></div>
            <p class="mt-2 text-gray-600">Loading requests...</p>
          </div>

          <!-- Error State -->
          <div
            v-else-if="error"
            class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"
          >
            <h3 class="font-bold">Error Loading Requests</h3>
            <p>{{ error }}</p>
            <button
              @click="fetchRequests"
              class="mt-2 bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700"
            >
              Retry
            </button>
          </div>

          <!-- Requests Table -->
          <div v-else class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
              <h2 class="text-xl font-semibold text-gray-800">
                Device Borrowing Requests ({{ requests.length }})
              </h2>
            </div>

            <div v-if="requests.length === 0" class="text-center py-12">
              <p class="text-gray-500">No requests found</p>
            </div>

            <div v-else class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      ID
                    </th>
                    <th
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      Borrower
                    </th>
                    <th
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      Device
                    </th>
                    <th
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      Status
                    </th>
                    <th
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      Date
                    </th>
                    <th
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      Actions
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="request in requests" :key="request.id" class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                      {{ request.id }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ request.borrower_name || 'Unknown' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ request.device_name || request.device_type || 'Unknown' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span
                        :class="getStatusClass(request.ict_approve)"
                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                      >
                        {{ request.ict_approve || 'pending' }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ formatDate(request.booking_date) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                      <router-link
                        :to="`/ict-approval/request/${request.id}`"
                        class="text-blue-600 hover:text-blue-900 mr-3"
                      >
                        View
                      </router-link>
                      <button
                        v-if="request.ict_approve === 'pending'"
                        @click="approveRequest(request.id)"
                        class="text-green-600 hover:text-green-900 mr-3"
                      >
                        Approve
                      </button>
                      <button
                        v-if="request.ict_approve === 'pending'"
                        @click="rejectRequest(request.id)"
                        class="text-red-600 hover:text-red-900"
                      >
                        Reject
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
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
  import { deviceBorrowingService } from '@/services/deviceBorrowingService'

  export default {
    name: 'RequestsListSimple',
    components: {
      Header,
      ModernSidebar
    },
    data() {
      return {
        requests: [],
        isLoading: false,
        error: null
      }
    },
    async mounted() {
      await this.fetchRequests()
    },
    methods: {
      async fetchRequests() {
        this.isLoading = true
        this.error = null

        try {
          console.log('Fetching requests...')
          const response = await deviceBorrowingService.getAllRequests({
            per_page: 50
          })

          console.log('Service response:', response)

          if (response.success) {
            this.requests = response.data.data || []
            console.log('Requests loaded:', this.requests.length)
          } else {
            throw new Error(response.message || 'Failed to fetch requests')
          }
        } catch (error) {
          console.error('Error fetching requests:', error)
          this.error = error.message
        } finally {
          this.isLoading = false
        }
      },

      async approveRequest(requestId) {
        try {
          const notes = prompt('Add approval notes (optional):') || ''
          const response = await deviceBorrowingService.approveRequest(requestId, notes)

          if (response.success) {
            alert('Request approved successfully!')
            await this.fetchRequests() // Refresh the list
          } else {
            throw new Error(response.message || 'Failed to approve request')
          }
        } catch (error) {
          console.error('Error approving request:', error)
          alert('Error approving request: ' + error.message)
        }
      },

      async rejectRequest(requestId) {
        try {
          const reason = prompt('Please provide a rejection reason (required):')
          if (!reason || reason.trim() === '') {
            alert('Rejection reason is required')
            return
          }

          const response = await deviceBorrowingService.rejectRequest(requestId, reason)

          if (response.success) {
            alert('Request rejected successfully!')
            await this.fetchRequests() // Refresh the list
          } else {
            throw new Error(response.message || 'Failed to reject request')
          }
        } catch (error) {
          console.error('Error rejecting request:', error)
          alert('Error rejecting request: ' + error.message)
        }
      },

      getStatusClass(status) {
        const classes = {
          pending: 'bg-yellow-100 text-yellow-800',
          approved: 'bg-green-100 text-green-800',
          rejected: 'bg-red-100 text-red-800'
        }
        return classes[status] || classes.pending
      },

      formatDate(dateString) {
        if (!dateString) return 'N/A'
        try {
          return new Date(dateString).toLocaleDateString()
        } catch {
          return dateString
        }
      }
    }
  }
</script>
