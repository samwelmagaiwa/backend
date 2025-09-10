# Repository Guidelines

## Project Structure & Module Organization

**Backend (Laravel API):** `backend/` contains the Laravel 12 API with controllers in `app/Http/Controllers/Api/v1/`, models in `app/Models/`, and database migrations in `database/migrations/`. **Frontend (Vue.js):** `frontend/` contains the Vue 3 application with components in `src/components/`, views in `src/components/views/`, and services in `src/services/`. **Key directories:** API routes (`backend/routes/api.php`), PDF templates (`backend/resources/views/pdf/`), signature storage (`backend/storage/app/`), and frontend assets (`frontend/public/`).

## Build, Test, and Development Commands

```bash
# Start all services (API, queue worker, frontend)
npm run dev

# Start individual services
npm run dev:api          # Laravel API server (port 8000)
npm run dev:queue        # Laravel queue worker
npm run dev:frontend     # Vue.js dev server (port 8080)

# Backend commands
cd backend
php artisan serve        # Start Laravel API
php artisan migrate      # Run database migrations
php artisan queue:listen # Start queue worker

# Frontend commands
cd frontend
npm run serve           # Start Vue dev server
npm run build          # Build for production
npm run lint           # Run ESLint
```

## Coding Style & Naming Conventions

- **Indentation:** 2 spaces for Vue.js/JavaScript, 4 spaces for PHP
- **File naming:** PascalCase for Vue components (`UserDashboard.vue`), snake_case for PHP files (`user_access_table.php`)
- **Function/variable naming:** camelCase for JavaScript (`formData`), snake_case for PHP (`user_access`)
- **Linting:** ESLint + Prettier for frontend, Laravel Pint for backend

## Testing Guidelines

- **Framework:** Pest for backend testing (Laravel), no frontend testing framework currently configured
- **Test files:** Located in `backend/tests/Feature/` following Laravel conventions
- **Running tests:** `cd backend && php artisan test`
- **Coverage:** No specific requirements defined

## Commit & Pull Request Guidelines

- **Commit format:** Descriptive messages with feature context (e.g., "booking service processing", "additon of modernsidebar")
- **PR process:** Direct commits to main branch (no formal PR process observed)
- **Branch naming:** No specific convention enforced

---

# Repository Tour

## 🎯 What This Repository Does

**lara-API-vue** is a comprehensive hospital management system for Muhimbili National Hospital that streamlines access requests and device booking services. It handles staff requests for critical hospital systems (Jeeva, Wellsoft, Internet Access) and manages ICT equipment reservations through a unified digital platform.

**Key responsibilities:**
- Process combined access requests for multiple hospital systems through a single form
- Manage device booking services with inventory tracking and approval workflows
- Provide role-based dashboards for different hospital staff levels (Admin, ICT Officers, HODs, Staff)
- Generate official PDF documents with digital signatures for audit trails

---

## 🏗️ Architecture Overview

### System Context
```
[Hospital Staff] → [Vue.js Frontend] → [Laravel API Backend] → [MySQL Database]
                        ↓                      ↓
                   [Role-based UI]      [PDF Generation & Signatures]
                        ↓                      ↓
                   [Request Forms]      [File Storage & Inventory]
```

### Key Components
- **Authentication System** - Laravel Sanctum-based API authentication with multi-role support (admin, staff, ict_officer, head_of_department, divisional_director, ict_director)
- **Combined Access Request Management** - Unified form system allowing staff to request multiple services (Jeeva, Wellsoft, Internet) in a single submission with JSON-based storage
- **Device Booking Service** - Equipment reservation system with real-time inventory tracking, availability checking, and ICT approval workflows
- **User Onboarding System** - Multi-step onboarding flow with terms acceptance, ICT policy acknowledgment, and declaration submission
- **PDF Generation Engine** - Automated form generation using DomPDF for official documentation with embedded signatures
- **Digital Signature Management** - Secure signature capture, storage, and validation system organized by user PF numbers

