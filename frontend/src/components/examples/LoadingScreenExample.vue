<template>
  <div class="loading-example">
    <!-- Example usage of LoadingScreen component -->
    <LoadingScreen
      :show="showLoading"
      :duration="loadingDuration"
      :message="loadingMessage"
      :auto-start="true"
      @loading-complete="onLoadingComplete"
      @loading-progress="onLoadingProgress"
    />

    <!-- Main app content (shown after loading) -->
    <div v-if="!showLoading" class="main-content">
      <div class="content-container">
        <h1>Welcome to Muhimbili National Hospital</h1>
        <p>Loading completed successfully!</p>

        <!-- Controls for testing -->
        <div class="controls">
          <button @click="startCustomLoading" class="btn btn-primary">
            Test Custom Loading (5s)
          </button>

          <button @click="startQuickLoading" class="btn btn-secondary">Quick Load (2s)</button>

          <button @click="resetExample" class="btn btn-outline">Reset Example</button>
        </div>

        <!-- Progress info -->
        <div v-if="progressInfo" class="progress-info">
          <h3>Last Loading Session:</h3>
          <p>Duration: {{ progressInfo.duration }}ms</p>
          <p>Final Percentage: {{ progressInfo.finalPercentage }}%</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import LoadingScreen from '@/components/common/LoadingScreen.vue'

  export default {
    name: 'LoadingScreenExample',

    components: {
      LoadingScreen
    },

    data() {
      return {
        showLoading: true,
        loadingDuration: 3000, // 3 seconds default
        loadingMessage: 'Loading your dashboard...',
        progressInfo: null,
        startTime: null
      }
    },

    mounted() {
      console.log('📱 LoadingScreen example mounted')
      this.startTime = Date.now()
    },

    methods: {
      /**
       * Handle loading completion
       */
      onLoadingComplete() {
        console.log('🎉 Loading screen completed!')
        const duration = Date.now() - this.startTime

        this.progressInfo = {
          duration: duration,
          finalPercentage: 100
        }

        // You could navigate to dashboard or trigger other actions here
        // this.$router.push('/dashboard')
      },

      /**
       * Handle loading progress updates
       */
      onLoadingProgress(progress) {
        console.log(`📊 Loading progress: ${progress.percentage}%`)

        // You could update external progress indicators here
        // or perform actions at specific progress points
        if (progress.percentage === 50) {
          console.log('🎯 Halfway through loading!')
        }
      },

      /**
       * Start custom loading with different duration
       */
      startCustomLoading() {
        this.showLoading = true
        this.loadingDuration = 5000 // 5 seconds
        this.loadingMessage = 'Custom loading process...'
        this.startTime = Date.now()
      },

      /**
       * Start quick loading
       */
      startQuickLoading() {
        this.showLoading = true
        this.loadingDuration = 2000 // 2 seconds
        this.loadingMessage = 'Quick loading...'
        this.startTime = Date.now()
      },

      /**
       * Reset the example
       */
      resetExample() {
        this.showLoading = true
        this.loadingDuration = 3000
        this.loadingMessage = 'Loading your dashboard...'
        this.progressInfo = null
        this.startTime = Date.now()
      }
    }
  }
</script>

<style scoped>
  .loading-example {
    min-height: 100vh;
    position: relative;
  }

  .main-content {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
    padding: 2rem;
  }

  .content-container {
    text-align: center;
    max-width: 600px;
    background: white;
    border-radius: 1rem;
    padding: 3rem;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
  }

  .content-container h1 {
    color: #1e40af;
    margin-bottom: 1rem;
    font-size: 2rem;
    font-weight: 700;
  }

  .content-container p {
    color: #6b7280;
    margin-bottom: 2rem;
    font-size: 1.1rem;
  }

  .controls {
    display: flex;
    gap: 1rem;
    justify-content: center;
    margin-bottom: 2rem;
    flex-wrap: wrap;
  }

  .btn {
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
    border: none;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-block;
  }

  .btn-primary {
    background: #3b82f6;
    color: white;
  }

  .btn-primary:hover {
    background: #2563eb;
    transform: translateY(-1px);
  }

  .btn-secondary {
    background: #f59e0b;
    color: white;
  }

  .btn-secondary:hover {
    background: #d97706;
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

  .progress-info {
    background: #f3f4f6;
    padding: 1.5rem;
    border-radius: 0.5rem;
    margin-top: 2rem;
  }

  .progress-info h3 {
    color: #374151;
    margin-bottom: 0.5rem;
    font-size: 1.1rem;
  }

  .progress-info p {
    color: #6b7280;
    margin: 0.25rem 0;
    font-size: 0.9rem;
  }

  @media (max-width: 768px) {
    .content-container {
      padding: 2rem;
      margin: 1rem;
    }

    .controls {
      flex-direction: column;
      align-items: center;
    }

    .btn {
      width: 100%;
      max-width: 200px;
    }
  }
</style>
