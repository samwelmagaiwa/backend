# Unused Variables Fix Guide

Based on the issues mentioned, here are the fixes needed for unused imports and variables:

## 1. UserRoleAssignment.vue (Line 711) ✅ ALREADY FIXED
The unused import has already been fixed:
```javascript
// Current (correct)
import _roleAssignmentService from '@/services/roleAssignmentService'
```

## 2. Multiple Files - Unused ref variables
These files need to be checked for unused `const ref = useRouter()` patterns:

### Files to Check:
- `UserCombinedAccessForm.vue` (line 955)
- `UserCombinedAccessFormFixed.vue` (line 312)
- `both-service-form.vue` (line 2173)
- `RequestDetails.vue` (line 601)
- `RequestsList.vue` (line 538)

### Fix Pattern:
```javascript
// Before
const ref = useRouter()

// After
const _ref = useRouter()
```

**Note**: Based on my search, these files don't currently have this issue, suggesting they may have already been fixed or the line numbers have changed.

## 3. InternalAccessDetails.vue & InternalAccessList.vue ✅ ALREADY FIXED
Both files already have the required sidebarCollapsed definition:
```vue
<script setup>
import { ref } from 'vue'

// Already present in both files
const sidebarCollapsed = ref(false)
</script>
```

## 4. stores/index.js (Line 15) ✅ NO ISSUE FOUND
The current stores/index.js file doesn't have the unused state parameter issue mentioned. The file is properly structured without unused parameters.

## Manual Verification Steps

Since some of the reported issues may have already been resolved or the line numbers may have changed, here's how to manually verify and fix any remaining issues:

### Step 1: Search for Unused Router Variables
```bash
# Search for potential unused router variables
grep -r "const.*= useRouter()" frontend/src/components/
```

### Step 2: Check for Unused Imports
```bash
# Search for unused imports
grep -r "import.*Service.*from" frontend/src/components/ | grep -v "_"
```

### Step 3: Verify Sidebar Variables
```bash
# Check for missing sidebarCollapsed in components that use ModernSidebar
grep -l "ModernSidebar" frontend/src/components/**/*.vue | xargs grep -L "sidebarCollapsed"
```

## Quick Fix Script

If you find any remaining issues, here are the patterns to apply:

### For Unused Variables:
```javascript
// Pattern 1: Unused router
const router = useRouter()  // → const _router = useRouter()

// Pattern 2: Unused imports
import serviceX from '@/services/serviceX'  // → import _serviceX from '@/services/serviceX'

// Pattern 3: Unused state parameters
state: (state) => ({  // → state: () => ({ OR state: (_state) => ({
```

### For Missing Sidebar Variables:
```vue
<script setup>
import { ref } from 'vue'

// Add this if missing
const sidebarCollapsed = ref(false)
</script>
```

## Status Summary

- ✅ **UserRoleAssignment.vue**: Already fixed
- ✅ **InternalAccessDetails.vue**: Already has sidebarCollapsed
- ✅ **InternalAccessList.vue**: Already has sidebarCollapsed  
- ✅ **stores/index.js**: No unused parameters found
- ❓ **Other files**: May need manual verification as line numbers may have changed

## Recommendation

The issues mentioned appear to have been largely resolved already. If you're still seeing linting warnings, run your linter to get the current line numbers and specific issues, then apply the underscore prefix pattern to any remaining unused variables.