<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['from_user_id', 'to_user_id', 'colocation_id', 'amount', 'paid_at'];

    protected $casts = ['paid_at' => 'datetime'];

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    public function colocation()
    {
        return $this->belongsTo(Colocation::class);
    }
}
