<template>
  <div class="flex flex-col h-screen">
    <Header />
    <div class="flex flex-1 overflow-hidden">
      <ModernSidebar />
      <main
        class="flex-1 p-4 bg-gradient-to-br from-blue-900 via-blue-800 to-teal-900 overflow-y-auto relative"
      >
        <div class="max-w-6xl mx-auto">
          <!-- Header Section -->
          <div class="medical-glass-card rounded-t-3xl p-4 mb-0 border-b border-blue-300/30">
            <div class="text-center">
              <h1 class="text-xl font-bold text-white mb-2 tracking-wide drop-shadow-lg">
                MUHIMBILI NATIONAL HOSPITAL
              </h1>
              <div class="flex justify-center mb-2">
                <div
                  class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-4 py-1.5 rounded-full text-sm font-bold shadow-lg inline-flex items-center gap-2"
                >
                  <i class="fas fa-clipboard-list text-sm"></i>
                  REQUEST DETAILS
                </div>
              </div>
              <h2 class="text-sm font-bold text-blue-100 tracking-wide drop-shadow-md mt-2">
                Internal Access Management
              </h2>
            </div>
          </div>

          <!-- Main Content -->
          <div class="medical-glass-card rounded-b-3xl overflow-hidden">
            <div class="p-6 space-y-6">
              <!-- Loading Message -->
              <div v-if="isLoading" class="text-center py-8">
                <div
                  class="w-16 h-16 border-4 border-blue-600 border-t-transparent rounded-full animate-spin mx-auto mb-4"
                ></div>
                <h2 class="text-white text-xl">Loading request details...</h2>
                <p class="text-blue-300">
                  ID: {{ formattedRequestId }}, Type: {{ formattedRequestType }}
                </p>
              </div>

              <!-- Request Data -->
              <div v-else-if="requestData" class="space-y-8">
                <!-- Approval Trail -->
                <div
                  class="bg-gradient-to-r from-blue-600/30 to-blue-700/30 border-2 border-blue-400/50 p-6 rounded-xl backdrop-blur-sm shadow-lg hover:shadow-blue-500/25 transition-all duration-300"
                >
                  <div class="flex items-center space-x-3 mb-6">
                    <div
                      class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-md"
                    >
                      <i class="fas fa-route text-white text-sm"></i>
                    </div>
                    <h3 class="text-lg font-bold text-white flex items-center">
                      <i class="fas fa-clipboard-check mr-2 text-blue-300"></i>
                      Approval Trail
                    </h3>
                  </div>

                  <!-- Compact Cards for Booking Service -->
                  <div v-if="isBookingService" class="space-y-4">
                    <!-- ICT Officer Status Card -->
                    <div
                      class="bg-gradient-to-r from-blue-600/20 to-blue-700/20 border border-blue-400/40 rounded-lg p-4 backdrop-blur-sm hover:shadow-lg transition-all duration-300"
                    >
                      <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                          <div
                            :class="getApprovalStepClass('ict')"
                            class="w-10 h-10 rounded-lg flex items-center justify-center shadow-md"
                          >
                            <i :class="getApprovalStepIcon('ict')" class="text-white text-sm"></i>
                          </div>
                          <div>
                            <h4 class="text-white font-semibold text-sm">ICT Officer</h4>
                            <p
                              :class="getApprovalStatusTextClass('ict')"
                              class="text-xs font-medium"
                            >
                              {{ getApprovalStatusText('ict') }}
                            </p>
                          </div>
                        </div>
                        <div class="text-right">
                          <p class="text-sm text-blue-300">
                            {{
                              requestData?.ict_approved_at
                                ? formatDate(requestData.ict_approved_at)
                                : requestData?.ictApprovalDate
                                  ? formatDate(requestData.ictApprovalDate)
                                  : 'Pending'
                            }}
                          </p>
                        </div>
                      </div>
                    </div>

                    <!-- ICT Officer Comments Card (Compact) -->
                    <div
                      v-if="
                        (requestData?.ictNotes && requestData?.ictNotes.trim()) ||
                        (requestData?.ict_notes && requestData?.ict_notes.trim())
                      "
                      class="mt-3"
                    >
                      <div
                        :class="getIctCommentsCardClass()"
                        class="rounded-lg p-4 backdrop-blur-sm border transition-all duration-300 hover:shadow-lg"
                      >
                        <!-- Compact Header -->
                        <div class="flex items-center justify-between mb-3">
                          <div class="flex items-center space-x-2">
                            <div
                              :class="getIctCommentsIconBgClass()"
                              class="w-8 h-8 rounded-lg flex items-center justify-center shadow-sm"
                            >
                              <i :class="getIctCommentsIcon()" class="text-white text-sm"></i>
                            </div>
                            <div>
                              <h5 class="text-white font-semibold text-sm">ICT Comments</h5>
                              <p class="text-xs opacity-75" :class="getIctCommentsTextColor()">
                                {{
                                  requestData?.ictStatus === 'rejected'
                                    ? 'Rejection'
                                    : requestData?.ictStatus === 'approved'
                                      ? 'Approval'
                                      : 'Review'
                                }}
                                Reason
                              </p>
                            </div>
                          </div>
                          <div
                            :class="getIctCommentsStatusBadgeClass()"
                            class="px-2 py-1 rounded text-sm font-semibold uppercase"
                          >
                            {{ requestData?.ictStatus }}
                          </div>
                        </div>

                        <!-- Comment Content -->
                        <div class="mb-3">
                          <p class="text-white text-base leading-relaxed">
                            {{ requestData?.ictNotes || requestData?.ict_notes }}
                          </p>
                        </div>

                        <!-- Footer -->
                        <div
                          class="flex justify-between items-center text-xs"
                          :class="getIctCommentsTextColor()"
                        >
                          <div class="flex items-center space-x-1">
                            <i class="fas fa-calendar-alt"></i>
                            <span>{{
                              requestData?.ict_approved_at
                                ? formatDateTime(requestData.ict_approved_at)
                                : requestData?.ictApprovalDate
                                  ? formatDateTime(requestData.ictApprovalDate)
                                  : 'Pending'
                            }}</span>
                          </div>
                          <div class="flex items-center space-x-1">
                            <i class="fas fa-user"></i>
                            <span>{{
                              requestData?.ict_approved_by_name ||
                              requestData?.ictApprovedBy ||
                              'ICT Dept.'
                            }}</span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- For Combined Access Requests: Full approval chain -->
                  <div v-else class="space-y-6">
                    <!-- Request Status Overview -->
                    <div class="mb-6 p-4 bg-white/10 rounded-xl border border-emerald-300/30">
                      <h4 class="text-sm font-bold text-blue-100 mb-3">
                        {{ formattedRequestType }} - Status Overview
                      </h4>
                      <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                        <div class="text-center">
                          <div
                            :class="
                              getCombinedApprovalStatus('hod') === 'approved'
                                ? 'bg-green-500'
                                : getCombinedApprovalStatus('hod') === 'rejected'
                                  ? 'bg-red-500'
                                  : 'bg-yellow-500'
                            "
                            class="w-8 h-8 rounded-full flex items-center justify-center mx-auto mb-1"
                          >
                            <i
                              :class="
                                getCombinedApprovalStatus('hod') === 'approved'
                                  ? 'fas fa-check'
                                  : getCombinedApprovalStatus('hod') === 'rejected'
                                    ? 'fas fa-times'
                                    : 'fas fa-clock'
                              "
                              class="text-white text-sm"
                            ></i>
                          </div>
                          <p class="text-xs text-white">HOD</p>
                        </div>
                        <div class="text-center">
                          <div
                            :class="
                              getCombinedApprovalStatus('divisional') === 'approved'
                                ? 'bg-green-500'
                                : getCombinedApprovalStatus('divisional') === 'rejected'
                                  ? 'bg-red-500'
                                  : 'bg-yellow-500'
                            "
                            class="w-8 h-8 rounded-full flex items-center justify-center mx-auto mb-1"
                          >
                            <i
                              :class="
                                getCombinedApprovalStatus('divisional') === 'approved'
                                  ? 'fas fa-check'
                                  : getCombinedApprovalStatus('divisional') === 'rejected'
                                    ? 'fas fa-times'
                                    : 'fas fa-clock'
                              "
                              class="text-white text-sm"
                            ></i>
                          </div>
                          <p class="text-xs text-white">Divisional</p>
                        </div>
                        <div class="text-center">
                          <div
                            :class="
                              getCombinedApprovalStatus('ict_director') === 'approved'
                                ? 'bg-green-500'
                                : getCombinedApprovalStatus('ict_director') === 'rejected'
                                  ? 'bg-red-500'
                                  : 'bg-yellow-500'
                            "
                            class="w-8 h-8 rounded-full flex items-center justify-center mx-auto mb-1"
                          >
                            <i
                              :class="
                                getCombinedApprovalStatus('ict_director') === 'approved'
                                  ? 'fas fa-check'
                                  : getCombinedApprovalStatus('ict_director') === 'rejected'
                                    ? 'fas fa-times'
                                    : 'fas fa-clock'
                              "
                              class="text-white text-sm"
                            ></i>
                          </div>
                          <p class="text-xs text-white">DICT</p>
                        </div>
                        <div class="text-center">
                          <div
                            :class="
                              getCombinedApprovalStatus('head_it') === 'approved'
                                ? 'bg-green-500'
                                : getCombinedApprovalStatus('head_it') === 'rejected'
                                  ? 'bg-red-500'
                                  : 'bg-yellow-500'
                            "
                            class="w-8 h-8 rounded-full flex items-center justify-center mx-auto mb-1"
                          >
                            <i
                              :class="
                                getCombinedApprovalStatus('head_it') === 'approved'
                                  ? 'fas fa-check'
                                  : getCombinedApprovalStatus('head_it') === 'rejected'
                                    ? 'fas fa-times'
                                    : 'fas fa-clock'
                              "
                              class="text-white text-sm"
                            ></i>
                          </div>
                          <p class="text-xs text-white">Head IT</p>
                        </div>
                        <div class="text-center">
                          <div
                            :class="
                              getCombinedApprovalStatus('ict_officer') === 'approved'
                                ? 'bg-green-500'
                                : getCombinedApprovalStatus('ict_officer') === 'rejected'
                                  ? 'bg-red-500'
                                  : 'bg-yellow-500'
                            "
                            class="w-8 h-8 rounded-full flex items-center justify-center mx-auto mb-1"
                          >
                            <i
                              :class="
                                getCombinedApprovalStatus('ict_officer') === 'approved'
                                  ? 'fas fa-check'
                                  : getCombinedApprovalStatus('ict_officer') === 'rejected'
                                    ? 'fas fa-times'
                                    : 'fas fa-clock'
                              "
                              class="text-white text-sm"
                            ></i>
                          </div>
                          <p class="text-xs text-white">ICT Officer</p>
                        </div>
                      </div>
                    </div>

                    <!-- HOD Approval Card -->
                    <div
                      class="bg-gradient-to-r from-blue-600/20 to-blue-700/20 border border-blue-400/40 rounded-lg p-4 backdrop-blur-sm hover:shadow-lg transition-all duration-300"
                    >
                      <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                          <div
                            :class="getHODApprovalStepClass()"
                            class="w-10 h-10 rounded-lg flex items-center justify-center shadow-md"
                          >
                            <i :class="getHODApprovalStepIcon()" class="text-white text-sm"></i>
                          </div>
                          <div>
                            <h4 class="text-white font-semibold text-sm">Head of Department</h4>
                            <p :class="getHODApprovalStatusTextClass()" class="text-xs font-medium">
                              {{ getHODApprovalStatusText() }}
                            </p>
                          </div>
                        </div>
                        <div class="text-right">
                          <p class="text-sm text-blue-300">
                            {{
                              requestData?.hodApproval?.approved_at ||
                              requestData?.hod_approved_at ||
                              requestData?.hod_rejected_at
                                ? formatDate(
                                    requestData?.hodApproval?.approved_at ||
                                      requestData?.hod_approved_at ||
                                      requestData?.hod_rejected_at
                                  )
                                : getHODApprovalStatus() === 'rejected'
                                  ? 'Recently'
                                  : 'Pending'
                            }}
                          </p>
                        </div>
                      </div>
                    </div>

                    <!-- HOD Comments Card -->
                    <div
                      v-if="getHODComment() || getHODApprovalStatus() === 'rejected'"
                      class="mt-3"
                    >
                      <div
                        :class="getHODCommentsCardClass()"
                        class="rounded-lg p-4 backdrop-blur-sm border transition-all duration-300 hover:shadow-lg"
                      >
                        <!-- Header -->
                        <div class="flex items-center justify-between mb-3">
                          <div class="flex items-center space-x-2">
                            <div
                              :class="getHODCommentsIconBgClass()"
                              class="w-8 h-8 rounded-lg flex items-center justify-center shadow-sm"
                            >
                              <i :class="getHODCommentsIcon()" class="text-white text-sm"></i>
                            </div>
                            <div>
                              <h5 class="text-white font-semibold text-sm">HOD Comments</h5>
                              <p class="text-xs opacity-75" :class="getHODCommentsTextColor()">
                                {{
                                  getHODApprovalStatus() === 'rejected'
                                    ? 'Rejection Reason'
                                    : getHODApprovalStatus() === 'approved'
                                      ? 'Approval Note'
                                      : 'Review Comment'
                                }}
                              </p>
                            </div>
                          </div>
                          <div
                            :class="getHODCommentsStatusBadgeClass()"
                            class="px-2 py-1 rounded text-sm font-semibold uppercase"
                          >
                            {{ getHODApprovalStatus() }}
                          </div>
                        </div>

                        <!-- Comment Content -->
                        <div class="mb-3">
                          <p class="text-white text-base leading-relaxed">
                            {{
                              getHODComment() ||
                              (getHODApprovalStatus() === 'rejected'
                                ? 'No rejection reason provided.'
                                : 'No comment available.')
                            }}
                          </p>
                        </div>

                        <!-- Footer -->
                        <div
                          class="flex justify-between items-center text-xs"
                          :class="getHODCommentsTextColor()"
                        >
                          <div class="flex items-center space-x-1">
                            <i class="fas fa-calendar-alt"></i>
                            <span>{{
                              requestData?.hodApproval?.approved_at ||
                              requestData?.hod_approved_at ||
                              requestData?.hod_rejected_at
                                ? formatDateTime(
                                    requestData?.hodApproval?.approved_at ||
                                      requestData?.hod_approved_at ||
                                      requestData?.hod_rejected_at
                                  )
                                : getHODApprovalStatus() === 'rejected'
                                  ? 'Recently'
                                  : 'Pending'
                            }}</span>
                          </div>
                          <div class="flex items-center space-x-1">
                            <i class="fas fa-user"></i>
                            <span>{{
                              requestData?.hodApproval?.approved_by_name ||
                              requestData?.hod_approved_by_name ||
                              'HOD'
                            }}</span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Basic Request Info -->
                <div
                  class="bg-gradient-to-r from-teal-600/25 to-blue-600/25 border-2 border-teal-400/40 p-6 rounded-2xl backdrop-blur-sm"
                >
                  <h3 class="text-xl font-bold text-white mb-4">Request Information</h3>
                  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 text-white">
                    <div>
                      <p class="text-blue-200 text-sm">Request ID:</p>
                      <p class="font-semibold">{{ formattedRequestId }}</p>
                    </div>
                    <div>
                      <p class="text-blue-200 text-sm">Request Type:</p>
                      <p class="font-semibold">{{ formattedRequestType }}</p>
                    </div>
                    <div>
                      <p class="text-blue-200 text-sm">Status:</p>
                      <p class="font-semibold" :class="getCurrentStatusTextClass()">
                        {{ formattedCurrentStatus }}
                      </p>
                    </div>
                    <div>
                      <p class="text-blue-200 text-sm">ICT Status:</p>
                      <p class="font-semibold">{{ requestData?.ictStatus || 'Pending' }}</p>
                    </div>
                    <!-- Return Status for Booking Service -->
                    <div v-if="isBookingService">
                      <p class="text-blue-200 text-sm">Return Status:</p>
                      <p class="font-semibold flex items-center gap-2">
                        <span
                          :class="
                            getReturnStatusBadgeClass(
                              requestData?.return_status || 'not_yet_returned'
                            )
                          "
                          class="px-2 py-1 rounded-full text-xs font-medium border"
                        >
                          <i
                            :class="
                              getReturnStatusIcon(requestData?.return_status || 'not_yet_returned')
                            "
                            class="mr-1"
                          ></i>
                          {{
                            getReturnStatusText(requestData?.return_status || 'not_yet_returned')
                          }}
                        </span>
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Error State -->
              <div v-else class="text-center py-8">
                <div
                  class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4"
                >
                  <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                </div>
                <h2 class="text-white text-xl">Failed to Load Request</h2>
                <p class="text-red-300">
                  Could not load request details for ID: {{ formattedRequestId }}
                </p>
                <button
                  @click="loadRequestData"
                  class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                >
                  Retry
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <AppFooter />
      </main>
    </div>
  </div>
