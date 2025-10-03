# SMS Approval Workflow Integration Guide

## Overview

This guide shows how to integrate SMS notifications into existing approval workflows. We have successfully integrated SMS notifications into the following controllers and services:

✅ **UserAccessWorkflowService** - Complete integration  
✅ **ModuleAccessApprovalController** - Complete integration  
✅ **ICTApprovalController** - Complete integration  

## Integration Pattern

Follow this pattern to add SMS notifications to other approval controllers:

### Step 1: Import Required Classes

```php
use App\Events\ApprovalRequestSubmitted;
use App\Events\ApprovalStatusChanged;
```

### Step 2: Fire Events at Appropriate Times

#### When a New Request is Submitted:
```php
try {
    $approvers = $this->getNextApprovers($request);
    
    ApprovalRequestSubmitted::dispatch(
        $user,                    // User who made the request
        $request,                 // The request object
        'request_type',           // Type identifier (e.g., 'user_access', 'device_booking')
        $approvers,              // Array of users who need to approve
        [                        // Additional context data
            'department' => $user->department->name,
            'details' => $this->getRequestSummary($request)
        ]
    );
    
    Log::info('SMS notification event fired for new request', [
        'request_id' => $request->id,
        'user_id' => $user->id
    ]);
} catch (\Exception $e) {
    Log::error('Failed to fire SMS notification', [
        'request_id' => $request->id,
        'error' => $e->getMessage()
    ]);
}
```

#### When Status Changes (Approve/Reject):
```php
try {
    $user = $request->user;
    $approver = auth()->user();
    
    // Get next approvers if request was approved
    $additionalNotifyUsers = [];
    if ($status === 'approved') {
        $additionalNotifyUsers = $this->getNextApprovers($request);
    }
    
    ApprovalStatusChanged::dispatch(
        $user,                    // User who made the original request
        $request,                 // The request object
        'request_type',           // Type identifier
        $oldStatus,               // Previous status
        $newStatus,               // New status
        $approver,               // User who approved/rejected
        $comments,               // Approval/rejection comments
        $additionalNotifyUsers   // Next approvers (optional)
    );
    
    Log::info('SMS notification event fired for status change', [
        'request_id' => $request->id,
        'old_status' => $oldStatus,
        'new_status' => $newStatus
    ]);
} catch (\Exception $e) {
    Log::error('Failed to fire SMS notification for status change', [
        'request_id' => $request->id,
        'error' => $e->getMessage()
    ]);
}
```

## Integration Examples for Remaining Controllers

### HodCombinedAccessController

Add this pattern to approval methods:

```php
// In approval method after successful approval
try {
    ApprovalStatusChanged::dispatch(
        $userAccess->user,
        $userAccess,
        'combined_access',
        'pending_hod',
        'hod_approved',
        auth()->user(),
        $request->comments ?? 'Request approved by HOD'
    );
} catch (\Exception $e) {
    Log::error('Failed to fire SMS notification for HOD combined access approval', [
        'request_id' => $userAccess->id,
        'error' => $e->getMessage()
    ]);
}
```

### DictCombinedAccessController

```php
// In approval method after successful approval
try {
    ApprovalStatusChanged::dispatch(
        $userAccess->user,
        $userAccess,
        'combined_access',
        'hod_approved',
        'divisional_approved',
        auth()->user(),
        $request->comments ?? 'Request approved by Divisional Director'
    );
} catch (\Exception $e) {
    Log::error('Failed to fire SMS notification for Divisional combined access approval', [
        'request_id' => $userAccess->id,
        'error' => $e->getMessage()
    ]);
}
```

### BothServiceFormController

```php
// In approval method after successful approval
try {
    ApprovalStatusChanged::dispatch(
        $form->user,
        $form,
        'both_service',
        'pending',
        'approved',
        auth()->user(),
        $request->comments ?? 'Both service form approved'
    );
} catch (\Exception $e) {
    Log::error('Failed to fire SMS notification for both service form approval', [
        'request_id' => $form->id,
        'error' => $e->getMessage()
    ]);
}
```

