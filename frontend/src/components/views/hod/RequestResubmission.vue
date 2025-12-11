<template>
  <div class="flex flex-col h-screen">
    <Header />
    <div class="flex flex-1 overflow-hidden">
      <ModernSidebar />
      <main
        class="flex-1 p-6 bg-gradient-to-br from-blue-900 via-blue-800 to-teal-900 overflow-y-auto relative"
      >
        <!-- Medical Background Pattern -->
        <div class="absolute inset-0 overflow-hidden">
          <div class="absolute inset-0 opacity-5">
            <div class="grid grid-cols-12 gap-8 h-full transform rotate-45">
              <div
                v-for="i in 48"
                :key="i"
                class="bg-white rounded-full w-2 h-2 animate-pulse"
                :style="{ animationDelay: i * 0.1 + 's' }"
              ></div>
            </div>
          </div>
        </div>

        <div class="max-w-4xl mx-auto relative z-10">
          <!-- Header Section -->
          <div class="medical-glass-card rounded-t-3xl p-6 mb-0 border-b border-blue-300/30">
            <div class="flex items-center justify-between">
              <div class="flex items-center">
                <router-link
                  to="/hod-dashboard/divisional-recommendations"
                  class="mr-4 p-2 rounded-lg bg-blue-600/20 hover:bg-blue-600/30 transition-colors"
                >
                  <i class="fas fa-arrow-left text-blue-300 hover:text-white"></i>
                </router-link>
                <div>
                  <h2 class="text-xl font-bold text-blue-100 tracking-wide drop-shadow-md">
                    <i class="fas fa-redo text-orange-400 mr-2"></i>
                    Resubmit Request
                  </h2>
                  <p class="text-sm text-teal-300 mt-1">
                    Address divisional director feedback and resubmit request
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Main Content -->
          <div class="medical-glass-card rounded-b-3xl overflow-hidden">
            <div class="p-6">
              <!-- Loading State -->
              <div v-if="loading" class="text-center py-12">
                <i class="fas fa-spinner fa-spin text-4xl text-blue-400 mb-4"></i>
                <p class="text-white text-lg">Loading request details...</p>
              </div>

              <!-- Error State -->
              <div v-else-if="error" class="text-center py-12">
                <div class="mb-6">
                  <i class="fas fa-exclamation-triangle text-6xl text-red-400 mb-4"></i>
                </div>
                <h3 class="text-lg font-semibold text-white mb-2">Error Loading Request</h3>
                <p class="text-red-300 max-w-md mx-auto mb-6">{{ error }}</p>
                <button
                  @click="fetchRequestDetails"
                  class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors"
                >
                  <i class="fas fa-sync-alt mr-2"></i>
                  Try Again
                </button>
              </div>

              <!-- Request Details and Resubmission Form -->
              <div v-else-if="requestDetails" class="space-y-8">
                <!-- Original Request Information -->
                <div
                  class="bg-gradient-to-r from-blue-600/20 to-cyan-600/20 border border-blue-400/30 rounded-xl p-6"
                >
                  <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                    <i class="fas fa-file-alt text-blue-400 mr-3"></i>
                    Request Information
                  </h3>
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                      <span class="text-blue-300">Request ID:</span>
                      <span class="text-white font-medium ml-2">#{{ requestDetails.id }}</span>
                    </div>
                    <div>
                      <span class="text-blue-300">Staff:</span>
                      <span class="text-white font-medium ml-2">{{
                        requestDetails.staff_name
                      }}</span>
                    </div>
                    <div>
                      <span class="text-blue-300">PF Number:</span>
                      <span class="text-white font-medium ml-2">{{
                        requestDetails.pf_number
                      }}</span>
                    </div>
                    <div>
                      <span class="text-blue-300">Department:</span>
                      <span class="text-white font-medium ml-2">{{
                        requestDetails.department
                      }}</span>
                    </div>
                  </div>
                </div>

                <!-- Divisional Director Feedback -->
                <div
                  class="bg-gradient-to-r from-red-600/20 to-pink-600/20 border border-red-400/30 rounded-xl p-6"
                >
                  <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                    <i class="fas fa-user-tie text-red-400 mr-3"></i>
                    Divisional Director Feedback
                  </h3>
                  <div class="space-y-3">
                    <div>
                      <span class="text-red-300 text-sm">Reviewer:</span>
                      <span class="text-white font-medium ml-2">{{
                        requestDetails.divisional_feedback.name
                      }}</span>
                    </div>
                    <div>
                      <span class="text-red-300 text-sm">Date:</span>
                      <span class="text-white font-medium ml-2">{{
                        formatDate(requestDetails.divisional_feedback.reviewed_at)
                      }}</span>
                    </div>
                    <div class="bg-red-600/20 rounded-lg p-4 border border-red-400/20">
                      <span class="text-red-300 text-sm block mb-2">Comments/Recommendations:</span>
                      <p class="text-white leading-relaxed">
                        {{ requestDetails.divisional_feedback.comments }}
                      </p>
                    </div>
                  </div>
                </div>

                <!-- Resubmission Form -->
                <form
                  @submit.prevent="submitResubmission"
                  class="bg-gradient-to-r from-green-600/20 to-emerald-600/20 border border-green-400/30 rounded-xl p-6"
                >
                  <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                    <i class="fas fa-edit text-green-400 mr-3"></i>
                    Resubmission Details
                  </h3>

                  <!-- Resubmission Notes -->
                  <div class="space-y-4">
                    <div>
                      <label class="block text-green-200 text-sm font-medium mb-2">
                        Response to Divisional Director's Feedback
                        <span class="text-red-400">*</span>
                      </label>
                      <textarea
                        v-model="resubmissionForm.resubmission_notes"
                        :disabled="submitting"
                        rows="6"
                        class="w-full px-4 py-3 bg-white/10 border border-green-400/30 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500/50 focus:border-green-400/50 disabled:opacity-50 disabled:cursor-not-allowed"
                        placeholder="Please explain how you have addressed the divisional director's feedback and recommendations. Be specific about changes made to the request..."
                        required
                      ></textarea>
                      <p class="text-green-300 text-xs mt-1">
                        Minimum 50 characters required to ensure detailed response
                      </p>
                    </div>

                    <!-- Signature Upload (Optional) -->
                    <div>
                      <label class="block text-green-200 text-sm font-medium mb-2">
                        Update HOD Signature (Optional)
                      </label>
                      <div class="space-y-2">
                        <input
                          type="file"
                          ref="signatureInput"
                          @change="handleSignatureUpload"
                          :disabled="submitting"
                          accept=".jpg,.jpeg,.png,.pdf"
                          class="block w-full text-base text-gray-300 bg-white/10 border border-green-400/30 rounded-lg cursor-pointer focus:outline-none focus:ring-2 focus:ring-green-500/50 disabled:opacity-50 disabled:cursor-not-allowed"
                        />
                        <p class="text-green-300 text-xs">
                          Upload a new signature if needed. Accepted formats: JPG, PNG, PDF (max
                          2MB)
                        </p>
                      </div>
                    </div>
                  </div>

                  <!-- Form Actions -->
                  <div
                    class="flex items-center justify-end space-x-4 mt-8 pt-6 border-t border-green-400/20"
                  >
                    <button
                      type="button"
                      @click="$router.push('/hod-dashboard/divisional-recommendations')"
                      :disabled="submitting"
                      class="px-6 py-3 bg-gray-600/30 hover:bg-gray-600/50 text-gray-200 rounded-lg transition-colors border border-gray-400/30 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                      <i class="fas fa-times mr-2"></i>
                      Cancel
                    </button>
                    <button
                      type="submit"
                      :disabled="!canSubmit || submitting"
                      class="px-8 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed relative"
                    >
                      <span v-if="submitting" class="flex items-center">
                        <i class="fas fa-spinner fa-spin mr-2"></i>
                        Submitting...
                      </span>
                      <span v-else class="flex items-center">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Resubmit Request
                      </span>
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script>
  import { ref, computed, onMounted } from 'vue'
  import { useRouter, useRoute } from 'vue-router'
  import Header from '@/components/header.vue'
  import ModernSidebar from '@/components/ModernSidebar.vue'
  import { useAuth } from '@/composables/useAuth'
  import apiClient from '@/services/apiClient'

  export default {
    name: 'RequestResubmission',
    components: {
      Header,
      ModernSidebar
    },
    setup() {
      const router = useRouter()
      const route = useRoute()
      const { requireRole, ROLES } = useAuth()

      const loading = ref(false)
      const submitting = ref(false)
      const error = ref(null)
      const requestDetails = ref(null)
      const signatureInput = ref(null)

      const resubmissionForm = ref({
        resubmission_notes: '',
        hod_signature: null
      })

      const requestId = computed(() => route.params.id)

      const canSubmit = computed(() => {
        return resubmissionForm.value.resubmission_notes.trim().length >= 50
      })

      const fetchRequestDetails = async () => {
        try {
          loading.value = true
          error.value = null

          const response = await apiClient.get(
            `/hod/divisional-recommendations/${requestId.value}/details`
          )

          if (response.data.success) {
            requestDetails.value = response.data.data

            // Check if request can be resubmitted
            if (!requestDetails.value.can_resubmit) {
              error.value =
                'This request cannot be resubmitted. Only requests rejected by divisional director can be resubmitted.'
              return
            }
          } else {
            error.value = response.data.message || 'Failed to fetch request details'
          }
        } catch (err) {
          console.error('Error fetching request details:', err)
          error.value = err.response?.data?.message || 'Failed to fetch request details'
        } finally {
          loading.value = false
        }
      }

      const handleSignatureUpload = (event) => {
        const file = event.target.files[0]
        if (file) {
          // Validate file size (2MB max)
          if (file.size > 2 * 1024 * 1024) {
            alert('File size must be less than 2MB')
            signatureInput.value.value = ''
            return
          }

          // Validate file type
          const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf']
          if (!allowedTypes.includes(file.type)) {
            alert('Only JPG, PNG, and PDF files are allowed')
            signatureInput.value.value = ''
            return
          }

          resubmissionForm.value.hod_signature = file
        }
      }

      const submitResubmission = async () => {
        try {
          submitting.value = true
          error.value = null

          // Create FormData for multipart upload
          const formData = new FormData()
          formData.append('resubmission_notes', resubmissionForm.value.resubmission_notes.trim())

          if (resubmissionForm.value.hod_signature) {
            formData.append('hod_signature', resubmissionForm.value.hod_signature)
          }

          const response = await apiClient.post(
            `/hod/divisional-recommendations/${requestId.value}/resubmit`,
            formData,
            {
              headers: {
                'Content-Type': 'multipart/form-data'
              }
            }
          )

          if (response.data.success) {
            // Show success message and redirect
            alert(
              'Request resubmitted successfully! The divisional director will review your response.'
            )
            router.push('/hod-dashboard/divisional-recommendations')
          } else {
            error.value = response.data.message || 'Failed to resubmit request'
          }
        } catch (err) {
          console.error('Error resubmitting request:', err)
          error.value = err.response?.data?.message || 'Failed to resubmit request'
        } finally {
          submitting.value = false
        }
      }

      const formatDate = (dateString) => {
        if (!dateString) return 'N/A'
        try {
          return new Date(dateString).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
          })
        } catch (error) {
          return dateString
        }
      }

      onMounted(() => {
        requireRole([ROLES.HEAD_OF_DEPARTMENT])
        fetchRequestDetails()
      })

      return {
        loading,
        submitting,
        error,
        requestDetails,
        resubmissionForm,
        signatureInput,
        canSubmit,
        fetchRequestDetails,
        handleSignatureUpload,
        submitResubmission,
        formatDate
      }
    }
  }
</script>

<style scoped>
  /* Medical Glass morphism effects */
  .medical-glass-card {
    background: rgba(59, 130, 246, 0.15);
    backdrop-filter: blur(25px);
    -webkit-backdrop-filter: blur(25px);
    border: 2px solid rgba(96, 165, 250, 0.3);
    box-shadow:
      0 8px 32px rgba(29, 78, 216, 0.4),
      inset 0 1px 0 rgba(255, 255, 255, 0.1);
  }
</style>
