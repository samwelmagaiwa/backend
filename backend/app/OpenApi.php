<?php

namespace App;

use OpenApi\Annotations as OA;

/**
 * @OA\OpenApi(
 *   @OA\Info(
 *     title="MNH API",
 *     version="1.0.0",
 *     description="API documentation for authentication, user management, departments, roles, onboarding, declarations, booking services, and combined service forms."
 *   ),
 *   @OA\Server(
 *     url="http://localhost:8000",
 *     description="Local server"
 *   )
 * )
 */
class OpenApi {}

/**
 * Global security scheme: Bearer token (Laravel Sanctum plain text token used as Bearer)
 *
 * @OA\SecurityScheme(
 *   securityScheme="bearerAuth",
 *   type="http",
 *   scheme="bearer",
 *   bearerFormat="JWT"
 * )
 */
class SecuritySchemes {}

/**
 * Common models (schemas)
 */
class Schemas {}

/**
 * @OA\Schema(
 *   schema="Role",
 *   type="object",
 *   @OA\Property(property="id", type="integer"),
 *   @OA\Property(property="name", type="string", example="admin"),
 *   @OA\Property(property="description", type="string", nullable=true),
 *   @OA\Property(property="is_system_role", type="boolean", example=true)
 * )
 */
class RoleSchema {}

/**
 * @OA\Schema(
 *   schema="Department",
 *   type="object",
 *   @OA\Property(property="id", type="integer"),
 *   @OA\Property(property="name", type="string"),
 *   @OA\Property(property="code", type="string", nullable=true),
 *   @OA\Property(property="description", type="string", nullable=true),
 *   @OA\Property(property="is_active", type="boolean")
 * )
 */
class DepartmentSchema {}

/**
 * @OA\Schema(
 *   schema="User",
 *   type="object",
 *   @OA\Property(property="id", type="integer"),
 *   @OA\Property(property="name", type="string"),
 *   @OA\Property(property="email", type="string"),
 *   @OA\Property(property="phone", type="string", nullable=true),
 *   @OA\Property(property="pf_number", type="string", nullable=true),
 *   @OA\Property(property="staff_name", type="string", nullable=true),
 *   @OA\Property(property="department_id", type="integer", nullable=true),
 *   @OA\Property(property="is_active", type="boolean"),
 *   @OA\Property(property="role", type="string", description="Primary role name (derived)", example="staff"),
 *   @OA\Property(property="roles", type="array", @OA\Items(type="string")),
 *   @OA\Property(property="permissions", type="array", @OA\Items(type="string")),
 *   @OA\Property(property="created_at", type="string", format="date-time"),
 *   @OA\Property(property="updated_at", type="string", format="date-time"),
 *   @OA\Property(property="department", ref="#/components/schemas/Department", nullable=true)
 * )
 */
class UserSchema {}

/**
 * @OA\Schema(
 *   schema="LoginRequest",
 *   type="object",
 *   required={"email","password"},
 *   @OA\Property(property="email", type="string", example="admin@mnh.go.tz"),
 *   @OA\Property(property="password", type="string", example="12345678")
 * )
 */
class LoginRequest {}

/**
 * @OA\Schema(
 *   schema="LoginResponse",
 *   type="object",
 *   @OA\Property(property="user", ref="#/components/schemas/User"),
 *   @OA\Property(property="token", type="string"),
 *   @OA\Property(property="token_name", type="string")
 * )
 */
class LoginResponse {}

/**
 * @OA\Schema(
 *   schema="CreateUserRequest",
 *   type="object",
 *   required={"name","email","password","password_confirmation","role_ids"},
 *   @OA\Property(property="name", type="string"),
 *   @OA\Property(property="email", type="string"),
 *   @OA\Property(property="password", type="string"),
 *   @OA\Property(property="password_confirmation", type="string"),
 *   @OA\Property(property="phone", type="string", nullable=true),
 *   @OA\Property(property="pf_number", type="string", nullable=true),
 *   @OA\Property(property="staff_name", type="string", nullable=true),
 *   @OA\Property(property="department_id", type="integer", nullable=true),
 *   @OA\Property(property="is_active", type="boolean", default=true),
 *   @OA\Property(property="role_ids", type="array", @OA\Items(type="integer"))
 * )
 */
class CreateUserRequest {}

/**
 * @OA\Schema(
 *   schema="AssignRolesRequest",
 *   type="object",
 *   required={"role_ids"},
 *   @OA\Property(property="role_ids", type="array", @OA\Items(type="integer"))
 * )
 */
class AssignRolesRequest {}

/**
 * @OA\Schema(
 *   schema="RevokeSessionRequest",
 *   type="object",
 *   required={"token_id"},
 *   @OA\Property(property="token_id", type="integer", example=1)
 * )
 */
class RevokeSessionRequest {}

