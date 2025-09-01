<template>
  <div
    class="fixed inset-0 bg-black/60 backdrop-blur-md flex items-center justify-center z-50 p-4"
    style="backdrop-filter: blur(12px)"
  >
    <div
      class="bg-gradient-to-br from-blue-50 via-white to-blue-100 rounded-3xl max-w-md w-full transform transition-all duration-500 scale-100 animate-slideUp border-2 border-blue-300/60"
      style="
        box-shadow: 0 35px 60px -12px rgba(29, 78, 216, 0.5),
          0 25px 50px -12px rgba(59, 130, 246, 0.3),
          0 0 0 1px rgba(59, 130, 246, 0.4),
          inset 0 2px 0 rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(20px);
      "
    >
      <!-- Header -->
      <div
        class="bg-gradient-to-r from-blue-900 via-blue-800 to-blue-700 rounded-t-3xl p-6 text-center relative overflow-hidden"
      >
        <!-- Background pattern -->
        <div class="absolute inset-0 opacity-15">
          <div
            class="absolute inset-0 bg-gradient-to-r from-blue-600/30 to-cyan-600/30 animate-pulse"
          ></div>
          <div
            class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-blue-400 to-transparent"
          ></div>
        </div>

        <div class="relative z-10">
          <div
            class="w-12 h-12 bg-gradient-to-br from-blue-400 via-blue-500 to-cyan-600 rounded-xl flex items-center justify-center mx-auto mb-3 shadow-2xl border-2 border-white/30"
          >
            <div
              class="absolute inset-0 bg-gradient-to-br from-blue-300/50 to-transparent rounded-xl"
            ></div>
            <i
              class="fas fa-trophy text-white text-lg drop-shadow-lg relative z-10"
            ></i>
          </div>
          <h2 class="text-xl font-bold text-white mb-1 drop-shadow-lg">
            Congratulations!
          </h2>
          <p class="text-blue-100 text-sm font-medium drop-shadow-sm">
            Head of Department
          </p>
        </div>
      </div>

      <!-- Body -->
      <div class="p-6">
        <!-- User Info -->
        <div class="text-center mb-6">
          <div
            class="w-16 h-16 bg-gradient-to-br from-blue-500 via-blue-600 to-cyan-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-xl border-2 border-blue-400/60"
          >
            <i class="fas fa-user-tie text-white text-xl drop-shadow-sm"></i>
          </div>
          <h3 class="text-lg font-bold text-blue-800 mb-2 drop-shadow-sm">
            {{ userName }}
          </h3>
          <p class="text-blue-600 text-sm leading-relaxed font-medium">
            {{ message }}
          </p>
        </div>

        <!-- Progress Steps -->
        <div class="space-y-3 mb-6">
          <div
            class="flex items-center space-x-3 p-2 bg-gradient-to-r from-blue-100 to-blue-50 rounded-lg border border-blue-300/60 shadow-sm hover:shadow-md transition-all duration-300"
          >
            <div
              class="w-6 h-6 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center flex-shrink-0 shadow-lg"
            >
              <i class="fas fa-check text-white text-xs drop-shadow-sm"></i>
            </div>
            <span class="text-blue-800 font-semibold text-sm"
              >Terms of Service Accepted</span
            >
          </div>

          <div
            class="flex items-center space-x-3 p-2 bg-gradient-to-r from-blue-200 to-cyan-100 rounded-lg border border-blue-400/60 shadow-sm hover:shadow-md transition-all duration-300"
          >
            <div
              class="w-6 h-6 bg-gradient-to-br from-blue-600 to-cyan-600 rounded-lg flex items-center justify-center flex-shrink-0 shadow-lg"
            >
              <i class="fas fa-check text-white text-xs drop-shadow-sm"></i>
            </div>
            <span class="text-blue-900 font-semibold text-sm"
              >ICT Policy Acknowledged</span
            >
          </div>

          <div
            class="flex items-center space-x-3 p-2 bg-gradient-to-r from-blue-300 to-blue-100 rounded-lg border border-blue-500/60 shadow-sm hover:shadow-md transition-all duration-300"
          >
            <div
              class="w-6 h-6 bg-gradient-to-br from-blue-700 to-blue-800 rounded-lg flex items-center justify-center flex-shrink-0 shadow-lg"
            >
              <i class="fas fa-check text-white text-xs drop-shadow-sm"></i>
            </div>
            <span class="text-blue-900 font-semibold text-sm"
              >Declaration Form Submitted</span
            >
          </div>
        </div>

        <!-- Action Button -->
        <button
          @click="handleContinue"
          class="w-full bg-gradient-to-r from-blue-600 via-blue-700 to-blue-800 hover:from-blue-700 hover:via-blue-800 hover:to-blue-900 text-white py-3 px-4 rounded-xl font-bold text-base transition-all duration-400 transform hover:scale-105 hover:shadow-2xl focus:outline-none focus:ring-4 focus:ring-blue-300 focus:ring-offset-2 shadow-xl border-2 border-blue-500/50 relative overflow-hidden group"
        >
          <div
            class="absolute inset-0 bg-gradient-to-r from-blue-400/30 to-cyan-400/30 opacity-0 group-hover:opacity-100 transition-opacity duration-400"
          ></div>
          <div
            class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity duration-400"
          ></div>
          <div class="relative z-10 flex items-center justify-center">
            <i class="fas fa-arrow-right mr-2 text-base drop-shadow-lg"></i>
            <span class="drop-shadow-lg">Continue to Dashboard</span>
          </div>
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'SuccessPopup',
  props: {
    userName: {
      type: String,
      required: true
    },
    message: {
      type: String,
      default:
        'You have successfully completed the onboarding process. Now you can proceed with your requests.'
    }
  },
  emits: ['continue'],
  methods: {
    handleContinue() {
      console.log('🎯 SuccessPopup: Continue button clicked')
      console.log('🚀 SuccessPopup: Emitting continue event')
      this.$emit('continue')
    }
  }
}
</script>

<style scoped>
/* Enhanced slide up animation */
@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(30px) scale(0.95);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

.animate-slideUp {
  animation: slideUp 0.5s ease-out;
}

/* Floating animation for trophy */
@keyframes float {
  0%,
  100% {
    transform: translateY(0px);
  }
  50% {
    transform: translateY(-5px);
  }
}

.animate-float {
  animation: float 3s ease-in-out infinite;
}

/* Simple backdrop blur */
.backdrop-blur-sm {
  backdrop-filter: blur(4px);
}

/* Responsive design */
@media (max-width: 640px) {
  .max-w-md {
    max-width: 90vw;
  }

  .p-6 {
    padding: 1rem;
  }
}

/* Accessibility */
@media (prefers-reduced-motion: reduce) {
  .animate-slideUp {
    animation: none;
  }
}

/* Focus styles */
button:focus {
  outline: 2px solid #3b82f6;
  outline-offset: 2px;
}
</style>
"
