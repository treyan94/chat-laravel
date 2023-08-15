<?php

namespace App\Models;

use App\Jobs\AddGptMessageJob;
use Cache;
use Illuminate\Database\Eloquent\Casts\Attribute;
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


    /* Helpers */

    public function addGptMessage(): void
    {
        AddGptMessageJob::dispatch($this);
    }

    /* Attributes */

    public function hasGptUser(): Attribute
    {
        $cacheKey = 'chat-room:' . $this->id . ':has-gpt-user';
        $ttl = 60 * 60 * 24;
        $fn = fn() => $this->users()->where('email', config('openai.gpt_user_email'))->exists();

        return new Attribute(
            get: fn() => Cache::remember($cacheKey, $ttl, $fn),
        );
    }
}
