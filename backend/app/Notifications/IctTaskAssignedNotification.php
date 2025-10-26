<?php

namespace App\Notifications;

use App\Models\UserAccess;
use App\Models\IctTaskAssignment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class IctTaskAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public UserAccess $userAccess;
    public IctTaskAssignment $taskAssignment;

    /**
     * Create a new notification instance.
     */
    public function __construct(UserAccess $userAccess, IctTaskAssignment $taskAssignment)
    {
        $this->userAccess = $userAccess;
        $this->taskAssignment = $taskAssignment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // Send via database, mail, and SMS
        $channels = ['database', 'mail'];
        
        // Add SMS channel if phone number is available
        if ($notifiable->phone) {
            $channels[] = '\App\Channels\SmsChannel';
        }
        
        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = url('/ict-officer/tasks/' . $this->taskAssignment->id);
        
        return (new MailMessage)
            ->subject('New ICT Task Assignment')
            ->line('You have been assigned a new ICT task.')
            ->line('**Staff Details:**')
            ->line('Name: ' . $this->userAccess->staff_name)
            ->line('PF Number: ' . $this->userAccess->pf_number)
            ->line('Department: ' . ($this->userAccess->department->name ?? 'Unknown'))
            ->line('')
            ->line('**Request Type:** ' . implode(', ', (array) $this->userAccess->request_type))
            ->line('**Assignment Notes:** ' . ($this->taskAssignment->assignment_notes ?? 'No additional notes'))
            ->action('View Task Details', $url)
            ->line('Please review and start working on this task as soon as possible.');
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): DatabaseMessage
    {
        return new DatabaseMessage([
            'type' => 'ict_task_assigned',
            'title' => 'New ICT Task Assignment',
            'message' => 'You have been assigned a new ICT task for ' . $this->userAccess->staff_name,
            'data' => [
                'user_access_id' => $this->userAccess->id,
                'task_assignment_id' => $this->taskAssignment->id,
                'staff_name' => $this->userAccess->staff_name,
                'pf_number' => $this->userAccess->pf_number,
                'department' => $this->userAccess->department->name ?? 'Unknown',
                'request_type' => $this->userAccess->request_type,
                'assignment_notes' => $this->taskAssignment->assignment_notes,
                'assigned_at' => $this->taskAssignment->assigned_at,
                'assigned_by' => $this->taskAssignment->assignedBy->name
            ],
            'action_url' => '/ict-officer/tasks/' . $this->taskAssignment->id
        ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'ict_task_assigned',
            'title' => 'New ICT Task Assignment',
            'message' => 'You have been assigned a new ICT task for ' . $this->userAccess->staff_name,
            'user_access_id' => $this->userAccess->id,
            'task_assignment_id' => $this->taskAssignment->id,
            'staff_name' => $this->userAccess->staff_name,
            'pf_number' => $this->userAccess->pf_number,
            'department' => $this->userAccess->department->name ?? 'Unknown',
            'assigned_at' => $this->taskAssignment->assigned_at,
            'action_url' => '/ict-officer/tasks/' . $this->taskAssignment->id
        ];
    }

    /**
     * Get the SMS representation of the notification.
     *
     * @param object $notifiable
     * @return string
     */
    public function toSms(object $notifiable): string
    {
        $staffName = $this->userAccess->staff_name;
        $pfNumber = $this->userAccess->pf_number;
        $requestTypes = is_array($this->userAccess->request_type) 
            ? implode(', ', $this->userAccess->request_type)
            : $this->userAccess->request_type;
        
        return "New ICT Task Assignment: You have been assigned to grant access for {$staffName} (PF: {$pfNumber}). Request Type: {$requestTypes}. Please log in to start implementation.";
    }
}
