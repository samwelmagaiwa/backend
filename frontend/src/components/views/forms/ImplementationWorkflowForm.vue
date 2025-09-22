<template>
  <div class="implementation-workflow-form">
    <div class="bg-white shadow-lg rounded-lg p-6">
      <h2 class="text-2xl font-bold text-gray-800 mb-6">For Implementation Workflow</h2>

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

        <!-- Implementation Workflow Section -->
        <div class="border border-gray-200 rounded-lg p-6">
          <h3 class="text-lg font-semibold text-gray-800 mb-6">Implementation Workflow</h3>

          <!-- Head of IT Approval -->
          <div class="mb-8 p-4 bg-indigo-50 rounded-lg">
            <h4 class="text-md font-semibold text-indigo-800 mb-4">1. Head of IT Approval</h4>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-base font-medium text-gray-700 mb-1">
                  Name <span class="text-red-500">*</span>
                </label>
                <input
                  type="text"
                  v-model="formData.headItName"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                  placeholder="Enter Head of IT name"
                  required
                />
              </div>

              <div>
                <label class="block text-base font-medium text-gray-700 mb-1">
                  Date <span class="text-red-500">*</span>
                </label>
                <input
                  type="date"
                  v-model="formData.headItDate"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                  required
                />
              </div>

              <div class="md:col-span-2">
                <label class="block text-base font-medium text-gray-700 mb-1">
                  Signature Upload <span class="text-red-500">*</span>
                </label>
                <input
                  type="file"
                  @change="handleFileUpload($event, 'headItSignature')"
                  accept=".jpg,.jpeg,.png,.pdf"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                  required
                />
                <p class="text-xs text-gray-500 mt-1">Accepted formats: JPG, PNG, PDF (max 2MB)</p>
                <p v-if="errors.headItSignature" class="text-red-500 text-sm mt-1">
                  {{ errors.headItSignature }}
                </p>
              </div>

              <div class="md:col-span-2">
                <label class="block text-base font-medium text-gray-700 mb-1">Comments</label>
                <textarea
                  v-model="formData.headItComments"
                  rows="3"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                  placeholder="Enter approval comments..."
                ></textarea>
              </div>
            </div>
          </div>

          <!-- ICT Officer Implementation -->
          <div class="mb-6 p-4 bg-orange-50 rounded-lg">
            <h4 class="text-md font-semibold text-orange-800 mb-4">
              2. ICT Officer Granting Access
            </h4>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-base font-medium text-gray-700 mb-1">
                  Name <span class="text-red-500">*</span>
                </label>
                <input
                  type="text"
                  v-model="formData.ictOfficerName"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500"
                  placeholder="Enter ICT Officer name"
                  required
                />
              </div>

              <div>
                <label class="block text-base font-medium text-gray-700 mb-1">
                  Date <span class="text-red-500">*</span>
                </label>
                <input
                  type="date"
                  v-model="formData.ictOfficerDate"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500"
                  required
                />
              </div>

              <div class="md:col-span-2">
                <label class="block text-base font-medium text-gray-700 mb-1">
                  Signature Upload <span class="text-red-500">*</span>
                </label>
                <input
                  type="file"
                  @change="handleFileUpload($event, 'ictOfficerSignature')"
                  accept=".jpg,.jpeg,.png,.pdf"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500"
                  required
                />
                <p class="text-xs text-gray-500 mt-1">Accepted formats: JPG, PNG, PDF (max 2MB)</p>
                <p v-if="errors.ictOfficerSignature" class="text-red-500 text-sm mt-1">
                  {{ errors.ictOfficerSignature }}
                </p>
              </div>

              <div class="md:col-span-2">
                <label class="block text-base font-medium text-gray-700 mb-1"
                  >ICT Officer Comments</label
                >
                <textarea
                  v-model="formData.ictOfficerComments"
                  rows="3"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500"
                  placeholder="Enter implementation comments..."
                ></textarea>
              </div>

              <div class="md:col-span-2">
                <label class="block text-base font-medium text-gray-700 mb-1"
                  >Implementation Comments</label
                >
                <textarea
                  v-model="formData.implementationComments"
                  rows="3"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500"
                  placeholder="Enter general implementation comments..."
                ></textarea>
              </div>
            </div>
          </div>
        </div>

        <!-- Validation Summary -->
        <div
          v-if="showValidationSummary"
          class="bg-yellow-50 border border-yellow-200 rounded-lg p-4"
        >
          <h4 class="text-sm font-semibold text-yellow-800 mb-2">Required Fields Summary:</h4>
          <ul class="text-sm text-yellow-700 space-y-1">
            <li>• Head of IT: Name, Signature, Date</li>
            <li>• ICT Officer: Name, Signature, Date</li>
            <li class="text-xs text-yellow-600 mt-2">
              All fields marked with * are required before finalizing.
            </li>
          </ul>
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
            :disabled="isSubmitting || !isFormValid"
            class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 disabled:opacity-50"
          >
            {{ isSubmitting ? 'Submitting...' : 'Submit Implementation Workflow' }}
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
            {
              formData: formDataForDebug,
              isFormValid,
              userAccessRequests: userAccessRequests.length
            },
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
    name: 'ImplementationWorkflowForm',
    data() {
      return {
        formData: {
          userAccessId: '',
          headItName: '',
          headItDate: '',
          headItSignature: null,
          headItComments: '',
          ictOfficerName: '',
          ictOfficerDate: '',
          ictOfficerSignature: null,
          ictOfficerComments: '',
          implementationComments: ''
        },
        userAccessRequests: [],
        errors: {},
        isSubmitting: false,
        successMessage: '',
        errorMessage: '',
        showDebug: true, // Set to false in production
        showValidationSummary: true
      }
    },
    computed: {
      isFormValid() {
        return (
          this.formData.userAccessId &&
          this.formData.headItName &&
          this.formData.headItDate &&
          this.formData.headItSignature &&
          this.formData.ictOfficerName &&
          this.formData.ictOfficerDate &&
          this.formData.ictOfficerSignature
        )
      },
      formDataForDebug() {
        // Return form data without file objects for debug display
        const debug = { ...this.formData }
        debug.headItSignature = this.formData.headItSignature ? 'File selected' : null
        debug.ictOfficerSignature = this.formData.ictOfficerSignature ? 'File selected' : null
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
            { id: 1, staff_name: 'Test User', pf_number: 'PF001', status: 'ict_director_approved' },
            { id: 2, staff_name: 'Another User', pf_number: 'PF002', status: 'pending_head_it' }
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

        if (!this.formData.userAccessId) {
          this.errors.userAccessId = 'Please select a user access request'
        }

        if (!this.formData.headItName) {
          this.errors.headItName = 'Head of IT name is required'
        }

        if (!this.formData.headItDate) {
          this.errors.headItDate = 'Head of IT approval date is required'
        }

        if (!this.formData.headItSignature) {
          this.errors.headItSignature = 'Head of IT signature is required'
        }

        if (!this.formData.ictOfficerName) {
          this.errors.ictOfficerName = 'ICT Officer name is required'
        }

        if (!this.formData.ictOfficerDate) {
          this.errors.ictOfficerDate = 'ICT Officer implementation date is required'
        }

        if (!this.formData.ictOfficerSignature) {
          this.errors.ictOfficerSignature = 'ICT Officer signature is required'
        }

        return Object.keys(this.errors).length === 0
      },

      async submitForm() {
        if (!this.validateForm()) {
          this.errorMessage = 'Please fill in all required fields before submitting.'
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

          // Add Head of IT data
          formData.append('head_it_name', this.formData.headItName)
          formData.append('head_it_date', this.formData.headItDate)
          formData.append('head_it_signature', this.formData.headItSignature)
          if (this.formData.headItComments) {
            formData.append('head_it_comments', this.formData.headItComments)
          }

          // Add ICT Officer data
          formData.append('ict_officer_name', this.formData.ictOfficerName)
          formData.append('ict_officer_date', this.formData.ictOfficerDate)
          formData.append('ict_officer_signature', this.formData.ictOfficerSignature)
          if (this.formData.ictOfficerComments) {
            formData.append('ict_officer_comments', this.formData.ictOfficerComments)
          }
          if (this.formData.implementationComments) {
            formData.append('implementation_comments', this.formData.implementationComments)
          }

          console.log('Submitting implementation workflow...')

          const response = await axios.post('/api/implementation-workflow', formData, {
            headers: {
              'Content-Type': 'multipart/form-data'
            }
          })

          console.log('Implementation workflow response:', response.data)

          if (response.data.status === 'success') {
            this.successMessage = response.data.message
            this.resetForm()
          } else {
            this.errorMessage = response.data.message || 'Failed to submit implementation workflow'
          }
        } catch (error) {
          console.error('Error submitting implementation workflow:', error)

          if (error.response && error.response.data) {
            if (error.response.data.errors) {
              this.errors = error.response.data.errors
            }
            this.errorMessage =
              error.response.data.message || 'Failed to submit implementation workflow'
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
          headItName: '',
          headItDate: '',
          headItSignature: null,
          headItComments: '',
          ictOfficerName: '',
          ictOfficerDate: '',
          ictOfficerSignature: null,
          ictOfficerComments: '',
          implementationComments: ''
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
  .implementation-workflow-form {
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

  .space-y-1 > * + * {
    margin-top: 0.25rem;
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
