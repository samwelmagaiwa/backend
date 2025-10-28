<template>
  <div class="flex flex-col h-screen">
    <Header />
    <div class="flex flex-1 overflow-hidden">
      <div class="sidebar-narrow">
        <ModernSidebar />
      </div>
      <main class="flex-1 p-4 bg-blue-900 overflow-y-auto">
        <div class="max-w-full mx-auto">
          <!-- Error Display -->
          <div
            v-if="error"
            class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"
          >
            <h3 class="font-bold text-2xl">Error</h3>
            <p class="text-xl">{{ error }}</p>
            <button
              @click="fetchAccessRequests"
              class="mt-2 bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 text-lg font-medium"
            >
              Retry
            </button>
          </div>

          <!-- Stats -->
          <div class="grid grid-cols-4 gap-4 mb-6">
            <div class="bg-green-600/25 border border-green-400/40 p-4 rounded-lg">
              <h3 class="text-green-200 text-xl font-semibold">Available for Assignment</h3>
              <p class="text-white text-4xl font-bold">{{ stats.unassigned }}</p>
            </div>
            <div class="bg-blue-600/25 border border-blue-400/40 p-4 rounded-lg">
              <h3 class="text-blue-200 text-xl font-semibold">Assigned to ICT</h3>
              <p class="text-white text-4xl font-bold">{{ stats.assigned }}</p>
            </div>
            <div class="bg-purple-600/25 border border-purple-400/40 p-4 rounded-lg">
              <h3 class="text-purple-200 text-xl font-semibold">In Progress</h3>
              <p class="text-white text-4xl font-bold">{{ stats.inProgress }}</p>
            </div>
            <div class="bg-teal-600/25 border border-teal-400/40 p-4 rounded-lg">
              <h3 class="text-teal-200 text-xl font-semibold">Total Requests</h3>
              <p class="text-white text-4xl font-bold">{{ stats.total }}</p>
            </div>
          </div>

          <!-- Filters -->
          <div class="bg-white/10 rounded-lg p-4 mb-6">
            <div class="flex gap-4">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Search by staff name, PF number, or department..."
                class="flex-1 px-3 py-2 bg-white/20 border border-blue-300/30 rounded text-white placeholder-blue-200/60 text-lg"
              />
              <select
                v-model="statusFilter"
                class="px-3 py-2 bg-white/20 border border-blue-300/30 rounded text-white text-lg"
              >
                <option value="">All Requests</option>
                <option value="unassigned">Available for Assignment</option>
                <option value="assigned_to_ict">Assigned to ICT</option>
                <option value="implementation_in_progress">In Progress</option>
                <option value="completed">Completed</option>
              </select>
              <button
                @click="refreshRequests"
                class="px-6 py-2 bg-teal-600 text-white rounded hover:bg-teal-700 text-lg font-medium"
              >
                Refresh
              </button>
            </div>
          </div>

          <!-- Requests Table -->
          <div class="bg-white/10 rounded-lg">
            <div class="overflow-x-auto" style="overflow-y: visible">
              <table class="w-full">
                <thead class="bg-blue-800/50">
                  <tr>
                    <th class="px-4 py-3 text-left text-blue-100 text-lg font-semibold">
                      Request ID
                    </th>
                    <th class="px-4 py-3 text-left text-blue-100 text-lg font-semibold">
                      Request Type
                    </th>
                    <th class="px-4 py-3 text-left text-blue-100 text-lg font-semibold">
                      Requested Modules
                    </th>
                    <th class="px-4 py-3 text-left text-blue-100 text-lg font-semibold">
                      Requester Name & PF Number
                    </th>
                    <th class="px-4 py-3 text-left text-blue-100 text-lg font-semibold">
                      Head of IT Approval Date
                    </th>
                    <th class="px-4 py-3 text-left text-blue-100 text-lg font-semibold">Status</th>
                    <th class="px-4 py-3 text-left text-blue-100 text-lg font-semibold">
                      SMS Status
                    </th>
                    <th class="px-4 py-3 text-center text-blue-100 text-lg font-semibold">
                      Actions
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="request in filteredRequests"
                    :key="request.id"
                    :class="[
                      'border-t border-blue-300/20 hover:bg-blue-700/30 transition-colors duration-200',
                      isUnassigned(request.status)
                        ? 'bg-yellow-900/20 border-l-4 border-yellow-400'
                        : 'bg-green-900/10 border-l-4 border-green-600'
                    ]"
                  >
                    <!-- Request ID -->
                    <td class="px-4 py-3">
                      <div class="flex items-center space-x-2">
                        <!-- Status Indicator Icon -->
                        <div class="flex-shrink-0">
                          <i
                            v-if="isUnassigned(request.status)"
                            class="fas fa-clock text-yellow-400 text-base"
                            title="Available for assignment"
                          ></i>
                          <i
                            v-else
                            class="fas fa-check-circle text-green-400 text-base"
                            title="Assigned or in progress"
                          ></i>
                        </div>
                        <div>
                          <div class="text-white font-medium text-lg">
                            {{
                              request.request_id || `REQ-${request.id.toString().padStart(6, '0')}`
                            }}
                          </div>
                          <div class="text-purple-300 text-base">ID: {{ request.id }}</div>
                        </div>
                      </div>
                    </td>

                    <!-- Request Type -->
                    <td class="px-4 py-3">
                      <div class="text-white font-medium text-lg">
                        {{ getRequestType(request) }}
                      </div>
                      <div class="text-blue-300 text-base">
                        {{ request.department_name || request.department || 'Unknown Dept' }}
                      </div>
                    </td>

                    <!-- Requested Modules -->
                    <td class="px-4 py-3">
                      <div class="flex flex-nowrap gap-1">
                        <span
                          v-if="hasService(request, 'jeeva')"
                          class="px-2 py-1 rounded text-base bg-blue-100 text-blue-800 whitespace-nowrap"
                        >
                          Jeeva
                        </span>
                        <span
                          v-if="hasService(request, 'wellsoft')"
                          class="px-2 py-1 rounded text-base bg-green-100 text-green-800 whitespace-nowrap"
                        >
                          Wellsoft
                        </span>
                        <span
                          v-if="hasService(request, 'internet')"
                          class="px-2 py-1 rounded text-base bg-cyan-100 text-cyan-800 whitespace-nowrap"
                        >
                          Internet
                        </span>
                      </div>
                    </td>

                    <!-- Requester Name & PF Number -->
                    <td class="px-4 py-3">
                      <div class="text-white font-medium text-lg">
                        {{ request.staff_name || request.full_name || 'Unknown User' }}
                      </div>
                      <div class="text-blue-300 text-base">
                        {{ request.phone || request.phone_number || 'No phone' }}
                      </div>
                      <div v-if="request.pf_number" class="text-teal-300 text-base">
                        PF: {{ request.pf_number }}
                      </div>
                    </td>

                    <!-- Head of IT Approval Date -->
                    <td class="px-4 py-3">
                      <div class="text-white font-medium text-lg">
                        {{
                          formatDate(
                            request.head_of_it_approval_date || request.head_of_it_approved_at
                          )
                        }}
                      </div>
                      <div class="text-blue-300 text-base">
                        {{
                          formatTime(
                            request.head_of_it_approval_date || request.head_of_it_approved_at
                          )
                        }}
                      </div>
                    </td>

                    <!-- Status -->
                    <td class="px-4 py-3">
                      <div class="flex flex-col">
                        <span
                          :class="getStatusBadgeClass(request.status)"
                          class="rounded text-lg font-medium inline-block whitespace-nowrap"
                          :style="{ padding: '6px 12px', width: 'fit-content' }"
                        >
                          {{ getStatusText(request.status) }}
                        </span>
                      </div>
                    </td>

                    <!-- SMS Status -->
                    <td class="px-4 py-3">
                      <div class="flex items-center space-x-2">
                        <div
                          class="w-3 h-3 rounded-full"
                          :class="getSmsStatusColor(getRelevantSmsStatus(request))"
                        ></div>
                        <span
                          class="text-base font-medium"
                          :class="getSmsStatusTextColor(getRelevantSmsStatus(request))"
                        >
                          {{ getSmsStatusText(getRelevantSmsStatus(request)) }}
                        </span>
                      </div>
                    </td>

                    <!-- Actions -->
                    <td class="px-4 py-3 text-center" style="overflow: visible">
                      <div class="relative inline-block" style="overflow: visible">
                        <!-- Three-dot menu button -->
                        <button
                          @click="toggleDropdown(request.id, $event, request)"
                          class="w-8 h-8 flex items-center justify-center text-white hover:bg-white/10 rounded-full transition-colors duration-200"
                          :title="
                            'Actions for ' +
                            (request.request_id || 'REQ-' + request.id.toString().padStart(6, '0'))
                          "
                        >
                          <i class="fas fa-ellipsis-v text-lg"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Empty State -->
            <div v-if="filteredRequests.length === 0" class="text-center py-12">
              <h3 class="text-white text-xl font-medium mb-2">No requests found</h3>
              <p class="text-blue-300 text-base">
                {{
                  searchQuery || statusFilter
                    ? 'No requests match your current filters.'
                    : 'No access requests approved by Head of IT are currently available.'
                }}
              </p>
            </div>

            <!-- Pagination -->
            <div v-if="filteredRequests.length > 0" class="px-4 py-3 border-t border-blue-300/30">
              <div class="text-blue-300 text-base">
                Showing {{ filteredRequests.length }} of {{ pagination.total }} requests
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>

    <!-- Dropdown menu portal (teleported to body to avoid all overflow issues) -->
    <Teleport to="body">
      <div
        v-if="activeDropdown !== null"
        class="fixed w-48 bg-white rounded-lg shadow-2xl border border-gray-200 py-1"
        :style="getDropdownPosition()"
        style="z-index: 99999"
        @click.stop
      >
        <!-- Conditional actions based on status -->
        <template v-if="selectedRequest && isUnassigned(selectedRequest.status)">
          <!-- Available for Assignment - show Approve and View Timeline -->
          <button
            @click="handleMenuAction('viewRequest', selectedRequest)"
            class="w-full text-left px-4 py-2 text-sm bg-green-50 text-green-800 border-b border-green-200 hover:bg-green-100 focus:bg-green-100 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-inset transition-all duration-200 flex items-center group first:rounded-t-lg"
          >
            <i
              class="fas fa-check mr-3 text-green-600 group-hover:text-green-700 group-focus:text-green-700 transition-colors duration-200"
            ></i>
            <span class="font-medium text-green-800">Approve</span>
          </button>

          <button
            @click="handleMenuAction('viewTimeline', selectedRequest)"
            class="w-full text-left px-4 py-2 text-sm bg-indigo-50 text-indigo-800 hover:bg-indigo-100 focus:bg-indigo-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-inset transition-all duration-200 flex items-center group last:rounded-b-lg"
          >
            <i
              class="fas fa-history mr-3 text-indigo-600 group-hover:text-indigo-700 group-focus:text-indigo-700 transition-colors duration-200"
            ></i>
            <span class="font-medium">View Timeline</span>
          </button>
        </template>

        <template v-else-if="selectedRequest && selectedRequest.status === 'assigned_to_ict'">
          <!-- Assigned to ICT - show Approve and View Timeline -->
          <button
            @click="handleMenuAction('viewRequest', selectedRequest)"
            class="w-full text-left px-4 py-2 text-sm bg-green-50 text-green-800 border-b border-green-200 hover:bg-green-100 focus:bg-green-100 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-inset transition-all duration-200 flex items-center group first:rounded-t-lg"
          >
            <i
              class="fas fa-check mr-3 text-green-600 group-hover:text-green-700 group-focus:text-green-700 transition-colors duration-200"
            ></i>
            <span class="font-medium text-green-800">Approve</span>
          </button>

          <button
            @click="handleMenuAction('viewTimeline', selectedRequest)"
            class="w-full text-left px-4 py-2 text-sm bg-indigo-50 text-indigo-800 hover:bg-indigo-100 focus:bg-indigo-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-inset transition-all duration-200 flex items-center group last:rounded-b-lg"
          >
            <i
              class="fas fa-history mr-3 text-indigo-600 group-hover:text-indigo-700 group-focus:text-indigo-700 transition-colors duration-200"
            ></i>
            <span class="font-medium">View Timeline</span>
          </button>
        </template>

        <template
          v-else-if="selectedRequest && selectedRequest.status === 'implementation_in_progress'"
        >
          <!-- Implementation in progress - show Approve and View Timeline -->
          <button
            @click="handleMenuAction('viewRequest', selectedRequest)"
            class="w-full text-left px-4 py-2 text-sm bg-green-50 text-green-800 border-b border-green-200 hover:bg-green-100 focus:bg-green-100 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-inset transition-all duration-200 flex items-center group first:rounded-t-lg"
          >
            <i
              class="fas fa-check mr-3 text-green-600 group-hover:text-green-700 group-focus:text-green-700 transition-colors duration-200"
            ></i>
            <span class="font-medium text-green-800">Approve</span>
          </button>

          <button
            @click="handleMenuAction('viewTimeline', selectedRequest)"
            class="w-full text-left px-4 py-2 text-sm bg-indigo-50 text-indigo-800 hover:bg-indigo-100 focus:bg-indigo-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-inset transition-all duration-200 flex items-center group last:rounded-b-lg"
          >
            <i
              class="fas fa-history mr-3 text-indigo-600 group-hover:text-indigo-700 group-focus:text-indigo-700 transition-colors duration-200"
            ></i>
            <span class="font-medium">View Timeline</span>
          </button>
        </template>

        <template
          v-else-if="
            selectedRequest &&
            (selectedRequest.status === 'completed' || selectedRequest.status === 'implemented')
          "
        >
          <!-- Completed/Implemented - show View Request (read-only) and View Timeline -->
          <button
            @click="handleMenuAction('viewRequest', selectedRequest)"
            class="w-full text-left px-4 py-2 text-sm bg-blue-50 text-blue-800 border-b border-blue-200 hover:bg-blue-100 focus:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-inset transition-all duration-200 flex items-center group first:rounded-t-lg"
          >
            <i
              class="fas fa-eye mr-3 text-blue-600 group-hover:text-blue-700 group-focus:text-blue-700 transition-colors duration-200"
            ></i>
            <span class="font-medium text-blue-800">View Request</span>
          </button>

          <button
            @click="handleMenuAction('viewTimeline', selectedRequest)"
            class="w-full text-left px-4 py-2 text-sm bg-indigo-50 text-indigo-800 hover:bg-indigo-100 focus:bg-indigo-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-inset transition-all duration-200 flex items-center group last:rounded-b-lg"
          >
            <i
              class="fas fa-history mr-3 text-indigo-600 group-hover:text-indigo-700 group-focus:text-indigo-700 transition-colors duration-200"
            ></i>
            <span class="font-medium">View Timeline</span>
          </button>
        </template>

        <template v-else-if="selectedRequest">
          <!-- Fallback for cancelled or unknown statuses - show View Request and View Timeline -->
          <button
            @click="handleMenuAction('viewRequest', selectedRequest)"
            class="w-full text-left px-4 py-2 text-sm bg-blue-50 text-blue-800 border-b border-blue-200 hover:bg-blue-100 focus:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-inset transition-all duration-200 flex items-center group first:rounded-t-lg"
          >
            <i
              class="fas fa-eye mr-3 text-blue-600 group-hover:text-blue-700 group-focus:text-blue-700 transition-colors duration-200"
            ></i>
            <span class="font-medium text-blue-800">View Request</span>
          </button>

          <button
            @click="handleMenuAction('viewTimeline', selectedRequest)"
            class="w-full text-left px-4 py-2 text-sm bg-indigo-50 text-indigo-800 hover:bg-indigo-100 focus:bg-indigo-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-inset transition-all duration-200 flex items-center group last:rounded-b-lg"
          >
            <i
              class="fas fa-history mr-3 text-indigo-600 group-hover:text-indigo-700 group-focus:text-indigo-700 transition-colors duration-200"
            ></i>
            <span class="font-medium">View Timeline</span>
          </button>
        </template>
      </div>
    </Teleport>

    <AppFooter />

    <!-- Centered MNH Loading Banner -->
    <div
      v-if="isLoading"
      class="absolute inset-0 z-40 flex items-center justify-center pointer-events-none animate-fade-in"
    >
      <div class="pointer-events-auto w-full max-w-lg mx-auto">
        <!-- Main Loading Card -->
        <div
          class="relative bg-gradient-to-br from-white/20 via-blue-50/30 to-white/10 border-2 border-blue-300/50 backdrop-blur-xl rounded-3xl p-8 shadow-2xl overflow-hidden"
        >
          <!-- Animated Background Pattern -->
          <div class="absolute inset-0 opacity-10">
            <div
              class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent animate-slide-across"
            ></div>
            <div
              class="absolute top-4 left-4 w-16 h-16 rounded-full animate-float"
              style="background: rgba(255, 0, 0, 0.2)"
            ></div>
            <div
              class="absolute bottom-4 right-4 w-20 h-20 bg-blue-400/20 rounded-full animate-float"
              style="animation-delay: 1s"
            ></div>
            <div
              class="absolute top-1/2 left-8 w-12 h-12 rounded-full animate-float"
              style="background: rgba(255, 0, 0, 0.15); animation-delay: 2s"
            ></div>
          </div>

          <!-- Hospital Logos and Branding -->
          <div class="relative z-10">
            <div class="flex items-center justify-center gap-6 mb-6">
              <!-- Left Logo -->
              <div class="relative">
                <div
                  class="w-16 h-16 bg-gradient-to-br from-blue-500/30 to-teal-500/30 rounded-2xl backdrop-blur-sm border-2 border-blue-300/50 flex items-center justify-center shadow-xl hover:scale-105 transition-transform duration-300"
                >
                  <img
                    src="/assets/images/ngao2.png"
                    alt="National Shield"
                    class="max-w-12 max-h-12 object-contain drop-shadow-lg"
                  />
                </div>
                <div
                  class="absolute -inset-1 bg-gradient-to-r from-blue-400 to-teal-400 rounded-2xl opacity-20 blur-sm animate-pulse"
                ></div>
              </div>

              <!-- Center Loading Spinner -->
              <div class="relative flex flex-col items-center">
                <!-- Main Spinner -->
                <div class="relative mb-3">
                  <!-- Base circle -->
                  <div class="rounded-full h-12 w-12 border-2 border-gray-300/20"></div>
                  <!-- Red orbiting segment -->
                  <div
                    class="absolute inset-0 animate-spin rounded-full h-12 w-12 border-4 border-transparent shadow-lg"
                    style="border-top-color: #ff0000; animation-duration: 2s"
                  ></div>
                  <!-- Blue orbiting segment -->
                  <div
                    class="absolute inset-0 animate-spin rounded-full h-12 w-12 border-4 border-transparent shadow-lg"
                    style="
                      border-bottom-color: #0000d1;
                      animation-direction: reverse;
                      animation-duration: 1.5s;
                    "
                  ></div>
                  <!-- Additional red segment for more dynamic effect -->
                  <div
                    class="absolute inset-0 animate-spin rounded-full h-12 w-12 border-4 border-transparent shadow-lg"
                    style="border-right-color: rgba(255, 0, 0, 0.7); animation-duration: 2.5s"
                  ></div>
                  <!-- Additional blue segment -->
                  <div
                    class="absolute inset-0 animate-spin rounded-full h-12 w-12 border-4 border-transparent shadow-lg"
                    style="
                      border-left-color: rgba(0, 0, 209, 0.7);
                      animation-direction: reverse;
                      animation-duration: 1.8s;
                    "
                  ></div>
                </div>

                <!-- Loading Text -->
                <div class="text-center">
                  <div class="text-white text-xl font-bold tracking-wide mb-1 drop-shadow-md">
                    Loading Access Requests
                  </div>
                  <div class="text-blue-100 text-sm animate-pulse">Please wait...</div>
                </div>
              </div>

              <!-- Right Logo -->
              <div class="relative">
                <div
                  class="w-16 h-16 bg-gradient-to-br from-teal-500/30 to-blue-500/30 rounded-2xl backdrop-blur-sm border-2 border-teal-300/50 flex items-center justify-center shadow-xl hover:scale-105 transition-transform duration-300"
                >
                  <img
                    src="/assets/images/logo2.png"
                    alt="MNH Logo"
                    class="max-w-12 max-h-12 object-contain drop-shadow-lg"
                  />
                </div>
                <div
                  class="absolute -inset-1 bg-gradient-to-r from-teal-400 to-blue-400 rounded-2xl opacity-20 blur-sm animate-pulse"
                  style="animation-delay: 0.5s"
                ></div>
              </div>
            </div>

            <!-- Hospital Name and Department -->
            <div class="text-center border-t border-blue-300/30 pt-4">
              <div
                class="text-lg font-black tracking-widest mb-1 drop-shadow-sm"
                style="color: #ff0000"
              >
                MUHIMBILI NATIONAL HOSPITAL
              </div>
              <div class="text-teal-200 text-sm font-semibold tracking-wider">
                ICT ACCESS REQUESTS MANAGEMENT
              </div>
            </div>

            <!-- Progress Indicators -->
            <div class="flex justify-center gap-2 mt-4">
              <div class="w-2 h-2 rounded-full animate-bounce" style="background: #ff0000"></div>
              <div
                class="w-2 h-2 rounded-full animate-bounce"
                style="background: #0000d1; animation-delay: 0.1s"
              ></div>
              <div
                class="w-2 h-2 rounded-full animate-bounce"
                style="background: #ff0000; animation-delay: 0.2s"
              ></div>
              <div
                class="w-2 h-2 rounded-full animate-bounce"
                style="background: #0000d1; animation-delay: 0.3s"
              ></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Timeline Modal -->
    <RequestTimeline
      :show="showTimeline"
      :request-id="selectedRequestId"
      @close="closeTimeline"
      @updated="handleTimelineUpdate"
      @openUpdateProgress="openUpdateProgress"
    />

    <!-- Update Progress Modal (separate from timeline) -->
    <div
      v-if="showUpdateProgress && selectedRequest"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[10001]"
      @click.self="closeUpdateProgress"
    >
      <div class="max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <UpdateProgress
          :show="showUpdateProgress"
          :request-id="selectedRequestId"
          :request-data="selectedRequest"
          @close="closeUpdateProgress"
          @updated="handleUpdateProgressUpdate"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
  import { ref, reactive, computed, onMounted, onBeforeUnmount, nextTick } from 'vue'
  import Header from '@/components/header.vue'
  import ModernSidebar from '@/components/ModernSidebar.vue'
  import AppFooter from '@/components/footer.vue'
  import RequestTimeline from '@/components/common/RequestTimeline.vue'
  import UpdateProgress from '@/components/common/UpdateProgress.vue'
  import { useAuth } from '@/composables/useAuth'
  import ictOfficerService from '@/services/ictOfficerService'

  // State
  const accessRequests = ref([])
  const searchQuery = ref('')
  const statusFilter = ref('')
  const isLoading = ref(false)
  const stats = reactive({ unassigned: 0, assigned: 0, inProgress: 0, total: 0 })
  const error = ref(null)
  const pagination = reactive({ total: 0, currentPage: 1, perPage: 20 })

  // Timeline / dialogs
  const showTimeline = ref(false)
  const selectedRequestId = ref(null)
  const selectedRequest = ref(null)
  const showUpdateProgress = ref(false)

  // Dropdown
  const activeDropdown = ref(null)
  const dropdownPosition = ref(null)

  // Auth (called inside setup to avoid inject warnings)
  const { ROLES, requireRole, user, userRole, ensureInitialized } = useAuth()

  // Helpers
  const isUnassigned = (status) => status === 'head_of_it_approved' || !status

  const filteredRequests = computed(() => {
    if (!Array.isArray(accessRequests.value)) {
      console.warn('IctAccessRequests: accessRequests is not an array:', accessRequests.value)
      return []
    }

    let filtered = accessRequests.value

    if (searchQuery.value) {
      const query = searchQuery.value.toLowerCase()
      filtered = filtered.filter(
        (request) =>
          (request.staff_name || request.full_name || '').toLowerCase().includes(query) ||
          (request.pf_number || '').toLowerCase().includes(query) ||
          (request.department_name || request.department || '').toLowerCase().includes(query) ||
          (request.request_id || '').toLowerCase().includes(query)
      )
    }

    if (statusFilter.value) {
      if (statusFilter.value === 'unassigned') {
        filtered = filtered.filter((r) => isUnassigned(r.status))
      } else {
        filtered = filtered.filter((r) => r.status === statusFilter.value)
      }
    }

    return filtered.sort((a, b) => {
      const aUnassigned = isUnassigned(a.status)
      const bUnassigned = isUnassigned(b.status)
      if (aUnassigned && !bUnassigned) return -1
      if (!aUnassigned && bUnassigned) return 1

      const getRelevantDate = (r) =>
        new Date(r.head_of_it_approval_date || r.head_of_it_approved_at || r.created_at || 0)
      return getRelevantDate(a) - getRelevantDate(b)
    })
  })

  async function fetchAccessRequests() {
    console.log('🔄 IctAccessRequests: Starting to fetch access requests...')
    isLoading.value = true
    error.value = null
    try {
      const result = await ictOfficerService.getAccessRequests()
      if (result.success) {
        const payload = result.data
        const list = Array.isArray(payload)
          ? payload
          : payload && Array.isArray(payload.data)
            ? payload.data
            : []
        const total = Array.isArray(payload) ? payload.length : (payload?.total ?? list.length)
        const currentPage = payload?.current_page ?? 1
        const perPage = payload?.per_page ?? list.length

        console.log('✅ IctAccessRequests: Requests loaded successfully:', list.length, {
          total,
          currentPage,
          perPage
        })
        accessRequests.value = list
        pagination.total = total
        pagination.currentPage = currentPage
        pagination.perPage = perPage

        const pendingForICT = accessRequests.value.filter((r) => isUnassigned(r.status)).length
        stats.unassigned = pendingForICT
        stats.assigned = accessRequests.value.filter((r) => r.status === 'assigned_to_ict').length
        stats.inProgress = accessRequests.value.filter(
          (r) => r.status === 'implementation_in_progress'
        ).length
        stats.total = accessRequests.value.length

        setPendingCountOverride(pendingForICT)
        setTimeout(() => diagnoseNotificationDiscrepancy(), 1000)
      } else {
        console.error('❌ IctAccessRequests: Failed to load requests:', result.message)
        error.value = result.message
        // Don't clear existing data on error - keep old data visible
      }
    } catch (err) {
      console.error('❌ IctAccessRequests: Error loading requests:', err)
      error.value = 'Network error while loading access requests. Please check your connection.'
      // Don't clear existing data on error - keep old data visible
    } finally {
      isLoading.value = false
    }
  }

  async function refreshRequests() {
    console.log('🔄 IctAccessRequests: Refreshing requests...')
    await fetchAccessRequests()
  }

  async function viewRequest(request) {
    console.log('👁️ IctAccessRequests: Viewing request details:', request.id)
    try {
      if (ensureInitialized && typeof ensureInitialized === 'function') {
        await ensureInitialized()
      }
      const role =
        (typeof userRole === 'function' ? userRole() : userRole?.value) ||
        user?.value?.primary_role ||
        user?.value?.role
      if (role !== ROLES.ICT_OFFICER) {
        console.warn('Blocked navigation: current role is not ICT_OFFICER', { role })
        window.location.href = '/ict-dashboard'
        await nextTick()
        alert('You do not have permission to process this request.')
        return
      }
      const targetId = request.id
      if (!targetId) {
        console.warn('Missing id on request, cannot open form.', request)
        await nextTick()
        alert('Cannot open form: missing request id.')
        return
      }
      window.location.href = `/ict-dashboard/both-service-form/${targetId}`
    } catch (e) {
      console.error('Failed to open request processing page:', e)
      await nextTick()
      alert('Unable to open the request. Please try again.')
    }
  }

  async function assignTask(request) {
    console.log('📋 IctAccessRequests: Assigning task for request:', request.id)
    try {
      const result = await ictOfficerService.assignTaskToSelf(
        request.id,
        'Task assigned by ICT Officer'
      )
      if (result.success) {
        await fetchAccessRequests()
        refreshNotificationBadge()
        alert('Task assigned successfully!')
      } else {
        alert('Failed to assign task: ' + result.message)
      }
    } catch (error) {
      console.error('Error assigning task:', error)
      alert('Network error while assigning task')
    }
  }

  async function updateProgress(request) {
    console.log('📈 IctAccessRequests: Updating progress for request:', request.id)
    try {
      const result = await ictOfficerService.updateProgress(
        request.id,
        'implementation_in_progress',
        'Implementation started'
      )
      if (result.success) {
        await fetchAccessRequests()
        refreshNotificationBadge()
        alert('Progress updated successfully!')
      } else {
        alert('Failed to update progress: ' + result.message)
      }
    } catch (error) {
      console.error('Error updating progress:', error)
      alert('Network error while updating progress')
    }
  }

  async function cancelTask(request) {
    console.log('❌ IctAccessRequests: Canceling task for request:', request.id)
    const reason = prompt('Please provide a reason for canceling this task:')
    if (reason && reason.trim()) {
      try {
        const result = await ictOfficerService.cancelTask(request.id, reason)
        if (result.success) {
          await fetchAccessRequests()
          refreshNotificationBadge()
          alert('Task canceled successfully!')
        } else {
          alert('Failed to cancel task: ' + result.message)
        }
      } catch (error) {
        console.error('Error canceling task:', error)
        alert('Network error while canceling task')
      }
    }
  }

  function hasService(request, serviceType) {
    if (serviceType === 'jeeva') {
      return (
        request.jeeva_access_required ||
        (request.request_types && request.request_types.includes('jeeva'))
      )
    }
    if (serviceType === 'wellsoft') {
      return (
        request.wellsoft_access_required ||
        (request.request_types && request.request_types.includes('wellsoft'))
      )
    }
    if (serviceType === 'internet') {
      return (
        request.internet_access_required ||
        (request.request_types && request.request_types.includes('internet'))
      )
    }
    return false
  }

  function getRequestType(request) {
    const services = []
    if (hasService(request, 'jeeva')) services.push('Jeeva')
    if (hasService(request, 'wellsoft')) services.push('Wellsoft')
    if (hasService(request, 'internet')) services.push('Internet')
    return services.length > 0 ? services.join(' + ') : 'Access Request'
  }

  function getStatusBadgeClass(status) {
    const statusClasses = {
      head_of_it_approved: 'bg-yellow-500 text-yellow-900',
      assigned_to_ict: 'bg-blue-500 text-blue-900',
      implementation_in_progress: 'bg-purple-500 text-purple-900',
      completed: 'bg-emerald-500 text-emerald-900',
      implemented: 'bg-green-500 text-green-900',
      cancelled: 'bg-red-500 text-red-900'
    }
    return statusClasses[status] || 'bg-gray-500 text-gray-900'
  }

  function getStatusText(status) {
    const statusTexts = {
      head_of_it_approved: 'Pending Implementation',
      assigned_to_ict: 'Assigned to ICT Officer',
      implementation_in_progress: 'Implementation in Progress',
      completed: 'Implementation Completed',
      implemented: 'Access Granted',
      cancelled: 'Task Cancelled'
    }
    return (
      statusTexts[status] ||
      status?.replace(/_/g, ' ').replace(/\b\w/g, (l) => l.toUpperCase()) ||
      'Unknown Status'
    )
  }

  // SMS Status helper functions
  function getRelevantSmsStatus(request) {
    // Only show SMS status if access has been granted (implemented/completed)
    if (request.status === 'implemented' || request.status === 'completed') {
      return request.sms_to_requester_status || 'pending'
    }
    // Otherwise, show pending (access not yet granted)
    return 'pending'
  }

  function getSmsStatusText(smsStatus) {
    const statusMap = {
      sent: 'Delivered',
      pending: 'Pending',
      failed: 'Failed'
    }
    return statusMap[smsStatus] || 'Pending'
  }

  function getSmsStatusColor(smsStatus) {
    const colorMap = {
      sent: 'bg-green-500',
      pending: 'bg-yellow-500',
      failed: 'bg-red-500'
    }
    return colorMap[smsStatus] || 'bg-gray-400'
  }

  function getSmsStatusTextColor(smsStatus) {
    const textColorMap = {
      sent: 'text-green-400',
      pending: 'text-yellow-400',
      failed: 'text-red-400'
    }
    return textColorMap[smsStatus] || 'text-gray-400'
  }

  function viewTimeline(request) {
    console.log('📅 IctAccessRequests: Opening timeline for request:', request.id)
    selectedRequestId.value = request.id
    selectedRequest.value = request
    showTimeline.value = true
  }

  function closeTimeline() {
    console.log('📅 IctAccessRequests: Closing timeline modal')
    showTimeline.value = false
    selectedRequestId.value = null
    selectedRequest.value = null
    showUpdateProgress.value = false
  }

  async function handleTimelineUpdate() {
    console.log('🔄 IctAccessRequests: Timeline updated, refreshing requests list...')
    await fetchAccessRequests()
    refreshNotificationBadge()
  }

  function openUpdateProgress(request) {
    console.log('📝 IctAccessRequests: Opening update progress section for request:', request?.id)
    if (request && canShowUpdateProgress(request)) {
      selectedRequest.value = request
      selectedRequestId.value = request.id
      showUpdateProgress.value = true
    }
  }

  function closeUpdateProgress() {
    console.log('📝 IctAccessRequests: Closing update progress section')
    showUpdateProgress.value = false
  }

  async function handleUpdateProgressUpdate() {
    console.log('🔄 IctAccessRequests: Progress updated, refreshing data...')
    await fetchAccessRequests()
    refreshNotificationBadge()
    closeTimeline()
    setTimeout(() => refreshNotificationBadge(), 3000)
    setTimeout(() => refreshNotificationBadge(), 5000)
  }

  function canShowUpdateProgress(request) {
    if (!request) return false
    const currentUserId = user.value?.id
    return (
      request.ict_officer_user_id &&
      request.ict_officer_user_id === currentUserId &&
      !request.ict_officer_implemented_at &&
      request.ict_officer_status !== 'implemented' &&
      request.ict_officer_status !== 'rejected' &&
      !['implemented', 'completed'].includes(request.status) &&
      (request.status === 'assigned_to_ict' || request.status === 'implementation_in_progress')
    )
  }

  function toggleDropdown(requestId, event, request) {
    if (activeDropdown.value === requestId) {
      activeDropdown.value = null
      selectedRequest.value = null
    } else {
      activeDropdown.value = requestId
      selectedRequest.value = request

      if (event) {
        const button = event.target.closest('button')
        if (button) {
          const rect = button.getBoundingClientRect()

          // Always show above the button
          dropdownPosition.value = {
            top: rect.top + window.scrollY,
            right: window.innerWidth - rect.right - window.scrollX,
            showAbove: true
          }
        }
      }
    }
  }

  function getDropdownPosition() {
    if (dropdownPosition.value) {
      const dropdownWidth = 192
      const dropdownHeight = 120 // Reduced from 200 to actual height
      let { top, right, showAbove } = dropdownPosition.value

      // Adjust horizontal position if too close to left edge
      if (right + dropdownWidth > window.innerWidth) {
        right = Math.max(4, window.innerWidth - dropdownWidth - 4)
      }

      // If showing above, adjust top position to be just above the button
      if (showAbove) {
        top = top - dropdownHeight
        // Ensure it doesn't go above viewport
        if (top < window.scrollY + 4) {
          top = window.scrollY + 4
        }
      }

      return { top: `${top}px`, right: `${right}px`, left: 'auto' }
    }
    return { top: '100%', right: '0', left: 'auto', position: 'absolute' }
  }

  function handleClickOutside(event) {
    if (
      activeDropdown.value &&
      !event.target.closest('[style*="z-index: 9999"]') &&
      !event.target.closest('button[title*="Actions for"]')
    ) {
      activeDropdown.value = null
      dropdownPosition.value = null
    }
  }

  function handleMenuAction(action, request) {
    activeDropdown.value = null
    dropdownPosition.value = null
    switch (action) {
      case 'viewRequest':
        viewRequest(request)
        break
      case 'viewTimeline':
        viewTimeline(request)
        break
      case 'assignTask':
        assignTask(request)
        break
      case 'updateProgress':
        updateProgress(request)
        break
      case 'cancelTask':
        cancelTask(request)
        break
      default:
        console.warn('Unknown action:', action)
    }
  }

  function formatDate(dateString) {
    if (!dateString) return 'N/A'
    try {
      return new Date(dateString).toLocaleDateString('en-GB')
    } catch {
      return 'Invalid Date'
    }
  }

  function formatTime(dateString) {
    if (!dateString) return 'N/A'
    try {
      return new Date(dateString).toLocaleTimeString('en-GB', {
        hour: '2-digit',
        minute: '2-digit'
      })
    } catch {
      return 'Invalid Time'
    }
  }

  function refreshNotificationBadge() {
    try {
      console.log('🔔 AccessRequests: Triggering notification badge refresh')
      const currentPendingCount = accessRequests.value.filter((r) => isUnassigned(r.status)).length
      setPendingCountOverride(currentPendingCount)
      clearNotificationCache()
      if (window.dispatchEvent) {
        const event = new CustomEvent('force-refresh-notifications', {
          detail: { source: 'AccessRequests', reason: 'request_updated', timestamp: Date.now() }
        })
        window.dispatchEvent(event)
        console.log('🚀 AccessRequests: Dispatched force-refresh-notifications event')
      }
      if (window.sidebarInstance && window.sidebarInstance.fetchNotificationCounts) {
        console.log('📞 AccessRequests: Calling sidebar fetchNotificationCounts directly')
        window.sidebarInstance.fetchNotificationCounts(true)
      }
    } catch (e) {
      console.warn('Failed to refresh notification badge:', e)
    }
  }

  function clearNotificationCache() {
    try {
      import('@/services/notificationService').then((m) => {
        m.default.clearCache()
        console.log('🗑️ AccessRequests: Cleared notification service cache')
      })
    } catch (e) {
      console.warn('Failed to clear notification cache:', e)
    }
  }

  function setPendingCountOverride(count) {
    try {
      console.log('🎠 AccessRequests: Setting pending count override:', count)
      if (typeof window !== 'undefined') {
        window.accessRequestsPendingCount = count
      }
      if (window.dispatchEvent) {
        const event = new CustomEvent('ict-access-requests-pending-count', {
          detail: { count, source: 'AccessRequests', timestamp: Date.now() }
        })
        window.dispatchEvent(event)
      }
      if (window.sidebarInstance && window.sidebarInstance.setNotificationCount) {
        window.sidebarInstance.setNotificationCount('/ict-dashboard/access-requests', count)
      }
      console.log('📻 AccessRequests: Emitted pending count override event')
    } catch (e) {
      console.warn('Failed to set pending count override:', e)
    }
  }

  async function diagnoseNotificationDiscrepancy() {
    try {
      console.log('🔍 DIAGNOSTIC: Comparing notification APIs...')
      const notificationService = (await import('@/services/notificationService')).default
      const universalResult = await notificationService.getPendingRequestsCount(true)
      const ictResult = await ictOfficerService.getPendingRequestsCount()
      const pageStatuses = accessRequests.value.map((r) => ({
        id: r.id,
        status: r.status,
        request_id: r.request_id || `REQ-${r.id.toString().padStart(6, '0')}`
      }))
      if (process.env.NODE_ENV === 'development') {
        console.log('🔴 NOTIFICATION API COMPARISON:', {
          universal_api: {
            endpoint: '/notifications/pending-count',
            count: universalResult.total_pending,
            full_response: universalResult
          },
          ict_specific_api: {
            endpoint: '/ict-officer/pending-count',
            count: ictResult.total_pending || ictResult,
            full_response: ictResult
          },
          page_data: {
            total_requests: accessRequests.value.length,
            pending_requests: accessRequests.value.filter((r) => isUnassigned(r.status)).length,
            completed_requests: accessRequests.value.filter((r) =>
              ['completed', 'implemented'].includes(r.status)
            ).length,
            all_statuses: pageStatuses
          },
          analysis: {
            universal_vs_ict:
              universalResult.total_pending === (ictResult.total_pending || ictResult)
                ? 'MATCH'
                : 'MISMATCH',
            badge_vs_page:
              universalResult.total_pending ===
              accessRequests.value.filter((r) => isUnassigned(r.status)).length
                ? 'CONSISTENT'
                : 'INCONSISTENT'
          }
        })
      }
    } catch (e) {
      console.error('🚫 DIAGNOSTIC ERROR:', e)
    }
  }

  // Lifecycle
  onMounted(async () => {
    try {
      console.log('IctAccessRequests: Component mounted, initializing...')
      requireRole([ROLES.ICT_OFFICER])
      await fetchAccessRequests()
      console.log('IctAccessRequests: Component initialized successfully')
      refreshNotificationBadge()
      document.addEventListener('click', handleClickOutside)
    } catch (e) {
      console.error('IctAccessRequests: Error during mount:', e)
      error.value = 'Failed to initialize component: ' + e.message
      isLoading.value = false
    }
  })

  onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside)
  })
