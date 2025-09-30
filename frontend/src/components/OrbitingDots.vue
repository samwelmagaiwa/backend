<template>
  <div class="orbiting-dots">
    <div class="dot dot-1"></div>
    <div class="dot dot-2"></div>
    <div class="dot dot-3"></div>
  </div>
</template>

<script>
  export default {
    name: 'OrbitingDots'
  }
</script>

<style scoped>
  .orbiting-dots {
    position: relative;
    display: inline-block;
    width: 40px;
    height: 40px;
  }

  .dot {
    position: absolute;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background-color: #3b82f6;
    animation: orbit 2s linear infinite;
  }

  .dot-1 {
    top: 0;
    left: 50%;
    transform: translateX(-50%);
    animation-delay: 0s;
  }

  .dot-2 {
    top: 50%;
    right: 0;
    transform: translateY(-50%);
    animation-delay: -0.67s;
  }

  .dot-3 {
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    animation-delay: -1.33s;
  }

  @keyframes orbit {
    0% {
      transform: rotate(0deg) translate(16px) rotate(0deg);
    }
    100% {
      transform: rotate(360deg) translate(16px) rotate(-360deg);
    }
  }
</style>
