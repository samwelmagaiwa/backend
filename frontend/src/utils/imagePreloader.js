/**
 * Smart Image Preloader
 * Preloads images based on user role and navigation patterns
 * to avoid unnecessary preloading warnings
 */

import { devLog } from './devLogger.js'

// Track preloaded images to avoid duplicates
const preloadedImages = new Set()

/**
 * Preload an image with proper error handling
 * @param {string} src - Image source URL
 * @param {string} alt - Alt text for accessibility
 * @returns {Promise<HTMLImageElement>} - Promise that resolves when image is loaded
 */
export function preloadImage(src, alt = '') {
  return new Promise((resolve, reject) => {
    // Skip if already preloaded
    if (preloadedImages.has(src)) {
      resolve(null)
      return
    }

    const img = new Image()

    img.onload = () => {
      preloadedImages.add(src)
      devLog.debug('image', `✅ Image preloaded: ${src}`)
      resolve(img)
    }

    img.onerror = (error) => {
      devLog.warn('❌ Failed to preload image:', src, error)
      reject(error)
    }

    img.src = src
    img.alt = alt
  })
}

/**
 * Preload images using link preload for better performance
 * @param {string} src - Image source URL
 * @param {string} type - Image MIME type
 */
export function preloadImageWithLink(src, type = 'image/png') {
  // Skip if already preloaded
  if (preloadedImages.has(src)) {
    return
  }

  // Check if link already exists
  const existingLink = document.querySelector(`link[href="${src}"]`)
  if (existingLink) {
    preloadedImages.add(src)
    return
  }

  const link = document.createElement('link')
  link.rel = 'preload'
  link.as = 'image'
  link.type = type
  link.href = src

  // Add to head
  document.head.appendChild(link)
  preloadedImages.add(src)

  devLog.debug('image', `🔗 Image preloaded via link: ${src}`)
}

/**
 * Role-based image preloading
 * Preloads images that are likely to be needed based on user role
 * @param {string} userRole - User's role
 */
export function preloadRoleBasedImages(userRole) {
  devLog.debug('image', `🎯 Preloading images for role: ${userRole}`)

  const baseUrl = '/assets/images/'

  // Common images used across multiple forms
  const commonImages = [
    { src: `${baseUrl}logo2.png`, alt: 'Muhimbili Logo' },
    { src: `${baseUrl}ngao2.png`, alt: 'National Shield' }
  ]

  // Normalize role for consistent mapping (handles spaces, hyphens, camelCase, etc.)
  const normalizedRole = (userRole || '').toLowerCase().replace(/[\s-]+/g, '_')
  devLog.debug('role', `🔄 Normalized role: ${normalizedRole}`)

  // Role-specific image preloading with normalized keys
  const roleImageMap = {
    // Head of Department variants
    head_of_department: {
      priority: 'high',
      images: commonImages,
      reason: 'HOD accesses request forms and approval pages'
    },
    hod: {
      priority: 'high',
      images: commonImages,
      reason: 'HOD accesses request forms and approval pages'
    },
    head_department: {
      priority: 'high',
      images: commonImages,
      reason: 'Head Department accesses request forms and approval pages'
    },
    // Divisional Director variants
    divisional_director: {
      priority: 'high',
      images: commonImages,
      reason: 'Divisional Director accesses forms and approval pages'
    },
    director_divisional: {
      priority: 'high',
      images: commonImages,
      reason: 'Director Divisional accesses forms and approval pages'
    },
    // ICT Director variants
    ict_director: {
      priority: 'high',
      images: commonImages,
      reason: 'ICT Director accesses forms and approval pages'
    },
    director_ict: {
      priority: 'high',
      images: commonImages,
      reason: 'Director ICT accesses forms and approval pages'
    },
    dict: {
      priority: 'high',
      images: commonImages,
      reason: 'DICT accesses forms and approval pages'
    },
    // Head IT variants
    head_it: {
      priority: 'high',
      images: commonImages,
      reason: 'Head IT accesses forms and approval pages'
    },
    head_of_it: {
      priority: 'high',
      images: commonImages,
      reason: 'Head of IT accesses forms and approval pages'
    },
    ict_head: {
      priority: 'high',
      images: commonImages,
      reason: 'ICT Head accesses forms and approval pages'
    },
    it_head: {
      priority: 'high',
      images: commonImages,
      reason: 'IT Head accesses forms and approval pages'
    },
    // ICT Officer variants
    ict_officer: {
      priority: 'high',
      images: commonImages,
      reason: 'ICT Officer accesses approval and management pages'
    },
    officer_ict: {
      priority: 'high',
      images: commonImages,
      reason: 'Officer ICT accesses approval and management pages'
    },
    // Staff
    staff: {
      priority: 'medium',
      images: commonImages,
      reason: 'Staff accesses user forms and booking service'
    },
    // Admin
    admin: {
      priority: 'low',
      images: [], // Admin rarely uses forms with these images
      reason: 'Admin primarily uses admin dashboard'
    }
  }

  const roleConfig = roleImageMap[normalizedRole]

  if (!roleConfig) {
    devLog.warn('⚠️ Unknown role for image preloading:', userRole)
    return
  }

  devLog.debug('image', `📋 Preloading strategy for ${userRole}: ${roleConfig.reason}`)

  // Preload images based on priority
  if (roleConfig.priority === 'high') {
    // Immediate preload for high priority roles
    roleConfig.images.forEach(({ src }) => {
      preloadImageWithLink(src)
    })
  } else if (roleConfig.priority === 'medium') {
    // Delayed preload for medium priority roles
    setTimeout(() => {
      roleConfig.images.forEach(({ src, alt }) => {
        preloadImage(src, alt).catch(() => {}) // Silent fail
      })
    }, 1000)
  }
  // Low priority roles don't preload
}

