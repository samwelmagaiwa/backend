<template>
  <div class="flex flex-col h-screen">
    <Header />
    <div class="flex flex-1 overflow-hidden">
      <ModernSidebar />
      <main
        class="flex-1 p-4 bg-gradient-to-br from-blue-900 via-blue-800 to-teal-900 overflow-y-auto relative"
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
          <div class="absolute inset-0">
            <div
              v-for="i in 15"
              :key="i"
              class="absolute text-white opacity-10 animate-float"
              :style="{
                left: Math.random() * 100 + '%',
                top: Math.random() * 100 + '%',
                animationDelay: Math.random() * 3 + 's',
                animationDuration: Math.random() * 3 + 2 + 's',
                fontSize: Math.random() * 20 + 10 + 'px'
              }"
            >
              <i
                :class="[
                  'fas',
                  ['fa-heartbeat', 'fa-user-md', 'fa-hospital', 'fa-stethoscope', 'fa-plus'][
                    Math.floor(Math.random() * 5)
                  ]
                ]"
              ></i>
            </div>
          </div>
        </div>

        <div class="max-w-full mx-auto relative z-10">
          <!-- Header Section -->
          <div class="medical-glass-card rounded-t-3xl p-6 mb-0 border-b border-blue-300/30">
            <div class="flex justify-between items-center">
              <!-- Left Logo -->
              <div
                class="w-28 h-28 mr-8 transform hover:scale-110 transition-transform duration-300"
              >
                <div
                  class="w-full h-full bg-gradient-to-br from-blue-500/20 to-teal-500/20 rounded-2xl backdrop-blur-sm border-2 border-blue-300/40 flex items-center justify-center shadow-2xl hover:shadow-blue-500/25"
                >
                  <img
                    src="/assets/images/ngao2.png"
                    alt="National Shield"
                    class="max-w-20 max-h-20 object-contain"
                  />
                </div>
              </div>

              <!-- Center Content -->
              <div class="text-center flex-1">
                <h1
                  class="text-3xl font-bold text-white mb-4 tracking-wide drop-shadow-lg animate-fade-in"
                >
                  MUHIMBILI NATIONAL HOSPITAL
                </h1>
                <div class="relative inline-block mb-4">
                  <div
                    class="bg-gradient-to-r from-emerald-600 to-emerald-700 text-white px-8 py-3 rounded-full text-xl font-bold shadow-2xl transform hover:scale-105 transition-all duration-300 border-2 border-emerald-400/60"
                  >
                    <span class="relative z-10 flex items-center gap-3">
                      <i class="fas fa-clipboard-check text-xl"></i>
                      ACCESS REQUESTS - HOD APPROVAL STAGE
                    </span>
                    <div
                      class="absolute inset-0 bg-gradient-to-r from-emerald-700 to-emerald-800 rounded-full opacity-0 hover:opacity-100 transition-opacity duration-300"
                    ></div>
                  </div>
                </div>
                <h2
                  class="text-lg font-bold text-blue-100 tracking-wide drop-shadow-md animate-fade-in-delay"
                >
                  Staff requests displayed in FIFO order. Click "View & Process" to capture: Module
                  Requested for, Module Request, Access Rights, and Comments.
                </h2>
              </div>

              <!-- Right Logo -->
              <div
                class="w-28 h-28 ml-8 transform hover:scale-110 transition-transform duration-300"
              >
                <div
                  class="w-full h-full bg-gradient-to-br from-teal-500/20 to-blue-500/20 rounded-2xl backdrop-blur-sm border-2 border-teal-300/40 flex items-center justify-center shadow-2xl hover:shadow-teal-500/25"
                >
                  <img
                    src="/assets/images/logo2.png"
                    alt="Muhimbili Logo"
                    class="max-w-20 max-h-20 object-contain"
                  />
                </div>
              </div>
            </div>
          </div>

          <!-- Error Display -->
          <div
            v-if="error"
            class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"
          >
            <h3 class="font-bold">Error</h3>
            <p>{{ error }}</p>
            <button
              @click="fetchRequests"
              class="mt-2 bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700"
            >
              Retry
            </button>
          </div>

          <!-- Main Content -->
          <div class="medical-glass-card rounded-b-3xl overflow-hidden animate-slide-up">
            <div class="p-6">
              <!-- Stats Cards -->
              <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div
                  class="bg-gradient-to-r from-yellow-600/25 to-orange-600/25 border-2 border-yellow-400/40 p-6 rounded-2xl backdrop-blur-sm hover:shadow-xl hover:shadow-yellow-500/20 transition-all duration-500"
                >
                  <div class="flex items-center">
                    <div
                      class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-orange-600 rounded-lg flex items-center justify-center shadow-lg"
                    >
                      <i class="fas fa-clock text-white text-lg"></i>
                    </div>
                    <div class="ml-4">
                      <p class="text-sm font-semibold text-yellow-200">Pending HOD Approval</p>
                      <p class="text-2xl font-bold text-white">
                        {{ stats.pendingHod }}
                      </p>
                    </div>
                  </div>
                </div>

                <div
                  class="bg-gradient-to-r from-green-600/25 to-emerald-600/25 border-2 border-green-400/40 p-6 rounded-2xl backdrop-blur-sm hover:shadow-xl hover:shadow-green-500/20 transition-all duration-500"
                >
                  <div class="flex items-center">
                    <div
                      class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-lg flex items-center justify-center shadow-lg"
                    >
                      <i class="fas fa-check-circle text-white text-lg"></i>
                    </div>
                    <div class="ml-4">
                      <p class="text-sm font-semibold text-green-200">HOD Approved</p>
                      <p class="text-2xl font-bold text-white">
                        {{ stats.hodApproved }}
                      </p>
                    </div>
                  </div>
                </div>

                <div
                  class="bg-gradient-to-r from-red-600/25 to-pink-600/25 border-2 border-red-400/40 p-6 rounded-2xl backdrop-blur-sm hover:shadow-xl hover:shadow-red-500/20 transition-all duration-500"
                >
                  <div class="flex items-center">
                    <div
                      class="w-12 h-12 bg-gradient-to-br from-red-500 to-pink-600 rounded-lg flex items-center justify-center shadow-lg"
                    >
                      <i class="fas fa-times-circle text-white text-lg"></i>
                    </div>
                    <div class="ml-4">
                      <p class="text-sm font-semibold text-red-200">HOD Rejected</p>
                      <p class="text-2xl font-bold text-white">
                        {{ stats.hodRejected }}
                      </p>
                    </div>
                  </div>
                </div>

                <div
                  class="bg-gradient-to-r from-blue-600/25 to-indigo-600/25 border-2 border-blue-400/40 p-6 rounded-2xl backdrop-blur-sm hover:shadow-xl hover:shadow-blue-500/20 transition-all duration-500"
                >
                  <div class="flex items-center">
                    <div
                      class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center shadow-lg"
                    >
                      <i class="fas fa-list text-white text-lg"></i>
                    </div>
                    <div class="ml-4">
                      <p class="text-sm font-semibold text-blue-200">Total Requests</p>
                      <p class="text-2xl font-bold text-white">
                        {{ stats.total }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Filters and Search -->
              <div
                class="bg-gradient-to-r from-blue-600/25 to-teal-600/25 border-2 border-blue-400/40 p-6 rounded-2xl backdrop-blur-sm mb-8"
              >
                <div class="flex flex-col md:flex-row gap-4 items-center">
                  <div class="flex-1">
                    <div class="relative">
                      <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Search by staff name, PF number, or department..."
                        class="w-full px-4 py-3 bg-white/15 border border-blue-300/30 rounded-lg focus:border-teal-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 text-sm"
                      />
                      <div
                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-teal-300/50"
                      >
                        <i class="fas fa-search text-sm"></i>
                      </div>
                    </div>
                  </div>

                  <div class="flex gap-4">
                    <select
                      v-model="statusFilter"
                      class="px-4 py-3 bg-white/15 border border-blue-300/30 rounded-lg focus:border-teal-400 focus:outline-none text-white backdrop-blur-sm transition-all appearance-none cursor-pointer text-sm"
                    >
                      <option value="">All Statuses</option>
                      <option value="pending">Pending Submission</option>
                      <option value="pending_hod">Pending HOD</option>
                      <option value="hod_approved">HOD Approved</option>
                      <option value="hod_rejected">HOD Rejected</option>
                      <option value="divisional_approved">Divisional Approved</option>
                      <option value="divisional_rejected">Divisional Rejected</option>
                      <option value="approved">Fully Approved</option>
                      <option value="implemented">Implemented</option>
                      <option value="completed">Completed</option>
                      <option value="cancelled">Cancelled</option>
                    </select>

                    <button
                      @click="refreshRequests"
                      class="px-6 py-3 bg-gradient-to-r from-teal-600 to-blue-600 text-white rounded-lg hover:from-teal-700 hover:to-blue-700 transition-all duration-300 font-semibold flex items-center shadow-lg hover:shadow-xl transform hover:scale-105 text-sm"
                    >
                      <i class="fas fa-sync-alt mr-2"></i>
                      Refresh
                    </button>
                  </div>
                </div>
              </div>

              <!-- Requests Table -->
              <div
                class="table-container bg-gradient-to-r from-blue-600/25 to-teal-600/25 border-2 border-blue-400/40 rounded-2xl backdrop-blur-sm"
                style="overflow: visible"
              >
                <div class="p-6 border-b border-blue-300/30">
                  <h3 class="text-xl font-bold text-white flex items-center">
                    <i class="fas fa-table mr-3 text-teal-300"></i>
                    Combined Access Requests for HOD Approval
                  </h3>
                </div>

                <div class="overflow-x-auto" style="overflow-y: visible">
                  <table class="w-full">
                    <thead class="bg-blue-800/50">
                      <tr>
                        <th
                          class="px-6 py-4 text-left text-sm font-semibold text-blue-100 uppercase tracking-wider"
                        >
                          <i class="fas fa-hashtag mr-2"></i>Request ID
                        </th>
                        <th
                          class="px-6 py-4 text-left text-sm font-semibold text-blue-100 uppercase tracking-wider"
                        >
                          <i class="fas fa-layer-group mr-2"></i>Request Type
                        </th>
                        <th
                          class="px-6 py-4 text-left text-sm font-semibold text-blue-100 uppercase tracking-wider"
                        >
                          <i class="fas fa-user mr-2"></i>Personal Information
                        </th>
                        <th
                          class="px-6 py-4 text-left text-sm font-semibold text-blue-100 uppercase tracking-wider"
                        >
                          <i class="fas fa-calendar mr-2"></i>Submission Date (FIFO)
                        </th>
                        <th
                          class="px-6 py-4 text-left text-sm font-semibold text-blue-100 uppercase tracking-wider"
                        >
                          <i class="fas fa-flag mr-2"></i>Current Status
                        </th>
                        <th
                          class="px-6 py-4 text-center text-sm font-semibold text-blue-100 uppercase tracking-wider"
                        >
                          <div class="flex items-center justify-center">
                            <span
                              class="bg-blue-100/10 px-3 py-1.5 rounded-lg border border-blue-300/20"
                            >
                              <i class="fas fa-cogs mr-2"></i>Actions
                            </span>
                          </div>
                        </th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-blue-300/20">
                      <tr
                        v-for="request in filteredRequests"
                        :key="request.id"
                        class="hover:bg-blue-700/30 transition-colors duration-200"
                      >
                        <!-- Request ID -->
                        <td class="px-6 py-4 whitespace-nowrap">
                          <div class="flex items-center">
                            <div
                              class="w-10 h-10 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-lg flex items-center justify-center shadow-lg mr-3"
                            >
                              <i class="fas fa-hashtag text-white text-sm"></i>
                            </div>
                            <div>
                              <div class="text-base font-medium text-white">
                                {{
                                  request.request_id ||
                                  `REQ-${request.id.toString().padStart(6, '0')}`
                                }}
                              </div>
                              <div class="text-xs text-purple-300">ID: {{ request.id }}</div>
                            </div>
                          </div>
                        </td>

                        <!-- Request Type -->
                        <td class="px-6 py-4 whitespace-nowrap">
                          <div class="flex flex-wrap gap-1">
                            <span
                              v-if="hasService(request, 'jeeva')"
                              class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                            >
                              <i class="fas fa-database mr-1"></i>Jeeva
                            </span>
                            <span
                              v-if="hasService(request, 'wellsoft')"
                              class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800"
                            >
                              <i class="fas fa-laptop-medical mr-1"></i>Wellsoft
                            </span>
                            <span
                              v-if="hasService(request, 'internet')"
                              class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-cyan-100 text-cyan-800"
                            >
                              <i class="fas fa-wifi mr-1"></i>Internet
                            </span>
                          </div>
                          <div class="text-sm text-blue-300 mt-1">Combined Access Request</div>
                        </td>

                        <!-- Personal Information -->
                        <td class="px-6 py-4 whitespace-nowrap">
                          <div class="flex items-center">
                            <div
                              class="w-10 h-10 bg-gradient-to-br from-teal-500 to-blue-600 rounded-full flex items-center justify-center shadow-lg mr-3"
                            >
                              <i class="fas fa-user text-white text-sm"></i>
                            </div>
                            <div>
                              <div class="text-base font-medium text-white">
                                {{ request.staff_name || request.full_name || 'Unknown User' }}
                              </div>
                              <div class="text-sm text-blue-300">
                                {{ request.phone || request.phone_number || 'No phone' }}
                              </div>
                              <div v-if="request.pf_number" class="text-sm text-teal-300">
                                PF: {{ request.pf_number }}
                              </div>
                              <div class="text-xs text-blue-200">
                                Dept: {{ request.department || 'Unknown' }}
                              </div>
                            </div>
                          </div>
                        </td>

                        <!-- Submission Date -->
                        <td class="px-6 py-4 whitespace-nowrap">
                          <div class="text-base text-white font-medium">
                            {{ formatDate(request.created_at || request.submission_date) }}
                          </div>
                          <div class="text-sm text-blue-300">
                            {{ formatTime(request.created_at || request.submission_date) }}
                          </div>
                        </td>

                        <!-- Current Status -->
                        <td class="px-6 py-4 whitespace-nowrap">
                          <span
                            :class="
                              getStatusBadgeClass(
                                request.hod_status || request.status || 'pending_hod'
                              )
                            "
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium"
                          >
                            <i
                              :class="
                                getStatusIcon(request.hod_status || request.status || 'pending_hod')
                              "
                              class="mr-1"
                            ></i>
                            {{
                              getStatusText(request.hod_status || request.status || 'pending_hod')
                            }}
                          </span>
                        </td>

                        <!-- Actions -->
                        <td class="px-6 py-4 whitespace-nowrap text-center relative">
                          <div class="relative inline-block text-left">
                            <!-- Three dots button -->
                            <button
                              @click.stop="toggleDropdown(request.id)"
                              :data-request-id="request.id"
                              class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-blue-600/20 hover:bg-blue-600/40 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500/50 shadow-lg hover:shadow-xl"
                            >
                              <div class="flex flex-col space-y-0.5">
                                <div class="w-1.5 h-1.5 bg-blue-100 rounded-full"></div>
                                <div class="w-1.5 h-1.5 bg-blue-100 rounded-full"></div>
                                <div class="w-1.5 h-1.5 bg-blue-100 rounded-full"></div>
                              </div>
                            </button>

                            <!-- Dropdown menu -->
                            <div
                              v-show="activeDropdown === request.id"
                              :id="'dropdown-' + request.id"
                              class="dropdown-menu fixed w-56 origin-top-right bg-white rounded-xl shadow-2xl border border-gray-200/50 focus:outline-none backdrop-blur-sm"
                              :style="getDropdownStyle(request.id)"
                              @click.stop
                            >
                              <div class="py-2">
                                <button
                                  @click="viewAndProcessRequest(request.id)"
                                  class="group flex items-center w-full px-4 py-3 text-sm text-gray-700 hover:bg-gradient-to-r hover:from-blue-50 hover:to-blue-100 hover:text-blue-700 transition-all duration-200 font-medium"
                                >
                                  <i
                                    class="fas fa-eye mr-3 text-blue-500 group-hover:text-blue-600"
                                  ></i>
                                  View & Process
                                </button>

                                <button
                                  v-if="canEdit(request)"
                                  @click="editRequest(request.id)"
                                  class="group flex items-center w-full px-4 py-3 text-sm text-gray-700 hover:bg-gradient-to-r hover:from-amber-50 hover:to-amber-100 hover:text-amber-700 transition-all duration-200 font-medium"
                                >
                                  <i
                                    class="fas fa-edit mr-3 text-amber-500 group-hover:text-amber-600"
                                  ></i>
                                  Edit
                                </button>

                                <div
                                  v-if="canCancel(request)"
                                  class="border-t border-gray-100 my-1"
                                ></div>

                                <button
                                  v-if="canCancel(request)"
                                  @click="cancelRequest(request.id)"
                                  class="group flex items-center w-full px-4 py-3 text-sm text-gray-700 hover:bg-gradient-to-r hover:from-red-50 hover:to-red-100 hover:text-red-700 transition-all duration-200 font-medium"
                                >
                                  <i
                                    class="fas fa-ban mr-3 text-red-500 group-hover:text-red-600"
                                  ></i>
                                  Cancel
                                </button>

                                <!-- Separator for additional actions -->
                                <div class="border-t border-gray-100 my-1"></div>

                                <!-- View Progress action -->
                                <button
                                  @click="viewProgress(request)"
                                  v-if="canViewProgress(request)"
                                  class="group flex items-center w-full px-4 py-3 text-sm text-gray-700 hover:bg-gradient-to-r hover:from-orange-50 hover:to-orange-100 hover:text-orange-700 transition-all duration-200 font-medium"
                                >
                                  <i
                                    class="fas fa-chart-line mr-3 text-orange-500 group-hover:text-orange-600"
                                  ></i>
                                  View Progress
                                </button>

                                <!-- View Timeline action -->
                                <button
                                  @click="viewTimeline(request)"
                                  class="group flex items-center w-full px-4 py-3 text-sm text-gray-700 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-indigo-100 hover:text-indigo-700 transition-all duration-200 font-medium"
                                >
                                  <i
                                    class="fas fa-history mr-3 text-indigo-500 group-hover:text-indigo-600"
                                  ></i>
                                  View Timeline
                                </button>
                              </div>
                            </div>
                          </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>

                  <!-- Empty State -->
                  <div v-if="filteredRequests.length === 0" class="text-center py-12">
                    <div
                      class="w-24 h-24 bg-gradient-to-br from-blue-500/20 to-teal-500/20 rounded-full flex items-center justify-center mx-auto mb-4"
                    >
                      <i class="fas fa-inbox text-blue-300 text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-white mb-2">No requests found</h3>
                    <p class="text-blue-300">
                      {{
                        searchQuery || statusFilter
                          ? 'Try adjusting your filters'
                          : 'No combined access requests have been submitted yet.'
                      }}
                    </p>
                  </div>
                </div>

                <!-- Pagination -->
                <div
                  v-if="filteredRequests.length > 0"
                  class="px-6 py-4 border-t border-blue-300/30 flex items-center justify-between"
                >
                  <div class="text-sm text-blue-300">
                    Showing {{ filteredRequests.length }} of {{ requests.length }} requests
                  </div>
                  <div class="flex space-x-2">
                    <!-- Add pagination controls here if needed -->
                  </div>
                </div>
              </div>

              <!-- Footer -->
              <AppFooter />
            </div>
          </div>
        </div>
      </main>
    </div>

    <!-- Loading Modal -->
    <div
      v-if="isLoading"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
      <div class="bg-white rounded-xl shadow-2xl p-8 text-center">
        <div class="flex justify-center mb-4">
          <OrbitingDots size="lg" />
        </div>
        <p class="text-gray-600">Loading requests...</p>
      </div>
    </div>

    <!-- Timeline Modal -->
    <RequestTimeline
      :show="showTimeline"
      :request-id="selectedRequestId"
      @close="closeTimeline"
      @updated="handleTimelineUpdate"
    />
  </div>
</template>

<style scoped>
  /* Force dropdown to be visible above all content */
  .relative {
    position: relative;
    z-index: 1;
  }

  /* Ensure dropdown menus are always on top */
  .dropdown-menu {
    position: absolute !important;
    z-index: 99999 !important;
    box-shadow:
      0 20px 25px -5px rgba(0, 0, 0, 0.1),
      0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
  }

  /* Prevent overflow clipping in containers */
  .table-container {
    overflow: visible !important;
  }

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

  @keyframes fade-in {
    from {
      opacity: 0;
      transform: translateY(20px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  @keyframes fade-in-delay {
    0% {
      opacity: 0;
      transform: translateY(20px);
    }
    50% {
      opacity: 0;
      transform: translateY(20px);
    }
    100% {
      opacity: 1;
      transform: translateY(0);
    }
  }

  @keyframes slide-up {
    from {
      opacity: 0;
      transform: translateY(30px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .animate-float {
    animation: float ease-in-out infinite;
  }

  .animate-fade-in {
    animation: fade-in 0.6s ease-out;
  }

  .animate-fade-in-delay {
    animation: fade-in-delay 1.2s ease-out;
  }

  .animate-slide-up {
    animation: slide-up 0.4s ease-out;
  }
</style>

<script>
  import Header from '@/components/header.vue'
  import ModernSidebar from '@/components/ModernSidebar.vue'
  import AppFooter from '@/components/footer.vue'
  import RequestTimeline from '@/components/common/RequestTimeline.vue'
  import OrbitingDots from '@/components/common/OrbitingDots.vue'
  import combinedAccessService from '@/services/combinedAccessService'
  import statusUtils from '@/utils/statusUtils'

  export default {
    name: 'HodRequestList',
    components: {
      Header,
      ModernSidebar,
      AppFooter,
      RequestTimeline,
      OrbitingDots
    },
    setup() {
      return {
        // No local state needed for sidebar
      }
    },
    data() {
      return {
        requests: [],
        searchQuery: '',
        statusFilter: '',
        isLoading: false,
        stats: {
          pendingHod: 0,
          hodApproved: 0,
          hodRejected: 0,
          total: 0
        },
        error: null,
        activeDropdown: null,
        // Timeline modal state
        showTimeline: false,
        selectedRequestId: null,
        // Add status utilities for consistent status handling
        $statusUtils: statusUtils
      }
    },
    computed: {
      filteredRequests() {
        // Ensure requests is always an array
        if (!Array.isArray(this.requests)) {
          console.warn('HodRequestList: requests is not an array:', this.requests)
          return []
        }

        let filtered = this.requests

        // Filter by search query
        if (this.searchQuery) {
          const query = this.searchQuery.toLowerCase()
          filtered = filtered.filter(
            (request) =>
              (request.staff_name || request.full_name || '').toLowerCase().includes(query) ||
              (request.pf_number || '').toLowerCase().includes(query) ||
              (request.department || '').toLowerCase().includes(query) ||
              (request.request_id || '').toLowerCase().includes(query)
          )
        }

        // Filter by status
        if (this.statusFilter) {
          filtered = filtered.filter(
            (request) =>
              (request.hod_status || request.status || 'pending_hod') === this.statusFilter
          )
        }

        // Sort by FIFO order (oldest first)
        return filtered.sort((a, b) => {
          const dateA = new Date(a.created_at || a.submission_date || 0)
          const dateB = new Date(b.created_at || b.submission_date || 0)
          return dateA - dateB
        })
      }
    },
    async mounted() {
      try {
        console.log('HodRequestList: Component mounted, initializing...')
        await this.fetchRequests()
        console.log('HodRequestList: Component initialized successfully')

        // Add click listener to close dropdowns when clicking outside
        document.addEventListener('click', this.closeDropdowns)
      } catch (error) {
        console.error('HodRequestList: Error during mount:', error)
        this.error = 'Failed to initialize component: ' + error.message
        this.isLoading = false
      }
    },

    beforeUnmount() {
      // Clean up the click listener
      document.removeEventListener('click', this.closeDropdowns)
    },

    methods: {
      toggleDropdown(requestId) {
        console.log('Toggle dropdown for request:', requestId)
        console.log('Current activeDropdown:', this.activeDropdown)

        if (this.activeDropdown === requestId) {
          this.activeDropdown = null
          console.log('Closing dropdown')
        } else {
          this.activeDropdown = requestId
          console.log('Opening dropdown for:', requestId)

          // Wait for DOM update before calculating position
          this.$nextTick(() => {
            // Force recalculation of dropdown position
            this.$forceUpdate()
          })
        }
      },

      closeDropdowns(event) {
        // Only close if clicking outside the dropdown area
        if (!event || !event.target.closest('.relative')) {
          this.activeDropdown = null
        }
      },

      getDropdownStyle(requestId) {
        if (this.activeDropdown !== requestId) {
          return { display: 'none' }
        }

        // Find the button element
        const buttonElement = document.querySelector(`[data-request-id="${requestId}"]`)
        if (!buttonElement) {
          return {
            position: 'fixed',
            top: '50px',
            right: '10px',
            zIndex: 99999
          }
        }

        const rect = buttonElement.getBoundingClientRect()
        const viewportHeight = window.innerHeight
        const dropdownHeight = 200 // Approximate height of dropdown (larger for HodRequestList)

        let top = rect.bottom + 8
        let right = window.innerWidth - rect.right

        // If dropdown would go below viewport, position it above the button
        if (top + dropdownHeight > viewportHeight) {
          top = rect.top - dropdownHeight - 8
        }

        // Ensure dropdown doesn't go off-screen to the left
        if (right < 0) {
          right = 10
        }

        return {
          position: 'fixed',
          top: top + 'px',
          right: right + 'px',
          zIndex: 99999
        }
      },
      async fetchRequests() {
        this.isLoading = true
        this.error = null

        try {
          console.log('Fetching combined access requests for HOD approval...')

          const response = await combinedAccessService.getHodRequests({
            search: this.searchQuery || undefined,
            status: this.statusFilter || undefined,
            per_page: 50
          })

          if (response.success) {
            // Handle the nested response structure: response.data.data.data
            const responseData = response.data?.data || response.data || {}
            this.requests = Array.isArray(responseData.data)
              ? responseData.data
              : Array.isArray(responseData)
                ? responseData
                : []
            console.log('Combined access requests loaded:', this.requests.length)
            console.log('Raw response data:', response.data)

            // Also fetch statistics
            await this.fetchStatistics()
          } else {
            throw new Error(response.error || 'Failed to fetch requests')
          }
        } catch (error) {
          console.error('Error fetching requests:', error)
          this.error =
            'Unable to load combined access requests. Please check your connection and try again.'
          this.requests = []
          this.calculateStats()
        } finally {
          this.isLoading = false
        }
      },

      async fetchStatistics() {
        try {
          const response = await combinedAccessService.getHodStatistics()

          if (response.success) {
            this.stats = response.data
          } else {
            // Fall back to calculating stats from loaded requests
            this.calculateStats()
          }
        } catch (error) {
          console.error('Error fetching statistics:', error)
          // Fall back to calculating stats from loaded requests
          this.calculateStats()
        }
      },

      calculateStats() {
        // Ensure requests is an array before calculating stats
        const requests = Array.isArray(this.requests) ? this.requests : []

        this.stats = {
          pendingHod: requests.filter((r) => (r.hod_status || r.status) === 'pending_hod').length,
          hodApproved: requests.filter((r) => (r.hod_status || r.status) === 'hod_approved').length,
          hodRejected: requests.filter((r) => (r.hod_status || r.status) === 'hod_rejected').length,
          approved: requests.filter((r) => (r.hod_status || r.status) === 'approved').length,
          implemented: requests.filter((r) => (r.hod_status || r.status) === 'implemented').length,
          completed: requests.filter((r) => (r.hod_status || r.status) === 'completed').length,
          cancelled: requests.filter((r) => (r.hod_status || r.status) === 'cancelled').length,
          total: requests.length
        }
      },

      async refreshRequests() {
        await this.fetchRequests()
      },

      viewAndProcessRequest(requestId) {
        this.closeDropdowns()
        // Navigate to both-service-form.vue with populated data
        this.$router.push(`/both-service-form/${requestId}`)
      },

      editRequest(requestId) {
        this.closeDropdowns()
        // Navigate to edit mode
        this.$router.push(`/both-service-form/${requestId}/edit`)
      },

      async cancelRequest(requestId) {
        this.closeDropdowns()
        try {
          const confirmed = confirm(
            'Are you sure you want to cancel this request? This action cannot be undone.'
          )
          if (!confirmed) return

          const reason = prompt('Please provide a reason for cancellation:')
          if (!reason || reason.trim() === '') {
            alert('Cancellation reason is required')
            return
          }

          console.log('Cancelling request:', requestId)

          const response = await combinedAccessService.cancelRequest(requestId, reason)

          if (response.success) {
            // Update local state
            const requestIndex = this.requests.findIndex((r) => r.id === requestId)
            if (requestIndex !== -1) {
              this.requests[requestIndex].hod_status = 'cancelled'
              this.requests[requestIndex].status = 'cancelled'
            }

            this.calculateStats()
            alert('Request cancelled successfully!')
          } else {
            throw new Error(response.error || 'Failed to cancel request')
          }
        } catch (error) {
          console.error('Error cancelling request:', error)
          alert('Error cancelling request: ' + error.message)
        }
      },

      hasService(request, serviceType) {
        return (
          (request.services && request.services.includes(serviceType)) ||
          (request.request_types &&
            request.request_types.some(
              (type) =>
                (serviceType === 'jeeva' && type === 'jeeva_access') ||
                (serviceType === 'wellsoft' && type === 'wellsoft') ||
                (serviceType === 'internet' && type === 'internet_access_request')
            ))
        )
      },

      canEdit(request) {
        // HOD can edit requests that are pending
        return (request.hod_status || request.status) === 'pending_hod'
      },

      canCancel(request) {
        // HOD can cancel requests that are not already rejected or cancelled
        const status = request.hod_status || request.status
        return status !== 'hod_rejected' && status !== 'cancelled'
      },

      formatDate(dateString) {
        if (!dateString) return 'N/A'
        const date = new Date(dateString)
        return date.toLocaleDateString('en-US', {
          year: 'numeric',
          month: 'short',
          day: 'numeric'
        })
      },

      formatTime(dateString) {
        if (!dateString) return 'N/A'
        const date = new Date(dateString)
        return date.toLocaleTimeString('en-US', {
          hour: '2-digit',
          minute: '2-digit'
        })
      },

      getStatusBadgeClass(status) {
        return this.$statusUtils.getStatusBadgeClass(status)
      },

      getStatusIcon(status) {
        return this.$statusUtils.getStatusIcon(status)
      },

      getStatusText(status) {
        // Use centralized status utility with component name for debugging
        return this.$statusUtils.getStatusText(status, 'HodRequestList')
      },

      // Timeline modal methods
      viewTimeline(request) {
        console.log('📅 HodRequestList: Opening timeline for request:', request.id)
        this.activeDropdown = null
        this.selectedRequestId = request.id
        this.showTimeline = true
      },

      closeTimeline() {
        console.log('📅 HodRequestList: Closing timeline modal')
        this.showTimeline = false
        this.selectedRequestId = null
      },

      async handleTimelineUpdate() {
        console.log('🔄 HodRequestList: Timeline updated, refreshing requests list...')
        await this.fetchRequests()
      },

      // View Progress method
      viewProgress(request) {
        console.log('👁️ HodRequestList: Viewing progress for request:', request.id)
        this.activeDropdown = null
        // Navigate to progress view - using ICT dashboard route for consistency
        this.$router.push(`/ict-dashboard/request-progress/${request.id}`)
      },

      canViewProgress(request) {
        // HOD can view progress for requests that are beyond the pending stage
        const status = request.hod_status || request.status
        return !['pending_hod', 'hod_rejected', 'cancelled'].includes(status)
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

  @keyframes fade-in {
    from {
      opacity: 0;
      transform: translateY(20px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  @keyframes fade-in-delay {
    from {
      opacity: 0;
      transform: translateY(20px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  @keyframes slide-up {
    from {
      opacity: 0;
      transform: translateY(30px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .animate-float {
    animation: float 6s ease-in-out infinite;
  }

  .animate-fade-in {
    animation: fade-in 1s ease-out;
  }

  .animate-fade-in-delay {
    animation: fade-in-delay 1s ease-out 0.3s both;
  }

  .animate-slide-up {
    animation: slide-up 0.6s ease-out;
  }

  /* Focus styles for accessibility */
  input:focus,
  select:focus {
    box-shadow: 0 0 0 3px rgba(45, 212, 191, 0.1);
  }

  button:focus {
    box-shadow: 0 0 0 3px rgba(45, 212, 191, 0.3);
  }

  /* Smooth transitions */
  * {
    transition-property:
      color, background-color, border-color, text-decoration-color, fill, stroke, opacity,
      box-shadow, transform, filter, backdrop-filter;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 200ms;
  }
</style>
