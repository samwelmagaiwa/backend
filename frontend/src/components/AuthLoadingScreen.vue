<template>
  <div class="auth-loading-screen">
    <div class="loading-container">
      <div class="loading-spinner">
        <div class="spinner-ring"></div>
        <div class="spinner-ring"></div>
        <div class="spinner-ring"></div>
      </div>
      <h2 class="loading-title">Initializing Application</h2>
      <p class="loading-message">{{ loadingMessage }}</p>
      <div class="loading-progress">
        <div class="progress-bar" :style="{ width: progressPercentage + '%' }"></div>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    name: 'AuthLoadingScreen',
    props: {
      message: {
        type: String,
        default: 'Restoring your session...'
      },
      progress: {
        type: Number,
        default: 0
      }
    },
    computed: {
      loadingMessage() {
        return this.message
      },
      progressPercentage() {
        return Math.min(Math.max(this.progress, 0), 100)
      }
    }
  }
</script>

<style scoped>
  .auth-loading-screen {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
  }

  .loading-container {
    text-align: center;
    color: white;
    max-width: 400px;
    padding: 2rem;
  }

  .loading-spinner {
    position: relative;
    width: 80px;
    height: 80px;
    margin: 0 auto 2rem;
  }

  .spinner-ring {
    position: absolute;
    width: 100%;
    height: 100%;
    border: 3px solid transparent;
    border-top: 3px solid rgba(255, 255, 255, 0.8);
    border-radius: 50%;
    animation: spin 1.2s linear infinite;
  }

  .spinner-ring:nth-child(2) {
    width: 60px;
    height: 60px;
    top: 10px;
    left: 10px;
    animation-delay: -0.4s;
  }

  .spinner-ring:nth-child(3) {
    width: 40px;
    height: 40px;
    top: 20px;
    left: 20px;
    animation-delay: -0.8s;
  }

  @keyframes spin {
    0% {
      transform: rotate(0deg);
    }
    100% {
      transform: rotate(360deg);
    }
  }

  .loading-title {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: white;
  }

  .loading-message {
    font-size: 1rem;
    margin-bottom: 1.5rem;
    color: rgba(255, 255, 255, 0.8);
  }

  .loading-progress {
    width: 100%;
    height: 4px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 2px;
    overflow: hidden;
  }

  .progress-bar {
    height: 100%;
    background: linear-gradient(90deg, #4facfe 0%, #00f2fe 100%);
    border-radius: 2px;
    transition: width 0.3s ease;
  }
</style>
