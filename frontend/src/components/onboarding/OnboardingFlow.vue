<template>
  <div>
    <!-- Loading State -->
    <div
      v-if="loading"
      class="flex items-center justify-center min-h-screen bg-gradient-to-br from-blue-900 via-blue-800 to-teal-900"
    >
      <div class="text-center">
        <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-white mx-auto mb-4"></div>
        <p class="text-white text-lg">Loading onboarding status...</p>
      </div>
    </div>

    <!-- Onboarding Flow (only show when not loading) -->
    <div v-else>
      <!-- Terms of Service Popup -->
      <OnboardingPopup
        v-if="currentStep === 'terms-popup'"
        title="Welcome to Muhimbili National Hospital"
        message="Click to read the Organization Terms of Service"
        button-text="Read Terms of Service"
        icon="fas fa-file-pen"
        :show-return-to-login="true"
        @click="showTermsOfService"
        @return-to-login="returnToLogin"
      />

      <!-- Terms of Service Page -->
      <TermsOfService
        v-if="currentStep === 'terms-page'"
        @terms-accepted="onTermsAccepted"
        @go-back="goToPreviousStep"
      />

      <!-- ICT Policy Popup -->
      <OnboardingPopup
        v-if="currentStep === 'policy-popup'"
        title="ICT Policy Review"
        message="Press to read Organization ICT Policy"
        button-text="Read ICT Policy"
        icon="fas fa-shield-alt"
        :show-return-to-login="true"
        @click="showIctPolicy"
        @return-to-login="returnToLogin"
      />

      <!-- ICT Policy Page -->
      <IctPolicy
        v-if="currentStep === 'policy-page'"
        @policy-accepted="onPolicyAccepted"
        @go-back="goToPreviousStep"
      />

      <!-- Declaration Form -->
      <DeclarationForm
        v-if="currentStep === 'declaration'"
        @form-submitted="onDeclarationSubmitted"
        @go-back="goToPreviousStep"
      />

      <!-- Success Popup -->
      <SuccessPopup
        v-if="currentStep === 'success'"
        :user-name="userName"
        message="You have successfully completed the onboarding process. Now you can proceed with your requests."
        @continue="completeOnboarding"
      />
    </div>
  </div>
</template>

