/**
 * Notification Store
 * Handles user notifications, alerts, and messages across the application
 */

import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useNotificationStore = defineStore('notification', () => {
  // State
  const notifications = ref([])
  const pendingBookingInfo = ref(null)

  // Actions
  function addNotification(notification) {
    const id = Date.now() + Math.random()
    const newNotification = {
      id,
      type: notification.type || 'info', // success, error, warning, info
      title: notification.title || '',
      message: notification.message || '',
      duration: notification.duration || 5000,
      persistent: notification.persistent || false,
      actions: notification.actions || [],
      createdAt: new Date()
    }

    notifications.value.push(newNotification)

    // Auto-remove non-persistent notifications
    if (!newNotification.persistent) {
      setTimeout(() => {
        removeNotification(id)
      }, newNotification.duration)
    }

    return id
  }

  function removeNotification(id) {
    const index = notifications.value.findIndex((n) => n.id === id)
    if (index > -1) {
      notifications.value.splice(index, 1)
    }
  }

  function clearAllNotifications() {
    notifications.value = []
  }

  // Specific notification types
  function showSuccess(title, message, options = {}) {
    return addNotification({
      type: 'success',
      title,
      message,
      ...options
    })
  }

  function showError(title, message, options = {}) {
    return addNotification({
      type: 'error',
      title,
      message,
      duration: options.duration || 8000, // Longer duration for errors
      ...options
    })
  }

  function showWarning(title, message, options = {}) {
    return addNotification({
      type: 'warning',
      title,
      message,
      duration: options.duration || 6000,
      ...options
    })
  }

  function showInfo(title, message, options = {}) {
    return addNotification({
      type: 'info',
      title,
      message,
      ...options
    })
  }

  // Booking-specific notifications
  function setPendingBookingInfo(bookingInfo) {
    pendingBookingInfo.value = bookingInfo
  }

  function clearPendingBookingInfo() {
    pendingBookingInfo.value = null
  }

  function showBookingLockNotification(bookingInfo) {
    const actions = [
      {
        label: 'View Request',
        action: 'view-request',
        icon: 'fas fa-eye',
        url: bookingInfo?.request_url || '/request-status'
      }
    ]

    return addNotification({
      type: 'booking',
      title: 'Booking Service Locked',
      message: `You have a pending ${bookingInfo?.device_name || 'device'} booking request. Please wait for it to be processed before submitting a new request.`,
      persistent: true,
      actions
    })
  }

  // Enhanced notification types for hospital management system
  function showBookingNotification(title, message, options = {}) {
    return addNotification({
      type: 'booking',
      title,
      message,
      ...options
    })
  }

  function showAccessNotification(title, message, options = {}) {
    return addNotification({
      type: 'access',
      title,
      message,
      ...options
    })
  }

  function showApprovalNotification(title, message, options = {}) {
    return addNotification({
      type: 'approval',
      title,
      message,
      ...options
    })
  }

  function showSystemNotification(title, message, options = {}) {
    return addNotification({
      type: 'system',
      title,
      message,
      ...options
    })
  }

  function showMedicalNotification(title, message, options = {}) {
    return addNotification({
      type: 'medical',
      title,
      message,
      ...options
    })
  }

  // Predefined hospital-specific notifications
  function showRequestSubmitted(requestType, requestId) {
    return showSuccess(
      'Request Submitted Successfully',
      `Your ${requestType} request #${requestId} has been submitted and is now in the approval process.`,
      {
        actions: [
          {
            label: 'View Status',
            icon: 'fas fa-eye',
            url: '/request-status'
          }
        ]
      }
    )
  }

  function showRequestApproved(requestType, requestId) {
    return showApproval(
      'Request Approved',
      `Your ${requestType} request #${requestId} has been approved by the relevant authority.`,
      {
        actions: [
          {
            label: 'View Details',
            icon: 'fas fa-info-circle',
            url: `/request-details?id=${requestId}`
          }
        ]
      }
    )
  }

  function showRequestRejected(requestType, requestId, reason) {
    return showError(
      'Request Rejected',
      `Your ${requestType} request #${requestId} has been rejected. Reason: ${reason}`,
      {
        actions: [
          {
            label: 'View Details',
            icon: 'fas fa-info-circle',
            url: `/request-details?id=${requestId}`
          },
          {
            label: 'Submit New',
            icon: 'fas fa-plus',
            url: '/user-dashboard'
          }
        ]
      }
    )
  }

  function showDeviceReady(deviceName, requestId) {
    return showBookingNotification(
      'Device Ready for Collection',
      `Your requested ${deviceName} is ready for collection. Please visit the ICT department.`,
      {
        actions: [
          {
            label: 'View Details',
            icon: 'fas fa-calendar-check',
            url: `/request-details?id=${requestId}&type=booking_service`
          }
        ]
      }
    )
  }

  function showDeviceOverdue(deviceName, requestId, daysOverdue) {
    return showWarning(
      'Device Return Overdue',
      `Your ${deviceName} is ${daysOverdue} days overdue for return. Please return it immediately to avoid penalties.`,
      {
        persistent: true,
        actions: [
          {
            label: 'View Details',
            icon: 'fas fa-exclamation-triangle',
            url: `/request-details?id=${requestId}&type=booking_service`
          }
        ]
      }
    )
  }

  function showAccessGranted(systemName, accessLevel) {
    return showAccessNotification(
      'Access Granted',
      `You have been granted ${accessLevel} access to ${systemName}. You can now use the system.`,
      {
        actions: [
          {
            label: 'View Access',
            icon: 'fas fa-key',
            url: '/request-status'
          }
        ]
      }
    )
  }

  function showSystemMaintenance(systemName, maintenanceWindow) {
    return showSystemNotification(
      'System Maintenance Scheduled',
      `${systemName} will be under maintenance ${maintenanceWindow}. Please plan accordingly.`,
      {
        persistent: true,
        actions: [
          {
            label: 'More Info',
            icon: 'fas fa-info-circle',
            url: '/system-status'
          }
        ]
      }
    )
  }

  function showEmergencyAlert(title, message) {
    return showMedicalNotification(
      title,
      message,
      {
        persistent: true,
        duration: 0, // Never auto-dismiss
        actions: [
          {
            label: 'Acknowledge',
            icon: 'fas fa-check',
            callback: () => console.log('Emergency alert acknowledged')
          }
        ]
      }
    )
  }

  // Additional Hospital Management System Notifications
  function showOnboardingComplete() {
    return showSuccess(
      'Onboarding Complete',
      'Welcome to Muhimbili National Hospital Access Management System! You can now submit access requests.',
      {
        actions: [
          {
            label: 'Start Using System',
            icon: 'fas fa-rocket',
            url: '/user-dashboard'
          }
        ]
      }
    )
  }

  function showProfileUpdated() {
    return showInfo(
      'Profile Updated',
      'Your profile information has been successfully updated.',
      {
        actions: [
          {
            label: 'View Profile',
            icon: 'fas fa-user',
            url: '/profile'
          }
        ]
      }
    )
  }

  function showPasswordChanged() {
    return showSuccess(
      'Password Changed',
      'Your password has been successfully updated. Please use your new password for future logins.',
      {
        duration: 6000
      }
    )
  }

  function showTermsAccepted() {
    return showSuccess(
      'Terms Accepted',
      'You have successfully accepted the Terms of Service and ICT Policy.',
      {
        actions: [
          {
            label: 'Continue',
            icon: 'fas fa-arrow-right',
            url: '/user-dashboard'
          }
        ]
      }
    )
  }

  function showFormSubmissionError(formType, errors) {
    const errorList = Array.isArray(errors) ? errors.join(', ') : errors
    return showError(
      `${formType} Submission Failed`,
      `Please correct the following errors: ${errorList}`,
      {
        duration: 10000,
        actions: [
          {
            label: 'Review Form',
            icon: 'fas fa-edit',
            callback: () => window.scrollTo({ top: 0, behavior: 'smooth' })
          }
        ]
      }
    )
  }

  function showFileUploadSuccess(fileName) {
    return showSuccess(
      'File Uploaded',
      `${fileName} has been successfully uploaded.`,
      {
        duration: 4000
      }
    )
  }

  function showFileUploadError(fileName, reason) {
    return showError(
      'File Upload Failed',
      `Failed to upload ${fileName}. ${reason}`,
      {
        duration: 8000
      }
    )
  }

  function showSignatureRequired() {
    return showWarning(
      'Signature Required',
      'Please provide your digital signature before submitting the request.',
      {
        persistent: true,
        actions: [
          {
            label: 'Add Signature',
            icon: 'fas fa-signature',
            callback: () => {
              const signatureSection = document.querySelector('[data-signature-section]')
              if (signatureSection) {
                signatureSection.scrollIntoView({ behavior: 'smooth' })
              }
            }
          }
        ]
      }
    )
  }

  function showRequestCancelled(requestType, requestId) {
    return showInfo(
      'Request Cancelled',
      `Your ${requestType} request #${requestId} has been successfully cancelled.`,
      {
        actions: [
          {
            label: 'Submit New Request',
            icon: 'fas fa-plus',
            url: '/user-dashboard'
          }
        ]
      }
    )
  }

  function showDeviceCollected(deviceName, requestId) {
    return showBookingNotification(
      'Device Collected',
      `You have successfully collected ${deviceName}. Please return it by the specified date.`,
      {
        actions: [
          {
            label: 'View Return Date',
            icon: 'fas fa-calendar',
            url: `/request-details?id=${requestId}&type=booking_service`
          }
        ]
      }
    )
  }

  function showDeviceReturned(deviceName, requestId) {
    return showSuccess(
      'Device Returned',
      `Thank you for returning ${deviceName} on time. Your booking request is now complete.`,
      {
        actions: [
          {
            label: 'Book Another Device',
            icon: 'fas fa-calendar-plus',
            url: '/user-dashboard'
          }
        ]
      }
    )
  }

  function showAccessRevoked(systemName, reason) {
    return showWarning(
      'Access Revoked',
      `Your access to ${systemName} has been revoked. Reason: ${reason}`,
      {
        persistent: true,
        actions: [
          {
            label: 'Contact Support',
            icon: 'fas fa-headset',
            url: '/support'
          },
          {
            label: 'Request New Access',
            icon: 'fas fa-key',
            url: '/user-dashboard'
          }
        ]
      }
    )
  }

  function showMaintenanceMode(systemName, estimatedTime) {
    return showSystemNotification(
      'System Under Maintenance',
      `${systemName} is currently under maintenance. Estimated completion: ${estimatedTime}`,
      {
        persistent: true,
        actions: [
          {
            label: 'Check Status',
            icon: 'fas fa-sync',
            callback: () => window.location.reload()
          }
        ]
      }
    )
  }

  function showNewFeatureAnnouncement(featureName, description) {
    return showInfo(
      `New Feature: ${featureName}`,
      description,
      {
        duration: 10000,
        actions: [
          {
            label: 'Learn More',
            icon: 'fas fa-info-circle',
            url: '/help'
          },
          {
            label: 'Try It Now',
            icon: 'fas fa-rocket',
            url: '/user-dashboard'
          }
        ]
      }
    )
  }

  function showQuotaExceeded(quotaType, currentUsage, limit) {
    return showWarning(
      'Quota Exceeded',
      `You have exceeded your ${quotaType} quota (${currentUsage}/${limit}). Please contact your supervisor.`,
      {
        persistent: true,
        actions: [
          {
            label: 'View Usage',
            icon: 'fas fa-chart-bar',
            url: '/usage-statistics'
          },
          {
            label: 'Contact Supervisor',
            icon: 'fas fa-user-tie',
            url: '/contact'
          }
        ]
      }
    )
  }

  function showSessionExpiring(minutesLeft) {
    return showWarning(
      'Session Expiring Soon',
      `Your session will expire in ${minutesLeft} minutes. Please save your work.`,
      {
        persistent: true,
        actions: [
          {
            label: 'Extend Session',
            icon: 'fas fa-clock',
            callback: () => {
              // Extend session logic
              console.log('Extending session...')
            }
          },
          {
            label: 'Save Work',
            icon: 'fas fa-save',
            callback: () => {
              // Auto-save logic
              console.log('Auto-saving...')
            }
          }
        ]
      }
    )
  }

  function showBackupReminder() {
    return showInfo(
      'Backup Reminder',
      'Remember to backup your important documents and data regularly.',
      {
        actions: [
          {
            label: 'Backup Now',
            icon: 'fas fa-download',
            url: '/backup'
          },
          {
            label: 'Schedule Backup',
            icon: 'fas fa-calendar-alt',
            url: '/settings'
          }
        ]
      }
    )
  }

  function showSecurityAlert(alertType, details) {
    return showError(
      `Security Alert: ${alertType}`,
      details,
      {
        persistent: true,
        duration: 0,
        actions: [
          {
            label: 'Change Password',
            icon: 'fas fa-shield-alt',
            url: '/profile'
          },
          {
            label: 'Review Activity',
            icon: 'fas fa-history',
            url: '/security-log'
          }
        ]
      }
    )
  }

  function showWelcomeMessage(userName) {
    return showMedicalNotification(
      `Welcome, ${userName}!`,
      'Welcome to Muhimbili National Hospital Access Management System. Your digital healthcare journey starts here.',
      {
        duration: 8000,
        actions: [
          {
            label: 'Take Tour',
            icon: 'fas fa-route',
            url: '/tour'
          },
          {
            label: 'Get Started',
            icon: 'fas fa-play',
            url: '/user-dashboard'
          }
        ]
      }
    )
  }

  return {
    // State
    notifications,
    pendingBookingInfo,

    // Actions
    addNotification,
    removeNotification,
    clearAllNotifications,

    // Basic notification types
    showSuccess,
    showError,
    showWarning,
    showInfo,

    // Enhanced notification types
    showBookingNotification,
    showAccessNotification,
    showApprovalNotification,
    showSystemNotification,
    showMedicalNotification,

    // Predefined hospital-specific notifications
    showRequestSubmitted,
    showRequestApproved,
    showRequestRejected,
    showDeviceReady,
    showDeviceOverdue,
    showAccessGranted,
    showSystemMaintenance,
    showEmergencyAlert,

    // Additional hospital management notifications
    showOnboardingComplete,
    showProfileUpdated,
    showPasswordChanged,
    showTermsAccepted,
    showFormSubmissionError,
    showFileUploadSuccess,
    showFileUploadError,
    showSignatureRequired,
    showRequestCancelled,
    showDeviceCollected,
    showDeviceReturned,
    showAccessRevoked,
    showMaintenanceMode,
    showNewFeatureAnnouncement,
    showQuotaExceeded,
    showSessionExpiring,
    showBackupReminder,
    showSecurityAlert,
    showWelcomeMessage,

    // Booking-specific
    setPendingBookingInfo,
    clearPendingBookingInfo,
    showBookingLockNotification
  }
})

export default useNotificationStore
