<template>
  <div
    v-if="isVisible"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 backdrop-blur-sm"
    @click.self="handleCancel"
  >
    <div
      class="bg-gradient-to-br from-blue-900 via-blue-800 to-blue-900 rounded-2xl shadow-2xl max-w-md w-full transform transition-all duration-300 border border-blue-400/30 relative overflow-hidden"
      @click.stop
    >
      <!-- Animated Background Pattern -->
      <div class="absolute inset-0 overflow-hidden">
        <div class="absolute inset-0 opacity-10">
          <div class="grid grid-cols-8 gap-4 h-full transform rotate-12">
            <div
              v-for="i in 32"
              :key="i"
              class="bg-white rounded-full w-1 h-1 animate-pulse"
              :style="{ animationDelay: i * 0.1 + 's' }"
            ></div>
          </div>
        </div>
        <!-- Floating medical icons -->
        <div class="absolute inset-0">
          <div
            v-for="i in 6"
            :key="i"
            class="absolute text-white opacity-5 animate-float"
            :style="{
              left: Math.random() * 100 + '%',
              top: Math.random() * 100 + '%',
              animationDelay: Math.random() * 3 + 's',
              animationDuration: Math.random() * 3 + 2 + 's',
              fontSize: Math.random() * 16 + 8 + 'px'
            }"
          >
            <i
              :class="[
                'fas',
                [
                  'fa-edit',
                  'fa-redo-alt',
                  'fa-file-alt',
                  'fa-check-circle',
                  'fa-sync-alt',
                  'fa-arrow-right'
                ][Math.floor(Math.random() * 6)]
              ]"
            ></i>
          </div>
        </div>
      </div>

      <!-- Header -->
      <div class="relative z-10 p-6 pb-4 border-b border-blue-400/20">
        <div class="flex items-center space-x-4">
          <!-- Icon -->
          <div
            class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg border border-blue-300/50 relative overflow-hidden"
          >
            <div
              class="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent rounded-xl"
            ></div>
            <i class="fas fa-edit text-white text-xl relative z-10 drop-shadow-lg"></i>
            <div class="absolute top-1 right-1 w-1 h-1 bg-white/60 rounded-full animate-ping"></div>
          </div>

          <!-- Title -->
          <div class="flex-1">
            <h3 class="text-xl font-bold text-white mb-1 flex items-center">
              <i class="fas fa-question-circle mr-2 text-blue-300"></i>
              Edit & Resubmit Request?
            </h3>
            <div class="flex items-center space-x-2">
              <span
                class="text-xs text-blue-300 font-medium bg-blue-500/20 px-2 py-1 rounded-full border border-blue-400/30"
              >
                <i class="fas fa-sync-alt mr-1 text-xs"></i>
                Confirmation Required
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Content -->
      <div class="relative z-10 p-6 space-y-4">
        <!-- Main Question -->
        <div class="text-center mb-4">
          <p class="text-white text-lg font-medium mb-4">
            Do you want to edit and resubmit this cancelled request?
          </p>
        </div>

        <!-- Request Details Card -->
        <div class="bg-blue-600/20 border border-blue-400/30 rounded-xl p-4 backdrop-blur-sm">
          <div class="space-y-3">
            <!-- Request ID -->
            <div class="flex items-center justify-between">
              <span class="text-blue-200 text-sm font-medium flex items-center">
                <i class="fas fa-tag mr-2 text-blue-300"></i>
                Request ID:
              </span>
              <span
                class="text-white font-bold text-sm bg-blue-500/20 px-2 py-1 rounded border border-blue-400/30"
              >
                {{ requestData.id }}
              </span>
            </div>

            <!-- Type -->
            <div class="flex items-center justify-between">
              <span class="text-blue-200 text-sm font-medium flex items-center">
                <i class="fas fa-layer-group mr-2 text-blue-300"></i>
                Type:
              </span>
              <span class="text-white font-medium text-sm">{{ requestData.type }}</span>
            </div>

            <!-- Status -->
            <div class="flex items-center justify-between">
              <span class="text-blue-200 text-sm font-medium flex items-center">
                <i class="fas fa-info-circle mr-2 text-blue-300"></i>
                Status:
              </span>
              <span
                class="text-red-300 font-medium text-sm bg-red-500/20 px-2 py-1 rounded border border-red-400/30 flex items-center"
              >
                <i class="fas fa-ban mr-1 text-xs"></i>
                {{ requestData.status }}
              </span>
            </div>
          </div>
        </div>

        <!-- Information Message -->
        <div class="bg-green-500/10 border border-green-400/30 rounded-xl p-4 backdrop-blur-sm">
          <div class="flex items-start space-x-3">
            <div
              class="w-5 h-5 bg-green-500/20 rounded-full flex items-center justify-center mt-0.5"
            >
              <i class="fas fa-info text-green-400 text-xs"></i>
            </div>
            <p class="text-green-100 text-sm">
              You will be able to modify the request details before resubmitting.
            </p>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex space-x-3 pt-4">
          <!-- Cancel Button -->
          <button
            @click="handleCancel"
            :disabled="processing"
            class="flex-1 px-4 py-3 bg-gray-600/20 hover:bg-gray-600/30 border border-gray-400/30 rounded-xl text-gray-300 font-medium transition-all duration-300 flex items-center justify-center space-x-2 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <i class="fas fa-times text-sm"></i>
            <span>Cancel</span>
          </button>

          <!-- Confirm Button -->
          <button
            @click="handleConfirm"
            :disabled="processing"
            class="flex-1 px-4 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-xl font-medium transition-all duration-300 flex items-center justify-center space-x-2 shadow-lg hover:shadow-xl transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
          >
            <i v-if="processing" class="fas fa-spinner fa-spin text-sm"></i>
            <i v-else class="fas fa-check text-sm"></i>
            <span>{{ processing ? 'Processing...' : 'OK' }}</span>
          </button>
        </div>
      </div>

      <!-- Loading Overlay -->
      <div
        v-if="processing"
        class="absolute inset-0 bg-blue-900/50 backdrop-blur-sm flex items-center justify-center z-20 rounded-2xl"
      >
        <div class="bg-blue-800/80 rounded-xl p-4 shadow-xl border border-blue-400/30">
          <div class="flex items-center space-x-3 text-white">
            <i class="fas fa-spinner fa-spin text-lg text-blue-300"></i>
            <span class="font-medium">Processing request...</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import { ref } from 'vue'

  export default {
    name: 'ResubmitConfirmationModal',

    props: {
      isVisible: {
        type: Boolean,
        default: false
      },
      requestData: {
        type: Object,
        default: () => ({
          id: 'REQ-000000',
          type: 'Combined Access',
          status: 'Cancelled'
        })
      }
    },

    emits: ['confirm', 'cancel'],

    setup(props, { emit }) {
      const processing = ref(false)

      const handleConfirm = async () => {
        processing.value = true

        // Add slight delay for better UX
        await new Promise((resolve) => setTimeout(resolve, 500))

        emit('confirm')
        processing.value = false
      }

      const handleCancel = () => {
        if (!processing.value) {
          emit('cancel')
        }
      }

      return {
        processing,
        handleConfirm,
        handleCancel
      }
    }
  }
</script>

<style scoped>
  /* Medical Glass morphism effects */
  .bg-gradient-to-br {
    background-image: linear-gradient(to bottom right, var(--tw-gradient-stops));
  }

  /* Floating animation */
  @keyframes float {
    0%,
    100% {
      transform: translateY(0px) rotate(0deg);
    }
    50% {
      transform: translateY(-10px) rotate(180deg);
    }
  }

  .animate-float {
    animation: float 4s ease-in-out infinite;
  }

  /* Backdrop blur for older browsers */
  .backdrop-blur-sm {
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
  }

  /* Custom scrollbar if needed */
  ::-webkit-scrollbar {
    width: 6px;
  }

  ::-webkit-scrollbar-track {
    background: transparent;
  }

  ::-webkit-scrollbar-thumb {
    background-color: rgba(59, 130, 246, 0.3);
    border-radius: 3px;
  }

  ::-webkit-scrollbar-thumb:hover {
    background-color: rgba(59, 130, 246, 0.5);
  }

  /* Responsive adjustments */
  @media (max-width: 640px) {
    .max-w-md {
      max-width: calc(100vw - 2rem);
    }
  }
</style>
