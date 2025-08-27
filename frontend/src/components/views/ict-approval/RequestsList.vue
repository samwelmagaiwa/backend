<template>
  <div class="flex flex-col h-screen">
    <Header />
    <div class="flex flex-1 overflow-hidden">
      <ModernSidebar v-model:collapsed="sidebarCollapsed" />
      <main
        class="flex-1 p-8 bg-gradient-to-br from-blue-900 via-blue-800 to-teal-900 overflow-y-auto relative"
      >
        <!-- Background Pattern -->
        <div class="absolute inset-0 overflow-hidden">
          <div class="absolute inset-0">
            <div
              v-for="i in 15"
              :key="i"
              class="absolute text-white opacity-5 animate-float"
              :style="{
                left: Math.random() * 100 + '%',
                top: Math.random() * 100 + '%',
                animationDelay: Math.random() * 3 + 's',
                animationDuration: Math.random() * 3 + 2 + 's',
                fontSize: Math.random() * 15 + 8 + 'px',
              }"
            >
              <i
                :class="[
                  'fas',
                  [
                    'fa-laptop',
                    'fa-tv',
                    'fa-desktop',
                    'fa-keyboard',
                    'fa-mouse',
                    'fa-headphones',
                  ][Math.floor(Math.random() * 6)],
                ]"
              ></i>
            </div>
          </div>
        </div>

        <div class="max-w-full mx-auto px-4 relative z-10">
          <!-- Header Section -->
          <div
            class="booking-glass-card rounded-t-3xl p-8 mb-0 border-b border-blue-300/30 animate-fade-in"
          >
            <div class="flex justify-between items-center">
              <!-- Left Logo -->
              <div
                class="w-28 h-28 mr-8 transform hover:scale-110 transition-transform duration-300"
              >
                <div
                  class="w-full h-full bg-gradient-to-br from-blue-500/20 to-teal-500/20 rounded-2xl backdrop-blur-sm border-2 border-blue-300/40 flex items-center justify-center shadow-2xl hover:shadow-blue-500/25"
                >
                  <img
                    src="/assets/images/ngao2.png"
                    alt="National Shield"
                    class="max-w-20 max-h-20 object-contain"
                  />
                </div>
              </div>

              <!-- Center Content -->
              <div class="text-center flex-1">
                <h1
                  class="text-2xl font-bold text-white mb-4 tracking-wide drop-shadow-lg animate-fade-in"
                >
                  MUHIMBILI NATIONAL HOSPITAL
                </h1>
                <div class="relative inline-block mb-4">
                  <div
                    class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-2 rounded-full text-base font-bold shadow-2xl transform hover:scale-105 transition-all duration-300 border-2 border-blue-400/60"
                  >
                    <span class="relative z-10 flex items-center gap-3">
                      <i class="fas fa-clipboard-list text-base"></i>
                      ICT DEVICE REQUESTS MANAGEMENT
                    </span>
                    <div
                      class="absolute inset-0 bg-gradient-to-r from-blue-700 to-blue-800 rounded-full opacity-0 hover:opacity-100 transition-opacity duration-300"
                    ></div>
                  </div>
                </div>
                <h2
                  class="text-lg font-bold text-blue-100 tracking-wide drop-shadow-md animate-fade-in-delay"
                >
                  ICT OFFICER DASHBOARD
                </h2>
              </div>

              <!-- Right Logo -->
              <div
                class="w-28 h-28 ml-8 transform hover:scale-110 transition-transform duration-300"
              >
                <div
                  class="w-full h-full bg-gradient-to-br from-teal-500/20 to-blue-500/20 rounded-2xl backdrop-blur-sm border-2 border-teal-300/40 flex items-center justify-center shadow-2xl hover:shadow-teal-500/25"
                >
                  <img
                    src="/assets/images/logo2.png"
                    alt="Muhimbili Logo"
                    class="max-w-20 max-h-20 object-contain"
                  />
                </div>
              </div>
            </div>
          </div>

          <!-- Main Content -->
          <div
            class="booking-glass-card rounded-b-3xl overflow-hidden animate-slide-up"
          >
            <div class="p-8">
              <!-- Stats Cards -->
              <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div
                  class="bg-gradient-to-r from-blue-600/25 to-teal-600/25 border-2 border-blue-400/40 p-6 rounded-2xl backdrop-blur-sm hover:shadow-xl hover:shadow-blue-500/20 transition-all duration-500"
                >
                  <div class="flex items-center">
                    <div
                      class="w-12 h-12 bg-gradient-to-br from-blue-500 to-teal-600 rounded-lg flex items-center justify-center shadow-lg"
                    >
                      <i class="fas fa-clock text-white text-lg"></i>
                    </div>
                    <div class="ml-4">
                      <p class="text-sm font-semibold text-blue-200">Pending</p>
                      <p class="text-2xl font-bold text-white">
                        {{ stats.pending }}
                      </p>
                    </div>
                  </div>
                </div>

                <div
                  class="bg-gradient-to-r from-green-600/25 to-emerald-600/25 border-2 border-green-400/40 p-6 rounded-2xl backdrop-blur-sm hover:shadow-xl hover:shadow-green-500/20 transition-all duration-500"
                >
                  <div class="flex items-center">
                    <div
                      class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-lg flex items-center justify-center shadow-lg"
                    >
                      <i class="fas fa-check-circle text-white text-lg"></i>
                    </div>
                    <div class="ml-4">
                      <p class="text-sm font-semibold text-green-200">
                        Returned
                      </p>
                      <p class="text-2xl font-bold text-white">
                        {{ stats.returned }}
                      </p>
                    </div>
                  </div>
                </div>

                <div
                  class="bg-gradient-to-r from-red-600/25 to-pink-600/25 border-2 border-red-400/40 p-6 rounded-2xl backdrop-blur-sm hover:shadow-xl hover:shadow-red-500/20 transition-all duration-500"
                >
                  <div class="flex items-center">
                    <div
                      class="w-12 h-12 bg-gradient-to-br from-red-500 to-pink-600 rounded-lg flex items-center justify-center shadow-lg"
                    >
                      <i
                        class="fas fa-exclamation-triangle text-white text-lg"
                      ></i>
                    </div>
                    <div class="ml-4">
                      <p class="text-sm font-semibold text-red-200">
                        Compromised
                      </p>
                      <p class="text-2xl font-bold text-white">
                        {{ stats.compromised }}
                      </p>
                    </div>
                  </div>
                </div>

                <div
                  class="bg-gradient-to-r from-purple-600/25 to-indigo-600/25 border-2 border-purple-400/40 p-6 rounded-2xl backdrop-blur-sm hover:shadow-xl hover:shadow-purple-500/20 transition-all duration-500"
                >
                  <div class="flex items-center">
                    <div
                      class="w-12 h-12 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-lg flex items-center justify-center shadow-lg"
                    >
                      <i class="fas fa-list text-white text-lg"></i>
                    </div>
                    <div class="ml-4">
                      <p class="text-sm font-semibold text-purple-200">Total</p>
                      <p class="text-2xl font-bold text-white">
                        {{ stats.total }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Filters and Search -->
              <div
                class="bg-gradient-to-r from-blue-600/25 to-teal-600/25 border-2 border-blue-400/40 p-6 rounded-2xl backdrop-blur-sm mb-8"
              >
                <div class="flex flex-col md:flex-row gap-4 items-center">
                  <div class="flex-1">
                    <div class="relative">
                      <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Search by borrower name, department, or device..."
                        class="w-full px-4 py-3 bg-white/15 border border-blue-300/30 rounded-lg focus:border-teal-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 text-sm"
                      />
                      <div
                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-teal-300/50"
                      >
                        <i class="fas fa-search text-sm"></i>
                      </div>
                    </div>
                  </div>

                  <div class="flex gap-4">
                    <select
                      v-model="statusFilter"
                      class="px-4 py-3 bg-white/15 border border-blue-300/30 rounded-lg focus:border-teal-400 focus:outline-none text-white backdrop-blur-sm transition-all appearance-none cursor-pointer text-sm"
                    >
                      <option value="">All Status</option>
                      <option value="pending">Pending</option>
                      <option value="returned">Returned</option>
                      <option value="compromised">Compromised</option>
                    </select>

                    <button
                      @click="refreshRequests"
                      class="px-6 py-3 bg-gradient-to-r from-teal-600 to-blue-600 text-white rounded-lg hover:from-teal-700 hover:to-blue-700 transition-all duration-300 font-semibold flex items-center shadow-lg hover:shadow-xl transform hover:scale-105 text-sm"
                    >
                      <i class="fas fa-sync-alt mr-2"></i>
                      Refresh
                    </button>
                  </div>
                </div>
              </div>

              <!-- Requests Table -->
              <div
                class="bg-gradient-to-r from-blue-600/25 to-teal-600/25 border-2 border-blue-400/40 rounded-2xl backdrop-blur-sm overflow-hidden"
              >
                <div class="p-6 border-b border-blue-300/30">
                  <h3 class="text-xl font-bold text-white flex items-center">
                    <i class="fas fa-table mr-3 text-teal-300"></i>
                    Device Borrowing Requests
                  </h3>
                </div>

                <div class="overflow-x-auto">
                  <table class="w-full">
                    <thead class="bg-blue-800/50">
                      <tr>
                        <th
                          class="px-6 py-4 text-left text-sm font-semibold text-blue-100 uppercase tracking-wider"
                        >
                          <i class="fas fa-user mr-2"></i>Borrower
                        </th>
                        <th
                          class="px-6 py-4 text-left text-sm font-semibold text-blue-100 uppercase tracking-wider"
                        >
                          <i class="fas fa-building mr-2"></i>Department
                        </th>
                        <th
                          class="px-6 py-4 text-left text-sm font-semibold text-blue-100 uppercase tracking-wider"
                        >
                          <i class="fas fa-laptop mr-2"></i>Device
                        </th>
                        <th
                          class="px-6 py-4 text-left text-sm font-semibold text-blue-100 uppercase tracking-wider"
                        >
                          <i class="fas fa-calendar mr-2"></i>Booking Date
                        </th>
                        <th
                          class="px-6 py-4 text-left text-sm font-semibold text-blue-100 uppercase tracking-wider"
                        >
                          <i class="fas fa-signature mr-2"></i>Signature
                        </th>
                        <th
                          class="px-6 py-4 text-left text-sm font-semibold text-blue-100 uppercase tracking-wider"
                        >
                          <i class="fas fa-flag mr-2"></i>Status
                        </th>
                        <th
                          class="px-6 py-4 text-center text-sm font-semibold text-blue-100 uppercase tracking-wider"
                        >
                          <i class="fas fa-cogs mr-2"></i>Actions
                        </th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-blue-300/20">
                      <tr
                        v-for="request in filteredRequests"
                        :key="request.id"
                        class="hover:bg-blue-700/30 transition-colors duration-200"
                      >
                        <td class="px-6 py-4 whitespace-nowrap">
                          <div class="flex items-center">
                            <div
                              class="w-10 h-10 bg-gradient-to-br from-teal-500 to-blue-600 rounded-full flex items-center justify-center shadow-lg mr-3"
                            >
                              <i class="fas fa-user text-white text-sm"></i>
                            </div>
                            <div>
                              <div class="text-sm font-medium text-white">
                                {{ request.borrowerName }}
                              </div>
                              <div class="text-sm text-blue-300">
                                {{ request.phoneNumber }}
                              </div>
                            </div>
                          </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                          <div class="text-sm text-white">
                            {{ request.department }}
                          </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                          <div class="flex items-center">
                            <i class="fas fa-laptop text-teal-300 mr-2"></i>
                            <span class="text-sm text-white">{{
                              getDeviceDisplayName(
                                request.deviceType,
                                request.customDevice
                              )
                            }}</span>
                          </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                          <div class="text-sm text-white">
                            {{ formatDate(request.bookingDate) }}
                          </div>
                          <div class="text-xs text-blue-300">
                            Return: {{ formatDate(request.collectionDate) }}
                          </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                          <div class="flex items-center">
                            <div
                              v-if="request.signature"
                              class="flex items-center text-green-400"
                            >
                              <i class="fas fa-check-circle mr-2"></i>
                              <span class="text-sm font-medium">Signed</span>
                            </div>
                            <div v-else class="flex items-center text-red-400">
                              <i class="fas fa-times-circle mr-2"></i>
                              <span class="text-sm font-medium"
                                >Not Signed</span
                              >
                            </div>
                          </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                          <span
                            :class="getStatusBadgeClass(request.status)"
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium"
                          >
                            <i
                              :class="getStatusIcon(request.status)"
                              class="mr-1"
                            ></i>
                            {{ getStatusText(request.status) }}
                          </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                          <div class="relative inline-block">
                            <!-- Actions Dropdown Button -->
                            <button
                              @click="toggleActionsDropdown(request.id)"
                              class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white text-sm font-medium rounded-lg hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105"
                            >
                              <i class="fas fa-eye mr-2"></i>
                              Actions
                              <i
                                class="fas fa-chevron-down ml-2 text-xs transition-transform duration-200"
                                :class="{
                                  'rotate-180': openDropdown === request.id,
                                }"
                              ></i>
                            </button>

                            <!-- Actions Dropdown Menu -->
                            <transition
                              enter-active-class="transition ease-out duration-200"
                              enter-from-class="opacity-0 scale-95 translate-y-1"
                              enter-to-class="opacity-100 scale-100 translate-y-0"
                              leave-active-class="transition ease-in duration-150"
                              leave-from-class="opacity-100 scale-100 translate-y-0"
                              leave-to-class="opacity-0 scale-95 translate-y-1"
                            >
                              <div
                                v-if="openDropdown === request.id"
                                class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border border-gray-200 z-50 overflow-hidden"
                                style="
                                  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1),
                                    0 10px 10px -5px rgba(0, 0, 0, 0.04);
                                "
                              >
                                <!-- View Action -->
                                <router-link
                                  :to="`/ict-approval/request/${request.id}`"
                                  @click="closeDropdown"
                                  class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors duration-200 group"
                                >
                                  <div
                                    class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3 group-hover:bg-blue-200 transition-colors duration-200"
                                  >
                                    <i
                                      class="fas fa-eye text-blue-600 text-sm"
                                    ></i>
                                  </div>
                                  <div class="flex-1">
                                    <p class="font-medium">View Details</p>
                                    <p class="text-xs text-gray-500">
                                      Review request information
                                    </p>
                                  </div>
                                </router-link>

                                <!-- Divider -->
                                <div class="border-t border-gray-100"></div>

                                <!-- Cancel Action -->
                                <button
                                  @click="cancelRequest(request.id)"
                                  class="w-full flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-yellow-50 hover:text-yellow-700 transition-colors duration-200 group"
                                  :disabled="request.status === 'cancelled'"
                                  :class="{
                                    'opacity-50 cursor-not-allowed':
                                      request.status === 'cancelled',
                                  }"
                                >
                                  <div
                                    class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center mr-3 group-hover:bg-yellow-200 transition-colors duration-200"
                                  >
                                    <i
                                      class="fas fa-ban text-yellow-600 text-sm"
                                    ></i>
                                  </div>
                                  <div class="flex-1 text-left">
                                    <p class="font-medium">Cancel Request</p>
                                    <p class="text-xs text-gray-500">
                                      Cancel this request
                                    </p>
                                  </div>
                                </button>

                                <!-- Divider -->
                                <div class="border-t border-gray-100"></div>

                                <!-- Delete Action -->
                                <button
                                  @click="deleteRequest(request.id)"
                                  class="w-full flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-red-50 hover:text-red-700 transition-colors duration-200 group"
                                >
                                  <div
                                    class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center mr-3 group-hover:bg-red-200 transition-colors duration-200"
                                  >
                                    <i
                                      class="fas fa-trash text-red-600 text-sm"
                                    ></i>
                                  </div>
                                  <div class="flex-1 text-left">
                                    <p class="font-medium">Delete Request</p>
                                    <p class="text-xs text-gray-500">
                                      Permanently remove
                                    </p>
                                  </div>
                                </button>
                              </div>
                            </transition>
                          </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>

                  <!-- Empty State -->
                  <div
                    v-if="filteredRequests.length === 0"
                    class="text-center py-12"
                  >
                    <div
                      class="w-24 h-24 bg-gradient-to-br from-blue-500/20 to-teal-500/20 rounded-full flex items-center justify-center mx-auto mb-4"
                    >
                      <i class="fas fa-inbox text-blue-300 text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-white mb-2">
                      No requests found
                    </h3>
                    <p class="text-blue-300">
                      {{
                        searchQuery || statusFilter
                          ? "Try adjusting your filters"
                          : "No device borrowing requests have been submitted yet."
                      }}
                    </p>
                  </div>
                </div>

                <!-- Pagination -->
                <div
                  v-if="filteredRequests.length > 0"
                  class="px-6 py-4 border-t border-blue-300/30 flex items-center justify-between"
                >
                  <div class="text-sm text-blue-300">
                    Showing {{ filteredRequests.length }} of
                    {{ requests.length }} requests
                  </div>
                  <div class="flex space-x-2">
                    <!-- Add pagination controls here if needed -->
                  </div>
                </div>
              </div>

              <!-- Footer -->
              <AppFooter />
            </div>
          </div>
        </div>
      </main>
    </div>

    <!-- Loading Modal -->
    <div
      v-if="isLoading"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
      <div class="bg-white rounded-xl shadow-2xl p-8 text-center">
        <div
          class="w-16 h-16 border-4 border-blue-600 border-t-transparent rounded-full animate-spin mx-auto mb-4"
        ></div>
        <p class="text-gray-600">Loading requests...</p>
      </div>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue'
