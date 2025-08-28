/**
 * Sidebar State Management Module
 * Handles persistent sidebar collapse/expand state across routes and page refreshes
 */

const SIDEBAR_STORAGE_KEY = 'sidebar_collapsed'

// Get initial state from localStorage or default to false (expanded)
const getInitialState = () => {
  try {
    const stored = localStorage.getItem(SIDEBAR_STORAGE_KEY)
    if (stored !== null) {
      return JSON.parse(stored)
    }
  } catch (error) {
    console.warn('Failed to parse sidebar state from localStorage:', error)
  }
  return false // Default to expanded
}

const state = {
  isCollapsed: getInitialState(),
  isInitialized: false // Track if state has been initialized
}

const mutations = {
  SET_COLLAPSED(state, collapsed) {
    state.isCollapsed = collapsed
    // Persist to localStorage
    try {
      localStorage.setItem(SIDEBAR_STORAGE_KEY, JSON.stringify(collapsed))
      console.log('💾 Sidebar state saved to localStorage:', collapsed)
    } catch (error) {
      console.error('Failed to save sidebar state to localStorage:', error)
    }
  },

  SET_INITIALIZED(state, initialized) {
    state.isInitialized = initialized
  },

  TOGGLE_COLLAPSED(state) {
    const newState = !state.isCollapsed
    state.isCollapsed = newState
    // Persist to localStorage
    try {
      localStorage.setItem(SIDEBAR_STORAGE_KEY, JSON.stringify(newState))
      console.log('💾 Sidebar state toggled and saved:', newState)
    } catch (error) {
      console.error('Failed to save sidebar state to localStorage:', error)
    }
  },

  RESTORE_FROM_STORAGE(state) {
    const stored = getInitialState()
    state.isCollapsed = stored
    state.isInitialized = true
    console.log('🔄 Sidebar state restored from localStorage:', stored)
  }
}

const actions = {
  // Initialize sidebar state (called on app startup)
  initializeSidebar({ commit, state }) {
    if (!state.isInitialized) {
      commit('RESTORE_FROM_STORAGE')
      console.log('✅ Sidebar state initialized')
    }
  },

  // Set sidebar collapsed state
  setSidebarCollapsed({ commit }, collapsed) {
    commit('SET_COLLAPSED', collapsed)
  },

  // Toggle sidebar state
  toggleSidebar({ commit }) {
    commit('TOGGLE_COLLAPSED')
  },

  // Force refresh from localStorage (useful for debugging)
  refreshSidebarFromStorage({ commit }) {
    commit('RESTORE_FROM_STORAGE')
  }
}

const getters = {
  isCollapsed: (state) => state.isCollapsed,
  isExpanded: (state) => !state.isCollapsed,
  isInitialized: (state) => state.isInitialized,

  // Computed classes for components
  sidebarClasses: (state) => ({
    'w-16': state.isCollapsed,
    'w-72': !state.isCollapsed
  }),

  // Main content margin classes
  mainContentClasses: (state) => ({
    'ml-16': state.isCollapsed,
    'ml-72': !state.isCollapsed
  })
}

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters
}
