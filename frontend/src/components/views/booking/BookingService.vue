<template>
  <div class="flex flex-col h-screen">
    <AppHeader />
    <div class="flex flex-1 overflow-hidden">
      <DynamicSidebar v-model:collapsed="sidebarCollapsed" />
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
                fontSize: Math.random() * 15 + 8 + 'px',
              }"
            >
              <i
                :class="[
                  'fas',
                  [
                    'fa-laptop',
                    'fa-tv',
                    'fa-desktop',
                    'fa-keyboard',
                    'fa-mouse',
                    'fa-headphones',
                  ][Math.floor(Math.random() * 6)],
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
          <div
            class="booking-glass-card rounded-b-3xl overflow-hidden animate-slide-up"
          >
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
                      <label
                        class="block text-sm font-bold text-blue-100 mb-2 flex items-center"
                      >
                        <i class="fas fa-user mr-2 text-blue-300"></i>
                        Name of Borrower
                        <span class="text-red-400 ml-1">*</span>
                      </label>
                      <div class="relative">
                        <input
                          v-model="formData.borrowerName"
                          type="text"
                          class="booking-input w-full px-3 py-3 bg-white/15 border-2 border-blue-300/30 rounded-xl focus:border-blue-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-blue-500/20 group-hover:border-blue-400/50"
                          placeholder="Enter borrower's full name"
                          required
                        />
                        <div
                          class="absolute inset-0 rounded-xl bg-gradient-to-r from-blue-500/10 to-blue-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"
                        ></div>
                        <div
                          class="absolute right-3 top-1/2 transform -translate-y-1/2 text-blue-300/50"
                        >
                          <i class="fas fa-user-circle"></i>
                        </div>
                      </div>
                      <div
                        v-if="errors.borrowerName"
                        class="text-red-400 text-xs mt-1 flex items-center"
                      >
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        {{ errors.borrowerName }}
                      </div>
                    </div>

                    <!-- Booking Date -->
                    <div class="group">
                      <label
                        class="block text-sm font-bold text-blue-100 mb-2 flex items-center"
                      >
                        <i class="fas fa-calendar mr-2 text-blue-300"></i>
                        Booking Date <span class="text-red-400 ml-1">*</span>
                      </label>
                      <div class="relative">
                        <input
                          v-model="formData.bookingDate"
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
                      <label
                        class="block text-sm font-bold text-blue-100 mb-2 flex items-center"
                      >
                        <i class="fas fa-laptop mr-2 text-blue-300"></i>
                        Type of Device Borrowed
                        <span class="text-red-400 ml-1">*</span>
                      </label>
                      <div class="relative">
                        <select
                          v-model="formData.deviceType"
                          @change="handleDeviceTypeChange"
                          class="w-full px-3 py-3 border-2 border-blue-300/30 rounded-xl focus:border-blue-400 focus:outline-none text-white bg-blue-600/80 focus:bg-blue-600/90 transition-all backdrop-blur-sm group-hover:border-blue-400/50 appearance-none cursor-pointer"
                          required
                        >
                          <option value="" class="bg-blue-800 text-blue-300">
                            Select Device Type
                          </option>
                          <option
                            value="projector"
                            class="bg-blue-800 text-white"
                          >
                            Projector
                          </option>
                          <option
                            value="tv_remote"
                            class="bg-blue-800 text-white"
                          >
                            TV Remote
                          </option>
                          <option
                            value="hdmi_cable"
                            class="bg-blue-800 text-white"
                          >
                            HDMI Cable
                          </option>
                          <option
                            value="monitor"
                            class="bg-blue-800 text-white"
                          >
                            Monitor
                          </option>
                          <option value="cpu" class="bg-blue-800 text-white">
                            CPU
                          </option>
                          <option
                            value="keyboard"
                            class="bg-blue-800 text-white"
                          >
                            Keyboard
                          </option>
                          <option value="pc" class="bg-blue-800 text-white">
                            PC
                          </option>
                          <option value="others" class="bg-blue-800 text-white">
                            Others
                          </option>
                        </select>
                        <div
                          class="absolute right-3 top-1/2 transform -translate-y-1/2 text-blue-300/50 pointer-events-none"
                        >
                          <i class="fas fa-chevron-down"></i>
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
                      <label
                        class="block text-sm font-bold text-blue-100 mb-2 flex items-center"
                      >
                        <i class="fas fa-building mr-2 text-blue-300"></i>
                        Department <span class="text-red-400 ml-1">*</span>
                      </label>
                      <div class="relative">
                        <select
                          v-model="formData.department"
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

                  <!-- Custom Device Input (if Others selected) -->
                  <div
                    v-if="formData.deviceType === 'others'"
                    class="group animate-slide-down"
                  >
                    <label
                      class="block text-sm font-bold text-blue-100 mb-2 flex items-center"
                    >
                      <i class="fas fa-edit mr-2 text-blue-300"></i>
                      Specify Device <span class="text-red-400 ml-1">*</span>
                    </label>
                    <div class="relative">
                      <input
                        v-model="formData.customDevice"
                        type="text"
                        class="booking-input w-full px-3 py-3 bg-white/15 border-2 border-blue-300/30 rounded-xl focus:border-blue-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-blue-500/20 group-hover:border-blue-400/50"
                        placeholder="Please specify the device you want to borrow"
                        :required="formData.deviceType === 'others'"
                      />
                      <div
                        class="absolute inset-0 rounded-xl bg-gradient-to-r from-blue-500/10 to-blue-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"
                      ></div>
                    </div>
                  </div>

                  <!-- Row 3: Phone Number & Collection Date -->
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <!-- Phone Number -->
                    <div class="group">
                      <label
                        class="block text-sm font-bold text-blue-100 mb-2 flex items-center"
                      >
                        <i class="fas fa-phone mr-2 text-blue-300"></i>
                        Phone Number <span class="text-red-400 ml-1">*</span>
                      </label>
                      <div class="relative">
                        <input
                          v-model="formData.phoneNumber"
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
                      <p
                        class="text-xs text-blue-200/60 mt-1 italic flex items-center"
                      >
                        <i class="fas fa-info-circle mr-1"></i>
                        e.g. 0712 000 000
                      </p>
                    </div>

                    <!-- Date of Collection -->
                    <div class="group">
                      <label
                        class="block text-sm font-bold text-blue-100 mb-2 flex items-center"
                      >
                        <i class="fas fa-calendar-plus mr-2 text-blue-300"></i>
                        Date of returning
                        <span class="text-red-400 ml-1">*</span>
                      </label>
                      <div class="relative">
                        <input
                          v-model="formData.collectionDate"
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
                      <label
                        class="block text-sm font-bold text-blue-100 mb-2 flex items-center"
                      >
                        <i class="fas fa-clock mr-2 text-blue-300"></i>
                        Return Time <span class="text-red-400 ml-1">*</span>
                      </label>
                      <div class="relative">
                        <input
                          v-model="formData.returnTime"
                          type="time"
                          class="booking-input w-full px-3 py-3 bg-white/15 border-2 border-blue-300/30 rounded-xl focus:border-blue-400 focus:outline-none text-white backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-blue-500/20 group-hover:border-blue-400/50"
                          required
                        />
                        <div
                          class="absolute inset-0 rounded-xl bg-gradient-to-r from-blue-500/10 to-blue-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"
                        ></div>
                      </div>
                      <div
                        v-if="errors.returnTime"
                        class="text-red-400 text-xs mt-1 flex items-center"
                      >
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        {{ errors.returnTime }}
                      </div>
                    </div>
                  </div>

                  <!-- Reason for Borrowing -->
                  <div class="group">
                    <label
                      class="block text-sm font-bold text-blue-100 mb-2 flex items-center"
                    >
                      <i class="fas fa-comment-alt mr-2 text-blue-300"></i>
                      Reason for Borrowing
                      <span class="text-red-400 ml-1">*</span>
                    </label>
                    <div class="relative">
                      <textarea
                        v-model="formData.reason"
                        class="booking-input w-full px-3 py-3 bg-white/15 border-2 border-blue-300/30 rounded-xl focus:border-blue-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-blue-500/20 group-hover:border-blue-400/50 resize-none"
                        rows="3"
                        placeholder="Please explain the reason for borrowing this device..."
                        required
                      ></textarea>
                      <div
                        class="absolute inset-0 rounded-xl bg-gradient-to-r from-blue-500/10 to-blue-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"
                      ></div>
                    </div>
                    <div
                      v-if="errors.reason"
                      class="text-red-400 text-xs mt-1 flex items-center"
                    >
                      <i class="fas fa-exclamation-circle mr-1"></i>
                      {{ errors.reason }}
                    </div>
                  </div>
                </div>
              </div>

              <!-- Device Condition Section -->

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
                  <h3 class="text-base font-bold text-white">
                    Digital Signature
                  </h3>
                </div>

                <div class="group">
                  <label
                    class="block text-sm font-bold text-blue-100 mb-2 flex items-center"
                  >
                    <i class="fas fa-signature mr-2 text-blue-300"></i>
                    Signature <span class="text-red-400 ml-1">*</span>
                    <span class="ml-2 text-xs text-blue-300/70 font-normal"
                      >(PNG, JPG, JPEG)</span
                    >
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
                      <div
                        v-else
                        class="w-full h-full flex items-center justify-center"
                      >
                        <div class="text-center">
                          <i
                            class="fas fa-signature text-blue-400/50 text-lg mb-1"
                          ></i>
                          <p class="text-xs text-blue-400 italic">
                            Signature here
                          </p>
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
                        <span
                          v-if="uploadProgress === 0"
                          class="flex items-center"
                        >
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

                      <p
                        class="text-xs text-blue-200/70 italic"
                      >
                        Max: 2MB
                      </p>
                    </div>
                  </div>

                  <div
                    v-if="errors.signature"
                    class="text-red-500 text-xs mt-2 flex items-center"
                  >
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
                    {{
                      isSubmitting
                        ? "Submitting Booking..."
                        : "Submit Booking Request"
                    }}
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </main>
      <!-- Footer -->
      <AppFooter />
    </div>

    <!-- Success Modal -->
    <div
      v-if="showSuccessModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
    >
      <div
        class="bg-white rounded-xl shadow-2xl max-w-md w-full transform transition-all duration-300 scale-100 animate-modal-in"
      >
        <div class="p-6 text-center">
          <div
            class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4"
          >
            <i class="fas fa-check text-green-600 text-2xl"></i>
          </div>
          <h3 class="text-xl font-bold text-gray-800 mb-2">
            Booking Submitted Successfully!
          </h3>
          <p class="text-gray-600 mb-4">
            Your device booking request has been submitted and is now under
            review.
          </p>
          <div class="bg-blue-50 rounded-lg p-3 mb-6">
            <p class="text-sm text-blue-800 font-medium">Booking Details:</p>
            <div class="text-xs text-blue-700 mt-2 space-y-1">
              <p><strong>Device:</strong> {{ getDeviceDisplayName() }}</p>
              <p><strong>Borrower:</strong> {{ formData.borrowerName }}</p>
              <p>
                <strong>Collection Date:</strong> {{ formData.collectionDate }}
              </p>
            </div>
          </div>
          <button
            @click="closeSuccessModal"
            class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200"
          >
            <i class="fas fa-home mr-2"></i>
            Return to Dashboard
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue'
import DynamicSidebar from '@/components/DynamicSidebar.vue'
import AppFooter from '@/components/footer.vue'
import AppHeader from '@/components/AppHeader.vue'
import bookingService from '@/services/bookingService'

