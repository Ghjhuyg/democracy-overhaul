<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Cast;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'bill_id',
    'user_id',
    'vote',
])]
class Vote extends Model
{
    use HasFactory;

    public const VOTE_FOR = 'for';
    public const VOTE_AGAINST = 'against';

    public function bill(): BelongsTo
    {
        return $this->belongsTo(Bill::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isFor(): bool
    {
        return $this->vote === self::VOTE_FOR;
    }

    public function isAgainst(): bool
    {
        return $this->vote === self::VOTE_AGAINST;
    }
        /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'vote' => 'string',
        ];
    }
}