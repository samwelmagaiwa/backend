/**
 * Pinia Store Setup
 * Central configuration for all Pinia stores
 */

import { createPinia } from 'pinia'

// Create the pinia instance
const pinia = createPinia()

// Optional: Add plugins or devtools
if (process.env.NODE_ENV === 'development') {
  // Enable devtools in development
  pinia.use(({ store }) => {
    store.$subscribe((mutation, _state) => {
      console.log(`🏪 Store "${store.$id}" mutation:`, mutation.type, mutation.payload)
    })
  })
}

export default pinia

// Export individual stores for convenience
export { useSidebarStore } from './sidebar'
export { useAuthStore } from './auth'
