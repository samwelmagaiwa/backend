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
        url: bookingInfo?.request_url || '/request-status'
      }
    ]

    return addNotification({
      type: 'warning',
      title: 'Booking Service Locked',
      message: `You have a pending ${bookingInfo?.device_name || 'device'} booking request. Please wait for it to be processed before submitting a new request.`,
      persistent: true,
      actions
    })
  }

  return {
    // State
    notifications,
    pendingBookingInfo,

    // Actions
    addNotification,
    removeNotification,
    clearAllNotifications,

    // Specific notification types
    showSuccess,
    showError,
    showWarning,
    showInfo,

    // Booking-specific
    setPendingBookingInfo,
    clearPendingBookingInfo,
    showBookingLockNotification
  }
})

export default useNotificationStore
