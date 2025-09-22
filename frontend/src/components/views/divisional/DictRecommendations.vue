<template>
  <div class="flex flex-col h-screen">
    <Header />
    <div class="flex flex-1 overflow-hidden">
      <ModernSidebar />
      <main
        class="flex-1 p-2 overflow-y-auto relative bg-blue-900"
        style="
          background: linear-gradient(
            135deg,
            #1e3a8a 0%,
            #1e40af 25%,
            #1d4ed8 50%,
            #1e40af 75%,
            #1e3a8a 100%
          );
        "
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

        <div class="max-w-full mx-auto relative z-10 px-4">
          <!-- Header Section -->
          <div class="medical-glass-card rounded-t-3xl p-6 mb-0 border-b border-blue-300/30">
            <div class="flex items-center justify-between">
              <div class="flex items-center">
                <router-link
                  to="/divisional-dashboard"
                  class="mr-4 p-2 rounded-lg bg-teal-600/20 hover:bg-teal-600/30 transition-colors"
                >
                  <i class="fas fa-arrow-left text-teal-300 hover:text-white"></i>
                </router-link>
                <div>
                  <h2 class="text-2xl font-bold text-blue-100 tracking-wide drop-shadow-md">
                    <i class="fas fa-comments text-teal-400 mr-3"></i>
                    Dict Recommendations
                  </h2>
                </div>
              </div>
              <div class="flex items-center space-x-4">
                <button
                  @click="refreshRecommendations"
                  :disabled="loading"
                  class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <i class="fas fa-sync-alt mr-2" :class="{ 'animate-spin': loading }"></i>
                  Refresh
                </button>
              </div>
            </div>
          </div>

          <!-- Search & Filter Controls -->
          <div class="medical-glass-card rounded-none border-t-0 border-b border-blue-300/30">
            <div class="p-4">
              <div
                class="flex flex-col lg:flex-row gap-4 items-start lg:items-center justify-between"
              >
                <!-- Search Section -->
                <div class="flex-1 max-w-xl">
                  <div class="relative">
                    <div
                      class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                    >
                      <i class="fas fa-search text-teal-300"></i>
                    </div>
                    <input
                      v-model="searchQuery"
                      @input="debouncedSearch"
                      type="text"
                      placeholder="Search by staff name, PF number, or request ID..."
                      class="w-full pl-10 pr-4 py-2.5 bg-white/10 border border-teal-300/30 rounded-lg focus:border-teal-400 focus:outline-none text-white placeholder-teal-200/60 backdrop-blur-sm transition-colors"
                    />
                    <button
                      v-if="searchQuery"
                      @click="clearSearch"
                      class="absolute inset-y-0 right-0 pr-3 flex items-center text-teal-300 hover:text-white"
                    >
                      <i class="fas fa-times"></i>
                    </button>
                  </div>
                </div>

                <!-- Filter Controls -->
                <div class="flex flex-wrap gap-3 items-center">
                  <!-- Status Filter -->
                  <div class="relative">
                    <select
                      v-model="statusFilter"
                      @change="applyFilters"
                      class="appearance-none bg-white/10 border border-teal-300/30 rounded-lg px-4 py-2.5 pr-10 text-white text-sm focus:border-teal-400 focus:outline-none backdrop-blur-sm cursor-pointer"
                    >
                      <option value="" class="bg-blue-900 text-white">All Statuses</option>
                      <option value="ict_director_approved" class="bg-blue-900 text-white">
                        Approved by ICT
                      </option>
                      <option value="dict_approved" class="bg-blue-900 text-white">
                        Dict Approved
                      </option>
                      <option value="ict_director_rejected" class="bg-blue-900 text-white">
                        Rejected by ICT
                      </option>
                      <option value="dict_rejected" class="bg-blue-900 text-white">
                        Dict Rejected
                      </option>
                      <option value="pending_ict_director" class="bg-blue-900 text-white">
                        Under ICT Review
                      </option>
                      <option value="implemented" class="bg-blue-900 text-white">
                        Implemented
                      </option>
                    </select>
                    <div
                      class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none"
                    >
                      <i class="fas fa-chevron-down text-teal-300"></i>
                    </div>
                  </div>

                  <!-- Request Type Filter -->
                  <div class="relative">
                    <select
                      v-model="requestTypeFilter"
                      @change="applyFilters"
                      class="appearance-none bg-white/10 border border-teal-300/30 rounded-lg px-4 py-2.5 pr-10 text-white text-sm focus:border-teal-400 focus:outline-none backdrop-blur-sm cursor-pointer"
                    >
                      <option value="" class="bg-blue-900 text-white">All Request Types</option>
                      <option value="jeeva_access" class="bg-blue-900 text-white">
                        Jeeva Access
                      </option>
                      <option value="wellsoft" class="bg-blue-900 text-white">Wellsoft</option>
                      <option value="internet_access_request" class="bg-blue-900 text-white">
                        Internet Access
                      </option>
                    </select>
                    <div
                      class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none"
                    >
                      <i class="fas fa-chevron-down text-teal-300"></i>
                    </div>
                  </div>

                  <!-- Comments Filter -->
                  <div class="relative">
                    <select
                      v-model="commentsFilter"
                      @change="applyFilters"
                      class="appearance-none bg-white/10 border border-teal-300/30 rounded-lg px-4 py-2.5 pr-10 text-white text-sm focus:border-teal-400 focus:outline-none backdrop-blur-sm cursor-pointer"
                    >
                      <option value="" class="bg-blue-900 text-white">All Requests</option>
                      <option value="with_comments" class="bg-blue-900 text-white">
                        With Comments
                      </option>
                      <option value="without_comments" class="bg-blue-900 text-white">
                        Without Comments
                      </option>
                    </select>
                    <div
                      class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none"
                    >
                      <i class="fas fa-chevron-down text-teal-300"></i>
                    </div>
                  </div>

                  <!-- Clear Filters Button -->
                  <button
                    v-if="hasActiveFilters"
                    @click="clearAllFilters"
                    class="px-3 py-2.5 bg-red-600/30 hover:bg-red-600/50 text-red-200 rounded-lg transition-colors border border-red-400/30 text-sm flex items-center"
                    title="Clear all filters"
                  >
                    <i class="fas fa-times mr-1"></i>
                    Clear
                  </button>
                </div>
              </div>

              <!-- Active Filters Display -->
              <div v-if="hasActiveFilters" class="mt-3 flex flex-wrap gap-2">
                <span class="text-sm text-teal-300 mr-2">Active filters:</span>
                <span
                  v-if="statusFilter"
                  class="inline-flex items-center px-2 py-1 bg-green-600/30 text-green-200 text-sm rounded-md border border-green-400/30"
                >
                  Status: {{ getStatusLabel(statusFilter) }}
                  <button @click="clearStatusFilter" class="ml-1 text-green-300 hover:text-white">
                    <i class="fas fa-times"></i>
                  </button>
                </span>
                <span
                  v-if="requestTypeFilter"
                  class="inline-flex items-center px-2 py-1 bg-blue-600/30 text-blue-200 text-sm rounded-md border border-blue-400/30"
                >
                  Type: {{ getRequestTypeLabel(requestTypeFilter) }}
                  <button
                    @click="clearRequestTypeFilter"
                    class="ml-1 text-blue-300 hover:text-white"
                  >
                    <i class="fas fa-times"></i>
                  </button>
                </span>
                <span
                  v-if="commentsFilter"
                  class="inline-flex items-center px-2 py-1 bg-purple-600/30 text-purple-200 text-sm rounded-md border border-purple-400/30"
                >
                  Comments:
                  {{ commentsFilter === 'with_comments' ? 'With Comments' : 'Without Comments' }}
                  <button
                    @click="clearCommentsFilter"
                    class="ml-1 text-purple-300 hover:text-white"
                  >
                    <i class="fas fa-times"></i>
                  </button>
                </span>
                <span
                  v-if="searchQuery"
                  class="inline-flex items-center px-2 py-1 bg-yellow-600/30 text-yellow-200 text-sm rounded-md border border-yellow-400/30"
                >
                  Search: "{{ searchQuery }}"
                  <button @click="clearSearch" class="ml-1 text-yellow-300 hover:text-white">
                    <i class="fas fa-times"></i>
                  </button>
                </span>
              </div>
            </div>
          </div>

          <!-- Results Summary -->
          <div
            v-if="!loading"
            class="medical-glass-card rounded-none border-t-0 border-b border-blue-300/30"
          >
            <div class="px-4 py-2 bg-blue-600/10">
              <div class="flex items-center justify-between text-base">
                <div class="text-teal-300">
                  <i class="fas fa-list-ul mr-1"></i>
                  Showing {{ filteredRecommendations.length }} of
                  {{ totalRecommendations }} recommendations
                  <span v-if="hasActiveFilters" class="text-yellow-300 ml-2">
                    <i class="fas fa-filter mr-1"></i>Filtered
                  </span>
                </div>
                <div v-if="filteredRecommendations.length > 0" class="text-teal-300">
                  <i class="fas fa-sort mr-1"></i>
                  Sorted by date (newest first)
                </div>
              </div>
            </div>
          </div>

          <!-- Main Content -->
          <div class="medical-glass-card rounded-b-3xl overflow-hidden">
            <div class="p-6">
              <!-- Loading State -->
              <div v-if="loading && recommendations.length === 0" class="text-center py-12">
                <i class="fas fa-spinner fa-spin text-4xl text-teal-400 mb-4"></i>
                <p class="text-white text-lg">Loading dict recommendations...</p>
              </div>

              <!-- Empty State -->
              <div
                v-else-if="!loading && filteredRecommendations.length === 0 && !hasActiveFilters"
                class="text-center py-12"
              >
                <div class="mb-6">
                  <i class="fas fa-comment-slash text-6xl text-gray-400 mb-4"></i>
                </div>
                <h3 class="text-xl font-semibold text-white mb-2">No Recommendations Found</h3>
                <p class="text-gray-300 max-w-md mx-auto">
                  There are currently no recommendations, rejection reasons, or comments from ICT
                  Director for requests in your department.
                </p>
                <button
                  @click="refreshRecommendations"
                  class="mt-6 px-6 py-3 bg-teal-600 hover:bg-teal-700 text-white rounded-lg transition-colors"
                >
                  <i class="fas fa-sync-alt mr-2"></i>
                  Check Again
                </button>
              </div>

              <!-- No Results After Filtering -->
              <div
                v-else-if="!loading && filteredRecommendations.length === 0 && hasActiveFilters"
                class="text-center py-12"
              >
                <div class="mb-6">
                  <i class="fas fa-search text-6xl text-gray-400 mb-4"></i>
                </div>
                <h3 class="text-xl font-semibold text-white mb-2">No Results Found</h3>
                <p class="text-gray-300 max-w-md mx-auto mb-4">
                  No recommendations match your current search and filter criteria.
                </p>
                <div class="space-y-2">
                  <p class="text-sm text-teal-300">Try:</p>
                  <ul class="text-sm text-gray-400 space-y-1 max-w-sm mx-auto">
                    <li>• Clearing your search terms</li>
                    <li>• Adjusting your filters</li>
                    <li>• Checking different status options</li>
                  </ul>
                </div>
                <button
                  @click="clearAllFilters"
                  class="mt-6 px-6 py-3 bg-red-600/30 hover:bg-red-600/50 text-red-200 rounded-lg transition-colors border border-red-400/30"
                >
                  <i class="fas fa-times mr-2"></i>
                  Clear All Filters
                </button>
              </div>

              <!-- Recommendations Table -->
              <div v-else class="overflow-hidden rounded-xl border border-blue-300/20">
                <div class="overflow-x-auto">
                  <table class="w-full">
                    <!-- Table Header -->
                    <thead
                      class="bg-gradient-to-r from-blue-600/20 to-indigo-600/20 border-b border-blue-300/20"
                    >
                      <tr>
                        <th
                          class="px-6 py-4 text-left text-sm font-semibold text-blue-200 uppercase tracking-wider"
                        >
                          <i class="fas fa-hashtag mr-2"></i>Request ID
                        </th>
                        <th
                          class="px-6 py-4 text-left text-sm font-semibold text-blue-200 uppercase tracking-wider"
                        >
                          <i class="fas fa-user mr-2"></i>Staff Details
                        </th>
                        <th
                          class="px-6 py-4 text-left text-sm font-semibold text-blue-200 uppercase tracking-wider"
                        >
                          <i class="fas fa-layer-group mr-2"></i>Request Type
                        </th>
                        <th
                          class="px-6 py-4 text-left text-sm font-semibold text-blue-200 uppercase tracking-wider"
                        >
                          <i class="fas fa-info-circle mr-2"></i>Status
                        </th>
                        <th
                          class="px-6 py-4 text-left text-sm font-semibold text-blue-200 uppercase tracking-wider"
                        >
                          <i class="fas fa-calendar mr-2"></i>Date
                        </th>
                        <th
                          class="px-6 py-4 text-left text-sm font-semibold text-blue-200 uppercase tracking-wider"
                        >
                          <i class="fas fa-comment mr-2"></i>Has Comments
                        </th>
                        <th
                          class="px-6 py-4 text-center text-sm font-semibold text-blue-200 uppercase tracking-wider"
                        >
                          <i class="fas fa-cogs mr-2"></i>Actions
                        </th>
                      </tr>
                    </thead>

                    <!-- Table Body -->
                    <tbody class="divide-y divide-blue-300/10">
                      <tr
                        v-for="(recommendation, index) in filteredRecommendations"
                        :key="recommendation.id"
                        :class="[
                          'hover:bg-blue-600/10 transition-colors duration-200',
                          index % 2 === 0 ? 'bg-blue-950/20' : 'bg-blue-950/10'
                        ]"
                      >
                        <!-- Request ID -->
                        <td class="px-6 py-4 whitespace-nowrap">
                          <div class="flex items-center">
                            <div
                              class="w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center mr-3"
                            >
                              <i class="fas fa-file-alt text-white text-sm"></i>
                            </div>
                            <div class="text-base font-medium text-white">
                              {{ formatRequestId(recommendation.id) }}
                            </div>
                          </div>
                        </td>

                        <!-- Staff Details -->
                        <td class="px-6 py-4">
                          <div class="text-base font-medium text-white">
                            {{ recommendation.staff_name }}
                          </div>
                          <div class="text-sm text-blue-300">{{ recommendation.pf_number }}</div>
                        </td>

                        <!-- Request Type -->
                        <td class="px-6 py-4">
                          <div class="flex flex-wrap gap-1">
                            <span
                              v-for="type in recommendation.request_type"
                              :key="type"
                              class="inline-flex px-2 py-1 text-sm rounded-md bg-blue-600/30 text-blue-200 border border-blue-400/20"
                            >
                              {{ getRequestTypeLabel(type) }}
                            </span>
                          </div>
                        </td>

                        <!-- Status -->
                        <td class="px-6 py-4 whitespace-nowrap">
                          <span
                            class="inline-flex px-2 py-1 text-sm font-medium rounded-md border"
                            :class="getStatusClass(recommendation.status)"
                          >
                            {{ getStatusLabel(recommendation.status) }}
                          </span>
                        </td>

                        <!-- Date -->
                        <td class="px-6 py-4 whitespace-nowrap text-base text-gray-300">
                          {{
                            formatDate(
                              recommendation.ict_director_comments_date ||
                                recommendation.dict_rejection_date ||
                                recommendation.updated_at
                            )
                          }}
                        </td>

                        <!-- Has Comments -->
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                          <div
                            v-if="
                              recommendation.ict_director_comments ||
                              recommendation.ict_director_rejection_reasons
                            "
                            class="inline-flex items-center"
                          >
                            <div
                              class="w-2 h-2 rounded-full mr-2"
                              :class="
                                recommendation.ict_director_status === 'rejected'
                                  ? 'bg-red-500'
                                  : 'bg-green-500'
                              "
                            ></div>
                            <span
                              class="text-sm font-medium"
                              :class="
                                recommendation.ict_director_status === 'rejected'
                                  ? 'text-red-400'
                                  : 'text-green-400'
                              "
                            >
                              {{
                                recommendation.ict_director_status === 'rejected'
                                  ? 'Rejected'
                                  : 'Yes'
                              }}
                            </span>
                          </div>
                          <div v-else class="inline-flex items-center">
                            <div class="w-2 h-2 bg-gray-500 rounded-full mr-2"></div>
                            <span class="text-sm text-gray-400">No</span>
                          </div>
                        </td>

                        <!-- Actions -->
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                          <div class="flex items-center justify-center space-x-2">
                            <button
                              @click="viewRequestDetails(recommendation)"
                              class="inline-flex items-center px-3 py-1.5 text-sm font-medium rounded-md bg-blue-600/30 text-blue-200 border border-blue-400/30 hover:bg-blue-600/50 transition-colors duration-200"
                              :title="`View details for ${formatRequestId(recommendation.id)}`"
                            >
                              <i class="fas fa-eye mr-1"></i>
                              View
                            </button>
                            <button
                              @click="openResponseModal(recommendation)"
                              class="inline-flex items-center px-3 py-1.5 text-sm font-medium rounded-md bg-green-600/30 text-green-200 border border-green-400/30 hover:bg-green-600/50 transition-colors duration-200"
                              :title="`Respond to ${formatRequestId(recommendation.id)}`"
                            >
                              <i class="fas fa-reply mr-1"></i>
                              Respond
                            </button>
                          </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
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
                  <span class="text-base text-white mx-4">
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

    <!-- Request Details Modal -->
    <div
      v-if="showDetailsModal"
      class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 z-50"
      @click="closeDetailsModal"
    >
      <div
        class="backdrop-blur-md rounded-2xl border-2 border-blue-400/30 max-w-4xl w-full max-h-[90vh] overflow-y-auto shadow-2xl"
        style="
          background: linear-gradient(
            135deg,
            #1e3a8a 0%,
            #1e40af 25%,
            #1d4ed8 50%,
            #1e40af 75%,
            #1e3a8a 100%
          );
        "
        @click.stop
      >
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-blue-300/20">
          <div class="flex items-center space-x-3">
            <div
              class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center"
            >
              <i class="fas fa-file-alt text-white"></i>
            </div>
            <div>
              <h3 class="text-xl font-bold text-white">
                {{ formatRequestId(selectedRequest?.id) }}
              </h3>
              <p class="text-sm text-blue-300">
                {{ selectedRequest?.staff_name }} ({{ selectedRequest?.pf_number }})
              </p>
            </div>
          </div>
          <button
            @click="closeDetailsModal"
            class="p-2 rounded-lg bg-red-600/20 hover:bg-red-600/30 text-red-300 hover:text-white transition-colors"
          >
            <i class="fas fa-times"></i>
          </button>
        </div>

        <!-- Modal Content -->
        <div class="p-6 space-y-6">
          <!-- Request Overview -->
          <div class="bg-blue-600/10 border border-blue-400/20 rounded-lg p-4">
            <h4 class="font-semibold text-blue-200 mb-3 flex items-center">
              <i class="fas fa-info-circle mr-2"></i>
              Request Overview
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
              <div class="space-y-2">
                <div class="flex justify-between">
                  <span class="text-gray-300">Status:</span>
                  <span
                    class="px-2 py-1 text-sm rounded-md border"
                    :class="getStatusClass(selectedRequest?.status)"
                  >
                    {{ getStatusLabel(selectedRequest?.status) }}
                  </span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-300">Access Type:</span>
                  <span class="text-white font-medium">{{
                    selectedRequest?.access_type || 'N/A'
                  }}</span>
                </div>
                <div v-if="selectedRequest?.temporary_until" class="flex justify-between">
                  <span class="text-gray-300">Valid Until:</span>
                  <span class="text-white font-medium">{{
                    formatDate(selectedRequest?.temporary_until)
                  }}</span>
                </div>
              </div>
              <div class="space-y-2">
                <div class="flex justify-between">
                  <span class="text-gray-300">Purpose:</span>
                  <span class="text-white font-medium">{{
                    selectedRequest?.module_requested_for || 'Use'
                  }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-300">Request Types:</span>
                  <div class="flex flex-wrap gap-1">
                    <span
                      v-for="type in selectedRequest?.request_type"
                      :key="type"
                      class="px-2 py-1 text-sm rounded-md bg-blue-600/30 text-blue-200 border border-blue-400/20"
                    >
                      {{ getRequestTypeLabel(type) }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- ICT Director Comments/Rejection Reasons -->
          <div
            v-if="
              selectedRequest?.ict_director_comments ||
              selectedRequest?.ict_director_rejection_reasons
            "
            class="border rounded-lg p-4"
            :class="
              selectedRequest?.ict_director_status === 'rejected'
                ? 'bg-gradient-to-r from-red-600/20 to-red-700/20 border-red-400/30'
                : 'bg-gradient-to-r from-teal-600/20 to-cyan-600/20 border border-teal-400/30'
            "
          >
            <div class="flex items-start space-x-3">
              <div
                class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0"
                :class="
                  selectedRequest?.ict_director_status === 'rejected'
                    ? 'bg-gradient-to-br from-red-500 to-red-600'
                    : 'bg-gradient-to-br from-teal-500 to-cyan-600'
                "
              >
                <i
                  class="text-white"
                  :class="
                    selectedRequest?.ict_director_status === 'rejected'
                      ? 'fas fa-times-circle'
                      : 'fas fa-user-tie'
                  "
                ></i>
              </div>
              <div class="flex-1">
                <div class="flex items-center space-x-2 mb-3">
                  <h4
                    class="font-semibold"
                    :class="
                      selectedRequest?.ict_director_status === 'rejected'
                        ? 'text-red-200'
                        : 'text-teal-200'
                    "
                  >
                    ICT Director
                    {{
                      selectedRequest?.ict_director_status === 'rejected'
                        ? 'Rejection Reasons'
                        : 'Comments'
                    }}
                  </h4>
                  <span
                    class="text-sm"
                    :class="
                      selectedRequest?.ict_director_status === 'rejected'
                        ? 'text-red-300'
                        : 'text-teal-300'
                    "
                  >
                    {{ selectedRequest?.ict_director_name || 'ICT Director' }}
                  </span>
                </div>
                <div
                  class="rounded-lg p-3 mb-3"
                  :class="
                    selectedRequest?.ict_director_status === 'rejected'
                      ? 'bg-red-900/20'
                      : 'bg-teal-900/20'
                  "
                >
                  <p class="text-white leading-relaxed whitespace-pre-line">
                    <span v-if="selectedRequest?.ict_director_status === 'rejected'">
                      {{
                        selectedRequest?.ict_director_rejection_reasons ||
                        'No rejection reasons provided'
                      }}
                    </span>
                    <span v-else>
                      {{ selectedRequest?.ict_director_comments || 'No comments provided' }}
                    </span>
                  </p>
                </div>
                <div class="text-sm text-teal-300">
                  <i class="fas fa-calendar mr-1"></i>
                  {{
                    formatDate(
                      selectedRequest?.ict_director_comments_date ||
                        selectedRequest?.dict_rejection_date ||
                        selectedRequest?.updated_at
                    )
                  }}
                </div>
              </div>
            </div>
          </div>

          <!-- Modules/Services Requested -->
          <div
            v-if="hasModules(selectedRequest)"
            class="bg-purple-600/10 border border-purple-400/20 rounded-lg p-4"
          >
            <h4 class="font-semibold text-purple-200 mb-3 flex items-center">
              <i class="fas fa-cube mr-2"></i>
              Modules & Services Requested
            </h4>
            <div class="space-y-3">
              <div v-if="selectedRequest?.wellsoft_modules_selected?.length" class="space-y-2">
                <span class="text-base text-gray-300 font-medium">Wellsoft Modules:</span>
                <div class="flex flex-wrap gap-2">
                  <span
                    v-for="module in selectedRequest.wellsoft_modules_selected"
                    :key="module"
                    class="px-3 py-1 bg-yellow-600/30 text-yellow-200 text-sm rounded-lg border border-yellow-400/30"
                  >
                    {{ module }}
                  </span>
                </div>
              </div>
              <div v-if="selectedRequest?.jeeva_modules_selected?.length" class="space-y-2">
                <span class="text-base text-gray-300 font-medium">Jeeva Modules:</span>
                <div class="flex flex-wrap gap-2">
                  <span
                    v-for="module in selectedRequest.jeeva_modules_selected"
                    :key="module"
                    class="px-3 py-1 bg-purple-600/30 text-purple-200 text-sm rounded-lg border border-purple-400/30"
                  >
                    {{ module }}
                  </span>
                </div>
              </div>
              <div v-if="selectedRequest?.internet_purposes?.length" class="space-y-2">
                <span class="text-base text-gray-300 font-medium">Internet Access Purposes:</span>
                <div class="flex flex-wrap gap-2">
                  <span
                    v-for="purpose in selectedRequest.internet_purposes"
                    :key="purpose"
                    class="px-3 py-1 bg-green-600/30 text-green-200 text-sm rounded-lg border border-green-400/30"
                  >
                    {{ purpose }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Response Modal -->
    <div
      v-if="showResponseModal"
      class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 z-50"
      @click="closeResponseModal"
    >
      <div
        class="backdrop-blur-md rounded-2xl border-2 border-blue-400/30 max-w-2xl w-full shadow-2xl"
        style="
          background: linear-gradient(
            135deg,
            #1e3a8a 0%,
            #1e40af 25%,
            #1d4ed8 50%,
            #1e40af 75%,
            #1e3a8a 100%
          );
        "
        @click.stop
      >
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-blue-300/20">
          <div class="flex items-center space-x-3">
            <div
              class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center"
            >
              <i class="fas fa-reply text-white"></i>
            </div>
            <div>
              <h3 class="text-xl font-bold text-white">Respond to DICT</h3>
              <p class="text-sm text-green-300">
                {{ formatRequestId(selectedRequest?.id) }} - {{ selectedRequest?.staff_name }}
              </p>
            </div>
          </div>
          <button
            @click="closeResponseModal"
            class="p-2 rounded-lg bg-red-600/20 hover:bg-red-600/30 text-red-300 hover:text-white transition-colors"
          >
            <i class="fas fa-times"></i>
          </button>
        </div>

        <!-- Modal Content -->
        <div class="p-6 space-y-4">
          <!-- Previous Response (if any) -->
          <div
            v-if="selectedRequest?.divisional_response_to_dict"
            class="bg-green-600/20 border border-green-500/30 rounded-lg p-4"
          >
            <h4 class="text-sm font-semibold text-green-300 mb-2">Your Previous Response:</h4>
            <p class="text-white text-base leading-relaxed">
              {{ selectedRequest.divisional_response_to_dict }}
            </p>
            <div v-if="selectedRequest.divisional_response_date" class="mt-2">
              <span class="text-sm text-green-300">
                <i class="fas fa-clock mr-1"></i>
                Sent: {{ formatDate(selectedRequest.divisional_response_date) }}
              </span>
            </div>
          </div>

          <!-- Response Form -->
          <div class="space-y-4">
            <div>
              <label class="block text-base font-medium text-green-200 mb-2">
                Your Response to ICT Director
              </label>
              <textarea
                v-model="responseText"
                rows="6"
                class="w-full px-4 py-3 bg-white/10 border border-green-400/30 rounded-lg focus:border-green-400 focus:outline-none text-white placeholder-green-200/60 backdrop-blur-sm resize-none"
                :placeholder="`Write your response to DICT regarding Request #${selectedRequest?.id}...`"
              ></textarea>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-3 pt-4">
              <button
                @click="closeResponseModal"
                class="px-4 py-2 bg-gray-600/30 hover:bg-gray-600/50 text-gray-200 text-sm rounded-lg transition-colors border border-gray-400/30"
              >
                Cancel
              </button>
              <button
                @click="submitModalResponse"
                :disabled="!responseText.trim() || submittingModalResponse"
                class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center"
              >
                <i v-if="submittingModalResponse" class="fas fa-spinner fa-spin mr-2"></i>
                <i v-else class="fas fa-paper-plane mr-2"></i>
                {{ submittingModalResponse ? 'Sending...' : 'Send Response' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import { ref, reactive, computed, onMounted } from 'vue'
  import { useRouter } from 'vue-router'
  import Header from '@/components/header.vue'
  import ModernSidebar from '@/components/ModernSidebar.vue'
  import { useAuth } from '@/composables/useAuth'
  import dictAccessService from '@/services/dictAccessService'

  export default {
    name: 'DictRecommendations',
    components: {
      Header,
      ModernSidebar
    },
    setup() {
      const router = useRouter()
      const { requireRole, ROLES } = useAuth()

      const loading = ref(false)
      const recommendations = ref([])
      const commentForms = reactive({})
      const submittingResponse = reactive({})
      const pagination = ref({
        current_page: 1,
        last_page: 1,
        per_page: 10,
        total: 0
      })

      // Search and filter state
      const searchQuery = ref('')
      const statusFilter = ref('')
      const requestTypeFilter = ref('')
      const commentsFilter = ref('')
      const debounceTimeout = ref(null)

      // Modal state
      const showDetailsModal = ref(false)
      const showResponseModal = ref(false)
      const selectedRequest = ref(null)
      const responseText = ref('')
      const submittingModalResponse = ref(false)

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
        ict_director_rejected: 'DICT Rejected',
        dict_rejected: 'DICT Rejected',
        implemented: 'Implemented',
        rejected: 'Rejected'
      }

      // Computed properties for filtering
      const totalRecommendations = computed(() => recommendations.value.length)

      const filteredRecommendations = computed(() => {
        let filtered = [...recommendations.value]

        // Apply search filter
        if (searchQuery.value) {
          const query = searchQuery.value.toLowerCase().trim()
          filtered = filtered.filter((recommendation) => {
            return (
              (recommendation.staff_name || '').toLowerCase().includes(query) ||
              (recommendation.pf_number || '').toLowerCase().includes(query) ||
              (recommendation.id || '').toString().includes(query) ||
              `#${recommendation.id}`.toLowerCase().includes(query) ||
              formatRequestId(recommendation.id).toLowerCase().includes(query)
            )
          })
        }

        // Apply status filter
        if (statusFilter.value) {
          filtered = filtered.filter(
            (recommendation) => recommendation.status === statusFilter.value
          )
        }

        // Apply request type filter
        if (requestTypeFilter.value) {
          filtered = filtered.filter((recommendation) => {
            const requestTypes = Array.isArray(recommendation.request_type)
              ? recommendation.request_type
              : [recommendation.request_type]
            return requestTypes.includes(requestTypeFilter.value)
          })
        }

        // Apply comments filter
        if (commentsFilter.value === 'with_comments') {
          filtered = filtered.filter(
            (recommendation) =>
              recommendation.ict_director_comments &&
              recommendation.ict_director_comments.trim() !== ''
          )
        } else if (commentsFilter.value === 'without_comments') {
          filtered = filtered.filter(
            (recommendation) =>
              !recommendation.ict_director_comments ||
              recommendation.ict_director_comments.trim() === ''
          )
        }

        // Sort by date (newest first)
        return filtered.sort((a, b) => {
          const dateA = new Date(
            a.ict_director_comments_date || a.dict_rejection_date || a.updated_at || 0
          )
          const dateB = new Date(
            b.ict_director_comments_date || b.dict_rejection_date || b.updated_at || 0
          )
          return dateB - dateA
        })
      })

      const hasActiveFilters = computed(() => {
        return !!(
          searchQuery.value ||
          statusFilter.value ||
          requestTypeFilter.value ||
          commentsFilter.value
        )
      })

      // Search and filter methods
      const debouncedSearch = () => {
        if (debounceTimeout.value) {
          clearTimeout(debounceTimeout.value)
        }
        debounceTimeout.value = setTimeout(() => {
          // Search triggers API call for server-side filtering
          console.log('Search applied:', searchQuery.value)
          fetchDictRecommendations(1)
        }, 300)
      }

      const applyFilters = () => {
        // Filters trigger API call for server-side filtering
        console.log('Filters applied:', {
          status: statusFilter.value,
          requestType: requestTypeFilter.value,
          comments: commentsFilter.value
        })
        // Refresh data with new filters
        fetchDictRecommendations(1)
      }

      const clearSearch = () => {
        searchQuery.value = ''
        // Refresh data after clearing search
        fetchDictRecommendations(1)
      }

      const clearAllFilters = () => {
        searchQuery.value = ''
        statusFilter.value = ''
        requestTypeFilter.value = ''
        commentsFilter.value = ''
        // Refresh data after clearing filters
        fetchDictRecommendations(1)
      }

      const clearStatusFilter = () => {
        statusFilter.value = ''
        applyFilters()
      }

      const clearRequestTypeFilter = () => {
        requestTypeFilter.value = ''
        applyFilters()
      }

      const clearCommentsFilter = () => {
        commentsFilter.value = ''
        applyFilters()
      }

      const fetchDictRecommendations = async (page = 1) => {
        try {
          loading.value = true
          console.log('🔄 Fetching dict recommendations using dictAccessService...')

          // Build parameters object with search and filter values
          const params = {
            page,
            per_page: 10
          }

          // Add search parameter if provided
          if (searchQuery.value && searchQuery.value.trim()) {
            params.search = searchQuery.value.trim()
          }

          // Add filter parameters if provided
          if (statusFilter.value) {
            params.status = statusFilter.value
          }

          if (requestTypeFilter.value) {
            params.request_type = requestTypeFilter.value
          }

          if (commentsFilter.value) {
            if (commentsFilter.value === 'with_comments') {
              params.has_comments = true
            } else if (commentsFilter.value === 'without_comments') {
              params.has_comments = false
            }
          }

          console.log('📋 Request parameters:', params)
          const result = await dictAccessService.getDictRecommendations(params)

          if (!result.success) {
            console.log('🔍 API calls failed, using mock data for demo...')
            // Mock data for demonstration when all API calls fail
            result.data = {
              success: true,
              data: [
                {
                  id: 1,
                  staff_name: 'John Doe',
                  pf_number: 'PF12345',
                  request_type: ['wellsoft', 'jeeva_access'],
                  status: 'dict_rejected',
                  dict_rejection_reason: null, // Not used anymore
                  dict_rejection_date: null,
                  dict_name: null,
                  ict_director_comments:
                    'REJECTION: The requested Wellsoft modules require additional security clearance. Please provide updated documentation before proceeding. The HR Management module access needs department head approval.',
                  ict_director_name: 'Dr. Smith (ICT Director)',
                  ict_director_comments_date: '2024-01-14',
                  wellsoft_modules_selected: ['Financial Management', 'HR Management'],
                  jeeva_modules_selected: ['Patient Records', 'Lab Results'],
                  access_type: 'permanent',
                  module_requested_for: 'use',
                  divisional_response_to_dict: null,
                  divisional_response_date: null
                },
                {
                  id: 2,
                  staff_name: 'Jane Smith',
                  pf_number: 'PF67890',
                  request_type: ['internet_access_request'],
                  status: 'ict_director_approved',
                  dict_rejection_reason: null,
                  dict_rejection_date: null,
                  dict_name: null,
                  ict_director_comments:
                    'APPROVED: Standard internet access granted for research and email communication. RECOMMENDATION: Monitor usage for compliance with hospital policies and ensure VPN access is configured for remote work.',
                  ict_director_name: 'Dr. Johnson (ICT Director)',
                  ict_director_comments_date: '2024-01-16',
                  internet_purposes: ['Research', 'Email Communication'],
                  access_type: 'temporary',
                  temporary_until: '2024-06-15',
                  module_requested_for: 'use',
                  divisional_response_to_dict:
                    'Thank you for the approval. We will monitor usage closely.',
                  divisional_response_date: '2024-01-17'
                }
              ],
              pagination: {
                current_page: 1,
                last_page: 1,
                per_page: 10,
                total: 2
              }
            }
          }

          const response = { data: result.data }

          console.log('📡 API Response:', response)
          console.log('📊 Response Data:', response.data)

          if (response.data.success) {
            console.log('✅ API call successful, data:', response.data.data)
            recommendations.value = response.data.data
            pagination.value = response.data.pagination || {
              current_page: 1,
              last_page: 1,
              per_page: 10,
              total: response.data.data.length
            }

            // Initialize comment forms for each recommendation
            response.data.data.forEach((rec) => {
              if (!commentForms[rec.id]) {
                commentForms[rec.id] = ''
              }
              submittingResponse[rec.id] = false
            })
          } else {
            console.error('❌ API returned error:', response.data.message)
          }
        } catch (error) {
          console.error('💥 Error fetching dict recommendations:', error)
          console.error('Response status:', error.response?.status)
          console.error('Response data:', error.response?.data)
          recommendations.value = []
        } finally {
          loading.value = false
        }
      }

      const refreshRecommendations = () => {
        fetchDictRecommendations(pagination.value.current_page)
      }

      const changePage = (page) => {
        if (page >= 1 && page <= pagination.value.last_page) {
          fetchDictRecommendations(page)
        }
      }

      const submitResponse = async (requestId) => {
        if (!commentForms[requestId] || submittingResponse[requestId]) {
          return
        }

        try {
          submittingResponse[requestId] = true
          console.log('📝 Attempting to send divisional response...', {
            requestId,
            comment: commentForms[requestId]
          })

          const result = await dictAccessService.submitDivisionalResponse(requestId, {
            response: commentForms[requestId],
            divisional_response_date: new Date().toISOString()
          })

          console.log('📡 Response result:', result)

          if (result.success) {
            // Clear the form
            commentForms[requestId] = ''

            // Show success message (you can implement a toast notification here)
            alert('Response sent successfully!')

            // Refresh the recommendations to show updated data
            refreshRecommendations()
          } else {
            console.warn('⚠️ Response submission failed:', result.error)
            alert(
              'Failed to send response: ' +
                (result.error || 'Unknown error') +
                '\n\nNote: This is expected since the backend endpoint is not implemented yet.'
            )
          }
        } catch (error) {
          console.error('💥 Error sending response:', error)
          console.error('Error details:', {
            message: error.message,
            stack: error.stack,
            requestId,
            comment: commentForms[requestId]
          })
          alert(
            'Failed to send response. Please try again.\n\nError: ' +
              error.message +
              '\n\nNote: This is expected if backend is not implemented yet.'
          )
        } finally {
          submittingResponse[requestId] = false
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
          ict_director_rejected: 'bg-red-600/30 text-red-200 border-red-400/30',
          dict_rejected: 'bg-red-600/30 text-red-200 border-red-400/30',
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

      const formatRequestId = (id) => {
        if (!id) return 'N/A'
        return `REQ-${id.toString().padStart(6, '0')}`
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

      const isRejectedByDict = (status) => {
        return status === 'ict_director_rejected' || status === 'dict_rejected'
      }

      const respondToRecommendation = (requestId) => {
        // This functionality is already handled by the existing response form in the template
        // Focus on the comment form for the specific request
        console.log('Responding to recommendation for request:', requestId)
      }

      // Modal control functions
      const viewRequestDetails = (request) => {
        selectedRequest.value = request
        showDetailsModal.value = true
      }

      const closeDetailsModal = () => {
        showDetailsModal.value = false
        selectedRequest.value = null
      }

      const openResponseModal = (request) => {
        selectedRequest.value = request
        responseText.value = commentForms[request.id] || ''
        showResponseModal.value = true
      }

      const closeResponseModal = () => {
        showResponseModal.value = false
        selectedRequest.value = null
        responseText.value = ''
      }

      const submitModalResponse = async () => {
        if (!responseText.value.trim() || submittingModalResponse.value || !selectedRequest.value) {
          return
        }

        try {
          submittingModalResponse.value = true
          console.log('📝 Attempting to send divisional response from modal...', {
            requestId: selectedRequest.value.id,
            comment: responseText.value
          })

          const result = await dictAccessService.submitDivisionalResponse(
            selectedRequest.value.id,
            {
              response: responseText.value,
              divisional_response_date: new Date().toISOString()
            }
          )

          console.log('📡 Response result:', result)

          if (result.success) {
            // Clear the form
            responseText.value = ''
            commentForms[selectedRequest.value.id] = ''

            // Show success message
            alert('Response sent successfully!')

            // Close modal and refresh
            closeResponseModal()
            refreshRecommendations()
          } else {
            console.warn('⚠️ Response submission failed:', result.error)
            alert(
              'Failed to send response: ' +
                (result.error || 'Unknown error') +
                '\n\nNote: This is expected since the backend endpoint is not implemented yet.'
            )
          }
        } catch (error) {
          console.error('💥 Error sending response:', error)
          alert(
            'Failed to send response. Please try again.\n\nError: ' +
              error.message +
              '\n\nNote: This is expected if backend is not implemented yet.'
          )
        } finally {
          submittingModalResponse.value = false
        }
      }

      onMounted(() => {
        requireRole([ROLES.DIVISIONAL_DIRECTOR])
        console.log('🔍 Debugging dictAccessService availability:', {
          dictAccessService,
          hasSubmitMethod: typeof dictAccessService.submitDivisionalResponse === 'function'
        })
        fetchDictRecommendations()
      })

      return {
        loading,
        recommendations,
        pagination,
        commentForms,
        submittingResponse,
        refreshRecommendations,
        changePage,
        submitResponse,
        getRequestTypeLabel,
        getStatusLabel,
        getStatusClass,
        formatDate,
        formatRequestId,
        hasModules,
        viewFullRequest,
        isRejectedByDict,
        respondToRecommendation,
        // Search and filter functionality
        searchQuery,
        statusFilter,
        requestTypeFilter,
        commentsFilter,
        filteredRecommendations,
        totalRecommendations,
        hasActiveFilters,
        debouncedSearch,
        applyFilters,
        clearSearch,
        clearAllFilters,
        clearStatusFilter,
        clearRequestTypeFilter,
        clearCommentsFilter,
        // Modal functionality
        showDetailsModal,
        showResponseModal,
        selectedRequest,
        responseText,
        submittingModalResponse,
        viewRequestDetails,
        closeDetailsModal,
        openResponseModal,
        closeResponseModal,
        submitModalResponse
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

  /* Enhanced Table Styling */
  table {
    border-collapse: separate;
    border-spacing: 0;
  }

  table th {
    position: sticky;
    top: 0;
    background: rgba(37, 99, 235, 0.2);
    backdrop-filter: blur(10px);
    z-index: 10;
  }

  table th:first-child {
    border-top-left-radius: 0.75rem;
  }

  table th:last-child {
    border-top-right-radius: 0.75rem;
  }

  table tr:last-child td:first-child {
    border-bottom-left-radius: 0.75rem;
  }

  table tr:last-child td:last-child {
    border-bottom-right-radius: 0.75rem;
  }

  /* Responsive table adjustments */
  @media (max-width: 768px) {
    .overflow-x-auto {
      scrollbar-width: thin;
    }

    table th,
    table td {
      padding: 0.75rem 0.5rem;
      font-size: 0.75rem;
    }

    .flex.flex-wrap {
      flex-direction: column;
      gap: 0.25rem;
    }
  }

  /* Search and filter responsive adjustments */
  @media (max-width: 1024px) {
    .lg\:flex-row {
      flex-direction: column;
      align-items: stretch;
    }

    .lg\:items-center {
      align-items: stretch;
    }

    .flex-wrap {
      flex-direction: column;
    }

    .max-w-md {
      max-width: 100%;
    }
  }

  /* Enhanced filter styling */
  select {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
    background-position: right 0.5rem center;
    background-repeat: no-repeat;
    background-size: 1.5em 1.5em;
  }

  /* Focus states */
  input:focus,
  select:focus {
    box-shadow: 0 0 0 3px rgba(20, 184, 166, 0.1);
    transform: translateY(-1px);
  }

  /* Filter chips animation */
  .inline-flex {
    transition: all 0.2s ease;
  }

  .inline-flex:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  }

  /* Modal backdrop styling */
  .fixed.inset-0 {
    backdrop-filter: blur(8px);
  }
</style>
