import apiClient from '../utils/apiClient'

/**
 * Both Service Form Service for combined access requests
 * Handles submission and management of combined Jeeva, Wellsoft, and Internet access requests
 */
class BothServiceFormService {
  /**
   * Submit a new combined access request
   * @param {Object} formData - Form data including modules, signatures, and user info
   * @returns {Promise<Object>} Response with submission result
   */
  async submitCombinedRequest(formData) {
    try {
      console.log('🔄 Submitting combined access request...', formData)

      // Create FormData for multipart/form-data submission
      const submitData = new FormData()

      // Add personal information
      if (formData.shared) {
        submitData.append('pf_number', formData.shared.pfNumber || '')
        submitData.append('staff_name', formData.shared.staffName || '')
        submitData.append('department', formData.shared.department || '')
        submitData.append('phone_number', formData.shared.phone || '')
      }

      // Add signature file if provided
      if (formData.signature) {
        submitData.append('signature', formData.signature)
      }

      // Add module selections with explicit debugging
      console.log('🔍 MODULE SELECTION DEBUG:')
      console.log(
        'selectedWellsoft:',
        formData.selectedWellsoft,
        'length:',
        formData.selectedWellsoft?.length
      )
      console.log(
        'selectedJeeva:',
        formData.selectedJeeva,
        'length:',
        formData.selectedJeeva?.length
      )
      console.log('selectedWellsoft type:', typeof formData.selectedWellsoft)
      console.log('selectedJeeva type:', typeof formData.selectedJeeva)
      console.log('selectedWellsoft isArray:', Array.isArray(formData.selectedWellsoft))
      console.log('selectedJeeva isArray:', Array.isArray(formData.selectedJeeva))

      if (formData.selectedWellsoft && formData.selectedWellsoft.length > 0) {
        console.log('✅ Adding Wellsoft modules to FormData:', formData.selectedWellsoft)
        formData.selectedWellsoft.forEach((module, index) => {
          submitData.append(`selectedWellsoft[${index}]`, module)
          console.log(`  Added selectedWellsoft[${index}]:`, module)
        })
      } else {
        console.log('⚠️ No Wellsoft modules to add - array is empty or undefined')
      }

      if (formData.selectedJeeva && formData.selectedJeeva.length > 0) {
        console.log('✅ Adding Jeeva modules to FormData:', formData.selectedJeeva)
        formData.selectedJeeva.forEach((module, index) => {
          submitData.append(`selectedJeeva[${index}]`, module)
          console.log(`  Added selectedJeeva[${index}]:`, module)
        })
      } else {
        console.log('⚠️ No Jeeva modules to add - array is empty or undefined')
      }

      // Add request type (use/revoke)
      if (formData.wellsoftRequestType) {
        submitData.append('wellsoftRequestType', formData.wellsoftRequestType)
      }

      // Add internet purposes
      if (formData.internetPurposes && formData.internetPurposes.length > 0) {
        formData.internetPurposes.forEach((purpose, index) => {
          if (purpose && purpose.trim()) {
            submitData.append(`internetPurposes[${index}]`, purpose.trim())
          }
        })
      }

      // Determine which services are being requested
      const requestTypes = []
      if (formData.selectedWellsoft && formData.selectedWellsoft.length > 0) {
        requestTypes.push('wellsoft')
      } else if (formData.selectedWellsoft !== undefined) {
        console.warn('⚠️ Wellsoft array exists but is empty - user may not have selected modules')
      }
      if (formData.selectedJeeva && formData.selectedJeeva.length > 0) {
        requestTypes.push('jeeva_access')
      } else if (formData.selectedJeeva !== undefined) {
        console.warn('⚠️ Jeeva array exists but is empty - user may not have selected modules')
      }
      if (
        formData.internetPurposes &&
        formData.internetPurposes.some((purpose) => purpose && purpose.trim())
      ) {
        requestTypes.push('internet_access_request')
      }

      // Add request types - ensure proper format expected by backend
      if (requestTypes.length > 0) {
        requestTypes.forEach((type, index) => {
          submitData.append(`request_types[${index}]`, type)
        })
      } else {
        // If no modules or purposes selected, this should be caught by validation
        console.warn('No request types determined from form data')
      }

      // Add approval data if provided
      if (formData.approvals) {
        // HOD data
        if (formData.approvals.hod) {
          if (formData.approvals.hod.name) {
            submitData.append('hodName', formData.approvals.hod.name)
          }
          if (formData.approvals.hod.signature) {
            submitData.append('hodSignature', formData.approvals.hod.signature)
          }
          if (formData.approvals.hod.date) {
            submitData.append('hodDate', formData.approvals.hod.date)
          }
        }

        // Divisional Director data
        if (formData.approvals.divisionalDirector) {
          if (formData.approvals.divisionalDirector.name) {
            submitData.append('divisionalDirectorName', formData.approvals.divisionalDirector.name)
          }
          if (formData.approvals.divisionalDirector.signature) {
            submitData.append(
              'divisionalDirectorSignature',
              formData.approvals.divisionalDirector.signature
            )
          }
          if (formData.approvals.divisionalDirector.date) {
            submitData.append('divisionalDirectorDate', formData.approvals.divisionalDirector.date)
          }
        }

        // ICT Director data
        if (formData.approvals.directorICT) {
          if (formData.approvals.directorICT.name) {
            submitData.append('ictDirectorName', formData.approvals.directorICT.name)
          }
          if (formData.approvals.directorICT.signature) {
            submitData.append('ictDirectorSignature', formData.approvals.directorICT.signature)
          }
          if (formData.approvals.directorICT.date) {
            submitData.append('ictDirectorDate', formData.approvals.directorICT.date)
          }
        }
      }

      // Add implementation data if provided
      if (formData.implementation) {
        // Head IT data
        if (formData.implementation.headIT) {
          if (formData.implementation.headIT.name) {
            submitData.append('headITName', formData.implementation.headIT.name)
          }
          if (formData.implementation.headIT.signature) {
            submitData.append('headITSignature', formData.implementation.headIT.signature)
          }
          if (formData.implementation.headIT.date) {
            submitData.append('headITDate', formData.implementation.headIT.date)
          }
        }

        // ICT Officer data
        if (formData.implementation.ictOfficer) {
          if (formData.implementation.ictOfficer.name) {
            submitData.append('ictOfficerName', formData.implementation.ictOfficer.name)
          }
          if (formData.implementation.ictOfficer.signature) {
            submitData.append('ictOfficerSignature', formData.implementation.ictOfficer.signature)
          }
          if (formData.implementation.ictOfficer.date) {
            submitData.append('ictOfficerDate', formData.implementation.ictOfficer.date)
          }
        }
      }

      // Add implementation comments if provided
      if (formData.comments) {
        submitData.append('implementationComments', formData.comments)
      }

      // Debug: Log FormData contents
      console.log('FormData contents:')
      for (let [key, value] of submitData.entries()) {
        console.log(`${key}:`, value)
      }

      const response = await apiClient.post('/both-service-form', submitData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })

      if (response.data && response.data.success) {
        console.log('✅ Combined access request submitted successfully:', response.data.data?.id)
        return {
          success: true,
          data: response.data.data,
          message: response.data.message,
          details: response.data.details
        }
      } else {
        throw new Error(response.data?.message || 'Failed to submit combined access request')
      }
    } catch (error) {
      console.error('❌ Error submitting combined access request:', error)

      // Handle validation errors
      if (error.response?.status === 422) {
        return {
          success: false,
          error: 'Validation failed',
          errors: error.response.data.errors || {},
          message: error.response.data.message || 'Please check the form and try again'
        }
      }

      return {
        success: false,
        error:
          error.response?.data?.message ||
          error.message ||
          'Failed to submit combined access request',
        message: error.response?.data?.message || 'An error occurred while submitting your request'
      }
    }
  }

  /**
   * Get user information for form pre-population
   * @returns {Promise<Object>} Response with user data
   */
  async getUserInfo() {
    try {
      console.log('🔄 Fetching user info for form pre-population...')

      const response = await apiClient.get('/both-service-form/user/info')

      if (response.data && response.data.success) {
        console.log('✅ User info retrieved successfully')
        return {
          success: true,
          data: response.data.data
        }
      } else {
        throw new Error(response.data?.message || 'Failed to retrieve user information')
      }
    } catch (error) {
      console.error('❌ Error fetching user info:', error)
      return {
        success: false,
        error:
          error.response?.data?.message || error.message || 'Failed to retrieve user information',
        data: null
      }
    }
  }

  /**
   * Get available departments
   * @returns {Promise<Object>} Response with departments list
   */
  async getDepartments() {
    try {
      console.log('🔄 Fetching departments...')

      const response = await apiClient.get('/both-service-form/departments/list')

      if (response.data && response.data.success) {
        console.log('✅ Departments retrieved successfully:', response.data.data?.length || 0)
        return {
          success: true,
          data: response.data.data
        }
      } else {
        throw new Error(response.data?.message || 'Failed to retrieve departments')
      }
    } catch (error) {
      console.error('❌ Error fetching departments:', error)
      return {
        success: false,
        error: error.response?.data?.message || error.message || 'Failed to retrieve departments',
        data: []
      }
    }
  }

  /**
   * Get a specific combined access request by ID
   * @param {number} requestId - ID of the request to retrieve
   * @returns {Promise<Object>} Response with request data
   */
  async getRequest(requestId) {
    try {
      console.log('🔄 Fetching combined access request:', requestId)

      const response = await apiClient.get(`/both-service-form/${requestId}`)

      if (response.data && response.data.success) {
        console.log('✅ Combined access request retrieved successfully')
        return {
          success: true,
          data: response.data.data,
          meta: response.data.meta
        }
      } else {
        throw new Error(response.data?.message || 'Failed to retrieve request')
      }
    } catch (error) {
      console.error('❌ Error fetching combined access request:', error)
      return {
        success: false,
        error: error.response?.data?.message || error.message || 'Failed to retrieve request',
        message: error.response?.data?.message || 'Could not load request details'
      }
    }
  }

  /**
   * Update comments/fields for an existing request (simple JSON PUT)
   */
  async updateComments(requestId, payload) {
    try {
      // Legacy endpoint (may not exist). Kept for backwards compatibility.
      const res = await apiClient.post(`/both-service-form/${requestId}/update`, payload, {
        headers: { 'Content-Type': 'application/json' }
      })
      if (res.data?.success) {
        return { success: true, data: res.data.data, message: res.data.message }
      }
      throw new Error(res.data?.message || 'Failed to update request comments')
    } catch (error) {
      return {
        success: false,
        error:
          error.response?.data?.message || error.message || 'Failed to update request comments',
        errors: error.response?.data?.errors || null
      }
    }
  }

  /**
   * Best-practice, role-aware comment save using existing role endpoints where possible,
   * with graceful fallback to a generic comment route if available.
   */
  async saveRoleCommentSmart(requestId, { role, comment, name }) {
    const r = (role || '').toLowerCase().replace(/[\s-]+/g, '_')

    // Build prioritized attempts per role
    const attempts = []

    if (r === 'divisional_director') {
      attempts.push(
        // Divisional responds to ICT Director recommendations
        {
          url: `/divisional/ict-director-recommendations/${requestId}/respond`,
          data: { divisional_comments: comment, comments: comment, divisional_name: name }
        },
        // Divisional combined access approval endpoint (accept comments only)
        {
          url: `/divisional/combined-access-requests/${requestId}/approve`,
          data: { divisional_comments: comment, comments: comment, divisional_name: name }
        }
      )
    } else if (r === 'ict_director' || r === 'dict') {
      attempts.push({
        // ICT Director combined access approval endpoint
        url: `/ict-director/combined-access-requests/${requestId}/approve`,
        data: {
          dict_comments: comment,
          ict_director_comments: comment,
          comments: comment,
          dict_name: name
        }
      })
    } else if (r === 'head_of_department' || r === 'hod' || r === 'head_department') {
      attempts.push({
        url: `/hod/combined-access-requests/${requestId}/approve`,
        data: { hod_comments: comment, comments: comment, hod_name: name }
      })
    } else if (r === 'head_it' || r === 'head_of_it' || r === 'it_head' || r === 'ict_head') {
      attempts.push({
        url: `/head-of-it/requests/${requestId}/approve`,
        data: { head_it_comments: comment, comments: comment, head_it_name: name }
      })
    }

    // Note: No generic fallback to /both-service-form/:id/update because the controller lacks an update method in some deployments.
    // We intentionally avoid that route to prevent 500 errors.

    // Try each attempt until one succeeds (HTTP 200 with success true or missing flag)
    for (const attempt of attempts) {
      try {
        const res = await apiClient.post(attempt.url, attempt.data, {
          headers: { 'Content-Type': 'application/json' }
        })
        const ok = res?.data?.success !== false // treat truthy or missing as success
        if (ok) {
          return { success: true, data: res.data?.data, message: res.data?.message }
        }
      } catch (e) {
        // Continue to next attempt
      }
    }

    return {
      success: false,
      error: 'No available endpoint accepted the comment payload for this role'
    }
  }

  /**
   * Update an existing combined access request (for rejected requests)
   * @param {number} requestId - ID of the request to update
   * @param {Object} formData - Updated form data
   * @returns {Promise<Object>} Response with update result
   */
  async updateRequest(requestId, formData) {
    try {
      console.log('🔄 Updating combined access request:', requestId, formData)

      // Create FormData for multipart/form-data submission (same as submitCombinedRequest)
      const submitData = new FormData()

      // Add personal information
      if (formData.shared) {
        submitData.append('pf_number', formData.shared.pfNumber || '')
        submitData.append('staff_name', formData.shared.staffName || '')
        submitData.append('department', formData.shared.department || '')
        submitData.append('phone_number', formData.shared.phone || '')
      }

      // Add signature file if provided (optional for updates)
      if (formData.signature) {
        submitData.append('signature', formData.signature)
      }

      // Add module selections
      if (formData.selectedWellsoft && formData.selectedWellsoft.length > 0) {
        formData.selectedWellsoft.forEach((module, index) => {
          submitData.append(`selectedWellsoft[${index}]`, module)
        })
      }

      if (formData.selectedJeeva && formData.selectedJeeva.length > 0) {
        formData.selectedJeeva.forEach((module, index) => {
          submitData.append(`selectedJeeva[${index}]`, module)
        })
      }

      // Add request type (use/revoke)
      if (formData.wellsoftRequestType) {
        submitData.append('wellsoftRequestType', formData.wellsoftRequestType)
      }

      // Add internet purposes
      if (formData.internetPurposes && formData.internetPurposes.length > 0) {
        formData.internetPurposes.forEach((purpose, index) => {
          if (purpose && purpose.trim()) {
            submitData.append(`internetPurposes[${index}]`, purpose.trim())
          }
        })
      }

      // Determine which services are being requested
      const requestTypes = []
      if (formData.selectedWellsoft && formData.selectedWellsoft.length > 0) {
        requestTypes.push('wellsoft')
      }
      if (formData.selectedJeeva && formData.selectedJeeva.length > 0) {
        requestTypes.push('jeeva_access')
      }
      if (
        formData.internetPurposes &&
        formData.internetPurposes.some((purpose) => purpose && purpose.trim())
      ) {
        requestTypes.push('internet_access_request')
      }

      // Add request types
      requestTypes.forEach((type, index) => {
        submitData.append(`request_types[${index}]`, type)
      })

      // Debug: Log FormData contents
      console.log('Update FormData contents:')
      for (let [key, value] of submitData.entries()) {
        console.log(`${key}:`, value)
      }

      // Use POST with method spoofing for multipart/form-data
      const response = await apiClient.post(`/both-service-form/${requestId}/update`, submitData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })

      if (response.data && response.data.success) {
        console.log('✅ Combined access request updated successfully:', requestId)
        return {
          success: true,
          data: response.data.data,
          message: response.data.message,
          details: response.data.details
        }
      } else {
        throw new Error(response.data?.message || 'Failed to update combined access request')
      }
    } catch (error) {
      console.error('❌ Error updating combined access request:', error)

      // Handle validation errors
      if (error.response?.status === 422) {
        return {
          success: false,
          error: 'Validation failed',
          errors: error.response.data.errors || {},
          message: error.response.data.message || 'Please check the form and try again'
        }
      }

      return {
        success: false,
        error:
          error.response?.data?.message ||
          error.message ||
          'Failed to update combined access request',
        message: error.response?.data?.message || 'An error occurred while updating your request'
      }
    }
  }

  /**
   * HOD approves a module request for an existing user_access record
   * Sends HOD signature, selected modules, and request intent (use/revoke)
   */
  async hodApproveModuleRequest(requestId, payload) {
    try {
      console.log(
        '🔄 HOD approving module request via BothServiceFormController:',
        requestId,
        payload
      )

      const fd = new FormData()

      // Required HOD fields
      fd.append('hod_name', payload.hodName || '')
      fd.append('approved_date', payload.approvedDate || new Date().toISOString().slice(0, 10))
      // Send comments using both keys for maximum backend compatibility
      const hodComments = payload.hodComments || payload.comments || ''
      fd.append('comments', hodComments)
      fd.append('hod_comments', hodComments)

      if (payload.hodSignature) {
        fd.append('hod_signature', payload.hodSignature)
      } else {
        throw new Error('HOD signature file is required')
      }

      // Access rights (default to permanent if not provided)
      fd.append('access_type', payload.accessType || 'permanent')
      if (payload.accessType === 'temporary' && payload.temporaryUntil) {
        fd.append('temporary_until', payload.temporaryUntil)
      }

      // Module selections
      const wellsoft = Array.isArray(payload.selectedWellsoft) ? payload.selectedWellsoft : []
      const jeeva = Array.isArray(payload.selectedJeeva) ? payload.selectedJeeva : []

      wellsoft.forEach((m, i) => fd.append(`wellsoft_modules_selected[${i}]`, m))
      jeeva.forEach((m, i) => fd.append(`jeeva_modules_selected[${i}]`, m))

      // Requested for: use/revoke
      if (payload.moduleRequestedFor) {
        fd.append('module_requested_for', payload.moduleRequestedFor)
      }

      // POST to HOD approve endpoint under both-service-form
      const res = await apiClient.post(
        `/both-service-form/module-requests/${requestId}/hod-approve`,
        fd,
        { headers: { 'Content-Type': 'multipart/form-data' } }
      )

      if (res.data?.success) {
        console.log('✅ HOD approval saved successfully:', res.data)
        return { success: true, data: res.data.data, message: res.data.message }
      }
      throw new Error(res.data?.message || 'Failed to save HOD approval')
    } catch (error) {
      console.error('❌ Error in hodApproveModuleRequest:', error)
      return {
        success: false,
        error: error.response?.data?.message || error.message || 'Failed to save HOD approval',
        errors: error.response?.data?.errors || null
      }
    }
  }

  /**
   * Divisional Director approves a user access request
   * @param {number} requestId - ID of the request to approve
   * @param {Object} payload - Approval data including signature and details
   * @returns {Promise<Object>} Response with approval result
   */
  async divisionalDirectorApprove(requestId, payload) {
    try {
      console.log('🔄 Divisional Director approving request:', requestId, payload)

      const fd = new FormData()

      // Required Divisional Director fields
      fd.append('divisional_director_name', payload.divisionalDirectorName || '')
      fd.append('approved_date', payload.approvedDate || new Date().toISOString().slice(0, 10))
      fd.append('comments', payload.comments || '')

      if (payload.divisionalDirectorSignature) {
        fd.append('divisional_director_signature', payload.divisionalDirectorSignature)
      } else {
        throw new Error('Divisional Director signature file is required')
      }

      // POST to Divisional Director approve endpoint
      const res = await apiClient.post(
        `/both-service-form/module-requests/${requestId}/divisional-approve`,
        fd,
        { headers: { 'Content-Type': 'multipart/form-data' } }
      )

      if (res.data?.success) {
        console.log('✅ Divisional Director approval saved successfully:', res.data)
        return { success: true, data: res.data.data, message: res.data.message }
      }
      throw new Error(res.data?.message || 'Failed to save Divisional Director approval')
    } catch (error) {
      console.error('❌ Error in divisionalDirectorApprove:', error)
      return {
        success: false,
        error:
          error.response?.data?.message ||
          error.message ||
          'Failed to save Divisional Director approval',
        errors: error.response?.data?.errors || null
      }
    }
  }

  /**
   * Divisional Director rejects a user access request
   * @param {number} requestId - ID of the request to reject
   * @param {Object} payload - Rejection data including reason
   * @returns {Promise<Object>} Response with rejection result
   */
  async divisionalDirectorReject(requestId, payload) {
    try {
      console.log('🔄 Divisional Director rejecting request:', requestId, payload)

      const data = {
        divisional_director_name: payload.divisionalDirectorName || '',
        rejection_reason: payload.rejectionReason || '',
        rejection_date: payload.rejectionDate || new Date().toISOString().slice(0, 10)
      }

      // POST to Divisional Director reject endpoint
      const res = await apiClient.post(
        `/both-service-form/module-requests/${requestId}/divisional-reject`,
        data
      )

      if (res.data?.success) {
        console.log('✅ Divisional Director rejection saved successfully:', res.data)
        return { success: true, data: res.data.data, message: res.data.message }
      }
      throw new Error(res.data?.message || 'Failed to save Divisional Director rejection')
    } catch (error) {
      console.error('❌ Error in divisionalDirectorReject:', error)
      return {
        success: false,
        error:
          error.response?.data?.message ||
          error.message ||
          'Failed to save Divisional Director rejection',
        errors: error.response?.data?.errors || null
      }
    }
  }

  /**
   * ICT Director approves a user access request
   * @param {number} requestId - ID of the request to approve
   * @param {Object} payload - Approval data including signature and details
   * @returns {Promise<Object>} Response with approval result
   */
  async ictDirectorApprove(requestId, payload) {
    try {
      console.log('🔄 ICT Director approving request:', requestId, payload)

      const fd = new FormData()

      // Required ICT Director fields
      fd.append('ict_director_name', payload.ictDirectorName || '')
      fd.append('approved_date', payload.approvedDate || new Date().toISOString().slice(0, 10))
      fd.append('comments', payload.comments || '')

      if (payload.ictDirectorSignature) {
        fd.append('ict_director_signature', payload.ictDirectorSignature)
      } else {
        throw new Error('ICT Director signature file is required')
      }

      // POST to ICT Director approve endpoint
      const res = await apiClient.post(
        `/both-service-form/module-requests/${requestId}/ict-director-approve`,
        fd,
        { headers: { 'Content-Type': 'multipart/form-data' } }
      )

      if (res.data?.success) {
        console.log('✅ ICT Director approval saved successfully:', res.data)
        return { success: true, data: res.data.data, message: res.data.message }
      }
      throw new Error(res.data?.message || 'Failed to save ICT Director approval')
    } catch (error) {
      console.error('❌ Error in ictDirectorApprove:', error)
      return {
        success: false,
        error:
          error.response?.data?.message || error.message || 'Failed to save ICT Director approval',
        errors: error.response?.data?.errors || null
      }
    }
  }

  /**
   * ICT Director rejects a user access request
   * @param {number} requestId - ID of the request to reject
   * @param {Object} payload - Rejection data including reason
   * @returns {Promise<Object>} Response with rejection result
   */
  async ictDirectorReject(requestId, payload) {
    try {
      console.log('🔄 ICT Director rejecting request:', requestId, payload)

      const data = {
        ict_director_name: payload.ictDirectorName || '',
        rejection_reason: payload.rejectionReason || '',
        rejection_date: payload.rejectionDate || new Date().toISOString().slice(0, 10)
      }

      // POST to ICT Director reject endpoint
      const res = await apiClient.post(
        `/both-service-form/module-requests/${requestId}/ict-director-reject`,
        data
      )

      if (res.data?.success) {
        console.log('✅ ICT Director rejection saved successfully:', res.data)
        return { success: true, data: res.data.data, message: res.data.message }
      }
      throw new Error(res.data?.message || 'Failed to save ICT Director rejection')
    } catch (error) {
      console.error('❌ Error in ictDirectorReject:', error)
      return {
        success: false,
        error:
          error.response?.data?.message || error.message || 'Failed to save ICT Director rejection',
        errors: error.response?.data?.errors || null
      }
    }
  }

  /**
   * Head IT approves a user access request
   * @param {number} requestId - ID of the request to approve
   * @param {Object} payload - Approval data including signature and details
   * @returns {Promise<Object>} Response with approval result
   */
  async headItApprove(requestId, payload) {
    try {
      console.log('🔄 Head IT approving request:', requestId, payload)

      const fd = new FormData()

      // Required Head IT fields
      fd.append('head_it_name', payload.headItName || '')
      fd.append('approved_date', payload.approvedDate || new Date().toISOString().slice(0, 10))
      fd.append('comments', payload.comments || '')

      if (payload.headItSignature) {
        fd.append('head_it_signature', payload.headItSignature)
      } else {
        throw new Error('Head IT signature file is required')
      }

      // POST to Head IT approve endpoint
      const res = await apiClient.post(
        `/both-service-form/module-requests/${requestId}/head-of-it-approve`,
        fd,
        { headers: { 'Content-Type': 'multipart/form-data' } }
      )

      if (res.data?.success) {
        console.log('✅ Head IT approval saved successfully:', res.data)
        return { success: true, data: res.data.data, message: res.data.message }
      }
      throw new Error(res.data?.message || 'Failed to save Head IT approval')
    } catch (error) {
      console.error('❌ Error in headItApprove:', error)
      return {
        success: false,
        error: error.response?.data?.message || error.message || 'Failed to save Head IT approval',
        errors: error.response?.data?.errors || null
      }
    }
  }

  /**
   * Head IT rejects a user access request
   * @param {number} requestId - ID of the request to reject
   * @param {Object} payload - Rejection data including reason
   * @returns {Promise<Object>} Response with rejection result
   */
  async headItReject(requestId, payload) {
    try {
      console.log('🔄 Head IT rejecting request:', requestId, payload)

      const data = {
        head_it_name: payload.headItName || '',
        rejection_reason: payload.rejectionReason || '',
        rejection_date: payload.rejectionDate || new Date().toISOString().slice(0, 10)
      }

      // POST to Head IT reject endpoint
      const res = await apiClient.post(
        `/both-service-form/module-requests/${requestId}/head-it-reject`,
        data
      )

      if (res.data?.success) {
        console.log('✅ Head IT rejection saved successfully:', res.data)
        return { success: true, data: res.data.data, message: res.data.message }
      }
      throw new Error(res.data?.message || 'Failed to save Head IT rejection')
    } catch (error) {
      console.error('❌ Error in headItReject:', error)
      return {
        success: false,
        error: error.response?.data?.message || error.message || 'Failed to save Head IT rejection',
        errors: error.response?.data?.errors || null
      }
    }
  }

  /**
   * ICT Officer approves/implements a user access request
   * @param {number} requestId - ID of the request to implement
   * @param {Object} payload - Implementation data including signature and details
   * @returns {Promise<Object>} Response with implementation result
   */
  async ictOfficerApprove(requestId, payload) {
    try {
      console.log('🔄 ICT Officer implementing request:', requestId, payload)

      const fd = new FormData()

      // Required ICT Officer fields
      fd.append('ict_officer_name', payload.ict_officer_name || '')
      fd.append('approved_date', payload.ict_officer_date || new Date().toISOString().slice(0, 10))
      fd.append('comments', payload.ict_officer_comments || '')
      fd.append('status', payload.ict_officer_status || 'implemented')

      if (payload.ict_officer_signature) {
        fd.append('ict_officer_signature', payload.ict_officer_signature)
      } else {
        throw new Error('ICT Officer signature file is required')
      }

      // POST to ICT Officer approve endpoint
      const res = await apiClient.post(
        `/both-service-form/module-requests/${requestId}/ict-officer-approve`,
        fd,
        { headers: { 'Content-Type': 'multipart/form-data' } }
      )

      if (res.data?.success) {
        console.log('✅ ICT Officer implementation saved successfully:', res.data)
        return { success: true, data: res.data.data, message: res.data.message }
      }
      throw new Error(res.data?.message || 'Failed to save ICT Officer implementation')
    } catch (error) {
      console.error('❌ Error in ictOfficerApprove:', error)
      return {
        success: false,
        error:
          error.response?.data?.message ||
          error.message ||
          'Failed to save ICT Officer implementation',
        errors: error.response?.data?.errors || null
      }
    }
  }

  /**
   * Validate form data before submission
   * @param {Object} formData - Form data to validate
   * @returns {Object} Validation result
   */
  validateFormData(formData) {
    const errors = []

    // Check required personal information
    if (!formData.shared?.pfNumber || formData.shared.pfNumber.trim() === '') {
      errors.push('PF Number is required')
    }

    if (!formData.shared?.staffName || formData.shared.staffName.trim() === '') {
      errors.push('Staff Name is required')
    }

    if (!formData.shared?.department || formData.shared.department.trim() === '') {
      errors.push('Department is required')
    }

    if (!formData.shared?.phone || formData.shared.phone.trim() === '') {
      errors.push('Phone number is required')
    }

    // Check if signature is provided
    if (!formData.signature) {
      errors.push('Digital signature is required')
    }

    // Check service-specific validations
    const hasWellsoft = formData.selectedWellsoft && formData.selectedWellsoft.length > 0
    const hasJeeva = formData.selectedJeeva && formData.selectedJeeva.length > 0
    const hasInternet =
      formData.internetPurposes &&
      formData.internetPurposes.some((purpose) => purpose && purpose.trim())

    if (!hasWellsoft && !hasJeeva && !hasInternet) {
      errors.push(
        'Please select at least one service (Wellsoft modules, Jeeva modules, or Internet access)'
      )
    }

    // If Wellsoft is being requested (based on having Wellsoft modules selected), ensure at least 1 module
    if (hasWellsoft && formData.selectedWellsoft.length === 0) {
      errors.push('Please select at least 1 Wellsoft module')
    }

    // If Jeeva is being requested (based on having Jeeva modules selected), ensure at least 1 module
    if (hasJeeva && formData.selectedJeeva.length === 0) {
      errors.push('Please select at least 1 Jeeva module')
    }

    // Check module requested for if modules are selected
    if ((hasWellsoft || hasJeeva) && !formData.wellsoftRequestType) {
      errors.push('Please select whether you want to Use or Revoke access')
    }

    return {
      isValid: errors.length === 0,
      errors: errors
    }
  }
}

export default new BothServiceFormService()
