<template>
  <div class="relative">
    <!-- Timer and Scroll Indicator -->
    <div class="fixed top-0 left-0 right-0 z-50">
      <div
        class="w-full px-6 py-2 flex items-center justify-center min-h-[80px]"
      >
        <!-- Timer Active State -->
        <div
          v-if="!timerFinished"
          class="flex items-center justify-center w-full max-w-lg"
        >
          <div
            class="bg-gradient-to-r from-amber-500 to-orange-500 text-white rounded-2xl px-6 py-4 shadow-xl border border-white/20 text-center"
          >
            <div class="flex items-center justify-center space-x-4">
              <div
                class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center animate-spin"
              >
                <i class="fas fa-clock text-lg"></i>
              </div>
              <div class="text-center">
                <div class="text-2xl font-bold text-white mb-1">
                  {{ formatTime(timeRemaining) }}
                </div>
                <p class="text-amber-100 text-xs">
                  Please wait {{ timeRemaining }}s
                </p>
              </div>
              <div class="w-12 h-12">
                <svg class="w-12 h-12 transform -rotate-90" viewBox="0 0 48 48">
                  <circle
                    cx="24"
                    cy="24"
                    r="20"
                    stroke="rgba(255,255,255,0.2)"
                    stroke-width="3"
                    fill="none"
                  />
                  <circle
                    cx="24"
                    cy="24"
                    r="20"
                    stroke="white"
                    stroke-width="3"
                    fill="none"
                    :stroke-dasharray="125.66"
                    :stroke-dashoffset="
                      125.66 - (125.66 * progressPercentage) / 100
                    "
                    class="transition-all duration-1000 ease-out"
                  />
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Scroll Down Indicator (after timer) -->
        <div v-else class="flex items-center justify-center w-full max-w-md">
          <div
            class="bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-2xl px-6 py-4 shadow-xl border border-white/20 text-center"
          >
            <div class="flex flex-col items-center space-y-2">
              <div
                class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center animate-bounce"
              >
                <i class="fas fa-arrow-down text-lg animate-pulse"></i>
              </div>
              <p class="text-white font-medium text-sm">Scroll Down</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main ICT Policy Content (with top padding to account for fixed timer) -->
    <div class="pt-24">
      <IctPolicyMain />
    </div>

    <!-- Floating Accept Button -->
    <div class="fixed bottom-20 left-1/2 transform -translate-x-1/2 z-40">
      <div
        class="bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-2xl px-8 py-4 shadow-2xl border border-white/20"
      >
        <div class="flex items-center space-x-4">
          <label class="flex items-center cursor-pointer">
            <input
              v-model="policyAccepted"
              type="checkbox"
              class="w-5 h-5 text-emerald-600 border-2 border-white rounded-lg focus:ring-emerald-400 focus:ring-2 mr-3 bg-white/20"
            />
            <span class="text-white font-medium">I accept the ICT Policy</span>
          </label>

          <button
            v-if="policyAccepted"
            @click="acceptPolicy"
            class="bg-white text-emerald-600 px-6 py-2 rounded-xl font-bold hover:bg-emerald-50 focus:outline-none focus:ring-4 focus:ring-white/50 transition-all duration-300 shadow-lg transform hover:scale-105"
          >
            <i class="fas fa-arrow-right mr-2"></i>
            Accept & Proceed
          </button>
        </div>
      </div>
    </div>

    <!-- Back Button -->
    <button
      @click="goBack"
      class="fixed bottom-6 left-6 w-14 h-14 bg-blue-600 hover:bg-blue-700 text-white rounded-full shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-110 focus:outline-none focus:ring-4 focus:ring-blue-300 focus:ring-offset-2 z-40 group"
      title="Go Back"
    >
      <i class="fas fa-arrow-left text-lg group-hover:animate-pulse"></i>
    </button>
  </div>
</template>

<script>
import IctPolicyMain from '../IctPolicy.vue'

export default {
  name: 'OnboardingIctPolicy',
  components: {
    IctPolicyMain
  },
  emits: ['policy-accepted', 'go-back'],
  data() {
    return {
      timeRemaining: 5, // 5 seconds timer
      timerFinished: false,
      policyAccepted: false,
      timer: null
    }
  },
  computed: {
    progressPercentage() {
      return ((5 - this.timeRemaining) / 5) * 100
    }
  },
  mounted() {
    this.startTimer()
  },
  beforeUnmount() {
    if (this.timer) {
      clearInterval(this.timer)
    }
  },
  methods: {
    startTimer() {
      this.timer = setInterval(() => {
        if (this.timeRemaining > 0) {
          this.timeRemaining--
        } else {
          this.timerFinished = true
          clearInterval(this.timer)
        }
      }, 1000)
    },
    formatTime(seconds) {
      const minutes = Math.floor(seconds / 60)
      const remainingSeconds = seconds % 60
      return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`
    },
    acceptPolicy() {
      // Show success message
      this.showSuccessMessage(
        'ICT Policy accepted successfully! You may now proceed with your access request.'
      )

      // Emit event after a short delay to show the message
      setTimeout(() => {
        this.$emit('policy-accepted')
      }, 2000)
    },

    showSuccessMessage(message) {
      // Create success toast notification
      const toast = document.createElement('div')
      toast.className =
        'fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-gradient-to-r from-emerald-500 to-teal-500 text-white px-8 py-6 rounded-2xl shadow-2xl z-50 max-w-md text-center border border-white/20'
      toast.innerHTML = `
        <div class="flex items-center justify-center mb-3">
          <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center mr-3">
            <i class="fas fa-check-circle text-2xl"></i>
          </div>
          <h3 class="text-xl font-bold">Success!</h3>
        </div>
        <p class="text-emerald-100 leading-relaxed">${message}</p>
      `

      // Add backdrop
      const backdrop = document.createElement('div')
      backdrop.className = 'fixed inset-0 bg-black/50 z-40 backdrop-blur-sm'

      document.body.appendChild(backdrop)
      document.body.appendChild(toast)

      // Remove after 2 seconds
      setTimeout(() => {
        if (document.body.contains(toast)) {
          document.body.removeChild(toast)
        }
        if (document.body.contains(backdrop)) {
          document.body.removeChild(backdrop)
        }
      }, 2000)
    },
    goBack() {
      this.$emit('go-back')
    }
  }
}
</script>

<style scoped>
/* Ensure the timer overlay stays on top */
.fixed {
  position: fixed;
  z-index: 50;
}
</style>
"
