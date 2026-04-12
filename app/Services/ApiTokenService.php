<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class ApiTokenService
{
    public function createToken(Model $tokenable, string $name, Carbon $expiresAt): array
    {
        $plainTextToken = Str::random(80);

        $token = $tokenable->apiTokens()->create([
            'name' => $name,
            'token_hash' => hash('sha256', $plainTextToken),
            'expires_at' => $expiresAt,
        ]);

        return [
            'plain_text_token' => $plainTextToken,
            'token' => $token,
        ];
    }
}
