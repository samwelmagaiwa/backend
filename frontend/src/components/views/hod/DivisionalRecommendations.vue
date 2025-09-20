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

        <div class="max-w-7xl mx-auto relative z-10">
          <!-- Header Section -->
          <div class="medical-glass-card rounded-t-3xl p-6 mb-0 border-b border-blue-300/30">
            <div class="flex items-center justify-between">
              <div class="flex items-center">
                <router-link
                  to="/hod-dashboard"
                  class="mr-4 p-2 rounded-lg bg-blue-600/20 hover:bg-blue-600/30 transition-colors"
                >
                  <i class="fas fa-arrow-left text-blue-300 hover:text-white"></i>
                </router-link>
                <div>
                  <h2 class="text-2xl font-bold text-blue-100 tracking-wide drop-shadow-md">
                    <i class="fas fa-comments text-blue-400 mr-3"></i>
                    Divisional Recommendations
                  </h2>
                  <p class="text-sm text-teal-300 mt-1">
                    Comments and suggestions from Divisional Directors
                  </p>
                </div>
              </div>
              <div class="flex items-center space-x-4">
                <button
                  @click="refreshRecommendations"
                  :disabled="loading"
                  class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <i class="fas fa-sync-alt mr-2" :class="{ 'animate-spin': loading }"></i>
                  Refresh
                </button>
              </div>
            </div>
          </div>

          <!-- Main Content -->
          <div class="medical-glass-card rounded-b-3xl overflow-hidden">
            <div class="p-6">
              <!-- Loading State -->
              <div v-if="loading && recommendations.length === 0" class="text-center py-12">
                <i class="fas fa-spinner fa-spin text-4xl text-blue-400 mb-4"></i>
                <p class="text-white text-lg">Loading divisional recommendations...</p>
              </div>

              <!-- Empty State -->
              <div v-else-if="!loading && recommendations.length === 0" class="text-center py-12">
                <div class="mb-6">
                  <i class="fas fa-comment-slash text-6xl text-gray-400 mb-4"></i>
                </div>
                <h3 class="text-xl font-semibold text-white mb-2">No Recommendations Found</h3>
                <p class="text-gray-300 max-w-md mx-auto">
                  There are currently no recommendations or comments from Divisional Directors for
                  requests in your department.
                </p>
                <button
                  @click="refreshRecommendations"
                  class="mt-6 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors"
                >
                  <i class="fas fa-sync-alt mr-2"></i>
                  Check Again
                </button>
              </div>

              <!-- Recommendations List -->
              <div v-else class="space-y-6">
                <div
                  v-for="recommendation in recommendations"
                  :key="recommendation.id"
                  class="medical-card bg-gradient-to-r from-white/10 to-white/5 border border-blue-300/30 rounded-xl p-6 hover:shadow-2xl hover:shadow-blue-500/10 transition-all duration-300"
                >
                  <!-- Request Header -->
                  <div
                    class="flex items-start justify-between mb-4 pb-4 border-b border-blue-300/20"
                  >
                    <div class="flex-1">
                      <div class="flex items-center space-x-3 mb-2">
                        <div
                          class="w-10 h-10 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-lg flex items-center justify-center shadow-lg"
                        >
                          <i class="fas fa-file-alt text-white text-sm"></i>
                        </div>
                        <div>
                          <h3 class="text-lg font-semibold text-white">
                            Request #{{ recommendation.id }}
                          </h3>
                          <p class="text-sm text-blue-300">
                            {{ recommendation.staff_name }} ({{ recommendation.pf_number }})
                          </p>
                        </div>
                      </div>
                      <div class="flex flex-wrap gap-2 mb-2">
                        <span
                          v-for="type in recommendation.request_type"
                          :key="type"
                          class="px-3 py-1 bg-blue-600/30 text-blue-200 text-xs rounded-full border border-blue-400/30"
                        >
                          {{ getRequestTypeLabel(type) }}
                        </span>
                      </div>
                    </div>
                    <div class="text-right ml-4">
                      <div class="text-sm text-gray-300 mb-1">
                        {{ formatDate(recommendation.divisional_approved_at) }}
                      </div>
                      <div class="flex items-center space-x-2">
                        <span
                          class="px-2 py-1 text-xs rounded-full"
                          :class="getStatusClass(recommendation.status)"
                        >
                          {{ getStatusLabel(recommendation.status) }}
                        </span>
                      </div>
                    </div>
                  </div>

                  <!-- Recommendation Content -->
                  <div class="space-y-4">
                    <!-- Divisional Director Comments -->
                    <div
                      v-if="recommendation.divisional_director_comments"
                      class="bg-gradient-to-r from-green-600/20 to-emerald-600/20 border border-green-400/30 rounded-lg p-4"
                    >
                      <div class="flex items-start space-x-3">
                        <div
                          class="w-8 h-8 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full flex items-center justify-center flex-shrink-0 mt-1"
                        >
                          <i class="fas fa-user-tie text-white text-xs"></i>
                        </div>
                        <div class="flex-1">
                          <div class="flex items-center space-x-2 mb-2">
                            <span class="font-medium text-green-200">Divisional Director</span>
                            <span class="text-xs text-green-300">
                              {{ recommendation.divisional_director_name }}
                            </span>
                          </div>
                          <p class="text-white text-sm leading-relaxed">
                            {{ recommendation.divisional_director_comments }}
                          </p>
                        </div>
                      </div>
                    </div>

                    <!-- Request Details -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-4">
                      <!-- Modules Requested -->
                      <div
                        v-if="hasModules(recommendation)"
                        class="bg-blue-600/10 border border-blue-400/20 rounded-lg p-4"
                      >
                        <h4 class="font-medium text-blue-200 mb-3 flex items-center">
                          <i class="fas fa-cube mr-2"></i>
                          Modules Requested
                        </h4>
                        <div class="space-y-2">
                          <div v-if="recommendation.wellsoft_modules_selected?.length" class="mb-3">
                            <span class="text-xs text-gray-300 font-medium">Wellsoft:</span>
                            <div class="flex flex-wrap gap-1 mt-1">
                              <span
                                v-for="module in recommendation.wellsoft_modules_selected"
                                :key="module"
                                class="px-2 py-1 bg-yellow-600/30 text-yellow-200 text-xs rounded border border-yellow-400/30"
                              >
                                {{ module }}
                              </span>
                            </div>
                          </div>
                          <div v-if="recommendation.jeeva_modules_selected?.length" class="mb-3">
                            <span class="text-xs text-gray-300 font-medium">Jeeva:</span>
                            <div class="flex flex-wrap gap-1 mt-1">
                              <span
                                v-for="module in recommendation.jeeva_modules_selected"
                                :key="module"
                                class="px-2 py-1 bg-purple-600/30 text-purple-200 text-xs rounded border border-purple-400/30"
                              >
                                {{ module }}
                              </span>
                            </div>
                          </div>
                          <div v-if="recommendation.internet_purposes?.length" class="mb-3">
                            <span class="text-xs text-gray-300 font-medium"
                              >Internet Purposes:</span
                            >
                            <div class="flex flex-wrap gap-1 mt-1">
                              <span
                                v-for="purpose in recommendation.internet_purposes"
                                :key="purpose"
                                class="px-2 py-1 bg-green-600/30 text-green-200 text-xs rounded border border-green-400/30"
                              >
                                {{ purpose }}
                              </span>
                            </div>
                          </div>
                        </div>
                      </div>

                      <!-- Access Details -->
                      <div class="bg-purple-600/10 border border-purple-400/20 rounded-lg p-4">
                        <h4 class="font-medium text-purple-200 mb-3 flex items-center">
                          <i class="fas fa-key mr-2"></i>
                          Access Details
                        </h4>
                        <div class="space-y-2 text-sm text-gray-300">
                          <div class="flex justify-between">
                            <span>Access Type:</span>
                            <span class="text-white font-medium">{{
                              recommendation.access_type || 'N/A'
                            }}</span>
                          </div>
                          <div v-if="recommendation.temporary_until" class="flex justify-between">
                            <span>Valid Until:</span>
                            <span class="text-white font-medium">{{
                              formatDate(recommendation.temporary_until)
                            }}</span>
                          </div>
                          <div class="flex justify-between">
                            <span>Request Purpose:</span>
                            <span class="text-white font-medium">{{
                              recommendation.module_requested_for || 'Use'
                            }}</span>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Action Buttons -->
                    <!-- Rejection Notice for Rejected Requests -->
                    <div
                      v-if="isRejectedByDivisional(recommendation.status)"
                      class="bg-red-600/20 border border-red-400/30 rounded-lg p-4 mb-4"
                    >
                      <div class="flex items-start space-x-3">
                        <div
                          class="w-8 h-8 bg-red-600 rounded-full flex items-center justify-center flex-shrink-0 mt-1"
                        >
                          <i class="fas fa-times text-white text-xs"></i>
                        </div>
                        <div class="flex-1">
                          <p class="text-red-200 font-medium text-sm mb-2">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Request Rejected by Divisional Director
                          </p>
                          <p class="text-red-100 text-xs leading-relaxed">
                            This request needs to be revised and resubmitted based on the divisional
                            director's feedback above.
                          </p>
                        </div>
                      </div>
                    </div>

                    <div
                      class="flex items-center justify-end space-x-3 pt-4 border-t border-blue-300/20"
                    >
                      <button
                        @click="viewFullRequest(recommendation.id)"
                        class="px-4 py-2 bg-blue-600/30 hover:bg-blue-600/50 text-blue-200 text-sm rounded-lg transition-colors border border-blue-400/30"
                      >
                        <i class="fas fa-eye mr-2"></i>
                        View Full Request
                      </button>

                      <!-- Show Resubmit button for rejected requests -->
                      <button
                        v-if="canResubmitRequest(recommendation.status)"
                        @click="resubmitRequest(recommendation.id)"
                        class="px-4 py-2 bg-orange-600/30 hover:bg-orange-600/50 text-orange-200 text-sm rounded-lg transition-colors border border-orange-400/30"
                      >
                        <i class="fas fa-redo mr-2"></i>
                        Resubmit Request
                      </button>

                      <!-- Show standard respond button for approved requests -->
                      <button
                        v-else
                        @click="respondToRecommendation(recommendation.id)"
                        class="px-4 py-2 bg-green-600/30 hover:bg-green-600/50 text-green-200 text-sm rounded-lg transition-colors border border-green-400/30"
                      >
                        <i class="fas fa-reply mr-2"></i>
                        Respond
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Pagination -->
              <div v-if="pagination.total > pagination.per_page" class="mt-8 flex justify-center">
                <div class="flex items-center space-x-2">
                  <button
                    @click="changePage(pagination.current_page - 1)"
                    :disabled="pagination.current_page <= 1"
                    class="px-3 py-2 bg-blue-600/30 hover:bg-blue-600/50 text-blue-200 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                  >
                    <i class="fas fa-chevron-left"></i>
                  </button>
                  <span class="text-white mx-4">
                    Page {{ pagination.current_page }} of {{ pagination.last_page }}
                  </span>
                  <button
                    @click="changePage(pagination.current_page + 1)"
                    :disabled="pagination.current_page >= pagination.last_page"
                    class="px-3 py-2 bg-blue-600/30 hover:bg-blue-600/50 text-blue-200 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                  >
                    <i class="fas fa-chevron-right"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script>
  import { ref, onMounted } from 'vue'
  import { useRouter } from 'vue-router'
  import Header from '@/components/header.vue'
  import ModernSidebar from '@/components/ModernSidebar.vue'
  import { useAuth } from '@/composables/useAuth'
  import apiClient from '@/services/apiClient'

  export default {
    name: 'DivisionalRecommendations',
    components: {
      Header,
      ModernSidebar
    },
    setup() {
      const router = useRouter()
      const { requireRole, ROLES } = useAuth()

      const loading = ref(false)
      const recommendations = ref([])
      const pagination = ref({
        current_page: 1,
        last_page: 1,
        per_page: 10,
        total: 0
      })

      const requestTypeLabels = {
        jeeva_access: 'Jeeva Access',
        wellsoft: 'Wellsoft',
        internet_access_request: 'Internet Access'
      }

      const statusLabels = {
        pending: 'Pending',
        pending_hod: 'Pending HOD',
        hod_approved: 'HOD Approved',
        pending_divisional: 'Pending Divisional',
        divisional_approved: 'Divisional Approved',
        pending_ict_director: 'Pending ICT Director',
        ict_director_approved: 'ICT Approved',
        implemented: 'Implemented',
        rejected: 'Rejected'
      }

      const fetchDivisionalRecommendations = async (page = 1) => {
        try {
          loading.value = true
          const response = await apiClient.get('/hod/divisional-recommendations', {
            params: { page, per_page: 10 }
          })

          if (response.data.success) {
            recommendations.value = response.data.data
            pagination.value = response.data.pagination || {
              current_page: 1,
              last_page: 1,
              per_page: 10,
              total: response.data.data.length
            }
          } else {
            console.error('Failed to fetch recommendations:', response.data.message)
          }
        } catch (error) {
          console.error('Error fetching divisional recommendations:', error)
          recommendations.value = []
        } finally {
          loading.value = false
        }
      }

      const refreshRecommendations = () => {
        fetchDivisionalRecommendations(pagination.value.current_page)
      }

      const changePage = (page) => {
        if (page >= 1 && page <= pagination.value.last_page) {
          fetchDivisionalRecommendations(page)
        }
      }

      const getRequestTypeLabel = (type) => {
        return requestTypeLabels[type] || type
      }

      const getStatusLabel = (status) => {
        return statusLabels[status] || status
      }

      const getStatusClass = (status) => {
        const statusClasses = {
          pending: 'bg-yellow-600/30 text-yellow-200 border-yellow-400/30',
          pending_hod: 'bg-orange-600/30 text-orange-200 border-orange-400/30',
          hod_approved: 'bg-blue-600/30 text-blue-200 border-blue-400/30',
          pending_divisional: 'bg-purple-600/30 text-purple-200 border-purple-400/30',
          divisional_approved: 'bg-green-600/30 text-green-200 border-green-400/30',
          pending_ict_director: 'bg-indigo-600/30 text-indigo-200 border-indigo-400/30',
          ict_director_approved: 'bg-teal-600/30 text-teal-200 border-teal-400/30',
          implemented: 'bg-green-700/30 text-green-100 border-green-500/30',
          rejected: 'bg-red-600/30 text-red-200 border-red-400/30'
        }
        return statusClasses[status] || 'bg-gray-600/30 text-gray-200 border-gray-400/30'
      }

      const formatDate = (dateString) => {
        if (!dateString) return 'N/A'
        try {
          return new Date(dateString).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
          })
        } catch (error) {
          return dateString
        }
      }

      const hasModules = (request) => {
        return (
          (request.wellsoft_modules_selected && request.wellsoft_modules_selected.length > 0) ||
          (request.jeeva_modules_selected && request.jeeva_modules_selected.length > 0) ||
          (request.internet_purposes && request.internet_purposes.length > 0)
        )
      }

      const viewFullRequest = (requestId) => {
        router.push(`/both-service-form/${requestId}`)
      }

      const respondToRecommendation = (requestId) => {
        // Navigate to HOD approval page for this request
        router.push(`/hod-dashboard/approve-request/${requestId}`)
      }

      const isRejectedByDivisional = (status) => {
        return status === 'divisional_rejected'
      }

      const canResubmitRequest = (status) => {
        return status === 'divisional_rejected'
      }

      const resubmitRequest = (requestId) => {
        // Navigate to resubmission page/modal for this request
        router.push(`/hod-dashboard/resubmit-request/${requestId}`)
      }

      onMounted(() => {
        requireRole([ROLES.HEAD_OF_DEPARTMENT])
        fetchDivisionalRecommendations()
      })

      return {
        loading,
        recommendations,
        pagination,
        refreshRecommendations,
        changePage,
        getRequestTypeLabel,
        getStatusLabel,
        getStatusClass,
        formatDate,
        hasModules,
        viewFullRequest,
        respondToRecommendation,
        isRejectedByDivisional,
        canResubmitRequest,
        resubmitRequest
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

  .medical-card {
    position: relative;
    overflow: hidden;
    background: rgba(59, 130, 246, 0.1);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
  }

  .medical-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(96, 165, 250, 0.2), transparent);
    transition: left 0.5s;
  }

  .medical-card:hover::before {
    left: 100%;
  }

  /* Animations */
  @keyframes float {
    0%,
    100% {
      transform: translateY(0px);
    }
    50% {
      transform: translateY(-20px);
    }
  }

  .animate-float {
    animation: float 6s ease-in-out infinite;
  }
</style>
