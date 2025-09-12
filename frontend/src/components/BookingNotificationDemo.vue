<template>
  <div
    class="booking-notification-demo p-6 bg-gradient-to-br from-blue-900 via-blue-800 to-blue-700 min-h-screen"
  >
    <div class="max-w-4xl mx-auto">
      <!-- Header -->
      <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-white mb-2 drop-shadow-lg">
          Booking Notification System Demo
        </h1>
        <p class="text-blue-200 text-lg">
          Attractive Small-Sized Card Notifications for Device Management
        </p>
      </div>

      <!-- Demo Controls -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
        <!-- Device Condition Assessment Saved -->
        <div
          class="demo-card bg-gradient-to-r from-cyan-600/20 to-cyan-700/20 border border-cyan-400/30 p-4 rounded-xl backdrop-blur-sm"
        >
          <h3 class="text-lg font-bold text-white mb-3 flex items-center">
            <i class="fas fa-clipboard-check mr-2 text-cyan-300"></i>
            Condition Assessment
          </h3>
          <button
            @click="showConditionSaved"
            class="w-full px-4 py-2 bg-gradient-to-r from-cyan-500 to-cyan-600 text-white rounded-lg font-medium hover:from-cyan-600 hover:to-cyan-700 transition-all duration-300 flex items-center justify-center space-x-2"
          >
            <i class="fas fa-save"></i>
            <span>Assessment Saved</span>
          </button>
        </div>

        <!-- Device Ready for Issue -->
        <div
          class="demo-card bg-gradient-to-r from-emerald-600/20 to-emerald-700/20 border border-emerald-400/30 p-4 rounded-xl backdrop-blur-sm"
        >
          <h3 class="text-lg font-bold text-white mb-3 flex items-center">
            <i class="fas fa-hand-holding mr-2 text-emerald-300"></i>
            Ready for Issue
          </h3>
          <button
            @click="showDeviceReady"
            class="w-full px-4 py-2 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-lg font-medium hover:from-emerald-600 hover:to-emerald-700 transition-all duration-300 flex items-center justify-center space-x-2"
          >
            <i class="fas fa-check-circle"></i>
            <span>Device Ready</span>
          </button>
        </div>

        <!-- Device Issued -->
        <div
          class="demo-card bg-gradient-to-r from-purple-600/20 to-purple-700/20 border border-purple-400/30 p-4 rounded-xl backdrop-blur-sm"
        >
          <h3 class="text-lg font-bold text-white mb-3 flex items-center">
            <i class="fas fa-handshake mr-2 text-purple-300"></i>
            Device Issued
          </h3>
          <button
            @click="showDeviceIssued"
            class="w-full px-4 py-2 bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-lg font-medium hover:from-purple-600 hover:to-purple-700 transition-all duration-300 flex items-center justify-center space-x-2"
          >
            <i class="fas fa-user-check"></i>
            <span>Device Issued</span>
          </button>
        </div>

        <!-- Condition Updated -->
        <div
          class="demo-card bg-gradient-to-r from-blue-600/20 to-blue-700/20 border border-blue-400/30 p-4 rounded-xl backdrop-blur-sm"
        >
          <h3 class="text-lg font-bold text-white mb-3 flex items-center">
            <i class="fas fa-edit mr-2 text-blue-300"></i>
            Condition Updated
          </h3>
          <button
            @click="showConditionUpdated"
            class="w-full px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg font-medium hover:from-blue-600 hover:to-blue-700 transition-all duration-300 flex items-center justify-center space-x-2"
          >
            <i class="fas fa-sync-alt"></i>
            <span>Update Condition</span>
          </button>
        </div>

        <!-- Return Assessment -->
        <div
          class="demo-card bg-gradient-to-r from-orange-600/20 to-orange-700/20 border border-orange-400/30 p-4 rounded-xl backdrop-blur-sm"
        >
          <h3 class="text-lg font-bold text-white mb-3 flex items-center">
            <i class="fas fa-undo mr-2 text-orange-300"></i>
            Return Assessment
          </h3>
          <button
            @click="showReturnAssessment"
            class="w-full px-4 py-2 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-lg font-medium hover:from-orange-600 hover:to-orange-700 transition-all duration-300 flex items-center justify-center space-x-2"
          >
            <i class="fas fa-clipboard-list"></i>
            <span>Return Assessment</span>
          </button>
        </div>

        <!-- Clear All -->
        <div
          class="demo-card bg-gradient-to-r from-gray-600/20 to-gray-700/20 border border-gray-400/30 p-4 rounded-xl backdrop-blur-sm"
        >
          <h3 class="text-lg font-bold text-white mb-3 flex items-center">
            <i class="fas fa-trash mr-2 text-gray-300"></i>
            Clear Notifications
          </h3>
          <button
            @click="clearAllNotifications"
            class="w-full px-4 py-2 bg-gradient-to-r from-gray-500 to-gray-600 text-white rounded-lg font-medium hover:from-gray-600 hover:to-gray-700 transition-all duration-300 flex items-center justify-center space-x-2"
          >
            <i class="fas fa-broom"></i>
            <span>Clear All</span>
          </button>
        </div>
      </div>

      <!-- Features Section -->
      <div
        class="demo-card bg-gradient-to-r from-indigo-600/20 to-indigo-700/20 border border-indigo-400/30 p-6 rounded-xl backdrop-blur-sm"
      >
        <h2 class="text-xl font-bold text-white mb-4 flex items-center">
          <i class="fas fa-star mr-2 text-indigo-300"></i>
          Notification Features
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="feature-item">
            <h4 class="text-white font-semibold mb-2 flex items-center">
              <i class="fas fa-compress-alt mr-2 text-indigo-300 text-sm"></i>
              Compact Design
            </h4>
            <p class="text-blue-200 text-sm">
              Small, attractive cards that don't obstruct the interface while providing essential
              information.
            </p>
          </div>
          <div class="feature-item">
            <h4 class="text-white font-semibold mb-2 flex items-center">
              <i class="fas fa-mouse-pointer mr-2 text-indigo-300 text-sm"></i>
              Interactive Actions
            </h4>
            <p class="text-blue-200 text-sm">
              Quick action buttons for immediate workflow continuation without navigation delays.
            </p>
          </div>
          <div class="feature-item">
            <h4 class="text-white font-semibold mb-2 flex items-center">
              <i class="fas fa-palette mr-2 text-indigo-300 text-sm"></i>
              Color-Coded Types
            </h4>
            <p class="text-blue-200 text-sm">
              Different colors for different notification types: success (green), booking (cyan),
              approval (purple).
            </p>
          </div>
          <div class="feature-item">
            <h4 class="text-white font-semibold mb-2 flex items-center">
              <i class="fas fa-clock mr-2 text-indigo-300 text-sm"></i>
              Auto-Dismiss
            </h4>
            <p class="text-blue-200 text-sm">
              Notifications automatically disappear after a set duration with visual progress
              indicators.
            </p>
          </div>
        </div>
      </div>

      <!-- Usage Instructions -->
      <div
        class="mt-8 demo-card bg-gradient-to-r from-teal-600/20 to-teal-700/20 border border-teal-400/30 p-6 rounded-xl backdrop-blur-sm"
      >
        <h2 class="text-xl font-bold text-white mb-4 flex items-center">
          <i class="fas fa-code mr-2 text-teal-300"></i>
          Usage Example
        </h2>
        <div class="bg-gray-900/50 rounded-lg p-4 border border-gray-600/30">
          <pre class="text-green-300 text-sm overflow-x-auto"><code>// Import the notification store
