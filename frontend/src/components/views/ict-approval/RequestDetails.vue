<template>
  <div class="flex flex-col h-screen">
    <Header />
    <div class="flex flex-1 overflow-hidden">
      <DynamicSidebar v-model:collapsed="sidebarCollapsed" />
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
                fontSize: Math.random() * 15 + 8 + 'px',
              }"
            >
              <i
                :class="[
                  'fas',
                  [
                    'fa-laptop',
                    'fa-tv',
                    'fa-desktop',
                    'fa-keyboard',
                    'fa-mouse',
                    'fa-headphones',
                  ][Math.floor(Math.random() * 6)],
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
          <div
            class="booking-glass-card rounded-b-3xl overflow-hidden animate-slide-up"
          >
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
                    :class="getStatusBadgeClass(request.status)"
                    class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium"
                  >
                    <i :class="getStatusIcon(request.status)" class="mr-2"></i>
                    {{ getStatusText(request.status) }}
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

                <div
                  class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
                >
                  <!-- Borrower Name -->
                  <div class="group">
                    <label
                      class="block text-sm font-semibold text-blue-100 mb-2 flex items-center"
                    >
                      <i class="fas fa-user mr-2 text-teal-300 text-sm"></i>
                      Borrower Name
                    </label>
                    <div
                      class="px-4 py-3 bg-white/10 border border-blue-300/30 rounded-lg text-white text-sm backdrop-blur-sm"
                    >
                      {{ request.borrowerName }}
                    </div>
                  </div>

                  <!-- Department -->
                  <div class="group">
                    <label
                      class="block text-sm font-semibold text-blue-100 mb-2 flex items-center"
                    >
                      <i class="fas fa-building mr-2 text-teal-300 text-sm"></i>
                      Department
                    </label>
                    <div
                      class="px-4 py-3 bg-white/10 border border-blue-300/30 rounded-lg text-white text-sm backdrop-blur-sm"
                    >
                      {{ request.department }}
                    </div>
                  </div>

                  <!-- Phone Number -->
                  <div class="group">
                    <label
                      class="block text-sm font-semibold text-blue-100 mb-2 flex items-center"
                    >
                      <i class="fas fa-phone mr-2 text-teal-300 text-sm"></i>
                      Phone Number
                    </label>
                    <div
                      class="px-4 py-3 bg-white/10 border border-blue-300/30 rounded-lg text-white text-sm backdrop-blur-sm"
                    >
                      {{ request.phoneNumber }}
                    </div>
                  </div>

                  <!-- Device Type -->
                  <div class="group">
                    <label
                      class="block text-sm font-semibold text-blue-100 mb-2 flex items-center"
                    >
                      <i class="fas fa-laptop mr-2 text-teal-300 text-sm"></i>
                      Device Type
                    </label>
                    <div
                      class="px-4 py-3 bg-white/10 border border-blue-300/30 rounded-lg text-white text-sm backdrop-blur-sm"
                    >
                      {{
                        getDeviceDisplayName(
                          request.deviceType,
                          request.customDevice
                        )
                      }}
                    </div>
                  </div>

                  <!-- Booking Date -->
                  <div class="group">
                    <label
                      class="block text-sm font-semibold text-blue-100 mb-2 flex items-center"
                    >
                      <i class="fas fa-calendar mr-2 text-teal-300 text-sm"></i>
                      Booking Date
                    </label>
                    <div
                      class="px-4 py-3 bg-white/10 border border-blue-300/30 rounded-lg text-white text-sm backdrop-blur-sm"
                    >
                      {{ formatDate(request.bookingDate) }}
                    </div>
                  </div>

                  <!-- Return Date & Time -->
                  <div class="group">
                    <label
                      class="block text-sm font-semibold text-blue-100 mb-2 flex items-center"
                    >
                      <i
                        class="fas fa-calendar-minus mr-2 text-teal-300 text-sm"
                      ></i>
                      Return Date & Time
                    </label>
                    <div
                      class="px-4 py-3 bg-white/10 border border-blue-300/30 rounded-lg text-white text-sm backdrop-blur-sm"
                    >
                      {{ formatDate(request.collectionDate) }} at
                      {{ request.returnTime }}
                    </div>
                  </div>

                  <!-- Signature Status -->
                  <div class="group">
                    <label
                      class="block text-sm font-semibold text-blue-100 mb-2 flex items-center justify-center"
                    >
                      <i
                        class="fas fa-signature mr-2 text-teal-300 text-sm"
                      ></i>
                      Signature Status
                    </label>
                    <div
                      class="px-4 py-3 bg-white/10 border border-blue-300/30 rounded-lg text-white text-sm backdrop-blur-sm"
                    >
                      <div
                        v-if="request.signature"
                        class="flex items-center justify-center text-green-400"
                      >
                        <i class="fas fa-check-circle mr-2"></i>
                        <span class="font-medium">Digitally Signed</span>
                      </div>
                      <div
                        v-else
                        class="flex items-center justify-center text-center"
                      >
                        <div class="text-center">
                          <div class="mb-2">
                            <i
                              class="fas fa-signature text-teal-300 text-2xl mb-2"
                            ></i>
                            <p class="text-blue-100 text-sm font-medium">
                              No signature uploaded
                            </p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Reason for Borrowing -->
                <div class="mt-6">
                  <label
                    class="block text-sm font-semibold text-blue-100 mb-2 flex items-center"
                  >
                    <i
                      class="fas fa-comment-alt mr-2 text-teal-300 text-sm"
                    ></i>
                    Reason for Borrowing
                  </label>
                  <div
                    class="px-4 py-3 bg-white/10 border border-blue-300/30 rounded-lg text-white text-sm backdrop-blur-sm"
                  >
                    {{ request.reason }}
                  </div>
                </div>

                <!-- Digital Signature -->
                <div v-if="request.signature" class="mt-6">
                  <label
                    class="block text-sm font-semibold text-blue-100 mb-2 flex items-center"
                  >
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
                          :src="request.signature"
                          alt="Digital Signature"
                          class="max-h-16 max-w-48 object-contain"
                        />
                      </div>
                      <div class="flex items-center text-green-400">
                        <i class="fas fa-check-circle mr-2"></i>
                        <div class="text-center">
                          <span class="text-sm font-semibold block"
                            >Verified</span
                          >
                          <span class="text-xs text-green-300"
                            >Signature captured</span
                          >
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- ICT Officer Assessment Form -->
              <div
                class="booking-card bg-gradient-to-r from-blue-600/25 to-teal-600/25 border-2 border-blue-400/40 p-6 rounded-2xl backdrop-blur-sm hover:shadow-xl hover:shadow-blue-500/20 transition-all duration-500 group"
              >
                <div class="flex items-center space-x-4 mb-6">
                  <div
                    class="w-12 h-12 bg-gradient-to-br from-blue-500 to-teal-600 rounded-lg flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-blue-300/50"
                  >
                    <i class="fas fa-clipboard-check text-white text-lg"></i>
                  </div>
                  <h3 class="text-xl font-bold text-white flex items-center">
                    <i class="fas fa-tools mr-2 text-blue-300"></i>
                    ICT Officer Assessment
                  </h3>
                </div>

                <form @submit.prevent="saveAssessment" class="space-y-6">
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Device Condition Assessment -->
                    <div class="group">
                      <label
                        class="block text-sm font-semibold text-indigo-100 mb-2 flex items-center"
                      >
                        <i
                          class="fas fa-search mr-2 text-indigo-300 text-sm"
                        ></i>
                        Device Condition Assessment
                        <span class="text-red-400 ml-1">*</span>
                      </label>
                      <div class="relative">
                        <select
                          v-model="assessment.deviceCondition"
                          class="w-full px-3 py-3 border border-indigo-300/30 rounded-lg focus:border-indigo-400 focus:outline-none text-white bg-indigo-100/20 focus:bg-indigo-100/30 transition-all backdrop-blur-sm group-hover:border-indigo-400/50 appearance-none cursor-pointer text-sm"
                          required
                        >
                          <option
                            value=""
                            class="bg-indigo-800 text-indigo-300"
                          >
                            Select Condition
                          </option>
                          <option value="good" class="bg-indigo-800 text-white">
                            Good
                          </option>
                          <option
                            value="minor_issues"
                            class="bg-indigo-800 text-white"
                          >
                            Minor Issues
                          </option>
                          <option
                            value="needs_repair"
                            class="bg-indigo-800 text-white"
                          >
                            Needs Repair
                          </option>
                        </select>
                        <div
                          class="absolute right-3 top-1/2 transform -translate-y-1/2 text-indigo-300/50 pointer-events-none"
                        >
                          <i class="fas fa-chevron-down text-sm"></i>
                        </div>
                      </div>
                      <div
                        v-if="errors.deviceCondition"
                        class="text-red-400 text-xs mt-1 flex items-center"
                      >
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        {{ errors.deviceCondition }}
                      </div>
                    </div>

                    <!-- Status -->
                    <div class="group">
                      <label
                        class="block text-sm font-semibold text-indigo-100 mb-2 flex items-center"
                      >
                        <i class="fas fa-flag mr-2 text-indigo-300 text-sm"></i>
                        Status <span class="text-red-400 ml-1">*</span>
                      </label>
                      <div class="relative">
                        <select
                          v-model="assessment.status"
                          class="w-full px-3 py-3 border border-indigo-300/30 rounded-lg focus:border-indigo-400 focus:outline-none text-white bg-indigo-100/20 focus:bg-indigo-100/30 transition-all backdrop-blur-sm group-hover:border-indigo-400/50 appearance-none cursor-pointer text-sm"
                          required
                        >
                          <option
                            value=""
                            class="bg-indigo-800 text-indigo-300"
                          >
                            Select Status
                          </option>
                          <option
                            value="pending"
                            class="bg-indigo-800 text-white"
                          >
                            Pending
                          </option>
                          <option
                            value="returned"
                            class="bg-indigo-800 text-white"
                          >
                            Returned
                          </option>
                          <option
                            value="compromised"
                            class="bg-indigo-800 text-white"
                          >
                            Compromised
                          </option>
                        </select>
                        <div
                          class="absolute right-3 top-1/2 transform -translate-y-1/2 text-indigo-300/50 pointer-events-none"
                        >
                          <i class="fas fa-chevron-down text-sm"></i>
                        </div>
                      </div>
                      <div
                        v-if="errors.status"
                        class="text-red-400 text-xs mt-1 flex items-center"
                      >
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        {{ errors.status }}
                      </div>
                    </div>
                  </div>

                  <!-- Condition Before Issues -->
                  <div class="group">
                    <label
                      class="block text-sm font-semibold text-indigo-100 mb-2 flex items-center"
                    >
                      <i
                        class="fas fa-comment-dots mr-2 text-indigo-300 text-sm"
                      ></i>
                      Condition Before Issues / Notes
                    </label>
                    <div class="relative">
                      <textarea
                        v-model="assessment.conditionNotes"
                        class="w-full px-3 py-3 bg-white/15 border border-indigo-300/30 rounded-lg focus:border-indigo-400 focus:outline-none text-white placeholder-indigo-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-indigo-500/20 group-hover:border-indigo-400/50 resize-none text-sm"
                        rows="4"
                        placeholder="Describe any issues noticed with the device or additional notes..."
                      ></textarea>
                      <div
                        class="absolute inset-0 rounded-lg bg-gradient-to-r from-indigo-500/10 to-blue-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"
                      ></div>
                    </div>
                  </div>

                  <!-- Assessment Date -->
                  <div class="group">
                    <label
                      class="block text-sm font-semibold text-indigo-100 mb-2 flex items-center"
                    >
                      <i
                        class="fas fa-calendar-check mr-2 text-indigo-300 text-sm"
                      ></i>
                      Assessment Date
                    </label>
                    <div class="relative">
                      <input
                        v-model="assessment.assessmentDate"
                        type="date"
                        class="w-full px-3 py-3 bg-white/15 border border-indigo-300/30 rounded-lg focus:border-indigo-400 focus:outline-none text-white backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-indigo-500/20 group-hover:border-indigo-400/50 text-sm"
                        required
                      />
                      <div
                        class="absolute inset-0 rounded-lg bg-gradient-to-r from-indigo-500/10 to-blue-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"
                      ></div>
                    </div>
                  </div>

                  <!-- Action Buttons -->
                  <div
                    class="flex justify-between items-center pt-6 border-t border-blue-300/20"
                  >
                    <button
                      type="button"
                      @click="goBack"
                      class="px-6 py-3 bg-gray-600/80 text-white rounded-lg hover:bg-gray-700/80 transition-all duration-300 font-semibold flex items-center shadow-lg hover:shadow-xl transform hover:scale-105 backdrop-blur-sm border border-gray-500/50 text-sm"
                    >
                      <i class="fas fa-times mr-2"></i>
                      Cancel
                    </button>

                    <button
                      type="submit"
                      :disabled="isSubmitting"
                      class="px-6 py-3 bg-gradient-to-r from-teal-600 to-blue-600 text-white rounded-lg hover:from-teal-700 hover:to-blue-700 transition-all duration-300 font-semibold flex items-center shadow-lg hover:shadow-xl transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none border border-teal-400/50 text-sm"
                    >
                      <i v-if="!isSubmitting" class="fas fa-save mr-2"></i>
                      <i v-else class="fas fa-spinner fa-spin mr-2"></i>
                      {{
                        isSubmitting
                          ? "Saving Assessment..."
                          : "Save Assessment"
                      }}
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </main>
      <!-- Footer -->
      <AppFooter />
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
          <h3 class="text-2xl font-bold text-gray-800 mb-3">
            Assessment Saved Successfully!
          </h3>
          <p class="text-gray-600 mb-6">
            The device request assessment has been updated and saved to the
            system.
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
import { ref } from 'vue'
import Header from '@/components/header.vue'
import DynamicSidebar from '@/components/DynamicSidebar.vue'
import AppFooter from '@/components/footer.vue'
import axios from 'axios'

