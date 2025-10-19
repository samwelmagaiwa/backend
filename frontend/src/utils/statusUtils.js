/**
 * Centralized status utility for consistent status display across all components
 * This replaces individual component status mapping to avoid inconsistencies
 */

const DEBUG = process.env.NODE_ENV === 'development'

// Status text mappings
export const STATUS_TEXTS = {
  // Basic status
  pending: 'Pending Submission',
  pending_hod: 'Pending HOD Approval',
  hod_approved: 'HOD Approved',
  hod_rejected: 'HOD Rejected',

  // Divisional Director
  pending_divisional: 'Pending Divisional Approval',
  divisional_approved: 'Divisional Approved',
  divisional_rejected: 'Divisional Rejected',

  // ICT Director
  pending_ict_director: 'Pending ICT Director Approval',
  ict_director_approved: 'Approved',
  ict_director_rejected: 'ICT Director Rejected',
  dict_approved: 'Approved',
  dict_rejected: 'ICT Director Rejected',

  // Head IT
  pending_head_it: 'Pending Head IT Approval',
  head_it_approved: 'Head IT Approved',
  head_it_rejected: 'Head IT Rejected',

  // ICT Officer
  pending_ict_officer: 'Pending ICT Officer Approval',
  ict_officer_approved: 'ICT Officer Approved',
  ict_officer_rejected: 'ICT Officer Rejected',

  // Final states
  approved: 'Fully Approved',
  implemented: 'Implemented',
  completed: 'Completed',
  cancelled: 'Cancelled',
  rejected: 'Rejected',

  // Additional states
  in_review: 'In Review',
  in_use: 'In Use',
  returned: 'Returned',
  overdue: 'Overdue'
}

// Status badge classes for styling
export const STATUS_BADGE_CLASSES = {
  // Pending states
  pending: 'bg-blue-100 text-blue-800',
  pending_hod: 'bg-yellow-100 text-yellow-800',
  pending_divisional: 'bg-purple-100 text-purple-800',
  pending_ict_director: 'bg-indigo-100 text-indigo-800',
  pending_head_it: 'bg-cyan-100 text-cyan-800',
  pending_ict_officer: 'bg-orange-100 text-orange-800',

  // Approved states
  hod_approved: 'bg-green-100 text-green-800',
  divisional_approved: 'bg-emerald-100 text-emerald-800',
  ict_director_approved: 'bg-teal-100 text-teal-800 font-bold',
  dict_approved: 'bg-teal-100 text-teal-800 font-bold',
  head_it_approved: 'bg-lime-100 text-lime-800',
  ict_officer_approved: 'bg-green-200 text-green-900',
  approved: 'bg-green-100 text-green-800 font-bold',

  // Rejected states
  hod_rejected: 'bg-red-100 text-red-800',
  divisional_rejected: 'bg-red-100 text-red-800',
  ict_director_rejected: 'bg-red-100 text-red-800',
  dict_rejected: 'bg-red-100 text-red-800',
  head_it_rejected: 'bg-red-100 text-red-800',
  ict_officer_rejected: 'bg-red-100 text-red-800',
  rejected: 'bg-red-100 text-red-800',

  // Final states
  implemented: 'bg-blue-100 text-blue-800',
  completed: 'bg-purple-100 text-purple-800',
  cancelled: 'bg-gray-100 text-gray-800',

  // Additional states
  in_review: 'bg-blue-100 text-blue-800',
  in_use: 'bg-purple-100 text-purple-800',
  returned: 'bg-gray-100 text-gray-800',
  overdue: 'bg-red-100 text-red-800'
}

// Status icons
export const STATUS_ICONS = {
  // Pending states
  pending: 'fas fa-hourglass-start',
  pending_hod: 'fas fa-clock',
  pending_divisional: 'fas fa-clock',
  pending_ict_director: 'fas fa-clock',
  pending_head_it: 'fas fa-clock',
  pending_ict_officer: 'fas fa-clock',

  // Approved states
  hod_approved: 'fas fa-check',
  divisional_approved: 'fas fa-check-double',
  ict_director_approved: 'fas fa-check',
  dict_approved: 'fas fa-check',
  head_it_approved: 'fas fa-check',
  ict_officer_approved: 'fas fa-check',
  approved: 'fas fa-check-circle',

  // Rejected states
  hod_rejected: 'fas fa-times',
  divisional_rejected: 'fas fa-times',
  ict_director_rejected: 'fas fa-times',
  dict_rejected: 'fas fa-times',
  head_it_rejected: 'fas fa-times',
  ict_officer_rejected: 'fas fa-times',
  rejected: 'fas fa-times',

  // Final states
  implemented: 'fas fa-cogs',
  completed: 'fas fa-flag-checkered',
  cancelled: 'fas fa-ban',

  // Additional states
  in_review: 'fas fa-eye',
  in_use: 'fas fa-play',
  returned: 'fas fa-undo',
  overdue: 'fas fa-exclamation-triangle'
}

