<template>
  <div
    v-if="show"
    class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50"
  >
    <div class="forgot-password-modal bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 relative">
      <!-- Close button -->
      <button
        type="button"
        class="absolute top-3 right-3 text-gray-400 hover:text-gray-600"
        @click="handleClose"
      >
        <i class="fas fa-times"></i>
      </button>

      <h2 class="text-xl font-semibold text-gray-800 mb-2 text-center">Reset Password</h2>
      <p class="text-xs text-gray-500 mb-4 text-center">
        Follow the steps below to securely reset your password using your registered phone number.
      </p>

      <!-- Error message -->
      <div
        v-if="errorMessage"
        class="mb-3 p-2 bg-red-50 border border-red-200 text-xs text-red-700 rounded flex items-center"
      >
        <i class="fas fa-exclamation-circle mr-2"></i>
        <span>{{ errorMessage }}</span>
      </div>

      <!-- Step indicators -->
      <div class="flex items-center justify-center mb-4 text-xs">
        <div class="flex items-center space-x-2">
          <div
            :class="[
              'w-6 h-6 rounded-full flex items-center justify-center text-white',
              step >= 1 ? 'bg-blue-600' : 'bg-gray-300'
            ]"
          >
            1
          </div>
          <span class="text-gray-600">Phone</span>
          <div class="w-6 h-px bg-gray-300 mx-1"></div>
          <div
            :class="[
              'w-6 h-6 rounded-full flex items-center justify-center text-white',
              step >= 2 ? 'bg-blue-600' : 'bg-gray-300'
            ]"
          >
            2
          </div>
          <span class="text-gray-600">OTP</span>
          <div class="w-6 h-px bg-gray-300 mx-1"></div>
          <div
            :class="[
              'w-6 h-6 rounded-full flex items-center justify-center text-white',
              step >= 3 ? 'bg-blue-600' : 'bg-gray-300'
            ]"
          >
            3
          </div>
          <span class="text-gray-600">New Password</span>
        </div>
      </div>

      <!-- Step 1: Phone number input -->
      <div v-if="step === 1">
        <label class="block text-xs font-semibold text-gray-700 mb-1"
          >Registered Phone Number</label
        >
        <input
          v-model="phoneNumber"
          type="tel"
          class="w-full p-2 border rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent"
          placeholder="Enter your registered phone number"
          :disabled="loading"
        />
        <p class="mt-1 text-[11px] text-gray-500">
          Enter the phone number that is registered in the system. A 6-digit OTP code will be sent
          to this number.
        </p>

        <button
          type="button"
          class="mt-4 w-full bg-blue-600 text-white py-2 rounded text-sm hover:bg-blue-700 disabled:opacity-50"
          :disabled="loading || !phoneNumber"
          @click="handleSendOtp"
        >
          <span v-if="loading">Sending OTP...</span>
          <span v-else>Send OTP</span>
        </button>
      </div>

      <!-- Step 2: OTP input -->
      <div v-else-if="step === 2">
        <p class="text-xs text-gray-600 mb-1">
          Enter the 6-digit OTP sent to your phone number ending with
          <span class="font-semibold">{{ phoneSuffix }}</span
          >.
        </p>
        <p v-if="otpCountdownLabel" class="text-[11px] text-blue-600">
          Code expires in {{ otpCountdownLabel }}
        </p>
        <p class="text-[11px] text-gray-500 mb-2">
          You have up to <span class="font-semibold">5 attempts</span> before this code locks and
          you must request a new one.
        </p>
        <label class="block text-xs font-semibold text-gray-700 mb-1">OTP Code</label>
        <input
          v-model="otp"
          type="text"
          maxlength="6"
          class="w-full p-2 border rounded text-center tracking-widest text-lg font-mono focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent"
          placeholder="••••••"
          :disabled="loading"
        />

        <button
          type="button"
          class="mt-4 w-full bg-blue-600 text-white py-2 rounded text-sm hover:bg-blue-700 disabled:opacity-50"
          :disabled="loading || otp.length !== 6"
          @click="handleVerifyOtp"
        >
          <span v-if="loading">Verifying OTP...</span>
          <span v-else>Proceed</span>
        </button>

        <button
          v-if="otpCountdown === 0"
          type="button"
          class="mt-2 w-full text-[11px] text-blue-600 hover:text-blue-800"
          :disabled="loading"
          @click="handleResendOtp"
        >
          Resend code
        </button>

        <button
          type="button"
          class="mt-2 w-full text-[11px] text-gray-500 hover:text-blue-600"
          :disabled="loading"
          @click="goBackToPhone"
        >
          ← Back to phone number
        </button>
      </div>

      <!-- Step 3: New password -->
      <div v-else>
        <label class="block text-xs font-semibold text-gray-700 mb-1">New Password</label>
        <input
          v-model="newPassword"
          type="password"
          class="w-full p-2 border rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent"
          :class="{ 'border-red-500 focus:ring-red-500 focus:border-red-500': newPasswordError }"
          placeholder="Create a new password"
          :disabled="loading"
        />
        <p v-if="newPasswordError" class="mt-1 text-xs text-red-600">
          {{ newPasswordError }}
        </p>

        <label class="block text-xs font-semibold text-gray-700 mb-1 mt-3"
          >Confirm New Password</label
        >
        <input
          v-model="confirmPassword"
          type="password"
          class="w-full p-2 border rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent"
          :class="{
            'border-red-500 focus:ring-red-500 focus:border-red-500': confirmPasswordError
          }"
          placeholder="Re-enter new password"
          :disabled="loading"
        />
        <p v-if="confirmPasswordError" class="mt-1 text-xs text-red-600">
          {{ confirmPasswordError }}
        </p>

        <p class="mt-1 text-[11px] text-gray-500">
          After confirming, your password will be updated and you will be logged in automatically.
        </p>

        <button
          type="button"
          class="mt-4 w-full bg-blue-600 text-white py-2 rounded text-sm hover:bg-blue-700 disabled:opacity-50"
          :disabled="loading || !newPassword || !confirmPassword"
          @click="handleResetPassword"
        >
          <span v-if="loading">Updating password...</span>
          <span v-else>OK</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script>
  import { ref, computed, watch, onBeforeUnmount } from 'vue'
  import { useAuth } from '@/composables/useAuth'
  import { authService } from '@/services/authService'

  export default {
    name: 'ForgotPasswordModal',
    props: {
      show: {
        type: Boolean,
        default: false
      }
    },
    emits: ['close'],
    setup(props, { emit }) {
      const auth = useAuth()

      const step = ref(1)
      const phoneNumber = ref('')
      const otp = ref('')
      const newPassword = ref('')
      const confirmPassword = ref('')
      const loading = ref(false)
      const errorMessage = ref('')

      const MIN_PASSWORD_LENGTH = 8

      const newPasswordError = computed(() => {
        if (!newPassword.value) return ''
        if (newPassword.value.length < MIN_PASSWORD_LENGTH) {
          return `Password must be at least ${MIN_PASSWORD_LENGTH} characters long.`
        }
        return ''
      })

      const confirmPasswordError = computed(() => {
        if (!confirmPassword.value) return ''
        if (newPassword.value && confirmPassword.value !== newPassword.value) {
          return 'Passwords do not match.'
        }
        return ''
      })

      const otpCountdown = ref(0)
      let otpCountdownTimer = null

      const phoneSuffix = computed(() => {
        if (!phoneNumber.value) return ''
        return phoneNumber.value.slice(-2)
      })

      const otpCountdownLabel = computed(() => {
        if (!otpCountdown.value) return ''
        const minutes = Math.floor(otpCountdown.value / 60)
        const seconds = otpCountdown.value % 60
        const mm = String(minutes).padStart(2, '0')
        const ss = String(seconds).padStart(2, '0')
        return `${mm}:${ss}`
      })

      const clearOtpTimer = () => {
        if (otpCountdownTimer) {
          clearInterval(otpCountdownTimer)
          otpCountdownTimer = null
        }
      }

      const resetState = () => {
        step.value = 1
        phoneNumber.value = ''
        otp.value = ''
        newPassword.value = ''
        confirmPassword.value = ''
        loading.value = false
        errorMessage.value = ''
        otpCountdown.value = 0
        clearOtpTimer()
      }

      const handleClose = () => {
        if (loading.value) return
        resetState()
        emit('close')
      }

      const handleSendOtp = async () => {
        if (!phoneNumber.value) {
          errorMessage.value = 'Please enter your registered phone number.'
          return
        }

        loading.value = true
        errorMessage.value = ''

        try {
          const response = await authService.requestPasswordResetByPhone(phoneNumber.value)

          if (!response.success) {
            const errors = response.errors || {}
            if (errors.phone && Array.isArray(errors.phone) && errors.phone.length) {
              errorMessage.value = errors.phone[0]
            } else if (
              response.message &&
              response.message.toLowerCase().includes('not') &&
              response.message.toLowerCase().includes('found')
            ) {
              errorMessage.value =
                'Phone number not found. Please enter the phone number registered in the system.'
            } else {
              errorMessage.value =
                response.message ||
                'Failed to send OTP. Please make sure the phone number is correct.'
            }
            return
          }

          // Move to OTP step and start countdown (e.g., 3 minutes)
          step.value = 2
          otpCountdown.value = 180
          otpCountdownTimer = setInterval(() => {
            if (otpCountdown.value > 0) {
              otpCountdown.value -= 1
            } else {
              clearOtpTimer()
            }
          }, 1000)
        } catch (error) {
          console.error('Error sending OTP:', error)
          errorMessage.value = 'Failed to send OTP. Please try again.'
        } finally {
          loading.value = false
        }
      }

      const handleVerifyOtp = async () => {
        if (otp.value.length !== 6) {
          errorMessage.value = 'Please enter the 6-digit OTP code sent to your phone.'
          return
        }

        loading.value = true
        errorMessage.value = ''

        try {
          const response = await authService.verifyPasswordResetOtp({
            phone: phoneNumber.value,
            otp: otp.value
          })

          if (!response.success) {
            const errors = response.errors || {}
            if (errors.otp && Array.isArray(errors.otp) && errors.otp.length) {
              errorMessage.value = errors.otp[0]
            } else if (response.message && response.message.toLowerCase().includes('expired')) {
              errorMessage.value = 'Your OTP has expired. Please request a new code and try again.'
            } else {
              errorMessage.value =
                response.message || 'The OTP you entered is incorrect. Please try again.'
            }
            return
          }

          step.value = 3
        } catch (error) {
          console.error('Error verifying OTP:', error)
          errorMessage.value = 'Failed to verify OTP. Please try again.'
        } finally {
          loading.value = false
        }
      }

      const handleResetPassword = async () => {
        if (!newPassword.value || !confirmPassword.value) {
          errorMessage.value = 'Please enter and confirm your new password.'
          return
        }
        if (newPasswordError.value || confirmPasswordError.value) {
          errorMessage.value = 'Please fix the highlighted password issues before continuing.'
          return
        }

        loading.value = true
        errorMessage.value = ''

        try {
          const response = await authService.resetPasswordWithOtp({
            phone: phoneNumber.value,
            otp: otp.value,
            password: newPassword.value,
            password_confirmation: confirmPassword.value
          })

          if (!response.success || !response.data) {
            const errors = response.errors || {}
            if (errors.password && Array.isArray(errors.password) && errors.password.length) {
              errorMessage.value = errors.password[0]
            } else if (errors.otp && Array.isArray(errors.otp) && errors.otp.length) {
              errorMessage.value = errors.otp[0]
            } else {
              errorMessage.value =
                response.message || 'Failed to update password. Please try again.'
            }
            return
          }

          const { user, token } = response.data

          // Set auth store directly and reuse normal redirect logic
          auth.updateUser(user)
          // Persist token in auth store
          const authStoreModule = await import('@/stores/auth')
          const authStore = authStoreModule.useAuthStore()
          authStore.setAuthData({ user, token })

          resetState()
          emit('close')
        } catch (error) {
          console.error('Error resetting password:', error)
          errorMessage.value = 'Failed to update password. Please try again.'
        } finally {
          loading.value = false
        }
      }

      const handleResendOtp = async () => {
        if (loading.value || !phoneNumber.value) return
        // clear old OTP and errors, then reuse send logic
        otp.value = ''
        errorMessage.value = ''
        await handleSendOtp()
      }

      const goBackToPhone = () => {
        if (loading.value) return
        step.value = 1
        otp.value = ''
        errorMessage.value = ''
        otpCountdown.value = 0
        clearOtpTimer()
      }

      // When modal is reopened, reset state
      watch(
        () => props.show,
        (value) => {
          if (value) {
            resetState()
          } else {
            clearOtpTimer()
          }
        }
      )

      onBeforeUnmount(() => {
        clearOtpTimer()
      })

      return {
        step,
        phoneNumber,
        otp,
        newPassword,
        confirmPassword,
        newPasswordError,
        confirmPasswordError,
        loading,
        errorMessage,
        phoneSuffix,
        otpCountdown,
        otpCountdownLabel,
        handleClose,
        handleSendOtp,
        handleVerifyOtp,
        handleResetPassword,
        handleResendOtp,
        goBackToPhone
      }
    }
  }
</script>

<style scoped>
  .forgot-password-modal {
    font-size: 0.95rem;
    color: #111827; /* near-black for better readability */
  }

  .forgot-password-modal p,
  .forgot-password-modal label,
  .forgot-password-modal button,
  .forgot-password-modal span,
  .forgot-password-modal input {
    font-size: 0.95rem;
    color: #111827;
  }

  .forgot-password-modal h2 {
    font-size: 1.5rem;
    color: #111827;
  }
</style>
