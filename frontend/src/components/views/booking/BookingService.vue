<template>
  <div class="flex flex-col h-screen">
    <AppHeader />
    <div class="flex flex-1 overflow-hidden">
      <DynamicSidebar v-model:collapsed="sidebarCollapsed" />
      <main class="flex-1 p-6 bg-gradient-to-br from-blue-900 via-blue-800 to-blue-700 overflow-y-auto relative">
        <!-- Background Pattern -->
        <div class="absolute inset-0 overflow-hidden">
          <!-- Floating geometric shapes -->
          <div class="absolute inset-0">
            <div v-for="i in 20" :key="i" 
                 class="absolute text-white opacity-5 animate-float"
                 :style="{
                   left: Math.random() * 100 + '%',
                   top: Math.random() * 100 + '%',
                   animationDelay: Math.random() * 3 + 's',
                   animationDuration: (Math.random() * 3 + 2) + 's',
                   fontSize: (Math.random() * 15 + 8) + 'px'
                 }">
              <i :class="['fas', ['fa-laptop', 'fa-tv', 'fa-desktop', 'fa-keyboard', 'fa-mouse', 'fa-headphones'][Math.floor(Math.random() * 6)]]"></i>
            </div>
          </div>
        </div>
        
        <div class="max-w-10xl mx-auto relative z-10">
          <!-- Header Section -->
          <div class="booking-glass-card rounded-t-3xl p-4 mb-0 border-b border-blue-300/30 animate-fade-in">
            <div class="flex justify-between items-center">
              <!-- Left Logo -->
              <div class="w-16 h-16 mr-4 transform hover:scale-110 transition-transform duration-300">
                <div class="w-full h-full bg-gradient-to-br from-blue-500/20 to-blue-600/20 rounded-xl backdrop-blur-sm border-2 border-blue-300/40 flex items-center justify-center shadow-xl hover:shadow-blue-500/25">
                  <img src="/assets/images/ngao2.png" alt="National Shield" class="max-w-12 max-h-12 object-contain" />
                </div>
              </div>

              <!-- Center Content -->
              <div class="text-center flex-1">
                <h1 class="text-2xl font-bold text-white mb-2 tracking-wide drop-shadow-lg animate-fade-in">
                  MUHIMBILI NATIONAL HOSPITAL
                </h1>
                <div class="relative inline-block mb-2">
                  <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-2 rounded-full text-base font-bold shadow-xl transform hover:scale-105 transition-all duration-300 border-2 border-blue-400/60">
                    <span class="relative z-10 flex items-center gap-2">
                      <i class="fas fa-calendar-check"></i>
                      DEVICE BOOKING SERVICE
                    </span>
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-700 to-blue-800 rounded-full opacity-0 hover:opacity-100 transition-opacity duration-300"></div>
                  </div>
                </div>
                <h2 class="text-lg font-bold text-blue-100 tracking-wide drop-shadow-md animate-fade-in-delay">
                  ICT EQUIPMENT RESERVATION SYSTEM
                </h2>
              </div>
              
              <!-- Right Logo -->
              <div class="w-16 h-16 ml-4 transform hover:scale-110 transition-transform duration-300">
                <div class="w-full h-full bg-gradient-to-br from-blue-500/20 to-blue-600/20 rounded-xl backdrop-blur-sm border-2 border-blue-300/40 flex items-center justify-center shadow-xl hover:shadow-blue-500/25">
                  <img src="/assets/images/logo2.png" alt="Muhimbili Logo" class="max-w-12 max-h-12 object-contain" />
                </div>
              </div>
            </div>
          </div>

          <!-- Main Form -->
          <div class="booking-glass-card rounded-b-3xl overflow-hidden animate-slide-up">
            <form @submit.prevent="submitBooking" class="p-6 space-y-6">
              
              <!-- Booking Information Section -->
              <div class="booking-card bg-gradient-to-r from-blue-600/25 to-blue-700/25 border-2 border-blue-400/40 p-4 rounded-lg backdrop-blur-sm hover:shadow-lg hover:shadow-blue-500/20 transition-all duration-300 group">
                <div class="flex items-center space-x-2 mb-3">
                  <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-md flex items-center justify-center shadow-md group-hover:scale-110 transition-transform duration-300 border border-blue-300/50">
                    <i class="fas fa-calendar-alt text-white text-sm"></i>
                  </div>
                  <h3 class="text-base font-bold text-white flex items-center">
                    <i class="fas fa-info-circle mr-1 text-blue-300 text-sm"></i>
                    Booking Information
                  </h3>
                </div>

                <div class="space-y-3">
                  <!-- Row 1: Booking Date & Name -->
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <!-- Booking Date -->
                    <div class="group">
                      <label class="block text-xs font-semibold text-blue-100 mb-1.5 flex items-center">
                        <i class="fas fa-calendar mr-1.5 text-blue-300 text-xs"></i>
                        Booking Date <span class="text-red-400 ml-1">*</span>
                      </label>
                      <div class="relative">
                        <input 
                          v-model="formData.booking_date" 
                          type="date" 
                          class="booking-input w-full px-3 py-2.5 bg-white/15 border border-blue-300/40 rounded-md focus:border-blue-400 focus:outline-none text-white text-sm backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/25 focus:shadow-md focus:shadow-blue-500/20 group-hover:border-blue-400/60"
                          required
                        />
                        <div class="absolute inset-0 rounded-md bg-gradient-to-r from-blue-500/5 to-blue-600/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
                      </div>
                      <div v-if="validationErrors.booking_date" class="text-red-400 text-xs mt-1 flex items-center">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        {{ validationErrors.booking_date[0] }}
                      </div>
                    </div>

                    <!-- Name of Borrower -->
                    <div class="group">
                      <label class="block text-xs font-semibold text-blue-100 mb-1.5 flex items-center">
                        <i class="fas fa-user mr-1.5 text-blue-300 text-xs"></i>
                        Name of Borrower <span class="text-red-400 ml-1">*</span>
                      </label>
                      <div class="relative">
                        <input 
                          v-model="formData.borrower_name" 
                          type="text" 
                          class="booking-input w-full px-3 py-2.5 bg-white/15 border border-blue-300/40 rounded-md focus:border-blue-400 focus:outline-none text-white text-sm placeholder-blue-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/25 focus:shadow-md focus:shadow-blue-500/20 group-hover:border-blue-400/60"
                          placeholder="Enter borrower's full name"
                          required
                        />
                        <div class="absolute inset-0 rounded-md bg-gradient-to-r from-blue-500/5 to-blue-600/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
                        <div class="absolute right-2.5 top-1/2 transform -translate-y-1/2 text-blue-300/50">
                          <i class="fas fa-user-circle text-sm"></i>
                        </div>
                      </div>
                      <div v-if="validationErrors.borrower_name" class="text-red-400 text-xs mt-1 flex items-center">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        {{ validationErrors.borrower_name[0] }}
                      </div>
                    </div>
                  </div>

                  <!-- Row 2: Device Type & Department -->
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <!-- Type of Device -->
                    <div class="group">
                      <label class="block text-xs font-semibold text-blue-100 mb-1.5 flex items-center">
                        <i class="fas fa-laptop mr-1.5 text-blue-300 text-xs"></i>
                        Type of Device Borrowed <span class="text-red-400 ml-1">*</span>
                      </label>
                      <div class="relative">
                        <select 
                          v-model="formData.device_type" 
                          @change="handleDeviceTypeChange"
                          class="w-full px-3 py-2.5 border border-blue-300/40 rounded-md focus:border-blue-400 focus:outline-none text-white text-sm bg-blue-600/60 focus:bg-blue-600/80 transition-all backdrop-blur-sm group-hover:border-blue-400/60 appearance-none cursor-pointer"
                          required
                        >
                          <option value="" class="bg-blue-800 text-blue-300">Select Device Type</option>
                          <option v-for="deviceType in deviceTypes" :key="deviceType.value" :value="deviceType.value" class="bg-blue-800 text-white">
                            {{ deviceType.label }}
                          </option>
                        </select>
                        <div class="absolute right-2.5 top-1/2 transform -translate-y-1/2 text-blue-300/50 pointer-events-none">
                          <i class="fas fa-chevron-down text-sm"></i>
                        </div>
                      </div>
                      <div v-if="validationErrors.device_type" class="text-red-400 text-xs mt-1 flex items-center">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        {{ validationErrors.device_type[0] }}
                      </div>
                    </div>

                    <!-- Department -->
                    <div class="group">
                      <label class="block text-xs font-semibold text-blue-100 mb-1.5 flex items-center">
                        <i class="fas fa-building mr-1.5 text-blue-300 text-xs"></i>
                        Department <span class="text-red-400 ml-1">*</span>
                      </label>
                      <div class="relative">
                        <select 
                          v-model="formData.department" 
                          class="w-full px-3 py-2.5 border border-blue-300/40 rounded-md focus:border-blue-400 focus:outline-none text-white text-sm bg-blue-600/60 focus:bg-blue-600/80 transition-all backdrop-blur-sm group-hover:border-blue-400/60 appearance-none cursor-pointer"
                          required
                        >
                          <option value="" class="bg-blue-800 text-blue-300">Select Department</option>
                          <option v-for="dept in departments" :key="dept.value" :value="dept.value" class="bg-blue-800 text-white">
                            {{ dept.label }}
                          </option>
                        </select>
                        <div class="absolute right-2.5 top-1/2 transform -translate-y-1/2 text-blue-300/50 pointer-events-none">
                          <i class="fas fa-chevron-down text-sm"></i>
                        </div>
                      </div>
                      <div v-if="validationErrors.department" class="text-red-400 text-xs mt-1 flex items-center">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        {{ validationErrors.department[0] }}
                      </div>
                    </div>
                  </div>

                  <!-- Custom Device Input (if Others selected) -->
                  <div v-if="formData.device_type === 'others'" class="group animate-slide-down">
                    <label class="block text-xs font-semibold text-blue-100 mb-1.5 flex items-center">
                      <i class="fas fa-edit mr-1.5 text-blue-300 text-xs"></i>
                      Specify Device <span class="text-red-400 ml-1">*</span>
                    </label>
                    <div class="relative">
                      <input 
                        v-model="formData.custom_device" 
                        type="text" 
                        class="booking-input w-full px-3 py-2.5 bg-white/15 border border-blue-300/40 rounded-md focus:border-blue-400 focus:outline-none text-white text-sm placeholder-blue-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/25 focus:shadow-md focus:shadow-blue-500/20 group-hover:border-blue-400/60"
                        placeholder="Please specify the device you want to borrow"
                        :required="formData.device_type === 'others'"
                      />
                      <div class="absolute inset-0 rounded-md bg-gradient-to-r from-blue-500/5 to-blue-600/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
                    </div>
                    <div v-if="validationErrors.custom_device" class="text-red-400 text-xs mt-1 flex items-center">
                      <i class="fas fa-exclamation-circle mr-1"></i>
                      {{ validationErrors.custom_device[0] }}
                    </div>
                  </div>

                  <!-- Row 3: Phone Number & Return Date -->
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <!-- Phone Number -->
                    <div class="group">
                      <label class="block text-xs font-semibold text-blue-100 mb-1.5 flex items-center">
                        <i class="fas fa-phone mr-1.5 text-blue-300 text-xs"></i>
                        Phone Number <span class="text-red-400 ml-1">*</span>
                      </label>
                      <div class="relative">
                        <input 
                          v-model="formData.phone_number" 
                          type="tel" 
                          class="booking-input w-full px-3 py-2.5 bg-white/15 border border-blue-300/40 rounded-md focus:border-blue-400 focus:outline-none text-white text-sm placeholder-blue-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/25 focus:shadow-md focus:shadow-blue-500/20 group-hover:border-blue-400/60"
                          placeholder="Enter phone number"
                          pattern="[0-9]{10,}"
                          required
                        />
                        <div class="absolute inset-0 rounded-md bg-gradient-to-r from-blue-500/5 to-blue-600/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
                        <div class="absolute right-2.5 top-1/2 transform -translate-y-1/2 text-blue-300/50">
                          <i class="fas fa-mobile-alt text-sm"></i>
                        </div>
                      </div>
                      <div v-if="validationErrors.phone_number" class="text-red-400 text-xs mt-1 flex items-center">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        {{ validationErrors.phone_number[0] }}
                      </div>
                      <p class="text-xs text-blue-200/60 mt-0.5 italic flex items-center">
                        <i class="fas fa-info-circle mr-1 text-xs"></i>
                        e.g. 0712 000 000
                      </p>
                    </div>

                    <!-- Date of Return -->
                    <div class="group">
                      <label class="block text-xs font-semibold text-blue-100 mb-1.5 flex items-center">
                        <i class="fas fa-calendar-plus mr-1.5 text-blue-300 text-xs"></i>
                        Date of returning <span class="text-red-400 ml-1">*</span>
                      </label>
                      <div class="relative">
                        <input 
                          v-model="formData.return_date" 
                          type="date" 
                          class="booking-input w-full px-3 py-2.5 bg-white/15 border border-blue-300/40 rounded-md focus:border-blue-400 focus:outline-none text-white text-sm backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/25 focus:shadow-md focus:shadow-blue-500/20 group-hover:border-blue-400/60"
                          required
                        />
                        <div class="absolute inset-0 rounded-md bg-gradient-to-r from-blue-500/5 to-blue-600/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
                      </div>
                      <div v-if="validationErrors.return_date" class="text-red-400 text-xs mt-1 flex items-center">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        {{ validationErrors.return_date[0] }}
                      </div>
                    </div>
                  </div>

                  <!-- Return Time -->
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div class="group">
                      <label class="block text-xs font-semibold text-blue-100 mb-1.5 flex items-center">
                        <i class="fas fa-clock mr-1.5 text-blue-300 text-xs"></i>
                        Return Time <span class="text-red-400 ml-1">*</span>
                      </label>
                      <div class="relative">
                        <input 
                          v-model="formData.return_time" 
                          type="time" 
                          class="booking-input w-full px-3 py-2.5 bg-white/15 border border-blue-300/40 rounded-md focus:border-blue-400 focus:outline-none text-white text-sm backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/25 focus:shadow-md focus:shadow-blue-500/20 group-hover:border-blue-400/60"
                          required
                        />
                        <div class="absolute inset-0 rounded-md bg-gradient-to-r from-blue-500/5 to-blue-600/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
                      </div>
                      <div v-if="validationErrors.return_time" class="text-red-400 text-xs mt-1 flex items-center">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        {{ validationErrors.return_time[0] }}
                      </div>
                    </div>
                  </div>

                  <!-- Reason for Borrowing -->
                  <div class="group">
                    <label class="block text-xs font-semibold text-blue-100 mb-1.5 flex items-center">
                      <i class="fas fa-comment-alt mr-1.5 text-blue-300 text-xs"></i>
                      Reason for Borrowing <span class="text-red-400 ml-1">*</span>
                    </label>
                    <div class="relative">
                      <textarea 
                        v-model="formData.reason" 
                        class="booking-input w-full px-3 py-2.5 bg-white/15 border border-blue-300/40 rounded-md focus:border-blue-400 focus:outline-none text-white text-sm placeholder-blue-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/25 focus:shadow-md focus:shadow-blue-500/20 group-hover:border-blue-400/60 resize-none"
                        rows="3"
                        placeholder="Please explain the reason for borrowing this device..."
                        required
                      ></textarea>
                      <div class="absolute inset-0 rounded-md bg-gradient-to-r from-blue-500/5 to-blue-600/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
                    </div>
                    <div v-if="validationErrors.reason" class="text-red-400 text-xs mt-1 flex items-center">
                      <i class="fas fa-exclamation-circle mr-1"></i>
                      {{ validationErrors.reason[0] }}
                    </div>
                  </div>
                </div>
              </div>

              <!-- Signature Section -->
              <div class="booking-card bg-gradient-to-r from-blue-600/25 to-blue-700/25 border-2 border-blue-400/40 p-4 rounded-lg backdrop-blur-sm hover:shadow-lg hover:shadow-blue-500/20 transition-all duration-300 group">
                <div class="flex items-center space-x-2 mb-3">
                  <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-md flex items-center justify-center shadow-md group-hover:scale-110 transition-transform duration-300 border border-blue-300/50">
                    <i class="fas fa-signature text-white text-sm"></i>
                  </div>
                  <h3 class="text-base font-bold text-white flex items-center">
                    <i class="fas fa-pen-fancy mr-1 text-blue-300 text-sm"></i>
                    Digital Signature
                  </h3>
                </div>

                <div class="group">
                  <label class="block text-xs font-semibold text-blue-100 mb-2 flex items-center">
                    <i class="fas fa-signature mr-1.5 text-blue-300 text-xs"></i>
                    Signature on Collection <span class="text-red-400 ml-1">*</span>
                    <span class="ml-2 text-xs text-blue-300/70 font-normal">(PNG, JPG, JPEG)</span>
                  </label>
                  
                  <div class="flex flex-col md:flex-row gap-3 items-start">
                    <!-- Signature Display Box -->
                    <div class="relative w-full md:w-48 h-14 border border-blue-300/40 rounded-md bg-blue-100/20 focus-within:bg-blue-100/30 focus-within:border-blue-400 overflow-hidden backdrop-blur-sm group-hover:border-blue-400/60 transition-all duration-300">
                      <!-- Signature Image Display -->
                      <div v-if="signaturePreview" class="w-full h-full flex items-center justify-center p-2">
                        <img 
                          :src="signaturePreview" 
                          alt="Digital Signature" 
                          class="max-w-full max-h-full object-contain"
                        />
                      </div>
                      
                      <!-- Placeholder Text -->
                      <div v-else class="w-full h-full flex items-center justify-center">
                        <div class="text-center">
                          <i class="fas fa-signature text-blue-400/50 text-sm mb-0.5"></i>
                          <p class="text-xs text-blue-400 italic">Signature will appear here</p>
                        </div>
                      </div>
                      
                      <!-- Remove Button (when signature exists) -->
                      <button
                        v-if="signaturePreview"
                        type="button"
                        @click="removeSignature"
                        class="absolute top-1 right-1 w-5 h-5 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600 transition duration-200 text-xs shadow-md"
                        title="Remove signature"
                      >
                        <i class="fas fa-times text-xs"></i>
                      </button>
                    </div>
                    
                    <!-- Upload Button -->
                    <div class="flex flex-col gap-2">
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
                          <i class="fas fa-upload mr-2"></i>
                          Load Signature
                        </span>
                        <span v-else-if="uploadProgress > 0 && uploadProgress < 100" class="flex items-center">
                          <i class="fas fa-spinner fa-spin mr-2"></i>
                          Uploading... {{ uploadProgress }}%
                        </span>
                        <span v-else class="flex items-center">
                          <i class="fas fa-check mr-2"></i>
                          Change Signature
                        </span>
                      </button>
                      
                      <p class="text-xs text-blue-200/70 italic flex items-center">
                        <i class="fas fa-info-circle mr-1"></i>
                        Max size: 2MB
                      </p>
                    </div>
                  </div>
                  
                  <div v-if="validationErrors.signature" class="text-red-500 text-xs mt-3 flex items-center">
                    <i class="fas fa-exclamation-circle mr-1"></i>
                    {{ validationErrors.signature[0] }}
                  </div>
                </div>
              </div>

              <!-- Submit Section -->
              <div class="border-t border-blue-300/20 pt-6">
                <div class="flex justify-between items-center">
                  <!-- Left: Back Button -->
                  <button
                    type="button"
                    @click="goBack"
                    class="group relative inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-gray-600 via-gray-700 to-gray-800 text-white font-bold rounded-xl shadow-xl hover:shadow-gray-500/25 transform hover:scale-105 transition-all duration-300 border border-gray-500/50 overflow-hidden"
                  >
                    <!-- Button background animation -->
                    <div class="absolute inset-0 bg-gradient-to-r from-gray-500 via-gray-600 to-gray-700 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    
                    <!-- Button content -->
                    <div class="relative z-10 flex items-center">
                      <i class="fas fa-arrow-left mr-3 text-lg group-hover:-translate-x-1 transition-transform duration-300"></i>
                      <span class="text-base">Back to Dashboard</span>
                    </div>
                    
                    <!-- Button shine effect -->
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -skew-x-12 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-700"></div>
                  </button>
                  
                  <!-- Right: Submit Button -->
                  <button
                    type="submit"
                    :disabled="isSubmitting"
                    @click="handleSubmitClick"
                    class="group relative inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-blue-600 via-blue-700 to-blue-800 text-white font-bold rounded-xl shadow-xl hover:shadow-blue-500/25 transform hover:scale-105 transition-all duration-300 border border-blue-500/50 overflow-hidden disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none disabled:hover:scale-100"
                  >
                    <!-- Button background animation -->
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    
                    <!-- Button content -->
                    <div class="relative z-10 flex items-center">
                      <i v-if="isSubmitting" class="fas fa-spinner fa-spin mr-3 text-lg"></i>
                      <i v-else class="fas fa-paper-plane mr-3 text-lg group-hover:translate-x-1 transition-transform duration-300"></i>
                      <span class="text-base">{{ isSubmitting ? 'Submitting...' : 'Submit Booking Request' }}</span>
                    </div>
                    
                    <!-- Button shine effect (only when not disabled) -->
                    <div v-if="!isSubmitting" class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -skew-x-12 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-700"></div>
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </main>
    </div>
    <AppFooter />

    <!-- Success Modal -->
    <div v-if="showSuccessModal" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 p-4 backdrop-blur-sm">
      <div class="bg-gradient-to-br from-white via-blue-50 to-blue-100 rounded-2xl shadow-2xl max-w-lg w-full transform transition-all duration-500 scale-100 animate-modal-in border border-blue-200/50 overflow-hidden">
        <!-- Header with animated background -->
        <div class="relative bg-gradient-to-r from-blue-600 via-blue-700 to-blue-800 p-6 text-center overflow-hidden">
          <!-- Animated background particles -->
          <div class="absolute inset-0">
            <div v-for="i in 8" :key="i" 
                 class="absolute w-2 h-2 bg-white/20 rounded-full animate-float"
                 :style="{
                   left: Math.random() * 100 + '%',
                   top: Math.random() * 100 + '%',
                   animationDelay: Math.random() * 3 + 's',
                   animationDuration: (Math.random() * 2 + 3) + 's'
                 }">
            </div>
          </div>
          
          <!-- Success Icon with pulse animation -->
          <div class="relative z-10 w-20 h-20 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center mx-auto mb-4 border-2 border-white/30 animate-pulse-slow">
            <div class="w-16 h-16 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center shadow-lg animate-bounce-slow">
              <i class="fas fa-check text-white text-2xl animate-scale-in"></i>
            </div>
          </div>
          
          <!-- Title -->
          <h3 class="text-2xl font-bold text-white mb-2 animate-fade-in-up">
            <i class="fas fa-calendar-check mr-2 text-blue-200"></i>
            Booking Submitted Successfully!
          </h3>
          <p class="text-blue-100 text-lg animate-fade-in-up-delay">
            Your device booking request has been submitted and is now under review.
          </p>
        </div>

        <!-- Content -->
        <div class="p-6">
          <!-- Booking Details Card -->
          <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-xl p-5 mb-6 border border-blue-200/50 shadow-inner animate-slide-up">
            <div class="flex items-center mb-4">
              <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center mr-3 shadow-md">
                <i class="fas fa-info-circle text-white text-sm"></i>
              </div>
              <h4 class="text-lg font-bold text-blue-800">Booking Details</h4>
            </div>
            
            <div class="space-y-3">
              <!-- Device -->
              <div class="flex items-center justify-between p-3 bg-white/60 rounded-lg border border-blue-200/30">
                <div class="flex items-center">
                  <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-500 rounded-lg flex items-center justify-center mr-3 shadow-sm">
                    <i class="fas fa-laptop text-white text-sm"></i>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-blue-600">Device</p>
                    <p class="text-lg font-bold text-blue-800">{{ getDeviceDisplayName() }}</p>
                  </div>
                </div>
                <div class="w-2 h-2 bg-blue-500 rounded-full animate-ping"></div>
              </div>
              
              <!-- Borrower -->
              <div class="flex items-center justify-between p-3 bg-white/60 rounded-lg border border-blue-200/30">
                <div class="flex items-center">
                  <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-500 rounded-lg flex items-center justify-center mr-3 shadow-sm">
                    <i class="fas fa-user text-white text-sm"></i>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-blue-600">Borrower</p>
                    <p class="text-lg font-bold text-blue-800">{{ formData.borrower_name }}</p>
                  </div>
                </div>
                <div class="w-2 h-2 bg-blue-500 rounded-full animate-ping" style="animation-delay: 0.5s"></div>
              </div>
              
              <!-- Return Date -->
              <div class="flex items-center justify-between p-3 bg-white/60 rounded-lg border border-blue-200/30">
                <div class="flex items-center">
                  <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-500 rounded-lg flex items-center justify-center mr-3 shadow-sm">
                    <i class="fas fa-calendar-alt text-white text-sm"></i>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-blue-600">Return Date</p>
                    <p class="text-lg font-bold text-blue-800">{{ formatDate(formData.return_date) }}</p>
                  </div>
                </div>
                <div class="w-2 h-2 bg-blue-500 rounded-full animate-ping" style="animation-delay: 1s"></div>
              </div>
              
              <!-- Return Time -->
              <div class="flex items-center justify-between p-3 bg-white/60 rounded-lg border border-blue-200/30">
                <div class="flex items-center">
                  <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-500 rounded-lg flex items-center justify-center mr-3 shadow-sm">
                    <i class="fas fa-clock text-white text-sm"></i>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-blue-600">Return Time</p>
                    <p class="text-lg font-bold text-blue-800">{{ formData.return_time }}</p>
                  </div>
                </div>
                <div class="w-2 h-2 bg-blue-500 rounded-full animate-ping" style="animation-delay: 1.5s"></div>
              </div>
            </div>
          </div>
          
          <!-- Status Badge -->
          <div class="flex justify-center mb-6">
            <div class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-yellow-400 to-orange-500 text-white rounded-full text-sm font-bold shadow-lg animate-pulse">
              <i class="fas fa-hourglass-half mr-2 animate-spin-slow"></i>
              Status: Pending Review
            </div>
          </div>
          
          <!-- Action Button -->
          <button
            @click="closeSuccessModal"
            class="group relative w-full bg-gradient-to-r from-blue-600 via-blue-700 to-blue-800 text-white py-4 px-6 rounded-xl font-bold text-lg shadow-xl hover:shadow-blue-500/25 transform hover:scale-105 transition-all duration-300 border border-blue-500/50 overflow-hidden"
          >
            <!-- Button background animation -->
            <div class="absolute inset-0 bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            
            <!-- Button content -->
            <div class="relative z-10 flex items-center justify-center">
              <i class="fas fa-home mr-3 text-xl group-hover:-translate-x-1 transition-transform duration-300"></i>
              <span>Return to Dashboard</span>
            </div>
            
            <!-- Button shine effect -->
            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -skew-x-12 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-700"></div>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue'
