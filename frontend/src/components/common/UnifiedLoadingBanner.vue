<template>
  <div
    v-if="show"
    class="fixed inset-0 z-40 flex items-center justify-center pointer-events-none animate-fade-in"
  >
    <div class="pointer-events-auto w-full max-w-lg mx-auto">
      <!-- Main Loading Card -->
      <div
        class="relative bg-gradient-to-br from-white/20 via-blue-50/30 to-white/10 border-2 border-blue-300/50 backdrop-blur-xl rounded-3xl p-8 shadow-2xl overflow-hidden"
      >
        <!-- Animated Background Pattern -->
        <div class="absolute inset-0 opacity-10">
          <div
            class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent animate-slide-across"
          ></div>
          <div
            class="absolute top-4 left-4 w-16 h-16 rounded-full animate-float"
            style="background: rgba(255, 0, 0, 0.2)"
          ></div>
          <div
            class="absolute bottom-4 right-4 w-20 h-20 bg-blue-400/20 rounded-full animate-float"
            style="animation-delay: 1s"
          ></div>
          <div
            class="absolute top-1/2 left-8 w-12 h-12 rounded-full animate-float"
            style="background: rgba(255, 0, 0, 0.15); animation-delay: 2s"
          ></div>
        </div>

        <!-- Hospital Logos and Branding -->
        <div class="relative z-10">
          <div class="flex items-center justify-center gap-6 mb-6">
            <!-- Left Logo -->
            <div class="relative">
              <div
                class="w-16 h-16 bg-gradient-to-br from-blue-500/30 to-teal-500/30 rounded-2xl backdrop-blur-sm border-2 border-blue-300/50 flex items-center justify-center shadow-xl hover:scale-105 transition-transform duration-300"
              >
                <img
                  src="/assets/images/ngao2.png"
                  alt="National Shield"
                  class="max-w-12 max-h-12 object-contain drop-shadow-lg"
                />
              </div>
              <div
                class="absolute -inset-1 bg-gradient-to-r from-blue-400 to-teal-400 rounded-2xl opacity-20 blur-sm animate-pulse"
              ></div>
            </div>

            <!-- Center Loading Spinner -->
            <div class="relative flex flex-col items-center">
              <!-- Main Spinner -->
              <div class="relative mb-3">
                <!-- Base circle -->
                <div class="rounded-full h-12 w-12 border-2 border-gray-300/20"></div>
                <!-- Red orbiting segment -->
                <div
                  :class="[
                    'absolute inset-0 rounded-full h-12 w-12 border-4 border-transparent shadow-lg',
                    forceSpin ? 'always-spin' : 'animate-spin'
                  ]"
                  style="border-top-color: #ff0000; animation-duration: 2s"
                ></div>
                <!-- Blue orbiting segment -->
                <div
                  :class="[
                    'absolute inset-0 rounded-full h-12 w-12 border-4 border-transparent shadow-lg',
                    forceSpin ? 'always-spin' : 'animate-spin'
                  ]"
                  style="
                    border-bottom-color: #0000d1;
                    animation-direction: reverse;
                    animation-duration: 1.5s;
                  "
                ></div>
                <!-- Additional red segment for more dynamic effect -->
                <div
                  :class="[
                    'absolute inset-0 rounded-full h-12 w-12 border-4 border-transparent shadow-lg',
                    forceSpin ? 'always-spin' : 'animate-spin'
                  ]"
                  style="border-right-color: rgba(255, 0, 0, 0.7); animation-duration: 2.5s"
                ></div>
                <!-- Additional blue segment -->
                <div
                  :class="[
                    'absolute inset-0 rounded-full h-12 w-12 border-4 border-transparent shadow-lg',
                    forceSpin ? 'always-spin' : 'animate-spin'
                  ]"
                  style="
                    border-left-color: rgba(0, 0, 209, 0.7);
                    animation-direction: reverse;
                    animation-duration: 1.8s;
                  "
                ></div>
              </div>

              <!-- Loading Text -->
              <div class="text-center">
                <div class="text-white text-2xl font-bold tracking-wide mb-1 drop-shadow-md">
                  {{ loadingTitle }}
                </div>
                <div class="text-blue-100 text-lg animate-pulse">{{ loadingSubtitle }}</div>
              </div>
            </div>

            <!-- Right Logo -->
            <div class="relative">
              <div
                class="w-16 h-16 bg-gradient-to-br from-teal-500/30 to-blue-500/30 rounded-2xl backdrop-blur-sm border-2 border-teal-300/50 flex items-center justify-center shadow-xl hover:scale-105 transition-transform duration-300"
              >
                <img
                  src="/assets/images/logo2.png"
                  alt="MNH Logo"
                  class="max-w-12 max-h-12 object-contain drop-shadow-lg"
                />
              </div>
              <div
                class="absolute -inset-1 bg-gradient-to-r from-teal-400 to-blue-400 rounded-2xl opacity-20 blur-sm animate-pulse"
                style="animation-delay: 0.5s"
              ></div>
            </div>
          </div>

          <!-- Hospital Name and Department -->
          <div class="text-center border-t border-blue-300/30 pt-4">
            <div
              class="text-lg font-black tracking-widest mb-1 drop-shadow-sm"
              style="color: #ff0000"
            >
              MUHIMBILI NATIONAL HOSPITAL
            </div>
            <div class="text-teal-200 text-base font-semibold tracking-wider">
              {{ departmentTitle }}
            </div>
          </div>

          <!-- Progress Indicators -->
          <div class="flex justify-center gap-2 mt-4">
            <div class="w-2 h-2 rounded-full animate-bounce" style="background: #ff0000"></div>
            <div
              class="w-2 h-2 rounded-full animate-bounce"
              style="background: #0000d1; animation-delay: 0.1s"
            ></div>
            <div
              class="w-2 h-2 rounded-full animate-bounce"
              style="background: #ff0000; animation-delay: 0.2s"
            ></div>
            <div
              class="w-2 h-2 rounded-full animate-bounce"
              style="background: #0000d1; animation-delay: 0.3s"
            ></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    name: 'UnifiedLoadingBanner',
    props: {
      show: {
        type: Boolean,
        default: true
      },
      loadingTitle: {
        type: String,
        default: 'Loading Requests'
      },
      loadingSubtitle: {
        type: String,
        default: 'Please wait...'
      },
      departmentTitle: {
        type: String,
        default: 'SYSTEM MANAGEMENT'
      },
      forceSpin: {
        type: Boolean,
        default: false
      }
    }
  }
