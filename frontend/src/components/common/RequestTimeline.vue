<template>
  <div
    v-if="show"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[10000]"
    @click.self="close"
  >
    <div class="bg-white rounded-lg max-w-4xl w-full mx-4 max-h-[90vh] overflow-hidden">
      <!-- Modal Header -->
      <div class="flex items-center justify-between p-6 border-b border-gray-200">
        <div>
          <h2 class="text-xl font-semibold text-gray-900">Request Timeline</h2>
          <p class="text-sm text-gray-600" v-if="timelineData?.request">
            Request #{{ timelineData.request.id }} - {{ timelineData.request.staff_name }}
          </p>
        </div>
        <button @click="close" class="text-gray-400 hover:text-gray-600 transition-colors">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M6 18L18 6M6 6l12 12"
            ></path>
          </svg>
        </button>
      </div>

      <!-- Modal Body -->
      <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
        <div v-if="loading" class="flex items-center justify-center py-12">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
          <span class="ml-3 text-gray-600">Loading timeline...</span>
        </div>

        <div v-else-if="error" class="text-center py-12">
          <div class="text-red-600 mb-2">
            <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.268 16.5c-.77.833.192 2.5 1.732 2.5z"
              ></path>
            </svg>
          </div>
          <p class="text-red-600 font-medium">Failed to load timeline</p>
          <p class="text-gray-500 text-sm mt-1">{{ error }}</p>
          <button
            @click="loadTimeline"
            class="mt-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors"
          >
            Retry
          </button>
        </div>

        <!-- Timeline Content -->
        <div v-else-if="timelineData" class="relative">
          <!-- Request Information -->
          <div class="mb-4 p-3 bg-gray-50 rounded-lg">
            <h3 class="font-medium text-gray-900 mb-1 text-sm">Request Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-xs">
              <div>
                <span class="font-medium">Staff Name:</span> {{ timelineData.request.staff_name }}
              </div>
              <div>
                <span class="font-medium">PF Number:</span> {{ timelineData.request.pf_number }}
              </div>
              <div>
                <span class="font-medium">Department:</span>
                {{ timelineData.request.department?.name || 'N/A' }}
              </div>
              <div>
                <span class="font-medium">Request Type:</span>
                {{ timelineData.request.request_type_name }}
              </div>
              <div v-if="timelineData.request.access_type">
                <span class="font-medium">Access Type:</span>
                {{ timelineData.request.access_type_name }}
              </div>
              <div>
                <span class="font-medium">Submitted:</span>
                {{ formatDateTime(timelineData.request.created_at) }}
              </div>
            </div>
          </div>

          <!-- Timeline Steps -->
          <div class="space-y-4">
            <div
              v-for="(step, index) in timelineSteps"
              :key="step.id"
              class="relative flex items-start"
            >
              <!-- Timeline Line -->
              <div
                v-if="index < timelineSteps.length - 1"
                class="absolute left-3 top-8 w-0.5 h-12 bg-gray-200"
                :class="{
                  'bg-green-400': step.status === 'completed',
                  'bg-red-400': step.status === 'rejected'
                }"
              ></div>

              <!-- Step Icon -->
              <div
                class="flex-shrink-0 w-6 h-6 rounded-full flex items-center justify-center border-2 bg-white"
                :class="getStepIconClasses(step.status)"
              >
                <svg
                  v-if="step.status === 'completed'"
                  class="w-3 h-3 text-white"
                  fill="currentColor"
                  viewBox="0 0 20 20"
                >
                  <path
                    fill-rule="evenodd"
                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                    clip-rule="evenodd"
                  ></path>
                </svg>
                <svg
                  v-else-if="step.status === 'rejected'"
                  class="w-3 h-3 text-white"
                  fill="currentColor"
                  viewBox="0 0 20 20"
                >
                  <path
                    fill-rule="evenodd"
                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                    clip-rule="evenodd"
                  ></path>
                </svg>
                <div v-else class="w-1.5 h-1.5 rounded-full bg-gray-400"></div>
              </div>

              <!-- Step Content -->
              <div class="ml-4 flex-1">
                <div
                  class="bg-white border rounded-lg p-3 shadow-sm"
                  :class="{
                    'border-green-200 bg-green-50': step.status === 'completed',
                    'border-red-200 bg-red-50': step.status === 'rejected',
                    'border-gray-200': step.status === 'pending'
                  }"
                >
                  <!-- Step Header -->
                  <div class="flex items-center justify-between mb-1">
                    <h4
                      class="font-medium text-sm"
                      :class="{
                        'text-green-900': step.status === 'completed',
                        'text-red-900': step.status === 'rejected',
                        'text-gray-500': step.status === 'pending',
                        'text-gray-900': step.status === 'active'
                      }"
                    >
                      {{ step.title }}
                    </h4>
                    <span
                      class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                      :class="getStatusClasses(step.status)"
                    >
                      {{ step.statusLabel }}
                    </span>
                  </div>

                  <!-- Step Details -->
                  <div class="space-y-1 text-xs">
                    <div v-if="step.actor" class="flex items-center">
                      <span class="font-medium text-gray-700 w-16 text-xs">Actor:</span>
                      <span class="text-xs">{{ step.actor }}</span>
                      <span v-if="step.position" class="text-gray-500 ml-1 text-xs"
                        >({{ step.position }})</span
                      >
                    </div>

                    <div v-if="step.timestamp" class="flex items-center">
                      <span class="font-medium text-gray-700 w-16 text-xs">Date:</span>
                      <span class="text-xs">{{ formatDateTime(step.timestamp) }}</span>
                    </div>

                    <div v-if="step.comments" class="flex items-start">
                      <span class="font-medium text-gray-700 w-16 flex-shrink-0 text-xs"
                        >Comments:</span
                      >
                      <span class="text-gray-900 text-xs leading-tight">{{ step.comments }}</span>
                    </div>

                    <div
                      v-if="step.rejectionReasons && step.rejectionReasons.length > 0"
                      class="flex items-start"
                    >
                      <span class="font-medium text-gray-700 w-16 flex-shrink-0 text-xs"
                        >Reasons:</span
                      >
                      <div class="space-y-0.5">
                        <div
                          v-for="reason in step.rejectionReasons"
                          :key="reason"
                          class="text-red-700 bg-red-100 px-1.5 py-0.5 rounded text-xs"
                        >
                          {{ reason }}
                        </div>
                      </div>
                    </div>

                    <div v-if="step.hasSignature" class="flex items-center">
                      <span class="font-medium text-gray-700 w-16 text-xs">Signature:</span>
                      <span class="text-green-600 text-xs">✓ Signed</span>
                    </div>

                    <div v-if="step.assignedOfficer" class="flex items-center">
                      <span class="font-medium text-gray-700 w-16 text-xs">Officer:</span>
                      <span class="text-xs">{{ step.assignedOfficer }}</span>
                    </div>
                  </div>

                  <!-- ICT Officer Update Form -->
                  <div
                    v-if="
                      step.id === 'ict_officer_implementation' &&
                      canUpdateImplementation &&
                      !showUpdateForm
                    "
                    class="mt-2"
                  >
                    <button
                      @click="openUpdateForm"
                      class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-xs font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                    >
                      <svg
                        class="w-3 h-3 mr-1"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                        />
                      </svg>
                      Update Progress
                    </button>
                  </div>

                  <!-- Progress Update Form -->
                  <div
                    v-if="step.id === 'ict_officer_implementation' && showUpdateForm"
                    class="mt-2 border-t border-gray-200 pt-2"
                  >
                    <div class="bg-gray-50 rounded-lg p-3">
                      <div class="flex items-center justify-between mb-2">
                        <h5 class="text-xs font-medium text-gray-900">
                          Update Implementation Progress
                        </h5>
                        <button @click="closeUpdateForm" class="text-gray-400 hover:text-gray-600">
                          <svg
                            class="w-4 h-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                          >
                            <path
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M6 18L18 6M6 6l12 12"
                            />
                          </svg>
                        </button>
                      </div>

                      <form @submit.prevent="updateImplementationProgress" class="space-y-2">
                        <div>
                          <label
                            for="status"
                            class="block text-xs font-medium text-gray-700 mb-0.5"
                          >
                            Status
                          </label>
                          <select
                            id="status"
                            v-model="updateForm.status"
                            required
                            class="w-full px-2 py-1 text-xs border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                          >
                            <option value="">Select Status</option>
                            <option value="implementation_in_progress">In Progress</option>
                            <option value="implemented">Implemented</option>
                            <option value="rejected">Rejected</option>
                          </select>
                        </div>

                        <div>
                          <label
                            for="comments"
                            class="block text-xs font-medium text-gray-700 mb-0.5"
                          >
                            Implementation Comments
                          </label>
                          <textarea
                            id="comments"
                            v-model="updateForm.comments"
                            rows="2"
                            placeholder="Add comments about the implementation progress..."
                            class="w-full px-2 py-1 text-xs border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 resize-none"
                          ></textarea>
                        </div>

                        <div v-if="updateError" class="text-red-600 text-xs">
                          {{ updateError }}
                        </div>

                        <div class="flex justify-end space-x-2">
                          <button
                            type="button"
                            @click="closeUpdateForm"
                            class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                          >
                            Cancel
                          </button>
                          <button
                            type="submit"
                            :disabled="updating || !updateForm.status"
                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                          >
                            <svg
                              v-if="updating"
                              class="w-3 h-3 mr-1 animate-spin"
                              fill="none"
                              viewBox="0 0 24 24"
                            >
                              <circle
                                class="opacity-25"
                                cx="12"
                                cy="12"
                                r="10"
                                stroke="currentColor"
                                stroke-width="4"
                              ></circle>
                              <path
                                class="opacity-75"
                                fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                              ></path>
                            </svg>
                            <span v-if="updating">Updating...</span>
                            <span v-else>Update Progress</span>
                          </button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal Footer -->
      <div class="flex justify-between p-4 border-t border-gray-200">
        <!-- Update Progress Button (for assigned ICT Officers) -->
        <div>
          <button
            v-if="canShowUpdateProgressButton"
            @click="openUpdateProgress"
            class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-xs font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
          >
            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
              />
            </svg>
            Update Progress
          </button>
        </div>

        <!-- Close Button -->
        <div>
          <button
            @click="close"
            class="px-3 py-1.5 text-xs text-gray-700 bg-gray-200 hover:bg-gray-300 rounded transition-colors"
          >
            Close
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import ictOfficerService from '../../services/ictOfficerService'
  import dictAccessService from '../../services/dictAccessService'
  import divisionalAccessService from '../../services/divisionalAccessService'
  import headOfItService from '../../services/headOfItService'
  import combinedAccessService from '../../services/combinedAccessService'
  import { useAuth } from '@/composables/useAuth'

  export default {
    name: 'RequestTimeline',
    props: {
      show: {
        type: Boolean,
        default: false
      },
      requestId: {
        type: [Number, String],
        default: null
      }
    },
    emits: ['close', 'updated', 'openUpdateProgress'],
    data() {
      return {
        loading: false,
        error: null,
        timelineData: null,
        // ICT Officer update form
        showUpdateForm: false,
        updateForm: {
          status: '',
          comments: ''
        },
        updating: false,
        updateError: null
      }
    },
    setup() {
      const { hasRole, ROLES, user } = useAuth()
      return {
        hasRole,
        ROLES,
        currentUser: user
      }
    },
    computed: {
      isIctOfficer() {
        return this.hasRole(this.ROLES.ICT_OFFICER)
      },

      isIctDirector() {
        return this.hasRole(this.ROLES.ICT_DIRECTOR)
      },

      isDivisionalDirector() {
        return this.hasRole(this.ROLES.DIVISIONAL_DIRECTOR)
      },

      isHeadOfIT() {
        return this.hasRole(this.ROLES.HEAD_OF_IT)
      },

      isHeadOfDepartment() {
        return this.hasRole(this.ROLES.HEAD_OF_DEPARTMENT)
      },

      currentService() {
        // Determine which service to use based on user role
        if (this.isIctOfficer) {
          return ictOfficerService
        } else if (this.isIctDirector) {
          return dictAccessService
        } else if (this.isDivisionalDirector) {
          return divisionalAccessService
        } else if (this.isHeadOfIT) {
          return headOfItService
        } else if (this.isHeadOfDepartment) {
          return combinedAccessService
        } else {
          // Default fallback to ICT Officer service
          return ictOfficerService
        }
      },

      canUpdateImplementation() {
        if (!this.isIctOfficer || !this.timelineData?.request) return false

        const request = this.timelineData.request
        // Check if this ICT officer is assigned to this request and it's not yet completed
        return (
          request.ict_officer_user_id &&
          request.ict_officer_user_id === this.currentUser?.id &&
          !request.ict_officer_implemented_at &&
          request.ict_officer_status !== 'implemented' &&
          request.ict_officer_status !== 'rejected'
        )
      },

      canShowUpdateProgressButton() {
        if (!this.isIctOfficer || !this.timelineData?.request) return false

        const request = this.timelineData.request
        console.log('🔍 RequestTimeline Debug - Request data:', {
          ict_officer_user_id: request.ict_officer_user_id,
          current_user_id: this.currentUser?.id,
          ict_officer_status: request.ict_officer_status,
          status: request.status,
          isIctOfficer: this.isIctOfficer
        })

        // Show button if:
        // 1. User is ICT Officer assigned to this request
        // 2. Request is in a state that allows progress updates (assigned or in progress)
        // 3. Not yet completed or rejected
        const canUpdate =
          request.ict_officer_user_id &&
          request.ict_officer_user_id === this.currentUser?.id &&
          (request.status === 'assigned_to_ict' ||
            request.status === 'implementation_in_progress' ||
            request.ict_officer_status === 'assigned' ||
            request.ict_officer_status === 'implementation_in_progress') &&
          request.ict_officer_status !== 'implemented' &&
          request.ict_officer_status !== 'rejected'

        console.log('🔍 Can show update button:', canUpdate)
        return canUpdate
      },

      timelineSteps() {
        if (!this.timelineData) return []

        const request = this.timelineData.request
        const steps = []

        // 1. Request Submission
        steps.push({
          id: 'submission',
          title: 'Request Submitted',
          status: 'completed',
          statusLabel: 'Completed',
          actor: request.staff_name,
          position: 'Requester',
          timestamp: request.created_at,
          comments: null,
          hasSignature: !!request.signature_path
        })

        // 2. HOD Approval
        const hodStatus = this.getApprovalStatus('hod', request)
        steps.push({
          id: 'hod',
          title: 'HOD/BM Approval',
          status: hodStatus.status,
          statusLabel: hodStatus.label,
          actor: request.hod_name,
          position: 'Head of Department',
          timestamp: request.hod_approved_at,
          comments: request.hod_comments,
          hasSignature: !!request.hod_signature_path
        })

        // 3. Divisional Director Approval
        const divStatus = this.getApprovalStatus('divisional', request)
        steps.push({
          id: 'divisional',
          title: 'Divisional Director Approval',
          status: divStatus.status,
          statusLabel: divStatus.label,
          actor: request.divisional_director_name,
          position: 'Divisional Director',
          timestamp: request.divisional_approved_at,
          comments: request.divisional_director_comments,
          hasSignature: !!request.divisional_director_signature_path
        })

        // 4. ICT Director Approval
        const ictDirStatus = this.getApprovalStatus('ict_director', request)
        steps.push({
          id: 'ict_director',
          title: 'ICT Director Approval',
          status: ictDirStatus.status,
          statusLabel: ictDirStatus.label,
          actor: request.ict_director_name,
          position: 'ICT Director',
          timestamp: request.ict_director_approved_at,
          comments: request.ict_director_comments,
          hasSignature: !!request.ict_director_signature_path,
          rejectionReasons: request.ict_director_rejection_reasons || []
        })

        // 5. Head of IT Approval
        const headItStatus = this.getApprovalStatus('head_it', request)
        steps.push({
          id: 'head_it',
          title: 'Head of IT Approval',
          status: headItStatus.status,
          statusLabel: headItStatus.label,
          actor: request.head_it_name,
          position: 'Head of IT',
          timestamp: request.head_it_approved_at,
          comments: request.head_it_comments,
          hasSignature: !!request.head_it_signature_path
        })

        // 6. ICT Officer Assignment
        const ictOfficerStatus = this.getApprovalStatus('ict_officer', request)
        steps.push({
          id: 'ict_officer_assignment',
          title: 'ICT Officer Assignment',
          status: ictOfficerStatus.assignmentStatus,
          statusLabel: ictOfficerStatus.assignmentLabel,
          actor: 'System', // or the person who assigned
          position: 'Auto Assignment',
          timestamp: request.ict_officer_assigned_at,
          assignedOfficer: request.ict_officer_name,
          comments: null
        })

        // 7. ICT Officer Implementation
        let implementationStatus = 'pending'
        let implementationLabel = 'Waiting'
        let implementationTimestamp = null

        // Use status field as primary source of truth, fall back to timestamps
        if (request.ict_officer_status === 'rejected') {
          implementationStatus = 'rejected'
          implementationLabel = 'Rejected'
          implementationTimestamp = request.ict_officer_rejected_at
        } else if (request.ict_officer_status === 'implemented') {
          implementationStatus = 'completed'
          implementationLabel = 'Implemented'
          implementationTimestamp = this.getValidTimestamp(request.ict_officer_implemented_at)
        } else if (request.ict_officer_status === 'implementation_in_progress') {
          implementationStatus = 'active'
          implementationLabel = 'In Progress'
          implementationTimestamp =
            this.getValidTimestamp(request.ict_officer_started_at) ||
            this.getValidTimestamp(request.ict_officer_assigned_at)
        } else if (request.ict_officer_assigned_at && request.ict_officer_user_id) {
          implementationStatus = 'active'
          implementationLabel = 'Assigned - Pending Implementation'
          implementationTimestamp = this.getValidTimestamp(request.ict_officer_assigned_at)
        }

        steps.push({
          id: 'ict_officer_implementation',
          title: 'ICT Officer Implementation',
          status: implementationStatus,
          statusLabel: implementationLabel,
          actor: request.ict_officer_name,
          position: 'ICT Officer',
          timestamp: implementationTimestamp,
          comments: request.implementation_comments || request.ict_officer_comments,
          hasSignature: !!request.ict_officer_signature_path
        })

        // 8. Final Status (if cancelled)
        if (request.cancelled_at) {
          steps.push({
            id: 'cancellation',
            title: 'Request Cancelled',
            status: 'rejected',
            statusLabel: 'Cancelled',
            actor: request.cancelled_by || 'System',
            position: 'Administrator',
            timestamp: request.cancelled_at,
            comments: request.cancellation_reason
          })
        }

        return steps
      }
    },
    watch: {
      show(newVal) {
        if (newVal && this.requestId) {
          this.loadTimeline()
        }
      },
      requestId(newVal) {
        if (this.show && newVal) {
          this.loadTimeline()
        }
      }
    },
    methods: {
      async loadTimeline() {
        if (!this.requestId) return

        this.loading = true
        this.error = null

        try {
          console.log(`🔄 RequestTimeline: Loading timeline with service for role:`, {
            isIctOfficer: this.isIctOfficer,
            isIctDirector: this.isIctDirector,
            isDivisionalDirector: this.isDivisionalDirector,
            isHeadOfIT: this.isHeadOfIT,
            isHeadOfDepartment: this.isHeadOfDepartment
          })

          const result = await this.currentService.getRequestTimeline(this.requestId)

          // Handle different response formats from different services
          if (result.success !== undefined) {
            // Services that return { success: boolean, data: object }
            if (result.success) {
              this.timelineData = result.data
            } else {
              throw new Error(result.error || result.message || 'Failed to load timeline data')
            }
          } else {
            // Services that return data directly (like ictOfficerService.getRequestTimeline)
            this.timelineData = result
          }

          console.log('✅ RequestTimeline: Timeline loaded successfully')
        } catch (error) {
          console.error('❌ RequestTimeline: Error loading timeline:', error)
          this.error =
            error.message || error.response?.data?.message || 'Failed to load timeline data'
        } finally {
          this.loading = false
        }
      },

      close() {
        this.$emit('close')
      },

      openUpdateProgress() {
        console.log('📝 RequestTimeline: Opening update progress modal')
        this.$emit('openUpdateProgress', this.timelineData?.request)
      },

      openUpdateForm() {
        this.showUpdateForm = true
        this.updateForm.status = ''
        this.updateForm.comments = ''
        this.updateError = null
      },

      closeUpdateForm() {
        this.showUpdateForm = false
        this.updateForm.status = ''
        this.updateForm.comments = ''
        this.updateError = null
      },

      async updateImplementationProgress() {
        if (!this.updateForm.status) {
          this.updateError = 'Please select a status'
          return
        }

        this.updating = true
        this.updateError = null

        try {
          const result = await ictOfficerService.updateImplementationProgress(
            this.requestId,
            this.updateForm.status,
            this.updateForm.comments
          )

          if (result.success) {
            // Refresh the timeline data
            await this.loadTimeline()

            // Close the form
            this.closeUpdateForm()

            // Emit event to parent to refresh main table
            this.$emit('updated')

            // Show success message
            this.showSuccessMessage()
          } else {
            this.updateError = result.message || 'Failed to update progress'
          }
        } catch (error) {
          console.error('Error updating progress:', error)
          this.updateError = error.message || 'Network error while updating progress'
        } finally {
          this.updating = false
        }
      },

      showSuccessMessage() {
        // Create a temporary success message
        const successDiv = document.createElement('div')
        successDiv.className =
          'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-all duration-300'
        successDiv.innerHTML = `
        <div class="flex items-center">
          <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
          </svg>
          Progress updated successfully!
        </div>
      `

        document.body.appendChild(successDiv)

        // Remove after 3 seconds
        setTimeout(() => {
          if (successDiv.parentNode) {
            successDiv.style.transform = 'translateX(100%)'
            successDiv.style.opacity = '0'
            setTimeout(() => {
              document.body.removeChild(successDiv)
            }, 300)
          }
        }, 3000)
      },

      getApprovalStatus(stage, request) {
        const statusField = `${stage}_status`
        const approvedField =
          stage === 'ict_officer' ? `${stage}_implemented_at` : `${stage}_approved_at`

        const status = request[statusField]
        const approvedAt = request[approvedField]

        let result = {}

        if (status === 'rejected') {
          result = { status: 'rejected', label: 'Rejected' }
        } else if (
          status === 'approved' ||
          (stage === 'ict_officer' && status === 'implemented') ||
          approvedAt
        ) {
          result = { status: 'completed', label: 'Approved' }
        } else if (this.isPreviousStageComplete(stage, request)) {
          result = { status: 'active', label: 'Pending' }
        } else {
          result = { status: 'pending', label: 'Waiting' }
        }

        // For ICT Officer assignment status
        if (stage === 'ict_officer') {
          if (request.ict_officer_assigned_at && request.ict_officer_user_id) {
            result.assignmentStatus = 'completed'
            result.assignmentLabel = 'Assigned'
          } else if (request.head_it_approved_at) {
            result.assignmentStatus = 'active'
            result.assignmentLabel = 'Pending Assignment'
          } else {
            result.assignmentStatus = 'pending'
            result.assignmentLabel = 'Waiting'
          }
        }

        return result
      },

      isPreviousStageComplete(currentStage, request) {
        const stages = ['hod', 'divisional', 'ict_director', 'head_it', 'ict_officer']
        const currentIndex = stages.indexOf(currentStage)

        if (currentIndex === 0) return true // HOD is always first

        const previousStage = stages[currentIndex - 1]
        const previousStatusField = `${previousStage}_status`
        const previousApprovedField =
          previousStage === 'ict_officer'
            ? `${previousStage}_implemented_at`
            : `${previousStage}_approved_at`

        return (
          request[previousStatusField] === 'approved' ||
          (previousStage === 'ict_officer' && request[previousStatusField] === 'implemented') ||
          !!request[previousApprovedField]
        )
      },

      getStepIconClasses(status) {
        switch (status) {
          case 'completed':
            return 'bg-green-500 border-green-500'
          case 'rejected':
            return 'bg-red-500 border-red-500'
          case 'active':
            return 'bg-blue-500 border-blue-500'
          default:
            return 'bg-gray-200 border-gray-300'
        }
      },

      getStatusClasses(status) {
        switch (status) {
          case 'Completed':
          case 'Approved':
          case 'Assigned':
            return 'bg-green-100 text-green-800'
          case 'Rejected':
          case 'Cancelled':
            return 'bg-red-100 text-red-800'
          case 'Pending':
          case 'Pending Assignment':
            return 'bg-blue-100 text-blue-800'
          default:
            return 'bg-gray-100 text-gray-800'
        }
      },

      getValidTimestamp(timestamp) {
        if (!timestamp) return null

        try {
          const date = new Date(timestamp)
          // Check if date is valid and not too far in the past/future
          if (isNaN(date.getTime()) || date.getFullYear() < 2020 || date.getFullYear() > 2030) {
            console.warn('Invalid timestamp detected:', timestamp)
            return null
          }
          return timestamp
        } catch (error) {
          console.warn('Error parsing timestamp:', timestamp, error)
          return null
        }
      },

      formatDateTime(dateTime) {
        if (!dateTime) return 'N/A'

        try {
          const date = new Date(dateTime)
          // Check if date is valid
          if (isNaN(date.getTime()) || date.getFullYear() < 2020 || date.getFullYear() > 2030) {
            return 'Invalid Date'
          }
          return date.toLocaleString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
          })
        } catch (error) {
          return 'Invalid Date'
        }
      }
    }
  }
</script>

<style scoped>
  /* Custom scrollbar for timeline content */
  .overflow-y-auto::-webkit-scrollbar {
    width: 6px;
  }

  .overflow-y-auto::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
  }

  .overflow-y-auto::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
  }

  .overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
  }
</style>
