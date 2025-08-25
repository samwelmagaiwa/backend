# Repository Tour

## 🎯 What This Repository Does

**lara-API-vue** is a comprehensive hospital management system for handling access requests and device booking services. It streamlines the process of requesting access to critical hospital systems (Jeeva, Wellsoft, Internet Access) and managing device bookings for hospital equipment.

**Key responsibilities:**
- Manage user access requests for hospital systems (Jeeva, Wellsoft, Internet Access)
- Handle device booking services for hospital equipment (projectors, monitors, CPUs, etc.)
- Provide role-based access control and approval workflows for different hospital departments

---

## 🏗️ Architecture Overview

### System Context
```
[Hospital Staff] → [Vue.js Frontend] → [Laravel API Backend] → [MySQL Database]
                        ↓                      ↓
                   [Role-based UI]      [PDF Generation]
                        ↓                      ↓
                   [Request Forms]      [File Storage (Signatures)]
```

### Key Components
- **Authentication System** - Laravel Sanctum-based API authentication with role-based access control
- **Combined Access Request Management** - Unified form system for Jeeva, Wellsoft, and Internet access requests with JSON-based request types
- **Device Booking Service** - Equipment reservation system with approval processes and return tracking
- **User Onboarding** - Multi-step onboarding flow with terms acceptance and ICT policy acknowledgment
- **PDF Generation** - Automated form generation using DomPDF for official documentation
- **Signature Management** - Digital signature capture and storage for request authentication
- **Performance Monitoring** - Built-in performance tracking and optimization tools

### Data Flow
1. **User Authentication** - Staff login through Vue.js frontend, authenticated via Laravel Sanctum API tokens
2. **Request Submission** - Users submit combined access requests or device bookings through role-specific forms
3. **Approval Workflow** - Requests route to appropriate approvers (ICT Officers, HODs, Directors) based on request type
4. **PDF Generation** - Approved requests generate official PDF documents with signatures and timestamps
5. **Status Tracking** - Real-time status updates and notifications throughout the approval process

---

## 📁 Project Structure [Partial Directory Tree]

```
lara-API-vue/
├── backend/                    # Laravel API backend
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/Api/v1/  # API controllers for access requests, booking, auth
│   │   │   │   ├── UserAccessController.php      # Combined access request handling
│   │   │   │   ├── BookingServiceController.php  # Device booking management
│   │   │   │   ├── AuthController.php            # Authentication & session management
│   │   │   │   ├── AdminController.php           # Admin dashboard functionality
│   │   │   │   └── OnboardingController.php      # User onboarding flow
│   │   │   ├── Middleware/          # Custom middleware
│   │   │   └── Requests/            # Form validation requests
│   │   ├── Models/                  # Eloquent models
│   │   │   ├── User.php             # User model with role relationships
│   │   │   ├── UserAccess.php       # Combined access requests (JSON-based)
│   │   │   ├── BookingService.php   # Device booking model
│   │   │   ├── Department.php       # Hospital departments
│   │   │   ├── Role.php             # User roles and permissions
│   │   │   └── UserOnboarding.php   # Onboarding progress tracking
│   │   └── Services/                # Business logic services
│   ├── config/                      # Laravel configuration files
│   ├── database/
│   │   ├── migrations/              # Database schema migrations
│   │   └── seeders/                 # Database seeders
│   ├── resources/views/pdf/         # PDF templates for form generation
│   ├── routes/api.php               # API route definitions
│   └── storage/app/                 # File storage (signatures, documents)
├── frontend/                   # Vue.js frontend application
│   ├── src/
│   │   ├── components/              # Vue components
│   │   │   ├── admin/               # Admin-specific components
│   │   │   ├── onboarding/          # User onboarding flow
│   │   │   ├── views/               # Main application views
│   │   │   │   ├── forms/           # Access request and booking forms
│   │   │   │   │   ├── UserCombinedAccessForm.vue  # Main combined form
│   │   │   │   │   └── declarationForm.vue         # Declaration form
│   │   │   │   ├── booking/         # Device booking components
│   │   │   │   │   └── BookingService.vue          # Device booking interface
│   │   │   │   └── requests/        # Request management views
│   │   │   ├── tables/              # Data table components
│   │   │   ├── PerformanceMonitor.vue  # Performance tracking component
│   │   │   └── LoadingSpinner.vue      # Loading state component
│   │   ├── router/                  # Vue Router configuration
│   │   ├── services/                # API service layer
│   │   │   ├── userCombinedAccessService.js  # Combined access API calls
│   │   │   └── bookingService.js             # Booking API calls
│   │   ├── utils/                   # Utility functions and helpers
│   │   │   └── performance.js       # Performance optimization utilities
│   │   └── composables/             # Vue 3 composition API composables
│   ├── public/                      # Static assets
│   │   ├── sw.js                    # Service worker for performance
│   │   └── index.html               # Main HTML template
│   ├── scripts/                     # Build and optimization scripts
│   │   └── optimize-images.js       # Image optimization script
│   ├── PERFORMANCE_OPTIMIZATION.md # Performance optimization documentation
│   └── package.json                 # Frontend dependencies
└── README.md                   # Project documentation
```

