/**
 * Debug Console Script
 * Copy and paste this into browser console to debug auth issues
 */

console.log('🔍 === ADMIN ROLE DEBUG ===')

// Check localStorage
const token = localStorage.getItem('auth_token')
const userData = localStorage.getItem('user_data')

console.log('📦 Storage Check:')
console.log('  Token:', token ? 'Present' : 'Missing')
console.log('  User Data:', userData ? 'Present' : 'Missing')

if (userData) {
  try {
    const user = JSON.parse(userData)
    console.log('👤 Current User Data:')
    console.log('  Name:', user.name)
    console.log('  Role:', user.role)
    console.log('  Role Name:', user.role_name)
    console.log('  Roles Array:', user.roles)
    console.log('  Permissions:', user.permissions?.length || 0, 'permissions')
    
    // Check if this looks like an admin
    const hasAdminPermissions = user.permissions?.includes('manage_users') || 
                               user.permissions?.includes('system_settings')
    console.log('  Has Admin Permissions:', hasAdminPermissions)
    
    if (user.name === 'System Administrator' && user.role !== 'admin') {
      console.warn('⚠️ ISSUE FOUND: System Administrator should have admin role!')
      console.log('🔧 Checking roles array for admin role...')
      
      if (user.roles && user.roles.length > 0) {
        const adminRole = user.roles.find(r => 
          (typeof r === 'object' && r.name === 'admin') || 
          (typeof r === 'string' && r === 'admin')
        )
        
        if (adminRole) {
          console.log('✅ Admin role found in roles array:', adminRole)
          console.log('🔧 Role mapping should extract this automatically')
        } else {
          console.error('❌ No admin role found in roles array')
          console.log('📋 Available roles:', user.roles)
        }
      } else {
        console.error('❌ No roles array or empty roles array')
      }
    }
    
  } catch (e) {
    console.error('❌ Failed to parse user data:', e)
  }
}

// Test backend API
if (token) {
  console.log('🌐 Testing backend API...')
  fetch('/api/current-user', {
    headers: {
      'Authorization': `Bearer ${token}`,
      'Accept': 'application/json'
    }
  })
  .then(response => response.json())
  .then(data => {
    console.log('📡 Backend Response:')
    if (data.success) {
      const user = data.data
      console.log('  Name:', user.name)
      console.log('  Role Name:', user.role_name)
      console.log('  Roles:', user.roles)
      console.log('  Permissions:', user.permissions?.length || 0, 'permissions')
      
      // Check if backend has correct data
      if (user.name === 'System Administrator') {
        if (!user.role_name && user.roles?.length > 0) {
          console.log('🔧 Backend issue: role_name is null but roles exist')
          console.log('✅ Frontend fix should handle this')
        } else if (user.role_name === 'admin') {
          console.log('✅ Backend has correct admin role')
        } else {
          console.warn('⚠️ Backend role issue:', user.role_name)
        }
      }
    } else {
      console.error('❌ Backend API error:', data.message)
    }
  })
  .catch(error => {
    console.error('❌ API request failed:', error)
  })
}

// Provide fix function
window.fixAdminRole = function() {
  console.log('🔧 Attempting to fix admin role...')
  
  const userData = localStorage.getItem('user_data')
  if (userData) {
    try {
      const user = JSON.parse(userData)
      
      // Check if user should be admin
      if (user.name === 'System Administrator' || 
          user.permissions?.includes('manage_users') ||
          user.permissions?.includes('system_settings')) {
        
        console.log('🔄 Setting role to admin...')
        user.role = 'admin'
        localStorage.setItem('user_data', JSON.stringify(user))
        
        console.log('✅ Role updated to admin')
        console.log('🔄 Please refresh the page to see changes')
        
        return true
      } else {
        console.warn('⚠️ User does not appear to be an admin')
        return false
      }
    } catch (e) {
      console.error('❌ Failed to update role:', e)
      return false
    }
  }
  
  console.error('❌ No user data found')
  return false
}

console.log('🔍 === DEBUG COMPLETE ===')
console.log('💡 Run fixAdminRole() to temporarily fix the role')
console.log('💡 Or refresh the page to apply the new role mapping logic')