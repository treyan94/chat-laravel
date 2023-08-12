<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChatRoomStoreRequest;
use App\Models\ChatRoom;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatRoomController extends Controller
{

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
