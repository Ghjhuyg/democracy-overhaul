<?php

namespace App\Models;

use App\Models\Vote; 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Cast;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Redis;

#[Fillable(['user_id', 'title', 'description', 'status', 'voting_start_at', 'voting_end_at'])]
class Bill extends Model
{
    use HasFactory;

    public const STATUS_STANDBY = 'standby';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_OPEN = 'open';

    public function finalizeIfEnded(): void
    {
        if ($this->status === self::STATUS_OPEN && $this->voting_end_at && now()->gte($this->voting_end_at)) {
            $this->determineStatusByVotes();
        }
    }

    public function updateStatusToOpenIfStarted(): void
    {
        if ($this->status === self::STATUS_STANDBY && $this->voting_start_at && now()->gte($this->voting_start_at)) {
            $this->status = self::STATUS_OPEN;
            $this->save();
        }

        Redis::publish('vote_events', json_encode([
            'type' => 'bill_opened',
            'bill_id' => $this->id,
            'status' => $this->status,
            'voting_start_at' => $this->voting_start_at ? $this->voting_start_at->toISOString() : null,
            'voting_end_at' => $this->voting_end_at ? $this->voting_end_at->toISOString() : null,
        ]));
    }

    public function determineStatusByVotes(): void
    {
        $for = $this->votesFor();
        $against = $this->votesAgainst();

        if ($for > $against) {
            $this->status = self::STATUS_ACCEPTED;
        } else {
            $this->status = self::STATUS_REJECTED;
        }
        $this->save();

        Redis::publish('vote_events', json_encode([
            'type' => 'bill_finalized',
            'bill_id' => $this->id,
            'status' => $this->status,
            'votes_for' => $for,
            'votes_against' => $against,
        ]));
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    public function votesFor(): int
    {
        return $this->votes()->where('vote', 'for')->count();
    }

    public function votesAgainst(): int
    {
        return $this->votes()->where('vote', 'against')->count();
    }

    public function isVotingActive(): bool
    {
        $now = now();
        return $this->voting_start_at && $this->voting_end_at &&
               $now->between($this->voting_start_at, $this->voting_end_at);
    }

        /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'voting_start_at' => 'datetime',
            'voting_end_at' => 'datetime',
            'status' => 'string',
        ];
    }
}