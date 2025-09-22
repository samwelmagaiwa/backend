<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use App\Models\UserAccess;
use App\Traits\HandlesStatusQueries;

// Bootstrap Laravel application
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== CONTROLLER STATUS MIGRATION TEST ===" . PHP_EOL . PHP_EOL;

// Create a test class to use the trait
class TestStatusQueries {
    use HandlesStatusQueries;
}

$testHelper = new TestStatusQueries();

echo "1. TESTING HandlesStatusQueries TRAIT:" . PHP_EOL;

// Test pending requests for each stage
$stages = ['hod', 'divisional', 'ict_director', 'head_it', 'ict_officer'];

foreach ($stages as $stage) {
    $pendingCount = $testHelper->getPendingRequestsForStage($stage)->count();
    $approvedCount = $testHelper->getApprovedRequestsForStage($stage)->count();
    $rejectedCount = $testHelper->getRejectedRequestsForStage($stage)->count();
    
    echo "Stage: {$stage}" . PHP_EOL;
    echo "  Pending: {$pendingCount}" . PHP_EOL;
    echo "  Approved: {$approvedCount}" . PHP_EOL;
    echo "  Rejected: {$rejectedCount}" . PHP_EOL;
    echo PHP_EOL;
}

echo "2. TESTING SYSTEM STATISTICS:" . PHP_EOL;
$stats = $testHelper->getSystemStatistics();
echo "Total requests: " . $stats['total'] . PHP_EOL;
echo "Requests in progress: " . $stats['pending'] . PHP_EOL;
echo "Completed requests: " . $stats['completed'] . PHP_EOL;
echo "Rejected requests: " . $stats['rejected'] . PHP_EOL;
echo PHP_EOL;

echo "3. TESTING STATUS COLUMN QUERIES:" . PHP_EOL;

// Test requests with rejections
$rejectedCount = $testHelper->getRequestsWithRejections()->count();
echo "Requests with any rejections: {$rejectedCount}" . PHP_EOL;

// Test completed requests
$completedCount = $testHelper->getCompletedRequests()->count();
echo "Fully completed requests: {$completedCount}" . PHP_EOL;

// Test in-progress requests
$inProgressCount = $testHelper->getRequestsInProgress()->count();
echo "Requests in progress: {$inProgressCount}" . PHP_EOL;

echo PHP_EOL;

echo "4. TESTING INDIVIDUAL STATUS COLUMNS:" . PHP_EOL;

// Test each status column individually
$hodPending = UserAccess::where('hod_status', 'pending')->count();
$hodApproved = UserAccess::where('hod_status', 'approved')->count();
$hodRejected = UserAccess::where('hod_status', 'rejected')->count();

echo "HOD Status Distribution:" . PHP_EOL;
echo "  Pending: {$hodPending}" . PHP_EOL;
echo "  Approved: {$hodApproved}" . PHP_EOL;
echo "  Rejected: {$hodRejected}" . PHP_EOL;

$divPending = UserAccess::where('divisional_status', 'pending')->count();
$divApproved = UserAccess::where('divisional_status', 'approved')->count();
$divRejected = UserAccess::where('divisional_status', 'rejected')->count();

echo "Divisional Status Distribution:" . PHP_EOL;
echo "  Pending: {$divPending}" . PHP_EOL;
echo "  Approved: {$divApproved}" . PHP_EOL;
echo "  Rejected: {$divRejected}" . PHP_EOL;

$ictDirPending = UserAccess::where('ict_director_status', 'pending')->count();
$ictDirApproved = UserAccess::where('ict_director_status', 'approved')->count();
$ictDirRejected = UserAccess::where('ict_director_status', 'rejected')->count();

echo "ICT Director Status Distribution:" . PHP_EOL;
echo "  Pending: {$ictDirPending}" . PHP_EOL;
echo "  Approved: {$ictDirApproved}" . PHP_EOL;
echo "  Rejected: {$ictDirRejected}" . PHP_EOL;

$headItPending = UserAccess::where('head_it_status', 'pending')->count();
$headItApproved = UserAccess::where('head_it_status', 'approved')->count();
$headItRejected = UserAccess::where('head_it_status', 'rejected')->count();

echo "Head IT Status Distribution:" . PHP_EOL;
echo "  Pending: {$headItPending}" . PHP_EOL;
echo "  Approved: {$headItApproved}" . PHP_EOL;
echo "  Rejected: {$headItRejected}" . PHP_EOL;

