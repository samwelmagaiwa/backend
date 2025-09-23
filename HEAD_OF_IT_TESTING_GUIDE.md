# 🎯 Head of IT Workflow - Complete Testing Guide

## 📋 Overview
This guide provides step-by-step instructions for testing the complete Head of IT workflow system, from backend API to frontend Vue.js components.

## 🔧 Setup Verification

### ✅ Backend Setup Complete
- [x] HeadOfItController created with all endpoints
- [x] TaskAssignment and UserCombinedAccessRequest models created
- [x] Database migrations run successfully
- [x] API routes configured and protected
- [x] File storage configured for signatures
- [x] Test data seeded successfully

### 📊 Test Data Created

#### 👥 ICT Officers (5 total)
```
• Senior ICT Officer (ict.officer@mnh.go.tz) - PF1289 [Existing]
• Michael Thompson (michael.thompson@hospital.gov) - ICT001 [New]
• Sarah Chen (sarah.chen@hospital.gov) - ICT002 [New]  
• James Rodriguez (james.rodriguez@hospital.gov) - ICT003 [New]
• Emily Johnson (emily.johnson@hospital.gov) - ICT004 [New]
```
**Password for new officers:** `password123`

#### 📝 Test Requests (4 total - Ready for Head of IT approval)
```
• REQ-000001 - Dr. Patricia Williams (Jeeva + Wellsoft Access - Permanent)
• REQ-000002 - Robert Davis (Internet Access - Permanent)
• REQ-000003 - Dr. Linda Garcia (Wellsoft + Internet - Temporary 6 months)
• [1 existing request] - David Selemani
```

## 🧪 Frontend Component Testing

### 1. HeadOfItRequestList.vue Component

**Purpose:** Display pending requests awaiting Head of IT approval

**Test Steps:**
1. **Navigate to Head of IT Dashboard**
   ```
   URL: /head-of-it/dashboard (or wherever the component is mounted)
   ```

2. **Verify Component Loading**
   - [ ] Component loads without errors
   - [ ] Loading spinner appears initially
   - [ ] Data fetches from `/api/head-of-it/pending-requests`

3. **Verify Request Display**
   - [ ] Shows 4 requests in the table
   - [ ] Displays correct columns: S/N, Request ID, Staff Name, Department, Request Types, Status, Actions
   - [ ] Request IDs: REQ-000001, REQ-000002, REQ-000003, plus 1 existing
   - [ ] Status shows "ICT Director Approved" for all requests

4. **Test Search Functionality**
   - [ ] Search by staff name (try "Patricia")
   - [ ] Search by request ID (try "REQ-000001")
   - [ ] Search by department name

5. **Test Filter Functionality**
   - [ ] Filter by request type (Jeeva, Wellsoft, Internet)
   - [ ] Filter by access type (Permanent, Temporary)
   - [ ] Date range filter

6. **Test Actions**
   - [ ] "View Details" button opens ProcessRequest component
   - [ ] "Process Request" button opens ProcessRequest component

### 2. ProcessRequest.vue Component

**Purpose:** Allow Head of IT to approve or reject individual requests

**Test Steps:**
1. **Open Request Details**
   - Click "View Details" or "Process Request" from the list
   - URL should show: `/head-of-it/process-request/{id}`

2. **Verify Request Information Display**
   - [ ] Shows all request details (staff name, department, position)
   - [ ] Displays requested services correctly
   - [ ] Shows request types (Jeeva, Wellsoft, Internet)
   - [ ] Shows approval history (HOD, Divisional, ICT Director)

3. **Test Signature Upload**
   - [ ] Signature upload field appears
   - [ ] Accepts PNG, JPG, JPEG files
   - [ ] Rejects files over 5MB
   - [ ] Shows preview of uploaded signature
   - [ ] Validates that signature is required

4. **Test Approval Process**
   - [ ] Upload a test signature (PNG/JPG file)
   - [ ] Click "Approve Request" button
   - [ ] Success message appears
   - [ ] Shows "Select ICT Officer" button
   - [ ] Request status updates to "head_of_it_approved"

5. **Test Rejection Process**
   - [ ] Upload a test signature
   - [ ] Click "Reject Request" button  
   - [ ] Rejection modal opens
   - [ ] Enter rejection reason (minimum 10 characters)
   - [ ] Confirm rejection
   - [ ] Success message appears
   - [ ] Request status updates to "head_of_it_rejected"

### 3. SelectIctOfficer.vue Component

**Purpose:** Allow Head of IT to assign approved requests to ICT Officers

**Test Steps:**
1. **Navigate to ICT Officer Selection**
   - After approving a request, click "Select ICT Officer" button
   - URL should show: `/head-of-it/select-ict-officer/{requestId}`

2. **Verify ICT Officers Display**
   - [ ] Shows 5 ICT Officers in the table
   - [ ] Displays columns: S/N, Full Name, PF Number, Phone, Status, Actions
   - [ ] Officers shown: Michael Thompson, Sarah Chen, James Rodriguez, Emily Johnson, Senior ICT Officer
   - [ ] Status shows "Available", "Assigned", or "Busy" based on workload

