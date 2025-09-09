<template>
  <div class="flex flex-col h-screen">
    <Header />
    <div class="flex flex-1 overflow-hidden">
      <ModernSidebar />
      <main
        class="flex-1 p-6 bg-gradient-to-br from-blue-900 via-blue-800 to-teal-900 overflow-y-auto relative"
      >
        <div class="max-w-5xl mx-auto">
          <!-- Header Section -->
          <div class="medical-glass-card rounded-t-3xl p-8 mb-0 border-b border-blue-300/30">
            <div class="flex justify-between items-center">
              <!-- Center Content -->
              <div class="text-center flex-1">
                <h1 class="text-2xl font-bold text-white mb-4 tracking-wide drop-shadow-lg">
                  MUHIMBILI NATIONAL HOSPITAL
                </h1>
                <div
                  class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-2 rounded-full text-base font-bold shadow-2xl"
                >
                  <span class="relative z-10 flex items-center gap-3">
                    <i class="fas fa-clipboard-list text-base"></i>
                    REQUEST DETAILS
                  </span>
                </div>
                <h2 class="text-lg font-bold text-blue-100 tracking-wide drop-shadow-md mt-4">
                  Internal Access Management
                </h2>
              </div>
            </div>
          </div>

          <!-- Main Content -->
          <div class="medical-glass-card rounded-b-3xl overflow-hidden">
            <div class="p-6 space-y-8">
              <!-- Loading Message -->
              <div v-if="isLoading" class="text-center py-8">
                <div
                  class="w-16 h-16 border-4 border-blue-600 border-t-transparent rounded-full animate-spin mx-auto mb-4"
                ></div>
                <h2 class="text-white text-xl">Loading request details...</h2>
                <p class="text-blue-300">ID: {{ requestId }}, Type: {{ requestType }}</p>
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
                          <p class="text-xs text-blue-300">
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
                            class="px-2 py-1 rounded text-xs font-semibold uppercase"
                          >
                            {{ requestData?.ictStatus }}
                          </div>
                        </div>

                        <!-- Comment Content -->
                        <div class="mb-3">
                          <p class="text-white text-sm leading-relaxed">
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

                  <!-- For Access Requests: Full approval chain -->
                  <div v-else class="grid grid-cols-2 md:grid-cols-5 gap-3">
                    <div class="text-center text-white text-sm">
                      Access Request Approval Chain (Not Booking Service)
                    </div>
                  </div>
                </div>

                <!-- Basic Request Info -->
                <div
                  class="bg-gradient-to-r from-teal-600/25 to-blue-600/25 border-2 border-teal-400/40 p-6 rounded-2xl backdrop-blur-sm"
                >
                  <h3 class="text-xl font-bold text-white mb-4">Request Information</h3>
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-white">
                    <div>
                      <p class="text-blue-200 text-sm">Request ID:</p>
                      <p class="font-semibold">{{ requestId }}</p>
                    </div>
                    <div>
                      <p class="text-blue-200 text-sm">Request Type:</p>
                      <p class="font-semibold">{{ requestType }}</p>
                    </div>
                    <div>
                      <p class="text-blue-200 text-sm">Status:</p>
                      <p class="font-semibold">{{ requestData?.currentStatus || 'Pending' }}</p>
                    </div>
                    <div>
                      <p class="text-blue-200 text-sm">ICT Status:</p>
                      <p class="font-semibold">{{ requestData?.ictStatus || 'Pending' }}</p>
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
                <p class="text-red-300">Could not load request details for ID: {{ requestId }}</p>
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

    <!-- Loading Modal -->
    <div
      v-if="isLoading"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
      <div class="bg-white rounded-xl shadow-2xl p-8 text-center">
        <div
          class="w-16 h-16 border-4 border-blue-600 border-t-transparent rounded-full animate-spin mx-auto mb-4"
        ></div>
        <p class="text-gray-600">Loading request details...</p>
      </div>
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
            } else {
              console.error('❌ Failed to load request:', response.error)
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

      // Lifecycle
      onMounted(() => {
        loadRequestData()
      })

      return {
        requestData,
        isLoading,
        requestId,
        requestType,
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
        getIctCommentsStatusBadgeClass
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