import Header from '@/components/header.vue'
import ModernSidebar from '@/components/ModernSidebar.vue'
import AppFooter from '@/components/footer.vue'
import axios from 'axios'

export default {
  name: 'RequestsList',
  components: {
    Header,
    ModernSidebar,
    AppFooter
  },
  setup() {
    const sidebarCollapsed = ref(false)

    return {
      sidebarCollapsed
    }
  },
  data() {
    return {
      requests: [],
      searchQuery: '',
      statusFilter: '',
      isLoading: false,
      openDropdown: null,
      stats: {
        pending: 0,
        returned: 0,
        compromised: 0,
        total: 0
      }
    }
  },
  computed: {
    filteredRequests() {
      let filtered = this.requests

      // Filter by search query
      if (this.searchQuery) {
        const query = this.searchQuery.toLowerCase()
        filtered = filtered.filter(
          (request) =>
            request.borrowerName.toLowerCase().includes(query) ||
            request.department.toLowerCase().includes(query) ||
            this.getDeviceDisplayName(request.deviceType, request.customDevice)
              .toLowerCase()
              .includes(query)
        )
      }

      // Filter by status
      if (this.statusFilter) {
        filtered = filtered.filter(
          (request) => request.status === this.statusFilter
        )
      }

      return filtered
    }
  },
  async mounted() {
    await this.fetchRequests()
  },
  methods: {
    async fetchRequests() {
      this.isLoading = true
      try {
        // Fetch real data from API
        const response = await axios.get('/api/device-requests')
        this.requests = response.data
        this.calculateStats()
      } catch (error) {
        console.error('Error fetching requests:', error)
        // Show user-friendly error message
        alert('Unable to load requests. Please check your connection and try again.')
        // No mock data - use real API data only
        this.requests = []
        this.calculateStats()
      } finally {
        this.isLoading = false
      }
    },

    calculateStats() {
      this.stats = {
        pending: this.requests.filter((r) => r.status === 'pending').length,
        returned: this.requests.filter((r) => r.status === 'returned').length,
        compromised: this.requests.filter((r) => r.status === 'compromised')
          .length,
        total: this.requests.length
      }
    },

    async refreshRequests() {
      await this.fetchRequests()
    },

    viewRequest(requestId) {
      this.$router.push(`/ict-approval/request/${requestId}`)
    },

    getDeviceDisplayName(deviceType, customDevice) {
      if (deviceType === 'others') {
        return customDevice || 'Other Device'
      }

      const deviceNames = {
        projector: 'Projector',
        tv_remote: 'TV Remote',
        hdmi_cable: 'HDMI Cable',
        monitor: 'Monitor',
        cpu: 'CPU',
        keyboard: 'Keyboard',
        pc: 'PC',
        laptop: 'Laptop'
      }

      return deviceNames[deviceType] || deviceType
    },

    formatDate(dateString) {
      if (!dateString) return ''
      const date = new Date(dateString)
      return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      })
    },

    getStatusBadgeClass(status) {
      const classes = {
        pending: 'bg-yellow-100 text-yellow-800 border border-yellow-200',
        returned: 'bg-green-100 text-green-800 border border-green-200',
        compromised: 'bg-red-100 text-red-800 border border-red-200'
      }
      return (
        classes[status] || 'bg-gray-100 text-gray-800 border border-gray-200'
      )
    },

    getStatusIcon(status) {
      const icons = {
        pending: 'fas fa-clock',
        returned: 'fas fa-check-circle',
        compromised: 'fas fa-exclamation-triangle'
      }
      return icons[status] || 'fas fa-question-circle'
    },

    getStatusText(status) {
      const texts = {
        pending: 'Pending',
        returned: 'Returned',
        compromised: 'Compromised'
      }
      return texts[status] || 'Unknown'
    },

    // Dropdown methods
    toggleActionsDropdown(requestId) {
      this.openDropdown = this.openDropdown === requestId ? null : requestId
    },

    closeDropdown() {
      this.openDropdown = null
    },

    // Action methods
    async cancelRequest(requestId) {
      try {
        // Show confirmation dialog
        if (!confirm('Are you sure you want to cancel this request?')) {
          return
        }

        // Find the request and update its status
        const requestIndex = this.requests.findIndex(
          (req) => req.id === requestId
        )
        if (requestIndex !== -1) {
          this.requests[requestIndex].status = 'cancelled'
        }

        // Here you would typically make an API call to update the status
        // await axios.put(`/api/device-requests/${requestId}/cancel`)

        this.closeDropdown()
        this.calculateStats()

        // Show success message (you can implement a toast notification)
        console.log(`Request ${requestId} has been cancelled`)
      } catch (error) {
        console.error('Error cancelling request:', error)
        // Show error message
        alert('Error cancelling request. Please try again.')
      }
    },

    async deleteRequest(requestId) {
      try {
        // Show confirmation dialog
        if (
          !confirm(
            'Are you sure you want to delete this request? This action cannot be undone.'
          )
        ) {
          return
        }

        // Remove the request from the list
        this.requests = this.requests.filter((req) => req.id !== requestId)

        // Here you would typically make an API call to delete the request
        // await axios.delete(`/api/device-requests/${requestId}`)

        this.closeDropdown()
        this.calculateStats()

        // Show success message (you can implement a toast notification)
        console.log(`Request ${requestId} has been deleted`)
      } catch (error) {
        console.error('Error deleting request:', error)
        // Show error message
        alert('Error deleting request. Please try again.')
      }
    }
  }
}
</script>

