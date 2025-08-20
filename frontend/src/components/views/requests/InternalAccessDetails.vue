<template>
  <div class="flex flex-col h-screen">
    <AppHeader />
    <div class="flex flex-1 overflow-hidden">
      <DynamicSidebar v-model:collapsed="sidebarCollapsed" />
      <main class="flex-1 p-6 bg-gradient-to-br from-blue-900 via-blue-800 to-teal-900 overflow-y-auto relative">
        <!-- Background Pattern -->
        <div class="absolute inset-0 overflow-hidden">
          <div class="absolute inset-0 opacity-5">
            <div class="grid grid-cols-12 gap-8 h-full transform rotate-45">
              <div v-for="i in 48" :key="i" class="bg-white rounded-full w-2 h-2 animate-pulse" :style="{animationDelay: (i * 0.1) + 's'}"></div>
            </div>
          </div>
          <div class="absolute inset-0">
            <div v-for="i in 15" :key="i" 
                 class="absolute text-white opacity-10 animate-float"
                 :style="{
                   left: Math.random() * 100 + '%',
                   top: Math.random() * 100 + '%',
                   animationDelay: Math.random() * 3 + 's',
                   animationDuration: (Math.random() * 3 + 2) + 's',
                   fontSize: (Math.random() * 20 + 10) + 'px'
                 }">
              <i :class="['fas', ['fa-clipboard-check', 'fa-user-check', 'fa-stamp', 'fa-signature', 'fa-check-double'][Math.floor(Math.random() * 5)]]"></i>
            </div>
          </div>
        </div>
        
        <div class="max-w-full mx-auto relative z-10">
          <!-- Header Section -->
          <div class="medical-glass-card rounded-t-3xl p-6 mb-0 border-b border-blue-300/30">
            <div class="flex justify-between items-center">
              <!-- Left Logo -->
              <div class="w-28 h-28 mr-6 transform hover:scale-110 transition-transform duration-300">
                <div class="w-full h-full bg-gradient-to-br from-blue-500/20 to-teal-500/20 rounded-2xl backdrop-blur-sm border-2 border-blue-300/40 flex items-center justify-center shadow-2xl hover:shadow-blue-500/25">
                  <img src="/assets/images/ngao2.png" alt="National Shield" class="max-w-18 max-h-18 object-contain" />
                </div>
              </div>

              <!-- Center Content -->
              <div class="text-center flex-1">
                <h1 class="text-3xl font-bold text-white mb-4 tracking-wide drop-shadow-lg animate-fade-in">
                  MUHIMBILI NATIONAL HOSPITAL
                </h1>
                <div class="relative inline-block mb-4">
                  <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-8 py-3 rounded-full text-lg font-bold shadow-2xl transform hover:scale-105 transition-all duration-300 border-2 border-blue-400/60">
                    <span class="relative z-10 flex items-center gap-2">
                      <i :class="getTypeIcon(requestData?.type)"></i>
                      {{ getTypeDisplayName(requestData?.type) }} REQUEST
                    </span>
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-700 to-blue-800 rounded-full opacity-0 hover:opacity-100 transition-opacity duration-300"></div>
                  </div>
                </div>
                <h2 class="text-lg font-bold text-blue-100 tracking-wide drop-shadow-md animate-fade-in-delay">
                  Request ID: {{ requestData?.id }} - {{ getRoleDisplayName(userRole) }} Review
                </h2>
              </div>
              
              <!-- Right Logo -->
              <div class="w-28 h-28 ml-6 transform hover:scale-110 transition-transform duration-300">
                <div class="w-full h-full bg-gradient-to-br from-teal-500/20 to-blue-500/20 rounded-2xl backdrop-blur-sm border-2 border-teal-300/40 flex items-center justify-center shadow-2xl hover:shadow-teal-500/25">
                  <img src="/assets/images/logo2.png" alt="Muhimbili Logo" class="max-w-18 max-h-18 object-contain" />
                </div>
              </div>
            </div>
          </div>

          <!-- Main Content -->
          <div class="medical-glass-card rounded-b-3xl overflow-hidden">
            <div class="p-6 space-y-8">

              <!-- Back Button -->
              <div class="flex justify-between items-center">
                <button 
                  @click="goBack"
                  class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-gray-600 to-gray-700 text-white rounded-lg hover:from-gray-700 hover:to-gray-800 transition-all duration-300 font-semibold shadow-lg hover:shadow-xl transform hover:scale-105"
                >
                  <i class="fas fa-arrow-left mr-2"></i>
                  Back to Requests
                </button>
                
                <div class="flex items-center space-x-4">
                  <span :class="getStatusBadgeClass(requestData?.currentStatus)" class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium">
                    <i :class="getStatusIcon(requestData?.currentStatus)" class="mr-2"></i>
                    {{ getStatusText(requestData?.currentStatus) }}
                  </span>
                </div>
              </div>

              <!-- Approval Trail -->
              <div class="bg-gradient-to-r from-indigo-600/25 to-purple-600/25 border-2 border-indigo-400/40 p-6 rounded-2xl backdrop-blur-sm">
                <div class="flex items-center space-x-4 mb-6">
                  <div class="w-14 h-14 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-route text-white text-xl"></i>
                  </div>
                  <h3 class="text-xl font-bold text-white flex items-center">
                    <i class="fas fa-clipboard-check mr-2 text-indigo-300"></i>
                    Approval Trail
                  </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                  <!-- HOD Approval -->
                  <div class="flex flex-col items-center">
                    <div :class="getApprovalStepClass('hod')" class="w-16 h-16 rounded-full flex items-center justify-center mb-3 shadow-lg">
                      <i :class="getApprovalStepIcon('hod')" class="text-white text-xl"></i>
                    </div>
                    <h4 class="text-sm font-semibold text-white text-center">Head of Department</h4>
                    <p :class="getApprovalStatusTextClass('hod')" class="text-xs text-center mt-1">
                      {{ getApprovalStatusText('hod') }}
                    </p>
                    <p v-if="requestData?.hodApprovalDate" class="text-xs text-blue-300 text-center mt-1">
                      {{ formatDate(requestData.hodApprovalDate) }}
                    </p>
                  </div>

                  <!-- Divisional Director -->
                  <div class="flex flex-col items-center">
                    <div :class="getApprovalStepClass('divisional')" class="w-16 h-16 rounded-full flex items-center justify-center mb-3 shadow-lg">
                      <i :class="getApprovalStepIcon('divisional')" class="text-white text-xl"></i>
                    </div>
                    <h4 class="text-sm font-semibold text-white text-center">Divisional Director</h4>
                    <p :class="getApprovalStatusTextClass('divisional')" class="text-xs text-center mt-1">
                      {{ getApprovalStatusText('divisional') }}
                    </p>
                    <p v-if="requestData?.divisionalApprovalDate" class="text-xs text-blue-300 text-center mt-1">
                      {{ formatDate(requestData.divisionalApprovalDate) }}
                    </p>
                  </div>

                  <!-- DICT -->
                  <div class="flex flex-col items-center">
                    <div :class="getApprovalStepClass('dict')" class="w-16 h-16 rounded-full flex items-center justify-center mb-3 shadow-lg">
                      <i :class="getApprovalStepIcon('dict')" class="text-white text-xl"></i>
                    </div>
                    <h4 class="text-sm font-semibold text-white text-center">DICT</h4>
                    <p :class="getApprovalStatusTextClass('dict')" class="text-xs text-center mt-1">
                      {{ getApprovalStatusText('dict') }}
                    </p>
                    <p v-if="requestData?.dictApprovalDate" class="text-xs text-blue-300 text-center mt-1">
                      {{ formatDate(requestData.dictApprovalDate) }}
                    </p>
                  </div>

                  <!-- Head of IT -->
                  <div class="flex flex-col items-center">
                    <div :class="getApprovalStepClass('headOfIt')" class="w-16 h-16 rounded-full flex items-center justify-center mb-3 shadow-lg">
                      <i :class="getApprovalStepIcon('headOfIt')" class="text-white text-xl"></i>
                    </div>
                    <h4 class="text-sm font-semibold text-white text-center">Head of IT</h4>
                    <p :class="getApprovalStatusTextClass('headOfIt')" class="text-xs text-center mt-1">
                      {{ getApprovalStatusText('headOfIt') }}
                    </p>
                    <p v-if="requestData?.headOfItApprovalDate" class="text-xs text-blue-300 text-center mt-1">
                      {{ formatDate(requestData.headOfItApprovalDate) }}
                    </p>
                  </div>

                  <!-- ICT Officer -->
                  <div class="flex flex-col items-center">
                    <div :class="getApprovalStepClass('ict')" class="w-16 h-16 rounded-full flex items-center justify-center mb-3 shadow-lg">
                      <i :class="getApprovalStepIcon('ict')" class="text-white text-xl"></i>
                    </div>
                    <h4 class="text-sm font-semibold text-white text-center">ICT Officer</h4>
                    <p :class="getApprovalStatusTextClass('ict')" class="text-xs text-center mt-1">
                      {{ getApprovalStatusText('ict') }}
                    </p>
                    <p v-if="requestData?.ictApprovalDate" class="text-xs text-blue-300 text-center mt-1">
                      {{ formatDate(requestData.ictApprovalDate) }}
                    </p>
                  </div>
                </div>
              </div>

              <!-- Staff Details Section -->
              <div class="bg-gradient-to-r from-teal-600/25 to-blue-600/25 border-2 border-teal-400/40 p-6 rounded-2xl backdrop-blur-sm">
                <div class="flex items-center space-x-4 mb-6">
                  <div class="w-14 h-14 bg-gradient-to-br from-teal-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-user-md text-white text-xl"></i>
                  </div>
                  <h3 class="text-xl font-bold text-white flex items-center">
                    <i class="fas fa-id-card mr-2 text-teal-300"></i>
                    Staff Information
                  </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                  <div class="bg-white/10 p-4 rounded-xl border border-teal-300/30">
                    <label class="block text-sm font-semibold text-blue-100 mb-2">
                      <i class="fas fa-hashtag mr-2"></i>PF Number
                    </label>
                    <p class="text-white font-medium">{{ requestData?.pfNumber }}</p>
                  </div>

                  <div class="bg-white/10 p-4 rounded-xl border border-teal-300/30">
                    <label class="block text-sm font-semibold text-blue-100 mb-2">
                      <i class="fas fa-user mr-2"></i>Staff Name
                    </label>
                    <p class="text-white font-medium">{{ requestData?.staffName }}</p>
                  </div>

                  <div class="bg-white/10 p-4 rounded-xl border border-teal-300/30">
                    <label class="block text-sm font-semibold text-blue-100 mb-2">
                      <i class="fas fa-building mr-2"></i>Department
                    </label>
                    <p class="text-white font-medium">{{ requestData?.department }}</p>
                  </div>

                  <div class="bg-white/10 p-4 rounded-xl border border-teal-300/30">
                    <label class="block text-sm font-semibold text-blue-100 mb-2">
                      <i class="fas fa-signature mr-2"></i>Digital Signature
                    </label>
                    <div class="flex items-center">
                      <i :class="requestData?.digitalSignature ? 'fas fa-check-circle text-green-400' : 'fas fa-times-circle text-red-400'" class="mr-2"></i>
                      <span class="text-white font-medium">{{ requestData?.digitalSignature ? 'Signed' : 'Not Signed' }}</span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Request Details Section -->
              <div class="bg-gradient-to-r from-blue-600/25 to-cyan-600/25 border-2 border-blue-400/40 p-6 rounded-2xl backdrop-blur-sm">
                <div class="flex items-center space-x-4 mb-6">
                  <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i :class="getTypeIcon(requestData?.type)" class="text-white text-xl"></i>
                  </div>
                  <h3 class="text-xl font-bold text-white flex items-center">
                    <i class="fas fa-cogs mr-2 text-blue-300"></i>
                    Request Details
                  </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div class="bg-white/10 p-4 rounded-xl border border-blue-300/30">
                    <label class="block text-sm font-semibold text-blue-100 mb-2">
                      <i class="fas fa-layer-group mr-2"></i>Request Type
                    </label>
                    <div class="flex items-center">
                      <div :class="getTypeIconClass(requestData?.type)" class="w-8 h-8 rounded-lg flex items-center justify-center mr-3">
                        <i :class="getTypeIcon(requestData?.type)" class="text-white text-sm"></i>
                      </div>
                      <span class="text-white font-medium">{{ getTypeDisplayName(requestData?.type) }}</span>
                    </div>
                  </div>

                  <div class="bg-white/10 p-4 rounded-xl border border-blue-300/30">
                    <label class="block text-sm font-semibold text-blue-100 mb-2">
                      <i class="fas fa-calendar mr-2"></i>Submission Date
                    </label>
                    <p class="text-white font-medium">{{ formatDate(requestData?.submissionDate) }}</p>
                  </div>

                  <div class="bg-white/10 p-4 rounded-xl border border-blue-300/30">
                    <label class="block text-sm font-semibold text-blue-100 mb-2">
                      <i class="fas fa-toggle-on mr-2"></i>Module Requested For
                    </label>
                    <p class="text-white font-medium">{{ requestData?.moduleRequestedFor }}</p>
                  </div>

                  <div class="bg-white/10 p-4 rounded-xl border border-blue-300/30">
                    <label class="block text-sm font-semibold text-blue-100 mb-2">
                      <i class="fas fa-key mr-2"></i>Access Rights
                    </label>
                    <p class="text-white font-medium">{{ requestData?.accessType }}</p>
                    <p v-if="requestData?.temporaryUntil" class="text-blue-300 text-sm mt-1">
                      Until: {{ formatDate(requestData.temporaryUntil) }}
                    </p>
                  </div>
                </div>

                <!-- Selected Modules -->
                <div v-if="requestData?.selectedModules?.length" class="mt-6">
                  <label class="block text-sm font-semibold text-blue-100 mb-3">
                    <i class="fas fa-list mr-2"></i>Selected Modules
                  </label>
                  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    <div 
                      v-for="module in requestData.selectedModules" 
                      :key="module"
                      class="bg-white/10 p-3 rounded-lg border border-blue-300/30 flex items-center"
                    >
                      <i class="fas fa-check-circle text-green-400 mr-2"></i>
                      <span class="text-white text-sm">{{ module }}</span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Comments Section -->
              <div v-if="requestData?.comments" class="bg-gradient-to-r from-purple-600/25 to-indigo-600/25 border-2 border-purple-400/40 p-6 rounded-2xl backdrop-blur-sm">
                <div class="flex items-center space-x-4 mb-4">
                  <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-comments text-white text-xl"></i>
                  </div>
                  <h3 class="text-xl font-bold text-white flex items-center">
                    <i class="fas fa-comment-alt mr-2 text-purple-300"></i>
                    Comments
                  </h3>
                </div>
                <div class="bg-white/10 p-4 rounded-xl border border-purple-300/30">
                  <p class="text-white">{{ requestData.comments }}</p>
                </div>
              </div>

              <!-- Approval Actions -->
              <div v-if="canTakeAction" class="bg-gradient-to-r from-emerald-600/25 to-green-600/25 border-2 border-emerald-400/40 p-6 rounded-2xl backdrop-blur-sm">
                <div class="flex items-center space-x-4 mb-6">
                  <div class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-gavel text-white text-xl"></i>
                  </div>
                  <h3 class="text-xl font-bold text-white flex items-center">
                    <i class="fas fa-check-double mr-2 text-emerald-300"></i>
                    Approval Actions
                  </h3>
                </div>

                <!-- Comments Input -->
                <div class="mb-6">
                  <label class="block text-sm font-semibold text-blue-100 mb-3">
                    <i class="fas fa-comment mr-2"></i>Add Comments (Optional)
                  </label>
                  <textarea 
                    v-model="approvalComments"
                    rows="4" 
                    class="w-full px-4 py-3 bg-white/15 border border-emerald-300/30 rounded-xl focus:border-emerald-400 focus:outline-none text-white placeholder-blue-200/60 backdrop-blur-sm transition-all duration-300 hover:bg-white/20 focus:bg-white/20 resize-y"
                    placeholder="Enter your comments or reasons for approval/rejection..."
                  ></textarea>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                  <button 
                    @click="approveRequest"
                    :disabled="isProcessing"
                    class="px-8 py-4 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl hover:from-green-700 hover:to-emerald-700 transition-all duration-300 font-bold flex items-center justify-center shadow-lg hover:shadow-xl transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    <i class="fas fa-check mr-3"></i>
                    {{ isProcessing ? 'Processing...' : 'Approve Request' }}
                  </button>
                  
                  <button 
                    @click="rejectRequest"
                    :disabled="isProcessing"
                    class="px-8 py-4 bg-gradient-to-r from-red-600 to-pink-600 text-white rounded-xl hover:from-red-700 hover:to-pink-700 transition-all duration-300 font-bold flex items-center justify-center shadow-lg hover:shadow-xl transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    <i class="fas fa-times mr-3"></i>
                    {{ isProcessing ? 'Processing...' : 'Reject Request' }}
                  </button>
                </div>
              </div>

            </div>
          </div>
        </div>
      </main>
    </div>
    <AppFooter />

    <!-- Loading Modal -->
    <div v-if="isLoading" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-xl shadow-2xl p-8 text-center">
        <div class="w-16 h-16 border-4 border-blue-600 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
        <p class="text-gray-600">Loading request details...</p>
      </div>
    </div>

    <!-- Success Modal -->
    <div v-if="showSuccessModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-xl shadow-2xl p-8 text-center max-w-md mx-4">
        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <i class="fas fa-check text-green-600 text-2xl"></i>
        </div>
        <h3 class="text-xl font-bold text-gray-800 mb-2">{{ successMessage.title }}</h3>
        <p class="text-gray-600 mb-6">{{ successMessage.text }}</p>
        <button 
          @click="closeSuccessModal"
          class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200 font-semibold"
        >
          Continue
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import AppHeader from '@/components/header.vue'
import DynamicSidebar from '@/components/DynamicSidebar.vue'
import AppFooter from '@/components/footer.vue'
import { useAuth } from '@/composables/useAuth'

