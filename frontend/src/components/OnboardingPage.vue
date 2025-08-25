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
    const { currentUser, defaultDashboard, markOnboardingComplete, logout } =
      useAuth()
    const router = useRouter()

    const handleOnboardingComplete = async() => {
      try {
        // Mark onboarding as complete
        if (currentUser.value) {
          const success = await markOnboardingComplete(currentUser.value.id)

          if (success) {
            console.log('Onboarding completed successfully')

            // Wait a moment for state to update
            await new Promise((resolve) => setTimeout(resolve, 100))

            // Redirect to dashboard
            const dashboard = defaultDashboard.value || '/user-dashboard'
            console.log('Redirecting to dashboard:', dashboard)
            await router.push(dashboard)
          } else {
            console.error('Failed to complete onboarding')
            // Still redirect but show error
            const dashboard = defaultDashboard.value || '/user-dashboard'
            await router.push(dashboard)
          }
        }
      } catch (error) {
        console.error('Error completing onboarding:', error)
        // Fallback redirect
        const dashboard = defaultDashboard.value || '/user-dashboard'
        await router.push(dashboard)
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
