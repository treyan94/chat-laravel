<?php

namespace App\Events;

use App\Models\Message;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageCreatedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Message $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function broadcastOn(): array
    {
        return $this->message
            ->chatRoom
            ->users
            ->map(fn(User $user) => new PrivateChannel('user.' . $user->id . '.chat'))
            ->toArray();
    }

    public function broadcastWith(): array
    {
        return [
            'message' => $this->message->unsetRelation('chatRoom'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.created';
    }
}
