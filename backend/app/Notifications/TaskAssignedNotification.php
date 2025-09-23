<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;
use App\Models\UserCombinedAccessRequest;
use App\Models\TaskAssignment;

class TaskAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $accessRequest;
    protected $taskAssignment;

    /**
     * Create a new notification instance.
     */
    public function __construct(UserCombinedAccessRequest $accessRequest, TaskAssignment $taskAssignment)
    {
        $this->accessRequest = $accessRequest;
        $this->taskAssignment = $taskAssignment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('New Task Assigned: Access Request Implementation')
                    ->greeting('Hello ' . $notifiable->name . ',')
                    ->line('A new task has been assigned to you for implementation.')
                    ->line('**Request Details:**')
                    ->line('Request ID: ' . $this->accessRequest->request_id)
                    ->line('Staff Name: ' . $this->accessRequest->staff_name)
                    ->line('Department: ' . $this->accessRequest->department_name)
                    ->line('Request Types: ' . implode(', ', $this->accessRequest->request_types ?? []))
                    ->line('**Task Details:**')
                    ->line('Priority: ' . ucfirst($this->taskAssignment->priority))
                    ->line('Estimated Completion: ' . $this->taskAssignment->estimated_completion?->format('M d, Y'))
                    ->line('Description: ' . $this->taskAssignment->description)
                    ->action('View Task Details', url('/ict-officer/dashboard'))
                    ->line('Please log in to your dashboard to start working on this task.')
                    ->line('If you have any questions, please contact the Head of IT.');
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'task_assigned',
            'title' => 'New Task Assigned',
            'message' => 'You have been assigned a new task for access request implementation.',
            'data' => [
                'task_assignment_id' => $this->taskAssignment->id,
                'request_id' => $this->accessRequest->id,
                'request_reference' => $this->accessRequest->request_id,
                'staff_name' => $this->accessRequest->staff_name,
                'department' => $this->accessRequest->department_name,
                'request_types' => $this->accessRequest->request_types,
                'priority' => $this->taskAssignment->priority,
                'estimated_completion' => $this->taskAssignment->estimated_completion?->toISOString(),
                'assigned_at' => $this->taskAssignment->assigned_at->toISOString(),
                'assigned_by' => [
                    'id' => $this->taskAssignment->assignedBy->id,
                    'name' => $this->taskAssignment->assignedBy->name
                ]
            ],
            'action_url' => '/ict-officer/dashboard',
            'read_at' => null,
            'created_at' => now()->toISOString()
        ];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'task_assigned',
            'task_assignment_id' => $this->taskAssignment->id,
            'request_id' => $this->accessRequest->id,
            'staff_name' => $this->accessRequest->staff_name,
            'department' => $this->accessRequest->department_name,
            'priority' => $this->taskAssignment->priority
        ];
    }

    /**
     * Determine which queues the notification should be sent on.
     */
    public function viaQueues(): array
    {
        return [
            'mail' => 'emails',
            'database' => 'notifications'
        ];
    }

    /**
     * Get the notification's priority.
     */
    public function getPriority(): string
    {
        return match($this->taskAssignment->priority) {
            'urgent' => 'high',
            'high' => 'high',
            'normal' => 'medium',
            'low' => 'low',
            default => 'medium'
        };
    }
}
