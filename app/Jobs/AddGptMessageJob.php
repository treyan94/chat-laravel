<?php

namespace App\Jobs;

use App\Models\ChatRoom;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use OpenAI\Laravel\Facades\OpenAI;

class AddGptMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue;

    protected ChatRoom $chatRoom;

    public function __construct(ChatRoom $chatRoom)
    {
        $this->chatRoom = $chatRoom->unsetRelations();
    }

    public function handle(): void
    {
        $messages = [
            [
                'role' => 'system',
                'content' => 'You are an assistant chat bot that is created for testing purposes.'
            ],
        ];

        $gptUserId = User::getChatGptUserId();

        foreach ($this->chatRoom->messages as $message) {
            $messages[] = [
                'role' => $message->user_id === $gptUserId ? 'assistant' : 'user',
                'content' => $message->body
            ];
        }

        $response = OpenAI::chat()->create([
            'model' => config('openai.model'),
            'messages' => $messages
        ])->toArray();

        if (empty($response['choices'])) {
            return;
        }


        $choice = $response['choices'][0];
        $content = $choice['message']['content'];

        $message = new Message();
        $message->body = $content;
        $message->user_id = $gptUserId;
        $message->chat_room_id = $this->chatRoom->id;

        $message->save();
    }
}
