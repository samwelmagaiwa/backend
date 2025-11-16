<template>
  <div class="flex flex-col h-screen">
    <AppHeader />
    <div class="flex flex-1 overflow-hidden">
      <ModernSidebar />
      <main class="flex-1 p-6 bg-gray-100 overflow-y-auto">
        <div class="container-responsive">
          <div class="bg-white rounded-xl shadow-md p-6">
            <div
              class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-6"
            >
              <h1 class="text-2xl sm:text-3xl font-bold text-primary">Settings</h1>
            </div>

            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
              <div class="flex items-center">
                <i class="fas fa-cog text-green-600 text-2xl mr-3"></i>
                <div>
                  <h3 class="text-lg font-semibold text-green-800">System Settings</h3>
                  <p class="text-green-600">
                    Configure system preferences and application settings here.
                  </p>
                </div>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h4 class="font-semibold text-gray-800 mb-4 flex items-center">
                  <i class="fas fa-user-cog mr-2 text-blue-600"></i>
                  User Preferences
                </h4>
                <div class="space-y-3">
                  <div class="flex items-center justify-between">
                    <span class="text-gray-700">Email Notifications</span>
                    <input type="checkbox" checked class="form-checkbox h-4 w-4 text-blue-600" />
                  </div>
                  <div class="flex items-center justify-between">
                    <span class="text-gray-700">Dark Mode</span>
                    <input type="checkbox" class="form-checkbox h-4 w-4 text-blue-600" />
                  </div>
                  <div class="flex items-center justify-between">
                    <span class="text-gray-700">Auto Save</span>
                    <input type="checkbox" checked class="form-checkbox h-4 w-4 text-blue-600" />
                  </div>
                </div>
              </div>

              <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h4 class="font-semibold text-gray-800 mb-4 flex items-center">
                  <i class="fas fa-shield-halved mr-2 text-green-600"></i>
                  Security Settings
                </h4>
                <div class="space-y-3">
                  <div class="flex items-center justify-between">
                    <span class="text-gray-700">Two-Factor Auth</span>
                    <input type="checkbox" class="form-checkbox h-4 w-4 text-green-600" />
                  </div>
                  <div class="flex items-center justify-between">
                    <span class="text-gray-700">Session Timeout</span>
                    <select class="border border-gray-300 rounded px-2 py-1 text-sm">
                      <option>30 minutes</option>
                      <option>1 hour</option>
                      <option>2 hours</option>
                    </select>
                  </div>
                  <div class="flex items-center justify-between">
                    <span class="text-gray-700">Login Alerts</span>
                    <input type="checkbox" checked class="form-checkbox h-4 w-4 text-green-600" />
                  </div>
                </div>
              </div>
            </div>

            <!-- Footer -->
            <AppFooter />
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script>
  import ModernSidebar from './ModernSidebar.vue'
  import AppFooter from './footer.vue'
  import AppHeader from './AppHeader.vue'

  export default {
    name: 'SettingsPage',
    components: {
      ModernSidebar,
      AppFooter,
      AppHeader
    },
    setup() {
      // Sidebar state now managed by Pinia - no local state needed

      return {
        // No local state needed for sidebar
      }
    }
  }
</script>

<style scoped>
  .text-primary {
    color: #1e40af;
  }
</style>
