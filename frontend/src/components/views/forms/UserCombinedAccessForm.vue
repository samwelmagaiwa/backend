<template>
  <div class="flex flex-col h-screen">
    <AppHeader />
    <div class="flex flex-1 overflow-hidden">
      <ModernSidebar />
      <main
        class="flex-1 p-3 bg-gradient-to-br from-blue-900 via-blue-800 to-teal-900 overflow-y-auto relative"
      >
        <!-- Loading Banner -->
        <UnifiedLoadingBanner
          :show="isLoadingProfile || isSubmitting || isLoadingModules"
          :loadingTitle="getLoadingTitle()"
          :loadingSubtitle="getLoadingSubtitle()"
          departmentTitle="COMBINED ACCESS REQUEST SYSTEM"
          :forceSpin="true"
        />

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

        <div class="max-w-13xl mx-auto relative z-10 text-[17px] md:text-[18px]">
          <!-- Header Section -->
          <div class="medical-glass-card rounded-t-3xl p-2 mb-0 border-b border-blue-300/30">
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
                  class="text-2xl font-bold text-white mb-2 tracking-wide drop-shadow-lg animate-fade-in"
                >
                  MUHIMBILI NATIONAL HOSPITAL
                </h1>
                <div class="relative inline-block mb-2">
                  <div
                    class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-2 rounded-full text-lg font-bold shadow-2xl transform hover:scale-105 transition-all duration-300 border-2 border-blue-400/60"
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
                  class="text-lg font-bold text-blue-100 tracking-wide drop-shadow-md animate-fade-in-delay"
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
            <form @submit.prevent="submitForm" class="p-3 space-y-3">
              <!-- Form Description -->

              <!-- Applicant Details Section -->
              <div
                class="medical-card bg-gradient-to-r from-blue-600/25 to-blue-700/25 border-2 border-blue-400/40 p-3 rounded-xl backdrop-blur-sm hover:shadow-2xl hover:shadow-blue-500/20 transition-all duration-500 group relative overflow-hidden"
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
                  <div class="flex items-center space-x-2 mb-1.5">
                    <div
                      class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-blue-300/50 relative overflow-hidden"
                    >
                      <div
                        class="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent rounded-lg"
                      ></div>
                      <i
                        class="fas fa-user-md text-white text-base relative z-10 drop-shadow-lg"
                      ></i>
                      <div
                        class="absolute top-1 right-1 w-1 h-1 bg-white/60 rounded-full animate-ping"
                      ></div>
                    </div>
                    <div class="flex-1">
                      <h3 class="text-xl font-bold text-white flex items-center">
                        <i class="fas fa-id-card mr-2 text-blue-300"></i>
                        Applicant Details
                      </h3>
                      <div class="flex items-center space-x-2 mt-0.5">
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
                          <OrbitingDots size="xs" class="mr-1" />
                          Loading your details...
                        </span>
                        <span
                          v-else-if="autoPopulated"
                          class="text-sm text-green-300 font-medium bg-green-500/20 px-2 py-1 rounded-full border border-green-400/30"
                        >
                          <i class="fas fa-lock mr-1 text-xs"></i>
                          Auto-populated (Read Only)
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
                  <div class="space-y-2 mb-2">
                    <!-- Row 1: Basic Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                      <!-- PF Number -->
                      <div class="group">
                        <label class="block text-lg font-bold text-blue-100 mb-2 flex items-center">
                          <i class="fas fa-id-badge mr-2 text-blue-300"></i>
                          PF Number <span class="text-red-400 ml-1">*</span>
                        </label>
                        <div class="relative">
                          <textarea
                            v-model="formData.pfNumber"
                            readonly
                            rows="1"
                            class="medical-input w-full px-3 py-2 bg-blue-600/20 border-2 border-blue-400/40 rounded-lg text-white backdrop-blur-sm resize-none cursor-not-allowed opacity-80"
                            :placeholder="
                              isLoadingProfile
                                ? 'Loading your PF Number...'
                                : 'Auto-populated from your profile'
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
                            <OrbitingDots v-if="isLoadingProfile" size="xs" />
                            <i
                              v-else-if="autoPopulated && formData.pfNumber"
                              class="fas fa-check text-green-400"
                            ></i>
                            <i v-else class="fas fa-hashtag"></i>
                          </div>
                        </div>
                        <p class="text-xs text-green-200/70 mt-1 italic flex items-center">
                          <i class="fas fa-lock mr-1"></i>
                          Auto-populated from your profile (cannot be edited)
                        </p>
                      </div>

                      <!-- Staff Name -->
                      <div class="group">
                        <label class="block text-lg font-bold text-blue-100 mb-2 flex items-center">
                          <i class="fas fa-user mr-2 text-blue-300"></i>
                          Staff Name <span class="text-red-400 ml-1">*</span>
                        </label>
                        <div class="relative">
                          <textarea
                            v-model="formData.staffName"
                            readonly
                            rows="1"
                            class="medical-input w-full px-4 py-2 bg-blue-600/20 border-2 border-blue-400/40 rounded-xl text-white backdrop-blur-sm resize-none cursor-not-allowed opacity-80"
                            :placeholder="
                              isLoadingProfile
                                ? 'Loading your name...'
                                : 'Auto-populated from your profile'
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
                            <OrbitingDots v-if="isLoadingProfile" size="xs" />
                            <i
                              v-else-if="autoPopulated && formData.staffName"
                              class="fas fa-check text-green-400"
                            ></i>
                            <i v-else class="fas fa-user-circle"></i>
                          </div>
                        </div>
                        <p class="text-xs text-green-200/70 mt-1 italic flex items-center">
                          <i class="fas fa-lock mr-1"></i>
                          Auto-populated from your profile (cannot be edited)
                        </p>
                      </div>
                    </div>

                    <!-- Row 2: Contact & Department -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                      <!-- Phone Number -->
                      <div class="group">
                        <label class="block text-lg font-bold text-blue-100 mb-2 flex items-center">
                          <i class="fas fa-phone mr-2 text-blue-300"></i>
                          Phone Number <span class="text-red-400 ml-1">*</span>
                        </label>
                        <div class="relative">
                          <textarea
                            v-model="formData.phoneNumber"
                            readonly
                            rows="1"
                            class="medical-input w-full px-4 py-2 bg-blue-600/20 border-2 border-blue-400/40 rounded-xl text-white backdrop-blur-sm resize-none cursor-not-allowed opacity-80"
                            :placeholder="
                              isLoadingProfile
                                ? 'Loading your phone...'
                                : 'Auto-populated from your profile'
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
                            <OrbitingDots v-if="isLoadingProfile" size="xs" />
                            <i
                              v-else-if="autoPopulated && formData.phoneNumber"
                              class="fas fa-check text-green-400"
                            ></i>
                            <i v-else class="fas fa-mobile-alt"></i>
                          </div>
                        </div>
                        <p class="text-xs text-green-200/70 mt-1 italic flex items-center">
                          <i class="fas fa-lock mr-1"></i>
                          Auto-populated from your profile (cannot be edited)
                        </p>
                      </div>

                      <!-- Department -->
                      <div class="group">
                        <label class="block text-lg font-bold text-blue-100 mb-2 flex items-center">
                          <i class="fas fa-building mr-2 text-blue-300"></i>
                          Department <span class="text-red-400 ml-1">*</span>
                        </label>
                        <div class="relative">
                          <textarea
                            :value="getDepartmentName(formData.department)"
                            readonly
                            rows="1"
                            class="medical-input w-full px-4 py-2 bg-blue-600/20 border-2 border-blue-400/40 rounded-xl text-white backdrop-blur-sm resize-none cursor-not-allowed opacity-80"
                            placeholder="Auto-populated from your profile"
                            required
                          />
                          <!-- Hidden input to maintain form value -->
                          <input v-model="formData.department" type="hidden" required />
                          <div
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-blue-300/50 pointer-events-none"
                          >
                            <i class="fas fa-chevron-down"></i>
                          </div>
                          <div
                            class="absolute inset-0 rounded-xl bg-gradient-to-r from-blue-500/10 to-blue-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"
                          ></div>
                        </div>
                        <p class="text-xs text-green-200/70 mt-1 italic flex items-center">
                          <i class="fas fa-lock mr-1"></i>
                          Auto-populated from your profile (cannot be edited)
                        </p>
                      </div>
                    </div>

                    <!-- Bottom row: Module Requested for (left), Access Rights (middle), and Signature (right) -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 mt-3 items-stretch">
                      <!-- Module Requested for Section (Left Column) -->
                      <div class="flex flex-col h-full">
                        <div
                          class="bg-white/10 rounded-lg p-2 border border-blue-300/30 backdrop-blur-sm w-full min-h-[85px] flex flex-col justify-center"
                        >
                          <label
                            class="block text-base font-bold text-blue-100 mb-1.5 text-center flex items-center justify-center gap-2"
                          >
                            <i class="fas fa-toggle-on mr-2 text-blue-300 text-base"></i>
                            Module Requested for
                            <span class="text-red-400">*</span>
                          </label>
                          <div class="flex items-center gap-4 justify-center">
                            <label
                              class="flex items-center cursor-pointer px-3 py-2 rounded-lg transition-all border min-w-16 hover:bg-blue-500/20 bg-white/5 border-white/10"
                              :class="{
                                'bg-blue-500/20 border-blue-400/40 text-blue-200 shadow-md ring-1 ring-blue-400/30':
                                  wellsoftRequestType === 'use'
                              }"
                            >
                              <input
                                v-model="wellsoftRequestType"
                                type="radio"
                                value="use"
                                class="w-5 h-5 border-blue-300 focus:ring-blue-500 mr-2 text-blue-600"
                              />
                              <span class="text-base font-bold flex items-center text-blue-100">
                                <i class="fas fa-plus-circle mr-2 text-base text-green-400"></i>
                                Use
                              </span>
                            </label>
                            <label
                              class="flex items-center cursor-pointer px-3 py-2 rounded-lg transition-all border min-w-16 hover:bg-red-500/20 bg-white/5 border-white/10"
                              :class="{
                                'bg-red-500/20 border-red-400/40 text-red-200 shadow-md ring-1 ring-red-400/30':
                                  wellsoftRequestType === 'revoke'
                              }"
                            >
                              <input
                                v-model="wellsoftRequestType"
                                type="radio"
                                value="revoke"
                                class="w-5 h-5 border-blue-300 focus:ring-blue-500 mr-2 text-blue-600"
                              />
                              <span class="text-base font-bold flex items-center text-blue-100">
                                <i class="fas fa-minus-circle mr-2 text-base text-red-400"></i>
                                Revoke
                              </span>
                            </label>
                          </div>
                        </div>
                      </div>

                      <!-- Access Rights Section (Middle) -->
                      <div class="flex flex-col h-full">
                        <div
                          class="bg-gradient-to-br from-blue-500/15 to-indigo-500/15 rounded-xl border-2 border-blue-400/40 p-2 w-full min-h-[85px] flex flex-col justify-center shadow-lg backdrop-blur-sm"
                        >
                          <label
                            class="block text-lg font-bold text-blue-100 mb-1.5 text-center flex items-center justify-center"
                          >
                            <i class="fas fa-clock mr-2 text-blue-300 text-lg"></i>
                            <span
                              class="bg-gradient-to-r from-blue-300 to-cyan-300 bg-clip-text text-transparent"
                              >Access Rights</span
                            >
                            <span class="text-red-400 ml-1">*</span>
                          </label>
                          <div class="space-y-1">
                            <!-- Permanent Option -->
                            <label
                              class="flex items-center cursor-pointer p-1.5 rounded-lg transition-all duration-300 border-2"
                              :class="
                                accessType === 'permanent'
                                  ? 'bg-green-500/20 border-green-400/50 shadow-md ring-2 ring-green-400/30'
                                  : 'bg-white/5 border-blue-300/20 hover:bg-white/10 hover:border-blue-400/40'
                              "
                            >
                              <input
                                v-model="accessType"
                                type="radio"
                                value="permanent"
                                class="w-4 h-4 text-green-600 mr-2 accent-green-500"
                              />
                              <div class="flex items-center flex-1">
                                <i class="fas fa-infinity mr-2 text-green-400 text-base"></i>
                                <span class="text-white font-semibold text-base">Permanent</span>
                              </div>
                            </label>

                            <!-- Temporary Option -->
                            <label
                              @click="accessType === 'temporary' && openDatePickerModal()"
                              class="flex items-center cursor-pointer p-1.5 rounded-lg transition-all duration-300 border-2"
                              :class="
                                accessType === 'temporary'
                                  ? 'bg-amber-500/20 border-amber-400/50 shadow-md ring-2 ring-amber-400/30'
                                  : 'bg-white/5 border-blue-300/20 hover:bg-white/10 hover:border-blue-400/40'
                              "
                            >
                              <input
                                v-model="accessType"
                                type="radio"
                                value="temporary"
                                class="w-4 h-4 text-amber-600 mr-2 accent-amber-500"
                                @click.stop
                              />
                              <div class="flex items-center flex-1 justify-between">
                                <div class="flex items-center">
                                  <i
                                    class="fas fa-hourglass-half mr-2 text-amber-400 text-base"
                                  ></i>
                                  <span class="text-white font-semibold text-base">Temporary</span>
                                </div>
                                <!-- Show selected date if available -->
                                <div
                                  v-if="accessType === 'temporary' && formattedDateDisplay"
                                  class="flex items-center gap-2"
                                >
                                  <span class="text-base text-amber-200 font-bold">{{
                                    formattedDateDisplay
                                  }}</span>
                                  <button
                                    type="button"
                                    @click.stop="openDatePickerModal"
                                    class="text-amber-300 hover:text-amber-100 transition-colors"
                                    title="Change date"
                                  >
                                    <i class="fas fa-calendar-edit text-base"></i>
                                  </button>
                                </div>
                                <button
                                  v-else-if="accessType === 'temporary'"
                                  type="button"
                                  @click.stop="openDatePickerModal"
                                  class="text-sm text-amber-300 hover:text-amber-100 font-bold underline"
                                >
                                  Select Date
                                </button>
                              </div>
                            </label>
                            <!-- Validation error message for temporary without date -->
                            <div
                              v-if="accessType === 'temporary' && !temporaryUntil"
                              class="mt-2 px-2 py-1.5 bg-red-500/20 border border-red-400/50 rounded-lg flex items-center gap-2 animate-pulse"
                            >
                              <i class="fas fa-exclamation-circle text-red-400 text-sm"></i>
                              <p class="text-xs text-red-200 font-medium">
                                End date is required for temporary access
                              </p>
                            </div>
                          </div>
                        </div>
                      </div>

                      <!-- Signature Section (Right) -->
                      <div class="flex flex-col h-full">
                        <div
                          class="relative bg-white/10 rounded-lg border border-blue-300/30 p-2 w-full min-h-[85px] flex flex-col justify-center"
                        >
                          <label class="block text-base font-bold text-blue-100 mb-1.5 text-center">
                            Signature <span class="text-red-400">*</span>
                          </label>

                          <!-- Edit mode: Show uploaded signature preview -->
                          <div
                            v-if="signaturePreview || hasUserSigned"
                            class="w-full px-1 py-1 border-2 border-blue-300/40 rounded-lg bg-white/15 backdrop-blur-sm transition-all duration-300 shadow-lg hover:shadow-xl hover:shadow-blue-500/20 flex items-center justify-center relative min-h-[35px]"
                          >
                            <template v-if="signaturePreview">
                              <div v-if="isImage(signaturePreview)" class="text-center">
                                <img
                                  :src="signaturePreview"
                                  alt="Digital Signature"
                                  class="max-h-[25px] max-w-full object-contain mx-auto"
                                />
                                <p class="text-base text-blue-100 mt-1 truncate">
                                  {{ signatureFileName }}
                                </p>
                              </div>
                              <div v-else class="text-center">
                                <div
                                  class="w-12 h-12 bg-red-500/20 rounded-xl flex items-center justify-center mx-auto"
                                >
                                  <i class="fas fa-file-pdf text-red-400 text-2xl"></i>
                                </div>
                                <p class="text-base text-blue-100 mt-1 truncate">
                                  {{ signatureFileName }}
                                </p>
                              </div>
                            </template>
                            <template v-else>
                              <div class="flex items-center gap-2 text-green-200 font-bold text-base">
                                <i class="fas fa-check-circle text-green-400"></i>
                                <span>
                                  Digitally signed
                                  <span v-if="lastSignedAt" class="opacity-80">at {{ new Date(lastSignedAt).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) }}</span>
                                </span>
                              </div>
                            </template>

                            <div class="absolute top-1 right-1 flex gap-1" v-if="signaturePreview">
                              <button
                                type="button"
                                @click="triggerFileUpload"
                                class="w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-xs hover:bg-blue-600 transition-colors duration-200 shadow-lg"
                                title="Change signature"
                              >
                                <i class="fas fa-edit"></i>
                              </button>
                              <button
                                type="button"
                                @click="clearSignature"
                                class="w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center text-xs hover:bg-red-600 transition-colors duration-200 shadow-lg"
                                title="Remove signature"
                              >
                                <i class="fas fa-times"></i>
                              </button>
                            </div>
                          </div>

                          <!-- Default signature area -->
                          <div
                            v-else
                            class="w-full px-1 py-2 border-2 border-dashed border-blue-300/40 rounded-lg focus-within:border-blue-400 bg-white/15 backdrop-blur-sm transition-all duration-300 shadow-lg hover:shadow-xl hover:shadow-blue-500/20 flex items-center justify-center hover:bg-white/20 min-h-[50px]"
                          >
                            <div class="text-center space-y-2">
                              <i class="fas fa-signature text-blue-300 text-lg"></i>
                              <p class="text-blue-100 text-base font-medium">
                                No signature yet
                              </p>
                              <div class="flex items-center justify-center">
                                <button
                                  type="button"
                                  @click="signCurrentDocument"
                                  :disabled="isSigning || !editRequestId"
                                  :title="editRequestId ? 'Digitally sign this request' : 'Submit first to generate a Request ID, then return to sign'"
                                  class="px-4 py-2 bg-gradient-to-r from-emerald-500 to-green-600 text-white text-base font-bold rounded-lg hover:from-emerald-600 hover:to-green-700 transition-all duration-300 flex items-center gap-2 mx-auto shadow-lg hover:shadow-xl transform hover:scale-105 border border-emerald-400/50 disabled:opacity-60 disabled:cursor-not-allowed"
                                >
                                  <OrbitingDots v-if="isSigning" size="xs" />
                                  <i v-else class="fas fa-file-signature"></i>
                                  Digitally Sign Document
                                </button>
                              </div>
                            </div>
                          </div>

                        </div>
                      </div>
                    </div>

                    <!-- Module Selection Section - Jeeva and Wellsoft Side by Side -->
                    <div class="mt-3">
                      <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 relative">
                        <!-- Vertical Divider Line -->
                        <div
                          class="hidden lg:block absolute left-2/3 top-0 bottom-0 w-px bg-gradient-to-b from-blue-300/30 via-blue-400/50 to-blue-300/30 transform -translate-x-px"
                        ></div>

                        <!-- Jeeva Modules (Left Side - Takes 2 columns) -->
                        <div class="lg:col-span-2">
                          <div
                            class="bg-white/10 rounded-lg border border-purple-300/30 p-3 w-full h-auto"
                          >
                            <div class="mb-1.5">
                              <label
                                class="block text-base font-bold text-purple-200 mb-1.5 flex items-center justify-between"
                              >
                                <span>
                                  <i class="fas fa-database mr-2 text-sm"></i>
                                  Jeeva Modules
                                </span>
                                <span class="text-sm text-purple-300/70">
                                  Selected: {{ formData.selectedJeeva?.length || 0 }} modules
                                </span>
                              </label>
                            </div>
                            <div
                              class="grid grid-cols-4 gap-2 border border-purple-300/20 rounded p-2"
                            >
                              <label
                                v-for="module in jeevaModules"
                                :key="'jeeva-' + module"
                                class="flex items-center cursor-pointer text-sm hover:bg-white/10 p-1 rounded transition-colors leading-tight"
                              >
                                <input
                                  v-model="formData.selectedJeeva"
                                  :value="module"
                                  type="checkbox"
                                  class="w-4 h-4 text-red-600 accent-red-600 mr-2 flex-shrink-0"
                                />
                                <span
                                  class="text-purple-100 text-base md:text-[17px] leading-tight"
                                  >{{ module }}</span
                                >
                              </label>
                            </div>
                          </div>
                        </div>

                        <!-- Wellsoft Modules (Right Side - Takes 1 column) -->
                        <div class="lg:col-span-1">
                          <div
                            class="bg-white/10 rounded-lg border border-amber-300/30 p-3 w-full h-auto"
                          >
                            <div class="mb-1.5">
                              <label
                                class="block text-base font-bold text-amber-200 mb-1.5 flex items-center justify-between"
                              >
                                <span>
                                  <i class="fas fa-hospital mr-2 text-sm"></i>
                                  Wellsoft Modules
                                </span>
                                <span class="text-sm text-amber-300/70">
                                  Selected: {{ formData.selectedWellsoft?.length || 0 }} modules
                                </span>
                              </label>
                            </div>
                            <div
                              class="grid grid-cols-2 gap-2 border border-amber-300/20 rounded p-2"
                            >
                              <label
                                v-for="module in wellsoftModules"
                                :key="'wellsoft-' + module"
                                class="flex items-center cursor-pointer text-sm hover:bg-white/10 p-1.5 rounded transition-colors"
                              >
                                <input
                                  v-model="formData.selectedWellsoft"
                                  :value="module"
                                  type="checkbox"
                                  class="w-4 h-4 text-red-600 accent-red-600 mr-2 flex-shrink-0"
                                />
                                <span
                                  class="text-amber-100 text-base md:text-[17px] leading-tight"
                                  >{{ module }}</span
                                >
                              </label>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Internet Purpose -->
              <div
                class="medical-card bg-gradient-to-r from-blue-600/25 to-blue-700/25 border-2 border-blue-400/40 p-3 rounded-xl backdrop-blur-sm hover:shadow-2xl hover:shadow-blue-500/20 transition-all duration-500 group relative overflow-hidden"
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
                  <div class="flex items-center space-x-2 mb-1.5">
                    <div
                      class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-blue-300/50 relative overflow-hidden"
                    >
                      <div
                        class="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent rounded-lg"
                      ></div>
                      <i class="fas fa-wifi text-white text-base relative z-10 drop-shadow-lg"></i>
                      <div
                        class="absolute top-1 right-1 w-1 h-1 bg-white/60 rounded-full animate-ping"
                      ></div>
                    </div>
                    <div>
                      <h3 class="text-xl font-bold text-white flex items-center">
                        <i class="fas fa-globe mr-2 text-blue-300"></i>
                        Internet Purpose
                      </h3>
                      <p class="text-base text-blue-100/80">
                        Specify the purposes for internet access
                      </p>
                    </div>
                  </div>

                  <div class="space-y-2">
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
                      />
                    </div>
                    <p class="text-sm text-blue-200 mt-1.5 italic">
                      <i class="fas fa-info-circle mr-1"></i>
                      Please provide at least one purpose for internet access. You can add up to 4
                      purposes.
                    </p>
                  </div>
                </div>
              </div>

              <!-- Form Actions -->
              <div
                class="medical-card bg-gradient-to-r from-indigo-600/25 to-purple-600/25 border-2 border-indigo-400/40 p-3 rounded-xl backdrop-blur-sm hover:shadow-2xl hover:shadow-indigo-500/20 transition-all duration-500 group relative overflow-hidden"
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
                      class="px-5 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-all duration-300 font-semibold flex items-center shadow-lg hover:shadow-xl text-base transform hover:scale-105"
                    >
                      <i class="fas fa-arrow-left mr-2"></i>
                      Back to Dashboard
                    </button>
                    <button
                      type="submit"
                      :disabled="isSubmitting || !hasSelectedService"
                      class="px-5 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-300 font-semibold flex items-center shadow-lg hover:shadow-xl text-base transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                    >
                      <OrbitingDots v-if="isSubmitting" size="sm" class="mr-2" />
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

    <!-- Date Picker Modal -->
    <div
      v-if="showDatePickerModal"
      class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4 animate-fade-in"
      @click="closeDatePickerModal"
    >
      <div
        class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl shadow-2xl max-w-md w-full transform transition-all duration-300 scale-100 border-2 border-blue-300/50"
        @click.stop
      >
        <!-- Modal Header -->
        <div
          class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 rounded-t-2xl relative overflow-hidden"
        >
          <div class="absolute inset-0 bg-gradient-to-r from-blue-600/90 to-blue-700/90"></div>
          <div class="relative z-10 flex items-center justify-between">
            <div class="flex items-center gap-3">
              <div
                class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center backdrop-blur-sm border border-white/30"
              >
                <i class="fas fa-calendar-alt text-white text-lg"></i>
              </div>
              <div>
                <h3 class="text-xl font-bold text-white">Select End Date</h3>
                <p class="text-blue-100 text-xs">Choose when access should expire</p>
              </div>
            </div>
            <button
              type="button"
              @click="closeDatePickerModal"
              class="w-8 h-8 bg-white/20 hover:bg-white/30 rounded-lg flex items-center justify-center transition-colors duration-200 backdrop-blur-sm border border-white/30"
            >
              <i class="fas fa-times text-white"></i>
            </button>
          </div>
        </div>

        <!-- Modal Body -->
        <div class="px-6 py-5">
          <!-- Date Input Section -->
          <div class="bg-white rounded-xl p-4 border-2 border-blue-300/40 shadow-inner mb-4">
            <label class="block text-base font-bold text-blue-900 mb-3 flex items-center">
              <i class="fas fa-hourglass-half mr-2 text-blue-600 text-lg"></i>
              Temporary Access End Date
            </label>

            <!-- Hidden native date input for proper date handling -->
            <input
              ref="dateInput"
              v-model="temporaryUntil"
              type="date"
              :min="tomorrowDate"
              class="absolute opacity-0 pointer-events-none"
              @change="updateDisplayDate"
              style="font-size: 18px"
            />

            <!-- Visible custom input with DD/MM/YYYY format -->
            <div
              @click="openDatePicker"
              class="w-full px-4 py-4 bg-gradient-to-br from-white to-blue-50 border-2 border-blue-400/50 rounded-xl text-gray-800 font-bold shadow-lg focus:ring-2 focus:ring-blue-400 focus:border-blue-500 transition-all duration-300 cursor-pointer hover:border-blue-500 hover:shadow-xl flex items-center justify-between group"
            >
              <div class="flex items-center gap-2">
                <i class="fas fa-calendar-day text-blue-500 text-xl"></i>
                <span
                  :class="
                    formattedDateDisplay ? 'text-gray-800 text-lg' : 'text-gray-400 text-base'
                  "
                  class="font-bold"
                >
                  {{ formattedDateDisplay || 'Click to select date' }}
                </span>
              </div>
              <i
                class="fas fa-chevron-down text-blue-400 group-hover:text-blue-600 transition-colors text-base"
              ></i>
            </div>

            <p
              class="text-sm text-blue-700 mt-2 italic flex items-center bg-blue-100/50 px-3 py-1.5 rounded-lg"
            >
              <i class="fas fa-info-circle mr-2"></i>
              Access will automatically expire on the selected date
            </p>
          </div>

          <!-- Action Buttons -->
          <div class="flex gap-3">
            <button
              type="button"
              @click="closeDatePickerModal"
              class="flex-1 px-4 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-xl transition-all duration-200 flex items-center justify-center gap-2"
            >
              <i class="fas fa-times"></i>
              Cancel
            </button>
            <button
              type="button"
              @click="closeDatePickerModal"
              :disabled="!temporaryUntil"
              class="flex-1 px-4 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold rounded-xl transition-all duration-200 flex items-center justify-center gap-2 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <i class="fas fa-check"></i>
              Confirm Date
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Success Modal - Compact & Modern -->
    <div
      v-if="showSuccessModal"
      class="fixed inset-0 bg-black/70 backdrop-blur-md flex items-center justify-center z-50 p-4 animate-fade-in"
    >
      <div
        class="bg-white rounded-2xl shadow-2xl w-[680px] h-[680px] max-w-none transform transition-all duration-500 scale-100 animate-scale-up border border-gray-100 relative overflow-hidden flex flex-col"
      >
        <!-- Compact Header with Icon -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 text-center relative">
          <div class="absolute inset-0 bg-gradient-to-r from-blue-600/90 to-blue-700/90"></div>
          <div class="relative z-10 flex items-center justify-center gap-4">
            <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center shadow-lg">
              <div
                class="w-16 h-16 bg-gradient-to-br from-green-400 to-green-500 rounded-full flex items-center justify-center relative"
              >
                <i class="fas fa-check text-white text-3xl"></i>
              </div>
            </div>
            <div class="text-left">
              <h3 class="text-3xl font-extrabold text-white">Request Submitted!</h3>
              <p class="text-blue-100 text-lg">Successfully processed</p>
            </div>
          </div>
        </div>

        <!-- Compact Content Section -->
        <div class="px-6 py-5 flex-1 overflow-y-auto">
          <!-- Message -->
          <div class="text-center mb-5">
            <p class="text-xl text-gray-700 font-semibold">
              Your <span class="font-bold text-blue-600">Combined Access Request</span> is now in
              the approval queue.
            </p>
          </div>

          <!-- Compact Services Card -->
          <div
            class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-5 mb-5 border border-blue-100"
          >
            <div class="flex items-center gap-3 mb-4">
              <i class="fas fa-list-check text-blue-600 text-2xl"></i>
              <h4 class="text-xl font-extrabold text-blue-800">Selected Services</h4>
            </div>

            <div class="flex flex-wrap gap-2">
              <div
                v-if="submittedServices.jeeva"
                class="bg-white rounded-lg px-5 py-3 shadow-sm border border-green-200 flex items-center gap-3"
              >
                <i class="fas fa-file-medical text-green-600 text-lg"></i>
                <span class="text-lg font-bold text-green-800">Jeeva</span>
              </div>

              <div
                v-if="submittedServices.wellsoft"
                class="bg-white rounded-lg px-5 py-3 shadow-sm border border-blue-200 flex items-center gap-3"
              >
                <i class="fas fa-laptop-medical text-blue-600 text-lg"></i>
                <span class="text-lg font-bold text-blue-800">Wellsoft</span>
              </div>

              <div
                v-if="submittedServices.internet"
                class="bg-white rounded-lg px-5 py-3 shadow-sm border border-teal-200 flex items-center gap-3"
              >
                <i class="fas fa-wifi text-teal-600 text-lg"></i>
                <span class="text-lg font-bold text-teal-800">Internet</span>
              </div>
            </div>

            <!-- Internet Purposes - Compact -->
            <div
              v-if="submittedServices.internet && hasInternetPurposes"
              class="mt-4 pt-4 border-t border-blue-200"
            >
              <div class="flex items-center gap-3 mb-3">
                <i class="fas fa-globe text-teal-600 text-xl"></i>
                <h5 class="text-xl font-extrabold text-teal-800">Internet Purposes:</h5>
              </div>
              <div class="space-y-2">
                <div
                  v-for="(purpose, index) in filledInternetPurposes.slice(0, 3)"
                  :key="index"
                  class="text-lg text-teal-700 flex items-start gap-3 font-semibold"
                >
                  <span class="text-teal-600 font-bold">{{ index + 1 }}.</span>
                  <span class="flex-1">{{ purpose }}</span>
                </div>
                <p
                  v-if="filledInternetPurposes.length > 3"
                  class="text-base text-gray-600 italic font-medium"
                >
                  +{{ filledInternetPurposes.length - 3 }} more purpose(s)
                </p>
              </div>
            </div>
          </div>

          <!-- Compact Status Indicator -->
          <div class="bg-gray-50 rounded-lg p-4 mb-6 flex items-center justify-center gap-3">
            <div class="w-3.5 h-3.5 bg-blue-500 rounded-full animate-pulse"></div>
            <span class="text-lg text-gray-700 font-bold"
              >Request ID will be generated shortly</span
            >
          </div>

          <!-- Compact Action Button -->
          <button
            @click="closeSuccessModal"
            class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white py-4 px-8 rounded-xl font-extrabold hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-200 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-[1.02] flex items-center justify-center gap-3"
          >
            <i class="fas fa-home text-xl"></i>
            <span class="text-xl">Return to Dashboard</span>
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
  import OrbitingDots from '@/components/common/OrbitingDots.vue'
  import UnifiedLoadingBanner from '@/components/common/UnifiedLoadingBanner.vue'
  import userCombinedAccessService from '@/services/userCombinedAccessService'
  import userProfileService from '@/services/userProfileService'
  import signatureService from '@/services/signatureService'

  export default {
    name: 'UserCombinedAccessForm',
    components: {
      ModernSidebar,
      AppFooter,
      AppHeader,
      OrbitingDots,
      UnifiedLoadingBanner
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
        // Digital signature state
        isSigning: false,
        hasUserSigned: false,
        lastSignedAt: null,
        departments: [],
        // Auto-population states
        isLoadingProfile: true,
        // Modules loading state
        isLoadingModules: false,
        profileLoadError: null,
        autoPopulated: false,
        // Edit mode properties
        isEditMode: false,
        editRequestId: null,
        originalRequestData: null,
        // Store selected services for success modal display
        submittedServices: {
          jeeva: false,
          wellsoft: false,
          internet: false
        },
        submittedInternetPurposes: [],
        // Module data from database
        wellsoftModules: [],
        jeevaModules: [],
        // Request type (use/revoke)
        wellsoftRequestType: 'use',
        // Access rights
        accessType: '', // No default - user must select
        temporaryUntil: '',
        temporaryUntilDisplay: '', // DD/MM/YYYY format for display
        showDatePickerModal: false,
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
        // If showing success modal, use submitted services, otherwise use current form data
        if (this.showSuccessModal) {
          return (
            this.submittedServices.jeeva ||
            this.submittedServices.wellsoft ||
            this.submittedServices.internet
          )
        }
        return (
          this.formData.services.jeeva ||
          this.formData.services.wellsoft ||
          this.formData.services.internet
        )
      },
      hasInternetPurposes() {
        // If showing success modal, use submitted purposes, otherwise use current form data
        if (this.showSuccessModal) {
          return this.submittedInternetPurposes.some((purpose) => purpose.trim() !== '')
        }
        return this.formData.internetPurposes.some((purpose) => purpose.trim() !== '')
      },
      filledInternetPurposes() {
        // If showing success modal, use submitted purposes, otherwise use current form data
        if (this.showSuccessModal) {
          return this.submittedInternetPurposes.filter((purpose) => purpose.trim() !== '')
        }
        return this.formData.internetPurposes.filter((purpose) => purpose.trim() !== '')
      },
      tomorrowDate() {
        const d = new Date()
        d.setDate(d.getDate() + 1)
        const yyyy = d.getFullYear()
        const mm = String(d.getMonth() + 1).padStart(2, '0')
        const dd = String(d.getDate()).padStart(2, '0')
        return `${yyyy}-${mm}-${dd}`
      },
      formattedDateDisplay() {
        if (!this.temporaryUntil) return ''
        const [year, month, day] = this.temporaryUntil.split('-')
        return `${day}/${month}/${year}`
      }
    },

    watch: {
      // Auto-set services based on module selections
      'formData.selectedWellsoft': {
        handler(newVal) {
          // Automatically set wellsoft service to true if modules are selected
          this.formData.services.wellsoft = newVal && newVal.length > 0
          console.log('👁️ Wellsoft modules changed:', {
            count: newVal?.length || 0,
            service_enabled: this.formData.services.wellsoft
          })
        },
        deep: true
      },
      'formData.selectedJeeva': {
        handler(newVal) {
          // Automatically set jeeva service to true if modules are selected
          this.formData.services.jeeva = newVal && newVal.length > 0
          console.log('👁️ Jeeva modules changed:', {
            count: newVal?.length || 0,
            service_enabled: this.formData.services.jeeva
          })
        },
        deep: true
      },
      'formData.internetPurposes': {
        handler(newVal) {
          // Automatically set internet service to true if any purpose is filled
          const hasPurpose = newVal && newVal.some((purpose) => purpose.trim() !== '')
          this.formData.services.internet = hasPurpose
          console.log('👁️ Internet purposes changed:', {
            filled_count: newVal?.filter((p) => p.trim() !== '').length || 0,
            service_enabled: this.formData.services.internet
          })
        },
        deep: true
      }
    },

    async mounted() {
      console.log('🔄 UserCombinedAccessForm: Component mounted, starting initialization...')

      // Check if in edit mode
      this.checkEditMode()

      const isSynthetic = this.isSyntheticDocId?.() || (this.editRequestId && !/^\d+$/.test(String(this.editRequestId)))

      // Load data: if editing a real request id, fetch it; otherwise just load user data and modules
      if (this.isEditMode && !isSynthetic) {
        await Promise.all([this.loadExistingRequest(), this.loadDepartments(), this.loadModules()])
      } else {
        await Promise.all([this.autoPopulateUserData(), this.loadDepartments(), this.loadModules()])
      }

      console.log('✅ UserCombinedAccessForm: Initialization completed')
    },

    methods: {
      isSyntheticDocId() {
        const id = this.editRequestId || this.$route.query.id
        if (!id) return false
        // Treat known tokens and any non-numeric id as synthetic
        const known = ['combined_access', 'booking_service', 'combined_access_form']
        return known.includes(String(id)) || !/^\d+$/.test(String(id))
      },
      normalizePhoneNumber(input) {
        if (!input) return ''
        let v = String(input).trim().replace(/\s|-/g, '')
        if (v.startsWith('+255')) return v
        if (v.startsWith('255')) return '+' + v
        if (v.startsWith('0')) return '+255' + v.slice(1)
        return v
      },
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
        this.formData.phoneNumber = this.normalizePhoneNumber(
          data.phone_number || data.phoneNumber || data.phone || data.mobile || data.contact || ''
        )
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
       * Load available modules from the database
       */
      async loadModules() {
        console.log('🔄 Loading Jeeva and Wellsoft modules from database...')
        this.isLoadingModules = true
        try {
          // Load both module types in parallel
          const [wellsoftResponse, jeevaResponse] = await Promise.all([
            userCombinedAccessService.getWellsoftModules(),
            userCombinedAccessService.getJeevaModules()
          ])

          // Process Wellsoft modules
          if (
            wellsoftResponse &&
            wellsoftResponse.success &&
            Array.isArray(wellsoftResponse.data)
          ) {
            this.wellsoftModules = wellsoftResponse.data.map((module) => module.name).sort()
            console.log('✅ Wellsoft modules loaded:', this.wellsoftModules.length)
          } else {
            console.warn('⚠️ Failed to load Wellsoft modules, using empty array')
            this.wellsoftModules = []
          }

          // Process Jeeva modules
          if (jeevaResponse && jeevaResponse.success && Array.isArray(jeevaResponse.data)) {
            this.jeevaModules = jeevaResponse.data.map((module) => module.name).sort()
            console.log('✅ Jeeva modules loaded:', this.jeevaModules.length)
          } else {
            console.warn('⚠️ Failed to load Jeeva modules, using empty array')
            this.jeevaModules = []
          }
        } catch (error) {
          console.error('❌ Error loading modules:', error)
          this.wellsoftModules = []
          this.jeevaModules = []
          this.showNotification('Failed to load module options', 'warning')
        } finally {
          this.isLoadingModules = false
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
            this.formData.phoneNumber = this.normalizePhoneNumber(result.data.phoneNumber || '')
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
              this.formData.phoneNumber = this.normalizePhoneNumber(result.data.phoneNumber || '')
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


        if (!this.hasSelectedService) {
          this.showNotification('Please select at least one service', 'error')
          return
        }

        // Validate access rights selection
        if (!this.accessType) {
          this.showNotification('Please specify access rights (Permanent or Temporary)', 'error')
          return
        }

        // Validate temporary date if temporary access is selected
        if (this.accessType === 'temporary' && !this.temporaryUntil) {
          this.showNotification('Please specify the temporary access end date', 'error')
          return
        }

        // Note: Module selection is done by HOD during approval process
        // Users only select services, HOD selects specific modules at /both-service-form/:id
        console.log('📋 Service selections for HOD approval:', {
          jeeva: this.formData.services.jeeva,
          wellsoft: this.formData.services.wellsoft,
          internet: this.formData.services.internet
        })

        // Internet purposes are optional - form is dynamic and allows submission without them
        // The purposes can be added or edited later by HOD or during approval process

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
          if (this.formData.signature) {
            formData.append('signature', this.formData.signature)
          }

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

          // Add module selections (user now selects modules, HOD can edit them later)
          console.log('📝 Submitting with module selections:', {
            jeeva: this.formData.services.jeeva,
            wellsoft: this.formData.services.wellsoft,
            internet: this.formData.services.internet,
            selectedWellsoft: this.formData.selectedWellsoft,
            selectedJeeva: this.formData.selectedJeeva,
            requestType: this.wellsoftRequestType,
            accessType: this.accessType,
            temporaryUntil: this.temporaryUntil
          })

          // Add Wellsoft modules if Wellsoft service is selected
          if (this.formData.services.wellsoft && this.formData.selectedWellsoft.length > 0) {
            this.formData.selectedWellsoft.forEach((module, index) => {
              formData.append(`selectedWellsoft[${index}]`, module)
            })
          }

          // Add Jeeva modules if Jeeva service is selected
          if (this.formData.services.jeeva && this.formData.selectedJeeva.length > 0) {
            this.formData.selectedJeeva.forEach((module, index) => {
              formData.append(`selectedJeeva[${index}]`, module)
            })
          }

          // Add module request type (use/revoke) - backend expects camelCase
          formData.append('wellsoftRequestType', this.wellsoftRequestType)

          // Add access rights information - backend expects camelCase
          formData.append('accessType', this.accessType)
          if (this.accessType === 'temporary' && this.temporaryUntil) {
            formData.append('temporaryUntil', this.temporaryUntil)
          }

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

            // Preserve service selections for success modal display
            this.submittedServices = { ...this.formData.services }
            this.submittedInternetPurposes = [
              ...this.formData.internetPurposes.filter((p) => p.trim() !== '')
            ]

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

      getDepartmentName(departmentId) {
        if (!departmentId) {
          return 'Please select department'
        }

        const department = this.departments.find((dept) => dept.id == departmentId)
        return department ? department.name : `Department ID: ${departmentId}`
      },

      openDatePicker() {
        // Trigger the hidden native date picker
        if (this.$refs.dateInput) {
          this.$refs.dateInput.showPicker()
        }
      },

      openDatePickerModal() {
        this.showDatePickerModal = true
      },

      closeDatePickerModal() {
        this.showDatePickerModal = false
      },

      updateDisplayDate() {
        // This is called when the date picker value changes
        // The computed property formattedDateDisplay will automatically update
      },

      async signCurrentDocument() {
        try {
          if (this.isSigning) return
          const documentId = this.editRequestId || this.$route.query.id
          if (!documentId) {
            this.showNotification('Cannot sign until the request exists. Save/submit first or use upload.', 'error')
            return
          }
          this.isSigning = true
          const res = await signatureService.sign(documentId)
          if (res && (res.success || res.data)) {
            this.hasUserSigned = true
            this.lastSignedAt = res?.data?.signed_at || res?.signed_at || new Date().toISOString()
            this.showNotification(`Document signed on ${new Date(this.lastSignedAt).toLocaleString()}`, 'success')
          } else {
            this.showNotification(res?.message || 'Failed to sign document', 'error')
          }
        } catch (e) {
          console.error('Sign error:', e)
          this.showNotification(e.message || 'Failed to sign document', 'error')
        } finally {
          this.isSigning = false
        }
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
      },

      getLoadingTitle() {
        if (this.isSubmitting) {
          return 'Submitting Request'
        }
        if (this.isLoadingProfile && this.isEditMode) {
          return 'Loading Request Data'
        }
        if (this.isLoadingProfile) {
          return 'Loading Your Profile'
        }
        if (this.isLoadingModules) {
          return 'Loading Module Lists'
        }
        return 'Loading Data'
      },

      getLoadingSubtitle() {
        if (this.isSubmitting) {
          return 'Processing your combined access request...'
        }
        if (this.isLoadingProfile && this.isEditMode) {
          return 'Retrieving existing request data...'
        }
        if (this.isLoadingProfile) {
          return 'Auto-populating your details...'
        }
        if (this.isLoadingModules) {
          return 'Fetching Jeeva and Wellsoft modules...'
        }
        return 'Please wait...'
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
