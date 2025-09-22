<template>
  <div class="access-rights-approval-form">
    <div class="bg-white shadow-lg rounded-lg p-6">
      <h2 class="text-2xl font-bold text-gray-800 mb-6">Access Rights and Approval Workflow</h2>

      <!-- Form -->
      <form @submit.prevent="submitForm" class="space-y-8">
        <!-- User Access ID Selection -->
        <div>
          <label for="userAccessId" class="block text-base font-medium text-gray-700 mb-2">
            Select User Access Request
          </label>
          <select
            id="userAccessId"
            v-model="formData.userAccessId"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            required
          >
            <option value="">Select a user access request...</option>
            <option v-for="request in userAccessRequests" :key="request.id" :value="request.id">
              {{ request.staff_name }} - {{ request.pf_number }} ({{ request.status }})
            </option>
          </select>
        </div>

        <!-- Access Rights Section -->
        <div class="border border-gray-200 rounded-lg p-6">
          <h3 class="text-lg font-semibold text-gray-800 mb-4">Access Rights</h3>

          <div class="space-y-4">
            <!-- Permanent Access -->
            <label class="flex items-center">
              <input
                type="radio"
                v-model="formData.accessType"
                value="permanent"
                class="mr-3"
                required
              />
              <span class="text-sm font-medium">Permanent (until retirement)</span>
            </label>

            <!-- Temporary Access -->
            <div>
              <label class="flex items-center mb-2">
                <input
                  type="radio"
                  v-model="formData.accessType"
                  value="temporary"
                  class="mr-3"
                  required
                />
                <span class="text-sm font-medium">Temporary Until</span>
              </label>

              <div v-if="formData.accessType === 'temporary'" class="ml-6">
                <input
                  type="date"
                  v-model="formData.temporaryUntil"
                  :min="tomorrow"
                  class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  required
                />
              </div>
            </div>
          </div>

          <p v-if="errors.accessType" class="text-red-500 text-sm mt-1">
            {{ errors.accessType }}
          </p>
          <p v-if="errors.temporaryUntil" class="text-red-500 text-sm mt-1">
            {{ errors.temporaryUntil }}
          </p>
        </div>

        <!-- Approval Workflow Section -->
        <div class="border border-gray-200 rounded-lg p-6">
          <h3 class="text-lg font-semibold text-gray-800 mb-6">Approval Workflow</h3>

          <!-- HOD/BM Approval -->
          <div class="mb-8 p-4 bg-blue-50 rounded-lg">
            <h4 class="text-md font-semibold text-blue-800 mb-4">1. HoD/BM Approval</h4>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-base font-medium text-gray-700 mb-1">Name</label>
                <input
                  type="text"
                  v-model="formData.hodName"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="Enter HoD/BM name"
                />
              </div>

              <div>
                <label class="block text-base font-medium text-gray-700 mb-1">Date</label>
                <input
                  type="date"
                  v-model="formData.hodDate"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
              </div>

              <div class="md:col-span-2">
                <label class="block text-base font-medium text-gray-700 mb-1"
                  >Signature Upload</label
                >
                <input
                  type="file"
                  @change="handleFileUpload($event, 'hodSignature')"
                  accept=".jpg,.jpeg,.png,.pdf"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
                <p class="text-xs text-gray-500 mt-1">Accepted formats: JPG, PNG, PDF (max 2MB)</p>
              </div>

              <div class="md:col-span-2">
                <label class="block text-base font-medium text-gray-700 mb-1">Comments</label>
                <textarea
                  v-model="formData.hodComments"
                  rows="3"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="Enter approval comments..."
                ></textarea>
              </div>
            </div>
          </div>

          <!-- Divisional Director Approval -->
          <div class="mb-8 p-4 bg-green-50 rounded-lg">
            <h4 class="text-md font-semibold text-green-800 mb-4">
              2. Divisional Director Approval
            </h4>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-base font-medium text-gray-700 mb-1">Name</label>
                <input
                  type="text"
                  v-model="formData.divisionalDirectorName"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                  placeholder="Enter Divisional Director name"
                />
              </div>

              <div>
                <label class="block text-base font-medium text-gray-700 mb-1">Date</label>
                <input
                  type="date"
                  v-model="formData.divisionalDirectorDate"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                />
              </div>

              <div class="md:col-span-2">
                <label class="block text-base font-medium text-gray-700 mb-1"
                  >Signature Upload</label
                >
                <input
                  type="file"
                  @change="handleFileUpload($event, 'divisionalDirectorSignature')"
                  accept=".jpg,.jpeg,.png,.pdf"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                />
                <p class="text-xs text-gray-500 mt-1">Accepted formats: JPG, PNG, PDF (max 2MB)</p>
              </div>

              <div class="md:col-span-2">
                <label class="block text-base font-medium text-gray-700 mb-1">Comments</label>
                <textarea
                  v-model="formData.divisionalDirectorComments"
                  rows="3"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                  placeholder="Enter approval comments..."
                ></textarea>
              </div>
            </div>
          </div>

          <!-- ICT Director Approval -->
          <div class="mb-6 p-4 bg-purple-50 rounded-lg">
            <h4 class="text-md font-semibold text-purple-800 mb-4">3. Director of ICT Approval</h4>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-base font-medium text-gray-700 mb-1">Name</label>
                <input
                  type="text"
                  v-model="formData.ictDirectorName"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500"
                  placeholder="Enter ICT Director name"
                />
              </div>

              <div>
                <label class="block text-base font-medium text-gray-700 mb-1">Date</label>
                <input
                  type="date"
                  v-model="formData.ictDirectorDate"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500"
                />
              </div>

              <div class="md:col-span-2">
                <label class="block text-base font-medium text-gray-700 mb-1"
                  >Signature Upload</label
                >
                <input
                  type="file"
                  @change="handleFileUpload($event, 'ictDirectorSignature')"
                  accept=".jpg,.jpeg,.png,.pdf"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500"
                />
                <p class="text-xs text-gray-500 mt-1">Accepted formats: JPG, PNG, PDF (max 2MB)</p>
              </div>

              <div class="md:col-span-2">
                <label class="block text-base font-medium text-gray-700 mb-1">Comments</label>
                <textarea
                  v-model="formData.ictDirectorComments"
                  rows="3"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500"
                  placeholder="Enter approval comments..."
                ></textarea>
              </div>
            </div>
          </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end space-x-4">
          <button
            type="button"
            @click="resetForm"
            class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
          >
            Reset
          </button>
          <button
            type="submit"
            :disabled="isSubmitting"
            class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50"
          >
            {{ isSubmitting ? 'Submitting...' : 'Submit Approval Workflow' }}
          </button>
        </div>
      </form>

      <!-- Success/Error Messages -->
      <div
        v-if="successMessage"
        class="mt-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded"
      >
        {{ successMessage }}
      </div>
      <div
        v-if="errorMessage"
        class="mt-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded"
      >
        {{ errorMessage }}
      </div>

      <!-- Debug Information -->
      <div v-if="showDebug" class="mt-6 p-4 bg-gray-100 rounded">
        <h3 class="font-bold mb-2">Debug Information:</h3>
        <pre class="text-xs">{{
          JSON.stringify(
            { formData: formDataForDebug, userAccessRequests: userAccessRequests.length },
            null,
            2
          )
        }}</pre>
      </div>
    </div>
  </div>
