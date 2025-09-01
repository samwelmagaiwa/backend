# Department Management API Documentation

## Overview

This document describes the clean Laravel backend implementation for department management with advanced configuration options, including HOD assignment functionality.

## Base URL
```
/api/admin/departments
```

## Authentication
All endpoints require:
- `Authorization: Bearer {token}` header
- Admin role permissions

## Endpoints

### 1. Get Department Creation Form Data

**GET** `/api/admin/departments/create-form-data`

Retrieves all necessary data for the department creation form, including eligible HOD users.

#### Response
```json
{
  "success": true,
  "data": {
    "eligible_hods": [
      {
        "id": 1,
        "name": "Dr. John Smith",
        "email": "john.smith@hospital.com",
        "pf_number": "PF001",
        "staff_name": "Dr. John Smith",
        "roles": [
          {
            "id": 2,
            "name": "head_of_department",
            "display_name": "Head Of Department"
          }
        ],
        "current_departments": [],
        "is_available": true,
        "display_name": "Dr. John Smith (PF001) - john.smith@hospital.com"
      }
    ],
    "department_statuses": [
      {
        "value": true,
        "label": "Active"
      },
      {
        "value": false,
        "label": "Inactive"
      }
    ]
  },
  "message": "Department creation form data retrieved successfully."
}
```

### 2. Get Eligible HOD Users

**GET** `/api/admin/departments/eligible-hods`

Retrieves users eligible to be assigned as Head of Department.

#### Response
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Dr. John Smith",
      "email": "john.smith@hospital.com",
      "pf_number": "PF001",
      "staff_name": "Dr. John Smith",
      "roles": [
        {
          "id": 2,
          "name": "head_of_department",
          "display_name": "Head Of Department"
        }
      ],
      "current_departments": [],
      "is_available": true,
      "display_name": "Dr. John Smith (PF001) - john.smith@hospital.com"
    }
  ],
  "message": "Eligible HOD users retrieved successfully."
}
```

### 3. Create New Department

**POST** `/api/admin/departments`

Creates a new department with advanced configuration options.

#### Request Body
```json
{
  "name": "Cardiology Department",
  "code": "CARDIO",
  "description": "Department specializing in heart and cardiovascular diseases",
  "is_active": true,
  "hod_user_id": 1,
  "assign_hod_immediately": true
}
```

#### Request Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `name` | string | Yes | Department name (max 255 chars, unique) |
| `code` | string | Yes | Department code (max 20 chars, uppercase, unique) |
| `description` | string | No | Department description (max 1000 chars) |
| `is_active` | boolean | No | Department status (default: true) |
| `hod_user_id` | integer | No | User ID to assign as HOD |
| `assign_hod_immediately` | boolean | No | Whether to assign HOD immediately (default: false) |

#### Validation Rules

1. **Department Name**: Required, unique, max 255 characters
2. **Department Code**: Required, unique, max 20 characters, uppercase letters/numbers/hyphens/underscores only
3. **HOD Assignment**: If `assign_hod_immediately` is true, `hod_user_id` is required
4. **HOD User Validation**: Selected user must have appropriate role (head_of_department, ict_director, or admin)
5. **HOD Availability**: User cannot be HOD of another department
6. **User Status**: HOD user must be active

#### Response (Success)
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Cardiology Department",
    "code": "CARDIO",
    "description": "Department specializing in heart and cardiovascular diseases",
    "is_active": true,
    "hod": {
      "id": 1,
      "name": "Dr. John Smith",
      "email": "john.smith@hospital.com",
      "pf_number": "PF001",
      "roles": ["head_of_department"]
    },
    "created_at": "2024-01-15T10:30:00.000000Z",
    "updated_at": "2024-01-15T10:30:00.000000Z"
  },
  "message": "Department 'Cardiology Department' created successfully with HOD assigned."
}
```

#### Response (Validation Error)
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "name": ["A department with this name already exists."],
    "code": ["A department with this code already exists."],
    "hod_user_id": ["HOD user is required when \"Assign HOD immediately\" is checked."]
  }
}
```

### 4. List Departments

**GET** `/api/admin/departments`

Retrieves a paginated list of departments with filtering and search capabilities.

#### Query Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `search` | string | Search in name, code, or description |
| `is_active` | boolean | Filter by active status |
| `sort_by` | string | Sort field (name, code, created_at) |
| `sort_order` | string | Sort direction (asc, desc) |
| `per_page` | integer | Items per page (max 100) |

#### Response
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "name": "Cardiology Department",
        "code": "CARDIO",
        "description": "Department specializing in heart and cardiovascular diseases",
        "is_active": true,
        "users_count": 15,
        "total_requests_count": 25,
        "pending_requests_count": 3,
        "hod": {
          "id": 1,
          "name": "Dr. John Smith",
          "email": "john.smith@hospital.com",
          "pf_number": "PF001",
          "roles": ["head_of_department"]
        },
        "created_at": "2024-01-15T10:30:00.000000Z",
        "updated_at": "2024-01-15T10:30:00.000000Z"
      }
    ],
    "last_page": 1,
    "per_page": 15,
    "total": 1
  },
  "message": "Departments retrieved successfully."
}
```

