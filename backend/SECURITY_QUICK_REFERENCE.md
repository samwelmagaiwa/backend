# Security Quick Reference Guide

## 🚀 Quick Start

### Test Security Implementation
```bash
# Start the server and check for colorized security boot message
php artisan serve

# Or clear config to see boot security checks
php artisan config:clear

# Check logs for security confirmation
tail -f storage/logs/laravel.log | grep -i security

# Demo colorized output
php test_security_colors.php
```

**Colorized Output:**
- 🟢 **Green** = Security feature working correctly
- 🔴 **Red** = Security issue needs attention

**Example Output:**
```
[SECURITY] Boot checks: OK
[SECURITY] CORS Protection: OK - CORS configured
[SECURITY] Security Headers: OK - SecurityHeaders middleware available
[SECURITY] Rate Limiting: FAIL - Rate limiter not configured
```

### Rate Limiting Usage
```php
// Apply to routes in routes/api.php
Route::middleware(['throttle:api'])->group(function () {
    // 60 requests per minute
});

Route::middleware(['throttle:auth'])->group(function () {
    // 10 requests per minute for auth endpoints
});

Route::middleware(['throttle:sensitive'])->group(function () {
    // 5 requests per minute for sensitive operations  
});
```

## 🛡️ Security Middleware Order
1. **SecurityHeaders** - First (sets HTTP headers)
2. **InputSanitization** - Second (cleans input) 
3. **XSSProtection** - Third (validates cleaned input)

## 🔧 Common Customizations

### Disable Sanitization for Specific Fields
```php
// In InputSanitization.php
private function shouldSkipSanitization(string $key): bool
{
    return in_array($key, ['password', 'raw_html', 'markdown_content']);
}
```

### Custom CSP for Admin Panel  
```php
// In SecurityHeaders.php
if ($request->is('admin/*')) {
    $csp = "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval';";
}
```

### Custom Rate Limiter
```php
// In RouteServiceProvider.php  
RateLimiter::for('admin', function (Request $request) {
    return Limit::perMinute(120)->by($request->user()?->id ?: $request->ip());
});
```

## 🚨 Security Headers Applied

| Header | Value | Purpose |
|--------|-------|---------|
| X-Frame-Options | DENY | Prevent clickjacking |
| X-Content-Type-Options | nosniff | Prevent MIME sniffing |
| X-XSS-Protection | 1; mode=block | XSS filter activation |
| Referrer-Policy | strict-origin-when-cross-origin | Control referrer info |
| Strict-Transport-Security | max-age=31536000 | Force HTTPS (HTTPS only) |
| Content-Security-Policy | [restrictive] | Prevent XSS/injection |

## 📊 Rate Limits Summary

| Limiter | Requests/Min | Best For |
|---------|--------------|----------|
| api | 60 | General API usage |
| auth | 10 | Login/register |
| sensitive | 5 | Delete account, password reset |
| user | 100 | Authenticated user operations |
| uploads | 10 | File uploads |

## 🔍 Security Logging

All security events logged to `storage/logs/laravel.log`:
- XSS attempts with pattern and IP
- Rate limit violations  
- Input sanitization events
- Security header violations

## ⚡ Environment Setup

```env
# Required for production
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# CORS configuration
CORS_ALLOWED_ORIGINS=https://yourdomain.com,https://app.yourdomain.com
CORS_SUPPORTS_CREDENTIALS=true
```

## 🧪 Testing Security

### Test Rate Limiting
```bash
# Test API rate limiting (should block after 60 requests)
for i in {1..65}; do curl http://localhost:8000/api/test; done
```

### Test Input Sanitization
```bash
# This should be sanitized
curl -X POST http://localhost:8000/api/test \
  -H "Content-Type: application/json" \
  -d '{"name": "<script>alert(1)</script>John"}'
```

### Check Security Headers
```bash
# Verify headers are applied
curl -I http://localhost:8000/api/test

# Security health check endpoint (JSON response)
curl http://localhost:8000/api/security-test/health
```

## 🔧 Troubleshooting

### Issue: Rate limiting not working
**Solution:** Check if cache is configured (`config/cache.php`)

### Issue: CORS errors
**Solution:** Update `CORS_ALLOWED_ORIGINS` in `.env`

### Issue: Input sanitization too aggressive
**Solution:** Add field to skip list in `InputSanitization.php`

### Issue: CSP blocking legitimate resources
**Solution:** Update CSP in `SecurityHeaders.php`

## 📁 File Locations

- **Middlewares:** `app/Http/Middleware/`
- **Rate Limiting:** `app/Providers/RouteServiceProvider.php`
- **CORS Config:** `config/cors.php`
- **Middleware Registration:** `bootstrap/app.php`
- **Logs:** `storage/logs/laravel.log`

## 🎯 Production Checklist

- [ ] Update `CORS_ALLOWED_ORIGINS` with production domains
- [ ] Set `APP_ENV=production` and `APP_DEBUG=false`
- [ ] Configure HTTPS and verify HSTS header
- [ ] Test all rate limits work as expected
- [ ] Verify security headers with online tools
- [ ] Set up log monitoring and rotation
- [ ] Review CSP policy for your specific needs
- [ ] Test input sanitization with your data types

---
*For detailed information, see the full SECURITY.md guide*
