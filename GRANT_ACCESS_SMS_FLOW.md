# Grant Access SMS Flow Documentation

## Overview
This document explains how SMS notifications are sent when an ICT Officer grants access to a requester through the "Grant Access" button.

---

## 🔄 Flow Diagram

```
User clicks "Grant Access" button (Frontend)
           ↓
Frontend validates and sends request
           ↓
Backend: BothServiceFormController.approveIctOfficer()
           ↓
Update database with ICT Officer implementation
           ↓
Call SmsModule.notifyAccessGranted()
           ↓
Build SMS message with access details
           ↓
Send SMS via KODA TECH API
           ↓
Log to sms_logs table
           ↓
Update user_access table SMS status fields
           ↓
Return success response to frontend
```

---

## 📱 Frontend Flow

### 1. User Interaction
**File**: `frontend/src/components/views/forms/both-service-form.vue`

**Lines 1332-1376**: Grant Access popup modal
- User fills in ICT Officer Comment
- Sees SMS preview of what will be sent
- Clicks "Grant Access" button

**Lines 6089-6154**: `confirmGrantAccess()` method
```javascript
async confirmGrantAccess() {
  const comment = (this.grantAccessComment || '').trim()
  if (!comment) {
    this.grantAccessError = 'Comment is required.'
    return
  }
  
  this.processing = true
  const fdPayload = {
    ict_officer_name: this.form?.implementation?.ictOfficer?.name || this.currentUser?.name || '',
    ict_officer_date: new Date().toISOString().slice(0, 10),
    ict_officer_comments: comment,
    ict_officer_status: 'implemented',
    ict_officer_signature: this.ictOfficerSignaturePreviewFile
  }
  
  // Call backend API
  const res = await bothServiceFormService.ictOfficerApprove(this.getRequestId, fdPayload)
  
  if (res.success) {
    this.showGrantAccessPopup = false
    this.showToast('Access granted successfully', 'success')
    await this.loadRequestData() // Refresh page data
  }
}
```

### 2. Service Layer
**File**: `frontend/src/services/bothServiceFormService.js`

**Lines 873-914**: `ictOfficerApprove()` method
```javascript
async ictOfficerApprove(requestId, payload) {
  const fd = new FormData()
  fd.append('ict_officer_name', payload.ict_officer_name || '')
  fd.append('approved_date', payload.ict_officer_date || new Date().toISOString().slice(0, 10))
  fd.append('comments', payload.ict_officer_comments || '')
  fd.append('status', payload.ict_officer_status || 'implemented')
  fd.append('ict_officer_signature', payload.ict_officer_signature)
  
  // POST to backend
  const res = await apiClient.post(
    `/both-service-form/module-requests/${requestId}/ict-officer-approve`,
    fd,
    { headers: { 'Content-Type': 'multipart/form-data' } }
  )
  
  return res.data
}
```

---

## 🔧 Backend Flow

### 1. Controller Entry Point
**File**: `backend/app/Http/Controllers/Api/v1/BothServiceFormController.php`

**Lines 1844-1994**: `approveIctOfficer()` method

#### Key Steps:

**A. Validation** (Lines 1868-1881)
```php
$validated = $request->validate([
    'ict_officer_name' => 'required|string|max:255',
    'ict_officer_signature' => 'required|file|mimes:jpeg,jpg,png,pdf|max:2048',
    'approved_date' => 'required|date',
    'comments' => 'nullable|string|max:1000',
    'status' => 'nullable|string|in:implemented,pending,rejected'
]);
```

**B. Database Update** (Lines 1921-1931)
```php
$updateData = [
    'ict_officer_name' => $validated['ict_officer_name'],
    'ict_officer_signature_path' => $ictOfficerSignaturePath,
    'ict_officer_implemented_at' => $validated['approved_date'],
    'ict_officer_comments' => $validated['comments'] ?? null,
    'ict_officer_status' => $status
];
$userAccess->update($updateData);
```

**C. SMS Notification** (Lines 1953-1970) ⭐ **NEWLY ADDED**
```php
// Send SMS notification to requester that access has been granted
try {
    $smsModule = app(\App\Services\SmsModule::class);
    $smsResults = $smsModule->notifyAccessGranted(
        $userAccess, 
        $currentUser, 
        $validated['comments'] ?? null
    );
    
    Log::info('✅ SMS notification sent to requester for access granted', [
        'request_id' => $userAccessId,
        'ict_officer_id' => $currentUser->id,
        'sms_results' => $smsResults
    ]);
} catch (\Exception $smsError) {
    Log::warning('❌ Failed to send access granted SMS notification', [
        'request_id' => $userAccessId,
        'ict_officer_id' => $currentUser->id,
        'error' => $smsError->getMessage()
    ]);
    // Don't fail the main operation if SMS fails
}

DB::commit();
```

---

## 📨 SMS Module Details

### 2. SMS Service
**File**: `backend/app/Services/SmsModule.php`

**Lines 233-319**: `notifyAccessGranted()` method

