<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ApiToken extends Model
{
    protected $table = 'api_tokens';

    protected $fillable = [
        'name',
        'token_hash',
        'expires_at',
        'last_used_at',
    ];

    protected $hidden = [
        'token_hash',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'last_used_at' => 'datetime',
    ];

    public function tokenable(): MorphTo
    {
        return $this->morphTo();
    }
}
