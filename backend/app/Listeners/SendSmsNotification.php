<?php

namespace App\Listeners;

use App\Events\ApprovalStatusChanged;
use App\Events\ApprovalRequestSubmitted;
use App\Services\SmsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendSmsNotification implements ShouldQueue
{
    use InteractsWithQueue;

    protected $smsService;

    /**
     * Create the event listener.
     *
     * @param SmsService $smsService
     */
    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Handle the event.
     *
     * @param mixed $event
     * @return void
     */
    public function handle($event)
    {
        try {
            if ($event instanceof ApprovalRequestSubmitted) {
                $this->handleApprovalRequestSubmitted($event);
            } elseif ($event instanceof ApprovalStatusChanged) {
                $this->handleApprovalStatusChanged($event);
            }
        } catch (\Exception $e) {
            Log::error('SMS Notification Listener Error: ' . $e->getMessage(), [
                'event' => get_class($event),
                'event_data' => $event->toArray()
            ]);

            // Re-throw to ensure the job fails and can be retried
            throw $e;
        }
    }

    /**
     * Handle approval request submitted event
     *
     * @param ApprovalRequestSubmitted $event
     * @return void
     */
    protected function handleApprovalRequestSubmitted(ApprovalRequestSubmitted $event)
    {
        Log::info('Processing SMS notification for approval request submitted', [
            'request_id' => $event->request->id,
            'request_type' => $event->requestType,
            'user_id' => $event->user->id
        ]);

        // Notify the requester that their request has been submitted
        $this->smsService->sendApprovalNotification(
            $event->user,
            $event->requestType,
            'pending',
            [
                'reference' => $event->request->reference ?? $event->request->id,
                'submitted_at' => $event->request->created_at->format('Y-m-d H:i')
            ]
        );

        // Notify approvers about the new request
        if (!empty($event->approvers)) {
            $this->smsService->notifyApprovers(
                $event->approvers,
                $event->requestType,
                $event->user,
                [
                    'reference' => $event->request->reference ?? $event->request->id,
                    'submitted_at' => $event->request->created_at->format('Y-m-d H:i')
                ]
            );
        }
    }

    /**
     * Handle approval status changed event
     *
     * @param ApprovalStatusChanged $event
     * @return void
     */
    protected function handleApprovalStatusChanged(ApprovalStatusChanged $event)
    {
        Log::info('Processing SMS notification for approval status change', [
            'request_id' => $event->request->id,
            'request_type' => $event->requestType,
            'old_status' => $event->oldStatus,
            'new_status' => $event->newStatus,
            'user_id' => $event->user->id
        ]);

        // Notify the requester about the status change
        $additionalData = [
            'reference' => $event->request->reference ?? $event->request->id,
            'updated_at' => now()->format('Y-m-d H:i'),
            'reason' => $event->reason ?? 'Not specified'
        ];

        $this->smsService->sendApprovalNotification(
            $event->user,
            $event->requestType,
            $event->newStatus,
            $additionalData
        );

        // If approved and there are additional stakeholders to notify
        if ($event->newStatus === 'approved' && !empty($event->additionalNotifyUsers)) {
            $message = $this->buildAdditionalApprovalMessage(
                $event->user,
                $event->requestType,
                $additionalData
            );

            $this->smsService->sendBulkSms(
                $event->additionalNotifyUsers,
                $message,
                'approval_notification'
            );
        }
    }

    /**
     * Build additional approval notification message
     *
     * @param $user
     * @param string $requestType
     * @param array $additionalData
     * @return string
     */
    protected function buildAdditionalApprovalMessage($user, string $requestType, array $additionalData): string
    {
        $template = "ACCESS APPROVED: {requester} has been granted {type} access. Reference: {ref}. Please provide necessary assistance. - MNH IT";

        return str_replace([
            '{requester}',
            '{type}',
            '{ref}'
        ], [
            $user->name,
            ucfirst(str_replace('_', ' ', $requestType)),
            $additionalData['reference']
        ], $template);
    }

    /**
     * The job failed to process.
     *
     * @param mixed $event
     * @param \Throwable $exception
     * @return void
     */
    public function failed($event, $exception)
    {
        Log::error('SMS Notification job failed permanently', [
            'event' => get_class($event),
            'exception' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}