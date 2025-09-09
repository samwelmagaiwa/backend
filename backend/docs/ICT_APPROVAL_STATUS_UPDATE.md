# ICT Approval Status Update

## Overview
This update ensures that when an ICT officer approves or rejects a device booking request, the status is properly reflected in the `/request-status` endpoint that regular users see.

## Problem
Previously, when an ICT officer approved/rejected a device booking request:
- The `ict_approve` field was updated correctly ('approved' or 'rejected')
- But the main `status` field remained 'pending'
- This caused users to see their requests as still "pending" even after ICT decisions

## Solution
Updated the ICT approval and rejection methods to:
1. Update both the ICT-specific status (`ict_approve`) AND the main status (`status`)
2. Set appropriate approval metadata (`approved_by`, `approved_at`)
3. Update the RequestStatusController to prioritize ICT approval status for user display

## Changes Made

### Backend Updates

#### 1. BookingServiceController.php
- **ictApprove()**: Now updates main `status` to 'approved' when ICT approves
- **ictReject()**: Now updates main `status` to 'rejected' when ICT rejects
- Both methods now set `approved_by` and `approved_at` fields
- Updated success messages and logging

#### 2. RequestStatusController.php
- Added `getBookingStatusForUser()` method to determine user-visible status
- Updated `transformBookingRequest()` to use ICT approval status
- Updated `transformBookingRequestForDetails()` to use ICT approval status
- Updated statistics calculation to reflect ICT approval status

### Status Flow

#### Before ICT Action:
```
status: 'pending'
ict_approve: 'pending'
```

#### After ICT Approval:
```
status: 'approved'           // ✅ Now updated
ict_approve: 'approved'      // ✅ Updated as before
approved_by: [ict_officer_id]
approved_at: [timestamp]
ict_approved_by: [ict_officer_id]
ict_approved_at: [timestamp]
```

#### After ICT Rejection:
```
status: 'rejected'           // ✅ Now updated
ict_approve: 'rejected'      // ✅ Updated as before
approved_by: [ict_officer_id]
approved_at: [timestamp]
ict_approved_by: [ict_officer_id]
ict_approved_at: [timestamp]
```

## API Endpoints Affected

### 1. ICT Approval Endpoints
- `POST /api/booking-service/bookings/{id}/ict-approve`
- `POST /api/booking-service/bookings/{id}/ict-reject`

**New Behavior**: Updates both ICT status and main booking status

### 2. User Status Endpoints
- `GET /api/request-status/`
- `GET /api/request-status/details`
- `GET /api/request-status/statistics`

**New Behavior**: Shows ICT approval status as the primary status for booking requests

## User Experience Impact

### Before
1. User submits device booking request → Status: "Pending"
2. ICT officer approves request → Status: Still shows "Pending" ❌
3. User confused about request status

### After
1. User submits device booking request → Status: "Pending"
2. ICT officer approves request → Status: Shows "Approved" ✅
3. User sees clear, accurate status

## Testing
Created comprehensive tests in `tests/Feature/ICTApprovalStatusTest.php` to verify:
- ICT approval updates main status correctly
- ICT rejection updates main status correctly
- Request status endpoint reflects ICT decisions
- Statistics are calculated based on user-visible status

## Backward Compatibility
- All existing API responses remain the same structure
- No breaking changes to frontend components
- ICT dashboard functionality unchanged
- Admin approval workflow unchanged

## Database Changes
No database migrations required. Uses existing columns:
- `status` (main status field)
- `ict_approve` (ICT-specific status)
- `approved_by` (who approved the request)
- `approved_at` (when approved)
- `ict_approved_by` (ICT officer who decided)
- `ict_approved_at` (when ICT decided)

## Security
- Maintains all existing role-based access controls
- Only ICT officers can approve/reject ICT requests
- Users can only see their own request status
- No privilege escalation risks

## Performance
- No additional database queries
- Minimal computational overhead
- Status calculation happens in-memory for collections
- Maintains efficient pagination and filtering
