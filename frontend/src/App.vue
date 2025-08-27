<template>
  <div id="app">
    <!-- Main content -->
    <main>
      <router-view />
    </main>
  </div>
</template>

<script>
import { onMounted } from 'vue'
import auth from './utils/auth'

export default {
  name: 'App',

  setup() {
    onMounted(async() => {
      console.log('🔄 App: Mounted, initializing auth...')

      // Ensure auth is initialized
      await auth.initializeAuth()

      console.log('✅ App: Auth initialization complete')
      console.log('  - isAuthenticated:', auth.isAuthenticated)
      console.log('  - userRole:', auth.userRole)
      console.log('  - currentUser:', auth.currentUser?.name)
    })

    return {}
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
