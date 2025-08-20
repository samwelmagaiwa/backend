<template>
  <div class="flex flex-col h-screen">
    <AppHeader />
    <div class="flex flex-1 overflow-hidden">
      <DynamicSidebar v-model:collapsed="sidebarCollapsed" />
      <main class="flex-1 p-6 bg-gradient-to-br from-blue-900 via-blue-800 to-teal-900 overflow-y-auto relative">
        <!-- Medical Background Pattern -->
        <div class="absolute inset-0 overflow-hidden">
          <!-- Medical Cross Pattern -->
          <div class="absolute inset-0 opacity-5">
            <div class="grid grid-cols-12 gap-8 h-full transform rotate-45">
              <div v-for="i in 48" :key="i" class="bg-white rounded-full w-2 h-2 animate-pulse" :style="{animationDelay: (i * 0.1) + 's'}"></div>
            </div>
          </div>
          <!-- Floating medical icons -->
          <div class="absolute inset-0">
            <div v-for="i in 15" :key="i" 
                 class="absolute text-white opacity-10 animate-float"
                 :style="{
                   left: Math.random() * 100 + '%',
                   top: Math.random() * 100 + '%',
                   animationDelay: Math.random() * 3 + 's',
                   animationDuration: (Math.random() * 3 + 2) + 's',
                   fontSize: (Math.random() * 20 + 10) + 'px'
                 }">
              <i :class="['fas', ['fa-heartbeat', 'fa-user-md', 'fa-hospital', 'fa-stethoscope', 'fa-plus'][Math.floor(Math.random() * 5)]]"></i>
            </div>
          </div>
        </div>
        
        <div class="max-w-12xl mx-auto relative z-10">
          <!-- Header Section -->
          <div class="medical-glass-card rounded-t-3xl p-6 mb-0 border-b border-blue-300/30">
            <div class="flex justify-between items-center">
              <!-- Left Logo -->
              <div class="w-28 h-28 mr-6 transform hover:scale-110 transition-transform duration-300">
                <div class="w-full h-full bg-gradient-to-br from-blue-500/20 to-teal-500/20 rounded-2xl backdrop-blur-sm border-2 border-blue-300/40 flex items-center justify-center shadow-2xl hover:shadow-blue-500/25">
                  <img src="/assets/images/ngao2.png" alt="National Shield" class="max-w-18 max-h-18 object-contain" />
                </div>
              </div>

              <!-- Center Content -->
              <div class="text-center flex-1">
                <h1 class="text-4xl font-bold text-white mb-4 tracking-wide drop-shadow-lg animate-fade-in">
                  MUHIMBILI NATIONAL HOSPITAL
                </h1>
                <div class="relative inline-block mb-4">
                  <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-10 py-4 rounded-full text-xl font-bold shadow-2xl transform hover:scale-105 transition-all duration-300 border-2 border-blue-400/60">
                    <span class="relative z-10 flex items-center gap-2">
                      <i class="fas fa-wifi"></i>
                      INTERNET ACCESS REQUEST
                    </span>
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-700 to-blue-800 rounded-full opacity-0 hover:opacity-100 transition-opacity duration-300"></div>
                  </div>
                </div>
                <h2 class="text-2xl font-bold text-blue-100 tracking-wide drop-shadow-md animate-fade-in-delay">
                  NETWORK CONNECTIVITY SERVICES
                </h2>
              </div>
              
              <!-- Right Logo -->
              <div class="w-28 h-28 ml-6 transform hover:scale-110 transition-transform duration-300">
                <div class="w-full h-full bg-gradient-to-br from-teal-500/20 to-blue-500/20 rounded-2xl backdrop-blur-sm border-2 border-teal-300/40 flex items-center justify-center shadow-2xl hover:shadow-teal-500/25">
                  <img src="/assets/images/logo2.png" alt="Muhimbili Logo" class="max-w-18 max-h-18 object-contain" />
                </div>
              </div>
            </div>
          </div>

          <!-- Main Form -->
          <div class="medical-glass-card rounded-b-3xl overflow-hidden">
            <form @submit.prevent="submitForm" class="p-8 space-y-8">
              
              <!-- Personal Information Section -->
              <div class="medical-card bg-gradient-to-r from-teal-600/25 to-blue-600/25 border-2 border-teal-400/40 p-8 rounded-2xl backdrop-blur-sm hover:shadow-2xl hover:shadow-teal-500/20 transition-all duration-500 group">
                <div class="flex items-center space-x-4 mb-6">
                  <div class="w-14 h-14 bg-gradient-to-br from-teal-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-teal-300/50">
                    <i class="fas fa-user-md text-white text-xl"></i>
                  </div>
                  <h3 class="text-xl font-bold text-white flex items-center">
                    <i class="fas fa-id-card mr-2 text-teal-300"></i>
                    Personal Information
                  </h3>
                </div>

                <!-- Attractive Field Layout -->
                <div class="space-y-6">
                  <!-- Row 1: Basic Information -->
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <!-- PF Number -->
                    <div class="group">
                      <label class="block text-sm font-bold text-blue-100 mb-3 flex items-center">
                        <i class="fas fa-id-badge mr-2 text-cyan-300"></i>
                        PF Number <span class="text-red-400 ml-1">*</span>
                      </label>
                      <div class="relative">
                        <input 
                          v-model="formData.pfNumber" 
                          @input="validatePfNumber"
                          @blur="validatePfNumber"
                          type="text" 
                          class="medical-input w-full px-4 py-4 bg-white/15 border-2 rounded-xl focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-cyan-500/20 group-hover:border-cyan-400/50"
                          :class="{
                            'border-blue-300/30 focus:border-cyan-400': !errors.pfNumber && !validationState.pfNumber.isValid,
                            'border-green-400 focus:border-green-500': validationState.pfNumber.isValid && formData.pfNumber,
                            'border-red-400 focus:border-red-500': errors.pfNumber
                          }"
                          placeholder="Enter PF Number (e.g., PF123, ABC/2024)"
                          required
                        />
                        <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-cyan-500/10 to-blue-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
                        <div class="absolute right-3 top-1/2 transform -translate-y-1/2 text-cyan-300/50">
                          <i v-if="validationState.pfNumber.isValid && formData.pfNumber" class="fas fa-check text-green-400"></i>
                          <i v-else-if="errors.pfNumber" class="fas fa-times text-red-400"></i>
                          <i v-else class="fas fa-hashtag"></i>
                        </div>
                      </div>
                      <div v-if="errors.pfNumber" class="text-red-400 text-xs mt-1 flex items-center animate-fade-in">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        {{ errors.pfNumber }}
                      </div>
                      <div v-else-if="validationState.pfNumber.isValid && formData.pfNumber" class="text-green-400 text-xs mt-1 flex items-center animate-fade-in">
                        <i class="fas fa-check-circle mr-1"></i>
                        Valid PF Number format
                      </div>
                      <div v-else-if="formData.pfNumber && !validationState.pfNumber.isValid" class="text-yellow-400 text-xs mt-1 flex items-center animate-fade-in">
                        <i class="fas fa-info-circle mr-1"></i>
                        {{ validationState.pfNumber.message || 'Enter a valid PF Number' }}
                      </div>
                      <p v-else class="text-xs text-cyan-200/60 mt-1 italic flex items-center">
                        <i class="fas fa-info-circle mr-1"></i>
                        e.g. PF123, ABC/2024
                      </p>
                    </div>

                    <!-- Staff Name -->
                    <div class="group">
                      <label class="block text-sm font-bold text-blue-100 mb-3 flex items-center">
                        <i class="fas fa-user mr-2 text-cyan-300"></i>
                        Staff Name <span class="text-red-400 ml-1">*</span>
                      </label>
                      <div class="relative">
                        <input 
                          v-model="formData.staffName" 
                          @input="validateStaffName"
                          @blur="validateStaffName"
                          type="text" 
                          class="medical-input w-full px-4 py-4 bg-white/15 border-2 rounded-xl focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-cyan-500/20 group-hover:border-cyan-400/50"
                          :class="{
                            'border-blue-300/30 focus:border-cyan-400': !errors.staffName && !validationState.staffName.isValid,
                            'border-green-400 focus:border-green-500': validationState.staffName.isValid && formData.staffName,
                            'border-red-400 focus:border-red-500': errors.staffName
                          }"
                          placeholder="Enter full name (letters, spaces, dots, hyphens only)"
                          required
                        />
                        <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-cyan-500/10 to-blue-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
                        <div class="absolute right-3 top-1/2 transform -translate-y-1/2 text-cyan-300/50">
                          <i v-if="validationState.staffName.isValid && formData.staffName" class="fas fa-check text-green-400"></i>
                          <i v-else-if="errors.staffName" class="fas fa-times text-red-400"></i>
                          <i v-else class="fas fa-user-circle"></i>
                        </div>
                      </div>
                      <div v-if="errors.staffName" class="text-red-400 text-xs mt-1 flex items-center animate-fade-in">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        {{ errors.staffName }}
                      </div>
                      <div v-else-if="validationState.staffName.isValid && formData.staffName" class="text-green-400 text-xs mt-1 flex items-center animate-fade-in">
                        <i class="fas fa-check-circle mr-1"></i>
                        Valid staff name format
                      </div>
                      <div v-else-if="formData.staffName && !validationState.staffName.isValid" class="text-yellow-400 text-xs mt-1 flex items-center animate-fade-in">
                        <i class="fas fa-info-circle mr-1"></i>
                        {{ validationState.staffName.message || 'Enter a valid staff name' }}
                      </div>
                      <p v-else class="text-xs text-cyan-200/60 mt-1 italic flex items-center">
                        <i class="fas fa-info-circle mr-1"></i>
                        e.g. Jane Doe
                      </p>
                    </div>
                  </div>

                  <!-- Row 2: Contact & Department -->
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <!-- Phone Number -->
                    <div class="group">
                      <label class="block text-sm font-bold text-blue-100 mb-3 flex items-center">
                        <i class="fas fa-phone mr-2 text-cyan-300"></i>
                        Phone Number <span class="text-red-400 ml-1">*</span>
                      </label>
                      <div class="relative">
                        <input 
                          v-model="formData.phoneNumber" 
                          @input="validatePhoneNumber"
                          @blur="validatePhoneNumber"
                          type="tel" 
                          class="medical-input w-full px-4 py-4 bg-white/15 border-2 rounded-xl focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-cyan-500/20 group-hover:border-cyan-400/50"
                          :class="{
                            'border-blue-300/30 focus:border-cyan-400': !errors.phoneNumber && !validationState.phoneNumber.isValid,
                            'border-green-400 focus:border-green-500': validationState.phoneNumber.isValid && formData.phoneNumber,
                            'border-red-400 focus:border-red-500': errors.phoneNumber
                          }"
                          placeholder="Enter phone number (+255123456789 or 0123456789)"
                          required
                        />
                        <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-cyan-500/10 to-blue-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
                        <div class="absolute right-3 top-1/2 transform -translate-y-1/2 text-cyan-300/50">
                          <i v-if="validationState.phoneNumber.isValid && formData.phoneNumber" class="fas fa-check text-green-400"></i>
                          <i v-else-if="errors.phoneNumber" class="fas fa-times text-red-400"></i>
                          <i v-else class="fas fa-mobile-alt"></i>
                        </div>
                      </div>
                      <div v-if="errors.phoneNumber" class="text-red-400 text-xs mt-1 flex items-center animate-fade-in">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        {{ errors.phoneNumber }}
                      </div>
                      <div v-else-if="validationState.phoneNumber.isValid && formData.phoneNumber" class="text-green-400 text-xs mt-1 flex items-center animate-fade-in">
                        <i class="fas fa-check-circle mr-1"></i>
                        Valid phone number format
                      </div>
                      <div v-else-if="formData.phoneNumber && !validationState.phoneNumber.isValid" class="text-yellow-400 text-xs mt-1 flex items-center animate-fade-in">
                        <i class="fas fa-info-circle mr-1"></i>
                        {{ validationState.phoneNumber.message || 'Enter a valid phone number' }}
                      </div>
                      <p v-else class="text-xs text-cyan-200/60 mt-1 italic flex items-center">
                        <i class="fas fa-info-circle mr-1"></i>
                        e.g. +255712000000 or 0712000000
                      </p>
                    </div>

                    <!-- Department -->
                    <div class="group">
                      <label class="block text-sm font-bold text-blue-100 mb-3 flex items-center">
                        <i class="fas fa-building mr-2 text-cyan-300"></i>
                        Department <span class="text-red-400 ml-1">*</span>
                      </label>
                      <div class="relative">
                        <select 
                          v-model="formData.department" 
                          @change="validateDepartment"
                          @blur="validateDepartment"
                          class="w-full px-4 py-4 border-2 rounded-xl focus:outline-none text-white bg-blue-100/20 focus:bg-blue-100/30 transition-all backdrop-blur-sm group-hover:border-cyan-400/50 appearance-none cursor-pointer"
                          :class="{
                            'border-blue-300/30 focus:border-cyan-400': !errors.department && !validationState.department.isValid,
                            'border-green-400 focus:border-green-500': validationState.department.isValid && formData.department,
                            'border-red-400 focus:border-red-500': errors.department
                          }"
                          required
                        >
                          <option value="" class="bg-blue-800 text-blue-300">
                            {{ isLoadingDepartments ? 'Loading departments...' : 'Select Department' }}
                          </option>
                          <option v-for="dept in departments" :key="dept.id" :value="dept.id" class="bg-blue-800 text-white">{{ dept.name }}</option>
                        </select>
                        <div class="absolute right-3 top-1/2 transform -translate-y-1/2 text-cyan-300/50 pointer-events-none">
                          <i v-if="validationState.department.isValid && formData.department" class="fas fa-check text-green-400"></i>
                          <i v-else-if="errors.department" class="fas fa-times text-red-400"></i>
                          <i v-else class="fas fa-chevron-down"></i>
                        </div>
                      </div>
                      <div v-if="errors.department" class="text-red-500 text-xs mt-1 flex items-center animate-fade-in">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        {{ errors.department }}
                      </div>
                      <div v-else-if="validationState.department.isValid && formData.department" class="text-green-400 text-xs mt-1 flex items-center animate-fade-in">
                        <i class="fas fa-check-circle mr-1"></i>
                        Department selected
                      </div>
                      <p v-else class="text-xs text-cyan-200/60 mt-1 italic flex items-center">
                        <i class="fas fa-info-circle mr-1"></i>
                        Select your department
                      </p>
                    </div>
                  </div>

                  <!-- Row 3: Digital Signature (Full Width) -->
                  <div class="bg-gradient-to-r from-cyan-500/10 to-blue-500/10 p-6 rounded-2xl border border-cyan-300/20 backdrop-blur-sm">
                    <div class="group">
                      <label class="block text-sm font-bold text-blue-100 mb-4 flex items-center">
                        <i class="fas fa-signature mr-2 text-cyan-300"></i>
                        Digital Signature <span class="text-red-400 ml-1">*</span>
                        <span class="ml-2 text-xs text-cyan-300/70 font-normal">(PNG, JPG, JPEG)</span>
                      </label>
                      
                      <div class="flex flex-col md:flex-row gap-4 items-start">
                        <!-- Signature Display Box -->
                        <div class="relative w-full md:w-80 h-20 border-2 border-blue-300/30 rounded-xl bg-blue-100/20 focus-within:bg-blue-100/30 focus-within:border-cyan-400 overflow-hidden backdrop-blur-sm group-hover:border-cyan-400/50 transition-all duration-300">
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
                              <i class="fas fa-signature text-cyan-400/50 text-2xl mb-1"></i>
                              <p class="text-xs text-blue-400 italic">Signature will appear here</p>
                            </div>
                          </div>
                          
                          <!-- Remove Button (when signature exists) -->
                          <button
                            v-if="signaturePreview"
                            type="button"
                            @click="removeFile"
                            class="absolute top-2 right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600 transition duration-200 text-xs shadow-lg"
                            title="Remove signature"
                          >
                            <i class="fas fa-times"></i>
                          </button>
                        </div>
                        
                        <!-- Upload Button -->
                        <div class="flex flex-col gap-2">
                          <input 
                            ref="fileInput"
                            type="file" 
                            accept=".png,.jpg,.jpeg"
                            @change="handleFileUpload"
                            class="hidden"
                          />
                          
                          <button
                            type="button"
                            @click="$refs.fileInput.click()"
                            :disabled="uploadProgress > 0 && uploadProgress < 100"
                            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-cyan-500 to-blue-600 text-white text-sm font-medium rounded-xl hover:from-cyan-600 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-1 transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg hover:shadow-xl transform hover:scale-105 border border-cyan-400/30"
                          >
                            <span v-if="uploadProgress === 0" class="flex items-center">
                              <i class="fas fa-upload mr-2"></i>
                              Press to load your signature 
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
                      
                      <div v-if="errors.signature" class="text-red-500 text-xs mt-3 flex items-center">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        {{ errors.signature }}
                      </div>
                      
                      <!-- General error message -->
                      <div v-if="errors.general" class="text-red-500 text-sm mt-3 p-3 bg-red-100/10 border border-red-400/30 rounded-lg flex items-center">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        {{ errors.general }}
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Internet Purpose Section -->
              <div class="medical-card bg-gradient-to-r from-blue-600/25 to-cyan-600/25 border-2 border-blue-400/40 p-8 rounded-2xl backdrop-blur-sm hover:shadow-2xl hover:shadow-blue-500/20 transition-all duration-500 group">
                <div class="flex items-center space-x-4 mb-6">
                  <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-blue-300/50">
                    <i class="fas fa-wifi text-white text-xl"></i>
                  </div>
                  <h3 class="text-xl font-bold text-white flex items-center">
                    <i class="fas fa-globe mr-2 text-blue-300"></i>
                    Internet Access Purpose
                  </h3>
                </div>

                <div class="space-y-4">
                  <div v-for="(purpose, index) in formData.internetPurposes" :key="index" class="flex items-center gap-3">
                    <div class="flex items-center justify-center w-8 h-8 bg-blue-100/20 text-blue-300 rounded-full font-bold text-sm">
                      {{ index + 1 }}.
                    </div>
                    <input 
                      v-model="formData.internetPurposes[index]" 
                      @input="validatePurpose(index)"
                      @blur="validatePurpose(index)"
                      type="text" 
                      class="medical-input flex-1 px-4 py-3 bg-white/15 border-2 rounded-xl focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-blue-500/20"
                      :class="{
                        'border-blue-300/30 focus:border-blue-400': !errors[`purpose${index}`] && !validationState.purposes[index]?.isValid,
                        'border-green-400 focus:border-green-500': validationState.purposes[index]?.isValid && formData.internetPurposes[index],
                        'border-red-400 focus:border-red-500': errors[`purpose${index}`]
                      }"
                      :placeholder="`Purpose ${index + 1} ${index === 0 ? '(Required)' : '(Optional)'}`"
                      :required="index === 0"
                    />
                  </div>
                  
                  <!-- Purpose validation messages -->
                  <div v-for="(purpose, index) in formData.internetPurposes" :key="`error-${index}`" class="mt-1">
                    <div v-if="errors[`purpose${index}`]" class="text-red-400 text-xs flex items-center animate-fade-in">
                      <i class="fas fa-exclamation-circle mr-1"></i>
                      {{ errors[`purpose${index}`] }}
                    </div>
                    <div v-else-if="validationState.purposes[index]?.isValid && formData.internetPurposes[index]" class="text-green-400 text-xs flex items-center animate-fade-in">
                      <i class="fas fa-check-circle mr-1"></i>
                      Purpose {{ index + 1 }} is valid
                    </div>
                    <div v-else-if="formData.internetPurposes[index] && !validationState.purposes[index]?.isValid" class="text-yellow-400 text-xs flex items-center animate-fade-in">
                      <i class="fas fa-info-circle mr-1"></i>
                      {{ validationState.purposes[index]?.message || `Purpose ${index + 1} needs more detail` }}
                    </div>
                  </div>
                  
                  <p class="text-xs text-blue-200 mt-2 italic">
                    <i class="fas fa-info-circle mr-1"></i>
                    Please provide at least one purpose for internet access. You can add up to 4 purposes.
                  </p>
                </div>
              </div>

              <!-- Footer & Submit -->
              <div class="border-t-3 border-gray-200 pt-1">
                <div class="text-center mb-6">
                  <div class="inline-block text-white px-4 py-2 rounded-lg">
                    <p class="font-bold">Directorate of ICT</p>
                    <p class="text-sm opacity-90">Network & Internet Services</p>
                  </div>
                </div>

                <div class="flex justify-between items-center">
                  <button type="button" @click="goBack"
                    class="px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-all duration-300 font-semibold flex items-center shadow-lg hover:shadow-xl"
                  >
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Dashboard
                  </button>
                  <button 
                    type="submit" 
                    :disabled="isSubmitting"
                    class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-300 font-semibold flex items-center shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    <i v-if="!isSubmitting" class="fas fa-paper-plane mr-2"></i>
                    <i v-else class="fas fa-spinner fa-spin mr-2"></i>
                    {{ isSubmitting ? 'Submitting...' : 'Submit Request' }}
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </main>
    </div>
    <AppFooter />

    <!-- Enhanced Success Modal -->
    <transition
      enter-active-class="transition ease-out duration-500"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition ease-in duration-300"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="showSuccessModal" class="fixed inset-0 z-50 overflow-y-auto">
        <!-- Multi-layer backdrop -->
        <div class="fixed inset-0 bg-gradient-to-br from-blue-900/80 via-blue-800/70 to-blue-700/60 backdrop-blur-md"></div>
        <div class="fixed inset-0 bg-black/30"></div>
        
        <!-- Modal container -->
        <div class="flex min-h-full items-center justify-center p-4">
          <transition
            enter-active-class="transition ease-out duration-500 delay-150"
            enter-from-class="opacity-0 transform scale-75 translate-y-8"
            enter-to-class="opacity-100 transform scale-100 translate-y-0"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="opacity-100 transform scale-100 translate-y-0"
            leave-to-class="opacity-0 transform scale-75 translate-y-8"
          >
            <div v-if="showSuccessModal" class="relative w-full max-w-md">
              <!-- Outer glow layer -->
              <div class="absolute inset-0 bg-gradient-to-r from-blue-400/20 to-blue-600/20 rounded-3xl blur-xl scale-110"></div>
              
              <!-- Main modal card -->
              <div class="relative bg-gradient-to-br from-blue-50 via-white to-blue-50 rounded-3xl shadow-2xl border-2 border-blue-200/50 overflow-hidden">
                <!-- Animated background pattern -->
                <div class="absolute inset-0 opacity-5">
                  <div class="absolute inset-0 bg-gradient-to-br from-blue-600/20 to-blue-800/20"></div>
                  <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-blue-400 to-transparent animate-pulse"></div>
                </div>
                
                <!-- Header section -->
                <div class="relative bg-gradient-to-r from-blue-600 via-blue-700 to-blue-800 px-8 py-6">
                  <!-- Floating particles effect -->
                  <div class="absolute inset-0 overflow-hidden">
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
                  
                  <!-- Success icon with animation -->
                  <div class="relative z-10 text-center">
                    <div class="mx-auto w-20 h-20 bg-gradient-to-br from-blue-400 via-blue-500 to-blue-600 rounded-full flex items-center justify-center mb-4 shadow-2xl border-4 border-white/30 relative overflow-hidden">
                      <div class="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent rounded-full"></div>
                      <i class="fas fa-check text-white text-3xl drop-shadow-lg relative z-10 animate-bounce"></i>
                      
                      <!-- Success ripple effect -->
                      <div class="absolute inset-0 rounded-full border-4 border-white/50 animate-ping"></div>
                    </div>
                    
                    <h3 class="text-2xl font-bold text-white mb-2 drop-shadow-lg">
                      Request Submitted Successfully!
                    </h3>
                  </div>
                </div>
                
                <!-- Content section -->
                <div class="relative px-8 py-8">
                  <!-- Success message -->
                  <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-blue-100 to-blue-200 rounded-2xl mb-6 shadow-lg border border-blue-300/50">
                      <i class="fas fa-wifi text-blue-600 text-2xl"></i>
                    </div>
                    
                    <p class="text-lg text-blue-900 font-semibold mb-3 leading-relaxed">
                      Your Internet access request has been submitted and is now under review.
                    </p>
                    
                    <!-- Status indicators -->
                    <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-2xl p-6 border border-blue-200/50 shadow-inner">
                      <div class="flex items-center justify-center space-x-3 mb-4">
                        <div class="flex items-center space-x-2">
                          <div class="w-3 h-3 bg-blue-500 rounded-full animate-pulse"></div>
                          <span class="text-sm font-medium text-blue-700">Processing</span>
                        </div>
                        <div class="w-8 h-0.5 bg-blue-300 rounded-full"></div>
                        <div class="flex items-center space-x-2">
                          <div class="w-3 h-3 bg-blue-300 rounded-full"></div>
                          <span class="text-sm text-blue-600">Review</span>
                        </div>
                        <div class="w-8 h-0.5 bg-blue-200 rounded-full"></div>
                        <div class="flex items-center space-x-2">
                          <div class="w-3 h-3 bg-blue-200 rounded-full"></div>
                          <span class="text-sm text-blue-500">Approval</span>
                        </div>
                      </div>
                      
                      <div class="text-center">
                        <p class="text-sm text-blue-600 font-medium">
                          <i class="fas fa-clock mr-2"></i>
                          Estimated processing time: 2-3 business days
                        </p>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Action button -->
                  <div class="text-center">
                    <button
                      @click="closeSuccessModal"
                      class="group relative inline-flex items-center justify-center w-full px-8 py-4 bg-gradient-to-r from-blue-600 via-blue-700 to-blue-800 text-white font-bold rounded-2xl shadow-2xl hover:shadow-blue-500/25 transform hover:scale-105 transition-all duration-300 border border-blue-500/50 overflow-hidden"
                    >
                      <!-- Button background animation -->
                      <div class="absolute inset-0 bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                      <div class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                      
                      <!-- Button content -->
                      <div class="relative z-10 flex items-center">
                        <i class="fas fa-arrow-right mr-3 text-xl group-hover:translate-x-1 transition-transform duration-300"></i>
                        <span class="text-lg">Continue to Dashboard</span>
                      </div>
                      
                      <!-- Button shine effect -->
                      <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -skew-x-12 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-700"></div>
                    </button>
                  </div>
                  
                  <!-- Additional info -->
                  <div class="mt-6 text-center">
                    <p class="text-xs text-blue-500 flex items-center justify-center">
                      <i class="fas fa-shield-alt mr-2"></i>
                      Your request has been securely submitted and logged
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </transition>
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
import { ref } from 'vue'
import AppHeader from '@/components/header.vue'
import DynamicSidebar from '@/components/DynamicSidebar.vue'
import AppFooter from '@/components/footer.vue'
import { userInternetAccessFormService } from '@/services/userInternetAccessFormService.js'

