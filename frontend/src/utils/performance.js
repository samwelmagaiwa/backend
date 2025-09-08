// Performance optimization utilities

/**
 * Register service worker for caching
 */
export function registerServiceWorker() {
  if ('serviceWorker' in navigator && process.env.NODE_ENV === 'production') {
    window.addEventListener('load', () => {
      navigator.serviceWorker
        .register('/sw.js')
        .then((registration) => {
          console.log('SW registered: ', registration)
        })
        .catch((registrationError) => {
          console.log('SW registration failed: ', registrationError)
        })
    })
  }
}

/**
 * Preload critical routes
 */
export function preloadCriticalRoutes() {
  const criticalRoutes = [
    () => import('@/components/LoginPage.vue'),
    () => import('@/components/UserDashboard.vue'),
    () => import('@/components/admin/AdminDashboard.vue')
  ]

  // Preload after initial load
  setTimeout(() => {
    criticalRoutes.forEach((route) => {
      route().catch(() => {
        // Ignore preload errors
      })
    })
  }, 2000)
}

/**
 * Lazy load images with intersection observer
 */
export function setupLazyLoading() {
  if ('IntersectionObserver' in window) {
    const imageObserver = new IntersectionObserver((entries, observer) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          const img = entry.target
          img.src = img.dataset.src
          img.classList.remove('lazy')
          observer.unobserve(img)
        }
      })
    })

    document.querySelectorAll('img[data-src]').forEach((img) => {
      imageObserver.observe(img)
    })
  }
}

/**
 * Debounce function for performance
 */
export function debounce(func, wait, immediate) {
  let timeout
  return function executedFunction(...args) {
    const later = () => {
      timeout = null
      if (!immediate) func(...args)
    }
    const callNow = immediate && !timeout
    clearTimeout(timeout)
    timeout = setTimeout(later, wait)
    if (callNow) func(...args)
  }
}

/**
 * Throttle function for performance
 */
export function throttle(func, limit) {
  let inThrottle
  return function (...args) {
    if (!inThrottle) {
      func.apply(this, args)
      inThrottle = true
      setTimeout(() => (inThrottle = false), limit)
    }
  }
}

/**
 * Optimize images by adding loading="lazy"
 */
export function optimizeImages() {
  document.querySelectorAll('img').forEach((img) => {
    if (!img.hasAttribute('loading')) {
      img.setAttribute('loading', 'lazy')
    }
  })
}

/**
 * Preconnect to external domains
 */
export function preconnectDomains() {
  const domains = [
    'https://fonts.googleapis.com',
    'https://fonts.gstatic.com',
    'https://cdnjs.cloudflare.com'
  ]

  domains.forEach((domain) => {
    const link = document.createElement('link')
    link.rel = 'preconnect'
    link.href = domain
    link.crossOrigin = 'anonymous'
    document.head.appendChild(link)
  })
}

/**
 * Monitor performance metrics
 */
export function monitorPerformance() {
  if ('performance' in window) {
    window.addEventListener('load', () => {
      setTimeout(() => {
        const perfData = performance.getEntriesByType('navigation')[0]
        const metrics = {
          dns: perfData.domainLookupEnd - perfData.domainLookupStart,
          tcp: perfData.connectEnd - perfData.connectStart,
          ttfb: perfData.responseStart - perfData.requestStart,
          download: perfData.responseEnd - perfData.responseStart,
          dom: perfData.domContentLoadedEventEnd - perfData.domContentLoadedEventStart,
          total: perfData.loadEventEnd - perfData.navigationStart
        }

        console.log('Performance Metrics:', metrics)

        // Send to analytics if needed
        if (window.gtag) {
          window.gtag('event', 'timing_complete', {
            name: 'load',
            value: metrics.total
          })
        }
      }, 0)
    })
  }
}

/**
 * Critical CSS inlining helper
 */
export function inlineCriticalCSS() {
  // This would be used with a build tool to inline critical CSS
  const criticalCSS = `
    /* Critical CSS for above-the-fold content */
    body { margin: 0; font-family: Inter, sans-serif; }
    .loading-spinner {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
  `

  const style = document.createElement('style')
  style.textContent = criticalCSS
  document.head.appendChild(style)
}

/**
 * Resource hints helper
 */
export function addResourceHints() {
  const hints = [
    { rel: 'dns-prefetch', href: 'https://fonts.googleapis.com' },
    { rel: 'dns-prefetch', href: 'https://cdnjs.cloudflare.com' },
    { rel: 'preconnect', href: 'https://fonts.gstatic.com', crossorigin: true }
  ]

  hints.forEach((hint) => {
    const link = document.createElement('link')
    Object.keys(hint).forEach((key) => {
      if (key === 'crossorigin') {
        link.crossOrigin = hint[key]
      } else {
        link[key] = hint[key]
      }
    })
    document.head.appendChild(link)
  })
}

/**
 * Initialize all performance optimizations
 */
export function initializePerformanceOptimizations() {
  registerServiceWorker()
  preloadCriticalRoutes()
  setupLazyLoading()
  optimizeImages()
  monitorPerformance()
  addResourceHints()
}
