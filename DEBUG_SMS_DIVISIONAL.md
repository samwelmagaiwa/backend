# Debugging: SMS Not Sent to Divisional Director After HOD Approval

## 🔍 Issue
When HOD approves a request, the SMS is not being sent to the Divisional Director.

---

## ✅ What We Know

1. **Backend code IS calling SMS service** (line 298-326 in `HodCombinedAccessController.php`)
2. **SMS status shows "Delivered"** but request still shows "Pending HOD Approval"
3. **Request visible in Divisional Director dashboard** (good!)

---

## 🐛 Possible Causes & Solutions

### 1. SMS Service is Disabled

**Check `.env` file:**
```env
SMS_ENABLED=true  # Must be true
```

**To verify:**
```bash
# In Laravel tinker
php artisan tinker
>>> config('sms.enabled')
# Should return: true

>>> app(SmsModule::class)->isEnabled()
# Should return: true
```

---

### 2. Divisional Director Has No Phone Number

**Check database:**
```sql
-- Find Divisional Director user
SELECT u.id, u.name, u.email, u.phone 
FROM users u
JOIN model_has_roles mhr ON u.id = mhr.model_id
JOIN roles r ON mhr.role_id = r.id
WHERE r.name = 'divisional_director';

-- Result should show phone number
```

**Fix if phone is NULL:**
```sql
UPDATE users 
SET phone = '0712345678'  -- Replace with actual phone
WHERE id = <divisional_director_user_id>;
```

---

### 3. Wrong Role Name in Database

**Check role query:**
```sql
-- Verify role exists
SELECT * FROM roles WHERE name LIKE '%division%';

-- Should return something like:
-- id | name                  | guard_name
-- 3  | divisional_director   | api
```

**If role name is different**, update the controller code:
```php
// In HodCombinedAccessController.php line 302
$nextApprover = User::whereHas('roles', fn($q) => 
    $q->where('name', 'YOUR_ACTUAL_ROLE_NAME')  // Change this
)->first();
```

---

### 4. Check Laravel Logs

**View logs for SMS errors:**
```bash
# Windows PowerShell
Get-Content backend\storage\logs\laravel.log -Tail 100 | Select-String "SMS"

# Or open the file directly:
notepad backend\storage\logs\laravel.log
```

**Look for:**
- `HOD SMS notifications sent` (success message)
- `HOD SMS notification failed` (error message)
- Any SMS-related warnings or errors

---

### 5. SMS Module Configuration Issue

**Check SMS configuration:**
```php
// In tinker
php artisan tinker

>>> $sms = app(SmsModule::class);
>>> $sms->isEnabled();  // Should return true
>>> $sms->isTestMode();  // Check if in test mode

// Test SMS manually
>>> $sms->sendSms('0712345678', 'Test message', 'test');
// Should return: ['success' => true/false, 'message' => '...']
```

---

### 6. Database Transaction Issue

The HOD approval happens inside a transaction. If SMS fails, it won't rollback the approval (by design).

**Check SMS was attempted:**
```sql
-- Check sms_logs table (if exists)
SELECT * FROM sms_logs 
WHERE created_at >= NOW() - INTERVAL 1 HOUR
ORDER BY created_at DESC
LIMIT 10;

-- Check request SMS status fields
SELECT id, staff_name, hod_status,
       sms_to_hod_status,
       sms_sent_to_hod_at,
       sms_to_divisional_status,
       sms_sent_to_divisional_at
FROM user_access 
WHERE hod_status = 'hod_approved'
ORDER BY hod_approved_at DESC
LIMIT 5;
```

---

## 🔧 Quick Fixes to Try

### Fix 1: Manual SMS Trigger

If SMS service is working, you can manually trigger SMS for a specific request:

```bash
php artisan sms:send-pending --request-id=<request_id>
```

### Fix 2: Enable SMS if Disabled

```bash
# Edit .env file
notepad backend\.env

# Add or update:
SMS_ENABLED=true
SMS_TEST_MODE=false

# Clear config cache
php artisan config:clear
```

### Fix 3: Add Phone Number to Divisional Director

1. Login as admin
2. Go to Users management
3. Find Divisional Director user
4. Add phone number (format: 0712345678 or 255712345678)
5. Save

---

## 📊 Step-by-Step Debugging

### Step 1: Check if SMS Module is Working
```bash
php artisan tinker
```