### BookingServiceController

```php
// In approval method after successful approval
try {
    ApprovalStatusChanged::dispatch(
        $booking->user,
        $booking,
        'service_booking',
        'pending',
        'approved',
        auth()->user(),
        $request->comments ?? 'Service booking approved'
    );
} catch (\Exception $e) {
    Log::error('Failed to fire SMS notification for service booking approval', [
        'booking_id' => $booking->id,
        'error' => $e->getMessage()
    ]);
}
```

## Helper Methods to Add

Each controller should include these helper methods:

### getNextApprovers Method

```php
/**
 * Get next approvers based on current status
 */
private function getNextApprovers($request): array
{
    $approvers = [];
    
    switch ($request->status) {
        case 'pending':
            // Get HODs for the department
            $approvers = User::whereHas('roles', function ($query) {
                $query->where('name', 'head_of_department');
            })->whereNotNull('phone')->get()->toArray();
            break;
            
        case 'hod_approved':
            // Get Divisional Directors
            $approvers = User::whereHas('roles', function ($query) {
                $query->where('name', 'divisional_director');
            })->whereNotNull('phone')->get()->toArray();
            break;
            
        // Add more cases as needed...
    }
    
    return $approvers;
}
```

### getRequestSummary Method

```php
/**
 * Get request summary for SMS notifications
 */
private function getRequestSummary($request): string
{
    // Customize based on your request type
    if (isset($request->request_type) && is_array($request->request_type)) {
        return implode(', ', $request->request_type);
    }
    
    return $request->purpose ?? 'System Access Request';
}
```

## Status Mapping

Use consistent status naming across controllers for SMS templates:

```php
// Standard status progression
'pending' -> 'hod_approved' -> 'divisional_approved' -> 'ict_director_approved' -> 'head_it_approved' -> 'implemented'

// Rejection statuses
'hod_rejected', 'divisional_rejected', 'ict_director_rejected', 'head_it_rejected', 'rejected'
```

## Testing Integration

After adding SMS notifications to a controller:

1. **Test New Request Creation**
   ```bash
   # Check that ApprovalRequestSubmitted event fires
   tail -f storage/logs/laravel.log | grep "SMS notification event fired for new"
   ```

2. **Test Status Changes**
   ```bash
   # Check that ApprovalStatusChanged event fires
   tail -f storage/logs/laravel.log | grep "SMS notification event fired for status change"
   ```

3. **Test Queue Processing**
   ```bash
   php artisan queue:work --queue=sms
   ```

4. **Check SMS Logs**
   ```bash
   # View SMS attempts in database
   php artisan tinker
   >>> App\Models\SmsLog::latest()->take(10)->get()
   ```

## Error Handling

All SMS notification code is wrapped in try-catch blocks to ensure:

- ❌ **SMS failures don't break approval workflows**
- ✅ **Errors are logged for debugging**
- ✅ **Main business logic continues to work**

## Best Practices

1. **Always wrap SMS events in try-catch blocks**
2. **Log SMS events for debugging and monitoring**
3. **Use consistent request type identifiers**
4. **Include meaningful context in event data**
5. **Don't fail the main workflow if SMS fails**
6. **Use queued events for better performance**

## Next Steps

To complete SMS integration across all controllers:

1. Apply the integration pattern to remaining controllers:
   - `HodCombinedAccessController`
   - `DictCombinedAccessController` 
   - `BothServiceFormController`
   - `BookingServiceController`
   - `HeadOfItController`
   - `IctOfficerController`

2. Test each integration thoroughly

3. Monitor SMS delivery and error rates

4. Adjust templates and rate limits as needed

---

**Note**: The SMS service will automatically handle phone number formatting, rate limiting, template selection, and delivery attempts based on the event data provided.