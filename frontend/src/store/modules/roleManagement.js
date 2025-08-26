import roleManagementService from '@/services/roleManagementService'

const state = {
  // Roles
  roles: [],
  rolesPagination: {},
  rolesLoading: false,
  roleStatistics: {},
  availablePermissions: {},

  // Users with roles
  usersWithRoles: [],
  usersWithRolesPagination: {},
  usersWithRolesLoading: false,
  userRoleStatistics: {},

  // Departments with HODs
  departmentsWithHods: [],
  departmentsWithHodsPagination: {},
  departmentsWithHodsLoading: false,
  eligibleHods: [],
  departmentHodStatistics: {},

  // UI state
  selectedRole: null,
  selectedUser: null,
  selectedDepartment: null,

  // Error handling
  error: null,
  successMessage: null
}

const mutations = {
  // Roles mutations
  SET_ROLES(state, { data, pagination }) {
    state.roles = data
    state.rolesPagination = pagination
  },

  SET_ROLES_LOADING(state, loading) {
    state.rolesLoading = loading
  },

  SET_ROLE_STATISTICS(state, statistics) {
    state.roleStatistics = statistics
  },

  SET_AVAILABLE_PERMISSIONS(state, permissions) {
    state.availablePermissions = permissions
  },

  ADD_ROLE(state, role) {
    state.roles.unshift(role)
  },

  UPDATE_ROLE(state, updatedRole) {
    const index = state.roles.findIndex((role) => role.id === updatedRole.id)
    if (index !== -1) {
      state.roles.splice(index, 1, updatedRole)
    }
  },

  REMOVE_ROLE(state, roleId) {
    state.roles = state.roles.filter((role) => role.id !== roleId)
  },

  SET_SELECTED_ROLE(state, role) {
    state.selectedRole = role
  },

  // Users with roles mutations
  SET_USERS_WITH_ROLES(state, { data, pagination }) {
    state.usersWithRoles = data
    state.usersWithRolesPagination = pagination
  },

  SET_USERS_WITH_ROLES_LOADING(state, loading) {
    state.usersWithRolesLoading = loading
  },

  SET_USER_ROLE_STATISTICS(state, statistics) {
    state.userRoleStatistics = statistics
  },

  UPDATE_USER_ROLES(state, { userId, roles }) {
    const user = state.usersWithRoles.find((u) => u.id === userId)
    if (user) {
      user.roles = roles
    }
  },

  SET_SELECTED_USER(state, user) {
    state.selectedUser = user
  },

  // Departments with HODs mutations
  SET_DEPARTMENTS_WITH_HODS(state, { data, pagination }) {
    state.departmentsWithHods = data
    state.departmentsWithHodsPagination = pagination
  },

  SET_DEPARTMENTS_WITH_HODS_LOADING(state, loading) {
    state.departmentsWithHodsLoading = loading
  },

  SET_ELIGIBLE_HODS(state, hods) {
    state.eligibleHods = hods
  },

  SET_DEPARTMENT_HOD_STATISTICS(state, statistics) {
    state.departmentHodStatistics = statistics
  },

  UPDATE_DEPARTMENT_HOD(state, { departmentId, hod }) {
    const department = state.departmentsWithHods.find(
      (d) => d.id === departmentId
    )
    if (department) {
      department.hod = hod
    }
  },

  SET_SELECTED_DEPARTMENT(state, department) {
    state.selectedDepartment = department
  },

  // General mutations
  SET_ERROR(state, error) {
    state.error = error
  },

  SET_SUCCESS_MESSAGE(state, message) {
    state.successMessage = message
  },

  CLEAR_MESSAGES(state) {
    state.error = null
    state.successMessage = null
  }
}

