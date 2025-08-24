<template>
  <div
    v-if="redirectParam"
    class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg"
  >
    <div class="flex items-center justify-between">
      <div class="flex-1">
        <div class="flex items-center text-blue-700 font-semibold mb-1">
          <i class="fas fa-info-circle mr-2"></i>
          Redirect Notice
        </div>
        <div class="text-sm text-blue-600 mb-1">
          You were redirected here because you tried to access 
          <code class="bg-blue-100 px-1 rounded">{{ redirectParam }}</code>
          without being logged in.
        </div>
        <div class="text-xs text-blue-500">
          After successful login, you'll be redirected to your intended destination.
        </div>
      </div>
      <button
        @click="clearRedirect"
        class="ml-4 text-blue-400 hover:text-blue-600 transition-colors"
        title="Clear redirect notice"
      >
        <i class="fas fa-times"></i>
      </button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'RedirectExplanation',
  
  computed: {
    redirectParam() {
      return this.$route.query.redirect
    }
  },
  
  methods: {
    clearRedirect() {
      // Remove the redirect parameter from the URL
      this.$router.replace({ 
        path: this.$route.path,
        query: { ...this.$route.query, redirect: undefined }
      })
    }
  }
}
</script>

<style scoped>
code {
  background-color: #e3f2fd;
  padding: 2px 4px;
  border-radius: 4px;
  font-family: 'Courier New', monospace;
  font-size: 0.875em;
}
</style>