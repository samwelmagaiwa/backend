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
          <!-- Back Button Only -->
          <div class="medical-glass-card rounded-t-3xl p-4 mb-0 border-b border-blue-300/30">
            <router-link
              to="/head_of_it-dashboard"
              class="p-2 rounded-lg bg-teal-600/20 hover:bg-teal-600/30 transition-colors"
            >
              <i class="fas fa-arrow-left text-teal-300 hover:text-white"></i>
            </router-link>
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
                        ICT Director Approved
                      </option>
                      <option value="ict_director_rejected" class="bg-blue-900 text-white">
                        ICT Director Rejected
                      </option>
                      <option value="pending_head_it" class="bg-blue-900 text-white">
                        Pending Head IT
                      </option>
                      <option value="head_it_approved" class="bg-blue-900 text-white">
                        Head IT Approved
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

                  <!-- Refresh Button -->
                  <button
                    @click="refreshRecommendations"
                    :disabled="loading"
                    class="px-4 py-2.5 bg-teal-600 hover:bg-teal-700 text-white rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed text-sm flex items-center"
                    title="Refresh recommendations list"
                  >
                    <i class="fas fa-sync-alt mr-2" :class="{ 'animate-spin': loading }"></i>
                    Refresh
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

          <!-- Main Content -->
          <div class="medical-glass-card rounded-b-3xl overflow-hidden">
            <div class="p-6">
              <!-- Loading State -->
              <div v-if="loading && recommendations.length === 0" class="text-center py-12">
                <i class="fas fa-spinner fa-spin text-4xl text-teal-400 mb-4"></i>
                <p class="text-white text-lg">Loading ICT Director recommendations...</p>
              </div>

              <!-- Empty State -->
              <div
                v-else-if="!loading && filteredRecommendations.length === 0 && !hasActiveFilters"
                class="text-center py-12"
              >
                <div class="mb-6">
                  <i class="fas fa-book-open text-6xl text-gray-400 mb-4"></i>
                </div>
                <h3 class="text-xl font-semibold text-white mb-2">No Recommendations Found</h3>
                <p class="text-gray-300 max-w-md mx-auto">
                  There are currently no recommendations, rejection reasons, or comments from ICT
                  Director for requests across all departments.
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
                          <i class="fas fa-building mr-2"></i>Department
                        </th>
                        <th
                          class="px-6 py-4 text-left text-sm font-semibold text-blue-200 uppercase tracking-wider"
                        >
                          <i class="fas fa-layer-group mr-2"></i>Request Type
                        </th>
                        <th
                          class="px-6 py-4 text-left text-sm font-semibold text-blue-200 uppercase tracking-wider"
                        >
                          <i class="fas fa-info-circle mr-2"></i>Current Status
                        </th>
                        <th
                          class="px-6 py-4 text-left text-sm font-semibold text-blue-200 uppercase tracking-wider"
                        >
                          <i class="fas fa-calendar mr-2"></i>Date
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
                              class="w-8 h-8 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-lg flex items-center justify-center mr-3"
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

                        <!-- Department -->
                        <td class="px-6 py-4">
                          <div class="text-base text-white">
                            {{ recommendation.department?.name || 'N/A' }}
                          </div>
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

                        <!-- Current Status -->
                        <td class="px-6 py-4 whitespace-nowrap">
                          <span
                            class="inline-flex px-2 py-1 text-sm font-medium rounded-md border"
                            :class="
                              getStatusClass(recommendation.current_stage || recommendation.status)
                            "
                          >
                            {{
                              getStatusLabel(recommendation.current_stage || recommendation.status)
                            }}
                          </span>
                        </td>

                        <!-- Date -->
                        <td class="px-6 py-4 whitespace-nowrap text-base text-gray-300">
                          {{
                            formatDate(
                              recommendation.ict_director_approved_at || recommendation.updated_at
                            )
                          }}
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
              class="w-10 h-10 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center"
            >
              <i class="fas fa-file-alt text-white"></i>
            </div>
            <div>
              <h3 class="text-xl font-bold text-white">
                {{ formatRequestId(selectedRequest?.id) }}
              </h3>
              <p class="text-sm text-blue-300">
                {{ selectedRequest?.staff_name }} ({{ selectedRequest?.pf_number }}) -
                {{ selectedRequest?.department?.name }}
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
                  <span class="text-gray-300">Current Status:</span>
                  <span
                    class="px-2 py-1 text-sm rounded-md border"
                    :class="
                      getStatusClass(selectedRequest?.current_stage || selectedRequest?.status)
                    "
                  >
                    {{ getStatusLabel(selectedRequest?.current_stage || selectedRequest?.status) }}
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
                <div class="flex justify-between">
                  <span class="text-gray-300">Department:</span>
                  <span class="text-white font-medium">{{
                    selectedRequest?.department?.name || 'N/A'
                  }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- ICT Director Recommendations/Comments -->
          <div
            v-if="
              selectedRequest?.ict_director_info?.comments ||
              selectedRequest?.ict_director_info?.rejection_reasons
            "
            class="border rounded-lg p-4"
            :class="
              selectedRequest?.ict_director_info?.status === 'rejected'
                ? 'bg-gradient-to-r from-red-600/20 to-red-700/20 border-red-400/30'
                : 'bg-gradient-to-r from-teal-600/20 to-cyan-600/20 border border-teal-400/30'
            "
          >
            <div class="flex items-start space-x-3">
              <div
                class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0"
                :class="
                  selectedRequest?.ict_director_info?.status === 'rejected'
                    ? 'bg-gradient-to-br from-red-500 to-red-600'
                    : 'bg-gradient-to-br from-teal-500 to-cyan-600'
                "
              >
                <i
                  class="text-white"
                  :class="
                    selectedRequest?.ict_director_info?.status === 'rejected'
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
                      selectedRequest?.ict_director_info?.status === 'rejected'
                        ? 'text-red-200'
                        : 'text-teal-200'
                    "
                  >
                    ICT Director
                    {{
                      selectedRequest?.ict_director_info?.status === 'rejected'
                        ? 'Rejection Reasons'
                        : 'Recommendations'
                    }}
                  </h4>
                  <span
                    class="text-sm"
                    :class="
                      selectedRequest?.ict_director_info?.status === 'rejected'
                        ? 'text-red-300'
                        : 'text-teal-300'
                    "
                  >
                    {{ selectedRequest?.ict_director_info?.approved_by || 'ICT Director' }}
                  </span>
                </div>
                <div
                  class="rounded-lg p-3 mb-3"
                  :class="
                    selectedRequest?.ict_director_info?.status === 'rejected'
                      ? 'bg-red-900/20'
                      : 'bg-teal-900/20'
                  "
                >
                  <p class="text-white leading-relaxed whitespace-pre-line">
                    <span v-if="selectedRequest?.ict_director_info?.status === 'rejected'">
                      {{
                        selectedRequest?.ict_director_info?.rejection_reasons ||
                        selectedRequest?.ict_director_info?.comments ||
                        'No rejection reasons provided'
                      }}
                    </span>
                    <span v-else>
                      {{ selectedRequest?.ict_director_info?.comments || 'No comments provided' }}
                    </span>
                  </p>
                </div>
                <div class="text-sm text-teal-300">
                  <i class="fas fa-calendar mr-1"></i>
                  {{
                    formatDate(
                      selectedRequest?.ict_director_info?.approved_at || selectedRequest?.updated_at
                    )
                  }}
                </div>
              </div>
            </div>
          </div>

          <!-- Head of IT Response (if available) -->
          <div
            v-if="selectedRequest?.head_it_info?.comments"
            class="bg-gradient-to-r from-purple-600/20 to-pink-600/20 border border-purple-400/30 rounded-lg p-4"
          >
            <div class="flex items-start space-x-3">
              <div
                class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-600 rounded-full flex items-center justify-center flex-shrink-0"
              >
                <i class="fas fa-user-cog text-white"></i>
              </div>
              <div class="flex-1">
                <div class="flex items-center space-x-2 mb-3">
                  <h4 class="font-semibold text-purple-200">Head of IT Response</h4>
                  <span class="text-sm text-purple-300">
                    {{ selectedRequest?.head_it_info?.approved_by || 'Head of IT' }}
                  </span>
                </div>
                <div class="bg-purple-900/20 rounded-lg p-3 mb-3">
                  <p class="text-white leading-relaxed whitespace-pre-line">
                    {{ selectedRequest?.head_it_info?.comments || 'No response provided' }}
                  </p>
                </div>
                <div class="text-sm text-purple-300">
                  <i class="fas fa-calendar mr-1"></i>
                  {{ formatDate(selectedRequest?.head_it_info?.approved_at) }}
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
  </div>
</template>

<script>
  import { ref, computed, onMounted } from 'vue'
  import Header from '@/components/header.vue'
  import ModernSidebar from '@/components/ModernSidebar.vue'
  import { useAuth } from '@/composables/useAuth'
  import headOfItService from '@/services/headOfItService'

  export default {
    name: 'HeadOfItDictRecommendations',
    components: {
      Header,
      ModernSidebar
    },
    setup() {
      const { requireRole, ROLES } = useAuth()

      const loading = ref(false)
      const recommendations = ref([])
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
      const debounceTimeout = ref(null)

      // Modal state
      const showDetailsModal = ref(false)
      const selectedRequest = ref(null)

      const requestTypeLabels = {
        jeeva_access: 'Jeeva Access',
        wellsoft: 'Wellsoft',
        internet_access_request: 'Internet Access'
      }

      const statusLabels = {
        pending: 'Pending',
        pending_hod: 'Pending HOD',
        hod_approved: 'HOD Approved',
        hod_rejected: 'HOD Rejected',
        pending_divisional: 'Pending Divisional',
        divisional_approved: 'Divisional Approved',
        divisional_rejected: 'Divisional Rejected',
        pending_ict_director: 'Pending ICT Director',
        ict_director_approved: 'ICT Director Approved',
        ict_director_rejected: 'ICT Director Rejected',
        pending_head_it: 'Pending Head IT',
        head_it_approved: 'Head IT Approved',
        head_it_rejected: 'Head IT Rejected',
        pending_ict_officer: 'Pending ICT Officer',
        ict_officer_rejected: 'ICT Officer Rejected',
        implemented: 'Implemented',
        completed: 'Completed',
        rejected: 'Rejected',
        unknown: 'Status Unknown'
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
            (recommendation) =>
              recommendation.current_stage === statusFilter.value ||
              recommendation.status === statusFilter.value
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

        // Sort by date (newest first)
        return filtered.sort((a, b) => {
          const dateA = new Date(a.ict_director_approved_at || a.updated_at || 0)
          const dateB = new Date(b.ict_director_approved_at || b.updated_at || 0)
          return dateB - dateA
        })
      })

      const hasActiveFilters = computed(() => {
        return !!(searchQuery.value || statusFilter.value || requestTypeFilter.value)
      })

      // Search and filter methods
      const debouncedSearch = () => {
        if (debounceTimeout.value) {
          clearTimeout(debounceTimeout.value)
        }
        debounceTimeout.value = setTimeout(() => {
          console.log('Search applied:', searchQuery.value)
          fetchDictRecommendations(1)
        }, 300)
      }

      const applyFilters = () => {
        console.log('Filters applied:', {
          status: statusFilter.value,
          requestType: requestTypeFilter.value
        })
        fetchDictRecommendations(1)
      }

      const clearSearch = () => {
        searchQuery.value = ''
        fetchDictRecommendations(1)
      }

      const clearAllFilters = () => {
        searchQuery.value = ''
        statusFilter.value = ''
        requestTypeFilter.value = ''
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

      const fetchDictRecommendations = async (page = 1) => {
        try {
          loading.value = true
          console.log('🔄 Fetching ICT Director recommendations using headOfItService...')

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

          console.log('📋 Request parameters:', params)
          const result = await headOfItService.getDictRecommendations(params)

          console.log('📡 API Response:', result)

          if (result.success) {
            console.log('✅ API call successful, data:', result.data.data)
            recommendations.value = result.data.data || []
            pagination.value = result.data.pagination || {
              current_page: 1,
              last_page: 1,
              per_page: 10,
              total: result.data.data?.length || 0
            }
          } else {
            console.error('❌ API returned error:', result.message)
            recommendations.value = []
          }
        } catch (error) {
          console.error('💥 Error fetching DICT recommendations:', error)
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

      const getRequestTypeLabel = (type) => {
        return requestTypeLabels[type] || type
      }

      const getStatusLabel = (status) => {
        if (!status) return 'No Status'
        return (
          statusLabels[status] || status.replace(/_/g, ' ').replace(/\b\w/g, (l) => l.toUpperCase())
        )
      }

      const getStatusClass = (status) => {
        const statusClasses = {
          pending: 'bg-yellow-600/30 text-yellow-200 border-yellow-400/30',
          pending_hod: 'bg-orange-600/30 text-orange-200 border-orange-400/30',
          hod_approved: 'bg-blue-600/30 text-blue-200 border-blue-400/30',
          hod_rejected: 'bg-red-500/30 text-red-200 border-red-400/30',
          pending_divisional: 'bg-purple-600/30 text-purple-200 border-purple-400/30',
          divisional_approved: 'bg-green-600/30 text-green-200 border-green-400/30',
          divisional_rejected: 'bg-red-600/30 text-red-200 border-red-400/30',
          pending_ict_director: 'bg-indigo-600/30 text-indigo-200 border-indigo-400/30',
          ict_director_approved: 'bg-teal-600/30 text-teal-200 border-teal-400/30',
          ict_director_rejected: 'bg-red-600/30 text-red-200 border-red-400/30',
          pending_head_it: 'bg-purple-700/30 text-purple-200 border-purple-500/30',
          head_it_approved: 'bg-pink-600/30 text-pink-200 border-pink-400/30',
          head_it_rejected: 'bg-red-700/30 text-red-200 border-red-500/30',
          pending_ict_officer: 'bg-blue-700/30 text-blue-200 border-blue-500/30',
          ict_officer_rejected: 'bg-red-800/30 text-red-200 border-red-600/30',
          implemented: 'bg-green-700/30 text-green-100 border-green-500/30',
          completed: 'bg-green-800/30 text-green-100 border-green-600/30',
          rejected: 'bg-red-600/30 text-red-200 border-red-400/30',
          unknown: 'bg-gray-600/30 text-gray-200 border-gray-400/30'
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
          (request?.wellsoft_modules_selected && request.wellsoft_modules_selected.length > 0) ||
          (request?.jeeva_modules_selected && request.jeeva_modules_selected.length > 0) ||
          (request?.internet_purposes && request.internet_purposes.length > 0)
        )
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

      onMounted(() => {
        requireRole([ROLES.HEAD_OF_IT])
        fetchDictRecommendations()
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
        formatRequestId,
        hasModules,
        // Search and filter functionality
        searchQuery,
        statusFilter,
        requestTypeFilter,
        filteredRecommendations,
        totalRecommendations,
        hasActiveFilters,
        debouncedSearch,
        applyFilters,
        clearSearch,
        clearAllFilters,
        clearStatusFilter,
        clearRequestTypeFilter,
        // Modal functionality
        showDetailsModal,
        selectedRequest,
        viewRequestDetails,
        closeDetailsModal
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

  /* Focus states */
  input:focus,
  select:focus {
    box-shadow: 0 0 0 3px rgba(20, 184, 166, 0.1);
    transform: translateY(-1px);
  }

  /* Modal backdrop styling */
  .fixed.inset-0 {
    backdrop-filter: blur(8px);
  }
</style>
