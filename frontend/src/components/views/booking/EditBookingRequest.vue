<template>
  <div class="flex flex-col h-screen">
    <AppHeader />
    <div class="flex flex-1 overflow-hidden">
      <ModernSidebar />
      <main
        class="flex-1 p-3 bg-gradient-to-br from-blue-900 via-blue-800 to-teal-900 overflow-y-auto relative"
      >
        <!-- Background Pattern -->
        <div class="absolute inset-0 overflow-hidden">
          <div class="absolute inset-0">
            <div
              v-for="i in 20"
              :key="i"
              class="absolute text-white opacity-5 animate-float"
              :style="{
                left: Math.random() * 100 + '%',
                top: Math.random() * 100 + '%',
                animationDelay: Math.random() * 3 + 's',
                animationDuration: Math.random() * 3 + 2 + 's',
                fontSize: Math.random() * 15 + 8 + 'px'
              }"
            >
              <i
                :class="[
                  'fas',
                  ['fa-laptop', 'fa-tv', 'fa-desktop', 'fa-keyboard', 'fa-mouse', 'fa-headphones'][
                    Math.floor(Math.random() * 6)
                  ]
                ]"
              ></i>
            </div>
          </div>
        </div>

        <div class="max-w-12xl mx-auto relative z-10">
          <!-- Header Section -->
          <div
            class="booking-glass-card rounded-t-3xl p-6 mb-0 border-b border-blue-300/30 animate-fade-in"
          >
            <div class="flex justify-between items-center">
              <!-- Left Logo -->
              <div
                class="w-24 h-24 mr-6 transform hover:scale-110 transition-transform duration-300"
              >
                <div
                  class="w-full h-full bg-gradient-to-br from-blue-500/20 to-teal-500/20 rounded-2xl backdrop-blur-sm border-2 border-blue-300/40 flex items-center justify-center shadow-2xl hover:shadow-blue-500/25"
                >
                  <img
                    src="/assets/images/ngao2.png"
                    alt="National Shield"
                    class="max-w-16 max-h-16 object-contain"
                  />
                </div>
              </div>

              <!-- Center Content -->
              <div class="text-center flex-1">
                <h1
                  class="text-3xl font-bold text-white mb-4 tracking-wide drop-shadow-lg animate-fade-in"
                >
                  EDIT BOOKING REQUEST
                </h1>
                <div class="relative inline-block mb-4">
                  <div
                    class="bg-gradient-to-r from-orange-600 to-orange-700 text-white px-8 py-3 rounded-full text-lg font-bold shadow-2xl transform hover:scale-105 transition-all duration-300 border-2 border-orange-400/60"
                  >
                    <span class="relative z-10 flex items-center gap-2">
                      <i class="fas fa-edit"></i>
                      MODIFY REJECTED REQUEST
                    </span>
                  </div>
                </div>
                <h2
                  class="text-xl font-bold text-blue-100 tracking-wide drop-shadow-md animate-fade-in-delay"
                >
                  Request ID: #{{ requestId }}
                </h2>
              </div>

              <!-- Right Logo -->
              <div
                class="w-24 h-24 ml-6 transform hover:scale-110 transition-transform duration-300"
              >
                <div
                  class="w-full h-full bg-gradient-to-br from-teal-500/20 to-blue-500/20 rounded-2xl backdrop-blur-sm border-2 border-teal-300/40 flex items-center justify-center shadow-2xl hover:shadow-teal-500/25"
                >
                  <img
                    src="/assets/images/logo2.png"
                    alt="Muhimbili Logo"
                    class="max-w-16 max-h-16 object-contain"
                  />
                </div>
              </div>
            </div>
          </div>

          <!-- Loading Screen -->
          <div
            v-if="loading"
            class="booking-glass-card rounded-b-3xl overflow-hidden animate-slide-up"
          >
            <div class="p-8 text-center">
              <div class="flex flex-col items-center space-y-4">
                <div
                  class="w-16 h-16 bg-gradient-to-br from-orange-500 to-orange-600 rounded-full flex items-center justify-center shadow-lg animate-bounce-gentle"
                >
                  <i class="fas fa-spinner fa-spin text-white text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-white">Loading Request...</h3>
                <p class="text-blue-200 text-sm">Please wait while we load your request data</p>
              </div>
            </div>
          </div>

          <!-- Main Edit Form -->
          <div
            v-else-if="originalRequest"
            class="booking-glass-card rounded-b-3xl overflow-hidden animate-slide-up"
          >
            <!-- Rejection Reason Display -->
            <div
              v-if="originalRequest.ict_notes"
              class="bg-gradient-to-r from-red-600/25 to-red-700/25 border-2 border-red-400/40 p-4 m-6 mb-0 rounded-2xl"
            >
              <div class="flex items-start space-x-3">
                <div
                  class="w-10 h-10 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center shadow-lg flex-shrink-0"
                >
                  <i class="fas fa-exclamation-triangle text-white text-sm"></i>
                </div>
                <div>
                  <h4 class="text-red-100 font-bold text-sm mb-1">Rejection Reason:</h4>
                  <p class="text-red-200 text-sm">{{ originalRequest.ict_notes }}</p>
                </div>
              </div>
            </div>

            <form @submit.prevent="updateBooking" class="p-6 space-y-6">
              <!-- Booking Information Section -->
              <div
                class="booking-card bg-gradient-to-r from-orange-600/25 to-orange-700/25 border-2 border-orange-400/40 p-6 rounded-2xl backdrop-blur-sm hover:shadow-2xl hover:shadow-orange-500/20 transition-all duration-500 group"
              >
                <div class="flex items-center space-x-4 mb-4">
                  <div
                    class="w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border border-orange-300/50"
                  >
                    <i class="fas fa-calendar-alt text-white text-lg"></i>
                  </div>
                  <h3 class="text-lg font-bold text-white flex items-center">
                    <i class="fas fa-info-circle mr-2 text-orange-300"></i>
                    Booking Information
                  </h3>
                </div>

                <div class="space-y-4">
                  <!-- Row 1: Name of Borrower & Booking Date -->
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <!-- Name of Borrower -->
                    <div class="group">
                      <label class="block text-sm font-bold text-blue-100 mb-2 flex items-center">
                        <i class="fas fa-user mr-2 text-orange-300"></i>
                        Name of Borrower
                        <span class="text-red-400 ml-1">*</span>
                      </label>
                      <div class="relative">
                        <input
                          v-model="formData.borrower_name"
                          type="text"
                          class="booking-input w-full px-3 py-3 bg-white/15 border-2 border-orange-300/30 rounded-xl focus:border-orange-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-orange-500/20 group-hover:border-orange-400/50"
                          placeholder="Enter borrower name"
                          required
                        />
                      </div>
                      <div
                        v-if="errors.borrower_name"
                        class="text-red-400 text-xs mt-1 flex items-center"
                      >
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        {{ errors.borrower_name }}
                      </div>
                    </div>

                    <!-- Booking Date -->
                    <div class="group">
                      <label class="block text-sm font-bold text-blue-100 mb-2 flex items-center">
                        <i class="fas fa-calendar mr-2 text-orange-300"></i>
                        Booking Date <span class="text-red-400 ml-1">*</span>
                      </label>
                      <div class="relative">
                        <input
                          v-model="formData.booking_date"
                          type="date"
                          :min="minDate"
                          class="booking-input w-full px-3 py-3 bg-white/15 border-2 border-orange-300/30 rounded-xl focus:border-orange-400 focus:outline-none text-white backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-orange-500/20 group-hover:border-orange-400/50"
                          required
                        />
                      </div>
                      <div
                        v-if="errors.booking_date"
                        class="text-red-400 text-xs mt-1 flex items-center"
                      >
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        {{ errors.booking_date }}
                      </div>
                    </div>
                  </div>

                  <!-- Row 2: Device Type & Custom Device -->
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <!-- Device Type -->
                    <div class="group">
                      <label class="block text-sm font-bold text-blue-100 mb-2 flex items-center">
                        <i class="fas fa-laptop mr-2 text-orange-300"></i>
                        Device Type <span class="text-red-400 ml-1">*</span>
                      </label>
                      <div class="relative">
                        <select
                          v-model="formData.device_type"
                          @change="onDeviceTypeChange"
                          class="booking-input w-full px-3 py-3 bg-white/15 border-2 border-orange-300/30 rounded-xl focus:border-orange-400 focus:outline-none text-white backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-orange-500/20 group-hover:border-orange-400/50"
                          required
                        >
                          <option value="" disabled>Select a device type</option>
                          <option
                            v-for="deviceType in deviceTypes"
                            :key="deviceType.value"
                            :value="deviceType.value"
                            class="bg-gray-800 text-white"
                          >
                            {{ deviceType.label }}
                          </option>
                        </select>
                      </div>
                    </div>

                    <!-- Custom Device (conditional) -->
                    <div v-if="formData.device_type === 'other'" class="group">
                      <label class="block text-sm font-bold text-blue-100 mb-2 flex items-center">
                        <i class="fas fa-edit mr-2 text-orange-300"></i>
                        Custom Device Name <span class="text-red-400 ml-1">*</span>
                      </label>
                      <div class="relative">
                        <input
                          v-model="formData.custom_device"
                          type="text"
                          class="booking-input w-full px-3 py-3 bg-white/15 border-2 border-orange-300/30 rounded-xl focus:border-orange-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-orange-500/20 group-hover:border-orange-400/50"
                          placeholder="Specify device name"
                          :required="formData.device_type === 'other'"
                        />
                      </div>
                    </div>
                  </div>

                  <!-- Row 3: Department & Return Date -->
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <!-- Department -->
                    <div class="group">
                      <label class="block text-sm font-bold text-blue-100 mb-2 flex items-center">
                        <i class="fas fa-building mr-2 text-orange-300"></i>
                        Department <span class="text-red-400 ml-1">*</span>
                      </label>
                      <div class="relative">
                        <select
                          v-model="formData.department"
                          class="booking-input w-full px-3 py-3 bg-white/15 border-2 border-orange-300/30 rounded-xl focus:border-orange-400 focus:outline-none text-white backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-orange-500/20 group-hover:border-orange-400/50"
                          required
                        >
                          <option value="" disabled>Select department</option>
                          <option
                            v-for="dept in departments"
                            :key="dept.value"
                            :value="dept.name"
                            class="bg-gray-800 text-white"
                          >
                            {{ dept.label }}
                          </option>
                        </select>
                      </div>
                    </div>

                    <!-- Return Date -->
                    <div class="group">
                      <label class="block text-sm font-bold text-blue-100 mb-2 flex items-center">
                        <i class="fas fa-calendar-check mr-2 text-orange-300"></i>
                        Return Date <span class="text-red-400 ml-1">*</span>
                      </label>
                      <div class="relative">
                        <input
                          v-model="formData.return_date"
                          type="date"
                          :min="minReturnDate"
                          class="booking-input w-full px-3 py-3 bg-white/15 border-2 border-orange-300/30 rounded-xl focus:border-orange-400 focus:outline-none text-white backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-orange-500/20 group-hover:border-orange-400/50"
                          required
                        />
                      </div>
                    </div>
                  </div>

                  <!-- Row 4: Return Time & Reason -->
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <!-- Return Time -->
                    <div class="group">
                      <label class="block text-sm font-bold text-blue-100 mb-2 flex items-center">
                        <i class="fas fa-clock mr-2 text-orange-300"></i>
                        Return Time <span class="text-red-400 ml-1">*</span>
                      </label>
                      <div class="relative">
                        <input
                          v-model="formData.return_time"
                          type="time"
                          class="booking-input w-full px-3 py-3 bg-white/15 border-2 border-orange-300/30 rounded-xl focus:border-orange-400 focus:outline-none text-white backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-orange-500/20 group-hover:border-orange-400/50"
                          required
                        />
                      </div>
                    </div>

                    <!-- Empty space to maintain grid -->
                    <div></div>
                  </div>

                  <!-- Reason for Booking -->
                  <div class="group">
                    <label class="block text-sm font-bold text-blue-100 mb-2 flex items-center">
                      <i class="fas fa-comment-alt mr-2 text-orange-300"></i>
                      Reason for Booking <span class="text-red-400 ml-1">*</span>
                    </label>
                    <div class="relative">
                      <textarea
                        v-model="formData.reason"
                        class="booking-input w-full px-3 py-3 bg-white/15 border-2 border-orange-300/30 rounded-xl focus:border-orange-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 focus:shadow-lg focus:shadow-orange-500/20 group-hover:border-orange-400/50 resize-vertical"
                        rows="4"
                        placeholder="Please provide a detailed reason for your booking request"
                        maxlength="1000"
                        required
                      ></textarea>
                    </div>
                    <div class="flex justify-between text-xs text-blue-200/60 mt-1">
                      <span>Provide a clear and detailed explanation</span>
                      <span>{{ formData.reason ? formData.reason.length : 0 }}/1000</span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="flex justify-between items-center pt-4">
                <button
                  type="button"
                  @click="goBack"
                  class="px-6 py-3 bg-gradient-to-r from-gray-600 to-gray-700 text-white rounded-xl font-medium hover:from-gray-700 hover:to-gray-800 transition-all duration-300 flex items-center space-x-2 border border-gray-500/50 hover:border-gray-400/50"
                >
                  <i class="fas fa-arrow-left"></i>
                  <span>Cancel</span>
                </button>

                <button
                  type="submit"
                  :disabled="submitting"
                  class="px-8 py-3 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-xl font-medium hover:from-orange-600 hover:to-orange-700 transition-all duration-300 flex items-center space-x-2 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg hover:shadow-orange-500/25"
                >
                  <i class="fas fa-save" :class="{ 'fa-spin': submitting }"></i>
                  <span>{{ submitting ? 'Updating...' : 'Update & Resubmit Request' }}</span>
                </button>
              </div>
            </form>
          </div>

          <!-- Error State -->
          <div
            v-else-if="!loading"
            class="booking-glass-card rounded-b-3xl overflow-hidden animate-slide-up"
          >
            <div class="p-8 text-center">
              <div class="flex flex-col items-center space-y-4">
                <div
                  class="w-16 h-16 bg-gradient-to-br from-red-500 to-red-600 rounded-full flex items-center justify-center shadow-lg"
                >
                  <i class="fas fa-exclamation-triangle text-white text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-white">Request Not Found</h3>
                <p class="text-red-200 text-sm">
                  The requested booking could not be found or you don't have permission to edit it.
                </p>
                <button
                  @click="goBack"
                  class="px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl font-medium hover:from-blue-600 hover:to-blue-700 transition-all duration-300"
                >
                  <i class="fas fa-arrow-left mr-2"></i>
                  Go Back
                </button>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import ModernSidebar from '@/components/ModernSidebar.vue'
