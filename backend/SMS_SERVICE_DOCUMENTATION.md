# SMS Service Integration Documentation

## Overview

The SMS Service module provides comprehensive SMS notification functionality for the Muhimbili National Hospital (MNH) IT system, enabling bulk messaging and automated approval workflow notifications.

## Features

- ✅ Single and bulk SMS sending
- ✅ Role-based and department-based messaging
- ✅ Approval workflow integration via events
- ✅ Rate limiting and queue support
- ✅ SMS logging and statistics
- ✅ Tanzanian phone number formatting
- ✅ Multiple SMS provider support
- ✅ Template-based messaging
- ✅ Comprehensive error handling

## Installation & Setup

### 1. Environment Configuration

Copy the SMS configuration variables from `.env.sms.example` to your `.env` file:

```env
# SMS Service Configuration
SMS_ENABLED=true
SMS_API_URL=https://api.bongolive.co.tz/v1/send-sms
SMS_API_KEY=your_api_key_here
SMS_USERNAME=your_username_here
SMS_PASSWORD=your_password_here
SMS_SENDER_ID=MNH-IT

# Service Settings
SMS_TIMEOUT=30
SMS_RETRY_ATTEMPTS=3
SMS_RETRY_DELAY=60

# Rate Limiting
SMS_RATE_LIMIT_PER_HOUR=5
SMS_MAX_BULK_SIZE=100
SMS_BULK_DELAY=0.1

# Queue Settings
SMS_QUEUE_ENABLED=true
SMS_QUEUE_NAME=sms
SMS_MAX_TRIES=3
```

### 2. Database Migration

Run the SMS logs table migration:

```bash
php artisan migrate
```

### 3. Event Listener Registration

Add the following to your `EventServiceProvider.php`:

```php
protected $listen = [
    ApprovalRequestSubmitted::class => [
        SendSmsNotification::class,
    ],
    ApprovalStatusChanged::class => [
        SendSmsNotification::class,
    ],
];
```

### 4. Queue Configuration

Make sure your queue worker is running:

```bash
php artisan queue:work --queue=sms
```

## Components Created

### 1. Services
- **`app/Services/SmsService.php`** - Main SMS service class

### 2. Events
- **`app/Events/ApprovalRequestSubmitted.php`** - Fired when approval requests are submitted
- **`app/Events/ApprovalStatusChanged.php`** - Fired when approval status changes

### 3. Listeners
- **`app/Listeners/SendSmsNotification.php`** - Queued listener for SMS notifications

### 4. Models
- **`app/Models/SmsLog.php`** - SMS logging model

### 5. Controllers
- **`app/Http/Controllers/Api/v1/SmsController.php`** - SMS management API
- **`app/Http/Controllers/Api/v1/ExampleApprovalController.php`** - Integration examples

### 6. Configuration
- **`config/sms.php`** - SMS service configuration

### 7. Database
- **Migration for `sms_logs` table** - SMS logging database structure

## Usage Examples

### 1. Basic SMS Sending

```php
use App\Services\SmsService;

$smsService = app(SmsService::class);

// Send single SMS
$result = $smsService->sendSms(
    '255700123456',
    'Your Jeeva access has been approved. You can now login.',
    'approval'
);

// Send bulk SMS
$recipients = ['255700123456', '255600987654'];
$result = $smsService->sendBulkSms(
    $recipients,
    'System maintenance scheduled for tonight 10 PM - 2 AM.',
    'announcement'
);
```

### 2. Approval Workflow Integration

```php
use App\Events\ApprovalRequestSubmitted;
use App\Events\ApprovalStatusChanged;

// When submitting a request
ApprovalRequestSubmitted::dispatch(
    $user,
    $request,
    'jeeva_access',
    $approvers,
    ['department' => $user->department->name]
);

// When updating request status
ApprovalStatusChanged::dispatch(
    $user,
    $request,
    'jeeva_access',
    $oldStatus,
    $newStatus,
    $approver,
    $reason,
    $additionalNotifyUsers
);
```

### 3. Role-based Messaging

```php
// Get all HODs
$hods = User::whereHas('roles', function ($query) {
    $query->where('name', 'head_of_department');
})->whereNotNull('phone')->get();

$result = $smsService->sendBulkSms(
    $hods->toArray(),
    'Monthly HOD meeting scheduled for tomorrow at 2 PM.',
    'announcement'
);
```

### 4. Direct Service Integration

```php
// In your existing controllers
public function approveRequest($requestId)
{
    // Your existing approval logic...
    
    // Send SMS notification
    $smsService = app(SmsService::class);
    $smsService->sendApprovalNotification(
        $user,
        'jeeva_access',
        'approved',
        ['reference' => $request->reference]
    );
}
```

## API Endpoints

### SMS Management

- **POST** `/api/sms/send` - Send single SMS
- **POST** `/api/sms/bulk` - Send bulk SMS
- **POST** `/api/sms/send-by-role` - Send SMS to users by role
- **POST** `/api/sms/send-by-department` - Send SMS to users by department
- **GET** `/api/sms/statistics` - Get SMS statistics
- **GET** `/api/sms/logs` - Get SMS logs with filtering
- **POST** `/api/sms/test` - Test SMS configuration

### Example API Usage

```bash
# Send single SMS
curl -X POST http://localhost:8000/api/sms/send \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "phone_number": "255700123456",
    "message": "Test SMS message",
    "type": "notification"
  }'

# Send bulk SMS by role
curl -X POST http://localhost:8000/api/sms/send-by-role \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "roles": ["head_of_department", "ict_officer"],
    "message": "Important system update tonight",
    "type": "announcement"
  }'
```

