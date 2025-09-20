<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-900 via-blue-800 to-teal-900 p-6">
    <div class="max-w-7xl mx-auto">
      <!-- Header -->
      <div class="bg-white/10 backdrop-blur-sm rounded-lg p-6 mb-6 border border-white/20">
        <h1 class="text-3xl font-bold text-white mb-2">Module Request Management</h1>
        <p class="text-blue-100">Example implementation of the new module request API endpoints</p>
      </div>

      <!-- Statistics Dashboard -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Statistics Card -->
        <div class="bg-white/10 backdrop-blur-sm rounded-lg p-6 border border-white/20">
          <h2 class="text-xl font-bold text-white mb-4 flex items-center">
            <i class="fas fa-chart-bar mr-2 text-blue-400"></i>
            Module Request Statistics
          </h2>

          <div v-if="loading.stats" class="flex justify-center items-center h-40">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-white"></div>
          </div>

          <div v-else-if="statistics" class="space-y-4">
            <!-- Total Requests -->
            <div class="bg-blue-600/30 rounded-lg p-4">
              <div class="flex justify-between items-center">
                <span class="text-blue-100 text-sm font-medium">Total Requests</span>
                <span class="text-white text-2xl font-bold">{{ statistics.total_requests }}</span>
              </div>
            </div>

            <!-- Module Requests Overview -->
            <div class="bg-purple-600/30 rounded-lg p-4">
              <h3 class="text-white font-semibold mb-2">Module Requests</h3>
              <div class="grid grid-cols-2 gap-2 text-sm">
                <div class="text-purple-100">Total: {{ statistics.module_requests.total }}</div>
                <div class="text-purple-100">Pending: {{ statistics.module_requests.pending }}</div>
                <div class="text-purple-100">
                  Use: {{ statistics.module_requests.use_requests }}
                </div>
                <div class="text-purple-100">
                  Revoke: {{ statistics.module_requests.revoke_requests }}
                </div>
              </div>
            </div>

            <!-- System Usage -->
            <div class="grid grid-cols-2 gap-4">
              <div class="bg-green-600/30 rounded-lg p-3">
                <div class="text-green-100 text-xs font-medium">Wellsoft Usage</div>
                <div class="text-white font-bold">
                  {{ statistics.wellsoft_usage.total_requests }} requests
                </div>
                <div class="text-green-200 text-xs">
                  {{ statistics.wellsoft_usage.modules_requested }} modules
                </div>
              </div>
              <div class="bg-yellow-600/30 rounded-lg p-3">
                <div class="text-yellow-100 text-xs font-medium">Jeeva Usage</div>
                <div class="text-white font-bold">
                  {{ statistics.jeeva_usage.total_requests }} requests
                </div>
                <div class="text-yellow-200 text-xs">
                  {{ statistics.jeeva_usage.modules_requested }} modules
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Requests -->
        <div class="bg-white/10 backdrop-blur-sm rounded-lg p-6 border border-white/20">
          <h2 class="text-xl font-bold text-white mb-4 flex items-center">
            <i class="fas fa-clock mr-2 text-green-400"></i>
            Recent Module Requests
          </h2>

          <div v-if="loading.stats" class="flex justify-center items-center h-40">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-white"></div>
          </div>

          <div v-else-if="statistics?.recent_requests" class="space-y-3">
            <div
              v-for="request in statistics.recent_requests"
              :key="request.id"
              class="bg-white/10 rounded-lg p-3 hover:bg-white/15 transition-all cursor-pointer"
              @click="viewRequestDetails(request.id)"
            >
              <div class="flex justify-between items-start">
                <div>
                  <div class="text-white font-medium">{{ request.staff_name }}</div>
                  <div class="text-blue-100 text-sm">
                    {{ request.module_requested_for }} request
                  </div>
                  <div class="text-xs text-blue-200 flex space-x-2 mt-1">
                    <span v-if="request.wellsoft_count"
                      >Wellsoft: {{ request.wellsoft_count }}</span
                    >
                    <span v-if="request.jeeva_count">Jeeva: {{ request.jeeva_count }}</span>
                  </div>
                </div>
                <div class="text-right">
                  <div class="text-xs text-blue-200">{{ formatDate(request.created_at) }}</div>
                  <div
                    :class="getStatusClass(request.status)"
                    class="text-xs px-2 py-1 rounded-full mt-1"
                  >
                    {{ request.status }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Pending Requests List -->
      <div class="bg-white/10 backdrop-blur-sm rounded-lg p-6 border border-white/20">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-xl font-bold text-white flex items-center">
            <i class="fas fa-list mr-2 text-yellow-400"></i>
            Pending Module Requests
          </h2>

          <div class="flex space-x-2">
            <button
              @click="toggleModulesOnlyFilter"
              :class="[
                'px-3 py-1 rounded-lg text-sm transition-all',
                modulesOnlyFilter
                  ? 'bg-blue-600 text-white'
                  : 'bg-white/10 text-blue-100 hover:bg-white/20'
              ]"
            >
              <i class="fas fa-filter mr-1"></i>
              {{ modulesOnlyFilter ? 'All Requests' : 'Modules Only' }}
            </button>
            <button
              @click="refreshPendingRequests"
              class="px-3 py-1 bg-white/10 text-blue-100 hover:bg-white/20 rounded-lg text-sm transition-all"
            >
              <i class="fas fa-refresh mr-1"></i>
              Refresh
            </button>
          </div>
        </div>

        <div v-if="loading.pending" class="flex justify-center items-center h-40">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-white"></div>
        </div>

        <div v-else-if="pendingRequests.length === 0" class="text-center py-8">
          <i class="fas fa-inbox text-6xl text-white/30 mb-4"></i>
          <p class="text-white/70">No pending module requests found</p>
        </div>

        <div v-else class="space-y-4">
          <div
            v-for="request in pendingRequests"
            :key="request.id"
            class="bg-white/5 rounded-lg p-4 hover:bg-white/10 transition-all border border-white/10"
          >
            <div class="flex justify-between items-start mb-3">
              <div>
                <h3 class="text-white font-semibold">{{ request.staff_name }}</h3>
                <p class="text-blue-200 text-sm">
                  PF: {{ request.pf_number }} | {{ request.department }}
                </p>
                <p class="text-blue-300 text-xs">{{ request.phone_number }}</p>
              </div>
              <div class="text-right">
                <div
                  :class="getStatusClass(request.status)"
                  class="px-3 py-1 rounded-full text-xs mb-2"
                >
                  {{ request.status }}
                </div>
                <div class="text-blue-200 text-xs">{{ formatDate(request.created_at) }}</div>
              </div>
            </div>

            <!-- Request Types -->
            <div class="flex flex-wrap gap-2 mb-3">
              <span
                v-for="type in request.request_types"
                :key="type"
                class="px-2 py-1 bg-blue-600/40 text-blue-100 rounded-full text-xs"
              >
                {{ formatRequestType(type) }}
              </span>
            </div>

            <!-- Module Summary (only show if has module request) -->
            <div v-if="request.has_module_request" class="bg-white/5 rounded-lg p-3 mb-3">
              <div class="flex items-center justify-between mb-2">
                <span class="text-white font-medium text-sm">Module Request Summary</span>
                <span
                  :class="getRequestTypeClass(request.module_summary.requested_for)"
                  class="px-2 py-1 rounded-full text-xs"
                >
                  {{ request.module_summary.requested_for.toUpperCase() }}
                </span>
              </div>
              <div class="grid grid-cols-2 gap-2 text-sm">
                <div class="text-green-200" v-if="request.module_summary.wellsoft_count > 0">
                  Wellsoft: {{ request.module_summary.wellsoft_count }} modules
                </div>
                <div class="text-yellow-200" v-if="request.module_summary.jeeva_count > 0">
                  Jeeva: {{ request.module_summary.jeeva_count }} modules
                </div>
              </div>

              <!-- Show specific modules -->
              <div class="mt-2 space-y-1">
                <div v-if="request.module_summary.wellsoft_modules.length > 0" class="text-xs">
                  <span class="text-green-300">Wellsoft modules:</span>
                  <span class="text-white ml-1">{{
                    request.module_summary.wellsoft_modules.join(', ')
                  }}</span>
                </div>
                <div v-if="request.module_summary.jeeva_modules.length > 0" class="text-xs">
                  <span class="text-yellow-300">Jeeva modules:</span>
                  <span class="text-white ml-1">{{
                    request.module_summary.jeeva_modules.join(', ')
                  }}</span>
                </div>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex space-x-2">
              <button
                @click="viewRequestDetails(request.id)"
                class="px-3 py-1 bg-blue-600/50 hover:bg-blue-600/70 text-white rounded-lg text-sm transition-all"
              >
                <i class="fas fa-eye mr-1"></i>
                View Details
              </button>
              <button
                v-if="request.has_hod_signature"
                class="px-3 py-1 bg-green-600/50 hover:bg-green-600/70 text-white rounded-lg text-sm transition-all"
              >
                <i class="fas fa-signature mr-1"></i>
                Has Signature
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Request Details Modal -->
      <div
        v-if="selectedRequestDetails"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
        @click.self="closeRequestDetails"
      >
        <div class="bg-white rounded-lg max-w-4xl w-full max-h-[90vh] overflow-y-auto">
          <div class="p-6">
            <div class="flex justify-between items-start mb-4">
              <h2 class="text-2xl font-bold text-gray-800">Request Details</h2>
              <button @click="closeRequestDetails" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
              </button>
            </div>

            <div v-if="loading.details" class="flex justify-center items-center h-40">
              <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            </div>

            <div v-else-if="selectedRequestDetails" class="space-y-6">
              <!-- User Information -->
              <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">User Information</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                  <div>
                    <strong>Staff Name:</strong> {{ selectedRequestDetails.user_info.staff_name }}
                  </div>
                  <div>
                    <strong>PF Number:</strong> {{ selectedRequestDetails.user_info.pf_number }}
                  </div>
                  <div>
                    <strong>Department:</strong> {{ selectedRequestDetails.user_info.department }}
                  </div>
                  <div>
                    <strong>Phone:</strong> {{ selectedRequestDetails.user_info.phone_number }}
                  </div>
                </div>
              </div>

              <!-- Access Rights Information -->
              <div class="bg-green-50 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Access Rights</h3>
                <div class="space-y-3">
                  <div class="flex items-center space-x-4">
                    <span class="font-medium">Type:</span>
                    <span
                      :class="
                        selectedRequestDetails.access_rights.is_permanent
                          ? 'bg-green-600 text-white'
                          : 'bg-orange-600 text-white'
                      "
                      class="px-3 py-1 rounded-full text-sm"
                    >
                      {{ selectedRequestDetails.access_rights.description }}
                    </span>
                  </div>
                  <div
                    v-if="selectedRequestDetails.access_rights.is_temporary"
                    class="text-sm text-gray-600"
                  >
                    <strong>Valid Until:</strong>
                    {{ selectedRequestDetails.access_rights.temporary_until_display }}
                  </div>
                </div>
              </div>

              <!-- Module Request Details -->
              <div class="bg-blue-50 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Module Request Details</h3>
                <div class="space-y-3">
                  <div class="flex items-center space-x-4">
                    <span class="font-medium">Request Type:</span>
                    <span
                      :class="getRequestTypeClass(selectedRequestDetails.module_requested_for)"
                      class="px-3 py-1 rounded-full text-sm"
                    >
                      {{ selectedRequestDetails.module_requested_for.toUpperCase() }}
                    </span>
                  </div>

                  <div class="grid grid-cols-2 gap-4">
                    <!-- Wellsoft Modules -->
                    <div v-if="selectedRequestDetails.wellsoft_modules.selected.length > 0">
                      <h4 class="font-medium text-green-700 mb-2">
                        Wellsoft Modules ({{ selectedRequestDetails.wellsoft_modules.count }})
                      </h4>
                      <div class="space-y-1">
                        <span
                          v-for="module in selectedRequestDetails.wellsoft_modules.selected"
                          :key="module"
                          class="inline-block bg-green-100 text-green-800 px-2 py-1 rounded text-sm mr-2 mb-1"
                        >
                          {{ module }}
                        </span>
                      </div>
                    </div>

                    <!-- Jeeva Modules -->
                    <div v-if="selectedRequestDetails.jeeva_modules.selected.length > 0">
                      <h4 class="font-medium text-yellow-700 mb-2">
                        Jeeva Modules ({{ selectedRequestDetails.jeeva_modules.count }})
                      </h4>
                      <div class="space-y-1">
                        <span
                          v-for="module in selectedRequestDetails.jeeva_modules.selected"
                          :key="module"
                          class="inline-block bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-sm mr-2 mb-1"
                        >
                          {{ module }}
                        </span>
                      </div>
                    </div>
                  </div>

                  <!-- Additional Request Information -->
                  <div class="border-t pt-3 mt-3">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                      <div><strong>Status:</strong> {{ selectedRequestDetails.status }}</div>
                      <div>
                        <strong>Created:</strong>
                        {{ formatDate(selectedRequestDetails.created_at) }}
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Approval Workflow -->
              <div
                v-if="selectedRequestDetails.approval_workflow"
                class="bg-purple-50 rounded-lg p-4"
              >
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Approval Workflow</h3>
                <div class="space-y-4">
                  <!-- HOD Approval -->
                  <div
                    class="border rounded-lg p-3"
                    :class="
                      selectedRequestDetails.approval_workflow.hod_approval.has_signature
                        ? 'bg-green-50 border-green-300'
                        : 'bg-gray-50 border-gray-300'
                    "
                  >
                    <div class="flex items-center justify-between mb-2">
                      <h4 class="font-medium text-gray-700">HOD/BM Approval</h4>
                      <span
                        :class="
                          selectedRequestDetails.approval_workflow.hod_approval.has_signature
                            ? 'bg-green-600 text-white'
                            : 'bg-gray-400 text-white'
                        "
                        class="px-2 py-1 rounded-full text-xs"
                      >
                        {{ selectedRequestDetails.approval_workflow.hod_approval.status }}
                      </span>
                    </div>
                    <div
                      v-if="selectedRequestDetails.approval_workflow.hod_approval.name"
                      class="text-sm space-y-1"
                    >
                      <div>
                        <strong>Name:</strong>
                        {{ selectedRequestDetails.approval_workflow.hod_approval.name }}
                      </div>
                      <div
                        v-if="
                          selectedRequestDetails.approval_workflow.hod_approval
                            .approved_at_formatted
                        "
                      >
                        <strong>Date:</strong>
                        {{
                          selectedRequestDetails.approval_workflow.hod_approval
                            .approved_at_formatted
                        }}
                      </div>
                      <div v-if="selectedRequestDetails.approval_workflow.hod_approval.comments">
                        <strong>Comments:</strong>
                        {{ selectedRequestDetails.approval_workflow.hod_approval.comments }}
                      </div>
                      <div
                        v-if="selectedRequestDetails.approval_workflow.hod_approval.signature_url"
                        class="mt-2"
                      >
                        <strong>Signature:</strong>
                        <img
                          :src="selectedRequestDetails.approval_workflow.hod_approval.signature_url"
                          alt="HOD Signature"
                          class="max-w-xs h-16 border rounded bg-white mt-1"
                        />
                      </div>
                    </div>
                  </div>

                  <!-- Divisional Director Approval -->
                  <div
                    class="border rounded-lg p-3"
                    :class="
                      selectedRequestDetails.approval_workflow.divisional_director_approval
                        .has_signature
                        ? 'bg-green-50 border-green-300'
                        : 'bg-gray-50 border-gray-300'
                    "
                  >
                    <div class="flex items-center justify-between mb-2">
                      <h4 class="font-medium text-gray-700">Divisional Director</h4>
                      <span
                        :class="
                          selectedRequestDetails.approval_workflow.divisional_director_approval
                            .has_signature
                            ? 'bg-green-600 text-white'
                            : 'bg-gray-400 text-white'
                        "
                        class="px-2 py-1 rounded-full text-xs"
                      >
                        {{
                          selectedRequestDetails.approval_workflow.divisional_director_approval
                            .status
                        }}
                      </span>
                    </div>
                    <div
                      v-if="
                        selectedRequestDetails.approval_workflow.divisional_director_approval.name
                      "
                      class="text-sm space-y-1"
                    >
                      <div>
                        <strong>Name:</strong>
                        {{
                          selectedRequestDetails.approval_workflow.divisional_director_approval.name
                        }}
                      </div>
                      <div
                        v-if="
                          selectedRequestDetails.approval_workflow.divisional_director_approval
                            .approved_at_formatted
                        "
                      >
                        <strong>Date:</strong>
                        {{
                          selectedRequestDetails.approval_workflow.divisional_director_approval
                            .approved_at_formatted
                        }}
                      </div>
                      <div
                        v-if="
                          selectedRequestDetails.approval_workflow.divisional_director_approval
                            .signature_url
                        "
                        class="mt-2"
                      >
                        <img
                          :src="
                            selectedRequestDetails.approval_workflow.divisional_director_approval
                              .signature_url
                          "
                          alt="Divisional Director Signature"
                          class="max-w-xs h-16 border rounded bg-white"
                        />
                      </div>
                    </div>
                  </div>

                  <!-- ICT Director Approval -->
                  <div
                    class="border rounded-lg p-3"
                    :class="
                      selectedRequestDetails.approval_workflow.ict_director_approval.has_signature
                        ? 'bg-green-50 border-green-300'
                        : 'bg-gray-50 border-gray-300'
                    "
                  >
                    <div class="flex items-center justify-between mb-2">
                      <h4 class="font-medium text-gray-700">ICT Director</h4>
                      <span
                        :class="
                          selectedRequestDetails.approval_workflow.ict_director_approval
                            .has_signature
                            ? 'bg-green-600 text-white'
                            : 'bg-gray-400 text-white'
                        "
                        class="px-2 py-1 rounded-full text-xs"
                      >
                        {{ selectedRequestDetails.approval_workflow.ict_director_approval.status }}
                      </span>
                    </div>
                    <div
                      v-if="selectedRequestDetails.approval_workflow.ict_director_approval.name"
                      class="text-sm space-y-1"
                    >
                      <div>
                        <strong>Name:</strong>
                        {{ selectedRequestDetails.approval_workflow.ict_director_approval.name }}
                      </div>
                      <div
                        v-if="
                          selectedRequestDetails.approval_workflow.ict_director_approval
                            .approved_at_formatted
                        "
                      >
                        <strong>Date:</strong>
                        {{
                          selectedRequestDetails.approval_workflow.ict_director_approval
                            .approved_at_formatted
                        }}
                      </div>
                    </div>
                  </div>

                  <!-- ICT Officer Implementation -->
                  <div
                    class="border rounded-lg p-3"
                    :class="
                      selectedRequestDetails.approval_workflow.ict_officer_implementation
                        .has_signature
                        ? 'bg-green-50 border-green-300'
                        : 'bg-gray-50 border-gray-300'
                    "
                  >
                    <div class="flex items-center justify-between mb-2">
                      <h4 class="font-medium text-gray-700">ICT Officer Implementation</h4>
                      <span
                        :class="
                          selectedRequestDetails.approval_workflow.ict_officer_implementation
                            .has_signature
                            ? 'bg-green-600 text-white'
                            : 'bg-gray-400 text-white'
                        "
                        class="px-2 py-1 rounded-full text-xs"
                      >
                        {{
                          selectedRequestDetails.approval_workflow.ict_officer_implementation.status
                        }}
                      </span>
                    </div>
                    <div
                      v-if="
                        selectedRequestDetails.approval_workflow.ict_officer_implementation.name
                      "
                      class="text-sm space-y-1"
                    >
                      <div>
                        <strong>Name:</strong>
                        {{
                          selectedRequestDetails.approval_workflow.ict_officer_implementation.name
                        }}
                      </div>
                      <div
                        v-if="
                          selectedRequestDetails.approval_workflow.ict_officer_implementation
                            .implemented_at_formatted
                        "
                      >
                        <strong>Date:</strong>
                        {{
                          selectedRequestDetails.approval_workflow.ict_officer_implementation
                            .implemented_at_formatted
                        }}
                      </div>
                      <div
                        v-if="
                          selectedRequestDetails.approval_workflow.ict_officer_implementation
                            .implementation_comments
                        "
                      >
                        <strong>Comments:</strong>
                        {{
                          selectedRequestDetails.approval_workflow.ict_officer_implementation
                            .implementation_comments
                        }}
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import { ref, onMounted } from 'vue'
  import axios from 'axios'

  export default {
    name: 'ModuleRequestExample',
    setup() {
      // Reactive state
      const statistics = ref(null)
      const pendingRequests = ref([])
      const selectedRequestDetails = ref(null)
      const modulesOnlyFilter = ref(false)

      const loading = ref({
        stats: false,
        pending: false,
        details: false
      })

      // API base URL - adjust according to your setup
      const API_BASE_URL = '/api/both-service-form'

      // Fetch module request statistics
      const fetchStatistics = async () => {
        loading.value.stats = true
        try {
          const response = await axios.get(`${API_BASE_URL}/module-requests-statistics`)
          if (response.data.success) {
            statistics.value = response.data.data
          } else {
            console.error('Failed to fetch statistics:', response.data.message)
          }
        } catch (error) {
          console.error('Error fetching statistics:', error)
        } finally {
          loading.value.stats = false
        }
      }

      // Fetch pending module requests
      const fetchPendingRequests = async () => {
        loading.value.pending = true
        try {
          const params = modulesOnlyFilter.value ? { modules_only: 'true' } : {}
          const response = await axios.get(`${API_BASE_URL}/module-requests`, { params })
          if (response.data.success) {
            pendingRequests.value = response.data.data
          } else {
            console.error('Failed to fetch pending requests:', response.data.message)
          }
        } catch (error) {
          console.error('Error fetching pending requests:', error)
        } finally {
          loading.value.pending = false
        }
      }

      // Fetch specific request details
      const viewRequestDetails = async (requestId) => {
        loading.value.details = true
        try {
          const response = await axios.get(`${API_BASE_URL}/module-requests/${requestId}`)
          if (response.data.success) {
            selectedRequestDetails.value = response.data.data
          } else {
            console.error('Failed to fetch request details:', response.data.message)
          }
        } catch (error) {
          console.error('Error fetching request details:', error)
        } finally {
          loading.value.details = false
        }
      }

      const closeRequestDetails = () => {
        selectedRequestDetails.value = null
      }

      const toggleModulesOnlyFilter = () => {
        modulesOnlyFilter.value = !modulesOnlyFilter.value
        fetchPendingRequests()
      }

      const refreshPendingRequests = () => {
        fetchPendingRequests()
      }

      // Utility functions
      const formatDate = (dateString) => {
        return new Date(dateString).toLocaleDateString('en-US', {
          year: 'numeric',
          month: 'short',
          day: 'numeric',
          hour: '2-digit',
          minute: '2-digit'
        })
      }

      const formatRequestType = (type) => {
        const typeMap = {
          wellsoft: 'Wellsoft Access',
          jeeva_access: 'Jeeva Access',
          internet_access_request: 'Internet Access'
        }
        return typeMap[type] || type
      }

      const getStatusClass = (status) => {
        const statusClasses = {
          pending: 'bg-yellow-500/80 text-white',
          hod_pending: 'bg-orange-500/80 text-white',
          approved: 'bg-green-500/80 text-white',
          rejected: 'bg-red-500/80 text-white'
        }
        return statusClasses[status] || 'bg-gray-500/80 text-white'
      }

      const getRequestTypeClass = (requestType) => {
        return requestType === 'use' ? 'bg-green-600/80 text-white' : 'bg-red-600/80 text-white'
      }

      // Initialize data on component mount
      onMounted(() => {
        fetchStatistics()
        fetchPendingRequests()
      })

      return {
        statistics,
        pendingRequests,
        selectedRequestDetails,
        modulesOnlyFilter,
        loading,
        fetchStatistics,
        fetchPendingRequests,
        viewRequestDetails,
        closeRequestDetails,
        toggleModulesOnlyFilter,
        refreshPendingRequests,
        formatDate,
        formatRequestType,
        getStatusClass,
        getRequestTypeClass
      }
    }
  }
</script>

<style scoped>
  @keyframes float {
    0%,
    100% {
      transform: translateY(0px);
    }
    50% {
      transform: translateY(-10px);
    }
  }

  .animate-float {
    animation: float 3s ease-in-out infinite;
  }

  /* Custom scrollbar for modal */
  .overflow-y-auto::-webkit-scrollbar {
    width: 8px;
  }

  .overflow-y-auto::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
  }

  .overflow-y-auto::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
  }

  .overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #555;
  }
</style>