import { useNotificationStore } from '@/stores/notification'

// In your component
const notificationStore = useNotificationStore()

// Show device condition assessment saved notification
notificationStore.showDeviceConditionSaved('Laptop Dell Inspiron', '12345')

// Show device ready for issue notification  
notificationStore.showDeviceReadyForIssue('Laptop Dell Inspiron', '12345')

// Show device issued notification
notificationStore.showDeviceIssued('Laptop Dell Inspiron', 'John Doe', '12345')</code></pre>
        </div>
      </div>
    </div>

    <!-- Compact Notification System -->
    <CompactNotificationSystem />
  </div>
</template>

<script>
  import { useNotificationStore } from '@/stores/notification'
  import CompactNotificationSystem from './CompactNotificationSystem.vue'

  export default {
    name: 'BookingNotificationDemo',
    components: {
      CompactNotificationSystem
    },
    setup() {
      const notificationStore = useNotificationStore()

      const showConditionSaved = () => {
        notificationStore.showDeviceConditionSaved('Laptop Dell Inspiron 15', '12345')
      }

      const showDeviceReady = () => {
        notificationStore.showDeviceReadyForIssue('Projector Epson EB-X41', '12346')
      }

      const showDeviceIssued = () => {
        notificationStore.showDeviceIssued(
          'Tablet Samsung Galaxy Tab',
          'Dr. Sarah Johnson',
          '12347'
        )
      }

      const showConditionUpdated = () => {
        notificationStore.showDeviceConditionUpdated('Desktop HP EliteDesk', '12348')
      }

      const showReturnAssessment = () => {
        notificationStore.showDeviceReturnAssessment('Camera Canon EOS', '12349', 'good')
      }

      const clearAllNotifications = () => {
        notificationStore.clearAllNotifications()
      }

      return {
        showConditionSaved,
        showDeviceReady,
        showDeviceIssued,
        showConditionUpdated,
        showReturnAssessment,
        clearAllNotifications
      }
    }
  }
</script>

<style scoped>
  .demo-card {
    transition: all 0.3s ease;
  }

  .demo-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
  }

  .feature-item {
    padding: 1rem;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 0.5rem;
    border: 1px solid rgba(255, 255, 255, 0.1);
  }

  pre {
    white-space: pre-wrap;
    word-wrap: break-word;
  }

  code {
    font-family: 'Fira Code', 'Monaco', 'Consolas', monospace;
    line-height: 1.5;
  }
</style>
"
