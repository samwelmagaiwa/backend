<template>
  <div class="notification-container fixed top-4 right-4 z-50 space-y-3 max-w-md">
    <TransitionGroup name="notification" tag="div">
      <div
        v-for="notification in notifications"
        :key="notification.id"
        :class="[
          'notification-card rounded-lg shadow-2xl border-l-4 p-4 backdrop-blur-sm',
          getNotificationClasses(notification.type)
        ]"
      >
        <!-- Header -->
        <div class="flex items-start justify-between">
          <div class="flex items-center space-x-2">
            <div :class="getIconClasses(notification.type)">
              <i :class="getIcon(notification.type)" class="text-sm"></i>
            </div>
            <h4 v-if="notification.title" class="text-sm font-semibold text-gray-800">
              {{ notification.title }}
            </h4>
          </div>
          <button
            @click="removeNotification(notification.id)"
            class="text-gray-400 hover:text-gray-600 transition-colors"
          >
            <i class="fas fa-times text-xs"></i>
          </button>
        </div>

        <!-- Message -->
        <p v-if="notification.message" class="text-sm text-gray-700 mt-2 leading-relaxed">
          {{ notification.message }}
        </p>

        <!-- Actions -->
        <div
          v-if="notification.actions && notification.actions.length > 0"
          class="mt-3 flex space-x-2"
        >
          <button
            v-for="action in notification.actions"
            :key="action.action"
            @click="handleAction(action, notification)"
            :class="getActionButtonClasses(notification.type)"
            class="px-3 py-1 text-xs font-medium rounded transition-all duration-200 hover:shadow-md"
          >
            {{ action.label }}
          </button>
        </div>

        <!-- Progress bar for non-persistent notifications -->
        <div
          v-if="!notification.persistent"
          :class="getProgressBarClasses(notification.type)"
          class="progress-bar mt-3 h-1 rounded-full"
          :style="`animation-duration: ${notification.duration}ms`"
        ></div>
      </div>
    </TransitionGroup>
  </div>
</template>

<script>
  import { computed } from 'vue'
  import { useRouter } from 'vue-router'
  import { useNotificationStore } from '@/stores/notification'

  export default {
    name: 'NotificationSystem',
    setup() {
      const router = useRouter()
      const notificationStore = useNotificationStore()

      const notifications = computed(() => notificationStore.notifications)

      const removeNotification = (id) => {
        notificationStore.removeNotification(id)
      }

      const handleAction = (action, notification) => {
        if (action.url) {
          router.push(action.url)
        }

        // Remove notification after action
        removeNotification(notification.id)
      }

      const getNotificationClasses = (type) => {
        const classes = {
          success:
            'bg-gradient-to-r from-green-50 to-emerald-50 border-green-400 shadow-green-500/20',
          error: 'bg-gradient-to-r from-red-50 to-rose-50 border-red-400 shadow-red-500/20',
          warning:
            'bg-gradient-to-r from-yellow-50 to-amber-50 border-yellow-400 shadow-yellow-500/20',
          info: 'bg-gradient-to-r from-blue-50 to-cyan-50 border-blue-400 shadow-blue-500/20'
        }
        return classes[type] || classes.info
      }

      const getIconClasses = (type) => {
        const classes = {
          success:
            'w-6 h-6 bg-green-100 text-green-600 rounded-full flex items-center justify-center',
          error: 'w-6 h-6 bg-red-100 text-red-600 rounded-full flex items-center justify-center',
          warning:
            'w-6 h-6 bg-yellow-100 text-yellow-600 rounded-full flex items-center justify-center',
          info: 'w-6 h-6 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center'
        }
        return classes[type] || classes.info
      }

      const getIcon = (type) => {
        const icons = {
          success: 'fas fa-check',
          error: 'fas fa-exclamation-triangle',
          warning: 'fas fa-exclamation-circle',
          info: 'fas fa-info-circle'
        }
        return icons[type] || icons.info
      }

      const getActionButtonClasses = (type) => {
        const classes = {
          success: 'bg-green-100 text-green-700 hover:bg-green-200',
          error: 'bg-red-100 text-red-700 hover:bg-red-200',
          warning: 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200',
          info: 'bg-blue-100 text-blue-700 hover:bg-blue-200'
        }
        return classes[type] || classes.info
      }

      const getProgressBarClasses = (type) => {
        const classes = {
          success: 'bg-green-200',
          error: 'bg-red-200',
          warning: 'bg-yellow-200',
          info: 'bg-blue-200'
        }
        return classes[type] || classes.info
      }

      return {
        notifications,
        removeNotification,
        handleAction,
        getNotificationClasses,
        getIconClasses,
        getIcon,
        getActionButtonClasses,
        getProgressBarClasses
      }
    }
  }
</script>

<style scoped>
  /* Notification transitions */
  .notification-enter-active,
  .notification-leave-active {
    transition: all 0.3s ease;
  }

  .notification-enter-from {
    opacity: 0;
    transform: translateX(100%);
  }

  .notification-leave-to {
    opacity: 0;
    transform: translateX(100%);
  }

  .notification-move {
    transition: transform 0.3s ease;
  }

  /* Progress bar animation */
  .progress-bar {
    animation: shrink linear;
    animation-fill-mode: forwards;
  }

  @keyframes shrink {
    from {
      width: 100%;
    }
    to {
      width: 0%;
    }
  }

  /* Notification card hover effects */
  .notification-card {
    transition: all 0.3s ease;
  }

  .notification-card:hover {
    transform: translateY(-2px);
    box-shadow:
      0 20px 25px -5px rgba(0, 0, 0, 0.1),
      0 10px 10px -5px rgba(0, 0, 0, 0.04);
  }

  /* Responsive adjustments */
  @media (max-width: 768px) {
    .notification-container {
      left: 1rem;
      right: 1rem;
      top: 1rem;
      max-width: none;
    }
  }
</style>
