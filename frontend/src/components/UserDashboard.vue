<template>
  <div class="flex flex-col h-screen">
    <Header />
    <div class="flex flex-1 overflow-hidden">
      <ModernSidebar />
      <main class="flex-1 p-6 overflow-y-auto relative user-dashboard-main">
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
              />
            </div>
          </div>
        </div>

        <div class="max-w-7xl mx-auto relative z-10">
          <h1 class="text-4xl font-bold text-white mb-8 drop-shadow-lg">User Dashboard</h1>

          <!-- Enhanced Multi-Layer Welcome Section -->
          <div
            class="medical-card bg-gradient-to-r from-blue-600/30 to-blue-600/30 border-2 border-blue-400/50 p-8 rounded-3xl backdrop-blur-sm hover:shadow-2xl hover:shadow-blue-500/30 transition-all duration-500 group relative overflow-hidden mb-8"
          >
            <!-- Animated Background Layers -->
            <div
              class="absolute inset-0 bg-gradient-to-r from-blue-500/15 via-blue-500/10 to-blue-500/15 opacity-0 group-hover:opacity-100 transition-opacity duration-700"
            ></div>
            <div
              class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-br from-blue-400/25 to-transparent rounded-full blur-2xl group-hover:scale-150 transition-transform duration-1000"
            ></div>
            <div
              class="absolute bottom-0 left-0 w-32 h-32 bg-gradient-to-tr from-blue-400/20 to-transparent rounded-full blur-xl group-hover:scale-125 transition-transform duration-800"
            ></div>

            <!-- Shine Effect -->
            <div
              class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -skew-x-12 -translate-x-full group-hover:translate-x-full transition-transform duration-1000"
            ></div>

            <div class="relative z-10">
              <!-- Enhanced Welcome Title -->
              <div class="flex items-center mb-6">
                <div
                  class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-blue-300/50 relative overflow-hidden mr-4"
                >
                  <div
                    class="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent"
                  ></div>
                  <i class="fas fa-home text-white text-2xl relative z-10 drop-shadow-lg" />
                  <div
                    class="absolute top-1 right-1 w-2 h-2 bg-white/60 rounded-full animate-ping"
                  ></div>
                </div>
                <div>
                  <h2 class="text-3xl font-bold text-white mb-2 drop-shadow-lg">
                    Welcome to ICT Access Portal
                  </h2>
                  <p class="text-blue-100 text-lg drop-shadow-sm opacity-90">
                    Streamline your system access and equipment requests
                  </p>
                </div>
              </div>

              <!-- Enhanced Quick Actions -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- View My Requests Button - Blue -->
                <button
                  @click="viewMyRequests"
                  class="medical-button bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-8 py-6 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl hover:shadow-blue-500/30 transform hover:scale-105 active:scale-95 relative overflow-hidden group/btn cursor-pointer"
                  style="pointer-events: auto; z-index: 1000"
                >
                  <!-- Multi-layer button effects -->
                  <div
                    class="absolute inset-0 bg-gradient-to-r from-blue-500/20 to-blue-600/20 opacity-0 group-hover/btn:opacity-100 transition-opacity duration-300"
                  ></div>
                  <div
                    class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-blue-300/60 to-transparent group-hover/btn:animate-pulse"
                  ></div>
                  <div
                    class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -skew-x-12 -translate-x-full group-hover/btn:translate-x-full transition-transform duration-700"
                  ></div>

                  <div class="relative z-10 flex items-center justify-center">
                    <div
                      class="w-8 h-8 bg-blue-400/40 rounded-lg flex items-center justify-center mr-3 group-hover/btn:scale-110 transition-transform duration-300"
                    >
                      <i class="fas fa-list-alt text-white text-lg drop-shadow-sm" />
                    </div>
                    <span class="text-lg drop-shadow-sm">Track My Applications</span>
                  </div>
                </button>

                <!-- Submit New Request Button - Red / Orange if any pending -->
                <button
                  @click="showFormSelector = true"
                  :class="[
                    'medical-button px-8 py-6 rounded-xl font-semibold transition-all duration-300 shadow-lg transform relative overflow-hidden group/btn',
                    (hasPendingBookingRequest || hasPendingCombinedRequest) &&
                    !isCheckingPendingRequests
                      ? 'bg-gradient-to-r from-orange-600 to-orange-700 hover:from-orange-700 hover:to-orange-800 text-white hover:shadow-xl hover:shadow-orange-500/30 hover:scale-105 active:scale-95'
                      : 'bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white hover:shadow-xl hover:shadow-red-500/30 hover:scale-105 active:scale-95',
                    isCheckingPendingRequests ? 'opacity-70 cursor-wait' : ''
                  ]"
                  :disabled="isCheckingPendingRequests"
                >
                  <!-- Multi-layer button effects -->
                  <div
                    :class="[
                      'absolute inset-0 opacity-0 group-hover/btn:opacity-100 transition-opacity duration-300',
                      (hasPendingBookingRequest || hasPendingCombinedRequest) &&
                      !isCheckingPendingRequests
                        ? 'bg-gradient-to-r from-orange-500/20 to-orange-600/20'
                        : 'bg-gradient-to-r from-red-500/20 to-red-600/20'
                    ]"
                  ></div>
                  <div
                    :class="[
                      'absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent to-transparent group-hover/btn:animate-pulse',
                      (hasPendingBookingRequest || hasPendingCombinedRequest) &&
                      !isCheckingPendingRequests
                        ? 'via-orange-300/60'
                        : 'via-red-300/60'
                    ]"
                  ></div>
                  <div
                    class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -skew-x-12 -translate-x-full group-hover/btn:translate-x-full transition-transform duration-700"
                  ></div>

                  <div class="relative z-10 flex items-center justify-center">
                    <div
                      :class="[
                        'w-8 h-8 rounded-lg flex items-center justify-center mr-3 group-hover/btn:scale-110 transition-transform duration-300',
                        (hasPendingBookingRequest || hasPendingCombinedRequest) &&
                        !isCheckingPendingRequests
                          ? 'bg-orange-400/40'
                          : 'bg-red-400/40'
                      ]"
                    >
                      <i
                        v-if="isCheckingPendingRequests"
                        class="fas fa-spinner fa-spin text-white text-lg drop-shadow-sm"
                      />
                      <i
                        v-else-if="hasPendingBookingRequest || hasPendingCombinedRequest"
                        class="fas fa-eye text-white text-lg drop-shadow-sm"
                      />
                      <i v-else class="fas fa-plus text-white text-lg drop-shadow-sm" />
                    </div>
                    <span class="text-lg drop-shadow-sm">
                      <template v-if="isCheckingPendingRequests">Checking...</template>
                      <template v-else-if="hasPendingBookingRequest || hasPendingCombinedRequest"
                        >View Pending Request</template
                      >
                      <template v-else>Create New Application</template>
                    </span>
                  </div>
                </button>
              </div>
            </div>
          </div>

          <!-- Enhanced Multi-Layer Statistics -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Pending Card - Enhanced Multi-Layer -->
            <div class="multi-layer-card group relative overflow-hidden">
              <!-- Layer 1: Base Card -->
              <div
                class="absolute inset-0 bg-gradient-to-br from-orange-600/20 to-orange-700/30 rounded-2xl border-2 border-orange-400/40 backdrop-blur-sm"
              ></div>

              <!-- Layer 2: Animated Background Gradients -->
              <div
                class="absolute inset-0 bg-gradient-to-br from-orange-500/10 via-transparent to-orange-600/20 opacity-0 group-hover:opacity-100 transition-opacity duration-500"
              ></div>
              <div
                class="absolute inset-0 bg-gradient-to-tr from-transparent via-orange-400/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-700"
              ></div>

              <!-- Layer 3: Floating Orbs -->
              <div
                class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-orange-400/25 to-transparent rounded-full blur-2xl group-hover:scale-150 transition-transform duration-1000 animate-pulse"
              ></div>
              <div
                class="absolute bottom-0 left-0 w-24 h-24 bg-gradient-to-tr from-orange-400/20 to-transparent rounded-full blur-xl group-hover:scale-125 transition-transform duration-800"
              ></div>
              <div
                class="absolute top-1/2 left-1/2 w-16 h-16 bg-gradient-to-r from-orange-300/15 to-orange-500/15 rounded-full blur-lg transform -translate-x-1/2 -translate-y-1/2 group-hover:scale-200 transition-transform duration-1200 opacity-0 group-hover:opacity-100"
              ></div>

              <!-- Layer 4: Shine Effect -->
              <div
                class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -skew-x-12 -translate-x-full group-hover:translate-x-full transition-transform duration-1000"
              ></div>

              <!-- Layer 5: Border Glow -->
              <div
                class="absolute inset-0 rounded-2xl border border-orange-300/30 group-hover:border-orange-300/60 transition-colors duration-500"
              ></div>
              <div
                class="absolute inset-0 rounded-2xl border-2 border-transparent group-hover:border-orange-400/20 transition-colors duration-700"
              ></div>

              <!-- Layer 6: Content Layer -->
              <div class="relative z-20 p-8 h-full">
                <div class="flex items-center justify-between mb-4">
                  <!-- Enhanced Icon Container -->
                  <div class="relative">
                    <div
                      class="w-14 h-14 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center shadow-xl group-hover:scale-110 transition-transform duration-300 border border-orange-300/50 relative overflow-hidden"
                    >
                      <!-- Icon background layers -->
                      <div
                        class="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent"
                      ></div>
                      <div
                        class="absolute inset-0 bg-gradient-to-tl from-orange-400/30 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                      ></div>
                      <i class="fas fa-clock text-white text-xl relative z-10 drop-shadow-lg" />
                      <!-- Pulsing dot -->
                      <div
                        class="absolute top-1 right-1 w-3 h-3 bg-orange-300 rounded-full animate-ping opacity-75"
                      ></div>
                      <div class="absolute top-1 right-1 w-3 h-3 bg-orange-200 rounded-full"></div>
                    </div>
                    <!-- Icon shadow -->
                    <div
                      class="absolute inset-0 bg-orange-500/20 rounded-xl blur-md group-hover:blur-lg transition-all duration-300 -z-10"
                    ></div>
                  </div>

                  <!-- Enhanced Status Badge -->
                  <div class="relative">
                    <div
                      class="flex items-center space-x-2 bg-orange-500/30 px-4 py-2 rounded-full border border-orange-400/40 backdrop-blur-sm group-hover:bg-orange-500/40 transition-colors duration-300"
                    >
                      <div class="w-2 h-2 bg-orange-400 rounded-full animate-pulse"></div>
                      <span class="text-xs text-orange-200 font-medium tracking-wide"
                        >Processing</span
                      >
                    </div>
                    <!-- Badge glow -->
                    <div
                      class="absolute inset-0 bg-orange-400/20 rounded-full blur-sm opacity-0 group-hover:opacity-100 transition-opacity duration-300 -z-10"
                    ></div>
                  </div>
                </div>

                <!-- Enhanced Title -->
                <h3
                  class="text-2xl font-bold text-white mb-4 drop-shadow-lg group-hover:text-orange-100 transition-colors duration-300"
                >
                  Under Review
                </h3>

                <!-- Enhanced Number Display -->
                <div class="relative mb-6">
                  <div
                    class="text-6xl font-bold text-orange-400 drop-shadow-lg relative group-hover:scale-105 transition-transform duration-300"
                  >
                    <span v-if="isLoadingStats" class="animate-pulse">--</span>
                    <span v-else>{{ dashboardStats.underReview }}</span>
                    <!-- Number glow layers -->
                    <div
                      class="absolute -inset-4 bg-orange-400/15 blur-xl opacity-50 group-hover:opacity-100 transition-opacity duration-300 rounded-full"
                    ></div>
                    <div
                      class="absolute -inset-2 bg-orange-400/20 blur-lg opacity-60 group-hover:opacity-90 transition-opacity duration-500 rounded-full"
                    ></div>
                  </div>
                  <!-- Floating particles -->
                  <div
                    class="absolute top-0 right-0 w-1 h-1 bg-orange-300 rounded-full animate-ping"
                    style="animation-delay: 0.5s"
                  ></div>
                  <div
                    class="absolute bottom-0 left-0 w-1 h-1 bg-orange-300 rounded-full animate-ping"
                    style="animation-delay: 1s"
                  ></div>
                </div>

                <!-- Enhanced Progress Bar -->
                <div class="relative">
                  <div
                    class="w-full bg-orange-900/30 rounded-full h-3 overflow-hidden border border-orange-700/30"
                  >
                    <div
                      class="h-full bg-gradient-to-r from-orange-400 via-orange-500 to-orange-600 rounded-full relative overflow-hidden group-hover:shadow-lg transition-shadow duration-300"
                      :style="`width: ${dashboardStats.processingPercentage}%`"
                    >
                      <!-- Progress shine effect -->
                      <div
                        class="absolute inset-0 bg-gradient-to-r from-transparent via-white/30 to-transparent -skew-x-12 animate-progress"
                      ></div>
                    </div>
                  </div>
                  <!-- Progress glow -->
                  <div
                    class="absolute inset-0 bg-orange-400/10 rounded-full blur-sm opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                  ></div>
                </div>
              </div>

              <!-- Layer 7: Hover Transform -->
              <div
                class="absolute inset-0 transform group-hover:scale-105 transition-transform duration-500 pointer-events-none"
              ></div>
            </div>

            <!-- Approved Card - Enhanced Multi-Layer -->
            <div class="multi-layer-card group relative overflow-hidden">
              <!-- Layer 1: Base Card -->
              <div
                class="absolute inset-0 bg-gradient-to-br from-green-600/20 to-green-700/30 rounded-2xl border-2 border-green-400/40 backdrop-blur-sm"
              ></div>

              <!-- Layer 2: Animated Background Gradients -->
              <div
                class="absolute inset-0 bg-gradient-to-br from-green-500/10 via-transparent to-green-600/20 opacity-0 group-hover:opacity-100 transition-opacity duration-500"
              ></div>
              <div
                class="absolute inset-0 bg-gradient-to-tr from-transparent via-green-400/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-700"
              ></div>

              <!-- Layer 3: Floating Orbs -->
              <div
                class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-green-400/25 to-transparent rounded-full blur-2xl group-hover:scale-150 transition-transform duration-1000 animate-pulse"
              ></div>
              <div
                class="absolute bottom-0 left-0 w-24 h-24 bg-gradient-to-tr from-green-400/20 to-transparent rounded-full blur-xl group-hover:scale-125 transition-transform duration-800"
              ></div>
              <div
                class="absolute top-1/2 left-1/2 w-16 h-16 bg-gradient-to-r from-green-300/15 to-green-500/15 rounded-full blur-lg transform -translate-x-1/2 -translate-y-1/2 group-hover:scale-200 transition-transform duration-1200 opacity-0 group-hover:opacity-100"
              ></div>

              <!-- Layer 4: Shine Effect -->
              <div
                class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -skew-x-12 -translate-x-full group-hover:translate-x-full transition-transform duration-1000"
              ></div>

              <!-- Layer 5: Border Glow -->
              <div
                class="absolute inset-0 rounded-2xl border border-green-300/30 group-hover:border-green-300/60 transition-colors duration-500"
              ></div>
              <div
                class="absolute inset-0 rounded-2xl border-2 border-transparent group-hover:border-green-400/20 transition-colors duration-700"
              ></div>

              <!-- Layer 6: Content Layer -->
              <div class="relative z-20 p-8 h-full">
                <div class="flex items-center justify-between mb-4">
                  <!-- Enhanced Icon Container -->
                  <div class="relative">
                    <div
                      class="w-14 h-14 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-xl group-hover:scale-110 transition-transform duration-300 border border-green-300/50 relative overflow-hidden"
                    >
                      <!-- Icon background layers -->
                      <div
                        class="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent"
                      ></div>
                      <div
                        class="absolute inset-0 bg-gradient-to-tl from-green-400/30 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                      ></div>
                      <i
                        class="fas fa-check-circle text-white text-xl relative z-10 drop-shadow-lg"
                      />
                      <!-- Success indicator -->
                      <div
                        class="absolute top-1 right-1 w-3 h-3 bg-green-300 rounded-full animate-ping opacity-75"
                      ></div>
                      <div class="absolute top-1 right-1 w-3 h-3 bg-green-200 rounded-full"></div>
                    </div>
                    <!-- Icon shadow -->
                    <div
                      class="absolute inset-0 bg-green-500/20 rounded-xl blur-md group-hover:blur-lg transition-all duration-300 -z-10"
                    ></div>
                  </div>

                  <!-- Enhanced Status Badge -->
                  <div class="relative">
                    <div
                      class="flex items-center space-x-2 bg-green-500/30 px-4 py-2 rounded-full border border-green-400/40 backdrop-blur-sm group-hover:bg-green-500/40 transition-colors duration-300"
                    >
                      <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                      <span class="text-xs text-green-200 font-medium tracking-wide"
                        >Completed</span
                      >
                    </div>
                    <!-- Badge glow -->
                    <div
                      class="absolute inset-0 bg-green-400/20 rounded-full blur-sm opacity-0 group-hover:opacity-100 transition-opacity duration-300 -z-10"
                    ></div>
                  </div>
                </div>

                <!-- Enhanced Title -->
                <h3
                  class="text-2xl font-bold text-white mb-4 drop-shadow-lg group-hover:text-green-100 transition-colors duration-300"
                >
                  Granted Access
                </h3>

                <!-- Enhanced Number Display -->
                <div class="relative mb-6">
                  <div
                    class="text-6xl font-bold text-green-400 drop-shadow-lg relative group-hover:scale-105 transition-transform duration-300"
                  >
                    <span v-if="isLoadingStats" class="animate-pulse">--</span>
                    <span v-else>{{ dashboardStats.grantedAccess }}</span>
                    <!-- Number glow layers -->
                    <div
                      class="absolute -inset-4 bg-green-400/15 blur-xl opacity-50 group-hover:opacity-100 transition-opacity duration-300 rounded-full"
                    ></div>
                    <div
                      class="absolute -inset-2 bg-green-400/20 blur-lg opacity-60 group-hover:opacity-90 transition-opacity duration-500 rounded-full"
                    ></div>
                  </div>
                  <!-- Floating particles -->
                  <div
                    class="absolute top-0 right-0 w-1 h-1 bg-green-300 rounded-full animate-ping"
                    style="animation-delay: 0.5s"
                  ></div>
                  <div
                    class="absolute bottom-0 left-0 w-1 h-1 bg-green-300 rounded-full animate-ping"
                    style="animation-delay: 1s"
                  ></div>
                </div>

                <!-- Enhanced Progress Bar -->
                <div class="relative">
                  <div
                    class="w-full bg-green-900/30 rounded-full h-3 overflow-hidden border border-green-700/30"
                  >
                    <div
                      class="h-full bg-gradient-to-r from-green-400 via-green-500 to-green-600 rounded-full relative overflow-hidden group-hover:shadow-lg transition-shadow duration-300"
                      :style="`width: ${dashboardStats.completedPercentage}%`"
                    >
                      <!-- Progress shine effect -->
                      <div
                        class="absolute inset-0 bg-gradient-to-r from-transparent via-white/30 to-transparent -skew-x-12 animate-progress"
                      ></div>
                    </div>
                  </div>
                  <!-- Progress glow -->
                  <div
                    class="absolute inset-0 bg-green-400/10 rounded-full blur-sm opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                  ></div>
                </div>
              </div>

              <!-- Layer 7: Hover Transform -->
              <div
                class="absolute inset-0 transform group-hover:scale-105 transition-transform duration-500 pointer-events-none"
              ></div>
            </div>

            <!-- Revision Card - Enhanced Multi-Layer -->
            <div class="multi-layer-card group relative overflow-hidden">
              <!-- Layer 1: Base Card -->
              <div
                class="absolute inset-0 bg-gradient-to-br from-purple-600/20 to-purple-700/30 rounded-2xl border-2 border-purple-400/40 backdrop-blur-sm"
              ></div>

              <!-- Layer 2: Animated Background Gradients -->
              <div
                class="absolute inset-0 bg-gradient-to-br from-purple-500/10 via-transparent to-purple-600/20 opacity-0 group-hover:opacity-100 transition-opacity duration-500"
              ></div>
              <div
                class="absolute inset-0 bg-gradient-to-tr from-transparent via-purple-400/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-700"
              ></div>

              <!-- Layer 3: Floating Orbs -->
              <div
                class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-purple-400/25 to-transparent rounded-full blur-2xl group-hover:scale-150 transition-transform duration-1000 animate-pulse"
              ></div>
              <div
                class="absolute bottom-0 left-0 w-24 h-24 bg-gradient-to-tr from-purple-400/20 to-transparent rounded-full blur-xl group-hover:scale-125 transition-transform duration-800"
              ></div>
              <div
                class="absolute top-1/2 left-1/2 w-16 h-16 bg-gradient-to-r from-purple-300/15 to-purple-500/15 rounded-full blur-lg transform -translate-x-1/2 -translate-y-1/2 group-hover:scale-200 transition-transform duration-1200 opacity-0 group-hover:opacity-100"
              ></div>

              <!-- Layer 4: Shine Effect -->
              <div
                class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -skew-x-12 -translate-x-full group-hover:translate-x-full transition-transform duration-1000"
              ></div>

              <!-- Layer 5: Border Glow -->
              <div
                class="absolute inset-0 rounded-2xl border border-purple-300/30 group-hover:border-purple-300/60 transition-colors duration-500"
              ></div>
              <div
                class="absolute inset-0 rounded-2xl border-2 border-transparent group-hover:border-purple-400/20 transition-colors duration-700"
              ></div>

              <!-- Layer 6: Content Layer -->
              <div class="relative z-20 p-8 h-full">
                <div class="flex items-center justify-between mb-4">
                  <!-- Enhanced Icon Container -->
                  <div class="relative">
                    <div
                      class="w-14 h-14 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center shadow-xl group-hover:scale-110 transition-transform duration-300 border border-purple-300/50 relative overflow-hidden"
                    >
                      <!-- Icon background layers -->
                      <div
                        class="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent"
                      ></div>
                      <div
                        class="absolute inset-0 bg-gradient-to-tl from-purple-400/30 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                      ></div>
                      <i class="fas fa-edit text-white text-xl relative z-10 drop-shadow-lg" />
                      <!-- Revision indicator -->
                      <div
                        class="absolute top-1 right-1 w-3 h-3 bg-purple-300 rounded-full animate-ping opacity-75"
                      ></div>
                      <div class="absolute top-1 right-1 w-3 h-3 bg-purple-200 rounded-full"></div>
                    </div>
                    <!-- Icon shadow -->
                    <div
                      class="absolute inset-0 bg-purple-500/20 rounded-xl blur-md group-hover:blur-lg transition-all duration-300 -z-10"
                    ></div>
                  </div>

                  <!-- Enhanced Status Badge -->
                  <div class="relative">
                    <div
                      class="flex items-center space-x-2 bg-purple-500/30 px-4 py-2 rounded-full border border-purple-400/40 backdrop-blur-sm group-hover:bg-purple-500/40 transition-colors duration-300"
                    >
                      <div class="w-2 h-2 bg-purple-400 rounded-full animate-pulse"></div>
                      <span class="text-xs text-purple-200 font-medium tracking-wide"
                        >Reviewed</span
                      >
                    </div>
                    <!-- Badge glow -->
                    <div
                      class="absolute inset-0 bg-purple-400/20 rounded-full blur-sm opacity-0 group-hover:opacity-100 transition-opacity duration-300 -z-10"
                    ></div>
                  </div>
                </div>

                <!-- Enhanced Title -->
                <h3
                  class="text-2xl font-bold text-white mb-4 drop-shadow-lg group-hover:text-purple-100 transition-colors duration-300"
                >
                  Needs Revision
                </h3>

                <!-- Enhanced Number Display -->
                <div class="relative mb-6">
                  <div
                    class="text-6xl font-bold text-purple-400 drop-shadow-lg relative group-hover:scale-105 transition-transform duration-300"
                  >
                    <span v-if="isLoadingStats" class="animate-pulse">--</span>
                    <span v-else>{{ dashboardStats.needsRevision }}</span>
                    <!-- Number glow layers -->
                    <div
                      class="absolute -inset-4 bg-purple-400/15 blur-xl opacity-50 group-hover:opacity-100 transition-opacity duration-300 rounded-full"
                    ></div>
                    <div
                      class="absolute -inset-2 bg-purple-400/20 blur-lg opacity-60 group-hover:opacity-90 transition-opacity duration-500 rounded-full"
                    ></div>
                  </div>
                  <!-- Floating particles -->
                  <div
                    class="absolute top-0 right-0 w-1 h-1 bg-purple-300 rounded-full animate-ping"
                    style="animation-delay: 0.5s"
                  ></div>
                  <div
                    class="absolute bottom-0 left-0 w-1 h-1 bg-purple-300 rounded-full animate-ping"
                    style="animation-delay: 1s"
                  ></div>
                </div>

                <!-- Enhanced Progress Bar -->
                <div class="relative">
                  <div
                    class="w-full bg-purple-900/30 rounded-full h-3 overflow-hidden border border-purple-700/30"
                  >
                    <div
                      class="h-full bg-gradient-to-r from-purple-400 via-purple-500 to-purple-600 rounded-full relative overflow-hidden group-hover:shadow-lg transition-shadow duration-300"
                      :style="`width: ${dashboardStats.revisionPercentage}%`"
                    >
                      <!-- Progress shine effect -->
                      <div
                        class="absolute inset-0 bg-gradient-to-r from-transparent via-white/30 to-transparent -skew-x-12 animate-progress"
                      ></div>
                    </div>
                  </div>
                  <!-- Progress glow -->
                  <div
                    class="absolute inset-0 bg-purple-400/10 rounded-full blur-sm opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                  ></div>
                </div>
              </div>

              <!-- Layer 7: Hover Transform -->
              <div
                class="absolute inset-0 transform group-hover:scale-105 transition-transform duration-500 pointer-events-none"
              ></div>
            </div>
          </div>
        </div>
      </main>
    </div>

    <!-- Form Selector Modal -->
    <div
      v-if="showFormSelector"
      class="fixed inset-0 bg-black/60 flex items-center justify-center z-50"
    >
      <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4">
        <div class="bg-blue-600 p-6 text-center rounded-t-xl">
          <h3 class="text-xl font-bold text-white">Select Access Form</h3>
        </div>
        <div class="p-6 space-y-4">
          <button
            @click="selectCombinedForm"
            class="w-full bg-blue-600 hover:bg-blue-700 text-white p-4 rounded-lg font-medium transition-colors"
          >
            <i class="fas fa-layer-group mr-2" />
            Combined Access Form
          </button>
          <button
            @click="selectBookingService"
            class="w-full bg-red-600 hover:bg-red-700 text-white p-4 rounded-lg font-medium transition-colors"
          >
            <i class="fas fa-calendar-check mr-2" />
            Booking Service
          </button>
          <button
            @click="showFormSelector = false"
            class="w-full bg-gray-300 hover:bg-gray-400 text-gray-800 p-3 rounded-lg font-medium transition-colors"
          >
            Cancel
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import { ref, onMounted } from 'vue'
  import { useRouter } from 'vue-router'
  import ModernSidebar from './ModernSidebar.vue'
  import Header from './header.vue'
  import { useAuth } from '@/composables/useAuth'
  import { useNotificationStore } from '@/stores/notification'
  import bookingService from '@/services/bookingService'
  import dashboardService from '@/services/dashboardService'
  import userCombinedAccessService from '@/services/userCombinedAccessService'

  export default {
    name: 'UserDashboard',
    components: {
      ModernSidebar,
      Header
    },
    setup() {
      const router = useRouter()
      const { requireRole, ROLES } = useAuth()
      const notificationStore = useNotificationStore()

      // Local state
      const showFormSelector = ref(false)
      const hasPendingBookingRequest = ref(false)
      const pendingBookingInfo = ref(null)
      const hasPendingCombinedRequest = ref(false)
      const pendingCombinedInfo = ref(null)
      const isCheckingPendingRequests = ref(true)

      // Dashboard statistics state
      const dashboardStats = ref({
        processing: 0,
        underReview: 0,
        completed: 0,
        grantedAccess: 0,
        revision: 0,
        needsRevision: 0,
        total: 0,
        processingPercentage: 0,
        completedPercentage: 0,
        revisionPercentage: 0
      })
      const isLoadingStats = ref(true)
      const statsError = ref(null)

      // Note: Sidebar state is now managed by Vuex, no local state needed

      // Fetch dashboard statistics from backend
      const fetchDashboardStats = async () => {
        try {
          isLoadingStats.value = true
          statsError.value = null

          console.log('📊 UserDashboard: Fetching dashboard statistics...')
          const result = await dashboardService.getUserDashboardStats()

          if (result.success && result.data) {
            // Update dashboard stats with backend data
            dashboardStats.value = {
              processing: result.data.processing || 0,
              underReview: result.data.underReview || result.data.processing || 0,
              completed: result.data.completed || 0,
              grantedAccess: result.data.grantedAccess || result.data.completed || 0,
              revision: result.data.revision || result.data.needsRevision || 0,
              needsRevision: result.data.needsRevision || result.data.revision || 0,
              total: result.data.total || 0,
              processingPercentage: result.data.processingPercentage || 0,
              completedPercentage: result.data.completedPercentage || 0,
              revisionPercentage: result.data.revisionPercentage || 0
            }

            console.log('✅ UserDashboard: Dashboard stats updated:', dashboardStats.value)
          } else {
            // Use fallback data if backend fails
            console.warn('⚠️ Using fallback dashboard statistics')
            if (result.data) {
              dashboardStats.value = result.data
            }
            if (result.error) {
              statsError.value = result.error
            }
          }
        } catch (error) {
          console.error('❌ UserDashboard: Error fetching dashboard stats:', error)
          statsError.value = error.message || 'Failed to load dashboard statistics'

          // Use mock data as fallback
          dashboardStats.value = dashboardService.getMockStats()
        } finally {
          isLoadingStats.value = false
        }
      }

      // Check for pending booking requests on mount
      const checkPendingBookingRequest = async () => {
        try {
          isCheckingPendingRequests.value = true
          const result = await bookingService.checkPendingRequests()

          if (result.success && result.data.has_pending_request) {
            hasPendingBookingRequest.value = true
            pendingBookingInfo.value = result.data.pending_request
          } else {
            hasPendingBookingRequest.value = false
            pendingBookingInfo.value = null
          }
        } catch (error) {
          console.error('Error checking pending requests:', error)
          // On error, assume no pending request to allow normal functionality
          hasPendingBookingRequest.value = false
          pendingBookingInfo.value = null
        } finally {
          isCheckingPendingRequests.value = false
        }
      }

      // Check for pending combined access requests on mount
      const checkPendingCombinedRequest = async () => {
        try {
          const result = await userCombinedAccessService.checkPendingRequests()

          if (result.success && result.data) {
            if (result.data.has_pending_request) {
              hasPendingCombinedRequest.value = true
              pendingCombinedInfo.value = result.data.pending_request
            } else {
              hasPendingCombinedRequest.value = false
              pendingCombinedInfo.value = null
            }
          } else {
            hasPendingCombinedRequest.value = false
            pendingCombinedInfo.value = null
          }
        } catch (error) {
          console.error('UserDashboard: Error checking pending combined requests:', error)
          // On error, assume no pending request to allow normal functionality
          hasPendingCombinedRequest.value = false
          pendingCombinedInfo.value = null
        }
      }

      // Guard this route - only staff can access
      onMounted(async () => {
        console.log('UserDashboard mounted successfully')
        requireRole([ROLES.STAFF])

        // Fetch dashboard statistics and check for pending requests in parallel
        await Promise.allSettled([
          fetchDashboardStats(),
          checkPendingBookingRequest(),
          checkPendingCombinedRequest()
        ])
      })

      // Methods
      const viewMyRequests = () => {
        console.log('Track My Applications button clicked!')
        try {
          router.push('/request-status')
          console.log('Successfully navigating to /request-status')
        } catch (error) {
          console.error('Error navigating to request status:', error)
        }
      }

      const selectCombinedForm = async () => {
        showFormSelector.value = false

        // Check if we have pending combined requests from state
        if (hasPendingCombinedRequest.value && pendingCombinedInfo.value) {
          // User has pending combined request, show notification and redirect to request status
          notificationStore.show({
            type: 'warning',
            title: 'Combined Access Request Restriction',
            message: `You cannot submit a new combined access request while you have a pending request (${pendingCombinedInfo.value.request_id || 'N/A'}). Please wait for your current request to be processed.`,
            duration: 8000,
            persistent: true,
            actions: [
              {
                text: 'View Request Status',
                action: () => {
                  router.push('/request-status')
                }
              }
            ]
          })

          // Redirect to request status with message
          router.push({
            path: '/request-status',
            query: {
              blocked: 'true',
              message:
                'You cannot submit a new combined access request while you have a pending request.',
              pending_request_id: pendingCombinedInfo.value.request_id || 'N/A'
            }
          })
        } else {
          // No pending combined requests, proceed to combined form
          router.push('/user-combined-form')
        }
      }

      const selectBookingService = () => {
        showFormSelector.value = false

        console.log('selectBookingService called', {
          hasPendingBookingRequest: hasPendingBookingRequest.value,
          pendingBookingInfo: pendingBookingInfo.value,
          isCheckingPendingRequests: isCheckingPendingRequests.value
        })

        if (hasPendingBookingRequest.value && pendingBookingInfo.value) {
          // Show notification about existing pending request restriction
          notificationStore.show({
            type: 'warning',
            title: 'Booking Request Restriction',
            message: `You cannot submit a new booking request while you have a pending request (ID: ${pendingBookingInfo.value.id}). Please check your request status.`,
            duration: 8000,
            persistent: true,
            actions: [
              {
                text: 'View Request Details',
                action: () => {
                  router.push(
                    `/request-details?id=${pendingBookingInfo.value.id}&type=booking_service`
                  )
                }
              }
            ]
          })

          // Redirect to request status page with pending booking notification
          router.push({
            path: '/request-status',
            query: {
              pendingBooking: 'true',
              pendingId: pendingBookingInfo.value.id
            }
          })
        } else {
          // No pending request, proceed to booking service
          router.push('/booking-service')
        }
      }

      return {
        // Form selector
        showFormSelector,

        // Booking requests
        hasPendingBookingRequest,
        pendingBookingInfo,

        // Combined access requests
        hasPendingCombinedRequest,
        pendingCombinedInfo,

        isCheckingPendingRequests,

        // Dashboard statistics
        dashboardStats,
        isLoadingStats,
        statsError,

        // Methods
        viewMyRequests,
        selectCombinedForm,
        selectBookingService,
        checkPendingBookingRequest,
        checkPendingCombinedRequest,
        fetchDashboardStats
      }
    }
  }
