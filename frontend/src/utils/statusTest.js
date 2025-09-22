/**
 * Status handling test utility
 * Use this to verify status handling and identify issues
 */

import statusUtils from './statusUtils'

// Test all available statuses
export function testAllStatuses() {
  console.log('🧪 [StatusTest] Testing all available statuses...')

  const allStatuses = statusUtils.getAllStatuses()
  console.log(`📊 Total statuses available: ${allStatuses.length}`)

  allStatuses.forEach((status) => {
    const text = statusUtils.getStatusText(status, 'StatusTest')
    const badge = statusUtils.getStatusBadgeClass(status)
    const icon = statusUtils.getStatusIcon(status)

    console.log(`✅ [StatusTest] ${status}:`, {
      text,
      badge,
      icon,
      isValid: statusUtils.isValidStatus(status)
    })
  })
}

// Test specific problematic statuses
export function testProblematicStatuses() {
  console.log('🔍 [StatusTest] Testing potentially problematic statuses...')

  const testStatuses = [
    'divisional_approved',
    'hod_approved',
    'ict_director_approved',
    'unknown_status',
    null,
    undefined,
    ''
  ]

  testStatuses.forEach((status) => {
    console.log(`🧪 [StatusTest] Testing status: "${status}"`)

    try {
      const text = statusUtils.getStatusText(status, 'StatusTest')
      const badge = statusUtils.getStatusBadgeClass(status)
      const icon = statusUtils.getStatusIcon(status)

      console.log(`✅ [StatusTest] Results for "${status}":`, {
        text,
        badge,
        icon,
        isValid: statusUtils.isValidStatus(status)
      })
    } catch (error) {
      console.error(`❌ [StatusTest] Error testing "${status}":`, error)
    }
  })
}

// Test component-specific status handling
export function testComponentStatusHandling(componentName, statuses) {
  console.log(`🧪 [StatusTest] Testing component "${componentName}" with statuses:`, statuses)

  statuses.forEach((status) => {
    const text = statusUtils.getStatusText(status, componentName)

    if (text.includes('Unknown Status')) {
      console.warn(
        `⚠️ [StatusTest] Component "${componentName}" shows unknown status for: "${status}"`
      )
    } else {
      console.log(`✅ [StatusTest] Component "${componentName}" status "${status}" -> "${text}"`)
    }
  })
}

// Run all tests
export function runAllStatusTests() {
  console.log('🚀 [StatusTest] Running comprehensive status tests...')

  testAllStatuses()
  testProblematicStatuses()

  // Test common component scenarios
  const commonStatuses = ['pending_hod', 'hod_approved', 'divisional_approved', 'implemented']
  testComponentStatusHandling('HodRequestList', commonStatuses)
  testComponentStatusHandling('DivisionalRequestList', commonStatuses)

  console.log('✅ [StatusTest] All status tests completed')
}

// Export individual functions for targeted testing
export default {
  testAllStatuses,
  testProblematicStatuses,
  testComponentStatusHandling,
  runAllStatusTests
}
