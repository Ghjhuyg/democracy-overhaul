<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'full_name', 'github_id', 'role', ])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{

    public const ROLE_VOTER = 'voter';
    public const ROLE_PROPOSER = 'proposer';

    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function bills()
    {
        return $this->hasMany(Bill::class);
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    public function isVoter(): bool
    {
        return in_array($this->role, [self::ROLE_VOTER]);
    }

    public function isProposer(): bool
    {
        return in_array($this->role, [self::ROLE_PROPOSER]);
    }

    public function hasVotedOnBill(Bill $bill): bool
    {
        return $this->votes()->where('bill_id', $bill->id)->exists();
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => 'string',
        ];
    }
}
