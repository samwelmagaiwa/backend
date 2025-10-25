# SMS System Update Summary

## ✅ Completed Updates

### 1. **SmsService.php** - Updated with cURL Implementation
**Location**: `backend/app/Services/SmsService.php`

**Changes**:
- Replaced Laravel HTTP client with direct cURL implementation
- Proper Basic Auth header: `Authorization: Basic base64(apiKey:secretKey)`
- Correct payload structure for KODA TECH API:
  ```json
  {
    "from": "KODA TECH",
    "to": "255712345678",
    "text": "message content",
    "reference": "ref_unique_id"
  }
  ```
- Enhanced error handling and logging
- Response validation for `messages` array

### 2. **sms.php Config** - Dynamic Endpoint Selection
**Location**: `backend/config/sms.php`

**Changes**:
- Automatic endpoint switching based on `SMS_TEST_MODE`:
  - Test: `https://messaging-service.co.tz/api/sms/v1/test/text/single`
  - Production: `https://messaging-service.co.tz/api/sms/v1/text/single`
- Default credentials added (can be overridden in `.env`)

### 3. **Event System** - Automatic SMS Notifications
**New Files Created**:
- `backend/app/Events/RequestApproved.php` - Event triggered on approval
- `backend/app/Listeners/SendApprovalSmsNotification.php` - Handles SMS sending
- Updated `backend/app/Providers/EventServiceProvider.php` - Registered event/listener

**Features**:
- ✅ Auto-sends SMS to requester when their request is approved
- ✅ Auto-notifies next approver in the chain
- ✅ Queued for background processing (non-blocking)
- ✅ Comprehensive error handling and logging
- ✅ Supports all approval levels: HOD → Divisional → ICT Director → Head of IT

### 4. **Documentation**
**Created**:
- `SMS_INTEGRATION_GUIDE.md` - Complete integration guide
- `SMS_UPDATE_SUMMARY.md` - This file

## 🎯 How It Works

### Approval Flow with SMS:

```
1. Staff submits request
   ↓
2. HOD approves
   ├→ SMS sent to staff: "Your request was approved by HOD"
   └→ SMS sent to Divisional Director: "New request needs your approval"
   ↓
3. Divisional approves
   ├→ SMS sent to staff: "Your request was approved by Divisional Director"
   └→ SMS sent to ICT Director: "New request needs your approval"
   ↓
4. ICT Director approves
   ├→ SMS sent to staff: "Your request was approved by ICT Director"
   └→ SMS sent to Head of IT: "New request needs your approval"
   ↓
5. Head of IT approves (Final)
   └→ SMS sent to staff: "Your request was approved by Head of IT"
```

## 📋 Integration Steps

### Step 1: Update .env File
```env
SMS_ENABLED=true
SMS_TEST_MODE=true
SMS_API_KEY=beneth
SMS_SECRET_KEY=Beneth@1701
SMS_SENDER_ID=KODA TECH
SMS_QUEUE_ENABLED=true
```

### Step 2: Test SMS Service
```bash
php artisan tinker

# Test:
$smsService = app(\App\Services\SmsService::class);
$result = $smsService->sendSms('255700000000', 'Test from MNH', 'test');
print_r($result);
exit
```

### Step 3: Add Event to Controllers

**Example for HOD Approval**:
```php
use App\Events\RequestApproved;
use App\Models\User;

// After successful approval:
$combinedRequest->save();

// Get next approver
$divisionalDirector = User::whereHas('roles', function($q) {
    $q->where('name', 'divisional_director');
})->where('department_id', $combinedRequest->department_id)->first();

// Trigger SMS
event(new RequestApproved(
    $combinedRequest,
    auth()->user(),
    'hod',
    $divisionalDirector
));
```

**Repeat for**:
- Divisional approval → next: ICT Director
- ICT Director approval → next: Head of IT  
- Head of IT approval → next: null (final)

### Step 4: Start Queue Worker
```bash
# Development
php artisan queue:work --tries=3

# Production (use supervisor)
php artisan queue:work --daemon --tries=3 --timeout=90
```

