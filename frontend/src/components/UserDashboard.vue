<template>
  <div class="flex flex-col h-screen">
    <AppHeader />
    <div class="flex flex-1 overflow-hidden">
      <DynamicSidebar v-model:collapsed="sidebarCollapsed" />
      <main
        class="flex-1 p-2 bg-gradient-to-br from-blue-900 via-blue-800 to-teal-900 overflow-y-auto relative"
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

        <div class="max-w-full mx-auto relative z-10">
          <!-- Main Content -->
          <div class="medical-glass-card rounded-3xl overflow-hidden">
            <div class="p-6 space-y-6">
              <!-- Enhanced Multi-Layer Request Access Section -->
              <div
                class="medical-card bg-gradient-to-r from-blue-600/25 to-blue-600/25 border-2 border-blue-400/40 p-6 rounded-2xl backdrop-blur-sm hover:shadow-2xl hover:shadow-blue-500/20 transition-all duration-500 group relative overflow-hidden"
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
                  <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                      <!-- Multi-Layer Icon Design -->
                      <div class="relative">
                        <div
                          class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-blue-300/50 relative overflow-hidden"
                        >
                          <!-- Inner Glow Layer -->
                          <div
                            class="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent rounded-xl"
                          ></div>
                          <!-- Icon with Drop Shadow -->
                          <i
                            class="fas fa-file-alt text-white text-xl relative z-10 drop-shadow-lg"
                          ></i>
                          <!-- Animated Particles -->
                          <div
                            class="absolute top-1 right-1 w-1 h-1 bg-white/60 rounded-full animate-ping"
                          ></div>
                          <div
                            class="absolute bottom-2 left-2 w-0.5 h-0.5 bg-blue-300/80 rounded-full animate-pulse"
                          ></div>
                        </div>
                        <!-- Pulsing Ring Effect -->
                        <div
                          class="absolute inset-0 rounded-xl border-2 border-blue-400/50 animate-ping opacity-75"
                        ></div>
                        <!-- Outer Glow -->
                        <div
                          class="absolute -inset-1 rounded-xl bg-gradient-to-r from-blue-500/20 to-blue-500/20 blur-sm group-hover:blur-md transition-all duration-300"
                        ></div>
                      </div>
                    </div>

                    <div class="flex-1">
                      <!-- Enhanced Title with Multi-Layer Effects -->
                      <div class="relative mb-6">
                        <h3
                          class="text-2xl font-bold text-white mb-2 flex items-center relative z-10"
                        >
                          <i
                            class="fas fa-plus-circle mr-3 text-blue-300 text-lg animate-pulse"
                          ></i>
                          <span
                            class="bg-gradient-to-r from-white to-blue-100 bg-clip-text text-transparent drop-shadow-lg"
                            >Request Access</span
                          >
                        </h3>
                        <!-- Title Glow Effect -->
                        <div
                          class="absolute -inset-2 bg-gradient-to-r from-blue-500/20 to-blue-500/20 blur-xl opacity-50 group-hover:opacity-75 transition-opacity duration-500"
                        ></div>
                        <p class="text-blue-100/80 text-sm relative z-10">
                          Submit new access requests for hospital systems and
                          services
                        </p>
                      </div>

                      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Enhanced Quick Access Forms -->
                        <div class="relative">
                          <label
                            class="block text-sm font-bold text-blue-100 mb-3 flex items-center"
                          >
                            <i
                              class="fas fa-rocket mr-2 text-blue-300 animate-bounce"
                            ></i>
                            Quick Access services
                          </label>
                          <div class="relative group/button">
                            <button
                              @click="showFormSelector = true"
                              class="medical-input w-full px-6 py-5 bg-gradient-to-r from-blue-600 to-blue-700 border-2 border-blue-400/60 rounded-xl focus:border-blue-300 focus:outline-none text-white backdrop-blur-sm transition-all duration-300 hover:from-blue-700 hover:to-blue-800 hover:border-blue-300/80 focus:shadow-lg focus:shadow-blue-500/30 text-left relative overflow-hidden group/inner shadow-lg hover:shadow-xl hover:shadow-blue-500/25 transform hover:scale-[1.02] active:scale-[0.98]"
                            >
                              <!-- Multi-Layer Button Background -->
                              <div
                                class="absolute inset-0 bg-gradient-to-r from-blue-500/20 to-blue-600/20 opacity-0 group-hover/button:opacity-100 transition-opacity duration-300"
                              ></div>
                              <div
                                class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-blue-300/60 to-transparent group-hover/button:animate-pulse"
                              ></div>
                              <div
                                class="absolute bottom-0 right-0 w-24 h-24 bg-gradient-to-tl from-blue-400/15 to-transparent rounded-full blur-xl group-hover/button:scale-150 transition-transform duration-500"
                              ></div>

                              <!-- Button Shine Effect -->
                              <div
                                class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -skew-x-12 -translate-x-full group-hover/button:translate-x-full transition-transform duration-700"
                              ></div>

                              <div class="relative z-10 flex items-center">
                                <!-- Enhanced Icon Container -->
                                <div
                                  class="w-6 h-12 bg-gradient-to-br from-blue-400/40 to-blue-600/40 rounded-lg flex items-center justify-center mr-4 group-hover/button:scale-110 transition-transform duration-300 relative overflow-hidden border border-blue-300/30"
                                >
                                  <div
                                    class="absolute inset-0 bg-gradient-to-br from-white/30 to-transparent"
                                  ></div>
                                  <i
                                    class="fas fa-file-alt text-white text-lg relative z-10 drop-shadow-sm"
                                  ></i>
                                  <!-- Icon Glow -->
                                  <div
                                    class="absolute inset-0 bg-blue-400/20 rounded-lg blur-sm opacity-0 group-hover/button:opacity-100 transition-opacity duration-300"
                                  ></div>
                                </div>
                                <div class="flex-1">
                                  <span
                                    class="font-bold text-white text-lg drop-shadow-sm"
                                    >Click to Select a Service</span
                                  >
                                  <!-- <div class="text-sm text-blue-100/90 mt-1 font-medium">Choose from available request forms</div> -->
                                </div>
                                <!-- Enhanced Animated Arrow -->
                                <div class="relative ml-3">
                                  <div
                                    class="w-10 h-10 bg-gradient-to-br from-blue-400/30 to-blue-600/30 rounded-full flex items-center justify-center group-hover/button:bg-gradient-to-br group-hover/button:from-blue-300/40 group-hover/button:to-blue-500/40 transition-all duration-300 border border-blue-300/40"
                                  >
                                    <i
                                      class="fas fa-chevron-right text-white group-hover/button:translate-x-0.5 transition-transform duration-300 drop-shadow-sm"
                                    ></i>
                                  </div>
                                  <div
                                    class="absolute inset-0 bg-blue-400/30 rounded-full blur-md opacity-0 group-hover/button:opacity-100 transition-opacity duration-300"
                                  ></div>
                                </div>
                              </div>

                              <!-- Button Press Effect -->
                              <div
                                class="absolute inset-0 bg-gradient-to-r from-blue-800/50 to-blue-800/50 opacity-0 group-active/button:opacity-100 transition-opacity duration-150 rounded-xl"
                              ></div>
                            </button>

                            <!-- Enhanced Multi-Layer Hover Effects -->
                            <div
                              class="absolute inset-0 rounded-xl bg-gradient-to-r from-blue-500/30 to-blue-600/30 opacity-0 group-hover/button:opacity-100 transition-opacity duration-300 pointer-events-none blur-sm"
                            ></div>
                            <div
                              class="absolute -inset-0.5 rounded-xl bg-gradient-to-r from-blue-400/40 to-blue-500/40 opacity-0 group-hover/button:opacity-100 transition-opacity duration-300 pointer-events-none blur-md"
                            ></div>
                            <div
                              class="absolute -inset-1 rounded-xl bg-gradient-to-r from-blue-300/30 to-blue-400/30 opacity-0 group-hover/button:opacity-100 transition-opacity duration-300 pointer-events-none blur-lg"
                            ></div>

                            <!-- Button Depth Shadow -->
                            <div
                              class="absolute inset-0 rounded-xl shadow-lg group-hover/button:shadow-xl group-hover/button:shadow-blue-500/25 transition-shadow duration-300 pointer-events-none"
                            ></div>
                          </div>
                        </div>

                        <!-- Enhanced Request Status -->
                        <div class="relative">
                          <label
                            class="block text-sm font-bold text-blue-100 mb-3 flex items-center"
                          >
                            <i
                              class="fas fa-chart-line mr-2 text-blue-300 animate-pulse"
                            ></i>
                            Request Status
                          </label>
                          <div class="relative group/status">
                            <div
                              class="medical-input w-full px-5 py-4 bg-white/15 border-2 border-blue-300/30 rounded-xl text-white backdrop-blur-sm relative overflow-hidden"
                            >
                              <!-- Multi-Layer Status Background -->
                              <div
                                class="absolute inset-0 bg-gradient-to-r from-blue-500/10 to-blue-500/10"
                              ></div>
                              <div
                                class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-blue-400/20 to-transparent rounded-full blur-xl"
                              ></div>
                              <div
                                class="absolute bottom-0 left-0 w-16 h-16 bg-gradient-to-tr from-blue-400/15 to-transparent rounded-full blur-lg"
                              ></div>

                              <div class="relative z-10">
                                <div
                                  class="flex items-center justify-between mb-3"
                                >
                                  <div class="flex items-center space-x-2">
                                    <!-- Enhanced Step Icon -->
                                    <div
                                      class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-500 rounded-lg flex items-center justify-center relative overflow-hidden"
                                    >
                                      <div
                                        class="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent"
                                      ></div>
                                      <i
                                        class="fas fa-tasks text-white text-xs relative z-10"
                                      ></i>
                                    </div>
                                    <span class="font-medium text-sm text-white"
                                      >Current Step: {{ currentStep }} of
                                      {{ steps.length }}</span
                                    >
                                  </div>

                                  <!-- Enhanced Active Status with Multi-Layer Design -->
                                  <div
                                    class="flex items-center space-x-2 bg-blue-500/20 px-3 py-1.5 rounded-full border border-blue-400/30 relative overflow-hidden"
                                  >
                                    <!-- Status Background Glow -->
                                    <div
                                      class="absolute inset-0 bg-gradient-to-r from-blue-400/10 to-blue-400/10"
                                    ></div>
                                    <div
                                      class="relative flex items-center space-x-2"
                                    >
                                      <!-- Multi-Layer Pulse Indicator -->
                                      <div class="relative">
                                        <div
                                          class="w-2 h-2 bg-blue-400 rounded-full animate-pulse relative z-10"
                                        ></div>
                                        <div
                                          class="absolute inset-0 w-2 h-2 bg-blue-400 rounded-full animate-ping opacity-75"
                                        ></div>
                                        <div
                                          class="absolute -inset-0.5 w-3 h-3 bg-blue-400/30 rounded-full blur-sm"
                                        ></div>
                                      </div>
                                      <span
                                        class="text-xs text-blue-300 font-medium relative z-10"
                                        >Active</span
                                      >
                                    </div>
                                  </div>
                                </div>

                                <!-- Enhanced Progress Indicator with Multi-Layer Design -->
                                <div class="mt-4">
                                  <div
                                    class="flex items-center justify-between text-xs text-blue-200 mb-2"
                                  >
                                    <span class="flex items-center space-x-1">
                                      <i
                                        class="fas fa-chart-bar text-blue-300"
                                      ></i>
                                      <span>Progress</span>
                                    </span>
                                    <span class="font-bold text-blue-300"
                                      >{{ progressPercentage }}%</span
                                    >
                                  </div>
                                  <!-- Multi-Layer Progress Bar -->
                                  <div class="relative">
                                    <div
                                      class="w-full bg-white/20 rounded-full h-2 overflow-hidden relative"
                                    >
                                      <!-- Background Glow -->
                                      <div
                                        class="absolute inset-0 bg-gradient-to-r from-blue-500/20 to-blue-500/20 blur-sm"
                                      ></div>
                                      <!-- Progress Fill with Multiple Layers -->
                                      <div
                                        class="h-full bg-gradient-to-r from-blue-400 via-blue-400 to-blue-400 rounded-full transition-all duration-1000 ease-out relative"
                                        :style="{
                                          width: progressPercentage + '%',
                                        }"
                                      >
                                        <!-- Inner Highlight -->
                                        <div
                                          class="absolute inset-0 bg-gradient-to-r from-white/30 to-transparent animate-pulse"
                                        ></div>
                                        <!-- Top Shine -->
                                        <div
                                          class="absolute top-0 left-0 right-0 h-0.5 bg-gradient-to-r from-white/50 to-transparent rounded-full"
                                        ></div>
                                      </div>
                                    </div>
                                    <!-- Progress Glow Effect -->
                                    <div
                                      class="absolute inset-0 bg-gradient-to-r from-blue-400/20 via-blue-400/20 to-blue-400/20 rounded-full blur-md opacity-50"
                                      :style="{
                                        width: progressPercentage + '%',
                                      }"
                                    ></div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Enhanced Multi-Layer Statistics Section -->
              <div
                class="medical-card bg-gradient-to-r from-blue-600/25 to-blue-600/25 border-2 border-blue-400/40 p-6 rounded-2xl backdrop-blur-sm hover:shadow-2xl hover:shadow-blue-500/20 transition-all duration-500 group relative overflow-hidden"
              >
                <!-- Animated Background Layers -->
                <div
                  class="absolute inset-0 bg-gradient-to-r from-blue-500/10 via-blue-500/5 to-blue-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-700"
                ></div>
                <div
                  class="absolute bottom-0 left-0 w-40 h-40 bg-gradient-to-tr from-blue-400/20 to-transparent rounded-full blur-2xl group-hover:scale-125 transition-transform duration-1000"
                ></div>
                <div
                  class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-blue-400/15 to-transparent rounded-full blur-xl group-hover:scale-110 transition-transform duration-800"
                ></div>

                <div class="relative z-10">
                  <!-- Enhanced Header with Multi-Layer Design -->
                  <div class="flex items-center space-x-4 mb-8">
                    <div class="relative">
                      <!-- Multi-Layer Icon Design -->
                      <div
                        class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-blue-300/50 relative overflow-hidden"
                      >
                        <!-- Inner Glow Layer -->
                        <div
                          class="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent"
                        ></div>
                        <!-- Icon with Enhanced Effects -->
                        <i
                          class="fas fa-chart-bar text-white text-xl relative z-10 drop-shadow-lg"
                        ></i>
                        <!-- Animated Particles -->
                        <div
                          class="absolute top-1 right-1 w-1 h-1 bg-white/60 rounded-full animate-ping"
                        ></div>
                        <div
                          class="absolute bottom-2 left-2 w-0.5 h-0.5 bg-blue-300/80 rounded-full animate-pulse"
                        ></div>
                        <div
                          class="absolute top-3 left-1 w-0.5 h-0.5 bg-blue-300/60 rounded-full animate-bounce"
                        ></div>
                      </div>
                      <!-- Orbital Ring Animation -->
                      <div
                        class="absolute inset-0 rounded-xl border border-blue-400/30 animate-spin-slow"
                      ></div>
                      <!-- Outer Glow -->
                      <div
                        class="absolute -inset-1 rounded-xl bg-gradient-to-r from-blue-500/20 to-blue-500/20 blur-sm group-hover:blur-md transition-all duration-300"
                      ></div>
                    </div>

                    <div class="flex-1">
                      <!-- Enhanced Title -->
                      <h3
                        class="text-2xl font-bold text-white flex items-center mb-2"
                      >
                        <i
                          class="fas fa-analytics mr-3 text-blue-300 animate-pulse"
                        ></i>
                        <span
                          class="bg-gradient-to-r from-white to-blue-100 bg-clip-text text-transparent"
                          >Request Statistics</span
                        >
                      </h3>
                      <!-- Enhanced Total Counter with Multi-Layer Design -->
                      <div class="flex items-center space-x-4">
                        <div
                          class="bg-blue-500/20 px-4 py-2 rounded-full border border-blue-400/30 relative overflow-hidden"
                        >
                          <!-- Counter Background Glow -->
                          <div
                            class="absolute inset-0 bg-gradient-to-r from-blue-400/10 to-blue-400/10"
                          ></div>
                          <div class="relative flex items-center space-x-2">
                            <span class="text-blue-300 font-bold text-lg">{{
                              totalRequests
                            }}</span>
                            <span class="text-blue-200 text-sm"
                              >Total Requests</span
                            >
                          </div>
                        </div>
                        <!-- Live Data Indicator -->
                        <div class="flex items-center space-x-2">
                          <div class="relative">
                            <div
                              class="w-2 h-2 bg-blue-400 rounded-full animate-pulse"
                            ></div>
                            <div
                              class="absolute inset-0 w-2 h-2 bg-blue-400 rounded-full animate-ping opacity-75"
                            ></div>
                          </div>
                          <span class="text-xs text-blue-300 font-medium"
                            >Live Data</span
                          >
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Loading State -->
                  <div v-if="loading" class="text-center py-8">
                    <div
                      class="inline-flex items-center space-x-2 text-blue-100"
                    >
                      <i class="fas fa-spinner fa-spin text-xl"></i>
                      <span>Loading statistics...</span>
                    </div>
                  </div>

                  <!-- Enhanced Multi-Layer Statistics Grid -->
                  <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Enhanced Pending Requests Card -->
                    <div
                      class="bg-white/15 p-5 rounded-xl backdrop-blur-sm border border-blue-300/30 hover:bg-white/25 transition-all duration-300 shadow-lg hover:shadow-xl hover:shadow-blue-500/25 relative overflow-hidden group/card"
                    >
                      <!-- Multi-Layer Card Background Effects -->
                      <div
                        class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-transparent opacity-0 group-hover/card:opacity-100 transition-opacity duration-300"
                      ></div>
                      <div
                        class="absolute top-0 right-0 w-16 h-16 bg-gradient-to-br from-blue-400/20 to-transparent rounded-full blur-xl"
                      ></div>
                      <div
                        class="absolute bottom-0 left-0 w-12 h-12 bg-gradient-to-tr from-blue-400/15 to-transparent rounded-full blur-lg"
                      ></div>

                      <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                          <div class="flex items-center space-x-3">
                            <!-- Enhanced Multi-Layer Icon -->
                            <div class="relative">
                              <div
                                class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-lg group-hover/card:scale-110 transition-transform duration-300 relative overflow-hidden"
                              >
                                <div
                                  class="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent"
                                ></div>
                                <i
                                  class="fas fa-clock text-white text-sm relative z-10"
                                ></i>
                              </div>
                              <div
                                class="absolute -inset-0.5 bg-gradient-to-br from-blue-400/50 to-blue-500/50 rounded-lg blur-sm opacity-0 group-hover/card:opacity-100 transition-opacity duration-300"
                              ></div>
                            </div>
                            <div>
                              <h4 class="text-sm font-bold text-white">
                                PENDING
                              </h4>
                              <div
                                class="w-10 h-0.5 bg-gradient-to-r from-blue-400 to-blue-400 rounded-full"
                              ></div>
                            </div>
                          </div>
                          <!-- Enhanced Status Indicator -->
                          <div
                            class="flex items-center space-x-1 bg-blue-500/20 px-2 py-1 rounded-full border border-blue-400/30"
                          >
                            <div
                              class="w-1.5 h-1.5 bg-blue-400 rounded-full animate-pulse"
                            ></div>
                            <span class="text-xs text-blue-300 font-medium"
                              >Active</span
                            >
                          </div>
                        </div>

                        <!-- Enhanced Number Display with Multi-Layer Effects -->
                        <div class="mb-4">
                          <div class="flex items-baseline space-x-2">
                            <div
                              class="text-4xl font-bold text-blue-400 relative"
                            >
                              56
                              <!-- Number Glow Effect -->
                              <div
                                class="absolute -inset-1 bg-blue-400/20 blur-lg opacity-50 group-hover/card:opacity-75 transition-opacity duration-300"
                              ></div>
                            </div>
                            <!-- Trend Indicator -->
                            <div
                              class="flex items-center space-x-1 text-blue-300 bg-blue-500/20 px-2 py-1 rounded-full"
                            >
                              <i class="fas fa-arrow-up text-xs"></i>
                              <span class="text-xs font-medium">+12%</span>
                            </div>
                          </div>
                          <p class="text-xs text-blue-200 mt-2">
                            Awaiting approval
                          </p>
                        </div>

                        <!-- Enhanced Progress Bar with Multi-Layer Design -->
                        <div class="relative">
                          <div
                            class="w-full bg-blue-900/30 rounded-full h-1.5 overflow-hidden relative"
                          >
                            <div
                              class="absolute inset-0 bg-gradient-to-r from-blue-500/20 to-blue-500/20 blur-sm"
                            ></div>
                            <div
                              class="h-full bg-gradient-to-r from-blue-400 to-blue-400 rounded-full relative"
                              :style="{ width: '65%' }"
                            >
                              <div
                                class="absolute inset-0 bg-gradient-to-r from-white/30 to-transparent animate-pulse"
                              ></div>
                              <div
                                class="absolute top-0 left-0 right-0 h-0.5 bg-gradient-to-r from-white/50 to-transparent rounded-full"
                              ></div>
                            </div>
                          </div>
                          <div
                            class="absolute inset-0 bg-gradient-to-r from-blue-400/20 to-blue-400/20 rounded-full blur-md opacity-50"
                            :style="{ width: '65%' }"
                          ></div>
                        </div>
                      </div>
                    </div>

                    <!-- Enhanced Approved Requests Card -->
                    <div
                      class="bg-white/15 p-3 rounded-lg backdrop-blur-sm border border-blue-300/30 hover:bg-white/25 transition-all duration-300 shadow-lg hover:shadow-xl hover:shadow-blue-500/25 relative overflow-hidden group/card"
                    >
                      <!-- Multi-Layer Card Background Effects -->
                      <div
                        class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-transparent opacity-0 group-hover/card:opacity-100 transition-opacity duration-300"
                      ></div>
                      <div
                        class="absolute top-0 right-0 w-16 h-16 bg-gradient-to-br from-blue-400/20 to-transparent rounded-full blur-xl"
                      ></div>
                      <div
                        class="absolute bottom-0 left-0 w-12 h-12 bg-gradient-to-tr from-blue-400/15 to-transparent rounded-full blur-lg"
                      ></div>

                      <div class="relative z-10">
                        <div class="flex items-center justify-between mb-3">
                          <div class="flex items-center space-x-2">
                            <!-- Enhanced Multi-Layer Icon -->
                            <div class="relative">
                              <div
                                class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-lg group-hover/card:scale-110 transition-transform duration-300 relative overflow-hidden"
                              >
                                <div
                                  class="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent"
                                ></div>
                                <i
                                  class="fas fa-check-circle text-white text-xs relative z-10"
                                ></i>
                              </div>
                              <div
                                class="absolute -inset-0.5 bg-gradient-to-br from-blue-400/50 to-blue-500/50 rounded-lg blur-sm opacity-0 group-hover/card:opacity-100 transition-opacity duration-300"
                              ></div>
                            </div>
                            <div>
                              <h4 class="text-xs font-bold text-white">
                                APPROVED
                              </h4>
                              <div
                                class="w-8 h-0.5 bg-gradient-to-r from-blue-400 to-blue-400 rounded-full"
                              ></div>
                            </div>
                          </div>
                          <!-- Enhanced Status Indicator -->
                          <div
                            class="flex items-center space-x-1 bg-blue-500/20 px-1 py-0.5 rounded-full border border-blue-400/30"
                          >
                            <div
                              class="w-1.5 h-1.5 bg-blue-400 rounded-full animate-pulse"
                            ></div>
                            <span class="text-xs text-blue-300 font-medium"
                              >Success</span
                            >
                          </div>
                        </div>

                        <!-- Enhanced Number Display with Multi-Layer Effects -->
                        <div class="mb-3">
                          <div class="flex items-baseline space-x-2">
                            <div
                              class="text-2xl font-bold text-blue-400 relative"
                            >
                              1,234
                              <!-- Number Glow Effect -->
                              <div
                                class="absolute -inset-1 bg-blue-400/20 blur-lg opacity-50 group-hover/card:opacity-75 transition-opacity duration-300"
                              ></div>
                            </div>
                            <!-- Trend Indicator -->
                            <div
                              class="flex items-center space-x-1 text-blue-300 bg-blue-500/20 px-1 py-0.5 rounded-full"
                            >
                              <i class="fas fa-arrow-up text-xs"></i>
                              <span class="text-xs font-medium">+8%</span>
                            </div>
                          </div>
                          <p class="text-xs text-blue-200 mt-1">
                            Successfully processed
                          </p>
                        </div>

                        <!-- Enhanced Progress Bar with Multi-Layer Design -->
                        <div class="relative">
                          <div
                            class="w-full bg-blue-900/30 rounded-full h-1.5 overflow-hidden relative"
                          >
                            <div
                              class="absolute inset-0 bg-gradient-to-r from-blue-500/20 to-blue-500/20 blur-sm"
                            ></div>
                            <div
                              class="h-full bg-gradient-to-r from-blue-400 to-blue-400 rounded-full relative"
                              :style="{ width: '89%' }"
                            >
                              <div
                                class="absolute inset-0 bg-gradient-to-r from-white/30 to-transparent animate-pulse"
                              ></div>
                              <div
                                class="absolute top-0 left-0 right-0 h-0.5 bg-gradient-to-r from-white/50 to-transparent rounded-full"
                              ></div>
                            </div>
                          </div>
                          <div
                            class="absolute inset-0 bg-gradient-to-r from-blue-400/20 to-blue-400/20 rounded-full blur-md opacity-50"
                            :style="{ width: '89%' }"
                          ></div>
                        </div>
                      </div>
                    </div>

                    <!-- Enhanced Rejected Requests Card -->
                    <div
                      class="bg-white/15 p-3 rounded-lg backdrop-blur-sm border border-blue-300/30 hover:bg-white/25 transition-all duration-300 shadow-lg hover:shadow-xl hover:shadow-blue-500/25 relative overflow-hidden group/card"
                    >
                      <!-- Multi-Layer Card Background Effects -->
                      <div
                        class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-transparent opacity-0 group-hover/card:opacity-100 transition-opacity duration-300"
                      ></div>
                      <div
                        class="absolute top-0 right-0 w-16 h-16 bg-gradient-to-br from-blue-400/20 to-transparent rounded-full blur-xl"
                      ></div>
                      <div
                        class="absolute bottom-0 left-0 w-12 h-12 bg-gradient-to-tr from-blue-400/15 to-transparent rounded-full blur-lg"
                      ></div>

                      <div class="relative z-10">
                        <div class="flex items-center justify-between mb-3">
                          <div class="flex items-center space-x-2">
                            <!-- Enhanced Multi-Layer Icon -->
                            <div class="relative">
                              <div
                                class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-lg group-hover/card:scale-110 transition-transform duration-300 relative overflow-hidden"
                              >
                                <div
                                  class="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent"
                                ></div>
                                <i
                                  class="fas fa-times-circle text-white text-xs relative z-10"
                                ></i>
                              </div>
                              <div
                                class="absolute -inset-0.5 bg-gradient-to-br from-blue-400/50 to-blue-500/50 rounded-lg blur-sm opacity-0 group-hover/card:opacity-100 transition-opacity duration-300"
                              ></div>
                            </div>
                            <div>
                              <h4 class="text-xs font-bold text-white">
                                REJECTED
                              </h4>
                              <div
                                class="w-8 h-0.5 bg-gradient-to-r from-blue-400 to-blue-400 rounded-full"
                              ></div>
                            </div>
                          </div>
                          <!-- Enhanced Status Indicator -->
                          <div
                            class="flex items-center space-x-1 bg-blue-500/20 px-1 py-0.5 rounded-full border border-blue-400/30"
                          >
                            <div
                              class="w-1.5 h-1.5 bg-blue-400 rounded-full animate-pulse"
                            ></div>
                            <span class="text-xs text-blue-300 font-medium"
                              >Closed</span
                            >
                          </div>
                        </div>

                        <!-- Enhanced Number Display with Multi-Layer Effects -->
                        <div class="mb-3">
                          <div class="flex items-baseline space-x-2">
                            <div
                              class="text-2xl font-bold text-blue-400 relative"
                            >
                              89
                              <!-- Number Glow Effect -->
                              <div
                                class="absolute -inset-1 bg-blue-400/20 blur-lg opacity-50 group-hover/card:opacity-75 transition-opacity duration-300"
                              ></div>
                            </div>
                            <!-- Trend Indicator -->
                            <div
                              class="flex items-center space-x-1 text-blue-300 bg-blue-500/20 px-1 py-0.5 rounded-full"
                            >
                              <i class="fas fa-arrow-down text-xs"></i>
                              <span class="text-xs font-medium">-3%</span>
                            </div>
                          </div>
                          <p class="text-xs text-blue-200 mt-1">
                            Cancelled or rejected
                          </p>
                        </div>

                        <!-- Enhanced Progress Bar with Multi-Layer Design -->
                        <div class="relative">
                          <div
                            class="w-full bg-blue-900/30 rounded-full h-1.5 overflow-hidden relative"
                          >
                            <div
                              class="absolute inset-0 bg-gradient-to-r from-blue-500/20 to-blue-500/20 blur-sm"
                            ></div>
                            <div
                              class="h-full bg-gradient-to-r from-blue-400 to-blue-400 rounded-full relative"
                              :style="{ width: '25%' }"
                            >
                              <div
                                class="absolute inset-0 bg-gradient-to-r from-white/30 to-transparent animate-pulse"
                              ></div>
                              <div
                                class="absolute top-0 left-0 right-0 h-0.5 bg-gradient-to-r from-white/50 to-transparent rounded-full"
                              ></div>
                            </div>
                          </div>
                          <div
                            class="absolute inset-0 bg-gradient-to-r from-blue-400/20 to-blue-400/20 rounded-full blur-md opacity-50"
                            :style="{ width: '25%' }"
                          ></div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Progress Tracking Section -->
              <div
                class="medical-card bg-gradient-to-r from-blue-600/25 to-blue-600/25 border-2 border-blue-400/40 p-6 rounded-2xl backdrop-blur-sm hover:shadow-2xl hover:shadow-blue-500/20 transition-all duration-500 group"
              >
                <div class="flex items-center space-x-4 mb-6">
                  <div
                    class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-blue-300/50"
                  >
                    <i class="fas fa-route text-white text-xl"></i>
                  </div>
                  <h3 class="text-xl font-bold text-white flex items-center">
                    <i class="fas fa-clipboard-check mr-2 text-blue-300"></i>
                    Request Approval Process
                  </h3>
                </div>

                <!-- Desktop Progress -->
                <div class="hidden md:block">
                  <div class="flex items-center justify-between relative mb-8">
                    <!-- Progress Line Background -->
                    <div
                      class="absolute top-6 left-0 w-full h-1 bg-white/20 rounded-full"
                    ></div>

                    <!-- Active Progress Line -->
                    <div
                      class="absolute top-6 left-0 h-1 bg-gradient-to-r from-blue-400 to-blue-500 rounded-full transition-all duration-500 ease-in-out shadow-lg"
                      :style="{ width: progressWidth }"
                    ></div>

                    <!-- Steps -->
                    <div
                      v-for="step in steps"
                      :key="step.id"
                      class="flex flex-col items-center relative z-10 cursor-pointer flex-1"
                      @click="handleStepClick(step.id)"
                    >
                      <!-- Step Circle -->
                      <div
                        class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold transition-all duration-300 mb-3"
                        :class="getStepCircleClass(step.id)"
                      >
                        <!-- Check Icon for Completed Steps -->
                        <i v-if="isCompleted(step.id)" class="fas fa-check"></i>
                        <span v-else>{{ step.id }}</span>
                      </div>

                      <!-- Step Label -->
                      <div class="text-center w-full px-1">
                        <p
                          class="text-xs font-medium transition-colors duration-300 leading-tight"
                          :class="getStepLabelClass(step.id)"
                        >
                          {{ step.label }}
                        </p>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Mobile Progress -->
                <div class="md:hidden">
                  <div class="space-y-4">
                    <div
                      v-for="step in steps"
                      :key="step.id"
                      class="flex items-center p-3 rounded-lg transition-all duration-300"
                      :class="getMobileStepClass(step.id)"
                      @click="handleStepClick(step.id)"
                    >
                      <!-- Step Circle -->
                      <div
                        class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold mr-4 transition-all duration-300 flex-shrink-0"
                        :class="getStepCircleClass(step.id)"
                      >
                        <i
                          v-if="isCompleted(step.id)"
                          class="fas fa-check text-sm"
                        ></i>
                        <span v-else class="text-sm">{{ step.id }}</span>
                      </div>

                      <!-- Step Content -->
                      <div class="flex-1">
                        <h3
                          class="font-medium transition-colors duration-300"
                          :class="getStepLabelClass(step.id)"
                        >
                          {{ step.label }}
                        </h3>
                        <p class="text-xs text-blue-200 mt-1">
                          {{ step.description }}
                        </p>
                      </div>

                      <!-- Status Icon -->
                      <div class="ml-4">
                        <i
                          v-if="isCompleted(step.id)"
                          class="fas fa-check-circle text-blue-400 text-lg"
                        ></i>
                        <i
                          v-else-if="isCurrent(step.id)"
                          class="fas fa-clock text-blue-400 text-lg animate-pulse"
                        ></i>
                        <i
                          v-else
                          class="fas fa-circle text-white/30 text-lg"
                        ></i>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Progress Summary -->
                <div
                  class="mt-6 bg-white/15 rounded-xl p-4 backdrop-blur-sm border border-blue-300/30"
                >
                  <div class="flex items-center justify-between">
                    <div>
                      <p class="text-sm font-medium text-white">
                        Step {{ currentStep }} of {{ steps.length }}:
                        <span class="text-blue-300">{{
                          getCurrentStepLabel()
                        }}</span>
                      </p>
                      <p class="text-xs text-blue-200 mt-1">
                        {{ getCurrentStepDescription() }}
                      </p>
                    </div>
                    <div class="text-right">
                      <div class="text-lg font-bold text-blue-300">
                        {{ progressPercentage }}%
                      </div>
                      <div class="text-xs text-blue-200">Complete</div>
                    </div>
                  </div>

                  <!-- Progress Bar -->
                  <div class="mt-3">
                    <div class="w-full bg-white/20 rounded-full h-2">
                      <div
                        class="bg-gradient-to-r from-blue-400 to-blue-500 h-2 rounded-full transition-all duration-500 ease-in-out shadow-lg"
                        :style="{ width: progressPercentage + '%' }"
                      ></div>
                    </div>
                  </div>
                </div>

                <!-- Control Buttons -->
                <div class="flex justify-center mt-6 space-x-4">
                  <button
                    @click="previousStep"
                    :disabled="currentStep <= 1"
                    class="px-6 py-3 bg-gradient-to-r from-gray-500 to-gray-600 text-white rounded-xl font-medium hover:from-gray-600 hover:to-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-300 transform hover:scale-105 shadow-lg border border-gray-400/30"
                  >
                    <i class="fas fa-chevron-left mr-2"></i>
                    Previous
                  </button>
                  <button
                    @click="nextStep"
                    :disabled="currentStep >= 7"
                    class="px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-500 text-white rounded-xl font-medium hover:from-blue-600 hover:to-blue-600 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-300 transform hover:scale-105 shadow-lg border border-blue-400/30"
                  >
                    Next
                    <i class="fas fa-chevron-right ml-2"></i>
                  </button>
                  <button
                    @click="resetProgress"
                    class="px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-500 text-white rounded-xl font-medium hover:from-blue-600 hover:to-blue-600 transition-all duration-300 transform hover:scale-105 shadow-lg border border-blue-400/30"
                  >
                    <i class="fas fa-refresh mr-2"></i>
                    Reset
                  </button>
                </div>
              </div>

              <!-- Footer -->
              <AppFooter />
            </div>
          </div>
        </div>
      </main>
    </div>

    <!-- Form Selector Modal -->
    <div
      v-if="showFormSelector"
      class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 backdrop-blur-sm"
    >
      <div
        class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 transform transition-all duration-300 scale-100 animate-slideUp overflow-hidden modal-shadow"
      >
        <!-- Header -->
        <div
          class="bg-gradient-to-r from-blue-500 to-blue-600 p-6 text-center header-shadow"
        >
          <div
            class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4 backdrop-blur-sm border border-white/30 icon-shadow"
          >
            <i class="fas fa-file-alt text-white text-2xl drop-shadow-lg"></i>
          </div>
          <h3 class="text-xl font-bold text-white mb-2 drop-shadow-md">
            Select Access Form
          </h3>
          <div
            class="w-12 h-1 bg-white/50 mx-auto rounded-full shadow-sm"
          ></div>
        </div>

        <!-- Body -->
        <div class="p-6 text-center body-shadow">
          <div class="mb-6">
            <p class="text-gray-600 text-sm leading-relaxed">
              Choose the type of access request you want to submit
            </p>
          </div>

          <!-- Enhanced Form Options with Multi-Layer Design -->
          <div class="space-y-6">
            <!-- Combined Access Form Card -->
            <div class="relative group/card">
              <button
                @click="selectBothServices"
                :disabled="formLoading"
                class="w-full relative overflow-hidden bg-gradient-to-br from-blue-600 via-blue-700 to-blue-800 text-white p-6 rounded-2xl font-medium transition-all duration-500 transform hover:scale-[1.02] hover:-translate-y-1 disabled:opacity-50 disabled:cursor-not-allowed group/button"
              >
                <!-- Multi-Layer Background Effects -->
                <div class="absolute inset-0 bg-gradient-to-br from-blue-500/20 via-blue-600/10 to-blue-700/20 opacity-0 group-hover/button:opacity-100 transition-opacity duration-500"></div>
                <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-blue-400/30 to-transparent rounded-full blur-2xl group-hover/button:scale-150 transition-transform duration-700"></div>
                <div class="absolute bottom-0 left-0 w-24 h-24 bg-gradient-to-tr from-blue-300/20 to-transparent rounded-full blur-xl group-hover/button:scale-125 transition-transform duration-600"></div>

                <!-- Animated Shine Effect -->
                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -skew-x-12 -translate-x-full group-hover/button:translate-x-full transition-transform duration-1000"></div>

                <!-- Content Layer -->
                <div class="relative z-10 flex items-start space-x-4">
                  <!-- Enhanced Icon Container -->
                  <div class="relative flex-shrink-0">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-400/40 to-blue-600/40 rounded-xl flex items-center justify-center group-hover/button:scale-110 transition-transform duration-300 relative overflow-hidden border border-blue-300/30">
                      <!-- Icon Background Glow -->
                      <div class="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent"></div>
                      <!-- Multi-Layer Icon -->
                      <i class="fas fa-layer-group text-white text-2xl relative z-10 drop-shadow-lg"></i>
                      <!-- Animated Particles -->
                      <div class="absolute top-1 right-1 w-1.5 h-1.5 bg-white/60 rounded-full animate-ping"></div>
                      <div class="absolute bottom-2 left-2 w-1 h-1 bg-blue-300/80 rounded-full animate-pulse"></div>
                    </div>
                    <!-- Orbital Ring -->
                    <div class="absolute inset-0 rounded-xl border border-blue-400/30 animate-spin-slow opacity-50"></div>
                    <!-- Outer Glow -->
                    <div class="absolute -inset-1 rounded-xl bg-gradient-to-br from-blue-400/30 to-blue-600/30 blur-md opacity-0 group-hover/button:opacity-100 transition-opacity duration-300"></div>
                  </div>

                  <!-- Text Content -->
                  <div class="flex-1 text-left">
                    <h3 class="text-xl font-bold text-white mb-2 flex items-center">
                      <span class="bg-gradient-to-r from-white to-blue-100 bg-clip-text text-transparent drop-shadow-lg">Combined Access Form</span>
                      <!-- New Badge -->
                      <span class="ml-3 px-2 py-1 bg-blue-400/30 text-blue-100 text-xs rounded-full border border-blue-300/50 animate-pulse">Popular</span>
                    </h3>
                    <p class="text-blue-100/90 text-sm leading-relaxed mb-3">
                      Request access to Jeeva, Wellsoft, and Internet services
                    </p>
                    <!-- Feature Tags -->
                    <div class="flex flex-wrap gap-2">
                      <span class="px-2 py-1 bg-blue-500/30 text-blue-200 text-xs rounded-full border border-blue-400/40">All-in-One</span>
                      <span class="px-2 py-1 bg-blue-500/30 text-blue-200 text-xs rounded-full border border-blue-400/40">Fast Process</span>
                      <span class="px-2 py-1 bg-blue-500/30 text-blue-200 text-xs rounded-full border border-blue-400/40">Recommended</span>
                    </div>
                  </div>

                  <!-- Arrow Indicator -->
                  <div class="flex-shrink-0 self-center">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-400/30 to-blue-600/30 rounded-full flex items-center justify-center group-hover/button:bg-gradient-to-br group-hover/button:from-blue-300/40 group-hover/button:to-blue-500/40 transition-all duration-300 border border-blue-300/40">
                      <i class="fas fa-chevron-right text-white group-hover/button:translate-x-0.5 transition-transform duration-300 drop-shadow-sm"></i>
                    </div>
                  </div>
                </div>

                <!-- Button Press Effect -->
                <div class="absolute inset-0 bg-gradient-to-br from-blue-800/50 to-blue-900/50 opacity-0 group-active/button:opacity-100 transition-opacity duration-150 rounded-2xl"></div>
              </button>

              <!-- Multi-Layer Card Shadow Effects -->
              <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-blue-500/20 to-blue-600/20 opacity-0 group-hover/card:opacity-100 transition-opacity duration-300 pointer-events-none blur-sm -z-10"></div>
              <div class="absolute -inset-0.5 rounded-2xl bg-gradient-to-br from-blue-400/30 to-blue-500/30 opacity-0 group-hover/card:opacity-100 transition-opacity duration-300 pointer-events-none blur-md -z-10"></div>
              <div class="absolute -inset-1 rounded-2xl bg-gradient-to-br from-blue-300/20 to-blue-400/20 opacity-0 group-hover/card:opacity-100 transition-opacity duration-300 pointer-events-none blur-lg -z-10"></div>
            </div>

            <!-- Booking Service Card -->
            <div class="relative group/card">
              <button
                @click="selectBookingService"
                :disabled="formLoading"
                class="w-full relative overflow-hidden bg-gradient-to-br from-red-600 via-red-700 to-red-800 text-white p-6 rounded-2xl font-medium transition-all duration-500 transform hover:scale-[1.02] hover:-translate-y-1 disabled:opacity-50 disabled:cursor-not-allowed group/button"
              >
                <!-- Multi-Layer Background Effects -->
                <div class="absolute inset-0 bg-gradient-to-br from-red-500/20 via-red-600/10 to-red-700/20 opacity-0 group-hover/button:opacity-100 transition-opacity duration-500"></div>
                <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-red-400/30 to-transparent rounded-full blur-2xl group-hover/button:scale-150 transition-transform duration-700"></div>
                <div class="absolute bottom-0 left-0 w-24 h-24 bg-gradient-to-tr from-red-300/20 to-transparent rounded-full blur-xl group-hover/button:scale-125 transition-transform duration-600"></div>

                <!-- Animated Shine Effect -->
                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -skew-x-12 -translate-x-full group-hover/button:translate-x-full transition-transform duration-1000"></div>

                <!-- Content Layer -->
                <div class="relative z-10 flex items-start space-x-4">
                  <!-- Enhanced Icon Container -->
                  <div class="relative flex-shrink-0">
                    <div class="w-16 h-16 bg-gradient-to-br from-red-400/40 to-red-600/40 rounded-xl flex items-center justify-center group-hover/button:scale-110 transition-transform duration-300 relative overflow-hidden border border-red-300/30">
                      <!-- Icon Background Glow -->
                      <div class="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent"></div>
                      <!-- Multi-Layer Icon -->
                      <i class="fas fa-calendar-check text-white text-2xl relative z-10 drop-shadow-lg"></i>
                      <!-- Animated Particles -->
                      <div class="absolute top-1 right-1 w-1.5 h-1.5 bg-white/60 rounded-full animate-ping"></div>
                      <div class="absolute bottom-2 left-2 w-1 h-1 bg-red-300/80 rounded-full animate-pulse"></div>
                    </div>
                    <!-- Orbital Ring -->
                    <div class="absolute inset-0 rounded-xl border border-red-400/30 animate-spin-slow opacity-50"></div>
                    <!-- Outer Glow -->
                    <div class="absolute -inset-1 rounded-xl bg-gradient-to-br from-red-400/30 to-red-600/30 blur-md opacity-0 group-hover/button:opacity-100 transition-opacity duration-300"></div>
                  </div>

                  <!-- Text Content -->
                  <div class="flex-1 text-left">
                    <h3 class="text-xl font-bold text-white mb-2 flex items-center">
                      <span class="bg-gradient-to-r from-white to-red-100 bg-clip-text text-transparent drop-shadow-lg">Booking Service</span>
                      <!-- New Badge -->
                      <span class="ml-3 px-2 py-1 bg-red-400/30 text-red-100 text-xs rounded-full border border-red-300/50 animate-pulse">Available</span>
                    </h3>
                    <p class="text-red-100/90 text-sm leading-relaxed mb-3">
                      Book hospital equipment and devices
                    </p>
                    <!-- Feature Tags -->
                    <div class="flex flex-wrap gap-2">
                      <span class="px-2 py-1 bg-red-500/30 text-red-200 text-xs rounded-full border border-red-400/40">Equipment</span>
                      <span class="px-2 py-1 bg-red-500/30 text-red-200 text-xs rounded-full border border-red-400/40">Real-time</span>
                      <span class="px-2 py-1 bg-red-500/30 text-red-200 text-xs rounded-full border border-red-400/40">Instant</span>
                    </div>
                  </div>

                  <!-- Arrow Indicator -->
                  <div class="flex-shrink-0 self-center">
                    <div class="w-10 h-10 bg-gradient-to-br from-red-400/30 to-red-600/30 rounded-full flex items-center justify-center group-hover/button:bg-gradient-to-br group-hover/button:from-red-300/40 group-hover/button:to-red-500/40 transition-all duration-300 border border-red-300/40">
                      <i class="fas fa-chevron-right text-white group-hover/button:translate-x-0.5 transition-transform duration-300 drop-shadow-sm"></i>
                    </div>
                  </div>
                </div>

                <!-- Button Press Effect -->
                <div class="absolute inset-0 bg-gradient-to-br from-red-800/50 to-red-900/50 opacity-0 group-active/button:opacity-100 transition-opacity duration-150 rounded-2xl"></div>
              </button>

              <!-- Multi-Layer Card Shadow Effects -->
              <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-red-500/20 to-red-600/20 opacity-0 group-hover/card:opacity-100 transition-opacity duration-300 pointer-events-none blur-sm -z-10"></div>
              <div class="absolute -inset-0.5 rounded-2xl bg-gradient-to-br from-red-400/30 to-red-500/30 opacity-0 group-hover/card:opacity-100 transition-opacity duration-300 pointer-events-none blur-md -z-10"></div>
              <div class="absolute -inset-1 rounded-2xl bg-gradient-to-br from-red-300/20 to-red-400/20 opacity-0 group-hover/card:opacity-100 transition-opacity duration-300 pointer-events-none blur-lg -z-10"></div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex space-x-3 mt-6">
            <button
              @click="cancelFormSelection"
              class="flex-1 bg-gray-100 text-gray-700 py-3 px-4 rounded-xl font-medium hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2 transition-all duration-200 shadow-md hover:shadow-lg cancel-button-shadow"
            >
              <span class="drop-shadow-sm">Cancel</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, onBeforeUnmount, onUnmounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import DynamicSidebar from './DynamicSidebar.vue'
