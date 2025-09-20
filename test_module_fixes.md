# Testing Guide for Module Selection and HOD Signature Fixes

## Fixed Issues

1. **Fixed UserAccessController** (used by `/api/v1/combined-access`) to properly process module arrays
2. **Enhanced BothServiceFormController** (used by `/api/both-service-form`) with better module processing
3. **Added comprehensive validation** to prevent empty module submissions when services are requested

## Test Steps

### 1. Test Module Selection with UserAccessController

**Endpoint**: `POST /api/v1/combined-access`

**Test Data**:
```javascript
// Create a FormData object with proper module selections
const formData = new FormData();
formData.append('pf_number', 'PF1010');
formData.append('staff_name', 'David Selemani');  
formData.append('phone_number', '+255700000019');
formData.append('department_id', '5');
formData.append('request_type[0]', 'jeeva_access');
formData.append('request_type[1]', 'wellsoft');

// Add module selections
formData.append('selectedWellsoft[0]', 'Wellsoft Module 1');
formData.append('selectedWellsoft[1]', 'Wellsoft Module 2');
formData.append('selectedJeeva[0]', 'Jeeva Module 1');
formData.append('selectedJeeva[1]', 'Jeeva Module 2');
formData.append('wellsoftRequestType', 'use');

// Add signature file
formData.append('signature', signatureFile);
```

### 2. Expected Log Messages

**Success indicators**:
- `📊 Processed module selections in UserAccessController`
- `Creating combined user access request` with non-zero module counts
- `✅ ACTUAL DATA STORED IN DATABASE` showing populated arrays

**Error indicators (should NOT appear)**:
- `⚠️ WELLSOFT SERVICE REQUESTED BUT NO MODULES SELECTED`  
- `⚠️ JEEVA SERVICE REQUESTED BUT NO MODULES SELECTED`

### 3. Database Verification

Check the `user_access` table after submission:

```sql
SELECT id, pf_number, staff_name,
       JSON_LENGTH(wellsoft_modules_selected) as wellsoft_count,
       JSON_LENGTH(jeeva_modules_selected) as jeeva_count,
       wellsoft_modules_selected,
       jeeva_modules_selected,
       request_type,
       module_requested_for
FROM user_access 
WHERE id = [NEW_RECORD_ID];
```

**Expected Results**:
- `wellsoft_count` > 0 (not 0)
- `jeeva_count` > 0 (not 0) 
- `wellsoft_modules_selected` contains actual module names (not empty array `[]`)
- `jeeva_modules_selected` contains actual module names (not empty array `[]`)
- `request_type` = `["jeeva_access","wellsoft"]`
- `module_requested_for` = `"use"`

### 4. Test HOD Signature Upload

**Method 1: Using both-service-form endpoint**
```bash
curl -X POST "http://127.0.0.1:8000/api/both-service-form/5/update-hod-approval" \
     -H "Authorization: Bearer YOUR_TOKEN" \
     -F "hod_name=Chief Laboratory Technologist" \
     -F "hod_date=2025-09-19" \
     -F "hod_comments=Approved with conditions" \
     -F "hod_signature=@signature.jpg"
```

**Method 2: Using initial form submission with HOD data**
Include HOD fields in the initial form submission to UserAccessController.

### 5. Expected Database Results for HOD Approval

```sql
SELECT id, pf_number, hod_name, hod_signature_path, hod_approved_at, status
FROM user_access 
WHERE id = [RECORD_ID];
```

**Expected Results**:
- `hod_name` = "Chief Laboratory Technologist" (not NULL)
- `hod_signature_path` = "signatures/hod/hod_signature_PF1010_[timestamp].jpg" (not NULL)
- `hod_approved_at` = "2025-09-19 [time]" (not NULL)
- `status` = "hod_approved"

### 6. File System Verification

Check that signature files are actually stored:

```bash
ls -la /c/xampp/htdocs/lara-API-vue/backend/storage/app/public/signatures/hod/
```

Should show files like:
- `hod_signature_PF1010_1758309558.jpg`

### 7. Error Testing

**Test 1: Submit Wellsoft service without modules**
```javascript
formData.append('request_type[0]', 'wellsoft');
// Don't add any selectedWellsoft[] fields
```

**Expected**: 422 error with message "Wellsoft service was requested but no Wellsoft modules were selected"

**Test 2: Submit Jeeva service without modules**
```javascript  
formData.append('request_type[0]', 'jeeva_access');
// Don't add any selectedJeeva[] fields
```

**Expected**: 422 error with message "Jeeva service was requested but no Jeeva modules were selected"

## Troubleshooting

### If modules are still empty:

1. **Check the logs** for FormData parsing messages:
   ```
   🔄 FormData parsing for selectedWellsoft in UserAccessController
   ```

2. **Inspect the frontend form submission** - ensure it's sending data in the correct format

3. **Use the debug endpoint**:
   ```bash
   curl "http://127.0.0.1:8000/api/both-service-form/debug-record/[ID]"
   ```

### If HOD signature is still missing:

1. **Check file upload logs**:
   ```
   ✅ HOD signature stored successfully
   ❌ HOD signature upload failed
   ```

2. **Check file permissions** on storage directories

3. **Verify file size limits** in PHP configuration

4. **Check disk space** on the server

## Summary of Changes Made

1. **UserAccessController.php**:
   - Added `processModuleArray()` method 
   - Enhanced `store()` method with module processing
   - Enhanced `update()` method with module processing
   - Added validation to prevent empty module submissions

2. **BothServiceFormController.php**:
   - Enhanced module selection processing with 5 fallback strategies
   - Improved HOD signature upload validation
   - Added `updateHodApprovalForRecord()` method for HOD approval updates

3. **Routes**:
   - Added `/api/both-service-form/{id}/update-hod-approval` route

The fixes address both the root cause (incorrect module array parsing) and provide comprehensive error handling and validation to prevent future issues.
