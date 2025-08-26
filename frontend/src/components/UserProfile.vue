<template>
  <div class="flex flex-col h-screen">
    <!-- AppHeader removed -->
    <div class="flex flex-1 overflow-hidden">
      <DynamicSidebar v-model:collapsed="sidebarCollapsed" />
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

        <div class="max-w-6xl mx-auto relative z-10">
          <!-- Blue Header Section -->
          <div
            class="medical-glass-card rounded-t-3xl p-8 mb-0 border-b border-blue-300/30 bg-gradient-to-r from-blue-600/30 to-blue-700/30"
          >
            <div class="flex items-center justify-between">
              <!-- Left: User Avatar and Basic Info -->
              <div class="flex items-center space-x-6">
                <div class="relative">
                  <div
                    class="w-24 h-24 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center shadow-xl border-4 border-white/20 relative overflow-hidden"
                  >
                    <div
                      class="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent rounded-full"
                    ></div>
                    <i
                      class="fas fa-user text-white text-3xl relative z-10 drop-shadow-lg"
                    ></i>
                    <div
                      class="absolute top-2 right-2 w-2 h-2 bg-green-400 rounded-full animate-pulse border border-white"
                    ></div>
                  </div>
                  <!-- Status Indicator -->
                  <div
                    class="absolute -bottom-1 -right-1 bg-green-500 text-white text-xs px-2 py-1 rounded-full font-bold shadow-lg"
                  >
                    Online
                  </div>
                </div>

                <div>
                  <h1
                    class="text-3xl font-bold text-white mb-2 flex items-center"
                  >
                    <i class="fas fa-user-circle mr-3 text-blue-300"></i>
                    {{ user?.name || 'User Profile' }}
                  </h1>
                  <div class="flex items-center space-x-4">
                    <span
                      class="bg-blue-500/20 text-blue-200 px-3 py-1 rounded-full text-sm font-medium border border-blue-400/30"
                    >
                      <i class="fas fa-id-badge mr-1"></i>
                      {{ user?.role || 'Staff' }}
                    </span>
                    <span
                      class="bg-green-500/20 text-green-200 px-3 py-1 rounded-full text-sm font-medium border border-green-400/30"
                    >
                      <i class="fas fa-check-circle mr-1"></i>
                      Active
                    </span>
                  </div>
                </div>
              </div>

              <!-- Right: Quick Actions -->
              <div class="flex items-center space-x-3">
                <button
                  @click="editMode = !editMode"
                  class="px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-all duration-300 font-medium flex items-center shadow-lg hover:shadow-xl transform hover:scale-105"
                >
                  <i
                    :class="editMode ? 'fas fa-times' : 'fas fa-edit'"
                    class="mr-2"
                  ></i>
                  {{ editMode ? 'Cancel' : 'Edit Profile' }}
                </button>
                <button
                  v-if="editMode"
                  @click="saveProfile"
                  :disabled="isSaving"
                  class="px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-all duration-300 font-medium flex items-center shadow-lg hover:shadow-xl transform hover:scale-105 disabled:opacity-50"
                >
                  <i
                    :class="isSaving ? 'fas fa-spinner fa-spin' : 'fas fa-save'"
                    class="mr-2"
                  ></i>
                  {{ isSaving ? 'Saving...' : 'Save Changes' }}
                </button>
              </div>
            </div>
          </div>

          <!-- Profile Content -->
          <div class="medical-glass-card rounded-b-3xl overflow-hidden">
            <div class="p-8 space-y-8">
              <!-- Personal Information Section -->
              <div
                class="medical-card bg-gradient-to-r from-blue-600/25 to-blue-700/25 border-2 border-blue-400/40 p-6 rounded-2xl backdrop-blur-sm hover:shadow-2xl hover:shadow-blue-500/20 transition-all duration-500 group relative overflow-hidden"
              >
                <!-- Animated Background Layers -->
                <div
                  class="absolute inset-0 bg-gradient-to-r from-blue-500/10 via-blue-500/5 to-blue-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-700"
                ></div>
                <div
                  class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-blue-400/20 to-transparent rounded-full blur-2xl group-hover:scale-150 transition-transform duration-1000"
                ></div>

                <div class="relative z-10">
                  <div class="flex items-center space-x-4 mb-6">
                    <div
                      class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-blue-300/50 relative overflow-hidden"
                    >
                      <div
                        class="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent rounded-xl"
                      ></div>
                      <i
                        class="fas fa-user-edit text-white text-xl relative z-10 drop-shadow-lg"
                      ></i>
                    </div>
                    <div>
                      <h3
                        class="text-2xl font-bold text-white flex items-center"
                      >
                        <i class="fas fa-info-circle mr-3 text-blue-300"></i>
                        Personal Information
                      </h3>
                      <p class="text-sm text-blue-100/80">
                        Manage your personal details and contact information
                      </p>
                    </div>
                  </div>

                  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Full Name -->
                    <div class="group">
                      <label
                        class="block text-sm font-bold text-blue-100 mb-3 flex items-center"
                      >
                        <i class="fas fa-user mr-2 text-blue-300"></i>
                        Full Name
                      </label>
                      <div class="relative">
                        <input
                          v-model="profileData.name"
                          type="text"
                          :readonly="!editMode"
                          class="medical-input w-full px-4 py-4 bg-white/15 border-2 border-blue-300/30 rounded-xl focus:border-blue-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-blue-500/20 group-hover:border-blue-400/50"
                          :class="{ 'cursor-not-allowed': !editMode }"
                          placeholder="Enter your full name"
                        />
                        <div
                          class="absolute right-3 top-1/2 transform -translate-y-1/2 text-blue-300/50"
                        >
                          <i class="fas fa-user-circle"></i>
                        </div>
                      </div>
                    </div>

                    <!-- Email -->
                    <div class="group">
                      <label
                        class="block text-sm font-bold text-blue-100 mb-3 flex items-center"
                      >
                        <i class="fas fa-envelope mr-2 text-blue-300"></i>
                        Email Address
                      </label>
                      <div class="relative">
                        <input
                          v-model="profileData.email"
                          type="email"
                          :readonly="!editMode"
                          class="medical-input w-full px-4 py-4 bg-white/15 border-2 border-blue-300/30 rounded-xl focus:border-blue-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-blue-500/20 group-hover:border-blue-400/50"
                          :class="{ 'cursor-not-allowed': !editMode }"
                          placeholder="Enter your email address"
                        />
                        <div
                          class="absolute right-3 top-1/2 transform -translate-y-1/2 text-blue-300/50"
                        >
                          <i class="fas fa-at"></i>
                        </div>
                      </div>
                    </div>

                    <!-- Phone Number -->
                    <div class="group">
                      <label
                        class="block text-sm font-bold text-blue-100 mb-3 flex items-center"
                      >
                        <i class="fas fa-phone mr-2 text-blue-300"></i>
                        Phone Number
                      </label>
                      <div class="relative">
                        <input
                          v-model="profileData.phone"
                          type="tel"
                          :readonly="!editMode"
                          class="medical-input w-full px-4 py-4 bg-white/15 border-2 border-blue-300/30 rounded-xl focus:border-blue-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-blue-500/20 group-hover:border-blue-400/50"
                          :class="{ 'cursor-not-allowed': !editMode }"
                          placeholder="Enter your phone number"
                        />
                        <div
                          class="absolute right-3 top-1/2 transform -translate-y-1/2 text-blue-300/50"
                        >
                          <i class="fas fa-mobile-alt"></i>
                        </div>
                      </div>
                    </div>

                    <!-- Department -->
                    <div class="group">
                      <label
                        class="block text-sm font-bold text-blue-100 mb-3 flex items-center"
                      >
                        <i class="fas fa-building mr-2 text-blue-300"></i>
                        Department
                      </label>
                      <div class="relative">
                        <input
                          v-model="profileData.department"
                          type="text"
                          readonly
                          class="medical-input w-full px-4 py-4 bg-white/10 border-2 border-blue-300/20 rounded-xl text-white/70 backdrop-blur-sm cursor-not-allowed"
                          placeholder="Department information"
                        />
                        <div
                          class="absolute right-3 top-1/2 transform -translate-y-1/2 text-blue-300/30"
                        >
                          <i class="fas fa-lock"></i>
                        </div>
                      </div>
                      <p class="text-xs text-blue-200/60 mt-1 italic">
                        <i class="fas fa-info-circle mr-1"></i>
                        Department information is managed by administrators
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Account Settings Section -->
              <div
                class="medical-card bg-gradient-to-r from-teal-600/25 to-green-600/25 border-2 border-teal-400/40 p-6 rounded-2xl backdrop-blur-sm hover:shadow-2xl hover:shadow-teal-500/20 transition-all duration-500 group relative overflow-hidden"
              >
                <!-- Animated Background Layers -->
                <div
                  class="absolute inset-0 bg-gradient-to-r from-teal-500/10 via-green-500/5 to-teal-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-700"
                ></div>
                <div
                  class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-teal-400/20 to-transparent rounded-full blur-2xl group-hover:scale-150 transition-transform duration-1000"
                ></div>

                <div class="relative z-10">
                  <div class="flex items-center space-x-4 mb-6">
                    <div
                      class="w-16 h-16 bg-gradient-to-br from-teal-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-teal-300/50 relative overflow-hidden"
                    >
                      <div
                        class="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent rounded-xl"
                      ></div>
                      <i
                        class="fas fa-cogs text-white text-xl relative z-10 drop-shadow-lg"
                      ></i>
                    </div>
                    <div>
                      <h3
                        class="text-2xl font-bold text-white flex items-center"
                      >
                        <i class="fas fa-sliders-h mr-3 text-teal-300"></i>
                        Account Settings
                      </h3>
                      <p class="text-sm text-teal-100/80">
                        Configure your account preferences and security
                      </p>
                    </div>
                  </div>

                  <div class="space-y-4">
                    <!-- Email Notifications -->
                    <div
                      class="flex items-center justify-between p-4 bg-white/10 rounded-xl backdrop-blur-sm border border-teal-300/20"
                    >
                      <div class="flex items-center space-x-3">
                        <div
                          class="w-10 h-10 bg-teal-500/30 rounded-lg flex items-center justify-center"
                        >
                          <i class="fas fa-bell text-teal-300"></i>
                        </div>
                        <div>
                          <h4 class="font-semibold text-white">
                            Email Notifications
                          </h4>
                          <p class="text-sm text-teal-200/70">
                            Receive updates about your requests
                          </p>
                        </div>
                      </div>
                      <label class="relative inline-flex items-center cursor-pointer">
                        <input
                          v-model="profileData.emailNotifications"
                          type="checkbox"
                          class="sr-only peer"
                          :disabled="!editMode"
                        />
                        <div
                          class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-teal-300 dark:peer-focus:ring-teal-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-teal-600"
                        ></div>
                      </label>
                    </div>

                    <!-- Two-Factor Authentication -->
                    <div
                      class="flex items-center justify-between p-4 bg-white/10 rounded-xl backdrop-blur-sm border border-teal-300/20"
                    >
                      <div class="flex items-center space-x-3">
                        <div
                          class="w-10 h-10 bg-teal-500/30 rounded-lg flex items-center justify-center"
                        >
                          <i class="fas fa-shield-alt text-teal-300"></i>
                        </div>
                        <div>
                          <h4 class="font-semibold text-white">
                            Two-Factor Authentication
                          </h4>
                          <p class="text-sm text-teal-200/70">
                            Add extra security to your account
                          </p>
                        </div>
                      </div>
                      <button
                        class="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition-colors text-sm font-medium"
                        :disabled="!editMode"
                      >
                        Configure
                      </button>
                    </div>

                    <!-- Password Change -->
                    <div
                      class="flex items-center justify-between p-4 bg-white/10 rounded-xl backdrop-blur-sm border border-teal-300/20"
                    >
                      <div class="flex items-center space-x-3">
                        <div
                          class="w-10 h-10 bg-teal-500/30 rounded-lg flex items-center justify-center"
                        >
                          <i class="fas fa-key text-teal-300"></i>
                        </div>
                        <div>
                          <h4 class="font-semibold text-white">Password</h4>
                          <p class="text-sm text-teal-200/70">
                            Last changed 30 days ago
                          </p>
                        </div>
                      </div>
                      <button
                        @click="showPasswordModal = true"
                        class="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition-colors text-sm font-medium"
                      >
                        Change Password
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Activity Summary -->
              <div
                class="medical-card bg-gradient-to-r from-purple-600/25 to-indigo-600/25 border-2 border-purple-400/40 p-6 rounded-2xl backdrop-blur-sm hover:shadow-2xl hover:shadow-purple-500/20 transition-all duration-500 group relative overflow-hidden"
              >
                <div class="relative z-10">
                  <div class="flex items-center space-x-4 mb-6">
                    <div
                      class="w-16 h-16 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-purple-300/50 relative overflow-hidden"
                    >
                      <div
                        class="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent rounded-xl"
                      ></div>
                      <i
                        class="fas fa-chart-line text-white text-xl relative z-10 drop-shadow-lg"
                      ></i>
                    </div>
                    <div>
                      <h3
                        class="text-2xl font-bold text-white flex items-center"
                      >
                        <i class="fas fa-activity mr-3 text-purple-300"></i>
                        Activity Summary
                      </h3>
                      <p class="text-sm text-purple-100/80">
                        Your recent activity and statistics
                      </p>
                    </div>
                  </div>

                  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Total Requests -->
                    <div
                      class="bg-white/10 p-4 rounded-xl backdrop-blur-sm border border-purple-300/20 text-center"
                    >
                      <div
                        class="w-12 h-12 bg-purple-500/30 rounded-lg flex items-center justify-center mx-auto mb-3"
                      >
                        <i class="fas fa-file-alt text-purple-300 text-xl"></i>
                      </div>
                      <div class="text-2xl font-bold text-white mb-1">12</div>
                      <div class="text-sm text-purple-200/70">
                        Total Requests
                      </div>
                    </div>

                    <!-- Pending Requests -->
                    <div
                      class="bg-white/10 p-4 rounded-xl backdrop-blur-sm border border-purple-300/20 text-center"
                    >
                      <div
                        class="w-12 h-12 bg-orange-500/30 rounded-lg flex items-center justify-center mx-auto mb-3"
                      >
                        <i class="fas fa-clock text-orange-300 text-xl"></i>
                      </div>
                      <div class="text-2xl font-bold text-white mb-1">3</div>
                      <div class="text-sm text-orange-200/70">
                        Pending Requests
                      </div>
                    </div>

                    <!-- Last Login -->
                    <div
                      class="bg-white/10 p-4 rounded-xl backdrop-blur-sm border border-purple-300/20 text-center"
                    >
                      <div
                        class="w-12 h-12 bg-green-500/30 rounded-lg flex items-center justify-center mx-auto mb-3"
                      >
                        <i class="fas fa-sign-in-alt text-green-300 text-xl"></i>
                      </div>
                      <div class="text-sm font-bold text-white mb-1">Today</div>
                      <div class="text-sm text-green-200/70">Last Login</div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="flex justify-between items-center pt-6">
                <button
                  @click="goBack"
                  class="px-6 py-3 bg-gray-500 text-white rounded-xl hover:bg-gray-600 transition-all duration-300 font-semibold flex items-center shadow-lg hover:shadow-xl text-sm transform hover:scale-105"
                >
                  <i class="fas fa-arrow-left mr-2"></i>
                  Back to Dashboard
                </button>

                <div class="flex space-x-3">
                  <button
                    @click="exportProfile"
                    class="px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-all duration-300 font-semibold flex items-center shadow-lg hover:shadow-xl text-sm transform hover:scale-105"
                  >
                    <i class="fas fa-download mr-2"></i>
                    Export Profile
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

    <!-- Password Change Modal -->
    <div
      v-if="showPasswordModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
    >
      <div
        class="bg-white rounded-xl shadow-2xl max-w-md w-full transform transition-all duration-300 scale-100"
      >
        <div class="p-6">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-gray-800">Change Password</h3>
            <button
              @click="showPasswordModal = false"
              class="text-gray-400 hover:text-gray-600"
            >
              <i class="fas fa-times"></i>
            </button>
          </div>

          <form @submit.prevent="changePassword" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Current Password
              </label>
              <input
                v-model="passwordData.current"
                type="password"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Enter current password"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                New Password
              </label>
              <input
                v-model="passwordData.new"
                type="password"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Enter new password"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Confirm New Password
              </label>
              <input
                v-model="passwordData.confirm"
                type="password"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Confirm new password"
              />
            </div>

            <div class="flex space-x-3 pt-4">
              <button
                type="button"
                @click="showPasswordModal = false"
                class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="isChangingPassword"
                class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors disabled:opacity-50"
              >
                <span v-if="isChangingPassword">Changing...</span>
                <span v-else>Change Password</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Success/Error Messages -->
    <div
      v-if="showMessage"
      class="fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300"
      :class="messageType === 'success' ? 'bg-green-600 text-white' : 'bg-red-600 text-white'"
    >
      <div class="flex items-center">
        <i
          :class="messageType === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle'"
          class="mr-2"
        ></i>
        {{ message }}
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from '@/composables/useAuth'
import DynamicSidebar from './DynamicSidebar.vue'
import AppFooter from './footer.vue'

