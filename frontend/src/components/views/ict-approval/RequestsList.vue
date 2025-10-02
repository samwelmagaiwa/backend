<template>
  <div class="flex flex-col h-screen">
    <Header />
    <div class="flex flex-1 overflow-hidden">
      <ModernSidebar />
      <main
        class="flex-1 p-4 bg-gradient-to-br from-blue-900 via-blue-800 to-teal-900 overflow-y-auto relative"
      >
        <!-- Background Pattern -->
        <div class="absolute inset-0 overflow-hidden">
          <div class="absolute inset-0">
            <div
              v-for="i in 15"
              :key="i"
              class="absolute text-white opacity-5 animate-float"
              :style="{
                left: Math.random() * 100 + '%',
                top: Math.random() * 100 + '%',
                animationDelay: Math.random() * 3 + 's',
                animationDuration: Math.random() * 3 + 2 + 's',
                fontSize: Math.random() * 15 + 8 + 'px'
              }"
            >
              <i
                :class="[
                  'fas',
                  ['fa-laptop', 'fa-tv', 'fa-desktop', 'fa-keyboard', 'fa-mouse', 'fa-headphones'][
                    Math.floor(Math.random() * 6)
                  ]
                ]"
              ></i>
            </div>
          </div>
        </div>

        <!-- Centered MNH Loading Banner -->
        <div v-if="isLoading" class="absolute inset-0 z-40 flex items-center justify-center pointer-events-none animate-fade-in">
          <div class="pointer-events-auto w-full max-w-lg mx-auto">
            <!-- Main Loading Card -->
            <div class="relative bg-gradient-to-br from-white/20 via-blue-50/30 to-white/10 border-2 border-blue-300/50 backdrop-blur-xl rounded-3xl p-8 shadow-2xl overflow-hidden">
              <!-- Animated Background Pattern -->
              <div class="absolute inset-0 opacity-10">
                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent animate-slide-across"></div>
                <div class="absolute top-4 left-4 w-16 h-16 bg-blue-400/20 rounded-full animate-float"></div>
                <div class="absolute bottom-4 right-4 w-20 h-20 bg-teal-400/20 rounded-full animate-float" style="animation-delay: 1s"></div>
                <div class="absolute top-1/2 left-8 w-12 h-12 bg-blue-300/15 rounded-full animate-float" style="animation-delay: 2s"></div>
              </div>

              <!-- Hospital Logos and Branding -->
              <div class="relative z-10">
                <div class="flex items-center justify-center gap-6 mb-6">
                  <!-- Left Logo -->
                  <div class="relative">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500/30 to-teal-500/30 rounded-2xl backdrop-blur-sm border-2 border-blue-300/50 flex items-center justify-center shadow-xl hover:scale-105 transition-transform duration-300">
                      <img src="/assets/images/ngao2.png" alt="National Shield" class="max-w-12 max-h-12 object-contain drop-shadow-lg" />
                    </div>
                    <div class="absolute -inset-1 bg-gradient-to-r from-blue-400 to-teal-400 rounded-2xl opacity-20 blur-sm animate-pulse"></div>
                  </div>

                  <!-- Center Loading Spinner -->
                  <div class="relative flex flex-col items-center">
                    <!-- Main Spinner -->
                    <div class="relative mb-3">
                      <div class="animate-spin rounded-full h-12 w-12 border-4 border-teal-300/40 border-t-teal-300 shadow-lg"></div>
                      <div class="absolute inset-0 animate-spin rounded-full h-12 w-12 border-4 border-transparent border-r-blue-300" style="animation-direction: reverse; animation-duration: 1.5s"></div>
                    </div>
                    
                    <!-- Loading Text -->
                    <div class="text-center">
                      <div class="text-white text-xl font-bold tracking-wide mb-1 drop-shadow-md">
                        Loading Device Requests
                      </div>
                      <div class="text-blue-100 text-sm animate-pulse">
                        Please wait...
                      </div>
                    </div>
                  </div>

                  <!-- Right Logo -->
                  <div class="relative">
                    <div class="w-16 h-16 bg-gradient-to-br from-teal-500/30 to-blue-500/30 rounded-2xl backdrop-blur-sm border-2 border-teal-300/50 flex items-center justify-center shadow-xl hover:scale-105 transition-transform duration-300">
                      <img src="/assets/images/logo2.png" alt="MNH Logo" class="max-w-12 max-h-12 object-contain drop-shadow-lg" />
                    </div>
                    <div class="absolute -inset-1 bg-gradient-to-r from-teal-400 to-blue-400 rounded-2xl opacity-20 blur-sm animate-pulse" style="animation-delay: 0.5s"></div>
                  </div>
                </div>

                <!-- Hospital Name and Department -->
                <div class="text-center border-t border-blue-300/30 pt-4">
                  <div class="text-red-400 text-lg font-black tracking-widest mb-1 drop-shadow-sm">
                    MUHIMBILI NATIONAL HOSPITAL
                  </div>
                  <div class="text-teal-200 text-sm font-semibold tracking-wider">
                    ICT DEVICE REQUESTS MANAGEMENT
                  </div>
                </div>

                <!-- Progress Indicators -->
                <div class="flex justify-center gap-2 mt-4">
                  <div class="w-2 h-2 bg-blue-400 rounded-full animate-bounce"></div>
                  <div class="w-2 h-2 bg-teal-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                  <div class="w-2 h-2 bg-blue-300 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                  <div class="w-2 h-2 bg-teal-300 rounded-full animate-bounce" style="animation-delay: 0.3s"></div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="max-w-full mx-auto px-2 relative z-10">
          <!-- Header Section -->
          <div
            class="booking-glass-card rounded-t-3xl p-6 mb-0 border-b border-blue-300/30 animate-fade-in"
          >
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
                  class="text-2xl font-bold text-white mb-4 tracking-wide drop-shadow-lg animate-fade-in"
                >
                  MUHIMBILI NATIONAL HOSPITAL
                </h1>
                <div class="relative inline-block mb-4">
                  <div
                    class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-2 rounded-full text-base font-bold shadow-2xl transform hover:scale-105 transition-all duration-300 border-2 border-blue-400/60"
                  >
                    <span class="relative z-10 flex items-center gap-3">
                      <i class="fas fa-clipboard-list text-base"></i>
                      ICT DEVICE REQUESTS MANAGEMENT
                    </span>
                    <div
                      class="absolute inset-0 bg-gradient-to-r from-blue-700 to-blue-800 rounded-full opacity-0 hover:opacity-100 transition-opacity duration-300"
                    ></div>
                  </div>
                </div>
                <h2
                  class="text-lg font-bold text-blue-100 tracking-wide drop-shadow-md animate-fade-in-delay"
                >
                  ICT OFFICER DASHBOARD
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
          <div class="booking-glass-card rounded-b-3xl overflow-hidden animate-slide-up">
            <div class="p-6">
              <!-- Stats Cards -->
              <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div
                  class="bg-gradient-to-r from-blue-600/25 to-teal-600/25 border-2 border-blue-400/40 p-6 rounded-2xl backdrop-blur-sm hover:shadow-xl hover:shadow-blue-500/20 transition-all duration-500"
                >
                  <div class="flex items-center">
                    <div
                      class="w-12 h-12 bg-gradient-to-br from-blue-500 to-teal-600 rounded-lg flex items-center justify-center shadow-lg"
                    >
                      <i class="fas fa-clock text-white text-lg"></i>
                    </div>
                    <div class="ml-4">
                      <p class="text-sm font-semibold text-blue-200">Pending</p>
                      <p class="text-2xl font-bold text-white">
                        {{ stats.pending }}
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
                      <p class="text-sm font-semibold text-green-200">Approved</p>
                      <p class="text-2xl font-bold text-white">
                        {{ stats.approved }}
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
                      <p class="text-sm font-semibold text-red-200">Rejected</p>
                      <p class="text-2xl font-bold text-white">
                        {{ stats.rejected }}
                      </p>
                    </div>
                  </div>
                </div>

                <div
                  class="bg-gradient-to-r from-purple-600/25 to-indigo-600/25 border-2 border-purple-400/40 p-6 rounded-2xl backdrop-blur-sm hover:shadow-xl hover:shadow-purple-500/20 transition-all duration-500"
                >
                  <div class="flex items-center">
                    <div
                      class="w-12 h-12 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-lg flex items-center justify-center shadow-lg"
                    >
                      <i class="fas fa-list text-white text-lg"></i>
                    </div>
                    <div class="ml-4">
                      <p class="text-sm font-semibold text-purple-200">Total</p>
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
                        placeholder="Search by borrower name, department, or device..."
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
                      v-model="ictStatusFilter"
                      class="px-4 py-3 bg-white/15 border border-blue-300/30 rounded-lg focus:border-teal-400 focus:outline-none text-white backdrop-blur-sm transition-all appearance-none cursor-pointer text-sm"
                    >
                      <option value="">All ICT Status</option>
                      <option value="pending">Pending ICT</option>
                      <option value="approved">Approved</option>
                      <option value="rejected">Rejected</option>
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

              <!-- Bulk Actions Toolbar -->
              <div
                v-if="selectedRequests.length > 0"
                class="bg-gradient-to-r from-rose-600/25 to-red-600/25 border-2 border-red-400/40 rounded-2xl backdrop-blur-sm overflow-hidden mb-4 animate-slide-up"
              >
                <div class="p-4 flex items-center justify-between">
                  <div class="flex items-center">
                    <div
                      class="w-10 h-10 bg-red-500 rounded-lg flex items-center justify-center mr-3"
                    >
                      <i class="fas fa-trash text-white"></i>
                    </div>
                    <div>
                      <h3 class="text-lg font-bold text-white">
                        {{ selectedRequests.length }} Request{{
                          selectedRequests.length > 1 ? 's' : ''
                        }}
                        Selected
                      </h3>
                      <p class="text-sm text-red-200">Ready for bulk deletion</p>
                    </div>
                  </div>
                  <div class="flex items-center space-x-3">
                    <button
                      @click="clearSelection"
                      class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors duration-200 text-sm"
                    >
                      <i class="fas fa-times mr-2"></i>
                      Clear Selection
                    </button>
                    <button
                      @click="bulkDeleteRequests"
                      class="px-4 py-2 bg-gradient-to-r from-red-600 to-rose-600 text-white rounded-lg hover:from-red-700 hover:to-rose-700 transition-all duration-200 text-sm font-medium shadow-lg hover:shadow-xl transform hover:scale-105"
                    >
                      <i class="fas fa-trash mr-2"></i>
                      Delete Selected ({{ selectedRequests.length }})
                    </button>
                  </div>
                </div>
              </div>

              <!-- Requests Table -->
              <div
                class="bg-gradient-to-r from-blue-600/25 to-teal-600/25 border-2 border-blue-400/40 rounded-2xl backdrop-blur-sm overflow-hidden"
              >
                <div class="p-6 border-b border-blue-300/30">
                  <h3 class="text-xl font-bold text-white flex items-center">
                    <i class="fas fa-table mr-3 text-teal-300"></i>
                    Device Borrowing Requests
                    <span
                      v-if="selectedRequests.length > 0"
                      class="ml-3 px-3 py-1 bg-red-500 text-white rounded-full text-sm"
                    >
                      {{ selectedRequests.length }} selected
                    </span>
                  </h3>
                </div>

                <div class="overflow-x-auto">
                  <table class="w-full">
                    <thead class="bg-blue-800/50">
                      <tr>
                        <th
                          class="px-4 py-4 text-left text-sm font-semibold text-blue-100 uppercase tracking-wider"
                        >
                          <div class="flex items-center">
                            <input
                              type="checkbox"
                              v-model="selectAll"
                              @change="toggleSelectAll"
                              class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 mr-3"
                            />
                            <i class="fas fa-check-square mr-2"></i>Select
                          </div>
                        </th>
                        <th
                          class="px-6 py-4 text-left text-sm font-semibold text-blue-100 uppercase tracking-wider"
                        >
                          <i class="fas fa-hashtag mr-2"></i>Request ID
                        </th>
                        <th
                          class="px-6 py-4 text-left text-sm font-semibold text-blue-100 uppercase tracking-wider"
                        >
                          <i class="fas fa-user mr-2"></i>Borrower
                        </th>
                        <th
                          class="px-6 py-4 text-left text-sm font-semibold text-blue-100 uppercase tracking-wider"
                        >
                          <i class="fas fa-building mr-2"></i>Department
                        </th>
                        <th
                          class="px-6 py-4 text-left text-sm font-semibold text-blue-100 uppercase tracking-wider"
                        >
                          <i class="fas fa-laptop mr-2"></i>Device
                        </th>
                        <th
                          class="px-6 py-4 text-left text-sm font-semibold text-blue-100 uppercase tracking-wider"
                        >
                          <i class="fas fa-calendar mr-2"></i>Booking Date
                        </th>
                        <th
                          class="px-6 py-4 text-left text-sm font-semibold text-blue-100 uppercase tracking-wider"
                        >
                          <i class="fas fa-signature mr-2"></i>Signature
                        </th>
                        <th
                          class="px-6 py-4 text-left text-sm font-semibold text-blue-100 uppercase tracking-wider"
                        >
                          <i class="fas fa-flag mr-2"></i>Status
                        </th>
                        <th
                          class="px-6 py-4 text-left text-sm font-semibold text-blue-100 uppercase tracking-wider"
                        >
                          <i class="fas fa-undo mr-2"></i>Return Status
                        </th>
                        <th
                          class="px-6 py-4 text-center text-sm font-semibold text-blue-100 uppercase tracking-wider"
                        >
                          <i class="fas fa-cogs mr-2"></i>Actions
                        </th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-blue-300/20">
                      <tr
                        v-for="request in filteredRequests"
                        :key="request.id"
                        class="hover:bg-blue-700/30 transition-colors duration-200"
                        :class="{
                          'bg-blue-600/20': selectedRequests.includes(request.id)
                        }"
                      >
                        <td class="px-4 py-4 whitespace-nowrap">
                          <div class="flex items-center">
                            <input
                              type="checkbox"
                              :value="request.id"
                              v-model="selectedRequests"
                              :disabled="!canDeleteRequest(request)"
                              class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                              :class="{
                                'opacity-50 cursor-not-allowed': !canDeleteRequest(request)
                              }"
                            />
                          </div>
                        </td>
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
                        <td class="px-6 py-4 whitespace-nowrap">
                          <div class="flex items-center">
                            <div
                              class="w-10 h-10 bg-gradient-to-br from-teal-500 to-blue-600 rounded-full flex items-center justify-center shadow-lg mr-3"
                            >
                              <i class="fas fa-user text-white text-sm"></i>
                            </div>
                            <div>
                              <div class="text-base font-medium text-white">
                                {{
                                  request.borrower_name || request.borrowerName || 'Unknown User'
                                }}
                              </div>
                              <div class="text-sm text-blue-300">
                                {{ request.borrower_phone || request.phoneNumber || 'No phone' }}
                              </div>
                              <div v-if="request.pf_number" class="text-sm text-teal-300">
                                PF: {{ request.pf_number }}
                              </div>
                            </div>
                          </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                          <div class="text-base text-white font-medium">
                            {{ request.department || 'Unknown Department' }}
                          </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                          <div class="flex items-center">
                            <i class="fas fa-laptop text-teal-300 mr-2"></i>
                            <div class="flex flex-col">
                              <span class="text-base text-white font-medium">{{
                                request.device_name ||
                                getDeviceDisplayName(
                                  request.device_type || request.deviceType,
                                  request.custom_device || request.customDevice
                                )
                              }}</span>
                              <span
                                v-if="
                                  request.device_available === false && request.device_inventory_id
                                "
                                class="text-xs text-red-300 font-medium"
                              >
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                Device No Longer Available
                              </span>
                            </div>
                          </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                          <div class="text-base text-white font-medium">
                            {{ formatDate(request.booking_date || request.bookingDate) }}
                          </div>
                          <div class="text-sm text-blue-300">
                            Return:
                            {{ formatDate(request.collection_date || request.collectionDate) }}
                          </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                          <div class="flex items-center">
                            <div
                              v-if="request.has_signature || request.signature"
                              class="flex items-center text-green-400"
                            >
                              <i class="fas fa-check-circle mr-2"></i>
                              <span class="text-sm font-medium">Signed</span>
                            </div>
                            <div v-else class="flex items-center text-red-400">
                              <i class="fas fa-times-circle mr-2"></i>
                              <span class="text-sm font-medium">Not Signed</span>
                            </div>
                          </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                          <span
                            :class="getStatusBadgeClass(request.ict_approve || 'pending')"
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium"
                          >
                            <i
                              :class="getStatusIcon(request.ict_approve || 'pending')"
                              class="mr-1"
                            ></i>
                            {{ getStatusText(request.ict_approve || 'pending') }}
                          </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                          <span
                            :class="
                              getReturnStatusBadgeClass(request.return_status || 'not_yet_returned')
                            "
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium"
                          >
                            <i
                              :class="
                                getReturnStatusIcon(request.return_status || 'not_yet_returned')
                              "
                              class="mr-1"
                            ></i>
                            {{ getReturnStatusText(request.return_status || 'not_yet_returned') }}
                          </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                          <div class="relative inline-block">
                            <!-- Compact Actions Dropdown Button -->
                            <button
                              @click="toggleActionsDropdown(request.id)"
                              class="relative inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white text-sm font-medium rounded-lg hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:ring-offset-1 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105 group overflow-hidden"
                            >
                              <!-- Animated Background Gradient -->
                              <div
                                class="absolute inset-0 bg-gradient-to-r from-blue-500 via-cyan-500 to-blue-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                              ></div>

                              <!-- Shimmer Effect -->
                              <div
                                class="absolute inset-0 -skew-x-12 bg-gradient-to-r from-transparent via-white/20 to-transparent translate-x-[-100%] group-hover:translate-x-[200%] transition-transform duration-600"
                              ></div>

                              <!-- Button Content -->
                              <div class="relative z-10 flex items-center">
                                <i class="fas fa-eye text-sm mr-2 group-hover:animate-pulse"></i>
                                <span class="font-medium">Actions</span>
                                <i
                                  class="fas fa-chevron-down ml-2 text-xs transition-all duration-200 group-hover:text-cyan-200"
                                  :class="{
                                    'rotate-180 text-cyan-300': openDropdown === request.id
                                  }"
                                ></i>
                              </div>

                              <!-- Pulse Ring Effect -->
                              <div
                                class="absolute inset-0 rounded-lg border border-blue-400/50 group-hover:border-cyan-400/70 transition-colors duration-200"
                              ></div>
                            </button>

                            <!-- Enhanced Actions Dropdown Menu -->
                            <transition
                              enter-active-class="transition ease-out duration-300"
                              enter-from-class="opacity-0 scale-90 translate-y-2 rotate-3"
                              enter-to-class="opacity-100 scale-100 translate-y-0 rotate-0"
                              leave-active-class="transition ease-in duration-200"
                              leave-from-class="opacity-100 scale-100 translate-y-0 rotate-0"
                              leave-to-class="opacity-0 scale-90 translate-y-2 rotate-3"
                            >
                              <div
                                v-if="openDropdown === request.id"
                                class="absolute right-0 mt-2 w-52 bg-gradient-to-br from-white via-gray-50 to-blue-50 rounded-xl shadow-xl border border-blue-200/50 z-50 overflow-hidden backdrop-blur-sm"
                                style="
                                  box-shadow:
                                    0 10px 25px -5px rgba(0, 0, 0, 0.15),
                                    0 0 0 1px rgba(59, 130, 246, 0.1),
                                    inset 0 1px 0 rgba(255, 255, 255, 0.1);
                                "
                              >
                                <!-- Compact View Action -->
                                <router-link
                                  :to="`/ict-approval/request/${request.id}`"
                                  @click="closeDropdown"
                                  class="relative flex items-center px-2 py-1.5 text-xs bg-gradient-to-r from-blue-500 to-blue-600 text-white hover:from-blue-600 hover:to-blue-700 transition-all duration-200 group overflow-hidden border-b border-blue-400/30 rounded-sm"
                                >
                                  <!-- Shimmer Effect -->
                                  <div
                                    class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent translate-x-[-100%] group-hover:translate-x-[200%] transition-transform duration-500"
                                  ></div>

                                  <!-- Icon -->
                                  <i class="fas fa-eye text-xs mr-2 relative z-10"></i>

                                  <!-- Content -->
                                  <span class="font-medium relative z-10">View Details</span>

                                  <!-- Arrow Icon -->
                                  <i
                                    class="fas fa-arrow-right text-xs ml-auto opacity-0 group-hover:opacity-100 transition-opacity duration-200 relative z-10"
                                  ></i>
                                </router-link>

                                <!-- Compact Reject Action -->
                                <button
                                  @click="rejectRequest(request.id)"
                                  class="relative w-full flex items-center px-2 py-1.5 text-xs bg-gradient-to-r from-orange-500 to-orange-600 text-white hover:from-orange-600 hover:to-orange-700 transition-all duration-200 group overflow-hidden border-b border-orange-400/30 rounded-sm"
                                  :disabled="request.ict_approve === 'rejected'"
                                  :class="{
                                    'opacity-50 cursor-not-allowed':
                                      request.ict_approve === 'rejected'
                                  }"
                                >
                                  <!-- Shimmer Effect -->
                                  <div
                                    class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent translate-x-[-100%] group-hover:translate-x-[200%] transition-transform duration-500"
                                  ></div>

                                  <!-- Icon -->
                                  <i class="fas fa-times text-xs mr-2 relative z-10"></i>

                                  <!-- Content -->
                                  <span class="font-medium relative z-10">Reject Request</span>

                                  <!-- Warning Icon -->
                                  <i
                                    class="fas fa-ban text-xs ml-auto opacity-0 group-hover:opacity-100 transition-opacity duration-200 relative z-10"
                                  ></i>
                                </button>

                                <!-- Compact Delete Action -->
                                <button
                                  @click="deleteRequest(request.id)"
                                  class="relative w-full flex items-center px-2 py-1.5 text-xs bg-gradient-to-r from-red-600 to-red-700 text-white hover:from-red-700 hover:to-red-800 transition-all duration-200 group overflow-hidden rounded-sm"
                                  :disabled="!canDeleteRequest(request)"
                                  :class="{
                                    'opacity-50 cursor-not-allowed': !canDeleteRequest(request)
                                  }"
                                >
                                  <!-- Shimmer Effect -->
                                  <div
                                    class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent translate-x-[-100%] group-hover:translate-x-[200%] transition-transform duration-500"
                                  ></div>

                                  <!-- Icon -->
                                  <i class="fas fa-trash text-xs mr-2 relative z-10"></i>

                                  <!-- Content -->
                                  <span class="font-medium relative z-10">Delete Request</span>

                                  <!-- Danger Icon -->
                                  <i
                                    class="fas fa-skull-crossbones text-xs ml-auto opacity-0 group-hover:opacity-100 transition-opacity duration-200 relative z-10"
                                  ></i>
                                </button>
                              </div>
                            </transition>
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
                        searchQuery || ictStatusFilter
                          ? 'Try adjusting your filters'
                          : 'No device borrowing requests have been submitted yet.'
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

  </div>
