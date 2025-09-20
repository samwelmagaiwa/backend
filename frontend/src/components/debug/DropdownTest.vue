<template>
  <div class="p-8 bg-blue-900 min-h-screen">
    <h1 class="text-white text-2xl mb-8">Dropdown Test Component</h1>

    <!-- Debug Info -->
    <div class="bg-yellow-100 text-yellow-800 p-4 rounded mb-4">
      <p>Active Dropdown: {{ activeDropdown || 'None' }}</p>
      <p>Test Requests Count: {{ testRequests.length }}</p>
    </div>

    <!-- Test Table -->
    <div class="bg-white/10 rounded-lg overflow-hidden">
      <table class="w-full">
        <thead class="bg-blue-800/50">
          <tr>
            <th class="px-4 py-3 text-left text-blue-100">Request ID</th>
            <th class="px-4 py-3 text-left text-blue-100">Staff Name</th>
            <th class="px-4 py-3 text-center text-blue-100">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="request in testRequests"
            :key="request.id"
            class="border-t border-blue-300/20 hover:bg-blue-700/30"
          >
            <td class="px-4 py-3 text-white">{{ request.id }}</td>
            <td class="px-4 py-3 text-white">{{ request.name }}</td>
            <td class="px-4 py-3 text-center relative">
              <div class="relative inline-block text-left">
                <!-- Three dots button -->
                <button
                  @click.stop="toggleDropdown(request.id)"
                  class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-600/20 hover:bg-blue-600/40 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500/50"
                >
                  <div class="flex flex-col space-y-0.5">
                    <div class="w-1 h-1 bg-blue-100 rounded-full"></div>
                    <div class="w-1 h-1 bg-blue-100 rounded-full"></div>
                    <div class="w-1 h-1 bg-blue-100 rounded-full"></div>
                  </div>
                </button>

                <!-- Dropdown menu -->
                <div
                  v-show="activeDropdown === request.id"
                  class="absolute right-0 z-[9999] mt-2 w-48 origin-top-right bg-white rounded-lg shadow-xl border border-gray-200 focus:outline-none"
                  style="position: absolute; top: 100%; right: 0"
                  @click.stop
                >
                  <div class="py-1">
                    <button
                      @click="handleAction('view', request.id)"
                      class="group flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150"
                    >
                      <i class="fas fa-eye mr-3 text-blue-500 group-hover:text-blue-600"></i>
                      View & Process
                    </button>

                    <button
                      @click="handleAction('edit', request.id)"
                      class="group flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-amber-50 hover:text-amber-600 transition-colors duration-150"
                    >
                      <i class="fas fa-edit mr-3 text-amber-500 group-hover:text-amber-600"></i>
                      Edit
                    </button>

                    <div class="border-t border-gray-100 my-1"></div>

                    <button
                      @click="handleAction('cancel', request.id)"
                      class="group flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-red-600 transition-colors duration-150"
                    >
                      <i class="fas fa-ban mr-3 text-red-500 group-hover:text-red-600"></i>
                      Cancel
                    </button>
                  </div>
                </div>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Action Log -->
    <div class="mt-8 bg-green-100 text-green-800 p-4 rounded">
      <h3 class="font-bold mb-2">Action Log:</h3>
      <div v-if="actionLog.length === 0">No actions performed yet</div>
      <div v-for="(action, index) in actionLog" :key="index" class="mb-1">
        {{ action }}
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    name: 'DropdownTest',
    data() {
      return {
        activeDropdown: null,
        testRequests: [
          { id: 1, name: 'John Doe' },
          { id: 2, name: 'Jane Smith' },
          { id: 3, name: 'Bob Johnson' }
        ],
        actionLog: []
      }
    },
    mounted() {
      // Add click listener to close dropdowns when clicking outside
      document.addEventListener('click', this.closeDropdowns)
    },
    beforeUnmount() {
      // Clean up the click listener
      document.removeEventListener('click', this.closeDropdowns)
    },
    methods: {
      toggleDropdown(requestId) {
        console.log('Toggle dropdown for request:', requestId)
        console.log('Current activeDropdown:', this.activeDropdown)

        if (this.activeDropdown === requestId) {
          this.activeDropdown = null
          console.log('Closing dropdown')
        } else {
          this.activeDropdown = requestId
          console.log('Opening dropdown for:', requestId)
        }

        this.actionLog.push(`Toggled dropdown for request ${requestId}`)
      },

      closeDropdowns(event) {
        // Only close if clicking outside the dropdown area
        if (!event || !event.target.closest('.relative')) {
          this.activeDropdown = null
        }
      },

      handleAction(action, requestId) {
        this.closeDropdowns()
        this.actionLog.push(`Performed ${action} on request ${requestId}`)
        alert(`${action.charAt(0).toUpperCase() + action.slice(1)} action for request ${requestId}`)
      }
    }
  }
</script>

<style scoped>
  /* Add any specific styles if needed */
</style>
