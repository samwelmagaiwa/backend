<template>
  <div class="flex flex-col h-screen">
    <AppHeader />
    <div class="flex flex-1 overflow-hidden">
      <ModernSidebar />
      <main class="flex-1 p-6 overflow-y-auto bg-gradient-to-br from-blue-50 to-indigo-100">
        <div class="max-w-4xl mx-auto">
          <h1 class="text-3xl font-bold text-gray-800 mb-8">Sidebar Persistence Demo (Pinia)</h1>

          <!-- Current State Display -->
          <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Current Sidebar State</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="space-y-2">
                <p><strong>Is Collapsed:</strong>
                  <span :class="isCollapsed ? 'text-red-600' : 'text-green-600'">
                    {{ isCollapsed ? 'Yes' : 'No' }}
                  </span>
                </p>
                <p><strong>Is Expanded:</strong>
                  <span :class="isExpanded ? 'text-green-600' : 'text-red-600'">
                    {{ isExpanded ? 'Yes' : 'No' }}
                  </span>
                </p>
                <p><strong>Is Initialized:</strong>
                  <span :class="isInitialized ? 'text-green-600' : 'text-orange-600'">
                    {{ isInitialized ? 'Yes' : 'No' }}
                  </span>
                </p>
                <p><strong>Is Mobile:</strong>
                  <span :class="isMobile ? 'text-orange-600' : 'text-blue-600'">
                    {{ isMobile ? 'Yes' : 'No' }}
                  </span>
                </p>
              </div>
              <div class="space-y-2">
                <p><strong>Sidebar Width:</strong>
                  <code class="bg-gray-100 px-2 py-1 rounded">{{ sidebarWidth }}</code>
                </p>
                <p><strong>Storage Value:</strong>
                  <code class="bg-gray-100 px-2 py-1 rounded">
                    {{ storageValue }}
                  </code>
                </p>
                <p><strong>Sections Storage:</strong>
                  <code class="bg-gray-100 px-2 py-1 rounded text-xs">
                    {{ sectionsStorageValue }}
                  </code>
                </p>
              </div>
            </div>
          </div>

          <!-- Expanded Sections Display -->
          <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Expanded Sections</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
              <div
                v-for="(expanded, section) in expandedSections"
                :key="section"
                class="flex items-center justify-between p-3 rounded border"
                :class="expanded ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200'"
              >
                <span class="font-medium capitalize">{{ section.replace(/([A-Z])/g, ' $1').trim() }}</span>
                <span :class="expanded ? 'text-green-600' : 'text-red-600'">
                  <i :class="expanded ? 'fas fa-check-circle' : 'fas fa-times-circle'"></i>
                </span>
              </div>
            </div>
          </div>

          <!-- Control Panel -->
          <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Sidebar Controls</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
              <button
                @click="toggleSidebar"
                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors"
              >
                Toggle Sidebar
              </button>
              <button
                @click="setCollapsed(true)"
                class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition-colors"
              >
                Collapse Sidebar
              </button>
              <button
                @click="setCollapsed(false)"
                class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition-colors"
              >
                Expand Sidebar
              </button>
              <button
                @click="refreshFromStorage"
                class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 transition-colors"
              >
                Refresh from Storage
              </button>
            </div>

            <h3 class="text-lg font-semibold text-gray-800 mb-3">Section Controls</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3 mb-6">
              <button
                v-for="section in Object.keys(expandedSections)"
                :key="section"
                @click="toggleSection(section)"
                class="px-3 py-2 text-sm rounded transition-colors"
                :class="expandedSections[section]
                  ? 'bg-green-600 text-white hover:bg-green-700'
                  : 'bg-gray-600 text-white hover:bg-gray-700'"
              >
                {{ section.replace(/([A-Z])/g, ' $1').trim() }}
              </button>
            </div>

            <div class="flex flex-wrap gap-3">
              <button
                @click="expandAllSections"
                class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition-colors"
              >
                Expand All Sections
              </button>
              <button
                @click="collapseAllSections"
                class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition-colors"
              >
                Collapse All Sections
              </button>
              <button
                @click="resetToDefaults"
                class="px-4 py-2 bg-orange-600 text-white rounded hover:bg-orange-700 transition-colors"
              >
                Reset to Defaults
              </button>
              <button
                @click="clearStorage"
                class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 transition-colors"
              >
                Clear Storage
              </button>
            </div>
          </div>

          <!-- Debug Information -->
          <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Debug Information</h2>
            <pre class="bg-gray-100 p-4 rounded text-sm overflow-x-auto">{{ debugInfo }}</pre>
          </div>

          <!-- Test Instructions -->
          <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Test Instructions</h2>
            <div class="space-y-4">
              <div class="border-l-4 border-blue-500 pl-4">
                <h3 class="font-semibold text-blue-800">Test 1: Route Navigation Persistence</h3>
                <p class="text-gray-600">
                  1. Toggle the sidebar to collapsed state<br>
                  2. Navigate to different pages using the sidebar menu<br>
                  3. Verify the sidebar stays collapsed across all routes
                </p>
              </div>

              <div class="border-l-4 border-green-500 pl-4">
                <h3 class="font-semibold text-green-800">Test 2: Page Refresh Persistence</h3>
                <p class="text-gray-600">
                  1. Set the sidebar to your preferred state (collapsed or expanded)<br>
                  2. Refresh the page (F5 or Ctrl+R)<br>
                  3. Verify the sidebar returns to the same state after refresh
                </p>
              </div>

              <div class="border-l-4 border-purple-500 pl-4">
                <h3 class="font-semibold text-purple-800">Test 3: Section State Persistence</h3>
                <p class="text-gray-600">
                  1. Expand/collapse different sections<br>
                  2. Navigate to other pages or refresh<br>
                  3. Verify section states are maintained
                </p>
              </div>

              <div class="border-l-4 border-orange-500 pl-4">
                <h3 class="font-semibold text-orange-800">Test 4: Mobile Responsiveness</h3>
                <p class="text-gray-600">
                  1. Resize browser window to mobile size<br>
                  2. Verify sidebar auto-collapses on mobile<br>
                  3. Test toggle functionality on mobile
                </p>
              </div>
            </div>
          </div>

          <!-- Navigation Links -->
          <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Test Navigation</h2>
            <p class="text-gray-600 mb-4">Use these links to test sidebar persistence across routes:</p>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
              <router-link
                to="/user-dashboard"
                class="px-4 py-2 bg-gray-100 text-gray-800 rounded hover:bg-gray-200 transition-colors text-center"
              >
                User Dashboard
              </router-link>
              <router-link
                to="/admin-dashboard"
                class="px-4 py-2 bg-gray-100 text-gray-800 rounded hover:bg-gray-200 transition-colors text-center"
              >
                Admin Dashboard
              </router-link>
              <router-link
                to="/user-combined-form"
                class="px-4 py-2 bg-gray-100 text-gray-800 rounded hover:bg-gray-200 transition-colors text-center"
              >
                Combined Form
              </router-link>
              <router-link
                to="/booking-service"
                class="px-4 py-2 bg-gray-100 text-gray-800 rounded hover:bg-gray-200 transition-colors text-center"
              >
                Booking Service
              </router-link>
              <router-link
                to="/request-status"
                class="px-4 py-2 bg-gray-100 text-gray-800 rounded hover:bg-gray-200 transition-colors text-center"
              >
                Request Status
              </router-link>
              <router-link
                to="/settings"
                class="px-4 py-2 bg-gray-100 text-gray-800 rounded hover:bg-gray-200 transition-colors text-center"
              >
                Settings
              </router-link>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script>
