<?php

namespace App\Http\Controllers\Api;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="User Access Management API",
 *     description="Laravel API for User Access Management, Module Requests, Device Booking and Workflow Approvals",
 *     @OA\Contact(
 *         name="API Support",
 *         email="admin@example.com"
 *     )
 * )
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="API Server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="apiKey",
 *     in="header",
 *     name="Authorization",
 *     description="Enter token in format (Bearer <token>)"
 * )
 *
 * @OA\Tag(
 *     name="Authentication",
 *     description="User authentication and authorization endpoints"
 * )
 *
 * @OA\Tag(
 *     name="User Access",
 *     description="User access request management"
 * )
 *
 * @OA\Tag(
 *     name="Both Service Form",
 *     description="Combined service forms for multi-level approval workflow"
 * )
 *
 * @OA\Tag(
 *     name="Device Booking",
 *     description="ICT device booking and management"
 * )
 *
 * @OA\Tag(
 *     name="ICT Approval",
 *     description="ICT officer approval processes"
 * )
 *
 * @OA\Tag(
 *     name="Admin",
 *     description="Administrative functions (user and department management)"
 * )
 *
 * @OA\Tag(
 *     name="HOD Workflow",
 *     description="Head of Department approval workflow"
 * )
 *
 * @OA\Tag(
 *     name="Divisional Workflow",
 *     description="Divisional Director approval workflow"
 * )
 *
 * @OA\Tag(
 *     name="ICT Director Workflow",
 *     description="ICT Director approval workflow"
 * )
 *
 * @OA\Tag(
 *     name="Profile",
 *     description="User profile management and auto-population"
 * )
 *
 * @OA\Tag(
 *     name="Notifications",
 *     description="User notification management"
 * )
 *
 * @OA\Tag(
 *     name="Utility",
 *     description="Health checks and system utilities"
 * )
 */
class OpenApiInfo
{
    // This class only exists to hold OpenAPI annotations
}
