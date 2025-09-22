/**
 * Vue plugin to make status utility globally available
 * This ensures all components can access consistent status handling
 */

import statusUtils from '@/utils/statusUtils'

export default {
  install(app) {
    // Make status utilities available globally in all components
    app.config.globalProperties.$statusUtils = statusUtils

    // Also provide the status utilities as composables for Vue 3 Composition API
    app.provide('statusUtils', statusUtils)

    // Add global properties for commonly used functions
    app.config.globalProperties.$getStatusText = (status, componentName) =>
      statusUtils.getStatusText(status, componentName)

    app.config.globalProperties.$getStatusBadgeClass = (status) =>
      statusUtils.getStatusBadgeClass(status)

    app.config.globalProperties.$getStatusIcon = (status) => statusUtils.getStatusIcon(status)

    // Log successful installation
    console.log('✅ [StatusUtils Plugin] Status utilities plugin installed successfully')
  }
}