import AppHeader from '@/components/AppHeader.vue'
import { useAuth } from '@/composables/useAuth'
import bookingService from '@/services/bookingService'

export default {
  name: 'EditBookingRequest',
  components: {
    ModernSidebar,
    AppHeader
  },
  setup() {
    const router = useRouter()
    const route = useRoute()
    const { requireRole, ROLES } = useAuth()

    // Reactive state
    const loading = ref(true)
    const submitting = ref(false)
    const requestId = ref(route.query.id || '')
    const originalRequest = ref(null)
    const errors = ref({})
    const deviceTypes = ref([])
    const departments = ref([])

    // Form data
    const formData = ref({
      borrower_name: '',
      department: '',
      booking_date: '',
      return_date: '',
      return_time: '',
      device_type: '',
      custom_device: '',
      reason: ''
    })

    // Computed properties
    const minDate = computed(() => {
      const today = new Date()
      return today.toISOString().split('T')[0]
    })

    const minReturnDate = computed(() => {
      if (formData.value.booking_date) {
        const bookingDate = new Date(formData.value.booking_date)
        bookingDate.setDate(bookingDate.getDate() + 1)
        return bookingDate.toISOString().split('T')[0]
      }
      return minDate.value
    })

    // Guard this route - only staff can access
    onMounted(() => {
      requireRole([ROLES.STAFF])
      loadBookingRequest()
      loadFormOptions()
    })

    // Methods
    const loadBookingRequest = async () => {
      if (!requestId.value) {
        loading.value = false
        return
      }

      try {
        const response = await fetch(`/api/booking-service/bookings/${requestId.value}`, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('auth_token')}`,
            'Content-Type': 'application/json',
            Accept: 'application/json'
          }
        })

        if (response.ok) {
          const result = await response.json()
          if (result.success) {
            originalRequest.value = result.data
            
            // Check if this request can be edited
            if (result.data.ict_approve !== 'rejected') {
              alert('Only rejected booking requests can be edited.')
              goBack()
              return
            }

            // Populate form with existing data
            formData.value = {
              borrower_name: result.data.borrower_name || '',
              department: result.data.department || '',
              booking_date: result.data.booking_date || '',
              return_date: result.data.return_date || '',
              return_time: result.data.return_time || '',
              device_type: result.data.device_type || '',
              custom_device: result.data.custom_device || '',
              reason: result.data.reason || ''
            }
          } else {
            throw new Error(result.message || 'Failed to load booking request')
          }
        } else {
          const errorData = await response.json()
          throw new Error(errorData.message || 'Failed to load booking request')
        }
      } catch (error) {
        console.error('Error loading booking request:', error)
        alert(`Error loading request: ${error.message}`)
      } finally {
        loading.value = false
      }
    }

    const loadFormOptions = async () => {
      try {
        // Load device types
        const deviceTypesResponse = await fetch('/api/booking-service/device-types', {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('auth_token')}`,
            'Content-Type': 'application/json',
            Accept: 'application/json'
          }
        })

        if (deviceTypesResponse.ok) {
          const result = await deviceTypesResponse.json()
          if (result.success) {
            deviceTypes.value = result.data
          }
        }

        // Load departments
        const departmentsResponse = await fetch('/api/booking-service/departments', {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('auth_token')}`,
            'Content-Type': 'application/json',
            Accept: 'application/json'
          }
        })

        if (departmentsResponse.ok) {
          const result = await departmentsResponse.json()
          if (result.success) {
            departments.value = result.data
          }
        }
      } catch (error) {
        console.error('Error loading form options:', error)
      }
    }

    const onDeviceTypeChange = () => {
      if (formData.value.device_type !== 'other') {
        formData.value.custom_device = ''
      }
    }

    const updateBooking = async () => {
      if (submitting.value) return

      errors.value = {}
      submitting.value = true

      try {
        // Prepare form data
        const requestData = {
          ...formData.value
        }

        // If device type is not 'other', clear custom device
        if (requestData.device_type !== 'other') {
          requestData.custom_device = ''
        }

        const response = await fetch(`/api/booking-service/bookings/${requestId.value}/edit-rejected`, {
          method: 'PUT',
          headers: {
            Authorization: `Bearer ${localStorage.getItem('auth_token')}`,
            'Content-Type': 'application/json',
            Accept: 'application/json'
          },
          body: JSON.stringify(requestData)
        })

        const result = await response.json()

        if (response.ok && result.success) {
          // Show success message
          alert('Booking request updated and resubmitted successfully!')
          
          // Redirect to request status page
          router.push({
            path: '/request-status',
            query: {
              success: 'true',
              type: 'booking_service',
              id: requestId.value
            }
          })
        } else {
          if (result.errors) {
            errors.value = result.errors
          }
          throw new Error(result.message || 'Failed to update booking request')
        }
      } catch (error) {
        console.error('Error updating booking request:', error)
        alert(`Error updating request: ${error.message}`)
      } finally {
        submitting.value = false
      }
    }

    const goBack = () => {
      router.go(-1)
    }

    return {
      loading,
      submitting,
      requestId,
      originalRequest,
      formData,
      errors,
      deviceTypes,
      departments,
      minDate,
      minReturnDate,
      onDeviceTypeChange,
      updateBooking,
      goBack
    }
  }
}
</script>

<style scoped>
/* Custom styles for the edit form */
.booking-glass-card {
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
}

.booking-card {
  transition: all 0.3s ease;
}

.booking-input {
  transition: all 0.3s ease;
}

.booking-input:focus {
  transform: translateY(-1px);
}

.animate-fade-in {
  animation: fadeIn 0.6s ease-out;
}

.animate-fade-in-delay {
  animation: fadeIn 0.8s ease-out;
}

.animate-slide-up {
  animation: slideUp 0.5s ease-out;
}

.animate-bounce-gentle {
  animation: bounceGentle 2s infinite;
}

.animate-float {
  animation: float 3s ease-in-out infinite;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes slideUp {
  from { transform: translateY(30px); opacity: 0; }
  to { transform: translateY(0); opacity: 1; }
}

@keyframes bounceGentle {
  0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
  40% { transform: translateY(-10px); }
  60% { transform: translateY(-5px); }
}

@keyframes float {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-20px); }
}
</style>
