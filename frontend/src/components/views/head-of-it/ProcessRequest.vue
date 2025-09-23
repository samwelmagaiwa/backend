<template>
  <div class="flex flex-col h-screen">
    <Header />
    <div class="flex flex-1 overflow-hidden">
      <div class="sidebar-narrow">
        <ModernSidebar />
      </div>
      <main
        class="flex-1 p-6 bg-gradient-to-br from-blue-900 via-blue-800 to-teal-900 overflow-y-auto"
      >
        <div class="max-w-6xl mx-auto">
          <!-- Header -->
          <div class="mb-6">
            <button
              @click="goBack"
              class="mb-4 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition duration-200 flex items-center text-base"
            >
              <i class="fas fa-arrow-left mr-2"></i>
              Back to Requests
            </button>
            <h1 class="text-3xl font-bold text-white">🔥 HEAD OF IT APPROVAL SYSTEM 🔥</h1>
            <p class="text-blue-200 mt-2 text-lg">
              ✅ This is the CORRECT ProcessRequest.vue component with signature upload enabled
            </p>
            <p class="text-yellow-300 mt-1 text-sm">
              🎯 Component: ProcessRequest.vue | Route: {{ $route.path }}
            </p>
          </div>

          <!-- Error Display -->
          <div
            v-if="error"
            class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6"
          >
            <h3 class="font-bold text-xl">Error</h3>
            <p class="text-lg">{{ error }}</p>
          </div>

          <!-- Loading State -->
          <div v-if="isLoading" class="text-center py-12">
            <div
              class="w-16 h-16 border-4 border-blue-600 border-t-transparent rounded-full animate-spin mx-auto mb-4"
            ></div>
            <p class="text-white text-lg">Loading request details...</p>
          </div>

          <!-- Request Details -->
          <div v-if="request && !isLoading" class="space-y-6">
            <!-- Request Overview -->
            <div class="medical-glass-card rounded-xl p-6">
              <h2 class="text-2xl font-bold text-white mb-4 flex items-center">
                <i class="fas fa-clipboard-list mr-3 text-blue-400"></i>
                Request Overview
              </h2>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <h3 class="text-lg font-semibold text-blue-200 mb-3">Request Information</h3>
                  <div class="space-y-2">
                    <p class="text-white">
                      <span class="font-medium">Request ID:</span>
                      {{ request.request_id || `REQ-${request.id?.toString().padStart(6, '0')}` }}
                    </p>
                    <p class="text-white">
                      <span class="font-medium">Submission Date:</span>
                      {{ formatDate(request.created_at) }}
                    </p>
                    <p class="text-white">
                      <span class="font-medium">Current Status:</span>
                      <span
                        :class="getStatusBadgeClass(request.status)"
                        class="px-2 py-1 rounded text-sm font-medium ml-2"
                      >
                        {{ getStatusText(request.status) }}
                      </span>
                    </p>
                  </div>
                </div>

                <div>
                  <h3 class="text-lg font-semibold text-blue-200 mb-3">Approval History</h3>
                  <div class="space-y-2">
                    <p class="text-white">
                      <span class="font-medium">HOD Approved:</span>
                      {{ formatDate(request.hod_approved_at) }}
                    </p>
                    <p class="text-white">
                      <span class="font-medium">Divisional Approved:</span>
                      {{ formatDate(request.divisional_approved_at) }}
                    </p>
                    <p class="text-white">
                      <span class="font-medium">ICT Director Approved:</span>
                      {{ formatDate(request.ict_director_approved_at) }}
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Applicant Details -->
            <div class="medical-glass-card rounded-xl p-6">
              <h2 class="text-2xl font-bold text-white mb-4 flex items-center">
                <i class="fas fa-user-md mr-3 text-blue-400"></i>
                Applicant Information
              </h2>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-3">
                  <p class="text-white">
                    <span class="font-medium text-blue-200">Full Name:</span>
                    {{ request.staff_name || 'N/A' }}
                  </p>
                  <p class="text-white">
                    <span class="font-medium text-blue-200">PF Number:</span>
                    {{ request.pf_number || 'N/A' }}
                  </p>
                  <p class="text-white">
                    <span class="font-medium text-blue-200">Phone Number:</span>
                    {{ request.phone_number || 'N/A' }}
                  </p>
                </div>
                <div class="space-y-3">
                  <p class="text-white">
                    <span class="font-medium text-blue-200">Department:</span>
                    {{ request.department_name || request.department || 'N/A' }}
                  </p>
                  <p class="text-white">
                    <span class="font-medium text-blue-200">Position:</span>
                    {{ request.position || 'N/A' }}
                  </p>
                  <p class="text-white">
                    <span class="font-medium text-blue-200">Email:</span>
                    {{ request.email || 'N/A' }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Requested Services -->
            <div class="medical-glass-card rounded-xl p-6">
              <h2 class="text-2xl font-bold text-white mb-4 flex items-center">
                <i class="fas fa-layer-group mr-3 text-blue-400"></i>
                Requested Services
              </h2>

              <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div
                  v-if="hasService(request, 'jeeva')"
                  class="bg-blue-600/20 border border-blue-400/40 rounded-lg p-4"
                >
                  <div class="flex items-center mb-2">
                    <i class="fas fa-file-medical text-blue-400 text-xl mr-3"></i>
                    <h3 class="text-white font-semibold text-lg">Jeeva Access</h3>
                  </div>
                  <p class="text-blue-200 text-base">Medical records system access</p>
                </div>

                <div
                  v-if="hasService(request, 'wellsoft')"
                  class="bg-green-600/20 border border-green-400/40 rounded-lg p-4"
                >
                  <div class="flex items-center mb-2">
                    <i class="fas fa-laptop-medical text-green-400 text-xl mr-3"></i>
                    <h3 class="text-white font-semibold text-lg">Wellsoft Access</h3>
                  </div>
                  <p class="text-green-200 text-base">Hospital management system</p>
                </div>

                <div
                  v-if="hasService(request, 'internet')"
                  class="bg-cyan-600/20 border border-cyan-400/40 rounded-lg p-4"
                >
                  <div class="flex items-center mb-2">
                    <i class="fas fa-wifi text-cyan-400 text-xl mr-3"></i>
                    <h3 class="text-white font-semibold text-lg">Internet Access</h3>
                  </div>
                  <p class="text-cyan-200 text-base">Internet connectivity</p>
                </div>
              </div>

              <!-- Internet Purposes if applicable -->
              <div v-if="hasService(request, 'internet') && request.internet_purposes" class="mt-4">
                <h3 class="text-lg font-semibold text-blue-200 mb-2">Internet Usage Purposes:</h3>
                <ul class="list-disc list-inside text-white space-y-1">
                  <li
                    v-for="(purpose, index) in getParsedInternetPurposes(request.internet_purposes)"
                    :key="index"
                    class="text-base"
                  >
                    {{ purpose }}
                  </li>
                </ul>
              </div>
            </div>

            <!-- Head of IT Approval Section -->
            <div class="medical-glass-card rounded-xl p-6">
              <h2 class="text-2xl font-bold text-white mb-4 flex items-center">
                <i class="fas fa-user-tie mr-3 text-blue-400"></i>
                Head of IT Approval
              </h2>

              <!-- Authenticated User Info -->
              <div class="bg-blue-600/10 border border-blue-400/20 rounded-lg p-4 mb-6">
                <h3 class="text-lg font-semibold text-blue-200 mb-2">Approving Officer</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <p class="text-white">
                      <span class="font-medium text-blue-200">Name:</span>
                      {{ userName || 'Loading...' }}
                    </p>
                    <p class="text-white">
                      <span class="font-medium text-blue-200">Role:</span> Head of IT
                    </p>
                  </div>
                  <div>
                    <p class="text-white">
                      <span class="font-medium text-blue-200">Approval Date:</span>
                      {{ getCurrentDate() }}
                    </p>
                    <p class="text-white">
                      <span class="font-medium text-blue-200">Status:</span>
                      <span v-if="!signatureFile" class="text-yellow-300"
                        >Pending Signature Upload</span
                      >
                      <span v-else class="text-green-300">Ready for Decision</span>
                    </p>
                  </div>
                </div>
              </div>

              <!-- Digital Signature Upload -->
              <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                <i class="fas fa-signature mr-2 text-blue-400"></i>
                Digital Signature Upload
              </h3>

              <div class="bg-blue-600/10 border border-blue-400/20 rounded-lg p-4 mb-4">
                <p class="text-blue-200 text-base">
                  <i class="fas fa-info-circle mr-2"></i>
                  Please upload your digital signature to approve or reject this request.
                </p>
              </div>

              <div class="flex items-center gap-4">
                <!-- Signature Preview -->
                <div
                  class="w-32 h-16 border-2 border-blue-300/30 rounded-lg bg-blue-100/20 flex items-center justify-center overflow-hidden"
                >
                  <img
                    v-if="signaturePreview"
                    :src="signaturePreview"
                    alt="Signature"
                    class="max-w-full max-h-full object-contain"
                  />
                  <div v-else class="text-center">
                    <i class="fas fa-signature text-blue-400/50 text-xl mb-1"></i>
                    <p class="text-xs text-blue-400">No signature</p>
                  </div>
                </div>

                <!-- Upload Button -->
                <div>
                  <input
                    ref="signatureInput"
                    type="file"
                    accept=".png,.jpg,.jpeg"
                    @change="onSignatureChange"
                    class="hidden"
                  />
                  <button
                    @click="$refs.signatureInput.click()"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200 flex items-center text-base font-medium"
                  >
                    <i class="fas fa-upload mr-2"></i>
                    {{ signatureFile ? 'Change Signature' : 'Upload Signature' }}
                  </button>
                  <p class="text-xs text-blue-300 mt-1">PNG, JPG, JPEG (Max: 5MB)</p>
                </div>

                <!-- Clear Button -->
                <button
                  v-if="signatureFile"
                  @click="clearSignature"
                  class="px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition duration-200 text-base"
                >
                  <i class="fas fa-times"></i>
                </button>
              </div>
            </div>

            <!-- Action Buttons -->
            <div v-if="isPendingApproval(request.status)" class="medical-glass-card rounded-xl p-6">
              <h2 class="text-2xl font-bold text-white mb-4 flex items-center">
                <i class="fas fa-gavel mr-3 text-blue-400"></i>
                Decision Required
              </h2>

              <div class="flex gap-4">
                <button
                  @click="approveRequest"
                  :disabled="!signatureFile || isProcessing"
                  class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed transition duration-200 flex items-center text-lg font-medium"
                >
                  <i
                    v-if="isProcessing && actionType === 'approve'"
                    class="fas fa-spinner fa-spin mr-2"
                  ></i>
                  <i v-else class="fas fa-check mr-2"></i>
                  {{
                    isProcessing && actionType === 'approve' ? 'Approving...' : 'Approve Request'
                  }}
                </button>

                <button
                  @click="showRejectModal = true"
                  :disabled="!signatureFile || isProcessing"
                  class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed transition duration-200 flex items-center text-lg font-medium"
                >
                  <i class="fas fa-times mr-2"></i>
                  Reject Request
                </button>
              </div>

              <p v-if="!signatureFile" class="text-yellow-300 mt-2 text-base">
                <i class="fas fa-exclamation-triangle mr-1"></i>
                Please upload your signature before making a decision.
              </p>
            </div>
          </div>
        </div>

        <!-- Success Message Card -->
        <div
          v-if="showSuccessMessage"
          class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4"
        >
          <div
            class="bg-white rounded-2xl shadow-2xl max-w-md w-full transform transition-all duration-500 scale-100"
          >
            <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4 text-center">
              <div
                class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-3 shadow-lg"
              >
                <i class="fas fa-check text-green-600 text-2xl"></i>
              </div>
              <h3 class="text-2xl font-bold text-white mb-1">Request Approved!</h3>
            </div>

            <div class="px-6 py-6">
              <p class="text-gray-700 leading-relaxed text-lg mb-6 text-center">
                Now you can select the ICT Officer who will be assigned this task of releasing
                access to
                <span class="font-semibold text-blue-600">{{
                  request?.staff_name || 'the requester'
                }}</span
                >.
              </p>

              <button
                @click="selectIctOfficer"
                class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white py-3 px-6 rounded-xl font-semibold hover:from-blue-700 hover:to-blue-800 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 text-lg"
              >
                Select ICT Officer
              </button>
            </div>
          </div>
        </div>

        <!-- Reject Modal -->
        <div
          v-if="showRejectModal"
          class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4"
        >
          <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full">
            <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4 text-center">
              <h3 class="text-xl font-bold text-white">Reject Request</h3>
            </div>

            <div class="px-6 py-6">
              <p class="text-gray-700 mb-4 text-base">
                Please provide a reason for rejecting this request:
              </p>
              <textarea
                v-model="rejectionReason"
                rows="4"
                placeholder="Enter reason for rejection..."
                class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 text-base"
              ></textarea>

              <div class="flex gap-3 mt-6">
                <button
                  @click="confirmReject"
                  :disabled="!rejectionReason.trim() || isProcessing"
                  class="flex-1 bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 disabled:opacity-50 transition duration-200 text-base font-medium"
                >
                  <i
                    v-if="isProcessing && actionType === 'reject'"
                    class="fas fa-spinner fa-spin mr-2"
                  ></i>
                  {{ isProcessing && actionType === 'reject' ? 'Rejecting...' : 'Confirm Reject' }}
                </button>
                <button
                  @click="showRejectModal = false"
                  :disabled="isProcessing"
                  class="flex-1 bg-gray-500 text-white py-2 px-4 rounded-lg hover:bg-gray-600 transition duration-200 text-base font-medium"
                >
                  Cancel
                </button>
              </div>
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
  import headOfItService from '@/services/headOfItService'
  import { useAuth } from '@/composables/useAuth'

  export default {
    name: 'ProcessRequest',
    components: {
      Header,
      ModernSidebar
    },
    setup() {
      const { userName } = useAuth()
      return {
        userName
      }
    },
    data() {
      return {
        request: null,
        isLoading: false,
        error: null,
        signatureFile: null,
        signaturePreview: '',
        isProcessing: false,
        actionType: null,
        showSuccessMessage: false,
        showRejectModal: false,
        rejectionReason: ''
      }
    },
    async mounted() {
      await this.loadRequest()
    },
    methods: {
      async loadRequest() {
        const requestId = this.$route.params.id
        if (!requestId) {
          this.error = 'No request ID provided'
          return
        }

        this.isLoading = true
        this.error = null

        try {
          const result = await headOfItService.getRequestById(requestId)
          if (result.success) {
            this.request = result.data
          } else {
            this.error = result.message
          }
        } catch (error) {
          this.error = 'Failed to load request details'
        } finally {
          this.isLoading = false
        }
      },

      async approveRequest() {
        if (!this.signatureFile) {
          alert('Please upload your signature first')
          return
        }

        this.isProcessing = true
        this.actionType = 'approve'

        try {
          const result = await headOfItService.approveRequest(this.request.id, this.signatureFile)
          if (result.success) {
            this.showSuccessMessage = true
          } else {
            alert('Failed to approve request: ' + result.message)
          }
        } catch (error) {
          alert('Error approving request: ' + error.message)
        } finally {
          this.isProcessing = false
          this.actionType = null
        }
      },

      async confirmReject() {
        if (!this.signatureFile) {
          alert('Please upload your signature first')
          return
        }

        if (!this.rejectionReason.trim()) {
          alert('Please provide a reason for rejection')
          return
        }

        this.isProcessing = true
        this.actionType = 'reject'

        try {
          const result = await headOfItService.rejectRequest(
            this.request.id,
            this.signatureFile,
            this.rejectionReason
          )
          if (result.success) {
            alert('Request rejected successfully')
            this.goBack()
          } else {
            alert('Failed to reject request: ' + result.message)
          }
        } catch (error) {
          alert('Error rejecting request: ' + error.message)
        } finally {
          this.isProcessing = false
          this.actionType = null
          this.showRejectModal = false
        }
      },

      selectIctOfficer() {
        this.$router.push(`/head_of_it-dashboard/select-ict-officer/${this.request.id}`)
      },

      onSignatureChange(event) {
        const file = event.target.files[0]
        if (!file) return

        // Validate file type
        if (!['image/png', 'image/jpeg', 'image/jpg'].includes(file.type)) {
          alert('Please select a PNG, JPG, or JPEG file')
          return
        }

        // Validate file size (5MB)
        if (file.size > 5 * 1024 * 1024) {
          alert('File size must be less than 5MB')
          return
        }

        this.signatureFile = file

        // Create preview
        const reader = new FileReader()
        reader.onload = (e) => {
          this.signaturePreview = e.target.result
        }
        reader.readAsDataURL(file)
      },

      clearSignature() {
        this.signatureFile = null
        this.signaturePreview = ''
        if (this.$refs.signatureInput) {
          this.$refs.signatureInput.value = ''
        }
      },

      goBack() {
        this.$router.push('/head_of_it-dashboard/combined-requests')
      },

      isPendingApproval(status) {
        return status === 'ict_director_approved'
      },

      hasService(request, serviceType) {
        if (!request?.request_types) return false

        if (Array.isArray(request.request_types)) {
          return request.request_types.some((type) =>
            type.toLowerCase().includes(serviceType.toLowerCase())
          )
        }

        if (typeof request.request_types === 'string') {
          return request.request_types.toLowerCase().includes(serviceType.toLowerCase())
        }

        return false
      },

      getParsedInternetPurposes(purposes) {
        if (Array.isArray(purposes)) {
          return purposes.filter((p) => p && p.trim())
        }
        if (typeof purposes === 'string') {
          return purposes
            .split(',')
            .map((p) => p.trim())
            .filter((p) => p)
        }
        return []
      },

      getStatusBadgeClass(status) {
        const statusClasses = {
          ict_director_approved: 'bg-yellow-500 text-yellow-900',
          head_of_it_approved: 'bg-green-500 text-green-900',
          head_of_it_rejected: 'bg-red-500 text-red-900'
        }
        return statusClasses[status] || 'bg-gray-500 text-gray-900'
      },

      getStatusText(status) {
        const statusTexts = {
          ict_director_approved: 'Pending Head of IT Approval',
          head_of_it_approved: 'Approved by Head of IT',
          head_of_it_rejected: 'Rejected by Head of IT'
        }
        return (
          statusTexts[status] || status.replace(/_/g, ' ').replace(/\b\w/g, (l) => l.toUpperCase())
        )
      },

      getCurrentDate() {
        try {
          return new Date().toLocaleDateString('en-GB', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
          })
        } catch {
          return 'Today'
        }
      },

      formatDate(dateString) {
        if (!dateString) return 'N/A'
        try {
          return new Date(dateString).toLocaleDateString('en-GB', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
          })
        } catch {
          return 'Invalid Date'
        }
      }
    }
  }
</script>

<style scoped>
  .sidebar-narrow {
    flex-shrink: 0;
  }

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
