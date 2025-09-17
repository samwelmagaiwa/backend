<template>
  <div
    v-if="isVisible"
    class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-50 p-4"
    @click.self="closeModal"
  >
    <div
      class="bg-gradient-to-br from-blue-900 to-blue-800 rounded-2xl shadow-2xl border border-blue-400/30 max-w-md w-full transform transition-all duration-300 scale-100 opacity-100"
      :class="{ 'scale-95 opacity-0': !isVisible }"
    >
      <!-- Header -->
      <div class="p-6 border-b border-blue-400/20">
        <div class="flex items-center space-x-3">
          <div
            class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center shadow-lg"
          >
            <i class="fas fa-edit text-white text-lg"></i>
          </div>
          <div>
            <h3 class="text-xl font-bold text-white">Edit & Resubmit Request</h3>
            <p class="text-blue-200 text-sm">Modify and resubmit for approval</p>
          </div>
        </div>
      </div>

      <!-- Content -->
      <div class="p-6 space-y-4">
        <!-- Request Type Badge -->
        <div class="flex justify-center">
          <div
            class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-teal-600/30 to-blue-600/30 border border-teal-400/40 rounded-full"
          >
            <i class="fas fa-clipboard-list text-teal-300 mr-2"></i>
            <span class="text-white font-semibold text-sm">{{ requestTypeName }}</span>
          </div>
        </div>

        <!-- Request Details Card -->
        <div class="bg-blue-800/30 border border-blue-400/30 rounded-lg p-4 space-y-3">
          <div class="flex items-center justify-between">
            <span class="text-blue-200 text-sm">Request ID:</span>
            <span class="text-white font-mono font-bold">{{ formattedRequestId }}</span>
          </div>
          <div class="flex items-center justify-between">
            <span class="text-blue-200 text-sm">Current Status:</span>
            <div class="flex items-center space-x-2">
              <div class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></div>
              <span class="text-red-400 font-semibold">{{ statusText }}</span>
            </div>
          </div>
        </div>

        <!-- Description -->
        <div class="text-center">
          <p class="text-blue-100 leading-relaxed">
            This will allow you to
            <span class="font-semibold text-white">modify the request details</span> and
            <span class="font-semibold text-white">resubmit it for approval</span>.
          </p>
        </div>

        <!-- Features List -->
        <div class="bg-green-900/20 border border-green-400/30 rounded-lg p-4">
          <h4 class="text-green-300 font-semibold text-sm mb-3 flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            What you can do:
          </h4>
          <ul class="space-y-2 text-sm text-green-100">
            <li class="flex items-start">
              <i class="fas fa-arrow-right text-green-400 mt-0.5 mr-2 text-xs"></i>
              Update request information
            </li>
            <li class="flex items-start">
              <i class="fas fa-arrow-right text-green-400 mt-0.5 mr-2 text-xs"></i>
              Modify service selections
            </li>
            <li class="flex items-start">
              <i class="fas fa-arrow-right text-green-400 mt-0.5 mr-2 text-xs"></i>
              Upload new digital signature
            </li>
            <li class="flex items-start">
              <i class="fas fa-arrow-right text-green-400 mt-0.5 mr-2 text-xs"></i>
              Address rejection feedback
            </li>
          </ul>
        </div>
      </div>

      <!-- Footer Actions -->
      <div class="p-6 border-t border-blue-400/20 flex space-x-3">
        <button
          @click="closeModal"
          class="flex-1 px-4 py-3 bg-gray-600/50 hover:bg-gray-600/70 text-gray-200 font-medium rounded-lg transition-all duration-200 border border-gray-500/30"
        >
          <i class="fas fa-times mr-2"></i>
          Cancel
        </button>
        <button
          @click="confirmEdit"
          class="flex-1 px-4 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-medium rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105"
        >
          <i class="fas fa-edit mr-2"></i>
          Edit Request
        </button>
      </div>
    </div>
  </div>
</template>

<script>
  import { ref, computed } from 'vue'

  export default {
    name: 'EditRequestModal',
    emits: ['confirm', 'cancel', 'close'],
    props: {
      isVisible: {
        type: Boolean,
        default: false
      },
      requestId: {
        type: String,
        required: true
      },
      requestType: {
        type: String,
        required: true
      },
      requestStatus: {
        type: String,
        required: true
      }
    },
    setup(props, { emit }) {
      const formattedRequestId = computed(() => {
        // Format the request ID (handle both #REQ-000004 and REQ-000004)
        const id = props.requestId
        if (id.startsWith('#')) {
          return id
        } else if (id.startsWith('REQ-')) {
          return `#${id}`
        } else {
          return `#REQ-${String(id).padStart(6, '0')}`
        }
      })

      const requestTypeName = computed(() => {
        const typeMap = {
          combined_access: 'Combined Access',
          booking_service: 'Booking Service',
          jeeva_access: 'Jeeva Access',
          wellsoft: 'Wellsoft Access',
          internet_access_request: 'Internet Access'
        }
        return typeMap[props.requestType] || 'Access Request'
      })

      const statusText = computed(() => {
        const statusMap = {
          hod_rejected: 'HOD Rejected',
          divisional_rejected: 'Divisional Rejected',
          rejected: 'Rejected',
          pending: 'Pending',
          approved: 'Approved'
        }
        return statusMap[props.requestStatus] || props.requestStatus
      })

      const confirmEdit = () => {
        emit('confirm')
        closeModal()
      }

      const closeModal = () => {
        emit('close')
      }

      return {
        formattedRequestId,
        requestTypeName,
        statusText,
        confirmEdit,
        closeModal
      }
    }
  }
</script>

<style scoped>
  /* Custom animations */
  @keyframes modalEnter {
    from {
      opacity: 0;
      transform: scale(0.95);
    }
    to {
      opacity: 1;
      transform: scale(1);
    }
  }

  .modal-enter {
    animation: modalEnter 0.3s ease-out;
  }

  /* Pulse animation for status indicator */
  @keyframes pulse {
    0%,
    100% {
      opacity: 1;
    }
    50% {
      opacity: 0.5;
    }
  }
</style>
