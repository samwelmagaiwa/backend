# Z-Index Fix for Departments Dropdown in User Role Assignment

## Problem
The departments dropdown in the "Create User Modal" was hidden behind the "Additional Roles" card due to improper z-index stacking.

## Issue Location
File: `/frontend/src/components/admin/UserRoleAssignment.vue`
Route: `/admin/user-roles`

## Root Cause
1. Department dropdown used `z-50` but was in the same stacking context as other elements
2. The "Additional Roles Section" was rendering after the dropdown in DOM order, causing it to appear on top
3. No proper z-index hierarchy established between related sections

## Solutions Applied

### 1. Fixed Positioning Strategy (Primary Fix)
Changed dropdown positioning from `absolute` to `fixed` to escape modal overflow context:

**Template Changes:**
- **Line 525:** Added `ref="departmentTrigger"` to dropdown trigger
- **Line 526:** Changed click handler to `@click="toggleDepartmentDropdown"`
- **Line 537-539:** Changed dropdown positioning:
```html
<!-- Before -->
class="absolute z-50 w-full mt-1 bg-blue-900..."

<!-- After -->
class="fixed z-[9999] bg-blue-900..."
ref="departmentDropdown"
:style="departmentDropdownPosition"
```

### 2. Dynamic Position Calculation
**Added reactive positioning system:**
- **Line 858-860:** Added refs: `departmentTrigger`, `departmentDropdown`, `dropdownPosition`
- **Line 953-962:** Added computed property `departmentDropdownPosition`
- **Line 1133-1146:** Added `toggleDepartmentDropdown()` method with position calculation

### 3. Click Outside Handler
**Added proper UX behavior:**
- **Line 1334-1343:** Added `handleClickOutside()` handler
- **Line 1347-1350:** Added event listener in `onMounted`
- **Line 1352-1355:** Added cleanup in `onUnmounted`
- **Line 816:** Imported `onUnmounted` from Vue

### 4. Z-Index Hierarchy (Secondary)
- **Department Container:** `z-10` (stacking context)
- **Department Dropdown:** `z-[9999]` (fixed positioning, highest priority)
- **Additional Roles Section:** `z-0` (lower priority)

## Z-Index Hierarchy Established
- **Department Container:** `z-10` (stacking context)
- **Department Dropdown:** `z-[60]` (highest priority for visibility)
- **Additional Roles Section:** `z-0` (lower priority)
- **Modal Background:** `z-50` (medium priority)
- **Toast Notifications:** `z-50` (medium priority)

## Expected Result
The department dropdown will now:
1. **Escape modal overflow context** using fixed positioning
2. **Position dynamically** relative to its trigger button
3. **Appear above all content** with z-index 9999
4. **Close when clicking outside** for better UX
5. **Maintain proper width** matching the trigger button

## Technical Implementation
- **Fixed positioning:** Escapes parent container overflow constraints
- **Dynamic calculation:** Uses `getBoundingClientRect()` for precise positioning
- **Reactive positioning:** Updates position when dropdown opens
- **Event delegation:** Proper click-outside behavior with cleanup

## Testing
1. Navigate to `/admin/user-roles`
2. Click "Create User" button
3. Open the Department dropdown in the modal
4. Verify dropdown appears above ALL content (including Additional Roles)
5. Test click outside to close
6. Test dropdown positioning on different screen sizes

## Files Modified
- `/frontend/src/components/admin/UserRoleAssignment.vue` (15+ changes across template and script)