</template>

<script>
  import Header from '@/components/header.vue'
  import ModernSidebar from '@/components/ModernSidebar.vue'
  import AppFooter from '@/components/footer.vue'
  import deviceBorrowingService from '@/services/deviceBorrowingService'

  export default {
    name: 'RequestsList',
    components: {
      Header,
      ModernSidebar,
      AppFooter
    },
    setup() {
      // Sidebar state now managed by Pinia - no local state needed

      return {
        // No local state needed for sidebar
      }
    },
    data() {
      return {
        requests: [],
        searchQuery: '',
        ictStatusFilter: '',
        deviceTypeFilter: '',
        departmentFilter: '',
        isLoading: false,
        openDropdown: null,
        isProcessing: false,
        stats: {
          pending: 0,
          approved: 0,
          rejected: 0,
          returned: 0,
          compromised: 0,
          total: 0
        },
        showApprovalModal: false,
        approvalNotes: '',
        selectedRequestId: null,
        actionType: null, // 'approve', 'reject', or 'delete'
        searchDebounce: null,
        // Bulk selection properties
        selectedRequests: [],
        selectAll: false,
        // Error handling
        error: null
      }
    },
    computed: {
      filteredRequests() {
        let filtered = this.requests

        // Filter by search query
        if (this.searchQuery) {
          const query = this.searchQuery.toLowerCase()
          filtered = filtered.filter(
            (request) =>
              (request.borrower_name || request.borrowerName || '').toLowerCase().includes(query) ||
              (request.department || '').toLowerCase().includes(query) ||
              (
                request.device_name ||
                this.getDeviceDisplayName(
                  request.device_type || request.deviceType,
                  request.custom_device || request.customDevice
                )
              )
                .toLowerCase()
                .includes(query) ||
              (request.request_id || '').toLowerCase().includes(query)
          )
        }

        // Filter by ICT status (client-side filtering for better responsiveness)
        if (this.ictStatusFilter) {
          filtered = filtered.filter(
            (request) => (request.ict_approve || 'pending') === this.ictStatusFilter
          )
        }

        return filtered
      }
    },
    async mounted() {
      try {
        console.log('RequestsList: Component mounted, initializing...')
        await this.fetchRequests()
        console.log('RequestsList: Component initialized successfully')
      } catch (error) {
        console.error('RequestsList: Error during mount:', error)
        this.error = 'Failed to initialize component: ' + error.message
        this.isLoading = false
      }
    },
    watch: {
      // Watch for filter changes and refresh data
      ictStatusFilter: {
        handler() {
          this.fetchRequests()
        },
        immediate: false
      },
      searchQuery: {
        handler() {
          // Debounce search to avoid too many API calls
          clearTimeout(this.searchDebounce)
          this.searchDebounce = setTimeout(() => {
            this.fetchRequests()
          }, 300)
        },
        immediate: false
      }
    },
    beforeUnmount() {
      // Clean up debounce timer
      if (this.searchDebounce) {
        clearTimeout(this.searchDebounce)
      }
    },
    methods: {
      async fetchRequests() {
        this.isLoading = true
        this.error = null

        try {
          console.log('Fetching device borrowing requests...')

          // Fetch all device borrowing requests for ICT approval from booking_service table
          const response = await deviceBorrowingService.getAllRequests({
            search: this.searchQuery || undefined,
            device_type: this.deviceTypeFilter || undefined,
            department: this.departmentFilter || undefined,
            ict_status: this.ictStatusFilter || undefined,
            per_page: 50
          })

          console.log('Service response:', response)

          if (response.success) {
            this.requests = response.data.data || []
            console.log('Device borrowing requests loaded:', this.requests.length)
            console.log('Raw requests data:', this.requests)

            // Also fetch statistics
            await this.fetchStatistics()
          } else {
            console.error('Failed to fetch requests:', response.message)
            throw new Error(response.message || 'Failed to fetch requests')
          }
        } catch (error) {
          console.error('Error fetching requests:', error)
          this.error =
            error.message ||
            'Unable to load device borrowing requests. Please check your connection and try again.'
          this.requests = []
          this.calculateStats()
        } finally {
          this.isLoading = false
        }
      },

      async fetchStatistics() {
        // For now, calculate stats from loaded requests
        // In future, we can add a dedicated API endpoint
        this.calculateStats()
      },

      calculateStats() {
        this.stats = {
          pending: this.requests.filter((r) => r.ict_approve === 'pending').length,
          approved: this.requests.filter((r) => r.ict_approve === 'approved').length,
          rejected: this.requests.filter((r) => r.ict_approve === 'rejected').length,
          returned: this.requests.filter((r) => r.status === 'returned').length,
          compromised: this.requests.filter((r) => r.status === 'compromised').length,
          total: this.requests.length
        }
      },

      async refreshRequests() {
        await this.fetchRequests()
      },

      viewRequest(requestId) {
        this.$router.push(`/ict-approval/request/${requestId}`)
      },

      getDeviceDisplayName(deviceType, customDevice) {
        return deviceBorrowingService.getDeviceDisplayName(deviceType, customDevice)
      },

      formatDate(dateString) {
        return deviceBorrowingService.formatDate(dateString)
      },

      getStatusBadgeClass(status) {
        return deviceBorrowingService.getStatusBadgeClass(status)
      },

      getStatusIcon(status) {
        return deviceBorrowingService.getStatusIcon(status)
      },

      getStatusText(status) {
        return deviceBorrowingService.getStatusText(status)
      },

      // Return status helpers
      getReturnStatusBadgeClass(status) {
        return deviceBorrowingService.getReturnStatusBadgeClass(status)
      },
      getReturnStatusIcon(status) {
        return deviceBorrowingService.getReturnStatusIcon(status)
      },
      getReturnStatusText(status) {
        return deviceBorrowingService.getReturnStatusText(status)
      },

      // Dropdown methods
      toggleActionsDropdown(requestId) {
        this.openDropdown = this.openDropdown === requestId ? null : requestId
      },

      closeDropdown() {
        this.openDropdown = null
      },

      // Action methods

      async rejectRequest(requestId) {
        try {
          this.selectedRequestId = requestId
          this.actionType = 'reject'

          const confirmed = confirm(
            'Are you sure you want to reject this device borrowing request?'
          )
          if (!confirmed) return

          // Get rejection reason (required)
          const reason = prompt('Please provide a rejection reason (required):')
          if (!reason || reason.trim() === '') {
            alert('Rejection reason is required')
            return
          }

          this.isProcessing = true
          this.closeDropdown()

          const response = await deviceBorrowingService.rejectRequest(requestId, reason)

          if (response.success) {
            // Update local request status
            const requestIndex = this.requests.findIndex((req) => req.id === requestId)
            if (requestIndex !== -1) {
              this.requests[requestIndex].ict_approve = 'rejected'
              this.requests[requestIndex].ict_notes = reason
              this.requests[requestIndex].ict_approved_at = new Date().toISOString()
            }

            this.calculateStats()
            alert('Device borrowing request rejected successfully!')
            console.log('Request rejected:', requestId)
          } else {
            throw new Error(response.message || 'Failed to reject request')
          }
        } catch (error) {
          console.error('Error rejecting request:', error)
          alert('Error rejecting request: ' + error.message)
        } finally {
          this.isProcessing = false
          this.selectedRequestId = null
          this.actionType = null
        }
      },

      async deleteRequest(requestId) {
        try {
          this.selectedRequestId = requestId
          this.actionType = 'delete'

          const confirmed = confirm(
            'Are you sure you want to permanently delete this device borrowing request? This action cannot be undone.'
          )
          if (!confirmed) return

          this.isProcessing = true
          this.closeDropdown()

          const response = await deviceBorrowingService.deleteRequest(requestId)

          if (response.success) {
            // Remove request from local array
            const requestIndex = this.requests.findIndex((req) => req.id === requestId)
            if (requestIndex !== -1) {
              this.requests.splice(requestIndex, 1)
            }

            // Remove from selected requests if it was selected
            this.selectedRequests = this.selectedRequests.filter((id) => id !== requestId)

            this.calculateStats()
            alert('Device borrowing request deleted successfully!')
            console.log('Request deleted:', requestId)
          } else {
            throw new Error(response.message || 'Failed to delete request')
          }
        } catch (error) {
          console.error('Error deleting request:', error)
          alert('Error deleting request: ' + error.message)
        } finally {
          this.isProcessing = false
          this.selectedRequestId = null
          this.actionType = null
        }
      },

      // Bulk selection methods
      toggleSelectAll() {
        if (this.selectAll) {
          // Select all deletable requests
          this.selectedRequests = this.filteredRequests
            .filter((request) => this.canDeleteRequest(request))
            .map((request) => request.id)
        } else {
          // Deselect all
          this.selectedRequests = []
        }
      },

      clearSelection() {
        this.selectedRequests = []
        this.selectAll = false
      },

      canDeleteRequest(request) {
        return deviceBorrowingService.canDeleteRequest(request)
      },

      async bulkDeleteRequests() {
        try {
          if (this.selectedRequests.length === 0) {
            alert('No requests selected for deletion.')
            return
          }

          const confirmed = confirm(
            `Are you sure you want to permanently delete ${this.selectedRequests.length} selected request${this.selectedRequests.length > 1 ? 's' : ''}? This action cannot be undone.`
          )
          if (!confirmed) return

          this.isProcessing = true
          this.actionType = 'bulk-delete'

          const response = await deviceBorrowingService.bulkDeleteRequests(this.selectedRequests)

          if (response.success) {
            const deletedCount = response.data.deleted_count || 0
            const failedCount = response.data.failed_count || 0

            // Remove successfully deleted requests from local array
            if (response.data.deleted_requests) {
              const deletedIds = response.data.deleted_requests.map((req) => req.id)
              this.requests = this.requests.filter((req) => !deletedIds.includes(req.id))
            }

            // Clear selection
            this.clearSelection()

            this.calculateStats()

            let message = `Bulk deletion completed. ${deletedCount} request${deletedCount > 1 ? 's' : ''} deleted successfully.`
            if (failedCount > 0) {
              message += ` ${failedCount} request${failedCount > 1 ? 's' : ''} could not be deleted.`
            }

            alert(message)
            console.log('Bulk deletion completed:', response.data)

            // Show details of failed deletions if any
            if (failedCount > 0 && response.data.failed_requests) {
              console.warn('Failed deletions:', response.data.failed_requests)
            }
          } else {
            throw new Error(response.message || 'Failed to delete requests')
          }
        } catch (error) {
          console.error('Error in bulk deletion:', error)
          alert('Error deleting requests: ' + error.message)
        } finally {
          this.isProcessing = false
          this.actionType = null
        }
      }
    }
  }
</script>

<style scoped>
  /* Glass morphism effects */
  .booking-glass-card {
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

  /* Sliding background animation for loading banner */
  @keyframes slide-across {
    0% {
      transform: translateX(-100%);
    }
    100% {
      transform: translateX(100%);
    }
  }

  .animate-slide-across {
    animation: slide-across 2.5s ease-in-out infinite;
  }

  /* Focus styles for accessibility */
  input:focus,
  select:focus {
    box-shadow: 0 0 0 3px rgba(45, 212, 191, 0.1);
  }

  button:focus {
    box-shadow: 0 0 0 3px rgba(45, 212, 191, 0.3);
  }

  /* Enhanced Action Button Animations */
  @keyframes float-up {
    0% {
      transform: translateY(0px) scale(1);
    }
    50% {
      transform: translateY(-5px) scale(1.05);
    }
    100% {
      transform: translateY(0px) scale(1);
    }
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
