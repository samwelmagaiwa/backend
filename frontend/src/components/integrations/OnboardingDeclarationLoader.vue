<template>
  <div class="onboarding-loader-wrapper">
    <!-- Simple Loading Banner for onboarding data fetching -->
    <SimpleLoadingBanner
      v-if="isLoadingOnboarding"
      :show="isLoadingOnboarding"
      :auto-start="false"
      @loading-complete="onLoadingComplete"
      ref="loadingBanner"
    />

    <!-- Main onboarding content after loading -->
    <slot v-else :onboarding-data="onboardingData" :user-data="userData">
      <!-- Default content if no slot provided -->
      <div class="onboarding-complete">
        <h2>Loading Complete!</h2>
        <p>Onboarding data has been successfully loaded.</p>
        <pre>{{ JSON.stringify({ userData, onboardingData }, null, 2) }}</pre>
      </div>
    </slot>
  </div>
</template>

<script>
  import SimpleLoadingBanner from '@/components/common/SimpleLoadingBanner.vue'
  import userProfileService from '@/services/userProfileService'

  export default {
    name: 'OnboardingDeclarationLoader',

    components: {
      SimpleLoadingBanner
    },

    props: {
      // Auto-start loading on mount
      autoStart: {
        type: Boolean,
        default: true
      },

      // Skip certain loading phases for testing
      skipPhases: {
        type: Array,
        default: () => []
      }
    },

    emits: [
      'loading-started',
      'loading-progress',
      'loading-complete',
      'loading-error',
      'user-data-loaded',
      'declaration-status-loaded',
      'phone-verification-complete'
    ],

    data() {
      return {
        isLoadingOnboarding: true,

        // Loaded data
        userData: null,
        onboardingData: null,

        // Loading phases specifically for onboarding
        loadingPhases: [
          {
            id: 'init',
            percentage: 0,
            message: 'Initializing your session...',
            action: this.initializeSession
          },
          {
            id: 'profile',
            percentage: 25,
            message: 'Loading your profile...',
            action: this.loadUserProfile
          },
          {
            id: 'declaration',
            percentage: 50,
            message: 'Checking declaration status...',
            action: this.loadDeclarationStatus
          },
          {
            id: 'phone',
            percentage: 75,
            message: 'Verifying phone number...',
            action: this.verifyPhoneStatus
          },
          {
            id: 'finalize',
            percentage: 90,
            message: 'Finalizing setup...',
            action: this.finalizeOnboarding
          },
          {
            id: 'complete',
            percentage: 100,
            message: 'Welcome to your dashboard!',
            action: this.completeOnboarding
          }
        ],

        // Error handling
        loadingErrors: [],
        retryCount: 0,
        maxRetries: 3
      }
    },

    async mounted() {
      console.log('🚀 OnboardingDeclarationLoader mounted')

      if (this.autoStart) {
        await this.startOnboardingLoad()
      }
    },

    methods: {
      /**
       * Start the complete onboarding loading process
       */
      async startOnboardingLoad() {
        console.log('📱 Starting onboarding data load process...')

        this.isLoadingOnboarding = true
        this.loadingErrors = []
        this.retryCount = 0

        this.$emit('loading-started')

        // Start loading banner
        if (this.$refs.loadingBanner) {
          this.$refs.loadingBanner.startLoading()
        }

        try {
          // Execute loading phases sequentially
          for (const phase of this.loadingPhases) {
            if (this.skipPhases.includes(phase.id)) {
              console.log(`⏭️ Skipping phase: ${phase.id}`)
              continue
            }

            await this.executeLoadingPhase(phase)
          }
        } catch (error) {
          console.error('❌ Critical error during onboarding load:', error)
          await this.handleCriticalError(error)
        }
      },

      /**
       * Execute a single loading phase
       */
      async executeLoadingPhase(phase) {
        console.log(`🔄 Executing phase: ${phase.id} (${phase.percentage}%)`)

        // Update UI - progress tracking only
        console.log(`📊 Phase progress: ${phase.percentage}%`)

        // Emit progress event
        this.$emit('loading-progress', {
          phase: phase.id,
          percentage: phase.percentage,
          message: phase.message
        })

        // Execute phase action
        if (phase.action) {
          try {
            await phase.action()
            console.log(`✅ Phase completed: ${phase.id}`)
          } catch (error) {
            console.error(`❌ Phase failed: ${phase.id}`, error)
            await this.handlePhaseError(phase, error)
          }
        }

        // Add realistic delay between phases
        await this.delay(200 + Math.random() * 300)
      },

      /**
       * Phase 1: Initialize session
       */
      async initializeSession() {
        console.log('🔧 Initializing session...')

        // Simulate session initialization
        await this.delay(500)

        // Check authentication status
        const isAuthenticated = this.checkAuthStatus()
        if (!isAuthenticated) {
          throw new Error('User not authenticated')
        }

        console.log('✅ Session initialized')
      },

      /**
       * Phase 2: Load user profile data
       */
      async loadUserProfile() {
        console.log('👤 Loading user profile...')

        try {
          const result = await userProfileService.getFormAutoPopulationData()

          if (result.success && result.data) {
            this.userData = {
              id: result.data.userId,
              name: result.data.fullName || result.data.staffName,
              pfNumber: result.data.pfNumber,
              email: result.data.email,
              phone: result.data.phoneNumber,
              department: {
                id: result.data.departmentId,
                name: result.data.departmentName,
                fullName: result.data.departmentFullName,
                code: result.data.departmentCode
              },
              roles: result.data.roles || []
            }

            console.log('✅ User profile loaded:', this.userData.name)
            this.$emit('user-data-loaded', this.userData)
          } else {
            throw new Error(result.error || 'Failed to load user profile')
          }
        } catch (error) {
          console.error('❌ Error loading user profile:', error)
          throw error
        }
      },

      /**
       * Phase 3: Load declaration status
       */
      async loadDeclarationStatus() {
        console.log('📋 Checking declaration status...')

        try {
          // Simulate API call to check declaration status
          await this.delay(800)

          // Mock declaration status
          const declarationStatus = {
            hasDeclaration: Math.random() > 0.3,
            declarationDate: new Date().toISOString(),
            isValid: true,
            requiresUpdate: false
          }

          // Initialize onboarding data if not exists
          if (!this.onboardingData) {
            this.onboardingData = {}
          }

          this.onboardingData.declaration = declarationStatus

          console.log(
            '✅ Declaration status loaded:',
            declarationStatus.hasDeclaration ? 'Submitted' : 'Pending'
          )
          this.$emit('declaration-status-loaded', declarationStatus)
        } catch (error) {
          console.error('❌ Error loading declaration status:', error)
          throw error
        }
      },

      /**
       * Phase 4: Verify phone number status
       */
      async verifyPhoneStatus() {
        console.log('📱 Verifying phone number...')

        try {
          // Simulate phone verification API call
          await this.delay(600)

          const phoneVerificationStatus = {
            isVerified: Math.random() > 0.4,
            phoneNumber: this.userData?.phone || null,
            verificationDate: new Date().toISOString(),
            requiresVerification: false
          }

          // Add to onboarding data
          this.onboardingData.phoneVerification = phoneVerificationStatus

          console.log(
            '✅ Phone verification checked:',
            phoneVerificationStatus.isVerified ? 'Verified' : 'Pending'
          )
          this.$emit('phone-verification-complete', phoneVerificationStatus)
        } catch (error) {
          console.error('❌ Error verifying phone status:', error)
          throw error
        }
      },

      /**
       * Phase 5: Finalize onboarding setup
       */
      async finalizeOnboarding() {
        console.log('🏁 Finalizing onboarding...')

        // Compile onboarding summary
        this.onboardingData.summary = {
          profileComplete: !!this.userData,
          declarationSubmitted: this.onboardingData.declaration?.hasDeclaration || false,
          phoneVerified: this.onboardingData.phoneVerification?.isVerified || false,
          completedAt: new Date().toISOString(),
          nextSteps: this.determineNextSteps()
        }

        console.log('✅ Onboarding finalized')
      },

      /**
       * Phase 6: Complete onboarding
       */
      async completeOnboarding() {
        console.log('🎉 Completing onboarding process...')

        // Final delay for smooth UX
        await this.delay(300)

        console.log('✅ Onboarding process completed successfully')
      },

      /**
       * Handle loading screen completion
       */
      onLoadingComplete() {
        console.log('🎯 Loading screen animation completed')

        setTimeout(() => {
          this.isLoadingOnboarding = false
          this.$emit('loading-complete', {
            userData: this.userData,
            onboardingData: this.onboardingData
          })
        }, 200)
      },

      /**
       * Handle loading progress from LoadingScreen component
       */
      onLoadingProgress(progress) {
        // Re-emit progress from loading screen
        this.$emit('loading-progress', progress)
      },

      /**
       * Update progress manually
       */
      updateProgress(percentage) {
        if (this.$refs.loadingScreen) {
          this.$refs.loadingScreen.setProgress(percentage)
        }
      },

      /**
       * Handle phase-specific errors
       */
      async handlePhaseError(phase, error) {
        console.error(`💥 Phase error in ${phase.id}:`, error)

        this.loadingErrors.push({
          phase: phase.id,
          error: error.message,
          timestamp: new Date().toISOString()
        })

        // Emit error event
        this.$emit('loading-error', {
          phase: phase.id,
          error: error,
          canRetry: this.retryCount < this.maxRetries
        })

        // Attempt retry for recoverable errors
        if (this.retryCount < this.maxRetries && this.isRecoverableError(error)) {
          console.log(`🔄 Retrying phase ${phase.id} (attempt ${this.retryCount + 1})`)
          this.retryCount++
          this.currentLoadingMessage = `Retrying ${phase.message.toLowerCase()}...`

          await this.delay(1000)
          await this.executeLoadingPhase(phase)
        } else {
          throw error // Re-throw to handle as critical error
        }
      },

      /**
       * Handle critical errors that stop the loading process
       */
      async handleCriticalError(error) {
        console.error('💀 Critical loading error:', error)

        this.currentLoadingMessage = 'Error loading data. Please try again.'

        this.$emit('loading-error', {
          critical: true,
          error: error,
          canRetry: true
        })

        // Show error state for a few seconds, then allow retry
        await this.delay(3000)

        if (this.retryCount < this.maxRetries) {
          console.log('🔄 Attempting full reload...')
          this.retryCount++
          await this.startOnboardingLoad()
        }
      },

      /**
       * Determine if an error is recoverable
       */
      isRecoverableError(error) {
        const recoverableErrors = ['Network Error', 'Timeout', 'Server Error', 'Failed to fetch']

        return recoverableErrors.some((recoverable) => error.message.includes(recoverable))
      },

      /**
       * Determine next steps based on onboarding status
       */
      determineNextSteps() {
        const steps = []

        if (!this.onboardingData.declaration?.hasDeclaration) {
          steps.push('Complete declaration form')
        }

        if (!this.onboardingData.phoneVerification?.isVerified) {
          steps.push('Verify phone number')
        }

        if (steps.length === 0) {
          steps.push('Access dashboard')
        }

        return steps
      },

      /**
       * Check authentication status
       */
      checkAuthStatus() {
        // This would check actual authentication
        // For now, just return true
        return true
      },

      /**
       * Utility delay function
       */
      delay(ms) {
        return new Promise((resolve) => setTimeout(resolve, ms))
      },

      /**
       * Public method to restart loading
       */
      async restartLoading() {
        console.log('🔄 Restarting onboarding load...')
        this.retryCount = 0
        this.loadingErrors = []
        await this.startOnboardingLoad()
      }
    }
  }
</script>

<style scoped>
  .onboarding-loader-wrapper {
    min-height: 100vh;
    position: relative;
  }

  .onboarding-complete {
    padding: 2rem;
    max-width: 800px;
    margin: 0 auto;
    background: white;
    border-radius: 1rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  }

  .onboarding-complete h2 {
    color: #1e40af;
    margin-bottom: 1rem;
  }

  .onboarding-complete pre {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 0.5rem;
    overflow-x: auto;
    font-size: 0.875rem;
    line-height: 1.4;
  }
</style>
