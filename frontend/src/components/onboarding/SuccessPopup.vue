<template>
  <div
    class="fixed inset-0 bg-gradient-to-br from-blue-900/80 via-blue-800/80 to-blue-700/80 backdrop-blur-md flex items-center justify-center z-50 p-4"
    style="backdrop-filter: blur(12px)"
  >
    <div
      class="bg-gradient-to-br from-blue-700 via-blue-600 to-blue-800 rounded-2xl max-w-sm w-full transform transition-all duration-500 scale-100 animate-slideUp border border-blue-300/30 shadow-2xl"
      style="
        box-shadow:
          0 25px 50px -12px rgba(29, 78, 216, 0.6),
          0 15px 35px -12px rgba(59, 130, 246, 0.4),
          0 0 0 1px rgba(255, 255, 255, 0.1),
          inset 0 2px 0 rgba(255, 255, 255, 0.2);
      "
    >
      <!-- Header -->
      <div class="text-center p-5 relative overflow-hidden">
        <!-- Trophy Icon -->
        <div
          class="w-10 h-10 bg-gradient-to-br from-yellow-400 to-yellow-600 rounded-xl flex items-center justify-center mx-auto mb-3 shadow-lg animate-pulse"
        >
          <i class="fas fa-trophy text-white text-sm drop-shadow-lg"></i>
        </div>

        <h2 class="text-lg font-bold text-white mb-1 drop-shadow-lg">Congratulations!</h2>
        <p class="text-blue-100 text-xs font-medium drop-shadow-sm">{{ userRoleDisplay }}</p>
      </div>

      <!-- Body -->
      <div class="px-5 pb-5">
        <!-- User Name -->
        <div class="text-center mb-4">
          <h3 class="text-sm font-bold text-white mb-2 drop-shadow-sm">
            {{ userName }}
          </h3>
          <p class="text-blue-100 text-xs leading-relaxed font-medium opacity-90">
            You have successfully completed the onboarding process. Now you can proceed with your
            requests.
          </p>
        </div>

        <!-- Progress Steps - Compact -->
        <div class="space-y-2 mb-4">
          <div
            class="flex items-center space-x-2 p-2 bg-white/10 rounded-lg border border-white/20 backdrop-blur-sm"
          >
            <div
              class="w-4 h-4 bg-green-500 rounded-full flex items-center justify-center flex-shrink-0"
            >
              <i class="fas fa-check text-white text-xs"></i>
            </div>
            <span class="text-white font-medium text-xs">Terms of Service Accepted</span>
          </div>

          <div
            class="flex items-center space-x-2 p-2 bg-white/10 rounded-lg border border-white/20 backdrop-blur-sm"
          >
            <div
              class="w-4 h-4 bg-green-500 rounded-full flex items-center justify-center flex-shrink-0"
            >
              <i class="fas fa-check text-white text-xs"></i>
            </div>
            <span class="text-white font-medium text-xs">ICT Policy Acknowledged</span>
          </div>

          <div
            class="flex items-center space-x-2 p-2 bg-white/10 rounded-lg border border-white/20 backdrop-blur-sm"
          >
            <div
              class="w-4 h-4 bg-green-500 rounded-full flex items-center justify-center flex-shrink-0"
            >
              <i class="fas fa-check text-white text-xs"></i>
            </div>
            <span class="text-white font-medium text-xs">Declaration Form Submitted</span>
          </div>
        </div>

        <!-- Action Button -->
        <button
          @click="handleContinue"
          class="w-full bg-gradient-to-r from-white/20 to-white/10 hover:from-white/30 hover:to-white/20 text-white py-2.5 px-4 rounded-lg font-semibold text-sm transition-all duration-300 transform hover:scale-105 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-white/50 border border-white/30 backdrop-blur-sm relative overflow-hidden group"
        >
          <div class="relative z-10 flex items-center justify-center">
            <i class="fas fa-arrow-right mr-2 text-sm"></i>
            <span>Continue to Dashboard</span>
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
      userRoleDisplay: {
        type: String,
        default: 'Staff Member'
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
    .max-w-sm {
      max-width: 95vw;
    }

    .px-5 {
      padding-left: 1rem;
      padding-right: 1rem;
    }

    .pb-5 {
      padding-bottom: 1rem;
    }

    .p-5 {
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