### Data Flow
1. **User Authentication** - Staff login through Vue.js frontend, authenticated via Laravel Sanctum API tokens with role-based redirects
2. **Request Submission** - Users submit combined access requests or device bookings through role-specific forms with real-time validation
3. **Approval Workflow** - Requests automatically route to appropriate approvers (ICT Officers, HODs, Directors) based on request type and organizational hierarchy
4. **Inventory Management** - Device bookings check real-time availability, reserve inventory, and handle out-of-stock scenarios with queue management
5. **Document Generation** - Approved requests generate official PDF documents with embedded signatures and timestamps for compliance

---

## 📁 Project Structure [Partial Directory Tree]

```
lara-API-vue/
├── backend/                    # Laravel 12 API backend
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/Api/v1/  # API controllers
│   │   │   │   ├── UserAccessController.php      # Combined access request handling
│   │   │   │   ├── BookingServiceController.php  # Device booking with inventory
│   │   │   │   ├── AuthController.php            # Authentication & sessions
│   │   │   │   ├── ICTApprovalController.php     # ICT approval workflows
│   │   │   │   ├── AdminController.php           # Admin dashboard functionality
│   │   │   │   └── OnboardingController.php      # User onboarding flow
│   │   │   ├── Middleware/          # Custom middleware for role checking
│   │   │   └── Requests/            # Form validation requests
│   │   ├── Models/                  # Eloquent models
│   │   │   ├── User.php             # User model with multi-role relationships
│   │   │   ├── UserAccess.php       # Combined access requests (JSON storage)
│   │   │   ├── BookingService.php   # Device booking with inventory links
│   │   │   ├── DeviceInventory.php  # ICT equipment inventory management
│   │   │   ├── Department.php       # Hospital departments with HOD assignments
│   │   │   └── Role.php             # Role-based permissions system
│   │   └── Services/                # Business logic services
│   │       └── SignatureService.php # Digital signature handling
│   ├── database/
│   │   ├── migrations/              # Database schema migrations
│   │   └── seeders/                 # Database seeders for roles/departments
│   ├── resources/views/pdf/         # PDF templates for document generation
│   ├── routes/api.php               # API route definitions with role middleware
│   └── storage/app/                 # File storage for signatures and documents
├── frontend/                   # Vue.js 3 frontend application
│   ├── src/
│   │   ├── components/              # Vue components
│   │   │   ├── admin/               # Admin-specific components
│   │   │   ├── onboarding/          # User onboarding flow components
│   │   │   ├── views/               # Main application views
│   │   │   │   ├── forms/           # Request forms
│   │   │   │   │   ├── UserCombinedAccessForm.vue  # Main combined form
│   │   │   │   │   └── declarationForm.vue         # Declaration form
│   │   │   │   ├── booking/         # Device booking components
│   │   │   │   │   └── BookingService.vue          # Device booking interface
│   │   │   │   ├── requests/        # Request management views
│   │   │   │   └── ict-approval/    # ICT approval interfaces
│   │   │   ├── ModernSidebar.vue    # Role-based navigation sidebar
│   │   │   └── UserDashboard.vue    # Staff dashboard
│   │   ├── router/                  # Vue Router with role-based guards
│   │   ├── services/                # API service layer
│   │   │   ├── userCombinedAccessService.js  # Combined access API calls
│   │   │   └── bookingService.js             # Booking API calls
│   │   ├── stores/                  # Pinia state management
│   │   │   ├── auth.js              # Authentication state
│   │   │   └── sidebar.js           # Sidebar state
│   │   └── utils/                   # Utility functions
│   │       ├── permissions.js       # Role and permission constants
│   │       └── performance.js       # Performance optimization utilities
│   ├── public/                      # Static assets
│   │   └── assets/images/           # Hospital logos and branding
│   └── package.json                 # Frontend dependencies
├── dev.sh                      # Development startup script
├── package.json                # Root package.json for concurrent development
└── README.md                   # Project documentation
```

### Key Files to Know

