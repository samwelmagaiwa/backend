/**
 * Sidebar Composable
 * Provides a convenient interface to the sidebar store
 */

import { useSidebarStore } from '@/stores/sidebar'
import { storeToRefs } from 'pinia'

export function useSidebar() {
  const sidebarStore = useSidebarStore()

  // Convert store state to refs for reactivity
  const {
    isCollapsed,
    isExpanded,
    isInitialized,
    isMobile,
    expandedSections,
    sidebarWidth,
    sidebarClasses,
    mainContentClasses
  } = storeToRefs(sidebarStore)

  // Actions (these don't need storeToRefs)
  const {
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
  } = sidebarStore

  // Convenience methods
  const openSidebar = () => expand()
  const closeSidebar = () => collapse()
  const toggleSidebar = () => toggle()

  const isSectionExpanded = (sectionName) => {
    return expandedSections.value[sectionName] || false
  }

  const expandSection = (sectionName) => {
    setSectionExpanded(sectionName, true)
  }

  const collapseSection = (sectionName) => {
    setSectionExpanded(sectionName, false)
  }

  const expandAllSections = () => {
    Object.keys(expandedSections.value).forEach((section) => {
      setSectionExpanded(section, true)
    })
  }

  const collapseAllSections = () => {
    Object.keys(expandedSections.value).forEach((section) => {
      setSectionExpanded(section, false)
    })
  }

  // Initialize sidebar on first use
  if (!isInitialized.value) {
    initializeSidebar()
  }

  return {
    // State
    isCollapsed,
    isExpanded,
    isInitialized,
    isMobile,
    expandedSections,
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
    getDebugInfo,

    // Convenience methods
    openSidebar,
    closeSidebar,
    toggleSidebar,
    isSectionExpanded,
    expandSection,
    collapseSection,
    expandAllSections,
    collapseAllSections
  }
}
