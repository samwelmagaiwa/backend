<template>
  <div v-if="isVisible" class="loading-screen" :class="{ 'fade-out': isComplete }">
    <!-- Background overlay -->
    <div class="loading-overlay"></div>

    <!-- Main content container -->
    <div class="loading-content">
      <!-- Logo container with orbiting dots -->
      <div class="logo-container">
        <!-- Muhimbili Logo -->
        <div class="logo-wrapper">
          <img
            src="/assets/images/logo2.png"
            alt="Muhimbili National Hospital"
            class="hospital-logo"
            @error="handleImageError"
          />
        </div>

        <!-- Orbiting loading dots - 24 dots in a perfect circle with rainbow colors -->
        <div class="orbit-container">
          <!-- Single circular orbit with 24 logo-colored dots -->
          <div
            v-for="(dot, index) in logoDots"
            :key="`logo-${index}`"
            :class="`orbit-dot ${dot.color}`"
            :style="{ transform: `rotate(${rotation + index * 15}deg) translateX(90px)` }"
          ></div>
        </div>
      </div>

      <!-- Loading text and percentage -->
      <div class="loading-text">
        <h2 class="hospital-title">Muhimbili National Hospital</h2>
        <p class="loading-subtitle">{{ loadingMessage }}</p>

        <!-- Percentage counter -->
        <div class="percentage-container">
          <span class="percentage">{{ Math.floor(currentPercentage) }}%</span>

          <!-- Progress bar -->
          <div class="progress-bar">
            <div class="progress-fill" :style="{ width: currentPercentage + '%' }"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    name: 'LoadingScreen',

    props: {
      // Control visibility externally
      show: {
        type: Boolean,
        default: true
      },

      // Custom loading duration in milliseconds
      duration: {
        type: Number,
        default: 2000
      },

      // Custom loading message
      message: {
        type: String,
        default: 'Loading your dashboard...'
      },

      // Auto start loading when component mounts
      autoStart: {
        type: Boolean,
        default: true
      }
    },

    emits: ['loading-complete', 'loading-progress'],

    data() {
      return {
        isVisible: true,
        isComplete: false,
        currentPercentage: 0,
        rotation: 0,
        loadingMessage: '',

        // Animation intervals
        rotationInterval: null,
        progressInterval: null,

        // Timing configurations - Optimized for faster loading
        rotationSpeed: 3, // degrees per frame - faster rotation
        updateFrequency: 40, // milliseconds between updates - more responsive

        // Hospital-themed loading phases with medical terminology
        loadingPhases: [
          { threshold: 0, message: 'Initializing medical system...' },
          { threshold: 15, message: 'Verifying credentials...' },
          { threshold: 30, message: 'Loading patient records...' },
          { threshold: 50, message: 'Preparing medical dashboard...' },
          { threshold: 70, message: 'Configuring hospital interface...' },
          { threshold: 85, message: 'Finalizing medical setup...' },
          { threshold: 95, message: 'Ready for patient care!' }
        ],

        // 24 orbiting dots matching exact Muhimbili Hospital logo colors
        // Red (from logo oval), Yellow/Gold (from torch), White (from background/text)
        logoDots: [
          { color: 'logo-red' }, // 0° - Logo red
          { color: 'logo-gold' }, // 15° - Logo gold
          { color: 'logo-white' }, // 30° - Logo white
          { color: 'logo-red' }, // 45° - Logo red
          { color: 'logo-gold' }, // 60° - Logo gold
          { color: 'logo-white' }, // 75° - Logo white
          { color: 'logo-red' }, // 90° - Logo red
          { color: 'logo-gold' }, // 105° - Logo gold
          { color: 'logo-white' }, // 120° - Logo white
          { color: 'logo-red' }, // 135° - Logo red
          { color: 'logo-gold' }, // 150° - Logo gold
          { color: 'logo-white' }, // 165° - Logo white
          { color: 'logo-red' }, // 180° - Logo red
          { color: 'logo-gold' }, // 195° - Logo gold
          { color: 'logo-white' }, // 210° - Logo white
          { color: 'logo-red' }, // 225° - Logo red
          { color: 'logo-gold' }, // 240° - Logo gold
          { color: 'logo-white' }, // 255° - Logo white
          { color: 'logo-red' }, // 270° - Logo red
          { color: 'logo-gold' }, // 285° - Logo gold
          { color: 'logo-white' }, // 300° - Logo white
          { color: 'logo-red' }, // 315° - Logo red
          { color: 'logo-gold' }, // 330° - Logo gold
          { color: 'logo-white' } // 345° - Logo white
        ]
      }
    },

    computed: {
      progressIncrement() {
        // Calculate how much to increment per update
        return 100 / (this.duration / this.updateFrequency)
      }
    },

    watch: {
      show: {
        handler(newVal) {
          if (newVal && !this.isVisible) {
            this.startLoading()
          } else if (!newVal) {
            this.stopLoading()
          }
        },
        immediate: true
      },

      currentPercentage(newVal) {
        // Update loading message based on progress
        this.updateLoadingMessage(newVal)

        // Emit progress event
        this.$emit('loading-progress', {
          percentage: newVal,
          isComplete: newVal >= 100
        })

        // Complete loading when reaching 100%
        if (newVal >= 100) {
          this.completeLoading()
        }
      }
    },

    mounted() {
      console.log('🔄 LoadingScreen component mounted')

      if (this.autoStart && this.show) {
        this.startLoading()
      }

      // Set initial message
      this.loadingMessage = this.message
    },

    beforeUnmount() {
      this.cleanup()
    },

    methods: {
      /**
       * Start the loading animation and progress
       */
      startLoading() {
        console.log('▶️ Starting loading screen animation')

        this.isVisible = true
        this.isComplete = false
        this.currentPercentage = 0
        this.rotation = 0
        this.loadingMessage = this.message

        // Start rotation animation
        this.rotationInterval = setInterval(() => {
          this.rotation = (this.rotation + this.rotationSpeed) % 360
        }, this.updateFrequency)

        // Start progress counter
        this.progressInterval = setInterval(() => {
          if (this.currentPercentage < 100) {
            this.currentPercentage = Math.min(100, this.currentPercentage + this.progressIncrement)
          }
        }, this.updateFrequency)
      },

      /**
       * Stop loading animation
       */
      stopLoading() {
        console.log('⏸️ Stopping loading screen')
        this.cleanup()
        this.isVisible = false
      },

      /**
       * Complete loading and trigger fade out
       */
      completeLoading() {
        console.log('✅ Loading complete, starting fade out')

        this.currentPercentage = 100
        this.loadingMessage = 'Welcome!'

        // Start fade out after a brief delay
        setTimeout(() => {
          this.isComplete = true

          // Hide completely after fade animation
          setTimeout(() => {
            this.isVisible = false
            this.$emit('loading-complete')
          }, 800) // Match CSS transition duration
        }, 300) // Brief pause at 100%
      },

      /**
       * Update loading message based on progress
       */
      updateLoadingMessage(percentage) {
        // Find appropriate message for current progress
        for (let i = this.loadingPhases.length - 1; i >= 0; i--) {
          if (percentage >= this.loadingPhases[i].threshold) {
            const newMessage = this.loadingPhases[i].message
            if (this.loadingMessage !== newMessage) {
              this.loadingMessage = newMessage
            }
            break
          }
        }
      },

      /**
       * Clean up intervals
       */
      cleanup() {
        if (this.rotationInterval) {
          clearInterval(this.rotationInterval)
          this.rotationInterval = null
        }

        if (this.progressInterval) {
          clearInterval(this.progressInterval)
          this.progressInterval = null
        }
      },

      /**
       * Handle logo image load error
       */
      handleImageError() {
        console.warn('⚠️ Hospital logo failed to load, using fallback')
        // Could implement fallback logo or text here
      },

      /**
       * Manually set progress (for external control)
       */
      setProgress(percentage) {
        this.currentPercentage = Math.min(100, Math.max(0, percentage))
      },

      /**
       * Reset loading screen
       */
      reset() {
        this.cleanup()
        this.currentPercentage = 0
        this.rotation = 0
        this.isComplete = false
        this.loadingMessage = this.message
      }
    }
  }