export default {
  name: 'RequestDetails',
  components: {
    Header,
    DynamicSidebar,
    AppFooter
  },
  setup() {
    const sidebarCollapsed = ref(false)

    return {
      sidebarCollapsed
    }
  },
  data() {
    return {
      request: {},
      assessment: {
        deviceCondition: '',
        status: '',
        conditionNotes: '',
        assessmentDate: new Date().toISOString().split('T')[0]
      },
      errors: {},
      isLoading: false,
      isSubmitting: false,
      showSuccessModal: false
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
        // Replace with your actual API endpoint
        const response = await axios.get(`/api/device-requests/${requestId}`)
        this.request = response.data

        // Pre-fill assessment if it exists
        if (this.request.assessment) {
          this.assessment = { ...this.request.assessment }
        }
      } catch (error) {
        console.error('Error fetching request details:', error)
        // Mock data for development
        const requestId = parseInt(this.$route.params.id)
        const mockRequests = [
          {
            id: 1,
            borrowerName: 'John Doe',
            department: 'ICT Department',
            phoneNumber: '0712345678',
            deviceType: 'projector',
            customDevice: '',
            bookingDate: '2024-01-15',
            collectionDate: '2024-01-20',
            returnTime: '14:00',
            reason: 'Presentation for board meeting',
            status: 'pending',
            signature: null
          },
          {
            id: 2,
            borrowerName: 'Jane Smith',
            department: 'Finance',
            phoneNumber: '0723456789',
            deviceType: 'laptop',
            customDevice: '',
            bookingDate: '2024-01-14',
            collectionDate: '2024-01-18',
            returnTime: '16:00',
            reason: 'Financial analysis work',
            status: 'returned',
            signature: null
          },
          {
            id: 3,
            borrowerName: 'Mike Johnson',
            department: 'Human Resources',
            phoneNumber: '0734567890',
            deviceType: 'others',
            customDevice: 'Wireless Microphone',
            bookingDate: '2024-01-13',
            collectionDate: '2024-01-17',
            returnTime: '12:00',
            reason: 'Staff training session',
            status: 'compromised',
            signature: null
          }
        ]

        this.request = mockRequests.find((r) => r.id === requestId) || {}

        // Pre-fill assessment with current status
        this.assessment.status = this.request.status || ''
      } finally {
        this.isLoading = false
      }
    },

    async saveAssessment() {
      if (!this.validateAssessment()) {
        return
      }

      this.isSubmitting = true
      try {
        const requestId = this.$route.params.id
        const assessmentData = {
          ...this.assessment,
          requestId: requestId,
          assessedBy: 'ICT Officer', // You can get this from user session
          assessedAt: new Date().toISOString()
        }

        // Replace with your actual API endpoint
        await axios.put(
          `/api/device-requests/${requestId}/assessment`,
          assessmentData
        )

        console.log('Assessment saved:', assessmentData)
        this.showSuccessModal = true
      } catch (error) {
        console.error('Error saving assessment:', error)
        alert('Error saving assessment. Please try again.')
      } finally {
        this.isSubmitting = false
      }
    },

    validateAssessment() {
      this.errors = {}

      if (!this.assessment.deviceCondition) {
        this.errors.deviceCondition = 'Device condition assessment is required'
      }

      if (!this.assessment.status) {
        this.errors.status = 'Status is required'
      }

      return Object.keys(this.errors).length === 0
    },

    closeSuccessModal() {
      this.showSuccessModal = false
      this.goBack()
    },

    goBack() {
      this.$router.push('/ict-approval/requests')
    },

    getDeviceDisplayName(deviceType, customDevice) {
      if (deviceType === 'others') {
        return customDevice || 'Other Device'
      }

      const deviceNames = {
        projector: 'Projector',
        tv_remote: 'TV Remote',
        hdmi_cable: 'HDMI Cable',
        monitor: 'Monitor',
        cpu: 'CPU',
        keyboard: 'Keyboard',
        pc: 'PC',
        laptop: 'Laptop'
      }

      return deviceNames[deviceType] || deviceType
    },

    formatDate(dateString) {
      if (!dateString) return ''
      const date = new Date(dateString)
      return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      })
    },

    getStatusBadgeClass(status) {
      const classes = {
        pending: 'bg-yellow-100 text-yellow-800 border border-yellow-200',
        returned: 'bg-green-100 text-green-800 border border-green-200',
        compromised: 'bg-red-100 text-red-800 border border-red-200'
      }
      return (
        classes[status] || 'bg-gray-100 text-gray-800 border border-gray-200'
      )
    },

    getStatusIcon(status) {
      const icons = {
        pending: 'fas fa-clock',
        returned: 'fas fa-check-circle',
        compromised: 'fas fa-exclamation-triangle'
      }
      return icons[status] || 'fas fa-question-circle'
    },

    getStatusText(status) {
      const texts = {
        pending: 'Pending',
        returned: 'Returned',
        compromised: 'Compromised'
      }
      return texts[status] || 'Unknown'
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
  box-shadow: 0 8px 32px rgba(29, 78, 216, 0.4),
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
  content: "";
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(
    90deg,
    transparent,
    rgba(96, 165, 250, 0.2),
    transparent
  );
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
  transition-property: color, background-color, border-color,
    text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter,
    backdrop-filter;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 200ms;
}
</style>
