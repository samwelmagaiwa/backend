<template>
  <div class="flex flex-col h-screen">
    <AppHeader />
    <div class="flex flex-1 overflow-hidden">
      <ModernSidebar />
      <main
        class="flex-1 p-3 bg-gradient-to-br from-blue-900 via-blue-800 to-teal-900 overflow-y-auto relative"
      >
        <!-- Global Loading Banner -->
        <UnifiedLoadingBanner
          :show="globalLoading"
          :loadingTitle="loadingBannerTitle"
          :loadingSubtitle="loadingBannerSubtitle"
          departmentTitle="ICT EQUIPMENT BOOKING SYSTEM"
          :forceSpin="true"
        />
        <!-- Background Pattern -->
        <div class="absolute inset-0 overflow-hidden">
          <!-- Floating geometric shapes -->
          <div class="absolute inset-0">
            <div
              v-for="i in 10"
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

        <div class="max-w-12xl mx-auto relative z-10">
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
                  class="w-full h-full bg-gradient-to-br from-blue-500/20 to-teal-500/20 rounded-2xl backdrop-blur-sm border-2 border-blue-300/40 flex items-center justify-center shadow-2xl hover:shadow-blue-500/25"
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
                  class="text-3xl font-bold text-white mb-2 tracking-wide drop-shadow-lg animate-fade-in"
                >
                  MUHIMBILI NATIONAL HOSPITAL
                </h1>
                <div class="relative inline-block mb-2">
                  <div
                    class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-2 rounded-full text-lg font-bold shadow-2xl transform hover:scale-105 transition-all duration-300 border-2 border-blue-400/60"
                  >
                    <span class="relative z-10 flex items-center gap-2">
                      <i class="fas fa-calendar-check"></i>
                      DEVICE BOOKING SERVICE
                    </span>
                    <div
                      class="absolute inset-0 bg-gradient-to-r from-blue-700 to-blue-800 rounded-full opacity-0 hover:opacity-100 transition-opacity duration-300"
                    ></div>
                  </div>
                </div>
                <h2
                  class="text-xl font-bold text-blue-100 tracking-wide drop-shadow-md animate-fade-in-delay"
                >
                  ICT EQUIPMENT RESERVATION SYSTEM
                </h2>
              </div>

              <!-- Right Logo -->
              <div
                class="w-16 h-16 ml-4 transform hover:scale-110 transition-transform duration-300"
              >
                <div
                  class="w-full h-full bg-gradient-to-br from-teal-500/20 to-blue-500/20 rounded-2xl backdrop-blur-sm border-2 border-teal-300/40 flex items-center justify-center shadow-2xl hover:shadow-teal-500/25"
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

          <!-- Loading Screen (kept for layout spacing while banner overlays) -->
          <div v-if="isCheckingPendingRequests" class="min-h-64"></div>

          <!-- Main Form -->
          <div
            v-else-if="!hasPendingRequest"
            class="booking-glass-card rounded-b-3xl overflow-hidden animate-slide-up"
          >
            <form @submit.prevent="submitBooking" class="p-3 space-y-3">
              <!-- Booking Information Section -->
              <div
                class="booking-card bg-gradient-to-r from-blue-600/25 to-blue-700/25 border-2 border-blue-400/40 p-3 rounded-2xl backdrop-blur-sm hover:shadow-2xl hover:shadow-blue-500/20 transition-all duration-500 group"
              >
                <div class="flex items-center space-x-3 mb-2">
                  <div
                    class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-blue-300/50"
                  >
                    <i class="fas fa-calendar-alt text-white text-lg"></i>
                  </div>
                  <h3 class="text-2xl font-bold text-white flex items-center">
                    <i class="fas fa-info-circle mr-2 text-blue-300"></i>
                    Booking Information
                  </h3>
                </div>

                <div class="space-y-1.5">
                  <!-- Row 1: Name of Borrower & Booking Date -->
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-1.5">
                    <!-- Name of Borrower -->
                    <div class="group">
                      <label class="block text-xl font-bold text-blue-100 mb-1 flex items-center">
                        <i class="fas fa-user mr-2 text-blue-300"></i>
                        Name of Borrower
                        <span class="text-red-400 ml-1">*</span>
                      </label>
                      <div class="relative">
                        <input
                          v-model="formData.borrowerName"
                          type="text"
                          readonly
                          class="booking-input w-full px-3 py-2 bg-white/15 border-2 border-blue-300/30 rounded-xl focus:border-blue-400 focus:outline-none text-white text-lg placeholder-blue-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-blue-500/20 group-hover:border-blue-400/50 cursor-not-allowed opacity-90"
                          placeholder="Auto-populated from your account"
                          title="This field is automatically filled with your account name"
                          required
                        />
                        <div
                          class="absolute inset-0 rounded-xl bg-gradient-to-r from-blue-500/10 to-blue-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"
                        ></div>
                        <div
                          class="absolute right-3 top-1/2 transform -translate-y-1/2 text-blue-300/50"
                        >
                          <i class="fas fa-lock" title="Auto-populated from your account"></i>
                        </div>
                      </div>
                      <div
                        v-if="errors.borrowerName"
                        class="text-red-400 text-base mt-1 flex items-center"
                      >
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        {{ errors.borrowerName }}
                      </div>
                    </div>

                    <!-- Booking Date -->
                    <div class="group">
                      <label class="block text-xl font-bold text-blue-100 mb-1 flex items-center">
                        <i class="fas fa-calendar mr-2 text-blue-300"></i>
                        Booking Date <span class="text-red-400 ml-1">*</span>
                      </label>
                      <div class="relative">
                        <input
                          v-model="formData.bookingDate"
                          @input="validateBookingDate"
                          @change="validateBookingDate"
                          type="date"
                          class="booking-input w-full px-3 py-2 bg-white/15 border-2 border-blue-300/30 rounded-xl focus:border-blue-400 focus:outline-none text-white text-lg backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-blue-500/20 group-hover:border-blue-400/50"
                          required
                        />
                        <div
                          class="absolute inset-0 rounded-xl bg-gradient-to-r from-blue-500/10 to-blue-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"
                        ></div>
                      </div>
                      <div
                        v-if="errors.bookingDate"
                        class="text-red-400 text-sm mt-1 flex items-center"
                      >
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        {{ errors.bookingDate }}
                      </div>
                    </div>
                  </div>

                  <!-- Row 2: Device Type & Department -->
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-1.5">
                    <!-- Type of Device (checkbox list only) -->
                    <div class="group">
                      <label class="block text-xl font-bold text-blue-100 mb-1 flex items-center">
                        <i class="fas fa-laptop mr-2 text-blue-300"></i>
                        Type of Device Borrowed
                        <span class="text-red-400 ml-1">*</span>
                      </label>

                      <!-- Checkbox list for available devices -->
                      <div
                        class="mt-1 max-h-40 overflow-y-auto bg-blue-900/60 border border-blue-400/60 rounded-xl p-2 space-y-1"
                      >
                        <label
                          v-for="device in availableDevices"
                          :key="`chk-` + device.id"
                          class="flex items-center space-x-2 text-sm text-blue-100"
                        >
                          <input
                            type="checkbox"
                            :value="String(device.id)"
                            v-model="formData.deviceInventoryIds"
                            @change="handleDeviceTypeChange"
                            :disabled="isLoadingDevices"
                            class="h-4 w-4 text-blue-500 rounded border-blue-300 bg-blue-900/60 focus:ring-blue-400"
                          />
                          <span class="flex-1">
                            {{ device.device_name }}
                            <span v-if="device.available_quantity > 0" class="text-green-300">
                              - {{ device.available_quantity }} available
                            </span>
                            <span v-else class="text-orange-300">
                              - Out of Stock (Can still request)
                            </span>
                          </span>
                        </label>
                      </div>

                      <div
                        v-if="errors.deviceType"
                        class="text-red-400 text-sm mt-1 flex items-center"
                      >
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        {{ errors.deviceType }}
                      </div>
                    </div>

                    <!-- Department -->
                    <div class="group">
                      <label class="block text-xl font-bold text-blue-100 mb-1 flex items-center">
                        <i class="fas fa-building mr-2 text-blue-300"></i>
                        Department <span class="text-red-400 ml-1">*</span>
                      </label>
                      <div class="relative">
                        <select
                          v-model="formData.department"
                          readonly
                          disabled
                          class="w-full px-3 py-2 bg-white/15 border-2 border-blue-300/30 rounded-xl focus:border-blue-400 focus:outline-none text-white text-lg backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-blue-500/20 group-hover:border-blue-400/50 cursor-not-allowed opacity-90 appearance-none"
                          required
                        >
                          <option value="" class="bg-blue-800 text-blue-300">
                            {{ formData.department ? '' : 'Auto-populated from your account' }}
                          </option>
                          <option
                            v-for="dept in departments"
                            :key="dept.id"
                            :value="dept.id"
                            class="bg-blue-800 text-white"
                            :selected="formData.department == dept.id"
                          >
                            {{ dept.name }}
                          </option>
                        </select>
                        <div
                          class="absolute inset-0 rounded-xl bg-gradient-to-r from-blue-500/10 to-blue-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"
                        ></div>
                        <div
                          class="absolute right-3 top-1/2 transform -translate-y-1/2 text-blue-300/50"
                        >
                          <i class="fas fa-lock" title="Auto-populated from your account"></i>
                        </div>
                      </div>
                      <div
                        v-if="errors.department"
                        class="text-red-400 text-sm mt-1 flex items-center"
                      >
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        {{ errors.department }}
                      </div>
                    </div>
                  </div>

                  <!-- Device Availability Warning Banner -->
                  <div
                    v-if="showAvailabilityWarning && deviceAvailabilityInfo"
                    class="mt-4 p-4 bg-gradient-to-r from-yellow-500/20 to-orange-500/20 border-2 border-yellow-400/40 rounded-xl backdrop-blur-sm animate-slide-down"
                  >
                    <div class="flex items-start space-x-3">
                      <div
                        class="w-8 h-8 bg-gradient-to-br from-yellow-500 to-orange-600 rounded-lg flex items-center justify-center shadow-lg flex-shrink-0"
                      >
                        <i class="fas fa-exclamation-triangle text-white text-sm"></i>
                      </div>
                      <div class="flex-1">
                        <h4 class="text-base font-bold text-yellow-100 mb-1 flex items-center">
                          <i class="fas fa-info-circle mr-2"></i>
                          Device Availability Notice
                        </h4>
                        <p class="text-base text-yellow-200 leading-relaxed">
                          {{ deviceAvailabilityInfo.message }}
                        </p>
                        <p class="text-sm text-yellow-300/80 mt-2 italic">
                          <i class="fas fa-lightbulb mr-1"></i>
                          You can still submit your request, and it will be processed when the
                          device becomes available.
                        </p>
                      </div>
                    </div>
                  </div>

                  <!-- Custom Device Input (if Others selected) -->
                  <div
                    v-if="showCustomDeviceField"
                    class="group animate-slide-down"
                  >
                    <label class="block text-lg font-bold text-blue-100 mb-1 flex items-center">
                      <i class="fas fa-edit mr-2 text-blue-300"></i>
                      Specify Device <span class="text-red-400 ml-1">*</span>
                    </label>
                    <div class="relative">
                      <input
                        v-model="formData.customDevice"
                        @input="validateCustomDevice"
                        type="text"
                        class="booking-input w-full px-3 py-2 bg-white/15 border-2 border-blue-300/30 rounded-xl focus:border-blue-400 focus:outline-none text-white text-lg placeholder-blue-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-blue-500/20 group-hover:border-blue-400/50"
                        placeholder="Please specify the device you want to borrow"
                        :required="formData.deviceInventoryId === 'others'"
                      />
                      <div
                        class="absolute inset-0 rounded-xl bg-gradient-to-r from-blue-500/10 to-blue-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"
                      ></div>
                    </div>
                    <div
                      v-if="errors.customDevice"
                      class="text-red-400 text-sm mt-1 flex items-center"
                    >
                      <i class="fas fa-exclamation-circle mr-1"></i>
                      {{ errors.customDevice }}
                    </div>
                  </div>

                  <!-- Row 3: Phone Number & Collection Date -->
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-1.5">
                    <!-- Phone Number -->
                    <div class="group">
                      <label class="block text-xl font-bold text-blue-100 mb-1 flex items-center">
                        <i class="fas fa-phone mr-2 text-blue-300"></i>
                        Phone Number <span class="text-red-400 ml-1">*</span>
                      </label>
                      <div class="relative">
                        <input
                          v-model="formData.phoneNumber"
                          type="tel"
                          readonly
                          class="booking-input w-full px-3 py-2 bg-white/15 border-2 border-blue-300/30 rounded-xl focus:border-blue-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-blue-500/20 group-hover:border-blue-400/50 cursor-not-allowed opacity-90"
                          placeholder="Auto-populated from your account"
                          title="This field is automatically filled with your account phone number"
                          required
                        />
                        <div
                          class="absolute inset-0 rounded-xl bg-gradient-to-r from-blue-500/10 to-blue-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"
                        ></div>
                        <div
                          class="absolute right-3 top-1/2 transform -translate-y-1/2 text-blue-300/50"
                        >
                          <i class="fas fa-lock" title="Auto-populated from your account"></i>
                        </div>
                      </div>
                      <div
                        v-if="errors.phoneNumber"
                        class="text-red-400 text-base mt-1 flex items-center"
                      >
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        {{ errors.phoneNumber }}
                      </div>
                    </div>

                    <!-- Date of Collection -->
                    <div class="group">
                      <label class="block text-lg font-bold text-blue-100 mb-1 flex items-center">
                        <i class="fas fa-calendar-plus mr-2 text-blue-300"></i>
                        Date of returning
                        <span class="text-red-400 ml-1">*</span>
                      </label>
                      <div class="relative">
                        <input
                          v-model="formData.collectionDate"
                          @input="validateReturnDate"
                          @change="validateReturnDate"
                          type="date"
                          class="booking-input w-full px-3 py-2 bg-white/15 border-2 border-blue-300/30 rounded-xl focus:border-blue-400 focus:outline-none text-white text-lg backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-blue-500/20 group-hover:border-blue-400/50"
                          required
                        />
                        <div
                          class="absolute inset-0 rounded-xl bg-gradient-to-r from-blue-500/10 to-blue-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"
                        ></div>
                      </div>
                      <div
                        v-if="errors.collectionDate"
                        class="text-red-400 text-base mt-1 flex items-center"
                      >
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        {{ errors.collectionDate }}
                      </div>
                    </div>
                  </div>

                  <!-- Return Time -->
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-1.5">
                    <div class="group">
                      <label class="block text-lg font-bold text-blue-100 mb-1 flex items-center">
                        <i class="fas fa-clock mr-2 text-blue-300"></i>
                        Return Time <span class="text-red-400 ml-1">*</span>
                      </label>
                      <div class="relative">
                        <input
                          v-model="formData.returnTime"
                          type="time"
                          @change="handleReturnTimeChange"
                          @input="validateReturnTime"
                          @click="handleReturnTimeClick"
                          class="booking-input w-full px-3 py-2 bg-white/15 border-2 border-blue-300/30 rounded-xl focus:border-blue-400 focus:outline-none text-white text-lg backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-blue-500/20 group-hover:border-blue-400/50 relative z-10"
                          style="color-scheme: dark"
                          required
                        />
                        <div
                          class="absolute inset-0 rounded-xl bg-gradient-to-r from-blue-500/10 to-blue-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none z-0"
                        ></div>
                        <div
                          class="absolute right-3 top-1/2 transform -translate-y-1/2 text-blue-300/50 pointer-events-none z-5"
                        >
                          <i class="fas fa-clock"></i>
                        </div>
                      </div>
                      <div
                        v-if="errors.returnTime"
                        class="text-red-400 text-base mt-1 flex items-center"
                      >
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        {{ errors.returnTime }}
                      </div>
                    </div>
                  </div>

                  <!-- Combined Row: Digital Signature & Reason for Borrowing -->
                  <div class="grid grid-cols-1 lg:grid-cols-2 gap-1.5">
                    <!-- Digital Signature Section (Left) -->
                    <div class="group">
                      <label class="block text-xl font-bold text-blue-100 mb-1 flex items-center">
                        <i class="fas fa-signature mr-2 text-blue-300"></i>
                        Signature <span class="text-red-400 ml-1">*</span>
                        <span class="ml-2 text-base text-blue-300/70 font-normal">Digital</span>
                      </label>

                      <div class="flex flex-col gap-2 items-start w-full">
                        <!-- Digital Signature Status -->
                        <div
                          class="w-full p-3 border-2 border-blue-300/30 rounded-lg bg-blue-100/10 backdrop-blur-sm"
                        >
                          <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                              <i
                                :class="
                                  hasUserSigned
                                    ? 'fas fa-check-circle text-green-400'
                                    : 'fas fa-exclamation-circle text-yellow-300'
                                "
                              ></i>
                              <span class="text-blue-100">
                                {{
                                  hasUserSigned
                                    ? 'You have signed this document'
                                    : 'You have not signed yet'
                                }}
                              </span>
                            </div>
                            <button
                              type="button"
                              @click="signDocument"
                              :disabled="hasUserSigned || isSubmitting"
                              class="inline-flex items-center px-3 py-1.5 bg-gradient-to-r from-green-500 to-green-600 text-white text-sm font-medium rounded-lg hover:from-blue-600 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg hover:shadow-xl transform hover:scale-105 border border-blue-400/30"
                            >
                              <i
                                :class="
                                  hasUserSigned ? 'fas fa-check mr-1' : 'fas fa-pen-fancy mr-1'
                                "
                              ></i>
                              {{ hasUserSigned ? 'Signed' : 'Sign Document' }}
                            </button>
                          </div>
                          <div v-if="signatures.length" class="mt-2 text-blue-200 text-sm">
                            <span class="opacity-75">Signatures:</span>
                            <ul class="list-disc ml-5 mt-1">
                              <li v-for="s in signatures" :key="s.id">
                                {{ s.user_name }} — {{ s.signed_at }}
                              </li>
                            </ul>
                          </div>
                        </div>

                        <div
                          v-if="errors.digital_signature"
                          class="text-red-500 text-base mt-1 flex items-center"
                        >
                          <i class="fas fa-exclamation-circle mr-1"></i>
                          {{ errors.digital_signature }}
                        </div>

                      </div>
                    </div>

                    <!-- Reason for Borrowing Section (Right) -->
                    <div class="group">
                      <label class="block text-xl font-bold text-blue-100 mb-1 flex items-center">
                        <i class="fas fa-comment-alt mr-2 text-blue-300"></i>
                        Reason for Borrowing
                        <span class="text-red-400 ml-1">*</span>
                      </label>
                      <div class="relative">
                        <textarea
                          v-model="formData.reason"
                          @input="handleReasonInput"
                          class="booking-input w-full px-3 py-2 bg-white/15 border-2 border-blue-300/30 rounded-xl focus:border-blue-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-blue-500/20 group-hover:border-blue-400/50 resize-none"
                          rows="2"
                          placeholder="Explain reason for borrowing... (min 10 chars)"
                          maxlength="1000"
                          required
                        ></textarea>
                        <div
                          class="absolute inset-0 rounded-xl bg-gradient-to-r from-blue-500/10 to-blue-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"
                        ></div>
                      </div>
                      <div v-if="errors.reason" class="text-red-400 text-sm mt-1 flex items-center">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        {{ errors.reason }}
                      </div>
                      <div class="flex justify-between items-center mt-0.5">
                        <p class="text-base text-blue-200/60 italic">Min 10 chars</p>
                        <p
                          class="text-base"
                          :class="
                            formData.reason.length >= 10 ? 'text-green-400' : 'text-yellow-400'
                          "
                        >
                          {{ formData.reason.length }}/1000
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Submit Section -->
              <div class="border-t border-blue-300/20 pt-3">
                <div class="flex justify-between items-center">
                  <button
                    type="button"
                    @click="goBack"
                    class="px-4 py-2 bg-gray-600/80 text-white rounded-xl hover:bg-gray-700/80 transition-all duration-300 font-semibold flex items-center shadow-lg hover:shadow-xl transform hover:scale-105 backdrop-blur-sm border border-gray-500/50"
                  >
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Dashboard
                  </button>

                  <button
                    type="submit"
                    :disabled="isSubmitting"
                    class="px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all duration-300 font-semibold flex items-center shadow-lg hover:shadow-xl transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none border border-blue-400/50"
                  >
                    <i v-if="!isSubmitting" class="fas fa-paper-plane mr-2"></i>
                    <i v-else class="fas fa-spinner fa-spin mr-2"></i>
                    {{ isSubmitting ? 'Submitting Booking...' : 'Submit Booking Request' }}
                  </button>
                </div>
              </div>
            </form>

            <!-- Footer -->
            <AppFooter />
          </div>
        </div>
      </main>
    </div>

    <!-- Enhanced Success Modal -->
    <div
      v-if="showSuccessModal"
      class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4 animate-fade-in"
    >
      <div
        class="bg-gradient-to-br from-white via-blue-50/50 to-blue-100/30 rounded-3xl shadow-2xl max-w-lg w-full transform transition-all duration-500 scale-100 animate-modal-bounce border-2 border-blue-200/50 backdrop-blur-lg"
      >
        <!-- Success Header with Animation -->
        <div class="relative overflow-hidden rounded-t-3xl">
          <!-- Animated Background Pattern -->
          <div
            class="absolute inset-0 bg-gradient-to-r from-blue-500/10 via-blue-600/5 to-blue-500/10 animate-pulse"
          ></div>
          <div
            class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-blue-400/20 to-transparent rounded-full blur-2xl animate-float"
          ></div>
          <div
            class="absolute bottom-0 left-0 w-24 h-24 bg-gradient-to-tr from-blue-300/15 to-transparent rounded-full blur-xl animate-float"
            style="animation-delay: 1s"
          ></div>

          <div class="relative z-10 p-8 text-center">
            <!-- Animated Success Icon -->
            <div class="relative mx-auto mb-6">
              <div
                class="w-20 h-20 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center mx-auto shadow-2xl animate-bounce-gentle border-4 border-white/50"
              >
                <i class="fas fa-check text-white text-3xl animate-check-mark"></i>
              </div>
              <!-- Success Ring Animation -->
              <div
                class="absolute inset-0 w-20 h-20 border-4 border-blue-400/30 rounded-full animate-ping"
              ></div>
              <div
                class="absolute inset-0 w-20 h-20 border-2 border-blue-300/50 rounded-full animate-pulse"
              ></div>
            </div>

            <!-- Success Title -->
            <h3
              class="text-3xl font-bold bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent mb-3 animate-slide-up"
            >
              🎉 Booking Submitted Successfully!
            </h3>

            <!-- Success Message -->
            <p
              class="text-lg text-gray-600 mb-6 leading-relaxed animate-slide-up"
              style="animation-delay: 0.2s"
            >
              Your device booking request has been submitted and is now under review by our ICT
              team.
            </p>
          </div>
        </div>

        <!-- Booking Details Card -->
        <div class="px-8 pb-6">
          <div
            class="bg-gradient-to-r from-blue-50 to-blue-100/50 rounded-2xl p-6 mb-6 border border-blue-200/50 shadow-inner animate-slide-up"
            style="animation-delay: 0.4s"
          >
            <!-- Details Header -->
            <div class="flex items-center justify-center mb-4">
              <div
                class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center mr-3 shadow-lg"
              >
                <i class="fas fa-clipboard-list text-white text-sm"></i>
              </div>
              <h4 class="text-xl font-bold text-blue-800">📋 Booking Details</h4>
            </div>

            <!-- Details Grid -->
            <div class="space-y-4">
              <!-- Device Info -->
              <div
                class="flex items-center p-3 bg-white/60 rounded-xl border border-blue-200/30 hover:bg-white/80 transition-all duration-300 group"
              >
                <div
                  class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center mr-4 shadow-md group-hover:scale-110 transition-transform duration-300"
                >
                  <i class="fas fa-laptop text-white text-sm"></i>
                </div>
                <div class="flex-1">
                  <p class="text-sm font-medium text-blue-600 uppercase tracking-wide">Device</p>
                  <p class="text-base font-bold text-gray-800">{{ getDeviceDisplayName() }}</p>
                </div>
              </div>

              <!-- Borrower Info -->
              <div
                class="flex items-center p-3 bg-white/60 rounded-xl border border-blue-200/30 hover:bg-white/80 transition-all duration-300 group"
              >
                <div
                  class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center mr-4 shadow-md group-hover:scale-110 transition-transform duration-300"
                >
                  <i class="fas fa-user text-white text-sm"></i>
                </div>
                <div class="flex-1">
                  <p class="text-sm font-medium text-blue-600 uppercase tracking-wide">Borrower</p>
                  <p class="text-base font-bold text-gray-800">{{ formData.borrowerName }}</p>
                </div>
              </div>

              <!-- Return Date Info -->
              <div
                class="flex items-center p-3 bg-white/60 rounded-xl border border-blue-200/30 hover:bg-white/80 transition-all duration-300 group"
              >
                <div
                  class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center mr-4 shadow-md group-hover:scale-110 transition-transform duration-300"
                >
                  <i class="fas fa-calendar-check text-white text-sm"></i>
                </div>
                <div class="flex-1">
                  <p class="text-sm font-medium text-blue-600 uppercase tracking-wide">
                    Return Date
                  </p>
                  <p class="text-base font-bold text-gray-800">
                    {{ formatDisplayDate(formData.collectionDate) }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Status Badge -->
            <div class="mt-4 text-center">
              <span
                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-yellow-400 to-orange-500 text-white text-base font-bold rounded-full shadow-lg animate-pulse"
              >
                <i class="fas fa-clock mr-2"></i>
                PENDING REVIEW
              </span>
            </div>
          </div>

          <!-- Action Button -->
          <button
            @click="closeSuccessModal"
            class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white py-4 px-6 rounded-2xl font-bold text-xl shadow-xl hover:shadow-2xl transform hover:scale-105 transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-blue-300/50 animate-slide-up border-2 border-blue-500/30"
            style="animation-delay: 0.6s"
          >
            <i class="fas fa-home mr-3 text-lg"></i>
            Return to Dashboard
          </button>
        </div>

        <!-- Decorative Elements -->
        <div class="absolute top-4 right-4 text-blue-300/30 animate-spin-slow">
          <i class="fas fa-cog text-2xl"></i>
        </div>
        <div
          class="absolute bottom-4 left-4 text-green-300/30 animate-bounce"
          style="animation-delay: 2s"
        >
          <i class="fas fa-check-circle text-xl"></i>
        </div>
      </div>
    </div>

    <!-- Pending Request Modal -->
    <div
      v-if="showPendingRequestModal"
      class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4 animate-fade-in"
    >
      <div
        class="bg-gradient-to-br from-white via-orange-50/50 to-orange-100/30 rounded-3xl shadow-2xl max-w-lg w-full transform transition-all duration-500 scale-100 animate-modal-bounce border-2 border-orange-200/50 backdrop-blur-lg"
      >
        <!-- Pending Header with Animation -->
        <div class="relative overflow-hidden rounded-t-3xl">
          <!-- Animated Background Pattern -->
          <div
            class="absolute inset-0 bg-gradient-to-r from-orange-500/10 via-yellow-600/5 to-orange-500/10 animate-pulse"
          ></div>
          <div
            class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-orange-400/20 to-transparent rounded-full blur-2xl animate-float"
          ></div>
          <div
            class="absolute bottom-0 left-0 w-24 h-24 bg-gradient-to-tr from-yellow-300/15 to-transparent rounded-full blur-xl animate-float"
            style="animation-delay: 1s"
          ></div>

          <div class="relative z-10 p-8 text-center">
            <!-- Animated Warning Icon -->
            <div class="relative mx-auto mb-6">
              <div
                class="w-20 h-20 bg-gradient-to-br from-orange-400 to-orange-600 rounded-full flex items-center justify-center mx-auto shadow-2xl animate-bounce-gentle border-4 border-white/50"
              >
                <i class="fas fa-exclamation-triangle text-white text-3xl animate-check-mark"></i>
              </div>
              <!-- Warning Ring Animation -->
              <div
                class="absolute inset-0 w-20 h-20 border-4 border-orange-400/30 rounded-full animate-ping"
              ></div>
              <div
                class="absolute inset-0 w-20 h-20 border-2 border-orange-300/50 rounded-full animate-pulse"
              ></div>
            </div>

            <!-- Warning Title -->
            <h3
              class="text-3xl font-bold bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent mb-3 animate-slide-up"
            >
              ⚠️ Pending Request Found
            </h3>

            <!-- Warning Message -->
            <p
              class="text-lg text-gray-600 mb-6 leading-relaxed animate-slide-up"
              style="animation-delay: 0.2s"
            >
              You already have a pending booking request. Please wait for it to be processed before
              submitting a new request.
            </p>
          </div>
        </div>

        <!-- Pending Request Details Card -->
        <div class="px-8 pb-6" v-if="pendingRequestInfo">
          <div
            class="bg-gradient-to-r from-orange-50 to-orange-100/50 rounded-2xl p-6 mb-6 border border-orange-200/50 shadow-inner animate-slide-up"
            style="animation-delay: 0.4s"
          >
            <!-- Details Header -->
            <div class="flex items-center justify-center mb-4">
              <div
                class="w-8 h-8 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center mr-3 shadow-lg"
              >
                <i class="fas fa-clipboard-list text-white text-sm"></i>
              </div>
              <h4 class="text-xl font-bold text-orange-800">📋 Your Pending Request</h4>
            </div>

            <!-- Details Grid -->
            <div class="space-y-4">
              <!-- Device Info -->
              <div
                class="flex items-center p-3 bg-white/60 rounded-xl border border-orange-200/30 hover:bg-white/80 transition-all duration-300 group"
              >
                <div
                  class="w-10 h-10 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center mr-4 shadow-md group-hover:scale-110 transition-transform duration-300"
                >
                  <i class="fas fa-laptop text-white text-sm"></i>
                </div>
                <div class="flex-1">
                  <p class="text-sm font-medium text-orange-600 uppercase tracking-wide">Device</p>
                  <p class="text-base font-bold text-gray-800">
                    {{ pendingRequestInfo.device_name }}
                  </p>
                </div>
              </div>

              <!-- Booking Date Info -->
              <div
                class="flex items-center p-3 bg-white/60 rounded-xl border border-orange-200/30 hover:bg-white/80 transition-all duration-300 group"
              >
                <div
                  class="w-10 h-10 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center mr-4 shadow-md group-hover:scale-110 transition-transform duration-300"
                >
                  <i class="fas fa-calendar text-white text-sm"></i>
                </div>
                <div class="flex-1">
                  <p class="text-sm font-medium text-orange-600 uppercase tracking-wide">
                    Booking Date
                  </p>
                  <p class="text-base font-bold text-gray-800">
                    {{ formatDisplayDate(pendingRequestInfo.booking_date) }}
                  </p>
                </div>
              </div>

              <!-- Status Info -->
              <div
                class="flex items-center p-3 bg-white/60 rounded-xl border border-orange-200/30 hover:bg-white/80 transition-all duration-300 group"
              >
                <div
                  class="w-10 h-10 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center mr-4 shadow-md group-hover:scale-110 transition-transform duration-300"
                >
                  <i class="fas fa-clock text-white text-sm"></i>
                </div>
                <div class="flex-1">
                  <p class="text-sm font-medium text-orange-600 uppercase tracking-wide">Status</p>
                  <p class="text-base font-bold text-gray-800 capitalize">
                    {{ pendingRequestInfo.status }} / {{ pendingRequestInfo.ict_approve }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Status Badge -->
            <div class="mt-4 text-center">
              <span
                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-orange-400 to-yellow-500 text-white text-base font-bold rounded-full shadow-lg animate-pulse"
              >
                <i class="fas fa-hourglass-half mr-2"></i>
                AWAITING APPROVAL
              </span>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex gap-3">
            <button
              @click="viewPendingRequest"
              class="flex-1 bg-gradient-to-r from-orange-600 to-orange-700 hover:from-orange-700 hover:to-orange-800 text-white py-4 px-6 rounded-2xl font-bold text-xl shadow-xl hover:shadow-2xl transform hover:scale-105 transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-orange-300/50 animate-slide-up border-2 border-orange-500/30"
              style="animation-delay: 0.6s"
            >
              <i class="fas fa-eye mr-3 text-lg"></i>
              View Request
            </button>

            <button
              @click="closePendingRequestModal"
              class="flex-1 bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white py-4 px-6 rounded-2xl font-bold text-xl shadow-xl hover:shadow-2xl transform hover:scale-105 transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-gray-300/50 animate-slide-up border-2 border-gray-500/30"
              style="animation-delay: 0.8s"
            >
              <i class="fas fa-arrow-left mr-3 text-lg"></i>
              Dashboard
            </button>
          </div>
        </div>

        <!-- Decorative Elements -->
        <div class="absolute top-4 right-4 text-orange-300/30 animate-spin-slow">
          <i class="fas fa-cog text-2xl"></i>
        </div>
        <div
          class="absolute bottom-4 left-4 text-yellow-300/30 animate-bounce"
          style="animation-delay: 2s"
        >
          <i class="fas fa-exclamation-circle text-xl"></i>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import ModernSidebar from '@/components/ModernSidebar.vue'
  import AppFooter from '@/components/footer.vue'
  import AppHeader from '@/components/AppHeader.vue'
  import UnifiedLoadingBanner from '@/components/common/UnifiedLoadingBanner.vue'
  import bookingService from '@/services/bookingService'
  import deviceInventoryService from '@/services/deviceInventoryService'
  import ictApprovalService from '@/services/ictApprovalService'
  import signatureService from '@/services/signatureService'
  import { useAuthStore } from '@/stores/auth'
  import apiClient from '@/utils/apiClient'

  export default {
    name: 'BookingService',
    components: {
      ModernSidebar,
      AppFooter,
      AppHeader,
      UnifiedLoadingBanner
    },
    setup() {
      // Get auth store for user information
      const authStore = useAuthStore()

      return {
        authStore
      }
    },
    data() {
      return {
        formData: {
          bookingDate: '',
          borrowerName: '',
          deviceInventoryId: '', // Primary device (for backward compatibility)
          deviceInventoryIds: [], // NEW: allow selecting multiple devices
          deviceType: '', // Keep for backward compatibility
          customDevice: '',
          reason: '',
          department: '',
          collectionDate: '', // Fixed: matches form template field name
          returnTime: '',
          phoneNumber: '',
          conditionBefore: '',
          conditionBeforeIssues: '',
          conditionReturned: '',
          conditionReturnedIssues: '',
          status: ''
        },
        selectedDeviceInfo: null, // Store primary device info for display
        selectedDevicesCount: 0, // NEW: number of selected inventory devices
        showCustomDeviceField: false, // NEW: whether to show custom device input
        errors: {},
        isSubmitting: false,
        showSuccessModal: false,
        departments: [],
        availableDevices: [],
        isLoadingDevices: false,
        deviceAvailabilityInfo: null, // Store availability info for out-of-stock devices
        showAvailabilityWarning: false,
        hasPendingRequest: false, // Track if user has pending request
        pendingRequestInfo: null, // Store pending request details
        showPendingRequestModal: false, // Show modal for pending request
        isCheckingPendingRequests: true, // Show loading while checking for pending requests
        // Digital signature state
        signatures: [],
        hasUserSigned: false
      }
    },
    async mounted() {
      try {
        // Set up automatic fallback timeout (10 seconds)
        const fallbackTimeout = setTimeout(async () => {
          if (this.isCheckingPendingRequests) {
            console.warn('⚠️ Pending requests check taking too long, showing form')
            this.isCheckingPendingRequests = false
            this.hasPendingRequest = false
            await this.initializeForm()
          }
        }, 10000)

        // Check if user has pending requests first
        await this.checkPendingRequests()

        // Clear the fallback timeout since check completed
        clearTimeout(fallbackTimeout)

        // Only proceed if no pending request found
        if (!this.hasPendingRequest) {
          await this.initializeForm()
        }
      } catch (error) {
        console.error('Error during component initialization:', error)
        // If pending check fails, still initialize the form
        this.isCheckingPendingRequests = false
        this.hasPendingRequest = false
        await this.initializeForm()
      }
    },
    computed: {
      globalLoading() {
        return this.isCheckingPendingRequests || this.isSubmitting || this.isLoadingDevices
      },
      loadingBannerTitle() {
        if (this.isCheckingPendingRequests) return 'Checking Your Requests'
        if (this.isLoadingDevices) return 'Loading Devices'
        if (this.isSubmitting) return 'Submitting Booking Request'
        return 'Loading'
      },
      loadingBannerSubtitle() {
        if (this.isCheckingPendingRequests) return 'Verifying pending booking requests...'
        if (this.isLoadingDevices) return 'Fetching available devices...'
        if (this.isSubmitting) return 'Please wait while we submit your booking...'
        return 'Please wait...'
      }
    },

    methods: {
      /**
       * Initialize form data and load required dependencies
       */
      async initializeForm() {
        try {
          // Load departments and available devices when component is mounted
          await this.loadDepartments()
          await this.loadAvailableDevices()

          // Auto-populate borrower name, phone number, and department from authenticated user
          this.populateBorrowerName()
          this.populatePhoneNumber()
          this.populateDepartment()

          // Debug: Check if time input is working
          console.log('BookingService mounted, returnTime field:', this.formData.returnTime)

          // Set a default time if none is set (optional)
          if (!this.formData.returnTime) {
            // Set default return time to 5 PM
            this.formData.returnTime = '17:00'
          }

          // Initialize real-time validation
          this.initializeValidation()

          // Load digital signatures for this booking document context
          await this.loadSignatures()
        } catch (error) {
          console.error('Error initializing form:', error)
        }
      },

      /**
       * Check if user has any pending booking requests
       */
      async checkPendingRequests() {
        try {
          this.isCheckingPendingRequests = true

          // Add timeout and retry logic
          const controller = new AbortController()
          const timeoutId = setTimeout(() => controller.abort(), 8000) // 8 second timeout

          const response = await apiClient.get('/booking-service/check-pending-requests', {
            signal: controller.signal
          })

          clearTimeout(timeoutId)

          if (response.data.success) {
            // Backend may flag has_pending_request due to auxiliary flags (e.g., SMS pending)
            // Treat only truly pending workflow states as blocking
            const pr = response.data.pending_request || {}
            const isWorkflowComplete = !!(
              pr.device_received_at ||
              /returned|completed|closed/i.test(pr.return_status || '') ||
              /returned|completed|closed/i.test(pr.status || '')
            )

            // Consider it pending only if backend says so AND not completed
            this.hasPendingRequest = response.data.has_pending_request && !isWorkflowComplete

            if (this.hasPendingRequest && response.data.pending_request) {
              // Double-check server state to avoid stale flags
              try {
                const pendingId =
                  response.data.pending_request.id || response.data.pending_request.request_id
                if (pendingId) {
                  const verify = await apiClient.get(`/booking-service/bookings/${pendingId}`)
                  const rd = verify.data?.data || verify.data || {}
                  const done = !!(
                    rd.device_received_at ||
                    /returned|completed|closed/i.test(rd.return_status || '') ||
                    /returned|completed|closed/i.test(rd.status || '') ||
                    (typeof rd.current_step === 'number' && rd.current_step >= 3)
                  )
                  if (done) {
                    console.log(
                      '🧹 Clearing stale pending flag (booking is already returned):',
                      pendingId
                    )
                    this.hasPendingRequest = false
                    this.showPendingRequestModal = false
                  }
                }
              } catch (e) {
                if (e?.response?.status === 404) {
                  console.log('🧹 Pending booking not found (404) — clearing stale flag')
                  this.hasPendingRequest = false
                  this.showPendingRequestModal = false
                } else {
                  console.warn(
                    'Pending verification failed (continuing with server flag):',
                    e?.message
                  )
                }
              }

              if (this.hasPendingRequest) {
                this.pendingRequestInfo = response.data.pending_request
                this.showPendingRequestModal = true
                console.log('🚫 User has pending request:', response.data.pending_request)
              }
            } else {
              // Ignore cases where only SMS status is pending
              console.log('✅ No blocking pending requests found (workflow complete or none)')
              this.hasPendingRequest = false
            }
          } else {
            console.error('Failed to check pending requests:', response.data.message)
            // Allow form to continue even if check fails
            this.hasPendingRequest = false
          }
        } catch (error) {
          console.error('Error checking pending requests:', error)

          // Handle different types of errors
          if (error.name === 'AbortError' || error.code === 'ECONNABORTED') {
            console.warn('⚠️ Pending requests check timed out - allowing form submission')
            // Don't show error to user, just proceed
          } else if (error.response?.status === 401) {
            console.warn('⚠️ Authentication error during pending requests check')
            // Let auth interceptor handle this
            return
          } else {
            console.warn(
              '⚠️ Network error during pending requests check - allowing form submission'
            )
          }

          // Always allow form to continue if check fails
          this.hasPendingRequest = false
        } finally {
          this.isCheckingPendingRequests = false
        }
      },

      /**
       * Handle user decision to view their pending request
       */
      viewPendingRequest() {
        if (this.pendingRequestInfo && this.pendingRequestInfo.request_url) {
          this.$router.push(this.pendingRequestInfo.request_url)
        } else {
          // Fallback to request status page
          this.$router.push('/request-status')
        }
      },

      /**
       * Close pending request modal and go back to dashboard
       */
      closePendingRequestModal() {
        this.showPendingRequestModal = false
        this.$router.push('/user-dashboard')
      },

      normalizePhoneNumber(input) {
        if (!input) return ''
        let v = String(input).trim().replace(/\s|-/g, '')
        if (v.startsWith('+255')) return v
        if (v.startsWith('255')) return '+' + v
        if (v.startsWith('0')) return '+255' + v.slice(1)
        return v
      },

      async checkDeviceAvailability(deviceInventoryId) {
        try {
          // Use apiClient instead of fetch to ensure correct base URL
          const response = await apiClient.get(
            `/booking-service/devices/${deviceInventoryId}/availability`
          )

          if (response.data.success) {
            this.deviceAvailabilityInfo = response.data.data
            this.showAvailabilityWarning =
              !response.data.data.available && response.data.data.can_request

            console.log('Device availability checked:', response.data.data)
          } else {
            console.error('Failed to check device availability:', response.data.message)
          }
        } catch (error) {
          console.error('Error checking device availability:', error)
          // Don't show availability warning if there's an error
          this.deviceAvailabilityInfo = null
          this.showAvailabilityWarning = false
        }
      },

      populateBorrowerName() {
        // Auto-populate borrower name from authenticated user
        try {
          if (this.authStore.isAuthenticated && this.authStore.user) {
            const userName = this.authStore.user.name || this.authStore.userName
            if (userName && !this.formData.borrowerName) {
              this.formData.borrowerName = userName
              console.log('✅ Auto-populated borrower name:', userName)
            } else if (!userName) {
              console.warn('⚠️ User name not available in auth store')
              // Try to get from localStorage as fallback
              const storedUserData = localStorage.getItem('user_data')
              if (storedUserData) {
                const userData = JSON.parse(storedUserData)
                if (userData.name) {
                  this.formData.borrowerName = userData.name
                  console.log('✅ Auto-populated borrower name from localStorage:', userData.name)
                }
              }
            }
          } else {
            console.warn('⚠️ User not authenticated or user data not available')
            // If not authenticated, redirect to login
            this.$router.push('/login')
          }
        } catch (error) {
          console.error('❌ Error populating borrower name:', error)
        }
      },

      populatePhoneNumber() {
        // Auto-populate phone number from authenticated user
        try {
          if (this.authStore.isAuthenticated && this.authStore.user) {
            const userPhone = this.authStore.user.phone || this.authStore.user.phone_number
            if (userPhone && !this.formData.phoneNumber) {
              this.formData.phoneNumber = this.normalizePhoneNumber(userPhone)
            } else if (!userPhone) {
              // Try to get from localStorage as fallback
              const storedUserData = localStorage.getItem('user_data')
              if (storedUserData) {
                const userData = JSON.parse(storedUserData)
                const phoneFromStorage = userData.phone || userData.phone_number
                if (phoneFromStorage) {
                  this.formData.phoneNumber = this.normalizePhoneNumber(phoneFromStorage)
                }
              }
            }
          }
        } catch (error) {
          console.error('❌ Error populating phone number:', error)
        }
      },

      /**
       * Auto-populate department dropdown based on user's department_id from authStore
       * This method maps the user's department_id to the department list and sets the form field
       */
      populateDepartment() {
        // Auto-populate department from authenticated user
        try {
          if (this.authStore.isAuthenticated && this.authStore.user) {
            // Check for department_id in user data
            const userDepartmentId = this.authStore.user.department_id
            const userDepartment = this.authStore.user.department

            console.log('🏢 Department population data:', {
              userDepartmentId,
              userDepartment,
              currentFormDepartment: this.formData.department,
              availableDepartments: this.departments.length
            })

            if (userDepartmentId && !this.formData.department) {
              // Verify the department ID exists in our departments list
              const matchingDept = this.departments.find((dept) => dept.id == userDepartmentId)
              if (matchingDept) {
                this.formData.department = parseInt(userDepartmentId)
                console.log(
                  '✅ Auto-populated department ID:',
                  userDepartmentId,
                  '-',
                  matchingDept.name
                )
              } else {
                console.warn(
                  '⚠️ User department ID not found in available departments:',
                  userDepartmentId
                )
              }
            } else if (userDepartment && userDepartment.id && !this.formData.department) {
              // If department is an object with id
              const matchingDept = this.departments.find((dept) => dept.id == userDepartment.id)
              if (matchingDept) {
                this.formData.department = parseInt(userDepartment.id)
                console.log(
                  '✅ Auto-populated department from object:',
                  userDepartment.id,
                  '-',
                  userDepartment.name || matchingDept.name
                )
              } else {
                console.warn(
                  '⚠️ User department object ID not found in available departments:',
                  userDepartment.id
                )
              }
            } else if (!userDepartmentId && !userDepartment) {
              // Try to get from localStorage as fallback
              const storedUserData = localStorage.getItem('user_data')
              if (storedUserData) {
                try {
                  const userData = JSON.parse(storedUserData)
                  const deptFromStorage = userData.department_id || userData.department?.id
                  if (deptFromStorage) {
                    const matchingDept = this.departments.find((dept) => dept.id == deptFromStorage)
                    if (matchingDept) {
                      this.formData.department = parseInt(deptFromStorage)
                      console.log(
                        '✅ Auto-populated department from localStorage:',
                        deptFromStorage,
                        '-',
                        matchingDept.name
                      )
                    } else {
                      console.warn(
                        '⚠️ Stored department ID not found in available departments:',
                        deptFromStorage
                      )
                    }
                  }
                } catch (parseError) {
                  console.error('❌ Error parsing stored user data:', parseError)
                }
              }
            }

            // If department is populated, clear any validation errors and run validation
            if (this.formData.department) {
              this.errors.department = ''
              this.validateDepartment()
            } else {
              console.warn(
                '⚠️ Could not auto-populate department - user may need to select manually'
              )
            }
          } else {
            console.warn(
              '⚠️ User not authenticated or user data not available for department population'
            )
          }
        } catch (error) {
          console.error('❌ Error populating department:', error)
        }
      },

      initializeValidation() {
        // Run initial validation to show red messages for empty required fields
        this.validateBookingDate()
        this.validateDeviceType()
        this.validateDepartment()
        this.validateReturnDate()
        this.validateReturnTime()
        this.validateReason()
        // Phone number validation removed since it's auto-populated
      },

      mapDeviceToType(device) {
        // Map device from inventory to valid ENUM values
        const validDeviceTypes = [
          'projector',
          'tv_remote',
          'hdmi_cable',
          'monitor',
          'cpu',
          'keyboard',
          'pc',
          'others'
        ]

        // First, try to use device_code if it's a valid ENUM value
        if (device.device_code && validDeviceTypes.includes(device.device_code.toLowerCase())) {
          console.log('Using device_code for mapping:', device.device_code)
          return device.device_code.toLowerCase()
        }

        // Special mapping for device_code that doesn't match enum exactly
        if (device.device_code) {
          const deviceCodeLower = device.device_code.toLowerCase()
          if (deviceCodeLower === 'hdmi') {
            console.log('Mapping HDMI device_code to hdmi_cable')
            return 'hdmi_cable'
          }
        }

        // Fallback to device_name mapping
        return this.mapDeviceNameToType(device.device_name)
      },

      mapDeviceNameToType(deviceName) {
        // Map device names from inventory to valid ENUM values
        const deviceNameLower = deviceName.toLowerCase()

        // Direct matches
        if (deviceNameLower.includes('projector')) return 'projector'
        if (deviceNameLower.includes('monitor')) return 'monitor'
        if (deviceNameLower.includes('cpu')) return 'cpu'
        if (deviceNameLower.includes('keyboard')) return 'keyboard'
        if (deviceNameLower.includes('pc') || deviceNameLower.includes('computer')) return 'pc'
        if (deviceNameLower.includes('tv') && deviceNameLower.includes('remote')) return 'tv_remote'
        if (deviceNameLower.includes('hdmi')) return 'hdmi_cable'

        // Partial matches for common device types
        if (deviceNameLower.includes('laptop') || deviceNameLower.includes('notebook')) return 'pc'
        if (deviceNameLower.includes('screen') || deviceNameLower.includes('display'))
          return 'monitor'
        if (deviceNameLower.includes('remote') && !deviceNameLower.includes('hdmi'))
          return 'tv_remote'
        if (deviceNameLower.includes('cable') && !deviceNameLower.includes('hdmi'))
          return 'hdmi_cable'

        // Additional matches
        if (deviceNameLower.includes('tv') || deviceNameLower.includes('television'))
          return 'tv_remote'
        if (deviceNameLower.includes('desktop')) return 'pc'
        if (deviceNameLower.includes('project')) return 'projector' // for 'projection' etc

        // For inventory devices that don't match any pattern, use 'others'
        // The backend validation will handle this case properly when device_inventory_id is provided
        console.warn(
          'Device name not recognized, defaulting to "others" (device_inventory_id will be used by backend):',
          {
            deviceName: deviceName,
            deviceNameLower: deviceNameLower,
            suggestedFix: 'Consider updating the device mapping logic or device_code in inventory'
          }
        )
        return 'others'
      },

      async loadDepartments() {
        try {
          const response = await bookingService.getDepartments()
          if (response.success && response.data && response.data.data) {
            this.departments = response.data.data
          } else {
            console.error('Failed to load departments:', response)
            // Fallback to static list if API fails
            this.departments = [
              { id: 1, name: 'Administration' },
              { id: 2, name: 'ICT Department' },
              { id: 3, name: 'Finance' },
              { id: 4, name: 'Human Resources' },
              { id: 5, name: 'Nursing' },
              { id: 6, name: 'Laboratory' },
              { id: 7, name: 'Radiology' }
            ]
          }
        } catch (error) {
          console.error('Error loading departments:', error)
          // Fallback to static list if API fails
          this.departments = [
            { id: 1, name: 'Administration' },
            { id: 2, name: 'ICT Department' },
            { id: 3, name: 'Finance' },
            { id: 4, name: 'Human Resources' },
            { id: 5, name: 'Nursing' },
            { id: 6, name: 'Laboratory' },
            { id: 7, name: 'Radiology' }
          ]
        }
      },
      async loadAvailableDevices() {
        this.isLoadingDevices = true
        try {
          console.time('Loading available devices')
          const response = await deviceInventoryService.getAvailableDevices()
          console.timeEnd('Loading available devices')

          if (response.success && response.data && response.data.data) {
            this.availableDevices = response.data.data
            console.log('✅ Loaded', this.availableDevices.length, 'available devices')
          } else {
            console.error('Failed to load available devices:', response)
            this.availableDevices = []
          }
        } catch (error) {
          console.error('Error loading available devices:', error)
          this.availableDevices = []
        } finally {
          this.isLoadingDevices = false
        }
      },
      handleDeviceTypeChange() {
        // Normalize selection
        const rawSelection = this.formData.deviceInventoryIds || []
        const selection = Array.isArray(rawSelection)
          ? rawSelection
          : [rawSelection].filter(Boolean)

        const inventoryIds = selection.filter((v) => v !== 'others')
        const hasOthers = selection.includes('others')

        this.selectedDevicesCount = inventoryIds.length
        this.showCustomDeviceField = hasOthers

        // Determine primary inventory device (for backward compatibility)
        const primaryId =
          inventoryIds.length > 0 ? inventoryIds[0] : hasOthers ? 'others' : ''
        this.formData.deviceInventoryId = primaryId

        if (primaryId && primaryId !== 'others') {
          this.formData.customDevice = ''

          const selectedDevice = this.availableDevices.find(
            (d) => String(d.id) === String(primaryId)
          )
          if (selectedDevice) {
            this.selectedDeviceInfo = {
              id: selectedDevice.id,
              name: selectedDevice.device_name,
              code: selectedDevice.device_code
            }

            // Use device_code first, then fallback to device_name mapping
            this.formData.deviceType = this.mapDeviceToType(selectedDevice)
          } else {
            this.formData.deviceType = ''
            this.selectedDeviceInfo = null
          }
        } else if (hasOthers) {
          this.formData.deviceType = 'others'
          this.selectedDeviceInfo = null
        } else {
          this.formData.deviceType = ''
          this.selectedDeviceInfo = null
        }

        // Validate + availability for primary device
        this.validateDeviceType()

        if (primaryId && primaryId !== 'others') {
          this.checkDeviceAvailability(primaryId)
        } else {
          this.deviceAvailabilityInfo = null
          this.showAvailabilityWarning = false
        }
      },

      validateDeviceType() {
        const selection = this.formData.deviceInventoryIds || []
        const inventoryIds = Array.isArray(selection)
          ? selection.filter((v) => v !== 'others')
          : this.formData.deviceInventoryId
          ? [this.formData.deviceInventoryId]
          : []
        const hasOthers = Array.isArray(selection) && selection.includes('others')
        const primaryId =
          this.formData.deviceInventoryId ||
          (inventoryIds.length > 0 ? inventoryIds[0] : hasOthers ? 'others' : '')

        if (!primaryId && !hasOthers) {
          this.errors.deviceType = 'Device type is required'
        } else if (primaryId && primaryId !== 'others' && !this.formData.deviceType) {
          this.errors.deviceType = 'Device type validation error'
        } else {
          // Validate that device_type is a valid ENUM value
          const validDeviceTypes = [
            'projector',
            'tv_remote',
            'hdmi_cable',
            'monitor',
            'cpu',
            'keyboard',
            'pc',
            'others'
          ]
          if (this.formData.deviceType && !validDeviceTypes.includes(this.formData.deviceType)) {
            this.errors.deviceType = 'Invalid device type selected'
          } else {
            // Allow selection of any device (including out-of-stock)
            if (this.formData.deviceInventoryId && this.formData.deviceInventoryId !== 'others') {
              const selectedDevice = this.availableDevices.find(
                (d) => d.id == this.formData.deviceInventoryId
              )
              if (!selectedDevice) {
                this.errors.deviceType = 'Selected device not found in inventory'
              } else {
                // Clear any previous errors - device can be selected even if out of stock
                this.errors.deviceType = ''
              }
            } else {
              this.errors.deviceType = ''
            }
          }
        }
      },

      handleReturnTimeChange(event) {
        console.log('Return time changed:', event.target.value)
        this.formData.returnTime = event.target.value
        // Clear any existing error
        if (this.errors.returnTime) {
          this.errors.returnTime = ''
        }
      },

      handleReturnTimeClick(event) {
        console.log('Return time input clicked')
        // Ensure the input is focused and clickable
        event.target.focus()
      },

      handleReasonInput() {
        // Clear reason error when user starts typing and meets minimum length
        if (this.errors.reason && this.formData.reason.trim().length >= 10) {
          this.errors.reason = ''
        }
        // Also run real-time validation
        this.validateReason()
      },

      // Real-time validation methods
      validateBookingDate() {
        if (!this.formData.bookingDate) {
          this.errors.bookingDate = 'Booking date is required'
        } else {
          const today = new Date().toISOString().split('T')[0]
          if (this.formData.bookingDate < today) {
            this.errors.bookingDate = 'Booking date cannot be in the past'
          } else {
            this.errors.bookingDate = ''
          }
        }
      },

      validateDepartment() {
        if (!this.formData.department) {
          this.errors.department = 'Department is required'
        } else {
          this.errors.department = ''
        }
      },

      validateCustomDevice() {
        if (this.formData.deviceInventoryId === 'others') {
          if (!this.formData.customDevice.trim()) {
            this.errors.customDevice = 'Please specify the device'
          } else {
            this.errors.customDevice = ''
          }
        }
      },

      validatePhoneNumber() {
        // Phone number is auto-populated from user account, just check if it exists
        if (!this.formData.phoneNumber || !this.formData.phoneNumber.trim()) {
          this.errors.phoneNumber =
            'Phone number not available in your account. Please contact admin to update your profile.'
        } else {
          this.errors.phoneNumber = ''
        }
      },

      validateReturnDate() {
        if (!this.formData.collectionDate) {
          this.errors.collectionDate = 'Return date is required'
        } else if (
          this.formData.bookingDate &&
          this.formData.collectionDate <= this.formData.bookingDate
        ) {
          this.errors.collectionDate = 'Return date must be after booking date'
        } else {
          this.errors.collectionDate = ''
        }
      },

      validateReturnTime() {
        if (!this.formData.returnTime) {
          this.errors.returnTime = 'Return time is required'
        } else {
          this.errors.returnTime = ''
        }
      },

      validateReason() {
        if (!this.formData.reason.trim()) {
          this.errors.reason = 'Reason for borrowing is required'
        } else if (this.formData.reason.trim().length < 10) {
          this.errors.reason = 'Reason must be at least 10 characters'
        } else {
          this.errors.reason = ''
        }
      },

      handleConditionBeforeChange() {
        if (this.formData.conditionBefore !== 'something_not_good') {
          this.formData.conditionBeforeIssues = ''
        }
      },

      handleConditionReturnedChange() {
        if (this.formData.conditionReturned !== 'compromised') {
          this.formData.conditionReturnedIssues = ''
        }
      },

      // Digital Signatures
      computeDocumentId() {
        // Synthetic pre-sign token id for booking_service: 910,000,000 + userId
        const uid = this.authStore?.user?.id || 0
        return 910000000 + Number(uid)
      },
      async loadSignatures() {
        try {
          const docId = this.computeDocumentId()
          const res = await signatureService.list(docId)
          this.signatures = Array.isArray(res.data) ? res.data : res
          const uid = this.authStore?.user?.id
          this.hasUserSigned = !!(this.signatures || []).find((s) => s.user_id === uid)
        } catch (e) {
          this.signatures = []
          this.hasUserSigned = false
        }
      },
      async signDocument() {
        try {
          const docId = 'booking_service' // resolveDocumentId will map per current user
          await signatureService.sign(docId)
          await this.loadSignatures()
        } catch (e) {
          alert('Failed to sign document. Please try again.')
        }
      },

      validateForm() {
        this.errors = {}

        // Validate required fields
        if (!this.formData.bookingDate) {
          this.errors.bookingDate = 'Booking date is required'
        }

        if (!this.formData.borrowerName.trim()) {
          this.errors.borrowerName = 'Borrower name is required'
        }

        const selection = this.formData.deviceInventoryIds || []
        const inventoryIds = Array.isArray(selection)
          ? selection.filter((v) => v !== 'others')
          : this.formData.deviceInventoryId
          ? [this.formData.deviceInventoryId]
          : []
        const hasOthers = Array.isArray(selection) && selection.includes('others')

        if (inventoryIds.length === 0 && !hasOthers) {
          this.errors.deviceType = 'Device type is required'
        }

        // Ensure device_type is set when a primary inventory device is selected
        if (
          this.formData.deviceInventoryId &&
          this.formData.deviceInventoryId !== 'others' &&
          !this.formData.deviceType
        ) {
          this.errors.deviceType = 'Device type validation error'
        }

        // Validate that device_type is a valid ENUM value
        const validDeviceTypes = [
          'projector',
          'tv_remote',
          'hdmi_cable',
          'monitor',
          'cpu',
          'keyboard',
          'pc',
          'others'
        ]
        if (this.formData.deviceType && !validDeviceTypes.includes(this.formData.deviceType)) {
          this.errors.deviceType = 'Invalid device type selected'
        }

        if (this.formData.deviceInventoryId === 'others' && !this.formData.customDevice.trim()) {
          this.errors.customDevice = 'Please specify the device'
        }

        // Allow selection of any device (including out-of-stock)
        if (this.formData.deviceInventoryId && this.formData.deviceInventoryId !== 'others') {
          const selectedDevice = this.availableDevices.find(
            (d) => d.id == this.formData.deviceInventoryId
          )
          if (!selectedDevice) {
            this.errors.deviceType = 'Selected device not found in inventory'
          }
          // Note: We don't check can_borrow here anymore - allow out-of-stock devices
        }

        if (!this.formData.reason.trim()) {
          this.errors.reason = 'Reason for borrowing is required'
        } else if (this.formData.reason.trim().length < 10) {
          this.errors.reason = 'Reason must be at least 10 characters'
        }

        if (!this.formData.department) {
          this.errors.department = 'Department is required'
        }

        if (!this.formData.collectionDate) {
          this.errors.collectionDate = 'Collection date is required'
        }

        if (!this.formData.returnTime) {
          this.errors.returnTime = 'Return time is required'
        }

        // Validate phone number (auto-populated from user account)
        this.validatePhoneNumber()

        // Require digital signature (frontend hint; backend enforces too)
        if (!this.hasUserSigned) {
          this.errors.digital_signature = 'Please sign the document before submitting.'
        }

        // Filter out empty error messages for more accurate validation
        const actualErrors = Object.keys(this.errors).filter(
          (key) => this.errors[key] && this.errors[key].trim()
        )
        return actualErrors.length === 0
      },

      async submitBooking() {
        // Set submitting state to true to show loading
        this.isSubmitting = true

        try {
          // Validate form first
          if (!this.validateForm()) {
            // Log validation errors safely
            const errorFields = Object.keys(this.errors)
            console.log('Form validation failed - Error count:', errorFields.length)
            console.log('Error fields:', errorFields)

            // Reset submitting state immediately
            this.isSubmitting = false

            // Scroll to the first error field for better UX
            this.scrollToFirstError()

            // Show a brief alert to notify about validation issues
            const errorCount = errorFields.length
            alert(
              `Please fill in all required fields. ${errorCount} ${errorCount === 1 ? 'field' : 'fields'} need(s) attention.`
            )
            return
          }

          // Double-check for pending requests before submitting
          try {
            await this.checkPendingRequests()
            if (this.hasPendingRequest) {
              alert(
                'You have a pending booking request. Please wait for it to be processed before submitting a new request.'
              )
              this.isSubmitting = false // Reset submitting state when pending request found
              return
            }
          } catch (error) {
            console.warn(
              '⚠️ Could not verify pending requests, proceeding with submission:',
              error.message
            )
            // Continue with submission if pending check fails
          }

          // Prepare JSON payload for API submission
          const payload = {
            booking_date: this.formData.bookingDate,
            borrower_name: this.formData.borrowerName,
            device_type: this.formData.deviceType,
            department: this.formData.department,
            phone_number: this.normalizePhoneNumber(this.formData.phoneNumber),
            return_date: this.formData.collectionDate, // Note: collectionDate is actually return_date
            return_time: this.formData.returnTime,
            reason: this.formData.reason
          }

          // Normalize device selections
          const selection = this.formData.deviceInventoryIds || []
          const inventoryIds = Array.isArray(selection)
            ? selection.filter((v) => v !== 'others')
            : this.formData.deviceInventoryId && this.formData.deviceInventoryId !== 'others'
            ? [this.formData.deviceInventoryId]
            : []
          const hasOthers = Array.isArray(selection) && selection.includes('others')

          // Primary inventory device for backward compatibility
          if (this.formData.deviceInventoryId && this.formData.deviceInventoryId !== 'others') {
            payload.device_inventory_id = this.formData.deviceInventoryId
          } else if (inventoryIds.length > 0) {
            payload.device_inventory_id = inventoryIds[0]
          }

          // Full list of selected inventory devices (new field)
          if (inventoryIds.length > 0) {
            payload.device_inventory_ids = inventoryIds
          }

          if (hasOthers && this.formData.customDevice) {
            payload.custom_device = this.formData.customDevice
          }

          console.log('Submitting booking request:', {
            borrowerName: this.formData.borrowerName,
            deviceType: this.formData.deviceType,
            deviceInventoryId: this.formData.deviceInventoryId,
            department: this.formData.department,
            hasUserSigned: this.hasUserSigned
          })

          // Submit to API
          const response = await bookingService.submitBooking(payload)

          if (response.success) {
            console.log('Booking submitted successfully:', response.data)

            // Auto-capture user details for ICT approval system
            await this.linkUserDetailsToICTApproval(response.data)
            console.log('Device info before showing modal:', {
              deviceInventoryId: this.formData.deviceInventoryId,
              selectedDeviceInfo: this.selectedDeviceInfo,
              deviceType: this.formData.deviceType
            })

            // Store availability info if device was out of stock
            if (response.data.availability_info) {
              this.deviceAvailabilityInfo = response.data.availability_info
            }

            // Redirect immediately to request status page to prevent multiple submissions
            this.resetForm()
            this.$router.push({
              path: '/request-status',
              query: {
                success: 'true',
                type: 'Device Booking Request',
                id: 'BOOK-' + Date.now()
              }
            })
          } else {
            console.error('API Error:', response)

            // Handle validation errors
            if (response.errors && Object.keys(response.errors).length > 0) {
              // Map backend errors to frontend form errors
              this.errors = {}
              Object.keys(response.errors).forEach((field) => {
                const errorMessages = response.errors[field]
                if (Array.isArray(errorMessages) && errorMessages.length > 0) {
                  this.errors[field] = errorMessages[0]
                }
              })

              // Also show alert for immediate feedback
              const errorMessages = Object.values(response.errors).flat()
              alert(errorMessages[0] || 'Validation failed')
            } else {
              alert(response.message || 'Failed to submit booking')
            }
          }
        } catch (error) {
          console.error('Error in submitBooking:', error)
          alert('An unexpected error occurred. Please try again.')
        } finally {
          this.isSubmitting = false
        }
      },

      resetForm() {
        this.formData = {
          bookingDate: '',
          borrowerName: '',
          deviceInventoryId: '',
          deviceInventoryIds: [],
          deviceType: '',
          customDevice: '',
          reason: '',
          department: '',
          collectionDate: '', // Fixed: matches form template field name
          returnTime: '',
          phoneNumber: '',
          conditionBefore: '',
          conditionBeforeIssues: '',
          conditionReturned: '',
          conditionReturnedIssues: '',
          status: ''
        }
        this.selectedDeviceInfo = null
        this.signatures = []
        this.hasUserSigned = false
      },

      getDeviceDisplayName() {
        console.log('getDeviceDisplayName called with:', {
          selectedDeviceInfo: this.selectedDeviceInfo,
          deviceInventoryId: this.formData.deviceInventoryId,
          deviceType: this.formData.deviceType,
          customDevice: this.formData.customDevice,
          availableDevicesCount: this.availableDevices.length
        })

        // First priority: Use stored device info if available
        if (this.selectedDeviceInfo && this.selectedDeviceInfo.name) {
          console.log('Using stored device info:', this.selectedDeviceInfo.name)
          return this.selectedDeviceInfo.name
        }

        // Second priority: If device_inventory_id is provided and not 'others', get from inventory
        if (this.formData.deviceInventoryId && this.formData.deviceInventoryId !== 'others') {
          const selectedDevice = this.availableDevices.find(
            (d) => d.id == this.formData.deviceInventoryId
          )
          if (selectedDevice) {
            console.log('Found device in inventory:', selectedDevice.device_name)
            return selectedDevice.device_name
          } else {
            console.warn(
              'Device not found in available devices list:',
              this.formData.deviceInventoryId
            )
            // Try to get device name from a stored reference if available
            return `Device ID: ${this.formData.deviceInventoryId}`
          }
        }

        // Third priority: If 'others' is selected, use custom device name
        if (this.formData.deviceInventoryId === 'others') {
          return this.formData.customDevice || 'Other Device'
        }

        // Fourth priority: Fallback to device type mapping for backward compatibility
        const deviceNames = {
          projector: 'Projector',
          tv_remote: 'TV Remote',
          hdmi_cable: 'HDMI Cable',
          monitor: 'Monitor',
          cpu: 'CPU',
          keyboard: 'Keyboard',
          pc: 'PC'
        }

        if (this.formData.deviceType && deviceNames[this.formData.deviceType]) {
          return deviceNames[this.formData.deviceType]
        }

        // Last resort
        console.warn('Could not determine device name:', {
          deviceInventoryId: this.formData.deviceInventoryId,
          deviceType: this.formData.deviceType,
          customDevice: this.formData.customDevice
        })
        return 'Unknown Device'
      },

      formatDisplayDate(dateString) {
        if (!dateString) return 'Not specified'

        try {
          const date = new Date(dateString)
          return date.toLocaleDateString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
          })
        } catch (error) {
          return dateString
        }
      },

      closeSuccessModal() {
        this.showSuccessModal = false
        // Reset form after modal is closed
        this.resetForm()
        // Always redirect to Request Status page to prevent multiple submissions
        this.$router.push({
          path: '/request-status',
          query: {
            success: 'true',
            type: 'Device Booking Request',
            id: 'BOOK-' + Date.now()
          }
        })
      },

      goBack() {
        this.$router.push('/user-dashboard')
      },

      /**
       * Auto-capture user details and link to ICT approval system
       * This is called automatically after successful booking submission
       */
      async linkUserDetailsToICTApproval(bookingData) {
        try {
          // Get user ID from auth store
          const userId = this.authStore.user?.id || this.authStore.userId
          if (!userId) {
            console.warn('⚠️ User ID not available for auto-capture')
            return
          }

          const bookingId = bookingData.id || bookingData.booking_id
          if (!bookingId) {
            console.warn('⚠️ Booking ID not available for auto-capture')
            return
          }

          console.log('🔗 Auto-capturing user details for ICT approval...', {
            bookingId,
            userId,
            borrowerName: this.formData.borrowerName,
            department: this.formData.department
          })

          // Link user details to booking for ICT approval system
          const response = await ictApprovalService.linkUserDetailsToBooking(bookingId, userId)

          if (response.success) {
            console.log('✅ User details successfully linked to ICT approval system')
          } else {
            console.warn('⚠️ Failed to link user details to ICT approval system:', response.message)
            // Don't show error to user as this is a background process
          }
        } catch (error) {
          console.error('❌ Error linking user details to ICT approval system:', error)
          // Don't show error to user as this is a background process
        }
      },

      /**
       * Scroll to the first field that has a validation error
       */
      scrollToFirstError() {
        const firstErrorField = Object.keys(this.errors)[0]
        if (firstErrorField) {
          // Create a map of error fields to form field names
          const fieldMap = {
            bookingDate: 'bookingDate',
            borrowerName: 'borrowerName',
            deviceType: 'deviceInventoryId',
            customDevice: 'customDevice',
            reason: 'reason',
            department: 'department',
            collectionDate: 'collectionDate',
            returnTime: 'returnTime',
            phoneNumber: 'phoneNumber',
            signature: 'signatureInput'
          }

          const fieldName = fieldMap[firstErrorField] || firstErrorField
          const element = document.querySelector(
            `[name="${fieldName}"], #${fieldName}, [v-model*="${fieldName}"]`
          )

          if (element) {
            element.scrollIntoView({ behavior: 'smooth', block: 'center' })
            // Add a slight delay before focusing to ensure scroll completes
            setTimeout(() => {
              element.focus()
            }, 500)
          }
        }
      }
    }
  }
