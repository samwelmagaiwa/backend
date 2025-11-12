<template>
  <footer
    class="bg-gradient-to-r from-blue-900 via-blue-800 to-blue-900 text-white py-4 px-6 border-t border-blue-600/40 shadow-lg backdrop-blur-sm relative"
    style="position: relative; z-index: 1; margin-top: auto"
  >
    <div class="flex flex-col md:flex-row justify-between items-center">
      <div class="text-base text-blue-100 flex items-center space-x-2">
        <div
          class="w-6 h-6 bg-blue-600/30 rounded-full flex items-center justify-center border border-blue-500/40"
        >
          <i class="fas fa-hospital text-blue-300 text-sm"></i>
        </div>
        <p class="font-medium">
          &copy; {{ currentYear }} Muhimbili National Hospital. All rights reserved.
        </p>
      </div>
    </div>
  </footer>
</template>

<script>
  export default {
    name: 'AppFooter',
    computed: {
      currentYear() {
        return new Date().getFullYear()
      }
    }
  }
</script>

<style scoped>
  /* Footer specific styles */
  footer {
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
  }

  /* Icon hover effects */
  .w-6.h-6:hover {
    transform: scale(1.1);
    transition: transform 0.2s ease;
  }

  /* Text hover effects */
  p:hover {
    color: rgba(219, 234, 254, 1);
    transition: color 0.2s ease;
  }

  /* Responsive adjustments */
  @media (max-width: 768px) {
    .flex.flex-col {
      text-align: center;
    }

    .space-x-2 {
      justify-content: center;
    }
  }

  /* Animation for icons */
  @keyframes pulse {
    0%,
    100% {
      opacity: 1;
    }
    50% {
      opacity: 0.7;
    }
  }

  .fas {
    animation: pulse 3s ease-in-out infinite;
  }
</style>
