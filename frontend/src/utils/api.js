// API layer to fetch users for each module using the configured API client
// Now uses the real backend with proper authentication

import apiClient from './apiClient'

export async function fetchJeevaUsers(params = {}) {
  try {
    const { data } = await apiClient.get('/jeeva-users', { params })
    return data // expected: { items: [...], total: number }
  } catch (error) {
    console.error('Failed to fetch Jeeva users:', error)
    throw error
  }
}

export async function fetchWellsoftUsers(params = {}) {
  try {
    const { data } = await apiClient.get('/wellsoft-users', { params })
    return data
  } catch (error) {
    console.error('Failed to fetch Wellsoft users:', error)
    throw error
  }
}

export async function fetchInternetUsers(params = {}) {
  try {
    const { data } = await apiClient.get('/internet-users', { params })
    return data
  } catch (error) {
    console.error('Failed to fetch Internet users:', error)
    throw error
  }
}

// Additional API methods for form submissions
export async function submitJeevaAccessRequest(formData) {
  try {
    const { data } = await apiClient.post('/jeeva-access-requests', formData)
    return { success: true, data }
  } catch (error) {
    return {
      success: false,
      error: error.response?.data?.message || error.message
    }
  }
}

export async function submitModuleAccessRequest(formData) {
  try {
    const { data } = await apiClient.post('/module-access-requests', formData)
    return { success: true, data }
  } catch (error) {
    return {
      success: false,
      error: error.response?.data?.message || error.message
    }
  }
}

// Get user info for forms
export async function getUserInfo() {
  try {
    const { data } = await apiClient.get('/user-info')
    return { success: true, data }
  } catch (error) {
    return {
      success: false,
      error: error.response?.data?.message || error.message
    }
  }
}
