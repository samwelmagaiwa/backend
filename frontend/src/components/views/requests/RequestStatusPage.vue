<template>
  <div class="flex flex-col h-screen">
    <AppHeader />
    <div class="flex flex-1 overflow-hidden">
      <ModernSidebar />
      <main
        class="flex-1 p-3 bg-gradient-to-br from-blue-900 via-blue-800 to-teal-900 overflow-y-auto relative"
      >
        <!-- Medical Background Pattern -->
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
        </div>

        <div class="max-w-12xl mx-auto relative z-10">
          <!-- Header Section -->
          <div class="medical-glass-card rounded-t-3xl p-6 mb-0 border-b border-blue-300/30">
            <div class="flex justify-between items-center">
              <div class="text-center flex-1">
                <h1 class="text-2xl font-bold text-white mb-2 tracking-wide drop-shadow-lg">
                  <i class="fas fa-clipboard-check mr-3 text-blue-300"></i>
                  REQUEST STATUS & TRACKING
                </h1>
                <p class="text-blue-100/80 text-sm">
                  Track your submitted requests and view approval status
                </p>
              </div>
            </div>
          </div>

          <!-- Main Content -->
          <div class="medical-glass-card rounded-b-3xl overflow-hidden">
            <div class="p-6 space-y-6">
              <!-- Success Message (shown after submission) -->
              <div
                v-if="showSuccessMessage"
                class="medical-card bg-gradient-to-r from-green-600/25 to-green-700/25 border-2 border-green-400/40 p-6 rounded-2xl"
              >
                <div class="flex items-center space-x-4">
                  <div
                    class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg"
                  >
                    <i class="fas fa-check-circle text-white text-2xl"></i>
                  </div>
                  <div class="flex-1">
                    <h3 class="text-xl font-bold text-white mb-2">
                      Request Submitted Successfully!
                    </h3>
                    <p class="text-green-100/90 text-sm mb-3">
                      Your {{ requestType }} has been submitted and is now in the approval process.
                    </p>
                    <div class="flex items-center space-x-4">
                      <div
                        class="bg-green-500/20 px-3 py-1 rounded-full border border-green-400/30"
                      >
                        <span class="text-green-300 text-sm font-medium">
                          Request ID: #{{ latestRequestId }}
                        </span>
                      </div>
                      <div
                        class="bg-green-500/20 px-3 py-1 rounded-full border border-green-400/30"
                      >
                        <span class="text-green-300 text-sm font-medium">
                          Status: Pending Review
                        </span>
                      </div>
                    </div>
                  </div>
                  <button
                    @click="showSuccessMessage = false"
                    class="w-8 h-8 bg-green-500/20 hover:bg-green-500/30 rounded-lg flex items-center justify-center transition-colors"
                  >
                    <i class="fas fa-times text-green-300"></i>
                  </button>
                </div>
              </div>

              <!-- Request List -->
              <div
                class="medical-card bg-gradient-to-r from-blue-600/25 to-blue-700/25 border-2 border-blue-400/40 p-6 rounded-2xl"
              >
                <div class="flex items-center justify-between mb-6">
                  <div class="flex items-center space-x-4">
                    <div
                      class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg"
                    >
                      <i class="fas fa-list-alt text-white text-xl"></i>
                    </div>
                    <div>
                      <h3 class="text-xl font-bold text-white">My Requests</h3>
                      <p class="text-blue-100/80 text-sm">View all your submitted requests</p>
                    </div>
                  </div>
                  <div class="flex items-center space-x-3">
                    <button
                      @click="refreshRequests"
                      :disabled="loading"
                      class="px-4 py-2 bg-blue-500/20 hover:bg-blue-500/30 border border-blue-400/30 rounded-lg text-blue-300 text-sm font-medium transition-colors"
                    >
                      <i class="fas fa-sync-alt mr-2" :class="{ 'fa-spin': loading }"></i>
                      Refresh
                    </button>
                    <div class="bg-blue-500/20 px-3 py-1 rounded-full border border-blue-400/30">
                      <span class="text-blue-300 text-sm font-medium">
                        Total: {{ totalRequests }}
                      </span>
                    </div>
                  </div>
                </div>

                <!-- Loading State -->
                <div v-if="loading" class="text-center py-8">
                  <div class="inline-flex items-center space-x-2 text-blue-100">
                    <i class="fas fa-spinner fa-spin text-xl"></i>
                    <span>Loading your requests...</span>
                  </div>
                </div>

                <!-- Empty State -->
                <div v-else-if="requests.length === 0" class="text-center py-12">
                  <div
                    class="w-20 h-20 bg-blue-500/20 rounded-full flex items-center justify-center mx-auto mb-4"
                  >
                    <i class="fas fa-inbox text-blue-400 text-2xl"></i>
                  </div>
                  <h4 class="text-lg font-semibold text-white mb-2">No Requests Found</h4>
                  <p class="text-blue-200/80 text-sm mb-6">
                    You haven't submitted any requests yet.
                  </p>
                  <button
                    @click="goToSubmitRequest"
                    class="px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl font-medium hover:from-blue-600 hover:to-blue-700 transition-all duration-300"
                  >
                    <i class="fas fa-plus mr-2"></i>
                    Submit New Request
                  </button>
                </div>

                <!-- Requests Table -->
                <div v-else>
                  <!-- Desktop Table -->
                  <div class="hidden lg:block overflow-x-auto">
                    <table class="w-full">
                      <thead>
                        <tr class="border-b border-blue-400/30">
                          <th class="text-left py-3 px-4 text-blue-200 font-semibold text-sm">
                            Request ID
                          </th>
                          <th class="text-left py-3 px-4 text-blue-200 font-semibold text-sm">
                            Type
                          </th>
                          <th class="text-left py-3 px-4 text-blue-200 font-semibold text-sm">
                            Services
                          </th>
                          <th class="text-left py-3 px-4 text-blue-200 font-semibold text-sm">
                            Status
                          </th>
                          <th class="text-left py-3 px-4 text-blue-200 font-semibold text-sm">
                            Current Step
                          </th>
                          <th class="text-left py-3 px-4 text-blue-200 font-semibold text-sm">
                            Submitted
                          </th>
                          <th class="text-left py-3 px-4 text-blue-200 font-semibold text-sm">
                            Actions
                          </th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr
                          v-for="request in requests"
                          :key="request.id"
                          class="border-b border-blue-400/20 hover:bg-blue-500/10 transition-colors"
                        >
                          <td class="py-4 px-4">
                            <div class="font-medium text-white">#{{ request.id }}</div>
                          </td>
                          <td class="py-4 px-4">
                            <div class="flex items-center space-x-2">
                              <i
                                :class="getRequestTypeIcon(request.type)"
                                class="text-blue-400"
                              ></i>
                              <span class="text-white text-sm">{{
                                getRequestTypeName(request.type)
                              }}</span>
                            </div>
                          </td>
                          <td class="py-4 px-4">
                            <div class="flex flex-wrap gap-1">
                              <span
                                v-for="service in request.services"
                                :key="service"
                                class="px-2 py-1 bg-blue-500/20 text-blue-300 text-xs rounded-full border border-blue-400/30"
                              >
                                {{ service }}
                              </span>
                            </div>
                          </td>
                          <td class="py-4 px-4">
                            <div class="flex items-center space-x-2">
                              <div
                                class="w-2 h-2 rounded-full"
                                :class="getStatusColor(request.status)"
                              ></div>
                              <span
                                class="text-sm font-medium"
                                :class="getStatusTextColor(request.status)"
                              >
                                {{ getStatusText(request.status) }}
                              </span>
                            </div>
                          </td>
                          <td class="py-4 px-4">
                            <div class="text-white text-sm">
                              {{ getCurrentStepText(request.current_step) }}
                            </div>
                            <div class="text-blue-300 text-xs">
                              Step {{ request.current_step }} of 7
                            </div>
                          </td>
                          <td class="py-4 px-4">
                            <div class="text-white text-sm">
                              {{ formatDate(request.created_at) }}
                            </div>
                            <div class="text-blue-300 text-xs">
                              {{ formatTime(request.created_at) }}
                            </div>
                          </td>
                          <td class="py-4 px-4">
                            <button
                              @click="viewRequestDetails(request)"
                              class="px-3 py-1 bg-blue-500/20 hover:bg-blue-500/30 border border-blue-400/30 rounded-lg text-blue-300 text-sm font-medium transition-colors"
                            >
                              <i class="fas fa-eye mr-1"></i>
                              View
                            </button>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>

                  <!-- Mobile Card Layout -->
                  <div class="lg:hidden space-y-4">
                    <div
                      v-for="request in requests"
                      :key="request.id"
                      class="bg-blue-500/10 border border-blue-400/30 rounded-xl p-4 hover:bg-blue-500/15 transition-colors"
                    >
                      <div class="flex items-center justify-between mb-3">
                        <div class="font-medium text-white">#{{ request.id }}</div>
                        <div class="flex items-center space-x-2">
                          <div
                            class="w-2 h-2 rounded-full"
                            :class="getStatusColor(request.status)"
                          ></div>
                          <span
                            class="text-sm font-medium"
                            :class="getStatusTextColor(request.status)"
                          >
                            {{ getStatusText(request.status) }}
                          </span>
                        </div>
                      </div>

                      <div class="space-y-2 mb-4">
                        <div class="flex items-center space-x-2">
                          <i :class="getRequestTypeIcon(request.type)" class="text-blue-400"></i>
                          <span class="text-white text-sm">{{
                            getRequestTypeName(request.type)
                          }}</span>
                        </div>

                        <div class="flex flex-wrap gap-1">
                          <span
                            v-for="service in request.services"
                            :key="service"
                            class="px-2 py-1 bg-blue-500/20 text-blue-300 text-xs rounded-full border border-blue-400/30"
                          >
                            {{ service }}
                          </span>
                        </div>

                        <div class="text-white text-sm">
                          {{ getCurrentStepText(request.current_step) }}
                          <span class="text-blue-300 text-xs ml-2">
                            (Step {{ request.current_step }} of 7)
                          </span>
                        </div>

                        <div class="text-blue-300 text-xs">
                          {{ formatDate(request.created_at) }} at
                          {{ formatTime(request.created_at) }}
                        </div>
                      </div>

                      <button
                        @click="viewRequestDetails(request)"
                        class="w-full px-3 py-2 bg-blue-500/20 hover:bg-blue-500/30 border border-blue-400/30 rounded-lg text-blue-300 text-sm font-medium transition-colors"
                      >
                        <i class="fas fa-eye mr-2"></i>
                        View Details
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Modal removed - now using full page view -->
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script>
  import { ref, onMounted } from 'vue'
  import { useRouter, useRoute } from 'vue-router'
  import ModernSidebar from '@/components/ModernSidebar.vue'
  import AppHeader from '@/components/AppHeader.vue'
  import { useAuth } from '@/composables/useAuth'
  import requestStatusService from '@/services/requestStatusService'

  export default {
    name: 'RequestStatusPage',
    components: {
      ModernSidebar,
      AppHeader
    },
    setup() {
      const router = useRouter()
      const route = useRoute()
      const { requireRole, ROLES } = useAuth()

      // Local state
      // Sidebar state now managed by Pinia - no local state needed
      const loading = ref(false)
      const showSuccessMessage = ref(false)
      const requestType = ref('')
      const latestRequestId = ref('')

      // Reactive data for requests
      const requests = ref([])
      const totalRequests = ref(0)
      const currentPage = ref(1)
      const perPage = ref(15)
      const lastPage = ref(1)

      const approvalSteps = [
        { id: 1, label: 'User Info', description: 'Submit your access request' },
        { id: 2, label: 'HOD Review', description: 'Head of Department review' },
        { id: 3, label: 'Divisional Director', description: 'Divisional Director approval' },
        { id: 4, label: 'DICT Review', description: 'DICT verification process' },
        { id: 5, label: 'HOD (IT)', description: 'IT Head assessment' },
        { id: 6, label: 'ICT Officer', description: 'ICT Officer processing' },
        { id: 7, label: 'Approved', description: 'Final approval granted' }
      ]

      // Guard this route - only staff can access
      onMounted(() => {
        requireRole([ROLES.STAFF])

        // Check if redirected from form submission
        if (route.query.success === 'true') {
          showSuccessMessage.value = true
          requestType.value = route.query.type || 'request'
          latestRequestId.value = route.query.id || 'REQ-2025-NEW'

          // Clear query parameters
          router.replace({ query: {} })
        }

        loadRequests()
      })

      // Methods
      const loadRequests = async (page = 1, filters = {}) => {
        loading.value = true
        try {
          const response = await requestStatusService.getRequestsPaginated(
            page,
            perPage.value,
            filters
          )

          if (response.success) {
            requests.value = response.data.data || []
            totalRequests.value = response.data.total || 0
            currentPage.value = response.data.current_page || 1
            lastPage.value = response.data.last_page || 1

            console.log('✅ Requests loaded successfully:', {
              total: totalRequests.value,
              current_page: currentPage.value,
              requests_count: requests.value.length
            })
          } else {
            console.error('❌ Failed to load requests:', response.error)
            // Show user-friendly error message
            alert('Failed to load requests: ' + response.error)
          }
        } catch (error) {
          console.error('❌ Error loading requests:', error)
          alert('An error occurred while loading requests. Please try again.')
        } finally {
          loading.value = false
        }
      }

      const refreshRequests = () => {
        loadRequests(currentPage.value)
      }

      const viewRequestDetails = (request) => {
        // Navigate to staff-specific request details page
        // Use original_id (database ID) instead of formatted id
        router.push({
          path: '/request-details',
          query: {
            id: request.original_id || request.id,
            type: request.type
          }
        })
      }

      const goToSubmitRequest = () => {
        router.push('/user-dashboard')
      }

      const submitNewRequest = () => {
        router.push('/user-dashboard')
      }

      // Use service methods for consistent formatting
      const getRequestTypeIcon = (type) => {
        return requestStatusService.getRequestTypeIcon(type)
      }

      const getRequestTypeName = (type) => {
        return requestStatusService.getRequestTypeDisplayName(type)
      }

      const getStatusColor = (status) => {
        return requestStatusService.getStatusColorClass(status)
      }

      const getStatusTextColor = (status) => {
        return requestStatusService.getStatusTextColorClass(status)
      }

      const getStatusText = (status) => {
        return requestStatusService.getStatusDisplayName(status)
      }

      const getCurrentStepText = (step) => {
        const stepObj = approvalSteps.find((s) => s.id === step)
        return stepObj ? stepObj.label : `Step ${step}`
      }

      const getStepStatusClass = (stepId, currentStep, status) => {
        if (stepId < currentStep || (status === 'approved' && stepId <= 7)) {
          return 'bg-green-500'
        } else if (stepId === currentStep && status === 'pending') {
          return 'bg-blue-500'
        } else if (status === 'rejected' && stepId === currentStep) {
          return 'bg-red-500'
        } else {
          return 'bg-gray-300'
        }
      }

      const getStepTextClass = (stepId, currentStep, status) => {
        if (stepId < currentStep || (status === 'approved' && stepId <= 7)) {
          return 'text-green-700'
        } else if (stepId === currentStep) {
          return status === 'rejected' ? 'text-red-700' : 'text-blue-700'
        } else {
          return 'text-gray-500'
        }
      }

      const formatDate = (dateString) => {
        return requestStatusService.formatDate(dateString)
      }

      const formatTime = (dateString) => {
        return requestStatusService.formatTime(dateString)
      }

      return {
        loading,
        requests,
        totalRequests,
        currentPage,
        perPage,
        lastPage,
        showSuccessMessage,
        requestType,
        latestRequestId,
        approvalSteps,
        loadRequests,
        refreshRequests,
        viewRequestDetails,
        goToSubmitRequest,
        submitNewRequest,
        getRequestTypeIcon,
        getRequestTypeName,
        getStatusColor,
        getStatusTextColor,
        getStatusText,
        getCurrentStepText,
        getStepStatusClass,
        getStepTextClass,
        formatDate,
        formatTime
      }
    }
  }
