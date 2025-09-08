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
