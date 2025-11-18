<template>
  <aside
    v-if="isAuthenticated && !isLoading && userRole"
    class="h-screen bg-gradient-to-b from-slate-900 via-blue-900 to-teal-900 text-white flex flex-col transition-all duration-300 ease-in-out overflow-y-auto border-r border-slate-700/30 backdrop-blur-sm shadow-2xl"
    :class="containerWidthClass"
    aria-label="Sidebar navigation"
    style="
      background: linear-gradient(
        180deg,
        rgba(15, 23, 42, 0.98) 0%,
        rgba(30, 58, 138, 0.95) 50%,
        rgba(13, 148, 136, 0.92) 100%
      );
      backdrop-filter: blur(25px);
    "
  >
    <!-- Header / Brand + Collapse Toggle -->
    <div class="relative p-4 border-b border-slate-600/20">
      <!-- Background Pattern -->
      <div
        class="absolute inset-0 bg-gradient-to-r from-blue-600/10 via-teal-600/10 to-cyan-600/10 backdrop-blur-sm"
      ></div>

      <div class="relative flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="relative">
            <div
              class="w-10 h-10 bg-gradient-to-br from-blue-400 via-teal-400 to-cyan-400 rounded-xl flex items-center justify-center shadow-xl border border-white/20 backdrop-blur-sm"
            >
              <i class="fas fa-hospital text-white text-lg drop-shadow-lg"></i>
            </div>
            <div
              class="absolute -top-1 -right-1 w-3 h-3 bg-gradient-to-br from-green-400 to-emerald-500 rounded-full border border-white animate-pulse"
            ></div>
          </div>
          <div v-show="!isCollapsed" class="flex flex-col">
            <span
              class="text-base font-bold tracking-wide text-white drop-shadow-lg bg-gradient-to-r from-white to-blue-100 bg-clip-text text-transparent"
            >
              Muhimbili
            </span>
            <span class="text-xs text-slate-300 font-semibold tracking-wider">
              National Hospital
            </span>
            <div class="flex items-center gap-1 mt-0.5">
              <div class="w-1.5 h-1.5 bg-green-400 rounded-full animate-pulse"></div>
              <span class="text-xs text-emerald-300 font-medium">Online</span>
            </div>
          </div>
        </div>
        <button
          class="p-2 rounded-lg hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-teal-400/50 transition-all duration-300 backdrop-blur-sm group"
          @click="toggleCollapse"
          :aria-label="isCollapsed ? 'Expand sidebar' : 'Collapse sidebar'"
        >
          <i
            :class="[
              isCollapsed ? 'fas fa-chevron-right' : 'fas fa-chevron-left',
              'text-slate-300 text-sm group-hover:text-white transition-colors'
            ]"
          ></i>
        </button>
      </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 py-3">
      <ul class="space-y-1">
        <!-- Dashboard Section -->
        <template v-if="dashboardItems.length > 0">
          <li class="tree-node">
            <!-- Dashboard Root Node -->
            <div
              class="relative flex items-center px-4 py-2 text-sm transition-all duration-300 cursor-pointer backdrop-blur-sm group"
              :class="[
                isCollapsed ? 'justify-center' : '',
                'text-slate-200 hover:bg-gradient-to-r hover:from-blue-600/20 hover:via-teal-600/20 hover:to-cyan-600/20 hover:text-white'
              ]"
              @click="!isCollapsed && (showDashboard = !showDashboard)"
            >
              <!-- Tree Lines -->
              <div
                v-show="!isCollapsed"
                class="absolute left-2 top-0 bottom-0 w-px bg-slate-600/30"
              ></div>

              <!-- Expand/Collapse Icon -->
              <div
                v-show="!isCollapsed"
                class="relative flex items-center justify-center w-4 h-4 mr-2"
              >
                <div class="w-2 h-2 bg-slate-600/50 rounded-full"></div>
                <i
                  :class="[
                    'fas text-xs text-slate-400 absolute transition-transform duration-200',
                    showDashboard ? 'fa-minus' : 'fa-plus'
                  ]"
                ></i>
              </div>

              <!-- Node Icon -->
              <div
                class="relative flex items-center justify-center w-6 h-6 rounded-lg bg-gradient-to-br from-blue-500/20 via-teal-500/20 to-cyan-500/20 group-hover:from-blue-500/40 group-hover:via-teal-500/40 group-hover:to-cyan-500/40 transition-all duration-300 shadow-lg border border-white/10 mr-3"
              >
                <i class="fas fa-tachometer-alt text-xs drop-shadow-lg"></i>
              </div>

              <span v-show="!isCollapsed" class="font-semibold tracking-wide drop-shadow-lg"
                >Dashboard</span
              >
            </div>

            <!-- Dashboard Children -->
            <transition
              enter-active-class="transition ease-out duration-300"
              enter-from-class="opacity-0 -translate-y-2 scale-95"
              enter-to-class="opacity-100 translate-y-0 scale-100"
              leave-active-class="transition ease-in duration-200"
              leave-from-class="opacity-100 translate-y-0 scale-100"
              leave-to-class="opacity-0 -translate-y-2 scale-95"
            >
              <ul
                v-if="showDashboard && !isCollapsed"
                class="tree-children ml-6 border-l border-slate-600/30 pl-4 space-y-1"
              >
                <li v-for="item in dashboardItems" :key="item.path" class="tree-leaf">
                  <router-link
                    :to="item.path"
                    class="relative flex items-center rounded-lg px-3 py-2 text-sm transition-all duration-300 backdrop-blur-sm group overflow-hidden"
                    :class="[
                      $route.path === item.path
                        ? 'bg-gradient-to-r from-blue-500/40 via-teal-500/40 to-cyan-500/40 text-white shadow-xl border border-blue-400/50'
                        : 'text-slate-300 hover:bg-gradient-to-r hover:from-blue-600/30 hover:via-teal-600/30 hover:to-cyan-600/30 hover:text-white hover:shadow-lg'
                    ]"
                  >
                    <!-- Tree Connector -->
                    <div class="absolute -left-4 top-1/2 w-3 h-px bg-slate-600/30"></div>
                    <div
                      class="absolute -left-4 top-1/2 w-1 h-1 bg-slate-600/50 rounded-full transform -translate-y-0.5"
                    ></div>

                    <!-- Background Glow -->
                    <div
                      class="absolute inset-0 bg-gradient-to-r from-blue-500/10 via-teal-500/10 to-cyan-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                    ></div>

                    <div
                      class="relative flex items-center justify-center w-5 h-5 rounded bg-gradient-to-br from-blue-500/30 via-teal-500/30 to-cyan-500/30 group-hover:from-blue-500/50 group-hover:via-teal-500/50 group-hover:to-cyan-500/50 transition-all duration-300 shadow border border-white/10 mr-3"
                    >
                      <i :class="[item.icon, 'text-xs drop-shadow-lg']"></i>
                    </div>
                    <span class="relative font-medium tracking-wide drop-shadow-lg">{{
                      item.displayName
                    }}</span>

                    <!-- Active Indicator -->
                    <div
                      v-if="$route.path === item.path"
                      class="absolute right-2 w-1.5 h-1.5 bg-cyan-400 rounded-full animate-pulse shadow-lg"
                    ></div>
                  </router-link>
                </li>
              </ul>
            </transition>
          </li>
        </template>

        <!-- User Management Section (Admin only) -->
        <li v-if="userManagementItems.length > 0" class="mt-6">
          <div v-show="!isCollapsed" class="px-3 py-2">
            <h3 class="text-sm font-semibold text-blue-200 uppercase tracking-wider drop-shadow-sm">
              User Management
            </h3>
          </div>

          <!-- Collapsible User Management -->
          <div
            class="flex items-center"
            :class="!isCollapsed ? 'justify-between' : 'justify-center'"
          >
            <div class="flex items-center flex-1">
              <div
                class="flex items-center rounded-md mx-2 px-3 py-2 text-sm transition-colors cursor-pointer flex-1 backdrop-blur-sm"
                :class="[
                  isCollapsed ? 'justify-center' : '',
                  'text-blue-100 hover:bg-blue-700/30 hover:text-white'
                ]"
                @click="!isCollapsed && (showUserMgmt = !showUserMgmt)"
              >
                <i :class="['fas fa-users', isCollapsed ? 'text-lg' : 'text-base mr-3']"></i>
                <span v-show="!isCollapsed">User Management</span>
              </div>
            </div>

            <button
              v-show="!isCollapsed"
              @click="showUserMgmt = !showUserMgmt"
              class="mr-2 p-2 rounded-md text-blue-200 hover:bg-blue-700/30 transition-colors backdrop-blur-sm"
              :aria-expanded="showUserMgmt.toString()"
            >
              <i :class="['fas text-xs', showUserMgmt ? 'fa-chevron-up' : 'fa-chevron-down']"></i>
            </button>
          </div>

          <!-- User Management Dropdown -->
          <transition
            enter-active-class="transition ease-out duration-300"
            enter-from-class="opacity-0 -translate-y-4 scale-95"
            enter-to-class="opacity-100 translate-y-0 scale-100"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="opacity-100 translate-y-0 scale-100"
            leave-to-class="opacity-0 -translate-y-4 scale-95"
          >
            <div v-if="showUserMgmt && !isCollapsed" class="ml-4 mr-2 mb-2">
              <div
                class="bg-gradient-to-br from-blue-50 via-blue-50 to-cyan-50 rounded-xl border-2 border-blue-200/50 overflow-hidden shadow-xl"
                style="
                  backdrop-filter: blur(10px);
                  box-shadow:
                    0 10px 25px -5px rgba(59, 130, 246, 0.3),
                    0 0 0 1px rgba(59, 130, 246, 0.1),
                    inset 0 1px 0 rgba(255, 255, 255, 0.2);
                "
              >
                <div class="py-2">
                  <router-link
                    v-for="item in userManagementItems"
                    :key="item.path"
                    :to="item.path"
                    @click="showUserMgmt = false"
                    class="flex items-center px-4 py-3 text-xs transition-all duration-300 group relative overflow-hidden"
                    :class="[
                      $route.path === item.path
                        ? 'bg-blue-100 text-blue-700 shadow-md'
                        : 'text-gray-700 hover:bg-blue-100 hover:text-blue-700 hover:shadow-md hover:transform hover:scale-105'
                    ]"
                  >
                    <!-- Hover gradient overlay -->
                    <div
                      class="absolute inset-0 bg-gradient-to-r from-blue-500/10 to-cyan-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                    ></div>

                    <div
                      class="w-8 h-8 bg-gradient-to-br from-blue-100 to-blue-200 rounded-xl flex items-center justify-center mr-3 group-hover:from-blue-200 group-hover:to-blue-300 transition-all duration-300 shadow-lg border border-blue-300/50"
                    >
                      <i :class="[item.icon, 'text-blue-600 text-sm drop-shadow-sm']"></i>
                    </div>
                    <div class="flex-1 relative z-10">
                      <p class="font-semibold drop-shadow-sm">
                        {{ item.displayName }}
                      </p>
                      <p
                        class="text-xs opacity-80 group-hover:opacity-100 transition-opacity duration-300"
                      >
                        {{ item.description }}
                      </p>
                    </div>

                    <!-- Chevron indicator -->
                    <div
                      class="opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-x-2 group-hover:translate-x-0"
                    >
                      <i class="fas fa-chevron-right text-xs text-blue-500"></i>
                    </div>
                  </router-link>
                </div>
              </div>
            </div>
          </transition>
        </li>

        <!-- Requests Management Section (for approvers) -->
        <template v-if="requestsManagementItems.length > 0">
          <li class="tree-node mt-4">
            <!-- Requests Management Root Node -->
            <div
              class="relative flex items-center px-4 py-2 text-sm transition-all duration-300 cursor-pointer backdrop-blur-sm group"
              :class="[
                isCollapsed ? 'justify-center' : '',
                $route.path.startsWith('/hod-dashboard/request-list') ||
                $route.path.startsWith('/internal-access')
                  ? 'text-white'
                  : 'text-slate-200 hover:bg-gradient-to-r hover:from-orange-600/20 hover:via-amber-600/20 hover:to-yellow-600/20 hover:text-white'
              ]"
              @click="!isCollapsed && (showRequestsManagement = !showRequestsManagement)"
            >
              <!-- Tree Lines -->
              <div
                v-show="!isCollapsed"
                class="absolute left-2 top-0 bottom-0 w-px bg-slate-600/30"
              ></div>

              <!-- Expand/Collapse Icon -->
              <div
                v-show="!isCollapsed"
                class="relative flex items-center justify-center w-4 h-4 mr-2"
              >
                <div class="w-2 h-2 bg-slate-600/50 rounded-full"></div>
                <i
                  :class="[
                    'fas text-xs text-slate-400 absolute transition-transform duration-200',
                    showRequestsManagement ? 'fa-minus' : 'fa-plus'
                  ]"
                ></i>
              </div>

              <!-- Node Icon -->
              <div
                class="relative flex items-center justify-center w-6 h-6 rounded-lg bg-gradient-to-br from-orange-500/20 via-amber-500/20 to-yellow-500/20 group-hover:from-orange-500/40 group-hover:via-amber-500/40 group-hover:to-yellow-500/40 transition-all duration-300 shadow-lg border border-white/10 mr-3"
              >
                <i class="fas fa-clipboard-check text-xs drop-shadow-lg"></i>
              </div>

              <span v-show="!isCollapsed" class="font-semibold tracking-wide drop-shadow-lg"
                >Requests Management</span
              >

              <!-- Active Indicator -->
              <div
                v-if="
                  $route.path.startsWith('/hod-dashboard/request-list') ||
                  $route.path.startsWith('/internal-access')
                "
                class="absolute right-2 w-1.5 h-1.5 bg-amber-400 rounded-full animate-pulse shadow-lg"
              ></div>
            </div>

            <!-- Requests Management Children -->
            <transition
              enter-active-class="transition ease-out duration-300"
              enter-from-class="opacity-0 -translate-y-2 scale-95"
              enter-to-class="opacity-100 translate-y-0 scale-100"
              leave-active-class="transition ease-in duration-200"
              leave-from-class="opacity-100 translate-y-0 scale-100"
              leave-to-class="opacity-0 -translate-y-2 scale-95"
            >
              <ul
                v-if="showRequestsManagement && !isCollapsed"
                class="tree-children ml-6 border-l border-slate-600/30 pl-4 space-y-1"
              >
                <!-- All Requests Node -->
                <li class="tree-leaf">
                  <router-link
                    to="/hod-dashboard/request-list"
                    class="relative flex items-center rounded-lg px-3 py-2 text-sm transition-all duration-300 backdrop-blur-sm group overflow-hidden"
                    :class="[
                      $route.path === '/hod-dashboard/request-list'
                        ? 'bg-gradient-to-r from-orange-500/40 via-amber-500/40 to-yellow-500/40 text-white shadow-xl border border-orange-400/50'
                        : 'text-slate-300 hover:bg-gradient-to-r hover:from-orange-600/30 hover:via-amber-600/30 hover:to-yellow-600/30 hover:text-white hover:shadow-lg'
                    ]"
                  >
                    <!-- Tree Connector -->
                    <div class="absolute -left-4 top-1/2 w-3 h-px bg-slate-600/30"></div>
                    <div
                      class="absolute -left-4 top-1/2 w-1 h-1 bg-slate-600/50 rounded-full transform -translate-y-0.5"
                    ></div>

                    <!-- Background Glow -->
                    <div
                      class="absolute inset-0 bg-gradient-to-r from-orange-500/10 via-amber-500/10 to-yellow-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                    ></div>

                    <div
                      class="relative flex items-center justify-center w-5 h-5 rounded bg-gradient-to-br from-orange-500/30 via-amber-500/30 to-yellow-500/30 group-hover:from-orange-500/50 group-hover:via-amber-500/50 group-hover:to-yellow-500/50 transition-all duration-300 shadow border border-white/10 mr-3"
                    >
                      <i class="fas fa-list text-xs drop-shadow-lg"></i>
                    </div>
                    <div class="relative flex-1">
                      <span class="font-medium tracking-wide drop-shadow-lg block"
                        >Access Requests</span
                      >
                      <span
                        class="text-xs opacity-70 group-hover:opacity-100 transition-opacity duration-300"
                        >Review pending requests</span
                      >
                    </div>

                    <!-- Active Indicator -->
                    <div
                      v-if="$route.path === '/hod-dashboard/request-list'"
                      class="absolute right-2 w-1.5 h-1.5 bg-amber-400 rounded-full animate-pulse shadow-lg"
                    ></div>
                  </router-link>
                </li>

                <!-- Request Details Node (Dynamic) -->
                <li
                  v-if="$route.query.id && $route.path === '/internal-access/details'"
                  class="tree-leaf"
                >
                  <div
                    class="relative flex items-center rounded-lg px-3 py-2 text-sm transition-all duration-300 backdrop-blur-sm bg-gradient-to-r from-amber-500/40 via-yellow-500/40 to-orange-500/40 text-white shadow-xl border border-amber-400/50"
                  >
                    <!-- Tree Connector -->
                    <div class="absolute -left-4 top-1/2 w-3 h-px bg-slate-600/30"></div>
                    <div
                      class="absolute -left-4 top-1/2 w-1 h-1 bg-slate-600/50 rounded-full transform -translate-y-0.5"
                    ></div>

                    <div
                      class="relative flex items-center justify-center w-5 h-5 rounded bg-gradient-to-br from-amber-500/50 via-yellow-500/50 to-orange-500/50 transition-all duration-300 shadow border border-white/20 mr-3"
                    >
                      <i class="fas fa-clipboard-check text-xs drop-shadow-lg"></i>
                    </div>
                    <div class="relative flex-1">
                      <span class="font-medium tracking-wide drop-shadow-lg block"
                        >Request Details</span
                      >
                      <span class="text-xs opacity-80">ID: {{ $route.query.id }}</span>
                    </div>

                    <!-- Active Indicator -->
                    <div
                      class="absolute right-2 w-1.5 h-1.5 bg-amber-300 rounded-full animate-pulse shadow-lg"
                    ></div>
                  </div>
                </li>
              </ul>
            </transition>
          </li>
        </template>

        <!-- Device Management Section (ICT Officer only) -->
        <template v-if="deviceManagementItems.length > 0">
          <li class="tree-node mt-4">
            <!-- Device Management Root Node -->
            <div
              class="relative flex items-center px-4 py-2 text-sm transition-all duration-300 cursor-pointer backdrop-blur-sm group"
              :class="[
                isCollapsed ? 'justify-center' : '',
                $route.path.startsWith('/ict-approval')
                  ? 'text-white'
                  : 'text-slate-200 hover:bg-gradient-to-r hover:from-teal-600/20 hover:via-cyan-600/20 hover:to-emerald-600/20 hover:text-white'
              ]"
              @click="!isCollapsed && (showDeviceManagement = !showDeviceManagement)"
            >
              <!-- Tree Lines -->
              <div
                v-show="!isCollapsed"
                class="absolute left-2 top-0 bottom-0 w-px bg-slate-600/30"
              ></div>

              <!-- Expand/Collapse Icon -->
              <div
                v-show="!isCollapsed"
                class="relative flex items-center justify-center w-4 h-4 mr-2"
              >
                <div class="w-2 h-2 bg-slate-600/50 rounded-full"></div>
                <i
                  :class="[
                    'fas text-xs text-slate-400 absolute transition-transform duration-200',
                    showDeviceManagement ? 'fa-minus' : 'fa-plus'
                  ]"
                ></i>
              </div>

              <!-- Node Icon -->
              <div
                class="relative flex items-center justify-center w-6 h-6 rounded-lg bg-gradient-to-br from-teal-500/20 via-cyan-500/20 to-emerald-500/20 group-hover:from-teal-500/40 group-hover:via-cyan-500/40 group-hover:to-emerald-500/40 transition-all duration-300 shadow-lg border border-white/10 mr-3"
              >
                <i class="fas fa-clipboard-list text-xs drop-shadow-lg"></i>
              </div>

              <span v-show="!isCollapsed" class="font-semibold tracking-wide drop-shadow-lg"
                >Device Management</span
              >

              <!-- Active Indicator -->
              <div
                v-if="$route.path.startsWith('/ict-approval')"
                class="absolute right-2 w-1.5 h-1.5 bg-emerald-400 rounded-full animate-pulse shadow-lg"
              ></div>
            </div>

            <!-- Device Management Children -->
            <transition
              enter-active-class="transition ease-out duration-300"
              enter-from-class="opacity-0 -translate-y-2 scale-95"
              enter-to-class="opacity-100 translate-y-0 scale-100"
              leave-active-class="transition ease-in duration-200"
              leave-from-class="opacity-100 translate-y-0 scale-100"
              leave-to-class="opacity-0 -translate-y-2 scale-95"
            >
              <ul
                v-if="showDeviceManagement && !isCollapsed"
                class="tree-children ml-6 border-l border-slate-600/30 pl-4 space-y-1"
              >
                <!-- All Requests Node -->
                <li class="tree-leaf">
                  <router-link
                    to="/ict-approval/requests"
                    class="relative flex items-center rounded-lg px-3 py-2 text-sm transition-all duration-300 backdrop-blur-sm group overflow-hidden"
                    :class="[
                      $route.path === '/ict-approval/requests'
                        ? 'bg-gradient-to-r from-teal-500/40 via-cyan-500/40 to-emerald-500/40 text-white shadow-xl border border-teal-400/50'
                        : 'text-slate-300 hover:bg-gradient-to-r hover:from-teal-600/30 hover:via-cyan-600/30 hover:to-emerald-600/30 hover:text-white hover:shadow-lg'
                    ]"
                  >
                    <!-- Tree Connector -->
                    <div class="absolute -left-4 top-1/2 w-3 h-px bg-slate-600/30"></div>
                    <div
                      class="absolute -left-4 top-1/2 w-1 h-1 bg-slate-600/50 rounded-full transform -translate-y-0.5"
                    ></div>

                    <!-- Background Glow -->
                    <div
                      class="absolute inset-0 bg-gradient-to-r from-teal-500/10 via-cyan-500/10 to-emerald-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                    ></div>

                    <div
                      class="relative flex items-center justify-center w-5 h-5 rounded bg-gradient-to-br from-teal-500/30 via-cyan-500/30 to-emerald-500/30 group-hover:from-teal-500/50 group-hover:via-cyan-500/50 group-hover:to-emerald-500/50 transition-all duration-300 shadow border border-white/10 mr-3"
                    >
                      <i class="fas fa-list text-xs drop-shadow-lg"></i>
                    </div>
                    <div class="relative flex-1">
                      <span class="font-medium tracking-wide drop-shadow-lg block"
                        >All Requests</span
                      >
                      <span
                        class="text-xs opacity-70 group-hover:opacity-100 transition-opacity duration-300"
                        >View device requests</span
                      >
                    </div>

                    <!-- Active Indicator -->
                    <div
                      v-if="$route.path === '/ict-approval/requests'"
                      class="absolute right-2 w-1.5 h-1.5 bg-emerald-400 rounded-full animate-pulse shadow-lg"
                    ></div>
                  </router-link>
                </li>
              </ul>
            </transition>
          </li>
        </template>

        <!-- Forms Section - REMOVED: Forms are now only accessible through Submit New Request button in dashboard -->
      </ul>
    </nav>

    <!-- Footer / Logout -->
    <div class="relative border-t border-slate-600/20 p-4 mt-auto">
      <!-- Background Pattern -->
      <div
        class="absolute inset-0 bg-gradient-to-r from-red-500/5 via-orange-500/5 to-red-500/5 backdrop-blur-sm"
      ></div>

      <button
        @click="handleLogout"
        class="relative w-full flex items-center rounded-xl px-4 py-3 text-sm text-red-300 hover:bg-gradient-to-r hover:from-red-500/30 hover:via-orange-500/30 hover:to-red-500/30 hover:text-white transition-all duration-300 backdrop-blur-sm group hover:shadow-xl transform hover:scale-105 overflow-hidden"
        :class="isCollapsed ? 'justify-center' : ''"
        :title="isCollapsed ? 'Logout' : ''"
      >
        <!-- Background Glow -->
        <div
          class="absolute inset-0 bg-gradient-to-r from-red-500/10 via-orange-500/10 to-red-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
        ></div>

        <div
          class="relative flex items-center justify-center w-8 h-8 rounded-lg bg-gradient-to-br from-red-500/20 via-orange-500/20 to-red-500/20 group-hover:from-red-500/40 group-hover:via-orange-500/40 group-hover:to-red-500/40 transition-all duration-300 shadow-lg border border-white/10"
        >
          <i class="fas fa-sign-out-alt text-sm drop-shadow-lg"></i>
        </div>
        <span v-show="!isCollapsed" class="relative ml-3 font-semibold tracking-wide drop-shadow-lg"
          >Logout</span
        >
      </button>

      <div
        v-show="!isCollapsed"
        class="mt-3 text-xs text-slate-400 text-center drop-shadow-sm font-medium"
      >
        <div class="flex items-center justify-center gap-1 mb-0.5">
          <div class="w-1 h-1 bg-slate-500 rounded-full"></div>
          <span>© 2025 Muhimbili National Hospital</span>
          <div class="w-1 h-1 bg-slate-500 rounded-full"></div>
        </div>
        <div class="text-xs text-slate-500">ICT Management System</div>
      </div>
      <div v-show="isCollapsed" class="mt-1 text-xs text-slate-400 text-center">©</div>
    </div>
  </aside>
