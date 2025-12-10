<template>
  <div class="flex flex-col h-screen">
    <Header />
    <div class="flex flex-1 overflow-hidden">
      <ModernSidebar />
      <main
        class="flex-1 p-6 bg-gradient-to-br from-blue-900 via-blue-800 to-teal-900 overflow-y-auto relative"
      >
        <!-- Background pattern (match other HOD pages) -->
        <div class="absolute inset-0 overflow-hidden">
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
        </div>

        <div class="max-w-9xl mx-auto relative z-10">
          <!-- Card container styled similar to Admin Create New User modal -->
          <div class="bg-blue-900 rounded-2xl shadow-2xl overflow-hidden border border-blue-500/40">
            <!-- Header -->
            <div
              class="bg-blue-800 p-6 rounded-t-2xl border-b border-blue-500/30 flex items-center justify-between"
            >
              <div class="flex items-center space-x-3">
                <div
                  class="w-10 h-10 bg-gradient-to-br from-blue-500 to-teal-500 rounded-xl flex items-center justify-center shadow-lg border border-blue-300/60"
                >
                  <i class="fas fa-user-plus text-white"></i>
                </div>
                <div>
                  <h1 class="text-xl md:text-2xl font-bold text-white">Create New Staff User</h1>
                  <p class="text-sm text-blue-100">
                    Heads of Department can register new staff for their department. New users are
                    automatically given the <span class="font-semibold">Staff</span> role.
                  </p>
                </div>
              </div>
              <button
                type="button"
                @click="goBack"
                class="hidden md:inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 text-sm font-medium shadow-lg hover:shadow-xl transition-all duration-200"
              >
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Dashboard
              </button>
            </div>

            <!-- Notification -->
            <div v-if="message" class="px-6 pt-4 bg-blue-900">
              <div
                :class="[
                  'px-4 py-3 rounded-lg text-sm flex items-center space-x-2',
                  messageType === 'success'
                    ? 'bg-green-600/20 text-green-100 border border-green-400/60'
                    : 'bg-red-600/20 text-red-100 border border-red-400/60'
                ]"
              >
                <i
                  :class="
                    messageType === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle'
                  "
                ></i>
                <span>{{ message }}</span>
              </div>
            </div>

            <!-- Form -->
            <form
              @submit.prevent="submit"
              class="px-6 pb-6 pt-4 space-y-6 bg-blue-900"
              autocomplete="off"
            >
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Full Name -->
                <div>
                  <label class="block text-sm font-semibold text-blue-100 mb-2"> Full Name </label>
                  <input
                    v-model="form.name"
                    type="text"
                    class="w-full px-4 py-3 rounded-lg bg-white/10 border text-white placeholder-blue-200/60 border-blue-300/40 focus:outline-none focus:ring-2 focus:ring-blue-400"
                    placeholder="Enter staff full name"
                  />
                  <p v-if="errors.name" class="mt-1 text-xs text-red-300 flex items-center">
                    <i class="fas fa-exclamation-circle mr-1"></i>
                    {{ errors.name }}
                  </p>
                </div>

                <!-- PF Number -->
                <div>
                  <label class="block text-sm font-semibold text-blue-100 mb-2"> PF Number </label>
                  <input
                    v-model="form.pf_number"
                    type="text"
                    class="w-full px-4 py-3 rounded-lg bg-white/10 border text-white placeholder-blue-200/60 border-blue-300/40 focus:outline-none focus:ring-2 focus:ring-blue-400"
                    placeholder="e.g. PF12345"
                  />
                  <p v-if="errors.pf_number" class="mt-1 text-xs text-red-300 flex items-center">
                    <i class="fas fa-exclamation-circle mr-1"></i>
                    {{ errors.pf_number }}
                  </p>
                </div>

                <!-- Email -->
                <div>
                  <label class="block text-sm font-semibold text-blue-100 mb-2">
                    Email Address
                  </label>
                  <input
                    v-model="form.email"
                    type="email"
                    name="email"
                    autocomplete="new-email"
                    @input="validateField('email')"
                    class="w-full px-4 py-3 rounded-lg bg-white/10 border text-white placeholder-blue-200/60 border-blue-300/40 focus:outline-none focus:ring-2 focus:ring-blue-400"
                    placeholder="staff@example.com"
                  />
                  <p v-if="errors.email" class="mt-1 text-xs text-red-300 flex items-center">
                    <i class="fas fa-exclamation-circle mr-1"></i>
                    {{ errors.email }}
                  </p>
                </div>

                <!-- Phone -->
                <div>
                  <label class="block text-sm font-semibold text-blue-100 mb-2">
                    Phone Number
                  </label>
                  <input
                    v-model="form.phone"
                    type="tel"
                    class="w-full px-4 py-3 rounded-lg bg-white/10 border text-white placeholder-blue-200/60 border-blue-300/40 focus:outline-none focus:ring-2 focus:ring-blue-400"
                    placeholder="e.g. 0712345678 or +255712345678"
                  />
                  <p v-if="errors.phone" class="mt-1 text-xs text-red-300 flex items-center">
                    <i class="fas fa-exclamation-circle mr-1"></i>
                    {{ errors.phone }}
                  </p>
                </div>

                <!-- Department & Role split into two columns -->
                <div class="md:col-span-2">
                  <div class="flex flex-col md:flex-row md:items-stretch md:space-x-0">
                    <!-- Department (left) -->
                    <div class="flex-1 md:pr-4 md:border-r md:border-blue-500/40 mb-4 md:mb-0">
                      <label class="block text-sm font-semibold text-blue-100 mb-2">
                        Department
                      </label>
                      <div
                        class="w-full px-4 py-3 rounded-lg bg-blue-900/40 border border-blue-400/60 text-blue-100 flex items-center justify-between"
                      >
                        <span>{{ departmentLabel }}</span>
                        <span
                          class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-500/30 border border-blue-300/60"
                        >
                          Assigned automatically
                        </span>
                      </div>
                      <p class="mt-1 text-xs text-blue-200/80">
                        New users are linked to one of your departments. Only Heads of Department
                        can perform this action.
                      </p>
                      <p
                        v-if="errors.department_id"
                        class="mt-1 text-xs text-red-300 flex items-center"
                      >
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        {{ errors.department_id }}
                      </p>
                    </div>

                    <!-- Role (right) -->
                    <div class="flex-1 md:pl-4">
                      <label class="block text-sm font-semibold text-blue-100 mb-2"> Role </label>
                      <div
                        class="w-full px-4 py-3 rounded-lg bg-blue-900/40 border border-blue-400/60 text-blue-100 flex items-center justify-between"
                      >
                        <span class="flex items-center space-x-2">
                          <i class="fas fa-id-badge text-blue-200"></i>
                          <span class="font-semibold">Staff (default)</span>
                        </span>
                        <span
                          class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-500/30 border border-blue-300/60"
                        >
                          Fixed role
                        </span>
                      </div>
                      <p class="mt-1 text-xs text-blue-200/80">
                        Role is fixed to <strong>Staff</strong> for users created by Heads of
                        Department. Admins can later adjust roles if needed.
                      </p>
                    </div>
                  </div>
                </div>

                <!-- Password & Confirm Password side by side -->
                <div class="md:col-span-2">
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Password -->
                    <div>
                      <label class="block text-sm font-semibold text-blue-100 mb-2">
                        Password
                      </label>
                      <input
                        v-model="form.password"
                        type="password"
                        name="password"
                        autocomplete="new-password"
                        @input="validateField('password')"
                        class="w-full px-4 py-3 rounded-lg bg-blue-900/40 border border-blue-400/60 text-blue-100 placeholder-blue-200/70 focus:outline-none focus:ring-2 focus:ring-blue-400"
                        placeholder="Minimum 8 characters"
                      />
                      <p v-if="errors.password" class="mt-1 text-xs text-red-300 flex items-center">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        {{ errors.password }}
                      </p>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                      <label class="block text-sm font-semibold text-blue-100 mb-2">
                        Confirm Password
                      </label>
                      <input
                        v-model="form.password_confirmation"
                        type="password"
                        name="password_confirmation"
                        autocomplete="new-password"
                        @input="validateField('password_confirmation')"
                        class="w-full px-4 py-3 rounded-lg bg-blue-900/40 border border-blue-400/60 text-blue-100 placeholder-blue-200/70 focus:outline-none focus:ring-2 focus:ring-blue-400"
                        placeholder="Re-enter password"
                      />
                      <p
                        v-if="errors.password_confirmation"
                        class="mt-1 text-xs text-red-300 flex items-center"
                      >
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        {{ errors.password_confirmation }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Actions -->
              <div class="flex flex-col md:flex-row md:items-center md:justify-between pt-2">
                <button
                  type="button"
                  @click="goBack"
                  class="mb-3 md:mb-0 inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 text-sm font-medium shadow-md hover:shadow-lg transition-all duration-200"
                >
                  <i class="fas fa-arrow-left mr-2"></i>
                  Back
                </button>

                <button
                  type="submit"
                  :disabled="submitting"
                  class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 disabled:opacity-60 disabled:cursor-not-allowed border border-blue-300/60 text-sm md:text-base transition-all duration-200"
                >
                  <i v-if="submitting" class="fas fa-spinner fa-spin mr-2"></i>
                  <i v-else class="fas fa-save mr-2"></i>
                  {{ submitting ? 'Creating User...' : 'Create User' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script>
  import { ref, computed, onMounted } from 'vue'
  import { useRouter } from 'vue-router'
  import Header from '@/components/header.vue'
  import ModernSidebar from '@/components/ModernSidebar.vue'
  import { useAuth } from '@/composables/useAuth'

  export default {
    name: 'HodCreateUser',
    components: {
      Header,
      ModernSidebar
    },
    setup() {
      const router = useRouter()
      const { user, ROLES, requireRole } = useAuth()

      const form = ref({
        name: '',
        email: '',
        phone: '',
        pf_number: '',
        password: '',
        password_confirmation: ''
      })

      const errors = ref({})
      const message = ref('')
      const messageType = ref('success')
      const submitting = ref(false)

      const departmentId = computed(() => {
        const u = user.value
        const dept = u?.department
        if (dept && dept.id) return dept.id
        return u?.department_id || null
      })

      const departmentLabel = computed(() => {
        const u = user.value
        const dept = u?.department
        if (dept) {
          return (
            dept.display_name ||
            dept.full_name ||
            dept.name ||
            (dept.code ? `${dept.name} (${dept.code})` : dept.code)
          )
        }
        return (
          u?.department_display_name ||
          u?.department_full_name ||
          u?.department_name ||
          u?.department_code ||
          'Not specified'
        )
      })

      const showMessage = (text, type = 'success') => {
        message.value = text
        messageType.value = type
      }

      const normalizePhoneNumber = (input) => {
        if (!input) return ''
        let v = String(input).trim().replace(/\s|-/g, '')
        if (v.startsWith('+255')) return v
        if (v.startsWith('255')) return '+' + v
        if (v.startsWith('0')) return '+255' + v.slice(1)
        return v
      }

      const validateField = (field) => {
        // Clear error for this field initially
        if (errors.value[field]) {
          const newErrors = { ...errors.value }
          delete newErrors[field]
          errors.value = newErrors
        }

        if (field === 'email') {
          if (!form.value.email.trim()) {
            errors.value = { ...errors.value, email: 'Email is required.' }
          } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.value.email.trim())) {
            errors.value = { ...errors.value, email: 'Enter a valid email address.' }
          }
        }

        if (field === 'password') {
          if (!form.value.password) {
            errors.value = { ...errors.value, password: 'Password is required.' }
          } else if (form.value.password.length < 8) {
            errors.value = { ...errors.value, password: 'Password must be at least 8 characters.' }
          }
          // Re-validate confirmation if it has a value
          if (form.value.password_confirmation) {
            validateField('password_confirmation')
          }
        }

        if (field === 'password_confirmation') {
          if (!form.value.password_confirmation) {
            errors.value = {
              ...errors.value,
              password_confirmation: 'Please confirm the password.'
            }
          } else if (form.value.password !== form.value.password_confirmation) {
            errors.value = { ...errors.value, password_confirmation: 'Passwords do not match.' }
          }
        }
      }

      const validate = () => {
        const e = {}
        if (!form.value.name.trim()) e.name = 'Full name is required.'
        if (!form.value.pf_number.trim()) e.pf_number = 'PF number is required.'

        if (!form.value.email.trim()) {
          e.email = 'Email is required.'
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.value.email.trim())) {
          e.email = 'Enter a valid email address.'
        }

        form.value.phone = normalizePhoneNumber(form.value.phone)
        if (!form.value.phone.trim()) {
          e.phone = 'Phone number is required.'
        } else if (!/^\+255\d{9}$/.test(form.value.phone)) {
          e.phone = 'Phone must be in the format +255XXXXXXXXX.'
        }

        if (!form.value.password) {
          e.password = 'Password is required.'
        } else if (form.value.password.length < 8) {
          e.password = 'Password must be at least 8 characters.'
        }

        if (!form.value.password_confirmation) {
          e.password_confirmation = 'Please confirm the password.'
        } else if (form.value.password !== form.value.password_confirmation) {
          e.password_confirmation = 'Passwords do not match.'
        }

        if (!departmentId.value) {
          e.department_id = 'Your department is not set. Please contact the system administrator.'
        }

        errors.value = e
        return Object.keys(e).length === 0
      }

      const submit = async () => {
        if (!validate()) {
          showMessage('Please correct the highlighted errors and try again.', 'error')
          return
        }

        submitting.value = true
        showMessage('')

        try {
          const payload = {
            name: form.value.name.trim(),
            email: form.value.email.trim().toLowerCase(),
            phone: normalizePhoneNumber(form.value.phone.trim()),
            pf_number: form.value.pf_number.trim(),
            password: form.value.password,
            password_confirmation: form.value.password_confirmation,
            department_id: departmentId.value
          }

          const { default: hodUserService } = await import('@/services/hodUserService')
          const result = await hodUserService.createUser(payload)

          if (result.success) {
            showMessage('Staff user created successfully.', 'success')
            form.value = {
              name: '',
              email: '',
              phone: '',
              pf_number: '',
              password: '',
              password_confirmation: ''
            }
            errors.value = {}
          } else {
            const backendErrors = result.errors || {}
            if (Object.keys(backendErrors).length) {
              const flatErrors = {}
              Object.entries(backendErrors).forEach(([key, val]) => {
                flatErrors[key] = Array.isArray(val) ? val[0] : val
              })
              errors.value = { ...errors.value, ...flatErrors }
              const firstKey = Object.keys(flatErrors)[0]
              if (firstKey) {
                showMessage(flatErrors[firstKey], 'error')
              } else {
                showMessage(result.message || 'Failed to create user.', 'error')
              }
            } else {
              showMessage(result.message || 'Failed to create user.', 'error')
            }
          }
        } catch (error) {
          if (error.response?.status === 422 && error.response.data?.errors) {
            const backendErrors = error.response.data.errors
            const flatErrors = {}
            Object.entries(backendErrors).forEach(([key, val]) => {
              flatErrors[key] = Array.isArray(val) ? val[0] : val
            })
            errors.value = flatErrors
            const firstKey = Object.keys(flatErrors)[0]
            showMessage(flatErrors[firstKey] || 'Validation failed.', 'error')
          } else {
            console.error('Error creating HOD staff user:', error)
            showMessage('An unexpected error occurred while creating the user.', 'error')
          }
        } finally {
          submitting.value = false
        }
      }

      const goBack = () => {
        router.push('/hod-dashboard')
      }

      onMounted(() => {
        // Ensure only HOD or Head of IT can access this page
        requireRole([ROLES.HEAD_OF_DEPARTMENT, ROLES.HEAD_OF_IT])
      })

      return {
        form,
        errors,
        message,
        messageType,
        submitting,
        departmentLabel,
        submit,
        goBack,
        validateField
      }
    }
  }
</script>

<style scoped></style>
