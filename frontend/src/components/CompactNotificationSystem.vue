<template>
  <div class="compact-notification-container fixed top-4 right-4 z-50 space-y-2 max-w-sm">
    <TransitionGroup name="compact-notification" tag="div">
      <div
        v-for="notification in compactNotifications"
        :key="notification.id"
        :class="[
          'compact-notification-card rounded-xl shadow-xl border-l-4 p-3 backdrop-blur-sm relative overflow-hidden transform hover:scale-105 transition-all duration-300',
          getCompactNotificationClasses(notification.type)
        ]"
      >
        <!-- Animated Background Pattern -->
        <div class="absolute inset-0 opacity-10">
          <div class="grid grid-cols-6 gap-1 h-full transform rotate-12">
            <div
              v-for="i in 24"
              :key="i"
              class="bg-white rounded-full w-0.5 h-0.5 animate-pulse"
              :style="{ animationDelay: i * 0.05 + 's' }"
            ></div>
          </div>
        </div>

        <!-- Header -->
        <div class="flex items-start justify-between relative z-10 mb-2">
          <div class="flex items-center space-x-2">
            <div :class="getCompactIconClasses(notification.type)">
              <i :class="getCompactIcon(notification.type)" class="text-xs"></i>
            </div>
            <h4
              v-if="notification.title"
              class="text-xs font-bold text-white drop-shadow-md truncate"
            >
              {{ notification.title }}
            </h4>
          </div>
          <button
            @click="removeNotification(notification.id)"
            class="text-white/60 hover:text-white transition-colors p-0.5 rounded-full hover:bg-white/10 flex-shrink-0"
          >
            <i class="fas fa-times text-xs"></i>
          </button>
        </div>

        <!-- Message -->
        <p
          v-if="notification.message"
          class="text-xs text-white/90 leading-relaxed relative z-10 mb-2"
        >
          {{ notification.message }}
        </p>

        <!-- Compact Actions -->
        <div
          v-if="notification.actions && notification.actions.length > 0"
          class="flex space-x-1 relative z-10"
        >
          <button
            v-for="action in notification.actions.slice(0, 2)"
            :key="action.action"
            @click="handleAction(action, notification)"
            :class="getCompactActionButtonClasses(notification.type)"
            class="px-2 py-1 text-xs font-semibold rounded-md transition-all duration-200 hover:shadow-md hover:scale-105 flex items-center space-x-1"
          >
            <i v-if="action.icon" :class="action.icon" class="text-xs"></i>
            <span class="truncate">{{ action.label }}</span>
          </button>
        </div>

        <!-- Progress bar for non-persistent notifications -->
        <div
          v-if="!notification.persistent"
          :class="getCompactProgressBarClasses(notification.type)"
          class="compact-progress-bar mt-2 h-0.5 rounded-full relative z-10"
          :style="`animation-duration: ${notification.duration}ms`"
        ></div>

        <!-- Compact Type Badge -->
        <div
          :class="getCompactTypeBadgeClasses(notification.type)"
          class="absolute top-1 right-8 px-1 py-0.5 rounded-full text-xs font-bold uppercase tracking-wide"
        >
          {{ notification.type.charAt(0) }}
        </div>
      </div>
    </TransitionGroup>
  </div>
</template>