| File | Purpose | When You'd Touch It |
|------|---------|---------------------|
| `backend/routes/api.php` | API route definitions with role middleware | Adding new endpoints or modifying access permissions |
| `backend/app/Models/UserAccess.php` | Combined access request data model with JSON storage | Modifying access request structure or adding new services |
| `backend/app/Models/BookingService.php` | Device booking with inventory integration | Changing booking functionality or device management |
| `frontend/src/components/views/forms/UserCombinedAccessForm.vue` | Main combined access form | Modifying the unified request interface or adding services |
| `frontend/src/components/views/booking/BookingService.vue` | Device booking interface with real-time availability | Updating booking UI or inventory display |
| `frontend/src/router/index.js` | Frontend routing with role-based guards | Adding new pages or modifying access permissions |
| `backend/app/Http/Controllers/Api/v1/UserAccessController.php` | Combined access request API logic | Implementing new access request features or approval workflows |
| `backend/app/Http/Controllers/Api/v1/BookingServiceController.php` | Device booking API with inventory management | Adding booking features, inventory tracking, or approval logic |
| `frontend/src/stores/auth.js` | Pinia authentication state management | Modifying authentication flow or user session handling |
| `backend/database/migrations/` | Database schema definitions | Adding new tables, columns, or modifying data structure |

---

## 🔧 Technology Stack

### Core Technologies
- **Language:** PHP 8.2+ - Modern PHP with strong typing and enhanced performance features
- **Backend Framework:** Laravel 12 - Latest Laravel with improved routing, middleware, and API features
- **Frontend Framework:** Vue.js 3 - Composition API with improved reactivity and performance
- **Database:** MySQL - Reliable relational database optimized for healthcare data with proper indexing
- **Authentication:** Laravel Sanctum - API token-based authentication with role-based access control

### Key Libraries
- **barryvdh/laravel-dompdf** - PDF generation for official request documents with embedded signatures and hospital branding
- **Vue Router 4** - Client-side routing with role-based navigation guards and dynamic route loading
- **Pinia** - Modern state management for Vue.js replacing Vuex with better TypeScript support
- **Tailwind CSS** - Utility-first CSS framework for responsive medical-themed UI design
- **Axios** - HTTP client for API communication with request/response interceptors

### Development Tools
- **Composer** - PHP dependency management with autoloading and package optimization
- **NPM** - Node.js package management for frontend dependencies and build tools
- **Vue CLI** - Vue.js development tooling with hot reload, build optimization, and code splitting
- **Laravel Artisan** - Command-line interface for migrations, seeders, and custom commands
- **ESLint + Prettier** - Code formatting and linting for consistent frontend code style
- **Laravel Pint** - PHP code style fixer following Laravel conventions

### Performance Optimizations
- **Service Worker** - Caching strategies for offline functionality and faster subsequent loads
- **Image Optimization** - Automated compression and optimization scripts for hospital assets
- **Code Splitting** - Dynamic imports and lazy loading for faster initial load times
- **Database Indexing** - Optimized indexes on frequently queried fields (status, dates, user relationships)

---

## 🌐 External Dependencies

### Required Services
- **MySQL Database** - Primary data storage for users, requests, bookings, device inventory, and audit trails
- **File System Storage** - Local storage for digital signatures, PDF documents, and uploaded files organized by user PF numbers
- **SMTP Server** - Email notifications for request status updates and approval workflows (configurable via Laravel Mail)

### Optional Integrations
- **Redis** - Caching and session storage for improved performance (configurable in Laravel cache config)
- **AWS S3** - Cloud file storage alternative for signatures and documents (configurable via Laravel filesystem)

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

