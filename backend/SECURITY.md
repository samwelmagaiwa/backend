# Laravel 12 Security Implementation Guide

This document outlines the comprehensive security features implemented in your Laravel 12 backend API.

## 🔒 Security Features Overview

### ✅ Implemented Security Features

1. **CORS Protection** - Configured with Laravel's built-in CORS support
2. **Security Headers** - Helmet-like security headers implementation
3. **Rate Limiting** - Multiple rate limiting strategies (60 req/min global)
4. **Input Sanitization** - Automatic HTML/JS/Script tag removal
5. **XSS Protection** - Advanced XSS detection and prevention
6. **SQL Injection Prevention** - Enforced through Eloquent ORM
7. **CSRF Protection** - Laravel's built-in CSRF protection

## 📁 File Structure

```
backend/
├── app/Http/Middleware/
│   ├── SecurityHeaders.php      # Comprehensive security headers
│   ├── InputSanitization.php    # Input sanitization middleware
│   └── XSSProtection.php        # XSS protection and detection
├── app/Providers/
│   ├── AppServiceProvider.php   # Security logging and boot message
│   └── RouteServiceProvider.php # Rate limiting configuration
├── config/
│   └── cors.php                 # CORS configuration
├── bootstrap/
│   └── app.php                  # Middleware registration
└── SECURITY.md                  # This documentation
```

## 🛡️ Security Middleware Details

### 1. Security Headers Middleware

**File:** `app/Http/Middleware/SecurityHeaders.php`

**Headers Applied:**
- `X-Frame-Options: DENY`
- `X-Content-Type-Options: nosniff`
- `X-XSS-Protection: 1; mode=block`
- `Referrer-Policy: strict-origin-when-cross-origin`
- `Permissions-Policy: [restricted permissions]`
- `Content-Security-Policy: [strict CSP]`
- `Strict-Transport-Security: [HTTPS only]`
- `Cross-Origin-Embedder-Policy: require-corp`
- `Cross-Origin-Opener-Policy: same-origin`

### 2. Input Sanitization Middleware

**File:** `app/Http/Middleware/InputSanitization.php`

**Features:**
- Removes HTML tags from all inputs
- Strips JavaScript event handlers
- Eliminates script, style, iframe tags
- Removes dangerous protocols (javascript:, data:, vbscript:)
- Filters control characters
- HTML entity encoding for output safety

### 3. XSS Protection Middleware

**File:** `app/Http/Middleware/XSSProtection.php`

**Features:**
- Real-time XSS pattern detection
- Comprehensive pattern matching
- Security logging for all attempts
- Automatic blocking of high-risk patterns
- Response content validation

## ⚡ Rate Limiting Configuration

**File:** `app/Providers/RouteServiceProvider.php`

### Rate Limits Applied:

| Limiter | Limit | Scope | Usage |
|---------|-------|-------|-------|
| `api` | 60/min | Per IP | Global API protection |
| `auth` | 10/min | Per IP | Authentication endpoints |
| `sensitive` | 5/min | Per IP | Sensitive operations |
| `user` | 100/min | Per User/IP | Authenticated users |
| `uploads` | 10/min | Per IP | File uploads |

### Usage in Routes:

```php
// Apply rate limiting to routes
Route::middleware(['throttle:api'])->group(function () {
    Route::get('/users', [UserController::class, 'index']);
});

// Authentication routes with stricter limits
Route::middleware(['throttle:auth'])->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
});

// Sensitive operations
Route::middleware(['auth:sanctum', 'throttle:sensitive'])->group(function () {
    Route::delete('/account', [AccountController::class, 'delete']);
    Route::post('/password/reset', [PasswordController::class, 'reset']);
});
```

## 🌐 CORS Configuration

**File:** `config/cors.php`

### Configuration Features:
- Restricted HTTP methods (GET, POST, PUT, PATCH, DELETE, OPTIONS)
- Specific allowed headers only
- Environment-based origin configuration
- Rate limit headers exposure
- 24-hour preflight cache

### Environment Variables:

```env
# .env configuration
CORS_ALLOWED_ORIGINS=https://yourdomain.com,https://app.yourdomain.com
CORS_SUPPORTS_CREDENTIALS=true
```

## 📊 Security Logging

All security events are automatically logged:

### Log Categories:
- XSS attempt detection
- Rate limit violations
- Suspicious input patterns
- Security header violations

### Log Location:
- Development: Console output + `storage/logs/laravel.log`
- Production: `storage/logs/laravel.log`

## 🚀 Usage Examples

### 1. Protecting API Routes

