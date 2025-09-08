<template>
  <div
    class="min-h-screen bg-gradient-to-br from-blue-900 via-blue-800 to-teal-900 flex items-center justify-center"
  >
    <div class="text-center">
      <div class="mb-8">
        <i class="fas fa-exclamation-triangle text-6xl text-yellow-400 mb-4"></i>
        <h1 class="text-4xl font-bold text-white mb-2">404 - Page Not Found</h1>
        <p class="text-xl text-blue-200">The page you're looking for doesn't exist.</p>
      </div>

      <div class="space-y-4">
        <button
          @click="goBack"
          class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-300 font-semibold mr-4"
        >
          <i class="fas fa-arrow-left mr-2"></i>
          Go Back
        </button>

        <button
          @click="goHome"
          class="px-6 py-3 bg-gradient-to-r from-teal-600 to-teal-700 text-white rounded-lg hover:from-teal-700 hover:to-teal-800 transition-all duration-300 font-semibold"
        >
          <i class="fas fa-home mr-2"></i>
          Go Home
        </button>
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
