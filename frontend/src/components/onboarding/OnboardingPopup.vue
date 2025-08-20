<template>
  <div class="fixed inset-0 bg-black bg-opacity-20 flex items-center justify-end z-50 p-4 backdrop-blur-sm">
    <!-- Background Image -->
    <div 
      class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-800"
      style="background-image: url('/assets/images/image1.jpg')"
    ></div>
    <div class="ultra-glass rounded-3xl max-w-md w-full transform transition-all duration-500 scale-100 animate-slideInRight relative z-10 border border-white border-opacity-30 shadow-2xl mr-8">
     
      <!-- Body -->
      <div class="transparent-body p-8 text-center relative">
        <!-- Body Background Pattern -->
        <div class="absolute inset-0 bg-gradient-to-t from-white from-opacity-2 to-transparent rounded-b-3xl"></div>
        
        <div class="relative z-10">
          <p class="text-white text-2xl leading-relaxed mb-8 drop-shadow-glow font-semibold tracking-wide text-shadow-strong">{{ message }}</p>
        
          <div class="space-y-4">
            <button
              @click="handleClick"
              class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 bg-opacity-90 text-white py-5 px-8 rounded-2xl font-bold text-lg hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-4 focus:ring-blue-300 focus:ring-offset-2 transition-all duration-300 transform hover:scale-105 shadow-xl hover:shadow-2xl backdrop-blur-md border border-white border-opacity-20 glow-effect"
            >
              <i class="fas fa-arrow-right mr-3"></i>
              {{ buttonText }}
            </button>
            
            <button
              v-if="showReturnToLogin"
              @click="handleReturnToLogin"
              class="w-full bg-gradient-to-r from-gray-600 to-gray-700 bg-opacity-80 text-white py-4 px-8 rounded-2xl font-semibold text-base hover:from-gray-700 hover:to-gray-800 focus:outline-none focus:ring-4 focus:ring-gray-300 focus:ring-offset-2 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl backdrop-blur-md border border-white border-opacity-10"
            >
              <i class="fas fa-arrow-left mr-3"></i>
              Return to Login
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'OnboardingPopup',
  props: {
    title: {
      type: String,
      required: true
    },
    message: {
      type: String,
      required: true
    },
    buttonText: {
      type: String,
      default: 'Continue'
    },
    icon: {
      type: String,
      default: 'fas fa-info-circle'
    },
    showReturnToLogin: {
      type: Boolean,
      default: false
    }
  },
  emits: ['click', 'return-to-login'],
  methods: {
    handleClick() {
      this.$emit('click')
    },
    handleReturnToLogin() {
      console.log('OnboardingPopup: Return to login clicked')
      this.$emit('return-to-login')
    }
  }
}
</script>

<style scoped>
/* Ultra Glass Morphism Effect */
.ultra-glass {
  background: rgba(255, 255, 255, 0.05);
  backdrop-filter: blur(25px);
  -webkit-backdrop-filter: blur(25px);
  border: 1px solid rgba(255, 255, 255, 0.3);
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1), inset 0 1px 0 rgba(255, 255, 255, 0.2);
}

/* Transparent Sections */
.transparent-header {
  background: rgba(255, 255, 255, 0.02);
  backdrop-filter: blur(15px);
}

.transparent-body {
  background: rgba(255, 255, 255, 0.01);
  backdrop-filter: blur(10px);
}

/* Animations */
@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(30px) scale(0.95);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

@keyframes slideInRight {
  from {
    opacity: 0;
    transform: translateX(100px) scale(0.95);
  }
  to {
    opacity: 1;
    transform: translateX(0) scale(1);
  }
}

@keyframes float {
  0%, 100% {
    transform: translateY(0px);
  }
  50% {
    transform: translateY(-10px);
  }
}

@keyframes pulse {
  0%, 100% {
    opacity: 0.4;
    transform: scale(1);
  }
  50% {
    opacity: 0.8;
    transform: scale(1.1);
  }
}

@keyframes glow {
  0%, 100% {
    box-shadow: 0 0 20px rgba(59, 130, 246, 0.3);
  }
  50% {
    box-shadow: 0 0 30px rgba(59, 130, 246, 0.6), 0 0 40px rgba(99, 102, 241, 0.4);
  }
}

/* Animation Classes */
.animate-slideUp {
  animation: slideUp 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.animate-slideInRight {
  animation: slideInRight 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.animate-float {
  animation: float 3s ease-in-out infinite;
}

.animate-pulse {
  animation: pulse 2s ease-in-out infinite;
}

.glow-effect {
  animation: glow 2s ease-in-out infinite;
}

/* Backdrop Effects */
.backdrop-blur-sm {
  backdrop-filter: blur(4px);
}

.backdrop-blur-md {
  backdrop-filter: blur(12px);
}

/* Opacity Classes */
.from-opacity-10 {
  --tw-gradient-from-opacity: 0.1;
}

.to-opacity-5 {
  --tw-gradient-to-opacity: 0.05;
}

.from-opacity-5 {
  --tw-gradient-from-opacity: 0.05;
}

.from-opacity-3 {
  --tw-gradient-from-opacity: 0.03;
}

.to-opacity-2 {
  --tw-gradient-to-opacity: 0.02;
}

.from-opacity-2 {
  --tw-gradient-from-opacity: 0.02;
}

/* Enhanced Hover Effects */
button:hover {
  transform: translateY(-2px) scale(1.05);
}

button:active {
  transform: translateY(0) scale(1.02);
}

/* Responsive Design */
@media (max-width: 640px) {
  .glass-morphism {
    margin: 1rem;
    max-width: calc(100% - 2rem);
  }
}

/* Enhanced Text Shadows */
.drop-shadow-2xl {
  filter: drop-shadow(0 25px 25px rgba(0, 0, 0, 0.5)) drop-shadow(0 0 10px rgba(255, 255, 255, 0.1));
}

.drop-shadow-glow {
  filter: drop-shadow(0 0 20px rgba(0, 0, 0, 0.8)) drop-shadow(0 0 40px rgba(0, 0, 0, 0.6)) drop-shadow(0 0 10px rgba(255, 255, 255, 0.3));
}

.text-shadow-strong {
  text-shadow: 
    0 0 10px rgba(0, 0, 0, 0.9),
    0 0 20px rgba(0, 0, 0, 0.7),
    0 0 30px rgba(0, 0, 0, 0.5),
    2px 2px 4px rgba(0, 0, 0, 0.8),
    0 0 5px rgba(255, 255, 255, 0.2);
}

/* Border Opacity Classes */
.border-opacity-20 {
  border-color: rgba(255, 255, 255, 0.2);
}

.border-opacity-30 {
  border-color: rgba(255, 255, 255, 0.3);
}

.border-opacity-10 {
  border-color: rgba(255, 255, 255, 0.1);
}
</style>"