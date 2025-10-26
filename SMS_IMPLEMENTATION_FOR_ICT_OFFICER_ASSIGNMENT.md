# SMS Notification Implementation for ICT Officer Task Assignment

## Overview
This document describes the SMS notification implementation for the Head of IT workflow when assigning tasks to ICT Officers.

## Flow Description

### Current Workflow
1. **Request Approval by Head of IT** - NO SMS sent at this stage
2. **Task Assignment to ICT Officer** - SMS IS sent at this stage ✅
3. **Head of IT Views SMS Status** - Can see if SMS was delivered, pending, or failed

### Key Points
- **SMS is NOT sent when Head of IT approves a request** (only status changes)
- **SMS IS sent when Head of IT assigns the task to an ICT Officer**
- The SMS notification informs the ICT Officer about the task assignment
- Head of IT can view the SMS delivery status (sent/pending/failed) in the request list

## Implementation Details

### 1. Backend Changes

#### A. Created SMS Channel (`app/Channels/SmsChannel.php`)
- Custom notification channel for sending SMS
- Integrates with existing `SmsService`
- Handles error logging and status tracking
- Auto-detects phone numbers from user objects

#### B. Updated Notification (`app/Notifications/IctTaskAssignedNotification.php`)
**Changes:**
- Added SMS channel to delivery channels (`via()` method)
- Created `toSms()` method for SMS message formatting
- SMS message includes: Staff name, PF number, request type

**SMS Message Format:**
```
New ICT Task Assignment: You have been assigned to grant access for {Staff Name} (PF: {PF Number}). Request Type: {Request Types}. Please log in to start implementation.
```

#### C. Database Migration (`database/migrations/2025_10_25_220000_add_sms_to_ict_officer_status_to_user_access_table.php`)
**New Fields Added to `user_access` table:**
- `sms_sent_to_ict_officer_at` (timestamp) - When SMS was sent
- `sms_to_ict_officer_status` (string) - Status: 'pending', 'sent', or 'failed'

#### D. Controller Updates (`app/Http/Controllers/Api/v1/HeadOfItController.php`)
**In `assignTaskToIctOfficer()` method:**
- Tracks SMS status when sending notification
- Updates `sms_to_ict_officer_status` and `sms_sent_to_ict_officer_at` fields
- Sets status to 'sent' on success, 'failed' on error
- Logs notification attempts for debugging

**In `getAllRequests()` method:**
- Returns SMS status fields in API response
- Frontend can now display SMS delivery status

### 2. Frontend Changes

#### Updated Component (`frontend/src/components/views/head-of-it/HeadOfItRequestList.vue`)

**In `getRelevantSmsStatus()` method:**
- Shows ICT Officer SMS status when task is assigned
- Shows 'pending' before assignment
- Shows requester SMS status after implementation complete
- Properly handles all workflow stages

**SMS Status Display:**
- **Pending (Yellow)**: SMS not yet sent or queued
- **Sent/Delivered (Green)**: SMS successfully sent
- **Failed (Red)**: SMS failed to send

## How It Works

### Step-by-Step Process

1. **Head of IT Approves Request**
   - Status changes to `head_of_it_approved`
   - NO SMS sent at this point
   - SMS status shows "Pending"

2. **Head of IT Assigns Task to ICT Officer**
   - Clicks "Assign Task" → Selects ICT Officer
   - System creates task assignment record
   - **SMS notification is sent to ICT Officer**
   - SMS status updates:
     - If successful: `sms_to_ict_officer_status = 'sent'`
     - If failed: `sms_to_ict_officer_status = 'failed'`
   - Timestamp recorded in `sms_sent_to_ict_officer_at`

3. **Head of IT Views Request List**
   - Can see SMS status for each assigned task
   - Status indicator shows:
     - 🟡 Pending - Not yet assigned/SMS not sent
     - 🟢 Delivered - SMS successfully sent
     - 🔴 Failed - SMS failed to send

## Database Schema

### user_access Table (New Columns)
```sql
sms_sent_to_ict_officer_at  TIMESTAMP NULL
sms_to_ict_officer_status   VARCHAR(255) DEFAULT 'pending'
```

## API Changes

### GET /api/head-of-it/all-requests
**New Response Fields:**
```json
{
  "id": 123,
  "request_id": "MLG-REQ000123",
  ...
  "sms_to_ict_officer_status": "sent",
  "sms_sent_to_ict_officer_at": "2025-10-25T22:30:00"
}
```

## Testing Checklist

### Backend Testing
- [ ] Run migration: `php artisan migrate`
- [ ] Verify SMS channel is registered in Laravel
- [ ] Test task assignment API endpoint
- [ ] Check SMS logs for delivery status
- [ ] Verify database fields are updated correctly

### Frontend Testing
- [ ] Approve a request (verify NO SMS sent)
- [ ] Assign task to ICT Officer (verify SMS IS sent)
- [ ] Check SMS status indicator shows correct status
- [ ] Verify status changes from pending → sent/failed
- [ ] Test with multiple requests

### Integration Testing
- [ ] Verify ICT Officer receives SMS
- [ ] Check SMS message content is correct
- [ ] Test failed SMS scenario (invalid phone)
- [ ] Verify retry mechanism works
- [ ] Check logs for any errors

## Configuration

### Required Environment Variables
```env
SMS_API_URL=your_sms_gateway_url
SMS_API_KEY=your_api_key
SMS_SECRET_KEY=your_secret_key
SMS_SENDER_ID=your_sender_id
SMS_TEST_MODE=false
```

## Troubleshooting

### Common Issues

1. **SMS Not Sending**
   - Check SMS service configuration
   - Verify ICT Officer has valid phone number
   - Check Laravel logs: `storage/logs/laravel.log`
   - Verify SMS channel is properly registered

2. **Status Shows 'pending' Forever**
   - Check notification queue is running: `php artisan queue:work`
   - Verify database fields are being updated
   - Check for exceptions in logs

3. **SMS Sent but Status Shows 'failed'**
   - Check exception handling in controller
   - Verify SMS service response format
   - Review SMS gateway API logs

## Files Modified/Created

### Backend
- ✅ Created: `app/Channels/SmsChannel.php`
- ✅ Modified: `app/Notifications/IctTaskAssignedNotification.php`
- ✅ Created: `database/migrations/2025_10_25_220000_add_sms_to_ict_officer_status_to_user_access_table.php`
- ✅ Modified: `app/Http/Controllers/Api/v1/HeadOfItController.php`

### Frontend
- ✅ Modified: `frontend/src/components/views/head-of-it/HeadOfItRequestList.vue`

### Documentation
- ✅ Created: `SMS_IMPLEMENTATION_FOR_ICT_OFFICER_ASSIGNMENT.md`

## Notes

- SMS is sent asynchronously via Laravel queue (implements `ShouldQueue`)
- SMS delivery is best-effort (network issues may cause delays)
- Failed SMS attempts are logged for manual review
- SMS status is informational only - does not block workflow
- The existing SMS service (`SmsService.php`) handles actual SMS sending
- No changes were made to the approval process - only task assignment sends SMS

## Future Enhancements

- Add SMS retry mechanism for failed messages
- Implement SMS template management
- Add SMS delivery reports/webhooks
- Create admin dashboard for SMS monitoring
- Add bulk SMS resend functionality

---

**Implementation Date:** October 25, 2025
**Implemented By:** AI Assistant
**Version:** 1.0