export default {
  name: 'UserInternetForm',
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
        pfNumber: '',
        staffName: '',
        phoneNumber: '',
        signature: null,
        department: '',
        internetPurposes: ['', '', '', '']
      },
      errors: {
        pfNumber: '',
        staffName: '',
        phoneNumber: '',
        department: '',
        signature: '',
        purpose0: '',
        purpose1: '',
        purpose2: '',
        purpose3: '',
        general: ''
      },
      isSubmitting: false,
      showSuccessModal: false,
      uploadProgress: 0,
      signaturePreview: null,
      signatureFileName: '',
      
      // Real-time validation state
      validationState: {
        pfNumber: {
          isValid: false,
          message: ''
        },
        staffName: {
          isValid: false,
          message: ''
        },
        phoneNumber: {
          isValid: false,
          message: ''
        },
        department: {
          isValid: false,
          message: ''
        },
        purposes: [
          { isValid: false, message: '' },
          { isValid: false, message: '' },
          { isValid: false, message: '' },
          { isValid: false, message: '' }
        ]
      },
      departments: [],  // Will be loaded from backend
      isLoadingDepartments: true
    }
  },
  
  async mounted() {
    await this.loadDepartments()
  },
  
  methods: {
    // Real-time validation methods
    validatePfNumber() {
      const pfNumber = this.formData.pfNumber?.trim()
      
      // Clear previous errors
      this.errors.pfNumber = ''
      
      if (!pfNumber) {
        this.validationState.pfNumber = {
          isValid: false,
          message: 'PF Number is required'
        }
        return
      }
      
      if (pfNumber.length < 3) {
        this.validationState.pfNumber = {
          isValid: false,
          message: 'PF Number must be at least 3 characters'
        }
        return
      }
      
      if (pfNumber.length > 50) {
        this.validationState.pfNumber = {
          isValid: false,
          message: 'PF Number cannot exceed 50 characters'
        }
        return
      }
      
      // Check format: alphanumeric, hyphens, and slashes
      const pfRegex = /^[A-Za-z0-9\-\/]+$/
      if (!pfRegex.test(pfNumber)) {
        this.validationState.pfNumber = {
          isValid: false,
          message: 'Only letters, numbers, hyphens, and slashes allowed'
        }
        return
      }
      
      // Valid PF Number
      this.validationState.pfNumber = {
        isValid: true,
        message: ''
      }
    },
    
    validateStaffName() {
      const staffName = this.formData.staffName?.trim()
      
      // Clear previous errors
      this.errors.staffName = ''
      
      if (!staffName) {
        this.validationState.staffName = {
          isValid: false,
          message: 'Staff name is required'
        }
        return
      }
      
      if (staffName.length < 2) {
        this.validationState.staffName = {
          isValid: false,
          message: 'Staff name must be at least 2 characters'
        }
        return
      }
      
      if (staffName.length > 255) {
        this.validationState.staffName = {
          isValid: false,
          message: 'Staff name cannot exceed 255 characters'
        }
        return
      }
      
      // Check format: letters, spaces, dots, hyphens, apostrophes
      const nameRegex = /^[a-zA-Z\s\.\-\']+$/
      if (!nameRegex.test(staffName)) {
        this.validationState.staffName = {
          isValid: false,
          message: 'Only letters, spaces, dots, hyphens, and apostrophes allowed'
        }
        return
      }
      
      // Valid staff name
      this.validationState.staffName = {
        isValid: true,
        message: ''
      }
    },
    
    validatePhoneNumber() {
      const phoneNumber = this.formData.phoneNumber?.trim()
      
      // Clear previous errors
      this.errors.phoneNumber = ''
      
      if (!phoneNumber) {
        this.validationState.phoneNumber = {
          isValid: false,
          message: 'Phone number is required'
        }
        return
      }
      
      if (phoneNumber.length < 10) {
        this.validationState.phoneNumber = {
          isValid: false,
          message: 'Phone number must be at least 10 digits'
        }
        return
      }
      
      if (phoneNumber.length > 20) {
        this.validationState.phoneNumber = {
          isValid: false,
          message: 'Phone number cannot exceed 20 characters'
        }
        return
      }
      
      // Check format: international format with numbers, spaces, hyphens, parentheses
      const phoneRegex = /^[\+]?[0-9\s\-\(\)]+$/
      if (!phoneRegex.test(phoneNumber)) {
        this.validationState.phoneNumber = {
          isValid: false,
          message: 'Invalid phone number format'
        }
        return
      }
      
      // Count actual digits
      const digitCount = phoneNumber.replace(/[^0-9]/g, '').length
      if (digitCount < 9) {
        this.validationState.phoneNumber = {
          isValid: false,
          message: 'Phone number must contain at least 9 digits'
        }
        return
      }
      
      // Valid phone number
      this.validationState.phoneNumber = {
        isValid: true,
        message: ''
      }
    },
    
    validateDepartment() {
      const departmentId = this.formData.department
      
      // Clear previous errors
      this.errors.department = ''
      
      if (!departmentId) {
        this.validationState.department = {
          isValid: false,
          message: 'Department selection is required'
        }
        return
      }
      
      // Check if department ID exists in the list
      const departmentExists = this.departments.find(dept => dept.id == departmentId)
      if (!departmentExists) {
        this.validationState.department = {
          isValid: false,
          message: 'Selected department does not exist'
        }
        return
      }
      
      // Valid department
      this.validationState.department = {
        isValid: true,
        message: ''
      }
    },
    
    validatePurpose(index) {
      const purpose = this.formData.internetPurposes[index]?.trim()
      
      // Clear previous errors
      this.errors[`purpose${index}`] = ''
      
      // First purpose is required, others are optional
      if (index === 0 && !purpose) {
        this.validationState.purposes[index] = {
          isValid: false,
          message: 'At least one purpose is required'
        }
        return
      }
      
      // If purpose is provided, validate it
      if (purpose) {
        if (purpose.length < 10) {
          this.validationState.purposes[index] = {
            isValid: false,
            message: 'Purpose must be at least 10 characters'
          }
          return
        }
        
        if (purpose.length > 500) {
          this.validationState.purposes[index] = {
            isValid: false,
            message: 'Purpose cannot exceed 500 characters'
          }
          return
        }
        
        // Valid purpose
        this.validationState.purposes[index] = {
          isValid: true,
          message: ''
        }
      } else {
        // Empty optional purpose
        this.validationState.purposes[index] = {
          isValid: false,
          message: ''
        }
      }
    },

    handleFileUpload(event) {
      const file = event.target.files[0]
      this.validateAndSetFile(file)
    },

    validateAndSetFile(file) {
      if (!file) return

      // Validate file type (only images for signature display)
      const allowedTypes = ['image/png', 'image/jpg', 'image/jpeg']
      if (!allowedTypes.includes(file.type)) {
        this.errors.signature = 'Please upload a PNG or JPG image file only'
        return
      }

      // Validate file size (2MB max for images)
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
      this.signatureFileName = file.name
      this.errors.signature = ''
    },

    removeFile() {
      this.formData.signature = null
      this.signaturePreview = null
      this.signatureFileName = ''
      this.errors.signature = ''
      this.uploadProgress = 0
      if (this.$refs.fileInput) {
        this.$refs.fileInput.value = ''
      }
    },

    validateForm() {
      // Reset errors
      this.errors = {
        pfNumber: '',
        staffName: '',
        phoneNumber: '',
        department: '',
        signature: '',
        purpose0: '',
        purpose1: '',
        purpose2: '',
        purpose3: '',
        general: ''
      }

      // Run all validations
      this.validatePfNumber()
      this.validateStaffName()
      this.validatePhoneNumber()
      this.validateDepartment()

      // Check if all fields are valid
      const isFormValid = 
        this.validationState.pfNumber.isValid &&
        this.validationState.staffName.isValid &&
        this.validationState.phoneNumber.isValid &&
        this.validationState.department.isValid

      // Validate Signature
      if (!this.formData.signature) {
        this.errors.signature = 'Digital signature is required'
        return false
      }

      // Validate Internet Purposes (at least first one required)
      if (!this.formData.internetPurposes[0]?.trim()) {
        this.errors.purpose0 = 'At least one internet purpose is required'
        return false
      }

      return isFormValid
    },

    async submitForm() {
      console.log('🚀 Starting Internet Access form submission...')
      
      // Client-side validation
      const validation = userInternetAccessFormService.validateFormData({
        pfNumber: this.formData.pfNumber,
        staffName: this.formData.staffName,
        phoneNumber: this.formData.phoneNumber,
        department: this.formData.department,
        purpose: this.formData.internetPurposes.filter(p => p.trim()).join('; '),
        signature: this.formData.signature
      })
      
      if (!validation.isValid) {
        this.errors = validation.errors
        console.error('❌ Client-side validation failed:', validation.errors)
        return
      }

      this.isSubmitting = true
      this.errors = {
        pfNumber: '',
        staffName: '',
        phoneNumber: '',
        department: '',
        signature: '',
        purpose0: '',
        purpose1: '',
        purpose2: '',
        purpose3: '',
        general: ''
      }

      try {
        // Create FormData for API submission
        const formData = userInternetAccessFormService.createFormData({
          pfNumber: this.formData.pfNumber,
          staffName: this.formData.staffName,
          phoneNumber: this.formData.phoneNumber,
          department: this.formData.department,
          purpose: this.formData.internetPurposes.filter(p => p.trim()).join('; '),
          signature: this.formData.signature
        })
        
        // Submit to backend
        const response = await userInternetAccessFormService.submitRequest(formData)
        
        if (response.success) {
          console.log('✅ Internet Access form submitted successfully:', response)
          this.showSuccessModal = true
          
          // Reset form after successful submission
          this.resetForm()
        } else {
          throw new Error(response.message || 'Submission failed')
        }
      } catch (error) {
        console.error('❌ Error submitting Internet Access form:', error)
        
        // Handle validation errors from backend
        if (error.details) {
          // Process each error field
          Object.keys(error.details).forEach(field => {
            const fieldErrors = error.details[field]
            
            // Handle array of errors or single error
            if (Array.isArray(fieldErrors)) {
              this.errors[field] = fieldErrors[0] // Take first error message
              
              // Special handling for PF number validation error
              if (field === 'pf_number') {
                console.error('🆔 PF Number validation error:', fieldErrors)
                
                // Show helpful message for PF number mismatch
                const errorMessage = fieldErrors[0]
                if (errorMessage && errorMessage.includes('does not match')) {
                  this.errors.general = 'PF Number issue: ' + errorMessage + ' Please check your profile or contact support.'
                }
              }
            } else {
              this.errors[field] = fieldErrors
            }
          })
          
          console.error('❌ Backend validation errors:', this.errors)
        } else if (error.message && error.message.includes('PF Number')) {
          // Handle PF number error in main message
          this.errors.pfNumber = error.message
          this.errors.general = 'Please check your PF Number and try again.'
        } else {
          // Show general error message
          this.errors.general = error.message || 'Error submitting form. Please try again.'
        }
      } finally {
        this.isSubmitting = false
      }
    },

    async loadDepartments() {
      this.isLoadingDepartments = true
      
      try {
        console.log('🚀 Loading departments from backend...')
        const response = await userInternetAccessFormService.getDepartments()
        
        if (response.success && response.data && response.data.length > 0) {
          // Use department objects with id and name from backend
          this.departments = response.data.map(dept => ({
            id: dept.id,
            name: dept.name,
            code: dept.code || ''
          }))
          console.log('✅ Successfully loaded', this.departments.length, 'departments from backend')
          console.log('📄 Departments:', this.departments.map(d => `${d.id}: ${d.name}`).join(', '))
        } else {
          console.error('❌ No departments received from backend')
          this.errors.general = 'Failed to load departments. Please refresh the page.'
        }
      } catch (error) {
        console.error('❌ Error loading departments from backend:', error)
        this.errors.general = 'Failed to load departments. Please check your connection and refresh the page.'
      } finally {
        this.isLoadingDepartments = false
      }
    },

    resetForm() {
      this.formData = {
        pfNumber: '',
        staffName: '',
        phoneNumber: '',
        signature: null,
        department: '',
        internetPurposes: ['', '', '', '']
      }
      this.errors = {
        pfNumber: '',
        staffName: '',
        phoneNumber: '',
        department: '',
        signature: '',
        purpose0: '',
        purpose1: '',
        purpose2: '',
        purpose3: '',
        general: ''
      }
      this.signaturePreview = null
      this.signatureFileName = ''
      this.uploadProgress = 0
      
      // Reset validation state
      this.validationState = {
        pfNumber: {
          isValid: false,
          message: ''
        },
        staffName: {
          isValid: false,
          message: ''
        },
        phoneNumber: {
          isValid: false,
          message: ''
        },
        department: {
          isValid: false,
          message: ''
        },
        purposes: [
          { isValid: false, message: '' },
          { isValid: false, message: '' },
          { isValid: false, message: '' },
          { isValid: false, message: '' }
        ]
      }
      
      if (this.$refs.fileInput) {
        this.$refs.fileInput.value = ''
      }
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
/* Medical Glass morphism effects */
.medical-glass-card {
  background: rgba(59, 130, 246, 0.15);
  backdrop-filter: blur(25px);
  -webkit-backdrop-filter: blur(25px);
  border: 2px solid rgba(96, 165, 250, 0.3);
  box-shadow: 0 8px 32px rgba(29, 78, 216, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.1);
}

.medical-card {
  position: relative;
  overflow: hidden;
  background: rgba(59, 130, 246, 0.1);
  backdrop-filter: blur(15px);
  -webkit-backdrop-filter: blur(15px);
}

.medical-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(96, 165, 250, 0.2), transparent);
  transition: left 0.5s;
}

.medical-card:hover::before {
  left: 100%;
}

.medical-input {
  position: relative;
  z-index: 1;
  color: white;
}

.medical-input::placeholder {
  color: rgba(191, 219, 254, 0.6);
}

.medical-input:focus {
  border-color: rgba(6, 182, 212, 0.8);
  box-shadow: 0 0 0 3px rgba(6, 182, 212, 0.2);
}

/* Select dropdown styling */
select.medical-input {
  background-image: none;
}

select.medical-input option {
  background-color: #1f2937;
  color: white;
  padding: 8px;
}

select.medical-input option:hover {
  background-color: #374151;
}

select.medical-input option:disabled {
  color: #9ca3af;
}

/* Animations */
@keyframes float {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-20px); }
}

