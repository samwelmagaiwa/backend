<template>
  <div class="min-h-screen bg-gray-100">
    <OnboardingFlow
      v-if="currentUser"
      :user-name="currentUser.name"
      :user-id="currentUser.id"
      @onboarding-complete="handleOnboardingComplete"
      @return-to-login="handleReturnToLogin"
    />
  </div>
</template>

<script>
import { useAuth } from '@/composables/useAuth'
import { useRouter } from 'vue-router'
import OnboardingFlow from './onboarding/OnboardingFlow.vue'

export default {
  name: 'OnboardingPage',
  components: {
    OnboardingFlow
  },
  setup() {
    const { user: currentUser, logout } = useAuth()
    const router = useRouter()

    const handleOnboardingComplete = async() => {
      try {
        console.log('🎉 Onboarding completed successfully')
        console.log('👤 Current user:', currentUser.value)
        console.log('🔑 User role:', currentUser.value?.role)

        // Wait a moment for state to update
        await new Promise((resolve) => setTimeout(resolve, 100))

        // Redirect to appropriate dashboard based on user role
        let dashboard = '/user-dashboard' // default

        if (currentUser.value?.role) {
          console.log('🔄 Determining dashboard for role:', currentUser.value.role)
          switch (currentUser.value.role) {
            case 'admin':
              dashboard = '/admin-dashboard'
              break
            case 'ict_officer':
              dashboard = '/ict-dashboard'
              break
            case 'head_of_department':
              dashboard = '/hod-dashboard'
              break
            case 'divisional_director':
              dashboard = '/divisional-dashboard'
              break
            case 'ict_director':
              dashboard = '/dict-dashboard'
              break
            case 'staff':
            default:
              dashboard = '/user-dashboard'
              break
          }
        } else {
          console.warn('⚠️ No user role found, using default dashboard')
        }

        console.log('🚀 Redirecting to dashboard:', dashboard)

        // Add a small delay to ensure the console logs are visible
        await new Promise((resolve) => setTimeout(resolve, 500))

        const navigationResult = await router.push(dashboard)
        console.log('✅ Navigation result:', navigationResult)

      } catch (error) {
        console.error('❌ Error completing onboarding:', error)
        console.error('📍 Error details:', {
          message: error.message,
          stack: error.stack,
          currentUser: currentUser.value
        })

        // Fallback redirect with more logging
        try {
          console.log('🔄 Attempting fallback redirect to /user-dashboard')
          await router.push('/user-dashboard')
          console.log('✅ Fallback redirect successful')
        } catch (fallbackError) {
          console.error('❌ Fallback redirect also failed:', fallbackError)
          // Force page reload as last resort
          console.log('🔄 Forcing page reload to /user-dashboard')
          window.location.href = '/user-dashboard'
        }
      }
    }

    const handleReturnToLogin = async() => {
      // Log out the user and navigate back to login page
      console.log('OnboardingPage: Logging out and navigating to login page')
      try {
        await logout()
        router.push('/')
      } catch (error) {
        console.error('Logout error:', error)
        // Even if logout fails, still redirect to login
        router.push('/')
      }
    }

    return {
      currentUser,
      handleOnboardingComplete,
      handleReturnToLogin
    }
  }
}
</script>
"
