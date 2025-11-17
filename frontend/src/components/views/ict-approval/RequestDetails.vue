<template>
  <div class="h-screen flex flex-col">
    <Header />
    <div class="flex flex-1 overflow-hidden">
      <ModernSidebar />
      <main
        class="flex-1 p-2 bg-gradient-to-br from-blue-900 via-blue-800 to-teal-900 overflow-y-auto"
      >
        <!-- Unified Loading Banner -->
        <UnifiedLoadingBanner
          :show="isLoading || isProcessing || isProcessingAssessment"
          :loadingTitle="getLoadingTitle()"
          :loadingSubtitle="getLoadingSubtitle()"
          departmentTitle="ICT APPROVAL PANEL"
          :forceSpin="true"
        />
        <!-- Background Pattern -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
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

        <div class="max-w-full mx-auto px-2 relative z-10 min-h-full">
          <!-- Header Section -->
          <div
            class="booking-glass-card rounded-t-2xl p-3 mb-0 border-b border-blue-300/30 animate-fade-in"
          >
            <div class="flex justify-between items-center">
              <!-- Left Logo -->
              <div
                class="w-16 h-16 mr-4 transform hover:scale-110 transition-transform duration-300"
              >
                <div
                  class="w-full h-full bg-gradient-to-br from-blue-500/20 to-teal-500/20 rounded-xl backdrop-blur-sm border-2 border-blue-300/40 flex items-center justify-center shadow-xl hover:shadow-blue-500/25"
                >
                  <img
                    src="/assets/images/ngao2.png"
                    alt="National Shield"
                    class="max-w-12 max-h-12 object-contain"
                    @error="handleImageError"
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
                    class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-4 py-1 rounded-full text-sm font-bold shadow-xl transform hover:scale-105 transition-all duration-300 border-2 border-blue-400/60"
                  >
                    <span class="relative z-10 flex items-center gap-2">
                      <i class="fas fa-clipboard-check text-sm"></i>
                      REQUEST DETAILS & ASSESSMENT
                    </span>
                    <div
                      class="absolute inset-0 bg-gradient-to-r from-blue-700 to-blue-800 rounded-full opacity-0 hover:opacity-100 transition-opacity duration-300"
                    ></div>
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
                  class="w-full h-full bg-gradient-to-br from-teal-500/20 to-blue-500/20 rounded-xl backdrop-blur-sm border-2 border-teal-300/40 flex items-center justify-center shadow-xl hover:shadow-teal-500/25"
                >
                  <img
                    src="/assets/images/logo2.png"
                    alt="Muhimbili Logo"
                    class="max-w-12 max-h-12 object-contain"
                    @error="handleImageError"
                  />
                </div>
              </div>
            </div>
          </div>

          <!-- Main Content -->
          <div class="booking-glass-card rounded-b-2xl overflow-hidden animate-slide-up">
            <div class="p-3 space-y-3">
              <!-- Back Button -->
              <div class="mb-2">
                <button
                  @click="goBack"
                  class="inline-flex items-center px-3 py-1 bg-gray-600 text-white text-sm rounded-lg hover:bg-gray-700 transition-colors"
                >
                  <i class="fas fa-arrow-left mr-2 text-xs"></i>
                  Back to Requests List
                </button>
              </div>

              <!-- Request Info -->
              <div v-if="!isLoading && Object.keys(request).length > 0" class="space-y-4">
                <h3 class="text-2xl font-bold text-white">Request Information</h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <div>
                    <label class="block text-base font-bold text-blue-200 mb-2">Request ID</label>
                    <div
                      class="px-3 py-2 bg-white/10 border border-blue-300/30 rounded text-white text-lg font-semibold"
                    >
                      {{ request.request_id || `REQ-${String(request.id).padStart(6, '0')}` }}
                    </div>
                  </div>

                  <div>
                    <label class="block text-base font-bold text-blue-200 mb-2"
                      >Borrower Name</label
                    >
                    <div
                      class="px-3 py-2 bg-white/10 border border-blue-300/30 rounded text-white text-lg font-semibold"
                    >
                      {{ request.borrower_name || request.borrowerName || 'Unknown' }}
                    </div>
                  </div>

                  <div>
                    <label class="block text-base font-bold text-blue-200 mb-2">Department</label>
                    <div
                      class="px-3 py-2 bg-white/10 border border-blue-300/30 rounded text-white text-lg font-semibold"
                    >
                      {{ request.department || 'Unknown Department' }}
                    </div>
                  </div>

                  <div>
                    <label class="block text-base font-bold text-blue-200 mb-2">Phone Number</label>
                    <div
                      class="px-3 py-2 bg-white/10 border border-blue-300/30 rounded text-white text-lg font-semibold"
                    >
                      {{ request.borrower_phone || request.phoneNumber || 'No phone provided' }}
                    </div>
                  </div>

                  <div>
                    <label class="block text-base font-bold text-blue-200 mb-2">PF Number</label>
                    <div
                      class="px-3 py-2 bg-white/10 border border-blue-300/30 rounded text-white text-lg font-semibold"
                    >
                      {{ request.pf_number }}
                    </div>
                  </div>

                  <div>
                    <label class="block text-base font-bold text-blue-200 mb-2">Device Type</label>
                    <div
                      class="px-3 py-2 bg-white/10 border border-blue-300/30 rounded text-white text-lg font-semibold"
                    >
                      {{
                        request.device_name ||
                        getDeviceDisplayName(request.device_type, request.custom_device)
                      }}
                    </div>
                  </div>

                  <div>
                    <label class="block text-base font-bold text-blue-200 mb-2">Booking Date</label>
                    <div
                      class="px-3 py-2 bg-white/10 border border-blue-300/30 rounded text-white text-lg font-semibold"
                    >
                      {{ formatDate(request.booking_date || request.bookingDate) }}
                    </div>
                  </div>

                  <div>
                    <label class="block text-base font-bold text-blue-200 mb-2">Return Time</label>
                    <div
                      class="px-3 py-2 bg-white/10 border border-blue-300/30 rounded text-white text-lg font-semibold"
                    >
                      {{ request.return_time || request.returnTime || 'No time specified' }}
                    </div>
                  </div>

                  <div>
                    <label class="block text-base font-bold text-blue-200 mb-2"
                      >Current Status</label
                    >
                    <div
                      class="px-3 py-2 bg-white/10 border border-blue-300/30 rounded text-lg font-semibold flex items-center gap-2"
                    >
                      <i
                        :class="
                          getStatusIcon(
                            deviceReceived
                              ? 'received'
                              : requestApproved
                                ? 'approved'
                                : request.ict_approve || request.status
                          )
                        "
                      ></i>
                      <span
                        :class="
                          getStatusTextColor(
                            deviceReceived
                              ? 'received'
                              : requestApproved
                                ? 'approved'
                                : request.ict_approve || request.status
                          )
                        "
                        >{{ currentStatusText }}</span
                      >
                    </div>
                  </div>
                </div>

                <div>
                  <label class="block text-base font-bold text-blue-200 mb-2">Purpose/Reason</label>
                  <div
                    class="px-3 py-2 bg-white/10 border border-blue-300/30 rounded text-white text-lg font-semibold"
                  >
                    {{ request.reason || request.purpose || 'No reason provided' }}
                  </div>
                </div>
              </div>

              <!-- Device Condition Assessment Section -->
              <div
                v-if="!isLoading && Object.keys(request).length > 0"
                :class="[
                  'bg-blue-800/30 backdrop-blur-sm rounded-lg p-3 border border-blue-600/40 animate-fade-in',
                  isReadOnly ? 'readonly-mode' : ''
                ]"
              >
                <div class="flex items-center gap-2 mb-3">
                  <div v-if="request.device_available === false && !isReadOnly" class="flex-1">
                    <div
                      class="px-3 py-2 rounded-md bg-red-500/20 border border-red-400/40 text-red-200 text-sm font-semibold"
                    >
                      <i class="fas fa-exclamation-triangle mr-2"></i>
                      Device is no longer available. You cannot issue this request.
                    </div>
                  </div>
                  <div
                    class="w-6 h-6 bg-gradient-to-br from-blue-500 to-blue-700 rounded-md flex items-center justify-center"
                  >
                    <i class="fas fa-clipboard-check text-white text-sm"></i>
                  </div>
                  <h3 class="text-3xl font-bold text-white">Device Condition Assessment</h3>
                  <span
                    v-if="isReadOnly"
                    class="ml-auto px-3 py-1 bg-emerald-500/20 text-emerald-200 text-xs font-semibold rounded-full border border-emerald-400/30"
                    >Finalized</span
                  >
                </div>

                <!-- Assessment Type Tabs -->
                <div class="flex mb-3 bg-blue-900/40 rounded-lg p-1">
                  <button
                    @click="assessmentType = 'issuing'"
                    :class="[
                      'flex-1 py-2 px-3 rounded-lg font-semibold transition-all duration-300 flex items-center justify-center gap-2 text-xl',
                      assessmentType === 'issuing'
                        ? 'bg-gradient-to-r from-blue-600 to-blue-700 text-white shadow-lg'
                        : 'text-blue-200 hover:text-white hover:bg-blue-800/50'
                    ]"
                  >
                    <i class="fas fa-hand-holding text-xs"></i>
                    Device Issuing
                  </button>
                  <button
                    @click="assessmentType = 'receiving'"
                    data-tab="receiving"
                    :class="[
                      'flex-1 py-2 px-3 rounded-lg font-semibold transition-all duration-300 flex items-center justify-center gap-2 text-xl',
                      assessmentType === 'receiving'
                        ? 'bg-gradient-to-r from-blue-600 to-blue-700 text-white shadow-lg'
                        : 'text-blue-200 hover:text-white hover:bg-blue-800/50'
                    ]"
                  >
                    <i class="fas fa-undo text-xs"></i>
                    Device Receiving
                  </button>
                </div>

                <!-- Assessment Form -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 mb-3">
                  <!-- Physical Condition -->
                  <div
                    class="assessment-section physical-section"
                    :class="{ 'readonly-highlight': isReadOnly }"
                  >
                    <div class="assessment-header">
                      <div
                        class="assessment-icon-wrapper bg-gradient-to-br from-blue-500 to-blue-600"
                      >
                        <i class="fas fa-search-plus text-white text-xl"></i>
                      </div>
                      <div>
                        <h4 class="text-3xl font-bold text-white mb-1">Physical Condition</h4>
                        <p class="text-blue-200 text-xl">Assess the overall physical appearance</p>
                      </div>
                      <span class="optional-badge">Required</span>
                    </div>

                    <div class="assessment-options">
                      <label
                        v-for="condition in physicalConditions"
                        :key="condition.value"
                        :class="[
                          'assessment-option',
                          selectedPhysical === condition.value ? 'selected' : '',
                          condition.value
                        ]"
                      >
                        <input
                          type="radio"
                          :value="condition.value"
                          v-model="currentAssessment.physical_condition"
                          :disabled="isReadOnly"
                          class="hidden"
                        />
                        <div class="option-content">
                          <div class="option-icon">
                            <i :class="condition.icon"></i>
                          </div>
                          <div class="option-text">
                            <span class="option-label">{{ condition.label }}</span>
                            <span class="option-description">{{ condition.description }}</span>
                          </div>
                          <div class="option-indicator">
                            <div class="radio-custom"></div>
                          </div>
                        </div>
                      </label>
                    </div>
                  </div>

                  <!-- Device Functionality -->
                  <div
                    class="assessment-section functionality-section"
                    :class="{ 'readonly-highlight': isReadOnly }"
                  >
                    <div class="assessment-header">
                      <div
                        class="assessment-icon-wrapper bg-gradient-to-br from-blue-500 to-blue-600"
                      >
                        <i class="fas fa-cogs text-white text-xl"></i>
                      </div>
                      <div>
                        <h4 class="text-3xl font-bold text-white mb-1">Device Functionality</h4>
                        <p class="text-blue-200 text-xl">Test and verify device operations</p>
                      </div>
                      <span class="required-badge">Required</span>
                    </div>

                    <div class="assessment-options">
                      <label
                        v-for="functionality in functionalityOptions"
                        :key="functionality.value"
                        :class="[
                          'assessment-option',
                          selectedFunctionality === functionality.value ? 'selected' : '',
                          functionality.value.replace('_', '-')
                        ]"
                      >
                        <input
                          type="radio"
                          :value="functionality.value"
                          v-model="currentAssessment.functionality"
                          :disabled="isReadOnly"
                          class="hidden"
                        />
                        <div class="option-content">
                          <div class="option-icon">
                            <i :class="functionality.icon"></i>
                          </div>
                          <div class="option-text">
                            <span class="option-label">{{ functionality.label }}</span>
                            <span class="option-description">{{ functionality.description }}</span>
                          </div>
                          <div class="option-indicator">
                            <div class="radio-custom"></div>
                          </div>
                        </div>
                      </label>
                    </div>
                  </div>
                </div>

                <!-- Accessories and Damage Assessment -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 mb-3">
                  <!-- Accessories Completeness -->
                  <div class="assessment-section">
                    <div class="assessment-header">
                      <div
                        class="assessment-icon-wrapper bg-gradient-to-br from-purple-500 to-purple-600"
                      >
                        <i class="fas fa-puzzle-piece text-white text-xl"></i>
                      </div>
                      <div>
                        <h4 class="text-2xl font-bold text-white mb-1">Accessories</h4>
                        <p class="text-blue-200 text-lg">Check accessory completeness</p>
                      </div>
                      <span class="optional-badge">Optional</span>
                    </div>

                    <div class="assessment-options">
                      <label class="assessment-option checkbox-option cursor-pointer">
                        <input
                          type="checkbox"
                          v-model="currentAssessment.accessories_complete"
                          :disabled="isReadOnly"
                          class="hidden"
                        />
                        <div class="option-content checkbox-content">
                          <div class="option-icon checkbox-icon bg-purple-500/20 text-purple-400">
                            <i class="fas fa-puzzle-piece"></i>
                          </div>
                          <div class="option-text">
                            <span class="option-label">All accessories included</span>
                            <span class="option-description"
                              >Cables, chargers, manuals, and original packaging</span
                            >
                          </div>
                          <div class="option-indicator">
                            <div class="checkbox-custom">
                              <i class="fas fa-check"></i>
                            </div>
                          </div>
                        </div>
                      </label>
                    </div>
                  </div>

                  <!-- Damage Assessment -->
                  <div class="assessment-section">
                    <div class="assessment-header">
                      <div
                        class="assessment-icon-wrapper bg-gradient-to-br from-red-500 to-red-600"
                      >
                        <i class="fas fa-exclamation-triangle text-white text-xl"></i>
                      </div>
                      <div>
                        <h4 class="text-2xl font-bold text-white mb-1">Damage Check</h4>
                        <p class="text-blue-200 text-lg">Identify any visible damage or issues</p>
                      </div>
                      <span class="optional-badge">Optional</span>
                    </div>

                    <div class="assessment-options">
                      <label class="assessment-option checkbox-option cursor-pointer">
                        <input
                          type="checkbox"
                          v-model="currentAssessment.visible_damage"
                          :disabled="isReadOnly"
                          class="hidden"
                        />
                        <div class="option-content checkbox-content">
                          <div class="option-icon checkbox-icon bg-red-500/20 text-red-400">
                            <i class="fas fa-exclamation-triangle"></i>
                          </div>
                          <div class="option-text">
                            <span class="option-label">Device has damage/issues</span>
                            <span class="option-description"
                              >Scratches, dents, cracks, or functional problems</span
                            >
                          </div>
                          <div class="option-indicator">
                            <div class="checkbox-custom">
                              <i class="fas fa-check"></i>
                            </div>
                          </div>
                        </div>
                      </label>

                      <!-- Damage Description (shown when damage is checked) -->
                      <div v-if="currentAssessment.visible_damage" class="mt-3">
                        <textarea
                          v-model="currentAssessment.damage_description"
                          rows="2"
                          :disabled="isReadOnly"
                          class="w-full px-3 py-2 bg-red-900/30 border-2 border-red-600/40 rounded-xl text-white text-base placeholder-red-300 resize-none focus:outline-none focus:border-red-400 focus:shadow-lg focus:shadow-red-500/20 transition-all duration-300 disabled:opacity-60"
                          placeholder="Describe the damage or issues in detail..."
                        ></textarea>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Additional Notes -->
                <div class="bg-indigo-800/20 rounded-lg p-2 mb-2">
                  <label class="flex items-center gap-2 mb-1">
                    <div class="w-4 h-4 bg-indigo-600 rounded-sm flex items-center justify-center">
                      <i class="fas fa-sticky-note text-white text-sm"></i>
                    </div>
                    <span class="text-lg font-bold text-white">Additional Notes</span>
                    <span class="text-lg text-indigo-300">(Optional)</span>
                  </label>
                  <textarea
                    v-model="currentAssessmentNotes"
                    rows="1"
                    :disabled="isReadOnly"
                    class="w-full px-1 py-1 bg-indigo-900/30 border border-indigo-600/40 rounded text-white text-sm placeholder-indigo-300 resize-none focus:outline-none focus:border-indigo-500 disabled:opacity-60"
                    placeholder="Special instructions, warranty info..."
                  ></textarea>
                </div>

                <!-- Compact Button Layout -->
                <div
                  v-if="!isReadOnly"
                  class="flex flex-col sm:flex-row gap-2 pt-3 border-t border-blue-600/30"
                >
                  <!-- Save Issuing Assessment Button -->
                  <button
                    v-if="showIssuingAssessmentButton"
                    @click="handleIssuingAssessmentAction"
                    :disabled="!issuingAssessmentButtonState.enabled"
                    :title="issuingAssessmentButtonState.tooltip"
                    :class="[
                      'flex-1 py-2 px-3 rounded-lg font-semibold transition-all duration-300 flex items-center justify-center gap-2 text-sm',
                      issuingAssessmentButtonState.classes
                    ]"
                  >
                    <i :class="['text-xs', issuingAssessmentButtonState.icon]"></i>
                    {{ issuingAssessmentButtonState.text }}
                  </button>

                  <!-- ICT Action Buttons (Approve/Reject) -->
                  <div v-if="showApprovalButton" class="flex flex-1 gap-2">
                    <!-- Approve Request Button -->
                    <button
                      @click="approveRequest"
                      :disabled="
                        !canTakeAction || isProcessing || requestApproved || !issuingAssessmentSaved
                      "
                      :class="[
                        'flex-1 py-2 px-3 rounded-lg font-semibold transition-all duration-300 flex items-center justify-center gap-2 text-sm',
                        requestApproved
                          ? 'bg-blue-600 text-white shadow-lg border-2 border-blue-400'
                          : canTakeAction && !isProcessing && issuingAssessmentSaved
                            ? 'bg-gradient-to-r from-green-600 to-green-700 text-white shadow-lg hover:from-green-700 hover:to-green-800 border-2 border-green-500/50 hover:border-green-400'
                            : 'bg-gray-600 text-gray-300 cursor-not-allowed opacity-50'
                      ]"
                      :title="getApproveButtonTooltip()"
                    >
                      <i
                        :class="[
                          'text-xs',
                          isProcessing
                            ? 'fas fa-spinner fa-spin'
                            : requestApproved
                              ? 'fas fa-check-double'
                              : 'fas fa-check'
                        ]"
                      ></i>
                      {{
                        isProcessing
                          ? 'Approving...'
                          : requestApproved
                            ? 'Request Approved'
                            : !issuingAssessmentSaved
                              ? 'Save Assessment First'
                              : 'Approve Request'
                      }}
                    </button>

                    <!-- Reject Request Button -->
                    <button
                      @click="rejectRequest"
                      :disabled="!canTakeAction || isProcessing || requestRejected"
                      :class="[
                        'flex-1 py-2 px-3 rounded-lg font-semibold transition-all duration-300 flex items-center justify-center gap-2 text-sm',
                        requestRejected
                          ? 'bg-gray-600 text-white shadow-lg border-2 border-gray-400'
                          : canTakeAction && !isProcessing
                            ? 'bg-gradient-to-r from-red-600 to-red-700 text-white shadow-lg hover:from-red-700 hover:to-red-800 border-2 border-red-500/50 hover:border-red-400'
                            : 'bg-gray-600 text-gray-300 cursor-not-allowed opacity-50'
                      ]"
                      title="Reject this device borrowing request"
                    >
                      <i
                        :class="[
                          'text-xs',
                          isProcessing
                            ? 'fas fa-spinner fa-spin'
                            : requestRejected
                              ? 'fas fa-times-circle'
                              : 'fas fa-times'
                        ]"
                      ></i>
                      {{
                        isProcessing
                          ? 'Rejecting...'
                          : requestRejected
                            ? 'Request Rejected'
                            : 'Reject Request'
                      }}
                    </button>
                  </div>

                  <!-- Receiving Assessment Button (Green) - Only show during receiving phase -->
                  <button
                    v-if="showReceivingButton"
                    @click="receiveDeviceNow"
                    :disabled="!canReceiveDevice"
                    class="flex-1 group relative overflow-hidden px-6 py-3 bg-gradient-to-r from-green-600 via-green-700 to-green-800 text-white rounded-xl font-semibold text-lg transition-all duration-300 hover:shadow-xl hover:shadow-green-500/40 border border-white/10 disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    <!-- Background Animation -->
                    <div
                      class="absolute inset-0 bg-gradient-to-r from-green-700 via-green-800 to-green-900 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                    ></div>

                    <!-- Button Content -->
                    <div class="relative z-10 flex items-center justify-center gap-3">
                      <div
                        class="flex items-center justify-center w-8 h-8 bg-white/20 rounded-full group-hover:bg-white/30 transition-colors duration-300"
                      >
                        <i class="fas fa-check-circle text-xl"></i>
                      </div>
                      <div class="flex flex-col items-start">
                        <span class="text-sm opacity-95 tracking-wide">Final Action</span>
                        <span class="text-xl font-bold tracking-wide">
                          {{ isProcessingAssessment ? 'Processing...' : 'Receive Now' }}
                        </span>
                      </div>
                    </div>

                    <!-- Status Indicator -->
                    <div v-if="deviceReceived" class="absolute top-2 right-2">
                      <div class="w-3 h-3 bg-green-400 rounded-full animate-pulse"></div>
                    </div>

                    <!-- Shimmer Effect -->
                    <div
                      class="absolute inset-0 -skew-x-12 bg-gradient-to-r from-transparent via-white/10 to-transparent opacity-0 group-hover:opacity-100 group-hover:animate-shimmer"
                    ></div>
                  </button>
                </div>

                <!-- Read-only notice -->
                <div v-if="isReadOnly" class="mt-3">
                  <div
                    class="p-2 rounded-lg border flex items-center gap-2 bg-blue-500/20 border-blue-400/40 text-blue-200"
                  >
                    <i class="fas fa-lock text-sm"></i>
                    <span class="font-medium"
                      >This request is completed and read-only. No further actions are
                      available.</span
                    >
                  </div>
                </div>

                <!-- Assessment Status Messages -->
                <div v-if="assessmentMessage" class="mt-3">
                  <div
                    :class="[
                      'p-2 rounded-lg border flex items-center gap-2 animate-fade-in',
                      assessmentMessage.type === 'success'
                        ? 'bg-green-500/20 border-green-400/40 text-green-200'
                        : 'bg-red-500/20 border-red-400/40 text-red-200'
                    ]"
                  >
                    <i
                      :class="[
                        'fas text-sm',
                        assessmentMessage.type === 'success'
                          ? 'fa-check-circle'
                          : 'fa-exclamation-circle'
                      ]"
                    ></i>
                    <span class="font-medium">{{ assessmentMessage.text }}</span>
                  </div>
                </div>
              </div>

              <!-- Error State -->
              <div
                v-else-if="!isLoading"
                class="bg-red-500/20 border border-red-400/40 rounded-lg p-6"
              >
                <div class="text-center space-y-3">
                  <i class="fas fa-exclamation-triangle text-red-400 text-4xl"></i>
                  <h3 class="text-red-200 text-xl font-semibold">Unable to Load Request Details</h3>
                  <p class="text-red-200 text-base">
                    {{ loadError || 'There was an error loading the request data.' }}
                  </p>
                  <div class="flex gap-3 justify-center">
                    <button
                      @click="fetchRequestDetails()"
                      class="px-5 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200"
                    >
                      <i class="fas fa-redo mr-2"></i>
                      Retry
                    </button>
                    <button
                      @click="$router.push('/ict-approval/requests')"
                      class="px-5 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors duration-200"
                    >
                      <i class="fas fa-list mr-2"></i>
                      Back to List
                    </button>
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

    <!-- Approval Confirmation Modal -->
    <ApprovalConfirmationModal
      :isVisible="showApprovalModal"
      :requestData="{
        request_id: request.request_id || `REQ-${String(request.id).padStart(6, '0')}`,
        borrower: request.borrower_name || 'Unknown',
        device:
          request.device_name || getDeviceDisplayName(request.device_type, request.custom_device),
        return_time: request.return_time || request.returnTime
      }"
      @confirm="confirmApproveRequest"
      @cancel="() => (showApprovalModal = false)"
    />

    <PostIssuingActionModal
      :isVisible="showPostIssuingModal && !requestApproved"
      :requestData="{
        request_id: request.request_id || `REQ-${String(request.id).padStart(6, '0')}`,
        device:
          request.device_name || getDeviceDisplayName(request.device_type, request.custom_device)
      }"
      @approve="
        () => {
          showPostIssuingModal = false
          approveRequest()
        }
      "
      @reject="
        () => {
          showPostIssuingModal = false
          showRejectModal = true
        }
      "
      @close="() => (showPostIssuingModal = false)"
    />

    <RejectRequestModal
      :isVisible="showRejectModal"
      :defaultNotes="rejectNotes"
      @confirm="confirmReject"
      @cancel="() => (showRejectModal = false)"
    />

    <ReceiveConfirmationModal
      :isVisible="showReceiveModal"
      :requestData="{
        request_id: request.request_id || `REQ-${String(request.id).padStart(6, '0')}`,
        device:
          request.device_name || getDeviceDisplayName(request.device_type, request.custom_device)
      }"
      @confirm="confirmReceive"
      @cancel="() => (showReceiveModal = false)"
    />
  </div>
