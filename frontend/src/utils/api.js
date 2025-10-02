// API layer to fetch users for each module using the configured API client
// Now uses the real backend with proper authentication and request_type filtering

import apiClient from './apiClient'

/**
 * Fetch Jeeva system users from user_access table filtered by request_type 'jeeva_access'
 */
export async function fetchJeevaUsers(params = {}) {
  try {
    console.log('🔍 Fetching Jeeva users with params:', params)
    const { data } = await apiClient.get('/jeeva-users', { params })
    console.log('✅ Jeeva users response:', data)
    
    // Transform response to match expected format
    return {
      items: data.items || [],
      total: data.total || 0,
      success: data.success || false,
      message: data.message || 'Jeeva users retrieved'
    }
  } catch (error) {
    console.error('❌ Failed to fetch Jeeva users:', error)
    console.error('Error details:', {
      status: error.response?.status,
      statusText: error.response?.statusText,
      message: error.response?.data?.message,
      data: error.response?.data
    })
    
    // Return fallback data to prevent UI breaking
    return {
      items: [],
      total: 0,
      success: false,
      message: error.response?.data?.message || 'Failed to fetch Jeeva users'
    }
  }
}

/**
 * Fetch Wellsoft system users from user_access table filtered by request_type 'wellsoft'
 */
export async function fetchWellsoftUsers(params = {}) {
  try {
    console.log('🔍 Fetching Wellsoft users with params:', params)
    const { data } = await apiClient.get('/wellsoft-users', { params })
    console.log('✅ Wellsoft users response:', data)
    
    // Transform response to match expected format
    return {
      items: data.items || [],
      total: data.total || 0,
      success: data.success || false,
      message: data.message || 'Wellsoft users retrieved'
    }
  } catch (error) {
    console.error('❌ Failed to fetch Wellsoft users:', error)
    console.error('Error details:', {
      status: error.response?.status,
      statusText: error.response?.statusText,
      message: error.response?.data?.message,
      data: error.response?.data
    })
    
    // Return fallback data to prevent UI breaking
    return {
      items: [],
      total: 0,
      success: false,
      message: error.response?.data?.message || 'Failed to fetch Wellsoft users'
    }
  }
}

/**
 * Fetch Internet access users from user_access table filtered by request_type 'internet_access_request'
 */
export async function fetchInternetUsers(params = {}) {
  try {
    console.log('🔍 Fetching Internet users with params:', params)
    const { data } = await apiClient.get('/internet-users', { params })
    console.log('✅ Internet users response:', data)
    
    // Transform response to match expected format
    return {
      items: data.items || [],
      total: data.total || 0,
      success: data.success || false,
      message: data.message || 'Internet users retrieved'
    }
  } catch (error) {
    console.error('❌ Failed to fetch Internet users:', error)
    console.error('Error details:', {
      status: error.response?.status,
      statusText: error.response?.statusText,
      message: error.response?.data?.message,
      data: error.response?.data
    })
    
    // Return fallback data to prevent UI breaking
    return {
      items: [],
      total: 0,
      success: false,
      message: error.response?.data?.message || 'Failed to fetch Internet users'
    }
  }
}
