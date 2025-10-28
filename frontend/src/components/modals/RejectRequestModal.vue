<template>
  <div
    v-if="isVisible"
    class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4"
    @click.self="$emit('cancel')"
  >
    <div
      class="relative bg-gradient-to-br from-red-900 via-rose-900 to-red-800 rounded-2xl shadow-2xl max-w-lg w-full border border-red-400/30 overflow-hidden animate-[modalEnter_.25s_ease-out]"
      @click.stop
    >
      <!-- Header -->
      <div class="relative z-10 p-6 pb-4 border-b border-red-400/20">
        <div class="flex items-center gap-4">
          <div
            class="w-14 h-14 rounded-xl bg-gradient-to-br from-red-600 to-rose-600 flex items-center justify-center shadow-lg border border-red-300/50"
          >
            <i class="fas fa-times text-white text-xl"></i>
          </div>
          <div class="flex-1">
            <h3 class="text-2xl font-extrabold text-white tracking-wide">Reject Request</h3>
            <p class="text-red-200 text-sm">Provide a reason and confirm rejection.</p>
          </div>
        </div>
      </div>

      <!-- Body -->
      <div class="relative z-10 p-6 space-y-4">
        <div class="bg-red-800/20 border border-red-400/30 rounded-xl p-4">
          <label class="block text-red-100 text-sm font-semibold mb-2">Rejection Reason</label>
          <textarea
            v-model="notesLocal"
            rows="3"
            class="w-full px-3 py-2 bg-red-900/30 border-2 border-red-600/40 rounded-xl text-white placeholder-red-300 resize-none focus:outline-none focus:border-red-400 focus:shadow-lg focus:shadow-red-500/20 transition-all duration-300"
            placeholder="Explain why this request is being rejected..."
          ></textarea>
          <p v-if="showError" class="text-red-300 text-xs mt-2">Rejection reason is required.</p>
        </div>
      </div>

      <!-- Footer -->
      <div class="relative z-10 p-6 pt-4 border-t border-red-400/20 flex gap-3">
        <button
          class="flex-1 px-4 py-3 bg-white/10 hover:bg-white/15 text-red-100 border border-white/10 rounded-xl transition-all duration-300"
          @click="$emit('cancel')"
        >
          <i class="fas fa-arrow-left mr-2"></i>Cancel
        </button>
        <button
          class="flex-1 px-4 py-3 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:scale-[1.02] transition-all duration-300"
          @click="confirm"
        >
          <i class="fas fa-times mr-2"></i>Reject Request
        </button>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    name: 'RejectRequestModal',
    props: {
      isVisible: { type: Boolean, default: false },
      defaultNotes: { type: String, default: '' }
    },
    emits: ['confirm', 'cancel'],
    data() {
      return { notesLocal: this.defaultNotes, showError: false }
    },
    watch: {
      defaultNotes(val) {
        this.notesLocal = val
      }
    },
    methods: {
      confirm() {
        if (!this.notesLocal || !this.notesLocal.trim()) {
          this.showError = true
          return
        }
        this.showError = false
        this.$emit('confirm', this.notesLocal.trim())
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
