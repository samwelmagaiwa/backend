<template>
  <div class="flex flex-col h-screen">
    <AppHeader />
    <div class="flex flex-1 overflow-hidden">
      <ModernSidebar v-model:collapsed="sidebarCollapsed" />
      <main class="flex-1 p-3 bg-gradient-to-br from-blue-900 via-blue-800 to-teal-900 overflow-y-auto relative">
        <!-- Your existing template content -->
        <div class="max-w-12xl mx-auto relative z-10">
          <!-- Header Section -->
          <div class="medical-glass-card rounded-t-3xl p-4 mb-0 border-b border-blue-300/30">
            <div class="flex justify-between items-center">
              <div class="text-center flex-1">
                <h1 class="text-xl font-bold text-white mb-2 tracking-wide drop-shadow-lg">
                  MUHIMBILI NATIONAL HOSPITAL
                </h1>
                <h2 class="text-base font-bold text-blue-100 tracking-wide drop-shadow-md">
                  COMBINED ACCESS REQUEST (FIXED)
                </h2>
              </div>
            </div>
          </div>

          <!-- Main Form -->
          <div class="medical-glass-card rounded-b-3xl overflow-hidden">
            <form @submit.prevent="submitForm" class="p-4 space-y-4">

              <!-- Applicant Details Section -->
              <div class="medical-card bg-gradient-to-r from-blue-600/25 to-blue-700/25 border-2 border-blue-400/40 p-5 rounded-xl">
                <h3 class="text-lg font-bold text-white mb-4">
                  <i class="fas fa-id-card mr-2 text-blue-300"></i>
                  Applicant Details
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                  <!-- PF Number -->
                  <div>
                    <label class="block text-sm font-bold text-blue-100 mb-2">
                      PF Number <span class="text-red-400">*</span>
                    </label>
                    <input
                      v-model="formData.pfNumber"
                      type="text"
                      class="w-full px-3 py-2 bg-white/15 border-2 border-blue-300/30 rounded-lg focus:border-blue-400 focus:outline-none text-white placeholder-blue-200/60"
                      placeholder="Enter PF Number"
                      required
                    />
                  </div>

                  <!-- Staff Name -->
                  <div>
                    <label class="block text-sm font-bold text-blue-100 mb-2">
                      Staff Name <span class="text-red-400">*</span>
                    </label>
                    <input
                      v-model="formData.staffName"
                      type="text"
                      class="w-full px-3 py-2 bg-white/15 border-2 border-blue-300/30 rounded-lg focus:border-blue-400 focus:outline-none text-white placeholder-blue-200/60"
                      placeholder="Enter full name"
                      required
                    />
                  </div>

                  <!-- Phone Number -->
                  <div>
                    <label class="block text-sm font-bold text-blue-100 mb-2">
                      Phone Number <span class="text-red-400">*</span>
                    </label>
                    <input
                      v-model="formData.phoneNumber"
                      type="tel"
                      class="w-full px-3 py-2 bg-white/15 border-2 border-blue-300/30 rounded-lg focus:border-blue-400 focus:outline-none text-white placeholder-blue-200/60"
                      placeholder="Enter phone number"
                      required
                    />
                  </div>

                  <!-- Department -->
                  <div>
                    <label class="block text-sm font-bold text-blue-100 mb-2">
                      Department <span class="text-red-400">*</span>
                    </label>
                    <select
                      v-model="formData.department"
                      class="w-full px-3 py-2 bg-white/15 border-2 border-blue-300/30 rounded-lg focus:border-blue-400 focus:outline-none text-white appearance-none cursor-pointer"
                      required
                      :disabled="departmentsLoading"
                    >
                      <option value="" class="bg-blue-800 text-blue-300">
                        {{ departmentsLoading ? 'Loading departments...' : 'Select Department' }}
                      </option>
                      <option
                        v-for="dept in departments"
                        :key="dept.id"
                        :value="dept.id"
                        class="bg-blue-800 text-white"
                      >
                        {{ dept.name }}
                      </option>
                    </select>

                    <!-- Error message for departments -->
                    <div v-if="departmentsError" class="text-red-400 text-xs mt-1">
                      {{ departmentsError }}
                      <button
                        @click="refreshDepartments"
                        class="ml-2 text-blue-300 hover:text-blue-200 underline"
                        type="button"
                      >
                        Retry
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Digital Signature -->
                <div class="mt-4">
                  <label class="block text-sm font-bold text-blue-100 mb-2">
                    Digital Signature <span class="text-red-400">*</span>
                  </label>
                  <div class="flex gap-3 items-start">
                    <div class="relative w-64 h-14 border-2 border-blue-300/30 rounded-lg bg-blue-100/20 overflow-hidden">
                      <div v-if="signaturePreview" class="w-full h-full flex items-center justify-center p-1">
                        <img :src="signaturePreview" alt="Signature" class="max-w-full max-h-full object-contain" />
                      </div>
                      <div v-else class="w-full h-full flex items-center justify-center">
                        <p class="text-xs text-blue-400 italic">Signature here</p>
                      </div>
                      <button
                        v-if="signaturePreview"
                        type="button"
                        @click="clearSignature"
                        class="absolute top-1 right-1 w-5 h-5 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600 text-xs"
                      >
                        ×
                      </button>
                    </div>
                    <div>
                      <input
                        ref="signatureInput"
                        type="file"
                        accept=".png,.jpg,.jpeg"
                        @change="onSignatureChange"
                        class="hidden"
                      />
                      <button
                        type="button"
                        @click="triggerFileUpload"
                        class="px-3 py-2 bg-blue-500 text-white text-sm rounded-lg hover:bg-blue-600"
                      >
                        Upload Signature
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Service Selection -->
              <div class="medical-card bg-gradient-to-r from-blue-600/25 to-blue-700/25 border-2 border-blue-400/40 p-5 rounded-xl">
                <h3 class="text-lg font-bold text-white mb-4">
                  <i class="fas fa-cogs mr-2 text-blue-300"></i>
                  Select Services
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <!-- Jeeva Service -->
                  <label class="block p-4 border-2 rounded-xl cursor-pointer transition-all duration-300"
                         :class="formData.services.jeeva ? 'border-blue-400 bg-blue-500/20' : 'border-white/30 bg-white/10'">
                    <input v-model="formData.services.jeeva" type="checkbox" class="sr-only" />
                    <div class="flex items-center justify-between">
                      <div class="flex items-center">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center mr-3"
                             :class="formData.services.jeeva ? 'bg-blue-500 text-white' : 'bg-white/20 text-blue-300'">
                          <i class="fas fa-file-medical"></i>
                        </div>
                        <div>
                          <h4 class="font-bold text-white">Jeeva Access</h4>
                          <p class="text-xs text-blue-200/80">Medical records</p>
                        </div>
                      </div>
                      <div class="w-5 h-5 rounded border-2 flex items-center justify-center"
                           :class="formData.services.jeeva ? 'border-blue-400 bg-blue-500' : 'border-white/40'">
                        <i v-if="formData.services.jeeva" class="fas fa-check text-white text-sm"></i>
                      </div>
                    </div>
                  </label>

                  <!-- Wellsoft Service -->
                  <label class="block p-4 border-2 rounded-xl cursor-pointer transition-all duration-300"
                         :class="formData.services.wellsoft ? 'border-blue-400 bg-blue-500/20' : 'border-white/30 bg-white/10'">
                    <input v-model="formData.services.wellsoft" type="checkbox" class="sr-only" />
                    <div class="flex items-center justify-between">
                      <div class="flex items-center">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center mr-3"
                             :class="formData.services.wellsoft ? 'bg-blue-500 text-white' : 'bg-white/20 text-blue-300'">
                          <i class="fas fa-laptop-medical"></i>
                        </div>
                        <div>
                          <h4 class="font-bold text-white">Wellsoft Access</h4>
                          <p class="text-xs text-blue-200/80">Hospital management</p>
                        </div>
                      </div>
                      <div class="w-5 h-5 rounded border-2 flex items-center justify-center"
                           :class="formData.services.wellsoft ? 'border-blue-400 bg-blue-500' : 'border-white/40'">
                        <i v-if="formData.services.wellsoft" class="fas fa-check text-white text-sm"></i>
                      </div>
                    </div>
                  </label>

                  <!-- Internet Service -->
                  <label class="block p-4 border-2 rounded-xl cursor-pointer transition-all duration-300"
                         :class="formData.services.internet ? 'border-blue-400 bg-blue-500/20' : 'border-white/30 bg-white/10'">
                    <input v-model="formData.services.internet" type="checkbox" class="sr-only" />
                    <div class="flex items-center justify-between">
                      <div class="flex items-center">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center mr-3"
                             :class="formData.services.internet ? 'bg-blue-500 text-white' : 'bg-white/20 text-blue-300'">
                          <i class="fas fa-wifi"></i>
                        </div>
                        <div>
                          <h4 class="font-bold text-white">Internet Access</h4>
                          <p class="text-xs text-blue-200/80">Internet connectivity</p>
                        </div>
                      </div>
                      <div class="w-5 h-5 rounded border-2 flex items-center justify-center"
                           :class="formData.services.internet ? 'border-blue-400 bg-blue-500' : 'border-white/40'">
                        <i v-if="formData.services.internet" class="fas fa-check text-white text-sm"></i>
                      </div>
                    </div>
                  </label>
                </div>

                <!-- Service Selection Validation -->
                <div v-if="!hasSelectedService" class="mt-4 p-3 bg-red-500/20 border border-red-400/40 rounded-lg">
                  <p class="text-red-300 text-sm">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Please select at least one service to continue.
                  </p>
                </div>
              </div>

              <!-- Internet Purpose Section -->
              <div v-if="formData.services.internet" class="medical-card bg-gradient-to-r from-blue-600/25 to-blue-700/25 border-2 border-blue-400/40 p-5 rounded-xl">
                <h3 class="text-lg font-bold text-white mb-4">
                  <i class="fas fa-globe mr-2 text-blue-300"></i>
                  Internet Purpose
                </h3>
                <div class="space-y-3">
                  <div v-for="(purpose, index) in formData.internetPurposes" :key="index" class="flex items-center gap-2">
                    <div class="w-6 h-6 bg-blue-500/20 text-blue-400 rounded-full font-bold text-xs flex items-center justify-center">
                      {{ index + 1 }}.
                    </div>
                    <input
                      v-model="formData.internetPurposes[index]"
                      type="text"
                      class="flex-1 px-3 py-2 bg-white/15 border-2 border-blue-300/30 rounded-lg focus:border-blue-400 focus:outline-none text-white placeholder-blue-200/60"
                      :placeholder="`Purpose ${index + 1}`"
                      :required="index === 0 && formData.services.internet"
                    />
                  </div>
                </div>
              </div>

              <!-- Form Actions -->
              <div class="flex justify-between items-center pt-4">
                <button
                  type="button"
                  @click="goBack"
                  class="px-5 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-all duration-300 font-semibold flex items-center"
                >
                  <i class="fas fa-arrow-left mr-2"></i>
                  Back to Dashboard
                </button>
                <button
                  type="submit"
                  :disabled="isSubmitting || !hasSelectedService"
                  class="px-5 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-300 font-semibold flex items-center disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <i v-if="isSubmitting" class="fas fa-spinner fa-spin mr-2"></i>
                  <i v-else class="fas fa-paper-plane mr-2"></i>
                  {{ isSubmitting ? "Submitting..." : "Submit Request" }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </main>
    </div>

    <!-- Success Modal -->
    <div v-if="showSuccessModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl shadow-2xl max-w-md w-full">
        <div class="p-6 text-center">
          <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-check text-green-600 text-2xl"></i>
          </div>
          <h3 class="text-xl font-bold text-gray-800 mb-2">Request Submitted!</h3>
          <p class="text-gray-600 mb-4">Your Combined Access Request has been submitted successfully.</p>
          <button
            @click="closeSuccessModal"
            class="w-full bg-green-600 text-white py-3 px-6 rounded-lg font-medium hover:bg-green-700"
          >
            <i class="fas fa-home mr-2"></i>
            Return to Dashboard
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, onUnmounted } from 'vue'
import ModernSidebar from '@/components/ModernSidebar.vue'
import AppHeader from '@/components/AppHeader.vue'
import { useDepartments } from '@/composables/useApiWithGuards'
import enhancedUserCombinedAccessService from '@/services/enhancedUserCombinedAccessService'

