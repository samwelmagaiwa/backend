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
 *   schema="DepartmentRequest",
 *   type="object",
 *   required={"name"},
 *   @OA\Property(property="name", type="string"),
 *   @OA\Property(property="code", type="string", nullable=true),
 *   @OA\Property(property="description", type="string", nullable=true),
 *   @OA\Property(property="is_active", type="boolean", default=true)
 * )
 */
class DepartmentRequest {}

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
 * Extended domain schemas
 */

/**
 * @OA\Schema(
 *   schema="OnboardingProgress",
 *   type="object",
 *   @OA\Property(property="terms_accepted", type="boolean"),
 *   @OA\Property(property="ict_policy_accepted", type="boolean"),
 *   @OA\Property(property="declaration_submitted", type="boolean"),
 *   @OA\Property(property="completed", type="boolean")
 * )
 */
class OnboardingProgress {}

/**
 * @OA\Schema(
 *   schema="OnboardingStatusResponse",
 *   type="object",
 *   @OA\Property(property="progress", ref="#/components/schemas/OnboardingProgress"),
 *   @OA\Property(property="user_info", type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="role", type="string"),
 *     @OA\Property(property="role_display", type="string")
 *   )
 * )
 */
class OnboardingStatusResponse {}

/**
 * @OA\Schema(
 *   schema="DeclarationFormRequest",
 *   type="object",
 *   required={"full_name","pf_number","department","job_title","date","agreement"},
 *   @OA\Property(property="full_name", type="string"),
 *   @OA\Property(property="pf_number", type="string"),
 *   @OA\Property(property="department", type="string"),
 *   @OA\Property(property="job_title", type="string"),
 *   @OA\Property(property="signature", type="string", nullable=true, description="Base64 or URL"),
 *   @OA\Property(property="date", type="string", format="date"),
 *   @OA\Property(property="agreement", type="boolean")
 * )
 */
class DeclarationFormRequest {}

/**
 * @OA\Schema(
 *   schema="Booking",
 *   type="object",
 *   @OA\Property(property="id", type="integer"),
 *   @OA\Property(property="device_type_id", type="integer"),
 *   @OA\Property(property="department_id", type="integer"),
 *   @OA\Property(property="requested_by", type="integer"),
 *   @OA\Property(property="status", type="string", example="pending"),
 *   @OA\Property(property="start_date", type="string", format="date-time"),
 *   @OA\Property(property="end_date", type="string", format="date-time"),
 *   @OA\Property(property="reason", type="string", nullable=true)
 * )
 */
class Booking {}

/**
 * @OA\Schema(
 *   schema="BookingRequest",
 *   type="object",
 *   required={"device_type_id","department_id","start_date","end_date"},
 *   @OA\Property(property="device_type_id", type="integer"),
 *   @OA\Property(property="department_id", type="integer"),
 *   @OA\Property(property="start_date", type="string", format="date-time"),
 *   @OA\Property(property="end_date", type="string", format="date-time"),
 *   @OA\Property(property="reason", type="string", nullable=true)
 * )
 */
class BookingRequest {}

/**
 * @OA\Schema(
 *   schema="DeviceInventory",
 *   type="object",
 *   @OA\Property(property="id", type="integer"),
 *   @OA\Property(property="device_type", type="string"),
 *   @OA\Property(property="model", type="string", nullable=true),
 *   @OA\Property(property="serial_number", type="string", nullable=true),
 *   @OA\Property(property="available_quantity", type="integer"),
 *   @OA\Property(property="is_active", type="boolean")
 * )
 */
class DeviceInventory {}

/**
 * @OA\Schema(
 *   schema="ModuleRequest",
 *   type="object",
 *   @OA\Property(property="id", type="integer"),
 *   @OA\Property(property="user_access_id", type="integer"),
 *   @OA\Property(property="system", type="string", example="jeeva"),
 *   @OA\Property(property="requested_modules", type="array", @OA\Items(type="string")),
 *   @OA\Property(property="status", type="string", example="submitted"),
 *   @OA\Property(property="remarks", type="string", nullable=true)
 * )
 */
class ModuleRequest {}

/**
 * @OA\Schema(
 *   schema="Notification",
 *   type="object",
 *   @OA\Property(property="id", type="string"),
 *   @OA\Property(property="type", type="string"),
 *   @OA\Property(property="title", type="string"),
 *   @OA\Property(property="body", type="string"),
 *   @OA\Property(property="read_at", type="string", format="date-time", nullable=true)
 * )
 */
class Notification {}