@keyframes fade-in {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes fade-in-delay {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
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

/* Backdrop blur effect */
.backdrop-blur-sm {
  backdrop-filter: blur(4px);
}

/* Smooth transitions */
* {
  transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 200ms;
}

/* Focus styles for accessibility */
input:focus {
  box-shadow: 0 0 0 3px rgba(6, 182, 212, 0.1);
}

button:focus {
  box-shadow: 0 0 0 3px rgba(6, 182, 212, 0.3);
}

/* Upload button hover effects */
button:hover {
  transform: translateY(-1px);
}

/* Loading spinner animation */
@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

.fa-spin {
  animation: spin 1s linear infinite;
}

/* Validation state transitions */
.medical-input {
  transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

/* Success state */
.border-green-400 {
  box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1);
}

.border-green-400:focus {
  box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.2);
}

/* Error state */
.border-red-400 {
  box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
}

.border-red-400:focus {
  box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.2);
}

/* Validation message animations */
@keyframes slideInDown {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in {
  animation: slideInDown 0.3s ease-out;
}

/* Icon animations */
@keyframes checkmark {
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

.fas.fa-check,
.fas.fa-check-circle {
  animation: checkmark 0.3s ease-out;
}

@keyframes shake {
  0%, 100% {
    transform: translateX(0);
  }
  25% {
    transform: translateX(-2px);
  }
  75% {
    transform: translateX(2px);
  }
}

.fas.fa-times {
  animation: shake 0.3s ease-out;
}

/* Enhanced Modal Animations */
@keyframes bounce {
  0%, 20%, 53%, 80%, 100% {
    transform: translate3d(0, 0, 0);
  }
  40%, 43% {
    transform: translate3d(0, -8px, 0);
  }
  70% {
    transform: translate3d(0, -4px, 0);
  }
  90% {
    transform: translate3d(0, -2px, 0);
  }
}

@keyframes ping {
  75%, 100% {
    transform: scale(2);
    opacity: 0;
  }
}

@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}

.animate-bounce {
  animation: bounce 1s infinite;
}

.animate-ping {
  animation: ping 1s cubic-bezier(0, 0, 0.2, 1) infinite;
}

.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Modal backdrop blur effect */
.backdrop-blur-md {
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
}

/* Enhanced shadow effects */
.shadow-2xl {
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

/* Enhanced button hover effects */
.group:hover .group-hover\:translate-x-1 {
  transform: translateX(0.25rem);
}

.group:hover .group-hover\:translate-x-\[100\%\] {
  transform: translateX(100%);
}

/* Floating particles animation */
@keyframes float-particles {
  0%, 100% {
    transform: translateY(0px) rotate(0deg);
    opacity: 0.7;
  }
  33% {
    transform: translateY(-10px) rotate(120deg);
    opacity: 1;
  }
  66% {
    transform: translateY(5px) rotate(240deg);
    opacity: 0.8;
  }
}

.animate-float {
  animation: float-particles 4s ease-in-out infinite;
}
</style>