</template>

<script>
  import Header from '@/components/header.vue'
  import ModernSidebar from '@/components/ModernSidebar.vue'
  import AppFooter from '@/components/footer.vue'
  import UnifiedLoadingBanner from '@/components/common/UnifiedLoadingBanner.vue'
  import ApprovalConfirmationModal from '@/components/modals/ApprovalConfirmationModal.vue'
  import PostIssuingActionModal from '@/components/modals/PostIssuingActionModal.vue'
  import RejectRequestModal from '@/components/modals/RejectRequestModal.vue'
  import ReceiveConfirmationModal from '@/components/modals/ReceiveConfirmationModal.vue'
  import deviceBorrowingService from '@/services/deviceBorrowingService'
  import apiClient from '@/utils/apiClient'
  import notificationService from '@/services/notificationService'

  export default {
    name: 'RequestDetails',
    components: {
      Header,
      ModernSidebar,
      AppFooter,
      UnifiedLoadingBanner,
      ApprovalConfirmationModal,
      PostIssuingActionModal,
      RejectRequestModal,
      ReceiveConfirmationModal
    },
    data() {
      return {
        request: {},
        isLoading: false,
        isProcessing: false,
        loadError: null,

        // Assessment data
        assessmentType: 'issuing', // 'issuing' or 'receiving'
        issuingAssessment: {
          physical_condition: '',
          functionality: '',
          accessories_complete: false,
          visible_damage: false,
          damage_description: '',
          notes: ''
        },
        receivingAssessment: {
          physical_condition: '',
          functionality: '',
          accessories_complete: false,
          visible_damage: false,
          damage_description: '',
          notes: ''
        },
        issuingAssessmentNotes: '',
        receivingAssessmentNotes: '',
        isProcessingAssessment: false,
        issuingAssessmentSaved: false,
        receivingAssessmentSaved: false,
        assessmentMessage: null,

        // Workflow state tracking
        deviceIssued: false, // Track if device has been issued
        deviceReceived: false, // Track if device has been received back
        requestApproved: false, // Track if request has been approved

        // Modal state
        showApprovalModal: false,
        showPostIssuingModal: false,
        showRejectModal: false,
        rejectNotes: '',
        showReceiveModal: false,

        // Assessment options
        physicalConditions: [
          {
            value: 'excellent',
            label: 'Excellent',
            icon: 'fas fa-star',
            color: 'green-400',
            description: 'Like new, no visible wear or damage'
          },
          {
            value: 'good',
            label: 'Good',
            icon: 'fas fa-thumbs-up',
            color: 'blue-400',
            description: 'Minor wear, fully functional appearance'
          },
          {
            value: 'fair',
            label: 'Fair',
            icon: 'fas fa-exclamation',
            color: 'yellow-400',
            description: 'Noticeable wear but still acceptable'
          },
          {
            value: 'poor',
            label: 'Poor',
            icon: 'fas fa-times',
            color: 'red-400',
            description: 'Significant wear, damage, or deterioration'
          }
        ],
        functionalityOptions: [
          {
            value: 'fully_functional',
            label: 'Fully Functional',
            icon: 'fas fa-check-circle',
            color: 'green-400',
            description: 'All features work perfectly as expected'
          },
          {
            value: 'partially_functional',
            label: 'Partially Functional',
            icon: 'fas fa-exclamation-circle',
            color: 'yellow-400',
            description: 'Some features work, others may have issues'
          },
          {
            value: 'not_functional',
            label: 'Not Functional',
            icon: 'fas fa-times-circle',
            color: 'red-400',
            description: 'Device does not work or has major failures'
          }
        ]
      }
    },
    computed: {
      canTakeAction() {
        // Check if request exists and has pending status
        if (!this.request || !this.request.id) return false

        // Check various status fields that might indicate pending approval
        const isPending =
          this.request.ict_approve === 'pending' ||
          this.request.ict_status === 'pending' ||
          this.request.status === 'pending' ||
          (!this.request.ict_approve && !this.request.ict_status) // No approval status set yet

        console.log('🔍 canTakeAction check:', {
          requestId: this.request.id,
          ict_approve: this.request.ict_approve,
          ict_status: this.request.ict_status,
          status: this.request.status,
          isPending
        })

        return isPending
      },

      requestRejected() {
        // Check if request has been rejected
        return this.request.ict_approve === 'rejected' || this.request.ict_status === 'rejected'
      },

      currentAssessment() {
        return this.assessmentType === 'issuing' ? this.issuingAssessment : this.receivingAssessment
      },

      // Read-only mode when request is fully completed/received or rejected
      isReadOnly() {
        const status = (this.request?.status || '').toLowerCase()
        const returnStatus = (this.request?.return_status || '').toLowerCase()
        const receivedMarkers = ['returned', 'completed', 'complete', 'closed', 'done', 'finished']
        const returnMarkers = ['returned', 'returned_but_compromised']
        return (
          this.deviceReceived ||
          receivedMarkers.includes(status) ||
          returnMarkers.includes(returnStatus) ||
          !!this.request?.device_received_at ||
          this.requestRejected
        )
      },

      currentAssessmentNotes: {
        get() {
          return this.assessmentType === 'issuing'
            ? this.issuingAssessmentNotes
            : this.receivingAssessmentNotes
        },
        set(value) {
          if (this.assessmentType === 'issuing') {
            this.issuingAssessmentNotes = value
          } else {
            this.receivingAssessmentNotes = value
          }
        }
      },

      isIssuingAssessmentComplete() {
        const assessment = this.issuingAssessment
        const requiredFields = assessment.physical_condition && assessment.functionality
        const damageValid = !assessment.visible_damage || assessment.damage_description?.trim()
        return requiredFields && damageValid
      },

      // Display helpers for finalized (read-only) state
      finalAssessment() {
        // Prefer receiving assessment if present, otherwise issuing
        return this.receivingAssessmentSaved ? this.receivingAssessment : this.issuingAssessment
      },

      selectedPhysical() {
        return this.isReadOnly
          ? this.finalAssessment?.physical_condition || ''
          : this.currentAssessment.physical_condition
      },

      selectedFunctionality() {
        return this.isReadOnly
          ? this.finalAssessment?.functionality || ''
          : this.currentAssessment.functionality
      },

      isReceivingAssessmentComplete() {
        const assessment = this.receivingAssessment
        const requiredFields = assessment.physical_condition && assessment.functionality
        const damageValid = !assessment.visible_damage || assessment.damage_description?.trim()
        return requiredFields && damageValid
      },

      canApproveRequest() {
        // Approval button should be enabled when:
        // 1. Request is pending (can take action)
        // 2. Assessment is saved (device condition assessed)
        // 3. Request is not already approved
        // 4. Not currently processing
        const canApprove =
          this.canTakeAction &&
          this.issuingAssessmentSaved &&
          !this.requestApproved &&
          !this.isProcessing &&
          !this.isReadOnly

        console.log('🔍 canApproveRequest check:', {
          canTakeAction: this.canTakeAction,
          issuingAssessmentSaved: this.issuingAssessmentSaved,
          requestApproved: this.requestApproved,
          isProcessing: this.isProcessing,
          canApprove
        })

        return canApprove
      },

      canSaveIssuingAssessment() {
        // Issuing assessment can be saved if:
        // 1. Assessment is complete (all required fields filled)
        // 2. Not already saved
        // 3. Request has pending ICT approval status specifically
        // 4. Device is available
        // 5. Not currently processing
        const isPendingRequest =
          this.request.ict_approve === 'pending' ||
          this.request.ict_status === 'pending' ||
          (!this.request.ict_approve && !this.request.ict_status)

        const isDeviceAvailable = this.request.device_available !== false

        const canSave =
          this.isIssuingAssessmentComplete &&
          !this.issuingAssessmentSaved &&
          !this.deviceIssued &&
          isPendingRequest &&
          isDeviceAvailable &&
          !this.isProcessingAssessment &&
          !this.isReadOnly

        console.log('🔍 canSaveIssuingAssessment check:', {
          isComplete: this.isIssuingAssessmentComplete,
          alreadySaved: this.issuingAssessmentSaved,
          isPending: isPendingRequest,
          processing: this.isProcessingAssessment,
          requestStatus: {
            ict_approve: this.request.ict_approve,
            ict_status: this.request.ict_status,
            status: this.request.status
          },
          canSave
        })

        return canSave
      },

      canReceiveDevice() {
        // Device can be received ONLY after approval and when:
        // 1. Receiving assessment is complete
        // 2. Request is approved
        // 3. Not already received
        // 4. Not currently processing
        const canReceive =
          this.isReceivingAssessmentComplete &&
          this.requestApproved &&
          !this.deviceReceived &&
          !this.isProcessingAssessment &&
          !this.isReadOnly

        console.log('🔍 canReceiveDevice check:', {
          isReceivingAssessmentComplete: this.isReceivingAssessmentComplete,
          requestApproved: this.requestApproved,
          deviceReceived: this.deviceReceived,
          isProcessingAssessment: this.isProcessingAssessment,
          canReceive
        })

        return canReceive
      },

      showIssuingAssessmentButton() {
        // Show issuing assessment button only if:
        // 1. Assessment type is 'issuing'
        // 2. Device hasn't been received yet
        return this.assessmentType === 'issuing' && !this.deviceReceived && !this.isReadOnly
      },

      showApprovalButton() {
        // Show approval buttons (approve/reject) in two scenarios:
        // 1. ISSUING TAB: Initial approval phase (before device is issued)
        // 2. RECEIVING TAB: Final approval phase (after device is issued but request still pending)
        const showOnIssuingTab =
          this.canTakeAction &&
          !this.requestApproved &&
          !this.requestRejected &&
          this.assessmentType === 'issuing' &&
          !this.isReadOnly

        const showOnReceivingTab =
          this.canTakeAction &&
          !this.requestApproved &&
          !this.requestRejected &&
          this.assessmentType === 'receiving' &&
          this.issuingAssessmentSaved &&
          !this.isReadOnly // Device has been issued but request is not finalized

        const showButtons = showOnIssuingTab || showOnReceivingTab

        console.log('🔍 showApprovalButton check:', {
          canTakeAction: this.canTakeAction,
          requestApproved: this.requestApproved,
          requestRejected: this.requestRejected,
          assessmentType: this.assessmentType,
          issuingAssessmentSaved: this.issuingAssessmentSaved,
          showOnIssuingTab,
          showOnReceivingTab,
          showButtons
        })

        return showButtons
      },

      showReceivingButton() {
        // Show receiving button only after approval when:
        // 1. Assessment type is 'receiving'
        // 2. Request approved
        // 3. Device hasn't been received yet
        return (
          this.assessmentType === 'receiving' &&
          this.requestApproved &&
          !this.deviceReceived &&
          !this.isReadOnly
        )
      },

      issuingAssessmentButtonState() {
        // Determine button state and messaging for issuing assessment
        const isPendingRequest =
          this.request.ict_approve === 'pending' ||
          this.request.ict_status === 'pending' ||
          (!this.request.ict_approve && !this.request.ict_status)

        if (this.isReadOnly) {
          return {
            enabled: false,
            text: 'Request Completed (Read-only)',
            icon: 'fas fa-lock',
            classes: 'bg-gray-700 text-gray-300 cursor-not-allowed opacity-60',
            tooltip: 'This request has been completed. Actions are disabled.'
          }
        }

        if (this.request.device_available === false) {
          return {
            enabled: false,
            text: 'Device Unavailable',
            icon: 'fas fa-ban',
            classes: 'bg-gray-700 text-gray-300 cursor-not-allowed opacity-60',
            tooltip: 'This device is no longer available. Please choose another request.'
          }
        }

        if (this.deviceIssued && !this.issuingAssessmentSaved) {
          return {
            enabled: false,
            text: 'Already Issued - Proceed to Receiving',
            icon: 'fas fa-check-double',
            classes: 'bg-blue-600 text-white shadow-lg cursor-not-allowed opacity-80',
            tooltip:
              'This device was already issued earlier. Switch to Device Receiving to continue.'
          }
        }

        if (this.issuingAssessmentSaved) {
          // After assessment is saved, fully mute this button (no further actions)
          return {
            enabled: false,
            text: 'Issuing Assessment Saved',
            icon: 'fas fa-check',
            classes:
              'bg-gray-700 text-gray-300 cursor-not-allowed opacity-70 border border-gray-500/60',
            tooltip: 'Issuing assessment has been saved. Use the Approve/Reject buttons below to continue.'
          }
        }

        if (!isPendingRequest) {
          const currentStatus =
            this.request.ict_approve || this.request.ict_status || this.request.status
          return {
            enabled: false,
            text: `Cannot Issue (${currentStatus || 'Unknown'} Status)`,
            icon: 'fas fa-lock',
            classes: 'bg-gray-700 text-gray-400 cursor-not-allowed opacity-60',
            tooltip: `Device can only be issued for pending requests. Current status: ${currentStatus || 'Unknown'}`
          }
        }

        if (!this.isIssuingAssessmentComplete) {
          return {
            enabled: false,
            text: 'Complete Assessment First',
            icon: 'fas fa-exclamation-triangle',
            classes: 'bg-yellow-700 text-yellow-200 cursor-not-allowed opacity-60',
            tooltip: 'Please fill in all required assessment fields'
          }
        }

        if (this.isProcessingAssessment) {
          return {
            enabled: false,
            text: 'Saving...',
            icon: 'fas fa-spinner fa-spin',
            classes: 'bg-blue-600 text-white opacity-75',
            tooltip: 'Processing assessment...'
          }
        }

        return {
          enabled: true,
          text: 'Save Issuing Assessment',
          icon: 'fas fa-save',
          classes:
            'bg-gradient-to-r from-blue-600 to-blue-700 text-white shadow-lg hover:from-blue-700 hover:to-blue-800',
          tooltip: 'Save device condition assessment and issue device'
        }
      },

      currentStatusText() {
        if (this.deviceReceived) {
          // Check return status for more specific messaging
          if (this.request.return_status === 'returned_but_compromised') {
            return 'Received (Compromised)'
          } else {
            return 'Received'
          }
        } else if (this.requestApproved) {
          return 'Approved'
        } else {
          return this.getStatusText(this.request.ict_approve || this.request.status)
        }
      }
    },
    async mounted() {
      console.log('🚀 RequestDetails component mounted successfully')
      console.log('🚀 Route ID:', this.$route.params.id)
      console.log('🚀 Route object:', this.$route)

      // Ensure we have a valid, positive numeric route ID
      const idParam = String(this.$route.params.id || '').trim()
      const isNumeric = /^\d+$/.test(idParam)
      if (!isNumeric || parseInt(idParam, 10) <= 0) {
        console.error('❌ Invalid route ID:', idParam)
        this.loadError = 'Invalid request ID in the URL.'
        this.isLoading = false
        return
      }

      try {
        await this.fetchRequestDetails()
      } catch (error) {
        console.error('❌ Error in mounted hook:', error)
        this.isLoading = false
      }
    },
    methods: {
      async fetchRequestDetails() {
        this.isLoading = true
        this.loadError = null
        try {
          const requestId = this.$route.params.id
          console.log('🔍 Fetching request details for ID:', requestId)

          if (!requestId || requestId === ':id' || !/^\d+$/.test(String(requestId))) {
            throw new Error('Invalid request ID: ' + requestId)
          }

          const response = await deviceBorrowingService.getRequestDetails(requestId)

          if (response.success) {
            this.request = response.data

            // Initialize workflow state based on request data
            this.initializeWorkflowState()

            console.log('✅ Request details loaded:', this.request)
            console.log('🔍 Request status fields:', {
              id: this.request.id,
              status: this.request.status,
              ict_approve: this.request.ict_approve,
              ict_status: this.request.ict_status,
              canTakeAction: this.canTakeAction,
              workflowState: {
                deviceIssued: this.deviceIssued,
                deviceReceived: this.deviceReceived,
                requestApproved: this.requestApproved
              }
            })
          } else {
            console.error('❌ Failed to load request details:', response)
            // Map known statuses to friendly messages without throwing
            if (response.status === 404) {
              this.loadError = 'Request not found. It may have been deleted or the ID is incorrect.'
            } else if (response.status === 400) {
              this.loadError = response.message || 'Invalid request ID.'
            } else {
              this.loadError = response.message || 'Failed to load request details.'
            }
            return
          }
        } catch (error) {
          console.error('❌ Error fetching request details:', error)
          const status = error?.response?.status
          if (status === 404) {
            this.loadError = 'Request not found. It may have been deleted or the ID is incorrect.'
          } else if (/Invalid request ID/i.test(error?.message)) {
            this.loadError = 'Invalid request ID in the URL.'
          } else {
            this.loadError = error?.response?.data?.message || 'Failed to load request details.'
          }
        } finally {
          this.isLoading = false
        }
      },

      initializeWorkflowState() {
        // Initialize workflow state based on request data
        this.requestApproved =
          this.request.ict_approve === 'approved' || this.request.status === 'approved'
        // Device is considered issued only when explicitly issued or in use
        this.deviceIssued = !!(
          this.request.device_issued_at ||
          this.request.device_condition_issuing ||
          this.request.status === 'in_use'
        )
        // Device is considered received only on explicit receive markers
        const returnMarkers = ['returned', 'returned_but_compromised']
        this.deviceReceived = !!(
          this.request.device_received_at ||
          this.request.device_condition_receiving ||
          returnMarkers.includes((this.request.return_status || '').toLowerCase()) ||
          this.request.status === 'returned' ||
          /completed|complete|closed|done|finished/i.test(this.request.status || '')
        )
        // Consider issued state as saved even if assessments table was not populated (backend may have set JSON/status)
        this.issuingAssessmentSaved = !!(this.request.device_condition_issuing || this.deviceIssued)
        this.receivingAssessmentSaved = !!this.request.device_condition_receiving

        console.log('🔄 Workflow state initialized:', {
          requestApproved: this.requestApproved,
          deviceIssued: this.deviceIssued,
          deviceReceived: this.deviceReceived,
          issuingAssessmentSaved: this.issuingAssessmentSaved,
          receivingAssessmentSaved: this.receivingAssessmentSaved
        })

        // Load assessment objects from request for display
        this.loadAssessmentsFromRequest()

        // If issuing assessment was already saved previously (or approved) and device not yet received,
        // auto-switch UI to the Receiving tab and guide the user.
        // Only switch to Device Receiving after approval, not merely after issuing save
        if (this.requestApproved && !this.deviceReceived) {
          this.assessmentType = 'receiving'
        }

        // If read-only, ensure we show the final assessment (receiving when available)
        if (this.isReadOnly && this.receivingAssessmentSaved) {
          this.assessmentType = 'receiving'
        }
      },

      async approveRequest() {
        // Guard: only allow when UI says we can approve
        if (!this.canApproveRequest) {
          this.showAssessmentMessage(
            'Cannot approve: request is not pending or assessment not saved.',
            'error'
          )
          return
        }
        // Open attractive confirmation modal instead of browser confirm()
        this.showApprovalModal = true
      },

      async confirmApproveRequest() {
        this.isProcessing = true
        try {
          const requestId = this.$route.params.id
          const response = await deviceBorrowingService.approveRequest(
            requestId,
            '' // No comments needed
          )

          if (response.success) {
            this.showAssessmentMessage('Device borrowing request approved successfully!', 'success')
            this.request.ict_approve = 'approved'
            this.requestApproved = true
            this.deviceIssued = true

            // Update the request data with the response
            if (response.data) {
              this.request = { ...this.request, ...response.data }
            }

            console.log('✅ Request approved, workflow state updated:', {
              requestApproved: this.requestApproved,
              deviceIssued: this.deviceIssued
            })

            // Redirect back to requests list promptly after success
            this.showApprovalModal = false
            this.$router.push('/ict-approval/requests')
          } else {
            throw new Error(response.message || 'Failed to approve request')
          }
        } catch (error) {
          console.error('Error approving request:', error)
          const status = error?.response?.status
          const message = error?.response?.data?.message || error?.message || ''
          if (status === 403) {
            this.showAssessmentMessage(
              'You do not have permission to approve device bookings. Please contact an administrator.',
              'error'
            )
          } else if (status === 400 && /pending ICT/i.test(message)) {
            this.showAssessmentMessage(
              'Approval allowed only for pending ICT requests. Refreshing request…',
              'error'
            )
            try {
              await this.fetchRequestDetails()
            } catch (e) {
              console.warn('Refresh after approval constraint failed:', e)
            }
            this.showApprovalModal = false
            this.showPostIssuingModal = false
          } else {
            this.showAssessmentMessage(
              'Error approving request: ' + (message || 'Please try again'),
              'error'
            )
          }
        } finally {
          this.isProcessing = false
        }
      },

      async rejectRequest() {
        // Open styled reject modal
        this.rejectNotes = ''
        this.showRejectModal = true
      },

      async confirmReject(rejectionReason) {
        this.showRejectModal = false
        this.isProcessing = true
        try {
          const requestId = this.$route.params.id
          const response = await deviceBorrowingService.rejectRequest(
            requestId,
            rejectionReason.trim()
          )

          if (response.success) {
            this.showAssessmentMessage('Device borrowing request rejected successfully!', 'success')
            this.request.ict_approve = 'rejected'

            // Update the request data with rejection details
            if (response.data) {
              this.request = { ...this.request, ...response.data }
            }

            console.log('✅ Request rejected successfully:', {
              requestId: this.request.id,
              rejectionReason: rejectionReason
            })

            // Automatically redirect to requests list
            this.$router.push('/ict-approval/requests')
          } else {
            throw new Error(response.message || 'Failed to reject request')
          }
        } catch (error) {
          console.error('Error rejecting request:', error)
          alert('Error rejecting request: ' + (error.message || 'Please try again'))
        } finally {
          this.isProcessing = false
        }
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

      getStatusIcon(status) {
        // Handle custom status icons
        if (status === 'received') {
          return 'fas fa-check-double text-green-400'
        } else if (status === 'returned_but_compromised') {
          return 'fas fa-exclamation-triangle text-yellow-400'
        }
        return deviceBorrowingService.getStatusIcon(status)
      },

      getStatusText(status) {
        return deviceBorrowingService.getStatusText(status)
      },

      getStatusTextColor(status) {
        // Return appropriate text color classes based on status
        switch (status) {
          case 'pending':
            return 'text-yellow-400 font-semibold'
          case 'approved':
            return 'text-green-400 font-semibold'
          case 'rejected':
            return 'text-red-400 font-semibold'
          case 'received':
            return 'text-green-400 font-semibold'
          case 'returned_but_compromised':
            return 'text-yellow-400 font-semibold'
          default:
            return 'text-white font-semibold'
        }
      },

      getApproveButtonTooltip() {
        if (this.requestApproved) {
          return 'This request has already been approved'
        }
        if (!this.canTakeAction) {
          return 'This request cannot be approved - check request status'
        }
        if (this.isProcessing) {
          return 'Processing request...'
        }
        if (!this.issuingAssessmentSaved) {
          return 'Please save the device issuing assessment first before approving'
        }
        return 'Approve this device borrowing request'
      },

      handleImageError(event) {
        console.warn('Image failed to load:', event.target.src)
        event.target.style.display = 'none'
      },

      getLoadingTitle() {
        if (this.isProcessing) return 'Processing Request'
        if (this.isProcessingAssessment) return 'Saving Assessment'
        if (this.isLoading) return 'Loading Request Details'
        return 'Loading'
      },

      getLoadingSubtitle() {
        if (this.isProcessing) return 'Applying your approval action...'
        if (this.isProcessingAssessment) return 'Persisting device assessment data...'
        if (this.isLoading) return 'Retrieving request information...'
        return 'Please wait...'
      },

      // Assessment methods
      handleIssuingAssessmentAction() {
        // Check if we should save assessment or approve request
        if (!this.issuingAssessmentSaved) {
          this.saveIssuingAssessment()
        } else if (!this.requestApproved) {
          this.approveRequest()
        }
      },

      async saveIssuingAssessment() {
        // Prevent multiple clicks
        if (this.isProcessingAssessment || this.issuingAssessmentSaved) {
          console.log('⚠️ Assessment already being processed or saved')
          return
        }

        if (!this.isIssuingAssessmentComplete) {
          this.showAssessmentMessage(
            'Please complete all required assessment fields before saving.',
            'error'
          )
          return
        }

        // Preflight availability check to avoid backend 400s
        try {
          const inventoryId = this.request.device_inventory_id
          if (inventoryId) {
            const availResp = await apiClient.get(
              `/booking-service/devices/${inventoryId}/availability`
            )
            const avail = availResp.data?.data || availResp.data
            if (!availResp.data?.success || avail?.available === false) {
              const msg = avail?.message || 'Device is currently unavailable.'
              this.showAssessmentMessage(msg, 'error')
              this.request.device_available = false
              return
            }
          }
        } catch (e) {
          console.warn('Availability precheck failed (continuing to backend):', e?.message)
        }

        this.isProcessingAssessment = true
        try {
          const requestId = this.$route.params.id
          const assessmentData = {
            physical_condition: this.issuingAssessment.physical_condition,
            functionality: this.issuingAssessment.functionality,
            accessories_complete: this.issuingAssessment.accessories_complete,
            visible_damage: this.issuingAssessment.visible_damage,
            damage_description: this.issuingAssessment.visible_damage
              ? this.issuingAssessment.damage_description
              : null,
            assessment_notes: this.issuingAssessmentNotes || null
          }

          console.log('💾 Saving issuing assessment:', assessmentData)

          const response = await deviceBorrowingService.saveIssuingAssessment(
            requestId,
            assessmentData
          )

          if (response.success) {
            this.issuingAssessmentSaved = true
            this.deviceIssued = true

            // Update request data with response
            if (response.data) {
              this.request = { ...this.request, ...response.data }
            }

            // Keep on Issuing tab until approval; prompt user to approve first
            this.assessmentMessage = {
              type: 'success',
              text: 'Issuing assessment saved. Please approve the request to continue to Device Receiving.'
            }

            // Show post-issuing action modal (approve/reject) only if request still pending
            this.showPostIssuingModal = this.canTakeAction

            console.log('✅ Device assessment saved successfully, switched to receiving phase')
          } else {
            throw new Error(response.message || 'Failed to save issuing assessment')
          }
        } catch (error) {
          console.error('❌ Error saving issuing assessment:', error)

          const status = error?.response?.status
          const message = error?.response?.data?.message || error?.message || ''

          // Gracefully handle already-issued case (idempotency)
          if (status === 400 && /already been issued|issue the same device twice/i.test(message)) {
            this.deviceIssued = true
            this.issuingAssessmentSaved = true
            this.assessmentType = 'receiving'
            this.showAssessmentMessage(
              'Device already issued for this request. Proceed to Device Receiving.',
              'success'
            )
            // Offer approve/reject actions since issuing is effectively complete (only if still pending)
            this.showPostIssuingModal = this.canTakeAction
            // Refresh request to sync latest backend state
            try {
              await this.fetchRequestDetails()
            } catch (e) {
              console.warn(
                'Failed to refresh request details after already-issued notice:',
                e?.message
              )
            }
          } else {
            this.showAssessmentMessage(
              'Error saving assessment: ' + (message || 'Please try again'),
              'error'
            )
          }
        } finally {
          this.isProcessingAssessment = false
        }
      },

      async receiveDeviceNow() {
        // Prevent multiple clicks
        if (this.isProcessingAssessment || this.deviceReceived) {
          console.log('⚠️ Device already being processed or already received')
          return
        }

        if (!this.isReceivingAssessmentComplete) {
          alert('Please complete all required assessment fields before receiving the device.')
          return
        }

        // Open styled confirmation instead of browser confirm
        this.showReceiveModal = true
      },

      async confirmReceive() {
        this.showReceiveModal = false
        this.isProcessingAssessment = true
        try {
          const requestId = this.$route.params.id
          const assessmentData = {
            physical_condition: this.receivingAssessment.physical_condition,
            functionality: this.receivingAssessment.functionality,
            accessories_complete: this.receivingAssessment.accessories_complete,
            visible_damage: this.receivingAssessment.visible_damage,
            damage_description: this.receivingAssessment.visible_damage
              ? this.receivingAssessment.damage_description
              : null,
            assessment_notes: this.receivingAssessmentNotes || null
          }

          console.log('💾 Processing device receiving with assessment:', assessmentData)

          const response = await deviceBorrowingService.saveReceivingAssessment(
            requestId,
            assessmentData
          )

          if (response.success) {
            this.deviceReceived = true
            this.receivingAssessmentSaved = true

            // Determine return status based on assessment
            let returnStatus = 'returned'
            let statusMessage = 'Device received successfully!'

            if (this.receivingAssessment.visible_damage) {
              returnStatus = 'returned_but_compromised'
              statusMessage = 'Device received with damage/issues noted.'
            } else if (this.receivingAssessment.functionality === 'not_functional') {
              returnStatus = 'returned_but_compromised'
              statusMessage = 'Device received but not functional.'
            } else if (this.receivingAssessment.physical_condition === 'poor') {
              returnStatus = 'returned_but_compromised'
              statusMessage = 'Device received in poor condition.'
            }

            // Update request status
            this.request.status = 'returned'
            this.request.return_status = returnStatus
            this.request.device_received_at = new Date().toISOString()

            // Update request data with response
            if (response.data) {
              this.request = { ...this.request, ...response.data }
            }

            // Refresh local assessment display from backend payload
            this.loadAssessmentsFromRequest()

            this.showAssessmentMessage(
              `${statusMessage} The request has been completed and return status updated.`,
              'success'
            )
            // Lock UI into read-only mode
            this.deviceReceived = true
            console.log('✅ Device received successfully, return status updated:', {
              returnStatus,
              deviceReceived: this.deviceReceived
            })

            // Clear cached pending notifications and redirect
            try {
              notificationService.clearCache()
            } catch (e) {
              // Non-blocking: cache clear failure should not stop navigation
              console.warn('Notification cache clear failed:', e?.message || e)
            }
            this.$router.push('/ict-approval/requests')
          } else {
            throw new Error(response.message || 'Failed to receive device')
          }
        } catch (error) {
          console.error('❌ Error receiving device:', error)
          this.showAssessmentMessage(
            'Error receiving device: ' + (error.message || 'Please try again'),
            'error'
          )
        } finally {
          this.isProcessingAssessment = false
        }
      },

      showAssessmentMessage(text, type) {
        this.assessmentMessage = { text, type }

        // Auto-hide messages after 5 seconds
        setTimeout(() => {
          if (this.assessmentMessage && this.assessmentMessage.text === text) {
            this.assessmentMessage = null
          }
        }, 5000)
      },

      // Safely map backend assessment blobs into UI state for display
      loadAssessmentsFromRequest() {
        const parseBlob = (blob) => {
          if (!blob) return null
          if (typeof blob === 'object') return blob
          try {
            return JSON.parse(blob)
          } catch (_) {
            return null
          }
        }

        const issuing = parseBlob(this.request.device_condition_issuing)
        if (issuing) {
          this.issuingAssessment = {
            physical_condition:
              issuing.physical_condition || this.issuingAssessment.physical_condition || '',
            functionality: issuing.functionality || this.issuingAssessment.functionality || '',
            accessories_complete: !!issuing.accessories_complete,
            visible_damage: !!issuing.visible_damage,
            damage_description: issuing.damage_description || '',
            notes: issuing.assessment_notes || issuing.notes || ''
          }
          this.issuingAssessmentNotes = this.issuingAssessment.notes
        }

        const receiving = parseBlob(this.request.device_condition_receiving)
        if (receiving) {
          this.receivingAssessment = {
            physical_condition:
              receiving.physical_condition || this.receivingAssessment.physical_condition || '',
            functionality: receiving.functionality || this.receivingAssessment.functionality || '',
            accessories_complete: !!receiving.accessories_complete,
            visible_damage: !!receiving.visible_damage,
            damage_description: receiving.damage_description || '',
            notes: receiving.assessment_notes || receiving.notes || ''
          }
          this.receivingAssessmentNotes = this.receivingAssessment.notes
        }
      }
    }
  }
</script>

<style scoped lang="postcss">
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

  @keyframes shimmer {
    0% {
      transform: translateX(-100%) skewX(-12deg);
    }
    100% {
      transform: translateX(200%) skewX(-12deg);
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

  .animate-shimmer {
    animation: shimmer 1.5s ease-in-out;
  }

  /* Enhanced button hover effects */
  .group:hover .animate-shimmer {
    animation: shimmer 1.5s ease-in-out infinite;
  }

  /* Assessment Section Styles */
  .assessment-section {
    @apply bg-gradient-to-br from-blue-800/40 to-blue-900/60 backdrop-blur-sm rounded-2xl p-4 border border-blue-600/30 shadow-xl;
  }

  .assessment-header {
    @apply flex items-center gap-3 mb-3 pb-2 border-b border-blue-600/30;
  }

  .assessment-icon-wrapper {
    @apply w-10 h-10 rounded-lg flex items-center justify-center shadow-md;
  }

  .required-badge {
    @apply ml-auto px-3 py-1 bg-red-500/20 text-red-300 text-xs font-semibold rounded-full border border-red-400/30;
  }

  .optional-badge {
    @apply ml-auto px-3 py-1 bg-blue-500/20 text-blue-300 text-xs font-semibold rounded-full border border-blue-400/30;
  }

  .assessment-options {
    @apply space-y-2;
  }

  .assessment-option {
    @apply block cursor-pointer transition-all duration-300 hover:shadow-md;
  }

  .option-content {
    @apply flex items-center gap-3 p-3 bg-blue-700/20 rounded-xl border-2 border-blue-600/40 transition-all duration-300;
  }

  .option-icon {
    @apply w-8 h-8 rounded-lg flex items-center justify-center text-lg transition-all duration-300;
  }

  .option-text {
    @apply flex-1;
  }

  .option-label {
    @apply block text-white font-bold text-2xl mb-1;
  }

  .option-description {
    @apply block text-blue-300 text-xl leading-relaxed;
  }

  .option-indicator {
    @apply flex items-center;
  }

  .radio-custom {
    @apply w-5 h-5 rounded-full border-2 border-blue-400 bg-blue-700 transition-all duration-300 flex items-center justify-center;
  }

  /* Selected states for different conditions */
  .assessment-option.selected .option-content {
    @apply border-emerald-400/60 bg-emerald-500/10 shadow-emerald-500/20;
  }

  /* Read-only visualization: emphasize ONLY selected choices in Physical & Functionality */
  .readonly-mode .readonly-highlight {
    @apply border-red-400/40;
  }

  .readonly-mode .readonly-highlight .assessment-option.selected .option-content {
    @apply border-red-400/70 bg-red-500/10 shadow-red-500/20;
  }

  .readonly-mode .readonly-highlight .assessment-option.selected .radio-custom {
    @apply border-red-400 bg-red-500;
  }

  .assessment-option.selected .radio-custom {
    @apply border-emerald-400 bg-emerald-500;
  }

  .assessment-option.selected .radio-custom::after {
    content: '';
    @apply w-2 h-2 bg-white rounded-full;
  }

  /* Excellent condition */
  .assessment-option.excellent.selected .option-content {
    @apply border-green-400/60 bg-green-500/10 shadow-green-500/20;
  }

  .assessment-option.excellent.selected .option-icon {
    @apply bg-green-500/20 text-green-400;
  }

  .assessment-option.excellent.selected .radio-custom {
    @apply border-green-400 bg-green-500;
  }

  /* Good condition */
  .assessment-option.good.selected .option-content {
     @apply border-green-400/60 bg-green-500/10 shadow-green-500/20;
  }

  .assessment-option.good.selected .option-icon {
    @apply bg-blue-500/20 text-blue-400;
  }

  .assessment-option.good.selected .radio-custom {
    @apply border-blue-400 bg-blue-500;
  }

  /* Fair condition */
  .assessment-option.fair.selected .option-content {
    @apply border-yellow-400/60 bg-yellow-500/10 shadow-yellow-500/20;
  }

  .assessment-option.fair.selected .option-icon {
    @apply bg-yellow-500/20 text-yellow-400;
  }

  .assessment-option.fair.selected .radio-custom {
    @apply border-yellow-400 bg-yellow-500;
  }

  /* Poor condition */
  .assessment-option.poor.selected .option-content {
    @apply border-red-400/60 bg-red-500/10 shadow-red-500/20;
  }

  .assessment-option.poor.selected .option-icon {
    @apply bg-red-500/20 text-red-400;
  }

  .assessment-option.poor.selected .radio-custom {
    @apply border-red-400 bg-red-500;
  }

  /* Functionality states */
  .assessment-option.fully-functional.selected .option-content {
    @apply border-green-400/60 bg-green-500/10 shadow-green-500/20;
  }

  .assessment-option.fully-functional.selected .option-icon {
    @apply bg-green-500/20 text-green-400;
  }

  .assessment-option.fully-functional.selected .radio-custom {
    @apply border-green-400 bg-green-500;
  }

  .assessment-option.partially-functional.selected .option-content {
    @apply border-yellow-400/60 bg-yellow-500/10 shadow-yellow-500/20;
  }

  .assessment-option.partially-functional.selected .option-icon {
    @apply bg-yellow-500/20 text-yellow-400;
  }

  .assessment-option.partially-functional.selected .radio-custom {
    @apply border-yellow-400 bg-yellow-500;
  }

  .assessment-option.not-functional.selected .option-content {
    @apply border-red-400/60 bg-red-500/10 shadow-red-500/20;
  }

  .assessment-option.not-functional.selected .option-icon {
    @apply bg-red-500/20 text-red-400;
  }

  .assessment-option.not-functional.selected .radio-custom {
    @apply border-red-400 bg-red-500;
  }

  /* Checkbox Styles */
  .checkbox-card {
    @apply space-y-4;
  }

  .checkbox-option {
    @apply block cursor-pointer transition-all duration-300 transform hover:scale-[1.02];
  }

  .checkbox-content {
    @apply flex items-center gap-3 p-3 bg-blue-700/20 rounded-xl border-2 border-blue-600/40 transition-all duration-300;
  }

  .checkbox-icon {
    @apply w-8 h-8 rounded-lg flex items-center justify-center text-lg bg-purple-500/20 text-purple-400 transition-all duration-300;
  }

  .checkbox-text {
    @apply flex-1;
  }

  .checkbox-label {
    @apply block text-white font-semibold text-sm mb-0.5;
  }

  .checkbox-description {
    @apply block text-blue-300 text-base leading-relaxed;
  }

  .checkbox-indicator {
    @apply flex items-center;
  }

  .checkbox-custom {
    @apply w-6 h-6 rounded border-2 border-blue-400 bg-blue-700 transition-all duration-300 flex items-center justify-center;
  }

  .checkbox-custom i {
    @apply text-xs opacity-0 transition-opacity duration-300;
  }

  /* Selected checkbox states */
  .checkbox-option input:checked + .checkbox-content {
    @apply border-purple-400/60 bg-purple-500/10 shadow-purple-500/20;
  }

  .checkbox-option input:checked + .checkbox-content .checkbox-custom {
    @apply border-purple-400 bg-purple-500;
  }

  .checkbox-option input:checked + .checkbox-content .checkbox-custom i {
    @apply opacity-100 text-white;
  }

  /* Damage checkbox special styling */
  .damage-option input:checked + .checkbox-content {
    @apply border-red-400/60 bg-red-500/10 shadow-red-500/20;
  }

  .damage-option input:checked + .checkbox-content .damage-icon {
    @apply bg-red-500/20 text-red-400;
  }

  .damage-option input:checked + .checkbox-content .damage-checkbox {
    @apply border-red-400 bg-red-500;
  }

  .damage-description {
    @apply mt-4 animate-slide-down;
  }

  .damage-textarea {
    @apply w-full px-4 py-3 bg-red-900/20 border-2 border-red-600/30 rounded-xl focus:border-red-400 focus:outline-none text-white placeholder-red-300/60 resize-none transition-all duration-300 focus:shadow-lg focus:shadow-red-500/20;
  }

  /* Notes Section */
  .notes-card {
    @apply bg-blue-700/20 rounded-xl border-2 border-blue-600/40 overflow-hidden;
  }

  .notes-textarea {
    @apply w-full px-4 py-4 bg-transparent border-none focus:outline-none text-white placeholder-blue-300/60 resize-none transition-all duration-300;
  }

  .notes-footer {
    @apply flex items-center gap-3 px-4 py-3 bg-blue-800/40 border-t border-blue-600/30;
  }

  .notes-icon {
    @apply flex-shrink-0;
  }

  .notes-hint {
    @apply text-blue-300 text-base leading-relaxed;
  }

  /* Hover effects */
  .assessment-option:hover .option-content {
    @apply border-blue-500/60 bg-blue-600/20 shadow-lg;
  }

  .checkbox-option:hover .checkbox-content {
    @apply border-blue-500/60 bg-blue-600/20 shadow-lg;
  }

  /* Animation for slide down */
  @keyframes slide-down {
    from {
      opacity: 0;
      transform: translateY(-10px);
      max-height: 0;
    }
    to {
      opacity: 1;
      transform: translateY(0);
      max-height: 200px;
    }
  }

  .animate-slide-down {
    animation: slide-down 0.3s ease-out;
  }

  /* Modern Button Styles */
  .modern-button {
    @apply relative overflow-hidden rounded-2xl transition-all duration-300 transform hover:scale-[1.02] focus:outline-none focus:ring-4 focus:ring-blue-500/30;
    min-height: 80px;
    box-shadow:
      0 8px 32px rgba(0, 0, 0, 0.12),
      0 2px 8px rgba(0, 0, 0, 0.08);
  }

  .modern-button-bg {
    @apply absolute inset-0 transition-all duration-300;
  }

  .modern-button-primary .modern-button-bg {
    @apply bg-gradient-to-br from-blue-600 via-blue-700 to-blue-800;
  }

  .modern-button-success .modern-button-bg {
    @apply bg-gradient-to-br from-green-600 via-green-700 to-green-800;
  }

  .modern-button-content {
    @apply relative z-10 flex items-center gap-4 p-6 h-full;
  }

  .modern-button-icon {
    @apply flex items-center justify-center w-12 h-12 rounded-xl bg-white/20 backdrop-blur-sm border border-white/30 text-white text-xl transition-all duration-300;
  }

  .modern-button-text {
    @apply flex-1 text-left;
  }

  .modern-button-label {
    @apply block text-white font-bold text-lg leading-tight mb-1;
  }

  .modern-button-subtitle {
    @apply block text-white/80 text-sm leading-tight;
  }

  .modern-button-badge {
    @apply flex items-center justify-center w-8 h-8 rounded-full bg-white/20 backdrop-blur-sm border border-white/30 text-white transition-all duration-300;
  }

  .modern-button-badge-success {
    @apply bg-green-500/30 border-green-400/50 text-green-200;
  }

  .modern-button-hover {
    @apply absolute inset-0 bg-gradient-to-r from-white/0 via-white/10 to-white/0 opacity-0 transition-all duration-500 transform -skew-x-12 -translate-x-full;
  }

  /* Hover Effects */
  .modern-button:hover .modern-button-bg {
    @apply brightness-110;
  }

  .modern-button:hover .modern-button-icon {
    @apply bg-white/30 border-white/50 scale-110;
  }

  .modern-button:hover .modern-button-badge {
    @apply bg-white/30 border-white/50 scale-110;
  }

  .modern-button:hover .modern-button-hover {
    @apply opacity-100 translate-x-full;
  }

  /* Disabled State */
  .modern-button-disabled {
    @apply opacity-60 cursor-not-allowed transform-none;
  }

  .modern-button-disabled:hover {
    @apply scale-100;
  }

  .modern-button-disabled .modern-button-bg {
    @apply brightness-75;
  }

  .modern-button-disabled .modern-button-icon {
    @apply bg-white/10 border-white/20;
  }

  .modern-button-disabled:hover .modern-button-icon {
    @apply bg-white/10 border-white/20 scale-100;
  }

  .modern-button-disabled .modern-button-hover {
    @apply opacity-0;
  }

  /* Success State */
  .modern-button-success .modern-button-bg {
    @apply from-green-600 via-green-700 to-green-800;
  }

  .modern-button-success .modern-button-icon {
    @apply bg-green-500/20 border-green-400/30;
  }

  /* Approved State */
  .modern-button-approved .modern-button-bg {
    @apply from-green-600 via-green-700 to-green-800;
  }

  .modern-button-approved .modern-button-icon {
    @apply bg-green-500/20 border-green-400/30;
  }

  /* Active/Focus States */
  .modern-button:active {
    @apply scale-[0.98];
  }

  .modern-button:focus {
    @apply ring-4;
  }

  .modern-button-primary:focus {
    @apply ring-blue-500/30;
  }

  .modern-button-success:focus {
    @apply ring-green-500/30;
  }

  /* Animation for spinner */
  .fa-spin {
    animation: fa-spin 1s infinite linear;
  }

  @keyframes fa-spin {
    0% {
      transform: rotate(0deg);
    }
    100% {
      transform: rotate(360deg);
    }
  }

  /* Responsive adjustments */
  @media (max-width: 640px) {
    .modern-button {
      min-height: 70px;
    }

    .modern-button-content {
      @apply p-4 gap-3;
    }

    .modern-button-icon {
      @apply w-10 h-10 text-lg;
    }

    .modern-button-label {
      @apply text-base;
    }

    .modern-button-subtitle {
      @apply text-xs;
    }
  }
</style>