## 🔍 Monitoring & Debugging

### Check Recent SMS Logs:
```bash
php artisan tinker
\App\Models\SmsLog::latest()->take(5)->get(['phone_number', 'status', 'message', 'created_at']);
```

### View Laravel Logs:
```bash
tail -f storage/logs/laravel.log | grep SMS
```

### API Endpoints (Already exist):
- `GET /api/v1/sms/statistics` - View SMS stats
- `GET /api/v1/sms/logs` - View SMS logs
- `POST /api/v1/sms/test-configuration` - Test SMS config

## ⚙️ Configuration Options

### Rate Limiting
Default: 5 SMS per hour per number

Modify in `config/sms.php`:
```php
'rate_limit' => [
    'max_per_hour_per_number' => 10, // Increase if needed
],
```

### Queue Settings
```php
'queue' => [
    'enabled' => true,
    'queue_name' => 'sms',
    'max_tries' => 3,
],
```

### Phone Number Formatting
Automatic formatting for Tanzania:
- `0712345678` → `255712345678`
- `712345678` → `255712345678`
- `255712345678` → `255712345678` (no change)

## 🚀 Going to Production

### Checklist:
1. ✅ Update `.env`: `SMS_TEST_MODE=false`
2. ✅ Verify credentials with KODA TECH
3. ✅ Test with 3-5 real phone numbers first
4. ✅ Set up supervisor for queue workers
5. ✅ Configure log rotation for SMS logs
6. ✅ Set up monitoring/alerts for failed SMS

### Supervisor Config Example:
```ini
[program:mnh-sms-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/storage/logs/worker.log
```

## 📊 Expected Behavior

### Test Mode (`SMS_TEST_MODE=true`):
- Uses test endpoint: `.../test/text/single`
- SMS may not actually be delivered
- Logs show "test_mode": true
- Good for development

### Production Mode (`SMS_TEST_MODE=false`):
- Uses production endpoint: `.../text/single`
- Real SMS are sent and charged
- Recipients receive actual messages

## 🆘 Troubleshooting

### Issue: SMS not being sent
**Solutions**:
1. Check `SMS_ENABLED=true` in `.env`
2. Verify API credentials
3. Check Laravel logs: `tail -f storage/logs/laravel.log`
4. Test endpoint directly with cURL
5. Verify phone numbers have correct format

### Issue: Queue not processing
**Solutions**:
1. Check queue worker is running: `ps aux | grep queue:work`
2. Check failed jobs: `php artisan queue:failed`
3. Retry failed: `php artisan queue:retry all`
4. Check database queue table: `SELECT * FROM jobs;`

### Issue: Rate limit errors
**Solutions**:
1. Increase limit in `config/sms.php`
2. Clear rate limit cache: `php artisan cache:clear`
3. Check rate limit key: `php artisan tinker` then `Cache::get('sms_rate_limit:255712345678')`

## 📞 Support Contacts

**KODA TECH SMS API**:
- Endpoint: https://messaging-service.co.tz
- API Docs: Contact KODA TECH for documentation

**MNH IT Team**:
- Check system logs
- Monitor SMS statistics dashboard
- Review failed SMS in logs table

## 🎉 Benefits

1. **Instant Notifications**: Approvers notified immediately when action needed
2. **Reduced Delays**: No waiting for email checks
3. **Better Tracking**: All SMS logged in database
4. **Automated**: No manual SMS sending required
5. **Reliable**: Queue system ensures delivery even during high load
6. **Scalable**: Handles bulk notifications efficiently

## Next Steps

1. ✅ Integrate event firing into all approval controllers
2. ✅ Test in development with SMS_TEST_MODE=true
3. ✅ Get test phone numbers from each approval level
4. ✅ Conduct UAT (User Acceptance Testing)
5. ✅ Switch to production mode when ready
6. ✅ Monitor for first week after deployment

---

**Last Updated**: {{ now }}
**Version**: 1.0
**Status**: Ready for Integration
