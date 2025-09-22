<template>
  <div class="flex flex-col h-screen">
    <Header />
    <div class="flex flex-1 overflow-hidden">
      <ModernSidebar />
      <main
        class="flex-1 p-6 bg-gradient-to-br from-blue-900 via-blue-800 to-teal-900 overflow-y-auto relative"
      >
        <div class="max-w-5xl mx-auto">
          <!-- Header Section -->
          <div class="medical-glass-card rounded-t-3xl p-8 mb-0 border-b border-blue-300/30">
            <div class="flex justify-between items-center">
              <!-- Center Content -->
              <div class="text-center flex-1">
                <h1 class="text-2xl font-bold text-white mb-4 tracking-wide drop-shadow-lg">
                  MUHIMBILI NATIONAL HOSPITAL
                </h1>
                <div
                  class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-2 rounded-full text-base font-bold shadow-2xl"
                >
                  <span class="relative z-10 flex items-center gap-3">
                    <i class="fas fa-clipboard-list text-base"></i>
                    REQUEST DETAILS
                  </span>
                </div>
                <h2 class="text-lg font-bold text-blue-100 tracking-wide drop-shadow-md mt-4">
                  Internal Access Management
                </h2>
              </div>
            </div>
          </div>

          <!-- Main Content -->
          <div class="medical-glass-card rounded-b-3xl overflow-hidden">
            <div class="p-6 space-y-8">
              <!-- Test Message -->
              <div class="text-center py-8">
                <h2 class="text-white text-xl">Request Details Test Page</h2>
                <p class="text-blue-300">ID: {{ requestId }}, Type: {{ requestType }}</p>
              </div>

              <!-- Approval Trail Test -->
              <div class="space-y-4">
                <!-- Section Header -->
                <div class="flex items-center space-x-3">
                  <div
                    class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-md"
                  >
                    <i class="fas fa-route text-white text-sm"></i>
                  </div>
                  <h3 class="text-lg font-bold text-white">Approval Trail</h3>
                </div>

                <!-- Test Compact Cards -->
                <div>
                  <!-- ICT Officer Status Card -->
                  <div
                    class="bg-gradient-to-r from-blue-600/20 to-blue-700/20 border border-blue-400/40 rounded-lg p-4 backdrop-blur-sm hover:shadow-lg transition-all duration-300"
                  >
                    <div class="flex items-center justify-between">
                      <div class="flex items-center space-x-3">
                        <div
                          class="w-10 h-10 bg-gradient-to-br from-blue-500 to-red-600 rounded-lg flex items-center justify-center shadow-md"
                        >
                          <i class="fas fa-times-circle text-white text-sm"></i>
                        </div>
                        <div>
                          <h4 class="text-white font-semibold text-sm">ICT Officer</h4>
                          <p class="text-red-400 text-xs font-medium">Rejected</p>
                        </div>
                      </div>
                      <div class="text-right">
                        <p class="text-sm text-blue-300">Sep 9, 2025</p>
                      </div>
                    </div>
                  </div>

                  <!-- ICT Officer Comments Card -->
                  <div class="mt-3">
                    <div
                      class="bg-gradient-to-br from-blue-600/25 to-red-600/25 border border-blue-400/60 shadow-blue-500/25 rounded-lg p-4 backdrop-blur-sm border transition-all duration-300 hover:shadow-lg"
                    >
                      <!-- Compact Header -->
                      <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                          <div
                            class="w-8 h-8 bg-gradient-to-br from-blue-500 to-red-600 rounded-lg flex items-center justify-center shadow-sm"
                          >
                            <i class="fas fa-times-circle text-white text-sm"></i>
                          </div>
                          <div>
                            <h5 class="text-white font-semibold text-sm">ICT Comments</h5>
                            <p class="text-xs opacity-75 text-blue-200">Rejection Reason</p>
                          </div>
                        </div>
                        <div
                          class="bg-red-600 text-red-100 border border-blue-400/50 px-2 py-1 rounded text-sm font-semibold uppercase"
                        >
                          rejected
                        </div>
                      </div>

                      <!-- Comment Content -->
                      <div class="mb-3">
                        <p class="text-white text-base leading-relaxed">sitaki tu</p>
                      </div>

                      <!-- Footer -->
                      <div class="flex justify-between items-center text-xs text-blue-200">
                        <div class="flex items-center space-x-1">
                          <i class="fas fa-calendar-alt"></i>
                          <span>Sep 9, 2025, 03:00 AM</span>
                        </div>
                        <div class="flex items-center space-x-1">
                          <i class="fas fa-user"></i>
                          <span>ICT Dept.</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <AppFooter />
      </main>
    </div>
  </div>
</template>

<script>
  import { ref } from 'vue'
  import { useRoute } from 'vue-router'
  import Header from '@/components/header.vue'
  import ModernSidebar from '@/components/ModernSidebar.vue'
  import AppFooter from '@/components/footer.vue'

  export default {
    name: 'InternalAccessDetailsTest',
    components: {
      Header,
      ModernSidebar,
      AppFooter
    },
    setup() {
      const route = useRoute()

      const requestId = ref(route.query.id || 'N/A')
      const requestType = ref(route.query.type || 'N/A')

      return {
        requestId,
        requestType
      }
    }
  }
</script>

<style scoped>
  /* Glass morphism effects */
  .medical-glass-card {
    background: rgba(59, 130, 246, 0.15);
    backdrop-filter: blur(25px);
    -webkit-backdrop-filter: blur(25px);
    border: 2px solid rgba(96, 165, 250, 0.3);
    box-shadow:
      0 8px 32px rgba(29, 78, 216, 0.4),
      inset 0 1px 0 rgba(255, 255, 255, 0.1);
  }
</style>
