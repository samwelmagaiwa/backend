# SMS Testing Checklist for ICT Officer Task Assignment

## Prerequisites

### 1. Database Migration
```bash
cd C:\xampp\htdocs\lara-API-vue\backend
php artisan migrate
```

**Expected Output:**
```
Migrating: 2025_10_25_220000_add_sms_to_ict_officer_status_to_user_access_table
Migrated:  2025_10_25_220000_add_sms_to_ict_officer_status_to_user_access_table (XX.XXms)
```

### 2. Verify Database Fields
```sql
DESCRIBE user_access;
```

**Should see:**
- `sms_sent_to_ict_officer_at` (timestamp, nullable)
- `sms_to_ict_officer_status` (varchar, default 'pending')

### 3. Queue Worker
Laravel notifications implement `ShouldQueue`, so you need the queue worker running:

```bash
cd C:\xampp\htdocs\lara-API-vue\backend
php artisan queue:work
```

**Keep this terminal open during testing!**

## Test Case: Samwel Magaiwa

**ICT Officer Details:**
- ID: 40
- Name: samwel magaiwa
- PF Number: PF1290
- Phone: +255617919104
- Email: sammy@mnh.go.tz

### Step 1: Verify User Data
```sql
SELECT id, name, pf_number, phone, email 
FROM users 
WHERE id = 40;
```

**Expected Result:**
- Phone should be: `+255617919104` or `255617919104`

### Step 2: Approve a Request
1. Login as Head of IT
2. Navigate to: `/head_of_it-dashboard/combined-requests`
3. Find a pending request
4. Click "Approve" button
5. Upload signature and approve

**Expected Behavior:**
- ❌ NO SMS should be sent at this stage
- ✅ Status changes to "Approved by You"
- ✅ SMS Status shows: "Pending" (yellow dot)

**Log Check:**
```bash
tail -f C:\xampp\htdocs\lara-API-vue\backend\storage\logs\laravel.log
```

Should NOT see: "SMS Channel: Attempting to send SMS"

### Step 3: Assign Task to Samwel Magaiwa
1. Click on the approved request
2. Click "Assign Task" button
3. Search for "samwel" or "PF1290"
4. Select Samwel Magaiwa
5. Confirm assignment

**Expected Behavior:**
- ✅ SMS should be sent at this stage
- ✅ Status changes to "Assigned to ICT Officer"
- ✅ SMS Status changes from "Pending" to "Delivered" (green dot)
- ✅ Success modal appears

### Step 4: Check Logs for SMS Sending

**In the Laravel log, you should see:**

```
Preparing to send notification to ICT Officer
{
  "request_id": XX,
  "ict_officer_id": 40,
  "ict_officer_name": "samwel magaiwa",
  "ict_officer_phone": "+255617919104",
  "ict_officer_email": "sammy@mnh.go.tz",
  "has_phone": true
}

ICT task notification queued successfully
{
  "request_id": XX,
  "ict_officer_id": 40,
  "ict_officer_phone": "+255617919104",
  "sms_status": "sent",
  "notification_channels": ["database", "mail", "sms"]
}

SMS Channel: Attempting to send SMS
{
  "notifiable_id": 40,
  "notifiable_type": "App\\Models\\User",
  "notifiable_name": "samwel magaiwa",
  "phone": "+255617919104",
  "has_phone_field": true,
  "has_phone_number_field": false
}

SMS Channel: SMS sent successfully
{
  "phone": "255617919104",
  "notification_type": "App\\Notifications\\IctTaskAssignedNotification",
  "message_id": "XXXX"
}
```

### Step 5: Verify Database Update
```sql
SELECT 
  id, 
  request_id,
  sms_to_ict_officer_status,
  sms_sent_to_ict_officer_at
FROM user_access 
WHERE id = [REQUEST_ID];
```

**Expected Result:**
- `sms_to_ict_officer_status` = 'sent'
- `sms_sent_to_ict_officer_at` = current timestamp

### Step 6: Verify Samwel Receives SMS

