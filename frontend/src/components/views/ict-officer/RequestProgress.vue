<template>
  <div class="min-h-screen flex overflow-hidden">
    <!-- Modern Sidebar -->
    <ModernSidebar />

    <!-- Main Content Area -->
    <div class="flex-1 min-h-screen overflow-y-auto relative">
      <!-- Animated Background -->
      <div
        class="absolute inset-0 bg-gradient-to-br from-[#0047AB] via-[#1458d6] to-[#0047AB] overflow-hidden"
      >
        <!-- Animated Particles -->
        <div class="absolute inset-0">
          <div
            class="absolute top-1/4 left-1/4 w-2 h-2 bg-white/20 rounded-full animate-ping animation-delay-0"
          ></div>
          <div
            class="absolute top-3/4 left-3/4 w-1.5 h-1.5 bg-white/30 rounded-full animate-ping animation-delay-1000"
          ></div>
          <div
            class="absolute top-1/2 left-1/6 w-1 h-1 bg-white/25 rounded-full animate-ping animation-delay-2000"
          ></div>
          <div
            class="absolute top-1/6 right-1/4 w-2.5 h-2.5 bg-white/15 rounded-full animate-ping animation-delay-3000"
          ></div>
          <div
            class="absolute bottom-1/4 right-1/6 w-1.5 h-1.5 bg-white/20 rounded-full animate-ping animation-delay-4000"
          ></div>
        </div>
        <!-- Floating Orbs -->
        <div
          class="absolute top-20 left-20 w-32 h-32 bg-white/10 rounded-full blur-xl animate-float"
        ></div>
        <div
          class="absolute top-40 right-32 w-24 h-24 bg-white/5 rounded-full blur-2xl animate-float-delayed"
        ></div>
        <div
          class="absolute bottom-32 left-40 w-20 h-20 bg-white/8 rounded-full blur-lg animate-float-slow"
        ></div>
        <!-- Geometric Patterns -->
        <div class="absolute inset-0 opacity-10">
          <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
            <defs>
              <pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">
                <path
                  d="M 10 0 L 0 0 0 10"
                  fill="none"
                  stroke="white"
                  stroke-width="0.5"
                  opacity="0.3"
                />
              </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#grid)" />
          </svg>
        </div>
      </div>

      <!-- Content Overlay -->
      <div
        class="relative z-10 bg-gradient-to-br from-white/5 via-transparent to-white/5 backdrop-blur-[1px]"
      >
        <!-- Spectacular Navigation Header -->
        <div
          class="relative bg-gradient-to-r from-[#0047AB] via-[#1458d6] to-[#0047AB] shadow-2xl overflow-hidden"
        >
          <!-- Header Background Effects -->
          <div class="absolute inset-0">
            <div
              class="absolute top-0 left-0 w-full h-full bg-gradient-to-r from-white/5 via-transparent to-white/5"
            ></div>
            <div
              class="absolute -top-10 -left-10 w-20 h-20 bg-white/10 rounded-full blur-xl animate-pulse"
            ></div>
            <div
              class="absolute -top-5 right-1/4 w-16 h-16 bg-white/5 rounded-full blur-lg animate-pulse animation-delay-1000"
            ></div>
            <div
              class="absolute top-0 right-0 w-32 h-32 bg-gradient-radial from-white/10 to-transparent rounded-full transform translate-x-8 -translate-y-8"
            ></div>
          </div>

          <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-24">
              <div class="flex items-center space-x-8">
                <button
                  @click="goBack"
                  class="group relative flex items-center space-x-3 bg-white/15 backdrop-blur-md hover:bg-white/25 px-6 py-3 rounded-2xl transition-all duration-500 transform hover:scale-110 hover:shadow-2xl text-white border border-white/20 hover:border-white/30"
                >
                  <div
                    class="absolute inset-0 bg-gradient-to-r from-white/5 to-transparent rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                  ></div>
                  <svg
                    class="w-5 h-5 transition-all duration-500 group-hover:-translate-x-2 group-hover:scale-110"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2.5"
                      d="M15 19l-7-7 7-7"
                    />
                  </svg>
                  <span class="font-semibold relative z-10">Back to Dashboard</span>
                </button>

                <div class="flex items-center space-x-4">
                  <div class="relative">
                    <div class="absolute inset-0 bg-white/20 rounded-2xl blur-sm"></div>
                    <div
                      class="relative w-14 h-14 bg-white/25 rounded-2xl flex items-center justify-center backdrop-blur-md border border-white/30 shadow-lg"
                    >
                      <svg
                        class="w-7 h-7 text-white animate-pulse"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                      </svg>
                    </div>
                  </div>
                  <div>
                    <h1 class="text-3xl font-extrabold text-white tracking-tight">
                      <span
                        class="bg-gradient-to-r from-white to-blue-100 bg-clip-text text-transparent"
                        >Implementation Progress</span
                      >
                    </h1>
                    <p class="text-blue-100/90 text-sm font-medium flex items-center mt-1">
                      <span class="w-2 h-2 bg-green-400 rounded-full mr-2 animate-pulse"></span>
                      Real-time request tracking & management
                    </p>
                  </div>
                </div>
              </div>
              <div v-if="request" class="relative group">
                <!-- Glowing Background -->
                <div
                  class="absolute inset-0 bg-gradient-to-r from-yellow-400/20 to-orange-400/20 rounded-2xl blur-lg opacity-75 group-hover:opacity-100 transition-opacity duration-300"
                ></div>

                <!-- Badge Content -->
                <div
                  class="relative bg-gradient-to-br from-white/20 to-white/10 backdrop-blur-md px-6 py-4 rounded-2xl border border-white/30 shadow-xl transform group-hover:scale-105 transition-all duration-300"
                >
                  <div class="flex items-center space-x-2 mb-1">
                    <div class="w-2 h-2 bg-yellow-400 rounded-full animate-pulse"></div>
                    <div class="text-blue-100 text-xs font-bold uppercase tracking-widest">
                      Request ID
                    </div>
                  </div>
                  <div class="text-white font-mono text-xl font-black tracking-wide">
                    {{ request.request_id || `REQ-${String(request.id).padStart(6, '0')}` }}
                  </div>

                  <!-- Sparkle Effect -->
                  <div class="absolute top-2 right-2">
                    <svg
                      class="w-4 h-4 text-yellow-300 animate-ping"
                      fill="currentColor"
                      viewBox="0 0 20 20"
                    >
                      <path
                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                      />
                    </svg>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
          <!-- Loading State -->
          <div v-if="isLoading" class="flex flex-col items-center justify-center py-20">
            <div class="relative">
              <div class="animate-spin rounded-full h-16 w-16 border-4 border-[#0047AB]/20"></div>
              <div
                class="animate-spin rounded-full h-16 w-16 border-4 border-t-[#0047AB] absolute top-0"
              ></div>
            </div>
            <div class="mt-6 text-center">
              <h3 class="text-lg font-semibold text-gray-900 mb-2">Loading Request Details</h3>
              <p class="text-gray-500">Please wait while we fetch the latest information...</p>
            </div>
          </div>

          <!-- Error State -->
          <div v-else-if="error" class="max-w-md mx-auto">
            <div
              class="bg-gradient-to-br from-red-50 to-red-100/50 backdrop-blur-sm border border-red-200/60 rounded-2xl p-8 text-center shadow-xl"
            >
              <div
                class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4"
              >
                <svg
                  class="w-8 h-8 text-red-600"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"
                  />
                </svg>
              </div>
              <h3 class="text-xl font-bold text-gray-900 mb-2">Unable to Load Request</h3>
              <p class="text-gray-600 mb-6">{{ error }}</p>
              <button
                @click="loadRequestDetails"
                class="bg-gradient-to-r from-[#0047AB] to-[#1458d6] text-white px-6 py-3 rounded-xl hover:from-[#003d99] hover:to-[#1250c2] transition-all duration-300 transform hover:scale-105 shadow-lg font-medium"
              >
                <svg
                  class="w-4 h-4 mr-2 inline"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                  />
                </svg>
                Try Again
              </button>
            </div>
          </div>

          <!-- Request Details -->
          <div v-else-if="request" class="space-y-8">
            <!-- Request Header -->
            <div
              class="relative bg-gradient-to-br from-white via-white to-blue-50/30 backdrop-blur-sm rounded-2xl shadow-xl border border-white/60 overflow-hidden"
            >
              <!-- Background Pattern -->
              <div class="absolute inset-0 bg-gradient-to-br from-[#0047AB]/5 to-transparent"></div>
              <div
                class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-[#0047AB]/10 to-transparent rounded-full -translate-y-8 translate-x-8"
              ></div>

              <div class="relative p-8">
                <!-- Header Section -->
                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6 mb-8">
                  <!-- Staff Info -->
                  <div class="flex items-start space-x-4">
                    <div
                      class="w-16 h-16 bg-gradient-to-br from-[#0047AB] to-[#1458d6] rounded-2xl flex items-center justify-center shadow-lg"
                    >
                      <svg
                        class="w-8 h-8 text-white"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                        />
                      </svg>
                    </div>
                    <div>
                      <h2 class="text-3xl font-bold text-gray-900 mb-1">
                        {{ request.staff_name }}
                      </h2>
                      <p class="text-gray-600 flex items-center">
                        <svg
                          class="w-4 h-4 mr-2"
                          fill="none"
                          stroke="currentColor"
                          viewBox="0 0 24 24"
                        >
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
                          />
                        </svg>
                        {{ request.department_name || 'N/A' }}
                      </p>
                    </div>
                  </div>

                  <!-- Status and Badges -->
                  <div class="flex flex-col items-start lg:items-end space-y-3">
                    <!-- Status Badge -->
                    <span
                      :class="getStatusBadgeClass(request.status)"
                      class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold shadow-sm"
                    >
                      <span class="w-2 h-2 rounded-full bg-current mr-2 animate-pulse"></span>
                      {{ getStatusText(request.status) }}
                    </span>
                    <!-- Service Type Badges -->
                    <div class="flex flex-wrap gap-2">
                      <span
                        v-if="request.jeeva_access_required"
                        class="inline-flex items-center bg-gradient-to-r from-emerald-100 to-emerald-200 text-emerald-800 px-3 py-1.5 rounded-lg text-xs font-semibold border border-emerald-200/50"
                      >
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                          <path
                            fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                          />
                        </svg>
                        Jeeva Access
                      </span>
                      <span
                        v-if="request.wellsoft_access_required"
                        class="inline-flex items-center bg-gradient-to-r from-violet-100 to-violet-200 text-violet-800 px-3 py-1.5 rounded-lg text-xs font-semibold border border-violet-200/50"
                      >
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                          <path
                            fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                          />
                        </svg>
                        Wellsoft Access
                      </span>
                      <span
                        v-if="request.internet_access_required"
                        class="inline-flex items-center bg-gradient-to-r from-blue-100 to-blue-200 text-blue-800 px-3 py-1.5 rounded-lg text-xs font-semibold border border-blue-200/50"
                      >
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                          <path
                            fill-rule="evenodd"
                            d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                          />
                        </svg>
                        Internet Access
                      </span>
                    </div>
                  </div>
                </div>

                <!-- Contact Details Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                  <div class="bg-white/50 backdrop-blur-sm rounded-xl p-4 border border-white/60">
                    <div class="flex items-center space-x-3">
                      <div
                        class="w-10 h-10 bg-[#0047AB]/10 rounded-lg flex items-center justify-center"
                      >
                        <svg
                          class="w-5 h-5 text-[#0047AB]"
                          fill="none"
                          stroke="currentColor"
                          viewBox="0 0 24 24"
                        >
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"
                          />
                        </svg>
                      </div>
                      <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">
                          PF Number
                        </p>
                        <p class="font-mono text-lg font-semibold text-gray-900">
                          {{ request.pf_number || 'N/A' }}
                        </p>
                      </div>
                    </div>
                  </div>
                  <div class="bg-white/50 backdrop-blur-sm rounded-xl p-4 border border-white/60">
                    <div class="flex items-center space-x-3">
                      <div
                        class="w-10 h-10 bg-[#0047AB]/10 rounded-lg flex items-center justify-center"
                      >
                        <svg
                          class="w-5 h-5 text-[#0047AB]"
                          fill="none"
                          stroke="currentColor"
                          viewBox="0 0 24 24"
                        >
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
                          />
                        </svg>
                      </div>
                      <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">
                          Department
                        </p>
                        <p class="text-lg font-semibold text-gray-900">
                          {{ request.department_name || 'N/A' }}
                        </p>
                      </div>
                    </div>
                  </div>
                  <div class="bg-white/50 backdrop-blur-sm rounded-xl p-4 border border-white/60">
                    <div class="flex items-center space-x-3">
                      <div
                        class="w-10 h-10 bg-[#0047AB]/10 rounded-lg flex items-center justify-center"
                      >
                        <svg
                          class="w-5 h-5 text-[#0047AB]"
                          fill="none"
                          stroke="currentColor"
                          viewBox="0 0 24 24"
                        >
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"
                          />
                        </svg>
                      </div>
                      <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">
                          Phone
                        </p>
                        <p class="text-lg font-semibold text-gray-900">
                          {{ request.phone_number || 'N/A' }}
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Progress Timeline -->
            <div
              class="relative bg-gradient-to-br from-white via-white to-slate-50/30 backdrop-blur-sm rounded-2xl shadow-xl border border-white/60 overflow-hidden"
            >
              <!-- Background Decoration -->
              <div class="absolute inset-0 bg-gradient-to-br from-[#0047AB]/3 to-transparent"></div>
              <div
                class="absolute bottom-0 left-0 w-40 h-40 bg-gradient-to-tr from-[#0047AB]/5 to-transparent rounded-full -translate-x-10 translate-y-10"
              ></div>

              <div class="relative p-8">
                <!-- Header -->
                <div class="flex items-center space-x-4 mb-8">
                  <div
                    class="w-12 h-12 bg-gradient-to-br from-[#0047AB] to-[#1458d6] rounded-xl flex items-center justify-center shadow-lg"
                  >
                    <svg
                      class="w-6 h-6 text-white"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M13 10V3L4 14h7v7l9-11h-7z"
                      />
                    </svg>
                  </div>
                  <div>
                    <h3 class="text-2xl font-bold text-gray-900">Implementation Timeline</h3>
                    <p class="text-gray-600">Track the progress of this access request</p>
                  </div>
                </div>

                <!-- Modern Timeline -->
                <div class="relative space-y-8">
                  <!-- Vertical Line -->
                  <div
                    class="absolute left-6 top-6 bottom-6 w-0.5 bg-gradient-to-b from-[#0047AB]/30 via-[#0047AB]/20 to-transparent"
                  ></div>

                  <!-- Head of IT Approved -->
                  <div
                    v-if="request.head_of_it_approval_date"
                    class="relative flex items-start space-x-6"
                  >
                    <div class="flex-shrink-0 relative z-10">
                      <div
                        class="w-12 h-12 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg border-2 border-white"
                      >
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                          <path
                            fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                          />
                        </svg>
                      </div>
                    </div>
                    <div
                      class="flex-1 bg-gradient-to-br from-emerald-50 to-emerald-100/50 backdrop-blur-sm rounded-xl p-6 border border-emerald-200/60 shadow-sm"
                    >
                      <div class="flex items-start justify-between mb-3">
                        <h4 class="text-lg font-bold text-gray-900">✅ Head of IT Approval</h4>
                        <span
                          class="bg-emerald-100 text-emerald-800 px-3 py-1 rounded-full text-sm font-semibold"
                          >Completed</span
                        >
                      </div>
                      <p class="text-emerald-700 font-medium mb-2">
                        {{ formatDateTime(request.head_of_it_approval_date) }}
                      </p>
                      <p class="text-gray-700">
                        Request has been approved by the Head of IT and is ready for ICT Officer
                        implementation.
                      </p>
                    </div>
                  </div>

                  <!-- ICT Officer Assignment -->
                  <div v-if="currentAssignment" class="relative flex items-start space-x-6">
                    <div class="flex-shrink-0 relative z-10">
                      <div
                        :class="
                          currentAssignment.status === 'assigned'
                            ? 'from-blue-400 to-blue-600'
                            : 'from-emerald-400 to-emerald-600'
                        "
                        class="w-12 h-12 bg-gradient-to-br rounded-xl flex items-center justify-center shadow-lg border-2 border-white"
                      >
                        <svg
                          v-if="currentAssignment.status === 'assigned'"
                          class="w-6 h-6 text-white"
                          fill="none"
                          stroke="currentColor"
                          viewBox="0 0 24 24"
                        >
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                          />
                        </svg>
                        <svg
                          v-else
                          class="w-6 h-6 text-white"
                          fill="currentColor"
                          viewBox="0 0 20 20"
                        >
                          <path
                            fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                          />
                        </svg>
                      </div>
                    </div>
                    <div
                      :class="
                        currentAssignment.status === 'assigned'
                          ? 'from-blue-50 to-blue-100/50 border-blue-200/60'
                          : 'from-emerald-50 to-emerald-100/50 border-emerald-200/60'
                      "
                      class="flex-1 bg-gradient-to-br backdrop-blur-sm rounded-xl p-6 border shadow-sm"
                    >
                      <div class="flex items-start justify-between mb-3">
                        <h4 class="text-lg font-bold text-gray-900">👨‍💻 ICT Officer Assignment</h4>
                        <span
                          :class="
                            currentAssignment.status === 'assigned'
                              ? 'bg-blue-100 text-blue-800'
                              : 'bg-emerald-100 text-emerald-800'
                          "
                          class="px-3 py-1 rounded-full text-sm font-semibold capitalize"
                          >{{ currentAssignment.status.replace('_', ' ') }}</span
                        >
                      </div>
                      <p
                        :class="
                          currentAssignment.status === 'assigned'
                            ? 'text-blue-700'
                            : 'text-emerald-700'
                        "
                        class="font-medium mb-2"
                      >
                        {{ formatDateTime(currentAssignment.assigned_at) }}
                      </p>
                      <p class="text-gray-700 mb-2">
                        <span class="font-semibold">Assigned to:</span>
                        {{ currentAssignment.ict_officer_name }}
                      </p>
                      <p
                        v-if="currentAssignment.assignment_notes"
                        class="text-gray-600 bg-white/50 rounded-lg p-3 border border-gray-200/50"
                      >
                        <span class="font-medium">Notes:</span>
                        {{ currentAssignment.assignment_notes }}
                      </p>
                    </div>
                  </div>

                  <!-- Implementation Started -->
                  <div
                    v-if="currentAssignment && currentAssignment.started_at"
                    class="relative flex items-start space-x-6"
                  >
                    <div class="flex-shrink-0 relative z-10">
                      <div
                        class="w-12 h-12 bg-gradient-to-br from-amber-400 to-amber-600 rounded-xl flex items-center justify-center shadow-lg border-2 border-white"
                      >
                        <svg
                          class="w-6 h-6 text-white"
                          fill="none"
                          stroke="currentColor"
                          viewBox="0 0 24 24"
                        >
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z"
                          />
                        </svg>
                      </div>
                    </div>
                    <div
                      class="flex-1 bg-gradient-to-br from-amber-50 to-amber-100/50 backdrop-blur-sm rounded-xl p-6 border border-amber-200/60 shadow-sm"
                    >
                      <div class="flex items-start justify-between mb-3">
                        <h4 class="text-lg font-bold text-gray-900">🚀 Implementation Started</h4>
                        <span
                          class="bg-amber-100 text-amber-800 px-3 py-1 rounded-full text-sm font-semibold animate-pulse"
                          >In Progress</span
                        >
                      </div>
                      <p class="text-amber-700 font-medium mb-2">
                        {{ formatDateTime(currentAssignment.started_at) }}
                      </p>
                      <p class="text-gray-700">
                        ICT Officer has begun working on implementing the requested access
                        permissions.
                      </p>
                    </div>
                  </div>

                  <!-- Implementation Completed -->
                  <div
                    v-if="currentAssignment && currentAssignment.completed_at"
                    class="relative flex items-start space-x-6"
                  >
                    <div class="flex-shrink-0 relative z-10">
                      <div
                        class="w-12 h-12 bg-gradient-to-br from-green-400 to-green-600 rounded-xl flex items-center justify-center shadow-lg border-2 border-white"
                      >
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                          <path
                            fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                          />
                        </svg>
                      </div>
                    </div>
                    <div
                      class="flex-1 bg-gradient-to-br from-green-50 to-green-100/50 backdrop-blur-sm rounded-xl p-6 border border-green-200/60 shadow-sm"
                    >
                      <div class="flex items-start justify-between mb-3">
                        <h4 class="text-lg font-bold text-gray-900">🎉 Implementation Completed</h4>
                        <span
                          class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-semibold"
                          >Completed</span
                        >
                      </div>
                      <p class="text-green-700 font-medium mb-2">
                        {{ formatDateTime(currentAssignment.completed_at) }}
                      </p>
                      <div
                        v-if="currentAssignment.completion_notes"
                        class="bg-white/50 rounded-lg p-4 border border-green-200/50 mt-3"
                      >
                        <p class="text-gray-700">
                          <span class="font-semibold text-green-800">Completion Notes:</span>
                        </p>
                        <p class="text-gray-700 mt-1">{{ currentAssignment.completion_notes }}</p>
                      </div>
                      <p v-else class="text-gray-700">
                        Access request has been successfully implemented and completed.
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- ICT Officer Actions -->
            <div
              v-if="canPerformActions"
              class="relative bg-gradient-to-br from-white via-white to-indigo-50/30 backdrop-blur-sm rounded-2xl shadow-xl border border-white/60 overflow-hidden"
            >
              <!-- Background Decoration -->
              <div class="absolute inset-0 bg-gradient-to-br from-[#0047AB]/3 to-transparent"></div>
              <div
                class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-[#0047AB]/8 to-transparent rounded-full -translate-y-8 translate-x-8"
              ></div>

              <div class="relative p-8">
                <!-- Header -->
                <div class="flex items-center space-x-4 mb-8">
                  <div
                    class="w-12 h-12 bg-gradient-to-br from-[#0047AB] to-[#1458d6] rounded-xl flex items-center justify-center shadow-lg"
                  >
                    <svg
                      class="w-6 h-6 text-white"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M13 10V3L4 14h7v7l9-11h-7z"
                      />
                    </svg>
                  </div>
                  <div>
                    <h3 class="text-2xl font-bold text-gray-900">Available Actions</h3>
                    <p class="text-gray-600">Manage and update this access request</p>
                  </div>
                </div>

                <!-- Action Buttons Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <button
                    v-if="canStartImplementation"
                    @click="startImplementation"
                    class="group relative bg-gradient-to-br from-violet-500 to-purple-600 text-white p-6 rounded-2xl hover:from-violet-600 hover:to-purple-700 transition-all duration-300 transform hover:scale-105 hover:shadow-2xl border border-violet-400/50"
                  >
                    <div
                      class="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent rounded-2xl"
                    ></div>
                    <div class="relative flex flex-col items-center text-center space-y-3">
                      <div
                        class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm group-hover:bg-white/30 transition-all duration-300"
                      >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z"
                          />
                        </svg>
                      </div>
                      <div>
                        <h4 class="font-bold text-lg">Start Implementation</h4>
                        <p class="text-violet-100 text-sm opacity-90">
                          Begin working on this request
                        </p>
                      </div>
                    </div>
                  </button>

                  <button
                    v-if="canCompleteImplementation"
                    @click="completeImplementation"
                    class="group relative bg-gradient-to-br from-emerald-500 to-green-600 text-white p-6 rounded-2xl hover:from-emerald-600 hover:to-green-700 transition-all duration-300 transform hover:scale-105 hover:shadow-2xl border border-emerald-400/50"
                  >
                    <div
                      class="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent rounded-2xl"
                    ></div>
                    <div class="relative flex flex-col items-center text-center space-y-3">
                      <div
                        class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm group-hover:bg-white/30 transition-all duration-300"
                      >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M5 13l4 4L19 7"
                          />
                        </svg>
                      </div>
                      <div>
                        <h4 class="font-bold text-lg">Complete Implementation</h4>
                        <p class="text-emerald-100 text-sm opacity-90">
                          Mark this request as completed
                        </p>
                      </div>
                    </div>
                  </button>

                  <button
                    v-if="canCancelTask"
                    @click="cancelTask"
                    class="group relative bg-gradient-to-br from-red-500 to-rose-600 text-white p-6 rounded-2xl hover:from-red-600 hover:to-rose-700 transition-all duration-300 transform hover:scale-105 hover:shadow-2xl border border-red-400/50"
                  >
                    <div
                      class="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent rounded-2xl"
                    ></div>
                    <div class="relative flex flex-col items-center text-center space-y-3">
                      <div
                        class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm group-hover:bg-white/30 transition-all duration-300"
                      >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                          />
                        </svg>
                      </div>
                      <div>
                        <h4 class="font-bold text-lg">Cancel Task</h4>
                        <p class="text-red-100 text-sm opacity-90">Cancel this implementation</p>
                      </div>
                    </div>
                  </button>
                </div>
              </div>
            </div>

            <!-- Request Details -->
            <div
              class="relative bg-gradient-to-br from-white via-white to-gray-50/30 backdrop-blur-sm rounded-2xl shadow-xl border border-white/60 overflow-hidden"
            >
              <!-- Background Decoration -->
              <div class="absolute inset-0 bg-gradient-to-br from-[#0047AB]/2 to-transparent"></div>
              <div
                class="absolute bottom-0 right-0 w-36 h-36 bg-gradient-to-tl from-[#0047AB]/5 to-transparent rounded-full translate-x-10 translate-y-10"
              ></div>

              <div class="relative p-8">
                <!-- Header -->
                <div class="flex items-center space-x-4 mb-8">
                  <div
                    class="w-12 h-12 bg-gradient-to-br from-[#0047AB] to-[#1458d6] rounded-xl flex items-center justify-center shadow-lg"
                  >
                    <svg
                      class="w-6 h-6 text-white"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                      />
                    </svg>
                  </div>
                  <div>
                    <h3 class="text-2xl font-bold text-gray-900">Access Requirements</h3>
                    <p class="text-gray-600">Detailed breakdown of requested services</p>
                  </div>
                </div>

                <!-- Service Details Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                  <!-- Internet Access Card -->
                  <div
                    v-if="request.internet_purposes"
                    class="bg-gradient-to-br from-blue-50 to-blue-100/50 backdrop-blur-sm rounded-2xl p-6 border border-blue-200/60 shadow-sm"
                  >
                    <div class="flex items-start space-x-4">
                      <div
                        class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg"
                      >
                        <svg
                          class="w-6 h-6 text-white"
                          fill="none"
                          stroke="currentColor"
                          viewBox="0 0 24 24"
                        >
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                          />
                        </svg>
                      </div>
                      <div class="flex-1">
                        <h4 class="text-lg font-bold text-gray-900 mb-2">🌐 Internet Access</h4>
                        <div class="bg-white/50 rounded-xl p-4 border border-blue-200/50">
                          <p class="text-gray-700 leading-relaxed">
                            {{ request.internet_purposes }}
                          </p>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Wellsoft Modules Card -->
                  <div
                    v-if="
                      request.wellsoft_modules_selected &&
                      request.wellsoft_modules_selected.length > 0
                    "
                    class="bg-gradient-to-br from-violet-50 to-violet-100/50 backdrop-blur-sm rounded-2xl p-6 border border-violet-200/60 shadow-sm"
                  >
                    <div class="flex items-start space-x-4">
                      <div
                        class="w-12 h-12 bg-gradient-to-br from-violet-500 to-violet-600 rounded-xl flex items-center justify-center shadow-lg"
                      >
                        <svg
                          class="w-6 h-6 text-white"
                          fill="none"
                          stroke="currentColor"
                          viewBox="0 0 24 24"
                        >
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"
                          />
                        </svg>
                      </div>
                      <div class="flex-1">
                        <h4 class="text-lg font-bold text-gray-900 mb-3">🔬 Wellsoft Modules</h4>
                        <div
                          class="bg-white/50 rounded-xl p-4 border border-violet-200/50 max-h-40 overflow-y-auto"
                        >
                          <div class="space-y-2">
                            <div
                              v-for="module in request.wellsoft_modules_selected"
                              :key="module"
                              class="flex items-center space-x-2 py-1"
                            >
                              <div class="w-2 h-2 bg-violet-500 rounded-full flex-shrink-0"></div>
                              <span class="text-gray-700 text-sm">{{ module }}</span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Jeeva Modules Card -->
                  <div
                    v-if="
                      request.jeeva_modules_selected && request.jeeva_modules_selected.length > 0
                    "
                    class="bg-gradient-to-br from-emerald-50 to-emerald-100/50 backdrop-blur-sm rounded-2xl p-6 border border-emerald-200/60 shadow-sm"
                  >
                    <div class="flex items-start space-x-4">
                      <div
                        class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg"
                      >
                        <svg
                          class="w-6 h-6 text-white"
                          fill="none"
                          stroke="currentColor"
                          viewBox="0 0 24 24"
                        >
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"
                          />
                        </svg>
                      </div>
                      <div class="flex-1">
                        <h4 class="text-lg font-bold text-gray-900 mb-3">🏥 Jeeva Modules</h4>
                        <div
                          class="bg-white/50 rounded-xl p-4 border border-emerald-200/50 max-h-40 overflow-y-auto"
                        >
                          <div class="space-y-2">
                            <div
                              v-for="module in request.jeeva_modules_selected"
                              :key="module"
                              class="flex items-center space-x-2 py-1"
                            >
                              <div class="w-2 h-2 bg-emerald-500 rounded-full flex-shrink-0"></div>
                              <span class="text-gray-700 text-sm">{{ module }}</span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Empty State -->
                <div
                  v-if="
                    !request.internet_purposes &&
                    (!request.wellsoft_modules_selected ||
                      request.wellsoft_modules_selected.length === 0) &&
                    (!request.jeeva_modules_selected || request.jeeva_modules_selected.length === 0)
                  "
                  class="text-center py-12"
                >
                  <div
                    class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4"
                  >
                    <svg
                      class="w-8 h-8 text-gray-400"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                      />
                    </svg>
                  </div>
                  <h4 class="text-lg font-semibold text-gray-900 mb-2">
                    No Service Details Available
                  </h4>
                  <p class="text-gray-500">
                    No specific access requirements have been documented for this request.
                  </p>
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
  import ictOfficerService from '../../../services/ictOfficerService'
  import ModernSidebar from '../../ModernSidebar.vue'

  export default {
    name: 'RequestProgress',
    components: {
      ModernSidebar
    },
    data() {
      return {
        request: null,
        timelineData: null,
        isLoading: true,
        error: null,
        requestId: null
      }
    },
    computed: {
      currentAssignment() {
        if (!this.timelineData || !this.timelineData.ict_assignments) return null
        // Get the most recent assignment - avoid mutating original array
        const assignments = [...this.timelineData.ict_assignments]
        return (
          assignments.sort((a, b) => new Date(b.assigned_at) - new Date(a.assigned_at))[0] || null
        )
      },

      canPerformActions() {
        return (
          this.currentAssignment &&
          this.currentAssignment.status !== 'completed' &&
          this.currentAssignment.status !== 'cancelled'
        )
      },

      canStartImplementation() {
        return this.currentAssignment && this.currentAssignment.status === 'assigned'
      },

      canCompleteImplementation() {
        return (
          this.currentAssignment &&
          (this.currentAssignment.status === 'assigned' ||
            this.currentAssignment.status === 'in_progress')
        )
      },

      canCancelTask() {
        return (
          this.currentAssignment &&
          (this.currentAssignment.status === 'assigned' ||
            this.currentAssignment.status === 'in_progress')
        )
      }
    },
    async mounted() {
      this.requestId = this.$route.params.id
      if (this.requestId) {
        await this.loadRequestDetails()
      } else {
        this.error = 'Invalid request ID'
        this.isLoading = false
      }
    },
    methods: {
      async loadRequestDetails() {
        this.isLoading = true
        this.error = null

        try {
          console.log('Loading request details for ID:', this.requestId)

          // Load basic request details
          const requestResult = await ictOfficerService.getAccessRequestById(this.requestId)
          if (!requestResult.success) {
            throw new Error(requestResult.message || 'Failed to load request details')
          }

          // Load timeline data
          const timelineResult = await ictOfficerService.getAccessRequestTimeline(this.requestId)
          if (!timelineResult.success) {
            throw new Error(timelineResult.message || 'Failed to load timeline data')
          }

          this.request = requestResult.data
          this.timelineData = timelineResult.data

          console.log('Request details loaded:', this.request)
          console.log('Timeline data loaded:', this.timelineData)
        } catch (error) {
          console.error('Error loading request details:', error)
          this.error = error.message || 'Failed to load request details'
        } finally {
          this.isLoading = false
        }
      },

      async startImplementation() {
        try {
          const notes =
            prompt('Please enter implementation notes (optional):') || 'Implementation started'

          const result = await ictOfficerService.updateProgress(
            this.requestId,
            'implementation_in_progress',
            notes
          )

          if (result.success) {
            alert('Implementation started successfully!')
            await this.loadRequestDetails() // Refresh data
          } else {
            alert('Failed to start implementation: ' + result.message)
          }
        } catch (error) {
          console.error('Error starting implementation:', error)
          alert('Network error while starting implementation')
        }
      },

      async completeImplementation() {
        try {
          const notes = prompt('Please enter completion notes (required):')
          if (!notes || !notes.trim()) {
            alert('Completion notes are required')
            return
          }

          const result = await ictOfficerService.updateProgress(
            this.requestId,
            'completed',
            notes.trim()
          )

          if (result.success) {
            alert('Implementation completed successfully!')
            await this.loadRequestDetails() // Refresh data
          } else {
            alert('Failed to complete implementation: ' + result.message)
          }
        } catch (error) {
          console.error('Error completing implementation:', error)
          alert('Network error while completing implementation')
        }
      },

      async cancelTask() {
        try {
          const reason = prompt('Please provide a reason for canceling this task:')
          if (!reason || !reason.trim()) {
            alert('Cancellation reason is required')
            return
          }

          const result = await ictOfficerService.cancelTask(this.requestId, reason.trim())

          if (result.success) {
            alert('Task canceled successfully!')
            await this.loadRequestDetails() // Refresh data
          } else {
            alert('Failed to cancel task: ' + result.message)
          }
        } catch (error) {
          console.error('Error canceling task:', error)
          alert('Network error while canceling task')
        }
      },

      goBack() {
        this.$router.push('/ict-dashboard/access-requests')
      },

      getStatusBadgeClass(status) {
        const statusClasses = {
          head_of_it_approved: 'bg-yellow-100 text-yellow-800',
          assigned_to_ict: 'bg-blue-100 text-blue-800',
          implementation_in_progress: 'bg-purple-100 text-purple-800',
          completed: 'bg-emerald-100 text-emerald-800',
          cancelled: 'bg-red-100 text-red-800'
        }
        return statusClasses[status] || 'bg-gray-100 text-gray-800'
      },

      getStatusText(status) {
        const statusTexts = {
          head_of_it_approved: 'Available for Assignment',
          assigned_to_ict: 'Assigned to ICT Officer',
          implementation_in_progress: 'Implementation in Progress',
          completed: 'Implementation Completed',
          cancelled: 'Task Cancelled'
        }
        return (
          statusTexts[status] ||
          status?.replace(/_/g, ' ').replace(/\b\w/g, (l) => l.toUpperCase()) ||
          'Unknown Status'
        )
      },

      formatDateTime(dateString) {
        if (!dateString) return 'N/A'
        try {
          return new Date(dateString).toLocaleString('en-GB', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
          })
        } catch {
          return 'Invalid Date'
        }
      }
    }
  }
</script>

<style scoped>
  /* Custom Animations and Effects */
  @keyframes fadeInUp {
    from {
      opacity: 0;
      transform: translateY(30px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  @keyframes slideInRight {
    from {
      opacity: 0;
      transform: translateX(30px);
    }
    to {
      opacity: 1;
      transform: translateX(0);
    }
  }

  @keyframes pulse-slow {
    0%,
    100% {
      opacity: 1;
    }
    50% {
      opacity: 0.7;
    }
  }

  @keyframes shimmer {
    0% {
      background-position: -200% 0;
    }
    100% {
      background-position: 200% 0;
    }
  }

  /* Apply animations to main containers */
  .space-y-8 > * {
    animation: fadeInUp 0.6s ease-out;
  }

  .space-y-8 > *:nth-child(1) {
    animation-delay: 0.1s;
  }

  .space-y-8 > *:nth-child(2) {
    animation-delay: 0.2s;
  }

  .space-y-8 > *:nth-child(3) {
    animation-delay: 0.3s;
  }

  .space-y-8 > *:nth-child(4) {
    animation-delay: 0.4s;
  }

  /* Timeline items slide in from right */
  .space-y-8 .flex.items-start {
    animation: slideInRight 0.5s ease-out;
  }

  /* Enhanced hover effects for action buttons */
  .group:hover {
    transform: translateY(-2px) scale(1.02);
  }

  /* Glassmorphism effect enhancement */
  .backdrop-blur-sm {
    backdrop-filter: blur(8px);
  }

  /* Custom scrollbar for module lists */
  .overflow-y-auto::-webkit-scrollbar {
    width: 6px;
  }

  .overflow-y-auto::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 0.1);
    border-radius: 10px;
  }

  .overflow-y-auto::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #0047ab, #1458d6);
    border-radius: 10px;
  }

  .overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #003d99, #1250c2);
  }

  /* Loading spinner enhancement */
  .animate-spin {
    animation-duration: 1s;
  }

  /* Pulsing animation for status indicators */
  .animate-pulse {
    animation: pulse-slow 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
  }

  /* Shimmer effect for loading states */
  .shimmer {
    background: linear-gradient(
      90deg,
      rgba(255, 255, 255, 0) 0%,
      rgba(255, 255, 255, 0.2) 20%,
      rgba(255, 255, 255, 0.5) 60%,
      rgba(255, 255, 255, 0)
    );
    background-size: 200% 100%;
    animation: shimmer 2s infinite;
  }

  /* Enhanced shadow effects */
  .shadow-xl {
    box-shadow:
      0 20px 25px -5px rgba(0, 0, 0, 0.1),
      0 10px 10px -5px rgba(0, 0, 0, 0.04),
      0 0 0 1px rgba(0, 71, 171, 0.05);
  }

  /* Gradient border effect */
  .gradient-border {
    position: relative;
  }

  .gradient-border::before {
    content: '';
    position: absolute;
    inset: 0;
    padding: 2px;
    background: linear-gradient(135deg, #0047ab, #1458d6, #0047ab);
    border-radius: inherit;
    mask:
      linear-gradient(#fff 0 0) content-box,
      linear-gradient(#fff 0 0);
    mask-composite: exclude;
  }

  /* Responsive adjustments */
  @media (max-width: 768px) {
    .space-y-8 > * {
      animation-delay: 0.05s;
    }

    .group:hover {
      transform: scale(1.01);
    }
  }

  /* Print styles */
  @media print {
    .bg-gradient-to-r,
    .bg-gradient-to-br {
      background: #0047ab !important;
      color: white !important;
    }

    .shadow-xl,
    .shadow-2xl {
      box-shadow: none !important;
    }
  }

  /* SPECTACULAR ANIMATIONS & VISUAL EFFECTS */

  /* Floating animations */
  @keyframes float {
    0%,
    100% {
      transform: translateY(0px) rotate(0deg);
    }
    33% {
      transform: translateY(-20px) rotate(2deg);
    }
    66% {
      transform: translateY(-10px) rotate(-1deg);
    }
  }

  @keyframes float-delayed {
    0%,
    100% {
      transform: translateY(0px) rotate(0deg) scale(1);
    }
    50% {
      transform: translateY(-15px) rotate(1deg) scale(1.05);
    }
  }

  @keyframes float-slow {
    0%,
    100% {
      transform: translateY(0px) rotate(0deg);
    }
    50% {
      transform: translateY(-25px) rotate(-2deg);
    }
  }

  .animate-float {
    animation: float 6s ease-in-out infinite;
  }

  .animate-float-delayed {
    animation: float-delayed 8s ease-in-out infinite;
  }

  .animate-float-slow {
    animation: float-slow 10s ease-in-out infinite;
  }

  /* Particle animation delays */
  .animation-delay-0 {
    animation-delay: 0s;
  }
  .animation-delay-1000 {
    animation-delay: 1s;
  }
  .animation-delay-2000 {
    animation-delay: 2s;
  }
  .animation-delay-3000 {
    animation-delay: 3s;
  }
  .animation-delay-4000 {
    animation-delay: 4s;
  }

  /* Glowing effects */
  @keyframes glow-pulse {
    0%,
    100% {
      box-shadow:
        0 0 20px rgba(255, 215, 0, 0.4),
        0 0 40px rgba(255, 215, 0, 0.2),
        0 0 60px rgba(255, 215, 0, 0.1);
    }
    50% {
      box-shadow:
        0 0 30px rgba(255, 215, 0, 0.6),
        0 0 60px rgba(255, 215, 0, 0.4),
        0 0 90px rgba(255, 215, 0, 0.2);
    }
  }

  .glow-pulse {
    animation: glow-pulse 2s ease-in-out infinite;
  }

  /* Radial gradient utility */
  .bg-gradient-radial {
    background: radial-gradient(circle, var(--tw-gradient-stops));
  }

  /* Enhanced card hover effects */
  .card-hover {
    transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
  }

  .card-hover:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow:
      0 25px 50px rgba(0, 71, 171, 0.25),
      0 0 0 1px rgba(255, 255, 255, 0.1);
  }

  /* Shimmer effect for loading states */
  @keyframes shimmer {
    0% {
      background-position: -200px 0;
    }
    100% {
      background-position: calc(200px + 100%) 0;
    }
  }

  .shimmer {
    background: linear-gradient(
      90deg,
      rgba(255, 255, 255, 0) 0%,
      rgba(255, 255, 255, 0.2) 20%,
      rgba(255, 255, 255, 0.5) 60%,
      rgba(255, 255, 255, 0)
    );
    background-size: 200px 100%;
    background-repeat: no-repeat;
    animation: shimmer 2s infinite linear;
  }

  /* Text gradient effects */
  .text-gradient-gold {
    background: linear-gradient(135deg, #ffd700, #ffed4e, #fbbf24);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
  }

  /* Enhanced timeline animations */
  .timeline-item {
    opacity: 0;
    transform: translateX(-50px);
    animation: slideInFromLeft 0.6s ease-out forwards;
  }

  .timeline-item:nth-child(2) {
    animation-delay: 0.1s;
  }
  .timeline-item:nth-child(3) {
    animation-delay: 0.2s;
  }
  .timeline-item:nth-child(4) {
    animation-delay: 0.3s;
  }
  .timeline-item:nth-child(5) {
    animation-delay: 0.4s;
  }

  @keyframes slideInFromLeft {
    to {
      opacity: 1;
      transform: translateX(0);
    }
  }

  /* Status badge pulse animations */
  .status-badge-completed {
    animation: statusPulse 2s ease-in-out infinite;
  }

  @keyframes statusPulse {
    0%,
    100% {
      box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.4);
    }
    50% {
      box-shadow: 0 0 0 10px rgba(34, 197, 94, 0);
    }
  }

  /* Interactive button enhancements */
  .btn-spectacular {
    position: relative;
    overflow: hidden;
    background: linear-gradient(135deg, #0047ab, #1458d6);
    border: 2px solid rgba(255, 255, 255, 0.2);
    transition: all 0.3s ease;
  }

  .btn-spectacular::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s;
  }

  .btn-spectacular:hover::before {
    left: 100%;
  }

  .btn-spectacular:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0, 71, 171, 0.4);
    border-color: rgba(255, 255, 255, 0.4);
  }

  /* Glass morphism enhancements */
  .glass-morphism {
    backdrop-filter: blur(20px) saturate(150%);
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
  }

  /* Responsive spectacular effects */
  @media (max-width: 768px) {
    .animate-float,
    .animate-float-delayed,
    .animate-float-slow {
      animation-duration: 4s; /* Faster on mobile */
    }

    .card-hover:hover {
      transform: translateY(-4px) scale(1.01);
    }
  }

  /* Performance optimizations */
  .animate-float,
  .animate-float-delayed,
  .animate-float-slow,
  .timeline-item {
    will-change: transform;
  }
</style>
