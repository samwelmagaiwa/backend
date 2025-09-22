<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *     title="User Access Management API",
 *     version="1.0.0",
 *     description="Laravel API for User Access Management, Module Requests, Device Booking and Workflow Approvals",
 *     @OA\Contact(
 *         email="admin@example.com",
 *         name="API Support"
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
 *     name="Module Requests",
 *     description="Wellsoft and Jeeva module access requests"
 * )
 *
 * @OA\Tag(
 *     name="Device Booking",
 *     description="ICT device booking and management"
 * )
 *
 * @OA\Tag(
 *     name="Admin",
 *     description="Administrative functions"
 * )
 *
 * @OA\Tag(
 *     name="Workflow",
 *     description="Approval workflow management"
 * )
 *
 * @OA\Tag(
 *     name="ICT Approval",
 *     description="ICT officer approval processes"
 * )
 *
 * @OA\Tag(
 *     name="Profile",
 *     description="User profile management"
 * )
 *
 * @OA\Tag(
 *     name="Dashboard",
 *     description="Dashboard and statistics endpoints"
 * )
 */
abstract class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
