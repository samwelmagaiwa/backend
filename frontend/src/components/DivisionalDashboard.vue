<template>
  <div class="flex flex-col h-screen">
    <AppHeader />
    <div class="flex flex-1 overflow-hidden">
      <DynamicSidebar v-model:collapsed="sidebarCollapsed" />
      <main class="flex-1 p-6 bg-gradient-to-br from-blue-900 via-blue-800 to-teal-900 overflow-y-auto relative">
        <!-- Medical Background Pattern -->
        <div class="absolute inset-0 overflow-hidden">
          <!-- Medical Cross Pattern -->
          <div class="absolute inset-0 opacity-5">
            <div class="grid grid-cols-12 gap-8 h-full transform rotate-45">
              <div v-for="i in 48" :key="i" class="bg-white rounded-full w-2 h-2 animate-pulse" :style="{animationDelay: (i * 0.1) + 's'}"></div>
            </div>
          </div>
          <!-- Floating medical icons -->
          <div class="absolute inset-0">
            <div v-for="i in 15" :key="i" 
                 class="absolute text-white opacity-10 animate-float"
                 :style="{
                   left: Math.random() * 100 + '%',
                   top: Math.random() * 100 + '%',
                   animationDelay: Math.random() * 3 + 's',
                   animationDuration: (Math.random() * 3 + 2) + 's',
                   fontSize: (Math.random() * 20 + 10) + 'px'
                 }">
              <i :class="['fas', ['fa-heartbeat', 'fa-user-md', 'fa-hospital', 'fa-stethoscope', 'fa-plus'][Math.floor(Math.random() * 5)]]"></i>
            </div>
          </div>
        </div>
        
        <div class="max-w-full mx-auto relative z-10">
          <!-- Header Section -->
          <div class="medical-glass-card rounded-t-3xl p-4 mb-0 border-b border-blue-300/30">
            <div class="text-center">
              <h2 class="text-xl font-bold text-blue-100 tracking-wide drop-shadow-md animate-fade-in-delay">
                Welcome, {{ userName }}
              </h2>
            </div>
          </div>

          <!-- Main Content -->
          <div class="medical-glass-card rounded-b-3xl overflow-hidden">
            <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div class="medical-card bg-gradient-to-r from-blue-600/25 to-cyan-600/25 border-2 border-blue-400/40 p-6 rounded-2xl backdrop-blur-sm hover:shadow-2xl hover:shadow-blue-500/20 transition-all duration-500 group">
                <div class="flex items-center mb-4">
                  <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-blue-300/50 mr-4">
                    <i class="fas fa-file-alt text-white text-xl"></i>
                  </div>
                  <h3 class="text-lg font-semibold text-white drop-shadow-md">TOTAL FORMS</h3>
                </div>
                <p class="text-3xl font-bold text-blue-100 drop-shadow-lg">1,234</p>
              </div>
              
              <div class="medical-card bg-gradient-to-r from-green-600/25 to-emerald-600/25 border-2 border-green-400/40 p-6 rounded-2xl backdrop-blur-sm hover:shadow-2xl hover:shadow-green-500/20 transition-all duration-500 group">
                <div class="flex items-center mb-4">
                  <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-green-300/50 mr-4">
                    <i class="fas fa-check-circle text-white text-xl"></i>
                  </div>
                  <h3 class="text-lg font-semibold text-white drop-shadow-md">FORMS APPROVED</h3>
                </div>
                <p class="text-3xl font-bold text-green-100 drop-shadow-lg">56</p>
              </div>
              
              <div class="medical-card bg-gradient-to-r from-pink-600/25 to-rose-600/25 border-2 border-pink-400/40 p-6 rounded-2xl backdrop-blur-sm hover:shadow-2xl hover:shadow-pink-500/20 transition-all duration-500 group">
                <div class="flex items-center mb-4">
                  <div class="w-12 h-12 bg-gradient-to-br from-pink-500 to-rose-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-pink-300/50 mr-4">
                    <i class="fas fa-clock text-white text-xl"></i>
                  </div>
                  <h3 class="text-lg font-semibold text-white drop-shadow-md">PENDING REQUESTS</h3>
                </div>
                <p class="text-3xl font-bold text-pink-100 drop-shadow-lg">89</p>
              </div>
              
              <div class="medical-card bg-gradient-to-r from-purple-600/25 to-indigo-600/25 border-2 border-purple-400/40 p-6 rounded-2xl backdrop-blur-sm hover:shadow-2xl hover:shadow-purple-500/20 transition-all duration-500 group">
                <div class="flex items-center mb-4">
                  <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-purple-300/50 mr-4">
                    <i class="fas fa-database text-white text-xl"></i>
                  </div>
                  <h3 class="text-lg font-semibold text-white drop-shadow-md">JEEVA REQUESTS</h3>
                </div>
                <p class="text-3xl font-bold text-purple-100 drop-shadow-lg">70</p>
              </div>
              
              <div class="medical-card bg-gradient-to-r from-yellow-600/25 to-orange-600/25 border-2 border-yellow-400/40 p-6 rounded-2xl backdrop-blur-sm hover:shadow-2xl hover:shadow-yellow-500/20 transition-all duration-500 group">
                <div class="flex items-center mb-4">
                  <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-yellow-300/50 mr-4">
                    <i class="fas fa-laptop-medical text-white text-xl"></i>
                  </div>
                  <h3 class="text-lg font-semibold text-white drop-shadow-md">WELLSOFT REQUESTS</h3>
                </div>
                <p class="text-3xl font-bold text-yellow-100 drop-shadow-lg">89</p>
              </div>
              
              <div class="medical-card bg-gradient-to-r from-teal-600/25 to-cyan-600/25 border-2 border-teal-400/40 p-6 rounded-2xl backdrop-blur-sm hover:shadow-2xl hover:shadow-teal-500/20 transition-all duration-500 group">
                <div class="flex items-center mb-4">
                  <div class="w-12 h-12 bg-gradient-to-br from-teal-500 to-cyan-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-teal-300/50 mr-4">
                    <i class="fas fa-wifi text-white text-xl"></i>
                  </div>
                  <h3 class="text-lg font-semibold text-white drop-shadow-md">INTERNET ACCESS</h3>
                </div>
                <p class="text-3xl font-bold text-teal-100 drop-shadow-lg">900</p>
              </div>
            </div>
            </div> 
          </div>
        </div>
      </main>
    </div>
    <AppFooter />
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import AppHeader from './header.vue'
import DynamicSidebar from './DynamicSidebar.vue'
import AppFooter from './footer.vue'
import { useAuth } from '@/composables/useAuth'

