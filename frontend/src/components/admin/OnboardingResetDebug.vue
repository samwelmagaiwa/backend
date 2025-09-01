<template>
  <div style="padding: 20px; background: #f0f9ff; min-height: 100vh; font-family: Arial, sans-serif;">
    <div style="max-width: 800px; margin: 0 auto;">
      <h1 style="color: #1e40af; margin-bottom: 20px; text-align: center;">
        🔍 Onboarding Reset - Debug Mode
      </h1>
      
      <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px;">
        <h2 style="color: #059669; margin: 0 0 15px 0;">✅ Component Status</h2>
        <p><strong>Status:</strong> Component loaded successfully!</p>
        <p><strong>Time:</strong> {{ currentTime }}</p>
        <p><strong>Route:</strong> /admin/onboarding-reset</p>
        <p><strong>Vue Version:</strong> 3.x</p>
      </div>

      <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px;">
        <h2 style="color: #7c3aed; margin: 0 0 15px 0;">🔧 Basic Tests</h2>
        <div style="margin-bottom: 10px;">
          <button 
            @click="testReactivity" 
            style="background: #3b82f6; color: white; padding: 8px 16px; border: none; border-radius: 4px; margin-right: 10px; cursor: pointer;"
          >
            Test Reactivity
          </button>
          <span v-if="reactivityTest">✅ Reactivity working!</span>
        </div>
        
        <div style="margin-bottom: 10px;">
          <button 
            @click="testNavigation" 
            style="background: #10b981; color: white; padding: 8px 16px; border: none; border-radius: 4px; margin-right: 10px; cursor: pointer;"
          >
            Test Navigation
          </button>
          <span v-if="navigationTest">✅ Navigation working!</span>
        </div>

        <div style="margin-bottom: 10px;">
          <button 
            @click="testAuth" 
            style="background: #f59e0b; color: white; padding: 8px 16px; border: none; border-radius: 4px; margin-right: 10px; cursor: pointer;"
          >
            Test Auth
          </button>
          <span v-if="authTest">{{ authResult }}</span>
        </div>
      </div>

      <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px;">
        <h2 style="color: #dc2626; margin: 0 0 15px 0;">🚨 Debug Information</h2>
        <p><strong>Current URL:</strong> {{ currentUrl }}</p>
        <p><strong>Local Storage Auth:</strong> {{ hasAuthToken ? '✅ Present' : '❌ Missing' }}</p>
        <p><strong>Local Storage User:</strong> {{ hasUserData ? '✅ Present' : '❌ Missing' }}</p>
        <p><strong>Console Errors:</strong> Check browser console (F12)</p>
      </div>

      <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px;">
        <h2 style="color: #0891b2; margin: 0 0 15px 0;">🧪 Component Tests</h2>
        <div style="margin-bottom: 10px;">
          <button 
            @click="testOriginalComponent" 
            style="background: #6366f1; color: white; padding: 8px 16px; border: none; border-radius: 4px; margin-right: 10px; cursor: pointer;"
          >
            Load Original Component
          </button>
        </div>
        
        <div style="margin-bottom: 10px;">
          <button 
            @click="testWithoutAuth" 
            style="background: #8b5cf6; color: white; padding: 8px 16px; border: none; border-radius: 4px; margin-right: 10px; cursor: pointer;"
          >
            Test Without Auth Guards
          </button>
        </div>

        <div style="margin-bottom: 10px;">
          <button 
            @click="goToAdminDashboard" 
            style="background: #059669; color: white; padding: 8px 16px; border: none; border-radius: 4px; margin-right: 10px; cursor: pointer;"
          >
            Go to Admin Dashboard
          </button>
        </div>
      </div>

      <div v-if="testResults.length > 0" style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h2 style="color: #374151; margin: 0 0 15px 0;">📋 Test Results</h2>
        <div v-for="(result, index) in testResults" :key="index" style="margin-bottom: 10px; padding: 10px; background: #f9fafb; border-radius: 4px;">
          <strong>{{ result.test }}:</strong> {{ result.result }}
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, computed } from 'vue'

export default {
  name: 'OnboardingResetDebug',
  setup() {
    const currentTime = ref(new Date().toLocaleString())
    const reactivityTest = ref(false)
    const navigationTest = ref(false)
    const authTest = ref(false)
    const authResult = ref('')
    const testResults = ref([])

    const currentUrl = computed(() => window.location.href)
    const hasAuthToken = computed(() => !!localStorage.getItem('auth_token'))
    const hasUserData = computed(() => !!localStorage.getItem('user_data'))

    const addTestResult = (test, result) => {
      testResults.value.push({
        test,
        result,
        timestamp: new Date().toLocaleTimeString()
      })
    }

    const testReactivity = () => {
      reactivityTest.value = true
      addTestResult('Reactivity Test', '✅ Vue reactivity is working')
      console.log('✅ Reactivity test passed')
    }

    const testNavigation = () => {
      navigationTest.value = true
      addTestResult('Navigation Test', '✅ Vue Router is accessible')
      console.log('✅ Navigation test passed')
    }

    const testAuth = async () => {
      authTest.value = true
      try {
        // Try to import useAuth
        const { useAuth } = await import('@/composables/useAuth')
        const auth = useAuth()
        
        authResult.value = `✅ Auth loaded - Authenticated: ${auth.isAuthenticated?.value || 'Unknown'}`
        addTestResult('Auth Test', authResult.value)
        console.log('✅ Auth test passed:', authResult.value)
      } catch (error) {
        authResult.value = `❌ Auth failed: ${error.message}`
        addTestResult('Auth Test', authResult.value)
        console.error('❌ Auth test failed:', error)
      }
    }

    const testOriginalComponent = () => {
      addTestResult('Original Component Test', '🔄 Attempting to load original component...')
      console.log('🔄 Attempting to load original OnboardingReset component')
      
      // Try to navigate to a route that loads the original component
      window.location.href = '/admin/onboarding-reset-original'
    }

    const testWithoutAuth = () => {
      addTestResult('No Auth Test', '🔄 Testing component without auth guards...')
      console.log('🔄 Testing without auth guards')
      
      // This would require creating a route without auth requirements
      alert('This would test the component without authentication requirements. Check console for details.')
    }

    const goToAdminDashboard = () => {
      addTestResult('Navigation', '🔄 Navigating to admin dashboard...')
      console.log('🔄 Navigating to admin dashboard')
      window.location.href = '/admin-dashboard'
    }

    // Update time every second
    const updateTime = () => {
      currentTime.value = new Date().toLocaleString()
    }

    onMounted(() => {
      console.log('🔍 OnboardingResetDebug component mounted')
      console.log('🔍 Current URL:', window.location.href)
      console.log('🔍 Auth token present:', hasAuthToken.value)
      console.log('🔍 User data present:', hasUserData.value)
      
      // Update time every second
      setInterval(updateTime, 1000)
      
      // Add initial test result
      addTestResult('Component Mount', '✅ Debug component mounted successfully')
    })

    return {
      currentTime,
      reactivityTest,
      navigationTest,
      authTest,
      authResult,
      testResults,
      currentUrl,
      hasAuthToken,
      hasUserData,
      testReactivity,
      testNavigation,
      testAuth,
      testOriginalComponent,
      testWithoutAuth,
      goToAdminDashboard
    }
  }
}
</script>
</template>