3. **Test Search and Filter**
   - [ ] Search by officer name (try "Michael")
   - [ ] Search by PF number (try "ICT001")
   - [ ] Filter by status (Available, Assigned, Busy)

4. **Test Task Assignment**
   - [ ] Click "Assign Task" for an available officer
   - [ ] Confirmation modal appears with officer details
   - [ ] Confirm assignment
   - [ ] Success message shows with assignment details
   - [ ] Officer status updates to show new assignment

5. **Test Progress and Cancel Features**
   - [ ] "View Progress" button works (may show placeholder)
   - [ ] "Cancel Task" button works for assigned tasks
   - [ ] Cancel confirmation modal appears
   - [ ] Task cancellation updates officer availability

## 🔄 Complete Workflow Testing

### End-to-End Test Scenario
1. **Start with Request List**
   - Navigate to Head of IT dashboard
   - Verify 4 pending requests are visible

2. **Process a Request (Approval)**
   - Click on "REQ-000001 - Dr. Patricia Williams"
   - Review request details
   - Upload signature image
   - Click "Approve Request"
   - Verify success message
   - Click "Select ICT Officer"

3. **Assign to ICT Officer**
   - Verify 5 ICT Officers are listed
   - Select "Michael Thompson" (should show as Available)
   - Click "Assign Task"
   - Confirm assignment in modal
   - Verify success message
   - Check that Michael's status updates

4. **Verify Database Changes**
   ```bash
   # Check request status update
   php artisan tinker --execute="use App\Models\UserAccess; echo UserAccess::find(1)->status;"
   
   # Check task assignment creation
   php artisan tinker --execute="use App\Models\TaskAssignment; echo TaskAssignment::count();"
   
   # Check ICT Officer workload
   php artisan tinker --execute="use App\Models\User; echo User::where('email', 'michael.thompson@hospital.gov')->first()->getCurrentWorkload();"
   ```

## 🐛 Common Issues and Solutions

### API Endpoint Issues
```javascript
// Check if API endpoints are properly configured
console.log('API Base URL:', process.env.VUE_APP_API_URL || 'http://localhost:8000/api')

// Verify authentication token is being sent
// Check browser Network tab for requests to /api/head-of-it/*
```

### Component Mounting Issues
```vue
<!-- Ensure components are properly imported and registered -->
<template>
  <HeadOfItRequestList v-if="currentView === 'list'" />
  <ProcessRequest v-if="currentView === 'process'" :requestId="selectedRequestId" />
  <SelectIctOfficer v-if="currentView === 'select'" :requestId="approvedRequestId" />
</template>
```

### File Upload Issues
```javascript
// Check if signature upload is working
const formData = new FormData()
formData.append('signature', signatureFile)
formData.append('_method', 'POST') // Important for Laravel

// Verify content-type is multipart/form-data
```

## 📱 UI/UX Testing Checklist

### Responsiveness
- [ ] Components work on desktop (1920x1080)
- [ ] Components work on tablet (768px width)
- [ ] Components work on mobile (375px width)
- [ ] Tables are scrollable on small screens

### Accessibility
- [ ] All buttons have proper labels
- [ ] Form inputs have labels and validation messages
- [ ] Loading states are announced to screen readers
- [ ] Error messages are clearly visible

### Performance
- [ ] Initial page load is under 3 seconds
- [ ] API requests show loading indicators
- [ ] Large file uploads show progress
- [ ] Tables with many rows render efficiently

## 🎯 Success Criteria

### ✅ Must Pass
- [ ] All 4 test requests load correctly
- [ ] All 5 ICT Officers display properly
- [ ] Signature upload and validation works
- [ ] Request approval creates database records
- [ ] Task assignment creates TaskAssignment record
- [ ] ICT Officer receives notification
- [ ] Officer workload status updates correctly

### 🎨 Nice to Have
- [ ] Smooth animations and transitions
- [ ] Toast notifications for all actions
- [ ] Keyboard navigation support
- [ ] Export functionality works
- [ ] Print-friendly views

## 🚀 Next Steps After Testing

1. **Frontend Server Setup**
   ```bash
   cd frontend
   npm install
   npm run serve
   # Navigate to http://localhost:8080
   ```

2. **Login as Head of IT User**
   - Create a Head of IT user account
   - Assign 'head_of_it' role
   - Login and navigate to dashboard

3. **Production Deployment**
   - Configure environment variables
   - Set up file storage permissions
   - Configure email notifications
   - Set up database backups
   - Configure SSL certificates

## 📞 Support
If you encounter issues during testing:
- Check browser console for JavaScript errors
- Review Laravel logs: `storage/logs/laravel.log`
- Verify database connections
- Check API endpoint authentication

---
**Testing Status:** ✅ Backend Ready | 🔄 Frontend Testing Required
**Last Updated:** September 23, 2025
