<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BotUser extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'messenger_user_id',
        'name',
        'profile_photo'
    ];

    /**
     * Relationship Scope
     */
    public function bot(): BelongsTo
    {
        return $this->belongsTo(Bot::class);
    }
}
