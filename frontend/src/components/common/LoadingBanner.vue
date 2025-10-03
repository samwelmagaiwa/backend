<template>
  <div class="loading-banner">
    <div class="loading-container">
      <!-- Static Logo in Center -->
      <div class="logo-container">
        <img
          src="/assets/images/logo2.png"
          alt="Muhimbili National Hospital"
          class="hospital-logo"
          @error="handleImageError"
        />
      </div>

      <!-- Orbiting Dots -->
      <div
        class="orbit-container"
        :style="{ transform: `translate(-50%, -50%) rotate(${rotation}deg)` }"
      >
        <div
          v-for="(dot, index) in dots"
          :key="`dot-${index}`"
          :class="`orbit-dot dot-${dot.color}`"
          :style="{
            transform: `rotate(${index * dotSpacing}deg) translateY(-48px)`
          }"
        ></div>
      </div>
    </div>

    <!-- Loading Text -->
    <div class="loading-text">Loading requests...</div>
  </div>
</template>

<script>
  export default {
    name: 'LoadingBanner',

    data() {
      return {
        rotation: 0,
        rotationInterval: null,

        // 24 dots with alternating colors: red, yellow, white
        dots: [
          { color: 'red' },
          { color: 'yellow' },
          { color: 'white' },
          { color: 'red' },
          { color: 'yellow' },
          { color: 'white' },
          { color: 'red' },
          { color: 'yellow' },
          { color: 'white' },
          { color: 'red' },
          { color: 'yellow' },
          { color: 'white' },
          { color: 'red' },
          { color: 'yellow' },
          { color: 'white' },
          { color: 'red' },
          { color: 'yellow' },
          { color: 'white' },
          { color: 'red' },
          { color: 'yellow' },
          { color: 'white' },
          { color: 'red' },
          { color: 'yellow' },
          { color: 'white' }
        ]
      }
    },

    computed: {
      dotSpacing() {
        return 360 / this.dots.length // 15 degrees between each dot
      }
    },

    mounted() {
      this.startAnimation()
    },

    beforeUnmount() {
      this.stopAnimation()
    },

    methods: {
      startAnimation() {
        // High speed: 360 degrees in 1.5 seconds = 240 degrees per second
        // At 60fps: 240/60 = 4 degrees per frame
        this.rotationInterval = setInterval(() => {
          this.rotation = (this.rotation + 4) % 360
        }, 16.67) // ~60fps
      },

      stopAnimation() {
        if (this.rotationInterval) {
          clearInterval(this.rotationInterval)
          this.rotationInterval = null
        }
      },

      handleImageError() {
        // Hide logo if image fails to load
        const logoEl = this.$el?.querySelector('.hospital-logo')
        if (logoEl) logoEl.style.display = 'none'
      }
    }
  }
</script>

<style scoped>
  .loading-banner {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 120px;
    padding: 20px 15px;
  }

  .loading-container {
    position: relative;
    width: 120px;
    height: 120px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 16px;
  }

  /* Static Logo Container */
  .logo-container {
    position: relative;
    z-index: 10;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 50px;
    height: 50px;
    background: rgba(255, 255, 255, 0.9);
    border-radius: 50%;
    box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
  }

  .hospital-logo {
    width: 36px;
    height: 36px;
    object-fit: contain;
    opacity: 0.9;
  }

  /* Orbit Container */
  .orbit-container {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 100%;
    height: 100%;
    transform-origin: 50% 50%;
    pointer-events: none;
  }

  /* Individual Orbit Dots */
  .orbit-dot {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    transform-origin: 50% 48px;
    margin-left: -4px;
    margin-top: -4px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
  }

  /* Dot Colors */
  .dot-red {
    background: #dc2626;
    border: 1px solid #b91c1c;
  }

  .dot-yellow {
    background: #fbbf24;
    border: 1px solid #f59e0b;
  }

  .dot-white {
    background: #ffffff;
    border: 1px solid #d1d5db;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
  }

  /* Loading Text */
  .loading-text {
    font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    font-size: 14px;
    font-weight: 600;
    color: #333333;
    text-align: center;
    letter-spacing: 0.3px;
    margin-top: 6px;
  }

  /* Responsive Design */
  @media (max-width: 640px) {
    .loading-container {
      width: 100px;
      height: 100px;
    }

    .logo-container {
      width: 40px;
      height: 40px;
    }

    .hospital-logo {
      width: 28px;
      height: 28px;
    }

    .orbit-dot {
      width: 6px;
      height: 6px;
      transform-origin: 0 40px; /* Scaled down orbit radius */
    }

    .loading-text {
      font-size: 13px;
    }
  }

  @media (max-width: 480px) {
    .loading-container {
      width: 90px;
      height: 90px;
    }

    .logo-container {
      width: 36px;
      height: 36px;
    }

    .hospital-logo {
      width: 24px;
      height: 24px;
    }

    .orbit-dot {
      width: 5px;
      height: 5px;
      transform-origin: 0 36px; /* Further scaled down */
    }

    .loading-text {
      font-size: 12px;
    }
  }

  /* Smooth Animation */
  .orbit-container {
    transition: none; /* Remove any transition for smooth rotation */
  }

  /* Performance Optimization */
  .orbit-dot {
    will-change: transform;
  }

  /* Accessibility - Reduced Motion */
  @media (prefers-reduced-motion: reduce) {
    .orbit-container {
      animation: none !important;
    }

    .loading-banner .orbit-container {
      transform: translate(-50%, -50%) !important;
    }

    .orbit-dot {
      animation: pulse 2s ease-in-out infinite alternate;
    }

    @keyframes pulse {
      from {
        opacity: 0.4;
      }
      to {
        opacity: 1;
      }
    }
  }
</style>
