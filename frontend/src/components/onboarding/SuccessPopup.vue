<template>
  <div
    class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 backdrop-blur-sm"
    style="backdrop-filter: blur(12px)"
  >
    <div
      class="bg-white rounded-2xl w-full max-w-xl min-h-[520px] transform transition-all duration-500 scale-100 animate-slideUp border border-blue-100 shadow-2xl overflow-hidden"
      style="
        box-shadow:
          0 25px 50px -12px rgba(30, 64, 175, 0.45),
          0 15px 35px -12px rgba(37, 99, 235, 0.35),
          0 0 0 1px rgba(255, 255, 255, 0.8);
      "
    >
      <!-- Header -->
      <div
        class="bg-gradient-to-r from-blue-600 via-blue-700 to-blue-800 p-8 text-center shadow-lg"
      >
        <!-- Trophy Icon -->
        <div
          class="w-16 h-16 bg-gradient-to-br from-yellow-400 to-yellow-600 rounded-md flex items-center justify-center mx-auto mb-4 shadow-lg animate-pulse"
        >
          <i class="fas fa-trophy text-white text-xl drop-shadow-lg"></i>
        </div>

        <h2 class="text-3xl font-bold text-white mb-2 drop-shadow-lg">Congratulations!</h2>
        <p class="text-blue-100 text-lg font-medium drop-shadow-sm">{{ userRoleDisplay }}</p>
      </div>

      <!-- Body -->
      <div class="px-8 pb-8 bg-blue-50/40">
        <!-- User Name -->
        <div class="text-center mb-6">
          <h3 class="text-2xl font-bold text-blue mb-3 drop-shadow-sm">
            {{ userName }}
          </h3>
          <p class="text-blue text-lg leading-relaxed font-medium opacity-90">
            You have successfully completed the onboarding process. Now you can proceed with your
            requests.
          </p>
        </div>

        <!-- Progress Steps - Compact -->
        <div class="space-y-3 mb-6">
          <div
            class="flex items-center space-x-3 p-3 bg-white/10 border border-white/20 backdrop-blur-sm"
          >
            <div
              class="w-5 h-5 bg-green-500 rounded-sm flex items-center justify-center flex-shrink-0"
            >
              <i class="fas fa-check text-white text-sm"></i>
            </div>
            <span class="text-blue font-medium text-base">Terms of Service Accepted</span>
          </div>

          <div
            class="flex items-center space-x-3 p-3 bg-white/10 border border-white/20 backdrop-blur-sm"
          >
            <div
              class="w-5 h-5 bg-green-500 rounded-sm flex items-center justify-center flex-shrink-0"
            >
              <i class="fas fa-check text-white text-sm"></i>
            </div>
            <span class="text-blue font-medium text-base">ICT Policy Acknowledged</span>
          </div>

          <div
            class="flex items-center space-x-3 p-3 bg-white/10 border border-white/20 backdrop-blur-sm"
          >
            <div
              class="w-5 h-5 bg-green-500 rounded-sm flex items-center justify-center flex-shrink-0"
            >
              <i class="fas fa-check text-white text-sm"></i>
            </div>
            <span class="text-blue font-medium text-base">Declaration Form Submitted</span>
          </div>
        </div>

        <!-- Action Button -->
        <button
          @click="handleContinue"
          class="w-full bg-gradient-to-r from-blue/20 to-blue/10 hover:from-white/30 hover:to-blue/20 text-blue py-4 px-6 rounded-none font-semibold text-lg transition-all duration-300 transform hover:scale-105 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-white/50 border border-white/30 backdrop-blur-sm relative overflow-hidden group"
        >
          <div class="relative z-10 flex items-center justify-center">
            <i class="fas fa-arrow-right mr-2 text-lg"></i>
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
    .max-w-xl {
      max-width: 95vw;
    }

    .px-8 {
      padding-left: 1rem;
      padding-right: 1rem;
    }

    .pb-8 {
      padding-bottom: 1rem;
    }

    .p-8 {
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
