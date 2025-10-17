# WARP.md - MNH User Access Management System

This file provides comprehensive guidance for WARP when working with the MNH (Muhimbili National Hospital) User Access Management System - a Laravel + Vue.js application for managing hospital staff access requests and ICT device bookings.

## 🚀 Quick Start Commands

### First-time setup
```bash
# Backend
cd backend
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
php artisan storage:link

# Frontend
cd ../frontend
npm install

# Root (for concurrently helper)
cd ..
npm install
```

### Development Environment Setup
```bash
# Start all services simultaneously
npm run dev

# Or start services individually:
npm run dev:api          # Laravel API server (port 8000)
npm run dev:queue        # Laravel queue worker
npm run dev:frontend     # Vue.js dev server (port 8080)
```

### Backend Commands (Laravel 12)
```bash
# Navigate to backend directory first
cd backend

# Database operations
php artisan migrate              # Run database migrations
php artisan migrate:fresh       # Fresh migration (drops all tables)
php artisan db:seed             # Seed database with initial data
php artisan migrate:fresh --seed # Fresh migration with seeding

# Development server
php artisan serve               # Start Laravel API (http://localhost:8000)

# Queue management
php artisan queue:listen        # Start queue worker
php artisan queue:work          # Process queue jobs once
php artisan queue:restart       # Restart all queue workers

# Cache management
php artisan route:clear         # Clear route cache
php artisan config:clear        # Clear config cache
php artisan view:clear          # Clear view cache

# Testing (Pest)
php artisan test                # Run all tests
php artisan test --filter=UserAccessTest # Run specific test
```

### Swagger/OpenAPI
```bash
php artisan l5-swagger:generate   # Generate OpenAPI docs
php artisan route:list --path=api # Inspect API routes
```

### Frontend Commands (Vue.js 3)
```bash
# Navigate to frontend directory first
cd frontend

# Development
npm run serve                   # Start Vue dev server (HMR disabled by config)
npm run dev                     # Alias for serve
npm run dev:mobile             # Serve on all network interfaces (0.0.0.0)

# Build operations
npm run build                   # Build for production
npm run build:prod             # Production build with optimizations
npm run build:analyze          # Build with bundle analyzer

> Windows (PowerShell) note: set NODE_ENV before running modern build
```powershell
$env:NODE_ENV = "production"; npm run build:modern
```

# Code quality
npm run lint                    # Run ESLint
npm run lint:fix               # Fix ESLint issues automatically
npm run format                 # Format code with Prettier
npm run format:check           # Check formatting without fixing

# Maintenance
npm run clean:hard             # Clean caches and build artifacts
```

### Single Test Execution
```bash
# Backend - run specific test file
cd backend
php artisan test tests/Feature/UserAccessTest.php

# Backend - run specific test method
php artisan test --filter=test_user_can_create_access_request

# Frontend - no testing framework currently configured
```

## 🏥 System Overview

### What This System Does
**MNH User Access Management System** is a comprehensive hospital management solution that handles:

1. **Combined Access Requests**: Staff can request access to multiple hospital systems in one form:
   - **Jeeva**: Hospital management system
   - **Wellsoft**: Hospital information system  
   - **Internet Access**: Controlled internet access for staff

2. **Device Booking Service**: ICT equipment reservation and inventory management
   - Real-time device availability checking
   - Booking workflow with approvals
   - Device condition assessments (issuing/receiving)

3. **Multi-Stage Approval Workflow**: Role-based approval system:
   - Staff submits request
   - Head of Department (HOD) approval
   - Divisional Director approval
   - ICT Director approval  
   - Head of IT approval
   - ICT Officer implementation

4. **Digital Signature Integration**: 
   - Canvas-based signature capture
   - PDF document generation with embedded signatures
   - Organized by PF (Personal File) numbers

## 🏗 Technical Architecture

### Stack Overview
```
Vue.js 3 (Frontend SPA) ↔ Laravel 12 (REST API) ↔ MySQL Database
        ↓                         ↓                      ↓
   Pinia + Vuex            Laravel Sanctum          Relational + JSON
   TailwindCSS             DomPDF + Queues          Foreign Keys + Indexes
   Axios + Router          Pest Testing             Migration System
```

