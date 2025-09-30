# User Access Management API Documentation

## Overview

The User Access Management API is a comprehensive Laravel-based REST API that provides endpoints for managing user access requests, module permissions, device bookings, and workflow approvals within an organization.

## API Documentation Access

### Swagger UI Interface
Access the interactive API documentation at:
```
http://your-domain/api/documentation
```

### JSON Schema
Get the OpenAPI 3.0 JSON schema at:
```
http://your-domain/api/api-docs
http://your-domain/api/docs.json
```

## API Features

### 🔐 Authentication & Authorization
- **Sanctum-based API authentication**
- **Role-based access control** (RBAC)
- **Multi-session support**
- **Token-based security**

### 📋 Core Functionality

#### User Access Management
- Submit access requests for Wellsoft, Jeeva, and Internet services
- Digital signature support
- Multi-service request handling
- Status tracking and filtering

#### Device Booking System
- ICT device reservation and management
- Device inventory tracking
- Approval workflow integration
- Condition assessment features

#### Workflow Management
- Multi-level approval processes
- HOD (Head of Department) approvals
- Divisional Director approvals
- ICT Director and Officer implementations

#### Administrative Features
- User management and role assignments
- Department management
- System statistics and reporting
- Bulk operations support

## 📊 **COMPREHENSIVE API STATISTICS - MAJOR UPDATE**

### 🎆 **Complete System Coverage**
- **Total Explicit Routes**: **265** (Complete system analysis)
- **Total Controllers**: **39** (Every controller examined)
- **Total Public Methods**: **249** (Full method inventory)
- **Resource Routes**: **3** (Each provides 7 RESTful endpoints)
- **Total Estimated Endpoints**: **285+**

### **HTTP Methods Distribution**
- **GET Routes**: **144** (54% - Data retrieval operations)
- **POST Routes**: **94** (36% - Create/Submit operations)  
- **PUT Routes**: **14** (5% - Update operations)
- **PATCH Routes**: **4** (2% - Partial updates)
- **DELETE Routes**: **9** (3% - Delete operations)

### **Access Control Distribution**
- **Public Endpoints**: 8 (Health, Login, Registration)
- **Authenticated Endpoints**: 257 (Require valid token)
- **Admin-Only Endpoints**: 45+ (System administration)
- **Role-Specific Endpoints**: 180+ (HOD, ICT Officer, Head of IT, etc.)

## 🏗️ **API Architecture - 12 Major Categories**

### **1. 🔐 Authentication & Security** (12 endpoints)
| Method | Endpoint | Description | Access |
|--------|----------|-------------|--------|
| POST | `/api/login` | User authentication | Public |
| POST | `/api/register` | User registration | Public |
| POST | `/api/logout` | Current session logout | Auth |
| POST | `/api/logout-all` | All sessions logout | Auth |
| GET | `/api/sessions` | List active sessions | Auth |
| POST | `/api/sessions/revoke` | Revoke specific session | Auth |
| GET | `/api/current-user` | Get current user | Auth |
| GET | `/api/role-redirect` | Role-based redirect | Auth |
| GET | `/api/user` | Current user profile | Auth |

### **2. 👥 User Management & Administration** (35+ endpoints)
| Method | Endpoint | Description | Access |
|--------|----------|-------------|--------|
| GET | `/api/admin/users` | List all users | Admin |
| POST | `/api/admin/users` | Create new user | Admin |
| GET | `/api/admin/users/{user}` | Get specific user | Admin |
| PUT | `/api/admin/users/{user}` | Update user | Admin |
| DELETE | `/api/admin/users/{user}` | Delete user | Admin |
| PATCH | `/api/admin/users/{user}/toggle-status` | Toggle user status | Admin |
| GET | `/api/admin/departments` | List departments | Admin |
| POST | `/api/admin/departments` | Create department | Admin |