</template>

<script>
  import { ref, computed, onMounted } from 'vue'
  import { useRoute } from 'vue-router'
  import Header from '@/components/header.vue'
  import ModernSidebar from '@/components/ModernSidebar.vue'
  import AppFooter from '@/components/footer.vue'
  import requestStatusService from '@/services/requestStatusService'

  export default {
    name: 'InternalAccessDetailsFixed',
    components: {
      Header,
      ModernSidebar,
      AppFooter
    },
    setup() {
      const route = useRoute()
      // const router = useRouter()
      // const { userRole, ROLES } = useAuth()

      // Reactive data
      const requestData = ref(null)
      const isLoading = ref(true)
      const requestId = ref(route.query.id || 'N/A')
      const requestType = ref(route.query.type || 'N/A')

      // Computed properties
      const isBookingService = computed(() => {
        return (
          requestData.value?.type === 'booking_service' || requestType.value === 'booking_service'
        )
      })

      // Format request ID with padding like #REQ-000004
      const formattedRequestId = computed(() => {
        if (!requestId.value || requestId.value === 'N/A') return 'N/A'
        // Extract numeric part from request ID and pad with zeros
        const numericMatch = requestId.value.toString().match(/\d+/)
        if (numericMatch) {
          const number = parseInt(numericMatch[0])
          return `#REQ-${number.toString().padStart(6, '0')}`
        }
        // If no numeric part found, just add # prefix
        return `#${requestId.value}`
      })

      // Format request type to show services for combined access
      const formattedRequestType = computed(() => {
        if (requestType.value === 'combined_access' && requestData.value) {
          const services = []

          // Check for different types of services in the request data
          if (
            requestData.value.jeeva_modules?.length > 0 ||
            requestData.value.request_types?.includes('jeeva_access') ||
            requestData.value.request_types?.includes('jeeva')
          ) {
            services.push('Jeeva')
          }

          if (
            requestData.value.wellsoft_modules?.length > 0 ||
            requestData.value.request_types?.includes('wellsoft')
          ) {
            services.push('Wellsoft')
          }

          if (
            requestData.value.internet_purposes?.length > 0 ||
            requestData.value.request_types?.includes('internet_access_request') ||
            requestData.value.request_types?.includes('internet')
          ) {
            services.push('Internet')
          }

          if (services.length > 0) {
            return `Combined Access - ${services.join(', ')}`
          }

          return 'Combined Access'
        }

        // For other request types, just capitalize and format nicely
        if (requestType.value === 'booking_service') {
          return 'Device Booking'
        }

        return (
          requestType.value?.replace(/_/g, ' ').replace(/\b\w/g, (l) => l.toUpperCase()) || 'N/A'
        )
      })

      // Get current status with proper formatting
      const formattedCurrentStatus = computed(() => {
        if (!requestData.value) return 'Loading...'

        const status = requestData.value.status || requestData.value.currentStatus || 'pending'

        // Map status values to user-friendly text
        const statusMap = {
          pending: 'Pending Review',
          pending_hod: 'Pending HOD Approval',
          hod_approved: 'HOD Approved',
          hod_rejected: 'HOD Rejected',
          pending_divisional: 'Pending Divisional Director',
          divisional_approved: 'Divisional Director Approved',
          divisional_rejected: 'Divisional Director Rejected',
          pending_ict_director: 'Pending ICT Director',
          ict_director_approved: 'ICT Director Approved',
          ict_director_rejected: 'ICT Director Rejected',
          pending_head_it: 'Pending Head of IT',
          head_it_approved: 'Head of IT Approved',
          head_it_rejected: 'Head of IT Rejected',
          pending_ict_officer: 'Pending ICT Officer',
          ict_officer_approved: 'ICT Officer Approved',
          ict_officer_rejected: 'ICT Officer Rejected',
          implemented: 'Implemented',
          approved: 'Fully Approved',
          cancelled: 'Cancelled'
        }

        return (
          statusMap[status] || status.replace(/_/g, ' ').replace(/\b\w/g, (l) => l.toUpperCase())
        )
      })

      // Methods
      const loadRequestData = async () => {
        isLoading.value = true
        try {
          if (
            requestId.value &&
            requestType.value &&
            requestId.value !== 'N/A' &&
            requestType.value !== 'N/A'
          ) {
            const response = await requestStatusService.getRequestDetails(
              requestId.value,
              requestType.value
            )

            if (response.success) {
              requestData.value = response.data
              console.log('✅ Request data loaded:', requestData.value)
              console.log('🔍 Request data keys:', Object.keys(requestData.value))
              console.log('🔍 Status field value:', requestData.value.status)
              console.log('🔍 Current status field value:', requestData.value.currentStatus)
              console.log('🔍 All status-related fields:', {
                status: requestData.value.status,
                currentStatus: requestData.value.currentStatus,
                request_status: requestData.value.request_status,
                hod_approval_status: requestData.value.hod_approval_status,
                hodApproval: requestData.value.hodApproval
              })
            } else {
              console.error('❌ Failed to load request:', {
                error: response.error,
                status: response.status,
                data: response.data,
                fullResponse: response
              })
              requestData.value = null
            }
          } else {
            console.warn('⚠️ Missing request ID or type')
            requestData.value = null
          }
        } catch (error) {
          console.error('Error loading request:', error)
          requestData.value = null
        } finally {
          isLoading.value = false
        }
      }

      const formatDate = (dateString) => {
        if (!dateString) return 'N/A'
        try {
          return new Date(dateString).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
          })
        } catch (error) {
          return dateString
        }
      }

      const formatDateTime = (dateString) => {
        if (!dateString) return 'N/A'
        try {
          return new Date(dateString).toLocaleString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
          })
        } catch (error) {
          return dateString
        }
      }

      // ICT Comments UI/UX Methods (simplified versions)
      const getApprovalStepClass = (_step) => {
        const status = requestData.value?.ictStatus || 'pending'
        if (status === 'approved') return 'bg-gradient-to-br from-blue-500 to-green-600'
        if (status === 'rejected') return 'bg-gradient-to-br from-blue-500 to-red-600'
        return 'bg-gradient-to-br from-blue-500 to-yellow-600'
      }

      const getApprovalStepIcon = (_step) => {
        const status = requestData.value?.ictStatus || 'pending'
        if (status === 'approved') return 'fas fa-check'
        if (status === 'rejected') return 'fas fa-times'
        return 'fas fa-clock'
      }

      const getApprovalStatusText = (_step) => {
        const status = requestData.value?.ictStatus || 'pending'
        return status.charAt(0).toUpperCase() + status.slice(1)
      }

      const getApprovalStatusTextClass = (_step) => {
        const status = requestData.value?.ictStatus || 'pending'
        if (status === 'approved') return 'text-green-400'
        if (status === 'rejected') return 'text-red-400'
        return 'text-yellow-400'
      }

      const getIctCommentsCardClass = () => {
        const status = requestData.value?.ictStatus || 'pending'
        if (status === 'approved')
          return 'bg-gradient-to-br from-blue-600/25 to-green-600/25 border-blue-400/60 shadow-blue-500/30'
        if (status === 'rejected')
          return 'bg-gradient-to-br from-blue-600/25 to-red-600/25 border-blue-400/60 shadow-blue-500/25'
        return 'bg-gradient-to-br from-blue-600/25 to-yellow-600/25 border-blue-400/60 shadow-blue-500/25'
      }

      const getIctCommentsIconBgClass = () => {
        const status = requestData.value?.ictStatus || 'pending'
        if (status === 'approved') return 'bg-gradient-to-br from-blue-500 to-green-600'
        if (status === 'rejected') return 'bg-gradient-to-br from-blue-500 to-red-600'
        return 'bg-gradient-to-br from-blue-500 to-yellow-600'
      }

      const getIctCommentsIcon = () => {
        const status = requestData.value?.ictStatus || 'pending'
        if (status === 'approved') return 'fas fa-check-circle'
        if (status === 'rejected') return 'fas fa-times-circle'
        return 'fas fa-clock'
      }

      const getIctCommentsTextColor = () => {
        return 'text-blue-200'
      }

      const getIctCommentsStatusBadgeClass = () => {
        const status = requestData.value?.ictStatus || 'pending'
        if (status === 'approved') return 'bg-green-600 text-green-100 border border-blue-400/50'
        if (status === 'rejected') return 'bg-red-600 text-red-100 border border-blue-400/50'
        return 'bg-yellow-600 text-yellow-100 border border-blue-400/50'
      }

      // HOD Approval Methods
      const getHODApprovalStatus = () => {
        // Use the same logic as formattedCurrentStatus to get the overall status
        const overallStatus = requestData.value?.status || requestData.value?.currentStatus
        const hodSpecificStatus =
          requestData.value?.hodApproval?.status || requestData.value?.hod_approval_status

        console.log('🔍 HOD Status Debug:', {
          overallStatus,
          hodSpecificStatus,
          statusField: requestData.value?.status,
          currentStatusField: requestData.value?.currentStatus,
          fullRequestData: requestData.value
        })

        // First check if the overall request status indicates HOD rejection or approval
        if (overallStatus === 'hod_rejected') {
          console.log('✅ HOD status determined from overall status: rejected')
          return 'rejected'
        }
        if (
          [
            'hod_approved',
            'divisional_approved',
            'ict_director_approved',
            'head_it_approved',
            'implemented',
            'approved'
          ].includes(overallStatus)
        ) {
          console.log('✅ HOD status determined from overall status: approved')
          return 'approved'
        }

        // Fall back to the specific HOD approval status if available
        const finalStatus = hodSpecificStatus || 'pending'
        console.log('✅ HOD status from specific field:', finalStatus)
        return finalStatus
      }

      const getHODComment = () => {
        const comment =
          requestData.value?.hod_comments ||
          requestData.value?.hodApproval?.comment ||
          requestData.value?.hodApproval?.rejection_reason ||
          requestData.value?.hod_approval_comment ||
          requestData.value?.hod_rejection_reason ||
          requestData.value?.hod_comment ||
          requestData.value?.rejection_comment ||
          requestData.value?.rejection_reason

        console.log('🔍 HOD Comment Debug:', {
          comment,
          hodApproval: requestData.value?.hodApproval,
          hod_comments: requestData.value?.hod_comments,
          hod_approval_comment: requestData.value?.hod_approval_comment,
          hod_rejection_reason: requestData.value?.hod_rejection_reason,
          hod_comment: requestData.value?.hod_comment,
          rejection_comment: requestData.value?.rejection_comment,
          rejection_reason: requestData.value?.rejection_reason,
          allCommentFields: {
            hod_comments: requestData.value?.hod_comments,
            'hodApproval?.comment': requestData.value?.hodApproval?.comment,
            'hodApproval?.rejection_reason': requestData.value?.hodApproval?.rejection_reason,
            hod_approval_comment: requestData.value?.hod_approval_comment,
            hod_rejection_reason: requestData.value?.hod_rejection_reason,
            hod_comment: requestData.value?.hod_comment,
            rejection_comment: requestData.value?.rejection_comment,
            rejection_reason: requestData.value?.rejection_reason
          }
        })

        return comment
      }

      const getHODApprovalStepClass = () => {
        const status = getHODApprovalStatus()
        if (status === 'approved') return 'bg-gradient-to-br from-blue-500 to-green-600'
        if (status === 'rejected') return 'bg-gradient-to-br from-blue-500 to-red-600'
        return 'bg-gradient-to-br from-blue-500 to-yellow-600'
      }

      const getHODApprovalStepIcon = () => {
        const status = getHODApprovalStatus()
        if (status === 'approved') return 'fas fa-check'
        if (status === 'rejected') return 'fas fa-times'
        return 'fas fa-clock'
      }

      const getHODApprovalStatusText = () => {
        const status = getHODApprovalStatus()
        return status.charAt(0).toUpperCase() + status.slice(1)
      }

      const getHODApprovalStatusTextClass = () => {
        const status = getHODApprovalStatus()
        if (status === 'approved') return 'text-green-400'
        if (status === 'rejected') return 'text-red-400'
        return 'text-yellow-400'
      }

      const getHODCommentsCardClass = () => {
        // Always return blue background regardless of status
        return 'bg-gradient-to-br from-blue-600/30 to-blue-700/30 border-blue-400/60 shadow-blue-500/25'
      }

      const getHODCommentsIconBgClass = () => {
        const status = getHODApprovalStatus()
        if (status === 'approved') return 'bg-gradient-to-br from-blue-500 to-green-600'
        if (status === 'rejected') return 'bg-gradient-to-br from-blue-500 to-red-600'
        return 'bg-gradient-to-br from-blue-500 to-yellow-600'
      }

      const getHODCommentsIcon = () => {
        const status = getHODApprovalStatus()
        if (status === 'approved') return 'fas fa-check-circle'
        if (status === 'rejected') return 'fas fa-times-circle'
        return 'fas fa-clock'
      }

      const getHODCommentsTextColor = () => {
        return 'text-blue-200'
      }

      const getHODCommentsStatusBadgeClass = () => {
        const status = getHODApprovalStatus()
        if (status === 'approved') return 'bg-green-600 text-green-100 border border-blue-400/50'
        if (status === 'rejected') return 'bg-red-600 text-red-100 border border-blue-400/50'
        return 'bg-yellow-600 text-yellow-100 border border-blue-400/50'
      }

      // Combined Access Request Status Helper
      const getCombinedApprovalStatus = (stage) => {
        if (!requestData.value) return 'pending'

        const status = requestData.value.status || requestData.value.currentStatus || 'pending'

        switch (stage) {
          case 'hod':
            if (
              [
                'hod_approved',
                'divisional_approved',
                'ict_director_approved',
                'head_it_approved',
                'implemented',
                'approved'
              ].includes(status)
            ) {
              return 'approved'
            }
            if (status === 'hod_rejected') {
              return 'rejected'
            }
            return 'pending'

          case 'divisional':
            if (
              [
                'divisional_approved',
                'ict_director_approved',
                'head_it_approved',
                'implemented',
                'approved'
              ].includes(status)
            ) {
              return 'approved'
            }
            if (status === 'divisional_rejected') {
              return 'rejected'
            }
            if (['hod_approved', 'pending_divisional'].includes(status)) {
              return 'pending'
            }
            return 'not_reached'

          case 'ict_director':
            if (
              ['ict_director_approved', 'head_it_approved', 'implemented', 'approved'].includes(
                status
              )
            ) {
              return 'approved'
            }
            if (status === 'ict_director_rejected') {
              return 'rejected'
            }
            if (['divisional_approved', 'pending_ict_director'].includes(status)) {
              return 'pending'
            }
            return 'not_reached'

          case 'head_it':
            if (['head_it_approved', 'implemented', 'approved'].includes(status)) {
              return 'approved'
            }
            if (status === 'head_it_rejected') {
              return 'rejected'
            }
            if (['ict_director_approved', 'pending_head_it'].includes(status)) {
              return 'pending'
            }
            return 'not_reached'

          case 'ict_officer':
            if (['implemented', 'approved'].includes(status)) {
              return 'approved'
            }
            if (status === 'ict_officer_rejected') {
              return 'rejected'
            }
            if (['head_it_approved', 'pending_ict_officer'].includes(status)) {
              return 'pending'
            }
            return 'not_reached'

          default:
            return 'pending'
        }
      }

      // Return Status Helper Methods
      const getReturnStatusBadgeClass = (returnStatus) => {
        const statusClasses = {
          not_yet_returned: 'bg-blue-100 text-blue-800 border-blue-200',
          returned: 'bg-green-100 text-green-800 border-green-200',
          returned_but_compromised: 'bg-red-100 text-red-800 border-red-200'
        }
        return statusClasses[returnStatus] || statusClasses.not_yet_returned
      }

      const getReturnStatusIcon = (returnStatus) => {
        const statusIcons = {
          not_yet_returned: 'fas fa-hourglass-half',
          returned: 'fas fa-check-circle',
          returned_but_compromised: 'fas fa-exclamation-triangle'
        }
        return statusIcons[returnStatus] || statusIcons.not_yet_returned
      }

      const getReturnStatusText = (returnStatus) => {
        const statusTexts = {
          not_yet_returned: 'Not Returned',
          returned: 'Returned',
          returned_but_compromised: 'Compromised'
        }
        return statusTexts[returnStatus] || returnStatus || 'Unknown'
      }

      // Auto-refresh functionality
      const setupAutoRefresh = () => {
        // Refresh data when window regains focus (useful when user comes back from approval page)
        const handleVisibilityChange = () => {
          if (!document.hidden) {
            console.log('🔄 Window regained focus, refreshing request data...')
            loadRequestData()
          }
        }

        const handleFocus = () => {
          console.log('🔄 Window focused, refreshing request data...')
          loadRequestData()
        }

        document.addEventListener('visibilitychange', handleVisibilityChange)
        window.addEventListener('focus', handleFocus)

        // Clean up listeners when component unmounts
        return () => {
          document.removeEventListener('visibilitychange', handleVisibilityChange)
          window.removeEventListener('focus', handleFocus)
        }
      }

      // Get current status text color class
      const getCurrentStatusTextClass = () => {
        if (!requestData.value) return 'text-white'

        const status = requestData.value.status || requestData.value.currentStatus || 'pending'

        // Check if status indicates rejection
        if (status.includes('rejected') || status === 'rejected') {
          return 'text-red-400'
        }

        // Check if status indicates approval/success
        if (status.includes('approved') || status === 'approved' || status === 'implemented') {
          return 'text-green-400'
        }

        // Check if status indicates pending
        if (status.includes('pending') || status === 'pending') {
          return 'text-yellow-400'
        }

        // Check if status indicates cancellation
        if (status === 'cancelled') {
          return 'text-gray-400'
        }

        // Default to white text
        return 'text-white'
      }

      // Lifecycle
      onMounted(() => {
        loadRequestData()
        setupAutoRefresh()
      })

      return {
        requestData,
        isLoading,
        requestId,
        requestType,
        formattedRequestId,
        formattedRequestType,
        formattedCurrentStatus,
        getCurrentStatusTextClass,
        isBookingService,
        loadRequestData,
        formatDate,
        formatDateTime,
        getApprovalStepClass,
        getApprovalStepIcon,
        getApprovalStatusText,
        getApprovalStatusTextClass,
        getIctCommentsCardClass,
        getIctCommentsIconBgClass,
        getIctCommentsIcon,
        getIctCommentsTextColor,
        getIctCommentsStatusBadgeClass,
        // HOD Methods
        getHODApprovalStatus,
        getHODComment,
        getHODApprovalStepClass,
        getHODApprovalStepIcon,
        getHODApprovalStatusText,
        getHODApprovalStatusTextClass,
        getHODCommentsCardClass,
        getHODCommentsIconBgClass,
        getHODCommentsIcon,
        getHODCommentsTextColor,
        getHODCommentsStatusBadgeClass,
        // Combined Access Request Status
        getCombinedApprovalStatus,
        // Return status methods
        getReturnStatusBadgeClass,
        getReturnStatusIcon,
        getReturnStatusText
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
</style>
