/**
 * Development Logger Utility
 * Provides controlled logging that can be easily disabled in production
 */

// Check if we're in development mode
const isDevelopment = process.env.NODE_ENV === 'development'

/**
 * Development-only console logging
 * Logs only show in development mode
 */
export const devLog = {
  // Regular logging
  log: (...args) => {
    if (isDevelopment) {
      console.log(...args)
    }
  },

  // Informational messages
  info: (...args) => {
    if (isDevelopment) {
      console.info(...args)
    }
  },

  // Warnings
  warn: (...args) => {
    if (isDevelopment) {
      console.warn(...args)
    }
  },

  // Errors (always show, even in production for debugging)
  error: (...args) => {
    console.error(...args)
  },

  // Debug messages
  // Supports two call styles:
  // 1) Tagged: devLog.debug('api', 'Request started', { ... })
  // 2) Console-style: devLog.debug('any', obj, otherArgs...)
  debug: (...args) => {
    if (!isDevelopment) return

    // Tagged format: (category: string, message: string, data?: any)
    if (args.length >= 2 && typeof args[0] === 'string' && typeof args[1] === 'string') {
      const [category, message, data = null] = args

      const emoji = {
        auth: '🔐',
        api: '🌐',
        form: '📝',
        approval: '✅',
        file: '📁',
        image: '🖼️',
        role: '👥',
        navigation: '🧭',
        state: '📊',
        validation: '✨',
        signature: '✍️',
        upload: '⬆️',
        download: '⬇️',
        cache: '💾',
        performance: '⚡',
        error: '💥',
        success: '🎉',
        warning: '⚠️',
        info: 'ℹ️'
      }

      const prefix = emoji[category] || '🔍'
      console.debug(`${prefix} [${category.toUpperCase()}]`, message, data || '')
      return
    }

    // Console-style format
    console.debug(...args)
  },

  // Performance timing
  time: (label) => {
    if (isDevelopment) {
      console.time(label)
    }
  },

  timeEnd: (label) => {
    if (isDevelopment) {
      console.timeEnd(label)
    }
  },

  // Group logging for better organization
  group: (title) => {
    if (isDevelopment) {
      console.group(title)
    }
  },

  groupEnd: () => {
    if (isDevelopment) {
      console.groupEnd()
    }
  },

  // Table logging for structured data
  table: (data) => {
    if (isDevelopment) {
      console.table(data)
    }
  }
}

/**
 * Component-specific loggers for better organization
 */
export const componentLoggers = {
  auth: {
    login: (message, data) => devLog.debug('auth', `Login: ${message}`, data),
    logout: (message, data) => devLog.debug('auth', `Logout: ${message}`, data),
    token: (message, data) => devLog.debug('auth', `Token: ${message}`, data),
    role: (message, data) => devLog.debug('role', `Role Check: ${message}`, data)
  },

  form: {
    submit: (message, data) => devLog.debug('form', `Submit: ${message}`, data),
    validate: (message, data) => devLog.debug('validation', `Validate: ${message}`, data),
    populate: (message, data) => devLog.debug('form', `Populate: ${message}`, data),
    clear: (message, data) => devLog.debug('form', `Clear: ${message}`, data)
  },

  approval: {
    stage: (message, data) => devLog.debug('approval', `Stage: ${message}`, data),
    signature: (message, data) => devLog.debug('signature', `Signature: ${message}`, data),
    decision: (message, data) => devLog.debug('approval', `Decision: ${message}`, data)
  },

  api: {
    request: (message, data) => devLog.debug('api', `Request: ${message}`, data),
    response: (message, data) => devLog.debug('api', `Response: ${message}`, data),
    error: (message, data) => devLog.debug('error', `API Error: ${message}`, data)
  },

  navigation: {
    route: (message, data) => devLog.debug('navigation', `Route: ${message}`, data),
    redirect: (message, data) => devLog.debug('navigation', `Redirect: ${message}`, data)
  },

  performance: {
    start: (operation) => devLog.time(`Performance: ${operation}`),
    end: (operation) => devLog.timeEnd(`Performance: ${operation}`),
    measure: (message, data) => devLog.debug('performance', message, data)
  }
}

/**
 * Production-safe assertions
 */
export const devAssert = (condition, message) => {
  if (isDevelopment && !condition) {
    console.error('❌ Assertion failed:', message)
    throw new Error(`Assertion failed: ${message}`)
  }
}

/**
 * Development environment check
 */
export const isDevMode = () => isDevelopment

export default {
  devLog,
  componentLoggers,
  devAssert,
  isDevMode
}