## Message Templates

Predefined templates are available in `config/sms.php`:

```php
'templates' => [
    'approval_pending' => "Dear {name}, your {type} request has been submitted (Ref: {ref}). You'll be notified once reviewed. - MNH IT",
    'approval_approved' => "Dear {name}, your {type} request has been APPROVED (Ref: {ref}). You can now proceed. - MNH IT",
    'approval_rejected' => "Dear {name}, your {type} request was REJECTED (Ref: {ref}). Reason: {reason}. Contact IT for help. - MNH IT",
    'approver_notification' => "New {type} request from {requester} ({department}) needs approval (Ref: {ref}). Please review in system. - MNH IT",
    // ... more templates
]
```

## SMS Providers

The service is configured for Tanzanian SMS providers but can be adapted for others:

### Supported/Tested Providers:
- **Bongo Live SMS** (Default configuration)
- Vodacom Tanzania
- Airtel Tanzania
- Tigo Tanzania
- Halotel Tanzania
### Adding New Providers:

1. Update the API URL in `.env`:
```env
SMS_API_URL=https://your-provider-api.com/send
```

2. Modify the `prepareSmsPayload()` method in `SmsService.php` if needed:
```php
protected function prepareSmsPayload(string $phoneNumber, string $message): array
{
    // Customize for your provider's expected format
    return [
        'to' => $phoneNumber,
        'message' => $message,
        'api_key' => $this->apiKey,
        // ... other provider-specific fields
    ];
}
```

## Phone Number Formatting

The service automatically formats Tanzanian phone numbers:

```php
// Input formats supported:
'0700123456'   → '255700123456'
'700123456'    → '255700123456'
'255700123456' → '255700123456' (no change)
'+255700123456'→ '255700123456'
```

## Rate Limiting

- **5 SMS per hour per phone number** (configurable)
- **100 SMS maximum per bulk request** (configurable)
- **0.1 second delay between bulk messages** (configurable)

## Queue Integration

SMS notifications are queued by default for better performance:

```php
// The listener implements ShouldQueue
class SendSmsNotification implements ShouldQueue
{
    use InteractsWithQueue;
    
    public $tries = 3;
    public $backoff = [60, 300, 900]; // Retry delays
}
```

## Monitoring & Logging

### SMS Logs

All SMS attempts are logged in the `sms_logs` table:

```php
// Get SMS statistics
$stats = $smsService->getStatistics('2025-01-01', '2025-01-31');

// Get recent SMS logs
$logs = SmsLog::with('user')
    ->where('type', 'approval')
    ->orderBy('created_at', 'desc')
    ->paginate(20);
```

### Application Logs

SMS activities are logged to Laravel's log system:

```php
// Successful SMS
Log::info('SMS sent successfully', [
    'phone' => $phoneNumber,
    'type' => $type,
    'message_length' => strlen($message)
]);

// Failed SMS
Log::error('SMS sending failed', [
    'phone' => $phoneNumber,
    'error' => $errorMessage
]);
```

## Error Handling

The service includes comprehensive error handling:

```php
$result = $smsService->sendSms($phone, $message);

if (!$result['success']) {
    // Handle error
    Log::error('SMS failed: ' . $result['message']);
    // Show user-friendly message
    return response()->json([
        'message' => 'Failed to send notification. Please try again.'
    ], 500);
}
```

## Testing

### Test SMS Configuration

```php
// Test endpoint
POST /api/sms/test
{
    "test_phone": "255700123456"
}
```

### Development Mode

```env
# In .env for development
SMS_FAKE_SEND=true
SMS_TEST_NUMBERS=255700123456,255600987654
```

## Security Considerations

1. **Rate Limiting**: Prevents abuse and API overload
2. **Phone Validation**: Ensures proper phone number format
3. **Queue Processing**: Prevents blocking of main application
4. **Error Logging**: Monitors for suspicious activity
5. **API Key Security**: Store credentials securely in environment variables

## Troubleshooting

### Common Issues

1. **SMS not sending**
   - Check if SMS_ENABLED=true
   - Verify API credentials
   - Check queue worker is running
   - Review error logs

2. **Phone number format errors**
   - Ensure phone numbers start with 255 for Tanzania
   - Check validation in frontend forms

3. **Rate limiting issues**
   - Adjust SMS_RATE_LIMIT_PER_HOUR
   - Check cache configuration

4. **Queue not processing**
   - Ensure queue worker is running: `php artisan queue:work`
   - Check queue configuration in .env

### Debug Commands

```bash
# Check SMS configuration
php artisan config:show sms

# View failed queue jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all

# Check SMS logs
php artisan tinker
>>> App\Models\SmsLog::latest()->take(10)->get()
```

## Performance Optimization

1. **Queue Processing**: Use dedicated queue worker for SMS
2. **Bulk Operations**: Use bulk endpoints for multiple recipients
3. **Rate Limiting**: Configure appropriate limits for your provider
4. **Database Indexing**: SMS logs table is properly indexed
5. **Caching**: Rate limiting uses Laravel's cache system

## Contributing

When modifying the SMS service:

1. Update tests for new functionality
2. Maintain backward compatibility
3. Update documentation
4. Test with actual SMS provider
5. Consider rate limiting implications

## Support

For issues related to:
- **SMS Provider**: Contact your SMS service provider
- **Application Integration**: Check Laravel logs and queue status
- **Phone Number Issues**: Verify formatting and validation
- **Performance**: Monitor queue processing and database performance

---

**Created by**: MNH IT Development Team  
**Last Updated**: October 2025  
**Version**: 1.0.0