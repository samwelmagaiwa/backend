<template>
  <div class="module-request-form">
    <div class="bg-white shadow-lg rounded-lg p-6">
      <h2 class="text-2xl font-bold text-gray-800 mb-6">Module Request Form</h2>

      <!-- Form -->
      <form @submit.prevent="submitForm" class="space-y-6">
        <!-- User Access ID Selection -->
        <div>
          <label for="userAccessId" class="block text-sm font-medium text-gray-700 mb-2">
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

        <!-- Module Requested For (Radio Button) -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2"> Module Requested For </label>
          <div class="space-y-2">
            <label class="flex items-center">
              <input
                type="radio"
                v-model="formData.moduleRequestedFor"
                value="use"
                class="mr-2"
                required
              />
              <span>Use</span>
            </label>
            <label class="flex items-center">
              <input
                type="radio"
                v-model="formData.moduleRequestedFor"
                value="revoke"
                class="mr-2"
                required
              />
              <span>Revoke</span>
            </label>
          </div>
        </div>

        <!-- Wellsoft Modules (Checkboxes) -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2"> Wellsoft Modules </label>
          <div class="grid grid-cols-2 gap-2">
            <label v-for="module in availableModules" :key="module.id" class="flex items-center">
              <input
                type="checkbox"
                :value="module.name"
                v-model="formData.wellsoftModules"
                class="mr-2"
              />
              <span class="text-sm">{{ module.name }}</span>
            </label>
          </div>
          <p v-if="errors.wellsoftModules" class="text-red-500 text-sm mt-1">
            {{ errors.wellsoftModules }}
          </p>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end space-x-4">
          <button
            type="button"
            @click="resetForm"
            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
          >
            Reset
          </button>
          <button
            type="submit"
            :disabled="isSubmitting"
            class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50"
          >
            {{ isSubmitting ? 'Submitting...' : 'Submit Request' }}
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
          JSON.stringify({ formData, availableModules, userAccessRequests }, null, 2)
        }}</pre>
      </div>
    </div>
  </div>
</template>

<script>
  import axios from 'axios'

  export default {
    name: 'ModuleRequestForm',
    data() {
      return {
        formData: {
          userAccessId: '',
          moduleRequestedFor: '',
          wellsoftModules: []
        },
        availableModules: [],
        userAccessRequests: [],
        errors: {},
        isSubmitting: false,
        successMessage: '',
        errorMessage: '',
        showDebug: true // Set to false in production
      }
    },
    async mounted() {
      await this.loadAvailableModules()
      await this.loadUserAccessRequests()
    },
    methods: {
      async loadAvailableModules() {
        try {
          const response = await axios.get('/api/module-requests/modules')
          this.availableModules = response.data.data || []
          console.log('Available modules loaded:', this.availableModules)
        } catch (error) {
          console.error('Error loading available modules:', error)
          this.errorMessage = 'Failed to load available modules'
        }
      },

      async loadUserAccessRequests() {
        try {
          // This is a simplified approach - in a real app, you'd have a proper endpoint
          // For now, we'll create some mock data or use existing endpoints
          const response = await axios.get('/api/v1/user-access')
          this.userAccessRequests = response.data.data || []
          console.log('User access requests loaded:', this.userAccessRequests)
        } catch (error) {
          console.error('Error loading user access requests:', error)
          // Create mock data for testing
          this.userAccessRequests = [
            { id: 1, staff_name: 'Test User', pf_number: 'PF001', status: 'pending' },
            { id: 2, staff_name: 'Another User', pf_number: 'PF002', status: 'approved' }
          ]
        }
      },

      validateForm() {
        this.errors = {}

        if (!this.formData.moduleRequestedFor) {
          this.errors.moduleRequestedFor = 'Please select whether you want to use or revoke modules'
        }

        if (this.formData.wellsoftModules.length === 0) {
          this.errors.wellsoftModules = 'Please select at least one Wellsoft module'
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
          const payload = {
            user_access_id: this.formData.userAccessId,
            module_requested_for: this.formData.moduleRequestedFor,
            wellsoft_modules: this.formData.wellsoftModules
          }

          console.log('Submitting module request:', payload)

          const response = await axios.post('/api/module-requests', payload)

          console.log('Module request response:', response.data)

          if (response.data.status === 'success') {
            this.successMessage = response.data.message
            this.resetForm()
          } else {
            this.errorMessage = response.data.message || 'Failed to submit module request'
          }
        } catch (error) {
          console.error('Error submitting module request:', error)

          if (error.response && error.response.data) {
            if (error.response.data.errors) {
              this.errors = error.response.data.errors
            }
            this.errorMessage = error.response.data.message || 'Failed to submit module request'
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
          moduleRequestedFor: '',
          wellsoftModules: []
        }
        this.errors = {}
        this.successMessage = ''
        this.errorMessage = ''
      }
    }
  }
</script>

<style scoped>
  .module-request-form {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
  }

  /* Additional styling for better UX */
  .grid {
    display: grid;
  }

  .grid-cols-2 {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }

  .gap-2 {
    gap: 0.5rem;
  }

  .space-y-2 > * + * {
    margin-top: 0.5rem;
  }

  .space-y-6 > * + * {
    margin-top: 1.5rem;
  }

  .space-x-4 > * + * {
    margin-left: 1rem;
  }
</style>
