<template>
  <div class="flex flex-col h-screen">
    <AppHeader />
    <div class="flex flex-1 overflow-hidden">
      <ModernSidebar />
      <main
        class="flex-1 p-3 bg-gradient-to-br from-blue-900 via-blue-800 to-teal-900 overflow-y-auto relative"
      >
        <!-- Background Pattern -->
        <div class="absolute inset-0 overflow-hidden">
          <!-- Floating geometric shapes -->
          <div class="absolute inset-0">
            <div
              v-for="i in 20"
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
            class="booking-glass-card rounded-t-3xl p-6 mb-0 border-b border-blue-300/30 animate-fade-in"
          >
            <div class="flex justify-between items-center">
              <!-- Left Logo -->
              <div
                class="w-24 h-24 mr-6 transform hover:scale-110 transition-transform duration-300"
              >
                <div
                  class="w-full h-full bg-gradient-to-br from-blue-500/20 to-teal-500/20 rounded-2xl backdrop-blur-sm border-2 border-blue-300/40 flex items-center justify-center shadow-2xl hover:shadow-blue-500/25"
                >
                  <img
                    src="/assets/images/ngao2.png"
                    alt="National Shield"
                    class="max-w-16 max-h-16 object-contain"
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
                    class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-8 py-3 rounded-full text-lg font-bold shadow-2xl transform hover:scale-105 transition-all duration-300 border-2 border-blue-400/60"
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
                class="w-24 h-24 ml-6 transform hover:scale-110 transition-transform duration-300"
              >
                <div
                  class="w-full h-full bg-gradient-to-br from-teal-500/20 to-blue-500/20 rounded-2xl backdrop-blur-sm border-2 border-teal-300/40 flex items-center justify-center shadow-2xl hover:shadow-teal-500/25"
                >
                  <img
                    src="/assets/images/logo2.png"
                    alt="Muhimbili Logo"
                    class="max-w-16 max-h-16 object-contain"
                  />
                </div>
              </div>
            </div>
          </div>

          <!-- Main Form -->
          <div class="booking-glass-card rounded-b-3xl overflow-hidden animate-slide-up">
            <form @submit.prevent="submitBooking" class="p-6 space-y-6">
              <!-- Booking Information Section -->
              <div
                class="booking-card bg-gradient-to-r from-blue-600/25 to-blue-700/25 border-2 border-blue-400/40 p-6 rounded-2xl backdrop-blur-sm hover:shadow-2xl hover:shadow-blue-500/20 transition-all duration-500 group"
              >
                <div class="flex items-center space-x-4 mb-4">
                  <div
                    class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-blue-300/50"
                  >
                    <i class="fas fa-calendar-alt text-white text-lg"></i>
                  </div>
                  <h3 class="text-lg font-bold text-white flex items-center">
                    <i class="fas fa-info-circle mr-2 text-blue-300"></i>
                    Booking Information
                  </h3>
                </div>

                <div class="space-y-4">
                  <!-- Row 1: Name of Borrower & Booking Date -->
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <!-- Name of Borrower -->
                    <div class="group">
                      <label class="block text-sm font-bold text-blue-100 mb-2 flex items-center">
                        <i class="fas fa-user mr-2 text-blue-300"></i>
                        Name of Borrower
                        <span class="text-red-400 ml-1">*</span>
                      </label>
                      <div class="relative">
                        <input
                          v-model="formData.borrowerName"
                          type="text"
                          readonly
                          class="booking-input w-full px-3 py-3 bg-white/15 border-2 border-blue-300/30 rounded-xl focus:border-blue-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-blue-500/20 group-hover:border-blue-400/50 cursor-not-allowed opacity-90"
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
                        class="text-red-400 text-xs mt-1 flex items-center"
                      >
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        {{ errors.borrowerName }}
                      </div>
                      <p class="text-xs text-blue-200/60 mt-1 italic flex items-center">
                        <i class="fas fa-info-circle mr-1"></i>
                        Automatically filled from your account information
                      </p>
                    </div>

                    <!-- Booking Date -->
                    <div class="group">
                      <label class="block text-sm font-bold text-blue-100 mb-2 flex items-center">
                        <i class="fas fa-calendar mr-2 text-blue-300"></i>
                        Booking Date <span class="text-red-400 ml-1">*</span>
                      </label>
                      <div class="relative">
                        <input
                          v-model="formData.bookingDate"
                          @input="validateBookingDate"
                          @change="validateBookingDate"
                          type="date"
                          class="booking-input w-full px-3 py-3 bg-white/15 border-2 border-blue-300/30 rounded-xl focus:border-blue-400 focus:outline-none text-white backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-blue-500/20 group-hover:border-blue-400/50"
                          required
                        />
                        <div
                          class="absolute inset-0 rounded-xl bg-gradient-to-r from-blue-500/10 to-blue-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"
                        ></div>
                      </div>
                      <div
                        v-if="errors.bookingDate"
                        class="text-red-400 text-xs mt-1 flex items-center"
                      >
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        {{ errors.bookingDate }}
                      </div>
                    </div>
                  </div>

                  <!-- Row 2: Device Type & Department -->
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <!-- Type of Device -->
                    <div class="group">
                      <label class="block text-sm font-bold text-blue-100 mb-2 flex items-center">
                        <i class="fas fa-laptop mr-2 text-blue-300"></i>
                        Type of Device Borrowed
                        <span class="text-red-400 ml-1">*</span>
                      </label>
                      <div class="relative">
                        <select
                          v-model="formData.deviceInventoryId"
                          @change="handleDeviceTypeChange"
                          :disabled="isLoadingDevices"
                          class="w-full px-3 py-3 border-2 border-blue-300/30 rounded-xl focus:border-blue-400 focus:outline-none text-white bg-blue-600/80 focus:bg-blue-600/90 transition-all backdrop-blur-sm group-hover:border-blue-400/50 appearance-none cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
                          required
                        >
                          <option value="" class="bg-blue-800 text-blue-300">
                            {{ isLoadingDevices ? 'Loading devices...' : 'Select Device Type' }}
                          </option>
                          <option
                            v-for="device in availableDevices"
                            :key="device.id"
                            :value="device.id"
                            :disabled="!device.can_borrow"
                            :class="
                              device.can_borrow
                                ? 'bg-blue-800 text-white'
                                : 'bg-gray-700 text-gray-400'
                            "
                          >
                            {{ device.device_name }} -
                            <span v-if="device.available_quantity > 0" class="text-green-300">
                              {{ device.available_quantity }} available
                            </span>
                            <span v-else class="text-red-400"> Out of Stock </span>
                          </option>
                          <option value="others" class="bg-blue-800 text-white">
                            Others (Not in inventory)
                          </option>
                        </select>
                        <div
                          class="absolute right-3 top-1/2 transform -translate-y-1/2 text-blue-300/50 pointer-events-none"
                        >
                          <i
                            :class="
                              isLoadingDevices ? 'fas fa-spinner fa-spin' : 'fas fa-chevron-down'
                            "
                          ></i>
                        </div>
                      </div>
                      <div
                        v-if="errors.deviceType"
                        class="text-red-400 text-xs mt-1 flex items-center"
                      >
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        {{ errors.deviceType }}
                      </div>
                    </div>

                    <!-- Department -->
                    <div class="group">
                      <label class="block text-sm font-bold text-blue-100 mb-2 flex items-center">
                        <i class="fas fa-building mr-2 text-blue-300"></i>
                        Department <span class="text-red-400 ml-1">*</span>
                      </label>
                      <div class="relative">
                        <select
                          v-model="formData.department"
                          @change="validateDepartment"
                          class="w-full px-3 py-3 border-2 border-blue-300/30 rounded-xl focus:border-blue-400 focus:outline-none text-white bg-blue-600/80 focus:bg-blue-600/90 transition-all backdrop-blur-sm group-hover:border-blue-400/50 appearance-none cursor-pointer"
                          required
                        >
                          <option value="" class="bg-blue-800 text-blue-300">
                            Select Department
                          </option>
                          <option
                            v-for="dept in departments"
                            :key="dept.id"
                            :value="dept.id"
                            class="bg-blue-800 text-white"
                          >
                            {{ dept.name }}
                          </option>
                        </select>
                        <div
                          class="absolute right-3 top-1/2 transform -translate-y-1/2 text-blue-300/50 pointer-events-none"
                        >
                          <i class="fas fa-chevron-down"></i>
                        </div>
                      </div>
                      <div
                        v-if="errors.department"
                        class="text-red-400 text-xs mt-1 flex items-center"
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
                        <h4 class="text-sm font-bold text-yellow-100 mb-1 flex items-center">
                          <i class="fas fa-info-circle mr-2"></i>
                          Device Availability Notice
                        </h4>
                        <p class="text-sm text-yellow-200 leading-relaxed">
                          {{ deviceAvailabilityInfo.message }}
                        </p>
                        <p class="text-xs text-yellow-300/80 mt-2 italic">
                          <i class="fas fa-lightbulb mr-1"></i>
                          You can still submit your request, and it will be processed when the
                          device becomes available.
                        </p>
                      </div>
                    </div>
                  </div>

                  <!-- Custom Device Input (if Others selected) -->
                  <div
                    v-if="formData.deviceInventoryId === 'others'"
                    class="group animate-slide-down"
                  >
                    <label class="block text-sm font-bold text-blue-100 mb-2 flex items-center">
                      <i class="fas fa-edit mr-2 text-blue-300"></i>
                      Specify Device <span class="text-red-400 ml-1">*</span>
                    </label>
                    <div class="relative">
                      <input
                        v-model="formData.customDevice"
                        @input="validateCustomDevice"
                        type="text"
                        class="booking-input w-full px-3 py-3 bg-white/15 border-2 border-blue-300/30 rounded-xl focus:border-blue-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-blue-500/20 group-hover:border-blue-400/50"
                        placeholder="Please specify the device you want to borrow"
                        :required="formData.deviceInventoryId === 'others'"
                      />
                      <div
                        class="absolute inset-0 rounded-xl bg-gradient-to-r from-blue-500/10 to-blue-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"
                      ></div>
                    </div>
                    <div
                      v-if="errors.customDevice"
                      class="text-red-400 text-xs mt-1 flex items-center"
                    >
                      <i class="fas fa-exclamation-circle mr-1"></i>
                      {{ errors.customDevice }}
                    </div>
                  </div>

                  <!-- Row 3: Phone Number & Collection Date -->
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <!-- Phone Number -->
                    <div class="group">
                      <label class="block text-sm font-bold text-blue-100 mb-2 flex items-center">
                        <i class="fas fa-phone mr-2 text-blue-300"></i>
                        Phone Number <span class="text-red-400 ml-1">*</span>
                      </label>
                      <div class="relative">
                        <input
                          v-model="formData.phoneNumber"
                          @input="validatePhoneNumber"
                          type="tel"
                          class="booking-input w-full px-3 py-3 bg-white/15 border-2 border-blue-300/30 rounded-xl focus:border-blue-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-blue-500/20 group-hover:border-blue-400/50"
                          placeholder="Enter phone number"
                          pattern="[0-9]{10,}"
                          required
                        />
                        <div
                          class="absolute inset-0 rounded-xl bg-gradient-to-r from-blue-500/10 to-blue-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"
                        ></div>
                        <div
                          class="absolute right-3 top-1/2 transform -translate-y-1/2 text-blue-300/50"
                        >
                          <i class="fas fa-mobile-alt"></i>
                        </div>
                      </div>
                      <div
                        v-if="errors.phoneNumber"
                        class="text-red-400 text-xs mt-1 flex items-center"
                      >
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        {{ errors.phoneNumber }}
                      </div>
                      <p class="text-xs text-blue-200/60 mt-1 italic flex items-center">
                        <i class="fas fa-info-circle mr-1"></i>
                        e.g. 0712 000 000
                      </p>
                    </div>

                    <!-- Date of Collection -->
                    <div class="group">
                      <label class="block text-sm font-bold text-blue-100 mb-2 flex items-center">
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
                          class="booking-input w-full px-3 py-3 bg-white/15 border-2 border-blue-300/30 rounded-xl focus:border-blue-400 focus:outline-none text-white backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-blue-500/20 group-hover:border-blue-400/50"
                          required
                        />
                        <div
                          class="absolute inset-0 rounded-xl bg-gradient-to-r from-blue-500/10 to-blue-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"
                        ></div>
                      </div>
                      <div
                        v-if="errors.collectionDate"
                        class="text-red-400 text-xs mt-1 flex items-center"
                      >
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        {{ errors.collectionDate }}
                      </div>
                    </div>
                  </div>

                  <!-- Return Time -->
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div class="group">
                      <label class="block text-sm font-bold text-blue-100 mb-2 flex items-center">
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
                          class="booking-input w-full px-3 py-3 bg-white/15 border-2 border-blue-300/30 rounded-xl focus:border-blue-400 focus:outline-none text-white backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-blue-500/20 group-hover:border-blue-400/50 relative z-10"
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
                        class="text-red-400 text-xs mt-1 flex items-center"
                      >
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        {{ errors.returnTime }}
                      </div>
                      <p class="text-xs text-blue-200/60 mt-1 italic flex items-center">
                        <i class="fas fa-info-circle mr-1"></i>
                        Select the time when you plan to return the device
                      </p>
                    </div>
                  </div>

                  <!-- Reason for Borrowing -->
                  <div class="group">
                    <label class="block text-sm font-bold text-blue-100 mb-2 flex items-center">
                      <i class="fas fa-comment-alt mr-2 text-blue-300"></i>
                      Reason for Borrowing
                      <span class="text-red-400 ml-1">*</span>
                    </label>
                    <div class="relative">
                      <textarea
                        v-model="formData.reason"
                        @input="handleReasonInput"
                        class="booking-input w-full px-3 py-3 bg-white/15 border-2 border-blue-300/30 rounded-xl focus:border-blue-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-blue-500/20 group-hover:border-blue-400/50 resize-none"
                        rows="3"
                        placeholder="Please explain the reason for borrowing this device... (minimum 10 characters)"
                        maxlength="1000"
                        required
                      ></textarea>
                      <div
                        class="absolute inset-0 rounded-xl bg-gradient-to-r from-blue-500/10 to-blue-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"
                      ></div>
                    </div>
                    <div v-if="errors.reason" class="text-red-400 text-xs mt-1 flex items-center">
                      <i class="fas fa-exclamation-circle mr-1"></i>
                      {{ errors.reason }}
                    </div>
                    <div class="flex justify-between items-center mt-1">
                      <p class="text-xs text-blue-200/60 italic flex items-center">
                        <i class="fas fa-info-circle mr-1"></i>
                        Minimum 10 characters required
                      </p>
                      <p
                        class="text-xs"
                        :class="formData.reason.length >= 10 ? 'text-green-400' : 'text-yellow-400'"
                      >
                        {{ formData.reason.length }}/1000 characters
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Signature Section -->
              <div
                class="booking-card bg-gradient-to-r from-blue-600/25 to-blue-700/25 border-2 border-blue-400/40 p-4 rounded-xl backdrop-blur-sm hover:shadow-2xl hover:shadow-blue-500/20 transition-all duration-500 group"
              >
                <div class="flex items-center space-x-3 mb-3">
                  <div
                    class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-blue-300/50"
                  >
                    <i class="fas fa-signature text-white text-sm"></i>
                  </div>
                  <h3 class="text-base font-bold text-white">Digital Signature</h3>
                </div>

                <div class="group">
                  <label class="block text-sm font-bold text-blue-100 mb-2 flex items-center">
                    <i class="fas fa-signature mr-2 text-blue-300"></i>
                    Signature <span class="text-red-400 ml-1">*</span>
                    <span class="ml-2 text-xs text-blue-300/70 font-normal">(PNG, JPG, JPEG)</span>
                  </label>

                  <div class="flex flex-col md:flex-row gap-2 items-start">
                    <!-- Signature Display Box -->
                    <div
                      class="relative w-full md:w-60 h-16 border-2 border-blue-300/30 rounded-lg bg-blue-100/20 focus-within:bg-blue-100/30 focus-within:border-blue-400 overflow-hidden backdrop-blur-sm group-hover:border-blue-400/50 transition-all duration-300"
                    >
                      <!-- Signature Image Display -->
                      <div
                        v-if="signaturePreview"
                        class="w-full h-full flex items-center justify-center p-1"
                      >
                        <img
                          :src="signaturePreview"
                          alt="Digital Signature"
                          class="max-w-full max-h-full object-contain"
                        />
                      </div>

                      <!-- Placeholder Text -->
                      <div v-else class="w-full h-full flex items-center justify-center">
                        <div class="text-center">
                          <i class="fas fa-signature text-blue-400/50 text-lg mb-1"></i>
                          <p class="text-xs text-blue-400 italic">Signature here</p>
                        </div>
                      </div>

                      <!-- Remove Button (when signature exists) -->
                      <button
                        v-if="signaturePreview"
                        type="button"
                        @click="removeSignature"
                        class="absolute top-1 right-1 w-4 h-4 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600 transition duration-200 text-xs shadow-lg"
                        title="Remove signature"
                      >
                        <i class="fas fa-times text-xs"></i>
                      </button>
                    </div>

                    <!-- Upload Button -->
                    <div class="flex flex-col gap-1">
                      <input
                        ref="signatureInput"
                        type="file"
                        accept=".png,.jpg,.jpeg"
                        @change="handleSignatureUpload"
                        class="hidden"
                      />

                      <button
                        type="button"
                        @click="$refs.signatureInput.click()"
                        :disabled="uploadProgress > 0 && uploadProgress < 100"
                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white text-sm font-medium rounded-lg hover:from-blue-600 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg hover:shadow-xl transform hover:scale-105 border border-blue-400/30"
                      >
                        <span v-if="uploadProgress === 0" class="flex items-center">
                          <i class="fas fa-upload mr-1"></i>
                          Upload
                        </span>
                        <span
                          v-else-if="uploadProgress > 0 && uploadProgress < 100"
                          class="flex items-center"
                        >
                          <i class="fas fa-spinner fa-spin mr-1"></i>
                          {{ uploadProgress }}%
                        </span>
                        <span v-else class="flex items-center">
                          <i class="fas fa-check mr-1"></i>
                          Change
                        </span>
                      </button>

                      <p class="text-xs text-blue-200/70 italic">Max: 2MB</p>
                    </div>
                  </div>

                  <div v-if="errors.signature" class="text-red-500 text-xs mt-2 flex items-center">
                    <i class="fas fa-exclamation-circle mr-1"></i>
                    {{ errors.signature }}
                  </div>
                </div>
              </div>

              <!-- Submit Section -->
              <div class="border-t border-blue-300/20 pt-6">
                <div class="flex justify-between items-center">
                  <button
                    type="button"
                    @click="goBack"
                    class="px-6 py-3 bg-gray-600/80 text-white rounded-xl hover:bg-gray-700/80 transition-all duration-300 font-semibold flex items-center shadow-lg hover:shadow-xl transform hover:scale-105 backdrop-blur-sm border border-gray-500/50"
                  >
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Dashboard
                  </button>

                  <button
                    type="submit"
                    :disabled="isSubmitting"
                    class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all duration-300 font-semibold flex items-center shadow-lg hover:shadow-xl transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none border border-blue-400/50"
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
                class="w-20 h-20 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center mx-auto shadow-2xl animate-bounce-gentle border-4 border-white/50"
              >
                <i class="fas fa-check text-white text-3xl animate-check-mark"></i>
              </div>
              <!-- Success Ring Animation -->
              <div
                class="absolute inset-0 w-20 h-20 border-4 border-green-400/30 rounded-full animate-ping"
              ></div>
              <div
                class="absolute inset-0 w-20 h-20 border-2 border-green-300/50 rounded-full animate-pulse"
              ></div>
            </div>

            <!-- Success Title -->
            <h3
              class="text-2xl font-bold bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent mb-3 animate-slide-up"
            >
              🎉 Booking Submitted Successfully!
            </h3>

            <!-- Success Message -->
            <p
              class="text-gray-600 mb-6 leading-relaxed animate-slide-up"
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
              <h4 class="text-lg font-bold text-blue-800">📋 Booking Details</h4>
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
                  <p class="text-xs font-medium text-blue-600 uppercase tracking-wide">Device</p>
                  <p class="text-sm font-bold text-gray-800">{{ getDeviceDisplayName() }}</p>
                </div>
              </div>

              <!-- Borrower Info -->
              <div
                class="flex items-center p-3 bg-white/60 rounded-xl border border-blue-200/30 hover:bg-white/80 transition-all duration-300 group"
              >
                <div
                  class="w-10 h-10 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center mr-4 shadow-md group-hover:scale-110 transition-transform duration-300"
                >
                  <i class="fas fa-user text-white text-sm"></i>
                </div>
                <div class="flex-1">
                  <p class="text-xs font-medium text-green-600 uppercase tracking-wide">Borrower</p>
                  <p class="text-sm font-bold text-gray-800">{{ formData.borrowerName }}</p>
                </div>
              </div>

              <!-- Return Date Info -->
              <div
                class="flex items-center p-3 bg-white/60 rounded-xl border border-blue-200/30 hover:bg-white/80 transition-all duration-300 group"
              >
                <div
                  class="w-10 h-10 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center mr-4 shadow-md group-hover:scale-110 transition-transform duration-300"
                >
                  <i class="fas fa-calendar-check text-white text-sm"></i>
                </div>
                <div class="flex-1">
                  <p class="text-xs font-medium text-purple-600 uppercase tracking-wide">
                    Return Date
                  </p>
                  <p class="text-sm font-bold text-gray-800">
                    {{ formatDisplayDate(formData.collectionDate) }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Status Badge -->
            <div class="mt-4 text-center">
              <span
                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-yellow-400 to-orange-500 text-white text-xs font-bold rounded-full shadow-lg animate-pulse"
              >
                <i class="fas fa-clock mr-2"></i>
                PENDING REVIEW
              </span>
            </div>
          </div>

          <!-- Action Button -->
          <button
            @click="closeSuccessModal"
            class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white py-4 px-6 rounded-2xl font-bold text-lg shadow-xl hover:shadow-2xl transform hover:scale-105 transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-blue-300/50 animate-slide-up border-2 border-blue-500/30"
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
  </div>
</template>

<script>
  import ModernSidebar from '@/components/ModernSidebar.vue'
  import AppFooter from '@/components/footer.vue'
  import AppHeader from '@/components/AppHeader.vue'
  import bookingService from '@/services/bookingService'
  import deviceInventoryService from '@/services/deviceInventoryService'
  import { useAuthStore } from '@/stores/auth'

  export default {
    name: 'BookingService',
    components: {
      ModernSidebar,
      AppFooter,
      AppHeader
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
          deviceInventoryId: '', // Changed from deviceType to deviceInventoryId
          deviceType: '', // Keep for backward compatibility
          customDevice: '',
          reason: '',
          department: '',
          returnDate: '',
          returnTime: '',
          phoneNumber: '',
          conditionBefore: '',
          conditionBeforeIssues: '',
          conditionReturned: '',
          conditionReturnedIssues: '',
          status: '',
          signature: null
        },
        selectedDeviceInfo: null, // Store selected device info for display
        errors: {},
        isSubmitting: false,
        showSuccessModal: false,
        uploadProgress: 0,
        signaturePreview: null,
        departments: [],
        availableDevices: [],
        isLoadingDevices: false,
        deviceAvailabilityInfo: null, // Store availability info for out-of-stock devices
        showAvailabilityWarning: false
      }
    },
    async mounted() {
      // Load departments and available devices when component is mounted
      await this.loadDepartments()
      await this.loadAvailableDevices()

      // Auto-populate borrower name from authenticated user
      this.populateBorrowerName()

      // Debug: Check if time input is working
      console.log('BookingService mounted, returnTime field:', this.formData.returnTime)

      // Set a default time if none is set (optional)
      if (!this.formData.returnTime) {
        // Set default return time to 5 PM
        this.formData.returnTime = '17:00'
      }

      // Initialize real-time validation
      this.initializeValidation()
    },
    methods: {
      async checkDeviceAvailability(deviceInventoryId) {
        try {
          const response = await fetch(
            `/api/booking-service/devices/${deviceInventoryId}/availability`,
            {
              method: 'GET',
              headers: {
                Authorization: `Bearer ${this.authStore.token}`,
                'Content-Type': 'application/json',
                Accept: 'application/json'
              }
            }
          )

          const result = await response.json()

          if (result.success) {
            this.deviceAvailabilityInfo = result.data
            this.showAvailabilityWarning = !result.data.available && result.data.can_request

            console.log('Device availability checked:', result.data)
          } else {
            console.error('Failed to check device availability:', result.message)
          }
        } catch (error) {
          console.error('Error checking device availability:', error)
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

      validateSignature() {
        if (!this.formData.signature) {
          this.errors.signature = 'Digital signature is required'
        } else {
          this.errors.signature = ''
        }
      },

      initializeValidation() {
        // Run initial validation to show red messages for empty required fields
        this.validateBookingDate()
        this.validateDeviceType()
        this.validateDepartment()
        this.validatePhoneNumber()
        this.validateReturnDate()
        this.validateReturnTime()
        this.validateReason()
        this.validateSignature()
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
        if (deviceNameLower.includes('hdmi') && deviceNameLower.includes('cable'))
          return 'hdmi_cable'

        // Partial matches for common device types
        if (deviceNameLower.includes('laptop') || deviceNameLower.includes('notebook')) return 'pc'
        if (deviceNameLower.includes('screen') || deviceNameLower.includes('display'))
          return 'monitor'
        if (deviceNameLower.includes('remote')) return 'tv_remote'
        if (deviceNameLower.includes('cable')) return 'hdmi_cable'

        // Additional matches
        if (deviceNameLower.includes('tv') || deviceNameLower.includes('television'))
          return 'tv_remote'
        if (deviceNameLower.includes('desktop')) return 'pc'
        if (deviceNameLower.includes('project')) return 'projector' // for 'projection' etc

        // For inventory devices that don't match any pattern, use 'others'
        // The backend validation will handle this case properly when device_inventory_id is provided
        console.log(
          'Device name not recognized, using others (but device_inventory_id will be provided):',
          deviceName
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
        console.log('Device type changed:', this.formData.deviceInventoryId)

        if (this.formData.deviceInventoryId !== 'others') {
          this.formData.customDevice = ''

          // If a device from inventory is selected
          if (this.formData.deviceInventoryId) {
            const selectedDevice = this.availableDevices.find(
              (d) => d.id == this.formData.deviceInventoryId
            )
            if (selectedDevice) {
              // Store device info for later display
              this.selectedDeviceInfo = {
                id: selectedDevice.id,
                name: selectedDevice.device_name,
                code: selectedDevice.device_code
              }

              // Use device_code first, then fallback to device_name mapping
              this.formData.deviceType = this.mapDeviceToType(selectedDevice)
              console.log('Selected inventory device:', {
                name: selectedDevice.device_name,
                code: selectedDevice.device_code,
                mappedType: this.formData.deviceType
              })
            }
          } else {
            // No device selected
            this.formData.deviceType = ''
            this.selectedDeviceInfo = null
          }
        } else {
          // Others selected
          this.formData.deviceType = 'others'
          console.log('Others selected')
        }

        console.log('Final device type:', this.formData.deviceType)

        // Run real-time validation
        this.validateDeviceType()

        // Check device availability if inventory device is selected
        if (this.formData.deviceInventoryId && this.formData.deviceInventoryId !== 'others') {
          this.checkDeviceAvailability(this.formData.deviceInventoryId)
        } else {
          this.deviceAvailabilityInfo = null
          this.showAvailabilityWarning = false
        }
      },

      validateDeviceType() {
        if (!this.formData.deviceInventoryId) {
          this.errors.deviceType = 'Device type is required'
        } else if (this.formData.deviceInventoryId && !this.formData.deviceType) {
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
            // Check if selected device is available
            if (this.formData.deviceInventoryId && this.formData.deviceInventoryId !== 'others') {
              const selectedDevice = this.availableDevices.find(
                (d) => d.id == this.formData.deviceInventoryId
              )
              if (!selectedDevice || !selectedDevice.can_borrow) {
                this.errors.deviceType = 'Selected device is not available for borrowing'
              } else {
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
        if (!this.formData.phoneNumber.trim()) {
          this.errors.phoneNumber = 'Phone number is required'
        } else if (this.formData.phoneNumber.trim().length < 10) {
          this.errors.phoneNumber = 'Phone number must be at least 10 digits'
        } else if (!/^[\+]?[0-9\s\-\(\)]+$/.test(this.formData.phoneNumber.trim())) {
          this.errors.phoneNumber = 'Please enter a valid phone number'
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

      handleSignatureUpload(event) {
        const file = event.target.files[0]
        this.validateAndSetSignature(file)
      },

      validateAndSetSignature(file) {
        if (!file) return

        // Validate file type
        const allowedTypes = ['image/png', 'image/jpg', 'image/jpeg']
        if (!allowedTypes.includes(file.type)) {
          this.errors.signature = 'Please upload a PNG or JPG image file only'
          return
        }

        // Validate file size (2MB max)
        if (file.size > 2 * 1024 * 1024) {
          this.errors.signature = 'File size must be less than 2MB'
          return
        }

        // Simulate upload progress
        this.uploadProgress = 0
        const interval = setInterval(() => {
          this.uploadProgress += 20
          if (this.uploadProgress >= 100) {
            clearInterval(interval)
            setTimeout(() => {
              this.uploadProgress = 0
            }, 500)
          }
        }, 150)

        // Create image preview
        const reader = new FileReader()
        reader.onload = (e) => {
          this.signaturePreview = e.target.result
        }
        reader.readAsDataURL(file)

        this.formData.signature = file
        this.errors.signature = ''

        // Run real-time validation
        this.validateSignature()
      },

      removeSignature() {
        this.formData.signature = null
        this.signaturePreview = null
        this.errors.signature = ''
        this.uploadProgress = 0
        if (this.$refs.signatureInput) {
          this.$refs.signatureInput.value = ''
        }

        // Run real-time validation
        this.validateSignature()
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

        if (!this.formData.deviceInventoryId) {
          this.errors.deviceType = 'Device type is required'
        }

        // Ensure device_type is set when deviceInventoryId is selected
        if (this.formData.deviceInventoryId && !this.formData.deviceType) {
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

        // Check if selected device is available
        if (this.formData.deviceInventoryId && this.formData.deviceInventoryId !== 'others') {
          const selectedDevice = this.availableDevices.find(
            (d) => d.id == this.formData.deviceInventoryId
          )
          if (!selectedDevice || !selectedDevice.can_borrow) {
            this.errors.deviceType = 'Selected device is not available for borrowing'
          }
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

        if (!this.formData.phoneNumber.trim()) {
          this.errors.phoneNumber = 'Phone number is required'
        } else if (this.formData.phoneNumber.trim().length < 10) {
          this.errors.phoneNumber = 'Phone number must be at least 10 digits'
        }

        // Remove condition validation as these fields are not in the current form

        if (!this.formData.signature) {
          this.errors.signature = 'Digital signature is required'
        }

        return Object.keys(this.errors).length === 0
      },

      async submitBooking() {
        if (!this.validateForm()) {
          return
        }

        this.isSubmitting = true

        try {
          // Prepare form data for API submission
          const formData = new FormData()

          // Add all form fields
          formData.append('booking_date', this.formData.bookingDate)
          formData.append('borrower_name', this.formData.borrowerName)
          formData.append('device_type', this.formData.deviceType)

          // Add device inventory ID if selected from inventory
          if (this.formData.deviceInventoryId && this.formData.deviceInventoryId !== 'others') {
            formData.append('device_inventory_id', this.formData.deviceInventoryId)
          }

          if (this.formData.deviceInventoryId === 'others' && this.formData.customDevice) {
            formData.append('custom_device', this.formData.customDevice)
          }

          formData.append('department', this.formData.department)
          formData.append('phone_number', this.formData.phoneNumber)
          formData.append('return_date', this.formData.collectionDate) // Note: collectionDate is actually return_date
          formData.append('return_time', this.formData.returnTime)
          formData.append('reason', this.formData.reason)
          formData.append('signature', this.formData.signature)

          console.log('Submitting booking request:', {
            borrowerName: this.formData.borrowerName,
            deviceType: this.formData.deviceType,
            deviceInventoryId: this.formData.deviceInventoryId,
            department: this.formData.department,
            hasSignature: !!this.formData.signature
          })

          // Debug: Log all form data being sent
          console.log('FormData contents:')
          for (let [key, value] of formData.entries()) {
            console.log(key, ':', value)
          }

          // Submit to API
          const response = await bookingService.submitBooking(formData)

          if (response.success) {
            console.log('Booking submitted successfully:', response.data)
            console.log('Device info before showing modal:', {
              deviceInventoryId: this.formData.deviceInventoryId,
              selectedDeviceInfo: this.selectedDeviceInfo,
              deviceType: this.formData.deviceType
            })

            // Store availability info if device was out of stock
            if (response.data.availability_info) {
              this.deviceAvailabilityInfo = response.data.availability_info
            }

            this.showSuccessModal = true
            // Don't reset form immediately - wait for modal to close
            // this.resetForm()
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
          console.error('Error submitting booking:', error)
          alert('Network error. Please check your connection and try again.')
        } finally {
          this.isSubmitting = false
        }
      },

      resetForm() {
        this.formData = {
          bookingDate: '',
          borrowerName: '',
          deviceInventoryId: '',
          deviceType: '',
          customDevice: '',
          reason: '',
          department: '',
          returnDate: '',
          returnTime: '',
          phoneNumber: '',
          conditionBefore: '',
          conditionBeforeIssues: '',
          conditionReturned: '',
          conditionReturnedIssues: '',
          status: '',
          signature: null
        }
        this.removeSignature()
        this.selectedDeviceInfo = null
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
        // Redirect to Request Status page with success parameters
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
