<template>
  <div class="p-8 bg-gray-100 min-h-screen">
    <div class="max-w-4xl mx-auto">
      <h1 class="text-3xl font-bold mb-6 text-gray-800">HOD Dashboard Debug</h1>
      
      <!-- Auth State Debug -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4 text-blue-600">Authentication State</h2>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <strong>Is Authenticated:</strong> {{ isAuthenticated }}
          </div>
          <div>
            <strong>User Role:</strong> {{ userRole }}
          </div>
          <div>
            <strong>User Roles:</strong> {{ JSON.stringify(userRoles) }}
          </div>
          <div>
            <strong>Is Loading:</strong> {{ isLoading }}
          </div>
          <div>
            <strong>User Name:</strong> {{ user?.name }}
          </div>
          <div>
            <strong>User Email:</strong> {{ user?.email }}
          </div>
        </div>
        
        <div class="mt-4">
          <strong>Full User Object:</strong>
          <pre class="bg-gray-100 p-3 rounded mt-2 text-sm overflow-auto">{{ JSON.stringify(user, null, 2) }}</pre>
        </div>
      </div>

      <!-- API Test -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4 text-green-600">API Test</h2>
        <button 
          @click="testAPI" 
          :disabled="isTestingAPI"
          class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 disabled:opacity-50"
        >
          {{ isTestingAPI ? 'Testing...' : 'Test API Endpoint' }}
        </button>
        
        <div v-if="apiResult" class="mt-4">
          <strong>API Result:</strong>
          <pre class="bg-gray-100 p-3 rounded mt-2 text-sm overflow-auto">{{ JSON.stringify(apiResult, null, 2) }}</pre>
        </div>
        
        <div v-if="apiError" class="mt-4 text-red-600">
          <strong>API Error:</strong>
          <pre class="bg-red-100 p-3 rounded mt-2 text-sm overflow-auto">{{ apiError }}</pre>
        </div>
      </div>

      <!-- Component State Debug -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4 text-purple-600">Component State</h2>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <strong>Requests Count:</strong> {{ requests.length }}
          </div>
          <div>
            <strong>Is Component Loading:</strong> {{ isComponentLoading }}
          </div>
          <div>
            <strong>Component Error:</strong> {{ componentError }}
          </div>
          <div>
            <strong>Last Refresh:</strong> {{ lastRefreshTime }}
          </div>
        </div>
        
        <div v-if="requests.length > 0" class="mt-4">
          <strong>Sample Request:</strong>
          <pre class="bg-gray-100 p-3 rounded mt-2 text-sm overflow-auto">{{ JSON.stringify(requests[0], null, 2) }}</pre>
        </div>
      </div>

      <!-- Navigation Test -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold mb-4 text-orange-600">Navigation Test</h2>
        <div class="space-y-2">
          <button 
            @click="goToOriginalComponent" 
            class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 mr-2"
          >
            Go to Original HOD Dashboard
          </button>
          <button 
            @click="goToUserDashboard" 
            class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600 mr-2"
          >
            Go to User Dashboard
          </button>
          <button 
            @click="refreshPage" 
            class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600"
          >
            Refresh Page
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from '@/composables/useAuth'
import personalInfoService from '@/services/personalInfoService'

export default {
  name: 'HODDashboardDebug',
  setup() {
    const router = useRouter()
    const { user, userRole, userRoles, isAuthenticated, isLoading } = useAuth()
    
    // Component state
    const requests = ref([])
    const isComponentLoading = ref(false)
    const componentError = ref(null)
    const lastRefreshTime = ref(null)
    
    // API testing
    const isTestingAPI = ref(false)
    const apiResult = ref(null)
    const apiError = ref(null)
    
    const testAPI = async () => {
      isTestingAPI.value = true
      apiResult.value = null
      apiError.value = null
      
      try {
        console.log('🔍 Testing API endpoint...')
        const result = await personalInfoService.getUserAccessRequestsForHOD()
        console.log('✅ API test result:', result)
        apiResult.value = result
      } catch (error) {
        console.error('❌ API test error:', error)
        apiError.value = error.message || error.toString()
      } finally {
        isTestingAPI.value = false
      }
    }
    
    const goToOriginalComponent = () => {
      router.push('/hod-dashboard/request-list')
    }
    
    const goToUserDashboard = () => {
      router.push('/user-dashboard')
    }
    
    const refreshPage = () => {
      window.location.reload()
    }
    
    // Simulate the original component's data loading
    const loadRequests = async () => {
      isComponentLoading.value = true
      componentError.value = null
      
      try {
        const result = await personalInfoService.getUserAccessRequestsForHOD()
        
        if (result.success) {
          requests.value = result.data || []
          lastRefreshTime.value = new Date()
        } else {
          componentError.value = result.error
        }
      } catch (error) {
        componentError.value = error.message
      } finally {
        isComponentLoading.value = false
      }
    }
    
    onMounted(() => {
      console.log('🔍 HOD Dashboard Debug mounted')
      console.log('User:', user.value)
      console.log('User Role:', userRole.value)
      console.log('User Roles:', userRoles.value)
      console.log('Is Authenticated:', isAuthenticated.value)
      
      // Load requests on mount
      loadRequests()
    })
    
    return {
      // Auth state
      user,
      userRole,
      userRoles,
      isAuthenticated,
      isLoading,
      
      // Component state
      requests,
      isComponentLoading,
      componentError,
      lastRefreshTime,
      
      // API testing
      isTestingAPI,
      apiResult,
      apiError,
      testAPI,
      
      // Navigation
      goToOriginalComponent,
      goToUserDashboard,
      refreshPage
    }
  }
}
</script>