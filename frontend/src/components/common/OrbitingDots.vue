<template>
  <div class="orbiting-dots" :class="sizeClass" :style="customSize">
    <!-- Static logo in center -->
    <div class="center-logo">
      <img
        src="/assets/images/logo2.png"
        alt="Loading"
        class="logo-img"
        @error="handleImageError"
      />
    </div>

    <!-- Orbiting dots (CSS-animated container for smoothness) -->
    <div class="orbit" :style="orbitStyle">
      <div
        v-for="(dot, index) in dots"
        :key="`dot-${index}`"
        :class="`dot ${dot.color}`"
        :style="getDotStyle(index)"
      ></div>
    </div>
  </div>
</template>

<script>
  export default {
    name: 'OrbitingDots',

    props: {
      // Size options: 'xs', 'sm', 'md', 'lg', 'xl', '2xl'
      size: { type: String, default: 'sm' },
      // Custom size in pixels (overrides size prop)
      customWidth: { type: Number, default: null },
      customHeight: { type: Number, default: null },
      // Animation speed in degrees per frame (kept for backwards compatibility)
      speed: { type: Number, default: 3 }
    },

    data() {
      return {
        updateFrequency: 50, // ms; used to derive duration from speed
        // 24 dots with logo colors in repeating pattern
        dots: [
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
          { color: 'logo-white' },
          { color: 'logo-red' },
          { color: 'logo-gold' },
          { color: 'logo-white' }
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
        return 360 / this.dots.length
      },
      orbitRadius() {
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
          case 'xl':
            return 24
          case '2xl':
            return 45
          default:
            return 8
        }
      },
      currentContainerSize() {
        if (this.customWidth) return this.customWidth
        switch (this.size) {
          case 'xs':
            return 16
          case 'sm':
            return 20
          case 'md':
            return 28
          case 'lg':
            return 36
          case 'xl':
            return 64
          case '2xl':
            return 120
          default:
            return 20
        }
      },
      // Convert speed + updateFrequency to a smooth CSS animation duration (seconds)
      durationSeconds() {
        const degPerSec = this.speed * (1000 / this.updateFrequency)
        const sec = degPerSec > 0 ? 360 / degPerSec : 1.5
        return Math.max(0.8, Math.min(sec, 4)) // clamp for stability
      },
      orbitStyle() {
        return { '--orbit-duration': `${this.durationSeconds}s` }
      }
    },

    methods: {
      getDotStyle(index) {
        const angle = index * this.dotSpacing
        const radius = this.orbitRadius
        return {
          top: '50%',
          left: '50%',
          transform: `translate(-50%, -50%) rotate(${angle}deg) translateX(${radius}px)`
        }
      },
      handleImageError() {
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
  .size-xl {
    width: 64px;
    height: 64px;
  }
  .size-2xl {
    width: 120px;
    height: 120px;
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
    opacity: 0.9;
  }

  /* Larger logo for 2xl size */
  .size-2xl .logo-img {
    width: 60%;
    height: 60%;
  }

  .orbit {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    transform-origin: 50% 50%;
    animation: spin var(--orbit-duration, 1.5s) linear infinite;
    will-change: transform;
  }

  @keyframes spin {
    from {
      transform: rotate(0deg);
    }
    to {
      transform: rotate(360deg);
    }
  }

  .dot {
    position: absolute;
    border-radius: 50%;
    will-change: transform;
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
  .size-xl .dot {
    width: 6px;
    height: 6px;
  }
  .size-2xl .dot {
    width: 8px;
    height: 8px;
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
      animation: none !important;
      transform: none !important;
    }
    .dot {
      position: static;
      display: inline-block;
      margin: 0 1px;
      transform: none !important;
    }
  }
</style>
