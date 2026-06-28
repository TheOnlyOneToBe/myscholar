<?php

namespace Modules\Dashboard\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StudentNotified implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public string $channel,
        public array $notification
    ) {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel($this->channel),
        ];
    }

    public function broadcastAs(): string
    {
        return 'studentNotified';
    }

    public function broadcastWith(): array
    {
        return [
            'notification' => $this->notification,
        ];
    }
}