import AppHeader from '@/components/header.vue'
import DynamicSidebar from '@/components/DynamicSidebar.vue'
import AppFooter from '@/components/footer.vue'
import bookingService from '@/services/bookingService.js'

export default {
  name: 'BookingService',
  components: {
    AppHeader,
    DynamicSidebar,
    AppFooter
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
        booking_date: '',
        borrower_name: '',
        device_type: '',
        custom_device: '',
        reason: '',
        department: '',
        return_date: '',
        return_time: '',
        phone_number: '',
        signature: null
      },
      validationErrors: {},
      isSubmitting: false,
      showSuccessModal: false,
      uploadProgress: 0,
      signaturePreview: null,
      departments: [],
      deviceTypes: []
    }
  },
  async mounted() {
    console.log('BookingService component mounted')
    console.log('Auth token:', localStorage.getItem('auth_token'))
    await this.loadInitialData()
  },
  methods: {
    async loadInitialData() {
      try {
        console.log('Loading initial data...')
        
        // Load device types and departments from backend
        const [deviceTypesResponse, departmentsResponse] = await Promise.all([
          bookingService.getDeviceTypes(),
          bookingService.getDepartments()
        ])

        console.log('Device types response:', deviceTypesResponse)
        console.log('Departments response:', departmentsResponse)

        if (deviceTypesResponse.success) {
          this.deviceTypes = deviceTypesResponse.data
        } else {
          // Fallback device types if API fails
          this.deviceTypes = [
            { value: 'projector', label: 'Projector' },
            { value: 'tv_remote', label: 'TV Remote' },
            { value: 'hdmi_cable', label: 'HDMI Cable' },
            { value: 'monitor', label: 'Monitor' },
            { value: 'cpu', label: 'CPU' },
            { value: 'keyboard', label: 'Keyboard' },
            { value: 'pc', label: 'PC' },
            { value: 'others', label: 'Others' }
          ]
        }

        if (departmentsResponse.success) {
          this.departments = departmentsResponse.data
        } else {
          // Fallback departments if API fails
          this.departments = [
            { value: 1, label: 'ICT Department' },
            { value: 2, label: 'Administration' },
            { value: 3, label: 'Finance' },
            { value: 4, label: 'Human Resources' }
          ]
        }

        console.log('Final device types:', this.deviceTypes)
        console.log('Final departments:', this.departments)
      } catch (error) {
        console.error('Error loading initial data:', error)
        
        // Set fallback data
        this.deviceTypes = [
          { value: 'projector', label: 'Projector' },
          { value: 'tv_remote', label: 'TV Remote' },
          { value: 'hdmi_cable', label: 'HDMI Cable' },
          { value: 'monitor', label: 'Monitor' },
          { value: 'cpu', label: 'CPU' },
          { value: 'keyboard', label: 'Keyboard' },
          { value: 'pc', label: 'PC' },
          { value: 'others', label: 'Others' }
        ]
        
        this.departments = [
          { value: 1, label: 'ICT Department' },
          { value: 2, label: 'Administration' },
          { value: 3, label: 'Finance' },
          { value: 4, label: 'Human Resources' }
        ]
        
        this.showNotification('Using offline data. Some features may be limited.', 'info')
      }
    },

    handleDeviceTypeChange() {
      if (this.formData.device_type !== 'others') {
        this.formData.custom_device = ''
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
        this.validationErrors.signature = ['Please upload a PNG or JPG image file only']
        return
      }

      // Validate file size (2MB max)
      if (file.size > 2 * 1024 * 1024) {
        this.validationErrors.signature = ['File size must be less than 2MB']
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
      delete this.validationErrors.signature
    },

    removeSignature() {
      this.formData.signature = null
      this.signaturePreview = null
      delete this.validationErrors.signature
      this.uploadProgress = 0
      if (this.$refs.signatureInput) {
        this.$refs.signatureInput.value = ''
      }
    },

    handleSubmitClick(event) {
      console.log('Submit button clicked!')
      console.log('Event:', event)
      console.log('Is submitting:', this.isSubmitting)
      // Don't prevent default - let the form submit naturally
    },

    async submitBooking() {
      console.log('Submit booking called')
      console.log('Form data:', this.formData)
      
      this.isSubmitting = true
      this.validationErrors = {}

      try {
        // Simple validation check
        if (!this.formData.booking_date) {
          this.showNotification('Please select a booking date', 'error')
          return
        }
        if (!this.formData.borrower_name) {
          this.showNotification('Please enter borrower name', 'error')
          return
        }
        if (!this.formData.device_type) {
          this.showNotification('Please select device type', 'error')
          return
        }
        if (!this.formData.department) {
          this.showNotification('Please select department', 'error')
          return
        }
        if (!this.formData.phone_number) {
          this.showNotification('Please enter phone number', 'error')
          return
        }
        if (!this.formData.return_date) {
          this.showNotification('Please select return date', 'error')
          return
        }
        if (!this.formData.return_time) {
          this.showNotification('Please select return time', 'error')
          return
        }
        if (!this.formData.reason) {
          this.showNotification('Please enter reason for borrowing', 'error')
          return
        }
        if (!this.formData.signature) {
          this.showNotification('Please upload your signature', 'error')
          return
        }

        console.log('All validation passed, calling API...')
        
        // Prepare data for submission
        const submissionData = {
          ...this.formData,
          // Ensure department is an integer
          department: parseInt(this.formData.department)
        }
        
        console.log('Prepared submission data:', submissionData)
        console.log('Calling bookingService.submitBooking...')
        const response = await bookingService.submitBooking(submissionData)
        
        console.log('Response received:', response)
        
        if (response.success) {
          console.log('Booking submitted successfully:', response.data)
          this.showSuccessModal = true
          this.showNotification(response.message, 'success')
        } else {
          console.log('Booking submission failed:', response)
          if (response.type === 'validation') {
            this.validationErrors = response.errors
            console.log('Validation errors:', response.errors)
            
            // Show specific validation errors
            const errorMessages = []
            Object.keys(response.errors).forEach(field => {
              if (response.errors[field] && response.errors[field].length > 0) {
                errorMessages.push(`${field}: ${response.errors[field][0]}`)
              }
            })
            
            if (errorMessages.length > 0) {
              this.showNotification(`Validation errors: ${errorMessages.join(', ')}`, 'error')
            } else {
              this.showNotification('Please check the form for errors', 'error')
            }
          } else {
            this.showNotification(response.message, 'error')
          }
        }
      } catch (error) {
        console.error('Error submitting booking:', error)
        this.showNotification('Error submitting booking. Please try again.', 'error')
      } finally {
        this.isSubmitting = false
      }
    },

    getDeviceDisplayName() {
      if (this.formData.device_type === 'others') {
        return this.formData.custom_device || 'Other Device'
      }
      
      const deviceType = this.deviceTypes.find(dt => dt.value === this.formData.device_type)
      return deviceType ? deviceType.label : this.formData.device_type
    },

    formatDate(dateString) {
      if (!dateString) return ''
      
      const date = new Date(dateString)
      const options = { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
      }
      
      return date.toLocaleDateString('en-US', options)
    },

    closeSuccessModal() {
      this.showSuccessModal = false
      this.resetForm()
      this.goBack()
    },

    resetForm() {
      this.formData = {
        booking_date: '',
        borrower_name: '',
        device_type: '',
        custom_device: '',
        reason: '',
        department: '',
        return_date: '',
        return_time: '',
        phone_number: '',
        signature: null
      }
      this.validationErrors = {}
      this.removeSignature()
    },

    goBack() {
      this.$router.push('/user-dashboard')
    },

    showNotification(message, type = 'info') {
      // Simple notification - you can replace with a proper notification system
      const colors = {
        success: 'green',
        error: 'red',
        info: 'blue'
      }
      
      // Create a simple toast notification
      const toast = document.createElement('div')
      toast.className = `fixed top-4 right-4 px-6 py-3 rounded-lg text-white font-semibold z-50 bg-${colors[type]}-600 transform transition-all duration-300`
      toast.textContent = message
      document.body.appendChild(toast)
      
      // Animate in
      setTimeout(() => {
        toast.style.transform = 'translateX(0)'
      }, 100)
      
      // Remove after 3 seconds
      setTimeout(() => {
        toast.style.transform = 'translateX(100%)'
        setTimeout(() => {
          if (document.body.contains(toast)) {
            document.body.removeChild(toast)
          }
        }, 300)
      }, 3000)
    }
  }
}
</script>

