<template>
  <div class="min-h-screen bg-blue-900 flex items-center justify-center p-4">
    <div class="w-full max-w-2xl">
      <div class="bg-blue-800/40 border border-blue-400/40 rounded-2xl p-8 text-center shadow-2xl">
        <div class="mb-4">
          <i class="fas fa-exclamation-triangle text-5xl text-yellow-400"></i>
        </div>
        <h1 class="text-3xl md:text-4xl font-extrabold text-white mb-2">Unable to Load Page</h1>
        <p class="text-blue-200 text-base md:text-lg mb-6">
          The requested page could not be found. It may have been moved or the URL is incorrect.
        </p>
        <div class="flex flex-wrap gap-3 justify-center">
          <button
            @click="goBack"
            class="px-5 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold"
          >
            <i class="fas fa-undo mr-2"></i>
            Retry/Back
          </button>
          <button
            @click="goHome"
            class="px-5 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-semibold"
          >
            <i class="fas fa-home mr-2"></i>
            Back to Dashboard
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import { useRouter } from 'vue-router'
  import { useAuth } from '@/composables/useAuth'
  import { getDefaultDashboard } from '@/utils/permissions'

  export default {
    name: 'NotFound',
    setup() {
      const router = useRouter()
      const { userRole } = useAuth()

      const goBack = () => {
        if (window.history.length > 1) {
          router.go(-1)
        } else {
          goHome()
        }
      }

      const goHome = () => {
        const defaultDashboard = getDefaultDashboard(userRole.value)
        if (defaultDashboard) {
          router.push(defaultDashboard)
        } else {
          router.push('/')
        }
      }

      return {
        goBack,
        goHome
      }
    }
  }
</script>
