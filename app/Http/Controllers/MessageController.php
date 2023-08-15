<?php

namespace App\Http\Controllers;

use App\Http\Requests\MessageStoreRequest;
use App\Models\Message;
use Illuminate\Http\JsonResponse;

class MessageController extends Controller
{
    public function store(MessageStoreRequest $request): JsonResponse
    {
        $message = new Message();
        $message->body = $request->input('body');
        $message->user_id = $request->user()->id;
        $message->chat_room_id = $request->input('chat_room_id');

        $message->save();

        if ($message->chatRoom->hasGptUser) {
            $message->chatRoom->addGptMessage();
        }

        return new JsonResponse([
            'data' => $message,
        ]);
    }
}
