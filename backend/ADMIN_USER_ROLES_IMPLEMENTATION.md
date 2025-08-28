# Admin/User-Roles Backend Implementation

## Overview
This document outlines the comprehensive implementation of the admin/user-roles system using the new many-to-many role system in Laravel.

## 🔄 System Migration: Old → New

### Old System (Deprecated)
- Single role per user: `$user->role_id`
- Direct relationship: `$user->role()`
- Limited flexibility

### New System (Implemented)
- Many-to-many roles: `$user->roles()`
- Flexible role assignments
- Role history tracking
- Permission-based access control

## 📁 Files Created/Modified

### 1. Models Updated
- **`app/Models/User.php`** - Completely rewritten to use new role system
  - New methods: `hasRole()`, `hasAnyRole()`, `hasAdminPrivileges()`, `getDisplayRoleNames()`
  - Removed old role system dependencies
  - Added scopes for filtering users

### 2. Controllers Created
- **`app/Http/Controllers/Api/v1/AdminUserController.php`** - Comprehensive user management
  - Full CRUD operations for users
  - Role assignment during user creation
  - User status management (active/inactive)
  - Statistics and reporting

- **`app/Http/Controllers/Api/v1/TestController.php`** - Testing endpoints
  - Role system verification
  - Available roles listing

### 3. Controllers Updated
- **`app/Http/Controllers/Api/v1/AdminController.php`** - Updated to use new role system
- **`app/Http/Middleware/RoleMiddleware.php`** - Updated to use many-to-many roles

### 4. Request Validation
- **`app/Http/Requests/UserRequest.php`** - Comprehensive user validation
  - Creation and update validation
  - Role assignment validation
  - Security checks for privilege escalation

### 5. Database Migrations
- **`2025_01_27_230000_add_is_active_to_users_table.php`** - Added user status field
- **`2025_01_27_240000_ensure_all_users_have_roles.php`** - Migration helper

### 6. Seeders Updated
- **`database/seeders/RoleManagementSeeder.php`** - Enhanced role setup
  - All hospital roles defined
  - Automatic user migration from old to new system

### 7. Routes Updated
- **`routes/api.php`** - Added comprehensive admin user management routes

## 🛠️ API Endpoints

### User Management Endpoints

#### Get Users List
```
GET /api/admin/user-management/
```
**Parameters:**
- `search` - Search by name, email, PF number
- `role` - Filter by role name
- `status` - Filter by active/inactive/all
- `sort_by` - Sort by name, email, created_at
- `sort_order` - asc/desc
- `per_page` - Pagination limit

**Response:**
```json
{
  "success": true,
  "data": {
    "users": [
      {
        "id": 1,
        "name": "John Doe",
        "email": "john@hospital.go.tz",
        "roles": [
          {
            "id": 2,
            "name": "staff",
            "display_name": "Staff"
          }
        ],
        "display_roles": "Staff",
        "is_active": true,
        "created_at": "2025-01-27T10:00:00Z"
      }
    ],
    "pagination": {
      "current_page": 1,
      "total": 50
    }
  }
}
```

#### Create User
```
POST /api/admin/user-management/
```
**Body:**
```json
{
  "name": "Jane Smith",
  "email": "jane@hospital.go.tz",
  "pf_number": "PF001234",
  "phone": "+255123456789",
  "password": "password123",
  "password_confirmation": "password123",
  "role_ids": [3, 4],
  "is_active": true
}
```

#### Update User
```
PUT /api/admin/user-management/{user_id}
```

#### Delete User
```
DELETE /api/admin/user-management/{user_id}
```

#### Toggle User Status
```
POST /api/admin/user-management/{user_id}/toggle-status
```

#### Get Available Roles
```
GET /api/admin/user-management/roles
```

#### Get User Statistics
```
GET /api/admin/user-management/statistics
```

### Role Management Endpoints (Existing)
```
GET    /api/roles/                    # List all roles
POST   /api/roles/                    # Create role
GET    /api/roles/{role}              # Get role details
PUT    /api/roles/{role}              # Update role
DELETE /api/roles/{role}              # Delete role
```

### User Role Assignment Endpoints (Existing)
```
GET    /api/user-roles/               # List users with roles
POST   /api/user-roles/{user}/assign  # Assign roles to user
DELETE /api/user-roles/{user}/roles/{role} # Remove role from user
GET    /api/user-roles/{user}/history # Get role change history
```

