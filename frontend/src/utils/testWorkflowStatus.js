/**
 * Test utility for the enhanced workflow status display
 * Use this to test the workflow progression logic
 */

// Mock the workflow status function (copied from HodRequestListSimplified)
function getWorkflowStatus(status) {
  const workflow = {
    pending: { current: 'Pending Submission', next: 'HOD' },
    pending_hod: { current: 'Pending HOD Approval', next: 'HOD' },
    hod_approved: { current: 'HOD Approved', next: 'Divisional', lastApproved: 'HOD' },
    pending_divisional: {
      current: 'Pending Divisional Approval',
      next: 'Divisional',
      lastApproved: 'HOD'
    },
    divisional_approved: {
      current: 'Divisional Approved',
      next: 'ICT Director',
      lastApproved: 'Divisional Director'
    },
    pending_ict_director: {
      current: 'Pending ICT Director Approval',
      next: 'ICT Director',
      lastApproved: 'Divisional Director'
    },
    ict_director_approved: {
      current: 'ICT Director Approved',
      next: 'Head IT',
      lastApproved: 'ICT Director'
    },
    pending_head_it: {
      current: 'Pending Head IT Approval',
      next: 'Head IT',
      lastApproved: 'ICT Director'
    },
    head_it_approved: { current: 'Head IT Approved', next: 'ICT Officer', lastApproved: 'Head IT' },
    pending_ict_officer: {
      current: 'Pending ICT Officer Approval',
      next: 'ICT Officer',
      lastApproved: 'Head IT'
    },
    ict_officer_approved: {
      current: 'ICT Officer Approved',
      next: 'Implementation',
      lastApproved: 'ICT Officer'
    },
    approved: { current: 'Fully Approved', next: 'Implementation', lastApproved: 'ICT Officer' },
    implemented: { current: 'Implemented', next: 'Complete', lastApproved: 'ICT Officer' },
    completed: { current: 'Completed', next: null, lastApproved: 'System' },
    hod_rejected: { current: 'HOD Rejected', next: null, lastApproved: null },
    divisional_rejected: {
      current: 'Divisional Rejected',
      next: 'HOD Review',
      lastApproved: 'HOD'
    },
    ict_director_rejected: {
      current: 'ICT Director Rejected',
      next: 'HOD Review',
      lastApproved: 'Divisional Director'
    },
    head_it_rejected: {
      current: 'Head IT Rejected',
      next: 'HOD Review',
      lastApproved: 'ICT Director'
    },
    ict_officer_rejected: {
      current: 'ICT Officer Rejected',
      next: 'HOD Review',
      lastApproved: 'Head IT'
    },
    cancelled: { current: 'Cancelled', next: null, lastApproved: null }
  }

  return (
    workflow[status] || {
      current: `Unknown Status (${status})`,
      next: null,
      lastApproved: null
    }
  )
}

function formatWorkflowStatus(status) {
  const workflow = getWorkflowStatus(status)

  if (workflow.lastApproved && workflow.next && !workflow.next.includes('Review')) {
    return `${workflow.lastApproved} approved — Next: ${workflow.next} pending`
  } else if (workflow.next && workflow.next.includes('Review')) {
    return `${workflow.current} — Next: ${workflow.next}`
  } else if (workflow.next === null) {
    return workflow.current
  } else {
    return `${workflow.current} — Next: ${workflow.next}`
  }
}

// Test all statuses
export function testWorkflowStatuses() {
  console.log('🧪 [WorkflowTest] Testing enhanced workflow status display...')

  const testStatuses = [
    'pending',
    'pending_hod',
    'hod_approved',
    'pending_divisional',
    'divisional_approved',
    'pending_ict_director',
    'ict_director_approved',
    'pending_head_it',
    'head_it_approved',
    'pending_ict_officer',
    'ict_officer_approved',
    'approved',
    'implemented',
    'completed',
    'hod_rejected',
    'divisional_rejected',
    'ict_director_rejected',
    'head_it_rejected',
    'ict_officer_rejected',
    'cancelled',
    'unknown_status'
  ]

  testStatuses.forEach((status) => {
    const formatted = formatWorkflowStatus(status)
    console.log(`📋 [WorkflowTest] "${status}" -> "${formatted}"`)
  })

  console.log('✅ [WorkflowTest] Workflow status testing completed')
}

