<template>
  <div
    class="min-h-screen bg-gradient-to-br from-blue-900 via-blue-800 to-teal-900 py-8 px-4 relative overflow-hidden"
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
              ['fa-heartbeat', 'fa-user-md', 'fa-hospital', 'fa-stethoscope', 'fa-plus'][
                Math.floor(Math.random() * 5)
              ]
            ]"
          ></i>
        </div>
      </div>
    </div>

    <div class="max-w-8xl mx-auto relative z-10">
      <!-- Header Section -->
      <div class="medical-glass-card rounded-t-3xl p-6 mb-0 border-b border-blue-300/30">
        <div class="flex justify-between items-center">
          <!-- Left Logo -->
          <div class="w-28 h-28 mr-6 transform hover:scale-110 transition-transform duration-300">
            <div
              class="w-full h-full bg-gradient-to-br from-blue-500/20 to-teal-500/20 rounded-2xl backdrop-blur-sm border-2 border-blue-300/40 flex items-center justify-center shadow-2xl hover:shadow-blue-500/25"
            >
              <img
                src="/assets/images/ngao2.png"
                alt="National Shield"
                class="max-w-18 max-h-18 object-contain"
              />
            </div>
          </div>

          <!-- Center Content -->
          <div class="text-center flex-1">
            <h1
              class="text-4xl font-bold text-white mb-4 tracking-wide drop-shadow-lg animate-fade-in"
            >
              MUHIMBILI NATIONAL HOSPITAL
            </h1>
            <div class="relative inline-block mb-4">
              <div
                class="bg-gradient-to-r from-red-600 to-red-700 text-white px-10 py-4 rounded-full text-xl font-bold shadow-2xl transform hover:scale-105 transition-all duration-300 border-2 border-red-400/60"
              >
                <span class="relative z-10 flex items-center gap-2">
                  <i class="fas fa-shield-alt text-red-200"></i>
                  RESTRICTED
                </span>
                <div
                  class="absolute inset-0 bg-gradient-to-r from-red-700 to-red-800 rounded-full opacity-0 hover:opacity-100 transition-opacity duration-300"
                ></div>
              </div>
            </div>
            <h2
              class="text-2xl font-bold text-blue-100 tracking-wide drop-shadow-md animate-fade-in-delay"
            >
              DECLARATIONS BY STAFF
            </h2>
          </div>

          <!-- Right Logo -->
          <div class="w-28 h-28 ml-6 transform hover:scale-110 transition-transform duration-300">
            <div
              class="w-full h-full bg-gradient-to-br from-teal-500/20 to-blue-500/20 rounded-2xl backdrop-blur-sm border-2 border-teal-300/40 flex items-center justify-center shadow-2xl hover:shadow-teal-500/25"
            >
              <img
                src="/assets/images/logo2.png"
                alt="Muhimbili Logo"
                class="max-w-18 max-h-18 object-contain"
              />
            </div>
          </div>
        </div>
      </div>

      <!-- Main Form -->
      <div class="medical-glass-card rounded-b-3xl overflow-hidden">
        <form @submit.prevent="submitDeclaration" class="p-6 space-y-6">
          <!-- Declaration Text -->
          <div
            class="medical-card bg-gradient-to-r from-blue-600/25 to-blue-700/25 border-2 border-blue-400/40 p-5 rounded-xl backdrop-blur-sm hover:shadow-xl hover:shadow-blue-500/20 transition-all duration-300 group"
          >
            <div class="flex items-start space-x-4">
              <div class="flex-shrink-0">
                <div
                  class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-blue-300/50"
                >
                  <i class="fas fa-info-circle text-white text-lg"></i>
                </div>
              </div>
              <div class="flex-1">
                <h3 class="text-lg font-bold text-white mb-3 flex items-center">
                  <i class="fas fa-clipboard-list mr-2 text-blue-300"></i>
                  Declaration Information
                </h3>
                <div class="text-blue-100 leading-relaxed space-y-3">
                  <p class="text-sm font-medium">
                    These declarations have been designed to certify that users acknowledge that
                    they are aware of Muhimbili National Hospital Acceptable Information and
                    Communication Technology use policy and agree to abide by their terms.
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Personal Information Section -->
          <div
            class="medical-card bg-gradient-to-r from-blue-600/25 to-blue-700/25 border-2 border-blue-400/40 p-5 rounded-xl backdrop-blur-sm hover:shadow-xl hover:shadow-blue-500/20 transition-all duration-300 group"
          >
            <div class="flex items-center space-x-3 mb-4">
              <div
                class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-blue-300/50"
              >
                <i class="fas fa-user-md text-white text-lg"></i>
              </div>
              <h3 class="text-lg font-bold text-white flex items-center">
                <i class="fas fa-id-card mr-2 text-blue-300"></i>
                Personal Information
              </h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <!-- Full Name -->
              <div class="md:col-span-2">
                <label class="block text-sm font-bold text-blue-100 mb-2">
                  Full Name <span class="text-red-400">*</span>
                </label>
                <div class="relative">
                  <input
                    v-model="formData.fullName"
                    type="text"
                    class="medical-input w-full px-2 py-1.5 bg-blue-500/30 border-2 border-blue-400/60 rounded-lg focus:border-blue-500 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-blue-500/40 focus:bg-blue-500/50 focus:shadow-lg focus:shadow-blue-500/30 text-sm"
                    placeholder="Enter your full name"
                    required
                  />
                  <div
                    class="absolute inset-0 rounded-lg bg-gradient-to-r from-blue-500/10 to-blue-600/10 opacity-0 hover:opacity-100 transition-opacity duration-300 pointer-events-none"
                  ></div>
                </div>
              </div>

              <!-- PF Number -->
              <div>
                <label class="block text-sm font-bold text-blue-100 mb-2">
                  PF Number <span class="text-red-400">*</span>
                </label>
                <div class="relative">
                  <input
                    v-model="formData.pfNumber"
                    type="text"
                    class="medical-input w-full px-2 py-1.5 bg-blue-500/30 border-2 border-blue-400/60 rounded-lg focus:border-blue-500 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-blue-500/40 focus:bg-blue-500/50 focus:shadow-lg focus:shadow-blue-500/30 text-sm"
                    placeholder="Enter PF Number"
                    required
                  />
                  <div
                    class="absolute inset-0 rounded-lg bg-gradient-to-r from-blue-500/10 to-blue-600/10 opacity-0 hover:opacity-100 transition-opacity duration-300 pointer-events-none"
                  ></div>
                </div>
              </div>

              <!-- Department/Unit -->
              <div>
                <label class="block text-sm font-bold text-blue-100 mb-2">
                  Department/Unit <span class="text-red-400">*</span>
                </label>
                <div class="relative">
                  <select
                    v-model="formData.department"
                    class="medical-input w-full px-2 py-1.5 bg-blue-500/30 border-2 border-blue-400/60 rounded-lg focus:border-blue-500 focus:outline-none text-white backdrop-blur-sm transition-all duration-300 hover:bg-blue-500/40 focus:bg-blue-500/50 focus:shadow-lg focus:shadow-blue-500/30 appearance-none cursor-pointer text-sm"
                    required
                  >
                    <option value="" disabled class="bg-gray-800 text-gray-300">
                      Select your department
                    </option>
                    <option
                      v-for="dept in departments"
                      :key="dept"
                      :value="dept"
                      class="bg-gray-800 text-white"
                    >
                      {{ dept }}
                    </option>
                  </select>
                  <div
                    class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none"
                  >
                    <i class="fas fa-chevron-down text-blue-300"></i>
                  </div>
                  <div
                    class="absolute inset-0 rounded-lg bg-gradient-to-r from-blue-500/10 to-blue-600/10 opacity-0 hover:opacity-100 transition-opacity duration-300 pointer-events-none"
                  ></div>
                </div>
              </div>

              <!-- Job Title -->
              <div class="md:col-span-2">
                <label class="block text-sm font-bold text-blue-100 mb-2">
                  Job Title <span class="text-red-400">*</span>
                </label>
                <div class="relative">
                  <input
                    v-model="formData.jobTitle"
                    type="text"
                    class="medical-input w-full px-2 py-1.5 bg-blue-500/30 border-2 border-blue-400/60 rounded-lg focus:border-blue-500 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-blue-500/40 focus:bg-blue-500/50 focus:shadow-lg focus:shadow-blue-500/30 text-sm"
                    placeholder="Enter your job title"
                    required
                  />
                  <div
                    class="absolute inset-0 rounded-xl bg-gradient-to-r from-blue-500/10 to-blue-600/10 opacity-0 hover:opacity-100 transition-opacity duration-300 pointer-events-none"
                  ></div>
                </div>
              </div>
            </div>
          </div>

          <!-- Declaration Statement -->
          <div
            class="medical-card bg-gradient-to-r from-blue-600/25 to-blue-700/25 border-2 border-blue-400/40 p-6 rounded-2xl backdrop-blur-sm hover:shadow-2xl hover:shadow-blue-500/20 transition-all duration-500 group"
          >
            <div class="flex items-center space-x-3 mb-4">
              <div
                class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-blue-300/50"
              >
                <i class="fas fa-file-contract text-white text-sm"></i>
              </div>
              <h3 class="text-lg font-bold text-white flex items-center">
                <i class="fas fa-scroll mr-2 text-blue-300 text-sm"></i>
                Declaration Statement
              </h3>
            </div>

            <div class="bg-white/15 p-6 rounded-xl backdrop-blur-sm border-2 border-blue-300/30">
              <div class="text-blue-100 leading-relaxed text-lg space-y-5 text-justify">
                <p>
                  I
                  <strong class="text-blue-300">{{
                    formData.fullName || '______________________'
                  }}</strong>
                  PF No.
                  <strong class="text-blue-300">{{ formData.pfNumber || '______' }}</strong>
                  acknowledge that Muhimbili National Hospital acceptable ICT use policy has been
                  made available to me for adequate review and understanding.
                </p>

                <p>
                  I certify that I have been given ample opportunity to read and understand it, and
                  ask questions about my responsibilities on it. I am, therefore, aware that I am
                  accountable to all its terms and requirements; and that I shall abide by them.
                </p>

                <p>
                  I also understand that failure to abide by them; Muhimbili National Hospital shall
                  take against me appropriate disciplinary action or legal action, or both, as the
                  case may be.
                </p>
              </div>
            </div>
          </div>

          <!-- Signature Section -->
          <div
            class="medical-card bg-gradient-to-r from-blue-600/25 to-blue-700/25 border-2 border-blue-400/40 p-4 rounded-xl backdrop-blur-sm hover:shadow-xl hover:shadow-blue-500/20 transition-all duration-300 group"
          >
            <div class="flex items-center space-x-2 mb-3">
              <div
                class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-blue-300/50"
              >
                <i class="fas fa-signature text-white text-xs"></i>
              </div>
              <h3 class="text-base font-bold text-white flex items-center">
                <i class="fas fa-pen-fancy mr-2 text-blue-300 text-xs"></i>
                Digital Signature
              </h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
              <!-- Signature Upload -->
              <div class="md:col-span-2">
                <label class="block text-sm font-bold text-blue-100 mb-2">
                  Signature <span class="text-red-400">*</span>
                </label>

                <div class="relative">
                  <div
                    v-if="!signaturePreview"
                    class="w-full px-2 py-2 border-2 border-dashed border-blue-400/60 rounded-lg focus-within:border-blue-500 bg-blue-500/20 backdrop-blur-sm transition-all duration-300 shadow-lg hover:shadow-xl hover:shadow-blue-500/30 min-h-[40px] flex items-center justify-center hover:bg-blue-500/30"
                  >
                    <div class="text-center">
                      <div class="mb-1">
                        <i class="fas fa-signature text-blue-300 text-sm mb-1"></i>
                        <p class="text-blue-100 text-xs">No signature</p>
                      </div>
                      <button
                        type="button"
                        @click="loadSignature"
                        class="px-2 py-1 bg-gradient-to-r from-blue-500 to-blue-600 text-white text-xs font-semibold rounded-md hover:from-blue-600 hover:to-blue-700 transition-all duration-300 flex items-center gap-1 mx-auto shadow-lg hover:shadow-xl transform hover:scale-105 border border-blue-400/50"
                      >
                        <i class="fas fa-download text-xs"></i>
                        Load
                      </button>
                    </div>
                  </div>

                  <div
                    v-else
                    class="w-full px-2 py-1.5 border-2 border-blue-400/60 rounded-lg bg-blue-500/20 backdrop-blur-sm transition-all duration-300 shadow-sm hover:shadow-md hover:bg-blue-500/30 min-h-[35px] flex items-center justify-center relative"
                  >
                    <div v-if="isImage(signaturePreview)" class="text-center">
                      <img
                        :src="signaturePreview"
                        alt="Digital Signature"
                        class="max-h-[25px] max-w-full object-contain mx-auto mb-1"
                      />
                      <p class="text-xs text-blue-100 truncate">
                        {{ signatureFileName }}
                      </p>
                    </div>
                    <div v-else class="text-center">
                      <div
                        class="w-5 h-5 bg-red-500/20 rounded-lg flex items-center justify-center mx-auto mb-1"
                      >
                        <i class="fas fa-file-pdf text-red-400 text-xs"></i>
                      </div>
                      <p class="text-xs text-blue-100 truncate">
                        {{ signatureFileName }}
                      </p>
                    </div>

                    <div class="absolute top-0.5 right-0.5 flex gap-0.5">
                      <button
                        type="button"
                        @click="loadSignature"
                        class="w-4 h-4 bg-blue-500 text-white rounded-full flex items-center justify-center text-xs hover:bg-blue-600 transition-colors duration-200"
                        title="Change signature"
                      >
                        <i class="fas fa-edit text-xs"></i>
                      </button>
                      <button
                        type="button"
                        @click="clearSignature"
                        class="w-4 h-4 bg-red-500 text-white rounded-full flex items-center justify-center text-xs hover:bg-red-600 transition-colors duration-200"
                        title="Remove signature"
                      >
                        <i class="fas fa-times text-xs"></i>
                      </button>
                    </div>
                  </div>

                  <input
                    ref="signatureInput"
                    type="file"
                    accept="image/png,image/jpeg,image/jpg,application/pdf"
                    @change="onSignatureChange"
                    class="hidden"
                  />
                </div>
              </div>

              <!-- Date -->
              <div>
                <label class="block text-sm font-bold text-blue-100 mb-2">
                  Date <span class="text-red-400">*</span>
                </label>
                <div class="relative">
                  <input
                    v-model="formData.date"
                    type="date"
                    class="medical-input w-full px-2 py-1.5 bg-blue-500/30 border-2 border-blue-400/60 rounded-lg focus:border-blue-500 focus:outline-none text-white backdrop-blur-sm transition-all duration-300 hover:bg-blue-500/40 focus:bg-blue-500/50 focus:shadow-lg focus:shadow-blue-500/30 text-sm"
                    required
                  />
                  <div
                    class="absolute inset-0 rounded-xl bg-gradient-to-r from-emerald-500/10 to-teal-500/10 opacity-0 hover:opacity-100 transition-opacity duration-300 pointer-events-none"
                  ></div>
                </div>
              </div>
            </div>
          </div>

          <!-- Agreement Checkbox -->
          <div
            class="medical-card bg-gradient-to-r from-red-600/25 to-rose-600/25 border-2 border-red-400/40 p-8 rounded-2xl backdrop-blur-sm hover:shadow-2xl hover:shadow-red-500/20 transition-all duration-500 group"
          >
            <div class="flex items-start space-x-4">
              <div class="flex-shrink-0 mt-1">
                <div class="relative">
                  <input
                    v-model="formData.agreement"
                    type="checkbox"
                    id="agreement"
                    class="w-7 h-7 text-red-500 bg-white/15 border-2 border-red-300/40 rounded-lg focus:ring-red-400 focus:ring-2 backdrop-blur-sm transition-all duration-300"
                    required
                  />
                  <div
                    class="absolute inset-0 rounded-lg bg-gradient-to-r from-red-500/10 to-rose-500/10 opacity-0 hover:opacity-100 transition-opacity duration-300 pointer-events-none"
                  ></div>
                </div>
              </div>
              <label for="agreement" class="text-sm text-blue-100 leading-relaxed flex-1">
                <span class="font-bold text-red-300 flex items-center gap-2"
                  ><i class="fas fa-exclamation-triangle"></i>I hereby confirm that:</span
                >
                I have read, understood, and agree to abide by all terms and conditions of the
                Muhimbili National Hospital Acceptable ICT Use Policy. I acknowledge that violation
                of this policy may result in disciplinary action or legal consequences.
              </label>
            </div>
          </div>

          <!-- Form Actions -->
          <div class="border-t-2 border-blue-300/30 pt-8">
            <div class="flex flex-col sm:flex-row justify-center gap-6">
              <button
                type="submit"
                :disabled="!isFormValid || isSubmitting"
                class="px-8 py-4 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all duration-300 font-semibold flex items-center justify-center shadow-xl hover:shadow-2xl hover:shadow-blue-500/25 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none border-2 border-blue-500/50"
              >
                <i v-if="isSubmitting" class="fas fa-spinner fa-spin mr-3"></i>
                <i v-else class="fas fa-file-signature mr-3"></i>
                {{ isSubmitting ? 'Submitting...' : 'Submit Declaration' }}
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Back Button -->
    <button
      @click="goBack"
      class="fixed bottom-6 left-6 w-14 h-14 bg-blue-600 hover:bg-blue-700 text-white rounded-full shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-110 focus:outline-none focus:ring-4 focus:ring-blue-300 focus:ring-offset-2 z-40 group"
      title="Go Back"
    >
      <i class="fas fa-arrow-left text-lg group-hover:animate-pulse"></i>
    </button>
  </div>