</script>

<style scoped>
  /* Medical Glass morphism effects */
  .medical-glass-card {
    background: rgba(59, 130, 246, 0.15);
    backdrop-filter: blur(25px);
    -webkit-backdrop-filter: blur(25px);
    border: 2px solid rgba(96, 165, 250, 0.3);
    box-shadow:
      0 8px 32px rgba(29, 78, 216, 0.4),
      inset 0 1px 0 rgba(255, 255, 255, 0.1);
  }

  .medical-card {
    position: relative;
    overflow: hidden;
    background: rgba(59, 130, 246, 0.1);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
  }

  /* Table styling */
  table {
    border-collapse: separate;
    border-spacing: 0;
  }

  tbody tr:hover {
    background: rgba(59, 130, 246, 0.1);
  }

  /* Modal improvements */
  .modal-overlay {
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
  }

  /* Ensure modal content is scrollable */
  .modal-body {
    scrollbar-width: thin;
    scrollbar-color: rgba(59, 130, 246, 0.3) transparent;
  }

  .modal-body::-webkit-scrollbar {
    width: 6px;
  }

  .modal-body::-webkit-scrollbar-track {
    background: transparent;
  }

  .modal-body::-webkit-scrollbar-thumb {
    background-color: rgba(59, 130, 246, 0.3);
    border-radius: 3px;
  }

  .modal-body::-webkit-scrollbar-thumb:hover {
    background-color: rgba(59, 130, 246, 0.5);
  }

  /* Animations */
  @keyframes float {
    0%,
    100% {
      transform: translateY(0px);
    }
    50% {
      transform: translateY(-20px);
    }
  }

  .animate-float {
    animation: float 6s ease-in-out infinite;
  }

  /* Responsive improvements */
  @media (max-width: 640px) {
    .modal-overlay {
      padding: 0.5rem;
    }
  }
</style>
