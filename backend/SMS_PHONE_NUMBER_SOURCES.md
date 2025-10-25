# SMS Phone Number Sources

## 📱 How Phone Numbers Are Retrieved

The SMS system automatically retrieves phone numbers from the correct database tables.

---

## 🎯 For Requester (Staff who submitted the request)

### Lookup Order:

```
1. request.phone             (from user_access table)
   ↓ (if empty)
2. request.phone_number      (from user_access table)
   ↓ (if empty)
3. request.user.phone        (from users table via relationship) ⭐
   ↓ (if empty)
4. No SMS sent (logged as warning)
```

### Database Query:

```php
// The system does this automatically:
$phone = $request->user->phone;  // Gets from users table
```

**SQL Equivalent:**
```sql
SELECT u.phone 
FROM users u
INNER JOIN user_access ua ON ua.user_id = u.id
WHERE ua.id = :request_id;
```

### Example Log Output:

```
[2024-01-15 10:30:45] SMS phone number resolved
Request ID: 123
Phone Source: users.phone (via relationship)
Phone Masked: 255712***
```

---

## 🎯 For Next Approver

### Direct Lookup:

```php
// Next approver is a User model instance
$phone = $nextApprover->phone;  // Directly from users table
```

### How It Works:

```php
// In your controller:
$nextApprover = User::whereHas('roles', fn($q) => 
    $q->where('name', 'divisional_director')
)->first();

// This User object has phone from users table
$sms->notifyRequestApproved($request, $approver, 'hod', $nextApprover);
```

**SQL Equivalent:**
```sql
SELECT u.phone, u.name, u.id
FROM users u
INNER JOIN role_user ru ON ru.user_id = u.id
INNER JOIN roles r ON r.id = ru.role_id
WHERE r.name = 'divisional_director'
LIMIT 1;
```

### Example Log Output:

```
[2024-01-15 10:30:46] Next approver phone number found
Approver ID: 45
Approver Name: Dr. John Smith
Phone Source: users.phone
Phone Masked: 255715***
```

---

## 📊 Database Schema

### users table (primary source):
```sql
CREATE TABLE users (
    id BIGINT PRIMARY KEY,
    name VARCHAR(255),
    email VARCHAR(255),
    phone VARCHAR(20),        ← Phone numbers stored here
    department_id BIGINT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### user_access table (requests):
```sql
CREATE TABLE user_access (
    id BIGINT PRIMARY KEY,
    user_id BIGINT,           ← FK to users table
    phone VARCHAR(20),        ← Optional, usually NULL
    phone_number VARCHAR(20), ← Optional, usually NULL
    staff_name VARCHAR(255),
    department_id BIGINT,
    ...
);
```

### Relationship:
```php
// In UserAccess model
public function user()
{
    return $this->belongsTo(User::class);
}

// This allows: $request->user->phone
```

---

## ✅ Validation

### Before SMS is sent, the system:

1. ✅ Retrieves phone number from appropriate source
2. ✅ Removes all non-numeric characters
3. ✅ Automatically adds +255 if missing
4. ✅ Validates format: `255[67]XXXXXXXX`
5. ✅ Checks rate limiting
6. ✅ Sends SMS

### If phone number is missing:

```
⚠️ Warning logged (does NOT fail approval)
SMS not sent
Approval continues successfully
```

---

## 🔍 How to Check Phone Numbers

### Check user's phone number:

```sql
SELECT id, name, email, phone 
FROM users 
WHERE id = :user_id;
```

### Check if all users have phone numbers:

```sql
SELECT 
    COUNT(*) as total_users,
    COUNT(phone) as users_with_phone,
    COUNT(*) - COUNT(phone) as users_without_phone
FROM users;
```

### Find users without phone numbers:

```sql
SELECT id, name, email, phone
FROM users
WHERE phone IS NULL OR phone = ''
ORDER BY name;
```

---

## 🛠️ Troubleshooting

### "No phone number for requester" Warning

**Cause:** User doesn't have phone in users table

**Fix:**
```sql
UPDATE users 
SET phone = '255712345678' 
WHERE id = :user_id;
```

### "No phone number for next approver" Warning

**Cause:** Approver doesn't have phone in users table

**Fix:**
```sql
-- Find the approver
SELECT u.id, u.name, u.phone
FROM users u
INNER JOIN role_user ru ON ru.user_id = u.id
INNER JOIN roles r ON r.id = ru.role_id
WHERE r.name = 'divisional_director';

-- Update their phone
UPDATE users 
SET phone = '255715123456' 
WHERE id = :approver_id;
```

---

## 📋 Data Flow Diagram

### Complete SMS Flow:

```
Request Submitted
    ↓
[user_access table]
  id: 123
  user_id: 10
    ↓
HOD Approves
    ↓
SMS Module → Get Requester Phone
    ↓
[users table] ← Via user_id relationship
  id: 10
  phone: '0712345678'
    ↓
Format → '255712345678'
    ↓
Send SMS to Requester ✅
    ↓
Get Next Approver
    ↓
[users table] ← Direct query by role
  id: 45
  phone: '0715123456'
    ↓
Format → '255715123456'
    ↓
Send SMS to Next Approver ✅
```

---

## 🎯 Summary

| Recipient | Source | Table | Access Method |
|-----------|--------|-------|---------------|
| **Requester** | `request->user->phone` | `users` | Eloquent relationship |
| **Next Approver** | `$nextApprover->phone` | `users` | Direct User model |

**Both phone numbers come from the `users` table!** ✅

The system:
- ✅ Uses Eloquent relationships for requester
- ✅ Uses User model directly for approvers
- ✅ Both pull from `users.phone` column
- ✅ Automatically formats phone numbers
- ✅ Logs source of each phone number
- ✅ Handles missing phone numbers gracefully

---

**Simple. Reliable. Works.** 📱✨