const actions = {
  // Role actions
  async fetchRoles({ commit }, params = {}) {
    commit('SET_ROLES_LOADING', true)
    commit('CLEAR_MESSAGES')

    try {
      const response = await roleManagementService.getRoles(params)

      if (response.success) {
        commit('SET_ROLES', {
          data: response.data.data,
          pagination: {
            current_page: response.data.current_page,
            last_page: response.data.last_page,
            per_page: response.data.per_page,
            total: response.data.total
          }
        })
      } else {
        commit('SET_ERROR', response.message)
      }
    } catch (error) {
      commit('SET_ERROR', error.message || 'Failed to fetch roles')
    } finally {
      commit('SET_ROLES_LOADING', false)
    }
  },

  async createRole({ commit }, roleData) {
    commit('CLEAR_MESSAGES')

    try {
      const response = await roleManagementService.createRole(roleData)

      if (response.success) {
        commit('ADD_ROLE', response.data)
        commit('SET_SUCCESS_MESSAGE', response.message)
        return { success: true, data: response.data }
      } else {
        commit('SET_ERROR', response.message)
        return { success: false, errors: response.errors }
      }
    } catch (error) {
      const errorMessage = error.message || 'Failed to create role'
      commit('SET_ERROR', errorMessage)
      return { success: false, errors: error.errors || {} }
    }
  },

  async updateRole({ commit }, { roleId, roleData }) {
    commit('CLEAR_MESSAGES')

    try {
      const response = await roleManagementService.updateRole(roleId, roleData)

      if (response.success) {
        commit('UPDATE_ROLE', response.data)
        commit('SET_SUCCESS_MESSAGE', response.message)
        return { success: true, data: response.data }
      } else {
        commit('SET_ERROR', response.message)
        return { success: false, errors: response.errors }
      }
    } catch (error) {
      const errorMessage = error.message || 'Failed to update role'
      commit('SET_ERROR', errorMessage)
      return { success: false, errors: error.errors || {} }
    }
  },

  async deleteRole({ commit }, roleId) {
    commit('CLEAR_MESSAGES')

    try {
      const response = await roleManagementService.deleteRole(roleId)

      if (response.success) {
        commit('REMOVE_ROLE', roleId)
        commit('SET_SUCCESS_MESSAGE', response.message)
        return { success: true }
      } else {
        commit('SET_ERROR', response.message)
        return { success: false }
      }
    } catch (error) {
      const errorMessage = error.message || 'Failed to delete role'
      commit('SET_ERROR', errorMessage)
      return { success: false }
    }
  },

  async fetchRoleStatistics({ commit }) {
    try {
      const response = await roleManagementService.getRoleStatistics()

      if (response.success) {
        commit('SET_ROLE_STATISTICS', response.data)
      }
    } catch (error) {
      console.error('Failed to fetch role statistics:', error)
    }
  },

  async fetchAvailablePermissions({ commit }) {
    try {
      const response = await roleManagementService.getAvailablePermissions()

      if (response.success) {
        commit('SET_AVAILABLE_PERMISSIONS', response.data)
      }
    } catch (error) {
      console.error('Failed to fetch available permissions:', error)
    }
  },

  // User role actions
  async fetchUsersWithRoles({ commit }, params = {}) {
    commit('SET_USERS_WITH_ROLES_LOADING', true)
    commit('CLEAR_MESSAGES')

    try {
      const response = await roleManagementService.getUsersWithRoles(params)

      if (response.success) {
        commit('SET_USERS_WITH_ROLES', {
          data: response.data.data,
          pagination: {
            current_page: response.data.current_page,
            last_page: response.data.last_page,
            per_page: response.data.per_page,
            total: response.data.total
          }
        })
      } else {
        commit('SET_ERROR', response.message)
      }
    } catch (error) {
      commit('SET_ERROR', error.message || 'Failed to fetch users')
    } finally {
      commit('SET_USERS_WITH_ROLES_LOADING', false)
    }
  },

  async assignRolesToUser({ commit }, { userId, roleIds }) {
    commit('CLEAR_MESSAGES')

    try {
      const response = await roleManagementService.assignRolesToUser(
        userId,
        roleIds
      )

      if (response.success) {
        commit('UPDATE_USER_ROLES', {
          userId,
          roles: response.data.user.roles
        })
        commit('SET_SUCCESS_MESSAGE', response.message)
        return { success: true, data: response.data }
      } else {
        commit('SET_ERROR', response.message)
        return { success: false, errors: response.errors }
      }
    } catch (error) {
      const errorMessage = error.message || 'Failed to assign roles'
      commit('SET_ERROR', errorMessage)
      return { success: false, errors: error.errors || {} }
    }
  },

  async removeRoleFromUser({ commit }, { userId, roleId }) {
    commit('CLEAR_MESSAGES')

    try {
      const response = await roleManagementService.removeRoleFromUser(
        userId,
        roleId
      )

      if (response.success) {
        // Refresh user data to get updated roles
        const user = state.usersWithRoles.find((u) => u.id === userId)
        if (user) {
          user.roles = user.roles.filter((role) => role.id !== roleId)
          commit('UPDATE_USER_ROLES', {
            userId,
            roles: user.roles
          })
        }
        commit('SET_SUCCESS_MESSAGE', response.message)
        return { success: true }
      } else {
        commit('SET_ERROR', response.message)
        return { success: false }
      }
    } catch (error) {
      const errorMessage = error.message || 'Failed to remove role'
      commit('SET_ERROR', errorMessage)
      return { success: false }
    }
  },

  async fetchUserRoleStatistics({ commit }) {
    try {
      const response = await roleManagementService.getUserRoleStatistics()

      if (response.success) {
        commit('SET_USER_ROLE_STATISTICS', response.data)
      }
    } catch (error) {
      console.error('Failed to fetch user role statistics:', error)
    }
  },

  // Department HOD actions
  async fetchDepartmentsWithHods({ commit }, params = {}) {
    commit('SET_DEPARTMENTS_WITH_HODS_LOADING', true)
    commit('CLEAR_MESSAGES')

    try {
      const response = await roleManagementService.getDepartmentsWithHods(
        params
      )

      if (response.success) {
        commit('SET_DEPARTMENTS_WITH_HODS', {
          data: response.data.data,
          pagination: {
            current_page: response.data.current_page,
            last_page: response.data.last_page,
            per_page: response.data.per_page,
            total: response.data.total
          }
        })
      } else {
        commit('SET_ERROR', response.message)
      }
    } catch (error) {
      commit('SET_ERROR', error.message || 'Failed to fetch departments')
    } finally {
      commit('SET_DEPARTMENTS_WITH_HODS_LOADING', false)
    }
  },

  async fetchEligibleHods({ commit }) {
    try {
      const response = await roleManagementService.getEligibleHods()

      if (response.success) {
        commit('SET_ELIGIBLE_HODS', response.data)
      }
    } catch (error) {
      console.error('Failed to fetch eligible HODs:', error)
    }
  },

  async assignHodToDepartment({ commit }, { departmentId, hodUserId }) {
    commit('CLEAR_MESSAGES')

    try {
      const response = await roleManagementService.assignHodToDepartment(
        departmentId,
        hodUserId
      )

      if (response.success) {
        commit('UPDATE_DEPARTMENT_HOD', {
          departmentId,
          hod: response.data.head_of_department
        })
        commit('SET_SUCCESS_MESSAGE', response.message)
        return { success: true, data: response.data }
      } else {
        commit('SET_ERROR', response.message)
        return { success: false, errors: response.errors }
      }
    } catch (error) {
      const errorMessage = error.message || 'Failed to assign HOD'
      commit('SET_ERROR', errorMessage)
      return { success: false, errors: error.errors || {} }
    }
  },

  async removeHodFromDepartment({ commit }, departmentId) {
    commit('CLEAR_MESSAGES')

    try {
      const response = await roleManagementService.removeHodFromDepartment(
        departmentId
      )

      if (response.success) {
        commit('UPDATE_DEPARTMENT_HOD', {
          departmentId,
          hod: null
        })
        commit('SET_SUCCESS_MESSAGE', response.message)
        return { success: true }
      } else {
        commit('SET_ERROR', response.message)
        return { success: false }
      }
    } catch (error) {
      const errorMessage = error.message || 'Failed to remove HOD'
      commit('SET_ERROR', errorMessage)
      return { success: false }
    }
  },

  async fetchDepartmentHodStatistics({ commit }) {
    try {
      const response = await roleManagementService.getDepartmentHodStatistics()

      if (response.success) {
        commit('SET_DEPARTMENT_HOD_STATISTICS', response.data)
      }
    } catch (error) {
      console.error('Failed to fetch department HOD statistics:', error)
    }
  },

  // UI actions
  setSelectedRole({ commit }, role) {
    commit('SET_SELECTED_ROLE', role)
  },

  setSelectedUser({ commit }, user) {
    commit('SET_SELECTED_USER', user)
  },

  setSelectedDepartment({ commit }, department) {
    commit('SET_SELECTED_DEPARTMENT', department)
  },

  clearMessages({ commit }) {
    commit('CLEAR_MESSAGES')
  }
}

