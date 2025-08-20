<template>
  <div class="session-manager">
    <!-- Session Manager Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 backdrop-blur-sm">
      <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full mx-4 transform transition-all duration-300 scale-100 animate-slideUp overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-6 text-center">
          <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4 backdrop-blur-sm border border-white/30">
            <i class="fas fa-desktop text-white text-2xl drop-shadow-lg"></i>
          </div>
          <h3 class="text-xl font-bold text-white mb-2 drop-shadow-md">Active Sessions</h3>
          <p class="text-blue-100 text-sm">Manage your active login sessions across different devices and browsers</p>
        </div>

        <!-- Body -->
        <div class="p-6 max-h-96 overflow-y-auto">
          <!-- Loading State -->
          <div v-if="loading" class="text-center py-8">
            <div class="inline-flex items-center space-x-2 text-gray-600">
              <i class="fas fa-spinner fa-spin text-xl"></i>
              <span>Loading sessions...</span>
            </div>
          </div>

          <!-- Error State -->
          <div v-else-if="error" class="text-center py-8">
            <div class="text-red-600 mb-4">
              <i class="fas fa-exclamation-triangle text-2xl mb-2"></i>
              <p>{{ error }}</p>
            </div>
            <button 
              @click="loadSessions"
              class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors"
            >
              <i class="fas fa-refresh mr-2"></i>
              Retry
            </button>
          </div>

          <!-- Sessions List -->
          <div v-else-if="sessions.length > 0" class="space-y-4">
            <div 
              v-for="session in sessions" 
              :key="session.id"
              class="border rounded-lg p-4 hover:bg-gray-50 transition-colors"
              :class="{ 'border-blue-500 bg-blue-50': session.is_current }"
            >
              <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                  <!-- Browser Icon -->
                  <div class="w-12 h-12 bg-gradient-to-br from-gray-100 to-gray-200 rounded-lg flex items-center justify-center">
                    <i :class="getBrowserIcon(session.browser)" class="text-gray-600 text-lg"></i>
                  </div>
                  
                  <!-- Session Info -->
                  <div>
                    <div class="flex items-center space-x-2">
                      <h4 class="font-semibold text-gray-900">{{ session.browser }} Browser</h4>
                      <span v-if="session.is_current" class="px-2 py-1 bg-blue-500 text-white text-xs rounded-full">
                        Current Session
                      </span>
                      <span class="px-2 py-1 bg-gray-200 text-gray-700 text-xs rounded-full">
                        {{ session.role }}
                      </span>
                    </div>
                    <div class="text-sm text-gray-600 mt-1">
                      <div class="flex items-center space-x-4">
                        <span>
                          <i class="fas fa-map-marker-alt mr-1"></i>
                          {{ session.ip_address }}
                        </span>
                        <span>
                          <i class="fas fa-clock mr-1"></i>
                          {{ formatDate(session.created_at) }}
                        </span>
                      </div>
                      <div v-if="session.last_used_at" class="mt-1">
                        <span class="text-xs text-gray-500">
                          Last used: {{ formatDate(session.last_used_at) }}
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
                
                <!-- Actions -->
                <div class="flex items-center space-x-2">
                  <button
                    v-if="!session.is_current"
                    @click="revokeSession(session.id)"
                    :disabled="revoking === session.id"
                    class="px-3 py-2 bg-red-500 text-white text-sm rounded-lg hover:bg-red-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    <i v-if="revoking === session.id" class="fas fa-spinner fa-spin mr-1"></i>
                    <i v-else class="fas fa-times mr-1"></i>
                    {{ revoking === session.id ? 'Revoking...' : 'Revoke' }}
                  </button>
                  <span v-else class="text-sm text-blue-600 font-medium">
                    <i class="fas fa-check-circle mr-1"></i>
                    This Session
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Empty State -->
          <div v-else class="text-center py-8">
            <div class="text-gray-500">
              <i class="fas fa-desktop text-3xl mb-4"></i>
              <p>No active sessions found</p>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="border-t bg-gray-50 p-6">
          <div class="flex justify-between items-center">
            <div class="text-sm text-gray-600">
              <i class="fas fa-info-circle mr-1"></i>
              Total Sessions: {{ sessions.length }}
            </div>
            <div class="flex space-x-3">
              <button
                @click="logoutAllSessions"
                :disabled="loading || sessions.length <= 1"
                class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <i class="fas fa-sign-out-alt mr-2"></i>
                Logout All Sessions
              </button>
              <button
                @click="closeModal"
                class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors"
              >
                Close
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Trigger Button -->
    <button
      @click="openModal"
      class="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors"
    >
      <i class="fas fa-desktop mr-2"></i>
      Manage Sessions
      <span v-if="sessionCount > 1" class="ml-2 px-2 py-1 bg-blue-400 text-white text-xs rounded-full">
        {{ sessionCount }}
      </span>
    </button>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import { useAuth } from '@/utils/auth'

