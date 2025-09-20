# How `/both-service-form/{id}/update-hod-approval` Endpoint Works

## 🎯 **Endpoint Overview**

The `/both-service-form/{id}/update-hod-approval` endpoint is designed to update HOD (Head of Department) approval information for existing user access requests in the system.

## 📍 **Route Definition**

```php
// File: backend/routes/api.php
Route::post('/{id}/update-hod-approval', [BothServiceFormController::class, 'updateHodApprovalForRecord'])
    ->name('both-service-form.update-hod-approval');
```

**Route Details:**
- **Method**: `POST`
- **URL Pattern**: `/api/both-service-form/{id}/update-hod-approval`
- **Controller**: `BothServiceFormController`
- **Method**: `updateHodApprovalForRecord`
- **Middleware**: `both.service.role` (role-based access control)

## 🔧 **Method Implementation**

### **Method Signature**
```php
public function updateHodApprovalForRecord(Request $request, int $id): JsonResponse
```

### **Parameters**
- `$request`: HTTP request object containing form data and files
- `$id`: The ID of the user access record to update

## 📝 **Request Validation**

The endpoint validates the following input fields:

```php
$validatedData = $request->validate([
    'hod_name' => 'required|string|max:255',
    'hod_signature' => 'required|file|mimes:jpeg,jpg,png,pdf|max:2048',
    'hod_date' => 'required|date',
    'hod_comments' => 'nullable|string|max:1000',
    // Optional: Update module selections if provided
    'selectedWellsoft' => 'sometimes|array',
    'selectedJeeva' => 'sometimes|array',
    'module_requested_for' => 'sometimes|string|in:use,revoke',
]);
```

### **Required Fields:**
- **hod_name**: HOD's full name (string, max 255 characters)
- **hod_signature**: Signature file (JPEG, JPG, PNG, or PDF, max 2MB)
- **hod_date**: Date of approval (valid date format)

### **Optional Fields:**
- **hod_comments**: Additional comments (string, max 1000 characters)
- **selectedWellsoft**: Array of selected Wellsoft modules
- **selectedJeeva**: Array of selected Jeeva modules
- **module_requested_for**: Type of module request ("use" or "revoke")

## 🔄 **Processing Flow**

### **1. Authentication & Authorization**
```php
$currentUser = $request->user();
$userRoles = $currentUser->roles()->pluck('name')->toArray();
```

### **2. Record Retrieval**
```php
$userAccess = UserAccess::findOrFail($id);
```

### **3. File Upload Processing**
```php
if ($request->hasFile('hod_signature')) {
    $hodSignatureFile = $request->file('hod_signature');
    
    if ($hodSignatureFile->isValid()) {
        $signatureDir = 'signatures/hod';
        if (!Storage::disk('public')->exists($signatureDir)) {
            Storage::disk('public')->makeDirectory($signatureDir);
        }
        
        $filename = 'hod_signature_' . $userAccess->pf_number . '_' . time() . '.' . $hodSignatureFile->getClientOriginalExtension();
        $hodSignaturePath = $hodSignatureFile->storeAs($signatureDir, $filename, 'public');
        
        if (!Storage::disk('public')->exists($hodSignaturePath)) {
            throw new Exception('Failed to store HOD signature file');
        }
    }
}
```

### **4. Module Selection Processing**
```php
// Update module selections if provided
if ($request->has('selectedWellsoft')) {
    $selectedWellsoft = $this->processModuleArray($request->input('selectedWellsoft', []));
    $updateData['wellsoft_modules_selected'] = $selectedWellsoft;
    $updateData['wellsoft_modules'] = $selectedWellsoft; // Keep both for compatibility
}

if ($request->has('selectedJeeva')) {
    $selectedJeeva = $this->processModuleArray($request->input('selectedJeeva', []));
    $updateData['jeeva_modules_selected'] = $selectedJeeva;
    $updateData['jeeva_modules'] = $selectedJeeva; // Keep both for compatibility
}
```

