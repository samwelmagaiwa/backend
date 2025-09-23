<template>
  <div class="onboarding-wrapper">
    <!-- Simple Loading Banner for data fetching -->
    <SimpleLoadingBanner
      v-if="isLoading"
      :show="isLoading"
      :auto-start="false"
      @loading-complete="onLoadingComplete"
      ref="loadingBanner"
    />

    <!-- Main onboarding content -->
    <div v-else class="onboarding-content">
      <!-- Your existing onboarding components go here -->
      <div class="onboarding-dashboard">
        <h1>Welcome to Your Dashboard</h1>
        <p>{{ userInfo.name || 'User' }}, your onboarding data has been loaded successfully!</p>

        <!-- Sample onboarding status -->
        <div class="onboarding-status">
          <h3>Onboarding Progress</h3>
          <div class="status-grid">
            <div class="status-item" :class="{ completed: onboardingData.profileComplete }">
              <i class="fas fa-user-circle"></i>
              <span>Profile Setup</span>
            </div>
            <div class="status-item" :class="{ completed: onboardingData.declarationSubmitted }">
              <i class="fas fa-file-signature"></i>
              <span>Declaration</span>
            </div>
            <div class="status-item" :class="{ completed: onboardingData.phoneVerified }">
              <i class="fas fa-mobile-alt"></i>
              <span>Phone Verification</span>
            </div>
          </div>
        </div>

        <!-- Debug info -->
        <div v-if="debugMode" class="debug-info">
          <h4>Debug Information</h4>
          <pre>{{ JSON.stringify(onboardingData, null, 2) }}</pre>
        </div>

        <!-- Controls -->
        <div class="controls">
          <button @click="simulateReload" class="btn btn-primary">
            <i class="fas fa-sync-alt"></i>
            Simulate Data Reload
          </button>
          <button @click="toggleDebug" class="btn btn-outline">
            <i class="fas fa-bug"></i>
            Toggle Debug
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import SimpleLoadingBanner from '@/components/common/SimpleLoadingBanner.vue'

  export default {
    name: 'OnboardingWithLoading',

    components: {
      SimpleLoadingBanner
    },

    data() {
      return {
        isLoading: true,
        debugMode: false,

        // Loading configuration
        loadingProgress: 0,

        // Sample user and onboarding data
        userInfo: {},
        onboardingData: {},

        // Loading phases that match actual data fetching
        loadingPhases: [
          {
            percentage: 0,
            message: 'Connecting to server...',
            action: 'initialize'
          },
          {
            percentage: 25,
            message: 'Loading your profile...',
            action: 'fetchProfile'
          },
          {
            percentage: 50,
            message: 'Checking onboarding status...',
            action: 'fetchOnboardingData'
          },
          {
            percentage: 75,
            message: 'Verifying phone number...',
            action: 'verifyPhone'
          },
          {
            percentage: 90,
            message: 'Finalizing setup...',
            action: 'finalize'
          },
          {
            percentage: 100,
            message: 'Welcome to your dashboard!',
            action: 'complete'
          }
        ]
      }
    },

    async mounted() {
      console.log('🚀 OnboardingWithLoading component mounted')

      // Start the loading process
      this.startOnboardingLoad()
    },

    methods: {
      /**
       * Start the onboarding loading process
       */
      async startOnboardingLoad() {
        console.log('📱 Starting onboarding data load...')

        this.isLoading = true
        this.loadingProgress = 0

        // Start the loading banner animation
        if (this.$refs.loadingBanner) {
          this.$refs.loadingBanner.startLoading()
        }

        // Simulate actual data fetching with realistic timing
        await this.simulateDataFetching()
      },

      /**
       * Simulate realistic data fetching process
       */
      async simulateDataFetching() {
        // This would be replaced with actual API calls

        try {
          // Phase 1: Initialize connection (0-25%)
          await this.delay(800)
          this.updateLoadingProgress(25)

          // Phase 2: Fetch user profile (25-50%)
          const profileData = await this.fetchUserProfile()
          this.userInfo = profileData
          this.updateLoadingProgress(50)

          // Phase 3: Fetch onboarding data (50-75%)
          const onboardingStatus = await this.fetchOnboardingStatus()
          this.onboardingData = onboardingStatus
          this.updateLoadingProgress(75)

          // Phase 4: Verify phone (75-90%)
          await this.verifyPhoneStatus()
          this.updateLoadingProgress(90)

          // Phase 5: Finalize (90-100%)
          await this.finalizeSetup()
          this.updateLoadingProgress(100)
        } catch (error) {
          console.error('❌ Error during onboarding load:', error)
          this.handleLoadingError(error)
        }
      },

      /**
       * Mock API call to fetch user profile
       */
      async fetchUserProfile() {
        await this.delay(1000)

        // Simulate API response
        return {
          id: 1,
          name: 'Dr. John Muhimbili',
          email: 'john.muhimbili@mnh.go.tz',
          pfNumber: 'MNH001',
          department: 'ICT Department',
          role: 'System Administrator'
        }
      },

      /**
       * Mock API call to fetch onboarding status
       */
      async fetchOnboardingStatus() {
        await this.delay(800)

        // Simulate onboarding status response
        return {
          profileComplete: true,
          declarationSubmitted: true,
          phoneVerified: Math.random() > 0.3, // Random for demo
          currentStep: 'phone_verification',
          completedSteps: ['profile', 'declaration'],
          nextStep: 'phone_verification'
        }
      },

      /**
       * Mock API call to verify phone status
       */
      async verifyPhoneStatus() {
        await this.delay(600)

        // Simulate phone verification check
        this.onboardingData.phoneVerified = true
        this.onboardingData.completedSteps.push('phone_verification')

        return { verified: true }
      },

      /**
       * Finalize setup process
       */
      async finalizeSetup() {
        await this.delay(400)

        // Mark onboarding as complete
        this.onboardingData.currentStep = 'completed'
        this.onboardingData.completedAt = new Date().toISOString()

        console.log('✅ Onboarding setup finalized')
      },

      /**
       * Update loading progress manually
       */
      updateLoadingProgress(percentage) {
        this.loadingProgress = percentage

        // Update message based on progress
        const phase = this.loadingPhases.find((p) => p.percentage === percentage)
        if (phase) {
          this.currentLoadingMessage = phase.message
          console.log(`📊 ${phase.action}: ${phase.message}`)
        }

        // Manually set progress on loading screen
        if (this.$refs.loadingScreen) {
          this.$refs.loadingScreen.setProgress(percentage)
        }
      },

      /**
       * Handle loading completion
       */
      onLoadingComplete() {
        console.log('🎉 Onboarding loading completed!')

        // Small delay before showing content for smooth transition
        setTimeout(() => {
          this.isLoading = false
        }, 200)
      },

      /**
       * Handle loading progress updates from LoadingScreen
       */
      onLoadingProgress(progress) {
        // This receives updates from the LoadingScreen component
        console.log(`🔄 Loading screen progress: ${progress.percentage}%`)
      },

      /**
       * Handle loading errors
       */
      handleLoadingError(error) {
        console.error('💥 Loading error:', error)

        // Show error state or retry option
        this.currentLoadingMessage = 'Error loading data. Retrying...'

        // Retry after delay
        setTimeout(() => {
          this.startOnboardingLoad()
        }, 2000)
      },

      /**
       * Utility function for delays
       */
      delay(ms) {
        return new Promise((resolve) => setTimeout(resolve, ms))
      },

      /**
       * Simulate reloading data
       */
      simulateReload() {
        this.startOnboardingLoad()
      },

      /**
       * Toggle debug mode
       */
      toggleDebug() {
        this.debugMode = !this.debugMode
      }
    }
  }
