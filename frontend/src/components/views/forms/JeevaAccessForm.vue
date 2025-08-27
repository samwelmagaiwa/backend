<template>
  <div class="flex flex-col h-screen">
    <Header />
    <div class="flex flex-1 overflow-hidden">
      <ModernSidebar v-model:collapsed="sidebarCollapsed" />
      <main
        class="flex-1 p-6 bg-gradient-to-br from-blue-900 via-blue-800 to-teal-900 overflow-y-auto relative"
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
                fontSize: Math.random() * 20 + 10 + 'px',
              }"
            >
              <i
                :class="[
                  'fas',
                  [
                    'fa-heartbeat',
                    'fa-user-md',
                    'fa-hospital',
                    'fa-stethoscope',
                    'fa-plus',
                  ][Math.floor(Math.random() * 5)],
                ]"
              ></i>
            </div>
          </div>
        </div>

        <div class="max-w-12xl mx-auto relative z-10">
          <!-- Header Section -->
          <div
            class="medical-glass-card rounded-t-3xl p-8 mb-0 border-b border-blue-300/30"
          >
            <div class="flex justify-between items-center">
              <!-- Left Logo -->
              <div
                class="w-28 h-28 mr-6 transform hover:scale-110 transition-transform duration-300"
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
                  class="text-4xl font-bold text-white mb-4 tracking-wide drop-shadow-lg animate-fade-in"
                >
                  MUHIMBILI NATIONAL HOSPITAL
                </h1>
                <div class="relative inline-block mb-4">
                  <div
                    class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-10 py-4 rounded-full text-xl font-bold shadow-2xl transform hover:scale-105 transition-all duration-300 border-2 border-blue-400/60"
                  >
                    <span class="relative z-10 flex items-center gap-2">
                      <i class="fas fa-database"></i>
                      JEEVA ACCESS REQUEST
                    </span>
                    <div
                      class="absolute inset-0 bg-gradient-to-r from-blue-700 to-blue-800 rounded-full opacity-0 hover:opacity-100 transition-opacity duration-300"
                    ></div>
                  </div>
                </div>
                <h2
                  class="text-2xl font-bold text-blue-100 tracking-wide drop-shadow-md animate-fade-in-delay"
                >
                  {{
                    isReviewMode
                      ? "REQUEST REVIEW - " + requestId
                      : "SYSTEM ACCESS FORM"
                  }}
                </h2>
              </div>

              <!-- Right Logo -->
              <div
                class="w-28 h-28 ml-6 transform hover:scale-110 transition-transform duration-300"
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
              @submit.prevent="submitForm"
              :class="['p-6 space-y-4', { 'review-mode': isReviewMode }]"
            >
              <!-- Personal Information Section -->
              <div
                class="medical-card bg-gradient-to-r from-blue-600/25 to-blue-700/25 border-2 border-blue-400/40 p-3 rounded-xl backdrop-blur-sm hover:shadow-xl hover:shadow-blue-500/20 transition-all duration-500 group"
              >
                <div class="flex items-center space-x-3 mb-4">
                  <div
                    class="w-10 h-10 bg-gradient-to-br from-teal-500 to-blue-600 rounded-lg flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-teal-300/50"
                  >
                    <i class="fas fa-user-md text-white text-lg"></i>
                  </div>
                  <h3 class="text-lg font-bold text-white flex items-center">
                    <i class="fas fa-id-card mr-2 text-teal-300"></i>
                    Personal Information
                  </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                  <!-- PF Number -->
                  <div>
                    <label class="block text-sm font-bold text-blue-100 mb-1">
                      PF Number <span class="text-red-400">*</span>
                    </label>
                    <div class="relative">
                      <input
                        v-model="formData.pfNumber"
                        type="text"
                        class="medical-input w-full px-3 py-3 bg-white/15 border-2 border-blue-300/30 rounded-lg focus:border-teal-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-teal-500/20"
                        placeholder="Enter PF Number"
                        required
                      />
                      <div
                        class="absolute inset-0 rounded-lg bg-gradient-to-r from-teal-500/10 to-blue-500/10 opacity-0 hover:opacity-100 transition-opacity duration-300 pointer-events-none"
                      ></div>
                    </div>
                  </div>

                  <!-- Staff Name -->
                  <div>
                    <label class="block text-sm font-bold text-blue-100 mb-1">
                      Staff Name <span class="text-red-400">*</span>
                    </label>
                    <div class="relative">
                      <input
                        v-model="formData.staffName"
                        type="text"
                        class="medical-input w-full px-3 py-3 bg-white/15 border-2 border-blue-300/30 rounded-lg focus:border-teal-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-teal-500/20"
                        placeholder="Enter full name"
                        required
                      />
                      <div
                        class="absolute inset-0 rounded-lg bg-gradient-to-r from-teal-500/10 to-blue-500/10 opacity-0 hover:opacity-100 transition-opacity duration-300 pointer-events-none"
                      ></div>
                    </div>
                  </div>

                  <!-- Department -->
                  <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-blue-100 mb-1">
                      Department <span class="text-red-400">*</span>
                    </label>
                    <div class="relative">
                      <select
                        v-model="formData.department_id"
                        class="medical-input w-full px-3 py-3 bg-white/15 border-2 border-blue-300/30 rounded-lg focus:border-teal-400 focus:outline-none text-white backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-teal-500/20 appearance-none cursor-pointer"
                        required
                        :disabled="departmentsLoading"
                      >
                        <option
                          value=""
                          disabled
                          class="bg-gray-800 text-gray-300"
                        >
                          {{
                            departmentsLoading
                              ? "Loading departments..."
                              : "Select your department"
                          }}
                        </option>
                        <option
                          v-for="dept in departments"
                          :key="dept.id"
                          :value="dept.id"
                          class="bg-gray-800 text-white"
                        >
                          {{ dept.name }}
                          {{ dept.code ? `(${dept.code})` : "" }}
                        </option>
                      </select>
                      <div
                        class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none"
                      >
                        <i
                          v-if="departmentsLoading"
                          class="fas fa-spinner fa-spin text-blue-300"
                        ></i>
                        <i v-else class="fas fa-chevron-down text-blue-300"></i>
                      </div>
                      <div
                        class="absolute inset-0 rounded-lg bg-gradient-to-r from-teal-500/10 to-blue-500/10 opacity-0 hover:opacity-100 transition-opacity duration-300 pointer-events-none"
                      ></div>
                    </div>
                    <!-- Error message for departments -->
                    <div
                      v-if="departmentsError"
                      class="mt-2 text-red-400 text-sm flex items-center"
                    >
                      <i class="fas fa-exclamation-triangle mr-2"></i>
                      {{ departmentsError }}
                      <button
                        @click="fetchDepartments"
                        class="ml-2 text-blue-400 hover:text-blue-300 underline"
                        type="button"
                      >
                        Retry
                      </button>
                    </div>
                  </div>

                  <!-- Digital Signature -->
                  <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-blue-100 mb-1">
                      Digital Signature <span class="text-red-400">*</span>
                    </label>

                    <div class="relative">
                      <div
                        v-if="!signaturePreview"
                        class="w-full px-3 py-3 border-2 border-dashed border-teal-300/40 rounded-lg focus-within:border-teal-400 bg-white/15 backdrop-blur-sm transition-all duration-300 shadow-lg hover:shadow-xl hover:shadow-teal-500/20 min-h-[80px] flex items-center justify-center hover:bg-white/20"
                      >
                        <div class="text-center">
                          <div class="mb-2">
                            <i
                              class="fas fa-signature text-teal-300 text-2xl mb-1"
                            ></i>
                            <p class="text-blue-100 text-xs">
                              No signature uploaded
                            </p>
                          </div>
                          <button
                            type="button"
                            @click="triggerFileUpload"
                            class="px-3 py-1.5 bg-gradient-to-r from-teal-500 to-blue-600 text-white text-xs font-semibold rounded-lg hover:from-teal-600 hover:to-blue-700 transition-all duration-300 flex items-center gap-1 mx-auto shadow-lg hover:shadow-xl transform hover:scale-105 border border-teal-400/50"
                          >
                            <i class="fas fa-upload"></i>
                            Load Signature
                          </button>
                        </div>
                      </div>

                      <div
                        v-else
                        class="w-full px-3 py-3 border-2 border-teal-300/40 rounded-lg bg-white/15 backdrop-blur-sm transition-all duration-300 shadow-lg hover:shadow-xl min-h-[80px] flex items-center justify-center relative"
                      >
                        <div
                          v-if="isImage(signaturePreview)"
                          class="text-center"
                        >
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
                            class="w-10 h-10 bg-red-500/20 rounded-lg flex items-center justify-center mx-auto mb-1"
                          >
                            <i class="fas fa-file-pdf text-red-400 text-lg"></i>
                          </div>
                          <p class="text-xs text-blue-100">
                            {{ signatureFileName }}
                          </p>
                        </div>

                        <div class="absolute top-1 right-1 flex gap-1">
                          <button
                            type="button"
                            @click="triggerFileUpload"
                            class="w-6 h-6 bg-teal-500 text-white rounded-full flex items-center justify-center text-xs hover:bg-teal-600 transition-colors duration-200 shadow-lg"
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

                      <input
                        ref="signatureInput"
                        type="file"
                        accept="image/png,image/jpeg,image/jpg,application/pdf"
                        @change="onSignatureChange"
                        class="hidden"
                      />
                    </div>
                  </div>
                </div>
              </div>

              <!-- Access Request Section -->
              <div
                class="medical-card bg-gradient-to-r from-blue-600/25 to-blue-700/25 border-2 border-blue-400/40 p-4 rounded-xl backdrop-blur-sm hover:shadow-xl hover:shadow-blue-500/20 transition-all duration-500 group"
              >
                <div class="flex items-center space-x-4 mb-4">
                  <div
                    class="w-14 h-14 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-blue-300/50"
                  >
                    <i class="fas fa-database text-white text-xl"></i>
                  </div>
                  <h3 class="text-xl font-bold text-white flex items-center">
                    <i class="fas fa-list-alt mr-2 text-blue-300"></i>
                    Module Request
                  </h3>
                </div>

                <!-- Request Type -->
                <div class="mb-4">
                  <label
                    class="block text-sm font-bold text-blue-100 mb-2 text-center"
                  >
                    Module Requested for <span class="text-red-400">*</span>
                  </label>
                  <div class="flex justify-center gap-4 mb-3">
                    <label
                      class="flex items-center cursor-pointer p-3 border-2 border-blue-300/30 rounded-lg hover:border-blue-400 hover:bg-white/10 transition-all backdrop-blur-sm"
                    >
                      <input
                        v-model="formData.requestType"
                        type="radio"
                        value="use"
                        class="w-4 h-4 text-blue-600 border-blue-300 focus:ring-blue-500 mr-2 module-request-editable"
                      />
                      <span class="font-medium text-white">Use</span>
                    </label>
                    <label
                      class="flex items-center cursor-pointer p-3 border-2 border-blue-300/30 rounded-lg hover:border-blue-400 hover:bg-white/10 transition-all backdrop-blur-sm"
                    >
                      <input
                        v-model="formData.requestType"
                        type="radio"
                        value="revoke"
                        class="w-4 h-4 text-red-600 border-blue-300 focus:ring-red-500 mr-2 module-request-editable"
                      />
                      <span class="font-medium text-white">Revoke</span>
                    </label>
                  </div>
                </div>

                <!-- Modules Selection -->
                <div>
                  <label class="block text-sm font-bold text-blue-100 mb-2">
                    Select Modules <span class="text-red-400">*</span>
                  </label>
                  <div
                    class="bg-white/15 rounded-lg p-3 max-h-80 border-2 border-blue-300/30 backdrop-blur-sm overflow-y-auto"
                  >
                    <div
                      class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-1"
                    >
                      <label
                        v-for="module in modules"
                        :key="module.id"
                        class="flex items-center p-1.5 hover:bg-white/20 rounded-md cursor-pointer transition-all duration-300 border border-blue-300/20 hover:border-blue-400/40"
                      >
                        <input
                          v-model="formData.selectedModules"
                          type="checkbox"
                          :value="module.id"
                          class="w-3 h-3 text-blue-600 border-blue-300 rounded focus:ring-blue-500 mr-1.5 module-request-editable"
                        />
                        <span class="text-xs font-medium text-white">{{
                          module.name
                        }}</span>
                      </label>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Access Rights Section -->
              <div
                class="medical-card bg-gradient-to-r from-blue-600/25 to-blue-700/25 border-2 border-blue-400/40 p-4 rounded-xl backdrop-blur-sm hover:shadow-xl hover:shadow-blue-500/20 transition-all duration-500 group"
              >
                <div class="flex items-center space-x-4 mb-4">
                  <div
                    class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-blue-300/50"
                  >
                    <i class="fas fa-lock text-white text-xl"></i>
                  </div>
                  <h3 class="text-xl font-bold text-white flex items-center">
                    <i class="fas fa-key mr-2 text-blue-300"></i>
                    Access Rights
                  </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <!-- Permanent Option -->
                  <div
                    class="flex items-center p-4 border-2 border-blue-300/30 rounded-xl hover:border-cyan-400 hover:bg-white/10 transition-all backdrop-blur-sm"
                  >
                    <input
                      v-model="formData.accessType"
                      type="radio"
                      value="permanent"
                      class="w-4 h-4 text-cyan-600 border-blue-300 focus:ring-cyan-500 mr-3 access-rights-editable"
                    />
                    <span class="font-medium text-white text-sm"
                      >Permanent (until retirement)</span
                    >
                  </div>

                  <!-- Temporary Until Option -->
                  <div
                    class="flex items-center justify-between p-4 border-2 border-blue-300/30 rounded-xl hover:border-cyan-400 hover:bg-white/10 transition-all backdrop-blur-sm"
                  >
                    <div class="flex items-center">
                      <input
                        v-model="formData.accessType"
                        type="radio"
                        value="temporary"
                        class="w-4 h-4 text-cyan-600 border-blue-300 focus:ring-cyan-500 mr-3 access-rights-editable"
                      />
                      <span class="font-medium text-white text-sm"
                        >Temporary Until</span
                      >
                    </div>

                    <div
                      class="flex items-center gap-1"
                      v-if="formData.accessType === 'temporary'"
                    >
                      <input
                        v-model="formData.tempDate.day"
                        type="text"
                        placeholder="DD"
                        maxlength="2"
                        class="w-9 px-1 py-1 border border-blue-300/30 rounded text-center text-xs focus:border-cyan-500 focus:outline-none bg-white/15 text-white access-rights-editable"
                      />
                      <span class="text-blue-200">/</span>
                      <input
                        v-model="formData.tempDate.month"
                        type="text"
                        placeholder="MM"
                        maxlength="2"
                        class="w-9 px-1 py-1 border border-blue-300/30 rounded text-center text-xs focus:border-cyan-500 focus:outline-none bg-white/15 text-white access-rights-editable"
                      />
                      <span class="text-blue-200">/</span>
                      <input
                        v-model="formData.tempDate.year"
                        type="text"
                        placeholder="YYYY"
                        maxlength="4"
                        class="w-14 px-1 py-1 border border-blue-300/30 rounded text-center text-xs focus:border-cyan-500 focus:outline-none bg-white/15 text-white access-rights-editable"
                      />
                    </div>
                  </div>
                </div>
              </div>

              <!-- Approval Section -->
              <div
                class="medical-card bg-gradient-to-r from-blue-600/25 to-blue-700/25 border-2 border-blue-400/40 p-4 rounded-xl backdrop-blur-sm hover:shadow-xl hover:shadow-blue-500/20 transition-all duration-500 group"
              >
                <div class="flex items-center space-x-4 mb-4">
                  <div
                    class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-blue-300/50"
                  >
                    <i class="fas fa-check-circle text-white text-xl"></i>
                  </div>
                  <h3 class="text-xl font-bold text-white flex items-center">
                    <i class="fas fa-stamp mr-2 text-blue-300"></i>
                    Approval
                  </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                  <!-- HoD/BM -->
                  <div
                    class="bg-white/15 rounded-xl p-4 border border-emerald-300/30 backdrop-blur-sm"
                  >
                    <h5 class="font-bold text-white mb-3 text-center text-sm">
                      HoD/BM
                    </h5>
                    <div class="space-y-3">
                      <input
                        v-model="formData.approvals.hod.name"
                        type="text"
                        placeholder="Name"
                        class="w-full px-3 py-2 border border-emerald-300/30 rounded-lg focus:border-emerald-400 focus:outline-none text-sm bg-white/15 text-white placeholder-blue-200/60 backdrop-blur-sm hod-approval-editable"
                      />
                      <div class="relative">
                        <div
                          v-if="!hodSignaturePreview"
                          class="w-full px-2 py-2 border border-emerald-300/30 rounded-lg focus-within:border-emerald-400 bg-white/15 transition-all duration-300 shadow-sm hover:shadow-md min-h-[50px] flex items-center justify-center backdrop-blur-sm"
                        >
                          <div class="text-center">
                            <div class="mb-1">
                              <i
                                class="fas fa-signature text-emerald-300 text-sm mb-1"
                              ></i>
                              <p class="text-blue-100 text-xs">No signature</p>
                            </div>
                            <button
                              type="button"
                              @click="triggerHodSignatureUpload"
                              class="px-2 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700 transition-colors duration-200 flex items-center gap-1 mx-auto"
                            >
                              <i class="fas fa-upload"></i>
                              Press to load your signature
                            </button>
                          </div>
                        </div>

                        <div
                          v-else
                          class="w-full px-2 py-2 border border-emerald-300/30 rounded-lg bg-white/15 transition-all duration-300 shadow-sm hover:shadow-md min-h-[50px] flex items-center justify-center relative backdrop-blur-sm"
                        >
                          <div
                            v-if="isImage(hodSignaturePreview)"
                            class="text-center"
                          >
                            <img
                              :src="hodSignaturePreview"
                              alt="HOD Signature"
                              class="max-h-[35px] max-w-full object-contain mx-auto mb-1"
                            />
                            <p class="text-xs text-blue-100">
                              {{ hodSignatureFileName }}
                            </p>
                          </div>
                          <div v-else class="text-center">
                            <div
                              class="w-8 h-8 bg-red-500/20 rounded flex items-center justify-center mx-auto mb-1"
                            >
                              <i
                                class="fas fa-file-pdf text-red-400 text-sm"
                              ></i>
                            </div>
                            <p class="text-xs text-blue-100">
                              {{ hodSignatureFileName }}
                            </p>
                          </div>

                          <div class="absolute top-1 right-1 flex gap-1">
                            <button
                              type="button"
                              @click="triggerHodSignatureUpload"
                              class="w-5 h-5 bg-emerald-500 text-white rounded-full flex items-center justify-center text-xs hover:bg-emerald-600 transition-colors duration-200"
                              title="Change signature"
                            >
                              <i class="fas fa-edit"></i>
                            </button>
                            <button
                              type="button"
                              @click="clearHodSignature"
                              class="w-5 h-5 bg-red-500 text-white rounded-full flex items-center justify-center text-xs hover:bg-red-600 transition-colors duration-200"
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
                      <input
                        v-model="formData.approvals.hod.date"
                        type="date"
                        class="w-full px-3 py-2 border border-emerald-300/30 rounded-lg focus:border-emerald-400 focus:outline-none text-sm bg-white/15 text-white backdrop-blur-sm hod-approval-editable"
                      />
                    </div>
                  </div>

                  <!-- Divisional Director -->
                  <div
                    class="bg-white/15 rounded-xl p-4 border border-emerald-300/30 backdrop-blur-sm"
                  >
                    <h5 class="font-bold text-white mb-3 text-center text-sm">
                      Divisional Director
                    </h5>
                    <div class="space-y-3">
                      <!-- Decision Section for Divisional Director -->
                      <div
                        v-if="
                          userRole === ROLES.DIVISIONAL_DIRECTOR &&
                          canApproveAtStage()
                        "
                        class="mb-4"
                      >
                        <label
                          class="block text-sm font-bold text-blue-100 mb-2"
                        >
                          Decision <span class="text-red-400">*</span>
                        </label>
                        <div class="flex gap-3 mb-3">
                          <label
                            class="flex items-center cursor-pointer p-3 border-2 border-blue-300/30 rounded-lg hover:border-emerald-400 hover:bg-white/10 transition-all backdrop-blur-sm"
                          >
                            <input
                              v-model="
                                formData.approvals.divisionalDirector.decision
                              "
                              type="radio"
                              value="approve"
                              class="w-4 h-4 text-emerald-600 border-blue-300 focus:ring-emerald-500 mr-2 divisional-approval-editable"
                            />
                            <span class="font-medium text-white text-sm"
                              >Approve</span
                            >
                          </label>
                          <label
                            class="flex items-center cursor-pointer p-3 border-2 border-blue-300/30 rounded-lg hover:border-red-400 hover:bg-white/10 transition-all backdrop-blur-sm"
                          >
                            <input
                              v-model="
                                formData.approvals.divisionalDirector.decision
                              "
                              type="radio"
                              value="reject"
                              class="w-4 h-4 text-red-600 border-blue-300 focus:ring-red-500 mr-2 divisional-approval-editable"
                            />
                            <span class="font-medium text-white text-sm"
                              >Reject</span
                            >
                          </label>
                        </div>

                        <!-- Rejection Comments (mandatory when rejecting) -->
                        <div
                          v-if="
                            formData.approvals.divisionalDirector.decision ===
                            'reject'
                          "
                          class="mb-3"
                        >
                          <label
                            class="block text-sm font-bold text-blue-100 mb-2"
                          >
                            Reason for Rejection
                            <span class="text-red-400">*</span>
                          </label>
                          <textarea
                            v-model="
                              formData.approvals.divisionalDirector
                                .rejectionComments
                            "
                            rows="3"
                            class="w-full px-3 py-2 border border-red-300/30 rounded-lg focus:border-red-400 focus:outline-none text-sm bg-white/15 text-white placeholder-blue-200/60 backdrop-blur-sm resize-y divisional-comments-editable"
                            placeholder="Please provide a detailed reason for rejecting this request..."
                            required
                          ></textarea>
                        </div>
                      </div>

                      <input
                        v-model="formData.approvals.divisionalDirector.name"
                        type="text"
                        placeholder="Name"
                        class="w-full px-3 py-2 border border-emerald-300/30 rounded-lg focus:border-emerald-400 focus:outline-none text-sm bg-white/15 text-white placeholder-blue-200/60 backdrop-blur-sm divisional-approval-editable"
                      />
                      <div class="relative">
                        <div
                          v-if="!divDirectorSignaturePreview"
                          class="w-full px-2 py-2 border border-emerald-300/30 rounded-lg focus-within:border-emerald-400 bg-white/15 transition-all duration-300 shadow-sm hover:shadow-md min-h-[50px] flex items-center justify-center backdrop-blur-sm"
                        >
                          <div class="text-center">
                            <div class="mb-1">
                              <i
                                class="fas fa-signature text-emerald-300 text-sm mb-1"
                              ></i>
                              <p class="text-blue-100 text-xs">No signature</p>
                            </div>
                            <button
                              type="button"
                              @click="triggerDivDirectorSignatureUpload"
                              class="px-2 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700 transition-colors duration-200 flex items-center gap-1 mx-auto"
                            >
                              <i class="fas fa-upload"></i>
                              Press to load your signature
                            </button>
                          </div>
                        </div>

                        <div
                          v-else
                          class="w-full px-2 py-2 border border-emerald-300/30 rounded-lg bg-white/15 transition-all duration-300 shadow-sm hover:shadow-md min-h-[50px] flex items-center justify-center relative backdrop-blur-sm"
                        >
                          <div
                            v-if="isImage(divDirectorSignaturePreview)"
                            class="text-center"
                          >
                            <img
                              :src="divDirectorSignaturePreview"
                              alt="Divisional Director Signature"
                              class="max-h-[35px] max-w-full object-contain mx-auto mb-1"
                            />
                            <p class="text-xs text-blue-100">
                              {{ divDirectorSignatureFileName }}
                            </p>
                          </div>
                          <div v-else class="text-center">
                            <div
                              class="w-8 h-8 bg-red-500/20 rounded flex items-center justify-center mx-auto mb-1"
                            >
                              <i
                                class="fas fa-file-pdf text-red-400 text-sm"
                              ></i>
                            </div>
                            <p class="text-xs text-blue-100">
                              {{ divDirectorSignatureFileName }}
                            </p>
                          </div>

                          <div class="absolute top-1 right-1 flex gap-1">
                            <button
                              type="button"
                              @click="triggerDivDirectorSignatureUpload"
                              class="w-5 h-5 bg-emerald-500 text-white rounded-full flex items-center justify-center text-xs hover:bg-emerald-600 transition-colors duration-200"
                              title="Change signature"
                            >
                              <i class="fas fa-edit"></i>
                            </button>
                            <button
                              type="button"
                              @click="clearDivDirectorSignature"
                              class="w-5 h-5 bg-red-500 text-white rounded-full flex items-center justify-center text-xs hover:bg-red-600 transition-colors duration-200"
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
                      <input
                        v-model="formData.approvals.divisionalDirector.date"
                        type="date"
                        class="w-full px-3 py-2 border border-emerald-300/30 rounded-lg focus:border-emerald-400 focus:outline-none text-sm bg-white/15 text-white backdrop-blur-sm divisional-approval-editable"
                      />
                    </div>
                  </div>

                  <!-- Director of ICT -->
                  <div
                    class="bg-white/15 rounded-xl p-4 border border-emerald-300/30 backdrop-blur-sm"
                  >
                    <h5 class="font-bold text-white mb-3 text-center text-sm">
                      Director of ICT
                    </h5>
                    <div class="space-y-3">
                      <input
                        v-model="formData.approvals.directorICT.name"
                        type="text"
                        placeholder="Name"
                        class="w-full px-3 py-2 border border-emerald-300/30 rounded-lg focus:border-emerald-400 focus:outline-none text-sm bg-white/15 text-white placeholder-blue-200/60 backdrop-blur-sm"
                      />
                      <div class="relative">
                        <div
                          v-if="!directorICTSignaturePreview"
                          class="w-full px-2 py-2 border border-emerald-300/30 rounded-lg focus-within:border-emerald-400 bg-white/15 transition-all duration-300 shadow-sm hover:shadow-md min-h-[50px] flex items-center justify-center backdrop-blur-sm"
                        >
                          <div class="text-center">
                            <div class="mb-1">
                              <i
                                class="fas fa-signature text-emerald-300 text-sm mb-1"
                              ></i>
                              <p class="text-blue-100 text-xs">No signature</p>
                            </div>
                            <button
                              type="button"
                              @click="triggerDirectorICTSignatureUpload"
                              class="px-2 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700 transition-colors duration-200 flex items-center gap-1 mx-auto"
                            >
                              <i class="fas fa-upload"></i>
                              Press to load your signature
                            </button>
                          </div>
                        </div>

                        <div
                          v-else
                          class="w-full px-2 py-2 border border-emerald-300/30 rounded-lg bg-white/15 transition-all duration-300 shadow-sm hover:shadow-md min-h-[50px] flex items-center justify-center relative backdrop-blur-sm"
                        >
                          <div
                            v-if="isImage(directorICTSignaturePreview)"
                            class="text-center"
                          >
                            <img
                              :src="directorICTSignaturePreview"
                              alt="Director ICT Signature"
                              class="max-h-[35px] max-w-full object-contain mx-auto mb-1"
                            />
                            <p class="text-xs text-blue-100">
                              {{ directorICTSignatureFileName }}
                            </p>
                          </div>
                          <div v-else class="text-center">
                            <div
                              class="w-8 h-8 bg-red-500/20 rounded flex items-center justify-center mx-auto mb-1"
                            >
                              <i
                                class="fas fa-file-pdf text-red-400 text-sm"
                              ></i>
                            </div>
                            <p class="text-xs text-blue-100">
                              {{ directorICTSignatureFileName }}
                            </p>
                          </div>

                          <div class="absolute top-1 right-1 flex gap-1">
                            <button
                              type="button"
                              @click="triggerDirectorICTSignatureUpload"
                              class="w-5 h-5 bg-emerald-500 text-white rounded-full flex items-center justify-center text-xs hover:bg-emerald-600 transition-colors duration-200"
                              title="Change signature"
                            >
                              <i class="fas fa-edit"></i>
                            </button>
                            <button
                              type="button"
                              @click="clearDirectorICTSignature"
                              class="w-5 h-5 bg-red-500 text-white rounded-full flex items-center justify-center text-xs hover:bg-red-600 transition-colors duration-200"
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
                      <input
                        v-model="formData.approvals.directorICT.date"
                        type="date"
                        class="w-full px-3 py-2 border border-emerald-300/30 rounded-lg focus:border-emerald-400 focus:outline-none text-sm bg-white/15 text-white backdrop-blur-sm"
                      />
                    </div>
                  </div>
                </div>
              </div>

              <!-- Comments Section -->
              <div
                class="medical-card bg-gradient-to-r from-blue-600/25 to-blue-700/25 border-2 border-blue-400/40 p-4 rounded-xl backdrop-blur-sm hover:shadow-xl hover:shadow-blue-500/20 transition-all duration-500 group"
              >
                <div class="flex items-center space-x-4 mb-4">
                  <div
                    class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-blue-300/50"
                  >
                    <i class="fas fa-comments text-white text-xl"></i>
                  </div>
                  <h3 class="text-xl font-bold text-white flex items-center">
                    <i class="fas fa-comment-alt mr-2 text-blue-300"></i>
                    Comments
                  </h3>
                </div>
                <div class="relative">
                  <textarea
                    v-model="formData.comments"
                    rows="4"
                    class="medical-input w-full px-4 py-4 bg-white/15 border-2 border-blue-300/30 rounded-xl focus:border-indigo-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-indigo-500/20 resize-y comments-editable"
                    placeholder="HOD: specify access Category here..."
                  ></textarea>
                  <div
                    class="absolute inset-0 rounded-xl bg-gradient-to-r from-indigo-500/10 to-blue-500/10 opacity-0 hover:opacity-100 transition-opacity duration-300 pointer-events-none"
                  ></div>
                </div>
              </div>

              <!-- Implementation Section -->
              <div
                class="medical-card bg-gradient-to-r from-blue-600/25 to-blue-700/25 border-2 border-blue-400/40 p-4 rounded-xl backdrop-blur-sm hover:shadow-xl hover:shadow-blue-500/20 transition-all duration-500 group"
              >
                <div class="flex items-center space-x-4 mb-4">
                  <div
                    class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-blue-300/50"
                  >
                    <i class="fas fa-cogs text-white text-xl"></i>
                  </div>
                  <h3 class="text-xl font-bold text-white flex items-center">
                    <i class="fas fa-tools mr-2 text-blue-300"></i>
                    For Implementation
                  </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <!-- Head of IT -->
                  <div
                    class="bg-white/15 rounded-xl p-4 border border-purple-300/30 backdrop-blur-sm"
                  >
                    <h5 class="font-bold text-white mb-3 text-center text-sm">
                      Head of IT
                    </h5>
                    <div class="space-y-3">
                      <input
                        v-model="formData.implementation.headIT.name"
                        type="text"
                        placeholder="Name"
                        class="w-full px-3 py-2 border border-purple-300/30 rounded-lg focus:border-purple-400 focus:outline-none text-sm bg-white/15 text-white placeholder-blue-200/60 backdrop-blur-sm"
                      />
                      <div class="relative">
                        <div
                          v-if="!headITSignaturePreview"
                          class="w-full px-2 py-2 border border-purple-300/30 rounded-lg focus-within:border-purple-400 bg-white/15 transition-all duration-300 shadow-sm hover:shadow-md min-h-[50px] flex items-center justify-center backdrop-blur-sm"
                        >
                          <div class="text-center">
                            <div class="mb-1">
                              <i
                                class="fas fa-signature text-purple-300 text-sm mb-1"
                              ></i>
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
                          class="w-full px-2 py-2 border border-purple-300/30 rounded-lg bg-white/15 transition-all duration-300 shadow-sm hover:shadow-md min-h-[50px] flex items-center justify-center relative backdrop-blur-sm"
                        >
                          <div
                            v-if="isImage(headITSignaturePreview)"
                            class="text-center"
                          >
                            <img
                              :src="headITSignaturePreview"
                              alt="Head IT Signature"
                              class="max-h-[35px] max-w-full object-contain mx-auto mb-1"
                            />
                            <p class="text-xs text-blue-100">
                              {{ headITSignatureFileName }}
                            </p>
                          </div>
                          <div v-else class="text-center">
                            <div
                              class="w-8 h-8 bg-red-500/20 rounded flex items-center justify-center mx-auto mb-1"
                            >
                              <i
                                class="fas fa-file-pdf text-red-400 text-sm"
                              ></i>
                            </div>
                            <p class="text-xs text-blue-100">
                              {{ headITSignatureFileName }}
                            </p>
                          </div>

                          <div class="absolute top-1 right-1 flex gap-1">
                            <button
                              type="button"
                              @click="triggerHeadITSignatureUpload"
                              class="w-5 h-5 bg-purple-500 text-white rounded-full flex items-center justify-center text-xs hover:bg-purple-600 transition-colors duration-200"
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
                      <input
                        v-model="formData.implementation.headIT.Date"
                        type="date"
                        class="w-full px-3 py-2 border border-purple-300/30 rounded-lg focus:border-purple-400 focus:outline-none text-sm bg-white/15 text-white backdrop-blur-sm"
                      />
                    </div>
                  </div>

                  <!-- ICT Officer -->
                  <div
                    class="bg-white/15 rounded-xl p-4 border border-purple-300/30 backdrop-blur-sm"
                  >
                    <h5 class="font-bold text-white mb-3 text-center text-sm">
                      ICT Officer granting access
                    </h5>
                    <div class="space-y-3">
                      <input
                        v-model="formData.implementation.ictOfficer.name"
                        type="text"
                        placeholder="Name"
                        class="w-full px-3 py-2 border border-purple-300/30 rounded-lg focus:border-purple-400 focus:outline-none text-sm bg-white/15 text-white placeholder-blue-200/60 backdrop-blur-sm"
                      />
                      <div class="relative">
                        <div
                          v-if="!ictOfficerSignaturePreview"
                          class="w-full px-2 py-2 border border-purple-300/30 rounded-lg focus-within:border-purple-400 bg-white/15 transition-all duration-300 shadow-sm hover:shadow-md min-h-[50px] flex items-center justify-center backdrop-blur-sm"
                        >
                          <div class="text-center">
                            <div class="mb-1">
                              <i
                                class="fas fa-signature text-purple-300 text-sm mb-1"
                              ></i>
                              <p class="text-blue-100 text-xs">No signature</p>
                            </div>
                            <button
                              type="button"
                              @click="triggerIctOfficerSignatureUpload"
                              class="px-2 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700 transition-colors duration-200 flex items-center gap-1 mx-auto"
                            >
                              <i class="fas fa-upload"></i>
                              Press to load your signature
                            </button>
                          </div>
                        </div>

                        <div
                          v-else
                          class="w-full px-2 py-2 border border-purple-300/30 rounded-lg bg-white/15 transition-all duration-300 shadow-sm hover:shadow-md min-h-[50px] flex items-center justify-center relative backdrop-blur-sm"
                        >
                          <div
                            v-if="isImage(ictOfficerSignaturePreview)"
                            class="text-center"
                          >
                            <img
                              :src="ictOfficerSignaturePreview"
                              alt="ICT Officer Signature"
                              class="max-h-[35px] max-w-full object-contain mx-auto mb-1"
                            />
                            <p class="text-xs text-blue-100">
                              {{ ictOfficerSignatureFileName }}
                            </p>
                          </div>
                          <div v-else class="text-center">
                            <div
                              class="w-8 h-8 bg-red-500/20 rounded flex items-center justify-center mx-auto mb-1"
                            >
                              <i
                                class="fas fa-file-pdf text-red-400 text-sm"
                              ></i>
                            </div>
                            <p class="text-xs text-blue-100">
                              {{ ictOfficerSignatureFileName }}
                            </p>
                          </div>

                          <div class="absolute top-1 right-1 flex gap-1">
                            <button
                              type="button"
                              @click="triggerIctOfficerSignatureUpload"
                              class="w-5 h-5 bg-purple-500 text-white rounded-full flex items-center justify-center text-xs hover:bg-purple-600 transition-colors duration-200"
                              title="Change signature"
                            >
                              <i class="fas fa-edit"></i>
                            </button>
                            <button
                              type="button"
                              @click="clearIctOfficerSignature"
                              class="w-5 h-5 bg-red-500 text-white rounded-full flex items-center justify-center text-xs hover:bg-red-600 transition-colors duration-200"
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
                      <input
                        v-model="formData.implementation.ictOfficer.Date"
                        type="date"
                        class="w-full px-3 py-2 border border-purple-300/30 rounded-lg focus:border-purple-400 focus:outline-none text-sm bg-white/15 text-white backdrop-blur-sm"
                      />
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
                <div
                  class="mb-6 p-4 bg-white/10 rounded-xl border border-emerald-300/30"
                >
                  <h4 class="text-sm font-bold text-blue-100 mb-3">
                    Request Status
                  </h4>
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
              <div v-if="!isReviewMode" class="border-t-3 border-gray-200 pt-1">
                <div class="text-center mb-3">
                  <div class="inline-block text-black px-4 py-2 rounded-lg">
                    <p class="font-bold">Directorate of ICT</p>
                    <p class="text-sm opacity-90">
                      IT and Telephone Department
                    </p>
                  </div>
                </div>

                <div class="flex justify-between items-center">
                  <button
                    type="button"
                    @click="resetForm"
                    class="px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-all duration-300 font-semibold flex items-center shadow-lg hover:shadow-xl"
                  >
                    <svg
                      class="w-4 h-4 mr-2"
                      fill="currentColor"
                      viewBox="0 0 20 20"
                    >
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
                    class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-300 font-semibold flex items-center shadow-lg hover:shadow-xl"
                  >
                    <svg
                      class="w-4 h-4 mr-2"
                      fill="currentColor"
                      viewBox="0 0 20 20"
                    >
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
            </form>

            <!-- Footer -->
            <AppFooter />
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import Header from '@/components/header.vue'
import ModernSidebar from '@/components/ModernSidebar.vue'
import AppFooter from '@/components/footer.vue'
import { useAuth } from '@/composables/useAuth'
import { useDepartments } from '@/composables/useApi'