export default {
  name: 'UserCombinedAccessFormFixed',
  components: {
    ModernSidebar,
    AppHeader
  },
  setup() {
    const sidebarCollapsed = ref(false)

    // Use the improved departments composable with deduplication
    const {
      departments,
      loading: departmentsLoading,
      error: departmentsError,
      fetchDepartments,
      refreshDepartments
    } = useDepartments()

    // Load departments only once on mount
    onMounted(async() => {
      console.log('🔄 UserCombinedAccessFormFixed mounted, loading departments...')
      await fetchDepartments()
    })

    // Cleanup on unmount
    onUnmounted(() => {
      console.log('🔄 UserCombinedAccessFormFixed unmounted')
    })

    return {
      sidebarCollapsed,
      departments,
      departmentsLoading,
      departmentsError,
      fetchDepartments,
      refreshDepartments
    }
  },

  data() {
    return {
      isSubmitting: false,
      showSuccessModal: false,
      signaturePreview: '',
      formData: {
        pfNumber: '',
        staffName: '',
        department: '',
        phoneNumber: '',
        signature: null,
        services: {
          jeeva: false,
          wellsoft: false,
          internet: false
        },
        internetPurposes: ['', '', '', '']
      }
    }
  },

  computed: {
    hasSelectedService() {
      return Object.values(this.formData.services).some(Boolean)
    }
  },

  methods: {
    async submitForm() {
      // Prevent double submission with guard
      if (this.isSubmitting) {
        console.log('⚠️ Form already submitting, ignoring duplicate call')
        return
      }

      // Validation
      if (!this.validateForm()) {
        return
      }

      this.isSubmitting = true

      try {
        const formData = new FormData()

        // Add basic fields
        formData.append('pf_number', this.formData.pfNumber)
        formData.append('staff_name', this.formData.staffName)
        formData.append('phone_number', this.formData.phoneNumber)
        formData.append('department_id', this.formData.department)
        formData.append('signature', this.formData.signature)

        // Prepare request types
        const requestTypes = []
        if (this.formData.services.jeeva) requestTypes.push('jeeva_access')
        if (this.formData.services.wellsoft) requestTypes.push('wellsoft')
        if (this.formData.services.internet) requestTypes.push('internet_access_request')

        requestTypes.forEach((type, index) => {
          formData.append(`request_type[${index}]`, type)
        })

        // Add internet purposes if needed
        if (this.formData.services.internet) {
          const purposes = this.formData.internetPurposes.filter(p => p.trim() !== '')
          purposes.forEach((purpose, index) => {
            formData.append(`internetPurposes[${index}]`, purpose)
          })
        }

        console.log('📤 Submitting combined access request...')

        // Submit using enhanced service (no deduplication for submissions)
        const response = await enhancedUserCombinedAccessService.submitCombinedRequest(formData)

        if (response.success) {
          console.log('✅ Combined access request submitted successfully')
          this.showSuccessModal = true
          this.resetForm()
          this.showNotification('Request submitted successfully!', 'success')
        } else {
          console.error('❌ API Error:', response)
          this.handleError(response)
        }
      } catch (error) {
        console.error('❌ Error submitting form:', error)
        this.showNotification('Network error. Please try again.', 'error')
      } finally {
        this.isSubmitting = false
      }
    },

    validateForm() {
      if (!this.formData.pfNumber || !this.formData.staffName || !this.formData.department || !this.formData.phoneNumber) {
        this.showNotification('Please fill in all required fields', 'error')
        return false
      }

      if (!this.formData.signature) {
        this.showNotification('Please upload your signature', 'error')
        return false
      }

      if (!this.hasSelectedService) {
        this.showNotification('Please select at least one service', 'error')
        return false
      }

      if (this.formData.services.internet && !this.formData.internetPurposes[0]) {
        this.showNotification('Please provide at least one internet purpose', 'error')
        return false
      }

      return true
    },

    handleError(response) {
      if (response.errors && Object.keys(response.errors).length > 0) {
        const errorMessages = Object.values(response.errors).flat()
        this.showNotification(errorMessages[0] || 'Validation failed', 'error')
      } else {
        this.showNotification(response.message || 'Failed to submit request', 'error')
      }
    },

    resetForm() {
      this.formData = {
        pfNumber: '',
        staffName: '',
        department: '',
        phoneNumber: '',
        signature: null,
        services: {
          jeeva: false,
          wellsoft: false,
          internet: false
        },
        internetPurposes: ['', '', '', '']
      }
      this.clearSignature()
    },

    triggerFileUpload() {
      this.$refs.signatureInput.click()
    },

    onSignatureChange(e) {
      const file = e.target.files[0]
      if (!file) {
        this.clearSignature()
        return
      }

      // Validate file
      const allowedTypes = ['image/png', 'image/jpeg', 'image/jpg']
      if (!allowedTypes.includes(file.type)) {
        this.showNotification('Please select a valid image file (PNG, JPG)', 'error')
        this.clearSignature()
        return
      }

      if (file.size > 5 * 1024 * 1024) {
        this.showNotification('File size must be less than 5MB', 'error')
        this.clearSignature()
        return
      }

      this.formData.signature = file

      // Create preview
      const reader = new FileReader()
      reader.onload = () => {
        this.signaturePreview = reader.result
      }
      reader.readAsDataURL(file)
    },

    clearSignature() {
      this.formData.signature = null
      this.signaturePreview = ''
      if (this.$refs.signatureInput) {
        this.$refs.signatureInput.value = ''
      }
    },

    goBack() {
      this.$router.push('/user-dashboard')
    },

    closeSuccessModal() {
      this.showSuccessModal = false
      this.$router.push('/user-dashboard')
    },

    showNotification(message, type = 'info') {
      // Simple notification implementation
      const colors = {
        success: 'green',
        error: 'red',
        info: 'blue'
      }

      const toast = document.createElement('div')
      toast.className = `fixed top-4 right-4 px-6 py-3 rounded-lg text-white font-semibold z-50 bg-${colors[type]}-600`
      toast.textContent = message
      document.body.appendChild(toast)

      setTimeout(() => {
        if (document.body.contains(toast)) {
          document.body.removeChild(toast)
        }
      }, 3000)
    }
  }
}
</script>

<style scoped>
.medical-glass-card {
  background: rgba(59, 130, 246, 0.15);
  backdrop-filter: blur(25px);
  border: 2px solid rgba(96, 165, 250, 0.3);
  box-shadow: 0 8px 32px rgba(29, 78, 216, 0.4);
}

.medical-card {
  background: rgba(59, 130, 246, 0.1);
  backdrop-filter: blur(15px);
}

.fa-spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}
</style>
