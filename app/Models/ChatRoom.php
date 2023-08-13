<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatRoom extends Model
{
    protected $fillable = [
        'name',
    ];

    protected static function boot(): void
    {
        parent::boot();

        // delete messages when chat room is deleted
        static::deleting(static fn(ChatRoom $chatRoom) => $chatRoom->messages()->delete());
    }

    /* Relations */

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}
