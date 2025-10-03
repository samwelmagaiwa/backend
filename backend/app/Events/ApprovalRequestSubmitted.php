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

class ApprovalRequestSubmitted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $request;
    public $requestType;
    public $approvers;
    public $metadata;

    /**
     * Create a new event instance.
     *
     * @param User $user The user who submitted the request
     * @param mixed $request The request model (could be various types)
     * @param string $requestType Type of request (jeeva_access, wellsoft_access, internet_access, etc.)
     * @param array $approvers List of users who need to approve this request
     * @param array $metadata Additional data for the notification
     */
    public function __construct(User $user, $request, string $requestType, array $approvers = [], array $metadata = [])
    {
        $this->user = $user;
        $this->request = $request;
        $this->requestType = $requestType;
        $this->approvers = $approvers;
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
            new PrivateChannel('approval-requests'),
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
            'approvers_count' => count($this->approvers),
            'metadata' => $this->metadata,
            'timestamp' => now()->toISOString()
        ];
    }
}