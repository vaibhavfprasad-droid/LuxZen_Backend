<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

use App\Models\Trip;
use App\Models\Vehicle;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     * 
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'type',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     * 
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     * 
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        // You can enable password hashing here if using Laravel 10+:
        // 'password' => 'hashed',
    ];

    /**
     * Get the trips where this user is the customer.
     * 
     * @return HasMany
     */
    public function tripsAsCustomer(): HasMany
    {
        return $this->hasMany(Trip::class, 'customer_id');
    }

    /**
     * Get the trips where this user is the driver.
     * 
     * @return HasMany
     */
    public function tripsAsDriver(): HasMany
    {
        return $this->hasMany(Trip::class, 'driver_id');
    }

    /**
     * Get the vehicle assigned to this user (driver).
     * 
     * @return HasOne
     */
    public function vehicle(): HasOne
    {
        return $this->hasOne(Vehicle::class, 'driver_id');
    }
}
