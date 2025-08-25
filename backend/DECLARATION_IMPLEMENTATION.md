# Declaration Form Implementation

This document outlines the implementation of the Declaration form for the onboarding process in the Laravel backend.

## Overview

The Declaration form is part of the user onboarding process where users must provide their personal information and confirm their agreement to the terms and conditions. The implementation ensures that PF numbers are unique across all declarations and provides a comprehensive API for managing declaration data.

## Features Implemented

### 1. Database Schema
- **Table Name**: `declarations`
- **Unique Constraint**: PF number is unique across all declarations
- **Relationships**: Each declaration belongs to a user
- **Signature Support**: Stores signature file information as JSON

### 2. Model Features
- **Declaration Model**: Full Eloquent model with relationships and scopes
- **User Relationship**: Added declaration relationship to User model
- **Validation**: Built-in validation for PF number uniqueness
- **File Handling**: Signature file upload and storage management

### 3. API Endpoints

#### Declaration Routes (`/api/declaration/`)
- `GET /departments` - Get all departments with blue color
- `POST /submit` - Submit declaration form
- `GET /show` - Get user's declaration
- `POST /check-pf-number` - Check PF number availability
- `GET /all` - Get all declarations (Admin only)

#### Onboarding Integration
- `POST /api/onboarding/submit-declaration` - Submit via onboarding flow

### 4. Validation Rules

```php
[
    'fullName' => 'required|string|max:255',
    'pfNumber' => 'required|string|max:100|unique:declarations,pf_number,except:current_user',
    'department' => 'required|string|max:255',
    'jobTitle' => 'required|string|max:255',
    'date' => 'required|date',
    'agreement' => 'required|boolean|accepted',
]
```

### 5. File Upload Support
- **Allowed Types**: PNG, JPG, JPEG, PDF
- **Max Size**: 5MB
- **Storage Path**: `storage/app/public/signatures/declarations/`
- **Naming Convention**: `declaration_signature_{user_id}_{timestamp}.{ext}`

## Database Migration

The migration creates the following table structure:

```sql
CREATE TABLE declarations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    pf_number VARCHAR(100) UNIQUE NOT NULL,
    department VARCHAR(255) NOT NULL,
    job_title VARCHAR(255) NOT NULL,
    signature_date DATE NOT NULL,
    agreement_accepted BOOLEAN DEFAULT FALSE,
    signature_info JSON NULL,
    ip_address VARCHAR(255) NULL,
    user_agent TEXT NULL,
    submitted_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_pf (user_id, pf_number),
    INDEX idx_submitted_at (submitted_at)
);
```

## Usage Examples

### 1. Submit Declaration Form

```javascript
// Frontend JavaScript example
const formData = new FormData();
formData.append('fullName', 'John Doe');
formData.append('pfNumber', 'PF001234');
formData.append('department', 'ICT');
formData.append('jobTitle', 'Software Developer');
formData.append('date', '2025-01-27');
formData.append('agreement', 'true');
formData.append('signature', signatureFile);

fetch('/api/declaration/submit', {
    method: 'POST',
    headers: {
        'Authorization': 'Bearer ' + token,
    },
    body: formData
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        console.log('Declaration submitted:', data.data);
    }
});
```

### 2. Check PF Number Availability

```javascript
fetch('/api/declaration/check-pf-number', {
    method: 'POST',
    headers: {
        'Authorization': 'Bearer ' + token,
        'Content-Type': 'application/json',
    },
    body: JSON.stringify({
        pf_number: 'PF001234'
    })
})
.then(response => response.json())
.then(data => {
    console.log('PF Number available:', data.data.is_available);
});
```

### 3. Get Departments with Blue Color

```javascript
fetch('/api/declaration/departments', {
    headers: {
        'Authorization': 'Bearer ' + token,
    }
})
.then(response => response.json())
.then(data => {
    // All departments will have color: 'blue'
    data.data.forEach(dept => {
        console.log(`${dept.name} - Color: ${dept.color}`);
    });
});
```

## Key Implementation Details

### 1. PF Number Uniqueness
- Enforced at database level with UNIQUE constraint
- Validated at application level in both controllers
- Custom validation rule checks existing declarations
- Excludes current user when updating their own declaration

### 2. Department Color Implementation
- All departments returned with `color: 'blue'` as requested
- Departments loaded from database with active status
- Consistent formatting with full name display

### 3. Onboarding Integration
- Declaration submission updates onboarding progress
- Maintains backward compatibility with existing onboarding flow
- Uses database transactions for data consistency
- Stores declaration ID in onboarding data for reference

### 4. File Storage
- Signatures stored in dedicated directory structure
- Proper file validation and security checks
- URL generation for frontend access
- File information stored as JSON in database

### 5. Error Handling
- Comprehensive validation with custom error messages
- Database transaction rollback on failures
- Detailed logging for debugging
- User-friendly error responses

## Security Considerations

1. **File Upload Security**
   - File type validation
   - File size limits
   - Secure file naming
   - Proper storage location

2. **Data Validation**
   - Server-side validation for all inputs
   - PF number uniqueness enforcement
   - Required field validation
   - Boolean conversion for agreement checkbox

3. **Access Control**
   - Authentication required for all endpoints
   - Admin-only routes for sensitive operations
   - User can only access their own declaration

## Testing

Run the test script to verify implementation:

```bash
cd backend
php test_declaration.php
```

This will check:
- Department loading functionality
- Declaration model creation
- Database schema verification
- Route registration

## Migration Commands

To set up the declaration system:

```bash
# Run the migration
php artisan migrate

# Seed departments (optional)
php artisan db:seed --class=DepartmentSeeder

# Create storage link (if not already created)
php artisan storage:link
```

## Frontend Integration Notes

1. **Form Submission**: Use FormData for file uploads
2. **PF Number Validation**: Implement real-time checking
3. **Department Loading**: All departments will have blue color
4. **Agreement Checkbox**: Ensure proper boolean handling
5. **Error Display**: Handle validation errors appropriately

## Troubleshooting

### Common Issues

1. **PF Number Already Exists**
   - Check if user already has a declaration
   - Verify PF number format and uniqueness

2. **File Upload Fails**
   - Check file size (max 5MB)
   - Verify file type (PNG, JPG, JPEG, PDF)
   - Ensure storage directory exists and is writable

3. **Department Loading Issues**
   - Run department seeder
   - Check database connection
   - Verify department table has active records

### Debug Commands

```bash
# Check migration status
php artisan migrate:status

# Check if declarations table exists
php artisan tinker
>>> Schema::hasTable('declarations')

# Check department count
>>> App\Models\Department::active()->count()

# Check declaration model
>>> new App\Models\Declaration()
```

## Future Enhancements

1. **PDF Generation**: Generate declaration PDFs for download
2. **Email Notifications**: Send confirmation emails after submission
3. **Audit Trail**: Track declaration changes and updates
4. **Bulk Operations**: Admin tools for bulk declaration management
5. **Export Features**: Export declarations to Excel/CSV formats