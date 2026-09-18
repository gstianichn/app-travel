<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoginSession extends Model
{
    /** @var array<int, string> */
    protected $fillable = [
        'user_id',
        'device',
        'ip_address',
        'user_agent',
        'last_active_at',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'last_active_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
