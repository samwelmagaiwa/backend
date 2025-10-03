<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ApprovalStatusChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $request;
    public $requestType;
    public $oldStatus;
    public $newStatus;
    public $approvedBy;
    public $reason;
    public $additionalNotifyUsers;
    public $metadata;

    /**
     * Create a new event instance.
     *
     * @param User $user The user whose request status changed
     * @param mixed $request The request model
     * @param string $requestType Type of request (jeeva_access, wellsoft_access, etc.)
     * @param string $oldStatus Previous status
     * @param string $newStatus New status (approved, rejected, pending, etc.)
     * @param User|null $approvedBy User who made the status change
     * @param string|null $reason Reason for rejection or additional notes
     * @param array $additionalNotifyUsers Additional users to notify (like IT staff for approved requests)
     * @param array $metadata Additional data for the notification
     */
    public function __construct(
        User $user, 
        $request, 
        string $requestType, 
        string $oldStatus, 
        string $newStatus, 
        User $approvedBy = null, 
        string $reason = null,
        array $additionalNotifyUsers = [],
        array $metadata = []
    ) {
        $this->user = $user;
        $this->request = $request;
        $this->requestType = $requestType;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        $this->approvedBy = $approvedBy;
        $this->reason = $reason;
        $this->additionalNotifyUsers = $additionalNotifyUsers;
        $this->metadata = $metadata;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('approval-status-' . $this->user->id),
        ];
    }

    /**
     * Convert event to array for logging purposes
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'request_id' => $this->request->id ?? null,
            'request_type' => $this->requestType,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'approved_by' => $this->approvedBy ? [
                'id' => $this->approvedBy->id,
                'name' => $this->approvedBy->name
            ] : null,
            'reason' => $this->reason,
            'additional_notify_count' => count($this->additionalNotifyUsers),
            'metadata' => $this->metadata,
            'timestamp' => now()->toISOString()
        ];
    }
}