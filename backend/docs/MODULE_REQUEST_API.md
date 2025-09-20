# Module Request API Documentation

This documentation covers the new module request data retrieval endpoints added to the `BothServiceFormController`.

## Overview

The module request functionality allows HODs, Divisional Directors, ICT Directors, and ICT Officers to:
- View detailed module request data from the `user_access` table
- Get lists of pending module requests with filtering capabilities
- Access comprehensive statistics about module usage and requests
- View individual request details including HOD signatures

## Authentication & Authorization

All endpoints require authentication via Sanctum and use role-based middleware:
- **Allowed Roles**: `head_of_department`, `divisional_director`, `ict_director`, `ict_officer`, `admin`, `super_admin`
- **Department Filtering**: HODs can only access requests from their assigned department

## API Endpoints

### 1. Get Module Request Statistics

**Endpoint**: `GET /api/both-service-form/module-requests-statistics`

**Purpose**: Retrieve comprehensive statistics about module requests for dashboard displays.

**Response Format**:
```json
{
  "success": true,
  "data": {
    "total_requests": 150,
    "module_requests": {
      "total": 75,
      "pending": 20,
      "hod_pending": 5,
      "approved": 45,
      "rejected": 5,
      "use_requests": 65,
      "revoke_requests": 10
    },
    "wellsoft_usage": {
      "total_requests": 45,
      "modules_requested": 180
    },
    "jeeva_usage": {
      "total_requests": 35,
      "modules_requested": 140
    },
    "by_status": {
      "pending": 25,
      "hod_pending": 8,
      "approved": 100,
      "rejected": 17
    },
    "recent_requests": [
      {
        "id": 123,
        "staff_name": "John Doe",
        "status": "pending",
        "module_requested_for": "use",
        "wellsoft_count": 3,
        "jeeva_count": 2,
        "created_at": "2024-01-15 10:30:00"
      }
    ]
  }
}
```

### 2. Get Pending Module Requests

**Endpoint**: `GET /api/both-service-form/module-requests`

**Query Parameters**:
- `modules_only` (optional): Set to `"true"` to filter only requests with module data

**Purpose**: Get a list of pending module requests with detailed summaries.

**Response Format**:
```json
{
  "success": true,
  "data": [
    {
      "id": 123,
      "pf_number": "PF001",
      "staff_name": "John Doe",
      "department": "Cardiology",
      "phone_number": "+1234567890",
      "status": "pending",
      "created_at": "2024-01-15 10:30:00",
      "request_types": ["wellsoft", "jeeva_access"],
      "has_module_request": true,
      "module_summary": {
        "requested_for": "use",
        "wellsoft_count": 3,
        "jeeva_count": 2,
        "wellsoft_modules": ["Patient Management", "Billing", "Reports"],
        "jeeva_modules": ["Lab Results", "Prescriptions"]
      },
      "has_hod_signature": true,
      "access_type": "permanent",
      "temporary_until": null
    }
  ],
  "total": 1,
  "filters_applied": {
    "status": ["pending", "hod_pending"],
    "modules_only": false
  }
}
```

### 3. Get Individual Module Request Details

**Endpoint**: `GET /api/both-service-form/module-requests/{userAccessId}`

**Purpose**: Get comprehensive details for a specific module request.

**Response Format**:
```json
{
  "success": true,
  "data": {
    "request_id": 123,
    "module_requested_for": "use",
    "wellsoft_modules": {
      "selected": ["Patient Management", "Billing", "Reports"],
      "count": 3
    },
    "jeeva_modules": {
      "selected": ["Lab Results", "Prescriptions"],
      "count": 2
    },
    "request_types": ["wellsoft", "jeeva_access"],
    "has_wellsoft_request": true,
    "has_jeeva_request": true,
    "has_internet_request": false,
    "internet_purposes": [],
    "access_type": "permanent",
    "temporary_until": null,
    "status": "pending",
    "hod_signature_path": "storage/signatures/hod_signature_123.png",
    "hod_signature_url": "/storage/signatures/hod_signature_123.png",
    "created_at": "2024-01-15 10:30:00",
    "user_info": {
      "pf_number": "PF001",
      "staff_name": "John Doe",
      "department": "Cardiology",
      "phone_number": "+1234567890"
    }
  }
}
```

### 4. HOD Approval with Access Rights

**Endpoint**: `POST /api/both-service-form/module-requests/{userAccessId}/hod-approve`

**Purpose**: Update HOD approval for a module request with access rights validation.

**Authentication**: Requires HOD role

**Form Data Parameters**:
- `hod_name` (required): Name of the approving HOD/BM
- `hod_signature` (required): Signature file (jpeg, jpg, png, pdf, max 2MB)
- `approved_date` (required): Approval date (YYYY-MM-DD format)
- `comments` (optional): Additional comments from HOD
- `access_type` (required): Either "permanent" or "temporary"
- `temporary_until` (conditional): Required if access_type is "temporary", must be future date

**Request Example**:
```javascript
const formData = new FormData()
formData.append('hod_name', 'Chief Laboratory Technologist')
formData.append('hod_signature', signatureFile)
formData.append('approved_date', '2024-01-20')
formData.append('comments', 'Approved for laboratory access')
formData.append('access_type', 'permanent')

const response = await axios.post(
  '/api/both-service-form/module-requests/123/hod-approve',
  formData,
  { headers: { 'Content-Type': 'multipart/form-data' } }
)
```