```php
$request = \App\Models\UserAccess::find(<request_id>);
$sms = app(\App\Services\SmsModule::class);

// Check if enabled
$sms->isEnabled();  // Should be true

// Get next approver
$nextApprover = \App\Models\User::whereHas('roles', fn($q) => 
    $q->where('name', 'divisional_director')
)->first();

echo "Next Approver: " . ($nextApprover ? $nextApprover->name : 'NOT FOUND') . "\n";
echo "Phone: " . ($nextApprover->phone ?? 'NO PHONE') . "\n";

// Try sending notification
if ($nextApprover && $nextApprover->phone) {
    $result = $sms->notifyRequestApproved(
        $request,
        auth()->user(),
        'hod',
        $nextApprover
    );
    
    dd($result);  // Show result
}
```

---

### Step 2: Check Request Status in Database

```sql
-- Find the approved request
SELECT 
    id,
    staff_name,
    hod_status,
    sms_to_hod_status,
    sms_sent_to_hod_at,
    sms_to_divisional_status,
    sms_sent_to_divisional_at,
    hod_approved_at
FROM user_access
WHERE id = <your_request_id>;
```

**Expected Result:**
- `hod_status` = 'hod_approved'
- `sms_to_hod_status` = 'sent' (HOD was notified)
- `sms_to_divisional_status` = 'sent' (Divisional should be notified)
- `sms_sent_to_divisional_at` = timestamp (when SMS was sent)

**If `sms_to_divisional_status` = 'pending' or 'failed':**
- SMS was not sent successfully
- Check logs for error

---

### Step 3: Test SMS Manually

```bash
# Send test SMS to Divisional Director
php artisan tinker
```

```php
$user = \App\Models\User::whereHas('roles', fn($q) => 
    $q->where('name', 'divisional_director')
)->first();

if ($user && $user->phone) {
    $sms = app(\App\Services\SmsModule::class);
    $result = $sms->sendSms(
        $user->phone, 
        'Test SMS from EABMS - Please ignore', 
        'test'
    );
    
    dd($result);
}
```

---

## 🎯 Most Likely Issues (In Order of Probability)

1. ✅ **SMS_ENABLED=false in .env** (80% likelihood)
2. ✅ **Divisional Director has no phone number** (15% likelihood)
3. ✅ **Wrong role name in database** (3% likelihood)
4. ✅ **SMS API credentials invalid** (2% likelihood)

---

## 🚀 Quick Fix Script

Run this script to diagnose all issues at once:

```bash
php artisan tinker
```

```php
echo "=== SMS DIAGNOSTIC TOOL ===\n\n";

// 1. Check SMS enabled
$smsEnabled = config('sms.enabled');
echo "1. SMS Enabled: " . ($smsEnabled ? '✅ YES' : '❌ NO') . "\n";

// 2. Check Divisional Director exists
$divDirector = \App\Models\User::whereHas('roles', fn($q) => 
    $q->where('name', 'divisional_director')
)->first();

echo "2. Divisional Director Found: " . ($divDirector ? '✅ YES' : '❌ NO') . "\n";

if ($divDirector) {
    echo "   Name: " . $divDirector->name . "\n";
    echo "   Phone: " . ($divDirector->phone ? '✅ ' . $divDirector->phone : '❌ NO PHONE') . "\n";
}

// 3. Check recent HOD approvals
$recentApprovals = \App\Models\UserAccess::where('hod_status', 'hod_approved')
    ->orderBy('hod_approved_at', 'desc')
    ->limit(3)
    ->get(['id', 'staff_name', 'sms_to_divisional_status', 'sms_sent_to_divisional_at']);

echo "\n3. Recent HOD Approvals:\n";
foreach ($recentApprovals as $req) {
    echo "   Request #{$req->id} - {$req->staff_name}\n";
    echo "   SMS Status: " . ($req->sms_to_divisional_status ?? 'N/A') . "\n";
    echo "   Sent At: " . ($req->sms_sent_to_divisional_at ?? 'Never') . "\n\n";
}

// 4. Check if SMS Module works
$sms = app(\App\Services\SmsModule::class);
echo "4. SMS Module Status:\n";
echo "   Enabled: " . ($sms->isEnabled() ? '✅ YES' : '❌ NO') . "\n";
echo "   Test Mode: " . ($sms->isTestMode() ? '⚠️ YES (using test endpoint)' : '✅ NO (using live endpoint)') . "\n";

echo "\n=== END DIAGNOSTIC ===\n";
```

---

## 📞 Support

After running the diagnostic, share the output to get specific help:
1. Copy the diagnostic output
2. Check the Laravel logs for errors
3. Verify `.env` SMS configuration

---

**Quick Actions:**
```bash
# Enable SMS
# Edit .env: SMS_ENABLED=true

# Clear config
php artisan config:clear

# Test SMS
php artisan sms:send-pending --request-id=<id>

# Check logs
tail -f backend/storage/logs/laravel.log | grep -i sms
```
