<?php

namespace App\Events;

use App\BulkShrink;
use App\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class BulkShrinkVerified
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var BulkShrink
     */
    public $bulkShrink;

    /**
     * @var User
     */
    public $user;

    /**
     * Create a new event instance.
     *
     * @param BulkShrink $bulkShrink
     * @param User $user
     */
    public function __construct(BulkShrink $bulkShrink, User $user)
    {
        $this->bulkShrink = $bulkShrink;
        $this->user = $user;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
