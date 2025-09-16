# Debugging Auto-Population Issue

## Steps to Debug:

### 1. Check Browser Console Logs
Open Developer Tools (F12) > Console tab and look for these debug messages:

#### Expected Messages:
```javascript
// User loading messages:
"Current user loaded: {id: X, name: 'User Name', ...}"
"User name from database: User Name"

// Approval section state messages:
"HOD approval editable changed: {isEditable: true/false, currentHodName: '', ...}"

// Auto-population messages:
"Auto-populating hod approver name with user from database: {stage: 'hod', userName: 'User Name', ...}"
"✅ HOD name populated: User Name"
```

### 2. Check for Error Messages
Look for any error messages related to:
- API calls failing
- Authentication issues
- Data access problems

### 3. Manual Console Check
In the browser console, run these commands while on the form page:

```javascript
// Check if component is loaded
console.log('Vue component:', this)

// Check current user data
console.log('Current user:', this.currentUser)

// Check HOD approval editable state
console.log('HOD editable:', this.isHodApprovalEditable)

// Check form data
console.log('HOD name field:', this.form.approvals.hod.name)

// Check approval stage
console.log('Current approval stage:', this.currentApprovalStage)
console.log('Is review mode:', this.isReviewMode)

// Manually trigger auto-population
this.populateApproverName('hod')
```

## Common Issues & Solutions:

### Issue 1: User not loaded
**Symptoms:** `currentUser` is null or undefined
**Solution:** Check authentication and API connectivity

### Issue 2: HOD section not editable
**Symptoms:** `isHodApprovalEditable` returns false
**Solution:** Check if request is at HOD approval stage

### Issue 3: Name field already has value
**Symptoms:** Field already contains a name
**Solution:** Auto-population only works on empty fields

### Issue 4: Wrong approval stage
**Symptoms:** Request is not at HOD approval stage
**Solution:** Check request status and current approval stage
