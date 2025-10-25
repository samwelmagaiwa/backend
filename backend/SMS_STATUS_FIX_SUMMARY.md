# SMS Status Display Fix - Summary

## Problem
SMS messages were being sent successfully to HOD phone numbers and received by users, but the status in the `/request-status` page was still showing "Pending" instead of "Delivered/Sent".

## Root Causes Found

### 1. Missing Database Columns
The `booking_service` table didn't have SMS notification tracking columns.

**Fix:** Created migration `2025_10_25_051000_add_sms_notification_tracking_to_booking_service_table.php` to add:
- `sms_notifications` (json)
- `sms_sent_to_hod_at` (timestamp)
- `sms_to_hod_status` (string: pending, sent, failed)
- Similar fields for other approvers

### 2. Missing API Response Field
The `RequestStatusController.php` wasn't returning the `sms_to_hod_status` field in the API response.

**Fix:** Updated both `transformAccessRequest()` and `transformBookingRequest()` methods to include:
```php
'sms_to_hod_status' => $accessRequest->sms_to_hod_status ?? 'pending',
```

### 3. Model $fillable Issue (Critical)
The SMS notification fields were not in the `$fillable` array of the models, which prevented them from being saved even when explicitly set.

**Fix:** Added SMS notification fields to `$fillable` in:
- `app/Models/UserAccess.php`
- `app/Models/BookingService.php`

Fields added:
```php
'sms_notifications',
'sms_sent_to_hod_at',
'sms_to_hod_status',
'sms_sent_to_divisional_at',
'sms_to_divisional_status',
'sms_sent_to_ict_director_at',
'sms_to_ict_director_status',
'sms_sent_to_head_it_at',
'sms_to_head_it_status',
'sms_sent_to_requester_at',
'sms_to_requester_status',
```

## Files Modified

1. **Backend Controller:**
   - `backend/app/Http/Controllers/Api/v1/RequestStatusController.php`
     - Line 326: Added `sms_to_hod_status` to access request transformation
     - Line 375: Added `sms_to_hod_status` to booking request transformation

2. **Backend Models:**
   - `backend/app/Models/UserAccess.php`
     - Lines 79-89: Added SMS notification fields to $fillable
   
   - `backend/app/Models/BookingService.php`
     - Lines 57-67: Added SMS notification fields to $fillable

3. **Database Migration:**
   - `backend/database/migrations/2025_10_25_051000_add_sms_notification_tracking_to_booking_service_table.php`
     - New migration to add SMS tracking columns to booking_service table

## Manual Fix for Request 52
Since request 52 was created before the fix, its status was manually updated:
```bash
php backend/fix-sms-status-req52.php
```

This updated the status from "pending" to "sent" for request 52.

## Expected Behavior After Fix

### For New Requests:
1. When a request is submitted and SMS is sent to HOD
2. The `sms_to_hod_status` field is updated to 'sent' in the database
3. The API returns this status to the frontend
4. The frontend displays "Delivered" in the SMS Status column

### For Existing Requests:
- Requests created before this fix will show "Pending" unless manually updated
- Future SMS notifications will be tracked correctly

## Testing
After these fixes:
1. Submit a new access request or booking request
2. Check the request status page
3. SMS Status column should show "Delivered" immediately after submission
4. Database should have `sms_to_hod_status = 'sent'` and `sms_sent_to_hod_at` timestamp

## Frontend Display Mapping
The frontend (RequestStatusPage.vue) maps SMS status as:
- `sent` → "Delivered" (green)
- `pending` → "Pending" (yellow)
- `failed` → "Failed" (red)