### **3. 📝 User Access Request System** (25+ endpoints)
| Method | Endpoint | Description | Access |
|--------|----------|-------------|--------|
| GET | `/api/v1/user-access` | List user requests | Auth |
| POST | `/api/v1/user-access` | Submit access request | Auth |
| GET | `/api/v1/user-access/{id}` | Get specific request | Auth |
| PUT | `/api/v1/user-access/{id}` | Update request | Auth |
| DELETE | `/api/v1/user-access/{id}` | Delete request | Auth |
| POST | `/api/v1/combined-access` | Submit combined request | Auth |
| GET | `/api/user-access-workflow` | List workflow requests | Auth |
| POST | `/api/user-access-workflow/{id}/approve/hod` | HOD approval | HOD |

### **4. 📱 Device Booking & Inventory** (40+ endpoints)
| Method | Endpoint | Description | Access |
|--------|----------|-------------|--------|
| GET | `/api/booking-service/bookings` | List device bookings | Auth |
| POST | `/api/booking-service/bookings` | Create booking | Auth |
| PUT | `/api/booking-service/bookings/{id}` | Update booking | Auth |
| POST | `/api/booking-service/bookings/{id}/approve` | Approve booking | Admin |
| GET | `/api/device-inventory` | List inventory | Admin |
| POST | `/api/device-inventory` | Add device | Admin |
| GET | `/api/ict-approval/device-requests` | ICT approval requests | ICT Officer |
| POST | `/api/ict-approval/device-requests/{id}/approve` | ICT approve | ICT Officer |

### **5. 🎯 Onboarding & Declaration System** (15+ endpoints)
| Method | Endpoint | Description | Access |
|--------|----------|-------------|--------|
| GET | `/api/onboarding/status` | Get onboarding status | Auth |
| POST | `/api/onboarding/accept-terms` | Accept terms | Auth |
| POST | `/api/onboarding/complete` | Complete onboarding | Auth |
| POST | `/api/declaration/submit` | Submit declaration | Auth |
| GET | `/api/declaration/departments` | Get departments | Auth |

### **6. 👔 Multi-Level Approval Workflow** (45+ endpoints)
| Method | Endpoint | Description | Access |
|--------|----------|-------------|--------|
| GET | `/api/hod/combined-access-requests` | List HOD requests | HOD |
| POST | `/api/hod/combined-access-requests/{id}/approve` | HOD approve | HOD |
| GET | `/api/divisional/combined-access-requests` | Divisional requests | Div Director |
| GET | `/api/dict/combined-access-requests` | ICT Director requests | ICT Director |
| GET | `/api/both-service-form` | Multi-stage forms | Multi-Role |

### **7. 🔧 Head of IT & ICT Officer Management** (35+ endpoints)
| Method | Endpoint | Description | Access |
|--------|----------|-------------|--------|
| GET | `/api/head-of-it/all-requests` | All IT requests | Head of IT |
| POST | `/api/head-of-it/assign-task` | Assign task | Head of IT |
| GET | `/api/ict-officer/access-requests` | ICT requests | ICT Officer |
| POST | `/api/ict-officer/access-requests/{id}/grant-access` | Grant access | ICT Officer |
| GET | `/api/ict-officer/dashboard` | ICT dashboard | ICT Officer |

### **8. 📊 Status Tracking & Dashboards** (20+ endpoints)
| Method | Endpoint | Description | Access |
|--------|----------|-------------|--------|
| GET | `/api/request-status` | Request status list | Auth |
| GET | `/api/user/dashboard-stats` | User dashboard stats | Auth |
| GET | `/api/notifications/pending-count` | Pending count | Auth |
| GET | `/api/notifications` | List notifications | Auth |

### **🎨 View Complete Documentation**
For the **full 265+ endpoint documentation** with detailed parameters, examples, and role requirements:
- **Complete API Reference**: `backend/COMPLETE_API_DOCUMENTATION.md`
- **Interactive Testing**: `/api/documentation`
- **JSON Schema**: `/api/api-docs`

## Authentication

The API uses Laravel Sanctum for authentication. Include the bearer token in the Authorization header:

