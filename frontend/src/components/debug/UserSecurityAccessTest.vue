<template>
  <div class="min-h-screen bg-gray-100 p-8">
    <div class="max-w-2xl mx-auto">
      <div class="bg-white rounded-lg shadow-lg p-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">UserSecurityAccess Test Page</h1>

        <div class="mb-6">
          <h2 class="text-lg font-semibold text-gray-700 mb-4">Test Navigation</h2>

          <div class="space-y-4">
            <!-- Test Request ID Input -->
            <div>
              <label for="requestId" class="block text-sm font-medium text-gray-700 mb-2">
                Request ID for Testing
              </label>
              <input
                id="requestId"
                v-model="testRequestId"
                type="number"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Enter a request ID (e.g., 1, 2, 3)"
              />
            </div>

            <!-- ICT Officer View -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
              <h3 class="text-md font-semibold text-blue-800 mb-2">ICT Officer View</h3>
              <p class="text-sm text-blue-600 mb-3">
                Test the ICT Officer interface - can edit comments and grant access
              </p>
              <button
                @click="navigateAsIctOfficer"
                :disabled="!testRequestId"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Open as ICT Officer
              </button>
            </div>

            <!-- Staff/Requester View -->
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
              <h3 class="text-md font-semibold text-green-800 mb-2">Staff/Requester View</h3>
              <p class="text-sm text-green-600 mb-3">
                Test the read-only interface for staff members
              </p>
              <button
                @click="navigateAsStaff"
                :disabled="!testRequestId"
                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Open as Staff Member
              </button>
            </div>
          </div>
        </div>

        <!-- Direct URL Examples -->
        <div class="mb-6">
          <h2 class="text-lg font-semibold text-gray-700 mb-4">Direct URL Examples</h2>
          <div class="bg-gray-50 rounded-lg p-4 space-y-2">
            <div class="text-sm">
              <strong>ICT Officer:</strong>
              <code class="bg-white px-2 py-1 rounded text-xs"
                >/user-security-access/{{ testRequestId || 'ID' }}?role=ict_officer</code
              >
            </div>
            <div class="text-sm">
              <strong>Staff:</strong>
              <code class="bg-white px-2 py-1 rounded text-xs"
                >/user-security-access/{{ testRequestId || 'ID' }}?role=staff</code
              >
            </div>
          </div>
        </div>

        <!-- Both Service Form Test -->
        <div class="mb-6">
          <h2 class="text-lg font-semibold text-gray-700 mb-4">Both Service Form Test</h2>
          <p class="text-sm text-gray-600 mb-3">
            Test the approval flow from both-service-form to UserSecurityAccess
          </p>
          <button
            @click="navigateToBothServiceForm"
            :disabled="!testRequestId"
            class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Open Both Service Form
          </button>
        </div>

        <!-- Information -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
          <h3 class="text-md font-semibold text-yellow-800 mb-2">Information</h3>
          <ul class="text-sm text-yellow-700 space-y-1">
            <li>• Enter a request ID to test the UserSecurityAccess page</li>
            <li>• ICT Officer view shows editable comments and "Grant Access Now" button</li>
            <li>
              • Staff view shows read-only interface with success message if access is granted
            </li>
            <li>• Check browser console for API calls and debugging information</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    name: 'UserSecurityAccessTest',
    data() {
      return {
        testRequestId: 1
      }
    },
    methods: {
      navigateAsIctOfficer() {
        if (this.testRequestId) {
          this.$router.push({
            path: `/user-security-access/${this.testRequestId}`,
            query: { role: 'ict_officer', debug: 'true' }
          })
        }
      },

      navigateAsStaff() {
        if (this.testRequestId) {
          this.$router.push({
            path: `/user-security-access/${this.testRequestId}`,
            query: { role: 'staff', debug: 'true' }
          })
        }
      },

      navigateToBothServiceForm() {
        if (this.testRequestId) {
          this.$router.push({
            path: `/both-service-form/${this.testRequestId}`
          })
        }
      }
    }
  }
</script>