### Key Technologies
- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: Vue.js 3 with Composition API
- **Database**: MySQL with JSON columns for flexible schema
- **Authentication**: Laravel Sanctum (token-based API auth)
- **PDF Generation**: DomPDF with embedded signatures
- **Testing**: Pest (PHP), ESLint + Prettier (JavaScript)
- **Styling**: TailwindCSS with custom hospital branding
- **State Management**: Pinia (primary) + Vuex (legacy)

### Key Architectural Decisions

#### 1. Dual Service Design
- **Combined Access Requests**: Single form for multiple hospital systems (Jeeva, Wellsoft, Internet)
- **Device Booking Service**: Separate workflow for ICT equipment reservations
- Both services share user authentication and approval infrastructure

#### 2. JSON-Based Flexible Schema
- `UserAccess.request_type` uses JSON columns for storing multiple service requests
- Allows adding new hospital systems without database migrations
- Trade-off: More complex queries, requires careful indexing

#### 3. Multi-Role Permission System
- Complex role hierarchy: `staff` → `ict_officer` → `head_of_department` → `divisional_director` → `ict_director` → `admin`
- Role-based middleware protection on all API endpoints
- Dynamic sidebar and dashboard content based on user roles

#### 4. Digital Signature Integration
- Signatures captured via HTML5 canvas and stored as files
- Organized by PF (Personal File) numbers for easy retrieval
- Embedded in PDF documents for official hospital documentation

#### 5. Real-Time Inventory Management
- Device availability checked in real-time during booking
- Out-of-stock devices handled through queue system with notifications
- Prevents double-booking through proper transaction handling

## 📊 Core Data Models & Relationships

### UserAccess Model (Primary)
```php
// Core fields
- id, user_id, staff_name, pf_number, department_id
- request_type (JSON) // ['jeeva', 'wellsoft', 'internet']
- status // pending, hod_approved, divisional_approved, etc.

// Approval workflow fields  
- hod_approval_status, hod_approval_date, hod_rejection_reason
- divisional_approval_status, divisional_approval_date
- ict_director_approval_status, ict_director_approval_date
- head_it_approval_status, head_it_approval_date
- ict_officer_implementation_status, implementation_date

// Relationships
- belongsTo(User::class)
- belongsTo(Department::class)
- hasMany(signatures, documents)
```

### BookingService Model
```php
// Core fields
- id, user_id, device_inventory_id, reason, duration_days
- booking_date, return_date, status
- assessment_data (JSON) // Device condition tracking

// Approval fields
- hod_approval_status, ict_approval_status
- approval_date, rejection_reason

// Relationships  
- belongsTo(User::class)
- belongsTo(DeviceInventory::class)
- hasMany(DeviceAssessment::class)
```

### DeviceInventory Model
```php
// Inventory tracking
- device_name, device_type, brand, model
- total_quantity, available_quantity, reserved_quantity
- status, location, purchase_date

// Relationships
- hasMany(BookingService::class)
```

### User Model (Enhanced)
```php
// Standard Laravel user fields + hospital-specific
- name, email, phone, pf_number, staff_name
- department_id, is_active

// Role system
- belongsToMany(Role::class) // Many-to-many roles
- hasOne(UserOnboarding::class)
- hasMany(UserAccess::class, BookingService::class)

// Methods
- getPrimaryRoleName() // Returns dominant role
- needsOnboarding() // Checks onboarding status
- getAllPermissions() // Role-based permissions
```

## 🔄 Complete Application Flow

### 1. User Authentication & Onboarding
```
Login → Check Onboarding Status → Complete Steps → Dashboard Access
  ↓
- Terms of Service acceptance
- ICT Policy acknowledgment  
- Declaration form submission
- Department/role verification
```

### 2. Access Request Workflow
```
Staff Form → HOD Review → Divisional Review → ICT Director → Head IT → ICT Officer
     ↓           ↓             ↓              ↓           ↓         ↓
  Combined    Department    Division       ICT Dept    Task      Access
   Request    Approval      Approval       Review      Assign    Grant
```

### 3. Device Booking Workflow
```
Device Selection → Availability Check → HOD Approval → ICT Approval → Collection
       ↓               ↓                   ↓             ↓           ↓
   Real-time       Inventory           Department     ICT Officer  Device
   Checking        Update              Review         Assessment   Handover
```

### 4. PDF Document Generation
```
Request Approval → Signature Collection → PDF Generation → Email Notification
       ↓                  ↓                    ↓               ↓
   Final Stage      Canvas Signatures     DomPDF Render    Auto Send
   Reached          Base64 Storage        With Signatures   to User
```

