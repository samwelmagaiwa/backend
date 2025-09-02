# HOD Dashboard Blank Page Fix

## Problem Summary
The HOD dashboard request list (`/hod-dashboard/request-list`) was showing a blank page instead of displaying the access requests.

## Root Cause Analysis
The issue was caused by a missing function in the `useAuth` composable. The `InternalAccessList.vue` component was trying to import `getRoleDisplayName` from `useAuth`, but this function was not defined there.

**Error in component:**
```javascript
const { userRole, ROLES, getRoleDisplayName } = useAuth()
```

**Missing function:** `getRoleDisplayName` was not exported from `useAuth` composable.

## Solution Applied
1. **Added `getRoleDisplayName` function to `useAuth` composable** - This function provides display names for user roles
2. **Fixed the controller corruption issue** - Previously fixed the `BothServiceFormController.php` corruption
3. **Added debug route** - Created `/hod-dashboard/debug` for troubleshooting

## Files Modified

### 1. `frontend/src/composables/useAuth.js`
**Added:**
```javascript
/**
 * Get display name for a role
 * @param {string} role - Role to get display name for
 * @returns {string} - Display name for the role
 */
const getRoleDisplayName = (role) => {
  if (!role) return 'User'

  const roleNames = {
    [ROLES.ADMIN]: 'Administrator',
    [ROLES.DIVISIONAL_DIRECTOR]: 'Divisional Director',
    [ROLES.HEAD_OF_DEPARTMENT]: 'Head of Department',
    [ROLES.ICT_DIRECTOR]: 'ICT Director',
    [ROLES.STAFF]: 'D. IN MEDICINE',
    [ROLES.ICT_OFFICER]: 'ICT Officer'
  }
  return roleNames[role] || role
}
```

**Updated return statement to include:**
```javascript
getRoleDisplayName,
```

### 2. `frontend/src/router/index.js`
**Added debug route:**
```javascript
{
  path: '/hod-dashboard/debug',
  name: 'HODDashboardDebug',
  component: () => import('../components/debug/HODDashboardDebug.vue'),
  meta: {
    requiresAuth: true,
    roles: [
      ROLES.HEAD_OF_DEPARTMENT,
      ROLES.DIVISIONAL_DIRECTOR,
      ROLES.ICT_DIRECTOR,
      ROLES.ICT_OFFICER
    ]
  }
}
```

### 3. `frontend/src/components/debug/HODDashboardDebug.vue`
**Created debug component** for troubleshooting authentication and API issues.

## Expected Result
The HOD dashboard request list should now:
1. ✅ Load without JavaScript errors
2. ✅ Display the correct role name in the header
3. ✅ Show user access requests from the API
4. ✅ Allow HOD and Divisional Director users to view and process requests

## Testing Steps
1. **Login as HOD or Divisional Director**
2. **Navigate to `/hod-dashboard/request-list`**
3. **Verify the page loads correctly**
4. **Check that requests are displayed**
5. **Verify role display name appears correctly**

## Debug Route
If issues persist, use the debug route:
- **URL:** `/hod-dashboard/debug`
- **Purpose:** Shows authentication state, API test results, and component state
- **Access:** Available to HOD, Divisional Director, ICT Director, and ICT Officer roles

## Prevention
- Ensure all functions used in components are properly exported from composables
- Use TypeScript or JSDoc for better function documentation
- Add unit tests for composables to catch missing exports
- Regular code reviews to catch import/export mismatches

## Related Issues Fixed
1. **Controller Corruption** - `BothServiceFormController.php` was corrupted and restored
2. **Missing Function Export** - `getRoleDisplayName` was not exported from `useAuth`
3. **API Endpoint** - Backend endpoint `/both-service-form/hod/user-access-requests` is working

The blank page issue should now be resolved, and the HOD dashboard should display access requests correctly.