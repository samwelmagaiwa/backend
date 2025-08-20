<template>
  <footer class="bg-gradient-to-r from-blue-900 via-blue-800 to-blue-700 border-t border-blue-600/30" style="backdrop-filter: blur(20px);">
    <div class="container-responsive py-2">
      <!-- Footer Content (Collapsible) -->
      <div 
        id="footer-content"
        class="footer-content"
        :class="{
          'footer-visible': isFooterVisible,
          'footer-hidden': !isFooterVisible
        }"
      >
        <!-- Combined Footer Information -->
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 pb-3">
          <!-- Left Section: Copyright & System Status -->
          <div class="flex flex-col sm:flex-row sm:items-center gap-4">
            <!-- Copyright & Branding -->
            <div class="text-center sm:text-left">
              <p class="text-sm font-medium text-white drop-shadow-sm">
                &copy; {{ currentYear }} 
                <span class="text-blue-300">Muhimbili National Hospital</span>
              </p>
              <p class="text-xs text-blue-200 mt-0.5">
                ICT Department - Access Management System
              </p>
            </div>
            
            <!-- Status Indicators -->
            <div class="flex flex-wrap justify-center sm:justify-start items-center gap-3 text-xs text-blue-200">
              <div class="flex items-center">
                <div class="w-2 h-2 bg-green-400 rounded-full mr-1.5 animate-pulse shadow-lg"></div>
                <span class="drop-shadow-sm">System Online</span>
              </div>
              <div class="flex items-center">
                <i class="fas fa-users mr-1.5 text-blue-300 drop-shadow-sm"></i>
                <span class="drop-shadow-sm">{{ activeUsers }} users</span>
              </div>
              <div class="flex items-center">
                <i class="fas fa-clock mr-1.5 text-blue-300 drop-shadow-sm"></i>
                <span class="drop-shadow-sm">Backup: {{ lastBackup }}</span>
              </div>
            </div>
          </div>

          <!-- Right Section: Links & Version Info -->
          <div class="flex flex-col sm:flex-row sm:items-center gap-4">
            <!-- Quick Links -->
            <div class="flex flex-wrap justify-center sm:justify-end gap-3 text-xs">
              <router-link 
                to="/ict-policy"
                class="text-blue-200 hover:text-blue-300 transition-colors duration-200 hover:underline drop-shadow-sm"
              >
                Privacy Policy
              </router-link>
              <router-link 
                to="/terms-of-service" 
                class="text-blue-200 hover:text-blue-300 transition-colors duration-200 hover:underline drop-shadow-sm"
              >
                Terms of Service
              </router-link>
              <a 
                href="#" 
                class="text-blue-200 hover:text-blue-300 transition-colors duration-200 hover:underline drop-shadow-sm"
                @click.prevent
              >
                Support
              </a>
            </div>
            
            <!-- Version Info -->
            <div class="text-center sm:text-right text-xs text-blue-300">
              <span class="inline-flex items-center px-2 py-1 rounded-full bg-blue-700/50 text-blue-100 backdrop-blur-sm border border-blue-500/30 shadow-lg">
                <i class="fas fa-code-branch mr-1 drop-shadow-sm"></i>
                v2.1.0
              </span>
              <span class="ml-2 hidden sm:inline drop-shadow-sm">Updated: {{ lastUpdated }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer Toggle Button (Right Side) -->
      <div class="flex justify-end">
        <button 
          @click="toggleFooter"
          class="flex items-center gap-1 px-3 py-1 text-xs text-blue-200 hover:text-blue-300 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 backdrop-blur-sm drop-shadow-sm"
          :aria-expanded="isFooterVisible"
          aria-controls="footer-content"
        >
          <span>{{ isFooterVisible ? 'Hide Details' : 'Show Details' }}</span>
          <i 
            :class="[
              'fas transition-transform duration-200 text-xs drop-shadow-sm',
              isFooterVisible ? 'fa-chevron-up' : 'fa-chevron-down'
            ]"
          ></i>
        </button>
      </div>
    </div>
  </footer>
</template>

<script>
export default {
  name: 'AppFooter',
  data() {
    return {
      currentYear: new Date().getFullYear(),
      activeUsers: 1247,
      lastUpdated: this.formatCurrentDate(),
      lastBackup: '2 hours ago',
      isFooterVisible: false // Footer is hidden by default
    }
  },
  mounted() {
    // Update active users count periodically (optional)
    this.updateActiveUsers()
  },
  methods: {
    formatCurrentDate() {
      const now = new Date()
      const options = { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric' 
      }
      return now.toLocaleDateString('en-US', options)
    },
    updateActiveUsers() {
      // Simulate real-time user count updates
      setInterval(() => {
        // Add some realistic variation (±5 users)
        const variation = Math.floor(Math.random() * 11) - 5
        this.activeUsers = Math.max(1200, Math.min(1300, 1247 + variation))
      }, 30000) // Update every 30 seconds
    },
    toggleFooter() {
      this.isFooterVisible = !this.isFooterVisible
    }
  }
}
</script>

<style scoped>
/* Ensure consistent container responsive behavior */
.container-responsive {
  @apply max-w-7xl mx-auto px-4 sm:px-6 lg:px-8;
}

/* Footer content animations */
.footer-content {
  overflow: hidden;
  transition: all 0.3s ease-in-out;
}

.footer-hidden {
  max-height: 0;
  opacity: 0;
  transform: translateY(-10px);
}

.footer-visible {
  max-height: 200px; /* Reduced height for cleaner look */
  opacity: 1;
  transform: translateY(0);
}

/* Smooth animations */
.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}

/* Hover effects */
a:hover {
  transform: translateY(-1px);
}

/* Focus styles for accessibility */
a:focus {
  outline: 2px solid #4f46e5;
  outline-offset: 2px;
  border-radius: 2px;
}

/* Button hover effects */
button:hover {
  transform: translateY(-1px);
}

/* Optimize button spacing */
button {
  min-height: 28px; /* Ensure consistent button height */
}

/* Responsive adjustments */
@media (max-width: 640px) {
  .text-xs {
    font-size: 0.7rem;
  }
  
  .footer-visible {
    max-height: 300px; /* Slightly more space on mobile for stacked layout */
  }
  
  /* Stack elements vertically on mobile for better readability */
  .flex-col.sm\:flex-row {
    gap: 0.75rem;
  }
}

@media (max-width: 480px) {
  /* Extra small screens - further optimize spacing */
  .gap-3 {
    gap: 0.5rem;
  }
  
  .gap-4 {
    gap: 0.75rem;
  }
  
  .footer-visible {
    max-height: 350px;
  }
}
</style>