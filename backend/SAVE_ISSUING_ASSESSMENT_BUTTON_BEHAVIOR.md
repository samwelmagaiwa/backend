# Save Issuing Assessment Button Behavior

## Overview
The "Save Issuing Assessment" button now has enhanced state management that only enables the button for requests with pending status, and provides clear visual feedback for different states.

## Button States

### 1. **Enabled State (Green - Ready to Save)**
- **Condition**: Request has pending status AND all assessment fields are complete
- **Appearance**: Blue gradient background, white text
- **Text**: "Save Issuing Assessment"
- **Icon**: Save icon (fas fa-save)
- **Tooltip**: "Save device condition assessment and issue device"

### 2. **Disabled - Non-Pending Status (Gray - Muted)**
- **Condition**: Request status is NOT pending (approved, rejected, etc.)
- **Appearance**: Dark gray background, gray text, reduced opacity
- **Text**: "Cannot Issue ({status} Status)" 
- **Icon**: Lock icon (fas fa-lock)
- **Tooltip**: "Device can only be issued for pending requests. Current status: {status}"
- **Examples**: 
  - "Cannot Issue (approved Status)"
  - "Cannot Issue (rejected Status)"

### 3. **Disabled - Incomplete Assessment (Yellow)**
- **Condition**: Assessment fields are not complete
- **Appearance**: Yellow-brown background, yellow text, reduced opacity
- **Text**: "Complete Assessment First"
- **Icon**: Warning icon (fas fa-exclamation-triangle)
- **Tooltip**: "Please fill in all required assessment fields"

### 4. **Processing State (Blue)**
- **Condition**: Assessment is being saved
- **Appearance**: Blue background, white text, slightly transparent
- **Text**: "Saving..."
- **Icon**: Spinning loader (fas fa-spinner fa-spin)
- **Tooltip**: "Processing assessment..."

### 5. **Completed State (Green)**
- **Condition**: Assessment has been successfully saved
- **Appearance**: Green background, white text
- **Text**: "Assessment Saved"
- **Icon**: Check circle (fas fa-check-circle)
- **Tooltip**: "Device assessment has been saved successfully"

## Status Priority Logic

1. **Saved Assessment**: If already saved → Show "Assessment Saved" (green)
2. **Non-Pending Status**: If not pending → Show "Cannot Issue" (muted gray)
3. **Incomplete Assessment**: If fields missing → Show "Complete Assessment First" (yellow)
4. **Processing**: If saving → Show "Saving..." (blue)
5. **Ready to Save**: All conditions met → Show "Save Issuing Assessment" (blue gradient)

## Technical Implementation

### Computed Property: `issuingAssessmentButtonState()`
```javascript
issuingAssessmentButtonState() {
  const isPendingRequest = this.request.ict_approve === 'pending' || 
                          this.request.ict_status === 'pending' ||
                          (!this.request.ict_approve && !this.request.ict_status)
  
  // Returns object with: enabled, text, icon, classes, tooltip
}
```

### Template Usage
```vue
<button
  :disabled="!issuingAssessmentButtonState.enabled"
  :title="issuingAssessmentButtonState.tooltip"
  :class="issuingAssessmentButtonState.classes"
>
  <i :class="issuingAssessmentButtonState.icon"></i>
  {{ issuingAssessmentButtonState.text }}
</button>
```

## Testing Scenarios

### Test Case 1: Pending Request with Complete Assessment
- **Setup**: Request with `ict_approve: 'pending'`, all fields filled
- **Expected**: Button enabled, blue gradient, "Save Issuing Assessment"

### Test Case 2: Approved Request
- **Setup**: Request with `ict_approve: 'approved'`
- **Expected**: Button disabled, gray, "Cannot Issue (approved Status)"

### Test Case 3: Rejected Request
- **Setup**: Request with `ict_approve: 'rejected'`
- **Expected**: Button disabled, gray, "Cannot Issue (rejected Status)"

### Test Case 4: Pending with Incomplete Assessment
- **Setup**: Request pending but missing physical_condition field
- **Expected**: Button disabled, yellow, "Complete Assessment First"

### Test Case 5: Already Saved Assessment
- **Setup**: Assessment previously saved successfully
- **Expected**: Button disabled, green, "Assessment Saved"

## Benefits

1. **Clear Visual Feedback**: Users immediately understand why the button is disabled
2. **Status-Specific Messaging**: Different messages for different blocking conditions  
3. **Improved UX**: Tooltip provides additional context
4. **Consistent State Management**: Centralized button state logic
5. **Workflow Guidance**: Users know exactly what needs to be done to proceed

## File Changes
- `/frontend/src/components/views/ict-approval/RequestDetails.vue`
  - Updated `canSaveIssuingAssessment()` computed property
  - Added `issuingAssessmentButtonState()` computed property  
  - Modified button template to use new state management
