<template>
  <div class="flex flex-col h-screen">
    <!-- Top Right Success Notification -->
    <transition name="slide-down">
      <div
        v-if="showSuccessNotification"
        class="fixed top-4 right-4 bg-green-500 text-white px-4 py-3 rounded-lg shadow-xl max-w-sm"
        style="z-index: 9999"
      >
        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <i class="fas fa-check-circle text-lg mr-3"></i>
            <div>
              <h3 class="font-semibold text-base">Access Granted Successfully!</h3>
              <p class="text-green-100 text-sm">
                Your access request has been processed and access has been granted.
              </p>
            </div>
          </div>
          <button
            @click="hideSuccessNotification"
            class="text-white hover:text-green-200 transition-colors ml-3 flex-shrink-0"
          >
            <i class="fas fa-times text-sm"></i>
          </button>
        </div>
      </div>
    </transition>

    <Header />
    <div class="flex flex-1 overflow-hidden">
      <ModernSidebar />
      <main
        class="flex-1 p-6 bg-gradient-to-br from-blue-900 via-blue-800 to-teal-900 overflow-y-auto relative"
      >
        <!-- Medical Background Pattern -->
        <div class="absolute inset-0 overflow-hidden">
          <!-- Medical Cross Pattern -->
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
          <!-- Floating medical icons -->
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
                  ['fa-laptop-code', 'fa-server', 'fa-network-wired', 'fa-shield-alt', 'fa-cogs'][
                    Math.floor(Math.random() * 5)
                  ]
                ]"
              ></i>
            </div>
          </div>
        </div>

        <div class="max-w-full mx-auto relative z-10">
          <!-- Header Section -->
          <div class="medical-glass-card rounded-t-3xl p-4 mb-0 border-b border-blue-300/30">
            <div class="flex items-center justify-between">
              <button
                @click="goBack"
                class="flex items-center px-4 py-2 bg-white/20 hover:bg-white/30 text-white rounded-lg transition-all duration-200 backdrop-blur-sm"
              >
                <i class="fas fa-arrow-left mr-2"></i>
                Back
              </button>
              <div class="text-center">
                <h1 class="text-2xl font-bold text-blue-100 mb-1">User Security Access</h1>
                <p class="text-blue-200 text-sm">Request ID: {{ requestId }}</p>
              </div>
              <div class="text-right">
                <div class="flex items-center">
                  <i class="fas fa-circle text-green-300 text-xs mr-2"></i>
                  <span class="text-white text-sm font-medium">{{ currentStatus }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Main Content -->
          <div class="medical-glass-card rounded-b-3xl overflow-hidden">
            <div class="p-6">
              <!-- Loading State -->
              <div v-if="loading" class="flex items-center justify-center py-16">
                <div class="text-center">
                  <div
                    class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-300 mx-auto mb-4"
                  ></div>
                  <p class="text-blue-200">Loading access request details...</p>
                </div>
              </div>

              <!-- Error State -->
              <div v-else-if="error" class="p-8 text-center">
                <div class="text-red-400 mb-4">
                  <i class="fas fa-exclamation-triangle text-4xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-white mb-2">Error Loading Request</h3>
                <p class="text-blue-200 mb-4">{{ error }}</p>
                <button
                  @click="loadRequestData"
                  class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                >
                  <i class="fas fa-redo mr-2"></i>
                  Retry
                </button>
              </div>

              <!-- Debug Information (remove in production) -->
              <div
                v-else-if="requestData && $route.query.debug"
                class="p-4 bg-blue-900/30 border border-blue-500/30 rounded-lg mb-6"
              >
                <details class="text-sm">
                  <summary class="font-semibold cursor-pointer text-blue-200">
                    Debug: Raw Request Data
                  </summary>
                  <pre class="mt-2 p-2 bg-gray-800 text-green-400 rounded text-xs overflow-auto">{{
                    JSON.stringify(requestData, null, 2)
                  }}</pre>
                </details>
              </div>

              <!-- Request Content -->
              <div v-else-if="requestData">
                <!-- Requirements Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                  <!-- Left Column: Basic Info -->
                  <div class="space-y-6">
                    <div
                      class="medical-card bg-gradient-to-r from-blue-600/25 to-cyan-600/25 border-2 border-blue-400/40 rounded-xl p-6"
                    >
                      <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                        <i class="fas fa-user mr-2"></i>
                        Requester Information
                      </h3>
                      <div class="space-y-3">
                        <div class="flex justify-between">
                          <span class="text-blue-200">PF Number:</span>
                          <span class="font-semibold text-white">{{
                            requestData.shared?.pfNumber ||
                            requestData.pf_number ||
                            requestData.pfNumber ||
                            'N/A'
                          }}</span>
                        </div>
                        <div class="flex justify-between">
                          <span class="text-blue-200">Staff Name:</span>
                          <span class="font-semibold text-white">{{
                            requestData.shared?.staffName ||
                            requestData.staff_name ||
                            requestData.staffName ||
                            requestData.name ||
                            'N/A'
                          }}</span>
                        </div>
                        <div class="flex justify-between">
                          <span class="text-blue-200">Department:</span>
                          <span class="font-semibold text-white">{{
                            requestData.shared?.department ||
                            requestData.department ||
                            requestData.dept ||
                            'N/A'
                          }}</span>
                        </div>
                        <div class="flex justify-between">
                          <span class="text-blue-200">Contact:</span>
                          <span class="font-semibold text-white">{{
                            requestData.shared?.phone ||
                            requestData.phone ||
                            requestData.contact ||
                            'N/A'
                          }}</span>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Right Column: Status -->
                  <div class="space-y-6">
                    <div
                      class="medical-card bg-gradient-to-r from-green-600/25 to-emerald-600/25 border-2 border-green-400/40 rounded-xl p-6"
                    >
                      <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        Current Status
                      </h3>
                      <div class="space-y-3">
                        <div class="flex justify-between">
                          <span class="text-green-200">Status:</span>
                          <span
                            class="px-3 py-1 bg-green-600/30 text-green-200 rounded-full text-sm font-medium border border-green-400/40"
                          >
                            {{ getStatusLabel(requestData.status) }}
                          </span>
                        </div>
                        <div class="flex justify-between">
                          <span class="text-green-200">Progress:</span>
                          <span class="font-semibold text-white">{{ getProgress() }}</span>
                        </div>
                        <div class="flex justify-between">
                          <span class="text-green-200">Last Updated:</span>
                          <span class="font-semibold text-white">{{
                            formatDate(requestData.updated_at)
                          }}</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Modules Selected Section -->
                <div class="mb-8">
                  <h3 class="text-xl font-semibold text-white mb-6 flex items-center">
                    <i class="fas fa-th-large mr-2 text-blue-300"></i>
                    Modules Selected
                  </h3>
                  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Wellsoft Modules -->
                    <div
                      class="medical-card bg-gradient-to-r from-blue-600/25 to-blue-600/25 border-2 border-blue-400/40 rounded-xl p-6"
                    >
                      <h4 class="text-lg font-semibold text-white mb-4 flex items-center">
                        <i class="fas fa-laptop mr-2"></i>
                        Wellsoft Modules
                      </h4>
                      <div v-if="wellsoftModules.length > 0" class="space-y-2">
                        <div
                          v-for="module in wellsoftModules"
                          :key="module"
                          class="flex items-center text-sm text-blue-200"
                        >
                          <i class="fas fa-check text-blue-400 mr-2 text-xs"></i>
                          {{ module }}
                        </div>
                      </div>
                      <p v-else class="text-blue-300 italic text-sm">
                        No Wellsoft modules requested
                      </p>
                    </div>

                    <!-- Jeeva Modules -->
                    <div
                      class="medical-card bg-gradient-to-r from-cyan-600/25 to-cyan-600/25 border-2 border-cyan-400/40 rounded-xl p-6"
                    >
                      <h4 class="text-lg font-semibold text-white mb-4 flex items-center">
                        <i class="fas fa-box mr-2"></i>
                        Jeeva Modules
                      </h4>
                      <div v-if="jeevaModules.length > 0" class="space-y-2">
                        <div
                          v-for="module in jeevaModules"
                          :key="module"
                          class="flex items-center text-sm text-cyan-200"
                        >
                          <i class="fas fa-check text-cyan-400 mr-2 text-xs"></i>
                          {{ module }}
                        </div>
                      </div>
                      <p v-else class="text-cyan-300 italic text-sm">No Jeeva modules requested</p>
                    </div>

                    <!-- Internet Purposes -->
                    <div
                      class="medical-card bg-gradient-to-r from-green-600/25 to-green-600/25 border-2 border-green-400/40 rounded-xl p-6"
                    >
                      <h4 class="text-lg font-semibold text-white mb-4 flex items-center">
                        <i class="fas fa-globe mr-2"></i>
                        Internet Purposes
                      </h4>
                      <div v-if="internetPurposes.length > 0" class="space-y-2">
                        <div
                          v-for="(purpose, index) in internetPurposes"
                          :key="index"
                          class="flex items-center text-sm text-green-200"
                        >
                          <span
                            class="w-4 h-4 bg-green-600/30 text-green-200 rounded-full flex items-center justify-center text-xs mr-2 border border-green-400/40"
                          >
                            {{ index + 1 }}
                          </span>
                          {{ purpose }}
                        </div>
                      </div>
                      <p v-else class="text-green-300 italic text-sm">
                        No internet purposes specified
                      </p>
                    </div>
                  </div>
                </div>

                <!-- Comments Section - Side by Side -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                  <!-- Head of Department Comments (Left) -->
                  <div v-if="hodComments" class="flex flex-col">
                    <h3 class="text-xl font-semibold text-white mb-4 flex items-center">
                      <i class="fas fa-comment mr-2 text-yellow-400"></i>
                      Head of Department Comments
                    </h3>
                    <div
                      class="flex-1 px-4 py-3 bg-blue-900/30 border border-blue-400/40 rounded-lg text-white leading-relaxed overflow-y-auto"
                      style="min-height: 120px; max-height: 120px"
                    >
                      {{ hodComments }}
                    </div>
                  </div>

                  <!-- ICT Officer Comments (Right) -->
                  <div v-if="isIctOfficer" class="flex flex-col">
                    <h3 class="text-xl font-semibold text-white mb-4 flex items-center">
                      <i class="fas fa-comment-dots mr-2 text-blue-300"></i>
                      ICT Officer Comments
                    </h3>
                    <textarea
                      v-model="ictOfficerComments"
                      rows="4"
                      class="flex-1 px-4 py-3 bg-blue-900/30 border border-blue-400/40 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-blue-400 resize-none text-white placeholder-blue-300"
                      placeholder="Add your implementation comments or notes here..."
                      :readonly="!isIctOfficer || isReadOnly"
                      style="min-height: 120px; max-height: 120px"
                    ></textarea>
                  </div>
                </div>

                <!-- Action Button -->
                <div v-if="isIctOfficer && !isReadOnly" class="flex justify-center">
                  <button
                    @click="grantAccessNow"
                    :disabled="granting"
                    class="px-8 py-4 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold text-lg rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300 flex items-center disabled:opacity-50 disabled:cursor-not-allowed border border-blue-400/40"
                  >
                    <i v-if="granting" class="fas fa-spinner fa-spin mr-3"></i>
                    <i v-else class="fas fa-key mr-3"></i>
                    {{ granting ? 'Granting Access...' : 'Grant Access Now' }}
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>

    <!-- Success Toast -->
    <transition name="fade">
      <div
        v-if="toast.show"
        class="fixed bottom-4 right-4 bg-green-600 text-white rounded-lg shadow-lg px-6 py-4 z-50"
      >
        <div class="flex items-center">
          <i class="fas fa-check-circle mr-3"></i>
          {{ toast.message }}
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
  import AppHeader from '@/components/AppHeader.vue'
  import ModernSidebar from '@/components/ModernSidebar.vue'
  import combinedAccessService from '@/services/combinedAccessService.js'
  import ictOfficerService from '@/services/ictOfficerService.js'
  import authService from '@/services/authService.js'

  export default {
    name: 'UserSecurityAccess',
    components: {
      Header: AppHeader,
      ModernSidebar
    },
    data() {
      return {
        requestData: null,
        loading: true,
        error: null,
        granting: false,
        ictOfficerComments: '',
        showSuccessNotification: false,
        dataRefreshTimestamp: Date.now(), // Force reactivity
        toast: {
          show: false,
          message: ''
        }
      }
    },
    computed: {
      requestId() {
        return this.$route.params.id
      },
      isIctOfficer() {
        // Check if current user is ICT Officer and has access to grant
        const role = this.$route.query.role
        const currentUser = authService.getCurrentUser()
        return role === 'ict_officer' || (currentUser && currentUser.role === 'ict_officer')
      },
      isReadOnly() {
        // Show read-only mode for requesters or when access is already granted
        const isAccessGranted =
          this.requestData?.status === 'implemented' || this.requestData?.status === 'approved'
        return !this.isIctOfficer || isAccessGranted
      },
      currentStatus() {
        // Use refresh timestamp to force reactivity
        this.dataRefreshTimestamp // Access timestamp to make this reactive

        if (!this.requestData) return 'Loading...'

        console.log('🔍 DEBUG currentStatus - Refresh timestamp:', this.dataRefreshTimestamp)
        console.log('🔍 DEBUG currentStatus - workflow_stage:', this.requestData.workflow_stage)
        console.log('🔍 DEBUG currentStatus - requestData.status:', this.requestData.status)
        console.log(
          '🔍 DEBUG currentStatus - ict_officer_status:',
          this.requestData.ict_officer_status
        )

        // Use workflow_stage if available, otherwise use getStatusLabel with null to force proper detection
        if (this.requestData.workflow_stage) {
          console.log(
            '🔍 DEBUG currentStatus - Using workflow_stage:',
            this.requestData.workflow_stage
          )
          return this.requestData.workflow_stage
        }

        const calculatedStatus = this.getStatusLabel(null) // Pass null to force proper detection
        console.log('🔍 DEBUG currentStatus - Calculated status:', calculatedStatus)
        return calculatedStatus
      },
      wellsoftModules() {
        if (!this.requestData) return []

        // Use the correct field name from API response
        const modules =
          this.requestData.wellsoft_modules_selected ||
          this.requestData.wellsoft_modules ||
          this.requestData.wellsoft ||
          this.requestData.wellsoftModules ||
          []

        // Handle both array and JSON string formats
        if (typeof modules === 'string') {
          try {
            return JSON.parse(modules).filter((m) => m && m.trim())
          } catch {
            return modules
              .split(',')
              .map((m) => m.trim())
              .filter((m) => m)
          }
        }

        return Array.isArray(modules) ? modules.filter((m) => m && m.trim()) : []
      },
      jeevaModules() {
        if (!this.requestData) return []

        // Use the correct field name from API response
        const modules =
          this.requestData.jeeva_modules_selected ||
          this.requestData.jeeva_modules ||
          this.requestData.jeeva ||
          this.requestData.jeevaModules ||
          []

        // Handle both array and JSON string formats
        if (typeof modules === 'string') {
          try {
            return JSON.parse(modules).filter((m) => m && m.trim())
          } catch {
            return modules
              .split(',')
              .map((m) => m.trim())
              .filter((m) => m)
          }
        }

        return Array.isArray(modules) ? modules.filter((m) => m && m.trim()) : []
      },
      internetPurposes() {
        if (!this.requestData) return []

        // Try different possible field names
        const purposes =
          this.requestData.internet_purposes ||
          this.requestData.internet ||
          this.requestData.internetPurposes ||
          []

        if (!purposes) return []

        // Handle array format
        if (Array.isArray(purposes)) {
          return purposes.filter((p) => p && p.trim && p.trim())
        }

        // Handle JSON string format
        if (typeof purposes === 'string') {
          try {
            const parsed = JSON.parse(purposes)
            if (Array.isArray(parsed)) {
              return parsed.filter((p) => p && p.trim && p.trim())
            }
            return [purposes] // Single purpose as string
          } catch {
            // Split by comma if it looks like a comma-separated list
            return purposes
              .split(',')
              .map((p) => p.trim())
              .filter((p) => p)
          }
        }

        return []
      },
      hodComments() {
        // Try multiple possible locations for HOD comments
        return (
          this.requestData?.shared?.hod_comments ||
          this.requestData?.hod_comments ||
          this.requestData?.approvals?.hod?.comments ||
          this.requestData?.approval_workflow?.hod?.comments ||
          'No comments provided'
        )
      }
    },
    async mounted() {
      await this.loadRequestData()

      // Show success notification for read-only users (requesters viewing granted access)
      this.checkAndShowSuccessNotification()
    },
    methods: {
      async loadRequestData() {
        this.loading = true
        this.error = null

        try {
          console.log('Loading request data for ID:', this.requestId)

          // Try to load from combined access service first
          let response = await combinedAccessService.getRequestById(this.requestId)

          if (response.success && response.data) {
            this.requestData = response.data
            console.log('Request data loaded from combinedAccessService:', this.requestData)
            console.log('🔍 DEBUG - Status fields:', {
              status: this.requestData.status,
              ict_officer_status: this.requestData.ict_officer_status,
              ict_officer_implemented_at: this.requestData.ict_officer_implemented_at,
              head_it_status: this.requestData.head_it_status,
              calculated_status: this.getStatusLabel(this.requestData.status)
            })
          } else {
            console.warn('Failed to load from combinedAccessService, trying ICT Officer service...')

            // Fallback: Try ICT Officer service
            const ictResponse = await ictOfficerService.getAccessRequestById(this.requestId)
            if (ictResponse.success && ictResponse.data) {
              this.requestData = ictResponse.data
              console.log('Request data loaded from ictOfficerService:', this.requestData)
              console.log('🔍 DEBUG - ICT Service Status fields:', {
                status: this.requestData.status,
                ict_officer_status: this.requestData.ict_officer_status,
                ict_officer_implemented_at: this.requestData.ict_officer_implemented_at,
                head_it_status: this.requestData.head_it_status,
                calculated_status: this.getStatusLabel(this.requestData.status)
              })
            } else {
              console.warn('Both services failed, using mock data for testing...')
              // Final fallback: Use mock data for testing purposes
              this.requestData = this.generateMockData()
              console.log('Using mock data:', this.requestData)
            }
          }

          // Request data loaded successfully
        } catch (error) {
          console.error('Error loading request data:', error)
          this.error = error.message || 'Failed to load request data'
        } finally {
          this.loading = false
        }
      },

      async grantAccessNow() {
        if (this.granting) return

        this.granting = true

        try {
          console.log('Granting access for request:', this.requestId)
          console.log('ICT Officer comments:', this.ictOfficerComments)

          // Call the ICT Officer service to grant access
          const result = await ictOfficerService.grantAccess(this.requestId, {
            comments: this.ictOfficerComments,
            status: 'implemented',
            grantedAt: new Date().toISOString()
          })

          if (result.success) {
            // Reload the request data to get updated status
            await this.loadRequestData()

            // IMMEDIATELY update badge count locally for instant feedback
            this.updateBadgeCountLocally()

            // Immediately trigger notification badge refresh
            this.refreshNotificationBadges()

            // Progressive refresh strategy to ensure backend has time to update
            setTimeout(() => {
              console.log('🔄 Secondary notification refresh (2 seconds)')
              this.refreshNotificationBadges()
            }, 2000)

            setTimeout(() => {
              console.log('🔄 Tertiary notification refresh (4 seconds)')
              this.refreshNotificationBadges()
            }, 4000)

            setTimeout(() => {
              console.log('🔄 Final notification refresh (6 seconds)')
              this.refreshNotificationBadges()
            }, 6000)

            // Show success message
            this.toast = {
              show: true,
              message: 'Access granted successfully!'
            }

            setTimeout(() => {
              this.toast.show = false
              // Final notification refresh before redirect
              this.refreshNotificationBadges()

              // Redirect to ICT dashboard access requests with final refresh
              setTimeout(() => {
                console.log('🚀 REDIRECT: Final notification refresh before redirect')
                this.refreshNotificationBadges()

                setTimeout(() => {
                  console.log('🚀 REDIRECT: Navigating to access requests page')
                  this.$router.push('/ict-dashboard/access-requests')
                }, 1000)
              }, 500)
            }, 2000)
          } else {
            throw new Error(result.error || 'Failed to grant access')
          }
        } catch (error) {
          console.error('Error granting access:', error)
          this.toast = {
            show: true,
            message: 'Error: ' + error.message
          }
          setTimeout(() => {
            this.toast.show = false
          }, 5000)
        } finally {
          this.granting = false
        }
      },

      getStatusLabel(status) {
        const data = this.requestData

        console.log('🔍 DEBUG getStatusLabel - INPUT status:', status)
        console.log('🔍 DEBUG getStatusLabel - Full data:', data)
        console.log('🔍 DEBUG getStatusLabel - Direct status fields:', {
          status: data?.status,
          ict_officer_status: data?.ict_officer_status,
          head_it_status: data?.head_it_status,
          ict_officer_implemented_at: data?.ict_officer_implemented_at
        })
        console.log('🔍 DEBUG getStatusLabel - implementation structure:', data?.implementation)
        console.log('🔍 DEBUG getStatusLabel - ictOfficer:', data?.implementation?.ictOfficer)
        console.log(
          '🔍 DEBUG getStatusLabel - is_implemented:',
          data?.implementation?.ictOfficer?.is_implemented
        )

        // Use workflow_stage if provided by backend (it's more accurate)
        if (data?.workflow_stage) {
          console.log('🔍 DEBUG - Using workflow_stage:', data.workflow_stage)
          return data.workflow_stage
        }

        // PRIORITY 1: Check if ICT Officer has implemented - multiple detection methods

        // Method 1a: Direct database status field (most reliable)
        if (data?.ict_officer_status === 'implemented') {
          console.log('🔍 DEBUG - ✅ Access granted via ict_officer_status === implemented')
          return 'Access Granted'
        }

        // Method 1b: Overall status field shows implemented
        if (data?.status === 'implemented') {
          console.log('🔍 DEBUG - ✅ Access granted via status === implemented')
          return 'Access Granted'
        }

        // Method 1c: Nested implementation structure (from BothServiceFormController)
        if (data?.implementation?.ictOfficer?.is_implemented === true) {
          console.log('🔍 DEBUG - ✅ Access granted via nested implementation structure')
          return 'Access Granted'
        }

        // Method 1d: Check if calculated status from backend indicates completion
        const backendStatus = data?.status?.toLowerCase()
        if (
          backendStatus &&
          (backendStatus.includes('access granted') ||
            backendStatus.includes('implemented') ||
            backendStatus.includes('completed'))
        ) {
          console.log('🔍 DEBUG - ✅ Access granted via calculated backend status:', data?.status)
          return 'Access Granted'
        }

        // Check for valid implementation date (not the default invalid date)
        const implDate = data?.ict_officer_implemented_at
        if (implDate && !implDate.includes('-000001') && !implDate.includes('1970-01-01')) {
          return 'Access Granted'
        }

        // Check the status hierarchy based on actual database columns
        if (
          data?.head_it_status === 'approved' &&
          (data?.ict_officer_status === 'pending' || !data?.ict_officer_status)
        ) {
          console.log('🔍 DEBUG - WRONG PATH: Showing Pending ICT Officer Implementation')
          console.log('🔍 DEBUG - head_it_status:', data?.head_it_status)
          console.log('🔍 DEBUG - ict_officer_status:', data?.ict_officer_status)
          console.log('🔍 DEBUG - This should not happen if access is granted!')
          return 'Pending ICT Officer Implementation'
        }

        if (
          data?.ict_director_status === 'approved' &&
          (data?.head_it_status === 'pending' || !data?.head_it_status)
        ) {
          return 'Pending Head IT Approval'
        }

        if (
          data?.divisional_status === 'approved' &&
          (data?.ict_director_status === 'pending' || !data?.ict_director_status)
        ) {
          return 'Pending ICT Director Approval'
        }

        if (
          data?.hod_status === 'approved' &&
          (data?.divisional_status === 'pending' || !data?.divisional_status)
        ) {
          return 'Pending Divisional Director Approval'
        }

        if (data?.hod_status === 'pending' || !data?.hod_status) {
          return 'Pending HOD Approval'
        }

        // Status mapping for backward compatibility
        const statusMap = {
          pending: 'Pending Approval',
          hod_approved: 'HOD Approved',
          divisional_approved: 'Divisional Approved',
          ict_director_approved: 'ICT Director Approved',
          head_it_approved: 'Head IT Approved',
          pending_ict_officer: 'Pending ICT Officer',
          assigned_to_ict: 'Assigned to ICT Officer',
          implementation_in_progress: 'Implementation In Progress',
          implemented: 'Access Granted',
          approved: 'Access Granted',
          completed: 'Access Granted',
          rejected: 'Rejected'
        }

        return (
          statusMap[status] ||
          status?.replace(/_/g, ' ').replace(/\b\w/g, (l) => l.toUpperCase()) ||
          'Pending Approval'
        )
      },

      getProgress() {
        if (!this.requestData) return '0%'

        const data = this.requestData

        // Use the backend-calculated progress if available
        if (data.approval_progress !== undefined) {
          return data.approval_progress + '%'
        }

        // Calculate progress based on actual database columns
        let progress = 0

        // HOD approval (20%)
        if (data.hod_status === 'approved' && data.hod_approved_at) {
          progress = 20
        }

        // Divisional approval (40%)
        if (data.divisional_status === 'approved' && data.divisional_approved_at) {
          progress = 40
        }

        // ICT Director approval (60%)
        if (data.ict_director_status === 'approved' && data.ict_director_approved_at) {
          progress = 60
        }

        // Head of IT approval (80%)
        if (data.head_it_status === 'approved' && data.head_it_approved_at) {
          progress = 80
        }

        // ICT Officer implementation (100%)
        if (data.ict_officer_status === 'implemented' || data.status === 'implemented') {
          // Check if implementation date is valid (not the default invalid date)
          const implDate = data.ict_officer_implemented_at
          if (implDate && !implDate.includes('-000001') && !implDate.includes('1970-01-01')) {
            progress = 100
          } else if (data.ict_officer_status === 'implemented' || data.status === 'implemented') {
            progress = 100 // Trust the status field if it says implemented
          } else {
            progress = 90 // Assigned but not yet implemented
          }
        }

        return progress + '%'
      },

      formatDate(dateStr) {
        if (!dateStr) return 'N/A'
        try {
          return new Date(dateStr).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
          })
        } catch {
          return dateStr
        }
      },

      checkAndShowSuccessNotification() {
        // Show notification if user is in read-only mode (requester viewing granted access)
        // and the request has been successfully implemented
        if (this.isReadOnly && this.requestData) {
          const data = this.requestData

          // Check multiple conditions for implemented/granted status
          const isImplemented =
            data?.ict_officer_status === 'implemented' ||
            data?.status === 'implemented' ||
            (data?.ict_officer_implemented_at &&
              !data?.ict_officer_implemented_at.includes('-000001') &&
              !data?.ict_officer_implemented_at.includes('1970-01-01'))

          const statusLabel = this.getStatusLabel(data?.status || '')

          // Show notification for any completed/granted access request
          if (
            isImplemented ||
            statusLabel === 'Access Granted' ||
            statusLabel.includes('Granted')
          ) {
            this.showSuccessNotification = true

            // Auto-hide after 8 seconds
            setTimeout(() => {
              this.hideSuccessNotification()
            }, 8000)
          }
        }
      },

      hideSuccessNotification() {
        this.showSuccessNotification = false
      },

      goBack() {
        this.$router.go(-1)
      },

      async refreshNotificationBadges() {
        // Try multiple approaches to ensure notification badges are updated
        try {
          console.log('🔔 Starting comprehensive notification badge refresh...')

          // Method 1: Dispatch force refresh event for immediate update
          if (window.dispatchEvent) {
            const forceEvent = new CustomEvent('force-refresh-notifications', {
              detail: {
                source: 'UserSecurityAccess',
                reason: 'access_granted',
                timestamp: Date.now()
              }
            })
            window.dispatchEvent(forceEvent)
            console.log('🚀 Dispatched force-refresh-notifications event')

            // Also dispatch regular refresh as backup
            const event = new CustomEvent('refresh-notifications', {
              detail: {
                source: 'UserSecurityAccess',
                reason: 'access_granted',
                timestamp: Date.now()
              }
            })
            window.dispatchEvent(event)
            console.log('📡 Dispatched refresh-notifications event')
          }

          // Method 2: Direct call to sidebar instance
          if (window.sidebarInstance && window.sidebarInstance.fetchNotificationCounts) {
            console.log('🔄 Calling sidebar fetchNotificationCounts directly')
            window.sidebarInstance.fetchNotificationCounts(true) // force refresh with cache bypass
          }

          // Method 3: Aggressive cache clearing and fresh API calls
          try {
            const notificationService = (await import('@/services/notificationService')).default
            console.log('🧽 Aggressive notification cache clearing and refresh')

            // Clear any cached data multiple times to ensure it's gone
            if (notificationService.clearCache) {
              notificationService.clearCache()
              console.log('🗑️ Notification cache cleared')
            }

            // Wait a moment then force multiple fresh fetches
            setTimeout(async () => {
              try {
                if (notificationService.getPendingRequestsCount) {
                  console.log('🚀 Force fetch #1 - Immediate notification count refresh')
                  await notificationService.getPendingRequestsCount(true) // force fresh fetch

                  // Clear cache again and fetch again
                  if (notificationService.clearCache) {
                    notificationService.clearCache()
                  }

                  console.log('🚀 Force fetch #2 - Second notification count refresh')
                  await notificationService.getPendingRequestsCount(true) // force fresh fetch again
                }
              } catch (fetchError) {
                console.warn('Failed during aggressive notification refresh:', fetchError)
              }
            }, 500)
          } catch (serviceError) {
            console.warn('Failed to refresh via notification service:', serviceError)
          }

          // Method 4: If we're still in the app, trigger a page-level refresh of components
          if (this.$root && this.$root.$emit) {
            this.$root.$emit('force-refresh-notifications', { reason: 'access_granted' })
          }

          console.log('\u2705 Notification badge refresh complete')
        } catch (error) {
          console.error('\u274c Failed to refresh notification badges:', error)
        }
      },

      // Immediately update badge count locally for instant user feedback
      updateBadgeCountLocally() {
        try {
          console.log(
            '\ud83d\udce6 UserSecurityAccess: Updating badge count locally for instant feedback'
          )

          // Access the sidebar's notification counts directly and decrement by 1
          if (window.sidebarInstance && window.sidebarInstance.notificationCounts) {
            const currentCounts = window.sidebarInstance.notificationCounts
            const ictRoute = '/ict-dashboard/access-requests'

            if (currentCounts[ictRoute] && currentCounts[ictRoute] > 0) {
              currentCounts[ictRoute] = Math.max(0, currentCounts[ictRoute] - 1)
              console.log(
                '\ud83d\udd04 Decremented badge count for',
                ictRoute,
                'to',
                currentCounts[ictRoute]
              )

              // Force Vue reactivity update if possible
              if (window.sidebarInstance.forceUpdate) {
                window.sidebarInstance.forceUpdate()
              }
            }
          }

          // Also try to access the global notification counts in ModernSidebar
          if (window.Vue && window.Vue.observable) {
            // Trigger Vue's reactivity system to update the UI immediately
            console.log('\ud83d\udd04 Triggering Vue reactivity update')
          }
        } catch (error) {
          console.warn('Failed to update badge count locally:', error)
        }
      },

      generateMockData() {
        // Generate mock data for testing when backend is not available
        return {
          id: this.requestId,
          pf_number: 'PF12345',
          staff_name: 'John Doe',
          department: 'ICT Department',
          phone: '+256-700-123456',
          status: 'pending_ict_officer',
          wellsoft_modules: ['OPD', 'IPD', 'Pharmacy', 'Laboratory'],
          jeeva_modules: ['Accounting', 'HR', 'Procurement'],
          internet_purposes: ['Email communication', 'Research purposes', 'Official documentation'],
          hod_comments: 'Staff requires access for daily operations. Approved by HOD.',
          comments: 'All requested modules are necessary for the staff role.',
          created_at: new Date().toISOString(),
          updated_at: new Date().toISOString(),
          request_type: 'combined_access',
          services: ['jeeva', 'wellsoft', 'internet']
        }
      }
    }
  }
</script>

<style scoped>
  .fade-enter-active,
  .fade-leave-active {
    transition: opacity 0.3s ease;
  }
  .fade-enter-from,
  .fade-leave-to {
    opacity: 0;
  }

  /* Slide animation for top-right notification */
  .slide-down-enter-active {
    transition: all 0.4s ease-out;
  }
  .slide-down-leave-active {
    transition: all 0.3s ease-in;
  }
  .slide-down-enter-from {
    opacity: 0;
    transform: translateX(100%) translateY(-10px);
  }
  .slide-down-leave-to {
    opacity: 0;
    transform: translateX(100%) translateY(-10px);
  }
  .slide-down-enter-to {
    opacity: 1;
    transform: translateX(0) translateY(0);
  }

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

  .medical-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(96, 165, 250, 0.2), transparent);
    transition: left 0.5s;
  }

  .medical-card:hover::before {
    left: 100%;
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
</style>
