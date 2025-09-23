<template>
  <div class="orbiting-dots" :class="sizeClass" :style="customSize">
    <!-- Small logo in center -->
    <div class="center-logo">
      <img
        src="/assets/images/logo2.png"
        alt="Loading"
        class="logo-img"
        @error="handleImageError"
      />
    </div>

    <!-- Small orbiting dots -->
    <div class="orbit">
      <div
        v-for="(dot, index) in dots"
        :key="`dot-${index}`"
        :class="`dot ${dot.color}`"
        :style="{
          transform: `rotate(${rotation + index * dotSpacing}deg) translateX(${orbitRadius}px)`
        }"
      ></div>
    </div>
  </div>
</template>

<script>
  export default {
    name: 'OrbitingDots',

    props: {
      // Size options: 'xs', 'sm', 'md', 'lg'
      size: {
        type: String,
        default: 'sm'
      },
      // Custom size in pixels (overrides size prop)
      customWidth: {
        type: Number,
        default: null
      },
      customHeight: {
        type: Number,
        default: null
      },
      // Animation speed
      speed: {
        type: Number,
        default: 3 // degrees per frame
      }
    },

    data() {
      return {
        rotation: 0,
        rotationInterval: null,
        updateFrequency: 50, // milliseconds between updates

        // 8 small dots with logo colors
        dots: [
          { color: 'logo-red' },
          { color: 'logo-gold' },
          { color: 'logo-white' },
          { color: 'logo-red' },
          { color: 'logo-gold' },
          { color: 'logo-white' },
          { color: 'logo-red' },
          { color: 'logo-gold' }
        ]
      }
    },

    computed: {
      sizeClass() {
        if (this.customWidth || this.customHeight) return ''
        return `size-${this.size}`
      },

      customSize() {
        if (this.customWidth || this.customHeight) {
          return {
            width: this.customWidth ? `${this.customWidth}px` : 'auto',
            height: this.customHeight ? `${this.customHeight}px` : 'auto'
          }
        }
        return {}
      },

      dotSpacing() {
        return 360 / this.dots.length // degrees between dots
      },

      orbitRadius() {
        // Adjust orbit radius based on size
        if (this.customWidth) return Math.max(8, this.customWidth / 4)

        switch (this.size) {
          case 'xs':
            return 6
          case 'sm':
            return 8
          case 'md':
            return 12
          case 'lg':
            return 16
          default:
            return 8
        }
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
        this.rotationInterval = setInterval(() => {
          this.rotation = (this.rotation + this.speed) % 360
        }, this.updateFrequency)
      },

      stopAnimation() {
        if (this.rotationInterval) {
          clearInterval(this.rotationInterval)
          this.rotationInterval = null
        }
      },

      handleImageError() {
        // Fallback: hide logo if it fails to load
        const logoEl = this.$el?.querySelector('.center-logo')
        if (logoEl) logoEl.style.display = 'none'
      }
    }
  }
</script>

<style scoped>
  .orbiting-dots {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }

  /* Size variants */
  .size-xs {
    width: 16px;
    height: 16px;
  }
  .size-sm {
    width: 20px;
    height: 20px;
  }
  .size-md {
    width: 28px;
    height: 28px;
  }
  .size-lg {
    width: 36px;
    height: 36px;
  }

  .center-logo {
    position: relative;
    z-index: 2;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .logo-img {
    width: 50%;
    height: 50%;
    object-fit: contain;
    opacity: 0.8;
  }

  .orbit {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 100%;
    height: 100%;
    pointer-events: none;
  }

  .dot {
    position: absolute;
    top: 50%;
    left: 50%;
    border-radius: 50%;
    transform-origin: 0 0;
  }

  /* Size-based dot sizing */
  .size-xs .dot {
    width: 2px;
    height: 2px;
  }
  .size-sm .dot {
    width: 2.5px;
    height: 2.5px;
  }
  .size-md .dot {
    width: 3px;
    height: 3px;
  }
  .size-lg .dot {
    width: 4px;
    height: 4px;
  }

  /* Custom size dot sizing */
  .orbiting-dots:not([class*='size-']) .dot {
    width: 3px;
    height: 3px;
  }

  /* Logo colors */
  .dot.logo-red {
    background: #dc143c;
  }
  .dot.logo-gold {
    background: #ffd700;
  }
  .dot.logo-white {
    background: #ffffff;
    border: 1px solid rgba(0, 0, 0, 0.2);
  }

  /* For dark backgrounds, make white dots more visible */
  .dark .dot.logo-white,
  .bg-dark .dot.logo-white,
  .text-white .dot.logo-white {
    background: #ffffff;
    border: 1px solid rgba(255, 255, 255, 0.3);
    box-shadow: 0 0 2px rgba(255, 255, 255, 0.5);
  }

  /* Reduced motion support */
  @media (prefers-reduced-motion: reduce) {
    .orbit {
      animation: none;
    }
    .dot {
      position: static;
      display: inline-block;
      margin: 0 1px;
      transform: none !important;
    }
  }
</style>