export default {
  name: 'BookingService',
  components: {
    DynamicSidebar,
    AppFooter,
    AppHeader
  },
  setup() {
    const sidebarCollapsed = ref(false)

    return {
      sidebarCollapsed
    }
  },
  data() {
    return {
      formData: {
        bookingDate: '',
        borrowerName: '',
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
      },
      errors: {},
      isSubmitting: false,
      showSuccessModal: false,
      uploadProgress: 0,
      signaturePreview: null,
      departments: []
    }
  },
  async mounted() {
    // Load departments when component is mounted
    await this.loadDepartments()
  },
  methods: {
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
    handleDeviceTypeChange() {
      if (this.formData.deviceType !== 'others') {
        this.formData.customDevice = ''
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
    },

    removeSignature() {
      this.formData.signature = null
      this.signaturePreview = null
      this.errors.signature = ''
      this.uploadProgress = 0
      if (this.$refs.signatureInput) {
        this.$refs.signatureInput.value = ''
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

      if (!this.formData.deviceType) {
        this.errors.deviceType = 'Device type is required'
      }

      if (
        this.formData.deviceType === 'others' &&
        !this.formData.customDevice.trim()
      ) {
        this.errors.customDevice = 'Please specify the device'
      }

      if (!this.formData.reason.trim()) {
        this.errors.reason = 'Reason for borrowing is required'
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
        if (
          this.formData.deviceType === 'others' &&
          this.formData.customDevice
        ) {
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
          department: this.formData.department,
          hasSignature: !!this.formData.signature
        })

        // Submit to API
        const response = await bookingService.submitBooking(formData)

        if (response.success) {
          console.log('Booking submitted successfully:', response.data)
          this.showSuccessModal = true
          this.resetForm()
        } else {
          console.error('API Error:', response)

          // Handle validation errors
          if (response.errors && Object.keys(response.errors).length > 0) {
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
    },

    getDeviceDisplayName() {
      if (this.formData.deviceType === 'others') {
        return this.formData.customDevice || 'Other Device'
      }

      const deviceNames = {
        projector: 'Projector',
        tv_remote: 'TV Remote',
        hdmi_cable: 'HDMI Cable',
        monitor: 'Monitor',
        cpu: 'CPU',
        keyboard: 'Keyboard',
        pc: 'PC'
      }

      return deviceNames[this.formData.deviceType] || this.formData.deviceType
    },

    closeSuccessModal() {
      this.showSuccessModal = false
      this.goBack()
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
  box-shadow: 0 8px 32px rgba(29, 78, 216, 0.4),
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
  content: "";
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(
    90deg,
    transparent,
    rgba(96, 165, 250, 0.2),
    transparent
  );
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
  transition-property: color, background-color, border-color,
    text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter,
    backdrop-filter;
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