</script>

<style scoped>
  .onboarding-wrapper {
    min-height: 100vh;
    position: relative;
  }

  .onboarding-content {
    min-height: 100vh;
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    padding: 2rem;
  }

  .onboarding-dashboard {
    max-width: 800px;
    margin: 0 auto;
    background: white;
    border-radius: 1rem;
    padding: 3rem;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
  }

  .onboarding-dashboard h1 {
    color: #1e40af;
    margin-bottom: 0.5rem;
    font-size: 2.5rem;
    font-weight: 800;
    text-align: center;
  }

  .onboarding-dashboard > p {
    color: #64748b;
    text-align: center;
    font-size: 1.1rem;
    margin-bottom: 3rem;
  }

  .onboarding-status {
    margin-bottom: 3rem;
  }

  .onboarding-status h3 {
    color: #374151;
    margin-bottom: 1.5rem;
    font-size: 1.3rem;
    font-weight: 600;
  }

  .status-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
  }

  .status-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 2rem;
    background: #f8fafc;
    border-radius: 1rem;
    border: 2px solid #e2e8f0;
    transition: all 0.3s ease;
  }

  .status-item.completed {
    background: #dcfdf7;
    border-color: #10b981;
    color: #065f46;
  }

  .status-item i {
    font-size: 2rem;
    margin-bottom: 1rem;
    color: #9ca3af;
  }

  .status-item.completed i {
    color: #10b981;
  }

  .status-item span {
    font-weight: 600;
    text-align: center;
  }

  .debug-info {
    background: #1f2937;
    color: #f9fafb;
    padding: 1.5rem;
    border-radius: 0.5rem;
    margin-bottom: 2rem;
    overflow-x: auto;
  }

  .debug-info h4 {
    color: #f59e0b;
    margin-bottom: 1rem;
    font-size: 1rem;
  }

  .debug-info pre {
    font-size: 0.8rem;
    line-height: 1.4;
    margin: 0;
  }

  .controls {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
  }

  .btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
    border: none;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
  }

  .btn-primary {
    background: #3b82f6;
    color: white;
  }

  .btn-primary:hover {
    background: #2563eb;
    transform: translateY(-1px);
  }

  .btn-outline {
    background: transparent;
    color: #6b7280;
    border: 2px solid #d1d5db;
  }

  .btn-outline:hover {
    background: #f9fafb;
    border-color: #9ca3af;
  }

  @media (max-width: 768px) {
    .onboarding-dashboard {
      padding: 2rem;
      margin: 1rem;
    }

    .onboarding-dashboard h1 {
      font-size: 2rem;
    }

    .status-grid {
      grid-template-columns: 1fr;
    }

    .controls {
      flex-direction: column;
      align-items: center;
    }

    .btn {
      width: 100%;
      max-width: 250px;
    }
  }
</style>