### Key Files to Know

| File | Purpose | When You'd Touch It |
|------|---------|---------------------|
| `backend/routes/api.php` | API route definitions | Adding new endpoints or modifying existing routes |
| `backend/app/Models/UserAccess.php` | Combined access request data model | Modifying access request structure or business logic |
| `backend/app/Models/BookingService.php` | Device booking data model | Changing booking functionality or device types |
| `frontend/src/components/views/forms/UserCombinedAccessForm.vue` | Main combined access form | Modifying the unified request interface |
| `frontend/src/components/views/booking/BookingService.vue` | Device booking interface | Updating booking functionality or UI |
| `frontend/src/router/index.js` | Frontend routing configuration | Adding new pages or modifying access permissions |
| `backend/app/Http/Controllers/Api/v1/UserAccessController.php` | Combined access request API logic | Implementing new access request features |
| `backend/app/Http/Controllers/Api/v1/BookingServiceController.php` | Device booking API logic | Adding booking features or validation |
| `backend/composer.json` | Backend dependencies | Adding new Laravel packages or updating versions |
| `frontend/package.json` | Frontend dependencies | Adding new Vue.js packages or updating versions |

---

## 🔧 Technology Stack

### Core Technologies
- **Language:** PHP (8.2+) - Modern PHP with strong typing and performance optimizations
- **Backend Framework:** Laravel 12 - Latest Laravel with enhanced features and security
- **Frontend Framework:** Vue.js 3 - Composition API and improved performance
- **Database:** MySQL - Reliable relational database for healthcare data
- **Authentication:** Laravel Sanctum - API token-based authentication system

### Key Libraries
- **barryvdh/laravel-dompdf** - PDF generation for official request documents and forms
- **Vue Router 4** - Client-side routing for single-page application navigation
- **Tailwind CSS** - Utility-first CSS framework for responsive and modern UI design
- **Axios** - HTTP client for API communication between frontend and backend

### Development Tools
- **Composer** - PHP dependency management and autoloading
- **NPM** - Node.js package management for frontend dependencies
- **Vue CLI** - Vue.js development tooling with hot reload and build optimization
- **Laravel Artisan** - Command-line interface for Laravel development tasks
- **Performance Monitoring** - Built-in performance tracking and optimization tools

### Performance Optimizations
- **Service Worker** - Caching and offline functionality for improved performance
- **Image Optimization** - Automated image compression and optimization scripts
- **Code Splitting** - Dynamic imports and lazy loading for faster initial load times
- **Bundle Analysis** - Webpack bundle analyzer for identifying optimization opportunities

---

## 🌐 External Dependencies

### Required Services
- **MySQL Database** - Primary data storage for users, requests, bookings, and system configuration
- **File System Storage** - Local storage for digital signatures, PDF documents, and uploaded files
- **SMTP Server** - Email notifications for request status updates and system alerts (configured via Laravel Mail)

### Optional Integrations
- **Redis** - Caching and session storage for improved performance (configurable)
- **AWS S3** - Cloud file storage alternative for signatures and documents (configurable)

---

### Environment Variables

