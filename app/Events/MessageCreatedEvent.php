<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageCreatedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Message $message;

    public int $from;

    public int $to;

    public function __construct(Message $message)
    {
        $this->message = $message;

        $this->from = $message->user_id;
        $this->to = $message
            ->chatRoom
            ->users()
            ->where('users.id', '!=', $message->user_id)
            ->first()
            ->id;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->from . '.chat'),
            new PrivateChannel('user.' . $this->to . '.chat'),
        ];
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