export default {
  name: 'SessionManager',
  setup() {
    const { 
      getActiveSessions, 
      revokeSession: revokeSessionAPI, 
      logoutAll,
      activeSessions,
      isLoading 
    } = useAuth()
    
    const showModal = ref(false)
    const loading = ref(false)
    const error = ref(null)
    const revoking = ref(null)
    
    const sessions = computed(() => activeSessions.value || [])
    const sessionCount = computed(() => sessions.value.length)
    
    const openModal = async () => {
      showModal.value = true
      await loadSessions()
    }
    
    const closeModal = () => {
      showModal.value = false
      error.value = null
    }
    
    const loadSessions = async () => {
      try {
        loading.value = true
        error.value = null
        
        const result = await getActiveSessions()
        if (!result.success) {
          error.value = result.error || 'Failed to load sessions'
        }
      } catch (err) {
        error.value = err.message || 'Failed to load sessions'
      } finally {
        loading.value = false
      }
    }
    
    const revokeSession = async (sessionId) => {
      try {
        revoking.value = sessionId
        
        const result = await revokeSessionAPI(sessionId)
        if (!result.success) {
          error.value = result.error || 'Failed to revoke session'
        }
      } catch (err) {
        error.value = err.message || 'Failed to revoke session'
      } finally {
        revoking.value = null
      }
    }
    
    const logoutAllSessions = async () => {
      if (!confirm('Are you sure you want to logout from all sessions? This will close all your active sessions.')) {
        return
      }
      
      try {
        loading.value = true
        
        const result = await logoutAll()
        if (result.success) {
          // Close modal and redirect to login
          closeModal()
          window.location.href = '/login'
        } else {
          error.value = result.error || 'Failed to logout from all sessions'
        }
      } catch (err) {
        error.value = err.message || 'Failed to logout from all sessions'
      } finally {
        loading.value = false
      }
    }
    
    const getBrowserIcon = (browser) => {
      switch (browser?.toLowerCase()) {
        case 'chrome':
          return 'fab fa-chrome'
        case 'firefox':
          return 'fab fa-firefox-browser'
        case 'safari':
          return 'fab fa-safari'
        case 'edge':
          return 'fab fa-edge'
        default:
          return 'fas fa-globe'
      }
    }
    
    const formatDate = (dateString) => {
      if (!dateString) return 'Unknown'
      
      try {
        const date = new Date(dateString)
        const now = new Date()
        const diffMs = now - date
        const diffMins = Math.floor(diffMs / 60000)
        const diffHours = Math.floor(diffMins / 60)
        const diffDays = Math.floor(diffHours / 24)
        
        if (diffMins < 1) {
          return 'Just now'
        } else if (diffMins < 60) {
          return `${diffMins} minute${diffMins > 1 ? 's' : ''} ago`
        } else if (diffHours < 24) {
          return `${diffHours} hour${diffHours > 1 ? 's' : ''} ago`
        } else if (diffDays < 7) {
          return `${diffDays} day${diffDays > 1 ? 's' : ''} ago`
        } else {
          return date.toLocaleDateString()
        }
      } catch (err) {
        return 'Invalid date'
      }
    }
    
    return {
      showModal,
      loading,
      error,
      revoking,
      sessions,
      sessionCount,
      openModal,
      closeModal,
      loadSessions,
      revokeSession,
      logoutAllSessions,
      getBrowserIcon,
      formatDate
    }
  }
}
</script>

<style scoped>
@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(20px) scale(0.95);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

.animate-slideUp {
  animation: slideUp 0.3s ease-out;
}

.session-manager {
  display: inline-block;
}
</style>