</template>

<script>
  export default {
    name: 'DeclarationForm',
    emits: ['form-submitted', 'go-back'],
    data() {
      return {
        isSubmitting: false,
        signaturePreview: '',
        signatureFileName: '',
        departments: [
          'Administration',
          'Anesthesiology',
          'Cardiology',
          'Dermatology',
          'Emergency Medicine',
          'Endocrinology',
          'Gastroenterology',
          'General Surgery',
          'Gynecology',
          'Hematology',
          'ICT Department',
          'Internal Medicine',
          'Laboratory',
          'Nephrology',
          'Neurology',
          'Nursing',
          'Obstetrics',
          'Oncology',
          'Ophthalmology',
          'Orthopedics',
          'Pediatrics',
          'Pharmacy',
          'Psychiatry',
          'Pulmonology',
          'Radiology',
          'Rehabilitation',
          'Urology',
          'Other'
        ],
        formData: {
          fullName: '',
          pfNumber: '',
          department: '',
          jobTitle: '',
          signature: null,
          date: '',
          agreement: false
        }
      }
    },

    computed: {
      isFormValid() {
        return (
          this.formData.fullName &&
          this.formData.pfNumber &&
          this.formData.department &&
          this.formData.jobTitle &&
          this.formData.date &&
          this.formData.agreement &&
          (this.formData.signature || this.signaturePreview)
        )
      }
    },

    methods: {
      loadSignature() {
        this.$refs.signatureInput.click()
      },

      onSignatureChange(e) {
        const file = e.target.files[0]
        this.formData.signature = file || null

        if (!file) {
          this.signaturePreview = ''
          this.signatureFileName = ''
          return
        }

        // Validate file type
        const allowedTypes = ['image/png', 'image/jpeg', 'image/jpg', 'application/pdf']
        if (!allowedTypes.includes(file.type)) {
          this.showNotification('Please select a valid file (PNG, JPG, JPEG, PDF)', 'error')
          this.clearSignature()
          return
        }

        // Validate file size (max 5MB)
        if (file.size > 5 * 1024 * 1024) {
          this.showNotification('File size must be less than 5MB', 'error')
          this.clearSignature()
          return
        }

        this.signatureFileName = file.name

        if (file.type.startsWith('image/')) {
          const reader = new FileReader()
          reader.onload = () => {
            this.signaturePreview = reader.result
          }
          reader.readAsDataURL(file)
        } else {
          this.signaturePreview = 'pdf'
        }
      },

      isImage(preview) {
        return typeof preview === 'string' && preview !== 'pdf'
      },

      clearSignature() {
        this.formData.signature = null
        this.signaturePreview = ''
        this.signatureFileName = ''
        if (this.$refs.signatureInput) {
          this.$refs.signatureInput.value = ''
        }
      },

      async submitDeclaration() {
        if (!this.isFormValid) {
          this.showNotification(
            'Please fill in all required fields and agree to the terms',
            'error'
          )
          return
        }

        try {
          // Show loading state
          this.isSubmitting = true
          this.showNotification('Submitting declaration...', 'info')

          // Prepare form data for submission
          const declarationData = {
            fullName: this.formData.fullName,
            pfNumber: this.formData.pfNumber,
            department: this.formData.department,
            jobTitle: this.formData.jobTitle,
            date: this.formData.date,
            agreement: this.formData.agreement
          }

          // Create FormData for file upload if signature exists
          const formData = new FormData()

          // Add declaration data with proper type conversion
          Object.keys(declarationData).forEach((key) => {
            let value = declarationData[key]
            // Convert boolean to string for FormData, but ensure it's a proper boolean string
            if (typeof value === 'boolean') {
              value = value ? '1' : '0' // Use 1/0 instead of true/false for better backend handling
            }
            formData.append(key, value)
          })

          // Log the form data for debugging
          console.log('Form data being sent:', {
            ...declarationData,
            hasSignature: !!this.formData.signature
          })

          // Add signature file if present
          if (this.formData.signature) {
            formData.append('signature', this.formData.signature)
          }

          // Import API client and submit
          const { authAPI } = await import('../../../utils/apiClient')
          const result = await authAPI.submitDeclaration(formData)

          if (result.success) {
            console.log('Declaration submitted successfully:', result.data)
            this.showNotification('Declaration submitted successfully!', 'success')

            // Emit event to parent component with the declaration data
            setTimeout(() => {
              this.$emit('form-submitted', declarationData)
            }, 1500) // Wait 1.5 seconds to show success message
          } else {
            console.error('Declaration submission failed:', result.error)
            this.showNotification(result.error || 'Failed to submit declaration', 'error')

            // Show validation errors if present
            if (result.errors) {
              Object.values(result.errors).forEach((errorArray) => {
                errorArray.forEach((error) => {
                  this.showNotification(error, 'error')
                })
              })
            }
          }
        } catch (error) {
          console.error('Error submitting declaration:', error)
          this.showNotification('An error occurred while submitting the declaration', 'error')
        } finally {
          this.isSubmitting = false
        }
      },

      resetForm() {
        this.formData = {
          fullName: '',
          pfNumber: '',
          department: '',
          jobTitle: '',
          signature: null,
          date: '',
          agreement: false
        }
        this.signaturePreview = ''
        this.signatureFileName = ''
        this.showNotification('Form has been reset', 'info')
      },

      printForm() {
        window.print()
      },

      showNotification(message, type = 'info') {
        const colors = {
          success: 'green',
          error: 'red',
          info: 'blue'
        }

        // Create a simple toast notification
        const toast = document.createElement('div')
        toast.className = `fixed top-4 right-4 px-6 py-3 rounded-lg text-white font-semibold z-50 bg-${colors[type]}-600 shadow-lg`
        toast.textContent = message
        document.body.appendChild(toast)

        setTimeout(() => {
          if (document.body.contains(toast)) {
            document.body.removeChild(toast)
          }
        }, 4000)
      },

      goBack() {
        this.$emit('go-back')
      },

      async loadUserData() {
        try {
          // Get current user data from API
          const { authAPI } = await import('../../../utils/apiClient')
          const result = await authAPI.getCurrentUser()

          if (result.success && result.data) {
            // Auto-populate the full name field
            this.formData.fullName = result.data.name || ''
            console.log('Auto-populated Full Name:', this.formData.fullName)
          } else {
            console.warn('Failed to load user data for auto-population')
          }
        } catch (error) {
          console.error('Error loading user data:', error)
        }
      }
    },

    async mounted() {
      // Set current date as default
      const today = new Date().toISOString().split('T')[0]
      this.formData.date = today

      // Auto-populate Full Name from authenticated user
      await this.loadUserData()
    }
  }
