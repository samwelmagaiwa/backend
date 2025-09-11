<template>
  <div class="notification-container fixed top-4 right-4 z-50 space-y-3 max-w-md">
    <TransitionGroup name="notification" tag="div">
      <div
        v-for="notification in notifications"
        :key="notification.id"
        :class="[
          'notification-card rounded-xl shadow-2xl border-l-4 p-5 backdrop-blur-sm relative overflow-hidden',
          getNotificationClasses(notification.type)
        ]"
      >
        <!-- Medical Background Pattern -->
        <div class="absolute inset-0 opacity-5">
          <div class="grid grid-cols-8 gap-2 h-full transform rotate-45">
            <div
              v-for="i in 32"
              :key="i"
              class="bg-white rounded-full w-1 h-1 animate-pulse"
              :style="{ animationDelay: i * 0.1 + 's' }"
            ></div>
          </div>
        </div>

        <!-- Header -->
        <div class="flex items-start justify-between relative z-10">
          <div class="flex items-center space-x-3">
            <div :class="getIconClasses(notification.type)">
              <i :class="getIcon(notification.type)" class="text-sm"></i>
            </div>
            <h4 v-if="notification.title" class="text-sm font-bold text-white drop-shadow-md">
              {{ notification.title }}
            </h4>
          </div>
          <button
            @click="removeNotification(notification.id)"
            class="text-white/70 hover:text-white transition-colors p-1 rounded-full hover:bg-white/10"
          >
            <i class="fas fa-times text-xs"></i>
          </button>
        </div>

        <!-- Message -->
        <p
          v-if="notification.message"
          class="text-sm text-white/90 mt-3 leading-relaxed relative z-10"
        >
          {{ notification.message }}
        </p>

        <!-- Actions -->
        <div
          v-if="notification.actions && notification.actions.length > 0"
          class="mt-4 flex space-x-2 relative z-10"
        >
          <button
            v-for="action in notification.actions"
            :key="action.action"
            @click="handleAction(action, notification)"
            :class="getActionButtonClasses(notification.type)"
            class="px-4 py-2 text-xs font-semibold rounded-lg transition-all duration-200 hover:shadow-lg hover:scale-105"
          >
            <i v-if="action.icon" :class="action.icon" class="mr-1"></i>
            {{ action.label }}
          </button>
        </div>

        <!-- Progress bar for non-persistent notifications -->
        <div
          v-if="!notification.persistent"
          :class="getProgressBarClasses(notification.type)"
          class="progress-bar mt-4 h-1 rounded-full relative z-10"
          :style="`animation-duration: ${notification.duration}ms`"
        ></div>

        <!-- Notification Type Badge -->
        <div
          :class="getTypeBadgeClasses(notification.type)"
          class="absolute top-2 right-12 px-2 py-1 rounded-full text-xs font-bold uppercase tracking-wide"
        >
          {{ notification.type }}
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

        if (action.callback && typeof action.callback === 'function') {
          action.callback()
        }

        // Remove notification after action
        removeNotification(notification.id)
      }

      const getNotificationClasses = (type) => {
        const classes = {
          success:
            'medical-card bg-gradient-to-r from-blue-600/30 to-emerald-600/30 border-emerald-400 shadow-emerald-500/30',
          error:
            'medical-card bg-gradient-to-r from-blue-600/30 to-red-600/30 border-red-400 shadow-red-500/30',
          warning:
            'medical-card bg-gradient-to-r from-blue-600/30 to-yellow-600/30 border-yellow-400 shadow-yellow-500/30',
          info: 'medical-card bg-gradient-to-r from-blue-600/30 to-blue-700/30 border-blue-400 shadow-blue-500/30',
          booking:
            'medical-card bg-gradient-to-r from-blue-600/30 to-cyan-600/30 border-cyan-400 shadow-cyan-500/30',
          access:
            'medical-card bg-gradient-to-r from-blue-600/30 to-indigo-600/30 border-indigo-400 shadow-indigo-500/30',
          approval:
            'medical-card bg-gradient-to-r from-blue-600/30 to-purple-600/30 border-purple-400 shadow-purple-500/30',
          system:
            'medical-card bg-gradient-to-r from-blue-600/30 to-teal-600/30 border-teal-400 shadow-teal-500/30',
          medical:
            'medical-card bg-gradient-to-r from-blue-600/30 to-blue-800/30 border-blue-400 shadow-blue-500/30',
          // Additional notification types with blue theme
          security:
            'medical-card bg-gradient-to-r from-blue-600/30 to-red-700/30 border-red-500 shadow-red-600/30',
          maintenance:
            'medical-card bg-gradient-to-r from-blue-600/30 to-orange-600/30 border-orange-400 shadow-orange-500/30',
          feature:
            'medical-card bg-gradient-to-r from-blue-600/30 to-violet-600/30 border-violet-400 shadow-violet-500/30',
          quota:
            'medical-card bg-gradient-to-r from-blue-600/30 to-amber-600/30 border-amber-400 shadow-amber-500/30',
          session:
            'medical-card bg-gradient-to-r from-blue-600/30 to-rose-600/30 border-rose-400 shadow-rose-500/30',
          backup:
            'medical-card bg-gradient-to-r from-blue-600/30 to-slate-600/30 border-slate-400 shadow-slate-500/30',
          welcome:
            'medical-card bg-gradient-to-r from-blue-600/30 to-sky-600/30 border-sky-400 shadow-sky-500/30'
        }
        return classes[type] || classes.info
      }

      const getIconClasses = (type) => {
        const classes = {
          success:
            'w-8 h-8 bg-emerald-500/20 text-emerald-300 rounded-full flex items-center justify-center border border-emerald-400/30',
          error:
            'w-8 h-8 bg-red-500/20 text-red-300 rounded-full flex items-center justify-center border border-red-400/30',
          warning:
            'w-8 h-8 bg-yellow-500/20 text-yellow-300 rounded-full flex items-center justify-center border border-yellow-400/30',
          info: 'w-8 h-8 bg-blue-500/20 text-blue-300 rounded-full flex items-center justify-center border border-blue-400/30',
          booking:
            'w-8 h-8 bg-cyan-500/20 text-cyan-300 rounded-full flex items-center justify-center border border-cyan-400/30',
          access:
            'w-8 h-8 bg-indigo-500/20 text-indigo-300 rounded-full flex items-center justify-center border border-indigo-400/30',
          approval:
            'w-8 h-8 bg-purple-500/20 text-purple-300 rounded-full flex items-center justify-center border border-purple-400/30',
          system:
            'w-8 h-8 bg-teal-500/20 text-teal-300 rounded-full flex items-center justify-center border border-teal-400/30',
          medical:
            'w-8 h-8 bg-blue-500/20 text-blue-300 rounded-full flex items-center justify-center border border-blue-400/30',
          // Additional notification types
          security:
            'w-8 h-8 bg-red-600/20 text-red-300 rounded-full flex items-center justify-center border border-red-500/30',
          maintenance:
            'w-8 h-8 bg-orange-500/20 text-orange-300 rounded-full flex items-center justify-center border border-orange-400/30',
          feature:
            'w-8 h-8 bg-violet-500/20 text-violet-300 rounded-full flex items-center justify-center border border-violet-400/30',
          quota:
            'w-8 h-8 bg-amber-500/20 text-amber-300 rounded-full flex items-center justify-center border border-amber-400/30',
          session:
            'w-8 h-8 bg-rose-500/20 text-rose-300 rounded-full flex items-center justify-center border border-rose-400/30',
          backup:
            'w-8 h-8 bg-slate-500/20 text-slate-300 rounded-full flex items-center justify-center border border-slate-400/30',
          welcome:
            'w-8 h-8 bg-sky-500/20 text-sky-300 rounded-full flex items-center justify-center border border-sky-400/30'
        }
        return classes[type] || classes.info
      }

      const getIcon = (type) => {
        const icons = {
          success: 'fas fa-check-circle',
          error: 'fas fa-exclamation-triangle',
          warning: 'fas fa-exclamation-circle',
          info: 'fas fa-info-circle',
          booking: 'fas fa-calendar-check',
          access: 'fas fa-key',
          approval: 'fas fa-clipboard-check',
          system: 'fas fa-cog',
          medical: 'fas fa-hospital',
          // Additional notification types
          security: 'fas fa-shield-alt',
          maintenance: 'fas fa-tools',
          feature: 'fas fa-star',
          quota: 'fas fa-chart-pie',
          session: 'fas fa-clock',
          backup: 'fas fa-download',
          welcome: 'fas fa-hand-wave'
        }
        return icons[type] || icons.info
      }

      const getActionButtonClasses = (type) => {
        const classes = {
          success:
            'bg-emerald-500/20 text-emerald-300 hover:bg-emerald-500/30 border border-emerald-400/30',
          error: 'bg-red-500/20 text-red-300 hover:bg-red-500/30 border border-red-400/30',
          warning:
            'bg-yellow-500/20 text-yellow-300 hover:bg-yellow-500/30 border border-yellow-400/30',
          info: 'bg-blue-500/20 text-blue-300 hover:bg-blue-500/30 border border-blue-400/30',
          booking: 'bg-cyan-500/20 text-cyan-300 hover:bg-cyan-500/30 border border-cyan-400/30',
          access:
            'bg-indigo-500/20 text-indigo-300 hover:bg-indigo-500/30 border border-indigo-400/30',
          approval:
            'bg-purple-500/20 text-purple-300 hover:bg-purple-500/30 border border-purple-400/30',
          system: 'bg-teal-500/20 text-teal-300 hover:bg-teal-500/30 border border-teal-400/30',
          medical: 'bg-blue-500/20 text-blue-300 hover:bg-blue-500/30 border border-blue-400/30',
          // Additional notification types
          security: 'bg-red-600/20 text-red-300 hover:bg-red-600/30 border border-red-500/30',
          maintenance:
            'bg-orange-500/20 text-orange-300 hover:bg-orange-500/30 border border-orange-400/30',
          feature:
            'bg-violet-500/20 text-violet-300 hover:bg-violet-500/30 border border-violet-400/30',
          quota: 'bg-amber-500/20 text-amber-300 hover:bg-amber-500/30 border border-amber-400/30',
          session: 'bg-rose-500/20 text-rose-300 hover:bg-rose-500/30 border border-rose-400/30',
          backup: 'bg-slate-500/20 text-slate-300 hover:bg-slate-500/30 border border-slate-400/30',
          welcome: 'bg-sky-500/20 text-sky-300 hover:bg-sky-500/30 border border-sky-400/30'
        }
        return classes[type] || classes.info
      }

      const getProgressBarClasses = (type) => {
        const classes = {
          success: 'bg-emerald-400/60',
          error: 'bg-red-400/60',
          warning: 'bg-yellow-400/60',
          info: 'bg-blue-400/60',
          booking: 'bg-cyan-400/60',
          access: 'bg-indigo-400/60',
          approval: 'bg-purple-400/60',
          system: 'bg-teal-400/60',
          medical: 'bg-blue-400/60',
          // Additional notification types
          security: 'bg-red-500/60',
          maintenance: 'bg-orange-400/60',
          feature: 'bg-violet-400/60',
          quota: 'bg-amber-400/60',
          session: 'bg-rose-400/60',
          backup: 'bg-slate-400/60',
          welcome: 'bg-sky-400/60'
        }
        return classes[type] || classes.info
      }

      const getTypeBadgeClasses = (type) => {
        const classes = {
          success: 'bg-emerald-500/30 text-emerald-200 border border-emerald-400/30',
          error: 'bg-red-500/30 text-red-200 border border-red-400/30',
          warning: 'bg-yellow-500/30 text-yellow-200 border border-yellow-400/30',
          info: 'bg-blue-500/30 text-blue-200 border border-blue-400/30',
          booking: 'bg-cyan-500/30 text-cyan-200 border border-cyan-400/30',
          access: 'bg-indigo-500/30 text-indigo-200 border border-indigo-400/30',
          approval: 'bg-purple-500/30 text-purple-200 border border-purple-400/30',
          system: 'bg-teal-500/30 text-teal-200 border border-teal-400/30',
          medical: 'bg-blue-500/30 text-blue-200 border border-blue-400/30',
          // Additional notification types
          security: 'bg-red-600/30 text-red-200 border border-red-500/30',
          maintenance: 'bg-orange-500/30 text-orange-200 border border-orange-400/30',
          feature: 'bg-violet-500/30 text-violet-200 border border-violet-400/30',
          quota: 'bg-amber-500/30 text-amber-200 border border-amber-400/30',
          session: 'bg-rose-500/30 text-rose-200 border border-rose-400/30',
          backup: 'bg-slate-500/30 text-slate-200 border border-slate-400/30',
          welcome: 'bg-sky-500/30 text-sky-200 border border-sky-400/30'
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
        getProgressBarClasses,
        getTypeBadgeClasses
      }
    }
  }
