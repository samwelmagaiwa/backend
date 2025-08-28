<template>
  <div class="min-h-screen bg-gray-100 flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full">
      <h1 class="text-2xl font-bold text-green-600 mb-4">✅ App is Working!</h1>
      <p class="text-gray-600 mb-4">
        If you can see this page, the Vue app is loading correctly.
      </p>

      <div class="space-y-2 text-sm">
        <div><strong>Current Route:</strong> {{ $route.path }}</div>
        <div><strong>Router Ready:</strong> {{ $router ? 'Yes' : 'No' }}</div>
        <div><strong>Timestamp:</strong> {{ new Date().toLocaleString() }}</div>
      </div>

      <div class="mt-6">
        <button
          @click="goToLogin"
          class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 mr-2"
        >
          Go to Login
        </button>
        <button
          @click="checkAuth"
          class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600"
        >
          Check Auth
        </button>
      </div>

      <div v-if="authInfo" class="mt-4 p-4 bg-gray-50 rounded">
        <h3 class="font-bold mb-2">Auth Info:</h3>
        <pre class="text-xs">{{ authInfo }}</pre>
      </div>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue'
import { useRouter } from 'vue-router'

export default {
  name: 'TestComponent',
  setup() {
    const router = useRouter()
    const authInfo = ref(null)

    const goToLogin = () => {
      router.push('/login')
    }

    const checkAuth = async() => {
      try {
        const authModule = await import('@/utils/auth')
        const auth = authModule.default

        authInfo.value = {
          isAuthenticated: auth.isAuthenticated,
          userRole: auth.userRole,
          currentUser: auth.currentUser,
          token: localStorage.getItem('auth_token') ? 'Present' : 'Missing'
        }
      } catch (error) {
        authInfo.value = { error: error.message }
      }
    }

    return {
      goToLogin,
      checkAuth,
      authInfo
    }
  }
}
</script>
