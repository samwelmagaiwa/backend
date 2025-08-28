# Batch Migration Script for Remaining Components

## Remaining Components to Update

The following components still need to be migrated:

### Form Components
- `frontend/src/components/views/forms/JeevaAccessForm.vue`
- `frontend/src/components/views/forms/internetAccessForm.vue`
- `frontend/src/components/views/forms/wellSoftAccessForm.vue`
- `frontend/src/components/views/forms/UserCombinedAccessFormFixed.vue`
- `frontend/src/components/views/forms/both-service-form.vue`

### Request Components
- `frontend/src/components/views/requests/InternalAccessDetails.vue`

### ICT Approval Components
- `frontend/src/components/views/ict-approval/RequestsList.vue`
- `frontend/src/components/views/ict-approval/RequestDetails.vue`

### Admin Components
- `frontend/src/components/admin/JeevaUsers.vue`
- `frontend/src/components/admin/InternetUsers.vue`
- `frontend/src/components/admin/WellsoftUsers.vue`
- `frontend/src/components/admin/CleanRoleAssignment.vue`
- `frontend/src/components/admin/RoleAssignmentDemo.vue`
- `frontend/src/components/admin/DepartmentHodAssignment.vue`
- `frontend/src/components/admin/UserRoleAssignment_updated.vue`

### Other Components
- `frontend/src/components/UserProfile.vue`
- `frontend/src/components/SettingsPage.vue`

## Migration Steps for Each Component

For each component, perform these steps:

### 1. Update Template
**Find:**
```vue
<ModernSidebar v-model:collapsed="sidebarCollapsed" />
```

**Replace with:**
```vue
<ModernSidebar />
```

### 2. Update Script Section
**Find:**
```javascript
const sidebarCollapsed = ref(false)
```

**Replace with:**
```javascript
// Sidebar state now managed by Pinia - no local state needed
```

### 3. Update Return Statement
**Find:**
```javascript
return {
  sidebarCollapsed,
  // other properties
}
```

**Replace with:**
```javascript
return {
  // other properties (remove sidebarCollapsed)
}
```

## Verification

After migration, verify each component:

1. **Template Check**: No `v-model:collapsed` on ModernSidebar
2. **Script Check**: No local `sidebarCollapsed` state
3. **Return Check**: No `sidebarCollapsed` in return statement
4. **Functionality Check**: Sidebar still works and persists state

## Testing

After completing all migrations:

1. Visit `/test-sidebar-persistence` to test the implementation
2. Navigate between different components
3. Verify sidebar state persists across all routes
4. Test page refresh persistence
5. Test mobile responsiveness

## Benefits After Migration

✅ **Consistent State**: All components share the same sidebar state
✅ **Persistent State**: Sidebar state survives navigation and refresh
✅ **Cleaner Code**: No more prop drilling or local state management
✅ **Better Performance**: Centralized state management with Pinia
✅ **Mobile Support**: Automatic mobile detection and responsive behavior