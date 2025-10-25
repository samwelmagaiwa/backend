# Controller SMS Integration Guide

## 🎯 Quick Integration Steps

Add SMS notifications to approval controllers in 3 simple steps:

### Step 1: Add SmsModule Import

At the top of your controller file, add:

```php
use App\Services\SmsModule;
use App\Models\User;
```

### Step 2: Get Next Approver

After approval, determine who approves next:

```php
// For HOD approval → Divisional Director
$nextApprover = User::whereHas('roles', fn($q) => 
    $q->where('name', 'divisional_director')
)->first();

// For Divisional → ICT Director
$nextApprover = User::whereHas('roles', fn($q) => 
    $q->where('name', 'ict_director')
)->first();

// For ICT Director → Head of IT
$nextApprover = User::whereHas('roles', fn($q) => 
    $q->where('name', 'head_of_it')
)->first();

// For Head of IT (final) → No next approver
$nextApprover = null;
```

### Step 3: Send SMS Notifications

Right after `DB::commit()`:

```php
DB::commit();

// Send SMS notifications
try {
    $sms = app(SmsModule::class);
    $sms->notifyRequestApproved(
        $userAccessRequest,
        auth()->user(),
        'hod',  // or 'divisional', 'ict_director', 'head_it'
        $nextApprover
    );
} catch (\Exception $e) {
    Log::warning('SMS notification failed', ['error' => $e->getMessage()]);
    // Don't fail the approval if SMS fails
}
```

---

## 📋 Controller-by-Controller Examples

### 1. HOD Controller

**File:** `app/Http/Controllers/Api/v1/HodCombinedAccessController.php`

**Method:** `updateApproval()`

**Add after line 294 (after `DB::commit()`):**

```php
DB::commit();

// Send SMS notifications
if ($validatedData['hod_status'] === 'approved') {
    try {
        $nextApprover = User::whereHas('roles', fn($q) => 
            $q->where('name', 'divisional_director')
        )->first();
        
        $sms = app(SmsModule::class);
        $sms->notifyRequestApproved(
            $userAccessRequest,
            auth()->user(),
            'hod',
            $nextApprover
        );
    } catch (\Exception $e) {
        Log::warning('HOD SMS notification failed', ['error' => $e->getMessage()]);
    }
}

Log::info('HOD Combined Access: Approval updated successfully with module data', [
```

---

### 2. Divisional Director Controller

**File:** `app/Http/Controllers/Api/v1/DivisionalCombinedAccessController.php`

**Method:** `updateApproval()`

**Add after `DB::commit()`:**

```php
DB::commit();

// Send SMS notifications
if ($validatedData['divisional_status'] === 'approved') {
    try {
        $nextApprover = User::whereHas('roles', fn($q) => 
            $q->where('name', 'ict_director')
        )->first();
        
        $sms = app(SmsModule::class);
        $sms->notifyRequestApproved(
            $userAccessRequest,
            auth()->user(),
            'divisional',
            $nextApprover
        );
    } catch (\Exception $e) {
        Log::warning('Divisional SMS notification failed', ['error' => $e->getMessage()]);
    }
}
```

---

### 3. ICT Director Controller

**File:** `app/Http/Controllers/Api/v1/DictCombinedAccessController.php`

**Method:** `updateApproval()`

**Add after `DB::commit()`:**

```php
DB::commit();

// Send SMS notifications
if ($validatedData['ict_director_status'] === 'approved') {
    try {
        $nextApprover = User::whereHas('roles', fn($q) => 
            $q->where('name', 'head_of_it')
        )->first();
        
        $sms = app(SmsModule::class);
        $sms->notifyRequestApproved(
            $userAccessRequest,
            auth()->user(),
            'ict_director',
            $nextApprover
        );
    } catch (\Exception $e) {
        Log::warning('ICT Director SMS notification failed', ['error' => $e->getMessage()]);
    }
}
```

---

### 4. Head of IT Controller

**File:** `app/Http/Controllers/Api/v1/HeadOfItController.php`

**Method:** `approveRequest()`

**Add after `DB::commit()`:**

```php
DB::commit();

// Send SMS notifications (final approval - no next approver)
try {
    $sms = app(SmsModule::class);
    $sms->notifyRequestApproved(
        $userAccessRequest,
        auth()->user(),
        'head_it',
        null  // No next approver for final approval
    );
} catch (\Exception $e) {
    Log::warning('Head of IT SMS notification failed', ['error' => $e->getMessage()]);
}
```

---

## ✅ What Happens When SMS Sends

### For Requester:
```
Dear John, your Jeeva & Wellsoft request has been APPROVED by HOD. 
Reference: REQ-000123. You will be notified on next steps. - MNH IT
```

### For Next Approver:
```
PENDING APPROVAL: Jeeva & Wellsoft request from John (Pharmacy Dept) 
requires your review. Ref: REQ-000123. Please check the system. - MNH IT
```

---

## 🔒 Error Handling

SMS errors **never fail the approval**. The approval still succeeds even if SMS fails. Errors are logged:

```php
try {
    // SMS code
} catch (\Exception $e) {
    Log::warning('SMS notification failed', ['error' => $e->getMessage()]);
    // Continue - don't throw
}
```

---

## 🧪 Testing

1. **Enable SMS in `.env`:**
   ```env
   SMS_ENABLED=true
   SMS_TEST_MODE=true
   ```

2. **Check logs after approval:**
   ```bash
   tail -f storage/logs/laravel.log | grep SMS
   ```

3. **Check SMS database logs:**
   ```sql
   SELECT * FROM sms_logs ORDER BY created_at DESC LIMIT 10;
   ```

---

## 📝 Controllers to Update

| Controller | Priority | Method | Approval Level |
|-----------|----------|--------|----------------|
| `HodCombinedAccessController` | ⚡ HIGH | `updateApproval()` | `hod` |
| `DivisionalCombinedAccessController` | ⚡ HIGH | `updateApproval()` | `divisional` |
| `DictCombinedAccessController` | ⚡ HIGH | `updateApproval()` | `ict_director` |
| `HeadOfItController` | ⚡ HIGH | `approveRequest()` | `head_it` |
| `BothServiceFormController` | 🟡 MEDIUM | Multiple methods | All levels |
| `ICTApprovalController` | 🟡 MEDIUM | `approveDeviceBorrowingRequest()` | `ict_officer` |

---

## 🎯 Next Steps

1. ✅ Add imports to each controller
2. ✅ Identify the approval method in each controller
3. ✅ Find the `DB::commit()` line
4. ✅ Add SMS notification code after commit
5. ✅ Test with a real approval
6. ✅ Monitor logs for success/errors

---

**Simple. Fast. Reliable.** 🚀