</script>

<style scoped>
  /* Glass morphism effects */
  .booking-glass-card {
    background: rgba(59, 130, 246, 0.15);
    backdrop-filter: blur(25px);
    -webkit-backdrop-filter: blur(25px);
    border: 2px solid rgba(96, 165, 250, 0.3);
    box-shadow:
      0 8px 32px rgba(29, 78, 216, 0.4),
      inset 0 1px 0 rgba(255, 255, 255, 0.1);
  }

  /* Animations */
  @keyframes spinner-rotate {
    0% {
      transform: rotate(0deg);
    }
    100% {
      transform: rotate(360deg);
    }
  }

  .always-spin {
    animation-name: spinner-rotate;
    animation-timing-function: linear;
    animation-iteration-count: infinite;
  }

  @keyframes float {
    0%,
    100% {
      transform: translateY(0px);
    }
    50% {
      transform: translateY(-15px);
    }
  }

  @keyframes fade-in {
    from {
      opacity: 0;
      transform: translateY(20px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  @keyframes slide-across {
    0% {
      transform: translateX(-100%);
    }
    100% {
      transform: translateX(100%);
    }
  }

  .animate-float {
    animation: float 6s ease-in-out infinite;
  }

  .animate-fade-in {
    animation: fade-in 1s ease-out;
  }

  .animate-slide-across {
    animation: slide-across 2.5s ease-in-out infinite;
  }

  /* Focus styles for accessibility */
  input:focus,
  select:focus {
    box-shadow: 0 0 0 3px rgba(45, 212, 191, 0.1);
  }

  button:focus {
    box-shadow: 0 0 0 3px rgba(45, 212, 191, 0.3);
  }

  /* Smooth transitions */
  * {
    transition-property:
      color, background-color, border-color, text-decoration-color, fill, stroke, opacity,
      box-shadow, transform, filter, backdrop-filter;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 200ms;
  }

  /* Accessibility - Reduced Motion */
  @media (prefers-reduced-motion: reduce) {
    .animate-spin {
      animation: none !important;
    }

    .animate-float {
      animation: none !important;
    }

    .animate-bounce {
      animation: pulse 2s ease-in-out infinite alternate;
    }

    .animate-slide-across {
      animation: none !important;
    }

    @keyframes pulse {
      from {
        opacity: 0.4;
      }
      to {
        opacity: 1;
      }
    }
  }
</style>
