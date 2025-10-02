<template>
  <div class="flex flex-col h-screen">
    <Header />
    <div class="flex flex-1 overflow-hidden">
      <ModernSidebar />
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
          <div class="medical-glass-card rounded-t-3xl p-6 mb-0 border-b border-blue-300/30">
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
                    class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white px-10 py-4 rounded-full text-xl font-bold shadow-2xl transform hover:scale-105 transition-all duration-300 border-2 border-blue-400/60"
                  >
                    <span class="relative z-10 flex items-center gap-2">
                      <i class="fas fa-wifi"></i>
                      INTERNET CONNECTIVITY APPLICATION
                    </span>
                    <div
                      class="absolute inset-0 bg-gradient-to-r from-indigo-700 to-blue-800 rounded-full opacity-0 hover:opacity-100 transition-opacity duration-300"
                    ></div>
                  </div>
                </div>
                <h2
                  class="text-2xl font-bold text-blue-100 tracking-wide drop-shadow-md animate-fade-in-delay"
                >
                  {{ isReviewMode ? 'REQUEST REVIEW - ' + (requestId || $route.params.id || 'N/A') : 'ACCESS REQUEST FORM' }}
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
              :class="['p-8 space-y-8', { 'review-mode': isReviewMode }]"
            >
              <!-- Section A: Employee Details -->
              <div
                class="medical-card bg-gradient-to-r from-blue-600/25 to-blue-700/25 border-2 border-blue-400/40 p-6 rounded-2xl backdrop-blur-sm hover:shadow-2xl hover:shadow-blue-500/20 transition-all duration-500 group"
              >
                <div class="flex items-center space-x-3 mb-4">
                  <div
                    class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-blue-300/50"
                  >
                    <span class="text-white font-bold text-lg">A</span>
                  </div>
                  <h3 class="text-lg font-bold text-white flex items-center">
                    <i class="fas fa-user-circle mr-2 text-blue-300"></i>
                    EMPLOYEE DETAILS
                  </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-4">
                  <!-- Employee Full Name -->
                  <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-blue-100 mb-1">
                      1(a). Employee Full Name
                      <span class="text-red-400">*</span>
                    </label>
                    <div class="relative">
                      <input
                        v-model="formData.employeeFullName"
                        type="text"
                        class="medical-input w-full px-3 py-2 bg-white/15 border-2 border-blue-300/30 rounded-lg focus:border-blue-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-blue-500/20 text-sm"
                        placeholder="Enter full name as per employment records"
                        required
                      />
                      <div
                        class="absolute inset-0 rounded-lg bg-gradient-to-r from-blue-500/10 to-blue-600/10 opacity-0 hover:opacity-100 transition-opacity duration-300 pointer-events-none"
                      ></div>
                    </div>
                  </div>

                  <!-- PF Number -->
                  <div>
                    <label class="block text-xs font-bold text-blue-100 mb-1">
                      (b). PF Number <span class="text-red-400">*</span>
                    </label>
                    <div class="relative">
                      <input
                        v-model="formData.pfNumber"
                        type="text"
                        class="medical-input w-full px-3 py-2 bg-white/15 border-2 border-blue-300/30 rounded-lg focus:border-blue-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-blue-500/20 text-sm"
                        placeholder="Enter PF Number"
                        required
                      />
                      <div
                        class="absolute inset-0 rounded-lg bg-gradient-to-r from-blue-500/10 to-blue-600/10 opacity-0 hover:opacity-100 transition-opacity duration-300 pointer-events-none"
                      ></div>
                    </div>
                  </div>

                  <!-- Department -->
                  <div>
                    <label class="block text-xs font-bold text-blue-100 mb-1">
                      2(a). Department <span class="text-red-400">*</span>
                    </label>
                    <div class="relative">
                      <input
                        v-model="formData.department"
                        type="text"
                        class="medical-input w-full px-3 py-2 bg-white/15 border-2 border-blue-300/30 rounded-lg focus:border-blue-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-blue-500/20 text-sm"
                        placeholder="Enter your department"
                        required
                      />
                      <div
                        class="absolute inset-0 rounded-lg bg-gradient-to-r from-blue-500/10 to-blue-600/10 opacity-0 hover:opacity-100 transition-opacity duration-300 pointer-events-none"
                      ></div>
                    </div>
                  </div>

                  <!-- Designation -->
                  <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-blue-100 mb-1">
                      Designation <span class="text-red-400">*</span>
                    </label>
                    <div class="relative">
                      <input
                        v-model="formData.designation"
                        type="text"
                        class="medical-input w-full px-3 py-2 bg-white/15 border-2 border-blue-300/30 rounded-lg focus:border-teal-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-teal-500/20 text-sm"
                        placeholder="Enter your job designation/title"
                        required
                      />
                      <div
                        class="absolute inset-0 rounded-lg bg-gradient-to-r from-teal-500/10 to-blue-500/10 opacity-0 hover:opacity-100 transition-opacity duration-300 pointer-events-none"
                      ></div>
                    </div>
                  </div>
                </div>

                <!-- Internet Purpose -->
                <div class="mb-4">
                  <label class="block text-xs font-bold text-blue-100 mb-1">
                    Internet Purpose <span class="text-red-400">*</span>
                  </label>
                  <div class="space-y-2">
                    <div
                      v-for="(purpose, index) in formData.internetPurposes"
                      :key="index"
                      class="flex items-center gap-2"
                    >
                      <span
                        class="text-xs font-medium text-blue-300 w-5 h-5 bg-blue-500/20 rounded-full flex items-center justify-center"
                        >{{ index + 1 }}</span
                      >
                      <div class="flex-1 relative">
                        <input
                          v-model="formData.internetPurposes[index]"
                          type="text"
                          class="medical-input w-full px-3 py-2 bg-white/15 border-2 border-blue-300/30 rounded-lg focus:border-teal-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 text-sm"
                          :placeholder="`Purpose ${index + 1}`"
                          :required="index === 0"
                        />
                        <div
                          class="absolute inset-0 rounded-lg bg-gradient-to-r from-teal-500/10 to-blue-500/10 opacity-0 hover:opacity-100 transition-opacity duration-300 pointer-events-none"
                        ></div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Employee Signature and Date -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                  <div>
                    <label class="block text-xs font-bold text-blue-100 mb-1">
                      Signature <span class="text-red-400">*</span>
                    </label>
                    <div class="relative">
                      <div
                        v-if="!signaturePreview"
                        class="w-full px-3 py-3 border-2 border-dashed border-blue-300/40 rounded-lg focus-within:border-blue-400 bg-white/15 backdrop-blur-sm transition-all duration-300 shadow-lg hover:shadow-xl hover:shadow-blue-500/20 min-h-[80px] flex items-center justify-center hover:bg-white/20"
                      >
                        <div class="text-center">
                          <div class="mb-2">
                            <i class="fas fa-signature text-blue-300 text-2xl mb-1"></i>
                            <p class="text-blue-100 text-xs">No signature uploaded</p>
                          </div>
                          <button
                            type="button"
                            @click="triggerFileUpload"
                            class="px-3 py-1.5 bg-gradient-to-r from-blue-500 to-blue-600 text-white text-sm font-semibold rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-300 flex items-center gap-1 mx-auto shadow-lg hover:shadow-xl transform hover:scale-105 border border-blue-400/50"
                          >
                            <i class="fas fa-upload"></i>
                            Upload Signature
                          </button>
                        </div>
                      </div>

                      <div
                        v-else
                        class="w-full px-3 py-3 border border-gray-300 rounded-lg bg-gray-50 transition-all duration-300 shadow-sm hover:shadow-md min-h-[80px] flex items-center justify-center relative"
                      >
                        <div v-if="isImage(signaturePreview)" class="text-center">
                          <img
                            :src="signaturePreview"
                            alt="Digital Signature"
                            class="max-h-[60px] max-w-full object-contain mx-auto mb-1"
                          />
                          <p class="text-xs text-gray-600">
                            {{ signatureFileName }}
                          </p>
                        </div>
                        <div v-else class="text-center">
                          <div
                            class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mx-auto mb-1"
                          >
                            <i class="fas fa-file-pdf text-red-600 text-lg"></i>
                          </div>
                          <p class="text-xs text-gray-600">
                            {{ signatureFileName }}
                          </p>
                        </div>

                        <div class="absolute top-1 right-1 flex gap-1">
                          <button
                            type="button"
                            @click="triggerFileUpload"
                            class="w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-xs hover:bg-blue-600 transition-colors duration-200"
                            title="Change signature"
                          >
                            <i class="fas fa-edit"></i>
                          </button>
                          <button
                            type="button"
                            @click="clearSignature"
                            class="w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center text-xs hover:bg-red-600 transition-colors duration-200"
                            title="Remove signature"
                          >
                            <i class="fas fa-times"></i>
                          </button>
                        </div>
                      </div>

                      <input
                        ref="signatureInput"
                        type="file"
                        accept="image/png,image/jpeg,application/pdf"
                        @change="onSignatureChange"
                        class="hidden"
                      />
                    </div>
                  </div>
                  <div>
                    <label class="block text-xs font-bold text-blue-100 mb-1">
                      Date <span class="text-red-400">*</span>
                    </label>
                    <div class="relative">
                      <input
                        v-model="formData.employeeDate"
                        type="date"
                        class="medical-input w-full px-3 py-2 bg-white/15 border-2 border-blue-300/30 rounded-lg focus:border-blue-400 focus:outline-none text-white backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-blue-500/20 text-sm"
                        required
                      />
                      <div
                        class="absolute inset-0 rounded-lg bg-gradient-to-r from-teal-500/10 to-blue-500/10 opacity-0 hover:opacity-100 transition-opacity duration-300 pointer-events-none"
                      ></div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Section B: Approval Process -->
              <div
                class="medical-card bg-gradient-to-r from-emerald-600/25 to-teal-600/25 border-2 border-emerald-400/40 p-8 rounded-2xl backdrop-blur-sm hover:shadow-2xl hover:shadow-emerald-500/20 transition-all duration-500 group"
              >
                <div class="flex items-center space-x-4 mb-6">
                  <div
                    class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-emerald-300/50"
                  >
                    <span class="text-white font-bold text-xl">B</span>
                  </div>
                  <h3 class="text-xl font-bold text-white flex items-center">
                    <i class="fas fa-check-circle mr-2 text-emerald-300"></i>
                    APPROVAL PROCESS
                  </h3>
                </div>

                <!-- Head of Department Approval -->
                <div
                  class="bg-white/15 rounded-xl p-6 mb-6 border border-emerald-300/30 backdrop-blur-sm"
                >
                  <h4 class="text-lg font-bold text-white mb-4 flex items-center">
                    <i class="fas fa-certificate mr-2 text-emerald-300"></i>
                    1. Head of Department Certification
                  </h4>
                  <p
                    class="text-sm text-blue-100 mb-4 italic font-medium bg-emerald-500/20 p-3 rounded-lg border border-emerald-400/30"
                  >
                    "I certify that he/she deserves internet connection and that his/her duties
                    require internet access."
                  </p>

                  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                      <label class="block text-base font-bold text-blue-100 mb-3">
                        Head of Department Name
                        <span class="text-red-400">*</span>
                      </label>
                      <div class="relative">
                        <input
                          v-model="formData.approvals.hod.name"
                          type="text"
                          class="medical-input w-full px-4 py-3 bg-white/15 border-2 border-blue-300/30 rounded-xl focus:border-emerald-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20"
                          placeholder="HOD Name"
                          required
                        />
                        <div
                          class="absolute inset-0 rounded-xl bg-gradient-to-r from-emerald-500/10 to-blue-500/10 opacity-0 hover:opacity-100 transition-opacity duration-300 pointer-events-none"
                        ></div>
                      </div>
                    </div>
                    <div>
                      <label class="block text-base font-bold text-blue-100 mb-3">
                        Signature <span class="text-red-400">*</span>
                      </label>
                      <div class="relative">
                        <div
                          v-if="!hodSignaturePreview"
                          class="w-full px-4 py-4 border-2 border-dashed border-emerald-300/40 rounded-xl focus-within:border-emerald-400 bg-white/15 backdrop-blur-sm transition-all duration-300 shadow-lg hover:shadow-xl hover:shadow-emerald-500/20 min-h-[80px] flex items-center justify-center hover:bg-white/20"
                        >
                          <div class="text-center">
                            <div class="mb-3">
                              <i class="fas fa-signature text-emerald-300 text-2xl mb-2"></i>
                              <p class="text-blue-100 text-sm">No signature uploaded</p>
                            </div>
                            <button
                              type="button"
                              @click="triggerHodSignatureUpload"
                              class="px-4 py-2 bg-gradient-to-r from-emerald-500 to-blue-600 text-white text-sm font-semibold rounded-lg hover:from-emerald-600 hover:to-blue-700 transition-all duration-300 flex items-center gap-2 mx-auto shadow-lg hover:shadow-xl transform hover:scale-105 border border-emerald-400/50"
                            >
                              <i class="fas fa-upload"></i>
                              Upload Digital Signature
                            </button>
                          </div>
                        </div>

                        <div
                          v-else
                          class="w-full px-2 py-2 border border-gray-300 rounded-lg bg-gray-50 transition-all duration-300 shadow-sm hover:shadow-md min-h-[50px] flex items-center justify-center relative"
                        >
                          <div v-if="isImage(hodSignaturePreview)" class="text-center">
                            <img
                              :src="hodSignaturePreview"
                              alt="HOD Signature"
                              class="max-h-[35px] max-w-full object-contain mx-auto mb-1"
                            />
                            <p class="text-xs text-gray-600">
                              {{ hodSignatureFileName }}
                            </p>
                          </div>
                          <div v-else class="text-center">
                            <div
                              class="w-8 h-8 bg-red-100 rounded flex items-center justify-center mx-auto mb-1"
                            >
                              <i class="fas fa-file-pdf text-red-600 text-sm"></i>
                            </div>
                            <p class="text-xs text-gray-600">
                              {{ hodSignatureFileName }}
                            </p>
                          </div>

                          <div class="absolute top-1 right-1 flex gap-1">
                            <button
                              type="button"
                              @click="triggerHodSignatureUpload"
                              class="w-5 h-5 bg-green-500 text-white rounded-full flex items-center justify-center text-xs hover:bg-green-600 transition-colors duration-200"
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
                    </div>
                    <div>
                      <label class="block text-base font-bold text-blue-100 mb-3">
                        Date <span class="text-red-400">*</span>
                      </label>
                      <div class="relative">
                        <input
                          v-model="formData.approvals.hod.date"
                          type="date"
                          class="medical-input w-full px-4 py-3 bg-white/15 border-2 border-blue-300/30 rounded-xl focus:border-emerald-400 focus:outline-none text-white backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20"
                          required
                        />
                        <div
                          class="absolute inset-0 rounded-xl bg-gradient-to-r from-emerald-500/10 to-blue-500/10 opacity-0 hover:opacity-100 transition-opacity duration-300 pointer-events-none"
                        ></div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Director Approval -->
                <div
                  class="bg-white/15 rounded-xl p-6 border border-emerald-300/30 backdrop-blur-sm"
                >
                  <h4 class="text-lg font-bold text-white mb-4 flex items-center">
                    <i class="fas fa-user-tie mr-2 text-emerald-300"></i>
                    2. Director's Decision
                  </h4>

                  <!-- Approval/Disapproval Radio Buttons -->
                  <div class="mb-6">
                    <label class="block text-base font-bold text-blue-100 mb-3">
                      Decision (please choose which is applicable/not applicable)
                      <span class="text-red-400">*</span>
                    </label>
                    <div class="flex justify-center gap-6">
                      <label
                        class="flex items-center cursor-pointer p-4 border-2 border-blue-300/30 rounded-xl hover:border-emerald-400 hover:bg-white/10 transition-all backdrop-blur-sm"
                      >
                        <input
                          v-model="formData.approvals.director.decision"
                          type="radio"
                          value="approve"
                          class="w-4 h-4 text-emerald-600 border-blue-300 focus:ring-emerald-500 mr-3"
                        />
                        <span class="font-medium text-white text-sm">I APPROVE</span>
                      </label>
                      <label
                        class="flex items-center cursor-pointer p-4 border-2 border-blue-300/30 rounded-xl hover:border-red-400 hover:bg-white/10 transition-all backdrop-blur-sm"
                      >
                        <input
                          v-model="formData.approvals.director.decision"
                          type="radio"
                          value="disapprove"
                          class="w-4 h-4 text-red-600 border-blue-300 focus:ring-red-500 mr-3"
                        />
                        <span class="font-medium text-white text-sm">I DISAPPROVE</span>
                      </label>
                    </div>
                  </div>

                  <!-- Comment Section (appears when disapproved) -->
                  <transition
                    enter-active-class="transition ease-out duration-300"
                    enter-from-class="opacity-0 transform -translate-y-2"
                    enter-to-class="opacity-100 transform translate-y-0"
                    leave-active-class="transition ease-in duration-200"
                    leave-from-class="opacity-100 transform translate-y-0"
                    leave-to-class="opacity-0 transform -translate-y-2"
                  >
                    <div v-if="formData.approvals.director.decision === 'disapprove'" class="mb-6">
                      <div
                        class="bg-white/15 border-2 border-red-400/40 rounded-xl p-6 backdrop-blur-sm"
                      >
                        <div class="flex items-center mb-4">
                          <div
                            class="w-8 h-8 bg-gradient-to-br from-red-500 to-red-600 rounded-full flex items-center justify-center mr-3 shadow-lg"
                          >
                            <i class="fas fa-exclamation text-white text-sm"></i>
                          </div>
                          <h5 class="text-lg font-bold text-white flex items-center">
                            <i class="fas fa-times-circle mr-2 text-red-400"></i>
                            Reason for Disapproval
                          </h5>
                        </div>
                        <label class="block text-base font-bold text-blue-100 mb-3">
                          Reason for Disapproval - Please provide the reason for disapproving this
                          internet access request
                          <span class="text-red-400">*</span>
                        </label>
                        <div class="relative">
                          <textarea
                            v-model="formData.approvals.director.comments"
                            rows="4"
                            class="medical-input w-full px-4 py-4 bg-white/15 border-2 border-blue-300/30 rounded-xl focus:border-red-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 resize-y focus:shadow-lg focus:shadow-red-500/20"
                            placeholder="Please explain the reason for disapproving this internet access request. Be specific about the concerns or requirements that are not met. This comment will be included in the official record and communicated to the applicant."
                            :required="formData.approvals.director.decision === 'disapprove'"
                          ></textarea>
                          <div
                            class="absolute inset-0 rounded-xl bg-gradient-to-r from-red-500/10 to-blue-500/10 opacity-0 hover:opacity-100 transition-opacity duration-300 pointer-events-none"
                          ></div>
                        </div>
                        <p
                          class="text-sm text-blue-200 mt-3 italic bg-red-500/20 p-3 rounded-lg border border-red-400/30"
                        >
                          <i class="fas fa-info-circle mr-2 text-red-300"></i>
                          This comment will be included in the official record and communicated to
                          the applicant.
                        </p>
                      </div>
                    </div>
                  </transition>

                  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                      <label class="block text-base font-bold text-blue-100 mb-3">
                        Director's Name <span class="text-red-400">*</span>
                      </label>
                      <div class="relative">
                        <input
                          v-model="formData.approvals.director.name"
                          type="text"
                          class="medical-input w-full px-4 py-3 bg-white/15 border-2 border-blue-300/30 rounded-xl focus:border-emerald-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20"
                          placeholder="Director Name"
                          required
                        />
                        <div
                          class="absolute inset-0 rounded-xl bg-gradient-to-r from-emerald-500/10 to-blue-500/10 opacity-0 hover:opacity-100 transition-opacity duration-300 pointer-events-none"
                        ></div>
                      </div>
                    </div>
                    <div>
                      <label class="block text-base font-bold text-blue-100 mb-3">
                        Signature <span class="text-red-400">*</span>
                      </label>
                      <div class="relative">
                        <div
                          v-if="!directorSignaturePreview"
                          class="w-full px-3 py-3 border-2 border-dashed border-emerald-300/40 rounded-xl focus-within:border-emerald-400 bg-white/15 backdrop-blur-sm transition-all duration-300 shadow-lg hover:shadow-xl hover:shadow-emerald-500/20 min-h-[70px] flex items-center justify-center hover:bg-white/20"
                        >
                          <div class="text-center">
                            <div class="mb-2">
                              <i class="fas fa-signature text-emerald-300 text-lg mb-1"></i>
                              <p class="text-blue-100 text-xs">No signature uploaded</p>
                            </div>
                            <button
                              type="button"
                              @click="triggerDirectorSignatureUpload"
                              class="px-3 py-1.5 bg-gradient-to-r from-emerald-500 to-blue-600 text-white text-sm font-semibold rounded-lg hover:from-emerald-600 hover:to-blue-700 transition-all duration-300 flex items-center gap-1 mx-auto shadow-lg hover:shadow-xl transform hover:scale-105 border border-emerald-400/50"
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
                          <div v-if="isImage(directorSignaturePreview)" class="text-center">
                            <img
                              :src="directorSignaturePreview"
                              alt="Director Signature"
                              class="max-h-[50px] max-w-full object-contain mx-auto mb-1"
                            />
                            <p class="text-xs text-blue-100">
                              {{ directorSignatureFileName }}
                            </p>
                          </div>
                          <div v-else class="text-center">
                            <div
                              class="w-12 h-12 bg-red-500/20 rounded-xl flex items-center justify-center mx-auto mb-1"
                            >
                              <i class="fas fa-file-pdf text-red-400 text-lg"></i>
                            </div>
                            <p class="text-xs text-blue-100">
                              {{ directorSignatureFileName }}
                            </p>
                          </div>

                          <div class="absolute top-2 right-2 flex gap-1">
                            <button
                              type="button"
                              @click="triggerDirectorSignatureUpload"
                              class="w-6 h-6 bg-emerald-500 text-white rounded-full flex items-center justify-center text-xs hover:bg-emerald-600 transition-colors duration-200 shadow-lg"
                              title="Change signature"
                            >
                              <i class="fas fa-edit"></i>
                            </button>
                            <button
                              type="button"
                              @click="clearDirectorSignature"
                              class="w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center text-xs hover:bg-red-600 transition-colors duration-200 shadow-lg"
                              title="Remove signature"
                            >
                              <i class="fas fa-times"></i>
                            </button>
                          </div>
                        </div>

                        <input
                          ref="directorSignatureInput"
                          type="file"
                          accept="image/png,image/jpeg,application/pdf"
                          @change="onDirectorSignatureChange"
                          class="hidden"
                        />
                      </div>
                    </div>
                    <div>
                      <label class="block text-base font-bold text-blue-100 mb-3">
                        Date <span class="text-red-400">*</span>
                      </label>
                      <div class="relative">
                        <input
                          v-model="formData.approvals.director.date"
                          type="date"
                          class="medical-input w-full px-4 py-3 bg-white/15 border-2 border-blue-300/30 rounded-xl focus:border-emerald-400 focus:outline-none text-white backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20"
                          required
                        />
                        <div
                          class="absolute inset-0 rounded-xl bg-gradient-to-r from-emerald-500/10 to-blue-500/10 opacity-0 hover:opacity-100 transition-opacity duration-300 pointer-events-none"
                        ></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Section C: IT Department -->
              <div
                class="medical-card bg-gradient-to-r from-purple-600/25 to-indigo-600/25 border-2 border-purple-400/40 p-8 rounded-2xl backdrop-blur-sm hover:shadow-2xl hover:shadow-purple-500/20 transition-all duration-500 group"
              >
                <div class="flex items-center space-x-4 mb-6">
                  <div
                    class="w-14 h-14 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-purple-300/50"
                  >
                    <span class="text-white font-bold text-xl">C</span>
                  </div>
                  <h3 class="text-xl font-bold text-white flex items-center">
                    <i class="fas fa-server mr-2 text-purple-300"></i>
                    IT DEPARTMENT
                  </h3>
                </div>

                <div
                  class="bg-white/15 rounded-xl p-6 border border-purple-300/30 backdrop-blur-sm"
                >
                  <h4 class="text-lg font-bold text-white mb-4 flex items-center">
                    <i class="fas fa-globe mr-2 text-purple-300"></i>
                    3. Internet Access Grant
                  </h4>

                  <div class="mb-6">
                    <label class="block text-base font-bold text-blue-100 mb-3">
                      Access Status <span class="text-red-400">*</span>
                    </label>
                    <div
                      class="bg-purple-500/20 p-4 rounded-xl border border-purple-400/30 backdrop-blur-sm"
                    >
                      <div class="flex items-center gap-3 mb-3 flex-wrap">
                        <span class="text-base text-white font-medium">The mentioned person</span>
                        <div class="relative">
                          <input
                            v-model="formData.itDepartment.personName"
                            type="text"
                            class="medical-input px-4 py-2 bg-white/15 border-2 border-blue-300/30 rounded-lg focus:border-purple-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm"
                            placeholder="Person's name"
                            required
                          />
                        </div>
                        <span class="text-base text-white font-medium"
                          >has been granted internet access as</span
                        >
                        <div class="relative">
                          <input
                            v-model="formData.itDepartment.accessType"
                            type="text"
                            class="medical-input px-4 py-2 bg-white/15 border-2 border-blue-300/30 rounded-lg focus:border-purple-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm"
                            placeholder="Access type/level"
                            required
                          />
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                      <label class="block text-base font-bold text-blue-100 mb-3">
                        IT Officer Signature <span class="text-red-400">*</span>
                      </label>
                      <div class="relative">
                        <div
                          v-if="!itSignaturePreview"
                          class="w-full px-3 py-3 border-2 border-dashed border-purple-300/40 rounded-xl focus-within:border-purple-400 bg-white/15 backdrop-blur-sm transition-all duration-300 shadow-lg hover:shadow-xl hover:shadow-purple-500/20 min-h-[70px] flex items-center justify-center hover:bg-white/20"
                        >
                          <div class="text-center">
                            <div class="mb-2">
                              <i class="fas fa-signature text-purple-300 text-lg mb-1"></i>
                              <p class="text-blue-100 text-xs">No signature uploaded</p>
                            </div>
                            <button
                              type="button"
                              @click="triggerItSignatureUpload"
                              class="px-3 py-1.5 bg-gradient-to-r from-purple-500 to-blue-600 text-white text-sm font-semibold rounded-lg hover:from-purple-600 hover:to-blue-700 transition-all duration-300 flex items-center gap-1 mx-auto shadow-lg hover:shadow-xl transform hover:scale-105 border border-purple-400/50"
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
                          <div v-if="isImage(itSignaturePreview)" class="text-center">
                            <img
                              :src="itSignaturePreview"
                              alt="IT Officer Signature"
                              class="max-h-[50px] max-w-full object-contain mx-auto mb-1"
                            />
                            <p class="text-xs text-blue-100">
                              {{ itSignatureFileName }}
                            </p>
                          </div>
                          <div v-else class="text-center">
                            <div
                              class="w-12 h-12 bg-red-500/20 rounded-xl flex items-center justify-center mx-auto mb-1"
                            >
                              <i class="fas fa-file-pdf text-red-400 text-lg"></i>
                            </div>
                            <p class="text-xs text-blue-100">
                              {{ itSignatureFileName }}
                            </p>
                          </div>

                          <div class="absolute top-2 right-2 flex gap-1">
                            <button
                              type="button"
                              @click="triggerItSignatureUpload"
                              class="w-6 h-6 bg-purple-500 text-white rounded-full flex items-center justify-center text-xs hover:bg-purple-600 transition-colors duration-200 shadow-lg"
                              title="Change signature"
                            >
                              <i class="fas fa-edit"></i>
                            </button>
                            <button
                              type="button"
                              @click="clearItSignature"
                              class="w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center text-xs hover:bg-red-600 transition-colors duration-200 shadow-lg"
                              title="Remove signature"
                            >
                              <i class="fas fa-times"></i>
                            </button>
                          </div>
                        </div>

                        <input
                          ref="itSignatureInput"
                          type="file"
                          accept="image/png,image/jpeg,application/pdf"
                          @change="onItSignatureChange"
                          class="hidden"
                        />
                      </div>
                    </div>
                    <div>
                      <label class="block text-base font-bold text-blue-100 mb-3">
                        Date <span class="text-red-400">*</span>
                      </label>
                      <div class="relative">
                        <input
                          v-model="formData.itDepartment.date"
                          type="date"
                          class="medical-input w-full px-4 py-3 bg-white/15 border-2 border-blue-300/30 rounded-xl focus:border-purple-400 focus:outline-none text-white backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20"
                          required
                        />
                        <div
                          class="absolute inset-0 rounded-xl bg-gradient-to-r from-purple-500/10 to-blue-500/10 opacity-0 hover:opacity-100 transition-opacity duration-300 pointer-events-none"
                        ></div>
                      </div>
                    </div>
                  </div>

                  <div>
                    <label class="block text-base font-bold text-blue-100 mb-3">
                      Other Comments DICT
                    </label>
                    <div class="relative">
                      <textarea
                        v-model="formData.itDepartment.comments"
                        rows="4"
                        class="medical-input w-full px-4 py-4 bg-white/15 border-2 border-blue-300/30 rounded-xl focus:border-purple-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 resize-y"
                        placeholder="Additional comments from IT department..."
                      ></textarea>
                      <div
                        class="absolute inset-0 rounded-xl bg-gradient-to-r from-purple-500/10 to-blue-500/10 opacity-0 hover:opacity-100 transition-opacity duration-300 pointer-events-none"
                      ></div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Section D: Executive Director -->

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

              <!-- Form Actions (Normal Mode) -->
              <div v-if="!isReviewMode" class="border-t-2 border-gray-200 pt-3">
                <div class="text-center mb-3">
                  <div class="inline-block text-gray-700 px-3 py-1 rounded-lg">
                    <p class="font-bold text-base">Directorate of ICT</p>
                    <p class="text-xs opacity-90">IT and Telephone Department</p>
                  </div>
                </div>

                <div class="flex justify-between items-center">
                  <button
                    type="button"
                    @click="goBack"
                    class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-all duration-300 font-semibold flex items-center shadow-lg hover:shadow-xl text-sm"
                  >
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Dashboard
                  </button>
                  <button
                    type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-300 font-semibold flex items-center shadow-lg hover:shadow-xl text-sm"
                  >
                    <i class="fas fa-paper-plane mr-2"></i>
                    Submit Request
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
  </div>
