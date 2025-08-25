# Both Service Form API Documentation

## Overview

The Both Service Form API provides a comprehensive backend solution for managing combined access requests for three services (Wellsoft, Jeeva, Internet Access Request) with a role-based approval workflow.

## Features

- **Auto-population**: Personal information is automatically populated from users, user_access, and departments tables
- **Role-based Access Control**: Only users with correct roles can fill and sign their sections
- **Sequential Approval Workflow**: HOD → Divisional Director → DICT → Head of IT → ICT Officer (final)
- **Required Comments**: HOD must provide comments during approval
- **Signature Management**: Digital signatures for each approval stage
- **PDF Export**: Generate official PDF documents for approved forms

## API Endpoints

### Base URL
```
/api/both-service-form
```

### Authentication
All endpoints require authentication via Laravel Sanctum tokens.

### Endpoints

#### 1. Get User Information (Auto-population)
```http
GET /api/both-service-form/user/info
```

**Response:**
```json
{
  "success": true,
  "data": {
    "pf_number": "12345",
    "staff_name": "John Doe",
    "phone_number": "+1234567890",
    "department_id": 1,
    "department_name": "IT Department",
    "role": "staff"
  }
}
```

#### 2. Get Departments List
```http
GET /api/both-service-form/departments/list
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "IT Department",
      "code": "IT"
    }
  ]
}
```

#### 3. Create Both Service Form Request
```http
POST /api/both-service-form/
```

**Request Body (multipart/form-data):**
```json
{
  "services_requested": ["wellsoft", "jeeva", "internet_access"],
  "access_type": "permanent",
  "temporary_until": "2025-12-31",
  "modules": ["module1", "module2"],
  "comments": "Request for system access",
  "department_id": 1,
  "signature": "file"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Both-service-form request submitted successfully",
  "data": {
    "id": 1,
    "user_id": 1,
    "form_type": "both_service_form",
    "services_requested": ["wellsoft", "jeeva", "internet_access"],
    "overall_status": "pending",
    "current_approval_stage": "hod"
  }
}
```

#### 4. Get Forms List (Role-based)
```http
GET /api/both-service-form/
```

**Response:**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "pf_number": "12345",
        "staff_name": "John Doe",
        "services_requested": ["wellsoft", "jeeva"],
        "overall_status": "pending",
        "current_approval_stage": "hod",
        "created_at": "2025-01-27T10:00:00.000000Z"
      }
    ],
    "per_page": 15,
    "total": 1
  }
}
```

#### 5. Get Specific Form
```http
GET /api/both-service-form/{id}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "user_id": 1,
    "pf_number": "12345",
    "staff_name": "John Doe",
    "services_requested": ["wellsoft", "jeeva"],
    "overall_status": "pending",
    "current_approval_stage": "hod",
    "user_permissions": {
      "can_edit_personal_info": false,
      "can_edit_services": false,
      "can_approve": true,
      "approval_stage": "hod",
      "user_role": "Head of Department",
      "readonly_sections": ["divisional_director", "dict", "hod_it", "ict_officer"]
    }
  }
}
```

#### 6. Role-based Approval Endpoints

##### HOD Approval (Comments Required)
```http
POST /api/both-service-form/{id}/approve/hod
```

**Request Body (multipart/form-data):**
```json
{
  "action": "approve",
  "comments": "Approved for department access",
  "signature": "file"
}
```

##### Divisional Director Approval
```http
POST /api/both-service-form/{id}/approve/divisional-director
```

##### DICT Approval
```http
POST /api/both-service-form/{id}/approve/dict
```

##### Head of IT Approval
```http
POST /api/both-service-form/{id}/approve/hod-it
```

##### ICT Officer Approval (Final)
```http
POST /api/both-service-form/{id}/approve/ict-officer
```

**Approval Response:**
```json
{
  "success": true,
  "message": "Form approved successfully",
  "data": {
    "id": 1,
    "overall_status": "in_review",
    "current_approval_stage": "divisional_director",
    "hod_approval_status": "approved",
    "hod_comments": "Approved for department access",
    "hod_approved_at": "2025-01-27T10:30:00.000000Z"
  }
}
```

#### 7. Export PDF
```http
GET /api/both-service-form/{id}/export-pdf
```

**Response:** PDF file download

## Role-based Access Control

### Roles and Permissions

1. **HOD (Head of Department)**
   - Can create new forms
   - Can approve/reject forms from their department
   - **Must provide comments** when approving
   - Can only edit HOD approval section

2. **Divisional Director**
   - Can approve/reject forms with HOD approval
   - Can edit Divisional Director section only
   - Comments are optional

3. **DICT (Director of ICT)**
   - Can approve/reject forms with Divisional Director approval
   - Can edit DICT section only
   - Comments are optional

4. **Head of IT**
   - Can approve/reject forms with DICT approval
   - Can edit Head of IT section only
   - Comments are optional

5. **ICT Officer**
   - Can approve/reject forms with Head of IT approval
   - **Final approval** - grants actual access
   - Can edit ICT Officer section only
   - Comments are optional

### Approval Workflow

```
Request Creation → HOD Approval (required comments) → Divisional Director → DICT → Head of IT → ICT Officer (final)
```

## Validation Rules

### Form Creation
- `services_requested`: Required array, minimum 1 service
- `access_type`: Required, either 'permanent' or 'temporary'
- `temporary_until`: Required if access_type is 'temporary', must be future date
- `department_id`: Required, must exist in departments table
- `signature`: Required file, PNG/JPG/JPEG, max 2MB

### Approval
- `action`: Required, either 'approve' or 'reject'
- `comments`: Required for HOD, optional for others, max 1000 characters
- `signature`: Required file, PNG/JPG/JPEG, max 2MB

## Error Responses

### Validation Error (422)
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "services_requested": ["At least one service must be selected."],
    "signature": ["Signature is required."]
  }
}
```

