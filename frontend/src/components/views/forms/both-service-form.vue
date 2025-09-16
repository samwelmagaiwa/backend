<template>
  <div class="flex flex-col h-screen">
    <Header />
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
                  class="w-full h-full bg-gradient-to-br from-blue-500/20 to-teal-500/20 rounded-2xl backdrop-blur-sm border-2 border-blue-300/40 flex items-center justify-center shadow-2xl hover:shadow-blue-500/25"
                >
                  <img
                    src="/assets/images/ngao2.png"
                    alt="National Shield"
                    class="max-w-18 max-h-18 object-contain"
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
                <div class="relative inline-block mb-4">
                  <div
                    class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-2 rounded-full text-base font-bold shadow-xl transform hover:scale-105 transition-all duration-300 border-2 border-blue-400/60"
                  >
                    <span class="relative z-10 flex items-center gap-2">
                      <i class="fas fa-layer-group"></i>
                      COMBINED ACCESS REQUEST
                    </span>
                    <div
                      class="absolute inset-0 bg-gradient-to-r from-blue-700 to-blue-800 rounded-full opacity-0 hover:opacity-100 transition-opacity duration-300"
                    ></div>
                  </div>
                </div>
                <h2
                  class="text-base font-bold text-blue-100 tracking-wide drop-shadow-md animate-fade-in-delay"
                >
                  {{ isReviewMode ? 'REQUEST REVIEW - ' + requestId : 'UNIFIED SERVICES FORM' }}
                </h2>
              </div>

              <!-- Right Logo -->
              <div
                class="w-20 h-20 ml-4 transform hover:scale-110 transition-transform duration-300"
              >
                <div
                  class="w-full h-full bg-gradient-to-br from-teal-500/20 to-blue-500/20 rounded-2xl backdrop-blur-sm border-2 border-teal-300/40 flex items-center justify-center shadow-2xl hover:shadow-teal-500/25"
                >
                  <img
                    src="/assets/images/logo2.png"
                    alt="Muhimbili Logo"
                    class="max-w-18 max-h-18 object-contain"
                  />
                </div>
              </div>
            </div>
          </div>

          <!-- Main Form -->
          <div class="medical-glass-card rounded-b-3xl overflow-hidden">
            <form
              @submit.prevent="onSubmit"
              :class="['p-4 space-y-4', { 'review-mode': isReviewMode }]"
            >
              <div class="flex-1 grid grid-cols-1 lg:grid-cols-2 gap-2 lg:gap-2 min-h-0">
                <!-- Left: shared + selectors -->
                <section aria-labelledby="applicant-details" class="xl:col-span-4 space-y-2">
                  <!-- Personal Information Section -->
                  <div
                    class="medical-card bg-gradient-to-r from-blue-600/25 to-blue-700/25 border-2 border-blue-400/40 p-2 rounded-lg backdrop-blur-sm hover:shadow-lg hover:shadow-blue-500/20 transition-all duration-300 group"
                  >
                    <div class="flex items-center space-x-2 mb-2">
                      <div
                        class="w-6 h-6 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-blue-300/50"
                      >
                        <i class="fas fa-user-md text-white text-xs"></i>
                      </div>
                      <h3 class="text-sm font-bold text-white flex items-center">
                        <i class="fas fa-id-card mr-1 text-blue-300 text-xs"></i>
                        Personal Information
                      </h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                      <div>
                        <label class="block text-xs font-bold text-blue-100 mb-1">
                          PF Number <span class="text-red-400">*</span>
                        </label>
                        <div class="relative">
                          <input
                            v-model.trim="form.shared.pfNumber"
                            type="text"
                            :readonly="isReviewMode"
                            class="medical-input w-full px-2 py-1 bg-white/15 border-2 border-blue-300/30 rounded-lg focus:border-blue-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-blue-500/20 text-sm"
                            placeholder="PF Number"
                            required
                          />
                          <div
                            class="absolute inset-0 rounded-xl bg-gradient-to-r from-blue-500/10 to-blue-600/10 opacity-0 hover:opacity-100 transition-opacity duration-300 pointer-events-none"
                          ></div>
                        </div>
                        <p v-if="errors.pfNumber" class="error text-red-400 text-sm mt-1">
                          {{ errors.pfNumber }}
                        </p>
                      </div>

                      <div>
                        <label class="block text-xs font-bold text-blue-100 mb-1">
                          Staff Name <span class="text-red-400">*</span>
                        </label>
                        <div class="relative">
                          <input
                            v-model.trim="form.shared.staffName"
                            type="text"
                            :readonly="isReviewMode"
                            class="medical-input w-full px-2 py-1 bg-white/15 border-2 border-blue-300/30 rounded-lg focus:border-blue-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-blue-500/20 text-sm"
                            placeholder="Full name"
                            required
                          />
                          <div
                            class="absolute inset-0 rounded-lg bg-gradient-to-r from-blue-500/10 to-blue-600/10 opacity-0 hover:opacity-100 transition-opacity duration-300 pointer-events-none"
                          ></div>
                        </div>
                        <p v-if="errors.staffName" class="error text-red-400 text-sm mt-1">
                          {{ errors.staffName }}
                        </p>
                      </div>

                      <div>
                        <label class="block text-xs font-bold text-blue-100 mb-1">
                          Department <span class="text-red-400">*</span>
                        </label>
                        <div class="relative">
                          <input
                            v-model.trim="form.shared.department"
                            type="text"
                            :readonly="isReviewMode"
                            class="medical-input w-full px-2 py-1 bg-white/15 border-2 border-blue-300/30 rounded-lg focus:border-blue-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-blue-500/20 text-sm"
                            placeholder="Department"
                            required
                          />
                          <div
                            class="absolute inset-0 rounded-lg bg-gradient-to-r from-blue-500/10 to-blue-600/10 opacity-0 hover:opacity-100 transition-opacity duration-300 pointer-events-none"
                          ></div>
                        </div>
                      </div>

                      <div>
                        <label class="block text-xs font-bold text-blue-100 mb-1">
                          Contact Number <span class="text-red-400">*</span>
                        </label>
                        <div class="relative">
                          <input
                            v-model.trim="form.shared.phone"
                            type="tel"
                            :readonly="isReviewMode"
                            class="medical-input w-full px-2 py-1 bg-white/15 border-2 border-blue-300/30 rounded-lg focus:border-blue-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-blue-500/20 text-sm"
                            placeholder="e.g. 0712 000 000"
                            required
                          />
                          <div
                            class="absolute inset-0 rounded-lg bg-gradient-to-r from-blue-500/10 to-blue-600/10 opacity-0 hover:opacity-100 transition-opacity duration-300 pointer-events-none"
                          ></div>
                        </div>
                      </div>
                      <div class="md:col-span-3">
                        <label class="block text-xs font-bold text-blue-100 mb-2 text-center">
                          Signature <span class="text-red-400">*</span>
                        </label>
                        <div class="relative max-w-sm mx-auto">
                          <!-- Review mode: Show signature status from database -->
                          <div
                            v-if="isReviewMode"
                            class="w-full px-2 py-2 border-2 border-blue-300/40 rounded-lg bg-white/15 backdrop-blur-sm transition-all duration-300 shadow-lg hover:shadow-xl hover:shadow-blue-500/20 min-h-[60px] flex items-center justify-center"
                          >
                            <!-- Loading state -->
                            <div v-if="loading" class="text-center">
                              <i class="fas fa-spinner fa-spin text-blue-300 text-lg mb-1"></i>
                              <p class="text-blue-100 text-xs">Loading signature...</p>
                            </div>
                            <!-- Loaded state -->
                            <div v-else class="text-center">
                              <div v-if="hasSignature" class="mb-1">
                                <i class="fas fa-check-circle text-green-400 text-lg mb-1"></i>
                                <p class="text-green-300 text-xs font-semibold flex items-center justify-center gap-1">
                                  <i class="fas fa-check text-xs"></i>
                                  Signed
                                </p>
                                <p v-if="requestData?.signature_path" class="text-blue-200 text-xs mt-1">
                                  File: {{ getSignatureFileName(requestData.signature_path) }}
                                </p>
                              </div>
                              <div v-else class="mb-1">
                                <i class="fas fa-signature text-blue-300 text-lg mb-1"></i>
                                <p class="text-blue-100 text-xs">No signature uploaded</p>
                              </div>
                            </div>
                          </div>

                          <!-- Edit mode: Show signature upload interface -->
                          <div
                            v-else-if="!signaturePreview"
                            class="w-full px-2 py-2 border-2 border-dashed border-blue-300/40 rounded-lg focus-within:border-blue-400 bg-white/15 backdrop-blur-sm transition-all duration-300 shadow-lg hover:shadow-xl hover:shadow-blue-500/20 min-h-[60px] flex items-center justify-center hover:bg-white/20"
                          >
                            <div class="text-center">
                              <div class="mb-1">
                                <i class="fas fa-signature text-blue-300 text-lg mb-1"></i>
                                <p class="text-blue-100 text-xs">No signature uploaded</p>
                              </div>
                              <button
                                type="button"
                                @click="triggerFileUpload"
                                class="px-3 py-1 bg-gradient-to-r from-blue-500 to-blue-600 text-white text-xs font-semibold rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-300 flex items-center gap-1 mx-auto shadow-lg hover:shadow-xl transform hover:scale-105 border border-blue-400/50"
                              >
                                <i class="fas fa-upload"></i>
                                Press to load your signature
                              </button>
                            </div>
                          </div>

                          <!-- Edit mode: Show uploaded signature preview -->
                          <div
                            v-else-if="!isReviewMode"
                            class="w-full px-2 py-2 border-2 border-blue-300/40 rounded-lg bg-white/15 backdrop-blur-sm transition-all duration-300 shadow-lg hover:shadow-xl hover:shadow-blue-500/20 min-h-[60px] flex items-center justify-center relative"
                          >
                            <div v-if="isImage(signaturePreview)" class="text-center">
                              <img
                                :src="signaturePreview"
                                alt="Digital Signature"
                                class="max-h-[50px] max-w-full object-contain mx-auto mb-1"
                              />
                              <p class="text-xs text-blue-100">
                                {{ signatureFileName }}
                              </p>
                            </div>
                            <div v-else class="text-center">
                              <div
                                class="w-16 h-16 bg-red-500/20 rounded-xl flex items-center justify-center mx-auto mb-2"
                              >
                                <i class="fas fa-file-pdf text-red-400 text-2xl"></i>
                              </div>
                              <p class="text-sm text-blue-100">
                                {{ signatureFileName }}
                              </p>
                            </div>

                            <div class="absolute top-2 right-2 flex gap-2">
                              <button
                                type="button"
                                @click="triggerFileUpload"
                                class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm hover:bg-blue-600 transition-colors duration-200 shadow-lg"
                                title="Change signature"
                              >
                                <i class="fas fa-edit"></i>
                              </button>
                              <button
                                type="button"
                                @click="clearSignature"
                                class="w-8 h-8 bg-red-500 text-white rounded-full flex items-center justify-center text-sm hover:bg-red-600 transition-colors duration-200 shadow-lg"
                                title="Remove signature"
                              >
                                <i class="fas fa-times"></i>
                              </button>
                            </div>
                          </div>

                          <input
                            v-if="!isReviewMode"
                            ref="signatureInput"
                            type="file"
                            accept="image/png,image/jpeg,application/pdf"
                            @change="onSignatureChange"
                            class="hidden"
                          />
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Module Request Section -->
                  <div
                    class="medical-card bg-gradient-to-r from-blue-600/25 to-blue-700/25 border-2 border-blue-400/40 p-2 rounded-lg backdrop-blur-sm hover:shadow-lg hover:shadow-blue-500/20 transition-all duration-300 group"
                  >
                    <div class="flex items-center space-x-2 mb-2">
                      <div
                        class="w-6 h-6 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-blue-300/50"
                      >
                        <i class="fas fa-desktop text-white text-xs"></i>
                      </div>
                      <h3 class="text-sm font-bold text-white flex items-center">
                        <i class="fas fa-th-large mr-1 text-blue-300 text-xs"></i>
                        Module Request
                      </h3>
                    </div>

                    <!-- Module Requested for - Compact -->
                    <div class="flex justify-center mb-3" :class="{ 'opacity-50': isReviewMode && !hasWellsoftRequest && !hasJeevaRequest }">
                      <div
                        class="bg-white/10 rounded-lg p-2 border border-blue-300/30 backdrop-blur-sm"
                      >
                        <label class="block text-sm font-bold text-blue-100 mb-2 text-center flex items-center justify-center gap-2">
                          <i class="fas fa-toggle-on mr-1 text-blue-300 text-xs"></i>
                          Module Requested for
                          <span class="text-red-400">*</span>
                          <span v-if="isReviewMode && !hasWellsoftRequest && !hasJeevaRequest" class="text-xs px-2 py-1 bg-gray-500/30 rounded-full text-gray-300">
                            <i class="fas fa-lock text-xs mr-1"></i>
                            Not Applicable
                          </span>
                        </label>
                        <div class="flex items-center gap-4 justify-center">
                          <label
                            class="flex items-center cursor-pointer hover:bg-blue-500/20 p-2 rounded transition-all"
                            :class="{ 'pointer-events-none': isReviewMode && !hasWellsoftRequest && !hasJeevaRequest }"
                          >
                            <input
                              v-model="wellsoftRequestType"
                              type="radio"
                              value="use"
                              :disabled="isReviewMode && !hasWellsoftRequest && !hasJeevaRequest"
                              class="w-4 h-4 text-blue-600 border-blue-300 focus:ring-blue-500 mr-2"
                            />
                            <span class="text-sm font-medium text-blue-100 flex items-center">
                              <i class="fas fa-plus-circle mr-1 text-green-400 text-xs"></i>
                              Use
                            </span>
                          </label>
                          <label
                            class="flex items-center cursor-pointer hover:bg-red-500/20 p-2 rounded transition-all"
                            :class="{ 'pointer-events-none': isReviewMode && !hasWellsoftRequest && !hasJeevaRequest }"
                          >
                            <input
                              v-model="wellsoftRequestType"
                              type="radio"
                              value="revoke"
                              :disabled="isReviewMode && !hasWellsoftRequest && !hasJeevaRequest"
                              class="w-4 h-4 text-blue-600 border-blue-300 focus:ring-blue-500 mr-2"
                            />
                            <span class="text-sm font-medium text-blue-100 flex items-center">
                              <i class="fas fa-minus-circle mr-1 text-red-400 text-xs"></i>
                              Revoke
                            </span>
                          </label>
                        </div>
                      </div>
                    </div>

                    <!-- Wellsoft selector -->
                    <div class="mb-6" :class="{ 'opacity-50': isWellsoftReadonly }">
                      <label class="block text-sm font-bold text-blue-100 mb-3 flex items-center gap-2">
                        Wellsoft Modules <span class="text-red-400">*</span>
                        <span v-if="isWellsoftReadonly" class="text-xs px-2 py-1 bg-gray-500/30 rounded-full text-gray-300">
                          <i class="fas fa-lock text-xs mr-1"></i>
                          Not Requested
                        </span>
                      </label>
                      <div class="flex items-center justify-between mb-3">
                        <span class="text-sm text-blue-200"
                          >{{ selectedWellsoft.length }} modules selected</span
                        >
                        <div v-if="!isWellsoftReadonly" class="flex items-center gap-2 text-sm">
                          <button
                            type="button"
                            class="px-3 py-1 bg-blue-500/20 text-blue-200 rounded-lg hover:bg-blue-500/30 transition-colors backdrop-blur-sm border border-blue-400/30"
                            @click="selectAll('wellsoft')"
                          >
                            Select all
                          </button>
                          <button
                            type="button"
                            class="px-3 py-1 bg-white/10 text-blue-200 rounded-lg hover:bg-white/20 transition-colors backdrop-blur-sm border border-blue-400/30"
                            @click="clearAll('wellsoft')"
                          >
                            Clear
                          </button>
                        </div>
                      </div>
                      <div v-if="!isWellsoftReadonly" class="relative mb-2">
                        <input
                          v-model.trim="wellsoftQuery"
                          class="medical-input w-full px-3 py-2 pl-8 bg-white/15 border border-blue-300/30 rounded-lg focus:border-blue-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm text-sm"
                          placeholder="Search Wellsoft modules"
                        />
                        <i
                          class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-blue-300 text-xs"
                        ></i>
                      </div>

                      <!-- Selected chips -->
                      <div v-if="selectedWellsoft.length" class="flex flex-wrap gap-2 mb-4">
                        <span
                          v-for="m in selectedWellsoft"
                          :key="'selW-' + m"
                          class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-blue-500/20 text-blue-100 text-sm backdrop-blur-sm border border-blue-400/30"
                        >
                          <i class="fas fa-check text-blue-300"></i> {{ m }}
                          <button
                            v-if="!isWellsoftReadonly"
                            type="button"
                            @click="toggleWellsoft(m)"
                            class="ml-1 hover:text-blue-200 transition-colors"
                          >
                            <i class="fas fa-times"></i>
                          </button>
                        </span>
                      </div>

                      <!-- No modules message for readonly state -->
                      <div v-if="isWellsoftReadonly && selectedWellsoft.length === 0" 
                           class="bg-gray-500/10 rounded-lg p-4 border border-gray-400/30 text-center">
                        <i class="fas fa-info-circle text-gray-400 mb-2 text-lg"></i>
                        <p class="text-gray-300 text-sm">No Wellsoft modules requested</p>
                      </div>

                      <!-- Options grid -->
                      <div
                        v-if="!isWellsoftReadonly"
                        class="bg-white/10 rounded-lg p-3 max-h-40 border border-blue-300/30 overflow-y-auto backdrop-blur-sm"
                      >
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
                          <label
                            v-for="m in filteredWellsoft"
                            :key="'optW-' + m"
                            class="flex items-center p-2 hover:bg-blue-500/20 rounded-lg cursor-pointer transition-colors border border-blue-400/20"
                          >
                            <input
                              type="checkbox"
                              :checked="isSelected('wellsoft', m)"
                              @change="toggleWellsoft(m)"
                              class="w-4 h-4 text-blue-600 border-blue-300 rounded focus:ring-blue-500 mr-3 module-request-editable"
                            />
                            <span class="text-sm font-medium text-blue-100">{{ m }}</span>
                          </label>
                        </div>
                      </div>
                    </div>

                    <!-- Jeeva selector -->
                    <div class="mb-6" :class="{ 'opacity-50': isJeevaReadonly }">
                      <label class="block text-sm font-bold text-blue-100 mb-3 flex items-center gap-2">
                        Jeeva Modules <span class="text-red-400">*</span>
                        <span v-if="isJeevaReadonly" class="text-xs px-2 py-1 bg-gray-500/30 rounded-full text-gray-300">
                          <i class="fas fa-lock text-xs mr-1"></i>
                          Not Requested
                        </span>
                      </label>
                      <div class="flex items-center justify-between mb-3">
                        <span class="text-sm text-blue-200"
                          >{{ selectedJeeva.length }} modules selected</span
                        >
                        <div v-if="!isJeevaReadonly" class="flex items-center gap-2 text-sm">
                          <button
                            type="button"
                            class="px-3 py-1 bg-cyan-500/20 text-cyan-200 rounded-lg hover:bg-cyan-500/30 transition-colors backdrop-blur-sm border border-cyan-400/30"
                            @click="selectAll('jeeva')"
                          >
                            Select all
                          </button>
                          <button
                            type="button"
                            class="px-3 py-1 bg-white/10 text-blue-200 rounded-lg hover:bg-white/20 transition-colors backdrop-blur-sm border border-blue-400/30"
                            @click="clearAll('jeeva')"
                          >
                            Clear
                          </button>
                        </div>
                      </div>
                      <div v-if="!isJeevaReadonly" class="relative mb-2">
                        <input
                          v-model.trim="jeevaQuery"
                          class="medical-input w-full px-3 py-2 pl-8 bg-white/15 border border-blue-300/30 rounded-lg focus:border-cyan-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm text-sm"
                          placeholder="Search Jeeva modules"
                        />
                        <i
                          class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-cyan-300 text-xs"
                        ></i>
                      </div>

                      <div v-if="selectedJeeva.length" class="flex flex-wrap gap-2 mb-4">
                        <span
                          v-for="m in selectedJeeva"
                          :key="'selJ-' + m"
                          class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-cyan-500/20 text-cyan-100 text-sm backdrop-blur-sm border border-cyan-400/30"
                        >
                          <i class="fas fa-check text-cyan-300"></i> {{ m }}
                          <button
                            v-if="!isJeevaReadonly"
                            type="button"
                            @click="toggleJeeva(m)"
                            class="ml-1 hover:text-cyan-200 transition-colors"
                          >
                            <i class="fas fa-times"></i>
                          </button>
                        </span>
                      </div>

                      <!-- No modules message for readonly state -->
                      <div v-if="isJeevaReadonly && selectedJeeva.length === 0" 
                           class="bg-gray-500/10 rounded-lg p-4 border border-gray-400/30 text-center">
                        <i class="fas fa-info-circle text-gray-400 mb-2 text-lg"></i>
                        <p class="text-gray-300 text-sm">No Jeeva modules requested</p>
                      </div>

                      <div
                        v-if="!isJeevaReadonly"
                        class="bg-white/10 rounded-lg p-3 max-h-40 border border-blue-300/30 overflow-y-auto backdrop-blur-sm"
                      >
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
                          <label
                            v-for="m in filteredJeeva"
                            :key="'optJ-' + m"
                            class="flex items-center p-2 hover:bg-cyan-500/20 rounded-lg cursor-pointer transition-colors border border-cyan-400/20"
                          >
                            <input
                              type="checkbox"
                              :checked="isSelected('jeeva', m)"
                              @change="toggleJeeva(m)"
                              class="w-4 h-4 text-cyan-600 border-cyan-300 rounded focus:ring-cyan-500 mr-3 module-request-editable"
                            />
                            <span class="text-sm font-medium text-cyan-100">{{ m }}</span>
                          </label>
                        </div>
                      </div>
                    </div>

                    <!-- Internet selector -->
                    <div class="mb-6" :class="{ 'opacity-50': isInternetReadonly }">
                      <label class="block text-sm font-bold text-blue-100 mb-3 flex items-center gap-2">
                        Internet Purpose <span class="text-red-400">*</span>
                        <span v-if="isInternetReadonly" class="text-xs px-2 py-1 bg-gray-500/30 rounded-full text-gray-300">
                          <i class="fas fa-lock text-xs mr-1"></i>
                          Not Requested
                        </span>
                      </label>
                      
                      <!-- No purposes message for readonly state -->
                      <div v-if="isInternetReadonly && !internetPurposes.some(p => p.trim())" 
                           class="bg-gray-500/10 rounded-lg p-4 border border-gray-400/30 text-center">
                        <i class="fas fa-info-circle text-gray-400 mb-2 text-lg"></i>
                        <p class="text-gray-300 text-sm">No internet access purposes requested</p>
                      </div>
                      
                      <!-- Purpose inputs -->
                      <div v-if="!isInternetReadonly || internetPurposes.some(p => p.trim())" class="space-y-2">
                        <div
                          v-for="(purpose, index) in internetPurposes"
                          :key="index"
                          class="flex items-center gap-2"
                        >
                          <span
                            class="text-xs font-medium text-blue-300 w-5 h-5 bg-blue-500/20 rounded-full flex items-center justify-center border border-blue-400/30"
                            >{{ index + 1 }}</span
                          >
                          <div class="flex-1 relative">
                            <input
                              v-model="internetPurposes[index]"
                              type="text"
                              :readonly="isInternetReadonly"
                              class="medical-input w-full px-3 py-2 bg-white/15 border border-blue-300/30 rounded-lg focus:border-blue-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 module-request-editable text-sm"
                              :class="{ 'cursor-not-allowed': isInternetReadonly }"
                              :placeholder="isInternetReadonly ? '' : `Purpose ${index + 1}`"
                              :required="index === 0 && !isInternetReadonly"
                            />
                            <div
                              v-if="!isInternetReadonly"
                              class="absolute inset-0 rounded-lg bg-gradient-to-r from-blue-500/10 to-cyan-500/10 opacity-0 hover:opacity-100 transition-opacity duration-300 pointer-events-none"
                            ></div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Access Rights Section -->
                  <div
                    class="medical-card bg-gradient-to-r from-blue-600/25 to-blue-700/25 border-2 border-blue-400/40 p-3 rounded-lg backdrop-blur-sm hover:shadow-lg hover:shadow-blue-500/20 transition-all duration-300 group"
                  >
                    <div class="flex items-center space-x-2 mb-3">
                      <div
                        class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-blue-300/50"
                      >
                        <i class="fas fa-lock text-white text-sm"></i>
                      </div>
                      <h3 class="text-base font-bold text-white flex items-center">
                        <i class="fas fa-key mr-1 text-blue-300"></i>
                        Access Rights
                      </h3>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                      <!-- Permanent Option -->
                      <div
                        class="flex items-center p-4 border-2 border-blue-300/30 rounded-xl hover:border-blue-400 hover:bg-white/10 transition-all backdrop-blur-sm"
                      >
                        <input
                          v-model="form.accessRights.type"
                          type="radio"
                          value="permanent"
                          class="w-5 h-5 text-blue-600 border-blue-300 focus:ring-blue-500 mr-4 access-rights-editable"
                        />
                        <span class="font-medium text-white text-sm"
                          >Permanent (until retirement)</span
                        >
                      </div>

                      <!-- Temporary Until Option -->
                      <div
                        class="flex items-center justify-between p-4 border-2 border-blue-300/30 rounded-xl hover:border-blue-400 hover:bg-white/10 transition-all backdrop-blur-sm"
                      >
                        <div class="flex items-center">
                          <input
                            v-model="form.accessRights.type"
                            type="radio"
                            value="temporary"
                            class="w-5 h-5 text-blue-600 border-blue-300 focus:ring-blue-500 mr-4 access-rights-editable"
                          />
                          <span class="font-medium text-white text-sm">Temporary Until</span>
                        </div>

                        <div
                          class="flex items-center gap-2"
                          v-if="form.accessRights.type === 'temporary'"
                        >
                          <input
                            v-model="form.accessRights.tempDate.month"
                            type="text"
                            placeholder="MM"
                            maxlength="2"
                            class="w-12 px-2 py-1 bg-white/15 border border-blue-300/30 rounded-lg text-center text-sm text-white focus:border-blue-400 focus:outline-none backdrop-blur-sm access-rights-editable"
                          />
                          <span class="text-blue-200">/</span>
                          <input
                            v-model="form.accessRights.tempDate.day"
                            type="text"
                            placeholder="DD"
                            maxlength="2"
                            class="w-12 px-2 py-1 bg-white/15 border border-blue-300/30 rounded-lg text-center text-sm text-white focus:border-blue-400 focus:outline-none backdrop-blur-sm access-rights-editable"
                          />
                          <span class="text-blue-200">/</span>
                          <input
                            v-model="form.accessRights.tempDate.year"
                            type="text"
                            placeholder="YYYY"
                            maxlength="4"
                            class="w-20 px-2 py-1 bg-white/15 border border-blue-300/30 rounded-lg text-center text-sm text-white focus:border-blue-400 focus:outline-none backdrop-blur-sm access-rights-editable"
                          />
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Approval Section -->
                  <div
                    class="medical-card bg-gradient-to-r from-blue-600/25 to-blue-700/25 border-2 border-blue-400/40 p-3 rounded-lg backdrop-blur-sm hover:shadow-lg hover:shadow-blue-500/20 transition-all duration-300 group"
                  >
                    <div class="flex items-center space-x-2 mb-3">
                      <div
                        class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-blue-300/50"
                      >
                        <i class="fas fa-check-circle text-white text-sm"></i>
                      </div>
                      <h3 class="text-base font-bold text-white flex items-center">
                        <i class="fas fa-clipboard-check mr-1 text-blue-300"></i>
                        Approval
                      </h3>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                      <!-- HoD/BM -->
                      <div
                        class="bg-white/15 rounded-lg p-4 border border-blue-300/30 backdrop-blur-sm"
                      >
                        <h5
                          class="font-bold text-white mb-4 text-center text-sm flex items-center justify-center"
                        >
                          <i class="fas fa-user-tie mr-2 text-blue-300"></i>
                          HoD/BM
                        </h5>
                        <div class="space-y-3">
                          <div>
                            <label class="block text-sm font-medium text-blue-100 mb-2"
                              >Name<span class="text-red-400">*</span></label
                            >
                            <div class="relative">
                              <input
                                v-model="form.approvals.hod.name"
                                type="text"
                                placeholder="Enter name"
                                class="medical-input w-full px-3 py-2 bg-white/15 border border-blue-300/30 rounded-lg focus:border-blue-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm hod-approval-editable"
                              />
                            </div>
                          </div>
                          <div>
                            <label class="block text-sm font-medium text-blue-100 mb-2"
                              >Signature<span class="text-red-400">*</span></label
                            >
                            <div class="relative">
                              <div
                                v-if="!hodSignaturePreview"
                                class="w-full px-3 py-2 border-2 border-dashed border-blue-300/40 rounded-lg focus-within:border-blue-400 bg-white/15 backdrop-blur-sm transition-all duration-300 shadow-lg hover:shadow-xl hover:shadow-blue-500/20 min-h-[50px] flex items-center justify-center hover:bg-white/20"
                              >
                                <div class="text-center">
                                  <div class="mb-2">
                                    <i class="fas fa-signature text-blue-300 text-lg mb-1"></i>
                                    <p class="text-blue-100 text-xs">No signature uploaded</p>
                                  </div>
                                  <button
                                    type="button"
                                    @click="triggerHodSignatureUpload"
                                    class="px-3 py-1.5 bg-gradient-to-r from-blue-500 to-blue-600 text-white text-xs font-semibold rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-300 flex items-center gap-1 mx-auto shadow-lg hover:shadow-xl transform hover:scale-105 border border-blue-400/50"
                                  >
                                    <i class="fas fa-upload"></i>
                                    Upload Signature
                                  </button>
                                </div>
                              </div>

                              <div
                                v-else
                                class="w-full px-3 py-3 border-2 border-emerald-300/40 rounded-xl bg-white/15 backdrop-blur-sm transition-all duration-300 shadow-lg hover:shadow-xl hover:shadow-emerald-500/20 min-h-[70px] flex items-center justify-center relative"
                              >
                                <div v-if="isImage(hodSignaturePreview)" class="text-center">
                                  <img
                                    :src="hodSignaturePreview"
                                    alt="HOD Signature"
                                    class="max-h-[50px] max-w-full object-contain mx-auto mb-1"
                                  />
                                  <p class="text-xs text-blue-100">
                                    {{ hodSignatureFileName }}
                                  </p>
                                </div>
                                <div v-else class="text-center">
                                  <div
                                    class="w-12 h-12 bg-red-500/20 rounded-xl flex items-center justify-center mx-auto mb-1"
                                  >
                                    <i class="fas fa-file-pdf text-red-400 text-lg"></i>
                                  </div>
                                  <p class="text-xs text-blue-100">
                                    {{ hodSignatureFileName }}
                                  </p>
                                </div>

                                <div class="absolute top-2 right-2 flex gap-1">
                                  <button
                                    type="button"
                                    @click="triggerHodSignatureUpload"
                                    class="w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-xs hover:bg-blue-600 transition-colors duration-200 shadow-lg"
                                    title="Change signature"
                                  >
                                    <i class="fas fa-edit"></i>
                                  </button>
                                  <button
                                    type="button"
                                    @click="clearHodSignature"
                                    class="w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center text-xs hover:bg-red-600 transition-colors duration-200 shadow-lg"
                                    title="Remove signature"
                                  >
                                    <i class="fas fa-times"></i>
                                  </button>
                                </div>
                              </div>

                              <input
                                ref="hodSignatureInput"
                                type="file"
                                accept="image/png,image/jpeg,application/pdf"
                                @change="onHodSignatureChange"
                                class="hidden"
                              />
                            </div>
                          </div>
                          <div>
                            <label class="block text-sm font-medium text-blue-100 mb-2"
                              >Date (mm/dd/yyyy)<span class="text-red-400">*</span></label
                            >
                            <div class="relative">
                              <input
                                v-model="form.approvals.hod.date"
                                type="date"
                                class="medical-input w-full px-3 py-2 bg-white/15 border border-blue-300/30 rounded-lg focus:border-blue-400 focus:outline-none text-white backdrop-blur-sm hod-approval-editable"
                              />
                            </div>
                          </div>
                        </div>
                      </div>

                      <!-- Divisional Director -->
                      <div
                        class="bg-white/15 rounded-lg p-4 border border-blue-300/30 backdrop-blur-sm"
                      >
                        <h5
                          class="font-bold text-white mb-4 text-center text-sm flex items-center justify-center"
                        >
                          <i class="fas fa-user-circle mr-2 text-blue-300"></i>
                          Divisional Director
                        </h5>
                        <div class="space-y-3">
                          <div>
                            <label class="block text-sm font-medium text-blue-100 mb-2"
                              >Name<span class="text-red-400">*</span></label
                            >
                            <div class="relative">
                              <input
                                v-model="form.approvals.divisionalDirector.name"
                                type="text"
                                placeholder="Enter name"
                                class="medical-input w-full px-3 py-2 bg-white/15 border border-blue-300/30 rounded-lg focus:border-blue-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm"
                              />
                            </div>
                          </div>
                          <div>
                            <label class="block text-sm font-medium text-blue-100 mb-2"
                              >Signature<span class="text-red-400">*</span></label
                            >
                            <div class="relative">
                              <div
                                v-if="!divDirectorSignaturePreview"
                                class="w-full px-3 py-2 border-2 border-dashed border-blue-300/40 rounded-lg focus-within:border-blue-400 bg-white/15 backdrop-blur-sm transition-all duration-300 shadow-lg hover:shadow-xl hover:shadow-blue-500/20 min-h-[50px] flex items-center justify-center hover:bg-white/20"
                              >
                                <div class="text-center">
                                  <div class="mb-2">
                                    <i class="fas fa-signature text-blue-300 text-lg mb-1"></i>
                                    <p class="text-blue-100 text-xs">No signature uploaded</p>
                                  </div>
                                  <button
                                    type="button"
                                    @click="triggerDivDirectorSignatureUpload"
                                    class="px-3 py-1.5 bg-gradient-to-r from-blue-500 to-blue-600 text-white text-xs font-semibold rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-300 flex items-center gap-1 mx-auto shadow-lg hover:shadow-xl transform hover:scale-105 border border-blue-400/50"
                                  >
                                    <i class="fas fa-upload"></i>
                                    Upload Signature
                                  </button>
                                </div>
                              </div>

                              <div
                                v-else
                                class="w-full px-3 py-3 border-2 border-emerald-300/40 rounded-xl bg-white/15 backdrop-blur-sm transition-all duration-300 shadow-lg hover:shadow-xl hover:shadow-emerald-500/20 min-h-[70px] flex items-center justify-center relative"
                              >
                                <div
                                  v-if="isImage(divDirectorSignaturePreview)"
                                  class="text-center"
                                >
                                  <img
                                    :src="divDirectorSignaturePreview"
                                    alt="Divisional Director Signature"
                                    class="max-h-[50px] max-w-full object-contain mx-auto mb-1"
                                  />
                                  <p class="text-xs text-blue-100">
                                    {{ divDirectorSignatureFileName }}
                                  </p>
                                </div>
                                <div v-else class="text-center">
                                  <div
                                    class="w-12 h-12 bg-red-500/20 rounded-xl flex items-center justify-center mx-auto mb-1"
                                  >
                                    <i class="fas fa-file-pdf text-red-400 text-lg"></i>
                                  </div>
                                  <p class="text-xs text-blue-100">
                                    {{ divDirectorSignatureFileName }}
                                  </p>
                                </div>

                                <div class="absolute top-2 right-2 flex gap-1">
                                  <button
                                    type="button"
                                    @click="triggerDivDirectorSignatureUpload"
                                    class="w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-xs hover:bg-blue-600 transition-colors duration-200 shadow-lg"
                                    title="Change signature"
                                  >
                                    <i class="fas fa-edit"></i>
                                  </button>
                                  <button
                                    type="button"
                                    @click="clearDivDirectorSignature"
                                    class="w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center text-xs hover:bg-red-600 transition-colors duration-200 shadow-lg"
                                    title="Remove signature"
                                  >
                                    <i class="fas fa-times"></i>
                                  </button>
                                </div>
                              </div>

                              <input
                                ref="divDirectorSignatureInput"
                                type="file"
                                accept="image/png,image/jpeg,application/pdf"
                                @change="onDivDirectorSignatureChange"
                                class="hidden"
                              />
                            </div>
                          </div>
                          <div>
                            <label class="block text-sm font-medium text-blue-100 mb-2"
                              >Date (mm/dd/yyyy)<span class="text-red-400">*</span></label
                            >
                            <div class="relative">
                              <input
                                v-model="form.approvals.divisionalDirector.date"
                                type="date"
                                class="medical-input w-full px-3 py-2 bg-white/15 border border-blue-300/30 rounded-lg focus:border-blue-400 focus:outline-none text-white backdrop-blur-sm"
                              />
                            </div>
                          </div>
                        </div>
                      </div>

                      <!-- Director of ICT -->
                      <div
                        class="bg-white/15 rounded-lg p-4 border border-blue-300/30 backdrop-blur-sm"
                      >
                        <h5
                          class="font-bold text-white mb-4 text-center text-sm flex items-center justify-center"
                        >
                          <i class="fas fa-laptop-code mr-2 text-blue-300"></i>
                          Director of ICT
                        </h5>
                        <div class="space-y-3">
                          <div>
                            <label class="block text-sm font-medium text-blue-100 mb-2"
                              >Name<span class="text-red-400">*</span></label
                            >
                            <div class="relative">
                              <input
                                v-model="form.approvals.directorICT.name"
                                type="text"
                                placeholder="Enter name"
                                class="medical-input w-full px-3 py-2 bg-white/15 border border-blue-300/30 rounded-lg focus:border-blue-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm"
                              />
                            </div>
                          </div>
                          <div>
                            <label class="block text-sm font-medium text-blue-100 mb-2"
                              >Signature<span class="text-red-400">*</span></label
                            >
                            <div class="relative">
                              <div
                                v-if="!directorICTSignaturePreview"
                                class="w-full px-3 py-2 border-2 border-dashed border-blue-300/40 rounded-lg focus-within:border-blue-400 bg-white/15 backdrop-blur-sm transition-all duration-300 shadow-lg hover:shadow-xl hover:shadow-blue-500/20 min-h-[50px] flex items-center justify-center hover:bg-white/20"
                              >
                                <div class="text-center">
                                  <div class="mb-2">
                                    <i class="fas fa-signature text-blue-300 text-lg mb-1"></i>
                                    <p class="text-blue-100 text-xs">No signature uploaded</p>
                                  </div>
                                  <button
                                    type="button"
                                    @click="triggerDirectorICTSignatureUpload"
                                    class="px-3 py-1.5 bg-gradient-to-r from-blue-500 to-blue-600 text-white text-xs font-semibold rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-300 flex items-center gap-1 mx-auto shadow-lg hover:shadow-xl transform hover:scale-105 border border-blue-400/50"
                                  >
                                    <i class="fas fa-upload"></i>
                                    Upload Signature
                                  </button>
                                </div>
                              </div>

                              <div
                                v-else
                                class="w-full px-3 py-3 border-2 border-emerald-300/40 rounded-xl bg-white/15 backdrop-blur-sm transition-all duration-300 shadow-lg hover:shadow-xl hover:shadow-emerald-500/20 min-h-[70px] flex items-center justify-center relative"
                              >
                                <div
                                  v-if="isImage(directorICTSignaturePreview)"
                                  class="text-center"
                                >
                                  <img
                                    :src="directorICTSignaturePreview"
                                    alt="Director ICT Signature"
                                    class="max-h-[50px] max-w-full object-contain mx-auto mb-1"
                                  />
                                  <p class="text-xs text-blue-100">
                                    {{ directorICTSignatureFileName }}
                                  </p>
                                </div>
                                <div v-else class="text-center">
                                  <div
                                    class="w-12 h-12 bg-red-500/20 rounded-xl flex items-center justify-center mx-auto mb-1"
                                  >
                                    <i class="fas fa-file-pdf text-red-400 text-lg"></i>
                                  </div>
                                  <p class="text-xs text-blue-100">
                                    {{ directorICTSignatureFileName }}
                                  </p>
                                </div>

                                <div class="absolute top-2 right-2 flex gap-1">
                                  <button
                                    type="button"
                                    @click="triggerDirectorICTSignatureUpload"
                                    class="w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-xs hover:bg-blue-600 transition-colors duration-200 shadow-lg"
                                    title="Change signature"
                                  >
                                    <i class="fas fa-edit"></i>
                                  </button>
                                  <button
                                    type="button"
                                    @click="clearDirectorICTSignature"
                                    class="w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center text-xs hover:bg-red-600 transition-colors duration-200 shadow-lg"
                                    title="Remove signature"
                                  >
                                    <i class="fas fa-times"></i>
                                  </button>
                                </div>
                              </div>

                              <input
                                ref="directorICTSignatureInput"
                                type="file"
                                accept="image/png,image/jpeg,application/pdf"
                                @change="onDirectorICTSignatureChange"
                                class="hidden"
                              />
                            </div>
                          </div>
                          <div>
                            <label class="block text-sm font-medium text-blue-100 mb-2"
                              >Date (mm/dd/yyyy)<span class="text-red-400">*</span></label
                            >
                            <div class="relative">
                              <input
                                v-model="form.approvals.directorICT.date"
                                type="date"
                                class="medical-input w-full px-3 py-2 bg-white/15 border border-blue-300/30 rounded-lg focus:border-blue-400 focus:outline-none text-white backdrop-blur-sm"
                              />
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Comments Section -->
                  <div
                    class="medical-card bg-gradient-to-r from-blue-600/25 to-blue-700/25 border-2 border-blue-400/40 p-3 rounded-lg backdrop-blur-sm hover:shadow-lg hover:shadow-blue-500/20 transition-all duration-300 group"
                  >
                    <div class="flex items-center space-x-2 mb-3">
                      <div
                        class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-blue-300/50"
                      >
                        <i class="fas fa-comments text-white text-sm"></i>
                      </div>
                      <h3 class="text-base font-bold text-white flex items-center">
                        <i class="fas fa-comment-alt mr-1 text-blue-300"></i>
                        Comments
                      </h3>
                    </div>
                    <div class="relative">
                      <textarea
                        v-model="form.comments"
                        rows="4"
                        class="medical-input w-full px-3 py-3 bg-white/15 border-2 border-blue-300/30 rounded-lg focus:border-blue-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-blue-500/20 resize-y comments-editable"
                        placeholder="HOD: specify access Category here..."
                      ></textarea>
                      <div
                        class="absolute inset-0 rounded-lg bg-gradient-to-r from-blue-500/10 to-blue-600/10 opacity-0 hover:opacity-100 transition-opacity duration-300 pointer-events-none"
                      ></div>
                    </div>
                  </div>

                  <!-- For Implementation Section -->
                  <div
                    class="medical-card bg-gradient-to-r from-blue-600/25 to-blue-700/25 border-2 border-blue-400/40 p-3 rounded-lg backdrop-blur-sm hover:shadow-lg hover:shadow-blue-500/20 transition-all duration-300 group"
                  >
                    <div class="flex items-center space-x-2 mb-3">
                      <div
                        class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-blue-300/50"
                      >
                        <i class="fas fa-cogs text-white text-sm"></i>
                      </div>
                      <h3 class="text-base font-bold text-white flex items-center">
                        <i class="fas fa-tools mr-1 text-blue-300"></i>
                        For Implementation
                      </h3>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                      <!-- Head of IT -->
                      <div
                        class="bg-white/15 rounded-lg p-4 border border-blue-300/30 backdrop-blur-sm"
                      >
                        <h5
                          class="font-bold text-white mb-3 text-center text-sm flex items-center justify-center"
                        >
                          <i class="fas fa-user-cog mr-2"></i>
                          Head of IT
                        </h5>
                        <div class="space-y-3">
                          <div>
                            <label class="block text-sm font-medium text-blue-100 mb-2"
                              >Name<span class="text-red-400">*</span></label
                            >
                            <div class="relative">
                              <input
                                v-model="form.implementation.headIT.name"
                                type="text"
                                placeholder="Enter name"
                                class="medical-input w-full px-3 py-2 bg-white/15 border border-blue-300/30 rounded-lg focus:border-blue-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm"
                              />
                            </div>
                          </div>
                          <div>
                            <label class="block text-sm font-medium text-blue-100 mb-2"
                              >Signature<span class="text-red-400">*</span></label
                            >
                            <div class="relative">
                              <div
                                v-if="!headITSignaturePreview"
                                class="w-full px-2 py-2 border border-blue-300/30 rounded-lg focus-within:border-blue-400 bg-white/15 transition-all duration-300 shadow-sm hover:shadow-md min-h-[50px] flex items-center justify-center backdrop-blur-sm"
                              >
                                <div class="text-center">
                                  <div class="mb-1">
                                    <i class="fas fa-signature text-blue-300 text-sm mb-1"></i>
                                    <p class="text-blue-100 text-xs">No signature</p>
                                  </div>
                                  <button
                                    type="button"
                                    @click="triggerHeadITSignatureUpload"
                                    class="px-2 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700 transition-colors duration-200 flex items-center gap-1 mx-auto"
                                  >
                                    <i class="fas fa-upload"></i>
                                    Press to load your signature
                                  </button>
                                </div>
                              </div>

                              <div
                                v-else
                                class="w-full px-2 py-2 border border-blue-300 rounded bg-gray-50 transition-all duration-300 shadow-sm hover:shadow-md min-h-[50px] flex items-center justify-center relative"
                              >
                                <div v-if="isImage(headITSignaturePreview)" class="text-center">
                                  <img
                                    :src="headITSignaturePreview"
                                    alt="Head IT Signature"
                                    class="max-h-[35px] max-w-full object-contain mx-auto mb-1"
                                  />
                                  <p class="text-xs text-gray-600">
                                    {{ headITSignatureFileName }}
                                  </p>
                                </div>
                                <div v-else class="text-center">
                                  <div
                                    class="w-8 h-8 bg-red-100 rounded flex items-center justify-center mx-auto mb-1"
                                  >
                                    <i class="fas fa-file-pdf text-red-600 text-sm"></i>
                                  </div>
                                  <p class="text-xs text-gray-600">
                                    {{ headITSignatureFileName }}
                                  </p>
                                </div>

                                <div class="absolute top-1 right-1 flex gap-1">
                                  <button
                                    type="button"
                                    @click="triggerHeadITSignatureUpload"
                                    class="w-5 h-5 bg-teal-500 text-white rounded-full flex items-center justify-center text-xs hover:bg-teal-600 transition-colors duration-200"
                                    title="Change signature"
                                  >
                                    <i class="fas fa-edit"></i>
                                  </button>
                                  <button
                                    type="button"
                                    @click="clearHeadITSignature"
                                    class="w-5 h-5 bg-red-500 text-white rounded-full flex items-center justify-center text-xs hover:bg-red-600 transition-colors duration-200"
                                    title="Remove signature"
                                  >
                                    <i class="fas fa-times"></i>
                                  </button>
                                </div>
                              </div>

                              <input
                                ref="headITSignatureInput"
                                type="file"
                                accept="image/png,image/jpeg,application/pdf"
                                @change="onHeadITSignatureChange"
                                class="hidden"
                              />
                            </div>
                          </div>
                          <div>
                            <label class="block text-sm font-medium text-blue-100 mb-2"
                              >Date (mm/dd/yyyy)<span class="text-red-400">*</span></label
                            >
                            <div class="relative">
                              <input
                                v-model="form.implementation.headIT.date"
                                type="date"
                                class="medical-input w-full px-3 py-2 bg-white/15 border border-blue-300/30 rounded-lg focus:border-blue-400 focus:outline-none text-white backdrop-blur-sm"
                              />
                            </div>
                          </div>
                        </div>
                      </div>

                      <!-- ICT Officer granting access -->
                      <div
                        class="bg-white/15 rounded-lg p-4 border border-blue-300/30 backdrop-blur-sm"
                      >
                        <h5
                          class="font-bold text-white mb-3 text-center text-sm flex items-center justify-center"
                        >
                          <i class="fas fa-user-shield mr-2"></i>
                          ICT Officer granting access
                        </h5>
                        <div class="space-y-3">
                          <div>
                            <label class="block text-sm font-medium text-blue-100 mb-2"
                              >Name<span class="text-red-400">*</span></label
                            >
                            <div class="relative">
                              <input
                                v-model="form.implementation.ictOfficer.name"
                                type="text"
                                placeholder="Enter name"
                                class="medical-input w-full px-3 py-2 bg-white/15 border border-blue-300/30 rounded-lg focus:border-blue-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm"
                              />
                            </div>
                          </div>
                          <div>
                            <label class="block text-sm font-medium text-blue-100 mb-2"
                              >Signature<span class="text-red-400">*</span></label
                            >
                            <div class="relative">
                              <div
                                v-if="!ictOfficerSignaturePreview"
                                class="w-full px-3 py-2 border-2 border-dashed border-blue-300/40 rounded-lg focus-within:border-blue-400 bg-white/15 backdrop-blur-sm transition-all duration-300 shadow-lg hover:shadow-xl hover:shadow-blue-500/20 min-h-[50px] flex items-center justify-center hover:bg-white/20"
                              >
                                <div class="text-center">
                                  <div class="mb-2">
                                    <i class="fas fa-signature text-blue-300 text-lg mb-1"></i>
                                    <p class="text-blue-100 text-xs">No signature uploaded</p>
                                  </div>
                                  <button
                                    type="button"
                                    @click="triggerIctOfficerSignatureUpload"
                                    class="px-3 py-1.5 bg-gradient-to-r from-blue-500 to-blue-600 text-white text-xs font-semibold rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-300 flex items-center gap-1 mx-auto shadow-lg hover:shadow-xl transform hover:scale-105 border border-blue-400/50"
                                  >
                                    <i class="fas fa-upload"></i>
                                    Upload Signature
                                  </button>
                                </div>
                              </div>

                              <div
                                v-else
                                class="w-full px-3 py-3 border-2 border-purple-300/40 rounded-xl bg-white/15 backdrop-blur-sm transition-all duration-300 shadow-lg hover:shadow-xl hover:shadow-purple-500/20 min-h-[70px] flex items-center justify-center relative"
                              >
                                <div v-if="isImage(ictOfficerSignaturePreview)" class="text-center">
                                  <img
                                    :src="ictOfficerSignaturePreview"
                                    alt="ICT Officer Signature"
                                    class="max-h-[50px] max-w-full object-contain mx-auto mb-1"
                                  />
                                  <p class="text-xs text-blue-100">
                                    {{ ictOfficerSignatureFileName }}
                                  </p>
                                </div>
                                <div v-else class="text-center">
                                  <div
                                    class="w-12 h-12 bg-red-500/20 rounded-xl flex items-center justify-center mx-auto mb-1"
                                  >
                                    <i class="fas fa-file-pdf text-red-400 text-lg"></i>
                                  </div>
                                  <p class="text-xs text-blue-100">
                                    {{ ictOfficerSignatureFileName }}
                                  </p>
                                </div>

                                <div class="absolute top-2 right-2 flex gap-1">
                                  <button
                                    type="button"
                                    @click="triggerIctOfficerSignatureUpload"
                                    class="w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-xs hover:bg-blue-600 transition-colors duration-200 shadow-lg"
                                    title="Change signature"
                                  >
                                    <i class="fas fa-edit"></i>
                                  </button>
                                  <button
                                    type="button"
                                    @click="clearIctOfficerSignature"
                                    class="w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center text-xs hover:bg-red-600 transition-colors duration-200 shadow-lg"
                                    title="Remove signature"
                                  >
                                    <i class="fas fa-times"></i>
                                  </button>
                                </div>
                              </div>

                              <input
                                ref="ictOfficerSignatureInput"
                                type="file"
                                accept="image/png,image/jpeg,application/pdf"
                                @change="onIctOfficerSignatureChange"
                                class="hidden"
                              />
                            </div>
                          </div>
                          <div>
                            <label class="block text-sm font-medium text-blue-100 mb-2"
                              >Date (mm/dd/yyyy)<span class="text-red-400">*</span></label
                            >
                            <div class="relative">
                              <input
                                v-model="form.implementation.ictOfficer.date"
                                type="date"
                                class="medical-input w-full px-3 py-2 bg-white/15 border border-blue-300/30 rounded-lg focus:border-blue-400 focus:outline-none text-white backdrop-blur-sm"
                              />
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Review Mode Actions -->
                  <div
                    v-if="isReviewMode"
                    class="medical-card bg-gradient-to-r from-emerald-600/25 to-green-600/25 border-2 border-emerald-400/40 p-8 rounded-2xl backdrop-blur-sm hover:shadow-2xl hover:shadow-emerald-500/20 transition-all duration-500 group"
                  >
                    <div class="flex items-center space-x-4 mb-6">
                      <div
                        class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-emerald-300/50"
                      >
                        <i class="fas fa-gavel text-white text-xl"></i>
                      </div>
                      <h3 class="text-xl font-bold text-white flex items-center">
                        <i class="fas fa-check-double mr-2 text-emerald-300"></i>
                        Review Actions
                      </h3>
                    </div>

                    <!-- Request Status -->
                    <div class="mb-6 p-4 bg-white/10 rounded-xl border border-emerald-300/30">
                      <h4 class="text-sm font-bold text-blue-100 mb-3">Request Status</h4>
                      <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                        <div class="text-center">
                          <div
                            :class="
                              getApprovalStatus('hod') === 'approved'
                                ? 'bg-green-500'
                                : getApprovalStatus('hod') === 'rejected'
                                  ? 'bg-red-500'
                                  : 'bg-yellow-500'
                            "
                            class="w-8 h-8 rounded-full flex items-center justify-center mx-auto mb-1"
                          >
                            <i
                              :class="
                                getApprovalStatus('hod') === 'approved'
                                  ? 'fas fa-check'
                                  : getApprovalStatus('hod') === 'rejected'
                                    ? 'fas fa-times'
                                    : 'fas fa-clock'
                              "
                              class="text-white text-sm"
                            ></i>
                          </div>
                          <p class="text-xs text-white">HOD</p>
                        </div>
                        <div class="text-center">
                          <div
                            :class="
                              getApprovalStatus('divisional') === 'approved'
                                ? 'bg-green-500'
                                : getApprovalStatus('divisional') === 'rejected'
                                  ? 'bg-red-500'
                                  : 'bg-yellow-500'
                            "
                            class="w-8 h-8 rounded-full flex items-center justify-center mx-auto mb-1"
                          >
                            <i
                              :class="
                                getApprovalStatus('divisional') === 'approved'
                                  ? 'fas fa-check'
                                  : getApprovalStatus('divisional') === 'rejected'
                                    ? 'fas fa-times'
                                    : 'fas fa-clock'
                              "
                              class="text-white text-sm"
                            ></i>
                          </div>
                          <p class="text-xs text-white">Divisional</p>
                        </div>
                        <div class="text-center">
                          <div
                            :class="
                              getApprovalStatus('dict') === 'approved'
                                ? 'bg-green-500'
                                : getApprovalStatus('dict') === 'rejected'
                                  ? 'bg-red-500'
                                  : 'bg-yellow-500'
                            "
                            class="w-8 h-8 rounded-full flex items-center justify-center mx-auto mb-1"
                          >
                            <i
                              :class="
                                getApprovalStatus('dict') === 'approved'
                                  ? 'fas fa-check'
                                  : getApprovalStatus('dict') === 'rejected'
                                    ? 'fas fa-times'
                                    : 'fas fa-clock'
                              "
                              class="text-white text-sm"
                            ></i>
                          </div>
                          <p class="text-xs text-white">DICT</p>
                        </div>
                        <div class="text-center">
                          <div
                            :class="
                              getApprovalStatus('headOfIt') === 'approved'
                                ? 'bg-green-500'
                                : getApprovalStatus('headOfIt') === 'rejected'
                                  ? 'bg-red-500'
                                  : 'bg-yellow-500'
                            "
                            class="w-8 h-8 rounded-full flex items-center justify-center mx-auto mb-1"
                          >
                            <i
                              :class="
                                getApprovalStatus('headOfIt') === 'approved'
                                  ? 'fas fa-check'
                                  : getApprovalStatus('headOfIt') === 'rejected'
                                    ? 'fas fa-times'
                                    : 'fas fa-clock'
                              "
                              class="text-white text-sm"
                            ></i>
                          </div>
                          <p class="text-xs text-white">Head IT</p>
                        </div>
                        <div class="text-center">
                          <div
                            :class="
                              getApprovalStatus('ict') === 'approved'
                                ? 'bg-green-500'
                                : getApprovalStatus('ict') === 'rejected'
                                  ? 'bg-red-500'
                                  : 'bg-yellow-500'
                            "
                            class="w-8 h-8 rounded-full flex items-center justify-center mx-auto mb-1"
                          >
                            <i
                              :class="
                                getApprovalStatus('ict') === 'approved'
                                  ? 'fas fa-check'
                                  : getApprovalStatus('ict') === 'rejected'
                                    ? 'fas fa-times'
                                    : 'fas fa-clock'
                              "
                              class="text-white text-sm"
                            ></i>
                          </div>
                          <p class="text-xs text-white">ICT Officer</p>
                        </div>
                      </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                      <button
                        type="button"
                        @click="goBackToRequests"
                        class="px-6 py-3 bg-gradient-to-r from-gray-600 to-gray-700 text-white rounded-lg hover:from-gray-700 hover:to-gray-800 transition-all duration-300 font-semibold flex items-center justify-center shadow-lg hover:shadow-xl transform hover:scale-105"
                      >
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Requests
                      </button>

                      <button
                        v-if="canApproveAtStage()"
                        type="button"
                        @click="approveRequest"
                        class="px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-lg hover:from-green-700 hover:to-emerald-700 transition-all duration-300 font-semibold flex items-center justify-center shadow-lg hover:shadow-xl transform hover:scale-105"
                      >
                        <i class="fas fa-check mr-2"></i>
                        Approve Request
                      </button>

                      <button
                        v-if="canApproveAtStage()"
                        type="button"
                        @click="rejectRequest"
                        class="px-6 py-3 bg-gradient-to-r from-red-600 to-pink-600 text-white rounded-lg hover:from-red-700 hover:to-pink-700 transition-all duration-300 font-semibold flex items-center justify-center shadow-lg hover:shadow-xl transform hover:scale-105"
                      >
                        <i class="fas fa-times mr-2"></i>
                        Reject Request
                      </button>
                    </div>
                  </div>

                  <!-- Footer & Submit (Normal Mode) -->
                  <div v-if="!isReviewMode" class="border-t-2 border-gray-200 pt-3">
                    <div class="flex flex-col sm:flex-row justify-between gap-3">
                      <button
                        type="button"
                        @click="onReset"
                        class="px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-all duration-300 font-semibold flex items-center justify-center shadow-lg hover:shadow-xl"
                      >
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                          <path
                            fill-rule="evenodd"
                            d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z"
                            clip-rule="evenodd"
                          />
                        </svg>
                        Reset Form
                      </button>
                      <button
                        type="submit"
                        class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-300 font-semibold flex items-center justify-center shadow-lg hover:shadow-xl"
                      >
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                          <path
                            fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd"
                          />
                        </svg>
                        Submit Request
                      </button>
                    </div>
                  </div>
                </section>

                <!-- Right: tabs -->
                <section aria-labelledby="module-tabs" class="lg:col-span-1 space-y-4">
                  <h2 id="module-tabs" class="sr-only">Module Details</h2>

                  <!-- Desktop tabs -->
                  <div class="hidden md:block">
                    <div class="flex items-center gap-2 overflow-x-auto pb-1">
                      <button
                        v-for="t in tabs"
                        :key="t.key"
                        class="tab"
                        :class="t.key === activeTab ? 'tab-active' : ''"
                        @click="activeTab = t.key"
                        :aria-selected="t.key === activeTab"
                        role="tab"
                      >
                        {{ t.label }}
                        <i
                          class="fas fa-times ml-2 text-xs opacity-70 hover:opacity-100"
                          @click.stop="tryCloseTab(t.key)"
                        ></i>
                      </button>
                    </div>
                    <div class="mt-2">
                      <transition name="fade" mode="out-in">
                        <div v-if="currentTab" :key="activeTab" class="card">
                          <component
                            :is="currentTab.component"
                            v-model="moduleData[currentTab.key]"
                          />
                        </div>
                      </transition>
                    </div>
                  </div>

                  <!-- Mobile accordion -->
                  <div class="md:hidden space-y-2">
                    <div v-for="t in tabs" :key="t.key" class="card">
                      <button
                        class="w-full flex items-center justify-between text-left"
                        @click="toggleAccordion(t.key)"
                      >
                        <span class="font-semibold">{{ t.label }}</span>
                        <i
                          :class="[
                            'fas',
                            openAccordions.has(t.key) ? 'fa-chevron-up' : 'fa-chevron-down'
                          ]"
                        ></i>
                      </button>
                      <transition name="fade">
                        <div v-show="openAccordions.has(t.key)" class="mt-3">
                          <component :is="t.component" v-model="moduleData[t.key]" />
                          <div class="mt-3 text-right">
                            <button class="btn-secondary btn-sm" @click="tryCloseTab(t.key)">
                              <i class="fas fa-times mr-1"></i> Remove
                            </button>
                          </div>
                        </div>
                      </transition>
                    </div>
                  </div>
                </section>
              </div>
            </form>
          </div>
          
          <!-- Footer moved to bottom of main content -->
          <div class="mt-6">
            <AppFooter />
          </div>
        </div>
      </main>
    </div>

    <!-- Close Tab Confirmation Modal -->
    <transition name="fade">
      <div
        v-if="confirm.key"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
      >
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-4">
          <h3 class="text-lg font-semibold text-gray-800">Remove {{ confirm.label }}?</h3>
          <p class="text-sm text-gray-600 mt-1">
            You have unsaved data in this module. Are you sure you want to close it?
          </p>
          <div class="mt-4 flex justify-end gap-2">
            <button class="btn-secondary" @click="confirm = { key: '', label: '' }">Cancel</button>
            <button class="btn-danger" @click="closeTab(confirm.key)">Remove</button>
          </div>
        </div>
      </div>
    </transition>

    <!-- Success Toast -->
    <transition name="fade">
      <div
        v-if="toast.show"
        class="fixed bottom-4 right-4 bg-green-600 text-white rounded-lg shadow-lg px-4 py-3 text-sm"
      >
        {{ toast.message }}
      </div>
    </transition>
  </div>