## 🔐 Security Features

### 1. Permission System
- Role-based permissions stored as JSON arrays
- Permission checking: `$user->hasPermission('permission_name')`
- Hierarchical permission inheritance

### 2. Admin Protection
- Super admin users cannot be deleted or deactivated
- Users cannot modify their own roles
- Privilege escalation prevention

### 3. Audit Trail
- All role changes logged in `role_change_logs` table
- Admin actions logged with user context
- Change history accessible via API

## 🎯 Role Definitions

### System Roles
1. **super_admin** - Unrestricted access
2. **admin** - Full system administration
3. **divisional_director** - Approval authority
4. **head_of_department** - Departmental oversight
5. **ict_director** - Technical oversight
6. **dict** - Director of ICT
7. **ict_officer** - Technical support
8. **staff** - Regular hospital staff

### Role Permissions
Each role has specific permissions:
- `create_users`, `edit_users`, `delete_users`, `view_users`
- `create_roles`, `edit_roles`, `delete_roles`, `assign_roles`
- `view_all_requests`, `approve_requests`, `reject_requests`
- `system_settings`, `audit_logs`, `backup_restore`

## 🔄 Migration Strategy

### Automatic Migration
The system automatically migrates users from the old single-role system to the new many-to-many system:

1. **RoleManagementSeeder** runs migration
2. Users with `role_id` get that role assigned in new system
3. Users without roles get default 'staff' role
4. Old `role_id` field preserved for backward compatibility

### Manual Migration
Run the seeder manually:
```bash
php artisan db:seed --class=RoleManagementSeeder
```

## 🧪 Testing

### Test Endpoints
```
GET /api/test/roles           # List available roles
GET /api/test/role-system     # Test role system functionality
```

### Verification Steps
1. Check role migration: `GET /api/test/role-system`
2. Verify user roles: `GET /api/user` (authenticated)
3. Test admin functions: `GET /api/admin/user-management/`
4. Test role assignment: `POST /api/user-roles/{user}/assign`

## 📋 Frontend Integration

### Role Filter Implementation
For the "Filter by Role" field, use:
```javascript
// Fetch available roles
const response = await fetch('/api/admin/user-management/roles');
const roles = await response.json();

// Use roles.data for dropdown options
roles.data.forEach(role => {
  console.log(role.name, role.display_name);
});
```

### User Creation Form
```javascript
// Create user with roles
const userData = {
  name: "John Doe",
  email: "john@hospital.go.tz",
  pf_number: "PF001234",
  password: "password123",
  password_confirmation: "password123",
  role_ids: [3, 4], // Array of role IDs
  is_active: true
};

const response = await fetch('/api/admin/user-management/', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Authorization': `Bearer ${token}`
  },
  body: JSON.stringify(userData)
});
```

### Refresh Functionality
```javascript
// Refresh users list
const refreshUsers = async () => {
  const response = await fetch('/api/admin/user-management/');
  const users = await response.json();
  // Update UI with users.data.users
};
```

## 🚀 Next Steps

### Immediate Tasks
1. Run migrations: `php artisan migrate`
2. Run seeders: `php artisan db:seed --class=RoleManagementSeeder`
3. Test endpoints with Postman/frontend
4. Verify role assignments work correctly

### Frontend Implementation
1. Update user management interface
2. Implement role filter dropdown
3. Add create user button and form
4. Add refresh functionality
5. Update user display to show multiple roles

### Future Enhancements
1. Bulk user operations
2. Advanced permission management
3. Role templates
4. User import/export
5. Advanced audit logging

## 🔧 Configuration

### Environment Variables
No additional environment variables required. The system uses existing database configuration.

### Middleware
The system uses existing middleware:
- `auth:sanctum` - Authentication
- `admin` - Admin access (updated to use new role system)

### Caching
Consider implementing caching for:
- Role permissions
- User role assignments
- Frequently accessed user data

## 📞 Support

For issues or questions:
1. Check the test endpoints first
2. Verify database migrations ran successfully
3. Check Laravel logs for errors
4. Ensure proper authentication tokens

This implementation provides a robust, scalable role management system that supports the hospital's complex organizational structure while maintaining security and auditability.