import AppFooter from './footer.vue'
import AppHeader from './AppHeader.vue'
import { useAuth } from '@/composables/useAuth'

export default {
  name: 'UserDashboard',
  components: {
    DynamicSidebar,
    AppFooter,
    AppHeader
  },
  setup() {
    const router = useRouter()
    const { userName, ROLES, requireRole, isAuthenticated, isLoading } =
      useAuth()

    // Local state
    const sidebarCollapsed = ref(false)
    const showFormSelector = ref(false)
    const formLoading = ref(false)
    const selectedForm = ref(null)
    const loading = ref(false)
    const currentStep = ref(3) // Current step in the approval process

    const steps = [
      {
        id: 1,
        label: 'User Info',
        description: 'Submit your access request'
      },
      {
        id: 2,
        label: 'In your (HOD)',
        description: 'Head of Department review'
      },
      {
        id: 3,
        label: 'In your Divisional Director',
        description: 'Divisional Director approval'
      },
      {
        id: 4,
        label: 'In DICT',
        description: 'DICT verification process'
      },
      {
        id: 5,
        label: 'In HOD (IT)',
        description: 'IT Head assessment'
      },
      {
        id: 6,
        label: 'In ICT Officer',
        description: 'ICT Officer processing'
      },
      {
        id: 7,
        label: 'Access Form Approved',
        description: 'Final approval granted'
      }
    ]

    // Mock data
    const totalRequests = computed(() => 56 + 1234 + 89)

    // Guard this route - only staff can access
    onMounted(() => {
      console.log('UserDashboard mounted - Route:', router.currentRoute.value.path)
      requireRole([ROLES.STAFF])
    })

    // Add debugging for unmount
    onBeforeUnmount(() => {
      console.log('UserDashboard beforeUnmount - Route:', router.currentRoute.value.path)
    })

    onUnmounted(() => {
      console.log('UserDashboard unmounted')
    })
    // Computed properties
    const progressWidth = computed(() => {
      if (currentStep.value === 1) return '0%'
      const progress = ((currentStep.value - 1) / (steps.length - 1)) * 100
      return `${progress}%`
    })

    const progressPercentage = computed(() => {
      return Math.round(((currentStep.value - 1) / (steps.length - 1)) * 100)
    })

    // Methods
    const selectBothServices = async() => {
      formLoading.value = true
      selectedForm.value = 'both'

      // Simulate loading delay
      await new Promise((resolve) => setTimeout(resolve, 1000))

      showFormSelector.value = false
      formLoading.value = false
      selectedForm.value = null

      // Navigate to User Combined Access Form
      router.push('/user-combined-form')
    }

    const selectBookingService = async() => {
      formLoading.value = true
      selectedForm.value = 'booking'

      // Simulate loading delay
      await new Promise((resolve) => setTimeout(resolve, 1000))

      showFormSelector.value = false
      formLoading.value = false
      selectedForm.value = null

      // Navigate to Booking Service
      router.push('/booking-service')
    }

    const cancelFormSelection = () => {
      showFormSelector.value = false
      selectedForm.value = null
    }

    const isCompleted = (stepId) => {
      return stepId < currentStep.value
    }

    const isCurrent = (stepId) => {
      return stepId === currentStep.value
    }

    const isPending = (stepId) => {
      return stepId > currentStep.value
    }

    const getStepCircleClass = (stepId) => {
      if (isCompleted(stepId)) {
        return 'bg-blue-500 shadow-lg'
      } else if (isCurrent(stepId)) {
        return 'bg-blue-500 shadow-lg animate-pulse'
      } else {
        return 'bg-white/30 shadow-md'
      }
    }

    const getStepLabelClass = (stepId) => {
      if (isCompleted(stepId)) {
        return 'text-blue-400 font-semibold'
      } else if (isCurrent(stepId)) {
        return 'text-blue-300 font-semibold'
      } else {
        return 'text-white/60'
      }
    }

    const getMobileStepClass = (stepId) => {
      if (isCompleted(stepId)) {
        return 'bg-blue-500/20 border-l-4 border-blue-400'
      } else if (isCurrent(stepId)) {
        return 'bg-blue-500/20 border-l-4 border-blue-400'
      } else {
        return 'bg-white/10 border-l-4 border-white/30'
      }
    }

    const getCurrentStepLabel = () => {
      const currentStepObj = steps.find(
        (step) => step.id === currentStep.value
      )
      return currentStepObj ? currentStepObj.label : ''
    }

    const getCurrentStepDescription = () => {
      const currentStepObj = steps.find(
        (step) => step.id === currentStep.value
      )
      return currentStepObj ? currentStepObj.description : ''
    }

    const handleStepClick = (stepId) => {
      // Allow clicking on completed steps or current step
      if (stepId <= currentStep.value) {
        currentStep.value = stepId
      }
    }

    const nextStep = () => {
      if (currentStep.value < steps.length) {
        currentStep.value++
      }
    }

    const previousStep = () => {
      if (currentStep.value > 1) {
        currentStep.value--
      }
    }

    const resetProgress = () => {
      currentStep.value = 1
    }

    return {
      userName,
      isAuthenticated,
      isLoading,
      sidebarCollapsed,
      showFormSelector,
      formLoading,
      selectedForm,
      loading,
      currentStep,
      steps,
      totalRequests,
      progressWidth,
      progressPercentage,
      selectBothServices,
      selectBookingService,
      cancelFormSelection,
      isCompleted,
      isCurrent,
      isPending,
      getStepCircleClass,
      getStepLabelClass,
      getMobileStepClass,
      getCurrentStepLabel,
      getCurrentStepDescription,
      handleStepClick,
      nextStep,
      previousStep,
      resetProgress
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

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(20px) scale(0.95);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
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

.animate-slideUp {
  animation: slideUp 0.3s ease-out;
}

/* Enhanced Shadow Effects */
.modal-shadow {
  box-shadow: 0 35px 60px -12px rgba(0, 0, 0, 0.35),
    0 25px 50px -12px rgba(59, 130, 246, 0.25),
    0 15px 30px -8px rgba(59, 130, 246, 0.15),
    0 8px 16px -4px rgba(0, 0, 0, 0.1), 0 0 0 1px rgba(255, 255, 255, 0.1),
    inset 0 2px 4px rgba(255, 255, 255, 0.15),
    inset 0 -1px 2px rgba(0, 0, 0, 0.05);
  transform: translateY(-4px);
}

.header-shadow {
  box-shadow: 0 8px 16px -4px rgba(0, 0, 0, 0.15),
    0 4px 8px -2px rgba(59, 130, 246, 0.2), 0 2px 4px -1px rgba(0, 0, 0, 0.1),
    inset 0 2px 4px rgba(255, 255, 255, 0.15),
    inset 0 -2px 4px rgba(0, 0, 0, 0.1);
  position: relative;
}

.header-shadow::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(
    135deg,
    rgba(255, 255, 255, 0.1) 0%,
    transparent 50%,
    rgba(0, 0, 0, 0.05) 100%
  );
  pointer-events: none;
  border-radius: inherit;
}

.icon-shadow {
  box-shadow: 0 16px 32px rgba(59, 130, 246, 0.4),
    0 12px 24px rgba(59, 130, 246, 0.3), 0 8px 16px rgba(0, 0, 0, 0.15),
    0 4px 8px rgba(0, 0, 0, 0.1), 0 0 0 2px rgba(255, 255, 255, 0.1),
    inset 0 2px 4px rgba(255, 255, 255, 0.25),
    inset 0 -2px 4px rgba(0, 0, 0, 0.1);
  animation: iconPulse 2s ease-in-out infinite;
}

@keyframes iconPulse {
  0%,
  100% {
    box-shadow: 0 16px 32px rgba(59, 130, 246, 0.4),
      0 12px 24px rgba(59, 130, 246, 0.3), 0 8px 16px rgba(0, 0, 0, 0.15),
      0 4px 8px rgba(0, 0, 0, 0.1), 0 0 0 2px rgba(255, 255, 255, 0.1),
      inset 0 2px 4px rgba(255, 255, 255, 0.25),
      inset 0 -2px 4px rgba(0, 0, 0, 0.1);
  }
  50% {
    box-shadow: 0 20px 40px rgba(59, 130, 246, 0.5),
      0 16px 32px rgba(59, 130, 246, 0.4), 0 12px 24px rgba(0, 0, 0, 0.2),
      0 8px 16px rgba(0, 0, 0, 0.15), 0 0 0 3px rgba(255, 255, 255, 0.15),
      inset 0 3px 6px rgba(255, 255, 255, 0.3),
      inset 0 -3px 6px rgba(0, 0, 0, 0.15);
  }
}

.body-shadow {
  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.1);
}

.cancel-button-shadow {
  box-shadow: 0 8px 16px -4px rgba(0, 0, 0, 0.15),
    0 6px 12px -2px rgba(0, 0, 0, 0.1), 0 4px 8px -1px rgba(0, 0, 0, 0.08),
    0 2px 4px -1px rgba(0, 0, 0, 0.06), inset 0 1px 2px rgba(255, 255, 255, 0.1);
  transform: translateY(-1px);
}

.cancel-button-shadow:hover {
  box-shadow: 0 16px 24px -4px rgba(0, 0, 0, 0.2),
    0 12px 20px -2px rgba(0, 0, 0, 0.15), 0 8px 16px -1px rgba(0, 0, 0, 0.1),
    0 4px 8px -1px rgba(0, 0, 0, 0.08),
    inset 0 2px 4px rgba(255, 255, 255, 0.15);
  transform: translateY(-3px);
}

/* Enhanced Multi-Layer Animations */
@keyframes spin-slow {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

.animate-spin-slow {
  animation: spin-slow 8s linear infinite;
}

@keyframes bounce {
  0%,
  100% {
    transform: translateY(0);
    animation-timing-function: cubic-bezier(0.8, 0, 1, 1);
  }
  50% {
    transform: translateY(-25%);
    animation-timing-function: cubic-bezier(0, 0, 0.2, 1);
  }
}

.animate-bounce {
  animation: bounce 1s infinite;
}

/* Multi-Layer Hover Transforms */
.group\/card:hover {
  transform: translateY(-2px) scale(1.02);
}

.group\/button:hover {
  transform: translateY(-1px);
}
</style>
