<template>
  <div class="notification-demo p-6 bg-gradient-to-br from-blue-900 via-blue-800 to-teal-900 min-h-screen">
    <div class="max-w-6xl mx-auto">
      <!-- Header -->
      <div class="medical-glass-card rounded-t-3xl p-8 mb-0 border-b border-blue-300/30">
        <div class="text-center">
          <h1 class="text-3xl font-bold text-white mb-4 tracking-wide drop-shadow-lg">
            <i class="fas fa-bell mr-3 text-blue-300"></i>
            Notification System Demo
          </h1>
          <p class="text-blue-100/80 text-lg">
            Comprehensive Hospital Management System Notifications
          </p>
        </div>
      </div>

      <!-- Demo Content -->
      <div class="medical-glass-card rounded-b-3xl overflow-hidden">
        <div class="p-8 space-y-8">
          <!-- Basic Notification Types -->
          <div class="notification-section">
            <h2 class="text-2xl font-bold text-white mb-6 flex items-center">
              <i class="fas fa-layer-group mr-3 text-blue-300"></i>
              Basic Notification Types
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
              <button
                @click="showSuccess('Operation Successful', 'Your request has been processed successfully.')"
                class="demo-button bg-emerald-500/20 hover:bg-emerald-500/30 border-emerald-400/30 text-emerald-300"
              >
                <i class="fas fa-check-circle mb-2"></i>
                <span>Success</span>
              </button>
              <button
                @click="showError('Operation Failed', 'An error occurred while processing your request.')"
                class="demo-button bg-red-500/20 hover:bg-red-500/30 border-red-400/30 text-red-300"
              >
                <i class="fas fa-exclamation-triangle mb-2"></i>
                <span>Error</span>
              </button>
              <button
                @click="showWarning('Warning Notice', 'Please review your input before proceeding.')"
                class="demo-button bg-yellow-500/20 hover:bg-yellow-500/30 border-yellow-400/30 text-yellow-300"
              >
                <i class="fas fa-exclamation-circle mb-2"></i>
                <span>Warning</span>
              </button>
              <button
                @click="showInfo('Information', 'Here is some important information for you.')"
                class="demo-button bg-blue-500/20 hover:bg-blue-500/30 border-blue-400/30 text-blue-300"
              >
                <i class="fas fa-info-circle mb-2"></i>
                <span>Info</span>
              </button>
            </div>
          </div>

          <!-- Hospital-Specific Notifications -->
          <div class="notification-section">
            <h2 class="text-2xl font-bold text-white mb-6 flex items-center">
              <i class="fas fa-hospital mr-3 text-blue-300"></i>
              Hospital Management Notifications
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
              <button
                @click="showBookingNotification('Device Ready', 'Your requested projector is ready for collection.')"
                class="demo-button bg-cyan-500/20 hover:bg-cyan-500/30 border-cyan-400/30 text-cyan-300"
              >
                <i class="fas fa-calendar-check mb-2"></i>
                <span>Booking</span>
              </button>
              <button
                @click="showAccessNotification('Access Granted', 'You now have access to Jeeva system.')"
                class="demo-button bg-indigo-500/20 hover:bg-indigo-500/30 border-indigo-400/30 text-indigo-300"
              >
                <i class="fas fa-key mb-2"></i>
                <span>Access</span>
              </button>
              <button
                @click="showApprovalNotification('Request Approved', 'Your access request has been approved by HOD.')"
                class="demo-button bg-purple-500/20 hover:bg-purple-500/30 border-purple-400/30 text-purple-300"
              >
                <i class="fas fa-clipboard-check mb-2"></i>
                <span>Approval</span>
              </button>
              <button
                @click="showSystemNotification('System Update', 'Wellsoft system will be updated tonight.')"
                class="demo-button bg-teal-500/20 hover:bg-teal-500/30 border-teal-400/30 text-teal-300"
              >
                <i class="fas fa-cog mb-2"></i>
                <span>System</span>
              </button>
              <button
                @click="showMedicalNotification('Emergency Alert', 'Critical system alert for medical staff.')"
                class="demo-button bg-blue-600/20 hover:bg-blue-600/30 border-blue-500/30 text-blue-300"
              >
                <i class="fas fa-hospital mb-2"></i>
                <span>Medical</span>
              </button>
            </div>
          </div>

          <!-- Predefined Hospital Notifications -->
          <div class="notification-section">
            <h2 class="text-2xl font-bold text-white mb-6 flex items-center">
              <i class="fas fa-clipboard-list mr-3 text-blue-300"></i>
              Predefined Hospital Notifications
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
              <button
                @click="showRequestSubmitted('Access Request', 'REQ-2025-001')"
                class="demo-button bg-green-500/20 hover:bg-green-500/30 border-green-400/30 text-green-300"
              >
                <i class="fas fa-paper-plane mb-2"></i>
                <span>Request Submitted</span>
              </button>
              <button
                @click="showDeviceReady('Projector', 'REQ-2025-002')"
                class="demo-button bg-cyan-500/20 hover:bg-cyan-500/30 border-cyan-400/30 text-cyan-300"
              >
                <i class="fas fa-calendar-check mb-2"></i>
                <span>Device Ready</span>
              </button>
              <button
                @click="showDeviceOverdue('Monitor', 'REQ-2025-003', 3)"
                class="demo-button bg-orange-500/20 hover:bg-orange-500/30 border-orange-400/30 text-orange-300"
              >
                <i class="fas fa-exclamation-triangle mb-2"></i>
                <span>Device Overdue</span>
              </button>
              <button
                @click="showAccessGranted('Wellsoft', 'Full Access')"
                class="demo-button bg-indigo-500/20 hover:bg-indigo-500/30 border-indigo-400/30 text-indigo-300"
              >
                <i class="fas fa-key mb-2"></i>
                <span>Access Granted</span>
              </button>
              <button
                @click="showOnboardingComplete()"
                class="demo-button bg-emerald-500/20 hover:bg-emerald-500/30 border-emerald-400/30 text-emerald-300"
              >
                <i class="fas fa-rocket mb-2"></i>
                <span>Onboarding Complete</span>
              </button>
              <button
                @click="showWelcomeMessage('Dr. John Doe')"
                class="demo-button bg-sky-500/20 hover:bg-sky-500/30 border-sky-400/30 text-sky-300"
              >
                <i class="fas fa-hand-wave mb-2"></i>
                <span>Welcome Message</span>
              </button>
            </div>
          </div>

          <!-- Advanced Notifications -->
          <div class="notification-section">
            <h2 class="text-2xl font-bold text-white mb-6 flex items-center">
              <i class="fas fa-cogs mr-3 text-blue-300"></i>
              Advanced System Notifications
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
              <button
                @click="showSecurityAlert('Suspicious Login', 'Multiple failed login attempts detected from unknown IP.')"
                class="demo-button bg-red-600/20 hover:bg-red-600/30 border-red-500/30 text-red-300"
              >
                <i class="fas fa-shield-alt mb-2"></i>
                <span>Security Alert</span>
              </button>
              <button
                @click="showMaintenanceMode('Jeeva System', '2 hours')"
                class="demo-button bg-orange-500/20 hover:bg-orange-500/30 border-orange-400/30 text-orange-300"
              >
                <i class="fas fa-tools mb-2"></i>
                <span>Maintenance</span>
              </button>
              <button
                @click="showSessionExpiring(5)"
                class="demo-button bg-rose-500/20 hover:bg-rose-500/30 border-rose-400/30 text-rose-300"
              >
                <i class="fas fa-clock mb-2"></i>
                <span>Session Expiring</span>
              </button>
              <button
                @click="showQuotaExceeded('Device Bookings', 5, 3)"
                class="demo-button bg-amber-500/20 hover:bg-amber-500/30 border-amber-400/30 text-amber-300"
              >
                <i class="fas fa-chart-pie mb-2"></i>
                <span>Quota Exceeded</span>
              </button>
            </div>
          </div>

          <!-- File and Form Notifications -->
          <div class="notification-section">
            <h2 class="text-2xl font-bold text-white mb-6 flex items-center">
              <i class="fas fa-file-alt mr-3 text-blue-300"></i>
              File & Form Notifications
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
              <button
                @click="showFileUploadSuccess('signature.png')"
                class="demo-button bg-green-500/20 hover:bg-green-500/30 border-green-400/30 text-green-300"
              >
                <i class="fas fa-upload mb-2"></i>
                <span>File Upload Success</span>
              </button>
              <button
                @click="showFileUploadError('document.pdf', 'File size too large')"
                class="demo-button bg-red-500/20 hover:bg-red-500/30 border-red-400/30 text-red-300"
              >
                <i class="fas fa-exclamation-triangle mb-2"></i>
                <span>File Upload Error</span>
              </button>
              <button
                @click="showSignatureRequired()"
                class="demo-button bg-yellow-500/20 hover:bg-yellow-500/30 border-yellow-400/30 text-yellow-300"
              >
                <i class="fas fa-signature mb-2"></i>
                <span>Signature Required</span>
              </button>
              <button
                @click="showFormSubmissionError('Access Request', ['PF Number is required', 'Invalid email format'])"
                class="demo-button bg-red-500/20 hover:bg-red-500/30 border-red-400/30 text-red-300"
              >
                <i class="fas fa-edit mb-2"></i>
                <span>Form Error</span>
              </button>
            </div>
          </div>

          <!-- Control Buttons -->
          <div class="notification-section">
            <h2 class="text-2xl font-bold text-white mb-6 flex items-center">
              <i class="fas fa-sliders-h mr-3 text-blue-300"></i>
              Notification Controls
            </h2>
            <div class="flex flex-wrap gap-4 justify-center">
              <button
                @click="clearAllNotifications"
                class="control-button bg-red-500/20 hover:bg-red-500/30 border-red-400/30 text-red-300"
              >
                <i class="fas fa-trash mr-2"></i>
                Clear All Notifications
              </button>
              <button
                @click="showAllTypes"
                class="control-button bg-blue-500/20 hover:bg-blue-500/30 border-blue-400/30 text-blue-300"
              >
                <i class="fas fa-layer-group mr-2"></i>
                Show All Types
              </button>
              <button
                @click="showRandomNotification"
                class="control-button bg-purple-500/20 hover:bg-purple-500/30 border-purple-400/30 text-purple-300"
              >
                <i class="fas fa-random mr-2"></i>
                Random Notification
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { useNotificationStore } from '@/stores/notification'