</template>

<script>
  import { computed, ref, watch, onMounted, nextTick } from 'vue'
  import { useRouter } from 'vue-router'
  import { ROLE_PERMISSIONS, ROLES } from '../utils/permissions'
  import { useAuth } from '../composables/useAuth'
  import auth from '../utils/auth'
  import { logoutGuard } from '@/utils/logoutGuard'

  export default {
    name: 'DynamicSidebar',
    props: {
      collapsed: {
        type: Boolean,
        default: false
      }
    },
    emits: ['update:collapsed'],
    setup(props, { emit }) {
      const router = useRouter()
      const { user: currentUser, userRole, logout, isAuthenticated, isLoading } = useAuth()

      // Stable role tracking to prevent flickering
      const stableUserRole = ref(null)

      // Watch for authentication state changes with debouncing
      watch(
        [isAuthenticated, userRole],
        ([authenticated, role]) => {
          if (authenticated && role) {
            stableUserRole.value = role
            console.log('🔄 Sidebar: Auth state updated - Role:', role)
          }
          // Don't immediately clear the role to prevent flickering during refresh
        },
        { immediate: true }
      )

      // Initialize auth state on mount
      onMounted(async () => {
        console.log('🔄 Sidebar mounted - checking auth state')
        console.log('  - isAuthenticated:', isAuthenticated.value)
        console.log('  - userRole:', userRole.value)
        console.log('  - isLoading:', isLoading.value)

        // Check if we have stored auth data
        const token = localStorage.getItem('auth_token')
        const userData = localStorage.getItem('user_data')

        if (token && userData) {
          try {
            const user = JSON.parse(userData)
            if (user.role) {
              // Set stable role immediately from stored data
              stableUserRole.value = user.role
              console.log('✅ Sidebar: Set stable role from stored data:', user.role)
            }
          } catch (error) {
            console.error('Failed to parse stored user data:', error)
          }

          // If not authenticated, reinitialize in background
          if (!isAuthenticated.value && !isLoading.value) {
            console.log('🔄 Sidebar: Reinitializing auth in background...')
            await nextTick() // Wait for next tick to avoid blocking UI
            auth.initializeAuth()
          }
        }

        // If already authenticated, use current role
        if (isAuthenticated.value && userRole.value) {
          stableUserRole.value = userRole.value
          console.log('✅ Sidebar: Using current auth role:', userRole.value)
        }
      })

      // Local state
      const showUserMgmt = ref(false)
      const showDeviceManagement = ref(false)
      const showRequestsManagement = ref(false)
      const showDashboard = ref(true) // Dashboard expanded by default

      // Computed property for v-model implementation
      const isCollapsed = computed({
        get: () => props.collapsed,
        set: (value) => emit('update:collapsed', value)
      })

      const containerWidthClass = computed(() => (isCollapsed.value ? 'w-16' : 'w-72'))

      // Get menu items based on stable user role
      const menuItems = computed(() => {
        const role = stableUserRole.value || userRole.value
        if (!role) return []

        const permissions = ROLE_PERMISSIONS[role]
        if (!permissions) return []

        return permissions.routes
          .map((route) => {
            const metadata = getRouteMetadata(route)
            return {
              path: route,
              ...metadata
            }
          })
          .filter((item) => item.name)
      })

      // Categorize menu items
      const dashboardItems = computed(() =>
        menuItems.value.filter((item) => item.category === 'dashboard')
      )

      const userManagementItems = computed(() =>
        menuItems.value.filter((item) => item.category === 'user-management')
      )

      // formItems removed - forms are now only accessible through Submit New Request button

      const deviceManagementItems = computed(() =>
        menuItems.value.filter((item) => item.category === 'device-management')
      )

      const requestsManagementItems = computed(() =>
        menuItems.value.filter((item) => item.category === 'requests-management')
      )

      // Methods
      function toggleCollapse() {
        isCollapsed.value = !isCollapsed.value
      }

      function getRoleDisplayName(role) {
        const roleNames = {
          [ROLES.ADMIN]: 'Administrator',
          [ROLES.DIVISIONAL_DIRECTOR]: 'Divisional Director',
          [ROLES.HEAD_OF_DEPARTMENT]: 'Head of Department',

          [ROLES.ICT_DIRECTOR]: 'ICT Director',
          [ROLES.STAFF]: 'Staff Member',
          [ROLES.ICT_OFFICER]: 'ICT Officer',
          [ROLES.SECRETARY_ICT]: 'Secretary ICT'
        }
        return roleNames[role] || role
      }

      // Form-related methods removed - forms are now only accessible through Submit New Request button

      async function handleLogout() {
        try {
          await logoutGuard.executeLogout(async () => {
            await logout()
          })
          router.push('/')
        } catch (error) {
          console.error('Logout failed:', error)
          // Force redirect even if logout fails
          router.push('/')
        }
      }

      function getRouteMetadata(route) {
        // Updated route metadata mapping based on current permissions
        const metadata = {
          // Dashboards
          '/admin-dashboard': {
            name: 'AdminDashboard',
            displayName: 'Admin Dashboard',
            icon: 'fas fa-user-shield',
            category: 'dashboard',
            description: 'Administrative control panel'
          },
          '/user-dashboard': {
            name: 'UserDashboard',
            displayName: 'User Dashboard',
            icon: 'fas fa-user',
            category: 'dashboard',
            description: 'User portal and services'
          },
          '/dict-dashboard': {
            name: 'DictDashboard',
            displayName: 'ICT Director Dashboard',
            icon: 'fas fa-user-cog',
            category: 'dashboard',
            description: 'ICT Director control panel'
          },
          '/hod-dashboard': {
            name: 'HodDashboard',
            displayName: 'HOD Dashboard',
            icon: 'fas fa-user-tie',
            category: 'dashboard',
            description: 'Head of Department panel'
          },
          '/hod-it-dashboard': {
            name: 'HodItDashboard',
            displayName: 'HOD IT Dashboard',
            icon: 'fas fa-laptop-code',
            category: 'dashboard',
            description: 'Head of IT Department panel'
          },
          '/divisional-dashboard': {
            name: 'DivisionalDashboard',
            displayName: 'Divisional Dashboard',
            icon: 'fas fa-building',
            category: 'dashboard',
            description: 'Divisional Director panel'
          },
          '/ict-dashboard': {
            name: 'IctDashboard',
            displayName: 'ICT Dashboard',
            icon: 'fas fa-laptop-code',
            category: 'dashboard',
            description: 'ICT Officer panel'
          },

          // User Management (Admin only)
          '/jeeva-users': {
            name: 'JeevaUsers',
            displayName: 'Jeeva Users',
            icon: 'fas fa-file-medical',
            category: 'user-management',
            description: 'Manage Jeeva system users'
          },
          '/wellsoft-users': {
            name: 'WellsoftUsers',
            displayName: 'Wellsoft Users',
            icon: 'fas fa-laptop-medical',
            category: 'user-management',
            description: 'Manage Wellsoft system users'
          },
          '/internet-users': {
            name: 'InternetUsers',
            displayName: 'Internet Users',
            icon: 'fas fa-wifi',
            category: 'user-management',
            description: 'Manage internet access users'
          },

          // Access Forms (Approval workflows)
          '/jeeva-access': {
            name: 'JeevaAccessForm',
            displayName: 'Jeeva Access',
            icon: 'fas fa-file-medical',
            category: 'access-form',
            description: 'Jeeva system access form'
          },
          '/wellsoft-access': {
            name: 'WellsoftAccessForm',
            displayName: 'Wellsoft Access',
            icon: 'fas fa-laptop-medical',
            category: 'access-form',
            description: 'Wellsoft system access form'
          },
          '/internet-access': {
            name: 'InternetAccessForm',
            displayName: 'Internet Access',
            icon: 'fas fa-wifi',
            category: 'access-form',
            description: 'Internet access form'
          },
          '/both-service-form': {
            name: 'BothServiceForm',
            displayName: 'Combined Services',
            icon: 'fas fa-file-alt',
            category: 'access-form',
            description: 'Multiple service access form'
          },

          // User Forms (Staff submissions)
          '/user-combined-form': {
            name: 'UserCombinedForm',
            displayName: 'Access Request',
            icon: 'fas fa-file-plus',
            category: 'user-form',
            description: 'Submit access request'
          },
          '/booking-service': {
            name: 'BookingService',
            displayName: 'Device Booking',
            icon: 'fas fa-calendar-plus',
            category: 'user-form',
            description: 'Book devices and equipment'
          },

          // Device Management (ICT Officer only)
          '/ict-approval/requests': {
            name: 'RequestsList',
            displayName: 'Device Requests',
            icon: 'fas fa-clipboard-list',
            category: 'device-management',
            description: 'Manage device borrowing requests'
          },

          // Requests Management (for approvers)
          '/hod-dashboard/request-list': {
            name: 'HODDashboardRequestList',
            displayName: 'Access Requests',
            icon: 'fas fa-clipboard-check',
            category: 'requests-management',
            description: 'Review access requests'
          },
          '/internal-access/list': {
            name: 'InternalAccessList',
            displayName: 'Access Requests',
            icon: 'fas fa-clipboard-check',
            category: 'requests-management',
            description: 'Review access requests'
          }
        }

        return metadata[route] || {}
      }

      return {
        // State
        isCollapsed,
        showUserMgmt,
        showDeviceManagement,
        showRequestsManagement,
        showDashboard,
        stableUserRole,

        // Computed
        currentUser,
        userRole,
        isAuthenticated,
        isLoading,
        containerWidthClass,
        dashboardItems,
        userManagementItems,
        deviceManagementItems,
        requestsManagementItems,

        // Methods
        toggleCollapse,
        getRoleDisplayName,
        handleLogout
      }
    }
  }
</script>

<style scoped>
  /* Custom scrollbar */
  aside::-webkit-scrollbar {
    width: 6px;
  }

  aside::-webkit-scrollbar-thumb {
    background-color: rgba(0, 0, 0, 0.15);
    border-radius: 3px;
  }

  aside::-webkit-scrollbar-track {
    background-color: transparent;
  }

  /* Active route indicator */
  .router-link-active {
    @apply bg-indigo-100 text-indigo-700;
  }
</style>