</template>

<script>
  import axios from 'axios'

  export default {
    name: 'AccessRightsApprovalForm',
    data() {
      return {
        formData: {
          userAccessId: '',
          accessType: '',
          temporaryUntil: '',
          hodName: '',
          hodDate: '',
          hodSignature: null,
          hodComments: '',
          divisionalDirectorName: '',
          divisionalDirectorDate: '',
          divisionalDirectorSignature: null,
          divisionalDirectorComments: '',
          ictDirectorName: '',
          ictDirectorDate: '',
          ictDirectorSignature: null,
          ictDirectorComments: ''
        },
        userAccessRequests: [],
        errors: {},
        isSubmitting: false,
        successMessage: '',
        errorMessage: '',
        showDebug: true // Set to false in production
      }
    },
    computed: {
      tomorrow() {
        const tomorrow = new Date()
        tomorrow.setDate(tomorrow.getDate() + 1)
        return tomorrow.toISOString().split('T')[0]
      },
      formDataForDebug() {
        // Return form data without file objects for debug display
        const debug = { ...this.formData }
        debug.hodSignature = this.formData.hodSignature ? 'File selected' : null
        debug.divisionalDirectorSignature = this.formData.divisionalDirectorSignature
          ? 'File selected'
          : null
        debug.ictDirectorSignature = this.formData.ictDirectorSignature ? 'File selected' : null
        return debug
      }
    },
    async mounted() {
      await this.loadUserAccessRequests()
    },
    methods: {
      async loadUserAccessRequests() {
        try {
          const response = await axios.get('/api/v1/user-access')
          this.userAccessRequests = response.data.data || []
          console.log('User access requests loaded:', this.userAccessRequests.length)
        } catch (error) {
          console.error('Error loading user access requests:', error)
          // Create mock data for testing
          this.userAccessRequests = [
            { id: 1, staff_name: 'Test User', pf_number: 'PF001', status: 'pending' },
            { id: 2, staff_name: 'Another User', pf_number: 'PF002', status: 'approved' }
          ]
        }
      },

      handleFileUpload(event, fieldName) {
        const file = event.target.files[0]
        if (file) {
          // Validate file size (2MB max)
          if (file.size > 2 * 1024 * 1024) {
            this.errors[fieldName] = 'File size must not exceed 2MB'
            event.target.value = ''
            return
          }

          // Validate file type
          const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf']
          if (!allowedTypes.includes(file.type)) {
            this.errors[fieldName] = 'File must be in JPEG, JPG, PNG, or PDF format'
            event.target.value = ''
            return
          }

          this.formData[fieldName] = file
          delete this.errors[fieldName]
        }
      },

      validateForm() {
        this.errors = {}

        if (!this.formData.accessType) {
          this.errors.accessType = 'Please select an access type (Permanent or Temporary)'
        }

        if (this.formData.accessType === 'temporary' && !this.formData.temporaryUntil) {
          this.errors.temporaryUntil = 'Please specify the temporary access end date'
        }

        if (this.formData.accessType === 'temporary' && this.formData.temporaryUntil) {
          const selectedDate = new Date(this.formData.temporaryUntil)
          const today = new Date()
          today.setHours(0, 0, 0, 0)

          if (selectedDate <= today) {
            this.errors.temporaryUntil = 'Temporary access end date must be in the future'
          }
        }

        return Object.keys(this.errors).length === 0
      },

      async submitForm() {
        if (!this.validateForm()) {
          return
        }

        this.isSubmitting = true
        this.successMessage = ''
        this.errorMessage = ''

        try {
          // Create FormData for file uploads
          const formData = new FormData()

          // Add basic form data
          formData.append('user_access_id', this.formData.userAccessId)
          formData.append('access_type', this.formData.accessType)

          if (this.formData.accessType === 'temporary' && this.formData.temporaryUntil) {
            formData.append('temporary_until', this.formData.temporaryUntil)
          }

          // Add HOD data if provided
          if (this.formData.hodName) {
            formData.append('hod_name', this.formData.hodName)
            if (this.formData.hodDate) formData.append('hod_date', this.formData.hodDate)
            if (this.formData.hodSignature)
              formData.append('hod_signature', this.formData.hodSignature)
            if (this.formData.hodComments)
              formData.append('hod_comments', this.formData.hodComments)
          }

          // Add Divisional Director data if provided
          if (this.formData.divisionalDirectorName) {
            formData.append('divisional_director_name', this.formData.divisionalDirectorName)
            if (this.formData.divisionalDirectorDate)
              formData.append('divisional_director_date', this.formData.divisionalDirectorDate)
            if (this.formData.divisionalDirectorSignature)
              formData.append(
                'divisional_director_signature',
                this.formData.divisionalDirectorSignature
              )
            if (this.formData.divisionalDirectorComments)
              formData.append(
                'divisional_director_comments',
                this.formData.divisionalDirectorComments
              )
          }

          // Add ICT Director data if provided
          if (this.formData.ictDirectorName) {
            formData.append('ict_director_name', this.formData.ictDirectorName)
            if (this.formData.ictDirectorDate)
              formData.append('ict_director_date', this.formData.ictDirectorDate)
            if (this.formData.ictDirectorSignature)
              formData.append('ict_director_signature', this.formData.ictDirectorSignature)
            if (this.formData.ictDirectorComments)
              formData.append('ict_director_comments', this.formData.ictDirectorComments)
          }

          console.log('Submitting access rights and approval workflow...')

          const response = await axios.post('/api/access-rights-approval', formData, {
            headers: {
              'Content-Type': 'multipart/form-data'
            }
          })

          console.log('Access rights and approval workflow response:', response.data)

          if (response.data.status === 'success') {
            this.successMessage = response.data.message
            this.resetForm()
          } else {
            this.errorMessage =
              response.data.message || 'Failed to submit access rights and approval workflow'
          }
        } catch (error) {
          console.error('Error submitting access rights and approval workflow:', error)

          if (error.response && error.response.data) {
            if (error.response.data.errors) {
              this.errors = error.response.data.errors
            }
            this.errorMessage =
              error.response.data.message || 'Failed to submit access rights and approval workflow'
          } else {
            this.errorMessage = 'Network error. Please try again.'
          }
        } finally {
          this.isSubmitting = false
        }
      },

      resetForm() {
        this.formData = {
          userAccessId: '',
          accessType: '',
          temporaryUntil: '',
          hodName: '',
          hodDate: '',
          hodSignature: null,
          hodComments: '',
          divisionalDirectorName: '',
          divisionalDirectorDate: '',
          divisionalDirectorSignature: null,
          divisionalDirectorComments: '',
          ictDirectorName: '',
          ictDirectorDate: '',
          ictDirectorSignature: null,
          ictDirectorComments: ''
        }
        this.errors = {}
        this.successMessage = ''
        this.errorMessage = ''

        // Reset file inputs
        const fileInputs = document.querySelectorAll('input[type="file"]')
        fileInputs.forEach((input) => {
          input.value = ''
        })
      }
    }
  }
</script>

<style scoped>
  .access-rights-approval-form {
    max-width: 1000px;
    margin: 0 auto;
    padding: 20px;
  }

  .grid {
    display: grid;
  }

  .grid-cols-1 {
    grid-template-columns: repeat(1, minmax(0, 1fr));
  }

  .grid-cols-2 {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }

  .gap-4 {
    gap: 1rem;
  }

  .space-y-4 > * + * {
    margin-top: 1rem;
  }

  .space-y-6 > * + * {
    margin-top: 1.5rem;
  }

  .space-y-8 > * + * {
    margin-top: 2rem;
  }

  .space-x-4 > * + * {
    margin-left: 1rem;
  }

  @media (min-width: 768px) {
    .md\:grid-cols-2 {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .md\:col-span-2 {
      grid-column: span 2 / span 2;
    }
  }
</style>