## 📁 Key Files & Directory Structure

### Backend Structure (Laravel)
```
backend/
├── app/Http/Controllers/Api/v1/          # API controllers
│   ├── AuthController.php               # Authentication
│   ├── UserAccessController.php         # Access requests CRUD
│   ├── BookingServiceController.php     # Device bookings
│   ├── ICTApprovalController.php        # ICT approval workflow
│   ├── BothServiceFormController.php    # Combined form handling
│   ├── AdminController.php              # Admin functions
│   └── [Role]Controller.php            # Role-specific controllers
├── app/Models/                          # Eloquent models
├── routes/api.php                       # API route definitions (265+ endpoints)
├── database/migrations/                 # Database schema
├── resources/views/pdf/                 # PDF templates
└── storage/app/signatures/              # Signature files storage
```

### Frontend Structure (Vue.js)
```
frontend/src/
├── components/
│   ├── views/forms/                     # Form components
│   │   ├── UserCombinedAccessForm.vue  # Main access request form
│   │   ├── both-service-form.vue       # Review/approval form
│   │   └── [ServiceType]Form.vue       # Individual service forms
│   ├── views/booking/                   # Device booking components
│   ├── admin/                          # Admin panel components
│   ├── ModernSidebar.vue               # Role-based navigation
│   └── [Role]Dashboard.vue             # Role-specific dashboards
├── stores/                             # Pinia state management
├── router/index.js                     # Vue router with role guards
├── services/                          # API service layer
└── utils/                             # Helper functions
```

### Configuration Files
```
backend/.env.example                    # Backend environment template
frontend/.env.example                   # Frontend environment template
backend/config/cors.php                 # CORS for API communication
frontend/vue.config.js                  # Vue CLI configuration
backend/config/l5-swagger.php          # API documentation (Swagger)
```

## ⚙️ Environment Configuration

### Backend Environment (.env)
```bash
# Application
APP_NAME="MNH User Access Management"
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mnh_access_management
DB_USERNAME=root
DB_PASSWORD=

# CORS (critical for frontend communication)
CORS_ALLOWED_ORIGINS=http://localhost:8080,http://127.0.0.1:8080
CORS_SUPPORTS_CREDENTIALS=true

# Queue & Session
QUEUE_CONNECTION=database
SESSION_DRIVER=database

# Mail (for PDF notifications)
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
```

### Frontend Environment (.env)
```bash
# API Configuration
VUE_APP_API_URL=http://127.0.0.1:8000/api

# Application Info
VUE_APP_NAME=MNH Access Management System
VUE_APP_VERSION=1.0.0

# Development
VUE_APP_DEBUG=true
VUE_APP_LOG_LEVEL=debug
VUE_APP_API_TIMEOUT=30000
```

## 🔐 Role-Based System Architecture

### Role Hierarchy (Ascending Authority)
1. **Staff**: Create requests, view own submissions
2. **ICT Officer**: Implement approved requests, device management
3. **Head of Department (HOD)**: Approve departmental requests
4. **Divisional Director**: Approve division-level requests  
5. **ICT Director**: Technical approval for system access
6. **Head of IT**: Task assignment and final technical approval
7. **Admin**: Full system access, user management

### Permission Matrix
| Action | Staff | ICT Officer | HOD | Divisional | ICT Director | Head IT | Admin |
|--------|-------|-------------|-----|-----------|--------------|---------|-------|
| Create Request | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| Approve HOD | ❌ | ❌ | ✓ | ❌ | ❌ | ❌ | ✓ |
| Approve Divisional | ❌ | ❌ | ❌ | ✓ | ❌ | ❌ | ✓ |
| Approve ICT Director | ❌ | ❌ | ❌ | ❌ | ✓ | ❌ | ✓ |
| Assign Tasks | ❌ | ❌ | ❌ | ❌ | ❌ | ✓ | ✓ |
| Implement Access | ❌ | ✓ | ❌ | ❌ | ❌ | ❌ | ✓ |
| Manage Users | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ✓ |

## 🔍 API Documentation

### Swagger Documentation
- **URL**: `http://localhost:8000/api-docs-modern`
- **JSON Schema**: `http://localhost:8000/api/api-docs`
- **Postman Collection**: `http://localhost:8000/api/postman-collection`

