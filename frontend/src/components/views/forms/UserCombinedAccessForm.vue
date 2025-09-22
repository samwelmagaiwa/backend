<template>
  <div class="flex flex-col h-screen">
    <AppHeader />
    <div class="flex flex-1 overflow-hidden">
      <ModernSidebar />
      <main
        class="flex-1 p-3 bg-gradient-to-br from-blue-900 via-blue-800 to-teal-900 overflow-y-auto relative"
      >
        <!-- Medical Background Pattern -->
        <div class="absolute inset-0 overflow-hidden">
          <!-- Medical Cross Pattern -->
          <div class="absolute inset-0 opacity-5">
            <div class="grid grid-cols-12 gap-8 h-full transform rotate-45">
              <div
                v-for="i in 48"
                :key="i"
                class="bg-white rounded-full w-2 h-2 animate-pulse"
                :style="{ animationDelay: i * 0.1 + 's' }"
              ></div>
            </div>
          </div>
          <!-- Floating medical icons -->
          <div class="absolute inset-0">
            <div
              v-for="i in 15"
              :key="i"
              class="absolute text-white opacity-10 animate-float"
              :style="{
                left: Math.random() * 100 + '%',
                top: Math.random() * 100 + '%',
                animationDelay: Math.random() * 3 + 's',
                animationDuration: Math.random() * 3 + 2 + 's',
                fontSize: Math.random() * 20 + 10 + 'px'
              }"
            >
              <i
                :class="[
                  'fas',
                  ['fa-heartbeat', 'fa-user-md', 'fa-hospital', 'fa-stethoscope', 'fa-plus'][
                    Math.floor(Math.random() * 5)
                  ]
                ]"
              ></i>
            </div>
          </div>
        </div>

        <div class="max-w-12xl mx-auto relative z-10">
          <!-- Header Section -->
          <div class="medical-glass-card rounded-t-3xl p-4 mb-0 border-b border-blue-300/30">
            <div class="flex justify-between items-center">
              <!-- Left Logo -->
              <div
                class="w-20 h-20 mr-4 transform hover:scale-110 transition-transform duration-300"
              >
                <div
                  class="w-full h-full bg-gradient-to-br from-blue-500/20 to-teal-500/20 rounded-xl backdrop-blur-sm border-2 border-blue-300/40 flex items-center justify-center shadow-2xl hover:shadow-blue-500/25"
                >
                  <img
                    src="/assets/images/ngao2.png"
                    alt="National Shield"
                    class="max-w-14 max-h-14 object-contain"
                  />
                </div>
              </div>

              <!-- Center Content -->
              <div class="text-center flex-1">
                <h1
                  class="text-xl font-bold text-white mb-2 tracking-wide drop-shadow-lg animate-fade-in"
                >
                  MUHIMBILI NATIONAL HOSPITAL
                </h1>
                <div class="relative inline-block mb-2">
                  <div
                    class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-2 rounded-full text-base font-bold shadow-2xl transform hover:scale-105 transition-all duration-300 border-2 border-blue-400/60"
                  >
                    <span class="relative z-10 flex items-center gap-2">
                      <i :class="isEditMode ? 'fas fa-edit' : 'fas fa-layer-group'"></i>
                      {{ isEditMode ? 'EDIT COMBINED ACCESS REQUEST' : 'COMBINED ACCESS REQUEST' }}
                    </span>
                    <div
                      class="absolute inset-0 bg-gradient-to-r from-blue-700 to-blue-800 rounded-full opacity-0 hover:opacity-100 transition-opacity duration-300"
                    ></div>
                  </div>
                </div>
                <h2
                  class="text-base font-bold text-blue-100 tracking-wide drop-shadow-md animate-fade-in-delay"
                >
                  {{ isEditMode ? 'MODIFY AND RESUBMIT REQUEST' : 'COMPREHENSIVE SYSTEM ACCESS' }}
                </h2>
              </div>

              <!-- Right Logo -->
              <div
                class="w-20 h-20 ml-4 transform hover:scale-110 transition-transform duration-300"
              >
                <div
                  class="w-full h-full bg-gradient-to-br from-teal-500/20 to-blue-500/20 rounded-xl backdrop-blur-sm border-2 border-teal-300/40 flex items-center justify-center shadow-2xl hover:shadow-teal-500/25"
                >
                  <img
                    src="/assets/images/logo2.png"
                    alt="Muhimbili Logo"
                    class="max-w-14 max-h-14 object-contain"
                  />
                </div>
              </div>
            </div>
          </div>

          <!-- Main Form -->
          <div class="medical-glass-card rounded-b-3xl overflow-hidden">
            <form @submit.prevent="submitForm" class="p-4 space-y-4">
              <!-- Form Description -->

              <!-- Applicant Details Section -->
              <div
                class="medical-card bg-gradient-to-r from-blue-600/25 to-blue-700/25 border-2 border-blue-400/40 p-5 rounded-xl backdrop-blur-sm hover:shadow-2xl hover:shadow-blue-500/20 transition-all duration-500 group relative overflow-hidden"
              >
                <!-- Animated Background Layers -->
                <div
                  class="absolute inset-0 bg-gradient-to-r from-blue-500/10 via-blue-500/5 to-blue-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-700"
                ></div>
                <div
                  class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-blue-400/20 to-transparent rounded-full blur-2xl group-hover:scale-150 transition-transform duration-1000"
                ></div>
                <div
                  class="absolute bottom-0 left-0 w-24 h-24 bg-gradient-to-tr from-blue-400/15 to-transparent rounded-full blur-xl group-hover:scale-125 transition-transform duration-800"
                ></div>

                <div class="relative z-10">
                  <div class="flex items-center space-x-3 mb-4">
                    <div
                      class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-blue-300/50 relative overflow-hidden"
                    >
                      <div
                        class="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent rounded-lg"
                      ></div>
                      <i class="fas fa-user-md text-white text-lg relative z-10 drop-shadow-lg"></i>
                      <div
                        class="absolute top-1 right-1 w-1 h-1 bg-white/60 rounded-full animate-ping"
                      ></div>
                    </div>
                    <div class="flex-1">
                      <h3 class="text-lg font-bold text-white flex items-center">
                        <i class="fas fa-id-card mr-2 text-blue-300"></i>
                        Applicant Details
                      </h3>
                      <div class="flex items-center space-x-2 mt-1">
                        <span
                          class="text-xs text-red-400 font-medium bg-red-500/20 px-2 py-1 rounded-full border border-red-400/30"
                        >
                          <i class="fas fa-asterisk mr-1 text-xs"></i>
                          Required Information
                        </span>
                        <!-- Auto-population status indicator -->
                        <span
                          v-if="isLoadingProfile"
                          class="text-sm text-blue-300 font-medium bg-blue-500/20 px-2 py-1 rounded-full border border-blue-400/30 animate-pulse"
                        >
                          <i class="fas fa-spinner fa-spin mr-1 text-xs"></i>
                          Loading your details...
                        </span>
                        <span
                          v-else-if="autoPopulated"
                          class="text-sm text-green-300 font-medium bg-green-500/20 px-2 py-1 rounded-full border border-green-400/30"
                        >
                          <i class="fas fa-check mr-1 text-xs"></i>
                          Auto-populated
                        </span>
                        <span
                          v-else-if="profileLoadError"
                          class="text-xs text-yellow-300 font-medium bg-yellow-500/20 px-2 py-1 rounded-full border border-yellow-400/30"
                        >
                          <i class="fas fa-exclamation-triangle mr-1 text-xs"></i>
                          Manual entry required
                        </span>
                      </div>
                    </div>
                  </div>

                  <!-- Attractive Field Layout -->
                  <div class="space-y-3 mb-3">
                    <!-- Row 1: Basic Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                      <!-- PF Number -->
                      <div class="group">
                        <label
                          class="block text-base font-bold text-blue-100 mb-2 flex items-center"
                        >
                          <i class="fas fa-id-badge mr-2 text-blue-300"></i>
                          PF Number <span class="text-red-400 ml-1">*</span>
                        </label>
                        <div class="relative">
                          <input
                            v-model="formData.pfNumber"
                            type="text"
                            class="medical-input w-full px-3 py-2 bg-white/15 border-2 border-blue-300/30 rounded-lg focus:border-blue-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-blue-500/20 group-hover:border-blue-400/50"
                            :placeholder="
                              isLoadingProfile ? 'Loading your PF Number...' : 'Enter PF Number'
                            "
                            :disabled="isLoadingProfile"
                            required
                          />
                          <div
                            class="absolute inset-0 rounded-lg bg-gradient-to-r from-blue-500/10 to-blue-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"
                          ></div>
                          <div
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-blue-300/50"
                          >
                            <i v-if="isLoadingProfile" class="fas fa-spinner fa-spin"></i>
                            <i
                              v-else-if="autoPopulated && formData.pfNumber"
                              class="fas fa-check text-green-400"
                            ></i>
                            <i v-else class="fas fa-hashtag"></i>
                          </div>
                        </div>
                        <p class="text-xs text-blue-200/60 mt-1 italic flex items-center">
                          <i class="fas fa-info-circle mr-1"></i>
                          e.g. 12345
                        </p>
                      </div>

                      <!-- Staff Name -->
                      <div class="group">
                        <label
                          class="block text-base font-bold text-blue-100 mb-2 flex items-center"
                        >
                          <i class="fas fa-user mr-2 text-blue-300"></i>
                          Staff Name <span class="text-red-400 ml-1">*</span>
                        </label>
                        <div class="relative">
                          <input
                            v-model="formData.staffName"
                            type="text"
                            class="medical-input w-full px-4 py-2 bg-white/15 border-2 border-blue-300/30 rounded-xl focus:border-blue-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-blue-500/20 group-hover:border-blue-400/50"
                            :placeholder="
                              isLoadingProfile ? 'Loading your name...' : 'Enter full name'
                            "
                            :disabled="isLoadingProfile"
                            required
                          />
                          <div
                            class="absolute inset-0 rounded-xl bg-gradient-to-r from-blue-500/10 to-blue-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"
                          ></div>
                          <div
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-blue-300/50"
                          >
                            <i v-if="isLoadingProfile" class="fas fa-spinner fa-spin"></i>
                            <i
                              v-else-if="autoPopulated && formData.staffName"
                              class="fas fa-check text-green-400"
                            ></i>
                            <i v-else class="fas fa-user-circle"></i>
                          </div>
                        </div>
                        <p class="text-xs text-blue-200/60 mt-1 italic flex items-center">
                          <i class="fas fa-info-circle mr-1"></i>
                          e.g. Jane Doe
                        </p>
                      </div>
                    </div>

                    <!-- Row 2: Contact & Department -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                      <!-- Phone Number -->
                      <div class="group">
                        <label
                          class="block text-base font-bold text-blue-100 mb-2 flex items-center"
                        >
                          <i class="fas fa-phone mr-2 text-blue-300"></i>
                          Phone Number <span class="text-red-400 ml-1">*</span>
                        </label>
                        <div class="relative">
                          <input
                            v-model="formData.phoneNumber"
                            type="tel"
                            class="medical-input w-full px-4 py-2 bg-white/15 border-2 border-blue-300/30 rounded-xl focus:border-blue-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-blue-500/20 group-hover:border-blue-400/50"
                            :placeholder="
                              isLoadingProfile ? 'Loading your phone...' : 'Enter phone number'
                            "
                            :disabled="isLoadingProfile"
                            required
                          />
                          <div
                            class="absolute inset-0 rounded-xl bg-gradient-to-r from-blue-500/10 to-blue-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"
                          ></div>
                          <div
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-blue-300/50"
                          >
                            <i v-if="isLoadingProfile" class="fas fa-spinner fa-spin"></i>
                            <i
                              v-else-if="autoPopulated && formData.phoneNumber"
                              class="fas fa-check text-green-400"
                            ></i>
                            <i v-else class="fas fa-mobile-alt"></i>
                          </div>
                        </div>
                        <p class="text-xs text-blue-200/60 mt-1 italic flex items-center">
                          <i class="fas fa-info-circle mr-1"></i>
                          e.g. 0712 000 000
                        </p>
                      </div>

                      <!-- Department -->
                      <div class="group">
                        <label
                          class="block text-base font-bold text-blue-100 mb-2 flex items-center"
                        >
                          <i class="fas fa-building mr-2 text-blue-300"></i>
                          Department <span class="text-red-400 ml-1">*</span>
                        </label>
                        <div class="relative">
                          <select
                            v-model="formData.department"
                            class="medical-input w-full px-4 py-2 bg-white/15 border-2 border-blue-300/30 rounded-xl focus:border-blue-400 focus:outline-none text-white backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-blue-500/20 appearance-none cursor-pointer group-hover:border-blue-400/50"
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
                          <div
                            class="absolute inset-0 rounded-xl bg-gradient-to-r from-blue-500/10 to-blue-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"
                          ></div>
                        </div>
                        <p class="text-xs text-blue-200/60 mt-1 italic flex items-center">
                          <i class="fas fa-info-circle mr-1"></i>
                          Select your department
                        </p>
                      </div>
                    </div>

                    <!-- Row 3: Digital Signature (Full Width) -->
                    <div
                      class="bg-gradient-to-r from-blue-500/10 to-blue-500/10 p-3 rounded-lg border border-blue-300/20 backdrop-blur-sm"
                    >
                      <div class="group">
                        <label
                          class="block text-base font-bold text-blue-100 mb-2 flex items-center"
                        >
                          <i class="fas fa-signature mr-2 text-blue-300"></i>
                          Digital Signature
                          <span class="text-red-400 ml-1">*</span>
                          <span class="ml-2 text-sm text-blue-300/70 font-normal"
                            >(PNG, JPG, JPEG)</span
                          >
                        </label>

                        <div class="flex flex-col md:flex-row gap-3 items-start">
                          <!-- Signature Display Box -->
                          <div
                            class="relative w-full md:w-64 h-14 border-2 border-blue-300/30 rounded-lg bg-blue-100/20 focus-within:bg-blue-100/30 focus-within:border-blue-400 overflow-hidden backdrop-blur-sm group-hover:border-blue-400/50 transition-all duration-300"
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
                              @click="clearSignature"
                              class="absolute top-1 right-1 w-5 h-5 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600 transition duration-200 text-xs shadow-lg"
                              title="Remove signature"
                            >
                              <i class="fas fa-times"></i>
                            </button>
                          </div>

                          <!-- Upload Button -->
                          <div class="flex flex-col gap-1">
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
                              class="inline-flex items-center px-3 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white text-sm font-medium rounded-lg hover:from-blue-600 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 transition duration-200 shadow-lg hover:shadow-xl transform hover:scale-105 border border-blue-400/30"
                            >
                              <span class="flex items-center">
                                <i class="fas fa-upload mr-1"></i>
                                Upload Signature
                              </span>
                            </button>

                            <p class="text-xs text-blue-200/70 italic">Max: 5MB</p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Module Selection Section -->
              <div
                class="medical-card bg-gradient-to-r from-blue-600/25 to-blue-700/25 border-2 border-blue-400/40 p-5 rounded-xl backdrop-blur-sm hover:shadow-2xl hover:shadow-blue-500/20 transition-all duration-500 group relative overflow-hidden"
              >
                <!-- Animated Background Layers -->
                <div
                  class="absolute inset-0 bg-gradient-to-r from-blue-500/10 via-blue-500/5 to-blue-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-700"
                ></div>
                <div
                  class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-blue-400/20 to-transparent rounded-full blur-2xl group-hover:scale-150 transition-transform duration-1000"
                ></div>
                <div
                  class="absolute bottom-0 left-0 w-24 h-24 bg-gradient-to-tr from-blue-400/15 to-transparent rounded-full blur-xl group-hover:scale-125 transition-transform duration-800"
                ></div>

                <div class="relative z-10">
                  <div class="flex items-center space-x-3 mb-4">
                    <div
                      class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-blue-300/50 relative overflow-hidden"
                    >
                      <div
                        class="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent rounded-lg"
                      ></div>
                      <i
                        class="fas fa-layer-group text-white text-lg relative z-10 drop-shadow-lg"
                      ></i>
                      <div
                        class="absolute top-1 right-1 w-1 h-1 bg-white/60 rounded-full animate-ping"
                      ></div>
                    </div>
                    <div>
                      <h3 class="text-lg font-bold text-white flex items-center">
                        <i class="fas fa-cogs mr-2 text-blue-300"></i>
                        Select Services
                      </h3>
                      <p class="text-sm text-blue-100/80">
                        Choose which services you need access to
                      </p>
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
                      />
                      <label
                        for="jeeva-service"
                        class="block p-4 border-2 rounded-xl cursor-pointer transition-all duration-300 hover:shadow-xl backdrop-blur-sm relative overflow-hidden transform hover:scale-105"
                        :class="
                          formData.services.jeeva
                            ? 'border-blue-400 bg-blue-500/20 shadow-lg shadow-blue-500/25'
                            : 'border-white/30 bg-white/10 hover:border-blue-300/50 hover:bg-blue-500/10'
                        "
                      >
                        <!-- Service Card Background Effects -->
                        <div
                          class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-transparent opacity-0 group-hover/service:opacity-100 transition-opacity duration-300"
                        ></div>
                        <div
                          class="absolute top-0 right-0 w-16 h-16 bg-gradient-to-br from-blue-400/20 to-transparent rounded-full blur-xl"
                        ></div>

                        <div class="relative z-10">
                          <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center">
                              <div
                                class="w-10 h-10 rounded-lg flex items-center justify-center mr-3 transition-all duration-300 relative overflow-hidden"
                                :class="
                                  formData.services.jeeva
                                    ? 'bg-blue-500 text-white shadow-lg'
                                    : 'bg-white/20 text-blue-300'
                                "
                              >
                                <div
                                  v-if="formData.services.jeeva"
                                  class="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent"
                                ></div>
                                <i class="fas fa-file-medical text-lg relative z-10"></i>
                              </div>
                              <div>
                                <h4 class="font-bold text-white text-base">Jeeva Access</h4>
                                <p class="text-xs text-blue-200/80">Medical records system</p>
                              </div>
                            </div>
                            <div
                              class="w-5 h-5 rounded border-2 flex items-center justify-center transition-all duration-300"
                              :class="
                                formData.services.jeeva
                                  ? 'border-blue-400 bg-blue-500 shadow-lg'
                                  : 'border-white/40'
                              "
                            >
                              <i
                                v-if="formData.services.jeeva"
                                class="fas fa-check text-white text-sm"
                              ></i>
                            </div>
                          </div>

                          <!-- Service Features -->
                          <div class="space-y-1">
                            <div class="flex items-center text-xs text-blue-100/70">
                              <i class="fas fa-check-circle mr-1 text-blue-400"></i>
                              Patient Records
                            </div>
                            <div class="flex items-center text-xs text-blue-100/70">
                              <i class="fas fa-check-circle mr-1 text-blue-400"></i>
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
                      />
                      <label
                        for="wellsoft-service"
                        class="block p-4 border-2 rounded-xl cursor-pointer transition-all duration-300 hover:shadow-xl backdrop-blur-sm relative overflow-hidden transform hover:scale-105"
                        :class="
                          formData.services.wellsoft
                            ? 'border-blue-400 bg-blue-500/20 shadow-lg shadow-blue-500/25'
                            : 'border-white/30 bg-white/10 hover:border-blue-300/50 hover:bg-blue-500/10'
                        "
                      >
                        <!-- Service Card Background Effects -->
                        <div
                          class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-transparent opacity-0 group-hover/service:opacity-100 transition-opacity duration-300"
                        ></div>
                        <div
                          class="absolute top-0 right-0 w-16 h-16 bg-gradient-to-br from-blue-400/20 to-transparent rounded-full blur-xl"
                        ></div>

                        <div class="relative z-10">
                          <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center">
                              <div
                                class="w-10 h-10 rounded-lg flex items-center justify-center mr-3 transition-all duration-300 relative overflow-hidden"
                                :class="
                                  formData.services.wellsoft
                                    ? 'bg-blue-500 text-white shadow-lg'
                                    : 'bg-white/20 text-blue-300'
                                "
                              >
                                <div
                                  v-if="formData.services.wellsoft"
                                  class="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent"
                                ></div>
                                <i class="fas fa-laptop-medical text-lg relative z-10"></i>
                              </div>
                              <div>
                                <h4 class="font-bold text-white text-base">Wellsoft Access</h4>
                                <p class="text-xs text-blue-200/80">Hospital management</p>
                              </div>
                            </div>
                            <div
                              class="w-5 h-5 rounded border-2 flex items-center justify-center transition-all duration-300"
                              :class="
                                formData.services.wellsoft
                                  ? 'border-blue-400 bg-blue-500 shadow-lg'
                                  : 'border-white/40'
                              "
                            >
                              <i
                                v-if="formData.services.wellsoft"
                                class="fas fa-check text-white text-sm"
                              ></i>
                            </div>
                          </div>

                          <!-- Service Features -->
                          <div class="space-y-1">
                            <div class="flex items-center text-xs text-blue-100/70">
                              <i class="fas fa-check-circle mr-1 text-blue-400"></i>
                              Patient Management
                            </div>
                            <div class="flex items-center text-xs text-blue-100/70">
                              <i class="fas fa-check-circle mr-1 text-blue-400"></i>
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
                      />
                      <label
                        for="internet-service"
                        class="block p-4 border-2 rounded-xl cursor-pointer transition-all duration-300 hover:shadow-xl backdrop-blur-sm relative overflow-hidden transform hover:scale-105"
                        :class="
                          formData.services.internet
                            ? 'border-blue-400 bg-blue-500/20 shadow-lg shadow-blue-500/25'
                            : 'border-white/30 bg-white/10 hover:border-blue-300/50 hover:bg-blue-500/10'
                        "
                      >
                        <!-- Service Card Background Effects -->
                        <div
                          class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-transparent opacity-0 group-hover/service:opacity-100 transition-opacity duration-300"
                        ></div>
                        <div
                          class="absolute top-0 right-0 w-16 h-16 bg-gradient-to-br from-blue-400/20 to-transparent rounded-full blur-xl"
                        ></div>

                        <div class="relative z-10">
                          <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center">
                              <div
                                class="w-10 h-10 rounded-lg flex items-center justify-center mr-3 transition-all duration-300 relative overflow-hidden"
                                :class="
                                  formData.services.internet
                                    ? 'bg-blue-500 text-white shadow-lg'
                                    : 'bg-white/20 text-blue-300'
                                "
                              >
                                <div
                                  v-if="formData.services.internet"
                                  class="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent"
                                ></div>
                                <i class="fas fa-wifi text-lg relative z-10"></i>
                              </div>
                              <div>
                                <h4 class="font-bold text-white text-base">Internet Access</h4>
                                <p class="text-xs text-blue-200/80">Internet connectivity</p>
                              </div>
                            </div>
                            <div
                              class="w-5 h-5 rounded border-2 flex items-center justify-center transition-all duration-300"
                              :class="
                                formData.services.internet
                                  ? 'border-blue-400 bg-blue-500 shadow-lg'
                                  : 'border-white/40'
                              "
                            >
                              <i
                                v-if="formData.services.internet"
                                class="fas fa-check text-white text-sm"
                              ></i>
                            </div>
                          </div>

                          <!-- Service Features -->
                          <div class="space-y-1">
                            <div class="flex items-center text-xs text-blue-100/70">
                              <i class="fas fa-check-circle mr-1 text-blue-400"></i>
                              Web Access
                            </div>
                            <div class="flex items-center text-xs text-blue-100/70">
                              <i class="fas fa-check-circle mr-1 text-blue-400"></i>
                              Email & Communication
                            </div>
                          </div>
                        </div>
                      </label>
                    </div>
                  </div>

                  <!-- Service Selection Validation -->
                  <div
                    v-if="!hasSelectedService"
                    class="mt-6 p-4 bg-red-500/20 border-2 border-red-400/40 rounded-xl backdrop-blur-sm"
                  >
                    <p class="text-red-300 text-sm font-medium flex items-center">
                      <i class="fas fa-exclamation-triangle mr-2 text-red-400"></i>
                      Please select at least one service to continue.
                    </p>
                  </div>
                </div>
              </div>

              <!-- Internet Purpose Section (Only show when Internet is selected) -->
              <div
                v-if="formData.services.internet"
                class="medical-card bg-gradient-to-r from-blue-600/25 to-blue-700/25 border-2 border-blue-400/40 p-5 rounded-xl backdrop-blur-sm hover:shadow-2xl hover:shadow-blue-500/20 transition-all duration-500 group relative overflow-hidden"
              >
                <!-- Animated Background Layers -->
                <div
                  class="absolute inset-0 bg-gradient-to-r from-blue-500/10 via-blue-500/5 to-blue-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-700"
                ></div>
                <div
                  class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-blue-400/20 to-transparent rounded-full blur-2xl group-hover:scale-150 transition-transform duration-1000"
                ></div>
                <div
                  class="absolute bottom-0 left-0 w-24 h-24 bg-gradient-to-tr from-blue-400/15 to-transparent rounded-full blur-xl group-hover:scale-125 transition-transform duration-800"
                ></div>

                <div class="relative z-10">
                  <div class="flex items-center space-x-3 mb-4">
                    <div
                      class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-blue-300/50 relative overflow-hidden"
                    >
                      <div
                        class="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent rounded-lg"
                      ></div>
                      <i class="fas fa-wifi text-white text-lg relative z-10 drop-shadow-lg"></i>
                      <div
                        class="absolute top-1 right-1 w-1 h-1 bg-white/60 rounded-full animate-ping"
                      ></div>
                    </div>
                    <div>
                      <h3 class="text-lg font-bold text-white flex items-center">
                        <i class="fas fa-globe mr-2 text-blue-300"></i>
                        Internet Purpose
                      </h3>
                      <p class="text-sm text-blue-100/80">
                        Specify the purposes for internet access
                      </p>
                    </div>
                  </div>

                  <div class="space-y-3">
                    <div
                      v-for="(purpose, index) in formData.internetPurposes"
                      :key="index"
                      class="flex items-center gap-2"
                    >
                      <div
                        class="flex items-center justify-center w-6 h-6 bg-blue-500/20 text-blue-400 rounded-full font-bold text-xs border border-blue-400/30 shadow-sm"
                      >
                        {{ index + 1 }}.
                      </div>
                      <input
                        v-model="formData.internetPurposes[index]"
                        type="text"
                        class="medical-input flex-1 px-3 py-2 bg-white/15 border-2 border-blue-300/30 rounded-lg focus:border-blue-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-blue-500/20"
                        :placeholder="`Purpose ${index + 1}`"
                        :required="index === 0 && formData.services.internet"
                      />
                    </div>
                    <p class="text-xs text-blue-200 mt-2 italic">
                      <i class="fas fa-info-circle mr-1"></i>
                      Please provide at least one purpose for internet access. You can add up to 4
                      purposes.
                    </p>
                  </div>
                </div>
              </div>

              <!-- Form Actions -->
              <div
                class="medical-card bg-gradient-to-r from-indigo-600/25 to-purple-600/25 border-2 border-indigo-400/40 p-5 rounded-xl backdrop-blur-sm hover:shadow-2xl hover:shadow-indigo-500/20 transition-all duration-500 group relative overflow-hidden"
              >
                <!-- Animated Background Layers -->
                <div
                  class="absolute inset-0 bg-gradient-to-r from-indigo-500/10 via-purple-500/5 to-indigo-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-700"
                ></div>
                <div
                  class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-indigo-400/20 to-transparent rounded-full blur-2xl group-hover:scale-150 transition-transform duration-1000"
                ></div>
                <div
                  class="absolute bottom-0 left-0 w-24 h-24 bg-gradient-to-tr from-purple-400/15 to-transparent rounded-full blur-xl group-hover:scale-125 transition-transform duration-800"
                ></div>

                <div class="relative z-10">
                  <div class="flex justify-between items-center">
                    <button
                      type="button"
                      @click="goBack"
                      class="px-5 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-all duration-300 font-semibold flex items-center shadow-lg hover:shadow-xl text-sm transform hover:scale-105"
                    >
                      <i class="fas fa-arrow-left mr-2"></i>
                      Back to Dashboard
                    </button>
                    <button
                      type="submit"
                      :disabled="isSubmitting || !hasSelectedService"
                      class="px-5 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-300 font-semibold flex items-center shadow-lg hover:shadow-xl text-sm transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                    >
                      <i v-if="isSubmitting" class="fas fa-spinner fa-spin mr-2"></i>
                      <i
                        v-else
                        :class="isEditMode ? 'fas fa-save' : 'fas fa-paper-plane'"
                        class="mr-2"
                      ></i>
                      {{
                        isSubmitting
                          ? isEditMode
                            ? 'Updating...'
                            : 'Submitting...'
                          : isEditMode
                            ? 'Update & Resubmit'
                            : 'Submit Request'
                      }}
                    </button>
                  </div>
                </div>
              </div>
            </form>

            <!-- Footer -->
            <AppFooter />
          </div>
        </div>
      </main>
    </div>

    <!-- Success Modal -->
    <div
      v-if="showSuccessModal"
      class="fixed inset-0 bg-black/70 backdrop-blur-md flex items-center justify-center z-50 p-4 animate-fade-in"
    >
      <div
        class="bg-white rounded-2xl shadow-2xl max-w-lg w-full transform transition-all duration-500 scale-100 animate-scale-up border border-gray-100 relative overflow-hidden"
      >
        <!-- Header Section with Icon -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 text-center relative">
          <div class="absolute inset-0 bg-gradient-to-r from-blue-600/90 to-blue-700/90"></div>
          <div class="relative z-10">
            <div
              class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-3 shadow-lg"
            >
              <div
                class="w-12 h-12 bg-gradient-to-br from-green-400 to-green-500 rounded-full flex items-center justify-center relative"
              >
                <i class="fas fa-check text-white text-xl"></i>
                <div
                  class="absolute inset-0 rounded-full border-2 border-green-300 animate-ping opacity-50"
                ></div>
              </div>
            </div>
            <h3 class="text-2xl font-bold text-white mb-1">Request Submitted!</h3>
            <p class="text-blue-100 text-sm">Your request has been processed successfully</p>
          </div>
        </div>

        <!-- Content Section -->
        <div class="px-6 py-6">
          <!-- Message -->
          <div class="text-center mb-6">
            <p class="text-gray-700 leading-relaxed">
              Your <span class="font-semibold text-blue-600">Combined Access Request</span> has been
              submitted successfully and is now in the approval queue.
            </p>
          </div>

          <!-- Services Card -->
          <div
            class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-4 mb-6 border border-blue-100"
          >
            <div class="flex items-center gap-2 mb-3">
              <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-list-check text-blue-600 text-sm"></i>
              </div>
              <h4 class="text-sm font-semibold text-blue-800">Selected Services</h4>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
              <div
                v-if="formData.services.jeeva"
                class="bg-white rounded-lg p-3 shadow-sm border border-green-200 flex items-center gap-2 transition-transform hover:scale-105"
              >
                <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center">
                  <i class="fas fa-file-medical text-green-600 text-xs"></i>
                </div>
                <span class="text-sm font-medium text-green-800">Jeeva</span>
              </div>

              <div
                v-if="formData.services.wellsoft"
                class="bg-white rounded-lg p-3 shadow-sm border border-blue-200 flex items-center gap-2 transition-transform hover:scale-105"
              >
                <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center">
                  <i class="fas fa-laptop-medical text-blue-600 text-xs"></i>
                </div>
                <span class="text-sm font-medium text-blue-800">Wellsoft</span>
              </div>

              <div
                v-if="formData.services.internet"
                class="bg-white rounded-lg p-3 shadow-sm border border-teal-200 flex items-center gap-2 transition-transform hover:scale-105"
              >
                <div class="w-6 h-6 bg-teal-100 rounded-full flex items-center justify-center">
                  <i class="fas fa-wifi text-teal-600 text-xs"></i>
                </div>
                <span class="text-sm font-medium text-teal-800">Internet</span>
              </div>
            </div>

            <!-- Internet Purposes -->
            <div
              v-if="formData.services.internet && hasInternetPurposes"
              class="mt-4 pt-4 border-t border-blue-200"
            >
              <div class="flex items-center gap-2 mb-3">
                <div class="w-6 h-6 bg-teal-100 rounded-full flex items-center justify-center">
                  <i class="fas fa-globe text-teal-600 text-xs"></i>
                </div>
                <h5 class="text-sm font-medium text-teal-800">Internet Purposes</h5>
              </div>
              <div class="space-y-2">
                <div
                  v-for="(purpose, index) in filledInternetPurposes"
                  :key="index"
                  class="bg-teal-50 rounded-lg p-2 flex items-center gap-2"
                >
                  <div
                    class="w-5 h-5 bg-teal-500 text-white rounded-full flex items-center justify-center text-xs font-bold"
                  >
                    {{ index + 1 }}
                  </div>
                  <span class="text-sm text-teal-700">{{ purpose }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Status Indicator -->
          <div class="bg-gray-50 rounded-lg p-3 mb-6 flex items-center justify-center gap-2">
            <div class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></div>
            <span class="text-sm text-gray-600 font-medium"
              >Request ID will be generated shortly</span
            >
          </div>

          <!-- Action Button -->
          <button
            @click="closeSuccessModal"
            class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white py-3 px-6 rounded-xl font-semibold hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-200 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-[1.02] flex items-center justify-center gap-2 relative overflow-hidden group"
          >
            <div
              class="absolute inset-0 bg-gradient-to-r from-white/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"
            ></div>
            <i class="fas fa-home text-sm relative z-10"></i>
            <span class="relative z-10">Return to Dashboard</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import ModernSidebar from '@/components/ModernSidebar.vue'
  import AppFooter from '@/components/footer.vue'
  import AppHeader from '@/components/AppHeader.vue'
  import userCombinedAccessService from '@/services/userCombinedAccessService'
  import userProfileService from '@/services/userProfileService'

  export default {
    name: 'UserCombinedAccessForm',
    components: {
      ModernSidebar,
      AppFooter,
      AppHeader
    },

    // Route guard to check for pending requests before entering the form
    beforeRouteEnter(to, from, next) {
      // Skip check if in edit mode (user is editing an existing request)
      if (to.query.mode === 'edit' && to.query.id) {
        next()
        return
      }

      // Check for pending requests
      userCombinedAccessService
        .checkPendingRequests()
        .then((response) => {
          if (response.success && response.data.has_pending_request) {
            // User has pending request, redirect to request status with message
            next({
              path: '/request-status',
              query: {
                blocked: 'true',
                message: 'You cannot submit a new request while you have a pending request.',
                pending_request_id: response.data.pending_request?.request_id || 'N/A'
              }
            })
          } else {
            // No pending requests, allow access to form
            next()
          }
        })
        .catch((error) => {
          console.error('Error checking pending requests:', error)
          // On error, still allow access (fail-safe)
          next()
        })
    },
    setup() {
      // Sidebar state now managed by Pinia - no local state needed

      return {
        // No local state needed for sidebar
      }
    },
    data() {
      return {
        isSubmitting: false,
        showSuccessModal: false,
        signaturePreview: '',
        signatureFileName: '',
        departments: [],
        // Auto-population states
        isLoadingProfile: true,
        profileLoadError: null,
        autoPopulated: false,
        // Edit mode properties
        isEditMode: false,
        editRequestId: null,
        originalRequestData: null,
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
          // Module Selections
          selectedWellsoft: [],
          selectedJeeva: [],
          // Internet Purposes (only used when internet service is selected)
          internetPurposes: ['', '', '', '']
        }
      }
    },

    computed: {
      hasSelectedService() {
        return (
          this.formData.services.jeeva ||
          this.formData.services.wellsoft ||
          this.formData.services.internet
        )
      },
      hasInternetPurposes() {
        return this.formData.internetPurposes.some((purpose) => purpose.trim() !== '')
      },
      filledInternetPurposes() {
        return this.formData.internetPurposes.filter((purpose) => purpose.trim() !== '')
      }
    },

    watch: {
      // Note: No module auto-selection needed since HOD selects modules during approval
      // Users only select services (Jeeva, Wellsoft, Internet Access)
    },

    async mounted() {
      console.log('🔄 UserCombinedAccessForm: Component mounted, starting initialization...')

      // Check if in edit mode
      this.checkEditMode()

      // Load both profile data and departments concurrently
      if (this.isEditMode) {
        await Promise.all([this.loadExistingRequest(), this.loadDepartments()])
      } else {
        await Promise.all([this.autoPopulateUserData(), this.loadDepartments()])
      }

      console.log('✅ UserCombinedAccessForm: Initialization completed')
    },

    methods: {
      /**
       * Check if the component is in edit mode by examining route query parameters
       */
      checkEditMode() {
        const { id, mode, status } = this.$route.query

        if (id && mode === 'edit') {
          this.isEditMode = true
          this.editRequestId = id

          console.log('🔄 Edit mode detected:', {
            requestId: this.editRequestId,
            mode: mode,
            status: status
          })
        } else {
          this.isEditMode = false
          this.editRequestId = null

          console.log('📝 New form mode detected')
        }
      },

      /**
       * Load existing request data for editing
       */
      async loadExistingRequest() {
        if (!this.editRequestId) {
          console.error('❌ No request ID provided for edit mode')
          this.showNotification('Invalid request ID for editing', 'error')
          this.$router.push('/request-status')
          return
        }

        console.log('🔄 Loading existing request data for ID:', this.editRequestId)
        this.isLoadingProfile = true

        try {
          const response = await userCombinedAccessService.getRequestById(this.editRequestId)

          if (response.success && response.data) {
            this.originalRequestData = response.data
            this.populateFormWithExistingData(response.data)

            console.log('✅ Existing request data loaded successfully')
            this.showNotification('Request data loaded for editing', 'info')
          } else {
            console.error('❌ Failed to load request data:', response)
            this.showNotification(response.message || 'Failed to load request data', 'error')
            this.$router.push('/request-status')
          }
        } catch (error) {
          console.error('❌ Error loading existing request:', error)
          this.showNotification('Error loading request data', 'error')
          this.$router.push('/request-status')
        } finally {
          this.isLoadingProfile = false
        }
      },

      /**
       * Populate form fields with existing request data
       */
      populateFormWithExistingData(requestData) {
        console.log('🔄 Populating form with existing data:', requestData)
        console.log('🔍 Available fields in requestData:', Object.keys(requestData))
        console.log('🔍 Full requestData structure:', JSON.stringify(requestData, null, 2))

        // Handle nested data if the response is wrapped
        const data = requestData.data || requestData
        console.log('🔍 Using data object:', data)
        console.log('🔍 Available fields in data:', Object.keys(data))

        // Basic information - try different possible field names
        this.formData.pfNumber =
          data.pf_number || data.pfNumber || data.PF_NUMBER || data.pf || data.employee_id || ''
        this.formData.staffName =
          data.staff_name ||
          data.staffName ||
          data.name ||
          data.full_name ||
          data.employee_name ||
          data.applicant_name ||
          ''
        this.formData.phoneNumber =
          data.phone_number || data.phoneNumber || data.phone || data.mobile || data.contact || ''
        this.formData.department =
          data.department_id ||
          data.departmentId ||
          data.department ||
          data.dept ||
          data.department_name ||
          ''

        console.log('🔍 Form field assignments:', {
          pfNumber: {
            value: this.formData.pfNumber,
            sources: [data.pf_number, data.pfNumber, data.PF_NUMBER, data.pf, data.employee_id]
          },
          staffName: {
            value: this.formData.staffName,
            sources: [
              data.staff_name,
              data.staffName,
              data.name,
              data.full_name,
              data.employee_name,
              data.applicant_name
            ]
          },
          phoneNumber: {
            value: this.formData.phoneNumber,
            sources: [data.phone_number, data.phoneNumber, data.phone, data.mobile, data.contact]
          },
          department: {
            value: this.formData.department,
            sources: [
              data.department_id,
              data.departmentId,
              data.department,
              data.dept,
              data.department_name
            ]
          }
        })

        // Services - parse from request_types array or individual fields
        if (data.request_types && Array.isArray(data.request_types)) {
          console.log('🔍 Request types found:', data.request_types)
          this.formData.services.jeeva =
            data.request_types.includes('jeeva_access') || data.request_types.includes('jeeva')
          this.formData.services.wellsoft = data.request_types.includes('wellsoft')
          this.formData.services.internet =
            data.request_types.includes('internet_access_request') ||
            data.request_types.includes('internet')
        } else if (typeof data.request_types === 'string') {
          // Handle comma-separated string
          console.log('🔍 Request types as string:', data.request_types)
          const types = data.request_types.split(',')
          this.formData.services.jeeva = types.some((type) => type.trim().includes('jeeva'))
          this.formData.services.wellsoft = types.some((type) => type.trim().includes('wellsoft'))
          this.formData.services.internet = types.some((type) => type.trim().includes('internet'))
        } else {
          // Try individual boolean fields
          this.formData.services.jeeva = data.jeeva_access || data.jeeva || false
          this.formData.services.wellsoft = data.wellsoft_access || data.wellsoft || false
          this.formData.services.internet = data.internet_access || data.internet || false
        }

        // Module selections
        if (data.selectedWellsoft && Array.isArray(data.selectedWellsoft)) {
          this.formData.selectedWellsoft = [...data.selectedWellsoft]
        } else if (
          data.wellsoft_modules_selected &&
          Array.isArray(data.wellsoft_modules_selected)
        ) {
          this.formData.selectedWellsoft = [...data.wellsoft_modules_selected]
        } else if (data.wellsoft_modules && Array.isArray(data.wellsoft_modules)) {
          this.formData.selectedWellsoft = [...data.wellsoft_modules]
        } else {
          this.formData.selectedWellsoft = []
        }

        if (data.selectedJeeva && Array.isArray(data.selectedJeeva)) {
          this.formData.selectedJeeva = [...data.selectedJeeva]
        } else if (data.jeeva_modules_selected && Array.isArray(data.jeeva_modules_selected)) {
          this.formData.selectedJeeva = [...data.jeeva_modules_selected]
        } else if (data.jeeva_modules && Array.isArray(data.jeeva_modules)) {
          this.formData.selectedJeeva = [...data.jeeva_modules]
        } else {
          this.formData.selectedJeeva = []
        }

        // Internet purposes
        if (data.internet_purposes && Array.isArray(data.internet_purposes)) {
          console.log('🔍 Internet purposes found:', data.internet_purposes)
          this.formData.internetPurposes = [...data.internet_purposes]
          // Fill remaining slots with empty strings
          while (this.formData.internetPurposes.length < 4) {
            this.formData.internetPurposes.push('')
          }
        } else if (data.internet_purposes && typeof data.internet_purposes === 'string') {
          // Handle comma-separated string
          console.log('🔍 Internet purposes as string:', data.internet_purposes)
          const purposes = data.internet_purposes
            .split(',')
            .map((p) => p.trim())
            .filter((p) => p)
          this.formData.internetPurposes = [...purposes]
          while (this.formData.internetPurposes.length < 4) {
            this.formData.internetPurposes.push('')
          }
        }

        // Note: Signature will need to be re-uploaded in edit mode as we don't store the file
        this.autoPopulated = true

        console.log('✅ Form populated with existing data:', {
          pfNumber: this.formData.pfNumber,
          staffName: this.formData.staffName,
          phoneNumber: this.formData.phoneNumber,
          department: this.formData.department,
          services: this.formData.services,
          selectedWellsoft: this.formData.selectedWellsoft,
          selectedJeeva: this.formData.selectedJeeva,
          internetPurposes: this.formData.internetPurposes
        })

        // Validate that essential fields are populated
        const missingFields = []
        if (!this.formData.pfNumber) missingFields.push('PF Number')
        if (!this.formData.staffName) missingFields.push('Staff Name')
        if (!this.formData.phoneNumber) missingFields.push('Phone Number')

        if (missingFields.length > 0) {
          console.warn('⚠️ Missing essential fields after population:', missingFields)
          console.log('🔄 Attempting to load user profile as fallback...')
          // Try to load user profile data to fill missing fields
          this.autoPopulateUserDataFallback(missingFields)
        }
      },

      async loadDepartments() {
        try {
          const response = await userCombinedAccessService.getDepartments()
          if (response.success && response.data && response.data.data) {
            this.departments = response.data.data.map((dept) => ({
              id: dept.id,
              name: dept.name
            }))
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

      /**
       * Auto-populate user data from the authenticated user's profile
       */
      async autoPopulateUserData() {
        console.log('🔄 Starting user data auto-population...')
        this.isLoadingProfile = true
        this.profileLoadError = null

        try {
          const result = await userProfileService.getFormAutoPopulationData()

          if (result.success && result.data) {
            console.log('✅ User profile retrieved successfully:', result.data)

            // Auto-populate form fields
            this.formData.pfNumber = result.data.pfNumber || ''
            this.formData.staffName = result.data.staffName || ''
            this.formData.phoneNumber = result.data.phoneNumber || ''
            this.formData.department = result.data.departmentId || ''

            this.autoPopulated = true

            console.log('✅ Form auto-populated with user data:', {
              pfNumber: this.formData.pfNumber,
              staffName: this.formData.staffName,
              phoneNumber: this.formData.phoneNumber,
              department: this.formData.department
            })

            // Show success notification
            this.showNotification('Your details have been auto-populated from your profile', 'info')
          } else {
            console.warn('⚠️ Failed to auto-populate user data:', result.error)
            this.profileLoadError = result.error || 'Failed to load profile data'
            this.showNotification(
              'Could not auto-populate your details. Please fill them manually.',
              'warning'
            )
          }
        } catch (error) {
          console.error('❌ Error during auto-population:', error)
          this.profileLoadError = error.message || 'Network error while loading profile'
          this.showNotification(
            'Error loading your profile. Please fill in your details manually.',
            'error'
          )
        } finally {
          this.isLoadingProfile = false
        }
      },

      /**
       * Fallback method to auto-populate user data when request data is incomplete
       */
      async autoPopulateUserDataFallback(missingFields) {
        console.log('🔄 Starting fallback user data auto-population for fields:', missingFields)

        try {
          const result = await userProfileService.getFormAutoPopulationData()

          if (result.success && result.data) {
            console.log('✅ User profile retrieved for fallback:', result.data)

            // Only populate missing fields
            if (missingFields.includes('PF Number') && !this.formData.pfNumber) {
              this.formData.pfNumber = result.data.pfNumber || ''
            }
            if (missingFields.includes('Staff Name') && !this.formData.staffName) {
              this.formData.staffName = result.data.staffName || ''
            }
            if (missingFields.includes('Phone Number') && !this.formData.phoneNumber) {
              this.formData.phoneNumber = result.data.phoneNumber || ''
            }
            if (!this.formData.department) {
              this.formData.department = result.data.departmentId || ''
            }

            // Check which fields were successfully populated
            const populatedFields = []
            if (this.formData.pfNumber) populatedFields.push('PF Number')
            if (this.formData.staffName) populatedFields.push('Staff Name')
            if (this.formData.phoneNumber) populatedFields.push('Phone Number')

            if (populatedFields.length > 0) {
              this.showNotification(`Auto-populated: ${populatedFields.join(', ')}`, 'success')
            }

            // Show warning for still missing fields
            const stillMissing = missingFields.filter(
              (field) =>
                (field === 'PF Number' && !this.formData.pfNumber) ||
                (field === 'Staff Name' && !this.formData.staffName) ||
                (field === 'Phone Number' && !this.formData.phoneNumber)
            )

            if (stillMissing.length > 0) {
              this.showNotification(`Please manually fill: ${stillMissing.join(', ')}`, 'warning')
            }
          } else {
            console.warn('⚠️ Failed to load user profile for fallback')
            this.showNotification(
              `Missing fields: ${missingFields.join(', ')}. Please fill them manually.`,
              'warning'
            )
          }
        } catch (error) {
          console.error('❌ Error during fallback auto-population:', error)
          this.showNotification(
            `Missing fields: ${missingFields.join(', ')}. Please fill them manually.`,
            'warning'
          )
        }
      },

      async submitForm() {
        // Validate required fields
        if (
          !this.formData.pfNumber ||
          !this.formData.staffName ||
          !this.formData.department ||
          !this.formData.phoneNumber
        ) {
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

        // Note: Module selection is done by HOD during approval process
        // Users only select services, HOD selects specific modules at /both-service-form/:id
        console.log('📋 Service selections for HOD approval:', {
          jeeva: this.formData.services.jeeva,
          wellsoft: this.formData.services.wellsoft,
          internet: this.formData.services.internet
        })

        // Validate internet purposes if internet service is selected
        if (this.formData.services.internet && !this.formData.internetPurposes[0]) {
          this.showNotification('Please provide at least one internet purpose', 'error')
          return
        }

        this.isSubmitting = true

        try {
          // Debug: Log current form data before submission
          console.log('🔍 DEBUG: Current formData object before submission:', {
            pfNumber: this.formData.pfNumber,
            staffName: this.formData.staffName,
            phoneNumber: this.formData.phoneNumber,
            department: this.formData.department,
            hasSignature: !!this.formData.signature,
            isEditMode: this.isEditMode
          })

          // Validate required fields before creating FormData
          if (!this.formData.pfNumber || this.formData.pfNumber.trim() === '') {
            console.error('❌ PF Number is empty or undefined:', this.formData.pfNumber)
            this.showNotification('PF Number is required but appears to be empty', 'error')
            return
          }

          if (!this.formData.staffName || this.formData.staffName.trim() === '') {
            console.error('❌ Staff Name is empty or undefined:', this.formData.staffName)
            this.showNotification('Staff Name is required but appears to be empty', 'error')
            return
          }

          // Prepare form data for API submission
          const formData = new FormData()

          // Add basic fields
          formData.append('pf_number', this.formData.pfNumber)
          formData.append('staff_name', this.formData.staffName)
          formData.append('phone_number', this.formData.phoneNumber)
          formData.append('department_id', this.formData.department)
          formData.append('signature', this.formData.signature)

          // Debug: Log what's being appended to FormData
          console.log('🔍 DEBUG: FormData entries being sent to API:')
          for (let [key, value] of formData.entries()) {
            if (key === 'signature') {
              console.log(
                `  ${key}: ${value ? `File(${value.name}, ${value.size} bytes)` : 'null'}`
              )
            } else {
              console.log(`  ${key}: "${value}"`)
            }
          }

          // Prepare request types array
          const requestTypes = []
          if (this.formData.services.jeeva) requestTypes.push('jeeva_access')
          if (this.formData.services.wellsoft) requestTypes.push('wellsoft')
          if (this.formData.services.internet) requestTypes.push('internet_access_request')

          // Add request types
          requestTypes.forEach((type, index) => {
            formData.append(`request_type[${index}]`, type)
          })

          // Note: Module selections are handled by HOD during approval at /both-service-form/:id
          // User form only submits service selections (Jeeva, Wellsoft, Internet Access)
          console.log('📝 Submitting service selections only (modules selected by HOD later):', {
            jeeva: this.formData.services.jeeva,
            wellsoft: this.formData.services.wellsoft,
            internet: this.formData.services.internet,
            requestTypes: requestTypes
          })

          // Add internet purposes if internet service is selected
          if (this.formData.services.internet) {
            const purposes = this.formData.internetPurposes.filter(
              (purpose) => purpose.trim() !== ''
            )
            purposes.forEach((purpose, index) => {
              formData.append(`internetPurposes[${index}]`, purpose)
            })
          }

          console.log(`${this.isEditMode ? 'Updating' : 'Submitting'} combined access request:`, {
            isEditMode: this.isEditMode,
            requestId: this.editRequestId,
            pfNumber: this.formData.pfNumber,
            staffName: this.formData.staffName,
            requestTypes,
            hasSignature: !!this.formData.signature
          })

          // Submit to API (update or create)
          const response = this.isEditMode
            ? await userCombinedAccessService.updateRequest(this.editRequestId, formData)
            : await userCombinedAccessService.submitCombinedRequest(formData)

          if (response.success) {
            const actionText = this.isEditMode ? 'updated and resubmitted' : 'submitted'
            console.log(`Combined access request ${actionText} successfully:`, response.data)

            this.showSuccessModal = true
            if (!this.isEditMode) {
              this.resetForm()
            }
            this.showNotification(`Combined access request ${actionText} successfully!`, 'success')
          } else {
            console.error('API Error:', response)

            // Handle validation errors
            if (response.errors && Object.keys(response.errors).length > 0) {
              const errorMessages = Object.values(response.errors).flat()
              this.showNotification(errorMessages[0] || 'Validation failed', 'error')
            } else {
              this.showNotification(
                response.message || `Failed to ${this.isEditMode ? 'update' : 'submit'} request`,
                'error'
              )
            }
          }
        } catch (error) {
          console.error(
            `Error ${this.isEditMode ? 'updating' : 'submitting'} combined access request:`,
            error
          )
          this.showNotification(
            'Network error. Please check your connection and try again.',
            'error'
          )
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
          selectedWellsoft: [],
          selectedJeeva: [],
          internetPurposes: ['', '', '', '']
        }
        this.clearSignature()
        this.showNotification('Form has been reset', 'info')
      },

      triggerFileUpload() {
        this.$refs.signatureInput.click()
      },

      onSignatureChange(e) {
        const file = e.target.files[0]
        this.formData.signature = file || null

        if (!file) {
          this.signaturePreview = ''
          this.signatureFileName = ''
          return
        }

        // Validate file type
        const allowedTypes = ['image/png', 'image/jpeg', 'image/jpg', 'application/pdf']
        if (!allowedTypes.includes(file.type)) {
          this.showNotification('Please select a valid file (PNG, JPG, or PDF)', 'error')
          this.clearSignature()
          return
        }

        // Validate file size (max 5MB)
        if (file.size > 5 * 1024 * 1024) {
          this.showNotification('File size must be less than 5MB', 'error')
          this.clearSignature()
          return
        }

        this.signatureFileName = file.name

        if (file.type.startsWith('image/')) {
          const reader = new FileReader()
          reader.onload = () => {
            this.signaturePreview = reader.result
          }
          reader.readAsDataURL(file)
        } else {
          this.signaturePreview = 'pdf'
        }
      },

      isImage(preview) {
        return typeof preview === 'string' && preview !== 'pdf'
      },

      clearSignature() {
        this.formData.signature = null
        this.signaturePreview = ''
        this.signatureFileName = ''
        if (this.$refs.signatureInput) {
          this.$refs.signatureInput.value = ''
        }
      },

      goBack() {
        this.$router.push('/user-dashboard')
      },

      closeSuccessModal() {
        this.showSuccessModal = false
        // Redirect to Request Status page with success parameters
        this.$router.push({
          path: '/request-status',
          query: {
            success: 'true',
            type: 'Combined Access Request',
            id: 'REQ-' + Date.now()
          }
        })
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
  .medical-glass-card {
    background: rgba(59, 130, 246, 0.15);
    backdrop-filter: blur(25px);
    -webkit-backdrop-filter: blur(25px);
    border: 2px solid rgba(96, 165, 250, 0.3);
    box-shadow:
      0 8px 32px rgba(29, 78, 216, 0.4),
      inset 0 1px 0 rgba(255, 255, 255, 0.1);
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
    0%,
    100% {
      transform: translateY(0px);
    }
    50% {
      transform: translateY(-20px);
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

  @keyframes scale-up {
    from {
      opacity: 0;
      transform: scale(0.8) translateY(30px) rotate(3deg);
    }
    to {
      opacity: 1;
      transform: scale(1) translateY(0) rotate(0deg);
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
      transform: translateY(-8px);
    }
    60% {
      transform: translateY(-4px);
    }
  }

  @keyframes pulse-ring {
    0% {
      transform: scale(1);
      opacity: 1;
    }
    100% {
      transform: scale(2.5);
      opacity: 0;
    }
  }

  .animate-scale-up {
    animation: scale-up 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
  }

  .animate-bounce-gentle {
    animation: bounce-gentle 2s ease-in-out infinite;
  }

  .animate-pulse-ring {
    animation: pulse-ring 2s cubic-bezier(0.455, 0.03, 0.515, 0.955) infinite;
  }

  .fixed.inset-0 {
    animation: modalFadeIn 0.3s ease-out;
  }

  /* Service card hover effects */
  label:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
  }

  /* Checkbox animations */
  input[type='checkbox']:checked + label {
    transform: scale(1.02);
  }

  /* File input styling */
  input[type='file']::-webkit-file-upload-button {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    border: none;
    color: white;
    padding: 8px 16px;
    border-radius: 20px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
  }

  input[type='file']::-webkit-file-upload-button:hover {
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
    border-color: #3b82f6;
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
    box-shadow:
      0 20px 25px -5px rgba(0, 0, 0, 0.1),
      0 10px 10px -5px rgba(0, 0, 0, 0.04);
  }
</style>