# Required - Authentication & CORS
SANCTUM_STATEFUL_DOMAINS=localhost:8080
CORS_ALLOWED_ORIGINS=http://localhost:8080,http://127.0.0.1:8080
CORS_SUPPORTS_CREDENTIALS=true

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
VUE_APP_API_TIMEOUT=30000
```

---

## 🔄 Common Workflows

### Combined Access Request Workflow
1. **Staff Login** - User authenticates through Vue.js frontend with automatic role-based dashboard redirect
2. **Service Selection** - Staff selects multiple services (Jeeva, Wellsoft, Internet) in a single combined form with real-time validation
3. **Digital Signature** - System captures and validates digital signature with automatic PF number organization
4. **Request Submission** - Combined request stored as JSON array allowing multiple services in one database record
5. **Approval Routing** - Request automatically routes to appropriate approvers based on service types and department hierarchy
6. **PDF Generation** - Approved requests generate official PDF documents with embedded signatures for compliance
7. **Status Tracking** - Real-time status updates with detailed approval workflow visibility

**Code path:** `UserCombinedAccessForm.vue` → `UserAccessController@store` → `UserAccess Model (JSON storage)` → `PDF Generation Service`

### Device Booking Workflow
1. **Equipment Selection** - Staff selects device from real-time inventory with availability checking and out-of-stock handling
2. **Booking Validation** - System prevents multiple pending requests per user and validates booking conflicts
3. **Inventory Reservation** - Available devices are immediately reserved, out-of-stock devices enter queue with return notifications
4. **ICT Approval Process** - All booking requests require ICT Officer approval with detailed review interface
5. **Device Tracking** - System tracks device collection, usage period, return status, and overdue management
6. **Return Processing** - Automated return tracking with inventory restoration and condition reporting

**Code path:** `BookingService.vue` → `BookingServiceController@store` → `DeviceInventory availability check` → `ICT Approval Queue`

---

## 📈 Performance & Scale

### Performance Considerations
- **Database Optimization** - Comprehensive indexing on user_id, status, booking_date, and department relationships for fast queries
- **JSON Storage Efficiency** - Combined access requests use JSON columns to reduce table complexity while maintaining query performance
- **Frontend Optimization** - Code splitting, lazy loading, and component caching with service worker implementation
- **API Caching** - Laravel caching for frequently accessed data like departments, device types, and user roles
- **File Storage Organization** - Signatures organized by PF number directories for efficient retrieval and management

### Monitoring
- **Request Tracking** - Comprehensive logging of all access requests, booking activities, and approval workflows with audit trails
- **User Activity Monitoring** - Session management, role changes, and security event tracking for compliance
- **System Health Checks** - Built-in health endpoints for database connectivity and system status monitoring
- **Performance Metrics** - Frontend performance monitoring with PerformanceMonitor component and backend query optimization

### Scalability Features
- **Multi-Role Architecture** - Flexible role system supporting complex organizational hierarchies with many-to-many relationships
- **JSON-based Request Types** - Extensible schema allowing easy addition of new services without database migrations
- **Modular Component Design** - Reusable Vue components for easy feature expansion and maintenance
- **API Versioning** - Structured API versioning (v1) for backward compatibility and future enhancements
- **Inventory Management** - Scalable device tracking system with real-time availability and queue management

---

## 🚨 Things to Be Careful About

### 🔒 Security Considerations
- **Role-based Access Control** - Strict role validation with middleware protection ensures users only access appropriate features and data
- **Digital Signature Security** - Signatures stored with proper file permissions, organized by PF numbers, and validated for authenticity
- **Healthcare Data Privacy** - Sensitive medical system access data requires careful handling with proper encryption and access logging
- **API Security** - All endpoints protected with Sanctum authentication, role-based middleware, and request validation
- **File Upload Validation** - Strict validation of signature uploads with file type, size, and content verification to prevent security vulnerabilities

### Database Considerations
- **JSON Column Handling** - UserAccess.request_type uses JSON for flexibility but requires careful migration planning and query optimization
- **Signature File Management** - File paths must be properly validated to prevent directory traversal attacks and ensure proper cleanup
- **Role System Migration** - Transition from single-role to multi-role system requires careful data migration and backward compatibility
- **Inventory Consistency** - Device booking conflicts prevented through proper transaction handling and availability checking
- **Audit Trail Integrity** - Request status changes and approvals must maintain complete audit trails for compliance

### Performance Considerations
- **File Storage Growth** - Signature files and PDFs accumulate over time; implement cleanup strategies and archiving policies
- **Database Growth Management** - Request and booking tables grow continuously; consider partitioning and archiving strategies for historical data
- **Cache Invalidation** - Ensure proper cache invalidation when device inventory, user roles, or department structures change
- **Concurrent Booking Handling** - Multiple users booking same device requires proper locking mechanisms and queue management

*Updated at: 2025-01-27 UTC*
*Last commit: 227ff96 - booking service processing*