<script>
  import OnboardingPopup from './OnboardingPopup.vue'
  import TermsOfService from './TermsOfService.vue'
  import IctPolicy from './IctPolicy.vue'
  import DeclarationForm from '../views/forms/declarationForm.vue'
  import SuccessPopup from './SuccessPopup.vue'

  export default {
    name: 'OnboardingFlow',
    components: {
      OnboardingPopup,
      TermsOfService,
      IctPolicy,
      DeclarationForm,
      SuccessPopup
    },
    props: {
      userName: {
        type: String,
        required: true
      },
      userId: {
        type: [String, Number],
        required: true
      }
    },
    emits: ['onboarding-complete', 'return-to-login'],
    data() {
      return {
        currentStep: 'terms-popup', // Always start from beginning
        loading: true,
        backendStatus: null
      }
    },

    async mounted() {
      await this.initializeOnboardingState()
    },

    watch: {
      currentStep(_newStep) {
        // Save the step whenever it changes
        this.saveCurrentStep()
      }
    },
    methods: {
      async initializeOnboardingState() {
        try {
          console.log('🔄 Initializing onboarding state for user:', this.userId)

          // Get onboarding status from backend
          const { authAPI } = await import('../../utils/apiClient')
          const result = await authAPI.getOnboardingStatus()

          if (result.success) {
            console.log('✅ Backend onboarding status:', result.data)
            this.backendStatus = result.data

            // If user has completed onboarding, show success
            if (result.data.progress && result.data.progress.completed) {
              console.log('✅ Onboarding already completed, showing success')
              this.currentStep = 'success'
            } else {
              // Determine current step based on backend progress
              this.currentStep = this.determineCurrentStep(result.data.progress || {})
              console.log('🔄 Determined current step:', this.currentStep)
            }
          } else {
            console.warn('⚠️ Failed to get onboarding status:', result.error)
            // Fallback to localStorage method
            this.currentStep = this.getInitialStepFromLocalStorage()
          }
        } catch (error) {
          console.warn('⚠️ Error initializing onboarding state:', error)
          // Fallback to localStorage method
          this.currentStep = this.getInitialStepFromLocalStorage()
        } finally {
          this.loading = false
          console.log('✅ Onboarding initialization complete, current step:', this.currentStep)
        }
      },

      determineCurrentStep(progress) {
        // Determine the current step based on backend progress
        if (!progress.terms_accepted) {
          return 'terms-popup'
        } else if (!progress.ict_policy_accepted) {
          return 'policy-popup'
        } else if (!progress.declaration_submitted) {
          return 'declaration'
        } else if (progress.completed) {
          return 'success'
        } else {
          // Default to terms if something is wrong
          return 'terms-popup'
        }
      },

      getInitialStepFromLocalStorage() {
        // Fallback method using localStorage (for backward compatibility)
        const savedStep = localStorage.getItem(`onboarding_step_${this.userId}`)

        // Check if user has already completed onboarding in localStorage
        const completedUsers = JSON.parse(localStorage.getItem('onboarding_completed') || '[]')
        if (completedUsers.includes(this.userId)) {
          return 'success'
        }

        // Return saved step or default to first step
        return savedStep || 'terms-popup'
      },

      saveCurrentStep() {
        // Save current step to localStorage for this user
        localStorage.setItem(`onboarding_step_${this.userId}`, this.currentStep)
      },

      showTermsOfService() {
        this.currentStep = 'terms-page'
        this.saveCurrentStep()
      },

      async onTermsAccepted() {
        try {
          // Call backend API to accept terms
          const { authAPI } = await import('../../utils/apiClient')
          const result = await authAPI.acceptTerms()

          if (result.success) {
            this.currentStep = 'policy-popup'
            this.saveCurrentStep()
          } else {
            console.error('Failed to accept terms:', result.error)
          }
        } catch (error) {
          console.error('Error accepting terms:', error)
          // Continue with local flow for now
          this.currentStep = 'policy-popup'
          this.saveCurrentStep()
        }
      },

      showIctPolicy() {
        this.currentStep = 'policy-page'
        this.saveCurrentStep()
      },

      async onPolicyAccepted() {
        try {
          // Call backend API to accept ICT policy
          const { authAPI } = await import('../../utils/apiClient')
          const result = await authAPI.acceptIctPolicy()

          if (result.success) {
            this.currentStep = 'declaration'
            this.saveCurrentStep()
          } else {
            console.error('Failed to accept ICT policy:', result.error)
          }
        } catch (error) {
          console.error('Error accepting ICT policy:', error)
          // Continue with local flow for now
          this.currentStep = 'declaration'
          this.saveCurrentStep()
        }
      },

      async onDeclarationSubmitted(declarationData) {
        try {
          // Call backend API to submit declaration
          const { authAPI } = await import('../../utils/apiClient')
          const result = await authAPI.submitDeclaration(declarationData)

          if (result.success) {
            this.currentStep = 'success'
            this.saveCurrentStep()
          } else {
            console.error('Failed to submit declaration:', result.error)
            // You might want to show an error message to the user
          }
        } catch (error) {
          console.error('Error submitting declaration:', error)
          // Continue with local flow for now
          this.currentStep = 'success'
          this.saveCurrentStep()
        }
      },

      async completeOnboarding() {
        console.log('🔄 OnboardingFlow: completeOnboarding() called')

        // Mark user as having completed onboarding
        const success = await this.markOnboardingComplete()
        console.log('📊 markOnboardingComplete result:', success)

        if (success) {
          console.log('✅ Onboarding marked as complete, cleaning up and emitting event')
          // Clean up the step tracking since onboarding is complete
          this.clearOnboardingStep()
          console.log('🚀 Emitting onboarding-complete event')
          this.$emit('onboarding-complete')
        } else {
          // Handle error - maybe show a notification
          console.error('❌ Failed to complete onboarding process')
        }
      },

      returnToLogin() {
        // Emit event to parent component to handle navigation
        console.log('OnboardingFlow: Return to login event received')
        this.$emit('return-to-login')
      },

      async markOnboardingComplete() {
        try {
          // Call backend API to complete onboarding
          const { authAPI } = await import('../../utils/apiClient')
          const result = await authAPI.completeOnboarding()

          if (result.success) {
            // Also store in localStorage for compatibility
            const completedUsers = JSON.parse(localStorage.getItem('onboarding_completed') || '[]')
            if (!completedUsers.includes(this.userId)) {
              completedUsers.push(this.userId)
              localStorage.setItem('onboarding_completed', JSON.stringify(completedUsers))
            }
            return true
          } else {
            console.error('Failed to complete onboarding:', result.error)
            return false
          }
        } catch (error) {
          console.error('Error completing onboarding:', error)
          return false
        }
      },

      clearOnboardingStep() {
        // Remove the step tracking for this user since onboarding is complete
        localStorage.removeItem(`onboarding_step_${this.userId}`)
      },

      resetOnboardingState() {
        // Method to reset onboarding state if needed (for debugging or user request)
        localStorage.removeItem(`onboarding_step_${this.userId}`)
        const completedUsers = JSON.parse(localStorage.getItem('onboarding_completed') || '[]')
        const updatedUsers = completedUsers.filter((id) => id !== this.userId)
        localStorage.setItem('onboarding_completed', JSON.stringify(updatedUsers))
        this.currentStep = 'terms-popup'
        this.saveCurrentStep()
      },

      goToPreviousStep() {
        // Define the step sequence
        const stepSequence = [
          'terms-popup',
          'terms-page',
          'policy-popup',
          'policy-page',
          'declaration',
          'success'
        ]

        const currentIndex = stepSequence.indexOf(this.currentStep)

        if (currentIndex > 0) {
          this.currentStep = stepSequence[currentIndex - 1]
          this.saveCurrentStep()
        } else {
          // If at the first step, emit return to login
          this.returnToLogin()
        }
      }
    }
  }
</script>
"