</script>

<style scoped>
  /* Booking Glass morphism effects */
  .booking-glass-card {
    background: rgba(59, 130, 246, 0.15);
    backdrop-filter: blur(25px);
    -webkit-backdrop-filter: blur(25px);
    border: 2px solid rgba(96, 165, 250, 0.3);
    box-shadow:
      0 8px 32px rgba(29, 78, 216, 0.4),
      inset 0 1px 0 rgba(255, 255, 255, 0.1);
  }

  .booking-card {
    position: relative;
    overflow: hidden;
    background: rgba(59, 130, 246, 0.1);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
  }

  .booking-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(96, 165, 250, 0.2), transparent);
    transition: left 0.5s;
  }

  .booking-card:hover::before {
    left: 100%;
  }

  .booking-input {
    position: relative;
    z-index: 1;
    color: white;
  }

  .booking-input::placeholder {
    color: rgba(191, 219, 254, 0.6);
  }

  .booking-input:focus {
    border-color: rgba(59, 130, 246, 0.8);
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
  }

  /* Select dropdown styling */
  select.booking-input {
    background-image: none;
  }

  select option {
    padding: 8px;
  }

  /* Time input styling - Fixed for clickability */
  input[type='time'].booking-input {
    position: relative;
    z-index: 10;
    cursor: pointer;
    color-scheme: dark;
  }

  /* Make the time picker indicator clickable */
  input[type='time']::-webkit-calendar-picker-indicator {
    cursor: pointer;
    filter: invert(1);
    opacity: 0.7;
  }

  input[type='time']::-webkit-calendar-picker-indicator:hover {
    opacity: 1;
  }

  /* Firefox time input styling */
  input[type='time']::-moz-focus-inner {
    border: 0;
  }

  /* Ensure proper display in all browsers */
  input[type='time'] {
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    background: transparent;
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

  @keyframes slide-down {
    from {
      opacity: 0;
      transform: translateY(-10px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  @keyframes modal-in {
    from {
      opacity: 0;
      transform: scale(0.9) translateY(-20px);
    }
    to {
      opacity: 1;
      transform: scale(1) translateY(0);
    }
  }

  @keyframes modal-bounce {
    0% {
      opacity: 0;
      transform: scale(0.3) translateY(-50px);
    }
    50% {
      opacity: 1;
      transform: scale(1.05) translateY(0);
    }
    70% {
      transform: scale(0.95);
    }
    100% {
      opacity: 1;
      transform: scale(1) translateY(0);
    }
  }

  @keyframes bounce-gentle {
    0%,
    20%,
    50%,
    80%,
    100% {
      transform: translateY(0);
    }
    40% {
      transform: translateY(-10px);
    }
    60% {
      transform: translateY(-5px);
    }
  }

  @keyframes check-mark {
    0% {
      transform: scale(0);
    }
    50% {
      transform: scale(1.2);
    }
    100% {
      transform: scale(1);
    }
  }

  @keyframes spin-slow {
    from {
      transform: rotate(0deg);
    }
    to {
      transform: rotate(360deg);
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

  .animate-slide-down {
    animation: slide-down 0.3s ease-out;
  }

  .animate-modal-in {
    animation: modal-in 0.3s ease-out;
  }

  .animate-modal-bounce {
    animation: modal-bounce 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55);
  }

  .animate-bounce-gentle {
    animation: bounce-gentle 2s ease-in-out infinite;
  }

  .animate-check-mark {
    animation: check-mark 0.6s ease-out 0.3s both;
  }

  .animate-spin-slow {
    animation: spin-slow 8s linear infinite;
  }

  /* Focus styles for accessibility */
  input:focus,
  select:focus,
  textarea:focus {
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
  }

  button:focus {
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
  }

  /* Loading spinner animation */
  @keyframes spin {
    from {
      transform: rotate(0deg);
    }
    to {
      transform: rotate(360deg);
    }
  }

  .fa-spin {
    animation: spin 1s linear infinite;
  }

  /* Smooth transitions */
  * {
    transition-property:
      color, background-color, border-color, text-decoration-color, fill, stroke, opacity,
      box-shadow, transform, filter, backdrop-filter;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 200ms;
  }

  /* Responsive adjustments */
  @media (max-width: 768px) {
    .grid-cols-1.md\\:grid-cols-2 {
      grid-template-columns: repeat(1, minmax(0, 1fr));
    }

    .p-8 {
      padding: 1.5rem;
    }

    .text-3xl {
      font-size: 1.5rem;
    }

    .text-xl {
      font-size: 1rem;
    }
  }
</style>
