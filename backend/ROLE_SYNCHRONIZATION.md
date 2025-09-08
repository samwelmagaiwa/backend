# Role Synchronization System

## Overview

The role synchronization system automatically updates the `role_id` field in the `users` table when roles are assigned or removed from users. This ensures that the user's primary role always reflects their highest priority assigned role.

## Features

### 1. Role Hierarchy System

Roles are prioritized based on their importance in the organization:

```php
$hierarchy = [
    'admin' => 100,                    // Highest priority
    'dict' => 90,
    'divisional_director' => 80,
    'head_of_department' => 70,
    'ict_officer' => 60,
    'hod_it' => 55,
    'staff' => 10,                     // Lowest priority
];
```

### 2. Automatic Role Assignment

When users are assigned to department positions:

- **Head of Department**: Automatically gets `head_of_department` role
- **Divisional Director**: Automatically gets `divisional_director` role

### 3. Automatic Role Removal

When users are removed from department positions:

- Roles are only removed if the user is not assigned to the same position in other departments
- The `role_id` is updated to reflect the next highest priority role

## Usage

### Manual Synchronization

To sync all users' `role_id` fields with their current role assignments:

```bash
# Dry run to see what would be changed
php artisan users:sync-role-ids --dry-run

# Apply the changes
php artisan users:sync-role-ids
```

### Programmatic Usage

```php
// Sync a single user's primary role
$user->syncPrimaryRole();

// Get a user's primary role
$primaryRole = $user->getPrimaryRole();

// Get primary role name
$roleName = $user->getPrimaryRoleName();

// Check role hierarchy
$role1->hasHigherPriorityThan($role2);
```

## Implementation Details

### User Model Methods

- `getPrimaryRole()`: Returns the highest priority role from assigned roles
- `updatePrimaryRoleId()`: Updates the role_id field based on primary role
- `syncPrimaryRole()`: Reloads roles and updates role_id

### Role Model Methods

- `getPriority()`: Returns the priority level of the role
- `getRolePriority($roleName)`: Static method to get priority by role name
- `hasHigherPriorityThan($otherRole)`: Compare role priorities
- `getHighestPriorityRole($roles)`: Get highest priority role from collection

### Automatic Triggers

The system automatically syncs `role_id` when:

1. Roles are assigned to users via `UserRoleController`
2. Roles are removed from users via `UserRoleController`
3. Users are assigned as HOD or Divisional Director in departments
4. Users are removed from HOD or Divisional Director positions
5. New users are created with roles

## Database Schema

### Indexes

The system includes optimized database indexes:

```sql
-- Single column index for role lookups
CREATE INDEX users_role_id_index ON users (role_id);

-- Composite index for common queries
CREATE INDEX users_role_id_is_active_index ON users (role_id, is_active);
```

## Testing

Run the role synchronization tests:

```bash
php artisan test --filter=RoleSynchronizationTest
```

## Logging

All role changes are logged with context:

- Role assignments/removals
- Primary role updates
- Department position assignments
- Context information (who made the change, when, why)

## Migration Guide

For existing systems, run the synchronization command after deployment:

```bash
# Check what would be updated
php artisan users:sync-role-ids --dry-run

# Apply the synchronization
php artisan users:sync-role-ids
```

## Configuration

### Adding New Roles

To add a new role to the hierarchy:

1. Update the `getRolePriority()` method in `Role.php`
2. Assign an appropriate priority level
3. Run the sync command to update existing users

### Changing Role Priorities

1. Update the hierarchy array in `Role::getRolePriority()`
2. Run `php artisan users:sync-role-ids` to update all users

## Error Handling

The system includes comprehensive error handling:

- Database transaction rollbacks on failures
- Detailed logging of all operations
- Graceful handling of missing roles or users
- Validation of role assignments

## Performance Considerations

- Database indexes optimize role-based queries
- Bulk operations use transactions
- Lazy loading of relationships where appropriate
- Efficient role hierarchy lookups

## Security

- All role changes require authentication
- Admin middleware protects role assignment endpoints
- Comprehensive audit trail via `RoleChangeLog`
- Prevention of self-removal of admin roles