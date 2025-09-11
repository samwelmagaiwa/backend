# Frontend Booking Submission Troubleshooting Guide

## Issue: "Submit Booking Request" Button Not Working

Based on backend analysis, the API is functioning correctly. The issue is likely frontend-related.

## Step-by-Step Debugging

### 1. **Check Browser Developer Tools**

#### JavaScript Console
1. Open browser Developer Tools (F12)
2. Go to **Console** tab
3. Look for JavaScript errors when:
   - Page loads
   - Filling out the form
   - Clicking submit button

**Common Errors to Look For:**
```
- TypeError: Cannot read property 'xxx' of undefined
- ReferenceError: xxx is not defined  
- Network request failed
- 422 Unprocessable Entity
- 401 Unauthorized
```

#### Network Tab
1. Go to **Network** tab in Developer Tools
2. Clear existing requests
3. Fill out and submit the form
4. Watch for API calls

**Expected Request:**
```
Method: POST
URL: http://localhost:8000/api/booking-service/bookings
Status: 201 Created (success) or 422 (validation error)
Headers: Authorization: Bearer [token]
Content-Type: multipart/form-data
```

**If No Network Request Appears:**
- Form validation is blocking submission
- JavaScript error preventing form submission
- Event handler not attached to button

### 2. **Form Validation Issues**

The form has extensive validation. Check these common blockers:

#### Required Fields
- ✅ **Booking Date** - Must be today or future
- ✅ **Borrower Name** - Auto-filled, should not be empty
- ✅ **Device Type** - Must select from dropdown
- ✅ **Department** - Must select from dropdown  
- ✅ **Collection Date** - Must be after booking date
- ✅ **Return Time** - Must be selected
- ✅ **Reason** - Must be at least 10 characters
- ✅ **Phone Number** - Auto-filled from user profile
- ✅ **Digital Signature** - Must upload image file

#### Device Selection Issues
- Check if `availableDevices` array is populated
- Look for console errors when selecting device
- Verify device inventory API is loading

### 3. **Authentication Issues**

#### Check Auth Token
```javascript
// In browser console, check:
localStorage.getItem('auth_token')
localStorage.getItem('user_data')
```

**If Token Missing:**
- User needs to log out and log in again
- Session expired

### 4. **Common Frontend Fixes**

#### A. Form Validation Blocking
Look in the form component for validation that prevents submission:

**In BookingService.vue, check:**
```javascript
validateForm() {
  // Clear previous errors
  this.errors = {}
  
  // Check each validation rule
  // If any fail, submission is blocked
}
```

#### B. Submit Button State
The button might be disabled due to:
```javascript
:disabled="isSubmitting"
// or
:disabled="!canSubmit"
```

#### C. Pending Request Check
The form checks for existing pending requests:
```javascript
if (this.hasPendingRequest) {
  alert('You have a pending booking request...')
  return // Blocks submission
}
```

### 5. **Debug Steps to Take**

#### Step 1: Check Form State
In browser console:
```javascript
// Find the Vue component instance and check its state
$vm0.formData
$vm0.errors  
$vm0.isSubmitting
$vm0.hasPendingRequest
```

#### Step 2: Test Submit Function
```javascript
// Check if submit function exists
$vm0.submitBooking
// Manually trigger it (be careful!)
$vm0.submitBooking()
```

#### Step 3: Check API Service
```javascript
// Test the booking service directly
import bookingService from '@/services/bookingService'
bookingService.checkPendingRequests()
```

### 6. **Likely Root Causes**

Based on the component code, most likely issues:

#### A. **Pending Request Block** (Most Likely)
- User has existing pending booking (ID: 11)
- Form blocks new submissions until old one is processed
- **Solution**: Process existing booking or allow multiple requests

#### B. **Form Validation Failure**
- Required field missing (especially signature upload)
- Date validation failing
- **Solution**: Check all form fields are properly filled

#### C. **Device Loading Issue**  
- Available devices not loading from API
- Device selection causing validation failure
- **Solution**: Check device inventory API

#### D. **Authentication Token Expired**
- Token missing or invalid
- **Solution**: Re-login to get fresh token

### 7. **Quick Fixes to Try**

#### Fix 1: Clear Existing Pending Request
Since there's an existing booking (ID: 11), either:
1. Process/approve the existing booking through ICT dashboard
2. Or modify the frontend to allow multiple pending requests

#### Fix 2: Check Signature Upload
- Verify signature file is properly selected
- Check file size < 2MB
- Ensure file format is PNG/JPG/JPEG

#### Fix 3: Refresh User Session
1. Log out completely
2. Clear localStorage
3. Log back in
4. Try submitting again

### 8. **Testing Checklist**

Before submission, verify:
- [ ] All form fields filled with valid data
- [ ] No JavaScript errors in console  
- [ ] Auth token present in localStorage
- [ ] No existing pending requests blocking
- [ ] Network tab shows API request on submit
- [ ] Device inventory loaded successfully

### 9. **Manual API Test**

To test if backend works, use browser console:
```javascript
// Test API manually
fetch('/api/booking-service/bookings', {
  method: 'POST',
  headers: {
    'Authorization': 'Bearer ' + localStorage.getItem('auth_token'),
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    booking_date: '2025-09-12',
    borrower_name: 'Test User',
    device_type: 'projector', 
    department: '1',
    reason: 'Test booking request for debugging purposes',
    return_date: '2025-09-13',
    return_time: '17:00',
    phone_number: '0743536363'
  })
})
.then(r => r.json())
.then(console.log)
```

## Next Steps

1. **Open browser Developer Tools**
2. **Check Console for errors**  
3. **Try submitting and watch Network tab**
4. **If no API request is made**: Form validation is blocking
5. **If API request fails**: Check response for error details
6. **Most likely**: Existing pending request is blocking new submissions

The backend is working correctly, so this is definitely a frontend JavaScript/validation issue.
