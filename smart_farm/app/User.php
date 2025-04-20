<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'last_login_attempt',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_attempt' => 'datetime',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function subscriptions()
    {
        return $this->hasMany(\App\Models\Subscription::class);
    }

    public function farms()
    {
        return $this->hasMany(\App\Models\Farm::class);
    }

    public function controlCommands()
    {
        return $this->hasMany(\App\Models\ControlCommand::class);
    }

    public function alerts()
    {
        return $this->hasMany(\App\Models\Alert::class);
    }

    public function reports()
    {
        return $this->hasMany(\App\Models\Report::class);
    }

    public function logs()
    {
        return $this->hasMany(\App\Models\Log::class);
    }
}