### Key API Endpoints
```
Authentication:
POST /api/login                         # User login
POST /api/logout                        # User logout
GET  /api/user                          # Current user info

Access Requests:
GET    /api/v1/user-access              # List requests
POST   /api/v1/user-access              # Create request
GET    /api/v1/user-access/{id}         # View request
PUT    /api/v1/user-access/{id}         # Update request

Device Bookings:
GET    /api/booking-service/bookings    # List bookings
POST   /api/booking-service/bookings    # Create booking
GET    /api/device-inventory            # Available devices

Approvals (Role-based):
POST /api/both-service-form/{id}/hod-submit        # HOD approval
POST /api/both-service-form/{id}/divisional-approve # Divisional approval
POST /api/head-of-it/assign-task                   # Task assignment
POST /api/ict-officer/access-requests/{id}/grant-access # Implementation
```

## 🚨 Common Development Issues & Solutions

### Port Conflicts
```bash
# If login fails with "POST method not supported"
# Kill conflicting processes on port 8000
netstat -aon | findstr :8000
taskkill /PID [PID_NUMBER] /F

# Start Laravel server cleanly
php artisan serve --host=127.0.0.1 --port=8000
```

### CORS Issues
```bash
# If frontend can't reach API
# Edit backend/config/cors.php and set 'allowed_origins' to your frontend URL(s)
# In development it's currently '*' (allow all). For production, set explicit origins.
php artisan config:clear
```

### Database Connection
```bash
# If migration fails
# Verify database exists and credentials are correct
php artisan migrate:status
php artisan migrate:fresh --seed
```

### Frontend Build Issues
```bash
# If build fails or components don't load
cd frontend
npm run clean:hard
npm install
npm run serve
```

## 🔧 Development Workflow Patterns

### Adding New Hospital Services
1. **Database**: Update `UserAccess` migration for new service type
2. **Backend**: Modify `UserAccessController` to handle new service logic  
3. **Frontend**: Update `UserCombinedAccessForm.vue` with new service options
4. **PDF**: Create template in `resources/views/pdf/` for new service
5. **Approval**: Add approval logic in relevant controllers

### Adding New Device Types  
1. **Migration**: Create new device categories in `DeviceInventory`
2. **Controller**: Update `BookingServiceController` for device handling
3. **Frontend**: Modify `BookingService.vue` for new device UI
4. **Inventory**: Update availability and booking logic

### Extending Role System
1. **Database**: Add role to `Role` model and seeder
2. **Component**: Create new role dashboard component
3. **Sidebar**: Update `ModernSidebar.vue` navigation
4. **Routes**: Add route guards and middleware protection
5. **Permissions**: Define role-specific permissions

## 🚀 Performance & Security Notes

### Performance Optimizations
- **Database**: Indexed fields (`user_id`, `status`, `created_at`)
- **Frontend**: Lazy loading routes, component caching
- **API**: Eloquent eager loading, query optimization
- **Caching**: Route and config caching in production

### Security Features
- **Authentication**: Laravel Sanctum with token expiration
- **Authorization**: Role-based middleware on all endpoints
- **File Security**: Signature validation, secure file storage
- **Data Privacy**: Healthcare data compliance, audit trails
- **Input Validation**: Request validation, XSS protection

## 📋 Testing Strategy

### Backend Testing (Pest)
```bash
cd backend

# Run all tests
php artisan test

# Run specific test files
php artisan test tests/Feature/UserAccessTest.php
php artisan test tests/Feature/BookingServiceTest.php

# Run with coverage
php artisan test --coverage
```

### Frontend Testing (Manual QA)
- Form validation testing
- Role-based navigation testing  
- API integration testing
- Responsive design testing

## 📋 Maintenance & Monitoring

### Regular Tasks
```bash
# Daily
php artisan queue:restart               # Restart queue workers
php artisan log:clear                   # Clear old logs

# Weekly  
php artisan route:cache                 # Cache routes for performance
npm run build:prod                      # Build optimized frontend

# Monthly
php artisan migrate:status              # Check migration status
composer update                         # Update dependencies
npm audit fix                          # Fix security vulnerabilities
```

### Monitoring Points
- Queue job processing (PDF generation)
- Database query performance
- File storage usage (signatures, PDFs)
- API response times
- User session management

---

**Last Updated**: October 2025  
**System Version**: 1.0.0  
**Laravel Version**: 12.x  
**Vue.js Version**: 3.x  

For additional support, refer to the inline documentation within controllers and components, or check the Swagger API documentation at `/api-docs-modern`.
