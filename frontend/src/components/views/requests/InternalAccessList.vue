<template>
  <div class="flex flex-col h-screen">
    <AppHeader />
    <div class="flex flex-1 overflow-hidden">
      <DynamicSidebar v-model:collapsed="sidebarCollapsed" />
      <main class="flex-1 p-6 bg-gradient-to-br from-blue-900 via-blue-800 to-teal-900 overflow-y-auto relative">
        <!-- Background Pattern -->
        <div class="absolute inset-0 overflow-hidden">
          <div class="absolute inset-0 opacity-5">
            <div class="grid grid-cols-12 gap-8 h-full transform rotate-45">
              <div v-for="i in 48" :key="i" class="bg-white rounded-full w-2 h-2 animate-pulse" :style="{animationDelay: (i * 0.1) + 's'}"></div>
            </div>
          </div>
          <div class="absolute inset-0">
            <div v-for="i in 15" :key="i" 
                 class="absolute text-white opacity-10 animate-float"
                 :style="{
                   left: Math.random() * 100 + '%',
                   top: Math.random() * 100 + '%',
                   animationDelay: Math.random() * 3 + 's',
                   animationDuration: (Math.random() * 3 + 2) + 's',
                   fontSize: (Math.random() * 20 + 10) + 'px'
                 }">
              <i :class="['fas', ['fa-clipboard-list', 'fa-tasks', 'fa-check-circle', 'fa-clock', 'fa-user-check'][Math.floor(Math.random() * 5)]]"></i>
            </div>
          </div>
        </div>
        
        <div class="max-w-full mx-auto relative z-10">
          <!-- Header Section -->
          <div class="medical-glass-card rounded-t-3xl p-6 mb-0 border-b border-blue-300/30">
            <div class="flex justify-between items-center">
              <!-- Left Logo -->
              <div class="w-28 h-28 mr-6 transform hover:scale-110 transition-transform duration-300">
                <div class="w-full h-full bg-gradient-to-br from-blue-500/20 to-teal-500/20 rounded-2xl backdrop-blur-sm border-2 border-blue-300/40 flex items-center justify-center shadow-2xl hover:shadow-blue-500/25">
                  <img src="/assets/images/ngao2.png" alt="National Shield" class="max-w-18 max-h-18 object-contain" />
                </div>
              </div>

              <!-- Center Content -->
              <div class="text-center flex-1">
                <h1 class="text-3xl font-bold text-white mb-4 tracking-wide drop-shadow-lg animate-fade-in">
                  MUHIMBILI NATIONAL HOSPITAL
                </h1>
                <div class="relative inline-block mb-4">
                  <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-8 py-3 rounded-full text-lg font-bold shadow-2xl transform hover:scale-105 transition-all duration-300 border-2 border-blue-400/60">
                    <span class="relative z-10 flex items-center gap-2">
                      <i class="fas fa-clipboard-list"></i>
                      REQUESTS DASHBOARD
                    </span>
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-700 to-blue-800 rounded-full opacity-0 hover:opacity-100 transition-opacity duration-300"></div>
                  </div>
                </div>
                <h2 class="text-lg font-bold text-blue-100 tracking-wide drop-shadow-md animate-fade-in-delay">
                  {{ getRoleDisplayName(userRole) }} - {{ getApprovalStage() }}
                </h2>
              </div>
              
              <!-- Right Logo -->
              <div class="w-28 h-28 ml-6 transform hover:scale-110 transition-transform duration-300">
                <div class="w-full h-full bg-gradient-to-br from-teal-500/20 to-blue-500/20 rounded-2xl backdrop-blur-sm border-2 border-teal-300/40 flex items-center justify-center shadow-2xl hover:shadow-teal-500/25">
                  <img src="/assets/images/logo2.png" alt="Muhimbili Logo" class="max-w-18 max-h-18 object-contain" />
                </div>
              </div>
            </div>
          </div>

          <!-- Main Content -->
          <div class="medical-glass-card rounded-b-3xl overflow-hidden">
            <div class="p-6">
              
              <!-- Stats Cards -->
              <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-gradient-to-r from-blue-600/25 to-teal-600/25 border-2 border-blue-400/40 p-6 rounded-2xl backdrop-blur-sm hover:shadow-xl hover:shadow-blue-500/20 transition-all duration-500">
                  <div class="flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-teal-600 rounded-lg flex items-center justify-center shadow-lg">
                      <i class="fas fa-clock text-white text-lg"></i>
                    </div>
                    <div class="ml-4">
                      <p class="text-sm font-semibold text-blue-200">Pending</p>
                      <p class="text-2xl font-bold text-white">{{ stats.pending }}</p>
                    </div>
                  </div>
                </div>

                <div class="bg-gradient-to-r from-green-600/25 to-emerald-600/25 border-2 border-green-400/40 p-6 rounded-2xl backdrop-blur-sm hover:shadow-xl hover:shadow-green-500/20 transition-all duration-500">
                  <div class="flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-lg flex items-center justify-center shadow-lg">
                      <i class="fas fa-check-circle text-white text-lg"></i>
                    </div>
                    <div class="ml-4">
                      <p class="text-sm font-semibold text-green-200">Approved</p>
                      <p class="text-2xl font-bold text-white">{{ stats.approved }}</p>
                    </div>
                  </div>
                </div>

                <div class="bg-gradient-to-r from-red-600/25 to-pink-600/25 border-2 border-red-400/40 p-6 rounded-2xl backdrop-blur-sm hover:shadow-xl hover:shadow-red-500/20 transition-all duration-500">
                  <div class="flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-pink-600 rounded-lg flex items-center justify-center shadow-lg">
                      <i class="fas fa-times-circle text-white text-lg"></i>
                    </div>
                    <div class="ml-4">
                      <p class="text-sm font-semibold text-red-200">Rejected</p>
                      <p class="text-2xl font-bold text-white">{{ stats.rejected }}</p>
                    </div>
                  </div>
                </div>

                <div class="bg-gradient-to-r from-purple-600/25 to-indigo-600/25 border-2 border-purple-400/40 p-6 rounded-2xl backdrop-blur-sm hover:shadow-xl hover:shadow-purple-500/20 transition-all duration-500">
                  <div class="flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-lg flex items-center justify-center shadow-lg">
                      <i class="fas fa-list text-white text-lg"></i>
                    </div>
                    <div class="ml-4">
                      <p class="text-sm font-semibold text-purple-200">Total</p>
                      <p class="text-2xl font-bold text-white">{{ stats.total }}</p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Filters and Search -->
              <div class="bg-gradient-to-r from-blue-600/25 to-teal-600/25 border-2 border-blue-400/40 p-6 rounded-2xl backdrop-blur-sm mb-8">
                <div class="flex flex-col md:flex-row gap-4 items-center">
                  <div class="flex-1">
                    <div class="relative">
                      <input 
                        v-model="searchQuery"
                        type="text" 
                        placeholder="Search by PF Number, Staff Name, Department..."
                        class="w-full px-4 py-3 bg-white/15 border border-blue-300/30 rounded-lg focus:border-teal-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 text-sm"
                      />
                      <div class="absolute right-3 top-1/2 transform -translate-y-1/2 text-teal-300/50">
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
                      <option value="approved">Approved</option>
                      <option value="rejected">Rejected</option>
                    </select>

                    <select 
                      v-model="typeFilter"
                      class="px-4 py-3 bg-white/15 border border-blue-300/30 rounded-lg focus:border-teal-400 focus:outline-none text-white backdrop-blur-sm transition-all appearance-none cursor-pointer text-sm"
                    >
                      <option value="">All Types</option>
                      <option value="jeeva">Jeeva Access</option>
                      <option value="wellsoft">Wellsoft Access</option>
                      <option value="internet">Internet Access</option>
                      <option value="combined">Combined Services</option>
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
              <div class="bg-gradient-to-r from-blue-600/25 to-teal-600/25 border-2 border-blue-400/40 rounded-2xl backdrop-blur-sm overflow-hidden">
                <div class="p-6 border-b border-blue-300/30">
                  <h3 class="text-xl font-bold text-white flex items-center">
                    <i class="fas fa-table mr-3 text-teal-300"></i>
                    Access Requests - {{ getApprovalStage() }}
                  </h3>
                </div>

                <div class="overflow-x-auto">
                  <table class="w-full">
                    <thead class="bg-blue-800/50">
                      <tr>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-blue-100 uppercase tracking-wider cursor-pointer hover:bg-blue-700/30" @click="sortBy('id')">
                          <i class="fas fa-hashtag mr-2"></i>Request ID
                          <i v-if="sortField === 'id'" :class="['fas ml-2', sortOrder === 'asc' ? 'fa-sort-up' : 'fa-sort-down']"></i>
                        </th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-blue-100 uppercase tracking-wider cursor-pointer hover:bg-blue-700/30" @click="sortBy('type')">
                          <i class="fas fa-layer-group mr-2"></i>Request Type
                          <i v-if="sortField === 'type'" :class="['fas ml-2', sortOrder === 'asc' ? 'fa-sort-up' : 'fa-sort-down']"></i>
                        </th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-blue-100 uppercase tracking-wider">
                          <i class="fas fa-user mr-2"></i>Personal Info
                        </th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-blue-100 uppercase tracking-wider">
                          <i class="fas fa-cogs mr-2"></i>Module Request
                        </th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-blue-100 uppercase tracking-wider">
                          <i class="fas fa-key mr-2"></i>Access Rights
                        </th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-blue-100 uppercase tracking-wider cursor-pointer hover:bg-blue-700/30" @click="sortBy('submissionDate')">
                          <i class="fas fa-calendar mr-2"></i>Submission Date
                          <i v-if="sortField === 'submissionDate'" :class="['fas ml-2', sortOrder === 'asc' ? 'fa-sort-up' : 'fa-sort-down']"></i>
                        </th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-blue-100 uppercase tracking-wider">
                          <i class="fas fa-flag mr-2"></i>Status
                        </th>
                        <th class="px-6 py-4 text-center text-sm font-semibold text-blue-100 uppercase tracking-wider">
                          <i class="fas fa-tools mr-2"></i>Actions
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
                        <td class="px-6 py-4 whitespace-nowrap">
                          <div class="text-sm font-medium text-white">#{{ request.id }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                          <div class="flex items-center">
                            <div :class="getTypeIconClass(request.type)" class="w-8 h-8 rounded-lg flex items-center justify-center mr-3">
                              <i :class="getTypeIcon(request.type)" class="text-white text-sm"></i>
                            </div>
                            <span class="text-sm font-medium text-white">{{ getTypeDisplayName(request.type) }}</span>
                          </div>
                        </td>
                        <td class="px-6 py-4">
                          <div class="text-sm text-white">
                            <div class="font-medium">{{ request.staffName }}</div>
                            <div class="text-blue-300">PF: {{ request.pfNumber }}</div>
                            <div class="text-blue-300">{{ request.department }}</div>
                            <div class="flex items-center mt-1">
                              <i :class="request.digitalSignature ? 'fas fa-check-circle text-green-400' : 'fas fa-times-circle text-red-400'" class="mr-1"></i>
                              <span class="text-xs">{{ request.digitalSignature ? 'Signed' : 'Not Signed' }}</span>
                            </div>
                          </div>
                        </td>
                        <td class="px-6 py-4">
                          <div class="text-sm text-white">
                            <div class="font-medium">{{ request.moduleRequestedFor }}</div>
                            <div class="text-blue-300 text-xs">{{ request.selectedModules?.join(', ') || 'N/A' }}</div>
                          </div>
                        </td>
                        <td class="px-6 py-4">
                          <div class="text-sm text-white">
                            <div class="font-medium">{{ request.accessType }}</div>
                            <div v-if="request.accessType === 'temporary'" class="text-blue-300 text-xs">
                              Until: {{ formatDate(request.temporaryUntil) }}
                            </div>
                          </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                          <div class="text-sm text-white">{{ formatDate(request.submissionDate) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                          <span :class="getStatusBadgeClass(request.currentStatus)" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium">
                            <i :class="getStatusIcon(request.currentStatus)" class="mr-1"></i>
                            {{ getStatusText(request.currentStatus) }}
                          </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                          <div class="flex justify-center space-x-2">
                            <button
                              @click.stop="viewRequest(request)"
                              class="inline-flex items-center px-3 py-1.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white text-xs font-medium rounded-lg hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 transition duration-200 shadow-lg hover:shadow-xl transform hover:scale-105"
                            >
                              <i class="fas fa-eye mr-1"></i>
                              View
                            </button>
                            <button
                              v-if="canApprove(request)"
                              @click.stop="approveRequest(request)"
                              class="inline-flex items-center px-3 py-1.5 bg-gradient-to-r from-green-600 to-green-700 text-white text-xs font-medium rounded-lg hover:from-green-700 hover:to-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-1 transition duration-200 shadow-lg hover:shadow-xl transform hover:scale-105"
                            >
                              <i class="fas fa-check mr-1"></i>
                              Approve
                            </button>
                          </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>

                  <!-- Empty State -->
                  <div v-if="filteredRequests.length === 0" class="text-center py-12">
                    <div class="w-24 h-24 bg-gradient-to-br from-blue-500/20 to-teal-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                      <i class="fas fa-inbox text-blue-300 text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-white mb-2">No requests found</h3>
                    <p class="text-blue-300">{{ searchQuery || statusFilter || typeFilter ? 'Try adjusting your filters' : 'No requests are pending your approval.' }}</p>
                  </div>
                </div>

                <!-- Pagination -->
                <div v-if="filteredRequests.length > 0" class="px-6 py-4 border-t border-blue-300/30 flex items-center justify-between">
                  <div class="text-sm text-blue-300">
                    Showing {{ ((currentPage - 1) * itemsPerPage) + 1 }} to {{ Math.min(currentPage * itemsPerPage, filteredRequests.length) }} of {{ filteredRequests.length }} requests
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

            </div>
          </div>
        </div>
      </main>
    </div>
    <AppFooter />

    <!-- Loading Modal -->
    <div v-if="isLoading" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-xl shadow-2xl p-8 text-center">
        <div class="w-16 h-16 border-4 border-blue-600 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
        <p class="text-gray-600">Loading requests...</p>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import AppHeader from '@/components/header.vue'
import DynamicSidebar from '@/components/DynamicSidebar.vue'
import AppFooter from '@/components/footer.vue'
import { useAuth } from '@/composables/useAuth'

export default {
  name: 'InternalAccessList',
  components: {
    AppHeader,
    DynamicSidebar,
    AppFooter
  },
  setup() {
    const router = useRouter()
    const { userRole, ROLES, getRoleDisplayName } = useAuth()
    
    // Reactive data
    const sidebarCollapsed = ref(false)
    const requests = ref([])
    const isLoading = ref(true)
    const searchQuery = ref('')
    const statusFilter = ref('')
    const typeFilter = ref('')
    const sortField = ref('submissionDate')
    const sortOrder = ref('desc')
    const currentPage = ref(1)
    const itemsPerPage = ref(10)

    // Mock data - replace with actual API calls
    const mockRequests = [
      {
        id: 'REQ-001',
        type: 'jeeva',
        staffName: 'Dr. John Doe',
        pfNumber: 'PF001234',
        department: 'Cardiology',
        digitalSignature: true,
        moduleRequestedFor: 'use',
        selectedModules: ['doctor_consultation', 'medical_records'],
        accessType: 'Permanent (until retirement)',
        temporaryUntil: null,
        submissionDate: '2024-01-15',
        currentStatus: 'pending',
        hodApprovalStatus: 'pending',
        divisionalStatus: 'pending',
        dictStatus: 'pending',
        headOfItStatus: 'pending',
        ictStatus: 'pending'
      },
      {
        id: 'REQ-002',
        type: 'wellsoft',
        staffName: 'Jane Smith',
        pfNumber: 'PF005678',
        department: 'Nursing',
        digitalSignature: false,
        moduleRequestedFor: 'use',
        selectedModules: ['nursing_station', 'inpatient'],
        accessType: 'Temporary Until',
        temporaryUntil: '2024-06-30',
        submissionDate: '2024-01-14',
        currentStatus: 'approved',
        hodApprovalStatus: 'approved',
        divisionalStatus: 'pending',
        dictStatus: 'pending',
        headOfItStatus: 'pending',
        ictStatus: 'pending'
      },
      {
        id: 'REQ-003',
        type: 'internet',
        staffName: 'Mike Johnson',
        pfNumber: 'PF009876',
        department: 'Administration',
        digitalSignature: true,
        moduleRequestedFor: 'Use',
        selectedModules: ['Email', 'Research'],
        accessType: 'Permanent (until retirement)',
        temporaryUntil: null,
        submissionDate: '2024-01-13',
        currentStatus: 'rejected',
        hodApprovalStatus: 'approved',
        divisionalStatus: 'approved',
        dictStatus: 'rejected',
        headOfItStatus: 'pending',
        ictStatus: 'pending'
      },
      {
        id: 'REQ-004',
        type: 'combined',
        staffName: 'Sarah Wilson',
        pfNumber: 'PF004321',
        department: 'Laboratory',
        digitalSignature: true,
        moduleRequestedFor: 'use',
        selectedModules: ['laboratory', 'blood_bank'],
        accessType: 'Temporary Until',
        temporaryUntil: '2024-12-31',
        submissionDate: '2024-01-12',
        currentStatus: 'pending',
        hodApprovalStatus: 'approved',
        divisionalStatus: 'approved',
        dictStatus: 'approved',
        headOfItStatus: 'pending',
        ictStatus: 'pending'
      }
    ]

    // Computed properties
    const filteredRequests = computed(() => {
      let filtered = requests.value

      // Filter by role-specific approval stage
      filtered = filtered.filter(request => {
        switch (userRole.value) {
          case ROLES.HEAD_OF_DEPARTMENT:
            return request.hodApprovalStatus === 'pending'
          case ROLES.DIVISIONAL_DIRECTOR:
            return request.hodApprovalStatus === 'approved' && request.divisionalStatus === 'pending'
          case ROLES.ICT_DIRECTOR:
            return request.divisionalStatus === 'approved' && request.dictStatus === 'pending'
          case ROLES.HOD_IT:
            return request.dictStatus === 'approved' && request.headOfItStatus === 'pending'
          case ROLES.ICT_OFFICER:
            return request.headOfItStatus === 'approved' && request.ictStatus === 'pending'
          default:
            return true
        }
      })

      // Filter by search query
      if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase()
        filtered = filtered.filter(request => 
          request.staffName.toLowerCase().includes(query) ||
          request.pfNumber.toLowerCase().includes(query) ||
          request.department.toLowerCase().includes(query) ||
          request.id.toLowerCase().includes(query)
        )
      }

      // Filter by status
      if (statusFilter.value) {
        filtered = filtered.filter(request => request.currentStatus === statusFilter.value)
      }

      // Filter by type
      if (typeFilter.value) {
        filtered = filtered.filter(request => request.type === typeFilter.value)
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
      const roleFilteredRequests = requests.value.filter(request => {
        switch (userRole.value) {
          case ROLES.HEAD_OF_DEPARTMENT:
            return request.hodApprovalStatus === 'pending'
          case ROLES.DIVISIONAL_DIRECTOR:
            return request.hodApprovalStatus === 'approved' && request.divisionalStatus === 'pending'
          case ROLES.ICT_DIRECTOR:
            return request.divisionalStatus === 'approved' && request.dictStatus === 'pending'
          case ROLES.HOD_IT:
            return request.dictStatus === 'approved' && request.headOfItStatus === 'pending'
          case ROLES.ICT_OFFICER:
            return request.headOfItStatus === 'approved' && request.ictStatus === 'pending'
          default:
            return true
        }
      })

      return {
        pending: roleFilteredRequests.filter(r => r.currentStatus === 'pending').length,
        approved: roleFilteredRequests.filter(r => r.currentStatus === 'approved').length,
        rejected: roleFilteredRequests.filter(r => r.currentStatus === 'rejected').length,
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
        case ROLES.HOD_IT:
          return 'Head of IT Approval Stage'
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

    const viewRequest = (request) => {
      // Route to the appropriate form based on request type
      const routeMap = {
        'jeeva': '/jeeva-access',
        'wellsoft': '/wellsoft-access', 
        'internet': '/internet-access',
        'combined': '/both-service-form'
      }
      
      const route = routeMap[request.type] || '/jeeva-access'
      
      // Build query parameters with all request data
      const queryParams = {
        mode: 'review',
        requestId: request.id,
        pfNumber: request.pfNumber,
        staffName: request.staffName,
        department: request.department,
        designation: request.designation, // For internet access
        moduleRequestedFor: request.moduleRequestedFor,
        selectedModules: JSON.stringify(request.selectedModules),
        accessType: request.accessType,
        temporaryUntil: request.temporaryUntil,
        digitalSignature: request.digitalSignature.toString(),
        submissionDate: request.submissionDate,
        currentStatus: request.currentStatus,
        hodApprovalStatus: request.hodApprovalStatus,
        divisionalStatus: request.divisionalStatus,
        dictStatus: request.dictStatus,
        headOfItStatus: request.headOfItStatus,
        ictStatus: request.ictStatus,
        comments: request.comments || '',
        phone: request.phone || '' // For combined form
      }
      
      router.push({
        path: route,
        query: queryParams
      })
    }

    const approveRequest = async (request) => {
      if (confirm(`Are you sure you want to approve request ${request.id}?`)) {
        try {
          // Here you would make an API call to approve the request
          // await api.approveRequest(request.id, userRole.value)
          
          // For demo, just update the local state
          const index = requests.value.findIndex(r => r.id === request.id)
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
              case ROLES.HOD_IT:
                requests.value[index].headOfItStatus = 'approved'
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

    const refreshRequests = async () => {
      isLoading.value = true
      try {
        // Simulate API call
        await new Promise(resolve => setTimeout(resolve, 1000))
        // In real implementation, fetch from API based on user role
        requests.value = mockRequests
      } catch (error) {
        console.error('Error fetching requests:', error)
      } finally {
        isLoading.value = false
      }
    }

    // Lifecycle
    onMounted(async () => {
      await refreshRequests()
    })

    return {
      sidebarCollapsed,
      requests,
      isLoading,
      searchQuery,
      statusFilter,
      typeFilter,
      sortField,
      sortOrder,
      currentPage,
      itemsPerPage,
      filteredRequests,
      paginatedRequests,
      totalPages,
      stats,
      userRole,
      ROLES,
      getRoleDisplayName,
      getApprovalStage,
      getTypeDisplayName,
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
      refreshRequests
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
  box-shadow: 0 8px 32px rgba(29, 78, 216, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.1);
}

/* Animations */
@keyframes float {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-15px); }
}

@keyframes fade-in {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes fade-in-delay {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
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
input:focus, select:focus {
  box-shadow: 0 0 0 3px rgba(45, 212, 191, 0.1);
}

button:focus {
  box-shadow: 0 0 0 3px rgba(45, 212, 191, 0.3);
}

/* Smooth transitions */
* {
  transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter;
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