**Response Format**:
```json
{
  "success": true,
  "message": "HOD approval updated successfully.",
  "data": {
    "request_id": 123,
    "status": "hod_approved",
    "access_rights": {
      "type": "permanent",
      "temporary_until": null,
      "description": "Permanent (until retirement)"
    },
    "hod_approval": {
      "name": "Chief Laboratory Technologist",
      "approved_at": "2024-01-20 10:30:00",
      "approved_at_formatted": "01/20/2024",
      "comments": "Approved for laboratory access",
      "signature_url": "/storage/signatures/hod/hod_signature_PF001_1705747800.png",
      "approved_by": "Dr. John Smith"
    },
    "next_step": "Divisional Director Approval"
  }
}
```

**Validation Rules**:
- HOD can only approve requests from their assigned department
- Signature file must be valid image or PDF format
- If access type is "temporary", temporary_until date must be provided and be in the future
- Approval date cannot be in the future

## Error Responses

All endpoints return standardized error responses:

```json
{
  "success": false,
  "message": "Error description"
}
```

Common HTTP status codes:
- `403`: Access denied (insufficient permissions or department restrictions)
- `404`: Request not found (for specific request details)
- `500`: Server error

## Frontend Implementation

### Vue.js Example

```javascript
import axios from 'axios'

// Get statistics
const fetchStatistics = async () => {
  try {
    const response = await axios.get('/api/both-service-form/module-requests-statistics')
    if (response.data.success) {
      return response.data.data
    }
  } catch (error) {
    console.error('Error fetching statistics:', error)
  }
}

// Get pending requests with module filter
const fetchPendingRequests = async (modulesOnly = false) => {
  try {
    const params = modulesOnly ? { modules_only: 'true' } : {}
    const response = await axios.get('/api/both-service-form/module-requests', { params })
    if (response.data.success) {
      return response.data.data
    }
  } catch (error) {
    console.error('Error fetching pending requests:', error)
  }
}

// Get specific request details
const fetchRequestDetails = async (requestId) => {
  try {
    const response = await axios.get(`/api/both-service-form/module-requests/${requestId}`)
    if (response.data.success) {
      return response.data.data
    }
  } catch (error) {
    console.error('Error fetching request details:', error)
  }
}
```

### React Example

```javascript
import axios from 'axios'

const ModuleRequestService = {
  async getStatistics() {
    const response = await axios.get('/api/both-service-form/module-requests-statistics')
    return response.data
  },

  async getPendingRequests(modulesOnly = false) {
    const params = modulesOnly ? { modules_only: 'true' } : {}
    const response = await axios.get('/api/both-service-form/module-requests', { params })
    return response.data
  },

  async getRequestDetails(requestId) {
    const response = await axios.get(`/api/both-service-form/module-requests/${requestId}`)
    return response.data
  }
}
```

## Integration with Existing HOD Dashboard

To integrate these endpoints with existing HOD dashboards:

1. **Statistics Card**: Use the statistics endpoint to populate dashboard cards showing module request counts, usage statistics, and recent activity.

2. **Pending Requests List**: Replace or enhance existing request lists with the new pending requests endpoint for better module-specific filtering.

3. **Request Details Modal**: Use the detailed request endpoint to show comprehensive information when users click on specific requests.

## Security Features

1. **Role-based Access Control**: Only authorized roles can access the endpoints
2. **Department Filtering**: HODs are restricted to their department's requests
3. **Data Sanitization**: All data is properly validated and sanitized
4. **Audit Logging**: All access is logged for security monitoring

## Performance Considerations

1. **Efficient Queries**: Uses optimized database queries with proper indexing
2. **Eager Loading**: Relationships are loaded efficiently to minimize N+1 queries  
3. **Caching Opportunities**: Results can be cached for frequently accessed statistics
4. **Pagination**: Consider implementing pagination for large datasets

## Example Vue Component

A complete example Vue component (`ModuleRequestExample.vue`) has been created at:
`/frontend/src/components/examples/ModuleRequestExample.vue`

This component demonstrates:
- Statistics dashboard with visual cards
- Pending requests list with filtering
- Request details modal
- Loading states and error handling
- Responsive design with Tailwind CSS

## Routes Configuration

The following routes have been added to `routes/api.php`:

```php
Route::prefix('both-service-form')->middleware('both.service.role')->group(function () {
    // Module request data routes
    Route::get('/module-requests/{userAccessId}', [BothServiceFormController::class, 'getModuleRequestData'])
        ->middleware('both.service.role:hod,divisional_director,dict,ict_officer')
        ->name('both-service-form.module-requests.show');
    Route::get('/module-requests', [BothServiceFormController::class, 'getPendingModuleRequests'])
        ->middleware('both.service.role:hod,divisional_director,dict,ict_officer')
        ->name('both-service-form.module-requests.pending');
    Route::get('/module-requests-statistics', [BothServiceFormController::class, 'getModuleRequestStatistics'])
        ->middleware('both.service.role:hod,divisional_director,dict,ict_officer')
        ->name('both-service-form.module-requests.statistics');
});
```

## Database Schema

The endpoints work with the existing `user_access` table structure:

```sql
-- Key columns for module requests
wellsoft_modules JSON NULL,
jeeva_modules JSON NULL,
module_requested_for VARCHAR(255) NULL,
request_type JSON NULL,
hod_signature_path VARCHAR(255) NULL,
access_type VARCHAR(255) DEFAULT 'permanent',
temporary_until DATE NULL,
status VARCHAR(255) DEFAULT 'pending'
```

## Future Enhancements

1. **Real-time Updates**: WebSocket integration for live updates
2. **Advanced Filtering**: Date range, department, and custom filters
3. **Export Functionality**: PDF/Excel export of request data
4. **Bulk Operations**: Approve/reject multiple requests at once
5. **Notification System**: Automated alerts for pending requests