/**
 * @OA\Schema(
 *   schema="ErrorResponse",
 *   type="object",
 *   @OA\Property(property="success", type="boolean", example=false),
 *   @OA\Property(property="message", type="string"),
 *   @OA\Property(property="errors", type="object", nullable=true)
 * )
 */
class ErrorResponse {}

/**
 * @OA\Schema(
 *   schema="SessionInfo",
 *   type="object",
 *   @OA\Property(property="id", type="integer"),
 *   @OA\Property(property="ip_address", type="string"),
 *   @OA\Property(property="user_agent", type="string"),
 *   @OA\Property(property="last_used_at", type="string", format="date-time")
 * )
 */
class SessionInfo {}

/**
 * @OA\Schema(
 *   schema="UserProfileUpdateRequest",
 *   type="object",
 *   @OA\Property(property="name", type="string"),
 *   @OA\Property(property="phone", type="string", nullable=true),
 *   @OA\Property(property="department_id", type="integer", nullable=true),
 *   @OA\Property(property="job_title", type="string", nullable=true)
 * )
 */
class UserProfileUpdateRequest {}

/**
 * @OA\Schema(
 *   schema="PfLookupRequest",
 *   type="object",
 *   required={"pf_number"},
 *   @OA\Property(property="pf_number", type="string")
 * )
 */
class PfLookupRequest {}

/**
 * @OA\Schema(
 *   schema="NotificationCounts",
 *   type="object",
 *   @OA\Property(property="total_pending", type="integer"),
 *   @OA\Property(property="hod", type="integer"),
 *   @OA\Property(property="divisional", type="integer"),
 *   @OA\Property(property="ict_director", type="integer"),
 *   @OA\Property(property="head_of_it", type="integer"),
 *   @OA\Property(property="ict_officer", type="integer")
 * )
 */
class NotificationCounts {}

/**
 * @OA\Schema(
 *   schema="ModuleAccessApprovalRequest",
 *   type="object",
 *   required={"action"},
 *   @OA\Property(property="action", type="string", enum={"approve","reject"}),
 *   @OA\Property(property="remarks", type="string", nullable=true)
 * )
 */
class ModuleAccessApprovalRequest {}

/**
 * @OA\Schema(
 *   schema="AccessRightsApproval",
 *   type="object",
 *   @OA\Property(property="id", type="integer"),
 *   @OA\Property(property="user_access_id", type="integer"),
 *   @OA\Property(property="status", type="string", example="approved"),
 *   @OA\Property(property="remarks", type="string", nullable=true)
 * )
 */
class AccessRightsApproval {}

/**
 * @OA\Schema(
 *   schema="ImplementationWorkflow",
 *   type="object",
 *   @OA\Property(property="id", type="integer"),
 *   @OA\Property(property="user_access_id", type="integer"),
 *   @OA\Property(property="status", type="string", example="in_progress"),
 *   @OA\Property(property="remarks", type="string", nullable=true)
 * )
 */
