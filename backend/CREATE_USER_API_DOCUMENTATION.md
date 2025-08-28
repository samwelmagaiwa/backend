# Create User API Documentation

## Overview
This document provides comprehensive API documentation for the "Create New User" functionality in the admin/user-roles system.

## 🛠️ API Endpoints

### 1. Get Form Data for Create User
**Endpoint:** `GET /api/admin/user-management/form-data`
**Purpose:** Get all necessary data to populate the create user form

**Response:**
```json
{
  "success": true,
  "data": {
    "roles": [
      {
        "id": 1,
        "name": "staff",
        "display_name": "Staff",
        "description": "Regular hospital staff member",
        "is_system_role": true
      },
      {
        "id": 2,
        "name": "head_of_department",
        "display_name": "Head Of Department",
        "description": "Head of department with departmental oversight",
        "is_system_role": true
      }
    ],
    "departments": [
      {
        "id": 1,
        "name": "Information Technology",
        "code": "IT",
        "description": "IT Department",
        "display_name": "Information Technology (IT)"
      },
      {
        "id": 2,
        "name": "Human Resources",
        "code": "HR",
        "description": "HR Department",
        "display_name": "Human Resources (HR)"
      }
    ]
  }
}
```

### 2. Get Available Roles
**Endpoint:** `GET /api/admin/user-management/roles`
**Purpose:** Get list of roles for the role filter dropdown

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "staff",
      "display_name": "Staff",
      "description": "Regular hospital staff member",
      "is_system_role": true
    },
    {
      "id": 2,
      "name": "head_of_department",
      "display_name": "Head Of Department",
      "description": "Head of department with departmental oversight",
      "is_system_role": true
    }
  ]
}
```

### 3. Get Available Departments
**Endpoint:** `GET /api/admin/user-management/departments`
**Purpose:** Get list of departments for the department dropdown

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Information Technology",
      "code": "IT",
      "description": "IT Department",
      "display_name": "Information Technology (IT)"
    },
    {
      "id": 2,
      "name": "Human Resources",
      "code": "HR",
      "description": "HR Department",
      "display_name": "Human Resources (HR)"
    }
  ]
}
```

### 4. Validate User Data (Optional)
**Endpoint:** `POST /api/admin/user-management/validate`
**Purpose:** Validate user data before submission (for real-time validation)

**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john.doe@hospital.go.tz",
  "phone": "+255123456789",
  "pf_number": "PF001234",
  "department_id": 1,
  "password": "password123",
  "password_confirmation": "password123",
  "role_ids": [1, 2]
}
```

**Response (Success):**
```json
{
  "success": true,
  "message": "Validation passed"
}
```

**Response (Validation Error):**
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": ["This email address is already registered."],
    "pf_number": ["This PF number is already registered."],
    "password_confirmation": ["Password confirmation does not match."]
  }
}
```

### 5. Create New User
**Endpoint:** `POST /api/admin/user-management/`
**Purpose:** Create a new user with assigned roles

**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john.doe@hospital.go.tz",
  "phone": "+255123456789",
  "pf_number": "PF001234",
  "staff_name": "John D. Doe",
  "department_id": 1,
  "password": "password123",
  "password_confirmation": "password123",
  "role_ids": [1, 2],
  "is_active": true
}
```

**Field Descriptions:**
- `name` (required): Full name of the user
- `email` (required): Email address (must be unique)
- `phone` (optional): Phone number
- `pf_number` (optional): PF number (must be unique if provided)
- `staff_name` (optional): Staff name (alternative name)
- `department_id` (optional): Department ID from departments table
- `password` (required): Password (minimum 8 characters)
- `password_confirmation` (required): Password confirmation
- `role_ids` (required): Array of role IDs to assign
- `is_active` (optional): User status (default: true)

**Response (Success):**
```json
{
  "success": true,
  "data": {
    "user": {
      "id": 15,
      "name": "John Doe",
      "email": "john.doe@hospital.go.tz",
      "pf_number": "PF001234",
      "staff_name": "John D. Doe",
      "phone": "+255123456789",
      "is_active": true,
      "roles": [
        {
          "id": 1,
          "name": "staff",
          "display_name": "Staff",
          "assigned_at": "2025-01-27T15:30:00Z"
        },
        {
          "id": 2,
          "name": "head_of_department",
          "display_name": "Head Of Department",
          "assigned_at": "2025-01-27T15:30:00Z"
        }
      ],
      "display_roles": "Staff, Head Of Department",
      "created_at": "2025-01-27T15:30:00Z"
    }
  },
  "message": "User created successfully"
}
```

**Response (Validation Error):**
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": ["This email address is already registered."],
    "role_ids": ["At least one role must be assigned."]
  }
}
```

### 6. Get Users List (with filtering)
**Endpoint:** `GET /api/admin/user-management/`
**Purpose:** Get paginated list of users with filtering options

**Query Parameters:**
- `search` (optional): Search by name, email, PF number
- `role` (optional): Filter by role name
- `status` (optional): Filter by status (active/inactive/all)
- `sort_by` (optional): Sort by field (name/email/created_at)
- `sort_order` (optional): Sort order (asc/desc)
- `per_page` (optional): Items per page (default: 20, max: 100)
- `page` (optional): Page number

