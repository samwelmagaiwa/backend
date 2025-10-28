<template>
  <div
    v-if="isVisible"
    class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4"
    @click.self="$emit('cancel')"
  >
    <div
      class="relative bg-blue-900 rounded-2xl shadow-2xl max-w-3xl w-full border border-blue-400/30 overflow-hidden animate-[modalEnter_.25s_ease-out]"
      @click.stop
    >
      <!-- Floating icons background -->
      <div class="pointer-events-none absolute inset-0 opacity-10">
        <div class="absolute inset-0">
          <div
            v-for="i in 18"
            :key="i"
            class="absolute text-white animate-float"
            :style="{
              left: Math.random() * 100 + '%',
              top: Math.random() * 100 + '%',
              fontSize: Math.random() * 14 + 8 + 'px',
              animationDelay: Math.random() * 2 + 's',
              opacity: 0.15
            }"
          >
            <i
              :class="[
                'fas',
                ['fa-check', 'fa-shield-check', 'fa-badge-check', 'fa-circle-check'][
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
            class="w-16 h-16 rounded-xl bg-blue-600 flex items-center justify-center shadow-lg border border-blue-300/50"
          >
            <i class="fas fa-check text-white text-xl"></i>
          </div>
          <div class="flex-1">
            <h3 class="text-4xl font-extrabold text-white tracking-wide">Approve Request?</h3>
            <p class="text-blue-200 text-xl">Please review the details before confirming.</p>
          </div>
        </div>
      </div>

      <!-- Body -->
      <div class="relative z-10 p-6 space-y-4">
        <div
          class="grid grid-cols-1 sm:grid-cols-2 gap-3 bg-blue-800/20 border border-blue-400/30 rounded-xl p-4"
        >
          <div class="flex items-center justify-between">
            <span class="text-blue-200 text-xl font-semibold flex items-center">
              <i class="fas fa-hashtag mr-2 text-blue-300 text-xl"></i>
              Request
            </span>
            <span class="text-white text-2xl font-mono font-bold">{{
              requestData.request_id
            }}</span>
          </div>
          <div class="flex items-center justify-between">
            <span class="text-blue-200 text-xl font-semibold flex items-center">
              <i class="fas fa-user mr-2 text-blue-300 text-xl"></i>
              Borrower
            </span>
            <span class="text-white text-xl font-medium truncate max-w-[60%] text-right">{{
              requestData.borrower
            }}</span>
          </div>
          <div class="flex items-center justify-between">
            <span class="text-blue-200 text-xl font-semibold flex items-center">
              <i class="fas fa-laptop mr-2 text-blue-300 text-xl"></i>
              Device
            </span>
            <span class="text-white text-xl font-medium truncate max-w-[60%] text-right">{{
              requestData.device
            }}</span>
          </div>
          <div class="flex items-center justify-between">
            <span class="text-blue-200 text-xl font-semibold flex items-center">
              <i class="fas fa-clock mr-2 text-blue-300 text-xl"></i>
              Return
            </span>
            <span class="text-blue-100 text-xl font-medium">{{
              requestData.return_time || '—'
            }}</span>
          </div>
        </div>

        <div class="flex items-start gap-3 bg-blue-500/10 border border-blue-400/30 rounded-xl p-4">
          <div class="w-5 h-5 rounded-full bg-blue-500/20 flex items-center justify-center mt-0.5">
            <i class="fas fa-info text-blue-400 text-xs"></i>
          </div>
          <p class="text-blue-100 text-xl">
            Approval will notify the requester and move this booking to the issuing/receiving
            workflow.
          </p>
        </div>
      </div>

      <!-- Footer -->
      <div class="relative z-10 p-6 pt-4 border-t border-blue-400/20 flex gap-3">
        <button
          class="flex-1 px-5 py-4 bg-white/5 hover:bg-white/10 text-blue-100 border border-white/10 rounded-xl transition-all duration-300"
          @click="$emit('cancel')"
        >
          <i class="fas fa-times mr-2"></i>Cancel
        </button>
        <button
          class="flex-1 px-5 py-4 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:scale-[1.02] transition-all duration-300"
          @click="$emit('confirm')"
        >
          <i class="fas fa-check mr-2"></i>Approve Now
        </button>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    name: 'ApprovalConfirmationModal',
    props: {
      isVisible: { type: Boolean, default: false },
      requestData: {
        type: Object,
        default: () => ({
          request_id: 'REQ-000000',
          borrower: 'Unknown',
          device: 'Device',
          return_time: null
        })
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
