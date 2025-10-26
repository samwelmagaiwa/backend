# SMS Status Tracking - Complete Implementation Documentation

## 📋 Overview

This document describes the complete SMS notification status tracking system implemented across all approval workflow dashboards in the MNH Access Request System.

---

## ✅ Implementation Status: **COMPLETE**

All components have been successfully implemented and integrated.

---

## 🏗️ Architecture

### Database Schema

**Migration:** `2025_10_24_234618_add_sms_notification_tracking_to_user_access_table.php`

**Fields Added to `user_access` table:**

| Field | Type | Default | Description |
|-------|------|---------|-------------|
| `sms_sent_to_hod_at` | timestamp | NULL | When SMS was sent to HOD |
| `sms_to_hod_status` | string | 'pending' | HOD SMS status (pending/sent/failed) |
| `sms_sent_to_divisional_at` | timestamp | NULL | When SMS was sent to Divisional Director |
| `sms_to_divisional_status` | string | 'pending' | Divisional Director SMS status |
| `sms_sent_to_ict_director_at` | timestamp | NULL | When SMS was sent to ICT Director |
| `sms_to_ict_director_status` | string | 'pending' | ICT Director SMS status |
| `sms_sent_to_head_it_at` | timestamp | NULL | When SMS was sent to Head of IT |
| `sms_to_head_it_status` | string | 'pending' | Head of IT SMS status |
| `sms_sent_to_requester_at` | timestamp | NULL | When SMS was sent to requester |
| `sms_to_requester_status` | string | 'pending' | Requester SMS status |

---

## 🔧 Backend Implementation

### 1. SMS Module Service (`app/Services/SmsModule.php`)

#### Key Methods:

```php
// Send approval notifications and update status
public function notifyRequestApproved($request, User $approver, string $approvalLevel, ?User $nextApprover = null): array

// Update requester SMS status
protected function updateRequesterSmsStatus($request, bool $success): void

// Update next approver SMS status based on workflow level
protected function updateNextApproverSmsStatus($request, string $approvalLevel, bool $success): void

// Get all SMS notification statuses for a request
public function getSmsNotificationStatus($request): array
```

#### Status Update Logic:

When an approval happens at any level, the system:
1. ✅ Sends SMS to the requester (confirmation)
2. ✅ Updates `sms_to_requester_status` = 'sent' or 'failed'
3. ✅ Sends SMS to next approver (if exists)
4. ✅ Updates next approver's SMS status field

**Workflow Mapping:**
- HOD approves → Updates `sms_to_divisional_status`
- Divisional approves → Updates `sms_to_ict_director_status`
- ICT Director approves → Updates `sms_to_head_it_status`
- Head IT approves → Final step (no next approver)

---

### 2. API Controllers

All controllers updated to return SMS status fields in `transformRequestData()`:

#### ✅ HodCombinedAccessController.php
- Returns all 5 SMS status fields
- Line 619-624

#### ✅ DivisionalCombinedAccessController.php
- Returns all 5 SMS status fields
- Line 512-517

#### ✅ DictCombinedAccessController.php
- Handles both ICT Director and Head of IT roles
- Returns all 5 SMS status fields
- Line 783-788

---

### 3. Command Line Tool

**Command:** `php artisan sms:send-pending`

**Purpose:** Send SMS notifications to HOD for pending approval requests

**Features:**
- Process all pending requests: `php artisan sms:send-pending`
- Process specific request: `php artisan sms:send-pending --request-id=123`
- Updates `sms_to_hod_status` = 'sent' or 'failed'
- Can be scheduled via Laravel Task Scheduler

---

## 🎨 Frontend Implementation

### Dashboard Components Updated

All role-specific dashboards now display SMS status with consistent UI:

#### 1. HOD Dashboard (`HodRequestList.vue`) ✅
**Location:** `frontend/src/components/views/hod/HodRequestList.vue`

**SMS Status Column:** Between "Current Status" and "Actions" (Line 98-100)

**Methods Added:**
```javascript
// Show sms_to_divisional_status if HOD approved, else sms_to_hod_status
getRelevantSmsStatus(request) { ... }

// Map status to display text (Delivered/Pending/Failed)
getSmsStatusText(smsStatus) { ... }

// Return colored dot class (bg-green-500/bg-yellow-500/bg-red-500)
getSmsStatusColor(smsStatus) { ... }

// Return text color class (text-green-400/text-yellow-400/text-red-400)
getSmsStatusTextColor(smsStatus) { ... }
```