</template>

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
    border-color: rgba(45, 212, 191, 0.8);
    box-shadow: 0 0 0 3px rgba(45, 212, 191, 0.2);
  }

  /* Animations */
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

  .animate-fade-in {
    animation: fade-in 1s ease-out;
  }

  .animate-fade-in-delay {
    animation: fade-in-delay 1s ease-out 0.3s both;
  }

  /* Review mode styles */
  .review-mode input[readonly],
  .review-mode select[disabled],
  .review-mode textarea[readonly] {
    background-color: rgba(59, 130, 246, 0.1) !important;
    border-color: rgba(96, 165, 250, 0.2) !important;
    cursor: not-allowed;
  }

  .review-mode input[type='checkbox'][disabled],
  .review-mode input[type='radio'][disabled] {
    opacity: 0.7;
    cursor: not-allowed;
  }
</style>

<script>
  // Wellsoft panel (key fields)
  const WellsoftPanel = {
    props: ['modelValue'],
    emits: ['update:modelValue'],
    template: `<div class="space-y-3">
    <div>
      <label class="label">Action Requested<span class="text-red-500">*</span></label>
      <div class="flex gap-3">
        <label class="inline-flex items-center gap-2 text-sm"><input type="radio" value="use" :checked="modelValue?.requestType==='use'" @change="$emit('update:modelValue', { ...modelValue, requestType: 'use' })"/> Use</label>
        <label class="inline-flex items-center gap-2 text-sm"><input type="radio" value="revoke" :checked="modelValue?.requestType==='revoke'" @change="$emit('update:modelValue', { ...modelValue, requestType: 'revoke' })"/> Revoke</label>
      </div>
    </div>
    <div>
      <label class="label">Access Rights<span class="text-red-500">*</span></label>
      <div class="flex flex-col sm:flex-row gap-2">
        <label class="inline-flex items-center gap-2 text-sm"><input type="radio" value="permanent" :checked="modelValue?.accessType==='permanent'" @change="$emit('update:modelValue', { ...modelValue, accessType: 'permanent' })"/> Permanent</label>
        <label class="inline-flex items-center gap-2 text-sm"><input type="radio" value="temporary" :checked="modelValue?.accessType==='temporary'" @change="$emit('update:modelValue', { ...modelValue, accessType: 'temporary' })"/> Temporary</label>
        <div v-if="modelValue?.accessType==='temporary'" class="flex items-center gap-1">
          <input placeholder="DD" maxlength="2" class="w-10 input" :value="modelValue?.tempDay||''" @input="$emit('update:modelValue', { ...modelValue, tempDay: $event.target.value })"/>
          <span class="text-gray-400">/</span>
          <input placeholder="MM" maxlength="2" class="w-10 input" :value="modelValue?.tempMonth||''" @input="$emit('update:modelValue', { ...modelValue, tempMonth: $event.target.value })"/>
          <span class="text-gray-400">/</span>
          <input placeholder="YYYY" maxlength="4" class="w-16 input" :value="modelValue?.tempYear||''" @input="$emit('update:modelValue', { ...modelValue, tempYear: $event.target.value })"/>
        </div>
      </div>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
      <div>
        <label class="label">HoD/BM Name<span class="text-red-500">*</span></label>
        <input class="input" :value="modelValue?.approvals?.hodName||''" @input="$emit('update:modelValue', { ...modelValue, approvals: { ...(modelValue?.approvals||{}), hodName: $event.target.value } })"/>
      </div>
      <div>
        <label class="label">Signature<span class="text-red-500">*</span></label>
        <input class="input" :value="modelValue?.approvals?.hodSignature||''" @input="$emit('update:modelValue', { ...modelValue, approvals: { ...(modelValue?.approvals||{}), hodSignature: $event.target.value } })"/>
      </div>
      <div>
        <label class="label">Date<span class="text-red-500">*</span></label>
        <input type="date" class="input" :value="modelValue?.approvals?.hodDate||''" @input="$emit('update:modelValue', { ...modelValue, approvals: { ...(modelValue?.approvals||{}), hodDate: $event.target.value } })"/>
      </div>
    </div>
    <div>
      <label class="label">Comments<span class="text-red-500">*</span></label>
<textarea class="input" rows="3" :value="modelValue?.notes || ''" @input="$emit('update:modelValue', { ...modelValue, notes: $event.target.value })"></textarea>
    </div>
  </div>`
  }

  // Jeeva panel (attractive UI mirroring Jeeva form)
  const JeevaPanel = {
    props: ['modelValue'],
    emits: ['update:modelValue'],
    data() {
      return {
        open: false,
        focusIndex: 0,
        options: ['Use', 'Revoke', 'Access Rights', 'Approval', 'Comments', 'For Implementation']
      }
    },
    methods: {
      isSelected(label) {
        const sel = this.modelValue?.selections || []
        return sel.includes(label)
      },
      toggle(label) {
        // enforce mutual exclusivity between Use and Revoke
        let sel = [...(this.modelValue?.selections || [])]
        if (label === 'Use') sel = sel.filter((v) => v !== 'Revoke')
        if (label === 'Revoke') sel = sel.filter((v) => v !== 'Use')
        if (sel.includes(label)) sel = sel.filter((v) => v !== label)
        else sel.push(label)
        this.$emit('update:modelValue', {
          ...(this.modelValue || {}),
          selections: sel
        })
      },
      close() {
        this.open = false
      },
      onKeydown(e) {
        if (!this.open && (e.key === 'Enter' || e.key === ' ')) {
          e.preventDefault()
          this.open = true
          return
        }
        if (!this.open) return
        if (e.key === 'Escape') {
          e.preventDefault()
          this.open = false
          return
        }
        const max = this.options.length - 1
        if (e.key === 'ArrowDown') {
          e.preventDefault()
          this.focusIndex = Math.min(max, this.focusIndex + 1)
        }
        if (e.key === 'ArrowUp') {
          e.preventDefault()
          this.focusIndex = Math.max(0, this.focusIndex - 1)
        }
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault()
          this.toggle(this.options[this.focusIndex])
        }
      }
    },
    template: `<div class="space-y-4">
    <!-- Multi-select dropdown trigger with chips -->
    <div>
      <label class="label m-0" id="jeeva-modules-label">Module Requested for <span class="text-red-500">*</span></label>
      <div class="relative" @keydown.prevent.stop="onKeydown" tabindex="0" aria-labelledby="jeeva-modules-label">
        <div class="input flex items-center flex-wrap gap-1 cursor-pointer" @click="open = !open" :aria-expanded="open.toString()" role="combobox" aria-controls="jeeva-options" aria-multiselectable="true">
          <template v-if="(modelValue?.selections||[]).length">
            <span v-for="s in modelValue.selections" :key="'chip-'+s" class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-indigo-100 text-indigo-800 text-xs">
              {{ s }} <i class="fas fa-times opacity-70" @click.stop="toggle(s)"></i>
            </span>
          </template>
          <span v-else class="text-gray-400 text-sm">Select items…</span>
          <i class="fas fa-chevron-down ml-auto text-gray-400"></i>
        </div>
        <div v-show="open" class="absolute z-20 mt-1 w-full bg-white border border-gray-200 rounded-md shadow-lg p-2" id="jeeva-options" role="listbox" :aria-activedescendant="'opt-'+focusIndex">
          <button
            v-for="(opt,idx) in options"
            :key="'opt-'+opt"
            :id="'opt-'+idx"
            class="option-tile w-full justify-start"
            :class="isSelected(opt) ? 'option-tile-active' : ''"
            role="option"
            :aria-selected="isSelected(opt) ? 'true' : 'false'"
            @click.stop="toggle(opt)"
            @mouseenter="focusIndex = idx"
          >
            <i :class="['fas', isSelected(opt) ? 'fa-check-circle text-indigo-700' : 'fa-circle text-gray-300']"></i>
            <span class="truncate">{{ opt }}</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Conditional sections based on selections -->
    <div v-if="isSelected('Use') || isSelected('Revoke')">
      <label class="label">Action Requested<span class="text-red-500">*</span></label>
      <div class="flex gap-3 mt-1">
        <label class="inline-flex items-center gap-2 text-sm px-2 py-1 rounded-md border border-gray-200 hover:bg-indigo-50">
          <input type="radio" value="use" :checked="modelValue?.requestType==='use'" @change="$emit('update:modelValue', { ...modelValue, requestType: 'use' })"/> Use
        </label>
        <label class="inline-flex items-center gap-2 text-sm px-2 py-1 rounded-md border border-gray-200 hover:bg-indigo-50">
          <input type="radio" value="revoke" :checked="modelValue?.requestType==='revoke'" @change="$emit('update:modelValue', { ...modelValue, requestType: 'revoke' })"/> Revoke
        </label>
      </div>
    </div>

    <div v-if="isSelected('Access Rights')">
      <label class="label">Access Rights <span class="text-red-500">*</span></label>
      <div class="flex flex-col sm:flex-row gap-2">
        <label class="inline-flex items-center gap-2 text-sm px-2 py-1 rounded-md border border-gray-200 hover:bg-purple-50">
          <input type="radio" value="permanent" :checked="modelValue?.accessType==='permanent'" @change="$emit('update:modelValue', { ...modelValue, accessType: 'permanent' })"/> Permanent
        </label>
        <label class="inline-flex items-center gap-2 text-sm px-2 py-1 rounded-md border border-gray-200 hover:bg-purple-50">
          <input type="radio" value="temporary" :checked="modelValue?.accessType==='temporary'" @change="$emit('update:modelValue', { ...modelValue, accessType: 'temporary' })"/> Temporary
        </label>
        <div v-if="modelValue?.accessType==='temporary'" class="flex items-center gap-1">
          <input placeholder="DD" maxlength="2" class="w-10 input" :value="modelValue?.tempDay||''" @input="$emit('update:modelValue', { ...modelValue, tempDay: $event.target.value })"/>
          <span class="text-gray-400">/</span>
          <input placeholder="MM" maxlength="2" class="w-10 input" :value="modelValue?.tempMonth||''" @input="$emit('update:modelValue', { ...modelValue, tempMonth: $event.target.value })"/>
          <span class="text-gray-400">/</span>
          <input placeholder="YYYY" maxlength="4" class="w-16 input" :value="modelValue?.tempYear||''" @input="$emit('update:modelValue', { ...modelValue, tempYear: $event.target.value })"/>
        </div>
      </div>
    </div>

    <div v-if="isSelected('Approval')" class="space-y-4">
      <label class="label">Approval</label>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
        <div class="bg-indigo-50 rounded-lg p-3">
          <h5 class="font-semibold text-indigo-800 text-xs mb-2 text-center">HoD / BM</h5>
          <input class="input mb-2" placeholder="Name*" :value="modelValue?.approvals?.hod?.name||''" @input="$emit('update:modelValue', { ...modelValue, approvals: { ...(modelValue?.approvals||{}), hod: { ...(modelValue?.approvals?.hod||{}), name: $event.target.value } } })"/>
          <input class="input mb-2" placeholder="Signature*" :value="modelValue?.approvals?.hod?.signature||''" @input="$emit('update:modelValue', { ...modelValue, approvals: { ...(modelValue?.approvals||{}), hod: { ...(modelValue?.approvals?.hod||{}), signature: $event.target.value } } })"/>
          <input type="date" class="input" placeholder="Date*" :value="modelValue?.approvals?.hod?.date||''" @input="$emit('update:modelValue', { ...modelValue, approvals: { ...(modelValue?.approvals||{}), hod: { ...(modelValue?.approvals?.hod||{}), date: $event.target.value } } })"/>
        </div>
        <div class="bg-indigo-50 rounded-lg p-3">
          <h5 class="font-semibold text-indigo-800 text-xs mb-2 text-center">Divisional Director</h5>
          <input class="input mb-2" placeholder="Name*" :value="modelValue?.approvals?.divisionalDirector?.name||''" @input="$emit('update:modelValue', { ...modelValue, approvals: { ...(modelValue?.approvals||{}), divisionalDirector: { ...(modelValue?.approvals?.divisionalDirector||{}), name: $event.target.value } } })"/>
          <input class="input mb-2" placeholder="Signature*" :value="modelValue?.approvals?.divisionalDirector?.signature||''" @input="$emit('update:modelValue', { ...modelValue, approvals: { ...(modelValue?.approvals||{}), divisionalDirector: { ...(modelValue?.approvals?.divisionalDirector||{}), signature: $event.target.value } } })"/>
          <input type="date" class="input" placeholder="Date*" :value="modelValue?.approvals?.divisionalDirector?.date||''" @input="$emit('update:modelValue', { ...modelValue, approvals: { ...(modelValue?.approvals||{}), divisionalDirector: { ...(modelValue?.approvals?.divisionalDirector||{}), date: $event.target.value } } })"/>
        </div>
        <div class="bg-indigo-50 rounded-lg p-3">
          <h5 class="font-semibold text-indigo-800 text-xs mb-2 text-center">Director of ICT</h5>
          <input class="input mb-2" placeholder="Name*" :value="modelValue?.approvals?.directorICT?.name||''" @input="$emit('update:modelValue', { ...modelValue, approvals: { ...(modelValue?.approvals||{}), directorICT: { ...(modelValue?.approvals?.directorICT||{}), name: $event.target.value } } })"/>
          <input class="input mb-2" placeholder="Signature*" :value="modelValue?.approvals?.directorICT?.signature||''" @input="$emit('update:modelValue', { ...modelValue, approvals: { ...(modelValue?.approvals||{}), directorICT: { ...(modelValue?.approvals?.directorICT||{}), signature: $event.target.value } } })"/>
          <input type="date" class="input" placeholder="Date*" :value="modelValue?.approvals?.directorICT?.date||''" @input="$emit('update:modelValue', { ...modelValue, approvals: { ...(modelValue?.approvals||{}), directorICT: { ...(modelValue?.approvals?.directorICT||{}), date: $event.target.value } } })"/>
        </div>
      </div>
    </div>

    <div v-if="isSelected('Comments')">
      <label class="label">Comments<span class="text-red-500">*</span></label>
      <textarea rows="3" class="input" placeholder="Comments*" :value="modelValue?.comments||''" @input="$emit('update:modelValue', { ...modelValue, comments: $event.target.value })"></textarea>
    </div>

    <div v-if="isSelected('For Implementation')" class="space-y-3">
      <label class="label">For Implementation</label>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div class="bg-emerald-50 rounded-lg p-3">
          <h5 class="font-semibold text-emerald-800 text-xs mb-2 text-center">Head of IT</h5>
          <input class="input mb-2" placeholder="Name*" :value="modelValue?.implementation?.headIT?.name||''" @input="$emit('update:modelValue', { ...modelValue, implementation: { ...(modelValue?.implementation||{}), headIT: { ...(modelValue?.implementation?.headIT||{}), name: $event.target.value } } })"/>
          <input class="input mb-2" placeholder="Signature*" :value="modelValue?.implementation?.headIT?.signature||''" @input="$emit('update:modelValue', { ...modelValue, implementation: { ...(modelValue?.implementation||{}), headIT: { ...(modelValue?.implementation?.headIT||{}), signature: $event.target.value } } })"/>
          <input type="date" class="input" placeholder="Date*" :value="modelValue?.implementation?.headIT?.date||''" @input="$emit('update:modelValue', { ...modelValue, implementation: { ...(modelValue?.implementation||{}), headIT: { ...(modelValue?.implementation?.headIT||{}), date: $event.target.value } } })"/>
        </div>
        <div class="bg-emerald-50 rounded-lg p-3">
          <h5 class="font-semibold text-emerald-800 text-xs mb-2 text-center">ICT Officer granting access</h5>
          <input class="input mb-2" placeholder="Name*" :value="modelValue?.implementation?.ictOfficer?.name||''" @input="$emit('update:modelValue', { ...modelValue, implementation: { ...(modelValue?.implementation||{}), ictOfficer: { ...(modelValue?.implementation?.ictOfficer||{}), name: $event.target.value } } })"/>
          <input class="input mb-2" placeholder="Signature*" :value="modelValue?.implementation?.ictOfficer?.signature||''" @input="$emit('update:modelValue', { ...modelValue, implementation: { ...(modelValue?.implementation||{}), ictOfficer: { ...(modelValue?.implementation?.ictOfficer||{}), signature: $event.target.value } } })"/>
          <input type="date" class="input" placeholder="Date*" :value="modelValue?.implementation?.ictOfficer?.date||''" @input="$emit('update:modelValue', { ...modelValue, implementation: { ...(modelValue?.implementation||{}), ictOfficer: { ...(modelValue?.implementation?.ictOfficer||{}), date: $event.target.value } } })"/>
        </div>
      </div>
    </div>
  </div>`
  }

  // Internet panel (key fields)
  const InternetPanel = {
    props: ['modelValue'],
    emits: ['update:modelValue'],
    template: `<div class="space-y-3">
    <div>
      <label class="label">Approval - HoD Certification<span class="text-red-500">*</span></label>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
        <input class="input" placeholder="HOD Name*" :value="modelValue?.hodName||''" @input="$emit('update:modelValue', { ...modelValue, hodName: $event.target.value })"/>
        <input class="input" placeholder="Signature*" :value="modelValue?.hodSignature||''" @input="$emit('update:modelValue', { ...modelValue, hodSignature: $event.target.value })"/>
        <input type="date" class="input" placeholder="Date*" :value="modelValue?.hodDate||''" @input="$emit('update:modelValue', { ...modelValue, hodDate: $event.target.value })"/>
      </div>
    </div>
    <div>
      <label class="label">Director's Decision<span class="text-red-500">*</span></label>
      <div class="flex gap-3">
        <label class="inline-flex items-center gap-2 text-sm"><input type="radio" value="approve" :checked="modelValue?.directorDecision==='approve'" @change="$emit('update:modelValue', { ...modelValue, directorDecision: 'approve' })"/> Approve</label>
        <label class="inline-flex items-center gap-2 text-sm"><input type="radio" value="disapprove" :checked="modelValue?.directorDecision==='disapprove'" @change="$emit('update:modelValue', { ...modelValue, directorDecision: 'disapprove' })"/> Disapprove</label>
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 mt-2">
        <input class="input" placeholder="Director Name*" :value="modelValue?.directorName||''" @input="$emit('update:modelValue', { ...modelValue, directorName: $event.target.value })"/>
        <input class="input" placeholder="Signature*" :value="modelValue?.directorSignature||''" @input="$emit('update:modelValue', { ...modelValue, directorSignature: $event.target.value })"/>
        <input type="date" class="input" placeholder="Date*" :value="modelValue?.directorDate||''" @input="$emit('update:modelValue', { ...modelValue, directorDate: $event.target.value })"/>
      </div>
    </div>
    <div>
      <label class="label">IT Department<span class="text-red-500">*</span></label>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <input class="input" placeholder="Person Name*" :value="modelValue?.personName||''" @input="$emit('update:modelValue', { ...modelValue, personName: $event.target.value })"/>
        <input class="input" placeholder="Access Level*" :value="modelValue?.accessLevel||''" @input="$emit('update:modelValue', { ...modelValue, accessLevel: $event.target.value })"/>
        <input class="input" placeholder="IT Officer Signature*" :value="modelValue?.itSignature||''" @input="$emit('update:modelValue', { ...modelValue, itSignature: $event.target.value })"/>
        <input type="date" class="input" placeholder="Date*" :value="modelValue?.itDate||''" @input="$emit('update:modelValue', { ...modelValue, itDate: $event.target.value })"/>
      </div>
      <div class="mt-2">
        <textarea rows="2" class="input" placeholder="DICT comments*" :value="modelValue?.itComments||''" @input="$emit('update:modelValue', { ...modelValue, itComments: $event.target.value })"></textarea>
      </div>
    </div>
    <div>
      <label class="label">Executive Director Final<span class="text-red-500">*</span></label>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <input class="input" placeholder="Signature*" :value="modelValue?.execSignature||''" @input="$emit('update:modelValue', { ...modelValue, execSignature: $event.target.value })"/>
        <input type="date" class="input" placeholder="Date*" :value="modelValue?.execDate||''" @input="$emit('update:modelValue', { ...modelValue, execDate: $event.target.value })"/>
      </div>
    </div>
  </div>
  `
  }

  // import { ref } from 'vue' // Removed unused import
  import Header from '@/components/header.vue'
  import ModernSidebar from '@/components/ModernSidebar.vue'
  import AppFooter from '@/components/footer.vue'
  import combinedAccessService from '@/services/combinedAccessService.js'

  export default {
    name: 'BothServiveForm',
    components: {
      WellsoftPanel,
      JeevaPanel,
      InternetPanel,
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
        // Signature handling
        signaturePreview: '',
        signatureFileName: '',
        // Approval signatures
        hodSignaturePreview: '',
        hodSignatureFileName: '',
        divDirectorSignaturePreview: '',
        divDirectorSignatureFileName: '',
        directorICTSignaturePreview: '',
        directorICTSignatureFileName: '',
        // Implementation signatures
        headITSignaturePreview: '',
        headITSignatureFileName: '',
        ictOfficerSignaturePreview: '',
        ictOfficerSignatureFileName: '',
        // Jeeva Requested for selector state
        jeevaItemOpen: false,
        jeevaItemFocusIndex: 0,
        jeevaItemOptions: [
          'Use',
          'Revoke',
          'Access Rights',
          'Approval',
          'Comments',
          'For Implementation'
        ],
        jeevaItemSelections: [],
        // From Wellsoft form
        wellsoftModules: [
          'Registrar',
          'Specialist',
          'Cashier',
          'Resident Nurse',
          'Intern Doctor',
          'Intern Nurse',
          'Medical Recorder',
          'Social Worker',
          'Quality Officer',
          'Administrator',
          'Health Attendant'
        ],
        // From Jeeva form (names extracted)
        jeevaModules: [
          'FINANCIAL ACCOUNTING',
          'DOCTOR CONSULTATION',
          'MEDICAL RECORDS',
          'OUTPATIENT',
          'NURSING STATION',
          'INPATIENT',
          'IP CASHIER',
          'HIV',
          'LINEN & LAUNDRY',
          'FIXED ASSETS',
          'PMTCT',
          'PHARMACY',
          'BILL NOTE',
          'BLOOD BANK',
          'ORDER MANAGEMENT',
          'PRIVATE CREDIT',
          'LABORATORY',
          'GENERAL STORE',
          'IP BILLING',
          'RADIOLOGY',
          'PURCHASE',
          'SCROLLING',
          'OPERATION THEATRE',
          'CSSD',
          'WEB INDENT',
          'MORTUARY',
          'GENERAL MAINTENANCE',
          'PERSONNEL',
          'MAINTENANCE',
          'PAYROLL',
          'CMS',
          'MIS STATISTICS'
        ],
        // Suggested Internet purposes
        // internetPurposes: ['Research','Training','Remote Work','Telemedicine','Email/Communication'], // Removed duplicate - using the one in form data

        form: {
          shared: { pfNumber: '', staffName: '', department: '', phone: '' },
          accessRights: {
            type: '',
            tempDate: {
              month: '',
              day: '',
              year: ''
            }
          },
          approvals: {
            hod: {
              name: '',
              signature: '',
              date: ''
            },
            divisionalDirector: {
              name: '',
              signature: '',
              date: ''
            },
            directorICT: {
              name: '',
              signature: '',
              date: ''
            }
          },
          comments: '',
          implementation: {
            headIT: {
              name: '',
              signature: '',
              date: ''
            },
            ictOfficer: {
              name: '',
              signature: '',
              date: ''
            }
          }
        },

        selectedWellsoft: [],
        selectedJeeva: [],
        wellsoftRequestType: 'use',
        // search queries
        wellsoftQuery: '',
        jeevaQuery: '',
        internetPurposes: ['', '', '', ''],

        tabs: [],
        activeTab: '',
        openAccordions: new Set(),
        moduleData: {},

        confirm: { key: '', label: '' },
        toast: { show: false, message: '' },
        errors: { pfNumber: '', staffName: '' },
        // Review mode data
        requestData: null,
        loading: false,
        error: null
      }
    },
    computed: {
      // Review mode check
      isReviewMode() {
        return this.$route.params.id != null || this.$route.query.id != null
      },

      // Request ID from route or query parameters
      requestId() {
        return this.$route.params.id || this.$route.query.id || null
      },

      currentTab() {
        return this.tabs.find((t) => t.key === this.activeTab) || null
      },
      filteredWellsoft() {
        const q = (this.wellsoftQuery || '').toLowerCase()
        return !q
          ? this.wellsoftModules
          : this.wellsoftModules.filter((m) => m.toLowerCase().includes(q))
      },
      filteredJeeva() {
        const q = (this.jeevaQuery || '').toLowerCase()
        return !q ? this.jeevaModules : this.jeevaModules.filter((m) => m.toLowerCase().includes(q))
      },

      summaryErrors() {
        const list = []
        // Defensive guards for shared
        const shared = this.form?.shared || {}
        if (!shared.pfNumber) list.push('PF Number is required.')
        if (!shared.staffName) list.push('Staff Name is required.')

        // Defensive guards for inline Jeeva block
        const ji = this.jeevaInline || {}
        if (!ji.accessType) list.push('Jeeva Access Rights: Please select Permanent or Temporary.')
        if (ji.accessType === 'temporary') {
          if (!ji.tempMonth || !ji.tempDay || !ji.tempYear) {
            list.push(
              'Jeeva Access Rights: Please provide complete Temporary Until date (MM/DD/YYYY).'
            )
          }
        }

        // Tabs validation (guard against undefined tabs/moduleData)
        const tabs = Array.isArray(this.tabs) ? this.tabs : []
        const data = this.moduleData || {}
        tabs.forEach((t) => {
          if (!t || !t.key) return
          const d = data[t.key] || null
          if (t.type === 'wellsoft' || t.type === 'jeeva') {
            const access = d && typeof d === 'object' ? d.accessType : undefined
            if (!access) list.push(`${t.label || 'Module'}: Access Type is required.`)
          }
          if (t.type === 'internet' && !this.internetPurposes[0].trim()) {
            list.push('Internet Purpose is required.')
          }
        })
        return list
      },

      // Check if signature exists in the database
      hasSignature() {
        const hasData = !!this.requestData
        const hasPath = this.requestData && !!this.requestData.signature_path
        const pathNotEmpty = hasPath && this.requestData.signature_path.trim() !== ''
        
        console.log('hasSignature debug:', {
          hasData,
          hasPath, 
          signaturePath: this.requestData?.signature_path,
          pathNotEmpty,
          result: hasData && hasPath && pathNotEmpty
        })
        
        return hasData && hasPath && pathNotEmpty
      },

      // Get request types from loaded data
      requestTypes() {
        if (!this.requestData) return []
        
        // Handle both array and object formats
        let types = this.requestData.request_types || this.requestData.request_type || []
        if (!Array.isArray(types)) {
          types = [types]
        }
        
        console.log('Request types:', types)
        return types
      },

      // Check if specific request type is included
      hasWellsoftRequest() {
        return this.requestTypes.includes('wellsoft')
      },

      hasJeevaRequest() {
        return this.requestTypes.includes('jeeva_access') || this.requestTypes.includes('jeeva')
      },

      hasInternetRequest() {
        return this.requestTypes.includes('internet_access_request') || this.requestTypes.includes('internet')
      },

      // Determine if sections should be readonly based on review mode and request type
      isWellsoftReadonly() {
        return this.isReviewMode && !this.hasWellsoftRequest
      },

      isJeevaReadonly() {
        return this.isReviewMode && !this.hasJeevaRequest
      },

      isInternetReadonly() {
        return this.isReviewMode && !this.hasInternetRequest
      }
    },
    async mounted() {
      if (this.isReviewMode && this.requestId) {
        await this.loadRequestData()
      }
    },
    watch: {
      selectedWellsoft: {
        handler(v) {
          this.syncTabs('wellsoft', v)
        },
        deep: true
      },
      selectedJeeva: {
        handler(v) {
          this.syncTabs('jeeva', v)
        },
        deep: true
      }
    },
    methods: {
      // Jeeva Requested for selector handlers
      isJeevaItemSelected(label) {
        return this.jeevaItemSelections.includes(label)
      },
      toggleJeevaItem(label) {
        let sel = [...this.jeevaItemSelections]
        if (label === 'Use') sel = sel.filter((v) => v !== 'Revoke')
        if (label === 'Revoke') sel = sel.filter((v) => v !== 'Use')
        if (sel.includes(label)) sel = sel.filter((v) => v !== label)
        else sel.push(label)
        this.jeevaItemSelections = sel
        // propagate into first Jeeva tab if exists
        const firstJeeva = this.tabs.find((t) => t.type === 'jeeva')
        if (firstJeeva) {
          const cur = this.moduleData[firstJeeva.key] || {}
          this.moduleData[firstJeeva.key] = { ...cur, selections: sel }
        }
      },
      onJeevaItemsKeydown(e) {
        if (!this.jeevaItemOpen && (e.key === 'Enter' || e.key === ' ')) {
          e.preventDefault()
          this.jeevaItemOpen = true
          return
        }
        if (!this.jeevaItemOpen) return
        if (e.key === 'Escape') {
          e.preventDefault()
          this.jeevaItemOpen = false
          return
        }
        const max = this.jeevaItemOptions.length - 1
        if (e.key === 'ArrowDown') {
          e.preventDefault()
          this.jeevaItemFocusIndex = Math.min(max, this.jeevaItemFocusIndex + 1)
        }
        if (e.key === 'ArrowUp') {
          e.preventDefault()
          this.jeevaItemFocusIndex = Math.max(0, this.jeevaItemFocusIndex - 1)
        }
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault()
          this.toggleJeevaItem(this.jeevaItemOptions[this.jeevaItemFocusIndex])
        }
      },

      // Tabs
      syncTabs(type, values) {
        const prefix = { wellsoft: 'W', jeeva: 'J', internet: 'I' }[type]
        const newTabs = []
        const newData = { ...this.moduleData }
        values.forEach((val) => {
          const key = `${prefix}:${val}`
          if (!newData[key]) newData[key] = {}
          newTabs.push({
            key,
            label: val,
            type,
            component: this.componentFor(type)
          })
        })
        this.tabs = newTabs
        this.moduleData = newData
        this.activeTab = this.tabs[0]?.key || ''
      },

      // Helpers
      isSelected(type, value) {
        if (type === 'wellsoft') return this.selectedWellsoft.includes(value)
        if (type === 'jeeva') return this.selectedJeeva.includes(value)
        return false
      },
      toggleWellsoft(m) {
        this.selectedWellsoft = this.isSelected('wellsoft', m)
          ? this.selectedWellsoft.filter((x) => x !== m)
          : [...this.selectedWellsoft, m]
      },
      toggleJeeva(m) {
        this.selectedJeeva = this.isSelected('jeeva', m)
          ? this.selectedJeeva.filter((x) => x !== m)
          : [...this.selectedJeeva, m]
      },
      selectAll(type) {
        if (type === 'wellsoft') this.selectedWellsoft = [...this.wellsoftModules]
        if (type === 'jeeva') this.selectedJeeva = [...this.jeevaModules]
      },
      clearAll(type) {
        if (type === 'wellsoft') this.selectedWellsoft = []
        if (type === 'jeeva') this.selectedJeeva = []
      },
      componentFor(type) {
        if (type === 'wellsoft') return 'WellsoftPanel'
        if (type === 'jeeva') return 'JeevaPanel'
        return 'InternetPanel'
      },
      toggleAccordion(key) {
        this.openAccordions.has(key)
          ? this.openAccordions.delete(key)
          : this.openAccordions.add(key)
      },
      tryCloseTab(key) {
        const t = this.tabs.find((x) => x.key === key)
        if (!t) return
        this.confirm = { key, label: t.label }
      },
      closeTab(key) {
        this.tabs = this.tabs.filter((t) => t.key !== key)
        const data = { ...this.moduleData }
        delete data[key]
        this.moduleData = data
        const [p, v] = key.split(':')
        if (p === 'W') this.selectedWellsoft = this.selectedWellsoft.filter((x) => x !== v)
        if (p === 'J') this.selectedJeeva = this.selectedJeeva.filter((x) => x !== v)
        this.confirm = { key: '', label: '' }
        this.activeTab = this.tabs[0]?.key || ''
      },

      // Submit
      onSubmit() {
        this.errors = { pfNumber: '', staffName: '' }
        if (!this.form.shared.pfNumber) this.errors.pfNumber = 'PF Number is required.'
        if (!this.form.shared.staffName) this.errors.staffName = 'Staff Name is required.'
        if (this.summaryErrors.length) return

        const payload = {
          shared: { ...this.form.shared },
          jeevaInline: { ...this.jeevaInline },
          modules: this.tabs.map((t) => ({
            type: t.type,
            name: t.label,
            details: this.moduleData[t.key] || {}
          })),
          internetPurposes: this.internetPurposes.filter((purpose) => purpose.trim()),
          wellsoftRequestType: this.wellsoftRequestType
        }
        console.log('Submitting payload', payload)
        this.toast = {
          show: true,
          message: 'Combined request submitted. You will receive updates.'
        }
        setTimeout(() => (this.toast.show = false), 3000)
      },
      onReset() {
        this.form = {
          shared: { pfNumber: '', staffName: '', department: '', phone: '' },
          accessRights: {
            type: '',
            tempDate: {
              month: '',
              day: '',
              year: ''
            }
          },
          approvals: {
            hod: {
              name: '',
              signature: '',
              date: ''
            },
            divisionalDirector: {
              name: '',
              signature: '',
              date: ''
            },
            directorICT: {
              name: '',
              signature: '',
              date: ''
            }
          },
          comments: '',
          implementation: {
            headIT: {
              name: '',
              signature: '',
              date: ''
            },
            ictOfficer: {
              name: '',
              signature: '',
              date: ''
            }
          }
        }
        this.selectedWellsoft = []
        this.selectedJeeva = []
        this.wellsoftQuery = ''
        this.jeevaQuery = ''
        this.internetPurposes = ['', '', '', '']
        this.wellsoftRequestType = 'use'
        this.tabs = []
        this.moduleData = {}
        this.activeTab = ''
        this.openAccordions.clear()
        this.errors = { pfNumber: '', staffName: '' }
        // Reset signatures
        this.signaturePreview = ''
        this.signatureFileName = ''
        this.hodSignaturePreview = ''
        this.hodSignatureFileName = ''
        this.divDirectorSignaturePreview = ''
        this.divDirectorSignatureFileName = ''
        this.directorICTSignaturePreview = ''
        this.directorICTSignatureFileName = ''
        this.headITSignaturePreview = ''
        this.headITSignatureFileName = ''
        this.ictOfficerSignaturePreview = ''
        this.ictOfficerSignatureFileName = ''
      },

      // Review mode methods
      async loadRequestData() {
        try {
          this.loading = true
          this.error = null
          
          console.log('Loading request data for ID:', this.requestId)
          const response = await combinedAccessService.getRequestById(this.requestId)
          
          if (response.success && response.data) {
            this.requestData = response.data
            console.log('Loaded request data:', this.requestData)
            console.log('Signature path from API:', this.requestData.signature_path)
            console.log('Type of signature_path:', typeof this.requestData.signature_path)

            // Populate form with request data
            this.form.shared = {
              pfNumber: this.requestData.pf_number || '',
              staffName: this.requestData.staff_name || this.requestData.full_name || '',
              department: this.requestData.department || this.requestData.department_name || '',
              phone: this.requestData.phone || this.requestData.phone_number || ''
            }

            // Handle signature data
            if (this.requestData.signature_path) {
              console.log('Signature found:', this.requestData.signature_path)
              // In review mode, we don't load the actual file, just show the status
              this.signatureFileName = this.getSignatureFileName(this.requestData.signature_path)
            }

            // Populate module selections based on actual database data
            if (this.requestData.request_types || this.requestData.request_type) {
              const types = this.requestData.request_types || this.requestData.request_type || []
              
              // Handle Wellsoft modules
              if (types.includes('wellsoft')) {
                const wellsoftModules = this.requestData.wellsoft_modules || []
                this.selectedWellsoft = Array.isArray(wellsoftModules) ? wellsoftModules : []
                console.log('Loaded Wellsoft modules:', this.selectedWellsoft)
              } else {
                this.selectedWellsoft = []
              }
              
              // Handle Jeeva modules
              if (types.includes('jeeva_access') || types.includes('jeeva')) {
                const jeevaModules = this.requestData.jeeva_modules || []
                this.selectedJeeva = Array.isArray(jeevaModules) ? jeevaModules : []
                console.log('Loaded Jeeva modules:', this.selectedJeeva)
              } else {
                this.selectedJeeva = []
              }
              
              // Handle Internet purposes
              if (types.includes('internet_access_request') || types.includes('internet')) {
                const internetPurposes = this.requestData.internet_purposes || this.requestData.purpose || []
                const purposes = Array.isArray(internetPurposes) ? internetPurposes : [internetPurposes]
                // Ensure we have exactly 4 purpose slots
                this.internetPurposes = [...purposes, '', '', '', ''].slice(0, 4)
                console.log('Loaded Internet purposes:', this.internetPurposes)
              } else {
                this.internetPurposes = ['', '', '', '']
              }
            } else {
              // Reset all selections if no request types
              this.selectedWellsoft = []
              this.selectedJeeva = []
              this.internetPurposes = ['', '', '', '']
            }

            console.log('Form populated successfully')
          } else {
            throw new Error(response.error || 'Failed to load request data')
          }
        } catch (error) {
          console.error('Error loading request data:', error)
          this.error = `Failed to load request data: ${error.message}`
          this.toast = {
            show: true,
            message: 'Error loading request data'
          }
          setTimeout(() => (this.toast.show = false), 3000)
        } finally {
          this.loading = false
        }
      },

      getApprovalStatus(role) {
        if (!this.requestData) return 'pending'

        switch (role) {
          case 'hod':
            return this.requestData.hod_approval_status || 'pending'
          case 'divisional':
            return this.requestData.divisional_approval_status || 'pending'
          case 'dict':
            return this.requestData.dict_approval_status || 'pending'
          case 'headOfIt':
            return this.requestData.head_it_approval_status || 'pending'
          case 'ict':
            return this.requestData.ict_approval_status || 'pending'
          default:
            return 'pending'
        }
      },

      // Extract filename from signature path
      getSignatureFileName(signaturePath) {
        if (!signaturePath) return ''
        return signaturePath.split('/').pop() || signaturePath
      },

      canApproveAtStage() {
        // This should check if the current user can approve at the current stage
        // Based on the user's role and current approval status
        return true // Simplified for now
      },

      async approveRequest() {
        try {
          this.loading = true
          await combinedAccessService.updateHodApproval(this.requestId, {
            status: 'approved',
            comments: this.form.comments || 'Approved'
          })

          this.toast = {
            show: true,
            message: 'Request approved successfully'
          }
          setTimeout(() => {
            this.toast.show = false
            this.goBackToRequests()
          }, 2000)
        } catch (error) {
          console.error('Error approving request:', error)
          this.toast = {
            show: true,
            message: 'Error approving request'
          }
          setTimeout(() => (this.toast.show = false), 3000)
        } finally {
          this.loading = false
        }
      },

      async rejectRequest() {
        const reason = prompt('Please provide a reason for rejection:')
        if (!reason) return

        try {
          this.loading = true
          await combinedAccessService.updateHodApproval(this.requestId, {
            status: 'rejected',
            comments: reason
          })

          this.toast = {
            show: true,
            message: 'Request rejected'
          }
          setTimeout(() => {
            this.toast.show = false
            this.goBackToRequests()
          }, 2000)
        } catch (error) {
          console.error('Error rejecting request:', error)
          this.toast = {
            show: true,
            message: 'Error rejecting request'
          }
          setTimeout(() => (this.toast.show = false), 3000)
        } finally {
          this.loading = false
        }
      },

      goBackToRequests() {
        this.$router.push({ name: 'HODCombinedRequestList' })
      },

      // Signature handling methods
      triggerFileUpload() {
        this.$refs.signatureInput.click()
      },

      onSignatureChange(e) {
        const file = e.target.files[0]
        this.form.shared.signature = file || null

        if (!file) {
          this.signaturePreview = ''
          this.signatureFileName = ''
          return
        }

        // Validate file type
        const allowedTypes = ['image/png', 'image/jpeg', 'image/jpg', 'application/pdf']
        if (!allowedTypes.includes(file.type)) {
          this.showNotification('Please select a valid file (PNG, JPG, or PDF)')
          this.clearSignature()
          return
        }

        // Validate file size (max 5MB)
        if (file.size > 5 * 1024 * 1024) {
          this.showNotification('File size must be less than 5MB')
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
        this.form.shared.signature = null
        this.signaturePreview = ''
        this.signatureFileName = ''
        if (this.$refs.signatureInput) {
          this.$refs.signatureInput.value = ''
        }
      },

      showNotification(message) {
        // Simple notification - you can replace with a proper notification system
        alert(message)
      },

      // HOD Signature methods
      triggerHodSignatureUpload() {
        this.$refs.hodSignatureInput.click()
      },

      onHodSignatureChange(e) {
        const file = e.target.files[0]
        this.form.approvals.hod.signature = file || null

        if (!file) {
          this.hodSignaturePreview = ''
          this.hodSignatureFileName = ''
          return
        }

        const allowedTypes = ['image/png', 'image/jpeg', 'image/jpg', 'application/pdf']
        if (!allowedTypes.includes(file.type)) {
          this.showNotification('Please select a valid file (PNG, JPG, or PDF)')
          this.clearHodSignature()
          return
        }

        if (file.size > 5 * 1024 * 1024) {
          this.showNotification('File size must be less than 5MB')
          this.clearHodSignature()
          return
        }

        this.hodSignatureFileName = file.name

        if (file.type.startsWith('image/')) {
          const reader = new FileReader()
          reader.onload = () => {
            this.hodSignaturePreview = reader.result
          }
          reader.readAsDataURL(file)
        } else {
          this.hodSignaturePreview = 'pdf'
        }
      },

      clearHodSignature() {
        this.form.approvals.hod.signature = null
        this.hodSignaturePreview = ''
        this.hodSignatureFileName = ''
        if (this.$refs.hodSignatureInput) {
          this.$refs.hodSignatureInput.value = ''
        }
      },

      // Divisional Director Signature methods
      triggerDivDirectorSignatureUpload() {
        this.$refs.divDirectorSignatureInput.click()
      },

      onDivDirectorSignatureChange(e) {
        const file = e.target.files[0]
        this.form.approvals.divisionalDirector.signature = file || null

        if (!file) {
          this.divDirectorSignaturePreview = ''
          this.divDirectorSignatureFileName = ''
          return
        }

        const allowedTypes = ['image/png', 'image/jpeg', 'image/jpg', 'application/pdf']
        if (!allowedTypes.includes(file.type)) {
          this.showNotification('Please select a valid file (PNG, JPG, or PDF)')
          this.clearDivDirectorSignature()
          return
        }

        if (file.size > 5 * 1024 * 1024) {
          this.showNotification('File size must be less than 5MB')
          this.clearDivDirectorSignature()
          return
        }

        this.divDirectorSignatureFileName = file.name

        if (file.type.startsWith('image/')) {
          const reader = new FileReader()
          reader.onload = () => {
            this.divDirectorSignaturePreview = reader.result
          }
          reader.readAsDataURL(file)
        } else {
          this.divDirectorSignaturePreview = 'pdf'
        }
      },

      clearDivDirectorSignature() {
        this.form.approvals.divisionalDirector.signature = null
        this.divDirectorSignaturePreview = ''
        this.divDirectorSignatureFileName = ''
        if (this.$refs.divDirectorSignatureInput) {
          this.$refs.divDirectorSignatureInput.value = ''
        }
      },

      // Director ICT Signature methods
      triggerDirectorICTSignatureUpload() {
        this.$refs.directorICTSignatureInput.click()
      },

      onDirectorICTSignatureChange(e) {
        const file = e.target.files[0]
        this.form.approvals.directorICT.signature = file || null

        if (!file) {
          this.directorICTSignaturePreview = ''
          this.directorICTSignatureFileName = ''
          return
        }

        const allowedTypes = ['image/png', 'image/jpeg', 'image/jpg', 'application/pdf']
        if (!allowedTypes.includes(file.type)) {
          this.showNotification('Please select a valid file (PNG, JPG, or PDF)')
          this.clearDirectorICTSignature()
          return
        }

        if (file.size > 5 * 1024 * 1024) {
          this.showNotification('File size must be less than 5MB')
          this.clearDirectorICTSignature()
          return
        }

        this.directorICTSignatureFileName = file.name

        if (file.type.startsWith('image/')) {
          const reader = new FileReader()
          reader.onload = () => {
            this.directorICTSignaturePreview = reader.result
          }
          reader.readAsDataURL(file)
        } else {
          this.directorICTSignaturePreview = 'pdf'
        }
      },

      clearDirectorICTSignature() {
        this.form.approvals.directorICT.signature = null
        this.directorICTSignaturePreview = ''
        this.directorICTSignatureFileName = ''
        if (this.$refs.directorICTSignatureInput) {
          this.$refs.directorICTSignatureInput.value = ''
        }
      },

      // Head IT Signature methods
      triggerHeadITSignatureUpload() {
        this.$refs.headITSignatureInput.click()
      },

      onHeadITSignatureChange(e) {
        const file = e.target.files[0]
        this.form.implementation.headIT.signature = file || null

        if (!file) {
          this.headITSignaturePreview = ''
          this.headITSignatureFileName = ''
          return
        }

        const allowedTypes = ['image/png', 'image/jpeg', 'image/jpg', 'application/pdf']
        if (!allowedTypes.includes(file.type)) {
          this.showNotification('Please select a valid file (PNG, JPG, or PDF)')
          this.clearHeadITSignature()
          return
        }

        if (file.size > 5 * 1024 * 1024) {
          this.showNotification('File size must be less than 5MB')
          this.clearHeadITSignature()
          return
        }

        this.headITSignatureFileName = file.name

        if (file.type.startsWith('image/')) {
          const reader = new FileReader()
          reader.onload = () => {
            this.headITSignaturePreview = reader.result
          }
          reader.readAsDataURL(file)
        } else {
          this.headITSignaturePreview = 'pdf'
        }
      },

      clearHeadITSignature() {
        this.form.implementation.headIT.signature = null
        this.headITSignaturePreview = ''
        this.headITSignatureFileName = ''
        if (this.$refs.headITSignatureInput) {
          this.$refs.headITSignatureInput.value = ''
        }
      },

      // ICT Officer Signature methods
      triggerIctOfficerSignatureUpload() {
        this.$refs.ictOfficerSignatureInput.click()
      },

      onIctOfficerSignatureChange(e) {
        const file = e.target.files[0]
        this.form.implementation.ictOfficer.signature = file || null

        if (!file) {
          this.ictOfficerSignaturePreview = ''
          this.ictOfficerSignatureFileName = ''
          return
        }

        const allowedTypes = ['image/png', 'image/jpeg', 'image/jpg', 'application/pdf']
        if (!allowedTypes.includes(file.type)) {
          this.showNotification('Please select a valid file (PNG, JPG, or PDF)')
          this.clearIctOfficerSignature()
          return
        }

        if (file.size > 5 * 1024 * 1024) {
          this.showNotification('File size must be less than 5MB')
          this.clearIctOfficerSignature()
          return
        }

        this.ictOfficerSignatureFileName = file.name

        if (file.type.startsWith('image/')) {
          const reader = new FileReader()
          reader.onload = () => {
            this.ictOfficerSignaturePreview = reader.result
          }
          reader.readAsDataURL(file)
        } else {
          this.ictOfficerSignaturePreview = 'pdf'
        }
      },

      clearIctOfficerSignature() {
        this.form.implementation.ictOfficer.signature = null
        this.ictOfficerSignaturePreview = ''
        this.ictOfficerSignatureFileName = ''
        if (this.$refs.ictOfficerSignatureInput) {
          this.$refs.ictOfficerSignatureInput.value = ''
        }
      }
    }
  }
