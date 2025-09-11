# Device Issuing and Approval Flow

## Overview
The system now supports flexible workflow where ICT officers can either:
1. **Issue device first, then approve** (recommended flow)
2. **Approve first, then issue device** (legacy flow)

## Updated Flow

### Step 1: Save Issuing Assessment
**Endpoint:** `POST /api/ict-approval/device-requests/{requestId}/assessment/issuing`

**Purpose:** Issue the device to the borrower and save assessment data

**What happens:**
- Validates device condition assessment data
- Borrows device from inventory (if `device_inventory_id` exists)
- Sets booking status to `in_use`
- Sets `device_issued_at` timestamp
- Creates assessment record in `device_assessments` table
- Updates `device_condition_issuing` field with JSON assessment data
- Returns success message: "Device issued successfully with assessment saved. Device is now in use."

**Requirements:**
- Request must not be rejected (`ict_approve !== 'rejected'`)
- Device must not have been issued already (no existing `device_condition_issuing` or `device_issued_at`)
- ICT officer permissions required

**Request Body:**
```json
{
    "physical_condition": "excellent|good|fair|poor",
    "functionality": "fully_functional|partially_functional|not_functional",
    "accessories_complete": true|false,
    "visible_damage": true|false,
    "damage_description": "string (optional)",
    "notes": "string (optional)"
}
```

### Step 2: Approve Request
**Endpoint:** `POST /api/ict-approval/device-requests/{requestId}/approve`

**Purpose:** Officially approve the borrowing request

**What happens:**
- Sets `ict_approve` to 'approved'
- Sets `ict_approved_by` and `ict_approved_at`
- **Smart inventory handling:** Only borrows device if not already issued
- Logs whether device was already issued or newly borrowed
- Updates ICT notes if provided

**Requirements:**
- Request must have `ict_approve` status of 'pending'
- ICT approval permissions required

**Request Body:**
```json
{
    "ict_notes": "string (optional)"
}
```

## Key Technical Changes

### 1. ICTApprovalController::saveIssuingAssessment()
- **Removed** requirement for `ict_approve === 'approved'`
- **Added** inventory borrowing logic during issuing
- **Added** status transition to `in_use`
- **Added** duplicate issuing prevention

### 2. ICTApprovalController::approveDeviceBorrowingRequest()
- **Added** smart detection of already-issued devices
- **Added** conditional inventory borrowing (only if not already issued)
- **Added** logging for both scenarios

### 3. Status Flow
```
Request Created (status: 'pending', ict_approve: 'pending')
     ↓
Issue Device (status: 'in_use', device_issued_at: timestamp)
     ↓
Approve Request (ict_approve: 'approved', ict_approved_at: timestamp)
     ↓
Return Device (status: 'returned', device_received_at: timestamp)
```

## Alternative Flows Supported

### Legacy Flow (Approve then Issue)
1. `POST /api/ict-approval/device-requests/{requestId}/approve`
2. `POST /api/ict-approval/device-requests/{requestId}/assessment/issuing`

### Recommended Flow (Issue then Approve)
1. `POST /api/ict-approval/device-requests/{requestId}/assessment/issuing`
2. `POST /api/ict-approval/device-requests/{requestId}/approve`

## Error Handling

### Issuing Assessment Errors
- **400:** Device already issued
- **400:** Request is rejected
- **400:** Device no longer available in inventory
- **403:** Unauthorized access
- **404:** Request not found
- **422:** Validation failed

### Approval Errors
- **400:** Request not in pending status
- **400:** Device not available (if trying to borrow during approval)
- **403:** Unauthorized access
- **404:** Request not found
- **422:** Validation failed

## Database Changes
- No schema changes required
- Existing fields are utilized more intelligently
- Better status consistency across workflow variations

## Benefits
1. **Flexibility:** Support both workflow preferences
2. **Inventory Safety:** Prevents double-borrowing
3. **Status Consistency:** Proper status transitions
4. **Audit Trail:** Better logging of device issuance vs approval
5. **Error Prevention:** Duplicate issuing protection
