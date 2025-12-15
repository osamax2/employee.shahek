<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Employee extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
        'device_id',
        'last_seen_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    public function locations()
    {
        return $this->hasMany(EmployeeLocation::class);
    }

    public function devices()
    {
        return $this->hasMany(Device::class);
    }

    public function trackingSessions()
    {
        return $this->hasMany(TrackingSession::class);
    }

    public function latestLocation()
    {
        return $this->hasOne(EmployeeLocation::class)->latestOfMany('recorded_at');
    }

    public function isOnline(): bool
    {
        if (!$this->last_seen_at) {
            return false;
        }

        $threshold = config('tracking.online_threshold_minutes', 10);
        return $this->last_seen_at->gt(now()->subMinutes($threshold));
    }

    // JWT Methods
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