```bash
Authorization: Bearer {your-token-here}
```

### Getting a Token
```bash
curl -X POST http://your-domain/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "user@example.com",
    "password": "your-password"
  }'
```

## Request/Response Format

### Standard Response Structure
```json
{
  "success": true,
  "data": { ... },
  "message": "Operation completed successfully"
}
```

### Error Response Structure
```json
{
  "success": false,
  "message": "Error description",
  "errors": {
    "field": ["Validation error message"]
  }
}
```

## Role-Based Access Control

### Available Roles
- **admin**: Full system access
- **ict_director**: ICT directorate oversight
- **ict_officer**: ICT operations and device management
- **head_of_department**: Department-level approvals
- **divisional_director**: Divisional oversight
- **staff**: Basic user access

### Permission System
Each role has specific permissions that control access to different API endpoints and operations.

## Data Models

### User Access Request
```json
{
  "id": 1,
  "pf_number": "PF12345",
  "staff_name": "John Doe",
  "phone_number": "+1234567890",
  "department_id": 1,
  "request_type": ["wellsoft_access", "internet_access_request"],
  "status": "pending",
  "signature_path": "/signatures/user_123_signature.png",
  "created_at": "2024-01-15T10:30:00Z"
}
```

### Device Booking
```json
{
  "id": 1,
  "borrower_name": "John Doe",
  "device_type": "Laptop",
  "device_inventory_id": 5,
  "booking_date": "2024-01-20",
  "return_date": "2024-01-25",
  "purpose": "Official training",
  "status": "pending"
}
```

## Status Codes

The API uses standard HTTP status codes:

- **200**: Success
- **201**: Created successfully
- **400**: Bad request
- **401**: Unauthorized
- **403**: Forbidden
- **404**: Not found
- **422**: Validation error
- **500**: Internal server error

## Rate Limiting

The API implements rate limiting to prevent abuse:
- **Login attempts**: 5 per minute
- **General API calls**: 60 per minute
- **Sensitive operations**: 10 per minute

## File Uploads

For endpoints that accept file uploads (like digital signatures):
- Use `multipart/form-data` content type
- Maximum file size: 2MB
- Supported formats: PNG, JPG, JPEG
- Files are validated and stored securely

## Filtering and Pagination

### Query Parameters
Most list endpoints support:
- `page`: Page number (default: 1)
- `per_page`: Items per page (default: 15, max: 100)
- `sort_by`: Field to sort by
- `sort_order`: asc or desc
- `search`: Search term
- `status`: Filter by status

### Example
```bash
GET /api/v1/user-access?page=2&per_page=20&status=pending&sort_by=created_at&sort_order=desc
```

## Development Setup

### Prerequisites
- PHP 8.2+
- Laravel 12.0+
- MySQL/PostgreSQL
- Composer

### Installation
1. Clone the repository
2. Install dependencies: `composer install`
3. Configure environment: Copy `.env.example` to `.env`
4. Generate application key: `php artisan key:generate`
5. Run migrations: `php artisan migrate`
6. Start the server: `php artisan serve`

### Accessing Documentation
Once the server is running, visit:
- Main API: `http://localhost:8000/api`
- Documentation: `http://localhost:8000/api/documentation`
- JSON Schema: `http://localhost:8000/api/api-docs`

## Testing

Use tools like Postman, curl, or the built-in Swagger UI interface to test the API endpoints.

### Example Test Request
```bash
# Get user access requests
curl -X GET http://localhost:8000/api/v1/user-access \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

## Support and Contact

For API support and inquiries:
- **Email**: admin@example.com
- **Documentation**: Available in the Swagger UI interface
- **Version**: 1.0.0

## Security Considerations

- All sensitive endpoints require authentication
- File uploads are validated and sanitized
- Rate limiting prevents abuse
- CORS is properly configured
- SQL injection protection via Eloquent ORM
- XSS protection enabled
- CSRF protection for web routes

---

*Last updated: December 2024*
