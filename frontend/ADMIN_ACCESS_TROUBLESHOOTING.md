# Admin Access Troubleshooting Guide

## "Failed to load data" Error Solutions

If you're seeing "Failed to load data" messages in admin components, follow this troubleshooting guide:

### 🔍 **Step 1: Use the Diagnostic Tool**

Visit `/admin-access-diagnostic` in your browser to run automated tests and identify the exact issue.

### 🚨 **Common Issues and Solutions**

#### **Issue 1: "Access denied. Admin privileges required"**
**Cause:** Your user account doesn't have the 'admin' role assigned.

**Solution:**
1. Contact a system administrator
2. Ask them to assign you the 'admin' role using the role management system
3. Or use the database to assign the role manually:

```sql
-- Find your user ID
SELECT id, name, email FROM users WHERE email = 'your-email@example.com';

-- Find the admin role ID
SELECT id, name FROM roles WHERE name = 'admin';

-- Assign admin role to your user
INSERT INTO role_user (user_id, role_id, assigned_at, assigned_by, created_at, updated_at) 
VALUES (YOUR_USER_ID, ADMIN_ROLE_ID, NOW(), 1, NOW(), NOW());
```

#### **Issue 2: "Authentication required"**
**Cause:** You're not logged in or your session has expired.

**Solution:**
1. Log out completely
2. Clear your browser's local storage
3. Log back in with valid credentials

#### **Issue 3: Network/Connection Errors**
**Cause:** The Laravel backend is not running or not accessible.

**Solution:**
1. Check that Laravel is running: `php artisan serve`
2. Verify the API URL in your `.env` file:
   ```
   VUE_APP_API_URL=http://127.0.0.1:8000/api
   ```
3. Test the backend directly: visit `http://127.0.0.1:8000/api/health`

#### **Issue 4: Token Issues**
**Cause:** Invalid or expired authentication token.

**Solution:**
1. Clear local storage in browser developer tools
2. Log out and log back in
3. Check browser console for token-related errors

### 🛠️ **Manual Database Role Assignment**

If you need to manually assign admin role to a user:

```sql
-- 1. Connect to your database
mysql -u root -p your_database_name

-- 2. Check existing users
SELECT id, name, email FROM users;

-- 3. Check existing roles
SELECT id, name FROM roles;

-- 4. Check current role assignments
SELECT u.name, u.email, r.name as role_name 
FROM users u 
JOIN role_user ru ON u.id = ru.user_id 
JOIN roles r ON ru.role_id = r.id;

-- 5. Assign admin role to a user (replace IDs with actual values)
INSERT INTO role_user (user_id, role_id, assigned_at, assigned_by, created_at, updated_at) 
VALUES (1, 1, NOW(), 1, NOW(), NOW());
```

### 🔧 **Quick Fixes**

#### **Clear Browser Data**
```javascript
// Run in browser console
localStorage.clear();
sessionStorage.clear();
location.reload();
```

#### **Reset Authentication**
```javascript
// Run in browser console
localStorage.removeItem('auth_token');
localStorage.removeItem('user_data');
localStorage.removeItem('session_data');
location.href = '/';
```

### 📋 **Verification Steps**

After applying fixes, verify access by:

1. **Visit the diagnostic tool:** `/admin-access-diagnostic`
2. **Run all tests** to ensure endpoints are accessible
3. **Check user roles** are properly assigned
4. **Test admin components** like User Role Assignment

### 🆘 **Still Having Issues?**

If the problem persists:

1. **Check browser console** for JavaScript errors
2. **Check Laravel logs** in `storage/logs/laravel.log`
3. **Verify database connections** are working
4. **Check middleware configuration** in Laravel
5. **Ensure all migrations** have been run: `php artisan migrate`

### 📞 **Getting Help**

When reporting issues, include:

- Results from the diagnostic tool (`/admin-access-diagnostic`)
- Browser console errors
- Laravel log entries
- Your user's role assignments
- Steps you've already tried

### 🔐 **Security Notes**

- Only assign admin roles to trusted users
- Regularly audit user role assignments
- Use the diagnostic tool only in development/testing environments
- Remove diagnostic routes in production