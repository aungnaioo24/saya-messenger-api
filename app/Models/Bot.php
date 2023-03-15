<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bot extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'bot_flow',
        'profile_photo'
    ];

    /**
     * Relationship Scope
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function botUsers(): HasMany
    {
        return $this->hasMany(BotUser::class);
    }

    public function chats(): HasMany
    {
        return $this->hasMany(Chat::class);
    }
}
