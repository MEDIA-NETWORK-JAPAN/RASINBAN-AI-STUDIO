<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Hash;

class TwoFactorToken extends Model
{
    /** @use HasFactory<\Database\Factories\TwoFactorTokenFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'token',
        'expires_at',
        'attempts',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'attempts' => 'integer',
        ];
    }

    protected function token(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => Hash::make($value),
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isMaxAttemptsReached(): bool
    {
        return $this->attempts >= 5;
    }

    public function incrementAttempts(): void
    {
        $this->increment('attempts');
    }

    /**
     * Verify the given token. Returns true on success (and deletes the token).
     * Increments attempts on failure.
     */
    public function verify(string $inputToken): bool
    {
        if ($this->isExpired() || $this->isMaxAttemptsReached()) {
            return false;
        }

        if (Hash::check($inputToken, $this->token)) {
            $this->delete();

            return true;
        }

        $this->incrementAttempts();

        return false;
    }
}
