# Both Service Form Table Data API Documentation

## Overview

This API provides endpoints to pull both-service-form data with specific columns for table display and HOD-specific functionality for viewing and approving forms.

## Table Data Columns

The API returns data with the following columns as requested:

1. **Request ID** - Unique identifier for the form
2. **Request Type** - Services requested (Wellsoft, Jeeva, Internet Access)
3. **Personal Information** - PF Number, Staff Name, Phone, Department
4. **Module Requested For** - Purpose of the access request
5. **Module Request** - Specific modules requested
6. **Access Rights** - Permanent or Temporary access
7. **Approval** - Approval status for each stage
8. **Comments** - All comments from different stages
9. **For Implementation** - Implementation status
10. **Submission Date** - When the form was submitted
11. **Current Status** - Current approval stage and status
12. **Actions** - Available actions (view, approve, reject, export)

## API Endpoints

### 1. Get Table Data with Specific Columns

```http
GET /api/both-service-form/table/data
```

**Description:** Returns both-service-form data formatted for table display with the requested columns.

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "request_id": 1,
      "request_type": "Wellsoft, Jeeva Access",
      "personal_information": {
        "pf_number": "12345",
        "staff_name": "John Doe",
        "phone_number": "+1234567890",
        "department": "IT Department"
      },
      "module_requested_for": "Wellsoft System Access, Jeeva System Access",
      "module_request": ["module1", "module2"],
      "access_rights": "Permanent",
      "approval": {
        "hod": {
          "status": "approved",
          "approved_by": "Jane Smith",
          "approved_at": "2025-01-27 10:30:00"
        },
        "divisional_director": {
          "status": "pending",
          "approved_by": null,
          "approved_at": null
        },
        "dict": {
          "status": "pending",
          "approved_by": null,
          "approved_at": null
        },
        "hod_it": {
          "status": "pending",
          "approved_by": null,
          "approved_at": null
        },
        "ict_officer": {
          "status": "pending",
          "approved_by": null,
          "approved_at": null
        }
      },
      "comments": {
        "initial": "Need access for project work",
        "hod": "Approved for department use"
      },
      "for_implementation": "Pending Approval",
      "submission_date": "2025-01-27 09:00:00",
      "current_status": "At Divisional Director - In Review",
      "actions": ["view", "export_pdf"]
    }
  ]
}
```

### 2. Get Form for HOD View/Edit

```http
GET /api/both-service-form/{id}/hod-view
```

**Description:** Returns form data specifically formatted for HOD viewing and editing. Only HOD can access this endpoint.

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "request_id": 1,
    "personal_information": {
      "pf_number": "12345",
      "staff_name": "John Doe",
      "phone_number": "+1234567890",
      "department": "IT Department",
      "department_id": 1
    },
    "services_requested": ["wellsoft", "jeeva"],
    "access_type": "permanent",
    "temporary_until": null,
    "modules": ["module1", "module2"],
    "comments": "Need access for project work",
    "signature_path": "signatures/both-service-forms/signature.png",
    "overall_status": "pending",
    "current_approval_stage": "hod",
    "created_at": "2025-01-27T09:00:00.000000Z",
    
    "hod_section": {
      "approval_status": "pending",
      "comments": null,
      "signature_path": null,
      "approved_at": null,
      "approved_by": null,
      "can_edit": true,
      "is_required": true
    },
    
    "divisional_director_section": {
      "approval_status": "pending",
      "comments": null,
      "approved_at": null,
      "approved_by": null,
      "can_edit": false,
      "is_readonly": true
    },
    "dict_section": {
      "approval_status": "pending",
      "comments": null,
      "approved_at": null,
      "approved_by": null,
      "can_edit": false,
      "is_readonly": true
    },
    "hod_it_section": {
      "approval_status": "pending",
      "comments": null,
      "approved_at": null,
      "approved_by": null,
      "can_edit": false,
      "is_readonly": true
    },
    "ict_officer_section": {
      "approval_status": "pending",
      "comments": null,
      "approved_at": null,
      "approved_by": null,
      "can_edit": false,
      "is_readonly": true
    },
    
    "user_permissions": {
      "can_approve": true,
      "can_reject": true,
      "current_user_role": "Head of Department",
      "is_hod": true,
      "can_submit_to_next_stage": true
    }
  }
}
```

### 3. HOD Submit to Next Stage

```http
POST /api/both-service-form/{id}/hod-submit
```

**Description:** Allows HOD to fill their section and submit the form to the next approval stage.

**Request Body (multipart/form-data):**
```json
{
  "action": "approve",
  "comments": "Approved for department access. Staff member is qualified for these systems.",
  "signature": "file"
}
```