### **5. Database Update**
```php
$updateData = [
    'hod_name' => $validatedData['hod_name'],
    'hod_signature_path' => $hodSignaturePath,
    'hod_approved_at' => $validatedData['hod_date'],
    'hod_comments' => $validatedData['hod_comments'] ?? null,
    'hod_approved_by' => $currentUser->id,
    'hod_approved_by_name' => $currentUser->name,
    'status' => 'hod_approved'
];

$userAccess->update($updateData);
$userAccess->refresh();
```

## 📤 **Response Format**

### **Success Response (200)**
```json
{
    "success": true,
    "message": "HOD approval updated successfully",
    "data": {
        "id": 4,
        "status": "hod_approved",
        "hod_approval": {
            "name": "Dr. John Smith",
            "approved_at": "2025-01-27T10:30:00.000000Z",
            "signature_path": "signatures/hod/hod_signature_PF1010_1642678900.jpg",
            "comments": "Approved for permanent access",
            "approved_by": "Dr. John Smith"
        },
        "modules": {
            "wellsoft": ["FINANCIAL ACCOUNTING", "DOCTOR CONSULTATION"],
            "jeeva": ["OUTPATIENT", "NURSING STATION"],
            "requested_for": "use"
        }
    }
}
```

### **Validation Error Response (422)**
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "hod_name": ["HOD name is required"],
        "hod_signature": ["HOD signature file is required"]
    }
}
```

### **Server Error Response (500)**
```json
{
    "success": false,
    "message": "Failed to update HOD approval: [error details]"
}
```

## 🛠 **Helper Methods**

### **processModuleArray Method**
```php
private function processModuleArray($input): array
{
    if (empty($input)) {
        return [];
    }
    
    if (is_array($input)) {
        return array_values(array_filter($input, function($item) {
            return !empty($item) && $item !== null && $item !== '';
        }));
    }
    
    if (is_string($input)) {
        try {
            $decoded = json_decode($input, true);
            if (is_array($decoded)) {
                return array_values(array_filter($decoded, function($item) {
                    return !empty($item) && $item !== null && $item !== '';
                }));
            }
            return [$input];
        } catch (Exception $e) {
            return [$input];
        }
    }
    
    return [];
}
```

## 🔒 **Security Features**

### **1. Database Transactions**
```php
DB::beginTransaction();
try {
    // Process update
    DB::commit();
} catch (Exception $e) {
    DB::rollBack();
    // Handle error
}
```

### **2. File Upload Security**
- File type validation (JPEG, JPG, PNG, PDF only)
- File size limit (2MB maximum)
- Secure file naming with timestamps
- Directory creation with proper permissions
- File existence verification after upload

### **3. Input Sanitization**
- All inputs are validated using Laravel's validation rules
- Module arrays are filtered to remove empty values
- SQL injection protection through Eloquent ORM

## 📊 **Database Fields Updated**

The endpoint updates the following fields in the `user_access` table:

| Field | Type | Description |
|-------|------|-------------|
| `hod_name` | string | HOD's full name |
| `hod_signature_path` | string | Path to uploaded signature file |
| `hod_approved_at` | timestamp | Date and time of approval |
| `hod_comments` | text | Optional comments from HOD |
| `hod_approved_by` | integer | ID of user who approved |
| `hod_approved_by_name` | string | Name of user who approved |
| `status` | string | Updated to "hod_approved" |
| `wellsoft_modules_selected` | json | Selected Wellsoft modules (if provided) |
| `wellsoft_modules` | json | Selected Wellsoft modules (compatibility) |
| `jeeva_modules_selected` | json | Selected Jeeva modules (if provided) |
| `jeeva_modules` | json | Selected Jeeva modules (compatibility) |
| `module_requested_for` | string | Module request type ("use" or "revoke") |

## 📁 **File Storage Structure**

Signature files are stored in the following structure:
```
storage/app/public/signatures/hod/
├── hod_signature_PF1010_1642678900.jpg
├── hod_signature_PF1011_1642678901.png
└── hod_signature_PF1012_1642678902.pdf
```

**File Naming Convention:**
`hod_signature_{pf_number}_{timestamp}.{extension}`

## 🧪 **Usage Example**

### **Frontend JavaScript/Vue.js**
```javascript
const formData = new FormData();
formData.append('hod_name', 'Dr. John Smith');
formData.append('hod_signature', signatureFile); // File object
formData.append('hod_date', '2025-01-27');
formData.append('hod_comments', 'Approved for permanent access');
formData.append('selectedWellsoft', JSON.stringify(['FINANCIAL ACCOUNTING', 'DOCTOR CONSULTATION']));
formData.append('selectedJeeva', JSON.stringify(['OUTPATIENT', 'NURSING STATION']));
formData.append('module_requested_for', 'use');

