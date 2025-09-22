<template>
  <div class="jeeva-module-request-form">
    <div class="bg-white shadow-lg rounded-lg p-6">
      <h2 class="text-2xl font-bold text-gray-800 mb-6">Jeeva Module Request Form</h2>

      <!-- Form -->
      <form @submit.prevent="submitForm" class="space-y-6">
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

        <!-- Jeeva Modules (Checkboxes) -->
        <div>
          <label class="block text-base font-medium text-gray-700 mb-2"> Jeeva Modules </label>
          <div
            class="grid grid-cols-3 gap-2 max-h-96 overflow-y-auto border border-gray-200 rounded-md p-4"
          >
            <label v-for="module in availableModules" :key="module.id" class="flex items-center">
              <input
                type="checkbox"
                :value="module.name"
                v-model="formData.jeevaModules"
                class="mr-2"
              />
              <span class="text-sm">{{ module.name }}</span>
            </label>
          </div>
          <p v-if="errors.jeevaModules" class="text-red-500 text-sm mt-1">
            {{ errors.jeevaModules }}
          </p>
          <p class="text-gray-500 text-sm mt-1">
            Selected: {{ formData.jeevaModules.length }} module(s)
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
            class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 disabled:opacity-50"
          >
            {{ isSubmitting ? 'Submitting...' : 'Submit Jeeva Request' }}
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
              formData,
              availableModules: availableModules.length,
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
    name: 'JeevaModuleRequestForm',
    data() {
      return {
        formData: {
          userAccessId: '',
          jeevaModules: []
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
          const response = await axios.get('/api/module-requests/jeeva/modules')
          this.availableModules = response.data.data || []
          console.log('Available Jeeva modules loaded:', this.availableModules.length)
        } catch (error) {
          console.error('Error loading available Jeeva modules:', error)
          this.errorMessage = 'Failed to load available Jeeva modules'
        }
      },

      async loadUserAccessRequests() {
        try {
          // This is a simplified approach - in a real app, you'd have a proper endpoint
          // For now, we'll create some mock data or use existing endpoints
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

      validateForm() {
        this.errors = {}

        if (this.formData.jeevaModules.length === 0) {
          this.errors.jeevaModules = 'Please select at least one Jeeva module'
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
            jeeva_modules: this.formData.jeevaModules
          }

          console.log('Submitting Jeeva module request:', payload)

          const response = await axios.post('/api/module-requests/jeeva', payload)

          console.log('Jeeva module request response:', response.data)

          if (response.data.status === 'success') {
            this.successMessage = response.data.message
            this.resetForm()
          } else {
            this.errorMessage = response.data.message || 'Failed to submit Jeeva module request'
          }
        } catch (error) {
          console.error('Error submitting Jeeva module request:', error)

          if (error.response && error.response.data) {
            if (error.response.data.errors) {
              this.errors = error.response.data.errors
            }
            this.errorMessage =
              error.response.data.message || 'Failed to submit Jeeva module request'
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
          jeevaModules: []
        }
        this.errors = {}
        this.successMessage = ''
        this.errorMessage = ''
      }
    }
  }
</script>

<style scoped>
  .jeeva-module-request-form {
    max-width: 1000px;
    margin: 0 auto;
    padding: 20px;
  }

  /* Additional styling for better UX */
  .grid {
    display: grid;
  }

  .grid-cols-3 {
    grid-template-columns: repeat(3, minmax(0, 1fr));
  }

  .gap-2 {
    gap: 0.5rem;
  }

  .space-y-6 > * + * {
    margin-top: 1.5rem;
  }

  .space-x-4 > * + * {
    margin-left: 1rem;
  }

  .max-h-96 {
    max-height: 24rem;
  }

  .overflow-y-auto {
    overflow-y: auto;
  }
</style>