<script>
  import { computed } from 'vue'
  import { useRouter } from 'vue-router'
  import { useNotificationStore } from '@/stores/notification'

  export default {
    name: 'CompactNotificationSystem',
    setup() {
      const router = useRouter()
      const notificationStore = useNotificationStore()

      // Filter for booking-related notifications only
      const compactNotifications = computed(() =>
        notificationStore.notifications.filter((notification) =>
          ['booking', 'success', 'approval'].includes(notification.type)
        )
      )

      const removeNotification = (id) => {
        notificationStore.removeNotification(id)
      }

      const handleAction = (action, notification) => {
        if (action.url) {
          router.push(action.url)
        }

        if (action.callback && typeof action.callback === 'function') {
          action.callback()
        }

        // Remove notification after action
        removeNotification(notification.id)
      }

      const getCompactNotificationClasses = (type) => {
        const classes = {
          success:
            'compact-card bg-gradient-to-r from-emerald-500/30 to-emerald-600/30 border-emerald-400 shadow-emerald-500/40',
          booking:
            'compact-card bg-gradient-to-r from-blue-500/30 to-cyan-600/30 border-cyan-400 shadow-cyan-500/40',
          approval:
            'compact-card bg-gradient-to-r from-purple-500/30 to-purple-600/30 border-purple-400 shadow-purple-500/40'
        }
        return classes[type] || classes.booking
      }

      const getCompactIconClasses = (type) => {
        const classes = {
          success:
            'w-5 h-5 bg-emerald-500/30 text-emerald-300 rounded-full flex items-center justify-center border border-emerald-400/40',
          booking:
            'w-5 h-5 bg-cyan-500/30 text-cyan-300 rounded-full flex items-center justify-center border border-cyan-400/40',
          approval:
            'w-5 h-5 bg-purple-500/30 text-purple-300 rounded-full flex items-center justify-center border border-purple-400/40'
        }
        return classes[type] || classes.booking
      }

      const getCompactIcon = (type) => {
        const icons = {
          success: 'fas fa-check-circle',
          booking: 'fas fa-calendar-check',
          approval: 'fas fa-clipboard-check'
        }
        return icons[type] || icons.booking
      }

      const getCompactActionButtonClasses = (type) => {
        const classes = {
          success:
            'bg-emerald-500/25 text-emerald-200 hover:bg-emerald-500/40 border border-emerald-400/40',
          booking: 'bg-cyan-500/25 text-cyan-200 hover:bg-cyan-500/40 border border-cyan-400/40',
          approval:
            'bg-purple-500/25 text-purple-200 hover:bg-purple-500/40 border border-purple-400/40'
        }
        return classes[type] || classes.booking
      }

      const getCompactProgressBarClasses = (type) => {
        const classes = {
          success: 'bg-emerald-400/70',
          booking: 'bg-cyan-400/70',
          approval: 'bg-purple-400/70'
        }
        return classes[type] || classes.booking
      }

      const getCompactTypeBadgeClasses = (type) => {
        const classes = {
          success: 'bg-emerald-500/40 text-emerald-100 border border-emerald-400/40',
          booking: 'bg-cyan-500/40 text-cyan-100 border border-cyan-400/40',
          approval: 'bg-purple-500/40 text-purple-100 border border-purple-400/40'
        }
        return classes[type] || classes.booking
      }

      return {
        compactNotifications,
        removeNotification,
        handleAction,
        getCompactNotificationClasses,
        getCompactIconClasses,
        getCompactIcon,
        getCompactActionButtonClasses,
        getCompactProgressBarClasses,
        getCompactTypeBadgeClasses
      }
    }
  }
</script>

<style scoped>
  /* Compact Glass morphism effects */
  .compact-card {
    background: rgba(59, 130, 246, 0.12);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1.5px solid rgba(96, 165, 250, 0.25);
    box-shadow:
      0 4px 20px rgba(29, 78, 216, 0.3),
      inset 0 1px 0 rgba(255, 255, 255, 0.08);
  }

  /* Compact notification transitions */
  .compact-notification-enter-active,
  .compact-notification-leave-active {
    transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
  }

  .compact-notification-enter-from {
    opacity: 0;
    transform: translateX(100%) scale(0.9);
  }

  .compact-notification-leave-to {
    opacity: 0;
    transform: translateX(100%) scale(0.9);
  }

  .compact-notification-move {
    transition: transform 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
  }

  /* Compact progress bar animation */
  .compact-progress-bar {
    animation: compactShrink linear;
    animation-fill-mode: forwards;
  }

  @keyframes compactShrink {
    from {
      width: 100%;
    }
    to {
      width: 0%;
    }
  }

  /* Compact notification card hover effects */
  .compact-notification-card {
    transition: all 0.2s cubic-bezier(0.25, 0.8, 0.25, 1);
    max-width: 320px;
  }

  .compact-notification-card:hover {
    transform: translateY(-2px) scale(1.02);
    box-shadow:
      0 8px 25px rgba(0, 0, 0, 0.2),
      0 0 0 1px rgba(255, 255, 255, 0.08);
  }

  /* Pulse animation for compact pattern */
  @keyframes compactPulse {
    0%,
    100% {
      opacity: 0.2;
    }
    50% {
      opacity: 0.6;
    }
  }

  .animate-pulse {
    animation: compactPulse 1.5s cubic-bezier(0.4, 0, 0.6, 1) infinite;
  }

  /* Responsive adjustments for compact notifications */
  @media (max-width: 768px) {
    .compact-notification-container {
      left: 0.5rem;
      right: 0.5rem;
      top: 0.5rem;
      max-width: none;
    }

    .compact-notification-card {
      padding: 0.75rem;
      max-width: none;
    }
  }

  @media (max-width: 480px) {
    .compact-notification-container {
      left: 0.25rem;
      right: 0.25rem;
      top: 0.25rem;
    }

    .compact-notification-card {
      padding: 0.5rem;
    }
  }

  /* Enhanced shadow effects for compact cards */
  .compact-notification-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(
      135deg,
      rgba(255, 255, 255, 0.08) 0%,
      rgba(255, 255, 255, 0.03) 100%
    );
    border-radius: inherit;
    pointer-events: none;
  }

  /* Compact glow effect on hover */
  .compact-notification-card:hover::after {
    content: '';
    position: absolute;
    top: -1px;
    left: -1px;
    right: -1px;
    bottom: -1px;
    background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.06), transparent);
    border-radius: inherit;
    z-index: -1;
    animation: compactGlow 1.5s ease-in-out infinite alternate;
  }

  @keyframes compactGlow {
    from {
      opacity: 0.3;
    }
    to {
      opacity: 0.8;
    }
  }
</style>
"