### Authorization Error (403)
```json
{
  "success": false,
  "message": "Unauthorized to approve at this stage"
}
```

### Not Found Error (404)
```json
{
  "success": false,
  "message": "Form not found"
}
```

## Database Schema

The implementation extends the existing `module_access_requests` table with the following additional fields:

### Personal Information
- `pf_number`, `staff_name`, `phone_number`, `department_id`, `signature_path`

### Form Type and Services
- `form_type`: Enum ('module_access', 'both_service_form')
- `services_requested`: JSON array of requested services

### Approval Workflow Fields
For each role (HOD, Divisional Director, DICT, Head of IT, ICT Officer):
- `{role}_approval_status`: Enum ('pending', 'approved', 'rejected')
- `{role}_comments`: Text field for comments
- `{role}_signature_path`: Path to signature file
- `{role}_approved_at`: Timestamp of approval
- `{role}_user_id`: Foreign key to approving user

### Status Tracking
- `overall_status`: Enum ('pending', 'in_review', 'approved', 'rejected')
- `current_approval_stage`: String indicating current approval stage

## Security Features

1. **Role-based Middleware**: Ensures only authorized users can access specific endpoints
2. **Form Access Control**: Users can only view forms they have permission to see
3. **Approval Stage Validation**: Users can only approve at their designated stage
4. **Signature Security**: Secure file upload and storage for digital signatures
5. **Input Validation**: Comprehensive validation for all form inputs

## Usage Examples

### Frontend Integration

```javascript
// Get user info for auto-population
const userInfo = await fetch('/api/both-service-form/user/info', {
  headers: { 'Authorization': `Bearer ${token}` }
});

// Create form
const formData = new FormData();
formData.append('services_requested[]', 'wellsoft');
formData.append('services_requested[]', 'jeeva');
formData.append('access_type', 'permanent');
formData.append('department_id', '1');
formData.append('signature', signatureFile);

const response = await fetch('/api/both-service-form/', {
  method: 'POST',
  headers: { 'Authorization': `Bearer ${token}` },
  body: formData
});

// Approve as HOD
const approvalData = new FormData();
approvalData.append('action', 'approve');
approvalData.append('comments', 'Approved for department access');
approvalData.append('signature', hodSignatureFile);

const approval = await fetch(`/api/both-service-form/${formId}/approve/hod`, {
  method: 'POST',
  headers: { 'Authorization': `Bearer ${token}` },
  body: approvalData
});
```

## Migration

To apply the database changes, run:

```bash
cd backend
php artisan migrate
```

This will add all the necessary fields to the `module_access_requests` table to support the both-service-form functionality.