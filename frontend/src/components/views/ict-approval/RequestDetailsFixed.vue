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
          <div class="bg-blue-800/30 backdrop-blur-sm rounded-xl p-6 mb-6 border border-blue-600/40">
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

            <!-- Loading State -->
            <div v-else-if="isLoading" class="text-center py-8">
              <div class="w-16 h-16 border-4 border-blue-600 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
              <p class="text-white">Loading request details...</p>
            </div>

            <!-- Error State -->
            <div v-else class="bg-red-500/20 border border-red-400/40 rounded-lg p-4">
              <p class="text-red-200 text-sm mb-2">⚠️ No request data loaded. Check console for errors.</p>
              <button 
                @click="fetchRequestDetails()" 
                class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
              >
                Retry Loading
              </button>
            </div>
          </div>

          <!-- Device Assessment Section -->
          <div v-if="canTakeAction || true" class="bg-purple-800/30 backdrop-blur-sm rounded-xl p-6 mb-6 border border-purple-600/40">
            <h3 class="text-lg font-bold text-white mb-4">
              <i class="fas fa-clipboard-check mr-2"></i>
              Device Condition Assessment
            </h3>
            
            <!-- Assessment Type Tabs -->
            <div class="mb-6">
              <div class="bg-slate-800/50 p-3 rounded-xl border border-purple-400/20">
                <div class="flex space-x-3">
                  <button
                    @click="assessmentType = 'issuing'"
                    :class="[
                      'flex-1 px-4 py-3 rounded-lg font-bold transition-all duration-300',
                      assessmentType === 'issuing'
                        ? 'bg-purple-600 text-white shadow-lg'
                        : 'bg-slate-700/50 text-purple-200 hover:bg-slate-600/60'
                    ]"
                  >
                    <i class="fas fa-hand-holding mr-2"></i>
                    Device Issuing
                  </button>
                  <button
                    @click="assessmentType = 'receiving'"
                    :class="[
                      'flex-1 px-4 py-3 rounded-lg font-bold transition-all duration-300',
                      assessmentType === 'receiving'
                        ? 'bg-purple-600 text-white shadow-lg'
                        : 'bg-slate-700/50 text-purple-200 hover:bg-slate-600/60'
                    ]"
                  >
                    <i class="fas fa-undo mr-2"></i>
                    Device Receiving
                  </button>
                </div>
              </div>
            </div>

            <!-- Assessment Form -->
            <div class="space-y-6">
              <!-- Physical Condition -->
              <div>
                <label class="block text-sm font-semibold text-purple-200 mb-3">
                  <i class="fas fa-eye mr-2"></i>
                  Physical Condition
                </label>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                  <label
                    v-for="condition in physicalConditions"
                    :key="condition.value"
                    class="cursor-pointer"
                  >
                    <input
                      v-model="assessment.physical_condition"
                      type="radio"
                      :value="condition.value"
                      class="sr-only"
                    />
                    <div
                      :class="[
                        'p-3 rounded-lg border-2 transition-all text-center',
                        assessment.physical_condition === condition.value
                          ? 'bg-purple-600 border-purple-400 text-white'
                          : 'bg-slate-800/50 border-slate-600 text-slate-300 hover:border-purple-400/50'
                      ]"
                    >
                      <div class="font-semibold text-sm">{{ condition.label }}</div>
                    </div>
                  </label>
                </div>
              </div>

              <!-- Functionality -->
              <div>
                <label class="block text-sm font-semibold text-purple-200 mb-3">
                  <i class="fas fa-cogs mr-2"></i>
                  Device Functionality
                </label>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                  <label
                    v-for="func in functionalityOptions"
                    :key="func.value"
                    class="cursor-pointer"
                  >
                    <input
                      v-model="assessment.functionality"
                      type="radio"
                      :value="func.value"
                      class="sr-only"
                    />
                    <div
                      :class="[
                        'p-3 rounded-lg border-2 transition-all text-center',
                        assessment.functionality === func.value
                          ? 'bg-purple-600 border-purple-400 text-white'
                          : 'bg-slate-800/50 border-slate-600 text-slate-300 hover:border-purple-400/50'
                      ]"
                    >
                      <div class="font-semibold text-sm">{{ func.label }}</div>
                    </div>
                  </label>
                </div>
              </div>

              <!-- Accessories Complete -->
              <div>
                <label class="flex items-center cursor-pointer p-4 bg-slate-800/30 rounded-lg border border-purple-500/20 hover:border-purple-400/40 transition-all">
                  <input
                    v-model="assessment.accessories_complete"
                    type="checkbox"
                    class="sr-only"
                  />
                  <div
                    :class="[
                      'w-6 h-6 rounded border-2 flex items-center justify-center mr-4',
                      assessment.accessories_complete
                        ? 'bg-purple-600 border-purple-400'
                        : 'bg-slate-700/50 border-slate-500/50'
                    ]"
                  >
                    <i v-if="assessment.accessories_complete" class="fas fa-check text-white text-sm"></i>
                  </div>
                  <div>
                    <span class="text-white font-semibold block">
                      All accessories are complete and included
                    </span>
                    <span class="text-purple-200/70 text-sm">
                      Verify all components, cables, and documentation are present
                    </span>
                  </div>
                </label>
              </div>

              <!-- Damage Check -->
              <div>
                <label class="flex items-center cursor-pointer p-4 bg-slate-800/30 rounded-lg border border-red-500/20 hover:border-red-400/40 transition-all">
                  <input
                    v-model="assessment.visible_damage"
                    type="checkbox"
                    class="sr-only"
                  />
                  <div
                    :class="[
                      'w-6 h-6 rounded border-2 flex items-center justify-center mr-4',
                      assessment.visible_damage
                        ? 'bg-red-600 border-red-400'
                        : 'bg-slate-700/50 border-slate-500/50'
                    ]"
                  >
                    <i v-if="assessment.visible_damage" class="fas fa-exclamation text-white text-sm"></i>
                  </div>
                  <div>
                    <span class="text-white font-semibold block">
                      Device has visible damage or defects
                    </span>
                    <span class="text-red-200/70 text-sm">
                      Check for scratches, cracks, dents, or missing parts
                    </span>
                  </div>
                </label>
              </div>

              <!-- Damage Description -->
              <div v-if="assessment.visible_damage" class="space-y-2">
                <label class="block text-sm font-semibold text-red-200">
                  <i class="fas fa-edit mr-2"></i>
                  Damage Description
                </label>
                <textarea
                  v-model="assessment.damage_description"
                  rows="4"
                  class="w-full px-3 py-2 bg-slate-800/60 border-2 border-red-500/30 rounded focus:border-red-400 focus:outline-none text-white placeholder-red-200/60"
                  placeholder="Provide detailed description of any visible damage, scratches, or defects..."
                ></textarea>
              </div>

              <!-- Assessment Notes -->
              <div>
                <label class="block text-sm font-semibold text-purple-200 mb-2">
                  <i class="fas fa-sticky-note mr-2"></i>
                  Additional Notes (Optional)
                </label>
                <textarea
                  v-model="assessmentNotes"
                  rows="3"
                  class="w-full px-3 py-2 bg-white/15 border border-purple-300/30 rounded focus:border-purple-400 focus:outline-none text-white placeholder-purple-200/60"
                  placeholder="Any additional observations or recommendations..."
                ></textarea>
              </div>

              <!-- Save Assessment Button -->
              <div class="text-center">
                <button
                  @click="saveAssessment"
                  :disabled="!isAssessmentComplete || isProcessingAssessment"
                  class="px-8 py-4 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-xl hover:from-purple-700 hover:to-indigo-700 transition-all font-bold flex items-center justify-center mx-auto disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <i class="fas fa-save mr-2"></i>
                  {{ isProcessingAssessment ? 'Saving Assessment...' : `${assessmentType === 'issuing' ? 'Issue' : 'Receive'} Device` }}
                </button>
              </div>
            </div>
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
      assessmentType: 'issuing',
      isProcessingAssessment: false,
      assessment: {
        physical_condition: '',
        functionality: '',
        accessories_complete: false,
        visible_damage: false,
        damage_description: ''
      },
      assessmentNotes: '',
      physicalConditions: [
        { value: 'excellent', label: 'Excellent' },
        { value: 'good', label: 'Good' },
        { value: 'fair', label: 'Fair' },
        { value: 'poor', label: 'Poor' }
      ],
      functionalityOptions: [
        { value: 'fully_functional', label: 'Fully Functional' },
        { value: 'partially_functional', label: 'Partially Functional' },
        { value: 'not_functional', label: 'Not Functional' }
      ]
    }
  },
  computed: {
    canTakeAction() {
      return this.request.ict_approve === 'pending' || this.request.ict_status === 'pending'
    },
    isAssessmentComplete() {
      return (
        this.assessment.physical_condition &&
        this.assessment.functionality &&
        (!this.assessment.visible_damage || this.assessment.damage_description)
      )
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

    async saveAssessment() {
      if (!this.isAssessmentComplete) {
        alert('Please complete all required assessment fields')
        return
      }

      const action = this.assessmentType === 'issuing' ? 'issue this device to the borrower' : 'mark this device as received'
      if (!confirm(`Are you sure you want to ${action}?`)) {
        return
      }

      this.isProcessingAssessment = true
      try {
        const requestId = this.$route.params.id
        const assessmentData = {
          device_condition: this.assessment,
          assessment_notes: this.assessmentNotes
        }

        let response
        if (this.assessmentType === 'issuing') {
          response = await deviceBorrowingService.saveIssuingAssessment(requestId, assessmentData)
        } else {
          response = await deviceBorrowingService.saveReceivingAssessment(requestId, assessmentData)
        }

        if (response.success) {
          const statusMessage = this.assessmentType === 'issuing' 
            ? 'Device issued successfully! Assessment saved.'
            : 'Device received successfully! Assessment completed.'
          
          alert(statusMessage)
          
          // Update local request data
          if (response.data) {
            this.request = { ...this.request, ...response.data }
          }
          
          // Refresh the request details
          await this.fetchRequestDetails()
        } else {
          throw new Error(response.message || 'Failed to save assessment')
        }
      } catch (error) {
        console.error('Error saving assessment:', error)
        alert('Error saving assessment: ' + (error.message || 'Unknown error'))
      } finally {
        this.isProcessingAssessment = false
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