```bash
# Required - Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=backend
DB_USERNAME=root
DB_PASSWORD=

# Required - Application Configuration
APP_NAME=Laravel
APP_ENV=local
APP_KEY=                    # Generated via php artisan key:generate
APP_DEBUG=true
APP_URL=http://localhost

# Required - Authentication
SANCTUM_STATEFUL_DOMAINS=localhost:8080

# Optional - Mail Configuration
MAIL_MAILER=log
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null

# Optional - File Storage
FILESYSTEM_DISK=local
AWS_ACCESS_KEY_ID=          # If using S3 storage
AWS_SECRET_ACCESS_KEY=      # If using S3 storage

# Frontend Configuration (frontend/.env)
VUE_APP_API_URL=http://localhost:8000/api
VUE_APP_NAME=Mnh Access Management System
VUE_APP_DEBUG=true
VUE_APP_API_TIMEOUT=10000
```

---

## 🔄 Common Workflows

### User Access Request Workflow
1. **Staff Login** - User authenticates through Vue.js frontend with role-based dashboard access
2. **Request Submission** - Staff submits combined access request form with digital signature
3. **Approval Routing** - Request automatically routes to appropriate approver based on request type and department
4. **Review Process** - ICT Officers or Department Heads review and approve/reject requests
5. **PDF Generation** - Approved requests generate official PDF documents for record-keeping
6. **Status Notification** - Users receive real-time updates on request status changes

**Code path:** `UserCombinedAccessForm.vue` → `UserAccessController@store` → `UserAccess Model` → `PDF Generation`

### Device Booking Workflow
1. **Equipment Request** - Staff selects device type, booking dates, and provides justification
2. **Availability Check** - System validates device availability and booking conflicts
3. **Approval Process** - Booking requests require ICT Officer approval before confirmation
4. **Device Tracking** - System tracks device collection, usage period, and return status
5. **Overdue Management** - Automated tracking of overdue devices with notification system

**Code path:** `BookingService.vue` → `BookingServiceController@store` → `BookingService Model` → `Approval Workflow`

---

## 📈 Performance & Scale

### Performance Considerations
- **Database Indexing** - Optimized indexes on frequently queried fields (booking_date, status, user_id)
- **Frontend Optimization** - Code splitting, lazy loading, and component caching for improved load times
- **API Caching** - Laravel caching for frequently accessed data like departments and device types
- **Service Worker** - Caching strategies for offline functionality and faster subsequent loads
- **Image Optimization** - Automated compression and optimization of static assets

### Monitoring
- **Request Tracking** - Comprehensive logging of all access requests and booking activities
- **User Activity** - Session management and user activity tracking for security and auditing
- **System Health** - Laravel logging and error tracking for system maintenance
- **Performance Metrics** - Built-in performance monitoring with PerformanceMonitor component

### Scalability Features
- **JSON-based Request Types** - Flexible schema allowing easy addition of new request types
- **Modular Component Architecture** - Reusable Vue components for easy feature expansion
- **API Versioning** - Structured API versioning (v1) for backward compatibility
- **Role-based Access Control** - Scalable permission system for different user types

---

## 🚨 Things to Be Careful About

### 🔒 Security Considerations
- **Role-based Access** - Strict role validation ensures users only access appropriate features and data
- **Signature Security** - Digital signatures are stored securely with proper file permissions and access controls
- **Data Privacy** - Healthcare data requires careful handling with proper encryption and access logging
- **API Security** - All API endpoints protected with Sanctum authentication and role-based middleware
- **File Upload Validation** - Strict validation of signature uploads to prevent security vulnerabilities

### Database Considerations
- **JSON Column Handling** - UserAccess.request_type uses JSON for flexibility but requires careful migration handling
- **Signature Storage** - File paths must be properly validated to prevent directory traversal attacks
- **Request Type Validation** - Enum constraints ensure data integrity for request types and statuses
- **Booking Conflicts** - Proper validation to prevent double-booking of devices

### Performance Considerations
- **Large File Handling** - Signature files and PDFs can accumulate; implement cleanup strategies
- **Database Growth** - Request and booking tables will grow over time; consider archiving strategies
- **Cache Invalidation** - Ensure proper cache invalidation when data changes

*Updated at: 2025-01-27 UTC*
*Last commit: 1af4329 - sending usercombined and bookingservice data to backend and deleting userjeeva,userwellsoft,userinternetaccess*