</script>

<style scoped>
  /* User Dashboard Main Background */
  .user-dashboard-main {
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 50%, #1e40af 100%) !important;
    min-height: 100vh;
  }

  /* Enhanced Multi-Layer Card Effects */
  .multi-layer-card {
    position: relative;
    height: 280px;
    cursor: pointer;
    transform-style: preserve-3d;
    perspective: 1000px;
  }

  .multi-layer-card:hover {
    transform: translateY(-8px) rotateX(2deg) rotateY(2deg);
    box-shadow:
      0 25px 50px -12px rgba(0, 0, 0, 0.25),
      0 0 0 1px rgba(255, 255, 255, 0.1),
      0 0 50px rgba(59, 130, 246, 0.15);
  }

  /* Medical Glass morphism effects */
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

  .medical-button {
    position: relative;
    z-index: 1;
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

  .animate-float {
    animation: float 6s ease-in-out infinite;
  }

  /* Enhanced transitions */
  .transition-colors {
    transition: all 0.3s ease;
  }

  /* Multi-layer hover transforms */
  .group:hover {
    transform: translateY(-2px) scale(1.02);
  }

  .group\/btn:hover {
    transform: translateY(-1px) scale(1.05);
  }

  .group\/btn:active {
    transform: translateY(0px) scale(0.95);
  }

  /* Enhanced 3D transforms for cards */
  .multi-layer-card {
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  }

  .multi-layer-card:hover {
    transform: translateY(-8px) rotateX(5deg) rotateY(5deg) scale(1.02);
  }

  /* Depth layering */
  .multi-layer-card > div:nth-child(1) {
    z-index: 1;
  }
  .multi-layer-card > div:nth-child(2) {
    z-index: 2;
  }
  .multi-layer-card > div:nth-child(3) {
    z-index: 3;
  }
  .multi-layer-card > div:nth-child(4) {
    z-index: 4;
  }
  .multi-layer-card > div:nth-child(5) {
    z-index: 5;
  }
  .multi-layer-card > div:nth-child(6) {
    z-index: 6;
  }
  .multi-layer-card > div:nth-child(7) {
    z-index: 20;
  }
  .multi-layer-card > div:nth-child(8) {
    z-index: 30;
  }

  /* Glow effects */
  .drop-shadow-lg {
    filter: drop-shadow(0 10px 8px rgba(0, 0, 0, 0.04)) drop-shadow(0 4px 3px rgba(0, 0, 0, 0.1));
  }

  /* Enhanced Progress bar animations */
  @keyframes progress-shine {
    0% {
      transform: translateX(-100%);
    }
    100% {
      transform: translateX(100%);
    }
  }

  .animate-progress {
    animation: progress-shine 2s ease-in-out infinite;
  }

  /* Floating particles animation */
  @keyframes float-particle {
    0%,
    100% {
      transform: translateY(0px) rotate(0deg);
      opacity: 0.7;
    }
    25% {
      transform: translateY(-10px) rotate(90deg);
      opacity: 1;
    }
    50% {
      transform: translateY(-5px) rotate(180deg);
      opacity: 0.8;
    }
    75% {
      transform: translateY(-15px) rotate(270deg);
      opacity: 0.9;
    }
  }

  /* Glow pulse animation */
  @keyframes glow-pulse {
    0%,
    100% {
      opacity: 0.5;
      transform: scale(1);
    }
    50% {
      opacity: 1;
      transform: scale(1.1);
    }
  }

  /* Orb floating animation */
  @keyframes orb-float {
    0%,
    100% {
      transform: translate(0, 0) scale(1);
    }
    25% {
      transform: translate(5px, -10px) scale(1.05);
    }
    50% {
      transform: translate(-5px, -5px) scale(0.95);
    }
    75% {
      transform: translate(10px, -15px) scale(1.1);
    }
  }

  /* Enhanced card layer effects */
  .multi-layer-card .absolute:nth-child(3) {
    animation: orb-float 8s ease-in-out infinite;
  }

  .multi-layer-card .absolute:nth-child(4) {
    animation: orb-float 6s ease-in-out infinite reverse;
  }

  .multi-layer-card .absolute:nth-child(5) {
    animation: orb-float 10s ease-in-out infinite;
    animation-delay: 2s;
  }

  /* Icon container enhancements */
  .multi-layer-card .relative .w-14 {
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  }

  .multi-layer-card:hover .relative .w-14 {
    transform: scale(1.15) rotate(5deg);
    box-shadow:
      0 20px 25px -5px rgba(0, 0, 0, 0.1),
      0 10px 10px -5px rgba(0, 0, 0, 0.04);
  }

  /* Number display enhancements */
  .multi-layer-card .text-6xl {
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    text-shadow: 0 0 20px currentColor;
  }

  .multi-layer-card:hover .text-6xl {
    transform: scale(1.1) translateY(-2px);
    text-shadow:
      0 0 30px currentColor,
      0 0 40px currentColor;
  }

  /* Status badge enhancements */
  .multi-layer-card .bg-orange-500\/30,
  .multi-layer-card .bg-green-500\/30,
  .multi-layer-card .bg-purple-500\/30 {
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
  }

  /* Progress bar container enhancements */
  .multi-layer-card .rounded-full.h-3 {
    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
  }

  .multi-layer-card:hover .rounded-full.h-3 {
    box-shadow:
      inset 0 2px 4px rgba(0, 0, 0, 0.2),
      0 0 10px currentColor;
  }

  /* Responsive enhancements */
  @media (max-width: 768px) {
    .multi-layer-card {
      height: 240px;
    }

    .multi-layer-card:hover {
      transform: translateY(-4px);
    }

    .multi-layer-card .text-6xl {
      font-size: 3rem;
    }
  }
</style>
