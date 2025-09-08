/**
 * Enhanced API Composable with Request Guards
 * Prevents duplicate requests and provides proper cleanup
 */

import { ref, onUnmounted } from 'vue'
import { enhancedAPI } from '@/utils/enhancedApiClient'
import { requestDeduplicator } from '@/utils/requestDeduplicator'

export function useApiWithGuards() {
  const loading = ref(false)
  const error = ref('')
  const abortController = ref(null)

  // Cleanup on unmount
  onUnmounted(() => {
    if (abortController.value) {
      abortController.value.abort()
    }
  })

  const executeRequest = async (requestFn, options = {}) => {
    const { showLoading = true, throwOnError = false, abortPrevious = true } = options

    // Abort previous request if needed
    if (abortPrevious && abortController.value) {
      abortController.value.abort()
    }

    // Create new abort controller
    abortController.value = new AbortController()

    if (showLoading) loading.value = true
    error.value = ''

    try {
      const response = await requestFn({
        signal: abortController.value.signal
      })
      return response
    } catch (err) {
      if (err.name === 'AbortError') {
        console.log('Request was aborted')
        return null
      }

      const errorMessage = err.response?.data?.message || err.message || 'An error occurred'
      error.value = errorMessage

      if (throwOnError) {
        throw err
      }

      console.error('API Error:', err)
      return null
    } finally {
      if (showLoading) loading.value = false
    }
  }

  return {
    loading,
    error,
    executeRequest,
    clearError: () => {
      error.value = ''
    }
  }
}

/**
 * Departments composable with proper guards and deduplication
 */
export function useDepartments() {
  const { loading, error, executeRequest } = useApiWithGuards()
  const departments = ref([])
  const isLoaded = ref(false)
  const lastFetchTime = ref(0)
  const CACHE_DURATION = 5 * 60 * 1000 // 5 minutes

  const fetchDepartments = async (force = false) => {
    // Check if we need to fetch
    const now = Date.now()
    const shouldFetch = force || !isLoaded.value || now - lastFetchTime.value > CACHE_DURATION

    if (!shouldFetch) {
      console.log('📋 Departments already loaded and fresh, skipping fetch')
      return departments.value
    }

    console.log('📋 Fetching departments...')

    const response = await executeRequest((config) => enhancedAPI.get('/v1/departments', config), {
      showLoading: true
    })

    if (response?.data) {
      if (response.data.success) {
        departments.value = response.data.data || []
        isLoaded.value = true
        lastFetchTime.value = now
        console.log(`📋 Departments loaded: ${departments.value.length} items`)
      } else {
        // Handle API error response
        const errorMsg = response.data.message || 'Failed to fetch departments'
        error.value = errorMsg
        console.error('API Error:', errorMsg)

        // Use fallback data
        departments.value = getFallbackDepartments()
        isLoaded.value = true
        lastFetchTime.value = now
      }
    } else if (error.value) {
      // Use fallback data on network error
      console.warn('Using fallback departments due to network error')
      departments.value = getFallbackDepartments()
      isLoaded.value = true
      lastFetchTime.value = now
    }

    return departments.value
  }

  const refreshDepartments = () => {
    console.log('🔄 Refreshing departments...')
    isLoaded.value = false
    lastFetchTime.value = 0
    requestDeduplicator.clearCache('departments')
    return fetchDepartments(true)
  }

  const getFallbackDepartments = () => [
    { id: 1, name: 'Administration', code: 'ADMIN' },
    { id: 2, name: 'Anesthesiology', code: 'ANESTH' },
    { id: 3, name: 'Cardiology', code: 'CARDIO' },
    { id: 4, name: 'Dermatology', code: 'DERMA' },
    { id: 5, name: 'Emergency Medicine', code: 'EMERG' },
    { id: 6, name: 'ICT Department', code: 'ICT' },
    { id: 7, name: 'Internal Medicine', code: 'INTMED' },
    { id: 8, name: 'Laboratory', code: 'LAB' },
    { id: 9, name: 'Nursing', code: 'NURS' },
    { id: 10, name: 'Pharmacy', code: 'PHARM' },
    { id: 11, name: 'Radiology', code: 'RADIO' },
    { id: 12, name: 'Other', code: 'OTHER' }
  ]

  return {
    departments,
    loading,
    error,
    isLoaded,
    fetchDepartments,
    refreshDepartments
  }
}

/**
 * Generic data fetcher with guards
 */
export function useDataFetcher(endpoint, options = {}) {
  const { loading, error, executeRequest } = useApiWithGuards()
  const data = ref(null)
  const isLoaded = ref(false)
  const lastFetchTime = ref(0)

  const {
    cacheDuration = 5 * 60 * 1000, // 5 minutes default
    fallbackData = null,
    transform = (data) => data
  } = options

  const fetchData = async (force = false) => {
    const now = Date.now()
    const shouldFetch = force || !isLoaded.value || now - lastFetchTime.value > cacheDuration

    if (!shouldFetch) {
      console.log(`📊 Data for ${endpoint} already loaded and fresh`)
      return data.value
    }

    console.log(`📊 Fetching data from ${endpoint}...`)

    const response = await executeRequest((config) => enhancedAPI.get(endpoint, config), {
      showLoading: true
    })

    if (response?.data) {
      if (response.data.success) {
        data.value = transform(response.data.data)
        isLoaded.value = true
        lastFetchTime.value = now
        console.log(`📊 Data loaded from ${endpoint}`)
      } else {
        error.value = response.data.message || 'Failed to fetch data'
        if (fallbackData) {
          data.value = fallbackData
          isLoaded.value = true
          lastFetchTime.value = now
        }
      }
    } else if (error.value && fallbackData) {
      console.warn(`Using fallback data for ${endpoint}`)
      data.value = fallbackData
      isLoaded.value = true
      lastFetchTime.value = now
    }

    return data.value
  }

  const refreshData = () => {
    console.log(`🔄 Refreshing data from ${endpoint}...`)
    isLoaded.value = false
    lastFetchTime.value = 0
    requestDeduplicator.clearCache(endpoint)
    return fetchData(true)
  }

  return {
    data,
    loading,
    error,
    isLoaded,
    fetchData,
    refreshData
  }
}
