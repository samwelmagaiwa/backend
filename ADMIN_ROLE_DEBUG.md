# 🔍 **Admin Role Debug Guide**

## 🚨 **Current Issue**
- **User**: "System Administrator" 
- **Expected Role**: `admin`
- **Actual Role**: `staff`
- **Status**: Authenticated but wrong role assignment

## 🔧 **Debugging Steps**

### **Step 1: Check Browser Console**
Open browser developer tools and look for these messages:

**Expected to see**:
```
🔍 Backend role received: undefined
🔍 User roles array: [{name: "admin", ...}]
🔄 role_name is undefined, extracting from roles array...
✅ Found role in roles array: admin
🎯 Successfully mapped to frontend role: admin
```

**If you see**:
```
🔍 Backend role received: undefined
🔍 User roles array: []
❌ Backend returned invalid role: undefined
🔄 Defaulting to "staff" role as fallback
```

This means the user has no roles assigned in the database.

### **Step 2: Check localStorage Data**
Run this in browser console:
```javascript
// Check stored user data
const userData = localStorage.getItem('user_data')
if (userData) {
  const user = JSON.parse(userData)
  console.log('Stored user data:', {
    name: user.name,
    role: user.role,
    role_name: user.role_name,
    roles: user.roles,
    permissions: user.permissions
  })
}
```

### **Step 3: Force Auth Refresh**
Run this in browser console to force refresh with new logic:
```javascript
// Force refresh auth state
import('./src/utils/auth.js').then(async (authModule) => {
  const auth = authModule.default
  await auth.initializeAuth(true)
  console.log('New role after refresh:', auth.userRole)
})
```

### **Step 4: Check Backend Response**
Look for these console messages during background verification:
```
🔄 Background verification - Raw backend user: {...}
🔍 Background - user.role: undefined
🔍 Background - user.role_name: undefined  
🔍 Background - user.roles: [{name: "admin", ...}]
🔍 Background - user.permissions: [...]
```

## 🛠️ **Possible Solutions**

### **Solution 1: Database Issue**
If the user has no roles in the database:

1. **Check Database**: Look at `role_user` table
2. **Assign Role**: Add admin role to the user
3. **Verify**: Check that user has admin role assigned

### **Solution 2: Backend Issue**
If `getPrimaryRoleName()` returns null:

1. **Check**: User model `getPrimaryRole()` method
2. **Verify**: Roles relationship is working
3. **Test**: Backend API returns correct role data

### **Solution 3: Frontend Caching**
If old data is cached:

1. **Clear**: localStorage data
2. **Refresh**: Page completely
3. **Re-login**: Fresh authentication

### **Solution 4: Role Mapping Issue**
If role exists but mapping fails:

1. **Check**: Role name matches exactly
2. **Verify**: Role mapping constants
3. **Debug**: Console logs during mapping

## 🧪 **Quick Tests**

### **Test 1: Clear Cache and Re-login**
```javascript
// Clear all auth data
localStorage.removeItem('auth_token')
localStorage.removeItem('user_data') 
localStorage.removeItem('session_data')
// Then refresh page and login again
```

### **Test 2: Check Raw Backend Data**
```javascript
// Check what backend actually returns
fetch('/api/current-user', {
  headers: {
    'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
  }
})
.then(r => r.json())
.then(data => console.log('Backend user data:', data))
```

### **Test 3: Manual Role Assignment**
```javascript
// Temporarily set correct role
const userData = JSON.parse(localStorage.getItem('user_data'))
userData.role = 'admin'
localStorage.setItem('user_data', JSON.stringify(userData))
// Then refresh page
```

## 📋 **Expected Database Structure**

### **Users Table**
```sql
SELECT id, name, email, role_id FROM users WHERE name = 'System Administrator';
```

### **Role_User Pivot Table**
```sql
SELECT * FROM role_user WHERE user_id = [user_id];
```

### **Roles Table**
```sql
SELECT * FROM roles WHERE name = 'admin';
```

## 🎯 **Expected Fix Result**

After applying the fix, you should see:

1. ✅ **Console**: Role extracted from roles array
2. ✅ **Debug Info**: User Role shows "admin" 
3. ✅ **Navigation**: Redirected to `/admin-dashboard`
4. ✅ **Features**: Admin features accessible

## 🚨 **If Issue Persists**

If the user still shows as "staff" after the fix:

1. **Database Check**: Verify user has admin role in `role_user` table
2. **Backend Test**: Test `/api/current-user` endpoint directly
3. **Clear Cache**: Complete browser cache clear
4. **Re-assign Role**: Use admin panel to re-assign admin role

The most likely cause is that the "System Administrator" user doesn't have the "admin" role properly assigned in the database `role_user` pivot table.