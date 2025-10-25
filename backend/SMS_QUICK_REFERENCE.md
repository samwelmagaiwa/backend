# SMS Integration - Quick Reference Card

## 🚀 Quick Start (3 Steps)

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
$smsService = app(\App\Services\SmsService::class);
$result = $smsService->sendSms('255700000000', 'Test', 'test');
exit
```

### 3. Add to Each Approval Controller:
```php
use App\Events\RequestApproved;

// After saving approval:
event(new RequestApproved($request, auth()->user(), 'hod', $nextApprover));
```

## 📝 Code Snippets

### HOD Approval:
```php
use App\Events\RequestApproved;
use App\Models\User;

$divisionalDirector = User::whereHas('roles', fn($q) => 
    $q->where('name', 'divisional_director')
)->first();

event(new RequestApproved(
    $combinedRequest, 
    auth()->user(), 
    'hod', 
    $divisionalDirector
));
```

### Divisional Approval:
```php
$ictDirector = User::whereHas('roles', fn($q) => 
    $q->where('name', 'ict_director')
)->first();

event(new RequestApproved(
    $combinedRequest, 
    auth()->user(), 
    'divisional', 
    $ictDirector
));
```

### ICT Director Approval:
```php
$headOfIt = User::whereHas('roles', fn($q) => 
    $q->where('name', 'head_of_it')
)->first();

event(new RequestApproved(
    $combinedRequest, 
    auth()->user(), 
    'ict_director', 
    $headOfIt
));
```

### Head of IT Approval (Final):
```php
event(new RequestApproved(
    $combinedRequest, 
    auth()->user(), 
    'head_it', 
    null  // No next approver
));
```

## 🔍 Quick Debug

### Check if SMS enabled:
```bash
php artisan tinker
config('sms.enabled')
config('sms.test_mode')
```

### Check recent SMS:
```bash
php artisan tinker
\App\Models\SmsLog::latest()->take(3)->get(['phone_number','status','created_at']);
```

### Watch logs live:
```bash
tail -f storage/logs/laravel.log | grep SMS
```

## ⚙️ API Endpoints

- **Test Config**: `POST /api/v1/sms/test-configuration`
- **View Stats**: `GET /api/v1/sms/statistics`
- **View Logs**: `GET /api/v1/sms/logs`
- **Send Single**: `POST /api/v1/sms/send`

## 🎯 What Gets Sent?

### To Requester:
```
Dear [Name], your [Type] request has been APPROVED 
by [Level]. Reference: [REF]. You will be notified 
on next steps. - MNH IT
```

### To Next Approver:
```
PENDING APPROVAL: [Type] request from [Name] 
([Dept]) requires your review. Ref: [REF]. 
Please check the system. - MNH IT
```

## ✅ Production Checklist

- [ ] Set `SMS_TEST_MODE=false`
- [ ] Test with 3 real numbers
- [ ] Start queue worker: `php artisan queue:work`
- [ ] Monitor logs for 1 hour
- [ ] Set up supervisor for queue worker

## 🆘 Quick Fixes

**SMS not sending?**
```bash
# Check config
php artisan config:clear
php artisan cache:clear

# Check queue
php artisan queue:failed
php artisan queue:retry all
```

**Phone format issues?**
- Accepts: `0712345678`, `712345678`, `255712345678`
- Auto-formats to: `255712345678`

## 📞 Endpoints

- **Test**: `https://messaging-service.co.tz/api/sms/v1/test/text/single`
- **Prod**: `https://messaging-service.co.tz/api/sms/v1/text/single`

## 🎓 More Info

- Full Guide: `SMS_INTEGRATION_GUIDE.md`
- Summary: `SMS_UPDATE_SUMMARY.md`

---
**Quick Reference v1.0** | Ready to use ✅