export default {
  name: 'DivisionalDashboard',
  components: {
    AppHeader,
    DynamicSidebar,
    AppFooter
  },
  setup() {
    const { userName, ROLES, requireRole } = useAuth()
    
    // Local state
    const sidebarCollapsed = ref(false)
    
    // Guard this route - only Divisional Directors can access
    onMounted(() => {
      requireRole([ROLES.DIVISIONAL_DIRECTOR])
    })
    
    return {
      userName,
      sidebarCollapsed
    }
  }
}
</script>

<style scoped>
.text-primary {
  color: #1E40AF;
}

/* Medical Glass morphism effects */
.medical-glass-card {
  background: rgba(59, 130, 246, 0.15);
  backdrop-filter: blur(25px);
  -webkit-backdrop-filter: blur(25px);
  border: 2px solid rgba(96, 165, 250, 0.3);
  box-shadow: 0 8px 32px rgba(29, 78, 216, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.1);
}

.medical-card {
  position: relative;
  overflow: hidden;
  background: rgba(59, 130, 246, 0.1);
  backdrop-filter: blur(15px);
  -webkit-backdrop-filter: blur(15px);
}

.medical-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(96, 165, 250, 0.2), transparent);
  transition: left 0.5s;
}

.medical-card:hover::before {
  left: 100%;
}

/* Animations */
@keyframes float {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-20px); }
}

@keyframes fade-in {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes fade-in-delay {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

.animate-float {
  animation: float 6s ease-in-out infinite;
}

.animate-fade-in {
  animation: fade-in 1s ease-out;
}

.animate-fade-in-delay {
  animation: fade-in-delay 1s ease-out 0.3s both;
}

/* Hover effects for cards */
.hover-card {
  transition: all 0.3s ease;
}

.hover-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
}

/* Button hover effects */
button:hover {
  transform: translateY(-1px);
}
</style>