export default {
  name: 'InternalAccessDetails',
  components: {
    AppHeader,
    DynamicSidebar,
    AppFooter
  },
  setup() {
    const route = useRoute()
    const router = useRouter()
    const { userRole, ROLES, getRoleDisplayName } = useAuth()
    
    // Reactive data
    const sidebarCollapsed = ref(false)
    const requestData = ref(null)
    const isLoading = ref(true)
    const isProcessing = ref(false)
    const approvalComments = ref('')
    const showSuccessModal = ref(false)
    const successMessage = ref({ title: '', text: '' })

    // Mock request data - replace with actual API call
    const mockRequestData = {
      'REQ-001': {
        id: 'REQ-001',
        type: 'jeeva',
        staffName: 'Dr. John Doe',
        pfNumber: 'PF001234',
        department: 'Cardiology',
        digitalSignature: true,
        moduleRequestedFor: 'Use',
        selectedModules: ['DOCTOR CONSULTATION', 'MEDICAL RECORDS', 'OUTPATIENT'],
        accessType: 'Permanent (until retirement)',
        temporaryUntil: null,
        submissionDate: '2024-01-15',
        currentStatus: 'pending',
        hodApprovalStatus: 'pending',
        divisionalStatus: 'pending',
        dictStatus: 'pending',
        headOfItStatus: 'pending',
        ictStatus: 'pending',
        comments: 'Need access for patient consultation and medical record management.',
        hodApprovalDate: null,
        divisionalApprovalDate: null,
        dictApprovalDate: null,
        headOfItApprovalDate: null,
        ictApprovalDate: null
      },
      'REQ-002': {
        id: 'REQ-002',
        type: 'wellsoft',
        staffName: 'Jane Smith',
        pfNumber: 'PF005678',
        department: 'Nursing',
        digitalSignature: false,
        moduleRequestedFor: 'Use',
        selectedModules: ['NURSING STATION', 'INPATIENT', 'MEDICATION'],
        accessType: 'Temporary Until',
        temporaryUntil: '2024-06-30',
        submissionDate: '2024-01-14',
        currentStatus: 'pending',
        hodApprovalStatus: 'approved',
        divisionalStatus: 'pending',
        dictStatus: 'pending',
        headOfItStatus: 'pending',
        ictStatus: 'pending',
        comments: 'Temporary assignment to nursing station for 6 months.',
        hodApprovalDate: '2024-01-16',
        divisionalApprovalDate: null,
        dictApprovalDate: null,
        headOfItApprovalDate: null,
        ictApprovalDate: null
      }
    }

    // Computed properties
    const canTakeAction = computed(() => {
      if (!requestData.value) return false
      
      switch (userRole.value) {
        case ROLES.HEAD_OF_DEPARTMENT:
          return requestData.value.hodApprovalStatus === 'pending'
        case ROLES.DIVISIONAL_DIRECTOR:
          return requestData.value.hodApprovalStatus === 'approved' && requestData.value.divisionalStatus === 'pending'
        case ROLES.ICT_DIRECTOR:
          return requestData.value.divisionalStatus === 'approved' && requestData.value.dictStatus === 'pending'
        case ROLES.HOD_IT:
          return requestData.value.dictStatus === 'approved' && requestData.value.headOfItStatus === 'pending'
        case ROLES.ICT_OFFICER:
          return requestData.value.headOfItStatus === 'approved' && requestData.value.ictStatus === 'pending'
        default:
          return false
      }
    })

    // Methods
    const getTypeDisplayName = (type) => {
      const names = {
        jeeva: 'Jeeva Access',
        wellsoft: 'Wellsoft Access',
        internet: 'Internet Access',
        combined: 'Combined Services'
      }
      return names[type] || type
    }

    const getTypeIcon = (type) => {
      const icons = {
        jeeva: 'fas fa-database',
        wellsoft: 'fas fa-laptop-medical',
        internet: 'fas fa-wifi',
        combined: 'fas fa-layer-group'
      }
      return icons[type] || 'fas fa-file'
    }

    const getTypeIconClass = (type) => {
      const classes = {
        jeeva: 'bg-gradient-to-br from-purple-500 to-indigo-600',
        wellsoft: 'bg-gradient-to-br from-yellow-500 to-orange-600',
        internet: 'bg-gradient-to-br from-green-500 to-emerald-600',
        combined: 'bg-gradient-to-br from-blue-500 to-cyan-600'
      }
      return classes[type] || 'bg-gradient-to-br from-gray-500 to-gray-600'
    }

    const getStatusBadgeClass = (status) => {
      const classes = {
        pending: 'bg-yellow-100 text-yellow-800 border border-yellow-200',
        approved: 'bg-green-100 text-green-800 border border-green-200',
        rejected: 'bg-red-100 text-red-800 border border-red-200'
      }
      return classes[status] || 'bg-gray-100 text-gray-800 border border-gray-200'
    }

    const getStatusIcon = (status) => {
      const icons = {
        pending: 'fas fa-clock',
        approved: 'fas fa-check-circle',
        rejected: 'fas fa-times-circle'
      }
      return icons[status] || 'fas fa-question-circle'
    }

    const getStatusText = (status) => {
      const texts = {
        pending: 'Pending',
        approved: 'Approved',
        rejected: 'Rejected'
      }
      return texts[status] || 'Unknown'
    }

    const getApprovalStepClass = (step) => {
      if (!requestData.value) return 'bg-gray-500'
      
      const statuses = {
        hod: requestData.value.hodApprovalStatus,
        divisional: requestData.value.divisionalStatus,
        dict: requestData.value.dictStatus,
        headOfIt: requestData.value.headOfItStatus,
        ict: requestData.value.ictStatus
      }
      
      const status = statuses[step]
      
      if (status === 'approved') return 'bg-gradient-to-br from-green-500 to-emerald-600'
      if (status === 'rejected') return 'bg-gradient-to-br from-red-500 to-pink-600'
      if (status === 'pending') return 'bg-gradient-to-br from-yellow-500 to-orange-600'
      return 'bg-gradient-to-br from-gray-500 to-gray-600'
    }

    const getApprovalStepIcon = (step) => {
      if (!requestData.value) return 'fas fa-clock'
      
      const statuses = {
        hod: requestData.value.hodApprovalStatus,
        divisional: requestData.value.divisionalStatus,
        dict: requestData.value.dictStatus,
        headOfIt: requestData.value.headOfItStatus,
        ict: requestData.value.ictStatus
      }
      
      const status = statuses[step]
      
      if (status === 'approved') return 'fas fa-check'
      if (status === 'rejected') return 'fas fa-times'
      if (status === 'pending') return 'fas fa-clock'
      return 'fas fa-minus'
    }

    const getApprovalStatusText = (step) => {
      if (!requestData.value) return 'Pending'
      
      const statuses = {
        hod: requestData.value.hodApprovalStatus,
        divisional: requestData.value.divisionalStatus,
        dict: requestData.value.dictStatus,
        headOfIt: requestData.value.headOfItStatus,
        ict: requestData.value.ictStatus
      }
      
      const status = statuses[step]
      
      if (status === 'approved') return 'Approved'
      if (status === 'rejected') return 'Rejected'
      if (status === 'pending') return 'Pending'
      return 'Not Started'
    }

    const getApprovalStatusTextClass = (step) => {
      if (!requestData.value) return 'text-gray-400'
      
      const statuses = {
        hod: requestData.value.hodApprovalStatus,
        divisional: requestData.value.divisionalStatus,
        dict: requestData.value.dictStatus,
        headOfIt: requestData.value.headOfItStatus,
        ict: requestData.value.ictStatus
      }
      
      const status = statuses[step]
      
      if (status === 'approved') return 'text-green-400'
      if (status === 'rejected') return 'text-red-400'
      if (status === 'pending') return 'text-yellow-400'
      return 'text-gray-400'
    }

    const formatDate = (dateString) => {
      if (!dateString) return ''
      const date = new Date(dateString)
      return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      })
    }

    const loadRequestData = async () => {
      isLoading.value = true
      try {
        const requestId = route.query.id
        const _requestType = route.query.type // Prefixed with _ to indicate intentionally unused
        
        // Simulate API call
        await new Promise(resolve => setTimeout(resolve, 1000))
        
        // In real implementation, fetch from API
        requestData.value = mockRequestData[requestId] || null
        
        if (!requestData.value) {
          throw new Error('Request not found')
        }
      } catch (error) {
        console.error('Error loading request:', error)
        alert('Error loading request details')
        router.push('/internal-access/list')
      } finally {
        isLoading.value = false
      }
    }

    const approveRequest = async () => {
      if (!confirm('Are you sure you want to approve this request?')) return
      
      isProcessing.value = true
      try {
        // Simulate API call
        await new Promise(resolve => setTimeout(resolve, 1500))
        
        // Update the appropriate approval status
        const currentDate = new Date().toISOString().split('T')[0]
        
        switch (userRole.value) {
          case ROLES.HEAD_OF_DEPARTMENT:
            requestData.value.hodApprovalStatus = 'approved'
            requestData.value.hodApprovalDate = currentDate
            break
          case ROLES.DIVISIONAL_DIRECTOR:
            requestData.value.divisionalStatus = 'approved'
            requestData.value.divisionalApprovalDate = currentDate
            break
          case ROLES.ICT_DIRECTOR:
            requestData.value.dictStatus = 'approved'
            requestData.value.dictApprovalDate = currentDate
            break
          case ROLES.HOD_IT:
            requestData.value.headOfItStatus = 'approved'
            requestData.value.headOfItApprovalDate = currentDate
            break
          case ROLES.ICT_OFFICER:
            requestData.value.ictStatus = 'approved'
            requestData.value.ictApprovalDate = currentDate
            requestData.value.currentStatus = 'approved'
            break
        }
        
        successMessage.value = {
          title: 'Request Approved!',
          text: `Request ${requestData.value.id} has been successfully approved.`
        }
        showSuccessModal.value = true
        
      } catch (error) {
        console.error('Error approving request:', error)
        alert('Error approving request. Please try again.')
      } finally {
        isProcessing.value = false
      }
    }

    const rejectRequest = async () => {
      if (!confirm('Are you sure you want to reject this request?')) return
      
      isProcessing.value = true
      try {
        // Simulate API call
        await new Promise(resolve => setTimeout(resolve, 1500))
        
        // Update the appropriate approval status
        const currentDate = new Date().toISOString().split('T')[0]
        
        switch (userRole.value) {
          case ROLES.HEAD_OF_DEPARTMENT:
            requestData.value.hodApprovalStatus = 'rejected'
            requestData.value.hodApprovalDate = currentDate
            break
          case ROLES.DIVISIONAL_DIRECTOR:
            requestData.value.divisionalStatus = 'rejected'
            requestData.value.divisionalApprovalDate = currentDate
            break
          case ROLES.ICT_DIRECTOR:
            requestData.value.dictStatus = 'rejected'
            requestData.value.dictApprovalDate = currentDate
            break
          case ROLES.HOD_IT:
            requestData.value.headOfItStatus = 'rejected'
            requestData.value.headOfItApprovalDate = currentDate
            break
          case ROLES.ICT_OFFICER:
            requestData.value.ictStatus = 'rejected'
            requestData.value.ictApprovalDate = currentDate
            break
        }
        
        requestData.value.currentStatus = 'rejected'
        
        successMessage.value = {
          title: 'Request Rejected!',
          text: `Request ${requestData.value.id} has been rejected.`
        }
        showSuccessModal.value = true
        
      } catch (error) {
        console.error('Error rejecting request:', error)
        alert('Error rejecting request. Please try again.')
      } finally {
        isProcessing.value = false
      }
    }

    const goBack = () => {
      router.push('/internal-access/list')
    }

    const closeSuccessModal = () => {
      showSuccessModal.value = false
      // Optionally redirect back to list
      setTimeout(() => {
        goBack()
      }, 500)
    }

    // Lifecycle
    onMounted(async () => {
      await loadRequestData()
    })

    return {
      sidebarCollapsed,
      requestData,
      isLoading,
      isProcessing,
      approvalComments,
      showSuccessModal,
      successMessage,
      canTakeAction,
      userRole,
      ROLES,
      getRoleDisplayName,
      getTypeDisplayName,
      getTypeIcon,
      getTypeIconClass,
      getStatusBadgeClass,
      getStatusIcon,
      getStatusText,
      getApprovalStepClass,
      getApprovalStepIcon,
      getApprovalStatusText,
      getApprovalStatusTextClass,
      formatDate,
      approveRequest,
      rejectRequest,
      goBack,
      closeSuccessModal
    }
  }
}
</script>

<style scoped>
/* Glass morphism effects */
.medical-glass-card {
  background: rgba(59, 130, 246, 0.15);
  backdrop-filter: blur(25px);
  -webkit-backdrop-filter: blur(25px);
  border: 2px solid rgba(96, 165, 250, 0.3);
  box-shadow: 0 8px 32px rgba(29, 78, 216, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.1);
}

/* Animations */
@keyframes float {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-15px); }
}

@keyframes fade-in {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes fade-in-delay {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
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

/* Focus styles for accessibility */
textarea:focus, button:focus {
  box-shadow: 0 0 0 3px rgba(45, 212, 191, 0.3);
}

/* Smooth transitions */
* {
  transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 200ms;
}

/* Custom scrollbar */
::-webkit-scrollbar {
  width: 8px;
}

::-webkit-scrollbar-track {
  background: rgba(59, 130, 246, 0.1);
}

::-webkit-scrollbar-thumb {
  background: rgba(59, 130, 246, 0.3);
  border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
  background: rgba(59, 130, 246, 0.5);
}
</style>