<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Membership;
use App\Models\Expense;
use App\Models\Invitation;
use App\Models\Category;

class Colocation extends Model
{
    protected $fillable = [
        'name', 
        'address', 
        'owner_id', 
        'status', 
        'cancelled_at'
    ];

    protected $casts = [
        'cancelled_at' => 'datetime'
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    // Membres ACTIFS uniquement
    public function members()
    {
        return $this->hasMany(Membership::class)->whereNull('left_at');
    }

    // Tous les memberships (y compris anciens)
    public function memberships()
    {
        return $this->hasMany(Membership::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function invitations()
    {
        return $this->hasMany(Invitation::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }
}
