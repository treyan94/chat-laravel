<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{

    /* Relations */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function chatRoom(): BelongsTo
    {
        return $this->belongsTo(ChatRoom::class);
    }

    /* Scopes */

    public function scopeForAuth(Builder $query): Builder
    {
        $query->where(function (Builder $query) {
            $query->where('from', Auth::id())->orWhere('to', Auth::id());
        });
    }
}
