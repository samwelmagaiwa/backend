# Adding SMS Notification Status to Request Details Page

## File to Update
`frontend/src/components/views/requests/InternalAccessDetails.vue`

---

## Step 1: Add SMS Status Badge Component

Add this after line 351 (inside the HOD Approval Card, before closing `</div>`):

```vue
<!-- SMS Notification Status -->
<div class="mt-2 pt-2 border-t border-blue-400/30">
  <div class="flex items-center justify-between">
    <span class="text-xs text-blue-200">SMS Status:</span>
    <div class="flex items-center gap-1">
      <!-- SMS to HOD -->
      <div 
        v-if="smsStatus && smsStatus.hod" 
        :class="getSmsStatusClass(smsStatus.hod.status)"
        class="px-2 py-0.5 rounded-full text-xs font-medium flex items-center gap-1"
        :title="`SMS to HOD: ${smsStatus.hod.status}${smsStatus.hod.sent_at ? ' at ' + formatDateTime(smsStatus.hod.sent_at) : ''}`"
      >
        <i :class="getSmsStatusIcon(smsStatus.hod.status)" class="text-xs"></i>
        <span>{{ getSmsStatusText(smsStatus.hod.status) }}</span>
      </div>
      <span v-else class="text-xs text-gray-400">Pending</span>
    </div>
  </div>
  <div class="flex items-center justify-between mt-1">
    <span class="text-xs text-blue-200">Your Notification:</span>
    <div class="flex items-center gap-1">
      <!-- SMS to Requester -->
      <div 
        v-if="smsStatus && smsStatus.requester" 
        :class="getSmsStatusClass(smsStatus.requester.status)"
        class="px-2 py-0.5 rounded-full text-xs font-medium flex items-center gap-1"
        :title="`SMS to you: ${smsStatus.requester.status}${smsStatus.requester.sent_at ? ' at ' + formatDateTime(smsStatus.requester.sent_at) : ''}`"
      >
        <i :class="getSmsStatusIcon(smsStatus.requester.status)" class="text-xs"></i>
        <span>{{ getSmsStatusText(smsStatus.requester.status) }}</span>
      </div>
      <span v-else class="text-xs text-gray-400">Pending</span>
    </div>
  </div>
</div>
```

---

## Step 2: Add SMS Status to Data (in `setup()`)

Find the `setup()` function and add this near the top with other `ref()` declarations:

```javascript
const smsStatus = ref(null)
```

---

## Step 3: Add Method to Load SMS Status

Add this method after `loadRequestData()` method (around line 762):

```javascript
const loadSmsStatus = async () => {
  try {
    if (requestId.value && requestType.value === 'combined_access') {
      const response = await apiClient.get(`/api/user-access/${requestId.value}/sms-status`)
      if (response.data.success) {
        smsStatus.value = response.data.data
        console.log('✅ SMS Status loaded:', smsStatus.value)
      }
    }
  } catch (error) {
    console.error('❌ Error loading SMS status:', error)
  }
}
```

---

## Step 4: Add SMS Status Helper Methods

Add these methods after `loadSmsStatus()`:

```javascript
const getSmsStatusClass = (status) => {
  switch (status) {
    case 'sent':
      return 'bg-green-500/20 text-green-300 border border-green-400/30'
    case 'failed':
      return 'bg-red-500/20 text-red-300 border border-red-400/30'
    case 'pending':
    default:
      return 'bg-yellow-500/20 text-yellow-300 border border-yellow-400/30'
  }
}

const getSmsStatusIcon = (status) => {
  switch (status) {
    case 'sent':
      return 'fas fa-check-circle'
    case 'failed':
      return 'fas fa-times-circle'
    case 'pending':
    default:
      return 'fas fa-clock'
  }
}

const getSmsStatusText = (status) => {
  switch (status) {
    case 'sent':
      return 'Delivered'
    case 'failed':
      return 'Failed'
    case 'pending':
    default:
      return 'Pending'
  }
}
```

---

## Step 5: Call loadSmsStatus in onMounted

Find the `onMounted()` hook and add the call to load SMS status:

```javascript
onMounted(() => {
  requireRole([ROLES.STAFF])
  loadRequestData()
  loadSmsStatus() // Add this line
})
```

