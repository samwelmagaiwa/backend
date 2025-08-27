<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-900 via-blue-800 to-teal-900 text-white">
    <!-- Header -->
    <header class="bg-blue-800/50 p-4 border-b border-blue-600/40">
      <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold">User Dashboard</h1>
        <div class="flex items-center space-x-4">
          <span>{{ currentUser?.name || 'User' }}</span>
          <button @click="handleLogout" class="bg-red-600 hover:bg-red-700 px-3 py-1 rounded text-sm">
            Logout
          </button>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="p-6">
      <div class="max-w-7xl mx-auto">
        <!-- Welcome Section -->
        <div class="bg-white/10 p-8 rounded-xl mb-8">
          <div class="flex items-center mb-6">
            <div class="w-16 h-16 bg-blue-600 rounded-xl flex items-center justify-center mr-4">
              <i class="fas fa-home text-white text-2xl"></i>
            </div>
            <div>
              <h2 class="text-3xl font-bold mb-2">Welcome to Your Dashboard</h2>
              <p class="text-blue-100 text-lg">Manage your access requests and bookings</p>
            </div>
          </div>

          <!-- Quick Actions -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <button
              @click="viewMyRequests"
              class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-6 rounded-xl font-semibold transition-colors shadow-lg"
            >
              <div class="flex items-center justify-center">
                <i class="fas fa-list-alt text-white text-lg mr-3"></i>
                <span class="text-lg">View My Requests</span>
              </div>
            </button>

            <button
              @click="showFormSelector = true"
              class="bg-red-600 hover:bg-red-700 text-white px-8 py-6 rounded-xl font-semibold transition-colors shadow-lg"
            >
              <div class="flex items-center justify-center">
                <i class="fas fa-plus text-white text-lg mr-3"></i>
                <span class="text-lg">Submit New Request</span>
              </div>
            </button>
          </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <!-- Pending Card -->
          <div class="bg-white/10 p-8 rounded-xl">
            <div class="flex items-center justify-between mb-4">
              <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center">
                <i class="fas fa-clock text-white text-lg"></i>
              </div>
              <span class="bg-blue-500/30 px-3 py-1 rounded-full text-xs font-medium">Active</span>
            </div>
            <h3 class="text-2xl font-bold mb-3">Pending</h3>
            <p class="text-5xl font-bold text-blue-400">56</p>
          </div>

          <!-- Approved Card -->
          <div class="bg-white/10 p-8 rounded-xl">
            <div class="flex items-center justify-between mb-4">
              <div class="w-12 h-12 bg-green-600 rounded-xl flex items-center justify-center">
                <i class="fas fa-check-circle text-white text-lg"></i>
              </div>
              <span class="bg-green-500/30 px-3 py-1 rounded-full text-xs font-medium">Success</span>
            </div>
            <h3 class="text-2xl font-bold mb-3">Approved</h3>
            <p class="text-5xl font-bold text-green-400">1,234</p>
          </div>

          <!-- Rejected Card -->
          <div class="bg-white/10 p-8 rounded-xl">
            <div class="flex items-center justify-between mb-4">
              <div class="w-12 h-12 bg-red-600 rounded-xl flex items-center justify-center">
                <i class="fas fa-times-circle text-white text-lg"></i>
              </div>
              <span class="bg-red-500/30 px-3 py-1 rounded-full text-xs font-medium">Closed</span>
            </div>
            <h3 class="text-2xl font-bold mb-3">Rejected</h3>
            <p class="text-5xl font-bold text-red-400">89</p>
          </div>
        </div>
      </div>
    </main>

    <!-- Form Selector Modal -->
    <div
      v-if="showFormSelector"
      class="fixed inset-0 bg-black/60 flex items-center justify-center z-50"
    >
      <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4">
        <div class="bg-blue-600 p-6 text-center rounded-t-xl">
          <h3 class="text-xl font-bold text-white">Select Access Form</h3>
        </div>
        <div class="p-6 space-y-4">
          <button
            @click="selectCombinedForm"
            class="w-full bg-blue-600 hover:bg-blue-700 text-white p-4 rounded-lg font-medium transition-colors"
          >
            <i class="fas fa-layer-group mr-2"></i>
            Combined Access Form
          </button>
          <button
            @click="selectBookingService"
            class="w-full bg-red-600 hover:bg-red-700 text-white p-4 rounded-lg font-medium transition-colors"
          >
            <i class="fas fa-calendar-check mr-2"></i>
            Booking Service
          </button>
          <button
            @click="showFormSelector = false"
            class="w-full bg-gray-300 hover:bg-gray-400 text-gray-800 p-3 rounded-lg font-medium transition-colors"
          >
            Cancel
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'

export default {
  name: 'UserDashboardWorking',
  setup() {
    const router = useRouter()

    // Local state
    const showFormSelector = ref(false)
    const currentUser = ref(null)

    // Initialize with fallback data
    onMounted(() => {
      console.log('✅ UserDashboardWorking mounted successfully')

      try {
        const userData = localStorage.getItem('user_data')
        if (userData) {
          currentUser.value = JSON.parse(userData)
        } else {
          // Set default user if no data exists
          currentUser.value = {
            id: 1,
            name: 'Staff User',
            email: 'staff@example.com',
            role: 'staff'
          }
        }
      } catch (error) {
        console.error('Error loading user data:', error)
        // Fallback user
        currentUser.value = {
          id: 1,
          name: 'Staff User',
          email: 'staff@example.com',
          role: 'staff'
        }
      }
    })

    // Methods
    const viewMyRequests = () => {
      router.push('/request-status').catch(error => {
        console.error('Navigation error:', error)
      })
    }

    const selectCombinedForm = () => {
      showFormSelector.value = false
      router.push('/user-combined-form').catch(error => {
        console.error('Navigation error:', error)
      })
    }

    const selectBookingService = () => {
      showFormSelector.value = false
      router.push('/booking-service').catch(error => {
        console.error('Navigation error:', error)
      })
    }

    const handleLogout = async() => {
      try {
        localStorage.removeItem('auth_token')
        localStorage.removeItem('user_data')
        router.push('/login')
      } catch (error) {
        console.error('Logout error:', error)
        router.push('/login')
      }
    }

    return {
      showFormSelector,
      currentUser,
      viewMyRequests,
      selectCombinedForm,
      selectBookingService,
      handleLogout
    }
  }
}
</script>

<style scoped>
.transition-colors {
  transition: background-color 0.3s ease;
}
</style>
