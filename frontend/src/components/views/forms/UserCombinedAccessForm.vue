<template>
  <div class="flex flex-col h-screen">
    <AppHeader />
    <div class="flex flex-1 overflow-hidden">
      <DynamicSidebar v-model:collapsed="sidebarCollapsed" />
      <main class="flex-1 p-4 bg-gradient-to-br from-blue-900 via-blue-800 to-blue-700 overflow-y-auto relative">
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
              <i :class="['fas', ['fa-cog', 'fa-server', 'fa-network-wired', 'fa-desktop', 'fa-database'][Math.floor(Math.random() * 5)]]"></i>
            </div>
          </div>
        </div>
        
        <div class="max-w-10xl mx-auto relative z-10">
          <!-- Header Section -->
          <div class="medical-glass-card rounded-t-2xl p-4 mb-0 border-b border-blue-300/30">
            <div class="flex justify-between items-center">
              <!-- Left Logo -->
              <div class="w-20 h-20 mr-4 transform hover:scale-110 transition-transform duration-300">
                <div class="w-full h-full bg-gradient-to-br from-blue-500/20 to-blue-600/20 rounded-xl backdrop-blur-sm border-2 border-blue-300/40 flex items-center justify-center shadow-xl hover:shadow-blue-500/25">
                  <img src="/assets/images/ngao2.png" alt="National Shield" class="max-w-14 max-h-14 object-contain" />
                </div>
              </div>

              <!-- Center Content -->
              <div class="text-center flex-1">
                <h1 class="text-xl font-bold text-white mb-2 tracking-wide drop-shadow-lg animate-fade-in">
                  MUHIMBILI NATIONAL HOSPITAL
                </h1>
                <div class="relative inline-block mb-2">
                  <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-2 rounded-full text-base font-bold shadow-xl transform hover:scale-105 transition-all duration-300 border-2 border-blue-400/60">
                    <span class="relative z-10 flex items-center gap-2">
                      <i class="fas fa-layer-group text-blue-200"></i>
                      COMBINED ACCESS REQUEST
                    </span>
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-700 to-blue-800 rounded-full opacity-0 hover:opacity-100 transition-opacity duration-300"></div>
                  </div>
                </div>
                <h2 class="text-base font-bold text-blue-100 tracking-wide drop-shadow-md animate-fade-in-delay">
                  COMPREHENSIVE SYSTEM ACCESS
                </h2>
              </div>
              
              <!-- Right Logo -->
              <div class="w-20 h-20 ml-4 transform hover:scale-110 transition-transform duration-300">
                <div class="w-full h-full bg-gradient-to-br from-blue-500/20 to-blue-600/20 rounded-xl backdrop-blur-sm border-2 border-blue-300/40 flex items-center justify-center shadow-xl hover:shadow-blue-500/25">
                  <img src="/assets/images/logo2.png" alt="Muhimbili Logo" class="max-w-14 max-h-14 object-contain" />
                </div>
              </div>
            </div>
          </div>

          <!-- Main Form -->
          <div class="medical-glass-card rounded-b-2xl overflow-hidden">
            <!-- Pending Request Warning Banner -->
            <div v-if="hasPendingRequest" class="bg-gradient-to-r from-yellow-600/90 to-orange-600/90 border-2 border-yellow-400/60 p-4 m-4 rounded-xl backdrop-blur-sm relative overflow-hidden">
              <!-- Animated Background -->
              <div class="absolute inset-0 bg-gradient-to-r from-yellow-500/20 to-orange-500/20 animate-pulse"></div>
              <div class="absolute top-0 right-0 w-16 h-16 bg-gradient-to-br from-yellow-400/30 to-transparent rounded-full blur-xl"></div>
              
              <div class="relative z-10 flex items-start space-x-4">
                <div class="flex-shrink-0">
                  <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-xl flex items-center justify-center shadow-lg border border-yellow-300/50">
                    <i class="fas fa-exclamation-triangle text-white text-xl animate-pulse"></i>
                  </div>
                </div>
                <div class="flex-1">
                  <h3 class="text-lg font-bold text-white mb-2 flex items-center">
                    <i class="fas fa-clock mr-2 text-yellow-300"></i>
                    Pending Request Found
                  </h3>
                  <p class="text-yellow-100 text-sm mb-3">
                    You have a pending {{ pendingRequestInfo?.request_type || 'access' }} request that is currently being processed. 
                    You cannot submit new requests until your current request is approved or rejected.
                  </p>
                  <div class="flex items-center space-x-4">
                    <div class="bg-yellow-500/20 px-3 py-1 rounded-full border border-yellow-400/30">
                      <span class="text-xs text-yellow-200 font-medium">
                        <i class="fas fa-calendar mr-1"></i>
                        Submitted: {{ pendingRequestInfo?.created_at ? new Date(pendingRequestInfo.created_at).toLocaleDateString() : 'Unknown' }}
                      </span>
                    </div>
                    <div class="bg-orange-500/20 px-3 py-1 rounded-full border border-orange-400/30">
                      <span class="text-xs text-orange-200 font-medium">
                        <i class="fas fa-hourglass-half mr-1"></i>
                        Status: {{ pendingRequestInfo?.status || 'Pending' }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Loading Banner -->
            <div v-if="checkingPendingStatus" class="bg-gradient-to-r from-blue-600/90 to-blue-700/90 border-2 border-blue-400/60 p-4 m-4 rounded-xl backdrop-blur-sm">
              <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center">
                  <i class="fas fa-spinner fa-spin text-white"></i>
                </div>
                <span class="text-white font-medium">Checking for pending requests...</span>
              </div>
            </div>
            
        <form @submit.prevent="submitForm" class="p-4 space-y-4" :class="{ 'opacity-50 pointer-events-none': hasPendingRequest }">
          
          <!-- Form Description -->
           
          <!-- Applicant Details Section -->
          <div class="medical-card bg-gradient-to-r from-blue-600/25 to-blue-700/25 border-2 border-blue-400/40 p-4 rounded-xl backdrop-blur-sm hover:shadow-xl hover:shadow-blue-500/20 transition-all duration-500 group relative overflow-hidden">
            <!-- Animated Background Layers -->
            <div class="absolute inset-0 bg-gradient-to-r from-blue-500/10 via-blue-600/5 to-blue-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>
            <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-blue-400/20 to-transparent rounded-full blur-xl group-hover:scale-150 transition-transform duration-1000"></div>
            <div class="absolute bottom-0 left-0 w-16 h-16 bg-gradient-to-tr from-blue-400/15 to-transparent rounded-full blur-lg group-hover:scale-125 transition-transform duration-800"></div>
            
            <div class="relative z-10">
              <div class="flex items-center space-x-3 mb-4">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-blue-300/50 relative overflow-hidden">
                  <div class="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent rounded-lg"></div>
                  <i class="fas fa-user text-white text-lg relative z-10 drop-shadow-lg"></i>
                  <div class="absolute top-1 right-1 w-1 h-1 bg-white/60 rounded-full animate-ping"></div>
                </div>
                <div>
                  <h3 class="text-lg font-bold text-white flex items-center">
                    <i class="fas fa-id-card mr-2 text-blue-300"></i>
                    Applicant Details
                  </h3>
                  <div class="flex items-center space-x-2 mt-1">
                    <span class="text-xs text-red-400 font-medium bg-red-500/20 px-2 py-1 rounded-full border border-red-400/30">
                      <i class="fas fa-asterisk mr-1 text-xs"></i>
                      Required Information
                    </span>
                  </div>
                </div>
              </div>

              <!-- Attractive Field Layout -->
              <div class="space-y-4 mb-4">
                <!-- Row 1: Basic Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                  <!-- PF Number -->
                  <div class="group">
                    <label class="block text-sm font-bold text-blue-100 mb-2 flex items-center">
                      <i class="fas fa-id-badge mr-2 text-blue-300"></i>
                      PF Number <span class="text-red-400 ml-1">*</span>
                    </label>
                    <div class="relative">
                      <input 
                        v-model="formData.pfNumber" 
                        type="text" 
                        :class="`medical-input w-full px-3 py-3 pr-12 bg-white/15 border-2 border-blue-300/30 rounded-lg focus:border-blue-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-blue-500/20 group-hover:border-blue-400/50 ${getFieldValidationClass('pfNumber')}`"
                        placeholder="Enter PF Number"
                        @input="onPfNumberInput"
                        @blur="fieldTouched.pfNumber = true; validatePfNumber()"
                        required
                      />
                      <div class="absolute inset-0 rounded-lg bg-gradient-to-r from-blue-500/10 to-blue-600/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
                      
                      <!-- Validation Icon -->
                      <div class="absolute right-3 top-1/2 transform -translate-y-1/2 flex items-center space-x-2">
                        <!-- Success Icon -->
                        <div v-if="isFieldValid('pfNumber')" class="text-green-400 animate-bounce">
                          <i class="fas fa-check-circle text-lg"></i>
                        </div>
                        <!-- Error Icon -->
                        <div v-else-if="fieldTouched.pfNumber && fieldErrors.pfNumber" class="text-red-400 animate-pulse">
                          <i class="fas fa-exclamation-circle text-lg"></i>
                        </div>
                        <!-- Default Icon -->
                        <div v-else class="text-blue-300/50">
                          <i class="fas fa-hashtag"></i>
                        </div>
                      </div>
                    </div>
                    <p v-if="fieldTouched.pfNumber && fieldErrors.pfNumber" class="text-xs text-red-400 mt-1 flex items-center">
                      <i class="fas fa-exclamation-circle mr-1"></i>
                      {{ fieldErrors.pfNumber }}
                    </p>
                    <p v-else class="text-xs text-blue-200/60 mt-1 italic flex items-center">
                      <i class="fas fa-info-circle mr-1"></i>
                      e.g. 12345
                    </p>
                  </div>

                  <!-- Staff Name -->
                  <div class="group">
                    <label class="block text-sm font-bold text-blue-100 mb-2 flex items-center">
                      <i class="fas fa-user mr-2 text-blue-300"></i>
                      Staff Name <span class="text-red-400 ml-1">*</span>
                    </label>
                    <div class="relative">
                      <input 
                        v-model="formData.staffName" 
                        type="text" 
                        :class="`medical-input w-full px-3 py-3 pr-12 bg-white/15 border-2 border-blue-300/30 rounded-lg focus:border-blue-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-blue-500/20 group-hover:border-blue-400/50 ${getFieldValidationClass('staffName')}`"
                        placeholder="Enter full name"
                        @input="onStaffNameInput"
                        @blur="fieldTouched.staffName = true; validateStaffName()"
                        required
                      />
                      <div class="absolute inset-0 rounded-lg bg-gradient-to-r from-blue-500/10 to-blue-600/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
                      
                      <!-- Validation Icon -->
                      <div class="absolute right-3 top-1/2 transform -translate-y-1/2 flex items-center space-x-2">
                        <!-- Success Icon -->
                        <div v-if="isFieldValid('staffName')" class="text-green-400 animate-bounce">
                          <i class="fas fa-check-circle text-lg"></i>
                        </div>
                        <!-- Error Icon -->
                        <div v-else-if="fieldTouched.staffName && fieldErrors.staffName" class="text-red-400 animate-pulse">
                          <i class="fas fa-exclamation-circle text-lg"></i>
                        </div>
                        <!-- Default Icon -->
                        <div v-else class="text-blue-300/50">
                          <i class="fas fa-user-circle"></i>
                        </div>
                      </div>
                    </div>
                    <p v-if="fieldTouched.staffName && fieldErrors.staffName" class="text-xs text-red-400 mt-1 flex items-center">
                      <i class="fas fa-exclamation-circle mr-1"></i>
                      {{ fieldErrors.staffName }}
                    </p>
                    <p v-else class="text-xs text-blue-200/60 mt-1 italic flex items-center">
                      <i class="fas fa-info-circle mr-1"></i>
                      e.g. Jane Doe
                    </p>
                  </div>
                </div>

                <!-- Row 2: Contact & Department -->
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
                        type="tel" 
                        :class="`medical-input w-full px-3 py-3 pr-12 bg-white/15 border-2 border-blue-300/30 rounded-lg focus:border-blue-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-blue-500/20 group-hover:border-blue-400/50 ${getFieldValidationClass('phoneNumber')}`"
                        placeholder="Enter phone number"
                        @input="onPhoneNumberInput"
                        @blur="fieldTouched.phoneNumber = true; validatePhoneNumber()"
                        required
                      />
                      <div class="absolute inset-0 rounded-lg bg-gradient-to-r from-blue-500/10 to-blue-600/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
                      
                      <!-- Validation Icon -->
                      <div class="absolute right-3 top-1/2 transform -translate-y-1/2 flex items-center space-x-2">
                        <!-- Success Icon -->
                        <div v-if="isFieldValid('phoneNumber')" class="text-green-400 animate-bounce">
                          <i class="fas fa-check-circle text-lg"></i>
                        </div>
                        <!-- Error Icon -->
                        <div v-else-if="fieldTouched.phoneNumber && fieldErrors.phoneNumber" class="text-red-400 animate-pulse">
                          <i class="fas fa-exclamation-circle text-lg"></i>
                        </div>
                        <!-- Default Icon -->
                        <div v-else class="text-blue-300/50">
                          <i class="fas fa-mobile-alt"></i>
                        </div>
                      </div>
                    </div>
                    <p v-if="fieldTouched.phoneNumber && fieldErrors.phoneNumber" class="text-xs text-red-400 mt-1 flex items-center">
                      <i class="fas fa-exclamation-circle mr-1"></i>
                      {{ fieldErrors.phoneNumber }}
                    </p>
                    <p v-else class="text-xs text-blue-200/60 mt-1 italic flex items-center">
                      <i class="fas fa-info-circle mr-1"></i>
                      e.g. 0712 000 000
                    </p>
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
                        :class="`medical-input w-full px-3 py-3 pr-16 bg-white/15 border-2 border-blue-300/30 rounded-lg focus:border-blue-400 focus:outline-none text-white backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-blue-500/20 appearance-none cursor-pointer group-hover:border-blue-400/50 ${getFieldValidationClass('department')}`"
                        @change="onDepartmentChange"
                        @blur="fieldTouched.department = true; validateDepartment()"
                        required
                      >
                        <option value="" class="bg-blue-800 text-blue-300">Select Department</option>
                        <option v-for="dept in departments" :key="dept.id" :value="dept.id" class="bg-blue-800 text-white">{{ dept.name }}</option>
                      </select>
                      
                      <!-- Validation and Dropdown Icons -->
                      <div class="absolute right-3 top-1/2 transform -translate-y-1/2 flex items-center space-x-2 pointer-events-none">
                        <!-- Success Icon -->
                        <div v-if="isFieldValid('department')" class="text-green-400 animate-bounce">
                          <i class="fas fa-check-circle text-lg"></i>
                        </div>
                        <!-- Error Icon -->
                        <div v-else-if="fieldTouched.department && fieldErrors.department" class="text-red-400 animate-pulse">
                          <i class="fas fa-exclamation-circle text-lg"></i>
                        </div>
                        <!-- Dropdown Icon -->
                        <div class="text-blue-300/50">
                          <i class="fas fa-chevron-down"></i>
                        </div>
                      </div>
                      <div class="absolute inset-0 rounded-lg bg-gradient-to-r from-blue-500/10 to-blue-600/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
                    </div>
                    <p v-if="fieldTouched.department && fieldErrors.department" class="text-xs text-red-400 mt-1 flex items-center">
                      <i class="fas fa-exclamation-circle mr-1"></i>
                      {{ fieldErrors.department }}
                    </p>
                    <p v-else class="text-xs text-blue-200/60 mt-1 italic flex items-center">
                      <i class="fas fa-info-circle mr-1"></i>
                      Select your department
                    </p>
                  </div>
                </div>

                <!-- Row 3: Digital Signature (Full Width) -->
                <div class="bg-gradient-to-r from-blue-500/10 to-blue-600/10 p-4 rounded-xl border border-blue-300/20 backdrop-blur-sm">
                  <div class="group">
                    <label class="block text-sm font-bold text-blue-100 mb-3 flex items-center">
                      <i class="fas fa-signature mr-2 text-blue-300"></i>
                      Digital Signature <span class="text-red-400 ml-1">*</span>
                      <span class="ml-2 text-xs text-blue-300/70 font-normal">(PNG, JPG, JPEG)</span>
                    </label>
                    
                    <div class="flex flex-col md:flex-row gap-3 items-start">
                      <!-- Signature Display Box -->
                      <div class="relative w-full md:w-64 h-16 border-2 border-blue-300/30 rounded-lg bg-blue-100/20 focus-within:bg-blue-100/30 focus-within:border-blue-400 overflow-hidden backdrop-blur-sm group-hover:border-blue-400/50 transition-all duration-300">
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
                            <i class="fas fa-signature text-blue-400/50 text-xl mb-1"></i>
                            <p class="text-xs text-blue-400 italic">Signature will appear here</p>
                          </div>
                        </div>
                        
                        <!-- Remove Button (when signature exists) -->
                        <button
                          v-if="signaturePreview"
                          type="button"
                          @click="clearSignature"
                          class="absolute top-2 right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600 transition duration-200 text-xs shadow-lg"
                          title="Remove signature"
                        >
                          <i class="fas fa-times"></i>
                        </button>
                      </div>
                      
                      <!-- Upload Button -->
                      <div class="flex flex-col gap-2">
                        <input 
                          ref="signatureInput"
                          type="file" 
                          accept=".png,.jpg,.jpeg"
                          @change="onSignatureChange"
                          class="hidden"
                        />
                        
                        <button
                          type="button"
                          @click="triggerFileUpload"
                          class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white text-sm font-medium rounded-lg hover:from-blue-600 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 transition duration-200 shadow-lg hover:shadow-xl transform hover:scale-105 border border-blue-400/30"
                        >
                          <span class="flex items-center">
                            <i class="fas fa-upload mr-2 text-blue-200"></i>
                            Load Signature 
                          </span>
                        </button>
                        
                        <p v-if="fieldTouched.signature && fieldErrors.signature" class="text-xs text-red-400 italic flex items-center">
                          <i class="fas fa-exclamation-circle mr-1"></i>
                          {{ fieldErrors.signature }}
                        </p>
                        <p v-else class="text-xs text-blue-200/70 italic flex items-center">
                          <i class="fas fa-info-circle mr-1"></i>
                          Max size: 5MB
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Module Selection Section -->
          <div class="medical-card bg-gradient-to-r from-blue-600/25 to-blue-700/25 border-2 border-blue-400/40 p-4 rounded-xl backdrop-blur-sm hover:shadow-xl hover:shadow-blue-500/20 transition-all duration-500 group relative overflow-hidden">
            <!-- Animated Background Layers -->
            <div class="absolute inset-0 bg-gradient-to-r from-blue-500/10 via-blue-600/5 to-blue-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>
            <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-blue-400/20 to-transparent rounded-full blur-xl group-hover:scale-150 transition-transform duration-1000"></div>
            <div class="absolute bottom-0 left-0 w-16 h-16 bg-gradient-to-tr from-blue-400/15 to-transparent rounded-full blur-lg group-hover:scale-125 transition-transform duration-800"></div>
            
            <div class="relative z-10">
              <div class="flex items-center space-x-3 mb-4">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-blue-300/50 relative overflow-hidden">
                  <div class="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent rounded-lg"></div>
                  <i class="fas fa-layer-group text-white text-lg relative z-10 drop-shadow-lg"></i>
                  <div class="absolute top-1 right-1 w-1 h-1 bg-white/60 rounded-full animate-ping"></div>
                </div>
                <div>
                  <h3 class="text-lg font-bold text-white flex items-center">
                    <i class="fas fa-cogs mr-2 text-blue-300"></i>
                    Select Services
                  </h3>
                  <p class="text-sm text-blue-100/80">Choose which services you need access to</p>
                </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Jeeva Service -->
                <div class="relative group/service">
                  <input 
                    v-model="formData.services.jeeva" 
                    type="checkbox" 
                    id="jeeva-service"
                    class="sr-only"
                    @change="onServiceChange"
                  />
                  <label 
                    for="jeeva-service"
                    class="block p-4 border-2 rounded-xl cursor-pointer transition-all duration-300 hover:shadow-lg backdrop-blur-sm relative overflow-hidden transform hover:scale-105"
                    :class="formData.services.jeeva ? 'border-blue-400 bg-blue-500/20 shadow-lg shadow-blue-500/25' : 'border-white/30 bg-white/10 hover:border-blue-300/50 hover:bg-blue-500/10'"
                  >
                    <!-- Service Card Background Effects -->
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-transparent opacity-0 group-hover/service:opacity-100 transition-opacity duration-300"></div>
                    <div class="absolute top-0 right-0 w-12 h-12 bg-gradient-to-br from-blue-400/20 to-transparent rounded-full blur-lg"></div>
                    
                    <div class="relative z-10">
                      <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center">
                          <div class="w-10 h-10 rounded-lg flex items-center justify-center mr-3 transition-all duration-300 relative overflow-hidden"
                               :class="formData.services.jeeva ? 'bg-blue-500 text-white shadow-lg' : 'bg-white/20 text-blue-300'">
                            <div v-if="formData.services.jeeva" class="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent"></div>
                            <i class="fas fa-file-medical text-base relative z-10"></i>
                          </div>
                          <div>
                            <h4 class="font-bold text-white text-base">Jeeva Access</h4>
                            <p class="text-xs text-blue-200/80">Medical records system</p>
                          </div>
                        </div>
                        <div class="w-5 h-5 rounded border-2 flex items-center justify-center transition-all duration-300"
                             :class="formData.services.jeeva ? 'border-blue-400 bg-blue-500 shadow-lg' : 'border-white/40'">
                          <i v-if="formData.services.jeeva" class="fas fa-check text-white text-xs"></i>
                        </div>
                      </div>
                      
                      <!-- Service Features -->
                      <div class="space-y-1">
                        <div class="flex items-center text-xs text-blue-100/70">
                          <i class="fas fa-check-circle mr-2 text-blue-400"></i>
                          Patient Records
                        </div>
                        <div class="flex items-center text-xs text-blue-100/70">
                          <i class="fas fa-check-circle mr-2 text-blue-400"></i>
                          Medical History
                        </div>
                      </div>
                    </div>
                  </label>
                </div>

                <!-- Wellsoft Service -->
                <div class="relative group/service">
                  <input 
                    v-model="formData.services.wellsoft" 
                    type="checkbox" 
                    id="wellsoft-service"
                    class="sr-only"
                    @change="onServiceChange"
                  />
                  <label 
                    for="wellsoft-service"
                    class="block p-4 border-2 rounded-xl cursor-pointer transition-all duration-300 hover:shadow-lg backdrop-blur-sm relative overflow-hidden transform hover:scale-105"
                    :class="formData.services.wellsoft ? 'border-blue-400 bg-blue-500/20 shadow-lg shadow-blue-500/25' : 'border-white/30 bg-white/10 hover:border-blue-300/50 hover:bg-blue-500/10'"
                  >
                    <!-- Service Card Background Effects -->
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-transparent opacity-0 group-hover/service:opacity-100 transition-opacity duration-300"></div>
                    <div class="absolute top-0 right-0 w-12 h-12 bg-gradient-to-br from-blue-400/20 to-transparent rounded-full blur-lg"></div>
                    
                    <div class="relative z-10">
                      <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center">
                          <div class="w-10 h-10 rounded-lg flex items-center justify-center mr-3 transition-all duration-300 relative overflow-hidden"
                               :class="formData.services.wellsoft ? 'bg-blue-500 text-white shadow-lg' : 'bg-white/20 text-blue-300'">
                            <div v-if="formData.services.wellsoft" class="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent"></div>
                            <i class="fas fa-laptop-medical text-base relative z-10"></i>
                          </div>
                          <div>
                            <h4 class="font-bold text-white text-base">Wellsoft Access</h4>
                            <p class="text-xs text-blue-200/80">Hospital management</p>
                          </div>
                        </div>
                        <div class="w-5 h-5 rounded border-2 flex items-center justify-center transition-all duration-300"
                             :class="formData.services.wellsoft ? 'border-blue-400 bg-blue-500 shadow-lg' : 'border-white/40'">
                          <i v-if="formData.services.wellsoft" class="fas fa-check text-white text-xs"></i>
                        </div>
                      </div>
                      
                      <!-- Service Features -->
                      <div class="space-y-1">
                        <div class="flex items-center text-xs text-blue-100/70">
                          <i class="fas fa-check-circle mr-2 text-blue-400"></i>
                          Patient Management
                        </div>
                        <div class="flex items-center text-xs text-blue-100/70">
                          <i class="fas fa-check-circle mr-2 text-blue-400"></i>
                          Hospital Operations
                        </div>
                      </div>
                    </div>
                  </label>
                </div>

                <!-- Internet Service -->
                <div class="relative group/service">
                  <input 
                    v-model="formData.services.internet" 
                    type="checkbox" 
                    id="internet-service"
                    class="sr-only"
                    @change="onServiceChange"
                  />
                  <label 
                    for="internet-service"
                    class="block p-4 border-2 rounded-xl cursor-pointer transition-all duration-300 hover:shadow-lg backdrop-blur-sm relative overflow-hidden transform hover:scale-105"
                    :class="formData.services.internet ? 'border-blue-400 bg-blue-500/20 shadow-lg shadow-blue-500/25' : 'border-white/30 bg-white/10 hover:border-blue-300/50 hover:bg-blue-500/10'"
                  >
                    <!-- Service Card Background Effects -->
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-transparent opacity-0 group-hover/service:opacity-100 transition-opacity duration-300"></div>
                    <div class="absolute top-0 right-0 w-12 h-12 bg-gradient-to-br from-blue-400/20 to-transparent rounded-full blur-lg"></div>
                    
                    <div class="relative z-10">
                      <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center">
                          <div class="w-10 h-10 rounded-lg flex items-center justify-center mr-3 transition-all duration-300 relative overflow-hidden"
                               :class="formData.services.internet ? 'bg-blue-500 text-white shadow-lg' : 'bg-white/20 text-blue-300'">
                            <div v-if="formData.services.internet" class="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent"></div>
                            <i class="fas fa-wifi text-base relative z-10"></i>
                          </div>
                          <div>
                            <h4 class="font-bold text-white text-base">Internet Access</h4>
                            <p class="text-xs text-blue-200/80">Internet connectivity</p>
                          </div>
                        </div>
                        <div class="w-5 h-5 rounded border-2 flex items-center justify-center transition-all duration-300"
                             :class="formData.services.internet ? 'border-blue-400 bg-blue-500 shadow-lg' : 'border-white/40'">
                          <i v-if="formData.services.internet" class="fas fa-check text-white text-xs"></i>
                        </div>
                      </div>
                      
                      <!-- Service Features -->
                      <div class="space-y-1">
                        <div class="flex items-center text-xs text-blue-100/70">
                          <i class="fas fa-check-circle mr-2 text-blue-400"></i>
                          Web Access
                        </div>
                        <div class="flex items-center text-xs text-blue-100/70">
                          <i class="fas fa-check-circle mr-2 text-blue-400"></i>
                          Email & Communication
                        </div>
                      </div>
                    </div>
                  </label>
                </div>
              </div>

              <!-- Service Selection Validation -->
              <div v-if="fieldTouched.services && fieldErrors.services" class="mt-6 p-4 bg-red-500/20 border-2 border-red-400/40 rounded-xl backdrop-blur-sm">
                <p class="text-red-300 text-sm font-medium flex items-center">
                  <i class="fas fa-exclamation-triangle mr-2 text-red-400"></i>
                  {{ fieldErrors.services }}
                </p>
              </div>
            </div>
          </div>

          <!-- Internet Purpose Section (Only show when Internet is selected) -->
          <div v-if="formData.services.internet" class="medical-card bg-gradient-to-r from-blue-600/25 to-blue-700/25 border-2 border-blue-400/40 p-3 rounded-lg backdrop-blur-sm hover:shadow-lg hover:shadow-blue-500/20 transition-all duration-300 group relative overflow-hidden">
            <!-- Animated Background Layers -->
            <div class="absolute inset-0 bg-gradient-to-r from-blue-500/10 via-blue-600/5 to-blue-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            <div class="absolute top-0 right-0 w-16 h-16 bg-gradient-to-br from-blue-400/20 to-transparent rounded-full blur-lg group-hover:scale-125 transition-transform duration-700"></div>
            <div class="absolute bottom-0 left-0 w-12 h-12 bg-gradient-to-tr from-blue-400/15 to-transparent rounded-full blur-md group-hover:scale-110 transition-transform duration-600"></div>
            
            <div class="relative z-10">
              <div class="flex items-center space-x-2 mb-3">
                <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-md flex items-center justify-center shadow-md group-hover:scale-105 transition-transform duration-300 border border-blue-300/50 relative overflow-hidden">
                  <div class="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent rounded-md"></div>
                  <i class="fas fa-wifi text-white text-sm relative z-10 drop-shadow-lg"></i>
                </div>
                <div>
                  <h3 class="text-base font-bold text-white flex items-center">
                    <i class="fas fa-globe mr-2 text-blue-300"></i>
                    Internet Purpose
                  </h3>
                  <p class="text-xs text-blue-100/80">Specify the purposes for internet access</p>
                </div>
              </div>

              <div class="space-y-2">
                <div v-for="(purpose, index) in formData.internetPurposes" :key="index" class="flex items-center gap-2">
                  <div class="flex items-center justify-center w-5 h-5 bg-blue-500/40 text-blue-200 rounded-full font-bold text-xs border-2 border-blue-400/70">
                    {{ index + 1 }}
                  </div>
                  <input 
                    v-model="formData.internetPurposes[index]" 
                    type="text" 
                    :class="`medical-input flex-1 px-2 py-1.5 bg-white/15 border-2 border-blue-400/70 rounded-md focus:border-blue-500 hover:border-blue-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-md focus:shadow-blue-500/20 text-sm hover:border-blue-500/80 ${fieldTouched.internetPurposes && fieldErrors.internetPurposes ? 'border-red-500' : ''}`"
                    :placeholder="`Purpose ${index + 1}`"
                    @input="onInternetPurposeInput"
                    @blur="fieldTouched.internetPurposes = true; validateInternetPurposes()"
                    :required="index === 0 && formData.services.internet"
                  />
                </div>
                <p v-if="fieldTouched.internetPurposes && fieldErrors.internetPurposes" class="text-xs text-red-400 mt-1 flex items-center">
                  <i class="fas fa-exclamation-circle mr-1"></i>
                  {{ fieldErrors.internetPurposes }}
                </p>
                <p v-else class="text-xs text-blue-200 mt-1 italic flex items-center">
                  <i class="fas fa-info-circle mr-1 text-blue-400"></i>
                  Please provide at least one purpose for internet access. You can add up to 4 purposes.
                </p>
              </div>
            </div>
          </div>

          <!-- Form Actions -->
          <div class="medical-card bg-gradient-to-r from-blue-600/25 to-blue-700/25 border-2 border-blue-400/40 p-3 rounded-lg backdrop-blur-sm hover:shadow-lg hover:shadow-blue-500/20 transition-all duration-300 group relative overflow-hidden">
            <!-- Animated Background Layers -->
            <div class="absolute inset-0 bg-gradient-to-r from-blue-500/10 via-blue-600/5 to-blue-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            <div class="absolute top-0 right-0 w-16 h-16 bg-gradient-to-br from-blue-400/20 to-transparent rounded-full blur-lg group-hover:scale-125 transition-transform duration-700"></div>
            <div class="absolute bottom-0 left-0 w-12 h-12 bg-gradient-to-tr from-blue-400/15 to-transparent rounded-full blur-md group-hover:scale-110 transition-transform duration-600"></div>
            
            <div class="relative z-10">
              <div class="flex justify-between items-center">
                <!-- Left: Enhanced Back Button -->
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
                
                <!-- Center: ICT Info -->
      
                <!-- Right: Enhanced Submit Button -->
                <button 
                  type="submit" 
                  :disabled="isSubmitting || !isFormValid || hasPendingRequest || checkingPendingStatus"
                  :class="[
                    'group relative inline-flex items-center justify-center px-6 py-3 text-white font-bold rounded-xl shadow-xl transform transition-all duration-300 border overflow-hidden',
                    hasPendingRequest 
                      ? 'bg-gradient-to-r from-gray-500 via-gray-600 to-gray-700 border-gray-400/50 cursor-not-allowed'
                      : 'bg-gradient-to-r from-blue-600 via-blue-700 to-blue-800 border-blue-500/50 hover:shadow-blue-500/25 hover:scale-105',
                    'disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none disabled:hover:scale-100'
                  ]"
                >
                  <!-- Button background animation -->
                  <div v-if="!hasPendingRequest" class="absolute inset-0 bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                  <div v-if="!hasPendingRequest" class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                  
                  <!-- Button content -->
                  <div class="relative z-10 flex items-center">
                    <i v-if="isSubmitting" class="fas fa-spinner fa-spin mr-3 text-lg"></i>
                    <i v-else-if="hasPendingRequest" class="fas fa-ban mr-3 text-lg"></i>
                    <i v-else-if="checkingPendingStatus" class="fas fa-spinner fa-spin mr-3 text-lg"></i>
                    <i v-else class="fas fa-paper-plane mr-3 text-lg group-hover:translate-x-1 transition-transform duration-300"></i>
                    <span class="text-base">
                      {{ 
                        isSubmitting ? 'Submitting...' : 
                        hasPendingRequest ? 'Request Blocked - Pending Request Exists' :
                        checkingPendingStatus ? 'Checking Status...' :
                        'Submit Request' 
                      }}
                    </span>
                  </div>
                  
                  <!-- Button shine effect (only when not disabled) -->
                  <div v-if="!isSubmitting && isFormValid && !hasPendingRequest" class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -skew-x-12 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-700"></div>
                </button>
              </div>
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
                <div class="relative bg-gradient-to-r from-blue-600 via-blue-700 to-blue-800 px-6 py-6">
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
                    <div class="mx-auto w-16 h-16 bg-gradient-to-br from-blue-400 via-blue-500 to-blue-600 rounded-full flex items-center justify-center mb-4 shadow-2xl border-4 border-white/30 relative overflow-hidden">
                      <div class="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent rounded-full"></div>
                      <i class="fas fa-check text-white text-2xl drop-shadow-lg relative z-10 animate-bounce"></i>
                      
                      <!-- Success ripple effect -->
                      <div class="absolute inset-0 rounded-full border-4 border-white/50 animate-ping"></div>
                    </div>
                    
                    <h3 class="text-xl font-bold text-white mb-2 drop-shadow-lg">
                      Request Submitted!
                    </h3>
                  </div>
                </div>
                
                <!-- Content section -->
                <div class="relative px-6 py-6">
                  <div class="text-center mb-6">
                    <p class="text-gray-600 mb-4">Your Combined Access Request has been submitted successfully.</p>
          <div class="bg-blue-50 rounded-lg p-4 mb-6">
            <p class="text-sm text-blue-800 font-medium mb-3 flex items-center">
              <i class="fas fa-check-circle mr-2 text-green-600"></i>
              Selected Services:
            </p>
            
            <!-- Services List -->
            <div class="space-y-3">
              <!-- Jeeva Service -->
              <div v-if="submittedServices && submittedServices.jeeva" class="flex items-center p-2 bg-emerald-50 rounded-lg border border-emerald-200">
                <div class="w-8 h-8 bg-emerald-500 rounded-lg flex items-center justify-center mr-3">
                  <i class="fas fa-file-medical text-white text-sm"></i>
                </div>
                <div class="flex-1">
                  <h4 class="font-semibold text-emerald-800 text-sm">Jeeva Access</h4>
                  <p class="text-xs text-emerald-600">Medical records system - Patient Records, Medical History</p>
                </div>
                <span class="px-2 py-1 bg-emerald-100 text-emerald-800 rounded-full text-xs font-medium">
                  ✓ Selected
                </span>
              </div>
              
              <!-- Wellsoft Service -->
              <div v-if="submittedServices && submittedServices.wellsoft" class="flex items-center p-2 bg-purple-50 rounded-lg border border-purple-200">
                <div class="w-8 h-8 bg-purple-500 rounded-lg flex items-center justify-center mr-3">
                  <i class="fas fa-laptop-medical text-white text-sm"></i>
                </div>
                <div class="flex-1">
                  <h4 class="font-semibold text-purple-800 text-sm">Wellsoft Access</h4>
                  <p class="text-xs text-purple-600">Hospital management - Patient Management, Hospital Operations</p>
                </div>
                <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-medium">
                  ✓ Selected
                </span>
              </div>
              
              <!-- Internet Service -->
              <div v-if="submittedServices && submittedServices.internet" class="flex items-center p-2 bg-orange-50 rounded-lg border border-orange-200">
                <div class="w-8 h-8 bg-orange-500 rounded-lg flex items-center justify-center mr-3">
                  <i class="fas fa-wifi text-white text-sm"></i>
                </div>
                <div class="flex-1">
                  <h4 class="font-semibold text-orange-800 text-sm">Internet Access</h4>
                  <p class="text-xs text-orange-600">Internet connectivity - Web Access, Email & Communication</p>
                </div>
                <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded-full text-xs font-medium">
                  ✓ Selected
                </span>
              </div>
            </div>
            
            <!-- Show Internet Purposes if Internet service is selected -->
            <div v-if="submittedServices && submittedServices.internet && hasInternetPurposes" class="mt-4 pt-3 border-t border-blue-200">
              <p class="text-sm text-blue-800 font-medium mb-2 flex items-center">
                <i class="fas fa-list-ul mr-2 text-orange-600"></i>
                Internet Access Purposes:
              </p>
              <div class="bg-orange-50 rounded-lg p-3 border border-orange-200">
                <div class="space-y-2">
                  <div v-for="(purpose, index) in filledInternetPurposes" :key="index" class="flex items-start text-sm text-orange-800">
                    <span class="w-5 h-5 bg-orange-200 text-orange-800 rounded-full flex items-center justify-center mr-3 font-bold text-xs mt-0.5 flex-shrink-0">
                      {{ index + 1 }}
                    </span>
                    <span class="flex-1">{{ purpose }}</span>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Summary -->
            <div class="mt-4 pt-3 border-t border-blue-200">
              <p class="text-xs text-blue-600 italic text-center">
                <i class="fas fa-info-circle mr-1"></i>
                Total services requested: {{ getSelectedServicesCount() }}
              </p>
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
                          <i class="fas fa-home mr-3 text-xl group-hover:translate-x-1 transition-transform duration-300"></i>
                          <span class="text-lg">Return to Dashboard</span>
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
import userCombinedAccessService from '@/services/userCombinedAccessService.js'

