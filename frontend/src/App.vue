<template>
  <div id="app">
    <!-- AppHeader removed -->

    <!-- Auth Debugger (temporary) -->
    <AuthDebugger v-if="showDebugger" />

    <!-- Main content -->
    <main>
      <router-view />
    </main>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import AuthDebugger from './components/AuthDebugger.vue'
import auth from './utils/auth'

export default {
  name: 'App',

  components: {
    AuthDebugger
  },

  setup() {
    const showDebugger = ref(false) // Set to false to hide debugger

    onMounted(async() => {
      console.log('🔄 App: Mounted, initializing auth...')

      // Ensure auth is initialized
      await auth.initializeAuth()

      console.log('✅ App: Auth initialization complete')
      console.log('  - isAuthenticated:', auth.isAuthenticated)
      console.log('  - userRole:', auth.userRole)
      console.log('  - currentUser:', auth.currentUser?.name)
    })

    return {
      showDebugger
    }
  }
}
</script>

<style>
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  color: #2c3e50;
  min-height: 100vh;
  background-color: #f5f5f5;
}

main {
  min-height: 100vh;
}

/* Global styles */
* {
  box-sizing: border-box;
}

body {
  margin: 0;
  padding: 0;
}
</style>
