<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChatRoomAddUsersRequest extends FormRequest
{
    public function rules(): array
    {
        $chatRoom = $this->route('chatRoom');
        $existingUsers = $chatRoom?->users()->pluck('users.id')->implode(',');

        return [
            'users' => 'required|array|distinct',
            'users.*' => ['required', 'exists:users,id', "not_in:$existingUsers"],
        ];
    }

    public function authorize(): bool
    {
        return $this->route('chatRoom')?->users()->where('user_id', auth()->id())->exists();
    }

    public function messages(): array
    {
        return [
            'users.*.not_in' => 'The selected user is already in the chat room.',
        ];
    }
}