</script>

<style scoped>
  /* Medical Glass morphism effects */
  .medical-card {
    background: rgba(59, 130, 246, 0.15);
    backdrop-filter: blur(25px);
    -webkit-backdrop-filter: blur(25px);
    border: 2px solid rgba(96, 165, 250, 0.3);
    box-shadow:
      0 8px 32px rgba(29, 78, 216, 0.4),
      inset 0 1px 0 rgba(255, 255, 255, 0.1);
  }

  /* Notification transitions */
  .notification-enter-active,
  .notification-leave-active {
    transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
  }

  .notification-enter-from {
    opacity: 0;
    transform: translateX(100%) scale(0.8);
  }

  .notification-leave-to {
    opacity: 0;
    transform: translateX(100%) scale(0.8);
  }

  .notification-move {
    transition: transform 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
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
    transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
  }

  .notification-card:hover {
    transform: translateY(-4px) scale(1.02);
    box-shadow:
      0 25px 50px -12px rgba(0, 0, 0, 0.25),
      0 0 0 1px rgba(255, 255, 255, 0.1);
  }

  /* Pulse animation for medical pattern */
  @keyframes pulse {
    0%,
    100% {
      opacity: 0.3;
    }
    50% {
      opacity: 0.8;
    }
  }

  .animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
  }

  /* Responsive adjustments */
  @media (max-width: 768px) {
    .notification-container {
      left: 1rem;
      right: 1rem;
      top: 1rem;
      max-width: none;
    }

    .notification-card {
      padding: 1rem;
    }
  }

  @media (max-width: 480px) {
    .notification-container {
      left: 0.5rem;
      right: 0.5rem;
      top: 0.5rem;
    }
  }

  /* Enhanced shadow effects */
  .notification-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(
      135deg,
      rgba(255, 255, 255, 0.1) 0%,
      rgba(255, 255, 255, 0.05) 100%
    );
    border-radius: inherit;
    pointer-events: none;
  }

  /* Glow effect on hover */
  .notification-card:hover::after {
    content: '';
    position: absolute;
    top: -2px;
    left: -2px;
    right: -2px;
    bottom: -2px;
    background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
    border-radius: inherit;
    z-index: -1;
    animation: glow 2s ease-in-out infinite alternate;
  }

  @keyframes glow {
    from {
      opacity: 0.5;
    }
    to {
      opacity: 1;
    }
  }
</style>
