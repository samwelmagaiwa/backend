<template>
  <div class="flex flex-col h-screen">
    <Header />
    <div class="flex flex-1 overflow-hidden">
      <ModernSidebar />
      <main
        class="flex-1 p-8 bg-gradient-to-br from-blue-900 via-blue-800 to-teal-900 overflow-y-auto relative"
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

        <div class="max-w-full mx-auto px-4 relative z-10">
          <!-- DEBUGGING: Component Status -->
          <div class="fixed top-4 right-4 z-50 bg-red-500 text-white p-4 rounded-lg shadow-lg">
            <h3 class="font-bold">🔧 DEBUG INFO</h3>
            <p>Component: MOUNTED ✅</p>
            <p>Route ID: {{ $route.params.id }}</p>
            <p>Request Data: {{ Object.keys(request).length }} keys</p>
            <p>Loading: {{ isLoading ? 'YES' : 'NO' }}</p>
          </div>
          
          <!-- Header Section -->
          <div
            class="booking-glass-card rounded-t-3xl p-4 mb-0 border-b border-blue-300/30 animate-fade-in"
          >
            <div class="flex justify-between items-center">
              <!-- Left Logo -->
              <div
                class="w-16 h-16 mr-4 transform hover:scale-110 transition-transform duration-300"
              >
                <div
                  class="w-full h-full bg-gradient-to-br from-blue-500/20 to-teal-500/20 rounded-xl backdrop-blur-sm border-2 border-blue-300/40 flex items-center justify-center shadow-lg hover:shadow-blue-500/25"
                >
                  <img
                    src="/assets/images/ngao2.png"
                    alt="National Shield"
                    class="max-w-12 max-h-12 object-contain"
                  />
                </div>
              </div>

              <!-- Center Content -->
              <div class="text-center flex-1">
                <h1
                  class="text-lg font-bold text-white mb-2 tracking-wide drop-shadow-lg animate-fade-in"
                >
                  MUHIMBILI NATIONAL HOSPITAL
                </h1>
                <div class="relative inline-block mb-2">
                  <div
                    class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-4 py-1 rounded-full text-sm font-bold shadow-lg transform hover:scale-105 transition-all duration-300 border-2 border-blue-400/60"
                  >
                    <span class="relative z-10 flex items-center gap-2">
                      <i class="fas fa-clipboard-check text-sm"></i>
                      REQUEST DETAILS & ASSESSMENT
                    </span>
                  </div>
                </div>
                <h2
                  class="text-sm font-bold text-blue-100 tracking-wide drop-shadow-md animate-fade-in-delay"
                >
                  ICT OFFICER REVIEW PANEL
                </h2>
              </div>

              <!-- Right Logo -->
              <div
                class="w-16 h-16 ml-4 transform hover:scale-110 transition-transform duration-300"
              >
                <div
                  class="w-full h-full bg-gradient-to-br from-teal-500/20 to-blue-500/20 rounded-xl backdrop-blur-sm border-2 border-teal-300/40 flex items-center justify-center shadow-lg hover:shadow-teal-500/25"
                >
                  <img
                    src="/assets/images/logo2.png"
                    alt="Muhimbili Logo"
                    class="max-w-12 max-h-12 object-contain"
                  />
                </div>
              </div>
            </div>
          </div>

          <!-- Main Content -->
          <div class="booking-glass-card rounded-b-3xl overflow-hidden animate-slide-up">
            <div class="p-4 space-y-4">
              <!-- Component Test -->
              <div class="bg-green-500/20 border border-green-400/40 rounded-lg p-4 mb-4">
                <p class="text-green-200 text-sm">
                  ✅ RequestDetails component is rendering successfully!
                </p>
                <p class="text-green-200 text-xs mt-2">
                  Route ID: {{ $route.params.id }} | Request Keys: {{ Object.keys(request).length }}
                </p>
              </div>
              
              <!-- Error Display -->
              <div v-if="!isLoading && (!request || Object.keys(request).length === 0)" class="bg-red-500/20 border border-red-400/40 rounded-lg p-4 mb-4">
                <p class="text-red-200 text-sm">
                  ⚠️ No request data loaded. Check console for errors.
                </p>
                <button @click="fetchRequestDetails()" class="mt-2 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                  Retry Loading
                </button>
              </div>

              <!-- Back Button -->
              <div class="flex items-center justify-between mb-3">
                <button
                  @click="goBack"
                  class="inline-flex items-center px-4 py-2 bg-gray-600/80 text-white rounded-lg hover:bg-gray-700/80 transition-all duration-300 font-semibold shadow-lg hover:shadow-xl transform hover:scale-105 backdrop-blur-sm border border-gray-500/50 text-sm"
                >
                  <i class="fas fa-arrow-left mr-2"></i>
                  Back to Requests List
                </button>

                <div class="flex items-center space-x-4">
                  <span
                    :class="
                      getStatusBadgeClass(
                        request.ict_approve || request.ict_status || request.status
                      )
                    "
                    class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium"
                  >
                    <i
                      :class="
                        getStatusIcon(request.ict_approve || request.ict_status || request.status)
                      "
                      class="mr-2"
                    ></i>
                    {{ getStatusText(request.ict_approve || request.ict_status || request.status) }}
                  </span>
                </div>
              </div>

              <!-- Request Details Section -->
              <div
                class="booking-card bg-gradient-to-r from-teal-600/25 to-blue-600/25 border-2 border-teal-400/40 p-4 rounded-2xl backdrop-blur-sm hover:shadow-xl hover:shadow-teal-500/20 transition-all duration-500 group"
              >
                <div class="flex items-center space-x-3 mb-4">
                  <div
                    class="w-10 h-10 bg-gradient-to-br from-teal-500 to-blue-600 rounded-lg flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-teal-300/50"
                  >
                    <i class="fas fa-info-circle text-white text-sm"></i>
                  </div>
                  <h3 class="text-lg font-bold text-white flex items-center">
                    <i class="fas fa-user mr-2 text-teal-300 text-sm"></i>
                    Borrower Information
                  </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                  <!-- Request ID -->
                  <div class="group">
                    <label class="block text-sm font-semibold text-blue-100 mb-1 flex items-center">
                      <i class="fas fa-hashtag mr-2 text-teal-300 text-xs"></i>
                      Request ID
                    </label>
                    <div
                      class="px-3 py-2 bg-white/10 border border-blue-300/30 rounded-lg text-white text-sm backdrop-blur-sm font-mono"
                    >
                      {{ request.request_id || `REQ-${String(request.id).padStart(6, '0')}` }}
                    </div>
                  </div>

                  <!-- Borrower Name -->
                  <div class="group">
                    <label class="block text-sm font-semibold text-blue-100 mb-1 flex items-center">
                      <i class="fas fa-user mr-2 text-teal-300 text-xs"></i>
                      Borrower Name
                    </label>
                    <div
                      class="px-3 py-2 bg-white/10 border border-blue-300/30 rounded-lg text-white text-sm backdrop-blur-sm"
                    >
                      {{ request.borrower_name || request.borrowerName || 'Unknown' }}
                    </div>
                  </div>

                  <!-- Department -->
                  <div class="group">
                    <label class="block text-sm font-semibold text-blue-100 mb-1 flex items-center">
                      <i class="fas fa-building mr-2 text-teal-300 text-xs"></i>
                      Department
                    </label>
                    <div
                      class="px-3 py-2 bg-white/10 border border-blue-300/30 rounded-lg text-white text-sm backdrop-blur-sm"
                    >
                      {{ request.department || 'Unknown Department' }}
                    </div>
                  </div>

                  <!-- Phone Number -->
                  <div class="group">
                    <label class="block text-sm font-semibold text-blue-100 mb-1 flex items-center">
                      <i class="fas fa-phone mr-2 text-teal-300 text-xs"></i>
                      Phone Number
                    </label>
                    <div
                      class="px-3 py-2 bg-white/10 border border-blue-300/30 rounded-lg text-white text-sm backdrop-blur-sm"
                    >
                      {{ request.borrower_phone || request.phoneNumber || 'No phone provided' }}
                    </div>
                  </div>

                  <!-- PF Number -->
                  <div v-if="request.pf_number" class="group">
                    <label class="block text-sm font-semibold text-blue-100 mb-1 flex items-center">
                      <i class="fas fa-id-badge mr-2 text-teal-300 text-xs"></i>
                      PF Number
                    </label>
                    <div
                      class="px-3 py-2 bg-white/10 border border-blue-300/30 rounded-lg text-white text-sm backdrop-blur-sm font-mono"
                    >
                      {{ request.pf_number }}
                    </div>
                  </div>

                  <!-- Device Type -->
                  <div class="group">
                    <label class="block text-sm font-semibold text-blue-100 mb-1 flex items-center">
                      <i class="fas fa-laptop mr-2 text-teal-300 text-xs"></i>
                      Device Type
                    </label>
                    <div
                      class="px-3 py-2 bg-white/10 border border-blue-300/30 rounded-lg text-white text-sm backdrop-blur-sm"
                    >
                      {{
                        request.device_name ||
                        getDeviceDisplayName(
                          request.device_type || request.deviceType,
                          request.custom_device || request.customDevice
                        )
                      }}
                    </div>
                  </div>

                  <!-- Booking Date -->
                  <div class="group">
                    <label class="block text-sm font-semibold text-blue-100 mb-1 flex items-center">
                      <i class="fas fa-calendar mr-2 text-teal-300 text-xs"></i>
                      Booking Date
                    </label>
                    <div
                      class="px-3 py-2 bg-white/10 border border-blue-300/30 rounded-lg text-white text-sm backdrop-blur-sm"
                    >
                      {{ formatDate(request.booking_date || request.bookingDate) }}
                    </div>
                  </div>

                  <!-- Return Date & Time -->
                  <div class="group">
                    <label class="block text-sm font-semibold text-blue-100 mb-1 flex items-center">
                      <i class="fas fa-calendar-minus mr-2 text-teal-300 text-xs"></i>
                      Return Date & Time
                    </label>
                    <div
                      class="px-3 py-2 bg-white/10 border border-blue-300/30 rounded-lg text-white text-sm backdrop-blur-sm"
                    >
                      {{ formatDate(request.collection_date || request.collectionDate) }} at
                      {{ request.return_time || request.returnTime || 'No time specified' }}
                    </div>
                  </div>

                  <!-- Signature Status -->
                  <div class="group">
                    <label
                      class="block text-sm font-semibold text-blue-100 mb-1 flex items-center justify-center"
                    >
                      <i class="fas fa-signature mr-2 text-teal-300 text-xs"></i>
                      Signature Status
                    </label>
                    <div
                      class="px-3 py-2 bg-white/10 border border-blue-300/30 rounded-lg text-white text-sm backdrop-blur-sm"
                    >
                      <div
                        v-if="request.has_signature || request.signature || request.signature_path"
                        class="flex items-center justify-center text-green-400"
                      >
                        <i class="fas fa-check-circle mr-2"></i>
                        <span class="font-medium">Digitally Signed</span>
                      </div>
                      <div v-else class="flex items-center justify-center text-center">
                        <div class="text-center">
                          <div class="mb-2">
                            <i class="fas fa-signature text-teal-300 text-2xl mb-2"></i>
                            <p class="text-blue-100 text-sm font-medium">No signature uploaded</p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Reason for Borrowing -->
                <div class="mt-4">
                  <label class="block text-sm font-semibold text-blue-100 mb-1 flex items-center">
                    <i class="fas fa-comment-alt mr-2 text-teal-300 text-xs"></i>
                    Reason for Borrowing
                  </label>
                  <div
                    class="px-3 py-2 bg-white/10 border border-blue-300/30 rounded-lg text-white text-sm backdrop-blur-sm"
                  >
                    {{ request.purpose || request.reason || 'No reason provided' }}
                  </div>
                </div>

                <!-- Digital Signature -->
                <div
                  v-if="request.has_signature || request.signature || request.signature_path"
                  class="mt-4"
                >
                  <label class="block text-sm font-semibold text-blue-100 mb-1 flex items-center">
                    <i class="fas fa-signature mr-2 text-teal-300 text-xs"></i>
                    Digital Signature
                  </label>
                  <div
                    class="px-3 py-2 bg-white/10 border border-blue-300/30 rounded-lg backdrop-blur-sm"
                  >
                    <div class="flex flex-col items-center space-y-3">
                      <div
                        class="inline-block bg-white/20 p-3 rounded-lg border border-teal-300/30"
                      >
                        <img
                          :src="request.signature_url || request.signature"
                          alt="Digital Signature"
                          class="max-h-16 max-w-48 object-contain"
                        />
                      </div>
                      <div class="flex items-center text-green-400">
                        <i class="fas fa-check-circle mr-2"></i>
                        <div class="text-center">
                          <span class="text-sm font-semibold block">Verified</span>
                          <span class="text-xs text-green-300">Signature captured</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>



              <!-- Device Condition Assessment Section -->
              <div
                v-if="request.ict_approve === 'approved'"
                class="relative overflow-hidden bg-gradient-to-br from-slate-800/90 via-blue-900/80 to-indigo-900/90 border-2 border-cyan-400/30 rounded-3xl backdrop-blur-xl shadow-2xl hover:shadow-cyan-500/25 transition-all duration-700 group"
              >
                <!-- Animated Background Elements -->
                <div class="absolute inset-0 overflow-hidden">
                  <div class="absolute -top-10 -right-10 w-40 h-40 bg-gradient-to-br from-cyan-400/10 to-blue-500/10 rounded-full blur-3xl animate-pulse"></div>
                  <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-gradient-to-tr from-indigo-400/10 to-purple-500/10 rounded-full blur-2xl animate-pulse" style="animation-delay: 2s"></div>
                  <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-60 h-60 bg-gradient-to-r from-blue-500/5 to-cyan-500/5 rounded-full blur-3xl animate-pulse" style="animation-delay: 4s"></div>
                </div>
                
                <!-- Floating Particles -->
                <div class="absolute inset-0 overflow-hidden pointer-events-none">
                  <div class="absolute top-20 left-20 w-2 h-2 bg-cyan-400/40 rounded-full animate-bounce" style="animation-delay: 1s; animation-duration: 3s"></div>
                  <div class="absolute top-32 right-32 w-1 h-1 bg-blue-400/60 rounded-full animate-bounce" style="animation-delay: 2s; animation-duration: 4s"></div>
                  <div class="absolute bottom-24 left-40 w-1.5 h-1.5 bg-indigo-400/50 rounded-full animate-bounce" style="animation-delay: 3s; animation-duration: 2.5s"></div>
                </div>

                <div class="relative z-10 p-8">
                  <!-- Enhanced Header -->
                  <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center space-x-6">
                      <div class="relative">
                        <div class="w-16 h-16 bg-gradient-to-br from-cyan-400 via-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-2xl transform group-hover:scale-110 group-hover:rotate-6 transition-all duration-500 border-2 border-cyan-300/40">
                          <div class="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent rounded-2xl"></div>
                          <i class="fas fa-clipboard-check text-white text-2xl relative z-10 drop-shadow-lg"></i>
                        </div>
                        <!-- Floating Badge -->
                        <div class="absolute -top-2 -right-2 w-7 h-7 bg-gradient-to-br from-emerald-400 to-green-500 rounded-full flex items-center justify-center shadow-lg animate-pulse">
                          <i class="fas fa-star text-white text-xs"></i>
                        </div>
                      </div>
                      <div>
                        <h3 class="text-3xl font-bold text-white mb-2 tracking-wide">
                          <span class="bg-gradient-to-r from-cyan-300 via-blue-300 to-indigo-300 bg-clip-text text-transparent">Device Condition</span>
                          <span class="text-white ml-2">Assessment</span>
                        </h3>
                        <div class="flex items-center space-x-3">
                          <div class="flex space-x-1">
                            <div class="w-2 h-2 bg-cyan-400 rounded-full animate-pulse"></div>
                            <div class="w-2 h-2 bg-blue-400 rounded-full animate-pulse" style="animation-delay: 0.5s"></div>
                            <div class="w-2 h-2 bg-indigo-400 rounded-full animate-pulse" style="animation-delay: 1s"></div>
                          </div>
                          <p class="text-cyan-200/90 text-sm font-medium tracking-wide">
                            Comprehensive quality control and inspection system
                          </p>
                        </div>
                      </div>
                    </div>
                    <!-- Status Indicators -->
                    <div class="hidden lg:flex flex-col items-center space-y-3">
                      <div class="w-14 h-14 bg-gradient-to-br from-cyan-500/20 to-blue-600/20 rounded-2xl border border-cyan-400/40 flex items-center justify-center backdrop-blur-sm">
                        <i class="fas fa-shield-check text-cyan-300 text-xl"></i>
                      </div>
                      <span class="text-xs text-cyan-300/80 font-semibold tracking-wider">QUALITY ASSURED</span>
                    </div>
                  </div>

                  <!-- Enhanced Assessment Type Tabs -->
                  <div class="mb-8">
                    <div class="bg-slate-800/50 p-3 rounded-2xl border border-cyan-400/20 backdrop-blur-sm">
                      <div class="flex space-x-3">
                        <button
                          @click="assessmentType = 'issuing'"
                          :class="[
                            'flex-1 px-6 py-4 rounded-xl font-bold transition-all duration-500 relative overflow-hidden group/tab',
                            assessmentType === 'issuing'
                              ? 'bg-gradient-to-r from-cyan-500 to-blue-600 text-white shadow-xl transform scale-105'
                              : 'bg-slate-700/50 text-cyan-200 hover:bg-slate-600/60 hover:text-white'
                          ]"
                        >
                          <div v-if="assessmentType === 'issuing'" class="absolute inset-0 bg-gradient-to-r from-cyan-400/20 to-blue-400/20 animate-pulse"></div>
                          <div class="relative z-10 flex items-center justify-center space-x-3">
                            <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                              <i class="fas fa-hand-holding text-lg"></i>
                            </div>
                            <span class="text-base font-bold">Device Issuing</span>
                          </div>
                        </button>
                        <button
                          @click="assessmentType = 'receiving'"
                          :class="[
                            'flex-1 px-6 py-4 rounded-xl font-bold transition-all duration-500 relative overflow-hidden group/tab',
                            assessmentType === 'receiving'
                              ? 'bg-gradient-to-r from-cyan-500 to-blue-600 text-white shadow-xl transform scale-105'
                              : 'bg-slate-700/50 text-cyan-200 hover:bg-slate-600/60 hover:text-white'
                          ]"
                        >
                          <div v-if="assessmentType === 'receiving'" class="absolute inset-0 bg-gradient-to-r from-cyan-400/20 to-blue-400/20 animate-pulse"></div>
                          <div class="relative z-10 flex items-center justify-center space-x-3">
                            <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                              <i class="fas fa-undo text-lg"></i>
                            </div>
                            <span class="text-base font-bold">Device Receiving</span>
                          </div>
                        </button>
                      </div>
                    </div>
                  </div>

                  <!-- Device Issuing Assessment -->
                  <div v-if="assessmentType === 'issuing'" class="space-y-8">
                    <!-- Assessment Header -->
                    <div class="text-center mb-8">
                      <div class="inline-flex items-center space-x-4 bg-gradient-to-r from-slate-700/60 to-slate-800/60 px-8 py-4 rounded-2xl border border-cyan-400/30 backdrop-blur-sm">
                        <div class="w-12 h-12 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                          <i class="fas fa-clipboard-list text-white text-xl"></i>
                        </div>
                        <div class="text-left">
                          <h4 class="text-xl font-bold text-white mb-1">
                            Device Condition Checklist
                          </h4>
                          <p class="text-cyan-200/80 text-sm font-medium">
                            Pre-issue quality assessment
                          </p>
                        </div>
                      </div>
                    </div>

                    <!-- Enhanced Physical Condition Section -->
                    <div class="bg-gradient-to-br from-slate-700/40 to-slate-800/30 p-6 rounded-2xl border border-cyan-400/20 backdrop-blur-sm hover:border-cyan-300/40 transition-all duration-500 group">
                      <div class="flex items-center mb-6">
                        <div class="w-10 h-10 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-xl flex items-center justify-center mr-4 shadow-lg">
                          <i class="fas fa-eye text-white text-lg"></i>
                        </div>
                        <div>
                          <h5 class="text-lg font-bold text-white">Physical Condition</h5>
                          <p class="text-cyan-200/70 text-sm">Visual inspection assessment</p>
                        </div>
                      </div>
                      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <label
                          v-for="condition in physicalConditions"
                          :key="condition.value"
                          class="relative cursor-pointer group/option"
                        >
                          <input
                            v-model="issuingAssessment.physical_condition"
                            type="radio"
                            :value="condition.value"
                            class="sr-only"
                          />
                          <div
                            :class="[
                              'p-4 rounded-xl border-2 transition-all duration-300 text-center relative overflow-hidden transform hover:scale-105',
                              issuingAssessment.physical_condition === condition.value
                                ? 'bg-gradient-to-br from-cyan-500 to-blue-600 border-cyan-400 text-white shadow-xl scale-105'
                                : 'bg-slate-800/50 border-slate-600/50 text-slate-300 hover:border-cyan-400/50 hover:bg-slate-700/60'
                            ]"
                          >
                            <div v-if="issuingAssessment.physical_condition === condition.value" class="absolute inset-0 bg-gradient-to-r from-cyan-400/20 to-blue-400/20 animate-pulse"></div>
                            <div class="relative z-10">
                              <div class="font-bold text-base mb-2">{{ condition.label }}</div>
                              <div class="flex justify-center">
                                <div v-if="issuingAssessment.physical_condition === condition.value" class="flex items-center space-x-1">
                                  <i class="fas fa-check-circle text-emerald-300 animate-pulse"></i>
                                  <span class="text-xs text-emerald-200">Selected</span>
                                </div>
                                <div v-else :class="[
                                  'w-3 h-3 rounded-full',
                                  condition.value === 'excellent' ? 'bg-emerald-400' :
                                  condition.value === 'good' ? 'bg-blue-400' :
                                  condition.value === 'fair' ? 'bg-yellow-400' : 'bg-red-400'
                                ]"></div>
                              </div>
                            </div>
                          </div>
                        </label>
                      </div>
                    </div>

                    <!-- Enhanced Device Functionality Section -->
                    <div class="bg-gradient-to-br from-indigo-700/40 to-purple-800/30 p-6 rounded-2xl border border-indigo-400/20 backdrop-blur-sm hover:border-indigo-300/40 transition-all duration-500 group">
                      <div class="flex items-center mb-6">
                        <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center mr-4 shadow-lg">
                          <i class="fas fa-cogs text-white text-lg"></i>
                        </div>
                        <div>
                          <h5 class="text-lg font-bold text-white">Device Functionality</h5>
                          <p class="text-indigo-200/70 text-sm">Performance testing evaluation</p>
                        </div>
                      </div>
                      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <label
                          v-for="func in functionalityOptions"
                          :key="func.value"
                          class="relative cursor-pointer group/option"
                        >
                          <input
                            v-model="issuingAssessment.functionality"
                            type="radio"
                            :value="func.value"
                            class="sr-only"
                          />
                          <div
                            :class="[
                              'p-4 rounded-xl border-2 transition-all duration-300 text-center relative overflow-hidden transform hover:scale-105',
                              issuingAssessment.functionality === func.value
                                ? 'bg-gradient-to-br from-indigo-500 to-purple-600 border-indigo-400 text-white shadow-xl scale-105'
                                : 'bg-slate-800/50 border-slate-600/50 text-slate-300 hover:border-indigo-400/50 hover:bg-slate-700/60'
                            ]"
                          >
                            <div v-if="issuingAssessment.functionality === func.value" class="absolute inset-0 bg-gradient-to-r from-indigo-400/20 to-purple-400/20 animate-pulse"></div>
                            <div class="relative z-10">
                              <div class="font-bold text-base mb-2">{{ func.label }}</div>
                              <div class="flex justify-center">
                                <div v-if="issuingAssessment.functionality === func.value" class="flex items-center space-x-1">
                                  <i class="fas fa-check-circle text-emerald-300 animate-pulse"></i>
                                  <span class="text-xs text-emerald-200">Selected</span>
                                </div>
                                <div v-else :class="[
                                  'w-3 h-3 rounded-full',
                                  func.value === 'fully_functional' ? 'bg-emerald-400' :
                                  func.value === 'partially_functional' ? 'bg-yellow-400' : 'bg-red-400'
                                ]"></div>
                              </div>
                            </div>
                          </div>
                        </label>
                      </div>
                    </div>

                    <!-- Enhanced Accessories Check -->
                    <div class="bg-gradient-to-br from-emerald-700/40 to-teal-800/30 p-6 rounded-2xl border border-emerald-400/20 backdrop-blur-sm hover:border-emerald-300/40 transition-all duration-500 group">
                      <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center mr-4 shadow-lg">
                          <i class="fas fa-puzzle-piece text-white text-lg"></i>
                        </div>
                        <div>
                          <h5 class="text-lg font-bold text-white">Accessories Verification</h5>
                          <p class="text-emerald-200/70 text-sm">Component completeness check</p>
                        </div>
                      </div>
                      <label class="flex items-center cursor-pointer group/check p-4 bg-slate-800/30 rounded-xl border border-emerald-500/20 hover:border-emerald-400/40 transition-all duration-300">
                        <div class="relative">
                          <input
                            v-model="issuingAssessment.accessories_complete"
                            type="checkbox"
                            class="sr-only"
                          />
                          <div
                            :class="[
                              'w-8 h-8 rounded-xl border-2 transition-all duration-300 flex items-center justify-center relative overflow-hidden',
                              issuingAssessment.accessories_complete
                                ? 'bg-gradient-to-br from-emerald-500 to-teal-600 border-emerald-400 shadow-lg scale-110'
                                : 'bg-slate-700/50 border-slate-500/50 group-hover/check:border-emerald-400/60'
                            ]"
                          >
                            <div v-if="issuingAssessment.accessories_complete" class="absolute inset-0 bg-gradient-to-r from-emerald-400/30 to-teal-400/30 animate-pulse"></div>
                            <i v-if="issuingAssessment.accessories_complete" class="fas fa-check text-white text-lg relative z-10"></i>
                          </div>
                        </div>
                        <div class="ml-4 flex-1">
                          <span class="text-white font-bold text-base block mb-1">
                            All accessories are complete and included
                          </span>
                          <span class="text-emerald-200/70 text-sm">
                            Verify all components, cables, and documentation are present
                          </span>
                        </div>
                      </label>
                    </div>

                    <!-- Enhanced Damage Check -->
                    <div class="bg-gradient-to-br from-orange-700/40 to-red-800/30 p-6 rounded-2xl border border-orange-400/20 backdrop-blur-sm hover:border-red-300/40 transition-all duration-500 group">
                      <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-red-600 rounded-xl flex items-center justify-center mr-4 shadow-lg">
                          <i class="fas fa-exclamation-triangle text-white text-lg"></i>
                        </div>
                        <div>
                          <h5 class="text-lg font-bold text-white">Damage Assessment</h5>
                          <p class="text-orange-200/70 text-sm">Visual defect identification</p>
                        </div>
                      </div>
                      <label class="flex items-center cursor-pointer group/check p-4 bg-slate-800/30 rounded-xl border border-orange-500/20 hover:border-red-400/40 transition-all duration-300">
                        <div class="relative">
                          <input
                            v-model="issuingAssessment.visible_damage"
                            type="checkbox"
                            class="sr-only"
                          />
                          <div
                            :class="[
                              'w-8 h-8 rounded-xl border-2 transition-all duration-300 flex items-center justify-center relative overflow-hidden',
                              issuingAssessment.visible_damage
                                ? 'bg-gradient-to-br from-red-500 to-orange-600 border-red-400 shadow-lg scale-110'
                                : 'bg-slate-700/50 border-slate-500/50 group-hover/check:border-red-400/60'
                            ]"
                          >
                            <div v-if="issuingAssessment.visible_damage" class="absolute inset-0 bg-gradient-to-r from-red-400/30 to-orange-400/30 animate-pulse"></div>
                            <i v-if="issuingAssessment.visible_damage" class="fas fa-exclamation text-white text-lg relative z-10"></i>
                          </div>
                        </div>
                        <div class="ml-4 flex-1">
                          <span class="text-white font-bold text-base block mb-1">
                            Device has visible damage or defects
                          </span>
                          <span class="text-red-200/70 text-sm">
                            Check for scratches, cracks, dents, or missing parts
                          </span>
                        </div>
                      </label>
                    </div>

                    <!-- Enhanced Damage Description -->
                    <div v-if="issuingAssessment.visible_damage" class="bg-gradient-to-br from-red-700/30 to-orange-700/20 p-6 rounded-2xl border border-red-400/20 backdrop-blur-sm">
                      <div class="flex items-center mb-4">
                        <div class="w-8 h-8 bg-red-500/20 rounded-lg flex items-center justify-center mr-3">
                          <i class="fas fa-edit text-red-300 text-sm"></i>
                        </div>
                        <label class="text-lg font-bold text-white">
                          Damage Description
                        </label>
                      </div>
                      <div class="relative">
                        <textarea
                          v-model="issuingAssessment.damage_description"
                          rows="4"
                          class="w-full px-6 py-4 bg-slate-800/60 border-2 border-red-500/30 rounded-xl focus:border-red-400 focus:outline-none text-white placeholder-red-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-slate-700/70 focus:bg-slate-700/80 resize-none"
                          placeholder="Provide detailed description of any visible damage, scratches, cracks, or defects found during inspection..."
                        ></textarea>
                        <div class="absolute bottom-3 right-3 text-red-300/50 text-xs">
                          <i class="fas fa-pen-alt"></i>
                        </div>
                      </div>
                    </div>

                    <!-- Enhanced Additional Notes -->
                    <div class="bg-gradient-to-br from-slate-700/40 to-blue-800/30 p-6 rounded-2xl border border-blue-400/20 backdrop-blur-sm">
                      <div class="flex items-center mb-4">
                        <div class="w-8 h-8 bg-blue-500/20 rounded-lg flex items-center justify-center mr-3">
                          <i class="fas fa-sticky-note text-blue-300 text-sm"></i>
                        </div>
                        <label class="text-lg font-bold text-white">
                          Additional Notes
                          <span class="text-blue-300/70 text-sm font-normal ml-2">(Optional)</span>
                        </label>
                      </div>
                      <div class="relative">
                        <textarea
                          v-model="issuingAssessmentNotes"
                          rows="4"
                          class="w-full px-6 py-4 bg-slate-800/60 border-2 border-blue-500/30 rounded-xl focus:border-cyan-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-slate-700/70 focus:bg-slate-700/80 resize-none"
                          placeholder="Any additional observations, special instructions, maintenance notes, or recommendations for this device..."
                        ></textarea>
                        <div class="absolute bottom-3 right-3 text-blue-300/50 text-xs">
                          <i class="fas fa-comment-alt"></i>
                        </div>
                      </div>
                    </div>

                    <!-- Enhanced Issue Device Button -->
                    <div class="text-center pt-4">
                      <button
                        @click="saveIssuingAssessment"
                        :disabled="!isIssuingAssessmentComplete || isProcessingAssessment"
                        class="w-full max-w-md mx-auto px-8 py-5 bg-gradient-to-r from-emerald-600 via-green-600 to-teal-600 text-white rounded-2xl hover:from-emerald-700 hover:via-green-700 hover:to-teal-700 transition-all duration-500 font-bold flex items-center justify-center shadow-2xl hover:shadow-emerald-500/30 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none relative overflow-hidden group"
                      >
                        <div class="absolute inset-0 bg-gradient-to-r from-emerald-400/20 to-green-400/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="relative z-10 flex items-center space-x-4">
                          <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-hand-holding text-xl"></i>
                          </div>
                          <div class="text-left">
                            <div class="text-lg font-bold">
                              {{ isProcessingAssessment ? 'Processing Assessment...' : 'Issue Device to Borrower' }}
                            </div>
                            <div class="text-sm text-emerald-100/80">
                              Complete quality verification
                            </div>
                          </div>
                          <div v-if="isProcessingAssessment" class="ml-4">
                            <div class="w-6 h-6 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                          </div>
                        </div>
                      </button>
                    </div>
                  </div>

                  <!-- Device Receiving Assessment -->
                <div v-if="assessmentType === 'receiving'" class="relative z-10 space-y-4">
                  <div class="bg-gradient-to-br from-blue-800/30 via-cyan-800/20 to-blue-900/40 p-4 rounded-2xl border-2 border-blue-400/40 backdrop-blur-md shadow-2xl relative overflow-hidden">
                    <!-- Decorative Elements -->
                    <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-cyan-400/10 to-blue-500/10 rounded-full blur-xl"></div>
                    <div class="absolute bottom-0 left-0 w-16 h-16 bg-gradient-to-tr from-blue-400/10 to-cyan-500/10 rounded-full blur-lg"></div>
                    
                    <div class="relative z-10">
                      <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg mr-3">
                          <i class="fas fa-clipboard-list text-white text-sm"></i>
                        </div>
                        <div>
                          <h4 class="text-lg font-bold text-white mb-1">
                            Device Condition Checklist
                          </h4>
                          <p class="text-blue-200/70 text-xs font-medium">
                            Assessment after device return
                          </p>
                        </div>
                      </div>

                      <!-- Physical Condition -->
                      <div class="mb-4">
                        <div class="flex items-center mb-3">
                          <div class="w-6 h-6 bg-blue-500/20 rounded-lg flex items-center justify-center mr-2">
                            <i class="fas fa-eye text-blue-300 text-xs"></i>
                          </div>
                          <label class="text-base font-bold text-white">
                            Physical Condition
                          </label>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                          <label
                            v-for="condition in physicalConditions"
                            :key="condition.value"
                            class="relative cursor-pointer group"
                          >
                            <input
                              v-model="issuingAssessment.physical_condition"
                              type="radio"
                              :value="condition.value"
                              class="sr-only"
                            />
                            <div
                              :class="[
                                'p-3 rounded-xl border-2 transition-all duration-300 text-center relative overflow-hidden',
                                issuingAssessment.physical_condition === condition.value
                                  ? 'bg-gradient-to-br from-blue-500 to-cyan-600 border-blue-400 text-white shadow-lg transform scale-105'
                                  : 'bg-blue-900/30 border-blue-500/30 text-blue-200 hover:border-blue-400/50 hover:bg-blue-800/40'
                              ]"
                            >
                              <div v-if="issuingAssessment.physical_condition === condition.value" class="absolute inset-0 bg-gradient-to-r from-blue-400/20 to-cyan-400/20 animate-pulse"></div>
                              <div class="relative z-10">
                                <div class="font-semibold text-sm mb-1">{{ condition.label }}</div>
                                <div v-if="issuingAssessment.physical_condition === condition.value" class="text-xs opacity-80">
                                  <i class="fas fa-check-circle"></i>
                                </div>
                              </div>
                            </div>
                          </label>
                        </div>
                      </div>

                      <!-- Functionality -->
                      <div class="mb-4">
                        <div class="flex items-center mb-3">
                          <div class="w-6 h-6 bg-blue-500/20 rounded-lg flex items-center justify-center mr-2">
                            <i class="fas fa-cogs text-blue-300 text-xs"></i>
                          </div>
                          <label class="text-base font-bold text-white">
                            Device Functionality
                          </label>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                          <label
                            v-for="func in functionalityOptions"
                            :key="func.value"
                            class="relative cursor-pointer group"
                          >
                            <input
                              v-model="issuingAssessment.functionality"
                              type="radio"
                              :value="func.value"
                              class="sr-only"
                            />
                            <div
                              :class="[
                                'p-3 rounded-xl border-2 transition-all duration-300 text-center relative overflow-hidden',
                                issuingAssessment.functionality === func.value
                                  ? 'bg-gradient-to-br from-blue-500 to-cyan-600 border-blue-400 text-white shadow-lg transform scale-105'
                                  : 'bg-blue-900/30 border-blue-500/30 text-blue-200 hover:border-blue-400/50 hover:bg-blue-800/40'
                              ]"
                            >
                              <div v-if="issuingAssessment.functionality === func.value" class="absolute inset-0 bg-gradient-to-r from-blue-400/20 to-cyan-400/20 animate-pulse"></div>
                              <div class="relative z-10">
                                <div class="font-semibold text-sm mb-1">{{ func.label }}</div>
                                <div v-if="issuingAssessment.functionality === func.value" class="text-xs opacity-80">
                                  <i class="fas fa-check-circle"></i>
                                </div>
                              </div>
                            </div>
                          </label>
                        </div>
                      </div>

                      <!-- Accessories Complete -->
                      <div class="mb-4">
                        <div class="bg-gradient-to-r from-blue-800/40 to-cyan-800/30 p-4 rounded-xl border border-blue-500/30 hover:border-blue-400/50 transition-all duration-300 relative overflow-hidden">
                          <div class="absolute top-0 right-0 w-16 h-16 bg-blue-400/10 rounded-full blur-lg"></div>
                          <label class="flex items-center cursor-pointer group relative z-10">
                            <div class="relative">
                              <input
                                v-model="issuingAssessment.accessories_complete"
                                type="checkbox"
                                class="sr-only"
                              />
                              <div
                                :class="[
                                  'w-7 h-7 rounded-xl border-2 transition-all duration-300 flex items-center justify-center relative overflow-hidden',
                                  issuingAssessment.accessories_complete
                                    ? 'bg-gradient-to-br from-blue-500 to-cyan-600 border-blue-400 shadow-lg'
                                    : 'bg-blue-900/50 border-blue-500/50 group-hover:border-blue-400/70 group-hover:bg-blue-800/60'
                                ]"
                              >
                                <div v-if="issuingAssessment.accessories_complete" class="absolute inset-0 bg-gradient-to-r from-blue-400/30 to-cyan-400/30 animate-pulse"></div>
                                <i v-if="issuingAssessment.accessories_complete" class="fas fa-check text-white text-sm relative z-10"></i>
                              </div>
                            </div>
                            <div class="ml-5 flex items-center">
                              <div class="w-10 h-10 bg-gradient-to-br from-blue-500/20 to-cyan-500/20 rounded-xl flex items-center justify-center mr-4 border border-blue-400/30">
                                <i class="fas fa-puzzle-piece text-blue-300 text-lg"></i>
                              </div>
                              <div>
                                <span class="text-white font-bold text-base block">
                                  All accessories are complete and included
                                </span>
                                <span class="text-blue-200/70 text-sm">
                                  Verify all components are present
                                </span>
                              </div>
                            </div>
                          </label>
                        </div>
                      </div>

                      <!-- Visible Damage -->
                      <div class="mb-4">
                        <div class="bg-gradient-to-r from-blue-800/40 to-red-800/20 p-4 rounded-xl border border-blue-500/30 hover:border-red-400/50 transition-all duration-300 relative overflow-hidden">
                          <div class="absolute top-0 right-0 w-16 h-16 bg-red-400/10 rounded-full blur-lg"></div>
                          <label class="flex items-center cursor-pointer group relative z-10">
                            <div class="relative">
                              <input
                                v-model="issuingAssessment.visible_damage"
                                type="checkbox"
                                class="sr-only"
                              />
                              <div
                                :class="[
                                  'w-7 h-7 rounded-xl border-2 transition-all duration-300 flex items-center justify-center relative overflow-hidden',
                                  issuingAssessment.visible_damage
                                    ? 'bg-gradient-to-br from-red-500 to-orange-600 border-red-400 shadow-lg'
                                    : 'bg-blue-900/50 border-blue-500/50 group-hover:border-red-400/70 group-hover:bg-red-800/30'
                                ]"
                              >
                                <div v-if="issuingAssessment.visible_damage" class="absolute inset-0 bg-gradient-to-r from-red-400/30 to-orange-400/30 animate-pulse"></div>
                                <i v-if="issuingAssessment.visible_damage" class="fas fa-exclamation text-white text-sm relative z-10"></i>
                              </div>
                            </div>
                            <div class="ml-5 flex items-center">
                              <div class="w-10 h-10 bg-gradient-to-br from-red-500/20 to-orange-500/20 rounded-xl flex items-center justify-center mr-4 border border-red-400/30">
                                <i class="fas fa-exclamation-triangle text-red-300 text-lg"></i>
                              </div>
                              <div>
                                <span class="text-white font-bold text-base block">
                                  Device has visible damage or defects
                                </span>
                                <span class="text-red-200/70 text-sm">
                                  Check for scratches, cracks, or missing parts
                                </span>
                              </div>
                            </div>
                          </label>
                        </div>
                      </div>

                      <!-- Damage Description -->
                      <div v-if="receivingAssessment.visible_damage" class="mb-4">
                        <div class="flex items-center mb-3">
                          <div class="w-6 h-6 bg-red-500/20 rounded-lg flex items-center justify-center mr-2">
                            <i class="fas fa-edit text-red-300 text-xs"></i>
                          </div>
                          <label class="text-base font-bold text-white">
                            Describe the damage or defects:
                          </label>
                        </div>
                        <div class="relative">
                          <textarea
                            v-model="receivingAssessment.damage_description"
                            rows="4"
                            class="w-full px-6 py-4 bg-blue-900/40 border-2 border-blue-500/40 rounded-xl focus:border-cyan-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-blue-800/50 focus:bg-blue-800/60 resize-none"
                            placeholder="Describe any new damage, scratches, or defects found during return inspection..."
                          ></textarea>
                          <div class="absolute bottom-3 right-3 text-blue-300/50 text-xs">
                            <i class="fas fa-pen-alt"></i>
                          </div>
                        </div>
                      </div>

                      <!-- Assessment Notes -->
                      <div class="mb-4">
                        <div class="flex items-center mb-3">
                          <div class="w-6 h-6 bg-blue-500/20 rounded-lg flex items-center justify-center mr-2">
                            <i class="fas fa-sticky-note text-blue-300 text-xs"></i>
                          </div>
                          <label class="text-base font-bold text-white">
                            Additional Notes
                            <span class="text-blue-300/70 text-xs font-normal ml-2">(Optional)</span>
                          </label>
                        </div>
                        <div class="relative">
                          <textarea
                            v-model="receivingAssessmentNotes"
                            rows="4"
                            class="w-full px-6 py-4 bg-blue-900/40 border-2 border-blue-500/40 rounded-xl focus:border-cyan-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-blue-800/50 focus:bg-blue-800/60 resize-none"
                            placeholder="Any additional notes about the device condition upon return, maintenance needs, or observations..."
                          ></textarea>
                          <div class="absolute bottom-3 right-3 text-blue-300/50 text-xs">
                            <i class="fas fa-comment-alt"></i>
                          </div>
                        </div>
                      </div>

                      <!-- Receive Device Button -->
                      <div class="relative">
                        <button
                          @click="saveReceivingAssessment"
                          :disabled="!isReceivingAssessmentComplete || isProcessingAssessment"
                          class="w-full px-6 py-3 bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 text-white rounded-xl hover:from-blue-700 hover:via-indigo-700 hover:to-purple-700 transition-all duration-500 font-bold flex items-center justify-center shadow-2xl hover:shadow-blue-500/30 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none relative overflow-hidden group text-sm"
                        >
                          <div class="absolute inset-0 bg-gradient-to-r from-blue-400/20 to-indigo-400/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                          <div class="relative z-10 flex items-center">
                            <div class="w-6 h-6 bg-white/20 rounded-lg flex items-center justify-center mr-3">
                              <i class="fas fa-undo text-sm"></i>
                            </div>
                            <span class="text-sm">
                              {{ isProcessingAssessment ? 'Processing Assessment...' : 'Receive Device from Borrower' }}
                            </span>
                            <div v-if="isProcessingAssessment" class="ml-3">
                              <div class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                            </div>
                          </div>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- ICT Officer Actions -->
              <div
                v-if="canTakeAction"
                class="booking-card bg-gradient-to-r from-emerald-600/25 to-green-600/25 border-2 border-emerald-400/40 p-4 rounded-2xl backdrop-blur-sm hover:shadow-xl hover:shadow-emerald-500/20 transition-all duration-500 group"
              >
                <div class="flex items-center space-x-3 mb-4">
                  <div
                    class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-green-600 rounded-lg flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-emerald-300/50"
                  >
                    <i class="fas fa-gavel text-white text-sm"></i>
                  </div>
                  <h3 class="text-lg font-bold text-white flex items-center">
                    <i class="fas fa-check-double mr-2 text-emerald-300 text-sm"></i>
                    ICT Officer Actions
                  </h3>
                </div>

                <!-- Comments Input -->
                <div class="mb-4">
                  <label class="block text-sm font-semibold text-emerald-100 mb-2">
                    <i class="fas fa-comment mr-2 text-xs"></i>Add Comments (Optional)
                  </label>
                  <textarea
                    v-model="approvalComments"
                    rows="3"
                    class="w-full px-3 py-2 bg-white/15 border border-emerald-300/30 rounded-xl focus:border-emerald-400 focus:outline-none text-white placeholder-emerald-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 resize-y text-sm"
                    placeholder="Enter your comments or reasons for approval/rejection..."
                  ></textarea>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                  <button
                    @click="approveRequest"
                    :disabled="isProcessing"
                    class="px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl hover:from-green-700 hover:to-emerald-700 transition-all duration-300 font-bold flex items-center justify-center shadow-lg hover:shadow-xl transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed text-sm"
                  >
                    <i class="fas fa-check mr-2 text-xs"></i>
                    {{ isProcessing ? 'Processing...' : 'Approve Request' }}
                  </button>

                  <button
                    @click="rejectRequest"
                    :disabled="isProcessing"
                    class="px-6 py-3 bg-gradient-to-r from-red-600 to-pink-600 text-white rounded-xl hover:from-red-700 hover:to-pink-700 transition-all duration-300 font-bold flex items-center justify-center shadow-lg hover:shadow-xl transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed text-sm"
                  >
                    <i class="fas fa-times mr-2 text-xs"></i>
                    {{ isProcessing ? 'Processing...' : 'Reject Request' }}
                  </button>
                </div>
              </div>

              <!-- Footer -->
              <AppFooter />
            </div>
          </div>
        </div>
      </main>
    </div>

    <!-- Success Modal -->
    <div
      v-if="showSuccessModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
    >
      <div
        class="bg-white rounded-xl shadow-2xl max-w-lg w-full transform transition-all duration-300 scale-100 animate-modal-in"
      >
        <div class="p-8 text-center">
          <div
            class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6"
          >
            <i class="fas fa-check text-green-600 text-3xl"></i>
          </div>
          <h3 class="text-2xl font-bold text-gray-800 mb-3">Action Completed Successfully!</h3>
          <p class="text-gray-600 mb-6">
            The device borrowing request has been processed successfully.
          </p>
          <button
            @click="closeSuccessModal"
            class="w-full bg-teal-600 text-white py-4 px-8 rounded-lg font-medium hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 transition duration-200 text-lg"
          >
            <i class="fas fa-arrow-left mr-3"></i>
            Return to Requests List
          </button>
        </div>
      </div>
    </div>

    <!-- Loading Modal -->
    <div
      v-if="isLoading"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
      <div class="bg-white rounded-xl shadow-2xl p-8 text-center">
        <div
          class="w-16 h-16 border-4 border-blue-600 border-t-transparent rounded-full animate-spin mx-auto mb-4"
        ></div>
        <p class="text-gray-600">Loading request details...</p>
      </div>
    </div>
  </div>