export default {
  name: 'UserProfile',
  components: {
    DynamicSidebar,
    AppFooter
  },
  setup() {
    const router = useRouter()
    const { user } = useAuth()

    // Reactive data
    const sidebarCollapsed = ref(false)
    const editMode = ref(false)
    const isSaving = ref(false)
    const showPasswordModal = ref(false)
    const isChangingPassword = ref(false)
    const showMessage = ref(false)
    const message = ref('')
    const messageType = ref('success')

    // Profile data
    const profileData = ref({
      name: '',
      email: '',
      phone: '',
      department: '',
      emailNotifications: true
    })

    // Password change data
    const passwordData = ref({
      current: '',
      new: '',
      confirm: ''
    })

    // Initialize profile data
    onMounted(() => {
      if (user.value) {
        profileData.value = {
          name: user.value.name || '',
          email: user.value.email || '',
          phone: user.value.phone || '',
          department: user.value.department || 'Not specified',
          emailNotifications: user.value.email_notifications ?? true
        }
      }
    })

    // Methods
    const saveProfile = async() => {
      isSaving.value = true
      try {
        // Simulate API call
        await new Promise(resolve => setTimeout(resolve, 1000))

        showNotification('Profile updated successfully!', 'success')
        editMode.value = false
      } catch (error) {
        showNotification('Failed to update profile. Please try again.', 'error')
      } finally {
        isSaving.value = false
      }
    }

    const changePassword = async() => {
      if (passwordData.value.new !== passwordData.value.confirm) {
        showNotification('New passwords do not match!', 'error')
        return
      }

      isChangingPassword.value = true
      try {
        // Simulate API call
        await new Promise(resolve => setTimeout(resolve, 1000))

        showNotification('Password changed successfully!', 'success')
        showPasswordModal.value = false
        passwordData.value = { current: '', new: '', confirm: '' }
      } catch (error) {
        showNotification('Failed to change password. Please try again.', 'error')
      } finally {
        isChangingPassword.value = false
      }
    }

    const exportProfile = () => {
      // Create a simple profile export
      const profileInfo = {
        name: profileData.value.name,
        email: profileData.value.email,
        phone: profileData.value.phone,
        department: profileData.value.department,
        role: user.value?.role,
        exportDate: new Date().toISOString()
      }

      const dataStr = JSON.stringify(profileInfo, null, 2)
      const dataBlob = new Blob([dataStr], { type: 'application/json' })
      const url = URL.createObjectURL(dataBlob)

      const link = document.createElement('a')
      link.href = url
      link.download = `profile_${profileData.value.name.replace(/\s+/g, '_')}_${new Date().toISOString().split('T')[0]}.json`
      link.click()

      URL.revokeObjectURL(url)
      showNotification('Profile exported successfully!', 'success')
    }

    const goBack = () => {
      router.push('/user-dashboard')
    }

    const showNotification = (msg, type = 'success') => {
      message.value = msg
      messageType.value = type
      showMessage.value = true

      setTimeout(() => {
        showMessage.value = false
      }, 3000)
    }

    return {
      user,
      sidebarCollapsed,
      editMode,
      isSaving,
      showPasswordModal,
      isChangingPassword,
      showMessage,
      message,
      messageType,
      profileData,
      passwordData,
      saveProfile,
      changePassword,
      exportProfile,
      goBack,
      showNotification
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

.animate-float {
  animation: float 6s ease-in-out infinite;
}

/* Custom transitions */
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

/* Button animations */
button:hover:not(:disabled) {
  transform: translateY(-2px) scale(1.05);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
}

button:active:not(:disabled) {
  transform: translateY(0) scale(1.02);
}

/* Toggle switch styles */
.peer:checked + div {
  background-color: #0d9488;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .grid-cols-2 {
    grid-template-columns: repeat(1, minmax(0, 1fr));
  }

  .px-8 {
    padding-left: 1rem;
    padding-right: 1rem;
  }
}
</style>