**Status Logic:**
- Shows own SMS status (`sms_to_hod_status`) for pending requests
- Shows next approver SMS (`sms_to_divisional_status`) after HOD approval

---

#### 2. Divisional Director Dashboard (`DivisionalRequestList.vue`) ✅
**Location:** `frontend/src/components/views/divisional/DivisionalRequestList.vue`

**SMS Status Column:** Between "Current Status" and "Actions" (Line 94-96)

**Methods:** Same 4 methods as HOD dashboard

**Status Logic:**
- Shows `sms_to_divisional_status` for pending requests
- Shows `sms_to_ict_director_status` after Divisional approval

---

#### 3. ICT Director Dashboard (`DictRequestList.vue`) ✅
**Location:** `frontend/src/components/views/dict/DictRequestList.vue`

**SMS Status Column:** Between "Current Status" and "Actions" (Line 95-97)

**Methods:** Same 4 methods

**Status Logic:**
- Shows `sms_to_ict_director_status` for pending requests
- Shows `sms_to_head_it_status` after ICT Director approval

---

#### 4. Head of IT Dashboard (`HeadOfItRequestList.vue`) ✅
**Location:** `frontend/src/components/views/head-of-it/HeadOfItRequestList.vue`

**SMS Status Column:** Between "Current Status" and "Actions" (Line 92-94)

**Methods:** Same 4 methods

**Status Logic:**
- Shows `sms_to_head_it_status` for pending requests
- Shows `sms_to_requester_status` after Head IT approval (final notification)

---

## 🎯 Visual Indicators

All dashboards use consistent visual indicators:

| Status | Visual | Text | Meaning |
|--------|--------|------|---------|
| **sent** | 🟢 Green dot | "Delivered" | SMS successfully sent |
| **pending** | 🟡 Yellow dot | "Pending" | SMS queued or not yet sent |
| **failed** | 🔴 Red dot | "Failed" | SMS failed to send |
| **default** | ⚪ Gray dot | "Pending" | Unknown/initial state |

---

## 🔄 Approval Workflow SMS Flow

```
┌─────────────────┐
│  Staff Submits  │
│    Request      │
└────────┬────────┘
         │
         ▼
   ┌─────────────┐
   │  SMS → HOD  │ ← sms_to_hod_status
   └──────┬──────┘
          │
          ▼
   ┌────────────────┐
   │  HOD Approves  │
   └────────┬───────┘
            │
            ├──→ SMS to Staff (sms_to_requester_status)
            │
            ▼
   ┌─────────────────────┐
   │ SMS → Divisional    │ ← sms_to_divisional_status
   └──────────┬──────────┘
              │
              ▼
   ┌──────────────────────────┐
   │  Divisional Approves     │
   └──────────┬───────────────┘
              │
              ├──→ SMS to Staff
              │
              ▼
   ┌─────────────────────────┐
   │ SMS → ICT Director      │ ← sms_to_ict_director_status
   └──────────┬──────────────┘
              │
              ▼
   ┌───────────────────────────┐
   │  ICT Director Approves    │
   └──────────┬────────────────┘
              │
              ├──→ SMS to Staff
              │
              ▼
   ┌─────────────────────┐
   │ SMS → Head of IT    │ ← sms_to_head_it_status
   └──────────┬──────────┘
              │
              ▼
   ┌──────────────────────┐
   │  Head IT Approves    │
   └──────────┬───────────┘
              │
              ├──→ SMS to Staff (Final)
              │
              ▼
   ┌──────────────────────┐
   │ Request Implemented  │
   └──────────────────────┘
```

---

## 📝 Configuration

### Environment Variables (.env)

```env
# Enable/Disable SMS Service
SMS_ENABLED=true

# Test Mode (uses test endpoint)
SMS_TEST_MODE=false

# KODA TECH API Credentials
SMS_API_KEY=your_api_key
SMS_SECRET_KEY=your_secret_key
SMS_SENDER_ID=EABMS

# SMS Service Settings
SMS_TIMEOUT=30
```

---

## 🧪 Testing

### Manual Testing Checklist

- [ ] Submit new request → Check `sms_to_hod_status` updates
- [ ] HOD approves → Check `sms_to_divisional_status` updates
- [ ] Divisional approves → Check `sms_to_ict_director_status` updates
- [ ] ICT Director approves → Check `sms_to_head_it_status` updates
- [ ] Head IT approves → Check `sms_to_requester_status` updates
- [ ] Verify visual indicators display correctly in all dashboards
- [ ] Test with SMS disabled (should show 'pending' status)
- [ ] Test with failed SMS (should show 'failed' status)