</template>

<script>
  import Header from '@/components/header.vue'
  import ModernSidebar from '@/components/ModernSidebar.vue'
  import AppFooter from '@/components/footer.vue'
  import deviceBorrowingService from '@/services/deviceBorrowingService'

  export default {
    name: 'RequestDetails',
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
        request: {},
        approvalComments: '',
        isLoading: false,
        isProcessing: false,
        showSuccessModal: false,
        // Assessment data
        assessmentType: 'issuing',
        isProcessingAssessment: false,
        issuingAssessment: {
          physical_condition: '',
          functionality: '',
          accessories_complete: false,
          visible_damage: false,
          damage_description: ''
        },
        receivingAssessment: {
          physical_condition: '',
          functionality: '',
          accessories_complete: false,
          visible_damage: false,
          damage_description: ''
        },
        issuingAssessmentNotes: '',
        receivingAssessmentNotes: '',
        physicalConditions: [
          { value: 'excellent', label: 'Excellent' },
          { value: 'good', label: 'Good' },
          { value: 'fair', label: 'Fair' },
          { value: 'poor', label: 'Poor' }
        ],
        functionalityOptions: [
          { value: 'fully_functional', label: 'Fully Functional' },
          { value: 'partially_functional', label: 'Partially Functional' },
          { value: 'not_functional', label: 'Not Functional' }
        ]
      }
    },
    computed: {
      canTakeAction() {
        // Check if request is in pending ICT approval status
        return this.request.ict_approve === 'pending' || this.request.ict_status === 'pending'
      },
      isIssuingAssessmentComplete() {
        return (
          this.issuingAssessment.physical_condition &&
          this.issuingAssessment.functionality &&
          (!this.issuingAssessment.visible_damage || this.issuingAssessment.damage_description)
        )
      },
      isReceivingAssessmentComplete() {
        return (
          this.receivingAssessment.physical_condition &&
          this.receivingAssessment.functionality &&
          (!this.receivingAssessment.visible_damage || this.receivingAssessment.damage_description)
        )
      }
    },
    async mounted() {
      console.log('🚀 RequestDetails component mounted successfully')
      console.log('🚀 Component data:', this.$data)
      console.log('🚀 Route info:', this.$route)
      console.log('🚀 API Base URL:', deviceBorrowingService)
      
      // Test API connectivity
      try {
        console.log('🔍 Testing API connectivity...')
        const testResponse = await fetch('http://127.0.0.1:8000/api/user', {
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
            'Accept': 'application/json'
          }
        })
        console.log('🔍 API Test Response:', testResponse.status, testResponse.statusText)
      } catch (apiError) {
        console.error('❌ API connectivity test failed:', apiError)
      }
      
      try {
        await this.fetchRequestDetails()
      } catch (error) {
        console.error('❌ Error in mounted hook:', error)
      }
    },
    methods: {
      async fetchRequestDetails() {
        this.isLoading = true
        try {
          const requestId = this.$route.params.id
          console.log('🔍 RequestDetails: Fetching request details for ID:', requestId)
          console.log('🔍 RequestDetails: Full route params:', this.$route.params)
          console.log('🔍 RequestDetails: Full route object:', this.$route)

          // Validate request ID
          if (!requestId || requestId === ':id') {
            console.error('Invalid request ID:', requestId)
            throw new Error('Invalid request ID: ' + requestId)
          }

          // Fetch real data from device borrowing service
          const response = await deviceBorrowingService.getRequestDetails(requestId)

          if (response.success) {
            this.request = response.data
            console.log('✅ Request details loaded:', this.request)
          } else {
            console.error('❌ Failed to load request details:', {
              message: response.message,
              status: response.status,
              details: response.details,
              error: response.error
            })
            throw new Error(response.message || response.error || 'Failed to load request details')
          }
        } catch (error) {
          console.error('❌ Error fetching request details:', error)
          console.error('❌ Error stack:', error.stack)
          console.error('❌ Error details:', {
            message: error.message,
            name: error.name,
            response: error.response
          })
          
          // Don't redirect immediately, let user see the error
          // alert('Failed to load request details: ' + (error.message || 'Unknown error'))
          // this.$router.push('/ict-approval/requests')
        } finally {
          this.isLoading = false
        }
      },

      async approveRequest() {
        if (!confirm('Are you sure you want to approve this request?')) {
          return
        }

        this.isProcessing = true
        try {
          const requestId = this.$route.params.id
          const response = await deviceBorrowingService.approveRequest(
            requestId,
            this.approvalComments
          )

          if (response.success) {
            alert('Device borrowing request approved successfully!')

            // Update local request status
            this.request.ict_approve = 'approved'

            // Redirect after a short delay
            setTimeout(() => {
              this.$router.push('/ict-approval/requests')
            }, 1500)
          } else {
            throw new Error(response.message || 'Failed to approve request')
          }
        } catch (error) {
          console.error('Error approving request:', error)
          alert(
            'Error approving request: ' +
              (error.message || 'Failed to approve the request. Please try again.')
          )
        } finally {
          this.isProcessing = false
        }
      },

      async rejectRequest() {
        if (!confirm('Are you sure you want to reject this request?')) {
          return
        }

        this.isProcessing = true
        try {
          const requestId = this.$route.params.id
          const response = await deviceBorrowingService.rejectRequest(
            requestId,
            this.approvalComments || 'No reason provided'
          )

          if (response.success) {
            alert('Device borrowing request rejected successfully!')

            // Update local request status
            this.request.ict_approve = 'rejected'

            // Redirect after a short delay
            setTimeout(() => {
              this.$router.push('/ict-approval/requests')
            }, 1500)
          } else {
            throw new Error(response.message || 'Failed to reject request')
          }
        } catch (error) {
          console.error('Error rejecting request:', error)
          alert(
            'Error rejecting request: ' +
              (error.message || 'Failed to reject the request. Please try again.')
          )
        } finally {
          this.isProcessing = false
        }
      },

      closeSuccessModal() {
        this.showSuccessModal = false
        this.goBack()
      },

      goBack() {
        this.$router.push('/ict-approval/requests')
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

      async saveIssuingAssessment() {
        if (!this.isIssuingAssessmentComplete) {
          alert('Please complete all required assessment fields')
          return
        }

        if (!confirm('Are you sure you want to issue this device to the borrower?')) {
          return
        }

        this.isProcessingAssessment = true
        try {
          const requestId = this.$route.params.id
          
          // Prepare assessment data in the format expected by the API
          const assessmentData = {
            physical_condition: this.issuingAssessment.physical_condition,
            functionality: this.issuingAssessment.functionality,
            accessories_complete: this.issuingAssessment.accessories_complete,
            visible_damage: this.issuingAssessment.visible_damage,
            damage_description: this.issuingAssessment.damage_description,
            notes: this.issuingAssessmentNotes
          }
          
          console.log('💾 Saving issuing assessment:', assessmentData)
          const response = await deviceBorrowingService.saveIssuingAssessment(requestId, assessmentData)

          if (response.success) {
            alert('Device issued successfully! Assessment saved.')
            // Update local request data with response data if available
            if (response.data) {
              this.request = { ...this.request, ...response.data }
            }
            // Refresh the request details to get updated data
            await this.fetchRequestDetails()
          } else {
            throw new Error(response.message || 'Failed to save assessment')
          }
        } catch (error) {
          console.error('Error saving issuing assessment:', error)
          alert('Error saving assessment: ' + (error.message || 'Unknown error'))
        } finally {
          this.isProcessingAssessment = false
        }
      },

      async saveReceivingAssessment() {
        if (!this.isReceivingAssessmentComplete) {
          alert('Please complete all required assessment fields')
          return
        }

        if (!confirm('Are you sure you want to mark this device as received?')) {
          return
        }

        this.isProcessingAssessment = true
        try {
          const requestId = this.$route.params.id
          
          // Prepare assessment data in the format expected by the API
          const assessmentData = {
            physical_condition: this.receivingAssessment.physical_condition,
            functionality: this.receivingAssessment.functionality,
            accessories_complete: this.receivingAssessment.accessories_complete,
            visible_damage: this.receivingAssessment.visible_damage,
            damage_description: this.receivingAssessment.damage_description,
            notes: this.receivingAssessmentNotes
          }
          
          console.log('📥 Saving receiving assessment:', assessmentData)
          const response = await deviceBorrowingService.saveReceivingAssessment(requestId, assessmentData)

          if (response.success) {
            const returnStatus = response.data?.return_status || 'returned'
            const statusMessage =
              returnStatus === 'returned_but_compromised'
                ? 'Device received but marked as compromised due to condition issues.'
                : 'Device received successfully and marked as returned.'

            alert(`Assessment completed! ${statusMessage}`)

            // Update local request data with response data if available
            if (response.data) {
              this.request = { ...this.request, ...response.data }
            }

            // Refresh the request details to get updated data
            await this.fetchRequestDetails()
          } else {
            throw new Error(response.message || 'Failed to save assessment')
          }
        } catch (error) {
          console.error('Error saving receiving assessment:', error)
          alert('Error saving assessment: ' + (error.message || 'Unknown error'))
        } finally {
          this.isProcessingAssessment = false
        }
      }
    }
  }
</script>

<style scoped>
  /* Custom animations */
  @keyframes float {
    0%,
    100% {
      transform: translateY(0px);
    }
    50% {
      transform: translateY(-10px);
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

  @keyframes modal-in {
    from {
      opacity: 0;
      transform: scale(0.9);
    }
    to {
      opacity: 1;
      transform: scale(1);
    }
  }

  .animate-float {
    animation: float 3s ease-in-out infinite;
  }

  .animate-fade-in {
    animation: fade-in 0.6s ease-out;
  }

  .animate-fade-in-delay {
    animation: fade-in-delay 0.8s ease-out 0.2s both;
  }

  .animate-slide-up {
    animation: slide-up 0.8s ease-out 0.4s both;
  }

  .animate-modal-in {
    animation: modal-in 0.3s ease-out;
  }

  /* Glass effect */
  .booking-glass-card {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
  }

  .booking-card {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(8px);
    border: 1px solid rgba(255, 255, 255, 0.1);
  }

  /* Scrollbar styling */
  ::-webkit-scrollbar {
    width: 8px;
  }

  ::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 4px;
  }

  ::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.3);
    border-radius: 4px;
  }

  ::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.5);
  }
</style>