export default {
  name: 'NotificationDemo',
  setup() {
    const notificationStore = useNotificationStore()

    // Basic notification methods
    const showSuccess = (title, message) => notificationStore.showSuccess(title, message)
    const showError = (title, message) => notificationStore.showError(title, message)
    const showWarning = (title, message) => notificationStore.showWarning(title, message)
    const showInfo = (title, message) => notificationStore.showInfo(title, message)

    // Enhanced notification methods
    const showBookingNotification = (title, message) => notificationStore.showBookingNotification(title, message)
    const showAccessNotification = (title, message) => notificationStore.showAccessNotification(title, message)
    const showApprovalNotification = (title, message) => notificationStore.showApprovalNotification(title, message)
    const showSystemNotification = (title, message) => notificationStore.showSystemNotification(title, message)
    const showMedicalNotification = (title, message) => notificationStore.showMedicalNotification(title, message)

    // Predefined hospital notifications
    const showRequestSubmitted = (type, id) => notificationStore.showRequestSubmitted(type, id)
    const showDeviceReady = (device, id) => notificationStore.showDeviceReady(device, id)
    const showDeviceOverdue = (device, id, days) => notificationStore.showDeviceOverdue(device, id, days)
    const showAccessGranted = (system, level) => notificationStore.showAccessGranted(system, level)
    const showOnboardingComplete = () => notificationStore.showOnboardingComplete()
    const showWelcomeMessage = (name) => notificationStore.showWelcomeMessage(name)

    // Advanced notifications
    const showSecurityAlert = (type, details) => notificationStore.showSecurityAlert(type, details)
    const showMaintenanceMode = (system, time) => notificationStore.showMaintenanceMode(system, time)
    const showSessionExpiring = (minutes) => notificationStore.showSessionExpiring(minutes)
    const showQuotaExceeded = (type, current, limit) => notificationStore.showQuotaExceeded(type, current, limit)

    // File and form notifications
    const showFileUploadSuccess = (fileName) => notificationStore.showFileUploadSuccess(fileName)
    const showFileUploadError = (fileName, reason) => notificationStore.showFileUploadError(fileName, reason)
    const showSignatureRequired = () => notificationStore.showSignatureRequired()
    const showFormSubmissionError = (formType, errors) => notificationStore.showFormSubmissionError(formType, errors)

    // Control methods
    const clearAllNotifications = () => notificationStore.clearAllNotifications()

    const showAllTypes = () => {
      const notifications = [
        () => showSuccess('Success Demo', 'This is a success notification example.'),
        () => showError('Error Demo', 'This is an error notification example.'),
        () => showWarning('Warning Demo', 'This is a warning notification example.'),
        () => showInfo('Info Demo', 'This is an info notification example.'),
        () => showBookingNotification('Booking Demo', 'This is a booking notification example.'),
        () => showAccessNotification('Access Demo', 'This is an access notification example.'),
        () => showApprovalNotification('Approval Demo', 'This is an approval notification example.'),
        () => showSystemNotification('System Demo', 'This is a system notification example.'),
        () => showMedicalNotification('Medical Demo', 'This is a medical notification example.')
      ]

      notifications.forEach((notification, index) => {
        setTimeout(() => notification(), index * 500)
      })
    }

    const showRandomNotification = () => {
      const randomNotifications = [
        () => showRequestSubmitted('Device Booking', 'REQ-' + Math.floor(Math.random() * 1000)),
        () => showDeviceReady('Random Device', 'REQ-' + Math.floor(Math.random() * 1000)),
        () => showAccessGranted('Random System', 'Read Access'),
        () => showOnboardingComplete(),
        () => showWelcomeMessage('Random User'),
        () => showFileUploadSuccess('random-file.pdf'),
        () => showSecurityAlert('Random Alert', 'Random security event detected.')
      ]

      const randomIndex = Math.floor(Math.random() * randomNotifications.length)
      randomNotifications[randomIndex]()
    }

    return {
      // Basic notifications
      showSuccess,
      showError,
      showWarning,
      showInfo,
      // Enhanced notifications
      showBookingNotification,
      showAccessNotification,
      showApprovalNotification,
      showSystemNotification,
      showMedicalNotification,
      // Predefined notifications
      showRequestSubmitted,
      showDeviceReady,
      showDeviceOverdue,
      showAccessGranted,
      showOnboardingComplete,
      showWelcomeMessage,
      // Advanced notifications
      showSecurityAlert,
      showMaintenanceMode,
      showSessionExpiring,
      showQuotaExceeded,
      // File and form notifications
      showFileUploadSuccess,
      showFileUploadError,
      showSignatureRequired,
      showFormSubmissionError,
      // Controls
      clearAllNotifications,
      showAllTypes,
      showRandomNotification
    }
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

.notification-section {
  padding: 2rem;
  border-radius: 1rem;
  background: rgba(59, 130, 246, 0.1);
  backdrop-filter: blur(15px);
  border: 1px solid rgba(96, 165, 250, 0.2);
}

.demo-button {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 1.5rem;
  border-radius: 0.75rem;
  border: 1px solid;
  backdrop-filter: blur(10px);
  transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
  font-weight: 600;
  min-height: 120px;
}

.demo-button:hover {
  transform: translateY(-2px) scale(1.02);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
}

.demo-button i {
  font-size: 1.5rem;
}

.control-button {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1rem 2rem;
  border-radius: 0.75rem;
  border: 1px solid;
  backdrop-filter: blur(10px);
  transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
  font-weight: 600;
}

.control-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .notification-demo {
    padding: 1rem;
  }
  
  .notification-section {
    padding: 1rem;
  }
  
  .demo-button {
    min-height: 100px;
    padding: 1rem;
  }
  
  .demo-button i {
    font-size: 1.25rem;
  }
}
</style>