class ImplementationWorkflow {}

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
 *   @OA\Response(response=200, description="OK", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/SessionInfo")))
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
 *   @OA\Response(response=200, description="OK", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/User")))
 * )
 * @OA\Post(
 *   path="/api/admin/users",
 *   tags={"Users"},
 *   summary="Create user",
 *   security={{"bearerAuth":{}}},
 *   @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/CreateUserRequest")),
 *   @OA\Response(response=201, description="Created", @OA\JsonContent(ref="#/components/schemas/User"))
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
 *   @OA\Response(response=200, description="Updated", @OA\JsonContent(ref="#/components/schemas/User")),
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
 *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/User"))
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
 *   @OA\Response(response=200, description="OK", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Department")))
 * )
 * @OA\Post(
 *   path="/api/admin/departments",
 *   tags={"Departments"},
 *   summary="Create department",
 *   security={{"bearerAuth":{}}},
 *   @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/DepartmentRequest")),
 *   @OA\Response(response=201, description="Created", @OA\JsonContent(ref="#/components/schemas/Department"))
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
 *   @OA\RequestBody(required=false, @OA\JsonContent(ref="#/components/schemas/DepartmentRequest")),
 *   @OA\Response(response=200, description="Updated", @OA\JsonContent(ref="#/components/schemas/Department"))
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
 *   @OA\Response(response=200, description="OK", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Booking")))
 * )
 * @OA\Post(
 *   path="/api/booking-service/bookings",
 *   tags={"BookingService"},
 *   summary="Create booking",
 *   security={{"bearerAuth":{}}},
 *   @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/BookingRequest")),
 *   @OA\Response(response=201, description="Created", @OA\JsonContent(ref="#/components/schemas/Booking"))
 * )
 */
class BookingServicePaths {}

/**
 * Paths: Onboarding
 * @OA\Get(
 *   path="/api/onboarding/status",
 *   tags={"Onboarding"},
 *   summary="Get onboarding status for the current user",
 *   security={{"bearerAuth":{}}},
 *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/OnboardingStatusResponse"))
 * )
 * @OA\Post(
 *   path="/api/onboarding/complete",
 *   tags={"Onboarding"},
 *   summary="Mark onboarding as complete",
 *   security={{"bearerAuth":{}}},
 *   @OA\Response(response=200, description="OK")
 * )
 * @OA\Post(
 *   path="/api/onboarding/accept-terms",
 *   tags={"Onboarding"},
 *   summary="Accept terms of service",
 *   security={{"bearerAuth":{}}},
 *   @OA\Response(response=200, description="OK")
 * )
 * @OA\Post(
 *   path="/api/onboarding/accept-ict-policy",
 *   tags={"Onboarding"},
 *   summary="Accept ICT policy",
 *   security={{"bearerAuth":{}}},
 *   @OA\Response(response=200, description="OK")
 * )
 */
class OnboardingPaths {}

/**
 * Paths: Declaration
 * @OA\Post(
 *   path="/api/declaration/submit",
 *   tags={"Declaration"},
 *   summary="Submit declaration form",
 *   security={{"bearerAuth":{}}},
 *   @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/DeclarationFormRequest")),
 *   @OA\Response(response=200, description="Submitted")
 * )
 */
class DeclarationPaths {}

/**
 * Paths: Device Inventory (basic docs)
 * @OA\Get(
 *   path="/api/device-inventory",
 *   tags={"BookingService"},
 *   summary="List available device inventory",
 *   security={{"bearerAuth":{}}},
 *   @OA\Response(response=200, description="OK", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/DeviceInventory")))
 * )
 */
class DeviceInventoryPaths {}

/**
 * Paths: User Access v1 (basic docs)
 * @OA\Get(
 *   path="/api/v1/user-access",
 *   tags={"Users"},
 *   summary="List user access requests",
 *   security={{"bearerAuth":{}}},
 *   @OA\Response(response=200, description="OK", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/ModuleRequest")))
 * )
 * @OA\Post(
 *   path="/api/v1/combined-access",
 *   tags={"Users"},
 *   summary="Create combined access request",
 *   security={{"bearerAuth":{}}},
 *   @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/ModuleRequest")),
 *   @OA\Response(response=201, description="Created", @OA\JsonContent(ref="#/components/schemas/ModuleRequest"))
 * )
 */
class UserAccessV1Paths {}

/**
 * Paths: Profile
 * @OA\Get(
 *   path="/api/profile/current",
 *   tags={"Users"},
 *   summary="Get current user profile",
 *   security={{"bearerAuth":{}}},
 *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/User"))
 * )
 * @OA\Put(
 *   path="/api/profile/current",
 *   tags={"Users"},
 *   summary="Update current user profile",
 *   security={{"bearerAuth":{}}},
 *   @OA\RequestBody(required=false, @OA\JsonContent(ref="#/components/schemas/UserProfileUpdateRequest")),
 *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/User"))
 * )
 * @OA\Post(
 *   path="/api/profile/lookup-pf",
 *   tags={"Users"},
 *   summary="Lookup user by PF number",
 *   security={{"bearerAuth":{}}},
 *   @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/PfLookupRequest")),
 *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/User"))
 * )
 * @OA\Post(
 *   path="/api/profile/check-pf",
 *   tags={"Users"},
 *   summary="Check if PF number exists",
 *   security={{"bearerAuth":{}}},
 *   @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/PfLookupRequest")),
 *   @OA\Response(response=200, description="OK", @OA\JsonContent(type="object",
 *     @OA\Property(property="exists", type="boolean")
 *   ))
 * )
 * @OA\Get(
 *   path="/api/profile/departments",
 *   tags={"Users"},
 *   summary="Get departments for profile",
 *   security={{"bearerAuth":{}}},
 *   @OA\Response(response=200, description="OK", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Department")))
 * )
 */
class ProfilePaths {}

/**
 * Paths: Module Access Approval
 * @OA\Get(
 *   path="/api/module-access-approval/{id}",
 *   tags={"BothServiceForm"},
 *   summary="Get request for approval",
 *   security={{"bearerAuth":{}}},
 *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/ModuleRequest"))
 * )
 * @OA\Post(
 *   path="/api/module-access-approval/{id}/process",
 *   tags={"BothServiceForm"},
 *   summary="Process approval (approve/reject)",
 *   security={{"bearerAuth":{}}},
 *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *   @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/ModuleAccessApprovalRequest")),
 *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/ModuleRequest"))
 * )
 */
class ModuleAccessApprovalPaths {}

/**
 * Paths: Module Requests
 * @OA\Post(
 *   path="/api/module-requests",
 *   tags={"BothServiceForm"},
 *   summary="Create a module request",
 *   security={{"bearerAuth":{}}},
 *   @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/ModuleRequest")),
 *   @OA\Response(response=201, description="Created", @OA\JsonContent(ref="#/components/schemas/ModuleRequest"))
 * )
 * @OA\Get(
 *   path="/api/module-requests/modules",
 *   tags={"BothServiceForm"},
 *   summary="List available modules",
 *   security={{"bearerAuth":{}}},
 *   @OA\Response(response=200, description="OK", @OA\JsonContent(type="array", @OA\Items(type="string")))
 * )
 * @OA\Get(
 *   path="/api/module-requests/{userAccessId}",
 *   tags={"BothServiceForm"},
 *   summary="Get module request",
 *   security={{"bearerAuth":{}}},
 *   @OA\Parameter(name="userAccessId", in="path", required=true, @OA\Schema(type="integer")),
 *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/ModuleRequest"))
 * )
 * @OA\Put(
 *   path="/api/module-requests/{userAccessId}",
 *   tags={"BothServiceForm"},
 *   summary="Update module request",
 *   security={{"bearerAuth":{}}},
 *   @OA\Parameter(name="userAccessId", in="path", required=true, @OA\Schema(type="integer")),
 *   @OA\RequestBody(required=false, @OA\JsonContent(ref="#/components/schemas/ModuleRequest")),
 *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/ModuleRequest"))
 * )
 * @OA\Post(
 *   path="/api/module-requests/jeeva",
 *   tags={"BothServiceForm"},
 *   summary="Create a Jeeva module request",
 *   security={{"bearerAuth":{}}},
 *   @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/ModuleRequest")),
 *   @OA\Response(response=201, description="Created", @OA\JsonContent(ref="#/components/schemas/ModuleRequest"))
 * )
 * @OA\Get(
 *   path="/api/module-requests/jeeva/modules",
 *   tags={"BothServiceForm"},
 *   summary="List available Jeeva modules",
 *   security={{"bearerAuth":{}}},
 *   @OA\Response(response=200, description="OK", @OA\JsonContent(type="array", @OA\Items(type="string")))
 * )
 * @OA\Get(
 *   path="/api/module-requests/jeeva/{userAccessId}",
 *   tags={"BothServiceForm"},
 *   summary="Get Jeeva module request",
 *   security={{"bearerAuth":{}}},
 *   @OA\Parameter(name="userAccessId", in="path", required=true, @OA\Schema(type="integer")),
 *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/ModuleRequest"))
 * )
 * @OA\Put(
 *   path="/api/module-requests/jeeva/{userAccessId}",
 *   tags={"BothServiceForm"},
 *   summary="Update Jeeva module request",
 *   security={{"bearerAuth":{}}},
 *   @OA\Parameter(name="userAccessId", in="path", required=true, @OA\Schema(type="integer")),
 *   @OA\RequestBody(required=false, @OA\JsonContent(ref="#/components/schemas/ModuleRequest")),
 *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/ModuleRequest"))
 * )
 */
class ModuleRequestsPaths {}

/**
 * Paths: Access Rights Approval
 * @OA\Post(
 *   path="/api/access-rights-approval",
 *   tags={"BothServiceForm"},
 *   summary="Create access rights approval data",
 *   security={{"bearerAuth":{}}},
 *   @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/AccessRightsApproval")),
 *   @OA\Response(response=201, description="Created", @OA\JsonContent(ref="#/components/schemas/AccessRightsApproval"))
 * )
 * @OA\Get(
 *   path="/api/access-rights-approval/{userAccessId}",
 *   tags={"BothServiceForm"},
 *   summary="Get access rights approval data",
 *   security={{"bearerAuth":{}}},
 *   @OA\Parameter(name="userAccessId", in="path", required=true, @OA\Schema(type="integer")),
 *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/AccessRightsApproval"))
 * )
 * @OA\Put(
 *   path="/api/access-rights-approval/{userAccessId}",
 *   tags={"BothServiceForm"},
 *   summary="Update access rights approval data",
 *   security={{"bearerAuth":{}}},
 *   @OA\Parameter(name="userAccessId", in="path", required=true, @OA\Schema(type="integer")),
 *   @OA\RequestBody(required=false, @OA\JsonContent(ref="#/components/schemas/AccessRightsApproval")),
 *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/AccessRightsApproval"))
 * )
 */
class AccessRightsApprovalPaths {}

/**
 * Paths: Implementation Workflow
 * @OA\Post(
 *   path="/api/implementation-workflow",
 *   tags={"BothServiceForm"},
 *   summary="Create an implementation workflow record",
 *   security={{"bearerAuth":{}}},
 *   @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/ImplementationWorkflow")),
 *   @OA\Response(response=201, description="Created", @OA\JsonContent(ref="#/components/schemas/ImplementationWorkflow"))
 * )
 * @OA\Get(
 *   path="/api/implementation-workflow/{userAccessId}",
 *   tags={"BothServiceForm"},
 *   summary="Get implementation workflow",
 *   security={{"bearerAuth":{}}},
 *   @OA\Parameter(name="userAccessId", in="path", required=true, @OA\Schema(type="integer")),
 *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/ImplementationWorkflow"))
 * )
 * @OA\Put(
 *   path="/api/implementation-workflow/{userAccessId}",
 *   tags={"BothServiceForm"},
 *   summary="Update implementation workflow",
 *   security={{"bearerAuth":{}}},
 *   @OA\Parameter(name="userAccessId", in="path", required=true, @OA\Schema(type="integer")),
 *   @OA\RequestBody(required=false, @OA\JsonContent(ref="#/components/schemas/ImplementationWorkflow")),
 *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/ImplementationWorkflow"))
 * )
 */
class ImplementationWorkflowPaths {}

/**
 * Paths: Notifications
 * @OA\Get(
 *   path="/api/notifications/pending-count",
 *   tags={"Notifications"},
 *   summary="Get pending requests count",
 *   security={{"bearerAuth":{}}},
 *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/NotificationCounts"))
 * )
 * @OA\Get(
 *   path="/api/notifications/breakdown",
 *   tags={"Notifications"},
 *   summary="Get pending requests breakdown (admin only)",
 *   security={{"bearerAuth":{}}},
 *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/NotificationCounts"))
 * )
 * @OA\Get(
 *   path="/api/notifications",
 *   tags={"Notifications"},
 *   summary="List notifications",
 *   security={{"bearerAuth":{}}},
 *   @OA\Response(response=200, description="OK", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Notification")))
 * )
 * @OA\Get(
 *   path="/api/notifications/unread-count",
 *   tags={"Notifications"},
 *   summary="Get unread notifications count",
 *   security={{"bearerAuth":{}}},
 *   @OA\Response(response=200, description="OK", @OA\JsonContent(type="object", @OA\Property(property="count", type="integer")))
 * )
 * @OA\Patch(
 *   path="/api/notifications/{id}/mark-read",
 *   tags={"Notifications"},
 *   summary="Mark a notification as read",
 *   security={{"bearerAuth":{}}},
 *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
 *   @OA\Response(response=200, description="OK")
 * )
 * @OA\Patch(
 *   path="/api/notifications/mark-all-read",
 *   tags={"Notifications"},
 *   summary="Mark all notifications as read",
 *   security={{"bearerAuth":{}}},
 *   @OA\Response(response=200, description="OK")
 * )
 * @OA\Delete(
 *   path="/api/notifications/{id}",
 *   tags={"Notifications"},
 *   summary="Delete a notification",
 *   security={{"bearerAuth":{}}},
 *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
 *   @OA\Response(response=200, description="OK")
 * )
 */
class NotificationPaths {}

/**
 * Paths: Filtered Admin User Access views
 * @OA\Get(
 *   path="/api/jeeva-users",
 *   tags={"Users"},
 *   summary="List Jeeva users",
 *   security={{"bearerAuth":{}}},
 *   @OA\Response(response=200, description="OK", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/User")))
 * )
 * @OA\Get(
 *   path="/api/wellsoft-users",
 *   tags={"Users"},
 *   summary="List Wellsoft users",
 *   security={{"bearerAuth":{}}},
 *   @OA\Response(response=200, description="OK", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/User")))
 * )
 * @OA\Get(
 *   path="/api/internet-users",
 *   tags={"Users"},
 *   summary="List Internet users",
 *   security={{"bearerAuth":{}}},
 *   @OA\Response(response=200, description="OK", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/User")))
 * )
 */
class FilteredUsersPaths {}