/**
 * Tags
 * @OA\Tag(name="Health", description="Health checks")
 * @OA\Tag(name="Auth", description="Authentication endpoints")
 * @OA\Tag(name="Users", description="Admin user management")
 * @OA\Tag(name="Departments", description="Admin department management")
 * @OA\Tag(name="UserRoles", description="User role assignment")
 * @OA\Tag(name="Onboarding", description="User onboarding")
 * @OA\Tag(name="Declaration", description="Declaration form")
 * @OA\Tag(name="BookingService", description="Booking service management")
 * @OA\Tag(name="BothServiceForm", description="Combined service form (HOD dashboard)")
 */
class Tags {}

/**
 * Paths: Health
 * @OA\Get(
 *   path="/api/health",
 *   tags={"Health"},
 *   summary="Basic health check",
 *   @OA\Response(response=200, description="OK",
 *     @OA\JsonContent(type="object",
 *       @OA\Property(property="status", type="string", example="ok"),
 *       @OA\Property(property="timestamp", type="string"))
 *   )
 * )
 * @OA\Get(
 *   path="/api/health/detailed",
 *   tags={"Health"},
 *   summary="Detailed health check including database",
 *   @OA\Response(response=200, description="OK")
 * )
 */
class HealthPaths {}

/**
 * Paths: Auth
 * @OA\Post(
 *   path="/api/login",
 *   tags={"Auth"},
 *   summary="Login",
 *   @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/LoginRequest")),
 *   @OA\Response(response=200, description="Success", @OA\JsonContent(ref="#/components/schemas/LoginResponse")),
 *   @OA\Response(response=401, description="Invalid credentials")
 * )
 * @OA\Post(
 *   path="/api/logout",
 *   tags={"Auth"},
 *   summary="Logout current session",
 *   security={{"bearerAuth":{}}},
 *   @OA\Response(response=200, description="Logged out")
 * )
 * @OA\Post(
 *   path="/api/logout-all",
 *   tags={"Auth"},
 *   summary="Logout all sessions",
 *   security={{"bearerAuth":{}}},
 *   @OA\Response(response=200, description="Logged out from all sessions")
 * )
 * @OA\Get(
 *   path="/api/sessions",
 *   tags={"Auth"},
 *   summary="List active sessions for current user",
 *   security={{"bearerAuth":{}}},
 *   @OA\Response(response=200, description="OK")
 * )
 * @OA\Post(
 *   path="/api/sessions/revoke",
 *   tags={"Auth"},
 *   summary="Revoke a specific session by token id",
 *   security={{"bearerAuth":{}}},
 *   @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/RevokeSessionRequest")),
 *   @OA\Response(response=200, description="Revoked")
 * )
 * @OA\Get(
 *   path="/api/current-user",
 *   tags={"Auth"},
 *   summary="Get current authenticated user",
 *   security={{"bearerAuth":{}}},
 *   @OA\Response(response=200, description="Current user", @OA\JsonContent(ref="#/components/schemas/User"))
 * )
 * @OA\Get(
 *   path="/api/role-redirect",
 *   tags={"Auth"},
 *   summary="Get role-based redirect URL",
 *   security={{"bearerAuth":{}}},
 *   @OA\Response(response=200, description="OK")
 * )
 */
class AuthPaths {}

/**
 * Paths: Admin Users
 * @OA\Get(
 *   path="/api/admin/users",
 *   tags={"Users"},
 *   summary="List users",
 *   security={{"bearerAuth":{}}},
 *   @OA\Parameter(name="search", in="query", required=false, @OA\Schema(type="string")),
 *   @OA\Parameter(name="role", in="query", required=false, @OA\Schema(type="string")),
 *   @OA\Parameter(name="status", in="query", required=false, @OA\Schema(type="string", enum={"active","inactive","all"})),
 *   @OA\Response(response=200, description="OK")
 * )
 * @OA\Post(
 *   path="/api/admin/users",
 *   tags={"Users"},
 *   summary="Create user",
 *   security={{"bearerAuth":{}}},
 *   @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/CreateUserRequest")),
 *   @OA\Response(response=201, description="Created")
 * )
 * @OA\Get(
 *   path="/api/admin/users/{user}",
 *   tags={"Users"},
 *   summary="Get user",
 *   security={{"bearerAuth":{}}},
 *   @OA\Parameter(name="user", in="path", required=true, @OA\Schema(type="integer")),
 *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/User")),
 *   @OA\Response(response=404, description="Not found")
 * )
 * @OA\Put(
 *   path="/api/admin/users/{user}",
 *   tags={"Users"},
 *   summary="Update user",
 *   security={{"bearerAuth":{}}},
 *   @OA\Parameter(name="user", in="path", required=true, @OA\Schema(type="integer")),
 *   @OA\RequestBody(required=false, @OA\JsonContent(ref="#/components/schemas/CreateUserRequest")),
 *   @OA\Response(response=200, description="Updated"),
 *   @OA\Response(response=404, description="Not found")
 * )
 * @OA\Delete(
 *   path="/api/admin/users/{user}",
 *   tags={"Users"},
 *   summary="Delete user",
 *   security={{"bearerAuth":{}}},
 *   @OA\Parameter(name="user", in="path", required=true, @OA\Schema(type="integer")),
 *   @OA\Response(response=200, description="Deleted")
 * )
 * @OA\Patch(
 *   path="/api/admin/users/{user}/toggle-status",
 *   tags={"Users"},
 *   summary="Toggle user active status",
 *   security={{"bearerAuth":{}}},
 *   @OA\Parameter(name="user", in="path", required=true, @OA\Schema(type="integer")),
 *   @OA\Response(response=200, description="OK")
 * )
 * @OA\Get(
 *   path="/api/admin/users/roles",
 *   tags={"Users"},
 *   summary="List available roles",
 *   security={{"bearerAuth":{}}},
 *   @OA\Response(response=200, description="OK", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Role")))
 * )
 * @OA\Get(
 *   path="/api/admin/users/departments",
 *   tags={"Users"},
 *   summary="List available departments",
 *   security={{"bearerAuth":{}}},
 *   @OA\Response(response=200, description="OK", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Department")))
 * )
 */