</script>

async assignTask(request) { console.log('📋 IctAccessRequests: Assigning task for request:',
request.id) try { const result = await ictOfficerService.assignTaskToSelf( request.id, 'Task
assigned by ICT Officer' ) if (result.success) { // Refresh the requests list to show updated status
await this.fetchAccessRequests() // Trigger notification badge refresh
this.refreshNotificationBadge() // Show success message (you could use a toast notification here)
alert('Task assigned successfully!') } else { alert('Failed to assign task: ' + result.message) } }
catch (error) { console.error('Error assigning task:', error) alert('Network error while assigning
task') } }, async updateProgress(request) { console.log('📈 IctAccessRequests: Updating progress for
request:', request.id) // For now, just set to in progress status // In a real application, you
might want to show a modal to capture progress details try { const result = await
ictOfficerService.updateProgress( request.id, 'implementation_in_progress', 'Implementation started'
) if (result.success) { // Refresh the requests list to show updated status await
this.fetchAccessRequests() // Trigger notification badge refresh this.refreshNotificationBadge()
alert('Progress updated successfully!') } else { alert('Failed to update progress: ' +
result.message) } } catch (error) { console.error('Error updating progress:', error) alert('Network
error while updating progress') } }, async cancelTask(request) { console.log('❌ IctAccessRequests:
Canceling task for request:', request.id) // Show confirmation dialog and handle cancellation const
reason = prompt('Please provide a reason for canceling this task:') if (reason && reason.trim()) {
try { const result = await ictOfficerService.cancelTask(request.id, reason) if (result.success) { //
Refresh the requests list to show updated status await this.fetchAccessRequests() // Trigger
notification badge refresh this.refreshNotificationBadge() alert('Task canceled successfully!') }
else { alert('Failed to cancel task: ' + result.message) } } catch (error) { console.error('Error
canceling task:', error) alert('Network error while canceling task') } } }, isUnassigned(status) {
// Status indicating request is available for assignment return status === 'head_of_it_approved' ||
!status }, hasService(request, serviceType) { // Check if request includes a specific service type
if (serviceType === 'jeeva') { return ( request.jeeva_access_required || (request.request_types &&
request.request_types.includes('jeeva')) ) } if (serviceType === 'wellsoft') { return (
request.wellsoft_access_required || (request.request_types &&
request.request_types.includes('wellsoft')) ) } if (serviceType === 'internet') { return (
request.internet_access_required || (request.request_types &&
request.request_types.includes('internet')) ) } return false }, getRequestType(request) { const
services = [] if (this.hasService(request, 'jeeva')) services.push('Jeeva') if
(this.hasService(request, 'wellsoft')) services.push('Wellsoft') if (this.hasService(request,
'internet')) services.push('Internet') return services.length > 0 ? services.join(' + ') : 'Access
Request' }, getStatusBadgeClass(status) { const statusClasses = { head_of_it_approved:
'bg-yellow-500 text-yellow-900', assigned_to_ict: 'bg-blue-500 text-blue-900',
implementation_in_progress: 'bg-purple-500 text-purple-900', completed: 'bg-emerald-500
text-emerald-900', implemented: 'bg-green-500 text-green-900', // Add styling for 'implemented'
status cancelled: 'bg-red-500 text-red-900' } return statusClasses[status] || 'bg-gray-500
text-gray-900' }, getStatusText(status) { const statusTexts = { head_of_it_approved: 'Available for
Assignment', assigned_to_ict: 'Assigned to ICT Officer', implementation_in_progress: 'Implementation
in Progress', completed: 'Implementation Completed', implemented: 'Access Granted', // Add mapping
for 'implemented' status cancelled: 'Task Cancelled' } return ( statusTexts[status] ||
status?.replace(/_/g, ' ').replace(/\b\w/g, (l) => l.toUpperCase()) || 'Unknown Status' ) },
viewTimeline(request) { console.log('📅 IctAccessRequests: Opening timeline for request:',
request.id) this.selectedRequestId = request.id this.selectedRequest = request this.showTimeline =
true }, closeTimeline() { console.log('📅 IctAccessRequests: Closing timeline modal')
this.showTimeline = false this.selectedRequestId = null this.selectedRequest = null
this.showUpdateProgress = false }, async handleTimelineUpdate() { console.log('🔄 IctAccessRequests:
Timeline updated, refreshing requests list...') await this.fetchAccessRequests() // Also refresh the
notification badge this.refreshNotificationBadge() }, openUpdateProgress(request) { console.log( '📝
IctAccessRequests: Opening update progress section for request:', request?.id ) if (request &&
this.canShowUpdateProgress(request)) { this.selectedRequest = request this.selectedRequestId =
request.id this.showUpdateProgress = true } }, closeUpdateProgress() { console.log('📝
IctAccessRequests: Closing update progress section') this.showUpdateProgress = false }, async
handleUpdateProgressUpdate() { console.log('🔄 IctAccessRequests: Progress updated, refreshing
data...') // Refresh the requests list await this.fetchAccessRequests() // Force immediate
notification badge refresh this.refreshNotificationBadge() // Close both timeline and update
progress this.closeTimeline() // Additional delayed refresh to ensure backend sync setTimeout(() =>
{ console.log('⏰ IctAccessRequests: Delayed refresh after progress update')
this.refreshNotificationBadge() }, 3000) // Extra refresh specifically for implemented/completed
status changes setTimeout(() => { console.log( '🔄 IctAccessRequests: Final refresh to ensure
implemented/completed requests are excluded from badge count' ) this.refreshNotificationBadge() },
5000) }, canShowUpdateProgress(request) { // Only show update progress for ICT Officer assigned
requests that are not completed if (!request) return false // Get current user from auth const {
user } = useAuth() const currentUserId = user.value?.id return ( request.ict_officer_user_id &&
request.ict_officer_user_id === currentUserId && !request.ict_officer_implemented_at &&
request.ict_officer_status !== 'implemented' && request.ict_officer_status !== 'rejected' &&
!['implemented', 'completed'].includes(request.status) && // Don't allow updates for completed
requests (request.status === 'assigned_to_ict' || request.status === 'implementation_in_progress') )
}, // Dropdown functionality toggleDropdown(requestId, event) { this.activeDropdown =
this.activeDropdown === requestId ? null : requestId if (this.activeDropdown === requestId && event)
{ // Store the button position for dropdown positioning const button =
event.target.closest('button') if (button) { const rect = button.getBoundingClientRect()
this.dropdownPosition = { top: rect.bottom + window.scrollY + 4, right: window.innerWidth -
rect.right - window.scrollX } } } }, getDropdownPosition() { if (this.dropdownPosition) { // Check
if dropdown would go off screen const dropdownWidth = 192 // w-48 = 12rem = 192px const
dropdownHeight = 200 // estimated height for dropdown let { top, right } = this.dropdownPosition //
Adjust horizontal position if too close to left edge if (right + dropdownWidth > window.innerWidth)
{ right = Math.max(4, window.innerWidth - dropdownWidth - 4) } // Adjust vertical position if too
close to bottom if (top + dropdownHeight > window.innerHeight + window.scrollY) { top = Math.max(4,
top - dropdownHeight - 32) // 32px for button height } return { top: `${top}px`, right:
`${right}px`, left: 'auto' } } // Fallback positioning return { top: '100%', right: '0', left:
'auto', position: 'absolute' } }, handleClickOutside(event) { // Close dropdown if clicking outside
the dropdown or three-dot button if ( this.activeDropdown &&
!event.target.closest('[style*="z-index: 9999"]') && !event.target.closest('button[title*="Actions
for"]') ) { this.activeDropdown = null this.dropdownPosition = null } }, handleMenuAction(action,
request) { // Close dropdown first this.activeDropdown = null this.dropdownPosition = null //
Execute the appropriate action switch (action) { case 'viewRequest': this.viewRequest(request) break
case 'viewTimeline': this.viewTimeline(request) break case 'assignTask': this.assignTask(request)
break case 'updateProgress': this.updateProgress(request) break case 'cancelTask':
this.cancelTask(request) break default: console.warn('Unknown action:', action) } },
formatDate(dateString) { if (!dateString) return 'N/A' try { return new
Date(dateString).toLocaleDateString('en-GB') } catch { return 'Invalid Date' } },
formatTime(dateString) { if (!dateString) return 'N/A' try { return new
Date(dateString).toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit' }) } catch {
return 'Invalid Time' } }, // Refresh notification badge in sidebar refreshNotificationBadge() { try
{ console.log('🔔 AccessRequests: Triggering notification badge refresh') // Method -1: Update our
override count first const currentPendingCount = this.accessRequests.filter((r) =>
this.isUnassigned(r.status) ).length this.setPendingCountOverride(currentPendingCount) // Method 0:
Clear notification service cache first this.clearNotificationCache() // Method 1: Trigger force
refresh event for immediate update if (window.dispatchEvent) { const event = new
CustomEvent('force-refresh-notifications', { detail: { source: 'AccessRequests', reason:
'request_updated', timestamp: Date.now() } }) window.dispatchEvent(event) console.log('🚀
AccessRequests: Dispatched force-refresh-notifications event') } // Method 2: Direct call to sidebar
instance with force flag if (window.sidebarInstance &&
window.sidebarInstance.fetchNotificationCounts) { console.log('📞 AccessRequests: Calling sidebar
fetchNotificationCounts directly') window.sidebarInstance.fetchNotificationCounts(true) // force
refresh } } catch (error) { console.warn('Failed to refresh notification badge:', error) } }, //
Clear notification cache to ensure fresh data clearNotificationCache() { try { // Import and clear
notification service cache dynamically import('@/services/notificationService').then((module) => {
module.default.clearCache() console.log('🗑️ AccessRequests: Cleared notification service cache') })
} catch (error) { console.warn('Failed to clear notification cache:', error) } }, // Set global
pending count override for accurate sidebar badge setPendingCountOverride(count) { try {
console.log('🎠 AccessRequests: Setting pending count override:', count) // Method 1: Global window
variable for sidebar to read if (typeof window !== 'undefined') { window.accessRequestsPendingCount
= count } // Method 2: Custom event for real-time updates if (window.dispatchEvent) { const event =
new CustomEvent('ict-access-requests-pending-count', { detail: { count, source: 'AccessRequests',
timestamp: Date.now() } }) window.dispatchEvent(event) } // Method 3: Direct global sidebar method
call if (window.sidebarInstance && window.sidebarInstance.setNotificationCount) {
window.sidebarInstance.setNotificationCount('/ict-dashboard/access-requests', count) }
console.log('📻 AccessRequests: Emitted pending count override event') } catch (error) {
console.warn('Failed to set pending count override:', error) } }, // DIAGNOSTIC: Compare
notification APIs to find discrepancy async diagnoseNotificationDiscrepancy() { try {
console.log('🔍 DIAGNOSTIC: Comparing notification APIs...') // Get universal notification count
const notificationService = (await import('@/services/notificationService')).default const
universalResult = await notificationService.getPendingRequestsCount(true) // Get ICT Officer
specific count const ictResult = await ictOfficerService.getPendingRequestsCount() // Get current
page data const pageStatuses = this.accessRequests.map((r) => ({ id: r.id, status: r.status,
request_id: r.request_id || `REQ-${r.id.toString().padStart(6, '0')}` })) if (process.env.NODE_ENV
=== 'development') { console.log('🔴 NOTIFICATION API COMPARISON:', { universal_api: { endpoint:
'/notifications/pending-count', count: universalResult.total_pending, full_response: universalResult
}, ict_specific_api: { endpoint: '/ict-officer/pending-count', count: ictResult.total_pending ||
ictResult, full_response: ictResult }, page_data: { total_requests: this.accessRequests.length,
pending_requests: this.accessRequests.filter((r) => this.isUnassigned(r.status)) .length,
completed_requests: this.accessRequests.filter((r) => ['completed',
'implemented'].includes(r.status) ).length, all_statuses: pageStatuses }, analysis: {
universal_vs_ict: universalResult.total_pending === (ictResult.total_pending || ictResult) ? 'MATCH'
: 'MISMATCH', badge_vs_page: universalResult.total_pending === this.accessRequests.filter((r) =>
this.isUnassigned(r.status)).length ? 'CONSISTENT' : 'INCONSISTENT' } }) } } catch (error) {
console.error('🚫 DIAGNOSTIC ERROR:', error) }
<style scoped>
  .sidebar-narrow {
    flex-shrink: 0;
  }

  /* Animations */
  @keyframes float {
    0%,
    100% {
      transform: translateY(0px);
    }
    50% {
      transform: translateY(-15px);
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

  /* Sliding background animation for loading banner */
  @keyframes slide-across {
    0% {
      transform: translateX(-100%);
    }
    100% {
      transform: translateX(100%);
    }
  }

  .animate-float {
    animation: float 6s ease-in-out infinite;
  }

  .animate-fade-in {
    animation: fade-in 1s ease-out;
  }

  .animate-slide-across {
    animation: slide-across 2.5s ease-in-out infinite;
  }
</style>