// Test specific case mentioned by user
export function testDivisionalApprovedCase() {
  console.log('🎯 [WorkflowTest] Testing specific case: divisional_approved')

  const status = 'divisional_approved'
  const workflow = getWorkflowStatus(status)
  const formatted = formatWorkflowStatus(status)

  console.log(`📊 [WorkflowTest] Status: ${status}`)
  console.log(`📊 [WorkflowTest] Workflow:`, workflow)
  console.log(`📊 [WorkflowTest] Formatted: "${formatted}"`)

  // This should show: "Divisional Director approved — Next: ICT Director pending"
  const expected = 'Divisional Director approved — Next: ICT Director pending'
  const isCorrect = formatted === expected

  console.log(`${isCorrect ? '✅' : '❌'} [WorkflowTest] Expected: "${expected}"`)
  console.log(`${isCorrect ? '✅' : '❌'} [WorkflowTest] Actual: "${formatted}"`)

  return isCorrect
}

// Mock function for testing split status parts
function getWorkflowStatusParts(status) {
  const workflow = getWorkflowStatus(status)

  // For statuses with both approved role and next step
  if (workflow.lastApproved && workflow.next && !workflow.next.includes('Review')) {
    return {
      approved: `${workflow.lastApproved} approved`,
      next: `Next: ${workflow.next} pending`
    }
  }

  // For rejected statuses with review next step
  if (workflow.next && workflow.next.includes('Review')) {
    return {
      approved: null,
      next: `Next: ${workflow.next}`
    }
  }

  // For pending statuses that don't have a "lastApproved"
  if (workflow.next && !workflow.lastApproved) {
    return {
      approved: null,
      next: `Next: ${workflow.next} pending`
    }
  }

  // For final states or single status (no splitting)
  return {
    approved: null,
    next: null
  }
}

// Test the split status parts for color display
export function testSplitStatusParts() {
  console.log('🎨 [WorkflowTest] Testing split status parts with colors...')

  const testStatuses = [
    'divisional_approved',
    'ict_director_approved',
    'head_it_approved',
    'pending_hod',
    'hod_rejected',
    'completed'
  ]

  testStatuses.forEach((status) => {
    const parts = getWorkflowStatusParts(status)
    console.log(`🎨 [WorkflowTest] "${status}":`)
    if (parts.approved) {
      console.log(`  ✅ Green badge: "${parts.approved}"`)
    }
    if (parts.next) {
      console.log(`  🟡 Yellow badge: "${parts.next}"`)
    }
    if (!parts.approved && !parts.next) {
      console.log(`  📋 Single badge: "${formatWorkflowStatus(status)}"`)
    }
    console.log('')
  })

  // Test the specific divisional_approved case
  console.log('🎯 [WorkflowTest] Testing divisional_approved split:')
  const divParts = getWorkflowStatusParts('divisional_approved')
  console.log(`  ✅ Green: "${divParts.approved}"`)
  console.log(`  🟡 Yellow: "${divParts.next}"`)

  const expectedGreen = 'Divisional Director approved'
  const expectedYellow = 'Next: ICT Director pending'
  const isCorrectGreen = divParts.approved === expectedGreen
  const isCorrectYellow = divParts.next === expectedYellow

  console.log(`${isCorrectGreen ? '✅' : '❌'} Expected green: "${expectedGreen}"`)
  console.log(`${isCorrectYellow ? '✅' : '❌'} Expected yellow: "${expectedYellow}"`)

  return isCorrectGreen && isCorrectYellow
}

// Add to window for browser console testing
if (typeof window !== 'undefined') {
  window.testWorkflow = testWorkflowStatuses
  window.testDivisionalApproved = testDivisionalApprovedCase
  window.testSplitColors = testSplitStatusParts
}

export default {
  testWorkflowStatuses,
  testDivisionalApprovedCase,
  testSplitStatusParts,
  getWorkflowStatus,
  getWorkflowStatusParts,
  formatWorkflowStatus
}