<style scoped>
/* Glass morphism effects */
.booking-glass-card {
  background: rgba(59, 130, 246, 0.15);
  backdrop-filter: blur(25px);
  -webkit-backdrop-filter: blur(25px);
  border: 2px solid rgba(96, 165, 250, 0.3);
  box-shadow: 0 8px 32px rgba(29, 78, 216, 0.4),
    inset 0 1px 0 rgba(255, 255, 255, 0.1);
}

/* Animations */
@keyframes float {
  0%,
  100% {
    transform: translateY(0px);
  }
  50% {
    transform: translateY(-15px);
  }
}

@keyframes fade-in {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes fade-in-delay {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes slide-up {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-float {
  animation: float 6s ease-in-out infinite;
}

.animate-fade-in {
  animation: fade-in 1s ease-out;
}

.animate-fade-in-delay {
  animation: fade-in-delay 1s ease-out 0.3s both;
}

.animate-slide-up {
  animation: slide-up 0.6s ease-out;
}

/* Focus styles for accessibility */
input:focus,
select:focus {
  box-shadow: 0 0 0 3px rgba(45, 212, 191, 0.1);
}

button:focus {
  box-shadow: 0 0 0 3px rgba(45, 212, 191, 0.3);
}

/* Smooth transitions */
* {
  transition-property: color, background-color, border-color,
    text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter,
    backdrop-filter;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 200ms;
}
</style>
