<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'message',
        'is_read',
    ];

    // Relationship
    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }
}