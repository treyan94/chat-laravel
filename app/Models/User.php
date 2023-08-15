<?php

namespace App\Models;

use Cache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /* Relations */

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function chatRooms(): BelongsToMany
    {
        return $this->belongsToMany(ChatRoom::class);
    }

    /* Helpers */

    public static function getChatGptUserId(bool $fresh = false)
    {
        $key = 'gpt-chat-bot-id';

        if ($fresh) {
            Cache::forget($key);
        }

        return Cache::remember($key, 60 * 60 * 24, static function () {
            return self::where('email', config('openai.gpt_user_email'))->first(['id'])->id;
        });
    }
}