export default {
  name: 'UserCombinedAccessForm',
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
      isSubmitting: false,
      showSuccessModal: false,
      signaturePreview: '',
      signatureFileName: '',
      departments: [],
      validationErrors: {},
      fieldErrors: {},
      fieldTouched: {},
      submittedServices: null, // Store services that were actually submitted
      hasPendingRequest: false, // Track if user has pending requests
      pendingRequestInfo: null, // Store pending request details
      checkingPendingStatus: true, // Loading state for pending check
      formData: {
        // Applicant Details
        pfNumber: '',
        staffName: '',
        department: '',
        phoneNumber: '',
        signature: null,
        // Services Selection
        services: {
          jeeva: false,
          wellsoft: false,
          internet: false
        },
        // Internet Purposes (only used when internet service is selected)
        internetPurposes: ['', '', '', '']
      }
    }
  },
  
  computed: {
    hasSelectedService() {
      return this.formData.services.jeeva || this.formData.services.wellsoft || this.formData.services.internet
    },
    hasInternetPurposes() {
      return this.formData.internetPurposes.some(purpose => purpose.trim() !== '')
    },
    filledInternetPurposes() {
      return this.formData.internetPurposes.filter(purpose => purpose.trim() !== '')
    },
    isFormValid() {
      return Object.keys(this.fieldErrors).length === 0 && 
             this.formData.pfNumber && 
             this.formData.staffName && 
             this.formData.phoneNumber && 
             this.formData.department && 
             this.formData.signature && 
             this.hasSelectedService &&
             (!this.formData.services.internet || this.hasInternetPurposes) &&
             !this.hasPendingRequest // Prevent submission if there are pending requests
    },
    getSelectedServicesCount() {
      if (!this.submittedServices) return 0
      let count = 0
      if (this.submittedServices.jeeva) count++
      if (this.submittedServices.wellsoft) count++
      if (this.submittedServices.internet) count++
      return count
    }
  },

  async mounted() {
    console.log('UserCombinedAccessForm mounted - Route:', this.$route.path)
    // Load departments and check for pending requests when component mounts
    await Promise.all([
      this.loadDepartments(),
      this.checkPendingRequestStatus()
    ])
  },

  beforeUnmount() {
    console.log('UserCombinedAccessForm beforeUnmount - Route:', this.$route.path)
  },

  unmounted() {
    console.log('UserCombinedAccessForm unmounted')
  },

  methods: {
    // Real-time validation methods
    validatePfNumber() {
      const pfNumber = this.formData.pfNumber.trim()
      
      if (!pfNumber) {
        this.fieldErrors.pfNumber = 'PF Number is required'
        return false
      }
      
      if (pfNumber.length < 2) {
        this.fieldErrors.pfNumber = 'PF Number must be at least 2 characters'
        return false
      }
      
      if (!/^[A-Za-z0-9\-\/]+$/.test(pfNumber)) {
        this.fieldErrors.pfNumber = 'PF Number can only contain letters, numbers, hyphens, and slashes'
        return false
      }
      
      delete this.fieldErrors.pfNumber
      return true
    },

    validateStaffName() {
      const staffName = this.formData.staffName.trim()
      
      if (!staffName) {
        this.fieldErrors.staffName = 'Staff name is required'
        return false
      }
      
      if (staffName.length < 2) {
        this.fieldErrors.staffName = 'Staff name must be at least 2 characters'
        return false
      }
      
      if (!/^[a-zA-Z\s\.\-\']+$/.test(staffName)) {
        this.fieldErrors.staffName = 'Staff name can only contain letters, spaces, dots, hyphens, and apostrophes'
        return false
      }
      
      delete this.fieldErrors.staffName
      return true
    },

    validatePhoneNumber() {
      const phoneNumber = this.formData.phoneNumber.trim()
      
      if (!phoneNumber) {
        this.fieldErrors.phoneNumber = 'Phone number is required'
        return false
      }
      
      if (phoneNumber.length < 10) {
        this.fieldErrors.phoneNumber = 'Phone number must be at least 10 digits'
        return false
      }
      
      if (phoneNumber.length > 20) {
        this.fieldErrors.phoneNumber = 'Phone number must not exceed 20 characters'
        return false
      }
      
      if (!/^[\+]?[0-9\s\-\(\)]+$/.test(phoneNumber)) {
        this.fieldErrors.phoneNumber = 'Please enter a valid phone number'
        return false
      }
      
      delete this.fieldErrors.phoneNumber
      return true
    },

    validateDepartment() {
      if (!this.formData.department) {
        this.fieldErrors.department = 'Please select a department'
        return false
      }
      
      delete this.fieldErrors.department
      return true
    },

    validateServices() {
      if (!this.hasSelectedService) {
        this.fieldErrors.services = 'Please select at least one service'
        return false
      }
      
      delete this.fieldErrors.services
      return true
    },

    validateInternetPurposes() {
      if (this.formData.services.internet) {
        const validPurposes = this.formData.internetPurposes.filter(purpose => purpose.trim())
        
        if (validPurposes.length === 0) {
          this.fieldErrors.internetPurposes = 'Please provide at least one internet purpose'
          return false
        }
        
        // Validate each purpose length
        for (let i = 0; i < this.formData.internetPurposes.length; i++) {
          const purpose = this.formData.internetPurposes[i]
          if (purpose && purpose.length > 255) {
            this.fieldErrors.internetPurposes = `Purpose ${i + 1} must not exceed 255 characters`
            return false
          }
        }
      }
      
      delete this.fieldErrors.internetPurposes
      return true
    },

    validateSignature() {
      if (!this.formData.signature) {
        this.fieldErrors.signature = 'Digital signature is required'
        return false
      }
      
      delete this.fieldErrors.signature
      return true
    },

    // Field event handlers
    onPfNumberInput() {
      this.fieldTouched.pfNumber = true
      this.validatePfNumber()
    },

    onStaffNameInput() {
      this.fieldTouched.staffName = true
      this.validateStaffName()
    },

    onPhoneNumberInput() {
      this.fieldTouched.phoneNumber = true
      this.validatePhoneNumber()
    },

    onDepartmentChange() {
      this.fieldTouched.department = true
      this.validateDepartment()
    },

    onServiceChange() {
      this.fieldTouched.services = true
      this.validateServices()
      this.validateInternetPurposes() // Re-validate purposes when services change
    },

    onInternetPurposeInput() {
      this.fieldTouched.internetPurposes = true
      this.validateInternetPurposes()
    },

    // Check if a field is valid (used in template)
    isFieldValid(fieldName) {
      return this.fieldTouched[fieldName] && !this.fieldErrors[fieldName] && this.formData[fieldName]
    },

    // Get field validation CSS class (used in template)
    getFieldValidationClass(fieldName) {
      if (this.fieldTouched[fieldName] && this.fieldErrors[fieldName]) {
        return 'border-red-500 focus:border-red-500'
      }
      if (this.fieldTouched[fieldName] && !this.fieldErrors[fieldName] && this.formData[fieldName]) {
        return 'border-green-500 focus:border-green-500'
      }
      return ''
    },

    // Get field error class (legacy method)
    getFieldErrorClass(fieldName) {
      if (this.fieldTouched[fieldName] && this.fieldErrors[fieldName]) {
        return 'border-red-500 focus:border-red-500'
      }
      if (this.fieldTouched[fieldName] && !this.fieldErrors[fieldName]) {
        return 'border-green-500 focus:border-green-500'
      }
      return ''
    },

    async loadDepartments() {
      try {
        const response = await userCombinedAccessService.getDepartments()
        if (response.success) {
          this.departments = response.data
        }
      } catch (error) {
        console.error('Error loading departments:', error)
        this.showNotification('Failed to load departments', 'error')
      }
    },

    async checkPendingRequestStatus() {
      try {
        this.checkingPendingStatus = true
        const response = await userCombinedAccessService.checkPendingRequests()
        
        if (response.success) {
          this.hasPendingRequest = response.data.has_pending
          this.pendingRequestInfo = response.data.pending_request
          
          if (this.hasPendingRequest) {
            console.log('User has pending request:', this.pendingRequestInfo)
            this.showNotification(
              `You have a pending request (${this.pendingRequestInfo?.request_type || 'Unknown'}). Please wait for approval before submitting a new request.`,
              'warning'
            )
          }
        }
      } catch (error) {
        console.error('Error checking pending requests:', error)
        // Don't block the form if we can't check pending status
        this.hasPendingRequest = false
      } finally {
        this.checkingPendingStatus = false
      }
    },

    async submitForm() {
      // Clear previous validation errors
      this.validationErrors = {}
      
      // Check for pending requests before allowing submission
      if (this.hasPendingRequest) {
        this.showNotification(
          `You cannot submit a new request while you have a pending request. Please wait for approval.`,
          'error'
        )
        return
      }
      
      // Basic client-side validation
      if (!this.formData.pfNumber || !this.formData.staffName || !this.formData.department || !this.formData.phoneNumber) {
        this.showNotification('Please fill in all required applicant details', 'error')
        return
      }

      if (!this.formData.signature) {
        this.showNotification('Please upload your signature', 'error')
        return
      }

      if (!this.hasSelectedService) {
        this.showNotification('Please select at least one service', 'error')
        return
      }

      // Validate internet purposes if internet service is selected
      if (this.formData.services.internet && !this.formData.internetPurposes.some(purpose => purpose.trim())) {
        this.showNotification('Please provide at least one internet purpose', 'error')
        return
      }

      this.isSubmitting = true

      try {
        // 🔹 Create clean payload to avoid Proxy serialization issues
        const payload = {
          pfNumber: this.formData.pfNumber,
          staffName: this.formData.staffName,
          phoneNumber: this.formData.phoneNumber,
          department: this.formData.department,
          signature: this.formData.signature,
          services: {
            jeeva: this.formData.services.jeeva,
            wellsoft: this.formData.services.wellsoft,
            internet: this.formData.services.internet
          },
          internetPurposes: this.formData.internetPurposes
        }

        console.log('Payload being sent:', payload)
        console.log('Services object:', payload.services)

        // Submit to backend
        const response = await userCombinedAccessService.submitCombinedAccessRequest(payload)
        
        if (response.success) {
          console.log('Combined Access Form submitted successfully:', response.data)
          
          // Show success notification
          this.showNotification('Request submitted successfully! Redirecting to dashboard...', 'success')
          
          // Reset form
          this.resetForm()
          
          // Redirect to dashboard after a short delay
          setTimeout(() => {
            this.$router.replace('/user-dashboard').catch(err => {
              console.log('Navigation after submission:', err.name)
              // Fallback to window.location if router navigation fails
              window.location.href = '/user-dashboard'
            })
          }, 2000) // 2 second delay to show the success message
        } else {
          this.showNotification(response.message || 'Failed to submit request', 'error')
        }
      } catch (error) {
        console.error('Error submitting form:', error)
        
        if (error.type === 'validation') {
          this.validationErrors = error.errors
          console.error('Validation errors received:', error.errors)
          
          // Show specific validation errors
          const errorMessages = []
          Object.keys(error.errors).forEach(field => {
            if (Array.isArray(error.errors[field])) {
              errorMessages.push(...error.errors[field])
            } else {
              errorMessages.push(error.errors[field])
            }
          })
          
          this.showNotification(`Validation errors: ${errorMessages.join(', ')}`, 'error')
        } else {
          this.showNotification(error.message || 'Error submitting request. Please try again.', 'error')
        }
      } finally {
        this.isSubmitting = false
      }
    },
    
    resetForm() {
      this.formData = {
        pfNumber: '',
        staffName: '',
        department: '',
        phoneNumber: '',
        signature: null,
        services: {
          jeeva: false,
          wellsoft: false,
          internet: false
        },
        internetPurposes: ['', '', '', '']
      }
      this.validationErrors = {}
      this.fieldErrors = {}
      this.fieldTouched = {}
      this.submittedServices = null
      this.clearSignature()
    },

    triggerFileUpload() {
      this.$refs.signatureInput.click()
    },

    onSignatureChange(e) {
      const file = e.target.files[0]
      this.formData.signature = file || null
      this.fieldTouched.signature = true
      
      if (!file) {
        this.signaturePreview = ''
        this.signatureFileName = ''
        this.validateSignature()
        return
      }

      // Validate file type
      const allowedTypes = ['image/png', 'image/jpeg', 'image/jpg']
      if (!allowedTypes.includes(file.type)) {
        this.fieldErrors.signature = 'Please select a valid image file (PNG, JPG, or JPEG)'
        this.showNotification('Please select a valid image file (PNG, JPG, or JPEG)', 'error')
        this.clearSignature()
        return
      }

      // Validate file size (max 5MB)
      if (file.size > 5 * 1024 * 1024) {
        this.fieldErrors.signature = 'File size must be less than 5MB'
        this.showNotification('File size must be less than 5MB', 'error')
        this.clearSignature()
        return
      }

      this.signatureFileName = file.name
      
      // Preview image
      const reader = new FileReader()
      reader.onload = () => {
        this.signaturePreview = reader.result
      }
      reader.readAsDataURL(file)
      
      // Validate after successful file selection
      this.validateSignature()
    },

    isImage(preview) {
      return typeof preview === 'string' && preview !== 'pdf'
    },

    clearSignature() {
      this.formData.signature = null
      this.signaturePreview = ''
      this.signatureFileName = ''
      this.fieldTouched.signature = false
      delete this.fieldErrors.signature
      if (this.$refs.signatureInput) {
        this.$refs.signatureInput.value = ''
      }
    },

    goBack() {
      console.log('goBack called - Current route:', this.$route.path)
      console.log('goBack called - Target route: /user-dashboard')
      
      // Force navigation to user dashboard with replace to avoid history issues
      this.$router.replace('/user-dashboard').then(() => {
        console.log('Navigation successful to:', this.$route.path)
      }).catch(err => {
        console.log('Navigation failed:', err.name, err.message)
        // Handle navigation failures gracefully
        if (err.name === 'NavigationDuplicated') {
          // User is already on the target route, force a page reload
          console.log('Already on user dashboard, forcing reload')
          window.location.href = '/user-dashboard'
        } else if (err.name === 'NavigationAborted' || err.name === 'NavigationCancelled') {
          // Navigation was aborted or cancelled, try alternative approach
          console.log('Navigation was cancelled, trying alternative approach')
          // Try using window.location as fallback
          window.location.href = '/user-dashboard'
        } else {
          // Only log actual errors and provide fallback
          console.error('Navigation error:', err)
          // Fallback to window.location
          window.location.href = '/user-dashboard'
        }
      })
    },

    closeSuccessModal() {
      this.showSuccessModal = false
      // Use replace to ensure proper navigation
      this.$router.replace('/user-dashboard').catch(err => {
        console.log('Navigation from success modal:', err.name)
        // Fallback to window.location if router navigation fails
        window.location.href = '/user-dashboard'
      })
    },

    showNotification(message, type = 'info') {
      // Simple notification - you can replace with a proper notification system
      const colors = {
        success: 'green',
        error: 'red',
        warning: 'yellow',
        info: 'blue'
      }
      
      // Create a simple toast notification
      const toast = document.createElement('div')
      toast.className = `fixed top-4 right-4 px-6 py-3 rounded-lg text-white font-semibold z-50 bg-${colors[type]}-600 transform transition-all duration-300 max-w-md`
      toast.innerHTML = `
        <div class="flex items-start space-x-3">
          <i class="fas ${
            type === 'success' ? 'fa-check-circle' :
            type === 'error' ? 'fa-exclamation-circle' :
            type === 'warning' ? 'fa-exclamation-triangle' :
            'fa-info-circle'
          } text-lg mt-0.5"></i>
          <div class="flex-1">
            <p class="text-sm leading-relaxed">${message}</p>
          </div>
        </div>
      `
      document.body.appendChild(toast)
      
      // Animate in
      setTimeout(() => {
        toast.style.transform = 'translateX(0)'
      }, 100)
      
      // Remove after longer time for warnings
      const duration = type === 'warning' ? 6000 : 3000
      setTimeout(() => {
        toast.style.transform = 'translateX(100%)'
        setTimeout(() => {
          if (document.body.contains(toast)) {
            document.body.removeChild(toast)
          }
        }, 300)
      }, duration)
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
  border-color: rgba(59, 130, 246, 0.8);
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
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

/* Custom transitions and animations */
.transition-all {
  transition: all 0.3s ease;
}

/* Hover effects */
.transform:hover {
  transform: translateY(-2px) scale(1.02);
}

/* Focus styles */
input:focus,
select:focus,
textarea:focus {
  transform: translateY(-1px);
  box-shadow: 0 8px 25px rgba(59, 130, 246, 0.15);
}

/* Animation for form sections */
.border-l-4 {
  animation: slideInLeft 0.6s ease-out;
}

@keyframes slideInLeft {
  from {
    opacity: 0;
    transform: translateX(-30px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

/* Hover effects for form sections */
.border-l-4:hover {
  transform: translateX(4px);
  transition: transform 0.3s ease;
}

/* Button animations */
button:hover:not(:disabled) {
  transform: translateY(-2px) scale(1.05);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
}

button:active:not(:disabled) {
  transform: translateY(0) scale(1.02);
}

/* Loading spinner */
@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

.fa-spin {
  animation: spin 1s linear infinite;
}

/* Modal animations */
@keyframes modalFadeIn {
  from {
    opacity: 0;
    transform: scale(0.9) translateY(-20px);
  }
  to {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}

.fixed.inset-0 {
  animation: modalFadeIn 0.3s ease-out;
}

/* Service card hover effects */
/* Enhanced button hover effects */
label:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
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

/* Checkbox animations */
input[type="checkbox"]:checked + label {
  transform: scale(1.02);
}

/* File input styling */
input[type="file"]::-webkit-file-upload-button {
  background: linear-gradient(135deg, #3b82f6, #1d4ed8);
  border: none;
  color: white;
  padding: 8px 16px;
  border-radius: 20px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

input[type="file"]::-webkit-file-upload-button:hover {
  background: linear-gradient(135deg, #1d4ed8, #1e40af);
  transform: translateY(-1px);
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .grid-cols-2 {
    grid-template-columns: repeat(1, minmax(0, 1fr));
  }
  
  .md\:col-span-2 {
    grid-column: span 1;
  }
  
  .px-8 {
    padding-left: 1rem;
    padding-right: 1rem;
  }

  .grid-cols-1.md\:grid-cols-3 {
    grid-template-columns: repeat(1, minmax(0, 1fr));
  }
}

/* Print styles */
@media print {
  .min-h-screen {
    min-height: auto;
  }
  
  button {
    display: none !important;
  }
  
  .shadow-xl {
    box-shadow: none;
  }
  
  .bg-gradient-to-br {
    background: white !important;
  }
  
  .rounded-t-2xl,
  .rounded-b-2xl {
    border-radius: 0 !important;
  }
  
  .border-l-4 {
    border-left: 3px solid #000 !important;
  }
  
  .bg-gray-50 {
    background: #f9f9f9 !important;
  }
}

/* Custom scrollbar */
::-webkit-scrollbar {
  width: 8px;
}

::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 4px;
}

::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
  background: #a1a1a1;
}

/* Focus styles for accessibility */
button:focus {
  outline: none;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
}

/* Disabled button styles */
button:disabled {
  transform: none !important;
  box-shadow: none !important;
}

/* Input validation styles */
input:invalid {
  border-color: #3b82f6;
}

input:valid {
  border-color: #10b981;
}

/* Override validation styles for Internet Purpose fields */
.medical-input:valid {
  border-color: rgba(96, 165, 250, 0.7) !important;
}

.medical-input:invalid {
  border-color: rgba(96, 165, 250, 0.7) !important;
}

/* Smooth transitions for all interactive elements */
* {
  transition: all 0.2s ease;
}

/* Service selection cards */
.service-card {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.service-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}
</style>