</script>

<style scoped>
  /* Medical Background Animations */
  @keyframes float {
    0%,
    100% {
      transform: translateY(0px);
    }
    50% {
      transform: translateY(-20px);
    }
  }

  .animate-float {
    animation: float 6s ease-in-out infinite;
  }

  /* Define primary color */
  .bg-primary {
    background-color: #1e40af; /* Blue-700 */
  }

  .text-primary {
    color: #1e40af; /* Blue-700 */
  }

  .border-primary {
    border-color: #1e40af; /* Blue-700 */
  }

  .focus\:border-primary:focus {
    border-color: #1e40af;
  }

  /* Custom checkbox and radio styling */
  input[type='checkbox']:checked,
  input[type='radio']:checked {
    background-color: #1e40af;
    border-color: #1e40af;
  }

  input[type='checkbox']:focus,
  input[type='radio']:focus {
    box-shadow: 0 0 0 3px rgba(30, 64, 175, 0.1);
  }

  /* Smooth transitions */
  .transition-all {
    transition: all 0.3s ease;
  }

  /* Glass morphism effect */
  .backdrop-blur-sm {
    backdrop-filter: blur(8px);
  }

  /* Enhanced form sections */
  .border-l-2 {
    position: relative;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.05));
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
  }

  .border-l-2:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
  }

  /* Full width responsive container */
  .label {
    @apply block text-sm font-medium text-gray-700 mb-1;
  }
  .input {
    @apply w-full rounded-lg border border-gray-300 px-4 py-3 text-base focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary;
  }
  .btn-primary {
    @apply inline-flex items-center px-6 py-3 rounded-lg bg-primary text-white text-base font-semibold hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition shadow-md hover:shadow-lg;
  }
  .btn-secondary {
    @apply inline-flex items-center px-6 py-3 rounded-lg bg-gray-100 text-gray-800 text-base font-semibold hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300 transition shadow-md hover:shadow-lg;
  }
  .btn-danger {
    @apply inline-flex items-center px-4 py-2 rounded-md bg-red-600 text-white text-sm font-medium hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition;
  }
  .btn-sm {
    @apply px-2 py-1 text-xs;
  }
  .card {
    @apply bg-white rounded-lg shadow-md border border-gray-200 p-4 mb-3;
  }
  .error {
    @apply text-xs text-red-600 mt-1;
  }
  .tab {
    @apply inline-flex items-center px-3 py-2 rounded-md bg-white border border-gray-200 text-sm text-gray-700 hover:bg-gray-50 transition;
  }
  .tab-active {
    @apply bg-blue-50 border-blue-300 text-blue-800;
  }
  .fade-enter-active,
  .fade-leave-active {
    transition:
      opacity 0.2s,
      transform 0.2s;
  }
  .fade-enter-from,
  .fade-leave-to {
    opacity: 0;
    transform: translateY(4px);
  }
  .focus\:ring-primary {
    --tw-ring-color: #1e40af;
  }
  .option-tile {
    @apply flex items-center gap-2 px-2 py-2 rounded-md border border-gray-200 bg-white hover:bg-gray-50 hover:border-gray-300 text-left text-sm;
  }
  .option-tile-active {
    @apply border-blue-300 bg-blue-50;
  }

  /* Print styles */
  @media print {
    .min-h-screen {
      min-height: auto;
    }

    button {
      display: none;
    }

    .shadow-xl {
      box-shadow: none;
    }

    .bg-gradient-to-br {
      background: white;
    }
  }

  /* Responsive adjustments */
  @media (max-width: 768px) {
    .grid-cols-4 {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .grid-cols-3 {
      grid-template-columns: repeat(1, minmax(0, 1fr));
    }

    .grid-cols-2 {
      grid-template-columns: repeat(1, minmax(0, 1fr));
    }
  }

  @media (max-width: 640px) {
    .lg\:grid-cols-4 {
      grid-template-columns: repeat(1, minmax(0, 1fr));
    }

    .md\:grid-cols-3 {
      grid-template-columns: repeat(1, minmax(0, 1fr));
    }
  }

  /* Responsive adjustments */
  @media (max-width: 1024px) {
    .lg\:col-span-2 {
      grid-column: span 1;
    }
    .lg\:col-span-1 {
      grid-column: span 1;
    }
  }

  /* Improve card spacing on smaller screens */
  @media (max-width: 768px) {
    .card {
      padding: 0.75rem;
    }
  }

  /* Ensure full width utilization */
  .max-w-full {
    max-width: 100%;
  }

  .max-w-8xl {
    max-width: 88rem; /* 1408px */
  }

  /* Responsive grid adjustments */
  @media (max-width: 1024px) {
    .lg\:col-span-3 {
      grid-column: span 1;
    }
  }
</style>
