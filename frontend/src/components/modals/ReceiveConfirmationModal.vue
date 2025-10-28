<template>
  <div
    v-if="isVisible"
    class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4"
    @click.self="$emit('cancel')"
  >
    <div
      class="relative bg-blue-900 rounded-2xl shadow-2xl max-w-2xl w-full min-h-[440px] border border-blue-400/30 overflow-hidden animate-[modalEnter_.25s_ease-out]"
      @click.stop
    >
      <!-- Header -->
      <div class="relative z-10 p-8 pb-6 border-b border-blue-400/20">
        <div class="flex items-center gap-4">
          <div
            class="w-14 h-14 rounded-xl bg-gradient-to-br from-emerald-600 to-green-700 flex items-center justify-center shadow-lg border border-emerald-300/50"
          >
            <i class="fas fa-undo text-white text-xl"></i>
          </div>
          <div class="flex-1">
            <h3 class="text-4xl font-extrabold text-white tracking-wide">Receive Device?</h3>
            <p class="text-blue-200 text-lg">Confirm to complete this request.</p>
          </div>
        </div>
      </div>

      <!-- Body -->
      <div class="relative z-10 p-8 space-y-5">
        <div
          class="grid grid-cols-1 gap-4 bg-blue-800/20 border border-blue-400/30 rounded-xl p-6 min-h-[220px]"
        >
          <div class="flex items-center justify-between">
            <span class="text-blue-200 text-lg font-semibold flex items-center"
              ><i class="fas fa-hashtag mr-2 text-blue-300"></i>Request</span
            >
            <span class="text-white font-mono font-bold text-2xl">
              {{ requestData.request_id }}
            </span>
          </div>
          <div class="flex items-center justify-between">
            <span class="text-blue-200 text-lg font-semibold flex items-center"
              ><i class="fas fa-laptop mr-2 text-blue-300"></i>Device</span
            >
            <span class="text-white font-semibold truncate max-w-[60%] text-right text-2xl">
              {{ requestData.device }}
            </span>
          </div>
          <div class="text-blue-100 text-lg">
            This will mark the booking as returned (or compromised based on assessment) and update
            inventory.
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="relative z-10 p-8 pt-6 border-t border-blue-400/20 flex gap-5">
        <button
          class="flex-1 px-6 py-4 bg-white/10 hover:bg-white/15 text-blue-100 border border-white/10 rounded-xl transition-all duration-300 text-lg"
          @click="$emit('cancel')"
        >
          <i class="fas fa-arrow-left mr-2"></i>Cancel
        </button>
        <button
          class="flex-1 px-6 py-4 bg-gradient-to-r from-emerald-600 to-green-700 hover:from-emerald-700 hover:to-green-800 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:scale-[1.02] transition-all duration-300 text-lg"
          @click="$emit('confirm')"
        >
          <i class="fas fa-check mr-2"></i>Confirm Receive
        </button>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    name: 'ReceiveConfirmationModal',
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
</style>
