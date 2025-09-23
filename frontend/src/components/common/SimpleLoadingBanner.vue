<template>
  <div v-if="isVisible" class="loading-banner" :class="{ 'fade-out': isComplete }">
    <!-- Logo container with orbiting dots only -->
    <div class="logo-container">
      <!-- Logo only -->
      <div class="logo-wrapper">
        <img
          src="/assets/images/logo2.png"
          alt="Loading..."
          class="hospital-logo"
          @error="handleImageError"
        />
      </div>

      <!-- Orbiting loading dots - matching logo colors -->
      <div class="orbit-container">
        <div
          v-for="(dot, index) in logoDots"
          :key="`logo-${index}`"
          :class="`orbit-dot ${dot.color}`"
          :style="{ transform: `rotate(${rotation + index * 15}deg) translateX(100px)` }"
        ></div>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    name: 'SimpleLoadingBanner',

    props: {
      // Control visibility externally
      show: {
        type: Boolean,
        default: true
      },

      // Auto start loading when component mounts
      autoStart: {
        type: Boolean,
        default: true
      }
    },

    emits: ['loading-complete'],

    data() {
      return {
        isVisible: true,
        isComplete: false,
        rotation: 0,

        // Animation intervals
        rotationInterval: null,

        // Timing configurations
        rotationSpeed: 3, // degrees per frame
        updateFrequency: 40, // milliseconds between updates

        // 24 orbiting dots matching Muhimbili Hospital logo colors
        logoDots: [
          { color: 'logo-red' }, // Logo red
          { color: 'logo-gold' }, // Logo gold
          { color: 'logo-white' }, // Logo white
          { color: 'logo-red' },
          { color: 'logo-gold' },
          { color: 'logo-white' },
          { color: 'logo-red' },
          { color: 'logo-gold' },
          { color: 'logo-white' },
          { color: 'logo-red' },
          { color: 'logo-gold' },
          { color: 'logo-white' },
          { color: 'logo-red' },
          { color: 'logo-gold' },
          { color: 'logo-white' },
          { color: 'logo-red' },
          { color: 'logo-gold' },
          { color: 'logo-white' },
          { color: 'logo-red' },
          { color: 'logo-gold' },
          { color: 'logo-white' },
          { color: 'logo-red' },
          { color: 'logo-gold' },
          { color: 'logo-white' }
        ]
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
      }
    },

    mounted() {
      if (this.autoStart && this.show) {
        this.startLoading()
      }
    },

    beforeUnmount() {
      this.cleanup()
    },

    methods: {
      /**
       * Start the loading animation
       */
      startLoading() {
        this.isVisible = true
        this.isComplete = false
        this.rotation = 0

        // Start rotation animation
        this.rotationInterval = setInterval(() => {
          this.rotation = (this.rotation + this.rotationSpeed) % 360
        }, this.updateFrequency)
      },

      /**
       * Stop loading animation
       */
      stopLoading() {
        this.cleanup()
        this.isVisible = false
      },

      /**
       * Complete loading and trigger fade out
       */
      completeLoading() {
        // Start fade out
        this.isComplete = true

        // Hide completely after fade animation
        setTimeout(() => {
          this.isVisible = false
          this.$emit('loading-complete')
        }, 300) // Match CSS transition duration
      },

      /**
       * Clean up intervals
       */
      cleanup() {
        if (this.rotationInterval) {
          clearInterval(this.rotationInterval)
          this.rotationInterval = null
        }
      },

      /**
       * Handle logo image load error
       */
      handleImageError() {
        console.warn('⚠️ Hospital logo failed to load, using fallback')
      }
    }
  }
</script>

<style scoped>
  /* Main loading banner container */
  .loading-banner {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 1;
    transition: opacity 0.3s ease-out;
    pointer-events: none;
  }

  /* Fade out animation */
  .loading-banner.fade-out {
    opacity: 0;
  }

  /* Logo container with positioning for orbit */
  .logo-container {
    position: relative;
    display: inline-block;
    margin-bottom: 1rem;
  }

  /* Logo wrapper */
  .logo-wrapper {
    position: relative;
    z-index: 2;
    border-radius: 50%;
    padding: 1rem;
  }

  /* Hospital logo styling */
  .hospital-logo {
    width: 120px;
    height: 120px;
    object-fit: contain;
  }

  /* Orbit container for rotating dots */
  .orbit-container {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 320px;
    height: 320px;
    pointer-events: none;
  }

  /* Individual orbit dots */
  .orbit-dot {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    transform-origin: 0 0;
    animation: logoPulse 2s ease-in-out infinite;
  }

  /* Logo colors */
  .orbit-dot.logo-red {
    background: #dc143c;
  }

  .orbit-dot.logo-gold {
    background: #ffd700;
  }

  .orbit-dot.logo-white {
    background: #ffffff;
    border: 1px solid rgba(0, 0, 0, 0.2);
  }

  /* Pulse animation */
  @keyframes logoPulse {
    0%,
    100% {
      transform: translateX(100px) scale(1);
      opacity: 0.8;
    }
    50% {
      transform: translateX(100px) scale(1.4);
      opacity: 1;
    }
  }

  /* Responsive Design */
  @media (max-width: 768px) {
    .hospital-logo {
      width: 80px;
      height: 80px;
    }

    .orbit-container {
      width: 240px;
      height: 240px;
    }

    .orbit-dot {
      width: 10px;
      height: 10px;
    }

    @keyframes logoPulse {
      0%,
      100% {
        transform: translateX(75px) scale(1);
        opacity: 0.8;
      }
      50% {
        transform: translateX(75px) scale(1.3);
        opacity: 1;
      }
    }
  }

  @media (max-width: 480px) {
    .hospital-logo {
      width: 60px;
      height: 60px;
    }

    .orbit-container {
      width: 180px;
      height: 180px;
    }

    .orbit-dot {
      width: 8px;
      height: 8px;
    }

    @keyframes logoPulse {
      0%,
      100% {
        transform: translateX(55px) scale(1);
        opacity: 0.8;
      }
      50% {
        transform: translateX(55px) scale(1.2);
        opacity: 1;
      }
    }
  }

  /* Reduced motion support */
  @media (prefers-reduced-motion: reduce) {
    .orbit-dot {
      animation: none;
    }

    .loading-banner {
      transition: opacity 0.1s ease;
    }
  }

  /* Print styles (hide loading banner) */
  @media print {
    .loading-banner {
      display: none !important;
    }
  }
</style>
