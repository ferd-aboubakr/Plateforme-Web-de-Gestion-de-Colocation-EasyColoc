<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    protected $fillable = [
        'colocation_id',
        'email',
        'token',
        'expires_at',
        'status'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function colocation()
    {
        return $this->belongsTo('App\Models\Colocation');
    }

    public function isExpired()
    {
        return \Carbon\Carbon::now()->gt($this->expires_at);
    }



}
