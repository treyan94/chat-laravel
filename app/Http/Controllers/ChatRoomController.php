<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChatRoomStoreRequest;
use App\Models\ChatRoom;
use Auth;
use Illuminate\Http\JsonResponse;

class ChatRoomController extends Controller
{
    public function store(ChatRoomStoreRequest $request): JsonResponse
    {
        $chatRoom = ChatRoom::create([
            'name' => $request->input('name'),
        ]);

        $chatRoom->users()->attach([Auth::id(), $request->input('user_id')]);
        $chatRoom->load('users');

        return new JsonResponse([
            'message' => 'Chat room created successfully',
            'data' => $chatRoom,
        ]);
    }

    public function show(ChatRoom $chatRoom): JsonResponse
    {
        if (!$chatRoom->users()->where('user_id', Auth::id())->exists()) {
            abort(403);
        }

        $chatRoom->load('messages');

        return new JsonResponse([
            'data' => $chatRoom,
        ]);
    }
}