</template>

<script>
  import { ref } from 'vue'
  import { useRoute, useRouter } from 'vue-router'
  import Header from '@/components/header.vue'
  import ModernSidebar from '@/components/ModernSidebar.vue'
  import AppFooter from '@/components/footer.vue'
  import { useAuth } from '@/composables/useAuth'

  export default {
    name: 'InternetAccessForm',
    components: {
      Header,
      ModernSidebar,
      AppFooter
    },
    setup() {
      const route = useRoute()
      const router = useRouter()
      const { userRole, ROLES } = useAuth()
      // Sidebar state now managed by Pinia - no local state needed
      const isReviewMode = ref(false)
      const requestId = ref('')

      return {
        isReviewMode,
        requestId,
        route,
        router,
        userRole,
        ROLES
      }
    },
    mounted() {
      this.checkReviewMode()
      // Set current date as default
      const today = new Date().toISOString().split('T')[0]
      this.formData.employeeDate = today
    },
    data() {
      return {
        signaturePreview: '',
        signatureFileName: '',
        // Approval signatures
        hodSignaturePreview: '',
        hodSignatureFileName: '',
        directorSignaturePreview: '',
        directorSignatureFileName: '',
        // IT Department signatures
        itSignaturePreview: '',
        itSignatureFileName: '',
        formData: {
          // Section A: Employee Details
          employeeFullName: '',
          pfNumber: '',
          department: '',
          designation: '',
          internetPurposes: ['', '', '', ''],
          employeeSignature: null,
          employeeDate: '',

          // Section B: Approval Process
          approvals: {
            hod: {
              name: '',
              signature: '',
              date: ''
            },
            director: {
              decision: '',
              name: '',
              signature: '',
              date: '',
              comments: ''
            }
          },

          // Section C: IT Department
          itDepartment: {
            personName: '',
            accessType: '',
            signature: '',
            date: '',
            comments: ''
          },

          // Section D: Executive Director
          executiveDirector: {
            decision: '',
            signature: '',
            date: ''
          }
        }
      }
    },

    methods: {
      submitForm() {
        // Validate required fields
        if (
          !this.formData.employeeFullName ||
          !this.formData.pfNumber ||
          !this.formData.department ||
          !this.formData.designation
        ) {
          this.showNotification('Please fill in all required employee details', 'error')
          return
        }

        if (!this.formData.internetPurposes[0]) {
          this.showNotification('Please provide at least one internet purpose', 'error')
          return
        }

        if (
          (!this.formData.employeeSignature && !this.signaturePreview) ||
          !this.formData.employeeDate
        ) {
          this.showNotification('Please provide your signature and date', 'error')
          return
        }

        // Validate director's comments if disapproved
        if (
          this.formData.approvals.director.decision === 'disapprove' &&
          !this.formData.approvals.director.comments
        ) {
          this.showNotification('Please provide a reason for disapproving this request', 'error')
          return
        }

        // Here you would typically send the data to your backend
        console.log('Internet Access Form submitted:', this.formData)
        this.showNotification(
          'Internet Connectivity Application submitted successfully!',
          'success'
        )

        // Optionally reset form after submission
        this.resetForm()
      },

      triggerFileUpload() {
        this.$refs.signatureInput.click()
      },

      onSignatureChange(e) {
        const file = e.target.files[0]
        this.formData.employeeSignature = file || null

        if (!file) {
          this.signaturePreview = ''
          this.signatureFileName = ''
          return
        }

        // Validate file type
        const allowedTypes = ['image/png', 'image/jpeg', 'image/jpg']
        if (!allowedTypes.includes(file.type)) {
          this.showNotification('Please select a valid file (PNG, JPG, JPEG)', 'error')
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
        this.formData.employeeSignature = null
        this.signaturePreview = ''
        this.signatureFileName = ''
        if (this.$refs.signatureInput) {
          this.$refs.signatureInput.value = ''
        }
      },

      // Generic signature upload handler
      handleSignatureUpload(type, file) {
        if (!file) {
          this[`${type}SignaturePreview`] = ''
          this[`${type}SignatureFileName`] = ''
          return
        }

        const allowedTypes = ['image/png', 'image/jpeg', 'image/jpg']
        if (!allowedTypes.includes(file.type)) {
          this.showNotification('Please select a valid file (PNG, JPG, JPEG)', 'error')
          this.clearSpecificSignature(type)
          return
        }

        if (file.size > 5 * 1024 * 1024) {
          this.showNotification('File size must be less than 5MB', 'error')
          this.clearSpecificSignature(type)
          return
        }

        this[`${type}SignatureFileName`] = file.name

        if (file.type.startsWith('image/')) {
          const reader = new FileReader()
          reader.onload = () => {
            this[`${type}SignaturePreview`] = reader.result
          }
          reader.readAsDataURL(file)
        } else {
          this[`${type}SignaturePreview`] = 'pdf'
        }
      },

      clearSpecificSignature(type) {
        this[`${type}SignaturePreview`] = ''
        this[`${type}SignatureFileName`] = ''
        const refName = `${type}SignatureInput`
        if (this.$refs[refName]) {
          this.$refs[refName].value = ''
        }
      },

      // Specific signature methods
      triggerHodSignatureUpload() {
        this.$refs.hodSignatureInput.click()
      },
      onHodSignatureChange(e) {
        this.handleSignatureUpload('hod', e.target.files[0])
      },
      clearHodSignature() {
        this.clearSpecificSignature('hod')
      },

      triggerDirectorSignatureUpload() {
        this.$refs.directorSignatureInput.click()
      },
      onDirectorSignatureChange(e) {
        this.handleSignatureUpload('director', e.target.files[0])
      },
      clearDirectorSignature() {
        this.clearSpecificSignature('director')
      },

      triggerItSignatureUpload() {
        this.$refs.itSignatureInput.click()
      },
      onItSignatureChange(e) {
        this.handleSignatureUpload('it', e.target.files[0])
      },
      clearItSignature() {
        this.clearSpecificSignature('it')
      },

      resetForm() {
        this.formData = {
          employeeFullName: '',
          pfNumber: '',
          department: '',
          designation: '',
          internetPurposes: ['', '', '', ''],
          employeeSignature: null,
          employeeDate: '',
          approvals: {
            hod: {
              name: '',
              signature: '',
              date: ''
            },
            director: {
              decision: '',
              name: '',
              signature: '',
              date: '',
              comments: ''
            }
          },
          itDepartment: {
            personName: '',
            accessType: '',
            signature: '',
            date: '',
            comments: ''
          },
          executiveDirector: {
            decision: '',
            signature: '',
            date: ''
          }
        }
        this.signaturePreview = ''
        this.signatureFileName = ''
        this.hodSignaturePreview = ''
        this.hodSignatureFileName = ''
        this.directorSignaturePreview = ''
        this.directorSignatureFileName = ''
        this.itSignaturePreview = ''
        this.itSignatureFileName = ''
        this.showNotification('Form has been reset', 'info')
      },

      printForm() {
        window.print()
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
        toast.className = `fixed top-4 right-4 px-6 py-3 rounded-lg text-white font-semibold z-50 bg-${colors[type]}-600`
        toast.textContent = message
        document.body.appendChild(toast)

        setTimeout(() => {
          document.body.removeChild(toast)
        }, 3000)
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
        this.formData.employeeFullName = query.staffName || ''
        this.formData.pfNumber = query.pfNumber || ''
        this.formData.department = query.department || ''
        this.formData.designation = query.designation || ''

        // Handle internet purposes
        if (query.selectedModules) {
          try {
            const modules = JSON.parse(query.selectedModules)
            // For internet access, modules might be purposes
            this.formData.internetPurposes = modules.length > 0 ? modules : ['', '', '', '']
          } catch (e) {
            console.error('Error parsing selected modules:', e)
          }
        }

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
            if (!checkbox.classList.contains('approval-input')) {
              checkbox.setAttribute('disabled', true)
            }
          })
        })
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
            return query.hodApprovalStatus === 'approved' && query.divisionalStatus === 'pending'
          case this.ROLES.ICT_DIRECTOR:
            return query.divisionalStatus === 'approved' && query.dictStatus === 'pending'
          case this.ROLES.HOD_IT:
            return query.dictStatus === 'approved' && query.headOfItStatus === 'pending'
          case this.ROLES.ICT_OFFICER:
            return query.headOfItStatus === 'approved' && query.ictStatus === 'pending'
          default:
            return false
        }
      },

      approveRequest() {
        if (!this.canApproveAtStage()) {
          this.showNotification('You cannot approve this request at this stage.', 'error')
          return
        }

        if (confirm(`Are you sure you want to approve request ${this.requestId}?`)) {
          // Here you would make an API call to approve the request
          console.log('Approving request:', this.requestId)
          this.showNotification('Request approved successfully!', 'success')

          // Redirect back to requests list
          setTimeout(() => {
            this.router.push('/hod-dashboard/request-list')
          }, 2000)
        }
      },

      rejectRequest() {
        if (!this.canApproveAtStage()) {
          this.showNotification('You cannot reject this request at this stage.', 'error')
          return
        }

        const reason = prompt('Please provide a reason for rejection:')
        if (reason && confirm(`Are you sure you want to reject request ${this.requestId}?`)) {
          // Here you would make an API call to reject the request
          console.log('Rejecting request:', this.requestId, 'Reason:', reason)
          this.showNotification('Request rejected successfully!', 'success')

          // Redirect back to requests list
          setTimeout(() => {
            this.router.push('/hod-dashboard/request-list')
          }, 2000)
        }
      },

      goBackToRequests() {
        this.router.push('/hod-dashboard/request-list')
      },

      goBack() {
        if (this.isReviewMode) {
          this.goBackToRequests()
        } else {
          this.$router.push('/user-dashboard')
        }
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
    border-color: rgba(45, 212, 191, 0.8);
    box-shadow: 0 0 0 3px rgba(45, 212, 191, 0.2);
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

  /* Static Grid Background - No Animations */

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
      border-left: 2px solid #000 !important;
    }

    .bg-blue-50,
    .bg-green-50,
    .bg-purple-50,
    .bg-orange-50 {
      background: #f9f9f9 !important;
    }

    .text-white {
      color: #000 !important;
    }

    .bg-blue-600,
    .bg-green-500,
    .bg-purple-500,
    .bg-orange-500 {
      background: #000 !important;
      color: #fff !important;
    }
  }

  /* Responsive adjustments */
  @media (max-width: 768px) {
    .grid-cols-3 {
      grid-template-columns: repeat(1, minmax(0, 1fr));
    }

    .grid-cols-2 {
      grid-template-columns: repeat(1, minmax(0, 1fr));
    }

    .md\:col-span-2 {
      grid-column: span 1;
    }
  }

  /* Custom input focus styles */
  input:focus,
  select:focus,
  textarea:focus {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  }

  /* Radio button custom styling */
  input[type='radio']:checked {
    background-color: currentColor;
    border-color: currentColor;
  }

  /* Animation for form sections */
  .border-l-2 {
    animation: slideInLeft 0.5s ease-out;
  }

  @keyframes slideInLeft {
    from {
      opacity: 0;
      transform: translateX(-20px);
    }
    to {
      opacity: 1;
      transform: translateX(0);
    }
  }

  /* Hover effects for form sections */
  .border-l-2:hover {
    transform: translateX(3px);
    transition: transform 0.2s ease;
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

  /* Custom scrollbar for textarea */
  textarea::-webkit-scrollbar {
    width: 4px;
  }

  textarea::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 2px;
  }

  textarea::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 2px;
  }

  textarea::-webkit-scrollbar-thumb:hover {
    background: #a1a1a1;
  }
</style>
