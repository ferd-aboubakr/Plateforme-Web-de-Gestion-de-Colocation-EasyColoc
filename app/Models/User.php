<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PhpParser\Node\Expr\FuncCall;
use Spatie\Permission\Traits\HasRoles;



class User extends Authenticatable
{
    use HasRoles;
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'reputation',
        'banned_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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
            'banned_at' => 'datetime',
        ];
    }

    public function activeMembership()
    {
        return $this->hasOne(Membership::class)->whereNull('left_at');
    }

    public function memberships()
    {
        return $this->hasMany(Membership::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'paid_by');
    }

    public function isBanned(): bool
    {
        return !is_null($this->banned_at);
    }

    // Relation wallet (crée automatiquement si absent)
    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    // Récupère ou crée le wallet de l'user
    public function getOrCreateWallet(): Wallet
    {
        return $this->wallet ?? Wallet::create([
            'user_id' => $this->id,
            'balance' => 0.00,
        ]);
    }

    // Raccourci pour le solde
    public function getWalletBalanceAttribute(): float
    {
        return (float) ($this->wallet?->balance ?? 0.00);
    }
}
