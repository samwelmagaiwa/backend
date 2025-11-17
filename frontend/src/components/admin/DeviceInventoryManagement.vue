<template>
  <div class="flex flex-col h-screen">
    <Header />
    <div class="flex flex-1 overflow-hidden">
      <ModernSidebar />
      <main
        class="flex-1 p-6 bg-gradient-to-br from-blue-900 via-blue-800 to-teal-900 overflow-y-auto relative"
      >
        <UnifiedLoadingBanner
          :show="isLoading"
          loadingTitle="Loading Device Inventory"
          loadingSubtitle="Synchronizing device list and inventory statistics..."
          departmentTitle="DEVICE INVENTORY MANAGEMENT"
          :forceSpin="true"
        />
        <!-- Background Pattern -->
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
                  ['fa-laptop', 'fa-tv', 'fa-desktop', 'fa-keyboard', 'fa-mouse'][
                    Math.floor(Math.random() * 5)
                  ]
                ]"
              ></i>
            </div>
          </div>
        </div>

        <div class="max-w-full mx-auto relative z-10">
          <!-- Header Section -->
          <div class="medical-glass-card rounded-t-3xl p-4 mb-0 border-b border-blue-300/30">
            <div class="flex justify-between items-center">
              <!-- Left Logo -->
              <div
                class="w-20 h-20 mr-6 transform hover:scale-110 transition-transform duration-300"
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
                  MUHIMBILI NATIONAL HOSPITAL
                </h1>
                <div class="relative inline-block mb-4">
                  <div
                    class="bg-gradient-to-r from-orange-600 to-red-700 text-white px-8 py-3 rounded-full text-lg font-bold shadow-2xl transform hover:scale-105 transition-all duration-300 border-2 border-orange-400/60"
                  >
                    <span class="relative z-10 flex items-center gap-2">
                      <i class="fas fa-laptop"></i>
                      BORROWED DEVICE MONITORING
                    </span>
                  </div>
                </div>
                <h2
                  class="text-xl font-bold text-blue-100 tracking-wide drop-shadow-md animate-fade-in-delay"
                >
                  ICT EQUIPMENT INVENTORY MANAGEMENT
                </h2>
              </div>

              <!-- Right Logo -->
              <div
                class="w-20 h-20 ml-6 transform hover:scale-110 transition-transform duration-300"
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

          <!-- Main Content -->
          <div class="medical-glass-card rounded-b-3xl overflow-hidden">
            <div class="p-6 space-y-6">
              <!-- Back Button -->
              <div class="flex justify-between items-center">
                <button
                  @click="goBack"
                  class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-gray-600 to-gray-700 text-white rounded-lg hover:from-gray-700 hover:to-gray-800 transition-all duration-300 font-semibold shadow-lg hover:shadow-xl transform hover:scale-105"
                >
                  <i class="fas fa-arrow-left mr-2"></i>
                  Back to Dashboard
                </button>

                <button
                  @click="showAddDeviceModal = true"
                  class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-orange-600 to-red-700 text-white rounded-lg hover:from-orange-700 hover:to-red-800 transition-all duration-300 font-semibold shadow-lg hover:shadow-xl transform hover:scale-105"
                >
                  <i class="fas fa-plus mr-2"></i>
                  Add New Device
                </button>
              </div>

              <!-- Statistics Cards -->
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div
                  class="medical-card bg-gradient-to-r from-blue-600/25 to-cyan-600/25 border-2 border-blue-400/40 p-4 rounded-xl backdrop-blur-sm"
                >
                  <div class="flex items-center">
                    <div
                      class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-lg flex items-center justify-center shadow-lg mr-4"
                    >
                      <i class="fas fa-laptop text-white text-xl"></i>
                    </div>
                    <div>
                      <p class="text-lg md:text-xl font-bold text-blue-100">Total Devices</p>
                      <p class="text-4xl md:text-5xl font-extrabold text-white">
                        {{ statistics.total_devices || 0 }}
                      </p>
                    </div>
                  </div>
                </div>

                <div
                  class="medical-card bg-gradient-to-r from-green-600/25 to-emerald-600/25 border-2 border-green-400/40 p-4 rounded-xl backdrop-blur-sm"
                >
                  <div class="flex items-center">
                    <div
                      class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-lg flex items-center justify-center shadow-lg mr-4"
                    >
                      <i class="fas fa-check-circle text-white text-xl"></i>
                    </div>
                    <div>
                      <p class="text-lg md:text-xl font-bold text-green-100">Available</p>
                      <p class="text-4xl md:text-5xl font-extrabold text-white">
                        {{ statistics.available_inventory || 0 }}
                      </p>
                    </div>
                  </div>
                </div>

                <div
                  class="medical-card bg-gradient-to-r from-yellow-600/25 to-orange-600/25 border-2 border-yellow-400/40 p-4 rounded-xl backdrop-blur-sm"
                >
                  <div class="flex items-center">
                    <div
                      class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-orange-600 rounded-lg flex items-center justify-center shadow-lg mr-4"
                    >
                      <i class="fas fa-hand-holding text-white text-xl"></i>
                    </div>
                    <div>
                      <p class="text-lg md:text-xl font-bold text-yellow-100">Borrowed</p>
                      <p class="text-4xl md:text-5xl font-extrabold text-white">
                        {{ statistics.borrowed_inventory || 0 }}
                      </p>
                    </div>
                  </div>
                </div>

                <div
                  class="medical-card bg-gradient-to-r from-red-600/25 to-pink-600/25 border-2 border-red-400/40 p-4 rounded-xl backdrop-blur-sm"
                >
                  <div class="flex items-center">
                    <div
                      class="w-12 h-12 bg-gradient-to-br from-red-500 to-pink-600 rounded-lg flex items-center justify-center shadow-lg mr-4"
                    >
                      <i class="fas fa-exclamation-triangle text-white text-xl"></i>
                    </div>
                    <div>
                      <p class="text-lg md:text-xl font-bold text-red-100">Out of Stock</p>
                      <p class="text-4xl md:text-5xl font-extrabold text-white">
                        {{ statistics.out_of_stock_devices || 0 }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Search and Filters -->
              <div
                class="medical-card bg-gradient-to-r from-blue-600/25 to-cyan-600/25 border-2 border-blue-400/40 p-4 rounded-xl backdrop-blur-sm"
              >
                <div class="flex flex-col md:flex-row gap-4 items-center">
                  <div class="flex-1">
                    <div class="relative">
                      <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Search devices..."
                        class="w-full px-4 py-2 pl-10 bg-white/15 border border-blue-300/30 rounded-lg focus:border-blue-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm text-lg md:text-xl"
                      />
                      <i
                        class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-blue-300/50"
                      ></i>
                    </div>
                  </div>
                  <div class="flex gap-2">
                    <select
                      v-model="statusFilter"
                      class="px-4 py-2 bg-white/15 border border-blue-300/30 rounded-lg focus:border-blue-400 focus:outline-none text-white backdrop-blur-sm text-lg md:text-xl"
                    >
                      <option value="">All Status</option>
                      <option value="available">Available</option>
                      <option value="low_stock">Low Stock</option>
                      <option value="out_of_stock">Out of Stock</option>
                      <option value="inactive">Inactive</option>
                    </select>
                    <button
                      @click="fixQuantities"
                      :disabled="isFixingQuantities"
                      class="px-4 py-2 bg-gradient-to-r from-yellow-600 to-orange-600 text-white rounded-lg hover:from-yellow-700 hover:to-orange-700 transition-all duration-300 disabled:opacity-50"
                      title="Fix quantity inconsistencies"
                    >
                      <i
                        :class="isFixingQuantities ? 'fas fa-spinner fa-spin' : 'fas fa-wrench'"
                      ></i>
                    </button>
                    <button
                      @click="loadDevices"
                      class="px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-300"
                    >
                      <i class="fas fa-sync-alt"></i>
                    </button>
                  </div>
                </div>
              </div>

              <!-- Devices Table -->
              <div
                class="medical-card bg-gradient-to-r from-blue-600/25 to-cyan-600/25 border-2 border-blue-400/40 rounded-xl backdrop-blur-sm overflow-hidden"
              >
                <div class="p-4 border-b border-blue-300/30">
                  <h3 class="text-3xl md:text-4xl font-extrabold text-white flex items-center">
                    <i class="fas fa-list mr-2 text-blue-300"></i>
                    Device Inventory
                  </h3>
                </div>

                <div class="overflow-x-auto">
                  <table class="w-full">
                    <thead class="bg-blue-600/30">
                      <tr>
                        <th
                          class="px-4 py-3 text-left text-base md:text-lg font-semibold text-blue-100 uppercase tracking-wider"
                        >
                          Device Name
                        </th>
                        <th
                          class="px-4 py-3 text-left text-base md:text-lg font-semibold text-blue-100 uppercase tracking-wider"
                        >
                          Code
                        </th>
                        <th
                          class="px-4 py-3 text-left text-base md:text-lg font-semibold text-blue-100 uppercase tracking-wider"
                        >
                          Total
                        </th>
                        <th
                          class="px-4 py-3 text-left text-base md:text-lg font-semibold text-blue-100 uppercase tracking-wider"
                        >
                          Available
                        </th>
                        <th
                          class="px-4 py-3 text-left text-base md:text-lg font-semibold text-blue-100 uppercase tracking-wider"
                        >
                          Borrowed
                        </th>
                        <th
                          class="px-4 py-3 text-left text-base md:text-lg font-semibold text-blue-100 uppercase tracking-wider"
                        >
                          Status
                        </th>
                        <th
                          class="px-4 py-3 text-left text-base md:text-lg font-semibold text-blue-100 uppercase tracking-wider"
                        >
                          Return Status
                        </th>
                        <th
                          class="px-4 py-3 text-left text-base md:text-lg font-semibold text-blue-100 uppercase tracking-wider"
                        >
                          Actions
                        </th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-blue-300/20">
                      <tr
                        v-for="device in filteredDevices"
                        :key="device.id"
                        class="hover:bg-white/5 transition-colors duration-200"
                      >
                        <td class="px-4 py-3">
                          <div>
                            <div class="text-xl md:text-2xl font-bold text-white">
                              {{ device.device_name }}
                            </div>
                            <div class="text-base text-blue-200" v-if="device.description">
                              {{ device.description }}
                            </div>
                          </div>
                        </td>
                        <td class="px-4 py-3">
                          <span class="text-lg text-blue-200 font-mono">{{
                            device.device_code
                          }}</span>
                        </td>
                        <td class="px-4 py-3">
                          <span class="text-xl font-bold text-white">{{
                            device.total_quantity
                          }}</span>
                        </td>
                        <td class="px-4 py-3">
                          <span class="text-xl font-bold text-green-300">{{
                            device.available_quantity
                          }}</span>
                        </td>
                        <td class="px-4 py-3">
                          <span class="text-xl font-bold text-yellow-300">{{
                            device.borrowed_quantity
                          }}</span>
                        </td>
                        <td class="px-4 py-3">
                          <span
                            :class="getStatusClass(device.availability_status)"
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium"
                          >
                            <i :class="getStatusIcon(device.availability_status)" class="mr-1"></i>
                            {{ getStatusText(device.availability_status) }}
                          </span>
                        </td>
                        <td class="px-4 py-3">
                          <span
                            :class="getReturnStatusClass(device.return_status_summary)"
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium"
                          >
                            <i
                              :class="getReturnStatusIcon(device.return_status_summary)"
                              class="mr-1"
                            ></i>
                            {{
                              getReturnStatusText(
                                device.return_status_summary,
                                device.unreturned_count,
                                device.compromised_count
                              )
                            }}
                          </span>
                        </td>
                        <td class="px-4 py-3">
                          <div class="flex space-x-2">
                            <button
                              @click="editDevice(device)"
                              class="text-blue-300 hover:text-blue-100 transition-colors duration-200"
                              title="Edit Device"
                            >
                              <i class="fas fa-edit"></i>
                            </button>
                            <button
                              @click="deleteDevice(device)"
                              class="text-red-300 hover:text-red-100 transition-colors duration-200"
                              title="Delete Device"
                            >
                              <i class="fas fa-trash"></i>
                            </button>
                          </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>

                  <!-- Empty State -->
                  <div v-if="filteredDevices.length === 0" class="text-center py-8">
                    <i class="fas fa-laptop text-blue-300/50 text-4xl mb-4"></i>
                    <p class="text-blue-200">No devices found</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <AppFooter />
      </main>
    </div>

    <!-- Add/Edit Device Modal -->
    <div
      v-if="showAddDeviceModal || showEditDeviceModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
    >
      <div class="bg-white rounded-xl shadow-2xl max-w-md w-full">
        <div class="p-6">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-800">
              {{ showEditDeviceModal ? 'Edit Device' : 'Add New Device' }}
            </h3>
            <button
              @click="closeModal"
              class="text-gray-400 hover:text-gray-600 transition-colors duration-200"
            >
              <i class="fas fa-times text-xl"></i>
            </button>
          </div>

          <form @submit.prevent="saveDevice" class="space-y-4">
            <div>
              <label class="block text-base font-medium text-gray-700 mb-2">Device Name *</label>
              <input
                v-model="deviceForm.device_name"
                type="text"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="Enter device name"
              />
            </div>

            <div>
              <label class="block text-base font-medium text-gray-700 mb-2">Description</label>
              <textarea
                v-model="deviceForm.description"
                rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="Enter device description"
              ></textarea>
            </div>

            <div>
              <label class="block text-base font-medium text-gray-700 mb-2">Total Quantity *</label>
              <input
                v-model.number="deviceForm.total_quantity"
                type="number"
                min="0"
                max="10000"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="Enter total quantity"
              />
            </div>

            <div v-if="showEditDeviceModal" class="flex items-center">
              <input
                v-model="deviceForm.is_active"
                type="checkbox"
                id="is_active"
                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
              />
              <label for="is_active" class="ml-2 block text-sm text-gray-700"> Active </label>
            </div>

            <div class="flex justify-end space-x-3 pt-4">
              <button
                type="button"
                @click="closeModal"
                class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors duration-200"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="isSubmitting"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 disabled:opacity-50"
              >
                {{ isSubmitting ? 'Saving...' : showEditDeviceModal ? 'Update' : 'Create' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import { ref, computed, onMounted } from 'vue'
  import { useRouter } from 'vue-router'
  import Header from '@/components/header.vue'
  import ModernSidebar from '@/components/ModernSidebar.vue'
  import AppFooter from '@/components/footer.vue'
  import UnifiedLoadingBanner from '@/components/common/UnifiedLoadingBanner.vue'
  import deviceInventoryService from '@/services/deviceInventoryService'

  export default {
    name: 'DeviceInventoryManagement',
    components: {
      Header,
      ModernSidebar,
      AppFooter,
      UnifiedLoadingBanner
    },
    setup() {
      const router = useRouter()

      // Reactive data
      const devices = ref([])
      const statistics = ref({})
      const isLoading = ref(false)
      const isSubmitting = ref(false)
      const isFixingQuantities = ref(false)
      const searchQuery = ref('')
      const statusFilter = ref('')
      const showAddDeviceModal = ref(false)
      const showEditDeviceModal = ref(false)
      const editingDevice = ref(null)

      // Device form
      const deviceForm = ref({
        device_name: '',
        description: '',
        total_quantity: 0,
        is_active: true
      })

      // Computed properties
      const filteredDevices = computed(() => {
        let filtered = devices.value

        if (searchQuery.value) {
          const query = searchQuery.value.toLowerCase()
          filtered = filtered.filter(
            (device) =>
              device.device_name.toLowerCase().includes(query) ||
              device.device_code.toLowerCase().includes(query) ||
              (device.description && device.description.toLowerCase().includes(query))
          )
        }

        if (statusFilter.value) {
          filtered = filtered.filter((device) => device.availability_status === statusFilter.value)
        }

        return filtered
      })

      // Methods
      const loadDevices = async () => {
        isLoading.value = true
        try {
          const response = await deviceInventoryService.getDevices()
          if (response.success) {
            devices.value = response.data.data.data || []
          } else {
            console.error('Failed to load devices:', response.message)
            alert('Failed to load devices: ' + response.message)
          }
        } catch (error) {
          console.error('Error loading devices:', error)
          alert('Error loading devices')
        } finally {
          isLoading.value = false
        }
      }

      const loadStatistics = async () => {
        try {
          const response = await deviceInventoryService.getStatistics()
          if (response.success) {
            statistics.value = response.data.data || {}
          }
        } catch (error) {
          console.error('Error loading statistics:', error)
        }
      }

      const saveDevice = async () => {
        isSubmitting.value = true
        try {
          let response
          if (showEditDeviceModal.value) {
            response = await deviceInventoryService.updateDevice(
              editingDevice.value.id,
              deviceForm.value
            )
          } else {
            response = await deviceInventoryService.createDevice(deviceForm.value)
          }

          if (response.success) {
            closeModal()
            await loadDevices()
            await loadStatistics()
            alert(
              showEditDeviceModal.value
                ? 'Device updated successfully!'
                : 'Device created successfully!'
            )
          } else {
            alert('Error: ' + response.message)
          }
        } catch (error) {
          console.error('Error saving device:', error)
          alert('Error saving device')
        } finally {
          isSubmitting.value = false
        }
      }

      const editDevice = (device) => {
        editingDevice.value = device
        deviceForm.value = {
          device_name: device.device_name,
          description: device.description || '',
          total_quantity: device.total_quantity,
          is_active: device.is_active
        }
        showEditDeviceModal.value = true
      }

      const deleteDevice = async (device) => {
        if (!confirm(`Are you sure you want to delete "${device.device_name}"?`)) {
          return
        }

        try {
          const response = await deviceInventoryService.deleteDevice(device.id)
          if (response.success) {
            await loadDevices()
            await loadStatistics()
            alert('Device deleted successfully!')
          } else {
            alert('Error: ' + response.message)
          }
        } catch (error) {
          console.error('Error deleting device:', error)
          alert('Error deleting device')
        }
      }

      const closeModal = () => {
        showAddDeviceModal.value = false
        showEditDeviceModal.value = false
        editingDevice.value = null
        deviceForm.value = {
          device_name: '',
          description: '',
          total_quantity: 0,
          is_active: true
        }
      }

      const getStatusClass = (status) => {
        return deviceInventoryService.getStatusColorClass(status)
      }

      const getStatusText = (status) => {
        return deviceInventoryService.getStatusDisplayText(status)
      }

      const getStatusIcon = (status) => {
        return deviceInventoryService.getStatusIcon(status)
      }

      // Return status helpers (aggregated per device)
      const getReturnStatusClass = (status) => {
        return deviceInventoryService.getReturnStatusColorClass(status)
      }
      const getReturnStatusIcon = (status) => {
        return deviceInventoryService.getReturnStatusIcon(status)
      }
      const getReturnStatusText = (status, unreturnedCount = 0, compromisedCount = 0) => {
        return deviceInventoryService.getReturnStatusDisplayText(
          status,
          unreturnedCount,
          compromisedCount
        )
      }

      const fixQuantities = async () => {
        if (
          !confirm('This will fix any quantity inconsistencies in the device inventory. Continue?')
        ) {
          return
        }

        isFixingQuantities.value = true
        try {
          const response = await deviceInventoryService.fixQuantities()
          if (response.success) {
            const { fixed_count, fixed_devices } = response.data

            if (fixed_count > 0) {
              let message = `Fixed ${fixed_count} device(s) with quantity inconsistencies:\n\n`
              fixed_devices.forEach((device) => {
                message += `${device.device_name}:\n`
                message += `  Total: ${device.total_quantity}, Borrowed: ${device.borrowed_quantity}\n`
                message += `  Available: ${device.old_available_quantity} → ${device.new_available_quantity}\n\n`
              })
              alert(message)
            } else {
              alert('All device quantities are already consistent!')
            }

            // Reload data to show updated quantities
            await loadDevices()
            await loadStatistics()
          } else {
            alert('Error: ' + response.message)
          }
        } catch (error) {
          console.error('Error fixing quantities:', error)
          alert('Error fixing quantities')
        } finally {
          isFixingQuantities.value = false
        }
      }

      const goBack = () => {
        router.push('/admin-dashboard')
      }

      // Lifecycle
      onMounted(async () => {
        await loadDevices()
        await loadStatistics()
      })

      return {
        devices,
        statistics,
        isLoading,
        isSubmitting,
        isFixingQuantities,
        searchQuery,
        statusFilter,
        showAddDeviceModal,
        showEditDeviceModal,
        deviceForm,
        filteredDevices,
        loadDevices,
        saveDevice,
        editDevice,
        deleteDevice,
        closeModal,
        getStatusClass,
        getStatusText,
        getStatusIcon,
        getReturnStatusClass,
        getReturnStatusIcon,
        getReturnStatusText,
        fixQuantities,
        goBack
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

  /* Animations */
  @keyframes float {
    0%,
    100% {
      transform: translateY(0px);
    }
    50% {
      transform: translateY(-15px);
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
</style>
