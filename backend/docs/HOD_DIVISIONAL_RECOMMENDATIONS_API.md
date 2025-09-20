# HOD Divisional Recommendations API

This API allows Head of Department (HOD) users to view divisional director recommendations for user access requests from their departments.

## Overview

When a divisional director reviews and provides comments on user access requests, those recommendations are made available to the HOD through this API. This allows HODs to see what divisional directors have recommended for their department's staff requests.

## Authentication

All endpoints require authentication via Laravel Sanctum. The user must have the `head_of_department` role.

## Endpoints

### 1. Get Divisional Recommendations

Retrieves a paginated list of user access requests that have divisional director comments/recommendations.

```http
GET /api/hod/divisional-recommendations
```

#### Query Parameters

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `page` | integer | 1 | Page number for pagination |
| `per_page` | integer | 10 | Number of records per page (max 50) |

#### Response

```json
{
  "success": true,
  "data": [
    {
      "id": 16,
      "pf_number": "TEST001",
      "staff_name": "Test Staff Member",
      "phone_number": "0701234567",
      "department": {
        "id": 2,
        "name": "Human Resources",
        "code": "HR"
      },
      "request_type": ["wellsoft"],
      "status": "divisional_approved",
      "access_type": "permanent",
      "temporary_until": null,
      "module_requested_for": "use",
      "wellsoft_modules_selected": ["patient_registration", "admission"],
      "jeeva_modules_selected": [],
      "internet_purposes": [],
      "divisional_director_name": "Test Divisional Director",
      "divisional_director_comments": "Recommended for approval by divisional director. Staff member has adequate training.",
      "divisional_approved_at": "2025-09-20T16:11:52.000000Z",
      "divisional_director_signature_path": null,
      "hod_name": "Head of HR Department",
      "hod_approved_at": "2025-09-20T16:11:52.000000Z",
      "hod_comments": "Approved by HOD",
      "created_at": "2025-09-20T16:11:52.000000Z",
      "updated_at": "2025-09-20T16:11:52.000000Z"
    }
  ],
  "pagination": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 10,
    "total": 2,
    "from": 1,
    "to": 2
  },
  "summary": {
    "total_recommendations": 2,
    "hod_departments": ["Human Resources"],
    "showing": "2 of 2"
  }
}
```

#### Error Response

```json
{
  "success": false,
  "message": "Access denied. Only Head of Department can view divisional recommendations.",
  "data": [],
  "pagination": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 10,
    "total": 0
  }
}
```

### 2. Get Recommendation Statistics

Retrieves statistical information about divisional director recommendations for the HOD's departments.

```http
GET /api/hod/divisional-recommendations/stats
```

#### Response

```json
{
  "success": true,
  "data": {
    "total_recommendations": 2,
    "recent_recommendations": 2,
    "pending_response": 0,
    "responded_to": 2,
    "departments": ["Human Resources"]
  }
}
```

#### Statistics Fields

| Field | Description |
|-------|-------------|
| `total_recommendations` | Total number of requests with divisional director comments |
| `recent_recommendations` | Number of recommendations in the last 7 days |
| `pending_response` | Number of recommendations that haven't been responded to by HOD |
| `responded_to` | Number of recommendations that HOD has already responded to |
| `departments` | List of department names under this HOD |

## Request Flow

1. **Staff Submission**: Staff member submits a user access request
2. **HOD Approval**: HOD reviews and approves the request
3. **Divisional Review**: Divisional director reviews the request and adds comments/recommendations
4. **HOD View**: HOD can now see the divisional director's recommendations via this API
5. **Further Processing**: Request continues through the workflow (ICT Director, Head of IT, etc.)

## Status Filtering

The API only returns requests that:
- Belong to departments where the authenticated user is HOD
- Have non-empty `divisional_director_comments`
- Are in one of these statuses:
  - `divisional_approved`
  - `divisional_rejected`
  - `pending_ict_director`
  - `ict_director_approved`
  - `ict_director_rejected`
  - `pending_head_it`
  - `head_it_approved`
  - `head_it_rejected`
  - `pending_ict_officer`
  - `implemented`
  - `approved`
  - `rejected`

## Security

- Only users with `head_of_department` role can access these endpoints
- HODs can only see recommendations for their own departments
- All requests are logged for audit purposes

## Usage Example

```javascript
// Fetch divisional recommendations with pagination
const response = await fetch('/api/hod/divisional-recommendations?per_page=10&page=1', {
  headers: {
    'Authorization': `Bearer ${token}`,
    'Accept': 'application/json'
  }
});

const data = await response.json();

if (data.success) {
  console.log(`Found ${data.pagination.total} recommendations`);
  data.data.forEach(recommendation => {
    console.log(`${recommendation.staff_name}: ${recommendation.divisional_director_comments}`);
  });
}

// Get statistics
const statsResponse = await fetch('/api/hod/divisional-recommendations/stats', {
  headers: {
    'Authorization': `Bearer ${token}`,
    'Accept': 'application/json'
  }
});

const statsData = await statsResponse.json();
console.log(`Total: ${statsData.data.total_recommendations}, Recent: ${statsData.data.recent_recommendations}`);
```

## Error Codes

| HTTP Code | Description |
|-----------|-------------|
| 200 | Success |
| 403 | Access denied - user is not an HOD |
| 500 | Internal server error |

## Notes

- The API uses the cleaned-up column names after the recent database schema optimization
- Results are ordered by `divisional_approved_at` descending, then `updated_at` descending
- Maximum 50 records can be requested per page
- All timestamps are returned in ISO 8601 format