#### Phone Number Resolution (Lines 238-260)
The system tries multiple sources to get the requester's phone number:

```php
// Priority order:
1. $request->phone
2. $request->phone_number  
3. $request->user->phone (from users table via relationship)
```

**Example Log Output:**
```
Access granted SMS - phone number resolved
  request_id: 59
  phone_source: 'request.phone_number'
  phone_masked: '255712***'
```

#### Message Building (Lines 268-285)
```php
// Get requester name
$name = $request->staff_name ?? $request->full_name ?? $request->user->name ?? 'User';

// Get request types (Jeeva, Wellsoft, Internet)
$types = $this->getRequestTypes($request);

// Get reference
$ref = $request->request_id ?? 'MLG-REQ' . str_pad($request->id, 6, '0', STR_PAD_LEFT);

// Build message with ICT Officer's comment if provided
$message = "Dear {$name}, your {$types} access has been GRANTED and is now ACTIVE. Ref: {$ref}.";

if ($comments && trim($comments) !== '') {
    $message .= " Note: {$comments}";
}

$message .= " - EABMS";
```

**Example SMS Message:**
```
Dear John Doe, your Jeeva & Internet access has been GRANTED and is now ACTIVE. 
Ref: MLG-REQ000059. Note: your password is HOSPITAL change it before you use - EABMS
```

#### SMS Sending (Lines 287-302)
```php
// Send SMS via KODA TECH API
$result = $this->sendSms($phone, $message, 'access_granted');
$results['requester_notified'] = $result['success'];

// Update SMS status tracking in user_access table
try {
    $request->update([
        'sms_sent_to_requester_at' => $results['requester_notified'] ? now() : null,
        'sms_to_requester_status' => $results['requester_notified'] ? 'sent' : 'failed'
    ]);
} catch (Exception $e) {
    Log::error('Failed to update requester SMS status (access granted)', [
        'request_id' => $request->id,
        'error' => $e->getMessage()
    ]);
}
```

---

## 🔌 KODA TECH API Integration

**File**: `backend/app/Services/SmsModule.php`

**Lines 428-503**: `sendViaApi()` method

### Configuration (Lines 27-37)
```php
$this->testMode = config('sms.test_mode', true);
$this->apiUrl = $this->testMode 
    ? 'https://messaging-service.co.tz/api/sms/v1/test/text/single'
    : 'https://messaging-service.co.tz/api/sms/v1/text/single';
$this->apiKey = config('sms.api_key', 'beneth');
$this->secretKey = config('sms.secret_key', 'Beneth@1701');
$this->senderId = config('sms.sender_id', 'KODA TECH');
$this->timeout = config('sms.timeout', 30);
```

### API Request (Lines 438-459)
```php
// Encode credentials for Basic Auth
$auth = base64_encode($this->apiKey . ':' . $this->secretKey);

// Prepare payload
$payload = [
    "from" => $this->senderId,
    "to" => $phoneNumber,  // Format: 255712345678
    "text" => $message,
    "reference" => uniqid("ref_")
];

// Initialize cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $this->apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Basic " . $auth,
    "Content-Type: application/json",
    "Accept: application/json"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
```

### Phone Number Formatting (Lines 511-545)
```php
protected function formatPhoneNumber(string $phoneNumber): string
{
    // Remove all non-numeric characters
    $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
    
    // Auto-add Tanzania country code +255
    
    // 255712345678 (12 digits) -> 255712345678 ✓
    // 712345678 (9 digits) -> 255712345678 ✓
    // 0712345678 (10 digits) -> 255712345678 ✓
    // 612345678 (9 digits Vodacom) -> 255612345678 ✓
    // 0612345678 (10 digits Vodacom) -> 255612345678 ✓
    
    return $phoneNumber;
}
```

---

## 💾 Database Tracking

### 1. SMS Logs Table
**File**: `backend/database/migrations/2025_10_03_070943_create_sms_logs_table.php`

```sql
CREATE TABLE sms_logs (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    phone_number VARCHAR(255),
    message TEXT,
    type VARCHAR(255) DEFAULT 'notification',  -- 'access_granted' in this case
    status ENUM('sent', 'failed', 'pending') DEFAULT 'pending',
    provider_response JSON,
    sent_at TIMESTAMP,
    user_id BIGINT,
    reference_id BIGINT,
    reference_type VARCHAR(255),
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    INDEX idx_phone (phone_number),
    INDEX idx_status (status),
    INDEX idx_type (type),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);
```

**Lines 638-652**: Logging logic
```php
protected function logSms(string $phoneNumber, string $message, string $type, bool $success, array $response): void
{
    try {
        SmsLog::create([
            'phone_number' => $phoneNumber,
            'message' => $message,
            'type' => $type,  // 'access_granted'
            'status' => $success ? 'sent' : 'failed',
            'provider_response' => json_encode($response),
            'sent_at' => $success ? now() : null
        ]);
    } catch (Exception $e) {
        Log::error('Failed to log SMS', ['error' => $e->getMessage()]);
    }
}
```

