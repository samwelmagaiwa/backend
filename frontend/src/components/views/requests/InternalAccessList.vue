<template>
  <div class="flex flex-col h-screen">
    <Header />
    <div class="flex flex-1 overflow-hidden">
      <ModernSidebar />
      <main
        class="flex-1 p-6 bg-gradient-to-br from-blue-900 via-blue-800 to-teal-900 overflow-y-auto relative"
      >
        <!-- Background Pattern -->
        <div class="absolute inset-0 overflow-hidden">
          <div class="absolute inset-0 opacity-5">
            <div class="grid grid-cols-12 gap-8 h-full transform rotate-45">
              <div
                v-for="i in 48"
                :key="i"
                class="bg-white rounded-full w-2 h-2 animate-pulse"
                :style="{ animationDelay: i * 0.1 + 's' }"
              ></div>
            </div>
          </div>
          <div class="absolute inset-0">
            <div
              v-for="i in 15"
              :key="i"
              class="absolute text-white opacity-10 animate-float"
              :style="{
                left: Math.random() * 100 + '%',
                top: Math.random() * 100 + '%',
                animationDelay: Math.random() * 3 + 's',
                animationDuration: Math.random() * 3 + 2 + 's',
                fontSize: Math.random() * 20 + 10 + 'px'
              }"
            >
              <i
                :class="[
                  'fas',
                  ['fa-clipboard-list', 'fa-tasks', 'fa-check-circle', 'fa-clock', 'fa-user-check'][
                    Math.floor(Math.random() * 5)
                  ]
                ]"
              ></i>
            </div>
          </div>
        </div>

        <div class="max-w-full mx-auto relative z-10">
          <!-- Header Section -->
          <div class="medical-glass-card rounded-t-3xl p-6 mb-0 border-b border-blue-300/30">
            <div class="flex justify-between items-center">
              <!-- Left Logo -->
              <div
                class="w-28 h-28 mr-6 transform hover:scale-110 transition-transform duration-300"
              >
                <div
                  class="w-full h-full bg-gradient-to-br from-blue-500/20 to-teal-500/20 rounded-2xl backdrop-blur-sm border-2 border-blue-300/40 flex items-center justify-center shadow-2xl hover:shadow-blue-500/25"
                >
                  <img
                    src="/assets/images/ngao2.png"
                    alt="National Shield"
                    class="max-w-18 max-h-18 object-contain"
                  />
                </div>
              </div>

              <!-- Center Content -->
              <div class="text-center flex-1">
                <h1
                  class="text-3xl font-bold text-white mb-4 tracking-wide drop-shadow-lg animate-fade-in"
                >
                  MUHIMBILI NATIONAL HOSPITAL
                </h1>
                <div class="relative inline-block mb-4">
                  <div
                    class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-8 py-3 rounded-full text-lg font-bold shadow-2xl transform hover:scale-105 transition-all duration-300 border-2 border-blue-400/60"
                  >
                    <span class="relative z-10 flex items-center gap-2">
                      <i class="fas fa-clipboard-list"></i>
                      REQUESTS DASHBOARD
                    </span>
                    <div
                      class="absolute inset-0 bg-gradient-to-r from-blue-700 to-blue-800 rounded-full opacity-0 hover:opacity-100 transition-opacity duration-300"
                    ></div>
                  </div>
                </div>
                <h2
                  class="text-lg font-bold text-blue-100 tracking-wide drop-shadow-md animate-fade-in-delay"
                >
                  {{ getRoleDisplayName(userRole) }} - {{ getApprovalStage() }}
                </h2>
              </div>

              <!-- Right Logo -->
              <div
                class="w-28 h-28 ml-6 transform hover:scale-110 transition-transform duration-300"
              >
                <div
                  class="w-full h-full bg-gradient-to-br from-teal-500/20 to-blue-500/20 rounded-2xl backdrop-blur-sm border-2 border-teal-300/40 flex items-center justify-center shadow-2xl hover:shadow-teal-500/25"
                >
                  <img
                    src="/assets/images/logo2.png"
                    alt="Muhimbili Logo"
                    class="max-w-18 max-h-18 object-contain"
                  />
                </div>
              </div>
            </div>
          </div>

          <!-- Main Content -->
          <div class="medical-glass-card rounded-b-3xl overflow-hidden">
            <div class="p-6">
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
                <!-- Last Refresh Indicator -->
                <div
                  v-if="lastRefreshTime"
                  class="flex items-center justify-between mb-4 pb-4 border-b border-blue-300/20"
                >
                  <div class="flex items-center text-sm text-blue-200">
                    <i class="fas fa-clock mr-2 text-blue-300"></i>
                    Last refreshed: {{ lastRefreshTime.toLocaleTimeString() }}
                  </div>
                  <div class="flex items-center text-xs text-blue-300">
                    <div class="w-2 h-2 bg-green-400 rounded-full mr-2 animate-pulse"></div>
                    Auto-refresh: 30s
                  </div>
                </div>

                <div class="flex flex-col md:flex-row gap-4 items-center">
                  <div class="flex-1">
                    <div class="relative">
                      <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Search by PF Number, Staff Name, Department..."
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
                      @change="onStatusFilterChange"
                      class="px-4 py-3 bg-white/15 border border-blue-300/30 rounded-lg focus:border-teal-400 focus:outline-none text-white backdrop-blur-sm transition-all appearance-none cursor-pointer text-sm"
                    >
                      <option value="" class="bg-blue-800 text-white">All Status</option>
                      <option value="pending" class="bg-blue-800 text-white">Pending</option>
                      <option value="approved" class="bg-blue-800 text-white">Approved</option>
                      <option value="rejected" class="bg-blue-800 text-white">Rejected</option>
                      <option value="in_progress" class="bg-blue-800 text-white">
                        In Progress
                      </option>
                    </select>

                    <select
                      v-model="typeFilter"
                      @change="onTypeFilterChange"
                      class="px-4 py-3 bg-white/15 border border-blue-300/30 rounded-lg focus:border-teal-400 focus:outline-none text-white backdrop-blur-sm transition-all appearance-none cursor-pointer text-sm"
                    >
                      <option value="" class="bg-blue-800 text-white">All Types</option>
                      <option value="jeeva" class="bg-blue-800 text-white">Jeeva Access</option>
                      <option value="wellsoft" class="bg-blue-800 text-white">
                        Wellsoft Access
                      </option>
                      <option value="internet" class="bg-blue-800 text-white">
                        Internet Access
                      </option>
                      <option value="combined" class="bg-blue-800 text-white">
                        Combined Services
                      </option>
                      <option value="both_service" class="bg-blue-800 text-white">
                        Both Service Form
                      </option>
                    </select>

                    <button
                      @click="refreshRequests"
                      :disabled="isRefreshing"
                      class="px-6 py-3 bg-gradient-to-r from-teal-600 to-blue-600 text-white rounded-lg hover:from-teal-700 hover:to-blue-700 transition-all duration-300 font-semibold flex items-center shadow-lg hover:shadow-xl transform hover:scale-105 text-sm disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                    >
                      <i
                        :class="[
                          'mr-2',
                          isRefreshing ? 'fas fa-spinner fa-spin' : 'fas fa-sync-alt'
                        ]"
                      ></i>
                      {{ isRefreshing ? 'Refreshing...' : 'Refresh' }}
                    </button>

                    <button
                      @click="clearAllFilters"
                      class="px-4 py-3 bg-gradient-to-r from-gray-600 to-gray-700 text-white rounded-lg hover:from-gray-700 hover:to-gray-800 transition-all duration-300 font-semibold flex items-center shadow-lg hover:shadow-xl transform hover:scale-105 text-sm"
                    >
                      <i class="fas fa-times mr-2"></i>
                      Clear
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
                    Access Requests - {{ getApprovalStage() }}
                  </h3>
                  <p class="text-sm text-blue-300 mt-2">
                    <i class="fas fa-info-circle mr-2"></i>
                    Staff requests displayed in FIFO order. Click "View & Process" to capture:
                    Module Requested for, Module Request, Access Rights, and Comments.
                  </p>
                </div>

                <div class="overflow-x-auto">
                  <table class="w-full">
                    <thead class="bg-blue-800/50">
                      <tr>
                        <th
                          class="px-4 py-3 text-left text-xs font-semibold text-blue-100 uppercase tracking-wider cursor-pointer hover:bg-blue-700/30"
                          @click="sortBy('id')"
                        >
                          <i class="fas fa-hashtag mr-1"></i>Request ID
                          <i
                            v-if="sortField === 'id'"
                            :class="[
                              'fas ml-1',
                              sortOrder === 'asc' ? 'fa-sort-up' : 'fa-sort-down'
                            ]"
                          ></i>
                        </th>
                        <th
                          class="px-4 py-3 text-left text-xs font-semibold text-blue-100 uppercase tracking-wider cursor-pointer hover:bg-blue-700/30"
                          @click="sortBy('type')"
                        >
                          <i class="fas fa-layer-group mr-1"></i>Request Type
                          <i
                            v-if="sortField === 'type'"
                            :class="[
                              'fas ml-1',
                              sortOrder === 'asc' ? 'fa-sort-up' : 'fa-sort-down'
                            ]"
                          ></i>
                        </th>
                        <th
                          class="px-4 py-3 text-left text-xs font-semibold text-blue-100 uppercase tracking-wider"
                        >
                          <i class="fas fa-user mr-1"></i>Personal Information
                        </th>
                        <th
                          class="px-4 py-3 text-left text-xs font-semibold text-blue-100 uppercase tracking-wider cursor-pointer hover:bg-blue-700/30"
                          @click="sortBy('submissionDate')"
                        >
                          <i class="fas fa-calendar mr-1"></i>Submission Date (FIFO)
                          <i
                            v-if="sortField === 'submissionDate'"
                            :class="[
                              'fas ml-1',
                              sortOrder === 'asc' ? 'fa-sort-up' : 'fa-sort-down'
                            ]"
                          ></i>
                        </th>
                        <th
                          class="px-4 py-3 text-left text-xs font-semibold text-blue-100 uppercase tracking-wider"
                        >
                          <i class="fas fa-flag mr-1"></i>Current Status
                        </th>
                        <th
                          class="px-4 py-3 text-center text-xs font-semibold text-blue-100 uppercase tracking-wider"
                        >
                          <i class="fas fa-tools mr-1"></i>Actions
                        </th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-blue-300/20">
                      <tr
                        v-for="request in paginatedRequests"
                        :key="request.id"
                        class="hover:bg-blue-700/30 transition-colors duration-200 cursor-pointer"
                        @click="viewRequest(request)"
                      >
                        <!-- Request ID -->
                        <td class="px-4 py-3 whitespace-nowrap">
                          <div class="text-sm font-medium text-white">#{{ request.id }}</div>
                          <div class="text-xs text-blue-300">
                            {{ getRequestPriority(request) }}
                          </div>
                        </td>

                        <!-- Request Type -->
                        <td class="px-4 py-3 whitespace-nowrap">
                          <div class="flex items-center">
                            <div
                              :class="getTypeIconClass(request.type)"
                              class="w-8 h-8 rounded-lg flex items-center justify-center mr-3"
                            >
                              <i :class="getTypeIcon(request.type)" class="text-white text-sm"></i>
                            </div>
                            <div class="text-sm text-white">
                              <div class="font-medium">
                                {{ getRequestTypeCode(request.type) }}
                              </div>
                              <div class="text-blue-300 text-xs">
                                {{ getTypeDisplayName(request.type) }}
                              </div>
                            </div>
                          </div>
                        </td>

                        <!-- Personal Information -->
                        <td class="px-4 py-3">
                          <div class="text-sm text-white">
                            <div class="font-medium">
                              {{ request.staffName }}
                            </div>
                            <div class="text-blue-300 text-xs">PF: {{ request.pfNumber }}</div>
                            <div class="text-blue-300 text-xs">
                              {{ request.department }}
                            </div>
                            <div class="flex items-center mt-1">
                              <i
                                :class="
                                  request.digitalSignature
                                    ? 'fas fa-check-circle text-green-400'
                                    : 'fas fa-times-circle text-red-400'
                                "
                                class="mr-1 text-xs"
                              ></i>
                              <span class="text-xs">{{
                                request.digitalSignature ? 'Signed' : 'Not Signed'
                              }}</span>
                            </div>
                          </div>
                        </td>

                        <!-- Submission Date (FIFO) -->
                        <td class="px-4 py-3 whitespace-nowrap">
                          <div class="text-sm text-white font-medium">
                            {{ formatDate(request.submissionDate) }}
                          </div>
                          <div class="text-xs text-blue-300">
                            {{ getTimeAgo(request.submissionDate) }}
                          </div>
                          <div class="text-xs text-yellow-300 font-medium">
                            FIFO: {{ getFIFOPosition(request) }}
                          </div>
                        </td>

                        <!-- Current Status -->
                        <td class="px-4 py-3 whitespace-nowrap">
                          <span
                            :class="getStatusBadgeClass(request.currentStatus)"
                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                          >
                            <i :class="getStatusIcon(request.currentStatus)" class="mr-2"></i>
                            {{ getStatusText(request.currentStatus) }}
                          </span>
                          <div class="text-xs text-blue-300 mt-1">
                            {{ getCurrentApprovalStage(request) }}
                          </div>
                        </td>

                        <!-- Actions -->
                        <td class="px-4 py-3 whitespace-nowrap text-center">
                          <div class="flex justify-center space-x-2">
                            <button
                              @click.stop="viewRequest(request)"
                              :disabled="isNavigating"
                              class="inline-flex items-center px-3 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white text-sm font-medium rounded-lg hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 transition duration-200 shadow-lg hover:shadow-xl transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                            >
                              <i v-if="!isNavigating" class="fas fa-eye mr-2"></i>
                              <i v-else class="fas fa-spinner fa-spin mr-2"></i>
                              {{ isNavigating ? 'Loading...' : 'View & Process' }}
                            </button>
                          </div>
                          <div class="text-xs text-blue-300 mt-1">
                            Click to capture: Module Requested for, Module Request, Access Rights,
                            Comments
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
                        searchQuery || statusFilter || typeFilter
                          ? 'Try adjusting your filters'
                          : 'No requests are pending your approval.'
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
                    Showing {{ (currentPage - 1) * itemsPerPage + 1 }} to
                    {{ Math.min(currentPage * itemsPerPage, filteredRequests.length) }}
                    of {{ filteredRequests.length }} requests
                  </div>
                  <div class="flex space-x-2">
                    <button
                      @click="currentPage = Math.max(1, currentPage - 1)"
                      :disabled="currentPage === 1"
                      class="px-3 py-1 bg-blue-600 text-white rounded-lg disabled:opacity-50 disabled:cursor-not-allowed hover:bg-blue-700 transition-colors"
                    >
                      <i class="fas fa-chevron-left"></i>
                    </button>
                    <span class="px-3 py-1 bg-blue-700 text-white rounded-lg">
                      {{ currentPage }} / {{ totalPages }}
                    </span>
                    <button
                      @click="currentPage = Math.min(totalPages, currentPage + 1)"
                      :disabled="currentPage === totalPages"
                      class="px-3 py-1 bg-blue-600 text-white rounded-lg disabled:opacity-50 disabled:cursor-not-allowed hover:bg-blue-700 transition-colors"
                    >
                      <i class="fas fa-chevron-right"></i>
                    </button>
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

    <!-- Navigation Loading Modal -->
    <div
      v-if="isNavigating"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
      <div class="bg-white rounded-xl shadow-2xl p-8 text-center">
        <div
          class="w-16 h-16 border-4 border-green-600 border-t-transparent rounded-full animate-spin mx-auto mb-4"
        ></div>
        <p class="text-gray-600">Opening request form...</p>
        <p class="text-sm text-gray-500 mt-2">Please wait while we load the form</p>
      </div>
    </div>
  </div>
