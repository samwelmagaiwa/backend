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
                                <p
                                  class="text-green-300 text-xs font-semibold flex items-center justify-center gap-1"
                                >
                                  <i class="fas fa-check text-xs"></i>
                                  Signed
                                </p>
                                <p
                                  v-if="requestData?.signature_path"
                                  class="text-blue-200 text-xs mt-1"
                                >
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
                    <div
                      class="flex justify-center mb-3"
                      :class="{
                        'opacity-50': isReviewMode && !hasWellsoftRequest && !hasJeevaRequest
                      }"
                    >
                      <div
                        class="bg-white/10 rounded-lg p-2 border border-blue-300/30 backdrop-blur-sm"
                      >
                        <label
                          class="block text-sm font-bold text-blue-100 mb-2 text-center flex items-center justify-center gap-2"
                        >
                          <i class="fas fa-toggle-on mr-1 text-blue-300 text-xs"></i>
                          Module Requested for
                          <span class="text-red-400">*</span>
                          <span
                            v-if="isReviewMode && !hasWellsoftRequest && !hasJeevaRequest"
                            class="text-xs px-2 py-1 bg-gray-500/30 rounded-full text-gray-300"
                          >
                            <i class="fas fa-lock text-xs mr-1"></i>
                            Not Applicable
                          </span>
                        </label>
                        <div class="flex items-center gap-4 justify-center">
                          <label
                            class="flex items-center cursor-pointer hover:bg-blue-500/20 p-2 rounded transition-all"
                            :class="{
                              'pointer-events-none':
                                isReviewMode && !hasWellsoftRequest && !hasJeevaRequest
                            }"
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
                            :class="{
                              'pointer-events-none':
                                isReviewMode && !hasWellsoftRequest && !hasJeevaRequest
                            }"
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
                      <label
                        class="block text-sm font-bold text-blue-100 mb-3 flex items-center gap-2"
                      >
                        Wellsoft Modules <span class="text-red-400">*</span>
                        <span
                          v-if="isWellsoftReadonly"
                          class="text-xs px-2 py-1 bg-gray-500/30 rounded-full text-gray-300"
                        >
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
                      <div
                        v-if="isWellsoftReadonly && selectedWellsoft.length === 0"
                        class="bg-gray-500/10 rounded-lg p-4 border border-gray-400/30 text-center"
                      >
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
                      <label
                        class="block text-sm font-bold text-blue-100 mb-3 flex items-center gap-2"
                      >
                        Jeeva Modules <span class="text-red-400">*</span>
                        <span
                          v-if="isJeevaReadonly"
                          class="text-xs px-2 py-1 bg-gray-500/30 rounded-full text-gray-300"
                        >
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
                      <div
                        v-if="isJeevaReadonly && selectedJeeva.length === 0"
                        class="bg-gray-500/10 rounded-lg p-4 border border-gray-400/30 text-center"
                      >
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
                      <label
                        class="block text-sm font-bold text-blue-100 mb-3 flex items-center gap-2"
                      >
                        Internet Purpose <span class="text-red-400">*</span>
                        <span
                          v-if="isInternetReadonly"
                          class="text-xs px-2 py-1 bg-gray-500/30 rounded-full text-gray-300"
                        >
                          <i class="fas fa-lock text-xs mr-1"></i>
                          Not Requested
                        </span>
                      </label>

                      <!-- No purposes message for readonly state -->
                      <div
                        v-if="isInternetReadonly && !internetPurposes.some((p) => p.trim())"
                        class="bg-gray-500/10 rounded-lg p-4 border border-gray-400/30 text-center"
                      >
                        <i class="fas fa-info-circle text-gray-400 mb-2 text-lg"></i>
                        <p class="text-gray-300 text-sm">No internet access purposes requested</p>
                      </div>

                      <!-- Purpose inputs -->
                      <div
                        v-if="!isInternetReadonly || internetPurposes.some((p) => p.trim())"
                        class="space-y-2"
                      >
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
                        :class="{ 'opacity-50': !isHodApprovalEditable }"
                      >
                        <h5
                          class="font-bold text-white mb-4 text-center text-sm flex items-center justify-center gap-2"
                        >
                          <i class="fas fa-user-tie mr-2 text-blue-300"></i>
                          HoD/BM
                          <span
                            v-if="isStageCompleted('hod')"
                            class="text-xs px-2 py-1 bg-green-500/30 rounded-full text-green-300"
                          >
                            <i class="fas fa-check text-xs mr-1"></i>
                            Completed
                          </span>
                          <span
                            v-else-if="!isHodApprovalEditable && isReviewMode"
                            class="text-xs px-2 py-1 bg-gray-500/30 rounded-full text-gray-300"
                          >
                            <i class="fas fa-clock text-xs mr-1"></i>
                            Pending
                          </span>
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
                                readonly
                                :placeholder="currentUser?.name || 'Loading user...' "
                                class="medical-input w-full px-3 py-2 bg-white/15 border border-blue-300/30 rounded-lg focus:border-blue-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm cursor-not-allowed"
                                :title="'Auto-filled with: ' + (currentUser?.name || 'Loading...')"
                              />
                              <div class="absolute right-3 top-1/2 transform -translate-y-1/2">
                                <i class="fas fa-lock text-blue-300 text-xs" title="This field is auto-populated from your account"></i>
                              </div>
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
                                    v-if="isHodApprovalEditable"
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

                                <div
                                  v-if="isHodApprovalEditable"
                                  class="absolute top-2 right-2 flex gap-1"
                                >
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
                                :readonly="!isHodApprovalEditable"
                                class="medical-input w-full px-3 py-2 bg-white/15 border border-blue-300/30 rounded-lg focus:border-blue-400 focus:outline-none text-white backdrop-blur-sm hod-approval-editable"
                                :class="{ 'cursor-not-allowed': !isHodApprovalEditable }"
                              />
                            </div>
                          </div>
                        </div>
                      </div>

                      <!-- Divisional Director -->
                      <div
                        class="bg-white/15 rounded-lg p-4 border border-blue-300/30 backdrop-blur-sm"
                        :class="{ 'opacity-50': !isDivisionalApprovalEditable }"
                      >
                        <h5
                          class="font-bold text-white mb-4 text-center text-sm flex items-center justify-center gap-2"
                        >
                          <i class="fas fa-user-circle mr-2 text-blue-300"></i>
                          Divisional Director
                          <span
                            v-if="isStageCompleted('divisional')"
                            class="text-xs px-2 py-1 bg-green-500/30 rounded-full text-green-300"
                          >
                            <i class="fas fa-check text-xs mr-1"></i>
                            Completed
                          </span>
                          <span
                            v-else-if="!isDivisionalApprovalEditable && isReviewMode"
                            class="text-xs px-2 py-1 bg-gray-500/30 rounded-full text-gray-300"
                          >
                            <i class="fas fa-clock text-xs mr-1"></i>
                            Pending
                          </span>
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
                                readonly
                                :placeholder="currentUser?.name || 'Loading user...' "
                                class="medical-input w-full px-3 py-2 bg-white/15 border border-blue-300/30 rounded-lg focus:border-blue-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm cursor-not-allowed"
                                :title="'Auto-filled with: ' + (currentUser?.name || 'Loading...')"
                              />
                              <div class="absolute right-3 top-1/2 transform -translate-y-1/2">
                                <i class="fas fa-lock text-blue-300 text-xs" title="This field is auto-populated from your account"></i>
                              </div>
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
                                    v-if="isDivisionalApprovalEditable"
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

                                <div
                                  v-if="isDivisionalApprovalEditable"
                                  class="absolute top-2 right-2 flex gap-1"
                                >
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
                                :readonly="!isDivisionalApprovalEditable"
                                class="medical-input w-full px-3 py-2 bg-white/15 border border-blue-300/30 rounded-lg focus:border-blue-400 focus:outline-none text-white backdrop-blur-sm"
                                :class="{ 'cursor-not-allowed': !isDivisionalApprovalEditable }"
                              />
                            </div>
                          </div>
                        </div>
                      </div>

                      <!-- Director of ICT -->
                      <div
                        class="bg-white/15 rounded-lg p-4 border border-blue-300/30 backdrop-blur-sm"
                        :class="{ 'opacity-50': !isIctDirectorApprovalEditable }"
                      >
                        <h5
                          class="font-bold text-white mb-4 text-center text-sm flex items-center justify-center gap-2"
                        >
                          <i class="fas fa-laptop-code mr-2 text-blue-300"></i>
                          Director of ICT
                          <span
                            v-if="isStageCompleted('ict_director')"
                            class="text-xs px-2 py-1 bg-green-500/30 rounded-full text-green-300"
                          >
                            <i class="fas fa-check text-xs mr-1"></i>
                            Completed
                          </span>
                          <span
                            v-else-if="!isIctDirectorApprovalEditable && isReviewMode"
                            class="text-xs px-2 py-1 bg-gray-500/30 rounded-full text-gray-300"
                          >
                            <i class="fas fa-clock text-xs mr-1"></i>
                            Pending
                          </span>
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
                                readonly
                                :placeholder="currentUser?.name || 'Loading user...' "
                                class="medical-input w-full px-3 py-2 bg-white/15 border border-blue-300/30 rounded-lg focus:border-blue-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm cursor-not-allowed"
                                :title="'Auto-filled with: ' + (currentUser?.name || 'Loading...')"
                              />
                              <div class="absolute right-3 top-1/2 transform -translate-y-1/2">
                                <i class="fas fa-lock text-blue-300 text-xs" title="This field is auto-populated from your account"></i>
                              </div>
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
                                    v-if="isIctDirectorApprovalEditable"
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

                                <div
                                  v-if="isIctDirectorApprovalEditable"
                                  class="absolute top-2 right-2 flex gap-1"
                                >
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
                                :readonly="!isIctDirectorApprovalEditable"
                                class="medical-input w-full px-3 py-2 bg-white/15 border border-blue-300/30 rounded-lg focus:border-blue-400 focus:outline-none text-white backdrop-blur-sm"
                                :class="{ 'cursor-not-allowed': !isIctDirectorApprovalEditable }"
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
                        :class="{ 'opacity-50': !isHeadItApprovalEditable }"
                      >
                        <h5
                          class="font-bold text-white mb-3 text-center text-sm flex items-center justify-center gap-2"
                        >
                          <i class="fas fa-user-cog mr-2"></i>
                          Head of IT
                          <span
                            v-if="isStageCompleted('head_it')"
                            class="text-xs px-2 py-1 bg-green-500/30 rounded-full text-green-300"
                          >
                            <i class="fas fa-check text-xs mr-1"></i>
                            Completed
                          </span>
                          <span
                            v-else-if="!isHeadItApprovalEditable && isReviewMode"
                            class="text-xs px-2 py-1 bg-gray-500/30 rounded-full text-gray-300"
                          >
                            <i class="fas fa-clock text-xs mr-1"></i>
                            Pending
                          </span>
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
                                readonly
                                :placeholder="currentUser?.name || 'Loading user...' "
                                class="medical-input w-full px-3 py-2 bg-white/15 border border-blue-300/30 rounded-lg focus:border-blue-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm cursor-not-allowed"
                                :title="'Auto-filled with: ' + (currentUser?.name || 'Loading...')"
                              />
                              <div class="absolute right-3 top-1/2 transform -translate-y-1/2">
                                <i class="fas fa-lock text-blue-300 text-xs" title="This field is auto-populated from your account"></i>
                              </div>
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
                                    v-if="isHeadItApprovalEditable"
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

                                <div
                                  v-if="isHeadItApprovalEditable"
                                  class="absolute top-1 right-1 flex gap-1"
                                >
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
                                :readonly="!isHeadItApprovalEditable"
                                class="medical-input w-full px-3 py-2 bg-white/15 border border-blue-300/30 rounded-lg focus:border-blue-400 focus:outline-none text-white backdrop-blur-sm"
                                :class="{ 'cursor-not-allowed': !isHeadItApprovalEditable }"
                              />
                            </div>
                          </div>
                        </div>
                      </div>

                      <!-- ICT Officer granting access -->
                      <div
                        class="bg-white/15 rounded-lg p-4 border border-blue-300/30 backdrop-blur-sm"
                        :class="{ 'opacity-50': !isIctOfficerApprovalEditable }"
                      >
                        <h5
                          class="font-bold text-white mb-3 text-center text-sm flex items-center justify-center gap-2"
                        >
                          <i class="fas fa-user-shield mr-2"></i>
                          ICT Officer granting access
                          <span
                            v-if="isStageCompleted('ict_officer')"
                            class="text-xs px-2 py-1 bg-green-500/30 rounded-full text-green-300"
                          >
                            <i class="fas fa-check text-xs mr-1"></i>
                            Completed
                          </span>
                          <span
                            v-else-if="!isIctOfficerApprovalEditable && isReviewMode"
                            class="text-xs px-2 py-1 bg-gray-500/30 rounded-full text-gray-300"
                          >
                            <i class="fas fa-clock text-xs mr-1"></i>
                            Pending
                          </span>
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
                                readonly
                                :placeholder="currentUser?.name || 'Loading user...' "
                                class="medical-input w-full px-3 py-2 bg-white/15 border border-blue-300/30 rounded-lg focus:border-blue-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm cursor-not-allowed"
                                :title="'Auto-filled with: ' + (currentUser?.name || 'Loading...')"
                              />
                              <div class="absolute right-3 top-1/2 transform -translate-y-1/2">
                                <i class="fas fa-lock text-blue-300 text-xs" title="This field is auto-populated from your account"></i>
                              </div>
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
                                    v-if="isIctOfficerApprovalEditable"
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

                  <!-- Action Buttons (Review Mode Only) -->
                  <div v-if="isReviewMode && canApproveAtStage()" class="flex justify-between gap-4 mt-6">
                    <!-- Approve Button - Left Side -->
                    <button
                      type="button"
                      @click="approveRequest"
                      :disabled="loading"
                      class="flex-1 px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-lg hover:from-green-700 hover:to-emerald-700 transition-all duration-300 font-semibold flex items-center justify-center shadow-lg hover:shadow-xl transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                    >
                      <i v-if="loading" class="fas fa-spinner fa-spin mr-2"></i>
                      <i v-else class="fas fa-check mr-2"></i>
                      {{ loading ? 'Processing...' : 'Approve Request' }}
                    </button>

                    <!-- Reject Button - Right Side -->
                    <button
                      type="button"
                      @click="rejectRequest"
                      :disabled="loading"
                      class="flex-1 px-6 py-3 bg-gradient-to-r from-red-600 to-pink-600 text-white rounded-lg hover:from-red-700 hover:to-pink-700 transition-all duration-300 font-semibold flex items-center justify-center shadow-lg hover:shadow-xl transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                    >
                      <i class="fas fa-times mr-2"></i>
                      Reject Request
                    </button>
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

    <!-- Rejection Reason Modal -->
    <transition name="fade">
      <div
        v-if="showRejectionModal"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
      >
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full p-6">
          <div class="flex items-center space-x-3 mb-4">
            <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
              <i class="fas fa-times text-red-600"></i>
            </div>
            <div>
              <h3 class="text-lg font-semibold text-gray-800">Reject Request</h3>
              <p class="text-sm text-gray-600">
                Please provide a reason for rejecting this request
              </p>
            </div>
          </div>

          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Rejection Reason *</label>
            <textarea
              v-model="rejectionReason"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm"
              rows="4"
              placeholder="Enter the reason for rejecting this request..."
              required
            ></textarea>
          </div>

          <div class="flex justify-end space-x-3">
            <button
              @click="cancelRejectRequest"
              class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-colors"
            >
              Cancel
            </button>
            <button
              @click="confirmRejectRequest"
              :disabled="!rejectionReason?.trim()"
              class="px-4 py-2 bg-red-600 hover:bg-red-700 disabled:bg-red-300 text-white rounded-lg font-medium transition-colors flex items-center"
            >
              <i class="fas fa-times mr-2"></i>
              Reject Request
            </button>
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
  import authService from '@/services/authService.js'

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
        error: null,
        // Rejection modal data
        showRejectionModal: false,
        rejectionReason: '',
        // Current authenticated user data
        currentUser: null
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
        return (
          this.requestTypes.includes('internet_access_request') ||
          this.requestTypes.includes('internet')
        )
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
      },

      // Get current approval stage from request status
      currentApprovalStage() {
        if (!this.requestData) return 'pending'

        const status = this.requestData.status || 'pending'
        const stageMap = {
          pending: 'hod',
          pending_hod: 'hod',
          hod_approved: 'divisional',
          pending_divisional: 'divisional',
          divisional_approved: 'ict_director',
          pending_ict_director: 'ict_director',
          ict_director_approved: 'head_it',
          pending_head_it: 'head_it',
          head_it_approved: 'ict_officer',
          pending_ict_officer: 'ict_officer',
          implemented: 'completed',
          approved: 'completed',
          hod_rejected: 'completed',
          divisional_rejected: 'completed',
          ict_director_rejected: 'completed',
          head_it_rejected: 'completed',
          cancelled: 'completed'
        }

        return stageMap[status] || 'hod'
      },

      // Determine if approval sections should be readonly based on current stage
      isHodApprovalEditable() {
        return !this.isReviewMode || this.currentApprovalStage === 'hod'
      },

      isDivisionalApprovalEditable() {
        return !this.isReviewMode || this.currentApprovalStage === 'divisional'
      },

      isIctDirectorApprovalEditable() {
        return !this.isReviewMode || this.currentApprovalStage === 'ict_director'
      },

      isHeadItApprovalEditable() {
        return !this.isReviewMode || this.currentApprovalStage === 'head_it'
      },

      isIctOfficerApprovalEditable() {
        return !this.isReviewMode || this.currentApprovalStage === 'ict_officer'
      },

      // Check if a stage has been completed
      isStageCompleted() {
        return (stage) => {
          if (!this.requestData) return false

          const status = this.requestData.status || 'pending'
          const completedStages = {
            hod: [
              'hod_approved',
              'divisional_approved',
              'pending_divisional',
              'ict_director_approved',
              'pending_ict_director',
              'head_it_approved',
              'pending_head_it',
              'ict_officer_approved',
              'pending_ict_officer',
              'implemented',
              'approved'
            ],
            divisional: [
              'divisional_approved',
              'ict_director_approved',
              'pending_ict_director',
              'head_it_approved',
              'pending_head_it',
              'ict_officer_approved',
              'pending_ict_officer',
              'implemented',
              'approved'
            ],
            ict_director: [
              'ict_director_approved',
              'head_it_approved',
              'pending_head_it',
              'ict_officer_approved',
              'pending_ict_officer',
              'implemented',
              'approved'
            ],
            head_it: [
              'head_it_approved',
              'ict_officer_approved',
              'pending_ict_officer',
              'implemented',
              'approved'
            ],
            ict_officer: ['implemented', 'approved']
          }

          return completedStages[stage]?.includes(status) || false
        }
      }
    },
    async mounted() {
      // Try to get current user from multiple sources
      await this.getCurrentUser()
      
      // Fallback to localStorage or Vuex if API fails
      if (!this.currentUser || !this.currentUser.name) {
        console.log('🔄 API failed, trying localStorage fallback...')
        this.tryGetUserFromLocalStorage()
      }
      
      // Fallback to Vuex store
      if (!this.currentUser || !this.currentUser.name) {
        console.log('🔄 localStorage failed, trying Vuex store fallback...')
        this.tryGetUserFromStore()
      }

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
      },
      // Watch for when approval sections become editable (now just for logging)
      isHodApprovalEditable: {
        handler(isEditable) {
          console.log('HOD approval editable changed:', {
            isEditable,
            currentHodName: this.form.approvals.hod.name,
            hasCurrentUser: !!this.currentUser,
            isReviewMode: this.isReviewMode,
            currentApprovalStage: this.currentApprovalStage
          })
          // Role-based population is now handled by currentUser watcher
        },
        immediate: true
      },
      isDivisionalApprovalEditable: {
        handler(isEditable) {
          console.log('Divisional approval editable changed:', {
            isEditable,
            currentDivisionalName: this.form.approvals.divisionalDirector.name,
            hasCurrentUser: !!this.currentUser,
            isReviewMode: this.isReviewMode,
            currentApprovalStage: this.currentApprovalStage
          })
          // Role-based population is now handled by currentUser watcher
        },
        immediate: true
      },
      isIctDirectorApprovalEditable: {
        handler(isEditable) {
          console.log('ICT Director approval editable changed:', {
            isEditable,
            currentIctDirectorName: this.form.approvals.directorICT.name,
            hasCurrentUser: !!this.currentUser,
            isReviewMode: this.isReviewMode,
            currentApprovalStage: this.currentApprovalStage
          })
          // Role-based population is now handled by currentUser watcher
        },
        immediate: true
      },
      isHeadItApprovalEditable: {
        handler(isEditable) {
          console.log('Head IT approval editable changed:', {
            isEditable,
            currentHeadItName: this.form.implementation.headIT.name,
            hasCurrentUser: !!this.currentUser,
            isReviewMode: this.isReviewMode,
            currentApprovalStage: this.currentApprovalStage
          })
          // Role-based population is now handled by currentUser watcher
        },
        immediate: true
      },
      isIctOfficerApprovalEditable: {
        handler(isEditable) {
          console.log('ICT Officer approval editable changed:', {
            isEditable,
            currentIctOfficerName: this.form.implementation.ictOfficer.name,
            hasCurrentUser: !!this.currentUser,
            isReviewMode: this.isReviewMode,
            currentApprovalStage: this.currentApprovalStage
          })
          // Role-based population is now handled by currentUser watcher
        },
        immediate: true
      },
      // Watch for when currentUser data is loaded to trigger auto-population
      currentUser: {
        handler(newUser) {
          if (newUser && newUser.name) {
            console.log('Current user data loaded, role-based auto-population:', {
              userName: newUser.name,
              userId: newUser.id,
              userRole: newUser.role,
              isReviewMode: this.isReviewMode,
              currentApprovalStage: this.currentApprovalStage
            })

            // Role-based population - only populate the field that matches user's role
            this.populateBasedOnUserRole(newUser)
          }
        },
        immediate: true
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
                const internetPurposes =
                  this.requestData.internet_purposes || this.requestData.purpose || []
                const purposes = Array.isArray(internetPurposes)
                  ? internetPurposes
                  : [internetPurposes]
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
        // Only show buttons in review mode
        if (!this.isReviewMode) return false

        // Only show buttons if request data is loaded
        if (!this.requestData) return false

        // Get current HOD approval status
        const hodStatus = this.requestData.hod_approval_status || 'pending'

        // Only show buttons if HOD approval is still pending
        // Once approved or rejected, HOD should not be able to change it
        return hodStatus === 'pending'
      },

      async approveRequest() {
        try {
          this.loading = true

          console.log('Approving request:', this.requestId)

          const result = await combinedAccessService.updateHodApproval(this.requestId, {
            status: 'approved',
            comments: this.form.comments || 'Approved by HOD'
          })

          if (result.success) {
            this.toast = {
              show: true,
              message: 'Request approved successfully'
            }
            setTimeout(() => {
              this.toast.show = false
              this.goBackToRequests()
            }, 2000)
          } else {
            throw new Error(result.error || 'Failed to approve request')
          }
        } catch (error) {
          console.error('Error approving request:', error)
          this.toast = {
            show: true,
            message: `Error approving request: ${error.message}`
          }
          setTimeout(() => (this.toast.show = false), 3000)
        } finally {
          this.loading = false
        }
      },

      async rejectRequest() {
        // Show rejection reason modal
        this.showRejectionModal = true
      },

      async confirmRejectRequest() {
        if (!this.rejectionReason?.trim()) {
          this.toast = {
            show: true,
            message: 'Please provide a reason for rejection'
          }
          setTimeout(() => (this.toast.show = false), 3000)
          return
        }

        try {
          this.loading = true
          this.showRejectionModal = false

          console.log('Rejecting request:', this.requestId, 'with reason:', this.rejectionReason)

          const result = await combinedAccessService.updateHodApproval(this.requestId, {
            status: 'rejected',
            comments: this.rejectionReason
          })

          if (result.success) {
            this.toast = {
              show: true,
              message: 'Request rejected successfully'
            }
            setTimeout(() => {
              this.toast.show = false
              this.goBackToRequests()
            }, 2000)
          } else {
            throw new Error(result.error || 'Failed to reject request')
          }
        } catch (error) {
          console.error('Error rejecting request:', error)
          this.toast = {
            show: true,
            message: `Error rejecting request: ${error.message}`
          }
          setTimeout(() => (this.toast.show = false), 3000)
        } finally {
          this.loading = false
          this.rejectionReason = ''
        }
      },

      cancelRejectRequest() {
        this.showRejectionModal = false
        this.rejectionReason = ''
      },

      goBackToRequests() {
        try {
          console.log('Navigating back to HOD combined requests list')
          this.$router.push({
            name: 'HODCombinedRequestList',
            path: '/hod-dashboard/combined-requests'
          })
        } catch (error) {
          console.error('Error navigating back to requests:', error)
          // Fallback navigation using path
          this.$router.push('/hod-dashboard/combined-requests')
        }
      },

      goToRequestDetails() {
        try {
          console.log('Navigating to request details for ID:', this.requestId)
          this.$router.push({
            path: '/request-details',
            query: {
              id: this.requestId,
              type: 'combined_access'
            }
          })
        } catch (error) {
          console.error('Error navigating to request details:', error)
          // Fallback navigation
          this.$router.push(`/request-details?id=${this.requestId}&type=combined_access`)
        }
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
      },
      // Manual debug method to trigger auto-population (for testing)
      debugAutoPopulation() {
        console.log('=== DEBUG AUTO-POPULATION =====')
        console.log('Current user:', this.currentUser)
        console.log('Is review mode:', this.isReviewMode)
        console.log('Current approval stage:', this.currentApprovalStage)
        console.log('HOD editable:', this.isHodApprovalEditable)
        console.log('HOD current name:', this.form.approvals.hod.name)
        console.log('Request data status:', this.requestData?.status)

        console.log('\n=== TRIGGERING ROLE-BASED AUTO-POPULATION ===')

        // Use role-based population for debugging
        if (this.currentUser?.name) {
          console.log('User role from database:', this.currentUser.role || this.currentUser.user_role)
          this.populateBasedOnUserRole(this.currentUser)
        } else {
          console.error('No current user data available!')
        }

        console.log('=== DEBUG COMPLETE ===')
      },

      // Get current authenticated user
      async getCurrentUser() {
        try {
          console.log('🔍 Attempting to fetch current user...')
          const response = await authService.getCurrentUser()
          console.log('📨 Raw API response:', response)
          
          if (response && response.success) {
            console.log('✅ API call successful, response.data:', response.data)
            
            // Handle different possible response structures
            let userData = null
            
            if (response.data && response.data.data && response.data.data.name) {
              // Case: {success: true, data: {data: {name: 'John', ...}}}
              userData = response.data.data
              console.log('👤 Current user loaded from response.data.data:', userData)
            } else if (response.data && response.data.user && response.data.user.name) {
              // Case: {success: true, data: {user: {name: 'John', ...}}}
              userData = response.data.user
              console.log('👤 Current user loaded from response.data.user:', userData)
            } else if (response.data && response.data.name) {
              // Case: {success: true, data: {name: 'John', ...}}
              userData = response.data
              console.log('👤 Current user loaded from response.data directly:', userData)
            } else {
              console.error('❌ User data structure not recognized. Full response:', JSON.stringify(response, null, 2))
              console.error('❌ response.data content:', response.data)
            }
            
            if (userData && userData.name) {
              this.currentUser = userData
              console.log('📋 User name from database:', userData.name)
              console.log('📋 User ID:', userData.id)
              console.log('📋 User email:', userData.email)
              console.log('📋 User role:', userData.role || userData.user_role)
            } else {
              console.error('❌ No valid user data found:', userData)
            }
          } else {
            console.warn('❌ Failed to get current user:', response?.message || 'Unknown error')
            console.log('❌ Response status:', response?.status)
          }
        } catch (error) {
          console.error('💥 Error fetching current user:', error)
          console.error('💥 Error details:', {
            message: error.message,
            response: error.response,
            status: error.response?.status,
            data: error.response?.data
          })
        }
      },

      // Fallback method to get user from localStorage
      tryGetUserFromLocalStorage() {
        try {
          const storedUser = localStorage.getItem('user_data')
          if (storedUser) {
            const userData = JSON.parse(storedUser)
            console.log('💾 Found user in localStorage:', userData)
            
            if (userData && userData.name) {
              this.currentUser = userData
              console.log('✅ Using localStorage user data:', this.currentUser.name)
            } else if (userData && userData.user && userData.user.name) {
              this.currentUser = userData.user
              console.log('✅ Using localStorage user.user data:', this.currentUser.name)
            }
          } else {
            console.log('❌ No user_data found in localStorage')
          }
        } catch (error) {
          console.error('❌ Error parsing localStorage user_data:', error)
        }
      },

      // Fallback method to get user from Vuex store
      tryGetUserFromStore() {
        try {
          // Try to get from Vuex store (if using Vuex)
          if (this.$store && this.$store.state && this.$store.state.auth) {
            const storeUser = this.$store.state.auth.user || this.$store.state.auth.currentUser
            if (storeUser && storeUser.name) {
              this.currentUser = storeUser
              console.log('✅ Using Vuex store user data:', this.currentUser.name)
            }
          }
          
          // Try alternative Vuex structure
          if (!this.currentUser && this.$store && this.$store.getters) {
            const getterUser = this.$store.getters.currentUser || this.$store.getters['auth/user']
            if (getterUser && getterUser.name) {
              this.currentUser = getterUser
              console.log('✅ Using Vuex getter user data:', this.currentUser.name)
            }
          }
          
          if (!this.currentUser) {
            console.log('❌ No user found in Vuex store')
          }
        } catch (error) {
          console.error('❌ Error accessing Vuex store:', error)
        }
      },

      // Role-based auto-population - only populate field matching user's role
      populateBasedOnUserRole(user) {
        if (!user || !user.name) {
          console.log('❌ No valid user data for role-based population')
          return
        }

        const userRole = user.role || user.user_role || ''
        const userName = user.name
        
        console.log(`🎯 Role-based population for role: "${userRole}" with name: "${userName}"`)
        
        // First, clear ALL approval name fields to ensure clean state
        console.log('🧺 Clearing all approval name fields before role-based population')
        this.form.approvals.hod.name = ''
        this.form.approvals.divisionalDirector.name = ''
        this.form.approvals.directorICT.name = ''
        this.form.implementation.headIT.name = ''
        this.form.implementation.ictOfficer.name = ''
        console.log('✅ All fields cleared')

        // Map roles to approval stages
        const roleToStageMap = {
          'head_of_department': 'hod',
          'hod': 'hod',
          'head_department': 'hod',
          'divisional_director': 'divisional', 
          'director_divisional': 'divisional',
          'ict_director': 'ict_director',
          'director_ict': 'ict_director',
          'head_it': 'head_it',
          'head_of_it': 'head_it',
          'ict_officer': 'ict_officer',
          'officer_ict': 'ict_officer'
        }

        // Normalize role string for matching
        const normalizedRole = userRole.toLowerCase().replace(/[\s_-]+/g, '_')
        const mappedStage = roleToStageMap[normalizedRole]

        if (mappedStage) {
          console.log(`\u2705 Found role mapping: "${userRole}" \u2192 "${mappedStage}"`)
          
          // Check if the mapped stage is currently editable (active)
          const isStageEditable = this.isApprovalStageEditable(mappedStage)
          console.log(`\ud83d\udd0d Is ${mappedStage} stage editable?`, isStageEditable)
          
          if (isStageEditable) {
            console.log(`\ud83d\udd04 About to populate ONLY the ${mappedStage} field with: ${userName}`)
            this.populateApproverName(mappedStage)
          } else {
            console.log(`\u26a0\ufe0f Skipping ${mappedStage} field - not currently active/editable`)
          }
          
          // Verify the population worked correctly
          console.log('\ud83d\udd0d Post-population verification:')
          console.log('HOD name:', this.form.approvals.hod.name)
          console.log('Divisional Director name:', this.form.approvals.divisionalDirector.name)
          console.log('ICT Director name:', this.form.approvals.directorICT.name)
          console.log('Head IT name:', this.form.implementation.headIT.name)
          console.log('ICT Officer name:', this.form.implementation.ictOfficer.name)
        } else {
          console.log(`⚠️ No role mapping found for: "${userRole}". Available mappings:`, Object.keys(roleToStageMap))
          
          // Fallback: if user is in review mode and has edit permissions for a specific stage
          if (this.isReviewMode) {
            if (this.isHodApprovalEditable) {
              console.log('📋 Fallback: Using HOD field based on edit permissions')
              this.populateApproverName('hod')
            } else if (this.isDivisionalApprovalEditable) {
              console.log('📋 Fallback: Using Divisional field based on edit permissions')
              this.populateApproverName('divisional')
            } else if (this.isIctDirectorApprovalEditable) {
              console.log('📋 Fallback: Using ICT Director field based on edit permissions')
              this.populateApproverName('ict_director')
            } else if (this.isHeadItApprovalEditable) {
              console.log('📋 Fallback: Using Head IT field based on edit permissions')
              this.populateApproverName('head_it')
            } else if (this.isIctOfficerApprovalEditable) {
              console.log('📋 Fallback: Using ICT Officer field based on edit permissions')
              this.populateApproverName('ict_officer')
            }
          }
        }
      },

      // Helper method to check if a specific approval stage is currently editable
      isApprovalStageEditable(stage) {
        const editableMap = {
          'hod': this.isHodApprovalEditable,
          'divisional': this.isDivisionalApprovalEditable,
          'ict_director': this.isIctDirectorApprovalEditable,
          'head_it': this.isHeadItApprovalEditable,
          'ict_officer': this.isIctOfficerApprovalEditable
        }
        
        return editableMap[stage] || false
      },

      // Auto-populate approver name based on current user and approval stage
      populateApproverName(stage) {
        if (!this.currentUser || !this.currentUser.name) {
          console.log('No current user data available for auto-population', {
            hasCurrentUser: !!this.currentUser,
            currentUser: this.currentUser,
            userName: this.currentUser?.name
          })
          return
        }

        const userName = this.currentUser.name
        console.log(`Auto-populating ${stage} approver name with user from database:`, {
          stage,
          userName,
          userId: this.currentUser.id,
          userEmail: this.currentUser.email,
          userPfNumber: this.currentUser.pf_number
        })

        // Always populate the appropriate name field with authenticated user's name
        switch (stage) {
          case 'hod':
            this.form.approvals.hod.name = userName
            console.log(`✅ HOD name populated: ${userName}`)
            break
          case 'divisional':
            this.form.approvals.divisionalDirector.name = userName
            console.log(`✅ Divisional Director name populated: ${userName}`)
            break
          case 'ict_director':
            this.form.approvals.directorICT.name = userName
            console.log(`✅ ICT Director name populated: ${userName}`)
            break
          case 'head_it':
            this.form.implementation.headIT.name = userName
            console.log(`✅ Head of IT name populated: ${userName}`)
            break
          case 'ict_officer':
            this.form.implementation.ictOfficer.name = userName
            console.log(`✅ ICT Officer name populated: ${userName}`)
            break
          default:
            console.warn(`Unknown approval stage: ${stage}`)
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