/**
 * Get status display text with debugging and context-aware display
 * @param {string} status - The status code
 * @param {string} componentName - Name of the component for debugging
 * @returns {string} The display text for the status
 */
export function getStatusText(status, componentName = 'Unknown Component') {
  if (!status) {
    console.warn(`🔍 [StatusUtils] Empty or undefined status passed from ${componentName}`)
    return 'No Status'
  }

  // Context-aware status display for ICT Director components
  if (componentName && componentName.includes('Dict')) {
    // For ICT Director components, divisional_approved means "pending ICT Director approval"
    if (status === 'divisional_approved') {
      console.log(`✅ [StatusUtils] Context-aware status for ${componentName}:`, {
        status,
        displayText: 'Pending'
      })
      return 'Pending'
    }
  }

  const statusText = STATUS_TEXTS[status]

  if (!statusText) {
    console.warn(`🔍 [StatusUtils] Unknown status encountered:`, {
      status,
      componentName,
      availableStatuses: Object.keys(STATUS_TEXTS)
    })
    return `Unknown Status (${status})`
  }

  if (DEBUG)
    console.log(`✅ [StatusUtils] Status resolved:`, {
      status,
      statusText,
      componentName
    })

  return statusText
}

/**
 * Get status badge CSS classes with context awareness
 * @param {string} status - The status code
 * @param {string} componentName - Component name for context
 * @returns {string} CSS classes for the status badge
 */
export function getStatusBadgeClass(status, componentName = '') {
  // Context-aware badge styling for ICT Director components
  if (componentName && componentName.includes('Dict')) {
    // For ICT Director components, divisional_approved should look like a pending status
    if (status === 'divisional_approved') {
      return 'bg-yellow-100 text-yellow-800' // Pending style
    }
  }

  return STATUS_BADGE_CLASSES[status] || 'bg-gray-100 text-gray-800'
}

/**
 * Get status icon class
 * @param {string} status - The status code
 * @returns {string} Font Awesome icon class
 */
export function getStatusIcon(status) {
  return STATUS_ICONS[status] || 'fas fa-question'
}

/**
 * Check if a status is a pending state
 * @param {string} status - The status code
 * @returns {boolean} True if the status is pending
 */
export function isPendingStatus(status) {
  return status && status.startsWith('pending')
}

/**
 * Check if a status is an approved state
 * @param {string} status - The status code
 * @returns {boolean} True if the status is approved
 */
export function isApprovedStatus(status) {
  return status && (status.includes('approved') || status === 'approved')
}

/**
 * Check if a status is a rejected state
 * @param {string} status - The status code
 * @returns {boolean} True if the status is rejected
 */
export function isRejectedStatus(status) {
  return status && (status.includes('rejected') || status === 'rejected')
}

/**
 * Check if a status is a final state (no more changes possible)
 * @param {string} status - The status code
 * @returns {boolean} True if the status is final
 */
export function isFinalStatus(status) {
  const finalStatuses = ['implemented', 'completed', 'cancelled']
  return finalStatuses.includes(status)
}

/**
 * Get the next expected status in the workflow
 * @param {string} currentStatus - Current status
 * @returns {string|null} Next expected status or null if workflow is complete
 */
export function getNextStatus(currentStatus) {
  const workflow = {
    pending: 'pending_hod',
    pending_hod: 'hod_approved',
    hod_approved: 'pending_divisional',
    pending_divisional: 'divisional_approved',
    divisional_approved: 'pending_ict_director',
    pending_ict_director: 'ict_director_approved',
    ict_director_approved: 'pending_head_it',
    pending_head_it: 'head_it_approved',
    head_it_approved: 'pending_ict_officer',
    pending_ict_officer: 'ict_officer_approved',
    ict_officer_approved: 'approved',
    approved: 'implemented',
    implemented: 'completed'
  }

  return workflow[currentStatus] || null
}

/**
 * Get all available statuses
 * @returns {Array<string>} Array of all status codes
 */
export function getAllStatuses() {
  return Object.keys(STATUS_TEXTS)
}

/**
 * Validate that a status exists in our mapping
 * @param {string} status - Status to validate
 * @returns {boolean} True if status is valid
 */
export function isValidStatus(status) {
  return Object.prototype.hasOwnProperty.call(STATUS_TEXTS, status)
}

export default {
  getStatusText,
  getStatusBadgeClass,
  getStatusIcon,
  isPendingStatus,
  isApprovedStatus,
  isRejectedStatus,
  isFinalStatus,
  getNextStatus,
  getAllStatuses,
  isValidStatus,
  STATUS_TEXTS,
  STATUS_BADGE_CLASSES,
  STATUS_ICONS
}
