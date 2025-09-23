<template>
  <div class="head-it-dashboard">
    <!-- Header Section -->
    <div
      class="bg-gradient-to-br from-blue-700 to-blue-900 text-white p-6 mb-8 rounded-lg shadow-lg"
    >
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold mb-2">Head of IT Dashboard</h1>
          <p class="text-blue-100 text-lg">
            Welcome, {{ userName }}! Manage IT access requests and approvals.
          </p>
        </div>
        <div class="text-right">
          <div class="text-sm text-blue-200">{{ currentDate }}</div>
          <div class="text-xs text-blue-300">{{ currentTime }}</div>
        </div>
      </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <!-- Pending Approvals -->
      <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-orange-500">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-orange-100 text-orange-600 mr-4">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
              />
            </svg>
          </div>
          <div>
            <p class="text-2xl font-bold text-gray-700">{{ pendingRequests }}</p>
            <p class="text-sm text-gray-500">Pending Requests</p>
          </div>
        </div>
      </div>

      <!-- This Month Approvals -->
      <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-green-500">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
              />
            </svg>
          </div>
          <div>
            <p class="text-2xl font-bold text-gray-700">{{ monthlyApprovals }}</p>
            <p class="text-sm text-gray-500">This Month</p>
          </div>
        </div>
      </div>

      <!-- Total Users -->
      <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-blue-500">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"
              />
            </svg>
          </div>
          <div>
            <p class="text-2xl font-bold text-gray-700">{{ totalUsers }}</p>
            <p class="text-sm text-gray-500">System Users</p>
          </div>
        </div>
      </div>

      <!-- System Status -->
      <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-purple-500">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
              />
            </svg>
          </div>
          <div>
            <p class="text-sm font-semibold text-green-600">ONLINE</p>
            <p class="text-sm text-gray-500">System Status</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Action Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
      <!-- Quick Actions -->
      <div class="bg-white rounded-lg shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
          <svg
            class="w-5 h-5 mr-2 text-blue-600"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M13 10V3L4 14h7v7l9-11h-7z"
            />
          </svg>
          Quick Actions
        </h3>
        <div class="space-y-3">
          <button
            @click="navigateTo('/jeeva-access')"
            class="w-full text-left p-3 rounded-lg border border-gray-200 hover:bg-blue-50 hover:border-blue-300 transition-colors duration-200 flex items-center"
          >
            <div class="p-2 bg-blue-100 rounded-lg mr-3">
              <svg
                class="w-4 h-4 text-blue-600"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                />
              </svg>
            </div>
            <div>
              <div class="font-medium text-gray-800">Jeeva Access Review</div>
              <div class="text-sm text-gray-500">Review Jeeva system access requests</div>
            </div>
          </button>

          <button
            @click="navigateTo('/wellsoft-access')"
            class="w-full text-left p-3 rounded-lg border border-gray-200 hover:bg-green-50 hover:border-green-300 transition-colors duration-200 flex items-center"
          >
            <div class="p-2 bg-green-100 rounded-lg mr-3">
              <svg
                class="w-4 h-4 text-green-600"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                />
              </svg>
            </div>
            <div>
              <div class="font-medium text-gray-800">WellSoft Access Review</div>
              <div class="text-sm text-gray-500">Review WellSoft system access requests</div>
            </div>
          </button>

          <button
            @click="navigateTo('/internet-access')"
            class="w-full text-left p-3 rounded-lg border border-gray-200 hover:bg-purple-50 hover:border-purple-300 transition-colors duration-200 flex items-center"
          >
            <div class="p-2 bg-purple-100 rounded-lg mr-3">
              <svg
                class="w-4 h-4 text-purple-600"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
            </div>
            <div>
              <div class="font-medium text-gray-800">Internet Access Review</div>
              <div class="text-sm text-gray-500">Review internet access requests</div>
            </div>
          </button>
        </div>
      </div>

      <!-- Recent Activity -->
      <div class="bg-white rounded-lg shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
          <svg
            class="w-5 h-5 mr-2 text-green-600"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
            />
          </svg>
          Recent Activity
        </h3>
        <div class="space-y-3 max-h-64 overflow-y-auto">
          <div v-if="recentActivity.length === 0" class="text-gray-500 text-center py-4">
            No recent activity
          </div>
          <div
            v-for="activity in recentActivity"
            :key="activity.id"
            class="flex items-center p-3 bg-gray-50 rounded-lg"
          >
            <div class="w-2 h-2 bg-blue-500 rounded-full mr-3"></div>
            <div class="flex-1">
              <div class="text-sm font-medium text-gray-800">{{ activity.title }}</div>
              <div class="text-xs text-gray-500">{{ activity.time }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- System Information -->
    <div class="bg-white rounded-lg shadow-lg p-6">
      <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
        <svg
          class="w-5 h-5 mr-2 text-gray-600"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
          />
        </svg>
        System Information
      </h3>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="p-4 bg-gray-50 rounded-lg">
          <div class="text-sm text-gray-500">Version</div>
          <div class="font-semibold text-gray-800">v2.1.0</div>
        </div>
        <div class="p-4 bg-gray-50 rounded-lg">
          <div class="text-sm text-gray-500">Last Update</div>
          <div class="font-semibold text-gray-800">{{ lastUpdateDate }}</div>
        </div>
        <div class="p-4 bg-gray-50 rounded-lg">
          <div class="text-sm text-gray-500">Uptime</div>
          <div class="font-semibold text-gray-800">99.9%</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { ref, computed, onMounted, onUnmounted } from 'vue'
  import { useRouter } from 'vue-router'
  import { useAuthStore } from '../stores/auth'

  const router = useRouter()
  const authStore = useAuthStore()

  // Reactive data
  const currentTime = ref('')
  const pendingRequests = ref(0)
  const monthlyApprovals = ref(0)
  const totalUsers = ref(0)
  const recentActivity = ref([])

  // Computed properties
  const userName = computed(() => authStore.userName || 'User')
  const currentDate = computed(() => {
    return new Date().toLocaleDateString('en-US', {
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    })
  })

  const lastUpdateDate = computed(() => {
    return new Date().toLocaleDateString('en-US', {
      month: 'short',
      day: 'numeric',
      year: 'numeric'
    })
  })

  // Methods
  const navigateTo = (path) => {
    router.push(path)
  }

  const updateTime = () => {
    currentTime.value = new Date().toLocaleTimeString('en-US', {
      hour: '2-digit',
      minute: '2-digit',
      second: '2-digit'
    })
  }

  const loadDashboardData = async () => {
    try {
      // Simulate loading dashboard data
      // In a real implementation, these would be API calls
      pendingRequests.value = Math.floor(Math.random() * 10) + 1
      monthlyApprovals.value = Math.floor(Math.random() * 50) + 20
      totalUsers.value = Math.floor(Math.random() * 200) + 100

      // Simulate recent activity
      recentActivity.value = [
        {
          id: 1,
          title: 'New Jeeva access request submitted',
          time: '2 hours ago'
        },
        {
          id: 2,
          title: 'WellSoft access approved for staff member',
          time: '4 hours ago'
        },
        {
          id: 3,
          title: 'System maintenance completed',
          time: '1 day ago'
        }
      ]
    } catch (error) {
      console.error('Error loading dashboard data:', error)
    }
  }

  // Lifecycle hooks
  let timeInterval

  onMounted(() => {
    updateTime()
    timeInterval = setInterval(updateTime, 1000)
    loadDashboardData()
  })

  onUnmounted(() => {
    if (timeInterval) {
      clearInterval(timeInterval)
    }
  })
</script>

<style scoped>
  /* Custom scrollbar for recent activity */
  .overflow-y-auto::-webkit-scrollbar {
    width: 4px;
  }

  .overflow-y-auto::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 2px;
  }

  .overflow-y-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 2px;
  }

  .overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
  }
</style>