</script>

<style scoped>
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

  .medical-input {
    position: relative;
    z-index: 1;
    color: white;
  }

  .medical-input::placeholder {
    color: rgba(191, 219, 254, 0.6);
  }

  .medical-input:focus {
    border-color: rgba(45, 212, 191, 0.8);
    box-shadow: 0 0 0 3px rgba(45, 212, 191, 0.2);
  }

  /* Select dropdown styling */
  select.medical-input {
    background-image: none;
  }

  select.medical-input option {
    background-color: #1f2937;
    color: white;
    padding: 8px;
  }

  select.medical-input option:hover {
    background-color: #374151;
  }

  select.medical-input option:disabled {
    color: #9ca3af;
  }

  /* Animations */
  @keyframes blob {
    0% {
      transform: translate(0px, 0px) scale(1);
    }
    33% {
      transform: translate(30px, -50px) scale(1.1);
    }
    66% {
      transform: translate(-20px, 20px) scale(0.9);
    }
    100% {
      transform: translate(0px, 0px) scale(1);
    }
  }

  @keyframes float {
    0%,
    100% {
      transform: translateY(0px);
    }
    50% {
      transform: translateY(-20px);
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

  .animate-blob {
    animation: blob 7s infinite;
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

  .animation-delay-2000 {
    animation-delay: 2s;
  }

  .animation-delay-4000 {
    animation-delay: 4s;
  }

  /* Custom transitions and animations */
  .transition-all {
    transition: all 0.3s ease;
  }

  /* Glass morphism effect */
  .backdrop-blur-sm {
    backdrop-filter: blur(8px);
  }

  /* Enhanced form sections */
  .border-l-4 {
    position: relative;
    transition: all 0.3s ease;
  }

  .border-l-4:hover {
    transform: translateX(2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
  }

  /* Print styles */
  @media print {
    .min-h-screen {
      min-height: auto;
    }

    button {
      display: none !important;
    }

    .shadow-xl {
      box-shadow: none;
    }

    .bg-gradient-to-br {
      background: white !important;
    }

    .rounded-t-2xl,
    .rounded-b-2xl {
      border-radius: 0 !important;
    }

    .border-l-4 {
      border-left: 2px solid #000 !important;
    }

    .bg-blue-50,
    .bg-green-50,
    .bg-yellow-50,
    .bg-red-50 {
      background: #f9f9f9 !important;
    }

    .text-white {
      color: #000 !important;
    }

    .bg-red-600 {
      background: #000 !important;
      color: #fff !important;
    }
  }

  /* Responsive adjustments */
  @media (max-width: 768px) {
    .grid-cols-2 {
      grid-template-columns: repeat(1, minmax(0, 1fr));
    }

    .md\:col-span-2 {
      grid-column: span 1;
    }
  }

  /* Custom input focus styles */
  input:focus,
  select:focus,
  textarea:focus {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  }

  /* Checkbox custom styling */
  input[type='checkbox']:checked {
    background-color: #dc2626;
    border-color: #dc2626;
  }

  /* Animation for form sections */
  .border-l-4 {
    animation: slideInLeft 0.5s ease-out;
  }

  @keyframes slideInLeft {
    from {
      opacity: 0;
      transform: translateX(-20px);
    }
    to {
      opacity: 1;
      transform: translateX(0);
    }
  }

  /* Button hover effects */
  button:hover:not(:disabled) {
    transform: translateY(-2px) scale(1.02);
  }

  button:active:not(:disabled) {
    transform: translateY(0) scale(0.98);
  }

  /* Signature area hover effects */
  .border-dashed:hover {
    border-color: #10b981;
    background-color: #f0fdf4;
  }

  /* Custom scrollbar */
  ::-webkit-scrollbar {
    width: 6px;
  }

  ::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
  }

  ::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
  }

  ::-webkit-scrollbar-thumb:hover {
    background: #a1a1a1;
  }
</style>
"