---

## Step 6: Add apiClient Import

At the top of the script section, add apiClient to imports:

```javascript
import apiClient from '@/services/apiClient'
```

---

## Step 7: Return New Variables and Methods

In the `return` statement at the end of `setup()`, add:

```javascript
return {
  // ... existing returns ...
  smsStatus,
  getSmsStatusClass,
  getSmsStatusIcon,
  getSmsStatusText,
  loadSmsStatus
}
```

---

## For Divisional, DICT, Head IT Cards

Repeat similar SMS status display for other approval cards by adding after their respective sections:

### Divisional Director Card

Add similar SMS status section but check `smsStatus.divisional`:

```vue
<!-- SMS Notification Status - Divisional -->
<div class="mt-2 pt-2 border-t border-blue-400/30">
  <div class="flex items-center justify-between">
    <span class="text-xs text-blue-200">SMS to Divisional:</span>
    <div 
      v-if="smsStatus && smsStatus.divisional" 
      :class="getSmsStatusClass(smsStatus.divisional.status)"
      class="px-2 py-0.5 rounded-full text-xs font-medium flex items-center gap-1"
    >
      <i :class="getSmsStatusIcon(smsStatus.divisional.status)" class="text-xs"></i>
      <span>{{ getSmsStatusText(smsStatus.divisional.status) }}</span>
    </div>
    <span v-else class="text-xs text-gray-400">Pending</span>
  </div>
</div>
```

### ICT Director Card

```vue
<!-- SMS Notification Status - DICT -->
<div class="mt-2 pt-2 border-t border-teal-400/30">
  <div class="flex items-center justify-between">
    <span class="text-xs text-teal-200">SMS to DICT:</span>
    <div 
      v-if="smsStatus && smsStatus.ict_director" 
      :class="getSmsStatusClass(smsStatus.ict_director.status)"
      class="px-2 py-0.5 rounded-full text-xs font-medium flex items-center gap-1"
    >
      <i :class="getSmsStatusIcon(smsStatus.ict_director.status)" class="text-xs"></i>
      <span>{{ getSmsStatusText(smsStatus.ict_director.status) }}</span>
    </div>
    <span v-else class="text-xs text-gray-400">Pending</span>
  </div>
</div>
```

### Head of IT Card

```vue
<!-- SMS Notification Status - Head IT -->
<div class="mt-2 pt-2 border-t border-purple-400/30">
  <div class="flex items-center justify-between">
    <span class="text-xs text-purple-200">SMS to Head IT:</span>
    <div 
      v-if="smsStatus && smsStatus.head_it" 
      :class="getSmsStatusClass(smsStatus.head_it.status)"
      class="px-2 py-0.5 rounded-full text-xs font-medium flex items-center gap-1"
    >
      <i :class="getSmsStatusIcon(smsStatus.head_it.status)" class="text-xs"></i>
      <span>{{ getSmsStatusText(smsStatus.head_it.status) }}</span>
    </div>
    <span v-else class="text-xs text-gray-400">Pending</span>
  </div>
</div>
```

---

## Visual Result

After implementation, users will see:

```
Head of Department
Approved

SMS Status: ✅ Delivered
Your Notification: ✅ Delivered
```

Or:

```
Head of Department
Pending

SMS Status: ⏳ Pending
Your Notification: ⏳ Pending
```

---

## Backend API Endpoint Still Needed!

Remember to add the API endpoint in your Laravel backend:

**File:** `app/Http/Controllers/Api/v1/UserAccessController.php`

```php
public function getSmsNotificationStatus($id)
{
    $request = UserAccess::findOrFail($id);
    $sms = app(\App\Services\SmsModule::class);
    
    return response()->json([
        'success' => true,
        'data' => $sms->getSmsNotificationStatus($request)
    ]);
}
```

**Route:** Add to `routes/api.php`

```php
Route::get('/user-access/{id}/sms-status', [UserAccessController::class, 'getSmsNotificationStatus'])
    ->middleware('auth:sanctum');
```

---

## Testing

1. Run the migration: `php artisan migrate`
2. Submit a request
3. Have HOD approve it  
4. Check the request details page - you should see SMS status badges!

---

**Simple. Clean. User-Friendly.** 📱✨