class AdminUserPaths {}

/**
 * Paths: Admin Departments
 * @OA\Get(
 *   path="/api/admin/departments",
 *   tags={"Departments"},
 *   summary="List departments",
 *   security={{"bearerAuth":{}}},
 *   @OA\Response(response=200, description="OK")
 * )
 * @OA\Post(
 *   path="/api/admin/departments",
 *   tags={"Departments"},
 *   summary="Create department",
 *   security={{"bearerAuth":{}}},
 *   @OA\Response(response=201, description="Created")
 * )
 * @OA\Get(
 *   path="/api/admin/departments/{department}",
 *   tags={"Departments"},
 *   summary="Get department",
 *   security={{"bearerAuth":{}}},
 *   @OA\Parameter(name="department", in="path", required=true, @OA\Schema(type="integer")),
 *   @OA\Response(response=200, description="OK")
 * )
 * @OA\Put(
 *   path="/api/admin/departments/{department}",
 *   tags={"Departments"},
 *   summary="Update department",
 *   security={{"bearerAuth":{}}},
 *   @OA\Parameter(name="department", in="path", required=true, @OA\Schema(type="integer")),
 *   @OA\Response(response=200, description="Updated")
 * )
 * @OA\Delete(
 *   path="/api/admin/departments/{department}",
 *   tags={"Departments"},
 *   summary="Delete department",
 *   security={{"bearerAuth":{}}},
 *   @OA\Parameter(name="department", in="path", required=true, @OA\Schema(type="integer")),
 *   @OA\Response(response=200, description="Deleted")
 * )
 */
class AdminDepartmentPaths {}

/**
 * Paths: User Roles management
 * @OA\Get(
 *   path="/api/user-roles",
 *   tags={"UserRoles"},
 *   summary="List users with roles",
 *   security={{"bearerAuth":{}}},
 *   @OA\Response(response=200, description="OK")
 * )
 * @OA\Post(
 *   path="/api/user-roles/{user}/assign",
 *   tags={"UserRoles"},
 *   summary="Assign roles to user",
 *   security={{"bearerAuth":{}}},
 *   @OA\Parameter(name="user", in="path", required=true, @OA\Schema(type="integer")),
 *   @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/AssignRolesRequest")),
 *   @OA\Response(response=200, description="OK")
 * )
 * @OA\Delete(
 *   path="/api/user-roles/{user}/roles/{role}",
 *   tags={"UserRoles"},
 *   summary="Remove a role from user",
 *   security={{"bearerAuth":{}}},
 *   @OA\Parameter(name="user", in="path", required=true, @OA\Schema(type="integer")),
 *   @OA\Parameter(name="role", in="path", required=true, @OA\Schema(type="integer")),
 *   @OA\Response(response=200, description="OK")
 * )
 * @OA\Get(
 *   path="/api/user-roles/{user}/history",
 *   tags={"UserRoles"},
 *   summary="Get user role history",
 *   security={{"bearerAuth":{}}},
 *   @OA\Parameter(name="user", in="path", required=true, @OA\Schema(type="integer")),
 *   @OA\Response(response=200, description="OK")
 * )
 */
class UserRolePaths {}

/**
 * Paths: Booking Service (basic docs)
 * @OA\Get(
 *   path="/api/booking-service/bookings",
 *   tags={"BookingService"},
 *   summary="List bookings",
 *   security={{"bearerAuth":{}}},
 *   @OA\Response(response=200, description="OK")
 * )
 * @OA\Post(
 *   path="/api/booking-service/bookings",
 *   tags={"BookingService"},
 *   summary="Create booking",
 *   security={{"bearerAuth":{}}},
 *   @OA\Response(response=201, description="Created")
 * )
 */
class BookingServicePaths {}

/**
 * Paths: User Access v1 (basic docs)
 * @OA\Get(
 *   path="/api/v1/user-access",
 *   tags={"Users"},
 *   summary="List user access requests",
 *   security={{"bearerAuth":{}}},
 *   @OA\Response(response=200, description="OK")
 * )
 * @OA\Post(
 *   path="/api/v1/combined-access",
 *   tags={"Users"},
 *   summary="Create combined access request",
 *   security={{"bearerAuth":{}}},
 *   @OA\Response(response=201, description="Created")
 * )
 */
class UserAccessV1Paths {}