export default {
  name: 'JeevaAccessForm',
  components: {
    Header,
    ModernSidebar,
    AppFooter
  },
  setup() {
    const route = useRoute()
    const router = useRouter()
    const { userRole, ROLES } = useAuth()
    const sidebarCollapsed = ref(false)
    const isReviewMode = ref(false)
    const requestId = ref('')

    // Use departments composable
    const {
      departments,
      loading: departmentsLoading,
      error: departmentsError,
      fetchDepartments
    } = useDepartments()

    // Fetch departments when component mounts
    onMounted(() => {
      fetchDepartments()
    })

    return {
      sidebarCollapsed,
      isReviewMode,
      requestId,
      route,
      router,
      userRole,
      ROLES,
      departments,
      departmentsLoading,
      departmentsError,
      fetchDepartments
    }
  },
  mounted() {
    this.checkReviewMode()
  },
  data() {
    return {
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

      formData: {
        pfNumber: '',
        staffName: '',
        department_id: '',
        signature: null,
        requestType: '',
        selectedModules: [],
        accessType: '',
        tempDate: {
          day: '',
          month: '',
          year: ''
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
            date: '',
            decision: '',
            rejectionComments: ''
          },
          directorICT: {
            name: '',
            signatureDate: ''
          }
        },
        comments: '',
        implementation: {
          headIT: {
            name: '',
            signatureDate: ''
          },
          ictOfficer: {
            name: '',
            signatureDate: ''
          }
        }
      },
      modules: [
        { id: 'financial_accounting', name: 'FINANCIAL ACCOUNTING' },
        { id: 'doctor_consultation', name: 'DOCTOR CONSULTATION' },
        { id: 'medical_records', name: 'MEDICAL RECORDS' },
        { id: 'outpatient', name: 'OUTPATIENT' },
        { id: 'nursing_station', name: 'NURSING STATION' },
        { id: 'inpatient', name: 'INPATIENT' },
        { id: 'ip_cashier', name: 'IP CASHIER' },
        { id: 'hiv', name: 'HIV' },
        { id: 'linen_laundry', name: 'LINEN & LAUNDRY' },
        { id: 'fixed_assets', name: 'FIXED ASSETS' },
        { id: 'pmtct', name: 'PMTCT' },
        { id: 'pharmacy', name: 'PHARMACY' },
        { id: 'bill_note', name: 'BILL NOTE' },
        { id: 'blood_bank', name: 'BLOOD BANK' },
        { id: 'order_management', name: 'ORDER MANAGEMENT' },
        { id: 'private_credit', name: 'PRIVATE CREDIT' },
        { id: 'laboratory', name: 'LABORATORY' },
        { id: 'general_store', name: 'GENERAL STORE' },
        { id: 'ip_billing', name: 'IP BILLING' },
        { id: 'radiology', name: 'RADIOLOGY' },
        { id: 'purchase', name: 'PURCHASE' },
        { id: 'scrolling', name: 'SCROLLING' },
        { id: 'operation_theatre', name: 'OPERATION THEATRE' },
        { id: 'cssd', name: 'CSSD' },
        { id: 'web_indent', name: 'WEB INDENT' },
        { id: 'mortuary', name: 'MORTUARY' },
        { id: 'general_maintenance', name: 'GENERAL MAINTENANCE' },
        { id: 'personnel', name: 'PERSONNEL' },
        { id: 'maintenance', name: 'MAINTENANCE' },
        { id: 'payroll', name: 'PAYROLL' },
        { id: 'cms', name: 'CMS' },
        { id: 'mis_statistics', name: 'MIS STATISTICS' }
      ]
    }
  },
  methods: {
    submitForm() {
      // Validate required fields
      if (
        !this.formData.pfNumber ||
        !this.formData.staffName ||
        !this.formData.department_id
      ) {
        this.showNotification(
          'Please fill in all required fields (PF Number, Staff Name, and Department)'
        )
        return
      }

      if (!this.formData.requestType) {
        this.showNotification(
          'Please select whether this is for Use or Revoke'
        )
        return
      }

      if (this.formData.selectedModules.length === 0) {
        this.showNotification('Please select at least one module')
        return
      }

      if (!this.formData.accessType) {
        this.showNotification('Please specify the access rights type')
        return
      }

      // If temporary access, validate date
      if (this.formData.accessType === 'temporary') {
        if (
          !this.formData.tempDate.day ||
          !this.formData.tempDate.month ||
          !this.formData.tempDate.year
        ) {
          this.showNotification(
            'Please provide a complete date for temporary access'
          )
          return
        }
      }

      // Here you would typically send the data to your backend
      console.log('Form submitted:', this.formData)
      this.showNotification('Jeeva Access Request submitted successfully!')

      // Optionally reset form after submission
      // this.resetForm()
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
      const allowedTypes = [
        'image/png',
        'image/jpeg',
        'image/jpg',
        'application/pdf'
      ]
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
      this.formData.signature = null
      this.signaturePreview = ''
      this.signatureFileName = ''
      if (this.$refs.signatureInput) {
        this.$refs.signatureInput.value = ''
      }
    },

    // HOD Signature methods
    triggerHodSignatureUpload() {
      this.$refs.hodSignatureInput.click()
    },

    onHodSignatureChange(e) {
      const file = e.target.files[0]
      this.formData.approvals.hod.signature = file || null

      if (!file) {
        this.hodSignaturePreview = ''
        this.hodSignatureFileName = ''
        return
      }

      const allowedTypes = [
        'image/png',
        'image/jpeg',
        'image/jpg',
        'application/pdf'
      ]
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
      this.formData.approvals.hod.signature = null
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
      this.formData.approvals.divisionalDirector.signature = file || null

      if (!file) {
        this.divDirectorSignaturePreview = ''
        this.divDirectorSignatureFileName = ''
        return
      }

      const allowedTypes = [
        'image/png',
        'image/jpeg',
        'image/jpg',
        'application/pdf'
      ]
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
      this.formData.approvals.divisionalDirector.signature = null
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
      this.formData.approvals.directorICT.signatureDate = file || null

      if (!file) {
        this.directorICTSignaturePreview = ''
        this.directorICTSignatureFileName = ''
        return
      }

      const allowedTypes = [
        'image/png',
        'image/jpeg',
        'image/jpg',
        'application/pdf'
      ]
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
      this.formData.approvals.directorICT.signatureDate = null
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
      this.formData.implementation.headIT.signature = file || null

      if (!file) {
        this.headITSignaturePreview = ''
        this.headITSignatureFileName = ''
        return
      }

      const allowedTypes = [
        'image/png',
        'image/jpeg',
        'image/jpg',
        'application/pdf'
      ]
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
      this.formData.implementation.headIT.signature = null
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
      this.formData.implementation.ictOfficer.signature = file || null

      if (!file) {
        this.ictOfficerSignaturePreview = ''
        this.ictOfficerSignatureFileName = ''
        return
      }

      const allowedTypes = [
        'image/png',
        'image/jpeg',
        'image/jpg',
        'application/pdf'
      ]
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
      this.formData.implementation.ictOfficer.signature = null
      this.ictOfficerSignaturePreview = ''
      this.ictOfficerSignatureFileName = ''
      if (this.$refs.ictOfficerSignatureInput) {
        this.$refs.ictOfficerSignatureInput.value = ''
      }
    },

    resetForm() {
      this.formData = {
        pfNumber: '',
        staffName: '',
        department_id: '',
        signature: null,
        requestType: '',
        selectedModules: [],
        accessType: '',
        tempDate: {
          day: '',
          month: '',
          year: ''
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
            date: '',
            decision: '',
            rejectionComments: ''
          },
          directorICT: {
            name: '',
            signatureDate: ''
          }
        },
        comments: '',
        implementation: {
          headIT: {
            name: '',
            signatureDate: ''
          },
          ictOfficer: {
            name: '',
            signatureDate: ''
          }
        }
      }
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
      this.showNotification('Form has been reset')
    },

    showNotification(message, type = 'info') {
      // Simple notification implementation
      // You can replace this with your preferred notification library
      console.log(`[${type.toUpperCase()}] ${message}`)

      // For now, using alert - replace with your notification system
      if (type === 'error') {
        alert(`Error: ${message}`)
      } else {
        alert(message)
      }
    },

    checkReviewMode() {
      // Check if we're in review mode (coming from requests dashboard)
      if (this.route.query.mode === 'review' && this.route.query.requestId) {
        this.isReviewMode = true
        this.requestId = this.route.query.requestId
        this.populateFormFromQuery()
        this.makeFormReadOnlyForReview()
      }
    },

    populateFormFromQuery() {
      const query = this.route.query

      // Populate basic information
      this.formData.pfNumber = query.pfNumber || ''
      this.formData.staffName = query.staffName || ''
      this.formData.department_id =
        query.department_id || query.department || ''
      this.formData.requestType = query.moduleRequestedFor?.toLowerCase() || ''
      this.formData.accessType =
        query.accessType === 'Permanent (until retirement)'
          ? 'permanent'
          : 'temporary'

      // Handle temporary date
      if (query.temporaryUntil) {
        const date = new Date(query.temporaryUntil)
        this.formData.tempDate = {
          day: date.getDate().toString().padStart(2, '0'),
          month: (date.getMonth() + 1).toString().padStart(2, '0'),
          year: date.getFullYear().toString()
        }
      }

      // Populate selected modules
      if (query.selectedModules) {
        try {
          const modules = JSON.parse(query.selectedModules)
          this.formData.selectedModules = modules
        } catch (e) {
          console.error('Error parsing selected modules:', e)
        }
      }

      // Populate comments
      this.formData.comments = query.comments || ''

      // Set digital signature status
      if (query.digitalSignature === 'true') {
        this.signaturePreview = 'review-mode-signature'
        this.signatureFileName = 'Digital Signature (Submitted)'
      }
    },

    makeFormReadOnlyForReview() {
      // Add readonly attributes to form elements
      this.$nextTick(() => {
        const inputs = this.$el.querySelectorAll('input, select, textarea')
        inputs.forEach((input) => {
          // Allow HOD to edit specific sections
          if (this.canHodEdit(input)) {
            return // Skip making this input readonly
          }

          if (!input.classList.contains('approval-input')) {
            input.setAttribute('readonly', true)
            input.setAttribute('disabled', true)
          }
        })

        // Disable checkboxes and radio buttons
        const checkboxes = this.$el.querySelectorAll(
          'input[type="checkbox"], input[type="radio"]'
        )
        checkboxes.forEach((checkbox) => {
          // Allow HOD to edit specific checkboxes/radios
          if (this.canHodEdit(checkbox)) {
            return // Skip disabling this checkbox
          }

          if (!checkbox.classList.contains('approval-input')) {
            checkbox.setAttribute('disabled', true)
          }
        })
      })
    },

    canHodEdit(element) {
      // Allow HOD to edit when they are the current approver
      if (
        this.userRole === this.ROLES.HEAD_OF_DEPARTMENT &&
        this.canApproveAtStage()
      ) {
        const hodEditableClasses = [
          'hod-editable',
          'module-request-editable',
          'access-rights-editable',
          'comments-editable',
          'hod-approval-editable'
        ]

        if (
          hodEditableClasses.some((className) =>
            element.classList.contains(className)
          )
        ) {
          return true
        }
      }

      // Allow Divisional Director to edit their approval section
      if (
        this.userRole === this.ROLES.DIVISIONAL_DIRECTOR &&
        this.canApproveAtStage()
      ) {
        const divDirectorEditableClasses = [
          'divisional-director-editable',
          'divisional-approval-editable',
          'divisional-comments-editable'
        ]

        if (
          divDirectorEditableClasses.some((className) =>
            element.classList.contains(className)
          )
        ) {
          return true
        }
      }

      return false
    },

    getApprovalStatus(stage) {
      const query = this.route.query
      const statusMap = {
        hod: query.hodApprovalStatus,
        divisional: query.divisionalStatus,
        dict: query.dictStatus,
        headOfIt: query.headOfItStatus,
        ict: query.ictStatus
      }
      return statusMap[stage] || 'pending'
    },

    canApproveAtStage() {
      if (!this.isReviewMode) return false

      const query = this.route.query
      switch (this.userRole) {
        case this.ROLES.HEAD_OF_DEPARTMENT:
          return query.hodApprovalStatus === 'pending'
        case this.ROLES.DIVISIONAL_DIRECTOR:
          return (
            query.hodApprovalStatus === 'approved' &&
            query.divisionalStatus === 'pending'
          )
        case this.ROLES.ICT_DIRECTOR:
          return (
            query.divisionalStatus === 'approved' &&
            query.dictStatus === 'pending'
          )
        case this.ROLES.HOD_IT:
          return (
            query.dictStatus === 'approved' &&
            query.headOfItStatus === 'pending'
          )
        case this.ROLES.ICT_OFFICER:
          return (
            query.headOfItStatus === 'approved' && query.ictStatus === 'pending'
          )
        default:
          return false
      }
    },

    approveRequest() {
      if (!this.canApproveAtStage()) {
        this.showNotification('You cannot approve this request at this stage.')
        return
      }

      // Special handling for Divisional Director
      if (this.userRole === this.ROLES.DIVISIONAL_DIRECTOR) {
        if (!this.formData.approvals.divisionalDirector.decision) {
          this.showNotification(
            'Please select Approve or Reject before submitting.'
          )
          return
        }

        if (this.formData.approvals.divisionalDirector.decision === 'reject') {
          if (
            !this.formData.approvals.divisionalDirector.rejectionComments?.trim()
          ) {
            this.showNotification('Please provide a reason for rejection.')
            return
          }

          if (
            confirm(
              `Are you sure you want to reject request ${this.requestId}? This will send it back to the Head of Department.`
            )
          ) {
            this.rejectRequestAsDivisionalDirector()
          }
          return
        }

        if (this.formData.approvals.divisionalDirector.decision === 'approve') {
          if (
            confirm(
              `Are you sure you want to approve request ${this.requestId}?`
            )
          ) {
            this.approveRequestAsDivisionalDirector()
          }
          return
        }
      }

      if (
        confirm(`Are you sure you want to approve request ${this.requestId}?`)
      ) {
        // Here you would make an API call to approve the request
        console.log('Approving request:', this.requestId)
        this.showNotification('Request approved successfully!')

        // Redirect back to requests list
        setTimeout(() => {
          this.router.push('/hod-dashboard/request-list')
        }, 2000)
      }
    },

    approveRequestAsDivisionalDirector() {
      // Validate required fields for Divisional Director approval
      if (!this.formData.approvals.divisionalDirector.name?.trim()) {
        this.showNotification('Please enter your name before approving.')
        return
      }

      // Here you would make an API call to approve the request as Divisional Director
      console.log('Divisional Director approving request:', this.requestId, {
        name: this.formData.approvals.divisionalDirector.name,
        date: this.formData.approvals.divisionalDirector.date,
        decision: 'approve'
      })

      this.showNotification(
        'Request approved successfully! Moving to next approval stage.'
      )

      // Redirect back to requests list
      setTimeout(() => {
        this.router.push('/hod-dashboard/request-list')
      }, 2000)
    },

    rejectRequestAsDivisionalDirector() {
      // Validate required fields for Divisional Director rejection
      if (!this.formData.approvals.divisionalDirector.name?.trim()) {
        this.showNotification('Please enter your name before rejecting.')
        return
      }

      if (
        !this.formData.approvals.divisionalDirector.rejectionComments?.trim()
      ) {
        this.showNotification('Please provide a reason for rejection.')
        return
      }

      // Here you would make an API call to reject the request and send back to HOD
      console.log('Divisional Director rejecting request:', this.requestId, {
        name: this.formData.approvals.divisionalDirector.name,
        date: this.formData.approvals.divisionalDirector.date,
        decision: 'reject',
        rejectionComments:
          this.formData.approvals.divisionalDirector.rejectionComments
      })

      this.showNotification(
        'Request rejected and sent back to Head of Department with comments.'
      )

      // Redirect back to requests list
      setTimeout(() => {
        this.router.push('/internal-access/list')
      }, 2000)
    },

    rejectRequest() {
      if (!this.canApproveAtStage()) {
        this.showNotification('You cannot reject this request at this stage.')
        return
      }

      // Special handling for Divisional Director
      if (this.userRole === this.ROLES.DIVISIONAL_DIRECTOR) {
        // Set decision to reject and let the form validation handle the rest
        this.formData.approvals.divisionalDirector.decision = 'reject'
        this.showNotification(
          'Please provide a reason for rejection in the comments field below.'
        )
        return
      }

      const reason = prompt('Please provide a reason for rejection:')
      if (
        reason &&
        confirm(`Are you sure you want to reject request ${this.requestId}?`)
      ) {
        // Here you would make an API call to reject the request
        console.log('Rejecting request:', this.requestId, 'Reason:', reason)
        this.showNotification('Request rejected successfully!')

        // Redirect back to requests list
        setTimeout(() => {
          this.router.push('/internal-access/list')
        }, 2000)
      }
    },

    goBackToRequests() {
      this.router.push('/internal-access/list')
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
  box-shadow: 0 8px 32px rgba(29, 78, 216, 0.4),
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
input[type="checkbox"]:checked,
input[type="radio"]:checked {
  background-color: #1e40af;
  border-color: #1e40af;
}

input[type="checkbox"]:focus,
input[type="radio"]:focus {
  box-shadow: 0 0 0 3px rgba(30, 64, 175, 0.1);
}

/* Smooth transitions */
.transition-all {
  transition: all 0.3s ease;
}

/* Static Grid Background - No Animations */

/* Glass morphism effect */
.backdrop-blur-sm {
  backdrop-filter: blur(8px);
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

/* Enhanced form sections */
.border-l-2 {
  position: relative;
  background: linear-gradient(
    135deg,
    rgba(255, 255, 255, 0.1),
    rgba(255, 255, 255, 0.05)
  );
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

/* Review mode styles */
.review-mode input[readonly],
.review-mode select[disabled],
.review-mode textarea[readonly] {
  background-color: rgba(59, 130, 246, 0.1) !important;
  border-color: rgba(96, 165, 250, 0.2) !important;
  cursor: not-allowed;
}

.review-mode input[type="checkbox"][disabled],
.review-mode input[type="radio"][disabled] {
  opacity: 0.7;
  cursor: not-allowed;
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
</style>