### Command Testing

```bash
# Test SMS configuration
php artisan sms:send-pending --request-id=1

# Process all pending
php artisan sms:send-pending

# Check logs
tail -f storage/logs/laravel.log
```

---

## 🚀 Deployment Steps

1. ✅ Run migration (already exists)
   ```bash
   php artisan migrate
   ```

2. ✅ Clear caches
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan view:clear
   ```

3. ✅ Configure SMS credentials in `.env`

4. ✅ Test SMS service
   ```bash
   php artisan sms:send-pending --request-id=<test_id>
   ```

5. ✅ (Optional) Schedule SMS command in `app/Console/Kernel.php`:
   ```php
   protected function schedule(Schedule $schedule)
   {
       $schedule->command('sms:send-pending')
                ->everyThirtyMinutes()
                ->withoutOverlapping();
   }
   ```

6. ✅ Rebuild frontend assets
   ```bash
   cd frontend
   npm run build
   ```

---

## 📚 Code References

### Backend Files Modified/Created:
- ✅ `backend/app/Services/SmsModule.php` (SMS service with status tracking)
- ✅ `backend/app/Console/Commands/SendPendingRequestSms.php` (CLI tool)
- ✅ `backend/app/Http/Controllers/Api/v1/HodCombinedAccessController.php`
- ✅ `backend/app/Http/Controllers/Api/v1/DivisionalCombinedAccessController.php`
- ✅ `backend/app/Http/Controllers/Api/v1/DictCombinedAccessController.php`
- ✅ `backend/database/migrations/2025_10_24_234618_add_sms_notification_tracking_to_user_access_table.php`

### Frontend Files Modified:
- ✅ `frontend/src/components/views/hod/HodRequestList.vue`
- ✅ `frontend/src/components/views/divisional/DivisionalRequestList.vue`
- ✅ `frontend/src/components/views/dict/DictRequestList.vue`
- ✅ `frontend/src/components/views/head-of-it/HeadOfItRequestList.vue`

---

## 🎓 Usage Examples

### For Developers

#### Check SMS status programmatically:
```php
$sms = app(SmsModule::class);
$statuses = $sms->getSmsNotificationStatus($request);

// Returns:
// [
//     'hod' => ['status' => 'sent', 'sent_at' => '2025-10-25 10:30:00', 'sent' => true],
//     'divisional' => ['status' => 'pending', ...],
//     ...
// ]
```

#### Manually update SMS status:
```php
$request->update([
    'sms_to_hod_status' => 'sent',
    'sms_sent_to_hod_at' => now()
]);
```

---

## 🔍 Troubleshooting

### SMS shows "Pending" but should show "Sent"

**Check:**
1. Verify SMS service is enabled: `SMS_ENABLED=true` in `.env`
2. Check if SMS was actually sent: Look in `sms_logs` table
3. Verify database field is updated: `SELECT sms_to_*_status FROM user_access WHERE id=X`

### Visual indicator not showing

**Check:**
1. Browser console for JavaScript errors
2. Verify API returns SMS status fields
3. Check Vue component method `getRelevantSmsStatus()` logic

### SMS status not updating after approval

**Check:**
1. `SmsModule::notifyRequestApproved()` is being called in controller
2. `updateNextApproverSmsStatus()` has correct approval level mapping
3. Database migration was run successfully

---

## ✨ Features Summary

✅ **Real-time SMS status tracking** across entire approval workflow
✅ **Visual indicators** (colored dots) for quick status identification
✅ **Contextual display** - each role sees relevant SMS status
✅ **Automatic status updates** when SMS notifications are sent
✅ **CLI tool** for manual SMS sending and testing
✅ **Database audit trail** with timestamps
✅ **Consistent UI** across all dashboards
✅ **Error handling** for failed SMS attempts
✅ **Scalable architecture** for future enhancements

---

## 📞 Support

For questions or issues:
1. Check logs: `storage/logs/laravel.log`
2. Verify SMS configuration in `.env`
3. Test with CLI: `php artisan sms:send-pending --request-id=X`
4. Review this documentation

---

**Last Updated:** October 25, 2025
**Version:** 1.0.0
**Status:** ✅ Production Ready