### 5. Get Department Details

**GET** `/api/admin/departments/{id}`

Retrieves detailed information about a specific department.

#### Response
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Cardiology Department",
    "code": "CARDIO",
    "description": "Department specializing in heart and cardiovascular diseases",
    "is_active": true,
    "users_count": 15,
    "total_requests_count": 25,
    "pending_requests_count": 3,
    "hod": {
      "id": 1,
      "name": "Dr. John Smith",
      "email": "john.smith@hospital.com",
      "pf_number": "PF001",
      "roles": ["head_of_department"]
    },
    "users": [
      {
        "id": 2,
        "name": "Dr. Jane Doe",
        "email": "jane.doe@hospital.com",
        "pf_number": "PF002",
        "roles": ["staff"]
      }
    ],
    "created_at": "2024-01-15T10:30:00.000000Z",
    "updated_at": "2024-01-15T10:30:00.000000Z"
  },
  "message": "Department retrieved successfully."
}
```

### 6. Update Department

**PUT** `/api/admin/departments/{id}`

Updates an existing department with the same validation rules as creation.

#### Request Body
Same as create department endpoint.

#### Response
Same structure as create department endpoint with updated data.

### 7. Delete Department

**DELETE** `/api/admin/departments/{id}`

Deletes a department with safety checks.

#### Safety Checks
- Cannot delete department with assigned users
- Cannot delete department with pending access requests

#### Response (Success)
```json
{
  "success": true,
  "message": "Department 'Cardiology Department' deleted successfully."
}
```

#### Response (Error)
```json
{
  "success": false,
  "message": "Cannot delete department that has assigned users. Please reassign users first."
}
```

### 8. Toggle Department Status

**PATCH** `/api/admin/departments/{id}/toggle-status`

Toggles the active/inactive status of a department.

#### Response
```json
{
  "success": true,
  "data": {
    "id": 1,
    "is_active": false
  },
  "message": "Department 'Cardiology Department' deactivated successfully."
}
```

## Error Responses

### 401 Unauthorized
```json
{
  "message": "Unauthenticated."
}
```

### 403 Forbidden
```json
{
  "success": false,
  "message": "Insufficient permissions."
}
```

### 404 Not Found
```json
{
  "success": false,
  "message": "Department not found."
}
```

### 422 Validation Error
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "field_name": ["Error message"]
  }
}
```

### 500 Internal Server Error
```json
{
  "success": false,
  "message": "Failed to process request.",
  "error": "Detailed error message (only in debug mode)"
}
```

## Implementation Features

### Clean Code Practices
- **Form Request Validation**: Uses `DepartmentRequest` for centralized validation
- **Database Transactions**: All operations wrapped in transactions for data integrity
- **Comprehensive Logging**: All actions logged with context for auditing
- **Error Handling**: Consistent error responses with proper HTTP status codes
- **Input Sanitization**: Department codes automatically converted to uppercase
- **Relationship Loading**: Efficient eager loading of related models

### Security Features
- **Role-based Access Control**: Admin middleware protection
- **Input Validation**: Comprehensive validation rules
- **SQL Injection Prevention**: Eloquent ORM usage
- **Data Integrity**: Foreign key constraints and unique validations

### Performance Optimizations
- **Pagination**: Configurable pagination with limits
- **Selective Loading**: Only load necessary relationships
- **Database Indexing**: Optimized queries with proper indexes
- **Caching Ready**: Structure supports future caching implementation

### Business Logic
- **HOD Assignment Validation**: Ensures users have appropriate roles
- **Department Code Formatting**: Automatic uppercase conversion
- **Availability Checking**: Prevents double HOD assignments
- **Cascade Protection**: Prevents deletion of departments with dependencies

## Usage Examples

### Frontend Integration
```javascript
// Get form data for department creation
const formData = await fetch('/api/admin/departments/create-form-data', {
  headers: { 'Authorization': `Bearer ${token}` }
}).then(res => res.json());

// Create department with HOD assignment
const newDepartment = await fetch('/api/admin/departments', {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    name: 'Cardiology Department',
    code: 'CARDIO',
    description: 'Heart and cardiovascular diseases',
    is_active: true,
    hod_user_id: 1,
    assign_hod_immediately: true
  })
}).then(res => res.json());
```

This implementation provides a robust, secure, and scalable department management system with comprehensive HOD assignment capabilities.