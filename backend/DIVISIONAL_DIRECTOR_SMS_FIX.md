# Divisional Director SMS Routing Fix

## Problem
The system was sending SMS notifications to the wrong divisional director because it was selecting the **first** user with the `divisional_director` role, rather than selecting the divisional director assigned to the **specific department** of the request.

### Issue Details
- Multiple users have the `divisional_director` role (4 users in the system)
- Each divisional director is assigned to specific department(s)
- The old code used: `User::whereHas('roles', fn($q) => $q->where('name', 'divisional_director'))->first()`
- This would always return the first divisional director in the database, regardless of the request's department

## Solution
Updated the SMS notification logic to **use the department relationship** to get the correct divisional director:

```php
// Get next approver (Divisional Director for this specific department)
// IMPORTANT: Divisional directors can oversee multiple departments,
// so we must get the director assigned to THIS request's department
$department = $userAccessRequest->department;
$nextApprover = $department && $department->divisional_director_id 
    ? User::find($department->divisional_director_id)
    : User::whereHas('roles', fn($q) => $q->where('name', 'divisional_director'))->first();
```

### How It Works
1. Get the department from the user access request
2. Check if the department has an assigned divisional director (`divisional_director_id`)
3. If yes, get that specific user
4. If no, fallback to getting the first user with the divisional_director role (for backwards compatibility)

## Files Modified

### 1. `app/Http/Controllers/Api/v1/HodCombinedAccessController.php`
- **Line 298-307**: Updated the `updateApproval()` method to use department-specific divisional director
- **Change**: Modified the logic that gets the next approver after HOD approval

### 2. `app/Console/Commands/SendPendingRequestSms.php`
- **Line 211-224**: Updated the `notifyNextLevel()` method to use department-specific divisional director
- **Change**: Added conditional logic to check if the next role is `divisional_director` and use the department relationship

## Database Structure
The fix leverages the existing database relationships:

- **departments** table has `divisional_director_id` column
- **user_access** table has `department_id` column
- **UserAccess** model has `department()` relationship
- **Department** model has `divisionalDirector()` relationship

## Current System State

### Divisional Directors Assignment
| Director Name | Email | Department(s) |
|--------------|-------|---------------|
| HR Director | hr.director@mnh.go.tz | Human Resources |
| John Mwanga | john.mwanga@mnh.go.tz | Laboratory Services |
| Pharmacy Technician | pharm.tech@mnh.go.tz | Pharmacy Department |
| David Selemani | david.selemani@mnh.go.tz | Finance Department |

### SMS Routing Examples
- **Pharmacy Department request** → SMS to `pharm.tech@mnh.go.tz` ✅
- **HR Department request** → SMS to `hr.director@mnh.go.tz` ✅
- **Lab Department request** → SMS to `john.mwanga@mnh.go.tz` ✅
- **Finance Department request** → SMS to `david.selemani@mnh.go.tz` ✅

## Testing
Comprehensive tests confirm:
- ✅ Each department routes to its assigned divisional director
- ✅ No cross-department notification errors
- ✅ Fallback logic works if no specific director assigned
- ✅ All 4 divisional directors have correct phone numbers

## Additional Notes
- **HOD assignment**: Already uses department-specific HOD (no fix needed)
- **ICT Director & Head of IT**: These are organization-wide roles, so getting the first user with these roles is correct
- **Divisional directors can oversee multiple departments**: The current system allows this through the `divisional_director_id` foreign key

## Verification Commands
```bash
# Check divisional director assignments
php check_divisional.php

# Verify SMS routing logic
php verify_divisional_fix.php

# Comprehensive routing test
php test_divisional_routing.php
```

## Impact
- ✅ SMS notifications now reach the correct approver
- ✅ Approval workflow is more efficient
- ✅ No duplicate or misdirected notifications
- ✅ Proper department-based authorization maintained

---
**Date Fixed**: 2025-10-25  
**Developer**: AI Assistant  
**Status**: ✅ DEPLOYED AND VERIFIED