/**
 * Route-based image preloading
 * Preloads images when user navigates to routes that will need them
 * @param {string} routePath - Current route path
 */
export function preloadRouteBasedImages(routePath) {
  const baseUrl = '/assets/images/'

  // Routes that use the common images
  const imageRoutes = [
    '/hod-dashboard/request-list',
    '/internal-access/details',
    '/both-service-form',
    '/jeeva-access',
    '/wellsoft-access',
    '/internet-access',
    '/user-combined-form',
    '/booking-service',
    '/ict-approval/requests',
    '/ict-approval/request',
    '/onboarding'
  ]

  // Check if current route needs images
  const needsImages = imageRoutes.some((route) => routePath.startsWith(route))

  if (needsImages) {
    devLog.debug('navigation', `🖼️ Route requires images, preloading: ${routePath}`)
    preloadImageWithLink(`${baseUrl}logo2.png`)
    preloadImageWithLink(`${baseUrl}ngao2.png`)
  }
}

/**
 * Clean up preloaded images that are no longer needed
 */
export function cleanupPreloadedImages() {
  // Remove preload links that are no longer needed
  const preloadLinks = document.querySelectorAll('link[rel="preload"][as="image"]')

  preloadLinks.forEach((link) => {
    // Remove after a delay to ensure images are cached
    setTimeout(() => {
      if (link.parentNode) {
        link.parentNode.removeChild(link)
        devLog.debug('cache', `🧹 Cleaned up preload link: ${link.href}`)
      }
    }, 5000) // 5 second delay
  })
}

/**
 * Initialize smart image preloading
 * @param {string} userRole - User's role
 * @param {string} currentRoute - Current route path
 */
export function initializeImagePreloading(userRole, currentRoute) {
  devLog.debug('image', '🚀 Initializing smart image preloading')

  // Preload based on user role
  if (userRole) {
    preloadRoleBasedImages(userRole)
  }

  // Preload based on current route
  if (currentRoute) {
    preloadRouteBasedImages(currentRoute)
  }

  // Clean up after some time
  setTimeout(cleanupPreloadedImages, 10000) // 10 seconds
}

export default {
  preloadImage,
  preloadImageWithLink,
  preloadRoleBasedImages,
  preloadRouteBasedImages,
  initializeImagePreloading,
  cleanupPreloadedImages
}
