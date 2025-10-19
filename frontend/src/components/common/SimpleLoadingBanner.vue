<template>
  <div v-if="isVisible" class="loading-banner" :class="{ 'fade-out': isComplete }">
    <div
      class="loading-card rounded-xl shadow-2xl p-8 text-center border border-blue-400/40"
      style="background: linear-gradient(90deg, #0b3a82, #0a2f6f, #0b3a82)"
    >
      <div class="flex justify-center mb-4">
        <div
          class="w-12 h-12 border-4 border-white border-t-transparent rounded-full animate-spin"
        ></div>
      </div>
      <p class="text-blue-100 font-medium">Loading...</p>
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
        isComplete: false
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

    methods: {
      startLoading() {
        this.isVisible = true
        this.isComplete = false
      },
      stopLoading() {
        this.isVisible = false
      },
      completeLoading() {
        this.isComplete = true
        setTimeout(() => {
          this.isVisible = false
          this.$emit('loading-complete')
        }, 300)
      }
    }
  }
</script>

<style scoped>
  /* Main loading banner container */
  .loading-banner {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(0, 0, 0, 0.5);
    opacity: 1;
    transition: opacity 0.3s ease-out;
    pointer-events: auto;
  }

  /* Fade out animation */
  .loading-banner.fade-out {
    opacity: 0;
  }

  /* Loader card */
  .loading-card {
    min-width: 280px;
  }

  /* Logo container with positioning for orbit */
  .logo-container {
    position: relative;
    display: inline-block;
    margin-bottom: 1rem;
  }

  /* Deprecated styles kept for reference; no longer used */
  .logo-wrapper {
    position: relative;
    z-index: 2;
    border-radius: 50%;
    padding: 1rem;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 2px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
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
