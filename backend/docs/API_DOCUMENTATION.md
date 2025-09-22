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

## API Endpoints Summary

### Authentication Endpoints
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/login` | User authentication |
| POST | `/api/logout` | Current session logout |
| POST | `/api/logout-all` | All sessions logout |
| GET | `/api/user` | Current user profile |

### User Access Requests
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/v1/user-access` | List user access requests |
| POST | `/api/v1/user-access` | Create new access request |
| GET | `/api/v1/user-access/{id}` | Get specific request |
| PUT | `/api/v1/user-access/{id}` | Update request |

### Device Booking
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/booking-service/bookings` | List device bookings |
| POST | `/api/booking-service/bookings` | Create booking |
| PUT | `/api/booking-service/bookings/{id}` | Update booking |
| POST | `/api/booking-service/bookings/{id}/approve` | Approve booking |

### ICT Approval System
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/ict-approval/device-requests` | Get requests for ICT approval |
| GET | `/api/ict-approval/device-requests/{id}` | Get specific request details |
| POST | `/api/ict-approval/device-requests/{id}/approve` | ICT approve request |
| POST | `/api/ict-approval/device-requests/{id}/reject` | ICT reject request |

### Administrative Endpoints
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/admin/users` | Manage users |
| GET | `/api/admin/departments` | Manage departments |
| GET | `/api/device-inventory` | Device inventory management |

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
