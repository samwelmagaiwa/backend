# 🔍 Blank Page Solution Guide

## 🚨 Current Issue
The `/admin/onboarding-reset` route shows a blank page.

## 🧪 Testing Routes Available

| Route | Purpose | Auth Required |
|-------|---------|---------------|
| `/admin/onboarding-reset` | Debug component | Yes (Admin) |
| `/admin/onboarding-reset-original` | Original component | Yes (Admin) |
| `/admin/onboarding-reset-noauth` | Original without auth | No |

## 📋 Step-by-Step Diagnosis

### Step 1: Test Basic Functionality
1. **Navigate to** `/admin/onboarding-reset`
2. **Expected:** Debug page with blue background and test buttons
3. **If blank:** Route guards or authentication issue
4. **If working:** Component loading issue

### Step 2: Test Authentication
1. **On debug page, click "Test Auth" button**
2. **Check console** for auth-related errors
3. **Verify** you're logged in as admin user

### Step 3: Test Without Authentication
1. **Navigate to** `/admin/onboarding-reset-noauth`
2. **This bypasses** all authentication checks
3. **If works:** Authentication/route guard issue
4. **If blank:** Component or dependency issue

### Step 4: Test Original Component
1. **On debug page, click "Load Original Component"**
2. **Or navigate to** `/admin/onboarding-reset-original`
3. **Check console** for specific errors

## 🔧 Common Solutions

### Solution 1: Authentication Issues
```javascript
// Check in browser console:
localStorage.getItem('auth_token')
localStorage.getItem('user_data')

// If missing, login again
```

### Solution 2: Clear Browser Cache
```
1. Press Ctrl+F5 (Windows) or Cmd+Shift+R (Mac)
2. Or right-click refresh → "Empty Cache and Hard Reload"
```

### Solution 3: Check Console Errors
```
1. Press F12 → Console tab
2. Look for red error messages
3. Common errors:
   - 404 (file not found)
   - Import/export errors
   - Authentication errors
   - Chunk loading failed
```

### Solution 4: Reset Authentication
```javascript
// In browser console:
localStorage.clear()
sessionStorage.clear()
// Then login again
```

## 🐛 Debugging Commands

### Check Authentication State
```javascript
// Run in browser console
console.log('Auth Token:', localStorage.getItem('auth_token'))
console.log('User Data:', JSON.parse(localStorage.getItem('user_data') || '{}'))
```

### Test API Connection
```javascript
// Run in browser console
fetch('/api/admin/users?page=1&per_page=5', {
  headers: {
    'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
    'Content-Type': 'application/json'
  }
}).then(r => r.json()).then(console.log).catch(console.error)
```

### Check Vue App Status
```javascript
// Run in browser console
console.log('App element:', document.getElementById('app'))
console.log('App content length:', document.getElementById('app').innerHTML.length)
```

## 📊 Diagnostic Checklist

- [ ] Debug page loads at `/admin/onboarding-reset`
- [ ] No errors in browser console
- [ ] Auth token exists in localStorage
- [ ] User is logged in as admin
- [ ] Backend server is running
- [ ] No network request failures
- [ ] Browser cache cleared

## 🚀 Quick Fixes

### Fix 1: Hard Refresh
```
Ctrl+F5 (Windows) or Cmd+Shift+R (Mac)
```

### Fix 2: Login Again
```
1. Go to /login
2. Login with admin credentials
3. Try the route again
```

### Fix 3: Check Backend
```bash
# Ensure Laravel is running
cd backend
php artisan serve
```

### Fix 4: Browser Issues
```
1. Try incognito/private mode
2. Disable browser extensions
3. Try different browser
```

## 📞 Next Steps

1. **Start with debug page** at `/admin/onboarding-reset`
2. **Use the test buttons** to identify the issue
3. **Check browser console** for specific errors
4. **Try the no-auth route** if debug page works
5. **Report specific error messages** found

## 🔍 What Each Test Route Does

### Debug Route (`/admin/onboarding-reset`)
- Tests basic Vue functionality
- Tests authentication
- Tests navigation
- Provides detailed diagnostics

### Original Route (`/admin/onboarding-reset-original`)
- Loads the full original component
- Requires admin authentication
- Shows any component-specific errors

### No-Auth Route (`/admin/onboarding-reset-noauth`)
- Bypasses authentication completely
- Tests if route guards are the issue
- Loads original component without restrictions

---

**Start Here:** Navigate to `/admin/onboarding-reset` and use the debug tools to identify the specific issue.