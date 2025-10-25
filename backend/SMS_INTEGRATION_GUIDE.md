# SMS Integration Guide

## Overview
This guide explains how to integrate automatic SMS notifications into the approval workflow.

## Configuration

### 1. Environment Variables (.env)
Add these to your `.env` file:

```env
# SMS Configuration
SMS_ENABLED=true
SMS_TEST_MODE=true  # Set to false for production
SMS_API_KEY=beneth
SMS_SECRET_KEY=Beneth@1701
SMS_SENDER_ID=KODA TECH

# Queue Configuration (recommended for production)
SMS_QUEUE_ENABLED=true
QUEUE_CONNECTION=database  # or redis
```

### 2. Test the SMS Configuration
Before integrating, test your SMS setup:

```bash
# Run this artisan command to test SMS
php artisan tinker

# Then run:
$smsService = app(\App\Services\SmsService::class);
$result = $smsService->sendSms('255700000000', 'Test message from MNH IT System', 'test');
print_r($result);
```

## Integration into Approval Controllers

### Example 1: HOD Approval (HodCombinedAccessController.php)

Add this code after successfully approving a request:

```php
use App\Events\RequestApproved;
use App\Models\User;

// In your hodApprove method, after successful approval:
public function hodApprove(Request $request, $id)
{
    // ... your existing approval logic ...
    
    // After approval is successful:
    $combinedRequest->status = 'hod_approved';
    $combinedRequest->hod_approved_at = now();
    $combinedRequest->hod_approved_by = auth()->id();
    $combinedRequest->save();

    // Get the Divisional Director for this request
    $divisionalDirector = User::whereHas('roles', function($q) {
        $q->where('name', 'divisional_director');
    })
    ->where('department_id', $combinedRequest->department_id)
    ->first();

    // Trigger SMS notifications
    event(new RequestApproved(
        $combinedRequest,
        auth()->user(),
        'hod',
        $divisionalDirector
    ));

    return response()->json([
        'success' => true,
        'message' => 'Request approved successfully. SMS notifications sent.',
        'data' => $combinedRequest
    ]);
}
```

### Example 2: Divisional Director Approval

```php
use App\Events\RequestApproved;
use App\Models\User;

public function divisionalApprove(Request $request, $id)
{
    // ... your existing approval logic ...
    
    $combinedRequest->status = 'divisional_approved';
    $combinedRequest->divisional_approved_at = now();
    $combinedRequest->divisional_approved_by = auth()->id();
    $combinedRequest->save();

    // Get the ICT Director
    $ictDirector = User::whereHas('roles', function($q) {
        $q->where('name', 'ict_director');
    })->first();

    // Trigger SMS notifications
    event(new RequestApproved(
        $combinedRequest,
        auth()->user(),
        'divisional',
        $ictDirector
    ));

    return response()->json([
        'success' => true,
        'message' => 'Request approved successfully. SMS notifications sent.'
    ]);
}
```

### Example 3: ICT Director Approval

```php
use App\Events\RequestApproved;
use App\Models\User;

public function ictDirectorApprove(Request $request, $id)
{
    // ... your existing approval logic ...
    
    $combinedRequest->status = 'ict_director_approved';
    $combinedRequest->ict_director_approved_at = now();
    $combinedRequest->ict_director_approved_by = auth()->id();
    $combinedRequest->save();

    // Get Head of IT
    $headOfIt = User::whereHas('roles', function($q) {
        $q->where('name', 'head_of_it');
    })->first();

    // Trigger SMS notifications
    event(new RequestApproved(
        $combinedRequest,
        auth()->user(),
        'ict_director',
        $headOfIt
    ));

    return response()->json([
        'success' => true,
        'message' => 'Request approved successfully. SMS notifications sent.'
    ]);
}
```

### Example 4: Head of IT Approval (Final)

```php
use App\Events\RequestApproved;

public function headOfItApprove(Request $request, $id)
{
    // ... your existing approval logic ...
    
    $combinedRequest->status = 'head_it_approved';
    $combinedRequest->head_it_approved_at = now();
    $combinedRequest->head_it_approved_by = auth()->id();
    $combinedRequest->save();

    // Trigger SMS notifications (no next approver - this is final)
    event(new RequestApproved(
        $combinedRequest,
        auth()->user(),
        'head_it',
        null  // No next approver
    ));

    return response()->json([
        'success' => true,
        'message' => 'Request approved successfully. Final SMS notification sent.'
    ]);
}
```

## What Happens When event() is Called?

1. **Requester gets notified**: The person who submitted the request receives an SMS saying their request was approved at this level.

2. **Next approver gets notified**: If there's a next person in the approval chain, they receive an SMS saying they have a new request to review.

3. **SMS is queued**: If `SMS_QUEUE_ENABLED=true`, SMS sending happens in background job.

4. **Logged**: All SMS attempts are logged in `sms_logs` table.

## SMS Message Examples

### Requester Notification:
```
Dear John Doe, your Jeeva & Wellsoft request has been APPROVED by HOD. Reference: REQ-000123. You will be notified on next steps. - MNH IT
```

### Next Approver Notification:
```
PENDING APPROVAL: Jeeva & Wellsoft request from John Doe (Pharmacy Department) requires your review. Ref: REQ-000123. Please check the system. - MNH IT
```

## Monitoring SMS

### Check SMS Logs
```bash
# Via tinker
php artisan tinker
$logs = \App\Models\SmsLog::latest()->take(10)->get();

# Or create an API endpoint to view logs (already exists):
GET /api/v1/sms/logs
GET /api/v1/sms/statistics
```

### View SMS Dashboard
Access the SMS statistics endpoint to see:
- Total SMS sent
- Success rate
- Failed SMS
- Recent activity

## Production Checklist

Before going live:

1. ✅ Set `SMS_TEST_MODE=false` in production `.env`
2. ✅ Verify API credentials are correct
3. ✅ Test with 2-3 real phone numbers
4. ✅ Enable queue worker: `php artisan queue:work`
5. ✅ Monitor logs: `tail -f storage/logs/laravel.log | grep SMS`
6. ✅ Set up cron for failed job retry: `* * * * * cd /path && php artisan schedule:run`

## Troubleshooting

### SMS not sending?
1. Check `.env` has `SMS_ENABLED=true`
2. Verify API credentials
3. Check logs: `storage/logs/laravel.log`
4. Test API directly using test endpoint

### Phone number format issues?
Phone numbers are automatically formatted to:
- Remove leading zero: `0712345678` → `255712345678`
- Add country code: `712345678` → `255712345678`

### Rate limiting?
Default: 5 SMS per hour per number. Configure in `config/sms.php`:
```php
'rate_limit' => [
    'max_per_hour_per_number' => env('SMS_RATE_LIMIT_PER_HOUR', 5),
],
```

## Advanced Usage

### Send Custom SMS
```php
use App\Services\SmsService;

$smsService = app(SmsService::class);
$result = $smsService->sendSms(
    '255712345678',
    'Your custom message here',
    'custom_type'
);
```

### Bulk SMS
```php
$result = $smsService->sendBulkSms(
    ['255712345678', '255787654321'],
    'Bulk announcement message',
    'announcement'
);
```

## Support

For issues or questions:
1. Check logs in `storage/logs/laravel.log`
2. Review SMS logs in database: `sms_logs` table
3. Test API connection using test endpoint
4. Contact KODA TECH support if API issues persist
