/**
 * Pinia Sidebar Store
 * Manages persistent sidebar state with localStorage integration
 */

import { defineStore } from 'pinia'
import { ref, computed, watch } from 'vue'

const SIDEBAR_STORAGE_KEY = 'sidebar_state'
const SIDEBAR_SECTIONS_KEY = 'sidebar_sections'

// Helper function to safely parse JSON from localStorage
const safeParseJSON = (value, fallback) => {
  try {
    return value ? JSON.parse(value) : fallback
  } catch (error) {
    console.warn('Failed to parse JSON from localStorage:', error)
    return fallback
  }
}

// Helper function to safely stringify and store in localStorage
const safeSetStorage = (key, value) => {
  try {
    localStorage.setItem(key, JSON.stringify(value))
    return true
  } catch (error) {
    console.error('Failed to save to localStorage:', error)
    return false
  }
}

export const useSidebarStore = defineStore('sidebar', () => {
  // State
  const isCollapsed = ref(false)
  const isInitialized = ref(false)
  const isMobile = ref(false)
  const expandedSections = ref({
    dashboard: true,
    userManagement: true,
    requestsManagement: true,
    deviceManagement: true,
    settings: true
  })

  // Getters (computed)
  const isExpanded = computed(() => !isCollapsed.value)

  const sidebarWidth = computed(() => {
    if (isMobile.value) {
      return isCollapsed.value ? '0px' : '280px'
    }
    return isCollapsed.value ? '64px' : '288px'
  })

  const sidebarClasses = computed(() => ({
    'w-16': isCollapsed.value && !isMobile.value,
    'w-72': !isCollapsed.value && !isMobile.value,
    'w-0': isCollapsed.value && isMobile.value,
    'w-70': !isCollapsed.value && isMobile.value,
    'transition-all': true,
    'duration-300': true,
    'ease-in-out': true
  }))

  const mainContentClasses = computed(() => ({
    'ml-16': isCollapsed.value && !isMobile.value,
    'ml-72': !isCollapsed.value && !isMobile.value,
    'ml-0': isMobile.value,
    'transition-all': true,
    'duration-300': true,
    'ease-in-out': true
  }))

  // Actions
  const initializeSidebar = () => {
    if (isInitialized.value) return

    console.log('🔄 Initializing sidebar state from localStorage...')

    // Restore sidebar collapsed state
    const storedState = localStorage.getItem(SIDEBAR_STORAGE_KEY)
    if (storedState !== null) {
      const parsedState = safeParseJSON(storedState, { isCollapsed: false })
      isCollapsed.value = parsedState.isCollapsed || false
      console.log('✅ Restored sidebar collapsed state:', isCollapsed.value)
    }

    // Restore expanded sections
    const storedSections = localStorage.getItem(SIDEBAR_SECTIONS_KEY)
    if (storedSections !== null) {
      const parsedSections = safeParseJSON(storedSections, expandedSections.value)
      expandedSections.value = { ...expandedSections.value, ...parsedSections }
      console.log('✅ Restored sidebar sections:', expandedSections.value)
    }

    // Detect mobile
    detectMobile()

    // Set up watchers for persistence
    setupPersistenceWatchers()

    isInitialized.value = true
    console.log('✅ Sidebar initialization completed')
  }

  const detectMobile = () => {
    const checkMobile = () => {
      const wasMobile = isMobile.value
      isMobile.value = window.innerWidth < 768

      // Auto-collapse on mobile if not already collapsed
      if (isMobile.value && !isCollapsed.value && !wasMobile) {
        console.log('📱 Mobile detected, auto-collapsing sidebar')
        setCollapsed(true)
      }
    }

    checkMobile()
    window.addEventListener('resize', checkMobile)

    // Return cleanup function
    return () => window.removeEventListener('resize', checkMobile)
  }

  const setupPersistenceWatchers = () => {
    // Watch sidebar collapsed state
    watch(
      () => isCollapsed.value,
      (newValue) => {
        const success = safeSetStorage(SIDEBAR_STORAGE_KEY, { isCollapsed: newValue })
        if (success) {
          console.log('💾 Sidebar collapsed state saved:', newValue)
        }
      },
      { immediate: false }
    )

    // Watch expanded sections
    watch(
      () => expandedSections.value,
      (newValue) => {
        const success = safeSetStorage(SIDEBAR_SECTIONS_KEY, newValue)
        if (success) {
          console.log('💾 Sidebar sections saved:', newValue)
        }
      },
      { deep: true, immediate: false }
    )
  }

  const setCollapsed = (collapsed) => {
    console.log('🔄 Setting sidebar collapsed:', collapsed)
    isCollapsed.value = collapsed
  }

  const toggle = () => {
    const newState = !isCollapsed.value
    console.log('🔄 Toggling sidebar:', isCollapsed.value, '->', newState)
    isCollapsed.value = newState
  }

  const expand = () => {
    console.log('🔄 Expanding sidebar')
    isCollapsed.value = false
  }

  const collapse = () => {
    console.log('🔄 Collapsing sidebar')
    isCollapsed.value = true
  }

  const toggleSection = (sectionName) => {
    if (Object.prototype.hasOwnProperty.call(expandedSections.value, sectionName)) {
      expandedSections.value[sectionName] = !expandedSections.value[sectionName]
      console.log(`🔄 Toggled section ${sectionName}:`, expandedSections.value[sectionName])
    } else {
      console.warn(`Section ${sectionName} not found in expandedSections`)
    }
  }

  const setSectionExpanded = (sectionName, expanded) => {
    if (Object.prototype.hasOwnProperty.call(expandedSections.value, sectionName)) {
      expandedSections.value[sectionName] = expanded
      console.log(`🔄 Set section ${sectionName} to:`, expanded)
    } else {
      console.warn(`Section ${sectionName} not found in expandedSections`)
    }
  }

  const resetToDefaults = () => {
    console.log('🔄 Resetting sidebar to defaults')
    isCollapsed.value = false
    expandedSections.value = {
      dashboard: true,
      userManagement: true,
      requestsManagement: true,
      deviceManagement: true,
      settings: true
    }
  }

  const clearStorage = () => {
    console.log('🗑️ Clearing sidebar localStorage')
    localStorage.removeItem(SIDEBAR_STORAGE_KEY)
    localStorage.removeItem(SIDEBAR_SECTIONS_KEY)
    resetToDefaults()
  }

  // Debug helpers
  const getDebugInfo = () => ({
    isCollapsed: isCollapsed.value,
    isExpanded: isExpanded.value,
    isInitialized: isInitialized.value,
    isMobile: isMobile.value,
    expandedSections: expandedSections.value,
    sidebarWidth: sidebarWidth.value,
    storageData: {
      sidebarState: localStorage.getItem(SIDEBAR_STORAGE_KEY),
      sidebarSections: localStorage.getItem(SIDEBAR_SECTIONS_KEY)
    }
  })

  return {
    // State
    isCollapsed,
    isInitialized,
    isMobile,
    expandedSections,

    // Getters
    isExpanded,
    sidebarWidth,
    sidebarClasses,
    mainContentClasses,

    // Actions
    initializeSidebar,
    detectMobile,
    setCollapsed,
    toggle,
    expand,
    collapse,
    toggleSection,
    setSectionExpanded,
    resetToDefaults,
    clearStorage,
    getDebugInfo
  }
})
