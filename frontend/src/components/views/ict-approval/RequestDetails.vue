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
                  />
                </div>
              </div>
            </div>
          </div>

          <!-- Main Content -->
          <div class="booking-glass-card rounded-b-3xl overflow-hidden animate-slide-up">
            <div class="p-8 space-y-8">
              <!-- Back Button -->
              <div class="flex items-center justify-between mb-6">
                <button
                  @click="goBack"
                  class="inline-flex items-center px-6 py-3 bg-gray-600/80 text-white rounded-lg hover:bg-gray-700/80 transition-all duration-300 font-semibold shadow-lg hover:shadow-xl transform hover:scale-105 backdrop-blur-sm border border-gray-500/50 text-sm"
                >
                  <i class="fas fa-arrow-left mr-2"></i>
                  Back to Requests List
                </button>

                <div class="flex items-center space-x-4">
                  <span
                    :class="
                      getStatusBadgeClass(
                        request.ict_approve || request.ict_status || request.status
                      )
                    "
                    class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium"
                  >
                    <i
                      :class="
                        getStatusIcon(request.ict_approve || request.ict_status || request.status)
                      "
                      class="mr-2"
                    ></i>
                    {{ getStatusText(request.ict_approve || request.ict_status || request.status) }}
                  </span>
                </div>
              </div>

              <!-- Request Details Section -->
              <div
                class="booking-card bg-gradient-to-r from-teal-600/25 to-blue-600/25 border-2 border-teal-400/40 p-6 rounded-2xl backdrop-blur-sm hover:shadow-xl hover:shadow-teal-500/20 transition-all duration-500 group"
              >
                <div class="flex items-center space-x-4 mb-6">
                  <div
                    class="w-12 h-12 bg-gradient-to-br from-teal-500 to-blue-600 rounded-lg flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-teal-300/50"
                  >
                    <i class="fas fa-info-circle text-white text-lg"></i>
                  </div>
                  <h3 class="text-xl font-bold text-white flex items-center">
                    <i class="fas fa-user mr-2 text-teal-300"></i>
                    Borrower Information
                  </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                  <!-- Request ID -->
                  <div class="group">
                    <label class="block text-sm font-semibold text-blue-100 mb-2 flex items-center">
                      <i class="fas fa-hashtag mr-2 text-teal-300 text-sm"></i>
                      Request ID
                    </label>
                    <div
                      class="px-4 py-3 bg-white/10 border border-blue-300/30 rounded-lg text-white text-sm backdrop-blur-sm font-mono"
                    >
                      {{ request.request_id || `REQ-${String(request.id).padStart(6, '0')}` }}
                    </div>
                  </div>

                  <!-- Borrower Name -->
                  <div class="group">
                    <label class="block text-sm font-semibold text-blue-100 mb-2 flex items-center">
                      <i class="fas fa-user mr-2 text-teal-300 text-sm"></i>
                      Borrower Name
                    </label>
                    <div
                      class="px-4 py-3 bg-white/10 border border-blue-300/30 rounded-lg text-white text-sm backdrop-blur-sm"
                    >
                      {{ request.borrower_name || request.borrowerName || 'Unknown' }}
                    </div>
                  </div>

                  <!-- Department -->
                  <div class="group">
                    <label class="block text-sm font-semibold text-blue-100 mb-2 flex items-center">
                      <i class="fas fa-building mr-2 text-teal-300 text-sm"></i>
                      Department
                    </label>
                    <div
                      class="px-4 py-3 bg-white/10 border border-blue-300/30 rounded-lg text-white text-sm backdrop-blur-sm"
                    >
                      {{ request.department || 'Unknown Department' }}
                    </div>
                  </div>

                  <!-- Phone Number -->
                  <div class="group">
                    <label class="block text-sm font-semibold text-blue-100 mb-2 flex items-center">
                      <i class="fas fa-phone mr-2 text-teal-300 text-sm"></i>
                      Phone Number
                    </label>
                    <div
                      class="px-4 py-3 bg-white/10 border border-blue-300/30 rounded-lg text-white text-sm backdrop-blur-sm"
                    >
                      {{ request.borrower_phone || request.phoneNumber || 'No phone provided' }}
                    </div>
                  </div>

                  <!-- PF Number -->
                  <div v-if="request.pf_number" class="group">
                    <label class="block text-sm font-semibold text-blue-100 mb-2 flex items-center">
                      <i class="fas fa-id-badge mr-2 text-teal-300 text-sm"></i>
                      PF Number
                    </label>
                    <div
                      class="px-4 py-3 bg-white/10 border border-blue-300/30 rounded-lg text-white text-sm backdrop-blur-sm font-mono"
                    >
                      {{ request.pf_number }}
                    </div>
                  </div>

                  <!-- Device Type -->
                  <div class="group">
                    <label class="block text-sm font-semibold text-blue-100 mb-2 flex items-center">
                      <i class="fas fa-laptop mr-2 text-teal-300 text-sm"></i>
                      Device Type
                    </label>
                    <div
                      class="px-4 py-3 bg-white/10 border border-blue-300/30 rounded-lg text-white text-sm backdrop-blur-sm"
                    >
                      {{
                        request.device_name ||
                        getDeviceDisplayName(
                          request.device_type || request.deviceType,
                          request.custom_device || request.customDevice
                        )
                      }}
                    </div>
                  </div>

                  <!-- Booking Date -->
                  <div class="group">
                    <label class="block text-sm font-semibold text-blue-100 mb-2 flex items-center">
                      <i class="fas fa-calendar mr-2 text-teal-300 text-sm"></i>
                      Booking Date
                    </label>
                    <div
                      class="px-4 py-3 bg-white/10 border border-blue-300/30 rounded-lg text-white text-sm backdrop-blur-sm"
                    >
                      {{ formatDate(request.booking_date || request.bookingDate) }}
                    </div>
                  </div>

                  <!-- Return Date & Time -->
                  <div class="group">
                    <label class="block text-sm font-semibold text-blue-100 mb-2 flex items-center">
                      <i class="fas fa-calendar-minus mr-2 text-teal-300 text-sm"></i>
                      Return Date & Time
                    </label>
                    <div
                      class="px-4 py-3 bg-white/10 border border-blue-300/30 rounded-lg text-white text-sm backdrop-blur-sm"
                    >
                      {{ formatDate(request.collection_date || request.collectionDate) }} at
                      {{ request.return_time || request.returnTime || 'No time specified' }}
                    </div>
                  </div>

                  <!-- Signature Status -->
                  <div class="group">
                    <label
                      class="block text-sm font-semibold text-blue-100 mb-2 flex items-center justify-center"
                    >
                      <i class="fas fa-signature mr-2 text-teal-300 text-sm"></i>
                      Signature Status
                    </label>
                    <div
                      class="px-4 py-3 bg-white/10 border border-blue-300/30 rounded-lg text-white text-sm backdrop-blur-sm"
                    >
                      <div
                        v-if="request.has_signature || request.signature || request.signature_path"
                        class="flex items-center justify-center text-green-400"
                      >
                        <i class="fas fa-check-circle mr-2"></i>
                        <span class="font-medium">Digitally Signed</span>
                      </div>
                      <div v-else class="flex items-center justify-center text-center">
                        <div class="text-center">
                          <div class="mb-2">
                            <i class="fas fa-signature text-teal-300 text-2xl mb-2"></i>
                            <p class="text-blue-100 text-sm font-medium">No signature uploaded</p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Reason for Borrowing -->
                <div class="mt-6">
                  <label class="block text-sm font-semibold text-blue-100 mb-2 flex items-center">
                    <i class="fas fa-comment-alt mr-2 text-teal-300 text-sm"></i>
                    Reason for Borrowing
                  </label>
                  <div
                    class="px-4 py-3 bg-white/10 border border-blue-300/30 rounded-lg text-white text-sm backdrop-blur-sm"
                  >
                    {{ request.purpose || request.reason || 'No reason provided' }}
                  </div>
                </div>

                <!-- Digital Signature -->
                <div
                  v-if="request.has_signature || request.signature || request.signature_path"
                  class="mt-6"
                >
                  <label class="block text-sm font-semibold text-blue-100 mb-2 flex items-center">
                    <i class="fas fa-signature mr-2 text-teal-300 text-sm"></i>
                    Digital Signature
                  </label>
                  <div
                    class="px-4 py-3 bg-white/10 border border-blue-300/30 rounded-lg backdrop-blur-sm"
                  >
                    <div class="flex flex-col items-center space-y-3">
                      <div
                        class="inline-block bg-white/20 p-3 rounded-lg border border-teal-300/30"
                      >
                        <img
                          :src="request.signature_url || request.signature"
                          alt="Digital Signature"
                          class="max-h-16 max-w-48 object-contain"
                        />
                      </div>
                      <div class="flex items-center text-green-400">
                        <i class="fas fa-check-circle mr-2"></i>
                        <div class="text-center">
                          <span class="text-sm font-semibold block">Verified</span>
                          <span class="text-xs text-green-300">Signature captured</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- ICT Officer Actions -->
              <div
                v-if="canTakeAction"
                class="booking-card bg-gradient-to-r from-emerald-600/25 to-green-600/25 border-2 border-emerald-400/40 p-6 rounded-2xl backdrop-blur-sm hover:shadow-xl hover:shadow-emerald-500/20 transition-all duration-500 group"
              >
                <div class="flex items-center space-x-4 mb-6">
                  <div
                    class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-green-600 rounded-lg flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-emerald-300/50"
                  >
                    <i class="fas fa-gavel text-white text-lg"></i>
                  </div>
                  <h3 class="text-xl font-bold text-white flex items-center">
                    <i class="fas fa-check-double mr-2 text-emerald-300"></i>
                    ICT Officer Actions
                  </h3>
                </div>

                <!-- Comments Input -->
                <div class="mb-6">
                  <label class="block text-sm font-semibold text-emerald-100 mb-3">
                    <i class="fas fa-comment mr-2"></i>Add Comments (Optional)
                  </label>
                  <textarea
                    v-model="approvalComments"
                    rows="4"
                    class="w-full px-4 py-3 bg-white/15 border border-emerald-300/30 rounded-xl focus:border-emerald-400 focus:outline-none text-white placeholder-emerald-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 resize-y"
                    placeholder="Enter your comments or reasons for approval/rejection..."
                  ></textarea>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                  <button
                    @click="approveRequest"
                    :disabled="isProcessing"
                    class="px-8 py-4 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl hover:from-green-700 hover:to-emerald-700 transition-all duration-300 font-bold flex items-center justify-center shadow-lg hover:shadow-xl transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    <i class="fas fa-check mr-3"></i>
                    {{ isProcessing ? 'Processing...' : 'Approve Request' }}
                  </button>

                  <button
                    @click="rejectRequest"
                    :disabled="isProcessing"
                    class="px-8 py-4 bg-gradient-to-r from-red-600 to-pink-600 text-white rounded-xl hover:from-red-700 hover:to-pink-700 transition-all duration-300 font-bold flex items-center justify-center shadow-lg hover:shadow-xl transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    <i class="fas fa-times mr-3"></i>
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

    <!-- Success Modal -->
    <div
      v-if="showSuccessModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
    >
      <div
        class="bg-white rounded-xl shadow-2xl max-w-lg w-full transform transition-all duration-300 scale-100 animate-modal-in"
      >
        <div class="p-8 text-center">
          <div
            class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6"
          >
            <i class="fas fa-check text-green-600 text-3xl"></i>
          </div>
          <h3 class="text-2xl font-bold text-gray-800 mb-3">Action Completed Successfully!</h3>
          <p class="text-gray-600 mb-6">
            The device borrowing request has been processed successfully.
          </p>
          <button
            @click="closeSuccessModal"
            class="w-full bg-teal-600 text-white py-4 px-8 rounded-lg font-medium hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 transition duration-200 text-lg"
          >
            <i class="fas fa-arrow-left mr-3"></i>
            Return to Requests List
          </button>
        </div>
      </div>
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
    setup() {
      // Sidebar state now managed by Pinia - no local state needed

      return {
        // No local state needed for sidebar
      }
    },
    data() {
      return {
        request: {},
        approvalComments: '',
        isLoading: false,
        isProcessing: false,
        showSuccessModal: false
      }
    },
    computed: {
      canTakeAction() {
        // Check if request is in pending ICT approval status
        return this.request.ict_approve === 'pending' || this.request.ict_status === 'pending'
      }
    },
    async mounted() {
      await this.fetchRequestDetails()
    },
    methods: {
      async fetchRequestDetails() {
        this.isLoading = true
        try {
          const requestId = this.$route.params.id
          console.log('Fetching request details for ID:', requestId)

          // Validate request ID
          if (!requestId || requestId === ':id') {
            console.error('Invalid request ID:', requestId)
            throw new Error('Invalid request ID: ' + requestId)
          }

          // Fetch real data from device borrowing service
          const response = await deviceBorrowingService.getRequestDetails(requestId)

          if (response.success) {
            this.request = response.data
            console.log('✅ Request details loaded:', this.request)
          } else {
            console.error('❌ Failed to load request details:', {
              message: response.message,
              status: response.status,
              details: response.details,
              error: response.error
            })
            throw new Error(response.message || response.error || 'Failed to load request details')
          }
        } catch (error) {
          console.error('Error fetching request details:', error)
          alert('Failed to load request details: ' + (error.message || 'Unknown error'))
          // Redirect to requests list if data loading fails
          this.$router.push('/ict-approval/requests')
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
          const response = await deviceBorrowingService.approveRequest(
            requestId,
            this.approvalComments
          )

          if (response.success) {
            alert('Device borrowing request approved successfully!')

            // Update local request status
            this.request.ict_approve = 'approved'

            // Redirect after a short delay
            setTimeout(() => {
              this.$router.push('/ict-approval/requests')
            }, 1500)
          } else {
            throw new Error(response.message || 'Failed to approve request')
          }
        } catch (error) {
          console.error('Error approving request:', error)
          alert(
            'Error approving request: ' +
              (error.message || 'Failed to approve the request. Please try again.')
          )
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

            // Update local request status
            this.request.ict_approve = 'rejected'

            // Redirect after a short delay
            setTimeout(() => {
              this.$router.push('/ict-approval/requests')
            }, 1500)
          } else {
            throw new Error(response.message || 'Failed to reject request')
          }
        } catch (error) {
          console.error('Error rejecting request:', error)
          alert(
            'Error rejecting request: ' +
              (error.message || 'Failed to reject the request. Please try again.')
          )
        } finally {
          this.isProcessing = false
        }
      },

      closeSuccessModal() {
        this.showSuccessModal = false
        this.goBack()
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

      getStatusBadgeClass(status) {
        return deviceBorrowingService.getStatusBadgeClass(status)
      },

      getStatusIcon(status) {
        return deviceBorrowingService.getStatusIcon(status)
      },

      getStatusText(status) {
        return deviceBorrowingService.getStatusText(status)
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

  .booking-card {
    position: relative;
    overflow: hidden;
    background: rgba(59, 130, 246, 0.1);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
  }

  .booking-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(96, 165, 250, 0.2), transparent);
    transition: left 0.5s;
  }

  .booking-card:hover::before {
    left: 100%;
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

  @keyframes modal-in {
    from {
      opacity: 0;
      transform: scale(0.9) translateY(-20px);
    }
    to {
      opacity: 1;
      transform: scale(1) translateY(0);
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

  .animate-modal-in {
    animation: modal-in 0.3s ease-out;
  }

  /* Focus styles for accessibility */
  input:focus,
  select:focus,
  textarea:focus {
    box-shadow: 0 0 0 3px rgba(45, 212, 191, 0.1);
  }

  button:focus {
    box-shadow: 0 0 0 3px rgba(45, 212, 191, 0.3);
  }

  /* Loading spinner animation */
  @keyframes spin {
    from {
      transform: rotate(0deg);
    }
    to {
      transform: rotate(360deg);
    }
  }

  .fa-spin {
    animation: spin 1s linear infinite;
  }

  /* Smooth transitions */
  * {
    transition-property:
      color, background-color, border-color, text-decoration-color, fill, stroke, opacity,
      box-shadow, transform, filter, backdrop-filter;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 200ms;
  }
</style>
