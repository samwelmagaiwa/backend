# Declaration API Endpoints

## Base URL
All endpoints are prefixed with `/api/declaration/`

## Authentication
All endpoints require Bearer token authentication:
```
Authorization: Bearer {your-token}
```

---

## 1. Get Departments

**Endpoint:** `GET /api/declaration/departments`

**Description:** Get all active departments with blue color for the declaration form.

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Information and Communication Technology",
      "code": "ICT",
      "full_name": "Information and Communication Technology (ICT)",
      "description": "Manages hospital IT infrastructure and systems",
      "color": "blue"
    },
    {
      "id": 2,
      "name": "Human Resources",
      "code": "HR",
      "full_name": "Human Resources (HR)",
      "description": "Manages staff recruitment, training, and welfare",
      "color": "blue"
    }
  ]
}
```

---

## 2. Submit Declaration

**Endpoint:** `POST /api/declaration/submit`

**Description:** Submit the declaration form with user details and signature.

**Content-Type:** `multipart/form-data` (for file upload)

**Request Body:**
```javascript
const formData = new FormData();
formData.append('fullName', 'John Doe');
formData.append('pfNumber', 'PF001234');
formData.append('department', 'ICT');
formData.append('jobTitle', 'Software Developer');
formData.append('date', '2025-01-27');
formData.append('agreement', 'true');
formData.append('signature', signatureFile); // Optional file
```

**Validation Rules:**
- `fullName`: Required, string, max 255 characters
- `pfNumber`: Required, string, max 100 characters, must be unique
- `department`: Required, string, max 255 characters
- `jobTitle`: Required, string, max 255 characters
- `date`: Required, valid date
- `agreement`: Required, must be true/accepted
- `signature`: Optional file (PNG, JPG, JPEG, PDF, max 5MB)

**Success Response:**
```json
{
  "success": true,
  "message": "Declaration submitted successfully",
  "data": {
    "declaration_id": 123,
    "current_step": "success",
    "next_step": "success",
    "declaration_submitted_at": "2025-01-27T10:30:00.000000Z",
    "ready_for_completion": true
  }
}
```

**Error Response:**
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "pfNumber": ["This PF number has already been used in another declaration."],
    "agreement": ["You must confirm the declaration statement."]
  }
}
```

---

## 3. Get User Declaration

**Endpoint:** `GET /api/declaration/show`

**Description:** Get the current user's declaration details.

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 123,
    "full_name": "John Doe",
    "pf_number": "PF001234",
    "department": "ICT",
    "job_title": "Software Developer",
    "signature_date": "2025-01-27",
    "agreement_accepted": true,
    "has_signature": true,
    "signature_url": "http://localhost:8000/storage/signatures/declarations/declaration_signature_1_1706345400.png",
    "submitted_at": "Jan 27, 2025 at 10:30"
  }
}
```

**Not Found Response:**
```json
{
  "success": false,
  "message": "Declaration not found"
}
```

---

## 4. Check PF Number Availability

**Endpoint:** `POST /api/declaration/check-pf-number`

**Description:** Check if a PF number is available for use.

**Content-Type:** `application/json`

**Request Body:**
```json
{
  "pf_number": "PF001234"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "pf_number": "PF001234",
    "is_available": false,
    "message": "PF number is already in use"
  }
}
```

---

## 5. Get All Declarations (Admin Only)

**Endpoint:** `GET /api/declaration/all`

**Description:** Get all declarations with pagination (Admin access required).

**Response:**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 123,
        "user_id": 1,
        "full_name": "John Doe",
        "pf_number": "PF001234",
        "department": "ICT",
        "job_title": "Software Developer",
        "signature_date": "2025-01-27",
        "agreement_accepted": true,
        "submitted_at": "2025-01-27T10:30:00.000000Z",
        "user": {
          "id": 1,
          "name": "John Doe",
          "email": "john@example.com"
        }
      }
    ],
    "per_page": 20,
    "total": 1
  }
}
```

---

## Alternative Onboarding Endpoint

**Endpoint:** `POST /api/onboarding/submit-declaration`

**Description:** Submit declaration through the onboarding flow (same functionality as `/api/declaration/submit`).

This endpoint maintains backward compatibility with the existing onboarding process.

---

## Error Codes

- `200` - Success
- `422` - Validation Error
- `403` - Unauthorized (Admin only endpoints)
- `404` - Not Found
- `500` - Server Error

---

## Frontend Implementation Example

```javascript
class DeclarationAPI {
  constructor(baseURL, token) {
    this.baseURL = baseURL;
    this.token = token;
  }

  async getDepartments() {
    const response = await fetch(`${this.baseURL}/api/declaration/departments`, {
      headers: {
        'Authorization': `Bearer ${this.token}`,
      }
    });
    return response.json();
  }

  async submitDeclaration(formData) {
    const response = await fetch(`${this.baseURL}/api/declaration/submit`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${this.token}`,
      },
      body: formData // FormData object
    });
    return response.json();
  }

  async checkPfNumber(pfNumber) {
    const response = await fetch(`${this.baseURL}/api/declaration/check-pf-number`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${this.token}`,
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ pf_number: pfNumber })
    });
    return response.json();
  }

  async getUserDeclaration() {
    const response = await fetch(`${this.baseURL}/api/declaration/show`, {
      headers: {
        'Authorization': `Bearer ${this.token}`,
      }
    });
    return response.json();
  }
}

// Usage
const api = new DeclarationAPI('http://localhost:8000', userToken);

// Get departments
const departments = await api.getDepartments();

// Submit declaration
const formData = new FormData();
formData.append('fullName', 'John Doe');
formData.append('pfNumber', 'PF001234');
formData.append('department', 'ICT');
formData.append('jobTitle', 'Software Developer');
formData.append('date', '2025-01-27');
formData.append('agreement', 'true');

const result = await api.submitDeclaration(formData);
```

---

## Notes for Frontend Development

1. **File Upload**: Use `FormData` for declaration submission with signature files
2. **PF Number Validation**: Implement real-time checking as user types
3. **Department Colors**: All departments will have `color: "blue"` as requested
4. **Agreement Checkbox**: Ensure the value is sent as string "true" or boolean `true`
5. **Error Handling**: Display validation errors next to respective form fields
6. **Loading States**: Show loading indicators during API calls
7. **Success Feedback**: Show confirmation message after successful submission