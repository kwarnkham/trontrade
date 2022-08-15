<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserBalanceUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    // public $afterCommit = true;

    public $balance;
    public $token_id;
    public $user_id;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($balance, $token_id, $user_id)
    {
        $this->balance = $balance;
        $this->token_id = $token_id;
        $this->user_id = $user_id;
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'token_id' => $this->token_id,
            'balance' => $this->balance
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return [
            new PrivateChannel('socket.' . $this->user_id),
        ];
    }
}
