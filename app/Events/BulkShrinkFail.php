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

class BulkShrinkFail
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
     * @var integer|string
     */
    public $statusCode;

    /**
     * @var string
     */
    public $message;


    /**
     * Create a new event instance.
     *
     * @param $statusCode
     * @param BulkShrink $bulkShrink
     * @param User $user
     * @param string $message
     * @internal param BulkShrink $shrink
     */
    public function __construct($statusCode, BulkShrink $bulkShrink, User $user, $message = "")
    {

        $this->bulkShrink = $bulkShrink;
        $this->user = $user;
        $this->statusCode = $statusCode;
        $this->message = $message;
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