```php
// routes/api.php
use Illuminate\Support\Facades\Route;

// Public routes with basic rate limiting
Route::middleware(['throttle:api'])->group(function () {
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
});

// Authentication routes with strict rate limiting
Route::middleware(['throttle:auth'])->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
});

// Protected routes for authenticated users
Route::middleware(['auth:sanctum', 'throttle:user'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

// Sensitive operations with strictest limits
Route::middleware(['auth:sanctum', 'throttle:sensitive'])->group(function () {
    Route::delete('/account', [AccountController::class, 'delete']);
    Route::post('/password/change', [PasswordController::class, 'change']);
    Route::post('/two-factor/enable', [TwoFactorController::class, 'enable']);
});

// File upload routes
Route::middleware(['auth:sanctum', 'throttle:uploads'])->group(function () {
    Route::post('/upload/avatar', [UploadController::class, 'avatar']);
    Route::post('/upload/documents', [UploadController::class, 'documents']);
});
```

### 2. Custom Rate Limiting

You can create custom rate limiters:

```php
// In RouteServiceProvider.php
RateLimiter::for('custom', function (Request $request) {
    return Limit::perMinute(30)
        ->by($request->user()?->id ?: $request->ip())
        ->response(function () {
            return response()->json([
                'error' => 'Custom rate limit exceeded'
            ], 429);
        });
});

// Use in routes
Route::middleware(['throttle:custom'])->group(function () {
    // Your routes here
});
```

### 3. Customizing Security Headers

```php
// Modify SecurityHeaders.php to customize headers
private function getCustomCSP(Request $request): string
{
    if ($request->is('admin/*')) {
        return "default-src 'self'; script-src 'self' 'unsafe-eval';";
    }
    
    return "default-src 'self'; script-src 'self';";
}
```

### 4. Input Sanitization Exceptions

```php
// In InputSanitization.php, add exceptions for specific fields
private function shouldSkipSanitization(string $key): bool
{
    $skipFields = ['password', 'password_confirmation', 'raw_content'];
    return in_array($key, $skipFields);
}
```

## 🔧 Configuration Options

### Environment Variables

```env
# Security Configuration
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# CORS Configuration
CORS_ALLOWED_ORIGINS=https://yourdomain.com,https://app.yourdomain.com
CORS_SUPPORTS_CREDENTIALS=true

# Rate Limiting (optional - defaults are set)
RATE_LIMIT_API=60
RATE_LIMIT_AUTH=10
RATE_LIMIT_SENSITIVE=5
```

### Custom Middleware Priority

The security middlewares are applied in this order:

1. `SecurityHeaders` - Sets security headers first
2. `InputSanitization` - Cleans input data early
3. `XSSProtection` - Validates cleaned data for XSS patterns

## 🚨 Security Best Practices

### 1. Database Security
- Always use Eloquent ORM or Query Builder
- Never concatenate user input in raw SQL
- Use parameter binding for all dynamic queries

```php
// ✅ Good - Using Eloquent
User::where('email', $email)->first();

// ✅ Good - Using Query Builder with bindings
DB::select('SELECT * FROM users WHERE email = ?', [$email]);

// ❌ Bad - Raw SQL concatenation
DB::select("SELECT * FROM users WHERE email = '$email'");
```

### 2. Data Validation
- Always validate input data with Laravel's validation
- Use Form Request classes for complex validation

```php
// Create Form Request
php artisan make:request StoreUserRequest

// In StoreUserRequest.php
public function rules()
{
    return [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:8|confirmed',
    ];
}
```

### 3. Authentication Security
- Use Laravel Sanctum for API authentication
- Implement proper token expiration
- Use strong password policies

```php
// In AuthController
public function login(Request $request)
{
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    if (Auth::attempt($credentials)) {
        $token = $request->user()->createToken('api-token', ['*'], now()->addHours(24));
        return response()->json(['token' => $token->plainTextToken]);
    }

    return response()->json(['error' => 'Invalid credentials'], 401);
}
```

### 4. File Upload Security
- Validate file types and sizes
- Store uploads outside webroot
- Scan for malware if possible

```php
public function upload(Request $request)
{
    $request->validate([
        'file' => 'required|file|mimes:jpg,png,pdf|max:2048'
    ]);
    
    $path = $request->file('file')->store('uploads', 'private');
    return response()->json(['path' => $path]);
}
```

## 🔍 Monitoring & Maintenance

### 1. Log Monitoring
- Regularly check `storage/logs/laravel.log` for security events
- Set up log rotation to prevent disk space issues
- Consider using log aggregation tools like ELK stack

### 2. Security Updates
- Keep Laravel and dependencies updated
- Monitor security advisories
- Regular security audits

### 3. Performance Impact
- Security middleware adds minimal overhead (~1-2ms per request)
- Rate limiting uses cache for efficiency
- Monitor application performance regularly

## 🔗 Additional Resources

- [Laravel Security Documentation](https://laravel.com/docs/security)
- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [Laravel Best Practices](https://github.com/alexeymezenin/laravel-best-practices)
- [Security Headers Guide](https://securityheaders.com/)

## 📞 Support

For security-related questions or issues:
1. Check this documentation first
2. Review Laravel's official security documentation
3. Test security implementations in development environment
4. Consider professional security auditing for production systems

---

**Last Updated:** September 11, 2025
**Laravel Version:** 12.x
**Security Implementation:** Comprehensive Protection Suite