**Validation Rules:**
- `action`: Required, must be 'approve' or 'reject'
- `comments`: Required, minimum 10 characters, maximum 1000 characters
- `signature`: Required file, PNG/JPG/JPEG format, maximum 2MB

**Response:**
```json
{
  "success": true,
  "message": "Form approved and submitted to Divisional Director",
  "data": {
    "form_id": 1,
    "overall_status": "in_review",
    "current_approval_stage": "divisional_director",
    "hod_approval_status": "approved",
    "next_stage": "Divisional Director"
  }
}
```

## HOD Workflow

### 1. View Table Data
HOD can view all forms from their department using the table data endpoint:

```javascript
const response = await fetch('/api/both-service-form/table/data', {
  headers: { 'Authorization': `Bearer ${token}` }
});
```

### 2. Click View Button
When HOD clicks the view button, use the HOD-specific endpoint:

```javascript
const formResponse = await fetch(`/api/both-service-form/${formId}/hod-view`, {
  headers: { 'Authorization': `Bearer ${token}` }
});
```

### 3. Fill HOD Section
HOD can only edit their section when:
- `hod_section.can_edit` is `true`
- `user_permissions.can_submit_to_next_stage` is `true`
- Form is in HOD approval stage

### 4. Submit to Next Stage
After filling their section, HOD submits to next stage:

```javascript
const formData = new FormData();
formData.append('action', 'approve'); // or 'reject'
formData.append('comments', 'HOD approval comments');
formData.append('signature', hodSignatureFile);

const submitResponse = await fetch(`/api/both-service-form/${formId}/hod-submit`, {
  method: 'POST',
  headers: { 'Authorization': `Bearer ${token}` },
  body: formData
});
```

## Frontend Implementation Guide

### Table Display
```javascript
// Get table data
const tableData = await fetch('/api/both-service-form/table/data');

// Display in table with columns:
// Request ID | Request Type | Personal Info | Module Requested For | 
// Module Request | Access Rights | Approval | Comments | 
// For Implementation | Submission Date | Current Status | Actions
```

### View Button Implementation
```javascript
function handleViewClick(requestId) {
  // Navigate to HOD form view
  window.location.href = `/hod/both-service-form/${requestId}`;
  
  // Or fetch form data for modal/page
  fetchFormForHOD(requestId);
}

async function fetchFormForHOD(formId) {
  const response = await fetch(`/api/both-service-form/${formId}/hod-view`);
  const formData = await response.json();
  
  // Render form with:
  // - Personal info (readonly)
  // - Services requested (readonly)
  // - HOD section (editable if can_edit is true)
  // - Other sections (readonly/disabled)
}
```

### Form Sections Display
```javascript
function renderFormSections(formData) {
  // Personal Information (readonly)
  renderPersonalInfo(formData.personal_information);
  
  // Services Requested (readonly)
  renderServicesRequested(formData.services_requested);
  
  // HOD Section (editable if can_edit is true)
  renderHODSection(formData.hod_section);
  
  // Other sections (readonly/disabled)
  renderOtherSections([
    formData.divisional_director_section,
    formData.dict_section,
    formData.hod_it_section,
    formData.ict_officer_section
  ]);
  
  // Submit button (visible if can_submit_to_next_stage is true)
  if (formData.user_permissions.can_submit_to_next_stage) {
    renderSubmitButton();
  }
}
```

## Security Features

1. **Role-based Access**: Only HOD can access HOD-specific endpoints
2. **Department Filtering**: HOD can only see forms from their department
3. **Stage Validation**: HOD can only edit forms in HOD approval stage
4. **Required Comments**: HOD must provide comments when approving
5. **Signature Validation**: Digital signature required for approval

## Error Handling

### Access Denied (403)
```json
{
  "success": false,
  "message": "Access denied. Only HOD can access this endpoint."
}
```

### Form Not in HOD Stage (403)
```json
{
  "success": false,
  "message": "Cannot submit to next stage. Form is not in HOD approval stage or you are not authorized."
}
```

### Validation Error (422)
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "comments": ["Comments are required for HOD approval."],
    "signature": ["Signature is required for approval."]
  }
}
```

## Status Flow

1. **Initial**: Form created by staff member
2. **HOD Stage**: Form awaits HOD approval
3. **HOD Approved**: Form moves to Divisional Director
4. **Divisional Director Approved**: Form moves to DICT
5. **DICT Approved**: Form moves to Head of IT
6. **Head of IT Approved**: Form moves to ICT Officer
7. **ICT Officer Approved**: Form completed and ready for implementation

## Implementation Notes

- All sections except HOD section are readonly/disabled for HOD users
- HOD comments are mandatory (minimum 10 characters)
- Digital signature is required for each approval
- Form automatically moves to next stage upon HOD approval
- Rejected forms stop the workflow and are marked as rejected
- Table data is filtered based on user role and department access