<template>
  <div id="app" class="min-h-screen bg-gray-50 overflow-x-hidden">
    <router-view />
  </div>
</template>

<script>
export default {
  name: 'App'
}
</script>

<style>
/* Prevent horizontal overflow globally */
html, body {
  overflow-x: hidden;
  max-width: 100vw;
  box-sizing: border-box;
}

*, *::before, *::after {
  box-sizing: border-box;
}

#app {
  font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  color: #2c3e50;
  width: 100%;
  max-width: 100vw;
  overflow-x: hidden;
}

/* Global responsive utilities */
.container-responsive {
  @apply w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8;
  max-width: 100%;
  box-sizing: border-box;
  overflow-x: hidden;
}

/* Smooth transitions for all interactive elements */
/* Limit bulk transitions to interactive elements only to avoid jank */
button, a, input, select, textarea, [role="button"], .transition, .transition-all {
  transition: all 0.2s ease-in-out;
}

/* Custom scrollbar */
::-webkit-scrollbar {
  width: 8px;
  height: 8px;
}

::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 4px;
}

::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

/* Mobile-first responsive text sizes */
.text-responsive-xs {
  @apply text-xs sm:text-sm;
}

.text-responsive-sm {
  @apply text-sm sm:text-base;
}

.text-responsive-base {
  @apply text-base sm:text-lg;
}

.text-responsive-lg {
  @apply text-lg sm:text-xl;
}

.text-responsive-xl {
  @apply text-xl sm:text-2xl;
}

.text-responsive-2xl {
  @apply text-2xl sm:text-3xl;
}

/* Responsive spacing utilities */
.spacing-responsive {
  @apply p-4 sm:p-6 lg:p-8;
}

.spacing-responsive-sm {
  @apply p-2 sm:p-4 lg:p-6;
}

/* Focus styles for accessibility */
.focus-visible {
  @apply focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2;
}

/* Print styles */
@media print {
  #app {
    background: white !important;
  }
  
  .no-print {
    display: none !important;
  }
}
</style>