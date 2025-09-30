<template>
  <div v-if="show" class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200">
    <!-- Header -->
    <div class="flex items-center justify-between p-4 border-b border-gray-200">
      <h3 class="text-lg font-semibold text-gray-900 flex items-center">
        <svg
          class="w-5 h-5 mr-2 text-blue-600"
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
        Update Implementation Progress
      </h3>
      <span
        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
        :class="getRequestStatusClasses(requestData?.status)"
      >
        {{ getRequestStatusLabel(requestData?.status) }}
      </span>
    </div>

    <!-- Request Information -->
    <div class="p-4 bg-gray-50 border-b border-gray-200">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
        <div>
          <span class="font-medium text-gray-700">Request ID:</span>
          <span class="ml-2 text-gray-900">
            {{ requestData?.request_id || `REQ-${requestData?.id?.toString().padStart(6, '0')}` }}
          </span>
        </div>
        <div>
          <span class="font-medium text-gray-700">Staff Name:</span>
          <span class="ml-2 text-gray-900">{{ requestData?.staff_name || 'N/A' }}</span>
        </div>
        <div>
          <span class="font-medium text-gray-700">Department:</span>
          <span class="ml-2 text-gray-900">{{ requestData?.department_name || 'N/A' }}</span>
        </div>
      </div>
    </div>

    <!-- Progress Status Options -->
    <div class="p-4">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <!-- In Progress Card -->
        <div
          :class="[
            'p-4 rounded-lg border-2 cursor-pointer transition-all duration-200',
            selectedStatus === 'implementation_in_progress'
              ? 'border-blue-500 bg-blue-50'
              : 'border-gray-200 bg-white hover:border-blue-300 hover:bg-blue-25'
          ]"
          @click="selectStatus('implementation_in_progress')"
        >
          <div class="flex items-center">
            <div
              :class="[
                'w-4 h-4 rounded-full border-2 mr-3',
                selectedStatus === 'implementation_in_progress'
                  ? 'border-blue-500 bg-blue-500'
                  : 'border-gray-300'
              ]"
            >
              <div
                v-if="selectedStatus === 'implementation_in_progress'"
                class="w-2 h-2 bg-white rounded-full m-0.5"
              ></div>
            </div>
            <div>
              <div
                :class="[
                  'font-semibold text-base',
                  selectedStatus === 'implementation_in_progress'
                    ? 'text-blue-900'
                    : 'text-gray-700'
                ]"
              >
                In Progress
              </div>
              <div class="text-sm text-gray-500">Implementation has started</div>
            </div>
          </div>
          <div class="mt-2 flex items-center text-sm text-blue-600">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
              />
            </svg>
            Working on it
          </div>
        </div>

        <!-- Completed Card -->
        <div
          :class="[
            'p-4 rounded-lg border-2 cursor-pointer transition-all duration-200',
            selectedStatus === 'implemented'
              ? 'border-green-500 bg-green-50'
              : 'border-gray-200 bg-white hover:border-green-300 hover:bg-green-25'
          ]"
          @click="selectStatus('implemented')"
        >
          <div class="flex items-center">
            <div
              :class="[
                'w-4 h-4 rounded-full border-2 mr-3',
                selectedStatus === 'implemented'
                  ? 'border-green-500 bg-green-500'
                  : 'border-gray-300'
              ]"
            >
              <div
                v-if="selectedStatus === 'implemented'"
                class="w-2 h-2 bg-white rounded-full m-0.5"
              ></div>
            </div>
            <div>
              <div
                :class="[
                  'font-semibold text-base',
                  selectedStatus === 'implemented' ? 'text-green-900' : 'text-gray-700'
                ]"
              >
                Completed
              </div>
              <div class="text-sm text-gray-500">Implementation finished</div>
            </div>
          </div>
          <div class="mt-2 flex items-center text-sm text-green-600">
            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
              <path
                fill-rule="evenodd"
                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                clip-rule="evenodd"
              />
            </svg>
            All done
          </div>
        </div>

        <!-- Rejected Card -->
        <div
          :class="[
            'p-4 rounded-lg border-2 cursor-pointer transition-all duration-200',
            selectedStatus === 'rejected'
              ? 'border-red-500 bg-red-50'
              : 'border-gray-200 bg-white hover:border-red-300 hover:bg-red-25'
          ]"
          @click="selectStatus('rejected')"
        >
          <div class="flex items-center">
            <div
              :class="[
                'w-4 h-4 rounded-full border-2 mr-3',
                selectedStatus === 'rejected' ? 'border-red-500 bg-red-500' : 'border-gray-300'
              ]"
            >
              <div
                v-if="selectedStatus === 'rejected'"
                class="w-2 h-2 bg-white rounded-full m-0.5"
              ></div>
            </div>
            <div>
              <div
                :class="[
                  'font-semibold text-base',
                  selectedStatus === 'rejected' ? 'text-red-900' : 'text-gray-700'
                ]"
              >
                Rejected
              </div>
              <div class="text-sm text-gray-500">Cannot be implemented</div>
            </div>
          </div>
          <div class="mt-2 flex items-center text-sm text-red-600">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M6 18L18 6M6 6l12 12"
              />
            </svg>
            Issue found
          </div>
        </div>
      </div>

      <!-- Comments Section -->
      <div class="mb-6">
        <label for="comments" class="block text-sm font-medium text-gray-700 mb-2">
          Implementation Comments
          <span class="text-gray-500 font-normal">(Optional)</span>
        </label>
        <textarea
          id="comments"
          v-model="comments"
          rows="4"
          placeholder="Add details about the implementation progress, any issues encountered, or completion notes..."
          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none text-sm"
        ></textarea>
        <div class="mt-1 text-xs text-gray-500">{{ comments.length }}/500 characters</div>
      </div>

      <!-- Error Message -->
      <div v-if="error" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-md">
        <div class="flex items-center">
          <svg class="w-4 h-4 text-red-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
            <path
              fill-rule="evenodd"
              d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
              clip-rule="evenodd"
            />
          </svg>
          <span class="text-red-700 text-sm">{{ error }}</span>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="flex justify-end space-x-3">
        <button
          @click="close"
          class="px-6 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200"
        >
          Cancel
        </button>
        <button
          @click="updateProgress"
          :disabled="!selectedStatus || updating"
          class="inline-flex items-center px-6 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200"
        >
          <svg v-if="updating" class="w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
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
          <svg v-else class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
            />
          </svg>
          <span v-if="updating">Updating...</span>
          <span v-else>Update Progress</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script>
  import ictOfficerService from '@/services/ictOfficerService'
  import { useAuth } from '@/composables/useAuth'

  export default {
    name: 'UpdateProgress',
    props: {
      show: {
        type: Boolean,
        default: false
      },
      requestId: {
        type: [Number, String],
        required: true
      },
      requestData: {
        type: Object,
        default: null
      }
    },
    emits: ['close', 'updated'],
    setup() {
      const { hasRole, ROLES, user } = useAuth()
      return {
        hasRole,
        ROLES,
        currentUser: user
      }
    },
    data() {
      return {
        selectedStatus: '',
        comments: '',
        updating: false,
        error: null
      }
    },
    computed: {
      canUpdate() {
        if (!this.hasRole(this.ROLES.ICT_OFFICER) || !this.requestData) return false

        // Check if this ICT officer is assigned to this request and it's not yet completed
        return (
          this.requestData.ict_officer_user_id &&
          this.requestData.ict_officer_user_id === this.currentUser?.id &&
          !this.requestData.ict_officer_implemented_at &&
          this.requestData.ict_officer_status !== 'implemented' &&
          this.requestData.ict_officer_status !== 'rejected'
        )
      }
    },
    watch: {
      show(newVal) {
        if (newVal) {
          this.resetForm()
        }
      },
      comments(newVal) {
        // Limit to 500 characters
        if (newVal.length > 500) {
          this.comments = newVal.substring(0, 500)
        }
      }
    },
    methods: {
      selectStatus(status) {
        this.selectedStatus = status
        this.error = null
      },

      resetForm() {
        this.selectedStatus = ''
        this.comments = ''
        this.error = null
        this.updating = false
      },

      async updateProgress() {
        if (!this.selectedStatus) {
          this.error = 'Please select a status'
          return
        }

        this.updating = true
        this.error = null

        try {
          const result = await ictOfficerService.updateProgress(
            this.requestId,
            this.selectedStatus,
            this.comments
          )

          if (result.success) {
            // Show success message
            this.showSuccessMessage()

            // Force immediate badge refresh for ICT Officers
            this.refreshNotificationBadge()

            // Emit events
            this.$emit('updated')
            this.$emit('close')

            // Reset form
            this.resetForm()
          } else {
            this.error = result.message || 'Failed to update progress'
          }
        } catch (error) {
          console.error('Error updating progress:', error)
          this.error = error.message || 'Network error while updating progress'
        } finally {
          this.updating = false
        }
      },

      close() {
        this.$emit('close')
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

      getRequestStatusClasses(status) {
        switch (status) {
          case 'assigned_to_ict':
            return 'bg-blue-100 text-blue-800'
          case 'implementation_in_progress':
            return 'bg-purple-100 text-purple-800'
          case 'implemented':
            return 'bg-green-100 text-green-800'
          case 'rejected':
            return 'bg-red-100 text-red-800'
          default:
            return 'bg-gray-100 text-gray-800'
        }
      },

      getRequestStatusLabel(status) {
        switch (status) {
          case 'assigned_to_ict':
            return 'Assigned to ICT'
          case 'implementation_in_progress':
            return 'In Progress'
          case 'implemented':
            return 'Implemented'
          case 'rejected':
            return 'Rejected'
          default:
            return 'Unknown'
        }
      },

      // Force refresh notification badge in sidebar
      refreshNotificationBadge() {
        try {
          console.log(
            '🔔 UpdateProgress: Triggering notification badge refresh after status update'
          )

          // Method 0: Clear notification service cache first
          this.clearNotificationCache()

          // Method 1: Trigger global refresh event with force flag
          if (window.dispatchEvent) {
            const event = new CustomEvent('force-refresh-notifications', {
              detail: {
                source: 'UpdateProgress',
                reason: 'implementation_status_updated',
                requestId: this.requestId,
                newStatus: this.selectedStatus,
                timestamp: Date.now()
              }
            })
            window.dispatchEvent(event)
            console.log('🚀 UpdateProgress: Dispatched force-refresh-notifications event')
          }

          // Method 2: Direct call to sidebar instance with force refresh
          if (window.sidebarInstance && window.sidebarInstance.fetchNotificationCounts) {
            console.log('📞 UpdateProgress: Calling sidebar fetchNotificationCounts directly')
            window.sidebarInstance.fetchNotificationCounts(true) // force refresh
          }

          // Method 3: Add a slight delay then refresh again to ensure backend has processed the change
          setTimeout(() => {
            console.log('⏰ UpdateProgress: Delayed badge refresh to ensure backend sync')
            this.clearNotificationCache() // Clear cache again before delayed refresh
            if (window.dispatchEvent) {
              const delayedEvent = new CustomEvent('refresh-notifications', {
                detail: {
                  source: 'UpdateProgress_delayed',
                  reason: 'delayed_refresh',
                  requestId: this.requestId
                }
              })
              window.dispatchEvent(delayedEvent)
            }
            if (window.sidebarInstance && window.sidebarInstance.fetchNotificationCounts) {
              window.sidebarInstance.fetchNotificationCounts(true)
            }
          }, 2000) // 2 second delay to allow backend processing

          // Method 4: Extra delayed refresh for implemented/completed status changes
          if (['implemented', 'completed'].includes(this.selectedStatus)) {
            setTimeout(() => {
              console.log(
                '🔄 UpdateProgress: Final refresh for completed status to ensure badge decrement'
              )
              this.clearNotificationCache()
              if (window.sidebarInstance && window.sidebarInstance.fetchNotificationCounts) {
                window.sidebarInstance.fetchNotificationCounts(true)
              }
            }, 5000) // 5 second delay for completed status changes
          }
        } catch (error) {
          console.warn('UpdateProgress: Failed to refresh notification badge:', error)
        }
      },

      // Clear notification cache to ensure fresh data
      clearNotificationCache() {
        try {
          // Import and clear notification service cache dynamically
          import('@/services/notificationService').then((module) => {
            module.default.clearCache()
            console.log('🗑️ UpdateProgress: Cleared notification service cache')
          })
        } catch (error) {
          console.warn('UpdateProgress: Failed to clear notification cache:', error)
        }
      }
    }
  }
</script>

<style scoped>
  .bg-blue-25 {
    background-color: rgba(59, 130, 246, 0.05);
  }

  .bg-green-25 {
    background-color: rgba(16, 185, 129, 0.05);
  }

  .bg-red-25 {
    background-color: rgba(239, 68, 68, 0.05);
  }

  .hover\:bg-blue-25:hover {
    background-color: rgba(59, 130, 246, 0.05);
  }

  .hover\:bg-green-25:hover {
    background-color: rgba(16, 185, 129, 0.05);
  }

  .hover\:bg-red-25:hover {
    background-color: rgba(239, 68, 68, 0.05);
  }
</style>
