<template>
  <div
    v-if="isVisible"
    class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4"
    @click.self="$emit('close')"
  >
    <div
      class="relative bg-gradient-to-br from-blue-900 via-blue-800 to-teal-900 rounded-2xl shadow-2xl max-w-xl w-full border border-blue-400/30 overflow-hidden animate-[modalEnter_.25s_ease-out]"
      @click.stop
    >
      <!-- Decorative background -->
      <div class="pointer-events-none absolute inset-0 opacity-10">
        <div class="absolute inset-0">
          <div
            v-for="i in 14"
            :key="i"
            class="absolute text-white animate-float"
            :style="{
              left: Math.random() * 100 + '%',
              top: Math.random() * 100 + '%',
              fontSize: Math.random() * 14 + 8 + 'px',
              animationDelay: Math.random() * 2 + 's',
              opacity: 0.12
            }"
          >
            <i
              :class="[
                'fas',
                ['fa-clipboard-check', 'fa-laptop', 'fa-check', 'fa-shield-alt'][
                  Math.floor(Math.random() * 4)
                ]
              ]"
            ></i>
          </div>
        </div>
      </div>

      <!-- Header -->
      <div class="relative z-10 p-6 pb-4 border-b border-blue-400/20">
        <div class="flex items-center gap-4">
          <div
            class="w-14 h-14 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-lg border border-blue-300/50"
          >
            <i class="fas fa-clipboard-check text-white text-xl"></i>
          </div>
          <div class="flex-1">
            <h3 class="text-2xl font-extrabold text-white tracking-wide">Issuing Saved</h3>
            <p class="text-blue-200 text-sm">Proceed to approval to continue the workflow.</p>
          </div>
        </div>
      </div>

      <!-- Body -->
      <div class="relative z-10 p-6 space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 border border-blue-400/30 rounded-xl p-4">
          <div class="flex items-center justify-between">
            <span class="text-blue-200 text-sm font-medium flex items-center"
              ><i class="fas fa-hashtag mr-2 text-blue-300"></i>Request</span
            >
            <span class="text-white font-mono font-bold">{{ requestData.request_id }}</span>
          </div>
          <div class="flex items-center justify-between">
            <span class="text-blue-200 text-sm font-medium flex items-center"
              ><i class="fas fa-laptop mr-2 text-blue-300"></i>Device</span
            >
            <span class="text-white font-medium truncate max-w-[60%] text-right">{{
              requestData.device
            }}</span>
          </div>
        </div>
        <div class="flex items-start gap-3 border border-blue-400/30 rounded-xl p-4">
          <div class="w-5 h-5 rounded-full bg-blue-500/20 flex items-center justify-center mt-0.5">
            <i class="fas fa-info text-blue-300 text-xs"></i>
          </div>
          <p class="text-blue-100 text-sm">
            You can approve or reject this request now. Approval will notify the requester and mark
            the device as in use.
          </p>
        </div>
      </div>

      <!-- Footer (Reject left, Approve right) -->
      <div
        class="relative z-10 p-6 pt-4 border-t border-blue-400/20 grid grid-cols-1 sm:grid-cols-2 gap-3"
      >
        <button
          class="px-4 py-3 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-300 order-2 sm:order-1"
          @click="$emit('reject')"
        >
          <i class="fas fa-times mr-2"></i>Reject Request
        </button>
        <button
          class="px-4 py-3 bg-gradient-to-r from-emerald-600 to-green-700 hover:from-emerald-700 hover:to-green-800 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:scale-[1.02] transition-all duration-300 order-1 sm:order-2"
          @click="$emit('approve')"
        >
          <i class="fas fa-check mr-2"></i>Approve Request
        </button>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    name: 'PostIssuingActionModal',
    props: {
      isVisible: { type: Boolean, default: false },
      requestData: {
        type: Object,
        default: () => ({ request_id: 'REQ-000000', device: 'Device' })
      }
    }
  }
</script>

<style scoped>
  @keyframes modalEnter {
    from {
      opacity: 0;
      transform: translateY(8px) scale(0.98);
    }
    to {
      opacity: 1;
      transform: translateY(0) scale(1);
    }
  }
  .animate-float {
    animation: float 6s ease-in-out infinite;
  }
  @keyframes float {
    0%,
    100% {
      transform: translateY(0);
    }
    50% {
      transform: translateY(-10px);
    }
  }
</style>