import { computed, ref, onMounted, onUnmounted } from 'vue'
import ModernSidebar from './ModernSidebar.vue'
import AppHeader from './AppHeader.vue'
import { useSidebar } from '@/composables/useSidebar'

export default {
  name: 'SidebarPersistenceDemo',
  components: {
    ModernSidebar,
    AppHeader
  },
  setup() {
    const {
      isCollapsed,
      isExpanded,
      isInitialized,
      isMobile,
      expandedSections,
      sidebarWidth,
      toggleSidebar,
      setCollapsed,
      toggleSection,
      expandAllSections,
      collapseAllSections,
      resetToDefaults,
      clearStorage,
      getDebugInfo
    } = useSidebar()

    // Local state for storage value display
    const storageValue = ref('')
    const sectionsStorageValue = ref('')

    // Update storage value display
    const updateStorageValues = () => {
      try {
        const stored = localStorage.getItem('sidebar_state')
        storageValue.value = stored || 'null'

        const sectionsStored = localStorage.getItem('sidebar_sections')
        sectionsStorageValue.value = sectionsStored || 'null'
      } catch (error) {
        storageValue.value = 'error'
        sectionsStorageValue.value = 'error'
      }
    }

    // Watch for storage changes
    const handleStorageChange = (event) => {
      if (event.key === 'sidebar_state' || event.key === 'sidebar_sections') {
        updateStorageValues()
      }
    }

    const refreshFromStorage = () => {
      // Force re-initialization
      const sidebarStore = useSidebar()
      sidebarStore.initializeSidebar()
      updateStorageValues()
    }

    const debugInfo = computed(() => {
      return getDebugInfo()
    })

    onMounted(() => {
      updateStorageValues()
      window.addEventListener('storage', handleStorageChange)

      // Update storage values when sidebar state changes
      const interval = setInterval(updateStorageValues, 1000)

      // Cleanup interval on unmount
      onUnmounted(() => {
        clearInterval(interval)
        window.removeEventListener('storage', handleStorageChange)
      })
    })

    return {
      // Sidebar state
      isCollapsed,
      isExpanded,
      isInitialized,
      isMobile,
      expandedSections,
      sidebarWidth,
      storageValue,
      sectionsStorageValue,
      debugInfo,

      // Sidebar actions
      toggleSidebar,
      setCollapsed,
      toggleSection,
      expandAllSections,
      collapseAllSections,
      resetToDefaults,
      clearStorage,
      refreshFromStorage
    }
  }
}
</script>

<style scoped>
/* Add any component-specific styles here */
</style>