### 2. User Access Table Tracking
**File**: `backend/database/migrations/2025_10_24_234618_add_sms_notification_tracking_to_user_access_table.php`

```sql
ALTER TABLE user_access ADD COLUMN (
    sms_sent_to_requester_at TIMESTAMP,
    sms_to_requester_status VARCHAR(255) DEFAULT 'pending'  -- 'pending', 'sent', 'failed'
);
```

**Purpose**: Track when SMS was sent to requester for this specific request

---

## 🔍 Request Types Detection

**File**: `backend/app/Services/SmsModule.php`

**Lines 596-619**: `getRequestTypes()` method

```php
protected function getRequestTypes($request): string
{
    $types = [];
    
    // Check Jeeva access
    if (!empty($request->jeeva_modules_selected) || 
        ($request->jeeva_access ?? false)) {
        $types[] = 'Jeeva';
    }
    
    // Check Wellsoft access
    if (!empty($request->wellsoft_modules_selected) || 
        ($request->wellsoft_access ?? false)) {
        $types[] = 'Wellsoft';
    }
    
    // Check Internet access
    if (!empty($request->internet_purposes) || 
        ($request->internet_access ?? false)) {
        $types[] = 'Internet';
    }
    
    return implode(' & ', $types) ?: 'Access';
}
```

**Examples:**
- Jeeva only: `"Jeeva access"`
- Jeeva + Internet: `"Jeeva & Internet access"`
- All three: `"Jeeva & Wellsoft & Internet access"`
- None specified: `"Access"`

---

## 🛡️ Error Handling & Rate Limiting

### Rate Limiting (Lines 557-571)
```php
protected function isRateLimited(string $phoneNumber): bool
{
    $key = 'sms_rate_limit:' . $phoneNumber;
    $attempts = Cache::get($key, 0);
    
    $maxAttempts = config('sms.rate_limit.max_per_hour_per_number', 5);
    
    if ($attempts >= $maxAttempts) {
        return true;  // Block if > 5 SMS per hour to same number
    }
    
    Cache::put($key, $attempts + 1, now()->addHour());
    return false;
}
```

### Error Handling
1. **Controller Level**: SMS failure doesn't rollback database transaction
2. **Service Level**: Catches exceptions and logs warnings
3. **API Level**: Retries with timeout protection

---

## 🧪 Testing & Verification

### Check if SMS was sent:

```sql
-- Check SMS logs table
SELECT * FROM sms_logs 
WHERE phone_number = '255712345678' 
  AND type = 'access_granted'
ORDER BY created_at DESC 
LIMIT 5;

-- Check user_access table
SELECT 
    id,
    request_id,
    sms_sent_to_requester_at,
    sms_to_requester_status
FROM user_access 
WHERE id = 59;
```

### Check Laravel logs:
```bash
# Location: storage/logs/laravel.log
tail -f storage/logs/laravel.log | grep "Access granted SMS"
```

**Expected Log Entries:**
```
[2025-10-26 13:45:00] Access granted SMS - phone number resolved
    request_id: 59
    phone_source: 'request.phone_number'
    phone_masked: '255712***'

[2025-10-26 13:45:01] SMS sent successfully
    count: 1

[2025-10-26 13:45:01] ✅ SMS notification sent to requester for access granted
    request_id: 59
    ict_officer_id: 15
    sms_results: {"requester_notified":true}
```

---

## 📊 Summary

| **Component** | **Purpose** | **Location** |
|---------------|-------------|--------------|
| Frontend Modal | User input & SMS preview | `both-service-form.vue` (1332-1376) |
| Frontend Service | API communication | `bothServiceFormService.js` (873-914) |
| Controller | Request handling & DB update | `BothServiceFormController.php` (1844-1994) |
| SMS Module | SMS sending logic | `SmsModule.php` (233-319) |
| KODA API | External SMS gateway | `SmsModule.php` (428-503) |
| SMS Logs | Audit trail | `sms_logs` table |
| User Access | Per-request tracking | `user_access` table |

---

## ✅ Recent Fix (2025-10-26)

**Issue**: SMS was not being sent when ICT Officer granted access

**Root Cause**: `BothServiceFormController.approveIctOfficer()` method was missing SMS notification call

**Solution**: Added `SmsModule.notifyAccessGranted()` call at lines 1953-1970

**Impact**: SMS now sent immediately when access is granted, including ICT Officer's implementation notes

---

## 🔮 Future Enhancements

1. **Queue-based SMS**: Move SMS sending to background job for better performance
2. **Retry mechanism**: Auto-retry failed SMS with exponential backoff
3. **Delivery reports**: Track SMS delivery status from provider
4. **Template management**: Store SMS templates in database for easy editing
5. **Multi-language support**: Send SMS in user's preferred language

---

**Last Updated**: 2025-10-26  
**Version**: 1.0  
**Author**: System Documentation
