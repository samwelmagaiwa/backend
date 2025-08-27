<template>
  <div class="flex flex-col h-screen">
    <AppHeader />
    <div class="flex flex-1 overflow-hidden">
      <ModernSidebar v-model:collapsed="sidebarCollapsed" />
      <main class="flex-1 p-3 bg-gradient-to-br from-blue-900 via-blue-800 to-teal-900 overflow-y-auto relative">
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
              <div v-if="showSuccessMessage" class="medical-card bg-gradient-to-r from-green-600/25 to-green-700/25 border-2 border-green-400/40 p-6 rounded-2xl">
                <div class="flex items-center space-x-4">
                  <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
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
                      <div class="bg-green-500/20 px-3 py-1 rounded-full border border-green-400/30">
                        <span class="text-green-300 text-sm font-medium">
                          Request ID: #{{ latestRequestId }}
                        </span>
                      </div>
                      <div class="bg-green-500/20 px-3 py-1 rounded-full border border-green-400/30">
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
              <div class="medical-card bg-gradient-to-r from-blue-600/25 to-blue-700/25 border-2 border-blue-400/40 p-6 rounded-2xl">
                <div class="flex items-center justify-between mb-6">
                  <div class="flex items-center space-x-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
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
                        Total: {{ requests.length }}
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
                  <div class="w-20 h-20 bg-blue-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-inbox text-blue-400 text-2xl"></i>
                  </div>
                  <h4 class="text-lg font-semibold text-white mb-2">No Requests Found</h4>
                  <p class="text-blue-200/80 text-sm mb-6">You haven't submitted any requests yet.</p>
                  <button
                    @click="goToSubmitRequest"
                    class="px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl font-medium hover:from-blue-600 hover:to-blue-700 transition-all duration-300"
                  >
                    <i class="fas fa-plus mr-2"></i>
                    Submit New Request
                  </button>
                </div>

                <!-- Requests Table -->
                <div v-else class="overflow-x-auto">
                  <table class="w-full">
                    <thead>
                      <tr class="border-b border-blue-400/30">
                        <th class="text-left py-3 px-4 text-blue-200 font-semibold text-sm">Request ID</th>
                        <th class="text-left py-3 px-4 text-blue-200 font-semibold text-sm">Type</th>
                        <th class="text-left py-3 px-4 text-blue-200 font-semibold text-sm">Services</th>
                        <th class="text-left py-3 px-4 text-blue-200 font-semibold text-sm">Status</th>
                        <th class="text-left py-3 px-4 text-blue-200 font-semibold text-sm">Current Step</th>
                        <th class="text-left py-3 px-4 text-blue-200 font-semibold text-sm">Submitted</th>
                        <th class="text-left py-3 px-4 text-blue-200 font-semibold text-sm">Actions</th>
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
                            <i :class="getRequestTypeIcon(request.type)" class="text-blue-400"></i>
                            <span class="text-white text-sm">{{ getRequestTypeName(request.type) }}</span>
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
                          <div class="text-white text-sm">{{ formatDate(request.created_at) }}</div>
                          <div class="text-blue-300 text-xs">{{ formatTime(request.created_at) }}</div>
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
              </div>

              <!-- Request Details Modal -->
              <div
                v-if="selectedRequest"
                class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 backdrop-blur-sm p-4"
              >
                <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
                  <!-- Modal Header -->
                  <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-6 text-white">
                    <div class="flex items-center justify-between">
                      <div>
                        <h3 class="text-xl font-bold">Request Details</h3>
                        <p class="text-blue-100 text-sm">Request ID: #{{ selectedRequest.id }}</p>
                      </div>
                      <button
                        @click="selectedRequest = null"
                        class="w-8 h-8 bg-white/20 hover:bg-white/30 rounded-lg flex items-center justify-center transition-colors"
                      >
                        <i class="fas fa-times"></i>
                      </button>
                    </div>
                  </div>

                  <!-- Modal Body -->
                  <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
                    <div class="space-y-6">

                      <!-- Request Information -->
                      <div class="bg-gray-50 rounded-xl p-4">
                        <h4 class="font-semibold text-gray-800 mb-3">Request Information</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                          <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Type</label>
                            <div class="text-gray-800">{{ getRequestTypeName(selectedRequest.type) }}</div>
                          </div>
                          <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Status</label>
                            <div class="flex items-center space-x-2">
                              <div
                                class="w-2 h-2 rounded-full"
                                :class="getStatusColor(selectedRequest.status)"
                              ></div>
                              <span :class="getStatusTextColor(selectedRequest.status)">
                                {{ getStatusText(selectedRequest.status) }}
                              </span>
                            </div>
                          </div>
                          <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Submitted</label>
                            <div class="text-gray-800">{{ formatDate(selectedRequest.created_at) }} at {{ formatTime(selectedRequest.created_at) }}</div>
                          </div>
                          <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Current Step</label>
                            <div class="text-gray-800">{{ getCurrentStepText(selectedRequest.current_step) }}</div>
                          </div>
                        </div>
                      </div>

                      <!-- Services Requested -->
                      <div class="bg-gray-50 rounded-xl p-4">
                        <h4 class="font-semibold text-gray-800 mb-3">Services Requested</h4>
                        <div class="flex flex-wrap gap-2">
                          <span
                            v-for="service in selectedRequest.services"
                            :key="service"
                            class="px-3 py-1 bg-blue-100 text-blue-800 text-sm rounded-full border border-blue-200"
                          >
                            {{ service }}
                          </span>
                        </div>
                      </div>

                      <!-- HOD Comments/Recommendations (if rejected or has recommendations) -->
                      <div
                        v-if="selectedRequest.hod_comments || selectedRequest.recommendations"
                        class="bg-red-50 rounded-xl p-4 border border-red-200"
                      >
                        <h4 class="font-semibold text-red-800 mb-3 flex items-center">
                          <i class="fas fa-comment-alt mr-2"></i>
                          {{ selectedRequest.status === 'rejected' ? 'Rejection Comments' : 'HOD Recommendations' }}
                        </h4>

                        <!-- Rejection Comments -->
                        <div v-if="selectedRequest.hod_comments" class="mb-4">
                          <label class="block text-sm font-medium text-red-700 mb-2">
                            Comments from HOD
                          </label>
                          <div class="bg-white rounded-lg border border-red-200 p-3">
                            <textarea
                              :value="selectedRequest.hod_comments"
                              readonly
                              class="w-full h-24 text-gray-800 text-sm resize-none border-none outline-none bg-transparent"
                              placeholder="No comments provided"
                            ></textarea>
                          </div>
                        </div>

                        <!-- Recommendations -->
                        <div v-if="selectedRequest.recommendations" class="mb-4">
                          <label class="block text-sm font-medium text-orange-700 mb-2">
                            Recommendations from HOD
                          </label>
                          <div class="bg-white rounded-lg border border-orange-200 p-3">
                            <textarea
                              :value="selectedRequest.recommendations"
                              readonly
                              class="w-full h-24 text-gray-800 text-sm resize-none border-none outline-none bg-transparent"
                              placeholder="No recommendations provided"
                            ></textarea>
                          </div>
                        </div>

                        <!-- Action Required Notice -->
                        <div v-if="selectedRequest.status === 'rejected'" class="bg-red-100 border border-red-300 rounded-lg p-3">
                          <div class="flex items-center space-x-2">
                            <i class="fas fa-exclamation-triangle text-red-600"></i>
                            <span class="text-red-800 text-sm font-medium">
                              This request has been rejected. Please review the comments and submit a new request if needed.
                            </span>
                          </div>
                        </div>
                      </div>

                      <!-- Approval Progress -->
                      <div class="bg-gray-50 rounded-xl p-4">
                        <h4 class="font-semibold text-gray-800 mb-4">Approval Progress</h4>
                        <div class="space-y-3">
                          <div
                            v-for="step in approvalSteps"
                            :key="step.id"
                            class="flex items-center space-x-3"
                          >
                            <div
                              class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium"
                              :class="getStepStatusClass(step.id, selectedRequest.current_step, selectedRequest.status)"
                            >
                              <i
                                v-if="step.id < selectedRequest.current_step || (selectedRequest.status === 'approved' && step.id <= 7)"
                                class="fas fa-check text-white"
                              ></i>
                              <i
                                v-else-if="step.id === selectedRequest.current_step && selectedRequest.status === 'pending'"
                                class="fas fa-clock text-white"
                              ></i>
                              <span v-else class="text-gray-500">{{ step.id }}</span>
                            </div>
                            <div class="flex-1">
                              <div
                                class="font-medium"
                                :class="getStepTextClass(step.id, selectedRequest.current_step, selectedRequest.status)"
                              >
                                {{ step.label }}
                              </div>
                              <div class="text-sm text-gray-500">{{ step.description }}</div>
                            </div>
                            <div
                              v-if="step.id === selectedRequest.current_step && selectedRequest.status === 'pending'"
                              class="text-blue-600 text-sm font-medium"
                            >
                              Current
                            </div>
                          </div>
                        </div>
                      </div>

                    </div>
                  </div>

                  <!-- Modal Footer -->
                  <div class="bg-gray-50 px-6 py-4 flex justify-end space-x-3">
                    <button
                      @click="selectedRequest = null"
                      class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg font-medium hover:bg-gray-300 transition-colors"
                    >
                      Close
                    </button>
                    <button
                      v-if="selectedRequest.status === 'rejected'"
                      @click="submitNewRequest"
                      class="px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors"
                    >
                      <i class="fas fa-plus mr-2"></i>
                      Submit New Request
                    </button>
                  </div>
                </div>
              </div>

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
    const sidebarCollapsed = ref(false)
    const loading = ref(false)
    const selectedRequest = ref(null)
    const showSuccessMessage = ref(false)
    const requestType = ref('')
    const latestRequestId = ref('')

    // Mock data - replace with actual API calls
    const requests = ref([
      {
        id: 'REQ-2025-001',
        type: 'combined_access',
        services: ['Jeeva', 'Wellsoft', 'Internet'],
        status: 'pending',
        current_step: 3,
        created_at: '2025-01-27T10:30:00Z',
        hod_comments: null,
        recommendations: null
      },
      {
        id: 'REQ-2025-002',
        type: 'booking_service',
        services: ['Projector', 'Laptop'],
        status: 'approved',
        current_step: 7,
        created_at: '2025-01-25T14:15:00Z',
        hod_comments: null,
        recommendations: null
      },
      {
        id: 'REQ-2025-003',
        type: 'combined_access',
        services: ['Internet'],
        status: 'rejected',
        current_step: 2,
        created_at: '2025-01-24T09:45:00Z',
        hod_comments: 'The justification provided is insufficient. Please provide more detailed reasons for internet access requirement and specify the intended use cases.',
        recommendations: 'Consider submitting a more comprehensive request with specific business justification and time-bound usage requirements.'
      }
    ])

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
    const loadRequests = async() => {
      loading.value = true
      try {
        // Simulate API call
        await new Promise(resolve => setTimeout(resolve, 1000))
        // In real implementation, fetch from API
        console.log('Requests loaded:', requests.value.length)
      } catch (error) {
        console.error('Failed to load requests:', error)
      } finally {
        loading.value = false
      }
    }

    const refreshRequests = () => {
      loadRequests()
    }

    const viewRequestDetails = (request) => {
      selectedRequest.value = request
    }

    const goToSubmitRequest = () => {
      router.push('/user-dashboard')
    }

    const submitNewRequest = () => {
      selectedRequest.value = null
      router.push('/user-dashboard')
    }

    const getRequestTypeIcon = (type) => {
      const icons = {
        combined_access: 'fas fa-layer-group',
        booking_service: 'fas fa-calendar-check',
        jeeva_access: 'fas fa-file-medical',
        wellsoft_access: 'fas fa-laptop-medical',
        internet_access: 'fas fa-wifi'
      }
      return icons[type] || 'fas fa-file-alt'
    }

    const getRequestTypeName = (type) => {
      const names = {
        combined_access: 'Combined Access',
        booking_service: 'Booking Service',
        jeeva_access: 'Jeeva Access',
        wellsoft_access: 'Wellsoft Access',
        internet_access: 'Internet Access'
      }
      return names[type] || 'Unknown'
    }

    const getStatusColor = (status) => {
      const colors = {
        pending: 'bg-yellow-400 animate-pulse',
        approved: 'bg-green-400',
        rejected: 'bg-red-400',
        in_progress: 'bg-blue-400 animate-pulse'
      }
      return colors[status] || 'bg-gray-400'
    }

    const getStatusTextColor = (status) => {
      const colors = {
        pending: 'text-yellow-400',
        approved: 'text-green-400',
        rejected: 'text-red-400',
        in_progress: 'text-blue-400'
      }
      return colors[status] || 'text-gray-400'
    }

    const getStatusText = (status) => {
      const texts = {
        pending: 'Pending',
        approved: 'Approved',
        rejected: 'Rejected',
        in_progress: 'In Progress'
      }
      return texts[status] || 'Unknown'
    }

    const getCurrentStepText = (step) => {
      const stepObj = approvalSteps.find(s => s.id === step)
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
      const date = new Date(dateString)
      return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      })
    }

    const formatTime = (dateString) => {
      const date = new Date(dateString)
      return date.toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit'
      })
    }

    return {
      sidebarCollapsed,
      loading,
      requests,
      selectedRequest,
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
  box-shadow: 0 8px 32px rgba(29, 78, 216, 0.4),
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

/* Animations */
@keyframes float {
  0%, 100% {
    transform: translateY(0px);
  }
  50% {
    transform: translateY(-20px);
  }
}

.animate-float {
  animation: float 6s ease-in-out infinite;
}
</style>
