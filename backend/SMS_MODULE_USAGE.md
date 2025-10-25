# SMS Module - Simple Usage Guide

## ✨ One File, Everything You Need

All SMS functionality is now in **ONE file**: `app/Services/SmsModule.php`

No events, no listeners, no multiple folders. Just one simple class.

---

## 🚀 Quick Setup

### 1. Add to `.env`:
```env
SMS_ENABLED=true
SMS_TEST_MODE=true
SMS_API_KEY=beneth
SMS_SECRET_KEY=Beneth@1701
SMS_SENDER_ID=KODA TECH
```

### 2. Test It:
```bash
php artisan tinker
$sms = app(\App\Services\SmsModule::class);
$result = $sms->testConfiguration('255700000000');
print_r($result);
exit
```

---

## 📱 How to Use in Controllers

### Example 1: HOD Approves Request

```php
use App\Services\SmsModule;
use App\Models\User;

public function hodApprove(Request $request, $id)
{
    // ... your approval logic ...
    
    $combinedRequest->status = 'hod_approved';
    $combinedRequest->save();

    // Get next approver
    $divisionalDirector = User::whereHas('roles', fn($q) => 
        $q->where('name', 'divisional_director')
    )->first();

    // Send SMS notifications
    $sms = app(SmsModule::class);
    $sms->notifyRequestApproved(
        $combinedRequest,
        auth()->user(),
        'hod',
        $divisionalDirector
    );

    return response()->json([
        'success' => true,
        'message' => 'Request approved. SMS notifications sent.'
    ]);
}
```

### Example 2: Divisional Director Approves

```php
use App\Services\SmsModule;

public function divisionalApprove(Request $request, $id)
{
    // ... your approval logic ...
    
    $combinedRequest->status = 'divisional_approved';
    $combinedRequest->save();

    // Get ICT Director
    $ictDirector = User::whereHas('roles', fn($q) => 
        $q->where('name', 'ict_director')
    )->first();

    // Send SMS
    $sms = app(SmsModule::class);
    $sms->notifyRequestApproved(
        $combinedRequest,
        auth()->user(),
        'divisional',
        $ictDirector
    );

    return response()->json(['success' => true]);
}
```

### Example 3: ICT Director Approves

```php
public function ictDirectorApprove(Request $request, $id)
{
    // ... your approval logic ...
    
    $combinedRequest->status = 'ict_director_approved';
    $combinedRequest->save();

    // Get Head of IT
    $headOfIt = User::whereHas('roles', fn($q) => 
        $q->where('name', 'head_of_it')
    )->first();

    // Send SMS
    $sms = app(SmsModule::class);
    $sms->notifyRequestApproved(
        $combinedRequest,
        auth()->user(),
        'ict_director',
        $headOfIt
    );

    return response()->json(['success' => true]);
}
```

### Example 4: Head of IT Approves (Final)

```php
public function headOfItApprove(Request $request, $id)
{
    // ... your approval logic ...
    
    $combinedRequest->status = 'head_it_approved';
    $combinedRequest->save();

    // Send SMS (no next approver)
    $sms = app(SmsModule::class);
    $sms->notifyRequestApproved(
        $combinedRequest,
        auth()->user(),
        'head_it',
        null  // Final approval
    );

    return response()->json(['success' => true]);
}
```

---

## 🛠️ Other Available Methods

### Send Custom SMS:
```php
$sms = app(\App\Services\SmsModule::class);
$result = $sms->sendSms('255712345678', 'Your message here', 'custom');
```

### Send Bulk SMS:
```php
$sms = app(\App\Services\SmsModule::class);
$recipients = ['255712345678', '255787654321'];
$result = $sms->sendBulkSms($recipients, 'Bulk message', 'announcement');
```

### Get Statistics:
```php
$sms = app(\App\Services\SmsModule::class);
$stats = $sms->getStatistics();
// Returns: ['total' => 100, 'sent' => 95, 'failed' => 5, 'success_rate' => 95.00]
```

### Check Status:
```php
$sms = app(\App\Services\SmsModule::class);
$enabled = $sms->isEnabled();  // true/false
$testMode = $sms->isTestMode();  // true/false
```

---

## 📋 What `notifyRequestApproved()` Does

When you call:
```php
$sms->notifyRequestApproved($request, $approver, 'hod', $nextApprover);
```

It automatically:

1. ✅ Sends SMS to **requester**:
   ```
   Dear John, your Jeeva & Wellsoft request has been APPROVED by HOD. 
   Reference: REQ-000123. You will be notified on next steps. - MNH IT
   ```

2. ✅ Sends SMS to **next approver** (if provided):
   ```
   PENDING APPROVAL: Jeeva & Wellsoft request from John (Pharmacy Dept) 
   requires your review. Ref: REQ-000123. Please check the system. - MNH IT
   ```

3. ✅ Logs everything to database
4. ✅ Handles errors gracefully
5. ✅ Respects rate limits

---

## 🔍 Quick Debug

### Check Recent SMS:
```bash
php artisan tinker
\App\Models\SmsLog::latest()->take(5)->get(['phone_number','status','message']);
```

### Watch Logs:
```bash
tail -f storage/logs/laravel.log | grep SMS
```

### Test Configuration:
```php
$sms = app(\App\Services\SmsModule::class);
$result = $sms->testConfiguration('255700000000');
print_r($result);
```

---

## 🎯 Complete Integration Example

Here's everything you need in one approval method:

```php
use App\Services\SmsModule;
use App\Models\User;
use Illuminate\Http\Request;

public function hodApprove(Request $request, $id)
{
    // 1. Find the request
    $combinedRequest = CombinedAccessRequest::findOrFail($id);
    
    // 2. Update approval status
    $combinedRequest->status = 'hod_approved';
    $combinedRequest->hod_approved_at = now();
    $combinedRequest->hod_approved_by = auth()->id();
    $combinedRequest->save();
    
    // 3. Find next approver
    $nextApprover = User::whereHas('roles', fn($q) => 
        $q->where('name', 'divisional_director')
    )->first();
    
    // 4. Send SMS notifications
    $sms = app(SmsModule::class);
    $sms->notifyRequestApproved(
        $combinedRequest,
        auth()->user(),
        'hod',
        $nextApprover
    );
    
    // 5. Return success
    return response()->json([
        'success' => true,
        'message' => 'Request approved successfully',
        'data' => $combinedRequest
    ]);
}
```

That's it! Copy this pattern to all approval methods.

---

## ✅ Benefits of Single Module

- ✅ **One file** to maintain (`SmsModule.php`)
- ✅ **No events** to register
- ✅ **No listeners** to create
- ✅ **Simple** to use: just call methods
- ✅ **Easy** to test and debug
- ✅ **All SMS logic** in one place
- ✅ Config still in `config/sms.php`

---

## 📞 Support

**File Location**: `backend/app/Services/SmsModule.php`
**Config Location**: `backend/config/sms.php`

For more details, see the inline comments in `SmsModule.php` - everything is well documented!

---

**Simple. Clean. Works.** ✨
