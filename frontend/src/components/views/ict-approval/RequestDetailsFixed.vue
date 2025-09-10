<template>
  <div class="flex flex-col h-screen">
    <Header />
    <div class="flex flex-1 overflow-hidden">
      <ModernSidebar />
      <main class="flex-1 p-8 bg-gradient-to-br from-blue-900 via-blue-800 to-teal-900 overflow-y-auto relative">
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
          <div class="booking-glass-card rounded-t-3xl p-8 mb-0 border-b border-blue-300/30 animate-fade-in">
            <div class="flex justify-between items-center">
              <!-- Left Logo -->
              <div class="w-28 h-28 mr-8 transform hover:scale-110 transition-transform duration-300">
                <div class="w-full h-full bg-gradient-to-br from-blue-500/20 to-teal-500/20 rounded-2xl backdrop-blur-sm border-2 border-blue-300/40 flex items-center justify-center shadow-2xl hover:shadow-blue-500/25">
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
                <h1 class="text-2xl font-bold text-white mb-4 tracking-wide drop-shadow-lg animate-fade-in">
                  MUHIMBILI NATIONAL HOSPITAL
                </h1>
                <div class="relative inline-block mb-4">
                  <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-2 rounded-full text-base font-bold shadow-2xl transform hover:scale-105 transition-all duration-300 border-2 border-blue-400/60">
                    <span class="relative z-10 flex items-center gap-3">
                      <i class="fas fa-clipboard-check text-base"></i>
                      REQUEST DETAILS & ASSESSMENT
                    </span>
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-700 to-blue-800 rounded-full opacity-0 hover:opacity-100 transition-opacity duration-300"></div>
                  </div>
                </div>
                <h2 class="text-lg font-bold text-blue-100 tracking-wide drop-shadow-md animate-fade-in-delay">
                  ICT OFFICER REVIEW PANEL
                </h2>
              </div>

              <!-- Right Logo -->
              <div class="w-28 h-28 ml-8 transform hover:scale-110 transition-transform duration-300">
                <div class="w-full h-full bg-gradient-to-br from-teal-500/20 to-blue-500/20 rounded-2xl backdrop-blur-sm border-2 border-teal-300/40 flex items-center justify-center shadow-2xl hover:shadow-teal-500/25">
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
                    <label class="block text-sm font-semibold text-blue-200 mb-1">Borrower Name</label>
                    <div class="px-3 py-2 bg-white/10 border border-blue-300/30 rounded text-white">
                      {{ request.borrower_name || 'Unknown' }}
                    </div>
                  </div>
                  
                  <div>
                    <label class="block text-sm font-semibold text-blue-200 mb-1">Department</label>
                    <div class="px-3 py-2 bg-white/10 border border-blue-300/30 rounded text-white">
                      {{ request.department || 'Unknown Department' }}
                    </div>
                  </div>
                  
                  <div>
                    <label class="block text-sm font-semibold text-blue-200 mb-1">Device Type</label>
                    <div class="px-3 py-2 bg-white/10 border border-blue-300/30 rounded text-white">
                      {{ request.device_name || getDeviceDisplayName(request.device_type, request.custom_device) }}
                    </div>
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-semibold text-blue-200 mb-1">Reason</label>
                  <div class="px-3 py-2 bg-white/10 border border-blue-300/30 rounded text-white">
                    {{ request.reason || request.purpose || 'No reason provided' }}
                  </div>
                </div>
              </div>

              <!-- Device Condition Assessment Section -->
              <div v-if="!isLoading && Object.keys(request).length > 0" class="bg-blue-800/30 backdrop-blur-sm rounded-xl p-6 border border-blue-600/40 animate-fade-in">
                <div class="flex items-center gap-3 mb-6">
                  <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-700 rounded-xl flex items-center justify-center">
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
                      <label v-for="condition in physicalConditions" :key="condition.value" 
                             class="flex items-center p-3 bg-blue-900/20 rounded-lg border border-blue-600/30 hover:border-blue-500/50 transition-all cursor-pointer group">
                        <input 
                          type="radio" 
                          :value="condition.value" 
                          v-model="assessment.physicalCondition"
                          class="mr-3 text-blue-600 focus:ring-blue-500"
                        >
                        <div class="flex items-center gap-2">
                          <i :class="condition.icon + ' text-' + condition.color"></i>
                          <span class="text-white group-hover:text-blue-100 font-medium">{{ condition.label }}</span>
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
                      <label v-for="functionality in functionalityOptions" :key="functionality.value" 
                             class="flex items-center p-3 bg-purple-900/20 rounded-lg border border-purple-600/30 hover:border-purple-500/50 transition-all cursor-pointer group">
                        <input 
                          type="radio" 
                          :value="functionality.value" 
                          v-model="assessment.functionality"
                          class="mr-3 text-purple-600 focus:ring-purple-500"
                        >
                        <div class="flex items-center gap-2">
                          <i :class="functionality.icon + ' text-' + functionality.color"></i>
                          <span class="text-white group-hover:text-purple-100 font-medium">{{ functionality.label }}</span>
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
                          v-model="assessment.accessoriesComplete"
                          class="mr-3 text-purple-600 focus:ring-purple-500 rounded"
                        >
                        <div class="flex items-center gap-2">
                          <i class="fas fa-check-circle text-green-400"></i>
                          <span class="text-white group-hover:text-purple-100 font-medium">All accessories included</span>
                        </div>
                      </label>
                      <p class="text-purple-300 text-xs mt-2 ml-6">Check if all required accessories are present and functional</p>
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
                          v-model="assessment.hasDamage"
                          class="mr-3 text-red-600 focus:ring-red-500 rounded"
                        >
                        <div class="flex items-center gap-2">
                          <i class="fas fa-tools text-red-400"></i>
                          <span class="text-white group-hover:text-purple-100 font-medium">Device has damage/issues</span>
                        </div>
                      </label>
                      <textarea 
                        v-if="assessment.hasDamage"
                        v-model="assessment.damageDescription"
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
                    v-model="assessment.notes"
                    rows="3"
                    class="w-full px-4 py-3 bg-purple-900/20 border border-purple-600/30 rounded-lg focus:border-purple-400 focus:outline-none text-white placeholder-purple-300/60 resize-none"
                    placeholder="Enter any additional observations, recommendations, or notes about the device condition..."
                  ></textarea>
                </div>

                <!-- Assessment Actions -->
                <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-purple-600/30">
                  <button
                    @click="saveAssessment"
                    :disabled="!isAssessmentValid || isSavingAssessment"
                    class="flex-1 px-6 py-3 bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-lg font-semibold transition-all duration-300 transform hover:scale-105 hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100 flex items-center justify-center gap-2"
                  >
                    <i class="fas fa-save"></i>
                    {{ isSavingAssessment ? 'Saving Assessment...' : `Save ${assessmentType === 'issuing' ? 'Issuing' : 'Receiving'} Assessment` }}
                  </button>
                  
                  <button
                    @click="resetAssessment"
                    :disabled="isSavingAssessment"
                    class="px-6 py-3 bg-gray-600 text-white rounded-lg font-semibold transition-colors hover:bg-gray-700 disabled:opacity-50 flex items-center gap-2"
                  >
                    <i class="fas fa-undo"></i>
                    Reset Form
                  </button>
                </div>

                <!-- Assessment Status Messages -->
                <div v-if="assessmentMessage" class="mt-4">
                  <div :class="[
                    'p-4 rounded-lg border flex items-center gap-3',
                    assessmentMessage.type === 'success' 
                      ? 'bg-green-500/20 border-green-400/40 text-green-200' 
                      : 'bg-red-500/20 border-red-400/40 text-red-200'
                  ]">
                    <i :class="[
                      'fas',
                      assessmentMessage.type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'
                    ]"></i>
                    <span>{{ assessmentMessage.text }}</span>
                  </div>
                </div>
              </div>

              <!-- Loading State -->
              <div v-else-if="isLoading" class="text-center py-8">
                <div class="w-16 h-16 border-4 border-blue-600 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
                <p class="text-white">Loading request details...</p>
              </div>

              <!-- Error State -->
              <div v-else class="bg-red-500/20 border border-red-400/40 rounded-lg p-4">
                <p class="text-red-200 text-sm mb-2">⚠️ No request data loaded. Check console for errors.</p>
                <button @click="fetchRequestDetails()" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                  Retry Loading
                </button>
              </div>

              <!-- ICT Officer Actions -->
              <div v-if="canTakeAction" class="bg-green-800/30 backdrop-blur-sm rounded-xl p-6 border border-green-600/40">
                <h3 class="text-lg font-bold text-white mb-4">ICT Officer Actions</h3>
                
                <div class="mb-4">
                  <label class="block text-sm font-semibold text-green-200 mb-2">Comments (Optional)</label>
                  <textarea
                    v-model="approvalComments"
                    rows="3"
                    class="w-full px-3 py-2 bg-white/15 border border-green-300/30 rounded focus:border-green-400 focus:outline-none text-white placeholder-green-200/60"
                    placeholder="Enter your comments..."
                  ></textarea>
                </div>

                <div class="flex gap-3">
                  <button
                    @click="approveRequest"
                    :disabled="isProcessing"
                    class="px-6 py-3 bg-green-600 text-white rounded hover:bg-green-700 transition-colors font-bold flex items-center disabled:opacity-50"
                  >
                    <i class="fas fa-check mr-2"></i>
                    {{ isProcessing ? 'Processing...' : 'Approve Request' }}
                  </button>

                  <button
                    @click="rejectRequest"
                    :disabled="isProcessing"
                    class="px-6 py-3 bg-red-600 text-white rounded hover:bg-red-700 transition-colors font-bold flex items-center disabled:opacity-50"
                  >
                    <i class="fas fa-times mr-2"></i>
                    {{ isProcessing ? 'Processing...' : 'Reject Request' }}
                  </button>
                </div>
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
      approvalComments: '',
      isLoading: false,
      isProcessing: false,
      
      // Assessment data
      assessmentType: 'issuing', // 'issuing' or 'receiving'
      assessment: {
        physicalCondition: '',
        functionality: '',
        accessoriesComplete: false,
        hasDamage: false,
        damageDescription: '',
        notes: ''
      },
      isSavingAssessment: false,
      assessmentMessage: null,
      
      // Assessment options
      physicalConditions: [
        { value: 'excellent', label: 'Excellent', icon: 'fas fa-star', color: 'green-400' },
        { value: 'good', label: 'Good', icon: 'fas fa-thumbs-up', color: 'blue-400' },
        { value: 'fair', label: 'Fair', icon: 'fas fa-exclamation', color: 'yellow-400' },
        { value: 'poor', label: 'Poor', icon: 'fas fa-times', color: 'red-400' }
      ],
      functionalityOptions: [
        { value: 'fully', label: 'Fully Functional', icon: 'fas fa-check-circle', color: 'green-400' },
        { value: 'partially', label: 'Partially Functional', icon: 'fas fa-exclamation-circle', color: 'yellow-400' },
        { value: 'not', label: 'Not Functional', icon: 'fas fa-times-circle', color: 'red-400' }
      ]
    }
  },
  computed: {
    canTakeAction() {
      return this.request.ict_approve === 'pending' || this.request.ict_status === 'pending'
    },
    
    isAssessmentValid() {
      const { physicalCondition, functionality } = this.assessment
      const requiredFields = physicalCondition && functionality
      const damageValid = !this.assessment.hasDamage || this.assessment.damageDescription.trim()
      return requiredFields && damageValid
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
          console.log('✅ Request details loaded:', this.request)
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

    async approveRequest() {
      if (!confirm('Are you sure you want to approve this request?')) {
        return
      }

      this.isProcessing = true
      try {
        const requestId = this.$route.params.id
        const response = await deviceBorrowingService.approveRequest(requestId, this.approvalComments)

        if (response.success) {
          alert('Device borrowing request approved successfully!')
          this.request.ict_approve = 'approved'
          setTimeout(() => {
            this.$router.push('/ict-approval/requests')
          }, 1500)
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
          this.approvalComments || 'No reason provided'
        )

        if (response.success) {
          alert('Device borrowing request rejected successfully!')
          this.request.ict_approve = 'rejected'
          setTimeout(() => {
            this.$router.push('/ict-approval/requests')
          }, 1500)
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

    handleImageError(event) {
      console.warn('Image failed to load:', event.target.src)
      event.target.style.display = 'none'
    },

    // Assessment methods
    async saveAssessment() {
      if (!this.isAssessmentValid) {
        this.showAssessmentMessage('Please fill in all required fields', 'error')
        return
      }

      this.isSavingAssessment = true
      this.assessmentMessage = null

      try {
        const requestId = this.$route.params.id
        const assessmentData = {
          physical_condition: this.assessment.physicalCondition,
          functionality: this.assessment.functionality === 'fully' ? 'fully_functional' : 
                        this.assessment.functionality === 'partially' ? 'partially_functional' : 'not_functional',
          accessories_complete: this.assessment.accessoriesComplete,
          visible_damage: this.assessment.hasDamage,
          damage_description: this.assessment.hasDamage ? this.assessment.damageDescription : null,
          notes: this.assessment.notes || null
        }

        console.log('💾 Saving assessment:', assessmentData)

        // Call the appropriate service method based on assessment type
        const response = this.assessmentType === 'issuing' 
          ? await deviceBorrowingService.saveIssuingAssessment(requestId, assessmentData)
          : await deviceBorrowingService.saveReceivingAssessment(requestId, assessmentData)

        if (response.success) {
          this.showAssessmentMessage('Assessment saved successfully!', 'success')
          
          // Update request status based on assessment type
          if (this.assessmentType === 'issuing') {
            this.request.status = 'in_use'
            this.request.device_issued_at = new Date().toISOString()
          } else {
            this.request.status = 'returned'
            this.request.device_received_at = new Date().toISOString()
            // Check if device was returned with damage
            if (this.assessment.hasDamage) {
              this.request.return_status = 'returned_but_compromised'
            } else {
              this.request.return_status = 'returned'
            }
          }
          
          // Refresh request data
          await this.fetchRequestDetails()
          
          // Auto-hide success message after 3 seconds
          setTimeout(() => {
            this.assessmentMessage = null
          }, 3000)
        } else {
          throw new Error(response.message || 'Failed to save assessment')
        }
      } catch (error) {
        console.error('❌ Error saving assessment:', error)
        this.showAssessmentMessage(
          'Error saving assessment: ' + (error.message || 'Please try again'), 
          'error'
        )
      } finally {
        this.isSavingAssessment = false
      }
    },

    resetAssessment() {
      if (confirm('Are you sure you want to reset the assessment form?')) {
        this.assessment = {
          physicalCondition: '',
          functionality: '',
          accessoriesComplete: false,
          hasDamage: false,
          damageDescription: '',
          notes: ''
        }
        this.assessmentMessage = null
      }
    },

    showAssessmentMessage(text, type) {
      this.assessmentMessage = { text, type }
      
      // Auto-hide error messages after 5 seconds
      if (type === 'error') {
        setTimeout(() => {
          if (this.assessmentMessage && this.assessmentMessage.type === 'error') {
            this.assessmentMessage = null
          }
        }, 5000)
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
  0%, 100% {
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
</style>