</template>

<script>
  import { ref, computed, onMounted } from 'vue'
  import { useRouter } from 'vue-router'
  import Header from '@/components/header.vue'
  import ModernSidebar from '@/components/ModernSidebar.vue'
  import AppFooter from '@/components/footer.vue'
  import { useAuth } from '@/composables/useAuth'
  import personalInfoService from '@/services/personalInfoService'
  // Temporarily commented out to fix blank page issue
  // import bothServiceFormService from '@/services/bothServiceFormService.js'

  export default {
    name: 'HODDashboardRequestList',
    components: {
      Header,
      ModernSidebar,
      AppFooter
    },
    setup() {
      const router = useRouter()
      const { userRole, ROLES, getRoleDisplayName } = useAuth()

      // Reactive data
      // Sidebar state now managed by Pinia - no local state needed
      const sidebarCollapsed = ref(false)
      const requests = ref([])
      const isLoading = ref(true)
      const isNavigating = ref(false)
      const isRefreshing = ref(false)
      const searchQuery = ref('')
      const statusFilter = ref('')
      const typeFilter = ref('')
      const sortField = ref('submissionDate')
      const sortOrder = ref('asc') // FIFO: oldest first
      const currentPage = ref(1)
      const itemsPerPage = ref(10)
      const lastRefreshTime = ref(null)

      // Real data will be loaded from API

      // Computed properties
      const filteredRequests = computed(() => {
        let filtered = requests.value

        // Filter by role-specific approval stage
        filtered = filtered.filter((request) => {
          switch (userRole.value) {
            case ROLES.HEAD_OF_DEPARTMENT:
              return request.hodApprovalStatus === 'pending'
            case ROLES.DIVISIONAL_DIRECTOR:
              return (
                request.hodApprovalStatus === 'approved' && request.divisionalStatus === 'pending'
              )
            case ROLES.ICT_DIRECTOR:
              return request.divisionalStatus === 'approved' && request.dictStatus === 'pending'

            case ROLES.ICT_OFFICER:
              return request.headOfItStatus === 'approved' && request.ictStatus === 'pending'
            default:
              return true
          }
        })

        // Filter by search query
        if (searchQuery.value) {
          const query = searchQuery.value.toLowerCase()
          filtered = filtered.filter(
            (request) =>
              request.staffName.toLowerCase().includes(query) ||
              request.pfNumber.toLowerCase().includes(query) ||
              request.department.toLowerCase().includes(query) ||
              request.id.toLowerCase().includes(query)
          )
        }

        // Filter by status
        if (statusFilter.value) {
          filtered = filtered.filter((request) => request.currentStatus === statusFilter.value)
        }

        // Filter by type
        if (typeFilter.value) {
          filtered = filtered.filter((request) => request.type === typeFilter.value)
        }

        // Sort
        filtered.sort((a, b) => {
          let aVal = a[sortField.value]
          let bVal = b[sortField.value]

          if (sortField.value === 'submissionDate') {
            aVal = new Date(aVal)
            bVal = new Date(bVal)
          }

          if (sortOrder.value === 'asc') {
            return aVal > bVal ? 1 : -1
          } else {
            return aVal < bVal ? 1 : -1
          }
        })

        return filtered
      })

      const paginatedRequests = computed(() => {
        const start = (currentPage.value - 1) * itemsPerPage.value
        const end = start + itemsPerPage.value
        return filteredRequests.value.slice(start, end)
      })

      const totalPages = computed(() => {
        return Math.ceil(filteredRequests.value.length / itemsPerPage.value)
      })

      const stats = computed(() => {
        const roleFilteredRequests = requests.value.filter((request) => {
          switch (userRole.value) {
            case ROLES.HEAD_OF_DEPARTMENT:
              return request.hodApprovalStatus === 'pending'
            case ROLES.DIVISIONAL_DIRECTOR:
              return (
                request.hodApprovalStatus === 'approved' && request.divisionalStatus === 'pending'
              )
            case ROLES.ICT_DIRECTOR:
              return request.divisionalStatus === 'approved' && request.dictStatus === 'pending'

            case ROLES.ICT_OFFICER:
              return request.headOfItStatus === 'approved' && request.ictStatus === 'pending'
            default:
              return true
          }
        })

        return {
          pending: roleFilteredRequests.filter((r) => r.currentStatus === 'pending').length,
          approved: roleFilteredRequests.filter((r) => r.currentStatus === 'approved').length,
          rejected: roleFilteredRequests.filter((r) => r.currentStatus === 'rejected').length,
          total: roleFilteredRequests.length
        }
      })

      // Methods
      const getApprovalStage = () => {
        switch (userRole.value) {
          case ROLES.HEAD_OF_DEPARTMENT:
            return 'HOD Approval Stage'
          case ROLES.DIVISIONAL_DIRECTOR:
            return 'Divisional Director Approval Stage'
          case ROLES.ICT_DIRECTOR:
            return 'DICT Approval Stage'

          case ROLES.ICT_OFFICER:
            return 'ICT Officer Final Approval Stage'
          default:
            return 'Approval Stage'
        }
      }

      const getTypeDisplayName = (type) => {
        const names = {
          jeeva: 'Jeeva Access',
          wellsoft: 'Wellsoft Access',
          internet: 'Internet Access',
          combined: 'Combined Services'
        }
        return names[type] || type
      }

      const getRequestTypeCode = (type) => {
        const codes = {
          jeeva: 'jeeva_access',
          wellsoft: 'wellsoft',
          internet: 'internet_access_request',
          combined: 'combined_services'
        }
        return codes[type] || type
      }

      const getModuleRequestedForClass = (moduleRequestedFor) => {
        const type = (moduleRequestedFor || 'use').toLowerCase()
        if (type === 'use') {
          return 'bg-green-100 text-green-800 border border-green-200'
        } else if (type === 'revoke') {
          return 'bg-red-100 text-red-800 border border-red-200'
        }
        return 'bg-blue-100 text-blue-800 border border-blue-200'
      }

      const getModuleRequestedForIcon = (moduleRequestedFor) => {
        const type = (moduleRequestedFor || 'use').toLowerCase()
        if (type === 'use') {
          return 'fas fa-plus-circle'
        } else if (type === 'revoke') {
          return 'fas fa-minus-circle'
        }
        return 'fas fa-circle'
      }

      const getModulesByType = (type, selectedModules) => {
        if (!selectedModules || selectedModules.length === 0) return 'N/A'

        // Limit display to first 2 modules with "..." if more
        if (selectedModules.length <= 2) {
          return selectedModules.join(', ')
        } else {
          return selectedModules.slice(0, 2).join(', ') + '...'
        }
      }

      const getApprovalStatusClass = (status, roleType) => {
        const isCurrentUserRole =
          (roleType === 'head_of_department' && userRole.value === ROLES.HEAD_OF_DEPARTMENT) ||
          (roleType === 'divisional_director' && userRole.value === ROLES.DIVISIONAL_DIRECTOR) ||
          (roleType === 'ict_director' && userRole.value === ROLES.ICT_DIRECTOR) ||
          (roleType === 'ict_officer' && userRole.value === ROLES.ICT_OFFICER)

        let baseClass = 'text-xs font-medium'

        if (status === 'approved') {
          baseClass += ' text-green-400'
        } else if (status === 'rejected') {
          baseClass += ' text-red-400'
        } else if (status === 'pending') {
          if (isCurrentUserRole) {
            baseClass += ' text-yellow-300 font-bold' // Highlight for current user
          } else {
            baseClass += ' text-yellow-400'
          }
        } else {
          baseClass += ' text-gray-400'
        }

        return baseClass
      }

      const getApprovalStatusText = (status) => {
        const texts = {
          pending: 'Pending',
          approved: '✓',
          rejected: '✗',
          '': '-'
        }
        return texts[status] || '-'
      }

      const getImplementationStatusClass = (status, roleType) => {
        const isCurrentUserRole =
          (roleType === 'hod_it' && userRole.value === ROLES.HOD_IT) ||
          (roleType === 'ict_officer' && userRole.value === ROLES.ICT_OFFICER)

        let baseClass = 'text-xs font-medium'

        if (status === 'approved') {
          baseClass += ' text-green-400'
        } else if (status === 'rejected') {
          baseClass += ' text-red-400'
        } else if (status === 'pending') {
          if (isCurrentUserRole) {
            baseClass += ' text-yellow-300 font-bold' // Highlight for current user
          } else {
            baseClass += ' text-yellow-400'
          }
        } else {
          baseClass += ' text-gray-400'
        }

        return baseClass
      }

      const getImplementationStatusText = (status) => {
        const texts = {
          pending: 'Pending',
          approved: '✓',
          rejected: '✗',
          '': '-'
        }
        return texts[status] || '-'
      }

      const canEditComments = (request) => {
        // HOD can edit comments when it's their turn to approve
        return (
          userRole.value === ROLES.HEAD_OF_DEPARTMENT && request.hodApprovalStatus === 'pending'
        )
      }

      const canReject = (request) => {
        // Users can reject requests at their approval stage
        return canApprove(request)
      }

      const rejectRequest = async (request) => {
        if (confirm(`Are you sure you want to reject request ${request.id}?`)) {
          try {
            // Here you would make an API call to reject the request
            // await api.rejectRequest(request.id, userRole.value)

            // For demo, just update the local state
            const index = requests.value.findIndex((r) => r.id === request.id)
            if (index !== -1) {
              // Update the appropriate approval status based on user role
              switch (userRole.value) {
                case ROLES.HEAD_OF_DEPARTMENT:
                  requests.value[index].hodApprovalStatus = 'rejected'
                  break
                case ROLES.DIVISIONAL_DIRECTOR:
                  requests.value[index].divisionalStatus = 'rejected'
                  break
                case ROLES.ICT_DIRECTOR:
                  requests.value[index].dictStatus = 'rejected'
                  break

                case ROLES.ICT_OFFICER:
                  requests.value[index].ictStatus = 'rejected'
                  break
              }
              requests.value[index].currentStatus = 'rejected'
            }

            console.log(`Request ${request.id} rejected by ${userRole.value}`)
          } catch (error) {
            console.error('Error rejecting request:', error)
            alert('Error rejecting request. Please try again.')
          }
        }
      }

      const getTypeIcon = (type) => {
        const icons = {
          jeeva: 'fas fa-database',
          wellsoft: 'fas fa-laptop-medical',
          internet: 'fas fa-wifi',
          combined: 'fas fa-layer-group'
        }
        return icons[type] || 'fas fa-file'
      }

      const getTypeIconClass = (type) => {
        const classes = {
          jeeva: 'bg-gradient-to-br from-purple-500 to-indigo-600',
          wellsoft: 'bg-gradient-to-br from-yellow-500 to-orange-600',
          internet: 'bg-gradient-to-br from-green-500 to-emerald-600',
          combined: 'bg-gradient-to-br from-blue-500 to-cyan-600'
        }
        return classes[type] || 'bg-gradient-to-br from-gray-500 to-gray-600'
      }

      const getStatusBadgeClass = (status) => {
        const classes = {
          pending: 'bg-yellow-100 text-yellow-800 border border-yellow-200',
          approved: 'bg-green-100 text-green-800 border border-green-200',
          rejected: 'bg-red-100 text-red-800 border border-red-200'
        }
        return classes[status] || 'bg-gray-100 text-gray-800 border border-gray-200'
      }

      const getStatusIcon = (status) => {
        const icons = {
          pending: 'fas fa-clock',
          approved: 'fas fa-check-circle',
          rejected: 'fas fa-times-circle'
        }
        return icons[status] || 'fas fa-question-circle'
      }

      const getStatusText = (status) => {
        const texts = {
          pending: 'Pending',
          approved: 'Approved',
          rejected: 'Rejected'
        }
        return texts[status] || 'Unknown'
      }

      const formatDate = (dateString) => {
        if (!dateString) return ''
        const date = new Date(dateString)
        return date.toLocaleDateString('en-US', {
          year: 'numeric',
          month: 'short',
          day: 'numeric'
        })
      }

      const sortBy = (field) => {
        if (sortField.value === field) {
          sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc'
        } else {
          sortField.value = field
          sortOrder.value = 'asc'
        }
      }

      const canApprove = (request) => {
        // Quick approve button only for pending requests at current user's stage
        return request.currentStatus === 'pending'
      }

      const viewRequest = async (request) => {
        isNavigating.value = true

        try {
          // Add small delay to show loading state
          await new Promise((resolve) => setTimeout(resolve, 200))

          // Navigate to internal-access/details with proper query parameters
          await router.push({
            path: '/internal-access/details',
            query: {
              id: request.id,
              type: request.type,
              userAccessId: request.userAccessId // Pass the real user access ID for API calls
            }
          })
        } catch (error) {
          console.error('Navigation error:', error)
          alert('Error opening request. Please try again.')
        } finally {
          isNavigating.value = false
        }
      }

      const approveRequest = async (request) => {
        if (confirm(`Are you sure you want to approve request ${request.id}?`)) {
          try {
            // Here you would make an API call to approve the request
            // await api.approveRequest(request.id, userRole.value)

            // For demo, just update the local state
            const index = requests.value.findIndex((r) => r.id === request.id)
            if (index !== -1) {
              // Update the appropriate approval status based on user role
              switch (userRole.value) {
                case ROLES.HEAD_OF_DEPARTMENT:
                  requests.value[index].hodApprovalStatus = 'approved'
                  break
                case ROLES.DIVISIONAL_DIRECTOR:
                  requests.value[index].divisionalStatus = 'approved'
                  break
                case ROLES.ICT_DIRECTOR:
                  requests.value[index].dictStatus = 'approved'
                  break

                case ROLES.ICT_OFFICER:
                  requests.value[index].ictStatus = 'approved'
                  requests.value[index].currentStatus = 'approved'
                  break
              }
            }

            console.log(`Request ${request.id} approved by ${userRole.value}`)
          } catch (error) {
            console.error('Error approving request:', error)
            alert('Error approving request. Please try again.')
          }
        }
      }

      const getRequestPriority = (request) => {
        const daysSinceSubmission = Math.floor(
          (new Date() - new Date(request.submissionDate)) / (1000 * 60 * 60 * 24)
        )
        if (daysSinceSubmission > 7) return 'High Priority'
        if (daysSinceSubmission > 3) return 'Medium Priority'
        return 'Normal Priority'
      }

      const getTimeAgo = (dateString) => {
        const now = new Date()
        const date = new Date(dateString)
        const diffInHours = Math.floor((now - date) / (1000 * 60 * 60))

        if (diffInHours < 1) return 'Just now'
        if (diffInHours < 24) return `${diffInHours}h ago`

        const diffInDays = Math.floor(diffInHours / 24)
        if (diffInDays < 7) return `${diffInDays}d ago`

        const diffInWeeks = Math.floor(diffInDays / 7)
        return `${diffInWeeks}w ago`
      }

      const getFIFOPosition = (request) => {
        // Sort all requests by submission date (oldest first) and find position
        const sortedRequests = [...requests.value].sort(
          (a, b) => new Date(a.submissionDate) - new Date(b.submissionDate)
        )
        const position = sortedRequests.findIndex((r) => r.id === request.id) + 1
        return `#${position}`
      }

      const getCurrentApprovalStage = (request) => {
        if (request.hodApprovalStatus === 'pending') return 'Awaiting HOD'
        if (request.divisionalStatus === 'pending') return 'Awaiting Divisional Director'
        if (request.dictStatus === 'pending') return 'Awaiting DICT'
        if (request.headOfItStatus === 'pending') return 'Awaiting Head of IT'
        if (request.ictStatus === 'pending') return 'Awaiting ICT Officer'
        return 'Completed'
      }

      const refreshRequests = async () => {
        isRefreshing.value = true
        try {
          console.log('🔄 Fetching real data from API...')

          // Fetch real data from the API
          const result = await personalInfoService.getUserAccessRequestsForHOD()

          if (result.success) {
            // Transform API data to match component structure
            requests.value = result.data.map((request) => {
              const requestTypes = Array.isArray(request.request_details.request_type)
                ? request.request_details.request_type
                : [request.request_details.request_type]

              // Determine the primary type for display
              const primaryType =
                requestTypes.length > 1
                  ? 'combined'
                  : requestTypes[0] === 'jeeva_access'
                    ? 'jeeva'
                    : requestTypes[0] === 'wellsoft'
                      ? 'wellsoft'
                      : requestTypes[0] === 'internet_access_request'
                        ? 'internet'
                        : 'combined'

              return {
                id: `REQ-${String(request.id).padStart(3, '0')}`,
                userAccessId: request.id, // Store the original ID for API calls
                type: primaryType,
                staffName: request.personal_information.staff_name,
                pfNumber: request.personal_information.pf_number,
                department: request.personal_information.department,
                digitalSignature: request.personal_information.signature.exists,
                moduleRequestedFor: 'use', // Default value
                selectedModules: requestTypes,
                accessType: 'Permanent (until retirement)', // Default value
                temporaryUntil: null,
                submissionDate: request.request_details.submission_date,
                currentStatus: request.request_details.status,
                hodApprovalStatus: 'pending', // Since these are for HOD approval
                divisionalStatus: 'pending',
                dictStatus: 'pending',
                headOfItStatus: 'pending',
                ictStatus: 'pending',
                daysPending: request.request_details.days_pending,
                contactNumber: request.personal_information.contact_number,
                purpose: request.request_details.purpose
              }
            })

            console.log('✅ Real data loaded successfully:', requests.value.length, 'requests')
            lastRefreshTime.value = new Date()
          } else {
            console.error('❌ Failed to fetch data:', result.error)
            // Show user-friendly error message
            alert('Failed to load requests: ' + result.error)
            requests.value = []
          }
        } catch (error) {
          console.error('❌ Error fetching requests:', error)
          // Show user-friendly error message
          alert('Error loading requests. Please try again.')
          requests.value = []
        } finally {
          isRefreshing.value = false
          if (isLoading.value) {
            isLoading.value = false
          }
        }
      }

      // Filter change handlers
      const onStatusFilterChange = () => {
        currentPage.value = 1 // Reset to first page when filter changes
        console.log('Status filter changed to:', statusFilter.value || 'All Status')
      }

      const onTypeFilterChange = () => {
        currentPage.value = 1 // Reset to first page when filter changes
        console.log('Type filter changed to:', typeFilter.value || 'All Types')
      }

      // Clear all filters
      const clearAllFilters = () => {
        searchQuery.value = ''
        statusFilter.value = ''
        typeFilter.value = ''
        currentPage.value = 1
        console.log('All filters cleared')
      }

      // Auto-refresh functionality
      const startAutoRefresh = () => {
        // Auto-refresh every 30 seconds
        setInterval(() => {
          if (!isRefreshing.value && !isLoading.value) {
            console.log('Auto-refreshing requests...')
            refreshRequests()
          }
        }, 30000)
      }

      // Load personal information from user_access table
      const loadPersonalInfoFromUserAccess = async (userAccessId) => {
        try {
          console.log('Loading personal info for user access ID:', userAccessId)
          const result = await personalInfoService.getPersonalInfoFromUserAccess(userAccessId)

          if (result.success) {
            console.log('Personal info loaded successfully:', result.data)
            return personalInfoService.transformPersonalInfoForForm(result.data)
          } else {
            console.error('Failed to load personal info:', result.error)
            throw new Error(result.error)
          }
        } catch (error) {
          console.error('Error loading personal info from user access:', error)
          throw error
        }
      }

      // Enhanced view request with personal info capture
      const viewRequestWithPersonalInfo = async (request) => {
        isNavigating.value = true

        try {
          // Add small delay to show loading state
          await new Promise((resolve) => setTimeout(resolve, 200))

          // If this is a user access request, load personal info first
          if (request.userAccessId) {
            try {
              const personalInfo = await loadPersonalInfoFromUserAccess(request.userAccessId)
              console.log('Personal info captured:', personalInfo)

              // Store personal info in session storage for the form
              sessionStorage.setItem('captured_personal_info', JSON.stringify(personalInfo))
            } catch (error) {
              console.warn('Could not load personal info, proceeding without it:', error)
            }
          }

          // Navigate to both-service-form with request ID for HOD review
          await router.push({
            path: '/both-service-form',
            query: {
              mode: 'hod-review',
              requestId: request.id,
              userAccessId: request.userAccessId || null
            }
          })
        } catch (error) {
          console.error('Navigation error:', error)
          alert('Error opening request. Please try again.')
        } finally {
          isNavigating.value = false
        }
      }

      // Lifecycle
      onMounted(async () => {
        await refreshRequests()
        startAutoRefresh()
      })

      return {
        sidebarCollapsed,
        requests,
        isLoading,
        isNavigating,
        isRefreshing,
        searchQuery,
        statusFilter,
        typeFilter,
        sortField,
        sortOrder,
        currentPage,
        itemsPerPage,
        lastRefreshTime,
        filteredRequests,
        paginatedRequests,
        totalPages,
        stats,
        userRole,
        ROLES,
        getRoleDisplayName,
        getApprovalStage,
        getTypeDisplayName,
        getRequestTypeCode,
        getModuleRequestedForClass,
        getModuleRequestedForIcon,
        getModulesByType,
        getApprovalStatusClass,
        getApprovalStatusText,
        getImplementationStatusClass,
        getImplementationStatusText,
        canEditComments,
        canReject,
        rejectRequest,
        getTypeIcon,
        getTypeIconClass,
        getStatusBadgeClass,
        getStatusIcon,
        getStatusText,
        formatDate,
        sortBy,
        canApprove,
        viewRequest,
        approveRequest,
        refreshRequests,
        // Filter methods
        onStatusFilterChange,
        onTypeFilterChange,
        clearAllFilters,
        // Personal info methods
        loadPersonalInfoFromUserAccess,
        viewRequestWithPersonalInfo,
        // New FIFO and priority methods
        getRequestPriority,
        getTimeAgo,
        getFIFOPosition,
        getCurrentApprovalStage
      }
    }
  }
</script>

<style scoped>
  /* Glass morphism effects */
  .medical-glass-card {
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

  .animate-float {
    animation: float 6s ease-in-out infinite;
  }

  .animate-fade-in {
    animation: fade-in 1s ease-out;
  }

  .animate-fade-in-delay {
    animation: fade-in-delay 1s ease-out 0.3s both;
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

  /* Table hover effects */
  tbody tr:hover {
    background-color: rgba(59, 130, 246, 0.1);
  }

  /* Custom scrollbar */
  ::-webkit-scrollbar {
    width: 8px;
  }

  ::-webkit-scrollbar-track {
    background: rgba(59, 130, 246, 0.1);
  }

  ::-webkit-scrollbar-thumb {
    background: rgba(59, 130, 246, 0.3);
    border-radius: 4px;
  }

  ::-webkit-scrollbar-thumb:hover {
    background: rgba(59, 130, 246, 0.5);
  }
</style>
