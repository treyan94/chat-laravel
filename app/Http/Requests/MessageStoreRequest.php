<?php

namespace App\Http\Requests;

use Auth;
use Illuminate\Foundation\Http\FormRequest;

class MessageStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'chat_room_id' => ['required', 'exists:chat_rooms,id'],
            'body' => ['required'],
        ];
    }

    public function authorize(): bool
    {
        return $this->user()?->chatRooms()->where('chat_room_id', $this->input('chat_room_id'))->exists();
    }
}
