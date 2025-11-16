<template>
  <div
    class="min-h-screen bg-gradient-to-br from-blue-900 via-blue-800 to-teal-900 flex items-center justify-center p-4"
  >
    <div
      class="w-full max-w-xl bg-white/10 border border-blue-300/40 rounded-2xl shadow-2xl backdrop-blur-xl p-8"
    >
      <h1 class="text-2xl font-bold text-white mb-2 flex items-center">
        <i class="fas fa-key mr-2 text-blue-300"></i>
        Change Your Password
      </h1>
      <p class="text-blue-100 mb-6 text-sm">
        For security reasons, you must change your password before accessing the system.
      </p>

      <div
        v-if="successMessage"
        class="mb-4 p-3 rounded-lg bg-green-500/15 border border-green-400/60 text-green-100 text-sm flex items-center"
      >
        <i class="fas fa-check-circle mr-2"></i>
        {{ successMessage }}
      </div>

      <div
        v-if="errorMessage"
        class="mb-4 p-3 rounded-lg bg-red-500/15 border border-red-400/60 text-red-100 text-sm flex items-center"
      >
        <i class="fas fa-exclamation-circle mr-2"></i>
        {{ errorMessage }}
      </div>

      <form @submit.prevent="handleSubmit" class="space-y-4">
        <div>
          <label class="block text-sm font-semibold text-blue-100 mb-1">Current Password</label>
          <input
            v-model="currentPassword"
            type="password"
            class="w-full px-4 py-2 rounded-lg bg-blue-900/40 border border-blue-300/60 text-white text-sm focus:outline-none focus:ring-2 focus:ring-cyan-400"
            required
            autocomplete="current-password"
          />
        </div>

        <div>
          <label class="block text-sm font-semibold text-blue-100 mb-1">New Password</label>
          <input
            v-model="newPassword"
            type="password"
            class="w-full px-4 py-2 rounded-lg bg-blue-900/40 border border-blue-300/60 text-white text-sm focus:outline-none focus:ring-2 focus:ring-cyan-400"
            required
            autocomplete="new-password"
          />
          <p v-if="newPasswordError" class="mt-1 text-xs text-red-300">
            {{ newPasswordError }}
          </p>
        </div>

        <div>
          <label class="block text-sm font-semibold text-blue-100 mb-1">Confirm New Password</label>
          <input
            v-model="confirmPassword"
            type="password"
            class="w-full px-4 py-2 rounded-lg bg-blue-900/40 border border-blue-300/60 text-white text-sm focus:outline-none focus:ring-2 focus:ring-cyan-400"
            required
            autocomplete="new-password"
          />
          <p v-if="confirmPasswordError" class="mt-1 text-xs text-red-300">
            {{ confirmPasswordError }}
          </p>
        </div>

        <p class="text-xs text-blue-100/80 mt-1">Password must be at least 8 characters long.</p>

        <div class="mt-6 flex justify-between items-center">
          <button
            type="button"
            class="text-xs text-blue-200 hover:text-white underline"
            @click="logout"
          >
            <i class="fas fa-sign-out-alt mr-1"></i>
            Logout instead
          </button>

          <button
            type="submit"
            :disabled="submitting"
            class="px-5 py-2 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white text-sm font-semibold rounded-lg shadow-lg hover:from-emerald-600 hover:to-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center"
          >
            <i :class="submitting ? 'fas fa-spinner fa-spin mr-2' : 'fas fa-save mr-2'"></i>
            {{ submitting ? 'Saving...' : 'Save New Password' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
  import { ref, computed } from 'vue'
  import { useRouter } from 'vue-router'
  import { useAuthStore } from '@/stores/auth'
  import authService from '@/services/authService'
  import { getDefaultDashboard } from '@/utils/permissions'

  export default {
    name: 'ForceChangePassword',
    setup() {
      const router = useRouter()
      const authStore = useAuthStore()

      const currentPassword = ref('')
      const newPassword = ref('')
      const confirmPassword = ref('')
      const submitting = ref(false)
      const errorMessage = ref('')
      const successMessage = ref('')

      const userRole = computed(() => authStore.userRole)

      const newPasswordError = computed(() => {
        if (!newPassword.value) return ''
        if (newPassword.value.length < 8) {
          return 'Password must be at least 8 characters long.'
        }
        return ''
      })

      const confirmPasswordError = computed(() => {
        if (!confirmPassword.value) return ''
        if (confirmPassword.value !== newPassword.value) {
          return 'New password and confirmation do not match.'
        }
        return ''
      })

      const handleSubmit = async () => {
        errorMessage.value = ''
        successMessage.value = ''

        if (newPassword.value.length < 8) {
          errorMessage.value = 'Password must be at least 8 characters long.'
          return
        }

        if (newPassword.value !== confirmPassword.value) {
          errorMessage.value = 'New password and confirmation do not match.'
          return
        }

        submitting.value = true
        try {
          const result = await authService.changePassword({
            current: currentPassword.value,
            new: newPassword.value,
            confirm: confirmPassword.value
          })

          if (!result.success) {
            errorMessage.value =
              result.errors?.current_password?.[0] || result.message || 'Failed to change password.'
            return
          }

          // Clear must_change_password flag locally
          authStore.updateUser({ must_change_password: false })

          successMessage.value = 'Password changed successfully. Redirecting to your dashboard...'

          currentPassword.value = ''
          newPassword.value = ''
          confirmPassword.value = ''

          const dashboard = getDefaultDashboard(userRole.value) || '/user-dashboard'
          setTimeout(() => {
            router.push(dashboard)
          }, 1200)
        } catch (error) {
          console.error('Error changing password:', error)
          errorMessage.value = 'An unexpected error occurred. Please try again.'
        } finally {
          submitting.value = false
        }
      }

      const logout = async () => {
        try {
          const { authService: asyncAuthService } = await import('@/services/authService')
          await asyncAuthService.logout()
        } catch (e) {
          // ignore
        }
        authStore.clearAuth()
        router.push('/login')
      }

      return {
        currentPassword,
        newPassword,
        confirmPassword,
        newPasswordError,
        confirmPasswordError,
        submitting,
        errorMessage,
        successMessage,
        handleSubmit,
        logout
      }
    }
  }
</script>
