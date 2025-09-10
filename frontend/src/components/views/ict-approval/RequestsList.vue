<template>
  <div class="flex flex-col h-screen">
    <Header />
    <div class="flex flex-1 overflow-hidden">
      <ModernSidebar />
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
                fontSize: Math.random() * 15 + 8 + 'px'
              }"
            >
              <i
                :class="[
                  'fas',
                  ['fa-laptop', 'fa-tv', 'fa-desktop', 'fa-keyboard', 'fa-mouse', 'fa-headphones'][
                    Math.floor(Math.random() * 6)
                  ]
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
          <div class="booking-glass-card rounded-b-3xl overflow-hidden animate-slide-up">
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
                      <p class="text-sm font-semibold text-green-200">Approved</p>
                      <p class="text-2xl font-bold text-white">
                        {{ stats.approved }}
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
                      <i class="fas fa-times-circle text-white text-lg"></i>
                    </div>
                    <div class="ml-4">
                      <p class="text-sm font-semibold text-red-200">Rejected</p>
                      <p class="text-2xl font-bold text-white">
                        {{ stats.rejected }}
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
                      v-model="ictStatusFilter"
                      class="px-4 py-3 bg-white/15 border border-blue-300/30 rounded-lg focus:border-teal-400 focus:outline-none text-white backdrop-blur-sm transition-all appearance-none cursor-pointer text-sm"
                    >
                      <option value="">All ICT Status</option>
                      <option value="pending">Pending ICT</option>
                      <option value="approved">Approved</option>
                      <option value="rejected">Rejected</option>
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
                          <i class="fas fa-hashtag mr-2"></i>Request ID
                        </th>
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
                          class="px-6 py-4 text-left text-sm font-semibold text-blue-100 uppercase tracking-wider"
                        >
                          <i class="fas fa-undo mr-2"></i>Return Status
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
                              class="w-10 h-10 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-lg flex items-center justify-center shadow-lg mr-3"
                            >
                              <i class="fas fa-hashtag text-white text-sm"></i>
                            </div>
                            <div>
                              <div class="text-sm font-medium text-white">
                                {{
                                  request.request_id ||
                                  `REQ-${request.id.toString().padStart(6, '0')}`
                                }}
                              </div>
                              <div class="text-xs text-purple-300">ID: {{ request.id }}</div>
                            </div>
                          </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                          <div class="flex items-center">
                            <div
                              class="w-10 h-10 bg-gradient-to-br from-teal-500 to-blue-600 rounded-full flex items-center justify-center shadow-lg mr-3"
                            >
                              <i class="fas fa-user text-white text-sm"></i>
                            </div>
                            <div>
                              <div class="text-sm font-medium text-white">
                                {{
                                  request.borrower_name || request.borrowerName || 'Unknown User'
                                }}
                              </div>
                              <div class="text-sm text-blue-300">
                                {{ request.borrower_phone || request.phoneNumber || 'No phone' }}
                              </div>
                              <div v-if="request.pf_number" class="text-xs text-teal-300">
                                PF: {{ request.pf_number }}
                              </div>
                            </div>
                          </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                          <div class="text-sm text-white font-medium">
                            {{ request.department || 'Unknown Department' }}
                          </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                          <div class="flex items-center">
                            <i class="fas fa-laptop text-teal-300 mr-2"></i>
                            <span class="text-sm text-white font-medium">{{
                              request.device_name ||
                              getDeviceDisplayName(
                                request.device_type || request.deviceType,
                                request.custom_device || request.customDevice
                              )
                            }}</span>
                          </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                          <div class="text-sm text-white font-medium">
                            {{ formatDate(request.booking_date || request.bookingDate) }}
                          </div>
                          <div class="text-xs text-blue-300">
                            Return:
                            {{ formatDate(request.collection_date || request.collectionDate) }}
                          </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                          <div class="flex items-center">
                            <div
                              v-if="request.has_signature || request.signature"
                              class="flex items-center text-green-400"
                            >
                              <i class="fas fa-check-circle mr-2"></i>
                              <span class="text-sm font-medium">Signed</span>
                            </div>
                            <div v-else class="flex items-center text-red-400">
                              <i class="fas fa-times-circle mr-2"></i>
                              <span class="text-sm font-medium">Not Signed</span>
                            </div>
                          </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                          <span
                            :class="getStatusBadgeClass(request.ict_approve || 'pending')"
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium"
                          >
                            <i
                              :class="getStatusIcon(request.ict_approve || 'pending')"
                              class="mr-1"
                            ></i>
                            {{ getStatusText(request.ict_approve || 'pending') }}
                          </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                          <span
                            :class="
                              getReturnStatusBadgeClass(request.return_status || 'not_yet_returned')
                            "
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium"
                          >
                            <i
                              :class="
                                getReturnStatusIcon(request.return_status || 'not_yet_returned')
                              "
                              class="mr-1"
                            ></i>
                            {{ getReturnStatusText(request.return_status || 'not_yet_returned') }}
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
                                  'rotate-180': openDropdown === request.id
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
                                  box-shadow:
                                    0 10px 25px -5px rgba(0, 0, 0, 0.1),
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
                                    <i class="fas fa-eye text-blue-600 text-sm"></i>
                                  </div>
                                  <div class="flex-1">
                                    <p class="font-medium">View Details</p>
                                    <p class="text-xs text-gray-500">Review request information</p>
                                  </div>
                                </router-link>

                                <!-- Divider -->
                                <div class="border-t border-gray-100"></div>

                                <!-- Approve Action -->
                                <button
                                  @click="approveRequest(request.id)"
                                  class="w-full flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-green-50 hover:text-green-700 transition-colors duration-200 group"
                                  :disabled="request.ict_approve === 'approved'"
                                  :class="{
                                    'opacity-50 cursor-not-allowed':
                                      request.ict_approve === 'approved'
                                  }"
                                >
                                  <div
                                    class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3 group-hover:bg-green-200 transition-colors duration-200"
                                  >
                                    <i class="fas fa-check text-green-600 text-sm"></i>
                                  </div>
                                  <div class="flex-1 text-left">
                                    <p class="font-medium">Approve Request</p>
                                    <p class="text-xs text-gray-500">
                                      Grant device borrowing access
                                    </p>
                                  </div>
                                </button>

                                <!-- Divider -->
                                <div class="border-t border-gray-100"></div>

                                <!-- Reject Action -->
                                <button
                                  @click="rejectRequest(request.id)"
                                  class="w-full flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-red-50 hover:text-red-700 transition-colors duration-200 group"
                                  :disabled="request.ict_approve === 'rejected'"
                                  :class="{
                                    'opacity-50 cursor-not-allowed':
                                      request.ict_approve === 'rejected'
                                  }"
                                >
                                  <div
                                    class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center mr-3 group-hover:bg-red-200 transition-colors duration-200"
                                  >
                                    <i class="fas fa-times text-red-600 text-sm"></i>
                                  </div>
                                  <div class="flex-1 text-left">
                                    <p class="font-medium">Reject Request</p>
                                    <p class="text-xs text-gray-500">
                                      Deny device borrowing access
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
                  <div v-if="filteredRequests.length === 0" class="text-center py-12">
                    <div
                      class="w-24 h-24 bg-gradient-to-br from-blue-500/20 to-teal-500/20 rounded-full flex items-center justify-center mx-auto mb-4"
                    >
                      <i class="fas fa-inbox text-blue-300 text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-white mb-2">No requests found</h3>
                    <p class="text-blue-300">
                      {{
                        searchQuery || ictStatusFilter
                          ? 'Try adjusting your filters'
                          : 'No device borrowing requests have been submitted yet.'
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
                    Showing {{ filteredRequests.length }} of {{ requests.length }} requests
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
  import Header from '@/components/header.vue'
  import ModernSidebar from '@/components/ModernSidebar.vue'
  import AppFooter from '@/components/footer.vue'
  import deviceBorrowingService from '@/services/deviceBorrowingService'

  export default {
    name: 'RequestsList',
    components: {
      Header,
      ModernSidebar,
      AppFooter
    },
    setup() {
      // Sidebar state now managed by Pinia - no local state needed

      return {
        // No local state needed for sidebar
      }
    },
    data() {
      return {
        requests: [],
        searchQuery: '',
        ictStatusFilter: '',
        deviceTypeFilter: '',
        departmentFilter: '',
        isLoading: false,
        openDropdown: null,
        isProcessing: false,
        stats: {
          pending: 0,
          approved: 0,
          rejected: 0,
          returned: 0,
          compromised: 0,
          total: 0
        },
        showApprovalModal: false,
        approvalNotes: '',
        selectedRequestId: null,
        actionType: null, // 'approve' or 'reject'
        searchDebounce: null
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
              (request.borrower_name || request.borrowerName || '').toLowerCase().includes(query) ||
              (request.department || '').toLowerCase().includes(query) ||
              (
                request.device_name ||
                this.getDeviceDisplayName(
                  request.device_type || request.deviceType,
                  request.custom_device || request.customDevice
                )
              )
                .toLowerCase()
                .includes(query) ||
              (request.request_id || '').toLowerCase().includes(query)
          )
        }

        // Filter by ICT status (client-side filtering for better responsiveness)
        if (this.ictStatusFilter) {
          filtered = filtered.filter(
            (request) => (request.ict_approve || 'pending') === this.ictStatusFilter
          )
        }

        return filtered
      }
    },
    async mounted() {
      await this.fetchRequests()
    },
    watch: {
      // Watch for filter changes and refresh data
      ictStatusFilter: {
        handler() {
          this.fetchRequests()
        },
        immediate: false
      },
      searchQuery: {
        handler() {
          // Debounce search to avoid too many API calls
          clearTimeout(this.searchDebounce)
          this.searchDebounce = setTimeout(() => {
            this.fetchRequests()
          }, 300)
        },
        immediate: false
      }
    },
    beforeUnmount() {
      // Clean up debounce timer
      if (this.searchDebounce) {
        clearTimeout(this.searchDebounce)
      }
    },
    methods: {
      async fetchRequests() {
        this.isLoading = true
        try {
          // Fetch all device borrowing requests for ICT approval from booking_service table
          const response = await deviceBorrowingService.getAllRequests({
            search: this.searchQuery || undefined,
            device_type: this.deviceTypeFilter || undefined,
            department: this.departmentFilter || undefined,
            ict_status: this.ictStatusFilter || undefined,
            per_page: 50
          })

          if (response.success) {
            this.requests = response.data.data || []
            console.log('✅ Device borrowing requests loaded:', this.requests.length)

            // Also fetch statistics
            await this.fetchStatistics()
          } else {
            console.error('❌ Failed to fetch requests:', response.message)
            throw new Error(response.message || 'Failed to fetch requests')
          }
        } catch (error) {
          console.error('Error fetching requests:', error)
          alert(
            'Unable to load device borrowing requests. Please check your connection and try again.'
          )
          this.requests = []
          this.calculateStats()
        } finally {
          this.isLoading = false
        }
      },

      async fetchStatistics() {
        // For now, calculate stats from loaded requests
        // In future, we can add a dedicated API endpoint
        this.calculateStats()
      },

      calculateStats() {
        this.stats = {
          pending: this.requests.filter((r) => r.ict_approve === 'pending').length,
          approved: this.requests.filter((r) => r.ict_approve === 'approved').length,
          rejected: this.requests.filter((r) => r.ict_approve === 'rejected').length,
          returned: this.requests.filter((r) => r.status === 'returned').length,
          compromised: this.requests.filter((r) => r.status === 'compromised').length,
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
        return deviceBorrowingService.getDeviceDisplayName(deviceType, customDevice)
      },

      formatDate(dateString) {
        return deviceBorrowingService.formatDate(dateString)
      },

      getStatusBadgeClass(status) {
        return deviceBorrowingService.getStatusBadgeClass(status)
      },

      getStatusIcon(status) {
        return deviceBorrowingService.getStatusIcon(status)
      },

      getStatusText(status) {
        return deviceBorrowingService.getStatusText(status)
      },

      // Return status helpers
      getReturnStatusBadgeClass(status) {
        return deviceBorrowingService.getReturnStatusBadgeClass(status)
      },
      getReturnStatusIcon(status) {
        return deviceBorrowingService.getReturnStatusIcon(status)
      },
      getReturnStatusText(status) {
        return deviceBorrowingService.getReturnStatusText(status)
      },

      // Dropdown methods
      toggleActionsDropdown(requestId) {
        this.openDropdown = this.openDropdown === requestId ? null : requestId
      },

      closeDropdown() {
        this.openDropdown = null
      },

      // Action methods
      async approveRequest(requestId) {
        try {
          this.selectedRequestId = requestId
          this.actionType = 'approve'

          const confirmed = confirm(
            'Are you sure you want to approve this device borrowing request?'
          )
          if (!confirmed) return

          this.isProcessing = true
          this.closeDropdown()

          // Get optional notes from user
          const notes = prompt('Add approval notes (optional):') || ''

          const response = await deviceBorrowingService.approveRequest(requestId, notes)

          if (response.success) {
            // Update local request status
            const requestIndex = this.requests.findIndex((req) => req.id === requestId)
            if (requestIndex !== -1) {
              this.requests[requestIndex].ict_approve = 'approved'
              this.requests[requestIndex].ict_notes = notes
              this.requests[requestIndex].ict_approved_at = new Date().toISOString()
            }

            this.calculateStats()
            alert('Device borrowing request approved successfully!')
            console.log('✅ Request approved:', requestId)
          } else {
            throw new Error(response.message || 'Failed to approve request')
          }
        } catch (error) {
          console.error('❌ Error approving request:', error)
          alert('Error approving request: ' + error.message)
        } finally {
          this.isProcessing = false
          this.selectedRequestId = null
          this.actionType = null
        }
      },

      async rejectRequest(requestId) {
        try {
          this.selectedRequestId = requestId
          this.actionType = 'reject'

          const confirmed = confirm(
            'Are you sure you want to reject this device borrowing request?'
          )
          if (!confirmed) return

          // Get rejection reason (required)
          const reason = prompt('Please provide a rejection reason (required):')
          if (!reason || reason.trim() === '') {
            alert('Rejection reason is required')
            return
          }

          this.isProcessing = true
          this.closeDropdown()

          const response = await deviceBorrowingService.rejectRequest(requestId, reason)

          if (response.success) {
            // Update local request status
            const requestIndex = this.requests.findIndex((req) => req.id === requestId)
            if (requestIndex !== -1) {
              this.requests[requestIndex].ict_approve = 'rejected'
              this.requests[requestIndex].ict_notes = reason
              this.requests[requestIndex].ict_approved_at = new Date().toISOString()
            }

            this.calculateStats()
            alert('Device borrowing request rejected successfully!')
            console.log('✅ Request rejected:', requestId)
          } else {
            throw new Error(response.message || 'Failed to reject request')
          }
        } catch (error) {
          console.error('❌ Error rejecting request:', error)
          alert('Error rejecting request: ' + error.message)
        } finally {
          this.isProcessing = false
          this.selectedRequestId = null
          this.actionType = null
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
    box-shadow:
      0 8px 32px rgba(29, 78, 216, 0.4),
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
    transition-property:
      color, background-color, border-color, text-decoration-color, fill, stroke, opacity,
      box-shadow, transform, filter, backdrop-filter;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 200ms;
  }
</style>