**Example:** `GET /api/admin/user-management/?search=john&role=staff&status=active&per_page=10`

**Response:**
```json
{
  "success": true,
  "data": {
    "users": [
      {
        "id": 15,
        "name": "John Doe",
        "email": "john.doe@hospital.go.tz",
        "pf_number": "PF001234",
        "staff_name": "John D. Doe",
        "phone": "+255123456789",
        "is_active": true,
        "roles": [
          {
            "id": 1,
            "name": "staff",
            "display_name": "Staff",
            "assigned_at": "2025-01-27T15:30:00Z"
          }
        ],
        "role_names": ["staff"],
        "display_roles": "Staff",
        "departments_as_hod": [],
        "is_hod": false,
        "created_at": "2025-01-27T15:30:00Z",
        "updated_at": "2025-01-27T15:30:00Z",
        "onboarding_status": {
          "needs_onboarding": true,
          "completed": false,
          "current_step": "terms-popup"
        }
      }
    ],
    "pagination": {
      "current_page": 1,
      "last_page": 3,
      "per_page": 10,
      "total": 25,
      "from": 1,
      "to": 10
    }
  }
}
```

## 🔐 Security Features

### 1. Authentication Required
All endpoints require authentication via Sanctum token:
```javascript
headers: {
  'Authorization': 'Bearer YOUR_TOKEN_HERE'
}
```

### 2. Admin Privileges Required
All endpoints require admin privileges (admin or super_admin role).

### 3. Privilege Escalation Prevention
- Regular admins cannot assign admin or super_admin roles
- Only super_admins can assign admin roles
- Users cannot modify their own roles

### 4. Input Validation
- Email uniqueness validation
- PF number uniqueness validation
- Password strength requirements
- Role existence validation
- Department existence validation

## 📝 Frontend Integration Examples

### 1. Initialize Create User Form
```javascript
// Fetch form data
const response = await fetch('/api/admin/user-management/form-data', {
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json'
  }
});

const formData = await response.json();

// Populate dropdowns
const roles = formData.data.roles;
const departments = formData.data.departments;
```

### 2. Real-time Validation
```javascript
// Validate as user types
const validateField = async (fieldData) => {
  const response = await fetch('/api/admin/user-management/validate', {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(fieldData)
  });
  
  const result = await response.json();
  return result;
};
```

### 3. Create User
```javascript
const createUser = async (userData) => {
  const response = await fetch('/api/admin/user-management/', {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(userData)
  });
  
  const result = await response.json();
  
  if (result.success) {
    // User created successfully
    console.log('User created:', result.data.user);
    // Refresh users list or redirect
  } else {
    // Handle validation errors
    console.log('Validation errors:', result.errors);
  }
};
```

### 4. Refresh Users List
```javascript
const refreshUsers = async (filters = {}) => {
  const queryParams = new URLSearchParams(filters);
  const response = await fetch(`/api/admin/user-management/?${queryParams}`, {
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    }
  });
  
  const result = await response.json();
  return result.data;
};
```

### 5. Role Filter Implementation
```javascript
// Get roles for filter dropdown
const getRolesForFilter = async () => {
  const response = await fetch('/api/admin/user-management/roles', {
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    }
  });
  
  const result = await response.json();
  return result.data; // Array of roles with display_name
};
```

## 🎯 Form Field Mapping

### Create User Form Fields → API Fields
```javascript
const formFieldMapping = {
  // User Information
  'fullName': 'name',           // Full Name * → name
  'email': 'email',             // Email Address * → email
  'pfNumber': 'pf_number',      // PF Number → pf_number
  'phoneNumber': 'phone',       // Phone Number → phone
  'staffName': 'staff_name',    // Staff Name → staff_name
  
  // Department & Security
  'department': 'department_id', // Department → department_id
  'password': 'password',        // Password * → password
  'confirmPassword': 'password_confirmation', // Confirm → password_confirmation
  
  // Assign Roles
  'selectedRoles': 'role_ids',   // Selected Roles → role_ids (array)
  
  // Status
  'isActive': 'is_active'        // Active Status → is_active (boolean)
};
```

## 🔄 Workflow Integration

### Complete Create User Workflow
1. **Load Form Data**: `GET /api/admin/user-management/form-data`
2. **Real-time Validation** (optional): `POST /api/admin/user-management/validate`
3. **Create User**: `POST /api/admin/user-management/`
4. **Refresh List**: `GET /api/admin/user-management/`

### Error Handling
```javascript
const handleApiError = (error, response) => {
  if (response.status === 422) {
    // Validation errors
    return response.errors;
  } else if (response.status === 403) {
    // Permission denied
    return { general: ['You do not have permission to perform this action.'] };
  } else if (response.status === 500) {
    // Server error
    return { general: ['An unexpected error occurred. Please try again.'] };
  }
};
```

This comprehensive backend implementation supports all the form fields and functionality described in your Create User card, with proper validation, security, and error handling.