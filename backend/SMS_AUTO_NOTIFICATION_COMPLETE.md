# SMS Auto-Notification Flow - Complete Integration

## Overview
SMS notifications are now automatically sent at every approval stage in the access request workflow.

## Workflow Integration Summary

### 1. Staff Submits Request
**Controller**: `UserAccessController@store`
**Triggers**: Automatic SMS to HOD
**Recipients**: Head of Department (HOD)
**Message**: "PENDING APPROVAL: [Request Type] request from [Staff Name] requires your review. Ref: [REQ-000XXX]. Please check the system. - MNH IT"

```php
// Location: app/Http/Controllers/Api/v1/UserAccessController.php (lines 567-587)
if ($hod->phone) {
    $sms = app(SmsModule::class);
    $smsResult = $sms->sendSms($hod->phone, $message, 'pending_notification');
    if ($smsResult['success']) {
        $userAccess->update([
            'sms_sent_to_hod_at' => now(),
            'sms_to_hod_status' => 'sent'
        ]);
    }
}
```

### 2. HOD Approves Request
**Controller**: `HodCombinedAccessController@updateApproval`
**Triggers**: Automatic SMS to requester + next approver
**Recipients**: 
- Requester (Staff who submitted)
- Divisional Director (next approver)

```php
// Location: app/Http/Controllers/Api/v1/HodCombinedAccessController.php (lines 298-326)
if ($validatedData['hod_status'] === 'approved') {
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
}
```

### 3. Divisional Director Approves
**Controller**: `DivisionalCombinedAccessController@updateApproval`
**Triggers**: Automatic SMS to requester + next approver
**Recipients**:
- Requester (confirmation of progress)
- ICT Director (next approver)

```php
// Location: app/Http/Controllers/Api/v1/DivisionalCombinedAccessController.php (lines 269-296)
if ($validatedData['divisional_status'] === 'approved') {
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
}
```

### 4. ICT Director Approves
**Controller**: `DictCombinedAccessController@updateApproval`
**Triggers**: Automatic SMS to requester + next approver
**Recipients**:
- Requester (confirmation of progress)
- Head of IT (next approver)

```php
// Location: app/Http/Controllers/Api/v1/DictCombinedAccessController.php (lines 387-423)
if ($approvalStatus === 'approved') {
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
}
```

### 5. Head of IT Approves
**Controller**: `DictCombinedAccessController@updateApproval` (same controller, different role)
**Triggers**: Automatic SMS to requester
**Recipients**:
- Requester (final approval confirmation)
- ICT Officer (implementation assignment notification)

```php
// Location: app/Http/Controllers/Api/v1/DictCombinedAccessController.php (lines 387-423)
if ($isHeadOfIT && $approvalStatus === 'approved') {
    $sms = app(SmsModule::class);
    $sms->notifyRequestApproved(
        $userAccessRequest,
        auth()->user(),
        'head_it',
        null  // No next approver, goes to ICT Officer
    );
}
```

## SMS Tracking

### Database Columns
All SMS notifications are tracked in the `user_access` table:

```sql
-- HOD notification tracking
sms_sent_to_hod_at (timestamp)
sms_to_hod_status (enum: 'pending', 'sent', 'failed')

-- Divisional Director notification tracking
sms_sent_to_divisional_at (timestamp)
sms_to_divisional_status (enum: 'pending', 'sent', 'failed')

-- ICT Director notification tracking
sms_sent_to_ict_director_at (timestamp)
sms_to_ict_director_status (enum: 'pending', 'sent', 'failed')

-- Head of IT notification tracking
sms_sent_to_head_it_at (timestamp)
sms_to_head_it_status (enum: 'pending', 'sent', 'failed')

-- Requester notification tracking
sms_sent_to_requester_at (timestamp)
sms_to_requester_status (enum: 'pending', 'sent', 'failed')
```

## SMS Module Configuration

### Environment Variables
```env
# .env configuration
SMS_ENABLED=true
SMS_TEST_MODE=false  # MUST be false for real SMS sending
SMS_API_KEY=beneth
SMS_SECRET_KEY=Beneth@1701
SMS_SENDER_ID="KODA TECH"
```

### Phone Number Auto-Formatting
The SmsModule automatically formats phone numbers:
- `0712345678` → `+255712345678`
- `712345678` → `+255712345678`
- `0612345678` → `+255612345678`
- `+255712345678` → `+255712345678` (no change)

## Complete Flow Diagram

```
[Staff Submits Request]
         ↓
    [SMS to HOD] ← UserAccessController
         ↓
    [HOD Approves]
         ↓
    [SMS to Requester + Divisional Director] ← HodCombinedAccessController
         ↓
    [Divisional Approves]
         ↓
    [SMS to Requester + ICT Director] ← DivisionalCombinedAccessController
         ↓
    [ICT Director Approves]
         ↓
    [SMS to Requester + Head of IT] ← DictCombinedAccessController
         ↓
    [Head of IT Approves]
         ↓
    [SMS to Requester + ICT Officer] ← DictCombinedAccessController
         ↓
    [ICT Officer Implements]
```

## Testing

### Manual Command for Pending Requests
```bash
php artisan sms:send-pending --request-id=49
```

### Check SMS Status
```bash
php check-req49-sms.php
```

### View Logs
```bash
tail -f storage/logs/laravel.log | grep SMS
```

## Error Handling
All SMS sending is wrapped in try-catch blocks. If SMS fails:
1. Error is logged but doesn't stop the approval process
2. SMS status updated to 'failed' in database
3. User can see status in frontend (future implementation)

## Success Confirmation
- ✅ SMS sent to HOD when request submitted
- ✅ SMS sent to requester and next approver at each approval stage
- ✅ Phone numbers auto-formatted with +255 country code
- ✅ SMS delivery status tracked in database
- ✅ All approval controllers integrated with SMS

## Date Completed
2025-10-25

## Tested With
- Request #49
- Phone: +255617919104 (Chief Pharmacist)
- Result: SMS received successfully ✅