**SMS Message Format:**
```
New ICT Task Assignment: You have been assigned to grant access for [Staff Name] (PF: [PF Number]). Request Type: [Types]. Please log in to start implementation.
```

**Example:**
```
New ICT Task Assignment: You have been assigned to grant access for John Doe (PF: PF1234). Request Type: Jeeva, Wellsoft. Please log in to start implementation.
```

## Troubleshooting

### Issue 1: SMS Status Stays "Pending"

**Possible Causes:**
1. Queue worker not running
2. Phone number missing/invalid
3. SMS service configuration error

**Solutions:**
```bash
# Check queue worker
ps aux | grep "queue:work"

# Start queue worker if not running
php artisan queue:work

# Check failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all
```

### Issue 2: "No phone number found" in logs

**Check:**
```sql
SELECT id, name, phone FROM users WHERE id = 40;
```

**If phone is NULL:**
```sql
UPDATE users 
SET phone = '+255617919104' 
WHERE id = 40;
```

### Issue 3: SMS Shows "Failed"

**Check Laravel logs:**
```bash
tail -50 C:\xampp\htdocs\lara-API-vue\backend\storage\logs\laravel.log
```

**Look for:**
- "SMS Channel: Failed to send SMS"
- Error message details

**Common issues:**
- SMS API credentials not configured
- Invalid phone number format
- SMS service down/unreachable

### Issue 4: Notification Error in Controller

**Check for:**
```
Failed to send ICT task notification
{
  "error": "..."
}
```

**This means the notification class has an error**

### Issue 5: SMS Service Configuration

**Check `.env` file:**
```env
SMS_API_URL=your_sms_gateway_url
SMS_API_KEY=your_api_key
SMS_SECRET_KEY=your_secret_key
SMS_SENDER_ID=your_sender_id
SMS_TEST_MODE=false
```

**If missing, add them and restart:**
```bash
php artisan config:cache
php artisan queue:restart
```

## Manual SMS Test

To test SMS service directly:

```php
// In tinker
php artisan tinker

$smsService = app(\App\Services\SmsService::class);
$result = $smsService->sendSms('+255617919104', 'Test message from system', 'test');

dd($result);
```

**Expected Output:**
```php
[
  "success" => true,
  "message" => "SMS sent successfully",
  "data" => [
    "message_id" => "XXXX"
  ]
]
```

## Verification Checklist

- [ ] Migration ran successfully
- [ ] Database fields exist (`sms_to_ict_officer_status`, `sms_sent_to_ict_officer_at`)
- [ ] Queue worker is running
- [ ] Samwel's phone number exists in database: `+255617919104`
- [ ] SMS service configured in `.env`
- [ ] Test approval (NO SMS sent) ✅
- [ ] Test assignment (SMS sent) ✅
- [ ] Log shows "SMS Channel: Attempting to send SMS"
- [ ] Log shows "SMS Channel: SMS sent successfully"
- [ ] Database updated with `sms_to_ict_officer_status = 'sent'`
- [ ] SMS status shows "Delivered" (green) in frontend
- [ ] Samwel receives SMS on phone: `+255617919104`

## Expected Timeline

1. **Head of IT approves** → Status: Approved, SMS: Pending (0 seconds)
2. **Head of IT assigns task** → Notification queued (immediate)
3. **Queue worker processes** → SMS sent (0-5 seconds)
4. **SMS Gateway delivers** → Samwel receives SMS (5-30 seconds)
5. **Frontend updates** → SMS status shows "Delivered" (refresh or real-time)

## Success Criteria

✅ Samwel Magaiwa receives SMS on `+255617919104`
✅ SMS contains correct request details
✅ SMS status shows "Delivered" in Head of IT dashboard
✅ No SMS sent during approval step
✅ SMS only sent during task assignment step

## Contact for Issues

If SMS is not being delivered:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Check queue jobs: `php artisan queue:failed`
3. Verify SMS service is working: Run manual test in tinker
4. Contact SMS gateway provider if service is down

---

**Last Updated:** October 25, 2025
**For:** ICT Officer Task Assignment SMS Implementation
