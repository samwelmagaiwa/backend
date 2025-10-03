<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-900 via-blue-800 to-teal-900 py-8 px-4">
    <div class="max-w-4xl mx-auto">
      <!-- Header -->
      <div class="bg-white/10 backdrop-blur-sm border border-blue-300/30 rounded-2xl p-8 mb-8">
        <div class="text-center">
          <h1 class="text-4xl font-bold text-white mb-4">ICT POLICY</h1>
          <p class="text-blue-100 text-lg">
            Muhimbili National Hospital - Information and Communication Technology Department
          </p>
        </div>
      </div>

      <!-- Content -->
      <div class="bg-white/10 backdrop-blur-sm border border-blue-300/30 rounded-2xl p-8 mb-8">
        <h2 class="text-2xl font-bold text-white mb-4">Policy Overview</h2>
        <p class="text-blue-100 leading-relaxed mb-4">
          This ICT Policy establishes the framework for the secure, efficient, and appropriate use
          of information and communication technology resources at Muhimbili National Hospital.
        </p>

        <div class="mt-8">
          <h3 class="text-xl font-bold text-white mb-4">Information Security</h3>
          <p class="text-blue-100 mb-4">
            Information security is paramount to protecting patient data, hospital operations, and
            institutional reputation.
          </p>
        </div>
      </div>

      <!-- Acknowledgment -->
      <div class="bg-white/10 backdrop-blur-sm border border-blue-300/30 rounded-2xl p-8">
        <h2 class="text-2xl font-bold text-white mb-4">Policy Acknowledgment</h2>

        <div class="mb-6">
          <p class="text-blue-100 mb-4">
            By using hospital ICT systems, you confirm that you understand and agree to comply with
            this ICT Policy and all related procedures.
          </p>

          <label class="flex items-center cursor-pointer mb-4">
            <input
              v-model="acknowledged"
              type="checkbox"
              class="w-5 h-5 text-blue-600 border-2 border-white rounded mr-3"
            />
            <span class="text-white font-medium">I have read and accept the ICT Policy</span>
          </label>
        </div>

        <div v-if="!isTimerActive && canProceed">
          <button
            @click="acceptPolicy"
            :disabled="!acknowledged"
            class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-gray-600 text-white font-semibold py-4 px-6 rounded-xl transition-colors duration-300"
          >
            Accept Policy & Proceed
          </button>
        </div>

        <div v-else class="text-center">
          <div class="bg-amber-500/20 rounded-xl p-6 border border-amber-400/40">
            <div class="text-amber-200 mb-2">Please wait {{ readingTimer }}s</div>
            <div class="text-amber-100 text-sm">You must read the policy before proceeding</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    name: 'IctPolicySimple',
    data() {
      return {
        acknowledged: false,
        readingTimer: 5,
        isTimerActive: true,
        canProceed: false,
        timerInterval: null
      }
    },
    methods: {
      startReadingTimer() {
        if (this.timerInterval) {
          clearInterval(this.timerInterval)
        }

        this.isTimerActive = true
        this.canProceed = false
        this.readingTimer = 5

        this.timerInterval = setInterval(() => {
          this.readingTimer--
          if (this.readingTimer <= 0) {
            clearInterval(this.timerInterval)
            this.timerInterval = null
            this.isTimerActive = false
            this.canProceed = true
          }
        }, 1000)
      },

      acceptPolicy() {
        if (this.acknowledged && this.canProceed) {
          alert('ICT Policy accepted successfully!')
          this.$emit('policy-acknowledged', {
            acknowledged: true,
            timestamp: new Date().toISOString(),
            version: '1.0'
          })
        }
      }
    },

    mounted() {
      this.startReadingTimer()
    },

    beforeUnmount() {
      if (this.timerInterval) {
        clearInterval(this.timerInterval)
        this.timerInterval = null
      }
    }
  }
</script>

<style scoped>
  /* Simple styles without heavy animations */
  .transition-colors {
    transition-property: background-color, border-color, color, fill, stroke;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 300ms;
  }
</style>
