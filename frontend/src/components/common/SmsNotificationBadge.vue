<template>
  <div v-if="show" class="inline-flex items-center gap-1.5">
    <!-- SMS Success Badge -->
    <span
      v-if="status === 'sent'"
      class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-800"
      title="SMS notification sent successfully"
    >
      <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
      </svg>
      <span>SMS Sent</span>
    </span>

    <!-- SMS Pending Badge -->
    <span
      v-else-if="status === 'pending'"
      class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800"
      title="SMS notification pending"
    >
      <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
      </svg>
      <span>Sending SMS</span>
    </span>

    <!-- SMS Failed Badge -->
    <span
      v-else-if="status === 'failed'"
      class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium rounded-full bg-red-100 text-red-800"
      title="SMS notification failed"
    >
      <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
      </svg>
      <span>SMS Failed</span>
    </span>

    <!-- SMS Disabled Badge -->
    <span
      v-else-if="status === 'disabled'"
      class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium rounded-full bg-gray-100 text-gray-600"
      title="SMS notifications are disabled"
    >
      <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z" clip-rule="evenodd"/>
      </svg>
      <span>SMS Off</span>
    </span>
  </div>
</template>

<script>
export default {
  name: 'SmsNotificationBadge',
  props: {
    status: {
      type: String,
      default: null,
      validator: (value) => ['sent', 'pending', 'failed', 'disabled', null].includes(value)
    },
    show: {
      type: Boolean,
      default: true
    }
  }
}
</script>

<style scoped>
/* Optional: Add custom animations */
@keyframes pulse-green {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.7;
  }
}

.bg-green-100 {
  animation: pulse-green 2s ease-in-out infinite;
}
</style>