const getters = {
  // Role getters
  allRoles: (state) => state.roles,
  rolesLoading: (state) => state.rolesLoading,
  rolesPagination: (state) => state.rolesPagination,
  roleStatistics: (state) => state.roleStatistics,
  availablePermissions: (state) => state.availablePermissions,
  selectedRole: (state) => state.selectedRole,

  // User role getters
  allUsersWithRoles: (state) => state.usersWithRoles,
  usersWithRolesLoading: (state) => state.usersWithRolesLoading,
  usersWithRolesPagination: (state) => state.usersWithRolesPagination,
  userRoleStatistics: (state) => state.userRoleStatistics,
  selectedUser: (state) => state.selectedUser,

  // Department HOD getters
  allDepartmentsWithHods: (state) => state.departmentsWithHods,
  departmentsWithHodsLoading: (state) => state.departmentsWithHodsLoading,
  departmentsWithHodsPagination: (state) => state.departmentsWithHodsPagination,
  eligibleHods: (state) => state.eligibleHods,
  departmentHodStatistics: (state) => state.departmentHodStatistics,
  selectedDepartment: (state) => state.selectedDepartment,

  // General getters
  error: (state) => state.error,
  successMessage: (state) => state.successMessage,
  hasError: (state) => !!state.error,
  hasSuccessMessage: (state) => !!state.successMessage,

  // Computed getters
  systemRoles: (state) => state.roles.filter((role) => role.is_system_role),
  customRoles: (state) => state.roles.filter((role) => !role.is_system_role),
  deletableRoles: (state) => state.roles.filter((role) => role.is_deletable),

  usersWithoutRoles: (state) =>
    state.usersWithRoles.filter((user) => user.roles.length === 0),
  usersWithMultipleRoles: (state) =>
    state.usersWithRoles.filter((user) => user.roles.length > 1),

  departmentsWithoutHod: (state) =>
    state.departmentsWithHods.filter((dept) => !dept.hod),
  departmentsWithHod: (state) =>
    state.departmentsWithHods.filter((dept) => dept.hod),

  availableHods: (state) =>
    state.eligibleHods.filter((hod) => hod.is_available)
}

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters
}
