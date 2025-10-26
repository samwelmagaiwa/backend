-- SQL Script to Diagnose and Fix ICT Officer Issue
-- Run these queries in order to identify and resolve the problem

-- =====================================================
-- STEP 1: Check if Samwel Magaiwa exists in users table
-- =====================================================
SELECT 
    id, 
    name, 
    email, 
    pf_number, 
    phone, 
    is_active,
    department_id
FROM users 
WHERE id = 40;

-- Expected result:
-- id: 40
-- name: samwel magaiwa
-- phone: +255617919104
-- is_active: 1 (true)


-- =====================================================
-- STEP 2: Check all roles in the system
-- =====================================================
SELECT id, name, description 
FROM roles 
ORDER BY name;

-- Look for 'ict_officer' role
-- If it doesn't exist, you need to create it first


-- =====================================================
-- STEP 3: Check if Samwel has any roles assigned
-- =====================================================
SELECT 
    ru.user_id,
    u.name as user_name,
    r.id as role_id,
    r.name as role_name,
    ru.assigned_at
FROM role_user ru
JOIN users u ON ru.user_id = u.id
JOIN roles r ON ru.role_id = r.id
WHERE ru.user_id = 40;

-- If this returns no rows, Samwel has NO roles assigned


-- =====================================================
-- STEP 4: Check all users with ict_officer role
-- =====================================================
SELECT 
    u.id,
    u.name,
    u.email,
    u.pf_number,
    u.phone,
    u.is_active,
    r.name as role_name
FROM users u
JOIN role_user ru ON u.id = ru.user_id
JOIN roles r ON ru.role_id = r.id
WHERE r.name = 'ict_officer'
AND u.is_active = 1;

-- This shows all active ICT Officers in the system


-- =====================================================
-- FIX: Assign ict_officer role to Samwel Magaiwa
-- =====================================================

-- First, get the role_id for 'ict_officer'
SET @role_id = (SELECT id FROM roles WHERE name = 'ict_officer' LIMIT 1);

-- Check if we found the role
SELECT @role_id as ict_officer_role_id;

-- If @role_id is NULL, you need to create the role first:
-- INSERT INTO roles (name, description, created_at, updated_at) 
-- VALUES ('ict_officer', 'ICT Officer', NOW(), NOW());
-- Then run the SET @role_id query again


-- Assign the role to Samwel Magaiwa (ID: 40)
-- Only insert if not already assigned
INSERT INTO role_user (user_id, role_id, assigned_at, created_at, updated_at)
SELECT 40, @role_id, NOW(), NOW(), NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM role_user 
    WHERE user_id = 40 AND role_id = @role_id
)
AND @role_id IS NOT NULL;

-- Verify the assignment
SELECT 
    u.id,
    u.name,
    r.name as role_name,
    ru.assigned_at
FROM users u
JOIN role_user ru ON u.id = ru.user_id
JOIN roles r ON ru.role_id = r.id
WHERE u.id = 40;


-- =====================================================
-- STEP 5: Verify the fix
-- =====================================================

-- This query should now return Samwel Magaiwa
SELECT 
    u.id,
    u.name,
    u.pf_number,
    u.phone,
    u.email,
    u.is_active,
    r.name as role_name
FROM users u
JOIN role_user ru ON u.id = ru.user_id
JOIN roles r ON ru.role_id = r.id
WHERE r.name = 'ict_officer'
AND u.is_active = 1
AND u.id = 40;

-- Expected result: 1 row showing Samwel with ict_officer role


-- =====================================================
-- ALTERNATIVE: Check if using old role system
-- =====================================================

-- Some older systems might use a 'role' column in users table
-- Check if this column exists:
DESCRIBE users;

-- If there's a 'role' column, check its value:
SELECT id, name, role, phone 
FROM users 
WHERE id = 40;

-- If role column exists and is used, you might need to update it:
-- UPDATE users SET role = 'ict_officer' WHERE id = 40;


-- =====================================================
-- TROUBLESHOOTING: Check Laravel logs
-- =====================================================

-- If the above fixes don't work, check Laravel logs:
-- Location: C:\xampp\htdocs\lara-API-vue\backend\storage\logs\laravel.log
-- Look for errors related to:
-- - "Getting ICT officers list"
-- - Role relationship errors
-- - Database connection issues


-- =====================================================
-- TEST THE FIX
-- =====================================================

-- After running the fix, test the API endpoint:
-- GET http://127.0.0.1:8000/api/head-of-it/ict-officers

-- Expected JSON response should include Samwel:
-- {
--   "success": true,
--   "message": "ICT officers retrieved successfully",
--   "data": [
--     {
--       "id": 40,
--       "name": "samwel magaiwa",
--       "pf_number": "PF1290",
--       "phone_number": "+255617919104",
--       "email": "sammy@mnh.go.tz",
--       "department": "ICT Department",
--       "position": "ICT Officer",
--       "status": "Available",
--       "active_assignments": 0,
--       "is_active": true
--     }
--   ]
-- }
