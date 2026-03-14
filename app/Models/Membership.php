<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class membership extends Model
{
    //
    protected $fillable = [
        'user_id', 'colocation_id', 'role', 'is_active', 'joined_at', 'left_at'
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'left_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function colocation()
    {
        return $this->belongsTo('App\Models\Colocation');
    }
}