<style scoped>
/* Medical Glass morphism effects */
.booking-glass-card {
  background: rgba(59, 130, 246, 0.15);
  backdrop-filter: blur(25px);
  -webkit-backdrop-filter: blur(25px);
  border: 2px solid rgba(96, 165, 250, 0.3);
  box-shadow: 0 8px 32px rgba(29, 78, 216, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.1);
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

/* Animations */
@keyframes float {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-15px); }
}

@keyframes fade-in {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes fade-in-delay {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes slide-up {
  from { opacity: 0; transform: translateY(30px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes slide-down {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes modal-in {
  from { opacity: 0; transform: scale(0.9) translateY(-20px); }
  to { opacity: 1; transform: scale(1) translateY(0); }
}

@keyframes fade-in-up {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes fade-in-up-delay {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes scale-in {
  from { transform: scale(0); }
  to { transform: scale(1); }
}

@keyframes bounce-slow {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-10px); }
}

@keyframes pulse-slow {
  0%, 100% { transform: scale(1); opacity: 1; }
  50% { transform: scale(1.05); opacity: 0.8; }
}

@keyframes spin-slow {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
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
  animation: modal-in 0.5s ease-out;
}

.animate-fade-in-up {
  animation: fade-in-up 0.6s ease-out;
}

.animate-fade-in-up-delay {
  animation: fade-in-up-delay 0.6s ease-out 0.2s both;
}

.animate-scale-in {
  animation: scale-in 0.5s ease-out 0.3s both;
}

.animate-bounce-slow {
  animation: bounce-slow 2s ease-in-out infinite;
}

.animate-pulse-slow {
  animation: pulse-slow 3s ease-in-out infinite;
}

.animate-spin-slow {
  animation: spin-slow 3s linear infinite;
}

/* Focus styles for accessibility */
input:focus, select:focus, textarea:focus {
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

button:focus {
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
}

/* Loading spinner animation */
@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

.fa-spin {
  animation: spin 1s linear infinite;
}

/* Smooth transitions */
* {
  transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 200ms;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .grid-cols-1.md\:grid-cols-2 {
    grid-template-columns: repeat(1, minmax(0, 1fr));
  }
  
  .p-6 {
    padding: 1rem;
  }
  
  .text-2xl {
    font-size: 1.25rem;
  }
  
  .text-lg {
    font-size: 1rem;
  }
}
</style>