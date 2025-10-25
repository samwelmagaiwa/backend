<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\UserAccess;

class NewAccessRequestNotification extends Notification
{
    use Queueable;

    protected $request;
    protected $requestId;
    protected $staffName;
    protected $department;
    protected $requestType;

    /**
     * Create a new notification instance.
     */
    public function __construct(UserAccess $request)
    {
        $this->request = $request;
        $this->requestId = 'REQ-' . str_pad($request->id, 6, '0', STR_PAD_LEFT);
        $this->staffName = $request->staff_name;
        $this->department = $request->department->name ?? 'Unknown';
        
        // Determine request type
        $types = [];
        if ($request->jeeva_access) $types[] = 'Jeeva';
        if ($request->wellsoft_access) $types[] = 'Wellsoft';
        if ($request->internet_access) $types[] = 'Internet';
        $this->requestType = implode(' & ', $types) ?: 'Access';
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'request_id' => $this->request->id,
            'request_number' => $this->requestId,
            'staff_name' => $this->staffName,
            'department' => $this->department,
            'request_type' => $this->requestType,
            'status' => 'pending',
            'message' => "New {$this->requestType} request from {$this->staffName} ({$this->department})",
            'action_url' => "/hod/combined-access-requests/{$this->request->id}",
            'created_at' => $this->request->created_at->toDateTimeString()
        ];
    }
}