</script>

<style scoped>
  /* Main loading screen container */
  .loading-screen {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 1;
    transition: opacity 0.8s ease-out;
  }

  /* Fade out animation */
  .loading-screen.fade-out {
    opacity: 0;
  }

  /* Background overlay with solid blue theme matching logo */
  .loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(
      135deg,
      #1e3a8a 0%,
      /* Navy blue */ #1e40af 25%,
      /* Deep blue */ #2563eb 50%,
      /* Royal blue */ #3b82f6 75%,
      /* Blue */ #1e3a8a 100% /* Back to navy blue */
    );
    animation: gradientShift 6s ease-in-out infinite;
  }

  /* Subtle gradient animation */
  @keyframes gradientShift {
    0%,
    100% {
      opacity: 1;
    }
    50% {
      opacity: 0.9;
    }
  }

  /* Main content container */
  .loading-content {
    position: relative;
    text-align: center;
    z-index: 10;
    max-width: 400px;
    padding: 2rem;
  }

  /* Logo container with positioning for orbit */
  .logo-container {
    position: relative;
    display: inline-block;
    margin-bottom: 3rem;
  }

  /* Logo wrapper */
  .logo-wrapper {
    position: relative;
    z-index: 2;
    background: rgba(255, 255, 255, 0.95);
    border-radius: 50%;
    padding: 1.5rem;
    box-shadow:
      0 10px 30px rgba(0, 0, 0, 0.2),
      0 4px 10px rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(10px);
  }

  /* Hospital logo styling */
  .hospital-logo {
    width: 100px;
    height: 100px;
    object-fit: contain;
    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
    transition: transform 0.3s ease;
  }

  .hospital-logo:hover {
    transform: scale(1.05);
  }

  /* Orbit container for rotating dots - Expanded for 24 dots */
  .orbit-container {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 280px;
    height: 280px;
    pointer-events: none;
  }

  /* Individual orbit dots - Logo colors */
  .orbit-dot {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    transform-origin: 0 0;
    animation: logoPulse 2s ease-in-out infinite;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
  }

  /* Muhimbili Hospital Logo Colors - Exact matches */
  /* Red from central oval, Gold from torch, White from background/text */

  .orbit-dot.logo-red {
    background: linear-gradient(45deg, #dc143c, #b91c1c);
    box-shadow: 0 0 20px rgba(220, 20, 60, 1);
    border: 1px solid rgba(220, 20, 60, 0.8);
  }

  .orbit-dot.logo-gold {
    background: linear-gradient(45deg, #ffd700, #f59e0b);
    box-shadow: 0 0 20px rgba(255, 215, 0, 1);
    border: 1px solid rgba(255, 215, 0, 0.8);
  }

  .orbit-dot.logo-white {
    background: linear-gradient(45deg, #ffffff, #f8fafc);
    box-shadow: 0 0 20px rgba(255, 255, 255, 1);
    border: 1px solid rgba(255, 255, 255, 0.9);
  }

  /* Logo color pulse animation for single orbit */
  @keyframes logoPulse {
    0%,
    100% {
      transform: translateX(90px) scale(1);
      opacity: 0.9;
    }
    50% {
      transform: translateX(90px) scale(1.5);
      opacity: 1;
    }
  }

  /* Loading text section */
  .loading-text {
    color: white;
  }

  /* Hospital title */
  .hospital-title {
    font-size: 1.75rem;
    font-weight: 700;
    margin: 0 0 0.5rem 0;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    letter-spacing: 0.02em;
  }

  /* Loading subtitle */
  .loading-subtitle {
    font-size: 1.1rem;
    margin: 0 0 2rem 0;
    opacity: 0.9;
    font-weight: 400;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
    min-height: 1.5rem; /* Prevent layout shift when message changes */
    transition: opacity 0.3s ease;
  }

  /* Percentage container */
  .percentage-container {
    margin-top: 1.5rem;
  }

  /* Percentage display */
  .percentage {
    display: block;
    font-size: 2.5rem;
    font-weight: 800;
    margin-bottom: 1rem;
    color: #fbbf24; /* Gold color */
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.4);
    font-family: 'Courier New', monospace;
  }

  /* Progress bar container */
  .progress-bar {
    width: 100%;
    height: 6px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 3px;
    overflow: hidden;
    box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.2);
  }

  /* Progress bar fill */
  .progress-fill {
    height: 100%;
    background: linear-gradient(
      90deg,
      #fbbf24 0%,
      /* Gold */ #f59e0b 50%,
      /* Amber */ #fbbf24 100% /* Gold */
    );
    border-radius: 3px;
    transition: width 0.3s ease;
    animation: shimmer 2s ease-in-out infinite;
  }

  /* Shimmer effect on progress bar */
  @keyframes shimmer {
    0% {
      transform: translateX(-100%);
    }
    100% {
      transform: translateX(100%);
    }
  }

  .progress-fill::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(
      90deg,
      transparent 0%,
      rgba(255, 255, 255, 0.4) 50%,
      transparent 100%
    );
    animation: shimmer 2s ease-in-out infinite;
  }

  /* Responsive Design */
  @media (max-width: 768px) {
    .loading-content {
      padding: 1rem;
      max-width: 320px;
    }

    .hospital-logo {
      width: 80px;
      height: 80px;
    }

    .orbit-container {
      width: 220px;
      height: 220px;
    }

    .orbit-dot {
      width: 6px;
      height: 6px;
    }

    @keyframes logoPulse {
      0%,
      100% {
        transform: translateX(65px) scale(1);
        opacity: 0.9;
      }
      50% {
        transform: translateX(65px) scale(1.4);
        opacity: 1;
      }
    }

    .hospital-title {
      font-size: 1.5rem;
    }

    .loading-subtitle {
      font-size: 1rem;
    }

    .percentage {
      font-size: 2rem;
    }
  }

  @media (max-width: 480px) {
    .loading-content {
      padding: 0.5rem;
      max-width: 280px;
    }

    .hospital-logo {
      width: 70px;
      height: 70px;
    }

    .orbit-container {
      width: 160px;
      height: 160px;
    }

    .orbit-dot {
      width: 4px;
      height: 4px;
    }

    @keyframes logoPulse {
      0%,
      100% {
        transform: translateX(45px) scale(1);
        opacity: 0.9;
      }
      50% {
        transform: translateX(45px) scale(1.3);
        opacity: 1;
      }
    }

    .hospital-title {
      font-size: 1.25rem;
    }

    .percentage {
      font-size: 1.75rem;
    }
  }

  /* High contrast mode support */
  @media (prefers-contrast: high) {
    .loading-overlay {
      background: linear-gradient(135deg, #000033 0%, #000066 100%);
    }

    .hospital-title,
    .loading-subtitle {
      text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);
    }
  }

  /* Reduced motion support */
  @media (prefers-reduced-motion: reduce) {
    .orbit-dot {
      animation: none;
    }

    .progress-fill {
      animation: none;
    }

    .loading-overlay {
      animation: none;
    }

    .loading-screen {
      transition: opacity 0.3s ease;
    }
  }

  /* Print styles (hide loading screen) */
  @media print {
    .loading-screen {
      display: none !important;
    }
  }
</style>