$ictOfficerPending = UserAccess::where('ict_officer_status', 'pending')->count();
$ictOfficerImplemented = UserAccess::where('ict_officer_status', 'implemented')->count();
$ictOfficerRejected = UserAccess::where('ict_officer_status', 'rejected')->count();

echo "ICT Officer Status Distribution:" . PHP_EOL;
echo "  Pending: {$ictOfficerPending}" . PHP_EOL;
echo "  Implemented: {$ictOfficerImplemented}" . PHP_EOL;
echo "  Rejected: {$ictOfficerRejected}" . PHP_EOL;

echo PHP_EOL;

echo "5. TESTING WORKFLOW PROGRESSION LOGIC:" . PHP_EOL;

// Get a sample record and test workflow methods
$sampleRecord = UserAccess::first();
if ($sampleRecord) {
    echo "Testing with record ID: {$sampleRecord->id}" . PHP_EOL;
    echo "Current status columns:" . PHP_EOL;
    echo "  HOD: " . ($sampleRecord->hod_status ?? 'null') . PHP_EOL;
    echo "  Divisional: " . ($sampleRecord->divisional_status ?? 'null') . PHP_EOL;
    echo "  ICT Director: " . ($sampleRecord->ict_director_status ?? 'null') . PHP_EOL;
    echo "  Head IT: " . ($sampleRecord->head_it_status ?? 'null') . PHP_EOL;
    echo "  ICT Officer: " . ($sampleRecord->ict_officer_status ?? 'null') . PHP_EOL;
    echo PHP_EOL;
    
    echo "Model method results:" . PHP_EOL;
    echo "  getCalculatedOverallStatus(): " . $sampleRecord->getCalculatedOverallStatus() . PHP_EOL;
    echo "  getNextPendingStageFromColumns(): " . ($sampleRecord->getNextPendingStageFromColumns() ?? 'none') . PHP_EOL;
    echo "  isWorkflowCompleteByColumns(): " . ($sampleRecord->isWorkflowCompleteByColumns() ? 'yes' : 'no') . PHP_EOL;
    echo "  hasRejectionsInColumns(): " . ($sampleRecord->hasRejectionsInColumns() ? 'yes' : 'no') . PHP_EOL;
    echo "  getWorkflowProgressFromColumns(): " . $sampleRecord->getWorkflowProgressFromColumns() . "%" . PHP_EOL;
}

echo PHP_EOL;

echo "6. TESTING ROLE-BASED FILTERING:" . PHP_EOL;

// Test role-based queries
$hodQueries = $testHelper->getPendingRequestsForStage('hod')->count();
$divisionalQueries = $testHelper->getPendingRequestsForStage('divisional')->count();
$ictDirectorQueries = $testHelper->getPendingRequestsForStage('ict_director')->count();
$headItQueries = $testHelper->getPendingRequestsForStage('head_it')->count();
$ictOfficerQueries = $testHelper->getPendingRequestsForStage('ict_officer')->count();

echo "Role-based pending requests:" . PHP_EOL;
echo "  HOD: {$hodQueries}" . PHP_EOL;
echo "  Divisional Director: {$divisionalQueries}" . PHP_EOL;
echo "  ICT Director: {$ictDirectorQueries}" . PHP_EOL;
echo "  Head IT: {$headItQueries}" . PHP_EOL;
echo "  ICT Officer: {$ictOfficerQueries}" . PHP_EOL;

echo PHP_EOL;

echo "7. TESTING COMPLEX QUERIES:" . PHP_EOL;

// Test complex workflow queries
$readyForDivisional = UserAccess::where('hod_status', 'approved')
    ->where('divisional_status', 'pending')
    ->count();

echo "Requests ready for divisional approval: {$readyForDivisional}" . PHP_EOL;

$readyForIctDirector = UserAccess::where('hod_status', 'approved')
    ->where('divisional_status', 'approved')
    ->where('ict_director_status', 'pending')
    ->count();

echo "Requests ready for ICT Director approval: {$readyForIctDirector}" . PHP_EOL;

$readyForImplementation = UserAccess::where('hod_status', 'approved')
    ->where('divisional_status', 'approved')
    ->where('ict_director_status', 'approved')
    ->where('head_it_status', 'approved')
    ->where('ict_officer_status', 'pending')
    ->count();

echo "Requests ready for implementation: {$readyForImplementation}" . PHP_EOL;

echo PHP_EOL . "=== CONTROLLER STATUS MIGRATION TEST COMPLETED ===" . PHP_EOL;