const response = await fetch('/api/both-service-form/4/update-hod-approval', {
    method: 'POST',
    headers: {
        'Authorization': `Bearer ${token}`,
        // Don't set Content-Type for FormData - browser will set it automatically
    },
    body: formData
});

const result = await response.json();
```

### **cURL Example**
```bash
curl -X POST \
  http://localhost:8000/api/both-service-form/4/update-hod-approval \
  -H 'Authorization: Bearer your-token-here' \
  -F 'hod_name=Dr. John Smith' \
  -F 'hod_signature=@signature.jpg' \
  -F 'hod_date=2025-01-27' \
  -F 'hod_comments=Approved for permanent access' \
  -F 'selectedWellsoft=["FINANCIAL ACCOUNTING","DOCTOR CONSULTATION"]' \
  -F 'selectedJeeva=["OUTPATIENT","NURSING STATION"]' \
  -F 'module_requested_for=use'
```

## 🔍 **Logging & Debugging**

The endpoint includes comprehensive logging:

```php
Log::info('📄 Updating HOD approval for existing record', [
    'record_id' => $id,
    'current_user' => $currentUser->id,
    'hod_name' => $validatedData['hod_name'],
    'hod_date' => $validatedData['hod_date'],
    'has_signature' => $request->hasFile('hod_signature'),
    'has_wellsoft_modules' => $request->has('selectedWellsoft'),
    'has_jeeva_modules' => $request->has('selectedJeeva')
]);
```

**Log Locations:**
- Success logs: `storage/logs/laravel.log`
- Error logs: `storage/logs/laravel.log`
- File upload logs: Detailed file processing information

## ⚠️ **Common Issues & Solutions**

### **1. File Upload Fails**
**Problem**: Signature file not uploading
**Solution**: 
- Check file size (must be ≤ 2MB)
- Verify file type (JPEG, JPG, PNG, PDF only)
- Ensure proper form encoding (`multipart/form-data`)

### **2. Module Arrays Empty**
**Problem**: Module selections not saving
**Solution**:
- Ensure arrays are properly formatted
- Check that array values are not empty strings
- Verify JSON encoding if sending as strings

### **3. Permission Denied**
**Problem**: User cannot access endpoint
**Solution**:
- Verify user has appropriate role permissions
- Check middleware configuration
- Ensure user is authenticated

## 🎯 **Key Features**

1. **✅ Robust File Upload**: Secure signature file handling with validation
2. **✅ Module Management**: Support for both Wellsoft and Jeeva module selections
3. **✅ Transaction Safety**: Database rollback on failures
4. **✅ Comprehensive Logging**: Detailed process tracking for debugging
5. **✅ Error Handling**: Clear error messages and proper HTTP status codes
6. **✅ Data Integrity**: Validation and sanitization of all inputs

This endpoint provides a complete solution for HOD approval processing with proper security, validation, and error handling mechanisms.