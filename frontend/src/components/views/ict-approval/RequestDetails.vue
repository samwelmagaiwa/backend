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
                    @error="handleImageError"
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
                      <i class="fas fa-clipboard-check text-base"></i>
                      REQUEST DETAILS & ASSESSMENT
                    </span>
                    <div
                      class="absolute inset-0 bg-gradient-to-r from-blue-700 to-blue-800 rounded-full opacity-0 hover:opacity-100 transition-opacity duration-300"
                    ></div>
                  </div>
                </div>
                <h2
                  class="text-lg font-bold text-blue-100 tracking-wide drop-shadow-md animate-fade-in-delay"
                >
                  ICT OFFICER REVIEW PANEL
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
                    @error="handleImageError"
                  />
                </div>
              </div>
            </div>
          </div>

          <!-- Main Content -->
          <div class="booking-glass-card rounded-b-3xl overflow-hidden animate-slide-up">
            <div class="p-6 space-y-6">
              <!-- Back Button -->
              <div class="mb-4">
                <button
                  @click="goBack"
                  class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors"
                >
                  <i class="fas fa-arrow-left mr-2"></i>
                  Back to Requests List
                </button>
              </div>

              <!-- Request Info -->
              <div v-if="!isLoading && Object.keys(request).length > 0" class="space-y-4">
                <h3 class="text-xl font-bold text-white">Request Information</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-semibold text-blue-200 mb-1">Request ID</label>
                    <div class="px-3 py-2 bg-white/10 border border-blue-300/30 rounded text-white">
                      {{ request.request_id || `REQ-${String(request.id).padStart(6, '0')}` }}
                    </div>
                  </div>

                  <div>
                    <label class="block text-sm font-semibold text-blue-200 mb-1"
                      >Borrower Name</label
                    >
                    <div class="px-3 py-2 bg-white/10 border border-blue-300/30 rounded text-white">
                      {{ request.borrower_name || request.borrowerName || 'Unknown' }}
                    </div>
                  </div>

                  <div>
                    <label class="block text-sm font-semibold text-blue-200 mb-1">Department</label>
                    <div class="px-3 py-2 bg-white/10 border border-blue-300/30 rounded text-white">
                      {{ request.department || 'Unknown Department' }}
                    </div>
                  </div>

                  <div>
                    <label class="block text-sm font-semibold text-blue-200 mb-1"
                      >Phone Number</label
                    >
                    <div class="px-3 py-2 bg-white/10 border border-blue-300/30 rounded text-white">
                      {{ request.borrower_phone || request.phoneNumber || 'No phone provided' }}
                    </div>
                  </div>

                  <div>
                    <label class="block text-sm font-semibold text-blue-200 mb-1">PF Number</label>
                    <div class="px-3 py-2 bg-white/10 border border-blue-300/30 rounded text-white">
                      {{ request.pf_number }}
                    </div>
                  </div>

                  <div>
                    <label class="block text-sm font-semibold text-blue-200 mb-1"
                      >Device Type</label
                    >
                    <div class="px-3 py-2 bg-white/10 border border-blue-300/30 rounded text-white">
                      {{
                        request.device_name ||
                        getDeviceDisplayName(request.device_type, request.custom_device)
                      }}
                    </div>
                  </div>

                  <div>
                    <label class="block text-sm font-semibold text-blue-200 mb-1"
                      >Booking Date</label
                    >
                    <div class="px-3 py-2 bg-white/10 border border-blue-300/30 rounded text-white">
                      {{ formatDate(request.booking_date || request.bookingDate) }}
                    </div>
                  </div>

                  <div>
                    <label class="block text-sm font-semibold text-blue-200 mb-1"
                      >Return Time</label
                    >
                    <div class="px-3 py-2 bg-white/10 border border-blue-300/30 rounded text-white">
                      {{ request.return_time || request.returnTime || 'No time specified' }}
                    </div>
                  </div>
                  
                  <div>
                    <label class="block text-sm font-semibold text-blue-200 mb-1"
                      >Current Status</label
                    >
                    <div class="px-3 py-2 bg-white/10 border border-blue-300/30 rounded text-white flex items-center gap-2">
                      <i :class="getStatusIcon(deviceReceived ? 'received' : (requestApproved ? 'approved' : (request.ict_approve || request.status)))"></i>
                      <span>{{ currentStatusText }}</span>
                    </div>
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-semibold text-blue-200 mb-1"
                    >Purpose/Reason</label
                  >
                  <div class="px-3 py-2 bg-white/10 border border-blue-300/30 rounded text-white">
                    {{ request.reason || request.purpose || 'No reason provided' }}
                  </div>
                </div>
              </div>

              <!-- Device Condition Assessment Section -->
              <div
                v-if="!isLoading && Object.keys(request).length > 0"
                class="bg-blue-800/30 backdrop-blur-sm rounded-xl p-6 border border-blue-600/40 animate-fade-in"
              >
                <div class="flex items-center gap-3 mb-6">
                  <div
                    class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-700 rounded-xl flex items-center justify-center"
                  >
                    <i class="fas fa-clipboard-check text-white text-lg"></i>
                  </div>
                  <h3 class="text-xl font-bold text-white">Device Condition Assessment</h3>
                </div>

                <!-- Assessment Type Tabs -->
                <div class="flex mb-6 bg-blue-900/40 rounded-xl p-1">
                  <button
                    @click="assessmentType = 'issuing'"
                    :class="[
                      'flex-1 py-3 px-4 rounded-lg font-semibold transition-all duration-300 flex items-center justify-center gap-2',
                      assessmentType === 'issuing'
                        ? 'bg-gradient-to-r from-blue-600 to-blue-700 text-white shadow-lg'
                        : 'text-blue-200 hover:text-white hover:bg-blue-800/50'
                    ]"
                  >
                    <i class="fas fa-hand-holding text-sm"></i>
                    Device Issuing
                  </button>
                  <button
                    @click="assessmentType = 'receiving'"
                    :class="[
                      'flex-1 py-3 px-4 rounded-lg font-semibold transition-all duration-300 flex items-center justify-center gap-2',
                      assessmentType === 'receiving'
                        ? 'bg-gradient-to-r from-blue-600 to-blue-700 text-white shadow-lg'
                        : 'text-blue-200 hover:text-white hover:bg-blue-800/50'
                    ]"
                  >
                    <i class="fas fa-undo text-sm"></i>
                    Device Receiving
                  </button>
                </div>

                <!-- Assessment Form -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                  <!-- Physical Condition -->
                  <div>
                    <label class="block text-sm font-semibold text-blue-200 mb-3">
                      <i class="fas fa-eye mr-2"></i>
                      Physical Condition *
                    </label>
                    <div class="space-y-2">
                      <label
                        v-for="condition in physicalConditions"
                        :key="condition.value"
                        class="flex items-center p-3 bg-blue-900/20 rounded-lg border border-blue-600/30 hover:border-blue-500/50 transition-all cursor-pointer group"
                      >
                        <input
                          type="radio"
                          :value="condition.value"
                          v-model="currentAssessment.physical_condition"
                          class="mr-3 text-blue-600 focus:ring-blue-500"
                        />
                        <div class="flex items-center gap-2">
                          <i :class="condition.icon + ' text-' + condition.color"></i>
                          <span class="text-white group-hover:text-blue-100 font-medium">{{
                            condition.label
                          }}</span>
                        </div>
                      </label>
                    </div>
                  </div>

                  <!-- Device Functionality -->
                  <div>
                    <label class="block text-sm font-semibold text-blue-200 mb-3">
                      <i class="fas fa-cogs mr-2"></i>
                      Device Functionality *
                    </label>
                    <div class="space-y-2">
                      <label
                        v-for="functionality in functionalityOptions"
                        :key="functionality.value"
                        class="flex items-center p-3 bg-purple-900/20 rounded-lg border border-purple-600/30 hover:border-purple-500/50 transition-all cursor-pointer group"
                      >
                        <input
                          type="radio"
                          :value="functionality.value"
                          v-model="currentAssessment.functionality"
                          class="mr-3 text-purple-600 focus:ring-purple-500"
                        />
                        <div class="flex items-center gap-2">
                          <i :class="functionality.icon + ' text-' + functionality.color"></i>
                          <span class="text-white group-hover:text-purple-100 font-medium">{{
                            functionality.label
                          }}</span>
                        </div>
                      </label>
                    </div>
                  </div>
                </div>

                <!-- Accessories and Damage Assessment -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                  <!-- Accessories Completeness -->
                  <div>
                    <label class="block text-sm font-semibold text-purple-200 mb-3">
                      <i class="fas fa-puzzle-piece mr-2"></i>
                      Accessories Status
                    </label>
                    <div class="bg-purple-900/20 rounded-lg border border-purple-600/30 p-4">
                      <label class="flex items-center cursor-pointer group">
                        <input
                          type="checkbox"
                          v-model="currentAssessment.accessories_complete"
                          class="mr-3 text-purple-600 focus:ring-purple-500 rounded"
                        />
                        <div class="flex items-center gap-2">
                          <i class="fas fa-check-circle text-green-400"></i>
                          <span class="text-white group-hover:text-purple-100 font-medium"
                            >All accessories included</span
                          >
                        </div>
                      </label>
                      <p class="text-purple-300 text-xs mt-2 ml-6">
                        Check if all required accessories are present and functional
                      </p>
                    </div>
                  </div>

                  <!-- Damage Assessment -->
                  <div>
                    <label class="block text-sm font-semibold text-purple-200 mb-3">
                      <i class="fas fa-exclamation-triangle mr-2"></i>
                      Damage Assessment
                    </label>
                    <div class="bg-purple-900/20 rounded-lg border border-purple-600/30 p-4">
                      <label class="flex items-center cursor-pointer group mb-3">
                        <input
                          type="checkbox"
                          v-model="currentAssessment.visible_damage"
                          class="mr-3 text-red-600 focus:ring-red-500 rounded"
                        />
                        <div class="flex items-center gap-2">
                          <i class="fas fa-tools text-red-400"></i>
                          <span class="text-white group-hover:text-purple-100 font-medium"
                            >Device has damage/issues</span
                          >
                        </div>
                      </label>
                      <textarea
                        v-if="currentAssessment.visible_damage"
                        v-model="currentAssessment.damage_description"
                        rows="2"
                        class="w-full px-3 py-2 bg-purple-800/30 border border-purple-500/30 rounded focus:border-purple-400 focus:outline-none text-white placeholder-purple-300/60 text-sm"
                        placeholder="Describe the damage or issues..."
                      ></textarea>
                    </div>
                  </div>
                </div>

                <!-- Additional Notes -->
                <div class="mb-6">
                  <label class="block text-sm font-semibold text-purple-200 mb-3">
                    <i class="fas fa-sticky-note mr-2"></i>
                    Additional Notes
                  </label>
                  <textarea
                    v-model="currentAssessmentNotes"
                    rows="3"
                    class="w-full px-4 py-3 bg-purple-900/20 border border-purple-600/30 rounded-lg focus:border-purple-400 focus:outline-none text-white placeholder-purple-300/60 resize-none"
                    placeholder="Enter any additional observations, recommendations, or notes about the device condition..."
                  ></textarea>
                </div>

                <!-- Enhanced Workflow-Based Button Layout -->
                <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-purple-600/30">
                  <!-- Issuing Assessment Button (Blue) - Only show during issuing phase -->
                  <button
                    v-if="showIssuingAssessmentButton"
                    @click="saveIssuingAssessment"
                    :disabled="!canSaveIssuingAssessment"
                    class="flex-1 group relative overflow-hidden px-8 py-4 bg-gradient-to-r from-blue-600 via-blue-700 to-blue-800 text-white rounded-xl font-bold text-lg transition-all duration-300 transform hover:scale-105 hover:shadow-2xl hover:shadow-blue-500/30 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100 disabled:hover:shadow-none"
                  >
                    <!-- Background Animation -->
                    <div
                      class="absolute inset-0 bg-gradient-to-r from-blue-700 via-blue-800 to-blue-900 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                    ></div>

                    <!-- Button Content -->
                    <div class="relative z-10 flex items-center justify-center gap-3">
                      <div
                        class="flex items-center justify-center w-8 h-8 bg-white/20 rounded-full group-hover:bg-white/30 transition-colors duration-300"
                      >
                        <i class="fas fa-save text-lg"></i>
                      </div>
                      <div class="flex flex-col items-start">
                        <span class="text-sm opacity-90">Device Assessment</span>
                        <span class="text-lg font-bold">
                          {{ isProcessingAssessment ? 'Saving...' : 'Save Issuing Assessment' }}
                        </span>
                      </div>
                    </div>

                    <!-- Status Indicator -->
                    <div v-if="issuingAssessmentSaved" class="absolute top-2 right-2">
                      <div class="w-3 h-3 bg-green-400 rounded-full animate-pulse"></div>
                    </div>

                    <!-- Shimmer Effect -->
                    <div
                      class="absolute inset-0 -skew-x-12 bg-gradient-to-r from-transparent via-white/10 to-transparent opacity-0 group-hover:opacity-100 group-hover:animate-shimmer"
                    ></div>
                  </button>

                  <!-- Approval Button (Green) - Only show during issuing phase after assessment -->
                  <button
                    v-if="showApprovalButton"
                    @click="approveRequest"
                    :disabled="!canApproveRequest || isProcessing"
                    class="flex-1 group relative overflow-hidden px-8 py-4 bg-gradient-to-r from-green-600 via-green-700 to-green-800 text-white rounded-xl font-bold text-lg transition-all duration-300 transform hover:scale-105 hover:shadow-2xl hover:shadow-green-500/30 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100 disabled:hover:shadow-none"
                  >
                    <!-- Background Animation -->
                    <div
                      class="absolute inset-0 bg-gradient-to-r from-green-700 via-green-800 to-green-900 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                    ></div>

                    <!-- Button Content -->
                    <div class="relative z-10 flex items-center justify-center gap-3">
                      <div
                        class="flex items-center justify-center w-8 h-8 bg-white/20 rounded-full group-hover:bg-white/30 transition-colors duration-300"
                      >
                        <i class="fas fa-check text-lg"></i>
                      </div>
                      <div class="flex flex-col items-start">
                        <span class="text-sm opacity-90">Final Action</span>
                        <span class="text-lg font-bold">
                          {{ isProcessing ? 'Approving...' : 'Approve Request' }}
                        </span>
                      </div>
                    </div>

                    <!-- Status Indicator -->
                    <div v-if="requestApproved" class="absolute top-2 right-2">
                      <div class="w-3 h-3 bg-blue-400 rounded-full animate-pulse"></div>
                    </div>

                    <!-- Shimmer Effect -->
                    <div
                      class="absolute inset-0 -skew-x-12 bg-gradient-to-r from-transparent via-white/10 to-transparent opacity-0 group-hover:opacity-100 group-hover:animate-shimmer"
                    ></div>
                  </button>

                  <!-- Receiving Assessment Button (Purple) - Only show during receiving phase -->
                  <button
                    v-if="showReceivingButton"
                    @click="saveReceivingAssessment"
                    :disabled="!canSaveReceivingAssessment"
                    class="w-full group relative overflow-hidden px-8 py-4 bg-gradient-to-r from-purple-600 via-purple-700 to-purple-800 text-white rounded-xl font-bold text-lg transition-all duration-300 transform hover:scale-105 hover:shadow-2xl hover:shadow-purple-500/30 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100 disabled:hover:shadow-none"
                  >
                    <!-- Background Animation -->
                    <div
                      class="absolute inset-0 bg-gradient-to-r from-purple-700 via-purple-800 to-purple-900 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                    ></div>

                    <!-- Button Content -->
                    <div class="relative z-10 flex items-center justify-center gap-3">
                      <div
                        class="flex items-center justify-center w-8 h-8 bg-white/20 rounded-full group-hover:bg-white/30 transition-colors duration-300"
                      >
                        <i class="fas fa-undo text-lg"></i>
                      </div>
                      <div class="flex flex-col items-start">
                        <span class="text-sm opacity-90">Device Return</span>
                        <span class="text-lg font-bold">
                          {{ isProcessingAssessment ? 'Processing...' : 'Receive Device & Complete Assessment' }}
                        </span>
                      </div>
                    </div>

                    <!-- Status Indicator -->
                    <div v-if="deviceReceived" class="absolute top-2 right-2">
                      <div class="w-3 h-3 bg-green-400 rounded-full animate-pulse"></div>
                    </div>

                    <!-- Shimmer Effect -->
                    <div
                      class="absolute inset-0 -skew-x-12 bg-gradient-to-r from-transparent via-white/10 to-transparent opacity-0 group-hover:opacity-100 group-hover:animate-shimmer"
                    ></div>
                  </button>
                </div>

                <!-- Assessment Status Messages -->
                <div v-if="assessmentMessage" class="mt-4">
                  <div
                    :class="[
                      'p-4 rounded-lg border flex items-center gap-3 animate-fade-in',
                      assessmentMessage.type === 'success'
                        ? 'bg-green-500/20 border-green-400/40 text-green-200'
                        : 'bg-red-500/20 border-red-400/40 text-red-200'
                    ]"
                  >
                    <i
                      :class="[
                        'fas text-lg',
                        assessmentMessage.type === 'success'
                          ? 'fa-check-circle'
                          : 'fa-exclamation-circle'
                      ]"
                    ></i>
                    <span class="font-medium">{{ assessmentMessage.text }}</span>
                  </div>
                </div>
              </div>

              <!-- Loading State -->
              <div v-else-if="isLoading" class="text-center py-8">
                <div
                  class="w-16 h-16 border-4 border-blue-600 border-t-transparent rounded-full animate-spin mx-auto mb-4"
                ></div>
                <p class="text-white">Loading request details...</p>
              </div>

              <!-- Error State -->
              <div v-else class="bg-red-500/20 border border-red-400/40 rounded-lg p-4">
                <p class="text-red-200 text-sm mb-2">
                  ⚠️ No request data loaded. Check console for errors.
                </p>
                <button
                  @click="fetchRequestDetails()"
                  class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
                >
                  Retry Loading
                </button>
              </div>



              <!-- Footer -->
              <AppFooter />
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script>
  import Header from '@/components/header.vue'
  import ModernSidebar from '@/components/ModernSidebar.vue'
  import AppFooter from '@/components/footer.vue'
  import deviceBorrowingService from '@/services/deviceBorrowingService'

  export default {
    name: 'RequestDetails',
    components: {
      Header,
      ModernSidebar,
      AppFooter
    },
    data() {
      return {
        request: {},
        isLoading: false,
        isProcessing: false,

        // Assessment data
        assessmentType: 'issuing', // 'issuing' or 'receiving'
        issuingAssessment: {
          physical_condition: '',
          functionality: '',
          accessories_complete: false,
          visible_damage: false,
          damage_description: '',
          notes: ''
        },
        receivingAssessment: {
          physical_condition: '',
          functionality: '',
          accessories_complete: false,
          visible_damage: false,
          damage_description: '',
          notes: ''
        },
        issuingAssessmentNotes: '',
        receivingAssessmentNotes: '',
        isProcessingAssessment: false,
        issuingAssessmentSaved: false,
        receivingAssessmentSaved: false,
        assessmentMessage: null,
        
        // Workflow state tracking
        deviceIssued: false, // Track if device has been issued
        deviceReceived: false, // Track if device has been received back
        requestApproved: false, // Track if request has been approved

        // Assessment options
        physicalConditions: [
          { value: 'excellent', label: 'Excellent', icon: 'fas fa-star', color: 'green-400' },
          { value: 'good', label: 'Good', icon: 'fas fa-thumbs-up', color: 'blue-400' },
          { value: 'fair', label: 'Fair', icon: 'fas fa-exclamation', color: 'yellow-400' },
          { value: 'poor', label: 'Poor', icon: 'fas fa-times', color: 'red-400' }
        ],
        functionalityOptions: [
          {
            value: 'fully_functional',
            label: 'Fully Functional',
            icon: 'fas fa-check-circle',
            color: 'green-400'
          },
          {
            value: 'partially_functional',
            label: 'Partially Functional',
            icon: 'fas fa-exclamation-circle',
            color: 'yellow-400'
          },
          {
            value: 'not_functional',
            label: 'Not Functional',
            icon: 'fas fa-times-circle',
            color: 'red-400'
          }
        ]
      }
    },
    computed: {
      canTakeAction() {
        // Check if request exists and has pending status
        if (!this.request || !this.request.id) return false
        
        // Check various status fields that might indicate pending approval
        const isPending = 
          this.request.ict_approve === 'pending' || 
          this.request.ict_status === 'pending' ||
          this.request.status === 'pending' ||
          (!this.request.ict_approve && !this.request.ict_status) // No approval status set yet
        
        console.log('🔍 canTakeAction check:', {
          requestId: this.request.id,
          ict_approve: this.request.ict_approve,
          ict_status: this.request.ict_status,
          status: this.request.status,
          isPending
        })
        
        return isPending
      },

      currentAssessment() {
        return this.assessmentType === 'issuing' ? this.issuingAssessment : this.receivingAssessment
      },

      currentAssessmentNotes: {
        get() {
          return this.assessmentType === 'issuing'
            ? this.issuingAssessmentNotes
            : this.receivingAssessmentNotes
        },
        set(value) {
          if (this.assessmentType === 'issuing') {
            this.issuingAssessmentNotes = value
          } else {
            this.receivingAssessmentNotes = value
          }
        }
      },

      isIssuingAssessmentComplete() {
        const assessment = this.issuingAssessment
        const requiredFields = assessment.physical_condition && assessment.functionality
        const damageValid = !assessment.visible_damage || assessment.damage_description?.trim()
        return requiredFields && damageValid
      },

      isReceivingAssessmentComplete() {
        const assessment = this.receivingAssessment
        const requiredFields = assessment.physical_condition && assessment.functionality
        const damageValid = !assessment.visible_damage || assessment.damage_description?.trim()
        return requiredFields && damageValid
      },

      canApproveRequest() {
        // Approval button should only be enabled after issuing assessment is saved
        // and the request is still pending
        const canApprove = this.canTakeAction && this.issuingAssessmentSaved && !this.requestApproved
        
        console.log('🔍 canApproveRequest check:', {
          canTakeAction: this.canTakeAction,
          issuingAssessmentSaved: this.issuingAssessmentSaved,
          requestApproved: this.requestApproved,
          canApprove
        })
        
        return canApprove
      },
      
      canSaveIssuingAssessment() {
        // Issuing assessment can be saved if:
        // 1. Assessment is complete
        // 2. Not already saved
        // 3. Request is pending
        // 4. Not currently processing
        return this.isIssuingAssessmentComplete && 
               !this.issuingAssessmentSaved && 
               this.canTakeAction && 
               !this.isProcessingAssessment
      },
      
      canSaveReceivingAssessment() {
        // Receiving assessment can be saved if:
        // 1. Assessment is complete
        // 2. Device has been issued (approved)
        // 3. Not already received
        // 4. Not currently processing
        return this.isReceivingAssessmentComplete && 
               this.requestApproved && 
               !this.deviceReceived && 
               !this.isProcessingAssessment
      },
      
      showIssuingAssessmentButton() {
        // Show issuing assessment button only if:
        // 1. Assessment type is 'issuing'
        // 2. Device hasn't been received yet
        return this.assessmentType === 'issuing' && !this.deviceReceived
      },
      
      showApprovalButton() {
        // Show approval button only if:
        // 1. Assessment type is 'issuing'
        // 2. Device hasn't been received yet
        return this.assessmentType === 'issuing' && !this.deviceReceived
      },
      
      showReceivingButton() {
        // Show receiving button only if:
        // 1. Assessment type is 'receiving'
        // 2. Request has been approved
        // 3. Device hasn't been received yet
        return this.assessmentType === 'receiving' && this.requestApproved && !this.deviceReceived
      },
      
      currentStatusText() {
        if (this.deviceReceived) {
          return 'Received'
        } else if (this.requestApproved) {
          return 'Approved'
        } else {
          return this.getStatusText(this.request.ict_approve || this.request.status)
        }
      }
    },
    async mounted() {
      console.log('🚀 RequestDetails component mounted successfully')
      console.log('🚀 Route ID:', this.$route.params.id)

      try {
        await this.fetchRequestDetails()
      } catch (error) {
        console.error('❌ Error in mounted hook:', error)
      }
    },
    methods: {
      async fetchRequestDetails() {
        this.isLoading = true
        try {
          const requestId = this.$route.params.id
          console.log('🔍 Fetching request details for ID:', requestId)

          if (!requestId || requestId === ':id') {
            throw new Error('Invalid request ID: ' + requestId)
          }

          const response = await deviceBorrowingService.getRequestDetails(requestId)

        if (response.success) {
          this.request = response.data
          
          // Initialize workflow state based on request data
          this.initializeWorkflowState()
          
          console.log('✅ Request details loaded:', this.request)
          console.log('🔍 Request status fields:', {
            id: this.request.id,
            status: this.request.status,
            ict_approve: this.request.ict_approve,
            ict_status: this.request.ict_status,
            canTakeAction: this.canTakeAction,
            workflowState: {
              deviceIssued: this.deviceIssued,
              deviceReceived: this.deviceReceived,
              requestApproved: this.requestApproved
            }
          })
        } else {
          console.error('❌ Failed to load request details:', response)
          throw new Error(response.message || 'Failed to load request details')
        }
        } catch (error) {
          console.error('❌ Error fetching request details:', error)
        } finally {
          this.isLoading = false
        }
      },

      initializeWorkflowState() {
        // Initialize workflow state based on request data
        this.requestApproved = this.request.ict_approve === 'approved' || this.request.status === 'approved'
        this.deviceIssued = this.request.device_issued_at || this.request.device_condition_issuing
        this.deviceReceived = this.request.device_received_at || this.request.device_condition_receiving || this.request.status === 'returned'
        this.issuingAssessmentSaved = !!this.request.device_condition_issuing
        this.receivingAssessmentSaved = !!this.request.device_condition_receiving
        
        console.log('🔄 Workflow state initialized:', {
          requestApproved: this.requestApproved,
          deviceIssued: this.deviceIssued,
          deviceReceived: this.deviceReceived,
          issuingAssessmentSaved: this.issuingAssessmentSaved,
          receivingAssessmentSaved: this.receivingAssessmentSaved
        })
      },

      async approveRequest() {
        if (!confirm('Are you sure you want to approve this request?')) {
          return
        }

        this.isProcessing = true
        try {
          const requestId = this.$route.params.id
          const response = await deviceBorrowingService.approveRequest(
            requestId,
            '' // No comments needed
          )

          if (response.success) {
            alert('Device borrowing request approved successfully!')
            this.request.ict_approve = 'approved'
            this.requestApproved = true
            this.deviceIssued = true
            
            // Update the request data with the response
            if (response.data) {
              this.request = { ...this.request, ...response.data }
            }
            
            console.log('✅ Request approved, workflow state updated:', {
              requestApproved: this.requestApproved,
              deviceIssued: this.deviceIssued
            })
            
            // Automatically redirect to requests list immediately
            this.$router.push('/ict-approval/requests')
          } else {
            throw new Error(response.message || 'Failed to approve request')
          }
        } catch (error) {
          console.error('Error approving request:', error)
          alert('Error approving request: ' + (error.message || 'Please try again'))
        } finally {
          this.isProcessing = false
        }
      },

      async rejectRequest() {
        if (!confirm('Are you sure you want to reject this request?')) {
          return
        }

        this.isProcessing = true
        try {
          const requestId = this.$route.params.id
          const response = await deviceBorrowingService.rejectRequest(
            requestId,
            'Request rejected by ICT Officer' // Default rejection reason
          )

          if (response.success) {
            alert('Device borrowing request rejected successfully!')
            this.request.ict_approve = 'rejected'
            // Automatically redirect to requests list immediately
            this.$router.push('/ict-approval/requests')
          } else {
            throw new Error(response.message || 'Failed to reject request')
          }
        } catch (error) {
          console.error('Error rejecting request:', error)
          alert('Error rejecting request: ' + (error.message || 'Please try again'))
        } finally {
          this.isProcessing = false
        }
      },

      goBack() {
        this.$router.push('/ict-approval/requests')
      },

      getDeviceDisplayName(deviceType, customDevice) {
        return deviceBorrowingService.getDeviceDisplayName(deviceType, customDevice)
      },

      formatDate(dateString) {
        return deviceBorrowingService.formatDate(dateString)
      },

      getStatusIcon(status) {
        return deviceBorrowingService.getStatusIcon(status)
      },

      getStatusText(status) {
        return deviceBorrowingService.getStatusText(status)
      },

      handleImageError(event) {
        console.warn('Image failed to load:', event.target.src)
        event.target.style.display = 'none'
      },

      // Assessment methods
      async saveIssuingAssessment() {
        // Prevent multiple clicks
        if (this.isProcessingAssessment || this.issuingAssessmentSaved) {
          console.log('⚠️ Assessment already being processed or saved')
          return
        }
        
        if (!this.isIssuingAssessmentComplete) {
          alert('Please complete all required assessment fields before saving.')
          return
        }

        this.isProcessingAssessment = true
        try {
          const requestId = this.$route.params.id
          const assessmentData = {
            device_condition: {
              physical_condition: this.issuingAssessment.physical_condition,
              functionality: this.issuingAssessment.functionality,
              accessories_complete: this.issuingAssessment.accessories_complete,
              visible_damage: this.issuingAssessment.visible_damage,
              damage_description: this.issuingAssessment.visible_damage
                ? this.issuingAssessment.damage_description
                : null
            },
            assessment_notes: this.issuingAssessmentNotes || null
          }

          console.log('💾 Saving issuing assessment:', assessmentData)

          const response = await deviceBorrowingService.saveIssuingAssessment(
            requestId,
            assessmentData
          )

          if (response.success) {
            this.issuingAssessmentSaved = true
            this.deviceIssued = true
            
            // Update request data with response
            if (response.data) {
              this.request = { ...this.request, ...response.data }
            }
            
            alert('Device issuing assessment saved successfully! You can now approve the request.')
            console.log('✅ Issuing assessment saved successfully, approval button enabled')
          } else {
            throw new Error(response.message || 'Failed to save issuing assessment')
          }
        } catch (error) {
          console.error('❌ Error saving issuing assessment:', error)
          alert('Error saving assessment: ' + (error.message || 'Please try again'))
        } finally {
          this.isProcessingAssessment = false
        }
      },

      async saveReceivingAssessment() {
        // Prevent multiple clicks
        if (this.isProcessingAssessment || this.deviceReceived) {
          console.log('⚠️ Assessment already being processed or device already received')
          return
        }
        
        if (!this.isReceivingAssessmentComplete) {
          alert('Please complete all required assessment fields before saving.')
          return
        }

        this.isProcessingAssessment = true
        try {
          const requestId = this.$route.params.id
          const assessmentData = {
            device_condition: {
              physical_condition: this.receivingAssessment.physical_condition,
              functionality: this.receivingAssessment.functionality,
              accessories_complete: this.receivingAssessment.accessories_complete,
              visible_damage: this.receivingAssessment.visible_damage,
              damage_description: this.receivingAssessment.visible_damage
                ? this.receivingAssessment.damage_description
                : null
            },
            assessment_notes: this.receivingAssessmentNotes || null
          }

          console.log('💾 Saving receiving assessment:', assessmentData)

          const response = await deviceBorrowingService.saveReceivingAssessment(
            requestId,
            assessmentData
          )

          if (response.success) {
            this.deviceReceived = true
            this.receivingAssessmentSaved = true
            this.request.status = 'returned'
            this.request.device_received_at = new Date().toISOString()

            // Check if device was returned with damage
            if (this.receivingAssessment.visible_damage) {
              this.request.return_status = 'returned_but_compromised'
            } else {
              this.request.return_status = 'returned'
            }
            
            // Update request data with response
            if (response.data) {
              this.request = { ...this.request, ...response.data }
            }

            alert('Device receiving assessment completed successfully! The device has been returned and the request is now closed.')
            console.log('✅ Receiving assessment saved successfully, workflow completed')

            // Refresh request data to ensure UI is updated
            await this.fetchRequestDetails()
          } else {
            throw new Error(response.message || 'Failed to save receiving assessment')
          }
        } catch (error) {
          console.error('❌ Error saving receiving assessment:', error)
          alert('Error saving assessment: ' + (error.message || 'Please try again'))
        } finally {
          this.isProcessingAssessment = false
        }
      },

      showAssessmentMessage(text, type) {
        this.assessmentMessage = { text, type }

        // Auto-hide messages after 5 seconds
        setTimeout(() => {
          if (this.assessmentMessage && this.assessmentMessage.text === text) {
            this.assessmentMessage = null
          }
        }, 5000)
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

  @keyframes shimmer {
    0% {
      transform: translateX(-100%) skewX(-12deg);
    }
    100% {
      transform: translateX(200%) skewX(-12deg);
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

  .animate-shimmer {
    animation: shimmer 1.5s ease-in-out;
  }

  /* Enhanced button hover effects */
  .group:hover .animate-shimmer {
    animation: shimmer 1.5s ease-in-out infinite;
  }
</style>
